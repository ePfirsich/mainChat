<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
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
		if (!isset($neue_email)) {
			$neue_email = array();
		}
		formular_neue_email($text, $neue_email);
		break;
	
	case "neu2":
		// Mail versenden, 2. Schritt: Benutzername Prüfen
		$neue_email['an_nick'] = str_replace(" ", "+", $neue_email['an_nick']);
		$neue_email['an_nick'] = mysqli_real_escape_string($mysqli_link, coreCheckName($neue_email['an_nick'], $check_name));
		
		if (!isset($m_id)) {
			$m_id = "";
		}
		
		$query = "SELECT `u_id`, `u_level` FROM `user` WHERE `u_nick` = '$neue_email[an_nick]'";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$neue_email['m_an_uid'] = mysqli_result($result, 0, "u_id");
			
			$ignore = false;
			$query2 = "SELECT * FROM iignore WHERE i_user_aktiv='" . intval($neue_email['m_an_uid']) . "' AND i_user_passiv = '$u_id'";
			$result2 = sqlQuery($query2);
			$num = mysqli_num_rows($result2);
			if ($num >= 1) {
				$ignore = true;
			}
			
			$boxzu = false;
			$query = "SELECT m_id FROM mail WHERE m_von_uid='" . intval($neue_email['m_an_uid']) . "' AND m_an_uid='" . intval($neue_email['m_an_uid']) . "' and m_betreff = 'MAILBOX IST ZU' and m_status != 'geloescht'";
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
					formular_neue_email2($text, $neue_email, $m_id);
				} else {
					$fehlermeldung = $t['nachrichten_fehler_keine_mail_an_gast'];
				}
			}
			mysqli_free_result($result2);
		} else if ($neue_email['an_nick'] == "") {
			$fehlermeldung = $t['nachrichten_fehler_kein_benutzername_angegeben'];
		} else {
			$fehlermeldung = str_replace("%nick%", $neue_email['an_nick'], $t['nachrichten_fehler_benutzername_existiert_nicht']);
		}
		
		if($fehlermeldung != null && $fehlermeldung != '') {
			// Box anzeigen
			$text .= hinweis($fehlermeldung, "fehler");
			formular_neue_email($text, $neue_email, $m_id);
		}
		mysqli_free_result($result);
		break;
	
	case "neu3":
		// Mail versenden, 3. Schritt: Inhalt Prüfen
		
		// Betreff prüfen
		$neue_email['m_betreff'] = htmlspecialchars($neue_email['m_betreff']);
		if (strlen($neue_email['m_betreff']) < 1) {
			$fehlermeldung .= $t['nachrichten_fehler_kein_betreff'];
		}
		if (strlen($neue_email['m_betreff']) > 254) {
			$fehlermeldung .= $t['nachrichten_fehler_betreff_zu_lang'];
		}
		
		// Text prüfen
		$neue_email['m_text'] = chat_parse(htmlspecialchars($neue_email['m_text']));
		if (strlen($neue_email['m_text']) < 4) {
			$fehlermeldung .= $t['nachrichten_fehler_kein_text'];
		}
		if (strlen($neue_email['m_text']) > 10000) {
			$fehlermeldung .= $t['nachrichten_fehler_text_zu_lang'];
		}
		
		// Benutzer-ID Prüfen
		$neue_email['m_an_uid'] = intval($neue_email['m_an_uid']);
		$query = "SELECT `u_nick` FROM `user` WHERE `u_id` = $neue_email[m_an_uid]";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$an_nick = mysqli_result($result, 0, "u_nick");
		} else {
			$fehlermeldung .= $t['nachrichten_fehler_kein_benutzername_angegeben'];
		}
		mysqli_free_result($result);
		
		if($fehlermeldung != "") {
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email2($text, $neue_email);
		} else {
			// Mail schreiben
			if ($neue_email['typ'] == 1) {
				// E-Mail an externe Adresse versenden
				if ($neue_email['m_id'] = email_versende($u_id, $neue_email['m_an_uid'], $neue_email['m_text'], $neue_email['m_betreff'], TRUE)) {
					$erfolgsmeldung = str_replace("%nick%", $an_nick, $t['nachrichten_versendet_email']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				} else {
					$fehlermeldung = str_replace("%nick%", $an_nick, $t['nachrichten_fehler_nicht_versendet_email']);
					$text .= hinweis($fehlermeldung, "fehler");
				}
			} else {
				$result = mail_sende($u_id, $neue_email['m_an_uid'],
				$neue_email['m_text'], $neue_email['m_betreff']);
				
				if ($result[0]) {
					$erfolgsmeldung = str_replace("%nick%", $an_nick, $t['nachrichten_versendet_nachricht']);
					$neue_email['m_id'] = $result[0];
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				} else {
					$fehlermeldung = str_replace("%nick%", $an_nick, $t['nachrichten_fehler_nicht_versendet_nachricht']);
					$fehlermeldung .= "<br>$result[1]";
					$text .= hinweis($fehlermeldung, "fehler");
				}
				
			}
			unset($neue_email);

			zeige_mailbox($text, "normal", "");
		}
		
		break;
	
	case "loesche":
		// Nachrichten als geloescht markieren oder aufheben
		
		if (isset($loesche_email) && is_array($loesche_email)) {
			// Mehrere Nachrichten auf gelöscht setzen
			foreach ($loesche_email as $m_id) {
				loesche_mail($m_id, $u_id);
			}
			
		} else {
			// Eine Mail auf gelöscht setzen
			if (isset($loesche_email)) {
				loesche_mail($loesche_email, $u_id);
			}
		}
		
		zeige_mailbox("", "normal", "");
		break;
	
	case "antworten":
	// Mail beantworten, Mail quoten und Absender lesen
		$m_id = intval($m_id);
		$query = "SELECT *,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit FROM mail WHERE m_an_uid=$u_id AND m_id=$m_id ";
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
			$neue_email['m_an_uid'] = $row2->u_id;
			if (substr($row->m_betreff, 0, 3) == "Re:") {
				$neue_email['m_betreff'] = $row->m_betreff;
			} else {
				$neue_email['m_betreff'] = "Re: " . $row->m_betreff;
			}
			$neue_email['m_text'] = erzeuge_umbruch($row->m_text, 70);
			$neue_email['m_text'] = erzeuge_quoting($neue_email['m_text'], $row2->u_nick, $row->zeit);
			$neue_email['m_text'] = erzeuge_fuss($neue_email['m_text']);
			
			$neue_email['m_text'] = str_replace("<b>", "_", $neue_email['m_text']);
			$neue_email['m_text'] = str_replace("</b>", "_", $neue_email['m_text']);
			
			$neue_email['m_text'] = str_replace("<i>", "*", $neue_email['m_text']);
			$neue_email['m_text'] = str_replace("</i>", "*", $neue_email['m_text']);
			
			formular_neue_email2($text, $neue_email);
			
		} elseif ($neue_email['an_nick'] == "") {
			$fehlermeldung = $t['nachrichten_fehler_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $neue_email);
		} else {
			$fehlermeldung = str_replace("%nick%", $neue_email['an_nick'], $t['nachrichten_fehler_benutzername_existiert_nicht']);
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $neue_email);
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
			$neue_email['m_an_uid'] = $row->u_id;
			if (substr($row->po_titel, 0, 3) == "Re:") {
				$neue_email['m_betreff'] = $row->po_titel;
			} else {
				$neue_email['m_betreff'] = "Re: " . $row->po_titel;
			}
			$neue_email['m_text'] = erzeuge_umbruch($row->po_text, 70);
			$neue_email['m_text'] = erzeuge_quoting($neue_email['m_text'],
				$row->u_nick, $row->po_date);
			$neue_email['m_text'] = erzeuge_fuss($neue_email['m_text']);
			
			formular_neue_email2($text, $neue_email);
		} else if ($neue_email['an_nick'] == "") {
			$fehlermeldung = $t['nachrichten_fehler_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $neue_email);
		} else {
			$fehlermeldung = str_replace("%nick%", $neue_email['an_nick'], $t['nachrichten_fehler_benutzername_existiert_nicht']);
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neue_email($text, $neue_email);
		}
		mysqli_free_result($result);
		break;
	
	case "zeige":
		// Eine Mail als Detailansicht zeigen
		zeige_email($m_id, "empfangen");
		break;
		
	case "postausgang":
		// Postausgang zeigen
		zeige_mailbox($text, "postausgang", "");
		break;
	
	case "papierkorbleeren":
		// Nachrichten mit Status geloescht löschen
		echo "<br>";
		$query = "DELETE FROM mail WHERE m_an_uid=$u_id AND m_status='geloescht'";
		$result = sqlUpdate($query);
		if (!isset($neue_email)) {
			$neue_email[] = "";
		}
		zeige_mailbox("", "normal", "");
		break;
	
	case "papierkorb":
	// Papierkorb zeigen
		if (isset($m_id)) {
			zeige_email($m_id);
			echo "<br>";
		}
		zeige_mailbox("", "geloescht", "");
		break;
	
	case "weiterleiten":
	// Formular zum Weiterleiten einer Mail ausgeben
		if (!isset($neue_email)) {
			$neue_email[] = "";
		}
		formular_neue_email($text, $neue_email, $m_id);
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