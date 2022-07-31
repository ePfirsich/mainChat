<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$uu_suchtext = URLENCODE($suchtext);


$text = "";

// welcher raum soll abgefragt werden? ggf voreinstellen
// Positives $schau_raum entspricht r_id
// Negatives $schau_raum entspricht o_who * -1
if (isset($schau_raum) && !is_numeric($schau_raum)) {
	unset($schau_raum);
}
if ((isset($schau_raum)) && $schau_raum < 0) {
	// Community-Bereich gewählt
	$raum_subquery = "AND `o_who` = " . ($schau_raum * (-1));
} else if((isset($schau_raum)) && $schau_raum > 0) {
	// Raum gewählt
	$raum_subquery = "AND `r_id` = $schau_raum";
} else if(!isset($schau_raum) && $o_who != 0) {
	// Voreinstellung auf den aktuellen Community-Bereich
	$schau_raum = $o_who * (-1);
	$raum_subquery = "AND `o_who` = $o_who";
} else if(!isset($schau_raum) || $schau_raum == 0) {
	// Voreinstellung auf den aktuellen Raum
	$schau_raum = $o_raum;
	$raum_subquery = "AND `r_id` = $schau_raum";
}

if ($admin && isset($kick_user_chat) && $ui_id) {
	// Nur Admins: Benutzer sofort aus dem Chat kicken
	$query = pdoQuery("SELECT `o_id`, `o_raum`, `o_name` FROM `online` WHERE `o_user` = :o_user AND `o_level` != 'C' AND `o_level` != 'S' AND `o_level` !='A'", [':o_user'=>$ui_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$row = $query->fetch();
		// Aus dem Chat ausloggen
		ausloggen($ui_id, $row['o_name'], $row['o_raum'], $row['o_id']);
	} else {
		$fehlermeldung = $lang['benutzer_fehlermeldung_kicken'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
}

// Auswahl
switch ($aktion) {
	case "suche":
	// Suchmaske für Ergebnis oder Suchmaske für erste Suche ausgeben
		if ( strlen($suchtext) > 3 || $formular == 1 ) {
			
			// Suchergebnis mit Formular ausgeben
			benutzer_suche($f, $suchtext, $suche_ip, $suche_level, $suche_anmeldung, $suche_login, $suche_benutzerseite);
			
			// wenn einer nur nach % sucht, kanns auch weggelassen werden
			if ($suchtext == "%") {
				unset($suchtext);
			}
			
			// Variablen säubern
			unset($query);
			unset($where);
			unset($subquery);
			unset($sortierung);
			
			
			// Zusammensetzen der Query
			$query = "SELECT * FROM user ";
			
			$subquery = "";
			
			// Subquerys, wenn parameter gesetzt, teils anhand des Levels
			if ($admin) {
				// Optional level ergänzen
				if ($suche_level && $subquery) {
					$subquery .= "AND u_level = '$suche_level' ";
				} else if ($suche_level) {
					$subquery = " u_level = '$suche_level' ";
				}
				
				if ($suche_ip && $subquery) {
					$subquery .= "AND u_ip_historie LIKE '%$suche_ip%' ";
				} else if ($suche_ip) {
					$subquery = " u_ip_historie LIKE '%$suche_ip%' ";
				}
			}
			
			if ($suche_benutzerseite == "1" && $subquery) {
				$subquery .= "AND u_chathomepage='1' ";
			} else if ($suche_benutzerseite == "1") {
				$subquery = " u_chathomepage='1' ";
			}
			
			if ($suche_anmeldung && $subquery) {
				$subquery .= "AND u_neu IS NOT NULL AND date_add(u_neu, interval '$suche_anmeldung' day)>=NOW() ";
			} else if ($suche_anmeldung) {
				$subquery = " u_neu IS NOT NULL AND date_add(u_neu, interval '$suche_anmeldung' day)>=NOW() ";
			}
			
			if ($suche_login && $subquery) {
				$subquery .= "AND date_add(u_login, interval '$suche_login' hour)>=NOW() ";
			} else if ($suche_login) {
				$subquery = " date_add(u_login, interval '$suche_login' hour)>=NOW() ";
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
			
			// Where bedingung anhand des Suchtextes und des Levels
			if (!isset($suchtext) || !$suchtext) {
				// Kein Suchtext, aber evtl subquery
				$where[0] = "WHERE ";
			} elseif (strpos($suchtext, "%") >= 0) {
				if ($admin) {
					$where[0] = "WHERE (u_nick LIKE '$suchtext' OR u_email LIKE '$suchtext') ";
				} else {
					$where[0] = "WHERE (u_nick LIKE '$suchtext') ";
				}
			} else {
				if ($admin) {
					$where[1] = "WHERE u_nick = '$suchtext' ";
					$where[3] = "WHERE u_email = '$suchtext' ";
				} else {
					$where[0] = "WHERE u_nick = '$suchtext' ";
				}
			}
			
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
					if ($subquery) {
						$query .= " AND " . $subquery;
					}
				}
			}
			
			// Sortierung
			if ($admin) {
				$query .= "ORDER BY u_nick ";
			} else {
				$query .= "ORDER BY u_nick LIMIT 0, $max_user_liste ";
			}
			
			$query = pdoQuery($query, []);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 0) {
				// Kein user gefunden
				if ($suchtext == "%") {
					$suchtext = "";
				}
				
				$box = $lang['benutzer_suchergebnisse'];
				$fehlermeldung = str_replace("%suchtext%", $suchtext, $lang['benutzer_suche_kein_treffer']);
				$text .= hinweis($fehlermeldung, "fehler");
				
				// Zeige die Tabelle mit den Suchergebnissen an
				zeige_tabelle_zentriert($box, $text);
			} else {
				// Bei mehr als 1000 Ergebnissen Fehlermeldung ausgeben
				if ($resultCount > 2000) {
					$box = $lang['benutzer_suchergebnisse'];
					
					$fehlermeldung = $lang['benutzer_suche_mehr_2000_benutzer'];
					$text = hinweis($fehlermeldung, "fehler");
					
					// Zeige die Tabelle mit den Suchergebnissen an
					zeige_tabelle_zentriert($box, $text);
				} else {
					// Mehrere Benutzer gefunden
					if($resultCount == 1) {
						$erfolgsmeldung = $lang['benutzer_suche_treffer_einzahl'];
					} else {
						$erfolgsmeldung = str_replace("%anzahl%", $resultCount, $lang['benutzer_suche_treffer']);
					}
					$text = hinweis($erfolgsmeldung, "erfolgreich");
					
					$result = $query->fetchAll();
					$i = 0;
					foreach($result as $zaehler => $row) {
						// Array mit Benutzerdaten und Infotexten aufbauen
						$larr[$i]['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($row['u_nick'])), "I", "i");
						$larr[$i]['u_id'] = $row['u_id'];
						$larr[$i]['u_away'] = $row['u_away'];
						
						// Raumbesitzer einstellen, falls Level=Benutzer
						if (isset($larr[$i]['isowner']) && $larr[$i]['isowner'] && $userdata['u_level'] == "U") {
							$larr[$i]['u_level'] = "B";
						}
						
						$i++;
						flush();
					}
					
					$box = $lang['benutzer_suchergebnisse'];
					$text .= user_liste($larr, false);
					
					// Zeige die Tabelle mit den Suchergenissen an
					zeige_tabelle_zentriert($box, $text);
				}
				
			}
			
		} else {
			// Suchergebnis mit Formular ausgeben
			benutzer_suche($f, $suchtext, $suche_ip, $suche_level, $suche_anmeldung, $suche_login, $suche_benutzerseite);
		}
		
		break;
	
	case "adminliste":
		if ($adminlisteabrufbar && $u_level != "G") {
			$query = pdoQuery("SELECT * FROM `user` WHERE `u_level` = 'S' OR `u_level` = 'C' ORDER BY `u_nick`", []);
			
			$i = 0;
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				// Array mit Benutzerdaten und Infotexten aufbauen
				$larr[$i]['u_nick'] = strtr(str_replace("\\", "", htmlspecialchars($row['u_nick'])), "I", "i");
				$larr[$i]['u_id'] = $row['u_id'];
				$larr[$i]['u_away'] = $row['u_away'];
				
				$i++;
				flush();
			}
			
			$rows = count($larr);
			
			$box = $lang['adminliste'];
			$text = user_liste($larr, false);
			zeige_tabelle_zentriert($box, $text);
		}
		
		break;
	
	case "benutzer_zeig":
		if (!isset($zeigeip)) {
			$zeigeip = 0;
		}
		// Benutzer anzeigen
		if ($ui_id) {
			user_zeige($text, $ui_id, $admin, $schau_raum, $u_level, $zeigeip);
		} else {
			$fehlermeldung = $lang['benutzer_nicht_mehr_in_diesem_raum'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			$box =$lang['titel'];
			zeige_tabelle_zentriert($box, $text);
		}
		
		break;
	
	default;
		// Raum listen
		$query = pdoQuery("SELECT `raum`.*, `o_user`, `o_name`, `o_ip`, `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4`, `r_besitzer`= `o_user` AS `isowner` "
			. "FROM `online` LEFT JOIN `raum` ON `o_raum` = `r_id` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout $raum_subquery ORDER BY `o_name`", [':timeout'=>$timeout]);
		
		$result = $query->fetchAll();
		foreach($result as $i => $row) {
			// Array mit Benutzerdaten und Infotexten aufbauen
			$userdata = unserialize($row['o_userdata'] . $row['o_userdata2'] . $row['o_userdata3'] . $row['o_userdata4']);
			
			// Variable aus o_userdata setzen
			$larr[$i]['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($userdata['u_nick'])), "I", "i");
			$larr[$i]['u_id'] = $userdata['u_id'];
			$larr[$i]['u_away'] = $userdata['u_away'];
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
				$larr[$i]['r_name'] = $lang['benutzer_raum'] . $row['r_name'];
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
		
		if (isset($larr)) {
			$rows = count($larr);
		} else {
			$rows = 0;
		}
		
		// Fehlerbehandlung, falls Arry leer ist -> nichts gefunden
		if (!$rows && $schau_raum > 0) {
			$box = $lang['benutzer_raum'];
			
			$query = pdoQuery("SELECT `r_name` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>intval($schau_raum)]);
			
			$resultCount = $query->rowCount();
			if ($resultCount != 0) {
				$result = $query->fetch();
				$r_name = $result['r_name'];
			}
			$fehlermeldung = str_replace("%r_name%", $r_name, $lang['benutzer_raum_leer']);
			
			$text .= hinweis($fehlermeldung, "fehler");
		} else if (!$rows && $schau_raum < 0) {
			$box = $lang['benutzer_raum'];
			
			$fehlermeldung = str_replace("%whotext%", $whotext[($schau_raum * (-1))], $lang['benutzer_raum_niemand']);
			$text .= hinweis($fehlermeldung, "fehler");
		} else {
			// array ist gefüllt -> Daten ausgeben
			$box = $larr[0]['r_name'];
			
			$text .= ($larr[0]['r_topic'] ? "<span class=\"smaller\">$lang[benutzer_raum_topic]: " . $larr[0]['r_topic'] . "</span><br>" : "") . "<br>";
			
			// Benutzerliste ausgeben
			$text .= user_liste($larr, false);
			
			$text .= '<br>';
			
			zeige_tabelle_zentriert($box, $text);
			
			// Raumauswahl
			$text = '';
			$box = $lang['benutzer_raum_anderen_raum_zeigen'];
			
			$text .= "<form name=\"raum\" action=\"inhalt.php?bereich=benutzer\" method=\"post\">";
			$text .= "<select name=\"schau_raum\" onChange=\"document.raum.submit()\">";
			if ($admin) {
				$text .= raeume_auswahl($schau_raum, TRUE, FALSE, FALSE);
			} else {
				$text .= raeume_auswahl($schau_raum, FALSE, FALSE, FALSE);
			}
			$text .= "</select>";
			$text .= "<input type=\"submit\" name=\"eingabe\" value=\"$lang[benutzer_absenden]\">";
			$text .= "</form>";
		}
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
}
?>