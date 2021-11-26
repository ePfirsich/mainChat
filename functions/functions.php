<?php
// Version / Copyright - nicht entfernen!
$mainchat_version = "mainChat 7.0.8 © by <a href=\"https://www.fidion.de\" target=\"_blank\">fidion GmbH</a> 1999-2018 - PHP7 Anpassung ab 2021 durch <a href=\"https://www.anime-community.de\" target=\"_blank\">Andreas Völkl</a> auf <a href=\"https://github.com/ePfirsich/mainChat\" target=\"_blank\">GitHub</a>";

// Caching unterdrücken
Header("Last-Modified: " . gmDate("D, d M Y H:i:s", Time()) . " GMT");
Header("Expires: " . gmDate("D, d M Y H:i:s", Time() - 3601) . " GMT");
Header("Pragma: no-cache");
Header("Cache-Control: no-cache");

// Variable initialisieren, einbinden, Community-Funktionen laden
require_once("functions-init.php");
require_once("functions-community.php");

// DB-Connect, ggf. 3 mal versuchen
for ($c = 0; $c++ < 3 AND (!(isset($mysqli_link)));) {
	$mysqli_link = mysqli_connect('p:'.$mysqlhost, $mysqluser, $mysqlpass, $dbase);
	if ($mysqli_link) {
		mysqli_set_charset($mysqli_link, "utf8mb4");
		mysqli_select_db($mysqli_link, $dbase);
	}
}

if (!(isset($mysqli_link))) {
	echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es später noch einmal!<br>";
	exit;
}

// Deklaration der gültigen Tabellenfelder die in die Datenbank geschrieben werden dürfen 
$valid_fields = array(
	'aktion' => array('a_id', 'a_user', 'a_wann', 'a_was', 'a_wie', 'a_text', 'a_zeit'),
	'bild' => array('b_id', 'b_user', 'b_name', 'b_bild', 'b_mime', 'b_width', 'b_height'),
	'blacklist' => array('f_id', 'f_userid', 'f_blacklistid', 'f_zeit', 'f_text'),
	'chat' => array('c_id', 'c_von_user', 'c_an_user', 'c_typ', 'c_raum', 'c_text', 'c_zeit', 'c_farbe', 'c_von_user_id', 'c_br'),
	'forum' => array('fo_id', 'fo_name', 'fo_order', 'fo_admin'),
	'freunde' => array('f_id', 'f_userid', 'f_freundid', 'f_zeit', 'f_text', 'f_status'),
	'iignore' => array('i_id', 'i_user_aktiv', 'i_user_passiv'),
	'invite' => array('inv_id', 'inv_raum', 'inv_user'),
	'ip_sperre' => array('is_id', 'is_ip', 'is_domain', 'is_zeit', 'is_ip_byte', 'is_owner', 'is_infotext', 'is_warn'),
	'mail' => array('m_id', 'm_status', 'm_von_uid', 'm_an_uid', 'm_zeit', 'm_geloescht_ts', 'm_betreff', 'm_text'),
	'mail_check' => array('email', 'datum', 'u_id'),
	'moderation' => array('c_id', 'c_von_user', 'c_an_user', 'c_typ', 'c_raum', 'c_text', 'c_zeit', 'c_farbe', 'c_von_user_id', 'c_moderator'),
	'online' => array('o_id', 'o_user', 'o_raum', 'o_hash', 'o_timestamp', 'o_ip', 'o_who', 'o_aktiv', 'o_chat_id', 'o_browser', 'o_name', 'o_js', 'o_knebel', 
		'o_http_stuff', 'o_http_stuff2', 'o_userdata', 'o_userdata2', 'o_userdata3', 'o_userdata4', 'o_level', 'o_ignore', 'o_login', 'o_punkte', 'o_aktion', 
		'o_timeout_zeit', 'o_timeout_warnung', 'o_chat_historie', 'o_spam_zeilen', 'o_spam_byte', 'o_spam_zeit'),
	'posting' => array('po_id', 'po_th_id', 'po_u_id', 'po_vater_id', 'po_ts', 'po_tiefe', 'po_threadorder', 'po_threadts', 'po_gesperrt', 'po_threadgesperrt', 
		'po_topposting', 'po_titel', 'po_text'),
	'raum' => array('r_id', 'r_name', 'r_eintritt', 'r_austritt', 'r_status1', 'r_besitzer', 'r_topic', 'r_status2', 'r_smilie', 'r_min_punkte'), 
	'sequence' => array('se_name', 'se_nextid'),
	'sperre' => array('s_id', 's_user', 's_raum', 's_zeit'),
	'thema' => array('th_id', 'th_fo_id', 'th_name', 'th_desc', 'th_anzthreads', 'th_anzreplys', 'th_postings', 'th_order'),
	'top10cache' => array('t_id', 't_zeit', 't_eintrag', 't_daten'),
	'user' => array('u_id', 'u_neu', 'u_login', 'u_auth', 'u_nick', 'u_passwort', 'u_adminemail', 'u_email', 'u_level', 'u_farbe', 
		'u_away', 'u_ip_historie', 'u_smilies', 'u_agb', 
		'u_zeilen', 'u_punkte_gesamt', 'u_punkte_monat', 'u_punkte_jahr', 'u_punkte_datum_monat', 'u_punkte_datum_jahr', 'u_punkte_gruppe', 'u_gelesene_postings',
		'u_chathomepage', 'u_eintritt', 'u_austritt', 'u_signatur', 'u_lastclean', 'u_loginfehler', 
		'u_nick_historie', 'u_profil_historie', 'u_kommentar', 'u_forum_postingproseite', 'u_systemmeldungen', 'u_punkte_anzeigen', 'u_sicherer_modus', 'u_knebel', 'u_avatare_anzeigen', 'u_layout_farbe', 'u_layout_chat_darstellung'),
	'userinfo' => array('ui_id', 'ui_userid', 'ui_geburt', 'ui_beruf', 'ui_hobby', 'ui_text', 'ui_farbe', 'ui_wohnort', 'ui_geschlecht', 'ui_beziehungsstatus', 'ui_typ',
		'ui_lieblingsfilm', 'ui_lieblingsserie', 'ui_lieblingsbuch', 'ui_lieblingsschauspieler', 'ui_lieblingsgetraenk', 'ui_lieblingsgericht', 'ui_lieblingsspiel', 'ui_lieblingsfarbe', 'ui_homepage')
);

// Funktionen
function mysqli_result($res,$row=0,$col=0){
	$numrows = mysqli_num_rows($res);
	if ($numrows && $row <= ($numrows-1) && $row >=0){
		mysqli_data_seek($res,$row);
		$resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
		if (isset($resrow[$col])){
			return $resrow[$col];
		}
	}
	return false;
}

function onlinezeit($onlinezeit) {
	// Alternative zu gmdate("H:i:s", $onlinezeit), jedoch geht hier Std > 24
	$zeit = ":" . substr("00" . ($onlinezeit % 60), -2); // Sekunden
	$onlinezeit = floor($onlinezeit / 60);
	$zeit = ":" . substr("00" . ($onlinezeit % 60), -2) . $zeit;
	$onlinezeit = floor($onlinezeit / 60);
	$zeit = substr("00" . ($onlinezeit), -2) . $zeit;
	return ($zeit);
}

