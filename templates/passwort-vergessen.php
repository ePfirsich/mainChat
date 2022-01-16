<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

unset($u_id);

$fehlermeldung = "";
$email_gesendet = false;

if (isset($email) && isset($username) && isset($hash)) {
	$username = mysqli_real_escape_string($mysqli_link, coreCheckName($username, $check_name));
	$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
	$query = "SELECT u_id, u_login, u_nick, u_passwort, u_email, u_punkte_jahr FROM user WHERE u_nick = '$username' AND u_level != 'G' AND u_email = '$email' LIMIT 1";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) == 1) {
		$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$hash2 = md5($a['u_id'] . $a['u_login'] . $a['u_nick'] . $a['u_passwort'] . $a['u_email'] . $a['u_punkte_jahr']);
		if ($hash == $hash2) {
			$u_id = $a['u_id'];
		} else {
			$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_sicherheitscode'];
		}
	} else {
		$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_sicherheitscode'];
	}
	mysqli_free_result($result);
} else if ( isset($email) || isset($username) ) {
	if( isset($username) && $username != "" ) {
		$username = mysqli_real_escape_string($mysqli_link, coreCheckName($username, $check_name));
	} else {
		$username = "";
	}
	
	if( isset($email) && $email != "" ) {
		$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
		if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", mysqli_real_escape_string($mysqli_link, $email))) {
			$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_email'];
		}
	} else {
		$email = "";
	}
	
	if( $email == "" && $username == "" ) {
		$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_email_benutzername'];
	}
	
	if ($fehlermeldung == "") {
		if($username != "") {
			$query = "SELECT `u_id`, `u_login`, `u_nick`, `u_passwort`, `u_email`, UNIX_TIMESTAMP(u_passwortanforderung) AS `u_passwortanforderung`, `u_punkte_jahr` FROM `user` WHERE `u_nick` = '$username' LIMIT 2";
		} else {
			$query = "SELECT `u_id`, `u_login`, `u_nick`, `u_passwort`, `u_email`, UNIX_TIMESTAMP(u_passwortanforderung) AS `u_passwortanforderung`, `u_punkte_jahr` FROM `user` WHERE `u_email` = '$email' LIMIT 2";
		}
		
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Es darf nur alle 4 Stunden ein neues Passwort angefordert werden
			// 4 Stunden = 14400 Sekunde
			if( ( time()-intval($a['u_passwortanforderung']) ) < 14400 ) {
				$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_bereits_angefordert'];
			} else {
				$hash = md5(
					$a['u_id'] . $a['u_login'] . $a['u_nick']
					. $a['u_passwort']
					. $a['u_email'] . $a['u_punkte_jahr']);
				
				$email = urlencode($a['u_email']);
				
				// Passwortcode generieren und in der Datenbank speichern
				$passwordcode = randomString();
				$queryPasswortcode = "UPDATE `user` SET `u_passwort_code` = '".sha1($passwordcode)."', `u_passwort_code_time` = NOW() WHERE `u_id` = $a[u_id];";
				sqlUpdate($queryPasswortcode);
				
				// ULR zusammenstellen
				$webseite_passwort = $chat_url . "/index.php?bereich=passwort-zuruecksetzen&uid=" . $a['u_id'] . "&code=".$passwordcode;
				$inhalt = str_replace("%webseite_passwort%", $webseite_passwort, $t['email_passwort_vergessen_inhalt']);
				$inhalt = str_replace("%u_nick%", $a['u_nick'], $inhalt);
				$email = urldecode($a['u_email']);
				
				// Aktuelle Zeit setzen, wann das Passwort angefordert wurde
				$queryPasswortanforderung = "UPDATE `user` SET `u_passwortanforderung` = NOW() WHERE `u_id` = $a[u_id]";
				sqlUpdate($queryPasswortanforderung);
				
				// E-Mail versenden
				email_senden($email, $t['email_passwort_vergessen_titel'], $inhalt);
				
				$email_gesendet = true;
				unset($hash);
			}
		} else {
			if($username != "") {
				$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_benutzername'];
			} else {
				$fehlermeldung .= $t['login_fehlermeldung_passwort_vergessen_email2'];
			}
			unset($hash);
		}
		mysqli_free_result($result);
	}
}

$text = "";

// Fehlermeldungen ausgeben
if ($fehlermeldung != "") {
	$text .= hinweis($fehlermeldung, "fehler");
}

if ($email_gesendet) {
	$erfolgsmeldung = $t['login_passwort_schritt2'];
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
} else {
	$text .= $t['login_passwort_schritt1'];
	
	// Formular zum Anfordern eines neuen Passworts
	$text .= "<form action=\"index.php?bereich=passwort-vergessen\" method=\"post\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	if (!isset($username)) {
		$username = "";
	}
	if (!isset($email)) {
		$email = "";
	}
	
	// Überschrift: Neues Passwort anfordern
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['login_passwort_anfordern'], "", "", 0, "70", "");
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $t['login_benutzername'], "username", $username);
	$zaehler++;
	
	// E-Mail
	$text .= zeige_formularfelder("input", $zaehler, $t['login_email'], "email", $email);
	$zaehler++;

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
}

zeige_tabelle_volle_breite($t['login_passwort_vergessen'], $text);
?>