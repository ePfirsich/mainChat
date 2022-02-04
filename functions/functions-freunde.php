<?php
function zeige_freunde($text, $aktion, $zeilen) {
	// Zeigt Liste der Freunde an
	global $id, $u_nick, $u_id, $t, $locale;
	
	$sql = "SET lc_time_names = '$locale'";
	$query = sqlQuery($sql);
	
	switch ($aktion) {
		case "normal":
		default:
			$query = "SELECT f_id,f_text,f_userid,f_freundid,f_zeit,date_format(f_zeit,'%e. %M %Y') as zeit FROM freunde WHERE f_userid=$u_id AND f_status = 'bestaetigt' "
				. "UNION "
				. "SELECT f_id,f_text,f_userid,f_freundid,f_zeit,date_format(f_zeit,'%e. %M %Y') as zeit FROM freunde WHERE f_freundid=$u_id AND f_status = 'bestaetigt' ORDER BY f_zeit desc ";
				$box = $t['freunde_meine_freunde'];
			break;
		
		case "bestaetigen":
			$query = "SELECT f_id,f_text,f_userid,f_freundid, date_format(f_zeit,'%e. %M %Y') AS zeit FROM freunde WHERE f_freundid=$u_id AND f_status='beworben' ORDER BY f_zeit desc";
			$box = $t['freunde_freundesanfragen'];
			break;
	}
	
	$text .= "<form name=\"eintraege_loeschen\" action=\"inhalt.php?bereich=freunde\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"bearbeite\">\n";
	
	$result = sqlQuery($query);
	if ($result) {
		$anzahl = mysqli_num_rows($result);
		if ($anzahl == 0) {
			// Keine Freunde
			if ($aktion == "normal") {
				$text.= $t['freunde_keine_freunde_vorhanden'];
			}
			if ($aktion == "bestaetigen") {
				$text.= $t['freunde_anfragen'];
			}
		} else {
			// Freunde anzeigen
			$text .= "<table style=\"width:100%;\">\n";
			$text .= "<tr>\n";
			$text .= "<td style=\"width:5%;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
			$text .= "<td style=\"width:35%;\" class=\"tabelle_kopfzeile\" colspan=\"2\">" . $t['freunde_benutzername'] . "</td>\n";
			$text .= "<td style=\"width:35%;\" class=\"tabelle_kopfzeile\">" . $t['freunde_info'] . "</td>\n";
			$text .= "<td style=\"width:20%; text-align:center;\" class=\"tabelle_kopfzeile\">" . $t['freunde_datum_des_eintrags'] . "</td>\n";
			$text .= "<td style=\"width:5%; text-align:center;\" class=\"tabelle_kopfzeile\">" . $t['freunde_aktion'] . "</td>\n";
			$text .= "</tr>\n";
			
			$i = 0;
			while ($row = mysqli_fetch_object($result)) {
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
				}
				
				// Benutzer aus der Datenbank lesen
				if ($row->f_userid != $u_id) {
					$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, date_format(u_login,'%d. %M %Y um %H:%i') AS login, "
						. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$row->f_userid ";
						$result2 = sqlQuery($query);
				} elseif ($row->f_freundid != $u_id) {
					$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, date_format(u_login,'%d. %M %Y um%H:%i') AS login, "
						. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$row->f_freundid ";
						$result2 = sqlQuery($query);
				}
				if ($result2 && mysqli_num_rows($result2) > 0) {
					// Benutzer gefunden -> Ausgeben
					$row2 = mysqli_fetch_object($result2);
					$freund_nick = "<b>" . zeige_userdetails($row2->u_id, $row2) . "</b>";
				} else {
					// Benutzer nicht gefunden, Freund löschen
					$freund_nick = "NOBODY";
					$query = "DELETE from freunde WHERE f_id=$row->f_id";
					$result2 = sqlUpdate($query);
					
				}
				
				// Unterscheidung online ja/nein, Text ausgeben
				if ($row2->o_id) {
					$txt = $freund_nick . "<br>online&nbsp;" . gmdate("H:i:s", $row2->online) . "&nbsp;Std/Min/Sek";
					$auf = "<b>";
					$zu = "</b>";
					$status = "<span class=\"fa fa-user icon16 user_online\"></span>";
				} else {
					$txt = $freund_nick . "<br>Letzter&nbsp;Login:&nbsp;" . str_replace(" ", "&nbsp;", $row2->login);
					$auf = "";
					$zu = "";
					$status = "<span class=\"fa fa-user icon16 user_offline\"></span>";
				}
				
				// Infotext setzen
				if ($row->f_text == "") {
					$infotext = "&nbsp;";
				} else {
					$infotext = htmlentities($row->f_text);
				}
				
				// ID des Freundes finden
				if ($u_id == $row->f_freundid) {
					$freundid = $row->f_userid;
				} else {
					$freundid = $row->f_freundid;
				}
				
				$text .= "<tr>\n";
				$text .= "<td style=\"text-align:center;\" $bgcolor>" . $auf . "<input type=\"checkbox\" name=\"bearbeite_ids[]\" value=\"" . $freundid . "\"></td>\n";
				$text .= "<td style=\"width:15px; text-align:center\" $bgcolor>$status</td>\n";
				$text .= "<td $bgcolor>" . $auf . $txt . $zu . "</td>\n";
				$text .= "<td $bgcolor>" . $auf . $infotext . $zu . "</td>\n";
				$text .= "<td style=\"text-align:center;\" $bgcolor>" . $auf . $row->zeit . $zu . "</td>\n";
				$text .= "<td style=\"text-align:center;\" $bgcolor><a href=\"inhalt.php?bereich=freunde&id=$id&aktion=editinfotext&daten_id=$row->f_id\" class=\"button\" title=\"$t[freunde_editieren]\"><span class=\"fa fa-pencil icon16\"></span> <span>$t[freunde_editieren]</span></a></td>";
				$text .= "</tr>\n";

				$i++;
			}
			
			$text .= "<tr>"
				."<td $bgcolor colspan=\"2\"><input type=\"checkbox\" onClick=\"toggle(this.checked)\">$t[freunde_alle_auswaehlen]</td>\n";
			$text .= "<td style=\"text-align:right;\" $bgcolor colspan=\"4\">";
			if ($aktion == "bestaetigen") {
				$text .= "<input type=\"submit\" name=\"uebergabe\" value=\"$t[freunde_bestaetigen]\">";
			}
			$text .= "<input type=\"submit\" name=\"uebergabe\" value=\"$t[freunde_loeschen]\">"
				. "</td></tr></table>\n";
		}
		
		$text .= "</form>\n";
		
		zeige_tabelle_zentriert($box,$text);
	}
}

