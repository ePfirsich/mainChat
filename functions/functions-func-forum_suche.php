<?php
function show_pfad_posting2($th_id, $id, $thread) {
	global $mysqli_link;
	//Infos über Forum und Thema holen
	$sql = "SELECT `fo_id`, `fo_name`, `th_name` FROM `forum`, `thema` WHERE `th_id` = " . intval($th_id) . " AND `fo_id` = `th_fo_id`";
	
	$query = mysqli_query($mysqli_link, $sql);
	$fo_id = mysqli_result($query, 0, "fo_id");
	$fo_name = htmlspecialchars( mysqli_result($query, 0, "fo_name") );
	$th_name = htmlspecialchars( mysqli_result($query, 0, "th_name") );
	mysqli_free_result($query);
	
	return "<a href=\"forum.php?id=$id#$fo_id\">$fo_name</a> > <a href=\"forum.php?id=$id&th_id=$th_id&show_tree=$thread&aktion=show_thema&seite=1\">$th_name</a>";
	
}

function vater_rekursiv($vater) {
	global $mysqli_link;
	$query = "SELECT `po_id`, `po_vater_id` FROM posting WHERE `po_id` = " . intval($vater);
	$result = mysqli_query($mysqli_link, $query);
	$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if (mysqli_num_rows($result) <> 1) {
		return -1;
	} else if ($a['po_vater_id'] <> 0) {
		$vater = vater_rekursiv($a['po_vater_id']);
		return $vater;
	} else {
		return $a['po_id'];
	}
}

