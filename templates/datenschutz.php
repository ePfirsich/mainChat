<?php
// Gibt die Kopfzeile im Login aus
show_kopfzeile_login();

$box = $t['datenschutzerklaerung1'];
$text = $t['datenschutzerklaerung2'];

show_box_title_content($box,$text);

zeige_fuss();
?>