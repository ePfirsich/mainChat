<?php
function suche_vaterposting($poid)
{
	// Diese Funktion sucht das Vaterposting des übergebenen Beitrags
	$query = "SELECT po_vater_id FROM posting WHERE po_id = '" . intval($poid) . "'";
	$result = mysqli_query($mysqli_link, $query);
	list($vp) = mysqli_fetch_array($result);
	return ($vp);
}

function suche_threadord($poid)
{
	// Diese Funktion sucht die Themenorder des Vaterpostings
	$query = "SELECT po_threadorder FROM posting WHERE po_id = '" . intval($poid) . "'";
	$result = mysqli_query($mysqli_link, $query);
	list($to) = mysqli_fetch_array($result);
	return ($to);
}

function mail_neu($u_id, $u_nick, $id, $nachricht = "OLM") {
	// Hat der Benutzer neue Chat-Mail?
	// $u_id ist die ID des des Benutzers
	// $nachricht ist die Art, wie die Nachricht verschickt wird (E-Mail, Chat-Mail, OLM)
	
	global $system_farbe, $mysqli_link, $communityfeatures, $t, $chat;
	
	$query = "SELECT mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,u_nick "
		. "FROM mail LEFT JOIN user on m_von_uid=u_id "
		. "WHERE m_an_uid=$u_id  AND m_status='neu' "
		. "order by m_zeit desc";
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		
		// Sonderfall OLM: "Sie haben neue Mail..." ausgeben.
		if ($nachricht == "OLM") {
			$ur1 = "mail.php?id=$id&aktion=";
			$url = "href=\"$ur1\" target=\"_blank\"";
			system_msg("", 0, $u_id, $system_farbe,
				str_replace("%link%", $url, $t['mail1']));
		}
		
		while ($row = mysqli_fetch_object($result)) {
			
			// Nachricht verschicken
			switch ($nachricht) {
				
				case "OLM":
					$txt = str_replace("%zeit%", $row->zeit, $t['mail2']);
					if ($row->u_nick != "NULL" && $row->u_nick != "") {
						$txt = str_replace("%nick%", $row->u_nick, $txt);
					} else {
						$txt = str_replace("%nick%", $chat, $txt);
					}
					system_msg("", 0, $u_id, $system_farbe,
						str_replace("%betreff%", $row->m_betreff, $txt));
					break;
				
				case "E-Mail":
				// Mail als verschickt markieren
					unset($f);
					$f['m_status'] = "neu/verschickt";
					$f['m_zeit'] = $row->m_zeit;
					schreibe_db("mail", $f, $row->m_id, "m_id");
					
					// E-Mail an u_id versenden
					email_versende($row->m_von_uid, $u_id,
						$t['mail3'] . $row->m_text, $row->m_betreff);
					break;
				
			}
		}
		
	}
	mysqli_free_result($result);
	
}

function profil_neu($u_id, $u_nick, $id) {
	// Hat der Benutzer sein Profil ausgefüllt?
	// Falls nein, wird einer Erinnerung ausgegeben
	// $u_id ist die ID des des Benutzers
	
	global $system_farbe, $mysqli_link, $communityfeatures, $t;
	
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);
	
	$query = "SELECT ui_id FROM userinfo WHERE ui_userid=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 0) {
		$ur1 = "profil.php?id=$id&aktion=neu";
		$url = "href=\"$ur1\" target=\"640_$fenster\" onclick=\"neuesFenster('$ur1'); return(false);\"";
		system_msg("", 0, $u_id, $system_farbe, str_replace("%link%", $url, $t['profil1']));
	}
	mysqli_free_result($result);
	
}

function autoselect($name, $voreinstellung, $tabelle, $feld) {
	// Erzeugt Select-Feld aus der Datenbank
	// $name=Name der Auswahl
	// $voreinstellung=Voreinstellung der Auswahl
	// $tabelle=Name der Tabelle in der Datenbank
	// $feld=Name des Felds
	
	global $system_farbe, $mysqli_link, $communityfeatures, $t;
	
	$query = "SHOW COLUMNS FROM $tabelle LIKE '" . mysqli_real_escape_string($mysqli_link, $feld) . "'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$txt = substr(mysqli_result($result, 0, "Type"), 4, -1);
		$felder = explode(",", $txt);
		echo "<select name=\"$name\">\n";
		while (list($key, $set_name) = each($felder)) {
			$set_name = substr($set_name, 1, -1);
			if ($set_name == $voreinstellung) {
				echo "<option selected value=$set_name>$set_name\n";
			} else {
				echo "<option value=\"$set_name\">$set_name\n";
			}
		}
		echo "</select>\n";
	}
	mysqli_free_result($result);
}

