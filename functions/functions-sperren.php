<?php
function sperren_liste() {
	global $id, $t;
	global $raumsperre;
	global $sperre_dialup;
	global $mysqli_link;
	
	$sperr = "";
	$text = '';
	
	for ($i = 0; $i < count($sperre_dialup); $i++) {
		if ($sperr != "") {
			$sperr .= " OR ";
		}
		$sperr .= "is_domain like '%" . mysqli_real_escape_string($mysqli_link, $sperre_dialup[$i]) . "%' ";
	}
	
	if (count($sperre_dialup) >= 1) {
		$query = "DELETE FROM ip_sperre WHERE ($sperr) AND to_days(now())-to_days(is_zeit) > 3 ";
		mysqli_query($mysqli_link, $query);
	}
	
	if ($raumsperre) {
		$query = "DELETE FROM sperre WHERE to_days(now())-to_days(s_zeit) > $raumsperre";
		mysqli_query($mysqli_link, $query);
	}
	
	// Alle Sperren ausgeben
	$query = "SELECT u_nick,is_infotext,is_id,is_domain,UNIX_TIMESTAMP(is_zeit) AS zeit,is_ip_byte,is_warn,"
		. "SUBSTRING_INDEX(is_ip,'.',is_ip_byte) AS isip FROM ip_sperre,user WHERE is_owner=u_id ORDER BY is_zeit DESC,is_domain,is_ip";
	$result = mysqli_query($mysqli_link, $query);
	$rows = mysqli_num_rows($result);
	
	if ($rows > 0) {
		$i = 0;
		
		$text .= "<table style=\"width:100%\">\n";
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst23]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst24]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst25]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst26]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst27]</td>";
		$text .= "</tr>\n";
		
		while ($i < $rows) {
			if ($i % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			
			// Ausgabe in Tabelle
			$row = mysqli_fetch_object($result);
			
			if (strlen($row->isip) > 0) {
				if ($row->is_ip_byte == 1) {
					$isip = $row->isip . ".xxx.yyy.zzz";
				} elseif ($row->is_ip_byte == 2) {
					$isip = $row->isip . ".yyy.zzz";
				} elseif ($row->is_ip_byte == 3) {
					$isip = $row->isip . ".zzz";
				} else {
					$isip = $row->isip;
				}
				
				flush();
				
				if ($row->is_ip_byte == 4) {
					$ip_name = @gethostbyaddr($row->isip);
					if ($ip_name == $row->isip) {
						unset($ip_name);
						$text .= "<tr><td $bgcolor><b>" . $row->isip . "</b></td>\n";
					} else {
						$text .= "<tr><td $bgcolor><b>" . $row->isip . "<br>(" . $ip_name . ")</b></td>\n";
					}
				} else {
					$text .= "<tr><td $bgcolor><b>" . $isip . "</b></td>\n";
				}
			} else {
				flush();
				$ip_name = gethostbyname($row->is_domain);
				if ($ip_name == $row->is_domain) {
					unset($ip_name);
					$text .= "<tr><td $bgcolor><b>" . $row->is_domain . "</b></td>\n";
				} else {
					$text .= "<tr><td $bgcolor><b>" . $row->is_domain . "<br>(" . $ip_name . ")</b></td>\n";
				}
			}
			
			// Infotext
			$text .= "<td $bgcolor>" . $row->is_infotext . "&nbsp;" . "</td>\n";
			
			// Nur Warnung ja/nein
			if ($row->is_warn == "ja") {
				$text .= "<td style=\"vertical-align:middle;\" $bgcolor>" . $t['sperren_warnung'] . "</td>\n";
			} else {
				$text .= "<td style=\"vertical-align:middle;\" $bgcolor>" . $t['sperren_sperre'] . "</td>\n";
			}
			
			// Eintrag - Benutzer und Datum
			$text .= "<td $bgcolor>$row->u_nick<br>\n";
			$text .= date("d.m.y", $row->zeit) . "&nbsp;";
			$text .= date("H:i:s", $row->zeit) . "&nbsp;";
			$text .= "</td>\n";
			
			// Aktion
			if ($row->is_domain == "-GLOBAL-") {
				$text .= "<td $bgcolor><a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperre0\" class=\"button\" title=\"$t[sonst30]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[sonst30]</span></a></td>\n";
			} elseif ($row->is_domain == "-GAST-") {
				$text .= "<td $bgcolor><a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperregast0\" class=\"button\" title=\"$t[sonst30]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[sonst30]</span></a></td>\n";
			} else {
				$text .= "<td $bgcolor>";
				$text .= "<a href=\"inhalt.php?seite=sperren&id=$id&aktion=aendern&is_id=$row->is_id\" class=\"button\" title=\"$t[sonst28]\"><span class=\"fa fa-pencil icon16\"></span> <span>$t[sonst28]</span></a>";
				$text .= "&nbsp;";
				$text .= "<a href=\"inhalt.php?seite=sperren&id=$id&aktion=loeschen&is_id=$row->is_id\" class=\"button\" title=\"$t[sonst29]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[sonst29]</span></a>";
				$text .= "</td>";
			}
			$text .= "</tr>\n";
			
			$i++;
		}
		
		$text .= "</table>\n";
	} else {
		$text .= "<p style=\"text-align:center;\">$t[sperren_keine_zugangssperren_definiert]</p>\n";
	}
	
	mysqli_free_result($result);
	
	zeige_tabelle_zentriert($t['sperren_menue1'], $text);
}

