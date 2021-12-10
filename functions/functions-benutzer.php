<?php

function user_zeige($user, $admin, $schau_raum, $u_level, $zeigeip) {
	// $user = ID des Benutzers
	// Falls $admin wahr werden IP und Onlinedaten ausgegeben
	
	global $mysqli_link, $level, $id, $f1, $f2, $f3, $f4;
	global $user_farbe, $ist_online_raum, $chat_max_eingabe, $chat_eingabe_breite, $t;
	global $chat_grafik, $whotext, $msgpopup, $chat_url;
	
	// Benutzer listen
	$query = "SELECT `user`.*,"
		. "FROM_Unixtime(UNIX_TIMESTAMP(u_login),'%d.%m.%Y %H:%i') AS `letzter_login`,"
		. "FROM_Unixtime(UNIX_TIMESTAMP(u_neu),'%d.%m.%Y %H:%i') AS `erster_login` "
			. "FROM `user` WHERE `u_id`=$user ";
			$result = mysqli_query($mysqli_link, $query);
			
			if ($result && mysqli_num_rows($result) == 1) {
				$row = mysqli_fetch_object($result);
				$uu_away = $row->u_away;
				$uu_nick = htmlspecialchars($row->u_nick);
				$uu_id = $row->u_id;
				$uu_adminemail = htmlspecialchars($row->u_adminemail);
				$uu_level = $row->u_level;
				$uu_farbe = $row->u_farbe;
				$letzter_login = $row->letzter_login;
				$erster_login = $row->erster_login;
				$ip_historie = unserialize($row->u_ip_historie);
				$uu_punkte_gesamt = $row->u_punkte_gesamt;
				$uu_punkte_monat = $row->u_punkte_monat;
				$uu_punkte_jahr = $row->u_punkte_jahr;
				$uu_chathomepage = $row->u_chathomepage;
				$uu_profil_historie = unserialize($row->u_profil_historie);
				$uu_kommentar = $row->u_kommentar;
				
				// Default für Farbe setzen, falls undefiniert
				if (strlen($uu_farbe) == 0) {
					$uu_farbe = $user_farbe;
				}
				
				// IP bestimmen
				unset($o_http_stuff);
				$query = "SELECT r_name, online.*, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS onlinezeit FROM online LEFT JOIN raum ON o_raum=r_id WHERE o_user=$user ";
				$result = mysqli_query($mysqli_link, $query);
				
				if ($result && $rows = mysqli_num_rows($result) == 1) {
					$o_row = mysqli_fetch_object($result);
					$onlinezeit = $o_row->onlinezeit;
					if ($admin) {
						$host_name = htmlspecialchars(gethostbyaddr($o_row->o_ip));
						$o_http_stuff = $o_row->o_http_stuff . $o_row->o_http_stuff2;
					}
					if (isset($o_http_stuff)) {
						$http_stuff = unserialize($o_http_stuff);
					}
				}
				
				// Ausgabe des Benutzers
				$zaehler = 0;
				$text = "";
				$text .= "<table style=\"width:100%;\">";
				
				// Überschrift: Avatar
				$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['benutzer_avatar'], "", "", 0, "70", "");
				
				// Avatare
				// Geschlecht holen
				$query1 = "SELECT * FROM userinfo WHERE ui_userid = '$row->u_id'";
				$result1 = mysqli_query($mysqli_link, $query1);
				if ($result1 && mysqli_num_rows($result1) == 1) {
					$row1 = mysqli_fetch_object($result1);
					$ui_gen = $row1->ui_geschlecht;
				} else {
					$ui_gen = 'leer';
				}
				
				// Bildinfos lesen und in Array speichern
				$queryAvatar = "SELECT b_name,b_height,b_width,b_mime FROM bild WHERE b_name = 'avatar' AND b_user=$row->u_id";
				$resultAvatar = mysqli_query($mysqli_link, $queryAvatar);
				unset($bilder);
				if ($resultAvatar && mysqli_num_rows($resultAvatar) > 0) {
					while ($rowAvatar = mysqli_fetch_object($resultAvatar)) {
						$bilder[$rowAvatar->b_name]['b_mime'] = $rowAvatar->b_mime;
						$bilder[$rowAvatar->b_name]['b_width'] = $rowAvatar->b_width;
						$bilder[$rowAvatar->b_name]['b_height'] = $rowAvatar->b_height;
					}
				}
				mysqli_free_result($resultAvatar);
				
				$value = "";
				if (!isset($bilder)) {
					if ($ui_gen[0] == "m") { // Männlicher Standard-Avatar
						$value .= '<img src="./images/avatars/no_avatar_m.jpg" style="width:200px; height:200x;" alt="" />';
					} else if ($ui_gen[0] == "w") { // Weiblicher Standard-Avatar
						$value .= '<img src="./images/avatars/no_avatar_w.jpg" style="width:200px; height:200px;" alt="" />';
					} else { // Neutraler Standard-Avatar
						$value .= '<img src="./images/avatars/no_avatar_es.jpg" style="width:200px; height:200px;" alt="" />';
					}
				} else {
					$value .= avatar_editieren_anzeigen($uu_id, $temp_von_user, "avatar", $bilder, "profil");
				}
				
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
				
				// "Private Nachricht"
				if (isset($onlinezeit) && $onlinezeit && $u_level != "G") {
					$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
					
					// Überschrift: Private Nachricht
					$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['benutzer_private_nachricht'], "", "", 0, "70", "");
					
					$value = "<form>";
					
					// Eingabeformular für private Nachricht ausgeben
					$value .= $f1;
					
					if ($msgpopup) {
						$value .= '<iframe src="messages-popup.php?id=' . $id
						. '&user=' . $user
						. '&user_nick=' . $uu_nick
						. '" width=100% height=200 marginwidth=\"0\" marginheight=\"0\" hspace=0 vspace=0 framespacing=\"0\"></iframe>';
						$pmu = mysqli_query($mysqli_link, "UPDATE chat SET c_gelesen=1 WHERE c_gelesen=0 AND c_typ='P' AND c_von_user_id=".$user);
					}
					
					$value .= "<input name=\"text\" autocomplete=\"off\" size=\"" . $chat_eingabe_breite . "\" maxlength=\"" . ($chat_max_eingabe - 1) . "\" value=\"\" type=\"text\">"
						. "<input name=\"id\" value=\"$id\" type=\"hidden\">"
						. "<input name=\"privat\" value=\"$uu_nick\" type=\"hidden\">"
						. "<input type=\"submit\" value=\"Go!\">" . $f2;
					$value .= "</form>";
					
					// Private Nachricht
					if ($zaehler % 2 != 0) {
						$bgcolor = 'class="tabelle_zeile2"';
					} else {
						$bgcolor = 'class="tabelle_zeile1"';
					}
					$text .= "<tr>\n";
					$text .= "<td colspan=\"2\" $bgcolor>" . $value . "</td>\n";
					$text .= "</tr>\n";
					$zaehler++;
				}
				
				$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
				
				// Überschrift: Benutzerdaten
				$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['benutzer_benutzerdaten'], "", "", 0, "70", "");
				
				// Benutzername
				$value = zeige_userdetails($user, $row);
				if ($uu_away != "") {
					$value .= "<br>($uu_away)";
				}
				$text .= zeige_formularfelder("text", $zaehler, "<b>".$t['benutzer_benutzername']."</b>", "", $value);
				$zaehler++;
				
				// Raum
				if (isset($o_row) && $o_row->r_name && $o_row->o_who == 0) {
					$value = "<b>" . $o_row->r_name . "&nbsp;[" . $whotext[$o_row->o_who] . "]</b>";
					$text .= zeige_formularfelder("text", $zaehler, "<b>".$t['benutzer_raum']."</b>", "", $value);
					$zaehler++;
				} else if (isset($o_row) && $o_row->o_who) {
					$value = "<b>" . "[" . $whotext[$o_row->o_who] . "]</b>";
					$text .= zeige_formularfelder("text", $zaehler, "&nbsp;", "", $value);
					$zaehler++;
				}
				if (isset($onlinezeit) && $onlinezeit) {
					$value = gmdate("H:i:s", $onlinezeit) . "&nbsp;" . $t['benutzer_onlinezeit_details'];
					$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_onlinezeit'], "", $value);
					$zaehler++;
				} else {
					$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_letzter_login'], "", $letzter_login);
					$zaehler++;
				}
				
				if ($erster_login && $erster_login != "01.01.1970 01:00") {
					$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_erster_login'], "", $erster_login);
					$zaehler++;
				}
				
				// Punkte
				if ($uu_punkte_gesamt) {
					if ($row->u_punkte_datum_monat != date("n", time())) {
						$uu_punkte_monat = 0;
					}
					if ($row->u_punkte_datum_jahr != date("Y", time())) {
						$uu_punkte_jahr = 0;
					}
					$value = $uu_punkte_gesamt . "/" . $uu_punkte_jahr . "/" . $uu_punkte_monat . "&nbsp;"
							. str_replace("%jahr%", substr(strftime("%Y", time()), 2, 2), str_replace("%monat%", substr(strftime("%B", time()), 0, 3), $t['benutzer_punkte_anzeige']));
					$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_punkte'], "", $value);
					$zaehler++;
				}
				
				// Interne E-Mail
				if ($admin) {
					$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_email_intern'], "", "<a href=\"maito:$uu_adminemail\">$uu_adminemail</a>");
					$zaehler++;
				}
				
				// Chat-Homepage
				if ($uu_chathomepage == "1") {
					$url = "home.php?/".URLENCODE($uu_nick);
					$value = "<a href=\"$url\" target=\"_blank\">$chat_grafik[home]</a>";
					$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_url'], "", $value);
					$zaehler++;
				}
				
				// Level
				$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_level'], "", $level[$uu_level]);
				$zaehler++;
				
				// Farbe
				if ($zaehler % 2 != 0) {
					$bgcolor = 'class="tabelle_zeile2"';
				} else {
					$bgcolor = 'class="tabelle_zeile1"';
				}
				$text .= "<tr>\n";
				$text .= "<td style=\"text-align:right;\" $bgcolor>" . $t['benutzer_farbe'] . "</td>\n";
				$text .= "<td style=\"background-color:#" . $uu_farbe . ";\">&nbsp;</td>\n";
				$text .= "</tr>\n";
				$zaehler++;
				
				// Kommentar
				if ($uu_kommentar && $admin) {
					$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_kommentar'], "", htmlspecialchars($uu_kommentar));
					$zaehler++;
				}
				
				// Profil-Edit-Historie
				if ($admin) {
					if (is_array($uu_profil_historie)) {
						$value = "";
						while (list($datum, $nick) = each($uu_profil_historie)) {
							$value .= $nick . "&nbsp;(" . str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum)) . ")<br>";
						}
						$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_profil_edit_historie'], "", $value);
						$zaehler++;
					}
					
					// IPs ausgeben
					if (isset($o_row) && $o_row->o_ip) {
						// IP-Adresse
						$value = $host_name . "<br>" . $o_row->o_ip . " " . $t['benutzer_ip_adressen_online'];
								
						if ($zeigeip == 1 && is_array($ip_historie)) {
							while (list($datum, $ip_adr) = each($ip_historie)) {
								$value .= "<br>" . $ip_adr . "&nbsp;(" . str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum)) . ")";
							}
						}
						
						$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_ip_adressen'], "", $value);
						$zaehler++;
						
						// Browser
						$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_browser'], "", htmlspecialchars($o_row->o_browser));
						$zaehler++;
						
						// HTTP-Info
						if ($o_http_stuff) {
							$value = "";
							if (is_array($http_stuff)) {
								while (list($o_http_stuff_name, $o_http_stuff_inhalt) = each($http_stuff)) {
									if ($o_http_stuff_inhalt) {
										$value .= "<b>" . htmlspecialchars($o_http_stuff_name) . ":</b>&nbsp;" . htmlspecialchars($o_http_stuff_inhalt) . "<br>\n";
									}
								}
							}
							$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_http_info'], "", $value);
							$zaehler++;
						}
					} else if ($zeigeip == 1 && is_array($ip_historie)) {
						$value = "";
						while (list($datum, $ip_adr) = each($ip_historie)) {
							$value .= $ip_adr . "&nbsp;(" . str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum)) . ")" . $f4 . "<br>";
						}
						$text .= zeige_formularfelder("text", $zaehler, $t['benutzer_letzte_ip_adressen'], "", $value);
						$zaehler++;
					}
					
				}
				
				// Benutzermenue mit Aktionen
				if ($u_level != "G") {
					$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
					
					// Überschrift: Interaktionen
					$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['benutzer_interaktionen'], "", "", 0, "70", "");
					
					$value = "";
					
					$mlnk[1] = "schreibe.php?id=$id&text=/ignore%20$uu_nick";
					$mlnk[2] = "schreibe.php?id=$id&text=/einlad%20$uu_nick";
					$value .= "[<a href=\"$mlnk[1]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[1]';return(false);\">$t[benutzer_ignorieren]</a>]<br>\n";
					$value .= "[<a href=\"$mlnk[2]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[2]';return(false);\">$t[benutzer_einladen_ausladen]</a>]<br>\n";
					$mlnk[8] = "inhalt.php?seite=nachrichten&id=$id&aktion=neu2&neue_email[an_nick]=$uu_nick";
					$mlnk[9] = "schreibe.php?id=$id&text=/freunde%20$uu_nick";
					$value .= "[<a href=\"$mlnk[8]\" target=\"_blank\">$t[benutzer_nachricht_senden]</a>]<br>\n"
					. "[<a href=\"$mlnk[9]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[9]';return(false);\">$t[benutzer_freund]</a>]<br>\n";
					
					// Adminmenue
					if ($admin) {
						$mlnk[7] = "inhalt.php?seite=benutzer&id=$id&zeigeip=1&aktion=benutzer_zeig&user=$user&schau_raum=$schau_raum";
						$value .= "[<a href=\"$mlnk[7]\">" . $t['benutzer_weitere_ip_adressen'] . "</a>]<br>\n";
					}
					
					// Adminmenue
					if ($admin && $rows == 1) {
						$mlnk[3] = "inhalt.php?seite=benutzer&id=$id&trace=" . urlencode($host_name) . "&aktion=benutzer_zeig&user=$user&schau_raum=$schau_raum";
						$mlnk[4] = "schreibe.php?id=$id&text=/gag%20$uu_nick";
						$mlnk[5] = "schreibe.php?id=$id&text=/kick%20$uu_nick";
						$mlnk[6] = "inhalt.php?seite=sperren&id=$id&aktion=neu&hname=$host_name&ipaddr=$o_row->o_ip&uname=" . urlencode($o_row->o_name);
						$value .= "[<a href=\"$mlnk[4]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[4]';return(false);\">$t[benutzer_knebeln]</a>]<br>\n"
						. "[<a href=\"$mlnk[5]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[5]';return(false);\">$t[benutzer_kicken]</a>]<br>\n"
						. "[<a href=\"$mlnk[6]\" target=\"chat\">$t[benutzer_sperren]</a>]<br>\n";
						$value .= "[<a href=\"inhalt.php?seite=sperren&id=$id&aktion=blacklist_neu&neuer_blacklist[u_nick]=$uu_nick\" target=\"chat\">$t[benutzer_blacklist]</a>]<br>\n";
					}
					
					$text .= zeige_formularfelder("text", $zaehler, "&nbsp;", "", $value);
					$zaehler++;
				}
				
				// Admin-Menü 3
				if ($admin) {
					$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
					
					// Überschrift: Admin
					$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['benutzer_admin'], "", "", 0, "70", "");
					
					// Ändern
					$value = "";
					$value .= "<form name=\"edit\" action=\"inhalt.php?seite=einstellungen\" method=\"post\" style=\"display:inline;\">\n";
					$value .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
					$value .= "<input type=\"hidden\" name=\"u_id\" value=\"$uu_id\">\n";
					$value .= "<input type=\"submit\" name=\"ein\" value=\"Ändern!\">\n";
					$value .= "</form>\n";
					
					$value .= "<form name=\"edit\" action=\"inhalt.php?seite=einstellungen\" method=\"post\" style=\"display:inline;\">\n";
					$value .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
					$value .= "<input type=\"hidden\" name=\"u_id\" value=\"$uu_id\">\n";
					$value .= "<input type=\"hidden\" name=\"u_nick\" value=\"$uu_nick\">\n";
					$value .= "<input type=\"hidden\" name=\"aktion\" value=\"editieren\">\n";
					$value .= "<input type=\"submit\" name=\"eingabe\" value=\"Löschen!\"><br>";
					
					$query = "SELECT `u_chathomepage` FROM `user` WHERE `u_id` = '$uu_id'";
					$result = mysqli_query($mysqli_link, $query);
					$g = mysqli_fetch_array($result, MYSQLI_ASSOC);
					
					if ($g['u_chathomepage'] == "1") {
						$value .= "<input type=\"submit\" name=\"eingabe\" value=\"Homepage löschen!\">" . $f2;
					}
					if ((($u_level == "C" || $u_level == "A") && ($uu_level == "U" || $uu_level == "M" || $uu_level == "Z")) || ($u_level == "S")) {
						$value .= "<br><input type=\"submit\" name=\"eingabe\" value=\"$t[chat_msg110]\">";
					}
					$value .= "</form>\n";
					
					// Avatar
					if ($zaehler % 2 != 0) {
						$bgcolor = 'class="tabelle_zeile2"';
					} else {
						$bgcolor = 'class="tabelle_zeile1"';
					}
					$text .= "<tr>\n";
					$text .= "<td colspan=\"2\" $bgcolor>" . $value . "</td>\n";
					$text .= "</tr>\n";
					$zaehler++;
				}
				
				
				// Tabellenende
				$text .= "</table>\n";
				
				// Kopf Tabelle Benutzerinfo
				if (isset($onlinezeit) && $onlinezeit) {
					$box = str_replace("%user%", $uu_nick, $t['benutzer_online']);
				} else {
					$box = str_replace("%user%", $uu_nick, $t['benutzer_offline']);
				}
				
				// Box anzeigen
				zeige_tabelle_zentriert($box, $text);
				
				?>
				<span id="out"></span>
				<script>
				document.querySelector('form').addEventListener('submit', event => {
					event.preventDefault();
					fetch('schreibe.php', {
						method: 'post',
						body: new FormData(document.querySelector('form'))
					}).then(res => {
						return res.text();
						}).then(res => {
							//console.log(res);
							document.querySelector('input[name="text"]').value = '';
							document.getElementById('out').innerHTML = res;
						});
				})
				</script>
				<?php
				
				// ggf Profil ausgeben, wenn ein externes Profil eingebunden werden soll (Benutzername: $uu_nick)
				mysqli_free_result($result);
			}
}

