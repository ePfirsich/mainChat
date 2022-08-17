<?php
/**
 * Führt eine SQL-Abfrage aus und gibt das Ergebnis zurück.
 * Benachrichtigt bei fehlerhaften Abfragen.
 * @param string $query Die Abfrage, die ausgeführt werden soll
 * @return Ressource|false Das Ergebnis der Abfrage
 */
function pdoQuery($query, $params) {
	global $pdo, $debug_modus;
	
	try {
		$statement = $pdo->prepare($query);
		$statement->execute($params);
	} catch (PDOException $exception) {
		if($debug_modus) {
			global $kontakt, $lang;
			email_senden($kontakt, $lang['sql_query_fehlgeschlagen'], $query.'<br><br>' . $exception->getMessage());
		}
		return false;
	}
	
	return $statement;
}

function raum_user($r_id, $u_id, $keine_benutzer_anzeigen = true) {
	// Gibt die Benutzer im Raum r_id im Text an $u_id aus
	global $timeout, $lang, $leveltext, $admin, $lobby, $unterdruecke_user_im_raum_anzeige;
	
	if ($unterdruecke_user_im_raum_anzeige != "1") {
		$query = pdoQuery("SELECT `r_name`, `r_besitzer`, `o_user`, `o_name`, `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4` FROM `raum`, `online` WHERE `r_id` = :r_id AND `o_raum` = `r_id` AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout ORDER BY `o_name`",
			[
				':r_id'=>$r_id,
				':timeout'=>$timeout
			]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$i = 0;
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				// Beim ersten Durchlauf Namen des Raums einfügen
				if ($i == 0) {
					$text = str_replace("%r_name%", $row['r_name'], $lang['raum_user1']);
				}
				
				// Benutzerdaten lesen, Liste ausgeben
				$userdata = unserialize($row['o_userdata'] . $row['o_userdata2'] . $row['o_userdata3'] . $row['o_userdata4']);
				
				// Variable aus o_userdata setzen, Level und away beachten
				$uu_level = $userdata['u_level'];
				$uu_nick = zeige_userdetails($userdata['u_id'], FALSE, TRUE);
				
				if ($userdata['u_away'] != "") {
					$text .= "(" . $uu_nick . ")";
				} else {
					$text .= $uu_nick;
				}
				
				$i++;
				if ($i < $resultCount) {
					$text = $text . ", ";
				}
			}
		} else {
			if($keine_benutzer_anzeigen) {
				$text = $lang['raum_user12'];
			} else {
				$text = '';
			}
		}
		
		if($text != '') {
			$ergebnis = system_msg("", 0, $u_id, "", $text);
		} else {
			$ergebnis = '';
		}
	} else {
		$ergebnis = 1;
	}
	
	return $ergebnis;
}

function ist_online($user) {
	// Prüft ob Benutzer noch online ist
	// liefert 1 oder 0 zurück
	
	global $timeout, $ist_online_raum, $whotext;
	
	$ist_online_raum = "";
	
	$query = pdoQuery("SELECT `o_id`, `r_name` FROM `online` LEFT JOIN `raum` ON `r_id` = `o_raum` WHERE `o_user` = :o_user AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout", [':o_user'=>$user, ':timeout'=>$timeout]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		$ist_online_raum = $result['r_name'];
		if (!$ist_online_raum || $ist_online_raum == "NULL") {
			$ist_online_raum = "[" . $whotext[2] . "]";
		}
		return (1);
	} else {
		return (0);
	}
}

function schreibe_moderiert($f) {
	// Schreibt Chattext in DB
	
	// Schreiben falls text>0
	if (strlen($f['c_text']) > 0) {
		pdoQuery("INSERT INTO `moderation` (`c_text`, `c_von_user`, `c_an_user`, `c_raum`, `c_farbe`, `c_von_user_id`, `c_moderator`, `c_typ`) VALUES (:c_text, :c_von_user, :c_an_user, :c_raum, :c_farbe, :c_von_user_id, :c_moderator, :c_typ)",
			[
				':c_text'=>$f['c_text'],
				':c_von_user'=>$f['c_von_user'],
				':c_an_user'=>$f['c_an_user'],
				':c_raum'=>$f['c_raum'],
				':c_farbe'=>$f['c_farbe'],
				':c_von_user_id'=>$f['c_von_user_id'],
				':c_moderator'=>$f['c_moderator'],
				':c_typ'=>$f['c_typ']
			]);
	}
}

function schreibe_moderation() {
	global $u_id;
	
	// alles aus der moderationstabelle schreiben, bei der u_id==c_moderator;
	$query = pdoQuery("SELECT * FROM `moderation` WHERE `c_moderator` = :c_moderator AND `c_typ` = 'N'", [':c_moderator'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $f) {
			unset($c);
			// vorbereiten für umspeichern... geht leider nicht 1:1, 
			// weil fetch_array mehr zurückliefert als in $f[] sein darf...
			$c['c_von_user'] = $f['c_von_user'];
			$c['c_an_user'] = $f['c_an_user'];
			$c['c_typ'] = $f['c_typ'];
			$c['c_raum'] = $f['c_raum'];
			$c['c_text'] = $f['c_text'];
			$c['c_farbe'] = $f['c_farbe'];
			$c['c_zeit'] = $f['c_zeit'];
			$c['c_von_user_id'] = $f['c_von_user_id'];
			// und in moderations-tabelle schreiben
			schreibe_chat($c);
			
			// und datensatz löschen...
			pdoQuery("DELETE FROM `moderation` WHERE `c_id` = :c_id", [':c_id'=>$f['c_id']]);
		}
	}
}

function schreibe_chat($f) {
	global $system_farbe;
	// Schreibt Chattext in DB
	
	// Schreiben falls text > 0
	if (isset($f['c_text']) && strlen($f['c_text']) > 0) {
		if(!isset($f['c_von_user'])) {
			$f['c_von_user'] = "";
		}
		
		if(!isset($f['c_farbe'])) {
			$f['c_farbe'] = $system_farbe;
		}
		
		if(!isset($f['c_raum'])) {
			$f['c_raum'] = 0;
		}
		
		if(!isset($f['c_zeit'])) {
			pdoQuery("INSERT INTO `chat` SET `c_von_user` = :c_von_user, `c_an_user` = :c_an_user, `c_typ` = :c_typ, `c_raum` = :c_raum, `c_text` = :c_text, `c_farbe` = :c_farbe, `c_von_user_id` = :c_von_user_id",
				[
					':c_von_user'=>$f['c_von_user'],
					':c_an_user'=>$f['c_an_user'],
					':c_typ'=>$f['c_typ'],
					':c_raum'=>$f['c_raum'],
					':c_text'=>$f['c_text'],
					':c_farbe'=>$f['c_farbe'],
					':c_von_user_id'=>$f['c_von_user_id']
				]);
		} else {
			pdoQuery("INSERT INTO `chat` SET `c_von_user` = :c_von_user, `c_an_user` = :c_an_user, `c_typ` = :c_typ, `c_raum` = :c_raum, `c_text` = :c_text, `c_zeit` = :c_zeit, `c_farbe` = :c_farbe, `c_von_user_id` = :c_von_user_id",
				[
					':c_von_userx'=>$f['c_von_user'],
					':c_an_user'=>$f['c_an_user'],
					':c_typ'=>$f['c_typ'],
					':c_raum'=>$f['c_raum'],
					':c_text'=>$f['c_text'],
					':c_zeit'=>$f['c_zeit'],
					':c_farbe'=>$f['c_farbe'],
					':c_von_user_id'=>$f['c_von_user_id']
				]);
		}
	}
}

