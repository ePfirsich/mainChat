<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

// Löscht alle Nachrichten, die älter als $mailloescheauspapierkorb Tage sind
if ($mailloescheauspapierkorb < 1) {
	$mailloescheauspapierkorb = 14;
}
$query2 = "DELETE FROM mail WHERE m_an_uid = $u_id AND m_status = 'geloescht' AND m_geloescht_ts < '"
	. date("YmdHis", mktime(0, 0, 0, date("m"), date("d") - intval($mailloescheauspapierkorb), date("Y"))) . "'";
$result2 = sqlUpdate($query2, true);

$text = "";

switch ($aktion) {
	case "neu":
	// Formular für neue E-Mail ausgeben
		formular_neue_email($text, $formulardaten);
		break;
	
	case "neu2":
		// Mail versenden, 2. Schritt: Benutzername Prüfen
		$formulardaten['u_nick'] = str_replace(" ", "+", $formulardaten['u_nick']);
		$formulardaten['u_nick'] = escape_string(coreCheckName($formulardaten['u_nick'], $check_name));
		
		$query = "SELECT `u_id`, `u_level` FROM `user` WHERE `u_nick` = '$formulardaten[u_nick]'";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$formulardaten['id'] = mysqli_result($result, 0, "u_id");
			
			$ignore = false;
			$query2 = "SELECT * FROM iignore WHERE i_user_aktiv='" . $formulardaten['id'] . "' AND i_user_passiv = '$u_id'";
			$result2 = sqlQuery($query2);
			$num = mysqli_num_rows($result2);
			if ($num >= 1) {
				$ignore = true;
			}
			
			$boxzu = false;
			$query = "SELECT m_id FROM mail WHERE m_von_uid='" . $formulardaten['id'] . "' AND m_an_uid='" . $formulardaten['id'] . "' and m_betreff = '$t[nachrichten_posteingang_geschlossen]' and m_status != 'geloescht'";
			$result2 = sqlQuery($query);
			$num = mysqli_num_rows($result2);
			if ($num >= 1) {
				$boxzu = true;
			}
			
			if ($boxzu == true) {
				$fehlermeldung = $t['nachrichten_fehler_mailbox_geschlossen'];
			} else {
				$m_u_level = mysqli_result($result, 0, "u_level");
				if ($m_u_level != "G" && $m_u_level = "Z" && $ignore != true) {
					// Alles in Ordnung
					formular_neue_email2($text, $formulardaten);
				} else {
					$fehlermeldung = $t['nachrichten_fehler_keine_mail_an_gast'];
				}
			}
			mysqli_free_result($result2);
		} else if ($formulardaten['u_nick'] == "") {
			$fehlermeldung = $t['nachrichten_fehler_kein_benutzername_angegeben'];
		} else {
			$fehlermeldung = str_replace("%nick%", $formulardaten['u_nick'], $t['nachrichten_fehler_benutzername_existiert_nicht']);
		}
		
		if($fehlermeldung != null && $fehlermeldung != '') {
			// Box anzeigen
			$text .= hinweis($fehlermeldung, "fehler");
			formular_neue_email($text, $formulardaten);
		}
		mysqli_free_result($result);
		break;
	
	case "neu3":
		// Mail versenden, 3. Schritt: Inhalt Prüfen
		
		// Betreff prüfen
		$formulardaten['m_betreff'] = htmlspecialchars($formulardaten['m_betreff']);
		if (strlen($formulardaten['m_betreff']) < 1) {
			$fehlermeldung = $t['nachrichten_fehler_kein_betreff'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		if (strlen($formulardaten['m_betreff']) > 254) {
			$fehlermeldung = $t['nachrichten_fehler_betreff_zu_lang'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		// Text prüfen
		$formulardaten['m_text'] = chat_parse(htmlspecialchars($formulardaten['m_text']));
		if (strlen($formulardaten['m_text']) < 4) {
			$fehlermeldung = $t['nachrichten_fehler_kein_text'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		if (strlen($formulardaten['m_text']) > 10000) {
			$fehlermeldung = $t['nachrichten_fehler_text_zu_lang'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		// Benutzer-ID Prüfen
		$formulardaten['id'] = intval($formulardaten['id']);
		$query = "SELECT `u_nick` FROM `user` WHERE `u_id` = $formulardaten[id]";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$an_nick = mysqli_result($result, 0, "u_nick");
		} else {
			$fehlermeldung = $t['nachrichten_fehler_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		mysqli_free_result($result);
		
		if($fehlermeldung != "") {
			formular_neue_email2($text, $formulardaten);
		} else {
			// Mail schreiben
			if ($formulardaten['typ'] == 1) {
				// E-Mail an externe Adresse versenden
				if (email_versende($u_id, $formulardaten['id'], $formulardaten['m_text'], $formulardaten['m_betreff'], TRUE)) {
					$erfolgsmeldung = str_replace("%nick%", $an_nick, $t['nachrichten_versendet_email']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				} else {
					$fehlermeldung = str_replace("%nick%", $an_nick, $t['nachrichten_fehler_nicht_versendet_email']);
					$text .= hinweis($fehlermeldung, "fehler");
				}
			} else {
				$result = mail_sende($u_id, $formulardaten['id'],
					$formulardaten['m_text'], $formulardaten['m_betreff']);
				
				if ($result[0]) {
					$erfolgsmeldung = str_replace("%nick%", $an_nick, $t['nachrichten_versendet_nachricht']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				} else {
					$fehlermeldung = str_replace("%nick%", $an_nick, $t['nachrichten_fehler_nicht_versendet_nachricht']);
					$text .= hinweis($fehlermeldung, "fehler");
				}
				
			}
			unset($formulardaten);

			zeige_mailbox($text, "normal", "");
		}
		
		break;
	
	case "loesche":
		// Nachrichten als geloescht markieren oder aufheben
		if (isset($bearbeite_ids) && is_array($bearbeite_ids)) {
			foreach ($bearbeite_ids as $bearbeite_id) {
				$text .= loesche_mail($bearbeite_id, $u_id);
			}
		}
		
		zeige_mailbox($text, "normal", "");
		break;
	
	case "antworten":
	// Mail beantworten, Mail quoten und Absender lesen
		$query = "SELECT *,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit FROM mail WHERE m_an_uid=$u_id AND m_id=$formulardaten[id] ";
		$result = sqlQuery($query);
		
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
		}
		mysqli_free_result($result);
		
		// Benutzername prüfen
		$query = "SELECT `u_id`, `u_nick` FROM `user` WHERE `u_id`=" . $row->m_von_uid;
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			
			$row2 = mysqli_fetch_object($result);
			$formulardaten['id'] = $row2->u_id;
			if (substr($row->m_betreff, 0, 3) == "Re:") {
				$formulardaten['m_betreff'] = $row->m_betreff;
			} else {
				$formulardaten['m_betreff'] = "Re: " . $row->m_betreff;
			}
			$formulardaten['m_text'] = erzeuge_umbruch($row->m_text, 70);
			$formulardaten['m_text'] = erzeuge_quoting($formulardaten['m_text'], $row2->u_nick, $row->zeit);
			$formulardaten['m_text'] = erzeuge_fuss($formulardaten['m_text']);
			
			$formulardaten['m_text'] = str_replace("<b>", "_", $formulardaten['m_text']);
			$formulardaten['m_text'] = str_replace("</b>", "_", $formulardaten['m_text']);
			
			$formulardaten['m_text'] = str_replace("<i>", "*", $formulardaten['m_text']);
			$formulardaten['m_text'] = str_replace("</i>", "*", $formulardaten['m_text']);
			
			formular_neue_email2($text, $formulardaten);
			
		} elseif ($formulardaten['u_nick'] == "") {
			$fehlermeldung = $t['nachrichten_fehler_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $formulardaten);
		} else {
			$fehlermeldung = str_replace("%nick%", $formulardaten['u_nick'], $t['nachrichten_fehler_benutzername_existiert_nicht']);
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $formulardaten);
		}
		mysqli_free_result($result);
		break;
	
	case "antworten_forum":
	// Mail beantworten, Mail quoten und Absender lesen
		$query = "SELECT date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_titel, po_text, u_nick, u_id FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id WHERE po_id = " . intval($po_vater_id);
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
		}
		
		// Benutzername prüfen
		if ($row->u_nick && $row->u_nick != "NULL") {
			$row2 = mysqli_fetch_object($result);
			$formulardaten['id'] = $row->u_id;
			if (substr($row->po_titel, 0, 3) == "Re:") {
				$formulardaten['m_betreff'] = $row->po_titel;
			} else {
				$formulardaten['m_betreff'] = "Re: " . $row->po_titel;
			}
			$formulardaten['m_text'] = erzeuge_umbruch($row->po_text, 70);
			$formulardaten['m_text'] = erzeuge_quoting($formulardaten['m_text'],
				$row->u_nick, $row->po_date);
			$formulardaten['m_text'] = erzeuge_fuss($formulardaten['m_text']);
			
			formular_neue_email2($text, $formulardaten);
		} else if ($formulardaten['u_nick'] == "") {
			$fehlermeldung = $t['nachrichten_fehler_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $formulardaten);
		} else {
			$fehlermeldung = str_replace("%nick%", $formulardaten['u_nick'], $t['nachrichten_fehler_benutzername_existiert_nicht']);
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $formulardaten);
		}
		mysqli_free_result($result);
		break;
	
	case "zeige_empfangen":
		// Eine Mail als Detailansicht zeigen
		zeige_email($formulardaten, "empfangen");
		break;
		
	case "zeige_gesendet":
		// Eine Mail als Detailansicht zeigen
		zeige_email($formulardaten, "gesendet");
		break;
		
	case "postausgang":
		// Postausgang zeigen
		$text .= $t['nachrichten_postausgang_info'];
		
		zeige_mailbox($text, "postausgang", "");
		break;
	
	case "papierkorbleeren":
		// Nachrichten mit Status geloescht löschen
		echo "<br>";
		$query = "DELETE FROM mail WHERE m_an_uid=$u_id AND m_status='geloescht'";
		$result = sqlUpdate($query);
		zeige_mailbox("", "normal", "");
		break;
	
	case "papierkorb":
	// Papierkorb zeigen
		if ( $formulardaten['id'] != "" ) {
			zeige_email($formulardaten, "papierkorb");
			echo "<br>";
		}
		zeige_mailbox("", "geloescht", "");
		break;
	
	case "weiterleiten":
	// Formular zum Weiterleiten einer Mail ausgeben
		formular_neue_email($text, $formulardaten);
		break;
	
	case "mailboxzu":
	// Formular zum Weiterleiten einer Mail ausgeben
		$erfolgsmeldung = $t['nachrichten_posteingang_geschlossen'];
		$text = hinweis($erfolgsmeldung, "erfolgreich");
		
		$betreff = $t['nachrichten_posteingang_geschlossen'];
		$nachricht = $t['nachrichten_posteingang_geschlossen_text'];
		$result = mail_sende($u_id, $u_id, $nachricht, $betreff);

		zeige_mailbox($text, "normal", "");
		break;
	
	default:
		zeige_mailbox("", "normal", "");
}
?>