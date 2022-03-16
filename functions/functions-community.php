<?php
function suche_vaterposting($poid) {
	// Diese Funktion sucht das Vaterposting des übergebenen Beitrags
	$query = pdoQuery("SELECT `beitrag_thema_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$poid]);
	
	list($vp) = $query->fetch();
	
	return ($vp);
}

function mail_neu($u_id, $u_nick, $nachricht = "OLM") {
	// Hat der Benutzer neue Chat-Mail?
	// $u_id ist die ID des des Benutzers
	// $nachricht ist die Art, wie die Nachricht verschickt wird (E-Mail, Chat-Mail, OLM)
	
	global $system_farbe, $lang, $chat;
	
	$query = pdoQuery("SELECT mail.*, date_format(`m_zeit`,'%d.%m.%y um %H:%i') AS `zeit`, `u_nick` FROM `mail` LEFT JOIN `user` ON `m_von_uid` = `u_id` WHERE `m_an_uid` = :m_an_uid AND `m_status` = 'neu' ORDER BY `m_zeit` DESC", [':m_an_uid'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		// Sonderfall OLM: "Sie haben neue Nachrichten..." ausgeben.
		if ($nachricht == "OLM") {
			$url = "href=\"inhalt.php?bereich=nachrichten\" target=\"_blank\"";
			system_msg("", 0, $u_id, $system_farbe, str_replace("%link%", $url, $lang['chatmsg_mail1']));
		}
		
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			// Nachricht verschicken
			switch ($nachricht) {
				case "OLM":
					$txt = str_replace("%zeit%", $row['zeit'], $lang['chatmsg_mail2']);
					if ($row['u_nick'] != "NULL" && $row['u_nick'] != "") {
						$txt = str_replace("%nick%", $row['u_nick'], $txt);
					} else {
						$txt = str_replace("%nick%", $chat, $txt);
					}
					system_msg("", 0, $u_id, $system_farbe, str_replace("%betreff%", html_entity_decode($row['m_betreff']), $txt));
					break;
				
				case "E-Mail":
				// Mail als verschickt markieren
					unset($f);
					$f['m_status'] = "neu/verschickt";
					$f['m_zeit'] = $row['m_zeit'];
					
					pdoQuery("UPDATE `mail` SET `m_status` = :m_status, `m_zeit` = :m_zeit WHERE `m_id` = :m_id",
						[
							':m_id'=>$row['m_id'],
							':m_status'=>$f['m_status'],
							':m_zeit'=>$f['m_zeit']
							
						]);
					
					// E-Mail an u_id versenden
					email_versende($row['m_von_uid'], $u_id, $lang['email_mail3'] . $row['f_text'], $row['m_betreff']);
					break;
			}
		}
		
	}
}

function profil_neu($u_id, $u_nick) {
	// Hat der Benutzer sein Profil ausgefüllt?
	// Falls nein, wird einer Erinnerung ausgegeben
	// $u_id ist die ID des des Benutzers
	global $system_farbe, $lang;
	
	$query = pdoQuery("SELECT `ui_id` FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		$url = "href=\"inhalt.php?bereich=profil&aktion=neu\" ";
		system_msg("", 0, $u_id, $system_farbe, str_replace("%link%", $url, $lang['profil1']));
	}
}

function punkte($anzahl, $o_id, $user_id = 0, $text = "", $sofort = FALSE) {
	// Addiert/Subtrahiert $anzahl Punkte auf das Punktekonto des Benutzers $o_id/$user_id
	// Dieser Benutzer muss online sein, die punkte werden in der Tabelle online addiert
	// Falls $text Zeichen enthält, wird der Text mit einem Standardtext ausgegeben
	
	global $lang, $o_punkte, $punkte_gruppe;
	
	// In die Datenbank schreiben
	if ($anzahl > 0 || $anzahl < 0) {
		$o_punkte = pdoQuery("SELECT `o_punkte` FROM `online` WHERE `o_id` = :o_id", [':o_id'=>$o_id])->fetch()['o_punkte'];
		$o_punkte = $o_punkte + $anzahl;
		
		pdoQuery("UPDATE `online` SET `o_punkte` = :o_punkte WHERE `o_id` = :o_id", [':o_punkte'=>$o_punkte, ':o_id'=>$o_id]);
	}
	
	// Meldung an Benutzer ausgeben
	if (strlen($text) > 0) {
		if ($anzahl > 0) {
			// Gutschrift
			$text = str_replace("%text%", $text, $lang['punkte1']);
			$text = str_replace("%punkte%", $anzahl, $text);
		} else {
			// Abzug
			$text = str_replace("%text%", $text, $lang['punkte2']);
			$text = str_replace("%punkte%", $anzahl * (-1), $text);
		}
		if ($user_id != 0) {
			system_msg("", 0, $user_id, (isset($system_farbe) ? $system_farbe : ""), $text);
		}
	}
	
	// Optional Punkte sofort in Benutzerdaten übertragen
	if ($sofort && $o_id) {
		// Tabellen online+user exklusiv locken
		pdoQuery("LOCK TABLES `online` WRITE, `user` WRITE", []);
		
		// Aktuelle Punkte auf Punkte in Benutzertabelle addieren
		$query = pdoQuery("SELECT `o_punkte`, `o_user` FROM `online` WHERE `o_id` = :o_id", [':o_id'=>intval($o_id)]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$row = $query->fetch();
			$user_id = $row['o_user'];
			
			// Es können maximal die Punkte abgezogen werden, die man auch hat
			$query = pdoQuery("SELECT `u_punkte_gesamt`, `u_punkte_jahr`, `u_punkte_monat` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				$result = $query->fetch();
				$p_gesamt = $result['u_punkte_gesamt'] + $row['o_punkte'];
				$p_jahr = $result['u_punkte_jahr'] + $row['o_punkte'];
				$p_monat = $result['u_punkte_monat'] + $row['o_punkte'];
				
				if ($p_gesamt < 0) {
					$p_gesamt = 0;
				}
				if ($p_jahr < 0) {
					$p_jahr = 0;
				}
				if ($p_monat < 0) {
					$p_monat = 0;
				}
			}
			
			pdoQuery("UPDATE `user` SET `u_punkte_monat` = :u_punkte_monat, `u_punkte_jahr` = :u_punkte_jahr, `u_punkte_gesamt` = :u_punkte_gesamt WHERE `u_id` = :u_id",
				[
					':u_punkte_monat'=>$p_monat,
					':u_punkte_jahr'=>$p_jahr,
					':u_punkte_gesamt'=>$p_gesamt,
					':u_id'=>$user_id
				]);
			
			pdoQuery("UPDATE `online` SET `o_punkte` = 0 WHERE `o_id` = :o_id", [':o_id'=>$o_id]);
		}
		
		// Gruppe neu berechnen
		unset($f);
		$f['u_punkte_gruppe'] = 0;
		
		$query = pdoQuery("SELECT `u_punkte_gesamt` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$u_punkte_gesamt = $result['u_punkte_gesamt'];
			
			foreach ($punkte_gruppe as $key => $value) {
				if ($u_punkte_gesamt < $value) {
					break;
				} else {
					$f['u_punkte_gruppe'] = dechex($key);
				}
			}
		}
		
		pdoQuery("UPDATE `user` SET `u_punkte_gruppe` = :u_punkte_gruppe WHERE `u_id` = :u_id",
			[
				':u_id'=>$user_id,
				':u_punkte_gruppe'=>$f['u_punkte_gruppe']
			]);
		aktualisiere_inhalt_online($user_id);
		
		// Lock freigeben
		pdoQuery("UNLOCK TABLES", []);
	}
}

function punkte_offline($anzahl, $user_id) {
	// Addiert/Subtrahiert $anzahl Punkte auf das Punktekonto des Benutzers $user_id
	// Die Punkte werden direkt in die user-tabelle geschrieben
	// Optional wird Info-Text als Ergebnis zurückgeliefert
	
	global $lang, $punkte_gruppe;
	
	// In die Datenbank schreiben
	// Tabellen online+user exklusiv locken
	pdoQuery("LOCK TABLES `online` WRITE, `user` WRITE", []);
	
	// Aktuelle Punkte auf Punkte in Benutzertabelle addieren
	if ($user_id && ($anzahl > 0 || $anzahl < 0)) {
		// Es können maximal die Punkte abgezogen werden, die man auch hat
		$query = pdoQuery("SELECT `u_punkte_gesamt`, `u_punkte_jahr`, `u_punkte_monat` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$p_gesamt = $result['u_punkte_gesamt'] + $anzahl;
			$p_jahr = $result['u_punkte_jahr'] + $anzahl;
			$p_monat = $result['u_punkte_monat'] + $anzahl;
			
			if ($p_gesamt < 0) {
				$p_gesamt = 0;
			}
			if ($p_jahr < 0) {
				$p_jahr = 0;
			}
			if ($p_monat < 0) {
				$p_monat = 0;
			}
		}
		
		pdoQuery("UPDATE `user` SET `u_punkte_monat` = :u_punkte_monat, `u_punkte_jahr` = :u_punkte_jahr, `u_punkte_gesamt` = :u_punkte_gesamt WHERE `u_id` = :u_id",
			[
				':u_punkte_monat'=>$p_monat,
				':u_punkte_jahr'=>$p_jahr,
				':u_punkte_gesamt'=>$p_gesamt,
				':u_id'=>$user_id
			]);
	}
	
	// Gruppe neu berechnen
	unset($f);
	$f['u_punkte_gruppe'] = 0;
	
	$query = pdoQuery("SELECT `u_punkte_gesamt`, `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$result = $query->fetch();
		$u_punkte_gesamt = $result['u_punkte_gesamt'];
		$u_nick = $result['u_nick'];
		foreach ($punkte_gruppe as $key => $value) {
			if ($u_punkte_gesamt < $value) {
				break;
			} else {
				$f['u_punkte_gruppe'] = dechex($key);
			}
		}
	}
	
	pdoQuery("UPDATE `user` SET `u_punkte_gruppe` = :u_punkte_gruppe WHERE `u_id` = :u_id",
		[
			':u_id'=>$user_id,
			':u_punkte_gruppe'=>$f['u_punkte_gruppe']
		]);
	aktualisiere_inhalt_online($user_id);
	
	// Lock freigeben
	pdoQuery("UNLOCK TABLES", []);
	
	// Meldung an Benutzer ausgeben
	if ($anzahl > 0) {
		// Gutschrift
		$text = str_replace("%user%", $u_nick, $lang['punkte21']);
		$text = str_replace("%punkte%", $anzahl, $text);
	} else {
		// Abzug
		$text = str_replace("%user%", $u_nick, $lang['punkte22']);
		$text = str_replace("%punkte%", $anzahl * (-1), $text);
	}
	
	return ($text);
	
}

function aktion($user_id, $typ, $an_u_id, $u_nick, $suche_was = "", $inhalt = "") {
	// Programmierbare Aktionen für Benutzer $an_u_id von Benutzer $u_nick
	// Verschickt eine Nachricht (Aktion) zum Login, Raumwechsel, Sofort oder alle 5 Minuten aus (a_wann)
	// Die Aktion ist Mail (Chatintern), E-Mail an die Adresse des Benutzers oder eine Online-Message (a_wie)
	// Die betroffene Chat-Funktion (zb. Freund-Login/Logout, Mailempfang) wird als Text definiert (a_was)
	// typ = "Sofort/Offline", "Sofort/Online", "Login", "Alle 5 Minuten"
	// Mit der Angabe von $suche_was kann die Suche auf ein a_was eingeschränkt werden
	// Für "Sofort/Offline" und "Sofort/Online" muss der Inhalt in $inhalt übergeben werden
	
	// Einstellungen aus DB in Array a_was merken und dabei SETs auflösen
	// Mögliche a_wann: Sofort/Offline, Sofort/Online, Login, Alle 5 Minuten
	if ($suche_was != "") {
		$query = pdoQuery("SELECT `a_was`, `a_wie` FROM `aktion` WHERE `a_user` = :a_user AND `a_wann` = :a_wann AND `a_was` = :a_was",
			[
				':a_user'=>$an_u_id,
				':a_wann'=>$typ,
				':a_was'=>$suche_was
			]);
	} else {
		$query = pdoQuery("SELECT `a_was`, `a_wie` FROM `aktion` WHERE `a_user` = :a_user AND `a_wann` = :a_wann",
			[
				':a_user'=>$an_u_id,
				':a_wann'=>$typ
			]);
	}
	
	$resultCount = $query->rowCount();
	if ($resultCount != 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$wie = explode(",", $row['a_wie']);
			foreach ($wie as $a_wie) {
				$a_was[$row['a_was']][$a_wie] = TRUE;
			}
		}
	}
	
	switch ($typ) {
		case "Sofort/Offline":
		case "Sofort/Online":
			if ($suche_was != "" && isset($a_was)
				&& is_array($a_was[$suche_was])) {
				foreach ($a_was[$suche_was] as $wie => $was) {
					aktion_sende($suche_was, $wie, $inhalt, $an_u_id, $user_id, $u_nick);
				}
			}
			break;
		
		case "Alle 5 Minuten":
		// Aktionen ausführen
			if (isset($a_was["Freunde"]["OLM"])) {
				freunde_online($an_u_id, $u_nick, "OLM");
			}
			if (isset($a_was["Freunde"]["Chat-Mail"])) {
				freunde_online($an_u_id, $u_nick, "Chat-Mail");
			}
			if (isset($a_was["Freunde"]["E-Mail"])) {
				freunde_online($an_u_id, $u_nick, "E-Mail");
			}
			
			if (isset($a_was["Neue Mail"]["OLM"])) {
				mail_neu($an_u_id, $u_nick, "OLM");
			}
			if (isset($a_was["Neue Mail"]["E-Mail"])) {
				mail_neu($an_u_id, $u_nick, "E-Mail");
			}
			
			if (isset($a_was["Antwort auf eigenen Beitrag"]["OLM"])) {
				postings_neu($an_u_id, $u_nick, "OLM");
			}
			if (isset($a_was["Antwort auf eigenen Beitrag"]["Chat-Mail"])) {
				postings_neu($an_u_id, $u_nick, "Chat-Mail");
			}
			if (isset($a_was["Antwort auf eigenen Beitrag"]["E-Mail"])) {
				postings_neu($an_u_id, $u_nick, "E-Mail");
			}
			
			// Merken, wann zuletzt die Aktionen ausgeführt wurden
			pdoQuery("UPDATE `online` SET `o_aktion` = :o_aktion WHERE `o_user` = :o_user", [':o_aktion'=>time(), ':o_user'=>$an_u_id]);
			
			break;
		
		case "Login":
		default:
		// Aktionen ausführen
			if (isset($a_was["Freunde"]["OLM"])) {
				freunde_online($an_u_id, $u_nick, "OLM");
			}
			if (isset($a_was["Freunde"]["Chat-Mail"])) {
				freunde_online($an_u_id, $u_nick, "Chat-Mail");
			}
			if (isset($a_was["Freunde"]["E-Mail"])) {
				freunde_online($an_u_id, $u_nick, "E-Mail");
			}
			
			if (isset($a_was["Neue Mail"]["OLM"])) {
				mail_neu($an_u_id, $u_nick, "OLM");
			}
			if (isset($a_was["Neue Mail"]["E-Mail"])) {
				mail_neu($an_u_id, $u_nick, "E-Mail");
			}
			
			if (isset($a_was["Antwort auf eigenen Beitrag"]["OLM"])) {
				postings_neu($an_u_id, $u_nick, "OLM");
			}
			if (isset($a_was["Antwort auf eigenen Beitrag"]["Chat-Mail"])) {
				postings_neu($an_u_id, $u_nick, "Chat-Mail");
			}
			if (isset($a_was["Antwort auf eigenen Beitrag"]["E-Mail"])) {
				postings_neu($an_u_id, $u_nick, "E-Mail");
			}
	}
}

