<?php
// Gibt die Kopfzeile im Login aus
show_kopfzeile_login();

$box = $t['impressum1'];
$text = "<b>" . $t['impressum2'] . "</b>";
$text .= "<br>";
$text .= $impressum_name;
$text .= "<br>";
$text .= $impressum_strasse;
$text .= "<br>";
$text .= $impressum_plz_ort;

show_box_title_content($box,$text);

zeige_fuss();
?>