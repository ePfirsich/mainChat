<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$fehlermeldung = "";

// Daten aus der E-Mail
$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
$hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_URL);

// Daten aus dem Formular
$f['u_nick'] = htmlspecialchars(filter_input(INPUT_POST, 'u_nick', FILTER_SANITIZE_STRING));
$f['u_passwort'] = htmlspecialchars(filter_input(INPUT_POST, 'u_passwort', FILTER_SANITIZE_STRING));
$f['u_passwort2'] = htmlspecialchars(filter_input(INPUT_POST, 'u_passwort2', FILTER_SANITIZE_STRING));
$f['u_email'] = filter_input(INPUT_POST, 'u_email', FILTER_VALIDATE_EMAIL);
$f['hash'] = filter_input(INPUT_POST, 'hash', FILTER_SANITIZE_URL);

$formular = filter_input(INPUT_POST, 'formular', FILTER_SANITIZE_NUMBER_INT);

$formular_anzeigen = true;
$weiter_zu_login = false;

if ( ($email != "" && $hash != md5($email . "+" . date("Y-m-d"))) ) {
	// Fehlermeldung wenn der Link aus der E-Mail verändert wurde oder zu alt ist
	$fehlermeldung = $lang['registrierung_fehler_email_link_falsch'];
	$formular = 0;
	$formular_anzeigen = false;
} else if($email != "" && $hash == md5($email . "+" . date("Y-m-d"))) {
	// Mit dieser E-Mail wurde bereits ein Account registriert
	$query = pdoQuery("SELECT `email` FROM `mail_check` WHERE `email` = :email", [':email'=>$email]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		$fehlermeldung = $lang['registrierung_fehler_aktivierungslink_verwendet'];
		$text .= hinweis($fehlermeldung, "fehler");
		$formular_anzeigen = false;
	}
} else if($f['u_email'] != "" && $f['hash'] == md5($f['u_email'] . "+" . date("Y-m-d"))) {
	// Korrekter Aufruf
} else {
	// Alle weiteren Fälle werden abgelehnt
	$fehlermeldung = $lang['registrierung_fehler_email_link_falsch'];
	$formular = 0;
	$formular_anzeigen = false;
}

