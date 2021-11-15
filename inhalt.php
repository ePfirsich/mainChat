<?php
require_once("functions.php");
$seite = filter_input(INPUT_GET, 'seite', FILTER_SANITIZE_URL);
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
if( $id == '') {
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_URL);
}

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, o_js
id_lese($id);

if( isset($u_id) ) {
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
}

// Wird die Seite aufgebaut?
$kein_seitenaufruf = false;

// Allgemeine Übersetzungen
require "languages/$sprache.php";

// Übersetzungen der entsprechenden Seiten einbinden
if (file_exists("languages/$sprache-$seite.php")) {
	require "languages/$sprache-$seite.php";
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
window.focus()
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
</script>
<?php
zeige_header_ende();

$user_eingeloggt = (strlen($u_id) != 0);

// Bestimmte Seiten dürfen nur im eingeloggten Zustand aufgerufen werden
$kein_aufruf_unter_bestimmten_bedinungen = false;
$nur_eingeloggten_seiten = array('raum', 'profil', 'einstellungen');
$nur_eingeloggten_seiten_und_registriert = array('nachrichten', 'top10', 'freunde');
$nur_eingeloggten_seiten_und_admin = array('statistik', 'blacklist', 'sperren');
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
	zeige_tabelle_zentriert($box, $text, true);
} else if($kein_aufruf_unter_bestimmten_bedinungen) {
		// Seite nicht gefunden oder Datei für die Übersetzung nicht gefunden
		$box = $t['fehler'];
		$text = $t['kein_zugriff'];
		zeige_tabelle_zentriert($box, $text, true);
	} else {
	// Seitenaufruf
	switch ($seite) {
		case "hilfe":
			// Hilfe anzeigen
			
			// Menü ausgeben
			$box = $t['menue1'];
			$text = "<a href=\"inhalt.php?seite=hilfe&id=$id\">$t[menue4]</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-befehle&id=$id\">$t[menue5]</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-sprueche&id=$id\">$t[menue6]</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-community&id=$id\">$t[menue7]</a>\n";
			zeige_tabelle_zentriert($box, $text, true);
			
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
		
		case "raum":
			// Raum anzeigen
			
			// Menü ausgeben
			$box = $t['menue1'];
			$text = "<a href=\"inhalt.php?seite=raum&id=$id\">" . $t['menue8'] . "</a>\n|\n";
			if ($u_level != "G") {
				$text .= "<a href=\"inhalt.php?seite=raum&aktion=neu&id=$id\">" . $t['menue2'] . "</a>\n";
			}
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/raum.php');
			
			break;
		
		case "nachrichten":
			// Nachrichten anzeigen
			require_once('functions-nachrichten.php');
			
			// Menü ausgeben
			$box = $t['menue1'];
			$text = "<a href=\"inhalt.php?seite=nachrichten&id=$id\">" . $t['menue2'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=nachrichten&aktion=neu&id=$id\">" . $t['menue3'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=nachrichten&aktion=papierkorb&id=$id\">" . $t['menue4'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=nachrichten&aktion=papierkorbleeren&id=$id\">" . $t['menue5'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=nachrichten&aktion=mailboxzu&id=$id\">" . $t['menue6'] . "</a>\n|\n"
				. "<a href=\"inhalt.php?seite=hilfe&id=$id&aktion=hilfe-community#mail\">" . $t['menue7'] . "</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/nachrichten.php');
			
			break;
		
		case "profil":
			// Profil anzeigen
			require_once('functions-profil.php');
			
			// Menü ausgeben
			$box = $t['menue1'];
			$text = "<a href=\"inhalt.php?seite=einstellungen&id=$id\">$t[profil_benutzereinstellungen_aendern]</a>\n";
			$text .= "| <a href=\"home.php?id=$id&aktion=aendern\">$t[profil_homepage_bearbeiten]</a>\n";
			if ($u_level == "S") {
				$text .= "| <a href=\"inhalt.php?seite=profil&id=$id&aktion=zeigealle\">$t[profil_alle_profile_ausgeben]</a>\n";
			}
			$text .= "| <a href=\"user.php?id=$id\">$t[profil_benutzer]</a>\n";
			zeige_tabelle_zentriert($box, $text);
				
			require_once('templates/profil.php');
				
			break;
		
		case "einstellungen":
			// Einstellungen anzeigen
			require_once("functions-msg.php");
			require_once('functions-einstellungen.php');
			
			// Menü ausgeben
			if ($u_level != "G") {
				$box = $t['menue4'];
				$ur1 = "inhalt.php?seite=profil&id=$id&aktion=aendern";
				$url = "href=\"$ur1\" ";
				$text = "<a $url>$t[menue7]</a>\n";
				$ur1 = "home.php?id=$id&aktion=aendern";
				$url = "href=\"$ur1\" ";
				$text .= "| <a $url>$t[menue10]</a>\n";
				$ur1 = "inhalt.php?seite=freunde&id=$id";
				$url = "href=\"$ur1\" ";
				$text .= "| <a $url>$t[menue9]</a>\n";
				$ur1 = "aktion.php?id=$id";
				$url = "href=\"$ur1\" ";
				$text .= "| <a $url>$t[menue8]</a>\n";
				zeige_tabelle_zentriert($box, $text);
			}
			
			require_once('templates/einstellungen.php');
			
			break;
		
		case "statistik":
			// Statistik anzeigen
			require_once("functions-statistik.php");
			
			// Menü ausgeben
			$box = $t['statistik1'];
			$text = "[<a href=\"inhalt.php?seite=statistik&aktion=monat&id=$id\">" . $t['statistik3'] . "</a>]\n"
					. "[<a href=\"inhalt.php?seite=statistik&aktion=stunde&id=$id\">" . $t['statistik2'] . "</a>]";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/statistik.php');
			
			break;
		
		case "blacklist":
			// Blacklist anzeigen
			require_once("functions-blacklist.php");
			
			// Menü ausgeben
			$box = $t['menue1'];
			$text = "<a href=\"inhalt.php?seite=blacklist&id=$id\">" . $t['menue2'] . "</a>\n"
			. "| <a href=\"inhalt.php?seite=blacklist&aktion=neu&id=$id\">" . $t['menue3'] . "</a>\n"
			. "| <a href=\"inhalt.php?seite=sperren&id=$id\">" . $t['menue4'] . "</a>\n";
			zeige_tabelle_zentriert($box, $text);
				
			require_once('templates/blacklist.php');
				
			break;
		
		case "sperren":
			// Sperren anzeigen
			require_once("functions-sperren.php");
			
			// Menü ausgeben
			if (isset($uname)) {
				$zusatztxt = "'" . $uname . "'&nbsp;&gt;&gt;&nbsp;";
			} else {
				$zusatztxt = "";
				$uname = "";
			}
			
			$box = $t['menue4'];
			$text = "<a href=\"inhalt.php?seite=sperren&id=$id\">$t[menue1]</a>\n" . "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=neu\">$t[menue2]</a>\n";
			
			$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GLOBAL-'";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) > 0) {
				$text .= "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperre0\">$t[menue5a]</a>\n";
			} else {
				$text .= "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperre1\">$t[menue5b]</a>\n";
			}
			mysqli_free_result($result);
			
			$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GAST-'";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) > 0) {
				$text .= "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperregast0\">$t[menue6a]</a>\n";
			} else {
				$text .= "| <a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperregast1\">$t[menue6b]</a>\n";
			}
			mysqli_free_result($result);
			
			$text .= "| <a href=\"inhalt.php?seite=blacklist&id=$id&neuer_blacklist[u_nick]=$uname\">" . $zusatztxt . $t['menue3'] . "</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/sperren.php');
				
			break;
			
		case "freunde":
			// Nachrichten anzeigen
			require_once('functions-freunde.php');
			
			// Menü ausgeben
			$box = $t['menue1'];
			$text = "<a href=\"inhalt.php?seite=freunde&id=$id\">$t[menue2]</a>\n"
			. "| <a href=\"inhalt.php?seite=freunde&aktion=neu&id=$id\">$t[menue3]</a>\n"
			. "| <a href=\"inhalt.php?seite=freunde&aktion=bestaetigen&id=$id\">$t[menue4]</a>\n";
			if ($admin) {
				$text .= "| <a href=\"inhalt.php?seite=freunde&aktion=admins&id=$id\">$t[menue5]n</a>\n";
			}
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-community#freunde&id=$id\">$t[menue6]</a>\n";
			zeige_tabelle_zentriert($box, $text);
			
			require_once('templates/freunde.php');
			
			break;
				
		case "top10":
			// Nachrichten anzeigen
			
			// Menü ausgeben
			$box = $t['menue1'];
			$text = "<a href=\"inhalt.php?seite=top10&id=$id\">".$t['menue2']."</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=top10&aktion=top100&id=$id\">".$t['menue3']."</a>\n";
			$text .= "| <a href=\"inhalt.php?seite=hilfe&aktion=hilfe-community#punkte&id=$id\">".$t['menue4']."</a>\n";
			zeige_tabelle_zentriert($box, $text);
				
			require_once('templates/top10.php');
					
			break;
		
		default:
			// Seite nicht gefunden
			$box = $t['fehler'];
			$text = $t['seite_nicht_gefunden'];
			zeige_tabelle_zentriert($box, $text, true);
	}
}
?>
</body>
</html>