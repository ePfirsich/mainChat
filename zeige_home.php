<?php
require_once("functions/functions.php");
require_once("functions/functions-home.php");
require_once("functions/functions-hash.php");
require_once("languages/$sprache-profil.php");

$title = $body_titel . ' - Home';
zeige_header_anfang($title, 'mini');

// Pfad auf Cache
$cache = "home_bild";

if (!checkhash($hash, $ui_userid)) {
	print "<b>Fehler!</b> Hash stimmt nicht!";
	exit;
}

if (!$ui_userid) {
	$ui_userid = $u_id;
}

if (isset($preview) && $preview == "yes") {
	id_lese($preview_id);
}

if (isset($u_id) && $ui_userid == $u_id) {
	zeige_home($ui_userid, TRUE);
} else {
	zeige_home($ui_userid, FALSE);
}
?>
</html>