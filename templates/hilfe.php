<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id)) {
	die;
}

$box = $t['hilfe6'];
$text = $hilfe_uebersichtstext;

zeige_tabelle_zentriert($box,$text);
?>