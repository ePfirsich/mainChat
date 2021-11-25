<?php
require_once("functions/functions.php");
$seite = filter_input(INPUT_GET, 'seite', FILTER_SANITIZE_URL);
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
if( $id == '') {
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_URL);
}

$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
if( $aktion == "") {
	$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
}

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, o_js
id_lese($id);

if( isset($u_id) && strlen($u_id) != 0 ) {
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	$user_eingeloggt = true;
	
	// Ermitteln, ob sich der Benutzer im Chat oder im Forum aufhält
	if ($o_raum && $o_raum == "-1") {
		$wo_online = "forum";
		$reset = "1";
	} else {
		$wo_online = "chat";
		$reset = "0";
	}
} else {
	$user_eingeloggt = false;
}

// Wird die Seite aufgebaut?
$kein_seitenaufruf = false;

// Allgemeine Übersetzungen
require_once("languages/$sprache.php");

// Übersetzungen der entsprechenden Seiten einbinden
if (file_exists("languages/$sprache-$seite.php")) {
	require_once("languages/$sprache-$seite.php");
} else {
	$kein_seitenaufruf = true;
}

// Titel aus der entsprechenden Übersetzung holen
if($t['titel'] != '') {
	$title = $body_titel . ' - ' . $t['titel'];
} else {
	$title = $body_titel;
}

zeige_header_anfang($title, 'mini', '', $u_layout_farbe);
?>
<script>
function toggleMail(tostat) {
		for(i=0; i<document.forms["mailbox"].elements.length; i++) {
			 e = document.forms["mailbox"].elements[i];
			 if ( e.type=='checkbox' )
				 e.checked=tostat;
		}
}

function toggleBlacklist(tostat) {
	for(i=0; i<document.forms["blacklist_loeschen"].elements.length; i++) {
		 e = document.forms["blacklist_loeschen"].elements[i];
		 if ( e.type=='checkbox' )
			 e.checked=tostat;
	}
}
function toggleFreunde(tostat) {
	for(i=0; i<document.forms["freund_loeschen"].elements.length; i++) {
		 e = document.forms["freund_loeschen"].elements[i];
		 if ( e.type=='checkbox' )
			 e.checked=tostat;
	}
}
function resetinput() {
	document.forms['form'].elements['text'].value=document.forms['form'].elements['text2'].value;
	document.forms['form'].elements['text2'].value='';
	document.forms['form'].submit();
	document.forms['form'].elements['text2'].focus();
	document.forms['form'].elements['text2'].select();
}
</script>
<?php
zeige_header_ende();

