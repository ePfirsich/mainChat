<?php
$text = "<b>" . $t['impressum'] . "</b>";
$text .= "<br>";
$text .= $impressum_name;
$text .= "<br>";
$text .= $impressum_strasse;
$text .= "<br>";
$text .= $impressum_plz_ort;

zeige_tabelle_volle_breite($t['login_impressum'], $text);
?>