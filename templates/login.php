<?php
// Gibt die Kopfzeile im Login aus
zeige_kopfzeile_login();

if ($zusatznachricht) {
	?>
	<p><span style="font-size: x-large;"><?php echo $zusatznachricht; ?></span></p>
	<?php
}

// Box für Login
?>
<form action="index.php" target="_top" name="form1" method="post">
<?php

$titel = $login_titel;
$titel .= "[<a href=\"" . $_SERVER['PHP_SELF'] . "?aktion=passwort_neu\">" . $t['login27'] . "</a>]";

// Box und Disclaimer ausgeben
zeige_tabelle_variable_breite($titel, $logintext, "100%");
echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n";
echo "</form>";

// Wie viele Benutzer sind in der DB?
$query = "SELECT COUNT(u_id) FROM `user` WHERE `u_level` IN ('A','C','G','M','S','U')";
$result = mysqli_query($mysqli_link, $query);
$rows = mysqli_num_rows($result);
if ($result) {
	$useranzahl = mysqli_result($result, 0, 0);
	mysqli_free_result($result);
}

// Benutzer online und Räume bestimmen -> merken
$query = "SELECT o_who,o_name,o_level,r_name,r_status1,r_status2, r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' as lobby "
	. "FROM online left join raum on o_raum=r_id  WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
	. "ORDER BY lobby desc,r_name,o_who,o_name ";
$result2 = mysqli_query($mysqli_link, $query);
if ($result2)
	$onlineanzahl = mysqli_num_rows($result2);
// Anzahl der angemeldeten Benutzer ausgeben
if ($onlineanzahl > 1 && $useranzahl > 1) {
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
		$query = "select count(po_id) from posting";
		$result = mysqli_query($mysqli_link, $query);
		if ($result AND mysqli_num_rows($result) > 0) {
			$beitraege = mysqli_result($result, 0, 0);
			mysqli_free_result($result);
		}
		if ($beitraege && $themen)
			$text .= str_replace("%themen%", $themen, str_replace("%beitraege%", $beitraege, $t['default8']));
	}
	
	zeige_tabelle_volle_breite(str_replace("%chat%", $chat, $t['default5']), $text);
}

if (!isset($unterdruecke_raeume)) {
	$unterdruecke_raeume = 0;
}
if (!$unterdruecke_raeume && $abweisen == false) {
	
	// Wer ist online? Boxen mit Benutzern erzeugen, Topic ist Raumname
	if ($onlineanzahl) {
		show_who_is_online($result2);
	}
}
// result wird in "show_who_is_online" freigegeben
// mysqli_free_result($result2);

// Fuss
zeige_fuss();
?>