function punkte($anzahl, $o_id, $u_id = 0, $text = "", $sofort = FALSE) {
	// Addiert/Subtrahiert $anzahl Punkte auf das Punktekonto des Benutzers $o_id/$u_id
	// Dieser Benutzer muss online sein, die punkte werden in der Tabelle online addiert
	// Falls $text Zeichen enthält, wird der Text mit einem Standardtext ausgegeben
	
	global $t, $o_punkte, $communityfeatures, $mysqli_link, $punkte_gruppe;
	
	// In die Datenbank schreiben
	if ($anzahl > 0 || $anzahl < 0) {
		if ($u_id != 0) {
			$where = "WHERE o_user=$u_id";
		} else {
			$where = "WHERE o_id=$o_id";
		}
		$query = "UPDATE online set o_punkte=o_punkte+" . intval($anzahl) . " " . $where;
		mysqli_query($mysqli_link, $query);
	}
	
	// Meldung an Benutzer ausgeben
	if (strlen($text) > 0) {
		if ($anzahl > 0) {
			// Gutschrift
			$text = str_replace("%text%", $text, $t['punkte1']);
			$text = str_replace("%punkte%", $anzahl, $text);
		} else {
			// Abzug
			$text = str_replace("%text%", $text, $t['punkte2']);
			$text = str_replace("%punkte%", $anzahl * (-1), $text);
		}
		if ($u_id != 0)
			system_msg("", 0, $u_id,
				(isset($system_farbe) ? $system_farbe : ""), $text);
	}
	
	// Optional Punkte sofort in Benutzerdaten übertragen
	if ($sofort && $o_id) {
		
		// Tabellen online+user exklusiv locken
		$query = "LOCK	TABLES online WRITE, user WRITE";
		$result = mysqli_query($mysqli_link, $query);
		
		// Aktuelle Punkte auf Punkte in Benutzertabelle addieren
		$result = mysqli_query($mysqli_link, 
			"select o_punkte,o_user FROM online WHERE o_id=" . intval($o_id));
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
			$u_id = $row->o_user;
			
			// es können maximal die punkte abgezogen werden, die man auch hat
			$result2 = mysqli_query($mysqli_link, 
				"SELECT `u_punkte_gesamt`, `u_punkte_jahr`, `u_punkte_monat` FROM `user` WHERE `u_id`=$u_id");
			if ($result2 && mysqli_num_rows($result2) == 1) {
				$row2 = mysqli_fetch_object($result2);
				$p_gesamt = $row2->u_punkte_gesamt + $row->o_punkte;
				$p_jahr = $row2->u_punkte_jahr + $row->o_punkte;
				$p_monat = $row2->u_punkte_monat + $row->o_punkte;
				
				if ($p_gesamt < 0)
					$p_gesamt = 0;
				if ($p_jahr < 0)
					$p_jahr = 0;
				if ($p_monat < 0)
					$p_monat = 0;
			}
			
			$query = "update user set " . "u_punkte_monat=$p_monat, "
				. "u_punkte_jahr=$p_jahr, " . "u_punkte_gesamt=$p_gesamt "
				. "where u_id=$u_id";
			$result2 = mysqli_query($mysqli_link, $query);
			$result = mysqli_query($mysqli_link, "UPDATE online set o_punkte=0 WHERE o_id=" . intval($o_id));
		}
		
		// Gruppe neu berechnen
		unset($f);
		$f['u_punkte_gruppe'] = 0;
		$result = mysqli_query($mysqli_link, 
			"SELECT `u_punkte_gesamt` FROM `user` WHERE `u_id`=$u_id");
		if ($result && mysqli_num_rows($result) == 1) {
			$u_punkte_gesamt = mysqli_result($result, 0, 0);
			foreach ($punkte_gruppe as $key => $value) {
				if ($u_punkte_gesamt < $value) {
					break;
				} else {
					$f['u_punkte_gruppe'] = dechex($key);
				}
			}
		}
		
		schreibe_db("user", $f, $u_id, "u_id");
		
		// Lock freigeben
		$query = "UNLOCK TABLES";
		$result = mysqli_query($mysqli_link, $query);
	}
}

function punkte_offline($anzahl, $u_id)
{
	// Addiert/Subtrahiert $anzahl Punkte auf das Punktekonto des Benutzers $u_id
	// Die Punkte werden direkt in die user-tabelle geschrieben
	// Optional wird Info-Text als Ergebnis zurückgeliefert
	
	global $t, $communityfeatures, $mysqli_link, $punkte_gruppe;
	
	// In die Datenbank schreiben
	// Tabellen online+user exklusiv locken
	$query = "LOCK	 TABLES online WRITE, user WRITE";
	$result = mysqli_query($mysqli_link, $query);
	
	// Aktuelle Punkte auf Punkte in Benutzertabelle addieren
	if ($u_id && ($anzahl > 0 || $anzahl < 0)) {
		
		// es können maximal die punkte abgezogen werden, die man auch hat
		$result2 = mysqli_query($mysqli_link, 
			"SELECT `u_punkte_gesamt`, `u_punkte_jahr`, `u_punkte_monat` FROM `user` WHERE `u_id`=$u_id");
		if ($result2 && mysqli_num_rows($result2) == 1) {
			$row2 = mysqli_fetch_object($result2);
			$p_gesamt = $row2->u_punkte_gesamt + $anzahl;
			$p_jahr = $row2->u_punkte_jahr + $anzahl;
			$p_monat = $row2->u_punkte_monat + $anzahl;
			
			if ($p_gesamt < 0)
				$p_gesamt = 0;
			if ($p_jahr < 0)
				$p_jahr = 0;
			if ($p_monat < 0)
				$p_monat = 0;
		}
		
		$query = "update user set " . "u_login=u_login, "
			. "u_punkte_monat=$p_monat, " . "u_punkte_jahr=$p_jahr, "
			. "u_punkte_gesamt=$p_gesamt " . "where u_id=$u_id";
		$result = mysqli_query($mysqli_link, $query);
		
	}
	
	// Gruppe neu berechnen
	unset($f);
	$f['u_punkte_gruppe'] = 0;
	$result = mysqli_query($mysqli_link, 
		"SELECT `u_punkte_gesamt`, `u_nick` FROM `user` WHERE `u_id`=$u_id");
	if ($result && mysqli_num_rows($result) == 1) {
		$u_punkte_gesamt = mysqli_result($result, 0, "u_punkte_gesamt");
		$u_nick = mysqli_result($result, 0, "u_nick");
		foreach ($punkte_gruppe as $key => $value) {
			if ($u_punkte_gesamt < $value) {
				break;
			} else {
				$f['u_punkte_gruppe'] = dechex($key);
			}
		}
	}
	schreibe_db("user", $f, $u_id, "u_id");
	
	// Lock freigeben
	$query = "UNLOCK TABLES";
	$result = mysqli_query($mysqli_link, $query);
	
	// Meldung an Benutzer ausgeben
	if ($anzahl > 0) {
		// Gutschrift
		$text = str_replace("%user%", $u_nick, $t['punkte21']);
		$text = str_replace("%punkte%", $anzahl, $text);
	} else {
		// Abzug
		$text = str_replace("%user%", $u_nick, $t['punkte22']);
		$text = str_replace("%punkte%", $anzahl * (-1), $text);
	}
	
	return ($text);
	
}

