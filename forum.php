<?php
require_once("functions/functions.php");
require_once("functions/functions-forum.php");
require_once("languages/$sprache-smilies.php");
require_once("languages/$sprache-forum.php");

$th_id = filter_input(INPUT_GET, 'th_id', FILTER_SANITIZE_NUMBER_INT);
if($th_id == "") {
	$th_id = filter_input(INPUT_POST, 'th_id', FILTER_SANITIZE_NUMBER_INT);
}

$fo_id = filter_input(INPUT_GET, 'fo_id', FILTER_SANITIZE_NUMBER_INT);
$po_id = filter_input(INPUT_GET, 'po_id', FILTER_SANITIZE_NUMBER_INT);
$seite = filter_input(INPUT_GET, 'seite', FILTER_SANITIZE_NUMBER_INT);
$fo_name = filter_input(INPUT_POST, 'fo_name', FILTER_SANITIZE_STRING);
$po_titel = filter_input(INPUT_POST, 'po_titel', FILTER_SANITIZE_STRING);
$po_text = filter_input(INPUT_POST, 'po_text', FILTER_SANITIZE_STRING);

$th_name = filter_input(INPUT_POST, 'th_name', FILTER_SANITIZE_STRING);
$th_desc = filter_input(INPUT_POST, 'th_desc', FILTER_SANITIZE_STRING);
$fo_id = filter_input(INPUT_POST, 'fo_id', FILTER_SANITIZE_STRING);

$thread = filter_input(INPUT_GET, 'thread', FILTER_SANITIZE_NUMBER_INT);
if($thread == "") {
	$thread = filter_input(INPUT_POST, 'thread', FILTER_SANITIZE_NUMBER_INT);
}

$po_vater_id = filter_input(INPUT_GET, 'po_vater_id', FILTER_SANITIZE_NUMBER_INT);
if($po_vater_id == "") {
	$po_vater_id = filter_input(INPUT_POST, 'po_vater_id', FILTER_SANITIZE_NUMBER_INT);
}

$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
if($aktion == "") {
	$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
}

// Suche
$suche['text'] = filter_input(INPUT_POST, 'suche_text', FILTER_SANITIZE_STRING);
$suche['thema'] = filter_input(INPUT_POST, 'suche_thema', FILTER_SANITIZE_STRING);
$suche['modus'] = filter_input(INPUT_POST, 'suche_modus', FILTER_SANITIZE_STRING);
$suche['ort'] = filter_input(INPUT_POST, 'suche_ort', FILTER_SANITIZE_STRING);
$suche['zeit'] = filter_input(INPUT_POST, 'suche_zeit', FILTER_SANITIZE_STRING);
$suche['username'] = filter_input(INPUT_POST, 'suche_username', FILTER_SANITIZE_STRING);
$suche['u_id'] = filter_input(INPUT_POST, 'suche_u_id', FILTER_SANITIZE_STRING);
$suche['sort'] = filter_input(INPUT_POST, 'suche_sort', FILTER_SANITIZE_STRING);

// Benutzerdaten setzen
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// In der Tabelle "online" merken, dass Text im "Chat" geschrieben wurde
$query = "UPDATE online SET o_timeout_zeit=DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), o_timeout_warnung = 0 WHERE o_user=" . intval($u_id);
$result = sqlUpdate($query, true);

//gelesene Beiträge lesen
lese_gelesene_postings($u_id);

