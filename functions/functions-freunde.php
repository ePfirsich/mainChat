<?php

function zeige_freunde($aktion, $zeilen) {
	// Zeigt Liste der Freunde an
	global $id, $u_nick, $u_id, $t;
	
	$text = '';
	
	switch ($aktion) {
		case "normal":
		default:
			$query = "SELECT f_id,f_text,f_userid,f_freundid,f_zeit,date_format(f_zeit,'%d.%m.%y %H:%i') as zeit FROM freunde WHERE f_userid=$u_id AND f_status = 'bestaetigt' "
				. "UNION "
				. "SELECT f_id,f_text,f_userid,f_freundid,f_zeit,date_format(f_zeit,'%d.%m.%y %H:%i') as zeit FROM freunde WHERE f_freundid=$u_id AND f_status = 'bestaetigt' ORDER BY f_zeit desc ";
				$box = $t['freunde_meine_freunde'];
			break;
		
		case "bestaetigen":
			$query = "SELECT f_id,f_text,f_userid,f_freundid, date_format(f_zeit,'%d.%m.%y %H:%i') AS zeit FROM freunde WHERE f_freundid=$u_id AND f_status='beworben' ORDER BY f_zeit desc";
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
					$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, date_format(u_login,'%d.%m.%y %H:%i') AS login, "
						. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$row->f_userid ";
						$result2 = sqlQuery($query);
				} elseif ($row->f_freundid != $u_id) {
					$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, date_format(u_login,'%d.%m.%y %H:%i') AS login, "
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
				
				$text .= "<tr>"
					."<td style=\"text-align:center;\" $bgcolor>" . $auf . "<input type=\"checkbox\" name=\"f_freundid[]\" value=\"" . $freundid . "\">" . "<input type=\"hidden\" name=\"f_nick[]\" value=\"" . $row2->u_nick . "\"></td>"
					. "<td style=\"width:15px; text-align:center\" $bgcolor>$status</td>"
					. "<td $bgcolor>" . $auf . $txt . $zu . "</td>"
					. "<td $bgcolor>" . $auf . $infotext . $zu . "</td>"
					. "<td style=\"text-align:center;\" $bgcolor>" . $auf . $row->zeit . $zu . "</td>"
					. "<td style=\"text-align:center;\" $bgcolor>" . $auf . "<a href=\"inhalt.php?bereich=freunde&id=$id&aktion=editinfotext&editeintrag=$row->f_id\">[$t[freunde_aendern]]</a>" . $zu . "</td>"
					. "</tr>\n";

				$i++;
			}
			
			$text .= "<tr>"
				."<td $bgcolor colspan=\"2\"><input type=\"checkbox\" onClick=\"toggle(this.checked)\">Alle Auswählen</td>\n";
			$text .= "<td style=\"text-align:right;\" $bgcolor colspan=\"4\">";
			if ($aktion == "bestaetigen") {
				$text .= "<input type=\"submit\" name=\"los\" value=\"$t[freunde_bestaetigen]\">";
			}
			$text .= "<input type=\"submit\" name=\"los\" value=\"$t[freunde_loeschen]\">"
				. "</td></tr></table>\n";
		}
		
		$text .= "</form>\n";
		
		zeige_tabelle_zentriert($box,$text);
		
		echo "<br>";
	}
}

function loesche_freund($f_freundid, $f_userid) {
	// Löscht Freund aus der Tabelle mit f_userid und f_freundid
	// $f_userid Benutzer-ID 
	// $f_freundid Benutzer-ID
	global $id, $mysqli_link, $u_nick, $u_id, $t;
	
	if (!$f_userid || !$f_freundid) {
		echo "Fehler beim Löschen des Freundes '$f_nick': $f_userid,$f_freundid!<br>";
		return (0);
	}
	$f_freundid = mysqli_real_escape_string($mysqli_link, $f_freundid);
	$f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
	
	$query = "DELETE from freunde WHERE (f_userid=$f_userid AND f_freundid=$f_freundid) OR (f_userid=$f_freundid AND f_freundid=$f_userid)";
	$result = sqlUpdate($query);
	
	$query = "SELECT `u_nick` FROM `user` WHERE `u_id`=$f_freundid";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) != 0) {
		$f_nick = mysqli_result($result, 0, 0);
		
		$value = str_replace("%user%", $f_nick, $t['freunde_erfolgsmeldung_freund_geloescht']);
	}
	mysqli_free_result($result);
	
	return $value;
}