function aktion(
	$typ,
	$an_u_id,
	$u_nick,
	$id = "",
	$suche_was = "",
	$inhalt = "")
{
	
	// Programmierbare Aktionen für Benutzer $an_u_id von Benutzer $u_nick
	// Verschickt eine Nachricht (Aktion) zum Login, Raumwechsel, Sofort oder alle 5 Minuten aus (a_wann)
	// Die Aktion ist Mail (Chatintern), E-Mail an die Adresse des Benutzers oder eine Online-Message (a_wie)
	// Die betroffene Chat-Funktion (zb. Freund-Login/Logout, Mailempfang) wird als Text definiert (a_was)
	// Pro Funktion kann ein Infotext hinterlegt werden (a_text)
	// typ = "Sofort/Offline", "Sofort/Online", "Login", "Alle 5 Minuten"
	// Die Session-ID $id kann optional übergeben werden
	// Mit der Angabe von $suche_was kann die Suche auf ein a_was eingeschränkt werden
	// Für "Sofort/Offline" und "Sofort/Online" muss der Inhalt in $inhalt übergeben werden
	global $u_id, $t, $communityfeatures, $mysqli_link;
	
	if ($communityfeatures) {
		
		// Einstellungen aus DB in Array a_was merken und dabei SETs auflösen
		// Mögliche a_wann: Sofort/Offline, Sofort/Online, Login, Alle 5 Minuten
		$query = "SELECT a_was,a_wie from aktion " . "WHERE a_user=$an_u_id "
			. "AND a_wann='$typ' ";
		if ($suche_was != "")
			$query .= "AND a_was='$suche_was'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) != 0) {
			while ($row = mysqli_fetch_object($result)) {
				$wie = explode(",", $row->a_wie);
				foreach ($wie as $a_wie) {
					$a_was[$row->a_was][$a_wie] = TRUE;
				}
			}
			
		}
		mysqli_free_result($result);
		
		switch ($typ) {
			
			case "Sofort/Offline":
			case "Sofort/Online":
				if ($suche_was != "" && isset($a_was)
					&& is_array($a_was[$suche_was])) {
					foreach ($a_was[$suche_was] as $wie => $was) {
						aktion_sende($suche_was, $wie, $inhalt, $an_u_id,
							$u_id, $u_nick, $id);
					}
				}
				break;
			
			case "Alle 5 Minuten":
			// Aktionen ausführen
				if (isset($a_was["Freunde"]["OLM"]))
					freunde_online($an_u_id, $u_nick, $id, "OLM");
				if (isset($a_was["Freunde"]["Chat-Mail"]))
					freunde_online($an_u_id, $u_nick, $id, "Chat-Mail");
				if (isset($a_was["Freunde"]["E-Mail"]))
					freunde_online($an_u_id, $u_nick, $id, "E-Mail");
				
				if (isset($a_was["Neue Mail"]["OLM"]))
					mail_neu($an_u_id, $u_nick, $id, "OLM");
				if (isset($a_was["Neue Mail"]["E-Mail"]))
					mail_neu($an_u_id, $u_nick, $id, "E-Mail");
				
				if (isset($a_was["Antwort auf eigenen Beitrag"]["OLM"]))
					postings_neu($an_u_id, $u_nick, $id, "OLM");
				if (isset($a_was["Antwort auf eigenen Beitrag"]["Chat-Mail"]))
					postings_neu($an_u_id, $u_nick, $id, "Chat-Mail");
				if (isset($a_was["Antwort auf eigenen Beitrag"]["E-Mail"]))
					postings_neu($an_u_id, $u_nick, $id, "E-Mail");
				
				// Merken, wann zuletzt die Aktionen ausgeführt wurden
				$query = "UPDATE online SET o_aktion=" . time()
					. " where o_user=$an_u_id";
				$result = mysqli_query($mysqli_link, $query);
				
				break;
			
			case "Login":
			default:
			// Aktionen ausführen
				if (isset($a_was["Freunde"]["OLM"]))
					freunde_online($an_u_id, $u_nick, $id, "OLM");
				if (isset($a_was["Freunde"]["Chat-Mail"]))
					freunde_online($an_u_id, $u_nick, $id, "Chat-Mail");
				if (isset($a_was["Freunde"]["E-Mail"]))
					freunde_online($an_u_id, $u_nick, $id, "E-Mail");
				
				if (isset($a_was["Neue Mail"]["OLM"]))
					mail_neu($an_u_id, $u_nick, $id, "OLM");
				if (isset($a_was["Neue Mail"]["E-Mail"]))
					mail_neu($an_u_id, $u_nick, $id, "E-Mail");
				
				if (isset($a_was["Antwort auf eigenen Beitrag"]["OLM"]))
					postings_neu($an_u_id, $u_nick, $id, "OLM");
				if (isset($a_was["Antwort auf eigenen Beitrag"]["Chat-Mail"]))
					postings_neu($an_u_id, $u_nick, $id, "Chat-Mail");
				if (isset($a_was["Antwort auf eigenen Beitrag"]["E-Mail"]))
					postings_neu($an_u_id, $u_nick, $id, "E-Mail");
			
		}
		
	}
	
}
;

