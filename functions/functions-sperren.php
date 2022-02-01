<?php
function sperren_liste($text) {
	global $id, $t, $raumsperre, $sperre_dialup, $locale;
	
	$sperr = "";
	
	for ($i = 0; $i < count($sperre_dialup); $i++) {
		if ($sperr != "") {
			$sperr .= " OR ";
		}
		$sperr .= "is_domain like '%" . escape_string($sperre_dialup[$i]) . "%' ";
	}
	
	if (count($sperre_dialup) >= 1) {
		$query = "DELETE FROM ip_sperre WHERE ($sperr) AND to_days(now())-to_days(is_zeit) > 3 ";
		sqlUpdate($query, true);
	}
	
	if ($raumsperre) {
		$query = "DELETE FROM sperre WHERE to_days(now())-to_days(s_zeit) > $raumsperre";
		sqlUpdate($query, true);
	}
	
	// Alle Sperren ausgeben
	$sql = "SET lc_time_names = '$locale'";
	$query = sqlQuery($sql);
	
	$query = "SELECT u_nick,is_infotext,is_id,is_domain, date_format(is_zeit,'%d. %M %Y um %H:%i') AS zeit,is_ip_byte,is_warn,"
		. "SUBSTRING_INDEX(is_ip,'.',is_ip_byte) AS isip FROM ip_sperre,user WHERE is_owner=u_id ORDER BY is_zeit DESC,is_domain,is_ip";
		$result = sqlQuery($query);
	$rows = mysqli_num_rows($result);
	
	if ($rows > 0) {
		$i = 0;
		
		$text .= "<table style=\"width:100%\">\n";
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sperren_adresse]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sperren_info]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sperren_art]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sperren_eintrag]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sperren_aktion]</td>";
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
			$text .= $row->zeit;
			$text .= "</td>\n";
			
			// Aktion
			if ($row->is_domain == "-GLOBAL-") {
				$text .= "<td $bgcolor><a href=\"inhalt.php?bereich=sperren&id=$id&aktion=loginsperre0\" class=\"button\" title=\"$t[sperren_deaktivieren]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[sperren_deaktivieren]</span></a></td>\n";
			} elseif ($row->is_domain == "-GAST-") {
				$text .= "<td $bgcolor><a href=\"inhalt.php?bereich=sperren&id=$id&aktion=loginsperregast0\" class=\"button\" title=\"$t[sperren_deaktivieren]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[sperren_deaktivieren]</span></a></td>\n";
			} else {
				$text .= "<td $bgcolor>";
				$text .= "<a href=\"inhalt.php?bereich=sperren&id=$id&aktion=aendern&is_id=$row->is_id\" class=\"button\" title=\"$t[sperren_aendern]\"><span class=\"fa fa-pencil icon16\"></span> <span>$t[sperren_aendern]</span></a>";
				$text .= "&nbsp;";
				$text .= "<a href=\"inhalt.php?bereich=sperren&id=$id&aktion=loeschen&is_id=$row->is_id\" class=\"button\" title=\"$t[sperren_loeschen]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[sperren_loeschen]</span></a>";
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

function zeige_blacklist($text, $aktion, $zeilen, $sort) {
	// Zeigt Liste der Blacklist an
	
	global $id, $t, $blacklistmaxdays, $locale;
	
	$blurl = "inhalt.php?bereich=sperren&aktion=blacklist&id=$id&sort=";
	
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
	
	$sql = "SET lc_time_names = '$locale'";
	$query = sqlQuery($sql);
	
	$query = "SELECT u_nick,f_id,f_text,f_userid,f_blacklistid,date_format(f_zeit,'%d. %M %Y um %H:%i') AS zeit FROM blacklist LEFT JOIN user ON f_blacklistid=u_id ORDER BY $qsort";
	
	// blacklist-expire
	if ($blacklistmaxdays) {
		$query2 = "DELETE FROM blacklist WHERE (TO_DAYS(NOW()) - TO_DAYS(f_zeit)>$blacklistmaxdays) ";
		sqlUpdate($query2, true);
	}
	
	$result = sqlQuery($query);
	if ($result) {
		$text .= "<form name=\"eintraege_loeschen\" action=\"inhalt.php?bereich=sperren\" method=\"post\">\n";
		$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"blacklist_loesche\">\n";
		
		$text .= "<table style=\"width:100%;\">\n";
				
		$anzahl = mysqli_num_rows($result);
		if ($anzahl == 0) {
			// Keine Blacklist-Einträge
			$text .= "<tr><td style=\"text-align:left;\" class=\"tabelle_zeile1\">$t[sperren_blackliste_leer]</td></tr>";
			
		} else {
			$text .= "<tr><td style=\"width:5%;\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
			$text .= "<td style=\"width:35%;\" class=\"tabelle_kopfzeile\"><a href=\"" . $blurl . $usort . "\">$t[sperren_benutzername]</a></td>\n";
			$text .= "<td style=\"width:35%;\" class=\"tabelle_kopfzeile\">$t[sperren_info]</td>\n";
			$text .= "<td style=\"width:13%; text-align:center;\" class=\"tabelle_kopfzeile\"><a href=\"" . $blurl . $fsort . "\">$t[sperren_eintrag]</a></td>\n";
			$text .= "<td style=\"width:13%; text-align:center;\" class=\"tabelle_kopfzeile\">$t[sperren_eintrag_von]</td></tr>\n";
			
			$i = 0;
			while ($row = mysqli_fetch_object($result)) {
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1 smaller"';
				} else {
					$bgcolor = 'class="tabelle_zeile2 smaller"';
				}
				
				// Benutzer aus der Datenbank lesen
				$sql = "SET lc_time_names = '$locale'";
				$query = sqlQuery($sql);
				
				$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, date_format(u_login,'%d. %M %Y um %H:%i') AS login, "
				. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$row->f_blacklistid ";
				$result2 = sqlQuery($query);
				if ($result2 && mysqli_num_rows($result2) > 0) {
					
					// Benutzer gefunden -> Ausgeben
					$row2 = mysqli_fetch_object($result2);
					$blacklist_nick = "<b>" . zeige_userdetails($row2->u_id, $row2) . "</b>";
					
				} else {
					
					// Benutzer nicht gefunden, Blacklist-Eintrag löschen
					$blacklist_nick = "NOBODY";
					$query = "DELETE FROM blacklist WHERE f_id=$row->f_id";
					$result2 = sqlUpdate($query);
					
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
				
				$text .= "<tr><td style=\"text-align:center;\" $bgcolor><input type=\"checkbox\" name=\"bearbeite_ids[]\" value=\"" . $row->f_blacklistid . "\"></td>\n";
				$text .= "<td $bgcolor>" . $txt . "</td>\n";
				$text .= "<td $bgcolor>" . $infotext . "</td>\n";
				$text .= "<td style=\"text-align:center;\" $bgcolor>" . $row->zeit . "</td>\n";
				$text .= "<td style=\"text-align:center;\" $bgcolor>" . $admin_nick . "</td>\n";
				$text .= "</tr>\n";
				
				$i++;
			}
			
			$text .= "<tr><td $bgcolor colspan=\"2\"><input type=\"checkbox\" onClick=\"toggle(this.checked)\">$t[sperren_alle_auswaehlen]</td>\n";
			$text .= "<td style=\"text-align:right;\" $bgcolor colspan=\"3\"><input type=\"submit\" name=\"los\" value=\"$t[sperren_loeschen]\"></td>\n";
			$text .= "</tr>\n";
		}
		
		$text .= "</table></form>\n";
	}
	
	$box = $t['sperren_blacklist'];
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}

function loesche_blacklist($blacklist_id) {
	// Löscht Blacklist-Eintrag aus der Tabelle mit f_blacklistid
	// $blacklist_id Benutzer-ID des Blacklist-Eintrags
	global $id, $admin, $t;
	
	$text = "";
	if (!$admin || !$blacklist_id) {
		// Box anzeigen
		$fehlermeldung = $t['sperren_fehlermeldung_fehler_beim_loeschen'];
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		$blacklist_id = intval($blacklist_id);
		
		$query = "SELECT `f_text` FROM `blacklist` WHERE `f_blacklistid` = $blacklist_id";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) != 0) {
			$f_text = mysqli_result($result, 0, 0);
			
			$erfolgsmeldung = str_replace("%username%", $f_text, $t['sperren_erfolgsmeldung_blacklisteintrag_geloescht']);
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
			
			$query = "DELETE FROM `blacklist` WHERE `f_blacklistid` = $blacklist_id";
			$result = sqlUpdate($query);
		} else {
			$fehlermeldung = $t['sperren_fehlermeldung_fehler_beim_loeschen'];
			$text .= hinweis($fehlermeldung, "fehler");
		}
	}
	
	return $text;
}