function raum_user($r_id, $u_id, $keine_benutzer_anzeigen = true) {
	// Gibt die Benutzer im Raum r_id im Text an $u_id aus
	global $timeout, $t, $leveltext, $mysqli_link, $admin, $lobby, $unterdruecke_user_im_raum_anzeige;
	
	if ($unterdruecke_user_im_raum_anzeige != "1") {
		$query = "SELECT r_name,r_besitzer,o_user,o_name,o_userdata,o_userdata2,o_userdata3,o_userdata4 "
			. "FROM raum,online WHERE r_id=$r_id AND o_raum=r_id AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ORDER BY o_name";
		
		$result = mysqli_query($mysqli_link, $query);
		$rows = @mysqli_num_rows($result);
		
		if ($result && $rows > 0) {
			$i = 0;
			while ($row = mysqli_fetch_object($result)) {
				// Beim ersten Durchlauf Namen des Raums einfügen
				if ($i == 0) {
					$text = str_replace("%r_name%", $row->r_name, $t['raum_user1']);
				}
				
				// Benutzerdaten lesen, Liste ausgeben
				$userdata = unserialize($row->o_userdata . $row->o_userdata2 . $row->o_userdata3 . $row->o_userdata4);
				
				// Variable aus o_userdata setzen, Level und away beachten
				$uu_id = $userdata['u_id'];
				$uu_level = $userdata['u_level'];
				$uu_nick = zeige_userdetails($userdata['u_id'], $userdata, FALSE, "&nbsp;", "", "", TRUE, TRUE);
				
				if ($userdata['u_away'] != "") {
					$text .= "(" . $uu_nick . ")";
				} else {
					$text .= $uu_nick;
				}
				
				$i++;
				if ($i < $rows) {
					$text = $text . ", ";
				}
			}
			
		} else {
			if($keine_benutzer_anzeigen) {
				$text = $t['raum_user12'];
			} else {
				$text = '';
			}
		}
		
		if($text != '') {
			$back = system_msg("", 0, $u_id, "", $text);
		} else {
			$back = '';
		}
		
		mysqli_free_result($result);
	} else {
		$back = 1;
	}
	
	return ($back);
}

function ist_online($user) {
	// Prüft ob Benutzer noch online ist
	// liefert 1 oder 0 zurück
	
	global $timeout, $ist_online_raum, $mysqli_link, $whotext;
	
	$ist_online_raum = "";
	
	$user = mysqli_real_escape_string($mysqli_link, $user); // sec
	
	$query = "SELECT o_id,r_name FROM online LEFT JOIN raum ON r_id=o_raum WHERE o_user=$user " . "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
	
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		$ist_online_raum = mysqli_result($result, 0, "r_name");
		if (!$ist_online_raum || $ist_online_raum == "NULL") {
			$ist_online_raum = "[" . $whotext[2] . "]";
		}
		mysqli_free_result($result);
		return (1);
	} else {
		mysqli_free_result($result);
		return (0);
	}
}

function schreibe_moderiert($f) {
	// Schreibt Chattext in DB
	
	global $mysqli_link;
	// Schreiben falls text>0
	if (strlen($f['c_text']) > 0) {
		$back = schreibe_db("moderation", $f, "", "c_id");
	} else {
		$back = 0;
	}
	return ($back);
}

function schreibe_moderation() {
	global $u_id;
	global $mysqli_link;
	
	// alles aus der moderationstabelle schreiben, bei der u_id==c_moderator;
	$query = "SELECT * FROM moderation WHERE c_moderator=$u_id AND c_typ='N'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result > 0) {
		while ($f = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
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
			$query = "DELETE FROM moderation WHERE c_id=$f[c_id]";
			$result2 = mysqli_query($mysqli_link, $query);
		}
	}
}

function schreibe_chat($f) {
	// Schreibt Chattext in DB
	global $mysqli_link;
	
	// Schreiben falls text > 0
	if (isset($f['c_text']) && strlen($f['c_text']) > 0) {
		// Falls Länge c_text mehr als 256 Zeichen, auf mehrere Zeilen aufteilen
		if (strlen($f['c_text']) > 256) {
			$temp = $f['c_text'];
			$laenge = strlen($temp);
			$i = 0;
			// Tabelle LOCK
			$result = mysqli_query($mysqli_link, "LOCK TABLES chat WRITE");
			while ($i < $laenge) {
				$f['c_text'] = substr($temp, $i, 255);
				if ($i == 0) {
					// erste Zeile
					$f['c_br'] = "erste";
				} elseif (($i + 255) >= $laenge) {
					// letzte Zeile
					$f['c_br'] = "letzte";
				} else {
					// mittlere Zeile
					$f['c_br'] = "mitte";
				}
				$i = $i + 255;
				$back = schreibe_db("chat", $f, "", "c_id");
			}
			$result = mysqli_query($mysqli_link, "UNLOCK TABLES chat");
		} else {
			// Normale Zeile in Tabelle schreiben
			$f['c_br'] = "normal";
			$back = schreibe_db("chat", $f, "", "c_id");
		}
	} else {
		$back = 0;
	}
	return ($back);
}

function global_msg($u_id, $r_id, $text) {
	// Schreibt Text $text in Raum $r_id an alle Benutzer
	// Art:		   N: Normal
	//				  S: Systemnachricht
	//				P: Privatnachticht
	//				H: Versteckte Nachricht
	
	global $mysqli_link;
	
	if (strlen($r_id) > 0) {
		$f['c_raum'] = $r_id;
	}
	$f['c_typ'] = "S";
	$f['c_von_user_id'] = $u_id;
	$f['c_text'] = $text;
	$f['c_an_user'] = 0;
	
	$back = schreibe_chat($f);
	
	// In Session merken, dass Text im Chat geschrieben wurde
	if ($u_id) {
		$query = "UPDATE online SET o_timeout_zeit=DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), o_timeout_warnung='N' "
			. "WHERE o_user=$u_id";
		$result = mysqli_query($mysqli_link, $query);
	}
	return ($back);
}

function hidden_msg($von_user, $von_user_id, $farbe, $r_id, $text) {
	// Schreibt Text in Raum r_id
	// Art:		   N: Normal
	//				  S: Systemnachricht
	//				P: Privatnachticht
	//				H: Versteckte Nachricht
	
	global $mysqli_link;
	
	$f['c_von_user'] = $von_user;
	$f['c_von_user_id'] = $von_user_id;
	$f['c_farbe'] = $farbe;
	$f['c_raum'] = $r_id;
	$f['c_typ'] = "H";
	$f['c_text'] = $text;
	
	$back = schreibe_chat($f);
	
	// In Session merken, dass Text im Chat geschrieben wurde
	if ($von_user_id) {
		$query = "UPDATE online SET o_timeout_zeit=DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), o_timeout_warnung='N' "
			. "WHERE o_user=$von_user_id";
		$result = mysqli_query($mysqli_link, $query);
	}
	
	return ($back);
}