function aktion_sende(
	$a_was,
	$a_wie,
	$inhalt,
	$an_u_id,
	$von_u_id,
	$u_nick,
	$id = "")
{
	
	// Versendet eine Nachricht an Benutzer $an_u_id von $von_u_id/$u_nick
	// Der Inhalt der Nachricht wird in $inhalt übergeben
	// Die Session-ID $id kann optional übergeben werden
	// Die Aktion ist Mail (Chatintern), E-Mail an die Adresse des Benutzers oder eine Online-Message (a_wie)
	// Die betroffene Chat-Funktion (zb. Freund-Login/Logout, Mailempfang) wird als Text definiert (a_was)
	
	global $system_farbe, $communityfeatures, $t;
	
	$userlink = zeige_userdetails($von_u_id, 0, FALSE, "&nbsp;", "", "", FALSE);
	
	switch ($a_wie) {
		
		case "OLM":
		// Nachricht erzeugen
			switch ($a_was) {
				case "Freunde":
				// Nachricht von Login/Logoff erzeugen
					if ($inhalt['aktion'] == "Login" && $inhalt['raum']) {
						$txt = str_replace("%u_nick%", $userlink,
							$t['freunde1']);
						$txt = str_replace("%raum%", $inhalt['raum'], $txt);
					} elseif ($inhalt['aktion'] == "Login") {
						$txt = str_replace("%u_nick%", $userlink,
							$t['freunde5']);
					} else {
						$txt = str_replace("%u_nick%", $u_nick, $t['freunde2']);
					}
					if ($inhalt['f_text'])
						$txt .= " (" . $inhalt['f_text'] . ")";
					system_msg("", 0, $an_u_id, $system_farbe, $txt);
					break;
				
				case "Neue Mail":
				// Nachricht erzeugen
					$txt = str_replace("%nick%", $u_nick, $t['mail7']);
					$txt = str_replace("%betreff%", $inhalt['m_betreff'], $txt);
					
					// Nachricht versenden
					system_msg("", 0, $an_u_id, $system_farbe, $txt);
					break;
				case "Antwort auf eigenen Beitrag":
					$text = str_replace("%po_titel%", $inhalt['po_titel'],
						$t['msg_new_posting_olm']);
					$text = str_replace("%po_ts%", $inhalt['po_ts'], $text);
					$text = str_replace("%forum%", $inhalt['forum'], $text);
					$text = str_replace("%thema%", $inhalt['thema'], $text);
					$text = str_replace("%user_from_nick%",
						$inhalt['user_from_nick'], $text);
					$text = str_replace("%po_titel_antwort%",
						$inhalt['po_titel_antwort'], $text);
					$text = str_replace("%po_ts_antwort%",
						$inhalt['po_ts_antwort'], $text);
					system_msg("", 0, $an_u_id, $system_farbe, $text);
					break;
			}
			break;
		
		case "Chat-Mail":
		// Nachricht erzeugen
			switch ($a_was) {
				case "Freunde":
				// Nachricht von Login/Logoff erzeugen
					if ($inhalt['aktion'] == "Login" && $inhalt[raum]) {
						$betreff = str_replace("%u_nick%", $u_nick,
							$t['freunde3']);
						$betreff = str_replace("%raum%", $inhalt['raum'],
							$betreff);
					} elseif ($inhalt[aktion] == "Login") {
						$betreff = str_replace("%u_nick%", $u_nick,
							$t['freunde6']);
					} else {
						$betreff = str_replace("%u_nick%", $u_nick,
							$t['freunde4']);
					}
					if ($inhalt['f_text']) {
						$txt = $t['mail9'] . $betreff . " ("
							. $inhalt['f_text'] . ")";
					} else {
						$txt = $t['mail9'] . $betreff;
					}
					// Mail-Absender ist mainChat
					$von_u_id = 0;
					mail_sende($von_u_id, $an_u_id, $txt, $betreff);
					break;
				
				case "Neue Mail":
				// Eine neue Chat-Mail kann keine Chat-Mail auslösen
					break;
				case "Antwort auf eigenen Beitrag":
					$betreff = str_replace("%po_titel%", $inhalt['po_titel'],
						$t['betreff_new_posting']);
					$text = str_replace("%po_titel%", $inhalt['po_titel'],
						$t['msg_new_posting_chatmail']);
					$text = str_replace("%po_ts%", $inhalt['po_ts'], $text);
					$text = str_replace("%forum%", $inhalt['forum'], $text);
					$text = str_replace("%thema%", $inhalt['thema'], $text);
					$text = str_replace("%user_from_nick%",
						$inhalt['user_from_nick'], $text);
					$text = str_replace("%po_titel_antwort%",
						$inhalt['po_titel_antwort'], $text);
					$text = str_replace("%po_ts_antwort%",
						$inhalt['po_ts_antwort'], $text);
					$text = str_replace("%baum%", $inhalt['baum'], $text);
					
					mail_sende($von_u_id, $an_u_id, $text, $betreff);
					break;
			}
			break;
		
		case "E-Mail":
		// Nachricht erzeugen
			switch ($a_was) {
				case "Freunde":
				// Nachricht von Login/Logoff erzeugen
					if ($inhalt['aktion'] == "Login" && $inhalt['raum']) {
						$betreff = str_replace("%u_nick%", $u_nick,
							$t['freunde3']);
						$betreff = str_replace("%raum%", $inhalt['raum'],
							$betreff);
					} elseif ($inhalt['aktion'] == "Login") {
						$betreff = str_replace("%u_nick%", $u_nick,
							$t['freunde6']);
					} else {
						$betreff = str_replace("%u_nick%", $u_nick,
							$t['freunde4']);
					}
					if ($inhalt['f_text']) {
						$txt = $t['mail8'] . $betreff . " ("
							. $inhalt['f_text'] . ")";
					} else {
						$txt = $t['mail8'] . $betreff;
					}
					email_versende($von_u_id, $an_u_id, $txt, $betreff);
					break;
				
				case "Neue Mail":
				// Mail als verschickt markieren
					unset($f);
					$f['m_status'] = "neu/verschickt";
					schreibe_db("mail", $f, $inhalt['m_id'], "m_id");
					
					// Nachricht versenden
					email_versende($inhalt['m_von_uid'], $inhalt['m_an_uid'],
						$t['mail3'] . $inhalt['m_text'], $inhalt['m_betreff']);
					break;
				case "Antwort auf eigenen Beitrag":
					$betreff = str_replace("%po_titel%", $inhalt['po_titel'],
						$t['betreff_new_posting']);
					$text = str_replace("%po_titel%", $inhalt['po_titel'],
						$t['msg_new_posting_email']);
					$text = str_replace("%po_ts%", $inhalt['po_ts'], $text);
					$text = str_replace("%forum%", $inhalt['forum'], $text);
					$text = str_replace("%thema%", $inhalt['thema'], $text);
					$text = str_replace("%user_from_nick%",
						$inhalt['user_from_nick'], $text);
					$text = str_replace("%po_titel_antwort%",
						$inhalt['po_titel_antwort'], $text);
					$text = str_replace("%po_ts_antwort%",
						$inhalt['po_ts_antwort'], $text);
					$text = str_replace("%baum%", $inhalt['baum'], $text);
					
					email_versende($von_u_id, $an_u_id, $text, $betreff);
					break;
			}
			break;
	}
}

