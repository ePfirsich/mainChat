<?php
$mitgliedId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
$code = filter_input(INPUT_POST, 'code');
$text = "";
if(!isset($mitgliedId) || !isset($code)) {
	$fehlermeldung = $t['login_fehlermeldung_passwort_vergessen_kein_code'];
	$text .= hinweis($fehlermeldung, "fehler");
} else {
	$showForm = true;
	
	//Abfrage des Nutzers
	$query = "SELECT `u_nick`, `u_passwort_code`, `u_passwort_code_time` FROM `user` WHERE `u_id` = '" . mysqli_real_escape_string($mysqli_link, $mitgliedId) . "'";
	$mdata = sqlQuery($query);
	if (!mysqli_num_rows($mdata)) {
		$fehlermeldung = $t['login_fehlermeldung_passwort_vergessen_kein_benutzer'];
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		$mitglied = mysqli_fetch_assoc($mdata);
		
		// Überprüfen ob ein Nutzer gefunden wurde und dieser auch ein Passwordcode hat
		if($mitglied === null || $mitglied['u_passwort_code'] === null) {
			$fehlermeldung = $t['login_passwort_fehler_kein_benutzer'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else if($mitglied['u_passwort_code_time'] === null || strtotime($mitglied['u_passwort_code_time']) < (time()-24*3600) ) {
			$fehlermeldung = $t['login_passwort_fehler_code_abgelaufen'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else if(sha1($code) != $mitglied['u_passwort_code']) {
			// Überprüfe den Passwortcode
			$fehlermeldung = $t['login_passwort_fehler_code_ungueltig'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else {
			// Der Code war korrekt, der Nutzer darf ein neues Passwort eingeben
			$send = filter_input(INPUT_POST, 'send', FILTER_SANITIZE_NUMBER_INT);
			if($send == 1) {
				$fehlermeldung = "";
				$passwort = filter_input(INPUT_POST, 'passwort');
				$passwort2 = filter_input(INPUT_POST, 'passwort2');
				
				if($passwort == '' || $passwort2 == '') {
					$fehlermeldung .= $t['login_passwort_fehler_passwort_zweimal_eingaben'];
				} else if($passwort != $passwort2) {
					$fehlermeldung .= $t['login_passwort_fehler_passwort_nicht_identisch'];
				} else if(strlen($passwort) < 4) {
					$fehlermeldung .= $t['login_passwort_fehler_passwort_zu_kurz'];
				}
				if($fehlermeldung != "") {
					$text .= hinweis($fehlermeldung, "fehler");
				} else {
					// Speichere neues Passwort und lösche den Code
					$passworthash = encrypt_password($passwort);
					$query = "UPDATE `user` SET `u_passwort` = '$passworthash', `u_passwort_code` = NULL, `u_passwort_code_time` = NULL WHERE `u_id` = '" . mysqli_real_escape_string($mysqli_link, $mitgliedId) . "'";
					sqlUpdate($query);
					
					$erfolgsmeldung = $t['login_passwort_erfolgreich_passwort_geaendert'];
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
					
					$showForm = false;
				}
			}
			if($showForm) {
				$text .= "<form action=\"index.php?bereich=passwort-zuruecksetzen\" method=\"post\">\n";
				$text .= "<input type=\"hidden\" name=\"send\" value=\"1\">\n";
				$text .= "<input type=\"hidden\" name=\"user_id\" value=\"" . htmlentities($mitgliedId) . "\">\n";
				$text .= "<input type=\"hidden\" name=\"code\" value=\"" . htmlentities($code) . "\">\n";
				
				$text .= str_replace("%u_nick%", $mitglied['u_nick'], $t['login_passwort_benutzername']);
				$text .= "<table style=\"width:100%;\">";
				
				// Neues Passwort
				$text .= zeige_formularfelder("password", $zaehler, $t['login_passwort'], "passwort", "");
				$zaehler++;
				
				// Neues Passwort wiederholen
				$text .= zeige_formularfelder("password", $zaehler, $t['login_passwort_wiederholen'], "passwort2", "");
				$zaehler++;
				
				if ($zaehler % 2 != 0) {
					$bgcolor = 'class="tabelle_zeile2"';
				} else {
					$bgcolor = 'class="tabelle_zeile1"';
				}
				$text .= "<tr>";
				$text .= "<td $bgcolor>&nbsp;</td>\n";
				$text .= "<td $bgcolor><input type=\"submit\" value=\"$t[login_neues_passwort_speichern]\"></td>\n";
				$text .= "</tr>\n";
				$text .= "</table>\n";
				
				$text .= "</form>\n";
			}
		}
	}
}

zeige_tabelle_volle_breite($t['login_passwort_vergessen'], $text);
?>