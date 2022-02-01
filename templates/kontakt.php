<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$kontakt_absender = "";
$kontakt_email = "";
$kontakt_betreff = "";
$kontakt_nachricht = "";
$kontakt_frage = "";

if($formular == 1) {
	$kontakt_absender = htmlspecialchars(filter_input(INPUT_POST, 'kontakt_absender', FILTER_SANITIZE_STRING));
	$kontakt_email = filter_input(INPUT_POST, 'kontakt_email', FILTER_VALIDATE_EMAIL);
	$kontakt_betreff = htmlspecialchars(filter_input(INPUT_POST, 'kontakt_betreff', FILTER_SANITIZE_STRING));
	$kontakt_nachricht = htmlspecialchars(filter_input(INPUT_POST, 'kontakt_nachricht', FILTER_SANITIZE_STRING));
	$kontakt_frage = htmlspecialchars(filter_input(INPUT_POST, 'kontakt_frage', FILTER_SANITIZE_STRING));
	$kontakt_datenschutz = filter_input(INPUT_POST, 'kontakt_datenschutz', FILTER_SANITIZE_NUMBER_INT);
	
	$fehlermeldung = "";
	
	if( $kontakt_absender == "" || strlen($kontakt_absender) < 5 ) {
		$fehlermeldung = $t['kontakt_fehler_absender'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if( $kontakt_email == "" || strlen($kontakt_email) < 5 ) {
		$fehlermeldung = $t['kontakt_fehler_email'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if( $kontakt_betreff == "" || strlen($kontakt_betreff) < 5 ) {
		$fehlermeldung = $t['kontakt_fehler_betreff'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if( $kontakt_nachricht == "" || strlen($kontakt_nachricht) < 15 ) {
		$fehlermeldung = $t['kontakt_fehler_nachricht'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if( $kontakt_frage == "" || strtolower($kontakt_frage) != strtolower($t['kontakt_antwort']) ) {
		$fehlermeldung = $t['kontakt_fehler_sicherheitsfrage'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if( $kontakt_datenschutz == "") {
		$fehlermeldung = $t['kontakt_fehler_datenschutz'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if($fehlermeldung == "") {
		// E-Mail versenden
		$nachricht = str_replace("%name%", $kontakt_absender, $t['kontakt_inhalt']);
		$nachricht = str_replace("%nachricht%", $kontakt_nachricht, $nachricht);
		$nachricht = str_replace("%email%", $kontakt_email, $nachricht);
		
		$email_ok = email_senden($kontakt_email, $kontakt_betreff, $nachricht);
		
		$erfolgsmeldung = $t['kontakt_erfolgsmeldung_email_versendet'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		
		$kontakt_absender = "";
		$kontakt_email = "";
		$kontakt_betreff = "";
		$kontakt_nachricht = "";
		$kontakt_frage = "";
	}
}

$text .= $t['kontakt_beschreibung'];
$text .= "<br><br>";

$text .= "<form action=\"index.php?bereich=kontakt\" method=\"post\">\n";
$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
$text .= "<table style=\"width:100%;\">\n";


// Absender
$text .= zeige_formularfelder("input", $zaehler, $t['kontakt_absender'], "kontakt_absender", $kontakt_absender);
$zaehler++;


// E-Mail
$text .= zeige_formularfelder("input", $zaehler, $t['kontakt_email'], "kontakt_email", $kontakt_email);
$zaehler++;


// Leerzeile
$text .= zeige_formularfelder("text", $zaehler, "", "", "&nbsp;");
$zaehler++;


// Betreff
$text .= zeige_formularfelder("input", $zaehler, $t['kontakt_betreff'], "kontakt_betreff", $kontakt_betreff);
$zaehler++;


// Nachricht
$text .= zeige_formularfelder("textarea", $zaehler, $t['kontakt_nachricht'], "kontakt_nachricht", $kontakt_nachricht);
$zaehler++;


// Sicherheitsfrage
$text .= zeige_formularfelder("input", $zaehler, $t['kontakt_sicherheitsfrage'].$t['kontakt_frage'], "kontakt_frage", $kontakt_frage);
$zaehler++;


// Datenschutz
if ($zaehler % 2 != 0) {
	$bgcolor = 'class="tabelle_zeile2"';
} else {
	$bgcolor = 'class="tabelle_zeile1"';
}
$text .= "<tr>\n";
$text .= "<td style=\"text-align:right;\" $bgcolor>" . $t['kontakt_datenschutz'] . "</td>\n";
$text .= "<td $bgcolor>";
$text .= "<input type=\"checkbox\" name=\"kontakt_datenschutz\" value=\"1\" /> $t[kontakt_datenschutz_beschreibung]";
$text .= "</td>\n";
$text .= "</tr>\n";
$zaehler++;


// Speichern oder zurücksetzen
if ($zaehler % 2 != 0) {
	$bgcolor = 'class="tabelle_zeile2"';
} else {
	$bgcolor = 'class="tabelle_zeile1"';
}
$text .= "<tr>\n";
$text .= "<td $bgcolor><input type=\"reset\" value=\"$t[kontakt_zuruecksetzen]\"></td>\n";
$text .= "<td $bgcolor><input type=\"submit\" value=\"$t[kontakt_absenden]\"></td>\n";
$text .= "</tr>\n";

$text .= "</table>\n";
$text .= "</form>\n";
?>