<?php
require_once("functions/functions.php");
require_once("functions/functions-formulare.php");

$bereich = filter_input(INPUT_GET, 'bereich', FILTER_SANITIZE_URL);

$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);

$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
if( $aktion == '') {
	$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
}
$aktion3 = filter_input(INPUT_POST, 'aktion3', FILTER_SANITIZE_URL);

$bildname = filter_input(INPUT_GET, 'bildname', FILTER_SANITIZE_URL);

$formular = filter_input(INPUT_POST, 'formular', FILTER_SANITIZE_NUMBER_INT);

// Räume
$extended = filter_input(INPUT_GET, 'extended', FILTER_SANITIZE_NUMBER_INT);
$order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_URL);

$schau_raum = filter_input(INPUT_GET, 'schau_raum', FILTER_SANITIZE_NUMBER_INT);
if( $schau_raum == '') {
	$schau_raum = filter_input(INPUT_POST, 'schau_raum', FILTER_SANITIZE_NUMBER_INT);
}

$uebergabe = filter_input(INPUT_POST, 'uebergabe', FILTER_SANITIZE_STRING);


// Benutzer suchen
$suchtext = filter_input(INPUT_POST, 'suchtext', FILTER_SANITIZE_STRING);
$suche_ip = filter_input(INPUT_POST, 'suche_ip', FILTER_SANITIZE_STRING);
$suche_level = filter_input(INPUT_POST, 'suche_level', FILTER_SANITIZE_STRING);
$suche_benutzerseite = filter_input(INPUT_POST, 'suche_benutzerseite', FILTER_SANITIZE_NUMBER_INT);
$suche_anmeldung = filter_input(INPUT_POST, 'suche_anmeldung', FILTER_SANITIZE_STRING);
$suche_login = filter_input(INPUT_POST, 'suche_login', FILTER_SANITIZE_STRING);


// Sperren
$f['is_infotext'] = filter_input(INPUT_POST, 'is_infotext', FILTER_SANITIZE_STRING);
$f['is_domain'] = filter_input(INPUT_POST, 'is_domain', FILTER_SANITIZE_STRING);
$ip1 = filter_input(INPUT_POST, 'ip1', FILTER_SANITIZE_STRING);
$ip2 = filter_input(INPUT_POST, 'ip2', FILTER_SANITIZE_STRING);
$ip3 = filter_input(INPUT_POST, 'ip3', FILTER_SANITIZE_STRING);
$ip4 = filter_input(INPUT_POST, 'ip4', FILTER_SANITIZE_STRING);
$f['is_warn'] = filter_input(INPUT_POST, 'is_warn', FILTER_SANITIZE_STRING);
$f['is_id'] = filter_input(INPUT_GET, 'is_id', FILTER_SANITIZE_NUMBER_INT);
$is_id = $f['is_id'];


// Blacklist, Freundesliste oder Nachrichten
$daten[] = "";

$daten['id'] = filter_input(INPUT_GET, 'daten_id', FILTER_SANITIZE_NUMBER_INT);
if( $daten['id'] == '') {
	$daten['id'] = filter_input(INPUT_POST, 'daten_id', FILTER_SANITIZE_NUMBER_INT);
}

$daten['u_nick'] = filter_input(INPUT_GET, 'daten_nick', FILTER_SANITIZE_STRING);
if( $daten['u_nick'] == '') {
	$daten['u_nick'] = filter_input(INPUT_POST, 'daten_nick', FILTER_SANITIZE_STRING);
}

$daten['m_text'] = filter_input(INPUT_POST, 'daten_text', FILTER_SANITIZE_STRING);
$daten['m_betreff'] = filter_input(INPUT_POST, 'daten_betreff', FILTER_SANITIZE_STRING);
$daten['typ'] = filter_input(INPUT_POST, 'daten_typ', FILTER_SANITIZE_NUMBER_INT);


