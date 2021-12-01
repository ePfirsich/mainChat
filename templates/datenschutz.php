<?php
// Direkten Aufruf der Datei verbieten
if( !isset($aktion)) {
	die;
}

$text = $t['datenschutzerklaerung'];

zeige_tabelle_volle_breite($t['login_datenschutzerklaerung'], $text);
?>