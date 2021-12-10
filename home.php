<?php
// Kopf nur ausgeben, wenn $ui_userid oder mit $argv[0] aufgerufen
// Sonst geht der redirekt nicht mehr.

require_once("functions/functions.php");
require_once("languages/$sprache-profil.php");

$title = $body_titel . ' - Home';
zeige_header_anfang($title, 'mini');
// Aufruf als home.php/USERNAME -> Redirekt auf home.php?USERNAME
$u_id = "";
$suchwort = $_SERVER["PATH_INFO"];

if (substr($suchwort, -1) == "/") {
	$suchwort = substr($suchwort, 0, -1);
}
$suchwort = strtolower(substr($suchwort, strrpos($suchwort, "/") + 1));

if (strlen($suchwort) >= 4) {
	header("Location: http://" . $_SERVER["HTTP_HOST"]. $_SERVER["SCRIPT_NAME"] . "?" . $suchwort);
}

if (isset($u_id) && $u_id) {
	// Voreinstellungen
	$max_groesse = 60; // Maximale Bild- und Text größe in KB
	
	$hash = genhash($ui_userid);
	//$url = "home.php?/$user_nick";
	$url = "zeige_home.php?ui_userid=$ui_userid&hash=$hash";
	if (isset($preview) && $preview == "yes") {
		$url = "zeige_home.php?ui_userid=$ui_userid&hash=$hash&preview=yes&preview_id=$id";
	}
	?>
	<!DOCTYPE html>
	<html dir="ltr" lang="de">
	<head>
	<title>DEREFER</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<style type="text/css">
	body {
		background-color:#ffffff;
	}
	a, a:link {
		color:#666666;
	}
	a:visited, a:active {
		color:#666666;
	}
	</style>
	<?php
	$meta_refresh = '<meta http-equiv="refresh" content="0; URL=' . $url . '">';
	zeige_header_ende($meta_refresh);
	?>
	<body>
	<table width="100%" height="100%" border="0">
		<tr>
			<td align="center"><a href="<?php echo $url; ?>">Einen Moment bitte, die angeforderte Seite wird geladen...</a></td>
		</tr>
	</table>
	<?php
} else {
	require_once("functions/functions-home.php");
	if (!isset($ui_userid)) {
		$ui_userid = -1;
	}
	zeige_home($ui_userid, FALSE);
}
?>
</body>
</html>