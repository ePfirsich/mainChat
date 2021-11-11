<?php
function frameset_forum($hash_id) {
	global $t, $frame_online, $frame_online_size;
	
	// Obersten Frame definieren
	if (!isset($frame_online)) {
		$frame_online = "frame_online.php";
	}
	
	?>
	<frameset rows="<?php echo $frame_online_size; ?>,*,5,100,1" border="0" frameborder="0" framespacing="0">
		<frame src="<?php echo $frame_online; ?>" name="frame_online" marginwidth="0" marginheight="0" scrolling="no">
		<frame src="forum.php?id=<?php echo $hash_id; ?>" name="forum" marginwidth="0" marginheight="0" scrolling="auto">
		<frame src="leer.php" name="leer" marginwidth="0" marginheight="0" scrolling="no">
		<frame src="interaktiv-forum.php?id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
		<frame src="schreibe.php?id=<?php echo $hash_id; ?>&o_who=2" name="schreibe" marginwidth="0" marginheight="0" scrolling="no">
	</frameset>
	<noframes>
	<?php echo (isset($t['login6']) ? $t['login6'] : ""); ?>
	</noframes>
	<?php
}

function frameset_chat($hash_id) {
	global $t, $frame_online, $frame_online_size, $u_level, $moderationsgroesse;
	
	$frame_size_interaktiv = 60;
	$frame_size_eingabe = 54;
	
	// Obersten Frame definieren
	if (!isset($frame_online)) {
		$frame_online = "frame_online.php";
	}
	
	if ($u_level == "M") {
		$frame_size_interaktiv = $moderationsgroesse;
		$frame_size_eingabe = $frame_size_eingabe * 2;
	}
	
	?>
	<frameset rows="<?php echo $frame_online_size;?>,*,<?php echo $frame_size_eingabe; ?>,<?php echo $frame_size_interaktiv; ?>,1" border="0" frameborder="0" framespacing="0">
		<frame src="<?php echo $frame_online;?>" name="frame_online" marginwidth="0" marginheight="0" scrolling="no">
		<frameset cols="*,250" border="0" frameborder="0" framespacing="0">
			<frame src="chat.php?id=<?php echo $hash_id; ?>&back=<?php echo $back; ?>" name="chat" marginwidth="4" marginheight="0">
			<frame src="user.php?id=<?php echo $hash_id; ?>&aktion=chatuserliste" marginwidth="4" marginheight="0" name="userliste">
		</frameset>
		<frame src="eingabe.php?id=<?php echo $hash_id; ?>" name="eingabe" marginwidth="0" marginheight="0" scrolling="no">
		<?php
		if ($u_level == "M") {
			?>
			<frameset cols="*,220" border="0" frameborder="0" framespacing="0">
				<frame src="moderator.php?id=<?php echo $hash_id; ?>" name="moderator" marginwidth="0" marginheight="0" scrolling="auto">
				<frame src="interaktiv.php?id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
			</frameset>
			<?php
		} else {
			?>
			<frame src="interaktiv.php?id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
			<?php
		}
		?>
		<frame src="schreibe.php?id=<?php echo $hash_id; ?>" name="schreibe" marginwidth="0" marginheight="0" scrolling="no">
	</frameset>
	<noframes>
	<?php echo (isset($t['login6']) ? $t['login6'] : ""); ?>
	</noframes>
	<?php
}
?>