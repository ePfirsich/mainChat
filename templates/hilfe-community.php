<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$box = $lang['hilfe_community'];
$text = $hilfe_community;
$text = str_replace("%chat_url%", $chat_url, $hilfe_community);

zeige_tabelle_zentriert($box,$text);
?>