function such_bereich() {
	global $id, $mysqli_link, $suche, $t;
	
	$select_breite = 250;
	
	$box = $t['forum_suche'];
	$box .= " <a href=\"forum.php?id=$id\" class=\"button\" title=\"$t[forum_menue2]\"><span class=\"fa fa-commenting icon16\"></span> <span>$t[forum_menue2]</span></a>";
	$text = '';
	
	$text .= "<form name=\"suche_neu\" action=\"forum.php\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"suchergebnisse\">\n";
	$text .= "<table style=\"width:100%\">\n";
	
	// Überschrift: Ihr Profil
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['forum_suche'], "", "", 0, "70", "");
	
	// Suchtext
	$text .= "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile1\"><b>$t[suche1]</b></td><td class=\"tabelle_zeile1\">"
	. "<input type=\"text\" name=\"suche[text]\" value=\"" . htmlspecialchars($suche['text']) . "\" size=50></td></tr>\n";
		
	// Suche in Board/Thema
	$text .= "<tr><td style=\"text-align:right; vertical-align:top;\" class=\"tabelle_zeile1\">$t[suche2]</td><td class=\"tabelle_zeile1\">"
	. "<select name=\"suche[thema]\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
		
	$sql = "SELECT fo_id, fo_admin, fo_name, th_id, th_name FROM forum left join thema on fo_id = th_fo_id WHERE th_anzthreads <> 0 ORDER BY fo_order, th_order ";
	$query = mysqli_query($mysqli_link, $sql);
	$themaalt = "";
	$text .= "<option ";
	if (substr($suche['thema'], 0, 1) <> "B") {
		$text .= "selected ";
	}
	$text .= "value=\"ALL\">$t[option1]</option>";
	while ($thema = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		if (pruefe_leserechte($thema['th_id'])) {
			if ($themaalt <> $thema['fo_name']) {
				$text .= "<option ";
				if ($suche['thema'] == "B" . $thema['fo_id']) {
					$text .= "selected ";
				}
				$text .= "value=\"B" . $thema['fo_id'] . "\">" . $thema['fo_name']
				. "</option>";
				$themaalt = $thema['fo_name'];
			}
			$text .= "<option ";
			if ($suche['thema']
				== "B" . $thema['fo_id'] . "T" . $thema['th_id']) {
					$text .= "selected ";
				}
				$text .= "value=\"B" . $thema['fo_id'] . "T" . $thema['th_id']
				. "\">&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;" . $thema['th_name']
				. "</option>";
		}
	}
	$text .= "</select></td></tr>\n";
	mysqli_free_result($query);
	
	// Sucheinstelung UND/ODER
	$text .= "<tr><td style=\"text-align:right; vertical-align:top;\" class=\"tabelle_zeile1\">$t[suche3]</td><td class=\"tabelle_zeile1\"><select name=\"suche[modus]\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	$text .= "<option ";
	if ($suche['modus'] <> "O") {
		$text .= "selected ";
	}
	$text .= "value=\"A\">$t[option2]</option>";
	$text .= "<option ";
	if ($suche['modus'] == "O") {
		$text .= "selected ";
	}
	$text .= "value=\"O\">$t[option3]</option>";
	$text .= "</select></td></tr>\n";
	
	// Sucheinstellung Betreff/Text
	$text .= "<tr><td class=\"tabelle_zeile1\"></td><td class=\"tabelle_zeile1\"><select name=\"suche[ort]\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	$text .= "<option ";
	if ($suche['ort'] <> "B" && $suche['ort'] <> "T") {
		$text .= "selected ";
	}
	$text .= "value=\"V\">$t[option4]</option>";
	$text .= "<option ";
	if ($suche['ort'] == "B") {
		$text .= "selected ";
	}
	$text .= "value=\"B\">$t[option5]</option>";
	$text .= "<option ";
	if ($suche['ort'] == "T") {
		$text .= "selected ";
	}
	$text .= "value=\"T\">$t[option6]</option>";
	$text .= "</select></td></tr>\n";
	
	// Sucheinstellung Zeit
	$text .= "<tr><td class=\"tabelle_zeile1\"></td><td class=\"tabelle_zeile1\"><select name=\"suche[zeit]\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	$text .= "<option ";
	if (substr($suche['zeit'], 0, 1) <> "B") {
		$text .= "selected ";
	}
	$text .= "value=\"ALL\">$t[option7]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B1") {
		$text .= "selected ";
	}
	$text .= "value=\"B1\">$t[option8]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B7") {
		$text .= "selected ";
	}
	$text .= "value=\"B7\">$t[option9]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B14") {
		$text .= "selected ";
	}
	$text .= "value=\"B14\">$t[option10]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B30") {
		$text .= "selected ";
	}
	$text .= "value=\"B30\">$t[option11]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B90") {
		$text .= "selected ";
	}
	$text .= "value=\"B90\">$t[option12]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B180") {
		$text .= "selected ";
	}
	$text .= "value=\"B180\">$t[option13]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B365") {
		$text .= "selected ";
	}
	$text .= "value=\"B365\">$t[option14]</option>";
	$text .= "</select></td></tr>\n";
	
	// Sucheinstellung Sortierung
	$text .= "<tr><td class=\"tabelle_zeile1\"></td><td class=\"tabelle_zeile1\"><select name=\"suche[sort]\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	$text .= "<option ";
	if (substr($suche['sort'], 0, 1) <> "S") {
		$text .= "selected ";
	}
	$text .= "value=\"DEFAULT\">$t[option15]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SZA") {
		$text .= "selected ";
	}
	$text .= "value=\"SZA\">$t[option16]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SBA") {
		$text .= "selected ";
	}
	$text .= "value=\"SBA\">$t[option17]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SBD") {
		$text .= "selected ";
	}
	$text .= "value=\"SBD\">$t[option18]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SAA") {
		$text .= "selected ";
	}
	$text .= "value=\"SAA\">$t[option19]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SAD") {
		$text .= "selected ";
	}
	$text .= "value=\"SAD\">$t[option20]</option>";
	$text .= "</select></td></tr>\n";
	
	// nur von Benutzer
	$text .= "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile1\">$t[suche4]</td>\n"
		. "<td class=\"tabelle_zeile1\"><input type=\"text\" name=\"suche[username]\" value=\""
		. htmlspecialchars($suche['username']) . "\" size=\"20\"></td></tr>\n";
	
	$text .= "<tr><td class=\"tabelle_zeile1\">&nbsp;</td><td class=\"tabelle_zeile1\">"
	. "<input type=\"submit\" name=\"los\" value=\"$t[suche5]\"></td></tr>\n";
	
	$text .= "</table>\n";
	
	
	return $text;
}

