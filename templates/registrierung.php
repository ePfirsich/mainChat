<?php
// Direkten Aufruf der Datei verbieten
if( !isset($aktion)) {
	die;
}

$fehlermeldung = "";

if ( isset($email) && !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", mysqli_real_escape_string($mysqli_link, $email)) ) {
	// dieser Regex macht eine primitive Prüfung ob eine Mailadresse
	// der Form name@do.main entspricht
	$fehlermeldung = str_replace("%email%", $email, $t['registrierung_fehler_ungueltige_email']);
	$email = "";
	
	zeige_tabelle_volle_breite($t['login_fehlermeldung'], $fehlermeldung);
}

if (!isset($email) || $fehlermeldung != "") {
	// Formular für die Erstregistierung 1. Schritt ausgeben
	$text = $t['registrierung_email_angeben'];
	
	$text .= "<form action=\"index.php\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"registrierung\">\n";
	
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
	$query = "SELECT * FROM `user` WHERE `u_adminemail`='$email' AND `u_level`='Z'";
	$result = mysqli_query($mysqli_link, $query);
	$num = mysqli_num_rows($result);
	if ($num >= 1) {
		$fehlermeldung = $t['registrierung_fehler_gesperrte_email'];
	}
	
	// oder user ist auf Blacklist
	$query = "SELECT u_nick FROM blacklist LEFT JOIN user ON f_blacklistid=u_id WHERE user.u_adminemail ='$email'";
	$result = mysqli_query($mysqli_link, $query);
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
		if (isset($domaingesperrt[$i]) && $domaingesperrt[$i]
			&& (preg_match($domaingesperrt[$i], $teststring))) {
			$fehlermeldung = $t['registrierung_fehler_gesperrte_email'];
		}
	}
	unset($teststring);
	
	$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '$email'";
	$result = mysqli_query($mysqli_link, $query);
	$num = mysqli_num_rows($result);
	// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
	if ($num >= 1) {
		$fehlermeldung = $t['registrierung_fehler_email_bereits_vorhanden'];
	}
	
	if($fehlermeldung != "") {
		zeige_tabelle_volle_breite($t['login_fehlermeldung'], $fehlermeldung);
	} else {
		// Überprüfung auf Formular mehrmals abgeschickt
		$query = "DELETE FROM mail_check WHERE email = '$email'";
		mysqli_query($mysqli_link, $query);
		
		$hash = md5($email . "+" . date("Y-m-d"));
		$email = urlencode($email);
		
		// Normale Anmeldung
		$link = $chat_url . $_SERVER['PHP_SELF'] . "?aktion=neu&email=$email&hash=$hash";
		$link2 = $chat_url . $_SERVER['PHP_SELF'] . "?aktion=neu2";
		
		$text2 = str_replace("%link%", $link, $t['registrierung_email_text']);
		$text2 = str_replace("%link2%", $link2, $text2);
		$text2 = str_replace("%hash%", $hash, $text2);
		$email = urldecode($email);
		$text2 = str_replace("%email%", $email, $text2);
		
		$mailbetreff = $t['registrierung_email_titel'];
		$mailempfaenger = $email;
		
		$text = $t['registrierung_email_versendet'];
		
		
		zeige_tabelle_volle_breite($t['login_registrierung'], $text);
		
		$header = "\n" . "X-MC-IP: " . $_SERVER["REMOTE_ADDR"] . "\n" . "X-MC-TS: " . time();
		$header .= "Mime-Version: 1.0\r\n";
		$header .= "Content-type: text/plain; charset=utf-8";
		
		// E-Mail versenden
		if($smtp_on) {
			mailsmtp($mailempfaenger, $mailbetreff, $text2, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
		} else {
			// Der PHP-Versand benötigt \n und nicht <br>
			$text2 = str_replace("<br>", "\n", $text2);
			mail($mailempfaenger, $mailbetreff, $text2, "From: $webmaster ($chat)" . $header);
		}
		
		$email = mysqli_real_escape_string($mysqli_link, $email);
		$query = "REPLACE INTO mail_check (email,datum) VALUES ('$email',NOW())";
		$result = mysqli_query($mysqli_link, $query);
	}
}
?>