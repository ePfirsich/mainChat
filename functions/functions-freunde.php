<?php

function zeige_freunde($aktion, $zeilen) {
	// Zeigt Liste der Freunde an
	global $id, $mysqli_link, $mysqli_link, $u_nick, $u_id;
	
	$text = '';
	
	switch ($aktion) {
		case "normal":
		default:
			$query = "SELECT f_id,f_text,f_userid,f_freundid,f_zeit,date_format(f_zeit,'%d.%m.%y %H:%i') as zeit FROM freunde WHERE f_userid=$u_id AND f_status = 'bestaetigt' "
				. "UNION "
				. "SELECT f_id,f_text,f_userid,f_freundid,f_zeit,date_format(f_zeit,'%d.%m.%y %H:%i') as zeit FROM freunde WHERE f_freundid=$u_id AND f_status = 'bestaetigt' "
				. "ORDER BY f_zeit desc ";
			$button = "LÖSCHEN";
			$titel1 = "hat";
			$titel2 = "bestätigte(n) Freunde";
			break;
		
		case "bestaetigen":
			$query = "SELECT f_id,f_text,f_userid,f_freundid,"
				. "date_format(f_zeit,'%d.%m.%y %H:%i') as zeit "
				. "from freunde " . "WHERE ( "
				. " f_freundid=$u_id) AND (f_status='beworben') "
				. "order by f_zeit desc";
			$button = "LÖSCHEN";
			$button2 = "BESTÄTIGEN";
			$titel1 = "hat";
			$titel2 = "noch zu bestätigende Freunde";
			break;
	}
	
	$text .= "<form name=\"freund_loeschen\" action=\"inhalt.php?seite=freunde\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"bearbeite\">\n";
	
	$result = mysqli_query($mysqli_link, $query);
	if ($result) {
		
		$anzahl = mysqli_num_rows($result);
		if ($anzahl == 0) {
			// Keine Freunde
			$box = "$u_nick $titel1 noch keine $titel2:";
			
			if ($aktion == "normal")
				$text.= "Sie haben keine Freunde eingestellt.";
			if ($aktion == "bestaetigen")
				$text.= "Sie haben keine Freunde zu bestaetigen.";
			
		} else {
			// Freunde anzeigen
			$box = "$u_nick $titel1 $anzahl $titel2:";
			
			$text .= "<table style=\"width:100%;\">";
			$text .= "<tr>"
				."<td style=\"width:5%;\" class=\"tabelle_kopfzeile\">" . "Markieren" . "</td>"
				."<td style=\"width:35%;\" class=\"tabelle_kopfzeile\">" . "Benutzername" . "</td>"
				. "<td style=\"width:35%;\" class=\"tabelle_kopfzeile\">" . "Info" . "</td>"
				."<td style=\"width:20%; text-align:center;\" class=\"tabelle_kopfzeile\">" . "Datum des Eintrags" . "</td>"
				."<td style=\"width:5%; text-align:center;\" class=\"tabelle_kopfzeile\">" . "Aktion" . "</td>"
				."</tr>\n";
			
			$i = 0;
			
			while ($row = mysqli_fetch_object($result)) {
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1 smaller"';
				} else {
					$bgcolor = 'class="tabelle_zeile2 smaller"';
				}
				
				// Benutzer aus der Datenbank lesen
				if ($row->f_userid != $u_id) {
					$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, date_format(u_login,'%d.%m.%y %H:%i') AS login, "
						. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$row->f_userid ";
					$result2 = mysqli_query($mysqli_link, $query);
				} elseif ($row->f_freundid != $u_id) {
					$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, date_format(u_login,'%d.%m.%y %H:%i') AS login, "
						. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$row->f_freundid ";
					$result2 = mysqli_query($mysqli_link, $query);
				}
				if ($result2 && mysqli_num_rows($result2) > 0) {
					
					// Benutzer gefunden -> Ausgeben
					$row2 = mysqli_fetch_object($result2);
					$freund_nick = "<b>" . zeige_userdetails($row2->u_id, $row2) . "</b>";
					
				} else {
					
					// Benutzer nicht gefunden, Freund löschen
					$freund_nick = "NOBODY";
					$query = "DELETE from freunde WHERE f_id=$row->f_id";
					$result2 = mysqli_query($mysqli_link, $query);
					
				}
				
				// Unterscheidung online ja/nein, Text ausgeben
				if ($row2->o_id) {
					$txt = $freund_nick . "<br>online&nbsp;"
						. gmdate("H:i:s", $row2->online) . "&nbsp;Std/Min/Sek";
					$auf = "<b>";
					$zu = "</b>";
				} else {
					$txt = $freund_nick . "<br>Letzter&nbsp;Login:&nbsp;"
						. str_replace(" ", "&nbsp;", $row2->login);
					$auf = "";
					$zu = "";
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
					. "<td $bgcolor>" . $auf . $txt . $zu . "</td>"
					. "<td $bgcolor>" . $auf . $infotext . $zu . "</td>"
					. "<td style=\"text-align:center;\" $bgcolor>" . $auf . $row->zeit . $zu . "</td>"
					. "<td style=\"text-align:center;\" $bgcolor>" . $auf . "<a href=\"inhalt.php?seite=freunde&id=$id&aktion=editinfotext&editeintrag=$row->f_id\">[ÄNDERN]</a>" . $zu . "</td>"
					. "</tr>\n";

				$i++;
			}
			
			$text .= "<tr>"
				."<td $bgcolor colspan=\"2\"><input type=\"checkbox\" onClick=\"toggleFreunde(this.checked)\">Alle Auswählen</td>\n";
			$text .= "<td style=\"text-align:right;\" $bgcolor colspan=\"3\">";
			if ($aktion == "bestaetigen") {
				$text .= "<input type=\"submit\" name=\"los\" value=\"$button2\">";
			}
			$text .= "<input type=\"submit\" name=\"los\" value=\"$button\">"
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
	
	global $id, $mysqli_link, $mysqli_link, $u_nick, $u_id;
	
	if (!$f_userid || !$f_freundid) {
		echo "Fehler beim Löschen des Freundes '$f_nick': $f_userid,$f_freundid!<br>";
		return (0);
	}
	$f_freundid = mysqli_real_escape_string($mysqli_link, $f_freundid);
	$f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
	
	$query = "DELETE from freunde WHERE "
		. "(f_userid=$f_userid AND f_freundid=$f_freundid) " . "OR "
		. "(f_userid=$f_freundid AND f_freundid=$f_userid)";
	$result = mysqli_query($mysqli_link, $query);
	
	$query = "SELECT `u_nick` FROM `user` WHERE `u_id`=$f_freundid";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$f_nick = mysqli_result($result, 0, 0);
		$back = "<P><b>Hinweis:</b> '$f_nick' ist nicht mehr Ihr Freund.</P>";
	}
	mysqli_free_result($result);
	return ($back);
}

function formular_neuer_freund($neuer_freund) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Freund aus
	
	global $id, $mysqli_link;
	
	$box = "Neuen Freund eintragen:";
	$text = '';
	
	$text .= "<form name=\"freund_neu\" action=\"inhalt.php?seite=freunde\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"neu2\">\n"
		. "<table>";
	
	$text .= "<tr><td style=\"text-align:right;\"><b>Benutzername:</b></td><td>"
		. "<input type=\"text\" name=\"neuer_freund[u_nick]\" value=\"" . $neuer_freund['u_nick'] . "\" size=20>"
		. "</td></tr>\n"
		. "<tr><td style=\"text-align:right;\"><b>Infotext:</b></td><td>"
		. "<input type=\"text\" name=\"neuer_freund[f_text]\" value=\"" . htmlentities($neuer_freund['f_text']) . "\" size=45>" . "&nbsp;" . "<input type=\"submit\" name=\"los\" value=\"Eintragen\">"
		. "</td></tr>\n" . "</table></form>\n";
	
		zeige_tabelle_zentriert($box,$text);
}

