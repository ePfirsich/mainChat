<?php

require_once("functions.php");
require_once("functions-func-verlasse_chat.php");
require_once("functions-func-nachricht.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, o_js, u_level, admin
id_lese($id);

if (!isset($suchtext)) {
	$suchtext = "";
}
$uu_suchtext = URLENCODE($suchtext);

// welcher raum soll abgefragt werden? ggf voreinstellen
// Positives $schau_raum entspricht r_id
// Negatives $schau_raum entspricht o_who * -1

if (isset($schau_raum) && !is_numeric($schau_raum)) {
	unset($schau_raum);
}

if ((isset($schau_raum)) && $schau_raum < 0) {
	// Community-Bereich gewählt
	$raum_subquery = "AND o_who=" . ($schau_raum * (-1));
} elseif ((isset($schau_raum)) && $schau_raum > 0) {
	// Raum gewählt
	$raum_subquery = "AND r_id=$schau_raum";
} elseif (!isset($schau_raum) && $o_who != 0) {
	// Voreinstellung auf den aktuellen Community-Bereich
	$schau_raum = $o_who * (-1);
	$raum_subquery = "AND o_who=$o_who";
} elseif (!isset($schau_raum) || $schau_raum == 0) {
	// Voreinstellung auf den aktuellen Raum
	$schau_raum = $o_raum;
	$raum_subquery = "AND r_id=$schau_raum";
}

$title = $body_titel . ' - Benutzer';
zeige_header_anfang($title, 'mini', '', $u_layout_farbe);

$eingabe_breite = 31;

if ($aktion != "zeigalle" || $u_level != "S") {
	// Target Sonderzeichen raus...
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);
	
	echo "<script>\n" . "  var id='$id';\n"
		. "  var u_nick='" . $fenster . "';\n" . "  var raum='$schau_raum';\n";
	if (($admin) || ($u_level == 'M')) {
		echo "  var inaktiv_ansprechen='0';\n"
			. "  var inaktiv_mailsymbol='0';\n"
			. "  var inaktiv_userfunktionen='0';\n";
	} else {
		echo "  var inaktiv_ansprechen='$userleiste_inaktiv_ansprechen';\n"
			. "  var inaktiv_mailsymbol='$userleiste_inaktiv_mailsymbol';\n"
			. "  var inaktiv_userfunktionen='$userleiste_inaktiv_userfunktionen';\n";
	}
	
	echo "  var stdparm='?id='+id+'&schau_raum='+raum;\n"
		. "  var stdparm2='?id='+id;\n";
		if ($aktion != "chatuserliste") {
			?>
			window.focus();
			function resetinput() {
				document.forms['form'].elements['text'].value=document.forms['form'].elements['text2'].value;
					<?php
					if ($u_clearedit == 1) {
						echo "	document.forms['form'].elements['text2'].value='';\n";
					}
					?>
				document.forms['form'].submit();
				document.forms['form'].elements['text2'].focus();
				document.forms['form'].elements['text2'].select();
			}
			<?php
		}
	?>
	</script>
	<?php
	if ($aktion == "chatuserliste") {
		$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=user.php?id=' . $id . '&aktion=chatuserliste">';
		zeige_header_ende($meta_refresh);
	} else {
		zeige_header_ende();
	}
}

?>
<body>
<?php

