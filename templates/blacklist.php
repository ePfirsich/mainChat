<?php
if (!isset($neuer_blacklist)) {
	$neuer_blacklist[] = "";
}
if (!isset($sort)) {
	$sort = "";
}

switch ($aktion) {
	case "neu":
	// Formular für neuen Eintrag ausgeben
		formular_neuer_blacklist($neuer_blacklist);
		break;
	
	case "neu2":
	// Neuer Eintrag, 2. Schritt: Benutzername Prüfen
		$neuer_blacklist['u_nick'] = mysqli_real_escape_string($mysqli_link, $neuer_blacklist['u_nick']); // sec
		$query = "SELECT `u_id` FROM `user` WHERE `u_nick` = '$neuer_blacklist[u_nick]'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$neuer_blacklist['u_id'] = mysqli_result($result, 0, 0);
			neuer_blacklist($u_id, $neuer_blacklist);
			unset($neuer_blacklist);
			$neuer_blacklist[] = "";
			formular_neuer_blacklist($neuer_blacklist);
			zeige_blacklist("normal", "", $sort);
		} elseif ($neuer_blacklist['u_nick'] == "") {
			echo "<b>Fehler:</b> Bitte geben Sie einen Benutzernamen an!<br>\n";
			formular_neuer_blacklist($neuer_blacklist);
		} else {
			echo "<b>Fehler:</b> Der Benutzername '$neuer_blacklist[u_nick]' existiert nicht!<br>\n";
			formular_neuer_blacklist($neuer_blacklist);
		}
		mysqli_free_result($result);
		break;
	
	case "loesche":
	// Eintrag löschen
	
		if (isset($f_blacklistid) && is_array($f_blacklistid)) {
			// Mehrere Einträge löschen
			foreach ($f_blacklistid as $key => $loesche_id) {
				loesche_blacklist($loesche_id);
			}
			
		} else {
			// Einen Eintrag löschen
			if (isset($f_blacklistid))
				loesche_blacklist($f_blacklistid);
		}
		
		formular_neuer_blacklist($neuer_blacklist);
		zeige_blacklist("normal", "", $sort);
		break;
	
	default:
		zeige_blacklist("normal", "", $sort);
}
?>