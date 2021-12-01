<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id)) {
	die;
}

$box = $t['hilfe11'];
$text = $hilfe_community;
$text = str_replace("%chat_url%", $chat_url, $hilfe_community);

zeige_tabelle_zentriert($box,$text);
?>