function aktion_sende($a_was, $a_wie, $inhalt, $an_u_id, $von_u_id, $u_nick) {
	// Versendet eine Nachricht an Benutzer $an_u_id von $von_u_id/$u_nick
	// Der Inhalt der Nachricht wird in $inhalt übergeben
	// Die Aktion ist Mail (Chatintern), E-Mail an die Adresse des Benutzers oder eine Online-Message (a_wie)
	// Die betroffene Chat-Funktion (zb. Freund-Login/Logout, Mailempfang) wird als Text definiert (a_was)
	
	global $system_farbe, $lang;
	
	$userlink = zeige_userdetails($von_u_id);
	
	switch ($a_wie) {
		case "OLM":
		// Nachricht erzeugen
			switch ($a_was) {
				case "Freunde":
				// Nachricht von Login/Logoff erzeugen
					if ($inhalt['aktion'] == "Login" && $inhalt['raum']) {
						$txt = str_replace("%u_nick%", $userlink, $lang['chatmsg_freunde1']);
						$txt = str_replace("%raum%", $inhalt['raum'], $txt);
					} else if ($inhalt['aktion'] == "Login") {
						$txt = str_replace("%u_nick%", $userlink, $lang['chatmsg_freunde5']);
					} else {
						$txt = str_replace("%u_nick%", $u_nick, $lang['chatmsg_freunde2']);
					}
					if ($inhalt['f_text']) {
						$txt .= " (" . $inhalt['f_text'] . ")";
					}
					system_msg("", 0, $an_u_id, $system_farbe, $txt);
					break;
				
				case "Neue Mail":
				// Nachricht erzeugen
					$txt = str_replace("%nick%", $u_nick, $lang['email_mail7']);
					$txt = str_replace("%betreff%", $inhalt['m_betreff'], $txt);
					
					// Nachricht versenden
					system_msg("", 0, $an_u_id, $system_farbe, $txt);
					break;
				case "Antwort auf eigenen Beitrag":
					$text = str_replace("%beitrag%", $inhalt['beitrag_titel'], $lang['msg_new_posting_olm']);
					$text = str_replace("%zeit%", $inhalt['beitrag_thema_timestamp'], $text);
					$text = str_replace("%forum%", $inhalt['forum'], $text);
					$text = str_replace("%thema%", $inhalt['thema'], $text);
					$text = str_replace("%user_from_nick%", $inhalt['user_from_nick'], $text);
					$text = str_replace("%po_titel_antwort%", $inhalt['po_titel_antwort'], $text);
					$text = str_replace("%po_ts_antwort%", $inhalt['po_ts_antwort'], $text);
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
						$betreff = str_replace("%u_nick%", $u_nick, $lang['nachricht_freunde3']);
						$betreff = str_replace("%raum%", $inhalt['raum'], $betreff);
					} elseif ($inhalt['aktion'] == "Login") {
						$betreff = str_replace("%u_nick%", $u_nick, $lang['nachricht_freunde6']);
					} else {
						$betreff = str_replace("%u_nick%", $u_nick, $lang['nachricht_freunde4']);
					}
					if ($inhalt['f_text']) {
						$txt = $lang['nachricht_mail9'] . $betreff . " (" . $inhalt['f_text'] . ")";
					} else {
						$txt = $lang['nachricht_mail9'] . $betreff;
					}
					// Mail-Absender ist mainChat
					$von_u_id = 0;
					mail_sende($von_u_id, $an_u_id, $txt, $betreff);
					break;
				
				case "Neue Mail":
				// Eine neue Chat-Mail kann keine Chat-Mail auslösen
					break;
				case "Antwort auf eigenen Beitrag":
					$betreff = str_replace("%beitrag%", $inhalt['beitrag_titel'], $lang['betreff_new_posting']);
					$text = str_replace("%beitrag%", $inhalt['beitrag_titel'], $lang['msg_new_posting_chatmail']);
					$text = str_replace("%zeit%", $inhalt['beitrag_thema_timestamp'], $text);
					$text = str_replace("%forum%", $inhalt['forum'], $text);
					$text = str_replace("%thema%", $inhalt['thema'], $text);
					$text = str_replace("%user_from_nick%", $inhalt['user_from_nick'], $text);
					$text = str_replace("%po_titel_antwort%", $inhalt['po_titel_antwort'], $text);
					$text = str_replace("%po_ts_antwort%", $inhalt['po_ts_antwort'], $text);
					
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
						$betreff = str_replace("%u_nick%", $u_nick, $lang['email_freunde3']);
						$betreff = str_replace("%raum%", $inhalt['raum'], $betreff);
					} elseif ($inhalt['aktion'] == "Login") {
						$betreff = str_replace("%u_nick%", $u_nick, $lang['email_freunde6']);
					} else {
						$betreff = str_replace("%u_nick%", $u_nick, $lang['email_freunde4']);
					}
					if ($inhalt['f_text']) {
						$txt = $lang['email_mail8'] . $betreff . " (" . $inhalt['f_text'] . ")";
					} else {
						$txt = $lang['email_mail8'] . $betreff;
					}
					email_versende($von_u_id, $an_u_id, $txt, $betreff);
					break;
				
				case "Neue Mail":
				// Mail als verschickt markieren
					unset($f);
					
					$f['m_status'] = "neu/verschickt";
					
					pdoQuery("UPDATE `mail` SET `m_status` = :m_status WHERE `m_id` = :m_id",
						[
							':m_id'=>$inhalt['m_id'],
							':m_status'=>$f['m_status']
						]);
					
					// Nachricht versenden
					email_versende($inhalt['m_von_uid'], $inhalt['m_an_uid'], $lang['email_mail3'] . $inhalt['f_text'], $inhalt['m_betreff']);
					break;
				case "Antwort auf eigenen Beitrag":
					$betreff = str_replace("%beitrag%", $inhalt['beitrag_titel'], $lang['betreff_new_posting']);
					$text = str_replace("%beitrag%", $inhalt['beitrag_titel'], $lang['msg_new_posting_email']);
					$text = str_replace("%zeit%", $inhalt['beitrag_thema_timestamp'], $text);
					$text = str_replace("%forum%", $inhalt['forum'], $text);
					$text = str_replace("%thema%", $inhalt['thema'], $text);
					$text = str_replace("%user_from_nick%", $inhalt['user_from_nick'], $text);
					$text = str_replace("%po_titel_antwort%", $inhalt['po_titel_antwort'], $text);
					$text = str_replace("%po_ts_antwort%", $inhalt['po_ts_antwort'], $text);
					
					email_versende($von_u_id, $an_u_id, $text, $betreff);
					break;
			}
			break;
	}
}

