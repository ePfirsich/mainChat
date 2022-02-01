<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$text = "";

switch ($aktion) {
	case "editinfotext":
		if ( strlen($formulardaten['id']) > 0 ) {
			$query = "SELECT f_text FROM freunde WHERE (f_userid = $u_id OR f_freundid = $u_id) AND (f_id = " . $formulardaten['id'] . ")";
			$result = sqlQuery($query);
			if ($result && mysqli_num_rows($result) == 1) {
				$formulardaten['f_text'] = mysqli_result($result, 0, 0);			
				formular_editieren($formulardaten);
			}
			mysqli_free_result($result);
		}
		break;
	
	case "editinfotext2":
		if ( strlen($formulardaten['id']) > 0 ) {
			$query = "SELECT f_text FROM freunde WHERE (f_userid = $u_id OR f_freundid = $u_id) AND (f_id = " . $formulardaten['id'] . ")";
			$result = sqlQuery($query);
			if ($result && mysqli_num_rows($result) == 1) {
				$erfolgsmeldung = edit_freund($formulardaten);
				$text .= hinweis($erfolgsmeldung, "erfolgreich");
				
				zeige_freunde($text, "normal", "");
			}
			mysqli_free_result($result);
		}
		break;
	
	case "neu":
	// Formular für neuen Freund ausgeben
		formular_neuer_freund("", $formulardaten);
		break;
	
	case "neu2":
		// Neuer Freund, 2. Schritt: Benutzername Prüfen
		$formulardaten['u_nick'] = htmlspecialchars($formulardaten['u_nick']);
		$query = "SELECT `u_id`, `u_level` FROM `user` WHERE `u_nick` = '" . escape_string($formulardaten['u_nick']) . "'";
		$fehlermeldung = "";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$formulardaten['id'] = mysqli_result($result, 0, 0);
			$formulardaten['u_level'] = mysqli_result($result, 0, 1);
			
			$ignore = false;
			$query2 = "SELECT * FROM `iignore` WHERE `i_user_aktiv`='$formulardaten[id]' AND `i_user_passiv` = '$u_id'";
			$result2 = sqlQuery($query2);
			$num = mysqli_num_rows($result2);
			if ($num >= 1) {
				$ignore = true;
			}
			
			mysqli_free_result($result2);
			
			if ($ignore) {
				$fehlermeldung = str_replace("%u_nick%", $formulardaten['u_nick'], $t['freunde_fehlermeldung_ignoriert']);
				$text .= hinweis($fehlermeldung, "fehler");
			} else if ($formulardaten['u_level'] == 'Z') {
				$fehlermeldung = str_replace("%u_nick%", $formulardaten['u_nick'], $t['freunde_fehlermeldung_gesperrt']);
				$text .= hinweis($fehlermeldung, "fehler");
			} else if ($formulardaten['u_level'] == 'G') {
				$fehlermeldung = str_replace("%u_nick%", $formulardaten['u_nick'], $t['freunde_fehlermeldung_gast']);
				$text .= hinweis($fehlermeldung, "fehler");
			}
		} else if ($formulardaten['u_nick'] == "") {
			$fehlermeldung = $t['freunde_fehlermeldung_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else {
			$fehlermeldung = str_replace("%u_nick%", $formulardaten['u_nick'], $t['freunde_fehlermeldung_benutzer_nicht_vorhanden']);
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if($fehlermeldung == "") {
			// Hinzufügen des Freundes erfolgreich
			$text .= neuer_freund($u_id, $formulardaten);
		}
		formular_neuer_freund($text, $formulardaten);
		mysqli_free_result($result);
		break;
	
	case "admins":
	// Alle Admins (Status C und S) als Freund hinzufügen
		$query = "SELECT `u_id`, `u_nick`, `u_level` FROM `user` WHERE `u_level`='S' OR `u_level`='C'";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) > 0) {
			while ($rows = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				unset($formulardaten);
				$formulardaten['u_nick'] = $rows['u_nick'];
				$formulardaten['id'] = $rows['u_id'];
				$formulardaten['f_text'] = $level[$rows['u_level']];
				$text .= neuer_freund($u_id, $formulardaten);
			}
		}
		zeige_freunde($text, "normal", "");
		mysqli_free_result($result);
		break;
	
	case "bearbeite":
		// Freund löschen
		if ($los == $t['freunde_loeschen']) {
			if (isset($bearbeite_ids) && is_array($bearbeite_ids)) {
				foreach ($bearbeite_ids as $bearbeite_id) {
					$text .= loesche_freund($bearbeite_id, $u_id);
				}
			}
		}
		
		if ($los == $t['freunde_bestaetigen']) {
			if (isset($bearbeite_ids) && is_array($bearbeite_ids)) {
				foreach ($bearbeite_ids as $bearbeite_id) {
					$erfolgsmeldung = bestaetige_freund($bearbeite_id, $u_id);
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