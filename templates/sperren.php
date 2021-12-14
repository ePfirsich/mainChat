<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}

switch ($aktion) {
	case "loginsperre0":
		$query2 = "DELETE FROM ip_sperre WHERE is_domain = '-GLOBAL-' ";
		$result2 = mysqli_query($mysqli_link, $query2);
		
		unset($aktion);
		break;
	
	case "loginsperregast0":
		$query2 = "DELETE FROM ip_sperre WHERE is_domain = '-GAST-' ";
		$result2 = mysqli_query($mysqli_link, $query2);
		
		unset($aktion);
		break;
	
	case "loginsperre1":
		unset($f);
		$f['is_infotext'] = "";
		$f['is_domain'] = "-GLOBAL-";
		$f['is_owner'] = $u_id;
		schreibe_db("ip_sperre", $f, 0, "is_id");
		
		unset($aktion);
		break;
	
	case "loginsperregast1":
		unset($f);
		$f['is_infotext'] = "";
		$f['is_domain'] = "-GAST-";
		$f['is_owner'] = $u_id;
		schreibe_db("ip_sperre", $f, 0, "is_id");
		
		unset($aktion);
		break;
	
	default;
}

// Soll Datensatz eingetragen oder geändert werden? (Nur für Sperren relevant)
if ((isset($eintragen)) && ($eintragen == $t['sonst13'])) {
	if (!isset($f['is_infotext'])) {
		$f['is_infotext'] = "";
	}
	if (!isset($f['is_domain'])) {
		$f['is_domain'] = "";
	}
	
	$f['is_infotext'] = htmlspecialchars($f['is_infotext']);
	$f['is_domain'] = htmlspecialchars($f['is_domain']);
	
	// IP zusamensetzen
	$f['is_ip'] = $ip1 . "." . $ip2 . "." . $ip3 . "." . $ip4;
	if (strlen($f['is_domain']) > 0 && $f['is_ip'] != "...") {
		echo "<p>$t[sonst5]</p>\n";
		$aktion = "neu";
	} elseif (strlen($f['is_domain']) > 0) {
		if (strlen($f['is_domain']) > 4) {
			// Eintrag Domain in DB
			unset($f['is_ip']);
			$f['is_owner'] = $u_id;
			schreibe_db("ip_sperre", $f, $f['is_id'], "is_id");
		} else {
			echo "<p>$t[sonst5]</p>\n";
			$aktion = "neu";
		}
	} elseif ($f['is_ip'] != "...") {
		if (strlen($ip1) > 0 && $ip1 < 256) {
			// Eintrag IP in DB
			if (strlen($ip4) > 0 && $ip4 < 256 && strlen($ip3) > 0
				&& $ip3 < 256 && strlen($ip2) > 0 && $ip2 < 256) {
				$f['is_ip_byte'] = 4;
			} elseif (strlen($ip3) > 0 && $ip3 < 256 && strlen($ip2) > 0
				&& $ip2 < 256) {
				$f['is_ip_byte'] = 3;
				$f['is_ip'] = $ip1 . "." . $ip2 . "." . $ip3 . ".";
			} elseif (strlen($ip2) > 0 && $ip2 < 256) {
				$f['is_ip_byte'] = 2;
				$f['is_ip'] = $ip1 . "." . $ip2 . "..";
			} else {
				$f['is_ip_byte'] = 1;
				$f['is_ip'] = $ip1 . "...";
			}
			$f['is_owner'] = $u_id;
			if (!isset($f['is_id'])) {
				$f['is_id'] = 0;
			}
			schreibe_db("ip_sperre", $f, $f['is_id'], "is_id");
		} else {
			$value = $t['sperren_fehlermeldung_domain'];
			
			// Box anzeigen
			zeige_tabelle_zentriert($t['sperren_fehlermeldung'], $value);
			$aktion = "neu";
		}
	} else {
		$value = $t['sperren_fehlermeldung_ip_ausfuellen'];
		
		// Box anzeigen
		zeige_tabelle_zentriert($t['sperren_fehlermeldung'], $value);
		$aktion = "neu";
	}
}

