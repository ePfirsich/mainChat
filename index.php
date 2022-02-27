<?php
// Funktionen und Config laden, Host bestimmen
require_once("functions/functions.php");
require_once("functions/functions-index.php");
require_once("languages/$sprache-index.php");

$bereich = filter_input(INPUT_GET, 'bereich', FILTER_SANITIZE_URL);

$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
if( $aktion == '') {
	$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
}

$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_STRING);
if($email == "") {
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
}
$hash = filter_input(INPUT_GET, 'hash', FILTER_SANITIZE_STRING);

$uebergabe = filter_input(INPUT_POST, 'uebergabe', FILTER_SANITIZE_STRING);
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$passwort = filter_input(INPUT_POST, 'passwort', FILTER_SANITIZE_STRING);
$angemeldet_bleiben = filter_input(INPUT_POST, 'angemeldet_bleiben', FILTER_SANITIZE_NUMBER_INT);
$bereits_angemeldet = filter_input(INPUT_POST, 'bereits_angemeldet', FILTER_SANITIZE_NUMBER_INT);
$formular = filter_input(INPUT_POST, 'formular', FILTER_SANITIZE_NUMBER_INT);

$o_raum_alt = filter_input(INPUT_POST, 'o_raum_alt', FILTER_SANITIZE_NUMBER_INT);
$neuer_raum = filter_input(INPUT_POST, 'neuer_raum', FILTER_SANITIZE_NUMBER_INT);

$captcha_text1 = filter_input(INPUT_POST, 'captcha_text1', FILTER_SANITIZE_STRING);
$captcha_text2 = filter_input(INPUT_POST, 'captcha_text2', FILTER_SANITIZE_STRING);
$ergebnis = filter_input(INPUT_POST, 'ergebnis', FILTER_SANITIZE_STRING);
$eintritt = filter_input(INPUT_POST, 'eintritt', FILTER_SANITIZE_STRING);

// Einloggen
if($aktion == "einloggen") {
	$bereich = "einloggen";
}

if($aktion == "relogin") {
	$bereich = "relogin";
}

// IP bestimmen und prüfen. Ist Login erlaubt?
$abweisen = false;
$warnung = false;
$remote_addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
$ip_name = @gethostbyaddr($remote_addr);

// Sperrt den Chat, wenn in der Sperre Domain "-GLOBAL-" ist
$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GLOBAL-'";
$result = sqlQuery($query);
if ($result && mysqli_num_rows($result) > 0) {
	$abweisen = true;
}
mysqli_free_result($result);

// Gastsperre aktiv? Wird beim Login und beim Nutzungsbestimmungen Login ausgewertet
$temp_gast_sperre = false;
// Wenn in Sperre = "-GAST-" dann Gastlogin gesperrt
$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GAST-'";
$result = sqlQuery($query);
if ($result && mysqli_num_rows($result) > 0) {
	$temp_gast_sperre = true;
}
mysqli_free_result($result);

$query = "SELECT * FROM ip_sperre WHERE (SUBSTRING_INDEX(is_ip,'.',is_ip_byte) LIKE SUBSTRING_INDEX('" . escape_string($remote_addr) . "','.',is_ip_byte) AND is_ip IS NOT NULL) "
	. "OR (is_domain LIKE RIGHT('" . escape_string($ip_name) . "',LENGTH(is_domain)) AND LENGTH(is_domain)>0)";
$result = sqlQuery($query);
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
$http_x_forwarded_for = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP);

