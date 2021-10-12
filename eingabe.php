<?php

// eingabe.php muss mit id=$hash_id aufgerufen werden

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js
id_lese($id);

if ($u_id) {
	
	// Ggf Farbe aktualisieren
	if (isset($farbe)) {
		if (strlen($farbe) == 6) {
			if (preg_match("/[a-f0-9]{6}/i", $farbe)) {
				$u_farbe = $farbe;
				$f['u_farbe'] = substr(htmlspecialchars($farbe), 0, 6);
				schreibe_db("user", $f, $u_id, "u_id");
				system_msg("", 0, $u_id, $system_farbe,
					"<b>$chat:</b> " . "<span style=\"color:#$u_farbe;\">"
						. str_replace("%u_nick%", $u_nick, $t['farbe1'])
						. "</span>");
			}
		}
	}
	
	// Default für Farbe setzen, falls undefiniert
	if (!isset($u_farbe)) {
		$u_farbe = $user_farbe;
	}
	
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

$title = $body_titel;
zeige_header_anfang($title, 'chateingabe');
?>
<script>
function resetinput() {
	document.forms['form'].elements['text'].value=document.forms['form'].elements['text2'].value;
<?php
	if ($u_clearedit == 1) {
		echo "	document.forms['form'].elements['text2'].value='';\n";
	}
?>
	document.forms['form'].submit();
	document.forms['form'].elements['text2'].focus();
	document.forms['form'].elements['text2'].select();
}
function neuesFenster(url) { 
	hWnd=window.open(url,"<?php echo $fenster; ?>","resizable=yes,scrollbars=yes,width=320,height=580"); 
}
function neuesFenster2(url) { 
	hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580"); 
}
function neuesFenster3(url) { 
	hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,menu=yes,width=780,height=580"); 
}
function window_reload(file,win_name) {
	win_name.location.href=file;
}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
	// Eingabeformular mit Menu und Farbauswahl
	reset($farbe_chat_user);
	$i = 0;
	echo "<form name=\"form\" method=\"post\" target=\"schreibe\" action=\"schreibe.php\" onSubmit=\"resetinput(); return false;\">";
	
	echo "<table style=\"border-collapse:collapse;\"><tr>"
		. "<td>&nbsp;</td><td colspan=" . (count($farbe_chat_user) + 3) . ">";
	
	// Typ Eingabefeld für Chateingabe setzen
	if ($u_level == "M") {
		$text2_typ = "<textarea rows=\"4\" name=\"text2\" autocomplete=\"off\" cols=\""
			. $chat_eingabe_breite . "\"></textarea>";
	} else {
		$text2_typ = "<input type=\"text\" name=\"text2\" autocomplete=\"off\" maxlength=\""
			. ($chat_max_eingabe - 1) . "\" value=\"\" size=\""
			. $chat_eingabe_breite . "\""
			. (isset($eingabe_inaktiv_autocomplete) && $eingabe_inaktiv_autocomplete == "1" ? "autocomplete=\"off\"" : "")
			. ">";
	}
	
	$chat_eingabe_breite = $chat_eingabe_breite - 11;
	$mindestbreite = 44;
	
	// Bei zu schmaler Eingabenzeilen diese für rundes Layout auf Mindesbreite setzen
	if ($chat_eingabe_breite < $mindestbreite) {
		$chat_eingabe_breite = $mindestbreite;
	}
	echo $text2_typ . "<input name=\"text\" value=\"\" type=\"hidden\">"
		. "<select name=\"user_chat_back\">\n";
	for ($i = 5; $i < 40; $i++) {
		echo "<option " . ($chat_back == $i ? "selected" : "")
			. " value=\"$i\">$i&nbsp;$t[eingabe1]\n";
	}
	echo "</select>"
		. "<input name=\"id\" value=\"$id\" type=\"hidden\">"
		. "<input name=\"u_level\" value=\"$u_level\" type=\"hidden\">"
		. $f1 . "<input type=\"submit\" value=\"Go!\">" . $f2;
	echo "</td></tr>\n";
	
	$mlnk[4] = "hilfe.php?id=$id";
	$mlnk[1] = "raum.php?id=$id";
	$mlnk[2] = "user.php?id=$id";
	$mlnk[3] = "edit.php?id=$id";
	$mlnk[5] = "sperre.php?id=$id";
	$mlnk[6] = "index.php?id=$id&aktion=logoff";
	$mlnk[7] = "log.php?id=$id&back=500";
	$mlnk[8] = "moderator.php?id=$id&mode=answer";
	$mlnk[9] = "hilfe.php?id=$id&reset=1";
	$mlnk[10] = "mail.php?id=$id";
	$mlnk[11] = "index-forum.php?id=$id";
	$mlnk[12] = "blacklist.php?id=$id";
	
	if ($forumfeatures && $communityfeatures) {
		// Raumstatus lesen, für Temporär, damit FORUM nicht angezeigt wird
		
		$query = "SELECT r_status1 from raum WHERE r_id=" . intval($o_raum);
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$aktraum = mysqli_fetch_object($result);
			mysqli_free_result($result);
		}
	}
	
	// Code funktioniert mit und ohne javascript
	//echo "<tr><td></td><td><b>" . $f1;
	echo "<tr><td></td><td><b>";
	//echo "[<a href=\"$mlnk[4]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[4]');return(false)\">$t[menue4]</a>]&nbsp;";
	?>
	<a href="<?php echo $mlnk[4]; ?>" onMouseOver="return(true)" onClick="neuesFenster2('<?php echo $mlnk[4]; ?>');return(false)" class="button" title="<?php echo $t['menue4']; ?>"><span class="fa fa-question"></span> <span><?php echo $t['menue4']; ?></span></a>&nbsp;
	<?php
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		//echo "[<a href=\"$mlnk[1]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[1]');return(false)\">$t[menue1]</a>]&nbsp;"
		//	. "[<a href=\"$mlnk[2]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[2]');return(false)\">$t[menue2]</a>]&nbsp;";
		?>
		<a href="<?php echo $mlnk[1]; ?>" target="640_<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster2('<?php echo $mlnk[1]; ?>');return(false)" class="button" title="<?php echo $t['menue1']; ?>"><span class="fa fa-road"></span> <span><?php echo $t['menue1']; ?></span></a>&nbsp;
		<a href="<?php echo $mlnk[2]; ?>" target="<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[2]; ?>');return(false)" class="button" title="<?php echo $t['menue2']; ?>"><span class="fa fa-user"></span> <span><?php echo $t['menue2']; ?></span></a>&nbsp;
		<?php
	}
	if ($communityfeatures && $u_level != 'G' && (!isset($beichtstuhl) || !$beichtstuhl || $admin)) {
		//echo "[<a href=\"$mlnk[10]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[10]');return(false)\">$t[menue10]</a>]&nbsp;";
		?>
		<a href="<?php echo $mlnk[6]; ?>" target="640_<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster2('<?php echo $mlnk[10]; ?>');return(false)" class="button" title="<?php echo $t['menue10']; ?>"><span class="fa fa-envelope"></span> <span><?php echo $t['menue10']; ?></span></a>&nbsp;
		<?php
	}
	if ($forumfeatures && $communityfeatures && (!isset($beichtstuhl) || !$beichtstuhl || $admin) && ((isset($aktraum) && $aktraum->r_status1 <> "L") || $admin)) {
		//echo "[<a href=\"$mlnk[11]\" onMouseOver=\"return(true)\" target=\"_top\">$t[menue11]</a>]&nbsp;";
		?>
		<a href="<?php echo $mlnk[11]; ?>" onMouseOver="return(true)" target="_top" class="button" title="<?php echo $t['menue11']; ?>"><span class="fa fa-commenting"></span> <span><?php echo $t['menue11']; ?></span></a>&nbsp;
		<?php
	}
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		//echo "[<a href=\"$mlnk[3]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[3]');return(false)\">$t[menue3]</a>]&nbsp;";
		?>
		<a href="<?php echo $mlnk[3]; ?>" target="<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[3]; ?>');return(false)" class="button" title="<?php echo $t['menue3']; ?>"><span class="fa fa-cog"></span> <span><?php echo $t['menue3']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin) {
		//echo "[<a href=\"$mlnk[5]\" target=\"640_$fenster\" onClick=\"neuesFenster2('$mlnk[5]');return(false)\">$t[menue5]</a>]&nbsp;";
		?>
		<a href="<?php echo $mlnk[5]; ?>" target="640_<?php echo $fenster; ?>" onClick="neuesFenster2('<?php echo $mlnk[5]; ?>');return(false)" class="button" title="<?php echo $t['menue5']; ?>"><span class="fa fa-lock"></span> <span><?php echo $t['menue5']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin && $communityfeatures) {
		//echo "[<a href=\"$mlnk[12]\" target=\"640_$fenster\" onClick=\"neuesFenster2('$mlnk[12]');return(false)\">$t[menue12]</a>]&nbsp;";
		?>
		<a href="<?php echo $mlnk[12]; ?>" target="640_<?php echo $fenster; ?>" onClick="neuesFenster2('<?php echo $mlnk[12]; ?>');return(false)" class="button" title="<?php echo $t['menue12']; ?>"><span class="fa fa-list"></span> <span><?php echo $t['menue12']; ?></span></a>&nbsp;
		<?php
	}
	if ($u_level == "M") {
		//echo "[<a href=\"$mlnk[8]\" target=\"$fenster\" onClick=\"neuesFenster2('$mlnk[8]');return(false)\">$t[menue8]</a>]&nbsp;";
		?>
		<a href="<?php echo $mlnk[8]; ?>" target="<?php echo $fenster; ?>" onClick="neuesFenster2('<?php echo $mlnk[8]; ?>');return(false)" class="button" title="<?php echo $t['menue8']; ?>"><span class="fa fa-reply"></span> <span><?php echo $t['menue8']; ?></span></a>&nbsp;
		<?php
	}
	//echo "[<a href=\"$mlnk[7]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster3('$mlnk[7]');return(false)\">$t[menue7]</a>]&nbsp;";
	?>
	<a href="<?php echo $mlnk[7]; ?>" target="<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster3('<?php echo $mlnk[7]; ?>');return(false)" class="button" title="<?php echo $t['menue7']; ?>"><span class="fa fa-archive"></span> <span><?php echo $t['menue7']; ?></span></a>&nbsp;
	<?php
	if ($o_js) {
		//echo "[<a href=\"$mlnk[9]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster3('$mlnk[9]');return(false)\">$t[menue9]</a>]&nbsp;";
		?>
		<a href="<?php echo $mlnk[9]; ?>" target="<?php echo $fenster; ?>" onMouseOver="return(true)" onClick="neuesFenster3('<?php echo $mlnk[9]; ?>');return(false)" class="button" title="<?php echo $t['menue9']; ?>"><span class="fa fa-refresh"></span> <span><?php echo $t['menue9']; ?></span></a>
		<?php
	}
	echo "&nbsp;&nbsp;";
	//echo "[<a href=\"$mlnk[6]\" onMouseOver=\"return(true)\" target=\"_top\">$t[menue6]</a>]&nbsp;" . $f2 . "</b></td>";
	?>
	<a href="<?php echo $mlnk[6]; ?>" onMouseOver="return(true)" target="_top" class="button" title="<?php echo $t['menue6']; ?>"><span class="fa fa-sign-out"></span> <span><?php echo $t['menue6']; ?></span></a>&nbsp;
	<?php
	
	unset($aktraum);
	
	echo "<td style=\"text-align: right;\"><span style=\"font-size: smaller; color:#" . $u_farbe . ";\"><b>$t[farbe2]</b>&nbsp;</span></td>";
	for (@reset($farbe_chat_user); list($nummer, $ufarbe) = each($farbe_chat_user);) {
		?>
		<td style="background-color:#<?php echo $ufarbe; ?>">
			<a onMouseOver="return(true)" href="eingabe.php?id=<?php echo $id; ?>&farbe=<?php echo $ufarbe; ?>">
				<img src="pics/fuell.gif" style="width:<?php echo $farbe_chat_user_groesse; ?>px; height:<?php echo $farbe_chat_user_groesse; ?>px; border:0px;" alt="">
			</a>
		</td>
		<?php
	}
	?>
	<td>
		<img src="pics/fuell.gif" style="width:<?php echo $farbe_chat_user_groesse; ?>px; height:<?php echo $farbe_chat_user_groesse; ?>px; border:0px;" alt="">
	</td>
	</tr>
	</table>
	</form>
	<?php
} else {
	echo "<vody onLoad='parent.location.href=\"index.php\"'>\n";
}

?>
</body>
</html>