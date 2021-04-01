<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js
id_lese($id);

// Fenstername
$fenster = str_replace("+", "", $u_nick);
$fenster = str_replace("-", "", $fenster);
$fenster = str_replace("ä", "", $fenster);
$fenster = str_replace("ö", "", $fenster);
$fenster = str_replace("ü", "", $fenster);
$fenster = str_replace("Ä", "", $fenster);
$fenster = str_replace("Ö", "", $fenster);
$fenster = str_replace("Ü", "", $fenster);
$fenster = str_replace("ß", "", $fenster);

$title = $body_titel . ' - Blacklist';
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
	function neuesFenster(url,name) {
			hWnd=window.open(url,name,"resizable=yes,scrollbars=yes,width=300,height=580");
	}
	function neuesFenster2(url) {
			hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
	}
	function toggle(tostat ) {
			for(i=0; i<document.forms["blacklist_loeschen"].elements.length; i++) {
				 e = document.forms["blacklist_loeschen"].elements[i];
				 if ( e.type=='checkbox' )
					 e.checked=tostat;
			}
	}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$eingabe_breite = 45;

if ($admin && $u_id && $communityfeatures) {
	
	// Menü als erstes ausgeben
	$box = $ft0 . "Menü Blacklist" . $ft1;
	$text = "<A HREF=\"blacklist.php?http_host=$http_host&id=$id&aktion=\">Blacklist zeigen</A>\n"
		. "| <A HREF=\"blacklist.php?http_host=$http_host&id=$id&aktion=neu\">Neuen Eintrag hinzufügen</A>\n"
		. "| <A HREF=\"sperre.php?http_host=$http_host&id=$id\">Zugangssperren</A>\n";
	
	show_box2($box, $text);
	?>
	<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
	<?php
	
	if (!isset($neuer_blacklist))
		$neuer_blacklist[] = "";
	if (!isset($sort))
		$sort = "";
	
	switch ($aktion) {
		
		case "neu":
		// Formular für neuen Eintrag ausgeben
			formular_neuer_blacklist($neuer_blacklist);
			break;
		
		case "neu2":
		// Neuer Eintrag, 2. Schritt: Nick Prüfen
			$neuer_blacklist['u_nick'] = mysqli_real_escape_string($mysqli_link, $neuer_blacklist['u_nick']); // sec
			$query = "SELECT `u_id` FROM `user` WHERE `u_nick` = '$neuer_blacklist[u_nick]'";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				$neuer_blacklist['u_id'] = mysqli_result($result, 0, 0);
				neuer_blacklist($u_id, $neuer_blacklist);
				unset($neuer_blacklist);
				$neuer_blacklist[] = "";
				formular_neuer_blacklist($neuer_blacklist);
				zeige_blacklist("normal", "", $sort);
			} elseif ($neuer_blacklist['u_nick'] == "") {
				echo "<b>Fehler:</b> Bitte geben Sie einen Nicknamen an!<br>\n";
				formular_neuer_blacklist($neuer_blacklist);
			} else {
				echo "<b>Fehler:</b> Der Nickname '$neuer_blacklist[u_nick]' existiert nicht!<br>\n";
				formular_neuer_blacklist($neuer_blacklist);
			}
			@mysqli_free_result($result);
			break;
		
		case "loesche":
		// Eintrag löschen
		
			if (isset($f_blacklistid) && is_array($f_blacklistid)) {
				// Mehrere Einträge löschen
				foreach ($f_blacklistid as $key => $loesche_id) {
					loesche_blacklist($loesche_id);
				}
				
			} else {
				// Einen Eintrag löschen
				if (isset($f_blacklistid))
					loesche_blacklist($f_blacklistid);
			}
			
			formular_neuer_blacklist($neuer_blacklist);
			zeige_blacklist("normal", "", $sort);
			break;
		
		default:
			formular_neuer_blacklist($neuer_blacklist);
			zeige_blacklist("normal", "", $sort);
	}
	
} else {
	echo "<P ALIGN=CENTER><b>Fehler:</b> Sie sind ausgelogt oder haben keine Berechtigung!</P>\n";
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