if (!$abweisen && $http_x_forwarded_for != $remote_addr) {
	$ip_name = @gethostbyaddr($http_x_forwarded_for);
	$query = "SELECT * FROM ip_sperre WHERE (SUBSTRING_INDEX(is_ip,'.',is_ip_byte) "
		. " LIKE SUBSTRING_INDEX('" . escape_string($http_x_forwarded_for) . "','.',is_ip_byte) AND is_ip IS NOT NULL) "
		. "OR (is_domain LIKE RIGHT('" . escape_string($ip_name) . "',LENGTH(is_domain)) AND LENGTH(is_domain)>0)";
	$result = sqlQuery($query);
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
$result = sqlQuery($query);
if ($result && mysqli_num_rows($result) > 0) {
	while ($row = mysqli_fetch_object($result)) {
		$part = explode("*", $row->is_domain, 2);
		if ((strlen($part[0]) > 0) && (strlen($part[1]) > 0)) {
			if (substr($ip_name, 0, strlen($part[0])) == $part[0] && substr($ip_name, strlen($ip_name) - strlen($part[1]), strlen($part[1])) == $part[1]) {
				
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
if ($abweisen && $bereich != "relogin" && strlen($username) > 0) {
	$query = "SELECT `u_nick`, `u_level` FROM `user` WHERE `u_nick`='" . escape_string(coreCheckName($username, $check_name)) . "' AND (u_level in ('S','C'))";
	$r = sqlQuery($query);
	$rw = mysqli_num_rows($r);
	
	if ($rw == 1 && (strlen($bereich) > 0)) {
		$abweisen = false;
	}
	mysqli_free_result($r);
	
	// Prüfung nun auf Admin beendet
	// Nun Prüfung ob genug Punkte
	$durchgangwegenpunkte = false;
	
	if ($loginwhileipsperre <> 0) {
		// Test auf Punkte 
		$query = "SELECT `u_id`, `u_nick`, `u_level`, `u_punkte_gesamt` FROM `user` WHERE `u_nick`='" . escape_string(coreCheckName($username, $check_name)) . "' AND (`u_level` IN ('A','C','G','M','S','U')) ";
		
		// Durchleitung wg. Punkten im Fall der MD5() verschlüsselung wird nicht gehen
		$r = sqlQuery($query);
		$rw = mysqli_num_rows($r);
		
		if ($rw == 1 && strlen($bereich) > 0) {
			$row = mysqli_fetch_object($r);
			if ($row->u_punkte_gesamt >= $loginwhileipsperre) {
				// genügend Punkte
				// Login zulassen
				$durchgangwegenpunkte = true;
				$abweisen = false;
				$t_u_id = $row->u_id;
				$t_u_nick = $row->u_nick;
				$infotext = str_replace("%punkte%", $row->u_punkte_gesamt, $lang['ipsperre2']);
			}
		}
		mysqli_free_result($r);
	}
	
	if ($durchgangwegenpunkte) {
		// Wenn Benutzer wegen Punkte durch IP Sperre kommen, dann Meldung an alle Admins
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
		
		$query2 = "SELECT o_user FROM online WHERE o_level='S' OR o_level='C'";
		$result2 = sqlQuery($query2);
		if ($result2 AND mysqli_num_rows($result2) > 0) {
			$txt = str_replace("%ip_adr%", $http_x_forwarded_for, $lang['ipsperre1']);
			$txt = str_replace("%ip_name%", $ip_name, $txt);
			$txt = str_replace("%is_infotext%", $infotext, $txt);
			while ($row2 = mysqli_fetch_object($result2)) {
				$ah1 = "<a href=\"inhalt.php?bereich=benutzer&aktion=benutzer_zeig&ui_id=$t_u_id\" target=\"chat\">";
				$ah2 = "</a>";
				system_msg("", 0, $row2->o_user, $system_farbe, str_replace("%u_nick%", $ah1 . $t_u_nick . $ah2 . $raumname, $txt));
			}
			unset($infotext);
			unset($t_u_id);
			unset($u_nick);
		}
		mysqli_free_result($result2);
	}
}

$text = "";

if($abweisen && (strlen($bereich) > 0) && $bereich <> "relogin") {
	$fehlermeldung = $lang['login_fehlermeldung_login_nicht_moeglich'];
	$text .= hinweis($fehlermeldung, "fehler");
	$bereich = "";
}

if($neuregistrierung_deaktivieren && ($bereich == "registrierung" || $bereich == "neu" || $bereich == "neu2")) {
	$bereich = "";
}

// Login ist für alle Benutzer gesperrt
if ($chat_offline) {
	$bereich = "gesperrt";
}

// Ausloggen, falls eingeloggt
if ($bereich == "logoff") {
	// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
	id_lese($id);
	// Logout falls noch online
	if (strlen($u_id) > 0) {
		// Aus dem Chat ausloggen
		ausloggen($u_id, $u_nick, $o_raum, $o_id);
	}
	$bereich = "";
}


// Abmelden
$nicht_angemeldet = false;
if ($bereich == "abmelden") {
	//Cookies entfernen
	
	setcookie("identifier","",time()-(3600*24*365));
	setcookie("securitytoken","",time()-(3600*24*365));
	
	unset($_COOKIE['identifier']);
	unset($_COOKIE['securitytoken']);
	
	$erfolgsmeldung = $lang['login_erfolgsmeldung_abmeldung'];
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
	
	$automatische_anmeldung = false;
	$nicht_angemeldet = true;
	$bereich = "";
}

// Falls in Loginmaske/Nutzungsbestimmungen auf Abbruch geklickt wurde
if ($uebergabe == $lang['login_nutzungsbestimmungen_abbruch'] && $bereich == "einloggen") {
	$bereich = "";
}

switch ($bereich) {
	case "impressum":
		// Impressum anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		$zaehler = 0;
		$text .= "<table style=\"width:100%;\">";
		
		$impressum = "<b>" . $lang['impressum'] . "</b>";
		$impressum .= "<br>";
		$impressum .= $impressum_name;
		$impressum .= "<br>";
		$impressum .= $impressum_strasse;
		$impressum .= "<br>";
		$impressum .= $impressum_plz_ort;
		
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor>" . $impressum . "</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		$text .= "</table>";
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_impressum'], $text);
		echo "</body>";
		
		break;
		
	case "datenschutz":
		// Datenschutz anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		$zaehler = 0;
		$text .= "<table style=\"width:100%;\">";
		
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor>" . $lang['datenschutzerklaerung'] . "</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		$text .= "</table>";
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_datenschutzerklaerung'], $text);
		echo "</body>";
		
		break;
		
	case "chatiquette":
		// Chatiquette anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		$zaehler = 0;
		$text .= "<table style=\"width:100%;\">";
		
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor>" . $chatiquette . "</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		$text .= "</table>";
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_chatiquette'], $text);
		echo "</body>";
		
		break;
		
	case "nutzungsbestimmungen":
		// Nutzungsbestimmungen anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		$zaehler = 0;
		$text .= "<table style=\"width:100%;\">";
		
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor>" . $lang['chat_agb'] . "</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		$text .= "</table>";
		$text .= zeige_index_footer();
			
		zeige_tabelle_volle_breite($lang['login_nutzungsbestimmungen'], $text);
		echo "</body>";
		
		break;
		
	case "kontakt":
		// Kontaktformular anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/kontakt.php');
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_kontakt'], $text);
		echo "</body>";
		
		break;
		
	case "passwort-vergessen":
		// Neues Passwort anfordern
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/passwort-vergessen.php');
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_passwort_vergessen'], $text);
		echo "</body>";
		
		break;
		
	case "passwort-zuruecksetzen":
		// Neues Passwort anfordern
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/passwort-zuruecksetzen.php');
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_passwort_vergessen'], $text);
		echo "</body>";
		
		break;
		
	case "email-bestaetigen":
		// Neues Passwort anfordern
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/email-bestaetigen.php');
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_email_aendern'], $text);
		echo "</body>";
		
		break;
	
	case "neu2":
		// Eingabe der Werte für die Registrierung aus der E-Mail
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/neu2.php');
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_registrierung'], $text);
		echo "</body>";
		
		break;
	
	case "gesperrt":
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		$zaehler = 0;
		$text .= "<table style=\"width:100%;\">";
		
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "<tr>\n";
		$text .= "<td $bgcolor><h2>" . $lang['chat_offline'] . "</h2></td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		
		$text .= "</table>";
		
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['willkommen'], $text);
		echo "</body>";
		
		break;
	
	case "einloggen":
		// In den Chat einloggen
		
		require_once('templates/einloggen.php');
		
		if($frameset == false) {
			if($fehlermeldung != "") {
				// Box für Login
				$text .= zeige_chat_login();
			}
			
			zeige_header($body_titel, 0);
			echo "<body>";
			
			// Gibt die Kopfzeile im Login aus
			zeige_kopfzeile_login();
			
			zeige_tabelle_volle_breite($lang['login_login'], $text);
			echo "</body>";
		}
		
		break;
	
	case "registrierung":
		// Registrierung mit Eingabe der E-Mail zum Senden der E-Mail
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/registrierung.php');
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_registrierung'], $text);
		echo "</body>";
		
		break;
	
	case "neu":
		// Vervollständigung der Registrierung aus dem Link der E-Mail
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/neu.php');
		$text .= zeige_index_footer();
		
		zeige_tabelle_volle_breite($lang['login_registrierung'], $text);
		echo "</body>";
		
		break;
	
	case "relogin":
		// Login aus dem Forum in den Chat; Benutzerdaten setzen
		id_lese($id);
		
		zeige_header($body_titel, 0);
		
		// Chat betreten
		betrete_chat($o_id, $u_id, $u_nick, $u_level, $neuer_raum);
		
		require_once("functions/functions-frameset.php");
		
		frameset_chat();
		
		break;
	
	default:
		// Login ausgeben
		
		// Session löschen
		//$_SESSION = array();
		//unset($_SESSION['id']);
		
		// Box für Login
		$text .= zeige_chat_login();
		
		$text .= zeige_index_footer();
		
		
		zeige_header($body_titel, 0);
		echo "<body>";
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		
		zeige_tabelle_volle_breite($lang['login_login'], $text);
		echo "</body>";
}
?>
</html>