function formular_neuer_freund($neuer_freund) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Freund aus
	global $id, $t;
	
	$box = $t['freunde_hinzufuegen'];
	$text = '';
	
	$text .= "<form name=\"freund_neu\" action=\"inhalt.php?bereich=freunde\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"neu2\">\n";
	$text = "<table style=\"width:100%;\">";
	
	$zaehler = 0;
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $t['freunde_benutzername'], "neuer_freund[u_nick]", $neuer_freund['u_nick']);
	$zaehler++;
	
	// Info
	$text .= zeige_formularfelder("input", $zaehler, $t['freunde_info'], "neuer_freund[f_text]", htmlentities($neuer_freund['f_text']));
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"los\" value=\"$t[freunde_eintragen]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	$text .= "</form>\n";
	
	zeige_tabelle_zentriert($box,$text);
}

function formular_editieren($f_id, $f_text) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Freund aus
	global $id, $t;
	
	$box = $t['freunde_freundestext_aendern'];
	
	$text = '';
	$text .= "<form name=\"freund_neu\" action=\"inhalt.php?bereich=freunde\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"editinfotext2\">\n"
		. "<input type=\"hidden\" name=\"f_id\" value=\"$f_id\">\n";
	
	$text .= "<b>Infotext:</b> <input type=\"text\" name=\"f_text\" value=\"" . htmlentities($f_text) . "\" size=\"45\">" . "&nbsp;" . "<input type=\"submit\" name=\"los\" value=\"$t[freunde_aendern]\"></form>\n";
	
	zeige_tabelle_zentriert($box,$text);
}

function neuer_freund($f_userid, $freund) {
	// Trägt neuen Freund in der Datenbank ein
	global $id, $mysqli_link, $system_farbe, $t;
	
	$back = "";
	if (!$freund['u_id'] || !$f_userid) {
		$back .= $t['freunde_fehlermeldung_fehler_beim_hinzufuegen'];
	} else {
		// Prüfen ob Freund bereits in Tabelle steht
		$freund['u_id'] = mysqli_real_escape_string($mysqli_link, $freund['u_id']);
		$f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
		$query = "SELECT f_id from freunde WHERE (f_userid=$freund[u_id] AND f_freundid=$f_userid) OR (f_userid=$f_userid AND f_freundid=$freund[u_id])";
		
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) > 0) {
			$back .= str_replace("%nickname%", $freund['u_nick'], $t['freunde_fehlermeldung_bereits_als_freund_vorhanden']);
		} else if ($freund['u_id'] == $f_userid) {
			// Eigener Freund ist verboten
			$back .= $t['freunde_fehlermeldung_selbst_als_freund'];
		}
		
		if($back == "") {
			// Benutzer ist noch kein Freund -> hinzufügen
			$f['f_userid'] = $f_userid;
			$f['f_freundid'] = $freund['u_id'];
			$f['f_text'] = $freund['f_text'];
			$f['f_status'] = "beworben";
			schreibe_db("freunde", $f, 0, "f_id");
			
			$betreff = $t['freunde_freundesbewerbung'];
			$text = str_replace("%nickname%", $freund['u_nick'], $t['freunde_freundesbewerbung_text']);
			
			mail_sende($f_userid, $freund['u_id'], $text, $betreff);
			if (ist_online($freund['u_id'])) {
				$msg = "Hallo $freund[u_nick], jemand möchte Ihr Freund werden. <a href=\"inhalt.php?bereich=freunde&aktion=bestaetigen&id=<ID>\" target=\"chat\">gleich zustimmen?</a>";
				
				system_msg("", 0, $freund['u_id'], $system_farbe, $msg);
			}
			
			$back .= str_replace("%nickname%", $freund['u_nick'], $t['freunde_erfolgsmeldung_freundesanfrage']);
		}
	}
	return $back;
}

function edit_freund($f_id, $f_text) {
	// Ändert den Infotext beim Freund
	global $t;
	
	$f_id = intval($f_id);
	
	unset($f);
	$f['f_text'] = $f_text;
	schreibe_db("freunde", $f, $f_id, "f_id");
	
	$back = $t['freunde_erfolgsmeldung_freund_text_geaendert'];
	
	return $back;
}

function bestaetige_freund($f_userid, $freund) {
	global $mysqli_link, $t;
	
	$f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
	$freund = mysqli_real_escape_string($mysqli_link, $freund);
	$query = "UPDATE freunde SET f_status = 'bestaetigt', f_zeit = NOW() WHERE f_userid = '$f_userid' AND f_freundid = '$freund'";
	sqlUpdate($query);
	$query = "SELECT `u_nick` FROM `user` WHERE `u_id`='$f_userid'";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) != 0) {
		$f_nick = mysqli_result($result, 0, 0);
		$back = str_replace("%nickname%", $f_nick, $t['freunde_erfolgsmeldung_freundschaft_bestaetigt']);
	}
	return $back;
}
?>