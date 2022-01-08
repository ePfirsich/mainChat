<?php
require_once("functions/functions.php");
require_once("functions/functions-forum.php");
require_once("functions/functions-formulare.php");
require_once("languages/$sprache-smilies.php");
require_once("languages/$sprache-forum.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
if($id == "") {
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_URL);
}

$th_id = filter_input(INPUT_GET, 'th_id', FILTER_SANITIZE_NUMBER_INT);
if($th_id == "") {
	$th_id = filter_input(INPUT_POST, 'th_id', FILTER_SANITIZE_NUMBER_INT);
}

$po_tiefe = filter_input(INPUT_GET, 'po_tiefe', FILTER_SANITIZE_NUMBER_INT);

$fo_id = filter_input(INPUT_GET, 'fo_id', FILTER_SANITIZE_NUMBER_INT);
$po_id = filter_input(INPUT_GET, 'po_id', FILTER_SANITIZE_NUMBER_INT);
$seite = filter_input(INPUT_GET, 'seite', FILTER_SANITIZE_NUMBER_INT);
$thread = filter_input(INPUT_GET, 'thread', FILTER_SANITIZE_NUMBER_INT);

$po_vater_id = filter_input(INPUT_GET, 'po_vater_id', FILTER_SANITIZE_NUMBER_INT);
if($po_vater_id == "") {
	$po_vater_id = filter_input(INPUT_POST, 'po_vater_id', FILTER_SANITIZE_NUMBER_INT);
}

$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
if($aktion == "") {
	$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
}

// Benutzerdaten setzen
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == "") {
	die;
}

// In Session merken, dass Text im "Chat" geschrieben wurde
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

