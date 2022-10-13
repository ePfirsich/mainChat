<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$zaehler = 0;
$text .= "<table style=\"width:100%;\">";

if ($zaehler % 2 != 0) {
	$bgcolor = 'class="tabelle_zeile2"';
} else {
	$bgcolor = 'class="tabelle_zeile1"';
}
$text .= "<tr>\n";
$text .= "<td $bgcolor>" . $lang['datenschutzerklaerung'] . "</td>\n";
$text .= "</tr>\n";
$zaehler++;

$text .= "</table>";
?>