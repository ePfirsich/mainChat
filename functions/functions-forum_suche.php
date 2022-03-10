<?php
function show_pfad_posting2($th_id, $thread) {
	//Infos über Forum und Thema holen
	$query = pdoQuery("SELECT `fo_id`, `fo_name`, `th_name` FROM `forum_kategorien`, `forum_foren` WHERE `th_id` = :th_id AND `fo_id` = `th_fo_id`", [':th_id'=>$th_id]);
	$result = $query->fetch();
	
	$fo_id = $result['fo_id'];
	$fo_name = htmlspecialchars( $result['fo_name'] );
	$th_name = htmlspecialchars( $result['th_name'] );
	
	return "<a href=\"forum.php#$fo_id\">" . html_entity_decode($fo_name) . "</a> > <a href=\"forum.php?th_id=$th_id&aktion=show_forum&seite=1\">" . html_entity_decode($th_name) . "</a>";
	
}

function vater_rekursiv($vater) {
	$query = pdoQuery("SELECT `po_id`, `po_vater_id` FROM `forum_beitraege` WHERE `po_id` = :po_id", [':po_id'=>$vater]);
	
	$resultCount = $query->rowCount();
	$result = $query->fetch();
	if ($resultCount <> 1) {
		return -1;
	} else if ($result['po_vater_id'] <> 0) {
		$vater = vater_rekursiv($result['po_vater_id']);
		return $vater;
	} else {
		return $result['po_id'];
	}
}

function such_bereich() {
	global $suche, $lang;
	
	$select_breite = 250;
	
	$text = '';
	
	$text .= "<form action=\"forum.php\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"suchergebnisse\">\n";
	$text .= "<table style=\"width:100%\">\n";
	
	// Suchtext
	$text .= "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile1\"><b>$lang[suche1]</b></td><td class=\"tabelle_zeile1\">"
	. "<input type=\"text\" name=\"suche_text\" value=\"" . $suche['text'] . "\" size=\"50\"></td></tr>\n";
		
	// Suche in Board/Thema
	$text .= "<tr><td style=\"text-align:right; vertical-align:top;\" class=\"tabelle_zeile1\">$lang[suche2]</td><td class=\"tabelle_zeile1\">"
	. "<select name=\"suche_thema\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	
	$query = pdoQuery("SELECT `fo_id`, `fo_admin`, `fo_name`, `th_id`, `th_name` FROM `forum_kategorien` LEFT JOIN `forum_foren` ON `fo_id` = `th_fo_id` WHERE `th_anzthreads` <> 0 ORDER BY `fo_order`, `th_order`", []);
	
	$result = $query->fetchAll();
	
	$themaalt = "";
	$text .= "<option ";
	if (substr($suche['thema'], 0, 1) <> "B") {
		$text .= "selected ";
	}
	$text .= "value=\"ALL\">$lang[option1]</option>";
	foreach($result as $zaehler => $thema) {
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
			if ($suche['thema'] == "B" . $thema['fo_id'] . "T" . $thema['th_id']) {
					$text .= "selected ";
			}
			$text .= "value=\"B" . $thema['fo_id'] . "T" . $thema['th_id'] . "\">&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;" . $thema['th_name'] . "</option>";
		}
	}
	$text .= "</select></td></tr>\n";
	
	// Sucheinstelung UND/ODER
	$text .= "<tr><td rowspan=\"4\" style=\"text-align:right; vertical-align:top;\" class=\"tabelle_zeile1\">$lang[suche3]</td><td class=\"tabelle_zeile1\"><select name=\"suche_modus\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	$text .= "<option ";
	if ($suche['modus'] <> "O") {
		$text .= "selected ";
	}
	$text .= "value=\"A\">$lang[option2]</option>";
	$text .= "<option ";
	if ($suche['modus'] == "O") {
		$text .= "selected ";
	}
	$text .= "value=\"O\">$lang[option3]</option>";
	$text .= "</select></td></tr>\n";
	
	// Sucheinstellung Betreff/Text
	$text .= "<tr><td class=\"tabelle_zeile1\"><select name=\"suche_ort\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	$text .= "<option ";
	if ($suche['ort'] <> "B" && $suche['ort'] <> "T") {
		$text .= "selected ";
	}
	$text .= "value=\"V\">$lang[option4]</option>";
	$text .= "<option ";
	if ($suche['ort'] == "B") {
		$text .= "selected ";
	}
	$text .= "value=\"B\">$lang[option5]</option>";
	$text .= "<option ";
	if ($suche['ort'] == "T") {
		$text .= "selected ";
	}
	$text .= "value=\"T\">$lang[option6]</option>";
	$text .= "</select></td></tr>\n";
	
	// Sucheinstellung Zeit
	$text .= "<tr><td class=\"tabelle_zeile1\"><select name=\"suche_zeit\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	$text .= "<option ";
	if (substr($suche['zeit'], 0, 1) <> "B") {
		$text .= "selected ";
	}
	$text .= "value=\"ALL\">$lang[option7]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B1") {
		$text .= "selected ";
	}
	$text .= "value=\"B1\">$lang[option8]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B7") {
		$text .= "selected ";
	}
	$text .= "value=\"B7\">$lang[option9]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B14") {
		$text .= "selected ";
	}
	$text .= "value=\"B14\">$lang[option10]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B30") {
		$text .= "selected ";
	}
	$text .= "value=\"B30\">$lang[option11]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B90") {
		$text .= "selected ";
	}
	$text .= "value=\"B90\">$lang[option12]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B180") {
		$text .= "selected ";
	}
	$text .= "value=\"B180\">$lang[option13]</option>";
	$text .= "<option ";
	if ($suche['zeit'] == "B365") {
		$text .= "selected ";
	}
	$text .= "value=\"B365\">$lang[option14]</option>";
	$text .= "</select></td></tr>\n";
	
	// Sucheinstellung Sortierung
	$text .= "<tr><td class=\"tabelle_zeile1\"><select name=\"suche_sort\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	$text .= "<option ";
	if (substr($suche['sort'], 0, 1) <> "S") {
		$text .= "selected ";
	}
	$text .= "value=\"DEFAULT\">$lang[option15]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SZA") {
		$text .= "selected ";
	}
	$text .= "value=\"SZA\">$lang[option16]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SBA") {
		$text .= "selected ";
	}
	$text .= "value=\"SBA\">$lang[option17]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SBD") {
		$text .= "selected ";
	}
	$text .= "value=\"SBD\">$lang[option18]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SAA") {
		$text .= "selected ";
	}
	$text .= "value=\"SAA\">$lang[option19]</option>";
	$text .= "<option ";
	if ($suche['sort'] == "SAD") {
		$text .= "selected ";
	}
	$text .= "value=\"SAD\">$lang[option20]</option>";
	$text .= "</select></td></tr>\n";
	
	// nur von Benutzer
	$text .= "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile1\">$lang[suche4]</td>\n"
		. "<td class=\"tabelle_zeile1\"><input type=\"text\" name=\"suche_username\" value=\""
		. htmlspecialchars($suche['username']) . "\" size=\"20\"></td></tr>\n";
	
	$text .= "<tr><td class=\"tabelle_zeile1\">&nbsp;</td><td class=\"tabelle_zeile1\">"
	. "<input type=\"submit\" value=\"$lang[suche5]\"></td></tr>\n";
	
	$text .= "</table>\n";
	
	
	return $text;
}