function mail_sende($von_id, $an_id, $text, $betreff = "") {
	// Verschickt Nachricht von ID $von_id an ID $an_id mit Text $text
	global $u_nick, $lang, $u_id, $pdo;
	
	$mailversand_ok = true;
	$fehlermeldung = "";
	
	// Benutzer welche keine Nachrichten empfangen möchten, bekommen keine Aktionen
	$query = pdoQuery("SELECT `u_nick`, `u_nachrichten_empfangen` FROM `user` WHERE `u_id` = :u_id",
		[
			':u_id'=>$an_id
		]);
	
	$resultCount = $query->rowCount();
	if ($resultCount >= 1) {
		$result = $query->fetch();
		if($result['u_nachrichten_empfangen'] == 0) {
			$mailversand_ok = false;
			$fehlermeldung = str_replace("%nick%", $result['u_nick'], $lang['nachrichten_fehler_keine_nachrichten_empfangen']);
		}
	}
	
	// Gesperrte Benutzer bekommen keine Nachrichten Aktionen mehr
	$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_id` = :u_id AND `u_level` = 'Z'", [':u_id'=>$an_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount >= 1) {
		$mailversand_ok = false;
		$fehlermeldung = $lang['nachrichten_fehler_mailbox_gesperrt'];
	}
	
	// Mailbombingschutz!
	$query = pdoQuery("SELECT `m_id`, now()-m_zeit AS `zeit` FROM `mail` WHERE `m_von_uid` = :m_von_uid AND `m_an_uid` = :m_an_uid ORDER BY `m_id` DESC LIMIT 0,1", [':m_von_uid'=>$von_id, ':m_an_uid'=>$an_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$result = $query->fetch();
		$zeit = $result['zeit'];
	} else {
		$zeit = 999;
	}
	
	if ($zeit < 30) {
		$mailversand_ok = false;
		$fehlermeldung = $lang['chat_msg104'];
	}
	
	if ($mailversand_ok == true) {
		$f['m_status'] = "neu";
		$f['m_von_uid'] = $von_id;
		$f['m_an_uid'] = $an_id;
		$f['f_text'] = chat_parse($text);
		if ($betreff) {
			$f['m_betreff'] = $betreff;
		} else {
			// Betreff aus Text übernehmen und kürzen
			$f['m_betreff'] = $text;
			if (strlen($f['m_betreff']) > 5) {
				$f['m_betreff'] = substr($f['m_betreff'], 0, 30);
				$f['m_betreff'] = substr($f['m_betreff'], 0, strrpos($f['m_betreff'], " ") + 1);
			}
		}
		
		pdoQuery("INSERT INTO `mail` (`m_status`, `m_von_uid`, `m_an_uid`, `m_text`, `m_betreff`) VALUES (:m_status, :m_von_uid, :m_an_uid, :m_text, :m_betreff)",
			[
				':m_status'=>$f['m_status'],
				':m_von_uid'=>$f['m_von_uid'],
				':m_an_uid'=>$f['m_an_uid'],
				':m_text'=>$f['f_text'],
				':m_betreff'=>$f['m_betreff']
			]);
		
		$f['m_id'] = $pdo->lastInsertId();
		
		// Nachricht über neue E-Mail sofort erzeugen
		if (ist_online($an_id)) {
			aktion($u_id, "Sofort/Online", $an_id, $u_nick, "Neue Mail", $f);
		} else {
			aktion($u_id, "Sofort/Offline", $an_id, $u_nick, "Neue Mail", $f);
		}
	}
	if (!isset($f['m_id'])) {
		$f['m_id'] = "";
	}
	$ret = array($f['m_id'], $fehlermeldung);
	return ($ret);
	
}

function email_versende($von_user_id, $an_user_id, $text, $betreff, $an_u_email = FALSE) {
	// Versendet "echte" E-Mail an Benutzer mit an_user_id
	// Falls an_u_email=TRUE wird E-Mail an u_email (E-Mail Adresse)
	
	global $lang;
	
	// Umwandlung der Entities rückgängig machen, Slashes und Tags entfernen
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	$text = strip_tags(strtr($text, $trans));
	$betreff = strip_tags(strtr($betreff, $trans));
	
	// Absender ermitteln
	$query = pdoQuery("SELECT `u_email`, `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>intval($von_user_id)]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$abrow = $query->fetch();
	}
	
	// Empfänger ermitteln und E-Mail versenden
	$query = pdoQuery("SELECT `u_email`, `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>intval($an_user_id)]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$row = $query->fetch();
		
		// Empfänger
		$adresse = $row['u_email'];
		
		$inhalt = str_replace("%user%", $row['u_nick'], $text);
		$nachricht = str_replace("%name%", $abrow['u_nick'], $lang['email_mail4']);
		$nachricht = str_replace("%nachricht%", $text, $nachricht);
		$nachricht = str_replace("%email%", $abrow['u_email'], $nachricht);
		
		// E-Mail versenden
		email_senden($adresse, $betreff, $nachricht);
		
		return (TRUE);
	} else {
		return (FALSE);
	}
}

