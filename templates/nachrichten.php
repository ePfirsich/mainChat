<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$text = "";

if($aktion == "papierkorbleeren") {
	// Nachrichten mit Status geloescht löschen
	pdoQuery("DELETE FROM `mail` WHERE `m_an_uid` = :m_an_uid AND `m_status` = 'geloescht'", [':m_an_uid'=>$u_id]);
	
	$erfolgsmeldung = $lang['nachrichten_papierkorb_geleert'];
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
	
	$aktion = "papierkorb";
}

switch ($aktion) {
	case "neu":
	// Formular für neue E-Mail ausgeben
		formular_neue_email($text, $daten);
		break;
	
	case "neu2":
		// Mail versenden, 2. Schritt: Benutzername Prüfen
		$daten['u_nick'] = str_replace(" ", "+", $daten['u_nick']);
		$daten['u_nick'] = coreCheckName($daten['u_nick'], $check_name);
		
		$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_level`, `u_nachrichten_empfangen` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$daten['u_nick']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$daten['u_id'] = $result['u_id'];
			
			$ignore = false;
			$query2 = pdoQuery("SELECT * FROM `iignore` WHERE `i_user_aktiv` = :i_user_aktiv AND `i_user_passiv` = :i_user_passiv", [':i_user_aktiv'=>$daten['u_id'], ':i_user_passiv'=>$u_id]);
			
			$result2Count = $query2->rowCount();
			if ($result2Count >= 1) {
				$ignore = true;
			}
			
			$boxzu = false;
			// Möchte der Benutzer keine Nachrichten empfangen?
			if($result['u_nachrichten_empfangen'] == 0) {
				$boxzu = true;
			}
			
			if ($boxzu == true) {
				$fehlermeldung = str_replace("%nick%", $result['u_nick'], $lang['nachrichten_fehler_keine_nachrichten_empfangen']);
			} else {
				$m_u_level = $result['u_level'];
				if ($m_u_level != "G" && $m_u_level = "Z" && $ignore != true) {
					// Alles in Ordnung
					formular_neue_email2($text, $lang['nachrichten_neue_nachricht'], $daten);
				} else {
					$fehlermeldung = $lang['nachrichten_fehler_keine_mail_an_gast'];
				}
			}
		} else if ($daten['u_nick'] == "") {
			$fehlermeldung = $lang['nachrichten_fehler_kein_benutzername_angegeben'];
		} else {
			$fehlermeldung = str_replace("%nick%", $daten['u_nick'], $lang['nachrichten_fehler_benutzername_existiert_nicht']);
		}
		
		if($fehlermeldung != null && $fehlermeldung != '') {
			// Box anzeigen
			$text .= hinweis($fehlermeldung, "fehler");
			formular_neue_email($text, $daten);
		}
		break;
	
	case "neu3":
		// Mail versenden, 3. Schritt: Inhalt Prüfen
		
		// Betreff prüfen
		$daten['m_betreff'] = htmlspecialchars($daten['m_betreff']);
		if (strlen($daten['m_betreff']) < 1) {
			$fehlermeldung = $lang['nachrichten_fehler_kein_betreff'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		if (strlen($daten['m_betreff']) > 254) {
			$fehlermeldung = $lang['nachrichten_fehler_betreff_zu_lang'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		// Text prüfen
		$daten['f_text'] = chat_parse(htmlspecialchars($daten['f_text']));
		if (strlen($daten['f_text']) < 4) {
			$fehlermeldung = $lang['nachrichten_fehler_kein_text'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		if (strlen($daten['f_text']) > 10000) {
			$fehlermeldung = $lang['nachrichten_fehler_text_zu_lang'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		// Benutzer-ID Prüfen
		$daten['u_id'] = intval($daten['u_id']);
		
		$query = pdoQuery("SELECT `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$daten['u_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$an_nick = $result['u_nick'];
		} else {
			$fehlermeldung = $lang['nachrichten_fehler_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if($fehlermeldung != "") {
			formular_neue_email2($text, $lang['nachrichten_neue_nachricht'], $daten);
		} else {
			// Mail schreiben
			if ($daten['typ'] == 1) {
				// E-Mail an externe Adresse versenden
				if (email_versende($u_id, $daten['u_id'], $daten['f_text'], $daten['m_betreff'], TRUE)) {
					$erfolgsmeldung = str_replace("%nick%", $an_nick, $lang['nachrichten_versendet_email']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				} else {
					$fehlermeldung = str_replace("%nick%", $an_nick, $lang['nachrichten_fehler_nicht_versendet_email']);
					$text .= hinweis($fehlermeldung, "fehler");
				}
			} else {
				$result = mail_sende($u_id, $daten['u_id'],
				$daten['f_text'], $daten['m_betreff']);
				
				if ($result[0]) {
					$erfolgsmeldung = str_replace("%nick%", $an_nick, $lang['nachrichten_versendet_nachricht']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				} else {
					$fehlermeldung = str_replace("%nick%", $an_nick, $lang['nachrichten_fehler_nicht_versendet_nachricht']);
					$text .= hinweis($fehlermeldung, "fehler");
				}
				
			}
			unset($daten);

			zeige_mailbox($text, "normal", "");
		}
		
		break;
	
	case "loesche":
		// Nachrichten als geloescht markieren oder aufheben
		if (isset($bearbeite_ids) && is_array($bearbeite_ids)) {
			foreach ($bearbeite_ids as $bearbeite_id) {
				$text .= loesche_nachricht($bearbeite_id, $u_id);
			}
		}
		
		zeige_mailbox($text, "normal", "");
		break;
	
	case "antworten":
		// Mail beantworten, Mail quoten und Absender lesen
		$query = pdoQuery("SELECT *, date_format(`m_zeit`,'%d.%m.%y um %H:%i') AS `zeit` FROM `mail` WHERE `m_an_uid` = :m_an_uid AND `m_id` = :m_id", [':m_an_uid'=>$u_id, ':m_id'=>$daten['id']]);
		
		$resultCount2 = 0;
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$row = $query->fetch();
			
			// Benutzername prüfen
			$query2 = pdoQuery("SELECT `u_id`, `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$row['m_von_uid']]);
			$resultCount2 = $query2->rowCount();
		}
		
		if ($resultCount2 == 1) {
			$row2 = $query2->fetch();
			$daten['u_id'] = $row2['u_id'];
			if (substr($row['m_betreff'], 0, 3) == "Re:") {
				$daten['m_betreff'] = html_entity_decode($row['m_betreff']);
			} else {
				$daten['m_betreff'] = "Re: " . html_entity_decode($row['m_betreff']);
			}
			//$daten['f_text'] = erzeuge_umbruch($row['m_text'], 70);
			$daten['f_text'] = $row['m_text'];
			$daten['f_text'] = erzeuge_quoting($daten['f_text'], $row2['u_nick'], $row['zeit']);
			$daten['f_text'] = erzeuge_fuss($daten['f_text']);
			
			$daten['f_text'] = str_replace("<b>", "_", $daten['f_text']);
			$daten['f_text'] = str_replace("</b>", "_", $daten['f_text']);
			
			$daten['f_text'] = str_replace("<i>", "*", $daten['f_text']);
			$daten['f_text'] = str_replace("</i>", "*", $daten['f_text']);
			
			formular_neue_email2($text, $lang['nachrichten_nachricht_antworten'], $daten);
		} else if ($daten['u_nick'] == "") {
			$fehlermeldung = $lang['nachrichten_fehler_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $daten);
		} else {
			$fehlermeldung = str_replace("%nick%", $daten['u_nick'], $lang['nachrichten_fehler_benutzername_existiert_nicht']);
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $daten);
		}
		break;
	
	case "antworten_forum":
		// Mail beantworten, Mail quoten und Absender lesen
		$query = pdoQuery("SELECT date_format(from_unixtime(`po_ts`), '%d.%m.%Y, %H:%i:%s') AS `po_date`, `po_titel`, `po_text`, `u_nick`, `u_id` FROM `forum_beitraege` LEFT JOIN `user` ON `po_u_id` = `u_id` WHERE `po_id` = :po_id", [':po_id'=>intval($po_vater_id)]);
		
		$resultCount = $query->rowCount();
		unset($row);
		if ($resultCount == 1) {
			$row = $query->fetch();
		}
		
		// Benutzername prüfen
		if (isset($row) && $row['u_nick'] && $row['u_nick'] != "NULL") {
			$daten['id'] = $row['u_id'];
			if (substr($row['po_titel'], 0, 3) == "Re:") {
				$daten['m_betreff'] = html_entity_decode($row['po_titel']);
			} else {
				$daten['m_betreff'] = "Re: " . html_entity_decode($row['po_titel']);
			}
			//$daten['f_text'] = erzeuge_umbruch($row['po_text'], 70);
			$daten['f_text'] = $row['m_text'];
			$daten['f_text'] = erzeuge_quoting($daten['f_text'], $row['u_nick'], $row['po_date']);
			$daten['f_text'] = erzeuge_fuss($daten['f_text']);
			
			formular_neue_email2($text, $lang['nachrichten_nachricht_antworten'], $daten);
		} else if ($daten['u_nick'] == "") {
			$fehlermeldung = $lang['nachrichten_fehler_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $daten);
		} else {
			$fehlermeldung = str_replace("%nick%", $daten['u_nick'], $lang['nachrichten_fehler_benutzername_existiert_nicht']);
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $daten);
		}
		break;
	
	case "zeige_empfangen":
		// Eine Mail als Detailansicht zeigen
		zeige_nachricht($daten, "empfangen");
		break;
		
	case "zeige_gesendet":
		// Eine Mail als Detailansicht zeigen
		zeige_nachricht($daten, "gesendet");
		break;
		
	case "postausgang":
		// Postausgang zeigen
		$text .= $lang['nachrichten_postausgang_info'];
		
		zeige_mailbox($text, "postausgang", "");
		break;
	
	case "papierkorb":
	// Papierkorb zeigen
		if ( $daten['id'] != "" ) {
			zeige_nachricht($daten, "papierkorb");
			echo "<br>";
		}
		zeige_mailbox($text, "geloescht", "");
		break;
	
	case "weiterleiten":
	// Formular zum Weiterleiten einer Mail ausgeben
		formular_neue_email($text, $daten);
		break;
	
	default:
		zeige_mailbox("", "normal", "");
}
?>