function such_ergebnis() {
	global $check_name, $u_id, $suche, $lang, $u_level;
	
	$text = "";
	$fehlermeldung = "";
	$maxpostingsprosuche = 1000;
	
	$query = pdoQuery("SELECT `u_gelesene_postings` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>intval($u_id)]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		$gelesene = $result['u_gelesene_postings'];
	}
	$u_gelesene = unserialize($gelesene);
	
	$fehler = "";
	if ($suche['username'] <> coreCheckName($suche['username'], $check_name)) {
		$fehler .= $lang['forum_suche_fehlermeldung_benutzername'];
	}
	$suche['username'] = coreCheckName($suche['username'], $check_name);
	unset($suche['u_id']);
	if (strlen($fehler) == 0 && $suche['username'] <> "") {
		$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$suche['username']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$suche['u_id'] = $result['u_id'];
		} else {
			$fehlermeldung = str_replace("%u_nick%", $suche['username'], $lang['forum_suche_fehlermeldung_benutzername_unbekannt']);
			$text .= hinweis($fehlermeldung, "fehler");
		}
	}
	
	if (trim($suche['username']) == "" && trim($suche['text']) == "" && (!($suche['zeit'] == "B1" || $suche['zeit'] == "B7" || $suche['zeit'] == "B14"))) {
		$fehlermeldung = $lang['forum_suche_fehlermeldung_zeitangabe'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	if ($suche['modus'] <> "A" && $suche['modus'] <> "O") {
		$fehlermeldung = $lang['forum_suche_fehlermeldung_sucheinstellung_woerter'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	if ($suche['ort'] <> "V" && $suche['ort'] <> "B" && $suche['ort'] <> "T") {
		$fehlermeldung = $lang['forum_suche_fehlermeldung_sucheinstellung_ort'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	if (!$suche['thema'] == "ALL" && !preg_match("/^B([0-9])+T([0-9])+$/i", $suche['thema']) && !preg_match("/^B([0-9])+$/i", $suche['thema'])) {
		$fehlermeldung = $lang['forum_suche_fehlermeldung_falsches_thema'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if($fehlermeldung == "") {
		$querytext = "";
		$querybetreff = "";
		if (trim($suche['text']) <> "") {
			$suche['text'] = htmlspecialchars($suche['text']);
			$suchetext = explode(" ", $suche['text']);
			
			for ($i = 0; $i < count($suchetext); $i++) {
				if (strlen($querytext) == 0) {
					$querytext = "po_text LIKE \"%" . $suchetext[$i] . "%\"";
				} else {
					if ($suche['modus'] == "O") {
						$querytext .= " OR po_text LIKE \"%" . $suchetext[$i] . "%\"";
					} else {
						$querytext .= " AND po_text LIKE \"%" . $suchetext[$i] . "%\"";
					}
				}
				
				if (strlen($querybetreff) == 0) {
					$querybetreff = "po_titel LIKE \"%" . $suchetext[$i] . "%\"";
				} else {
					if ($suche['modus'] == "O") {
						$querybetreff .= " OR po_titel LIKE \"%" . $suchetext[$i] . "%\"";
					} else {
						$querybetreff .= " AND po_titel LIKE \"%" . $suchetext[$i] . "%\"";
					}
				}
			}
			$querytext = " (" . $querytext . ") ";
			$querybetreff = " (" . $querybetreff . ") ";
		}
		
		$sql = "SELECT `forum_beitraege`.*, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_zeit, u_id, u_nick, u_level, u_punkte_gesamt, u_punkte_gruppe, u_chathomepage FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id WHERE ";
		
		$abfrage = "";
		if ($suche['ort'] == "V" && $querybetreff <> "") {
			$abfrage = " (" . $querybetreff . " OR " . $querytext . ") ";
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
		$query2 = pdoQuery("SELECT `fo_id`, `fo_admin`, `fo_name`, `th_id`, `th_name` FROM `forum_kategorien` LEFT JOIN `forum_foren` ON `fo_id` = `th_fo_id` WHERE `th_anzthreads` <> 0 ORDER BY `fo_order`, `th_order`", []);
		
		$result2 = $query2->fetchAll();
		foreach($result2 as $zaehler => $thema) {
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
						$boards = "`po_th_id` = $thema[th_id]";
					}
				} else if (preg_match("/^B([0-9])+$/i", $suche['thema'])) {
					$tempboard = substr($suche['thema'], 1, 4);
					if ($thema['fo_id'] == $tempboard) {
						if (strlen($boards) == 0) {
							$boards = "`po_th_id` = " . intval($thema['th_id']);
						} else {
							$boards .= " OR `po_th_id` = " . intval($thema['th_id']);
						}
					}
				}
			}
		}
		
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
		
		$text = "<br>";
		$text .= "<table style=\"width:100%;\">\n";
		
		flush();
		$sql = $sql . " " . $abfrage;
		
		$query = pdoQuery($sql, []);
		
		$resultCount = $query->rowCount();
		
		$erfolgsmeldung = str_replace("%anzahl%", $resultCount, $lang['forum_suche_erfolgsmeldung']);
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		
		if ($resultCount > $maxpostingsprosuche) {
			$text .= "<span style=\"color:#ff0000;\"> (Ausgabe wird auf $maxpostingsprosuche begrenzt.)</span>";
		}
		$text .= "</td></tr>\n";
		if ($resultCount > 0) {
			$text .= "<tr>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[forum]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[betreff]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[geschrieben_am]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[geschrieben_autor]</td>\n";
			$text .= "</tr>";
			
			$i = 0;
			$result = $query->fetchAll();
			foreach($result as $zaehler => $fund) {
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
					$col = "font-weight:bold;";
				} else {
					$col = '';
				}
				
				$thread = vater_rekursiv($fund['po_id']);
				$text .= "<tr><td $bgcolor><span class=\"smaller\">" . show_pfad_posting2($fund['po_th_id'], $thread) . "</span></td>";
				$text .= "<td $bgcolor><span class=\"smaller\"><b><a href=\"forum.php?th_id=" . $fund['po_th_id'] . "&po_id=" . $fund['po_id'] . "&thread=" . $thread . "&aktion=show_posting&seite=1\">
				<span style=\"font-size: smaller; $col \">" . html_entity_decode($fund['po_titel']) . "</span></a>";
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
		}
		$text .= "</table>\n";
	}
	
	return $text;
}
?>