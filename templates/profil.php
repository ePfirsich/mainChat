<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id)) {
	die;
}

// Prüfung, ob für diesen Benutzer bereits ein profil vorliegt -> in $f lesen und merken
// Falls Array aus Formular übergeben wird, nur ui_id überschreiben
$query = "SELECT * FROM `userinfo` WHERE `ui_userid`=$u_id";
$result = mysqli_query($mysqli_link, $query);
if ($result && mysqli_num_rows($result) != 0) {
	// Bestehendes Profil aus der Datenbank laden
	
	$f = array();
	$f = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$f['u_chathomepage'] = $userdata['u_chathomepage'];
	$profil_gefunden = true;
} else {
	// Neues Profil anlegen
	$f = array();
	$f['ui_id'] = 0;
	$f['ui_userid'] = $u_id;
	$f['ui_wohnort'] = '';
	$f['ui_geburt'] = '';
	$f['ui_geschlecht'] = 0;
	$f['ui_beziehungsstatus'] = 0;
	$f['ui_typ'] = 0;
	$f['ui_homepage'] = '';
	$f['ui_beruf'] = '';
	$f['ui_lieblingsfilm'] = '';
	$f['ui_lieblingsserie'] = '';
	$f['ui_lieblingsbuch'] = '';
	$f['ui_lieblingsschauspieler'] = '';
	$f['ui_lieblingsgetraenk'] = '';
	$f['ui_lieblingsgericht'] = '';
	$f['ui_lieblingsspiel'] = '';
	$f['ui_lieblingsfarbe'] = '';
	$f['ui_hobby'] = '';
	$f['ui_text'] = '';
	$f['ui_hintergrundfarbe'] = $standard_ui_hintergrundfarbe;
	$f['ui_ueberschriften_textfarbe'] = $standard_ui_ueberschriften_textfarbe;
	$f['ui_ueberschriften_hintergrundfarbe'] = $standard_ui_ueberschriften_hintergrundfarbe;
	$f['ui_inhalt_textfarbe'] = $standard_ui_inhalt_textfarbe;
	$f['ui_inhalt_linkfarbe'] = $standard_ui_inhalt_linkfarbe;
	$f['ui_inhalt_linkfarbe_aktiv'] = $standard_ui_inhalt_linkfarbe_aktiv;
	$f['ui_inhalt_hintergrundfarbe'] = $standard_ui_inhalt_hintergrundfarbe;
	$profil_gefunden = false;
}
mysqli_free_result($result);

$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
if( $aktion == '') {
	$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
}

