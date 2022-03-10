<?php
function zeige_freunde($text, $aktion, $zeilen) {
	// Zeigt Liste der Freunde an
	global $u_nick, $u_id, $lang, $locale;
	
	pdoQuery("SET `lc_time_names` = :lc_time_names", [':lc_time_names'=>$locale]);
	
	switch ($aktion) {
		case "bestaetigen":
			$query = pdoQuery("SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, date_format(f_zeit,'%e. %M %Y') AS `zeit` FROM `freunde` WHERE `f_freundid` = :f_freundid AND `f_status` = 'beworben' ORDER BY `f_zeit` DESC", [':f_freundid'=>$u_id]);
			$box = $lang['freunde_freundesanfragen'];
			break;
		
		case "normal":
		default:
			$query = pdoQuery("SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit`, date_format(`f_zeit`,'%e. %M %Y') AS `zeit` FROM `freunde` WHERE `f_userid` = :f_userid AND `f_status` = 'bestaetigt' "
				. "UNION SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit`, date_format(`f_zeit`,'%e. %M %Y') AS `zeit` FROM `freunde` WHERE `f_freundid` = :f_freundid AND `f_status` = 'bestaetigt' ORDER BY `f_zeit` DESC", [':f_userid'=>$u_id, ':f_freundid'=>$u_id]);
			$box = $lang['freunde_meine_freunde'];
		break;
	}
	
	$text .= "<form name=\"eintraege_loeschen\" action=\"inhalt.php?bereich=freunde\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"bearbeite\">\n";
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		// Keine Freunde
		if ($aktion == "normal") {
			$hinweismeldung = $lang['freunde_keine_freunde_vorhanden'];
			$text .= hinweis($hinweismeldung, "hinweis");
		}
		if ($aktion == "bestaetigen") {
			$hinweismeldung = $lang['freunde_anfragen'];
			$text .= hinweis($hinweismeldung, "hinweis");
		}
	} else {
		// Freunde anzeigen
		$text .= "<table style=\"width:100%;\">\n";
		$text .= "<tr>\n";
		$text .= "<td style=\"width:5%;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
		$text .= "<td style=\"width:35%;\" class=\"tabelle_kopfzeile\" colspan=\"2\">" . $lang['freunde_benutzername'] . "</td>\n";
		$text .= "<td style=\"width:35%;\" class=\"tabelle_kopfzeile\">" . $lang['freunde_info'] . "</td>\n";
		$text .= "<td style=\"width:20%; text-align:center;\" class=\"tabelle_kopfzeile\">" . $lang['freunde_datum_des_eintrags'] . "</td>\n";
		$text .= "<td style=\"width:5%; text-align:center;\" class=\"tabelle_kopfzeile\">" . $lang['freunde_aktion'] . "</td>\n";
		$text .= "</tr>\n";
		
		$i = 0;
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			if (($i % 2) > 0) {
				$bgcolor = 'class="tabelle_zeile1"';
			} else {
				$bgcolor = 'class="tabelle_zeile2"';
			}
			
			// Benutzer aus der Datenbank lesen
			if ($row['f_userid'] != $u_id) {
				$query2 = pdoQuery("SELECT `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `o_id`, date_format(`u_login`,'%d. %M %Y um %H:%i') AS `login`, "
					. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_login`) AS `online` FROM `user` LEFT JOIN `online` ON `o_user` = `u_id` WHERE `u_id` = :u_id", [':u_id'=>$row['f_userid']]);
			} else if ($row['f_freundid'] != $u_id) {
				$query2 = pdoQuery("SELECT `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `o_id`, date_format(`u_login`,'%d. %M %Y um%H:%i') AS `login`, "
					. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_login`) AS `online` FROM `user` LEFT JOIN `online` ON `o_user` = `u_id` WHERE `u_id` = :u_id", [':u_id'=>$row['f_freundid']]);
			}
			$result2 = $query2->fetch();
			$result2Count = $query2->rowCount();
			if ($result2Count > 0) {
				// Benutzer gefunden -> Ausgeben
				$freund_nick = "<b>" . zeige_userdetails($result2['u_id'], $result2) . "</b>";
			} else {
				// Benutzer nicht gefunden, Freund löschen
				$freund_nick = "NOBODY";
				
				pdoQuery("DELETE FROM `freunde` WHERE `f_id` = :f_id", [':f_id'=>$row['f_id']]);
			}
			
			// Unterscheidung online ja/nein, Text ausgeben
			if ($result2['o_id']) {
				$txt = $freund_nick . "<br>online&nbsp;" . gmdate("H:i:s", $result2['online']) . "&nbsp;Std/Min/Sek";
				$auf = "<b>";
				$zu = "</b>";
				$status = "<span class=\"fa-solid fa-user icon16 user_online\"></span>";
			} else {
				$txt = $freund_nick . "<br>Letzter&nbsp;Login:&nbsp;" . str_replace(" ", "&nbsp;", $result2['login']);
				$auf = "";
				$zu = "";
				$status = "<span class=\"fa-solid fa-user icon16 user_offline\"></span>";
			}
			
			// Infotext setzen
			if ($row['f_text'] == "") {
				$infotext = "&nbsp;";
			} else {
				$infotext = htmlentities($row['f_text']);
			}
			
			// ID des Freundes finden
			if ($u_id == $row['f_freundid']) {
				$freundid = $row['f_userid'];
			} else {
				$freundid = $row['f_freundid'];
			}
			
			$text .= "<tr>\n";
			$text .= "<td style=\"text-align:center;\" $bgcolor>" . $auf . "<input type=\"checkbox\" name=\"bearbeite_ids[]\" value=\"" . $freundid . "\"></td>\n";
			$text .= "<td style=\"width:15px; text-align:center\" $bgcolor>$status</td>\n";
			$text .= "<td $bgcolor>" . $auf . $txt . $zu . "</td>\n";
			$text .= "<td $bgcolor>" . $auf . $infotext . $zu . "</td>\n";
			$text .= "<td style=\"text-align:center;\" $bgcolor>" . $auf . $row['zeit'] . $zu . "</td>\n";
			$text .= "<td style=\"text-align:center;\" $bgcolor><a href=\"inhalt.php?bereich=freunde&aktion=editinfotext&daten_id=$row[f_id]\" class=\"button\" title=\"$lang[freunde_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[freunde_editieren]</span></a></td>";
			$text .= "</tr>\n";

			$i++;
		}
		
		$text .= "<tr>"
			."<td $bgcolor colspan=\"2\"><input type=\"checkbox\" onClick=\"toggle(this.checked)\">$lang[freunde_alle_auswaehlen]</td>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor colspan=\"4\">";
		if ($aktion == "bestaetigen") {
			$text .= "<input type=\"submit\" name=\"uebergabe\" value=\"$lang[freunde_bestaetigen]\">";
		}
		$text .= "<input type=\"submit\" name=\"uebergabe\" value=\"$lang[freunde_loeschen]\">"
			. "</td></tr></table>\n";
	}
	
	$text .= "</form>\n";
	
	zeige_tabelle_zentriert($box,$text);
}

function loesche_freund($f_freundid, $f_userid) {
	// Löscht Freund aus der Tabelle mit f_userid und f_freundid
	// $f_userid Benutzer-ID 
	// $f_freundid Benutzer-ID
	global $u_nick, $u_id, $lang;
	
	$text = "";
	if (!$f_userid || !$f_freundid) {
		$fehlermeldung = $lang['freunde_fehlermeldung_benutzer_loeschen_nicht_moeglich'];
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		pdoQuery("DELETE FROM `freunde` WHERE (`f_userid` = :f_userid1 AND `f_freundid` = :f_freundid1) OR (`f_userid` = :f_userid2 AND `f_freundid` = :f_freundid2)", [':f_userid1'=>$f_userid, ':f_freundid1'=>$f_freundid, ':f_userid2'=>$f_freundid, ':f_freundid2'=>$f_userid]);
		
		$query = pdoQuery("SELECT `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$f_freundid]);
		
		$resultCount = $query->rowCount();
		if ($resultCount != 0) {
			$resultCount = $query->fetch();
			$f_nick = $resultCount['u_nick'];
			
			$erfolgsmeldung = str_replace("%user%", $f_nick, $lang['freunde_erfolgsmeldung_freund_geloescht']);
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
		} else {
			$fehlermeldung = $lang['freunde_fehlermeldung_benutzer_loeschen_nicht_moeglich'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
	}
	
	return $text;
}

function formular_neuer_freund($text, $daten) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Freund aus
	global $lang;
	
	$box = $lang['freunde_neuen_freund_hinzufuegen'];
	
	$text .= "<form action=\"inhalt.php?bereich=freunde\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"neu2\">\n";
	$text .= "<table style=\"width:100%;\">";
	
	$zaehler = 0;
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $lang['freunde_benutzername'], "daten_nick", $daten['u_nick']);
	$zaehler++;
	
	// Info
	$text .= zeige_formularfelder("input", $zaehler, $lang['freunde_info'], "daten_text", htmlentities($daten['f_text']));
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"uebergabe\" value=\"$lang[freunde_eintragen]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	$text .= "</form>\n";
	
	zeige_tabelle_zentriert($box,$text);
}

