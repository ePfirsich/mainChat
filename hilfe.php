<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

$title = $body_titel . ' - Info';
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
	@mysqli_free_result($result);
}
// Frameset refreshen, falls reset=1, dann Fenster schliessen
if (isset($reset) && $reset && $o_js) {
	if (isset($forum) && $forum) {
		
		echo "<script>";
		if ($frame_online != "")
			echo "opener_reload('$frame_online?http_host=$http_host','0');\n";
		echo "opener_reload('forum.php?http_host=$http_host&id=$id','1');\n"
			. "opener_reload('messages-forum.php?http_host=$http_host&id=$id','3');\n"
			. "opener_reload('interaktiv-forum.php?http_host=$http_host&id=$id','4');\n"
			. "window.close();\n" . "</script>\n";
	} elseif ($u_level == "M") {
		echo "<script>";
		if ($frame_online != "")
			echo "opener_reload('$frame_online?http_host=$http_host','0');\n";
		echo "opener_reload('chat.php?http_host=$http_host&id=$id&back=$chat_back','1');\n";
		if ($userframe_url) {
			echo "opener_reload('$userframe_url','2');\n";
		} else {
			echo "opener_reload('user.php?http_host=$http_host&id=$id&aktion=chatuserliste','2');\n";
		}
		echo "opener_reload('eingabe.php?http_host=$http_host&id=$id','3');\n"
			. "opener_reload('moderator.php?http_host=$http_host&id=$id','4');\n"
			. "opener_reload('interaktiv.php?http_host=$http_host&id=$id&o_raum_alt=$o_raum','5');\n"
			. "window.close();\n" . "</script>\n";
	} else {
		echo "<script>";
		if (isset($frame_online) && $frame_online != "")
			echo "opener_reload('$frame_online?http_host=$http_host','0');\n";
		echo "opener_reload('chat.php?http_host=$http_host&id=$id&back=$chat_back','1');\n";
		if (isset($userframe_url)) {
			echo "opener_reload('$userframe_url','2');\n";
		} else {
			echo "opener_reload('user.php?http_host=$http_host&id=$id&aktion=chatuserliste','2');\n";
		}
		echo "opener_reload('eingabe.php?http_host=$http_host&id=$id','3');\n"
			. "opener_reload('interaktiv.php?http_host=$http_host&id=$id&o_raum_alt=$o_raum','4');\n"
			. "window.close();\n" . "</script>\n";
	}
}

// Chat bei u_backup neu aufbauen, damit nach Umstellung der Chat refresht wird
// u_backup in DB eintragen
if (strlen($u_id) > 0 && isset($f['u_backup']) && strlen($f['u_backup']) > 0) {
	unset($f['u_id']);
	unset($f['u_level']);
	unset($f['u_name']);
	unset($f['u_nick']);
	unset($f['u_auth']);
	unset($f['u_passwort']);
	schreibe_db("user", $f, $u_id, "u_id");
	if ($f['u_backup'] == 1)
		warnung($u_id, $u_nick, "sicherer_modus");
	if ($o_js) {
		echo "<script LANGUAGE=JavaScript>"
			. "opener_reload('chat.php?http_host=$http_host&id=$id&back=$chat_back','1')\n"
			. "opener_reload('eingabe.php?http_host=$http_host&id=$id','3')"
			. "</script>\n";
	}
}

// Menü als erstes ausgeben
$box = $t['menue4'];
$text = "<a href=\"hilfe.php?http_host=$http_host&id=$id\">$t[menue1]</a>\n"
	. "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=befehle\">$t[menue2]</a>\n"
	. "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=sprueche\">$t[menue3]</a>\n";
if ($communityfeatures) {
	$text .= "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=legende\">$t[menue6]</a>\n";
	$text .= "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=community\">$t[menue7b]</a>\n";
}
$text .= "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=chatiquette\">$t[menue5]</a>\n";
$text .= "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=agb\">$t[menue8]</a>\n";
$text .= "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=privacy\">$t[menue9]</a>\n";
if ($aktion != "logout") {
	show_box2($box, $text);
	echo "<img src=\"pics/fuell.gif\" alt=\"\" style=\"width:4px; height:4px;\"><br>";
}

