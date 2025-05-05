<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

$in_den_chat_einloggen = true;
$frameset = false;

// Prüfung, ob dieser Benutzer bereits existiert
$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$username]);

$resultCount = $query->rowCount();
if ($resultCount == 0) {
	// Login als Gast
	if (!isset($passwort) || $passwort == "") {
		// Login als Gast
		$rows = 0;
		if ($gast_login) {
			$remote_addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
			$http_user_agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
			
			// Prüfen, ob von der IP und dem Benutzer-Agent schon ein Gast online ist und ggf abweisen
			$query = pdoQuery("SELECT `o_id` FROM `online` WHERE `o_browser` = :o_browser AND `o_ip` = :o_ip AND `o_level` = 'G' AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout", [':o_browser'=>$http_user_agent, ':o_ip'=>$remote_addr, ':timeout'=>$timeout]);
			$rows = $query->rowCount();
		}
		
		// Login abweisen, falls mehr als ein Gast online ist oder Gäste gesperrt sind
		if ( !$gast_login || ($rows > 1 && !$gast_login_viele) || ist_gastspeere_aktiv() ) {
			// Gäste sind gesperrt
			
			$fehlermeldung = $lang['login_fehlermeldung_gastlogin'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			$in_den_chat_einloggen = false;
		} else {
			// Login als Gast forfahren
			
			// Im Benutzername alle Sonderzeichen entfernen, Länge prüfen
			$username = coreCheckName($username, $check_name);
			
			if (strlen($username) < 4 || strlen($username) > 20) {
				$username = "";
			}
			
			// Falls kein Benutzername übergeben, Nick finden
			if (strlen($username) == 0) {
				if ($gast_name_auto) {
					// Freien Benutzername bestimmen falls in der Config erlaubt
					$rows = 1;
					$i = 0;
					$anzahl = count($gast_name);
					while ($rows != 0 && $i < 100) {
						$username = $gast_name[mt_rand(1, $anzahl)];
						$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$username]);
						
						$rows = $query->rowCount();
						$i++;
					}
				} else {
					// freien Namen bestimmen
					$rows = 1;
					$i = 0;
					while ($rows != 0 && $i < 100) {
						$username = $lang['login_gast'] . strval((mt_rand(1, 10000)) + 1);
						$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$username]);
						
						$rows = $query->rowCount();
						$result = $query->fetch();
						$i++;
					}
				}
			}
			
			// Im Benutzername alle Sonderzeichen entfernen, vorsichtshalber nochmals prüfen
			$username = coreCheckName($username, $check_name);
			
			// Benutzerdaten für Gast setzen
			$f['u_nick'] = $username;
			$f['u_passwort'] = mt_rand(1, 10000);
			$f['u_level'] = "G";
			$passwort = $f['u_passwort'];
			
			// Prüfung, ob dieser Benutzer bereits existiert
			$query = pdoQuery("SELECT `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$f['u_nick']]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 0) {
				pdoQuery("INSERT INTO `user` (`u_nick`, `u_passwort`, `u_level`, `u_neu`) VALUES (:u_nick, :u_passwort, :u_level, DATE_FORMAT(now(),\"%Y%m%d%H%i%s\"))",
					[
						':u_nick'=>$f['u_nick'],
						':u_passwort'=>encrypt_password($f['u_passwort']),
						':u_level'=>$f['u_level']
					]);
			}
		}
	}
}

