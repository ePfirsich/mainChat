<?php
require_once("functions/functions-nachrichten_betrete_verlasse.php");
require_once("functions/functions-html_parse.php");
require_once("functions/functions-raum_gehe.php");
require_once("functions/functions-freunde.php");

function chat_msg($o_id, $u_id, $u_nick, $u_farbe, $admin, $r_id, $text, $typ) {
	// Schreibt Text in Raum r_id
	// Benutzer, u_id, Farbe, Raum und Typ der Nachricht werden übergeben
	// Vor Schreiben wird der Text geparsed und die Aktionen ausgeführt
	// Art:		   N: Normal
	//			  S: Systemnachricht
	//				P: Privatnachticht
	//				H: Versteckte Nachricht
	// $raum_einstellungen und $ist_moderiert wurde von raum_ist_moderiert() gesetzt
	
	global $user_farbe, $hilfstext, $system_farbe;
	global $chat, $timeout, $datei_spruchliste, $lang, $ak, $check_name, $raumstatus1, $raum_max;
	global $lobby, $o_raum, $o_knebel, $r_status1, $u_level, $max_user_liste;
	global $o_punkte, $raum_einstellungen, $ist_moderiert, $ist_eingang, $lustigefeatures;
	global $punkte_ab_user, $punktefeatures, $whotext, $knebelzeit, $nickwechsel, $raumanlegenpunkte, $o_dicecheck;
	
	// Text $text parsen, Befehle ausführen, Texte im Chat ausgeben
	
	// Voreinstellung für Nachrichtenfilter
	$privat = FALSE;
	
	// N als Voreinstellung setzen
	$typ OR $typ = "N";
	
	// Farbe voreinstellen
	$u_farbe OR $u_farbe = $user_farbe;
	
	// Verbotene Zeichen filtern
	$text = preg_replace("/[" . chr(1) . "-" . chr(31) . "]/", "", $text);
	$text = str_replace(chr(173), "", $text);
	
	// autoknebel - ist text unerwünscht? Falls ja, Benutzer automatisch knebeln
	$text = auto_knebel($text);
	
	// Eingabe parsen
	$chatzeile = explode(" ", $text, 4);
	if (!isset($chatzeile[1])) {
		$chatzeile[1] = "";
	}
	if (!isset($chatzeile[2])) {
		$chatzeile[2] = "";
	}
	if (!isset($chatzeile[3])) {
		$chatzeile[3] = "";
	}
	
	switch (strtolower($chatzeile[0])) {
		case "/oplist":
		// Admins auflisten
			if ($admin || $u_level == "A") {
				$query = pdoQuery("SELECT `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4`, `o_level`, `r_name`, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_login`) AS `online` "
					. "FROM `online` LEFT JOIN `raum` ON `r_id` = `o_raum` WHERE (`o_level` = 'S' OR `o_level` = 'C' OR `o_level` = 'A') ORDER BY `r_name`", [':u_id'=>$u_id]);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$result = $query->fetchAll();
					foreach($result as $zaehler => $row) {
						$userdaten = unserialize($row['o_userdata'] . $row['o_userdata2'] . $row['o_userdata3'] . $row['o_userdata4']);
						$txt = "";
						if ($row['r_name'] && $row['r_name'] != "NULL") {
							$raumname = $row['r_name'];
						} else {
							$raumname = "[" . $whotext[2] . "]";
						}
						if (!$userdaten['u_away']) {
							$txt .= "<b>" . $raumname . ":</b> "
								. zeige_userdetails($userdaten['u_id'], TRUE);
						} else {
							$txt .= $raumname . ": "
								. zeige_userdetails($userdaten['u_id'], FALSE) . " -> " . $userdaten['u_away'];
						}
						system_msg("", 0, $u_id, $system_farbe, $txt);
					}
				}
			}
			break;
		
		case "/op":
			// Nachrichten zwischen den Admins austauschen
		case "/int":
		// Admin um Hilfe rufen.
			if ($u_level == "G") {
				system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg55']);
			} else {
				
				if ($o_knebel > 0) {
					// user ist geknebelt...
					$zeit = gmdate("H:i:s", $o_knebel);
					$txt = str_replace("%zeit%", $zeit, $lang['knebel6']);
					system_msg("", 0, $u_id, $system_farbe, $txt);
					break;
				}
				// Smilies parsen
				$privat = TRUE;
				
				$query = pdoQuery("SELECT `r_name` FROM raum WHERE `r_id` = :r_id", [':r_id'=>$o_raum]);
				
				$result = $query->fetch();
				$r_name = $result['r_name'];
				
				if (strtolower($chatzeile[0] != "/int")) {
					$query = pdoQuery("SELECT `o_user` FROM `online` WHERE `o_level` = 'S' OR `o_level` = 'C' OR `o_level` = 'A'", []);
				} else {
					$query = pdoQuery("SELECT `o_user` FROM `online` WHERE `o_level` = 'S' OR `o_level` = 'C'", []);
				}
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					// die Nachricht wurde weitergeleitet.
					$txt = html_parse($privat, htmlspecialchars( $chatzeile[1] . " " . $chatzeile[2] . " " . $chatzeile[3]))[0];
					if (!($admin || $u_level == "A")) {
						// Admins wissen, daß das an alle Admins geht. daher nicht nötig.
						system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg47']);
						$txt = "<b>" . $txt . "</b>";
					}
					if (strtolower($chatzeile[0]) == "/int") {
						$txt2 = "<b>[=====$lang[chat_msg52]&nbsp;" . $lang['chat_msg51'] . "=====]</b> " . $txt;
					} else {
						$txt2 = "<b>[" . $lang['chat_msg52'] . "&nbsp;" . $lang['chat_msg50'] . "]</b> " . $txt;
					}
					
					$result = $query->fetchAll();
					foreach($result as $zaehler => $row) {
						$text = str_replace("%user%", zeige_userdetails($u_id), $lang['chat_msg49']);
						$text = str_replace("%raum%", $r_name, $text);
						if (!($admin || $u_level == "A")) {
							// Benutzer aus Raum... ruft um hilfe
							system_msg("", $u_id, $row['o_user'], $system_farbe, $text);
						}
						if (($admin || $u_level == "A") && $row['o_user'] == $u_id) {
							// falls eigener nick:
							if (strtolower($chatzeile[0]) == "/int") {
								system_msg("", $u_id, $u_id, $system_farbe, "<b>" . zeige_userdetails($u_id) . "&nbsp; ===== $lang[chat_msg24] $lang[chat_msg51]: =====</b> $txt");
							} else {
								system_msg("", $u_id, $u_id, $system_farbe, "<b>" . zeige_userdetails($u_id) . "&nbsp; $lang[chat_msg24] $lang[chat_msg50]: </b> $txt");
							}
						} else {
							priv_msg($u_nick, $u_id, $row['o_user'], $u_farbe, $txt2);
						}
					}
				} else {
					// kein Admin im Chat :-(
					system_msg("", $u_id, $u_id, $system_farbe, $lang['chat_msg48']);
				}
			}
			break;
		
		case "/dupes":
		// sucht nach doppelten IPs
			if ($admin) {
				$query = pdoQuery("SELECT `o_ip`, `o_user`, `o_raum`, `o_browser`, `r_name`, `o_name` "
					. "FROM `online` LEFT JOIN `raum` ON `o_raum` = `r_id` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout ORDER BY `o_ip`", [':timeout'=>$timeout]);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$result = $query->fetchAll();
					$dupecount = 0;
					foreach($result as $zaehler => $row) {
						if (isset($alt) && $row['o_ip'] == $alt['o_ip']) {
							$hostname = htmlspecialchars(@gethostbyaddr($row['o_ip']));
							if (!$shown) {
								$dupecount++;
								$txt = "<br><b>" . $hostname . "(" . $alt['o_ip'] . "):</b>";
								$txt .= "<br><b>" . $alt['r_name'] . " " . zeige_userdetails($alt['o_user']) . "</b> " . htmlspecialchars($alt['o_browser']) . "<br>";
								$shown = true;
							}
							$txt .= "<b>" . $row['r_name'] . " " . zeige_userdetails($row['o_user']) . "</b> " . htmlspecialchars($row['o_browser']);
							system_msg("", 0, $u_id, $system_farbe, $txt);
							$txt = "";
						} else {
							$shown = false;
						}
						$alt = $row;
					}
					if ($dupecount > 0) {
						system_msg("", 0, $u_id, $system_farbe, "<br>\n" . $lang['chat_msg45']);
					} else {
						system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg46']);
					}
				}
			}
			break;
		
		case "/pp":
		case "/ip":
			if (!$admin) {
				system_msg("", 0, $u_id, $system_farbe, "<b>He $u_nick!</b> " . str_replace("%chatzeile%", $chatzeile[0], $lang['chat_msg1']));
				break;
			}
			
			unset($onlineip);
			unset($onlinedatum);
			
			if (!$chatzeile[1]) {
				system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg119']);
				break;
			}
			
			if ($admin && preg_match( "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $chatzeile[1])) {
				$onlineip = $chatzeile[1];
			} else if ($admin) {
				$nick = nick_ergaenze($chatzeile[1], "online", 1);
				
				if (isset($nick['u_nick']) && $nick['u_nick'] != "") {
					$query = pdoQuery("SELECT `o_ip` FROM `online` WHERE `o_user` = :o_user", [':o_user'=>$nick['u_id']]);
					
					$resultCount = $query->rowCount();
					if ($resultCount == 1) {
						$result = $query->fetch();
						$onlineip = $result['o_ip'];
					}
				} else {
					system_msg("", 0, $u_id, $system_farbe, str_replace("%u_nick%", $chatzeile[1], $lang['chat_msg120']));
					
					// Nick nich Online - Hole IP aus Historie der Benutzertabelle
					$query = pdoQuery("SELECT `u_nick`, `u_id`, `u_ip_historie` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>coreCheckName($chatzeile[1], $check_name)]);
					
					$resultCount = $query->rowCount();
					if ($resultCount == 1) {
						$result = $query->fetchAll();
						$uu_ip_historie = unserialize($result['u_ip_historie']);
						
						if (is_array($uu_ip_historie)) {
							foreach($uu_ip_historie as $onlinedatum => $onlineip) {
								break;
							}
							$temp = $lang['chat_msg123'];
							$temp = str_replace("%datum%", date("d.m.y H:i", $onlinedatum), $temp);
							$temp = str_replace("%ip%", $onlineip, $temp);
							system_msg("", 0, $u_id, $system_farbe, $temp);
						}
					}
				}
			}
			
			if ($admin && $onlineip) {
				$date = date('Y-m-d H:i:s');
				$temp = str_replace("%datum%", formatDate($date,"d.m.Y H:i","Y-m-d H:i:s"), $lang['chat_msg122']);
				$temp = str_replace("%ip%", $onlineip, $temp);
				system_msg("", 0, $u_id, $system_farbe, $temp);
				
				$query = pdoQuery("SELECT `o_ip`, `o_user`, `o_raum`, `o_browser`, `r_name`, `o_name` FROM `online`
					LEFT JOIN `raum` ON `o_raum` = `r_id` WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout AND `o_ip` = :o_ip ORDER BY `o_user`", [':timeout'=>$timeout, ':o_ip'=>$onlineip]);
				
				$result = $query->fetchAll();
				if ($resultCount >= 1) {
					$txt = "";
					$resultCount = $query->rowCount();
					foreach($result as $zaehler => $row) {
						$txt .= "<b>" . $row['r_name'] . " " . zeige_userdetails($row['o_user']) . "</b> " . htmlspecialchars($row['o_browser']) . "<br>";
					}
					system_msg("", 0, $u_id, $system_farbe, $txt);
				} else {
					system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg121']);
				}
			}
			
			break;
		
		case "/edithistory":
		case "/edithistorie":
			if ($admin) {
				$nick = nick_ergaenze($chatzeile[1], "online", 1);
				
				// Falls keinen Empfänger gefunden, in Benutzertabelle nachsehen
				if ($nick['u_nick'] == "") {
					$query = pdoQuery("SELECT `u_nick`, `u_id` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>coreCheckName($chatzeile[1], $check_name)]);
					
					$resultCount = $query->rowCount();
					if ($resultCount == 1) {
						$nick = $query->fetch();
					}
				}
				
				if ($nick['u_nick'] != "") {
					$query = pdoQuery("SELECT `u_profil_historie` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$nick['u_nick']]);
					
					$resultCount = $query->rowCount();
					$bla = $query->fetchAll();
					$uu_profil_historie = unserialize($bla['u_profil_historie']);
				} else {
					system_msg("", 0, $u_id, $system_farbe, "Es konnte kein eindeutiger Nick gefunden werden!");
				}
				
				if (is_array($uu_profil_historie)) {
					system_msg("", 0, $u_id, $system_farbe, "Das Profil von <b>$nick[u_nick]</b> wurde zuletzt geändert von: ");
					foreach($uu_profil_historie as $datum => $nick) {
						$zeile = $nick . "&nbsp;(" . str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum)) . ")";
						system_msg("", 0, $u_id, $system_farbe, $zeile);
					}
				}
			} else {
				system_msg("", 0, $u_id, $system_farbe, "<b>He $u_nick!</b> " . str_replace("%chatzeile%", $chatzeile[0], $lang['chat_msg1']));
			}
			
			break;
		
		case "/knebel":
		case "/gag":
		case "/gaga":
			//   /gag - zeige alle geknebelten user
			//   /gag user - sperre user für 5 min
			//   /gag user x  - sperre user für x min 
			//   /gag user 0  - freigabe...
		
			if (!$admin AND $u_level != "A") {
				system_msg("", 0, $u_id, $system_farbe, "<b>He $u_nick!</b> " . str_replace("%chatzeile%", $chatzeile[0], $lang['chat_msg1']));
				break;
			}
			
			if (!isset($chatzeile[1]) || $chatzeile[1] == "") {
				// 	Kurzhilfe ausgeben.
				$temp = $lang['knebel2'];
				$temp = str_replace("%chatzeile%", $chatzeile[0], $temp);
				system_msg("", 0, $u_id, $system_farbe, "<b>$chat</b>: $temp");
			} else {
				// knebel setzen, default=5 min...
				if (!$knebelzeit) {
					$knebelzeit = 5;
				}
				
				// knebelzeit übergeben? dann setzen, absolut, vorzeichen ignorieren
				if (isset($chatzeile[2]) && $chatzeile[2] != "") {
					$knebelzeit = abs(intval($chatzeile[2]));
				}
				
				// maximal 1440 Minuten
				if ($knebelzeit > 1440) {
					$knebelzeit = 1440;
				}
				
				// Benutzer finden, zuerst im aktuellen Raum, dann global
				$nick = nick_ergaenze($chatzeile[1], "raum", 1);
				if (!$nick['u_nick']) {
					$nick = nick_ergaenze($chatzeile[1], "online", 0);
				}
				
				// Admins dürfen,
				if ($nick['u_nick']) {
					switch ($nick['u_level']) {
						case "C":
						case "S":
						case "M":
						case "A":
							$txt = str_replace("%admin%", $nick['u_nick'], $lang['knebel5']);
							system_msg("", 0, $u_id, $system_farbe, $txt);
							break;
						default:
							pdoQuery("UPDATE `online` SET `o_knebel` = :o_knebel WHERE `o_user` = :o_user", [':o_knebel'=>"FROM_UNIXTIME(UNIX_TIMESTAMP(NOW())+" . intval($knebelzeit) . "*60)", ':o_user'=>$nick['u_id']]);
							
							$txt = ($knebelzeit ? $lang['knebel4'] : $lang['knebel3']);
							$txt = str_replace("%admin%", $u_nick, $txt);
							$txt = str_replace("%user%", $nick['u_nick'], $txt);
							$txt = str_replace("%zeit%", $knebelzeit, $txt);
							global_msg($u_id, $o_raum, $txt);
					}
				}
			}
			
			// 	knebel listen 
			if ($chatzeile[0] == "/gaga" && $admin) {
				$query = pdoQuery("SELECT `o_id`, `o_raum`, `o_user`, UNIX_TIMESTAMP(`o_knebel`)-UNIX_TIMESTAMP(NOW()) AS `knebel` FROM `online` WHERE `o_knebel` > NOW()", []);
			} else {
				$query = pdoQuery("SELECT `o_id`, `o_raum`, `o_user`, UNIX_TIMESTAMP(`o_knebel`)-UNIX_TIMESTAMP(NOW()) AS `knebel` FROM `online` WHERE `o_raum` = :o_raum AND `o_knebel` > NOW()", [':o_raum'=>$o_raum]);
			}
			
			
			$txt = "";
			$resultCount = $query->rowCount();
			if ($resultCount > 0) {
				$result = $query->fetchAll();
				foreach($result as $zaehler => $row) {
					if ($txt != "") {
						$txt .= ", ";
					}
					$txt .= zeige_userdetails($row['o_user']) . " " . gmdate("H:i:s", $row['knebel']);
				}
			}
			
			if ($txt != "") {
				system_msg("", 0, $u_id, $system_farbe, "<b>$chat</b>:$lang[knebel1]: " . $txt);
			}
			break;
		
		case "/einlad":
		case "/invite":
			// Einladen in Räume... darf nur Admin oder Raumbesitzer
			// Raum ermitteln
			$raum_id = $r_id;
			if ($chatzeile[2] != "") {
				$query = pdoQuery("SELECT `r_id` from `raum` WHERE `r_name` LIKE :r_name", [':r_name'=>"$chatzeile[2]%"]);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$row = $query->fetch();
					$raum_id = $row['r_id'];
				} else {
					// Raum nicht gefunden -> Fehlermeldung
					$txt = str_replace("%raumname%", $chatzeile[1], $lang['chat_msg53']);
					system_msg("", $u_id, $u_id, $system_farbe, $txt);
					break;
				}
			}
		
			// Besitzer des Raums ermitteln
			$query = pdoQuery("SELECT `r_besitzer`, `r_name` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$raum_id]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				$result = $query->fetch();
				$r_besitzer = $result['r_besitzer'];
				$r_name = $result['r_name'];
			}
			if ($admin || $r_besitzer == $u_id) {
				if ($chatzeile[1] != "") {
					// user eintragen oder löschen
					
					// schaue zuerst, ob online ein Benutzer ist, der genauso heisst wie angegeben
					$nick = nick_ergaenze($chatzeile[1], "online", 1);
					if ($nick['u_nick'] == $chatzeile[1]) {
					} else {
						$nick['u_nick'] == "";
					}
					
					if ($nick['u_nick'] == "")
						$nick = nick_ergaenze($chatzeile[1], "raum", 1);
					if ($nick['u_nick'] == "")
						$nick = nick_ergaenze($chatzeile[1], "online", 1);
					if ($nick['u_nick'] == "")
						$nick = nick_ergaenze($chatzeile[1], "chat", 0);
					if ($nick['u_nick'] != "") {
						// nick gefunden. jetzt eintragen oder löschen...
						$query = pdoQuery("SELECT `inv_user` FROM `invite` WHERE `inv_raum` = :inv_raum AND `inv_user` = :inv_user", [':inv_raum'=>$raum_id, ':inv_user'=>$nick['u_id']]);
						
						$resultCount = $query->rowCount();
						$result = $query->fetch();
						if ($result > 0) {
							if ($resultCount > 0) {
								pdoQuery("DELETE FROM `invite` WHERE `inv_raum` = :inv_raum AND `inv_user` = :inv_user", [':inv_raum'=>$raum_id, ':inv_user'=>$nick['u_id']]);
								
								$msg = $lang['invite4'];
							} else {
								$f['inv_user'] = $nick['u_id'];
								$f['inv_raum'] = $raum_id;
								
								pdoQuery("INSERT INTO `invite` (`inv_user`, `inv_raum`) VALUES (:inv_user, :inv_raum)",
									[
										':inv_user'=>$f['inv_user'],
										':inv_raum'=>$f['inv_raum']
									]);
								
								$msg = str_replace("%admin%", $u_nick, $lang['invite5']);
								$msg = str_replace("%raum%", $r_name, $msg);
								system_msg($u_nick, $u_id, $nick['u_id'], $system_farbe, $msg);
								$msg = $lang['invite3'];
							}
							
							$msg = str_replace("%admin%", $u_nick, $msg);
							$msg = str_replace("%raum%", $r_name, $msg);
							$msg = str_replace("%user%", $nick['u_nick'], $msg);
							global_msg($u_id, $o_raum, "$msg");
						}
					} else {
						// Nick nicht gefunden, d.h. nicht Online, aber vielleicht doch eingeladen zum runterwerfen?
						$query = pdoQuery("SELECT `u_nick`, `u_id` FROM `user`, `invite` WHERE `inv_raum` = :inv_raum AND `inv_user` = `u_id` AND `u_nick` = :u_nick ORDER BY `u_nick`", [':inv_raum'=>$raum_id, ':u_nick'=>$chatzeile[1]]);
						
						$resultCount = $query->rowCount();
						if ($resultCount == 1) {
							$row = $query->fetch();
							pdoQuery("DELETE FROM `invite` WHERE `inv_raum` = :inv_raum AND `inv_user` = :inv_user", [':inv_raum'=>$raum_id, ':inv_user'=>$row['u_id']]);
							
							$msg = $lang['invite4'];
							$msg = str_replace("%admin%", $u_nick, $msg);
							$msg = str_replace("%raum%", $r_name, $msg);
							$msg = str_replace("%user%", $row['u_nick'], $msg);
							global_msg($u_id, $o_raum, "$msg");
						}
					}
				}
				// Invite-Liste ausgeben...
				$query = pdoQuery("SELECT `u_id`, `inv_id` FROM `invite` LEFT JOIN `user` ON `u_id` = `inv_user` WHERE `inv_raum` = :inv_raum", [':inv_raum'=>$raum_id]);
				
				$txt = "";
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$result = $query->fetchAll();
					foreach($result as $zaehler => $row) {
						if ($row['u_id'] && $row['u_id'] != "NULL") {
							if ($txt != "") {
								$txt .= ", ";
							}
							$txt .= zeige_userdetails($row['u_id']);
						} else {
							pdoQuery("DELETE FROM `invite` WHERE `inv_id` = :inv_id", [':inv_id'=>$row['inv_id']]);
						}
					}
				}
				if ($txt == "")
					$txt = $lang['invite2'];
				else $txt = $lang['invite1'] . " " . $txt;
				system_msg("", 0, $u_id, $system_farbe, "<b>$chat:</b> $txt");
			}
			break;
		
		case "/such":
		case "/suche":
		// Gibt Spruchliste aus
			if (!isset($chatzeile[1])) {
				$chatzeile[1] = "";
			}
			$suchtext = preg_replace("/[*%$!?.,;:\\/]/i", "", $chatzeile[1]);
			if (strlen($suchtext) > 2) {
				// Sprüche in Array lesen
				$spruchliste = file("conf/$datei_spruchliste");
				
				// Treffer ausgeben
				reset($spruchliste);
				$anzahl = count($spruchliste);
				$i = 0;
				while ($i < $anzahl) {
					$spname = key($spruchliste);
					if (preg_match("/" . $suchtext . "/i", $spruchliste[$spname])) {
						$spruchtmp = preg_split("/\t/", substr($spruchliste[$spname], 0, strlen($spruchliste[$spname]) - 1), 3);
						
						$spruchtmp[2] = str_replace("<", "&lt;", $spruchtmp[2]);
						$spruchtmp[2] = str_replace(">", "&gt;", $spruchtmp[2]);
						$spruchtmp[2] = preg_replace('|\*(.*?)\*|', '<i>\1</i>', preg_replace('|_(.*?)_|', '<b>\1</b>', $spruchtmp[2]));
						
						$txt = "<b>$lang[chat_msg4] <I>" . $spruchtmp[0] . " " . $spruchtmp[1] . "</I></b> &lt;" . $spruchtmp[2] . "&gt;";
						system_msg("", 0, $u_id, $system_farbe, $txt);
					}
					next($spruchliste);
					$i++;
				}
			
			} else {
				// Fehler ausgeben
				system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg5']);
			}
			break;
		
		case "/ende":
		case "/exit":
		case "/quit":
		case "/bye":
		// Ende-Nachricht absetzen?
			if (strlen($chatzeile[1]) > 0) {
				hidden_msg($u_nick, $u_id, $u_farbe, $r_id, $u_nick . "$lang[chat_msg6] "
					. html_parse($privat, htmlspecialchars($chatzeile[1] . " " . $chatzeile[2] . " " . $chatzeile[3]))[0] );
			}
			
			// Aus dem Chat ausloggen
			ausloggen($u_id, $u_nick, $r_id, $o_id);
			?>
			<!DOCTYPE html>
			<html dir="ltr" lang="de">
			<body onLoad='javascript:parent.location.href="index.php"'>
			</body>
			</html>
			<?php
			exit;
			break;
		
		case "/ig":
		case "/ignore":
		case "/ignoriere":
		// Benutzer ignorieren oder freigeben
		
			if (strlen($chatzeile[1]) > 0 && $u_level != "G") {
				// Benutzername angegeben -> ignorieren oder aufheben
				
				// versuchen, den namen zu ergänzen.
				// wenn geschafft, dann ist Benutzer auch online. Somit kann er ignoriert werden.
				// sonst evtl. DB Probs weil der expire evtl. user löscht, die hier gerade 
				// eingetragen werden.
				$nick = nick_ergaenze($chatzeile[1], "online", 0);
				if ($nick['u_nick'] != "") {
					$i_user_passiv = $nick['u_id'];
					$i_user_name_passiv = $nick['u_nick'];
					
					// testen, ob nicht DAU sich selbst ignoriert...
					if ($i_user_passiv != $u_id) {
						ignore($o_id, $u_id, $u_nick, $i_user_passiv, $i_user_name_passiv);
					} else {
						system_msg("", 0, $u_id, $u_farbe, $lang['chat_msg7']);
					}
				} else {
					// Benutzer nicht gefunden oder nicht online? dann testen, ob ignoriert, damit man das ignore auch wieder raus bekommt.
					$query = pdoQuery("SELECT `u_nick`, `u_id` FROM `user,iignore` WHERE `i_user_aktiv` = :i_user_aktiv AND `i_user_passiv` = `u_id` AND `u_nick` = :u_nick ORDER BY `u_nick`", [':i_user_aktiv'=>$u_id, ':u_nick'=>$chatzeile[1]]);
					
					$resultCount = $query->rowCount();
					if ($resultCount == 1) {
						$row = $query->fetch();
						ignore($o_id, $u_id, $u_nick, $row['u_id'], $row['u_nick']);
					}
				}
			}
			
			// Prüfung sollte nach dem Einfügen sein, denn sonst können 17 Benutzer drauf sein
			// Prüfung, ob bereits mehr als 16 user ignoriert werden
			// Alle ignorierten user > 16 werden gelöscht
			// Wenn mehr als 16 gebraucht werden, grössere Änderung in Tabelle online Feld o_ignore
			// Denn da passt nur ein Array mit 255 chars rein.
			$query = pdoQuery("SELECT `u_nick`, `u_id` FROM `user`, `iignore` WHERE `i_user_aktiv` = :i_user_aktiv AND `u_id` = `i_user_passiv` ORDER BY `i_id`", [':i_user_aktiv'=>$u_id]);
			
			$resultCount = $query->rowCount() - 16;
			if ($resultCount > 0) {
				$result = $query->fetchAll();
				foreach($result as $zaehler => $row) {
					ignore($o_id, $u_id, $u_nick, $row['u_id'], $row['u_nick']);
				}
			}
			
			if ($u_level == "G") {
				system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg55']);
			} else {
				// Liste der Benutzer ausgeben, die von mir ignoriert werden
				$query = pdoQuery("SELECT `i_id`, `u_id` FROM `iignore` LEFT JOIN `user` ON `i_user_passiv` = `u_id` WHERE `i_user_aktiv` = :i_user_aktiv ORDER BY `u_nick`, `i_id`", [':i_user_aktiv'=>$u_id]);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$i = 0;
					$text = str_replace("%u_nick%", $u_nick, $lang['chat_msg10']);
					$result = $query->fetchAll();
					foreach($result as $zaehler => $row) {
						if ($row['u_id'] && $row['u_id'] != "NULL") {
							if ($i > 0) {
								$text = $text . ", ";
							}
							$text = $text . zeige_userdetails($row['u_id']);
						} else {
							pdoQuery("DELETE FROM `iignore` WHERE `i_id` = :i_id", [':i_id'=>$row['i_id']]);
						}
						$i++;
					}
					system_msg("", 0, $u_id, $system_farbe, $text . "");
				} else {
					system_msg("", 0, $u_id, $system_farbe, str_replace("%u_nick%", $u_nick, $lang['chat_msg9']));
				}
				
				// Liste der Benutzer ausgeben, die mich ignorieren
				$query = pdoQuery("SELECT `i_id`, `u_id` FROM `iignore` LEFT JOIN `user` ON `i_user_aktiv` = `u_id` WHERE `i_user_passiv` = :i_user_passiv ORDER BY `u_nick`, `i_id`", [':i_user_passiv'=>$u_id]);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$i = 0;
					$text = str_replace("%u_nick%", $u_nick, $lang['chat_msg82']);
					$result = $query->fetchAll();
					foreach($result as $zaehler => $row) {
						if ($row['u_id'] && $row['u_id'] != "NULL") {
							if ($i > 0) {
								$text = $text . ", ";
							}
							$text = $text . zeige_userdetails($row['u_id']);
						} else {
							pdoQuery("DELETE FROM `iignore` WHERE `i_id` = :i_id", [':i_id'=>$row['i_id']]);
						}
						$i++;
					}
					system_msg("", 0, $u_id, $system_farbe, $text . "");
				}
			}
			break;
		
		case "/channel":
		case "/go":
		case "/raum":
		case "/rooms":
		case "/j":
		case "/join":
			if ($chatzeile[1]) {
				// Raumname erzeugen
				$chatzeile[1] = preg_replace("/[ \\'\"]/", "", $chatzeile[1]);
				$f['r_name'] = htmlspecialchars($chatzeile[1]);
				
				if (!isset($chatzeile[2])) {
					$chatzeile[2] = "";
				}
				
				// Raum wechseln
				$query = pdoQuery("SELECT r_id, (LENGTH(`r_name`)-length(:r_name1)) AS `laenge` FROM `raum` WHERE `r_name` LIKE :r_name2 ORDER BY `laenge`", [':r_name1'=>$chatzeile[1], ':r_name2'=>"$chatzeile[1]%"]);
				
				$resultCount = $query->rowCount();
				if ($resultCount != 0) {
					$result = $query->fetch();
					$r_id_neu = $result['r_id'];
					if ($r_id_neu != $r_id) {
						$r_id = raum_gehe($o_id, $u_id, $u_nick, $r_id, $r_id_neu);
						raum_user($r_id, $u_id);
					}
				} else if ($u_level != "G" && strlen($f['r_name']) <= $raum_max && strlen($f['r_name']) > 3) {
					// Neuen Raum anlegen, ausser Benutzer ist Gast
					$f['r_topic'] = htmlspecialchars($u_nick . $lang['chat_msg56']);
					$f['r_eintritt'] = "";
					$f['r_austritt'] = "";
					$f['r_status1'] = "O";
					$f['r_status2'] = "T";
					$f['r_besitzer'] = $u_id;
					$raumanlegen = true;
					
					// ab hier neu: wir prüfen ob community an ist, der Benutzer kein Admin und $raumanlegenpunkte gesetzt
					
					if (!$admin && $raumanlegenpunkte) {
						$query = pdoQuery("SELECT `u_punkte_gesamt` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
						
						$resultCount = $query->rowCount();
						if ($resultCount == 1) {
							$result = $query->fetch();
							$u_punkte_gesamt = $result['u_punkte_gesamt'];
						}
						
						if ($u_punkte_gesamt < $raumanlegenpunkte) {
							// Fehlermeldung und Raum nicht anlegen
							system_msg("", 0, $u_id, $system_farbe, str_replace("%punkte%", $raumanlegenpunkte, $lang['chat_msg108']));
							$raumanlegen = false;
						}
					}
					
					if ($raumanlegen == true) {
						global $pdo;
						// Hier wird der Raum angelegt
						pdoQuery("INSERT INTO `blacklist` (`r_name`, `r_eintritt`, `r_austritt`, `r_status1`, `r_besitzer`, `r_topic`, `r_status2`) VALUES (:r_name, :r_eintritt, :r_austritt, :r_status1, :r_besitzer, :r_topic, :r_status2)",
						[
						':r_name'=>$f['r_name'],
						':r_eintritt'=>$f['r_eintritt'],
						':r_austritt'=>$f['r_austritt'],
						':r_status1'=>$f['r_status1'],
						':r_besitzer'=>$f['f_text'],
						':r_topic'=>$f['f_text'],
						':r_status2'=>$f['r_status2']
						]);
						
						$r_id_neu = $pdo->lastInsertId();
						
						if ($r_id_neu != $r_id) {
							$r_id = raum_gehe($o_id, $u_id, $u_nick, $r_id, $r_id_neu);
							raum_user($r_id, $u_id);
						}
					}
				} else {
					// Fehlermeldung
					system_msg("", 0, $u_id, $system_farbe, str_replace("%chatzeile%", $chatzeile[1], $lang['chat_msg11']));
				}
			} else {
				// Liste alle Räume auf
				if ($admin) {
					$query = pdoQuery("SELECT `r_name` FROM `raum` ORDER BY `r_name`", []);
				} else {
					$query = pdoQuery("SELECT `r_name` FROM `raum` WHERE `r_status1` = 'O' ORDER BY `r_name`", []);
				}
				$resultCount = $query->rowCount();
				$result = $query->fetch();
				
				$text = $lang['chat_msg12'] . " ";
				$i = 0;
				foreach($result as $zaehler => $thema) {
					$text = $text . $thema['r_name'];
					$i++;
					if ($i < $resultCount) {
						$text = $text . ", ";
					}
				}
				system_msg("", 0, $u_id, $system_farbe, $text);
			}
			break;
		
		case "/wer":
		case "/who":
		case "/w":
		case "/user":
		case "/list":
		// Benutzer listen 
			if ($chatzeile[1] == "*") {
				
				// * zeigt alle Räume
				$chatzeile[0] = "/people";
				
			} else if ($chatzeile[1] != "") {
				if (!$admin) {
					$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE `r_name` LIKE :r_name AND (`r_status1` = 'O' OR `r_status1` = 'm' OR `r_id` = :r_id)", [':r_name'=>"$chatzeile[1]%", ':r_id'=>$o_raum]);
				} else {
					$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE `r_name` LIKE :r_name", [':r_name'=>"$chatzeile[1]%"]);
				}
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$text = $lang['chat_msg12'] . "<br>";
					$row = $query->fetch();
					raum_user($row['r_id'], $u_id);
				} else {
					// Raum nicht gefunden -> Fehlermeldung
					$txt = str_replace("%raumname%", $chatzeile[1], $lang['chat_msg53']);
					system_msg("", $u_id, $u_id, $system_farbe, $txt);
				}
				break;
			} else {
				// Der aktuelle Raum wird gezeigt
				raum_user($r_id, $u_id);
				break;
			}
		
		case "/people":
			// Liste alle Räume mit Benutzern auf
			if (!$admin) {
				$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE (`r_status1` = 'O' OR `r_status1` = 'm' OR `r_id` = :r_id) ORDER BY `r_name`", [':r_id'=>$o_raum]);
			} else {
				$query = pdoQuery("SELECT `r_id` FROM `raum` ORDER BY `r_name`", []);
			}
			
			$resultCount = $query->rowCount();
			if ($resultCount > 0) {
				$result = $query->fetchAll();
				foreach($result as $zaehler => $row) {
					raum_user($row['r_id'], $u_id, false);
				}
			}
			break;
		
		case "/txt":
		case "/me":
		// Spruch ausgeben 
			if (!($o_knebel > 0)) {
				if ($u_level == "M" || !$ist_moderiert) {
					hidden_msg($u_nick, $u_id, $u_farbe, $r_id, $u_nick . " " . html_parse($privat, htmlspecialchars( $chatzeile[1] . " " . $chatzeile[2] . " " . $chatzeile[3]), 1)[0] );
				} else {
					system_msg("", $u_id, $u_id, $system_farbe, $lang['moderiert1']);
				}
			} else {
				// user ist geknebelt...
				$zeit = gmdate("H:i:s", $o_knebel);
				$txt = str_replace("%zeit%", $zeit, $lang['knebel6']);
				system_msg("", $u_id, $u_id, $system_farbe, $txt);
			}
			break;
		
		case "/away":
		case "/weg":
		case "/afk":
		// Away-Text setzen oder löschen
		// Smilies parsen
			$privat = FALSE;
			
			if ($o_knebel > 0) {
				// Benutzer ist noch geknebelt
				$zeit = gmdate("H:i:s", $o_knebel);
				$txt = str_replace("%zeit%", $zeit, $lang['knebel6']);
				system_msg("", 0, $u_id, $system_farbe, $txt);
			} else {
				$away = substr(trim($chatzeile[1] . " " . $chatzeile[2] . " " . $chatzeile[3]), 0, 80);
				$away = html_parse($privat, htmlspecialchars($away))[0];
				if ($away == "") {
					$text = "$u_nick $lang[away2]";
					$f['u_away'] = "";
				} else {
					$text = "$u_nick $lang[away1] $away";
					$f['u_away'] = $away;

				}
				pdoQuery("UPDATE `user` SET `u_away` = :u_away WHERE `u_id` = :u_id",
					[
						':u_id'=>$u_id,
						':u_away'=>$f['u_away']
					]);
				aktualisiere_inhalt_online($u_id);
				
				// Bei Moderation private Nachricht, sonst Nachricht an alle
				if (!$ist_moderiert || $u_level == "M") {
					global_msg($u_id, $r_id, $text);
				} else {
					system_msg("", $u_id, $u_id, $system_farbe, $text);
				}
			}
			break;
		
		case "/schubs":
		// Nur für Admins und Tempadmins, schubst Benutzer in einen andere Raum
			if ($admin || $u_level == "A") {
				$user = nick_ergaenze($chatzeile[1], "raum", 1);
				if ($user['u_nick'] == "") {
					$user = nick_ergaenze($chatzeile[1], "online", 1);
				}
				if ($user['u_nick'] != "") {
					$raum = $chatzeile[2];
					if ($raum == "") {
						$query = pdoQuery("SELECT `r_id`, `r_name` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$o_raum]);
					} else {
						$query = pdoQuery("SELECT `r_id`, `r_name` FROM `raum` WHERE `r_name` LIKE :r_name", [':r_name'=>"$raum%"]);
					}
					$resultCount = $query->rowCount();
					if ($resultCount > 0) {
						$result = $query->fetch();
						$raumid = $result['r_id'];
						$raum = $result['r_name'];
					}
					
					$query = pdoQuery("SELECT `o_raum`, `o_id`, `r_name` FROM `online`, `raum` WHERE `o_user` = :o_user AND `r_id` = `o_raum`", [':o_user'=>$user['u_id']]);
					
					$resultCount = $query->rowCount();
					if ($resultCount > 0) {
						$result = $query->fetch();
						$raumalt = $result['r_name'];
						$uo_id = $result['o_id'];
						$uo_raum = $result['o_raum'];
					}
					// $text="Schubbern...$user[u_nick]/$user[u_id] $raumalt/$uo_raum -&gt; $raum/$raumid";
					$text = "<b>$chat:</b> $user[u_nick]: $raumalt -&gt; $raum";
					system_msg("", 0, $u_id, $system_farbe, $text);
					global_msg($u_id, $o_raum, "'$u_nick' $lang[sperre2] '$user[u_nick]' $lang[sperre8] $raum");
					raum_gehe($uo_id, $user['u_id'], $user['u_nick'], $uo_raum, $raumid);
				}
			}
			break;
		
		case "/plop":
		case "/kick":
		// Besitzer des aktuellen Raums ermitteln
			$query = pdoQuery("SELECT `r_besitzer` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$r_id]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				$result = $query->fetch();
				$r_besitzer = $result['r_besitzer'];
			}
			
			// Nur für Admins und Tempadmins, wirft Benutzer aus dem aktuellen Raum oder hebt Sperre auf
			if (strlen($chatzeile[1]) > 0) {
				// Benutzer kicken....
				
				// Benutzer aus Raum werfen oder Sperre freigeben, falls $u_id ist Admin oder Besitzer
				if ($r_besitzer == $u_id || $admin || $u_level == "A") {
					// Ist Benutzer im aktuellen Raum eingeloggt?
					$query = pdoQuery("SELECT `o_user`, `o_name`, `o_level`, (LENGTH(`o_name`)-length(:o_name1)) AS `laenge` "
						. "FROM `online` WHERE `o_name` LIKE :o_name2 ORDER BY `laenge`", [':o_name1'=>$chatzeile[1], ':o_name2'=>"%$chatzeile[1]%"]);
					
					$resultCount = $query->rowCount();
					// Benutzer bestimmen
					if ($resultCount != 0) {
						$result = $query->fetch();
						$s_user = $result['o_user'];
						$s_user_name = $result['o_name'];
						$s_user_level = $result['o_level'];
						
						// Auf admin prüfen
						if ($s_user_level == "S" || $s_user_level == "C" || $s_user_level == "M" || $s_user_level == "A") {
							system_msg("", 0, $u_id, $system_farbe, str_replace("%u_nick%", $u_nick, str_replace("%s_user_name%", $s_user_name, $lang['chat_msg13'])));
						} else {
							// Sperren oder freigeben
							sperre($r_id, $u_id, $u_nick, $s_user, $s_user_name, $admin);
						}
					} else {
						// Benutzer nicht gefunden oder nicht online? dann testen, ob gesperrt, damit man das gesperrt auch wieder raus bekommt.
						$query = pdoQuery("SELECT `u_nick`, `u_id` FROM `user` LEFT JOIN `sperre` ON `u_id` = `s_user` WHERE `u_nick` = :u_nick AND `s_raum` = :s_raum", [':u_nick'=>$chatzeile[1], ':s_raum'=>$r_id]);
						
						$resultCount = $query->rowCount();
						if ($resultCount == 1) {
							$row = $query->fetch();
							sperre($r_id, $u_id, $u_nick, $row['u_id'], $row['u_nick'], $admin);
						} else {
							system_msg("", 0, $u_id, $system_farbe, str_replace("%chatzeile%", $chatzeile[1], $lang['chat_msg14']));
						}
					}
				} else {
					system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg15']);
				}
				
			} elseif ($r_besitzer == $u_id || $admin || $u_level == "A") {
				// Liste der für diesen Raum gesperrten Benutzer ausgeben
				$query = pdoQuery("SELECT `u_nick` FROM `sperre`, `user` WHERE `s_user` = `u_id` AND `s_raum` = :s_raum ORDER BY `u_nick`", [':s_raum'=>$r_id]);
				
				$resultCount = $query->rowCount();
				if ($resultCount > 0) {
					$result = $query->fetch();
					$text = $lang['chat_msg16'] . " ";
					$i = 0;
					while ($rows > $i) {
						$text = $text . $result['u_nick'];
						$i++;
						if ($rows > $i) {
							$text = $text . ", ";
						}
					}
				} else {
					$text = $lang['chat_msg17'];
				}
				system_msg("", 0, $u_id, $system_farbe, $text);
			} else {
				// Keine Berechtigung zur Ausgabe der gesperrten user
				system_msg("", 0, $u_id, $system_farbe, "<b>He $u_nick!</b> " . str_replace("%chatzeile%", $chatzeile[0], $lang['chat_msg1']));
			}
			
			break;
		
		case "/color":
		case "/farbe":
			// Zeigt oder ändert Farbe
			if (strlen($chatzeile[1]) == 6) {
				if (preg_match("/[a-f0-9]{6}/i", $chatzeile[1])) {
					if (isset($f)) {
						unset($f);
					}
					$f['u_farbe'] = substr(htmlspecialchars($chatzeile[1]), 0, 6);
					pdoQuery("UPDATE `user` SET `u_farbe` = :u_farbe WHERE `u_id` = :u_id",
						[
							':u_id'=>$u_id,
							':u_farbe'=>$f['u_farbe']
						]);
					
					aktualisiere_inhalt_online($u_id);
					
					system_msg("", 0, $u_id, $system_farbe, str_replace("%u_farbe%", $f['u_farbe'], $lang['chat_msg21']));
				} else {
					system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg23']);
				}
			} else if (strlen($chatzeile[1]) == 0) {
				system_msg("", 0, $u_id, $system_farbe, str_replace("%u_farbe%", $u_farbe, $lang['chat_msg22']));
			} else {
				system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg23']);
			}
			break;
		
		case "/help":
		case "/hilfe":
		case "/?":
			// Hilfe listen 
			reset($hilfstext);
			$anzahl = count($hilfstext);
			$i = 0;
			while ($i < $anzahl) {
				$hhkey = key($hilfstext);
				system_msg("", 0, $u_id, "", "<SMALL>" . $hilfstext[$hhkey] . "</SMALL>");
				next($hilfstext);
				$i++;
			}
			break;
		
		case "/dice":
		case "/wuerfel":
		// Würfeln
			if ($u_level == "G") {
				system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg55']);
			} else {
				
				if ($o_knebel > 0) {
					// user ist geknebelt...
					$zeit = gmdate("H:i:s", $o_knebel);
					$txt = str_replace("%zeit%", $zeit, $lang['knebel6']);
					system_msg("", 0, $u_id, $system_farbe, $txt);
					break;
				}
				
				if (preg_match("!^\d+[wW]\d+$!", $chatzeile[1])) {
					$w = preg_split("![wW]!", $chatzeile[1]);
					if ($w[0] > 30) {
						$w[0] = 30;
					}
					if ($w[1] == 0) {
						$w[1] = 6;
					}
					$lang['chat_msg34'] = str_replace("%user%", $u_nick, $lang['chat_msg34']);
					if ($w[0] == 1) {
						$tmp = "einem ";
					} else {
						$tmp = htmlspecialchars("$w[0] ");
					}
					if (preg_match("!w!", $chatzeile[1])) {
						$tmp = $tmp . "kleinen ";
					} else {
						$tmp = $tmp . "großen ";
					}
					if ($w[0] == 1) {
						$tmp = $tmp . htmlspecialchars(" $w[1]-seitigen Würfel");
					} else {
						$tmp = $tmp . htmlspecialchars(" $w[1]-seitigen Würfeln");
					}
					$lang['chat_msg34'] = str_replace("%wuerfel%", $tmp, $lang['chat_msg34']);
					
					$summe = 0;
					for ($i = 0; $i < $w[0]; $i++) {
						$wurf = mt_rand(1, $w[1]);
						$summe = $summe + $wurf;
						$lang['chat_msg34'] = $lang['chat_msg34'] . " $wurf";
					}
					if ($w[0] > 1)
						$lang['chat_msg34'] = $lang['chat_msg34'] . ". Summe=$summe.";
					
					// Bei Moderation private Nachricht, sonst Nachricht an alle
					if (!$ist_moderiert || $u_level == "M") {
						hidden_msg($u_nick, $u_id, $u_farbe, $r_id, $lang['chat_msg34']);
					} else {
						system_msg("", $u_id, $u_id, $system_farbe, $lang['moderiert1']);
					}
				} else {
					system_msg("", $u_id, $u_id, $system_farbe, $lang['chat_msg35']);
				}
			}
			break;
		
		case "/zeige":
		case "/whois":
			if ($u_level == "G") {
				system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg55']);
			} else {
				if (strlen($chatzeile[1]) >= 3) {
					$chatzeile[1] = str_replace("$", "", $chatzeile[1]);
					$chatzeile[1] = str_replace("^", "", $chatzeile[1]);
					
					if (preg_match("/%/", $chatzeile[1])) {
						$sucheper = "LIKE";
					} else {
						$sucheper = "=";
					}
					
					if ((strcasecmp($chatzeile[1], "gast") == 0) && ($admin)) {
						// suche "/whois gast" zeigt alle Gäste und den Benutzer gast, wenn vorhanden
						$query = pdoQuery("SELECT *,date_format(u_login,'%d.%m.%y %H:%i') AS `login` FROM `user` WHERE (`u_nick` LIKE :u_nick) OR (`u_level` = 'G') ORDER BY `u_nick` LIMIT $max_user_liste", [':u_nick'=>$chatzeile[1]]);
					} else if (($admin) || ($u_level == "A")) {
						// suche für Admins und Tempadmins zeigt alle Benutzer
						$query = pdoQuery("SELECT *,date_format(u_login,'%d.%m.%y %H:%i') AS `login` FROM `user` WHERE `u_nick` $sucheper :u_nick ORDER BY `u_nick` LIMIT $max_user_liste", [':u_nick'=>$chatzeile[1]]);
					} else {
						// suche für Benutzer zeigt die gesperrten nicht an
						$query = pdoQuery("SELECT *,date_format(u_login,'%d.%m.%y %H:%i') AS `login` FROM `user` WHERE `u_nick` $sucheper :u_nick AND `u_level` IN ('A','C','G','M','S','U') ORDER BY `u_nick` LIMIT $max_user_liste", [':u_nick'=>$chatzeile[1]]);
					}
					
					$text = "<b>WHOIS $chatzeile[1]:</b><br>\n";
					$result = $query->fetchAll();
					foreach($result as $zaehler => $row) {
						$query2 = pdoQuery("SELECT `r_name`, `o_ip`, `o_browser`, `o_id`, `o_who`,"
							. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_login`) AS `online_zeit` FROM `online` LEFT JOIN `raum` ON `r_id` = `o_raum` WHERE `o_user` = :o_user", [':o_user'=>$row['u_id']]);
						
						$result2Count = $query2->rowCount();
						if ($result2Count > 0) {
							// Benutzer ist online
							$result2 = $query2->fetch();

							$text = $text . zeige_userdetails($row['u_id'], TRUE) . ", ";
							
							$text .= "<b>[" . $whotext[$result2['o_who']] . "]</b>";
							if ($result2['o_who'] == 0) {
								$text .= ", %raum%&nbsp;" . $result2['r_name'];
							}
							
							if ($row['u_away']) {
								$awaytext = htmlspecialchars($row['u_away']);
								$awaytext = str_replace('&lt;b&gt;', '<b>', $awaytext);
								$awaytext = str_replace('&lt;/b&gt;', '</b>', $awaytext);
								$awaytext = str_replace('&lt;i&gt;', '<I>', $awaytext);
								$awaytext = str_replace('&lt;/i&gt;', '</I>', $awaytext);
								$awaytext = str_replace('&amp;lt;', '<', $awaytext);
								$awaytext = str_replace('&amp;gt;', '>', $awaytext);
								$awaytext = str_replace('&amp;quot;', '"', $awaytext);
								$text = $text . ", (" . $awaytext . ")";
							}
							
							if ($admin) {
								$host_name = @gethostbyaddr($result2['o_ip']);
								$text = $text . htmlspecialchars(", ($result2[o_ip]($host_name), $result2[o_browser]) ");
							}
							$text = $text . "</b><br>\n";
						} else {
							// Benutzer ist nicht online
							$text = $text . zeige_userdetails($row['u_id'], TRUE) . ", ";
							
							if ($row['u_away']) {
								$awaytext = htmlspecialchars($row['u_away']);
								$awaytext = str_replace('&lt;b&gt;', '<b>', $awaytext);
								$awaytext = str_replace('&lt;/b&gt;', '</b>', $awaytext);
								$awaytext = str_replace('&lt;i&gt;', '<i>', $awaytext);
								$awaytext = str_replace('&lt;/i&gt;', '</i>', $awaytext);
								$awaytext = str_replace('&amp;lt;', '<', $awaytext);
								$awaytext = str_replace('&amp;gt;', '>', $awaytext);
								$text = $text . ", (" . $awaytext . ")";
							}
							$text = $text . "<br>\n";
						}
					}
					$text = $text . "\n";
					$text = str_replace("%email%", $lang['chat_msg64'], $text);
					$text = str_replace("%adminemail%", $lang['chat_msg65'], $text);
					$text = str_replace("%online%", $lang['chat_msg66'], $text);
					$text = str_replace("%raum%", $lang['chat_msg67'], $text);
					$text = str_replace("%login%", $lang['chat_msg68'], $text);
					
					system_msg("", 0, $u_id, $system_farbe, $text);
					
				} else {
					system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg33']);
				}
			}
			break;
		
		case "/msg": // Sollte hier in den Aliasen etwas dazu kommen, muss der Autoknebel angepasst werden, damit Admins die privaten
		case "/talk": // Nachrichten nicht lesen können, wenn der Autoknebel zuschlägt
		case "/tell":
		case "/t":
		case "/msgpriv": // Extra behandlung für Private Nachrichten im Benutzerfenster, für den Fall, dass der Benutzer sich ausloggt, keine Nickergänzung
			// Private Nachricht 
			if (!($o_knebel > 0) && $u_level != "G" && !$ist_eingang) {
				// Smilies und Text parsen
				$privat = TRUE;
				$text = html_parse($privat, htmlspecialchars($chatzeile[2] . " " . $chatzeile[3]))[0];
				
				// Empfänger im Chat suchen
				// /talk muss z.B. mit "/talk kleiner" auch dann an kleiner gehen 
				// wenn kleiner in anderem Raum ist und im eigenen Raum ein kleinerpfuscher anwesend ist.
				if (!isset($nick) || $nick['u_nick'] == "") {
					if ($chatzeile[0] == "/msgpriv") {
						// Keine Nickergänzung bei diesem Nachrichtentyp
						// Prüfen ob Benutzer noch online
						$query = pdoQuery("SELECT `o_id`, `o_name`, `o_user`, `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4` FROM `online` WHERE `o_name` = :o_name", [':o_name'=>$chatzeile[1]]);
						
						$resultCount = $query->rowCount();
						if ($resultCount == 1) {
							$result = $query->fetch();
							$nick = unserialize(
								$result['o_userdata']
								. $result['o_userdata2']
								. $result['o_userdata3']
								. $result['o_userdata4']);
							$nick['u_nick'] = $result['o_name'];
							$nick['u_id'] = $result['o_user'];
						} else {
							$nick['u_nick'] = "";
							system_msg("", 0, $u_id, $u_farbe, str_replace("%chatzeile%", $chatzeile[1], $lang['chat_msg25']));
						}
					} else {
						$nick = nick_ergaenze($chatzeile[1], "online", 0);
					}
				}
				
				// Falls Empfänger gefunden, Nachricht versenden
				if (isset($nick) && $nick['u_nick'] != "") {
					// Nick gefunden und eindeutig
					if ($text != " ") {
						// Wir prüfen, ob der Benutzer ignoriert wird, wenn ja => Fehlermeldung
						$query = pdoQuery("SELECT `i_user_aktiv`, `i_user_passiv` FROM `iignore` WHERE (`i_user_aktiv` = :i_user_aktiv1 AND `i_user_passiv` = :i_user_passiv1) OR "
							. " (`i_user_passiv` = :i_user_passiv2 AND `i_user_aktiv` = :i_user_aktiv2)",
							[
								':i_user_aktiv1'=>$u_id,
								':i_user_passiv1'=>$nick['u_id'],
								':i_user_passiv2'=>$u_id,
								':i_user_aktiv2'=>$nick['u_id']
							]);
						
						$resultCount = $query->rowCount();
						if ($resultCount == 0) {
							priv_msg($u_nick, $u_id, $nick['u_id'], $u_farbe, $text);
							system_msg("", $u_id, $u_id, $system_farbe, "<b>$u_nick $lang[chat_msg24] $nick[u_nick]:</b> $text");
						} else {
							system_msg("", $u_id, $u_id, $system_farbe, str_replace('%nick%', $nick['u_nick'], $lang['chat_msg109']));
						}
						if ($nick['u_away'] != "") {
							system_msg("", 0, $u_id, $system_farbe, "<b>$chat:</b> $nick[u_nick] $lang[away1] $nick[u_away]");
						}
						pdoQuery("UPDATE `chat` SET `c_gelesen` = 1 WHERE `c_gelesen` = 0 AND `c_typ` = 'P' AND `c_von_user_id` = :c_von_user_id", [':c_von_user_id'=>$nick['u_id']]);
						
						reset_system("userliste");
					}
				} else {
					// Nachricht konnte nicht verschickt werden, als Kopie ausgeben
					system_msg("", 0, $u_id, $system_farbe, str_replace("%nachricht%", $text, $lang['chat_msg77']));
				}
			} elseif ($u_level == "G") {
				system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg55']);
			} elseif ($o_knebel > 0) {
				// user ist geknebelt...
				$zeit = gmdate("H:i:s", $o_knebel);
				$txt = str_replace("%zeit%", $zeit, $lang['knebel6']);
				system_msg("", $u_id, $u_id, $system_farbe, $txt);
			} elseif ($ist_eingang) {
				system_msg("", 0, $u_id, $system_farbe, $raum_einstellungen['r_topic']);
			} elseif (!$admin) {
				system_msg("", 0, $u_id, $system_farbe, str_replace("%chatzeile%", $chatzeile[0], $lang['chat_msg1']));
			}
			break;
		
		case "/tf":
		case "/msgf":
			$query = pdoQuery("SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_userid`= :f_userid AND `f_status` = 'bestaetigt' "
				. "UNION SELECT `f_id`, `f_text`, `f_userid`, `f_freundid`, `f_zeit` FROM `freunde` WHERE `f_freundid` = :f_freundid AND `f_status` = 'bestaetigt' ORDER BY `f_zeit` DESC", [':f_userid'=>$u_id, ':f_freundid'=>$u_id]);
			
			$fid = array();
			$nicks = "";
			$result = $query->fetchAll();
			$rueckgabe = false;
			foreach($result as $zaehler => $a) {
				$query2 = pdoQuery("SELECT `o_user`, `o_name` FROM `online` WHERE (`o_user` = :o_user1 OR `o_user` = :o_user2) AND `o_user` != :o_user3", [':o_user1'=>$a['f_userid'], ':o_user2'=>$a['f_freundid'], ':o_user3'=>$u_id]);
				
				$result2Count = $query2->rowCount();
				if ($result2Count == 1) {
					$rueckgabe = true;
					if ($a['f_userid'] != $u_id) {
						$fid[] = $a['f_userid'];
					}
					if ($a['f_freundid'] != $u_id) {
						$fid[] = $a['f_freundid'];
					}
				}
			}
			
			if($rueckgabe) {
				$privat = TRUE;
				$text = html_parse($privat, htmlspecialchars( $chatzeile[1] . " " . $chatzeile[2] . " " . $chatzeile[3]))[0];
				
				for ($i = 0; $i < count($fid); $i++) {
					$nick = zeige_userdetails($fid[$i], FALSE, true);
					priv_msg($u_nick, $u_id, $fid[$i], $u_farbe, $text);
					system_msg("", $u_id, $u_id, $system_farbe, "<b>$u_nick $lang[chat_msg24] $nick:</b> $text");
				}
			}
				
			break;
		
		case "/analle":
		case "/toall":
		// Private nachricht an alle Benutzer
			if ($admin || $u_level == "A") {
				// Smilies und Text parsen
				$privat = TRUE;
				$text = html_parse($privat, $lang['chat_msg100'] . htmlspecialchars( $chatzeile[1] . " " . $chatzeile[2] . " " . $chatzeile[3]))[0];
				
				if ($text) {
					// Schleife über alle Benutzer, die online sind
					$query = pdoQuery("SELECT `o_user` FROM `online`", []);
					
					$resultCount = $query->rowCount();
					if ($resultCount > 0) {
						$result = $query->fetchAll();
						foreach($result as $zaehler => $row) {
							if ($row['o_user'] != $u_id) {
								priv_msg($u_nick, $u_id, $row['o_user'], $u_farbe, $text);
							}
						}
						system_msg("", $u_id, $u_id, $system_farbe, "<b>$u_nick $lang[chat_msg78]:</b> $text");
					} else {
						// Kein Benutzer online
						system_msg("", $u_id, $u_id, $system_farbe, $lang['chat_msg79']);
					}
				}
			} else {
				// user ist kein Admin
				system_msg("", $u_id, $u_id, $system_farbe, $lang['chat_msg1']);
			}
			break;
		
		case "/lob":
		// Nur für Admins, lobt Benutzer mit Punkten
			if ($admin) {
				$user = nick_ergaenze($chatzeile[1], "raum", 1);
				if ($user['u_nick'] == "") {
					$user = nick_ergaenze($chatzeile[1], "online", 0);
					$im_raum = FALSE;
				} else {
					$im_raum = TRUE;
				}
				
				// Nur Superuser dürfen Gästen Punkte geben
				if ($user['u_level'] == 'G' && $u_level != 'S') {
					system_msg("", 0, $u_id, $system_farbe, $lang['punkte22']);
					$user['u_nick'] = "";
				}
				
				$anzahl = (int) $chatzeile[2];
				if ((string) $anzahl != (string) $chatzeile[2])
					$anzahl = 0;
				
				// Mehr als ein Punkt? Ansonsten Fehlermeldung
				if ($anzahl < 1) {
					system_msg("", 0, $u_id, $system_farbe, $lang['punkte7']);
					$user['u_nick'] = "";
				}
				
				// Kein SU und mehr als 1000 Punkte? Ansonsten Fehlermeldung
				if ($anzahl > 1000 && $u_level != 'S') {
					system_msg("", 0, $u_id, $system_farbe, $lang['punkte11']);
					$user['u_nick'] = "";
				}
				
				// selber Punkte geben ist verboten
				if ($u_nick == $user['u_nick']) {
					system_msg("", 0, $u_id, $system_farbe, $lang['punkte10']);
					$user['u_nick'] = "";
				}
				
				if ($user['u_nick'] != "") {
					
					if ($im_raum) {
						// eine öffentliche Nachricht an alle schreiben
						punkte($anzahl, $user['o_id'], $user['u_id'], "", TRUE);
						$txt = str_replace("%user1%", $u_nick, $lang['punkte5']);
						$txt = str_replace("%user2%", $user['u_nick'], $txt);
						$txt = str_replace("%punkte%", $anzahl, $txt);
						global_msg($u_id, $o_raum, str_replace("%user%", $u_nick, $txt));
					} else {
						// Zwei private Nachrichten (admin/user)
						punkte($anzahl, $user['o_id'], $user['u_id'], str_replace("%user%", $u_nick, $lang['punkte3']), TRUE);
						$txt = str_replace("%user2%", $user['u_nick'], $lang['punkte8']);
						$txt = str_replace("%user1%", $u_nick, $txt);
						$txt = str_replace("%punkte%", $anzahl, $txt);
						global_msg($u_id, $o_raum, str_replace("%user%", $u_nick, $txt));
					}
				}
			} else {
				system_msg("", 0, $u_id, $system_farbe, "<b>He $u_nick!</b> " . str_replace("%chatzeile%", $chatzeile[0], $lang['chat_msg1']));
			}
			break;
		
		case "/tadel":
		case "/bestraf":
		case "/peitsch":
		// Nur für Admins, zieht Benutzer Punkte ab
			if ($admin) {
				$user = nick_ergaenze($chatzeile[1], "raum", 1);
				if ($user['u_nick'] == "") {
					$user = nick_ergaenze($chatzeile[1], "online", 0);
					$im_raum = FALSE;
				} else {
					$im_raum = TRUE;
				}
				
				$anzahl = (int) $chatzeile[2];
				if ((string) $anzahl != (string) $chatzeile[2])
					$anzahl = 0;
				
				// Mehr als ein Punkt? Ansonsten Fehlermeldung
				if ($anzahl < 1) {
					system_msg("", 0, $u_id, $system_farbe, $lang['punkte7']);
					$user['u_nick'] = "";
				}
				
				// Lustiger Text, wenn der Admin sich selbst etwas abzieht
				if ($lustigefeatures && $u_nick == $user['u_nick']) {
					if ($anzahl == 1) {
						$txt = $lang['punkte12'];
					} elseif ($anzahl == 42) {
						$txt = $lang['punkte13'];
					} elseif ($anzahl < 100) {
						$txt = $lang['punkte14'];
					} elseif ($anzahl < 200) {
						$txt = $lang['punkte15'];
					} elseif ($anzahl < 500) {
						$txt = $lang['punkte16'];
					} elseif ($anzahl < 1000) {
						$txt = $lang['punkte17'];
					} elseif ($anzahl < 5000) {
						$txt = $lang['punkte18'];
					} elseif ($anzahl < 10000) {
						$txt = $lang['punkte19'];
					} else {
						$txt = $lang['punkte20'];
					}
					
					// eine öffentliche Nachricht an alle schreiben
					punkte($anzahl * (-1), $user['o_id'], $user['u_id'], "", TRUE);
					
					$txt = str_replace("%user%", $u_nick, $txt);
					$txt = str_replace("%punkte%", $anzahl, $txt);
					global_msg($u_id, $o_raum, str_replace("%user%", $u_nick, $txt));
				} else if ($user['u_nick'] != "") {
					if ($im_raum) {
						// eine öffentliche Nachricht an alle schreiben
						punkte($anzahl * (-1), $user['o_id'], $user['u_id'], "", TRUE);
						$txt = str_replace("%user1%", $u_nick, $lang['punkte6']);
						$txt = str_replace("%user2%", $user['u_nick'], $txt);
						$txt = str_replace("%punkte%", $anzahl, $txt);
						global_msg($u_id, $o_raum, str_replace("%user%", $u_nick, $txt));
					} else {
						// Zwei private Nachrichten (admin/user)
						punkte($anzahl * (-1), $user['o_id'], $user['u_id'], str_replace("%user%", $u_nick, $lang['punkte4']), TRUE);
						$txt = str_replace("%user2%", $user['u_nick'], $lang['punkte9']);
						$txt = str_replace("%user1%", $u_nick, $txt);
						$txt = str_replace("%punkte%", $anzahl, $txt);
						global_msg($u_id, $o_raum, str_replace("%user%", $u_nick, $txt));
					}
				}
			} else {
				system_msg("", 0, $u_id, $system_farbe, "<b>He $u_nick!</b> " . str_replace("%chatzeile%", $chatzeile[0], $lang['chat_msg1']));
			}
			break;
		
		case "/time":
			// Gibt die Systemuhrzeit aus
			$date = date('Y-m-d H:i:s');
			$tempzeit = str_replace("%datum%", formatDate($date,"d.m.Y H:i","Y-m-d H:i:s"), $lang['chat_msg114']);
			system_msg("", 0, $u_id, $system_farbe, $tempzeit);
			unset($tempzeit);
			break;
			
		case "/quiddice":
		case "/dicecheck":
			if (!$o_dicecheck) {
				if (preg_match("!^\d+[wW]\d+$!", $chatzeile[1])) {
					$o_dicecheck = $chatzeile[1];
					pdoQuery("UPDATE `online` SET `o_dicecheck` = :o_dicecheck WHERE `o_user` = :o_user", [':o_dicecheck'=>$o_dicecheck, ':o_user'=>$u_id]);
					
					if (!isset($lang['chat_dicecheck_msg0'])) {
						$lang['chat_dicecheck_msg0'] = "<b>$chat:</b> <b>%dicecheck%</b> wurde aktiviert. Bitte klicke auf RESET.";
					}
					system_msg("", 0, $u_id, $system_farbe, str_replace("%dicecheck%", $chatzeile[0] . " " . $chatzeile[1], $lang['chat_dicecheck_msg0']));
				} else {
					if (!isset($lang['chat_dicecheck_msg1'])) {
						$lang['chat_dicecheck_msg1'] = "<b>$chat:</b> Beispiel: <b>%dicecheck% 3W6</b> prüft alle Würfelwürfe, ob sie mit /dice 3W6 geworfen wurden.";
					}
					system_msg("", 0, $u_id, $system_farbe, str_replace("%dicecheck%", $chatzeile[0], $lang['chat_dicecheck_msg1']));
				}
			} else {
				$o_dicecheck = "";
				pdoQuery("UPDATE `online` SET `o_dicecheck` = '' WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
				
				if (!isset($lang['chat_dicecheck_msg2'])) {
					$lang['chat_dicecheck_msg2'] = "<b>$chat:</b> <b>%dicecheck%</b> wurde deaktiviert. Bitte klicke auf RESET.";
				}
				system_msg("", 0, $u_id, $system_farbe, str_replace("%dicecheck%", $chatzeile[0], $lang['chat_dicecheck_msg2']));
			}
			break;
		
		default:
			
			if (!($o_knebel > 0)) {
				// Gibt Spruch aus, falls Eingabe mit = beginnt
				if (preg_match("/^=/", $chatzeile[0])) {
					// Spruch suchen und ausgeben
					if ($u_level == "G") {
						system_msg("", 0, $u_id, $system_farbe, $lang['chat_msg55']);
					} else {
						// Art des Spruchs
						// 0: Ohne Argumente
						// 1: Benutzername wurde angegeben
						// 2: Benutzername und Zusatztext wurden übergeben
						
						// Wenn in $chatzeile[1] oder anderen auser 0 ein " vorkommt,
						// dann leerzeichen durch + ersetzen und neu splitten
						if (!isset($chatzeile[1])) {
							$chatzeile[1] = "";
						}
						if (!isset($chatzeile[2])) {
							$chatzeile[2] = "";
						}
						if (!isset($chatzeile[3])) {
							$chatzeile[3] = "";
						}
						if (preg_match("/\"/", $chatzeile[1]) || preg_match("/\"/", $chatzeile[2]) || preg_match("/\"/", $chatzeile[3])) {
							$text = trim($text);
							
							// Nur bei gerader anzahl an " geht es
							if (substr_count($text, '"') == 2) {
								$temp = explode('"', $text, 3);
								if (trim($temp[2]) == '') {
									$temp[1] = "\"" . $temp[1] . "\"";
								}
								$text = $temp[0]
									. str_replace(' ', '+', trim($temp[1]))
									. " "
									. str_replace(' ', '+', trim($temp[2]));
								$chatzeile = preg_split("/ /", $text, 4);
							} else if (substr_count($text, '"') == 4) {
								$temp = explode('"', $text, 5);
								$text = $temp[0] . "\""
									. str_replace(' ', '+', $temp[1]) . "\" "
									. str_replace(' ', '+', $temp[3]);
								$chatzeile = preg_split("/ /", $text, 4);
							}
							if (!isset($chatzeile[3])) {
								$chatzeile[3] = "";
							}
						}
						
						// Befehl bestimmen und ungültige Zeichen filtern
						$spruchname = strtr(substr($chatzeile[0], 1), "*%$!?.,;:\\/", "");
						$username = $chatzeile[1];
						$zusatztext = $chatzeile[2] . " " . $chatzeile[3];
						
						if ($username) {
							$nick = nick_ergaenze($username, "raum", 1);
						}
						if (isset($nick) && $nick != "") {
							$username = $nick['u_nick'];
						}
						
						$spruchart = 0;
						if ($username) {
							$spruchart++;
						}
						if ($zusatztext != " ") {
							$spruchart++;
						}
						
						$username = str_replace("+", " ", $username);
						$username = trim(str_replace("\"", "", $username));
						$zusatztext = str_replace("+", " ", $zusatztext);
						$zusatztext = trim(str_replace("\"", "", $zusatztext));
						
						// Spruch suchen
						// Sprüche in Array lesen
						$sp_list = file("conf/$datei_spruchliste");
						$sp_such = "^" . preg_quote($spruchname, "/") . "\t" . $spruchart . "\t";
						foreach($sp_list as $zaehler => $spruch) {
							if(preg_match("/" . $sp_such . "/i", $spruch)) {
								$sp_text = $spruch;
								break;
							}
						}
						
						// Spruch gefunden?
						if(isset($sp_text)) {
							// Spruch parsen
							$spruchtmp = preg_split("/\t/", $sp_text, 3);
							$spruchtxt = $spruchtmp[2];
							
							$spruchtxt = str_replace("`2", $zusatztext, $spruchtxt);
							$spruchtxt = str_replace("`1", $username, $spruchtxt);
							$spruchtxt = str_replace("`0", $u_nick, $spruchtxt);
							
							// Spruch ausgeben
							$spruchtxt = str_replace("\n", "", $spruchtxt);
							
							if (!$ist_moderiert || $u_level == "M") {
								hidden_msg($u_nick, $u_id, $u_farbe, $r_id, trim( html_parse($privat, htmlspecialchars($spruchtxt . " "))[0] ) . "<!-- ($u_nick) -->");
							} else {
								system_msg("", $u_id, $u_id, $system_farbe, $lang['moderiert1']);
							}
						} else {
							// Fehler ausgeben
							$fehler = $lang['chat_spruch3'];
							if ($spruchart == 2) {
								$fehler = $lang['chat_spruch1'];
							}
							if ($spruchart == 1) {
								$fehler = $lang['chat_spruch2'];
							}
							
							$fehler = str_replace("%spruchname%", $spruchname, $fehler);
							$fehler = str_replace("%u_nick%", $u_nick, $fehler);
							system_msg("", 0, $u_id, $system_farbe, $fehler);
							
							// Hinweise ausgeben
							foreach($sp_list as $sp_text){
								if (preg_match( "/^" . preg_quote($spruchname, "/") . "\t/i", $sp_text)) {
									$spruchtmp = preg_split("/\t/", $sp_text, 3);
									$txt = "<small><b>$lang[chat_spruch4] <i>" . $spruchtmp[0] . " " . $spruchtmp[1] . "</i></b> &lt;" . $spruchtmp[2] . "&gt;</small>";
									system_msg("", 0, $u_id, $system_farbe, $txt);
								}
							}
						}
					}
				} else {
					// Gibt Fehler aus, falls Eingabe mit / beginnt
					if (preg_match("/^\//", $chatzeile[0])) {
						system_msg("", 0, $u_id, $system_farbe, str_replace("%chatzeile%", $chatzeile[0], $lang['chat_spruch5']));
					} else {
						// Normaler Text im Chat ausgeben
						// Text filtern und in DB schreiben
						
						// Benutzernamen ergänzen, gesucht wird nach aktiven usern im selben Raum
						$temp = substr($chatzeile[0], -1);
						
						// Ansprechen mit "Nick:" "@nick" oder "Nick," bzw "Nick."
						// Problem: "Nick," und "Nick." macht Probleme bei allen Wörten, die am Anfang mit , oder "..." 
						// getrennt geschrieben werden
						// z.b. "so, ich gehe jetzt" wird zu "[zu Sonnenschein] ich gehe jetzt" wenn Benutzer Sonnenschein im Raum ist
						// if ($temp==":" || $temp=="@" || $temp==",") {
						// Daher auskommentiert und nur "Nick:" "@nick" und testweise "Nick." akzeptiert
						// da obriges seltener für den . passiert, und der . öfters aus versehen statt dem : erwischt wird
						if ($temp == ":" || $temp == "@") {
							$nick = nick_ergaenze($chatzeile[0], "raum", 0);
							//$f['c_an_user'] = $nick['u_id'];
							if ($nick['u_nick'] != "") {
								// Falls Benutzer gefunden wurde Benutzernamen einfügen und filtern
								$f['c_text'] = "[" . $lang['chat_spruch6'] . "&nbsp;$nick[u_nick]] " . html_parse($privat, htmlspecialchars( $chatzeile[1] . " " . $chatzeile[2] . " " . $chatzeile[3]))[0];
								if ($nick['u_away'] != "") {
									system_msg("", 0, $u_id, $system_farbe, "<b>$chat:</b> $nick[u_nick] $lang[away1] $nick[u_away]");
								}
							} else {
								// keine Fehlermeldung, wird von nick_replace schon erzeugt...
								// Ansonsten Chatzeile gefiltert ausgeben
								$f['c_text'] = html_parse($privat, htmlspecialchars($text))[0];
							}
							$f['c_an_user'] = 0;
						} else if (isset($chatzeile[1]) && substr($chatzeile[1], 0, 1) == "@") {
							$nick = nick_ergaenze($chatzeile[1], "raum", 0);
							$f['c_an_user'] = 0;
							if ($nick['u_nick'] != "") {
								// Falls Benutzer gefunden wurde Benutzernamen einfügen und filtern
								$f['c_text'] = "[" . $lang['chat_spruch6'] . "&nbsp;$nick[u_nick]] " . html_parse($privat, htmlspecialchars( $chatzeile[0] . " " . $chatzeile[2] . " " . $chatzeile[3]))[0];
								if ($nick['u_away'] != "") {
									system_msg("", 0, $u_id, $system_farbe, "<b>$chat:</b> $nick[u_nick] $lang[away1] $nick[u_away]");
								}
							} else {
								// keine Fehlermeldung, wird von nick_replace schon erzeugt...
								// Ansonsten Chatzeile gefiltert ausgeben
								$f['c_text'] = html_parse($privat, htmlspecialchars($text))[0];
							}
						} else {
							$chat_text = html_parse($privat, htmlspecialchars($text));
							// Wenn Benutzername in html-parse vervollständigt wurde, die ID des angesprochenen Benutzers mit übergeben, um es in der Datenbank zu speichern
							if($chat_text[1] != "") {
								$f['c_an_user'] = $chat_text[1];
							} else {
								$f['c_an_user'] = 0;
							}
							
							// Chatzeile gefiltert ausgeben
							$f['c_text'] = $chat_text[0];
						}
						
						// Attribute ergänzen und Nachricht schreiben
						$f['c_an_user'] = 0;
						$f['c_von_user'] = $u_nick;
						$f['c_von_user_id'] = $u_id;
						$f['c_raum'] = $r_id;
						$f['c_typ'] = $typ;
						$f['c_farbe'] = $u_farbe;
						
						// Ist Raum moderiert? $ist_moderiert und $raum_einstellungen wurde in raum_ist_moderiert() gesetzt
						if (!$ist_eingang && !$ist_moderiert) {
							// raum ist nicht moderiert -> schreiben.
							schreibe_chat($f);
							
							// In Session merken, dass Text im Chat geschrieben wurde
							pdoQuery("UPDATE `online` SET `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
							
							// Punkte berechnen und Benutzer gutschreiben
							// Punkte=Anzahl der Wörter mit mindestens 4 Buchstaben
							// Punkte werden nur in permanenten, offen Räumen gutgeschrieben!
							if ($u_level != "G" && ($raum_einstellungen['r_status1'] == "O" || $raum_einstellungen['r_status1'] == "m") && $raum_einstellungen['r_status2'] == "P") {
								// Punkte gutschreiben, wenn im Raum mindestens $punkte_ab_user Benutzer online sind
								$query = pdoQuery("SELECT COUNT(`o_id`) AS `anzahl` FROM `online` WHERE `o_raum` = :o_raum", [':o_raum'=>$o_raum]);
								
								$result = $query->fetch();
								if ($punktefeatures && $result && $result['anzahl'] >= $punkte_ab_user) {
									// löscht die * und _ aus dem Eingabetext, damit es für z.b. *_*_ keine Punkte mehr gibt
									$punktetext = $f['c_text'];
									$punktetext = str_replace('<i>', '', $punktetext);
									$punktetext = str_replace('<b>', '', $punktetext);
									$punktetext = str_replace('</i>', '', $punktetext);
									$punktetext = str_replace('</b>', '', $punktetext);
									
									$anzahlPunkte = strlen(
										trim(
											preg_replace(
												'/(^[[:space:]]*|[[:space:]]+)[^[:space:]]+/', "x",
												preg_replace('/(^|[[:space:]]+)[^[:space:]]{1,3}/', " ", $punktetext)
												)));
									
									punkte($anzahlPunkte, $o_id);
									
									unset($punktetext);
								}
							}
						} else if ($ist_moderiert) {
							if ($u_level == "M") {
								// sonderfall: moderator -> hier doch schreiben	
								// vorher testen, ob es markierte fragen gibt:
								schreibe_moderation();
								schreibe_chat($f);
								
								// In Session merken, dass Text im Chat geschrieben wurde
								pdoQuery("UPDATE `online` SET `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
							} else {
								if ($f['c_text'] != "") {
									// raum ist moderiert -> normal nicht schreiben.
									schreibe_moderiert($f);
									
									// In Session merken, dass Text im Chat geschrieben wurde
									pdoQuery("UPDATE `online` SET `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
									
									system_msg("", 0, $u_id, $system_farbe, $lang['moderiert2']);
									system_msg("", 0, $u_id, $system_farbe, "&gt;&gt;&gt; " . $f['c_text']);
								}
							}
						} elseif ($ist_eingang && strlen(trim($f['c_text'])) > 0)  {
							system_msg("", 0, $u_id, $system_farbe, $raum_einstellungen['r_topic']);
						}
					}
				}
			} else {
				// user ist geknebelt...
				$zeit = gmdate("H:i:s", $o_knebel);
				$txt = str_replace("%zeit%", $zeit, $lang['knebel6']);
				system_msg("", $u_id, $u_id, $system_farbe, $txt);
			}
	}
}

function sperre($r_id, $u_id, $u_nick, $s_user, $s_user_name, $admin) {
	// Sperrt Benutzer s_user für Raum `r_id` oder hebt Sperre auf
	// Wirft Benutzer ggf aus dem Raum r_id
	
	global $lobby, $timeout, $lang, $u_level;
	
	// Id der Lobby als Voreinstellung ermitteln
	$query = pdoQuery("SELECT `r_id` FROM `raum` WHERE `r_name` LIKE :r_name", [':r_name'=>$lobby]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		$lobby_id = $result['r_id'];
	} else {
		system_msg("", 0, $u_id, $system_farbe, $lang['sperre1']);
	}
	
	if ($r_id != $lobby_id) {
		$query = pdoQuery("SELECT `s_id` FROM `sperre` WHERE `s_user` = :s_user AND `s_raum` = :s_raum", [':s_user'=>$s_user, ':s_raum'=>$r_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 0) {
			// Ist Benutzer s_user in diesem Raum? $o_id ermitteln
			$query2 = pdoQuery("SELECT `o_id` FROM `online` WHERE `o_raum` = :o_raum AND `o_user` = :o_user AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout", [':o_raum'=>$r_id, ':o_user'=>$s_user, ':timeout'=>$timeout]);
			
			$result2Count = $query2->rowCount();
			if ($result2Count > 0) {
				$result2 = $query2->fetch();
				// Sperre neu setzen
				$o_id = $result2['o_id'];
				$f['s_user'] = $s_user;
				$f['s_raum'] = $r_id;
				
				pdoQuery("INSERT INTO `sperre` (`s_user`, `s_raum`) VALUES (:s_user, :s_raum)",
					[
						':s_user'=>$f['s_user'],
						':s_raum'=>$f['s_raum']
					]);
				
				// Benutzer aus Raum werfen
				global_msg($u_id, $r_id, "'$u_nick' $lang[sperre2] '$s_user_name' $lang[sperre3]");
				raum_gehe($o_id, $s_user, $s_user_name, $r_id, $lobby_id);
				
			} else {
				// Benutzer ist nicht in diesem Raum... Meldung ausgeben.
				system_msg("", 0, $u_id, $system_farbe, str_replace("%s_user_name%", $s_user_name, $lang['sperre4']));
				// trotzdem in Sperre eintragen.
				$f['s_user'] = $s_user;
				$f['s_raum'] = $r_id;
				
				pdoQuery("INSERT INTO `sperre` (`s_user`, `s_raum`) VALUES (:s_user, :s_raum)",
					[
						':s_user'=>$f['s_user'],
						':s_raum'=>$f['s_raum']
					]);
			}
		} else {
			// Sperre löschen
			$result = $query->fetch();
			$s_id = $result['s_id'];
			pdoQuery("DELETE FROM `sperre` WHERE `s_id` = :s_id", [':s_id'=>$s_id]);
			
			global_msg($u_id, $r_id, "'$u_nick' $lang[sperre2] '$s_user_name' $lang[sperre5]");
		}
	} else {
		// Benutzer ist in Lobby
		if (!($admin || $u_level == "A")) {
			system_msg("", 0, $u_id, $system_farbe, $lang['sperre6']);
		} else {
			// Benutzer aus Chat werfen
			// Ist Benutzer s_user in diesem Raum? $o_id ermitteln
			$query2 = pdoQuery("SELECT `o_id` FROM `online` WHERE `o_raum` = :o_raum AND `o_user` = :o_user AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(`o_aktiv`)) <= :timeout", [':o_raum'=>$r_id, ':o_user'=>$s_user, ':timeout'=>$timeout]);
			
			$result2Count = $query2->rowCount();
			if ($result2Count > 0) {
				$result2 = $query2->fetch();
				// Benutzer rauswerfen
				$o_id = $result2['o_id']
				;
				global_msg($u_id, $r_id, "'$u_nick' $lang[sperre2] '$s_user_name' $lang[sperre7]");
				// 2 sek vor dem ausloggen warten, damit der Störer die Meldung auch noch lesen kann !!!
				sleep(2);
				
				// Aus dem Chat ausloggen
				ausloggen($s_user, $s_user_name, $r_id, $o_id);
			} else {
				system_msg("", 0, $u_id, $system_farbe, str_replace("%s_user_name%", $s_user_name, $lang['sperre4']));
			}
		}
	}
}

function ignore($o_id, $i_user_aktiv, $i_user_name_aktiv, $i_user_passiv, $i_user_name_passiv) {
	// Unterdrückt Mitteilungen von i_user_passiv an i_user_aktiv
	// Schaltet bei neuem Aufruf wieder zurück
	
	global $chat, $lang, $system_farbe;
	
	$i_user_aktiv = intval($i_user_aktiv);
	$i_user_passiv = intval($i_user_passiv);
	
	$query = pdoQuery("SELECT `u_level` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$i_user_passiv]);
	$result = $query->fetch();
	$isadmin = false;
	if ($result['u_level'] == "C" || $result['u_level'] == "S" || $result['u_level'] == "A") {
		$isadmin = true;
	}
	
	$query = pdoQuery("SELECT `i_id` FROM `iignore` WHERE `i_user_aktiv` = :i_user_aktiv AND `i_user_passiv` = :i_user_passiv", [':i_user_aktiv'=>$i_user_aktiv, ':i_user_passiv'=>$i_user_passiv]);
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		if (!$isadmin) {
			// Ignore neu setzen
			$f['i_user_aktiv'] = $i_user_aktiv;
			$f['i_user_passiv'] = $i_user_passiv;
			
			pdoQuery("INSERT INTO `iignore` (`i_user_aktiv`, `i_user_passiv`) VALUES (:i_user_aktiv, :i_user_passiv)",
				[
					':i_user_aktiv'=>$f['i_user_aktiv'],
					':i_user_passiv'=>$f['i_user_passiv']
				]);
			
			system_msg("", 0, $i_user_aktiv, (isset($system_farbe) ? $system_farbe : ""), str_replace("%i_user_name_passiv%", $i_user_name_passiv, $lang['ignore1']));
			system_msg("", 0, $i_user_passiv, (isset($system_farbe) ? $system_farbe : ""), str_replace("%i_user_name_passiv%", $i_user_name_passiv, str_replace("%i_user_name_aktiv%", $i_user_name_aktiv, $lang['ignore2'])));
		} else {
			system_msg("", 0, $i_user_aktiv, $system_farbe, str_replace("%i_user_name_passiv%", $i_user_name_passiv, $lang['ignore5']));
		}
	} else if ($resultCount > 0) {
		// Ignore löschen
		$result = $query->fetch();
		$i_id = $result['i_id'];
		pdoQuery("DELETE FROM `iignore` WHERE `i_id` = :i_id", [':i_id'=>$i_id]);
		
		system_msg("", 0, $i_user_aktiv, $system_farbe, str_replace("%i_user_name_passiv%", $i_user_name_passiv, $lang['ignore3']));
		system_msg("", 0, $i_user_passiv, $system_farbe, str_replace("%i_user_name_passiv%", $i_user_name_passiv, str_replace("%i_user_name_aktiv%", $i_user_name_aktiv, $lang['ignore4'])));
	} else {
		echo "Fataler Fehler bei ignore ($query)<br>";
	}
	
	// Kopie in Onlinedatenbank aktualisieren
	$query = pdoQuery("SELECT `i_user_passiv` FROM `iignore` WHERE `i_user_aktiv` = :i_user_aktiv", [':i_user_aktiv'=>$i_user_aktiv]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $iignore) {
			$ignore[$iignore['i_user_passiv']] = TRUE;
		}
	} else {
		$ignore[0] = FALSE;
	}
	
	pdoQuery("UPDATE `online` SET `o_ignore` = :o_ignore WHERE `o_user` = :o_user", [':o_ignore'=>serialize($ignore), ':o_user'=>$i_user_aktiv]);
}

function nick_ergaenze($part, $scope = "raum", $noerror = 0) {
	// gibt ein assoziativ_array $nick[u_nick], $nick[u_id] zurück, falls ein Nick ergänzt werden konnt.
	// falls kein Nick gefunden wurde oder mehrere Nicks gefunden wurde, werd bereits hier die Fehlerausgaben 
	// erzeugt
	
	global $u_id, $o_raum, $u_farbe, $system_farbe, $lang;
	
	// initialisieren:
	unset($nickcomplete);
	
	// Letztes Zeichen nach Nick merken
	$letzes_zeichen = substr($part, -1);
	
	// ":" und "," am Ende kürzen.
	if (preg_match("![:,]$!", $part)) {
		$part = substr($part, 0, strlen($part) - 1);
	}
	if (substr($part, 0, 1) == "@") {
		$part = substr($part, 1, strlen($part) - 1);
	}
	
	// sonderfall für Gäste -> falls nur Ziffern -> Gast ergänzen
	if (preg_match("!\d$!", $part)) {
		$ziff = ".*";
	} else {
		$ziff = "";
	}
	
	// Benutzer suchen, unterschiedliche Queries:
	switch ($scope) {
		case "raum":
			$fehler = $lang['chat_msg44'];
			$answer = $lang['chat_msg42'];
			$query = pdoQuery("SELECT `o_id`, `o_name`, `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4`, (LENGTH(`o_name`)-length(:part)) AS `laenge` FROM `online` WHERE `o_name` RLIKE :u_nick AND `o_raum` = :o_raum ORDER BY `laenge`",
				[
					':part'=>$part,
					':u_nick'=>"^($ziff$part)",
					':o_raum'=>$o_raum
				]);
		break;
		
		case "chat":
			$fehler = $lang['chat_msg26'];
			$answer = $lang['chat_msg43'];
			$query = pdoQuery("SELECT *,(LENGTH(u_nick)-length(:part)) AS `laenge` FROM `user` WHERE `u_nick` RLIKE :u_nick ORDER BY `laenge`",
				[
					':part'=>$part,
					':u_nick'=>"^($ziff$part)"
				]);
		break;
		
		case "online":
			$fehler = $lang['chat_msg25'];
			$answer = $lang['chat_msg32'];
			$query = pdoQuery("SELECT `o_id`, `o_name`, `o_userdata`, `o_userdata2`, `o_userdata3`, `o_userdata4`, (LENGTH(`o_name`)-length(:part)) AS `laenge` FROM `online` WHERE `o_name` RLIKE :u_nick ORDER BY `laenge`",
				[
					':part'=>$part,
					':u_nick'=>"^($ziff$part)"
				]);
		break;
	}
	
	$resultCount = $query->rowCount();
	if ($resultCount != 0) {
		$ok = 0;
		$result = $query->fetchAll();
		foreach($result as $zaehler => $runde) {
			if (strcasecmp($part, $runde['o_name']) == 0) {
				$nickcomplete = unserialize(
					$runde['o_userdata']
					. $runde['o_userdata2']
					. $runde['o_userdata3']
					. $runde['o_userdata4']);
				$nickcomplete['o_id'] = $runde['o_id'];
				$ok = 1;
			}
		}
		if ($resultCount == 1 || $ok == 1) {
			// gefunden
			if ($resultCount == 1) {
				//$result = $query->fetch();
				if ($scope != "chat") {
					// Daten aus online Tabelle lesen, auspacken
					$nickcomplete = unserialize(
						$result[0]['o_userdata']
						. $result[0]['o_userdata2']
						. $result[0]['o_userdata3']
						. $result[0]['o_userdata4']);
					$nickcomplete['o_id'] = $result[0]['o_id'];
				} else {
					// Daten aus Benutzertabelle lesen
					$nickcomplete = $result;
				}
			}
		} else {
			// nicht ergänzen, ist nicht eindeutig.
			$answer = str_replace("%nickkurz%", $part, $answer);
			// Info aufbereiten, wer alles hätte angesprochen werden können
			//$result = $query->fetchAll();
			$i = 0;
			foreach($result as $zaehler => $ergebnis) {
				$answer = $answer . $ergebnis['o_name'];
				if ($i < $resultCount) {
					$answer = $answer . " ";
				}
				$i++;
			}
			// und ausgeben.
			system_msg("", 0, $u_id, $system_farbe, $answer);
		}
	} else if ($letzes_zeichen != ",") {
		// Fehler ausgeben, falls erstes Wort nicht mit einem "," endet ("fido, Du..." wird ergänzt, aber "Hallo, ich..." erzeugt keinen Fehler
		// : nicht gefunden.
		if (!$noerror) {
			system_msg("", 0, $u_id, $u_farbe, str_replace("%chatzeile%", $part, $fehler));
		}
	}
	
	if (isset($nickcomplete)) {
		return $nickcomplete;
	}
}

function auto_knebel($text) {
	global $admin, $u_id, $u_nick, $o_raum, $lang, $ak, $ak2, $system_farbe, $knebelzeit;
	
	// Prüfen ob private Nachricht
	$chatzeile = preg_split("/ /", $text, 2);
	$chatzeile[0] = strtolower($chatzeile[0]);
	if (($chatzeile[0] == '/msg') || ($chatzeile[0] == '/talk') || ($chatzeile[0] == '/tell') || ($chatzeile[0] == '/msgpriv') || ($chatzeile[0] == '/t')) {
		$privatnachricht = true;
	} else {
		$privatnachricht = false;
	}
	unset($chatzeile);
	
	if (!$knebelzeit) {
		$knebelzeit = 5;
	}
	
	// für temp-admins gibts nun auch keinen autoknebel mehr
	$query = pdoQuery("SELECT `o_level` FROM `online` WHERE `o_user` = :o_user", [':o_user'=>$u_id]);
	
	$a = $query->fetch();
	if ($a['o_level'] == "A" || $a['o_level'] == "S" || $a['o_level'] == "C"
		|| $a['o_level'] == "M") {
		$admin2 = true;
	} else {
		$admin2 = false;
	}
	
	if (!$admin2) {
		if ($ak2) {
			$ak2 = str_replace("//", "", $ak2);
			$ak2 = explode("#", $ak2);
			$ak = array_merge($ak, $ak2);
		}
		for ($i = 0; $i < count($ak); $i++) {
			// Text in Kleinschrift gegen verbotene Text prüfen
			$testtext = strtolower($text);
			#$testtext=strip_tags($testtext);
			$testtext = str_replace("*", "", $testtext);
			$testtext = str_replace("_", "", $testtext);
			#$testtext=str_replace(".","\.",$testtext);
			
			if (isset($ak[$i]) && $ak[$i]) {
				if (preg_match($ak[$i], $testtext, $treffer)) {
					system_msg("", 0, $u_id, $system_farbe, $lang['knebel7']);
					
					// Hole aktuelle Knebelendzeit und geplante neue
					$query = pdoQuery("SELECT `o_knebel`, FROM_UNIXTIME(UNIX_TIMESTAMP(NOW())+" . intval($knebelzeit) . "*60) AS `knebelneu` FROM `online` WHERE `o_user` = :u_id", [':u_id'=>$u_id]);
					
					$row = $query->fetch();
					
					// Nur wenn neue Zeit größer als alte, dann erneut knebeln
					if ($row['o_knebel'] <= $row['knebelneu']) {
						pdoQuery("UPDATE `online` SET `o_knebel` = :o_knebel WHERE `o_user` = :o_user", [':o_knebel'=>"FROM_UNIXTIME(UNIX_TIMESTAMP(NOW())+" . intval($knebelzeit) . "*60)", ':o_user'=>$u_id]);
					}
					
					// Admins immer benachrichtigen, auch wenn schon geknebelt gewesen
					$query = pdoQuery("SELECT `r_name` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$o_raum]);
					$result = $query->fetch();
					$r_name = $result['r_name'];
					
					$query = pdoQuery("SELECT `o_level`, `o_name`, `o_user`, `r_name` FROM `online`, `raum` WHERE (`o_level` = 'S' OR `o_level` = 'C') AND `r_id` = `o_raum`", []);
					
					$resultCount = $query->rowCount();
					if ($resultCount > 0) {
						$result = $query->fetchAll();
						foreach($result as $zaehler => $row) {
							$txt = str_replace("%user%", zeige_userdetails($u_id, FALSE, true), $lang['knebel8']);
							$txt = str_replace("%raum%", $r_name, $txt);
							system_msg("", 0, $row['o_user'], $system_farbe, $txt);
							if (!$privatnachricht) {
								system_msg("", 0, $row['o_user'], $system_farbe, $lang['knebel9'] . " " . htmlspecialchars($text));
							} else {
								system_msg("", 0, $row['o_user'], $system_farbe, $lang['knebel10'] . " " . htmlspecialchars($treffer[0]));
							}
						}
					}
					$text = "";
				}
			}
		}
	}
	return $text;
}

?>