function loesche_freund($f_freundid, $f_userid) {
	// Löscht Freund aus der Tabelle mit f_userid und f_freundid
	// $f_userid Benutzer-ID 
	// $f_freundid Benutzer-ID
	global $id, $u_nick, $u_id, $t;
	
	$text = "";
	if (!$f_userid || !$f_freundid) {
		$fehlermeldung = $t['freunde_fehlermeldung_benutzer_loeschen_nicht_moeglich'];
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		$f_freundid = escape_string($f_freundid);
		$f_userid = escape_string($f_userid);
		
		$query = "DELETE FROM freunde WHERE (f_userid=$f_userid AND f_freundid=$f_freundid) OR (f_userid=$f_freundid AND f_freundid=$f_userid)";
		$result = sqlUpdate($query);
		
		$query = "SELECT `u_nick` FROM `user` WHERE `u_id`=$f_freundid";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) != 0) {
			$f_nick = mysqli_result($result, 0, 0);
			
			$erfolgsmeldung = str_replace("%user%", $f_nick, $t['freunde_erfolgsmeldung_freund_geloescht']);
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
		}
		mysqli_free_result($result);
	}
	
	return $text;
}

function formular_neuer_freund($text, $daten) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Freund aus
	global $id, $t;
	
	$box = $t['freunde_neuen_freund_hinzufuegen'];
	
	$text .= "<form action=\"inhalt.php?bereich=freunde\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"neu2\">\n";
	$text .= "<table style=\"width:100%;\">";
	
	$zaehler = 0;
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $t['freunde_benutzername'], "daten_nick", $daten['u_nick']);
	$zaehler++;
	
	// Info
	$text .= zeige_formularfelder("input", $zaehler, $t['freunde_info'], "daten_text", htmlentities($daten['f_text']));
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"uebergabe\" value=\"$t[freunde_eintragen]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	$text .= "</form>\n";
	
	zeige_tabelle_zentriert($box,$text);
}