// Bestimmte Seiten dürfen nur im eingeloggten Zustand aufgerufen werden
$kein_aufruf_unter_bestimmten_bedinungen = false;
$nur_eingeloggten_seiten = array('raum', 'profil', 'einstellungen', 'log', 'benutzer');
$nur_eingeloggten_seiten_und_registriert = array('nachrichten', 'top10', 'freunde');
$nur_eingeloggten_seiten_und_admin = array('statistik', 'sperren');
if( !$user_eingeloggt && in_array($seite, $nur_eingeloggten_seiten, true) ) {
	$kein_aufruf_unter_bestimmten_bedinungen = true;
} else if( !$user_eingeloggt || ($user_eingeloggt && $u_level == "G" && in_array($seite, $nur_eingeloggten_seiten_und_registriert, true) ) ) {
	$kein_aufruf_unter_bestimmten_bedinungen = true;
} else if( $user_eingeloggt && !$admin && in_array($seite, $nur_eingeloggten_seiten_und_admin, true) ) {
	$kein_aufruf_unter_bestimmten_bedinungen = true;
}
?>
<body>
<br>
<?php
if(!$seite || $kein_seitenaufruf) {
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
	switch ($seite) {
		case "hilfe":
			// Hilfe anzeigen
			
			// Menü ausgeben
			$box = $t['hilfe_menue1'];
			$text = "<a href=\"inhalt.php?seite=hilfe&id=$id\">$t[hilfe_menue4]</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-befehle&id=$id\">$t[hilfe_menue5]</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-sprueche&id=$id\">$t[hilfe_menue6]</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-community&id=$id\">$t[hilfe_menue7]</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			switch ($aktion) {
				case "chatiquette":
					// Datenschutz anzeigen
					require_once('templates/chatiquette.php');
					
					break;
					
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
			require_once("functions/functions-func-nachricht.php");
			
			// Menü ausgeben
			$box = $t['benutzer_menue13'];
			$text = "<a href=\"inhalt.php?seite=benutzer&id=$id&schau_raum=$schau_raum\">$t[benutzer_menue1]</a>\n";
			if ($u_level != "G") {
				$text .= "| <a href=\"inhalt.php?seite=benutzer&id=$id&schau_raum=$schau_raum&aktion=suche\">$t[benutzer_menue2]</a>\n";
			}
			
			if ($adminlisteabrufbar && $u_level != "G") {
				$text .= "| <a href=\"inhalt.php?seite=benutzer&id=$id&schau_raum=$schau_raum&aktion=adminliste\">$t[benutzer_menue12]</a>\n";
			}
			if ($u_level != "G") {
				if ($punktefeatures) {
					$ur1 = "inhalt.php?seite=top10&id=$id";
					$url = "href=\"$ur1\" ";
					$text .= "| <a $url>$t[benutzer_menue7]</a>\n";
				}
				$ur1 = "inhalt.php?seite=freunde&id=$id";
				$url = "href=\"$ur1\" ";
				$text .= "| <a $url>$t[benutzer_menue8]</a>\n";
			}
			
			zeige_tabelle_zentriert($box, $text);
	
			require_once('templates/benutzer.php');
	
			break;
		
		case "raum":
			// Raum anzeigen
			require_once("functions/functions-msg.php");
			
			// Menü ausgeben
			$box = $t['raum_menue1'];
			$text = "<a href=\"inhalt.php?seite=raum&id=$id\">" . $t['raum_menue8'] . "</a>\n";
			if ($u_level != "G") {
				$text .= "| <a href=\"inhalt.php?seite=raum&aktion=neu&id=$id\">" . $t['raum_menue2'] . "</a>\n";
			}
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/raum.php');
			
			break;
		
		case "nachrichten":
			// Nachrichten anzeigen
			require_once("functions/functions-nachrichten.php");
			
			// Menü ausgeben
			$box = $t['nachrichten_menue1'];
			$text = "<a href=\"inhalt.php?seite=nachrichten&id=$id\">" . $t['nachrichten_menue2'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=nachrichten&aktion=neu&id=$id\">" . $t['nachrichten_menue3'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=nachrichten&aktion=papierkorb&id=$id\">" . $t['nachrichten_menue4'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=nachrichten&aktion=papierkorbleeren&id=$id\">" . $t['nachrichten_menue5'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=nachrichten&aktion=mailboxzu&id=$id\">" . $t['nachrichten_menue6'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=hilfe&id=$id&aktion=hilfe-community#mail\">" . $t['nachrichten_menue7'] . "</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/nachrichten.php');
			
			break;
		
		case "profil":
			// Profil anzeigen
			require_once("functions/functions-profil.php");
			require_once("functions/functions-formulare.php");
			
			// Menü ausgeben
			$box = $t['profil_menue1'];
			$text .= "<a href=\"home.php?id=$id&aktion=aendern\">$t[profil_homepage_bearbeiten]</a>\n";
			if ($admin) {
				$text .= "| <a href=\"inhalt.php?seite=profil&id=$id&aktion=zeigealle\">$t[profil_alle_profile_ausgeben]</a>\n";
			}
			$text .= "| <a href=\"inhalt.php?seite=benutzer&id=$id\">$t[profil_benutzer]</a>\n";
			zeige_tabelle_zentriert($box, $text);
				
			require_once('templates/profil.php');
				
			break;
		
		case "einstellungen":
			// Einstellungen anzeigen
			require_once("languages/$sprache-benutzer.php");
			
			require_once("functions/functions-msg.php");
			require_once("functions/functions-einstellungen.php");
			require_once("functions/functions-formulare.php");
			
			// Menü ausgeben
			if ($u_level != "G") {
				$box = $t['einstellungen_menue_titel'];
				$text .= "<a href=\"inhalt.php?seite=einstellungen&id=$id\">$t[einstellungen_menue1]</a>\n";
				$text .= "| <a href=\"inhalt.php?seite=einstellungen&aktion=aktion&id=$id\">$t[einstellungen_menue2]</a>\n";
				$text .= "| <a href=\"inhalt.php?seite=hilfe&id=$id&aktion=hilfe-community#home\">$t[einstellungen_menue3]</a>\n";
				zeige_tabelle_zentriert($box, $text);
			}
			
			switch ($aktion) {
				case "aktion-eintragen":
					// Ab in die Datenbank mit dem Eintrag
					eintrag_aktionen($aktion_datensatz);
					zeige_aktionen("normal");
					
					$box = $t['aktion1'];
					$text = $t['aktion2'];
					zeige_tabelle_zentriert($box, $text);
					break;
					
				case "aktion":
					zeige_aktionen("normal");
					
					$box = $t['aktion1'];
					$text = $t['aktion2'];
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
			$box = $t['statistik1'];
			$text = "[<a href=\"inhalt.php?seite=statistik&aktion=monat&id=$id\">" . $t['statistik3'] . "</a>]\n"
					. "[<a href=\"inhalt.php?seite=statistik&aktion=stunde&id=$id\">" . $t['statistik2'] . "</a>]";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/statistik.php');
			
			break;
		
		case "sperren":
			// Sperren anzeigen
			require_once("functions/functions-sperren.php");
			
			// Menü ausgeben
			if (isset($uname)) {
				$zusatztxt = "'" . $uname . "'&nbsp;&gt;&gt;&nbsp;";
			} else {
				$zusatztxt = "";
				$uname = "";
			}
			
			$box = $t['sperren_menue4'];
			$text = "<a href=\"inhalt.php?seite=sperren&id=$id\">$t[sperren_menue1]</a>\n" . "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=neu\">$t[sperren_menue2]</a>\n";
			
			$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GLOBAL-'";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) > 0) {
				$text .= "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperre0\">$t[sperren_menue5a]</a>\n";
			} else {
				$text .= "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperre1\">$t[sperren_menue5b]</a>\n";
			}
			mysqli_free_result($result);
			
			$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GAST-'";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) > 0) {
				$text .= "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperregast0\">$t[sperren_menue6a]</a>\n";
			} else {
				$text .= "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperregast1\">$t[sperren_menue6b]</a>\n";
			}
			mysqli_free_result($result);
			
			$text .= "| <a href=\"inhalt.php?seite=sperren&aktion=blacklist&id=$id&neuer_blacklist[u_nick]=$uname\">" . $zusatztxt . $t['sperren_menue3'] . "</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=sperren&aktion=blacklist_neu&id=$id\">" . $t['sperren_menue6'] . "</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/sperren.php');
				
			break;
			
		case "freunde":
			// Freunde anzeigen
			require_once("functions/functions-freunde.php");
			
			// Menü ausgeben
			$box = $t['freunde_menue1'];
			$text = "<a href=\"inhalt.php?seite=freunde&id=$id\">$t[freunde_menue2]</a>\n"
			. "| <a href=\"inhalt.php?seite=freunde&aktion=neu&id=$id\">$t[freunde_menue3]</a>\n"
			. "| <a href=\"inhalt.php?seite=freunde&aktion=bestaetigen&id=$id\">$t[freunde_menue4]</a>\n";
			if ($admin) {
				$text .= "| <a href=\"inhalt.php?seite=freunde&aktion=admins&id=$id\">$t[freunde_menue5]n</a>\n";
			}
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-community#freunde&id=$id\">$t[freunde_menue6]</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/freunde.php');
			
			break;
				
		case "top10":
			// Top 10/100 anzeigen
			
			// Menü ausgeben
			$box = $t['top_menue1'];
			$text = "<a href=\"inhalt.php?seite=top10&id=$id\">".$t['top_menue2']."</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=top10&aktion=top100&id=$id\">".$t['top_menue3']."</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-community#punkte&id=$id\">".$t['top_menue4']."</a>\n";
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