function such_ergebnis() {
	global $id, $mysqli_link, $check_name, $u_id;
	global $farbe_hervorhebung_forum;
	global $suche, $farbe_neuesposting_forum, $t, $u_level;
	
	$select_breite = 250;
	$maxpostingsprosuche = 1000;
	$box = $t['ergebnis1'];
	
	$sql = "SELECT `u_gelesene_postings` FROM `user` WHERE `u_id`=" . intval($u_id);
	$query = mysqli_query($mysqli_link, $sql);
	if (mysqli_num_rows($query) > 0) {
		$gelesene = mysqli_result($query, 0, "u_gelesene_postings");
	}
	$u_gelesene = unserialize($gelesene);
	
	$fehler = "";
	if ($suche['username'] <> coreCheckName($suche['username'], $check_name)) {
		$fehler .= $t['fehler1'];
	}
	$suche['username'] = coreCheckName($suche['username'], $check_name);
	unset($suche['u_id']);
	if (strlen($fehler) == 0 && $suche['username'] <> "") {
		$sql = "SELECT `u_id` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $suche['username']) . "'";
		$query = mysqli_query($mysqli_link, $sql);
		if (mysqli_num_rows($query) == 1) {
			$suche['u_id'] = mysqli_result($query, 0, "u_id");
		} else {
			$fehler .= 'Benutzername unbekannt<br>';
		}
	}
	
	if (trim($suche['username']) == "" && trim($suche['text']) == "" && (!($suche['zeit'] == "B1" || $suche['zeit'] == "B7" || $suche['zeit'] == "B14"))) {
		$fehler .= $t['fehler2'];
	}
	if ($suche['modus'] <> "A" && $suche['modus'] <> "O") {
		$fehler .= $t['fehler3'];
	}
	if ($suche['ort'] <> "V" && $suche['ort'] <> "B" && $suche['ort'] <> "T") {
		$fehler .= $t['fehler4'];
	}
	if (!$suche['thema'] == "ALL" && !preg_match("/^B([0-9])+T([0-9])+$/i", $suche['thema']) && !preg_match("/^B([0-9])+$/i", $suche['thema'])) {
		$fehler .= $t['fehler5'];
	}
		
	if (strlen($fehler) > 0) {
		echo "<p style=\"color:$farbe_hervorhebung_forum; font-weight:bold; text-align:center;\">$fehler</p>";
	} else {
		$querytext = "";
		$querybetreff = "";
		if (trim($suche['text']) <> "") {
			$suche['text'] = htmlspecialchars($suche['text']);
			$suchetext = explode(" ", $suche['text']);
			
			for ($i = 0; $i < count($suchetext); $i++) {
				if (strlen($querytext) == 0) {
					$querytext = "po_text LIKE \"%" . mysqli_real_escape_string($mysqli_link, $suchetext[$i]) . "%\"";
				} else {
					if ($suche['modus'] == "O") {
						$querytext .= " OR po_text LIKE \"%" . mysqli_real_escape_string($mysqli_link, $suchetext[$i])
						. "%\"";
					} else {
						$querytext .= " AND po_text LIKE \"%" . mysqli_real_escape_string($mysqli_link, $suchetext[$i])
						. "%\"";
					}
				}
				
				if (strlen($querybetreff) == 0) {
					$querybetreff = "po_titel LIKE \"%" . mysqli_real_escape_string($mysqli_link, $suchetext[$i])
					. "%\"";
				} else {
					if ($suche['modus'] == "O") {
						$querybetreff .= " OR po_titel LIKE \"%"
							. mysqli_real_escape_string($mysqli_link, $suchetext[$i]) . "%\"";
					} else {
						$querybetreff .= " AND po_titel LIKE \"%"
							. mysqli_real_escape_string($mysqli_link, $suchetext[$i]) . "%\"";
					}
				}
				
			}
			$querytext = " (" . $querytext . ") ";
			$querybetreff = " (" . $querybetreff . ") ";
		}
		
		$sql = "SELECT posting.*, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_zeit, u_id, u_nick, u_level, u_punkte_gesamt, u_punkte_gruppe, u_chathomepage FROM posting left join user on po_u_id = u_id WHERE ";
		
		$abfrage = "";
		if ($suche['ort'] == "V" && $querybetreff <> "") {
			$abfrage = " (" . $querybetreff . " or " . $querytext . ") ";
		} else if ($suche['ort'] == "B" && $querybetreff <> "") {
			$abfrage = " " . $querybetreff . " ";
		} else if ($suche['ort'] == "T" && $querytext <> "") {
			$abfrage = " " . $querytext . " ";
		}
		
		if (isset($suche['u_id']) && $suche['u_id']) {
			if ($abfrage == "") {
				$abfrage = " (po_u_id = $suche[u_id]) ";
			} else {
				$abfrage .= " AND (po_u_id = $suche[u_id]) ";
			}
		}
		
		$boards = "";
		$sql2 = "SELECT fo_id, fo_admin, fo_name, th_id, th_name FROM forum left join thema on fo_id = th_fo_id WHERE th_anzthreads <> 0 ORDER BY fo_order, th_order ";
		$query2 = mysqli_query($mysqli_link, $sql2);
		while ($thema = mysqli_fetch_array($query2, MYSQLI_ASSOC)) {
			if (pruefe_leserechte($thema['th_id'])) {
				if ($suche['thema'] == "ALL") {
					if (strlen($boards) == 0) {
						$boards = "po_th_id = " . intval($thema['th_id']);
					} else {
						$boards .= " OR po_th_id = " . intval($thema['th_id']);
					}
				} else if (preg_match("/^B([0-9])+T([0-9])+$/i", $suche['thema'])) {
					$tempthema = substr($suche['thema'],
						-1 * strpos($suche['thema'], "T"), 4);
					if ($thema['th_id'] = $tempthema) {
						$boards = "po_th_id = $thema[th_id]";
					}
				} else if (preg_match("/^B([0-9])+$/i", $suche['thema'])) {
					$tempboard = substr($suche['thema'], 1, 4);
					if ($thema['fo_id'] == $tempboard) {
						if (strlen($boards) == 0) {
							$boards = "po_th_id = " . intval($thema[th_id]);
						} else {
							$boards .= " OR po_th_id = " . intval($thema[th_id]);
						}
					}
				}
			}
		}
		mysqli_free_result($query2);
		
		if (strlen(trim($boards)) == 0) {
			$boards = " 1 = 2 ";
		}
		if (strlen(trim($abfrage)) == 0) {
			$abfrage .= " (" . $boards . ") ";
		} else {
			$abfrage .= " AND (" . $boards . ") ";
		}
		
		$sucheab = 0;
		if ($suche['zeit'] == "B1") {
			$sucheab = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));
		} else if ($suche['zeit'] == "B7") {
			$sucheab = mktime(0, 0, 0, date("m"), date("d") - 7, date("Y"));
		} else if ($suche['zeit'] == "B14") {
			$sucheab = mktime(0, 0, 0, date("m"), date("d") - 14, date("Y"));
		} else if ($suche['zeit'] == "B30") {
			$sucheab = mktime(0, 0, 0, date("m") - 1, date("d"), date("Y"));
		} else if ($suche['zeit'] == "B90") {
			$sucheab = mktime(0, 0, 0, date("m") - 3, date("d"), date("Y"));
		} else if ($suche['zeit'] == "B180") {
			$sucheab = mktime(0, 0, 0, date("m") - 6, date("d"), date("Y"));
		} else if ($suche['zeit'] == "B365") {
			$sucheab = mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1);
		}
		
		if ($sucheab > 0) {
			$abfrage .= " AND (po_ts >= $sucheab) ";
		}
		
		if ($u_level <> "S" and $u_level <> "C")
			$abfrage .= " AND po_gesperrt = 0 ";
			
			if ($suche['sort'] == "SZD") {
				$abfrage .= " ORDER BY po_ts DESC";
			} else if ($suche['sort'] == "SZA") {
				$abfrage .= " ORDER BY po_ts ASC";
			} else if ($suche['sort'] == "SBD") {
				$abfrage .= " ORDER BY po_titel DESC, po_ts DESC";
			} else if ($suche['sort'] == "SBA") {
				$abfrage .= " ORDER BY po_titel ASC, po_ts ASC";
			} else if ($suche['sort'] == "SAD") {
				$abfrage .= " ORDER BY u_nick DESC, po_ts DESC";
			} else if ($suche['sort'] == "SAA") {
				$abfrage .= " ORDER BY u_nick ASC, po_ts ASC";
			} else {
				$abfrage .= " ORDER BY po_ts DESC";
			}
			
			$text = "<table style=\"width:100%;\">\n";
			
			flush();
			$sql = $sql . " " . $abfrage;
			$query = mysqli_query($mysqli_link, $sql);
			
			$anzahl = mysqli_num_rows($query);
			
			$text .= "<tr><td colspan=\"4\" class=\"tabelle_kopfzeile\" style=\"font-weight: bold;\">$t[ergebnis2] $anzahl";
			if ($anzahl > $maxpostingsprosuche) {
				$text .= "<span style=\"color:#ff0000;\"> (Ausgabe wird auf $maxpostingsprosuche begrenzt.)</span>";
			}
			$text .= "</td></tr>\n";
			if ($anzahl > 0) {
				$text .= "<tr>\n";
				$text .= "<td class=\"tabelle_kopfzeile\">$t[forum]</td>\n";
				$text .= "<td class=\"tabelle_kopfzeile\">$t[betreff]</td>\n";
				$text .= "<td class=\"tabelle_kopfzeile\">$t[geschrieben_am]</td>\n";
				$text .= "<td class=\"tabelle_kopfzeile\">$t[geschrieben_autor]</td>\n";
				$text .= "</tr>";
				
				$i = 0;
				while ($fund = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
					$i++;
					
					if ($i > $maxpostingsprosuche) {
						break;
					}
					
					if (($i % 2) > 0) {
						$bgcolor = 'class="tabelle_zeile1"';
					} else {
						$bgcolor = 'class="tabelle_zeile2"';
					}
					
					if (!@in_array($fund['po_id'], $u_gelesene[$fund['po_th_id']])) {
						$col = "color:$farbe_neuesposting_forum;";
					} else {
						$col = '';
					}
					
					$text .= "<tr><td $bgcolor><span class=\"smaller\">" . show_pfad_posting2($fund['po_th_id'], $id, $thread) . "</span></td><td $bgcolor>";
					$thread = vater_rekursiv($fund['po_id']);
					$text .= "<span class=\"smaller\"><b><a href=\"forum.php?id=$id&th_id=" . $fund['po_th_id'] . "&po_id=" . $fund['po_id'] . "&thread=" . $thread . "&aktion=show_posting&seite=1\">
					<span style=\"font-size: smaller; $col \">" . $fund['po_titel'] . "</span></a>";
					if ($fund['po_gesperrt'] == 1) {
						$text .= " <span style=\"color:#ff0000;\">(gesperrt)</span>";
					}
					$text .= "</b></span></td>";
					$text .= "<td $bgcolor><span class=\"smaller\">" . $fund['po_zeit'] . "</span></td>";
					
					if (!$fund['u_nick']) {
						$text .= "<td $bgcolor><span class=\"smaller\"><b>Nobody</b></span></td>\n";
					} else {
						$userdata = array();
						$userdata['u_id'] = $fund['po_u_id'];
						$userdata['u_nick'] = $fund['u_nick'];
						$userdata['u_level'] = $fund['u_level'];
						$userdata['u_punkte_gesamt'] = $fund['u_punkte_gesamt'];
						$userdata['u_punkte_gruppe'] = $fund['u_punkte_gruppe'];
						$userdata['u_chathomepage'] = $fund['u_chathomepage'];
						$userlink = zeige_userdetails($fund['po_u_id'], $userdata);
						if ($fund['u_level'] == 'Z') {
							$text .= "<td $bgcolor><span class=\"smaller\">$userdata[u_nick]</span></td>\n";
						} else {
							$text .= "<td $bgcolor><span class=\"smaller\">$userlink</span></td>\n";
						}
					}
					$text .= "</tr>";
				}
				mysqli_free_result($query);
			}
		$text .= "</table>\n";
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
	}
}
?>