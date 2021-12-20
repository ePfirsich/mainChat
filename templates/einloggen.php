<?php
// Direkten Aufruf der Datei verbieten
if( !isset($aktion)) {
	die;
}

// Sequence für online- und chat-Tabelle erzeugen
erzeuge_sequence("online", "o_id");
erzeuge_sequence("chat", "c_id");

$kein_gastlogin_ausblenden = false;

// Weiter mit login
if (!isset($passwort) || $passwort == "") {
	// Login als Gast
	
	// Falls Gast-Login erlaubt ist:
	if ($gast_login) {
		// Prüfen, ob von der IP und dem Benutzer-Agent schon ein Gast online ist und ggf abweisen
		$query4711 = "SELECT o_id FROM online WHERE o_browser='" . $_SERVER["HTTP_USER_AGENT"]
			. "' AND o_ip='" . $_SERVER["REMOTE_ADDR"] . "' AND o_level='G' AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
		$result = mysqli_query($mysqli_link, $query4711);
		if ($result) {
			$rows = mysqli_num_rows($result);
		}
		mysqli_free_result($result);
	}
	
	// Login abweisen, falls mehr als ein Gast online ist oder Gäste gesperrt sind
	if (!$gast_login || ($rows > 1 && !$gast_login_viele) || $temp_gast_sperre) {
		// Gäste sind gesperrt
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		$fehlermeldung = $t['login_fehlermeldung_gastlogin'];
		zeige_tabelle_volle_breite($t['login_fehlermeldung'], $fehlermeldung);
		
		$kein_gastlogin_ausblenden = true;
	}
	
	if(!$kein_gastlogin_ausblenden) {
		// Login als Gast
		
		// Im Benutzername alle Sonderzeichen entfernen, Länge prüfen
		if (!isset($login)) {
			$login = "";
		}
		$login = coreCheckName($login, $check_name);
		
		if (strlen($login) < 4 || strlen($login) > 20) {
			$login = "";
		}
		
		// Falls kein Benutzername übergeben, Nick finden
		if (strlen($login) == 0) {
			if ($gast_name_auto) {
				// Freien Benutzername bestimmen falls in der Config erlaubt
				$rows = 1;
				$i = 0;
				$anzahl = count($gast_name);
				while ($rows != 0 && $i < 100) {
					$login = $gast_name[mt_rand(1, $anzahl)];
					$query4711 = "SELECT u_id FROM user WHERE u_nick='$login' ";
					$result = mysqli_query($mysqli_link, $query4711);
					$rows = mysqli_num_rows($result);
					$i++;
				}
				mysqli_free_result($result);
			} else {
				// freien Namen bestimmen
				$rows = 1;
				$i = 0;
				while ($rows != 0 && $i < 100) {
					$login = $t['login_gast'] . strval((mt_rand(1, 10000)) + 1);
					$query4711 = "SELECT `u_id` FROM `user` WHERE `u_nick`='" . mysqli_real_escape_string($mysqli_link, $login) . "'";
					$result = mysqli_query($mysqli_link, $query4711);
					$rows = mysqli_num_rows($result);
					$i++;
				}
				mysqli_free_result($result);
			}
		}
		
		// Im Benutzername alle Sonderzeichen entfernen, vorsichtshalber nochmals prüfen
		$login = mysqli_real_escape_string($mysqli_link, coreCheckName($login, $check_name));
		
		// Benutzerdaten für Gast setzen
		$f['u_level'] = "G";
		$f['u_passwort'] = mt_rand(1, 10000);
		$f['u_nick'] = $login;
		$passwort = $f['u_passwort'];
		
		// Prüfung, ob dieser Benutzer bereits existiert
		$query4711 = "SELECT `u_id` FROM `user` WHERE `u_nick`='$f[u_nick]'";
		$result = mysqli_query($mysqli_link, $query4711);
		
		if (mysqli_num_rows($result) == 0) {
			// Account in DB schreiben
			schreibe_db("user", $f, "", "u_id");
		}
	}
}