// Login ok?
if (strlen($u_id) != 0) {
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	if ($aktion != "chatuserliste" && $aktion != "statistik") {
		// Menü als erstes ausgeben, falls nicht Spezialfall im Chatwindow oder statistikfenster.
		$box = $t['menue13'];
		$text .= "<ul style=\"list-style-type: none; padding:0px;\">";
		$text .= "<li><a href=\"user.php?id=$id&schau_raum=$schau_raum\">$t[menue1]</a>\n";
		if ($u_level != "G") {
			$text .= "<li><a href=\"user.php?id=$id&schau_raum=$schau_raum&aktion=suche\">$t[menue2]</a>\n";
		}
		
		if ($adminlisteabrufbar && $u_level != "G") {
			$text .= "<li><a href=\"user.php?id=$id&schau_raum=$schau_raum&aktion=adminliste\">$t[menue12]</a>\n";
		}
		if ($u_level != "G") {
			if ($punktefeatures) {
				$ur1 = "top10.php?id=$id";
				$url = "href=\"$ur1\" ";
				$text .= "<li><a $url>$t[menue7]</a>\n";
			}
			;
			$ur1 = "freunde.php?id=$id";
			$url = "href=\"$ur1\" ";
			$text .= "<li><a $url>$t[menue8]</a>\n";
		}
		if ($admin) {
			$text .= "<li><a href=\"user.php?id=$id&schau_raum=$schau_raum&aktion=zeigalle\">$t[menue3]</a>\n";
			if ($userimport) {
				$text .= "<li><a href=\"user.php?id=$id&schau_raum=$schau_raum&aktion=userimport\">$t[menue10]</a>\n";
				$text .= "<li><a href=\"user.php?id=$id&schau_raum=$schau_raum&aktion=userloeschen\">$t[menue11]</a>\n";
			}
		}
		$text .= "</ul>";
		
		if ($aktion != "zeigalle") {
			echo "<br>";
			zeige_tabelle_zentriert($box, $text);
		}
	}
	
	if ($admin && isset($kick_user_chat) && $user) {
		// Nur Admins: Benutzer sofort aus dem Chat kicken
		$query = "SELECT o_id,o_raum,o_name FROM online WHERE o_user='" . mysqli_real_escape_string($mysqli_link, $user) . "' AND o_level!='C' AND o_level!='S' AND o_level!='A' ";
		$result = mysqli_query($mysqli_link, $query);
		
		if ($result && mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_object($result);
			verlasse_chat($user, $row->o_name, $row->o_raum);
			logout($row->o_id, $user, "user->kick " . $u_id . " " . $u_nick);
			
			mysqli_free_result($result);
		} else {
			echo $t['sonst40'];
		}
	}
	
	// Auswahl
	switch ($aktion) {
		
		case "userimport2":
		// Benutzer importieren
			if ($u_level == "S") {
				if (is_uploaded_file($HTTP_POST_FILES['userdatei']['tmp_name'])) {
					$file = fopen($HTTP_POST_FILES['userdatei']['tmp_name'],
						"r");
					if ($file) {
						
						echo "import.....<br><small>";
						$i = 1;
						while (!feof($file)) {
							$zeile = rtrim(fgets($file, 4096));
							$userd = explode("\t", $zeile);
							if ($userd[0] != "Benutzername"
								AND strlen($userd[0]) > 2 AND $userd[1]
								AND $userd[2] AND $userd[3]) {
								
								echo " $i " . $userd[0];
								
								unset($f);
								unset($ui_userid);
								$f['u_nick'] = $userd[0];
								$f['u_adminemail'] = $userd[2];
								$f['u_passwort'] = $userd[3];
								$query = "SELECT `u_id` FROM `user` WHERE `u_nick` LIKE '" . mysqli_real_escape_string($mysqli_link, $f[u_nick]) . "'"; // Benutzer importieren
								$result = mysqli_query($mysqli_link, $query);
								if ($result && mysqli_num_rows($result)) {
									$ui_userid = mysqli_result($result, 0, 0);
									echo " ID: $ui_userid";
								}
								
								if (schreibe_db("user", $f, $ui_userid, "u_id")) {
									echo " Ok";
								}
								
								echo "<br>\n";
								$i++;
							}
						}
						echo "</small><br><br>";
						
						fclose($file);
						unlink($HTTP_POST_FILES['userdatei']['tmp_name']);
					} else {
						echo $t['sonst48'];
					}
				} else {
					echo $t['sonst47'];
				}
			} else {
				echo "$t[sonst41]";
			}
		
		case "userimport":
		// Benutzer im Format "Benutzername\tAdmin E-Mail\tPasswort" importieren
		// Formular ausgeben
			if ($u_level == "S") {
				
				$box = $t['sonst45'];
				$text = "<form name=\"userimport2\" enctype=\"multipart/form-data\" action=\"user.php\" method=\"post\">\n"
					. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
					. "<input type=\"hidden\" name=\"aktion\" value=\"userimport2\">\n"
					. "<input type=\"hidden\" name=\"aktion\" value=\"userimport2\">\n"
					. $f1
					. htmlspecialchars($t['sonst46']) . $f2 . "\n"
					. "<br>\n"
					. "<br>" . $f1
					. "&nbsp;<input name=\"userdatei\" type=\"file\" size=\"$eingabe_breite\">"
					. $f2 . "\n";
				
				$text .= "<br><div style=\"text-align: right;\">" . $f1
					. "<input type=\"submit\" name=\"los\" value=\"Go!\">"
					. $f2 . "</div></form>\n";

				echo "<br>";
				
				// Box anzeigen
				zeige_tabelle_zentriert($box, $text);
				
			} else {
				echo "$t[sonst41]";
			}
			break;
		
		case "userloeschen":
		// Benutzer Löschen
			if ($u_level == "S") {
				
				$box = $t['sonst51'];
				$text = $f1 . $t['sonst49'] . " <a href=\"user.php?id=$id&aktion=userloeschen2\">[" . $t['sonst50'] . "]</a>" . $f2;
				
				echo "<br>";
				
				// Box anzeigen
				zeige_tabelle_zentriert($box, $text);
			} else {
				echo "$t[sonst41]";
			}
			break;
		
		case "userloeschen2":
		// Benutzer Löschen
			if ($u_level == "S") {
				
				$query = "DELETE FROM `user` WHERE `u_level` != 'C' AND `u_level` != 'S'";
				$result = mysqli_query($mysqli_link, $query);
				if ($result) {
					$ok = mysqli_affected_rows($mysqli_link) . " " . $ok = $t['sonst52'];
				} else {
					$ok = $t['sonst53'];
				}
				
				$box = $t['sonst51'];
				$text = $f1 . $ok . $f2;
				
				echo "<br>";
				
				// Box anzeigen
				zeige_tabelle_zentriert($box, $text);
			} else {
				echo "$t[sonst41]";
			}
			break;
		
		case "zeigalle":
			if ($u_level == "S") {
				
				// Alle Benutzer listen
				$query = "SELECT u_nick,u_email,u_adminemail,u_url,u_level,u_punkte_gesamt,"
					. "date_format(u_login,'%d.%m.%y %H:%i') as login FROM user "
					. "ORDER BY u_nick";
				
				$result = mysqli_query($mysqli_link, $query);
				$rows = mysqli_num_rows($result);
				
				if (!$result || mysqli_num_rows($result) == 0) {
					echo "<p>$t[sonst4]</p>";
				} else {
					/*
					Header("Content-Disposition: attachment; filename=\"mainChat-Export.xls\"");
					Header("Pragma: no-cache");
					Header("Expires: 0");
					Header("ETag: 900aa-551-3c45620d");
					Header("Accept-Ranges: bytes");
					Header("Keep-Alive: timeout=15, max=100");
					Header("Connection: Keep-Alive");
					Header("Content-Type: application/vnd.ms-excel");
					*/
					
					echo $t['sonst26'];
					
					while ($row = mysqli_fetch_object($result)) {
						echo str_replace("\\", "",
							htmlspecialchars($row->u_nick)) . "\t"
							. str_replace("\\", "", htmlspecialchars($row->u_adminemail)) . "\t"
							. str_replace("\\", "", htmlspecialchars($row->u_email)) . "\t"
							. str_replace("\\", "", htmlspecialchars($row->u_url)) . "\t"
							. $row->u_punkte_gesamt . "\t" . $row->login
							. "\t" . $row->u_level . "\n";
					}
				}
			} else {
				echo "$t[sonst41]";
			}
			break;
		
		case "suche":
		// Suchmaske für Ergebnis oder Suchmaske für erste Suche ausgeben
			if (strlen($suchtext) > 3 || (isset($suchtext_eingabe) && $suchtext_eingabe == "Go!")) {
				
				// Suchergebnis mit Formular ausgeben
				$box = $t['sonst16'];
				$text = "<form name=\"suche\" action=\"user.php\" method=\"post\">\n"
					. "<table class=\"tabelle_kopf\">"
					. "<tr><td class=\"tabelle_koerper\" colspan=2>" . $f1 . $t['sonst6'] . $f2
					. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
					. "<input type=\"hidden\" name=\"aktion\" value=\"suche\">\n"
					. "</td></tr>" . "<tr><td class=\"tabelle_koerper\" colspan=2>" . $f1
					. "&nbsp;<input type=\"text\" name=\"suchtext\" value=\"$suchtext\" size=\"$eingabe_breite\">"
					. $f2 . "</td></tr>\n";
				
				if ($admin) {
					// Suchformular nach IP-Adressen
					$text .= "<tr><td class=\"tabelle_koerper\" colspan=2><br>" . $f1 . $t['sonst30']
						. $f2 . "</td></tr>" . "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1
						. $t['sonst31'] . $f2 . "</td><td class=\"tabelle_koerper\">" . $f1
						. "&nbsp;<input type=\"text\" name=\"f[ip]\" value=\"$f[ip]\" size=\"6\">"
						. $f2 . "</td></tr>\n";
					
					// Liste der Gruppen ausgeben
					$text .= "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1 . $t['sonst21']
						. "</td>\n" . "<td class=\"tabelle_koerper\">&nbsp;<SELECT name=\"f[level]\">\n"
						. "<option value=\"\">$t[sonst22]\n";
					
					reset($level);
					while (list($levelname, $levelbezeichnung) = each($level)) {
						if ($levelname != "B") {
							if ($f['level'] == $levelname) {
								$text .= "<option selected value=\"$levelname\">$levelbezeichnung\n";
							} else {
								$text .= "<option value=\"$levelname\">$levelbezeichnung\n";
							}
						}
					}
					$text .= "</select>" . $f2 . "</td></tr>\n";
				}
				
				// Suche nach Neu & erstem Login
				$text .= "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1 . $t['sonst32'] . $f2
					. "</td><td class=\"tabelle_koerper\">" . $f1
					. "&nbsp;<input type=\"text\" name=\"f[user_neu]\" value=\"$f[user_neu]\" size=\"6\">&nbsp;"
					. $t['sonst34'] . $f2 . "</td></tr>\n"
					. "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1 . $t['sonst33'] . $f2
					. "</td><td class=\"tabelle_koerper\">" . $f1
					. "&nbsp;<input type=\"text\" name=\"f[user_login]\" value=\"$f[user_login]\" size=\"6\">&nbsp;"
					. $t['sonst35'] . $f2 . "</td></tr>\n";
				
				// Suche nach Benutzer mit Homepage
				$text .= "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1 . $t['sonst38']
					. "</td>\n"
					. "<td class=\"tabelle_koerper\">&nbsp;<select name=\"f[u_chathomepage]\">\n";
				if ($f['u_chathomepage'] == "J") {
					$text .= "<option value=\"N\">$t[sonst22]\n"
						. "<option selected value=\"J\">$t[sonst39]\n";
				} else {
					$text .= "<option selected value=\"N\">$t[sonst22]\n"
						. "<option value=\"J\">$t[sonst39]\n";
				}
				$text .= "</select>" . $f2 . "</td></tr>\n";
				
				$text .= "<tr><td class=\"tabelle_koerper\" colspan=2 align=right>" . $f1
					. "<input type=\"submit\" name=\"suchtext_eingabe\" value=\"Go!\">"
					. $f2 . "</td></tr>" . "</table></form>\n";
				
				// Box anzeigen
				zeige_tabelle_zentriert($box, $text);
				
				echo "<br>";
				
				// wenn einer nur nach % sucht, kanns auch weggelassen werden
				if ($suchtext == "%")
					unset($suchtext);
				
				// Variablen säubern
				unset($query);
				unset($where);
				unset($subquery);
				unset($sortierung);
				
				$subquery = "";
				
				// Grundselect
				$select = "SELECT * FROM user ";
				
				// Where bedingung anhand des Suchtextes und des Levels
				if (!isset($suchtext) || !$suchtext) {
					// Kein Suchtext, aber evtl subquery
					$where[0] = "WHERE ";
				} elseif (strpos($suchtext, "%") >= 0) {
					$suchtext = mysqli_real_escape_string($mysqli_link, $suchtext);
					if ($admin) {
						$where[0] = "WHERE (u_nick LIKE '$suchtext' OR u_email LIKE '$suchtext' "
							. " OR u_adminemail LIKE '$suchtext') ";
					} else {
						$where[0] = "WHERE (u_nick LIKE '$suchtext' OR u_email LIKE '$suchtext') ";
					}
				} else {
					$suchtext = mysqli_real_escape_string($mysqli_link, $suchtext);
					if ($admin) {
						$where[1] = "WHERE u_nick = '$suchtext' ";
						$where[2] = "WHERE u_email = '$suchtext' ";
						$where[3] = "WHERE u_adminemail = '$suchtext' ";
					} else {
						$where[0] = "WHERE u_nick = '$suchtext' ";
						$where[1] = "WHERE u_email = '$suchtext' ";
					}
					
				}
				
				// Subquerys, wenn parameter gesetzt, teils anhand des Levels
				if ($admin) {
					// Optional level ergänzen
					if ($f['level'] && $subquery)
						$subquery .= "AND u_level = '" . mysqli_real_escape_string($mysqli_link, $f[level]) . "' ";
					elseif ($f['level'])
						$subquery = " u_level = '" . mysqli_real_escape_string($mysqli_link, $f[level]) . "' ";
					
					if ($f['ip'] && $subquery)
						$subquery .= "AND u_ip_historie LIKE '%" . mysqli_real_escape_string($mysqli_link, $f[ip]) . "%' ";
					elseif ($f['ip'])
						$subquery = " u_ip_historie LIKE '%" . mysqli_real_escape_string($mysqli_link, $f[ip]) . "%' ";
				}
				
				if ($f['u_chathomepage'] == "J" && $subquery)
					$subquery .= "AND u_chathomepage='J' ";
				elseif ($f['u_chathomepage'] == "J")
					$subquery = " u_chathomepage='J' ";
				
				if ($f['user_neu'] && $subquery)
					$subquery .= "AND u_neu IS NOT NULL AND date_add(u_neu, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_neu]) . "' day)>=NOW() ";
				elseif ($f['user_neu'])
					$subquery = " u_neu IS NOT NULL AND date_add(u_neu, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_neu]) . "' day)>=NOW() ";
				
				if ($f['user_login'] && $subquery)
					$subquery .= "AND date_add(u_login, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_login]) . "' hour)>=NOW() ";
				elseif ($f['user_login'])
					$subquery = " date_add(u_login, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_login]) . "' hour)>=NOW() ";
				
				if ($u_level == "U" && $subquery)
					$subquery .= "AND u_level IN ('A','C','G','M','S','U') ";
				elseif ($u_level == "U")
					$subquery = " u_level IN ('A','C','G','M','S','U') ";
				
				// Zur Sicherheit, falls ein Gast die Such URL direkt aufruft
				if ($u_level == "G")
					$subquery = " 1=2 ";
				
				// Sortierung
				if ($admin) {
					$sortierung = "ORDER BY u_nick ";
				} else {
					$sortierung = "ORDER BY u_nick LIMIT 0,$max_user_liste ";
				}
				
				// Zusammensetzen der Query
				$query = $select;
				if ($where[0] == "WHERE " && $subquery)
					$query .= $where[0] . " " . $subquery;
				elseif ($where[0] != "WHERE " && $subquery)
					$query .= $where[0] . " AND " . $subquery;
				elseif ($where[0] != "WHERE ")
					$query .= $where[0];
				
				for ($i = 1; $i < count($where); $i++) {
					if ($where[$i]) {
						$query .= "UNION " . $select . $where[$i];
						if ($subquery)
							$query .= " AND " . $subquery;
					}
				}
				$query .= $sortierung;
				
				$result = mysqli_query($mysqli_link, $query);
				$anzahl = mysqli_num_rows($result);
				
				if ($anzahl == 0) {
					// Kein user gefunden
					if ($suchtext == "%")
						$suchtext = "";
					echo "<P>"
						. str_replace("%suchtext%", $suchtext, $t['sonst5'])
						. "</P>\n";
				} elseif ($anzahl == 1) {
					// Einen Benutzer gefunden, alle Benutzerdaten ausgeben
					if (!isset($zeigeip))
						$zeigeip = 0;
					while ($arr = mysqli_fetch_array($result)) {
						user_zeige($arr['u_id'], $admin, $schau_raum, $u_level,
							$zeigeip);
					}
				} else {
					
					// Bei mehr als 1000 Ergebnissen Fehlermeldung ausgeben
					if ($anzahl > 2000) {
						echo "<b>$t[sonst24]</b><br>";
					} else {
						
						// Mehrere Benutzer gefunden, als Tabelle ausgeben
						for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
							
							// Array mit Benutzerdaten und Infotexten aufbauen
							$larr[$i]['u_email'] = str_replace("\\", "", htmlspecialchars($row['u_email']));
							$larr[$i]['u_nick'] = strtr(
								str_replace("\\", "",
									htmlspecialchars($row['u_nick'])), "I",
								"i");
							$larr[$i]['u_level'] = $row['u_level'];
							$larr[$i]['u_id'] = $row['u_id'];
							$larr[$i]['u_away'] = $row['u_away'];
							// Wenn der Benutzer nicht möchte, daß sein Würfel angezeigt wird, ist hier die einfachste Möglichkeit
							if ($row['u_punkte_anzeigen'] != "N") {
								$larr[$i]['gruppe'] = hexdec($row['u_punkte_gruppe']);
							} else {
								$larr[$i]['gruppe'] = 0;
							}
							$larr[$i]['u_chathomepage'] = $row['u_chathomepage'];
							// Raumbesitzer einstellen, falls Level=Benutzer
							if (isset($larr[$i]['isowner'])
								&& $larr[$i]['isowner']
								&& $userdata['u_level'] == "U") {
								$larr[$i]['u_level'] = "B";
							}
							
							flush();
						}
						mysqli_free_result($result);
						
						$rows = count($larr);
						echo user_liste($larr, $rows);
					}
					
				}
				
			} else {
				
				// Suchformular ausgeben
				$box = $t['sonst17'];
				$text = "<form name=\"suche\" action=\"user.php\" method=\"post\">\n"
					. "<table class=\"tabelle_kopf\">";
				if ($admin) {
					$text .= "<tr><td class=\"tabelle_koerper\" colspan=2>" . $f1 . $t['sonst25'] . $f2
						. "</td></tr>";
				} else {
					$text .= "<tr><td class=\"tabelle_koerper\" colspan=2>" . $f1 . $t['sonst7'] . $f2
						. "</td></tr>";
				}
				
				$text .= "<tr><td class=\"tabelle_koerper\" colspan=2>" . $f1
				. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"suche\">\n"
					. "&nbsp;<input type=\"text\" name=\"suchtext\" value=\"$suchtext\" size=\"$eingabe_breite\">"
					. $f2 . "</td></tr>\n";
				
				if ($admin) {
					
					if (!isset($f)) {
						$f['ip'] = "";
						$f['level'] = "";
					}
					
					// Suchformular nach IP-Adressen
					$text .= "<tr><td class=\"tabelle_koerper\" colspan=2><br>" . $f1 . $t['sonst30']
						. $f2 . "</td></tr>" . "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1
						. $t['sonst31'] . $f2 . "</td><td class=\"tabelle_koerper\">" . $f1
						. "&nbsp;<input type=\"text\" name=\"f[ip]\" value=\"$f[ip]\" size=\"6\">"
						. $f2 . "</td></tr>\n";
					
					// Liste der Gruppen ausgeben
					$text .= "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1 . $t['sonst21']
						. "</td>\n" . "<td class=\"tabelle_koerper\">&nbsp;<select name=\"f[level]\">\n"
						. "<option value=\"\">$t[sonst22]\n";
					
					reset($level);
					while (list($levelname, $levelbezeichnung) = each($level)) {
						if ($levelname != "B") {
							if ($f['level'] == $levelname) {
								$text .= "<option selected value=\"$levelname\">$levelbezeichnung\n";
							} else {
								$text .= "<option value=\"$levelname\">$levelbezeichnung\n";
							}
						}
					}
					$text .= "</select>" . $f2 . "</td></tr>\n";
					
				}
				
				if (!isset($f['user_neu'])) {
					$f['user_neu'] = "";
				}
				if (!isset($f['user_login'])) {
					$f['user_login'] = "";
				}
				if (!isset($f['u_chathomepage'])) {
					$f['u_chathomepage'] = "";
				}
				
				// Suche nach Neu & erstem login
				$text .= "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1 . $t['sonst32'] . $f2
					. "</td><td class=\"tabelle_koerper\">" . $f1
					. "&nbsp;<input type=\"text\" name=\"f[user_neu]\" value=\"$f[user_neu]\" size=\"6\">&nbsp;"
					. $t['sonst34'] . $f2 . "</td></tr>\n"
					. "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1 . $t['sonst33'] . $f2
					. "</td><td class=\"tabelle_koerper\">" . $f1
					. "&nbsp;<input type=\"text\" name=\"f[user_login]\" value=\"$f[user_login]\" size=\"6\">&nbsp;"
					. $t['sonst35'] . $f2 . "</td></tr>\n";
				
				// Suche nach Benutzer mit Homepage
				$text .= "<tr><td class=\"tabelle_koerper\" style=\"text-align: right;\">" . $f1 . $t['sonst38'] . $f2
					. "</td>\n"
					. "<td class=\"tabelle_koerper\">&nbsp;<select name=\"f[u_chathomepage]\">\n";
				if ($f['u_chathomepage'] == "J") {
					$text .= "<option value=\"N\">$t[sonst22]\n"
						. "<option selected value=\"J\">$t[sonst39]\n";
				} else {
					$text .= "<option selected value=\"N\">$t[sonst22]\n"
						. "<option value=\"J\">$t[sonst39]\n";
				}
				$text .= "</select></td></tr>\n";
				
				$text .= "<tr><td class=\"tabelle_koerper\" colspan=2 style=\"text-align: right;\">" . $f1
					. "<input type=\"submit\" name=\"suchtext_eingabe\" value=\"Go!\">"
					. $f2 . "</td></tr>" . "</table></form>\n";
				
				zeige_tabelle_zentriert($box, $text);
			}
			
			break;
		
		case "adminliste":
			if ($adminlisteabrufbar && $u_level != "G") {
				
				$result = mysqli_query($mysqli_link, 'SELECT * FROM `user` WHERE `u_level` = "S" OR `u_level` = "C" ORDER BY `u_nick` ');
				$anzahl = mysqli_num_rows($result);
				
				for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
					// Array mit Benutzerdaten und Infotexten aufbauen
					$larr[$i]['u_email'] = str_replace("\\", "", htmlspecialchars($row['u_email']));
					$larr[$i]['u_nick'] = strtr(str_replace("\\", "", htmlspecialchars($row['u_nick'])),
						"I", "i");
					$larr[$i]['u_level'] = $row['u_level'];
					$larr[$i]['u_id'] = $row['u_id'];
					$larr[$i]['u_away'] = $row['u_away'];
					// Wenn der Benutzer nicht möchte, daß sein Würfel angezeigt wird, ist hier die einfachste Möglichkeit
					if ($row['u_punkte_anzeigen'] != "N") {
						$larr[$i]['gruppe'] = hexdec($row['u_punkte_gruppe']);
					} else {
						$larr[$i]['gruppe'] = 0;
					}
					$larr[$i]['u_chathomepage'] = $row['u_chathomepage'];
					
					flush();
				}
				mysqli_free_result($result);
				
				$rows = count($larr);
				echo user_liste($larr, $rows);
			}
			
			break;
		
		case "zeig":
			if (!isset($zeigeip)) {
				$zeigeip = 0;
			}
			// Benutzer mit ID $user anzeigen
			if ($user) {
				// Benutzer listen
				user_zeige($user, $admin, $schau_raum, $u_level, $zeigeip);
			} else echo "<p>$t[sonst11]</p>\n";
			
			break;
		
		case "statistik":
			include "statistik.php";
			break;
		
		default;
		// Beichtstuhlmodus
		// ID der Lobby merken
			if (isset($beichtstuhl) && $beichtstuhl && !$admin) {
				// Id der Lobby als Voreinstellung ermitteln
				$query = "SELECT r_id FROM raum WHERE r_name LIKE '" . mysqli_real_escape_string($mysqli_link, $lobby) . "' ";
				$result = mysqli_query($mysqli_link, $query);
				$rows = mysqli_num_rows($result);
				
				if ($rows > 0) {
					$lobby_id = mysqli_result($result, 0, "r_id");
				}
				mysqli_free_result($result);
			}
			
			// Raum listen
			$query = "SELECT raum.*,o_user,o_name,o_ip,o_userdata,o_userdata2,o_userdata3,o_userdata4,r_besitzer=o_user AS isowner "
				. "FROM online LEFT JOIN raum ON o_raum=r_id "
				. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout $raum_subquery "
				. "ORDER BY o_name";
			
			$result = mysqli_query($mysqli_link, $query);
			
			for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
				// Array mit Benutzerdaten und Infotexten aufbauen
				$userdata = unserialize(
					$row['o_userdata'] . $row['o_userdata2']
						. $row['o_userdata3'] . $row['o_userdata4']);
				
				if ((!isset($beichtstuhl) || !$beichtstuhl) || $admin
					|| $userdata['u_id'] == $u_id || $lobby_id == $schau_raum
					|| $userdata['u_level'] == "S"
					|| $userdata['u_level'] == "C") {
					
					// Variable aus o_userdata setzen
					$larr[$i]['u_email'] = str_replace("\\", "", htmlspecialchars($userdata['u_email']));
					$larr[$i]['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($userdata['u_nick'])), "I", "i");
					$larr[$i]['u_level'] = $userdata['u_level'];
					$larr[$i]['u_id'] = $userdata['u_id'];
					$larr[$i]['u_away'] = $userdata['u_away'];
					$larr[$i]['u_punkte_anzeigen'] = $userdata['u_punkte_anzeigen'];
					$larr[$i]['u_punkte_gruppe'] = $userdata['u_punkte_gruppe'];
					$larr[$i]['r_besitzer'] = $row['r_besitzer'];
					$larr[$i]['r_topic'] = $row['r_topic'];
					$larr[$i]['o_ip'] = $row['o_ip'];
					$larr[$i]['isowner'] = $row['isowner'];
					
					if ($userdata['u_punkte_anzeigen'] != "N") {
						$larr[$i]['gruppe'] = hexdec($userdata['u_punkte_gruppe']);
					} else {
						$larr[$i]['gruppe'] = 0;
					}
					$larr[$i]['u_chathomepage'] = $userdata['u_chathomepage'];
					if (!$row['r_name'] || $row['r_name'] == "NULL") {
						$larr[$i]['r_name'] = "[" . $whotext[($schau_raum * (-1))] . "]";
					} else {
						$larr[$i]['r_name'] = $t['sonst42'] . $row['r_name'];
					}
				} else {
					// Anonyme Benutzer
					$larr[$i]['u_nick'] = $t['sonst37'];
					$larr[$i]['u_level'] = $userdata['u_level'];
					$larr[$i]['u_away'] = $userdata['u_away'];
					$larr[$i]['r_besitzer'] = $row['r_besitzer'];
					$larr[$i]['r_topic'] = $row['r_topic'];
					$larr[$i]['o_ip'] = $row['o_ip'];
					if (!$row['r_name'] || $row['r_name'] == "NULL") {
						$larr[$i]['r_name'] = "[" . $whotext[($schau_raum * (-1))] . "]";
					} else {
						$larr[$i]['r_name'] = $t['sonst42'] . $row['r_name'];
					}
					
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
			mysqli_free_result($result);
			
			if (isset($larr)) {
				$rows = count($larr);
			} else {
				$rows = 0;
			}
			
			// Fehlerbehandlung, falls Arry leer ist -> nichts gefunden
			if (!$rows && $schau_raum > 0) {
				
				$query = "SELECT r_name FROM raum WHERE r_id=" . intval($schau_raum);
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) != 0)
					$r_name = mysqli_result($result, 0, 0);
				echo "<p>" . str_replace("%r_name%", $r_name, $t['sonst13'])
					. "</p>\n";
				
			} elseif (!$rows && $schau_raum < 0) {
				
				echo "<p>"
					. str_replace("%whotext%", $whotext[($schau_raum * (-1))],
						$t['sonst43']) . "</p>\n";
				
			} else { // array ist gefüllt -> Daten ausgeben
				if ($aktion != "chatuserliste") {
					$box = $t['sonst54'];
					$text = '';
					
					$text .= "<b>" . $larr[0]['r_name'] . "</b><br>"
					. ($larr[0]['r_topic'] ? $f1 . "Topic: "
							. $larr[0]['r_topic'] . $f2 . "<br>" : "")
					. "<br>";
					
					// Benutzerliste ausgeben
					$text .= user_liste($larr, $rows);
					
					$text .= "<p style=\"text-align:center;\">" . $f1 . $t['sonst12'] . $f2 . "</p>";
					
					zeige_tabelle_zentriert($box, $text);
					
					// Raumauswahl
					$text = '';
					$box = $t['sonst14'];
					
					$text .= "<form name=\"raum\" action=\"user.php\" method=\"post\">";
					$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">";
					$text .= $f1;
					$text .= "<select name=\"schau_raum\" onChange=\"document.raum.submit()\">";
					if ($admin) {
						$text .= raeume_auswahl($schau_raum, TRUE, FALSE, FALSE);
					} else {
						$text .= raeume_auswahl($schau_raum, FALSE, FALSE, FALSE);
					}
					$text .= "</select>";
					$text .= "<input type=\"submit\" name=\"eingabe\" value=\"Go!\">";
					$text .= $f2;
					$text .= "</form>";
					
					// Box anzeigen
					zeige_tabelle_zentriert($box, $text);
						
				} else {
					$box = "<center>" . $t['sonst1'] . "</center>";
					$text = "";
					
					$linkuser = "user.php?id=$id&aktion=chatuserliste";
					$linksmilies = "smilies.php?id=$id";
					$text .= "<center>";
					$text .= "<a href=\"$linkuser\"><span class=\"fa fa-refresh icon16\"></span>" . $t['sonst19'] . "</a>";
					if (!isset($beichtstuhl) || !$beichtstuhl) {
						$text .= " | <a href=\"$linksmilies\"><span class=\"fa fa-smile-o icon16\"></span>" . $t['sonst20'] . "</a>";
					}
					$text .= "<br><br>\n" . $larr[0]['r_name'] . "<br>\n";
					
					// Benutzerliste ausgeben
					$text .= user_liste($larr, 0);
				
					if ($rows > 15) {
						$text .= "<a href=\"$linkuser\"><span class=\"fa fa-refresh icon16\"></span>" . $t['sonst19'] . "</a>";
						if (!isset($beichtstuhl) || !$beichtstuhl) {
							$text .= " | <a href=\"$linksmilies\"><span class=\"fa fa-smile-o icon16\"></span>" . $t['sonst20'] . "</a>";
						}
						$text .= "</center>\n";
					}
					
					zeige_tabelle_volle_breite($box, $text);
				}
				
			}
			
	}
	
} else {
	echo "<p style=\"text-align: center;\">$t[sonst15]</p>\n";
}
?>
</body>
</html>