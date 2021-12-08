<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}
switch ($aktion) {
	case "editinfotext":
		if ((strlen($editeintrag) > 0) && (preg_match("/^[0-9]+$/", trim($editeintrag)) == 1)) {
			$query = "SELECT f_text FROM freunde WHERE (f_userid = $u_id or f_freundid = $u_id) AND (f_id = " . mysqli_real_escape_string($mysqli_link, $editeintrag) . ")";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				$infotext = mysqli_result($result, 0, 0);
				formular_editieren($editeintrag, $infotext);
			}
			mysqli_free_result($result);
		}
		break;
	
	case "editinfotext2":
		if ((strlen($f_id) > 0)
			&& (preg_match("/^[0-9]+$/", trim($f_id)) == 1)) {
			$query = "SELECT f_text FROM freunde WHERE (f_userid = $u_id or f_freundid = $u_id) AND (f_id = " . intval($f_id) . ")";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				// f_id ist zahl und gehört zu dem Benutzer, also ist update möglich
				$back = edit_freund($f_id, $f_text);
				echo "<p>$back</p>";
				zeige_freunde("normal", "");
			}
			mysqli_free_result($result);
		}
		break;
	
	case "neu":
	// Formular für neuen Freund ausgeben
		if (!isset($neuer_freund)) {
			$neuer_freund['u_nick'] = "";
			$neuer_freund['f_text'] = "";
		}
		formular_neuer_freund($neuer_freund);
		break;
	
	case "neu2":
	// Neuer Freund, 2. Schritt: Benutzername Prüfen
		$neuer_freund['u_nick'] = htmlspecialchars($neuer_freund['u_nick']);
		$query = "SELECT `u_id`, `u_level` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $neuer_freund['u_nick']) . "'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$neuer_freund['u_id'] = mysqli_result($result, 0, 0);
			$neuer_freund['u_level'] = mysqli_result($result, 0, 1);
			
			$ignore = false;
			$query2 = "SELECT * FROM `iignore` WHERE `i_user_aktiv`='$neuer_freund[u_id]' AND `i_user_passiv` = '$u_id'";
			$result2 = mysqli_query($mysqli_link, $query2);
			$num = mysqli_num_rows($result2);
			if ($num >= 1) {
				$ignore = true;
			}
			;
			mysqli_free_result($result2);
			
			if ($ignore) {
				echo (str_replace("%u_nick%", $neuer_freund['u_nick'], $t['chat_msg116']));
				formular_neuer_freund($neuer_freund);
			} else if ($neuer_freund['u_level'] == 'Z') {
				echo (str_replace("%u_nick%", $neuer_freund['u_nick'], $t['chat_msg117']));
				formular_neuer_freund($neuer_freund);
			} else if ($neuer_freund['u_level'] == 'G') {
				echo (str_replace("%u_nick%", $neuer_freund['u_nick'], $t['chat_msg118']));
				formular_neuer_freund($neuer_freund);
			} else {
				$back = neuer_freund($u_id, $neuer_freund);
				echo "<p>$back</p>";
				formular_neuer_freund($neuer_freund);
				zeige_freunde("normal", "");
			}
			
		} elseif ($neuer_freund['u_nick'] == "") {
			echo "<b>Fehler:</b> Bitte geben Sie einen Benutzernamen an!<br>\n";
			formular_neuer_freund($neuer_freund);
		} else {
			echo "<b>Fehler:</b> Der Benutzername '$neuer_freund[u_nick]' existiert nicht!<br>\n";
			formular_neuer_freund($neuer_freund);
		}
		mysqli_free_result($result);
		break;
	
	case "admins":
	// Alle Admins (Status C und S) als Freund hinzufügen
		$query = "SELECT `u_id`, `u_nick`, `u_level` FROM `user` WHERE `u_level`='S' OR `u_level`='C'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				unset($neuer_freund);
				$neuer_freund['u_nick'] = $rows['u_nick'];
				$neuer_freund['u_id'] = $rows['u_id'];
				$neuer_freund['f_text'] = $level[$rows['u_level']];
				neuer_freund($u_id, $neuer_freund);
			}
			;
		}
		zeige_freunde("normal", "");
		mysqli_free_result($result);
		break;
	
	case "bearbeite":
	// Freund löschen
		if ($los == "LÖSCHEN") {
			if (isset($f_freundid) && is_array($f_freundid)) {
				// Mehrere Freunde löschen
				foreach ($f_freundid as $key => $loesche_id) {
					loesche_freund($loesche_id, $u_id);
				}
				
			} else {
				// Einen Freund löschen
				if (isset($f_freundid) && $f_freundid)
					loesche_freund($f_freundid, $u_id);
			}
		}
		
		if ($los == "BESTÄTIGEN") {
			if (isset($f_freundid) && is_array($f_freundid)) {
				// Mehrere Freunde bestätigen
				foreach ($f_freundid as $key => $bearbeite_id) {
					bestaetige_freund($bearbeite_id, $u_id);
				}
				
			} else {
				// Einen Freund bestätigen
				if (isset($f_freundid) && $f_freundid)
					bestaetige_freund($f_freundid, $u_id);
			}
		}
		
		zeige_freunde("normal", "");
		break;
	
	case "bestaetigen":
		zeige_freunde("bestaetigen", "");
		break;
	
	default:
		zeige_freunde("normal", "");
}
?>