function zeige_blacklist($aktion, $zeilen, $sort) {
	// Zeigt Liste der Blacklist an
	
	global $id, $mysqli_link, $mysqli_link, $u_nick, $u_id, $t;
	global $blacklistmaxdays;
	
	$blurl = "inhalt.php?seite=sperren&aktion=blacklist&id=$id&sort=";
	
	switch ($sort) {
		case "f_zeit desc":
			$qsort = $sort . ",u_nick";
			$fsort = "f_zeit";
			$usort = "u_nick";
			break;
			
		case "f_zeit":
			$qsort = $sort . ",u_nick";
			$fsort = "f_zeit+desc";
			$usort = "u_nick";
			break;
			
		case "u_nick desc":
			$qsort = $sort . ",f_zeit desc";
			$fsort = "f_zeit+desc";
			$usort = "u_nick";
			break;
			
		case "u_nick":
			$qsort = $sort . ",f_zeit desc";
			$fsort = "f_zeit+desc";
			$usort = "u_nick+desc";
			break;
			
		default:
			$qsort = "f_zeit desc, u_nick";
			$fsort = "f_zeit";
			$usort = "u_nick";
	}
	
	switch ($aktion) {
		case "normal":
		default;
		$query = "SELECT u_nick,f_id,f_text,f_userid,f_blacklistid,date_format(f_zeit,'%d.%m.%y %H:%i') AS zeit FROM blacklist LEFT JOIN user ON f_blacklistid=u_id ORDER BY $qsort";
		$button = "LÖSCHEN";
		
		// blacklist-expire
		if ($blacklistmaxdays) {
			$query2 = "DELETE FROM blacklist WHERE (TO_DAYS(NOW()) - TO_DAYS(f_zeit)>$blacklistmaxdays) ";
			mysqli_query($mysqli_link, $query2);
		}
	}
	
	$result = mysqli_query($mysqli_link, $query);
	if ($result) {
		$text = '';
		
		$text .= "<form name=\"eintraege_loeschen\" action=\"inhalt.php?seite=sperren\" method=\"post\">\n"
			. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
			. "<input type=\"hidden\" name=\"aktion\" value=\"blacklist_loesche\">\n"
				. "<table style=\"width:100%;\">";
				
		$anzahl = mysqli_num_rows($result);
		if ($anzahl == 0) {
			// Keine Blacklist-Einträge
			$box = $t['blacklist1'];
			
			$text .= "<tr><td style=\"text-align:left;\" class=\"tabelle_zeile1\">Es sind keine Blacklist-Einträge vorhanden.</td></tr>";
			
		} else {
			// Blacklist anzeigen
			$box = $t['blacklist2'] . $anzahl;
			
			$text .= "<tr><td style=\"width:5%;\" class=\"smaller\">Löschen</td>"
			. "<td style=\"width:35%;\" class=\"smaller\"><a href=\"" . $blurl . $usort . "\">Benutzername</a></td>"
			. "<td style=\"width:35%;\" class=\"smaller\">Info</td>"
			. "<td style=\"width:13%; text-align:center;\" class=\"smaller\"><a href=\"" . $blurl . $fsort . "\">Datum&nbsp;Eintrag</a></td>\n"
			. "<td style=\"width:13%; text-align:center;\" class=\"smaller\">Eintrag von</td></tr>\n";
			
			$i = 0;
			while ($row = mysqli_fetch_object($result)) {
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1 smaller"';
				} else {
					$bgcolor = 'class="tabelle_zeile2 smaller"';
				}
				
				// Benutzer aus der Datenbank lesen
				$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, " . "date_format(u_login,'%d.%m.%y %H:%i') AS login, "
				. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$row->f_blacklistid ";
				$result2 = mysqli_query($mysqli_link, $query);
				if ($result2 && mysqli_num_rows($result2) > 0) {
					
					// Benutzer gefunden -> Ausgeben
					$row2 = mysqli_fetch_object($result2);
					$blacklist_nick = "<b>" . zeige_userdetails($row2->u_id, $row2) . "</b>";
					
				} else {
					
					// Benutzer nicht gefunden, Blacklist-Eintrag löschen
					$blacklist_nick = "NOBODY";
					$query = "DELETE from blacklist WHERE f_id=$row->f_id";
					$result2 = mysqli_query($mysqli_link, $query);
					
				}
				
				// Unterscheidung online ja/nein, Text ausgeben
				if ($row2->o_id) {
					$txt = $blacklist_nick . "<br>online&nbsp;" . gmdate("H:i:s", $row2->online) . "&nbsp;Std/Min/Sek";
				} else {
					$txt = $blacklist_nick . "<br>Letzter&nbsp;Login:&nbsp;" . str_replace(" ", "&nbsp;", $row2->login);
				}
				
				// Infotext setzen
				if ($row->f_text == "") {
					$infotext = "&nbsp;";
				} else {
					$infotext = $row->f_text;
				}
				
				// Nick des Admins (Eintrager) setzen
				$f_userid = $row->f_userid;
				if ($f_userid && $f_userid != "NULL") {
					$admin_nick = zeige_userdetails($f_userid, "", FALSE);
				} else {
					$admin_nick = "";
				}
				
				$text .= "<tr><td style=\"text-align:center;\" $bgcolor>"
				. "<input type=\"checkbox\" name=\"f_blacklistid[]\" value=\"" . $row->f_blacklistid . "\"></td>"
				. "<input type=\"hidden\" name=\"f_nick[]\" value=\"" . $row2->u_nick . "\"></td>" . "<td $bgcolor>" . $txt . "</td>"
				. "<td $bgcolor>" . $infotext . "</td>"
				. "<td style=\"text-align:center;\" $bgcolor>" . $row->zeit . "</td>"
				. "<td style=\"text-align:center;\" $bgcolor>" . $admin_nick . "</td>" . "</tr>\n";
				
				$i++;
			}
			
			$text .= "<tr><td $bgcolor colspan=\"2\"><input type=\"checkbox\" onClick=\"toggle(this.checked)\">Alle Auswählen</td>\n"
				. "<td style=\"text-align:right;\" $bgcolor colspan=\"3\"><input type=\"submit\" name=\"los\" value=\"$button\"></td></tr>\n";
		}
		
		$text .= "</table></form>\n";
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
	}
}

