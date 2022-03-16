<?php
require_once("functions/functions.php");
require_once("functions/functions-forum.php");
require_once("languages/$sprache-smilies.php");
require_once("languages/$sprache-forum.php");

$bereich = filter_input(INPUT_GET, 'bereich', FILTER_SANITIZE_URL);

$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
if($aktion == "") {
	$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
}

$formular = filter_input(INPUT_POST, 'formular', FILTER_SANITIZE_NUMBER_INT);

$mode = filter_input(INPUT_POST, 'mode', FILTER_SANITIZE_STRING);
$seite = filter_input(INPUT_GET, 'seite', FILTER_SANITIZE_NUMBER_INT);


// Forum -> Kategorien
$kat_id = filter_input(INPUT_GET, 'kat_id', FILTER_SANITIZE_NUMBER_INT);
if($kat_id == "") {
	$kat_id = filter_input(INPUT_POST, 'kat_id', FILTER_SANITIZE_NUMBER_INT);
}
$kat_name = filter_input(INPUT_POST, 'kat_name', FILTER_SANITIZE_STRING);
$kat_gast = filter_input(INPUT_POST, 'kat_gast', FILTER_SANITIZE_NUMBER_INT);
$kat_user = filter_input(INPUT_POST, 'kat_user', FILTER_SANITIZE_NUMBER_INT);
$kat_order = filter_input(INPUT_GET, 'kat_order', FILTER_SANITIZE_NUMBER_INT);


// Forum -> Foren
$forum_id = filter_input(INPUT_GET, 'forum_id', FILTER_SANITIZE_NUMBER_INT);
if($forum_id == "") {
	$forum_id = filter_input(INPUT_POST, 'forum_id', FILTER_SANITIZE_NUMBER_INT);
}
$forum_name = filter_input(INPUT_POST, 'forum_name', FILTER_SANITIZE_STRING);
$forum_beschreibung = filter_input(INPUT_POST, 'forum_beschreibung', FILTER_SANITIZE_STRING);
$forum_kategorie_id = filter_input(INPUT_GET, 'forum_kategorie_id', FILTER_SANITIZE_NUMBER_INT);
if($forum_kategorie_id == "") {
	$forum_kategorie_id = filter_input(INPUT_POST, 'forum_kategorie_id', FILTER_SANITIZE_NUMBER_INT);
}
$forum_order = filter_input(INPUT_GET, 'forum_order', FILTER_SANITIZE_NUMBER_INT);


// Forum -> Themen
// Forum -> Beiträge
$beitrag_id = filter_input(INPUT_GET, 'beitrag_id', FILTER_SANITIZE_NUMBER_INT);
if($beitrag_id == "") {
	$beitrag_id = filter_input(INPUT_POST, 'beitrag_id', FILTER_SANITIZE_NUMBER_INT);
}
$beitrag_forum_id = filter_input(INPUT_POST, 'beitrag_forum_id', FILTER_SANITIZE_NUMBER_INT);
$beitrag_forum_id_neu = filter_input(INPUT_POST, 'beitrag_forum_id_neu', FILTER_SANITIZE_NUMBER_INT);
$beitrag_titel = filter_input(INPUT_POST, 'beitrag_titel', FILTER_SANITIZE_STRING);
$beitrag_thema_id = filter_input(INPUT_GET, 'beitrag_thema_id', FILTER_SANITIZE_NUMBER_INT);
if($beitrag_thema_id == "") {
	$beitrag_thema_id = filter_input(INPUT_POST, 'beitrag_thema_id', FILTER_SANITIZE_NUMBER_INT);
}
$beitrag_text = filter_input(INPUT_POST, 'beitrag_text', FILTER_SANITIZE_STRING);
$beitrag_angepinnt = filter_input(INPUT_POST, 'beitrag_angepinnt', FILTER_SANITIZE_NUMBER_INT);
$beitrag_gesperrt = filter_input(INPUT_POST, 'beitrag_gesperrt', FILTER_SANITIZE_NUMBER_INT);

