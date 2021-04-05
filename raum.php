<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

$fenster = str_replace("+", "", $u_nick);
$fenster = str_replace("-", "", $fenster);
$fenster = str_replace("ä", "", $fenster);
$fenster = str_replace("ö", "", $fenster);
$fenster = str_replace("ü", "", $fenster);
$fenster = str_replace("Ä", "", $fenster);
$fenster = str_replace("Ö", "", $fenster);
$fenster = str_replace("Ü", "", $fenster);
$fenster = str_replace("ß", "", $fenster);

if (!isset($raumstatus['E'])) {
	$raumstatus1['E'] = "Stiller Eingangsraum";
}

$title = $body_titel . ' - Räume';
zeige_header_anfang($title, $farbe_mini_background, $grafik_mini_background, $farbe_mini_link, $farbe_mini_vlink);
?>
<script>
		window.focus()
	function neuesFenster(url,name) {
				hWnd=window.open(url,name,"resizable=yes,scrollbars=yes,width=300,height=580"); 
	}
	function neuesFenster2(url) {
			hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580"); 
	}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Login ok?
if (strlen($u_id) != 0) {
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	// Menü als erstes ausgeben
	$box = $t['menue5'];
	if (isset($raum))
		$rraum = "&schau_raum=$raum";
	else $rraum = "";
	
	$text = "<a href=\"raum.php?http_host=$http_host&id=$id\">$t[menue1]</A>\n";
	if ($u_level != "G")
		$text .= "| <a href=\"raum.php?http_host=$http_host&id=$id&aktion=neu\">$t[menue2]</A>\n";
	show_box2($box, $text);
	?>
	<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
	<?php
	
	if (isset($f['r_name'])) {
		// In Namen die Leerzeichen und ' und " entfernen
		$ersetzen = array("'", "\"", "\\");
		$f['r_name'] = str_replace($ersetzen, "", $f['r_name']);
		
		if (!isset($f['r_werbung']))
			$f['r_werbung'] = "";
		
		// html unterdrücken
		$f['r_name'] = htmlspecialchars($f['r_name']);
		$f['r_topic'] = htmlspecialchars($f['r_topic']);
		$f['r_eintritt'] = htmlspecialchars($f['r_eintritt']);
		$f['r_austritt'] = htmlspecialchars($f['r_austritt']);
		$f['r_werbung'] = htmlspecialchars($f['r_werbung']);
		
		// Nur Admin darf Werbung eintragen
		if (!$admin || !$erweitertefeatures) {
			unset($f['r_werbung']);
		}
		
		if (isset($f['r_werbung']) && strlen($f['r_werbung']) > 0
			&& strtolower(substr($f['r_werbung'], 0, 7)) != "http://")
			$f['r_werbung'] = "http://" . $f['r_werbung'];
		
		// In Permanenten Räumen darf ein RB keine Punkte Ändern
		if (!$admin && $f['r_status2'] == "P") {
			echo "<P>" . $t['fehler14'] . "</P>\n";
			unset($f['r_min_punkte']);
		}
		
		// Nur Admin darf Nicht-Temporäre Räume setzen
		if (!$admin && $f['r_status2'] == "P") {
			echo "<P>"
				. str_replace("%r_status%", $raumstatus2[$f['r_status2']],
					$t['fehler10']) . "</P>\n";
			unset($f['r_status2']);
		}
		
		// Status moderiert nur falls kommerzielle Version
		if (isset($f['r_status1']) && (strtolower($f['r_status1']) == "m")
			&& $moderationsmodul == 0) {
			echo "<P>"
				. str_replace("%r_status%", $raumstatus1[$f['r_status1']],
					$t['fehler11']) . "</P>\n";
			unset($f['r_status1']);
		}
		
		// Nur Admin darf andere Stati als offen oder geschlossen setzen
		if (isset($f['r_status1']) && $f['r_status1'] != "O"
			&& $f['r_status1'] != "G" && !$admin) {
			$tmp = str_replace("%r_status%", $raumstatus1[$f['r_status1']],
				$t['fehler10']);
			echo "<P>" . str_replace("%r_name%", $f['r_name'], $tmp) . "</P>\n";
			unset($f['r_status1']);
		}
		
		// Prüfen ob Mindestpunkte zwischen 0 und 99.999.999
		if (isset($f['r_min_punkte']) && ($f['r_min_punkte'])
			&& ($f['r_min_punkte'] < 0 || $f['r_min_punkte'] > 99999999)) {
			echo "<P>" . $t['fehler13'] . "</P>\n";
			unset($f['r_min_punkte']);
		}
		
	}
	
	// Besitzer des Raumes und Original Raumname ermitteln
	$r_besitzer = 0;
	
	if (!isset($f['r_id']))
		$f['r_id'] = "";
	if (!isset($f['r_name']))
		$f['r_name'] = "";
	if (!isset($neu))
		$neu = false;
	$f['r_id'] = mysqli_real_escape_string($mysqli_link, $f['r_id']);
	$f['r_name'] = mysqli_real_escape_string($mysqli_link, $f['r_name']);
	
	$query = "SELECT r_id, r_besitzer, r_name FROM raum WHERE r_id = '$f[r_id]'";
	$result = mysqli_query($mysqli_link, $query);
	$num = mysqli_num_rows($result);
	if ($num == 1) {
		$row = mysqli_fetch_object($result);
		$r_besitzer = $row->r_besitzer;
		if ($row->r_name == $lobby)
			$f['r_name'] = $lobby;
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
	if ($f['r_id'] != "" && strlen($f['r_name']) > 3
		&& strlen($f['r_name']) < $raum_max && $los == "$t[sonst9]" && !$neu
		&& ($admin || $u_id == $r_besitzer)) {
		schreibe_db("raum", $f, $f['r_id'], "r_id");
	}
	
	if (!isset($loesch))
		$loesch = "";
	if (!isset($loesch2))
		$loesch2 = "";
	
	// Raum neu eintragen und in Raum gehen
	// bei keinen Admins ist nur temporär erlaubt
	if (strlen($f['r_name']) > 3 && strlen($f['r_name']) < $raum_max
		&& $los == "$t[sonst9]" && $loesch != "$t[sonst4]" && $neu
		&& ($u_level != "G")) {
		// Voreinstellungen für den Raumstatus
		if (!$f['r_status1'])
			$f['r_status1'] = "O";
		if (!$f['r_status2'])
			$f['r_status2'] = "T";
		
		// gibts den Raum schon?
		$query = "SELECT r_id FROM raum " . "WHERE r_name LIKE '$f[r_name]' ";
		$result = mysqli_query($mysqli_link, $query);
		$rows = mysqli_num_rows($result);
		
		if ($rows == 0) {
			// Raum neu eintragen und in Raum gehen
			$raum_neu = schreibe_db("raum", $f, "", "r_id");
			$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $raum_neu, TRUE);
			raum_user($o_raum, $u_id, $id);
		} else {
			echo "<P>" . str_replace("%r_name%", $f['r_name'], $t['fehler0'])
				. "</P>\n";
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
				
				// Berechtigung prüfen, alle User in Lobby werfen und löschen
				if ($admin || ($row->r_besitzer == $u_id)) {
					// Lobby suchen
					$query = "SELECT r_id FROM raum WHERE r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "'";
					$result2 = mysqli_query($mysqli_link, $query);
					if ($result2 AND mysqli_num_rows($result2) > 0) {
						$lobby_id = mysqli_result($result2, 0, "r_id");
					}
					@mysqli_free_result($result2);
					
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
							system_msg("", 0, $row2->o_user, $system_farbe,
								str_replace("%r_name%", $row->r_name,
									$t['fehler4']));
							$oo_raum = raum_gehe($o_id, $row2->o_user,
								$row2->o_name, $f['r_id'], $lobby_id, FALSE);
							raum_user($lobby_id, $row2->o_user, $id);
							$i++;
						}
						@mysqli_free_result($result2);
						
						$query = "DELETE FROM raum WHERE r_id=$f[r_id] ";
						$result2 = mysqli_query($mysqli_link, $query);
						@mysqli_free_result($result2);
						
						// Gesperrte Räume löschen
						$query = "DELETE FROM sperre WHERE s_raum=$f[r_id]";
						$result2 = mysqli_query($mysqli_link, $query);
						@mysqli_free_result($result2);
						
						// ausgeben: raum wurde gelöscht.
						echo "<P>"
							. str_replace("%r_name%", $row->r_name,
								$t['fehler3']) . "</P>\n";
					}
					
				} else {
					echo "<P>"
						. str_replace("%r_name%", $row->r_name, $t['fehler5'])
						. "</p>\n";
				}
				
				mysqli_free_result($result);
				
			} else {
				echo "<P>$t[fehler6]</P>\n";
			}
			break;
		
		case "loesch":
		// Raum löschen
			$text = '';
			$query = "SELECT raum.*,u_id FROM raum left join user ON r_besitzer=u_id WHERE r_id=$f[r_id] ";
			
			$result = mysqli_query($mysqli_link, $query);
			
			if ($result AND mysqli_num_rows($result) > 0) {
				// Kopf Tabelle
				$box = $t['sonst6'];
				
				// Raum zeigen
				$row = mysqli_fetch_object($result);
				$text = '';
				$text .= "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0><TR>\n";
				$text .= "<TD><b>$t[sonst2] $row->r_name</b></TD></TR>\n";
				$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>$t[sonst3]</b> "
					. htmlspecialchars($row->r_topic) . $f2
					. "</TD></TR>\n";
				$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>$t[sonst7]</b> "
					. htmlspecialchars($row->r_eintritt) . $f2
					. "</TD></TR>\n";
				$text .= "</TABLE>\n";
				
				// Formular löschen
				$text .= "<P>$t[fehler7]</P>\n";
				$text .= "<FORM NAME=\"$row->r_name\" ACTION=\"raum.php\" METHOD=POST>\n"
					. "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
					. "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
					. "<INPUT TYPE=\"HIDDEN\" NAME=\"f[r_id]\" VALUE=\"$row->r_id\">\n"
					. "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"loesch2\">"
					. "<INPUT TYPE=\"SUBMIT\" NAME=\"loesch2\" VALUE=\"$t[sonst4]\">"
					. "&nbsp;"
					. "<INPUT TYPE=\"SUBMIT\" NAME=\"loesch2\" VALUE=\"$t[sonst5]\">"
					. "</FORM>\n";
				
				// Box anzeigen
				show_box_title_content($box, $text);
				
				mysqli_free_result($result);
				
			} else {
				echo "<P>$t[fehler8]</P>\n";
			}
			break;
		
		case "neu":
		// Raum neu anlegen, Voreinstellungen
			$text = '';
			$r_status1 = "O";
			$r_status2 = "T";
			$r_smilie = "Y";
			$r_min_punkte = "0";
			
			$text = '';
			
			if ($communityfeatures && !$admin) {
				
				$result = mysqli_query($mysqli_link, "SELECT `u_punkte_gesamt` FROM `user` WHERE `u_id`=$u_id");
				if ($result && mysqli_num_rows($result) == 1) {
					$u_punkte_gesamt = mysqli_result($result, 0, 0);
				}
				
				if (isset($raumanlegenpunkte) && $u_punkte_gesamt < $raumanlegenpunkte) {
					$text .= str_replace("%punkte%", $raumanlegenpunkte, $t['sonst13']);
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
			
			$box = $t['menue2'];
				
			// Raum neu anlegen, Eingabeformular
			$text .= "<FORM NAME=\"$r_name\" ACTION=\"raum.php\" METHOD=POST>\n";
			$text .= "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
				. "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
				$text .= "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=1>\n";
			
				$text .= "<TR><TD><b>$t[sonst2]</b></TD>";
				$text .= "<TD>" . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[r_name]\" "
				. "VALUE=\"$r_name\" SIZE=$eingabe_breite>" . $f2
				. "</TD></TR>\n";
			
			// Status1 als Auswahl
				$text .= "<TR><TD>" . $f1 . "<b>" . $t['raum_user5'] . "</b>" . $f2
				. "</TD>" . "<TD>" . $f1 . "<SELECT NAME=\"f[r_status1]\">\n";
			
			$i = 0;
			while ($i < count($raumstatus1)) {
				$keynum = key($raumstatus1);
				$status1 = $raumstatus1[$keynum];
				if ($keynum == $r_status1) {
					$text .= "<OPTION SELECTED VALUE=\"$keynum\">$status1\n";
				} else {
					$text .= "<OPTION VALUE=\"$keynum\">$status1\n";
				}
				next($raumstatus1);
				$i++;
			}
			
			$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
			
			// Status2 als Auswahl
			$text .= "<TR><TD>" . $f1 . "<b>" . $t['raum_user6'] . "</b>" . $f2
				. "</TD>" . "<TD>" . $f1 . "<SELECT NAME=\"f[r_status2]\">\n";
			
			$i = 0;
			while ($i < count($raumstatus2)) {
				$keynum = key($raumstatus2);
				$status2 = $raumstatus2[$keynum];
				if ($keynum == $r_status2) {
					$text .= "<OPTION SELECTED VALUE=\"$keynum\">$status2\n";
				} else {
					$text .= "<OPTION VALUE=\"$keynum\">$status2\n";
				}
				next($raumstatus2);
				$i++;
			}
			
			$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
			
			if ($smilies_pfad && $erweitertefeatures) {
				$text .= "<TR><TD>" . $f1 . "<b>" . $t['raum_user7'] . "</b>" . $f2
					. "</TD><TD>" . $f1 . "<SELECT NAME=\"f[r_smilie]\">";
				if ($r_smilie == "Y") {
					$text .= "<OPTION SELECTED VALUE=\"Y\">$t[raum_user8]";
					$text .= "<OPTION VALUE=\"N\">$t[raum_user9]";
				} else {
					$text .= "<OPTION VALUE=\"Y\">$t[raum_user8]";
					$text .= "<OPTION SELECTED VALUE=\"N\">$t[raum_user9]";
				}
				$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
			}
			
			if ($erweitertefeatures) {
				// Punkte zum Betreten des Raumes
				$text .= "<TR><TD>" . $f1 . "<b>$t[sonst14]</b>" . $f2 . "</TD>";
				$text .= "<TD>" . $f1
					. "<INPUT TYPE=\"TEXT\" NAME=\"f[r_min_punkte]\" "
					. "VALUE=\"$r_min_punkte\" SIZE=\"7\">" . $f2
					. "</TD></TR>\n";
			}
			
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
			
			$text .= "<TR><TD>" . $f1 . "<b>" . $t['sonst3'] . "</b>" . $f2
				. "</TD>" . "<TD>" . $f1 . "<TEXTAREA rows=5 cols="
				. ($eingabe_breite) . " NAME=\"f[r_topic]\">"
				. $r_topic . "</TEXTAREA>" . $f2 . "</TD></TR>\n";
				$text .= "<TR style=\"vertical-align:top;\"><TD>" . $f1 . "<b>" . $t['sonst7']
				. "</b>" . $f2 . "</TD>" . "<TD>" . $f1
				. "<TEXTAREA rows=5 cols=" . ($eingabe_breite)
				. " NAME=\"f[r_eintritt]\">" . $r_eintritt
				. "</TEXTAREA>" . $f2 . "</TD></TR>\n";
			$text .= "<TR style=\"vertical-align:top;\"><TD>" . $f1 . "<b>" . $t['sonst8']
				. "</b>" . $f2 . "</TD>" . "<TD>" . $f1
				. "<TEXTAREA rows=5 cols=" . ($eingabe_breite)
				. " NAME=\"f[r_austritt]\">" . $r_austritt
				. "</TEXTAREA>" . $f2 . "</TD></TR>\n";
			
			if ($admin && $erweitertefeatures) {
				$text .= "<TR><TD>" . $f1 . "<b>" . $t['sonst12'] . "</b>" . $f2
					. "</TD>" . "<TD>" . $f1
					. "<INPUT TYPE=\"TEXT\" NAME=\"f[r_werbung]\" VALUE=\""
					 . "\" SIZE=$eingabe_breite>"
					. $f2 . "</TD></TR>\n";
			}
			
			$text .= "<TR><TD COLSPAN=2>" . $f1
				. "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$t[sonst9]\">"
				. $f2 . "</TD></TR>\n";
			$text .= "</TABLE>\n";
			$text .= "<INPUT TYPE=\"HIDDEN\" NAME=\"neu\" VALUE=\"1\">";
			$text .= "<INPUT TYPE=\"HIDDEN\" NAME=\"f[r_besitzer]\" VALUE=\"$u_id\">";
			$text .= "</FORM>\n";
			
			// Box anzeigen
			show_box_title_content($box, $text);
			
			break;
		
		case "edit":
			// kein eigener Fall, wird in default abgehandelt. 
		default;
		// Alle Räume
			$text = '';
			if (!isset($order))
				$order = "r_name";
			
			// Anzeige aller Räume als Liste oder eines Raums im Editor
			if ($aktion == "edit") {
				$text = '';
				$query = "SELECT raum.*,u_id,u_nick "
					. "FROM raum left join user on r_besitzer=u_id "
					. "WHERE r_id=" . intval($raum) . " ORDER BY $order";
				$result = mysqli_query($mysqli_link, $query);
				
				while ($rows = mysqli_fetch_object($result)) {
					// Ausgabe in Tabelle
					if ($admin || $rows->u_id == $u_id) {
						$box = $t['sonst2'] . ': ' . $rows->r_name;
						// für diesen Raum Admin
						$text .= "<FORM NAME=\"$rows->r_name\" ACTION=\"raum.php\" METHOD=POST>\n";
						$text .= "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
							. "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
						$text .= "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0 CELLPADDING=1>\n";
						
						$text .= "<TR><TD><b>$t[sonst2]</b></TD>";
						if ($rows->r_name == $lobby) {
							$text .= "<TD><b>$rows->r_name</b>"
								. "<INPUT TYPE=\"HIDDEN\" NAME=\"f[r_name]\" "
								. "VALUE=\"$rows->r_name\" SIZE=$eingabe_breite>"
								. $f2 . "</TD></TR>\n";
						} else {
							$text .= "<TD>" . $f1
								. "<INPUT TYPE=\"TEXT\" NAME=\"f[r_name]\" "
								. "VALUE=\"$rows->r_name\" SIZE=$eingabe_breite>"
								. $f2 . "</TD></TR>\n";
							
								$text .= "<TR><TD>" . $f1 . "<b>" . $t['raum_user2']
								. "</b>" . $f2 . "</TD>" . "<TD>" . $f1
								. $rows->u_nick . $f2 . "</TD></TR>\n";
							
							// Status1 als Auswahl
							$text .= "<TR><TD>" . $f1 . "<b>" . $t['raum_user5']
								. "</b>" . $f2 . "</TD>" . "<TD>" . $f1
								. "<SELECT NAME=\"f[r_status1]\">\n";
							
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
							
							$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
							
							// Status2 als Auswahl
							$text .= "<TR><TD>" . $f1 . "<b>" . $t['raum_user6']
								. "</b>" . $f2 . "</TD>" . "<TD>" . $f1
								. "<SELECT NAME=\"f[r_status2]\">\n";
							
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
							
							$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
							
						}
						
						if ($smilies_pfad && $erweitertefeatures) {
							$text .= "<TR><TD>" . $f1 . "<b>" . $t['raum_user7']
								. "</b>" . $f2 . "</TD><TD>" . $f1
								. "<SELECT NAME=\"f[r_smilie]\">";
							if ($rows->r_smilie == "Y") {
								$text .= "<OPTION SELECTED VALUE=\"Y\">$t[raum_user8]";
								$text .= "<OPTION VALUE=\"N\">$t[raum_user9]";
							} else {
								$text .= "<OPTION VALUE=\"Y\">$t[raum_user8]";
								$text .= "<OPTION SELECTED VALUE=\"N\">$t[raum_user9]";
							}
							$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
						}
						
						if ($erweitertefeatures && $rows->r_name != $lobby
							&& $rows->r_status1 != "L") {
							// Punkte zum Betreten des Raumes ändern
							$text .= "<TR><TD>" . $f1 . "<b>$t[sonst14]</b>" . $f2 . "</TD>";
							$text .= "<TD>" . $f1
								. "<INPUT TYPE=\"TEXT\" NAME=\"f[r_min_punkte]\" "
								. "VALUE=\"$rows->r_min_punkte\" SIZE=\"7\">"
								. $f2 . "</TD></TR>\n";
						}
						
						$text .= "<TR style=\"vertical-align:top;\"><TD>" . $f1 . "<b>"
							. $t['sonst3'] . "</b>" . $f2 . "</TD>" . "<TD>"
							. $f1 . "<TEXTAREA rows=5 cols="
							. ($eingabe_breite) . " NAME=\"f[r_topic]\">"
							. $rows->r_topic . "</TEXTAREA>"
							. $f2 . "</TD></TR>\n";
							$text .= "<TR style=\"vertical-align:top;\"><TD>" . $f1 . "<b>"
							. $t['sonst7'] . "</b>" . $f2 . "</TD>" . "<TD>"
							. $f1 . "<TEXTAREA rows=5 cols="
							. ($eingabe_breite) . " NAME=\"f[r_eintritt]\">"
							. $rows->r_eintritt . "</TEXTAREA>"
							. $f2 . "</TD></TR>\n";
						$text .= "<TR style=\"vertical-align:top;\"><TD>" . $f1 . "<b>"
							. $t['sonst8'] . "</b>" . $f2 . "</TD>" . "<TD>"
							. $f1 . "<TEXTAREA rows=5 cols="
							. ($eingabe_breite) . " NAME=\"f[r_austritt]\">"
							. $rows->r_austritt . "</TEXTAREA>"
							. $f2 . "</TD></TR>\n";
						
						// Werbung für Frame
						if ($admin && $erweitertefeatures) {
							$text .= "<TR><TD>" . $f1 . "<b>" . $t['sonst12']
								. "</b>" . $f2 . "</TD>" . "<TD>" . $f1
								. "<INPUT TYPE=\"TEXT\" NAME=\"f[r_werbung]\" VALUE=\""
								. $rows->r_werbung
								. "\" SIZE=$eingabe_breite>" . $f2
								. "</TD></TR>\n";
						}
						
						$text .= "<TR><TD COLSPAN=2>" . $f1
							. "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$t[sonst9]\">"
							. "&nbsp;&nbsp;"
							. "<INPUT TYPE=\"SUBMIT\" NAME=\"loesch\" VALUE=\"$t[sonst4]\">"
							. $f2 . "</TD></TR>\n";
						
						$text .= "</TABLE>\n";
						
						$text .= "<INPUT TYPE=\"HIDDEN\" NAME=\"f[r_id]\" VALUE=\"$rows->r_id\">";
						$text .= "<INPUT TYPE=\"HIDDEN\" NAME=\"f[r_besitzer]\" VALUE=\"$rows->r_besitzer\">";
						$text .= "</FORM>\n";
						
					} else {
						$box = $t['sonst2'] . ': ' . $rows->r_name;
						// Normalsterblicher
						$text .= "<TABLE WIDTH=100% BORDER=0 CELLSPACING=0>\n";
						if ($rows->r_name) {
							$text .= "<TR><TD><b>$t[sonst2] $rows->r_name</b></TD></TR>\n";
						}
						if ($rows->r_topic) {
							$text .= "<TR><TD COLSPAN=2>" . $f1
								. "<b>$t[sonst3]</b> "
								. htmlspecialchars($rows->r_topic)
								. $f2 . "</TD></TR>\n";
						}
						if ($rows->r_eintritt) {
							$text .= "<TR><TD COLSPAN=2>" . $f1
								. "<b>$t[sonst7]</b> "
								. htmlspecialchars($rows->r_eintritt) . $f2
								. "</TD></TR>\n";
						}
						$text .= "</TR></TABLE>\n";
						
					}
					
				}
				
				// Box anzeigen
				show_box_title_content($box, $text);
				
				mysqli_free_result($result);
			} else {
				$text = '';
				// Liste der Räume mit der Anzahl der User aufstellen
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
					. "FROM raum left join user on r_besitzer=u_id "
					. "GROUP BY r_name ORDER BY $order";
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) > 0) {
					
					// extended==Ansicht mit Details im extra beiten Fenster
					if ($admin && $adminfeatures == 1) {
						if (isset($extended) && ($extended == 1)) {
							$rlink = "<CENTER>" . $f1
								. "<b><a href=\"raum.php?http_host=$http_host&id=$id&order=$order\">"
								. $t['menue7'] . "</A></b>" . $f2
								. "</CENTER>\n";
							$text .= "<script language=\"javascript\">\n"
								. "window.resizeTo(800,600); window.focus();"
								. "</script>\n";
						} else {
							$rlink = "<CENTER>" . $f1
								. "<b><a href=\"raum.php?http_host=$http_host&id=$id&order=$order&extended=1\">"
								. $t['menue6'] . "</A></b>" . $f2
								. "</CENTER>\n";
						}
						$text .= "$rlink<br>";
					}
					$rlink = "<a href=\"raum.php?http_host=$http_host&id=$id&order=r_name\">" . $t['sonst2'] . "</a>";
					$text .= "<table class=\"tabelle_kopf\">\n";
					$text .= "<tr><td class=\"tabelle_kopfzeile\"><small><b>" . $rlink . "</b></small></td>";
					$text .= "<td class=\"tabelle_kopfzeile\">&nbsp;</td>";
					
					$rlink = "<a href=\"raum.php?http_host=$http_host&id=$id&order=r_status1,r_name\">Status</a>";
					$text .= "<td class=\"tabelle_kopfzeile\"><small><b>" . $rlink . "</b>&nbsp;</small></td>";
					
					$rlink = "<a href=\"raum.php?http_host=$http_host&id=$id&order=r_status2,r_name\">Art</a>";
					$text .= "<td class=\"tabelle_kopfzeile\"><small><b>" . $rlink . "</b>&nbsp;</small></td>";
					
					$rlink = "<a href=\"raum.php?http_host=$http_host&id=$id&order=u_nick\">" . $t['raum_user2'] . "</a>";
					$text .= "<td class=\"tabelle_kopfzeile\"><small><b>" . $rlink . "</b></small></td><td>&nbsp</td>";
					
					if (isset($extended) && $extended) {
						if ($smilies_pfad && $erweitertefeatures) {
							$rlink = $t['raum_user10'];
							$text .= "<td><small><b>" . $rlink . "</b></small></td>";
						}
						
						if ($erweitertefeatures) {
							$rlink = $t['sonst14'];
							$text .= "<td><small><b>" . $rlink . "</b></small></td>";
						}
						
						$rlink = $t['sonst3'];
						$text .= "<td><small><b>" . $rlink . "</b></small></td>";
						$rlink = $t['sonst7'];
						$text .= "<td><small><b>" . $rlink . "</b></small></td>";
						$rlink = $t['sonst8'];
						$text .= "<td><small><b>" . $rlink . "</b></small></td>";
					}
					
					$text .= "</tr>\n";
					$i = 1;
					$bgcolor = $farbe_tabelle_zeile1;
					while ($row = mysqli_fetch_array($result)) {
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
						if ($row['r_status1'] == "O"
							|| $row['r_status1'] == "m" || $uu_id == $u_id
							|| $admin) {
							
							$rlink = "<a href=\"raum.php?http_host=$http_host&id=$id&aktion=edit&raum="
								. $row['r_id'] . "\">" . $row['r_name']
								. "</A>";
							
							// Anzahl der User online ermitteln
							if ((isset($anzahl_user[$row['r_id']]))
								&& ($anzahl_user[$row['r_id']] > 0)) {
								$anzahl = $anzahl_user[$row['r_id']];
							} else {
								$anzahl = 0;
							}
							$ulink = "<a href=\"user.php?http_host=$http_host&id=$id&schau_raum="
								. $row['r_id'] . "\">$anzahl</A>";
							
							$text .= "<tr bgcolor=$bgcolor><td>$b1" . $rlink
								. "$b2</td>";
							$text .= "<td align=right>$b1" . $ulink
								. "$b2&nbsp;</td>";
							
							if ($admin || TRUE) {
								$text .= "<td>$b1"
									. substr($raumstatus1[$row['r_status1']],
										0, 1) . "$b2&nbsp;</td>";
								$text .= "<td>$b1"
									. substr($raumstatus2[$row['r_status2']],
										0, 1) . "$b2&nbsp;</td>";
								$text .= "<td>$b1"
									. user($row['u_id'], $row, TRUE, FALSE,
										"$b2</td><td align=RIGHT>$b1") . $b2
									. "</td>";
								if ((isset($extended)) && ($extended)) {
									
									if ($smilies_pfad && $erweitertefeatures) {
										if ($row['r_smilie'] == "Y") {
											$r_smilie = $t['raum_user8'];
										} else {
											$r_smilie = $t['raum_user9'];
										}
										$text .= "<td>$b1" . $r_smilie . "&nbsp;$b2</td>";
									}
									
									if ($erweitertefeatures) {
										$text .= "<td>$b1" . $row['r_min_punkte'] . "&nbsp;$b2</td>";
									}
									
									$temp = htmlspecialchars($row['r_topic']);
									$temp = str_replace('&amp;lt;', '<', $temp);
									$temp = str_replace('&amp;gt;', '>', $temp);
									$temp = str_replace('&amp;quot;', '"',
										$temp);
									$text .= "<td>$b1" . $temp . "&nbsp;$b2</td>";
									
									$temp = htmlspecialchars($row['r_eintritt']);
									$temp = str_replace('&amp;lt;', '<', $temp);
									$temp = str_replace('&amp;gt;', '>', $temp);
									$temp = str_replace('&amp;quot;', '"',
										$temp);
									$text .= "<td>$b1" . $temp . "&nbsp;$b2</td>";
									
									$temp = htmlspecialchars($row['r_austritt']);
									$temp = str_replace('&amp;lt;', '<', $temp);
									$temp = str_replace('&amp;gt;', '>', $temp);
									$temp = str_replace('&amp;quot;', '"',
										$temp);
									$text .= "<td>$b1" . $temp . "&nbsp;$b2</td>";
								}
							}
							$text .= "</tr>\n";
							$i++;
							if (($i % 2) > 0) {
								$bgcolor = $farbe_tabelle_zeile1;
							} else {
								$bgcolor = $farbe_tabelle_zeile2;
							}
						}
					}
					$text .= "</table>";
					
					// Box anzeigen
					show_box_title_content($box, $text);
					
					mysqli_free_result($result);
				}
			}
		
	}
	
	if ($o_js) {
		echo $f1
			. "<p style=\"text-align:center;\">[<a href=\"javascript:window.close();\">$t[sonst1]</A>]</p>"
			. $f2 . "\n";
	}
	
} else {
	echo "<p style=\"text-align:center;\">$t[sonst11]</p>\n";
}

?>
</body>
</html>