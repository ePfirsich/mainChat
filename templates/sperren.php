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
		pdoQuery("DELETE FROM `ip_sperre` WHERE `is_domain` = '-GLOBAL-'", []);
		
		$erfolgsmeldung = $lang['sperren_erfolgsmeldung_loginsperre_deaktiviert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		$aktion = "";
	break;
	
	case "loginsperregast0":
		pdoQuery("DELETE FROM `ip_sperre` WHERE `is_domain` = '-GAST-'", []);
		
		$erfolgsmeldung = $lang['sperren_erfolgsmeldung_gastsperre_deaktiviert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		$aktion = "";
	break;
	
	case "loginsperre1":
		unset($f);
		$f['is_infotext'] = "";
		$f['is_domain'] = "-GLOBAL-";
		$f['is_owner'] = $u_id;
		
		pdoQuery("INSERT INTO `ip_sperre` (`is_infotext`, `is_domain`, `is_owner`) VALUES (:is_infotext, :is_domain, :is_owner)",
			[
				':is_infotext'=>$f['is_infotext'],
				':is_domain'=>$f['is_domain'],
				':is_owner'=>$f['is_owner']
			]);
		
		$erfolgsmeldung = $lang['sperren_erfolgsmeldung_loginsperre_aktiviert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		$aktion = "";
	break;
	
	case "loginsperregast1":
		unset($f);
		$f['is_infotext'] = "";
		$f['is_domain'] = "-GAST-";
		$f['is_owner'] = $u_id;
		
		pdoQuery("INSERT INTO `ip_sperre` (`is_infotext`, `is_domain`, `is_owner`) VALUES (:is_infotext, :is_domain, :is_owner)",
			[
				':is_infotext'=>$f['is_infotext'],
				':is_domain'=>$f['is_domain'],
				':is_owner'=>$f['is_owner']
			]);
		
		$erfolgsmeldung = $lang['sperren_erfolgsmeldung_gastsperre_aktiviert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		$aktion = "";
	break;
	
	default;
}

// Kopfzeile
// Menü ausgeben
$box = $lang['titel'];
$kopfzeile = "<a href=\"inhalt.php?bereich=sperren\">$lang[sperren_menue1]</a>\n" . "| <a href=\"inhalt.php?bereich=sperren&aktion=neu\">$lang[sperren_menue2]</a>\n";

$query = pdoQuery("SELECT `is_domain` FROM `ip_sperre` WHERE `is_domain` = '-GLOBAL-'", []);

$resultCount = $query->rowCount();
if ($resultCount > 0) {
	$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&aktion=loginsperre0\">$lang[sperren_menue5a]</a>\n";
} else {
	$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&aktion=loginsperre1\">$lang[sperren_menue5b]</a>\n";
}

$query = pdoQuery("SELECT `is_domain` FROM `ip_sperre` WHERE `is_domain` = '-GAST-'", []);

$resultCount = $query->rowCount();
if ($resultCount > 0) {
	$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&aktion=loginsperregast0\">$lang[sperren_menue7a]</a>\n";
} else {
	$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&aktion=loginsperregast1\">$lang[sperren_menue7b]</a>\n";
}

$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&aktion=blacklist\">$lang[sperren_menue3]</a>\n";
$kopfzeile .= "| <a href=\"inhalt.php?bereich=sperren&aktion=blacklist_neu\">" . $lang['sperren_menue6'] . "</a>\n";
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
		$fehlermeldung = $lang['sperren_fehlermeldung_domain_oder_ip'];
		$text .= hinweis($fehlermeldung, "fehler");
		$aktion = "neu";
	} else if (strlen($f['is_domain']) > 0) {
		if (strlen($f['is_domain']) > 4) {
			// Eintrag Domain in DB
			unset($f['is_ip']);
			$f['is_owner'] = $u_id;
			
			if (!isset($f['is_id']) || $f['is_id'] == '') {
				// Hinzufügen
				pdoQuery("INSERT INTO `ip_sperre` (`is_domain`, `is_owner`, `is_infotext`, `is_warn`) VALUES (:is_domain, :is_owner, :is_infotext, :is_warn)",
					[
						':is_domain'=>$f['is_domain'],
						':is_owner'=>$f['is_owner'],
						':is_infotext'=>$f['is_infotext'],
						':is_warn'=>$f['is_warn']
					]);
				
				$erfolgsmeldung = $lang['sperren_erfolgsmeldung_erfolgreich_eingetragen'];
				$text .= hinweis($erfolgsmeldung, "erfolgreich");
			} else {
				// Editieren
				pdoQuery("UPDATE `ip_sperre` SET `is_domain` = :is_domain, `is_owner` = :is_owner, `is_infotext` = :is_infotext, `is_warn` = :is_warn WHERE `is_id` = :is_id",
					[
						':is_id'=>$f['is_id'],
						':is_domain'=>$f['is_domain'],
						':is_owner'=>$f['is_owner'],
						':is_infotext'=>$f['is_infotext'],
						':is_warn'=>$f['is_warn']
					]);
				
				$erfolgsmeldung = $lang['sperren_erfolgsmeldung_erfolgreich_editiert'];
				$text .= hinweis($erfolgsmeldung, "erfolgreich");
			}
		} else {
			$fehlermeldung = $lang['sperren_fehlermeldung_domain_oder_ip'];
			$text .= hinweis($fehlermeldung, "fehler");
			$aktion = "neu";
		}
	} else if ($f['is_ip'] != "...") {
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
			if (!isset($f['is_id']) || $f['is_id'] == '') {
				// Hinzufügen
				pdoQuery("INSERT INTO `ip_sperre` (`is_ip`, `is_ip_byte`, `is_owner`, `is_infotext`, `is_warn`) VALUES (:is_ip, :is_ip_byte, :is_owner, :is_infotext, :is_warn)",
					[
						':is_ip'=>$f['is_ip'],
						':is_ip_byte'=>$f['is_ip_byte'],
						':is_owner'=>$f['is_owner'],
						':is_infotext'=>$f['is_infotext'],
						':is_warn'=>$f['is_warn']
					]);
				
				$erfolgsmeldung = $lang['sperren_erfolgsmeldung_erfolgreich_eingetragen'];
				$text .= hinweis($erfolgsmeldung, "erfolgreich");
			} else {
				// Editieren
				pdoQuery("UPDATE `ip_sperre` SET `is_ip` = :is_ip, `is_ip_byte` = :is_ip_byte, `is_owner` = :is_owner, `is_infotext` = :is_infotext, `is_warn` = :is_warn WHERE `is_id` = :is_id",
					[
						':is_id'=>$f['is_id'],
						':is_ip'=>$f['is_ip'],
						':is_ip_byte'=>$f['is_ip_byte'],
						':is_owner'=>$f['is_owner'],
						':is_infotext'=>$f['is_infotext'],
						':is_warn'=>$f['is_warn']
					]);
				
				$erfolgsmeldung = $lang['sperren_erfolgsmeldung_erfolgreich_editiert'];
				$text .= hinweis($erfolgsmeldung, "erfolgreich");
			}
		} else {
			$fehlermeldung = $lang['sperren_fehlermeldung_domain'];
			$text .= hinweis($fehlermeldung, "fehler");
			$aktion = "neu";
		}
	} else {
		$fehlermeldung = $lang['sperren_fehlermeldung_ip_ausfuellen'];
		$text .= hinweis($fehlermeldung, "fehler");
		$aktion = "neu";
	}
}

