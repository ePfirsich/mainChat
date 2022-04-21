<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

unset($u_id);

$fehlermeldung = "";
$email_gesendet = false;

if (isset($email) && isset($username) && isset($hash)) {
	$username = coreCheckName($username, $check_name);
	$email = urldecode($email);
	$query = pdoQuery("SELECT `u_id`, `u_login`, `u_nick`, `u_passwort`, `u_email`, `u_punkte_jahr` FROM `user` WHERE `u_nick` = :u_nick AND `u_level` != 'G' AND `u_email` = :u_email LIMIT 1", [':u_nick'=>$username, ':u_email'=>$email]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$a = $query->fetch();
		$hash2 = md5($a['u_id'] . $a['u_login'] . $a['u_nick'] . $a['u_passwort'] . $a['u_email'] . $a['u_punkte_jahr']);
		if ($hash == $hash2) {
			$u_id = $a['u_id'];
		} else {
			$fehlermeldung .= $lang['login_fehlermeldung_passwort_vergessen_sicherheitscode'];
		}
	} else {
		$fehlermeldung .= $lang['login_fehlermeldung_passwort_vergessen_sicherheitscode'];
	}
} else if ( isset($email) || isset($username) ) {
	if( isset($username) && $username != "" ) {
		$username = coreCheckName($username, $check_name);
	} else {
		$username = "";
	}
	
	if( isset($email) && $email != "" ) {
		$email = urldecode($email);
		if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", $email)) {
			$fehlermeldung .= $lang['login_fehlermeldung_passwort_vergessen_email'];
		}
	} else {
		$email = "";
	}
	
	if( $email == "" && $username == "" ) {
		$fehlermeldung .= $lang['login_fehlermeldung_passwort_vergessen_email_benutzername'];
	}
	
	if ($fehlermeldung == "") {
		if($username != "") {
			$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_email`, UNIX_TIMESTAMP(`u_passwortanforderung`) AS `u_passwortanforderung`, `u_punkte_jahr` FROM `user` WHERE `u_nick` = :u_nick LIMIT 2", [':u_nick'=>$username]);
		} else {
			$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_email`, UNIX_TIMESTAMP(`u_passwortanforderung`) AS `u_passwortanforderung`, `u_punkte_jahr` FROM `user` WHERE `u_email` = :u_email LIMIT 2", [':u_email'=>$email]);
		}
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$a = $query->fetch();
			
			// Es darf nur alle 4 Stunden ein neues Passwort angefordert werden
			// 4 Stunden = 14400 Sekunde
			if( ( time()-intval($a['u_passwortanforderung']) ) < 14400 ) {
				$fehlermeldung .= $lang['login_fehlermeldung_passwort_vergessen_bereits_angefordert'];
			} else {
				$email = urlencode($a['u_email']);
				
				// Passwortcode generieren und in der Datenbank speichern
				$passwordcode = random_string();
				
				pdoQuery("UPDATE `user` SET `u_passwort_code` = :u_passwort_code, `u_passwort_code_time` = NOW() WHERE `u_id` = :u_id", [':u_passwort_code'=>sha1($passwordcode), ':u_id'=>$a['u_id']]);
				
				// URL zusammenstellen
				$webseite_passwort = $chat_url . "/index.php?bereich=passwort-zuruecksetzen&uid=" . $a['u_id'] . "&code=".$passwordcode;
				$inhalt = str_replace("%webseite_passwort%", $webseite_passwort, $lang['email_passwort_vergessen_inhalt']);
				$inhalt = str_replace("%u_nick%", $a['u_nick'], $inhalt);
				$email = urldecode($a['u_email']);
				
				// Aktuelle Zeit setzen, wann das Passwort angefordert wurde
				pdoQuery("UPDATE `user` SET `u_passwortanforderung` = NOW() WHERE `u_id` = :u_id", [':u_id'=>$a['u_id']]);
				
				// E-Mail versenden
				email_senden($email, $lang['email_passwort_vergessen_titel'], $inhalt);
				
				$email_gesendet = true;
			}
		} else {
			if($username != "") {
				$fehlermeldung .= $lang['login_fehlermeldung_passwort_vergessen_benutzername'];
			} else {
				$fehlermeldung .= $lang['login_fehlermeldung_passwort_vergessen_email2'];
			}
		}
	}
}

$text = "";

// Fehlermeldungen ausgeben
if ($fehlermeldung != "") {
	$text .= hinweis($fehlermeldung, "fehler");
}

if ($email_gesendet) {
	$erfolgsmeldung = $lang['login_passwort_schritt2'];
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
} else {
	$text .= $lang['login_passwort_schritt1'];
	
	// Formular zum Anfordern eines neuen Passworts
	$text .= "<form action=\"index.php?bereich=passwort-vergessen\" method=\"post\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	if (!isset($username)) {
		$username = "";
	}
	if (!isset($email)) {
		$email = "";
	}
	
	$zaehler = 0;
	
	// Überschrift: Neues Passwort anfordern
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['login_passwort_anfordern'], "", "", 0, "70", "");
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $lang['login_benutzername'], "username", $username);
	$zaehler++;
	
	// E-Mail
	$text .= zeige_formularfelder("input", $zaehler, $lang['login_email'], "email", $email);
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
?>