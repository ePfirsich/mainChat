<?php

// Die öffentlichen Räume als Liste für ein Loginformular ausgeben

require_once("functions.php");

// Falls eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
if (strlen($eintrittsraum) == 0) {
	$eintrittsraum = $lobby;
}

// Raumauswahlliste erstellen
$query = "SELECT r_name,r_id FROM raum WHERE (r_status1='O' OR r_status1 LIKE BINARY 'm') AND r_status2='P' ORDER BY r_name";

$result = mysqli_query($mysqli_link, $query);

if ($result) {
	$rows = mysqli_num_rows($result);
}

$raeume = "";

if ($rows > 0) {
	$i = 0;
	while ($i < $rows) {
		$r_id = mysqli_result($result, $i, "r_id");
		$r_name = mysqli_result($result, $i, "r_name");
		if ((!$eintritt AND $r_name == $eintrittsraum) || ($eintritt AND $r_id == $eintritt)) {
			$raeume = $raeume . "<OPTION SELECTED VALUE=\"$r_id\">$r_name\n";
		} else {
			$raeume = $raeume . "<OPTION VALUE=\"$r_id\">$r_name\n";
		}
		$i++;
	}
}
mysqli_free_result($result);

echo "$raeume";
?>
