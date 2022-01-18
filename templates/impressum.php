<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$text = "<b>" . $t['impressum'] . "</b>";
$text .= "<br>";
$text .= $impressum_name;
$text .= "<br>";
$text .= $impressum_strasse;
$text .= "<br>";
$text .= $impressum_plz_ort;
?>