// Eingaben prüfen
if($formular == 1) {
	// Sind der Hash und die E-Mail gesetzt ?
	if ( !isset($f['hash']) || $f['hash'] == "" || !isset($f['u_email'])|| $f['u_email'] == "" ) {
		$fehlermeldung .= $lang['registrierung_fehler_falscher_aufruf'];
	}
	
	// Eingabefelder auf Fehler prüfen
	if($fehlermeldung == "") {
		// Im Benutzername alle Sonderzeichen entfernen
		$f['u_nick'] = coreCheckName($f['u_nick'], $check_name);
		
		$pos = strpos($f['u_nick'], "+");
		if ($pos === false) {
			$pos = -1;
		}
		if ($pos >= 0) {
			$fehlermeldung = $lang['registrierung_fehler_benutzername_pluszeichen'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if (strlen($f['u_nick']) > 20 || strlen($f['u_nick']) < 4) {
			$fehlermeldung = str_replace("%zeichen%", $check_name, $lang['registrierung_fehler_benutzername_zeichenlaenge']);
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if (preg_match("/^" . $lang['login_gast'] . "/i", $f['u_nick'])) {
			$fehlermeldung = str_replace("%gast%", $lang['login_gast'], $lang['registrierung_fehler_benutzername_gast']);
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if (strlen($f['u_passwort']) < 4) {
			$fehlermeldung = $lang['registrierung_fehler_passwort_zeichenlaenge'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if ($f['u_passwort'] != $f['u_passwort2']) {
			$fehlermeldung = $lang['registrierung_fehler_passwort_unterschiedlich'];
			$text .= hinweis($fehlermeldung, "fehler");
			$f['u_passwort'] = "";
			$f['u_passwort2'] = "";
		}
		
		if (strlen($f['u_email']) == 0 || !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", $f['u_email'])) {
			$fehlermeldung = $lang['registrierung_fehler_adminemail_falsch'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		// Gibt es den Benutzernamen schon?
		$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$f['u_nick']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount != 0) {
			$fehlermeldung = $lang['registrierung_fehler_benutzername_vergeben'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
	}
	
	// Prüfe ob Mailadresse schon zu oft registriert, durch ZURÜCK Button bei der 1. Registrierung
	$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_email` = :u_email", [':u_email'=>$f['u_email']]);
	
	$resultCount = $query->rowCount();
	// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
	if ($resultCount > 0) {
		$fehlermeldung .= $lang['registrierung_fehler_email_bereits_vorhanden'];
	}
	
	// Keine Fehler -> Registrierung erfolgreich
	if($fehlermeldung == "") {
		$weiter_zu_login = true;
	}
} else {
	// Formular wurde noch nicht aufgerufen -> Alle Werte als leer setzen
	$f = array();
	$f['u_nick'] = "";
	$f['u_passwort'] = "";
	$f['u_passwort2'] = "";
	$f['u_email'] = $email;
	$f['hash'] = $hash;
}

if ( (isset($email) || $fehlermeldung != "") && $formular_anzeigen) {
	$text .= $lang['registrierung_informationen'];
	
	$text .= "<form action=\"index.php?bereich=neu\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "<input type=\"hidden\" name=\"u_email\" value=\"$f[u_email]\">\n";
	$text .= "<input type=\"hidden\" name=\"hash\" value=\"$f[hash]\">\n";
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Neuen Account registrieren
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['registrierung_neuen_account_registrieren'], "", "", 0, "70", "");
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $lang['login_benutzername'], "u_nick", $f['u_nick'], 0, "70", "* " . $lang['login_benutzername_beschreibung']);
	$zaehler++;
	
	// Passwort
	$text .= zeige_formularfelder("password", $zaehler, $lang['login_passwort'], "u_passwort", $f['u_passwort'], 0, "70", "*");
	$zaehler++;
	
	// Passwort wiederholen
	$text .= zeige_formularfelder("password", $zaehler, $lang['login_passwort_wiederholen'], "u_passwort2", $f['u_passwort2'], 0, "70", "*");
	$zaehler++;
	
	// Registrierung absenden
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" $bgcolor>\n";
	$text .= "<input type=\"submit\" value=\"". $lang['registrierung_absenden'] . "\">\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	
	$text .= "</table>\n";
	$text .= "</form>\n";
}

if ($weiter_zu_login) {
	$willkommensnachricht = $lang['registrierung_erfolgreich'];
	$willkommensnachricht .= $lang['registrierung_erfolgreich2'];
	$willkommensnachricht .= "<br><b>" . $lang['login_benutzername'] . ":</b> " . $f['u_nick'] . "<br><br>";
	$willkommensnachricht .= $lang['registrierung_erfolgreich3'];
	
	$text = "";
	
	// Daten in DB als Benutzer eintragen
	$text .= "<form action=\"index.php\" name=\"login\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"einloggen\">\n";
	$text .= "<input type=\"hidden\" name=\"username\" value=\"$f[u_nick]\">\n";
	$text .= "<input type=\"hidden\" name=\"passwort\" value=\"$f[u_passwort]\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	
	// Willkommensnachricht
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor>$willkommensnachricht</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	// Angemeldet bleiben
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor><label><input type=\"checkbox\" name=\"angemeldet_bleiben\" value=\"1\"> $lang[login_angemeldet_bleiben]</label></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	// Login
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"". $lang['login_weiter_zum_chat'] . "\"></td>\n";
	$text .= "</tr>\n";
	
	$text .= "</table>\n";
	$text .= "</form>\n";
	
	// Im Benutzername alle Sonderzeichen entfernen
	$f['u_nick'] = coreCheckName($f['u_nick'], $check_name);
	
	$f['u_level'] = "U";
	pdoQuery("INSERT INTO `user` (`u_nick`, `u_passwort`, `u_email`, `u_level`, `u_neu`) VALUES (:u_nick, :u_passwort, :u_email, :u_level, DATE_FORMAT(now(),\"%Y%m%d%H%i%s\"))",
		[
			':u_nick'=>$f['u_nick'],
			':u_passwort'=>encrypt_password($f['u_passwort']),
			':u_email'=>$f['u_email'],
			':u_level'=>$f['u_level']
		]);
	
	pdoQuery("DELETE FROM `mail_check` WHERE `email` = :email", [':email'=>$f['u_email']]);
}
?>