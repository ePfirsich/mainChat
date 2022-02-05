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

$formular_anzeigen = true;
$weiter_zu_login = false;

if ( ($email != "" && $hash != md5($email . "+" . date("Y-m-d"))) ) {
	// Fehlermeldung wenn der Link aus der E-Mail verändert wurde oder zu alt ist
	$fehlermeldung = $t['registrierung_fehler_email_link_falsch'];
	$formular = 0;
	$formular_anzeigen = false;
} else if($email != "" && $hash == md5($email . "+" . date("Y-m-d"))) {
	// Korrekter Aufruf
	
	// Mit dieser E-Mail wurde bereits ein Account registriert
	$query = "SELECT email FROM mail_check WHERE email = '" . escape_string($email) . "'";
	$result = sqlQuery($query);
	$rows = mysqli_num_rows($result);
	mysqli_free_result($result);
	
	if ($rows == 0) {
		$fehlermeldung = $t['registrierung_fehler_aktivierungslink_verwendet'];
		$text .= hinweis($fehlermeldung, "fehler");
		$formular_anzeigen = false;
	}
	
} else if($f['u_email'] != "" && $f['hash'] == md5($f['u_email'] . "+" . date("Y-m-d"))) {
	// Korrekter Aufruf
} else {
	// Alle weiteren Fälle werden abgelehnt
	$fehlermeldung = $t['registrierung_fehler_email_link_falsch'];
	$formular = 0;
	$formular_anzeigen = false;
}

// Eingaben prüfen
if($formular == 1) {
	// Sind der Hash und die E-Mail gesetzt ?
	if ( !isset($f['hash']) || $f['hash'] == "" || !isset($f['u_email'])|| $f['u_email'] == "" ) {
		$fehlermeldung .= $t['registrierung_fehler_falscher_aufruf'];
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
			$fehlermeldung = $t['registrierung_fehler_benutzername_pluszeichen'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if (strlen($f['u_nick']) > 20 || strlen($f['u_nick']) < 4) {
			$fehlermeldung = str_replace("%zeichen%", $check_name, $t['registrierung_fehler_benutzername_zeichenlaenge']);
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if (preg_match("/^" . $t['login_gast'] . "/i", $f['u_nick'])) {
			$fehlermeldung = str_replace("%gast%", $t['login_gast'], $t['registrierung_fehler_benutzername_gast']);
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if (strlen($f['u_passwort']) < 4) {
			$fehlermeldung = $t['registrierung_fehler_passwort_zeichenlaenge'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if ($f['u_passwort'] != $f['u_passwort2']) {
			$fehlermeldung = $t['registrierung_fehler_passwort_unterschiedlich'];
			$text .= hinweis($fehlermeldung, "fehler");
			$f['u_passwort'] = "";
			$f['u_passwort2'] = "";
		}
		
		if (strlen($f['u_email']) == 0 || !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", $f['u_email'])) {
			$fehlermeldung = $t['registrierung_fehler_adminemail_falsch'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		// Gibt es den Benutzernamen schon?
		$query = "SELECT `u_id` FROM `user` WHERE `u_nick` = '" . escape_string($f['u_nick']) . "'";
		$result = sqlQuery($query);
		$rows = mysqli_num_rows($result);
		
		if ($rows != 0) {
			$fehlermeldung = $t['registrierung_fehler_benutzername_vergeben'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		mysqli_free_result($result);
	}
	
	// Prüfe ob Mailadresse schon zu oft registriert, durch ZURÜCK Button bei der 1. registrierung
	$query = "SELECT `u_id` FROM `user` WHERE `u_email` = '" . escape_string($f['u_email']) . "'";
	$result = sqlQuery($query);
	$num = mysqli_num_rows($result);
	// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
	if ($num > 0) {
		$fehlermeldung .= $t['registrierung_fehler_email_bereits_vorhanden'];
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
	$text .= $t['registrierung_informationen'];
	
	$text .= "<form action=\"index.php?bereich=neu\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "<input type=\"hidden\" name=\"u_email\" value=\"$f[u_email]\">\n";
	$text .= "<input type=\"hidden\" name=\"hash\" value=\"$f[hash]\">\n";
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Neuen Account registrieren
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['registrierung_neuen_account_registrieren'], "", "", 0, "70", "");
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $t['login_benutzername'], "u_nick", $f['u_nick'], 0, "70", $t['login_benutzername_beschreibung']);
	$zaehler++;
	
	// Passwort
	$text .= zeige_formularfelder("password", $zaehler, $t['login_passwort'], "u_passwort", $f['u_passwort'], 0, "70", "*");
	$zaehler++;
	
	// Passwort wiederholen
	$text .= zeige_formularfelder("password", $zaehler, $t['login_passwort_wiederholen'], "u_passwort2", $f['u_passwort2'], 0, "70", "*");
	$zaehler++;
	
	// Registrierung absenden
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
}

if ($weiter_zu_login) {
	$text = $t['registrierung_erfolgreich'];
	$text .= $t['registrierung_erfolgreich2'];
	
	$text .= "<br><b>" . $t['login_benutzername'] . ":</b> " . $f['u_nick'] . "<br><br>";
	
	$text .= $t['registrierung_erfolgreich3'];
	$text .= "<br>";
	
	// Daten in DB als Benutzer eintragen
	$text .= "<form action=\"index.php\" name=\"login\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"einloggen\">\n";
	$text .= "<input type=\"hidden\" name=\"username\" value=\"$f[u_nick]\">\n";
	$text .= "<input type=\"hidden\" name=\"passwort\" value=\"$f[u_passwort]\">\n";
	$text .= "<input type=\"submit\" value=\"$t[login_weiter_zum_chat]\">\n";
	$text .= "</form>\n";
	
	$f['u_level'] = "U";
	
	$u_id = schreibe_db("user", $f, "", "u_id");
	$result = sqlUpdate("UPDATE user SET u_neu=DATE_FORMAT(now(),\"%Y%m%d%H%i%s\") WHERE u_id=$u_id");
	
	// E-Mail überprüfen
	$query = "DELETE FROM mail_check WHERE email = '" . escape_string($f['u_email']) . "'";
	$result = sqlUpdate($query);
}
?>