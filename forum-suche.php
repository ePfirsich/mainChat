<?php

require("functions.php");
require("functions.php-func-forum_lib.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

function show_pfad_posting2($th_id)
{
	
	global $mysqli_link, $f1, $f2, $f3, $f4, $id, $http_host, $thread;
	//Infos über Forum und Thema holen
	$sql = "select fo_id, fo_name, th_name
				from forum, thema
				where th_id = " . intval($th_id) . "
				and fo_id = th_fo_id";
	$query = mysqli_query($mysqli_link, $sql);
	$fo_id = mysqli_result($query, 0, "fo_id");
	$fo_name = htmlspecialchars($query, 0, "fo_name");
	$th_name = htmlspecialchars($query, 0, "th_name");
	@mysqli_free_result($query);
	
	return "$f3<a href=\"#\" onClick=\"opener_reload('forum.php?id=$id&http_host=$http_host#$fo_id',1); return(false);\">$fo_name</a> > <a href=\"#\" onclick=\"opener_reload('forum.php?id=$id&http_host=$http_host&th_id=$th_id&show_tree=$thread&aktion=show_thema&seite=1',1); return(false);\">$th_name</a>$f4";
	
}

function vater_rekursiv($vater)
{
	$query = "SELECT po_id, po_vater_id FROM posting WHERE po_id = " . intval($vater);
	$result = mysqli_query($mysqli_link, $query);
	$a = mysqli_fetch_array($result);
	if (mysqli_num_rows($result) <> 1) {
		return -1;
	} else if ($a['po_vater_id'] <> 0) {
		$vater = vater_rekursiv($a['po_vater_id']);
		return $vater;
	} else {
		return $a['po_id'];
	}
}

function such_bereich()
{
	global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
	global $farbe_tabelle_kopf2;
	global $suche, $t;
	
	$eingabe_breite = 50;
	$select_breite = 250;
	$titel = $t['titel'];
	
	?>
	<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
	<?php
	
	echo "<FORM NAME=\"suche_neu\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"suche\">\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
		. "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
	
	echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2><b>$titel</b></TD></TR>\n"
		. 
		// Suchtext
		"<tr><TD align=\"right\" class=\"tabelle_zeile1\"><b>$t[suche1]</b></TD><TD class=\"tabelle_zeile1\">"
		. $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"suche[text]\" VALUE=\""
		. htmlspecialchars($suche['text'])
		. "\" SIZE=$eingabe_breite>" . $f2 . "</TD></TR>\n";
	
	// Suche in Board/Thema
	echo "<tr><TD align=\"right\" style=\"vertical-align:top;\" class=\"tabelle_zeile1\">$t[suche2]</TD><TD class=\"tabelle_zeile1\">"
		. $f1 . "<SELECT NAME=\"suche[thema]\" SIZE=\"1\" STYLE=\"width: "
		. $select_breite . "px;\">";
	
	$sql = "SELECT fo_id, fo_admin, fo_name, th_id, th_name FROM forum left join thema on fo_id = th_fo_id "
		. "WHERE th_anzthreads <> 0 ORDER BY fo_order, th_order ";
	$query = mysqli_query($mysqli_link, $sql);
	$themaalt = "";
	echo "<OPTION ";
	if (substr($suche['thema'], 0, 1) <> "B")
		echo "SELECTED ";
	echo "VALUE=\"ALL\">$t[option1]</OPTION>";
	while ($thema = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		if (pruefe_leserechte($thema['th_id'])) {
			if ($themaalt <> $thema['fo_name']) {
				echo "<OPTION ";
				if ($suche['thema'] == "B" . $thema['fo_id'])
					echo "SELECTED ";
				echo "VALUE=\"B" . $thema['fo_id'] . "\">" . $thema['fo_name']
					. "</OPTION>";
				$themaalt = $thema['fo_name'];
			}
			echo "<OPTION ";
			if ($suche['thema']
				== "B" . $thema['fo_id'] . "T" . $thema['th_id'])
				echo "SELECTED ";
			echo "VALUE=\"B" . $thema['fo_id'] . "T" . $thema['th_id']
				. "\">&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;" . $thema['th_name']
				. "</OPTION>";
		}
	}
	echo "</SELECT></TD></TR>\n";
	@mysqli_free_result($query);
	
	// Sucheinstelung UND/ODER
	echo "<TR><TD align=\"right\" style=\"vertical-align:top;\" class=\"tabelle_zeile1\">$t[suche3]</TD><TD class=\"tabelle_zeile1\">"
		. $f1 . "<SELECT NAME=\"suche[modus]\" SIZE=\"1\" STYLE=\"width: "
		. $select_breite . "px;\">";
	echo "<OPTION ";
	if ($suche['modus'] <> "O")
		echo "SELECTED ";
	echo "VALUE=\"A\">$t[option2]</OPTION>";
	echo "<OPTION ";
	if ($suche['modus'] == "O")
		echo "SELECTED ";
	echo "VALUE=\"O\">$t[option3]</OPTION>";
	echo "</SELECT></TD></TR>\n";
	
	// Sucheinstellung Betreff/Text
	echo "<TR><TD class=\"tabelle_zeile1\"></TD><TD class=\"tabelle_zeile1\">" . $f1
		. "<SELECT NAME=\"suche[ort]\" SIZE=\"1\" STYLE=\"width: "
		. $select_breite . "px;\">";
	echo "<OPTION ";
	if ($suche['ort'] <> "B" && $suche['ort'] <> "T")
		echo "SELECTED ";
	echo "VALUE=\"V\">$t[option4]</OPTION>";
	echo "<OPTION ";
	if ($suche['ort'] == "B")
		echo "SELECTED ";
	echo "VALUE=\"B\">$t[option5]</OPTION>";
	echo "<OPTION ";
	if ($suche['ort'] == "T")
		echo "SELECTED ";
	echo "VALUE=\"T\">$t[option6]</OPTION>";
	echo "</SELECT></TD></TR>\n";
	
	// Sucheinstellung Zeit
	echo "<TR><TD class=\"tabelle_zeile1\"></TD><TD class=\"tabelle_zeile1\">" . $f1
		. "<SELECT NAME=\"suche[zeit]\" SIZE=\"1\" STYLE=\"width: "
		. $select_breite . "px;\">";
	echo "<OPTION ";
	if (substr($suche['zeit'], 0, 1) <> "B")
		echo "SELECTED ";
	echo "VALUE=\"ALL\">$t[option7]</OPTION>";
	echo "<OPTION ";
	if ($suche['zeit'] == "B1")
		echo "SELECTED ";
	echo "VALUE=\"B1\">$t[option8]</OPTION>";
	echo "<OPTION ";
	if ($suche['zeit'] == "B7")
		echo "SELECTED ";
	echo "VALUE=\"B7\">$t[option9]</OPTION>";
	echo "<OPTION ";
	if ($suche['zeit'] == "B14")
		echo "SELECTED ";
	echo "VALUE=\"B14\">$t[option10]</OPTION>";
	echo "<OPTION ";
	if ($suche['zeit'] == "B30")
		echo "SELECTED ";
	echo "VALUE=\"B30\">$t[option11]</OPTION>";
	echo "<OPTION ";
	if ($suche['zeit'] == "B90")
		echo "SELECTED ";
	echo "VALUE=\"B90\">$t[option12]</OPTION>";
	echo "<OPTION ";
	if ($suche['zeit'] == "B180")
		echo "SELECTED ";
	echo "VALUE=\"B180\">$t[option13]</OPTION>";
	echo "<OPTION ";
	if ($suche['zeit'] == "B365")
		echo "SELECTED ";
	echo "VALUE=\"B365\">$t[option14]</OPTION>";
	echo "</SELECT></TD></TR>\n";
	
	// Sucheinstellung Sortierung
	echo "<TR><TD class=\"tabelle_zeile1\"></TD><TD class=\"tabelle_zeile1\">" . $f1
		. "<SELECT NAME=\"suche[sort]\" SIZE=\"1\" STYLE=\"width: "
		. $select_breite . "px;\">";
	echo "<OPTION ";
	if (substr($suche['sort'], 0, 1) <> "S")
		echo "SELECTED ";
	echo "VALUE=\"DEFAULT\">$t[option15]</OPTION>";
	echo "<OPTION ";
	if ($suche['sort'] == "SZA")
		echo "SELECTED ";
	echo "VALUE=\"SZA\">$t[option16]</OPTION>";
	echo "<OPTION ";
	if ($suche['sort'] == "SBA")
		echo "SELECTED ";
	echo "VALUE=\"SBA\">$t[option17]</OPTION>";
	echo "<OPTION ";
	if ($suche['sort'] == "SBD")
		echo "SELECTED ";
	echo "VALUE=\"SBD\">$t[option18]</OPTION>";
	echo "<OPTION ";
	if ($suche['sort'] == "SAA")
		echo "SELECTED ";
	echo "VALUE=\"SAA\">$t[option19]</OPTION>";
	echo "<OPTION ";
	if ($suche['sort'] == "SAD")
		echo "SELECTED ";
	echo "VALUE=\"SAD\">$t[option20]</OPTION>";
	echo "</SELECT></TD></TR>\n";
	
	// nur von User
	echo "<TR><TD align=\"right\" class=\"tabelle_zeile1\">$t[suche4]</TD><TD class=\"tabelle_zeile1\">"
		. $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"suche[username]\" VALUE=\""
		. htmlspecialchars($suche['username'])
		. "\" SIZE=\"20\">" . $f2 . "</TD></TR>\n"
		. "<TR><TD COLSPAN=\"2\" align=\"center\" class=\"tabelle_zeile1\">"
		. $f1 . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$t[suche5]\">"
		. $f2 . "</TD></TR>\n" . "</TABLE></FORM>\n";
	
	?>
	<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
	<?php
}

function such_ergebnis() {
	global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase, $check_name, $u_id;
	global $farbe_tabelle_kopf2, $farbe_hervorhebung_forum, $farbe_link;
	global $suche, $o_js, $farbe_neuesposting_forum, $t, $u_level;
	
	$eingabe_breite = 50;
	$select_breite = 250;
	$maxpostingsprosuche = 1000;
	$titel = $t['ergebnis1'];
	
	$sql = "SELECT `u_gelesene_postings` FROM `user` WHERE `u_id`=" . intval($u_id);
	$query = mysqli_query($mysqli_link, $sql);
	if (mysqli_num_rows($query) > 0)
		$gelesene = mysqli_result($query, 0, "u_gelesene_postings");
	$u_gelesene = unserialize($gelesene);
	
	$fehler = "";
	if ($suche['username'] <> coreCheckName($suche['username'], $check_name))
		$fehler .= $t['fehler1'];
	$suche['username'] = coreCheckName($suche['username'], $check_name);
	unset($suche['u_id']);
	if (strlen($fehler) == 0 && $suche['username'] <> "") {
		$sql = "SELECT `u_id` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $suche['username']) . "'";
		$query = mysqli_query($mysqli_link, $sql);
		if (mysqli_num_rows($query) == 1) {
			$suche['u_id'] = mysqli_result($query, 0, "u_id");
		} else {
			$fehler .= 'Username unbekannt<br>';
		}
	}
	
	if (trim($suche['username']) == "" && trim($suche['text']) == ""
		&& (!($suche['zeit'] == "B1" || $suche['zeit'] == "B7"
			|| $suche['zeit'] == "B14")))
		$fehler .= $t['fehler2'];
	if ($suche['modus'] <> "A" && $suche['modus'] <> "O")
		$fehler .= $t['fehler3'];
	if ($suche['ort'] <> "V" && $suche['ort'] <> "B" && $suche['ort'] <> "T")
		$fehler .= $t['fehler4'];
	if (!$suche['thema'] == "ALL"
		&& !preg_match("/^B([0-9])+T([0-9])+$/i", $suche['thema'])
		&& !preg_match("/^B([0-9])+$/i", $suche['thema']))
		$fehler .= $t['fehler5'];
	
	if (strlen($fehler) > 0) {
		echo "<p><center><span style=\"color:$farbe_hervorhebung_forum; font-weight:bold;\">$fehler</span></center></p>";
	} else {
		$querytext = "";
		$querybetreff = "";
		if (trim($suche['text']) <> "") {
			$suche['text'] = htmlspecialchars($suche['text']);
			$suchetext = explode(" ", $suche['text']);
			
			for ($i = 0; $i < count($suchetext); $i++) {
				if (strlen($querytext) == 0) {
					$querytext = "po_text LIKE \"%" . mysqli_real_escape_string($mysqli_link, $suchetext[$i]) . "%\"";
				} else {
					if ($suche['modus'] == "O") {
						$querytext .= " OR po_text LIKE \"%" . mysqli_real_escape_string($mysqli_link, $suchetext[$i])
							. "%\"";
					} else {
						$querytext .= " AND po_text LIKE \"%" . mysqli_real_escape_string($mysqli_link, $suchetext[$i])
							. "%\"";
					}
				}
				
				if (strlen($querybetreff) == 0) {
					$querybetreff = "po_titel LIKE \"%" . mysqli_real_escape_string($mysqli_link, $suchetext[$i])
						. "%\"";
				} else {
					if ($suche['modus'] == "O") {
						$querybetreff .= " OR po_titel LIKE \"%"
							. mysqli_real_escape_string($mysqli_link, $suchetext[$i]) . "%\"";
					} else {
						$querybetreff .= " AND po_titel LIKE \"%"
							. mysqli_real_escape_string($mysqli_link, $suchetext[$i]) . "%\"";
					}
				}
				
			}
			$querytext = " (" . $querytext . ") ";
			$querybetreff = " (" . $querybetreff . ") ";
		}
		
		$sql = "SELECT posting.*, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_zeit, u_id, u_nick, u_level, u_punkte_gesamt, u_punkte_gruppe, u_chathomepage FROM posting left join user on po_u_id = u_id WHERE ";
		
		$abfrage = "";
		if ($suche['ort'] == "V" && $querybetreff <> "") {
			$abfrage = " (" . $querybetreff . " or " . $querytext . ") ";
		} else if ($suche['ort'] == "B" && $querybetreff <> "") {
			$abfrage = " " . $querybetreff . " ";
		} else if ($suche['ort'] == "T" && $querytext <> "") {
			$abfrage = " " . $querytext . " ";
		}
		
		if (isset($suche['u_id']) && $suche['u_id']) {
			if ($abfrage == "") {
				$abfrage = " (po_u_id = $suche[u_id]) ";
			} else {
				$abfrage .= " AND (po_u_id = $suche[u_id]) ";
			}
		}
		
		$boards = "";
		$sql2 = "SELECT fo_id, fo_admin, fo_name, th_id, th_name FROM forum left join thema on fo_id = th_fo_id "
			. "WHERE th_anzthreads <> 0 " . "ORDER BY fo_order, th_order ";
		$query2 = mysqli_query($mysqli_link, $sql2);
		while ($thema = mysqli_fetch_array($query2, MYSQLI_ASSOC)) {
			if (pruefe_leserechte($thema['th_id'])) {
				if ($suche['thema'] == "ALL") {
					if (strlen($boards) == 0) {
						$boards = "po_th_id = " . intval($thema[th_id]);
					} else {
						$boards .= " OR po_th_id = " . intval($thema[th_id]);
					}
				} else if (preg_match("/^B([0-9])+T([0-9])+$/i",
					$suche['thema'])) {
					$tempthema = substr($suche['thema'],
						-1 * strpos($suche['thema'], "T"), 4);
					if ($thema['th_id'] = $tempthema) {
						$boards = "po_th_id = $thema[th_id]";
					}
				} else if (preg_match("/^B([0-9])+$/i", $suche['thema'])) {
					$tempboard = substr($suche['thema'], 1, 4);
					if ($thema['fo_id'] == $tempboard) {
						if (strlen($boards) == 0) {
							$boards = "po_th_id = " . intval($thema[th_id]);
						} else {
							$boards .= " OR po_th_id = " . intval($thema[th_id]);
						}
					}
				}
			}
		}
		@mysqli_free_result($query2);
		
		if (strlen(trim($boards)) == 0)
			$boards = " 1 = 2 ";
		if (strlen(trim($abfrage)) == 0) {
			$abfrage .= " (" . $boards . ") ";
		} else {
			$abfrage .= " AND (" . $boards . ") ";
		}
		
		$sucheab = 0;
		if ($suche['zeit'] == "B1") {
			$sucheab = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
		} else if ($suche['zeit'] == "B7") {
			$sucheab = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
		} else if ($suche['zeit'] == "B14") {
			$sucheab = mktime(0, 0, 0, date("m"), date("d") - 14, date("Y"));
		} else if ($suche['zeit'] == "B30") {
			$sucheab = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
		} else if ($suche['zeit'] == "B90") {
			$sucheab = mktime(0, 0, 0, date("m") - 3, date("d"), date("Y"));
		} else if ($suche['zeit'] == "B180") {
			$sucheab = mktime(0, 0, 0, date("m") - 6, date("d"), date("Y"));
		} else if ($suche['zeit'] == "B365") {
			$sucheab = mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1);
		}
		
		if ($sucheab > 0) {
			$abfrage .= " AND (po_ts >= $sucheab) ";
		}
		
		if ($u_level <> "S" and $u_level <> "C")
			$abfrage .= " AND po_gesperrt = 'N' ";
		
		if ($suche['sort'] == "SZD") {
			$abfrage .= " ORDER BY po_ts DESC";
		} else if ($suche['sort'] == "SZA") {
			$abfrage .= " ORDER BY po_ts ASC";
		} else if ($suche['sort'] == "SBD") {
			$abfrage .= " ORDER BY po_titel DESC, po_ts DESC";
		} else if ($suche['sort'] == "SBA") {
			$abfrage .= " ORDER BY po_titel ASC, po_ts ASC";
		} else if ($suche['sort'] == "SAD") {
			$abfrage .= " ORDER BY u_nick DESC, po_ts DESC";
		} else if ($suche['sort'] == "SAA") {
			$abfrage .= " ORDER BY u_nick ASC, po_ts ASC";
		} else {
			$abfrage .= " ORDER BY po_ts DESC";
		}
		
		?>
		<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
		<?php
		echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
		echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><b>$titel</b></TD></TR>\n";
		
		flush();
		$sql = $sql . " " . $abfrage;
		$query = mysqli_query($mysqli_link, $sql);
		
		$anzahl = mysqli_num_rows($query);
		
		echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3>$f1<b>$t[ergebnis2] $anzahl</b>";
		if ($anzahl > $maxpostingsprosuche) {
			echo "<span style=\"color:#ff0000; font-weight: bold;\"> (Ausgabe wird auf $maxpostingsprosuche begrenzt.)</span>";
		}
		echo "$f2</TD></TR>\n";
		if ($anzahl > 0) {
			
			echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD>" . $f1
				. "<b>$t[ergebnis3]<br>$t[ergebnis4]</b>" . $f2 . "</TD>";
			echo "<TD>" . $f1 . "<b>$t[ergebnis6]</b>" . $f2 . "</TD>";
			echo "<TD>" . $f1 . "<b>$t[ergebnis7]</b>" . $f2 . "</TD>";
			echo "</tr>";
			
			$i = 0;
			while ($fund = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
				$i++;
				
				if ($i > $maxpostingsprosuche) {
					break;
				}
				
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
				}
				
				if (!@in_array($fund['po_id'], $u_gelesene[$fund['po_th_id']])) {
					$col = $farbe_neuesposting_forum;
				} else {
					$col = $farbe_link;
				}
				
				echo "<tr><td $bgcolor>" . show_pfad_posting2($fund['po_th_id']) . "<br>";
				$thread = vater_rekursiv($fund['po_id']);
				echo $f1
					. "<b><a href=\"#\" onClick=\"opener_reload('forum.php?id=$id&http_host=$http_host&th_id="
					. $fund['po_th_id'] . "&po_id=" . $fund['po_id']
					. "&thread=" . $thread
					. "&aktion=show_posting&seite=1',1); return(false);\">< span style=\"font-size: smaller; color:$col; \">"
					. $fund['po_titel'] . "</span></a>";
				if ($fund['po_gesperrt'] == 'Y')
					echo " <span style=\"color:#ff0000;\">(gesperrt)</span>";
				echo $f2 . "</b></td>";
				
				echo "<td $bgcolor>" . $f1 . $fund['po_zeit'] . $f2 . "</td>";
				
				if (!$fund['u_nick']) {
					echo "<td $bgcolor>$f3<b>Nobody</b>$f4</td>\n";
				} else {
					
					$userdata = array();
					$userdata['u_id'] = $fund['po_u_id'];
					$userdata['u_nick'] = $fund['u_nick'];
					$userdata['u_level'] = $fund['u_level'];
					$userdata['u_punkte_gesamt'] = $fund['u_punkte_gesamt'];
					$userdata['u_punkte_gruppe'] = $fund['u_punkte_gruppe'];
					$userdata['u_chathomepage'] = $fund['u_chathomepage'];
					$userlink = user($fund['po_u_id'], $userdata, $o_js, FALSE,
						"&nbsp;", "", "", TRUE, FALSE, 29);
					if ($fund['u_level'] == 'Z') {
						echo "<td $bgcolor>$f1 $userdata[u_nick] $f2</td>\n";
					} else {
						echo "<td $bgcolor>$f1 $userlink $f2</td>\n";
					}
				}
				
				echo "</tr>";
			}
			@mysqli_free_result($query);
			
		}
		echo "</table>\n";
		
		?>
		<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
		<?php
	}
}

$title = $body_titel . ' - Info';
zeige_header_anfang($title, $farbe_mini_background, $grafik_mini_background, $farbe_mini_link, $farbe_mini_vlink);
?>
<script>
	window.focus()
</script>
<script>
function neuesFenster(url) {
	hWnd=window.open(url,"<?php echo $fenster; ?>","resizable=yes,scrollbars=yes,width=300,height=580");
}
function neuesFenster2(url) {
	hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
}
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

if (strlen($u_id) > 0) {
	// Menü als erstes ausgeben
	$box = "$chat Menü";
	$text = "<a href=\"forum-suche.php?http_host=$http_host&id=$id\">$t[menue1]</A>\n";
	
	show_box2($box, $text);
	?>
	<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
	<?php
	
	// Auswahl
	switch ($aktion) {
		case "suche":
			such_bereich();
			flush();
			such_ergebnis();
			break;
		default;
			such_bereich();
	}
} else {
	echo "<p style=\"text-align:center;\">$t[sonst2]</p>\n";
}

if ($o_js) {
	echo $f1
		. "<p style=\"text-align:center;\">[<a href=\"javascript:window.close();\">$t[sonst1]</a>]</p>"
		. $f2 . "\n";
}

?>
</body>
</html>