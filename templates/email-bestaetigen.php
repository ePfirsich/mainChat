<?php
$mitgliedId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$code = filter_input(INPUT_GET, 'code');
if(!isset($mitgliedId) || !isset($code)) {
	zeige_tabelle_volle_breite($t['login_passwort_fehler_fehlermeldung'], $t['login_fehlermeldung_passwort_vergessen_kein_code']);
} else {
	//Abfrage des Nutzers
	$query = "SELECT `u_nick`, `u_email`, `u_email_neu`, `u_email_code` FROM `user` WHERE `u_id` = '" . mysqli_real_escape_string($mysqli_link, $mitgliedId) . "'";
	$mdata = sqlQuery($query);
	if (!mysqli_num_rows($mdata)) {
		zeige_tabelle_volle_breite($t['login_passwort_fehler_fehlermeldung'], $t['login_fehlermeldung_passwort_vergessen_kein_benutzer']);
	} else {
		$mitglied = mysqli_fetch_assoc($mdata);
		
		// Überprüfen ob ein Nutzer gefunden wurde und dieser auch einen Emailcode hat
		if($mitglied === null || $mitglied['u_email_code'] === null) {
			zeige_tabelle_volle_breite($t['login_passwort_fehler_fehlermeldung'], $t['login_passwort_fehler_kein_benutzer']);
		} else if(sha1($code) != $mitglied['u_email_code']) {
			// Überprüfe den Passwortcode
			zeige_tabelle_volle_breite($t['login_passwort_fehler_fehlermeldung'], $t['login_passwort_fehler_code_ungueltig']);
		} else {
			// Der Code war korrekt, die E-Mail-Adresse wird geändert
			
			$text = str_replace("%u_nick%", $mitglied['u_nick'], $t['login_email_erfolgreich_geandert']);
			$text = str_replace("%u_email%", $mitglied['u_email'], $text);
			$text = str_replace("%u_email_neu%", $mitglied['u_email_neu'], $text);
			
			zeige_tabelle_volle_breite($t['login_passwort_erfolgsmeldung'], $text);
			
			$query = "UPDATE `user` SET `u_email` = '" . mysqli_real_escape_string($mysqli_link, $mitglied['u_email_neu']) . "', `u_email_neu` = NULL, `u_email_code` = NULL WHERE `u_id` = '" . mysqli_real_escape_string($mysqli_link, $mitgliedId) . "'";
			sqlUpdate($query);
		}
	}
}
?>