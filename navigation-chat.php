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
		<a href="raum.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue1']; ?>"><span class="fa fa-road icon16"></span> <span><?php echo $t['menue1']; ?></span></a>&nbsp;
		<a href="user.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue2']; ?>"><span class="fa fa-user icon16"></span> <span><?php echo $t['menue2']; ?></span></a>&nbsp;
		<?php
	}
	if ($u_level != 'G' && (!isset($beichtstuhl) || !$beichtstuhl || $admin)) {
		?>
		<a href="mail.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue10']; ?>"><span class="fa fa-envelope icon16"></span> <span><?php echo $t['menue10'] . $neue_nachrichten;; ?></span></a>&nbsp;
		<?php
	}
	if ($forumfeatures && (!isset($beichtstuhl) || !$beichtstuhl || $admin) && ((isset($aktraum) && $aktraum->r_status1 <> "L") || $admin)) {
		?>
		<a href="index-forum.php?id=<?php echo $id; ?>" onMouseOver="return(true)" target="_top" class="button" title="<?php echo $t['menue11']; ?>"><span class="fa fa-commenting icon16"></span> <span><?php echo $t['menue11']; ?></span></a>&nbsp;
		<?php
	}
	if (!isset($beichtstuhl) || !$beichtstuhl || $admin) {
		?>
		<a href="edit.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue3']; ?>"><span class="fa fa-cog icon16"></span> <span><?php echo $t['menue3']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin) {
		?>
		<a href="sperre.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue5']; ?>"><span class="fa fa-lock icon16"></span> <span><?php echo $t['menue5']; ?></span></a>&nbsp;
		<?php
	}
	if ($admin) {
		?>
		<a href="blacklist.php?id=<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue12']; ?>"><span class="fa fa-list icon16"></span> <span><?php echo $t['menue12']; ?></span></a>&nbsp;
		<?php
	}
	
	if ($admin) {
		?>
		<a href="user.php?id==<?php echo $id; ?>&aktion=statistik" target="chat" class="button" title="<?php echo $t['menue13']; ?>"><span class="fa fa-bar-chart icon16"></span> <span><?php echo $t['menue13']; ?></span></a>&nbsp;
		<?php
	}
	
	if ($u_level == "M") {
		?>
		<a href="moderator.php?id=<?php echo $id; ?>&mode=answer" onClick="neuesFenster('<?php echo $mlnk[8]; ?>');return(false)" class="button" title="<?php echo $t['menue8']; ?>"><span class="fa fa-reply icon16"></span> <span><?php echo $t['menue8']; ?></span></a>&nbsp;
		<?php
	}
	?>
	<a href="log.php?id=<?php echo $id; ?>&back=500" target="_blank" class="button" title="<?php echo $t['menue7']; ?>"><span class="fa fa-archive icon16"></span> <span><?php echo $t['menue7']; ?></span></a>&nbsp;
	<?php
	if ($o_js) {
		?>
		<a href="logout.php?id=<?php echo $id; ?>&reset=1" onMouseOver="return(true)" onClick="neuesFenster('logout.php?id=<?php echo $id; ?>&reset=1');return(false)" class="button" title="<?php echo $t['menue9']; ?>"><span class="fa fa-refresh icon16"></span> <span><?php echo $t['menue9']; ?></span></a>&nbsp;
		<?php
	}
	?>
	<a href="hilfe.php?id==<?php echo $id; ?>" target="chat" class="button" title="<?php echo $t['menue4']; ?>"><span class="fa fa-question icon16"></span> <span><?php echo $t['menue4']; ?></span></a>&nbsp;
	&nbsp;
	<a href="index.php?id=<?php echo $id; ?>&aktion=logoff" target="_top" class="button" title="<?php echo $t['menue6']; ?>"><span class="fa fa-sign-out icon16"></span> <span><?php echo $t['menue6']; ?></span></a>&nbsp;
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