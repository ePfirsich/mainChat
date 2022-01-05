<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}

if (!isset($suchtext)) {
	$suchtext = "";
}
$uu_suchtext = URLENCODE($suchtext);

// welcher raum soll abgefragt werden? ggf voreinstellen
// Positives $schau_raum entspricht r_id
// Negatives $schau_raum entspricht o_who * -1

if (isset($schau_raum) && !is_numeric($schau_raum)) {
	unset($schau_raum);
}

if ((isset($schau_raum)) && $schau_raum < 0) {
	// Community-Bereich gewählt
	$raum_subquery = "AND o_who=" . ($schau_raum * (-1));
} elseif ((isset($schau_raum)) && $schau_raum > 0) {
	// Raum gewählt
	$raum_subquery = "AND r_id=$schau_raum";
} elseif (!isset($schau_raum) && $o_who != 0) {
	// Voreinstellung auf den aktuellen Community-Bereich
	$schau_raum = $o_who * (-1);
	$raum_subquery = "AND o_who=$o_who";
} elseif (!isset($schau_raum) || $schau_raum == 0) {
	// Voreinstellung auf den aktuellen Raum
	$schau_raum = $o_raum;
	$raum_subquery = "AND r_id=$schau_raum";
}

if ($admin && isset($kick_user_chat) && $user) {
	// Nur Admins: Benutzer sofort aus dem Chat kicken
	$query = "SELECT o_id,o_raum,o_name FROM online WHERE o_user='" . mysqli_real_escape_string($mysqli_link, $user) . "' AND o_level!='C' AND o_level!='S' AND o_level!='A' ";
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_object($result);
		// Aus dem Chat ausloggen
		ausloggen($user, $row->o_name, $row->o_raum, $row->o_id);
		
		mysqli_free_result($result);
	} else {
		zeige_tabelle_zentriert($t['benutzer_fehlermeldung'], $t['benutzer_fehlermeldung_kicken']);
	}
}

