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
	if (!isset($u_farbe))
		$u_farbe = $user_farbe;
	
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
zeige_header_anfang($title, $farbe_chat_background2, $grafik_background2, $farbe_chat_link2, $farbe_chat_vlink2);
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
	echo "<FORM NAME=\"form\" METHOD=POST target=\"schreibe\" ACTION=\"schreibe.php\" onSubmit=\"resetinput(); return false;\">";
	
	echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR>"
		. "<TD>&nbsp;</td><TD COLSPAN=" . (count($farbe_chat_user) + 3) . ">";
	
	// Typ Eingabefeld für Chateingabe setzen
	if ($u_level == "M") {
		$text2_typ = "<TEXTAREA ROWS=\"4\" NAME=\"text2\" COLS=\""
			. $chat_eingabe_breite . "\"></TEXTAREA>";
	} else {
		$text2_typ = "<INPUT TYPE=\"TEXT\" NAME=\"text2\" maxlength=\""
			. ($chat_max_eingabe - 1) . "\" VALUE=\"\" SIZE=\""
			. $chat_eingabe_breite . "\""
			. (isset($eingabe_inaktiv_autocomplete) && $eingabe_inaktiv_autocomplete == "1" ? "autocomplete=\"off\"" : "")
			. ">";
	}
	
	// Unterscheidung Normal oder sicherer Modus
	if ($backup_chat || $u_backup) {
		
		$chat_eingabe_breite = $chat_eingabe_breite - 11;
		$mindestbreite = 44;
		
		// Bei zu schmaler Eingabenzeilen diese für rundes Layout auf Mindesbreite setzen
		if ($chat_eingabe_breite < $mindestbreite) {
			$chat_eingabe_breite = $mindestbreite;
		}
		
		echo $text2_typ . "<INPUT NAME=\"text\" VALUE=\"\" TYPE=\"HIDDEN\">"
			. "<SELECT NAME=\"user_chat_back\">\n";
		for ($i = 5; $i < 40; $i++) {
			echo "<OPTION " . ($chat_back == $i ? "SELECTED" : "")
				. " VALUE=\"$i\">$i&nbsp;$t[eingabe1]\n";
		}
		echo "</SELECT>"
			. "<INPUT NAME=\"http_host\" VALUE=\"$http_host\" TYPE=\"HIDDEN\">"
			. "<INPUT NAME=\"id\" VALUE=\"$id\" TYPE=\"HIDDEN\">"
			. "<INPUT NAME=\"u_backup\" VALUE=\"$u_backup\" TYPE=\"HIDDEN\">"
			. "<INPUT NAME=\"u_level\" VALUE=\"$u_level\" TYPE=\"HIDDEN\">"
			. $f1 . "<INPUT TYPE=\"SUBMIT\" VALUE=\"Go!\">" . $f2;
		echo "</td></TR>\n";
	} else {
		
		$mindestbreite = 55;
		
		// Bei zu schmaler Eingabenzeilen diese für rundes Layout auf Mindesbreite setzen
		if ($chat_eingabe_breite < $mindestbreite)
			$chat_eingabe_breite = $mindestbreite;
		
		echo $text2_typ . "<INPUT NAME=\"text\" VALUE=\"\" TYPE=\"HIDDEN\">"
			. "<INPUT NAME=\"id\" VALUE=\"$id\" TYPE=\"HIDDEN\">"
			. "<INPUT NAME=\"http_host\" VALUE=\"$http_host\" TYPE=\"HIDDEN\">"
			. "<INPUT NAME=\"u_backup\" VALUE=\"$u_backup\" TYPE=\"HIDDEN\">"
			. "<INPUT NAME=\"u_level\" VALUE=\"$u_level\" TYPE=\"HIDDEN\">"
			. $f1 . "<INPUT TYPE=\"SUBMIT\" VALUE=\"Go!\">" . $f2;
		echo "</td></TR>\n";
	}
	
	$mlnk[4] = "hilfe.php?http_host=$http_host&id=$id";
	$mlnk[1] = "raum.php?http_host=$http_host&id=$id";
	$mlnk[2] = "user.php?http_host=$http_host&id=$id";
	$mlnk[3] = "edit.php?http_host=$http_host&id=$id";
	$mlnk[5] = "sperre.php?http_host=$http_host&id=$id";
	$mlnk[6] = "index.php?http_host=$http_host&id=$id&aktion=logoff";
	$mlnk[7] = "log.php?http_host=$http_host&id=$id&back=500";
	$mlnk[8] = "moderator.php?http_host=$http_host&id=$id&mode=answer";
	$mlnk[9] = "hilfe.php?http_host=$http_host&id=$id&reset=1";
	$mlnk[10] = "mail.php?http_host=$http_host&id=$id";
	$mlnk[11] = "index-forum.php?http_host=$http_host&id=$id";
	$mlnk[12] = "blacklist.php?http_host=$http_host&id=$id";
	
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
	echo "<TR><TD></td><TD><b>" . $f1;
	echo "[<a href=\"$mlnk[4]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[4]');return(false)\">$t[menue4]</a>]&nbsp;";
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		echo "[<a href=\"$mlnk[1]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[1]');return(false)\">$t[menue1]</a>]&nbsp;"
			. "[<a href=\"$mlnk[2]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[2]');return(false)\">$t[menue2]</a>]&nbsp;";
	}
	if ($communityfeatures && $u_level != 'G' && (!isset($beichtstuhl) || !$beichtstuhl || $admin)) {
		echo "[<a href=\"$mlnk[10]\" target=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[10]');return(false)\">$t[menue10]</a>]&nbsp;";
	}
	if ($forumfeatures && $communityfeatures && (!isset($beichtstuhl) || !$beichtstuhl || $admin) && ((isset($aktraum) && $aktraum->r_status1 <> "L") || $admin)) {
		echo "[<a href=\"$mlnk[11]\" onMouseOver=\"return(true)\" target=\"topframe\">$t[menue11]</a>]&nbsp;";
	}
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		echo "[<a href=\"$mlnk[3]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[3]');return(false)\">$t[menue3]</a>]&nbsp;";
	}
	if ($admin) {
		echo "[<a href=\"$mlnk[5]\" target=\"640_$fenster\" onClick=\"neuesFenster2('$mlnk[5]');return(false)\">$t[menue5]</a>]&nbsp;";
	}
	if ($admin && $communityfeatures) {
		echo "[<a href=\"$mlnk[12]\" target=\"640_$fenster\" onClick=\"neuesFenster2('$mlnk[12]');return(false)\">$t[menue12]</a>]&nbsp;";
	}
	if ($u_level == "M") {
		echo "[<a href=\"$mlnk[8]\" target=\"$fenster\" onClick=\"neuesFenster2('$mlnk[8]');return(false)\">$t[menue8]</a>]&nbsp;";
	}
	echo "[<a href=\"$mlnk[7]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster3('$mlnk[7]');return(false)\">$t[menue7]</a>]&nbsp;";
	if ($o_js) {
		echo "[<a href=\"$mlnk[9]\" target=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster3('$mlnk[9]');return(false)\">$t[menue9]</a>]&nbsp;";
	}
	echo "&nbsp;&nbsp;";
	if (isset($chat_logout_url)) {
		echo "[<a href=\"$mlnk[6]\" onMouseOver=\"return(true)\" target=\"_top\">$t[menue6]</a>]&nbsp;"
			. $f2 . "</b></td>";
	} else {
		echo "[<a href=\"$mlnk[6]\" onMouseOver=\"return(true)\" target=\"topframe\">$t[menue6]</a>]&nbsp;"
			. $f2 . "</b></td>";
	}
	
	unset($aktraum);
	
	echo "<td style=\"text-align: right;\"><span style=\"font-size: smaller; color:#" . $u_farbe . ";\"><b>$t[farbe2]</b>&nbsp;</span></td>";
	for (@reset($farbe_chat_user); list($nummer, $ufarbe) = each(
		$farbe_chat_user);) {
		?>
		<td style="background-color:#<?php echo $ufarbe; ?>">
			<a onMouseOver="return(true)" href="eingabe.php?http_host=<?php echo $http_host; ?>&id=<?php echo $id; ?>&farbe=<?php echo $ufarbe; ?>">
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
	echo "<BODY onLoad='parent.location.href=\"index.php?http_host=$http_host\"'>\n";
}

?>
</body>
</html>