function freunde_online($u_id, $u_nick, $nachricht = "OLM") {
	// Sind Freunde des Benutzers online?
	// $u_id ist die ID des des Benutzers
	// $nachricht ist die Art, wie die Nachricht verschickt wird (E-Mail, Chat-Mail, OLM)
	
	global $system_farbe, $lang, $whotext, $locale;
	
	$query = pdoQuery("SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_userid` = :f_userid AND `f_status` = 'bestaetigt' "
		. "UNION "
		. "SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_freundid` = :f_freundid AND `f_status` = 'bestaetigt' "
		. "ORDER BY `f_zeit` DESC", [':f_userid'=>intval($u_id), ':f_freundid'=>intval($u_id)]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$txt = "";
		$i = 0;
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			// Benutzer aus DB lesen
			pdoQuery("SET `lc_time_names` = :lc_time_names", [':lc_time_names'=>$locale]);
			
			if ($row['f_userid'] != $u_id) {
				$query2 = pdoQuery("SELECT `u_nick`, `u_id`, `u_level`, `u_nachrichten_empfangen`, `u_punkte_gesamt`, `u_punkte_gruppe`, `o_id`, date_format(`u_login`,'%d. %M %Y um %H:%i') AS `login`, "
					. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_login`) AS `online` FROM `user`, `online` WHERE `o_user` = `u_id` AND `u_id` = :u_id", [':u_id'=>$row['f_userid']]);
				
				$result2Count = $query2->rowCount();
			} else if ($row['f_freundid'] != $u_id) {
				$query2 = pdoQuery("SELECT `u_nick`, `u_id`, `u_level`, `u_nachrichten_empfangen`, `u_punkte_gesamt`, `u_punkte_gruppe`, `o_id`, date_format(`u_login`,'%d. %M %Y um %H:%i') AS `login`, "
					. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS `online` FROM `user`, `online` WHERE `o_user` = `u_id` AND `u_id` = :u_id", [':u_id'=>$row['f_freundid']]);
				
				$result2Count = $query2->rowCount();
			}
			
			if ($result2Count > 0) {
				// Nachricht erzeugen
				$row2 = $query2->fetch();
				switch ($nachricht) {
					
					case "OLM":
					// Onlinenachricht an u_id versenden
						if ($i > 0) {
							$txt .= "<br>";
						}
						
						$query3 = pdoQuery("SELECT `r_name`, `o_id`, `o_who`, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_login`) AS `online_zeit` FROM `online` LEFT JOIN `raum` ON `r_id` = `o_raum` WHERE `o_user` = :o_user", [':o_user'=>$row2['u_id']]);
						
						$result3Count = $query3->rowCount();
						if ($result3Count > 0) {
							$result3 = $query3->fetch();
							if ($result3['o_who'] == 0) {
								$raum = $lang['chat_msg67'] . " <b>" . $result3['r_name'] . "</b>";
							} else {
								$raum = "<b>[" . $whotext[$result3['o_who']] . "]</b>";
							}
						}
						
						$weiterer = $lang['chat_msg90'];
						$weiterer = str_replace("%u_nick%", zeige_userdetails($row2['u_id'], TRUE), $weiterer);
						$weiterer = str_replace("%raum%", $raum, $weiterer);
						
						$txt .= $weiterer;
						
						if ($row['f_text']) {
							$txt .= " (" . $row['f_text'] . ")";
						}
						break;
					
					case "Chat-Mail":
					// Chat-Mail an u_id versenden
						if ($i > 0) {
							$txt .= "\n\n";
						}
						$txt .= str_replace("%u_nick%", $row2['u_nick'], $lang['chat_msg91']);
						$txt = str_replace("%online%", gmdate("H:i:s", $row2['online']), $txt);
						if ($row['f_text']) {
							$txt .= " (" . $row['f_text'] . ")";
						}
						break;
					
					case "E-Mail":
					// E-Mail an u_id versenden
						if ($i > 0) {
							$txt .= "\n\n";
						}
						$txt .= str_replace("%u_nick%", $row2['u_nick'], $lang['chat_msg91']);
						$txt = str_replace("%online%", gmdate("H:i:s", $row2['online']), $txt);
						if ($row['f_text']) {
							$txt .= " (" . $row['f_text'] . ")";
						}
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
					if($i == 1) {
						$betreff = str_replace("%anzahl%", $i, $lang['nachricht_mail6a']);
					} else {
						$betreff = str_replace("%anzahl%", $i, $lang['nachricht_mail6']);
					}
					mail_sende(0, $u_id, str_replace("%user%", $u_nick, $lang['nachricht_mail5']) . $txt, $betreff);
					break;
				
				case "E-Mail":
					if($i == 1) {
						$betreff = str_replace("%anzahl%", $i, $lang['email_mail6a']);
					} else {
						$betreff = str_replace("%anzahl%", $i, $lang['email_mail6']);
					}
					email_versende("", $u_id, $lang['email_mail5'] . $txt, $betreff);
					break;
				
			}
		}
		
	}
}

