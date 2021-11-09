<?php
require_once("functions-registerglobals.php");

if (!isset($_SERVER["HTTP_REFERER"])) {
	$_SERVER["HTTP_REFERER"] = "";
}

// Funktionen und Config laden, Host bestimmen
require_once("functions-init.php");
require_once("functions.php");

zeige_header_anfang($body_titel, 'login', $zusatztext_kopf);

// Liste offener Räume definieren (optional)
if ($raum_auswahl && (!isset($beichtstuhl) || !$beichtstuhl)) {
	// Falls der Eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
	if (strlen($eintrittsraum) == 0) {
		$eintrittsraum = $lobby;
	}
	
	// Raumauswahlliste erstellen
	$query = "SELECT r_name,r_id FROM raum WHERE (r_status1='O' OR r_status1='E' OR r_status1 LIKE BINARY 'm') AND r_status2='P' ORDER BY r_name";
	
	$result = @mysqli_query($mysqli_link, $query);
	if ($result) {
		$rows = mysqli_num_rows($result);
	} else {
		echo "<p><b>Datenbankfehler:</b> " . mysqli_error($mysqli_link) . ", " . mysqli_errno($mysqli_link) . "</p>";
		die();
	}
	if ($communityfeatures && $forumfeatures) {
		$raeume = "<td><b>" . $t['login22'] . "</b><br>";
	} else {
		$raeume = "<td><b>" . $t['login12'] . "</b><br>";
	}
	$raeume .= $f1 . "<select name=\"eintritt\">";
	
	if ($communityfeatures && $forumfeatures) {
		$raeume = $raeume . "<option value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
	}
	if ($rows > 0) {
		$i = 0;
		while ($i < $rows) {
			$r_id = mysqli_result($result, $i, "r_id");
			$r_name = mysqli_result($result, $i, "r_name");
			if ((!isset($eintritt) AND $r_name == $eintrittsraum)
				|| (isset($eintritt) AND $r_id == $eintritt)) {
				$raeume = $raeume
					. "<option selected value=\"$r_id\">$r_name\n";
			} else {
				$raeume = $raeume . "<option value=\"$r_id\">$r_name\n";
			}
			$i++;
		}
	}
	if ($communityfeatures && $forumfeatures)
		$raeume = $raeume
			. "<option value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
	$raeume = $raeume . "</select>" . $f2 . "</td>\n";
	mysqli_free_result($result);
} else {
	if (strlen($eintrittsraum) == 0) {
		$eintrittsraum = $lobby;
	}
	$lobby_id = RaumNameToRaumID($eintrittsraum);
	$raeume = "<td><input type=hidden name=\"eintritt\" value=$lobby_id></td>\n";
}

$eingabe_breite = 20;

// Logintext definieren
$logintext = "<table style=\"width:100%;\"><tr><td><b>"	. $t['login8'] . "</b><br>" . $f1 . "<input type=\"text\" name=\"login\" value=\"";
if (isset($login)) {
	$logintext .= $login;
}
$logintext .= "\" size=$eingabe_breite>" . $f2 . "</td>\n" . "<td><b>"
	. $t['login9'] . "</b><br>" . $f1
	. "<input type=\"password\" name=\"passwort\" size=$eingabe_breite>"
	. $f2 . "</td>\n" . $raeume . "<td><br>" . $f1
	. "<b><input type=\"submit\" name=\"los\" value=\"" . $t['login10']
	. "\"></b>\n"
	. "<input type=\"hidden\" name=\"aktion\" value=\"login\">" . $f2
	. "</td>\n" . "</tr></table>\n" . $t['login3'];

// IP bestimmen und prüfen. Ist Login erlaubt?
$abweisen = false;
$warnung = false;
$ip_adr = $_SERVER["REMOTE_ADDR"];
$ip_name = @gethostbyaddr($ip_adr);

// Sperrt den Chat, wenn in der Sperre Domain "-GLOBAL-" ist
$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GLOBAL-'";
$result = mysqli_query($mysqli_link, $query);
if ($result && mysqli_num_rows($result) > 0) {
	$abweisen = true;
}
mysqli_free_result($result);

// Gastsperre aktiv? Wird beim Login und beim Nutzungsbestimmungen Login ausgewertet
$temp_gast_sperre = false;
// Wenn die dbase = "mainchat" und in Sperre = "-GAST-"
// dann Gastlogin gesperrt
$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GAST-'";
$result = mysqli_query($mysqli_link, $query);
if ($result && mysqli_num_rows($result) > 0) {
	$temp_gast_sperre = true;
}
mysqli_free_result($result);

$query = "SELECT * FROM ip_sperre "
	. "WHERE (SUBSTRING_INDEX(is_ip,'.',is_ip_byte) "
	. " LIKE SUBSTRING_INDEX('" . mysqli_real_escape_string($mysqli_link, $ip_adr) . "','.',is_ip_byte) AND is_ip IS NOT NULL) "
	. "OR (is_domain LIKE RIGHT('" . mysqli_real_escape_string($mysqli_link, $ip_name) . "',LENGTH(is_domain)) AND LENGTH(is_domain)>0)";
$result = mysqli_query($mysqli_link, $query);
$rows = mysqli_num_rows($result);

if ($rows > 0) {
	while ($row = mysqli_fetch_object($result)) {
		if ($row->is_warn == "ja") {
			// Warnung ausgeben
			$warnung = true;
			$infotext = $row->is_infotext;
		} else {
			// IP ist gesperrt
			$abweisen = true;
		}
	}
}
mysqli_free_result($result);

// HTTP_X_FORWARDED_FOR IP bestimmen und prüfen. Ist Login erlaubt?
if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
	$ip_adr = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else {
	$ip_adr = "";
}

if (!$abweisen && $ip_adr && $ip_adr != $_SERVER["REMOTE_ADDR"]) {
	$ip_name = @gethostbyaddr($ip_adr);
	$query = "SELECT * FROM ip_sperre "
		. "WHERE (SUBSTRING_INDEX(is_ip,'.',is_ip_byte) "
		. " LIKE SUBSTRING_INDEX('" . mysqli_real_escape_string($mysqli_link, $ip_adr) . "','.',is_ip_byte) AND is_ip IS NOT NULL) "
		. "OR (is_domain LIKE RIGHT('" . mysqli_real_escape_string($mysqli_link, $ip_name) . "',LENGTH(is_domain)) AND LENGTH(is_domain)>0)";
	$result = mysqli_query($mysqli_link, $query);
	$rows = mysqli_num_rows($result);
	
	if ($rows > 0) {
		while ($row = mysqli_fetch_object($result)) {
			if ($row->is_warn == "ja") {
				// Warnung ausgeben
				$warnung = true;
				$infotext = $row->is_infotext;
			} else {
				// IP ist gesperrt
				$abweisen = true;
			}
		}
	}
	mysqli_free_result($result);
}

// zweite Prüfung, gibts was, was mit "*" in der mitte schafft? für p3cea9*.t-online.de
$query = "SELECT * FROM ip_sperre WHERE is_domain like '_%*%_'";
$result = mysqli_query($mysqli_link, $query);
if ($result && mysqli_num_rows($result) > 0) {
	while ($row = mysqli_fetch_object($result)) {
		$part = explode("*", $row->is_domain, 2);
		if ((strlen($part[0]) > 0) && (strlen($part[1]) > 0)) {
			if (substr($ip_name, 0, strlen($part[0])) == $part[0]
				&& substr($ip_name, strlen($ip_name) - strlen($part[1]),
					strlen($part[1])) == $part[1]) {
				
				// IP stimmt überein
				if ($row->is_warn == "ja") {
					// Warnung ausgeben
					$warnung = true;
					$infotext = $row->is_infotext;
				} else {
					// IP ist gesperrt
					$abweisen = true;
				}
			}
		}
	}
}
mysqli_free_result($result);

