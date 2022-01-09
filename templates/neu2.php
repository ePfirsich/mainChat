<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$fehlermeldung = "";
$aktivierung_erfolgreich = false;

// Daten aus dem Formular
$formular = filter_input(INPUT_POST, 'formular', FILTER_SANITIZE_URL);
$f['email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$f['hash'] = filter_input(INPUT_POST, 'hash', FILTER_SANITIZE_URL);

$text = "";
if( isset($formular) && $formular == "abgesendet") {
	if ( $f['hash'] != md5($f['email'] . "+" . date("Y-m-d")) ) {
		// Fehlermeldung wenn der Link aus der E-Mail verändert wurde oder zu alt ist
		$fehlermeldung = $t['registrierung_fehler_email_freischaltcode'];
	}
	
	if($fehlermeldung != "") {
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		// Keine Fehlermeldung -> Weiterleitung zum Forumlar für die Registrierung
		$aktivierung_erfolgreich = true;
	}
}

if($aktivierung_erfolgreich) {
	// Weiterleitung zum Forumlar für die Registrierung
	$url = "index.php?bereich=neu&email=$f[email]&hash=$f[hash]";
	$text .= str_replace("%url%", $url, $t['registrierung_freischaltcode_erfolgreich']);
	
	zeige_tabelle_volle_breite($t['login_registrierung'], $text);
} else {
	// E-mail und Freischalt-Code eingeben
	$text .= $t['registrierung_informationen_freischaltcode'];
	
	$text .= "<form action=\"index.php?bereich=neu2\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"abgesendet\">\n";
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Neuen Account registrieren
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['registrierung_neuen_account_registrieren'], "", "", 0, "70", "");
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $t['login_email'], "email", $f['email']);
	$zaehler++;
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $t['login_freischalt_code'], "hash", $f['hash']);
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

?>