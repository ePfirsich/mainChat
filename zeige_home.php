<?php

require_once("functions.php");
require_once("functions-home.php");
require("functions-hash.php");

$title = $body_titel . ' - Home';
zeige_header_anfang($title, 'mini');
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
	hWnd=window.open(url,name,"resizable=yes,scrollbars=yes,width=600,height=580");
}
</script>
<?php

// Pfad auf Cache
$cache = "home_bild";

if (!checkhash($hash, $ui_userid)) {
	print "<b>Fehler!</b> Hash stimmt nicht!";
	exit;
}

if (!$ui_userid)
	$ui_userid = $u_id;

if (isset($preview) && $preview == "yes") {
	id_lese($preview_id);
}

if (!isset($farben))
	$farben = "";

if (isset($u_id) && $ui_userid == $u_id) {
	zeige_home($ui_userid, TRUE, $farben);
} else {
	zeige_home($ui_userid, FALSE, $farben);
}
?>
</html>