function priv_msg(
	$von_user,
	$von_user_id,
	$an_user,
	$farbe,
	$text,
	$userdata = "")
{
	// Schreibt privaten Text von $von_user an Benutzer $an_user
	// Art:		   N: Normal
	//				  S: Systemnachricht
	//				P: Privatnachticht
	//				H: Versteckte Nachricht
	
	global $mysqli_link;
	
	// Optional Link auf Benutzer erzeugen
	
	if ($von_user_id && is_array($userdata)) {
		$f['c_von_user'] = zeige_userdetails($von_user_id, $userdata, FALSE, "&nbsp;", "", "", FALSE, TRUE);
	} else {
		$f['c_von_user'] = $von_user;
	}
	$f['c_von_user_id'] = $von_user_id;
	$f['c_an_user'] = $an_user;
	$f['c_farbe'] = $farbe;
	$f['c_typ'] = "P";
	$f['c_text'] = $text;
	
	$back = schreibe_chat($f);
	
	// In Session merken, dass Text im Chat geschrieben wurde
	if ($von_user_id) {
		$von_user_id = mysqli_real_escape_string($mysqli_link, $von_user_id); // sec
		$query = "UPDATE online SET o_timeout_zeit=DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), o_timeout_warnung='N' "
			. "WHERE o_user=$von_user_id";
		$result = mysqli_query($mysqli_link, $query);
	}
	
	return ($back);
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
	
	$back = schreibe_chat($f);
	
	return ($back);
}

function aktualisiere_online($u_id, $o_raum) {
	// Timestamp im Datensatz aktualisieren -> Benutzer gilt als online
	global $mysqli_link;
	// sec ??
	$query = "UPDATE online SET o_aktiv=NULL WHERE o_user=$u_id";
	
	$result = mysqli_query($mysqli_link, $query);
}

function id_lese($id, $auth_id = "", $ipaddr = "", $agent = "", $referrer = "") {
	// Vergleicht Hash-Wert mit IP und Browser des Benutzers
	// Liefert Benutzer- und Online-Variable
	
	global $u_id, $u_nick, $o_id, $o_raum, $o_js, $u_level, $u_farbe, $u_smilies, $u_systemmeldungen, $u_punkte_anzeigen, $u_sicherer_modus, $u_zeilen;
	global $admin, $system_farbe, $chat_back, $ignore, $userdata, $o_punkte, $o_aktion;
	global $u_layout_farbe, $u_layout_chat_darstellung;
	global $u_away, $o_knebel, $u_punkte_gesamt, $u_punkte_gruppe, $moderationsmodul, $mysqli_link;
	global $o_who, $o_timeout_zeit, $o_timeout_warnung;
	global $o_spam_zeilen, $o_spam_byte, $o_spam_zeit, $o_dicecheck;
	global $chat, $t;
	
	// IP und Browser ermittlen
	$ip = $ipaddr ? $ipaddr : $_SERVER["REMOTE_ADDR"];
	$browser = $agent ? $agent : $_SERVER["HTTP_USER_AGENT"];
	
	$browser = str_replace("MSIE 8.0", "MSIE 7.0", $browser);
	
	$id = mysqli_real_escape_string($mysqli_link, $id);
	
	// u_id und o_id aus Objekt ermitteln, o_hash, o_browser müssen übereinstimmen
	
	$query = "SELECT HIGH_PRIORITY *,UNIX_TIMESTAMP(o_timeout_zeit) as o_timeout_zeit,"
		. "UNIX_TIMESTAMP(o_knebel)-UNIX_TIMESTAMP(NOW()) as o_knebel FROM online WHERE o_hash='$id' ";
	
	$result = mysqli_query($mysqli_link, $query);
	if (!$result) {
		echo "Fehler: " . mysqli_error($mysqli_link) . "<br><b>$query</b><br>";
		exit;
	}
	
	if ($ar = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		
		// userdaten und ignore Arrays setzen
		$userdata = unserialize($ar['o_userdata'] . $ar['o_userdata2'] . $ar['o_userdata3'] . $ar['o_userdata4']);
		$ignore = unserialize($ar['o_ignore']);
		
		unset($ar['o_ignore']);
		unset($ar['o_userdata']);
		unset($ar['o_userdata2']);
		unset($ar['o_userdata3']);
		unset($ar['o_userdata4']);
		
		// Schleife über alle Variable; Variable setzen
		while (list($k, $v) = each($ar)) {
			$$k = $v;
		}
		mysqli_free_result($result);
		
		// o_browser prüfen Benutzerdaten in Array schreiben
		if (is_array($userdata) && $ar['o_browser'] == $browser) {
			// Schleife über Benutzerdaten, Variable setzen
			while (list($k, $v) = each($userdata)) {
				@$$k = $v;
			}
			
			// Benutzereinstellungen überschreiben Default-Einstellungen
			if ($u_zeilen) {
				$chat_back = $u_zeilen;
			}
			
			// ChatAdmin oder Superuser oder moderator?
			if ($u_level == "S" || $u_level == "C" || ($moderationsmodul == 1 && $u_level == "M" && isset($r_status1) && strtolower($r_status1) == "m")) {
				$admin = 1;
			} else {
				$admin = 0;
			}
		} else {
			// Aus Sicherheitsgründen die Variablen löschen
			$userdata = "";
			$u_id = "";
			$u_nick = "";
			$o_id = "";
			$u_level = "";
			$admin = 0;
		}
	} else {
		// Aus Sicherheitsgründen die Variablen löschen
		$u_id = "";
		$u_nick = "";
		$o_id = "";
		$u_level = "";
		$admin = 0;
	}
	
	// Hole die Farbe des Benutzers
	global $u_farbe, $user_farbe;
	if (!empty($u_id)) {
		$result = mysqli_query($mysqli_link, "SELECT u_farbe FROM user WHERE u_id = " . intval($u_id));
		$row = mysqli_fetch_row($result);
		if (empty($row[0])) {
			return;
		}
		$user_farbe = $u_farbe = $row[0];
		mysqli_free_result($result);
	}
}

function iCrypt($passwort, $salt) {
	
	global $crypted_password_extern, $upgrade_password;
	
	$v_passwort = "";
	if ($upgrade_password == 1 && $crypted_password_extern == 0) {
		$salt = "";
		if (defined("CRYPT_SHA256")) {
			$salt = '$5$rounds=5000$' . gensalt(16) . '$';
		} elseif (CRYPT_MD5 == 1) {
			$salt = '$1$' . gensalt(8) . '$';
		} else {
			$salt = gensalt(2); // für den Notfall Std. DES
		}
		$upgrade_password = 0;
		$v_passwort = crypt($passwort, $salt);
	} elseif ($crypted_password_extern == 0) {
		$upgrade_password = 0;
		if ($salt == 'MD5') {
			$v_passwort = md5($passwort);
		} else {
			$v_passwort = crypt($passwort, $salt);
		}
	} else {
		$v_passwort = $passwort;
	}
	return ($v_passwort);
}