function mail_sende($von, $an, $text, $betreff = "") {
	// Verschickt Nachricht von ID $von an ID $an mit Text $text
	global $u_nick, $mysqli_link, $t;
	
	$mailversand_ok = true;
	$fehlermeldung = "";
	
	// Benutzer die die Mailbox zu haben, bekommen keine Aktionen per mainChat
	$query = "SELECT m_id FROM mail WHERE m_von_uid=" . intval($an) . " AND m_an_uid=" . intval($an) . " and m_betreff = 'MAILBOX IST ZU' and m_status != 'geloescht'";
	$result = mysqli_query($mysqli_link, $query);
	$num = mysqli_num_rows($result);
	if ($num >= 1) {
		$mailversand_ok = false;
		$fehlermeldung = $t['chat_msg105'];
	}
	
	// Gesperrte Benutzer bekommen keine Chatmail Aktionen mehr
	$query = "SELECT `u_id` FROM `user` WHERE `u_id`=" . intval($an) . " AND u_level='Z'";
	$result = mysqli_query($mysqli_link, $query);
	$num = mysqli_num_rows($result);
	if ($num >= 1) {
		$mailversand_ok = false;
		$fehlermeldung = "Benutzer ist gesperrt, und kann deswegen keine Chatmail empfangen";
	}
	
	// Mailbombing schutz!
	$query = "SELECT `m_id`, now()-m_zeit AS `zeit` FROM `mail` WHERE `m_von_uid` = " . intval($von) . " AND `m_an_uid` = " . intval($an) . " ORDER BY `m_id` DESC LIMIT 0,1";
	// system_msg("",0,$von,$system_farbe,"DEBUG: $query");
	$result = mysqli_query($mysqli_link, $query);
	
	if (mysqli_num_rows($result) == 1) {
		$a = mysqli_fetch_array($result);
		$zeit = $a['zeit'];
	} else {
		$zeit = 999;
	}
	
	if ($zeit < 30) {
		$mailversand_ok = false;
		$fehlermeldung = $t['chat_msg104'];
	}
	
	if ($mailversand_ok == true) {
		$f['m_von_uid'] = $von;
		$f['m_an_uid'] = $an;
		$f['m_status'] = "neu";
		$f['m_text'] = chat_parse($text);
		if ($betreff) {
			$f['m_betreff'] = $betreff;
		} else {
			// Betreff aus Text übernehmen und kürzen
			$f['m_betreff'] = $text;
			if (strlen($f['m_betreff']) > 5) {
				$f['m_betreff'] = substr($f['m_betreff'], 0, 30);
				$f['m_betreff'] = substr($f['m_betreff'], 0,
					strrpos($f['m_betreff'], " ") + 1);
			}
			;
		}
		;
		
		$f['m_id'] = schreibe_db("mail", $f, "", "m_id");
		
		// Nachricht über neue E-Mail sofort erzeugen
		if (ist_online($an)) {
			aktion("Sofort/Online", $an, $u_nick, "", "Neue Mail", $f);
		} else {
			aktion("Sofort/Offline", $an, $u_nick, "", "Neue Mail", $f);
		}
		;
		
	}
	if (!isset($f['m_id']))
		$f['m_id'] = "";
	$ret = array($f['m_id'], $fehlermeldung);
	return ($ret);
	
}

function email_versende(
	$von_user_id,
	$an_user_id,
	$text,
	$betreff,
	$an_u_email = FALSE)
{
	
	// Versendet "echte" E-Mail an Benutzer mit an_user_id
	// Falls an_u_email=TRUE wird Mail an u_email Adressen verschickt,
	// sonst an u_adminemail (interne Adresse)
	
	global $chat, $mysqli_link, $t;
	
	// Umwandlung der Entities rückgängig machen, Slashes und Tags entfernen
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	$text = str_replace("<ID>", "", $text);
	$text = strip_tags(strtr($text, $trans));
	$betreff = strip_tags(strtr($betreff, $trans));
	
	// Absender ermitteln
	$query = "SELECT `u_email`, `u_nick` FROM `user` WHERE `u_id`=" . intval($von_user_id);
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$abrow = mysqli_fetch_object($result);
	}
	mysqli_free_result($result);
	
	// Empfänger ermitteln und E-Mail versenden, Footer steht in $t[mail4]
	$query = "SELECT `u_adminemail`, `u_email`, `u_nick` FROM `user` WHERE `u_id`=" . intval($an_user_id);
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_object($result);
		
		// Empfänger
		if ($an_u_email && $row->u_email) {
			$adresse = $row->u_email;
		} else {
			$adresse = $row->u_adminemail;
		}
		
		// Absender
		if ($abrow->u_email == "") {
			$absender = $chat . " <" . $adresse . ">";
		} else {
			$absender = $abrow->u_nick . " <" . $abrow->u_email . ">";
		}
		// E-Mail versenden
		if($smtp_on) {
			mailsmtp($adresse, $betreff, str_replace("%user%", $row->u_nick, $text) . $t['mail4'], $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
		} else {
			mail($adresse, $betreff, str_replace("%user%", $row->u_nick, $text) . $t['mail4'], "From: $absender\nReply-To: $absender\n");
		}
		
		mysqli_free_result($result);
		return (TRUE);
	} else {
		mysqli_free_result($result);
		return (FALSE);
	}
}

