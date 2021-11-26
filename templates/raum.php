<?php
if (isset($f['r_name'])) {
	// In Namen die Leerzeichen und ' und " entfernen
	$ersetzen = array("'", "\"", "\\");
	$f['r_name'] = str_replace($ersetzen, "", $f['r_name']);
	
	// html unterdrücken
	$f['r_name'] = htmlspecialchars($f['r_name']);
	$f['r_topic'] = htmlspecialchars($f['r_topic']);
	$f['r_eintritt'] = htmlspecialchars($f['r_eintritt']);
	$f['r_austritt'] = htmlspecialchars($f['r_austritt']);
	
	// In Permanenten Räumen darf ein RB keine Punkte Ändern
	if (!$admin && $f['r_status2'] == "P") {
		echo "<p>" . $t['fehler14'] . "</p>\n";
		unset($f['r_min_punkte']);
	}
	
	// Nur Admin darf Nicht-Temporäre Räume setzen
	if (!$admin && $f['r_status2'] == "P") {
		echo "<p>" . str_replace("%r_status%", $raumstatus2[$f['r_status2']], $t['fehler10']) . "</p>\n";
		unset($f['r_status2']);
	}
	
	// Status moderiert nur falls kommerzielle Version
	if (isset($f['r_status1']) && (strtolower($f['r_status1']) == "m") && $moderationsmodul == 0) {
		echo "<p>" . str_replace("%r_status%", $raumstatus1[$f['r_status1']], $t['fehler11']) . "</p>\n";
		unset($f['r_status1']);
	}
	
	// Nur Admin darf andere Stati als offen oder geschlossen setzen
	if (isset($f['r_status1']) && $f['r_status1'] != "O" && $f['r_status1'] != "G" && !$admin) {
		$tmp = str_replace("%r_status%", $raumstatus1[$f['r_status1']], $t['fehler10']);
		echo "<p>" . str_replace("%r_name%", $f['r_name'], $tmp) . "</p>\n";
		unset($f['r_status1']);
	}
	
	// Prüfen ob Mindestpunkte zwischen 0 und 99.999.999
	if (isset($f['r_min_punkte']) && ($f['r_min_punkte']) && ($f['r_min_punkte'] < 0 || $f['r_min_punkte'] > 99999999)) {
		echo "<p>" . $t['fehler13'] . "</p>\n";
		unset($f['r_min_punkte']);
	}
}

// Besitzer des Raumes und Original Raumname ermitteln
$r_besitzer = 0;

if (!isset($f['r_id'])) {
	$f['r_id'] = "";
}
if (!isset($f['r_name'])) {
	$f['r_name'] = "";
}
if (!isset($neu)) {
	$neu = false;
}
$f['r_id'] = mysqli_real_escape_string($mysqli_link, $f['r_id']);
$f['r_name'] = mysqli_real_escape_string($mysqli_link, $f['r_name']);

$query = "SELECT r_id, r_besitzer, r_name FROM raum WHERE r_id = '$f[r_id]'";
$result = mysqli_query($mysqli_link, $query);
$num = mysqli_num_rows($result);
if ($num == 1) {
	$row = mysqli_fetch_object($result);
	$r_besitzer = $row->r_besitzer;
	if ($row->r_name == $lobby) {
		$f['r_name'] = $lobby;
	}
}

// Gibt es den Raumnamen schon? Wenn ja dann Raum nicht speichern damit keine doppelten Raumnamen entstehen
$query = "SELECT r_id, r_besitzer FROM raum WHERE r_name = '$f[r_name]' AND r_id != '$f[r_id]'";
$result = mysqli_query($mysqli_link, $query);
$num = mysqli_num_rows($result);
if ($num >= 1) {
	$f['r_id'] = "";
	$f['f_name'] = "";
}