// Nachrichten, Sperren, Freunde löschen
$bearbeite_ids = filter_input(INPUT_POST, 'bearbeite_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);

$ui_id = filter_input(INPUT_GET, 'ui_id', FILTER_SANITIZE_NUMBER_INT);
if( $ui_id == '') {
	$ui_id = filter_input(INPUT_POST, 'ui_id', FILTER_SANITIZE_NUMBER_INT);
}

// Benutzer
$zeigeip = filter_input(INPUT_GET, 'zeigeip', FILTER_SANITIZE_NUMBER_INT);
$kick_user_chat = filter_input(INPUT_GET, 'kick_user_chat', FILTER_SANITIZE_NUMBER_INT);

//Räume
$loesch = filter_input(INPUT_POST, 'loesch', FILTER_SANITIZE_STRING);
$loesch2 = filter_input(INPUT_POST, 'loesch2', FILTER_SANITIZE_STRING);

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

if( isset($u_id) && strlen($u_id) != 0 ) {
	$user_eingeloggt = true;
	
	// Ermitteln, ob sich der Benutzer im Chat oder im Forum aufhält
	if ($o_raum && $o_raum == "-1") {
		$wo_online = "forum";
	} else {
		$wo_online = "chat";
	}
} else {
	$user_eingeloggt = false;
}

// Wird die Seite aufgebaut?
$kein_seitenaufruf = false;

// Übersetzungen der entsprechenden Seite einbinden
if (file_exists("languages/$sprache-$bereich.php")) {
	require_once("languages/$sprache-$bereich.php");
} else {
	$kein_seitenaufruf = true;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

if($bereich == "log" && $aktion == "abspeichern") {
	// Falls Abspeichern, Header senden
	$dateiname = "log-" . date("YmdHi") . ".htm";
	
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$dateiname\"");
	header("Content-Location: $dateiname");
	header("Cache-Control: maxage=15"); //In seconds
	header("Pragma: public");
	$minimalistisch = true;
} else {
	$minimalistisch = false;
}

$meta_refresh = "";
if($bereich == "sperren" || $bereich == "freunde" || $bereich == "nachrichten") {
	$meta_refresh = "<script>
		function toggle(tostat) {
			for(i=0; i<document.forms[\"eintraege_loeschen\"].elements.length; i++) {
				 e = document.forms[\"eintraege_loeschen\"].elements[i];
				 if ( e.type=='checkbox' )
					 e.checked=tostat;
			}
		}
	</script>\n";
}

// Titel aus der entsprechenden Übersetzung holen
if($t['titel'] != '') {
	$title = $body_titel . ' - ' . $t['titel'];
} else {
	$title = $body_titel;
}

zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh, $minimalistisch);

// Bestimmte Seiten dürfen nur im eingeloggten Zustand aufgerufen werden
$kein_aufruf_unter_bestimmten_bedinungen = false;
$nur_eingeloggten_seiten = array('raum', 'profilbilder', 'einstellungen', 'log', 'benutzer');
$nur_eingeloggten_seiten_und_registriert = array('profil', 'nachrichten', 'top10', 'freunde');
$nur_eingeloggten_seiten_und_admin = array('statistik', 'sperren');
if( !$user_eingeloggt && in_array($bereich, $nur_eingeloggten_seiten, true) ) {
	$kein_aufruf_unter_bestimmten_bedinungen = true;
} else if( !$user_eingeloggt || ($user_eingeloggt && $u_level == "G" && in_array($bereich, $nur_eingeloggten_seiten_und_registriert, true) ) ) {
	$kein_aufruf_unter_bestimmten_bedinungen = true;
} else if( $user_eingeloggt && !$admin && in_array($bereich, $nur_eingeloggten_seiten_und_admin, true) ) {
	$kein_aufruf_unter_bestimmten_bedinungen = true;
}
?>
<body>
<br>
<?php
if(!$bereich || $kein_seitenaufruf) {
	// Seite nicht gefunden oder Datei für die Übersetzung nicht gefunden
	$box = $t['fehler'];
	$text = $t['seite_nicht_gefunden'];
	zeige_tabelle_zentriert($box, $text);
} else if($kein_aufruf_unter_bestimmten_bedinungen) {
		// Seite nicht gefunden oder Datei für die Übersetzung nicht gefunden
		$box = $t['fehler'];
		$text = $t['kein_zugriff'];
		zeige_tabelle_zentriert($box, $text);
	} else {
	// Seitenaufruf
	switch ($bereich) {
		case "hilfe":
			// Hilfe anzeigen
			
			// Menü ausgeben
			$box = $t['titel'];
			$text = "<a href=\"inhalt.php?bereich=hilfe\">$t[hilfe_menue4]</a>\n";
			$text .= "| <a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-befehle\">$t[hilfe_menue5]</a>\n";
			$text .= "| <a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-sprueche\">$t[hilfe_menue6]</a>\n";
			$text .= "| <a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-community\">$t[hilfe_menue7]</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			switch ($aktion) {
				case "hilfe-befehle":
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
					
				default:
					// Hilfe anzeigen
					require_once('templates/hilfe.php');
			}
			break;
		
		case "benutzer":
			// Benutzer anzeigen
			require_once("functions/functions-benutzer.php");
			require_once("functions/functions-user.php");
			require_once("functions/functions-nachrichten_betrete_verlasse.php");
			require_once("languages/$sprache-einstellungen.php");
			
			// Menü ausgeben
			$box = $t['benutzer_titel'];
			$text = "<a href=\"inhalt.php?bereich=benutzer\">$t[benutzer_uebersicht]</a>\n";
			if ($u_level != "G") {
				$text .= "| <a href=\"inhalt.php?bereich=benutzer&aktion=suche\">$t[benutzer_benutzer_suchen]</a>\n";
			}
			
			if ($adminlisteabrufbar && $u_level != "G") {
				$text .= "| <a href=\"inhalt.php?bereich=benutzer&aktion=adminliste\">$t[benutzer_adminliste_anzeigen]</a>\n";
			}
			if ($u_level != "G") {
				if ($punktefeatures) {
					$ur1 = "inhalt.php?bereich=top10";
					$url = "href=\"$ur1\" ";
					$text .= "| <a $url>$t[benutzer_top10]</a>\n";
				}
			}
			
			zeige_tabelle_zentriert($box, $text);
	
			require_once('templates/benutzer.php');
	
			break;
		
		case "raum":
			// Raum anzeigen
			require_once("functions/functions-raum.php");
			require_once("functions/functions-msg.php");
			
			// Menü ausgeben
			$box = $t['raum_titel'];
			$text = "<a href=\"inhalt.php?bereich=raum\">" . $t['raum_uebersicht'] . "</a>\n";
			if ($u_level != "G") {
				$text .= "| <a href=\"inhalt.php?bereich=raum&aktion=edit\">" . $t['raum_neuer_raum'] . "</a>\n";
			}
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/raum.php');
			
			break;
		
		case "nachrichten":
			// Nachrichten anzeigen
			require_once("functions/functions-nachrichten.php");
			
			// Menü ausgeben
			$box = $t['titel'];
			$text = "<a href=\"inhalt.php?bereich=nachrichten\">" . $t['nachrichten_posteingang'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?bereich=nachrichten&aktion=postausgang\">" . $t['nachrichten_postausgang'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?bereich=nachrichten&aktion=neu\">" . $t['nachrichten_neue_nachricht'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?bereich=nachrichten&aktion=papierkorb\">" . $t['nachrichten_papierkorb'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?bereich=nachrichten&aktion=mailboxzu\">" . $t['nachrichten_nachrichten_deaktivieren'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-community#mail\">" . $t['nachrichten_hilfe'] . "</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/nachrichten.php');
			
			break;
		
		case "profil":
			// Profil anzeigen
			require_once("functions/functions-profil.php");
			
			// Menü ausgeben
			$box = $t['titel'];
			$text .= "<a href=\"inhalt.php?bereich=profil\">$t[profil_profil_bearbeiten]</a>\n";
			$text .= "| <a href=\"inhalt.php?bereich=profilbilder\">$t[profil_bilder_hochladen]</a>\n";
			if ($admin) {
				$text .= "| <a href=\"inhalt.php?bereich=profil&aktion=zeigealle\">$t[profil_alle_profile_ausgeben]</a>\n";
			}
			zeige_tabelle_zentriert($box, $text);
				
			require_once('templates/profil.php');
				
			break;
			
		case "profilbilder":
			// Profil anzeigen
			require_once("languages/$sprache-profil.php");
			
			require_once("functions/functions-profilbilder.php");
			
			// Menü ausgeben
			$box = $t['titel'];
			$text .= "<a href=\"inhalt.php?bereich=profil\">$t[profil_profil_bearbeiten]</a>\n";
			$text .= "| <a href=\"inhalt.php?bereich=profilbilder\">$t[profil_bilder_hochladen]</a>\n";
			if ($admin) {
				$text .= "| <a href=\"inhalt.php?bereich=profil&aktion=zeigealle\">$t[profil_alle_profile_ausgeben]</a>\n";
			}
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/profilbilder.php');
			
			break;
		
		case "einstellungen":
			// Einstellungen anzeigen
			require_once("languages/$sprache-benutzer.php");
			require_once("languages/$sprache-profilbilder.php");
			
			require_once("functions/functions-profilbilder.php");
			require_once("functions/functions-msg.php");
			require_once("functions/functions-einstellungen.php");
			
			// Menü ausgeben
			if ($u_level != "G") {
				$box = $t['einstellungen_titel'];
				$text .= "<a href=\"inhalt.php?bereich=einstellungen\">$t[einstellungen_menue1]</a>\n";
				$text .= "| <a href=\"inhalt.php?bereich=einstellungen&aktion=aktion\">$t[einstellungen_menue2]</a>\n";
				$text .= "| <a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-community#home\">$t[einstellungen_menue3]</a>\n";
				zeige_tabelle_zentriert($box, $text);
			}
			
			switch ($aktion) {
				case "aktion-eintragen":
					// Ab in die Datenbank mit dem Eintrag
					$text = eintrag_aktionen($aktion_datensatz);
					$text .= zeige_aktionen();
					
					$box = $t['einstellungen_benachrichtigungen_inhalt'];
					zeige_tabelle_zentriert($box, $text);
					break;
					
				case "aktion":
					$text = zeige_aktionen();
					
					$box = $t['einstellungen_benachrichtigungen_inhalt'];
					zeige_tabelle_zentriert($box, $text);
					break;
					
				default:
					require_once('templates/einstellungen.php');
			}
			
			break;
		
		case "statistik":
			// Statistik anzeigen
			require_once("functions/functions-statistik.php");
			
			// Menü ausgeben
			$box = $t['titel'];
			$text = "[<a href=\"inhalt.php?bereich=statistik&aktion=monat\">" . $t['statistik_nach_monaten'] . "</a>]\n"
					. "[<a href=\"inhalt.php?bereich=statistik&aktion=stunde\">" . $t['statistik_nach_stunden'] . "</a>]";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/statistik.php');
			
			break;
		
		case "sperren":
			// Sperren anzeigen
			require_once("functions/functions-sperren.php");
			
			require_once('templates/sperren.php');
				
			break;
			
		case "freunde":
			// Freunde anzeigen
			require_once("functions/functions-freunde.php");
			
			// Menü ausgeben
			$box = $t['titel'];
			$text = "<a href=\"inhalt.php?bereich=freunde\">$t[freunde_meine_freunde]</a>\n"
			. "| <a href=\"inhalt.php?bereich=freunde&aktion=neu\">$t[freunde_neuen_freund_hinzufuegen]</a>\n"
			. "| <a href=\"inhalt.php?bereich=freunde&aktion=bestaetigen\">$t[freunde_freundesanfragen]</a>\n";
			if ($admin) {
				$text .= "| <a href=\"inhalt.php?bereich=freunde&aktion=admins\">$t[freunde_alle_admins_als_freund_hinzufuegen]</a>\n";
			}
			$text .= "| <a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-community#freunde\">$t[freunde_hilfe]</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/freunde.php');
			
			break;
				
		case "top10":
			// Top 10/100 anzeigen
			
			// Menü ausgeben
			$box = $t['titel'];
			$text = "<a href=\"inhalt.php?bereich=top10\">".$t['top_menue2']."</a>\n";
			$text .= "| <a href=\"inhalt.php?bereich=top10&aktion=top100\">".$t['top_menue3']."</a>\n";
			$text .= "| <a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-community#punkte\">".$t['top_menue4']."</a>\n";
			zeige_tabelle_zentriert($box, $text);
				
			require_once('templates/top10.php');
			
			break;
			
		case "log":
			// Log anzeigen
			require_once("functions/functions-log.php");
			
			require_once('templates/log.php');
			
			break;
		
		default:
			// Seite nicht gefunden
			$box = $t['fehler'];
			$text = $t['seite_nicht_gefunden'];
			zeige_tabelle_zentriert($box, $text);
	}
}
?>
</body>
</html>