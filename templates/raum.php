<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}

require_once("functions/functions-formulare.php");

$formular = filter_input(INPUT_POST, 'formular', FILTER_SANITIZE_NUMBER_INT);
$raum = filter_input(INPUT_GET, 'raum', FILTER_SANITIZE_NUMBER_INT);

$text = "";

if (!isset($loesch)) {
	$loesch = "";
}
if (!isset($loesch2)) {
	$loesch2 = "";
}

$f = array();
$f['r_id'] = filter_input(INPUT_POST, 'r_id', FILTER_SANITIZE_NUMBER_INT);

// Raum löschen
if ($los != "$t[raum_speichern]" && $loesch == "$t[raum_loeschen]") {
	$aktion = "loesch";
}

// Raum löschen abgebrochen
if ($loesch2 == "$t[sonst5]") {
	$aktion = "";
}


// Auswahl
switch ($aktion) {
	case "loesch2":
		// Raum löschen
		$query = "SELECT raum.*,u_id FROM raum left join user on r_besitzer=u_id WHERE r_id=$f[r_id] ";
		$result = sqlQuery($query);
		
		if ($result && mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_object($result);
			
			// Berechtigung prüfen, alle Benutzer in Lobby werfen und löschen
			if ($admin || ($row->r_besitzer == $u_id)) {
				// Lobby suchen
				$query = "SELECT r_id FROM raum WHERE r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "'";
				$result2 = sqlQuery($query);
				if ($result2 AND mysqli_num_rows($result2) > 0) {
					$lobby_id = mysqli_result($result2, 0, "r_id");
				}
				mysqli_free_result($result2);
				
				// Raum ist nicht Lobby -> Löschen
				if ($f['r_id'] == $lobby_id) {
					$fehlermeldung = str_replace("%r_name%", $row->r_name, $t['raum_fehler_kann_nicht_geloescht_werden']);
					$text .= hinweis($fehlermeldung, "fehler");
				} else {
					// Raum schließen
					$f['r_status1'] = "G";
					schreibe_db("raum", $f, $f['r_id'], "r_id");
					
					// Raum leeren
					$query = "SELECT o_user,o_name FROM online WHERE o_raum=$f[r_id] ";
					$result2 = sqlQuery($query);
					
					while ($row2 = mysqli_fetch_object($result2)) {
						system_msg("", 0, $row2->o_user, $system_farbe, str_replace("%r_name%", $row->r_name, $t['fehler4']));
						$oo_raum = raum_gehe($o_id, $row2->o_user, $row2->o_name, $f['r_id'], $lobby_id);
						raum_user($lobby_id, $row2->o_user);
						$i++;
					}
					mysqli_free_result($result2);
					
					$query = "DELETE FROM raum WHERE r_id=$f[r_id] ";
					$result2 = sqlUpdate($query);
					@mysqli_free_result($result2);
					
					// Gesperrte Räume löschen
					$query = "DELETE FROM sperre WHERE s_raum=$f[r_id]";
					$result2 = sqlUpdate($query, true);
					@mysqli_free_result($result2);
					
					// ausgeben: Der Raum wurde gelöscht.
					$erfolgsmeldung = str_replace("%r_name%", $row->r_name, $t['raum_erfolgsmeldung_geloescht']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				}
			} else {
				$fehlermeldung = str_replace("%r_name%", $row->r_name, $t['raum_fehler_loeschen_keine_berechtigung']);
				$text .= hinweis($fehlermeldung, "fehler");
			}
			mysqli_free_result($result);
		} else {
			$fehlermeldung = $t['raum_fehler_falsche_id'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
		$box = str_replace("%r_name%", $row->r_name, $t['raum_raumname']);
		
		break;
	
	case "loesch":
	// Raum löschen
		$query = "SELECT raum.*,u_id FROM raum LEFT JOIN user ON r_besitzer=u_id WHERE r_id=$f[r_id] ";
		$result = sqlQuery($query);
		
		if ($result && mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_object($result);
			
			// Kopf Tabelle
			$box = $t['sonst6'];
			$zaehler = 0;
			$text .= "<table style=\"width:100%;\">\n";
			
			// Raum
			$text .= zeige_formularfelder("text", $zaehler, $t['raeume_raum'], "", $row->r_name);
			$zaehler++;
			
			// Topic
			$text .= zeige_formularfelder("text", $zaehler, $t['raeume_topic'], "", htmlspecialchars($row->r_topic));
			$zaehler++;
			
			
			// Eintrittsnachricht
			$text .= zeige_formularfelder("text", $zaehler, $t['raeume_eintritt'], "", htmlspecialchars($row->r_eintritt));
			$zaehler++;
			
			
			// Austrittsnachricht
			$text .= zeige_formularfelder("text", $zaehler, $t['raeume_austritt'], "", htmlspecialchars($row->r_austritt));
			$zaehler++;
			
			
			// Formular löschen
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>";
			$text .= "<td $bgcolor colspan=\"2\">$t[raum_wirklich_loeschen]<br>\n";
			$text .= "<form name=\"$row->r_name\" action=\"inhalt.php?bereich=raum\" method=\"post\">\n"
				. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"r_id\" value=\"$row->r_id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"loesch2\">"
				. "<input type=\"submit\" name=\"loesch2\" value=\"$t[raum_loeschen]\">"
				. "&nbsp;"
				. "<input type=\"submit\" name=\"loesch2\" value=\"$t[sonst5]\">"
				. "</form>\n";
			$text .= "</td>\n";
			$text .= "</tr>\n";
			$zaehler++;
			
			
			$text .= "</table>\n";
			
			
			mysqli_free_result($result);
		} else {
			$fehlermeldung = $t['raum_fehler_falsche_id'];
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
			if($f['status2'] == "") {
				$f['status2'] = "T";
			}
			if($f['r_min_punkte'] == "") {
				$f['r_min_punkte'] = 0;
			}
			
			$query = "SELECT r_id, r_besitzer, r_name FROM raum WHERE r_id = '$f[r_id]'";
			$result = sqlQuery($query);
			$num = mysqli_num_rows($result);
			if ($num == 1) {
				$row = mysqli_fetch_object($result);
				$r_besitzer = $row->r_besitzer;
				if ($row->r_name == $lobby) {
					$f['r_name'] = $lobby;
				}
			}
			
			// In Namen die Leerzeichen und ' und " entfernen
			$ersetzen = array("'", "\"", "\\");
			$f['r_name'] = str_replace($ersetzen, "", $f['r_name']);
			
			$fehlermeldung = "";
			
			// Der Name des Raums ist zu kurz
			if (strlen($f['r_name']) <= 3) {
				$fehlermeldung = $t['raum_fehler_name_zu_kurz'];
				$text .= hinweis($fehlermeldung, "fehler");
			}
			
			// Der Name des Raums ist zu lang
			if (strlen($f['r_name']) >= $raum_max) {
				$fehlermeldung = $t['raum_fehler_name_zu_lang'];
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
					$query = "SELECT u_id FROM user WHERE u_nick= '".$f['r_besitzer_name']."' AND `u_level` != 'Z' AND `u_level` != 'C';";
					$result = sqlQuery($query);
					if ($result && mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_object($result);
						$f['r_besitzer'] = $row->u_id;
					} else {
						$fehlermeldung = str_replace("%r_nick%", $f['r_besitzer_name'], $t['raum_fehler_raumbesitzer_benutzername_existiert_nicht']);
						$text .= hinweis($fehlermeldung, "fehler");
					}
				}
			}
			
			
			// In permanenten Räumen darf ein Raumbesitzer keine Punkte ändern
			if (!$admin && $f['status2'] == "P") {
				$fehlermeldung = $t['raum_fehler_mindestpunktezahl_nur_admin'];
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_min_punkte']);
			}
			
			// Nur Admin darf Nicht-Temporäre Räume setzen
			if (!$admin && $f['r_status2'] == "P") {
				$fehlermeldung = str_replace("%r_status%", $raumstatus2[$f['r_status2']], $t['raum_fehler_status_nur_admin']);
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_status2']);
			}
			
			// Status moderiert nur falls Moderationsmodul aktiv
			if (isset($f['r_status1']) && (strtolower($f['r_status1']) == "m") && $moderationsmodul == 0) {
				$fehlermeldung = str_replace("%r_status%", $raumstatus1[$f['r_status1']], $t['raum_fehler_status_moderationsmodul_deaktiviert']);
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_status1']);
			}
			
			// Nur Admin darf andere Stati als offen oder geschlossen setzen
			if (isset($f['r_status1']) && $f['r_status1'] != "O" && $f['r_status1'] != "G" && !$admin) {
				$tmp = str_replace("%r_status%", $raumstatus1[$f['r_status1']], $t['raum_fehler_status_nur_admin']);
				$fehlermeldung = str_replace("%r_name%", $f['r_name'], $tmp);
				$text .= hinweis($fehlermeldung, "fehler");
				unset($f['r_status1']);
			}
			
			// Prüfen ob Mindestpunkte zwischen 0 und 99.999.999
			if (isset($f['r_min_punkte']) && ($f['r_min_punkte']) && ($f['r_min_punkte'] < 0 || $f['r_min_punkte'] > 99999999)) {
				$fehlermeldung = $t['raum_fehler_mindestpunktezahl_falsch'];
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
					$query = "SELECT r_id FROM raum WHERE r_name = '$f[r_name]' ";
					$result = sqlQuery($query);
					$row = mysqli_num_rows($result);
					if ($row == 0) {
						// Raum neu eintragen und in den Raum gehen
						$erfolgsmeldung = str_replace("%r_name%", $f['r_name'], $t['raum_erfolgsmeldung_erstellt']);
						$text .= hinweis($erfolgsmeldung, "erfolgreich");
						
						$raum_neu = schreibe_db("raum", $f, "", "r_id");
						
						$text .= raeume_auflisten($order, $extended);
						$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $raum_neu);
						raum_user($o_raum, $u_id);
					} else {
						$fehlermeldung = str_replace("%r_name%", $f['r_name'], $t['raum_fehler_name_existiert_bereits']);
						$text .= hinweis($fehlermeldung, "fehler");
						
						// Formular erneut aufrufen
						$text .= raum_editieren($f['r_id'], $f['r_name'], $f['r_status1'], $f['r_status2'], $f['r_smilie'], $f['r_min_punkte'], $f['r_topic'], $f['r_eintritt'], $f['r_austritt'], $f['r_besitzer']);
					}
				} else {
					// Vorhandenen Raum ändern
					
					// Gibt es den Raumnamen schon?
					$query = "SELECT r_name FROM raum WHERE r_name = '$f[r_name]' AND r_id != '$f[r_id]';";
					$result = sqlQuery($query);
					$result2 = sqlQuery($query);
					$rows = mysqli_num_rows($result);
					
					if ($rows == 0 || ($row->r_name == $f['r_name']) ) {
						// Raum editieren
						if($admin || $u_id == $f['r_besitzer']) {
							// Richtiger Besitzer
							$erfolgsmeldung = str_replace("%r_name%", $f['r_name'], $t['raum_erfolgsmeldung_editiert']);
							$text .= hinweis($erfolgsmeldung, "erfolgreich");
							
							schreibe_db("raum", $f, $f['r_id'], "r_id");
							
							$text .= raeume_auflisten($order, $extended);
							
						} else {
							// Falscher Besitzer
							$fehlermeldung = str_replace("%r_name%", $f['r_name'], $t['raum_fehler_editieren_keine_berechtigung']);
							$text .= hinweis($fehlermeldung, "fehler");
						}
					} else {
						$fehlermeldung = str_replace("%r_name%", $f['r_name'], $t['raum_fehler_name_existiert_bereits']);
						$text .= hinweis($fehlermeldung, "fehler");
						
						// Formular erneut aufrufen
						$text .= raum_editieren($f['r_id'], $f['r_name'], $f['r_status1'], $f['r_status2'], $f['r_smilie'], $f['r_min_punkte'], $f['r_topic'], $f['r_eintritt'], $f['r_austritt'], $f['r_besitzer']);
					}
				}
				mysqli_free_result($result);
			}
		} else {
			// Erstmaliger Aufruf des Raums
			$raum_id = filter_input(INPUT_GET, 'raum', FILTER_SANITIZE_NUMBER_INT);
			if($raum_id != "") {
				// Raum editieren
				$query = "SELECT r_id FROM raum WHERE r_id = '" . htmlspecialchars($raum_id) . "' ";
				//$query = "SELECT r_id FROM raum WHERE r_name = '" . htmlspecialchars($raum_id) . "' ";
				$result = sqlQuery($query);
				$row = mysqli_num_rows($result);
				
				if ($row == 0) {
					// Der Raum existiert nicht
					$fehlermeldung = $t['raum_fehler_falsche_id'];
					$text .= hinweis($fehlermeldung, "fehler");
					$text .= raeume_auflisten($order, $extended);
				} else {
					$query = "SELECT raum.*,u_id,u_nick FROM raum LEFT JOIN user ON r_besitzer=u_id WHERE r_id=" . intval($raum_id);
					$result = sqlQuery($query);
					$row = mysqli_fetch_object($result);
					
					if ($admin || $row->u_id == $u_id) {
						// Raum aufrufen
						$box = $t['raeume_raum'] . ': ' . $row->r_name;
						$text .= raum_editieren($row->r_id, $row->r_name, $row->r_status1, $row->r_status2, $row->r_smilie, $row->r_min_punkte, $row->r_topic, $row->r_eintritt, $row->r_austritt, $row->r_besitzer);
					} else {
						// Nur Leserechte
						$box = $t['raeume_raum'] . ': ' . $row->r_name;
						
						$zaehler = 0;
						$text .= "<table style=\"width:100%;\">\n";
						
						
						// Status
						$a = 0;
						reset($raumstatus1);
						while ($a < count($raumstatus1)) {
							$keynum = key($raumstatus1);
							$status1Select = $raumstatus1[$keynum];
							if ($keynum == $row->r_status1) {
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
						$text .= "<td $bgcolor style=\"text-align:right\">$t[raeume_status]</td>\n";
						$text .= "<td $bgcolor>$inhaltStatus1</td>\n";
						$text .= "</tr>\n";
						$zaehler++;
						
						
						// Art
						$a = 0;
						reset($raumstatus2);
						while ($a < count($raumstatus2)) {
							$keynum = key($raumstatus2);
							$status2Select = $raumstatus2[$keynum];
							if ($keynum == $row->r_status2) {
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
						$text .= "<td $bgcolor style=\"text-align:right\">$t[raeume_art]</td>\n";
						$text .= "<td $bgcolor>$inhaltStatus2</td>\n";
						$text .= "</tr>\n";
						$zaehler++;
						
						
						// Topic
						$text .= zeige_formularfelder("text", $zaehler, $t['raeume_topic'], "", htmlspecialchars($row->r_topic));
						$zaehler++;
						
						
						// Eintrittsnachricht
						$text .= zeige_formularfelder("text", $zaehler, $t['raeume_eintritt'], "", htmlspecialchars($row->r_eintritt));
						$zaehler++;
						
						
						// Austrittsnachricht
						$text .= zeige_formularfelder("text", $zaehler, $t['raeume_austritt'], "", htmlspecialchars($row->r_austritt));
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
				
				$box = $t['raum_neuer_raum'];
				$text .= raum_editieren($raum_id, $raumname, $status1, $status2, $smilie, $min_punkte, $topic, $eintrittsnachricht, $austrittsnachricht, $raumbesitzer);
			}
		}
		
		break;
		
	default;
		// Anzeige aller Räume
		$box = $t['raum_titel'];
		$text .= raeume_auflisten($order, $extended);
}

// Box anzeigen
zeige_tabelle_zentriert($box, $text);
?>