<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}

$text = $hilfe_spruchtext;
$box = $t['hilfe3'];

zeige_tabelle_zentriert($box, $text);

// Liste mit Sprüchen ausgeben
$text = "<br>";
$box = $t['hilfe4'];

// Sprüche in Array einlesen
$spruchliste = file("conf/$datei_spruchliste");

reset($spruchliste);
$anzahl = count($spruchliste);
$i = 0;
$bgcolor = 'class="tabelle_zeile1"';

// Sprüche ausgeben

$text .= "<table style=\"width:100%;\">\n";
$text .= "<tr>\n";
$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe21]</td>";
$text .= "<td class=\"tabelle_kopfzeile\" style=\"text-align:center;\">$t[hilfe22]</td>";
$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe23]</td>";
$text .= "</tr>\n";

while ($i < $anzahl) {
	$spname = key($spruchliste);
	$spruchtmp = preg_split("/\t/",
		substr($spruchliste[$spname], 0,
			strlen($spruchliste[$spname]) - 1), 3);
	
	$spruchtmp[2] = str_replace("<", "&lt;", $spruchtmp[2]);
	$spruchtmp[2] = str_replace(">", "&gt;", $spruchtmp[2]);
	$spruchtmp[2] = preg_replace('|\*(.*?)\*|', '<i>\1</i>',
		preg_replace('|_(.*?)_|', '<b>\1</b>', $spruchtmp[2]));
	
	$text .= "<tr>"
		. "<td style=\"width:15%;\" $bgcolor>" . $f1 . "&nbsp;<b>$spruchtmp[0]</b>" . $f2 . "</td>\n"
			. "<td style=\"width:10%; text-align:center;\" $bgcolor>" . $f1 . "<b>$spruchtmp[1]</b>" . $f2 . "</td>\n"
				. "<td style=\"width:75%;\" $bgcolor>" . $f1 . "&lt;$spruchtmp[2]&gt;" . $f2 . "</td>\n"
					. "</tr>\n";
					next($spruchliste);
					
					// Farben umschalten
					if (($i % 2) > 0) {
						$bgcolor = 'class="tabelle_zeile1"';
					} else {
						$bgcolor = 'class="tabelle_zeile2"';
					}
					
					$i++;
}

$text .= "</table>\n";

zeige_tabelle_zentriert($box,$text);
?>