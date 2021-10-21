<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, u_level, o_js
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, 'mini');
?>
<script>
		window.focus()
		function win_reload(file,win_name) {
				win_name.location.href=file;
}
		function opener_reload(file,frame_number) {
				opener.parent.frames[frame_number].location.href=file;
}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

// Optional via JavaScript den oberen Werbeframe mit dem Werbeframe des Raums neu laden
if ($erweitertefeatures) {
	
	$query = "SELECT r_werbung FROM raum WHERE r_id=" . intval($o_raum);
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) != 0) {
		$txt = mysqli_result($result, 0, 0);
		if (strlen($txt) > 7)
			$frame_online = $txt;
	}
	mysqli_free_result($result);
}
// Frameset refreshen, falls reset=1, dann Fenster schliessen
if (isset($reset) && $reset && $o_js) {
	if (isset($forum) && $forum) {
		
		echo "<script>";
		if ($frame_online != "")
			echo "opener_reload('$frame_online','0');\n";
		echo "opener_reload('forum.php?id=$id','1');\n"
			. "opener_reload('messages-forum.php?id=$id','3');\n"
			. "opener_reload('interaktiv-forum.php?id=$id','4');\n"
			. "window.close();\n" . "</script>\n";
	} elseif ($u_level == "M") {
		echo "<script>";
		if ($frame_online != "")
			echo "opener_reload('$frame_online','0');\n";
		echo "opener_reload('chat.php?id=$id&back=$chat_back','1');\n";
		if ($userframe_url) {
			echo "opener_reload('$userframe_url','2');\n";
		} else {
			echo "opener_reload('user.php?id=$id&aktion=chatuserliste','2');\n";
		}
		echo "opener_reload('eingabe.php?id=$id','3');\n"
			. "opener_reload('moderator.php?id=$id','4');\n"
			. "opener_reload('interaktiv.php?id=$id&o_raum_alt=$o_raum','5');\n"
			. "window.close();\n" . "</script>\n";
	} else {
		echo "<script>";
		if (isset($frame_online) && $frame_online != "")
			echo "opener_reload('$frame_online','0');\n";
		echo "opener_reload('chat.php?id=$id&back=$chat_back','1');\n";
		if (isset($userframe_url)) {
			echo "opener_reload('$userframe_url','2');\n";
		} else {
			echo "opener_reload('user.php?id=$id&aktion=chatuserliste','2');\n";
		}
		echo "opener_reload('eingabe.php?id=$id','3');\n"
			. "opener_reload('interaktiv.php?id=$id&o_raum_alt=$o_raum','4');\n"
			. "window.close();\n" . "</script>\n";
	}
}

// Chat neu aufbauen, damit nach Umstellung der Chat refresht wird
if (strlen($u_id) > 0) {
	unset($f['u_id']);
	unset($f['u_level']);
	unset($f['u_nick']);
	unset($f['u_auth']);
	unset($f['u_passwort']);
	schreibe_db("user", $f, $u_id, "u_id");
	if ($o_js) {
		echo "<script language=JavaScript>"
			. "opener_reload('chat.php?id=$id&back=$chat_back','1')\n"
			. "opener_reload('eingabe.php?id=$id','3')"
			. "</script>\n";
	}
}

switch ($aktion) {
	case "logout":
		$box = $t['hilfe15'];
		$text = str_replace("%zeit%", $chat_timeout / 60, $t['hilfe16']);
		
		show_box_title_content($box, $text);
		
		echo "<br><br>";
		break;
	
	default;
	// Übersicht
}

echo schliessen_link();
?>
<br>
</body>
</html>