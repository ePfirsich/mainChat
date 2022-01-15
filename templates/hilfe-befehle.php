<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

// Erklärung zu den Befehlen
$text = '';
$box = $t['hilfe0'];

// Tabelle ausgeben
reset($hilfe_befehlstext);
$anzahl = count($hilfe_befehlstext);
$i = 0;

$text .= "<div style=\"text-align:center;\">$t[hilfe1]</div>";

// Befehle für alle Benutzer
$text .= "<table style=\"width:100%;\">\n";
$text .= "<tr>\n";
$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe17]</td>";
$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe18]</td>";
$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe19]</td>";
$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe20]</td>";
$text .= "</tr>\n";

while ($i < $anzahl) {
	// Farben umschalten
	if (($i % 2) > 0) {
		$bgcolor = 'class="tabelle_zeile1"';
	} else {
		$bgcolor = 'class="tabelle_zeile2"';
	}
	
	$spname = key($hilfe_befehlstext);
	$spruchtmp = preg_split("/\t/", $hilfe_befehlstext[$spname], 4);
	if (!isset($spruchtmp[0])) {
		$spruchtmp[0] = "&nbsp;";
	}
	if (!isset($spruchtmp[1])) {
		$spruchtmp[1] = "&nbsp;";
	}
	if (!isset($spruchtmp[2])) {
		$spruchtmp[2] = "&nbsp;";
	}
	if (!isset($spruchtmp[3])) {
		$spruchtmp[3] = "&nbsp;";
	}
	$text .= "<tr>"
		. "<td $bgcolor>&nbsp;<b>$spruchtmp[0]</b></td>\n"
		. "<td $bgcolor>$spruchtmp[1]</td>\n"
		. "<td $bgcolor>$spruchtmp[2]</td>\n"
		. "<td $bgcolor>$spruchtmp[3]</td>\n"
		. "</tr>";
	next($hilfe_befehlstext);
	
	$i++;
}
$text .= "</table>\n";

if ( $u_level == 'C' || $u_level == 'S' || $u_level == 'A') {
	
	reset($hilfe_befehlstext_admin);
	$anzahl = count($hilfe_befehlstext_admin);
	$i = 0;
	
	$text .= "<div style=\"text-align:center;\"><b>$t[hilfe8]</b></div>";
	
	// Befehle für Admins
	$text .= "<table style=\"width:100%;\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe17]</td>";
	$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe18]</td>";
	$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe19]</td>";
	$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe20]</td>";
	
	while ($i < $anzahl) {
		// Farben umschalten
		if (($i % 2) > 0) {
			$bgcolor = 'class="tabelle_zeile1"';
		} else {
			$bgcolor = 'class="tabelle_zeile2"';
		}
		
		$spname = key($hilfe_befehlstext_admin);
		$spruchtmp = preg_split("/\t/",
			$hilfe_befehlstext_admin[$spname], 4);
		if (!isset($spruchtmp[0])) {
			$spruchtmp[0] = "&nbsp;";
		}
		if (!isset($spruchtmp[1])) {
			$spruchtmp[1] = "&nbsp;";
		}
		if (!isset($spruchtmp[2])) {
			$spruchtmp[2] = "&nbsp;";
		}
		if (!isset($spruchtmp[3])) {
			$spruchtmp[3] = "&nbsp;";
		}
		$text .= "<tr>"
				. "<td $bgcolor>&nbsp;<b>$spruchtmp[0]</b></td>\n"
				. "<td $bgcolor>$spruchtmp[1]</td>\n"
				. "<td $bgcolor>$spruchtmp[2]</td>\n"
				. "<td $bgcolor>$spruchtmp[3]</td>\n"
				. "</tr>";
		next($hilfe_befehlstext_admin);
		
		$i++;
	}
	$text .= "</table>\n";
}

zeige_tabelle_zentriert($box,$text);
?>