function schreibe_online($f, $aktion, $u_id) {
	if($aktion == "einloggen") {
		// In den Chat einloggen
		$query = pdoQuery("SELECT `o_id` FROM `online` WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
		$resultCount = $query->rowCount();
		if($resultCount == 0) {
			// Insert
			$query = pdoQuery("INSERT INTO `online` (`o_user`, `o_raum`, `o_hash`, `o_ip`, `o_who`, `o_browser`, `o_name`, `o_level`, `o_http_stuff`, `o_http_stuff2`, `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4`, `o_ignore`, `o_punkte`, `o_aktion`)
											 VALUES (:o_user,  :o_raum,  :o_hash,  :o_ip,  :o_who,  :o_browser,  :o_name,  :o_level,  :o_http_stuff,  :o_http_stuff2,  :o_userdata,  :o_userdata2,  :o_userdata3,  :o_userdata4,  :o_ignore,  :o_punkte,  :o_aktion)",
				[
					':o_user'=>$f['o_user'],
					':o_raum'=>$f['o_raum'],
					':o_hash'=>$f['o_hash'],
					':o_ip'=>$f['o_ip'],
					':o_who'=>$f['o_who'],
					':o_browser'=>$f['o_browser'],
					':o_name'=>$f['o_name'],
					':o_level'=>$f['o_level'],
					':o_http_stuff'=>$f['o_http_stuff'],
					':o_http_stuff2'=>$f['o_http_stuff2'],
					':o_userdata'=>$f['o_userdata'],
					':o_userdata2'=>$f['o_userdata2'],
					':o_userdata3'=>$f['o_userdata3'],
					':o_userdata4'=>$f['o_userdata4'],
					':o_ignore'=>$f['o_ignore'],
					':o_punkte'=>$f['o_punkte'],
					':o_aktion'=>$f['o_aktion']
				]);
		} else {
			// Update
			$query = pdoQuery("UPDATE `online` SET
								`o_raum` = :o_raum,
								`o_hash` = :o_hash,
								`o_ip` = :o_ip,
								`o_who` = :o_who,
								`o_browser` = :o_browser,
								`o_name` = :o_name,
								`o_level` = :o_level,
								`o_http_stuff` = :o_http_stuff,
								`o_http_stuff2` = :o_http_stuff2,
								`o_userdata` = :o_userdata,
								`o_userdata2` = :o_userdata2,
								`o_userdata3` = :o_userdata3,
								`o_userdata4` = :o_userdata4,
								`o_ignore` = :o_ignore,
								`o_punkte` = :o_punkte,
								`o_aktion` = :o_aktion
								WHERE `o_user` = :o_user",
				[
					':o_user'=>$u_id,
					':o_raum'=>$f['o_raum'],
					':o_hash'=>$f['o_hash'],
					':o_ip'=>$f['o_ip'],
					':o_who'=>$f['o_who'],
					':o_browser'=>$f['o_browser'],
					':o_name'=>$f['o_name'],
					':o_level'=>$f['o_level'],
					':o_http_stuff'=>$f['o_http_stuff'],
					':o_http_stuff2'=>$f['o_http_stuff2'],
					':o_userdata'=>$f['o_userdata'],
					':o_userdata2'=>$f['o_userdata2'],
					':o_userdata3'=>$f['o_userdata3'],
					':o_userdata4'=>$f['o_userdata4'],
					':o_ignore'=>$f['o_ignore'],
					':o_punkte'=>$f['o_punkte'],
					':o_aktion'=>$f['o_aktion']
				]);
		}
	} else if($aktion == "chat_forum") {
		// Wechsel vom Chat ins Forum
		$query = pdoQuery("UPDATE `online` SET `o_raum` = :o_raum, `o_who` = :o_who WHERE `o_user` = :o_user",
			[
				':o_user'=>$u_id,
				':o_raum'=>$f['o_raum'],
				':o_who'=>$f['o_who']
			]);
	} else if($aktion == "warnung") {
		// Inaktivitäswarnung
		$query = pdoQuery("UPDATE `online` SET `o_timeout_warnung` = :o_timeout_warnung WHERE `o_user` = :o_user",
			[
				':o_user'=>$u_id,
				':o_timeout_warnung'=>$f['o_timeout_warnung']
				
			]);
	} else if($aktion == "schreiben") {
		// Nachricht im Chat abschicken
		$query = pdoQuery("UPDATE `online` SET `o_spam_zeit` = :o_spam_zeit, `o_spam_zeilen` = :o_spam_zeilen, `o_spam_byte` = :o_spam_byte WHERE `o_user` = :o_user",
			[
				':o_user'=>$u_id,
				':o_spam_zeit'=>$f['o_spam_zeit'],
				':o_spam_zeilen'=>$f['o_spam_zeilen'],
				':o_spam_byte'=>$f['o_spam_byte']
			]);
	} else if($aktion == "betreten") {
		// Raum oder Forum betreten
		$query = pdoQuery("UPDATE `online` SET `o_raum` = :o_raum, `o_who` = :o_who WHERE `o_user` = :o_user",
			[
				':o_user'=>$u_id,
				':o_raum'=>$f['o_raum'],
				':o_who'=>$f['o_who']
			]);
	} else {
		return null;
	}
	
	return $query;
}

function global_msg($u_id, $r_id, $text) {
	// Schreibt Text $text in Raum $r_id an alle Benutzer
	// Art:		   N: Normal
	//				  S: Systemnachricht
	//				P: Privatnachricht
	//				H: Versteckte Nachricht
	
	if (strlen($r_id) > 0) {
		$f['c_raum'] = $r_id;
	}
	$f['c_typ'] = "S";
	$f['c_von_user_id'] = $u_id;
	$f['c_text'] = $text;
	$f['c_an_user'] = 0;
	
	$ergebnis = schreibe_chat($f);
	
	// In der Tabelle "online" merken, dass Text im Chat geschrieben wurde
	if ($u_id) {
		pdoQuery("UPDATE `online` SET `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
	}
}

function hidden_msg($von_user, $von_user_id, $farbe, $r_id, $text) {
	// Schreibt Text in Raum r_id
	// Art:		   N: Normal
	//				  S: Systemnachricht
	//				P: Privatnachticht
	//				H: Versteckte Nachricht
	
	$f['c_von_user'] = $von_user;
	$f['c_von_user_id'] = $von_user_id;
	$f['c_an_user'] = 0;
	$f['c_farbe'] = $farbe;
	$f['c_raum'] = $r_id;
	$f['c_typ'] = "H";
	$f['c_text'] = $text;
	
	schreibe_chat($f);
	
	// In der Tabelle "online" merken, dass Text im Chat geschrieben wurde
	if ($von_user_id) {
		pdoQuery("UPDATE `online` SET `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_user'=>$von_user_id]);
	}
}

function priv_msg($von_user, $von_user_id, $an_user, $farbe, $text) {
	// Schreibt privaten Text von $von_user an Benutzer $an_user
	// Art:	N: Normal
	//		S: Systemnachricht
	//		P: Privatnachticht
	//		H: Versteckte Nachricht
	
	// Optional Link auf Benutzer erzeugen
	
	if ($von_user_id) {
		$f['c_von_user'] = zeige_userdetails($von_user_id, FALSE, TRUE); 
	} else {
		$f['c_von_user'] = $von_user;
	}
	$f['c_von_user_id'] = $von_user_id;
	$f['c_an_user'] = $an_user;
	$f['c_farbe'] = $farbe;
	$f['c_typ'] = "P";
	$f['c_text'] = $text;
	
	if( !isset($f['c_an_user']) || $f['c_an_user'] == null || $f['c_an_user'] == "") {
		global $kontakt;
		email_senden($kontakt, "c_an_user leer1", "Von User: " . $von_user . " Von User-ID: " . $von_user_id . " Nachricht: " . $text);
	}
	
	schreibe_chat($f);
	
	// In der Tabelle "online" merken, dass Text im Chat geschrieben wurde
	if ($von_user_id) {
		pdoQuery("UPDATE `online` SET `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_user'=>$von_user_id]);
	}
}

function system_msg($von_user, $von_user_id, $an_user, $farbe, $text) {
	// Schreibt privaten Text als Systemnachricht an Benutzer $an_user
	// $von_user wird nicht benutzt
	// $von_user_id ist Absender der Nachricht (normalerweise wie $an_user, notwendig für Spamschutz)
	// Art:		   N: Normal
	//				  S: Systemnachricht
	//				P: Privatnachticht
	//				H: Versteckte Nachricht
	
	$f['c_an_user'] = $an_user;
	$f['c_von_user_id'] = $von_user_id;
	$f['c_farbe'] = $farbe;
	$f['c_typ'] = "S";
	$f['c_text'] = $text;
	
	if( !isset($f['c_an_user']) || $f['c_an_user'] == null || $f['c_an_user'] == "") {
		global $kontakt;
		email_senden($kontakt, "c_an_user leer2", "Von User: " . $von_user . " Von User-ID: " . $von_user_id . " Nachricht: " . $text);
	}
	
	schreibe_chat($f);
}

function aktualisiere_online($u_id) {
	// Timestamp im Datensatz aktualisieren -> Benutzer gilt als online
	pdoQuery("UPDATE `online` SET `o_aktiv` = NULL WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
}

function id_lese() {
	// Vergleicht Hash-Wert mit IP und Browser des Benutzers
	// Liefert Benutzer- und Online-Variable
	global $id;
	
	if(!isset($id) || $id == null | $id == "") {
		// Aus Sicherheitsgründen die Variablen löschen
		$userdata = "";
		$u_id = "";
		$u_nick = "";
		$o_id = 0;
		$u_level = "";
		$admin = 0;
		
		return false;
	}
	global $u_id, $u_nick, $o_id, $o_raum, $u_level, $u_farbe, $u_punkte_anzeigen, $admin, $ignore, $userdata, $o_punkte, $o_aktion;
	global $o_knebel, $u_punkte_gesamt, $moderationsmodul, $user_farbe;
	global $o_who, $o_timeout_zeit, $o_timeout_warnung, $o_spam_zeilen, $o_spam_byte, $o_spam_zeit, $o_dicecheck;
	
	// IP und Browser ermittlen
	$http_user_agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
	
	// u_id und o_id aus Objekt ermitteln, o_hash, o_browser müssen übereinstimmen
	$query = pdoQuery("SELECT HIGH_PRIORITY *, UNIX_TIMESTAMP(`o_timeout_zeit`) AS `o_timeout_zeit`, UNIX_TIMESTAMP(`o_knebel`)-UNIX_TIMESTAMP(NOW()) AS `o_knebel` FROM `online` WHERE `o_hash` = :o_hash", [':o_hash'=>$id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		exit;
	}
	
	$ar = $query->fetch();
	// Benutzerdaten und ignore Arrays setzen
	$userdata = unserialize($ar['o_userdata'] . $ar['o_userdata2'] . $ar['o_userdata3'] . $ar['o_userdata4']);
	$ignore = unserialize($ar['o_ignore']);
	
	unset($ar['o_ignore']);
	unset($ar['o_userdata']);
	unset($ar['o_userdata2']);
	unset($ar['o_userdata3']);
	unset($ar['o_userdata4']);
	
	// Schleife über alle Variable; Variable setzen
	foreach($ar as $k => $v) {
		$$k = $v;
	}
	
	// o_browser prüfen Benutzerdaten in Array schreiben
	if (is_array($userdata) && $ar['o_browser'] == $http_user_agent) {
		// Schleife über Benutzerdaten, Variable setzen
		foreach($userdata as $k => $v) {
			@$$k = $v;
		}
		
		// ChatAdmin oder Superuser oder Moderator?
		if ($u_level == "S" || $u_level == "C" || ($moderationsmodul == 1 && $u_level == "M" && isset($r_status1) && strtolower($r_status1) == "m")) {
			$admin = 1;
		} else {
			$admin = 0;
		}
		
		// Hole die Farbe des Benutzers
		if (!empty($u_id)) {
			$query = pdoQuery("SELECT `u_farbe` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
			
			$row = $query->fetch();
			if (!empty($row['u_farbe'])) {
				$u_farbe = $row['u_farbe'];
			} else {
				$u_farbe = $user_farbe;
			}
			return true;
		}
	} else {
		// Aus Sicherheitsgründen die Variablen löschen
		$userdata = "";
		$u_id = "";
		$u_nick = "";
		$o_id = 0;
		$u_level = "";
		$admin = 0;
		
		return false;
	}
}

function aktualisiere_inhalt_online($user_id) {
	// Kopie in Onlinedatenbank aktualisieren
	// Query muss mit dem Code in login() übereinstimmen
	$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_level`, `u_farbe`, `u_away`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_login`, `u_nachrichten_empfangen`, `u_chathomepage`, `u_punkte_anzeigen` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$userdata = $query->fetch();
		
		// Slashes in jedem Eintrag des Array ergänzen
		foreach($userdata as $ukey => $udata) {
			$udata = $udata;
		}
		
		// Benutzerdaten in 255-Byte Häppchen zerlegen
		$userdata_array = zerlege(serialize($userdata));
		
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
		
		pdoQuery("UPDATE `online` SET `o_userdata` = :o_userdata, `o_userdata2` = :o_userdata2, `o_userdata3` = :o_userdata3, `o_userdata4` = :o_userdata4, `o_level` = :o_level, `o_name` = :o_name WHERE `o_user` = :o_user",
			[
				':o_userdata'=>$userdata_array[0],
				':o_userdata2'=>$userdata_array[1],
				':o_userdata3'=>$userdata_array[2],
				':o_userdata4'=>$userdata_array[3],
				':o_level'=>$userdata['u_level'],
				':o_name'=>$userdata['u_nick'],
				':o_user'=>$user_id
			]);
	}
}

function zerlege($daten) {
	// Zerlegt $daten in 255byte-Häppchen und liefert diese in einem Array zurück
	
	$i = 0;
	$laenge = strlen($daten);
	$fertig = array();
	
	if ($laenge != 0) {
		$j = 0;
		$i = 0;
		while ($j < $laenge) {
			$fertig[] = substr($daten, $j, 255);
			// system_msg("",0,1225,0,"<b>DEBUG:</b> $i,$j,'$fertig[$i]'");
			$j = $j + 255;
			$i++;
		}
	}
	
	return ($fertig);
}

function zeige_tabelle_volle_breite($box, $text) {
	// Gibt Tabelle mit 100% Breiter mit Kopf und Inhalt aus
	?>
	<table class="tabelle_kopf">
		<tr>
			<td class="tabelle_kopfzeile"><?php echo $box; ?></td>
		</tr>
		<tr>
			<td class="tabelle_koerper smaller"><?php echo $text; ?></td>
		</tr>
	</table>
	<?php
}

function zeige_tabelle_zentriert($box, $text, $margin_top = false, $kopfzeile = true) {
	// Gibt zentrierte Tabelle mit 99% Breiter und optionalem Abstand nach oben URL mit Kopf und Inhalt aus
	
	$css = "";
	if ($margin_top) {
		$css = "style=\"margin-top: 5px;\"";
	}
	?>
	<table class="tabelle_kopf_zentriert" <?php echo $css; ?>>
		<?php if($kopfzeile) { ?>
		<tr>
			<td class="tabelle_kopfzeile"><?php echo $box; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td class="tabelle_koerper smaller"><?php echo $text; ?></td>
		</tr>
	</table>
	<?php
}

function zeige_kopfzeile_login() {
	// Gibt die Kopfzeile im Login aus
	global $lang, $logo, $chat;
	
	if($logo != "") {
	echo "<p style=\"text-align:center\"><img src=\"$logo\" alt =\"$chat\" title=\"$chat\"></p>";
	}
	
	$box = $lang['login_chatname'];
	$text = "<a href=\"index.php\">$lang[login_login]</a>\n";
	$text .= "| <a href=\"index.php?bereich=registrierung\">$lang[login_registrierung]</a>\n";
	$text .= "| <a href=\"index.php?bereich=chatiquette\">$lang[login_chatiquette]</a>\n";
	$text .= "| <a href=\"index.php?bereich=nutzungsbestimmungen\">$lang[login_nutzungsbestimmungen]</a>\n";
	
	zeige_tabelle_volle_breite($box, $text);
}

function coreCheckName($name, $check_name) {
	global $upper_name;
	// Nicht-druckbare Zeichen, Leerzeichen, " und ' entfernen
	$name = preg_replace("/[ '" . chr(34) . chr(1) . "-" . chr(31) . "]/",
		"", $name);
	$name = str_replace("#", "", $name);
	$name = str_replace("+", "", $name);
	$name = trim($name);
	
	if ((strlen($check_name) > 0) && (strlen($name) > 0)) {
		$i = 0;
		while ($i < strlen($name)) {
			if (!strstr($check_name, substr($name, $i, 1))) {
				if ($i > 0 && $i < strlen($name) - 1) {
					$name = substr($name, 0, $i) . substr($name, $i + 1);
				} elseif ($i > 0) {
					$name = substr($name, 0, -1);
				} else {
					$name = substr($name, $i + 1);
				}
			} else {
				$i++;
			}
		}
		
		if (!strstr($check_name, substr($name, 0, 1))) {
			return (substr($name, 1));
		}
	}
	
	if ($upper_name) {
		$name = UCFirst($name);
	}
	
	return $name;
}

function raum_ist_moderiert($raum) {
	// Liefert Status des Raums zurück: Moderiert ja/nein
	// Liefert zusätzlich in $raum_einstellungen alle Einstellungen des Raums zurück
	// Liefert in ist_moderiert zurück: Moderiert ja/nein
	
	// Liefert in ist_eingang zurück: Stiller Eingangsraum ja/nein
	global $moderationsmodul, $raum_einstellungen, $ist_moderiert, $ist_eingang;
	$moderiert = 0;
	$raum = intval($raum);
	
	$query = pdoQuery("SELECT `r_status1`, `r_status2` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$raum]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$raum_einstellungen = $query->fetch();
		$r_status1 = $raum_einstellungen['r_status1'];
	}
	if (isset($r_status1) && ($r_status1 == "m" || $r_status1 == "M")) {
		$query = pdoQuery("SELECT `o_user` FROM `online` WHERE `o_raum` = :o_raum AND `o_level` = 'M'", [':o_raum'=>$raum]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$moderiert = 1;
		}
	}
	$ist_moderiert = $moderiert;
	$ist_eingang = $r_status1 == "E";
	return $moderiert;
}

function debug($text = "", $rundung = 3) {
	static $zeit;
	static $durchlauf;
	
	if (!$text || !$zeit) {
		list($usec, $sec) = explode(" ", microtime());
		$zeit = (float) $usec + (float) $sec;
		$durchlauf = 0;
		$erg = "";
	} else {
		list($usec, $sec) = explode(" ", microtime());
		$zeit_neu = (float) $usec + (float) $sec;
		$durchlauf++;
		$laufzeit = $zeit_neu - $zeit;
		$zeit = $zeit_neu;
		$erg = $text . "_" . $durchlauf . ": " . round($laufzeit, $rundung) . "s";
		if ($durchlauf > 1) {
			$erg = ", " . $erg;
		}
	}
	return ($erg);
}

function zeige_smilies($anzeigeort, $benutzerdaten) {
	global $smilie, $smilietxt;
	
	$text = "";
	if( $anzeigeort == 'chat' ) {
		// Chatausgabe
		// Array mit Smilies einlesen, HTML-Tabelle ausgeben
		$zahl = 0;
		foreach($smilie as $smilie_code => $smilie_grafik) {
			if ( $zahl % 2 != 0 ) {
				$farbe_tabelle = 'class="tabelle_zeile1"';
			} else {
				$farbe_tabelle = 'class="tabelle_zeile2"';
			}
			$text .= "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat(' " . $smilie_code . " '); return(false)\"><img src=\"images/smilies/style-" . $benutzerdaten['u_layout_farbe'] . "/" . $smilie_grafik . "\" alt=\"".str_replace(" ", "&nbsp;", $smilietxt[$smilie_code])."\" title=\"".str_replace(" ", "&nbsp;", $smilietxt[$smilie_code])."\"></a> ";
			$zahl++;
		}
	} else {
		// Forumausgabe
		foreach($smilie as $smilie_code => $smilie_grafik) {
			$text .= "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_forum(' " . $smilie_code . " '); return(false)\"><img src=\"images/smilies/style-" . $benutzerdaten['u_layout_farbe'] . "/" . $smilie_grafik . "\" alt=\"".str_replace(" ", "&nbsp;", $smilietxt[$smilie_code])."\" title=\"".str_replace(" ", "&nbsp;", $smilietxt[$smilie_code])."\"></a>&nbsp;";
		}
	}
	return $text;
}

function zeige_userdetails($zeige_user_id, $online = FALSE, $extra_kompakt = FALSE, $benutzername_fett = TRUE) {
	// Liefert Benutzernamen + Level + Gruppe + E-Mail + Benutzerseite zurück
	// Bei online=TRUE wird der Status online/offline und opt die Onlinezeit oder der letzte Login ausgegeben
	// Falls extra_kompakt=TRUE wird nur Nick ausgegeben
	// $benutzername_fett -> Soll der Benutzername fett geschrieben werden?
	
	global $lang, $show_geschlecht, $leveltext, $punkte_grafik, $chat_grafik, $locale;
	
	$trenner = "&nbsp;";
	
	$text = "";
	if(!$zeige_user_id || $zeige_user_id == null || $zeige_user_id == 0) {
		return '';
	} else {
		$query = pdoQuery("SELECT `o_name`, `o_level`, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_login`) AS `u_online`, `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4`
						FROM `online` WHERE `o_user` = :o_user ", [':o_user'=>$zeige_user_id]);
		
		$resultCount = $query->rowCount();
		if($resultCount == 1) {
			// Benutzer online: Daten aus der Tabelle `online` holen
			
			$result = $query->fetch();
			$userdata = unserialize($result['o_userdata'] . $result['o_userdata2'] . $result['o_userdata3'] . $result['o_userdata4']);
			
			// Variable aus o_userdata setzen
			$benutzerdaten['u_id'] = $zeige_user_id;
			$benutzerdaten['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($result['o_name'])), "I", "i");
			$benutzerdaten['u_level'] = $result['o_level'];
			$benutzerdaten['u_punkte_anzeigen'] = $userdata['u_punkte_anzeigen'];
			$benutzerdaten['u_punkte_gruppe'] = $userdata['u_punkte_gruppe'];
			$benutzerdaten['u_nachrichten_empfangen'] = $userdata['u_nachrichten_empfangen'];
			$benutzerdaten['u_chathomepage'] = $userdata['u_chathomepage'];
			$benutzerdaten['u_online'] = $result['u_online'];
			$benutzerdaten['u_login'] = $userdata['u_login'];
		} else {
			// Benutzer offline: Daten aus der Tabelle `user` holen
			$query = pdoQuery("SELECT `u_nick`, `u_level`, `u_punkte_anzeigen`, `u_punkte_gruppe`, `u_nachrichten_empfangen`, `u_chathomepage`, `u_login`
						FROM `user` WHERE `u_id` = :u_id ", [':u_id'=>$zeige_user_id]);
			
			$resultCount = $query->rowCount();
			if($resultCount == 1) {
				$result = $query->fetch();
				$benutzerdaten['u_id'] = $zeige_user_id;
				$benutzerdaten['u_nick'] = strtr( str_replace("\\", "", htmlspecialchars($result['u_nick'])), "I", "i");
				$benutzerdaten['u_level'] = $result['u_level'];
				$benutzerdaten['u_punkte_anzeigen'] = $result['u_punkte_anzeigen'];
				$benutzerdaten['u_punkte_gruppe'] = $result['u_punkte_gruppe'];
				$benutzerdaten['u_nachrichten_empfangen'] = $result['u_nachrichten_empfangen'];
				$benutzerdaten['u_chathomepage'] = $result['u_chathomepage'];
				$benutzerdaten['u_online'] = null;
				$benutzerdaten['u_login'] = $result['u_login'];
			} else {
				$benutzerdaten['u_id'] = 0;
				$benutzerdaten['u_nick'] = "ehemaliges Mitglied";
				$benutzerdaten['u_level'] = "G";
				$benutzerdaten['u_punkte_anzeigen'] = 1;
				$benutzerdaten['u_punkte_gruppe'] = 0;
				$benutzerdaten['u_nachrichten_empfangen'] = 0;
				$benutzerdaten['u_chathomepage'] = 0;
				$benutzerdaten['u_online'] = null;
				$benutzerdaten['u_login'] = null;
				
				/*
				$nachricht = "Fehler: Falscher Aufruf von zeige_userdetails() für den Benutzer ";
				$nachricht_titel = $nachricht;
				if (isset($zeige_user_id)) {
					$nachricht .= $zeige_user_id;
				}
				global $kontakt;
				email_senden($kontakt, $nachricht_titel, $nachricht);
				*/
				
				return '';
			}
		}
	}
	
	$benutzerdaten['u_login'] = formatDate($benutzerdaten['u_login'],"d.m.Y H:i","Y-m-d H:i:s");
	
	if ($show_geschlecht == true) {
		$user_geschlecht = hole_geschlecht($zeige_user_id);
	}
	
	if (!$extra_kompakt) {
		$url = "inhalt.php?bereich=benutzer&aktion=benutzer_zeig&ui_id=" . $benutzerdaten['u_id'];
		if($benutzername_fett) {
			$text = "<a href=\"$url\" target=\"chat\"><b>" . $benutzerdaten['u_nick'] . "</b></a>";
		} else {
			$text = "<a href=\"$url\" target=\"chat\">" . $benutzerdaten['u_nick'] . "</a>";
		}
	} else {
		$text = $benutzerdaten['u_nick'];
	}
	
	if ($show_geschlecht && $user_geschlecht) {
		$text .= $chat_grafik[$user_geschlecht];
	}
	
	// Levels, Gruppen, Home & Mail Grafiken
	$text2 = "";
	if (!isset($leveltext[$benutzerdaten['u_level']])) {
		$leveltext[$benutzerdaten['u_level']] = "";
	}
	if (!$extra_kompakt && $leveltext[$benutzerdaten['u_level']] != "") {
		$text2 .= "&nbsp;(" . $leveltext[$benutzerdaten['u_level']] . ")";
	}
	
	if (!$extra_kompakt) {
		$grafikurl1 = "<a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-community\" target=\"chat\">";
		$grafikurl2 = "</a>";
	} else {
		$grafikurl1 = "";
		$grafikurl2 = "";
	}
	
	if (!$extra_kompakt && $benutzerdaten['u_punkte_gruppe'] != 0 && $benutzerdaten['u_punkte_anzeigen'] == "1" ) {
		
		if ($benutzerdaten['u_level'] == "C" || $benutzerdaten['u_level'] == "S") {
			$text2 .= "&nbsp;" . $grafikurl1 . $punkte_grafik[0] . $benutzerdaten['u_punkte_gruppe'] . $punkte_grafik[1] . $grafikurl2;
		} else {
			$text2 .= "&nbsp;" . $grafikurl1 . $punkte_grafik[2] . $benutzerdaten['u_punkte_gruppe'] . $punkte_grafik[3] . $grafikurl2;
		}
	}
	
	// Chat-Homepage
	if (!$extra_kompakt && $benutzerdaten['u_chathomepage'] == "1") {
		$text2 .= "&nbsp;" . "<a href=\"home.php?/" . $benutzerdaten['u_nick'] . "\" target=\"_blank\">$chat_grafik[home]</a>";
	}
	
	// Nachrichten
	if (!$extra_kompakt && $trenner != "" && $benutzerdaten['u_nachrichten_empfangen'] == "1") {
		$url = "inhalt.php?bereich=nachrichten&aktion=neu2&daten_nick=" . $benutzerdaten['u_nick'];
		$text2 .= $trenner . "<a href=\"$url\" target=\"chat\" title=\"E-Mail\">$chat_grafik[mail]</a>";
	} else if (!$extra_kompakt && $trenner != "") {
		$text2 .= $trenner;
	}
	
	// Onlinezeit oder Datum des letzten Logins einfügen, falls Online Text fett ausgeben
	if ($benutzerdaten['u_online'] != NULL && $online) {
		$text2 .= $trenner . str_replace("%online%", gmdate("H:i:s", $benutzerdaten['u_online']), $lang['chat_msg92']);
		$fett1 = "<b>";
		$fett2 = "</b>";
	} else if ($benutzerdaten['u_login'] != NULL && $online) {
		$text2 .= $trenner . str_replace("%login%", $benutzerdaten['u_login'], $lang['chat_msg94']);
		$fett1 = "";
		$fett2 = "";
	} else if ($online) {
		$text2 .= $trenner . $lang['chat_msg93'];
		$fett1 = "";
		$fett2 = "";
	} else {
		$fett1 = "";
		$fett2 = "";
	}
	
	if ($text2 != "") {
		$text .= "<span class=\"smaller\">" . $text2 . "</span>";
	}
	
	return ($fett1 . $text . $fett2);
}

function formatDate($date, $expectedFormat = 'd. M Y', $currentFormat = 'Y-m-d'){
	$date = DateTime::createFromFormat($currentFormat, $date);
	$date->setTimeZone(new DateTimeZone('Europe/Berlin'));
	return $date->format($expectedFormat);
} 

function chat_parse($text) {
	// Filtert Text und ersetzt folgende Zeichen:
	// http://###### oder www.###### in <a href="http://###" target=_blank>http://###</a>
	// E-Mail Adressen in A-Tag mit Mailto
	
	global $admin, $sprachconfig, $u_id, $u_level;
	
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	
	// doppelte/ungültige Zeichen merken und wegspeichern...
	$text = str_replace("__", "###substr###", $text);
	$text = str_replace("**", "###stern###", $text);
	$text = str_replace("@@", "###klaffe###", $text);
	$text = str_replace("[", "###auf###", $text);
	$text = str_replace("]", "###zu###", $text);
	$text = str_replace("|", "###strich###", $text);
	$text = str_replace("!", "###ausruf###", $text);
	$text = str_replace("+", "###plus###", $text);
	$text = str_replace("<br />", "<br>", $text);
	
	// jetzt nach nach italic und bold parsen...  * in <I>, $_ in <b>
	$text = preg_replace('|\*(.*?)\*|', '<i>\1</i>',
		preg_replace('|_(.*?)_|', '<b>\1</b>', $text));
	
	// erst mal testen ob www oder http oder email vorkommen
	if (preg_match("/(http:|www\.|@)/i", $text)) {
		// Zerlegen der Zeile in einzelne Bruchstücke. Trennzeichen siehe $split
		// leider müssen zunächst erstmal in $text die gefundenen urls durch dummies 
		// ersetzt werden, damit bei der Angabge von 2 gleichen urls nicht eine bereits 
		// ersetzte url nochmal ersetzt wird -> gibt sonst Müll bei der Ausgabe.
		
		// wird evtl. später nochmal gebraucht...
		$split = '/[ \r\n\t,\)\(]/';
		$txt = preg_split($split, $text);
		
		// wieviele Worte hat die Zeile?
		for ($i = 500; $i >= 0; $i--) {
			if (isset($txt[$i]) && $txt[$i] != "") {
				break;
			}
		}
		$text2 = $text;
		// Schleife über alle Worte...
		for ($j = 0; $j <= $i; $j++) {
			// test, ob am Ende der URL noch ein Sonderzeichen steht...
			$txt[$j] = preg_replace("!\?$!", "", $txt[$j]);
			$txt[$j] = preg_replace("!\.$!", "", $txt[$j]);
		}
		for ($j = 0; $j <= $i; $j++) {
			// E-Mail Adressen in A-Tag mit Mailto
			// E-Mail-Adresse -> Format = *@*.*
			if (preg_match("/^.+@.+\..+/i", $txt[$j])
				&& !preg_match("/^href=\"mailto:.+@.+\..+/i", $txt[$j])) {
				// Wort=Mailadresse? -> im text durch dummie ersetzen, im wort durch href.
				$text = preg_replace("!$txt[$j]!", "####$j####", $text);
				$rep = "<a href=\"mailto:" . str_replace("<br>", "", $txt[$j])
					. "\">" . $txt[$j] . "</a>";
				$txt[$j] = str_replace($txt[$j], $rep, $txt[$j]);
			}
			
			// www.###### in <a href="http://###" target=_blank>http://###</A>
			if (preg_match("/^www\..*\..*/i", $txt[$j])) {
				// sonderfall -> "?" in der URL -> dann "?" als Sonderzeichen behandeln...
				$txt2 = preg_replace("!\?!", "\\?", $txt[$j]);
				
				// Doppelcheck, kann ja auch mit http:// in gleicher Zeile nochmal vorkommen. also erst die 
				// http:// mitrausnehmen, damit dann in der Ausgabe nicht http://http:// steht...
				$text = preg_replace("!http://$txt2!", "-###$j####", $text);
				
				// Wort=URL mit www am Anfang? -> im text durch dummie ersetzen, im wort durch href.
				$text = preg_replace("!$txt2!", "####$j####", $text);
				
				// und den ersten Fall wieder Rückwärts, der wird ja später in der schleife nochmal behandelt.
				$text = preg_replace("!-###\d*####/!", "http://$txt2/", $text);
				
				// Ersetzte Zeichen für die URL wieder zurückwandeln
				$txt3 = str_replace("###plus###", "+", $txt2);
				$txt3 = str_replace("###strich###", "|", $txt3);
				$txt3 = str_replace("###auf###", "[", $txt3);
				$txt3 = str_replace("###zu###", "]", $txt3);
				$txt3 = str_replace("###substr###", "_", $txt3);
				$txt3 = str_replace("###stern###", "*", $txt3);
				$txt3 = str_replace("###klaffe###", "@", $txt3);
				
				// url aufbereiten
				$txt[$j] = str_replace($txt2,
					"<a href=\"redirect.php?url="
						. urlencode(
							"http://" . strtr(preg_replace("!\\\\\\?!", "?", $txt3), $trans)) . "\" target=\"_blank\">http://"
						. preg_replace("!\\\\\\?!", "?", $txt2) . "</a>",
					$txt[$j]);
				
				$txt[$j] = str_replace("###ausruf###", "!", $txt[$j]);
				$txt[$j] = str_replace("%23%23%23ausruf%23%23%23", "!", $txt[$j]);
				
			}
			// http://###### in <a href="http://###" target=_blank>http://###</A>
			if (preg_match("!^https?://!", $txt[$j])) {
				// Wort=URL mit http:// am Anfang? -> im text durch dummie ersetzen, im wort durch href.
				// Zusatzproblematik.... könnte ein http-get-URL sein, mit "?" am Ende oder zwischendrin... urgs.
				
				// sonderfall -> "?" in der URL -> dann "?" als Sonderzeichen behandeln...
				$txt2 = preg_replace("!\?!", "\\?", $txt[$j]);
				$text = preg_replace("!$txt2!", "####$j####", $text);
				
				// und wieder Rückwärts, falls zuviel ersetzt wurde...
				$text = preg_replace("!####\d*####/!", "$txt[$j]/", $text);
				
				// Ersetzte Zeichen für die URL wieder zurückwandeln
				$txt3 = str_replace("###plus###", "+", $txt2);
				$txt3 = str_replace("###strich###", "|", $txt3);
				$txt3 = str_replace("###auf###", "[", $txt3);
				$txt3 = str_replace("###zu###", "]", $txt3);
				$txt3 = str_replace("###substr###", "_", $txt3);
				$txt3 = str_replace("###stern###", "*", $txt3);
				$txt3 = str_replace("###klaffe###", "@", $txt3);
				
				// url aufbereiten, \? in ? wandeln
				$txt[$j] = preg_replace("!$txt2!",
					"<a href=\"redirect.php?url="
						. urlencode(
							strtr(preg_replace("!\\\\\\?!", "?", $txt3), $trans))
						. "\" target=\"_blank\">"
						. preg_replace("!\\\\\\?!", "?", $txt2) . "</a>",
					$txt[$j]);
				// echo "\n<br>####<br>\n".$txt2."<br>\n".urlencode(strtr($txt2,$trans))."<br>\n".urldecode(urlencode(strtr($txt2,$trans)))."<br>\n\n";
				
				$txt[$j] = str_replace("###ausruf###", "!", $txt[$j]);
				$txt[$j] = str_replace("%23%23%23ausruf%23%23%23", "!",
					$txt[$j]);
				
			}
		}
		
		// nun noch die Dummy-Strings durch die urls ersetzen...
		for ($j = 0; $j <= $i; $j++) {
			// erst mal "_" in den dummy-strings vormerken...
			// es soll auch urls mit "_" geben ;-)
			$txt[$j] = str_replace("_", "###substr###", $txt[$j]);
			$text = preg_replace("![-#]###$j####!", $txt[$j], $text);
		}
	} // ende http, mailto, etc.
	
	// gemerkte Zeichen zurückwandeln.
	$text = str_replace("###plus###", "+", $text);
	$text = str_replace("###strich###", "|", $text);
	$text = str_replace("###auf###", "[", $text);
	$text = str_replace("###zu###", "]", $text);
	$text = str_replace("###substr###", "_", $text);
	$text = str_replace("###stern###", "*", $text);
	$text = str_replace("###klaffe###", "@", $text);
	$text = str_replace("###ausruf###", "!", $text);
	
	return $text;
}

function hole_geschlecht($user_id) {
	$query = pdoQuery("SELECT `ui_geschlecht` FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$userinfo = $query->fetch();
		$user_geschlecht = $userinfo['ui_geschlecht'];
	}
	
	if ($user_geschlecht == "männlich") {
		$user_geschlecht = "geschlecht_maennlich";
	} else if ($user_geschlecht == "weiblich") {
		$user_geschlecht = "geschlecht_weiblich";
	} else {
		$user_geschlecht = "";
	}
	
	return $user_geschlecht;
}

function ausloggen($u_id, $u_nick, $o_raum, $o_id){
	// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
	
	// Logout falls noch online
	if (strlen($u_id) > 0) {
		verlasse_chat($u_id, $u_nick, $o_raum);
		logout($o_id, $u_id);
		echo "<meta http-equiv=\"refresh\" content=\"0; URL=index.php\">";
	}
}

function verlasse_chat($u_id, $u_nick, $raum) {
	// user $u_id/$u_nick verlässt $raum
	// Nachricht in Raum $raum wird erzeugt
	// Liefert ID des geschriebenen Datensatzes zurück
	global $nachricht_vc, $eintritt_individuell, $lustigefeatures;
	$ergebnis = 0;
	
	// Nachricht an alle
	if ($raum && $u_id) {
		// Nachricht Standard
		$text = $nachricht_vc[0];
		
		// Nachricht Lustige Ein-/Austrittsnachrichten
		if ($lustigefeatures) {
			reset($nachricht_vc);
			$anzahl = count($nachricht_vc);
			$text = $nachricht_vc[mt_rand(1, $anzahl) - 1];
		}
		
		if ($eintritt_individuell == "1") {
			$query = pdoQuery("SELECT `u_austritt` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
			
			$row = $query->fetch();
			if (strlen($row['u_austritt']) > 0) {
				$text = $row['u_austritt'];
				$text = "<b>&lt;&lt;&lt;</b> " . $text . " (<b>$u_nick</b> - verlässt den Chat) ";
			}
			
			$query = pdoQuery("SELECT `r_name` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$raum]);
			
			$row = $query->fetch();
			if (isset($row['r_name'])) {
				$r_name = $row['r_name'];
			} else {
				$r_name = "[unbekannt]";
			}
		}
		
		$text = str_replace("%u_nick%", $u_nick, $text);
		$text = str_replace("%user%", $u_nick, $text);
		$text = str_replace("%r_name%", $r_name, $text);
		
		$text = preg_replace("|%nick%|i", $u_nick, $text);
		$text = preg_replace("|%raum%|i", $r_name, $text);
		
		$ergebnis = global_msg($u_id, $raum, $text);
	}
	
	return $ergebnis;
}

function logout($o_id, $u_id) {
	// Logout aus dem Gesamtsystem
	
	// Tabellen online+user exklusiv locken
	pdoQuery("LOCK TABLES `online` WRITE, `user` WRITE", []);
	
	// Aktuelle Punkte auf Punkte in Benutzertabelle addieren
	$query = pdoQuery("SELECT `o_punkte`, `o_name`, `o_knebel`, UNIX_TIMESTAMP(`o_knebel`)-UNIX_TIMESTAMP(NOW()) AS `knebelrest` FROM `online` WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$row = $query->fetch();
		$u_nick = $row['o_name'];
		$u_punkte = $row['o_punkte'];
		
		if ($row['knebelrest'] > 0) {
			pdoQuery("UPDATE `user` SET `u_punkte_monat` = `u_punkte_monat` + :u_punkte_monat, `u_punkte_jahr` = `u_punkte_jahr` + :u_punkte_jahr, "
					. "`u_punkte_gesamt` = `u_punkte_gesamt` + :u_punkte_gesamt, `u_knebel` = :u_knebel, `u_away` = '' WHERE `u_id` = :u_id",
				[
					':u_punkte_monat'=>$u_punkte,
					':u_punkte_jahr'=>$u_punkte,
					':u_punkte_gesamt'=>$u_punkte,
					':u_knebel'=>$row['o_knebel'],
					':u_id'=>$u_id
				]);
		} else {
			pdoQuery("UPDATE `user` SET `u_punkte_monat` = `u_punkte_monat` + :u_punkte_monat, `u_punkte_jahr` = `u_punkte_jahr` + :u_punkte_jahr, "
					. "`u_punkte_gesamt` = `u_punkte_gesamt` + :u_punkte_gesamt, `u_away` = '' WHERE `u_id` = :u_id",
				[
					':u_punkte_monat'=>$u_punkte,
					':u_punkte_jahr'=>$u_punkte,
					':u_punkte_gesamt'=>$u_punkte,
					':u_id'=>$u_id
				]);
		}
	}
	
	// Benutzer löschen
	pdoQuery("DELETE FROM `online` WHERE `o_id` = :o_id OR `o_user` = :o_user", [':o_id'=>$o_id, ':o_user'=>$u_id]);
	
	// Lock freigeben
	pdoQuery("UNLOCK TABLES", []);
	
	// Punkterepair
	pdoQuery("UPDATE `user` SET `u_punkte_jahr` = 0, `u_punkte_monat` = 0, `u_punkte_datum_jahr` = YEAR(NOW()), `u_punkte_datum_monat` = MONTH(NOW()), `u_login` = `u_login` WHERE `u_punkte_datum_jahr` != YEAR(NOW()) AND `u_id` = :u_id", [':u_id'=>$u_id]);
	pdoQuery("UPDATE `user` SET `u_punkte_monat` = 0, `u_punkte_datum_monat` = MONTH(NOW()), `u_login` = `u_login` WHERE `u_punkte_datum_monat` != MONTH(NOW()) AND `u_id` = :u_id", [':u_id'=>$u_id]);
	
	// Nachrichten an Freunde verschicken
	$query = pdoQuery("SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_userid` = :f_userid AND `f_status` = 'bestaetigt' UNION "
		. "SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_freundid` = :f_freundid AND `f_status` = 'bestaetigt' ORDER BY `f_zeit` DESC", [':f_userid'=>$u_id, ':f_freundid'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			unset($f);
			$f['aktion'] = "Logout";
			$f['f_text'] = $row['f_text'];
			if ($row['f_userid'] == $u_id) {
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
			aktion($u_id, $wann, $an_u_id, $u_nick, "Freunde", $f);
		}
	}
}

function avatar_anzeigen($userId, $username, $anzeigeort, $geschlecht) {
	// Wenn ein Gast den Chat verlässt, hat er keine ID mehr
	if($userId != null && $userId != "") {
		$query = pdoQuery("SELECT `b_name`, `b_height`, `b_width`, `b_mime` FROM `bild` WHERE `b_name` = 'avatar' AND `b_user` = :b_user LIMIT 1", [':b_user'=>$userId]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$row = $query->fetch();
			$avatar[$row['b_name']]['b_mime'] = $row['b_mime'];
			$avatar[$row['b_name']]['b_width'] = $row['b_width'];
			$avatar[$row['b_name']]['b_height'] = $row['b_height'];
		}
	}
	
	// Maximale Größe Avatars festlegen
	if($anzeigeort == "profil") {
		$maxWidth = "200";
		$maxHeight = "200";
	} else if($anzeigeort == "forum") {
		$maxWidth = "80";
		$maxHeight = "80";
	} else {
		$maxWidth = "25";
		$maxHeight = "25";
	}
	
	if (!isset($avatar)) {
		// Platzhalter-Avatar anzeigen
		
		if ($geschlecht == "1") { // Männlicher Standard-Avatar
			$avatar = '<img src="./images/avatars/no_avatar_m.jpg" style="width:'.$maxWidth.'px; height:'.$maxHeight.'px;" alt="'.$username.'" />';
		} else if ($geschlecht == "2") { // Weiblicher Standard-Avatar
			$avatar = '<img src="./images/avatars/no_avatar_w.jpg" style="width:'.$maxWidth.'px; height:'.$maxHeight.'px;" alt="'.$username.'" />';
		} else { // Neutraler Standard-Avatar
			$avatar = '<img src="./images/avatars/no_avatar_es.jpg" style="width:'.$maxWidth.'px; height:'.$maxHeight.'px;" alt="'.$username.'" />';
		}
	} else {
		// Avatar anzeigen
		$avatar = avatar_editieren_anzeigen($userId, $username, $avatar, $anzeigeort, $maxWidth, $maxHeight);
	}
	
	return $avatar;
}

function avatar_editieren_anzeigen($u_id, $u_nick, $bilder, $aufruf, $maxWidth, $maxHeight) {
	global $lang;
	
	if (is_array($bilder) && isset($bilder['avatar']) && $bilder['avatar']['b_mime']) {
		$width = $bilder['avatar']['b_width'];
		$height = $bilder['avatar']['b_height'];
		$mime = $bilder['avatar']['b_mime'];
		
		$info = "<br>Info: " . $width . "x" . $height . " als " . $mime;
		
		if($width > $maxWidth) {
			$width = $maxWidth;
		}
		if($height > $maxHeight) {
			$height = $maxHeight;
		}
		
		if ($aufruf == "avatar_aendern") {
			// Avatar ändern
			$text = "<td style=\"vertical-align:top;\"><img src=\"home_bild.php?u_id=$u_id&feld=avatar\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\"><br>" . $info . "<br>";
			$text .= "<b>[<a href=\"inhalt.php?bereich=einstellungen&aktion=avatar_aendern&bildname=avatar&u_id=$u_id\">$lang[benutzer_loeschen]</a>]</b><br><br></td>\n";
		} else if ($aufruf == "profil") {
			// Profil
			$text = "<img src=\"home_bild.php?u_id=$u_id&feld=avatar\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\">";
		} else if ($aufruf == "forum") {
			// Forum
			$text = "<img src=\"home_bild.php?u_id=$u_id&feld=avatar\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\">";
		} else {
			// Chat
			$text = "<img src=\"home_bild.php?u_id=$u_id&feld=avatar\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\">";
		}
	} else if ($aufruf == "avatar_aendern") {
		$text = "<td style=\"vertical-align:top;\">";
		$text .= "<form enctype=\"multipart/form-data\" name=\"home\" action=\"inhalt.php?bereich=einstellungen\" method=\"post\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"avatar_aendern\">\n";
		$text .= "<input type=\"hidden\" name=\"u_id\" value=\"$u_id\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion3\" value=\"avatar_hochladen\">\n";
		$text .= "$lang[user_kein_bild_hochgeladen] <input type=\"file\" name=\"avatar\" size=\"" . (55 / 8) . "\"><br>";
		$text .= "<br><input type=\"submit\" value=\"GO\"></form><br><br></td>";
	}
	
	if ($text && $aufruf == "avatar_aendern") {
		$text = "<table style=\"width:100%;\"><tr>" . $text . "</tr></table>";
	}
	
	return $text;
}

function zeige_profilinformationen_von_id($profilfeld, $key) {
	// Löse die IDs in menschliche Informationen auf
	global $lang, $sprache;
	
	require_once("languages/$sprache-profil.php");
	
	if($profilfeld == "geschlecht") {
		if($key == 1) {
			return $lang['profil_geschlecht_maennlich'];
		} else if($key == 2) {
			return $lang['profil_geschlecht_weiblich'];
		} else if($key == 3) {
			return $lang['profil_geschlecht_divers'];
		} else {
			return $lang['profil_keine_angabe'];
		}
	} else if($profilfeld == "beziehungsstatus") {
		if($key == 1) {
			return $lang['profil_verheiratet'];
		} else if($key == 2) {
			return $lang['profil_ledig'];
		} else if($key == 3) {
			return $lang['profil_vergeben'];
		} else if($key == 4) {
			return $lang['profil_single'];
		} else {
			return $lang['profil_keine_angabe'];
		}
	} else if($profilfeld == "typ") {
		if($key == 1) {
			return $lang['profil_typ_zierlich'];
		} else if($key == 2) {
			return $lang['profil_typ_schlank'];
		} else if($key == 3) {
			return $lang['profil_typ_sportlich'];
		} else if($key == 4) {
			return $lang['profil_typ_normal'];
		} else if($key == 5) {
			return $lang['profil_typ_mollig'];
		} else if($key == 6) {
			return $lang['profil_typ_dick'];
		} else {
			return $lang['profil_keine_angabe'];
		}
	} else {
		return '';
	}
}

// Beiträge im Forum neu zählen
function bereinige_anz_in_thema() {
	$query = pdoQuery("SELECT `forum_id` FROM `forum_foren` ORDER BY `forum_id`", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			pdoQuery("LOCK TABLES `forum_beitraege` WRITE, `forum_foren` WRITE", []);
			
			$result2 = pdoQuery("SELECT COUNT(*) AS `anzahl` FROM `forum_beitraege` WHERE `beitrag_thema_id` = 0 AND `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_forum_id'=>$row['forum_id']])->fetch();
			$anzahl_thread = $result2['anzahl'];
			
			$result2 = pdoQuery("SELECT COUNT(*) AS `anzahl` FROM `forum_beitraege` WHERE `beitrag_thema_id` <> 0 AND `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_forum_id'=>$row['forum_id']])->fetch();
			$anzahl_reply = $result2['anzahl'];
			
			pdoQuery("UPDATE `forum_foren` SET `forum_anzahl_themen` = :forum_anzahl_themen, `forum_anzahl_antworten` = :forum_anzahl_antworten WHERE `forum_id` = :forum_id", [':forum_anzahl_themen'=>$anzahl_thread, ':forum_anzahl_antworten'=>$anzahl_reply, ':forum_id'=>$row['forum_id']]);
			
			pdoQuery("UNLOCK TABLES", []);
		}
	}
}

function reset_system($wo_online) {
	global $u_level, $o_raum;
	// Reset ausführen
	if ( $wo_online == "userliste" ) {
		echo "<script>\n";
		echo "parent.frames[2].location.href='user.php';\n";
		echo "</script>";
	} else if ( $wo_online == "userliste_interaktiv" ) {
		echo "<script>\n";
		echo "parent.frames[2].location.href='user.php';\n";
		echo "</script>";
	} else if ( $wo_online == "forum" ) {
		echo "<script>\n";
		echo "parent.frames[2].location.href='user.php';\n";
		echo "</script>";
	} else if ( $wo_online == "moderator" ) {
		echo "<script>\n";
		echo "parent.frames[4].location.href='moderator.php';\n";
		echo "</script>";
	} else if ($u_level == "M") {
		echo "<script>\n";
		echo "parent.frames[0].location.href='navigation.php';";
		echo "parent.frames[1].location.href='chat.php';\n";
		echo "parent.frames[2].location.href='user.php';\n";
		echo "parent.frames[3].location.href='eingabe.php';\n";
		echo "parent.frames[4].location.href='moderator.php';\n";
		echo "</script>";
	} else {
		echo "<script>\n";
		echo "parent.frames[0].location.href='navigation.php';";
		echo "parent.frames[1].location.href='chat.php';\n";
		echo "parent.frames[2].location.href='user.php';\n";
		echo "parent.frames[3].location.href='eingabe.php';\n";
		echo "</script>";
	}
}

function hole_benutzerliste() {
	$query = pdoQuery("SELECT `u_nick` FROM `user` WHERE `u_level` NOT IN ('Z', 'G')", []);
	
	$result = $query->fetchAll();
	
	return $result;
}

function hole_benutzer_einstellungen($u_id, $ort) {
	if($ort == "chatausgabe") {
		// Benötigte Einstellugen für das Chatfenster
		$query = pdoQuery("SELECT `u_systemmeldungen`, `u_avatare_anzeigen`, `u_layout_farbe`, `u_layout_chat_darstellung`, `u_smilies` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
		
		$benutzerdaten = $query->fetch();
	} else if($ort == "standard") {
		// Benötigte Einstellungen für alle anderen Seiten
		$query = pdoQuery("SELECT `u_layout_farbe` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
		
		$benutzerdaten = $query->fetch();
	} else {
		// Benötigte Einstellugen für alle Seiten im Login
		$benutzerdaten = array();
		$benutzerdaten['u_layout_farbe'] = 0;
	}
	
	return $benutzerdaten;
}

/*
 * Datum formatieren
 * Beispiel:
 * formatIntl($date,'EEEE dd. MMMM Y','de_DE');
 * EEEE: Wochentag
 * d: Tag
 * MMMM: Monat
 * Y: Jahr
 */
function formatIntl(DateTimeInterface $date, string $format, string $language) {
	//check calendar in $language
	$calType = (stripos($language,"@calendar") > 0
	AND stripos($language,"gregorian") === false)
	? IntlDateFormatter::TRADITIONAL
	: IntlDateFormatter::GREGORIAN;
	
	$intlFormatter = datefmt_create( $language ,
		IntlDateFormatter::FULL,
		IntlDateFormatter::FULL,
		$date->getTimezone(),
		$calType,
		$format);
	return datefmt_format($intlFormatter ,$date);
}

function email_senden($empfaenger, $betreff, $inhalt) {
	global $smtp_on, $smtp_sender, $chat, $chat_url, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS;
	
	$absender = $smtp_sender;
	
	// Anfang und Ende an jede E-Mail anhängen
	$inhalt_anfang = "<h1>$chat</h1><br>";
	$inhalt_ende = "<br><br>" . "-- <br>   $chat ($chat_url)<br>";
	$inhalt = $inhalt_anfang . $inhalt . $inhalt_ende;
	
	// E-Mail versenden
	if($smtp_on) {
		mailsmtp($empfaenger, $betreff, $inhalt, $absender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
	} else {
		// Der PHP-Vessand benötigt \n und nicht <br>
		$inhalt = str_replace("<br>", "\n", $inhalt);
		
		$remote_addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
		
		$charset = "\n" . "X-MC-IP: " . $remote_addr . "\n" . "X-MC-TS: " . time();
		$charset .= "Mime-Version: 1.0\r\n";
		$charset .= "Content-type: text/plain; charset=utf-8";
		
		mail($empfaenger, $betreff, $inhalt, "From: $absender ($chat)" . $charset);
	}
}

function mailsmtp($mailempfaenger, $mailbetreff, $inhalt, $header, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS) {
	global $chat;
	
	require('functions/functions-smtp.php');
}

/**
 * Hasht das eingegebene Passwort
 * @param string $password Eingegebenes Psswort des Mitglieds
 * @return string Gibt das gehashte Passwort zurück
 */
function encrypt_password($password) {
	global $secret_salt;
	
	$salted_password = $secret_salt.$password;
	$password_hash = hash('sha256', $salted_password);
	
	return $password_hash;
}

function random_string() {
	global $secret_salt;
	
	if(function_exists('random_bytes')) {
		$bytes = random_bytes(16);
		$str = bin2hex($bytes);
	} else if(function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes(16);
		$str = bin2hex($bytes);
	} else if(function_exists('mcrypt_create_iv')) {
		$bytes = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
		$str = bin2hex($bytes);
	} else {
		//Bitte euer_geheim_string durch einen zufälligen String mit >12 Zeichen austauschen
		$str = md5(uniqid($secret_salt, true));
	}
	return $str;
}

function hinweis($text, $typ = "fehler") {
	global $lang;
	
	if($typ == "fehler") {
		return "<p class=\"fehler\"><span class=\"fa-solid fa-exclamation-triangle icon24\" alt=\"$lang[hinweis_fehler]\" title=\"$lang[hinweis_fehler]\"></span> $text</p>\n";
	} else if($typ == "erfolgreich") {
		return "<p class=\"erfolgreich\"><span class=\"fa-solid fa-check-square icon24\" alt=\"$lang[hinweis_erfolgreich]\" title=\"$lang[hinweis_erfolgreich]\"></span> $text</p>\n";
	} else {
		return "<p class=\"hinweis\"><span class=\"fa-solid fa-info icon24\" alt=\"$lang[hinweis_hinweis]\" title=\"$lang[hinweis_hinweis]\"></span> $text</p>\n";
	}
}
?>