<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

// Prüfung, ob für diesen Benutzer bereits ein profil vorliegt -> in $f lesen und merken
// Falls Array aus Formular übergeben wird, nur ui_id überschreiben
$query = pdoQuery("SELECT * FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$u_id]);

$resultCount = $query->rowCount();
if ($resultCount != 0) {
	$result = $query->fetch();
	// Bestehendes Profil aus der Datenbank laden
	
	$f = array();
	$f = $result;
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

$text = "";
// Profil prüfen und ggf. neu eintragen
if($aktion == "aendern" && $f['ui_userid'] && $formular == 1) {
	if( !isset($f['ui_id']) || $f['ui_id'] == '' || $f['ui_id'] == 0) {
		$f = array();
		$f['ui_id'] = $ui_id;
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
	
	// Wir löschen die On-Handler aus der Benutzerseite (einfache Version)
	// on gefolgt von 3-12 Buchstaben wird durch off ersetzt
	
	$text_inhalt = preg_replace('|\son([a-z]{3,12})\s*=|i', ' off\\1=', $text_inhalt);
	$f['ui_text'] = $text_inhalt;
	// Spezialbehandlung für den Text über sich selbst - Ende
	
	$fehlermeldung = "";
	
	// Prüfungen
	if (strlen($f['ui_wohnort']) > 100) {
		$fehlermeldung = $lang['profil_fehler_wohnort'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_geburt'] != "" && !preg_match("/^[0-9]{2}[.][0-9]{2}[.][0-9]{4}$/i", $f['ui_geburt'])) {
		$fehlermeldung = $lang['profil_fehler_geburt'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_geschlecht'] != "" && $f['ui_geschlecht'] < 0 && $f['ui_geschlecht'] > 3) {
		$fehlermeldung = $lang['profil_fehler_geschlecht'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_beziehungsstatus'] != "" && $f['ui_beziehungsstatus'] < 0 && $f['ui_beziehungsstatus'] > 4) {
		$fehlermeldung = $lang['profil_fehler_beziehungsstatus'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_typ'] != "" && $f['ui_typ'] < 0 && $f['ui_typ'] > 6) {
		$fehlermeldung = $lang['profil_fehler_typ'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	// Webseite muss http:// oder https://enthalten
	if (isset($f['ui_homepage']) && strlen($f['ui_homepage']) > 0 && !preg_match("/^http:\/\//i", $f['ui_homepage']) && !preg_match("/^https:\/\//i", $f['ui_homepage']) ) {
		$f['ui_homepage'] = "http://" . $f['ui_homepage'];
	}
	
	if (strlen($f['ui_homepage']) > 160) {
		$fehlermeldung = $lang['profil_fehler_homepage'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_beruf']) > 100) {
		$fehlermeldung = $lang['profil_fehler_beruf'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_lieblingsfilm']) > 100) {
		$fehlermeldung = $lang['profil_fehler_lieblingsfilm'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_lieblingsserie']) > 100) {
		$fehlermeldung = $lang['profil_fehler_lieblingsserie'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_lieblingsbuch']) > 100) {
		$fehlermeldung = $lang['profil_fehler_lieblingsbuch'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_lieblingsschauspieler']) > 100) {
		$fehlermeldung = $lang['profil_fehler_lieblingsschauspieler'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_lieblingsgetraenk']) > 100) {
		$fehlermeldung = $lang['profil_fehler_lieblingsgetraenk'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_lieblingsgericht']) > 100) {
		$fehlermeldung = $lang['profil_fehler_lieblingsgericht'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_lieblingsspiel']) > 100) {
		$fehlermeldung = $lang['profil_fehler_lieblingsspiel'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_lieblingsfarbe']) > 100) {
		$fehlermeldung = $lang['profil_fehler_lieblingsfarbe'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if (strlen($f['ui_hobby']) > 255) {
		$fehlermeldung = $lang['profil_fehler_hobby'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['u_chathomepage'] != "" && $f['u_chathomepage'] < 0 && $f['u_chathomepage'] > 1) {
		$fehlermeldung = $lang['profil_fehler_chathomepage'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_hintergrundfarbe'] == "" || strlen($f['ui_hintergrundfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_hintergrundfarbe'])) {
		$fehlermeldung = str_replace("%farbe%", $lang['profil_hintergrundfarbe'], $lang['profil_fehler_farbe']);
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_ueberschriften_textfarbe'] == "" || strlen($f['ui_ueberschriften_textfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_ueberschriften_textfarbe'])) {
		$fehlermeldung = str_replace("%farbe%", $lang['profil_ueberschriften_textfarbe'], $lang['profil_fehler_farbe']);
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_ueberschriften_hintergrundfarbe'] == "" || strlen($f['ui_ueberschriften_hintergrundfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_ueberschriften_hintergrundfarbe'])) {
		$fehlermeldung = str_replace("%farbe%", $lang['profil_ueberschriften_hintergrundfarbe'], $lang['profil_fehler_farbe']);
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_inhalt_textfarbe'] == "" || strlen($f['ui_inhalt_textfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_inhalt_textfarbe'])) {
		$fehlermeldung = str_replace("%farbe%", $lang['profil_inhalt_textfarbe'], $lang['profil_fehler_farbe']);
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_inhalt_linkfarbe'] == "" || strlen($f['ui_inhalt_linkfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_inhalt_linkfarbe'])) {
		$fehlermeldung = str_replace("%farbe%", $lang['profil_inhalt_linkfarbe'], $lang['profil_fehler_farbe']);
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_inhalt_linkfarbe_aktiv'] == "" || strlen($f['ui_inhalt_linkfarbe_aktiv']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_inhalt_linkfarbe_aktiv'])) {
		$fehlermeldung = str_replace("%farbe%", $lang['profil_inhalt_linkfarbe_aktiv'], $lang['profil_fehler_farbe']);
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	if ($f['ui_inhalt_hintergrundfarbe'] == "" || strlen($f['ui_inhalt_hintergrundfarbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['ui_inhalt_hintergrundfarbe'])) {
		$fehlermeldung = str_replace("%farbe%", $lang['profil_inhalt_hintergrundfarbe'], $lang['profil_fehler_farbe']);
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	
	if ($fehlermeldung == "") {
		// Punkte gutschreiben?
		if ($profil_gefunden == false && strlen($f['ui_wohnort']) > 2) {
			punkte(500, $o_id, $u_id, $lang['profil_punkte']);
			$profil_gefunden = true;
		}
		
		// Sonderfall für "u_chathomepage", da dies in der Tabelle "user" gespeichert wird
		$userArray = array();
		$userArray['u_chathomepage'] = $f['u_chathomepage'];
		
		pdoQuery("UPDATE `user` SET `u_chathomepage` = :u_chathomepage WHERE `u_id` = :u_id",
			[
				':u_id'=>$u_id,
				':u_chathomepage'=>$userArray['u_chathomepage']
			]);
		unset($f['u_chathomepage']);
		
		aktualisiere_online($u_id);
		
		// Alle restlichen Daten in die Tabelle "userinfo" schreiben
		if($f['ui_id'] == 0) {
			// Neues Profil anlegen
			unset($f['ui_id']);
			
			pdoQuery("INSERT INTO `userinfo` (`ui_wohnort`, `ui_geburt`, `ui_hobby`, `ui_text`, `ui_geschlecht`, `ui_lieblingsfilm`, `ui_lieblingsserie`, `ui_lieblingsbuch`, `ui_lieblingsschauspieler`,
					`ui_lieblingsgetraenk`, `ui_lieblingsgericht`, `ui_lieblingsspiel`, `ui_lieblingsfarbe`, `ui_homepage`, `ui_hintergrundfarbe`, `ui_ueberschriften_textfarbe`, `ui_ueberschriften_hintergrundfarbe`,
					`ui_inhalt_textfarbe`, `ui_inhalt_linkfarbe`, `ui_inhalt_linkfarbe_aktiv`, `ui_inhalt_hintergrundfarbe`)
					VALUES (:ui_wohnort, :ui_geburt, :ui_hobby, :ui_text, :ui_geschlecht, :ui_lieblingsfilm, :ui_lieblingsserie, :ui_lieblingsbuch, :ui_lieblingsschauspieler,
					:ui_lieblingsgetraenk, :ui_lieblingsgericht, :ui_lieblingsspiel, :ui_lieblingsfarbe, :ui_homepage, :ui_hintergrundfarbe, :ui_ueberschriften_textfarbe, :ui_ueberschriften_hintergrundfarbe,
					:ui_inhalt_textfarbe, :ui_inhalt_linkfarbe, :ui_inhalt_linkfarbe_aktiv, :ui_inhalt_hintergrundfarbe)",
				[
					':ui_wohnort'=>$f['ui_wohnort'],
					':ui_geburt'=>$f['ui_geburt'],
					':ui_hobby'=>$f['ui_hobby'],
					':ui_text'=>$f['ui_text'],
					':ui_geschlecht'=>$f['ui_geschlecht'],
					':ui_lieblingsfilm'=>$f['ui_lieblingsfilm'],
					':ui_lieblingsserie'=>$f['ui_lieblingsserie'],
					':ui_lieblingsbuch'=>$f['ui_lieblingsbuch'],
					':ui_lieblingsschauspieler'=>$f['ui_lieblingsschauspieler'],
					':ui_lieblingsgetraenk'=>$f['ui_lieblingsgetraenk'],
					':ui_lieblingsgericht'=>$f['ui_lieblingsgericht'],
					':ui_lieblingsspiel'=>$f['ui_lieblingsspiel'],
					':ui_lieblingsfarbe'=>$f['ui_lieblingsfarbe'],
					':ui_homepage'=>$f['ui_homepage'],
					':ui_hintergrundfarbe'=>$f['ui_hintergrundfarbe'],
					':ui_ueberschriften_textfarbe'=>$f['ui_ueberschriften_textfarbe'],
					':ui_ueberschriften_hintergrundfarbe'=>$f['ui_ueberschriften_hintergrundfarbe'],
					':ui_inhalt_textfarbe'=>$f['ui_inhalt_textfarbe'],
					':ui_inhalt_linkfarbe'=>$f['ui_inhalt_linkfarbe'],
					':ui_inhalt_linkfarbe_aktiv'=>$f['ui_inhalt_linkfarbe_aktiv'],
					':ui_inhalt_hintergrundfarbe'=>$f['ui_inhalt_hintergrundfarbe']
				]);
			
		} else {
			// Profil editieren
			
			pdoQuery("UPDATE `userinfo` SET `ui_wohnort` = :ui_wohnort, `ui_geburt` = :ui_geburt, `ui_hobby` = :ui_hobby, `ui_text` = :ui_text,`ui_geschlecht` = :ui_geschlecht,
					`ui_lieblingsfilm` = :ui_lieblingsfilm, `ui_lieblingsserie` = :ui_lieblingsserie, `ui_lieblingsbuch` = :ui_lieblingsbuch, `ui_lieblingsschauspieler` = :ui_lieblingsschauspieler,
					`ui_lieblingsgetraenk` = :ui_lieblingsgetraenk, `ui_lieblingsgericht` = :ui_lieblingsgericht, `ui_lieblingsspiel` = :ui_lieblingsspiel, `ui_lieblingsfarbe` = :ui_lieblingsfarbe,
					`ui_homepage` = :ui_homepage, `ui_hintergrundfarbe` = :ui_hintergrundfarbe, `ui_ueberschriften_textfarbe` = :ui_ueberschriften_textfarbe,
					`ui_ueberschriften_hintergrundfarbe` = :ui_ueberschriften_hintergrundfarbe, `ui_inhalt_textfarbe` = :ui_inhalt_textfarbe, `ui_inhalt_linkfarbe` = :ui_inhalt_linkfarbe,
					`ui_inhalt_linkfarbe_aktiv` = :ui_inhalt_linkfarbe_aktiv, `ui_inhalt_hintergrundfarbe` = :ui_inhalt_hintergrundfarbe WHERE `ui_userid` = :ui_userid",
				[
					':ui_userid'=>$u_id,
					':ui_wohnort'=>$f['ui_wohnort'],
					':ui_geburt'=>$f['ui_geburt'],
					':ui_hobby'=>$f['ui_hobby'],
					':ui_text'=>$f['ui_text'],
					':ui_geschlecht'=>$f['ui_geschlecht'],
					':ui_lieblingsfilm'=>$f['ui_lieblingsfilm'],
					':ui_lieblingsserie'=>$f['ui_lieblingsserie'],
					':ui_lieblingsbuch'=>$f['ui_lieblingsbuch'],
					':ui_lieblingsschauspieler'=>$f['ui_lieblingsschauspieler'],
					':ui_lieblingsgetraenk'=>$f['ui_lieblingsgetraenk'],
					':ui_lieblingsgericht'=>$f['ui_lieblingsgericht'],
					':ui_lieblingsspiel'=>$f['ui_lieblingsspiel'],
					':ui_lieblingsfarbe'=>$f['ui_lieblingsfarbe'],
					':ui_homepage'=>$f['ui_homepage'],
					':ui_hintergrundfarbe'=>$f['ui_hintergrundfarbe'],
					':ui_ueberschriften_textfarbe'=>$f['ui_ueberschriften_textfarbe'],
					':ui_ueberschriften_hintergrundfarbe'=>$f['ui_ueberschriften_hintergrundfarbe'],
					':ui_inhalt_textfarbe'=>$f['ui_inhalt_textfarbe'],
					':ui_inhalt_linkfarbe'=>$f['ui_inhalt_linkfarbe'],
					':ui_inhalt_linkfarbe_aktiv'=>$f['ui_inhalt_linkfarbe_aktiv'],
					':ui_inhalt_hintergrundfarbe'=>$f['ui_inhalt_hintergrundfarbe']
				]);
		}
		$f['u_chathomepage'] = $userArray['u_chathomepage'];
		unset($userArray);
		
		
		$erfolgsmeldung = $lang['profil_erfolgsmeldung_profil_gespeichert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
	}
}

switch ($aktion) {
	case "zeigealle":
	// Alle Profile listen
		$box = $lang['profil_alle_profile'];
		if (!$admin) {
			$text = $lang['profil_fehlermeldung_keine_berechtigung'];
		} else {
			$text = '';
			$text .= "<table style=\"width:100%;\">\n";
			$text .= "<tr>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[profil_benutzername]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[profil_wohnort]</td>\n";
			if($admin) {
				$text .= "<td class=\"tabelle_kopfzeile\">$lang[profil_email]</td>\n";
			}
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[profil_webseite]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[profil_geburt]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[profil_geschlecht]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[profil_beziehungsstatus]</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">$lang[profil_typ]</td>\n";
			$text .= "</tr>";
			
			$query = pdoQuery("SELECT * FROM `user`, `userinfo` WHERE `ui_userid` = `u_id` ORDER BY `u_nick`", []);
			
			$resultCount = $query->rowCount();
			if ($resultCount > 0) {
				$zaehler = 0;
				$result = $query->fetchAll();
				foreach($result as $zaehler => $row) {
					if ($zaehler % 2 != 0) {
						$bgcolor = 'class="tabelle_zeile2"';
					} else {
						$bgcolor = 'class="tabelle_zeile1"';
					}
					
					$userdata = array();
					$userdata['u_id'] = $row['u_id'];
					$userdata['u_nick'] = $row['u_nick'];
					$userdata['u_level'] = $row['u_level'];
					$userdata['u_punkte_gesamt'] = $row['u_punkte_gesamt'];
					$userdata['u_punkte_gruppe'] = $row['u_punkte_gruppe'];
					$userdata['u_punkte_anzeigen'] = $row['u_punkte_anzeigen'];
					$userdata['u_chathomepage'] = $row['u_chathomepage'];
					$userdaten = zeige_userdetails($row['u_id'], $userdata);
					
					$text .= "<tr>\n";
					$text .= "<td $bgcolor><b>" . $userdaten . "</b></td>\n";
					$text .= "<td $bgcolor>" . htmlspecialchars($row['ui_wohnort']) . "</td>\n";
					if($admin) {
						$text .= "<td $bgcolor>" . htmlspecialchars($row['u_email']) . "</td>\n";
					}
					$text .= "<td $bgcolor>" . htmlspecialchars($row['ui_homepage']) . "</td>\n";
					$text .= "<td $bgcolor>" . htmlspecialchars($row['ui_geburt']) . "</td>\n";
					$text .= "<td $bgcolor>" . zeige_profilinformationen_von_id("geschlecht", htmlspecialchars($row['ui_geschlecht'])) . "</td>\n";
					$text .= "<td $bgcolor>" . zeige_profilinformationen_von_id("beziehungsstatus", htmlspecialchars($row['ui_beziehungsstatus'])) . "</td>\n";
					$text .= "<td $bgcolor>" . zeige_profilinformationen_von_id("typ", htmlspecialchars($row['ui_typ'])) . "</td>\n";
					$text .= "</tr>\n";
					
					$zaehler++;
				}
			}
			$text .= "</table>\n";
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
		}
		
	break;
	
	default:
		// Neues Profil einrichten oder bestehendes ändern
		if ($profil_gefunden == true) {
			$box = $lang['bestehendes_profil'];
		} else {
			$box = $lang['neues_profil'];
		}
		
		// Textkopf
		if ($uebergabe != $lang['einstellungen_speichern']) {
			$text .= str_replace("%nickname%", $u_nick, $lang['profil_informationen']);
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