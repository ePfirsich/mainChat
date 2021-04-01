<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$fenster = str_replace("+", "", $u_nick);
$fenster = str_replace("-", "", $fenster);
$fenster = str_replace("ä", "", $fenster);
$fenster = str_replace("ö", "", $fenster);
$fenster = str_replace("ü", "", $fenster);
$fenster = str_replace("Ä", "", $fenster);
$fenster = str_replace("Ö", "", $fenster);
$fenster = str_replace("Ü", "", $fenster);
$fenster = str_replace("ß", "", $fenster);

$title = $body_titel . ' - Top 10';
zeige_header_anfang($title, $farbe_mini_background, $grafik_mini_background, $farbe_mini_link, $farbe_mini_vlink);
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
<script>
function neuesFenster(url) { 
		hWnd=window.open(url,"<?php echo $fenster; ?>","resizable=yes,scrollbars=yes,width=300,height=580"); 
}
function neuesFenster2(url) { 
		hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580"); 
}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Menue ausgeben

// Menü als erstes ausgeben
$box = $ft0 . "Menü Top10" . $ft1;
$text = "<A HREF=\"top10.php?http_host=$http_host&id=$id&aktion=top10\">Top 10</A>\n";
$text .= "| <A HREF=\"top10.php?http_host=$http_host&id=$id&aktion=top100\">Top 100</A>\n";
$text .= "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#punkte\">Hilfe</A>\n";
show_box2($box, $text);
?>
<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
<?php

if ($erweitertefeatures) {
	switch ($aktion) {
		case "top100":
			$anzahl = 100;
			break;
		case "top1000":
			$anzahl = 1000;
			break;
		case "top10":
		default:
			$anzahl = 10;
	}
	
	$bgcolor = $farbe_tabelle_zeile1;
	echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=2 CELLSPACING=0>\n"
		. "<TR BGCOLOR=\"$farbe_tabelle_kopf2\" align=\"left\"><TD WIDTH=\"4%\">&nbsp;</TD>"
		. "<TD WIDTH=\"32%\"colspan=2><DIV style=\"color:$farbe_text; font-weight:bold;\">"
		. "&nbsp;&nbsp;&nbsp;&nbsp;Punkte " . strftime("%B", time())
		. "</DIV></TD>"
		. "<TD WIDTH=\"32%\" colspan=2><DIV style=\"color:$farbe_text; font-weight:bold;\">"
		. "&nbsp;&nbsp;&nbsp;&nbsp;Punkte " . strftime("%Y", time())
		. "</DIV></TD>"
		. "<TD WIDTH=\"32%\" colspan=2><DIV style=\"color:$farbe_text; font-weight:bold;\">"
		. "&nbsp;&nbsp;&nbsp;&nbsp;Gesamtpunkte</DIV></TD></TR></TABLE>\n";
	
	// im Cache nachsehen, ob aktuelle Daten vorhanden sind (nicht älter als 6 Stunden)
	$query = "select * from top10cache where t_eintrag=1 "
		. "AND date_add(t_zeit, interval '6' hour)>=NOW()";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) > 0) {
		$t_id = mysqli_result($result, 0, "t_id");
		$array_user = unserialize(mysqli_result($result, 0, "t_daten"));
	} else {
		unset($array_user);
		// Top 100 Punkte im aktuellen Monat als Array aufbauen
		$query = "select u_punkte_monat as punkte,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage from user "
			. "where u_punkte_monat!=0 " . "and u_punkte_datum_monat="
			. date("n", time()) . " and u_punkte_datum_jahr="
			. date("Y", time()) . " and u_level != 'Z' "
			. "order by u_punkte_monat desc,u_punkte_gesamt desc,u_punkte_jahr desc limit 0,100";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			$array_anzahl[0] = mysqli_num_rows($result);
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$array_user[0][] = $row;
			}
		}
		@mysqli_free_result($result);
		
		// Top 100 Punkte im aktuellen Jahr als Array aufbauen
		$query = "select u_punkte_jahr as punkte,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage from user "
			. "where u_punkte_jahr!=0 " . "and u_punkte_datum_jahr="
			. date("Y", time()) . "  and u_level != 'Z' "
			. "order by u_punkte_jahr desc,u_punkte_gesamt desc,u_punkte_monat desc limit 0,100";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			$array_anzahl[1] = mysqli_num_rows($result);
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$array_user[1][] = $row;
			}
		}
		@mysqli_free_result($result);
		
		// Top 100 Gesamtpunkte als Array aufbauen
		$query = "select u_punkte_gesamt as punkte,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage from user "
			. "where u_punkte_gesamt!=0  and u_level != 'Z' "
			. "order by u_punkte_gesamt desc,u_punkte_monat desc,u_punkte_jahr desc limit 0,100";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			$array_anzahl[2] = mysqli_num_rows($result);
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$array_user[2][] = $row;
			}
		}
		@mysqli_free_result($result);
		
		// Daten in Cache schreiben und alle anderen Einträge löschen
		unset($f);
		$f['t_eintrag'] = 1;
		$f['t_daten'] = isset($array_user) ? serialize($array_user) : null;
		$t_id = schreibe_db("top10cache", $f, 0, "t_id");
		$query = "DELETE FROM top10cache WHERE t_eintrag=1 AND t_id!='$t_id'";
		$result = mysqli_query($mysqli_link, $query);
	}
	
	// Array als Tabelle ausgeben
	if (is_array($array_user)) {
		for ($i = 0; $i < $anzahl; $i++) {
			echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=2 CELLSPACING=0>\n"
				. "<TR BGCOLOR=\"$bgcolor\"><TD WIDTH=\"4%\" align=\"right\"><b>"
				. $f1 . ($i + 1) . $f2 . "</b></TD>";
			for ($j = 0; $j < 3; $j++) {
				if (isset($array_user[$j]) && isset($array_user[$j][$i])
					&& $array_user[$j][$i]['punkte']) {
					$array_user[$j][$i]['u_punkte_anzeigen'] = 'Y';
					echo "<TD WIDTH=\"8%\" align=\"right\">" . $f1
						. $array_user[$j][$i]['punkte'] . $f2
						. "</TD><TD  WIDTH=\"24%\">" . $f1
						. user($array_user[$j][$i]['u_id'],
							$array_user[$j][$i], TRUE) . $f2 . "</TD>\n";
				} else {
					echo "<TD WIDTH=\"32%\" colspan=2>" . $f1 . "&nbsp;" . $f2
						. "</TD>\n";
				}
			}
			echo "</TR></TABLE>\n";
			flush();
			if (($i % 2) > 0) {
				$bgcolor = $farbe_tabelle_zeile1;
			} else {
				$bgcolor = $farbe_tabelle_zeile2;
			}
			
		}
	}
	echo "<br>\n";
}

if ($o_js || !$u_id) {
	?>
	<div style="text-align:center;">[<a href="javascript:window.close();"><?php echo $t['sonst1']; ?></a>]</div>
	<br>
	<?php
}

?>
</body>
</html>