function formular_neuer_blacklist($text, $daten) {
	// Gibt Formular für Benutzernamen zum Hinzufügen als Blacklist-Eintrag aus
	global $id, $t;
	
	$box = $t['sperren_blacklist_neu'];
	
	$zaehler = 0;
	$text .= "<form name=\"blacklist_neu\" action=\"inhalt.php?bereich=sperren\" method=\"post\">\n";
	$text .= "<table style=\"width:100%;\">";
	
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"blacklist_neu2\">\n";
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $t['sperren_benutzername'], "daten_nick", htmlspecialchars($daten['u_nick']));
	$zaehler++;
	
	// Info
	$text .= zeige_formularfelder("input", $zaehler, $t['sperren_info'], "daten_text", htmlspecialchars($daten['f_text']));
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"los\" value=\"$t[sperren_eintragen]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	$text .= "</form>\n";
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}

function neuer_blacklist_eintrag($f_userid, $daten) {
	// Trägt neuen Blacklist-Eintrag in der Datenbank ein
	global $id, $t;
	
	$text = "";
	if (!$daten['id'] || !$f_userid) {
		// Box anzeigen
		$fehlermeldung = str_replace("%userid%", $f_userid, $t['sperren_fehlermeldung_fehler_beim_anlegen']);
		$fehlermeldung = str_replace("%eintrag%", $daten['id'], $fehlermeldung);
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		$f_userid = escape_string($f_userid);
		
		// Prüfen ob Blacklist-Eintrag bereits in Tabelle steht
		$query = "SELECT f_id from blacklist WHERE (f_userid=$daten[id] AND f_blacklistid=$f_userid) OR (f_userid=$f_userid AND f_blacklistid=$daten[id])";
			
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) > 0) {
			$fehlermeldung = str_replace("%username%", $daten['u_nick'], $t['sperren_fehlermeldung_eintrag_bereits_vorhanden']);
			$text .= hinweis($fehlermeldung, "fehler");
		} else if ($daten['id'] == $f_userid) {
			// Eigener Blacklist-Eintrag ist verboten
			$fehlermeldung = $t['sperren_fehlermeldung_selbst_hinzufuegen_nicht_moeglich'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else {
			// Benutzer ist noch kein Blacklist-Eintrag -> hinzufügen
			$f['f_userid'] = $f_userid;
			$f['f_blacklistid'] = $daten['id'];
			$f['f_text'] = htmlspecialchars($daten['f_text']);
			schreibe_db("blacklist", $f, 0, "f_id");
			
			$erfolgsmeldung = str_replace("%username%", $daten['u_nick'], $t['sperren_erfolgsmeldung_blacklisteintrag_erfolgreich']);
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
		}
	}
	
	return $text;
}
?>