// Änderungen in DB eintragen, falls gesetzt und Admin oder Besitzer
// bei keinen Admins ist nur temporär erlaubt
if ($f['r_id'] != "" && strlen($f['r_name']) > 3 && strlen($f['r_name']) < $raum_max && $los == "$t[sonst9]" && !$neu && ($admin || $u_id == $r_besitzer)) {
	schreibe_db("raum", $f, $f['r_id'], "r_id");
}

if (!isset($loesch)) {
	$loesch = "";
}
if (!isset($loesch2)) {
	$loesch2 = "";
}

// Raum neu eintragen und in Raum gehen
// bei keinen Admins ist nur temporär erlaubt
if (strlen($f['r_name']) > 3 && strlen($f['r_name']) < $raum_max && $los == "$t[sonst9]" && $loesch != "$t[sonst4]" && $neu && ($u_level != "G")) {
	// Voreinstellungen für den Raumstatus
	if (!$f['r_status1']) {
		$f['r_status1'] = "O";
	}
	if (!$f['r_status2']) {
		$f['r_status2'] = "T";
	}
	
	// gibts den Raum schon?
	$query = "SELECT r_id FROM raum " . "WHERE r_name LIKE '$f[r_name]' ";
	$result = mysqli_query($mysqli_link, $query);
	$rows = mysqli_num_rows($result);
	
	if ($rows == 0) {
		// Raum neu eintragen und in Raum gehen
		$raum_neu = schreibe_db("raum", $f, "", "r_id");
		$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $raum_neu, TRUE);
		raum_user($o_raum, $u_id);
	} else {
		echo "<p>" . str_replace("%r_name%", $f['r_name'], $t['fehler0']) . "</p>\n";
	}
	mysqli_free_result($result);
	
}

// Raum löschen
if ($los != "$t[sonst9]" && $loesch == "$t[sonst4]") {
	$aktion = "loesch";
}

// Raum löschen abgebrochen
if ($loesch2 == "$t[sonst5]") {
	$aktion = "";
}

// Fehlermeldung für Raum-anlegen
if (strlen($f['r_name']) <= 3 && $los == "$t[sonst9]"
	&& $loesch != "$t[sonst4]" && $neu) {
	echo $t['fehler1'];
}
if (strlen($f['r_name']) >= $raum_max) {
	echo $t['fehler2'];
}

$eingabe_breite = 54;