if($in_den_chat_einloggen) {
	// Login in den Chat (Als Gast und registrierter Benutzer)
	
	// Passwort prüfen und Benutzerdaten lesen
	$rows = 0;
	$result = auth_user($username, $passwort, $bereits_angemeldet);
	
	if ($result) {
		// Login Ok, Benutzerdaten setzen
		$row = $result;
		$user_id = $row['u_id'];
		$u_nick = $row['u_nick'];
		$u_level = $row['u_level'];
		$u_agb = $row['u_agb'];
		$u_punkte_monat = $row['u_punkte_monat'];
		$u_punkte_jahr = $row['u_punkte_jahr'];
		$u_punkte_datum_monat = $row['u_punkte_datum_monat'];
		$u_punkte_datum_jahr = $row['u_punkte_datum_jahr'];
		$u_punkte_gesamt = $row['u_punkte_gesamt'];
		$ip_historie = unserialize($row['u_ip_historie']);
		
		aktualisiere_inhalt_online($user_id);
		
		// Eingeloggt bleiben (nicht als Gast)
		if($row['u_level'] != "G" && $angemeldet_bleiben == 1) {
			$identifier = random_string();
			$securitytoken = random_string();
			$securitytoken2 = sha1($securitytoken);
			
			// Neuen Eintrag anlegen
			pdoQuery("INSERT INTO `securitytokens` SET `user_id` = :user_id, `identifier` = :identifier, `securitytoken` = :securitytoken", [':user_id'=>$user_id, ':identifier'=>$identifier, ':securitytoken'=>$securitytoken2]);
			
			setcookie("identifier",$identifier,time()+(3600*24*365)); //1 Jahr Gültigkeit
			setcookie("securitytoken",$securitytoken,time()+(3600*24*365)); //1 Jahr Gültigkeit
		}
		
		// Benutzer online bestimmen
		if ($chat_max[$u_level] != 0) {
			$query = pdoQuery("SELECT COUNT(`o_id`) AS `anzahl` FROM `online` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :o_aktiv", [':o_aktiv'=>$timeout]);
			
			$resultCount = $query->rowCount();
			if ($resultCount > 0) {
				$result = $query->fetch();
				$onlineanzahl = $result['anzahl'];
			}
		}
		
		// Login erfolgreich ?
		if ($u_level == "Z") {
			// Benutzer gesperrt -> Fehlermeldung ausgeben
			$fehlermeldung = str_replace("%u_nick%", $u_nick, $lang['login_fehlermeldung_login_account_gesperrt']);
			$text .= hinweis($fehlermeldung, "fehler");
		} else if ($chat_max[$u_level] != 0 && $onlineanzahl > $chat_max[$u_level]) {
			// Maximale Anzahl der Benutzer im Chat erreicht -> Fehlermeldung ausgeben
			$fehlermeldung = str_replace("%online%", $onlineanzahl, $lang['login_fehlermeldung_login_zu_viele_benutzer_online']);
			$fehlermeldung = str_replace("%max%", $chat_max[$u_level], $fehlermeldung);
			$fehlermeldung = str_replace("%leveltxt%", $level[$u_level], $txt);
			$fehlermeldung = str_replace("%zusatztext%", $chat_max[zusatztext], $txt);
			
			$text .= hinweis($fehlermeldung, "fehler");
			
			unset($u_nick);
		} else {
			$rest_ausblenden = false;
			
			if ( ist_gastspeere_aktiv() && ($u_level == 'G')) {
				$captcha_text1 = "999";
			} // abweisen, falls Gastsperre aktiv
			
			if ( isset($captcha_text1) && ($captcha_text1 == "")) {
				$captcha_text1 = "999";
			} // Abweisen, falls leere Eingabe
			
			// Prüfen, ob alles korrekt eingegeben wurde.
			if ( isset($captcha_text2) && ( $captcha_text2 != md5( $user_id . "+code+" . $captcha_text1 . "+" . date("Y-m-d h") ) ) ) {
				$uebergabe = "";
			}
			
			// muss Benutzer/Gast noch Nutzungsbestimmungen bestätigen?
			if ($uebergabe != $lang['login_nutzungsbestimmungen_ok'] && $u_agb != "1") {
				// Nutzungsbestimmungen ausgeben
				
				if (!isset($eintritt)) {
					$eintritt = RaumNameToRaumID($lobby);
				}
				
				$zaehler = 0;
				
				$text .= $lang['chat_agb'];
				$text .= "<form action=\"index.php\" target=\"_top\" method=\"post\">\n";
				$text .= "<input type=\"hidden\" name=\"aktion\" value=\"einloggen\">\n";
				$text .= "<table style=\"width:100%;\">\n";
				
				// Überschrift: Bestätigung der Nutzungsbestimmungen
				$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['login_bestaetigung_nutzungsbestimmungen'], "", "", 0, "70", "");
				
				if ($zaehler % 2 != 0) {
					$bgcolor = 'class="tabelle_zeile2"';
				} else {
					$bgcolor = 'class="tabelle_zeile1"';
				}
				$text .= "<tr>\n";
				$text .= "<td colspan=\"2\" $bgcolor>\n";
				$text .= "$lang[captcha1]<br>";
				
				$aufgabe = mt_rand(1, 3);
				if ($aufgabe == 1) { // +
					$zahl1 = mt_rand(0, 99);
					$zahl2 = mt_rand(0, 99 - $zahl1);
					$ergebnis = $zahl1 + $zahl2;
				} else if ($aufgabe == 2) { // -
					$zahl1 = mt_rand(0, 99);
					$zahl2 = mt_rand(0, $zahl1);
					$ergebnis = $zahl1 - $zahl2;
				} else { // *
					$zahl1 = mt_rand(0, 10);
					$zahl2 = mt_rand(0, 10);
					$ergebnis = $zahl1 * $zahl2;
				}
				
				$text .= $langzahl[$zahl1] . " " . $langaufgabe[$aufgabe] . " " . $langzahl[$zahl2] . " &nbsp;&nbsp;&nbsp;&nbsp;";
				
				//echo md5( $user_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") );
				//echo md5( $user_id . "code" . $ergebnis . "+" . date("Y-m-d h") );
				$text .= "<input type=\"text\" name=\"captcha_text1\">\n";
				$text .= "<input type=\"hidden\" name=\"captcha_text2\" value=\"". md5( $user_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") ) . "\">\n";
				$text .= "<input type=\"hidden\" name=\"ergebnis\" value=\"$ergebnis\">\n";
				$text .= "</td>\n";
				$text .= "</tr>\n";
				
				if ($zaehler % 2 != 0) {
					$bgcolor = 'class="tabelle_zeile2"';
				} else {
					$bgcolor = 'class="tabelle_zeile1"';
				}
				$text .= "<tr>\n";
				$text .= "<td align=\"left\" $bgcolor>\n";
				$text .= "<b><input type=\"submit\" name=\"uebergabe\" value=\"$lang[login_nutzungsbestimmungen_ok]\"></b>\n";
				$text .= "</td>\n";
				$text .= "<td align=\"right\" $bgcolor>\n";
				$text .= "<input type=\"submit\" name=\"uebergabe\" value=\"$lang[login_nutzungsbestimmungen_abbruch]\">\n";
				$text .= "</td>\n";
				$text .= "</tr>\n";
				
				$text .= "</table>\n";
				$text .= "<input type=\"hidden\" name=\"username\" value=\"$username\">\n";
				$text .= "<input type=\"hidden\" name=\"passwort\" value=\"$passwort\">\n";
				$text .= "<input type=\"hidden\" name=\"eintritt\" value=\"$eintritt\">\n";
				$text .= "</form>\n";
				
				$rest_ausblenden = true;
			} else if ($uebergabe == $lang['login_nutzungsbestimmungen_ok']) {
				// Nutzungsbestimmungen wurden bestätigt
				$u_agb = "1";
			}
			
			if(!$rest_ausblenden) {
				// Benutzer in Blacklist überprüfen
				$query = pdoQuery("SELECT `f_text` FROM `blacklist` WHERE `f_blacklistid` = :f_blacklistid", [':f_blacklistid'=>$user_id]);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$result = $query->fetch();
					$infotext = "Blacklist: " . $result['f_text'];
					$warnung = true;
				}
				
				// Bei Login dieses Benutzers alle Admins (online, nicht Temp) warnen
				if ($warnung) {
					if ($eintritt == 'forum') {
						$raumname = " (" . $whotext[2] . ")";
					} else {
						$query = pdoQuery("SELECT `r_name` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>intval($eintritt)]);
						
						$resultCount = $query->rowCount();
						if ($resultCount > 0) {
							$result = $query->fetch();
							$raumname = " (" . $result['r_name'] . ") ";
						} else {
							$raumname = "";
						}
					}
					
					$query = pdoQuery("SELECT `o_user` FROM `online` WHERE (`o_level` = 'S' OR `o_level` = 'C')", []);
					
					$resultCount = $query->rowCount();
					if ($resultCount > 0) {
						$txt = str_replace("%ip_adr%", $ip_adr, $lang['login_warnung']);
						$txt = str_replace("%ip_name%", $ip_name, $txt);
						$txt = str_replace("%is_infotext%", $infotext, $txt);
						
						$result = $query->fetchAll();
						foreach($result as $zaehler => $row) {
							$nachricht = "<a href=\"inhalt.php?bereich=benutzer&aktion=benutzer_zeig&ui_id=$user_id\" target=\"chat\">$u_nick</a>";
							system_msg("", 0, $row['o_user'], $system_farbe, str_replace("%u_nick%", $nachricht . $raumname, $txt));
						}
					}
				}
				
				// Benutzer nicht gesperrt, weiter mit Login und Eintritt in ausgewählten Raum mit ID $eintritt
				$o_id = login($user_id, $u_level, $ip_historie, $u_agb, $u_punkte_monat, $u_punkte_jahr, $u_punkte_datum_monat, $u_punkte_datum_jahr, $u_punkte_gesamt);
				
				if ($eintritt == "forum") {
					// Login ins Forum
					
					$frameset = true;
					betrete_forum($o_id, $user_id, $u_nick, $u_level);
					
					require_once("./functions/functions-frameset.php");
					
					zeige_header($body_titel, 0);
					frameset_forum();
				} else {
					// Chat betreten
					
					$frameset = true;
					
					if( !isset($user_id) || $user_id == null || $user_id == "") {
						global $kontakt;
						email_senden($kontakt, "user_id leer beim Login1", "Nickname: " . $u_nick);
					}
					
					betrete_chat($o_id, $user_id, $u_nick, $u_level, $eintritt);
					
					require_once("./functions/functions-frameset.php");
					
					zeige_header($body_titel, 0);
					frameset_chat();
				}
			}
		}
	} else {
		// Login fehlgeschlagen
		// Falsches Passwort oder Benutzername
		unset($u_nick);
		
		$fehlermeldung = $lang['login_fehlermeldung_login_fehlgeschlagen'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
}
?>