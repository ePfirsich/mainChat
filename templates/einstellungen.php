<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$f = array();
$f['u_id'] = filter_input(INPUT_POST, 'u_id', FILTER_SANITIZE_NUMBER_INT);
$eingabe = filter_input(INPUT_POST, 'eingabe', FILTER_SANITIZE_STRING);

if($admin && $f['u_id'] != "" && $f['u_id'] != $u_id) {
	// Nur Admins dürfen andere Benutzer bearbeiten
	$temp_u_id = $f['u_id'];
} else {
	$temp_u_id = $u_id;
}

$benutzerdaten_query = "SELECT `u_id`, `u_nick`, `u_email`, `u_kommentar`, `u_signatur`, `u_eintritt`, `u_austritt`, `u_systemmeldungen`, `u_emails_akzeptieren`, "
	."`u_avatare_anzeigen`, `u_layout_farbe`, `u_layout_chat_darstellung`, `u_smilies`, `u_punkte_anzeigen`, `u_sicherer_modus`, `u_level`, `u_farbe` FROM `user` WHERE `u_id`=$temp_u_id";
$benutzerdaten_result = sqlQuery($benutzerdaten_query);
	
if ($benutzerdaten_result && mysqli_num_rows($benutzerdaten_result) == 1) {
	// Diese Zeile wird benötigt, um bei Falscheingaben den falschen Wert mit dem korrekten Wert zu überschreiben
	$benutzerdaten_result2 = sqlQuery($benutzerdaten_query);
	$benutzerdaten_row = mysqli_fetch_object($benutzerdaten_result2);
	
	// Bestehendes Profil aus der Datenbank laden
	$f = array();
	$f = mysqli_fetch_array($benutzerdaten_result, MYSQLI_ASSOC);
	$f['u_id'] = $temp_u_id;
}

mysqli_free_result($benutzerdaten_result);

