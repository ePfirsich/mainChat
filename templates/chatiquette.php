<?php
// Direkten Aufruf der Datei verbieten
if( !isset($aktion)) {
	die;
}

$text = "$chatiquette";

zeige_tabelle_volle_breite($t['login_chatiquette'], $text);
?>