// Auswahl
switch ($aktion) {
	
	case "loesch2":
	// Raum löschen
		$query = "SELECT raum.*,u_id FROM raum left join user on r_besitzer=u_id WHERE r_id=$f[r_id] ";
		
		$result = mysqli_query($mysqli_link, $query);
		
		if ($result AND mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_object($result);
			
			// Berechtigung prüfen, alle Benutzer in Lobby werfen und löschen
			if ($admin || ($row->r_besitzer == $u_id)) {
				// Lobby suchen
				$query = "SELECT r_id FROM raum WHERE r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "'";
				$result2 = mysqli_query($mysqli_link, $query);
				if ($result2 AND mysqli_num_rows($result2) > 0) {
					$lobby_id = mysqli_result($result2, 0, "r_id");
				}
				mysqli_free_result($result2);
				
				// Raum ist nicht Lobby -> Löschen
				if ($f['r_id'] == $lobby_id) {
					echo "<p>" . str_replace("%r_name%", $row->r_name, $t['fehler12']) . "</p>\n";
				} else {
					
					// Raum schließen
					$f['r_status1'] = "G";
					schreibe_db("raum", $f, $f['r_id'], "r_id");
					
					// Raum leeren
					$query = "SELECT o_user,o_name FROM online WHERE o_raum=$f[r_id] ";
					
					$result2 = mysqli_query($mysqli_link, $query);
					while ($row2 = mysqli_fetch_object($result2)) {
						system_msg("", 0, $row2->o_user, $system_farbe, str_replace("%r_name%", $row->r_name, $t['fehler4']));
						$oo_raum = raum_gehe($o_id, $row2->o_user, $row2->o_name, $f['r_id'], $lobby_id, FALSE);
						raum_user($lobby_id, $row2->o_user);
						$i++;
					}
					mysqli_free_result($result2);
					
					$query = "DELETE FROM raum WHERE r_id=$f[r_id] ";
					$result2 = mysqli_query($mysqli_link, $query);
					@mysqli_free_result($result2);
					
					// Gesperrte Räume löschen
					$query = "DELETE FROM sperre WHERE s_raum=$f[r_id]";
					$result2 = mysqli_query($mysqli_link, $query);
					@mysqli_free_result($result2);
					
					// ausgeben: Der Raum wurde gelöscht.
					echo "<p>" . str_replace("%r_name%", $row->r_name, $t['fehler3']) . "</p>\n";
				}
				
			} else {
				echo "<p>" . str_replace("%r_name%", $row->r_name, $t['fehler5']) . "</p>\n";
			}
			
			mysqli_free_result($result);
			
		} else {
			echo "<p>$t[fehler6]</p>\n";
		}
		break;
	
	case "loesch":
	// Raum löschen
		$text = "";
		$query = "SELECT raum.*,u_id FROM raum LEFT JOIN user ON r_besitzer=u_id WHERE r_id=$f[r_id] ";
		
		$result = mysqli_query($mysqli_link, $query);
		
		if ($result AND mysqli_num_rows($result) > 0) {
			// Kopf Tabelle
			$box = $t['sonst6'];
			
			// Raum zeigen
			$row = mysqli_fetch_object($result);
			$text .= "<table style=\"width:100%;\"><tr>\n";
			$text .= "<td><b>$t[raeume_raum] $row->r_name</b></td></tr>\n";
			$text .= "<tr><td colspan=\"2\">" . $f1 . "<b>$t[raeume_topic]</b> " . htmlspecialchars($row->r_topic) . $f2 . "</td></tr>\n";
			$text .= "<tr><td colspan=\"2\">" . $f1 . "<b>$t[raeume_eintritt]</b> " . htmlspecialchars($row->r_eintritt) . $f2 . "</td></tr>\n";
			$text .= "</table>\n";
			
			// Formular löschen
			$text .= "<p>$t[fehler7]</p>\n";
			$text .= "<form name=\"$row->r_name\" action=\"inhalt.php?seite=raum\" method=\"post\">\n"
				. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"f[r_id]\" value=\"$row->r_id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"loesch2\">"
				. "<input type=\"submit\" name=\"loesch2\" value=\"$t[sonst4]\">"
				. "&nbsp;"
				. "<input type=\"submit\" name=\"loesch2\" value=\"$t[sonst5]\">"
				. "</form>\n";
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
			
			mysqli_free_result($result);
			
		} else {
			echo "<p>$t[fehler8]</p>\n";
		}
		break;
	
	case "neu":
	// Raum neu anlegen, Voreinstellungen
		$r_status1 = "O";
		$r_status2 = "T";
		$r_smilie = "Y";
		$r_min_punkte = "0";
		
		$text = "";
		
		if (!$admin) {
			
			$result = mysqli_query($mysqli_link, "SELECT `u_punkte_gesamt` FROM `user` WHERE `u_id`=$u_id");
			if ($result && mysqli_num_rows($result) == 1) {
				$u_punkte_gesamt = mysqli_result($result, 0, 0);
			}
			
			if (isset($raumanlegenpunkte) && $u_punkte_gesamt < $raumanlegenpunkte) {
				$text .= str_replace("%punkte%", $raumanlegenpunkte, $t['fehler15']);
				break;
			}
		}
		
		if (!isset($f['r_name']))
			$r_name = htmlspecialchars($u_nick . $t['chat_msg56']);
		if (!isset($f['r_topic']))
			$r_topic = htmlspecialchars($u_nick . $t['chat_msg56']);
		
		// In Namen die Leerzeichen und ' entfernen
		$ersetzen = array("'", " ", "\\");
		$f['r_name'] = str_replace($ersetzen, "", $f['r_name']);
		
		if (!isset($r_name)) {
			$r_name = "NeuerRaum";
		}
		
		$box = $t['raum_menue2'];
			
		// Raum neu anlegen, Eingabeformular
		$text .= "<form name=\"$r_name\" action=\"inhalt.php?seite=raum\" method=\"post\">\n";
		$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
		$text .= "<table style=\"width:100%;\">\n";
		
		$text .= "<tr><td><b>$t[raeume_raum]</b></td>";
		$text .= "<td>" . $f1 . "<input type=\"text\" name=\"f[r_name]\" "
			. "value=\"$r_name\" size=$eingabe_breite>" . $f2
			. "</td></tr>\n";
		
		// Status1 als Auswahl
		$text .= "<tr><td>" . $f1 . "<b>" . $t['raeume_status'] . "</b>" . $f2
			. "</td>" . "<td>" . $f1 . "<select name=\"f[r_status1]\">\n";
		
		$i = 0;
		while ($i < count($raumstatus1)) {
			$keynum = key($raumstatus1);
			$status1 = $raumstatus1[$keynum];
			if ($keynum == $r_status1) {
				$text .= "<option selected value=\"$keynum\">$status1\n";
			} else {
				$text .= "<option value=\"$keynum\">$status1\n";
			}
			next($raumstatus1);
			$i++;
		}
		
		$text .= "</select>" . $f2 . "</td></tr>\n";
		
		// Status2 als Auswahl
		$text .= "<tr><td>" . $f1 . "<b>" . $t['raeume_art'] . "</b>" . $f2
			. "</td>" . "<td>" . $f1 . "<select name=\"f[r_status2]\">\n";
		
		$i = 0;
		while ($i < count($raumstatus2)) {
			$keynum = key($raumstatus2);
			$status2 = $raumstatus2[$keynum];
			if ($keynum == $r_status2) {
				$text .= "<option selected value=\"$keynum\">$status2\n";
			} else {
				$text .= "<option value=\"$keynum\">$status2\n";
			}
			next($raumstatus2);
			$i++;
		}
		
		$text .= "</select>" . $f2 . "</td></tr>\n";
		
		$text .= "<tr><td>" . $f1 . "<b>" . $t['raeume_smilies'] . "</b>" . $f2
			. "</td><td>" . $f1 . "<select name=\"f[r_smilie]\">";
		if ($r_smilie == "Y") {
			$text .= "<option selected value=\"Y\">$t[raum_erlaubt]";
			$text .= "<option value=\"N\">$t[raum_verboten]";
		} else {
			$text .= "<option value=\"Y\">$t[raum_erlaubt]";
			$text .= "<option selected value=\"N\">$t[raum_verboten]";
		}
		$text .= "</select>" . $f2 . "</td></tr>\n";
		
		// Punkte zum Betreten des Raumes
		$text .= "<tr><td>" . $f1 . "<b>$t[raeume_mindestpunkte]</b>" . $f2 . "</td>";
		$text .= "<td>" . $f1
			. "<input type=\"text\" name=\"f[r_min_punkte]\" "
			. "value=\"$r_min_punkte\" size=\"7\">" . $f2
			. "</td></tr>\n";
		
		if (!isset($rows->r_topic)) {
			$r_topic = "";
		} else {
			$r_topic = $rows->r_topic;
		}
		if (!isset($rows->r_eintritt)) {
			$r_eintritt = "";
		} else {
			$r_eintritt = $rows->r_eintritt;
		}
		if (!isset($rows->r_austritt)) {
			$r_austritt = "";
		} else {
			$r_austritt = $rows->r_austritt;
		}
		
		$text .= "<tr><td>" . $f1 . "<b>" . $t['raeume_topic'] . "</b>" . $f2
			. "</td>" . "<td>" . $f1 . "<textarea rows=5 cols="
			. ($eingabe_breite) . " name=\"f[r_topic]\">"
			. $r_topic . "</textarea>" . $f2 . "</td></tr>\n";
		$text .= "<tr style=\"vertical-align:top;\"><td>" . $f1 . "<b>" . $t['raeume_eintritt']
			. "</b>" . $f2 . "</td>" . "<td>" . $f1
			. "<textarea rows=5 cols=" . ($eingabe_breite)
			. " name=\"f[r_eintritt]\">" . $r_eintritt
			. "</textarea>" . $f2 . "</td></tr>\n";
		$text .= "<tr style=\"vertical-align:top;\"><td>" . $f1 . "<b>" . $t['raeume_austritt']
			. "</b>" . $f2 . "</td>" . "<td>" . $f1
			. "<textarea rows=5 cols=" . ($eingabe_breite)
			. " name=\"f[r_austritt]\">" . $r_austritt
			. "</textarea>" . $f2 . "</td></tr>\n";
		
		$text .= "<tr><td colspan=\"2\">" . $f1
			. "<input type=\"submit\" name=\"los\" value=\"$t[sonst9]\">"
			. $f2 . "</td></tr>\n";
		$text .= "</table>\n";
		$text .= "<input type=\"hidden\" name=\"neu\" value=\"1\">";
		$text .= "<input type=\"hidden\" name=\"f[r_besitzer]\" value=\"$u_id\">";
		$text .= "</form>\n";
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
		
		break;
	
	case "edit":
		// kein eigener Fall, wird in default abgehandelt. 
	default;
		// Alle Räume
	$text = "";
		if (!isset($order))
			$order = "r_name";
		
		// Anzeige aller Räume als Liste oder eines Raums im Editor
		if ($aktion == "edit") {
			$text = "";
			$query = "SELECT raum.*,u_id,u_nick "
				. "FROM raum left join user on r_besitzer=u_id "
				. "WHERE r_id=" . intval($raum) . " ORDER BY $order";
			$result = mysqli_query($mysqli_link, $query);
			
			while ($rows = mysqli_fetch_object($result)) {
				// Ausgabe in Tabelle
				if ($admin || $rows->u_id == $u_id) {
					$box = $t['raeume_raum'] . ': ' . $rows->r_name;
					// für diesen Raum Admin
					$text .= "<form name=\"$rows->r_name\" action=\"inhalt.php?seite=raum\" method=\"post\">\n";
					$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
					$text .= "<table style=\"width:100%;\">\n";
					
					$text .= "<tr><td><b>$t[raeume_raum]</b></td>";
					if ($rows->r_name == $lobby) {
						$text .= "<td><b>$rows->r_name</b>"
							. "<input type=\"hidden\" name=\"f[r_name]\" "
							. "value=\"$rows->r_name\" size=$eingabe_breite>"
							. $f2 . "</td></tr>\n";
					} else {
						$text .= "<td>" . $f1
							. "<input type=\"text\" name=\"f[r_name]\" "
							. "value=\"$rows->r_name\" size=$eingabe_breite>"
							. $f2 . "</td></tr>\n";
						
							$text .= "<tr><td>" . $f1 . "<b>" . $t['raeume_raumbesitzer']
							. "</b>" . $f2 . "</td>" . "<td>" . $f1
							. $rows->u_nick . $f2 . "</td></tr>\n";
						
						// Status1 als Auswahl
						$text .= "<tr><td>" . $f1 . "<b>" . $t['raeume_status']
							. "</b>" . $f2 . "</td>" . "<td>" . $f1
							. "<select name=\"f[r_status1]\">\n";
						
						$a = 0;
						reset($raumstatus1);
						while ($a < count($raumstatus1)) {
							$keynum = key($raumstatus1);
							$status1 = $raumstatus1[$keynum];
							if ($keynum == $rows->r_status1) {
								$text .= "<option selected value=\"$keynum\">$status1\n";
							} else {
								$text .= "<option value=\"$keynum\">$status1\n";
							}
							next($raumstatus1);
							$a++;
						}
						
						$text .= "</select>" . $f2 . "</td></tr>\n";
						
						// Status2 als Auswahl
						$text .= "<tr><td>" . $f1 . "<b>" . $t['raeume_art']
							. "</b>" . $f2 . "</td>" . "<td>" . $f1
							. "<select name=\"f[r_status2]\">\n";
						
						$a = 0;
						reset($raumstatus2);
						while ($a < count($raumstatus2)) {
							$keynum = key($raumstatus2);
							$status2 = $raumstatus2[$keynum];
							if ($keynum == $rows->r_status2) {
								$text .= "<option selected value=\"$keynum\">$status2\n";
							} else {
								$text .= "<option value=\"$keynum\">$status2\n";
							}
							next($raumstatus2);
							$a++;
						}
						
						$text .= "</select>" . $f2 . "</td></tr>\n";
						
					}
					
					$text .= "<tr><td>" . $f1 . "<b>" . $t['raeume_smilies']
						. "</b>" . $f2 . "</td><td>" . $f1
						. "<select name=\"f[r_smilie]\">";
					if ($rows->r_smilie == "Y") {
						$text .= "<option selected value=\"Y\">$t[raum_erlaubt]";
						$text .= "<option value=\"N\">$t[raum_verboten]";
					} else {
						$text .= "<option value=\"Y\">$t[raum_erlaubt]";
						$text .= "<option selected value=\"N\">$t[raum_verboten]";
					}
					$text .= "</select>" . $f2 . "</td></tr>\n";
					
					if ($rows->r_name != $lobby) {
						// Punkte zum Betreten des Raumes ändern
						$text .= "<tr><td>" . $f1 . "<b>$t[raeume_mindestpunkte]</b>" . $f2 . "</td>";
						$text .= "<td>" . $f1
							. "<input type=\"text\" name=\"f[r_min_punkte]\" "
							. "value=\"$rows->r_min_punkte\" size=\"7\">"
							. $f2 . "</td></tr>\n";
					}
					
					$text .= "<tr style=\"vertical-align:top;\"><td>" . $f1 . "<b>"
						. $t['raeume_topic'] . "</b>" . $f2 . "</td>" . "<td>"
						. $f1 . "<textarea rows=5 cols="
						. ($eingabe_breite) . " name=\"f[r_topic]\">"
						. $rows->r_topic . "</textarea>"
						. $f2 . "</td></tr>\n";
						$text .= "<tr style=\"vertical-align:top;\"><td>" . $f1 . "<b>"
						. $t['raeume_eintritt'] . "</b>" . $f2 . "</td>" . "<td>"
						. $f1 . "<textarea rows=5 cols="
						. ($eingabe_breite) . " name=\"f[r_eintritt]\">"
						. $rows->r_eintritt . "</textarea>"
						. $f2 . "</td></tr>\n";
					$text .= "<tr style=\"vertical-align:top;\"><td>" . $f1 . "<b>"
						. $t['raeume_austritt'] . "</b>" . $f2 . "</td>" . "<td>"
						. $f1 . "<textarea rows=5 cols="
						. ($eingabe_breite) . " name=\"f[r_austritt]\">"
						. $rows->r_austritt . "</textarea>"
						. $f2 . "</td></tr>\n";
					
					$text .= "<tr><td colspan=\"2\">" . $f1
						. "<input type=\"submit\" name=\"los\" value=\"$t[sonst9]\">"
						. "&nbsp;&nbsp;"
						. "<input type=\"submit\" name=\"loesch\" value=\"$t[sonst4]\">"
						. $f2 . "</td></tr>\n";
					
					$text .= "</table>\n";
					
					$text .= "<input type=\"hidden\" name=\"f[r_id]\" value=\"$rows->r_id\">";
					$text .= "<input type=\"hidden\" name=\"f[r_besitzer]\" value=\"$rows->r_besitzer\">";
					$text .= "</form>\n";
					
				} else {
					$box = $t['raeume_raum'] . ': ' . $rows->r_name;
					// Normalsterblicher
					$text .= "<table style=\"width:100%;\">\n";
					if ($rows->r_name) {
						$text .= "<tr><td><b>$t[raeume_raum] $rows->r_name</b></td></tr>\n";
					}
					if ($rows->r_topic) {
						$text .= "<tr><td colspan=\"2\">" . $f1
						. "<b>$t[raeume_topic]</b> "
							. htmlspecialchars($rows->r_topic)
							. $f2 . "</td></tr>\n";
					}
					if ($rows->r_eintritt) {
						$text .= "<tr><td colspan=\"2\">" . $f1 . "<b>$t[raeume_eintritt]</b> " . htmlspecialchars($rows->r_eintritt) . $f2 . "</td></tr>\n";
					}
					$text .= "</tr></table>\n";
					
				}
				
			}
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
			
			mysqli_free_result($result);
		} else {
			$text = "";
			$box = $t['titel'];
			// Liste der Räume mit der Anzahl der Benutzer aufstellen
			$query = "SELECT r_id,count(o_id) as anzahl FROM raum "
				. "LEFT JOIN online ON r_id=o_raum "
				. "WHERE ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout OR o_id IS NULL) "
				. "GROUP BY r_id";
			
			//system_msg("",0,$u_id,"#000000","Debug: ".$query);
			$result = mysqli_query($mysqli_link, $query);
			
			while ($row = mysqli_fetch_object($result)) {
				$anzahl_user[$row->r_id] = $row->anzahl;
			}
			mysqli_free_result($result);
			
			// Liste der Räume und der Raumbesitzer lesen
			$query = "SELECT raum.*,u_id,u_nick,u_level,u_punkte_gesamt,u_punkte_gruppe "
				. "FROM raum left join user on r_besitzer=u_id GROUP BY r_name ORDER BY $order";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) > 0) {
				
				// extended==Ansicht mit Details im extra beiten Fenster
				if ($admin) {
					if (isset($extended) && ($extended == 1)) {
						$rlink = "<center>" . $f1
							. "<b><a href=\"inhalt.php?seite=raum&id=$id&order=$order\">"
							. $t['raum_menue7'] . "</a></b>" . $f2
							. "</center>\n";
						$text .= "<script language=\"javascript\">\n"
							. "window.resizeTo(800,600); window.focus();"
							. "</script>\n";
					} else {
						$rlink = "<center>" . $f1
							. "<b><a href=\"inhalt.php?seite=raum&id=$id&order=$order&extended=1\">"
							. $t['raum_menue6'] . "</a></b>" . $f2
							. "</center>\n";
					}
					$text .= "$rlink<br>";
				}
				$rlink = "<a href=\"inhalt.php?seite=raum&id=$id&order=r_name\">" . $t['raeume_raum'] . "</a>";
				$text .= "<table class=\"tabelle_kopf\">\n";
				$text .= "<tr><td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b></small></td>";
				$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $t['raeume_benutzer_online'] . "</b></small></td>";
				
				$rlink = "<a href=\"inhalt.php?seite=raum&id=$id&order=r_status1,r_name\">" . $t['raeume_status'] . "</a>";
				$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b>&nbsp;</small></td>";
				
				$rlink = "<a href=\"inhalt.php?seite=raum&id=$id&order=r_status2,r_name\">" . $t['raeume_art'] . "</a>";
				$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b>&nbsp;</small></td>";
				
				$rlink = "<a href=\"inhalt.php?seite=raum6id=$id&order=u_nick\">" . $t['raeume_raumbesitzer'] . "</a>";
				$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b></small></td>";
				
				if (isset($extended) && $extended) {
					$rlink = $t['raeume_smilies'];
					$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b></small></td>";
					
					$rlink = $t['raeume_mindestpunkte'];
					$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b></small></td>";
					
					$rlink = $t['raeume_topic'];
					$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b></small></td>";
					$rlink = $t['raeume_eintritt'];
					$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b></small></td>";
					$rlink = $t['raeume_austritt'];
					$text .= "<td class=\"tabelle_koerper_login\"><small><b>" . $rlink . "</b></small></td>";
				}
				
				$text .= "</tr>\n";
				$i = 1;
				$bgcolor = 'class="tabelle_zeile1"';
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
					$uu_id = $row['u_id'];
					if ($row['r_status2'] == "P") {
						$b1 = "<small><b>";
						$b2 = "</b></small>";
					} else {
						$b1 = "<small>";
						$b2 = "</small>";
					}
					// raum nur anzeigen falls offen, moderiert, besitzer oder admin...
					// "m" -> moderiert, "M" -> moderiert+geschlossen.
					if ($row['r_status1'] == "O" || $row['r_status1'] == "m" || $uu_id == $u_id || $admin) {
						$rlink = "<a href=\"inhalt.php?seite=raum&id=$id&aktion=edit&raum=" . $row['r_id'] . "\">" . $row['r_name'] . "</a>";
						
						// Anzahl der Benutzer online ermitteln
						if ((isset($anzahl_user[$row['r_id']])) && ($anzahl_user[$row['r_id']] > 0)) {
							$anzahl = $anzahl_user[$row['r_id']];
						} else {
							$anzahl = 0;
						}
						$ulink = "<a href=\"user.php?id=$id&schau_raum=" . $row['r_id'] . "\">$anzahl</A>";
						
						$text .= "<tr><td $bgcolor>$b1" . $rlink . "$b2</td>";
						$text .= "<td $bgcolor>$b1" . $ulink . "$b2&nbsp;</td>";
						
						$text .= "<td $bgcolor>$b1". $raumstatus1[$row['r_status1']] . "$b2&nbsp;</td>";
						$text .= "<td $bgcolor>$b1" . $raumstatus2[$row['r_status2']] . "$b2&nbsp;</td>";
						$text .= "<td $bgcolor>$b1" . zeige_userdetails($row['u_id'], $row, false) . $b2 . "</td>";
						if ((isset($extended)) && ($extended)) {
							if ($row['r_smilie'] == "Y") {
								$r_smilie = $t['raum_erlaubt'];
							} else {
								$r_smilie = $t['raum_verboten'];
							}
							$text .= "<td $bgcolor>$b1" . $r_smilie . "&nbsp;$b2</td>";
							
							if( $row['r_min_punkte'] == "1") {
								$punkte_name = $t['raum_punkt'];
							} else {
								$punkte_name = $t['raum_punkte'];
							}
							$text .= "<td $bgcolor>$b1" . $row['r_min_punkte'] . " $punkte_name$b2</td>";
							
							$temp = htmlspecialchars($row['r_topic']);
							$temp = str_replace('&amp;lt;', '<', $temp);
							$temp = str_replace('&amp;gt;', '>', $temp);
							$temp = str_replace('&amp;quot;', '"', $temp);
							$text .= "<td $bgcolor>$b1" . $temp . "&nbsp;$b2</td>";
							
							$temp = htmlspecialchars($row['r_eintritt']);
							$temp = str_replace('&amp;lt;', '<', $temp);
							$temp = str_replace('&amp;gt;', '>', $temp);
							$temp = str_replace('&amp;quot;', '"', $temp);
							$text .= "<td $bgcolor>$b1" . $temp . "&nbsp;$b2</td>";
							
							$temp = htmlspecialchars($row['r_austritt']);
							$temp = str_replace('&amp;lt;', '<', $temp);
							$temp = str_replace('&amp;gt;', '>', $temp);
							$temp = str_replace('&amp;quot;', '"', $temp);
							$text .= "<td $bgcolor>$b1" . $temp . "&nbsp;$b2</td>";
						}
							
						$text .= "</tr>\n";
						$i++;
						if (($i % 2) > 0) {
							$bgcolor = 'class="tabelle_zeile1"';
						} else {
							$bgcolor = 'class="tabelle_zeile2"';
						}
					}
				}
				$text .= "</table>";
				
				// Box anzeigen
				zeige_tabelle_zentriert($box, $text);
				
				mysqli_free_result($result);
			}
		}
	
}
?>