function schreibe_db($db, $f, $id, $id_name) {
	// Assoziatives Array $f in DB $db schreiben 
	// Liefert als Ergebnis die ID des geschriebenen Datensatzes zurück
	// Sonderbehandlung für Passwörter
	// Akualiert ggf Kopie des Datensatzes in online-Tabelle
	global $mysqli_link, $u_id, $valid_fields;
	
	$neu = array();
	foreach ($valid_fields[$db] as $field) {
		if (isset($f[$field])) {
			$neu[$field] = $f[$field];
		}
	}
	$f = $neu;
	
	$id == intval($id);
	
	if (strlen($id) == 0 || $id == 0) {
		// ID generieren
		if ($db == "online" || $db == "chat") {
			// ID aus sequence verwenden
			$query = "LOCK TABLES sequence WRITE";
			$result = mysqli_query($mysqli_link, $query);
			$query = "SELECT se_nextid FROM sequence WHERE se_name='$db'";
			$result = mysqli_query($mysqli_link, $query);
			if ($result) {
				$id = mysqli_result($result, 0, 0);
				mysqli_free_result($result);
				$query = "UPDATE sequence SET se_nextid='" . ($id + 1)
					. "' WHERE se_name='$db'";
				$result = mysqli_query($mysqli_link, $query);
			} else {
				echo "Schwerer Fehler in $query: " . mysqli_errno($mysqli_link) . " - "
					. mysqli_error($mysqli_link);
				$query = "UNLOCK TABLES";
				$result = mysqli_query($mysqli_link, $query);
				die();
			}
			$query = "UNLOCK TABLES";
			$result = mysqli_query($mysqli_link, $query);
			
		} else {
			// ID mit auto_increment erzeugen
			$id = 0;
		}
		
		// Datensatz neu schreiben
		$q = "";
		for (reset($f); list($name, $inhalt) = each($f);) {
			if (($name != $id_name) && ($name != "u_salt")) {
				$q .= "," . mysqli_real_escape_string($mysqli_link, $name);
				if ($name == "u_passwort") {
					if (!isset($f['u_salt']))
						$f['u_salt'] = substr($inhalt, 0, 2);
					// Verschlüsseln
						$q .= "='" . mysqli_real_escape_string($mysqli_link, iCrypt($inhalt, $f['u_salt'])) . "'";
				} else {
					$q .= "='" . mysqli_real_escape_string($mysqli_link, $inhalt) . "'";
				}
			}
		}
		$query = "INSERT INTO $db SET $id_name=$id " . $q;
		$result = mysqli_query($mysqli_link, $query);
		if (!$result) {
			echo "Fataler Fehler in $query: " . mysqli_errno($mysqli_link) . " - " . mysqli_error($mysqli_link);
			die();
		}
		if ($id == 0) {
			$id = mysqli_insert_id($mysqli_link);
		}
		
	} else {
		// bestehenden Datensatz updaten
		$q = "";
		for (reset($f); list($name, $inhalt) = each($f);) {
			if ($name != "u_salt") {
				if ($q == "") {
					$q = mysqli_real_escape_string($mysqli_link, $name);
				} else {
					$q .= "," . mysqli_real_escape_string($mysqli_link, $name);
				}
				if ($name == "u_passwort") {
					// Verschlüsseln
					if (!isset($f['u_salt'])) {
						$f['u_salt'] = substr($inhalt, 0, 2);
					}
					$q .= "='" . mysqli_real_escape_string($mysqli_link, iCrypt($inhalt, $f['u_salt'])) . "'";
				} else {
					$q .= "='" . mysqli_real_escape_string($mysqli_link, $inhalt) . "'";
				}
			}
		}
		$q = "UPDATE $db SET " . $q . " WHERE $id_name=$id";
		$result = mysqli_query($mysqli_link, $q);
	}
	
	if ($db == "user" && $id_name == "u_id") {
		// Kopie in Onlinedatenbank aktualisieren
		// Query muss mit dem Code in login() übereinstimmen
		$query = "SELECT `u_id`, `u_nick`, `u_level`, `u_farbe`, `u_zeilen`, "
			. "`u_away`, `u_email`, `u_adminemail`, `u_smilies`, `u_punkte_gesamt`, `u_punkte_gruppe`, "
			. "`u_chathomepage`, `u_systemmeldungen`, `u_punkte_anzeigen`, `u_sicherer_modus`, `u_layout_farbe`, `u_layout_chat_darstellung` "
			. "FROM `user` WHERE `u_id`=$id";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$userdata = mysqli_fetch_array($result, MYSQLI_ASSOC);
			
			// Slashes in jedem Eintrag des Array ergänzen
			reset($userdata);
			while (list($ukey, $udata) = each($userdata)) {
				$udata = mysqli_real_escape_string($mysqli_link, $udata);
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
			
			$query = "UPDATE online SET "
				. "o_userdata='" . mysqli_real_escape_string($mysqli_link, $userdata_array[0]) . "', "
				. "o_userdata2='" . mysqli_real_escape_string($mysqli_link, $userdata_array[1]) . "', "
				. "o_userdata3='" . mysqli_real_escape_string($mysqli_link, $userdata_array[2]) . "', "
				. "o_userdata4='" . mysqli_real_escape_string($mysqli_link, $userdata_array[3]) . "', "
				. "o_level='" . mysqli_real_escape_string($mysqli_link, $userdata['u_level']) . "', "
				. "o_name='" . mysqli_real_escape_string($mysqli_link, $userdata['u_nick']) . "' "
					. "WHERE o_user=$id";
			mysqli_query($mysqli_link, $query);
			mysqli_free_result($result);
			
		}
	}
	
	return ($id);
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

function zeige_tabelle_variable_breite($box, $text, $width = "") {
	// Gibt Tabelle mit einstellbarer Breiter mit Kopf und Inhalt aus
	
	if (strlen($width) > 0) {
		$width = "style=\"width:" . $width . ";\"";
	}
	?>
	
	<table class="tabelle_kopf2" <?php echo $width; ?>>
	
		<tr>
			<td class="tabelle_kopfzeile"><?php echo $box; ?></td>
		</tr>
		<tr>
			<td class="tabelle_koerper_login"><?php echo $text; ?></td>
		</tr>
	</table>
	<?php
}

function zeige_tabelle_volle_breite($box, $text) {
	// Gibt Tabelle mit 100% Breiter mit Kopf und Inhalt aus
	global $f1, $f2;
	?>
	
	<table class="tabelle_kopf">
		<tr>
			<td class="tabelle_kopfzeile"><?php echo $box; ?></td>
		</tr>
		<tr>
			<td class="tabelle_koerper"><?php echo  $f1 . $text . $f2; ?></td>
		</tr>
	</table>
	<?php
}

function zeige_tabelle_zentriert($box, $text, $margin_top = false) {
	// Gibt zentrierte Tabelle mit 99% Breiter und optionalem Abstand nach oben URL mit Kopf und Inhalt aus
	global $f1, $f2;
	
	$css = "";
	if ($margin_top) {
		$css = "style=\"margin-top: 5px;\"";
	}
	?>
	
	<table class="tabelle_kopf_zentriert" <?php echo $css; ?>>
	
		<tr>
			<td class="tabelle_kopfzeile"><?php echo $box; ?></td>
		</tr>
		<tr>
			<td class="tabelle_koerper"><?php echo $f1 . $text . $f2; ?></td>
		</tr>
	</table>
	<?php
}

function zeige_kopfzeile_login() {
	// Gibt die Kopfzeile im Login aus
	global $t, $layout_kopf, $id;
	
	// Willkommen wird nur angezeigt, wenn kein eigener Kopf definiert ist
	if (isset($layout_kopf) && $layout_kopf != "") {
		$willkommen = "";
	} else {
		$willkommen = $t['willkommen2'];
	}
	
	zeige_header_ende();
	echo "<body>";
	
	$box = $t['login_menue1'];
	$text = "<a href=\"index.php\">$t[login_menue2]</a>\n";
	if( $id == '' ) { // Registrierung nur anzeigen, wenn man nicht eingeloggt ist
		$text .= "| <a href=\"index.php?id=$id&aktion=neu\">$t[login_menue3]</a>\n";
	}
	$text .= "| <a href=\"index.php?id=$id&aktion=chatiquette\">$t[login_menue4]</a>\n";
	$text .= "| <a href=\"index.php?id=$id&aktion=nutzungsbestimmungen\">$t[login_menue5]</a>\n";
	$text .= "| <a href=\"index.php?id=$id&aktion=datenschutz\">$t[login_menue6]</a>\n";
	
	zeige_tabelle_volle_breite($box, $text);
	
	zeige_kopf();
	
	echo $willkommen;
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
	global $mysqli_link, $u_id, $system_farbe, $moderationsmodul, $raum_einstellungen, $ist_moderiert, $ist_eingang;
	$moderiert = 0;
	$raum = intval($raum);
	
	$query = "SELECT * FROM raum WHERE r_id=$raum";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) > 0) {
		$raum_einstellungen = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$r_status1 = $raum_einstellungen['r_status1'];
	}
	mysqli_free_result($result);
	if (isset($r_status1) && ($r_status1 == "m" || $r_status1 == "M")) {
		$query = "SELECT o_user FROM online "
			. "WHERE o_raum=$raum AND o_level='M' ";
		$result = mysqli_query($mysqli_link, $query);
		if (mysqli_num_rows($result) > 0)
			$moderiert = 1;
		mysqli_free_result($result);
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
		$erg = $text . "_" . $durchlauf . ": " . round($laufzeit, $rundung)
			. "s";
		if ($durchlauf > 1)
			$erg = ", " . $erg;
	}
	return ($erg);
}

