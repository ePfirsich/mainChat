<?php

require_once("functions/functions.php");

// Anzahl der Benutzer, die gerade online sind, als kurzliste ausgeben

$raum_alt = "";
$userliste = "";
$user_im_raum = 0;

$query = "SELECT count(u_id) AS anzahl_user FROM user";
$result = @mysqli_query($mysqli_link, $query);

if ($result && mysqli_num_rows($result) > 0) {
	$anzahl_user = mysqli_result($result, 0, 0);
}
mysqli_free_result($result);

$query = "SELECT count(o_id) as anzahl_online FROM online WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
$result = @mysqli_query($mysqli_link, $query);

if ($result && mysqli_num_rows($result) > 0) {
	$anzahl_online = mysqli_result($result, 0, 0);
}
mysqli_free_result($result);

$query = "SELECT o_name,r_name,UNIX_TIMESTAMP(o_aktiv) as login FROM raum,online WHERE o_raum=r_id " . "AND (r_status1 like 'o' OR r_status1 like 'm') "
		. "AND r_status2='P' AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ORDER BY r_name,o_name ";
$result = @mysqli_query($mysqli_link, $query);

if ($result && mysqli_num_rows($result) > 0) {
	while ($row = @mysqli_fetch_object($result)) {
		if (!$raum_alt)
			$raum_alt = $row->r_name;
		
		if ($raum_alt != $row->r_name) {
			if ($user_im_raum) {
				$txt = str_replace("%raum_alt%", $raum_alt, $t[userliste1]);
				$txt = str_replace("%user_im_raum%", $user_im_raum, $txt);
				$userliste .= str_replace("%nicks%", $nicks, $txt);
				$nicks = "";
				$user_im_raum = 0;
				$raum_alt = $row->r_name;
			}
		}
		
		if ($nicks) {
			$nicks .= ", " . $row->o_name;
		} else {
			$nicks .= $row->o_name;
		}
		$user_im_raum++;
	}
	if ($user_im_raum) {
		$txt = str_replace("%raum_alt%", $raum_alt, $t[userliste1]);
		$txt = str_replace("%user_im_raum%", $user_im_raum, $txt);
		$userliste .= str_replace("%nicks%", $nicks, $txt);
		$nicks = "";
	}
}
mysqli_free_result($result);

if ($anzahl_online && $anzahl_user) {
	$txt = str_replace("%anzahl_online%", $anzahl_online, $t[userliste2]);
}
$txt = str_replace("%anzahl_user%", $anzahl_user, $txt);

header('Access-Control-Allow-Origin: *');
echo str_replace("%userliste%", $userliste, $txt);

?>