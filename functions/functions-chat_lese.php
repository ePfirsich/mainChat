<?php

function chat_lese($o_id, $raum, $user_id, $sysmsg, $ignore, $anzahl_der_zeilen, $benutzerdaten, $nur_privat = FALSE, $nur_privat_user = "") {
	// Gibt Text gefiltert aus
	// $raum = ID des aktuellen Raums
	// $user_id = ID des aktuellen Benutzers
	
	global $user_farbe, $letzte_id, $chat, $system_farbe, $lang, $chat_status_klein, $admin;
	global $u_nick, $u_level, $show_spruch_owner, $o_dicecheck, $user_nick;
	
	$o_id = intval($o_id);
	
	// Workaround, falls Benutzer im Forum ist
	if (!$raum) {
		$raum = "-1";
	}
	
	// Voreinstellung
	$text_ausgegeben = FALSE;
	$erste_zeile = TRUE;
	$br = "<br>\n";
	$qquery = "";
	
	// Optional keine Systemnachrichten
	if (!$sysmsg) {
		$qquery .= " AND c_typ!='S'";
	}
	
	// Optional keine öffentlichen oder versteckten Nachrichten
	if ($nur_privat) {
		$qquery .= " AND c_typ!='H' AND c_typ!='N'";
	}
	
	if ($nur_privat_user) {
		$txt = "<b>$u_nick flüstert an " . $user_nick . ":</b>";
		$len = strlen($txt);
		$qquery .= " AND (`c_an_user` = '$user_id' AND `c_von_user_id` != '0' AND ( (`c_von_user_id` = '$user_id' AND LEFT(`c_text`,$len) = '$txt') OR `c_von_user_id` = '$nur_privat_user') )";
	}
	if ($anzahl_der_zeilen == 1) {
		// ID des Benutzers (o_chat_zeilen) auslesen
		$o_chat_zeilen = 0;
		
		// Nachrichten ab o_chat_zeilen (Merker) in Tabelle online ausgeben
		// Nur Nachrichten im aktuellen Raum anzeigen, außer Typ P oder S und an Benutzer adressiert
		$query = pdoQuery("SELECT `c_id` FROM `chat` WHERE `c_raum` = :c_raum AND `c_id` >= :c_id" . $qquery, [':c_raum'=>$raum, ':c_id'=>$o_chat_zeilen]);
		
		
		unset($rows);
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$rows[] = $row['c_id'];
			}
		}
		
		$query = pdoQuery("SELECT `c_id` FROM `chat` WHERE `c_typ` IN ('P','S') AND `c_an_user` = :c_an_user AND `c_id` >= :c_id" . $qquery, [':c_an_user'=>$user_id, ':c_id'=>$o_chat_zeilen]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$rows[] = $row['c_id'];
			}
		}
		if (isset($rows) && is_array($rows)) {
			sort($rows);
		}
	} else if ($anzahl_der_zeilen > 1) {
		// o_chat_zeilen lesen (nicht bei Admins)
		// Admins dürfen alle Nachrichten sehen
		$o_chat_zeilen = 0;
		
		// $anzahl_der_zeilen-Zeilen in Tabelle online ausgeben, höchstens aber ab o_chat_zeilen
		// Nur Nachrichten im aktuellen Raum anzeigen, außer Typ P oder S und an Benutzer adressiert
		$query = pdoQuery("SELECT `c_id` FROM `chat` WHERE `c_raum` = :c_raum AND `c_id` >= :c_id" . $qquery, [':c_raum'=>$raum, ':c_id'=>$o_chat_zeilen]);
		
		$resultCount = $query->rowCount();
		unset($rows);
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$rows[] = $row['c_id'];
			}
		}
		
		$query = pdoQuery("SELECT `c_id` FROM `chat` WHERE `c_typ` IN ('P','S') AND `c_an_user` = :c_an_user AND `c_id` >= :c_id" . $qquery, [':c_an_user'=>$user_id, ':c_id'=>$o_chat_zeilen]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$rows[] = $row['c_id'];
			}
		}
		
		if (isset($rows) && is_array($rows)) {
			sort($rows);
		}
		
		// Erste Zeile ausrechnen, ab der Nachrichten ausgegeben werden
		// Chat-Zeilen vor $o_chat_zeilen löschen
		if (isset($rows)) {
			$zeilen = count($rows);
		} else {
			$zeilen = 0;
		}
		if ($zeilen > $anzahl_der_zeilen) {
			$o_chat_zeilen = $rows[($zeilen - intval($anzahl_der_zeilen))];
			foreach ($rows as $key => $value) {
				if ($value < $o_chat_zeilen) {
					unset($rows[$key]);
				}
			}
		}
	} else {
		// Die letzten Nachrichten seit $letzte_id ausgeben
		// Nur Nachrichten im aktuellen Raum anzeigen, außer Typ P oder S und an Benutzer adressiert
		$query = pdoQuery("SELECT `c_id` FROM `chat` WHERE `c_raum` = :c_raum AND `c_id` > :c_id" . $qquery, [':c_raum'=>$raum, ':c_id'=>$letzte_id]);
		
		$resultCount = $query->rowCount();
		unset($rows);
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$rows[] = $row['c_id'];
			}
		}
		
		$query = pdoQuery("SELECT `c_id` FROM `chat` WHERE `c_typ` IN ('P','S') AND `c_an_user` = :c_an_user AND `c_id` > :c_id" . $qquery, [':c_an_user'=>$user_id, ':c_id'=>$letzte_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$rows[] = $row['c_id'];
			}
		}
		if (isset($rows) && is_array($rows))  {
			sort($rows);
		}
		
	}
	
	// + für regulären Ausdruck filtern
	$nick = str_replace("+", "\\+", $u_nick);
	
	// Query aus Array erzeugen und die Chatzeilen lesen
	if (isset($rows) && is_array($rows)) {
		$query = pdoQuery("SELECT * FROM `chat` WHERE c_id IN (" . implode(",", $rows) . ") ORDER BY `c_id`", []);
		
		$resultCount = $query->rowCount();
	} else {
		unset($result);
	}
	
	// Nachrichten zeilenweise ausgeben
	if (isset($resultCount) && $resultCount > 0) {
		$text_weitergabe = "";
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			// Falls ID ignoriert werden soll -> Ausgabe überspringen
			// Falls noch kein Text ausgegeben wurde und es eine Zeile in 
			// der Mitte oder am Ende einer Serie ist -> Ausgabe überspringen
			
			// Systemnachrichten, die <<< oder >>> an Stelle 4-16 enthalten herausfiltern
			$ausgeben = true;
			if ($benutzerdaten['u_systemmeldungen'] == "0") {
				if (($row['c_typ'] == "S") && (substr($row['c_text'], 3, 12) == "&gt;&gt;&gt;" || substr($row['c_text'], 3, 12) == "&lt;&lt;&lt;")) {
					$ausgeben = false;
				}
			}
			
			// Die Ignorierten Benutzer rausfiltern
			if (isset($ignore[$row['c_von_user_id']]) && $ignore[$row['c_von_user_id']]) {
				$ausgeben = false;
			}
			
			if ($ausgeben) {
				// Letzte ID merken
				$letzte_id = $row['c_id'];
				
				// Benutzerfarbe setzen
				if (strlen($row['c_farbe']) == 0) {
					$row['c_farbe'] = "#" . $user_farbe;
				} else {
					$row['c_farbe'] = "#" . $row['c_farbe'];
				}
				
				// Text filtern
				$c_text = $row['c_text'];
				$c_text = $text_weitergabe . $c_text;
				$text_weitergabe = "";
				
				// Merken, dass Text ausgegeben wurde
				$text_ausgegeben = TRUE;
				
				// Smilies ausgeben oder unterdrücken
				if ($benutzerdaten['u_smilies'] == "0") {
					$c_text = str_replace("<smil ", "<small>&lt;SMILIE&gt;</small><!--", $c_text);
					$c_text = str_replace(" smil>", "-->", $c_text);
				} else {
					$c_text = str_replace("<smil ", "<img src=\"images/smilies/style-$benutzerdaten[u_layout_farbe]/", $c_text);
					$c_text = str_replace(" smil>", "\">", $c_text);
				}
				
				// bugfix: gegen große Is... wg. verwechslung mit kleinen Ls ...
				if ($chat_status_klein) {
					$sm1 = "<small>";
					$sm2 = "</small>";
				} else {
					$sm1 = "";
					$sm2 = "";
				}
				
				// In Zeilen mit c_br=letzte das Zeilenende/-umbruch ergänzen
				$br = "<br>\n";
				
				// Verschienen Nachrichtenarten unterscheiden und Nachricht ausgeben
				switch ($row['c_typ']) {
					case "S":
						if (($admin || $u_level == "A")) {
							$c_text = str_replace("<!--", "", $c_text);
							$c_text = str_replace("-->", "", $c_text);
						}
						// S: Systemnachricht
						if ($row['c_an_user']) {
							// an aktuellen Benutzer
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$zanfang = $sm1 . "<span style=\"color:#$system_farbe;\" title=\"$row[c_zeit]\">";
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "</span>" . $sm2 . $br;
							}
						} else {
							// an alle Benutzer
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$zanfang = $sm1 . "<span style=\"color:#$system_farbe;\" title=\"$row[c_zeit]\"><b>$chat:</b>&nbsp;";
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "</span>" . $sm2 . $br;
							}
							reset_system("userliste_interaktiv");
						}
						break;
					
					case "P":
					// P: Privatnachricht an einen Benutzer
					
					// Falls dies eine Folgezeile ist, Von-Text unterdrücken
					
						// Darstellung der Nachrichten im Chat
						if ($benutzerdaten['u_layout_chat_darstellung'] == '0') {
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$temp_von_user = $row['c_von_user'];
								$zanfang = "<span class=\"nachrichten_privat\" title=\"$row[c_zeit]\"><b>". $temp_von_user . "&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg " . $temp_von_user . " '); return(false)\">$lang[chat_lese1]</a>):</b> ";
								reset_system("userliste");
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "</span>" . $br;
							}
						} else {
							if (strlen($row['c_von_user']) != 0) {
								if (!$erste_zeile) {
									$zanfang = "";
								} else {
									$temp_von_user = $row['c_von_user'];
									$zanfang = "<span style=\"color:" . $row['c_farbe'] . ";\" title=\"$row[c_zeit]\"><b>". $temp_von_user . "&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg " . $temp_von_user . " '); return(false)\">$lang[chat_lese1]</a>):</b> ";
									reset_system("userliste");
								}
								if ($br == "") {
									$zende = "";
								} else {
									$zende = "</span>" . $br;
								}
							} else {
								if (!$erste_zeile) {
									$zanfang = "";
								} else {
									$temp_von_user = $row['c_von_user'];
									$zanfang = $sm1 . "<span style=\"color:#$system_farbe;\" title=\"$row[c_zeit]\"><b>" . $temp_von_user . "&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg " . $temp_von_user . " '); return(false)\">$lang[chat_lese1]</a>):</b> ";
									reset_system("userliste");
								}
								if ($br == "") {
									$zende = "";
								} else {
									$zende = "</span>" . $sm2 . $br;
								}
							}
						}
						break;
					
					case "H":
					// H: Versteckte Nachricht an alle ohne Absender
						if ($show_spruch_owner && ($admin || $u_level == "A")) {
							$c_text = str_replace("<!--", "<b>", $c_text);
							$c_text = str_replace("-->", "</b>", $c_text);
						}
						
						if ($row['c_von_user_id'] != 0) {
							// prüfe auf Würfelwurf, falls dicecheck aktiviert
							$diceRoll = false;
							$validDiceRoll = false;
							if ($o_dicecheck) {
								$diceRoll = preg_match("/(\d+|einem) +(k.einen|gro..?en) +\d+.seitigen +W..?rfe./", $c_text);
								// ..? für Umlaute wegen Codierung, . für l und - wegen Betrug (I/–/—)
								if ($diceRoll) {
									$dice = preg_split("/[wW]/", $o_dicecheck);
									if ($dice[0] == "1") {
										$dice[0] = "einem";
									}
									$regex = "/^<[bB]>.+";
									$regex .= $dice[0];
									$regex .= " +(k.einen|gro..?en) +";
									$regex .= $dice[1];
									$regex .= "/";
									$validDiceRoll = preg_match($regex, $c_text);
								}
							}
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$zanfang = "<span title=\"$row[c_zeit]\"";
								if ($diceRoll) {
									$zanfang .= " style=\"border-left: .7em solid ";
									if ($validDiceRoll) {
										$zanfang .= "lime";
									} else {
										$zanfang .= "red";
									}
									$zanfang .= ";\"";
								}
								$zanfang .= "><i><span style=\"color:$row[c_farbe];\">&lt;";
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "&gt;</span></i></span>" . $br;
							}
						} else {
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$zanfang = $sm1 . "<span style=\"color:#$system_farbe;\" title=\"$row[c_zeit]\"><i>&lt;";
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "&gt;</i></span>" . $sm2 . $br;
							}
						}
						break;
					
					default:
					// N: Normal an alle mit Absender
						
						// Darstellung der Nachrichten im Chat
						if ($benutzerdaten['u_layout_chat_darstellung'] == '0') {
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$temp_von_user = $row['c_von_user'];
								
								// User-ID holen
								$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$temp_von_user]);
								
								$resultCount = $query->rowCount();
								if ($resultCount == 1) {
									$result = $query->fetch();
									$uu_id = $result['u_id'];
								}
								
								// Geschlecht holen
								// Wenn ein Gast den Chat verlässt, hat er keine ID mehr
								if($uu_id != null && $uu_id != "") {
									$query = pdoQuery("SELECT `ui_geschlecht` FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$uu_id]);
									
									$resultCount = $query->rowCount();
									if ($resultCount == 1) {
										$result = $query->fetch();
										$ui_gen = $result['ui_geschlecht'];
									} else {
										$ui_gen = '0';
									}
								} else {
									$ui_gen = '0';
								}
								
								//Alle Avatare Ja/Nein Eigene Variable entscheidet.
								if($benutzerdaten['u_avatare_anzeigen'] == 1) {
									// Avatar
									$ava = avatar_anzeigen($uu_id, $temp_von_user, "chat", $ui_gen);
									
									// Leerzeichen nach dem Avatar anzeigen
									$ava .= ' ';
								} else {
									$ava = "";
								}
								
								if($benutzerdaten['u_avatare_anzeigen'] == 0) {
									$ava = "";
								}
								// Ende des Avatars
								
								if($row['c_an_user'] == $user_id) {
									$zanfang = $ava. "<span class=\"nachrichten_privat\" title=\"$row[c_zeit]\">" . "<b>" . $temp_von_user . ":</b> ";
								} else {
									$zanfang = $ava. "<span class=\"nachrichten_oeffentlich\" title=\"$row[c_zeit]\">" . "<b>" . $temp_von_user . ":</b> ";
								}
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "</span>" . $br;
							}
						} else {
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$temp_von_user = $row['c_von_user'];
								
								// User-ID holen
								$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$temp_von_user]);
								
								$resultCount = $query->rowCount();
								if ($resultCount == 1) {
									$result = $query->fetch();
									$uu_id = $result['u_id'];
								}
								
								// Geschlecht holen
								// Wenn ein Gast den Chat verlässt, hat er keine ID mehr
								if($uu_id != null && $uu_id != "") {
									$query = pdoQuery("SELECT `ui_geschlecht` FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$uu_id]);
									
									$resultCount = $query->rowCount();
									if ($resultCount == 1) {
										$result = $query->fetch();
										$ui_gen = $result['ui_geschlecht'];
									} else {
										$ui_gen = '0';
									}
								} else {
									$ui_gen = '0';
								}
								
								//Alle Avatare Ja/Nein Eigene Variable entscheidet.
								if($benutzerdaten['u_avatare_anzeigen'] == 1) {
									// Avatar
									$ava = avatar_anzeigen($uu_id, $temp_von_user, "chat", $ui_gen);
									
									// Leerzeichen nach dem Avatar anzeigen
									$ava .= ' ';
								} else {
									$ava = "";
								}
								
								if($benutzerdaten['u_avatare_anzeigen'] == 0) {
									$ava = "";
								}
								// Ende des Avatars
								
								$zanfang = $ava . "<span style=\"color:" . $row['c_farbe'] . ";\" title=\"$row[c_zeit]\">" . "<b>" . $temp_von_user . ":</b> ";
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "</span>" . $br;
							}
						}
				}
				// Chatzeile ausgeben
				echo $zanfang . html_entity_decode($c_text) . $zende;
				
				// Ist aktuelle Zeile die erste Zeile oder eine Folgezeile einer Serie ?
				// Eine Serie steht immer am Stück in der DB (schreiben mit Lock Table)
				// Falls br gesetzt ist, ist nächste Zeile eine neue Zeile (erste zeile einer Serie)
				if ($br) {
					$erste_zeile = TRUE;
				} else {
					$erste_zeile = FALSE;
				}
				
			}
		}
	}
	
	flush();
	return $text_ausgegeben;
}

?>