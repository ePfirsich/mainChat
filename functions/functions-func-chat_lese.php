<?php

function chat_lese($o_id, $raum, $u_id, $sysmsg, $ignore, $back, $nur_privat = FALSE, $nur_privat_user = "") {
	// Gibt Text gefiltert aus
	// $raum = ID des aktuellen Raums
	// $u_id = ID des aktuellen Benutzers
	
	global $user_farbe, $letzte_id, $chat, $system_farbe, $t, $chat_status_klein, $admin, $u_layout_chat_darstellung;
	global $u_nick, $u_level, $u_smilies, $u_systemmeldungen;
	global $show_spruch_owner, $id, $o_dicecheck, $cssDeklarationen;
	global $user_nick, $mysqli_link;
	
	$o_id = intval($o_id);
	
	// Workaround, falls Benutzer in Community ist
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
		#echo "nur_privat_user ist gesetzt! $nur_privat_user | $u_id";
		$txt = mysqli_real_escape_string($mysqli_link, "<b>$u_nick flüstert an " . $user_nick . ":</b>");
		$len = strlen($txt);
		#print $txt;
		$qquery .= " AND (c_an_user = '$u_id' and c_von_user_id != '0' and ( (c_von_user_id = '$u_id' and left(c_text,$len) = '$txt') or c_von_user_id = '$nur_privat_user') )";
		#print htmlentities($qquery);
	}
	if ($back == 1) {
		// o_chat_id lesen
		$query = "SELECT HIGH_PRIORITY o_chat_id FROM online WHERE o_id=$o_id";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$o_chat_id = mysqli_result($result, 0, "o_chat_id");
		} else {
			$o_chat_id = 0;
		}
		;
		mysqli_free_result($result);
		
		// Nachrichten ab o_chat_id (Merker) in Tabelle online ausgeben
		// Nur Nachrichten im aktuellen Raum anzeigen, außer Typ P oder S und an Benutzer adressiert
		$query = "SELECT c_id FROM chat WHERE c_raum='$raum' AND c_id >= $o_chat_id" . $qquery;
		
		unset($rows);
		$result = mysqli_query($mysqli_link, $query);
		if ($result) {
			while ($row = mysqli_fetch_row($result)) {
				$rows[] = $row[0];
			}
		}
		mysqli_free_result($result);
		
		$query = "SELECT c_id FROM chat WHERE c_typ IN ('P','S') AND c_an_user=$u_id AND c_id >= $o_chat_id" . $qquery;
		$result = mysqli_query($mysqli_link, $query);
		if ($result) {
			while ($row = mysqli_fetch_row($result)) {
				$rows[] = $row[0];
			}
		}
		mysqli_free_result($result);
		if (isset($rows) && is_array($rows))
			sort($rows);
		
	} else if ($back > 1) {
		// o_chat_id lesen (nicht bei Admins)
		// Admins dürfen alle Nachrichten sehen
		if (!$admin) {
			$query = "SELECT HIGH_PRIORITY o_chat_id FROM online WHERE o_id=$o_id";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				$o_chat_id = mysqli_result($result, 0, "o_chat_id");
			} else {
				$o_chat_id = 0;
			}
			;
			mysqli_free_result($result);
		} else {
			$o_chat_id = 0;
		}
		
		// $back-Zeilen in Tabelle online ausgeben, höchstens aber ab o_chat_id
		// Nur Nachrichten im aktuellen Raum anzeigen, außer Typ P oder S und an Benutzer adressiert
		$query = "SELECT c_id FROM chat WHERE c_raum='$raum' AND c_id >= $o_chat_id" . $qquery;
		
		unset($rows);
		$result = mysqli_query($mysqli_link, $query);
		if ($result) {
			while ($row = mysqli_fetch_row($result)) {
				$rows[] = $row[0];
			}
		}
		mysqli_free_result($result);
		$query = "SELECT c_id FROM chat WHERE c_typ IN ('P','S') AND c_an_user=$u_id AND c_id >= $o_chat_id" . $qquery;
		
		$result = mysqli_query($mysqli_link, $query);
		if ($result) {
			while ($row = mysqli_fetch_row($result)) {
				$rows[] = $row[0];
			}
		}
		
		mysqli_free_result($result);
		if (isset($rows) && is_array($rows))
			sort($rows);
		
		// Erste Zeile ausrechnen, ab der Nachrichten ausgegeben werden
		// Chat-Zeilen vor $o_chat_id löschen
		if (isset($rows)) {
			$zeilen = count($rows);
		} else {
			$zeilen = 0;
		}
		if ($zeilen > $back) {
			$o_chat_id = $rows[($zeilen - intval($back))];
			foreach ($rows as $key => $value) {
				if ($value < $o_chat_id) {
					unset($rows[$key]);
				}
			}
		}
	} else {
		// Die letzten Nachrichten seit $letzte_id ausgeben
		// Nur Nachrichten im aktuellen Raum anzeigen, außer Typ P oder S und an Benutzer adressiert
		$query = "SELECT c_id FROM chat WHERE c_raum=$raum AND c_id > $letzte_id" . $qquery;
		
		unset($rows);
		$result = mysqli_query($mysqli_link, $query);
		if ($result) {
			while ($row = mysqli_fetch_row($result)) {
				$rows[] = $row[0];
			}
		}
		mysqli_free_result($result);
		
		$query = "SELECT c_id FROM chat WHERE c_typ IN ('P','S') AND c_an_user=$u_id AND c_id > $letzte_id" . $qquery;
		
		$result = mysqli_query($mysqli_link, $query);
		if ($result) {
			while ($row = mysqli_fetch_row($result)) {
				$rows[] = $row[0];
			}
		}
		mysqli_free_result($result);
		if (isset($rows) && is_array($rows))
			sort($rows);
		
	}
	
	// + für regulären Ausdruck filtern
	$nick = str_replace("+", "\\+", $u_nick);
	
	// Query aus Array erzeugen und die Chatzeilen lesen
	if (isset($rows) && is_array($rows)) {
		$query = "SELECT * FROM chat WHERE c_id IN (" . implode(",", $rows) . ") ORDER BY c_id";
		$result = mysqli_query($mysqli_link, $query);
	} else {
		unset($result);
	}
	
	// Nachrichten zeilenweise ausgeben
	if (isset($result) && $result) {
		
		$text_weitergabe = "";
		while ($row = mysqli_fetch_object($result)) {
			
			// Falls ID ignoriert werden soll -> Ausgabe überspringen
			// Falls noch kein Text ausgegeben wurde und es eine Zeile in 
			// der Mitte oder am Ende einer Serie ist -> Ausgabe überspringen
			
			// Systemnachrichten, die <<< oder >>> an Stelle 4-16 enthalten herausfiltern
			$ausgeben = true;
			if ($u_systemmeldungen == "N") {
				if (($row->c_typ == "S") && (substr($row->c_text, 3, 12) == "&gt;&gt;&gt;" || substr($row->c_text, 3, 12) == "&lt;&lt;&lt;")) {
					$ausgeben = false;
				}
			}
			
			// Die Ignorierten Benutzer rausfiltern
			if (isset($ignore[$row->c_von_user_id]) && $ignore[$row->c_von_user_id]) {
				$ausgeben = false;
			}
			
			if ($ausgeben && ($text_ausgegeben || $row->c_br == "normal" || $row->c_br == "erste")) {
				// Letzte ID merken
				$letzte_id = $row->c_id;
				
				// Benutzerfarbe setzen
				if (strlen($row->c_farbe) == 0) {
					$row->c_farbe = "#" . $user_farbe;
				} else {
					$row->c_farbe = "#" . $row->c_farbe;
				}
				
				// Student: 29.08.07 - Problem ML BGSLH
				// Wenn das 255. Zeichen ein leerzeichen
				// ist, dann wird es in der Datenbank nicht
				// gespeichert, da varchar feld
				// hier wird es reingehängt, wenn es in sequenz am
				// anfang oder in der mitte, und der text nur 254
				// zeichen breit ist
				if ((strlen($row->c_text) == 254) && (($row->c_br == "erste") || ($row->c_br == "mitte"))) {
					$row->c_text .= ' ';
				}
				
				// Text filtern
				$c_text = $row->c_text;
				$c_text = $text_weitergabe . $c_text;
				$text_weitergabe = "";
				
				// Merken, dass Text ausgegeben wurde
				$text_ausgegeben = TRUE;
				
				// Smilies ausgeben oder unterdrücken
				if ($u_smilies == "0") {
					$c_text = str_replace("<smil ", "<small>&lt;SMILIE&gt;</small><!--", $c_text);
					$c_text = str_replace(" smil>", "-->", $c_text);
				} else {
					$c_text = str_replace("<smil ", "<img src=\"images/smilies/$cssDeklarationen/", $c_text);
					$c_text = str_replace(" smil>", "\">", $c_text);
				}
				
				// bugfix: gegen große Is... wg. verwechslung mit kleinen Ls ...
				// $row->c_von_user=str_replace("I","i",$row->c_von_user);
				
				if ($chat_status_klein) {
					$sm1 = "<small>";
					$sm2 = "</small>";
				} else {
					$sm1 = "";
					$sm2 = "";
				}
				
				// In Zeilen mit c_br=letzte das Zeilenende/-umbruch ergänzen
				if ($row->c_br == "erste" || $row->c_br == "mitte") {
					$br = "";
				} else {
					$br = "<br>\n";
				}
				;
				
				// im Text die Session-IDs in den Platzhalter <ID> einfügen
				if ($id)
					$c_text = str_replace("<ID>", $id, $c_text);
				
				// alternativ, falls am ende der Zeile, und das "<ID>" auf 2 Zeilen verteilt wird
				if (($id) && (($row->c_br == "erste") || ($row->c_br == "mitte"))) {
					if (substr($c_text, -3) == '<ID') {
						$text_weitergabe = substr($c_text, -3);
						$c_text = substr($c_text, 0, -3);
					}
					
					if (substr($c_text, -2) == '<I') {
						$text_weitergabe = substr($c_text, -2);
						$c_text = substr($c_text, 0, -2);
					}
					
					if (substr($c_text, -1) == '<') {
						$text_weitergabe = substr($c_text, -1);
						$c_text = substr($c_text, 0, -1);
					}
				}
				
				// Verschienen Nachrichtenarten unterscheiden und Nachricht ausgeben
				switch ($row->c_typ) {
					
					case "S":
						if (($admin || $u_level == "A")) {
							$c_text = str_replace("<!--", "", $c_text);
							$c_text = str_replace("-->", "", $c_text);
						}
						// S: Systemnachricht
						if ($row->c_an_user) {
							// an aktuellen Benutzer
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$zanfang = $sm1 . "<span style=\"color:#$system_farbe;\" title=\"$row->c_zeit\">";
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
								$zanfang = $sm1 . "<span style=\"color:#$system_farbe;\" title=\"$row->c_zeit\"><b>$chat:</b>&nbsp;";
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "</span>" . $sm2 . $br;
							}
						}
						break;
					
					case "P":
					// P: Privatnachricht an einen Benutzer
					
					// Falls dies eine Folgezeile ist, Von-Text unterdrücken
					
						// Darstellung der Nachrichten im Chat
						if ($u_layout_chat_darstellung == '2') {
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$temp_von_user = str_replace("<ID>", $id, $row->c_von_user);
								$zanfang = "<span class=\"nachrichten_privat\" title=\"$row->c_zeit\"><b>". $temp_von_user . "&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg " . $temp_von_user . " '); return(false)\">$t[chat_lese1]</a>):</b> ";
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "</span>" . $br;
							}
						} else {
							if (strlen($row->c_von_user) != 0) {
								if (!$erste_zeile) {
									$zanfang = "";
								} else {
									$temp_von_user = str_replace("<ID>", $id, $row->c_von_user);
									$zanfang = "<span style=\"color:" . $row->c_farbe . ";\" title=\"$row->c_zeit\"><b>". $temp_von_user . "&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg " . $temp_von_user . " '); return(false)\">$t[chat_lese1]</a>):</b> ";
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
									$temp_von_user = str_replace("<ID>", $id, $row->c_von_user);
									$zanfang = $sm1 . "<span style=\"color:#$system_farbe;\" title=\"$row->c_zeit\"><b>" . $temp_von_user . "&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg " . $temp_von_user . " '); return(false)\">$t[chat_lese1]</a>):</b> ";
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
						
						if ($row->c_von_user_id != 0) {
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
								$zanfang = "<span title=\"$row->c_zeit\"";
								if ($diceRoll) {
									$zanfang .= " style=\"border-left: .7em solid ";
									if ($validDiceRoll) {
										$zanfang .= "lime";
									} else {
										$zanfang .= "red";
									}
									$zanfang .= ";\"";
								}
								$zanfang .= "><i><span style=\"color:$row->c_farbe;\">&lt;";
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
								$zanfang = $sm1 . "<span style=\"color:#$system_farbe;\" title=\"$row->c_zeit\"><i>&lt;";
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
						if ($u_layout_chat_darstellung == '2') {
							if (!$erste_zeile) {
								$zanfang = "";
							} else {
								$temp_von_user = str_replace("<ID>", $id, $row->c_von_user);
								
								// Anfang des Avatars
								// Sollen Avatare angezeigt werden?
								$query3 = "SELECT * FROM user WHERE u_nick = '$u_nick'";
								$result3 = mysqli_query($mysqli_link, $query3);
								if ($result3 && mysqli_num_rows($result3) == 1) {
									$row3 = mysqli_fetch_object($result3);
									$u_avatare_anzeigen = $row3->u_avatare_anzeigen;
								}
								
								// User-ID holen
								$query2 = "SELECT * FROM user WHERE u_nick = '$temp_von_user'";
								$result2 = mysqli_query($mysqli_link, $query2);
								if ($result2 && mysqli_num_rows($result2) == 1) {
									$row2 = mysqli_fetch_object($result2);
									$uu_id = $row2->u_id;
								}
								
								// Geschlecht holen
								$query1 = "SELECT * FROM userinfo WHERE ui_userid = '$uu_id'";
								$result1 = mysqli_query($mysqli_link, $query1);
								if ($result1 && mysqli_num_rows($result1) == 1) {
									$row1 = mysqli_fetch_object($result1);
									$ui_gen = $row1->ui_geschlecht;
								} else {
									$ui_gen = 'leer';
								}
								
								if($result3 && mysqli_num_rows($result3) == 1) {
									//Alle Avatare Ja/Nein Eigene Variable entscheidet.
									if($u_avatare_anzeigen == 1) {
										// Bildinfos lesen und in Array speichern
										$queryAvatar = "SELECT b_name,b_height,b_width,b_mime FROM bild WHERE b_name = 'avatar' AND b_user=$uu_id";
										$resultAvatar = mysqli_query($mysqli_link, $queryAvatar);
										unset($bilder);
										if ($resultAvatar && mysqli_num_rows($resultAvatar) > 0) {
											while ($rowAvatar = mysqli_fetch_object($resultAvatar)) {
												$bilder[$rowAvatar->b_name]['b_mime'] = $rowAvatar->b_mime;
												$bilder[$rowAvatar->b_name]['b_width'] = $rowAvatar->b_width;
												$bilder[$rowAvatar->b_name]['b_height'] = $rowAvatar->b_height;
											}
										}
										mysqli_free_result($result2);
										
										if (!isset($bilder)) {
											if ($ui_gen[0] == "m") { // Männlicher Standard-Avatar
												$ava = '<img src="./images/avatars/no_avatar_m.jpg" style="width:25px; height:25px;" alt="" /> ';
											} else if ($ui_gen[0] == "w") { // Weiblicher Standard-Avatar
												$ava = '<img src="./images/avatars/no_avatar_w.jpg" style="width:25px; height:25px;" alt="" /> ';
											} else { // Neutraler Standard-Avatar
												$ava = '<img src="./images/avatars/no_avatar_es.jpg" style="width:25px; height:25px;" alt="" /> ';
											}
										} else {
											$ava = avatar_editieren_anzeigen($uu_id, $temp_von_user, "avatar", $bilder) ." ";
										}
									} else {
										$ava = "";
									}
								} else {
									$ava = '<img src="./avatars/no_avatar_es.jpg" style="width:25px; height:25px;" alt=""> ';
								}
								
								if($u_avatare_anzeigen == 0) {
									$ava = "";
								}
								// Ende des Avatars
								
								if($row->c_an_user == $u_id) {
									$zanfang = $ava. "<span class=\"nachrichten_privat\" title=\"$row->c_zeit\">" . "<b>" . $temp_von_user . ":</b> ";
								} else {
									$zanfang = $ava. "<span class=\"nachrichten_oeffentlich\" title=\"$row->c_zeit\">" . "<b>" . $temp_von_user . ":</b> ";
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
								$temp_von_user = str_replace("<ID>", $id, $row->c_von_user);
								
								// Anfang des Avatars
								// Sollen Avatare angezeigt werden?
								$query3 = "SELECT * FROM user WHERE u_nick = '$u_nick'";
								$result3 = mysqli_query($mysqli_link, $query3);
								if ($result3 && mysqli_num_rows($result3) == 1) {
									$row3 = mysqli_fetch_object($result3);
									$u_avatare_anzeigen = $row3->u_avatare_anzeigen;
								}
								
								// User-ID holen
								$query2 = "SELECT * FROM user WHERE u_nick = '$temp_von_user'";
								$result2 = mysqli_query($mysqli_link, $query2);
								if ($result2 && mysqli_num_rows($result2) == 1) {
									$row2 = mysqli_fetch_object($result2);
									$uu_id = $row2->u_id;
								}
								
								// Geschlecht holen
								$query1 = "SELECT * FROM userinfo WHERE ui_userid = '$uu_id'";
								$result1 = mysqli_query($mysqli_link, $query1);
								if ($result1 && mysqli_num_rows($result1) == 1) {
									$row1 = mysqli_fetch_object($result1);
									$ui_gen = $row1->ui_geschlecht;
								} else {
									$ui_gen = 'leer';
								}
								
								if($result3 && mysqli_num_rows($result3) == 1) {
									//Alle Avatare Ja/Nein Eigene Variable entscheidet.
									if($u_avatare_anzeigen == 1) {
										// Bildinfos lesen und in Array speichern
										$queryAvatar = "SELECT b_name,b_height,b_width,b_mime FROM bild WHERE b_name = 'avatar' AND b_user=$uu_id";
										$resultAvatar = mysqli_query($mysqli_link, $queryAvatar);
										unset($bilder);
										if ($resultAvatar && mysqli_num_rows($resultAvatar) > 0) {
											while ($rowAvatar = mysqli_fetch_object($resultAvatar)) {
												$bilder[$rowAvatar->b_name]['b_mime'] = $rowAvatar->b_mime;
												$bilder[$rowAvatar->b_name]['b_width'] = $rowAvatar->b_width;
												$bilder[$rowAvatar->b_name]['b_height'] = $rowAvatar->b_height;
											}
										}
										mysqli_free_result($result2);
										
										if (!isset($bilder)) {
											if ($ui_gen[0] == "m") { // Männlicher Standard-Avatar
												$ava = '<img src="./images/avatars/no_avatar_m.jpg" style="width:25px; height:25px;" alt="" /> ';
											} else if ($ui_gen[0] == "w") { // Weiblicher Standard-Avatar
												$ava = '<img src="./images/avatars/no_avatar_w.jpg" style="width:25px; height:25px;" alt="" /> ';
											} else { // Neutraler Standard-Avatar
												$ava = '<img src="./images/avatars/no_avatar_es.jpg" style="width:25px; height:25px;" alt="" /> ';
											}
										} else {
											$ava = avatar_editieren_anzeigen($uu_id, $temp_von_user, "avatar", $bilder) ." ";
										}
									} else {
										$ava = "";
									}
								} else {
									$ava = '<img src="./avatars/no_avatar_es.jpg" style="width:25px; height:25px;" alt=""> ';
								}
								
								if($u_avatare_anzeigen == 0) {
									$ava = "";
								}
								// Ende des Avatars
								
								$zanfang = $ava . "<span style=\"color:" . $row->c_farbe . ";\" title=\"$row->c_zeit\">" . "<b>" . $temp_von_user . ":</b> ";
							}
							if ($br == "") {
								$zende = "";
							} else {
								$zende = "</span>" . $br;
							}
						}
				}
				// Chatzeile ausgeben
				echo $zanfang . $c_text . $zende;
				
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
	
	if (isset($result))
		mysqli_free_result($result);
	
	flush();
	return ($text_ausgegeben);
	
}

?>