<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$text = "";

$f = array();
$f['r_id'] = filter_input(INPUT_POST, 'r_id', FILTER_SANITIZE_NUMBER_INT);

// Raum löschen
if ($loesch == "$lang[raum_loeschen]") {
	$aktion = "loesch";
}

// Raum löschen abgebrochen
if ($loesch2 == $lang['raeume_zurueck']) {
	$aktion = "";
}


// Auswahl
switch ($aktion) {
	case "loesch2":
		// Raum löschen
		$query = pdoQuery("SELECT raum.*, `u_id` FROM `raum` LEFT JOIN `user` ON `r_besitzer` = `u_id` WHERE `r_id` = :r_id", [':r_id'=>$f['r_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$row = $query->fetch();
			
			// Berechtigung prüfen, alle Benutzer in Lobby werfen und löschen
			if ($admin || ($row['r_besitzer'] == $u_id)) {
				// Lobby suchen
				$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE `r_name` = :r_name", [':r_name'=>$lobby]);
				
				$result2Count = $query->rowCount();
				if ($result2Count > 0) {
					$result2 = $query->fetch();
					$lobby_id = $result2['r_id'];
				}
				
				// Raum ist nicht Lobby -> Löschen
				if ($f['r_id'] == $lobby_id) {
					$fehlermeldung = str_replace("%r_name%", $row['r_name'], $lang['raum_fehler_kann_nicht_geloescht_werden']);
					$text .= hinweis($fehlermeldung, "fehler");
				} else {
					// Raum schließen
					$f['r_status1'] = "G";
					
					pdoQuery("UPDATE `raum` SET `r_status1` = :r_status1 WHERE `r_id` = :r_id",
						[
							':r_id'=>$f['r_id'],
							':r_status1'=>$f['r_status1']
						]);
					
					// Raum leeren
					$query2 = pdoQuery("SELECT `o_user`, `o_name` FROM `online` WHERE `o_raum` = :o_raum", [':o_raum'=>$f['r_id']]);
					
					$result2 = $query2->fetchAll();
					foreach($result2 as $zaehler => $row2) {
						system_msg("", 0, $row2['o_user'], $system_farbe, str_replace("%r_name%", $row['r_name'], $lang['fehler4']));
						$oo_raum = raum_gehe($o_id, $row2['o_user'], $row2['o_name'], $f['r_id'], $lobby_id);
						raum_user($lobby_id, $row2['o_user']);
					}
					
					pdoQuery("DELETE FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$f['r_id']]);
					
					// Gesperrte Räume löschen
					pdoQuery("DELETE FROM `sperre` WHERE `s_raum` = :r_id", [':r_id'=>$f['r_id']]);
					
					// ausgeben: Der Raum wurde gelöscht.
					$erfolgsmeldung = str_replace("%r_name%", $row['r_name'], $lang['raum_erfolgsmeldung_geloescht']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				}
			} else {
				$fehlermeldung = str_replace("%r_name%", $row['r_name'], $lang['raum_fehler_loeschen_keine_berechtigung']);
				$text .= hinweis($fehlermeldung, "fehler");
			}
		} else {
			$fehlermeldung = $lang['raum_fehler_falsche_id'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		$box = str_replace("%r_name%", $row['r_name'], $lang['raum_raumname']);
		
		break;
	
	case "loesch":
		// Raum löschen
		$query = pdoQuery("SELECT raum.*, `u_id` FROM `raum` LEFT JOIN `user` ON `r_besitzer` = `u_id` WHERE `r_id` = :r_id", [':r_id'=>$f['r_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$row = $query->fetch();
			
			// Kopf Tabelle
			$box = $lang['raeume_raum_loeschen'];
			$zaehler = 0;
			$text .= "<table style=\"width:100%;\">\n";
			
			// Raum
			$text .= zeige_formularfelder("text", $zaehler, $lang['raeume_raum'], "", $row['r_name']);
			$zaehler++;
			
			// Topic
			$text .= zeige_formularfelder("text", $zaehler, $lang['raeume_topic'], "", htmlspecialchars($row['r_topic']));
			$zaehler++;
			
			
			// Eintrittsnachricht
			$text .= zeige_formularfelder("text", $zaehler, $lang['raeume_eintritt'], "", htmlspecialchars($row['r_eintritt']));
			$zaehler++;
			
			
			// Austrittsnachricht
			$text .= zeige_formularfelder("text", $zaehler, $lang['raeume_austritt'], "", htmlspecialchars($row['r_austritt']));
			$zaehler++;
			
			
			// Formular löschen
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>";
			$text .= "<td $bgcolor colspan=\"2\">$lang[raeume_raum_wirklich_loeschen]<br>\n";
			$text .= "<form action=\"inhalt.php?bereich=raum\" method=\"post\">\n";
			$text .= "<input type=\"hidden\" name=\"r_id\" value=\"$row[r_id]\">\n";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"loesch2\">\n";
			$text .= "<input type=\"submit\" name=\"loesch2\" value=\"$lang[raum_loeschen]\">&nbsp;<input type=\"submit\" name=\"loesch2\" value=\"$lang[raeume_zurueck]\">\n";
			$text .= "</form>\n";
			$text .= "</td>\n";
			$text .= "</tr>\n";
			$zaehler++;
			
			$text .= "</table>\n";
		} else {
			$fehlermeldung = $lang['raum_fehler_falsche_id'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		break;
	
	case "edit":
		// Raum anlegen und editieren
		if($formular == 1) {
			// Formular wurde abgesendet
			// Raum speichern oder anlegen
			$f['r_name'] = htmlspecialchars(filter_input(INPUT_POST, 'r_name', FILTER_SANITIZE_STRING));
			$f['r_status1'] = htmlspecialchars(filter_input(INPUT_POST, 'r_status1', FILTER_SANITIZE_STRING));
			$f['r_status2'] = htmlspecialchars(filter_input(INPUT_POST, 'r_status2', FILTER_SANITIZE_STRING));
			$f['r_smilie'] = filter_input(INPUT_POST, 'r_smilie', FILTER_SANITIZE_NUMBER_INT);
			$f['r_min_punkte'] = filter_input(INPUT_POST, 'r_min_punkte', FILTER_SANITIZE_NUMBER_INT);
			$f['r_topic'] = htmlspecialchars(filter_input(INPUT_POST, 'r_topic', FILTER_SANITIZE_STRING));
			$f['r_eintritt'] = htmlspecialchars(filter_input(INPUT_POST, 'r_eintritt', FILTER_SANITIZE_STRING));
			$f['r_austritt'] = htmlspecialchars(filter_input(INPUT_POST, 'r_austritt', FILTER_SANITIZE_STRING));
			$f['r_besitzer'] = filter_input(INPUT_POST, 'r_besitzer', FILTER_SANITIZE_NUMBER_INT);
			$f['r_besitzer_name'] = htmlspecialchars(filter_input(INPUT_POST, 'r_besitzer_name', FILTER_SANITIZE_STRING));
			
			if($f['r_besitzer'] == "") {
				$f['r_besitzer'] = 0;
			}
			if($f['r_status1'] == "") {
				$f['r_status1'] = "O";
			}
			if($f['r_status2'] == "") {
				$f['r_status2'] = "T";
			}
			if($f['r_min_punkte'] == "") {
				$f['r_min_punkte'] = 0;
			}
			
			$query = pdoQuery("SELECT `r_id`, `r_besitzer`, `r_name` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$f['r_id']]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				$row = $query->fetch();
				$r_besitzer = $row['r_besitzer'];
				if ($row['r_name'] == $lobby) {
					$f['r_name'] = $lobby;
				}
			}
			
			// In Namen die Leerzeichen und ' und " entfernen
			$ersetzen = array("'", "\"", "\\");
			$f['r_name'] = str_replace($ersetzen, "", $f['r_name']);
			
			$fehlermeldung = "";
			
			// Der Name des Raums ist zu kurz
			if (strlen($f['r_name']) <= 3) {
				$fehlermeldung = $lang['raum_fehler_name_zu_kurz'];
				$text .= hinweis($fehlermeldung, "fehler");
			}
			
			// Der Name des Raums ist zu lang
			if (strlen($f['r_name']) >= $raum_max) {
				$fehlermeldung = $lang['raum_fehler_name_zu_lang'];
				$text .= hinweis($fehlermeldung, "fehler");
			}
			
			// Der Benutzername für den Raumbesitzer existiert nicht oder ist ein Gast
			if ($f['r_besitzer'] != 0) {
				// Beim Anlegen des Raums wird eine ID übergeben
				
				// Keine Weitere Prüfung notwendig, da einfach nochmal die Benutzer-ID gesetzt wird.
				$f['r_besitzer'] = $u_id;
			} else {
				// Beim Editieren eines Raums wird der Name übergeben
				if($f['r_besitzer_name'] == "") {
					// Wenn kein Benutzername übergeben wurde, die Besitzer-ID auf 0 setzen
					$f['r_besitzer'] = 0;
				} else {
					$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick AND `u_level` != 'Z' AND `u_level` != 'C'", [':u_nick'=>$f['r_besitzer_name']]);
					
					$resultCount = $query->rowCount();
					if ($resultCount > 0) {
						$row3 = $query->fetch();
						$f['r_besitzer'] = $row3['u_id'];
					} else {
						$fehlermeldung = str_replace("%r_nick%", $f['r_besitzer_name'], $lang['raum_fehler_raumbesitzer_benutzername_existiert_nicht']);
						$text .= hinweis($fehlermeldung, "fehler");
					}
				}
			}
			
			// In permanenten Räumen darf ein Raumbesitzer keine Punkte ändern
			if (!$admin && $f['status2'] == "P") {
				$fehlermeldung = $lang['raum_fehler_mindestpunktezahl_nur_admin'];
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_min_punkte']);
			}
			
			// Nur Admin darf Nicht-Temporäre Räume setzen
			if (!$admin && $f['r_status2'] == "P") {
				$fehlermeldung = str_replace("%r_status%", $raumstatus2[$f['r_status2']], $lang['raum_fehler_status_nur_admin']);
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_status2']);
			}
			
			// Status moderiert nur falls Moderationsmodul aktiv
			if (isset($f['r_status1']) && (strtolower($f['r_status1']) == "m") && $moderationsmodul == 0) {
				$fehlermeldung = str_replace("%r_status%", $raumstatus1[$f['r_status1']], $lang['raum_fehler_status_moderationsmodul_deaktiviert']);
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_status1']);
			}
			
			// Nur Admin darf andere Stati als offen oder geschlossen setzen
			if (isset($f['r_status1']) && $f['r_status1'] != "O" && $f['r_status1'] != "G" && !$admin) {
				$tmp = str_replace("%r_status%", $raumstatus1[$f['r_status1']], $lang['raum_fehler_status_nur_admin']);
				$fehlermeldung = str_replace("%r_name%", $f['r_name'], $tmp);
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_status1']);
			}
			
			// Prüfen ob Mindestpunkte zwischen 0 und 99.999.999
			if (isset($f['r_min_punkte']) && ($f['r_min_punkte']) && ($f['r_min_punkte'] < 0 || $f['r_min_punkte'] > 99999999)) {
				$fehlermeldung = $lang['raum_fehler_mindestpunktezahl_falsch'];
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_min_punkte']);
			}
			
			if($fehlermeldung != "") {
				// Formular erneut aufrufen
				$text .= raum_editieren($f['r_id'], $f['r_name'], $f['r_status1'], $f['r_status2'], $f['r_smilie'], $f['r_min_punkte'], $f['r_topic'], $f['r_eintritt'], $f['r_austritt'], $f['r_besitzer']);
			} else {
				if($f['r_id'] == 0) {
					// Neuen Raum anlegen
					
					// Gibt es den Raumnamen schon?
					$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE `r_name` = :r_name", [':r_name'=>$f['r_name']]);
					
					$resultCount = $query->rowCount();
					if ($resultCount == 0) {
						// Raum neu eintragen und in den Raum gehen
						$erfolgsmeldung = str_replace("%r_name%", $f['r_name'], $lang['raum_erfolgsmeldung_erstellt']);
						$text .= hinweis($erfolgsmeldung, "erfolgreich");
						
						pdoQuery("INSERT INTO `raum` (`r_name`, `r_eintritt`, `r_austritt`, `r_status1`, `r_besitzer`, `r_topic`, `r_status2`, `r_min_punkte`, `r_smilie`)
								VALUES (:r_name, :r_eintritt, :r_austritt, :r_status1, :r_besitzer, :r_topic, :r_status2, :r_min_punkte, :r_smilie)",
							[
								':r_name'=>$f['r_name'],
								':r_eintritt'=>$f['r_eintritt'],
								':r_austritt'=>$f['r_austritt'],
								':r_status1'=>$f['r_status1'],
								':r_besitzer'=>$f['r_besitzer'],
								':r_topic'=>$f['r_topic'],
								':r_status2'=>$f['r_status2'],
								':r_min_punkte'=>$f['r_min_punkte'],
								':r_smilie'=>$f['r_smilie']
							]);
						
						$raum_neu = $pdo->lastInsertId();
						
						$text .= raeume_auflisten($order, $extended);
						$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $raum_neu);
						raum_user($o_raum, $u_id);
					} else {
						$fehlermeldung = str_replace("%r_name%", $f['r_name'], $lang['raum_fehler_name_existiert_bereits']);
						$text .= hinweis($fehlermeldung, "fehler");
						
						// Formular erneut aufrufen
						$text .= raum_editieren($f['r_id'], $f['r_name'], $f['r_status1'], $f['r_status2'], $f['r_smilie'], $f['r_min_punkte'], $f['r_topic'], $f['r_eintritt'], $f['r_austritt'], $f['r_besitzer']);
					}
				} else {
					// Vorhandenen Raum ändern
					
					// Gibt es den Raumnamen?
					$query2 = pdoQuery("SELECT `r_name` FROM `raum` WHERE `r_name` = :r_name AND `r_id` != :r_id", [':r_name'=>$f['r_name'], ':r_id'=>$f['r_id']]);
					
					$result2Count = $query2->rowCount();
					if ($result2Count == 0 || ($row['r_name'] == $f['r_name']) ) {
						// Raum editieren
						if($admin || $u_id == $f['r_besitzer']) {
							// Richtiger Besitzer
							$erfolgsmeldung = str_replace("%r_name%", $f['r_name'], $lang['raum_erfolgsmeldung_editiert']);
							$text .= hinweis($erfolgsmeldung, "erfolgreich");
							
							pdoQuery("UPDATE `raum` SET `r_name` = :r_name, `r_eintritt` = :r_eintritt, `r_austritt` = :r_austritt, `r_status1` = :r_status1,
									`r_besitzer` = :r_besitzer, `r_topic` = :r_topic, `r_status2` = :r_status2, `r_min_punkte` = :r_min_punkte, `r_smilie` = :r_smilie
									WHERE `r_id` = :r_id",
								[
									':r_id'=>$f['r_id'],
									':r_name'=>$f['r_name'],
									':r_eintritt'=>$f['r_eintritt'],
									':r_austritt'=>$f['r_austritt'],
									':r_status1'=>$f['r_status1'],
									':r_besitzer'=>$f['r_besitzer'],
									':r_topic'=>$f['r_topic'],
									':r_status2'=>$f['r_status2'],
									':r_min_punkte'=>$f['r_min_punkte'],
									':r_smilie'=>$f['r_smilie']
								]);
							
							$text .= raeume_auflisten($order, $extended);
							
						} else {
							// Falscher Besitzer
							$fehlermeldung = str_replace("%r_name%", $f['r_name'], $lang['raum_fehler_editieren_keine_berechtigung']);
							$text .= hinweis($fehlermeldung, "fehler");
						}
					} else {
						$fehlermeldung = str_replace("%r_name%", $f['r_name'], $lang['raum_fehler_name_existiert_bereits']);
						$text .= hinweis($fehlermeldung, "fehler");
						
						// Formular erneut aufrufen
						$text .= raum_editieren($f['r_id'], $f['r_name'], $f['r_status1'], $f['r_status2'], $f['r_smilie'], $f['r_min_punkte'], $f['r_topic'], $f['r_eintritt'], $f['r_austritt'], $f['r_besitzer']);
					}
				}
			}
		} else {
			// Erstmaliger Aufruf des Raums
			$raum_id = filter_input(INPUT_GET, 'raum', FILTER_SANITIZE_NUMBER_INT);
			if($raum_id != "") {
				// Raum editieren
				$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$raum_id]);
				
				$resultCount = $query->rowCount();
				if ($resultCount == 0) {
					// Der Raum existiert nicht
					$fehlermeldung = $lang['raum_fehler_falsche_id'];
					$text .= hinweis($fehlermeldung, "fehler");
					$text .= raeume_auflisten($order, $extended);
				} else {
					$query = pdoQuery("SELECT raum.*, `u_id`, `u_nick` FROM `raum` LEFT JOIN `user` ON `r_besitzer` = `u_id` WHERE `r_id` = :r_id", [':r_id'=>$raum_id]);
					
					$row = $query->fetch();
					if ($admin || $row['u_id'] == $u_id) {
						// Raum aufrufen
						$box = $lang['raeume_raum'] . ': ' . $row['r_name'];
						$text .= raum_editieren($row['r_id'], $row['r_name'], $row['r_status1'], $row['r_status2'], $row['r_smilie'], $row['r_min_punkte'], $row['r_topic'], $row['r_eintritt'], $row['r_austritt'], $row['r_besitzer']);
					} else {
						// Nur Leserechte
						$box = $lang['raeume_raum'] . ': ' . $row['r_name'];
						
						$zaehler = 0;
						$text .= "<table style=\"width:100%;\">\n";
						
						
						// Status
						$a = 0;
						reset($raumstatus1);
						while ($a < count($raumstatus1)) {
							$keynum = key($raumstatus1);
							$status1Select = $raumstatus1[$keynum];
							if ($keynum == $row['r_status1']) {
								$inhaltStatus1 = $status1Select;
								break;
							}
							next($raumstatus1);
							$a++;
						}
						
						if ($zaehler % 2 != 0) {
							$bgcolor = 'class="tabelle_zeile2"';
						} else {
							$bgcolor = 'class="tabelle_zeile1"';
						}
						
						$text .= "<tr>";
						$text .= "<td $bgcolor style=\"text-align:right\">$lang[raeume_status]</td>\n";
						$text .= "<td $bgcolor>$inhaltStatus1</td>\n";
						$text .= "</tr>\n";
						$zaehler++;
						
						
						// Art
						$a = 0;
						reset($raumstatus2);
						while ($a < count($raumstatus2)) {
							$keynum = key($raumstatus2);
							$status2Select = $raumstatus2[$keynum];
							if ($keynum == $row['r_status2']) {
								$inhaltStatus2 = $status2Select;
								break;
							}
							next($raumstatus2);
							$a++;
						}
						
						if ($zaehler % 2 != 0) {
							$bgcolor = 'class="tabelle_zeile2"';
						} else {
							$bgcolor = 'class="tabelle_zeile1"';
						}
						
						$text .= "<tr>";
						$text .= "<td $bgcolor style=\"text-align:right\">$lang[raeume_art]</td>\n";
						$text .= "<td $bgcolor>$inhaltStatus2</td>\n";
						$text .= "</tr>\n";
						$zaehler++;
						
						
						// Topic
						$text .= zeige_formularfelder("text", $zaehler, $lang['raeume_topic'], "", htmlspecialchars($row['r_topic']));
						$zaehler++;
						
						
						// Eintrittsnachricht
						$text .= zeige_formularfelder("text", $zaehler, $lang['raeume_eintritt'], "", htmlspecialchars($row['r_eintritt']));
						$zaehler++;
						
						
						// Austrittsnachricht
						$text .= zeige_formularfelder("text", $zaehler, $lang['raeume_austritt'], "", htmlspecialchars($row['r_austritt']));
						$zaehler++;
						
						
						$text .= "</table>\n";
					}
				}
			} else {
				// Neuen Raum anlegen
				$raum_id = 0;
				$raumname = "";
				$status1 = "";
				$status2 = "";
				$smilie = 1;
				$min_punkte = 0;
				$topic = "";
				$eintrittsnachricht = "";
				$austrittsnachricht = "";
				$raumbesitzer = $u_id;
				
				$box = $lang['raum_neuer_raum'];
				$text .= raum_editieren($raum_id, $raumname, $status1, $status2, $smilie, $min_punkte, $topic, $eintrittsnachricht, $austrittsnachricht, $raumbesitzer);
			}
		}
		
		break;
		
	default;
		// Anzeige aller Räume
		$box = $lang['titel'];
		$text .= raeume_auflisten($order, $extended);
}

// Box anzeigen
zeige_tabelle_zentriert($box, $text);
?>