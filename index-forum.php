<?php

require_once("functions/functions.php");
require_once("functions/functions-forum.php");
// Benutzerdaten setzen
id_lese($id);

// Raumwechsel nicht erlaubt, wenn alter Raum Teergrube (ausser für Admins + Tempadmins)

// Info zu altem Raum lesen
$query = "SELECT r_name,r_status1,r_austritt from raum WHERE r_id=" . intval($o_raum);

$result = mysqli_query($mysqli_link, $query);

if ($result && mysqli_num_rows($result) == 1) {
	$alt = mysqli_fetch_object($result);
	mysqli_free_result($result);
}

if ($alt->r_status1 == "L" && $u_level != "A" && !$admin)
	$darf_forum = false;
else $darf_forum = true;

if (!$darf_forum) {
	// Da manche Benutzer immernoch übers Forum gehen, weil sie den link fürs Forum kopieren
	// erstmal ein Logout, bis ich ne Möglichkeit gefunden habe, das schöner zu machen
	Header("Location: index.php");
	exit();
}

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