switch ($aktion) {
	// Suche
	case "suchergebnisse":
		// Suche anzeigen
		$box = $t['forum_header'];
		$text = such_bereich();
		zeige_tabelle_zentriert($box, $text);
		
		echo '<br>';
		flush();
		such_ergebnis();
		break;
		
	case "suche":
		// Suche anzeigen
		$box = $t['forum_header'];
		$text = such_bereich();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	//Aktionen, die das Forum betreffen
	case "forum_neu":
		// Forum anzeigen
		$box = $t['forum_header'];
		$text = maske_forum();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_anlegen":
		$missing = check_input("forum");
		if (!$missing) {
			schreibe_forum();
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = forum_liste();
			zeige_tabelle_zentriert($box, $text);
		} else {
			zeige_tabelle_zentriert($t['fehlermeldung'], $missing);
			
			// Forum anzeigen
			$box = $t['forum_header'];
			$text = maske_forum();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "forum_editieren":
		$missing = check_input("forum");
		if (!$missing) {
			aendere_forum();
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = forum_liste();
			zeige_tabelle_zentriert($box, $text);
		} else {
			zeige_tabelle_zentriert($t['fehlermeldung'], $missing);
			
			// Forum anzeigen
			$box = $t['forum_header'];
			$text = maske_forum($fo_id);
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "forum_up":
		forum_up($fo_id, $fo_order);
		
		// Forenübersicht anzeigen
		$box = $t['forum_header'];
		$text = forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_down":
		forum_down($fo_id, $fo_order);
		
		// Forenübersicht anzeigen
		$box = $t['forum_header'];
		$text = forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_edit":
		// Forum anzeigen
		$box = $t['forum_header'];
		$text = maske_forum($fo_id);
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "forum_delete":
		if ($forum_admin) {
			loesche_forum($fo_id);
		}
		
		// Forenübersicht anzeigen
		$box = $t['forum_header'];
		$text = forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	//Aktionen, die ein Thema betreffen
	case "thema_neu":
		if ($u_level != "G") {
			// Thema erstellen
			$box = $t['forum_header'];
			$text = maske_thema();
			zeige_tabelle_zentriert($box, $text);
		} else {
			echo "<p>" . $t[forum_gast] . "</p>";
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "thema_anlegen":
		$missing = check_input("thema");
		if (!$missing) {
			schreibe_thema();
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = forum_liste();
			zeige_tabelle_zentriert($box, $text);
		} else {
			zeige_tabelle_zentriert($t['fehlermeldung'], $missing);
			
			// Thema erstellen
			$box = $t['forum_header'];
			$text = maske_thema();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "thema_edit":
		// Thema erstellen
		$box = $t['forum_header'];
		$text = maske_thema($th_id);
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "thema_editieren":
		$missing = check_input("thema");
		if (!$missing) {
			schreibe_thema($th_id);
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = forum_liste();
			zeige_tabelle_zentriert($box, $text);
		} else {
			zeige_tabelle_zentriert($t['fehlermeldung'], $missing);
			
			// Thema erstellen
			$box = $t['forum_header'];
			$text = maske_thema($th_id);
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "thema_up":
		thema_up($th_id, $th_order, $fo_id);
		
		// Forenübersicht anzeigen
		$box = $t['forum_header'];
		$text = forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	case "thema_down":
		thema_down($th_id, $th_order, $fo_id);
		
		// Forenübersicht anzeigen
		$box = $t['forum_header'];
		$text = forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "show_thema":
		// Forum anzeigen
		$box = $t['forum_header'];
		$text = show_thema();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "thema_delete":
		if ($forum_admin) {
			loesche_thema($th_id);
		}
		
		// Forenübersicht anzeigen
		$box = $t['forum_header'];
		$text = forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	case "thema_alles_gelesen":
		thema_alles_gelesen($th_id, $u_id);
		
		// Forenübersicht anzeigen
		$box = $t['forum_header'];
		$text = forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	//Aktionen, die einen Beitrag betreffen
	case "thread_neu":
		$schreibrechte = pruefe_schreibrechte($th_id);
		
		if ($schreibrechte) {
			// Neues Thema erstellen
			$box = $t['forum_header'];
			$text = maske_posting("neuer_thread");
			zeige_tabelle_zentriert($box, $text);
		} else {
			print $t[schreibrechte];
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "sperre_posting":
		if ($forum_admin) {
			sperre_posting($po_id);
			
			// Thema zeigen
			$box = $t['forum_header'];
			$text = show_posting();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "posting_anlegen":
		$thread_gesperrt = false;
		if (isset($thread) && ($thread > 0) && !$forum_admin) {
			$thread_gesperrt = ist_thread_gesperrt($thread);
		}
		$schreibrechte = pruefe_schreibrechte($th_id);
		
		if ($schreibrechte && !$thread_gesperrt) {
			$missing = check_input("posting");
			if (!$missing) {
				$new_po_id = schreibe_posting();
				if ($new_po_id) {
					markiere_als_gelesen($new_po_id, $u_id, $th_id);
					aktion_sofort($new_po_id, $po_vater_id, $thread);
					$po_id = $new_po_id;
					// Punkte gutschreiben
					if ($u_id) {
						verbuche_punkte($u_id);
					}
				}
				
				// Thema zeigen
				$box = $t['forum_header'];
				$text = show_posting();
				zeige_tabelle_zentriert($box, $text);
			} else {
				zeige_tabelle_zentriert($t['fehlermeldung'], $missing);
				
				// Neues Thema erstellen
				$box = $t['forum_header'];
				$text = maske_posting($mode);
				zeige_tabelle_zentriert($box, $text);
			}
		} else {
			echo $t[schreibrechte];
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = "<a href=\"forum.php?id=$id\" class=\"button\" title=\"$t[forum_zur_uebersicht]\"><span class=\"fa fa-commenting icon16\"></span> <span>$t[forum_zur_uebersicht]</span></a>\n";
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
			$box = $t['forum_header'];
			$text = show_posting();
			zeige_tabelle_zentriert($box, $text);
			
			markiere_als_gelesen($po_id, $u_id, $th_id);
		} else {
			print $t[leserechte];
		}
		break;
	case "reply":
		$schreibrechte = pruefe_schreibrechte($th_id);
		if ($schreibrechte) {
			// Neues Thema erstellen
			$box = $t['forum_header'];
			$text = maske_posting("reply");
			zeige_tabelle_zentriert($box, $text);
		} else {
			echo $t[schreibrechte];
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	case "answer":
		$schreibrechte = pruefe_schreibrechte($th_id);
		if ($schreibrechte) {
			// Neues Thema erstellen
			$box = $t['forum_header'];
			$text = maske_posting("answer");
			zeige_tabelle_zentriert($box, $text);
		} else {
			echo $t[schreibrechte];
			
			// Forenübersicht anzeigen
			$box = $t['forum_header'];
			$text = forum_liste();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	case "edit":
		// Neues Thema erstellen
		$box = $t['forum_header'];
		$text = maske_posting("edit");
		zeige_tabelle_zentriert($box, $text);
		
		break;
	case "delete_posting":
		if ($forum_admin) {
			loesche_posting();
		}
		
		// Forum anzeigen
		$box = $t['forum_header'];
		$text = show_thema();
		zeige_tabelle_zentriert($box, $text);
		break;
	case "verschiebe_posting":
		if ($forum_admin) {
			// Thema verschieben
			$box = $t['forum_header'];
			$text = verschiebe_posting();
			zeige_tabelle_zentriert($box, $text);
		}
		break;
	
	case "verschiebe_posting_ausfuehren":
		if ($forum_admin && $verschiebe_von <> $verschiebe_nach) {
			verschiebe_posting_ausfuehren();
		}
		$th_id = $verschiebe_von;
		
		// Forum anzeigen
		$box = $t['forum_header'];
		$text = show_thema();
		zeige_tabelle_zentriert($box, $text);
		break;
	
	//Default
	default:
		// Forenübersicht anzeigen
		$box = $t['forum_header'];
		$text = forum_liste();
		zeige_tabelle_zentriert($box, $text);
		break;
}
?>
</body>
</html>