function loesche_blacklist($f_blacklistid) {
	// Löscht Blacklist-Eintrag aus der Tabelle mit f_blacklistid
	// $f_blacklistid Benutzer-ID des Blacklist-Eintrags
	global $id, $mysqli_link, $mysqli_link;
	global $u_id, $u_nick, $admin, $t;
	
	if (!$admin || !$f_blacklistid) {
		// Box anzeigen
		$fehlermeldung = str_replace("%userid%", $f_userid, $t['sperren_fehlermeldung_fehler_beim_loeschen']);
		$fehlermeldung = str_replace("%eintrag%", $blacklist['u_id'], $fehlermeldung);
		zeige_tabelle_zentriert($t['sperren_fehlermeldung'], $fehlermeldung);
		return (0);
	}
	
	$f_blacklistid = intval($f_blacklistid);
	
	$query = "DELETE from blacklist WHERE f_blacklistid=$f_blacklistid";
	$result = mysqli_query($mysqli_link, $query);
	
	$query = "SELECT `u_nick` FROM `user` WHERE `u_id`=$f_blacklistid";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$f_nick = mysqli_result($result, 0, 0);
		
		// Box anzeigen
		$erfolgsmeldung = str_replace("%username%", $f_nick, $t['sperren_erfolgsmeldung_blacklisteintrag_geloescht']);
		zeige_tabelle_zentriert($t['sperren_erfolgsmeldung'], $erfolgsmeldung);
	}
	mysqli_free_result($result);
}

