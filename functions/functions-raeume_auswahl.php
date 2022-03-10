<?php
function raeume_auswahl($raum, $offen, $alle, $nur_chat = true) {
	// Gibt Liste aller Räume aus, wenn $offen=0
	// Gibt Liste aller offenen (O,T) Räume aus, wenn $offen=1
	// $raum ist Voreinstellung
	// $nur_chat=TRUE -> Nur Räume zeigen
	// $nur_chat=FALSE -> Räume + Community-Bereiche (whotext) zeigen
	
	global $u_id, $o_raum, $timeout, $whotext, $forumfeatures;
	
	$text = '';
	
	// alle=TRUE, falls alle Räume (auch leere) gezeigt werden sollen
	if ($alle) {
		$query = pdoQuery("SELECT `r_id`, COUNT(`o_id`) AS `anzahl` FROM `raum` LEFT JOIN `online` ON `r_id` = `o_raum` WHERE ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout OR o_id IS NULL) GROUP BY `r_id`", [':timeout'=>$timeout]);
	} else {
		$query = pdoQuery("SELECT `r_id`, COUNT(`o_id`) AS `anzahl` FROM `raum` LEFT JOIN `online` ON `r_id` = `o_raum` WHERE ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout) GROUP BY `r_id`", [':timeout'=>$timeout]);
	}
	
	// Liste der Räume mit der Anzahl der Benutzer aufstellen
	
	
	$result = $query->fetchAll();
	foreach($result as $zaehler => $row) {
		$anzahl_user[$row['r_id']] = $row['anzahl'];
	}
	
	// Optional Formularzusatz für Community-Module  ergänzen
	$zusatz_select = "";
	if ($forumfeatures && !$nur_chat) {
		$query = pdoQuery("SELECT `o_who`, COUNT(`o_who`) AS `anzahl` FROM `online` WHERE `o_who` > 1 GROUP BY `o_who`", []);
		
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$anzahl_who[$row['o_who']] = $row['anzahl'];
		}
		
		foreach ($whotext as $key => $whotxt) {
			if ($key > 1 && isset($anzahl_who) && $anzahl_who[$key]) {
				$zusatz_select .= "<OPTION VALUE=\"" . ($key * (-1)) . "\">&gt;&gt;" . $whotxt . "&lt;&lt; (" . $anzahl_who[$key] . ")\n";
			}
		}
	}
	
	// offen=FALSE, falls nur offene Räume gezeigt werden sollen
	if (!$offen) {
		// Liste der Räume aufstellen und die Anzahl der Benutzer ergänzen
		$query = pdoQuery("SELECT `r_id` FROM `raum` LEFT JOIN `invite` ON `inv_raum` = `r_id` WHERE `inv_user` = :inv_user", [':inv_user'=>$u_id]);
		
		$resultCount = $query->rowCount();
		$rows = array();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$rows[] = $row['r_id'];
			}
		}
		
		$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE `r_status1` = 'O' OR `r_status1` LIKE binary 'm' OR `r_besitzer` = :r_besitzer OR `r_id` = :r_id", [':r_besitzer'=>$u_id, ':r_id'=>intval($o_raum)]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$rows[] = $row['r_id'];
			}
		}
	}
	
	// offen=TRUE alle Räume anzeigen
	if ($offen) {
		$query = pdoQuery("SELECT `r_status1`, `r_name`, `r_id` FROM `raum` ORDER BY `r_name`", []);
	} else if (is_array($rows)) {
		$query = pdoQuery("SELECT `r_status1`, `r_name`, `r_id` FROM `raum` WHERE `r_id` IN (" . implode(",", $rows) . ") ORDER BY `r_name`", []);
	} else {
		return 1;
	}
	
	$result = $query->fetchAll();
	$text .= $zusatz_select;
	foreach($result as $zaehler => $row) {
		if ($row['r_status1'] != "O") {
			// G->g übersetzen
			if ($row['r_status1'] == "G") {
				$status = "/g";
			} else {
				$status = "/" . $row['r_status1'];
			}
		} else {
			$status = "";
		}
		if (isset($anzahl_user[$row['r_id']]) && $anzahl_user[$row['r_id']] > 0) {
			$anzahl = $anzahl_user[$row['r_id']];
		} else {
			$anzahl = 0;
		}
		
		// Alle Räume oder nur die Räume mit Benutzern zeigen
		if (($anzahl > 0) || $alle) {
			if ($row['r_id'] == $raum) {
				$text .= "<option selected value=\"$row[r_id]\">$row[r_name] (" . $anzahl . $status . ")\n";
			} else {
				$text .= "<option value=\"$row[r_id]\">$row[r_name] (" . $anzahl . $status . ")\n";
			}
		}
	}
	$text .= $zusatz_select;
	
	return $text;
}
?>