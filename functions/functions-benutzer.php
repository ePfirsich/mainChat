<?php

function user_zeige($text, $ui_id, $admin, $schau_raum, $u_level, $zeigeip) {
	// Falls $admin wahr werden IP und Onlinedaten ausgegeben
	
	global $level, $locale, $user_farbe, $ist_online_raum, $lang;
	global $chat_grafik, $whotext, $msgpopup, $chat_url;
	
	// Benutzer listen
	pdoQuery("SET `lc_time_names` = :lc_time_names", [':lc_time_names'=>$locale]);
	
	$result_user = pdoQuery("SELECT `user`.*, date_format(u_login,'%d. %M %Y um %H:%i') AS `letzter_login`, date_format(u_neu,'%d. %M %Y um %H:%i') AS `erster_login` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$ui_id])->fetch();
	
	if ($result_user) {
		$uu_nick = htmlspecialchars($result_user['u_nick']);
		$uu_email = htmlspecialchars($result_user['u_email']);
		$ip_historie = unserialize($result_user['u_ip_historie']);
		$uu_profil_historie = unserialize($result_user['u_profil_historie']);
		
		// Default für Farbe setzen, falls undefiniert
		if (strlen($result_user['u_farbe']) == 0 || $result_user['u_farbe'] == null) {
			$result_user['u_farbe'] = $user_farbe;
		}
		
		// IP bestimmen
		unset($o_http_stuff);
		
		$query = pdoQuery("SELECT `r_name`, online.*, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS `onlinezeit` FROM `online` LEFT JOIN `raum` ON `o_raum` = `r_id` WHERE `o_user` = :o_user", [':o_user'=>$ui_id]);
		$result_raumCount = $query->rowCount();
		$result_raum = $query->fetch();
		
		if ($result_raumCount > 0) {
			$onlinezeit = $result_raum['onlinezeit'];
			if ($admin) {
				$host_name = htmlspecialchars(gethostbyaddr($result_raum['o_ip']));
				$o_http_stuff = $result_raum['o_http_stuff'] . $result_raum['o_http_stuff2'];
			}
			if (isset($o_http_stuff)) {
				$http_stuff = unserialize($o_http_stuff);
			}
		}
		
		if ($result_user['u_away'] != "") {
			$hinweis = "<b>" . $lang['hinweis_hinweis_abwesend'] . "</b> " . $result_user['u_away'];
			$text .= hinweis($hinweis, "hinweis");
		}
		
		// Ausgabe des Benutzers
		$zaehler = 0;
		$text .= "<table style=\"width:100%;\">";
		
		// Überschrift: Avatar
		$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_avatar'], "", "", 0, "70", "");
		
		// Avatare
		// Geschlecht holen
		// Wenn ein Gast den Chat verlässt, hat er keine ID mehr
		if($ui_id != null && $ui_id != "") {
			$query1 = pdoQuery("SELECT `ui_geschlecht` FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$ui_id]);
			
			$result1Count = $query1->rowCount();
			if ($result1Count > 0) {
				$result1 = $query1->fetch();
				$ui_gen = $result1['ui_geschlecht'];
			} else {
				$ui_gen = '0';
			}
		} else {
			$ui_gen = '0';
		}
		
		// Avatar
		$value = avatar_anzeigen($ui_id, $uu_nick, "profil", $ui_gen);
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
			$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_private_nachricht'], "", "", 0, "70", "");
			
			$value = "<form>";
			
			// Eingabeformular für private Nachricht ausgeben
			if ($msgpopup) {
				$value .= '<iframe src="messages-popup.php?user=' . $ui_id . '&user_nick=' . $uu_nick . '" width=100% height=200 marginwidth=\"0\" marginheight=\"0\" hspace=0 vspace=0 framespacing=\"0\"></iframe>';
				pdoQuery("UPDATE `chat` SET `c_gelesen` = 1 WHERE `c_gelesen` = 0 AND `c_typ` = 'P' AND `c_von_user_id` = :c_von_user_id", [':c_von_user_id'=>$ui_id]);
			}
			
			$value .= "<input name=\"text\" autocomplete=\"off\" size=\"111\" maxlength=\"1000\" value=\"\" type=\"text\">\n";
			$value .= "<input name=\"privat\" value=\"$uu_nick\" type=\"hidden\">\n";
			$value .= "<input type=\"submit\" value=\"Go!\">\n";
			$value .= "</form>\n";
			
			// Private Nachricht
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>\n";
			$text .= "<td colspan=\"2\" $bgcolor class=\"smaller\">" . $value . "</td>\n";
			$text .= "</tr>\n";
			$zaehler++;
		}
		
		$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
		
		// Überschrift: Benutzerdaten
		$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_benutzerdaten'], "", "", 0, "70", "");
		
		// Benutzername
		$value = zeige_userdetails($ui_id);
		$text .= zeige_formularfelder("text", $zaehler, "<b>".$lang['benutzer_benutzername']."</b>", "", $value);
		$zaehler++;
		
		// Raum
		if ($result_raum && $result_raum['r_name'] && $result_raum['o_who'] == 0) {
			$value = "<b>" . $result_raum['r_name'] . "&nbsp;[" . $whotext[$result_raum['o_who']] . "]</b>";
			$text .= zeige_formularfelder("text", $zaehler, "<b>".$lang['benutzer_raum']."</b>", "", $value);
			$zaehler++;
		} else if ($result_raum && $result_raum['o_who']) {
			$value = "<b>" . "[" . $whotext[$result_raum['o_who']] . "]</b>";
			$text .= zeige_formularfelder("text", $zaehler, "&nbsp;", "", $value);
			$zaehler++;
		}
		if (isset($onlinezeit) && $onlinezeit) {
			$value = gmdate("H:i:s", $onlinezeit) . "&nbsp;" . $lang['benutzer_onlinezeit_details'];
			$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_onlinezeit'], "", $value);
			$zaehler++;
		} else {
			$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_letzter_login'], "", $result_user['letzter_login']);
			$zaehler++;
		}
		
		if ($result_user['erster_login'] && $result_user['erster_login'] != "01.01.1970 01:00") {
			$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_erster_login'], "", $result_user['erster_login']);
			$zaehler++;
		}
		
		// Punkte
		if ($result_user['u_punkte_gesamt']) {
			if ($result_user['u_punkte_datum_monat'] != date("n", time())) {
				$result_user['u_punkte_monat'] = 0;
			}
			if ($result_user['u_punkte_datum_jahr'] != date("Y", time())) {
				$result_user['u_punkte_jahr'] = 0;
			}
			
			$date = new DateTime();
			$date->format('Y-m-d H:i:s');
			$value = $result_user['u_punkte_gesamt'] . "/" . $result_user['u_punkte_jahr'] . "/" . $result_user['u_punkte_monat'] . "&nbsp;"
			. str_replace("%jahr%", formatIntl($date,'Y',$locale), str_replace("%monat%", formatIntl($date,'MMMM',$locale), $lang['benutzer_punkte_anzeige']));
			$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_punkte'], "", $value);
			$zaehler++;
		}
		
		// E-Mail
		if ($admin) {
			$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_email_intern'], "", "<a href=\"maito:$uu_email\">$uu_email</a>");
			$zaehler++;
		}
		
		// Benutzerseite
		if ($result_user['u_chathomepage'] == "1") {
			$url = "home.php?/".URLENCODE($uu_nick);
			$value = "<a href=\"$url\" target=\"_blank\">$chat_grafik[home]</a>";
			$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_webseite'], "", $value);
			$zaehler++;
		}
		
		// Level
		$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_level'], "", $level[$result_user['u_level']]);
		$zaehler++;
		
		// Farbe
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['benutzer_farbe'] . "</td>\n";
		$text .= "<td style=\"background-color:#" . $result_user['u_farbe'] . ";\">&nbsp;</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		// Kommentar
		if ($result_user['u_kommentar'] && $admin) {
			$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_kommentar'], "", htmlspecialchars($result_user['u_kommentar']));
			$zaehler++;
		}
		
		// Profil-Edit-Historie
		if ($admin) {
			if (is_array($uu_profil_historie)) {
				$value = "";
				foreach($uu_profil_historie as $datum => $nick) {
					$value .= $nick . "&nbsp;(" . str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum)) . ")<br>";
				}
				$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_profil_edit_historie'], "", $value);
				$zaehler++;
			}
			
			// IPs ausgeben
			if ($result_raum && $result_raum['o_ip']) {
				// IP-Adresse
				$value = $host_name . "<br>" . $result_raum['o_ip'] . " " . $lang['benutzer_ip_adressen_online'];
						
				if ($zeigeip == 1 && is_array($ip_historie)) {
					foreach($ip_historie as $datum => $ip_adr) {
						$value .= "<br>" . $ip_adr . "&nbsp;(" . str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum)) . ")";
					}
				}
				
				$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_ip_adressen'], "", $value);
				$zaehler++;
				
				// Browser
				$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_browser'], "", htmlspecialchars($result_raum['o_browser']));
				$zaehler++;
				
				// HTTP-Info
				if ($o_http_stuff) {
					$value = "";
					if (is_array($http_stuff)) {
						foreach($http_stuff as $o_http_stuff_name => $o_http_stuff_inhalt) {
							if ($o_http_stuff_inhalt) {
								$value .= "<b>" . htmlspecialchars($o_http_stuff_name) . ":</b>&nbsp;" . htmlspecialchars($o_http_stuff_inhalt) . "<br>\n";
							}
						}
					}
					$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_http_info'], "", $value);
					$zaehler++;
				}
			} else if ($zeigeip == 1 && is_array($ip_historie)) {
				$value = "";
				foreach($ip_historie as $datum => $ip_adr) {
					$value .= $ip_adr . "&nbsp;(" . str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum)) . ")" . "<br>";
				}
				$text .= zeige_formularfelder("text", $zaehler, $lang['benutzer_letzte_ip_adressen'], "", $value);
				$zaehler++;
			}
			
		}
		
		// Benutzermenue mit Aktionen
		if ($u_level != "G") {
			$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
			
			// Überschrift: Interaktionen
			$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_interaktionen'], "", "", 0, "70", "");
			
			$value = "";
			$value .= "[<a href=\"schreibe.php?text=/ignore%20$uu_nick\" class=\"schreibe-chat\">$lang[benutzer_ignorieren]</a>]<br>\n";
			$value .= "[<a href=\"schreibe.php?text=/einlad%20$uu_nick\" class=\"schreibe-chat\">$lang[benutzer_einladen_ausladen]</a>]<br>\n";
			$value .= "[<a href=\"inhalt.php?bereich=nachrichten&aktion=neu2&daten_nick=$uu_nick\" target=\"chat\">$lang[benutzer_nachricht_senden]</a>]<br>\n";
			$value .= "[<a href=\"inhalt.php?bereich=freunde&aktion=neu&daten_nick=$uu_nick\" target=\"chat\">$lang[benutzer_freund_hinzufuegen]</a>]<br>\n";
			
			// Adminmenue
			if ($admin) {
				$value .= "[<a href=\"inhalt.php?bereich=benutzer&zeigeip=1&aktion=benutzer_zeig&ui_id=$ui_id&schau_raum=$schau_raum\">" . $lang['benutzer_weitere_ip_adressen'] . "</a>]<br>\n";
				$value .= "[<a href=\"inhalt.php?bereich=benutzer&kick_user_chat=1&aktion=benutzer_zeig&ui_id=$ui_id&schau_raum=$schau_raum\">" . $lang['benutzer_aus_dem_chat_kicken'] . "</a>]<br>\n";
			
				if ($result_raumCount == 1) {
					$value .= "[<a href=\"schreibe.php?text=/gag%20$uu_nick\" class=\"schreibe-chat\">$lang[benutzer_knebeln]</a>]<br>\n";
					$value .= "[<a href=\"schreibe.php?text=/kick%20$uu_nick\" class=\"schreibe-chat\">$lang[benutzer_kicken]</a>]<br>\n";
					$value .= "[<a href=\"inhalt.php?bereich=sperren&aktion=neu&hname=$host_name&ipaddr=$result_raum[o_ip]&uname=" . urlencode($result_raum['o_name']) . "\" target=\"chat\">$lang[benutzer_sperren]</a>]<br>\n";
					$value .= "[<a href=\"inhalt.php?bereich=sperren&aktion=blacklist_neu&daten_nick=$uu_nick\" target=\"chat\">$lang[benutzer_blacklist]</a>]<br>\n";
				}
			}
			
			$text .= zeige_formularfelder("text", $zaehler, "&nbsp;", "", $value);
			$zaehler++;
			
			$value .= "<span id=\"out\"></span>\n";
			$value .= "<script>
				document.querySelectorAll('a.schreibe-chat').forEach(item => {
					item.addEventListener('click', event => {
						// Default-Aktion für Klick auf den Link,
						// d. h. direktes Aufrufen der Seite, verhindern, da wir das Linkziel mit Ajax aufrufen wollen:
						event.preventDefault();
						// Link aus dem href-Attribut holen:
						const link = event.target.href;
						fetch(link, {
							method: 'get'
						}).then(res => {
							return res.text();
							}).then(res => {
								console.log(res);
								document.getElementById('out').innerHTML = res;
							});
					});
				})
				</script>";
		}
		
		// Admin-Menü 3
		if ($admin) {
			$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
			
			// Überschrift: Admin
			$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['benutzer_admin'], "", "", 0, "70", "");
			
			// Ändern
			$value = "";
			$value .= "<form name=\"edit\" action=\"inhalt.php?bereich=einstellungen\" method=\"post\" style=\"display:inline;\">\n";
			$value .= "<input type=\"hidden\" name=\"u_id\" value=\"$ui_id\">\n";
			$value .= "<input type=\"submit\" name=\"ein\" value=\"Ändern!\">\n";
			$value .= "</form>\n";
			
			$value .= "<form name=\"edit\" action=\"inhalt.php?bereich=einstellungen\" method=\"post\" style=\"display:inline;\">\n";
			$value .= "<input type=\"hidden\" name=\"u_id\" value=\"$ui_id\">\n";
			$value .= "<input type=\"hidden\" name=\"u_nick\" value=\"$uu_nick\">\n";
			$value .= "<input type=\"hidden\" name=\"aktion\" value=\"editieren\">\n";
			$value .= "<input type=\"submit\" name=\"eingabe\" value=\"$lang[einstellungen_loeschen]\"><br>";
			
			$queryX = pdoQuery("SELECT `u_chathomepage` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$ui_id]);
			
			$g = $queryX->fetch();
			
			if ($g['u_chathomepage'] == "1") {
				$value .= "<input type=\"submit\" name=\"eingabe\" value=\"$lang[einstellungen_benutzerseite_loeschen]\">";
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
			$box = str_replace("%user%", $uu_nick, $lang['benutzer_online']);
		} else {
			$box = str_replace("%user%", $uu_nick, $lang['benutzer_offline']);
		}
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
		
		if (isset($onlinezeit) && $onlinezeit && $u_level != "G") {
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
		}
	}
}

