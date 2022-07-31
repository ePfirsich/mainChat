<?php
// Funktionen nur für index.php
require_once("functions/functions-nachrichten_betrete_verlasse.php");

function login($user_id, $u_level, $u_ip_historie, $u_agb, $u_punkte_monat, $u_punkte_jahr, $u_punkte_datum_monat, $u_punkte_datum_jahr, $u_punkte_gesamt) {
	// In das System einloggen
	// $o_id wird zurückgeliefert
	// u_id=Benutzer-ID, u_nick ist Benutzername, u_level ist Level
	// ip_historie ist Array mit IPs alter Logins, u_agb ist Nutzungsbestimmungen gelesen Y/N
	
	global $punkte_gruppe;
	
	// IP/Browser Adresse des Benutzer setzen
	$remote_addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
	$http_user_agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
	
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
		$query = pdoQuery("SELECT `a_id` FROM `aktion` WHERE `a_user` = :a_user", [':a_user'=>$user_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 0 || $resultCount > 20) {
			pdoQuery("INSERT INTO `aktion` SET `a_user` = :a_user, `a_wann` = 'Sofort/Online', `a_was` = 'Freunde', `a_wie` = 'OLM'", [':a_user'=>$user_id]);
			pdoQuery("INSERT INTO `aktion` SET `a_user` = :a_user, `a_wann` = 'Login', `a_was` = 'Freunde', `a_wie` = 'OLM'", [':a_user'=>$user_id]);
			pdoQuery("INSERT INTO `aktion` SET `a_user` = :a_user, `a_wann` = 'Sofort/Online', `a_was` = 'Neue Mail', `a_wie` = 'OLM'", [':a_user'=>$user_id]);
			pdoQuery("INSERT INTO `aktion` SET `a_user` = :a_user, `a_wann` = 'Login', `a_was` = 'Neue Mail', `a_wie` = 'OLM'", [':a_user'=>$user_id]);
			pdoQuery("INSERT INTO `aktion` SET `a_user` = :a_user, `a_wann` = 'Alle 5 Minuten', `a_was` = 'Neue Mail', `a_wie` = 'OLM'", [':a_user'=>$user_id]);
		}
	}
	
	// Prüfen, ob Benutzer noch online ist und ggf. ausloggen
	$alteloginzeit = "";
	
	$query = pdoQuery("SELECT `o_id`, `o_login` FROM `online` WHERE `o_user` = :o_user", [':o_user'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount != 0) {
		$result = $query->fetch();
		
		$alteloginzeit = $result['o_login'];
		$online_id = $result['o_id'];
		
		logout($online_id, $user_id);
	}
	
	// Benutzerdaten ändern
	
	// Punkte des Vormonats/Vorjahres löschen und Benutzergruppe ermitteln, falls nicht Gast
	if ($u_level != "G") {
		// Ist u_punkte_monat vom aktuellen Monat?
		if ($u_punkte_datum_monat != date("n", time())) {
			$f['u_punkte_datum_monat'] = date("n", time());
			$f['u_punkte_monat'] = 0;
		} else {
			$f['u_punkte_datum_monat'] = $u_punkte_datum_monat;
			$f['u_punkte_monat'] = $u_punkte_monat;
		}
		
		// Ist u_punkte_jahr vom aktuellen Jahr?
		if ($u_punkte_datum_jahr != date("Y", time())) {
			$f['u_punkte_datum_jahr'] = date("Y", time());
			$f['u_punkte_jahr'] = 0;
		} else {
			$f['u_punkte_datum_jahr'] = $u_punkte_datum_jahr;
			$f['u_punkte_jahr'] = $u_punkte_jahr;
		}
		
		// Aus der Zahl der Gesamtpunkten die Benutzergruppe ableiten und in u_punkte_gruppe speichern
		$f['u_punkte_gruppe'] = 0;
		foreach ($punkte_gruppe as $key => $value) {
			if ($u_punkte_gesamt < $value) {
				break;
			} else {
				$f['u_punkte_gruppe'] = dechex($key);
			}
		}
	} else {
		// Beim Gast die Punkte immer auf 0 setzen
		$f['u_punkte_datum_monat'] = date("n", time());
		$f['u_punkte_monat'] = 0;
		$f['u_punkte_jahr'] = 0;
		$f['u_punkte_gruppe'] = 0;
	}
	
	// Aktuelle IP/Datum zu ip_historie hinzufügen
	$datum = time();
	$ip_historie_neu[$datum] = $remote_addr;
	if (is_array($u_ip_historie)) {
		$i = 0;
		foreach($u_ip_historie as $datum => $ip_adr) {
			if($i >= 3) {
				break;
			}
			$ip_historie_neu[$datum] = $ip_adr;
			$i++;
		}
	}
	
	// u_agb, ip_historie und Temp-Admin schreiben
	if ($u_agb == "1") {
		$f['u_agb'] = 1;
	} else {
		$f['u_agb'] = 0;
	}
	$f['u_ip_historie'] = serialize($ip_historie_neu);
	if ($u_level == "A") {
		// falls Status bisher Temp-Admin -> zurücksetzen.
		$f['u_level'] = "U";
		$u_level = "U";
	} else {
		$f['u_level'] = $u_level;
	}
	
	if (!$user_id) {
		global $kontakt;
		email_senden($kontakt, "Fataler Fehler beim Login", "Fataler Fehler beim Login:<PRE>" . print_r($f) . "</PRE>");
		exit;
	}
	
	// Login als letzten Login setzen, dabei away und loginfehler zurücksetzen.
	pdoQuery("UPDATE `user` SET `u_login` = NOW(), `u_away` = '', `u_level` = :u_level, `u_agb` = :u_agb, `u_ip_historie` = :u_ip_historie, `u_punkte_datum_monat` = :u_punkte_datum_monat,
				`u_punkte_monat` = :u_punkte_monat, `u_punkte_jahr` = :u_punkte_jahr, `u_punkte_gruppe` = :u_punkte_gruppe WHERE `u_id` = :u_id",
		[
			':u_id'=>$user_id,
			':u_level'=>$f['u_level'],
			':u_agb'=>$f['u_agb'],
			':u_ip_historie'=>$f['u_ip_historie'],
			':u_punkte_datum_monat'=>$f['u_punkte_datum_monat'],
			':u_punkte_monat'=>$f['u_punkte_monat'],
			':u_punkte_jahr'=>$f['u_punkte_jahr'],
			':u_punkte_gruppe'=>$f['u_punkte_gruppe']
		]);
	aktualisiere_inhalt_online($user_id);
	
	// Aktuelle Daten des Benutzers aus Tabelle iignore lesen
	// Query muss mit Code in ignore übereinstimmen
	$query = pdoQuery("SELECT `i_user_passiv` FROM `iignore` WHERE `i_user_aktiv` = :i_user_aktiv", [':i_user_aktiv'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		$ignore[0] = FALSE;
	} else {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$ignore[$row['i_user_passiv']] = TRUE;
		}
	}
	
	$knebelzeit = NULL;
	// Aktuelle Benutzerdaten aus Tabelle user lesen
	$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_level`, `u_farbe`, `u_away`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_login`, `u_nachrichten_empfangen`, `u_chathomepage`, `u_punkte_anzeigen` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		global $kontakt;
		email_senden($kontakt, "Fehler beim Login", "Fehler beim Login: $query");
		exit;
	} else {
		$userdata = $query->fetch();
		
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
		$query = pdoQuery("SELECT `u_knebel` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$knebelzeit = $result['u_knebel'];
		}
	}
	
	// Vorbereitung für Login ist abgeschlossen. Jetzt nochmals Prüfen ob Benutzer online ist, 
	// ggf. Session löschen und neue Session schreiben
	
	// Tabellen online+user exklusiv locken
	pdoQuery("LOCK TABLES `online` WRITE, `user` WRITE", []);
	
	pdoQuery("DELETE FROM `online` WHERE `o_user` = :o_user", [':o_user'=>$user_id]);
	
	// Hash-Wert ermitteln
	$hash_id = id_erzeuge();
	
	// Session setzen
	$_SESSION["id"] = $hash_id;
	$_SESSION["u_id"] = $user_id; // Nötig für den Ajax-Chat
	$_SESSION['DateAndTime'] = date('Y-m-d H:i:s', time()); // Nötig für den Ajax-Chat
	
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
	
	$o_id = schreibe_online($f, "einloggen", $user_id);
	
	if (!$o_id) {
		email_senden($kontakt, "Fataler Fehler beim Login", $f);
		exit;
	}
	
	// Timestamps im Datensatz aktualisieren -> Benutzer gilt als eingeloggt
	if ($alteloginzeit != "") {
		pdoQuery("UPDATE `online` SET `o_aktiv` = NULL, `o_login` = :o_login, `o_knebel` = :o_knebel, `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_login'=>$alteloginzeit, ':o_knebel'=>$knebelzeit, ':o_user'=>$user_id]);
	} else {
		pdoQuery("UPDATE `online` SET `o_aktiv` = NULL, `o_login` = NULL, `o_knebel` = :o_knebel, `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_knebel'=>$knebelzeit, ':o_user'=>$user_id]);
	}
	
	// Lock freigeben
	pdoQuery("UNLOCK TABLES", []);
	
	return $o_id;
}

function betrete_chat($o_id, $user_id, $u_nick, $u_level, $raum) {
	// Benutzer $user_id betritt Raum $raum (r_id)
	// Nachricht in Raum $raum wird erzeugt
	// Zeiger auf letzte Zeile wird zurückgeliefert
	
	global $lobby, $eintrittsraum, $lang, $system_farbe, $u_punkte_gesamt;
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
		$query = pdoQuery("SELECT `r_id`, `r_status1`, `r_besitzer`, `r_name`, `r_min_punkte` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$raum]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$rows = $query->fetch();
			$r_min_punkte = $rows['r_min_punkte'];
			if ($rows['r_name'] != $eintrittsraum) {
				switch ($rows['r_status1']) {
					case "G":
					case "M":
					// Grundsätzlich nicht in geschlossene oder moderierte räume
						$raumeintritt = false;
						
						// es sei denn, man ist dorthin eingeladen
						$query = pdoQuery("SELECT `inv_user` FROM `invite` WHERE `inv_raum` = :inv_raum AND `inv_user` = :inv_user", [':inv_raum'=>$rows['r_id'], ':inv_user'=>$user_id]);
						
						$resultCount = $query->rowCount();
						if ($resultCount > 0) {
							$raumeintritt = true;
						}
						
						// oder man ist Raumbesiter dort
						if ($rows['r_besitzer'] == $user_id) {
							$raumeintritt = true;
						}
						
						// abweisen wenn $raumeintritt = false
						if (!$raumeintritt) {
							system_msg("", 0, $user_id, $system_farbe, str_replace("%r_name_neu%", $rows['r_name'], $lang['raum_gehe4']));
							unset($raum);
						}
						break;
					
					default:
						break;
				}
			}
		}
	}
	
	if (strlen($raum) > 0) {
		// Prüfung ob Benutzer aus Raum ausgesperrt ist
		$resultCount = pdoQuery("SELECT `s_id` FROM `sperre` WHERE `s_raum` = :s_raum AND `s_user` = :s_user", [':s_raum'=>intval($raum), ':s_user'=>$user_id])->rowCount();
		if ($resultCount > 0 && $r_min_punkte > $u_punkte_gesamt) {
			// Aktueller Raum ist gesperrt, oder zu wenige Punkte
			
			// Ist Benutzer aus Raum ausgesperrt, dann nicht einfach den Eintrittsraum oder die Lobby nehmen,
			// da kann der Benutzer auch ausgesperrt sein.
			
			unset($raum);
			
			$query = pdoQuery("SELECT `r_id` FROM `raum` LEFT JOIN `sperre` ON `r_id` = `s_raum` AND `s_user` = :s_user WHERE `r_status1` = 'O' and `r_status2` = 'P' AND `r_min_punkte` <= :r_min_punkte AND `s_id` IS NULL ORDER BY `r_id`", [':s_user'=>$user_id, ':r_min_punkte'=>$u_punkte_gesamt]);
			
			$resultCount = $query->rowCount();
			if ($resultCount > 0) {
				$result = $query->fetch();
				// Es gibt Räume, für die man noch nicht gesperrt ist.
				// hiervon den ersten nehmen
				$raum = $result['r_id'];
			}
		}
	}
	
	// Welchen Raum betreten?
	if (strlen($raum) == 0) {
		// Id des Eintrittsraums als Voreinstellung ermitteln
		$query = pdoQuery("SELECT `r_id`, `r_name`, `r_eintritt`, `r_topic` FROM `raum` WHERE `r_id` = :r_name", [':r_name'=>$eintrittsraum]);
		
		$resultCount = $query->rowCount();
		$result = $query->fetch();
		
		// Eintrittsraum nicht gefunden? -> lobby probieren.
		if ($resultCount == 0) {
			$query = pdoQuery("SELECT `r_id`, `r_name`, `r_eintritt`, `r_topic` FROM `raum` WHERE `r_name` = :r_name", [':r_name'=>$lobby]);
			
			$resultCount = $query->rowCount();
			$result = $query->fetch();
		}
		
		// Lobby  nicht gefunden? --> Lobby anlegen
		if ($resultCount == 0) {
			// Lobby neu anlegen
			pdoQuery("INSERT INTO `raum` (`r_id`, `r_name`, `r_eintritt`, `r_austritt`, `r_status1`, `r_besitzer`, `r_topic`, `r_status2`, `r_smilie`) VALUES (0, :r_name, 'Willkommen', '', 'O', 1, 'Eingangshalle', 'P', 1)", [':r_name'=>$lobby]);
			
			// neu lesen
			$query = pdoQuery("SELECT `r_id`, `r_name`, `r_eintritt`, `r_topic` FROM `raum` WHERE `r_name` = :r_name", [':r_name'=>$lobby]);
			
			$resultCount = $query->rowCount();
			$result = $query->fetch();
		}
	} else {
		// Gewählten Raum ermitteln
		$query = pdoQuery("SELECT `r_id`, `r_name`, `r_eintritt`, `r_topic` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$raum]);
		
		$resultCount = $query->rowCount();
		$result = $query->fetch();
	}
	
	if ($resultCount == 1) {
		$r_id = $result['r_id'];
		$r_eintritt = $result['r_eintritt'];
		$r_topic = $result['r_topic'];
		$r_name = $result['r_name'];
	} else {
		?>
		<body><p>Fehler: Ungültige Raum-ID <?php echo $raum; ?> beim Login!</p></body>
		<?php
		exit;
	}
	
	// Aktuellen Raum merken, o_who auf chat setzen
	$f['o_raum'] = $r_id;
	$f['o_who'] = "0";
	
	if( !isset($user_id) || $user_id == null || $user_id == "") {
		global $kontakt;
		email_senden($kontakt, "user_id leer1", "Nickname: " . $u_nick);
	}
	
	// Nachricht im Chat ausgeben
	nachricht_betrete($user_id, $r_id, $u_nick, $r_name);
	
	// ID der Eintrittsnachricht merken und online-Datensatz schreiben
	schreibe_online($f, "betreten", $user_id);
	
	
	// Topic vorhanden? ausgeben
	if (strlen($r_topic) > 0) {
		system_msg("", 0, $user_id, "", "<br><b>$lang[betrete_chat3] $r_name:</b> $r_topic");
	}
	
	// Eintrittsnachricht
	if ($lang['betrete_chat1']) {
		$txt = $lang['betrete_chat1'] . " " . $r_name . ":";
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
		system_msg("", 0, $user_id, "", "<br><b>$txt</b> $lang[betrete_chat2], $u_nick!</b><br>");
	}
	
	// Wer ist alles im Raum?
	raum_user($r_id, $user_id);
	
	// Hat der Benutzer sein Profil ausgefüllt?
	if ($u_level != "G") {
		profil_neu($user_id, $u_nick);
	}
	
	// Hat der Benutzer Aktionen für den Login eingestellt, wie Nachricht bei neuer Mail oder Freunden an sich selbst?
	if ($u_level != "G") {
		aktion($user_id, "Login", $user_id, $u_nick);
	}
	
	// Nachrichten an Freude verschicken
	$query = pdoQuery("SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_userid` = :f_userid AND `f_status` = 'bestaetigt'
		UNION SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_freundid` = :f_freundid AND `f_status` = 'bestaetigt' ORDER BY `f_zeit` DESC", [':f_userid'=>$user_id, ':f_freundid'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			unset($f);
			$f['raum'] = $r_name;
			$f['aktion'] = "Login";
			$f['f_text'] = $row['f_text'];
			if ($row['f_userid'] == $user_id) {
				if (ist_online($row['f_freundid'])) {
					$wann = "Sofort/Online";
					$an_u_id = $row['f_freundid'];
				} else {
					$wann = "Sofort/Offline";
					$an_u_id = $row['f_freundid'];
				}
			} else {
				if (ist_online($row['f_userid'])) {
					$wann = "Sofort/Online";
					$an_u_id = $row['f_userid'];
				} else {
					$wann = "Sofort/Offline";
					$an_u_id = $row['f_userid'];
				}
			}
			// Aktion ausführen
			aktion($user_id, $wann, $an_u_id, $u_nick, "Freunde", $f);
		}
	}
}

function id_erzeuge() {
	// Erzeugt eindeutige ID für jeden Benutzer
	
	$random_id = md5(uniqid(mt_rand()));
	return $random_id;
}

function betrete_forum($o_id, $user_id, $u_nick, $u_level) {
	// Benutzer betritt beim Login das Forum
	global $lobby, $eintrittsraum, $lang;
	
	//Daten in onlinetabelle schreiben
	$f['o_raum'] = -1;
	$f['o_who'] = "2";
	
	//user betritt nicht chat, sondern direkt forum
	schreibe_online($f, "betreten", $user_id);
	
	// Hat der Benutzer Aktionen für den Login eingestellt, wie Nachricht bei neuer Mail oder Freunden an sich selbst?
	if ($u_level != "G") {
		aktion($user_id, "Login", $user_id, $u_nick);
	}
	
	// Nachrichten an Freude verschicken
	$query = pdoQuery("SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_userid` = :f_userid AND `f_status` = 'bestaetigt'
		UNION SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_freundid` = :f_freundid AND `f_status` = 'bestaetigt' ORDER BY `f_zeit` DESC", [':f_userid'=>$user_id, ':f_freundid'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			unset($f);
			$f['raum'] = "";
			$f['aktion'] = "Login";
			$f['f_text'] = $row['f_text'];
			if ($row['f_userid'] == $user_id) {
				if (ist_online($row['f_freundid'])) {
					$wann = "Sofort/Online";
					$an_u_id = $row['f_freundid'];
				} else {
					$wann = "Sofort/Offline";
					$an_u_id = $row['f_freundid'];
				}
			} else {
				if (ist_online($row['f_userid'])) {
					$wann = "Sofort/Online";
					$an_u_id = $row['f_userid'];
				} else {
					$wann = "Sofort/Offline";
					$an_u_id = $row['f_userid'];
				}
			}
			// Aktion ausführen
			aktion($user_id, $wann, $an_u_id, $u_nick, "Freunde", $f);
		}
	}
}

function RaumNameToRaumID($eintrittsraum) {
	// Holt anhand der Globalen Lobby Raumbezeichnung die Passende Raum ID
	
	$lobby_id = false;
	$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE `r_name` = :r_name", [':r_name'=>$eintrittsraum]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$result = $query->fetch();
		$lobby_id = $result['r_id'];
	}
	return ($lobby_id);
}

function auth_user($username, $passwort, $bereits_angemeldet = 0) {
	// Passwort prüfen und Benutzerdaten lesen
	// Funktion liefert das Ergebnis zurück, wenn auf EINEN Benutzer das login/passwort passt
	
	if($bereits_angemeldet == 1) {
		if(isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken'])) {
			$identifier = $_COOKIE['identifier'];
			$securitytoken = $_COOKIE['securitytoken'];
			
			$query = pdoQuery("SELECT `securitytoken` FROM `securitytokens` WHERE `identifier` = :identifier", [':identifier'=>$identifier]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				$result = $query->fetch();
				
				if(sha1($securitytoken) !== $result['securitytoken']) {
					// Ein vermutlich gestohlener Security Token wurde identifiziert
					
					//Cookies entfernen
					setcookie("identifier","",time()-(3600*24*365));
					setcookie("securitytoken","",time()-(3600*24*365));
					unset($_COOKIE['identifier']);
					unset($_COOKIE['securitytoken']);
					
					$v_passwort = "";
				} else {
					//Token war korrekt
					$query = pdoQuery("SELECT `u_passwort` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$username]);
					
					$resultCount = $query->rowCount();
					if ($resultCount == 1) {
						$result = $query->fetch();
						$v_passwort = $usernick = $result['u_passwort'];
					} else {
						$v_passwort = "";
					}
				}
			} else {
				//Cookies entfernen
				setcookie("identifier","",time()-(3600*24*365));
				setcookie("securitytoken","",time()-(3600*24*365));
				unset($_COOKIE['identifier']);
				unset($_COOKIE['securitytoken']);
				
				$v_passwort = "";
			}
		}
	} else {
		// Übergebenes Passwort hashen und gegen das gespeicherte Passwort prüfen
		$v_passwort = encrypt_password($passwort);
	}
	
	$query = pdoQuery("SELECT * FROM `user` WHERE `u_nick` = :u_nick AND `u_passwort` = :u_passwort", [':u_nick'=>$username, ':u_passwort'=>$v_passwort]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		return $query->fetch();
	} else {
		return (0);
	}
}

function zeige_chat_login() {
	global $lang, $eintrittsraum, $eintritt, $forumfeatures, $gast_login, $nicht_angemeldet;
	global $lobby, $timeout, $whotext, $layout_kopf, $neuregistrierung_deaktivieren, $abweisen, $unterdruecke_raeume;
	
	$text = "";
	
	// Räume
	if ($forumfeatures) {
		$raeume_titel = $lang['login_raum_forum'];
	} else {
		$raeume_titel = $lang['login_raum'];
	}
	
	// Falls der Eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
	if (strlen($eintrittsraum) == 0) {
		$eintrittsraum = $lobby;
	}
	
	// Raumauswahlliste erstellen
	$query = pdoQuery("SELECT `r_name`, `r_id` FROM `raum` WHERE (`r_status1` = 'O' OR `r_status1` = 'E' OR `r_status1` LIKE BINARY 'm') AND `r_status2` = 'P' ORDER BY `r_name`", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount = 0) {
		// Kein öffentlicher Raum gefunden
		die;
	}

	$raeume = "<select name=\"eintritt\">";
	if ($forumfeatures) {
		$raeume .= "<option value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
	}
	
	$result = $query->fetchAll();
	foreach($result as $zaehler => $rows) {
		$r_id = $rows['r_id'];
		$r_name = $rows['r_name'];
		if ((!isset($eintritt) && $r_name == $eintrittsraum) || (isset($eintritt) && $r_id == $eintritt)) {
			$raeume .= "<option selected value=\"$r_id\">$r_name\n";
		} else {
			$raeume .= "<option value=\"$r_id\">$r_name\n";
		}
	}
	if ($forumfeatures) {
		$raeume .= "<option value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
	}
	$raeume .= "</select>";
	
	// Zeigt die Willkommensnachricht auf der Eingangsseite aus
	if (isset($layout_kopf) && $layout_kopf != "") {
		// Eigenen Text anzeigen
		$text .= $layout_kopf;
	} else {
		// Willkommen wird nur angezeigt, wenn kein eigener Kopf definiert ist
		$text.= "<h2>" . $lang['willkommen'] . "</h2><br>\n";
	}
	
	$automatische_anmeldung = false;
	
	//Überprüfe auf den 'Angemeldet bleiben'-Cookie
	//if( !isset($_SESSION['user_id']) && isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken']) && !$nicht_angemeldet) {
	if( isset($_COOKIE['identifier']) && isset($_COOKIE['securitytoken']) && !$nicht_angemeldet) {
		$identifier = $_COOKIE['identifier'];
		$securitytoken = $_COOKIE['securitytoken'];
		
		$query = pdoQuery("SELECT `securitytoken`, `user_id` FROM `securitytokens` WHERE `identifier` = :identifier", [':identifier'=>$identifier]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$row = $query->fetch();
			
			if(sha1($securitytoken) !== $row['securitytoken']) {
				// Ein vermutlich gestohlener Security Token wurde identifiziert
				$fehlermeldung = $lang['login_fehlermeldung_automatische_anmeldung'];
				$text .= hinweis($fehlermeldung, "fehler");
				
				//Cookies entfernen
				setcookie("identifier","",time()-(3600*24*365));
				setcookie("securitytoken","",time()-(3600*24*365));
				unset($_COOKIE['identifier']);
				unset($_COOKIE['securitytoken']);
				
				$automatische_anmeldung = false;
				echo "Test FALSE2 ";
			} else {
				//Token war korrekt
				
				//Setze neuen Token
				$neuer_securitytoken = random_string();
				
				pdoQuery("UPDATE `securitytokens` SET `securitytoken` = :securitytoken WHERE `identifier` = :identifier", [':securitytoken'=>sha1($neuer_securitytoken), ':identifier'=>$identifier]);
				
				setcookie("identifier",$identifier,time()+(3600*24*365)); //1 Jahr Gültigkeit
				setcookie("securitytoken",$neuer_securitytoken,time()+(3600*24*365)); //1 Jahr Gültigkeit
				
				//Logge den Benutzer ein
				$_SESSION['user_id'] = $row['user_id'];
				
				$automatische_anmeldung = true;
			}
		} else {
			$fehlermeldung = $lang['login_fehlermeldung_automatische_anmeldung'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			//Cookies entfernen
			setcookie("identifier","",time()-(3600*24*365));
			setcookie("securitytoken","",time()-(3600*24*365));
			unset($_COOKIE['identifier']);
			unset($_COOKIE['securitytoken']);
			
			$automatische_anmeldung = false;
			echo "Test FALSE1 ";
		}
	}
	
	if($automatische_anmeldung) {
		// Bereits angemeldet
		$query = pdoQuery("SELECT `u_nick` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$row['user_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$row = $query->fetch();
			$usernick = $row['u_nick'];
			
			$zaehler = 0;
			$text .= "<form action=\"index.php\" target=\"_top\" method=\"post\">\n";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"einloggen\">\n";
			$text .= "<input type=\"hidden\" name=\"username\" value=\"$usernick\">\n";
			$text .= "<input type=\"hidden\" name=\"passwort\" value=\"\">\n";
			$text .= "<input type=\"hidden\" name=\"bereits_angemeldet\" value=\"1\">\n";
			$text .= "<table style=\"width:100%;\">\n";
			
			
			// Überschrift: Login oder neu registrierten/Passwort vergessen
			$login_titel = str_replace("%username%", $usernick, $lang['login_formular_kopfzeile_eingeloggt']);
			$login_titel .= " ";
			$login_titel .= "[<a href=\"index.php?bereich=abmelden\">" . str_replace("%username%", $usernick, $lang['login_nicht_username']) . "</a>]";
			$text .= zeige_formularfelder("ueberschrift", $zaehler, $login_titel, "", "", 0, "70", "");
			
			// Benutzername
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>\n";
			$text .= "<td $bgcolor style=\"text-align:right; width:25%;\">$lang[login_benutzername]</td>\n";
			$text .= "<td $bgcolor><a href=\"index.php?bereich=abmelden\">$usernick [$lang[login_abmelden]]</a></td>\n";
			$text .= "</tr>\n";
			$zaehler++;
			
			
			// Räume
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>\n";
			$text .= "<td $bgcolor style=\"text-align:right; width:25%;\">$raeume_titel</td>\n";
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
			$text .= "<td $bgcolor><input type=\"submit\" value=\"". $lang['login_login'] . "\"></td>\n";
			$text .= "</tr>\n";
			
			
			$text .= "</table>\n";
			$text .= "</form>\n";
		} else {
			$fehlermeldung = $lang['login_fehlermeldung_automatische_anmeldung'];
			$text .= hinweis($fehlermeldung, "fehler");
			
			//Cookies entfernen
			setcookie("identifier","",time()-(3600*24*365));
			setcookie("securitytoken","",time()-(3600*24*365));
			unset($_COOKIE['identifier']);
			unset($_COOKIE['securitytoken']);
			
			$automatische_anmeldung = false;
		}
	}
	
	if(!$automatische_anmeldung) {
		// Nicht angemeldet
		
		// Benutzerlogin
		$zaehler = 0;
		$text .= "<form action=\"index.php\" target=\"_top\" method=\"post\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"einloggen\">\n";
		$text .= "<table style=\"width:100%;\">\n";
		
		
		// Überschrift: Login oder neu registrierten/Passwort vergessen
		if ($neuregistrierung_deaktivieren) {
			$login_titel = $lang['login_formular_kopfzeile'];
		} else {
			$login_titel = $lang['login_formular_kopfzeile'] . " [<a href=\"index.php?bereich=registrierung\">" . $lang['login_neuen_benutzernamen_registrieren'] . "</a>]";
		}
		$login_titel .= " [<a href=\"index.php?bereich=passwort-vergessen\">" . $lang['login_passwort_vergessen'] . "</a>]";
		
		$text .= zeige_formularfelder("ueberschrift", $zaehler, $login_titel, "", "", 0, "70", "");
		
		
		// Benutzername
		$text .= zeige_formularfelder("input", $zaehler, $lang['login_benutzername'], "username", "");
		$zaehler++;
		
		
		// Passwort
		$text .= zeige_formularfelder("password", $zaehler, $lang['login_passwort'], "passwort", "");
		$zaehler++;
		
		
		// Räume
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor style=\"text-align:right; width:25%;\">$raeume_titel</td>\n";
		$text .= "<td $bgcolor>$raeume</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		
		// Angemeldet bleiben
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor style=\"text-align:right; width:25%;\">&nbsp;</td>\n";
		$text .= "<td $bgcolor><label><input type=\"checkbox\" name=\"angemeldet_bleiben\" value=\"1\"> $lang[login_angemeldet_bleiben]</label></td>\n";
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
		$text .= "<td $bgcolor><input type=\"submit\" value=\"". $lang['login_login'] . "\"></td>\n";
		$text .= "</tr>\n";
		
		$text .= "</table>\n";
		$text .= "</form>\n";
		
		if( $gast_login && !ist_gastspeere_aktiv() ) {
			// Gastlogin
			$text .= "<form action=\"index.php\" target=\"_top\" method=\"post\">\n";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"einloggen\">\n";
			$text .= "<input type=\"hidden\" name=\"username\" value=\"\">\n";
			$text .= "<input type=\"hidden\" name=\"passwort\" value=\"\">\n";
			$text .= "<table style=\"width:100%;\">\n";
			
			
			// Überschrift: Gastlogin
			$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['login_gastlogin'], "", "", 0, "70", "");
			
			
			// Hinweis zum Gastlogin
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>\n";
			$text .= "<td colspan=\"2\" $bgcolor>$lang[login_gastinformation]</td>\n";
			$text .= "</tr>\n";
			
			
			// Räume
			if ($zaehler % 2 != 0) {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
			$text .= "<tr>\n";
			$text .= "<td $bgcolor style=\"text-align:right; width:25%;\">$raeume_titel</td>\n";
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
			$text .= "<td $bgcolor><input type=\"submit\" value=\"". $lang['login_login'] . "\"></td>\n";
			$text .= "</tr>\n";
			
			$text .= "</table>\n";
			$text .= "</form>\n";
		}
	}
	
	// Wie viele Benutzer sind im Chat registriert?
	$query = pdoQuery("SELECT COUNT(`u_id`) AS `anzahl` FROM `user` WHERE `u_level` IN ('A','C','G','M','S','U')", []);
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		$useranzahl = $result['anzahl'];
		
		// Anzahl der angemeldeten Benutzer ausgeben
		if ($useranzahl > 1) {
			$text .= "<table style=\"width:100%;\">\n";
			
			// Leerzeile
			$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
			
			// Überschrift: Statistik
			$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['login_statistik'], "", "", 0, "70", "");
			
			$textStatistik = str_replace("%useranzahl%", $useranzahl, $lang['login_benutzer_registriert']);
			
			// Anzahl der Beiträge im Forum ausgeben
			if ($forumfeatures) {
				// Anzahl Themen
				$query = pdoQuery("SELECT COUNT(`forum_id`) AS `anzahl` FROM `forum_foren`", []);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$result = $query->fetch();
					$themen = $result['anzahl'];
				}
				
				// Anzahl Beiträge
				$query = pdoQuery("SELECT COUNT(`beitrag_id`) AS `anzahl` FROM `forum_beitraege`", []);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$result = $query->fetch();
					$beitraege = $result['anzahl'];
				}
				if ($beitraege && $themen) {
					$textStatistik .= str_replace("%themen%", $themen, str_replace("%beitraege%", $beitraege, $lang['login_forum_beitraege']));
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
				
				// Benutzer online und Räume bestimmen -> merken
				$query2 = pdoQuery("SELECT `o_who`, `o_name`, `o_level`, `r_name`, `r_status1`, `r_status2`, `r_name` = :r_name AS `lobby` "
					. "FROM `online` LEFT JOIN `raum` ON `o_raum` = `r_id` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout ORDER BY `lobby` DESC, `r_name`, `o_who`, `o_name`", [':r_name'=>$lobby, ':timeout'=>$timeout]);
				
				$result2Count = $query2->rowCount();
				if ($result2Count > 0) {
					// Gibt eine Liste der Räume mit Benutzern aus
					$benutzeranzeige = "";
					$r_name_alt = "";
					$runde = 0;
					$result2 = $query2->fetchAll();
					foreach($result2 as $zaehler => $row) {
						// Nur offene, permanente Räume und das Forum zeigen
						if ( (($row['r_status1'] == 'O' || $row['r_status1'] == 'm') && $row['r_status2'] == 'P') || (!$row['r_name'] && !$row['r_status1'] && !$row['r_status2']) ) {
							// Admins fett schreiben
							if (($row['o_level'] == "S") || ($row['o_level'] == "C")) {
								$nick = "<b>$row[o_name]</b> ";
							} else {
								$nick = "$row[o_name] ";
							}
							
							// Wenn es sich um einen neuen Raum handelt, dem Raumname davor hinzufügen
							if($runde == 0 || $row['r_name'] != $r_name_alt) {
								// Wenn es sich nicht um den 1. Raum handelt, einen Zeilenumbruch hinzufügen
								if($runde != 0) {
									$benutzeranzeige .= "<br>";
								}
								// Benutzer befindet sich im Forum
								if(!$row['r_name'] && !$row['r_status1'] && !$row['r_status2']) {
									$benutzeranzeige .= $lang['login_benutzer_online_forum'];
								} else {
									$benutzeranzeige .= str_replace("%raum%", $row['r_name'], $lang['login_benutzer_online_raum']);
								}
							}
							// Benutzer hinzufügen
							$benutzeranzeige .= $nick;
							
							$r_name_alt = $row['r_name'];
							$runde++;
						}
					}
					
					// Leerzeile
					$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
					
					// Meldung, wenn sich keine Benutzer in öffentlichen Räumen oder im Forum befinden
					if($benutzeranzeige == "") {
						$benutzeranzeige = $lang['login_benutzer_niemand_online'];
					}
					
					// Benutzer anzeigen
					$text .= zeige_formularfelder("ueberschrift", $zaehler, str_replace("%onlineanzahl%", $result2Count, $lang['login_benutzer_online']), "", "", 0, "70", "");
					
					$text .= "<tr>\n";
					$text .= "<td colspan=\"2\" $bgcolor>$benutzeranzeige</td>\n";
					$text .= "</tr>\n";
				}
			}
			$text .= "</table>\n";
		}
	}
	
	return $text;
}

function ist_gastspeere_aktiv() {
	// Wenn in Sperre = "-GAST-" dann Gastlogin gesperrt
	$query = pdoQuery("SELECT `is_domain` FROM `ip_sperre` WHERE `is_domain` = '-GAST-'", []);
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		return true;
	} else {
		return false;
	}
}

function ist_globale_speere_aktiv() {
	// Wenn in Sperre = "-GAST-" dann Gastlogin gesperrt
	$query = pdoQuery("SELECT `is_domain` FROM `ip_sperre` WHERE `is_domain` = '-GLOBAL-'", []);
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		return true;
	} else {
		return false;
	}
}

function checke_ipsperren($remote_addr, $http_x_forwarded_for) {
	// Kontrolliere die IP-Sperren
	global $ip_name;
	
	$warnung = false;
	$abweisen = false;
	
	$ip_name = @gethostbyaddr($remote_addr);
	$query = pdoQuery("SELECT `is_warn` FROM `ip_sperre` WHERE (SUBSTRING_INDEX(`is_ip`,'.',`is_ip_byte`) LIKE SUBSTRING_INDEX(:remote_addr,'.',`is_ip_byte`) AND `is_ip` IS NOT NULL) "
		. "OR (`is_domain` LIKE RIGHT(:ip_name, LENGTH(`is_domain`)) AND LENGTH(`is_domain`)>0)", [':remote_addr'=>$remote_addr, ':ip_name'=>$ip_name]);
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			if ($row['is_warn'] == "ja") {
				// Warnung ausgeben
				$warnung = true;
				$infotext = $row['is_infotext'];
			} else {
				// IP ist gesperrt
				$abweisen = true;
			}
		}
	}
	
	// HTTP_X_FORWARDED_FOR IP bestimmen und prüfen. Ist Login erlaubt?
	if (!$abweisen && $http_x_forwarded_for != $remote_addr) {
		$ip_name = @gethostbyaddr($http_x_forwarded_for);
		$query = pdoQuery("SELECT `is_warn` FROM `ip_sperre` WHERE (SUBSTRING_INDEX(`is_ip`,'.',`is_ip_byte`) LIKE SUBSTRING_INDEX(:http_x_forwarded_for,'.',`is_ip_byte`) AND `is_ip` IS NOT NULL) "
			. "OR (`is_domain` LIKE RIGHT(:ip_name, LENGTH(`is_domain`)) AND LENGTH(`is_domain`)>0)", [':http_x_forwarded_for'=>$http_x_forwarded_for, ':ip_name'=>$ip_name]);
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				if ($row['is_warn'] == "ja") {
					// Warnung ausgeben
					$warnung = true;
				} else {
					// IP ist gesperrt
					$abweisen = true;
				}
			}
		}
	}
	
	// zweite Prüfung, gibts was, was mit "*" in der mitte schafft? für p3cea9*.t-online.de
	$query = pdoQuery("SELECT `is_domain`, `is_warn`, `is_infotext` FROM `ip_sperre` WHERE `is_domain` LIKE '_%*%_'", []);
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$part = explode("*", $row['is_domain'], 2);
			if ((strlen($part[0]) > 0) && (strlen($part[1]) > 0)) {
				if (substr($ip_name, 0, strlen($part[0])) == $part[0] && substr($ip_name, strlen($ip_name) - strlen($part[1]), strlen($part[1])) == $part[1]) {
					// IP stimmt überein
					if ($row['is_warn'] == "ja") {
						// Warnung ausgeben
						$warnung = true;
					} else {
						// IP ist gesperrt
						$abweisen = true;
					}
				}
			}
		}
	}
	
	if($abweisen) {
		// Login sperren
		return "abweisen";
	} else if($warnung) {
		// Warnung ausgeben
		return "warnung";
	} else {
		return "";
	}
}

function zeige_index_footer() {
	global $mainchat_version, $lang;
	$text = "<br>\n";
	$text .= "<div align=\"center\">$mainchat_version\n";
	$text .= "<br><br>\n";
	$text .= "<a href=\"index.php?bereich=datenschutz\">$lang[login_datenschutzerklaerung]</a> | <a href=\"index.php?bereich=kontakt\">$lang[login_kontakt]</a> | <a href=\"index.php?bereich=impressum\">$lang[login_impressum]</a>\n";
	$text .= "</div>\n";
	
	return $text;
}
?>