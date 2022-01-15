<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

switch ($aktion) {
	case "editinfotext":
		if ((strlen($editeintrag) > 0) && (preg_match("/^[0-9]+$/", trim($editeintrag)) == 1)) {
			$query = "SELECT f_text FROM freunde WHERE (f_userid = $u_id or f_freundid = $u_id) AND (f_id = " . mysqli_real_escape_string($mysqli_link, $editeintrag) . ")";
			$result = sqlQuery($query);
			if ($result && mysqli_num_rows($result) == 1) {
				$infotext = mysqli_result($result, 0, 0);
				formular_editieren($editeintrag, $infotext);
			}
			mysqli_free_result($result);
		}
		break;
	
	case "editinfotext2":
		if ((strlen($f_id) > 0) && (preg_match("/^[0-9]+$/", trim($f_id)) == 1)) {
			$query = "SELECT f_text FROM freunde WHERE (f_userid = $u_id or f_freundid = $u_id) AND (f_id = " . intval($f_id) . ")";
			$result = sqlQuery($query);
			if ($result && mysqli_num_rows($result) == 1) {
				// f_id ist zahl und gehört zu dem Benutzer, also ist update möglich
				$erfolgsmeldung = edit_freund($f_id, $f_text);
				$text = hinweis($erfolgsmeldung, "erfolgreich");
				
				zeige_freunde($text, "normal", "");
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
		formular_neuer_freund("", $neuer_freund);
		break;
	
	case "neu2":
		// Neuer Freund, 2. Schritt: Benutzername Prüfen
		$neuer_freund['u_nick'] = htmlspecialchars($neuer_freund['u_nick']);
		$query = "SELECT `u_id`, `u_level` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $neuer_freund['u_nick']) . "'";
		$fehlermeldung = "";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$neuer_freund['u_id'] = mysqli_result($result, 0, 0);
			$neuer_freund['u_level'] = mysqli_result($result, 0, 1);
			
			$ignore = false;
			$query2 = "SELECT * FROM `iignore` WHERE `i_user_aktiv`='$neuer_freund[u_id]' AND `i_user_passiv` = '$u_id'";
			$result2 = sqlQuery($query2);
			$num = mysqli_num_rows($result2);
			if ($num >= 1) {
				$ignore = true;
			}
			
			mysqli_free_result($result2);
			
			if ($ignore) {
				$fehlermeldung = str_replace("%nickname%", $neuer_freund['u_nick'], $t['freunde_fehlermeldung_ignoriert']);
				$text .= hinweis($fehlermeldung, "fehler");
			} else if ($neuer_freund['u_level'] == 'Z') {
				$fehlermeldung = str_replace("%nickname%", $neuer_freund['u_nick'], $t['freunde_fehlermeldung_gesperrt']);
				$text .= hinweis($fehlermeldung, "fehler");
			} else if ($neuer_freund['u_level'] == 'G') {
				$fehlermeldung = str_replace("%nickname%", $neuer_freund['u_nick'], $t['freunde_fehlermeldung_gast']);
				$text .= hinweis($fehlermeldung, "fehler");
			}
		} else if ($neuer_freund['u_nick'] == "") {
			$fehlermeldung = $t['freunde_fehlermeldung_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else {
			$fehlermeldung = str_replace("%nickname%", $neuer_freund['u_nick'], $t['freunde_fehlermeldung_benutzer_nicht_vorhanden']);
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if($fehlermeldung == "") {
			// Hinzufügen des Freundes erfolgreich
			$text = neuer_freund($u_id, $neuer_freund);
		}
		formular_neuer_freund($text, $neuer_freund);
		mysqli_free_result($result);
		break;
	
	case "admins":
	// Alle Admins (Status C und S) als Freund hinzufügen
		$query = "SELECT `u_id`, `u_nick`, `u_level` FROM `user` WHERE `u_level`='S' OR `u_level`='C'";
		$result = sqlQuery($query);
		$text = "";
		if ($result && mysqli_num_rows($result) > 0) {
			while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				unset($neuer_freund);
				$neuer_freund['u_nick'] = $rows['u_nick'];
				$neuer_freund['u_id'] = $rows['u_id'];
				$neuer_freund['f_text'] = $level[$rows['u_level']];
				$text .= neuer_freund($u_id, $neuer_freund);
			}
		}
		zeige_freunde($text, "normal", "");
		mysqli_free_result($result);
		break;
	
	case "bearbeite":
		// Freund löschen
		$text = "";
		if ($los == $t['freunde_loeschen']) {
			if (isset($f_freundid) && is_array($f_freundid)) {
				// Mehrere Freunde löschen
				foreach ($f_freundid as $key => $loesche_id) {
					$text .= loesche_freund($loesche_id, $u_id);
				}
			} else {
				// Einen Freund löschen
				if (isset($f_freundid) && $f_freundid) {
					$text .= loesche_freund($f_freundid, $u_id);
				}
			}
		}
		
		if ($los == $t['freunde_bestaetigen']) {
			if (isset($f_freundid) && is_array($f_freundid)) {
				// Mehrere Freunde bestätigen
				foreach ($f_freundid as $key => $bearbeite_id) {
					$erfolgsmeldung = bestaetige_freund($bearbeite_id, $u_id);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				}
				
			} else {
				// Einen Freund bestätigen
				if (isset($f_freundid) && $f_freundid) {
					$erfolgsmeldung = bestaetige_freund($f_freundid, $u_id);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				}
			}
		}
		
		zeige_freunde($text, "normal", "");
		break;
	
	case "bestaetigen":
		zeige_freunde("", "bestaetigen", "");
		break;
	
	default:
		zeige_freunde("", "normal", "");
}
?>