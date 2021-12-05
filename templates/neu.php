<?php
// Direkten Aufruf der Datei verbieten
if( !isset($aktion)) {
	die;
}

$fehlermeldung = "";

// Daten aus der E-Mail
$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
$hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_URL);

// Daten aus dem Formular
$formular = filter_input(INPUT_POST, 'formular', FILTER_SANITIZE_URL);
$f['u_nick'] = htmlspecialchars(filter_input(INPUT_POST, 'u_nick', FILTER_SANITIZE_STRING));
$f['u_passwort'] = htmlspecialchars(filter_input(INPUT_POST, 'u_passwort', FILTER_SANITIZE_STRING));
$f['u_passwort2'] = htmlspecialchars(filter_input(INPUT_POST, 'u_passwort2', FILTER_SANITIZE_STRING));
$f['u_adminemail'] = filter_input(INPUT_POST, 'u_adminemail', FILTER_VALIDATE_EMAIL);
$f['hash'] = filter_input(INPUT_POST, 'hash', FILTER_SANITIZE_URL);

$formular_anzeigen = true;
$weiter_zu_login = false;

if ( ($email != "" && $hash != md5($email . "+" . date("Y-m-d"))) ) {
	// Fehlermeldung wenn der Link aus der E-Mail verändert wurde oder zu alt ist
	$fehlermeldung = $t['registrierung_fehler_email_link_falsch'];
	unset($formular);
	$formular_anzeigen = false;
} else if ($email != "" && $hash == md5($email . "+" . date("Y-m-d"))) {
	// Korrekter Aufruf
} else if ($f['u_adminemail'] != "" && $f['hash'] == md5($f['u_adminemail'] . "+" . date("Y-m-d"))) {
	// Korrekter Aufruf
} else {
	// Alle weiteren Fälle werden abgelehnt
	$fehlermeldung = $t['registrierung_fehler_email_link_falsch'];
	unset($formular);
	$formular_anzeigen = false;
}

// Eingaben prüfen
if( isset($formular) && $formular == "abgesendet") {
	// Sind der Hash und die interne E-Mail gesetzt
	if ( !isset($f['hash']) || $f['hash'] == "" || !isset($f['u_adminemail'])|| $f['u_adminemail'] == "" ) {
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
			$fehlermeldung .= $t['registrierung_fehler_benutzername_pluszeichen'];
		}
		
		if (strlen($f['u_nick']) > 20 || strlen($f['u_nick']) < 4) {
			$fehlermeldung .= str_replace("%zeichen%", $check_name, $t['registrierung_fehler_benutzername_zeichenlaenge']);
		}
		
		if (preg_match("/^" . $t['login_gast'] . "/i", $f['u_nick'])) {
			$fehlermeldung .= str_replace("%gast%", $t['login_gast'], $t['registrierung_fehler_benutzername_gast']);
		}
		
		if (strlen($f['u_passwort']) < 4) {
			$fehlermeldung .= $t['registrierung_fehler_passwort_zeichenlaenge'];
		}
		
		if ($f['u_passwort'] != $f['u_passwort2']) {
			$fehlermeldung .= $t['registrierung_fehler_passwort_unterschiedlich'];
			$f['u_passwort'] = "";
			$f['u_passwort2'] = "";
		}
		
		if (strlen($f['u_adminemail']) == 0 || !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", $f['u_adminemail'])) {
			$fehlermeldung .= $t['registrierung_fehler_adminemail_falsch'];
		}
		
		// Gibt es den Benutzernamen schon?
		$query = "SELECT `u_id` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $f['u_nick']) . "'";
		
		$result = mysqli_query($mysqli_link, $query);
		$rows = mysqli_num_rows($result);
		
		if ($rows != 0) {
			$fehlermeldung .= $t['registrierung_fehler_benutzername_vergeben'];
		}
		mysqli_free_result($result);
	}
	
	// Prüfe ob Mailadresse schon zu oft registriert, durch ZURÜCK Button bei der 1. registrierung
	$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
	$result = mysqli_query($mysqli_link, $query);
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
	$f['u_adminemail'] = $email;
	$f['hash'] = $hash;
}

if($fehlermeldung != "") {
	zeige_tabelle_volle_breite($t['login_fehlermeldung'], $fehlermeldung);
}

if ( (isset($email) || $fehlermeldung != "") && $formular_anzeigen) {
	$text = $t['registrierung_informationen'];
	
	$text .= "<form action=\"index.php\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"neu\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"abgesendet\">\n";
	$text .= "<input type=\"hidden\" name=\"u_adminemail\" value=\"$f[u_adminemail]\">\n";
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
	
	zeige_tabelle_volle_breite($t['login_registrierung'], $text);
}

if ($weiter_zu_login) {
	$text = $t['registrierung_erfolgreich'];
	$text .= $t['registrierung_erfolgreich2'];
	
	$text .= "<br><b>" . $t['login_benutzername'] . ":</b> " . $f['u_nick'] . "<br><br>";
	
	$text .= $t['registrierung_erfolgreich3'];
	$text .= "<br>";
	
	// Daten in DB als Benutzer eintragen
	$text .= "<form action=\"$chat_url\" name=\"login\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"login\" value=\"$f[u_nick]\">\n";
	$text .= "<input type=\"hidden\" name=\"passwort\" value=\"$f[u_passwort]\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"login\">\n";
	$text .= "<input type=\"submit\" value=\"$t[login_weiter_zum_chat]\">\n";
	$text .= "</form>\n";
	
	zeige_tabelle_volle_breite($t['login_registrierung'], $text);
	
	$f['u_level'] = "U";
	$f['u_loginfehler'] = "";
	
	$u_id = schreibe_db("user", $f, "", "u_id");
	$result = mysqli_query($mysqli_link, "UPDATE user SET u_neu=DATE_FORMAT(now(),\"%Y%m%d%H%i%s\") WHERE u_id=$u_id");
	
	// E-Mail überprüfen
	$query = "DELETE FROM mail_check WHERE email = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
	$result = mysqli_query($mysqli_link, $query);
}
?>