function formular_editieren($f_id, $f_text) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Freund aus
	global $id, $mysqli_link;
	
	$text = '';
	
	$box = "Freundestext ändern:";
	
	$text .= "<form name=\"freund_neu\" action=\"inhalt.php?seite=freunde\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"editinfotext2\">\n"
		. "<input type=\"hidden\" name=\"f_id\" value=\"$f_id\">\n";
	
	$text .= "<b>Infotext:</b> <input type=\"text\" name=\"f_text\" value=\"" . htmlentities($f_text) . "\" size=\"45\">" . "&nbsp;" . "<input type=\"submit\" name=\"los\" value=\"ÄNDERN\"></form>\n";
	
	zeige_tabelle_zentriert($box,$text);
}

function neuer_freund($f_userid, $freund) {
	// Trägt neuen Freund in der Datenbank ein
	
	global $id, $mysqli_link, $chat, $system_farbe;
	
	if (!$freund['u_id'] || !$f_userid) {
		echo "Fehler beim Anlegen des Freundes: $f_userid,$freund[u_id]!<br>";
	} else {
		
		// Prüfen ob Freund bereits in Tabelle steht
		$freund['u_id'] = mysqli_real_escape_string($mysqli_link, $freund['u_id']);
		$f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
		
		$query = "SELECT f_id from freunde WHERE "
			. "(f_userid=$freund[u_id] AND f_freundid=$f_userid) " . "OR "
			. "(f_userid=$f_userid AND f_freundid=$freund[u_id])";
		
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			
			$back = "<P><b>Fehler:</b> '$freund[u_nick]' ist bereits als Ihr Freund eingetragen!</P>\n";
			
		} elseif ($freund['u_id'] == $f_userid) {
			
			// Eigener Freund ist verboten
			$back = "<P><b>Fehler:</b> Sie können sich nicht selbst als Freund eintragen!</P>\n";
		} else {
			
			// Benutzer ist noch kein Freund -> hinzufügen
			$f['f_userid'] = $f_userid;
			$f['f_freundid'] = $freund['u_id'];
			$f['f_text'] = $freund['f_text'];
			$f['f_status'] = "beworben";
			schreibe_db("freunde", $f, 0, "f_id");
			
			$betreff = "Neue Freundesbewerbung";
			$text = "Hallo $freund[u_nick]!\nIch möchte gerne Ihr Freund im $chat werden!\n"
				. "wenn Sie das auch wollen und im $chat eingeloggt sind, so klicken Sie bitte "
				. "<a href=\"inhalt.php?seite=freunde&id=<ID>&aktion=bestaetigen\">hier</a>.\n"
				. "Sollten Sie diese E-Mail als Weiterleitung bekommen, so müssen Sie sich erst in den Chat einloggen.\n\n";
			
			mail_sende($f_userid, $freund['u_id'], $text, $betreff);
			if (ist_online($freund['u_id'])) {
				$msg = "Hallo $freund[u_nick], jemand möchte Ihr Freund werden. <a href=\"inhalt.php?seite=freunde&aktion=bestaetigen&id=<ID>\" target=\"chat\">gleich zustimmen?</a>";
				
				system_msg("", 0, $freund['u_id'], $system_farbe, $msg);
			}
			$back = "<b>Hinweis:</b> Sie bewerben sich nun bei '$freund[u_nick]' als Freund.";
			
		}
		
	}
	return ($back);
}

function edit_freund($f_id, $f_text) {
	// Ändert den Infotext beim Freund
	
	$f_id = intval($f_id);
	
	unset($f);
	$f['f_text'] = $f_text;
	schreibe_db("freunde", $f, $f_id, "f_id");
	
	$back = "<b>Hinweis:</b> Der Freundestext wurde geändert.";
	
	return ($back);
}

function bestaetige_freund($f_userid, $freund) {
	global $mysqli_link;
	
	$f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
	$freund = mysqli_real_escape_string($mysqli_link, $freund);
	$query = "UPDATE freunde SET f_status = 'bestaetigt', f_zeit = NOW() WHERE f_userid = '$f_userid' AND f_freundid = '$freund'";
	mysqli_query($mysqli_link, $query);
	$query = "SELECT `u_nick` FROM `user` WHERE `u_id`='$f_userid'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$f_nick = mysqli_result($result, 0, 0);
		$back = "<P><b>Hinweis: </b>Die Freundschaft mit '$f_nick' wurde bestätigt!</P>";
	}
	return ($back);
}
?>