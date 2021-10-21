<?php
// Gibt die Kopfzeile im Login aus
show_kopfzeile_login();

$box = $t['hilfe11'];
$text = $hilfe_community;
$text = str_replace("%chat_url%", $chat_url, $hilfe_community);

show_box_title_content($box,$text);

zeige_fuss();
?>