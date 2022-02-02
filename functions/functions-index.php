<?php
// Funktionen nur für index.php
require_once("functions/functions-func-nachricht.php");

function erzeuge_sequence($db, $id) {
	// Funktion erzeugt einen Datensatz in der Tabelle squence mit der nächsten freien ID
	
	$query = "SELECT se_nextid FROM sequence WHERE se_name='$db'";
	$result = sqlQuery($query);
	if (!($result && mysqli_num_rows($result) == 1)) {
		// Tabelle neu anlegen
		$query = "CREATE TABLE sequence (" . "se_name varchar(127) NOT NULL default ''," . "se_nextid int(10) unsigned NOT NULL default '0'," . "PRIMARY KEY (se_name));";
		$result = sqlUpdate($query);
		
		// Sperren
		$query = "LOCK TABLES $db,sequence WRITE";
		$result = sqlUpdate($query, true);
		
		// Höchste ID lesen
		$query = "SELECT max($id) FROM $db";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$temp = mysqli_result($result, 0, 0) + 1;
		}
		if ($temp == "NULL" || !$temp) {
			$temp = 0;
		}
		
		// ID eintragen
		$query = "INSERT INTO sequence (se_name,se_nextid) VALUES ('$db','$temp')";
		$result = sqlUpdate($query);
		
		// Sperre aufheben
		$query = "UNLOCK TABLES";
		$result = sqlUpdate($query, true);
		
	}
	mysqli_free_result($result);
}