function zeige_smilies($anzeigeort = 'chat') {
	global $t, $f1, $f2, $smilie, $smilietxt, $cssDeklarationen;
	
	$text = "";
	if( $anzeigeort == 'chat' ) { // Chatausgabe
		// Array mit Smilies einlesen, HTML-Tabelle ausgeben
		$text .= "<table style=\"width:100%; border:0px;\">\n";
		$zahl = 0;
		while (list($smilie_code, $smilie_grafik) = each($smilie)) {
			if ( $zahl % 2 != 0 ) {
				$farbe_tabelle = 'class="tabelle_zeile1"';
			} else {
				$farbe_tabelle = 'class="tabelle_zeile2"';
			}
			$text .= "<tr>";
			$text .= "<td $farbe_tabelle>";
			$text .= "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat(' " . $smilie_code . " '); return(false)\"><img src=\"images/smilies/" . $cssDeklarationen . "/" . $smilie_grafik . "\" alt=\"".str_replace(" ", "&nbsp;", $smilietxt[$smilie_code])."\" title=\"".str_replace(" ", "&nbsp;", $smilietxt[$smilie_code])."\"></a>";
			$text .= "</td>";
			$text .= "<td $farbe_tabelle>" . $f1 . str_replace(" ", "&nbsp;", $smilietxt[$smilie_code]) . $f2 . "</td>";
			$text .= "</tr>\n";
			$zahl++;
		}
		$text .= "</table>";
	} else { // Forumausgabe
		while (list($smilie_code, $smilie_grafik) = each($smilie)) {
			$text .= "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_forum(' " . $smilie_code . " '); return(false)\"><img src=\"images/smilies/" . $cssDeklarationen . "/" . $smilie_grafik . "\" alt=\"".str_replace(" ", "&nbsp;", $smilietxt[$smilie_code])."\" title=\"".str_replace(" ", "&nbsp;", $smilietxt[$smilie_code])."\"></a>&nbsp;";
			$zahl++;
		}
	}
	return $text;
}