function freunde_online($u_id, $u_nick, $id, $nachricht = "OLM")
{
	// Sind Freunde des Benutzers online?
	// $u_id ist die ID des des Benutzers
	// $nachricht ist die Art, wie die Nachricht verschickt wird (E-Mail, Chat-Mail, OLM)
	
	global $mysqli_link, $system_farbe, $communityfeatures, $t, $o_js, $whotext;
	
	$query = "SELECT f_id,f_text,f_userid,f_freundid,f_zeit FROM freunde WHERE f_userid=" . intval($u_id) . " AND f_status = 'bestaetigt' "
		. "UNION "
		. "SELECT f_id,f_text,f_userid,f_freundid,f_zeit FROM freunde WHERE f_freundid=" . intval($u_id) . " AND f_status = 'bestaetigt' "
		. "ORDER BY f_zeit desc ";
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		
		$txt = "";
		$i = 0;
		while ($row = mysqli_fetch_object($result)) {
			
			// Benutzer aus DB lesen
			if ($row->f_userid != $u_id) {
				$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id,"
					. "date_format(u_login,'%d.%m.%y %H:%i') as login, "
					. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online "
					. "from user, online " . "WHERE o_user=u_id "
					. "AND u_id=$row->f_userid ";
			} elseif ($row->f_freundid != $u_id) {
				$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id,"
					. "date_format(u_login,'%d.%m.%y %H:%i') as login, "
					. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online "
					. "from user,online " . "WHERE o_user=u_id "
					. "AND u_id=$row->f_freundid ";
			}
			// system_msg("",0,$u_id,$system_farbe,"DEBUG: $nachricht, $query");
			$result2 = mysqli_query($mysqli_link, $query);
			if ($result2 && mysqli_num_rows($result2) > 0) {
				
				// Nachricht erzeugen
				$row2 = mysqli_fetch_object($result2);
				switch ($nachricht) {
					
					case "OLM":
					// Onlinenachricht an u_id versenden
						if ($i > 0)
							$txt .= "<br>";
						
						$q2 = "SELECT r_name,o_id,o_who,"
							. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online_zeit "
							. "FROM online left join raum on r_id=o_raum WHERE o_user=$row2->u_id ";
						$r2 = mysqli_query($mysqli_link, $q2);
						if ($r2 && mysqli_num_rows($r2) > 0) {
							$r = mysqli_fetch_object($r2);
							if ($r->o_who == 0) {
								$raum = $t['chat_msg67'] . " <b>" . $r->r_name
									. "</b>";
							} else {
								$raum = "<b>[" . $whotext[$r->o_who] . "]</b>";
							}
						}
						mysqli_free_result($r2);
						
						$weiterer = $t['chat_msg90'];
						$weiterer = str_replace("%u_nick%",
							zeige_userdetails($row2->u_id, $row2, TRUE, "&nbsp;", $row2->online, "", FALSE), $weiterer);
						$weiterer = str_replace("%raum%", $raum, $weiterer);
						
						$txt .= $weiterer;
						
						if ($row->f_text)
							$txt .= " (" . $row->f_text . ")";
						break;
					
					case "Chat-Mail":
					// Chat-Mail an u_id versenden
						if ($i > 0)
							$txt .= "\n\n";
						$txt .= str_replace("%u_nick%", $row2->u_nick, $t['chat_msg91']);
						$txt = str_replace("%online%",
							gmdate("H:i:s", $row2->online), $txt);
						if ($row->f_text)
							$txt .= " (" . $row->f_text . ")";
						break;
					
					case "E-Mail":
					// E-Mail an u_id versenden
						if ($i > 0)
							$txt .= "\n\n";
						$txt .= str_replace("%u_nick%", $row2->u_nick, $t['chat_msg91']);
						$txt = str_replace("%online%",
							gmdate("H:i:s", $row2->online), $txt);
						if ($row->f_text)
							$txt .= " (" . $row->f_text . ")";
						break;
				}
				$i++;
			}
		}
		
		// Nachricht versenden
		if ($i != 0) {
			switch ($nachricht) {
				
				case "OLM":
					system_msg("", 0, $u_id, $system_farbe, $txt);
					break;
				
				case "Chat-Mail":
					$betreff = str_replace("%anzahl%", $i, $t['mail6']);
					mail_sende(0, $u_id,
						str_replace("%user%", $u_nick, $t['mail5']) . $txt,
						$betreff);
					break;
				
				case "E-Mail":
					$betreff = str_replace("%anzahl%", $i, $t['mail6']);
					email_versende("", $u_id, $t['mail5'] . $txt, $betreff);
					break;
				
			}
		}
		
	}
	mysqli_free_result($result);
}

