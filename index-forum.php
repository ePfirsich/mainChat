<?php

require_once("functions/functions.php");
require_once("functions/functions-forum.php");
// Benutzerdaten setzen
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == "") {
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

// Benutzerdaten u_gelesene_postings bereinigen
bereinige_u_gelesene_postings($u_id);

// Bereinige Anzahl Themen und Antworten wenn ein Superuser das Forum betritt
if ($u_level == "S") {
	bereinige_anz_in_thema();
}

// Ins Forum wechseln
gehe_forum($u_id, $u_nick, $o_id, $o_raum);

$title = $body_titel;
zeige_header_anfang($title, 'login', '', $benutzerdaten['u_layout_farbe']);
zeige_header_ende();

require_once("functions/functions-frameset.php");

frameset_forum($id);
?>
</html>