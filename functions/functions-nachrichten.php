<?php
function formular_neue_email($text, $daten) {
	// Gibt Formular für den Benutzernamen zum Versand einer Mail aus
	global $lang;
	
	// Benutzername aus u_nick lesen und setzen
	if ( $daten['u_id'] != "" ) {
		$query = pdoQuery("SELECT `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$daten['u_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$an_nick = $result['u_nick'];
			if (strlen($an_nick) > 0) {
				$daten['an_nick'] = $an_nick;
			}
		}
	}
	
	if ($daten != "" && $daten['id'] != "") {
		$box = $lang['nachrichten_nachricht_weiterleiten'];
	} else {
		$box = $lang['nachrichten_neue_nachricht'];
	}
	
	$text .= "<form action=\"inhalt.php?bereich=nachrichten\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"neu2\">\n";
	$text .= "<input type=\"hidden\" name=\"daten_id\" value=\"$daten[id]\">\n";
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">";
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $lang['nachrichten_benutzername'], "daten_nick", $daten['u_nick']);
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[nachrichten_weiter]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	$text .= "</form>\n";
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}

function formular_neue_email2($text, $box, $daten) {
	// Gibt Formular zum Versand einer neuen Mail aus
	global $u_id, $lang;
	
	if ($daten['id'] != "") {
		// Alte Mail lesen und als Kopie in Formular schreiben
		$query = pdoQuery("SELECT `m_betreff`, `m_text` FROM `mail` WHERE `m_id` = :m_id AND `m_an_uid` = :m_an_uid", [':m_id'=>$daten['id'], ':m_an_uid'=>$u_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			
			$daten['m_betreff'] = html_entity_decode($result['m_betreff']);
			if($box == $lang['nachrichten_nachricht_antworten']) {
				$daten['m_betreff'] = "AW: " . $daten['m_betreff'];
			} else {
				$daten['m_betreff'] = "WG: " . $daten['m_betreff'];
			}
			$daten['f_text'] = html_entity_decode($result['m_text']);
			
			$daten['f_text'] = str_replace("<b>", "_", $daten['f_text']);
			$daten['f_text'] = str_replace("</b>", "_", $daten['f_text']);
			
			$daten['f_text'] = str_replace("<i>", "*", $daten['f_text']);
			$daten['f_text'] = str_replace("</i>", "*", $daten['f_text']);
			
		}
	} else {
		// Neue Mail versenden
		$box = $lang['nachrichten_neue_nachricht'];
	}
	
	// Signatur anfügen
	if (!isset($daten['f_text'])) {
		$daten['f_text'] = htmlspecialchars(erzeuge_fuss(""));
	}
	
	// Benutzerdaten aus u_id lesen und setzen
	if ($daten['u_id']) {
		$query = pdoQuery("SELECT `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_emails_akzeptieren`, `o_id`, "
			. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_login`) AS `online` FROM `user` LEFT JOIN `online` ON `o_user` = `u_id` WHERE `u_id` = :u_id", [':u_id'=>$daten['u_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$row = $query->fetch();
			
			$zaehler = 0;
			$text .= "<form action=\"inhalt.php?bereich=nachrichten\" method=post>\n";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"neu3\">\n";
			$text .= "<input type=\"hidden\" name=\"daten_uid\" value=\"$daten[u_id]\">\n";
			$text .= "<table style=\"width:100%;\">\n";
			
			if ($row['o_id'] == "" || $row['o_id'] == "NULL") {
				$row['online'] = "";
			}
			
			if (!isset($daten['m_betreff'])) {
				$daten['m_betreff'] = "";
			}
			
			// An
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right;\" class=\"tabelle_zeile1\">$lang[nachrichten_an]</td>\n";
			$text .= "<td class=\"tabelle_zeile1\">$row[u_nick]</td>\n";
			$text .= "</tr>\n";
			
			// Betreff
			$text .= zeige_formularfelder("input", $zaehler, $lang['nachrichten_betreff'], "daten_betreff", $daten['m_betreff']);
			$zaehler++;
			
			// Text
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right;\" class=\"tabelle_zeile1\">$lang[nachrichten_text]</td>\n";
			$text .= "<td class=\"tabelle_zeile1\"><textarea cols=\"75\" rows=\"20\" name=\"daten_text\">" . $daten['f_text'] . "</textarea></td>\n";
			$text .= "</tr>\n";
			
			// Art des Versands
			$email_select = "<select name=\"daten_typ\">";
			if (isset($daten['typ']) && $daten['typ'] == 1) {
				$email_select .= "<option value=\"0\">$lang[nachrichten_art_des_versands_nachricht]\n";
				if ($row['u_emails_akzeptieren'] == "1") {
					$email_select .= "<option selected value=\"1\">$lang[nachrichten_art_des_versands_email]\n";
				}
			} else {
				$email_select .= "<option selected value=\"0\">$lang[nachrichten_art_des_versands_nachricht]\n";
				if ($row['u_emails_akzeptieren'] == "1") {
					$email_select .= "<option value=\"1\">$lang[nachrichten_art_des_versands_email]\n";
				}
			}
			$email_select .= "</select>\n";
			$text .= "<tr>\n";
			$text .= "<td style=\"text-align:right;\" class=\"tabelle_zeile2\">$lang[nachrichten_art_des_versands]</td>\n";
			$text .= "<td class=\"tabelle_zeile2\">$email_select</td>\n";
			$text .= "</tr>\n";
			
			$text .= "<tr>\n";
			$text .= "<td class=\"tabelle_zeile1\">&nbsp;</td>\n";
			$text .= "<td class=\"tabelle_zeile1\"><input type=\"submit\" value=\"Versenden\"></td>\n";
			$text .= "</tr>\n";
			$text .= "</table>\n";
			$text .= "</form>\n";
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
		} else {
			$fehlermeldung = $lang['nachrichten_fehler_benutzername_existiert_nicht2'];
			$text = hinweis($fehlermeldung, "fehler");
			
			$box = $lang['titel'];
			zeige_tabelle_zentriert($box, $text);
		}
	}
}

function zeige_mailbox($text, $aktion, $zeilen) {
	// Zeigt die Nachrichten in der Übersicht an
	global $u_nick, $u_id, $chat, $lang, $locale;
	
	pdoQuery("SET `lc_time_names` = :lc_time_names", [':lc_time_names'=>$locale]);
	
	$art = "empfangen";
	switch ($aktion) {
		case "geloescht":
			$query = pdoQuery("SELECT `m_id`, `m_status`, `m_von_uid`, date_format(`m_zeit`,'%d. %M %Y um %H:%i') AS `zeit`, `m_betreff`, `u_nick` "
				. "FROM `mail` LEFT JOIN `user` ON `m_von_uid` = `u_id` WHERE `m_an_uid` = :m_an_uid AND `m_status` = 'geloescht' ORDER BY `m_zeit` DESC", [':m_an_uid'=>$u_id]);
			
			$button = $lang['nachrichten_wiederherstellen'];
			$titel = $lang['nachrichten_papierkorb'];
			break;
		
		case "postausgang":
			$query = pdoQuery("SELECT `m_id`, `m_status`, `m_von_uid`, date_format(`m_zeit`,'%d. %M %Y um %H:%i') AS `zeit`, `m_betreff`, `u_nick` "
				. "FROM `mail` LEFT JOIN `user` ON `m_an_uid` = `u_id` WHERE `m_von_uid` = :m_an_uid AND `m_status` != 'geloescht' ORDER BY `m_zeit` DESC", [':m_an_uid'=>$u_id]);
			
			$button = $lang['nachrichten_loeschen'];
			$titel = $lang['nachrichten_postausgang2'];
			$art = "gesendet";
			break;
		
		case "normal":
		default;
			$query = pdoQuery("SELECT `m_id`, `m_status`, `m_von_uid`, date_format(`m_zeit`,'%d. %M %Y um %H:%i') AS `zeit`, `m_betreff`, `u_nick` "
				. "FROM `mail` LEFT JOIN `user` ON `m_von_uid` = `u_id` WHERE `m_an_uid` = :m_an_uid AND `m_status` !='geloescht' ORDER BY `m_zeit` DESC", [':m_an_uid'=>$u_id]);
			
			$button = $lang['nachrichten_loeschen'];
			$titel = $lang['nachrichten_posteingang2'];
	}
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$box = "$resultCount $titel";
		
		if($aktion == "geloescht") {
			$text .= "<br><a href=\"inhalt.php?bereich=nachrichten&aktion=papierkorbleeren\" class=\"button\" title=\"$lang[nachrichten_papierkorb_leeren]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[nachrichten_papierkorb_leeren]</span></a><br><br>";
		}
		
		$text .= "<form name=\"eintraege_loeschen\" action=\"inhalt.php?bereich=nachrichten\" method=\"post\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n";
		
		// Nachrichten anzeigen
		$text .= "<table style=\"width:100%;\">\n"
			. "<tr>"
			."<td class=\"tabelle_kopfzeile\" style=\"width:30px; text-align:center;\"><input type=\"checkbox\" onClick=\"toggle(this.checked)\"></td>"
			."<td class=\"tabelle_kopfzeile\" style=\"width:40px; text-align:center;\">" . $lang['nachrichten_status'] . "</td>"
			."<td class=\"tabelle_kopfzeile\">" . $lang['nachrichten_von'] . "</td>"
			."<td class=\"tabelle_kopfzeile\">" . $lang['nachrichten_betreff'] . "</td>"
			."<td class=\"tabelle_kopfzeile\">" . $lang['nachrichten_datum'] . "</td>"
			."</tr>\n";
			
		$i = 0;
		$bgcolor = 'class="tabelle_zeile1"';
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			if($art == "gesendet") {
				$url = "<a href=\"inhalt.php?bereich=nachrichten&aktion=zeige_gesendet&daten_id=". $row['m_id'] . "\">";
				$url2 = "</a>";
			} else {
				$url = "<a href=\"inhalt.php?bereich=nachrichten&aktion=zeige_empfangen&daten_id=". $row['m_id'] . "\">";
				$url2 = "</a>";
			}
			if ($row['m_status'] == "neu" || $row['m_status'] == "neu/verschickt") {
				$auf = "<b>";
				$zu = "</b>";
				$status = "<span class=\"fa-solid fa-comment-dots icon24\" alt=\"Neue Nachrichten\" title=\"Neue Nachrichten\"></span>";
			} else {
				$auf = "";
				$zu = "";
				$status = "<span class=\"fa-regular fa-comment-dots icon24\" alt=\"Keine neuen Nachrichten\" title=\"Keine neuen Nachrichten\"></span>";
			}
			
			if ($row['u_nick'] == "NULL" || $row['u_nick'] == "") {
				$von_nick = "$chat";
			} else {
				$von_nick = $row['u_nick'];
			}
			
			$text .= "<tr>\n";
			$text .= "<td style=\"text-align:center;\" $bgcolor>" . $auf . "<input type=\"checkbox\" name=\"bearbeite_ids[]\" value=\"" . $row['m_id'] . "\">" . $zu . "</td>\n";
			$text .= "<td $bgcolor style=\"text-align:center; padding-left: 5px;\">" . $status . "</td>\n";
			$text .= "<td $bgcolor>" . $auf . $url . $von_nick . $url2 . $zu . "</td>\n";
			$text .= "<td style=\"width:50%;\" $bgcolor>" . $auf . $url . html_entity_decode($row['m_betreff']) . "</a>" . $zu . "</td>\n";
			$text .= "<td $bgcolor>" . $auf . $row['zeit'] . $zu . "</td>\n";
			$text .= "</tr>\n";
			
			if (($i % 2) > 0) {
				$bgcolor = 'class="tabelle_zeile1"';
			} else {
				$bgcolor = 'class="tabelle_zeile2"';
			}
			$i++;
		}
		if($aktion != "postausgang") {
			$text .= "<tr><td $bgcolor colspan=\"5\"><input type=\"submit\" value=\"$button\"></td>\n";
		}
		$text .= "</tr>\n";
		$text .= "</table>\n";
		$text .= "</form>\n";
	} else {
		$text .=  $lang['nachrichten_keine_nachrichten_vorhanden'];
	}
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}

function zeige_email($daten, $art) {
	// Zeigt die Mail im Detail an
	global $u_nick, $u_id, $chat, $lang, $locale;
	
	pdoQuery("SET `lc_time_names` = :lc_time_names", [':lc_time_names'=>$locale]);
	
	if($art == "gesendet") {
		$query = pdoQuery("SELECT mail.*,date_format(`m_zeit`,'%d. %M %Y um %H:%i') AS `zeit`, `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe` "
			. "FROM `mail` LEFT JOIN `user` ON `m_von_uid` = `u_id` WHERE `m_von_uid` = :m_von_uid AND `m_id` = :m_id ORDER BY `m_zeit` DESC", [':m_von_uid'=>$u_id, ':m_id'=>$daten['id']]);
	} else {
		$query = pdoQuery("SELECT mail.*,date_format(`m_zeit`,'%d. %M %Y um %H:%i') AS `zeit`, `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe` "
			. "FROM `mail` LEFT JOIN `user` ON `m_von_uid` = `u_id` WHERE `m_an_uid` = :m_an_uid AND `m_id` = :m_id ORDER BY `m_zeit` DESC", [':m_an_uid'=>$u_id, ':m_id'=>$daten['id']]);
	}
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$row = $query->fetch();
		// Mail anzeigen
		
		if ($row['u_nick'] == "NULL" || $row['u_nick'] == "") {
			$von_nick = "$chat";
		} else {
			$von_nick = "<b>" . zeige_userdetails($row['u_id'], $row) . "</b>";
		}
		
		$text = '';
		$box = html_entity_decode($row['m_betreff']);
		
		// Mail ausgeben
		$text .= "<table style=\"width:100%;\">\n";
		
		// Von
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right; width:200px;\" class=\"tabelle_zeile1\"><b>$lang[nachrichten_von]</b></td>\n";
		$text .= "<td colspan=\"3\" class=\"tabelle_zeile1\">" . $von_nick . "</td>\n";
		$text .= "</tr>\n";
		
		// Am
		$text .=  "<tr>\n";
		$text .= "<td style=\"text-align:right;\" class=\"tabelle_zeile2\"><b>$lang[nachrichten_am]</b></td>\n";
		$text .= "<td colspan=\"3\" class=\"tabelle_zeile2 smaller\">" . $row['zeit'] . "</td>\n";
		$text .= "</tr>\n";
		
		// Text
		$text .= "<tr>\n";
		$text .= "<td style=\"vertical-align:top; text-align:right;\" class=\"tabelle_zeile1\"><b>$lang[nachrichten_text]</b></td>\n";
		$text .= "<td colspan=3 class=\"tabelle_zeile1\">" . html_entity_decode( str_replace("\n", "<br>\n", $row['m_text']) ) . "</td>\n";
		$text .= "</tr>\n";
		
		// Formular zur Löschen, Beantworten
		if($art != "gesendet") {
			
			$text .= "<tr>\n";
			$text .= "<td class=\"tabelle_zeile2\">&nbsp;</td>\n";
			$text .= "<td style=\"text-align:left;\" class=\"tabelle_zeile2\">\n";
			if ($row['u_nick'] != "NULL" && $row['u_nick'] != "") {
				$text .= "<form action=\"inhalt.php?bereich=nachrichten\" method=\"post\">\n";
				$text .= "<input type=\"hidden\" name=\"daten_id\" value=\"" . $row['m_id'] . "\">\n";
				$text .= "<input type=\"hidden\" name=\"aktion\" value=\"antworten\">\n";
				$text .= "<input type=\"submit\" value=\"$lang[nachrichten_antworten]\">" . "</form>\n";
			}
			$text .= "</td>\n";
			
			$text .= "<td style=\"text-align:center;\" class=\"tabelle_zeile2\">\n";
			$text .= "<form action=\"inhalt.php?bereich=nachrichten\" method=\"post\">\n";
			$text .= "<input type=\"hidden\" name=\"daten_id\" value=\"" . $row['m_id'] . "\">\n";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"weiterleiten\">\n";
			$text .= "<input type=\"submit\" value=\"$lang[nachrichten_weiterleiten]\">\n";
			$text .= "</form>\n";
			$text .= "</td>\n";
			
			$text .= "<td style=\"text-align:right;\" class=\"tabelle_zeile2\">\n";
			$text .= "<form action=\"inhalt.php?bereich=nachrichten\" method=\"post\">\n";
			$text .= "<input type=\"hidden\" name=\"bearbeite_ids[]\" value=\"" . $row['m_id'] . "\">\n";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n";
			$text .= "<input type=\"submit\" value=\"$lang[nachrichten_loeschen]\">\n";
			$text .= "</form>\n";
			$text .= "</td>\n";
			$text .= "</tr>\n";
		}
		$text .= "</table>\n";
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
		
		if($art != "gesendet") {
			if ($row['m_status'] != "geloescht") {
				// E-Mail auf gelesen setzen
				unset($f);
				$f['m_status'] = "gelesen";
				$f['m_zeit'] = $row['m_zeit'];
				
				pdoQuery("UPDATE `mail` SET `m_status` = :m_status, `m_zeit` = :m_zeit WHERE `m_id` = :m_id",
					[
						':m_id'=>$row['m_id'],
						':m_status'=>$f['m_status'],
						':m_zeit'=>$f['m_zeit']
					]);
			}
		}
	}
}

function loesche_mail($bearbeite_id, $u_id) {
	// Löscht eine Mail der ID m_id
	global $u_nick, $u_id, $lang;
	
	$query = pdoQuery("SELECT `m_zeit`, `m_id`, `m_status`, `m_betreff` FROM `mail` WHERE `m_id` = :m_id AND `m_an_uid` = :m_an_uid", [':m_id'=>$bearbeite_id, ':m_an_uid'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$row = $query->fetch();
		$f['m_zeit'] = $row['m_zeit'];
		
		if ($row['m_status'] != "geloescht") {
			$f['m_status'] = "geloescht";
			$f['m_geloescht_ts'] = date("YmdHis");
		} else {
			$f['m_status'] = "gelesen";
			$f['m_geloescht_ts'] = '00000000000000';
		}
		
		pdoQuery("UPDATE `mail` SET `m_zeit` = :m_zeit, `m_status` = :m_status, `m_geloescht_ts` = :m_geloescht_ts WHERE `m_id` = :m_id",
			[
				':m_id'=>$row['m_id'],
				':m_zeit'=>$f['m_zeit'],
				':m_status'=>$f['m_status'],
				':m_geloescht_ts'=>$f['m_geloescht_ts']
			]);
		
		$erfolgsmeldung = str_replace("%nachricht%", html_entity_decode($row['m_betreff']), $lang['nachrichten_erfolgreich_geloescht']);
		$text = hinweis($erfolgsmeldung, "erfolgreich");
	} else {
		$fehlermeldung = str_replace("%nachricht%", html_entity_decode($row['m_betreff']), $lang['nachrichten_fehler_nicht_geloescht']);
		$text = hinweis($fehlermeldung, "fehler");
	}
	
	return $text;
}

?>