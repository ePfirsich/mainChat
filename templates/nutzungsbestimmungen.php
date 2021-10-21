<?php
// Gibt die Kopfzeile im Login aus
show_kopfzeile_login();

if (isset($extra_agb) and ($extra_agb <> "") && ($extra_agb <> "standard")) {
	$t['agb'] = $extra_agb;
}
$box = $t['hilfe14'];
$text = $t['agb'] . "\n";

show_box_title_content($box,$text);

zeige_fuss();
?>