function login($user_id, $u_nick, $u_level, $hash_id, $u_ip_historie, $u_agb, $u_punkte_monat, $u_punkte_jahr, $u_punkte_datum_monat, $u_punkte_datum_jahr, $u_punkte_gesamt) {
	// In das System einloggen
	// $o_id wird zurückgeliefert
	// u_id=Benutzer-ID, u_nick ist Benutzername, u_level ist Level, hash_id ist Session-ID
	// ip_historie ist Array mit IPs alter Logins, u_agb ist Nutzungsbestimmungen gelesen Y/N
	
	global $punkte_gruppe;
	
	// IP/Browser Adresse des Benutzer setzen
	$remote_addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
	$http_user_agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
	
	$http_user_agent = str_replace("MSIE 8.0", "MSIE 7.0", $http_user_agent);
	
	$http_stuff = array();
	$http_stuff['HTTP_COOKIE'] = filter_input(INPUT_SERVER, 'HTTP_COOKIE', FILTER_SANITIZE_STRING);
	$http_stuff['HTTP_ORIGIN'] = filter_input(INPUT_SERVER, 'HTTP_ORIGIN', FILTER_SANITIZE_STRING);
	$http_stuff['HTTP_CONTENT_TYPE'] = filter_input(INPUT_SERVER, 'HTTP_CONTENT_TYPE', FILTER_SANITIZE_STRING);
	$http_stuff['HTTP_USER_AGENT'] = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
	$http_stuff['HTTP_HOST'] = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
	$http_stuff['REDIRECT_STATUS'] = filter_input(INPUT_SERVER, 'REDIRECT_STATUS', FILTER_SANITIZE_STRING);
	$http_stuff['REMOTE_ADDR'] = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
	$http_stuff['HTTPS'] = filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_STRING);
	$http_stuff['REQUEST_SCHEME'] = filter_input(INPUT_SERVER, 'REQUEST_SCHEME', FILTER_SANITIZE_STRING);
	$http_stuff['DOCUMENT_URI'] = filter_input(INPUT_SERVER, 'DOCUMENT_URI', FILTER_SANITIZE_STRING);
	$http_stuff['PATH_INFO'] = filter_input(INPUT_SERVER, 'PATH_INFO', FILTER_SANITIZE_STRING);
	$http_stuff['REQUEST_TIME_FLOAT'] = filter_input(INPUT_SERVER, 'REQUEST_TIME_FLOAT', FILTER_SANITIZE_STRING);
	$http_stuff['REQUEST_TIME'] = filter_input(INPUT_SERVER, 'REQUEST_TIME', FILTER_SANITIZE_STRING);
	
	// Aktionen initialisieren, nicht für Gäste
	if ($u_level != "G") {
		$query = "SELECT a_id FROM aktion WHERE a_user=$user_id ";
		$result = sqlQuery($query);
		if ($result && (mysqli_num_rows($result) == 0 || mysqli_num_rows($result) > 20)) {
			sqlUpdate("INSERT INTO aktion set a_user=$user_id, a_wann='Sofort/Online', a_was='Freunde', a_wie='OLM'");
			sqlUpdate("INSERT INTO aktion set a_user=$user_id, a_wann='Login', a_was='Freunde', a_wie='OLM'");
			sqlUpdate("INSERT INTO aktion set a_user=$user_id, a_wann='Sofort/Online', a_was='Neue Mail', a_wie='OLM'");
			sqlUpdate("INSERT INTO aktion set a_user=$user_id, a_wann='Login',	a_was='Neue Mail', a_wie='OLM'");
			sqlUpdate("INSERT INTO aktion set a_user=$user_id, a_wann='Alle 5 Minuten', a_was='Neue Mail', a_wie='OLM'");
		}
		mysqli_free_result($result);
	}
	
	// Prüfen, ob Benutzer noch online ist und ggf. ausloggen
	$alteloginzeit = "";
	$query = "SELECT o_id, o_login FROM online WHERE o_user=$user_id ";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) != 0) {
		$alteloginzeit = mysqli_result($result, 0, 1);
		logout(mysqli_result($result, 0, 0), $user_id);
	}
	mysqli_free_result($result);
	
	// Benutzerdaten ändern
	
	// Login als letzten Login merken, dabei away und loginfehler zurücksetzen.
	$query = "UPDATE user SET u_login=NOW(),u_away='' WHERE u_id=$user_id";
	$result = sqlUpdate($query, true);
	if (!$result) {
		echo "Fehler beim Login: $query<br>";
		exit;
	}
	
	// Punkte des Vormonats/Vorjahres löschen und Benutzergruppe ermitteln, falls nicht Gast
	if ($u_level != "G") {
		// Ist u_punkte_monat vom aktuellen Monat?
		if ($u_punkte_datum_monat != date("n", time())) {
			$f['u_punkte_datum_monat'] = date("n", time());
			$f['u_punkte_monat'] = 0;
		}
		
		// Ist u_punkte_jahr vom aktuellen Jahr?
		if ($u_punkte_datum_jahr != date("Y", time())) {
			$f['u_punkte_datum_jahr'] = date("Y", time());
			$f['u_punkte_jahr'] = 0;
		}
		
		// Aus der Zahl der Gesamtpunkten die Benutzergruppe ableiten und in u_punkte_gruppe
		// speichern
		$f['u_punkte_gruppe'] = 0;
		foreach ($punkte_gruppe as $key => $value) {
			if ($u_punkte_gesamt < $value) {
				break;
			} else {
				$f['u_punkte_gruppe'] = dechex($key);
			}
		}
	}
	
	// Aktuelle IP/Datum zu ip_historie hinzufügen
	$datum = time();
	$ip_historie_neu[$datum] = $remote_addr;
	if (is_array($u_ip_historie)) {
		$i = 0;
		while (($i < 3) AND list($datum, $ip_adr) = each($u_ip_historie)) {
			$ip_historie_neu[$datum] = $ip_adr;
			$i++;
		}
	}
	
	// u_agb, ip_historie und Temp-Admin schreiben
	if ($u_agb == "1") {
		$f['u_agb'] = $u_agb;
	}
	$f['u_ip_historie'] = serialize($ip_historie_neu);
	if ($u_level == "A") {
		// falls Status bisher Temp-Admin -> zurücksetzen.
		$f['u_level'] = "U";
		$u_level = "U";
	}
	$user_id = schreibe_db("user", $f, $user_id, "u_id");
	if (!$user_id) {
		email_senden($kontakt_email, $kontakt_betreff, "Fataler Fehler beim Login:<PRE>" . print_r($f) . "</PRE>");
		exit;
	}
	
	// Aktuelle Daten des Benutzers aus Tabelle iignore lesen
	// Query muss mit Code in ignore übereinstimmen
	$query = "SELECT i_user_passiv FROM iignore WHERE i_user_aktiv=$user_id";
	$result = sqlQuery($query);
	if (!$result) {
		email_senden($kontakt_email, $kontakt_betreff, "Fehler beim Login (iignore): $query<br>");
		exit;
	} else {
		if (mysqli_num_rows($result) == 0) {
			$ignore[0] = FALSE;
		} else {
			while ($iignore = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$ignore[$iignore[i_user_passiv]] = TRUE;
			}
		}
	}
	
	$knebelzeit = NULL;
	// Aktuelle Benutzerdaten aus Tabelle user lesen
	// Query muss mit Code in schreibe_db übereinstimmen
	$query = "SELECT `u_id`, `u_nick`, `u_level`, `u_farbe`, `u_zeilen`, `u_away`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_chathomepage`, `u_punkte_anzeigen` FROM `user` WHERE `u_id`=$user_id";
	$result = sqlQuery($query);
	if (!$result) {
		global $kontakt_email, $kontakt_betreff;
		email_senden($kontakt_email, $kontakt_betreff, "Fehler beim Login: $query");
		exit;
	} else {
		$userdata = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$userdata_array = zerlege(serialize($userdata));
		$http_stuff_array = zerlege(serialize($http_stuff));
		
		if (!isset($http_stuff_array[0])) {
			$http_stuff_array[0] = "";
		}
		if (!isset($http_stuff_array[1])) {
			$http_stuff_array[1] = "";
		}
		if (!isset($userdata_array[0])) {
			$userdata_array[0] = "";
		}
		if (!isset($userdata_array[1])) {
			$userdata_array[1] = "";
		}
		if (!isset($userdata_array[2])) {
			$userdata_array[2] = "";
		}
		if (!isset($userdata_array[3])) {
			$userdata_array[3] = "";
		}
		
		// Hole Knebelzeit aus Benutzertabelle
		$query = "SELECT `u_knebel` FROM `user` WHERE `u_id`=$user_id";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
			$knebelzeit = $row->u_knebel;
		}
		mysqli_free_result($result);
	}
	
	// Vorbereitung für Login ist abgeschlossen. Jetzt nochmals Prüfen ob Benutzer online ist, 
	// ggf. Session löschen und neue Session schreiben
	
	// Tabellen online+user exklusiv locken
	$query = "LOCK TABLES `online` WRITE, user WRITE";
	$result = sqlUpdate($query, true);
	$query = "DELETE FROM `online` WHERE o_user=$user_id";
	$result = sqlUpdate($query, true);
	
	// Benutzer in in Tabelle online merken -> Benutzer ist online
	unset($f);
	$f['o_user'] = $user_id;
	$f['o_raum'] = 0;
	$f['o_hash'] = $hash_id;
	$f['o_ip'] = $remote_addr;
	$f['o_who'] = "1";
	$f['o_browser'] = $http_user_agent;
	$f['o_name'] = $userdata['u_nick'];
	$f['o_level'] = $userdata['u_level'];
	$f['o_http_stuff'] = $http_stuff_array[0];
	$f['o_http_stuff2'] = $http_stuff_array[1];
	$f['o_userdata'] = $userdata_array[0];
	$f['o_userdata2'] = $userdata_array[1];
	$f['o_userdata3'] = $userdata_array[2];
	$f['o_userdata4'] = $userdata_array[3];
	$f['o_ignore'] = serialize($ignore);
	$f['o_punkte'] = 0; // Zähler mit 0 Punkten neu initialisieren
	$f['o_aktion'] = time(); // 5-min Aktionen initialisieren
	
	$o_id = schreibe_db("online", $f, "", "o_id");
	if (!$o_id) {
		email_senden($kontakt, "Fataler Fehler beim Login", $f);
		exit;
	}
	
	// Timestamps im Datensatz aktualisieren -> Benutzer gilt als eingeloggt
	if ($alteloginzeit != "") {
		$query = "UPDATE online SET o_aktiv=NULL, o_login='$alteloginzeit', o_knebel='$knebelzeit', o_timeout_zeit=DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), o_timeout_warnung = 0 WHERE o_user=$user_id ";
	} else {
		$query = "UPDATE online SET o_aktiv=NULL, o_login=NULL, o_knebel='$knebelzeit', o_timeout_zeit=DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), o_timeout_warnung = 0 WHERE o_user=$user_id ";
	}
	$result = sqlUpdate($query);
	
	// Lock freigeben
	$query = "UNLOCK TABLES";
	$result = sqlUpdate($query, true);
	
	// Bei Admins Cookie setzen zur Überprüfung der Session
	if (false && ( $userdata['u_level'] == "C" || $userdata['u_level'] == "S") ) {
		$cookie_name = "MAINCHAT" . $userdata['u_nick'];
		$cookie_inhalt = md5($o_id . $hash_id . "42");
		setcookie($cookie_name, $cookie_inhalt, 0, "/");
	}
	
	return $o_id;
}

