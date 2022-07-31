<?php
function show_pfad_posting2($forum_id, $thread) {
	//Infos über Forum und Thema holen
	$query = pdoQuery("SELECT `kat_id`, `kat_name`, `forum_name` FROM `forum_kategorien`, `forum_foren` WHERE `forum_id` = :forum_id AND `kat_id` = `forum_kategorie_id`", [':forum_id'=>$forum_id]);
	$result = $query->fetch();
	
	$kat_id = $result['kat_id'];
	$kat_name = $result['kat_name'];
	$forum_name = $result['forum_name'];
	
	return "<a href=\"forum.php?bereich=forum#$kat_id\">" . html_entity_decode($kat_name) . "</a> > <a href=\"forum.php?bereich=forum&forum_id=$forum_id&aktion=show_forum&seite=1\">" . html_entity_decode($forum_name) . "</a>";
	
}

function vater_rekursiv($vater) {
	$query = pdoQuery("SELECT `beitrag_id`, `beitrag_thema_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$vater]);
	
	$resultCount = $query->rowCount();
	$result = $query->fetch();
	if ($resultCount <> 1) {
		return -1;
	} else if ($result['beitrag_thema_id'] <> 0) {
		$vater = vater_rekursiv($result['beitrag_thema_id']);
		return $vater;
	} else {
		return $result['beitrag_id'];
	}
}

function such_bereich() {
	global $suche, $lang;
	
	$select_breite = 250;
	
	$text = '';
	
	$text .= "<form action=\"forum.php?bereich=forum\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"suchergebnisse\">\n";
	$text .= "<table style=\"width:100%\">\n";
	
	// Suchtext
	$text .= "<tr><td style=\"text-align:right;\" class=\"tabelle_zeile1\"><b>$lang[suche1]</b></td><td class=\"tabelle_zeile1\">"
	. "<input type=\"text\" name=\"suche_text\" value=\"" . $suche['text'] . "\" size=\"50\"></td></tr>\n";
		
	// Suche in Board/Thema
	$text .= "<tr><td style=\"text-align:right; vertical-align:top;\" class=\"tabelle_zeile1\">$lang[suche2]</td><td class=\"tabelle_zeile1\">"
	. "<select name=\"suche_thema\" size=\"1\" style=\"width: " . $select_breite . "px;\">";
	
	$query = pdoQuery("SELECT `kat_id`, `kat_name`, `forum_id`, `forum_name` FROM `forum_kategorien` LEFT JOIN `forum_foren` ON `kat_id` = `forum_kategorie_id` WHERE `forum_anzahl_themen` <> 0 ORDER BY `kat_order`, `forum_order`", []);
	
	$result = $query->fetchAll();
	
	$themaalt = "";
	$text .= "<option ";
	if (substr($suche['thema'], 0, 1) <> "B") {
		$text .= "selected ";
	}
	$text .= "value=\"ALL\">$lang[option1]</option>";
	foreach($result as $zaehler => $thema) {
		if (pruefe_leserechte($thema['forum_id'])) {
			if ($themaalt <> $thema['kat_name']) {
				$text .= "<option ";
				if ($suche['thema'] == "B" . $thema['kat_id']) {
					$text .= "selected ";
				}
				$text .= "value=\"B" . $thema['kat_id'] . "\">" . $thema['kat_name']
				. "</option>";
				$themaalt = $thema['kat_name'];
			}
			$text .= "<option ";
			if ($suche['thema'] == "B" . $thema['kat_id'] . "T" . $thema['forum_id']) {
					$text .= "selected ";
			}
			$text .= "value=\"B" . $thema['kat_id'] . "T" . $thema['forum_id'] . "\">&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;" . $thema['forum_name'] . "</option>";
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
	
	$query = pdoQuery("SELECT `u_gelesene_postings` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
	
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
					$querytext = "`beitrag_text` LIKE \"%" . $suchetext[$i] . "%\"";
				} else {
					if ($suche['modus'] == "O") {
						$querytext .= " OR `beitrag_text` LIKE \"%" . $suchetext[$i] . "%\"";
					} else {
						$querytext .= " AND `beitrag_text` LIKE \"%" . $suchetext[$i] . "%\"";
					}
				}
				
				if (strlen($querybetreff) == 0) {
					$querybetreff = "`beitrag_titel` LIKE \"%" . $suchetext[$i] . "%\"";
				} else {
					if ($suche['modus'] == "O") {
						$querybetreff .= " OR `beitrag_titel` LIKE \"%" . $suchetext[$i] . "%\"";
					} else {
						$querybetreff .= " AND `beitrag_titel` LIKE \"%" . $suchetext[$i] . "%\"";
					}
				}
			}
			$querytext = " (" . $querytext . ") ";
			$querybetreff = " (" . $querybetreff . ") ";
		}
		
		$sql = "SELECT `forum_beitraege`.*, date_format(from_unixtime(beitrag_thema_timestamp), '%d.%m.%Y, %H:%i:%s') AS `po_zeit`, `u_id`, `u_nick`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_chathomepage` FROM `forum_beitraege` LEFT JOIN `user` ON `beitrag_user_id` = u_id WHERE ";
		
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
				$abfrage = " (beitrag_user_id = $suche[u_id]) ";
			} else {
				$abfrage .= " AND (beitrag_user_id = $suche[u_id]) ";
			}
		}
		
		$boards = "";
		$query2 = pdoQuery("SELECT `kat_id`, `forum_id`, `forum_name` FROM `forum_kategorien` LEFT JOIN `forum_foren` ON `kat_id` = `forum_kategorie_id` WHERE `forum_anzahl_themen` <> 0 ORDER BY `kat_order`, `forum_order`", []);
		
		$result2 = $query2->fetchAll();
		foreach($result2 as $zaehler => $thema) {
			if (pruefe_leserechte($thema['forum_id'])) {
				if ($suche['thema'] == "ALL") {
					if (strlen($boards) == 0) {
						$boards = "beitrag_forum_id = " . $thema['forum_id'];
					} else {
						$boards .= " OR beitrag_forum_id = " . $thema['forum_id'];
					}
				} else if (preg_match("/^B([0-9])+T([0-9])+$/i", $suche['thema'])) {
					$tempthema = substr($suche['thema'],
						-1 * strpos($suche['thema'], "T"), 4);
					if ($thema['forum_id'] = $tempthema) {
						$boards = "`beitrag_forum_id` = $thema[forum_id]";
					}
				} else if (preg_match("/^B([0-9])+$/i", $suche['thema'])) {
					$tempboard = substr($suche['thema'], 1, 4);
					if ($thema['kat_id'] == $tempboard) {
						if (strlen($boards) == 0) {
							$boards = "`beitrag_forum_id` = " . $thema['forum_id'];
						} else {
							$boards .= " OR `beitrag_forum_id` = " . $thema['forum_id'];
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
			$abfrage .= " AND (beitrag_thema_timestamp >= $sucheab) ";
		}
		
		if ($suche['sort'] == "SZD") {
			$abfrage .= " ORDER BY beitrag_thema_timestamp DESC";
		} else if ($suche['sort'] == "SZA") {
			$abfrage .= " ORDER BY beitrag_thema_timestamp ASC";
		} else if ($suche['sort'] == "SBD") {
			$abfrage .= " ORDER BY beitrag_titel DESC, beitrag_thema_timestamp DESC";
		} else if ($suche['sort'] == "SBA") {
			$abfrage .= " ORDER BY beitrag_titel ASC, beitrag_thema_timestamp ASC";
		} else if ($suche['sort'] == "SAD") {
			$abfrage .= " ORDER BY u_nick DESC, beitrag_thema_timestamp DESC";
		} else if ($suche['sort'] == "SAA") {
			$abfrage .= " ORDER BY u_nick ASC, beitrag_thema_timestamp ASC";
		} else {
			$abfrage .= " ORDER BY beitrag_thema_timestamp DESC";
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
				
				if (!@in_array($fund['beitrag_id'], $u_gelesene[$fund['beitrag_forum_id']])) {
					$col = "font-weight:bold;";
				} else {
					$col = '';
				}
				
				$thread = vater_rekursiv($fund['beitrag_id']);
				$text .= "<tr><td $bgcolor><span class=\"smaller\">" . show_pfad_posting2($fund['beitrag_forum_id'], $thread) . "</span></td>";
				$text .= "<td $bgcolor><span class=\"smaller\"><b><a href=\"forum.php?bereich=forum&forum_id=" . $fund['beitrag_forum_id'] . "&beitrag_id=" . $fund['beitrag_id'] . "&thread=" . $thread . "&aktion=zeige_thema&seite=1\">
				<span style=\"font-size: smaller; $col \">" . html_entity_decode($fund['beitrag_titel']) . "</span></a>";
				$text .= "</b></span></td>";
				$text .= "<td $bgcolor><span class=\"smaller\">" . $fund['po_zeit'] . "</span></td>";
				
				if (!$fund['u_nick']) {
					$text .= "<td $bgcolor><span class=\"smaller\"><b>Nobody</b></span></td>\n";
				} else {
					$userlink = zeige_userdetails($fund['beitrag_user_id']);
					if ($fund['u_level'] == 'Z') {
						$text .= "<td $bgcolor><span class=\"smaller\">$fund[u_nick]</span></td>\n";
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