if(!$kein_gastlogin_ausblenden) {
	// Login als registrierter Benutzer
	
	// Passwort prüfen und Benutzerdaten lesen
	$rows = 0;
	$result = auth_user($login, $passwort);
	if ($result) {
		$rows = mysqli_num_rows($result);
	}
	
	if ($result && $rows == 1) {
		// Login Ok, Benutzerdaten setzen
		$row = mysqli_fetch_object($result);
		$u_id = $row->u_id;
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
		
		$f['u_id'] = $u_id;
		schreibe_db("user", $f, $u_id, "u_id");
		
		// Benutzer online bestimmen
		if ($chat_max[$u_level] != 0) {
			$query = "SELECT count(o_id) FROM online WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
			$result2 = mysqli_query($mysqli_link, $query);
			if ($result2) {
				$onlineanzahl = mysqli_result($result2, 0, 0);
			}
			mysqli_free_result($result2);
		}
		
		// Login erfolgreich ?
		if ($u_level == "Z") {
			// Benutzer gesperrt -> Fehlermeldung ausgeben
			echo str_replace("%u_nick%", $u_nick, $t['login5']);
		} else if ($chat_max[$u_level] != 0 && $onlineanzahl > $chat_max[$u_level]) {
			// Maximale Anzahl der Benutzer im Chat erreicht -> Fehlermeldung ausgeben
			
			$txt = str_replace("%online%", $onlineanzahl, $t['login24']);
			$txt = str_replace("%max%", $chat_max[$u_level], $txt);
			$txt = str_replace("%leveltxt%", $level[$u_level], $txt);
			$txt = str_replace("%zusatztext%", $chat_max[zusatztext], $txt);
			echo $txt;
			
			unset($u_nick);
		} else {
			$rest_ausblenden = false;
			
			$captcha_text1 = filter_input(INPUT_POST, 'captcha_text1', FILTER_SANITIZE_STRING);
			$captcha_text2 = filter_input(INPUT_POST, 'captcha_text2', FILTER_SANITIZE_STRING);
			$ergebnis = filter_input(INPUT_POST, 'ergebnis', FILTER_SANITIZE_STRING);
			
			if (($temp_gast_sperre) && ($u_level == 'G')) {
				$captcha_text1 = "999";
			} // abweisen, falls Gastsperre aktiv
			
			if ( isset($captcha_text1) && ($captcha_text1 == "")) {
				$captcha_text1 = "999";
			} // abweisen, falls leere eingabe
			
			// Prüfen, ob alles korrekt eingegeben wurde.
			if ( isset($captcha_text2) && ( $captcha_text2 != md5( $u_id . "+code+" . $captcha_text1 . "+" . date("Y-m-d h") ) ) ) {
				$los = "";
			}
			
			// muss Benutzer/Gast noch Nutzungsbestimmungen bestätigen?
			if ($los != $t['login17'] && $u_agb != "1") {
				// Nutzungsbestimmungen ausgeben
				echo "<body>";
				
				// Gibt die Kopfzeile im Login aus
				zeige_kopfzeile_login();
				
				echo "<form action=\"index.php\" target=\"_top\" name=\"form1\" method=\"post\">\n";
				
				if (!isset($eintritt)) {
					$eintritt = RaumNameToRaumID($lobby);
				}
				
				
				$text .= $t['chat_agb'];
				
				$zaehler = 0;
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
				
				//echo md5( $u_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") );
				//echo md5( $u_id . "code" . $ergebnis . "+" . date("Y-m-d h") );
				$text .= "<input type=\"text\" name=\"captcha_text1\">\n";
				$text .= "<input type=\"hidden\" name=\"captcha_text2\" value=\"". md5( $u_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") ) . "\">\n";
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
				$text .= "<b><input type=\"submit\" name=\"los\" value=\"$t[login17]\"></b>\n";
				$text .= "</td>\n";
				$text .= "<td align=\"right\" $bgcolor>\n";
				$text .= "<input type=\"submit\" name=\"los\" value=\"$t[login18]\">\n";
				$text .= "</td>\n";
				$text .= "</tr>\n";
				
				$text .= "<input type=\"hidden\" name=\"login\" value=\"$login\">\n";
				$text .= "<input type=\"hidden\" name=\"passwort\" value=\"$passwort\">\n";
				$text .= "<input type=\"hidden\" name=\"eintritt\" value=\"$eintritt\">\n";
				$text .= "<input type=\"hidden\" name=\"aktion\" value=\"login\">\n";
				$text .= "</table>\n";
				$text .= "</form>\n";
				
				zeige_tabelle_volle_breite($t['login_nutzungsbestimmungen'], $text);
				
				$rest_ausblenden = true;
			} else if ($los == $t['login17']) {
				// Nutzungsbestimmungen wurden bestätigt
				$u_agb = "1";
			} else {
				$u_agb = "0";
			}
			
			if(!$rest_ausblenden) {
				// Benutzer in Blacklist überprüfen
				$query2 = "SELECT f_text FROM blacklist WHERE f_blacklistid=$u_id";
				$result2 = mysqli_query($mysqli_link, $query2);
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
						$query2 = "SELECT r_name from raum where r_id=" . intval($eintritt);
						$result2 = mysqli_query($mysqli_link, $query2);
						if ($result2 AND mysqli_num_rows($result2) > 0) {
							$raumname = " (" . mysqli_result($result2, 0, 0) . ") ";
						} else {
							$raumname = "";
						}
						mysqli_free_result($result2);
					}
					
					$query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
					$result2 = mysqli_query($mysqli_link, $query2);
					if ($result2 AND mysqli_num_rows($result2) > 0) {
						$txt = str_replace("%ip_adr%", $ip_adr, $t['login_warnung']);
						$txt = str_replace("%ip_name%", $ip_name, $txt);
						$txt = str_replace("%is_infotext%", $infotext, $txt);
						while ($row2 = mysqli_fetch_object($result2)) {
							$ur1 = "inhalt.php?seite=benutzer&id=<ID>&aktion=benutzer_zeig&user=$u_id";
							$ah1 = "<a href=\"$ur1\" target=\"chat\">";
							$ah2 = "</a>";
							system_msg("", 0, $row2->o_user, $system_farbe, str_replace("%u_nick%", $ah1 . $u_nick . $ah2 . $raumname, $txt));
						}
					}
					mysqli_free_result($result2);
				}
			
				// Benutzer nicht gesperrt, weiter mit Login und Eintritt in ausgewählten Raum mit ID $eintritt
			
				// Hash-Wert ermitteln
				$hash_id = id_erzeuge($u_id);
				
				// Login
				$o_id = login($u_id, $u_nick, $u_level, $hash_id, $ip_historie, $u_agb, $u_punkte_monat, $u_punkte_jahr, $u_punkte_datum_monat, $u_punkte_datum_jahr, $u_punkte_gesamt);
				
				if ($eintritt == "forum") {
					// Login ins Forum
					betrete_forum($o_id, $u_id, $u_nick, $u_level);
					
					require_once("functions/functions-frameset.php");
					
					frameset_forum($hash_id);
					
				} else {
					// Chat betreten
					betrete_chat($o_id, $u_id, $u_nick, $u_level, $eintritt);
					$back = 1;
					
					require_once("functions/functions-frameset.php");
					
					frameset_chat($hash_id);
				}
			}
		}
	} else {
		// Login fehlgeschlagen
		
		unset($u_nick);
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		// Falsches Passwort oder Benutzername
		zeige_tabelle_volle_breite($t['login_fehlermeldung'], $t['login_fehlermeldung_login_fehlgeschlagen']);
		
		// Box für Login
		zeige_chat_login();
	}
}
?>