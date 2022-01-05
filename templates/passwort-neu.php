<?php
// Direkten Aufruf der Datei verbieten
if( !isset($aktion)) {
	die;
}

unset($u_id);

$richtig = false;
$fehlermeldung = "";
$email_gesendet = false;

if (isset($email) && isset($nickname) && isset($hash)) {
	$nickname = mysqli_real_escape_string($mysqli_link, coreCheckName($nickname, $check_name));
	$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
	$query = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user WHERE u_nick = '$nickname' AND u_level != 'G' AND u_adminemail = '$email' LIMIT 1";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$hash2 = md5($a['u_id'] . $a['u_login'] . $a['u_nick'] . $a['u_passwort'] . $a['u_adminemail'] . $a['u_punkte_jahr']);
		if ($hash == $hash2) {
			$richtig = true;
			$u_id = $a['u_id'];
		} else {
			$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_sicherheitscode'];
		}
	} else {
		$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_sicherheitscode'];
	}
	mysqli_free_result($result);
} else if ( isset($email) || isset($nickname) ) {
	if( isset($nickname) && $nickname != "" ) {
		$nickname = mysqli_real_escape_string($mysqli_link, coreCheckName($nickname, $check_name));
	} else {
		$nickname = "";
	}
	
	if( isset($email) && $email != "" ) {
		$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
		if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", mysqli_real_escape_string($mysqli_link, $email))) {
			$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_email'];
		}
	} else {
		$email = "";
	}
	
	if( $email == "" && $nickname == "" ) {
		$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_email_benutzername'];
	}
	
	if ($fehlermeldung == "") {
		if($nickname != "") {
			$query = "SELECT `u_id`, `u_login`, `u_nick`, `u_passwort`, `u_adminemail`, UNIX_TIMESTAMP(u_passwortanforderung) AS `u_passwortanforderung`, `u_punkte_jahr` FROM `user` WHERE `u_nick` = '$nickname' LIMIT 2";
		} else {
			$query = "SELECT `u_id`, `u_login`, `u_nick`, `u_passwort`, `u_adminemail`, UNIX_TIMESTAMP(u_passwortanforderung) AS `u_passwortanforderung`, `u_punkte_jahr` FROM `user` WHERE `u_adminemail` = '$email' LIMIT 2";
		}
		
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Es darf nur alle 2 Stunden ein neues Passwort angefordert werden
			// 2 Stunden = 7200 Sekunde
			if( ( time()-intval($a['u_passwortanforderung']) ) < 7200 ) {
				$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_bereits_angefordert'];
				$richtig = false;
			} else {
				$hash = md5(
					$a['u_id'] . $a['u_login'] . $a['u_nick']
					. $a['u_passwort']
					. $a['u_adminemail'] . $a['u_punkte_jahr']);
				
				$email = urlencode($a['u_adminemail']);
				$link = $chat_url . "/index.php?aktion=passwort_neu&email=" . $email . "&nickname=" . $nickname . "&hash=" . $hash;
				
				$inhalt = str_replace("%link%", $link, $t['pwneu9']);
				$inhalt = str_replace("%hash%", $hash, $inhalt);
				$inhalt = str_replace("%nickname%", $a['u_nick'], $inhalt);
				$inhalt = str_replace("%email%", $email, $inhalt);
				$email = urldecode($a['u_adminemail']);
				
				// Aktuelle Zeit setzen, wann das Passwort angefordert wurde
				$queryPasswortanforderung = "UPDATE `user` SET `u_passwortanforderung` = NOW() WHERE `u_id` = $a[u_id]";
				mysqli_query($mysqli_link, $queryPasswortanforderung);
				
				// E-Mail versenden
				email_senden($email, $t['pwneu8'], $inhalt);
				
				$email_gesendet = true;
				unset($hash);
			}
		} else {
			if($nickname != "") {
				$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_benutzername'];
			} else {
				$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_email2'];
			}
			unset($hash);
		}
		mysqli_free_result($result);
	}
}

if (!$richtig) {
	// Fehlermeldungen ausgeben
	if ($fehlermeldung != "") {
		zeige_tabelle_volle_breite($t['login_fehlermeldung'], $fehlermeldung);
	}
	
	if ($email_gesendet) {
		$text = $t['login_passwort_schritt2'];
	} else {
		$text = $t['login_passwort_schritt1'];
	}
	
	$text .= "<form action=\"index.php\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"passwort_neu\">";
	
	if ( (isset($email) || isset($nickname)) && $email <> "" && $nickname <> "" && (isset($hash) || $fehlermeldung == "") ) {
		// Eingabe des Sicherheitscodes, der per E-Mail zugeschickt wurde
		
		$text .= "<input type=\"hidden\" name=\"nickname\" value=\"$nickname\">";
		$text .= "<input type=\"hidden\" name=\"email\" value=\"$email\">";
		$text .= "<table style=\"width:100%;\">\n";
		if (!isset($hash)) {
			$hash = "";
		}
		
		// Überschrift: Neues Passwort anfordern
		$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['login_passwort_anfordern'], "", "", 0, "70", "");
		
		// Benutzername
		$text .= zeige_formularfelder("text", $zaehler, $t['login_benutzername'], "", $nickname);
		$zaehler++;
		
		// E-Mail
		$text .= zeige_formularfelder("text", $zaehler, $t['login_email'], "", $email);
		$zaehler++;
		
		// Sicherheitsocde
		$text .= zeige_formularfelder("input", $zaehler, $t['login_passwort_sicherheitscode'], "hash", $hash);
		$zaehler++;
	} else {
		// Formular zum Anfordern eines neuen Passworts
		
		$text .= "<table style=\"width:100%;\">\n";
		if (!isset($nickname)) {
			$nickname = "";
		}
		if (!isset($email)) {
			$email = "";
		}
		
		// Überschrift: Neues Passwort anfordern
		$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['login_passwort_anfordern'], "", "", 0, "70", "");
		
		// Benutzername
		$text .= zeige_formularfelder("input", $zaehler, $t['login_benutzername'], "nickname", $nickname);
		$zaehler++;
		
		// E-Mail
		$text .= zeige_formularfelder("input", $zaehler, $t['login_email'], "email", $email);
		$zaehler++;
	}
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" $bgcolor>\n";
	$text .= "<input type=\"submit\" value=\"Absenden\">\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	
	$text .= "</table>";
	$text .= "</form>";
	
	zeige_tabelle_volle_breite($t['login_passwort_vergessen'], $text);
	
} else if ($richtig && $u_id) {
	$query = "SELECT `u_adminemail`, `u_nick` FROM `user` WHERE `u_id` = '$u_id' LIMIT 2";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		unset($f);
		$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$pwdneu = genpassword(8);
		$f['u_passwort'] = $pwdneu;
		$f['u_id'] = $u_id;
		$text = str_replace("%passwort%", $f['u_passwort'], $t['pwneu15']);
		$text = str_replace("%nickname%", $a['u_nick'], $text);
		
		// E-Mail versenden
		$ok = email_senden($a['u_adminemail'], $t['pwneu14'], $text);
		
		if ($ok) {
			$text = $t['login_passwort_schritt3'];
			zeige_tabelle_volle_breite($t['login_passwort_vergessen'], $text);
			schreibe_db("user", $f, $f['u_id'], "u_id");
		} else {
			$text = $t['login_fehlermeldung_passwort_versand'];
			zeige_tabelle_volle_breite($t['login_passwort_vergessen'], $text);
		}
	}
}
?>