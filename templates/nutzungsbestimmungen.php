<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$text = $t['chat_agb'] . "\n";

zeige_tabelle_volle_breite($t['login_nutzungsbestimmungen'], $text);
?>