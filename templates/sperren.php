<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$hname = filter_input(INPUT_GET, 'hname', FILTER_SANITIZE_STRING);
$ipaddr = filter_input(INPUT_GET, 'ipaddr', FILTER_SANITIZE_STRING);
$uname = filter_input(INPUT_GET, 'uname', FILTER_SANITIZE_STRING);

$text = "";
switch ($aktion) {
	case "loginsperre0":
		$query2 = "DELETE FROM ip_sperre WHERE is_domain = '-GLOBAL-' ";
		$result2 = sqlUpdate($query2);
		
		$erfolgsmeldung = $t['sperren_erfolgsmeldung_loginsperre_deaktiviert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		unset($aktion);
		break;
	
	case "loginsperregast0":
		$query2 = "DELETE FROM ip_sperre WHERE is_domain = '-GAST-' ";
		$result2 = sqlUpdate($query2);
		
		$erfolgsmeldung = $t['sperren_erfolgsmeldung_gastsperre_deaktiviert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		unset($aktion);
		break;
	
	case "loginsperre1":
		unset($f);
		$f['is_infotext'] = "";
		$f['is_domain'] = "-GLOBAL-";
		$f['is_owner'] = $u_id;
		schreibe_db("ip_sperre", $f, 0, "is_id");
		
		$erfolgsmeldung = $t['sperren_erfolgsmeldung_loginsperre_aktiviert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		unset($aktion);
		break;
	
	case "loginsperregast1":
		unset($f);
		$f['is_infotext'] = "";
		$f['is_domain'] = "-GAST-";
		$f['is_owner'] = $u_id;
		schreibe_db("ip_sperre", $f, 0, "is_id");
		
		$erfolgsmeldung = $t['sperren_erfolgsmeldung_gastsperre_aktiviert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		unset($aktion);
		break;
	
	default;
}

// Kopfzeile
// Menü ausgeben
$box = $t['titel'];
$kopfzeile = "<a href=\"inhalt.php?bereich=sperren&id=$id\">$t[sperren_menue1]</a>\n" . "| <a href=\"inhalt.php?bereich=sperren&id=$id&aktion=neu\">$t[sperren_menue2]</a>\n";

$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GLOBAL-'";
$result = sqlQuery($query);
if ($result && mysqli_num_rows($result) > 0) {
	$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&id=$id&aktion=loginsperre0\">$t[sperren_menue5a]</a>\n";
} else {
	$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&id=$id&aktion=loginsperre1\">$t[sperren_menue5b]</a>\n";
}
mysqli_free_result($result);

$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GAST-'";
$result = sqlQuery($query);
if ($result && mysqli_num_rows($result) > 0) {
	$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&id=$id&aktion=loginsperregast0\">$t[sperren_menue7a]</a>\n";
} else {
	$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&id=$id&aktion=loginsperregast1\">$t[sperren_menue7b]</a>\n";
}
mysqli_free_result($result);

$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&aktion=blacklist&id=$id\">$t[sperren_menue3]</a>\n";
$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&aktion=blacklist_neu&id=$id\">" . $t['sperren_menue6'] . "</a>\n";
zeige_tabelle_zentriert($box, $kopfzeile);


// Soll Datensatz eingetragen oder geändert werden? (Nur für Sperren relevant)
if ( $formular == 1 ) {
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
		$fehlermeldung = $t['sperren_fehlermeldung_domain_oder_ip'];
		$text .= hinweis($fehlermeldung, "fehler");
		$aktion = "neu";
	} elseif (strlen($f['is_domain']) > 0) {
		if (strlen($f['is_domain']) > 4) {
			// Eintrag Domain in DB
			unset($f['is_ip']);
			$f['is_owner'] = $u_id;
			schreibe_db("ip_sperre", $f, $f['is_id'], "is_id");
		} else {
			$fehlermeldung = $t['sperren_fehlermeldung_domain_oder_ip'];
			$text .= hinweis($fehlermeldung, "fehler");
			$aktion = "neu";
		}
	} elseif ($f['is_ip'] != "...") {
		if (strlen($ip1) > 0 && $ip1 < 256) {
			// Eintrag IP in DB
			if (strlen($ip4) > 0 && $ip4 < 256 && strlen($ip3) > 0 && $ip3 < 256 && strlen($ip2) > 0 && $ip2 < 256) {
				$f['is_ip_byte'] = 4;
			} elseif (strlen($ip3) > 0 && $ip3 < 256 && strlen($ip2) > 0 && $ip2 < 256) {
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
			
			$erfolgsmeldung = $t['sperren_erfolgsmeldung_erfolgreich_editiert'];
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
		} else {
			$fehlermeldung = $t['sperren_fehlermeldung_domain'];
			$text .= hinweis($fehlermeldung, "fehler");
			$aktion = "neu";
		}
	} else {
		$fehlermeldung = $t['sperren_fehlermeldung_ip_ausfuellen'];
		$text .= hinweis($fehlermeldung, "fehler");
		$aktion = "neu";
	}
}

// Auswahl
switch ($aktion) {
	case "blacklist":
		// Blacklist anzeigen
		zeige_blacklist("", "normal", "", $sort);
		break;
		
	case "blacklist_neu":
		// Formular für neuen Eintrag ausgeben
		formular_neuer_blacklist("", $formulardaten);
		break;
		
	case "blacklist_neu2":
		// Neuer Eintrag, 2. Schritt: Benutzername Prüfen
		$formulardaten['u_nick'] = escape_string($formulardaten['u_nick']);
		$query = "SELECT `u_id` FROM `user` WHERE `u_nick` = '$formulardaten[u_nick]'";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$formulardaten['id'] = mysqli_result($result, 0, 0);
			$text .= neuer_blacklist_eintrag($u_id, $formulardaten);
			unset($formulardaten);
			$formulardaten[] = "";
			
			zeige_blacklist($text, "normal", "", $sort);
		} else if ($formulardaten['u_nick'] == "") {
			$fehlermeldung = $t['sperren_fehlermeldung_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neuer_blacklist($text, $formulardaten);
		} else {
			$fehlermeldung = str_replace("%u_nick%", $formulardaten['u_nick'], $t['sperren_fehlermeldung_benutzer_nicht_vorhanden']);
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neuer_blacklist($text, $formulardaten);
		}
		mysqli_free_result($result);
		break;
		
	case "blacklist_loesche":
		// Eintrag löschen
		if (isset($bearbeite_ids) && is_array($bearbeite_ids)) {
			foreach ($bearbeite_ids as $bearbeite_id) {
				$text .= loesche_blacklist($bearbeite_id);
			}
		}
		
		zeige_blacklist($text, "normal", "", $sort);
		break;
	
	case "loeschen":
	// ID gesetzt?
		if (strlen($is_id) > 0) {
			$query = "SELECT is_infotext,is_domain,is_ip_byte,SUBSTRING_INDEX(is_ip,'.',is_ip_byte) as isip FROM ip_sperre WHERE is_id=" . intval($is_id);
			
			$result = sqlQuery($query);
			$rows = mysqli_num_rows($result);
			
			if ($rows == 1) {
				// Zeile lesen, IP zerlegen
				$row = mysqli_fetch_object($result);
				
				$query2 = "DELETE FROM ip_sperre WHERE is_id=" . intval($is_id);
				$result2 = sqlUpdate($query2);
				
				if (strlen($row->is_domain) > 0) {
					$erfolgsmeldung = str_replace("%domain%", $row->is_domain, $t['sperren_erfolgsmeldung_adresse']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
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
					$erfolgsmeldung = str_replace("%domain%", $isip, $t['sperren_erfolgsmeldung_adresse']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				}
			} else {
				$fehlermeldung = $t['sperren_fehlermeldung_eintrag_nicht_gefunden'];
				$text .= hinweis($fehlermeldung, "fehler");
			}
			mysqli_free_result($result);
		}
		sperren_liste($text);
		
		break;
	
	case "aendern":
		// ID gesetzt?
		if (strlen($is_id) > 0) {
			$query = "SELECT is_infotext,is_domain,is_ip,is_ip_byte,is_warn FROM ip_sperre WHERE is_id=" . $is_id;
			$result = sqlQuery($query);
			$rows = mysqli_num_rows($result);
			
			if ($rows == 1) {
				// Zeile lesen, IP zerlegen
				$row = mysqli_fetch_object($result);
				$ip = explode(".", $row->is_ip);
				
				// Kopf Tabelle
				$text .= "<form action=\"inhalt.php?bereich=sperren\" method=\"post\">\n";
				$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
				
				$box = $t['sonst18'];
				
				$text .= "<table style=\"width:100%\">\n";
				
				// Infotext
				$text .= "<tr><td class=\"tabelle_zeile1\"><b>$t[sonst19]</b></td>";
				$text .= "<td class=\"tabelle_zeile1\">"
					. "<input type=\"text\" name=\"is_infotext\" value=\"$row->is_infotext\" size=\"24\">"
					. "</td></tr>\n";
				
				// Domain/IP-Adresse
				if (strlen($row->is_domain) > 0) {
					$text .= "<tr><td class=\"tabelle_zeile2\"><b>$t[sonst11]</b></td>";
					$text .= "<td class=\"tabelle_zeile2\"><input type=\"text\" name=\"is_domain\" value=\"$row->is_domain\" size=\"24\">\n";
				} else {
					$text .= "<tr><td class=\"tabelle_zeile2\"><b>$t[sonst12]</b></td>";
					$text .= "<td class=\"tabelle_zeile2\">"
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
				$text .= "<tr><td class=\"tabelle_zeile1\"><b>$t[sonst22]</b></td><td class=\"tabelle_zeile1\">"
					. "<select name=\"is_warn\">";
				if ($row->is_warn == "ja") {
					$text .= "<option selected value=\"ja\">$t[sperren_warnung]";
					$text .= "<option value=\"nein\">$t[sperren_sperre]";
				} else {
					$text .= "<option value=\"ja\">$t[sperren_warnung]";
					$text .= "<option selected value=\"nein\">$t[sperren_sperre]";
				}
				$text .= "</select>";
				
				// Submitknopf
				$text .= "&nbsp;<b><input type=\"submit\" value=\"$t[sperren_eintragen]\"></b></td></tr>\n";
				$text .= "</table>\n";
				
				$text .= "<input type=\"hidden\" name=\"is_id\" value=\"$is_id\">\n"
					. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
					$text .= "</form>\n";
			}
			mysqli_free_result($result);
		} else {
			$text .= "<p>$t[sonst9]</p>\n";
		}
		
		zeige_tabelle_zentriert($box, $text);
		
		break;
	
	case "neu":
	// Werte von [SPERREN] aus Benutzerliste übernehmen, falls vorhanden...
		if (isset($hname)) {
			$f['is_domain'] = $hname;
		} else if (isset($ipaddr)) {
			list($ip1, $ip2, $ip3, $ip4) = preg_split("/\./", $ipaddr, 4);
		}
		if (isset($uname)) {
			$f['is_infotext'] = $t['sperren_benutzername'] . " " . $uname;
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
		$box = $t['sperren_neue_zugangssperre'];
		
		$text .= "<form action=\"inhalt.php?bereich=sperren\" method=\"post\">\n";
		
		$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
		$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
		$text .= $t['sonst15'] . "<br>\n";
		$text .= "<table style=\"width:100%\">\n";
		
		$text .= "<tr><td class=\"tabelle_zeile1\"><b>$t[sonst19]</b></td>";
		$text .= "<td class=\"tabelle_zeile1\"><input type=\"text\" name=\"is_infotext\" value=\"$f[is_infotext]\" size=\"24\"></td></tr>\n";
		
		$text .= "<tr><td class=\"tabelle_zeile2\"><b>$t[sonst11]</b></td>";
		$text .= "<td class=\"tabelle_zeile2\"><input type=\"text\" name=\"is_domain\" value=\"$f[is_domain]\" size=\"24\"></td></tr>\n";
		
		$text .= "<tr><td class=\"tabelle_zeile1\"><b>$t[sonst12]</b></td>";
		$text .= "<td class=\"tabelle_zeile1\"><input type=\"text\" name=\"ip1\" value=\"$ip1\" size=\"3\" maxlength=\"3\"><b>.</b>"
			. "<input type=\"text\" name=\"ip2\" value=\"$ip2\" size=\"3\" maxlength=\"3\"><b>.</b>"
			. "<input type=\"text\" name=\"ip3\" value=\"$ip3\" size=\"3\" maxlength=\"3\"><b>.</b>"
			. "<input type=\"text\" name=\"ip4\" value=\"$ip4\" size=\"3\" maxlength=\"3\"></td></tr>\n";
		
		$text .= "<tr><td class=\"tabelle_zeile2\"><b>$t[sonst22]</b></td><td class=\"tabelle_zeile2\"><select name=\"is_warn\">";
		if (isset($f['is_warn']) && $f['is_warn'] == "ja") {
			$text .= "<option selected value=\"ja\">$t[sperren_warnung]";
			$text .= "<option value=\"nein\">$t[sperren_sperre]";
		} else {
			$text .= "<option value=\"ja\">$t[sperren_warnung]";
			$text .= "<option selected value=\"nein\">$t[sperren_sperre]";
		}
		$text .= "</select>&nbsp;&nbsp;&nbsp;"
			. "<b><input type=\"submit\" value=\"$t[sperren_eintragen]\"></b>\n"
			. "</td></tr>\n";
		$text .= "</table>\n";
		
		$text .= "</form>\n";
		
		zeige_tabelle_zentriert($box, $text);
		
		break;
	
	default;
	// Übersicht
		// Liste ausgeben
	sperren_liste($text);
}
?>