//prüft ob neue Antworten auf eigene Beiträge 
//vorhanden sind und benachrichtigt entsprechend
function postings_neu($an_u_id, $u_nick, $nachricht) {
	global $lang, $system_farbe;
	
	//schon gelesene Beiträge des Benutzers holen
	$query = pdoQuery("SELECT `u_gelesene_postings` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>intval($an_u_id)]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		$gelesene = $result['u_gelesene_postings'];
	}
	$u_gelesene = unserialize($gelesene);
	
	//alle eigenen Beiträge des Benutzers mit allen Antworten 
	//die RegExp matcht auf die Beitrags-ID im Feld Themenorder
	//entweder mit vorher und nachher keiner Zahl (damit z.B. 32
	//in 131,132,133 nicht matcht) oder am Anfang oder Ende
	$query = pdoQuery("SELECT a.beitrag_id AS beitrag_id_own, a.beitrag_forum_id AS beitrag_forum_id, a.beitrag_titel as po_titel_own, date_format(from_unixtime(a.beitrag_thema_timestamp), '%d.%m.%Y %H:%i') as po_date_own, forum_name, kat_name,
		b.beitrag_id as beitrag_id_reply, b.beitrag_user_id as po_u_id_reply, b.beitrag_titel as po_titel_reply, date_format(from_unixtime(b.beitrag_thema_timestamp), '%d.%m.%Y %H:%i') as po_date_reply,
		u_nick, a.beitrag_id as beitrag_id_thread
		FROM `forum_beitraege` a, `forum_beitraege` b, `forum_foren`, `forum_kategorien`, user 
		WHERE a.beitrag_user_id = :beitrag_user_id AND a.beitrag_id = b.beitrag_thema_id AND a.beitrag_forum_id = forum_id AND `kat_id = `forum_kategorie_id` AND b.beitrag_user_id = u_id AND a.beitrag_user_id <> b.beitrag_user_id", [':beitrag_user_id'=>intval($an_u_id)]);
	
	$result = $query->fetchAöö();
	
	foreach($result as $zaehler => $postings) {
		//falls posting noch nicht gelesen ist es neu
		if (is_array($u_gelesene[$postings['beitrag_forum_id']])) {
			if (!in_array($postings['beitrag_id_reply'], $u_gelesene[$postings['beitrag_forum_id']])) {
				$poid = $postings['beitrag_id_own'];
				$postings['beitrag_id_thread'] = suche_vaterposting($poid);
				
				//Nachricht versenden
				switch ($nachricht) {
					case "OLM":
						$text = str_replace("%beitrag%", $postings['po_titel_own'], $lang['msg_new_posting_olm']);
						$text = str_replace("%zeit%", $postings['po_date_own'], $text);
						$text = str_replace("%forum%", $postings['kat_name'], $text);
						$text = str_replace("%thema%", $postings['forum_name'], $text);
						$text = str_replace("%user_from_nick%", $postings['u_nick'], $text);
						$text = str_replace("%po_titel_antwort%", $postings['po_titel_reply'], $text);
						$text = str_replace("%po_ts_antwort%", $postings['po_date_reply'], $text);
						
						system_msg("", 0, $an_u_id, $system_farbe, $text);
						break;
					case "Chat-Mail":
						$betreff = str_replace("%beitrag%", $postings['po_titel_own'], $lang['betreff_new_posting']);
						$text = str_replace("%beitrag%", $postings['po_titel_own'], $lang['msg_new_posting_chatmail']);
						$text = str_replace("%zeit%", $postings['po_date_own'], $text);
						$text = str_replace("%forum%", $postings['kat_name'], $text);
						$text = str_replace("%thema%", $postings['forum_name'], $text);
						$text = str_replace("%user_from_nick%", $postings['u_nick'], $text);
						$text = str_replace("%po_titel_antwort%", $postings['po_titel_reply'], $text);
						$text = str_replace("%po_ts_antwort%", $postings['po_date_reply'], $text);
						mail_sende($postings['po_u_id_reply'], $an_u_id, $text, $betreff);
						break;
					case "E-Mail":
						$betreff = str_replace("%beitrag%", $postings['po_titel_own'], $lang['betreff_new_posting']);
						$text = str_replace("%beitrag%", $postings['po_titel_own'], $lang['msg_new_posting_email']);
						$text = str_replace("%zeit%", $postings['po_date_own'], $text);
						$text = str_replace("%forum%", $postings['kat_name'], $text);
						$text = str_replace("%thema%", $postings['forum_name'], $text);
						$text = str_replace("%user_from_nick%", $postings['u_nick'], $text);
						$text = str_replace("%po_titel_antwort%", $postings['po_titel_reply'], $text);
						$text = str_replace("%po_ts_antwort%", $postings['po_date_reply'], $text);
						email_versende($postings['po_u_id_reply'], $an_u_id, $text, $betreff);
						break;
					
				}
				
			}
		}
	}
}

function erzeuge_fuss($text) {
	//generiert den Fuss eines Beitrags (Signatur)
	global $lang, $u_id;
	
	$query = pdoQuery("SELECT `u_signatur`, `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$row = $query->fetch();
	}
	
	if (!$row['u_signatur']) {
		$sig = "<br>-- <br> " . $lang['gruss'] . "\n  " . $row['u_nick'];
	} else {
		$sig = "<br>-- <br>  " . $row['u_signatur'];
	}
	
	return $text . $sig;
}

