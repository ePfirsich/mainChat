<?php
function frameset_forum() {
	global $lang;
	?>
	<frameset rows="100,*" border="0" frameborder="0" framespacing="0">
		<frame src="navigation.php" name="navigation" marginwidth="0" marginheight="0" scrolling="no">
		<frame src="forum.php?bereich=forum" name="chat" marginwidth="0" marginheight="0" scrolling="auto">
	</frameset>
	<noframes>
	<?php echo $lang['login_fehlermeldung_login_fehlermeldung_frames']; ?>
	</noframes>
	<?php
}

function frameset_chat() {
	global $lang, $u_level;
	
	if ($u_level == "M") {
		echo "<frameset rows=\"65,*,170,280\" border=\"0\" frameborder=\"0\" framespacing=\"0\">\n";
	} else {
		echo "<frameset rows=\"65,*,170\" border=\"0\" frameborder=\"0\" framespacing=\"0\">\n";
	}
	?>
		<frame src="navigation.php" name="navigation" marginwidth="0" marginheight="0" scrolling="no">
		<frameset cols="*,250" border="0" frameborder="0" framespacing="0">
			<frame src="chat.php" name="chat" marginwidth="4" marginheight="0">
			<frame src="user.php" name="userliste" marginwidth="4" marginheight="0">
		</frameset>
		<frame src="eingabe.php" name="eingabe" marginwidth="0" marginheight="0" scrolling="no">
		<?php
		if ($u_level == "M") {
			?>
			<frame src="moderator.php" name="moderator" marginwidth="0" marginheight="0" scrolling="auto">
			<?php
		}
		?>
	</frameset>
	<noframes>
	<?php echo $lang['login_fehlermeldung_login_fehlermeldung_frames']; ?>
	</noframes>
	<?php
}
?>