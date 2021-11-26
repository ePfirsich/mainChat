<?php
if (!isset($_SERVER["HTTP_REFERER"])) {
	$_SERVER["HTTP_REFERER"] = "";
}

// Funktionen und Config laden, Host bestimmen
require_once("functions/functions.php");
require_once("functions/functions-init.php");
require_once("languages/$sprache-index.php");

zeige_header_anfang($body_titel, 'login', $zusatztext_kopf);

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
if ($forumfeatures) {
	$raeume = "<td><b>" . $t['login22'] . "</b><br>";
} else {
	$raeume = "<td><b>" . $t['login12'] . "</b><br>";
}
$raeume .= $f1 . "<select name=\"eintritt\">";

if ($forumfeatures) {
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
if ($forumfeatures)
	$raeume = $raeume
		. "<option value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
$raeume = $raeume . "</select>" . $f2 . "</td>\n";
mysqli_free_result($result);

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
	
	if ($loginwhileipsperre <> 0) {
		// Test auf Punkte 
		$query = "SELECT `u_id`, `u_nick`, `u_level`, `u_punkte_gesamt` FROM `user` "
			. "WHERE `u_nick`='" . mysqli_real_escape_string($mysqli_link, coreCheckName($login, $check_name))
			. "' AND (`u_level` IN ('A','C','G','M','S','U')) ";
		
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
				$ur1 = "inhalt.php?seite=benutzer&id=<ID>&aktion=benutzer_zeig&user=$t_u_id";
				$ah1 = "<a href=\"$ur1\" target=\"chat\">";
				$ah2 = "</a>";
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
		// Aus dem Chat ausloggen
		ausloggen($u_id, $u_nick, $o_raum, $o_id);
	}
}

// Falls in Loginmaske/Nutzungsbestimmungen auf Abbruch geklickt wurde
if ($los == $t['login18'] && $aktion == "login")
	$aktion = "";

// Titeltext der Loginbox setzen, Link auf Registrierung optional ausgeben

if ($neuregistrierung_deaktivieren) {
	$login_titel = $t['default1'];
	if ($aktion == "neu") {
		$aktion = "";
	}
	if ($aktion == "neu2") {
		$aktion = "";
	}
	if ($aktion == "mailcheckm") {
		$aktion = "";
	}
} else if ($t['login14']) {
	$login_titel = $t['default1'] . " [<a href=\"index.php?aktion=neu\">" . $t['login14'] . "</a>]";
} else {
	$login_titel = $t['default1'];
}

if ($aktion == "neu" && isset($f['u_adminemail']) && $hash != md5($f['u_adminemail'] . "+" . date("Y-m-d"))) {
		zeige_header_ende();
		?>
		<body>
		<?php
	zeige_kopf();
	echo "<p><b>Fehler:</b> Die URL ist nicht korrekt! Bitte melden Sie sich <a href=\"" . $chat_url . "/index.php\">hier</a> neu an.</p>";
	
	zeige_fuss();
	exit;
}

if ($aktion == "neu" && $los != $t['neu22']) {
	$aktion = "mailcheck";
}
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
		$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
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
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		// Impressumg anzeigen
		require_once('templates/impressum.php');
		
		zeige_fuss();
		
		break;
		
	case "datenschutz":
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		// Datenschutz anzeigen
		require_once('templates/datenschutz.php');
		
		zeige_fuss();
		
		break;
		
	case "chatiquette":
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		// Datenschutz anzeigen
		require_once('templates/chatiquette.php');
		
		zeige_fuss();
		
		break;
		
	case "nutzungsbestimmungen":
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		// Datenschutz anzeigen
		require_once('templates/nutzungsbestimmungen.php');
		
		zeige_fuss();
		
		break;
		
	case "passwort_neu":
		// Neues Passwort anfordern
		require_once('templates/passwort-neu.php');
		
		break;
	
	case "neubestaetigen":
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
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
					$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
					$hash2 = md5($a['email'] . "+" . $a['datum']);
					if ($hash == $hash2) {
						$link = $chat_url . "/index.php?aktion=neu2";
						
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
							// Der PHP-Vessand benötigt \n und nicht <br>
							$text2 = str_replace("<br>", "\n", $text2);
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
		zeige_kopfzeile_login();
		
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
		zeige_kopfzeile_login();
		
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
			
			zeige_tabelle_variable_breite($box,$text);
		} else if (!isset($email)) {
			// Formular für die Erstregistierung ausgeben, 1. Schritt
			echo "<p>" . $t['neu33'];
			if (isset($anmeldung_nurmitbest) && strlen($anmeldung_nurmitbest) > 0) { // Anmeldung mit Externer Bestätigung
				echo $t['neu45'];
			}
			
			$box = $t['neu31'];
			$text = '';
			$text .= "<form action=\"index.php\">";
			$text .= $t['neu34'] . " <input name=\"email\" maxlength=\"80\">";
			$text .= "<input type=\"hidden\" name=\"aktion\" value=\"mailcheck\">";
			$text .= "<input type=\"submit\" value=\"". $t['neu35'] . "\">";
			$text .= "</form>";
			
			zeige_tabelle_variable_breite($box,$text);
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
					$link1 = $chat_url . "/index.php?aktion=neubestaetigen";
					$link2 = $chat_url . "/index.php?aktion=neu2";
					
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
					$link = $chat_url . $_SERVER['PHP_SELF'] . "?aktion=neu&email=$email&hash=$hash";
					$link2 = $chat_url . $_SERVER['PHP_SELF'] . "?aktion=neu2";
					
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
					// Der PHP-Vessand benötigt \n und nicht <br>
					$text = str_replace("<br>", "\n", $text);
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
		// In den Chat einloggen
		require_once('templates/einloggen.php');
		
		break;
	
	case "neu":
		// Registrierung
		require_once('templates/registrierung.php');
		
		break;
	
	case "relogin":
	// Login aus dem Forum in den Chat; Benutzerdaten setzen
		id_lese($id);
		$hash_id = $id;
		
		// Chat betreten
		$back = betrete_chat($o_id, $u_id, $u_nick, $u_level, $neuer_raum, $o_js);
		
		require_once("functions/functions-frameset.php");
		
		frameset_chat($hash_id);
		
		break;
	
	default:
		// Homepage des Chats ausgeben
		require_once('templates/login.php');
}
?>
</body>
</html>