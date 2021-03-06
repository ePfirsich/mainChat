<?php
$userId = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
if( $userId == '') {
	$userId = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_NUMBER_INT);
}

$code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);
if( $code == '') {
	$code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);
}

$text = "";
if(!isset($userId) || !isset($code)) {
	$fehlermeldung = $lang['login_fehlermeldung_passwort_vergessen_kein_code'];
	$text .= hinweis($fehlermeldung, "fehler");
} else {
	$showForm = true;
	
	//Abfrage des Nutzers
	$query = pdoQuery("SELECT `u_nick`, `u_passwort_code`, `u_passwort_code_time` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$userId]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		$fehlermeldung = $lang['login_fehlermeldung_passwort_vergessen_kein_benutzer'];
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		$result = $query->fetch();
		
		// Überprüfen ob ein Nutzer gefunden wurde und dieser auch ein Passwordcode hat
		if($result === null || $result['u_passwort_code'] === null) {
			$fehlermeldung = $lang['login_passwort_fehler_kein_benutzer'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else if($result['u_passwort_code_time'] === null || strtotime($result['u_passwort_code_time']) < (time()-24*3600) ) {
			$fehlermeldung = $lang['login_passwort_fehler_code_abgelaufen'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else if(sha1($code) != $result['u_passwort_code']) {
			// Überprüfe den Passwortcode
			$fehlermeldung = $lang['login_passwort_fehler_code_ungueltig'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else {
			// Der Code war korrekt, der Nutzer darf ein neues Passwort eingeben
			$send = filter_input(INPUT_POST, 'send', FILTER_SANITIZE_NUMBER_INT);
			if($send == 1) {
				$fehlermeldung = "";
				$passwort = filter_input(INPUT_POST, 'passwort');
				$passwort2 = filter_input(INPUT_POST, 'passwort2');
				
				if($passwort == '' || $passwort2 == '') {
					$fehlermeldung .= $lang['login_passwort_fehler_passwort_zweimal_eingaben'];
				} else if($passwort != $passwort2) {
					$fehlermeldung .= $lang['login_passwort_fehler_passwort_nicht_identisch'];
				} else if(strlen($passwort) < 4) {
					$fehlermeldung .= $lang['login_passwort_fehler_passwort_zu_kurz'];
				}
				if($fehlermeldung != "") {
					$text .= hinweis($fehlermeldung, "fehler");
				} else {
					// Speichere neues Passwort und lösche den Code
					$passworthash = encrypt_password($passwort);
					pdoQuery("UPDATE `user` SET `u_passwort` = :u_passwort, `u_passwort_code` = NULL, `u_passwort_code_time` = NULL WHERE `u_id` = :u_id", [':u_passwort'=>$passworthash, ':u_id'=>$userId]);
					
					$erfolgsmeldung = $lang['login_passwort_erfolgreich_passwort_geaendert'];
					$text .= hinweis($erfolgsmeldung, "erfolgreich");
					
					$showForm = false;
				}
			}
			if($showForm) {
				$text .= "<form action=\"index.php?bereich=passwort-zuruecksetzen\" method=\"post\">\n";
				$text .= "<input type=\"hidden\" name=\"send\" value=\"1\">\n";
				$text .= "<input type=\"hidden\" name=\"uid\" value=\"" . htmlentities($userId) . "\">\n";
				$text .= "<input type=\"hidden\" name=\"code\" value=\"" . htmlentities($code) . "\">\n";
				
				$text .= str_replace("%u_nick%", $result['u_nick'], $lang['login_passwort_benutzername']);
				$text .= "<table style=\"width:100%;\">";
				
				// Neues Passwort
				$text .= zeige_formularfelder("password", $zaehler, $lang['login_passwort'], "passwort", "");
				$zaehler++;
				
				// Neues Passwort wiederholen
				$text .= zeige_formularfelder("password", $zaehler, $lang['login_passwort_wiederholen'], "passwort2", "");
				$zaehler++;
				
				if ($zaehler % 2 != 0) {
					$bgcolor = 'class="tabelle_zeile2"';
				} else {
					$bgcolor = 'class="tabelle_zeile1"';
				}
				$text .= "<tr>";
				$text .= "<td $bgcolor>&nbsp;</td>\n";
				$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[login_neues_passwort_speichern]\"></td>\n";
				$text .= "</tr>\n";
				$text .= "</table>\n";
				
				$text .= "</form>\n";
			}
		}
	}
}
?>