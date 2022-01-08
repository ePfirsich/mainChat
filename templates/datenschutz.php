<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$text = $t['datenschutzerklaerung'];

zeige_tabelle_volle_breite($t['login_datenschutzerklaerung'], $text);
?>