$thread = filter_input(INPUT_GET, 'thread', FILTER_SANITIZE_NUMBER_INT);
if($thread == "") {
	$thread = filter_input(INPUT_POST, 'thread', FILTER_SANITIZE_NUMBER_INT);
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
id_lese();

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// In der Tabelle "online" merken, dass Text im "Chat" geschrieben wurde
pdoQuery("UPDATE `online` SET `o_timeout_zeit` = DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), `o_timeout_warnung` = 0 WHERE `o_user` = :o_user", [':o_user'=>$u_id]);

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
$text = "<a href=\"forum.php?bereich=forum\">$lang[forum_menue_forenuebersicht_anzeigen]</a>\n";
$text .= "| <a href=\"forum.php?bereich=forum&aktion=suche\">$lang[forum_button_suche]</a>\n";
if ($forum_admin && !$aktion) {
	$text .= "| <a href=\"forum.php?bereich=forum&aktion=kategorie_aendern\">$lang[forum_kategorie_anlegen]</a>\n";
}
zeige_tabelle_zentriert($box, $text);

// Nur Foren-Admins dürfen Kategorien und Foren anlegen und editieren
if(!$forum_admin) {
	$nur_admin_seiten = array('kategorie_aendern', 'kategorie_up', 'kategorie_down', 'kategorie_delete', 'forum_aendern', 'forum_up', 'forum_down', 'forum_delete', 'verschiebe_thema', 'delete_posting');
	if(in_array($aktion, $nur_admin_seiten, true)) {
		$aktion = "";
	}
}

$text = "";
if($bereich == "forum") {
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
		case "kategorie_aendern":
			if($kat_id == 0 || $kat_id == "") {
				// Neue Kategorie anlegen
				$box = $lang['forum_kopfzeile_kategorie_anlegen'];
				$kat_id = 0;
			} else {
				// Kategorie editieren
				$box = $lang['forum_kopfzeile_kategorie_editieren'];
			}
			
			if($formular == 1) {
				// Formular wurde abgesendet
				$kat_admin = $kat_gast + $kat_user + 1;
				$fehlermeldung = "";
				
				if($kat_name == "") {
					$fehlermeldung = $lang['forum_fehlermeldung_kategorie_name'];
					$text .= hinweis($fehlermeldung, "fehler");
				}
				if($fehlermeldung != "") {
					// Formular erneut anzeigen
					$text .= maske_kategorie($kat_id, $kat_name, $kat_admin);
					
					zeige_tabelle_zentriert($box, $text);
				} else {
					// Änderung speichern
					erstelle_editiere_kategorie($kat_id, $kat_name, $kat_admin);
					
					// Erfolgsmeldung anzeigen
					if($kat_id == 0) {
						$erfolgsmeldung = $lang['forum_erfolgsmeldung_kategorie_erstellt'];
					} else {
						$erfolgsmeldung = $lang['forum_erfolgsmeldung_kategorie_editiert'];
					}
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
					
					// Forenübersicht anzeigen
					$box = $lang['forum_kopfzeile_forenuebersicht'];
					$text .= forum_liste();
					zeige_tabelle_zentriert($box, $text);
				}
			} else {
				// Erster Aufruf des Formulars
				$kat_name = "";
				$kat_admin = 0;
				$text .= maske_kategorie($kat_id, $kat_name, $kat_admin);
				
				zeige_tabelle_zentriert($box, $text);
			}
		break;
		
		case "kategorie_up":
			kategorie_up($kat_id, $kat_order);
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		break;
		
		case "kategorie_down":
			kategorie_down($kat_id, $kat_order);
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		break;
		
		case "kategorie_delete":
			$text .= loesche_kategorie($kat_id);
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		break;
		
		case "forum_aendern":
			if($forum_kategorie_id != null && $forum_kategorie_id != "" && $forum_kategorie_id != 0) {
				if($forum_id == 0 || $forum_id == "") {
					// Neues Forum anlegen
					$box = $lang['forum_kopfzeile_forum_anlegen'];
					$forum_id = 0;
				} else {
					// Forum editieren
					$box = $lang['forum_kopfzeile_forum_editieren'];
				}
				
				if($formular == 1) {
					// Formular wurde abgesendet
					$fehlermeldung = "";
					
					if($forum_name == "") {
						$fehlermeldung = $lang['forum_fehlermeldung_forum_name'];
						$text .= hinweis($fehlermeldung, "fehler");
					}
					if($fehlermeldung != "") {
						// Formular erneut anzeigen
						$text .= maske_forum($forum_id, $forum_kategorie_id, $forum_name, $forum_beschreibung);
						
						zeige_tabelle_zentriert($box, $text);
					} else {
						// Änderung speichern
						erstelle_editiere_forum($forum_id, $forum_kategorie_id, $forum_name, $forum_beschreibung);
						
						// Erfolgsmeldung anzeigen
						if($forum_id == 0) {
							$erfolgsmeldung = $lang['forum_erfolgsmeldung_forum_erstellt'];
						} else {
							$erfolgsmeldung = $lang['forum_erfolgsmeldung_forum_editiert'];
						}
						$text .= hinweis($erfolgsmeldung, "erfolgreich");
						
						// Forenübersicht anzeigen
						$box = $lang['forum_kopfzeile_forenuebersicht'];
						$text .= forum_liste();
						zeige_tabelle_zentriert($box, $text);
					}
				} else {
					// Erster Aufruf des Formulars
					if($forum_id == null || $forum_id == "") {
						$forum_id = 0;
					}
					$forum_name = "";
					$forum_beschreibung = "";
					$text .= maske_forum($forum_id, $forum_kategorie_id, $forum_name, $forum_beschreibung);
					
					zeige_tabelle_zentriert($box, $text);
				}
			} else {
				// Forenübersicht anzeigen
				$box = $lang['forum_kopfzeile_forenuebersicht'];
				$text .= forum_liste();
				zeige_tabelle_zentriert($box, $text);
			}
		break;
		
		case "forum_up":
			forum_up($forum_id, $forum_order, $kat_id);
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		break;
		
		case "forum_down":
			forum_down($forum_id, $forum_order, $kat_id);
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		break;
		
		case "show_forum":
			// Forum anzeigen
			$box = $lang['forum_kopfzeile_forum'];
			$text .= show_forum($forum_id);
			zeige_tabelle_zentriert($box, $text);
		break;
		
		case "forum_delete":
			$text .= loesche_forum($forum_id);
			
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		break;
		
		case "forum_als_gelesen":
			forum_alles_gelesen($u_id, $forum_id);
			
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
		
		case "thema_aendern":
			$schreibrechte = pruefe_schreibrechte($forum_id);
			if($schreibrechte && $forum_id != null && $forum_id != "" && $forum_id != 0) {
				if($beitrag_id == 0 || $beitrag_id == "") {
					// Neues Thema anlegen
					$box = $lang['forum_kopfzeile_thema_erstellen'];
					$beitrag_id = 0;
				} else {
					// Thema editieren
					$box = $lang['forum_kopfzeile_thema_editieren'];
				}
				
				if($formular == 1) {
					// Formular wurde abgesendet
					$fehlermeldung = "";
					
					if($beitrag_titel == "") {
						$fehlermeldung = $lang['forum_fehlermeldung_thema_titel'];
						$text .= hinweis($fehlermeldung, "fehler");
					}
					
					// Nur beim Erstellen eines Themas kann Text eingetragen werden
					if($beitrag_id == 0 && $beitrag_text == "") {
						$fehlermeldung = $lang['forum_fehlermeldung_thema_text'];
						$text .= hinweis($fehlermeldung, "fehler");
					}
					
					if($beitrag_angepinnt == "" || $beitrag_gesperrt == "") {
						$fehlermeldung = $lang['forum_fehlermeldung_thema_fehler'];
						$text .= hinweis($fehlermeldung, "fehler");
					}
					
					if($fehlermeldung != "") {
						// Formular erneut anzeigen
						$text .= maske_thema($beitrag_id, $beitrag_titel, $beitrag_text, $forum_id, $beitrag_angepinnt, $beitrag_gesperrt, $benutzerdaten['u_layout_farbe']);
						
						zeige_tabelle_zentriert($box, $text);
					} else {
						// Änderung speichern
						$inhalt = erstelle_editiere_thema($beitrag_id, $beitrag_titel, $beitrag_text, $forum_id, $beitrag_angepinnt, $beitrag_gesperrt);
						
						$text .= $inhalt[0];
						$beitrag_id = $inhalt[1];
						
						$leserechte = pruefe_leserechte(hole_themen_id_anhand_posting_id($beitrag_id));
						if ($leserechte) {
							if( zeige_forenthema($beitrag_id) ) {
								// Thema zeigen
								$box = $lang['forum_kopfzeile_thema'];
								$text .= zeige_forenthema($beitrag_id);
								zeige_tabelle_zentriert($box, $text);
								
								markiere_als_gelesen($beitrag_id, $u_id, $forum_id);
							} else {
								// Kein Thema gefunden -> Forenübersicht anzeigen
								$box = $lang['forum_kopfzeile_forenuebersicht'];
								
								$text .= forum_liste();
								zeige_tabelle_zentriert($box, $text);
							}
						} else {
							// Forenübersicht anzeigen
							$box = $lang['forum_kopfzeile_forenuebersicht'];
							$text .= forum_liste();
							zeige_tabelle_zentriert($box, $text);
						}
					}
				} else {
					// Erster Aufruf des Formulars
					if($beitrag_id == null || $beitrag_id == "") {
						$beitrag_id = 0;
					}
					$beitrag_titel = "";
					$beitrag_text = "";
					$text .= maske_thema($beitrag_id, $beitrag_titel, $beitrag_text, $forum_id, $beitrag_angepinnt, $beitrag_gesperrt, $benutzerdaten['u_layout_farbe']);
					
					zeige_tabelle_zentriert($box, $text);
				}
			} else {
				// Forenübersicht anzeigen
				$box = $lang['forum_kopfzeile_forenuebersicht'];
				$text .= forum_liste();
				zeige_tabelle_zentriert($box, $text);
			}
		break;
		
		case "sperre_thema":
			$text .= sperre_thema($forum_id);
			
			if( zeige_forenthema($beitrag_id) ) {
				// Thema zeigen
				$box = $lang['forum_kopfzeile_thema'];
				$text .= zeige_forenthema($beitrag_id);
				zeige_tabelle_zentriert($box, $text);
			} else {
				// Kein Thema gefunden -> Forenübersicht anzeigen
				$box = $lang['forum_kopfzeile_forenuebersicht'];
				
				$text .= forum_liste();
				zeige_tabelle_zentriert($box, $text);
			}
		break;
		
		case "posting_anlegen":
			$thema_gesperrt = false;
			if (isset($thread) && ($thread > 0) && !$forum_admin) {
				$thema_gesperrt = ist_thema_gesperrt($forum_id);
			}
			$schreibrechte = pruefe_schreibrechte($forum_id);
			
			if ($schreibrechte && !$thema_gesperrt) {
				$fehlermeldung = check_input();
				if (!$fehlermeldung) {
					$new_beitrag_id = schreibe_posting($mode, $beitrag_id, $forum_id, $beitrag_titel, $beitrag_text, $beitrag_angepinnt, $beitrag_gesperrt);
					if ($new_beitrag_id) {
						markiere_als_gelesen($new_beitrag_id, $u_id, $forum_id);
						aktion_sofort($new_beitrag_id, $beitrag_thema_id, $thread);
						// Punkte gutschreiben
						if ($u_id) {
							$text .= verbuche_punkte($u_id);
						}
					}
					
					if( zeige_forenthema($beitrag_id) ) {
						// Thema zeigen
						$box = $lang['forum_kopfzeile_thema'];
						$text .= zeige_forenthema($beitrag_id);
						zeige_tabelle_zentriert($box, $text);
					} else {
						// Kein Thema gefunden -> Forenübersicht anzeigen
						$box = $lang['forum_kopfzeile_forenuebersicht'];
						
						$text .= forum_liste();
						zeige_tabelle_zentriert($box, $text);
					}
				} else {
					$text .= hinweis($fehlermeldung, "fehler");
					
					// Neues Thema erstellen
					$box = $lang['forum_kopfzeile_thema_erstellen'];
					$text .= maske_thema($beitrag_id, $beitrag_titel, $beitrag_text, $beitrag_forum_id, $beitrag_angepinnt, $beitrag_gesperrt, $benutzerdaten['u_layout_farbe']);
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
			$leserechte = pruefe_leserechte(hole_themen_id_anhand_posting_id($beitrag_id));
			if ($leserechte) {
				if( zeige_forenthema($beitrag_id) ) {
					// Thema zeigen
					$box = $lang['forum_kopfzeile_thema'];
					$text .= zeige_forenthema($beitrag_id);
					zeige_tabelle_zentriert($box, $text);
					
					markiere_als_gelesen($beitrag_id, $u_id, $forum_id);
				} else {
					// Kein Thema gefunden -> Forenübersicht anzeigen
					$box = $lang['forum_kopfzeile_forenuebersicht'];
					
					$text .= forum_liste();
					zeige_tabelle_zentriert($box, $text);
				}
			} else {
				// Forenübersicht anzeigen
				$box = $lang['forum_kopfzeile_forenuebersicht'];
				$text .= forum_liste();
				zeige_tabelle_zentriert($box, $text);
			}
		break;
		
		case "reply":
			$schreibrechte = pruefe_schreibrechte($forum_id);
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
			$schreibrechte = pruefe_schreibrechte($forum_id);
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
			$text .= loesche_posting($forum_id, $beitrag_thema_id, $beitrag_id);
			
			// Forum anzeigen
			$box = $lang['forum_kopfzeile_forum'];
			$text .= show_forum($forum_id);
			zeige_tabelle_zentriert($box, $text);
			break;
		
		case "verschiebe_thema":
			// Thema verschieben
			if($formular == 1) {
				// Formular wurde abgesendet
				if ($beitrag_forum_id != $beitrag_forum_id_neu) {
					verschiebe_thema_ausfuehren($beitrag_id, $beitrag_forum_id, $beitrag_forum_id_neu);
					
					$erfolgsmeldung = $lang['forum_erfolgsmeldung_thema_verschoben'];
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
				} else {
					$fehlermeldung = $lang['forum_fehlermeldung_thema_nicht_verschoben'];
					$text .= hinweis($fehlermeldung, "fehler");
				}
				
				if( zeige_forenthema($beitrag_id) ) {
					// Thema zeigen
					$box = $lang['forum_kopfzeile_thema'];
					$text .= zeige_forenthema($beitrag_id);
					zeige_tabelle_zentriert($box, $text);
				} else {
					// Kein Thema gefunden -> Forenübersicht anzeigen
					$box = $lang['forum_kopfzeile_forenuebersicht'];
					
					$text .= forum_liste();
					zeige_tabelle_zentriert($box, $text);
				}
			}
		break;
		
		default:
			// Forenübersicht anzeigen
			$box = $lang['forum_kopfzeile_forenuebersicht'];
			
			$text .= forum_liste();
			zeige_tabelle_zentriert($box, $text);
		break;
	}
} else {
	echo "Noch nicht konfiguriert";
}
?>
</body>
</html>