function betrete_chat($o_id, $user_id, $u_nick, $u_level, $raum) {
	// Benutzer $user_id betritt Raum $raum (r_id)
	// Nachricht in Raum $raum wird erzeugt
	// Zeiger auf letzte Zeile wird zurückgeliefert
	
	global $lobby, $eintrittsraum, $t, $hash_id, $system_farbe, $u_punkte_gesamt;
	global $raum_eintrittsnachricht_kurzform, $raum_eintrittsnachricht_anzeige_deaktivieren;
	
	// Falls eintrittsraum nicht definiert, lobby voreinstellen
	if (strlen($eintrittsraum) == 0) {
		$eintrittsraum = $lobby;
	}
	
	// Ist $raum geschlossen oder Benutzer ausgesperrt?
	// ausnahme: geschlossener Raum ist Eingangsraum -> für e-rotic-räume
	// die sind geschlossen, aber bei manchen kostenlosen chats default :-(
	// geschlossener Raum betreten werden
	if (strlen($raum) > 0) {
		$query4711 = "SELECT r_id,r_status1,r_besitzer,r_name,r_min_punkte FROM raum WHERE r_id=$raum";
		$result = sqlQuery($query4711);
		if ($result && mysqli_num_rows($result) > 0) {
			$rows = mysqli_fetch_object($result);
			$r_min_punkte = $rows->r_min_punkte;
			if ($rows->r_name != $eintrittsraum) {
				switch ($rows->r_status1) {
					case "G":
					case "M":
					// Grundsätzlich nicht in geschlossene oder moderierte räume
						$raumeintritt = false;
						
						// es sei denn, man ist dorthin eingeladen
						$query2911 = "SELECT inv_user FROM invite WHERE inv_raum=$rows->r_id AND inv_user=$user_id";
						$result2911 = sqlQuery($query2911);
						if ($result2911 > 0) {
							if (mysqli_num_rows($result2911) > 0) {
								$raumeintritt = true;
							}
							mysqli_free_result($result);
						}
						
						// oder man ist Raumbesiter dort
						if ($rows->r_besitzer == $user_id) {
							$raumeintritt = true;
						}
						
						// abweisen wenn $raumeintritt = false
						if (!$raumeintritt) {
							system_msg("", 0, $user_id, $system_farbe, str_replace("%r_name_neu%", $rows->r_name, $t['raum_gehe4']));
							unset($raum);
						}
						break;
					default:
						break;
				}
			}
		}
		mysqli_free_result($result);
	}
	
	if (strlen($raum) > 0) {
		// Prüfung ob Benutzer aus Raum ausgesperrt ist
		
		$query4711 = "SELECT s_id FROM sperre WHERE s_raum=" . intval($raum) . " AND s_user=$user_id";
		$result = sqlQuery($query4711);
		if ($result > 0) {
			$rows = mysqli_num_rows($result);
			if (($rows != 0) || ($r_min_punkte > $u_punkte_gesamt)) {
				// Aktueller Raum ist gesperrt, oder zu wenige Punkte
				
				// Ist Benutzer aus Raum ausgesperrt, dann nicht einfach den Eintrittsraum oder die Lobby nehmen,
				// da kann der Benutzer auch ausgesperrt sein.
				
				unset($raum);
				
				$query1222b = "SELECT r_id FROM raum LEFT JOIN sperre ON r_id = s_raum and s_user = '$user_id' WHERE r_status1 = 'O' and r_status2 = 'P' and r_min_punkte <= $u_punkte_gesamt AND s_id is NULL ORDER BY r_id ";
				$result1222b = sqlQuery($query1222b);
				if (($result1222b > 0) && (mysqli_num_rows($result1222b) > 0)) {
					// Es gibt Räume, für die man noch nicht gesperrt ist.
					// hiervon den ersten nehmen
					$raum = mysqli_result($result1222b, 0, 0);
				}
				mysqli_free_result($result1222b);
			}
			mysqli_free_result($result);
		}
	}
	
	// Welchen Raum betreten?
	if (strlen($raum) == 0) {
		// Id des Eintrittsraums als Voreinstellung ermitteln
		$query4711 = "SELECT r_id,r_name,r_eintritt,r_topic FROM raum WHERE r_name='$eintrittsraum' ";
		$result = sqlQuery($query4711);
		if ($result)
			$rows = mysqli_num_rows($result);
		
		// eintrittsraum nicht gefunden? -> lobby probieren.
		if ($rows == 0) {
			$query4711 = "SELECT r_id,r_name,r_eintritt,r_topic FROM raum WHERE r_name='$lobby' ";
			$result = sqlQuery($query4711);
			if ($result)
				$rows = mysqli_num_rows($result);
		}
		// Lobby  nicht gefunden? --> Lobby anlegen.
		if ($rows == 0) {
			// Lobby neu anlegen
			$query4711 = "INSERT INTO raum (r_id,r_name,r_eintritt,r_austritt,r_status1,r_besitzer,r_topic,r_status2,r_smilie) VALUES (0,'$lobby','Willkommen','','O',1,'Eingangshalle','P',1)";
			$result = sqlUpdate($query4711);
			// neu lesen.
			$query4711 = "SELECT r_id,r_name,r_eintritt,r_topic FROM raum WHERE r_name='$lobby' ";
			$result = sqlQuery($query4711);
			if ($result) {
				$rows = mysqli_num_rows($result);
			}
		}
		
	} else {
		// Gewählten Raum ermitteln
		$query4711 = "SELECT r_id,r_name,r_eintritt,r_topic FROM raum WHERE r_id=$raum ";
		$result = sqlQuery($query4711);
		if ($result) {
			$rows = mysqli_num_rows($result);
		}
	}
	
	if ($result && $rows == 1) {
		$r_id = mysqli_result($result, 0, "r_id");
		$r_eintritt = mysqli_result($result, 0, "r_eintritt");
		$r_topic = mysqli_result($result, 0, "r_topic");
		$r_name = mysqli_result($result, 0, "r_name");
		mysqli_free_result($result);
	} else {
		?>
		<body><p>Fehler: Ungültige Raum-ID <?php echo $raum; ?> beim Login!</p></body>
		<?php
		exit;
	}
	
	// Aktuellen Raum merken, o_who auf chat setzen
	$f['o_raum'] = $r_id;
	$f['o_who'] = "0";
	
	// Nachricht im Chat ausgeben
	$ergebnis = nachricht_betrete($user_id, $r_id, $u_nick, $r_name);
	
	// ID der Eintrittsnachricht merken und online-Datensatz schreiben
	$f['o_chat_id'] = $ergebnis;
	schreibe_db("online", $f, $o_id, "o_id");
	
	// Topic vorhanden? ausgeben
	if (strlen($r_topic) > 0) {
		system_msg("", 0, $user_id, "", "<br><b>$t[betrete_chat3] $r_name:</b> $r_topic");
	}
	
	// Eintrittsnachricht
	if ($t['betrete_chat1']) {
		$txt = $t['betrete_chat1'] . " " . $r_name . ":";
	} else {
		unset($txt);
	}
	
	if ($raum_eintrittsnachricht_kurzform == "1") {
		unset($txt);
	}
	
	if ($raum_eintrittsnachricht_anzeige_deaktivieren == "1") {
	} else if (strlen($r_eintritt) > 0) {
		system_msg("", 0, $user_id, "", "<br><b>$txt $r_eintritt, $u_nick!</b><br>");
	} else {
		system_msg("", 0, $user_id, "", "<br><b>$txt</b> $t[betrete_chat2], $u_nick!</b><br>");
	}
	
	// Wer ist alles im Raum?
	raum_user($r_id, $user_id);
	
	// Hat der Benutzer sein Profil ausgefüllt?
	if ($u_level != "G") {
		profil_neu($user_id, $u_nick, $hash_id);
	}
	
	// Hat der Benutzer Aktionen für den Login eingestellt, wie Nachricht bei neuer Mail oder Freunden an sich selbst?
	if ($u_level != "G") {
		aktion($user_id, "Login", $user_id, $u_nick, $hash_id);
	}
	
	// Nachrichten an Freude verschicken
	$query = "SELECT f_id,f_text,f_userid,f_freundid,f_zeit FROM freunde WHERE f_userid=$user_id AND f_status = 'bestaetigt' UNION SELECT f_id,f_text,f_userid,f_freundid,f_zeit FROM freunde WHERE f_freundid=$user_id AND f_status = 'bestaetigt' ORDER BY f_zeit desc ";
	$result = sqlQuery($query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_object($result)) {
			unset($f);
			$f['raum'] = $r_name;
			$f['aktion'] = "Login";
			$f['f_text'] = $row->f_text;
			if ($row->f_userid == $user_id) {
				if (ist_online($row->f_freundid)) {
					$wann = "Sofort/Online";
					$an_u_id = $row->f_freundid;
				} else {
					$wann = "Sofort/Offline";
					$an_u_id = $row->f_freundid;
				}
			} else {
				if (ist_online($row->f_userid)) {
					$wann = "Sofort/Online";
					$an_u_id = $row->f_userid;
				} else {
					$wann = "Sofort/Offline";
					$an_u_id = $row->f_userid;
				}
			}
			// Aktion ausführen
			aktion($user_id, $wann, $an_u_id, $u_nick, "", "Freunde", $f);
		}
	}
	mysqli_free_result($result);
}