// Wenn $abweisen=true, dann ist Login ist für diesen Benutzer gesperrt
// Es sei denn wechsel Forum -> Chat, dann "Relogin", und wechsel trotz IP Sperre in Chat möglich
if ($abweisen && $aktion != "relogin" && strlen($login) > 0) {
	// test: ist user=admin -> dann nicht abweisen...
	$query = "SELECT `u_nick`, `u_level` FROM `user` WHERE `u_nick`='"
		. mysqli_real_escape_string($mysqli_link, coreCheckName($login, $check_name))
		. "' AND (u_level in ('S','C'))";
	$r = mysqli_query($mysqli_link, $query);
	$rw = mysqli_num_rows($r);
	
	if ($rw == 1 && (strlen($aktion) > 0)) {
		$abweisen = false;
	}
	mysqli_free_result($r);
	
	// Prüfung nun auf Admin beendet
	// Nun Prüfung ob genug Punkte
	$durchgangwegenpunkte = 0;
	
	if ($communityfeatures && $loginwhileipsperre <> 0) {
		// Test auf Punkte 
		$query = "SELECT u_id, u_nick,u_level,u_punkte_gesamt FROM user "
			. "WHERE (u_nick='" . mysqli_real_escape_string($mysqli_link, coreCheckName($login, $check_name))
			. "' AND (u_level in ('A','C','G','M','S','U')) ";
		"AND u_passwort = encrypt('" . mysqli_real_escape_string($mysqli_link, $passwort) . "',u_passwort)";
		// Nutzt die MYSQL -> Unix crypt um DES, SHA256, etc. automatisch zu erkennen
		// Durchleitung wg. Punkten im Fall der MD5() verschlüsselung wird nicht gehen
		$r = mysqli_query($mysqli_link, $query);
		$rw = mysqli_num_rows($r);
		
		if ($rw == 1 && strlen($aktion) > 0) {
			$row = mysqli_fetch_object($r);
			if ($row->u_punkte_gesamt >= $loginwhileipsperre) {
				// genügend Punkte
				// Login zulassen
				$durchgangwegenpunkte = 1;
				$abweisen = false;
				$t_u_id = $row->u_id;
				$t_u_nick = $row->u_nick;
				$infotext = str_replace("%punkte%", $row->u_punkte_gesamt,
					$t['ipsperre2']);
			}
		}
		mysqli_free_result($r);
	}
	
	if ($durchgangwegenpunkte == 1) {
		// Wenn Benutzer wegen Punkte durch IP Sperre kommen, dann Meldung an alle Admins
		if ($eintritt == 'forum') {
			$raumname = " (" . $whotext[2] . ")";
		} else {
			$query2 = "SELECT r_name FROM raum WHERE r_id=" . intval($eintritt);
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
			$txt = str_replace("%ip_adr%", $ip_adr, $t['ipsperre1']);
			$txt = str_replace("%ip_name%", $ip_name, $txt);
			$txt = str_replace("%is_infotext%", $infotext, $txt);
			while ($row2 = mysqli_fetch_object($result2)) {
				$ur1 = "user.php?id=<ID>&aktion=zeig&user=$t_u_id";
				$ah1 = "<a href=\"$ur1\" target=\"$t_u_nick\" onclick=\"neuesFenster('$ur1','"
					. $t_u_nick . "'); return(false);\">";
				$ah2 = "</A>";
				system_msg("", 0, $row2->o_user, $system_farbe,
					str_replace("%u_nick%",
						$ah1 . $t_u_nick . $ah2 . $raumname, $txt));
			}
			unset($infotext);
			unset($t_u_id);
			unset($u_nick);
		}
		mysqli_free_result($result2);
	}
}

if ($abweisen && (strlen($aktion) > 0) && $aktion <> "relogin") {
	$aktion = "abweisen";
	unset($logintext);
}

// Login ist für alle Benutzer gesperrt
if (($chat_offline_kunde) || ((isset($chat_offline)) && (strlen($chat_offline) > 0))) {
	$aktion = "gesperrt";
}

// Ausloggen, falls eingeloggt
if ($aktion == "logoff") {
	// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
	id_lese($id);
	
	// Logout falls noch online
	if (strlen($u_id) > 0) {
		verlasse_chat($u_id, $u_nick, $o_raum);
		sleep(2);
		logout($o_id, $u_id, "index->logout");
	}
}

// Falls in Loginmaske/Nutzungsbestimmungen auf Abbruch geklickt wurde
if ($los == $t['login18'] && $aktion == "login")
	$aktion = "";

// Titeltext der Loginbox setzen, Link auf Registrierung optional ausgeben

if ($neuregistrierung_deaktivieren) {
	$login_titel = $t['default1'];
	if ($aktion == "neu")
		$aktion = "";
	if ($aktion == "neu2")
		$aktion = "";
	if ($aktion == "mailcheckm")
		$aktion = "";
} else if ($t['login14']) {
	$login_titel = $t['default1'] . " [<a href=\"index.php?aktion=neu\">" . $t['login14'] . "</a>]";
} else {
	$login_titel = $t['default1'];
}

if ($aktion == "neu" && $pruefe_email == "1" && isset($f['u_adminemail']) && $hash != md5($f['u_adminemail'] . "+" . date("Y-m-d"))) {
		zeige_header_ende();
		?>
		<body>
		<?php
	zeige_kopf();
	echo "<p><b>Fehler:</b> Die URL ist nicht korrekt! Bitte melden Sie sich <a href=\"" . $chat_url . "/index.php\">hier</a> neu an.</p>";
	
	zeige_fuss();
	exit;
}

if ($aktion == "neu" && $pruefe_email == "1" && $los != $t['neu22'])
	$aktion = "mailcheck";
if ($aktion == "neu2") {
	$aktion = "mailcheckm";
}

if (isset($f['u_adminemail'])) {
	$ro = "readonly";
}

if ($aktion == "mailcheck" && isset($email) && isset($hash)) {
	$email = mysqli_real_escape_string($mysqli_link, $email);
	$query = "SELECT * FROM mail_check WHERE email = '$email'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$a = mysqli_fetch_array($result);
		$hash2 = md5($a['email'] . "+" . $a['datum']);
		if ($hash == $hash2) {
			
			$aktion = "neu";
			$ro = "readonly";
			$f['u_adminemail'] = $email;
		} else {
			// Header ausgeben
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			echo "<p><b>Fehler:</b> Die URL ist nicht korrekt! Bitte melden Sie sich <a href=\"" . mysqli_real_escape_string($mysqli_link, $chat_url) . "/index.php\">hier</a> neu an.</p>";
			$query = "DELETE FROM mail_check WHERE email = '$email'";
			mysqli_query($mysqli_link, $query);
			zeige_fuss();
			exit;
		}
	} else {
		// Header ausgeben
		zeige_header_ende();
		?>
		<body>
		<?php
		zeige_kopf();
		echo "<p><b>Fehler:</b> Diese Mail wurde bereits für eine Anmeldung benutzt! Bitte melden Sie sich " . "<a href=\"" . $chat_url . "/index.php\">hier</a> neu an.</p>";
		zeige_fuss();
		exit;
	}
}

