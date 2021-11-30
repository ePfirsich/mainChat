<?php
// Box für Login
zeige_chat_login();

// Wie viele Benutzer sind in der DB?
$query = "SELECT COUNT(u_id) FROM `user` WHERE `u_level` IN ('A','C','G','M','S','U')";
$result = mysqli_query($mysqli_link, $query);
$rows = mysqli_num_rows($result);
if ($result) {
	$useranzahl = mysqli_result($result, 0, 0);
	mysqli_free_result($result);
}

// Benutzer online und Räume bestimmen -> merken
$query = "SELECT o_who,o_name,o_level,r_name,r_status1,r_status2, r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' AS lobby "
	. "FROM online left join raum on o_raum=r_id  WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
	. "ORDER BY lobby desc,r_name,o_who,o_name ";
$result2 = mysqli_query($mysqli_link, $query);
if ($result2) {
	$onlineanzahl = mysqli_num_rows($result2);
}
// Anzahl der angemeldeten Benutzer ausgeben
if ($useranzahl > 1) {
	$text = str_replace("%onlineanzahl%", $onlineanzahl, $t['default2']) . str_replace("%useranzahl%", $useranzahl, $t['default3']);
	
	// Anzahl der Beiträge im Forum ausgeben
	if ($forumfeatures) {
		// Anzahl Themen
		$query = "select count(th_id) from thema";
		$result = mysqli_query($mysqli_link, $query);
		if ($result AND mysqli_num_rows($result) > 0) {
			$themen = mysqli_result($result, 0, 0);
			mysqli_free_result($result);
		}
		
		// Dummy Themen abziehen
		$query = "SELECT count(th_id) FROM thema WHERE th_name = 'dummy-thema'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result AND mysqli_num_rows($result) > 0) {
			$themen = $themen - mysqli_result($result, 0, 0);
			mysqli_free_result($result);
		}
		
		// Anzahl Beiträge 
		$query = "SELECT count(po_id) FROM posting";
		$result = mysqli_query($mysqli_link, $query);
		if ($result AND mysqli_num_rows($result) > 0) {
			$beitraege = mysqli_result($result, 0, 0);
			mysqli_free_result($result);
		}
		if ($beitraege && $themen)
			$text .= str_replace("%themen%", $themen, str_replace("%beitraege%", $beitraege, $t['default8']));
	}
	
	zeige_tabelle_volle_breite($t['default1'], $text);
}

if (!isset($unterdruecke_raeume)) {
	$unterdruecke_raeume = 0;
}
if (!$unterdruecke_raeume && !$abweisen) {
	
	// Wer ist online? Boxen mit Benutzern erzeugen, Topic ist Raumname
	if ($onlineanzahl) {
		show_who_is_online($result2);
	}
}
?>