function id_erzeuge() {
	// Erzeugt eindeutige ID für jeden Benutzer
	
	$id = md5(uniqid(mt_rand()));
	return $id;
}

function betrete_forum($o_id, $user_id, $u_nick, $u_level) {
	// Benutzer betritt beim Login das Forum
	global $lobby, $eintrittsraum, $t, $hash_id, $system_farbe;
	
	//Daten in onlinetabelle schreiben
	$f['o_raum'] = -1;
	$f['o_who'] = "2";
	
	//user betritt nicht chat, sondern direkt forum
	$f['o_chat_id'] = system_msg("", 0, $user_id, "", str_replace("%u_nick%", $u_nick, $t['betrete_forum1']));
	schreibe_db("online", $f, $o_id, "o_id");
	
	// Hat der Benutzer Aktionen für den Login eingestellt, wie Nachricht bei neuer Mail oder Freunden an sich selbst?
	if ($u_level != "G") {
		aktion($user_id, "Login", $user_id, $u_nick, $hash_id);
	}
	
	// Nachrichten an Freude verschicken
	$query = "SELECT f_id,f_text,f_userid,f_freundid,f_zeit FROM freunde WHERE f_userid=$user_id AND f_status = 'bestaetigt' UNION SELECT f_id,f_text,f_userid,f_freundid,f_zeit FROM freunde WHERE f_freundid=$user_id AND f_status = 'bestaetigt' ORDER BY f_zeit desc ";
	$result = sqlQuery($query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		
		while ($row = mysqli_fetch_object($result)) {
			unset($f);
			$f['raum'] = "";
			$f['aktion'] = "Login";
			$f['f_text'] = $row->f_text;
			if ($row->f_userid == $user_id) {
				if (ist_online($row->f_freundid)) {
					$wann = "Sofort/Online";
					$an_u_id = $row->f_freundid;
				} else {
					$wann = "Sofort/Offline";
					$an_u_id = $row->f_freundid;
				}
			} else {
				if (ist_online($row->f_userid)) {
					$wann = "Sofort/Online";
					$an_u_id = $row->f_userid;
				} else {
					$wann = "Sofort/Offline";
					$an_u_id = $row->f_userid;
				}
			}
			// Aktion ausführen
			aktion($user_id, $wann, $an_u_id, $u_nick, "", "Freunde", $f);
		}
	}
	mysqli_free_result($result);
}

