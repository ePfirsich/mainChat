<?php

require("functions.php");
require("functions-msg.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

$title = $body_titel . ' - Einstellungen';
zeige_header_anfang($title, 'mini');
?>
<script>
		window.focus()
	function win_reload(file,win_name) {
			win_name.location.href=file;
}
	function opener_reload(file,frame_number) {
			opener.parent.frames[frame_number].location.href=file;
}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$input_breite = 32;
$passwort_breite = 15;

// Login ok?
if (strlen($u_id) != 0) {
	// Fenstername
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);
	
	// Ggf Farbe aktualisieren
	if (isset($farbe) && strlen($farbe) > 0) {
		// In Userdatenbank schreiben
		$f['u_farbe'] = $farbe;
		unset($f['u_id']);
		unset($f['u_level']);
		unset($f['u_name']);
		unset($f['u_email']);
		unset($f['u_adminemail']);
		unset($f['u_nick']);
		unset($f['u_auth']);
		unset($f['u_passwort']);
		unset($f['u_smilie']);
		unset($f['avatar_status']);
		unset($f['u_systemmeldungen']);
		unset($f['u_punkte_anzeigen']);
		unset($f['u_signatur']);
		unset($f['u_eintritt']);
		unset($f['u_austritt']);
		schreibe_db("user", $f, $u_id, "u_id");
		
		if ($o_js && $o_who == 0) {
			echo "<SCRIPT>"
				. "opener_reload('eingabe.php?id=$id','3')"
				. "</SCRIPT>\n";
		}
		unset($f['u_farbe']);
	}
	
	// Benutzer darf Passwort nicht ändern (optional)
	if (!$einstellungen_aendern) {
		unset($f['u_passwort']);
	}
	
	// Chat neu aufbauen, damit nach Umstellung der Chat refresht wird
	if ($o_js && $o_who == 0) {
		echo "<script>"
			. "opener_reload('chat.php?id=$id&back=$chat_back','1')"
			. "</script>\n";
		echo "<script>"
			. "opener_reload('eingabe.php?id=$id','3')"
			. "</script>\n";
	}
	
	$box = $t['menue4'];
	if ($communityfeatures && $u_level != "G") {
		$ur1 = "profil.php?id=$id&aktion=aendern";
		$url = "href=\"$ur1\" target=\"640_$fenster\" onclick=\"window.open('$ur1','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\"";
		$text = "<a $url>$t[menue7]</a>\n";
		$ur1 = "home.php?id=$id&aktion=aendern";
		$url = "href=\"$ur1\" target=\"640_$fenster\" onclick=\"window.open('$ur1','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\"";
		$text .= "| <a $url>$t[menue10]</a>\n";
		$ur1 = "freunde.php?id=$id";
		$url = "href=\"$ur1\" target=\"640_$fenster\" onclick=\"window.open('$ur1','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\"";
		$text .= "| <a $url>$t[menue9]</a>\n";
		$ur1 = "aktion.php?id=$id";
		$url = "href=\"$ur1\" target=\"640_$fenster\" onclick=\"window.open('$ur1','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\"";
		$text .= "| <a $url>$t[menue8]</a>\n";
	}
	if (isset($text) && $text) {
		show_menue($box, $text);
	}
	
	if ($aktion == "edit2") {
		if ((strlen($f['u_name']) < 4 || strlen($f['u_name']) > 50)) {
			echo "<p><b>$t[edit2]</b></p>\n";
			$aktion = "andereadminmail";
		}
		
		if (isset($f['u_adminemail']) && (strlen($f['u_adminemail']) > 0) && (filter_var($f['u_adminemail'], FILTER_VALIDATE_EMAIL) == false) ) {
		//if (isset($f['u_adminemail']) && (strlen($f['u_adminemail']) > 0) && (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,10})", $f['u_adminemail'])) ) {
			echo "<p><b>$t[edit1]</b></p>\n";
			$aktion = "andereadminmail";
		}
		
		// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
		$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
		$result = mysqli_query($mysqli_link, $query);
		$num = mysqli_num_rows($result);
		if ($num > 1) {
			echo "<p><b>$t[edit23]</b></p>\n";
			$aktion = "andereadminmail";
		}
		
		// oder Domain ist lt. Config verboten
		if ($domaingesperrtdbase != $dbase) {
			$domaingesperrt = array();
		}
		for ($i = 0; $i < count($domaingesperrt); $i++) {
			$teststring = strtolower($f['u_adminemail']);
			if (($domaingesperrt[$i]) && (preg_match($domaingesperrt[$i], $teststring))) {
				echo "<p><b>$t[edit1]</b></p>\n";
				$aktion = "andereadminmail";
			}
		}
	}
	
	// Auswahl
	switch ($aktion) {
		
		case "andereadminmail":
			$text = '';
			$box = $t['edit20'];
			
			$text .= "<form name=\"$u_nick\" action=\"edit.php\" method=\"post\">\n"
				. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"f[u_id]\" value=\"$u_id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"edit2\">\n";
			
			
			$text .= $f1 . $t['edit21'] . "<br><br>" . $t['edit22'] . "<br><br>" . "\n" . $f2;
			
			$text .= $f1 . "<b>" . $t['user_zeige17'] . "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\"$u_name\" name=\"f[u_name]\" SIZE=$input_breite>";
			
			$query = "SELECT `user`.* " . "FROM `user` WHERE `u_id`=$u_id ";
			$result = mysqli_query($mysqli_link, $query);
			$rows = mysqli_num_rows($result);
			
			if ($rows == 1) {
				$row = mysqli_fetch_object($result);
				$u_adminemail = $row->u_adminemail;
				mysqli_free_result($result);
				
				$text .= "<br><br>" . $f1 . "<b>" . $t['user_zeige3'] . "</b><br>\n" . $f2 . "<input type=\"text\" value=\"$u_adminemail\" name=\"f[u_adminemail]\" size=$input_breite>";
			}
			$text .= '<br>';
			
			$text .= $f1 . "<br><input type=\"submit\" name=\"eingabe\" value=\"Ändern!\">" . $f2;
			$text .= "</form>\n";
			
			// Box anzeigen
			show_box_title_content($box, $text);
			
			break;
		
		case "edit2":
			if (($eingabe == "Ändern!") && ($u_id == $f['u_id'])
				&& ($einstellungen_aendern)) {
				
				$query = "SELECT `user`.* FROM `user` WHERE `u_id`=$u_id ";
				$result = mysqli_query($mysqli_link, $query);
				$rows = mysqli_num_rows($result);
				
				if ($rows == 1) {
					$row = mysqli_fetch_object($result);
					$u_adminemail = $row->u_adminemail;
					mysqli_free_result($result);
					
					unset($p);
					
					// Länge des Feldes und Format Mailadresse werden weiter oben geprüft
					$p['u_id'] = $u_id;
					$p['u_name'] = $f['u_name'];
					$p['u_adminemail'] = $f['u_adminemail'];
					$pwdneu = genpassword(8);
					$p['u_passwort'] = $pwdneu;
					
					$ok = mail($p['u_adminemail'], $t['chat_msg112'],
						str_replace("%passwort%", $p['u_passwort'],
							$t['chat_msg113']), "From: $webmaster ($chat)");
					if ($ok) {
						echo "Die Änderungen wurden gespeichert und Sie wurden ausgeloggt. Ihr neues Passwort wurde an Ihre neue E-Mailadresse gesendet.";
						echo $f1 . $t['chat_msg111'] . $f2;
						schreibe_db("user", $p, $p['u_id'], "u_id");
					} else {
						echo $f1
							. "<p><b>Fehler: Die Mail konnte nicht verschickt werden. Es wurden keine Einstellungen geändert!</b></p>"
							. $f2;
					}
					
					$user = $row->u_nick;
					$query = "SELECT o_id,o_raum,o_name FROM online WHERE o_user='$u_id' AND o_level!='C' AND o_level!='S'";
					
					$result = mysqli_query($mysqli_link, $query);
					if ($result && mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_object($result);
						verlasse_chat($f['u_id'], $row->o_name, $row->o_raum);
						logout($row->o_id, $f['u_id'], "edit->einständerung");
						mysqli_free_result($result);
					}
				}
			}
			break;
		
		case "loesche":
			if ($eingabe == "Löschen!" && $admin) {
				if ($u_id == $f['u_id']) {
					// nicht sich selbst löschen...
					echo "$t[edit16]<br>";
				} else {
					// test, ob zu löschender Admin ist...
					$query = "SELECT * FROM `user` WHERE `u_id`=$f[u_id] ";
					$result = mysqli_query($mysqli_link, $query);
					$del_level = mysqli_result($result, 0, "u_level");
					if ($del_level != "S" && $del_level != "C"
						&& $del_level != "M") {
						
						// Userdaten löschen
						echo "<p><b>"
							. str_replace("%u_nick%", $f['u_nick'],
								$t['menue5']) . "</b></p>\n";
						$query = "DELETE FROM `user` WHERE `u_id`=$f[u_id] ";
						$result = mysqli_query($mysqli_link, $query);
						
						// Ignore-Einträge löschen
						$query = "DELETE FROM iignore WHERE i_user_aktiv=$f[u_id] OR i_user_passiv=$f[u_id]";
						$result = mysqli_query($mysqli_link, $query);
						
						// Gesperrte Räume löschen
						$query = "DELETE FROM sperre WHERE s_user=$f[u_id]";
						$result = mysqli_query($mysqli_link, $query);
					} else {
						echo "<p><b>"
							. str_replace("%u_nick%", $f['u_nick'],
								$t['menue6']) . "</b></p>\n";
					}
				}
				
			} else {
				echo "<p><b>"
					. str_replace("%u_nick%", $f['u_nick'], $t['menue6'])
					. "</b></p>\n";
			}
			break;
		
		case "edit":
			if (((isset($eingabe) && $eingabe == "Ändern!")
				|| (isset($farben['u_farbe']) && $farben['u_farbe']))
				&& ($u_id == $f['u_id'] || $admin)) {
				// In Namen die unerlaubten Zeichen entfernen
				$f['u_nick'] = coreCheckName($f['u_nick'], $check_name);
				
				// Bestimmte Felder dürfen nicht geändert werden
				unset($f['u_neu']);
				unset($f['u_login']);
				unset($f['u_agb']);
				unset($f['u_ip_historie']);
				unset($f['u_punkte_gesamt']);
				unset($f['u_punkte_monat']);
				unset($f['u_punkte_jahr']);
				unset($f['u_punkte_datum_monat']);
				unset($f['u_punkte_datum_jahr']);
				unset($f['u_punkte_gruppe']);
				unset($f['u_gelesene_postings']);
				unset($f['u_lastclean']);
				unset($f['u_loginfehler']);
				unset($f['u_nick_historie']);
				unset($f['u_profil_historie']);
				unset($f['u_knebel']);
				
				// Nicht-Admin darf Einstellungen nicht ändern
				if (!$admin) {
					unset($f['u_name']);
					unset($f['u_adminemail']);
					unset($f['u_level']);
					unset($f['u_kommentar']);
				}
				
				// Gast darf Daten nicht ändern
				if ($u_level == "G") {
					unset($f['u_email']);
					unset($f['u_auth']);
					unset($passwort1);
				}
				
				$ok = 1;
				
				// Farbe aus Farb-Popup in Hidden-Feld
				if ($farben['u_farbe'])
					$f['u_farbe'] = $farben['u_farbe'];
				
				// Farbe direkt über Input-Feld
				if (substr($f['u_farbe'], 0, 1) == "#") {
					$f['u_farbe'] = substr($f['u_farbe'], 1, 6);
				}
				if (strlen($f['u_farbe']) != 6) {
					unset($f['u_farbe']);
				} else if (!preg_match("/[a-f0-9]{6}/i", $f['u_farbe'])) {
					unset($f['u_farbe']);
				}
				
				// E-Mail ok
				if (isset($f['u_email']) && (strlen($f['u_email']) > 0) && (filter_var($f['u_email'], FILTER_VALIDATE_EMAIL) == false) ) {
					echo "<p><b>$t[edit1]</b></p>\n";
					unset($f['u_email']);
					$ok = 0;
				}
				
				// Admin E-mail kontrollieren
				if (isset($f['u_adminemail']) && (strlen($f['u_adminemail']) > 0) && (filter_var($f['u_adminemail'], FILTER_VALIDATE_EMAIL) == false) ) {
					echo "<p><b>$t[edit1]</b></p>\n";
					$ok = 0;
				}
				
				// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
				$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
				$result = mysqli_query($mysqli_link, $query);
				$num = mysqli_num_rows($result);
				if ($num > 1) {
					echo "<p><b>$t[edit23]</b></p>\n";
					$aktion = "andereadminmail";
				}
				
				// Name muss 4-50 Zeichen haben
				if ($admin
					&& (strlen($f['u_name']) < 4 || strlen($f['u_name']) > 50)) {
					echo "<p><b>$t[edit2]</b></p>\n";
					unset($f['u_name']);
					$ok = 0;
				}
				
				// Nick muss 4-20 Zeichen haben
				if (!isset($keineloginbox)) {
					$keineloginbox = 0;
				}
				if (!$keineloginbox
					&& (strlen($f['u_nick']) < 4 || strlen($f['u_nick']) > 20)) {
					
					// Wenn man den Nicknamen nicht ändern darf, und man User ist, dann den Parameter
					// sicherheitshalber löschen
					if (!$einstellungen_aendern && !$admin) {
						unset($f['u_nick']);
					}
					
					// Falls er oben gelöscht wurde, den Alten aus der Datenbank holen
					if ((!$einstellungen_aendern)
						&& (strlen($f['u_nick']) == 0)) {
						// wird nicht übergeben, wenn $einstellungen_aendern==0, also aus DB laden falls $admin.
						$query = "SELECT `u_nick` FROM `user` WHERE `u_id`=$f[u_id]";
						$result = mysqli_query($mysqli_link, $query);
						if ($result) {
							$f['u_nick'] = mysqli_result($result, 0);
						}
					}
					
					// immernoch keine 4-20 Zeichen?
					// Hier müsste aus den oberen beiden Fällen der Nickname nun da sein,
					// Jetzt wird geprüft, ob man normalerweise den Nick ändern darf, und dort 4-20 
					// Zeichen eingegeben hat
					if (!$keineloginbox
						&& (strlen($f['u_nick']) < 4
							|| strlen($f['u_nick']) > 20)) {
						echo "<p><b>$t[edit3]</b></p>\n";
						unset($f['u_nick']);
						$ok = 0;
					}
				}
				
				// Homepage muss http:// oder https://enthalten
				if (isset($f['u_url']) && strlen($f['u_url']) > 0 && !preg_match("/^http:\/\//i", $f['u_url']) && !preg_match("/^https:\/\//i", $f['u_url']) ) {
					$f['u_url'] = "http://" . $f['u_url'];
				}
				
				// nur Zahlen zulassen bei den Fenstergrößen
				$size['eingabe'] = preg_replace("/[^0-9]/", "",
					(isset($size['eingabe']) ? $size['eingabe'] : ""));
				$size['interaktiv'] = preg_replace("/[^0-9]/", "",
					(isset($size['interaktiv']) ? $size['interaktiv'] : ""));
				$size['chatuserliste'] = preg_replace("/[^0-9]/", "",
					(isset($size['chatuserliste']) ? $size['chatuserliste'] : ""));
				$size['interaktivforum'] = preg_replace("/[^0-9]/", "",
					(isset($size['interaktivforum']) ? $size['interaktivforum']
						: ""));
				$size['messagesforum'] = preg_replace("/[^0-9]/", "",
					(isset($size['messagesforum']) ? $size['messagesforum'] : ""));
				
				// Gibts den User/Nicknamen schon?
				if ($f['u_nick']) {
					$query = "SELECT u_id FROM user "
						. "WHERE u_nick = '$f[u_nick]' AND u_id!=$f[u_id]";
					// echo "Debug: $query<br>";
					$result = mysqli_query($mysqli_link, $query);
					$rows = mysqli_num_rows($result);
					if ($rows != 0) {
						echo "<p><b>$t[edit7]</b></p>\n";
						unset($f['u_name']);
						unset($f['u_nick']);
						$ok = 0;
					}
				} else {
					// Nickname nicht schreiben (keine Änderung oder ungültiger Text)
					unset($f['u_nick']);
				}
				
				// Wenn noch keine 30 Sekunden Zeit seit der letzten Änderung vorbei sind, 
				// dann Nickname nicht speichern
				if (isset($f['u_nick']) && $f['u_nick']) {
					$query = "SELECT `u_nick_historie`, `u_nick` FROM `user` WHERE `u_id` = '$f[u_id]'";
					$result = mysqli_query($mysqli_link, $query);
					$xyz = mysqli_fetch_array($result);
					$nick_historie = unserialize($xyz['u_nick_historie']);
					$nick_alt = $xyz['u_nick'];
					
					if (is_array($nick_historie)) {
						reset($nick_historie);
						list($key, $value) = each($nick_historie);
						$differenz = time() - $key;
					}
					if (!isset($differenz))
						$differenz = 999;
					if ($admin)
						$differenz = 999;
					
					if ($nick_alt <> $f['u_nick']) {
						
						if ($differenz < $nickwechsel) {
							echo "<p><b>Sie dürfen Ihren Nicknamen nur alle $nickwechsel Sekunden ändern!</b></p>\n";
							unset($f['u_nick']);
						} else {
							$datum = time();
							$nick_historie_neu[$datum] = $nick_alt;
							if (is_array($nick_historie)) {
								$i = 0;
								while (($i < 3)
									&& list($datum, $nick) = each(
										$nick_historie)) {
									$nick_historie_neu[$datum] = $nick;
									$i++;
								}
							}
							$f['u_nick_historie'] = serialize(
								$nick_historie_neu);
						}
					}
				}
				
				// Level "M" nur vergeben, wenn moderation in diesem Chat erlaubt ist.
				if (isset($f['u_level']) && $f['u_level'] == "M"
					&& !$moderationsmodul) {
					unset($f['u_level']);
					echo $t['edit18'];
				}
				
				// aufpassen, wenn Admin sich selbst ändert -> keine leveländerung zulassen.
				if ($u_id == $f['u_id'] && $admin) {
					if ($u_level != $f['u_level'])
						print "$t[edit16]";
					unset($f['u_level']);
				}
				
				$query = "SELECT `u_level` FROM `user` WHERE `u_id`=" . intval($f['u_id']);
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) > 0) {
					$uu_level = mysqli_result($result, 0, "u_level");
					
					// Falls Userlevel G -> Änderung verboten
					if (isset($f['u_level']) && strlen($f['u_level']) != 0
						&& $f['u_level'] != "G" && $uu_level == "G") {
						echo $t['edit15'];
						unset($f['u_level']);
					}
					
					// uu_level = Level des Users, der geändert wird
					// u_level  = Level des Users, der ändert
					// Admin (C) darf für anderen Admin (C oder S) nicht ändern: Level, Passwort, Admin-EMail
					if ($u_id != $f['u_id'] && $u_level == "C"
						&& ($uu_level == "S" || $uu_level == "C")) {
						
						// Array Löschen
						echo $t['edit17'] . "<br>";
						$ok = 0;
						$f = array(u_id => $f['u_id']);
						unset($passwort1);
					}
					mysqli_free_result($result);
					
				} else {
					
					// Per default nichts ändern -> Array Löschen
					echo $t['edit17'] . "<br>";
					$ok = 0;
					$f = array(u_id => $f['u_id']);
					unset($passwort1);
				}
				
				// Nur Superuser darf Level S oder C vergeben
				if ($ok && isset($f['u_level'])
					&& ($f['u_level'] == "S" || $f['u_level'] == "C")
					&& $u_level != "S") {
					unset($f['u_level']);
					echo $t['edit17'] . "<br>";
				}
				
				// Ist passwort gesetzt?
				if (isset($passwort1) && strlen($passwort1) > 0) {
					if ($passwort1 != $passwort2) {
						echo "<p><b>$t[edit4]</b></p>\n";
						$ok = 0;
					} elseif (strlen($passwort1) < 4) {
						echo "<p><b>$t[edit5]</b></p>\n";
						$ok = 0;
					} else {
						// Paßwort neu eintragen
						echo "<p><b>$t[edit6]</b></p>\n";
						$f['u_passwort'] = $passwort1;
					}
				}
				
				// Fenstergrößen setzen
				if (is_array($size)) {
					$f['u_frames'] = serialize($size);
				}
				
				// Userdaten schreiben
				if ($ok) {
					if (isset($zeige_loesch) && $zeige_loesch != 1) {
						// Änderungen anzeigen
						
						$query = "SELECT o_userdata,o_userdata2,o_userdata3,o_userdata4,o_raum "
							. "FROM online " . "WHERE o_user=" . intval($f[u_id]);
						$result = mysqli_query($mysqli_link, $query);
						if ($result && mysqli_num_rows($result) == 1) {
							$row = mysqli_fetch_object($result);
							$userdata = unserialize(
								$row->o_userdata . $row->o_userdata2
									. $row->o_userdata3 . $row->o_userdata4);
							if (($f['u_name'] != $userdata['u_name'])
								AND $f['u_name'] AND $admin) {
								echo "<p><b>"
									. str_replace("%u_name%",
										htmlspecialchars($f['u_name']),
										$t['edit8']) . "</b></p>\n";
							}
							if ($f['u_nick']
								AND ($f['u_nick'] != $userdata['u_nick'])) {
								echo "<p><b>"
									. str_replace("%u_nick%", $f['u_nick'],
										$t['edit9']) . "</b></p>\n";
								global_msg($u_id, $row->o_raum,
									str_replace("%u_nick%", $f['u_nick'],
										str_replace("%row->u_nick%",
											$userdata['u_nick'], $t['edit10'])));
							}
						}
						@mysqli_free_result($result);
						echo "<p><b>$t[edit11]</b></p>\n";
						
					}
					
					$query = "SELECT `u_profil_historie` FROM `user` WHERE `u_id` = " . intval($f['u_id']);
					$result = mysqli_query($mysqli_link, $query);
					$g = mysqli_fetch_array($result);
					
					$g['u_profil_historie'] = unserialize(
						$g['u_profil_historie']);
					
					$datum = time();
					$u_profil_historie_neu[$datum] = $u_nick;
					
					if (is_array($g['u_profil_historie'])) {
						$i = 0;
						while (($i < 3)
							&& list($datum, $nick) = each(
								$g['u_profil_historie'])) {
							$u_profil_historie_neu[$datum] = $nick;
							$i++;
						}
					}
					$f['u_profil_historie'] = serialize($u_profil_historie_neu);
					$f['u_eintritt'] = isset($f['u_eintritt']) ? $f['u_eintritt'] : "";
					$f['u_austritt'] = isset($f['u_austritt']) ? $f['u_austritt'] : "";
					
					schreibe_db("user", $f, $f['u_id'], "u_id");
					
					// Hat der User den u_level = 'Z', dann lösche die Ignores, wo er der Aktive ist
					if (isset($f['u_level']) && $f['u_level'] == "Z") {
						$queryii = "SELECT u_nick,u_id from user,iignore "
							. "WHERE i_user_aktiv=" . intval($f[u_id]) . " AND u_id=i_user_passiv order by i_id";
						$resultii = @mysqli_query($mysqli_link, $queryii);
						$anzahlii = @mysqli_num_rows($resultii);
						
						if ($resultii && $anzahlii > 0) {
							for ($i = 0; $i < $anzahlii; $i++) {
								$rowii = @mysqli_fetch_object($resultii);
								ignore($o_id, $f['u_id'], $f['u_nick'],
									$rowii->u_id, $rowii->u_nick);
							}
						}
						@mysqli_free_result($resultii);
					}
					// Hat der User den u_level = 'C' oder 'S', dann lösche die Ignores, wo er der Passive ist
 else if ((isset($f['u_level']) && $f['u_level'] == "C")
						|| (isset($f['u_level']) && $f['u_level'] == "S")) {
						$queryii = "SELECT u_nick,u_id from user,iignore "
							. "WHERE i_user_passiv=" . intval($f[u_id]) . " AND u_id=i_user_aktiv order by i_id";
						$resultii = @mysqli_query($mysqli_link, $queryii);
						$anzahlii = @mysqli_num_rows($resultii);
						
						if ($resultii && $anzahlii > 0) {
							for ($i = 0; $i < $anzahlii; $i++) {
								$rowii = @mysqli_fetch_object($resultii);
								ignore($o_id, $rowii->u_id, $rowii->u_nick,
									$f['u_id'], $f['u_nick']);
							}
						}
						@mysqli_free_result($resultii);
					}
					
					// Eingabe-Frame mit Farben aktualisieren
					if ($o_js && $o_who == 0 && isset($f['u_farbe'])
						&& $f['u_farbe']) {
						echo "<SCRIPT LANGUAGE=JavaScript>"
							. "opener_reload('eingabe.php?id=$id','3')"
							. "</SCRIPT>\n";
					}
				}
				
				// Falls User auf Level "Z" gesetzt wurde -> logoff
				if (ist_online($f['u_id']) && isset($f['u_level'])
					&& $f['u_level'] == "Z") {
					// o_id und o_raum bestimmen
					$query = "SELECT o_id,o_raum FROM online "
						. "WHERE o_user=" . intval($f[u_id]);
					$result = mysqli_query($mysqli_link, $query);
					$rows = mysqli_num_rows($result);
					
					if ($rows > 0) {
						$row = mysqli_fetch_object($result);
						verlasse_chat($f['u_id'], $f['u_nick'], $row->o_raum);
						logout($row->o_id, $f['u_id'], "edit->levelZ");
						echo "<p><b>"
							. str_replace("%u_name%", htmlspecialchars($f['u_nick']),
								$t['edit12']) . "</b></p>\n";
					}
					mysqli_free_result($result);
				}
				
				// User mit ID $u_id anzeigen
				$query = "SELECT `user`.* " . "FROM `user` WHERE `u_id`=" . intval($f['u_id']);
				$result = mysqli_query($mysqli_link, $query);
				
				if ($result && mysqli_num_rows($result) == 1) {
					$row = mysqli_fetch_object($result);
					$f['u_id'] = $u_id;
					$f['u_name'] = htmlspecialchars($row->u_name);
					$f['u_nick'] = $row->u_nick;
					$f['u_id'] = $row->u_id;
					$f['u_email'] = htmlspecialchars($row->u_email);
					$f['u_adminemail'] = htmlspecialchars($row->u_adminemail);
					$f['u_url'] = htmlspecialchars($row->u_url);
					$f['u_level'] = $row->u_level;
					$f['u_farbe'] = $row->u_farbe;
					$f['u_zeilen'] = $row->u_zeilen;
					$f['u_smilie'] = $row->u_smilie;
					$f['avatar_status'] = $row->avatar_status;
					$f['u_systemmeldungen'] = $row->u_systemmeldungen;
					$f['u_eintritt'] = $row->u_eintritt;
					$f['u_austritt'] = $row->u_austritt;
					$f['u_punkte_anzeigen'] = $row->u_punkte_anzeigen;
					$f['u_signatur'] = $row->u_signatur;
					$f['u_kommentar'] = $row->u_kommentar;
					$size = unserialize($row->u_frames);
					user_edit($f, $admin, $u_level, $size);
				}
				mysqli_free_result($result);
				
				// Bei Änderungen an u_smilie, u_systemmeldungen, u_punkte_anzeigen chat-Fenster neu laden
				if (($u_smilie != $f['u_smilie']
					|| $u_systemmeldungen != $f['u_systemmeldungen']
					|| $avatar_status != $f['avatar_status']
					|| $u_punkte_anzeigen != $f['u_punkte_anzeigen'])
					&& $o_who == 0) {
					echo "<SCRIPT LANGUAGE=JavaScript>"
						. "opener_reload('chat.php?id=$id&back=$chat_back','1')"
						. "</SCRIPT>\n";
				}
				
			} elseif ((isset($eingabe) && $eingabe == "Löschen!") && $admin) {
				// User löschen
				
				// Ist User noch Online?
				if (!ist_online($f['u_id'])) {
					// Nachfrage ob sicher	
					echo "<p><b>"
						. str_replace("%u_nick%", $f['u_nick'], $t['edit13'])
						. "</b></p>\n";
					echo "<form name=\"$f[u_nick]\" action=\"edit.php\" METHOD=POST>\n"
						. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
						. "<input type=\"hidden\" name=\"f[u_id]\" value=\"$f[u_id]\">\n"
						. "<input type=\"hidden\" name=\"f[u_name]\" value=\"$f[u_name]\">\n"
						. "<input type=\"hidden\" name=\"f[u_nick]\" value=\"$f[u_nick]\">\n"
						. "<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n";
					echo $f1
						. "<input type=\"SUBMIT\" name=\"eingabe\" value=\"Löschen!\">";
					echo "&nbsp;<input type=\"SUBMIT\" name=\"eingabe\" value=\"Abbrechen\">"
						. $f2;
					echo "</form>\n";
				} else {
					echo "<p><b>"
						. str_replace("%u_nick%", $f['u_nick'], $t['edit14'])
						. "</b></p>\n";
					
					// User mit ID $u_id anzeigen
					
					$query = "SELECT `user`.* "
						. "FROM `user` WHERE u_id=" . intval($f[u_id]);
					$result = mysqli_query($mysqli_link, $query);
					$rows = mysqli_num_rows($result);
					
					if ($rows == 1) {
						$row = mysqli_fetch_object($result);
						$f['u_id'] = $u_id;
						$f['u_name'] = htmlspecialchars($row->u_name);
						$f['u_nick'] = $row->u_nick;
						$f['u_id'] = $row->u_id;
						$f['u_email'] = htmlspecialchars($row->u_email);
						$f['u_adminemail'] = htmlspecialchars($row->u_adminemail);
						$f['u_url'] = htmlspecialchars($row->u_url);
						$f['u_level'] = $row->u_level;
						$f['u_farbe'] = $row->u_farbe;
						$f['u_zeilen'] = $row->u_zeilen;
						$f['u_smilie'] = $row->u_smilie;
						$f['avatar_status'] = $row->avatar_status;
						$f['u_systemmeldungen'] = $row->u_systemmeldungen;
						$f['u_eintritt'] = $row->u_eintritt;
						$f['u_austritt'] = $row->u_austritt;
						$f['u_punkte_anzeigen'] = $row->u_punkte_anzeigen;
						$f['u_signatur'] = $row->u_signatur;
						$size = unserialize($row->u_frames);
						user_edit($f, $admin, $u_level, $size);
						mysqli_free_result($result);
					}
				}
			} elseif (isset($eingabe) && $eingabe == "Homepage löschen!"
				&& $admin) {
				if ($aktion3 == "loeschen") {
					echo "Homepage wurde gelöscht!";
					$query = "DELETE FROM userinfo WHERE ui_userid = " . intval($f[u_id]);
					mysqli_query($mysqli_link, $query);
					
					$query = "UPDATE user SET u_login = u_login, u_chathomepage = 'N' WHERE u_id = " . intval($f[u_id]);
					mysqli_query($mysqli_link, $query);
					
					$query = "DELETE FROM bild WHERE b_user = " . intval($f[u_id]);
					mysqli_query($mysqli_link, $query);
				} else {
					echo "<form name=\"edit\" action=\"edit.php\" method=\"post\">\n"
						. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
						. "<input type=\"hidden\" name=\"f[u_id]\" value=\"$f[u_id]\">\n"
						. "<input type=\"hidden\" name=\"f[u_name]\" value=\"$f[u_name]\">\n"
						. "<input type=\"hidden\" name=\"f[u_nick]\" value=\"$f[u_nick]\">\n"
						. "<input type=\"hidden\" name=\"aktion\" value=\"edit\">\n"
						. "<input type=\"hidden\" name=\"aktion3\" value=\"loeschen\">\n"
						. "<input type=\"SUBMIT\" name=\"eingabe\" value=\"Homepage löschen!\">"
						. $f2 . "</form>\n";
					
				}
			} elseif (isset($eingabe) && $eingabe == $t['chat_msg110']
				&& $admin) {
				// Admin E-Mailadresse aus DB holen
				$query = "SELECT `u_adminemail`, `u_level` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $f[u_nick]) . "'";
				$result = mysqli_query($mysqli_link, $query);
				
				$x = mysqli_fetch_array($result);
				$f['u_adminemail'] = $x['u_adminemail'];
				$pwdneu = genpassword(8);
				$f['u_passwort'] = $pwdneu;
				$uu_level = $x['u_level'];
				
				// Prüfung ob der User das überhaupt darf...
				
				if ($f['u_adminemail'] == "") {
					echo $f1
						. "<p><b>Fehler: Keine E-Mail Adresse hinterlegt!</b></p>"
						. $f2;
				} elseif ((($u_level == "C" || $u_level == "A")
					&& ($uu_level == "U" || $uu_level == "M"
						|| $uu_level == "Z")) || ($u_level == "S")) {
					
					$ok = mail($f['u_adminemail'], $t['chat_msg112'],
						str_replace("%passwort%", $f['u_passwort'],
							$t['chat_msg113']), "From: $webmaster ($chat)");
					if ($ok) {
						echo $f1 . $t['chat_msg111'] . $f2;
						schreibe_db("user", $f, $f['u_id'], "u_id");
					} else {
						echo $f1
							. "<p><b>Fehler: Die Mail konnte nicht verschickt werden. Das Passwort wurde beibehalten!</b></p>"
							. $f2;
					}
					
					$user = $f['u_nick'];
					$query = "SELECT o_id,o_raum,o_name FROM online WHERE o_user=" . intval($f[u_id]) . " AND o_level!='C' AND o_level!='S'";
					
					$result = mysqli_query($mysqli_link, $query);
					if ($result && mysqli_num_rows($result) > 0) {
						$row = mysqli_fetch_object($result);
						verlasse_chat($f['u_id'], $row->o_name, $row->o_raum);
						logout($row->o_id, $f['u_id'], "edit->pwänderung");
						mysqli_free_result($result);
					}
				} else {
					echo $f1 . "<p><b>Fehler: Aktion nicht erlaubt!</b></p>"
						. $f2;
				}
				
			} else {
				// User mit ID $u_id anzeigen
				
				if ($admin && strlen($f['u_id']) > 0) {
					// Jeden User anzeigen
					$query = "SELECT `user`.* FROM `user` WHERE u_id=" . intval($f['u_id']);
					$result = mysqli_query($mysqli_link, $query);
					$rows = mysqli_num_rows($result);
					
				} else {
					// Nur eigene Daten anzeigen
					$query = "SELECT `user`.* FROM `user` WHERE u_id=" . intval($u_id);
					$result = mysqli_query($mysqli_link, $query);
					$rows = mysqli_num_rows($result);
				}
				
				if ($rows == 1) {
					$row = mysqli_fetch_object($result);
					$f['u_id'] = $u_id;
					$f['u_name'] = $row->u_name;
					$f['u_nick'] = $row->u_nick;
					$f['u_id'] = $row->u_id;
					$f['u_email'] = $row->u_email;
					$f['u_adminemail'] = $row->u_adminemail;
					$f['u_url'] = $row->u_url;
					$f['u_level'] = $row->u_level;
					$f['u_farbe'] = $row->u_farbe;
					$f['u_zeilen'] = $row->u_zeilen;
					$f['u_smilie'] = $row->u_smilie;
					$f['avatar_status'] = $row->avatar_status;
					$f['u_eintritt'] = $row->u_eintritt;
					$f['u_austritt'] = $row->u_austritt;
					$f['u_systemmeldungen'] = $row->u_systemmeldungen;
					$f['u_punkte_anzeigen'] = $row->u_punkte_anzeigen;
					$f['u_signatur'] = $row->u_signatur;
					$f['u_kommentar'] = $row->u_kommentar;
					$size = unserialize($row->u_frames);
					user_edit($f, $admin, $u_level, $size);
					mysqli_free_result($result);
				}
			}
			
			break;
		
		default:
		// User mit ID $u_id anzeigen
		
			$query = "SELECT `user`.* FROM `user` WHERE u_id=" . intval($u_id);
			$result = mysqli_query($mysqli_link, $query);
			$rows = mysqli_num_rows($result);
			
			if ($rows == 1) {
				$row = mysqli_fetch_object($result);
				$f['u_id'] = $u_id;
				$f['u_name'] = $row->u_name;
				$f['u_nick'] = $row->u_nick;
				$f['u_id'] = $row->u_id;
				$f['u_email'] = $row->u_email;
				$f['u_adminemail'] = $row->u_adminemail;
				$f['u_url'] = $row->u_url;
				$f['u_level'] = $row->u_level;
				$f['u_farbe'] = $row->u_farbe;
				$f['u_zeilen'] = $row->u_zeilen;
				$f['u_smilie'] = $row->u_smilie;
				$f['avatar_status'] = $row->avatar_status;
				$f['u_systemmeldungen'] = $row->u_systemmeldungen;
				$f['u_eintritt'] = $row->u_eintritt;
				$f['u_austritt'] = $row->u_austritt;
				$f['u_punkte_anzeigen'] = $row->u_punkte_anzeigen;
				$f['u_signatur'] = $row->u_signatur;
				$size = unserialize($row->u_frames);
				user_edit($f, $admin, $u_level, $size);
				mysqli_free_result($result);
			}
		
	}
	
} else {
	echo "<p style=\"text-align:center;\">$t[sonst2]</p>\n";
}

// Fuß
if ($o_js) {
	echo schliessen_link();
}
?>
</body>
</html>