// Auswahl
switch ($aktion) {
	case "blacklist":
		// Blacklist anzeigen
		zeige_blacklist($text, $sort);
		break;
		
	case "blacklist_neu":
		// Formular für neuen Eintrag ausgeben
		formular_neuer_blacklist("", $daten);
		break;
		
	case "blacklist_neu2":
		// Neuer Eintrag, 2. Schritt: Benutzername Prüfen
		$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$daten['u_nick']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$daten['id'] = $result['u_id'];
			$text .= neuer_blacklist_eintrag($u_id, $daten);
			
			unset($daten);
			$daten[] = "";
			
			zeige_blacklist($text, $sort);
		} else if ($daten['u_nick'] == "") {
			$fehlermeldung = $lang['sperren_fehlermeldung_kein_benutzername_angegeben'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neuer_blacklist($text, $daten);
		} else {
			$fehlermeldung = str_replace("%u_nick%", $daten['u_nick'], $lang['sperren_fehlermeldung_benutzer_nicht_vorhanden']);
			$text .= hinweis($fehlermeldung, "fehler");
			
			formular_neuer_blacklist($text, $daten);
		}
		break;
		
	case "blacklist_loesche":
		// Eintrag löschen
		if (isset($bearbeite_ids) && is_array($bearbeite_ids)) {
			foreach ($bearbeite_ids as $bearbeite_id) {
				$text .= loesche_blacklist($bearbeite_id);
			}
		}
		
		zeige_blacklist($text, $sort);
		break;
	
	case "loeschen":
	// ID gesetzt?
		if (strlen($is_id) > 0) {
			$query = pdoQuery("SELECT `is_infotext`, `is_domain`, `is_ip_byte`, SUBSTRING_INDEX(`is_ip`,'.',`is_ip_byte`) AS `isip` FROM `ip_sperre` WHERE `is_id` = :is_id", [':is_id'=>intval($is_id)]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				// Zeile lesen, IP zerlegen
				$row = $query->fetch();
				
				pdoQuery("DELETE FROM `ip_sperre` WHERE `is_id` = :is_id", [':is_id'=>$is_id]);
				
				if (strlen($row['is_domain']) > 0) {
					$erfolgsmeldung = str_replace("%domain%", $row['is_domain'], $lang['sperren_erfolgsmeldung_adresse']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				} else {
					if ($row['is_ip_byte'] == 1) {
						$isip = $row['isip'] . ".xxx.yyy.zzz";
					} elseif ($row['is_ip_byte'] == 2) {
						$isip = $row['isip'] . ".yyy.zzz";
					} elseif ($row['is_ip_byte'] == 3) {
						$isip = $row['isip'] . ".zzz";
					} else {
						$isip = $row['isip'];
					}
					$erfolgsmeldung = str_replace("%domain%", $isip, $lang['sperren_erfolgsmeldung_adresse']);
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				}
			} else {
				$fehlermeldung = $lang['sperren_fehlermeldung_eintrag_nicht_gefunden'];
				$text .= hinweis($fehlermeldung, "fehler");
			}
		}
		sperren_liste($text);
		
		break;
	
	case "aendern":
		// ID gesetzt?
		if (strlen($is_id) > 0) {
			$query = pdoQuery("SELECT `is_infotext`, `is_domain`, `is_ip`, `is_ip_byte`, `is_warn` FROM `ip_sperre` WHERE `is_id` = :is_id", [':is_id'=>$is_id]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				// Zeile lesen, IP zerlegen
				$row = $query->fetch();
				$ip = explode(".", $row['is_ip']);
				
				// Kopf Tabelle
				$text .= "<form action=\"inhalt.php?bereich=sperren\" method=\"post\">\n";
				$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
				$text .= "<input type=\"hidden\" name=\"is_id\" value=\"$is_id\">\n";
				
				$box = $lang['sperren_aendern'];
				$zaehler = 0;
				
				$text .= "<table style=\"width:100%\">\n";
				
				
				// Grund der Sperre
				$text .= zeige_formularfelder("input", $zaehler, $lang['sperren_grund_der_sperre'], "is_infotext", $row['is_infotext']);
				$zaehler++;
				
				
				// Domain/IP-Adresse
				if (strlen($row['is_domain']) > 0) {
					// Domain
					$text .= zeige_formularfelder("input", $zaehler, $lang['sperren_domain'], "is_domain", $row['is_domain']);
					$zaehler++;
				} else {
					// IP-Adresse
					if ($zaehler % 2 != 0) {
						$bgcolor = 'class="tabelle_zeile2"';
					} else {
						$bgcolor = 'class="tabelle_zeile1"';
					}
					
					$text .= "<tr>\n";
					$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['sperren_ip_adresse'] . "</td>\n";
					$text .= "<td $bgcolor>";
					$text .= "<input type=\"text\" name=\"ip1\" value=\"$ip[0]\" size=\"3\" maxlength=\"3\"><b>.</b>\n";
					$text .= "<input type=\"text\" name=\"ip2\" value=\"$ip[1]\" size=\"3\" maxlength=\"3\"><b>.</b>\n";
					$text .= "<input type=\"text\" name=\"ip3\" value=\"$ip[2]\" size=\"3\" maxlength=\"3\"><b>.</b>\n";
					$text .= "<input type=\"text\" name=\"ip4\" value=\"$ip[3]\" size=\"3\" maxlength=\"3\">\n";
					$text .= "</td>\n";
					$text .= "</tr>\n";
					$zaehler++;
				}
				
				
				// Art
				if ($zaehler % 2 != 0) {
					$bgcolor = 'class="tabelle_zeile2"';
				} else {
					$bgcolor = 'class="tabelle_zeile1"';
				}
				
				$text .= "<tr>\n";
				$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['sperren_art_warnung'] . "</td>\n";
				$text .= "<td $bgcolor>";
				$text .= "<select name=\"is_warn\">";
				if ($row['is_warn'] == "ja") {
					$text .= "<option selected value=\"ja\">$lang[sperren_warnung]";
					$text .= "<option value=\"nein\">$lang[sperren_sperre]";
				} else {
					$text .= "<option value=\"ja\">$lang[sperren_warnung]";
					$text .= "<option selected value=\"nein\">$lang[sperren_sperre]";
				}
				$text .= "</select>\n";
				$text .= "</td>\n";
				$text .= "</tr>\n";
				$zaehler++;
				
				
				// Eintragen
				if ($zaehler % 2 != 0) {
					$bgcolor = 'class="tabelle_zeile2"';
				} else {
					$bgcolor = 'class="tabelle_zeile1"';
				}
				$text .= "<tr>";
				$text .= "<td $bgcolor>&nbsp;</td>\n";
				$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[sperren_eintragen]\"></td>\n";
				$text .= "</tr>\n";
				
				
				$text .= "</table>\n";
				$text .= "</form>\n";
			}
		} else {
			$text .= "<p>$lang[sonst9]</p>\n";
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
			$f['is_infotext'] = $lang['sperren_benutzername'] . " " . $uname;
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
		$box = $lang['sperren_neue_zugangssperre'];
		$zaehler = 0;
		
		$text .= "<form action=\"inhalt.php?bereich=sperren\" method=\"post\">\n";
		
		$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
		$text .= $lang['sperren_nur_eine_zeile_ausfuellen'] . "<br>\n";
		$text .= "<table style=\"width:100%\">\n";
		
		
		// Grund der Sperre
		$text .= zeige_formularfelder("input", $zaehler, $lang['sperren_grund_der_sperre'], "is_infotext", $f['is_infotext']);
		$zaehler++;
		
		
		// Domain
		$text .= zeige_formularfelder("input", $zaehler, $lang['sperren_domain'], "is_domain", $f['is_domain']);
		$zaehler++;
		
		
		// IP-Adresse
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['sperren_ip_adresse'] . "</td>\n";
		$text .= "<td $bgcolor>";
		$text .= "<input type=\"text\" name=\"ip1\" value=\"$ip1\" size=\"3\" maxlength=\"3\"><b>.</b>\n";
		$text .= "<input type=\"text\" name=\"ip2\" value=\"$ip2\" size=\"3\" maxlength=\"3\"><b>.</b>\n";
		$text .= "<input type=\"text\" name=\"ip3\" value=\"$ip3\" size=\"3\" maxlength=\"3\"><b>.</b>\n";
		$text .= "<input type=\"text\" name=\"ip4\" value=\"$ip4\" size=\"3\" maxlength=\"3\">\n";
		$text .= "</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		
		// Art
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['sperren_art_warnung'] . "</td>\n";
		$text .= "<td $bgcolor>";
		$text .= "<select name=\"is_warn\">";
		if (isset($f['is_warn']) && $f['is_warn'] == "ja") {
			$text .= "<option selected value=\"ja\">$lang[sperren_warnung]";
			$text .= "<option value=\"nein\">$lang[sperren_sperre]";
		} else {
			$text .= "<option value=\"ja\">$lang[sperren_warnung]";
			$text .= "<option selected value=\"nein\">$lang[sperren_sperre]";
		}
		$text .= "</select>\n";
		$text .= "</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		
		// Eintragen
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>";
		$text .= "<td $bgcolor>&nbsp;</td>\n";
		$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[sperren_eintragen]\"></td>\n";
		$text .= "</tr>\n";
		
		
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