function formular_editieren($daten) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Freund aus
	global $lang;
	
	$box = $lang['freunde_freundestext_aendern'];
	
	$text = "";
	$text .= "<form action=\"inhalt.php?bereich=freunde\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"editinfotext2\">\n";
	$text .= "<input type=\"hidden\" name=\"daten_id\" value=\"$daten[id]\">\n";
	$text .= "<table style=\"width:100%;\">";
	
	$zaehler = 0;
	
	// Info
	$text .= zeige_formularfelder("input", $zaehler, $lang['freunde_info'], "daten_text", htmlentities($daten['f_text']));
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"uebergabe\" value=\"$lang[freunde_editieren]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	$text .= "</form>\n";
	
	zeige_tabelle_zentriert($box, $text);
}

function neuer_freund($f_userid, $daten) {
	// Trägt neuen Freund in der Datenbank ein
	global $system_farbe, $lang;
	
	$text = "";
	if (!$daten['id'] || !$f_userid) {
		$fehlermeldung .= $lang['freunde_fehlermeldung_fehler_beim_hinzufuegen'];
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		// Prüfen ob Freund bereits in Tabelle steht
		$daten['id'] = $daten['id'];
		
		$query = pdoQuery("SELECT `f_id` FROM `freunde` WHERE (`f_userid` = :f_userid1 AND `f_freundid` = :f_freundid1) OR (`f_userid` = :f_userid2 AND `f_freundid` = :f_freundid2)",
			[
				':f_userid1'=>$daten['id'],
				':f_freundid1'=>$f_userid,
				':f_userid2'=>$f_userid,
				':f_freundid2'=>$daten['id']
			]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$fehlermeldung .= str_replace("%u_nick%", $daten['u_nick'], $lang['freunde_fehlermeldung_bereits_als_freund_vorhanden']);
			$text .= hinweis($fehlermeldung, "fehler");
		} else if ($daten['id'] == $f_userid) {
			// Eigener Freund ist verboten
			$fehlermeldung .= $lang['freunde_fehlermeldung_selbst_als_freund'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if($text == "") {
			// Benutzer ist noch kein Freund -> hinzufügen
			$f['f_userid'] = $f_userid;
			$f['f_freundid'] = $daten['id'];
			$f['f_text'] = $daten['f_text'];
			$f['f_status'] = "beworben";
			
			pdoQuery("INSERT INTO `freunde` (`f_userid`, `f_freundid`, `f_text`, `f_status`) VALUES (:f_userid, :f_freundid, :f_text, :f_status)",
				[
					':f_userid'=>$f['f_userid'],
					':f_freundid'=>$f['f_freundid'],
					':f_text'=>$f['f_text'],
					':f_status'=>$f['f_status']
				]);
			
			$betreff = $lang['freunde_freundesanfrage'];
			$email_text = str_replace("%u_nick%", $daten['u_nick'], $lang['freunde_freundesanfrage_email']);
			
			mail_sende($f_userid, $daten['id'], $email_text, $betreff);
			if (ist_online($daten['id'])) {
				$msg = str_replace("%u_nick%", $daten['u_nick'], $lang['freunde_freundesanfrage_chat']);
				system_msg("", 0, $daten['id'], $system_farbe, $msg);
			}
			
			$erfolgsmeldung = str_replace("%u_nick%", $daten['u_nick'], $lang['freunde_erfolgsmeldung_freundesanfrage']);
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
			
			$daten['u_nick'] = "";
			$daten['f_text'] = "";
		}
	}
	return $text;
}

function edit_freund($daten) {
	// Ändert den Infotext beim Freund
	global $lang;
	
	unset($f);
	$f['f_text'] = $daten['f_text'];
	
	pdoQuery("UPDATE `freunde` SET `f_text` = :f_text WHERE `f_id` = :f_id",
		[
			':f_id'=>$daten['id'],
			':f_text'=>$f['f_text']
		]);
	
	$text = $lang['freunde_erfolgsmeldung_freund_text_geaendert'];
	
	return $text;
}

function bestaetige_freund($f_userid, $u_id) {
	global $lang;
	
	pdoQuery("UPDATE `freunde` SET `f_status` = 'bestaetigt', `f_zeit` = NOW() WHERE `f_userid` = :f_userid AND `f_freundid` = :f_freundid", [':f_userid'=>$f_userid, ':f_freundid'=>$u_id]);
	
	$text = "";
	
	$query = pdoQuery("SELECT `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$f_userid]);
	$resultCount = $query->rowCount();
	if ($resultCount != 0) {
		$resultCount = $query->fetch();
		$f_nick = $resultCount['u_nick'];
		$text = str_replace("%u_nick%", $f_nick, $lang['freunde_erfolgsmeldung_freundschaft_bestaetigt']);
	}
	return $text;
}
?>