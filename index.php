<?php
// Funktionen und Config laden, Host bestimmen
require_once("./functions/functions.php");
require_once("./functions/functions-index.php");
require_once("./languages/$sprache-index.php");

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

$fehlermeldung = "";
// IP bestimmen und prüfen. Ist Login erlaubt?
$remote_addr = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
$http_x_forwarded_for = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP);
$warnung = false;

// Sperrt den Chat, wenn in der Sperre Domain "-GLOBAL-" ist
$abweisen = ist_globale_speere_aktiv();

if(!$abweisen) {
	$checke_ipsperren = checke_ipsperren($remote_addr, $http_x_forwarded_for);
	if($checke_ipsperren == "abweisen") {
		// Login sperren
		$infotext = $row['is_infotext'];
		$abweisen = true;
	} else if($checke_ipsperren == "warnung") {
		// Warnung ausgeben
		$infotext = $row['is_infotext'];
		$warnung = true;
	}
}
	
// Wenn $abweisen=true, dann ist Login ist für diesen Benutzer gesperrt
// Es sei denn wechsel Forum -> Chat, dann "Relogin", und wechsel trotz IP Sperre in Chat möglich
if ($abweisen && $bereich != "relogin" && strlen($username) > 0) {
	$query = pdoQuery("SELECT `u_nick`, `u_level` FROM `user` WHERE `u_nick` = :u_nick AND (`u_level` IN ('S','C'))", [':u_nick'=>coreCheckName($username, $check_name)]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1 && (strlen($bereich) > 0)) {
		$abweisen = false;
	}
	
	// Prüfung nun auf Admin beendet
	// Nun Prüfung ob genug Punkte
	$durchgangwegenpunkte = false;
	
	if ($loginwhileipsperre <> 0) {
		// Test auf Punkte
		$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_level`, `u_punkte_gesamt` FROM `user` WHERE `u_nick` = :u_nick AND (`u_level` IN ('A','C','G','M','S','U'))", [':u_nick'=>coreCheckName($username, $check_name)]);
		
		// Durchleitung wg. Punkten im Fall der MD5() Verschlüsselung wird nicht gehen
		$resultCount = $query->rowCount();
		if ($resultCount == 1 && strlen($bereich) > 0) {
			$result = $query->fetch();
			if ($result['u_punkte_gesamt'] >= $loginwhileipsperre) {
				// genügend Punkte
				// Login zulassen
				$durchgangwegenpunkte = true;
				$abweisen = false;
				$t_u_id = $result['u_id'];
				$t_u_nick = $result['u_nick'];
				$infotext = str_replace("%punkte%", $result['u_punkte_gesamt'], $lang['ipsperre2']);
			}
		}
	}
	
	if ($durchgangwegenpunkte) {
		// Wenn Benutzer wegen Punkte durch IP Sperre kommen, dann Meldung an alle Admins
		if ($eintritt == 'forum') {
			$raumname = " (" . $whotext[2] . ")";
		} else {
			$query2 = pdoQuery("SELECT `r_name` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>intval($eintritt)]);
			
			$result2Count = $query2->rowCount();
			if ($result2Count > 0) {
				$result2 = $query->fetch();
				$raumname = " (" . $result2['r_name'] . ") ";
			} else {
				$raumname = "";
			}
		}
		
		$query = pdoQuery("SELECT `o_user` FROM `online` WHERE `o_level` = 'S' OR `o_level` = 'C'", []);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$txt = str_replace("%ip_adr%", $http_x_forwarded_for, $lang['ipsperre1']);
			$txt = str_replace("%ip_name%", $ip_name, $txt);
			$txt = str_replace("%is_infotext%", $infotext, $txt);
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$ah = "<a href=\"inhalt.php?bereich=benutzer&aktion=benutzer_zeig&ui_id=$t_u_id\" target=\"chat\">$t_u_nick</a>";
				system_msg("", 0, $row['o_user'], $system_farbe, str_replace("%u_nick%", $ah . $raumname, $txt));
			}
			unset($infotext);
			unset($t_u_id);
			unset($u_nick);
		}
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
	if(isset($id)) {
		id_lese();
		// Logout falls noch online
		if (strlen($u_id) > 0) {
			// Aus dem Chat ausloggen
			ausloggen($u_id, $u_nick, $o_raum, $o_id);
		}
		
		// Session löschen
		$_SESSION = array();
		unset($_SESSION['id']);
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
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/impressum.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_impressum'], $text);
		
		break;
		
	case "datenschutz":
		// Datenschutz anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/datenschutz.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_datenschutzerklaerung'], $text);
		
		break;
		
	case "chatiquette":
		// Chatiquette anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/chatiquette.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_chatiquette'], $text);
		
		break;
		
	case "nutzungsbestimmungen":
		// Nutzungsbestimmungen anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/nutzungsbestimmungen.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_nutzungsbestimmungen'], $text);
		
		break;
		
	case "kontakt":
		// Kontaktformular anzeigen
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/kontakt.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_kontakt'], $text);
		
		break;
		
	case "passwort-vergessen":
		// Neues Passwort anfordern
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/passwort-vergessen.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_passwort_vergessen'], $text);
		
		break;
		
	case "passwort-zuruecksetzen":
		// Neues Passwort anfordern
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/passwort-zuruecksetzen.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_passwort_vergessen'], $text);
		
		break;
		
	case "email-bestaetigen":
		// Neues Passwort anfordern
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/email-bestaetigen.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_email_aendern'], $text);
		
		break;
	
	case "neu2":
		// Eingabe der Werte für die Registrierung aus der E-Mail
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/neu2.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_registrierung'], $text);
		
		break;
	
	case "gesperrt":
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/gesperrt.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['willkommen'], $text);
		
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
			require_once("./templates/index-header.php");
			
			// Gibt die Kopfzeile im Login aus
			zeige_kopfzeile_login();
			
			zeige_tabelle_login($lang['login_login'], $text);
		}
		
		break;
	
	case "registrierung":
		// Registrierung mit Eingabe der E-Mail zum Senden der E-Mail
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/registrierung.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_registrierung'], $text);
		
		break;
	
	case "neu":
		// Vervollständigung der Registrierung aus dem Link der E-Mail
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		require_once('templates/neu.php');
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_registrierung'], $text);
		
		break;
	
	case "relogin":
		// Login aus dem Forum in den Chat; Benutzerdaten setzen
		id_lese($id);
		
		zeige_header($body_titel, 0);
		
		if( !isset($u_id) || $u_id == null || $u_id == "") {
			global $kontakt;
			email_senden($kontakt, "u_id leer beim Login2", "Nickname: " . $u_nick);
		}
		
		// Chat betreten
		betrete_chat($o_id, $u_id, $u_nick, $u_level, $neuer_raum);
		
		require_once("./functions/functions-frameset.php");
		
		frameset_chat();
		
		break;
	
	default:
		// Login ausgeben
		
		
		zeige_header($body_titel, 0);
		echo "<body>";
		require_once("./templates/index-header.php");
		
		// Gibt die Kopfzeile im Login aus
		zeige_kopfzeile_login();
		
		// Box für Login
		$text .= zeige_chat_login();
		$text .= zeige_index_footer();
		zeige_tabelle_login($lang['login_login'], $text);
}
?>
</html>