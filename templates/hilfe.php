<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$box = $lang['hilfe_hilfe'];
$text = $hilfe_uebersichtstext;

zeige_tabelle_zentriert($box,$text);
?>