function benutzer_suche($f, $suchtext) {
	global $id, $t, $admin, $level, $f1, $f2;
	// Suchergebnis mit Formular ausgeben
	$box = $t['benutzer_suche_benutzer_suchen'];
	$zaehler = 0;
	
	$text = "";
	$text .= "<form name=\"suche\" action=\"inhalt.php?seite=benutzer\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"suche\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Benutzerdaten
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['benutzer_suche_neue_suche'], "", "", 0, "70", "");
	
	// Suchtext eingeben
	$text .= zeige_formularfelder("input", $zaehler, $t['benutzer_suche_suchtext_eingeben'], "suchtext", $suchtext);
	$zaehler++;
	
	if ($admin) {
		// Suchformular nach IP-Adressen
		$text .= zeige_formularfelder("input", $zaehler, $t['benutzer_suche_ip_adresse'], "f[ip]", $f['ip']);
		$zaehler++;
		
		// Liste der Gruppen ausgeben
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $t['benutzer_suche_benutzergruppe'] . "</td>";
		$text .= "<td $bgcolor>" . $f1 . "<select name=\"f[level]\">\n";
		$text .= "<option value=\"\">$t[benutzer_suche_egal]\n";
		
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
		$text .= "</select>" . $f2 . "</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
	}
	
	// Anmeldung vor
	$text .= zeige_formularfelder("input", $zaehler, $t['benutzer_suche_anmeldung_vor'], "f[user_neu]", $f['user_neu'], 0, "10", $t['benutzer_suche_tagen']);
	$zaehler++;
	
	// Letzter Login vor
	$text .= zeige_formularfelder("input", $zaehler, $t['benutzer_suche_letzter_login_vor'], "f[user_login]", $f['user_login'], 0, "10", $t['benutzer_suche_stunden']);
	$zaehler++;
	
	// Mit Homepage
	$value = array($t['benutzer_suche_egal'], $t['benutzer_suche_ja']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $t['benutzer_suche_mit_homepage'], "f[u_chathomepage]", $value, $f['u_chathomepage']);
	$zaehler++;
	
	// Formular-Button anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "
				<tr>
					<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>
					<td $bgcolor>
						<input type=\"submit\" name=\"suchtext_eingabe\" value=\"Go!\">
					</td>
				</tr>";
	$text .= "</table>";
	$text .= "</form>";
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}
?>