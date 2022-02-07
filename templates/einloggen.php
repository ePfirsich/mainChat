<?php
// Direkten Aufruf der Datei verbieten
if( !isset($bereich)) {
	die;
}

// Sequence für online- und chat-Tabelle erzeugen
erzeuge_sequence("online", "o_id");
erzeuge_sequence("chat", "c_id");

$in_den_chat_einloggen = true;
$frameset = false;

// Prüfung, ob dieser Benutzer bereits existiert
$query = "SELECT `u_id` FROM `user` WHERE `u_nick`='$username'";
$result = sqlQuery($query);

if (mysqli_num_rows($result) == 0) {
	// Login als Gast
	if (!isset($passwort) || $passwort == "") {
		// Login als Gast
		if ($gast_login) {
			$remote_addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
			$http_user_agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
			// Prüfen, ob von der IP und dem Benutzer-Agent schon ein Gast online ist und ggf abweisen
			$query4711 = "SELECT o_id FROM online WHERE o_browser='" . $http_user_agent . "' AND o_ip='" . $remote_addr . "' AND o_level='G' AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
			$result = sqlQuery($query4711);
			if ($result) {
				$rows = mysqli_num_rows($result);
			}
			mysqli_free_result($result);
		}
		
		// Login abweisen, falls mehr als ein Gast online ist oder Gäste gesperrt sind
		if (!$gast_login || ($rows > 1 && !$gast_login_viele) || $temp_gast_sperre) {
			// Gäste sind gesperrt
			
			$fehlermeldung = $t['login_fehlermeldung_gastlogin'];
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
						$query4711 = "SELECT u_id FROM user WHERE u_nick='$username' ";
						$result = sqlQuery($query4711);
						$rows = mysqli_num_rows($result);
						$i++;
					}
					mysqli_free_result($result);
				} else {
					// freien Namen bestimmen
					$rows = 1;
					$i = 0;
					while ($rows != 0 && $i < 100) {
						$username = $t['login_gast'] . strval((mt_rand(1, 10000)) + 1);
						$query4711 = "SELECT `u_id` FROM `user` WHERE `u_nick`='" . escape_string($username) . "'";
						$result = sqlQuery($query4711);
						$rows = mysqli_num_rows($result);
						$i++;
					}
					mysqli_free_result($result);
				}
			}
			
			// Im Benutzername alle Sonderzeichen entfernen, vorsichtshalber nochmals prüfen
			$username = escape_string(coreCheckName($username, $check_name));
			
			// Benutzerdaten für Gast setzen
			$f['u_level'] = "G";
			$f['u_passwort'] = mt_rand(1, 10000);
			$f['u_nick'] = $username;
			$passwort = $f['u_passwort'];
			
			// Prüfung, ob dieser Benutzer bereits existiert
			$query4711 = "SELECT `u_id` FROM `user` WHERE `u_nick`='$f[u_nick]'";
			$result = sqlQuery($query4711);
			
			if (mysqli_num_rows($result) == 0) {
				// Account in DB schreiben
				schreibe_db("user", $f, "", "u_id");
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
		$rows = mysqli_num_rows($result);
	}
	
	if ($result && $rows == 1) {
		// Login Ok, Benutzerdaten setzen
		$row = mysqli_fetch_object($result);
		$user_id = $row->u_id;
		$u_nick = $row->u_nick;
		$u_level = $row->u_level;
		$u_agb = $row->u_agb;
		$u_punkte_monat = $row->u_punkte_monat;
		$u_punkte_jahr = $row->u_punkte_jahr;
		$u_punkte_datum_monat = $row->u_punkte_datum_monat;
		$u_punkte_datum_jahr = $row->u_punkte_datum_jahr;
		$u_punkte_gesamt = $row->u_punkte_gesamt;
		$ip_historie = unserialize($row->u_ip_historie);
		$nick_historie = unserialize($row->u_nick_historie);
		
		$f['u_id'] = $user_id;
		schreibe_db("user", $f, $user_id, "u_id");
		
		// Eingeloggt bleiben (nicht als Gast)
		if($f['u_level'] != "G" && $angemeldet_bleiben == 1) {
			$identifier = random_string();
			$securitytoken = random_string();
			
			$query = "SELECT * FROM `securitytokens` WHERE `user_id` = '$user_id'";
			$result = sqlQuery($query);
			
			if ($result && $rows = mysqli_num_rows($result) >= 1) {
				$query = "UPDATE `securitytokens` SET securitytoken = '" . sha1($securitytoken) . "', `identifier`='$identifier' WHERE `user_id` = '$user_id'";
			} else {
				$query = "INSERT INTO `securitytokens` SET `user_id`='$user_id', `identifier`='$identifier', `securitytoken`='" . sha1($securitytoken) . "'";
			}
			sqlUpdate($query);
			
			setcookie("identifier",$identifier,time()+(3600*24*365)); //1 Jahr Gültigkeit
			setcookie("securitytoken",$securitytoken,time()+(3600*24*365)); //1 Jahr Gültigkeit
		}
		
		// Benutzer online bestimmen
		if ($chat_max[$u_level] != 0) {
			$query = "SELECT COUNT(o_id) FROM online WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
			$result2 = sqlQuery($query);
			if ($result2) {
				$onlineanzahl = mysqli_result($result2, 0, 0);
			}
			mysqli_free_result($result2);
		}
		
		// Login erfolgreich ?
		if ($u_level == "Z") {
			// Benutzer gesperrt -> Fehlermeldung ausgeben
			$fehlermeldung = str_replace("%u_nick%", $u_nick, $t['login_fehlermeldung_login_account_gesperrt']);
			$text .= hinweis($fehlermeldung, "fehler");
		} else if ($chat_max[$u_level] != 0 && $onlineanzahl > $chat_max[$u_level]) {
			// Maximale Anzahl der Benutzer im Chat erreicht -> Fehlermeldung ausgeben
			$fehlermeldung = str_replace("%online%", $onlineanzahl, $t['login_fehlermeldung_login_zu_viele_benutzer_online']);
			$fehlermeldung = str_replace("%max%", $chat_max[$u_level], $fehlermeldung);
			$fehlermeldung = str_replace("%leveltxt%", $level[$u_level], $txt);
			$fehlermeldung = str_replace("%zusatztext%", $chat_max[zusatztext], $txt);
			
			$text .= hinweis($fehlermeldung, "fehler");
			
			unset($u_nick);
		} else {
			$rest_ausblenden = false;
			
			if (($temp_gast_sperre) && ($u_level == 'G')) {
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
			if ($uebergabe != $t['login_nutzungsbestimmungen_ok'] && $u_agb != "1") {
				// Nutzungsbestimmungen ausgeben
				
				if (!isset($eintritt)) {
					$eintritt = RaumNameToRaumID($lobby);
				}
				
				$zaehler = 0;
				
				$text .= $t['chat_agb'];
				$text .= "<form action=\"index.php\" target=\"_top\" method=\"post\">\n";
				$text .= "<input type=\"hidden\" name=\"aktion\" value=\"einloggen\">\n";
				$text .= "<table style=\"width:100%;\">\n";
				
				// Überschrift: Bestätigung der Nutzungsbestimmungen
				$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['login_bestaetigung_nutzungsbestimmungen'], "", "", 0, "70", "");
				
				if ($zaehler % 2 != 0) {
					$bgcolor = 'class="tabelle_zeile2"';
				} else {
					$bgcolor = 'class="tabelle_zeile1"';
				}
				$text .= "<tr>\n";
				$text .= "<td colspan=\"2\" $bgcolor>\n";
				$text .= "$t[captcha1]<br>";
				
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
				
				$text .= $tzahl[$zahl1] . " " . $taufgabe[$aufgabe] . " " . $tzahl[$zahl2] . " &nbsp;&nbsp;&nbsp;&nbsp;";
				
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
				$text .= "<b><input type=\"submit\" name=\"uebergabe\" value=\"$t[login_nutzungsbestimmungen_ok]\"></b>\n";
				$text .= "</td>\n";
				$text .= "<td align=\"right\" $bgcolor>\n";
				$text .= "<input type=\"submit\" name=\"uebergabe\" value=\"$t[login_nutzungsbestimmungen_abbruch]\">\n";
				$text .= "</td>\n";
				$text .= "</tr>\n";
				
				$text .= "</table>\n";
				$text .= "<input type=\"hidden\" name=\"username\" value=\"$username\">\n";
				$text .= "<input type=\"hidden\" name=\"passwort\" value=\"$passwort\">\n";
				$text .= "<input type=\"hidden\" name=\"eintritt\" value=\"$eintritt\">\n";
				$text .= "</form>\n";
				
				$rest_ausblenden = true;
			} else if ($uebergabe == $t['login_nutzungsbestimmungen_ok']) {
				// Nutzungsbestimmungen wurden bestätigt
				$u_agb = "1";
			} else {
				$u_agb = "0";
			}
			
			if(!$rest_ausblenden) {
				// Benutzer in Blacklist überprüfen
				$query2 = "SELECT f_text FROM blacklist WHERE f_blacklistid=$user_id";
				$result2 = sqlQuery($query2);
				if ($result2 AND mysqli_num_rows($result2) > 0) {
					$infotext = "Blacklist: " . mysqli_result($result2, 0, 0);
					$warnung = true;
				}
				if ($result2) {
					mysqli_free_result($result2);
				}
				
				// Bei Login dieses Benutzers alle Admins (online, nicht Temp) warnen
				if ($warnung) {
					if ($eintritt == 'forum') {
						$raumname = " (" . $whotext[2] . ")";
					} else {
						$query2 = "SELECT r_name FROM raum WHERE r_id=" . intval($eintritt);
						$result2 = sqlQuery($query2);
						if ($result2 AND mysqli_num_rows($result2) > 0) {
							$raumname = " (" . mysqli_result($result2, 0, 0) . ") ";
						} else {
							$raumname = "";
						}
						mysqli_free_result($result2);
					}
					
					$query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
					$result2 = sqlQuery($query2);
					if ($result2 AND mysqli_num_rows($result2) > 0) {
						$txt = str_replace("%ip_adr%", $ip_adr, $t['login_warnung']);
						$txt = str_replace("%ip_name%", $ip_name, $txt);
						$txt = str_replace("%is_infotext%", $infotext, $txt);
						while ($row2 = mysqli_fetch_object($result2)) {
							$ah1 = "<a href=\"inhalt.php?bereich=benutzer&aktion=benutzer_zeig&ui_id=$user_id\" target=\"chat\">";
							$ah2 = "</a>";
							system_msg("", 0, $row2->o_user, $system_farbe, str_replace("%u_nick%", $ah1 . $u_nick . $ah2 . $raumname, $txt));
						}
					}
					mysqli_free_result($result2);
				}
				
				// Benutzer nicht gesperrt, weiter mit Login und Eintritt in ausgewählten Raum mit ID $eintritt
				
				// Hash-Wert ermitteln
				$hash_id = id_erzeuge();
				
				// Login
				$o_id = login($user_id, $u_nick, $u_level, $hash_id, $ip_historie, $u_agb, $u_punkte_monat, $u_punkte_jahr, $u_punkte_datum_monat, $u_punkte_datum_jahr, $u_punkte_gesamt);
				
				if ($eintritt == "forum") {
					// Login ins Forum
					
					$frameset = true;
					betrete_forum($o_id, $user_id, $u_nick, $u_level);
					
					require_once("functions/functions-frameset.php");
					
					zeige_header($body_titel, 0);
					frameset_forum();
				} else {
					// Chat betreten
					
					$frameset = true;
					betrete_chat($o_id, $user_id, $u_nick, $u_level, $eintritt);
					
					require_once("functions/functions-frameset.php");
					
					zeige_header($body_titel, 0);
					frameset_chat();
				}
			}
		}
	} else {
		// Login fehlgeschlagen
		// Falsches Passwort oder Benutzername
		unset($u_nick);
		
		$fehlermeldung = $t['login_fehlermeldung_login_fehlgeschlagen'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
}
?>