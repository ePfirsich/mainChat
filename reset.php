<?php

require_once("functions/functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, u_level, o_js
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, 'mini', '', $u_layout_farbe);
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

// Frameset refreshen
if ($o_js) {
	if (isset($forum) && $forum) {
		echo "<script>";
		if ($frame_online != "") {
			echo "opener_reload('$frame_online','0');\n";
		}
		echo "opener_reload('navigation-forum.php?id=$id','1');\n";
		echo "opener_reload('forum.php?id=$id','2');\n";
		echo "window.close();\n" . "</script>\n";
	} else if ($u_level == "M") {
		echo "<script>\n";
		if ($frame_online != "") {
			echo "opener_reload('$frame_online','0');\n";
		}
		echo "opener_reload('navigation-chat.php?id=$id','1');\n";
		echo "opener_reload('chat.php?id=$id&back=$chat_back','2');\n";
		echo "opener_reload('user.php?id=$id','3');\n";
		echo "opener_reload('eingabe.php?id=$id','4');\n"
			. "opener_reload('moderator.php?id=$id','5');\n"
			. "opener_reload('interaktiv.php?id=$id&o_raum_alt=$o_raum','6');\n"
			. "window.close();\n" . "</script>\n";
	} else {
		echo "<script>";
		if (isset($frame_online) && $frame_online != "") {
			echo "opener_reload('$frame_online','0');\n";
		}
		echo "opener_reload('navigation-chat.php?id=$id','1');\n";
		echo "opener_reload('chat.php?id=$id&back=$chat_back','2');\n";
		echo "opener_reload('user.php?id=$id','3');\n";
		echo "opener_reload('eingabe.php?id=$id','4');\n"
			. "opener_reload('interaktiv.php?id=$id&o_raum_alt=$o_raum','5');\n"
			. "window.close();\n" . "</script>\n";
	}
}
?>
</body>
</html>