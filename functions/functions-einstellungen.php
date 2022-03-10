<?php
function user_edit($text, $f, $admin, $u_level) {
	// $f = Ass. Array mit Benutzerdaten
	
	global $level, $user_farbe, $lang;
	global $u_id, $punktefeatures, $eintritt_individuell;
	
	// Ausgabe des Benutzers
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">";
	
	if ($u_level != "G") {
		// Überschrift: Avatar
		$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_avatar'], "", "", 0, "70", "");
		
		// Avatar lesen und in Array speichern
		$query = pdoQuery("SELECT `b_name`, `b_height`, `b_width`, `b_mime` FROM `bild` WHERE `b_name` = 'avatar' AND `b_user` = :b_user", [':b_user'=>$f['u_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			unset($bilder);
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$bilder[$row['b_name']]['b_mime'] = $row['b_mime'];
				$bilder[$row['b_name']]['b_width'] = $row['b_width'];
				$bilder[$row['b_name']]['b_height'] = $row['b_height'];
			}
		}
		// Wenn kein Avatar gefunden wurde
		if (!isset($bilder)) {
			$bilder = "";
		}
		
		$maxWidth = "200";
		$maxHeight = "200";
		
		// Avatar
		$value = avatar_editieren_anzeigen($f['u_id'], $f['u_nick'], $bilder, "avatar_aendern", $maxWidth, $maxHeight);
		
		// Avatar
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\" style=\"text-align:center;\" $bgcolor>" . $value . "</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
	}
	
	// Ausgabe in Tabelle
	$text .= "<form action=\"inhalt.php?bereich=einstellungen\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"u_id\" value=\"$f[u_id]\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"editieren\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	
	$zaehler = 0;
	
	// Überschrift: Benutzerdaten
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_benutzerdaten'], "", "", 0, "70", "");
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_benutzername'], "u_nick", $f['u_nick']);
	$zaehler++;
	
	// E-Mail (Nur Admins können sie direkt ändern)
	if ($admin) {
		$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_email_intern'], "u_email", $f['u_email']);
		$zaehler++;
	} else if ($u_level == 'U') {
		$value = $f['u_email'] . " (<a href=\"inhalt.php?bereich=einstellungen&aktion=email_aendern\">$lang[benutzer_avatar_aendern]</a>)";
		$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_email_intern'], "", $value);
		$zaehler++;
	}
	
	// Sperrkommentar (Nur für Admins)
	if ($admin) {
		if (!isset($f['u_kommentar'])) {
			$f['u_kommentar'] = "";
		}
		$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_kommentar'], "u_kommentar", $f['u_kommentar']);
		$zaehler++;
	}
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		// Signatur
		$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_signatur'], "u_signatur", $f['u_signatur']);
		$zaehler++;
		
		if ($eintritt_individuell == "1") {
			// Eintrittsnachricht
			$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_eintrittsnachricht'], "u_eintritt", $f['u_eintritt']);
			$zaehler++;
			
			// Austrittsnachricht
			$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_austrittsnachricht'], "u_austritt", $f['u_austritt']);
			$zaehler++;
		}
		
		$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
		
		// Überschrift: Passwort ändern
		$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_passwort_aendern'], "", "", 0, "70", "");
		
		// Neues Passwort
		$text .= zeige_formularfelder("password", $zaehler, $lang['benutzer_neues_passwort'], "u_passwort", "");
		$zaehler++;
		
		// Neues Passwort wiederholen
		$text .= zeige_formularfelder("password", $zaehler, $lang['benutzer_neues_passwort_wiederholen'], "u_passwort2", "");
		$zaehler++;
	}
	
	$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
	
	// Überschrift: Farbeinstellungen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_farbeinstellungen'], "", "", 0, "70", "");
	
	// Standardfarbe setzen, falls undefiniert
	if (strlen($f['u_farbe']) == 0) {
		$f['u_farbe'] = $user_farbe;
	}
	
	// Farbe ändern
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['benutzer_farbe'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<input type=\"text\" data-wheelcolorpicker name=\"u_farbe\" value=\"$f[u_farbe]\" />";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Farbe anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['benutzer_aktuell_gespeicherte_farbe'] . "</td>\n";
	$text .= "<td style=\"background-color:#" . $f['u_farbe'] . ";\">&nbsp;</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
	
	// Überschrift: Allgemeine Einstellungen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_allgemeine_einstellungen'], "", "", 0, "70", "");
	
	// E-Mails von anderen Benutzern akzeptieren
	if ($u_level != 'G') {
		$value = array($lang['einstellungen_verbieten'], $lang['einstellungen_erlauben']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $lang['benutzer_emails_akzeptieren'], "u_emails_akzeptieren", $value, $f['u_emails_akzeptieren']);
		$zaehler++;
	} else {
		$text .=  "<input type=\"hidden\" name=\"u_emails_akzeptieren\" value=\"$f[u_emails_akzeptieren]\">\n";
	}
	
	// System Ein/Austrittsnachrichten anzeigen/verbergen
	if ($u_level != "G") {
		$value = array($lang['einstellungen_unterdruecken'], $lang['einstellungen_anzeigen']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $lang['benutzer_systemmeldungen'], "u_systemmeldungen", $value, $f['u_systemmeldungen']);
		$zaehler++;
	}
	
	// Avatare im Chat anzeigen/verbergen
	$value = array($lang['einstellungen_unterdruecken'], $lang['einstellungen_anzeigen']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $lang['benutzer_avatare_im_chat'], "u_avatare_anzeigen", $value, $f['u_avatare_anzeigen']);
	$zaehler++;
	
	// Farbe des Chats
	$value = array($lang['benutzer_farbe_des_chats_blau'], $lang['benutzer_farbe_des_chats_gruen'], $lang['benutzer_farbe_des_chats_rot'], $lang['benutzer_farbe_des_chats_pink']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $lang['benutzer_farbe_des_chats'], "u_layout_farbe", $value, $f['u_layout_farbe']);
	$zaehler++;
	
	// Darstellung der Nachrichten im Chat
	if ($u_level != "G") {
		$value = array($lang['benutzer_darstellung_der_nachrichten_privat'], $lang['benutzer_darstellung_der_nachrichten_standard']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $lang['benutzer_darstellung_der_nachrichten'], "u_layout_chat_darstellung", $value, $f['u_layout_chat_darstellung']);
		$zaehler++;
	}
	
	// Smilies
	$value = array($lang['einstellungen_unterdruecken'], $lang['einstellungen_anzeigen']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $lang['benutzer_smilies'], "u_smilies", $value, $f['u_smilies']);
	$zaehler++;
	
	// Eigenen Punktewürfel
	if ($u_level <> 'G' && $punktefeatures) {
		$value = array($lang['einstellungen_unterdruecken'], $lang['einstellungen_anzeigen']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $lang['benutzer_eigener_punktewuerfel'], "u_punkte_anzeigen", $value, $f['u_punkte_anzeigen']);
		$zaehler++;
	}
	
	// Level nur für Admins
	if ($admin) {
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['benutzer_level'] . "</td>";
		$text .= "<td $bgcolor><select name=\"u_level\">\n";
		
		// Liste der Gruppen ausgeben
		reset($level);
		$i = 0;
		while ($i < count($level)) {
			$name = key($level);
			// Alle Level außer Besitzer zur Auswahl geben, für Gäste gibt es nur Gast
			if ($name != "B") {
				if ($f['u_level'] == "G") {
					if ($i == 0) {
						$text .= "<option selected value=\"G\">$level[G]\n";
					}
				} else {
					if ($name != "G") {
						if ($f['u_level'] == $name) {
							$text .= "<option selected value=\"$name\">$level[$name]\n";
						} else {
							$text .= "<option value=\"$name\">$level[$name]\n";
						}
					}
				}
			}
			next($level);
			$i++;
		}
		$text .= "</select></td>\n";
		$text .= "</tr>\n";
		$zaehler++;
	}
	
	$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
	
	// Überschrift: Geänderte Einstellungen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_geaenderte_einstellungen'], "", "", 0, "70", "");
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>";
	$text .= "<td $bgcolor><input type=\"reset\" value=\"$lang[einstellungen_zuruecksetzen]\"> $lang[einstellungen_zuruecksetzen_beschreibung]</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"eingabe\" value=\"$lang[einstellungen_speichern]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	// Fuß der Tabelle
	$text .= "</form>\n";
	
	// Kopf Tabelle Benutzerinfo
	$box = str_replace("%user%", $f['u_nick'], $lang['einstellungen_benutzer']);
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}

function zeige_aktionen() {
	// Zeigt Matrix der Aktionen an
	// Definition der aktionen in config.php ($def_was)
	
	global $u_id, $def_was, $lang, $forumfeatures;
	
	$text = '';
	$text .= "<form action=\"inhalt.php?bereich=einstellungen\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"aktion-eintragen\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	// Einstellungen aus Datenbank lesen
	$query = pdoQuery("SELECT * FROM `aktion` WHERE `a_user` = :a_user", [':a_user'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$was[$row['a_was']][$row['a_wann']] = $row;
		}
	}
	
	// Aktionen zeigen
	$text .= "<tr><td></td>";
	
	$i = 0;
	
	// Alle möglichen a_wann in Array lesen
	$query = pdoQuery("SHOW COLUMNS FROM `aktion` LIKE 'a_wann'", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount != 0) {
		$result = $query->fetch();
		$txt = str_replace("'", "", substr($result['Type'], 5, -1));
		$a_wann = explode(",", $txt);
	}
	$anzahl_spalten = count($a_wann);
	
	// Alle möglichen a_wie in Array lesen
	$query = pdoQuery("SHOW COLUMNS FROM `aktion` LIKE 'a_wie'", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount != 0) {
		$result = $query->fetch();
		$txt = str_replace("'", "", substr($result['Type'], 4, -1));
		$a_wie = explode(",", $txt);
	}
	$anzahl_wie = count($a_wie);
	
	// OLM merken
	$onlinemessage = $a_wie[3];
	
	// Wenn das Forum deaktiviert wird, sollte der 3. Eintrag aus $def_was raus, wichtig ist aber die Einhaltung der Reihenfolge in def_was
	if (!$forumfeatures) {
		unset($def_was[2]);
	}
	
	// Spezial Offline-Nachrichten (OLM löschen)
	$offline_wie = $a_wie;
	for ($i = 0; $i < $anzahl_wie; $i++) {
		if (isset($offline_wie[$i]) && $offline_wie[$i] == $onlinemessage)
			unset($offline_wie[$i]);
	}
	
	// Alle Kombinationen von a_wie mit onlinemessage erstellen
	// Keine muss als erstes, OLM als letztes definiert sein!
	for ($i = 1; $i < $anzahl_wie - 2; $i++) {
		$a_wie[] = $a_wie[$i] . "," . $onlinemessage;
	}
	
	// Zeile der a_wann ausgeben
	foreach ($a_wann as $a_wann_eintrag) {
		$text .= "<td style=\"text-align:center;\" class=\"smaller\"><b>" . $a_wann_eintrag . "</b></td>\n";
	}
	$text .= "</tr>\n";
	
	// Tabelle zeilenweise ausgeben
	$i = 0;
	foreach ($def_was as $def_was_eintrag) {
		if (($i % 2) > 0) {
			$bgcolor = 'class="tabelle_zeile1"';
		} else {
			$bgcolor = 'class="tabelle_zeile2"';
		}
		
		$text .= "<tr><td style=\"text-align:right; font-weight:bold;\" $bgcolor>" . $def_was_eintrag . "</td>\n";
		foreach ($a_wann as $a_wann_eintrag) {
			$text .= "<td style=\"text-align:center;\" $bgcolor>"
			. "<select name=\"aktion_datensatz[$def_was_eintrag][$a_wann_eintrag]\">\n";
			
			// Zwischen offline/online unterscheiden
			if ($a_wann_eintrag == "Sofort/Offline") {
				$wie = $offline_wie;
			} else {
				$wie = $a_wie;
			}
			
			// Nachrichtentypen einzeln als Auswahl ausgeben
			foreach ($wie as $auswahl) {
				if (isset($was[$def_was_eintrag][$a_wann_eintrag]) && $was[$def_was_eintrag][$a_wann_eintrag]['a_wie'] == $auswahl) {
					$text .= "<option selected value=\"" . $was[$def_was_eintrag][$a_wann_eintrag]['a_id'] . "|" . $auswahl . "\">" . str_replace(",", "+", $auswahl) . "\n";
				} else {
					$text .= "<option value=\"" . (isset($was[$def_was_eintrag][$a_wann_eintrag]) ? $was[$def_was_eintrag][$a_wann_eintrag]['a_id'] : "") . "|" . $auswahl . "\">"
							. str_replace(",", " + ", $auswahl) . "\n";
				}
			}
			$text .= "</select></td>\n";
			
		}
		$text .= "</tr>\n";
		
		$i++;
	}
	
	$text .= "<tr>\n";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor colspan=\"4\"><input type=\"submit\" name=\"uebergabe\" value=\"$lang[einstellungen_speichern]\">" . "</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	$text .= "</form>\n";
	
	return $text;
}

function eintrag_aktionen($aktion_datensatz) {
	// Array mit definierten Aktionen in die DB schreiben
	global $def_was, $u_id, $lang;
	
	$text = "";
	
	// Alle möglichen a_wann in Array lesen
	$query = pdoQuery("SHOW COLUMNS FROM `aktion` LIKE 'a_wann'", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount != 0) {
		$result = $query->fetch();
		$txt = str_replace("'", "", substr($result['Type'], 5, -1));
		$a_wann = explode(",", $txt);
	}
	
	foreach ($def_was as $def_was_eintrag) {
		foreach ($a_wann as $a_wann_eintrag) {
			// In aktion_datensatz stehen ID und Wert als a_id|a_wie
			$temp = explode("|", $aktion_datensatz[$def_was_eintrag][$a_wann_eintrag]);
			
			$f['a_wie'] = $temp[1];
			$f['a_was'] = $def_was_eintrag;
			$f['a_wann'] = $a_wann_eintrag;
			$f['a_user'] = $u_id;
			
			if (!$temp[0] || $temp[0] == "0" || $temp[0] == "") {
				pdoQuery("DELETE FROM `aktion` WHERE `a_was` = :a_was AND `a_wann` = :a_wann AND `a_user` = :a_user", [':a_was'=>$f['a_was'], ':a_wann'=>$f['a_wann'], ':a_user'=>$u_id]);
				
				pdoQuery("INSERT INTO `aktion` (`a_wie`, `a_was`, `a_wann`, `a_user`) VALUES (:a_wie, :a_was, :a_wann, :a_user)",
					[
						':a_wie'=>$f['a_wie'],
						':a_was'=>$f['a_was'],
						':a_wann'=>$f['a_wann'],
						':a_user'=>$f['a_user']
					]);
			} else {
				pdoQuery("UPDATE `aktion` SET `a_wie` = :a_wie, `a_was` = :a_was, `a_wann` = :a_wann, `a_user` = :a_user WHERE `a_id` = :a_id",
					[
						':a_id'=>$temp[0],
						':a_wie'=>$f['a_wie'],
						':a_was'=>$f['a_was'],
						':a_wann'=>$f['a_wann'],
						':a_user'=>$f['a_user']
					]);
			}
		}
	}
	$erfolgsmeldung = $lang['einstellungen_erfolgsmeldung_einstellungen'];
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
	
	return $text;
}

function formular_email_aendern($f) {
	global $lang;
	
	$text = '';
	$box = str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_email_aendern']);
	
	$text .= $lang['einstellungen_email_aendern_inhalt'] . "\n";
	$text .= "<form action=\"inhalt.php?bereich=einstellungen\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"u_id\" value=\"$f[u_id]\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"email_aendern_final\">\n";
	$text .= "<table style=\"width:100%;\">";
	$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_email_intern'], "u_email", $f['u_email']);
	$zaehler++;
	
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"eingabe\" value=\"$lang[einstellungen_speichern]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	$text .= "</form>\n";
	
	zeige_tabelle_zentriert($box, $text);
}
?>