<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}

require_once("functions/functions-formulare.php");

$text = "";
$fehlermeldung = "";
if (isset($f['r_name'])) {
	// In Namen die Leerzeichen und ' und " entfernen
	$ersetzen = array("'", "\"", "\\");
	$f['r_name'] = str_replace($ersetzen, "", $f['r_name']);
	
	// html unterdrücken
	$f['r_name'] = htmlspecialchars($f['r_name']);
	$f['r_topic'] = htmlspecialchars($f['r_topic']);
	$f['r_eintritt'] = htmlspecialchars($f['r_eintritt']);
	$f['r_austritt'] = htmlspecialchars($f['r_austritt']);
	
	// In Permanenten Räumen darf ein RB keine Punkte ändern
	if (!$admin && $f['r_status2'] == "P") {
		$fehlermeldung .= $t['raum_fehler_mindestpunktezahl_nur_admin'];
		unset($f['r_min_punkte']);
	}
	
	// Nur Admin darf Nicht-Temporäre Räume setzen
	if (!$admin && $f['r_status2'] == "P") {
		$fehlermeldung .= str_replace("%r_status%", $raumstatus2[$f['r_status2']], $t['raum_fehler_status_nur_admin']);
		unset($f['r_status2']);
	}
	
	// Status moderiert nur falls kommerzielle Version
	if (isset($f['r_status1']) && (strtolower($f['r_status1']) == "m") && $moderationsmodul == 0) {
		$fehlermeldung .= str_replace("%r_status%", $raumstatus1[$f['r_status1']], $t['raum_fehler_status_moderationsmodul_deaktiviert']);
		unset($f['r_status1']);
	}
	
	// Nur Admin darf andere Stati als offen oder geschlossen setzen
	if (isset($f['r_status1']) && $f['r_status1'] != "O" && $f['r_status1'] != "G" && !$admin) {
		$tmp = str_replace("%r_status%", $raumstatus1[$f['r_status1']], $t['raum_fehler_status_nur_admin']);
		$fehlermeldung .= str_replace("%r_name%", $f['r_name'], $tmp);
		unset($f['r_status1']);
	}
	
	// Prüfen ob Mindestpunkte zwischen 0 und 99.999.999
	if (isset($f['r_min_punkte']) && ($f['r_min_punkte']) && ($f['r_min_punkte'] < 0 || $f['r_min_punkte'] > 99999999)) {
		$fehlermeldung .= $t['raum_fehler_mindestpunktezahl_falsch'];
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
$result = sqlQuery($query);
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
$result = sqlQuery($query);
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
	$result = sqlQuery($query);
	$rows = mysqli_num_rows($result);
	
	if ($rows == 0) {
		// Raum neu eintragen und in Raum gehen
		$raum_neu = schreibe_db("raum", $f, "", "r_id");
		$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $raum_neu);
		raum_user($o_raum, $u_id);
	} else {
		$fehlermeldung .= str_replace("%r_name%", $f['r_name'], $t['raum_fehler_name_existiert_bereits']);
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
if (strlen($f['r_name']) <= 3 && $neu) {
	$fehlermeldung .= $t['raum_fehler_name_zu_kurz'];
}
if (strlen($f['r_name']) >= $raum_max && $neu) {
	$fehlermeldung .= $t['raum_fehler_name_zu_lang'];
}

if($fehlermeldung != "") {
	$text .= hinweis($fehlermeldung, "fehler");
}

// Auswahl
switch ($aktion) {
	case "loesch2":
		// Raum löschen
		$query = "SELECT raum.*,u_id FROM raum left join user on r_besitzer=u_id WHERE r_id=$f[r_id] ";
		$result = sqlQuery($query);
		$text = "";
		
		if ($result AND mysqli_num_rows($result) > 0) {
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
					$erfolgsmeldung = str_replace("%r_name%", $row->r_name, $t['raum_geloescht']);
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
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
		
		break;
	
	case "loesch":
	// Raum löschen
		$text = "";
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
				. "<input type=\"hidden\" name=\"f[r_id]\" value=\"$row->r_id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"loesch2\">"
				. "<input type=\"submit\" name=\"loesch2\" value=\"$t[sonst4]\">"
				. "&nbsp;"
				. "<input type=\"submit\" name=\"loesch2\" value=\"$t[sonst5]\">"
				. "</form>\n";
			$text .= "</td>\n";
			$text .= "</tr>\n";
			$zaehler++;
			
			
			$text .= "</table>\n";
			
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
			
			mysqli_free_result($result);
			
		} else {
			echo "<p>$t[raum_fehler_falsche_id]</p>\n";
		}
		break;
	
	case "neu":
		// Raum neu anlegen
		
		if( ($fehlermeldung == "" && !$neu) || ($fehlermeldung != "" && $neu) ) {
			//Voreinstellungen
			$r_status1 = "O";
			$r_status2 = "T";
			$r_smilie = 1;
			$r_min_punkte = "0";
			
			if (!$admin) {
				$query = "SELECT `u_punkte_gesamt` FROM `user` WHERE `u_id`=$u_id";
				$result = sqlQuery($query);
				if ($result && mysqli_num_rows($result) == 1) {
					$u_punkte_gesamt = mysqli_result($result, 0, 0);
				}
				
				if (isset($raumanlegenpunkte) && $u_punkte_gesamt < $raumanlegenpunkte) {
					$fehlermeldung = str_replace("%punkte%", $raumanlegenpunkte, $t['wenig_mindestpunktezahl_oder_admin']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
					break;
				}
			}
			
			if (!isset($f['r_name'])) {
				$r_name = htmlspecialchars($u_nick . $t['chat_msg56']);
			}
			if (!isset($f['r_topic'])) {
				$r_topic = htmlspecialchars($u_nick . $t['chat_msg56']);
			}
			
			// In Namen die Leerzeichen und ' entfernen
			$ersetzen = array("'", " ", "\\");
			$f['r_name'] = str_replace($ersetzen, "", $f['r_name']);
			
			if (!isset($r_name)) {
				$r_name = "NeuerRaum";
			}
			
			$box = $t['raum_menue2'];
			
			$zaehler = 0;
				
			// Raum neu anlegen, Eingabeformular
			$text .= "<form name=\"$r_name\" action=\"inhalt.php?bereich=raum\" method=\"post\">\n";
			$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
			$text .= "<table style=\"width:100%;\">\n";
			
			$text .= zeige_formularfelder("input", $zaehler, $t['raeume_raum'], "f[r_name]", $r_name);
			$zaehler++;
			
			
			// Status1 als Auswahl
			$selectbox = "<select name=\"f[r_status1]\">\n";
			
			$i = 0;
			while ($i < count($raumstatus1)) {
				$keynum = key($raumstatus1);
				$status1 = $raumstatus1[$keynum];
				if ($keynum == $r_status1) {
					$selectbox .= "<option selected value=\"$keynum\">$status1\n";
				} else {
					$selectbox .= "<option value=\"$keynum\">$status1\n";
				}
				next($raumstatus1);
				$i++;
			}
			
			$selectbox .= "</select>\n";
			
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>";
			$text .= "<td $bgcolor style=\"text-align:right\">$t[raeume_status]</td>\n";
			$text .= "<td $bgcolor>$selectbox</td>\n";
			$text .= "</tr>\n";
			$zaehler++;
			
			
			// Status2 als Auswahl
			$selectbox = "<select name=\"f[r_status2]\">\n";
			
			$i = 0;
			while ($i < count($raumstatus2)) {
				$keynum = key($raumstatus2);
				$status2 = $raumstatus2[$keynum];
				if ($keynum == $r_status2) {
					$selectbox .= "<option selected value=\"$keynum\">$status2\n";
				} else {
					$selectbox .= "<option value=\"$keynum\">$status2\n";
				}
				next($raumstatus2);
				$i++;
			}
			
			$selectbox .= "</select>\n";
			
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>";
			$text .= "<td $bgcolor style=\"text-align:right\">$t[raeume_art]</td>\n";
			$text .= "<td $bgcolor>$selectbox</td>\n";
			$text .= "</tr>\n";
			$zaehler++;
			
			
			// Smilies
			$value = array($t['raum_verboten'], $t['raum_erlaubt']);
			$text .= zeige_formularfelder("selectbox", $zaehler, $t['raeume_smilies'], "f[r_smilie]", $value, $r_smilie);
			$zaehler++;
			
			
			// Mindestpunkte
			$text .= zeige_formularfelder("input", $zaehler, $t['raeume_mindestpunkte'], "f[r_min_punkte]",  $r_min_punkte, 0, "7");
			$zaehler++;
			
			
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
			
			
			// Topic
			$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_topic'], "f[r_topic]", $r_topic);
			$zaehler++;
			
			
			// Eintrittsnachricht
			$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_eintritt'], "f[r_eintritt]", $r_eintritt);
			$zaehler++;
			
			
			// Austrittsnachricht
			$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_austritt'], "f[r_austritt]", $r_austritt);
			$zaehler++;
			
			
			// Eintragen
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>";
			$text .= "<td $bgcolor style=\"text-align:right\">&nbsp;</td>\n";
			$text .= "<td $bgcolor><input type=\"submit\" name=\"los\" value=\"$t[sonst9]\"></td>\n";
			$text .= "</tr>\n";
			$zaehler++;
			
			
			$text .= "</table>\n";
			$text .= "<input type=\"hidden\" name=\"neu\" value=\"1\">";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"neu\">";
			$text .= "<input type=\"hidden\" name=\"f[r_besitzer]\" value=\"$u_id\">";
			$text .= "</form>\n";
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
			
			break;
		}
	
	case "edit":
		// kein eigener Fall, wird in default abgehandelt.
	default;
		// Alle Räume
		if (!isset($order)) {
			$order = "r_name";
		}
		
		// Anzeige aller Räume als Liste oder eines Raums im Editor
		if ($aktion == "edit") {
			$query = "SELECT raum.*,u_id,u_nick FROM raum left join user on r_besitzer=u_id WHERE r_id=" . intval($raum) . " ORDER BY $order";
			$result = sqlQuery($query);
			
			while ($rows = mysqli_fetch_object($result)) {
				// Ausgabe in Tabelle
				if ($admin || $rows->u_id == $u_id) {
					$box = $t['raeume_raum'] . ': ' . $rows->r_name;
					// Für diesen Raum Admin
					$text .= "<form name=\"$rows->r_name\" action=\"inhalt.php?bereich=raum\" method=\"post\">\n";
					$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
					$text .= "<table style=\"width:100%;\">\n";
					
					if ($rows->r_name == $lobby) {
						// Raum
						$text .= zeige_formularfelder("text", $zaehler, $t['raeume_raum'], "", $rows->r_name . "<input type=\"hidden\" name=\"f[r_name]\" value=\"$rows->r_name\" size=\"54\">");
						$zaehler++;
					} else {
						// Raum
						$text .= zeige_formularfelder("input", $zaehler, $t['raeume_raum'], "f[r_name]", $rows->r_name);
						$zaehler++;
						
						// Raumbesitzer
						$text .= zeige_formularfelder("text", $zaehler, $t['raeume_raumbesitzer'], "", $rows->u_nick);
						$zaehler++;
						
						
						// Status
						$selectbox = "<select name=\"f[r_status1]\">\n";
						
						$a = 0;
						reset($raumstatus1);
						while ($a < count($raumstatus1)) {
							$keynum = key($raumstatus1);
							$status1 = $raumstatus1[$keynum];
							if ($keynum == $rows->r_status1) {
								$selectbox .= "<option selected value=\"$keynum\">$status1\n";
							} else {
								$selectbox .= "<option value=\"$keynum\">$status1\n";
							}
							next($raumstatus1);
							$a++;
						}
						
						$selectbox .= "</select>\n";
						
						if ($zaehler % 2 != 0) {
							$bgcolor = 'class="tabelle_zeile2"';
						} else {
							$bgcolor = 'class="tabelle_zeile1"';
						}
						
						$text .= "<tr>";
						$text .= "<td $bgcolor style=\"text-align:right\">$t[raeume_status]</td>\n";
						$text .= "<td $bgcolor>$selectbox</td>\n";
						$text .= "</tr>\n";
						$zaehler++;
						
						
						// Art
						$selectbox = "<select name=\"f[r_status2]\">\n";
						
						$a = 0;
						reset($raumstatus2);
						while ($a < count($raumstatus2)) {
							$keynum = key($raumstatus2);
							$status2 = $raumstatus2[$keynum];
							if ($keynum == $rows->r_status2) {
								$selectbox .= "<option selected value=\"$keynum\">$status2\n";
							} else {
								$selectbox .= "<option value=\"$keynum\">$status2\n";
							}
							next($raumstatus2);
							$a++;
						}
						
						$selectbox .= "</select>\n";
						
						if ($zaehler % 2 != 0) {
							$bgcolor = 'class="tabelle_zeile2"';
						} else {
							$bgcolor = 'class="tabelle_zeile1"';
						}
						
						$text .= "<tr>";
						$text .= "<td $bgcolor style=\"text-align:right\">$t[raeume_art]</td>\n";
						$text .= "<td $bgcolor>$selectbox</td>\n";
						$text .= "</tr>\n";
						$zaehler++;
					}
					
					
					// Smilies
					$value = array($t['raum_verboten'], $t['raum_erlaubt']);
					$text .= zeige_formularfelder("selectbox", $zaehler, $t['raeume_smilies'], "f[r_smilie]", $value, $rows->r_smilie);
					$zaehler++;
					
					
					// Mindestpunkte
					if ($rows->r_name != $lobby) {
						$text .= zeige_formularfelder("input", $zaehler, $t['raeume_mindestpunkte'], "f[r_min_punkte]",  $rows->r_min_punkte, 0, "7");
						$zaehler++;
					}
					
					
					// Topic
					$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_topic'], "f[r_topic]", $rows->r_topic);
					$zaehler++;
					
					
					// Eintrittsnachricht
					$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_eintritt'], "f[r_eintritt]", $rows->r_eintritt);
					$zaehler++;
					
					
					// Austrittsnachricht
					$text .= zeige_formularfelder("textarea", $zaehler, $t['raeume_austritt'], "f[r_austritt]", $rows->r_austritt);
					$zaehler++;
					
					
					// Eintragen
					if ($zaehler % 2 != 0) {
						$bgcolor = 'class="tabelle_zeile2"';
					} else {
						$bgcolor = 'class="tabelle_zeile1"';
					}
					$text .= "<tr>";
					$text .= "<td $bgcolor style=\"text-align:right\"><input type=\"submit\" name=\"loesch\" value=\"$t[sonst4]\"></td>\n";
					$text .= "<td $bgcolor><input type=\"submit\" name=\"los\" value=\"$t[sonst9]\"></td>\n";
					$text .= "</tr>\n";
					$zaehler++;
					
					
					$text .= "</table>\n";
					
					$text .= "<input type=\"hidden\" name=\"f[r_id]\" value=\"$rows->r_id\">\n";
					$text .= "<input type=\"hidden\" name=\"f[r_besitzer]\" value=\"$rows->r_besitzer\">\n";
					$text .= "<input type=\"hidden\" name=\"neu\" value=\"1\">\n";
					$text .= "</form>\n";
					
				} else {
					$box = $t['raeume_raum'] . ': ' . $rows->r_name;
					// Normalsterblicher
					
					
					$zaehler = 0;
					$text .= "<table style=\"width:100%;\">\n";
					
					
					// Topic
					$text .= zeige_formularfelder("text", $zaehler, $t['raeume_topic'], "", htmlspecialchars($rows->r_topic));
					$zaehler++;
					
					
					// Eintrittsnachricht
					$text .= zeige_formularfelder("text", $zaehler, $t['raeume_eintritt'], "", htmlspecialchars($rows->r_eintritt));
					$zaehler++;
					
					
					// Austrittsnachricht
					$text .= zeige_formularfelder("text", $zaehler, $t['raeume_austritt'], "", htmlspecialchars($rows->r_austritt));
					$zaehler++;
					
					
					$text .= "</table>\n";
				}
				
			}
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
			
			mysqli_free_result($result);
		} else {
			$box = $t['titel'];
			// Liste der Räume mit der Anzahl der Benutzer aufstellen
			$query = "SELECT r_id,count(o_id) as anzahl FROM raum LEFT JOIN online ON r_id=o_raum "
				. "WHERE ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout OR o_id IS NULL) GROUP BY r_id";
			$result = sqlQuery($query);
			
			while ($row = mysqli_fetch_object($result)) {
				$anzahl_user[$row->r_id] = $row->anzahl;
			}
			mysqli_free_result($result);
			
			// Liste der Räume und der Raumbesitzer lesen
			$query = "SELECT raum.*,u_id,u_nick,u_level,u_punkte_gesamt,u_punkte_gruppe FROM raum left join user on r_besitzer=u_id GROUP BY r_name ORDER BY $order";
			$result = sqlQuery($query);
			if ($result && mysqli_num_rows($result) > 0) {
				
				// extended==Ansicht mit Details im extra beiten Fenster
				if ($admin) {
					if (isset($extended) && ($extended == 1)) {
						$rlink = "<center><span class=\"smaller\">" . "<b><a href=\"inhalt.php?bereich=raum&id=$id&order=$order\">" . $t['raum_menue7'] . "</a></b>" . "</span></center>\n";
						$text .= "<script language=\"javascript\">\n"
							. "window.resizeTo(800,600); window.focus();"
							. "</script>\n";
					} else {
						$rlink = "<center><span class=\"smaller\">" . "<b><a href=\"inhalt.php?bereich=raum&id=$id&order=$order&extended=1\">" . $t['raum_menue6'] . "</a></b>" . "</span></center>\n";
					}
					$text .= "$rlink<br>";
				}
				$text .= "<table style=\"width:100%\">\n";
				$text .= "<tr><td class=\"tabelle_kopfzeile\">$t[raeume_raum] <a href=\"inhalt.php?bereich=raum&id=$id&order=r_name\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a></td>";
				$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_benutzer_online]</td>";
				$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_status] <a href=\"inhalt.php?bereich=raum&id=$id&order=r_status1,r_name\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a></td>";
				$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_art] <a href=\"inhalt.php?bereich=raum&id=$id&order=r_status2,r_name\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a></td>";
				$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_raumbesitzer] <a href=\"inhalt.php?bereich=raum6id=$id&order=u_nick\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a></td>";
				if (isset($extended) && $extended) {
					$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_smilies]</td>";
					$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_mindestpunkte]</td>";
					$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_topic]</td>";
					$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_eintritt]</td>";
					$text .= "<td class=\"tabelle_kopfzeile\">$t[raeume_austritt]</td>";
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
						$rlink = "<a href=\"inhalt.php?bereich=raum&id=$id&aktion=edit&raum=" . $row['r_id'] . "\">" . $row['r_name'] . "</a>";
						
						// Anzahl der Benutzer online ermitteln
						if ((isset($anzahl_user[$row['r_id']])) && ($anzahl_user[$row['r_id']] > 0)) {
							$anzahl = $anzahl_user[$row['r_id']];
						} else {
							$anzahl = 0;
						}
						// Bei 0 Benutzern keinen Link anzeigen
						if( $anzahl == 0) {
							$ulink = "$anzahl $t[raeume_benutzer]";
						} else {
							$ulink = "<a href=\"inhalt.php?bereich=benutzer&id=$id&schau_raum=" . $row['r_id'] . "\">$anzahl $t[raeume_benutzer]</a>";
						}
						
						$text .= "<tr><td $bgcolor>$b1" . $rlink . "$b2</td>";
						$text .= "<td $bgcolor>$b1" . $ulink . "$b2&nbsp;</td>";
						
						$text .= "<td $bgcolor>$b1". $raumstatus1[$row['r_status1']] . "$b2&nbsp;</td>";
						$text .= "<td $bgcolor>$b1" . $raumstatus2[$row['r_status2']] . "$b2&nbsp;</td>";
						$text .= "<td $bgcolor>$b1" . zeige_userdetails($row['u_id'], $row, false) . $b2 . "</td>";
						if ((isset($extended)) && ($extended)) {
							if ($row['r_smilie'] == 1) {
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