function zeige_userdetails(
	$zeige_user_id,
	$userdaten = 0,
	$online = FALSE,
	$trenner = "&nbsp;",
	$online_zeit = "",
	$letzter_login = "",
	$mit_id = TRUE,
	$extra_kompakt = FALSE,
	$benutzername_fett = TRUE) {
	// Liefert Benutzernamen + Level + Gruppe + E-Mail + Homepage zurück
	// Bei online=TRUE wird der Status online/offline und opt die Onlinezeit oder der letzte Login ausgegeben
	// Falls trenner gesetzt, wird Mail/Home Symbol ausgegeben und trenner vor Mail/Home Symbol eingefügt
	// $online_zeit -> Zeit in Sekunden seit Login
	// $letzter_login -> Datum des letzten Logins
	// Falls mit_id=TRUE wird Session-ID ausgegeben, ansonsten Platzhalter
	// Falls extra_kompakt=TRUE wird nur Nick ausgegeben
	// $benutzername_fett -> Soll der Benutzername fett geschrieben werden?
	
	global $id, $system_farbe, $mysqli_link, $t, $show_geschlecht;
	global $f1, $f2, $leveltext, $punkte_grafik, $chat_grafik, $homep_ext_link;
	
	$text = "";
	if ($mit_id) {
		$idtag = $id;
	} else {
		$idtag = "<ID>";
	}
	
	if (is_array($userdaten)) {
		// Array wurde übergeben
		
		if (!isset($userdaten['u_chathomepage'])) {
			$userdaten['u_chathomepage'] = '0';
		}
		if (!isset($userdaten['u_punkte_anzeigen'])) {
			$userdaten['u_punkte_anzeigen'] = '0';
		}
		
		$user_id = $userdaten['u_id'];
		$user_nick = $userdaten['u_nick'];
		$user_level = $userdaten['u_level'];
		$user_punkte_gesamt = $userdaten['u_punkte_gesamt'];
		$user_punkte_gruppe = hexdec($userdaten['u_punkte_gruppe']);
		$user_chathomepage = htmlspecialchars($userdaten['u_chathomepage']);
		
		if ($show_geschlecht == true)
			$user_geschlecht = hole_geschlecht($zeige_user_id);
		
		$user_punkte_anzeigen = $userdaten['u_punkte_anzeigen'];
		
	} else if (is_object($userdaten)) {
		// Object wurde übergeben
		$user_id = $userdaten->u_id;
		$user_nick = $userdaten->u_nick;
		$user_level = $userdaten->u_level;
		$user_punkte_gesamt = $userdaten->u_punkte_gesamt;
		$user_punkte_gruppe = hexdec($userdaten->u_punkte_gruppe);
		if (isset($userdaten->u_chathomepage)) {
			$user_chathomepage = htmlspecialchars($userdaten->u_chathomepage);
		} else {
			$user_chathomepage = "";
		}
		
		if ($show_geschlecht == true) {
			$user_geschlecht = hole_geschlecht($zeige_user_id);
		}
		
		if (isset($userdaten->u_punkte_anzeigen)) {
			$user_punkte_anzeigen = $userdaten->u_punkte_anzeigen;
		} else {
			$user_punkte_anzeigen = "";
		}
	} else if ($zeige_user_id) {
		// Benutzerdaten aus DB lesen
		$query = "SELECT u_id,u_nick,u_level,u_away,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage,u_punkte_anzeigen, "
			. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online, date_format(u_login,'%d.%m.%y %H:%i') AS login "
			. "FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=" . intval($zeige_user_id);
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$userdaten = mysqli_fetch_object($result);
			$user_id = $userdaten->u_id;
			$user_nick = $userdaten->u_nick;
			$user_level = $userdaten->u_level;
			$user_punkte_gesamt = $userdaten->u_punkte_gesamt;
			$user_punkte_gruppe = hexdec($userdaten->u_punkte_gruppe);
			$user_chathomepage = htmlspecialchars($userdaten->u_chathomepage);
			$user_punkte_anzeigen = $userdaten->u_punkte_anzeigen;
			
			$online_zeit = $userdaten->online;
			$letzter_login = $userdaten->login;
			
		}
		mysqli_free_result($result);
		
		if ($show_geschlecht == true) {
			$user_geschlecht = hole_geschlecht($zeige_user_id);
		}
		
	} else {
		echo "<p><b>Fehler:</b> Falscher Aufruf von zeige_userdetails() für den Benutzer ";
		if (isset($zeige_user_id)) {
			echo $zeige_user_id;
		}
		if (isset($userdaten['u_id'])) {
			echo $userdaten['u_id'];
		}
		if (isset($userdaten->u_id)) {
			echo $userdaten->u_id;
		}
		echo "</p>";
		
		return "";
	}
	
	// Wenn die $user_punkte_anzeigen nicht im Array war, dann seperat abfragen
	if (!isset($user_punkte_anzeigen) || ($user_punkte_anzeigen != "1" && $user_punkte_anzeigen != "0")) {
		$query = "SELECT `u_punkte_anzeigen` FROM `user` WHERE `u_id`=" . intval($user_id);
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$userdaten = mysqli_fetch_object($result);
			$user_punkte_anzeigen = $userdaten->u_punkte_anzeigen;
		}
		mysqli_free_result($result);
	}
	
	if ($user_id != $zeige_user_id) {
		echo "<p><b>Fehler: </b> $user_id!=$zeige_user_id</p>\n";
		return "";
	}
	
	if (!$extra_kompakt) {
		$url = "inhalt.php?seite=benutzer&id=$idtag&aktion=benutzer_zeig&user=$user_id";
		if($benutzername_fett) {
			$text = "<a href=\"$url\" target=\"chat\"><b>" . $user_nick . "</b></a>";
		} else {
			$text = "<a href=\"$url\" target=\"chat\">" . $user_nick . "</a>";
		}
	} else {
		$text = $user_nick;
	}
	
	if ($show_geschlecht && $user_geschlecht) {
		$text .= $chat_grafik[$user_geschlecht];
	}
	
	// Levels, Gruppen, Home & Mail Grafiken
	$text2 = "";
	if (!isset($leveltext[$user_level])) {
		$leveltext[$user_level] = "";
	}
	if (!$extra_kompakt && $leveltext[$user_level] != "") {
		$text2 .= "&nbsp;(" . $leveltext[$user_level] . ")";
	}
	
	if (!$extra_kompakt) {
		$grafikurl1 = "<a href=\"inhalt.php?seite=hilfe&aktion=hilfe-community&id=$idtag\" target=\"chat\">";
		$grafikurl2 = "</a>";
	} else {
		$grafikurl1 = "";
		$grafikurl2 = "";
	}
	
	if (!$extra_kompakt && $user_punkte_gruppe != 0 && $user_punkte_anzeigen == "1" ) {
		
		if ($user_level == "C" || $user_level == "S") {
			$text2 .= "&nbsp;" . $grafikurl1 . $punkte_grafik[0] . $user_punkte_gruppe . $punkte_grafik[1] . $grafikurl2;
		} else {
			$text2 .= "&nbsp;" . $grafikurl1 . $punkte_grafik[2] . $user_punkte_gruppe . $punkte_grafik[3] . $grafikurl2;
		}
	}
	
	if (!$extra_kompakt && ($user_chathomepage == "1" || $homep_ext_link != "")) {
		if ($homep_ext_link != "" AND $user_level != "G") {
			$url = $homep_ext_link . $user_nick;
			$text2 .= "&nbsp;"
				. "<a href=\"$url\" target=\"_blank\">$chat_grafik[home]</a>";
		} else if ($user_chathomepage == "1") {
			$url = "home.php?/$user_nick";
			//$url = "home.php?ui_userid=$user_id&id=$idtag";
			$text2 .= "&nbsp;" . "<a href=\"$url\" target=\"_blank\">$chat_grafik[home]</a>";
		}
	}
	
	if (!$extra_kompakt && $trenner != "" && $user_nick) {
		$url = "inhalt.php?seite=nachrichten&aktion=neu2&neue_email[an_nick]=" . URLENCODE($user_nick) . "&id=" . $idtag;
		$text2 .= $trenner . "<a href=\"$url\" target=\"chat\" title=\"E-Mail\">" . "<span class=\"fa fa-envelope icon16\" alt=\"Mail\" title=\"Mail\"></span>" . "</a>";
	} else if (!$extra_kompakt && $trenner != "") {
		$text2 .= $trenner;
	}
	
	// Onlinezeit oder Datum des letzten Logins einfügen, falls Online Text fett ausgeben
	if ($online_zeit && $online_zeit != "NULL" && $online) {
		$text2 .= $trenner . str_replace("%online%", gmdate("H:i:s", $online_zeit), $t['chat_msg92']);
		$fett1 = "<b>";
		$fett2 = "</b>";
	} else if ($letzter_login && $letzter_login != "NULL" && $online) {
		$text2 .= $trenner
			. str_replace("%login%", $letzter_login, $t['chat_msg94']);
		$fett1 = "";
		$fett2 = "";
	} else if ($online) {
		$text2 .= $trenner . $t['chat_msg93'];
		$fett1 = "";
		$fett2 = "";
	} else {
		$fett1 = "";
		$fett2 = "";
	}
	
	if ($text2 != "") {
		$text .= $f1 . $text2 . $f2;
	}
	
	return ($fett1 . $text . $fett2);
}

function chat_parse($text) {
	// Filtert Text und ersetzt folgende Zeichen:
	// http://###### oder www.###### in <a href="http://###" target=_blank>http://###</A>
	// E-Mail Adressen in A-Tag mit Mailto
	
	global $admin, $sprachconfig, $u_id, $u_level;
	global $t, $system_farbe;
	
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
			if (isset($txt[$i]) && $txt[$i] != "")
				break;
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
							"http://"
								. strtr(preg_replace("!\\\\\\?!", "?", $txt3),
									$trans)) . "\" target=\"_blank\">http://"
						. preg_replace("!\\\\\\?!", "?", $txt2) . "</a>",
					$txt[$j]);
				
				$txt[$j] = str_replace("###ausruf###", "!", $txt[$j]);
				$txt[$j] = str_replace("%23%23%23ausruf%23%23%23", "!",
					$txt[$j]);
				
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

