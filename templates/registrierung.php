<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$fehlermeldung = "";
$text = "";

if ( isset($email) && !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", mysqli_real_escape_string($mysqli_link, $email)) ) {
	// dieser Regex macht eine primitive Prüfung ob eine Mailadresse
	// der Form name@do.main entspricht
	if($email == "") {
		$fehlermeldung = str_replace("%email%", $email, $t['registrierung_fehler_keine_email']);
	} else {
		$fehlermeldung = str_replace("%email%", $email, $t['registrierung_fehler_ungueltige_email']);
	}
	$email = "";
	
	$text .= hinweis($fehlermeldung, "fehler");
}

if (!isset($email) || $fehlermeldung != "") {
	// Formular für die Erstregistierung 1. Schritt ausgeben
	$text .= $t['registrierung_email_angeben'];
	
	$text .= "<form action=\"index.php?bereich=registrierung\" method=\"post\">\n";
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Neuen Account registrieren
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['registrierung_neuen_account_registrieren'], "", "", 0, "70", "");
	
	// E-Mail Adresse
	$text .= zeige_formularfelder("input", $zaehler, $t['registrierung_email_adresse'], "email", "");
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" $bgcolor>\n";
	$text .= "<input type=\"submit\" value=\"". $t['registrierung_absenden'] . "\">\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	
	$text .= "</table>\n";
	$text .= "</form>\n";
	
	zeige_tabelle_volle_breite($t['login_registrierung'], $text);
}

if($fehlermeldung == "" && isset($email)) {
	// Mail verschicken, 2. Schritt
	
	$email = trim($email);
	
	// wir prüfen ob Benutzer gesperrt ist
	// entweder Benutzer = gesperrt
	$query = "SELECT * FROM `user` WHERE `u_email`='$email' AND `u_level`='Z'";
	$result = sqlQuery($query);
	$num = mysqli_num_rows($result);
	if ($num >= 1) {
		$fehlermeldung = $t['registrierung_fehler_gesperrte_email'];
	}
	
	// oder Benutzer ist auf Blacklist
	$query = "SELECT u_nick FROM blacklist LEFT JOIN user ON f_blacklistid=u_id WHERE user.u_email ='$email'";
	$result = sqlQuery($query);
	$num = mysqli_num_rows($result);
	if ($num >= 1) {
		$fehlermeldung = $t['registrierung_fehler_gesperrte_email'];
	}
	
	// oder Domain ist lt. Config verboten
	if ($domaingesperrtdbase != $dbase) {
		$domaingesperrt = array();
	}
	
	for ($i = 0; $i < count($domaingesperrt); $i++) {
		$teststring = strtolower($email);
		if (isset($domaingesperrt[$i]) && $domaingesperrt[$i] && (preg_match($domaingesperrt[$i], $teststring))) {
			$fehlermeldung = $t['registrierung_fehler_gesperrte_email'];
		}
	}
	unset($teststring);
	
	$query = "SELECT `u_id` FROM `user` WHERE `u_email` = '$email'";
	$result = sqlQuery($query);
	$num = mysqli_num_rows($result);
	// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
	if ($num >= 1) {
		$fehlermeldung = $t['registrierung_fehler_email_bereits_vorhanden'];
	}
	
	if($fehlermeldung != "") {
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		// Überprüfung auf Formular mehrmals abgeschickt
		$query = "DELETE FROM mail_check WHERE email = '$email'";
		sqlUpdate($query, true);
		
		$hash = md5($email . "+" . date("Y-m-d"));
		$email = urlencode($email);
		
		// Normale Anmeldung
		$link = $chat_url . $_SERVER['PHP_SELF'] . "?bereich=neu&email=$email&hash=$hash";
		$link2 = $chat_url . $_SERVER['PHP_SELF'] . "?bereich=neu2";
		
		$email = urldecode($email);
		
		$inhalt = str_replace("%link%", $link, $t['registrierung_email_text']);
		$inhalt = str_replace("%link2%", $link2, $inhalt);
		$inhalt = str_replace("%hash%", $hash, $inhalt);
		$inhalt = str_replace("%email%", $email, $inhalt);
		
		$text = $t['registrierung_email_versendet'];
		zeige_tabelle_volle_breite($t['login_registrierung'], $text);
		
		// E-Mail versenden
		email_senden($email, $t['registrierung_email_titel'], $inhalt);
		
		$email = mysqli_real_escape_string($mysqli_link, $email);
		$query = "REPLACE INTO mail_check (email,datum) VALUES ('$email',NOW())";
		$result = sqlUpdate($query);
	}
}
?>