<?php

require_once("functions/functions.php");
require_once("functions/functions-forum.php");
// Benutzerdaten setzen
id_lese($id);

// Benutzerdaten u_gelesene_postings bereinigen
bereinige_u_gelesene_postings($u_id);

// Bereinige Anzahl Themen und Antworten wenn ein SU das Forum betritt
if ($u_level == "S") {
	bereinige_anz_in_thema();
}

// Ins Forum wechseln
gehe_forum($u_id, $u_nick, $o_id, $o_raum);

$title = $body_titel;
zeige_header_anfang($title, 'login', '', $u_layout_farbe);
zeige_header_ende();

require_once("functions/functions-frameset.php");

frameset_forum($id);
?>
</html>