function gensalt($length)
{
	return genpassword($length);
}

function genpassword($length)
{
	// Generiert ein Passwort
	// wird auch für gensalt() genutzt
	$vowels = array("a", "e", "i", "o", "u");
	$cons = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p", "r",
		"s", "t", "u", "v", "w", "tr", "cr", "br", "fr", "th", "dr", "ch",
		"ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
	$num_vowels = count($vowels);
	$num_cons = count($cons);
	
	$password = "";
	for ($i = 0; $i < $length; $i++) {
		$password .= $cons[mt_rand(0, $num_cons - 1)]
			. $vowels[mt_rand(0, $num_vowels - 1)];
	}
	
	return substr($password, 0, $length);
}

function hole_geschlecht($userid) {
	global $mysqli_link;
	
	$query = "SELECT ui_geschlecht FROM userinfo WHERE ui_userid=" . intval($userid);
	$result = mysqli_query($mysqli_link, $query);
	if ($result AND mysqli_num_rows($result) == 1) {
		$userinfo = mysqli_fetch_object($result);
		$user_geschlecht = $userinfo->ui_geschlecht;
	}
	mysqli_free_result($result);
	
	if ($user_geschlecht == "männlich")
		$user_geschlecht = "geschlecht_maennlich";
	elseif ($user_geschlecht == "weiblich")
		$user_geschlecht = "geschlecht_weiblich";
	else
		$user_geschlecht = "";
	
	return $user_geschlecht;
}

function mailsmtp($mailempfaenger, $mailbetreff, $text2, $header, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS) {
	require 'PHPMailerAutoload.php';
	
	$mail = new PHPMailer;
	
	$mail->isSMTP();
	//$mail->SMTPDebug = 2;
	//$mail->Debugoutput = 'html';
	$mail->Host = $smtp_host;
	$mail->Port = $smtp_port;
	$mail->SMTPSecure = $smtp_encryption;
	$mail->SMTPAuth = $smtp_auth;
	$mail->SMTPAutoTLS = $smtp_autoTLS;
	$mail->Username = $smtp_username;
	$mail->Password = $smtp_password;
	$mail->setFrom($header, $chat);
	$mail->addAddress($mailempfaenger);
	$mail->Subject = $mailbetreff;
	$mail->isHtml(true);
	$mail->Body = $text2;
	
	return $mail->send();
}

function ausloggen($u_id, $u_nick, $o_raum, $o_id){
	// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
	id_lese($id);
	
	// Logout falls noch online
	if (strlen($u_id) > 0) {
		verlasse_chat($u_id, $u_nick, $o_raum);
		//sleep(2);
		logout($o_id, $u_id);
	}
}

function verlasse_chat($u_id, $u_nick, $raum) {
	// user $u_id/$u_nick verlässt $raum
	// Nachricht in Raum $raum wird erzeugt
	// Liefert ID des geschriebenen Datensatzes zurück
	
	global $mysqli_link, $chat, $system_farbe, $t, $lustigefeatures;
	global $eintritt_individuell;
	$back = 0;
	
	// Nachricht an alle
	if ($raum && $u_id) {
		$text = $t['chat_msg102'];
		
		if ($eintritt_individuell == "1") {
			$query = "SELECT `u_austritt` FROM `user` WHERE `u_id` = $u_id";
			$result = mysqli_query($mysqli_link, $query);
			$row = mysqli_fetch_object($result);
			if (strlen($row->u_austritt) > 0) {
				$text = $row->u_austritt;
				$text = "<b>&lt;&lt;&lt;</b> " . htmlspecialchars($text) . " (<b>$u_nick</b> - verlässt Chat) ";
			}
			mysqli_free_result($result);
			
			$query = "SELECT r_name FROM raum where r_id = " . intval($raum);
			$result = mysqli_query($mysqli_link, $query);
			$row = mysqli_fetch_object($result);
			if (isset($row->r_name)) {
				$r_name = $row->r_name;
			} else {
				$r_name = "[unbekannt]";
			}
			
			mysqli_free_result($result);
		}
		
		$text = str_replace("%u_nick%", $u_nick, $text);
		$text = str_replace("%user%", $u_nick, $text);
		$text = str_replace("%r_name%", $r_name, $text);
		
		$text = preg_replace("|%nick%|i", $u_nick, $text);
		$text = preg_replace("|%raum%|i", $r_name, $text);
		
		$back = global_msg($u_id, $raum, $text);
	}
	
	return ($back);
}

function logout($o_id, $u_id) {
	// Logout aus dem Gesamtsystem
	
	global $u_farbe, $mysqli_link;
	
	// Tabellen online+user exklusiv locken
	$query = "LOCK TABLES online WRITE, user WRITE";
	$result = mysqli_query($mysqli_link, $query);
	
	$o_id = mysqli_real_escape_string($mysqli_link, $o_id);
	
	// Aktuelle Punkte auf Punkte in Benutzertabelle addieren
	$result = @mysqli_query($mysqli_link,  "SELECT o_punkte,o_name,o_knebel, UNIX_TIMESTAMP(o_knebel)-UNIX_TIMESTAMP(NOW()) AS knebelrest FROM online WHERE o_user=$u_id");
	if ($result && mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_object($result);
		$u_nick = $row->o_name;
		$u_punkte = $row->o_punkte;
		if ($row->knebelrest > 0) {
			$knebelzeit = ", u_knebel='$row->o_knebel'";
		} else {
			$knebelzeit = '';
		}
		
		$query = "UPDATE user SET u_punkte_monat=u_punkte_monat+" . intval($u_punkte) . ", u_punkte_jahr=u_punkte_jahr+" . intval($u_punkte) . ", "
		. "u_punkte_gesamt=u_punkte_gesamt+" . intval($u_punkte) . $knebelzeit . " " . "WHERE u_id=$u_id";
		$result2 = mysqli_query($mysqli_link, $query);
	}
	mysqli_free_result($result);
	
	// Benutzer löschen
	$result2 = mysqli_query($mysqli_link,
	"DELETE FROM online WHERE o_id=$o_id OR o_user=$u_id");
	
	// Lock freigeben
	$query = "UNLOCK TABLES";
	$result = mysqli_query($mysqli_link, $query);
	
	// Punkterepair
	$repair1 = "UPDATE user SET u_punkte_jahr = 0, u_punkte_monat = 0, u_punkte_datum_jahr = YEAR(NOW()), u_punkte_datum_monat = MONTH(NOW()), u_login=u_login WHERE u_punkte_datum_jahr != YEAR(NOW()) AND u_id=$u_id";
	mysqli_query($mysqli_link, $repair1);
	$repair2 = "UPDATE user SET u_punkte_monat = 0, u_punkte_datum_monat = MONTH(NOW()), u_login=u_login WHERE u_punkte_datum_monat != MONTH(NOW()) AND u_id=$u_id";
	mysqli_query($mysqli_link, $repair2);
	
	// Nachrichten an Freunde verschicken
	$query = "SELECT f_id,f_text,f_userid,f_freundid,f_zeit FROM freunde WHERE f_userid=$u_id AND f_status = 'bestaetigt' UNION "
		. "SELECT f_id,f_text,f_userid,f_freundid,f_zeit FROM freunde WHERE f_freundid=$u_id AND f_status = 'bestaetigt' ORDER BY f_zeit desc ";
			
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_object($result)) {
			unset($f);
			$f['aktion'] = "Logout";
			$f['f_text'] = $row->f_text;
			if ($row->f_userid == $u_id) {
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
			aktion($u_id, $wann, $an_u_id, $u_nick, "", "Freunde", $f);
		}
	}
	mysqli_free_result($result);
}

