<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$text = "";

switch ($aktion) {
	case "editinfotext":
		if ( strlen($daten['id']) > 0 ) {
			$query = pdoQuery("SELECT `f_text` FROM `freunde` WHERE (`f_userid` = :f_userid OR `f_freundid` = :f_freundid) AND (`f_id` = :f_id)", [':f_userid'=>$u_id, ':f_freundid'=>$u_id, ':f_id'=>$daten['id']]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				$result = $query->fetch();
				$daten['f_text'] = $result['f_text'];
				formular_editieren($daten);
			}
		}
		break;
	
	case "editinfotext2":
		if ( strlen($daten['id']) > 0 ) {
			$query = pdoQuery("SELECT `f_text` FROM `freunde` WHERE (`f_userid` = :f_userid OR `f_freundid` = :f_freundid) AND (`f_id` = :f_id)", [':f_userid'=>$u_id, ':f_freundid'=>$u_id, ':f_id'=>$daten['id']]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				$erfolgsmeldung = edit_freund($daten);
				$text .= hinweis($erfolgsmeldung, "erfolgreich");
				
				zeige_freunde($text, "normal", "");
			}
		}
		break;
	
	case "neu":
	// Formular für neuen Freund ausgeben
		formular_neuer_freund("", $daten);
		break;
	
	case "neu2":
		// Neuer Freund, 2. Schritt: Benutzername Prüfen
		$daten['u_nick'] = htmlspecialchars($daten['u_nick']);
		
		$query = pdoQuery("SELECT `u_id`, `u_level` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$daten['u_nick']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$daten['id'] = $result['u_id'];
			$daten['u_level'] = $result['u_level'];
			
			$ignore = false;
			
			$query2 = pdoQuery("SELECT * FROM `iignore` WHERE `i_user_aktiv` = :i_user_aktiv AND `i_user_passiv` = :i_user_passiv", [':i_user_aktiv'=>$daten['id'], ':i_user_passiv'=>$u_id]);
			
			$result2Count = $query2->rowCount();
			if ($result2Count >= 1) {
				$ignore = true;
			}
			
			
			if ($ignore) {
				$fehlermeldung = str_replace("%u_nick%", $daten['u_nick'], $lang['freunde_fehlermeldung_ignoriert']);
				$text .= hinweis($fehlermeldung, "fehler");
			} else if ($daten['u_level'] == 'Z') {
				$fehlermeldung = str_replace("%u_nick%", $daten['u_nick'], $lang['freunde_fehlermeldung_gesperrt']);
				$text .= hinweis($fehlermeldung, "fehler");
			} else if ($daten['u_level'] == 'G') {
				$fehlermeldung = str_replace("%u_nick%", $daten['u_nick'], $lang['freunde_fehlermeldung_gast']);
				$text .= hinweis($fehlermeldung, "fehler");
			}
		} else if ($daten['u_nick'] == "") {
			$fehlermeldung = $lang['freunde_fehlermeldung_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else {
			$fehlermeldung = str_replace("%u_nick%", $daten['u_nick'], $lang['freunde_fehlermeldung_benutzer_nicht_vorhanden']);
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if($fehlermeldung == "") {
			// Hinzufügen des Freundes erfolgreich
			$text .= neuer_freund($u_id, $daten);
		}
		formular_neuer_freund($text, $daten);
		break;
	
	case "admins":
		// Alle Admins (Status C und S) als Freund hinzufügen
		$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_level` FROM `user` WHERE `u_level` = 'S' OR `u_level` = 'C'", []);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				unset($daten);
				$daten['u_nick'] = $row['u_nick'];
				$daten['id'] = $row['u_id'];
				$daten['f_text'] = $level[$row['u_level']];
				$text .= neuer_freund($u_id, $daten);
			}
		}
		zeige_freunde($text, "normal", "");
		break;
	
	case "bearbeite":
		// Freund löschen
		if ($uebergabe == $lang['freunde_loeschen']) {
			if (isset($bearbeite_ids) && is_array($bearbeite_ids)) {
				foreach ($bearbeite_ids as $bearbeite_id) {
					$text .= loesche_freund($bearbeite_id, $u_id);
				}
			}
		}
		
		if ($uebergabe == $lang['freunde_bestaetigen']) {
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