switch ($aktion) {
	
	case "befehle":
	// Erklärung zu den Befehlen
		$text = '';
		$box = $t['hilfe0'];
		
		// Tabelle ausgeben
		reset($hilfe_befehlstext);
		$anzahl = count($hilfe_befehlstext);
		$i = 0;
		$bgcolor = 'class="tabelle_zeile1"';
		
		$text .= "<div style=\"text-align:center;\">$t[hilfe1]</div>";
		// Befehle für alle User
		$text .= "<table class=\"tabelle_kopf\">\n";
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe17]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe18]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe19]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe20]</td>";
		$text .= "</tr>\n";
		
		while ($i < $anzahl) {
			$spname = key($hilfe_befehlstext);
			$spruchtmp = preg_split("/\t/", $hilfe_befehlstext[$spname], 4);
			if (!isset($spruchtmp[0])) {
				$spruchtmp[0] = "&nbsp;";
			}
			if (!isset($spruchtmp[1])) {
				$spruchtmp[1] = "&nbsp;";
			}
			if (!isset($spruchtmp[2])) {
				$spruchtmp[2] = "&nbsp;";
			}
			if (!isset($spruchtmp[3])) {
				$spruchtmp[3] = "&nbsp;";
			}
			$text .= "<tr>"
				. "<td $bgcolor>" . $f1 . "&nbsp;<b>$spruchtmp[0]</b>" . $f2 . "</td>\n"
				. "<td $bgcolor>" . $f1 . "$spruchtmp[1]" . $f2 . "</td>\n"
				. "<td $bgcolor>" . $f1 . "$spruchtmp[2]" . $f2 . "</td>\n"
				. "<td $bgcolor>" . $f1 . "$spruchtmp[3]" . $f2 . "</td>\n"
				. "</tr>";
			next($hilfe_befehlstext);
			
			// Farben umschalten
			if (($i % 2) > 0) {
				$bgcolor = 'class="tabelle_zeile1"';
			} else {
				$bgcolor = 'class="tabelle_zeile2"';
			}
			
			$i++;
		}
		$text .= "</table>\n";
		
		if ($admin && $hilfe_befehlstext_admin_ok == 1) {
			
			reset($hilfe_befehlstext_admin);
			$anzahl = count($hilfe_befehlstext_admin);
			$i = 0;
			$bgcolor = 'class="tabelle_zeile1"';
			
			$text .= "<br>";
			$text .= "<div style=\"text-align:center;\">$t[hilfe8]</div>";
			// Befehle für Admins
			$text .= "<table class=\"tabelle_kopf\">\n";
			$text .= "<tr>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe17]</td>";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe18]</td>";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe19]</td>";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe20]</td>";
			
			while ($i < $anzahl) {
				$spname = key($hilfe_befehlstext_admin);
				$spruchtmp = preg_split("/\t/",
					$hilfe_befehlstext_admin[$spname], 4);
				if (!isset($spruchtmp[0])) {
					$spruchtmp[0] = "&nbsp;";
				}
				if (!isset($spruchtmp[1])) {
					$spruchtmp[1] = "&nbsp;";
				}
				if (!isset($spruchtmp[2])) {
					$spruchtmp[2] = "&nbsp;";
				}
				if (!isset($spruchtmp[3])) {
					$spruchtmp[3] = "&nbsp;";
				}
				$text .= "<tr>"
					. "<td $bgcolor>" . $f1 . "&nbsp;<b>$spruchtmp[0]</b>" . $f2 . "</td>\n"
					. "<td $bgcolor>" . $f1 . "$spruchtmp[1]" . $f2 . "</td>\n"
					. "<td $bgcolor>" . $f1 . "$spruchtmp[2]" . $f2 . "</td>\n"
					. "<td $bgcolor>" . $f1 . "$spruchtmp[3]" . $f2 . "</td>\n"
					. "</tr>";
				next($hilfe_befehlstext_admin);
				
				// Farben umschalten
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
				}
				
				$i++;
			}
			$text .= "</table>\n";
		}
		
		show_box_title_content($box,$text);
		
		break;
	
	case "sprueche":
	// Erklärung zu den Sprüchen
		$text = '';
		$box = $t['hilfe3'];
		show_box2($box, $hilfe_spruchtext);
		
		$text .= "<img src=\"pics/fuell.gif\" alt=\"\" style=\"width:4px; height:4px;\"><br>";
		
		// Liste mit Sprüchen ausgeben
		$box = $t['hilfe4'];
		
		// Sprüche in Array einlesen
		$spruchliste = file("conf/$datei_spruchliste");
		
		reset($spruchliste);
		$anzahl = count($spruchliste);
		$i = 0;
		$bgcolor = 'class="tabelle_zeile1"';
		
		// Sprüche ausgeben
		$text .= "<table class=\"tabelle_kopf\">\n";
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe21]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\" style=\"text-align:center;\">$t[hilfe22]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[hilfe23]</td>";
		$text .= "</tr>\n";
		
		while ($i < $anzahl) {
			$spname = key($spruchliste);
			$spruchtmp = preg_split("/\t/",
				substr($spruchliste[$spname], 0,
					strlen($spruchliste[$spname]) - 1), 3);
			
			$spruchtmp[2] = str_replace("<", "&lt;", $spruchtmp[2]);
			$spruchtmp[2] = str_replace(">", "&gt;", $spruchtmp[2]);
			$spruchtmp[2] = preg_replace('|\*(.*?)\*|', '<i>\1</i>',
				preg_replace('|_(.*?)_|', '<b>\1</b>', $spruchtmp[2]));
			
			$text .= "<tr>"
				. "<td style=\"width:15%;\" $bgcolor>" . $f1 . "&nbsp;<b>$spruchtmp[0]</b>" . $f2 . "</td>\n"
				. "<td style=\"width:10%; text-align:center;\" $bgcolor>" . $f1 . "<b>$spruchtmp[1]</b>" . $f2 . "</td>\n"
				. "<td style=\"width:75%;\" $bgcolor>" . $f1 . "&lt;$spruchtmp[2]&gt;" . $f2 . "</td>\n"
				. "</tr>\n";
			next($spruchliste);
			
			// Farben umschalten
			if (($i % 2) > 0) {
				$bgcolor = 'class="tabelle_zeile1"';
			} else {
				$bgcolor = 'class="tabelle_zeile2"';
			}
			
			$i++;
		}
		
		$text .= "</table>\n";
		
		show_box_title_content($box,$text);
		
		break;
	
	case "legende":
	// Legende
		$text = '';
		$box = $t['hilfe10'];
		
		$text .= "<table class=\"tabelle_kopf\">\n"
			. $legende . "</table><br>\n"
			. "<a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#punkte\">$t[hilfe12]</a><br><br>\n";
		
		show_box_title_content($box,$text);
		
		break;
	
	case "community":
	// Punkte/Community
		$text = '';
		$box = $t['hilfe11'];
		
		$text .= $hilfe_community . "\n";
		$text .= "<img src=\"pics/fuell.gif\" alt=\"\" style=\"width:4px; height:4px;\"><br>";
		
		show_box_title_content($box,$text);
		
		break;
	
	case "agb":
	// Nutzungsbestimmungen
		$text = '';
		include("conf/" . $sprachconfig . "-index.php");
		if (isset($extra_agb) and ($extra_agb <> "")
			and ($extra_agb <> "standard"))
			$t['agb'] = $extra_agb;
		$box = $t['hilfe14'];
		
		$text .= $t['agb'] . "\n";
		$text .= "<img src=\"pics/fuell.gif\" alt=\"\" style=\"width:4px; height:4px;\"><br>";
		
		show_box_title_content($box,$text);
		
		break;
	
	case "privacy":
	// Datenschutz
		$text = '';
		$box = $t['hilfe13'];
		
		$text .=  $hilfe_privacy . "\n";
		$text .= "<img src=\"pics/fuell.gif\" alt=\"\" style=\"width:4px; height:4px;\"><br>";
		
		show_box_title_content($box,$text);
		
		break;
	
	case "chatiquette":
	// Chatiquette
		$text = '';
		$box = $t['hilfe9'];
		
		$text .= "$chatiquette\n";
		$text .= "<img src=\"pics/fuell.gif\" alt=\"\" style=\"width:4px; height:4px;\"><br>";
		
		show_box_title_content($box,$text);
		
		break;
	
	case "logout":
		$box = $t['hilfe15'];
		$text = str_replace("%zeit%", $chat_timeout / 60, $t['hilfe16']);
		show_box2($box, $text);
		
		echo "<br><br>";
		break;
	
	default;
	// Übersicht
		$text = '';
		$box = $t['hilfe6'];
		
		$text .= $hilfe_uebersichtstext;
		$text .= "<img src=\"pics/fuell.gif\" alt=\"\" style=\"width:4px; height:4px;\"><br>";
		
		show_box_title_content($box,$text);
}

echo $f1 . "<p style=\"text-align:center;\">[<a href=\"javascript:window.close();\">$t[sonst1]</a>]</p>" . $f2 . "\n";
?>
<br>
</body>
</html>