switch ($aktion) {
	case "impressum":
		// Impressumg anzeigen
		require_once('templates/impressum.php');

		break;
		
	case "datenschutz":
		// Datenschutz anzeigen
		require_once('templates/datenschutz.php');
		
		break;
		
	case "chatiquette":
		// Datenschutz anzeigen
		require_once('templates/chatiquette.php');
		
		break;
		
	case "nutzungsbestimmungen":
		// Datenschutz anzeigen
		require_once('templates/nutzungsbestimmungen.php');
		
		break;
		
	case "hilfe":
		// Hilfe anzeigen
		require_once('templates/hilfe.php');
		
		break;
		
	case "hilfe-befehle":
		id_lese($id);
		// Liste aller Befehle anzeigen
		require_once('templates/hilfe-befehle.php');
		
		break;
		
	case "hilfe-sprueche":
		// Liste aller Sprüche anzeigen
		require_once('templates/hilfe-sprueche.php');
		
		break;
		
	case "hilfe-community":
		// Punkte/Community anzeigen
		require_once('templates/hilfe-community.php');
		
		break;
		
	case "passwort_neu":
		// Gibt die Kopfzeile im Login aus
		show_kopfzeile_login();
		
		unset($richtig);
		unset($u_id);
		
		$richtig = 0;
		$fehlermeldung = "";
		
		if (isset($email) && isset($nickname) && isset($hash)) {
			$nickname = mysqli_real_escape_string($mysqli_link, coreCheckName($nickname, $check_name));
			$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
			$query = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user WHERE u_nick = '$nickname' AND u_level != 'G' AND u_adminemail = '$email' LIMIT 1";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				$a = mysqli_fetch_array($result);
				$hash2 = md5($a['u_id'] . $a['u_login'] . $a['u_nick'] . $a['u_passwort'] . $a['u_adminemail'] . $a['u_punkte_jahr']);
				if ($hash == $hash2) {
					$richtig = 1;
					$u_id = $a['u_id'];
				} else {
					$fehlermeldung .= $t['pwneu11'];
				}
			} else {
				$fehlermeldung .= $t['pwneu11'];
			}
			mysqli_free_result($result);
		} else if (isset($email) || isset($nickname)) {
			
			if( isset($nickname) && $nickname != "" ) {
				$nickname = mysqli_real_escape_string($mysqli_link, coreCheckName($nickname, $check_name));
			} else {
				$nickname = "";
			}
			
			if( isset($email) && $email != "" ) {
				$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
				if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", mysqli_real_escape_string($mysqli_link, $email))) {
					$fehlermeldung .= $t['pwneu5'] . '<br>';
				}
			} else {
				$email = "";
			}
			
			if( $email == "" && $nickname == "" ) {
				$fehlermeldung .= $t['pwneu17'] . '<br>';
			}
			
			if ($fehlermeldung == "") {
				if($nickname != "") {
					$query = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user " . "WHERE u_nick = '$nickname' LIMIT 2";
				} else {
					$query1 = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user " . "WHERE u_adminemail = '$email' LIMIT 2";
				}
				//$query = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user " . "WHERE u_nick = '$nickname' OR u_adminemail = '$email' LIMIT 2";
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) == 1) {
					$a = mysqli_fetch_array($result);
					$hash = md5(
						$a['u_id'] . $a['u_login'] . $a['u_nick']
							. $a['u_passwort']
							. $a['u_adminemail'] . $a['u_punkte_jahr']);
					
					$email = urlencode($a['u_adminemail']);
					$link = $chat_url . "/index.php?aktion=passwort_neu&frame=1&email=" . $email . "&nickname=" . $nickname . "&hash=" . $hash;
					
					$text2 = str_replace("%link%", $link, $t['pwneu9']);
					$text2 = str_replace("%hash%", $hash, $text2);
					$text2 = str_replace("%nickname%", $a['u_nick'], $text2);
					$email = urldecode($a['u_adminemail']);
					$text2 = str_replace("%email%", $email, $text2);
					
					$mailbetreff = $t['pwneu8'];
					$mailempfaenger = $email;
					$header = "\n" . "X-MC-IP: " . $_SERVER["REMOTE_ADDR"] . "\n" . "X-MC-TS: " . time();
					$header .= "Mime-Version: 1.0\r\n";
					$header .= "Content-type: text/plain; charset=utf-8";
					
					// E-Mail versenden
					if($smtp_on) {
						mailsmtp($mailempfaenger, $mailbetreff, $text2, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
					} else {
						mail($mailempfaenger, $mailbetreff, $text2, "From: $webmaster ($chat)" . $header);
					}
					
					echo $t['pwneu7'];
					unset($hash);
				} else {
					if($nickname != "") {
						$fehlermeldung .= $t['pwneu6'] . '<br>';
					} else {
						$fehlermeldung .= $t['pwneu18'] . '<br>';
					}
					unset($hash);
				}
				mysqli_free_result($result);
			}
		} else {
			echo $t['pwneu1'];
		}
		
		if (!$richtig) {
			if ($fehlermeldung <> "") {
				if (isset($hash)) {
					echo $t['pwneu7'];
				} else {
					echo $t['pwneu1'];
				}
				echo "<p style=\"color:#ff0000; font-weight:bold;\">$fehlermeldung</p>\n";
			}
			$box = $t['pwneu16'];
			$text = '';
			$text .= "<form action=\"index.php\">\n";
			$text .= "<table>\n";
			if ( (isset($email) || isset($nickname)) && $email <> "" && $nickname <> "" && (isset($hash) || $fehlermeldung == "") ) {
				if (!isset($hash)) {
					$hash = "";
				}
				$text .="<tr>";
				$text .="<td style=\"font-weight:bold;\">$t[pwneu2]</td>";
				$text .="<td><input type=hidden name=\"nickname\" width=\"50\" value=\"$nickname\">$nickname</td>";
				$text .="</tr>";
				$text .="<tr>";
				$text .="<td style=\"font-weight:bold;\">$t[pwneu3]</td>";
				$text .="<td style=\"font-weight:bold;\"><input type=\"hidden\" name=\"email\" width=\"50\" value=\"$email\">$email</td>";
				$text .="</tr>";
				$text .="<tr>";
				$text .="<td style=\"font-weight:bold;\">$t[pwneu10]</td>";
				$text .="<td style=\"font-weight:bold;\"><input name=\"hash\" width=\"50\" value=\"$hash\"></td>";
				$text .="</tr>";
			} else {
				if (!isset($nickname)) {
					$nickname = "";
				}
				if (!isset($email)) {
					$email = "";
				}
				$text .="<tr>";
				$text .="<td style=\"font-weight:bold;\">$t[pwneu2]</td>";
				$text .="<td style=\"font-weight:bold;\"><input name=\"nickname\" width=\"50\" value=\"$nickname\"></td>";
				$text .="</tr>";
				$text .="<tr>";
				$text .="<td style=\"font-weight:bold;\">$t[pwneu3]</td>";
				$text .="<td style=\"font-weight:bold;\"><input name=\"email\" width=\"50\" value=\"$email\"></td>";
				$text .="</tr>";
			}
			$text .="<tr>";
			$text .="<td colspan=\"2\" style=\"font-weight:bold;\"><input type=\"hidden\" name=\"aktion\" value=\"passwort_neu\"><input type=\"submit\" value=\"Absenden\"></td>";
			$text .="</tr>";
			$text .= "</table>";
			$text .= "</form>";
			
			show_box($box,$text);
			
		} else if ($richtig && $u_id) {
			$query = "SELECT `u_adminemail`, `u_nick` FROM `user` WHERE `u_id` = '$u_id' LIMIT 2";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				unset($f);
				$a = mysqli_fetch_array($result);
				
				$pwdneu = genpassword(8);
				$f['u_passwort'] = $pwdneu;
				$f['u_id'] = $u_id;
				$text = str_replace("%passwort%", $f['u_passwort'], $t['pwneu15']);
				$text = str_replace("%nickname%", $a['u_nick'], $text);
				
				// E-Mail versenden
				if($smtp_on) {
					$ok = mailsmtp($a['u_adminemail'], $t['pwneu14'], $text, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
				} else {
					$ok = mail($a['u_adminemail'], $t['pwneu14'], $text, "From: $webmaster ($chat)");
				}
				
				if ($ok) {
					echo $t['pwneu12'];
					schreibe_db("user", $f, $f['u_id'], "u_id");
				} else {
					echo $t['pwneu13'];
				}
			}
		}
		
		zeige_fuss();
		break;
	
	case "neubestaetigen":
		// Gibt die Kopfzeile im Login aus
		show_kopfzeile_login();
		
		unset($fehlermeldung);
		if ($email) {
			$email = mysqli_real_escape_string($mysqli_link, $email);
			if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
				mysqli_real_escape_string($mysqli_link, $email))) {
				$fehlermeldung = $t['neu51'];
			} else {
				$query = "SELECT * FROM mail_check WHERE email = '$email'";
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) == 1) {
					$a = mysqli_fetch_array($result);
					$hash2 = md5($a['email'] . "+" . $a['datum']);
					if ($hash == $hash2) {
						$link = $chat_url . "/index.php?aktion=neu2&frame=1";
						
						$text2 = str_replace("%link%", $link, $t['neu53']);
						$text2 = str_replace("%hash%", $hash, $text2);
						$email = urldecode($email);
						$text2 = str_replace("%email%", $email, $text2);
						
						$mailbetreff = $t['neu38'];
						$mailempfaenger = $email;
						
						echo $t['neu54'];
						
						// E-Mail versenden
						if($smtp_on) {
							mailsmtp($mailempfaenger, $mailbetreff, $text2, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
						} else {
							mail($mailempfaenger, $mailbetreff, $text2, "From: $webmaster ($chat)" . $header);
						}
					} else {
						$fehlermeldung = $t['neu52'];
					}
				} else {
					$fehlermeldung = $t['neu51'];
				}
				mysqli_free_result($result);
			}
		}
		
		if (!$email || $fehlermeldung) {
			// Formular für die Freischaltung/Bestätigung der Mailadress
			echo "<p>" . $t['neu50'] . "</p>";
			if ($fehlermeldung) {
				echo "<p style=\"color:#ff0000; font-weight:bold;\">$fehlermeldung</p>\n";
			}
			?>
			<form action="index.php">
				<table>
					<tr>
						<td class="tabelle_kopfzeile"><?php echo $t['neu49']; ?></td>
						<td><input name="email" width="50" value="<?php echo $email; ?>"></td>
					</tr>
					<tr>
						<td class="tabelle_kopfzeile"><?php echo $t['neu43']; ?></td>
						<td class="tabelle_kopfzeile"><input name="hash" width="50" value="<?php echo $hash; ?>"></td>
					</tr>
					<input type="hidden" name="aktion" value="neubestaetigen">
					<tr>
						<td colspan="2" class="tabelle_kopfzeile"><input type="submit" value="Absenden"></td>
					</tr>
				</table>
			</form>
			<?php
		}
		
		zeige_fuss();
		break;
	
	case "mailcheckm":
		// Gibt die Kopfzeile im Login aus
		show_kopfzeile_login();
		
		echo "<p>" . $t['neu42'] . "</p>";
		?>
		<form action="index.php">
			<table>
				<tr>
					<td class="tabelle_kopfzeile"><?php echo $t['neu17']; ?></td>
					<td class="tabelle_kopfzeile"><input name="email" width="50"></td>
				</tr>
				<tr>
					<td class="tabelle_kopfzeile"><?php echo $t['neu43']; ?></td>
					<td class="tabelle_kopfzeile"><input name="hash" width="50"></td>
				</tr>
				<input type="hidden" name="aktion" value="neu">
				<tr>
					<td colspan="2" class="tabelle_kopfzeile"><input type=submit value="Absenden"></td>
				</tr>
			</table>
		</form>
		<?php
		zeige_fuss();
		break;
	
	case "mailcheck":
		// Gibt die Kopfzeile im Login aus
		show_kopfzeile_login();
		
		// dieser Regex macht eine primitive Prüfung ob eine Mailadresse
		// der Form name@do.main entspricht
		if ( isset($email) && !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", mysqli_real_escape_string($mysqli_link, $email)) ) {
				echo str_replace("%email%", $email, $t['neu41']);
				$email = "";
				
				$box = $t['neu31'];
				$text = '';
				$text .= "<form action=\"index.php\">";
				$text .= $t['neu34'] . " <input name=\"email\" maxlength=\"80\">";
				$text .= "<input type=\"hidden\" name=\"aktion\" value=\"mailcheck\">";
				$text .= "<input type=\"submit\" value=\"". $t['neu35'] . "\">";
				$text .= "</form>";
				
				show_box($box,$text);
		} else if (!isset($email)) {
			// Formular für die Erstregistierung ausgeben, 1. Schritt
			echo "<p>" . $t['neu33'];
			if (isset($anmeldung_nurmitbest)
				&& strlen($anmeldung_nurmitbest) > 0) { // Anmeldung mit Externer Bestätigung
				echo $t['neu45'];
			}
			
			$box = $t['neu31'];
			$text = '';
			$text .= "<form action=\"index.php\">";
			$text .= $t['neu34'] . " <input name=\"email\" maxlength=\"80\">";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"mailcheck\">";
			$text .= "<input type=\"submit\" value=\"". $t['neu35'] . "\">";
			$text .= "</form>";
			
			show_box($box,$text);
		} else {
			// Mail verschicken, 2. Schritt
			
			$email = trim($email);
			
			// wir prüfen ob Benutzer gesperrt ist
			// entweder Benutzer = gesperrt
			$query = "SELECT * FROM `user` WHERE ( (`u_adminemail`='$email') OR (`u_email`='$email') ) AND `u_level`='Z'";
			$result = mysqli_query($mysqli_link, $query);
			$num = mysqli_num_rows($result);
			$gesperrt = false;
			if ($num >= 1) {
				$gesperrt = true;
				echo $t['neu40'];
			}
			
			// oder user ist auf Blacklist
			$query = "select u_nick from blacklist left join user on f_blacklistid=u_id "
				. "WHERE user.u_adminemail ='$email'";
			$result = mysqli_query($mysqli_link, $query);
			$num = mysqli_num_rows($result);
			if ($num >= 1) {
				$gesperrt = true;
				echo $t['neu40'];
			}
			
			// oder Domain ist lt. Config verboten
			if ($domaingesperrtdbase != $dbase) {
				$domaingesperrt = array();
			}
			
			for ($i = 0; $i < count($domaingesperrt); $i++) {
				$teststring = strtolower($email);
				if (isset($domaingesperrt[$i]) && $domaingesperrt[$i]
					&& (preg_match($domaingesperrt[$i], $teststring))) {
					$gesperrt = true;
					echo $t['neu40'];
				}
			}
			unset($teststring);
			
			if ( !$gesperrt ) {
				$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '$email'";
				$result = mysqli_query($mysqli_link, $query);
				$num = mysqli_num_rows($result);
				// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
				if ($num >= 1) {
					$gesperrt = true;
					echo $t['neu55'] . "<br><br>";
				}
			}
			
			if (!$gesperrt) {
				// Überprüfung auf Formular mehrmals abgeschickt
				$query = "DELETE FROM mail_check WHERE email = '$email'";
				mysqli_query($mysqli_link, $query);
				
				$hash = md5($email . "+" . date("Y-m-d"));
				$email = urlencode($email);
				
				if (isset($anmeldung_nurmitbest) && strlen($anmeldung_nurmitbest) > 0) {
					// Anmeldung mit externer Bestätigung
					$link1 = $chat_url . "/index.php?aktion=neubestaetigen&frame=1";
					$link2 = $chat_url . "/index.php?aktion=neu2&frame=1";
					
					$text2 = str_replace("%link1%", $link1, $t['neu47']);
					$text2 = str_replace("%link2%", $link2, $text2);
					$text2 = str_replace("%hash%", $hash, $text2);
					$email = urldecode($email);
					$text2 = str_replace("%email%", $email, $text2);
					
					$mailbetreff = $t['neu46'];
					$mailempfaenger = $anmeldung_nurmitbest;
					
					echo $t['neu48'];
				} else {
					// Normale Anmeldung
					$link = $chat_url . $_SERVER['PHP_SELF'] . "?aktion=neu&email=$email&hash=$hash&frame=1";
					$link2 = $chat_url . $_SERVER['PHP_SELF'] . "?aktion=neu2&frame=1";
					
					$text2 = str_replace("%link%", $link, $t['neu36']);
					$text2 = str_replace("%link2%", $link2, $text2);
					$text2 = str_replace("%hash%", $hash, $text2);
					$email = urldecode($email);
					$text2 = str_replace("%email%", $email, $text2);
					
					$mailbetreff = $t['neu38'];
					$mailempfaenger = $email;
					
					echo $t['neu37'];
				}
				
				$header = "\n" . "X-MC-IP: " . $_SERVER["REMOTE_ADDR"] . "\n" . "X-MC-TS: " . time();
				$header .= "Mime-Version: 1.0\r\n";
				$header .= "Content-type: text/plain; charset=utf-8";
				
				// E-Mail versenden
				if($smtp_on) {
					mailsmtp($mailempfaenger, $mailbetreff, $text2, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
				} else {
					mail($mailempfaenger, $mailbetreff, $text2, "From: $webmaster ($chat)" . $header);
				}
				
				$email = mysqli_real_escape_string($mysqli_link, $email);
				$query = "REPLACE INTO mail_check (email,datum) VALUES ('$email',NOW())";
				$result = mysqli_query($mysqli_link, $query);
				
			}
		}
		
		zeige_fuss();
		break;
	
	case "abweisen":
	// Header ausgeben
		zeige_header_ende();
		?>
		<body>
		<?php
		zeige_kopf();
		
		echo $t['login4'];
		
		zeige_fuss();
		
		break;
	
	case "gesperrt":
	// Header ausgeben
		zeige_header_ende();
		?>
		<body>
		<?php
		zeige_kopf();
		
		if ($chat_offline_kunde) {
			echo $chat_offline_kunde_txt;
		} else {
			echo $chat_offline;
		}
		
		zeige_fuss();
		
		break;
	
	case "login":
	// Sequence für online- und chat-Tabelle erzeugen
		erzeuge_sequence("online", "o_id");
		erzeuge_sequence("chat", "c_id");
		
		// Weiter mit login
		if (!isset($passwort) || $passwort == "") {
			// Login als Gast
			
			// Falls Gast-Login erlaubt ist:
			if ($gast_login) {
				
				// Prüfen, ob von der IP und dem Benutzer-Agent schon ein Gast online ist und ggf abweisen
				$query4711 = "SELECT o_id FROM online "
					. "WHERE o_browser='" . $_SERVER["HTTP_USER_AGENT"]
					. "' " . "AND o_ip='" . $_SERVER["REMOTE_ADDR"] . "' "
					. "AND o_level='G' "
					. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
				$result = mysqli_query($mysqli_link, $query4711);
				if ($result)
					$rows = mysqli_num_rows($result);
				mysqli_free_result($result);
				
			}
			
			// Login abweisen, falls mehr als ein Gast online ist oder Gäste gesperrt sind
			if (!$gast_login || ($rows > 1 && !$gast_login_viele) || $temp_gast_sperre) {
				
				// Gäste sind gesperrt
				
				// Header ausgeben
				zeige_header_ende();
				?>
				<body>
				<?php
				zeige_kopf();
				
				// Passenden Fehlertext ausgeben
				if (!$gast_login) {
					echo $t['login16'];
				} else {
					echo $t['login15'];
				}
				
				// Box für Login
				?>
				<form action="index.php" target="_top" name="form1" method="post">
				<?php
				
				// Disclaimer ausgeben
				show_box($login_titel, $logintext, "100%");
				echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n";
				echo "</form>";
				zeige_fuss();
				exit;
			}
			
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
						$login = $t['login13'] . strval((mt_rand(1, 10000)) + 1);
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
			$f['u_loginfehler'] = '';
			
			// Prüfung, ob dieser Benutzer bereits existiert
			$query4711 = "SELECT `u_id` FROM `user` WHERE `u_nick`='$f[u_nick]'";
			$result = mysqli_query($mysqli_link, $query4711);
			
			if (mysqli_num_rows($result) == 0) {
				// Account in DB schreiben
				schreibe_db("user", $f, "", "u_id");
			}
		}
		
		// Login als registrierter Benutzer
		
		// Testen, ob frühere Loginversuche fehlschlugen (nur Admins)
		$query4711 = "SELECT u_id,u_nick,u_loginfehler,u_login FROM user "
			. "WHERE (u_nick = '$login') "
			. "AND (u_level='S' OR u_level='C') ";
		$result = mysqli_query($mysqli_link, $query4711);
		if ($result) {
			$rows = mysqli_num_rows($result);
		}
		
		// Voreinstellung: Weiter mit Login
		$login_ok = true;
		
		if ($result && $rows == 1) {
			$userdata = mysqli_fetch_object($result);
			if ($userdata->u_loginfehler) {
				
				// Nach zwei fehlgeschlagenen Logins und dem letzen Versuch keine 5 min vergangen 
				// -> Login sperren und merken
				$u_loginfehler = unserialize($userdata->u_loginfehler);
				$letzter_eintrag = end($u_loginfehler);
				if ((time() - $letzter_eintrag[login]) < 300
					&& count($u_loginfehler) > 9) {
					$login_ok = false;
				}
			}
		}
		
		// Passwort prüfen und Benutzerdaten lesen
		$rows = 0;
		$result = auth_user($login, $passwort);
		if ($result) {
			$rows = mysqli_num_rows($result);
		}
		
		// Login fehlgeschlagen
		if ($rows == 0 && isset($userdata) && is_array($userdata) && $userdata) {
			
			// Fehllogin bei Admin: falsches Passwort oder Benutzername -> max 100 Loginversuche in Benutzerdaten merken
			if ($userdata->u_loginfehler)
				$u_loginfehler = unserialize($userdata->u_loginfehler);
			if (count($u_loginfehler) < 100) {
				unset($f);
				unset($temp);
				$f['nick'] = substr($userdata->u_nick, 0, 20);
				$f['pw'] = substr($passwort, 0, 20);
				$f['login'] = time();
				$f['ip'] = substr($_SERVER["REMOTE_ADDR"], 0, 20);
				$u_loginfehler[] = $f;
				$temp['u_loginfehler'] = serialize($u_loginfehler);
				$temp['u_login'] = $userdata->u_login;
				schreibe_db("user", $temp, $userdata->u_id, "u_id");
			}
		}
		
		// güliger Account gefunden, weiter mit Login oder Fehlermeldungen ausgeben
		if ($result && $rows == 1 && $login_ok) {
			
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
				$query = "SELECT count(o_id) FROM online "
					. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
				$result2 = mysqli_query($mysqli_link, $query);
				if ($result2)
					$onlineanzahl = mysqli_result($result2, 0, 0);
				mysqli_free_result($result2);
			}
			
			// Login erfolgreich ?
			if ($u_level == "Z") {
				
				// Benutzer gesperrt -> Fehlermeldung ausgeben
				
				// Header ausgeben
				zeige_header_ende();
				?>
				<body>
				<?php
				zeige_kopf();
				
				echo str_replace("%u_nick%", $u_nick, $t['login5']);
				
				zeige_fuss();
				
			} else if (false && $HTTP_COOKIE_VARS[MAINCHAT2] != "on" && ($u_level == "C" || $u_level == "S")) {
				
				// Der Benutzer ein Admin und es sind cookies gesetzt -> Fehlermeldung ausgeben
				
				// Header ausgeben
				zeige_header_ende();
				?>
				<body>
				<?php
				zeige_kopf();
				
				echo str_replace("%url%", $chat_url, $t['login25']);
				unset($u_nick);
				
				zeige_fuss();
				
			} else if ($chat_max[$u_level] != 0 && $onlineanzahl > $chat_max[$u_level]) {
				
				// Maximale Anzahl der Benutzer im Chat erreicht -> Fehlermeldung ausgeben
				
				// Header ausgeben
				zeige_header_ende();
				?>
				<body>
				<?php
				zeige_kopf();
				
				$txt = str_replace("%online%", $onlineanzahl, $t['login24']);
				$txt = str_replace("%max%", $chat_max[$u_level], $txt);
				$txt = str_replace("%leveltxt%", $level[$u_level], $txt);
				$txt = str_replace("%zusatztext%", $chat_max[zusatztext],
					$txt);
				echo $txt;
				
				unset($u_nick);
				
				// Box für Login
				?>
				<form action="index.php" target="_top" name="form1" method="post">
				<?php
				
				$titel = $login_titel;
				$titel .= "[<a href=\"" . $_SERVER['PHP_SELF'] . "?aktion=passwort_neu\">" . $t['login27'] . "</a>]";
				
				// Box und Disclaimer ausgeben
				show_box($titel, $logintext, "100%");
				echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n";
				echo "</form>";
				zeige_fuss();
				
			} else {
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
				if ( ($captcha_text == 1) && (isset($captcha_text2)) && ( $captcha_text2 != md5( $u_id . "+code+" . $captcha_text1 . "+" . date("Y-m-d h") ) )) {
					$los = "";
				}
				
				// muss Benutzer/Gast  noch Nutzungsbestimmungen bestätigen?
				if ($los != $t['login17'] && $u_agb != "Y") {
					
					// Nutzungsbestimmungen ausgeben
					
					// ggf. Nutzungsbestimmungen aus Sprachdatei mit extra Nutzungsbestimmungen aus config.php überschreiben
					if ((isset($extra_agb)) and ($extra_agb <> "")
						and ($extra_agb <> "standard"))
						$t['agb'] = $extra_agb;
					
					// Header ausgeben
					zeige_header_ende();
					?>
					<body>
					<?php
					zeige_kopf();
					
					// Box für Login
					?>
					<form action="index.php" target="_top" name="form1" method="post">
					<?php
					
					if (!isset($eintritt))
						$eintritt = RaumNameToRaumID($lobby);
					
					if ($captcha_text == 1) {
						?>
						<table class="tabelle_kopf">
							<tr>
								<td colspan="2" class="tabelle_kopfzeile"><?php echo $t['willkommen3']; ?></td>
							</tr>
							<tr>
								<td colspan="2" class="tabelle_koerper_login"><?php echo $t['agb']; ?></td>
							</tr>
							<tr>
								<td colspan="2" class="tabelle_koerper_login"><?php echo $t['captcha1']; ?><br>
							<?php
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
							
							echo $tzahl[$zahl1] . " " . $taufgabe[$aufgabe]
								. " " . $tzahl[$zahl2]
								. " &nbsp;&nbsp;&nbsp;&nbsp;";
							
								//echo md5( $u_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") );
								//echo md5( $u_id . "code" . $ergebnis . "+" . date("Y-m-d h") );
								?>
									<input type="text" name="captcha_text1">
									<input type="hidden" name="captcha_text2" value="<?php echo md5( $u_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") ); ?>">
									<input type="hidden" name="ergebnis" value="<?php echo $ergebnis; ?>">
								</td>
							</tr>
							<tr>
								<td align="left" class="tabelle_koerper_login"><?php echo $f1; ?><b><input type="submit" name="los" value="<?php echo $t['login17']; ?>"></b></td>
								<td align="right" class="tabelle_koerper_login"><?php echo $f1; ?><input type="submit" name="los" value="<?php echo $t['login18']; ?>"></td>
							</tr>
						</table>
						<?php echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n"; ?>
						<input type="hidden" name="login" value="<?php echo $login; ?>">
						<input type="hidden" name="passwort" value="<?php echo $passwort; ?>">
						<input type="hidden" name="eintritt" value="<?php echo $eintritt; ?>">
						<input type="hidden" name="aktion" value="login">
						</form>
						<?php
					} else {
						?>
						<table class="tabelle_kopf">
							<tr>
								<td colspan="2" class="tabelle_kopfzeile"><?php echo $t['willkommen3']; ?></td>
							</tr>
							<tr>
								<td colspan="2" class="tabelle_koerper_login"><?php echo $t['agb']; ?></td>
							</tr>
							<tr>
								<td align="left" class="tabelle_koerper_login"><?php echo $f1; ?><b><input type="submit" name="los" value="<?php echo $t['login17']; ?>"></b></td>
								<td align="right" class="tabelle_koerper_login"><?php echo $f1; ?><input type="submit" name="los" value="<?php echo $t['login18']; ?>"></td>
							</tr>
						</table>
						<?php echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n"; ?>
						<input type="hidden" name="login" value="<?php echo $login; ?>">
						<input type="hidden" name="passwort" value="<?php echo $passwort; ?>">
						<input type="hidden" name="eintritt" value="<?php echo $eintritt; ?>">
						<input type="hidden" name="aktion" value="login">
						</form>
						<?php
					}
					
					zeige_fuss();
					exit;
					
				} else if ($los == $t['login17']) {
					// Nutzungsbestimmungen wurden bestätigt
					$u_agb = "Y";
				} else {
					$u_agb = "";
				}
				
				// Benutzer in Blacklist überprüfen
				$query2 = "SELECT f_text FROM blacklist WHERE f_blacklistid=$u_id";
				$result2 = mysqli_query($mysqli_link, $query2);
				if ($result2 AND mysqli_num_rows($result2) > 0) {
					$infotext = "Blacklist: "
						. mysqli_result($result2, 0, 0);
					$warnung = TRUE;
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
							$raumname = " (" . mysqli_result($result2, 0, 0)
								. ") ";
						} else {
							$raumname = "";
						}
						mysqli_free_result($result2);
					}
					
					$query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
					$result2 = mysqli_query($mysqli_link, $query2);
					if ($result2 AND mysqli_num_rows($result2) > 0) {
						$txt = str_replace("%ip_adr%", $ip_adr,
							$t['default6']);
						$txt = str_replace("%ip_name%", $ip_name, $txt);
						$txt = str_replace("%is_infotext%", $infotext, $txt);
						while ($row2 = mysqli_fetch_object($result2)) {
							$ur1 = "user.php?id=<ID>&aktion=zeig&user=$u_id";
							$ah1 = "<a href=\"$ur1\" target=\"$u_nick\" onclick=\"neuesFenster('$ur1','$u_nick'); return(false);\">";
							$ah2 = "</A>";
							system_msg("", 0, $row2->o_user, $system_farbe,
								str_replace("%u_nick%",
									$ah1 . $u_nick . $ah2 . $raumname,
									$txt));
						}
					}
					mysqli_free_result($result2);
				}
				
				// Benutzer nicht gesperrt, weiter mit Login und Eintritt in ausgewählten Raum mit ID $eintritt
				
				// Hash-Wert ermitteln
				$hash_id = id_erzeuge($u_id);
				
				// $javascript wird als input type hidden übergeben...
				if (isset($javascript) && $javascript == "on") {
					$javascript = 1;
				} else {
					$javascript = 0;
				}
				
				// Login
				$o_id = login($u_id, $u_nick, $u_level, $hash_id,
					$javascript, $ip_historie, $u_agb, $u_punkte_monat,
					$u_punkte_jahr, $u_punkte_datum_monat,
					$u_punkte_datum_jahr, $u_punkte_gesamt);
				
				// Beichtstuhl-Special
				// Falls es Räume mit nur einem Admin gibt und niemand in der Lobby ist,
				// Eintritt in einen solchen Raum, ansonsten in Lobby
				if (isset($beichtstuhl) && $beichtstuhl) {
					
					$login_in_lobby = FALSE;
					
					// Prüfen, ob ein Benutzer in der Lobby ist
					$query2 = "SELECT o_id "
						. "FROM raum,online WHERE o_raum=r_id "
						. "AND r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' "
						. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
					$result2 = mysqli_query($mysqli_link, $query2);
					
					if ($result2 && mysqli_num_rows($result2) > 0) {
						
						// Mehr als 0 Benutzer in Lobby
						mysqli_free_result($result2);
						$login_in_lobby = TRUE;
						
					} else {
						
						// Prüfen, ob es Räume mit genau einem Admin gibt
						mysqli_free_result($result2);
						$query2 = "SELECT r_id,count(o_id) as anzahl, "
							. "count(o_level='C') as CADMIN, count(o_level='S') as SADMIN "
							. "FROM raum,online WHERE o_raum=r_id "
							. "AND r_name!='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' "
							. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
							. "GROUP BY r_id HAVING anzahl=1 AND (CADMIN=1 OR SADMIN=1)";
						$result2 = mysqli_query($mysqli_link, $query2);
						$anzahl = mysqli_num_rows($result2);
						if ($result2 && $anzahl == 1) {
							$eintritt = mysqli_result($result2, 0, "r_id");
						} elseif ($result2 && $anzahl > 1) {
							$eintritt = mysqli_result($result2,
								mt_rand(0, $anzahl - 1), "r_id");
						} else {
							$login_in_lobby = TRUE;
						}
						mysqli_free_result($result2);
						
					}
					
					// ID der Lobby neu ermitteln -> Login in Lobby
					if ($login_in_lobby) {
						$query2 = "SELECT r_id FROM raum WHERE r_name = '" . mysqli_real_escape_string($mysqli_link, $eintrittsraum) . "' ";
						$result2 = mysqli_query($mysqli_link, $query2);
						if ($result2 && mysqli_num_rows($result2) == 1) {
							$eintritt = mysqli_result($result2, 0, "r_id");
						}
						mysqli_free_result($result2);
					}
					
					// Bei Login dieses Benutzers alle Admins (online, nicht Temp) informieren
					$query2 = "SELECT r_name from raum where r_id= '" . mysqli_real_escape_string($mysqli_link, $eintritt) . "'";
					$result2 = mysqli_query($mysqli_link, $query2);
					if ($result2 AND mysqli_num_rows($result2) > 0) {
						$raumname = mysqli_result($result2, 0, 0);
					} else {
						$raumname = "";
					}
					mysqli_free_result($result2);
					
					$query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
					$result2 = mysqli_query($mysqli_link, $query2);
					if ($result2 AND mysqli_num_rows($result2) > 0) {
						$txt = str_replace("%raumname%", $raumname,
							$t['default7']);
						while ($row2 = mysqli_fetch_object($result2)) {
							$ur1 = "user.php?id=<ID>&aktion=zeig&user=$u_id";
							$ah1 = "<a href=\"$ur1\" target=\"$u_nick\" onclick=\"neuesFenster('$ur1','$u_nick'); return(false);\">";
							$ah2 = "</A>";
							system_msg("", 0, $row2->o_user, $system_farbe,
								str_replace("%u_nick%",
									$ah1 . $u_nick . $ah2, $txt));
						}
					}
					mysqli_free_result($result2);
					
				}
				
				zeige_header_ende();
				
				if ($communityfeatures && $eintritt == "forum") {
					
					// Login ins Forum
					betrete_forum($o_id, $u_id, $u_nick, $u_level);
					
					// Obersten Frame definieren
					if (!isset($frame_online)) {
						$frame_online = "frame_online.php";
					}
					
					echo "<frameset rows=\"$frame_online_size,*,5," . $frame_size['interaktivforum'] . ",1\" border=\"0\" frameborder=\"0\" framespacing=\"0\">\n";
					echo "<frame src=\"$frame_online\" name=\"frame_online\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\">\n";
					echo "<frame src=\"forum.php?id=$hash_id\" name=\"forum\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"auto\">\n";
					echo "<frame src=\"leer.php\" name=\"leer\" marginwidth=\"0\" marginheight=\"0\" scrolling=no>\n";
					echo "<frameset cols=\"*," . $frame_size['messagesforum'] . "\" border=\"0\" frameborder=\"0\" framespacing=\"0\">\n";
					echo "<frame src=\"messages-forum.php?id=$hash_id\" name=\"messages\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"auto\">\n";
					echo "<frame src=\"interaktiv-forum.php?id=$hash_id\" name=\"interaktiv\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\">\n";
					echo "</frameset>\n";
					echo "<frame src=\"schreibe.php?id=$hash_id&o_who=2\" name=\"schreibe\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\">\n";
					echo "</frameset>\n";
					echo "<noframes>\n";
					echo $t['login6'];
					die();
				} else {
					// Chat betreten
					betrete_chat($o_id, $u_id, $u_nick, $u_level, $eintritt, $javascript);
					$back = 1;
					
					// Obersten Frame definieren
					if (!isset($frame_online) || $frame_online == "") {
						$frame_online = "frame_online.php";
					}
					if ($u_level == "M") {
						$frame_size['interaktiv'] = $moderationsgroesse;
						$frame_size['eingabe'] = $frame_size['eingabe']
							* 2;
					}
					
					// Frameset aufbauen
					?>
					<frameset rows="<?php echo $frame_online_size;?>,*,<?php echo $frame_size['eingabe']; ?>,<?php echo $frame_size['interaktiv']; ?>,1" border="0" frameborder="0" framespacing="0">
						<frame src="<?php echo $frame_online;?>" name="frame_online" marginwidth="0" marginheight="0" scrolling="no">
						<frameset cols="*,<?php echo $frame_size['chatuserliste'];?>" border="0" frameborder="0" framespacing="0">
							<frame src="chat.php?id=<?php echo $hash_id; ?>&back=<?php echo $back; ?>" name="chat" marginwidth="4" marginheight="0">
							<?php
							if (!isset($userframe_url)) {
								?>
								<frame src="user.php?id=<?php echo $hash_id; ?>&aktion=chatuserliste" marginwidth="4" marginheight="0" name="userliste">
								<?php
							} else {
								?>
								<frame src="<?php echo $userframe_url; ?>" marginwidth="4" marginheight="0" name="userliste">
								<?php
							}
							?>
						</frameset>
						<frame src="eingabe.php?id=<?php echo $hash_id; ?>" name="eingabe" marginwidth="0" marginheight="0" scrolling="no">
						<?php
						if ($u_level == "M") {
							?>
							<frameset cols="*,220" border="0" frameborder="0" framespacing="0">
								<frame src="moderator.php?id=<?php echo $hash_id; ?>" name="moderator" marginwidth="0" marginheight="0" scrolling="auto">
								<frame src="interaktiv.php?id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
							</frameset>
							<?php
						} else {
							?>
							<frame src="interaktiv.php?id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
							<?php
						}
						?>
						<frame src="schreibe.php?id=<?php echo $hash_id; ?>" name="schreibe" marginwidth="0" marginheight="0" scrolling="no">
					</frameset>
					<?php
					echo "<noframes>\n"
						. "<!-- Browser/OS -->\n" . $t['login6'];
				}
				?>
				<body>
				<?php
			}
			
		} elseif (!$login_ok) {
			if ($rows == 0) {
				// Zu viele fehlgeschlagenen Logins, aktueller Login ist
				// wieder fehlgeschlagen, daher Mail an Betreiber verschicken
				if ($userdata->u_loginfehler) {
					$u_loginfehler = unserialize($userdata->u_loginfehler);
				}
				$betreff = str_replace("%login%", $login, $t['login20']);
				$text = str_replace("%login%", $login, $t['login21'])
					. "\n";
				foreach ($u_loginfehler as $key => $val) {
					$text .= "[" . substr("00" . $key, -3) . "] "
						. date("M d Y H:i:s", $val[login])
						. " \tIP: $val[ip] \tPW: $val[pw]\n";
				}
				$text .= "\n-- \n   $chat (".$chat_url . $_SERVER['PHP_SELF'] . " | " . $chat_url . ")\n";
				
				// E-Mail versenden
				if($smtp_on) {
					mailsmtp($webmaster, $betreff, $text, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
				} else {
					mail($webmaster, $betreff, $text, "From: $hackmail\nReply-To: $hackmail\nCC: $hackmail\n");
				}
			}
			
			// Fehlermeldung ausgeben
			
			// Header ausgeben
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			unset($u_nick);
			echo "<div style=\"text-align: center;\"><b>" . str_replace("%login%", $login, $t['login20']) . "</b></div>";
			zeige_fuss();
			
		} else {
			// Login fehlgeschlagen
			
			// Header ausgeben
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			
			unset($u_nick);
			
			if (strlen($passwort) == 0) {
				// Kein Passwort eingegeben oder der Benutzername exitiert bereits
				echo $t['login19'];
			} else {
				// Falsches Passwort oder Benutzername
				echo $t['login7'];
			}
			
			// Box für Login
			?>
			<form action="index.php" target="_top" name="form1" method="post">
			<?php
			
			$titel = $login_titel;
			$titel .= "[<a href=\"" . $_SERVER['PHP_SELF'] . "?aktion=passwort_neu\">" . $t['login27'] . "</a>]";
			
			// Box und Disclaimer ausgeben
			show_box($titel, $logintext, "100%");
			echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n";
			echo "</form>";
			zeige_fuss();
			
		}
		break;
	
	case "neu":
	// Neu anmelden
	
		// Gibt die Kopfzeile im Login aus
		show_kopfzeile_login();
		
		// Im Benutzername alle Sonderzeichen entfernen
		if (isset($f['u_nick'])) {
			$f['u_nick'] = coreCheckName($f['u_nick'], $check_name);
		}
		
		// Eingaben prüfen
		
		if ($los == $t['neu22']) {
			$ok = "1";
			$pos = strpos($f['u_nick'], "+");
			if ($pos === false)
				$pos = -1;
			if ($pos >= 0) {
				echo $t['neu44'];
				$ok = "0";
			}
			
			if (strlen($f['u_nick']) > 20 || strlen($f['u_nick']) < 4) {
				echo str_replace("%zeichen%", $check_name, $t['neu4']);
				$ok = "0";
			}
			if (preg_match("/^" . $t['login13'] . "/i", $f['u_nick'])) {
				echo str_replace("%gast%", $t['login13'], $t['neu32']);
				$ok = "0";
			}
			if (strlen($f['u_passwort']) < 4) {
				echo $t['neu5'];
				$ok = "0";
			}
			if ($f['u_passwort'] != $u_passwort2) {
				echo $t['neu6'];
				$f['u_passwort'] = "";
				$u_passwort2 = "";
				$ok = "0";
			}
			
			if (strlen($f['u_email']) != 0
				&& !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
					$f['u_email'])) {
				echo $t['neu8'];
				$ok = "0";
			}
			if (strlen($f['u_adminemail']) == 0
				|| !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
					$f['u_adminemail'])) {
				echo $t['neu7'];
				$ok = "0";
			}
			
			// Gibt es den Benutzernamen schon?
			$query = "SELECT `u_id` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $f['u_nick']) . "'";
			
			$result = mysqli_query($mysqli_link, $query);
			$rows = mysqli_num_rows($result);
			
			if ($rows != 0) {
				echo $t['neu9'];
				$ok = "0";
			}
			mysqli_free_result($result);
			
		} else {
			$ok = "0";
		}
		
		if (!isset($f['u_nick'])) {
			$f['u_nick'] = "";
		}
		if (!isset($f['u_passwort'])) {
			$f['u_passwort'] = "";
		}
		if (!isset($f['u_email'])) {
			$f['u_email'] = "";
		}
		if (!isset($f['u_url'])) {
			$f['u_url'] = "";
		}
		
		$text = "<table><tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu12']
			. "</td>" . "<td>" . $f1
			. "<input type=\"text\" name=\"f[u_nick]\" value=\"$f[u_nick]\" size=\"40\">"
			. $f2 . "</td>" . "<td>" . $f1 . $t['neu13'] . $f2
			. "</td></tr>" . "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu14']
			. "</td>" . "<td>" . $f1
			. "<input type=\"PASSWORD\" name=\"f[u_passwort]\" value=\"$f[u_passwort]\" size=\"40\">"
			. $f2 . "</td>" . "<td><b>*</b></td></tr>"
			. "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu15'] . "</td>"
			. "<td>"
			. "<input type=\"PASSWORD\" name=\"u_passwort2\" value=\"$f[u_passwort]\" size=\"40\">"
			. $f2 . "</td>" . "<td><b>*</b>" . $f1 . $t['neu16'] . $f2
			. "</td></tr>";
		if (!isset($ro) || $ro == "") {
			$text .= "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu17'] . "</td>"
				. "<td>" . $f1
				. "<input type=\"text\" name=\"f[u_adminemail]\" value=\""
				. (isset($f[u_adminemail]) ? $f[u_adminemail] : "")
				. "\" size=\"40\">" . $f2 . "</td>" . "<td><b>*</b>&nbsp;"
				. $f1 . $t['neu18'] . $f2 . "</td></tr>";
		} else {
			$text .= "<tr><td colspan=3>"
				. "<input type=\"hidden\" name=\"f[u_adminemail]\" value=\"$f[u_adminemail]\"></td></tr>";
		}
		$text .= "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu19'] . "</td>\n"
			. "<td>" . $f1
			. "<input type=\"text\" name=\"f[u_email]\" value=\"$f[u_email]\" size=\"40\">"
			. $f2 . "</td>\n" . "<td>" . $f1 . $t['neu20'] . $f2
			. "</td></tr>\n" . "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu21']
			. "</td>\n" . "<td>" . $f1
			. "<input type=\"text\" name=\"f[u_url]\" value=\"$f[u_url]\" size=\"40\">"
			. $f2 . "</td>\n" . "<td>" . $f1
			. "<input type=\"hidden\" name=\"aktion\" value=\"neu\">\n"
			. "<input type=\"hidden\" name=\"hash\" value=\""
			. (isset($hash) ? $hash : "") . "\">\n";
		$text .= "<b><input type=\"submit\" name=\"los\" value=\""
			. $t['neu22'] . "\"></b>" . $f2 . "</td>\n" . "</tr></table>";
		if ($los != $t['neu22'])
			echo $t['neu23'];
		
		// Prüfe ob Mailadresse schon zu oft registriert, durch ZURÜCK Button bei der 1. registrierung
		if ($ok) {
			$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
			$result = mysqli_query($mysqli_link, $query);
			$num = mysqli_num_rows($result);
			// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
			if ($num > 0) {
				$ok = 0;
				echo $t['neu55'] . "<br><br>";
				zeige_fuss();
				break;
			}
		}
		
		if ($ok) {
			echo ($t['neu24']);
		}
		
		if ((!$ok && $los == $t['neu22']) || ($los != $t['neu22'])) {
			?>
			<form action="index.php" name="form1" method="post">
				<?php
				$titel = $t['neu31'];
				show_box($titel, $text, "");
				?>
			</form>
			<?php
		}
		
		if ($ok && $los == $t['neu22']) {
			// Daten in DB als Benutzer eintragen
			$text = $t['neu25'] . "<table><tr><td style=\"text-align: right; font-weight:bold;\">"
				. "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu12'] . "</td>"
				. "<td>" . $f1 . $f['u_nick'] . $f2
				. "</td></tr></table>\n" . $t['neu28']
				. "<form action=\"$chat_url\" name=\"login\" method=\"post\">\n"
				. "<input type=\"hidden\" name=\"login\" value=\"$f[u_nick]\">\n"
				. "<input type=\"hidden\" name=\"passwort\" value=\"$f[u_passwort]\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"login\">\n"
				. "<b><input type=\"submit\" value=\"" . $t['neu29']
				. "\"></b>\n"
				. "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n// end hiding -->\n</script>\n"
				. "</form>\n";
			$titel = $t['neu30'];

			show_box($titel, $text, "");
			echo "<br>";
			
			// Homepage muss http:// enthalten
			if (!preg_match("|^(http://)|i", $f['u_url'])
				&& strlen($f['u_url']) > 0) {
				$f['u_url'] = "http://" . $f['u_url'];
			}
			
			$f['u_level'] = "U";
			$f['u_loginfehler'] = "";

			$u_id = schreibe_db("user", $f, "", "u_id");
			$result = mysqli_query($mysqli_link, "UPDATE user SET u_neu=DATE_FORMAT(now(),\"%Y%m%d%H%i%s\") WHERE u_id=$u_id");
			
			if ($pruefe_email == "1") {
				$query = "DELETE FROM mail_check WHERE email = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
				$result = mysqli_query($mysqli_link, $query);
			}
			
		}
		
		zeige_fuss();
		
		break;
	
	case "relogin":
	// Login aus Forum in Chat; Benutzerdaten setzen
		id_lese($id);
		$hash_id = $id;
		
		//system_msg("",0,$u_id,"","DEBUG: $neuer_raum ");
		
		// Chat betreten
		$back = betrete_chat($o_id, $u_id, $u_nick, $u_level, $neuer_raum, $o_js);
		
		// Obersten Frame definieren
		if (!isset($frame_online)) {
			$frame_online = "";
		}
		if (strlen($frame_online) == 0) {
			$frame_online = "frame_online.php";
		}
		if ($u_level == "M") {
			$frame_size[interaktiv] = $moderationsgroesse;
		}
		
		// Frameset aufbauen
		?>
		<frameset rows="<?php echo $frame_online_size;?>,*,<?php echo $frame_size['eingabe']; ?>,<?php echo $frame_size['interaktiv']; ?>,1" border="0" frameborder="0" framespacing="0">
			<frame src="<?php echo $frame_online;?>" name="frame_online" marginwidth="0" marginheight="0" scrolling="no">
			<frameset cols="*,<?php echo $frame_size['chatuserliste'];?>" border="0" frameborder="0" framespacing="0">
				<frame src="chat.php?id=<?php echo $hash_id; ?>&back=<?php echo $back; ?>" name="chat" marginwidth="4" marginheight="0">
				<frame src="user.php?id=<?php echo $hash_id; ?>&aktion=chatuserliste" marginwidth="4" marginheight="0" name="userliste">
			</frameset>
			<frame src="eingabe.php?id=<?php echo $hash_id; ?>" name="eingabe" marginwidth="0" marginheight="0" scrolling="no">
			<?php
			if ($u_level == "M") {
				?>
				<frameset cols="*,220" border="0" frameborder="0" framespacing="0">
				<frame src="moderator.php?id=<?php echo $hash_id; ?>" name="moderator" marginwidth="0" marginheight="0" scrolling="auto">
				<frame src="interaktiv.php?id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
				</frameset>
				<?php
			} else {
				?>
				<frame src="interaktiv.php?id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
				<?php
			}
			?>
			<frame src="schreibe.php?id=<?php echo $hash_id; ?>" name="schreibe" marginwidth="0" marginheight="0" scrolling="no">
		</frameset>
		<?php
		echo "<noframes>\n"
			. "<!-- Browser/OS -->\n" . $t['login6'];
		
		break;
	
	default:
		// Homepage des Chats ausgeben
		
		// Gibt die Kopfzeile im Login aus
		show_kopfzeile_login();
		
		if ($zusatznachricht) {
			?>
			<p><span style="font-size: x-large;"><?php echo $zusatznachricht; ?></span></p>
			<?php
		}

		// Box für Login
		?>
		<form action="index.php" target="_top" name="form1" method="post">
		<?php
		
		$titel = $login_titel;
		$titel .= "[<a href=\"" . $_SERVER['PHP_SELF'] . "?aktion=passwort_neu\">" . $t['login27'] . "</a>]";
		
		// Box und Disclaimer ausgeben
		show_box($titel, $logintext, "100%");
		echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n";
		echo "</form>";
		
		if (!isset($beichtstuhl) || !$beichtstuhl) {
			// Wie viele Benutzer sind in der DB?
			$query = "SELECT COUNT(u_id) FROM `user` WHERE `u_level` IN ('A','C','G','M','S','U')";
			$result = mysqli_query($mysqli_link, $query);
			$rows = mysqli_num_rows($result);
			if ($result) {
				$useranzahl = mysqli_result($result, 0, 0);
				mysqli_free_result($result);
			}
			
			// Benutzer online und Räume bestimmen -> merken
			$query = "SELECT o_who,o_name,o_level,r_name,r_status1,r_status2, "
				. "r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' as lobby "
				. "FROM online left join raum on o_raum=r_id  "
				. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
				. "ORDER BY lobby desc,r_name,o_who,o_name ";
			$result2 = mysqli_query($mysqli_link, $query);
			if ($result2)
				$onlineanzahl = mysqli_num_rows($result2);
			// Anzahl der angemeldeten Benutzer ausgeben
			if ($onlineanzahl > 1 && $useranzahl > 1) {
				$text = str_replace("%onlineanzahl%", $onlineanzahl,
					$t['default2'])
					. str_replace("%useranzahl%", $useranzahl,
						$t['default3']);
				
				// Anzahl der Beiträge im Forum ausgeben
				if ($communityfeatures && $forumfeatures) {
					// Anzahl Themen
					$query = "select count(th_id) from thema";
					$result = mysqli_query($mysqli_link, $query);
					if ($result AND mysqli_num_rows($result) > 0) {
						$themen = mysqli_result($result, 0, 0);
						mysqli_free_result($result);
					}
					
					// Dummy Themen abziehen
					$query = "SELECT count(th_id) FROM thema WHERE th_name = 'dummy-thema'";
					$result = mysqli_query($mysqli_link, $query);
					if ($result AND mysqli_num_rows($result) > 0) {
						$themen = $themen - mysqli_result($result, 0, 0);
						mysqli_free_result($result);
					}
					
					// Anzahl Beiträge 
					$query = "select count(po_id) from posting";
					$result = mysqli_query($mysqli_link, $query);
					if ($result AND mysqli_num_rows($result) > 0) {
						$beitraege = mysqli_result($result, 0, 0);
						mysqli_free_result($result);
					}
					if ($beitraege && $themen)
						$text .= str_replace("%themen%", $themen,
							str_replace("%beitraege%", $beitraege,
								$t['default8']));
				}
				
				show_box_title_content(str_replace("%chat%", $chat, $t['default5']), $text);
			}
			
			if (!isset($unterdruecke_raeume))
				$unterdruecke_raeume = 0;
			if (!$unterdruecke_raeume && $abweisen == false) {
				
				// Wer ist online? Boxen mit Benutzern erzeugen, Topic ist Raumname
				if ($onlineanzahl) {
					show_who_is_online($result2);
				}
			}
			// result wird in "show_who_is_online" freigegeben
			// mysqli_free_result($result2);
		}
		
		// Fuss
		zeige_fuss();
}
?>
</body>
</html>