//prüft ob neue Antworten auf eigene Beiträge 
//vorhanden sind und benachrichtigt entsprechend
function postings_neu($an_u_id, $u_nick, $id, $nachricht) {
	global $mysqli_link, $t, $system_farbe;
	
	//schon gelesene Beiträge des Benutzers holen
	$sql = "SELECT `u_gelesene_postings` FROM `user` WHERE `u_id` = " . intval($an_u_id);
	$query = mysqli_query($mysqli_link, $sql);
	if (mysqli_num_rows($query) > 0) {
		$gelesene = mysqli_result($query, 0, "u_gelesene_postings");
	}
	$u_gelesene = unserialize($gelesene);
	mysqli_free_result($query);
	
	//alle eigenen Beiträge des Benutzers mit allen Antworten 
	//und dem Themenbaum holen
	//die RegExp matcht auf die Beitrags-ID im Feld Themenorder
	//entweder mit vorher und nachher keiner Zahl (damit z.B. 32
	//in 131,132,133 nicht matcht) oder am Anfang oder Ende
	
	//	$sql = "select a.po_id as po_id_own, a.po_th_id as po_th_id,
	//		a.po_titel as po_titel_own,
	//		date_format(from_unixtime(a.po_ts), '%d.%m.%Y %H:%i') as po_date_own,
	//		th_name, fo_name,
	//		b.po_id as po_id_reply, b.po_u_id as po_u_id_reply,
	//		b.po_titel as po_titel_reply,
	//		date_format(from_unixtime(b.po_ts), '%d.%m.%Y %H:%i') as po_date_reply,
	//		u_nick, 
	//		c.po_threadorder as threadord, c.po_id as po_id_thread
	//		from posting a, posting b, thema, forum, user 
	//		left join posting c on c.po_threadorder REGEXP concat(\"(^|[^0-9])\",a.po_id,\"($|[^0-9])\") 
	//		where a.po_u_id = $an_u_id 
	//		and a.po_id = b.po_vater_id 
	//		and a.po_th_id = th_id 
	//		and fo_id=th_fo_id 
	//		and b.po_u_id = u_id 
	//		and a.po_u_id <> b.po_u_id";
	
	// Vereinfachter Query ohne Left join, ist nun viel viel schneller. Fehlende Felder werden über zwei weitere Queries gesucht.
	$sql = "
		select a.po_id as po_id_own, a.po_th_id as po_th_id,
		a.po_titel as po_titel_own,
		date_format(from_unixtime(a.po_ts), '%d.%m.%Y %H:%i') as po_date_own,
		th_name, fo_name,
		b.po_id as po_id_reply, b.po_u_id as po_u_id_reply,
		b.po_titel as po_titel_reply,
		date_format(from_unixtime(b.po_ts), '%d.%m.%Y %H:%i') as po_date_reply,
		u_nick, a.po_threadorder as threadord, a.po_id as po_id_thread
		from posting a, posting b, thema, forum, user 
		where a.po_u_id = " . intval($an_u_id) . "
		and a.po_id = b.po_vater_id 
		and a.po_th_id = th_id 
		and fo_id=th_fo_id 
		and b.po_u_id = u_id 
		and a.po_u_id <> b.po_u_id
		";
	
	$query = mysqli_query($mysqli_link, $sql);
	while ($postings = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		
		//falls posting noch nicht gelesen ist es neu
		if (is_array($u_gelesene[$postings['po_th_id']])) {
			if (!in_array($postings['po_id_reply'],
				$u_gelesene[$postings['po_th_id']])) {
				$poid = $postings['po_id_own'];
				$postings['po_id_thread'] = suche_vaterposting($poid);
				$postings['threadord'] = suche_threadord(
					$postings['po_id_thread']);
				
				//Nachricht versenden
				switch ($nachricht) {
					case "OLM":
						$text = str_replace("%po_titel%",
							$postings['po_titel_own'],
							$t['msg_new_posting_olm']);
						$text = str_replace("%po_ts%",
							$postings['po_date_own'], $text);
						$text = str_replace("%forum%", $postings['fo_name'],
							$text);
						$text = str_replace("%thema%", $postings['th_name'],
							$text);
						$text = str_replace("%user_from_nick%",
							$postings['u_nick'], $text);
						$text = str_replace("%po_titel_antwort%",
							$postings['po_titel_reply'], $text);
						$text = str_replace("%po_ts_antwort%",
							$postings['po_date_reply'], $text);
						
						system_msg("", 0, $an_u_id, $system_farbe, $text);
						break;
					case "Chat-Mail":
						if ($postings['threadord']) {
							$baum = erzeuge_baum($postings['threadord'],
								$postings['po_id_own'],
								$postings['po_id_thread']);
						} else {
							
							$baum = $postings['po_titel_own'] . " -> "
								. $postings['po_titel_reply'];
							
						}
						$betreff = str_replace("%po_titel%",
							$postings['po_titel_own'],
							$t['betreff_new_posting']);
						$text = str_replace("%po_titel%",
							$postings['po_titel_own'],
							$t['msg_new_posting_chatmail']);
						$text = str_replace("%po_ts%",
							$postings['po_date_own'], $text);
						$text = str_replace("%forum%", $postings['fo_name'],
							$text);
						$text = str_replace("%thema%", $postings['th_name'],
							$text);
						$text = str_replace("%user_from_nick%",
							$postings['u_nick'], $text);
						$text = str_replace("%po_titel_antwort%",
							$postings['po_titel_reply'], $text);
						$text = str_replace("%po_ts_antwort%",
							$postings['po_date_reply'], $text);
						$text = str_replace("%baum%", $baum, $text);
						mail_sende($postings['po_u_id_reply'], $an_u_id, $text,
							$betreff);
						break;
					case "E-Mail":
						if ($postings['threadord']) {
							$baum = erzeuge_baum($postings['threadord'],
								$postings['po_id_own'],
								$postings['po_id_thread']);
						} else {
							
							$baum = $postings['po_titel_own'] . " -> "
								. $postings['po_titel_reply'];
							
						}
						
						$betreff = str_replace("%po_titel%",
							$postings['po_titel_own'],
							$t['betreff_new_posting']);
						$text = str_replace("%po_titel%",
							$postings['po_titel_own'],
							$t['msg_new_posting_email']);
						$text = str_replace("%po_ts%",
							$postings['po_date_own'], $text);
						$text = str_replace("%forum%", $postings['fo_name'],
							$text);
						$text = str_replace("%thema%", $postings['th_name'],
							$text);
						$text = str_replace("%user_from_nick%",
							$postings['u_nick'], $text);
						$text = str_replace("%po_titel_antwort%",
							$postings['po_titel_reply'], $text);
						$text = str_replace("%po_ts_antwort%",
							$postings['po_date_reply'], $text);
						$text = str_replace("%baum%", $baum, $text);
						email_versende($postings['po_u_id_reply'], $an_u_id,
							$text, $betreff);
						break;
					
				}
				
			}
		} // endif is_array
	}
	mysqli_free_result($query);
	
}