// Profil prüfen und ggf. neu eintragen
if($aktion == "aendern" && $f['ui_userid'] && filter_input(INPUT_POST, 'formular', FILTER_SANITIZE_URL) == "gefuellt") {
	if( !isset($f['ui_id']) || $f['ui_id'] == '' || $f['ui_id'] == 0) {
		$f = array();
		$f['ui_id'] = filter_input(INPUT_POST, 'ui_id', FILTER_SANITIZE_URL);
	} else {
		$ui_id_temp = $f['ui_id'];
		$f = array();
		$f['ui_id'] = $ui_id_temp;
	}
	$f['ui_userid'] = $u_id;
	$f['ui_wohnort'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_wohnort', FILTER_SANITIZE_STRING));
	$f['ui_geburt'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_geburt', FILTER_SANITIZE_STRING));
	$f['ui_geschlecht'] = filter_input(INPUT_POST, 'ui_geschlecht', FILTER_SANITIZE_NUMBER_INT);
	$f['ui_beziehungsstatus'] = filter_input(INPUT_POST, 'ui_beziehungsstatus', FILTER_SANITIZE_NUMBER_INT);
	$f['ui_typ'] = filter_input(INPUT_POST, 'ui_typ', FILTER_SANITIZE_NUMBER_INT);
	$f['ui_homepage'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_homepage', FILTER_SANITIZE_URL));
	$f['ui_beruf'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_beruf', FILTER_SANITIZE_STRING));
	$f['ui_lieblingsfilm'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_lieblingsfilm', FILTER_SANITIZE_STRING));
	$f['ui_lieblingsserie'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_lieblingsserie', FILTER_SANITIZE_STRING));
	$f['ui_lieblingsbuch'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_lieblingsbuch', FILTER_SANITIZE_STRING));
	$f['ui_lieblingsschauspieler'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_lieblingsschauspieler', FILTER_SANITIZE_STRING));
	$f['ui_lieblingsgetraenk'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_lieblingsgetraenk', FILTER_SANITIZE_STRING));
	$f['ui_lieblingsgericht'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_lieblingsgericht', FILTER_SANITIZE_STRING));
	$f['ui_lieblingsspiel'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_lieblingsspiel', FILTER_SANITIZE_STRING));
	$f['ui_lieblingsfarbe'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_lieblingsfarbe', FILTER_SANITIZE_STRING));
	$f['ui_hobby'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_hobby', FILTER_SANITIZE_STRING));
	$f['u_chathomepage'] = filter_input(INPUT_POST, 'u_chathomepage', FILTER_SANITIZE_NUMBER_INT);
	$f['ui_hintergrundfarbe'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_hintergrundfarbe', FILTER_SANITIZE_URL));
	$f['ui_ueberschriften_textfarbe'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_ueberschriften_textfarbe', FILTER_SANITIZE_URL));
	$f['ui_ueberschriften_hintergrundfarbe'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_ueberschriften_hintergrundfarbe', FILTER_SANITIZE_URL));
	$f['ui_inhalt_textfarbe'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_inhalt_textfarbe', FILTER_SANITIZE_URL));
	$f['ui_inhalt_linkfarbe'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_inhalt_linkfarbe', FILTER_SANITIZE_URL));
	$f['ui_inhalt_linkfarbe_aktiv'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_inhalt_linkfarbe_aktiv', FILTER_SANITIZE_URL));
	$f['ui_inhalt_hintergrundfarbe'] = htmlspecialchars(filter_input(INPUT_POST, 'ui_inhalt_hintergrundfarbe', FILTER_SANITIZE_URL));
	$text_inhalt = htmlspecialchars(filter_input(INPUT_POST, 'ui_text'));
	
	// Spezialbehandlung für den Text über sich selbst - Anfang
	
	// Wieso ist "target" in der stopwordliste??
	// Umschiffung der ausfilterung bei: target="_blank"
	$text_inhalt = preg_replace("|target\s*=\s*.\"\s*\_blank\s*.\"|i", '####----TAR----####', $text_inhalt);
	
	// Problem: wenn zeichen z.b. b&#97;ckground codiert sind
	// dann würde dadurch ein sicherheitsloch entstehen
	$text_inhalt = unhtmlentities($text_inhalt);
	
	// $stopwordarray=array("background","java","script","activex","embed","target","javascript");
	$stopwordarray = array("|background|i", "|java|i", "|script|i", "|activex|i", "|target|i", "|javascript|i");
	//$text_inhalt = str_replace("\n", "<br>\n", $text_inhalt);
	
	foreach ($stopwordarray as $stopword) {
		// str_replace ist case sensitive, gefährlichen, wenn JavaScript geschrieben wird, oder Target, oder ActiveX
		while (preg_match($stopword, $text_inhalt)) {
			$text_inhalt = preg_replace($stopword, "", $text_inhalt);
		}
	}
	
	$text_inhalt = str_replace("####----TAR----####", "target=\"_blank\"", $text_inhalt);
	
	// Wir löschen die On-Handler aus der Homepage (einfache Version)
	// on gefolgt von 3-12 Buchstaben wird durch off ersetzt
	
	$text_inhalt = preg_replace('|\son([a-z]{3,12})\s*=|i', ' off\\1=', $text_inhalt);
	$f['ui_text'] = $text_inhalt;
	// Spezialbehandlung für den Text über sich selbst - Ende
	
	$fehlermeldung = "";
	
	// Prüfungen
	if (strlen($f['ui_wohnort']) > 100) {
		$fehlermeldung .= $t['profil_fehler_wohnort'];
	}
	
	if ($f['ui_geburt'] != "" && !preg_match("/^[0-9]{2}[.][0-9]{2}[.][0-9]{4}$/i", $f['ui_geburt'])) {
		$fehlermeldung .= $t['profil_fehler_geburt'];
	}
	
	if ($f['ui_geschlecht'] != "" && $f['ui_geschlecht'] < 0 && $f['ui_geschlecht'] > 3) {
		$fehlermeldung .= $t['profil_fehler_geschlecht'];
	}
	
	if ($f['ui_beziehungsstatus'] != "" && $f['ui_beziehungsstatus'] < 0 && $f['ui_beziehungsstatus'] > 4) {
		$fehlermeldung .= $t['profil_fehler_beziehungsstatus'];
	}
	
	if ($f['ui_typ'] != "" && $f['ui_typ'] < 0 && $f['ui_typ'] > 6) {
		$fehlermeldung .= $t['profil_fehler_typ'];
	}
	
	// Homepage muss http:// oder https://enthalten
	if (isset($f['ui_webseite']) && strlen($f['ui_webseite']) > 0 && !preg_match("/^http:\/\//i", $f['ui_webseite']) && !preg_match("/^https:\/\//i", $f['ui_webseite']) ) {
		$f['ui_webseite'] = "http://" . $f['ui_webseite'];
	}
	
	if (strlen($f['ui_webseite']) > 160) {
		$fehlermeldung .= $t['profil_fehler_homepage'];
	}
	
	if (strlen($f['ui_beruf']) > 100) {
		$fehlermeldung .= $t['profil_fehler_beruf'];
	}
	
	if (strlen($f['ui_lieblingsfilm']) > 100) {
		$fehlermeldung .= $t['profil_fehler_lieblingsfilm'];
	}
	
	if (strlen($f['ui_lieblingsserie']) > 100) {
		$fehlermeldung .= $t['profil_fehler_lieblingsserie'];
	}
	
	if (strlen($f['ui_lieblingsbuch']) > 100) {
		$fehlermeldung .= $t['profil_fehler_lieblingsbuch'];
	}
	
	if (strlen($f['ui_lieblingsschauspieler']) > 100) {
		$fehlermeldung .= $t['profil_fehler_lieblingsschauspieler'];
	}
	
	if (strlen($f['ui_lieblingsgetraenk']) > 100) {
		$fehlermeldung .= $t['profil_fehler_lieblingsgetraenk'];
	}
	
	if (strlen($f['ui_lieblingsgericht']) > 100) {
		$fehlermeldung .= $t['profil_fehler_lieblingsgericht'];
	}
	
	if (strlen($f['ui_lieblingsspiel']) > 100) {
		$fehlermeldung .= $t['profil_fehler_lieblingsspiel'];
	}
	
	if (strlen($f['ui_lieblingsfarbe']) > 100) {
		$fehlermeldung .= $t['profil_fehler_lieblingsfarbe'];
	}
	
	if (strlen($f['ui_hobby']) > 255) {
		$fehlermeldung .= $t['profil_fehler_hobby'];
	}
	
	if ($f['u_chathomepage'] != "" && $f['u_chathomepage'] < 0 && $f['u_chathomepage'] > 1) {
		$fehlermeldung .= $t['profil_fehler_chathomepage'];
	}
	
	if ($f['ui_hintergrundfarbe'] == "" || strlen($f['ui_hintergrundfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_hintergrundfarbe'])) {
		$fehlermeldung .= str_replace("%farbe%", $t['profil_hintergrundfarbe'], $t['profil_fehler_farbe']);
	}
	
	if ($f['ui_ueberschriften_textfarbe'] == "" || strlen($f['ui_ueberschriften_textfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_ueberschriften_textfarbe'])) {
		$fehlermeldung .= str_replace("%farbe%", $t['profil_ueberschriften_textfarbe'], $t['profil_fehler_farbe']);
	}
	
	if ($f['ui_ueberschriften_hintergrundfarbe'] == "" || strlen($f['ui_ueberschriften_hintergrundfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_ueberschriften_hintergrundfarbe'])) {
		$fehlermeldung .= str_replace("%farbe%", $t['profil_ueberschriften_hintergrundfarbe'], $t['profil_fehler_farbe']);
	}
	
	if ($f['ui_inhalt_textfarbe'] == "" || strlen($f['ui_inhalt_textfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_inhalt_textfarbe'])) {
		$fehlermeldung .= str_replace("%farbe%", $t['profil_inhalt_textfarbe'], $t['profil_fehler_farbe']);
	}
	
	if ($f['ui_inhalt_linkfarbe'] == "" || strlen($f['ui_inhalt_linkfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_inhalt_linkfarbe'])) {
		$fehlermeldung .= str_replace("%farbe%", $t['profil_inhalt_linkfarbe'], $t['profil_fehler_farbe']);
	}
	
	if ($f['ui_inhalt_linkfarbe_aktiv'] == "" || strlen($f['ui_inhalt_linkfarbe_aktiv']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_inhalt_linkfarbe_aktiv'])) {
		$fehlermeldung .= str_replace("%farbe%", $t['profil_inhalt_linkfarbe_aktiv'], $t['profil_fehler_farbe']);
	}
	
	if ($f['ui_inhalt_hintergrundfarbe'] == "" || strlen($f['ui_inhalt_hintergrundfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_inhalt_hintergrundfarbe'])) {
		$fehlermeldung .= str_replace("%farbe%", $t['profil_inhalt_hintergrundfarbe'], $t['profil_fehler_farbe']);
	}
	
	
	if ($fehlermeldung != "") {
		$box = $t['profil_fehlermeldung'];
		zeige_tabelle_zentriert($box, $fehlermeldung);
	} else {
		// Punkte gutschreiben?
		if ($profil_gefunden == false && strlen($f['ui_wohnort']) > 2) {
			punkte(500, $o_id, $u_id, $t['profil_punkte']);
			$profil_gefunden = true;
		}
		
		// Wenn noch kein Profil existiert, die ID aus dem Array entfernen
		if($f['ui_id'] == 0) {
			unset($f['ui_id']);
			$ui_id = 0;
		} else {
			$ui_id = $f['ui_id'];
		}
		
		// Datensatz schreiben
		
		// Sonderfall für "u_chathomepage", da dies in der Tabelle "user" gespeichert wird
		$userArray = array();
		$userArray['u_chathomepage'] = $f['u_chathomepage'];
		schreibe_db("user", $userArray, $u_id, "u_id");
		unset($f['u_chathomepage']);
		
		
		// Alle restlichen Daten in die Tabelle "userinfo" schreiben
		schreibe_db("userinfo", $f, $ui_id, "ui_id");
		
		$f['u_chathomepage'] = $userArray['u_chathomepage'];
		unset($userArray);
		
		$box = $t['profil_erfolgsmeldung'];
		$text = $t['profil_erfolgsmeldung_details'];
		zeige_tabelle_zentriert($box, $text);
	}
}

switch ($aktion) {
	case "zeigealle":
	// Alle Profile listen
		$box = $t['profil_alle_profile'];
		if (!$admin) {
			$text = "<p><b>Fehler:</b> Sie haben keine Berechtigung, die Profile zu lesen!</p>";
		} else {
			$box = $t['profil_alle_profile'];
			$text = '';
			$text .= "<table style=\"width:100%;\">\n";
			$text .= "<tr>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[profil_benutzername]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[profil_wohnort]</td>\n";
			if($admin) {
				$text .= "<td class=\"tabelle_kopfzeile\">$t[profil_interne_email]</td>\n";
			}
			$text .= "<td class=\"tabelle_kopfzeile\">$t[profil_homepage]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[profil_geburt]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[profil_geschlecht]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[profil_beziehungsstatus]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$t[profil_typ]</td>\n";
			$text .= "</tr>";
			
			$query = "SELECT * FROM user,userinfo WHERE ui_userid=u_id ORDER BY u_nick";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) > 0) {
				$zaehler = 0;
				while ($row = mysqli_fetch_object($result)) {
					if ($zaehler % 2 != 0) {
						$bgcolor = 'class="tabelle_zeile2"';
					} else {
						$bgcolor = 'class="tabelle_zeile1"';
					}
					
					$userdata = array();
					$userdata['u_id'] = $row->u_id;
					$userdata['u_nick'] = $row->u_nick;
					$userdata['u_level'] = $row->u_level;
					$userdata['u_punkte_gesamt'] = $row->u_punkte_gesamt;
					$userdata['u_punkte_gruppe'] = $row->u_punkte_gruppe;
					$userdata['u_punkte_anzeigen'] = $row->u_punkte_anzeigen;
					$userdata['u_chathomepage'] = $row->u_chathomepage;
					$userdaten = zeige_userdetails($row->u_id, $userdata);
					
					$text .= "<tr>\n";
					$text .= "<td $bgcolor><b>" . $userdaten . "</b></td>\n";
					$text .= "<td $bgcolor>" . htmlspecialchars($row->ui_wohnort) . "</td>\n";
					if($admin) {
						$text .= "<td $bgcolor>" . htmlspecialchars($row->u_adminemail) . "</td>\n";
					}
					$text .= "<td $bgcolor>" . htmlspecialchars($row->ui_homepage) . "</td>\n";
					$text .= "<td $bgcolor>" . htmlspecialchars($row->ui_geburt) . "</td>\n";
					$text .= "<td $bgcolor>" . zeige_profilinformationen_von_id("geschlecht", htmlspecialchars($row->ui_geschlecht)) . "</td>\n";
					$text .= "<td $bgcolor>" . zeige_profilinformationen_von_id("beziehungsstatus", htmlspecialchars($row->ui_beziehungsstatus)) . "</td>\n";
					$text .= "<td $bgcolor>" . zeige_profilinformationen_von_id("typ", htmlspecialchars($row->ui_typ)) . "</td>\n";
					$text .= "</tr>\n";
					
					$zaehler++;
				}
			}
			$text .= "</table>\n";
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
			
			mysqli_free_result($result);
		}
		
		break;
	
	default:
		// Neues Profil einrichten oder bestehendes ändern
		if ($profil_gefunden == true) {
			$box = $t['bestehendes_profil'];
		} else {
			$box = $t['neues_profil'];
		}
		
		$text = '';
		
		// Textkopf
		if ($los != $t['einstellungen_speichern']) {
			$text .= $t['profil_informationen'];
		}
		
		// Editor ausgeben
		if (!isset($f)) {
			$f[] = "";
		}
		$text .= profil_editor($u_id, $u_nick, $f);
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
}
?>