function formular_neuer_blacklist($neuer_blacklist) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Blacklist-Eintrag aus
	global $id, $mysqli_link, $t;
	
	$box = $t['blacklist3'];
	$text = '';
	
	$text .= "<form name=\"blacklist_neu\" action=\"inhalt.php?seite=sperren\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"blacklist_neu2\">\n"
			. "<table style=\"width:100%;\">";
			
	if (!isset($neuer_blacklist['u_nick'])) {
		$neuer_blacklist['u_nick'] = "";
	}
	if (!isset($neuer_blacklist['f_text'])) {
		$neuer_blacklist['f_text'] = "";
	}
	
	$text .= "<tr><td style=\"text-align:right; font-weight:bold;\" class=\"tabelle_zeile1\">"."Benutzername:</td>"
	. "<td class=\"tabelle_zeile1 smaller\"><input type=\"text\" name=\"neuer_blacklist[u_nick]\" value=\"" . htmlspecialchars($neuer_blacklist['u_nick']) . "\" size=20></td></tr>\n"
	. "<tr><td style=\"text-align:right; font-weight:bold;\" class=\"tabelle_zeile1\">Infotext:</td>"
	. "<td class=\"tabelle_zeile1 smaller\"><input type=\"text\" name=\"neuer_blacklist[f_text]\" value=\"" . htmlspecialchars($neuer_blacklist['f_text']) . "\" size=45>" . "&nbsp;"
	. "<input type=\"submit\" name=\"los\" value=\"Eintragen\"></td></tr>\n" . "</table></form>\n";
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}

function neuer_blacklist($f_userid, $blacklist) {
	// Trägt neuen Blacklist-Eintrag in der Datenbank ein
	global $id, $mysqli_link, $t;
	
	if (!$blacklist['u_id'] || !$f_userid) {
		// Box anzeigen
		$fehlermeldung = str_replace("%userid%", $f_userid, $t['sperren_fehlermeldung_fehler_beim_anlegen']);
		$fehlermeldung = str_replace("%eintrag%", $blacklist['u_id'], $fehlermeldung);
		zeige_tabelle_zentriert($t['sperren_fehlermeldung'], $fehlermeldung);
	} else {
		$blacklist['u_id'] = mysqli_real_escape_string($mysqli_link, $blacklist['u_id']); // sec
		$f_userid = mysqli_real_escape_string($mysqli_link, $f_userid); // sec
		
		// Prüfen ob Blacklist-Eintrag bereits in Tabelle steht
		$query = "SELECT f_id from blacklist WHERE (f_userid=$blacklist[u_id] AND f_blacklistid=$f_userid) OR (f_userid=$f_userid AND f_blacklistid=$blacklist[u_id])";
			
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			
			// Box anzeigen
			$fehlermeldung = str_replace("%username%", $blacklist['u_nick'], $t['sperren_fehlermeldung_eintrag_bereits_vorhanden']);
			zeige_tabelle_zentriert($t['sperren_fehlermeldung'], $fehlermeldung);
		} else if ($blacklist['u_id'] == $f_userid) {
			// Eigener Blacklist-Eintrag ist verboten
			
			// Box anzeigen
			zeige_tabelle_zentriert($t['sperren_fehlermeldung'], $t['sperren_fehlermeldung_selbst_hinzufuegen_nicht_moeglich']);
		} else {
			// Benutzer ist noch kein Blacklist-Eintrag -> hinzufügen
			$f['f_userid'] = $f_userid;
			$f['f_blacklistid'] = $blacklist['u_id'];
			$f['f_text'] = htmlspecialchars($blacklist['f_text']);
			schreibe_db("blacklist", $f, 0, "f_id");
			
			// Box anzeigen
			$erfolgsmeldung = str_replace("%username%", $blacklist['u_nick'], $t['sperren_erfolgsmeldung_blacklisteintrag_erfolgreich']);
			zeige_tabelle_zentriert($t['sperren_erfolgsmeldung'], $erfolgsmeldung);
		}
	}
}
?>