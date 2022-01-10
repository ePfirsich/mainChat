<?php
function frameset_forum($hash_id) {
	global $t;
	?>
	<frameset rows="100,*" border="0" frameborder="0" framespacing="0">
		<frame src="navigation.php?id=<?php echo $hash_id; ?>" name="navigation" marginwidth="0" marginheight="0" scrolling="no">
		<frame src="forum.php?id=<?php echo $hash_id; ?>" name="chat" marginwidth="0" marginheight="0" scrolling="auto">
	</frameset>
	<noframes>
	<?php echo $t['login_fehlermeldung_login_fehlermeldung_frames']; ?>
	</noframes>
	<?php
}

function frameset_chat($hash_id) {
	global $t, $u_level, $back;
	
	if ($u_level == "M") {
		echo "<frameset rows=\"65,*,94,65,280,1\" border=\"0\" frameborder=\"0\" framespacing=\"0\">\n";
	} else {
		echo "<frameset rows=\"65,*,34,65,1\" border=\"0\" frameborder=\"0\" framespacing=\"0\">\n";
	}
	?>
		<frame src="navigation.php?id=<?php echo $hash_id; ?>" name="navigation" marginwidth="0" marginheight="0" scrolling="no">
		<frameset cols="*,250" border="0" frameborder="0" framespacing="0">
			<frame src="chat.php?id=<?php echo $hash_id; ?>&back=<?php echo $back; ?>" name="chat" marginwidth="4" marginheight="0">
			<frame src="user.php?id=<?php echo $hash_id; ?>" name="userliste" marginwidth="4" marginheight="0">
		</frameset>
		<frame src="eingabe.php?id=<?php echo $hash_id; ?>" name="eingabe" marginwidth="0" marginheight="0" scrolling="no">
		<frame src="interaktiv.php?id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
		<?php
		if ($u_level == "M") {
			?>
			<frame src="moderator.php?id=<?php echo $hash_id; ?>" name="moderator" marginwidth="0" marginheight="0" scrolling="auto">
			<?php
		}
		?>
		<frame src="schreibe.php?id=<?php echo $hash_id; ?>" name="schreibe" marginwidth="0" marginheight="0" scrolling="no">
	</frameset>
	<noframes>
	<?php echo $t['login_fehlermeldung_login_fehlermeldung_frames']; ?>
	</noframes>
	<?php
}
?>