function word_wrap(
	$org_message,
	$cols = "85",
	$returns = "AUTO",
	$spacer = "",
	$joiner = ' ') {
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

function erzeuge_umbruch($text, $breite) {
	//bricht text nach breite zeichen um
	
	if (strstr($text, "&gt;&nbsp;")) {
		// Alte Version für Quotes beibehalten
		//text in feld einlesen mit \n als trennzeichen
		$arr_text = explode("\n", $text);
		
		//fuer jeden Feldeintrag Zeilenumbruch hinzufuegen
		foreach($arr_text as $k => $zeile) {
			//nur Zeilen umbrechen die nicht gequotet sind
			if (!strstr($zeile, "&gt;&nbsp;")) {
				$arr_text[$k] = wordwrap($zeile, $breite, "\n", 0);
			}
		}
		
		return implode("\n", $arr_text);
	} else {
		// neue version, verhaut die umbrüche nicht mehr
		$text = word_wrap($text, $breite, $returns = "AUTO", $spacer = "", $joiner = ' ');
		return ($text);
	}
	
}

function erzeuge_quoting($text, $autor, $date) {
	// Fügt > vor die zeilen und fügt zu beginn xxx schrieb am xxx an
	global $lang;
	
	$kopf = $lang['kopfzeile'];
	$kopf = str_replace("{autor}", $autor, $kopf);
	$kopf = str_replace("{date}", $date, $kopf);
	
	$arr_zeilen = explode("\n", $text);
	
	foreach($arr_zeilen as $k => $zeile) {
		$arr_zeilen[$k] = "&gt;&nbsp;" . $zeile;
	}
	
	return $kopf . "\n" . implode("\n", $arr_zeilen);
	
}
?>