// Prüfen, ob ein ChatAdmin einen anderen ChatAdmin oder einen Superuser bearbeiten möchte
if($u_level == 'C' && ($f['u_id'] != "" && $f['u_id'] != $u_id) && ($benutzerdaten_row->u_level == "C" || $benutzerdaten_row->u_level == "S")) {
	$fehlermeldung = $lang['einstellungen_fehler_fehlende_berechtigung'];
	zeige_tabelle_zentriert($lang['einstellungen_fehlermeldung'], $fehlermeldung);
} else {
	// Wenn Daten aus dem Formular übermittelt werden, diese verwenden, da Änderungen vorgenommen wurden
	if($aktion == "editieren" && $temp_u_id && filter_input(INPUT_POST, 'u_nick', FILTER_SANITIZE_URL) != "" && $formular == 1) {
		$f['u_id'] = $temp_u_id;
		$f['u_nick'] = htmlspecialchars(filter_input(INPUT_POST, 'u_nick', FILTER_SANITIZE_STRING));
		$f['u_email'] = filter_input(INPUT_POST, 'u_email', FILTER_VALIDATE_EMAIL);
		$f['u_kommentar'] = htmlspecialchars(filter_input(INPUT_POST, 'u_kommentar', FILTER_SANITIZE_STRING));
		$f['u_signatur'] = htmlspecialchars(filter_input(INPUT_POST, 'u_signatur', FILTER_SANITIZE_STRING));
		$f['u_eintritt'] = htmlspecialchars(filter_input(INPUT_POST, 'u_eintritt'));
		$f['u_austritt'] = htmlspecialchars(filter_input(INPUT_POST, 'u_austritt'));
		$f['u_passwort'] = htmlspecialchars(filter_input(INPUT_POST, 'u_passwort', FILTER_SANITIZE_STRING));
		$f['u_passwort2'] = htmlspecialchars(filter_input(INPUT_POST, 'u_passwort2', FILTER_SANITIZE_STRING));
		$f['u_systemmeldungen'] = filter_input(INPUT_POST, 'u_systemmeldungen', FILTER_SANITIZE_NUMBER_INT);
		$f['u_emails_akzeptieren'] = filter_input(INPUT_POST, 'u_emails_akzeptieren', FILTER_SANITIZE_NUMBER_INT);
		$f['u_avatare_anzeigen'] = filter_input(INPUT_POST, 'u_avatare_anzeigen', FILTER_SANITIZE_NUMBER_INT);
		$f['u_layout_farbe'] = filter_input(INPUT_POST, 'u_layout_farbe', FILTER_SANITIZE_NUMBER_INT);
		$f['u_layout_chat_darstellung'] = filter_input(INPUT_POST, 'u_layout_chat_darstellung', FILTER_SANITIZE_NUMBER_INT);
		$f['u_smilies'] = filter_input(INPUT_POST, 'u_smilies', FILTER_SANITIZE_NUMBER_INT);
		$f['u_punkte_anzeigen'] = filter_input(INPUT_POST, 'u_punkte_anzeigen', FILTER_SANITIZE_NUMBER_INT);
		$f['u_sicherer_modus'] = filter_input(INPUT_POST, 'u_sicherer_modus', FILTER_SANITIZE_NUMBER_INT);
		$f['u_level'] = filter_input(INPUT_POST, 'u_level', FILTER_SANITIZE_URL);
		$f['u_farbe'] = filter_input(INPUT_POST, 'u_farbe', FILTER_SANITIZE_URL);
	}
	
	// Werte aus dem Array entfernen, welche die aktuelle Benutzergruppe nicht bearbeiten darf
	if (!$admin) {
		unset($f['u_kommentar']);
		unset($f['u_level']);
	}
	if ($u_level == "G") {
		unset($f['u_signatur']);
		unset($f['u_systemmeldungen']);
		unset($f['u_layout_chat_darstellung']);
		unset($f['u_eintritt']);
		unset($f['u_austritt']);
		unset($f['u_passwort']);
		unset($f['u_passwort2']);
	}
	if ( $u_level == 'G' && !$punktefeatures) {
		unset($f['u_punkte_anzeigen']);
	}
	
	// Auswahl
	switch ($aktion) {
		case "avatar_aendern":
			// Avatar hochladen und löschen
			$text = "";
			
			// Avatar löschen
			if ($bildname == "avatar") {
				$text .= bild_loeschen($bildname, $f['u_id']);
			}
			
			// Avatar hochladen
			if ($aktion3 == "avatar_hochladen") {
				// Die hochgeladenen Bilder speichern
				$bildliste = ARRAY("avatar");
				foreach ($bildliste as $val) {
					if (isset($_FILES[$val]) && is_uploaded_file($_FILES[$val]['tmp_name'])) {
						// Abspeichern
						$fehlermeldung = bild_holen($f['u_id'], $val, $_FILES[$val]['tmp_name'], $_FILES[$val]['size']);
						
						if ($fehlermeldung != "") {
							// Fehlermeldungen anzeigen
							$text .= hinweis($fehlermeldung, "fehler");
						} else {
							$erfolgsmeldung = $lang['profilbilder_erfolgsmeldung_bild_hochgeladen'];
							$text .= hinweis($erfolgsmeldung, "erfolgreich");
						}
					}
				}
			}
			
			// Einstellungen des Benutzers mit ID $f['u_id'] anzeigen
			user_edit($text, $f, $admin, $u_level);
			
			break;
		
		case "email_aendern":
			formular_email_aendern($f);
			
			break;
			
			if($fehlermeldung != "") {
				// Fehlermeldung anzeigen
				$text = hinweis($fehlermeldung, "fehler");
				$fehlermeldung = "";
			} else {
				// Einstellungen übernehmen
				unset($p);
				
				// Länge des Feldes und Format Mailadresse werden weiter oben geprüft
				$p['u_id'] = $f['u_id'];
				$p['u_farbe'] = $f['u_farbe'];
				
				// Neue Farbe in der Datenbank speichern
				schreibe_db("user", $p, $p['u_id'], "u_id");
				
				$erfolgsmeldung = $lang['einstellungen_erfolgsmeldung_farbe'];
				$text = hinweis($erfolgsmeldung, "erfolgreich");
			}
			
			// Einstellungen des Benutzers mit ID $u_id anzeigen
			user_edit($text, $f, $admin, $u_level);
			
			break;
		
		case "email_aendern_final":
			$f['u_email'] = filter_input(INPUT_POST, 'u_email', FILTER_VALIDATE_EMAIL);
			
			$fehlermeldung = "";
			
			if ( $u_id != $f['u_id'] ) {
				$fehlermeldung .= $lang['einstellungen_fehler_allgemein'];
			}
			
			if ( filter_var($f['u_email'], FILTER_VALIDATE_EMAIL) == false ) {
				$fehlermeldung .= $lang['einstellungen_fehler_email1'];
			}
			
			// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
			$query = "SELECT `u_id` FROM `user` WHERE `u_email` = '" . escape_string($f['u_email']) . "'";
			$result = sqlQuery($query);
			$num = mysqli_num_rows($result);
			if ($num > 1) {
				$fehlermeldung .= $lang['einstellungen_fehler_email2'];
			}
			
			// oder Domain ist lt. Config verboten
			if ($domaingesperrtdbase != $dbase) {
				$domaingesperrt = array();
			}
			for ($i = 0; $i < count($domaingesperrt); $i++) {
				$teststring = strtolower($f['u_email']);
				if (($domaingesperrt[$i]) && (preg_match($domaingesperrt[$i], $teststring))) {
					$fehlermeldung .= $lang['einstellungen_fehler_email1'];
				}
			}
			
			if($fehlermeldung != "") {
				// Fehlermeldung anzeigen
				$text = hinweis($fehlermeldung, "fehler");
				$fehlermeldung = "";
				
				formular_email_aendern($f);
			} else {
				// Einstellungen speichern
				unset($p);
				
				// Passwortcode generieren und in der Datenbank speichern
				$emailcode = random_string();
				$queryEmailcode = "UPDATE `user` SET `u_email_code` = '".sha1($emailcode)."', `u_email_neu` = '" . escape_string($f['u_email']) . "' WHERE `u_id` = $f[u_id];";
				sqlUpdate($queryEmailcode);
				
				// ULR zusammenstellen
				$webseite_email = $chat_url . "/index.php?bereich=email-bestaetigen&uid=" . $f['u_id'] . "&code=".$emailcode;
				$inhalt = str_replace("%webseite_passwort%", $webseite_email, $lang['einstellungen_email_aendern_email_inhalt']);
				$inhalt = str_replace("%u_nick%", $f['u_nick'], $inhalt);
				$email = urldecode($f['u_email']);
				
				// E-Mail versenden
				email_senden($email, $lang['einstellungen_email_aendern_email_titel'], $inhalt);
				
				$box = str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_email_aendern']);
				$text = str_replace("%u_email%", $f['u_email'], $lang['einstellungen_email_aendern_bestaetigung']);
			}
			
			$box = str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_email_aendern']);
			zeige_tabelle_zentriert($box, $text);
			
			break;
		
		case "loesche":
			if ($eingabe == "Löschen!" && $admin) {
				if ($u_id == $f['u_id']) {
					// nicht sich selbst löschen...
					$fehlermeldung = $lang['einstellungen_fehler_loeschen1'];
					$text = hinweis($fehlermeldung, "fehler");
				} else {
					// test, ob zu löschender Admin ist...
					$query = "SELECT * FROM `user` WHERE `u_id`=$f[u_id] ";
					$result = sqlQuery($query);
					$del_level = mysqli_result($result, 0, "u_level");
					if ($del_level != "S" && $del_level != "C" && $del_level != "M") {
						
						// Benutzerdaten löschen
						$erfolgsmeldung = str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_erfolgsmeldung_loeschen']);
						$text = hinweis($erfolgsmeldung, "erfolgreich");
						
						$query = "DELETE FROM `user` WHERE `u_id`=$f[u_id] ";
						$result = sqlUpdate($query);
						
						// Ignore-Einträge löschen
						$query = "DELETE FROM iignore WHERE i_user_aktiv=$f[u_id] OR i_user_passiv=$f[u_id]";
						$result = sqlUpdate($query);
						
						// Gesperrte Räume löschen
						$query = "DELETE FROM sperre WHERE s_user=$f[u_id]";
						$result = sqlUpdate($query);
					} else {
						$fehlermeldung = str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_fehler_loeschen2']);
						$text = hinweis($fehlermeldung, "fehler");
					}
				}
			} else {
				$fehlermeldung = str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_fehler_loeschen2']);
				$text = hinweis($fehlermeldung, "fehler");
			}
			
			// Kopf Tabelle Benutzerinfo
			$box = str_replace("%user%", $f['u_nick'], $lang['einstellungen_benutzer']);
			
			// Box anzeigen
			zeige_tabelle_zentriert($box, $text);
			break;
		
		case "editieren":
			$text = "";
			
			if (!$admin) {
				unset($f['u_email']);
			}
			$fehlermeldung = "";
			
			if ( (isset($eingabe) && $eingabe == $lang['einstellungen_speichern']) && ($u_id == $f['u_id'] || $admin) ) {
				// In Namen die unerlaubten Zeichen entfernen
				$f['u_nick'] = coreCheckName($f['u_nick'], $check_name);
				
				$fehlermeldung = "";
				
				// Farbe überprüfen
				if (strlen($f['u_farbe']) != 6 || !preg_match("/[a-f0-9]{6}/i", $f['u_farbe'])) {
					$fehlermeldung .= $lang['einstellungen_fehler_farbe'];
					
					// Mit korrekten Wert überschreiben
					$f['u_farbe'] = $benutzerdaten_row->u_farbe;
				}
				
				// Admin E-Mail kontrollieren
				if ($admin) {
					if (isset($f['u_email']) && (strlen($f['u_email']) > 0) && (filter_var($f['u_email'], FILTER_VALIDATE_EMAIL) == false) ) {
						$fehlermeldung .= $lang['einstellungen_fehler_email1'];
						
						// Mit korrekten Wert überschreiben
						$f['u_email'] = $benutzerdaten_row->u_email;
					}
					
					// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
					$query = "SELECT `u_id` FROM `user` WHERE `u_email` = '" . escape_string($f['u_email']) . "'";
					$result = sqlQuery($query);
					$num = mysqli_num_rows($result);
					if ($num > 1) {
						$fehlermeldung .= $lang['einstellungen_fehler_email2'];
						
						// Mit korrekten Wert überschreiben
						$f['u_email'] = $benutzerdaten_row->u_email;
					}
				}
				
				// Wurde der Benutzername geändert?
				if ($f['u_nick'] != $benutzerdaten_row->u_nick) {
					// Benutzername muss 4-20 Zeichen haben
					if ( strlen($f['u_nick']) < 4 || strlen($f['u_nick']) > 20 ) {
						// immernoch keine 4-20 Zeichen?
						// Hier müsste aus den oberen beiden Fällen der Benutzername nun da sein,
						// Jetzt wird geprüft, ob man normalerweise den Benutzernamen ändern darf, und dort 4-20
						// Zeichen eingegeben hat
						if ( strlen($f['u_nick']) < 4 || strlen($f['u_nick']) > 20 ) {
							$fehlermeldung .= $lang['einstellungen_fehler_benutzername_zu_kurz_oder_zu_lang'];
							
							// Mit korrekten Wert überschreiben
							$f['u_nick'] = $benutzerdaten_row->u_nick;
						}
					}
					
					// Wenn noch keine 30 Sekunden Zeit seit der letzten Änderung vorbei sind, dann Benutzername nicht speichern
					$query = "SELECT `u_nick_historie`, `u_nick` FROM `user` WHERE `u_id` = '$f[u_id]'";
					$result = sqlQuery($query);
					$xyz = mysqli_fetch_array($result, MYSQLI_ASSOC);
					$nick_historie = unserialize($xyz['u_nick_historie']);
					$nick_alt = $xyz['u_nick'];
					
					if (is_array($nick_historie)) {
						reset($nick_historie);
						list($key, $value) = each($nick_historie);
						$differenz = time() - $key;
					}
					if (!isset($differenz) || $admin) {
						$differenz = 999;
					}
					
					if (!$admin && $nick_alt <> $f['u_nick']) {
						if ($differenz < $nickwechsel) {
							$fehlermeldung .= str_replace("%nickwechsel%", $nickwechsel, $lang['einstellungen_fehler_benutzername_zeitsperre']);
							
							// Mit "altem" Benutzername wieder überschreiben
							$f['u_nick'] = $benutzerdaten_row->u_nick;
						} else {
							$datum = time();
							$nick_historie_neu[$datum] = $nick_alt;
							if (is_array($nick_historie)) {
								$i = 0;
								while (($i < 3) && list($datum, $nick) = each($nick_historie)) {
									$nick_historie_neu[$datum] = $nick;
									$i++;
								}
							}
							$f['u_nick_historie'] = serialize($nick_historie_neu);
						}
					}
					
					// Existiert der Benutzername schon oder ist der Benutername gesperrt?
					$query = "SELECT u_id, u_level FROM user WHERE u_nick = '" . escape_string($f['u_nick']) . "' AND u_id != $u_id";
					$result = sqlQuery($query);
					$rows = mysqli_num_rows($result);
					if ($rows != 0) {
						if ($rows == 1) {
							$xyz = mysqli_fetch_array($result, MYSQLI_ASSOC);
							if ($xyz[u_level] == 'Z') {
								$fehlermeldung .= str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_fehler_benutzername_gesperrt']);
								
								// Mit "altem" Benutzername wieder überschreiben
								$f['u_nick'] = $benutzerdaten_row->u_nick;
							} else {
								$fehlermeldung .= str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_fehler_benutzername_belegt']);
								
								// Mit "altem" Benutzername wieder überschreiben
								$f['u_nick'] .= $benutzerdaten_row->u_nick;
							}
						} else {
							$fehlermeldung .= str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_fehler_benutzername_belegt']);
							
							// Mit "altem" Benutzername wieder überschreiben
							$f['u_nick'] = $benutzerdaten_row->u_nick;
						}
					}
				}
				
				// Level "M" nur vergeben, wenn moderation in diesem Chat erlaubt ist.
				if ($admin && isset($f['u_level']) && $f['u_level'] == "M" && $moderationsmodul == 0) {
					$fehlermeldung .= $lang['einstellungen_fehler_level1'];
					
					// Mit korrekten Wert überschreiben
					$f['u_level'] = $benutzerdaten_row->u_level;
				}
				
				// Ist die Eintrittsnachricht zu lang?
				if ( strlen($f['u_eintritt']) > 100 ) {
					$fehlermeldung .= $lang['einstellungen_fehler_eintrittsnachricht'];
				}
				
				// Ist die Austrittsnachricht zu lang?
				if ( strlen($f['u_austritt']) > 100 ) {
					$fehlermeldung .= $lang['einstellungen_fehler_austrittsnachricht'];
				}
				
				// Aufpassen, wenn Admin sich selbst ändert -> keine leveländerung zulassen.
				if ($u_id == $f['u_id'] && $admin) {
					if ($benutzerdaten_row->u_level != $f['u_level']) {
						$fehlermeldung .= $lang['einstellungen_fehler_level4'];
						
						// Mit korrekten Wert überschreiben
						$f['u_level'] = $benutzerdaten_row->u_level;
					}
				}
				
				if ($admin) {
					$uu_level = $benutzerdaten_row->u_level;
					
					// Falls Benutzerlevel G -> Änderung verboten
					if (isset($f['u_level']) && strlen($f['u_level']) != 0 && $f['u_level'] != "G" && $uu_level == "G") {
						$fehlermeldung .= $lang['einstellungen_fehler_level2'];
						
						// Mit korrekten Wert überschreiben
						$f['u_level'] = $benutzerdaten_row->u_level;
					}
					
					// uu_level = Level des Benutzers, der geändert wird
					// u_level  = Level des Benutzers, der ändert
					// Admin (C) darf für anderen Admin (C oder S) nicht ändern: Level, Passwort, E-Mail
					if ($u_id != $f['u_id'] && $u_level == "C" && ($uu_level == "S" || $uu_level == "C") ) {
						if ( (isset($f['u_passwort']) && $f['u_passwort'] != "") ||
							(isset($f['u_passwort2']) && $f['u_passwort2'] != "") ||
							(isset($f['u_level']) && $f['u_level'] != "") ||
							(isset($f['u_email']) && $f['u_email'] != "") ) {
							$fehlermeldung .= $lang['einstellungen_fehler_level3'];
						
							// Mit korrekten Wert überschreiben
							$f['u_level'] = $benutzerdaten_row->u_level;
							unset($f['u_passwort']);
							unset($f['u_passwort2']);
							$f['u_email'] = $benutzerdaten_row->u_email;
						}
					}
				}
				
				// Ist das Passwort gesetzt?
				if (isset($f['u_passwort']) && strlen($f['u_passwort']) > 0) {
					if ($f['u_passwort'] != $f['u_passwort2']) {
						$fehlermeldung .= $lang['einstellungen_fehler_passwort1'];
						
						// Passwort zurücksetzen
						unset($f['u_passwort']);
						unset($f['u_passwort2']);
					} else if (strlen($f['u_passwort']) < 4) {
						$fehlermeldung .= $lang['einstellungen_fehler_passwort2'];
						
						// Mit Passwort zurücksetzen
						unset($f['u_passwort']);
						unset($f['u_passwort2']);
					}
				}
				
				if ($fehlermeldung != "") {
					// Fehlermeldungen anzeigen
					$text .= hinweis($fehlermeldung, "fehler");
				} else {
					// Benutzerdaten schreiben
					
					// Paswort neu eintragen
					if (isset($f['u_passwort']) && strlen($f['u_passwort']) > 0) {
						$erfolgsmeldung .= $lang['einstellungen_erfolgsmeldung_passwort'];
						
						unset($f['u_passwort2']);
					} else {
						// Wenn kein Passwort eingetragen ist, die Felder komplett löschen
						unset($f['u_passwort']);
						unset($f['u_passwort2']);
					}
					
					// Änderungen anzeigen
					if ($f['u_nick'] != $benutzerdaten_row->u_nick) {
						$erfolgsmeldung .= str_replace("%u_nick%", $f['u_nick'], $lang['einstellungen_erfolgsmeldung_benutzername_geaendert']);
						global_msg($f['u_id'], $o_raum, str_replace("%u_nick%", $f['u_nick'], str_replace("%u_nick_alt%", $benutzerdaten_row->u_nick, $lang['einstellungen_erfolgsmeldung_benutzername_geaendert_chat_ausgabe'])));
						
						$query = "SELECT `u_nick_historie` FROM `user` WHERE `u_id` = " . intval($f['u_id']);
						$result = sqlQuery($query);
						$xyz = mysqli_fetch_array($result, MYSQLI_ASSOC);
						$nick_historie = unserialize($xyz['u_nick_historie']);
						
						$datum = time();
						$nick_historie_neu[$datum] = $u_nick;
						
						if (is_array($nick_historie)) {
							$i = 0;
							while (($i < 3) && list($datum, $nick) = each($nick_historie)) {
								$nick_historie_neu[$datum] = $nick;
								$i++;
							}
						}
						$f['u_nick_historie'] = serialize($nick_historie_neu);
					}
					
					$query = "SELECT `u_profil_historie` FROM `user` WHERE `u_id` = " . intval($f['u_id']);
					$result = sqlQuery($query);
					$g = mysqli_fetch_array($result, MYSQLI_ASSOC);
					
					$g['u_profil_historie'] = unserialize($g['u_profil_historie']);
					
					$datum = time();
					$u_profil_historie_neu[$datum] = $u_nick;
					
					if (is_array($g['u_profil_historie'])) {
						$i = 0;
						while (($i < 3) && list($datum, $nick) = each($g['u_profil_historie'])) {
							$u_profil_historie_neu[$datum] = $nick;
							$i++;
						}
					}
					$f['u_profil_historie'] = serialize($u_profil_historie_neu);
					$f['u_eintritt'] = isset($f['u_eintritt']) ? $f['u_eintritt'] : "";
					$f['u_austritt'] = isset($f['u_austritt']) ? $f['u_austritt'] : "";
					
					schreibe_db("user", $f, $f['u_id'], "u_id");
					
					// Hat der Benutzer den u_level = 'Z', dann lösche die Ignores, wo er der Aktive ist
					if (isset($f['u_level']) && $f['u_level'] == "Z") {
						$queryii = "SELECT u_nick,u_id from user,iignore WHERE i_user_aktiv=" . intval($f['u_id']) . " AND u_id=i_user_passiv order by i_id";
						$resultii = sqlQuery($queryii);
						$anzahlii = @mysqli_num_rows($resultii);
						
						if ($resultii && $anzahlii > 0) {
							for ($i = 0; $i < $anzahlii; $i++) {
								$rowii = @mysqli_fetch_object($resultii);
								ignore($o_id, $f['u_id'], $f['u_nick'], $rowii->u_id, $rowii->u_nick);
							}
						}
						mysqli_free_result($resultii);
					} else if ((isset($f['u_level']) && $f['u_level'] == "C") || (isset($f['u_level']) && $f['u_level'] == "S")) {
						// Hat der Benutzer den u_level = 'C' oder 'S', dann lösche die Ignores, wo er der Passive ist
						$queryii = "SELECT u_nick,u_id FROM user,iignore ". "WHERE i_user_passiv=" . intval($f['u_id']) . " AND u_id=i_user_aktiv order by i_id";
						$resultii = sqlQuery($queryii);
						$anzahlii = @mysqli_num_rows($resultii);
						
						if ($resultii && $anzahlii > 0) {
							for ($i = 0; $i < $anzahlii; $i++) {
								$rowii = @mysqli_fetch_object($resultii);
								ignore($o_id, $rowii->u_id, $rowii->u_nick, $f['u_id'], $f['u_nick']);
							}
						}
						mysqli_free_result($resultii);
					}
					
					// Falls Benutzer auf Level "Z" gesetzt wurde -> logoff
					if (ist_online($f['u_id']) && isset($f['u_level']) && $f['u_level'] == "Z") {
						// o_id und o_raum bestimmen
						$query = "SELECT o_id,o_raum FROM online WHERE o_user=" . intval($f[u_id]);
						$result = sqlQuery($query);
						$rows = mysqli_num_rows($result);
						
						if ($rows > 0) {
							$row = mysqli_fetch_object($result);
							
							// Aus dem Chat ausloggen
							ausloggen($f['u_id'], $f['u_nick'], $row->o_raum, $row->o_id);
							
							$erfolgsmeldung .= $lang['einstellungen_erfolgsmeldung_kick'];
						}
						mysqli_free_result($result);
					}
					
					$erfolgsmeldung .= $lang['einstellungen_erfolgsmeldung_einstellungen'];
					
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
					
					// Bei Änderungen, welche die Darstellung im Chat betrifft, das Chat-Fenster neu laden
					if ($benutzerdaten_row->u_smilies != $f['u_smilies']
						|| $benutzerdaten_row->u_systemmeldungen != $f['u_systemmeldungen']
						|| $benutzerdaten_row->u_avatare_anzeigen != $f['u_avatare_anzeigen']
						|| $benutzerdaten_row->u_punkte_anzeigen != $f['u_punkte_anzeigen']
						|| $benutzerdaten_row->u_sicherer_modus != $f['u_sicherer_modus']
						|| $benutzerdaten_row->u_layout_farbe != $f['u_layout_farbe']
						|| $benutzerdaten_row->u_layout_chat_darstellung != $f['u_layout_chat_darstellung']) {
							reset_system($wo_online);
					}
				}
				
				// Einstellungen des Benutzers mit ID $u_id anzeigen
				user_edit($text, $f, $admin, $u_level);
			} else if ((isset($eingabe) && $eingabe == $lang['einstellungen_loeschen']) && $admin) {
				// Benutzer löschen
				
				// Ist Benutzer noch Online?
				if (!ist_online($f['u_id'])) {
					$text = "";
					// Nachfrage ob sicher
					$text .= str_replace("%u_nick%", $f['u_nick'], $lang['benutzer_loeschen_sicherheitsabfrage2']);
					$text .= "<br><form action=\"inhalt.php?bereich=einstellungen\" method=\"post\" style=\"display:inline;\">\n";
					$text .= "<input type=\"hidden\" name=\"u_id\" value=\"$f[u_id]\">\n";
					$text .= "<input type=\"hidden\" name=\"u_nick\" value=\"$f[u_nick]\">\n";
					$text .="<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n";
					$text .= "<input type=\"submit\" name=\"eingabe\" value=\"$lang[einstellungen_loeschen]\">";
					$text .= "</form>\n";
					
					// Abbrechen
					$text .= "<form action=\"inhalt.php?bereich=einstellungen\" method=\"post\" style=\"display:inline;\">\n";
					$text .= "<input type=\"hidden\" name=\"u_id\" value=\"$f[u_id]\">\n";
					$text .= "&nbsp;<input type=\"submit\" name=\"eingabe\" value=\"$lang[einstellungen_abbrechen]\">";
					$text .= "</form>\n";
					
					zeige_tabelle_zentriert($lang['benutzer_loeschen_sicherheitsabfrage1'], $text);
					$text = "";
				} else {
					$fehlermeldung = str_replace("%u_nick%", $f['u_nick'], $lang['benutzer_loeschen_online']);
					$text = hinweis($fehlermeldung, "fehler");
					
					// Benutzer mit ID $u_id anzeigen
					user_edit($text, $f, $admin, $u_level);
				}
			} else if(isset($eingabe) && $eingabe == $lang['einstellungen_benutzerseite_loeschen'] && $admin) {
				if ($aktion3 == "loeschen") {
					$query = "DELETE FROM userinfo WHERE ui_userid = " . intval($f['u_id']);
					sqlUpdate($query);
					
					$query = "UPDATE user SET u_login = u_login, u_chathomepage = '0' WHERE u_id = " . intval($f['u_id']);
					sqlUpdate($query);
					
					$query = "DELETE FROM bild WHERE b_user = " . intval($f['u_id']);
					sqlUpdate($query);
					
					zeige_tabelle_zentriert($lang['benutzer_loeschen_erledigt'], $lang['benutzer_loeschen_benutzerseite']);
				} else {
					$text = "";
					$text .= "<form action=\"inhalt.php?bereich=einstellungen\" method=\"post\">\n";
					$text .= "<input type=\"hidden\" name=\"u_id\" value=\"$f[u_id]\">\n";
					$text .= "<input type=\"hidden\" name=\"u_nick\" value=\"$f[u_nick]\">\n";
					$text .= "<input type=\"hidden\" name=\"aktion\" value=\"editieren\">\n";
					$text .= "<input type=\"hidden\" name=\"aktion3\" value=\"loeschen\">\n";
					$text .= "<input type=\"submit\" name=\"eingabe\" value=\"$lang[einstellungen_benutzerseite_loeschen]\">\n";
					$text .= "</form>\n";
					
					zeige_tabelle_zentriert($lang['benutzer_loeschen_sicherheitsabfrage1'], $text);
					$text = "";
				}
			} else {
				// Einstellungen des Benutzers mit ID $u_id anzeigen
				user_edit("", $f, $admin, $u_level);
			}
			
			break;
		
		default:
			// Einstellungen des Benutzers mit ID $u_id anzeigen
			user_edit("", $f, $admin, $u_level);
	}
}
?>