function benutzer_suche($f, $suchtext, $suche_ip, $suche_level, $suche_anmeldung, $suche_login, $suche_benutzerseite) {
	global $lang, $admin, $level;
	// Suchergebnis mit Formular ausgeben
	$box = $lang['benutzer_suche_benutzer_suchen'];
	$zaehler = 0;
	
	$text = "";
	$text .= "<form name=\"suche\" action=\"inhalt.php?bereich=benutzer\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"suche\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	// Suchtext eingeben
	$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_suche_suchtext_eingeben'], "suchtext", $suchtext);
	$zaehler++;
	
	if ($admin) {
		// Suchformular nach IP-Adressen
		$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_suche_ip_adresse'], "suche_ip", $suche_ip);
		$zaehler++;
		
		// Liste der Gruppen ausgeben
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['benutzer_suche_benutzergruppe'] . "</td>";
		$text .= "<td $bgcolor><select name=\"suche_level\">\n";
		$text .= "<option value=\"\">$lang[benutzer_suche_egal]\n";
		
		reset($level);
		foreach($level as $levelname => $levelbezeichnung) {
			if ($levelname != "B") {
				if ($suche_level == $levelname) {
					$text .= "<option selected value=\"$levelname\">$levelbezeichnung\n";
				} else {
					$text .= "<option value=\"$levelname\">$levelbezeichnung\n";
				}
			}
		}
		$text .= "</select></td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
	}
	
	// Anmeldung vor
	$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_suche_anmeldung_vor'], "suche_anmeldung", $suche_anmeldung, 0, "10", $lang['benutzer_suche_tagen']);
	$zaehler++;
	
	// Letzter Login vor
	$text .= zeige_formularfelder("input", $zaehler, $lang['benutzer_suche_letzter_login_vor'], "suche_login", $suche_login, 0, "10", $lang['benutzer_suche_stunden']);
	$zaehler++;
	
	// Mit Benutzerseite
	$value = array($lang['benutzer_suche_egal'], $lang['benutzer_suche_ja']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $lang['benutzer_suche_mit_benutzerseite'], "suche_benutzerseite", $value, $suche_benutzerseite);
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
						<input type=\"submit\" name=\"suchtext_eingabe\" value=\"$lang[benutzer_absenden]\">
					</td>
				</tr>";
	$text .= "</table>";
	$text .= "</form>";
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}
?>