function formular_editieren($daten) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Freund aus
	global $id, $t;
	
	$box = $t['freunde_freundestext_aendern'];
	
	$text = "";
	$text .= "<form action=\"inhalt.php?bereich=freunde\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"editinfotext2\">\n";
	$text .= "<input type=\"hidden\" name=\"daten_id\" value=\"$daten[id]\">\n";
	$text .= "<table style=\"width:100%;\">";
	
	$zaehler = 0;
	
	// Info
	$text .= zeige_formularfelder("input", $zaehler, $t['freunde_info'], "daten_text", htmlentities($daten['f_text']));
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"uebergabe\" value=\"$t[freunde_editieren]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	$text .= "</form>\n";
	
	zeige_tabelle_zentriert($box, $text);
}

function neuer_freund($f_userid, $daten) {
	// Trägt neuen Freund in der Datenbank ein
	global $id, $system_farbe, $t;
	
	$text = "";
	if (!$daten['id'] || !$f_userid) {
		$fehlermeldung .= $t['freunde_fehlermeldung_fehler_beim_hinzufuegen'];
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		// Prüfen ob Freund bereits in Tabelle steht
		$daten['id'] = escape_string($daten['id']);
		$f_userid = escape_string($f_userid);
		$query = "SELECT f_id from freunde WHERE (f_userid=$daten[id] AND f_freundid=$f_userid) OR (f_userid=$f_userid AND f_freundid=$daten[id])";
		
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) > 0) {
			$fehlermeldung .= str_replace("%u_nick%", $daten['u_nick'], $t['freunde_fehlermeldung_bereits_als_freund_vorhanden']);
			$text .= hinweis($fehlermeldung, "fehler");
		} else if ($daten['id'] == $f_userid) {
			// Eigener Freund ist verboten
			$fehlermeldung .= $t['freunde_fehlermeldung_selbst_als_freund'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		
		if($text == "") {
			// Benutzer ist noch kein Freund -> hinzufügen
			$f['f_userid'] = $f_userid;
			$f['f_freundid'] = $daten['id'];
			$f['f_text'] = $daten['f_text'];
			$f['f_status'] = "beworben";
			schreibe_db("freunde", $f, 0, "f_id");
			
			$betreff = $t['freunde_freundesanfrage'];
			$email_text = str_replace("%u_nick%", $daten['u_nick'], $t['freunde_freundesanfrage_email']);
			
			mail_sende($f_userid, $daten['id'], $email_text, $betreff);
			if (ist_online($daten['id'])) {
				$msg = str_replace("%u_nick%", $daten['u_nick'], $t['freunde_freundesanfrage_chat']);
				system_msg("", 0, $daten['id'], $system_farbe, $msg);
			}
			
			$erfolgsmeldung .= str_replace("%u_nick%", $daten['u_nick'], $t['freunde_erfolgsmeldung_freundesanfrage']);
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
			
			$daten['u_nick'] = "";
			$daten['f_text'] = "";
		}
	}
	return $text;
}

function edit_freund($daten) {
	// Ändert den Infotext beim Freund
	global $t;
	
	unset($f);
	$f['f_text'] = $daten['f_text'];
	schreibe_db("freunde", $f, $daten['id'], "f_id");
	
	$text = $t['freunde_erfolgsmeldung_freund_text_geaendert'];
	
	return $text;
}

function bestaetige_freund($f_userid, $u_id) {
	global $t;
	
	$text = "";
	$f_userid = escape_string($f_userid);
	$daten = escape_string($daten);
	$query = "UPDATE freunde SET f_status = 'bestaetigt', f_zeit = NOW() WHERE f_userid = '$f_userid' AND f_freundid = '$u_id'";
	sqlUpdate($query);
	$query = "SELECT `u_nick` FROM `user` WHERE `u_id`='$f_userid'";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) != 0) {
		$f_nick = mysqli_result($result, 0, 0);
		$text = str_replace("%u_nick%", $f_nick, $t['freunde_erfolgsmeldung_freundschaft_bestaetigt']);
	}
	return $text;
}
?>