function RaumNameToRaumID($eintrittsraum) {
	// Holt anhand der Globalen Lobby Raumbezeichnung die Passende Raum ID
	
	$lobby_id = False;
	$eintrittsraum = escape_string($eintrittsraum);
	$query = "SELECT r_id FROM raum WHERE r_name = '$eintrittsraum' ";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) == 1) {
		$lobby_id = mysqli_result($result, 0, "r_id");
	}
	mysqli_free_result($result);
	return ($lobby_id);
}

function auth_user($username, $passwort) {
	// Passwort prüfen und Benutzerdaten lesen
	// Funktion liefert das mysqli_result zurück, wenn auf EINEN Benutzer das login/passwort passt
	
	// Übergebenes Passwort hashen und gegen das gespeicherte Passwort prüfen
	$v_passwort = encrypt_password($passwort);
	
	$query = "SELECT * FROM `user` WHERE `u_nick` = '" . escape_string($username) . "' AND `u_passwort` = '" . escape_string($v_passwort) . "'";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) == 1) {
		return ($result);
	} else {
		return (0);
	}
}

function zeige_chat_login($text = "") {
	global $t, $eintrittsraum, $eintritt, $forumfeatures, $gast_login, $temp_gast_sperre;
	global $lobby, $timeout, $whotext, $layout_kopf, $neuregistrierung_deaktivieren, $abweisen, $unterdruecke_raeume;
	
	// Kopfzeile
	if ($neuregistrierung_deaktivieren) {
		$login_titel = $t['login_formular_kopfzeile'];
	} else {
		$login_titel = $t['login_formular_kopfzeile'] . " [<a href=\"index.php?bereich=registrierung\">" . $t['login_neuen_benutzernamen_registrieren'] . "</a>]";
	}
	$login_titel .= " [<a href=\"index.php?bereich=passwort-vergessen\">" . $t['login_passwort_vergessen'] . "</a>]";
	
	
	// Räume
	if ($forumfeatures) {
		$raeume_titel = $t['login_raum_forum'];
	} else {
		$raeume_titel = $t['login_raum'];
	}
	
	// Falls der Eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
	if (strlen($eintrittsraum) == 0) {
		$eintrittsraum = $lobby;
	}
	
	// Raumauswahlliste erstellen
	$query = "SELECT r_name,r_id FROM raum WHERE (r_status1='O' OR r_status1='E' OR r_status1 LIKE BINARY 'm') AND r_status2='P' ORDER BY r_name";
	$result = sqlQuery($query);
	
	if ($result) {
		$rows = mysqli_num_rows($result);
	} else {
		// Kein öffentlicher Raum gefunden
		die;
	}

	$raeume = "<select name=\"eintritt\">";
	if ($forumfeatures) {
		$raeume .= "<option value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
	}
	
	if ($rows > 0) {
		$i = 0;
		while ($i < $rows) {
			$r_id = mysqli_result($result, $i, "r_id");
			$r_name = mysqli_result($result, $i, "r_name");
			if ((!isset($eintritt) && $r_name == $eintrittsraum)
				|| (isset($eintritt) && $r_id == $eintritt)) {
					$raeume .= "<option selected value=\"$r_id\">$r_name\n";
				} else {
					$raeume .= "<option value=\"$r_id\">$r_name\n";
				}
				$i++;
		}
	}
	if ($forumfeatures) {
		$raeume .= "<option value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
	}
	$raeume .= "</select>";
	mysqli_free_result($result);
	
	// Zeigt die Willkommensnachricht auf der Eingangsseite aus
	if (isset($layout_kopf) && $layout_kopf != "") {
		// Eigenen Text anzeigen
		$text .= $layout_kopf;
	} else {
		// Willkommen wird nur angezeigt, wenn kein eigener Kopf definiert ist
		$text.= "<h2>" . $t['willkommen'] . "</h2><br>\n";
	}
	
	// Benutzerlogin
	$zaehler = 0;
	$text .= "<form action=\"index.php?bereich=einloggen\" target=\"_top\" method=\"post\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Login ioder neu registrierten/Passwort vergessen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $login_titel, "", "", 0, "70", "");
	
	// Benutzername
	$text .= zeige_formularfelder("input", $zaehler, $t['login_benutzername'], "username", "");
	$zaehler++;
	
	// Passwort
	$text .= zeige_formularfelder("password", $zaehler, $t['login_passwort'], "passwort", "");
	$zaehler++;
	
	// Räume
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor style=\"text-align:right; width:300px;\">$raeume_titel</td>\n";
	$text .= "<td $bgcolor>$raeume</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Login
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor></td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"". $t['login_login'] . "\"></td>\n";
	$text .= "</tr>\n";
	
	$text .= "</table>\n";
	$text .= "</form>\n";
	
	if($gast_login && !$temp_gast_sperre) {
		// Gastlogin
		$text .= "<form action=\"index.php?bereich=einloggen\" target=\"_top\" method=\"post\">\n";
		$text .= "<input type=\"hidden\" name=\"username\" value=\"\">\n";
		$text .= "<input type=\"hidden\" name=\"passwort\" value=\"\">\n";
		$text .= "<table style=\"width:100%;\">\n";
		
		// Überschrift: Gastlogin
		$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['login_gastlogin'], "", "", 0, "70", "");
		
		// Hinweis zum Gastlogin
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\" $bgcolor>$t[login_gastinformation]</td>\n";
		$text .= "</tr>\n";
		
		// Räume
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor style=\"text-align:right; width:300px;\">$raeume_titel</td>\n";
		$text .= "<td $bgcolor>$raeume</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		// Login
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor></td>\n";
		$text .= "<td $bgcolor><input type=\"submit\" value=\"". $t['login_login'] . "\"></td>\n";
		$text .= "</tr>\n";
		
		$text .= "</table>\n";
		$text .= "</form>\n";
	}
	
	// Wie viele Benutzer sind im Chat registriert?
	$query = "SELECT COUNT(u_id) FROM `user` WHERE `u_level` IN ('A','C','G','M','S','U')";
	$result = sqlQuery($query);
	$rows = mysqli_num_rows($result);
	if ($result) {
		$useranzahl = mysqli_result($result, 0, 0);
		mysqli_free_result($result);
		
		// Benutzer online und Räume bestimmen -> merken
		$query = "SELECT o_who,o_name,o_level,r_name,r_status1,r_status2, r_name='" . escape_string($lobby) . "' AS lobby "
			. "FROM online LEFT JOIN raum ON o_raum=r_id WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ORDER BY lobby desc,r_name,o_who,o_name ";
		$result2 = sqlQuery($query);
		if ($result2) {
			$onlineanzahl = mysqli_num_rows($result2);
		}
		// Anzahl der angemeldeten Benutzer ausgeben
		if ($useranzahl > 1) {
			$text .= "<table style=\"width:100%;\">\n";
			
			// Leerzeile
			$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
			
			// Überschrift: Statistik
			$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['login_statistik'], "", "", 0, "70", "");
			
			$textStatistik = str_replace("%useranzahl%", $useranzahl, $t['login_benutzer_registriert']);
			
			// Anzahl der Beiträge im Forum ausgeben
			if ($forumfeatures) {
				// Anzahl Themen
				$query = "SELECT COUNT(th_id) FROM `forum_foren`";
				$result = sqlQuery($query);
				if ($result AND mysqli_num_rows($result) > 0) {
					$themen = mysqli_result($result, 0, 0);
					mysqli_free_result($result);
				}
				
				// Dummy Themen abziehen
				$query = "SELECT COUNT(th_id) FROM `forum_foren` WHERE th_name = 'dummy-thema'";
				$result = sqlQuery($query);
				if ($result AND mysqli_num_rows($result) > 0) {
					$themen = $themen - mysqli_result($result, 0, 0);
					mysqli_free_result($result);
				}
				
				// Anzahl Beiträge
				$query = "SELECT COUNT(po_id) FROM `forum_beitraege`";
				$result = sqlQuery($query);
				if ($result AND mysqli_num_rows($result) > 0) {
					$beitraege = mysqli_result($result, 0, 0);
					mysqli_free_result($result);
				}
				if ($beitraege && $themen) {
					$textStatistik .= str_replace("%themen%", $themen, str_replace("%beitraege%", $beitraege, $t['login_forum_beitraege']));
				}
			}
			
			$text .= "<tr>\n";
			$text .= "<td colspan=\"2\" $bgcolor>$textStatistik</td>\n";
			$text .= "</tr>\n";
			
			
			if (!isset($unterdruecke_raeume)) {
				$unterdruecke_raeume = 0;
			}
			if (!$unterdruecke_raeume && !$abweisen) {
				// Wer ist online? Boxen mit Benutzern erzeugen, Topic ist Raumname
				if ($onlineanzahl) {
					// Gibt eine Liste der Räume mit Benutzern aus
					$benutzeranzeige = "";
					$r_name_alt = "";
					$runde = 0;
					if ($result2) {
						while ($row = mysqli_fetch_object($result2)) {
							// Nur offene, permanente Räume und das Forum zeigen
							if ( (($row->r_status1 == 'O' || $row->r_status1 == 'm') && $row->r_status2 == 'P') || (!$row->r_name && !$row->r_status1 && !$row->r_status2) ) {
								// Admins fett schreiben
								if (($row->o_level == "S") || ($row->o_level == "C")) {
									$nick = "<b>$row->o_name</b> ";
								} else {
									$nick = "$row->o_name ";
								}
								
								// Wenn es sich um einen neuen Raum handelt, dem Raumname davor hinzufügen
								if($runde == 0 || $row->r_name != $r_name_alt) {
									// Wenn es sich nicht um den 1. Raum handelt, einen Zeilenumbruch hinzufügen
									if($runde != 0) {
										$benutzeranzeige .= "<br>";
									}
									// Benutzer befindet sich im Forum
									if(!$row->r_name && !$row->r_status1 && !$row->r_status2) {
										$benutzeranzeige .= $t['login_benutzer_online_forum'];
									} else {
										$benutzeranzeige .= str_replace("%raum%", $row->r_name, $t['login_benutzer_online_raum']);
									}
								}
								// Benutzer hinzufügen
								$benutzeranzeige .= $nick;
								
								$r_name_alt = $row->r_name;
								$runde++;
							}
						}
						mysqli_free_result($result2);
						
						// Leerzeile
						$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
						
						// Meldung, wenn sich keine Benutzer in öffentlichen Räumen oder im Forum befinden
						if($benutzeranzeige == "") {
							$benutzeranzeige = $t['login_benutzer_niemand_online'];
						}
						
						// Benutzer anzeigen
						$text .= zeige_formularfelder("ueberschrift", $zaehler, str_replace("%onlineanzahl%", $onlineanzahl, $t['login_benutzer_online']), "", "", 0, "70", "");
						
						$text .= "<tr>\n";
						$text .= "<td colspan=\"2\" $bgcolor>$benutzeranzeige</td>\n";
						$text .= "</tr>\n";
					}
				}
			}
			$text .= "</table>\n";
		}
	}
	
	zeige_tabelle_volle_breite($t['login_login'], $text);
}
?>