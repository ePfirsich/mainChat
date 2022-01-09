<?php
$mitgliedId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$code = filter_input(INPUT_GET, 'code');
if(!isset($mitgliedId) || !isset($code)) {
	$fehlermeldung = $t['login_fehlermeldung_passwort_vergessen_kein_code'];
	$text = hinweis($fehlermeldung, "fehler");
	zeige_tabelle_volle_breite($t['login_email_aendern'], $text);
} else {
	//Abfrage des Nutzers
	$query = "SELECT `u_nick`, `u_email`, `u_email_neu`, `u_email_code` FROM `user` WHERE `u_id` = '" . mysqli_real_escape_string($mysqli_link, $mitgliedId) . "'";
	$mdata = sqlQuery($query);
	if (!mysqli_num_rows($mdata)) {
		$fehlermeldung = $t['login_fehlermeldung_passwort_vergessen_kein_benutzer'];
		$text = hinweis($fehlermeldung, "fehler");
		zeige_tabelle_volle_breite($t['login_email_aendern'], $text);
	} else {
		$mitglied = mysqli_fetch_assoc($mdata);
		
		// Überprüfen ob ein Nutzer gefunden wurde und dieser auch einen Emailcode hat
		if($mitglied === null || $mitglied['u_email_code'] === null) {
			$fehlermeldung = $t['login_passwort_fehler_kein_benutzer'];
			$text = hinweis($fehlermeldung, "fehler");
			zeige_tabelle_volle_breite($t['login_email_aendern'], $text);
		} else if(sha1($code) != $mitglied['u_email_code']) {
			// Überprüfe den Passwortcode
			$fehlermeldung = $t['login_passwort_fehler_code_ungueltig'];
			$text = hinweis($fehlermeldung, "fehler");
			zeige_tabelle_volle_breite($t['login_email_aendern'], $text);
		} else {
			// Der Code war korrekt, die E-Mail-Adresse wird geändert
			
			$erfolgsmeldung = str_replace("%u_nick%", $mitglied['u_nick'], $t['login_email_erfolgreich_geandert']);
			$erfolgsmeldung = str_replace("%u_email%", $mitglied['u_email'], $erfolgsmeldung);
			$erfolgsmeldung = str_replace("%u_email_neu%", $mitglied['u_email_neu'], $erfolgsmeldung);
			
			$text = hinweis($erfolgsmeldung, "erfolgreich");
			
			zeige_tabelle_volle_breite($t['login_email_aendern'], $text);
			
			$query = "UPDATE `user` SET `u_email` = '" . mysqli_real_escape_string($mysqli_link, $mitglied['u_email_neu']) . "', `u_email_neu` = NULL, `u_email_code` = NULL WHERE `u_id` = '" . mysqli_real_escape_string($mysqli_link, $mitgliedId) . "'";
			sqlUpdate($query);
		}
	}
}
?>