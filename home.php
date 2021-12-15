<?php
// Kopf nur ausgeben, wenn $ui_userid oder mit $argv[0] aufgerufen
// Sonst geht der redirekt nicht mehr.

require_once("functions/functions.php");
require_once("functions/functions-home.php");
require_once("languages/$sprache-profil.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
$ui_userid_get = filter_input(INPUT_GET, 'ui_userid', FILTER_SANITIZE_NUMBER_INT);

$title = $body_titel . ' - Home';
zeige_header_anfang($title, 'mini');

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == "") {
	$ui_userid = -1;
}

// Aufruf als home.php/USERNAME -> Redirekt auf home.php?USERNAME
$suchwort = $_SERVER["PATH_INFO"];

if (substr($suchwort, -1) == "/") {
	$suchwort = substr($suchwort, 0, -1);
}
$suchwort = strtolower(substr($suchwort, strrpos($suchwort, "/") + 1));

if (strlen($suchwort) >= 4) {
	header("Location: http://" . $_SERVER["HTTP_HOST"]. $_SERVER["SCRIPT_NAME"] . "?" . $suchwort);
}

if (!isset($ui_userid)) {
	$ui_userid = -1;
}

if($ui_userid != -1) {
	// Die eigene Homepage darf immer aufgerufen werden
	zeige_home($ui_userid, true);
} else {
	zeige_home($ui_userid, false);
}
?>
</body>
</html>