function avatar_editieren_anzeigen(
	$u_id,
	$u_nick,
	$feld,
	$bilder,
	$aktion = "") {
	
	global $f1, $f2, $f3, $f4, $id, $t;
	
	$eingabe_breite = 55;
	
	if (is_array($bilder) && isset($bilder[$feld]) && $bilder[$feld]['b_mime']) {
		$width = $bilder[$feld]['b_width'];
		$height = $bilder[$feld]['b_height'];
		$mime = $bilder[$feld]['b_mime'];
		
		$info = $f3 . "<br>Info: " . $width . "x" . $height . " als " . $mime . $f4;
		
		if (!isset($info)) {
			$info = "";
		}
		if ($aktion == "avatar_aendern") {
			$text = "<td style=\"vertical-align:top;\"><img src=\"home_bild.php?u_id=$u_id&feld=$feld\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\"><br>" . $info . "<br>";
			$text .= $f3 . "<b>[<a href=\"inhalt.php?seite=einstellungen&id=$id&aktion=avatar_aendern&loesche=$feld&u_id=$u_id\">$t[user_zeige73]</a>]</b>" . $f4 . "<br><br></td>\n";
		} else if ($aktion == "profil") {
			if($width > 200) {
				$width = 200;
			}
			if($height > 200) {
				$height = 200;
			}
			$text = "<img src=\"home_bild.php?u_id=$u_id&feld=$feld\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\">";
		} else if ($aktion == "forum") {
			if($width > 60) {
				$width = 60;
			}
			if($height > 60) {
				$height = 60;
			}
			$text = "<img src=\"home_bild.php?u_id=$u_id&feld=$feld\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\">";
		} else {
			$text = "<img src=\"home_bild.php?u_id=$u_id&feld=$feld\" style=\"width:25px; height:25px;\" alt=\"$u_nick\">";
		}
	} else if ($aktion == "avatar_aendern") {
		$text = "<td style=\"vertical-align:top;\">";
		$text .= "<form enctype=\"multipart/form-data\" name=\"home\" action=\"inhalt.php?seite=einstellungen&id=$id&aktion=avatar_aendern\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"avatar_aendern\">\n"
		. "<input type=\"hidden\" name=\"u_id\" value=\"$u_id\">\n"
		. "<input type=\"hidden\" name=\"avatar_hochladen\" value=\"avatar_hochladen\">\n";
	$text .= "$t[user_kein_bild_hochgeladen] <input type=\"file\" name=\"$feld\" size=\"" . ($eingabe_breite / 8) . "\"><br>";
	$text .= "<br>" . "<input type=\"submit\" name=\"los\" value=\"GO\"></form><br><br></td>";
	}
	
	if ($text && $aktion == "avatar_aendern") {
		$text = "<table style=\"width:100%;\"><tr>" . $text . "</tr></table>";
	}
	
	return ($text);
}

function zeige_profilinformationen_von_id($profilfeld, $key) {
	// Löse die IDs in menschliche Informationen auf
	global $t, $sprache;
	
	require_once("languages/$sprache-profil.php");
	
	if($profilfeld == "geschlecht") {
		if($key == 1) {
			return $t['profil_geschlecht_maennlich'];
		} else if($key == 2) {
			return $t['profil_geschlecht_weiblich'];
		} else if($key == 3) {
			return $t['profil_geschlecht_divers'];
		} else {
			return $t['profil_keine_angabe'];
		}
	} else if($profilfeld == "beziehungsstatus") {
		if($key == 1) {
			return $t['profil_verheiratet'];
		} else if($key == 2) {
			return $t['profil_ledig'];
		} else if($key == 3) {
			return $t['profil_vergeben'];
		} else if($key == 4) {
			return $t['profil_single'];
		} else {
			return $t['profil_keine_angabe'];
		}
	} else if($profilfeld == "typ") {
		if($key == 1) {
			return $t['profil_typ_zierlich'];
		} else if($key == 2) {
			return $t['profil_typ_schlank'];
		} else if($key == 3) {
			return $t['profil_typ_sportlich'];
		} else if($key == 4) {
			return $t['profil_typ_normal'];
		} else if($key == 5) {
			return $t['profil_typ_mollig'];
		} else if($key == 6) {
			return $t['profil_typ_dick'];
		} else {
			return $t['profil_keine_angabe'];
		}
	} else {
		return '';
	}
}

function reset_system($wo_online) {
	global $id, $o_js, $u_level, $o_raum, $chat_back;
	// Reset ausführen
	if ($o_js) {
		if ( $wo_online == "userliste" ) {
			echo "<script>\n";
			echo "parent.frames[2].location.href='user.php?id=$id';\n";
			echo "</script>";
		} else if ( $wo_online == "forum" ) {
			echo "<script>\n";
			echo "parent.frames[2].location.href='user.php?id=$id';\n";
			echo "</script>";
		} else if ( $wo_online == "chatfenster" ) {
			echo "<script>\n";
			echo "parent.frames[1].location.href='chat.php?id=$id&back=$chat_back';\n";
			echo "</script>";
		} else if ( $wo_online == "moderator" ) {
			echo "<script>\n";
			echo "parent.frames[4].location.href='moderator.php?id=$id';\n";
			echo "</script>";
		} else if ($u_level == "M") {
			echo "<script>\n";
			echo "parent.frames[0].location.href='navigation.php?id=$id';";
			echo "parent.frames[1].location.href='chat.php?id=$id&back=$chat_back';\n";
			echo "parent.frames[2].location.href='user.php?id=$id';\n";
			echo "parent.frames[3].location.href='eingabe.php?id=$id';\n";
			echo "parent.frames[4].location.href='moderator.php?id=$id';\n";
			echo "parent.frames[5].location.href='interaktiv.php?id=$id&o_raum_alt=$o_raum';\n";
			echo "</script>";
		} else {
			echo "<script>\n";
			echo "parent.frames[0].location.href='navigation.php?id=$id';";
			echo "parent.frames[1].location.href='chat.php?id=$id&back=$chat_back';\n";
			echo "parent.frames[2].location.href='user.php?id=$id';\n";
			echo "parent.frames[3].location.href='eingabe.php?id=$id';\n";
			echo "parent.frames[4].location.href='interaktiv.php?id=$id&o_raum_alt=$o_raum';\n";
			echo "</script>";
		}
	}
}
?>