// Auswahl
switch ($aktion) {
	case "suche":
	// Suchmaske für Ergebnis oder Suchmaske für erste Suche ausgeben
		if (strlen($suchtext) > 3 || (isset($suchtext_eingabe) && $suchtext_eingabe == "Go!")) {
			
			// Suchergebnis mit Formular ausgeben
			benutzer_suche($f, $suchtext);
			
			// wenn einer nur nach % sucht, kanns auch weggelassen werden
			if ($suchtext == "%") {
				unset($suchtext);
			}
			
			// Variablen säubern
			unset($query);
			unset($where);
			unset($subquery);
			unset($sortierung);
			
			$subquery = "";
			
			// Grundselect
			$select = "SELECT * FROM user ";
			
			// Where bedingung anhand des Suchtextes und des Levels
			if (!isset($suchtext) || !$suchtext) {
				// Kein Suchtext, aber evtl subquery
				$where[0] = "WHERE ";
			} elseif (strpos($suchtext, "%") >= 0) {
				$suchtext = mysqli_real_escape_string($mysqli_link, $suchtext);
				if ($admin) {
					$where[0] = "WHERE (u_nick LIKE '$suchtext' OR u_adminemail LIKE '$suchtext') ";
				} else {
					$where[0] = "WHERE (u_nick LIKE '$suchtext') ";
				}
			} else {
				$suchtext = mysqli_real_escape_string($mysqli_link, $suchtext);
				if ($admin) {
					$where[1] = "WHERE u_nick = '$suchtext' ";
					$where[3] = "WHERE u_adminemail = '$suchtext' ";
				} else {
					$where[0] = "WHERE u_nick = '$suchtext' ";
				}
				
			}
			
			// Subquerys, wenn parameter gesetzt, teils anhand des Levels
			if ($admin) {
				// Optional level ergänzen
				if ($f['level'] && $subquery) {
					$subquery .= "AND u_level = '" . mysqli_real_escape_string($mysqli_link, $f['level']) . "' ";
				} else if ($f['level']) {
					$subquery = " u_level = '" . mysqli_real_escape_string($mysqli_link, $f['level']) . "' ";
				}
				
				if ($f['ip'] && $subquery) {
					$subquery .= "AND u_ip_historie LIKE '%" . mysqli_real_escape_string($mysqli_link, $f['ip']) . "%' ";
				} else if ($f['ip']) {
					$subquery = " u_ip_historie LIKE '%" . mysqli_real_escape_string($mysqli_link, $f['ip']) . "%' ";
				}
			}
			
			if ($f['u_chathomepage'] == "1" && $subquery) {
				$subquery .= "AND u_chathomepage='1' ";
			} else if ($f['u_chathomepage'] == "1") {
				$subquery = " u_chathomepage='1' ";
			}
			
			if ($f['user_neu'] && $subquery) {
				$subquery .= "AND u_neu IS NOT NULL AND date_add(u_neu, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_neu]) . "' day)>=NOW() ";
			} else if ($f['user_neu']) {
				$subquery = " u_neu IS NOT NULL AND date_add(u_neu, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_neu]) . "' day)>=NOW() ";
			}
			
			if ($f['user_login'] && $subquery) {
				$subquery .= "AND date_add(u_login, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_login]) . "' hour)>=NOW() ";
			} else if ($f['user_login']) {
				$subquery = " date_add(u_login, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_login]) . "' hour)>=NOW() ";
			}
			
			if ($u_level == "U" && $subquery) {
				$subquery .= "AND u_level IN ('A','C','G','M','S','U') ";
			}else if ($u_level == "U") {
				$subquery = " u_level IN ('A','C','G','M','S','U') ";
			}
			
			// Zur Sicherheit, falls ein Gast die Such URL direkt aufruft
			if ($u_level == "G") {
				$subquery = " 1=2 ";
			}
			
			// Sortierung
			if ($admin) {
				$sortierung = "ORDER BY u_nick ";
			} else {
				$sortierung = "ORDER BY u_nick LIMIT 0,$max_user_liste ";
			}
			
			// Zusammensetzen der Query
			$query = $select;
			if ($where[0] == "WHERE " && $subquery) {
				$query .= $where[0] . " " . $subquery;
			} else if ($where[0] != "WHERE " && $subquery) {
				$query .= $where[0] . " AND " . $subquery;
			} else if ($where[0] != "WHERE ") {
				$query .= $where[0];
			}
			
			for ($i = 1; $i < count($where); $i++) {
				if ($where[$i]) {
					$query .= "UNION " . $select . $where[$i];
					if ($subquery)
						$query .= " AND " . $subquery;
				}
			}
			$query .= $sortierung;
			
			$result = mysqli_query($mysqli_link, $query);
			$anzahl = mysqli_num_rows($result);
			
			if ($anzahl == 0) {
				// Kein user gefunden
				if ($suchtext == "%") {
					$suchtext = "";
				}
				
				$box = $t['benutzer_suchergebnisse'];
				$text = str_replace("%suchtext%", $suchtext, $t['sonst5']);
				
				// Zeige die Tabelle mit den Suchergenissen an
				zeige_tabelle_zentriert($box, $text);
			} else if ($anzahl == 1) {
				// Einen Benutzer gefunden, alle Benutzerdaten ausgeben
				if (!isset($zeigeip)) {
					$zeigeip = 0;
				}
				while ($arr = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
					user_zeige($arr['u_id'], $admin, $schau_raum, $u_level, $zeigeip);
				}
			} else {
				// Bei mehr als 1000 Ergebnissen Fehlermeldung ausgeben
				if ($anzahl > 2000) {
					$box = $t['benutzer_suchergebnisse'];
					// Zeige die Tabelle mit den Suchergenissen an
					zeige_tabelle_zentriert($box, $t['benutzer_suche_mehr_2000_benutzer']);
				} else {
					// Mehrere Benutzer gefunden, als Tabelle ausgeben
					for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
						// Array mit Benutzerdaten und Infotexten aufbauen
						$larr[$i]['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($row['u_nick'])), "I", "i");
						$larr[$i]['u_level'] = $row['u_level'];
						$larr[$i]['u_id'] = $row['u_id'];
						$larr[$i]['u_away'] = $row['u_away'];
						$larr[$i]['u_punkte_anzeigen'] = $row['u_punkte_anzeigen'];
						$larr[$i]['u_punkte_gruppe'] = $row['u_punkte_gruppe'];
						$larr[$i]['u_chathomepage'] = $row['u_chathomepage'];
						
						// Raumbesitzer einstellen, falls Level=Benutzer
						if (isset($larr[$i]['isowner']) && $larr[$i]['isowner'] && $userdata['u_level'] == "U") {
							$larr[$i]['u_level'] = "B";
						}
						
						flush();
					}
					mysqli_free_result($result);
					
					$box = $t['benutzer_suchergebnisse'];
					$text = user_liste($larr, false);
					
					// Zeige die Tabelle mit den Suchergenissen an
					zeige_tabelle_zentriert($box, $text);
				}
				
			}
			
		} else {
			// Suchergebnis mit Formular ausgeben
			benutzer_suche($f, $suchtext);
		}
		
		break;
	
	case "adminliste":
		if ($adminlisteabrufbar && $u_level != "G") {
			
			$result = mysqli_query($mysqli_link, 'SELECT * FROM `user` WHERE `u_level` = "S" OR `u_level` = "C" ORDER BY `u_nick` ');
			$anzahl = mysqli_num_rows($result);
			
			for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
				// Array mit Benutzerdaten und Infotexten aufbauen
				$larr[$i]['u_nick'] = strtr(str_replace("\\", "", htmlspecialchars($row['u_nick'])),
					"I", "i");
				$larr[$i]['u_level'] = $row['u_level'];
				$larr[$i]['u_id'] = $row['u_id'];
				$larr[$i]['u_away'] = $row['u_away'];
				// Wenn der Benutzer nicht möchte, daß sein Würfel angezeigt wird, ist hier die einfachste Möglichkeit
				if ($row['u_punkte_anzeigen'] != "0") {
					$larr[$i]['gruppe'] = hexdec($row['u_punkte_gruppe']);
				} else {
					$larr[$i]['gruppe'] = 0;
				}
				$larr[$i]['u_chathomepage'] = $row['u_chathomepage'];
				
				flush();
			}
			mysqli_free_result($result);
			
			$rows = count($larr);
			
			$box = $t['adminliste'];
			$text = user_liste($larr, false);
			zeige_tabelle_zentriert($box, $text);
		}
		
		break;
	
	case "benutzer_zeig":
		if (!isset($zeigeip)) {
			$zeigeip = 0;
		}
		// Benutzer mit ID $user anzeigen
		if ($user) {
			// Benutzer listen
			user_zeige($user, $admin, $schau_raum, $u_level, $zeigeip);
		} else {
			echo "<p>$t[sonst11]</p>\n";
		}
		
		break;
	
	default;
		// Raum listen
		$query = "SELECT raum.*,o_user,o_name,o_ip,o_userdata,o_userdata2,o_userdata3,o_userdata4,r_besitzer=o_user AS isowner "
			. "FROM online LEFT JOIN raum ON o_raum=r_id WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout $raum_subquery " . "ORDER BY o_name";
		
		$result = mysqli_query($mysqli_link, $query);
		
		for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
			// Array mit Benutzerdaten und Infotexten aufbauen
			$userdata = unserialize(
				$row['o_userdata'] . $row['o_userdata2'] . $row['o_userdata3'] . $row['o_userdata4']);
			
			// Variable aus o_userdata setzen
			$larr[$i]['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($userdata['u_nick'])), "I", "i");
			$larr[$i]['u_level'] = $userdata['u_level'];
			$larr[$i]['u_id'] = $userdata['u_id'];
			$larr[$i]['u_away'] = $userdata['u_away'];
			$larr[$i]['u_punkte_anzeigen'] = $userdata['u_punkte_anzeigen'];
			$larr[$i]['u_punkte_gruppe'] = $userdata['u_punkte_gruppe'];
			$larr[$i]['r_besitzer'] = $row['r_besitzer'];
			$larr[$i]['r_topic'] = $row['r_topic'];
			$larr[$i]['o_ip'] = $row['o_ip'];
			$larr[$i]['isowner'] = $row['isowner'];
			
			if ($userdata['u_punkte_anzeigen'] != "0") {
				$larr[$i]['gruppe'] = hexdec($userdata['u_punkte_gruppe']);
			} else {
				$larr[$i]['gruppe'] = 0;
			}
			$larr[$i]['u_chathomepage'] = $userdata['u_chathomepage'];
			if (!$row['r_name'] || $row['r_name'] == "NULL") {
				$larr[$i]['r_name'] = "[" . $whotext[($schau_raum * (-1))] . "]";
			} else {
				$larr[$i]['r_name'] = $t['sonst42'] . $row['r_name'];
			}
			
			// Spezialbehandlung für Admins
			if (!$admin) {
				$larr[$i]['o_ip'] = "";
			}
			
			// Raumbesitzer einstellen, falls Level=Benutzer
			if ($larr[$i]['isowner'] && $userdata['u_level'] == "U") {
				$larr[$i]['u_level'] = "B";
			}
			
		}
		mysqli_free_result($result);
		
		if (isset($larr)) {
			$rows = count($larr);
		} else {
			$rows = 0;
		}
		
		// Fehlerbehandlung, falls Arry leer ist -> nichts gefunden
		if (!$rows && $schau_raum > 0) {
			
			$query = "SELECT r_name FROM raum WHERE r_id=" . intval($schau_raum);
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) != 0)
				$r_name = mysqli_result($result, 0, 0);
			echo "<p>" . str_replace("%r_name%", $r_name, $t['sonst13'])
				. "</p>\n";
			
		} elseif (!$rows && $schau_raum < 0) {
			
			echo "<p>"
				. str_replace("%whotext%", $whotext[($schau_raum * (-1))],
					$t['sonst43']) . "</p>\n";
			
		} else { // array ist gefüllt -> Daten ausgeben
			$box = $t['sonst54'];
			$text = '';
			$text .= "<b>" . $larr[0]['r_name'] . "</b><br>" . ($larr[0]['r_topic'] ? "<span class=\"smaller\">Topic: " . $larr[0]['r_topic'] . "</span><br>" : "") . "<br>";
			
			// Benutzerliste ausgeben
			$text .= user_liste($larr, false);
			
			$text .= "<p style=\"text-align:center;\" class=\"smaller\">" . $t['sonst12'] . "</p>";
			
			zeige_tabelle_zentriert($box, $text);
			
			// Raumauswahl
			$text = '';
			$box = $t['sonst14'];
			
			$text .= "<form name=\"raum\" action=\"inhalt.php?seite=benutzer\" method=\"post\">";
			$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">";
			$text .= "<select name=\"schau_raum\" onChange=\"document.raum.submit()\">";
			if ($admin) {
				$text .= raeume_auswahl($schau_raum, TRUE, FALSE, FALSE);
			} else {
				$text .= raeume_auswahl($schau_raum, FALSE, FALSE, FALSE);
			}
			$text .= "</select>";
			$text .= "<input type=\"submit\" name=\"eingabe\" value=\"Go!\">";
			$text .= "</form>";
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
		}
}
?>