// Auswahl
switch ($aktion) {
	case "blacklist":
		// Blacklist anzeigen
		zeige_blacklist("normal", "", $sort);
		break;
		
	case "blacklist_neu":
		// Formular für neuen Eintrag ausgeben
		formular_neuer_blacklist($neuer_blacklist);
		break;
		
	case "blacklist_neu2":
		// Neuer Eintrag, 2. Schritt: Benutzername Prüfen
		$neuer_blacklist['u_nick'] = mysqli_real_escape_string($mysqli_link, $neuer_blacklist['u_nick']); // sec
		$query = "SELECT `u_id` FROM `user` WHERE `u_nick` = '$neuer_blacklist[u_nick]'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$neuer_blacklist['u_id'] = mysqli_result($result, 0, 0);
			neuer_blacklist($u_id, $neuer_blacklist);
			unset($neuer_blacklist);
			$neuer_blacklist[] = "";
			formular_neuer_blacklist($neuer_blacklist);
			zeige_blacklist("normal", "", $sort);
		} elseif ($neuer_blacklist['u_nick'] == "") {
			echo "<b>Fehler:</b> Bitte geben Sie einen Benutzernamen an!<br>\n";
			formular_neuer_blacklist($neuer_blacklist);
		} else {
			echo "<b>Fehler:</b> Der Benutzername '$neuer_blacklist[u_nick]' existiert nicht!<br>\n";
			formular_neuer_blacklist($neuer_blacklist);
		}
		mysqli_free_result($result);
		break;
		
	case "blacklist_loesche":
		// Eintrag löschen
		
		if (isset($f_blacklistid) && is_array($f_blacklistid)) {
			// Mehrere Einträge löschen
			foreach ($f_blacklistid as $key => $loesche_id) {
				loesche_blacklist($loesche_id);
			}
			
		} else {
			// Einen Eintrag löschen
			if (isset($f_blacklistid))
				loesche_blacklist($f_blacklistid);
		}
		
		formular_neuer_blacklist($neuer_blacklist);
		zeige_blacklist("normal", "", $sort);
		break;
	
	case "loeschen":
	// ID gesetzt?
		if (strlen($is_id) > 0) {
			$query = "SELECT is_infotext,is_domain,is_ip_byte,SUBSTRING_INDEX(is_ip,'.',is_ip_byte) as isip FROM ip_sperre "
				. "WHERE is_id=" . intval($is_id);
			
			$result = mysqli_query($mysqli_link, $query);
			$rows = mysqli_num_rows($result);
			
			if ($rows == 1) {
				// Zeile lesen, IP zerlegen
				$row = mysqli_fetch_object($result);
				
				$query2 = "DELETE FROM ip_sperre WHERE is_id=" . intval($is_id);
				
				$result2 = mysqli_query($mysqli_link, $query2);
				
				if (strlen($row->is_domain) > 0) {
					$value = str_replace("%domain%", $row->is_domain, $t['sperren_erfolgsmeldung_adresse']);
				} else {
					if ($row->is_ip_byte == 1) {
						$isip = $row->isip . ".xxx.yyy.zzz";
					} elseif ($row->is_ip_byte == 2) {
						$isip = $row->isip . ".yyy.zzz";
					} elseif ($row->is_ip_byte == 3) {
						$isip = $row->isip . ".zzz";
					} else {
						$isip = $row->isip;
					}
					$value = str_replace("%domain%", $isip, $t['sperren_erfolgsmeldung_adresse']);
				}
				// Box anzeigen
				zeige_tabelle_zentriert($t['sperren_erfolgsmeldung'], $value);
			} else {
				$value = $t['sperren_fehlermeldung_eintrag_nicht_gefunden'];
				
				// Box anzeigen
				zeige_tabelle_zentriert($t['sperren_fehlermeldung'], $value);
			}
			mysqli_free_result($result);
		}
		sperren_liste();
		
		break;
	
	case "aendern":
		$text = '';
		// ID gesetzt?
		if (strlen($is_id) > 0) {
			$query = "SELECT is_infotext,is_domain,is_ip,is_ip_byte,is_warn FROM ip_sperre "
				. "WHERE is_id=" . intval($is_id);
			
			$result = mysqli_query($mysqli_link, $query);
			$rows = mysqli_num_rows($result);
			
			if ($rows == 1) {
				// Zeile lesen, IP zerlegen
				$row = mysqli_fetch_object($result);
				$ip = explode(".", $row->is_ip);
				
				// Kopf Tabelle
				$text .= "<form name=\"Sperre\" action=\"inhalt.php?seite=sperren\" method=\"post\">\n";
				
				$box = $t['sonst18'];
				
				$text .= "<table>\n";
				$text .= "<tr><td><b>$t[sonst19]</b></td>";
				
				// Infotext
				$text .= "<td class=\"smaller\">"
					. "<input type=\"text\" name=\"f[is_infotext]\" value=\"$row->is_infotext\" size=\"24\">"
					. "</td></tr>\n";
				
				// Domain/IP-Adresse
				if (strlen($row->is_domain) > 0) {
					$text .= "<tr><td><b>$t[sonst11]</b></td>";
					$text .= "<td class=\"smaller\"><input type=\"text\" name=\"f[is_domain]\" " . "value=\"$row->is_domain\" size=\"24\">\n";
				} else {
					$text .= "<tr><td><b>$t[sonst12]</b></td>";
					$text .= "<td class=\"smaller\">"
						. "<input type=\"text\" name=\"ip1\" "
						. "value=\"$ip[0]\" size=\"3\" maxlength=\"3\"><b>.</b>"
						. "<input type=\"text\" name=\"ip2\" "
						. "value=\"$ip[1]\" size=\"3\" maxlength=\"3\"><b>.</b>"
						. "<input type=\"text\" name=\"ip3\" "
						. "value=\"$ip[2]\" size=\"3\" maxlength=\"3\"><b>.</b>"
						. "<input type=\"text\" name=\"ip4\" "
						. "value=\"$ip[3]\" size=\"3\" maxlength=\"3\"></td></tr>\n";
				}
				
				// Warnung ja/nein
				$text .= "<tr><td><b>$t[sonst22]</b></td><td class=\"smaller\">"
					. "<select name=\"f[is_warn]\">";
				if ($row->is_warn == "ja") {
					$text .= "<option selected value=\"ja\">$t[sperren_warnung]";
					$text .= "<option value=\"nein\">$t[sperren_sperre]";
				} else {
					$text .= "<option value=\"ja\">$t[sperren_warnung]";
					$text .= "<option selected value=\"nein\">$t[sperren_sperre]";
				}
				$text .= "</select>";
				
				// Submitknopf
				$text .= "&nbsp;<b><input type=\"submit\" value=\"$t[sonst13]\" name=\"eintragen\"></b></td></tr>\n";
				$text .= "</table>\n";
				
				$text .= "<input type=\"hidden\" name=\"f[is_id]\" value=\"$is_id\">\n"
					. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
					$text .= "</form>\n";
			}
			mysqli_free_result($result);
		} else {
			$text .= "<p>$t[sonst9]</p>\n";
		}
		
		zeige_tabelle_zentriert($box, $text);
		
		// Liste ausgeben
		sperren_liste();
		
		break;
	
	case "neu":
	// Werte von [SPERREN] aus Benutzerliste übernehmen, falls vorhanden...
		$text = '';
		if (isset($hname)) {
			$f['is_domain'] = $hname;
		} else if (isset($ipaddr)) {
			list($ip1, $ip2, $ip3, $ip4) = preg_split("/\./", $ipaddr, 4);
		}
		if (isset($uname)) {
			$f['is_infotext'] = "Benutzername " . $uname;
		}
		
		if (!isset($f['is_domain'])) {
			$f['is_domain'] = "";
		}
		if (!isset($ip1)) {
			$ip1 = "";
		}
		if (!isset($ip2)) {
			$ip2 = "";
		}
		if (!isset($ip3)) {
			$ip3 = "";
		}
		if (!isset($ip4)) {
			$ip4 = "";
		}
		
		// Sperre neu anlegen, Eingabeformular
		$box = $t['sonst14'];
		
		$text .= "<form name=\"Sperre\" action=\"inhalt.php?seite=sperren\" method=\"post\">\n";
		
		$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
		$text .= $t['sonst15'] . "<br>\n";
		$text .= "<table>\n";
		
		$text .= "<tr><td><b>$t[sonst19]</b></td>";
		$text .= "<td class=\"smaller\"><input type=\"text\" name=\"f[is_infotext]\" value=\"$f[is_infotext]\" size=\"24\"></td></tr>\n";
		$text .= "<tr><td><b>$t[sonst11]</b></td>";
		$text .= "<td class=\"smaller\"><input type=\"text\" name=\"f[is_domain]\" value=\"$f[is_domain]\" size=\"24\"></td></tr>\n";
		$text .= "<tr><td><b>$t[sonst12]</b></td>";
		$text .= "<td class=\"smaller\"><input type=\"text\" name=\"ip1\" value=\"$ip1\" size=\"3\" maxlength=\"3\"><b>.</b>"
			. "<input type=\"text\" name=\"ip2\" value=\"$ip2\" size=\"3\" maxlength=\"3\"><b>.</b>"
			. "<input type=\"text\" name=\"ip3\" value=\"$ip3\" size=\"3\" maxlength=\"3\"><b>.</b>"
			. "<input type=\"text\" name=\"ip4\" value=\"$ip4\" size=\"3\" maxlength=\"3\"></td></tr>\n";
		
			$text .= "<tr><td><b>$t[sonst22]</b></td><td class=\"smaller\"><select name=\"f[is_warn]\">";
		if (isset($f['is_warn']) && $f['is_warn'] == "ja") {
			$text .= "<option selected value=\"ja\">$t[sperren_warnung]";
			$text .= "<option value=\"nein\">$t[sperren_sperre]";
		} else {
			$text .= "<option value=\"ja\">$t[sperren_warnung]";
			$text .= "<option selected value=\"nein\">$t[sperren_sperre]";
		}
		$text .= "</select>&nbsp;&nbsp;&nbsp;"
			. "<b><input type=\"submit\" value=\"$t[sonst13]\" name=\"eintragen\"></b>\n"
			. "</td></tr>\n";
		$text .= "</table>\n";
		
		$text .= "</form>\n";
		
		zeige_tabelle_zentriert($box, $text);
		
		// Liste ausgeben
		sperren_liste();
		
		break;
	
	default;
	// Übersicht
		// Liste ausgeben
		sperren_liste();
}
?>