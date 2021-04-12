<?php

include("functions.php");
include("functions.php-forum.php");
// Userdaten setzen
id_lese($id);

// Raumwechsel nicht erlaubt, wenn alter Raum Teergrube (ausser für Admins + Tempadmins)

// Info zu altem Raum lesen
$query = "SELECT r_name,r_status1,r_austritt from raum "
	. "WHERE r_id=" . intval($o_raum);

$result = mysqli_query($mysqli_link, $query);

if ($result && mysqli_num_rows($result) == 1) {
	$alt = mysqli_fetch_object($result);
	mysqli_free_result($result);
}

if ($alt->r_status1 == "L" && $u_level != "A" && !$admin)
	$darf_forum = false;
else $darf_forum = true;

if (!$darf_forum) {
	// Da manche User immernoch übers Forum gehen, weil sie den link fürs Forum kopieren
	// erstmal ein Logout, bis ich ne Möglichkeit gefunden habe, das schöner zu machen
	Header("Location: index.php");
	exit();
}

//Userdaten u_gelesene_postings bereinigen
bereinige_u_gelesene_postings($u_id);

//Bereinige Anzahl Themen und Antworten wenn ein SU das Forum betritt
if ($u_level == "S") {
	bereinige_anz_in_thema();
}

//ins forum wechseln
gehe_forum($u_id, $u_nick, $o_id, $o_raum);

// Obersten Frame definieren
if (isset($frame_online) && strlen($frame_online) == 0) {
	$frame_online = "frame_online.php";
}

// Falls user eigene Einstellungen für das Frameset hat -> überschreiben
$sql = "SELECT `u_frames` FROM `user` WHERE `u_id` = $u_id";
$query = mysqli_query($mysqli_link, $sql);
$u_frames = mysqli_result($query, 0, "u_frames");
if ($u_frames) {
	$u_frames = unserialize($u_frames);
	if (is_array($u_frames)) {
		foreach ($u_frames as $key => $val) {
			if ($val) {
				$frame_size[$key] = $val;
			}
		}
	}
}

$title = $body_titel;
zeige_header_anfang($title, 'login');

zeige_header_ende();

echo "<FRAMESET ROWS=\"$frame_online_size,*,5,"
	. $frame_size['interaktivforum']
	. ",1\" border=0 frameborder=0 framespacing=0>\n";
echo "<FRAME SRC=\"$frame_online\" name=\"frame_online\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
echo "<FRAME SRC=\"forum.php?id=$id\" name=\"forum\" MARGINWIDTH=\"0\" MARGINHEIGHT=\"0\" SCROLLING=AUTO>\n";
echo "<FRAME SRC=\"leer.php\" name=\"leer\" MARGINWIDTH=\"0\" MARGINHEIGHT=\"0\" SCROLLING=NO>\n";
echo "<FRAMESET COLS=\"*," . $frame_size['messagesforum']
	. "\" border=0 frameborder=0 framespacing=0>\n";
echo "<FRAME SRC=\"messages-forum.php?id=$id\" name=\"messages\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=AUTO>\n";
echo "<FRAME SRC=\"interaktiv-forum.php?id=$id\" name=\"interaktiv\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
echo "</FRAMESET>\n";
echo "<FRAME SRC=\"schreibe.php?id=$id&o_who=2\" name=\"schreibe\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
echo "</FRAMESET>\n";
echo "<NOFRAMES>\n";
echo "<body " . (isset($t['login6']) ? $t['login6'] : "");
?>
</body>
</html>