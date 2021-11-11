<?php

require_once("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

if ($u_id) {	
	// Default für Farbe setzen, falls undefiniert
	if (!isset($u_farbe)) {
		$u_farbe = $user_farbe;
	}

$title = $body_titel;
zeige_header_anfang($title, 'chatunten');
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
$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=navigation-chat.php?id=' . $id . '">';
zeige_header_ende($meta_refresh);
?>
<body>
<?php
	// Anzahl der ungelesenen Nachrichten ermitteln
	$query_nachrichten = "SELECT mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') AS zeit,u_nick FROM mail LEFT JOIN user ON m_von_uid=u_id WHERE m_an_uid=$u_id  AND m_status='neu' ORDER BY m_zeit desc";
	$result_nachrichten = mysqli_query($mysqli_link, $query_nachrichten);
	
	if ($result_nachrichten && mysqli_num_rows($result_nachrichten) > 0) {
		$neue_nachrichten = " <span class=\"nachrichten_neu\">(".mysqli_num_rows($result_nachrichten).")</span>";
	} else {
		$neue_nachrichten = '';
	}
	
	$mlnk[4] = "hilfe.php?id=$id";
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
	
	if ($forumfeatures) {
		// Raumstatus lesen, für Temporär, damit FORUM nicht angezeigt wird
		
		$query = "SELECT r_status1 from raum WHERE r_id=" . intval($o_raum);
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$aktraum = mysqli_fetch_object($result);
			mysqli_free_result($result);
		}
	}
	
	echo "<center><table>\n";
	echo "<tr><td style=\"text-align:center;\"><b>";
	echo $f1;
	?>
	
	<?php
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		?>
		<a href="chat.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue1']; ?>"><span class="fa fa-commenting icon16"></span> <span><?php echo $t['menue14']; ?></span></a>&nbsp;
		<a href="<?php echo $mlnk[1]; ?>" target="chat" class="button" title="<?php echo $t['menue1']; ?>"><span class="fa fa-road icon16"></span> <span><?php echo $t['menue1']; ?></span></a>&nbsp;
		<a href="<?php echo $mlnk[2]; ?>" target="chat" class="button" title="<?php echo $t['menue2']; ?>"><span class="fa fa-user icon16"></span> <span><?php echo $t['menue2']; ?></span></a>&nbsp;
		<?php
	}
	if ($u_level != 'G' && (!isset($beichtstuhl) || !$beichtstuhl || $admin)) {
		?>
		<a href="<?php echo $mlnk[10]; ?>" target="chat" class="button" title="<?php echo $t['menue10']; ?>"><span class="fa fa-envelope icon16"></span> <span><?php echo $t['menue10'] . $neue_nachrichten;; ?></span></a>&nbsp;
		<?php
	}
	if ($forumfeatures && (!isset($beichtstuhl) || !$beichtstuhl || $admin) && ((isset($aktraum) && $aktraum->r_status1 <> "L") || $admin)) {
		?>
		<a href="<?php echo $mlnk[11]; ?>" onMouseOver="return(true)" target="_top" class="button" title="<?php echo $t['menue11']; ?>"><span class="fa fa-commenting icon16"></span> <span><?php echo $t['menue11']; ?></span></a>&nbsp;
		<?php
	}
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		?>
		<a href="<?php echo $mlnk[3]; ?>" target="chat" class="button" title="<?php echo $t['menue3']; ?>"><span class="fa fa-cog icon16"></span> <span><?php echo $t['menue3']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin) {
		?>
		<a href="<?php echo $mlnk[5]; ?>" target="chat" class="button" title="<?php echo $t['menue5']; ?>"><span class="fa fa-lock icon16"></span> <span><?php echo $t['menue5']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin) {
		?>
		<a href="<?php echo $mlnk[12]; ?>" target="chat" class="button" title="<?php echo $t['menue12']; ?>"><span class="fa fa-list icon16"></span> <span><?php echo $t['menue12']; ?></span></a>&nbsp;
		<?php
	}
	
	if ($erweitertefeatures && $admin) {
		?>
		<a href="<?php echo $mlnk[13]; ?>" target="chat" class="button" title="<?php echo $t['menue13']; ?>"><span class="fa fa-bar-chart icon16"></span> <span><?php echo $t['menue13']; ?></span></a>&nbsp;
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
		<a href="<?php echo $mlnk[9]; ?>" onMouseOver="return(true)" onClick="neuesFenster('<?php echo $mlnk[9]; ?>');return(false)" class="button" title="<?php echo $t['menue9']; ?>"><span class="fa fa-refresh icon16"></span> <span><?php echo $t['menue9']; ?></span></a>&nbsp;
		<?php
	}
	?>
	<a href="<?php echo $mlnk[4]; ?>" target="chat" class="button" title="<?php echo $t['menue4']; ?>"><span class="fa fa-question icon16"></span> <span><?php echo $t['menue4']; ?></span></a>&nbsp;
	&nbsp;
	<a href="<?php echo $mlnk[6]; ?>" target="_top" class="button" title="<?php echo $t['menue6']; ?>"><span class="fa fa-sign-out icon16"></span> <span><?php echo $t['menue6']; ?></span></a>&nbsp;
	<?php
	echo $f2;
	echo "</b></td></tr></table></center>";
	
	unset($aktraum);
} else {
	echo "<vody onLoad='parent.location.href=\"index.php\"'>\n";
}

?>
</body>
</html>