function erzeuge_baum($threadorder, $po_id, $thread)
{
	
	global $mysqli_link;
	
	$in_stat = $thread . "," . $threadorder;
	
	//erst mal alle Beiträge des Themas holen
	$sql = "select po_id, po_titel, po_vater_id
				from posting
				where po_id in ($in_stat)";
	$query = mysqli_query($mysqli_link, $sql);
	
	//alle postings in feld einlesen
	$arr_postings = array();
	while ($posting = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		
		$arr_postings[$posting['po_id']]['vater'] = $posting['po_vater_id'];
		$arr_postings[$posting['po_id']]['titel'] = $posting['po_titel'];
	}
	
	$arr_baum = array();
	//vom Beitrag ausgehend zurück zur Wurzel
	array_unshift($arr_baum, $po_id);
	
	$vater = $arr_postings[$po_id]['vater'];
	while ($vater > 0) {
		array_unshift($arr_baum, $vater);
		$vater = $arr_postings[$vater]['vater'];
	}
	
	$baum = "";
	foreach ($arr_baum as $key => $id) {
		
		if ($key == 0)
			$baum = $arr_postings[$id]['titel'];
		else $baum .= " -> " . $arr_postings[$id]['titel'];
		
	}
	
	return $baum;
	
}

function erzeuge_fuss($text)
{
	//generiert den Fuss eines Beitrags (Signatur)
	
	global $t, $u_id, $mysqli_link;
	
	$query = "SELECT `u_signatur`, `u_nick` FROM `user` WHERE `u_id`=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_object($result);
	}
	;
	mysqli_free_result($result);
	
	if (!$row->u_signatur) {
		$sig = "\n\n-- \n  " . $t['gruss'] . "\n  " . $row->u_nick;
	} else {
		$sig = "\n\n-- \n  " . $row->u_signatur;
	}
	$sig = htmlspecialchars($sig); //sec 
	return $text . $sig;
	
}

function word_wrap(
	$org_message,
	$cols = "85",
	$returns = "AUTO",
	$spacer = "",
	$joiner = ' ')
{
	/*
	 Verbesserte Word-wrap funktion, die auch Zeile zusammenfügt (wenn nötig)
	  $org_message = Originaltext
	  $cols = Anzahl Zeichen pro Zeile
	  $cut  = Trennzeichen
	  $returns = hier kann angegeben werden, welches Zeichen als Umbruch verwendet werden soll
	  $spacer = zusätzliches Zeichen was beim Treenzeichen noch angehängt werden soll
	  $joiner = Zeichen welches zum zusammenfügen von Zeilen verwendet werden soll
	 */
	
	if ($returns == 'AUTO') {
		$MS = substr_count($org_message, "\r\n");
		$Linux = substr_count($org_message, "\n");
		$Mac = substr_count($org_message, "\r");
		if ($MS >= $Linux && $MS >= $Mac) {
			$returns = "\r\n";
		} elseif ($Linux > $Mac) {
			$returns = "\n";
		} elseif ($Linux < $Mac) {
			$returns = "\r";
		}
	}
	
	$message = explode($returns, $org_message);
	$msg_lines = count($message);
	$removed = array('');
	$open = 0;
	for ($a = 0; $a < $msg_lines; $a++) {
		$line = $message[$a];
		$lenght = strlen($line);
		$new_line = '';
		$num = 0;
		$changed = 0;
		if ($lenght > $cols) {
			for ($b = 0; $b <= $lenght; $b++) {
				$addon = '';
				switch ($line[$b]) {
					
					case '<':
						$open++;
						break;
					
					case '>':
						if ($open > 0) {
							$open--;
						}
						break;
					
					case ' ':
						if ($open == 0 && $num >= $cols) {
							$addon = $returns;
							$chnaged = 1;
							$num = 0;
						} elseif ($open == 0 && $b == strrpos($line, ' ')
							&& $chnaged == 0) {
							$addon = $returns;
							$num = 0;
						}
						break;
				}
				$num++;
				if ($addon) {
					$new_line .= $addon . $spacer;
				} else {
					$new_line .= $line[$b];
				}
			}
		} else {
			$new_line = $line;
		}
		
		if ($message[$a] != $new_line) {
			$message[$a] = $new_line . $joiner . $message[$a + 1];
			if ($message[$a + 1] == '') {
				$message[$a] .= $returns;
			}
			$message[$a + 1] = '';
			$removed[] = $a + 1;
		}
		
	}
	$new_message = "";
	for ($a = 0; $a < count($message); $a++) {
		if (!array_search($a, $removed)) {
			$new_message .= $message[$a] . $returns;
		}
	}
	return $new_message;
}

function erzeuge_umbruch($text, $breite)
{
	//bricht text nach breite zeichen um
	
	if (strstr($text, "&gt;&nbsp;")) {
		// Alte Version für Quotes beibehalten
		//text in feld einlesen mit \n als trennzeichen
		$arr_text = explode("\n", $text);
		
		//fuer jeden Feldeintrag Zeilenumbruch hinzufuegen
		while (list($k, $zeile) = @each($arr_text)) {
			//nur Zeilen umbrechen die nicht gequotet sind
			if (!strstr($zeile, "&gt;&nbsp;")) {
				$arr_text[$k] = wordwrap($zeile, $breite, "\n", 0);
			}
		}
		
		return implode("\n", $arr_text);
	} else {
		// neue version, verhaut die umbrüche nicht mehr
		$text = word_wrap($text, $breite, $returns = "AUTO", $spacer = "",
			$joiner = ' ');
		return ($text);
	}
	
}

function erzeuge_quoting($text, $autor, $date) {
	//fügt > vor die zeilen und fügt zu beginn xxx schrieb am xxx an
	
	global $t;
	
	$kopf = $t['kopfzeile'];
	$kopf = str_replace("{autor}", $autor, $kopf);
	$kopf = str_replace("{date}", $date, $kopf);
	
	$arr_zeilen = explode("\n", $text);
	
	while (list($k, $zeile) = @each($arr_zeilen)) {
		
		$arr_zeilen[$k] = "&gt;&nbsp;" . $zeile;
	}
	
	return $kopf . "\n" . implode("\n", $arr_zeilen);
	
}
?>