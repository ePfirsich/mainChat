<?php 
function zeige_formularfelder($art_der_anzeige, $zaehler, $name, $key, $value, $ausgewaehltes_feld_selectbox = 0, $breite_eingabefeld = "70", $beschreibung = "") {
	// Hintergrundfarbe mit jeder Zeile wechseln
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	
	$text = "";
	
	$text .= "<tr>\n";
	if($art_der_anzeige == "selectbox") {
		// Auswahlfeld anzeigen
		$text .= "<td style=\"text-align:right; width:300px;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor>";
		
		$text .= "<select name=\"$key\">";
		for ($i=0; $i<count($value);$i++) {
			if($i == $ausgewaehltes_feld_selectbox) {
				$selected = " selected";
			} else {
				$selected = "";
			}
			$text .= "<option$selected value=\"$i\">$value[$i]</option>\n";
			$felder[$i];
		}
		$text .= "</select> $beschreibung</td>\n";
	} else if($art_der_anzeige == "input") {
		// Input anzeigen
		$text .= "<td style=\"text-align:right; width:300px;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor><input type=\"text\" name=\"$key\" value=\"$value\" maxlenght=\"160\" size=\"$breite_eingabefeld\"> $beschreibung</td>\n";
	} else if($art_der_anzeige == "password") {
		// Password-Feld anzeigen
		$text .= "<td style=\"text-align:right; width:300px;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor><input type=\"password\" name=\"$key\" value=\"$value\" maxlenght=\"160\" size=\"$breite_eingabefeld\"> $beschreibung</td>\n";
	} else if($art_der_anzeige == "textarea") {
		// Textarea anzeigen
		$text .= "<td style=\"text-align:right; width:300px;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor><textarea name=\"$key\" rows=\"4\" cols=\"" . ($breite_eingabefeld - 10) . "\" maxlenght=\"255\">$value</textarea> $beschreibung</td>\n";
	} else if($art_der_anzeige == "textarea2") {
		// Textarea2 anzeigen
		$text .= "<td style=\"text-align:right; width:300px;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor><textarea class=\"ckeditor\" name=\"$key\" rows=\"20\" cols=\"" . ($breite_eingabefeld - 10) . "\" maxlenght=\"255\">$value</textarea> $beschreibung</td>\n";
	} else if($art_der_anzeige == "ueberschrift") {
		// Überschrift anzeigen
		$text .= "<td class=\"tabelle_kopfzeile\" colspan=\"2\">$name</td>";
	} else if($art_der_anzeige == "leerzeile") {
		// Leerzeile anzeigen
		$text .= "<td colspan=\"2\">&nbsp;</td>";
	} else {
		// Text anzeigen
		$text .= "<td style=\"text-align:right; width:300px;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor>$value $beschreibung</td>\n";
	}
	$text .= "</tr>\n";
	
	return $text;
}
?>