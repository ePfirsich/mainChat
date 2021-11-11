<?php

// eingabe.php muss mit id=$hash_id aufgerufen werden

require_once("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, o_js
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
		$text2_typ = "<textarea rows=\"3\" name=\"text2\" autofocus autocomplete=\"off\" cols=\""
			. $chat_eingabe_breite . "\"></textarea>";
	} else {
		$text2_typ = "<input type=\"text\" name=\"text2\" autofocus autocomplete=\"off\" maxlength=\""
			. ($chat_max_eingabe - 1) . "\" value=\"\" size=\""
			. $chat_eingabe_breite . "\">";
	}
	
	$chat_eingabe_breite = $chat_eingabe_breite - 11;
	$mindestbreite = 44;
	
	// Bei zu schmaler Eingabenzeilen diese für rundes Layout auf Mindesbreite setzen
	if ($chat_eingabe_breite < $mindestbreite) {
		$chat_eingabe_breite = $mindestbreite;
	}
	
	// Unterscheidung Normal oder sicherer Modus
	if ($sicherer_modus == 1 || $u_sicherer_modus == "Y") {
		echo $text2_typ . "<input name=\"text\" value=\"\" type=\"hidden\">"
			. "<select name=\"user_chat_back\">\n";
		for ($i = 5; $i < 40; $i++) {
			echo "<option " . ($chat_back == $i ? "selected" : "") . " value=\"$i\">$i&nbsp;$t[eingabe1]\n";
		}
		echo "</select>"
			. "<input name=\"id\" value=\"$id\" type=\"hidden\">"
			. "<input name=\"u_level\" value=\"$u_level\" type=\"hidden\">"
			. $f1 . "<input type=\"submit\" value=\"Go!\">" . $f2;
	} else {
		echo $text2_typ . "<input name=\"text\" value=\"\" type=\"hidden\">"
			. "<input name=\"id\" value=\"$id\" type=\"hidden\">"
			. "<input name=\"u_level\" value=\"$u_level\" type=\"hidden\">"
			. $f1 . "<input type=\"submit\" value=\"Go!\">" . $f2;
	}
	
	echo "</td></tr>\n";
	

	// Anzahl der ungelesenen Nachrichten ermitteln
	$query_nachrichten = "SELECT mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') AS zeit,u_nick FROM mail LEFT JOIN user ON m_von_uid=u_id WHERE m_an_uid=$u_id  AND m_status='neu' ORDER BY m_zeit desc";
	$result_nachrichten = mysqli_query($mysqli_link, $query_nachrichten);
	
	if ($result_nachrichten && mysqli_num_rows($result_nachrichten) > 0) {
		$neue_nachrichten = " <span class=\"nachrichten_neu\">(".mysqli_num_rows($result_nachrichten).")</span>";
	} else {
		$neue_nachrichten = '';
	}
	
	$mlnk[4] = "index.php?id=$id&aktion=hilfe";
	$mlnk[1] = "raum.php?id=$id";
	$mlnk[2] = "user.php?id=$id";
	$mlnk[3] = "edit.php?id=$id";
	$mlnk[5] = "sperre.php?id=$id";
	$mlnk[6] = "index.php?id=$id&aktion=logoff";
	$mlnk[7] = "log.php?id=$id&back=500";
	$mlnk[8] = "moderator.php?id=$id&mode=answer";
	$mlnk[9] = "logout.php?id=$id&reset=1";
	$mlnk[10] = "mail.php?id=$id";
	$mlnk[11] = "index-forum.php?id=$id";
	$mlnk[12] = "blacklist.php?id=$id";
	$mlnk[13] = "user.php?id=$id&aktion=statistik";
	
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
	echo "<tr><td></td><td><b>";
	?>
	<a href="<?php echo $mlnk[4]; ?>" target="_blank" class="button" title="<?php echo $t['menue4']; ?>"><span class="fa fa-question icon16"></span> <span><?php echo $t['menue4']; ?></span></a>&nbsp;
	<?php
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		?>
		<a href="<?php echo $mlnk[1]; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[1]; ?>');return(false)" class="button" title="<?php echo $t['menue1']; ?>"><span class="fa fa-road icon16"></span> <span><?php echo $t['menue1']; ?></span></a>&nbsp;
		<a href="<?php echo $mlnk[2]; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[2]; ?>');return(false)" class="button" title="<?php echo $t['menue2']; ?>"><span class="fa fa-user icon16"></span> <span><?php echo $t['menue2']; ?></span></a>&nbsp;
		<?php
	}
	if ($communityfeatures && $u_level != 'G' && (!isset($beichtstuhl) || !$beichtstuhl || $admin)) {
		?>
		<a href="<?php echo $mlnk[10]; ?>" target="_blank" class="button" title="<?php echo $t['menue10']; ?>"><span class="fa fa-envelope icon16"></span> <span><?php echo $t['menue10'] . $neue_nachrichten;; ?></span></a>&nbsp;
		<?php
	}
	if ($forumfeatures && $communityfeatures && (!isset($beichtstuhl) || !$beichtstuhl || $admin) && ((isset($aktraum) && $aktraum->r_status1 <> "L") || $admin)) {
		?>
		<a href="<?php echo $mlnk[11]; ?>" onMouseOver="return(true)" target="_top" class="button" title="<?php echo $t['menue11']; ?>"><span class="fa fa-commenting icon16"></span> <span><?php echo $t['menue11']; ?></span></a>&nbsp;
		<?php
	}
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		?>
		<a href="<?php echo $mlnk[3]; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[3]; ?>');return(false)" class="button" title="<?php echo $t['menue3']; ?>"><span class="fa fa-cog icon16"></span> <span><?php echo $t['menue3']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin) {
		?>
		<a href="<?php echo $mlnk[5]; ?>" onClick="neuesFenster('<?php echo $mlnk[5]; ?>');return(false)" class="button" title="<?php echo $t['menue5']; ?>"><span class="fa fa-lock icon16"></span> <span><?php echo $t['menue5']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin && $communityfeatures) {
		?>
		<a href="<?php echo $mlnk[12]; ?>" onClick="neuesFenster('<?php echo $mlnk[12]; ?>');return(false)" class="button" title="<?php echo $t['menue12']; ?>"><span class="fa fa-list icon16"></span> <span><?php echo $t['menue12']; ?></span></a>&nbsp;
		<?php
	}
	
	if ($erweitertefeatures && $admin) {
		?>
		<a href="<?php echo $mlnk[13]; ?>" onClick="neuesFenster('<?php echo $mlnk[13]; ?>');return(false)" class="button" title="<?php echo $t['menue13']; ?>"><span class="fa fa-bar-chart icon16"></span> <span><?php echo $t['menue13']; ?></span></a>&nbsp;
		<?php
	}
	
	if ($u_level == "M") {
		?>
		<a href="<?php echo $mlnk[8]; ?>" onClick="neuesFenster('<?php echo $mlnk[8]; ?>');return(false)" class="button" title="<?php echo $t['menue8']; ?>"><span class="fa fa-reply icon16"></span> <span><?php echo $t['menue8']; ?></span></a>&nbsp;
		<?php
	}
	?>
	<a href="<?php echo $mlnk[7]; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[7]; ?>');return(false)" class="button" title="<?php echo $t['menue7']; ?>"><span class="fa fa-archive icon16"></span> <span><?php echo $t['menue7']; ?></span></a>&nbsp;
	<?php
	if ($o_js) {
		?>
		<a href="<?php echo $mlnk[9]; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[9]; ?>');return(false)" class="button" title="<?php echo $t['menue9']; ?>"><span class="fa fa-refresh icon16"></span> <span><?php echo $t['menue9']; ?></span></a>
		<?php
	}
	echo "&nbsp;&nbsp;";
	?>
	<a href="<?php echo $mlnk[6]; ?>" onMouseOver="return(true)" target="_top" class="button" title="<?php echo $t['menue6']; ?>"><span class="fa fa-sign-out icon16"></span> <span><?php echo $t['menue6']; ?></span></a>&nbsp;
	</b></td>
	<?php
	
	unset($aktraum);
	
	echo "<td style=\"text-align: right;\"><span style=\"font-size: smaller; color:#" . $u_farbe . ";\"><b>$t[farbe2]</b>&nbsp;</span></td>";
	for (@reset($farbe_chat_user); list($nummer, $ufarbe) = each($farbe_chat_user);) {
		?>
		<td style="padding-left:0px; padding-right:0px;">
			<a onMouseOver="return(true)" href="eingabe.php?id=<?php echo $id; ?>&farbe=<?php echo $ufarbe; ?>">
				<div style="background-color:#<?php echo $ufarbe; ?>; width:<?php echo $farbe_chat_user_breite; ?>px; height:<?php echo $farbe_chat_user_hoehe; ?>px; border:0px;"></div>
			</a>
		</td>
		<?php
	}
	?>
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