//Admin fuer Forum darf kein Temp-Admin sein
if ($u_level == "S" || $u_level == "C") {
	$forum_admin = TRUE;
} else {
	$forum_admin = FALSE;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe']);

echo "<body>\n";

// Menü ausgeben
$box = $lang['forum_header'];
$text = "<a href=\"forum.php\">$lang[forum_menue_forenuebersicht_anzeigen]</a>\n";
$text .= "| <a href=\"forum.php?aktion=suche\">$lang[forum_button_suche]</a>\n";
if ($forum_admin && !$aktion) {
	$text .= "| <a href=\"forum.php?aktion=kategorie_neu\">$lang[forum_kategorie_anlegen]</a>\n";
}
zeige_tabelle_zentriert($box, $text);

// Nur Foren-Admins dürfen Kategorien und Foren anlegen und editieren
if(!$forum_admin) {
	$nur_admin_seiten = array('kategorie_neu', 'kategorie_anlegen', 'kategorie_editieren', 'kategorie_up', 'kategorie_down', 'kategorie_edit', 'kategorie_delete', 'forum_neu',
								'forum_edit', 'forum_editieren', 'forum_up', 'forum_down', 'forum_delete', 'sperre_posting', 'delete_posting');
	if(in_array($aktion, $nur_admin_seiten, true)) {
		$aktion = "";
	}
}

$text = "";
switch ($aktion) {
	// Suche
	case "suchergebnisse":
		// Suche anzeigen
		$box = $lang['forum_kopfzeile_suche'];
		$text .= such_bereich();
		
		flush();
		$text .= such_ergebnis();
		
		zeige_tabelle_zentriert($box, $text);
		break;
		
	case "suche":
		// Suche anzeigen
		$box = $lang['forum_kopfzeile_suche'];
		$text .= such_bereich();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	//Aktionen, die das Forum betreffen
	case "kategorie_neu":
		// Forum anzeigen
		$box = $lang['forum_kopfzeile_kategorie_anlegen'];
		$text .= maske_kategorie();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "kategorie_anlegen":
		$fehlermeldung = check_input("kategorie");
		if (!$fehlermeldung) {
			schreibe_forum();
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		} else {
			$text .= hinweis($fehlermeldung, "fehler");
			
			// Forum anzeigen
			$box = $lang['forum_kopfzeile_kategorie_anlegen'];
			$text .= maske_kategorie();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "kategorie_editieren":
		$fehlermeldung = check_input("kategorie");
		if (!$fehlermeldung) {
			aendere_forum();
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_kategorie_editieren'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		} else {
			$text .= hinweis($fehlermeldung, "fehler");
			
			// Forum anzeigen
			$box = $lang['forum_kopfzeile_kategorie_editieren'];
			$text .= maske_kategorie($fo_id);
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "kategorie_up":
		kategorie_up($fo_id, $fo_order);
		
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "kategorie_down":
		kategorie_down($fo_id, $fo_order);
		
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "kategorie_edit":
		// Kategorie editieren
		$box = $lang['forum_kopfzeile_kategorie_editieren'];
		$text .= maske_kategorie($fo_id);
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "kategorie_delete":
		$text .= loesche_kategorie($fo_id);
		
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_neu":
		// Forum erstellen
		$box = $lang['forum_kopfzeile_forum_anlegen'];
		$text .= maske_forum();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_anlegen":
		$fehlermeldung = check_input("forum");
		if (!$fehlermeldung) {
			schreibe_thema();
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		} else {
			$text .= hinweis($fehlermeldung, "fehler");
			
			// Thema erstellen
			$box = $lang['forum_kopfzeile_forum_anlegen'];
			$text .= maske_forum();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "forum_edit":
		// Forum editieren
		$box = $lang['forum_kopfzeile_forum_editieren'];
		$text .= maske_forum($th_id);
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_editieren":
		$fehlermeldung = check_input("forum");
		if (!$fehlermeldung) {
			schreibe_thema($th_id);
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		} else {
			$text .= hinweis($fehlermeldung, "fehler");
			
			// Thema erstellen
			$box = $lang['forum_kopfzeile_forum_editieren'];
			$text .= maske_forum($th_id);
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "forum_up":
		forum_up($th_id, $th_order, $fo_id);
		
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	case "forum_down":
		forum_down($th_id, $th_order, $fo_id);
		
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "show_forum":
		// Forum anzeigen
		$box = $lang['forum_kopfzeile_forum'];
		$text .= show_forum();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_delete":
		$text .= loesche_thema($th_id);
		
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_als_gelesen":
		forum_alles_gelesen($u_id, $th_id);
		
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
		
	case "foren_als_gelesen":
		forum_alles_gelesen($u_id);
		
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	//Aktionen, die einen Beitrag betreffen
	case "thread_neu":
		$schreibrechte = pruefe_schreibrechte($th_id);
		
		if ($schreibrechte) {
			// Neues Thema erstellen
			$box = $lang['thema_erstellen'];
			$text .= maske_posting("neuer_thread");
			zeige_tabelle_zentriert($box, $text);
		} else {
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "sperre_posting":
		sperre_posting($po_id);
		
		// Thema zeigen
		$box = $lang['forum_kopfzeile_thema'];
		$text .= show_posting();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "posting_anlegen":
		$thread_gesperrt = false;
		if (isset($thread) && ($thread > 0) && !$forum_admin) {
			$thread_gesperrt = ist_thread_gesperrt($thread);
		}
		$schreibrechte = pruefe_schreibrechte($th_id);
		
		if ($schreibrechte && !$thread_gesperrt) {
			$fehlermeldung = check_input("posting");
			if (!$fehlermeldung) {
				$new_po_id = schreibe_posting();
				if ($new_po_id) {
					markiere_als_gelesen($new_po_id, $u_id, $th_id);
					aktion_sofort($new_po_id, $po_vater_id, $thread);
					$po_id = $new_po_id;
					// Punkte gutschreiben
					if ($u_id) {
						$text .= verbuche_punkte($u_id);
					}
				}
				
				// Thema zeigen
				$box = $lang['forum_kopfzeile_thema'];
				$text .= show_posting();
				zeige_tabelle_zentriert($box, $text);
			} else {
				$text .= hinweis($fehlermeldung, "fehler");
				
				// Neues Thema erstellen
				$box = $lang['forum_kopfzeile_thema_erstellen'];
				$text .= maske_posting($mode);
				zeige_tabelle_zentriert($box, $text);
			}
		} else {
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	case "show_posting":
		// Falsch, die Leserechte anhand der $th_id die übergeben wurde zu überprüfen
		// $leserechte=pruefe_leserechte($th_id);
	
		// Richtig, die $th_id anhand der $po_id zu bestimmen und zu prüfen
		$leserechte = pruefe_leserechte(hole_themen_id_anhand_posting_id($po_id));
		if ($leserechte) {
			// Thema zeigen
			$box = $lang['forum_kopfzeile_thema'];
			$text .= show_posting();
			zeige_tabelle_zentriert($box, $text);
			
			markiere_als_gelesen($po_id, $u_id, $th_id);
		} else {
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	case "reply":
		$schreibrechte = pruefe_schreibrechte($th_id);
		if ($schreibrechte) {
			// Neues Thema erstellen
			$box = $lang['forum_kopfzeile_thema'];
			$text .= maske_posting("reply");
			zeige_tabelle_zentriert($box, $text);
		} else {
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	case "answer":
		$schreibrechte = pruefe_schreibrechte($th_id);
		if ($schreibrechte) {
			// Neues Thema erstellen
			$box = $lang['forum_kopfzeile_thema'];
			$text .= maske_posting("answer");
			zeige_tabelle_zentriert($box, $text);
		} else {
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	case "edit":
		// Neues Thema erstellen
		$box = $lang['forum_kopfzeile_thema_editieren'];
		$text .= maske_posting("edit");
		zeige_tabelle_zentriert($box, $text);
		
		break;
	case "delete_posting":
		$text .= loesche_posting();
		
		// Forum anzeigen
		$box = $lang['forum_kopfzeile_forum'];
		$text .= show_forum();
		zeige_tabelle_zentriert($box, $text);
		break;
	case "verschiebe_posting":
		if ($forum_admin) {
			// Thema verschieben
			$box = $lang['forum_kopfzeile_thema_verschieben'];
			$text .= verschiebe_posting();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "verschiebe_posting_ausfuehren":
		if ($forum_admin && $verschiebe_von <> $verschiebe_nach) {
			verschiebe_posting_ausfuehren();
		}
		$th_id = $verschiebe_von;
		
		// Forum anzeigen
		$box = $lang['forum_kopfzeile_forum'];
		$text .= show_forum();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	//Default
	default:
		// Forenübersicht anzeigen
		$box = $lang['forum_kopfzeile_forenuebersicht'];
		
		$text .= forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
}
?>
</body>
</html>