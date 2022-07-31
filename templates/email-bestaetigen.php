<?php
$userId = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);
$code = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING);

$text = "";
if(!isset($userId) || !isset($code)) {
	$fehlermeldung = $lang['login_fehlermeldung_passwort_vergessen_kein_code'];
	$text .= hinweis($fehlermeldung, "fehler");
} else {
	//Abfrage des Nutzers
	$query = pdoQuery("SELECT `u_nick`, `u_email`, `u_email_neu`, `u_email_code` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$userId]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		$fehlermeldung = $lang['login_fehlermeldung_passwort_vergessen_kein_benutzer'];
		$text .= hinweis($fehlermeldung, "fehler");
	} else {
		$result = $query->fetch();
		
		// Überprüfen ob ein Nutzer gefunden wurde und dieser auch einen Emailcode hat
		if($result === null || $result['u_email_code'] === null) {
			$fehlermeldung = $lang['login_passwort_fehler_kein_benutzer'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else if(sha1($code) != $result['u_email_code']) {
			// Überprüfe den Passwortcode
			$fehlermeldung = $lang['login_passwort_fehler_code_ungueltig'];
			$text .= hinweis($fehlermeldung, "fehler");
		} else {
			// Der Code war korrekt, die E-Mail-Adresse wird geändert
			
			$erfolgsmeldung = str_replace("%u_nick%", $result['u_nick'], $lang['login_email_erfolgreich_geandert']);
			$erfolgsmeldung = str_replace("%u_email%", $result['u_email'], $erfolgsmeldung);
			$erfolgsmeldung = str_replace("%u_email_neu%", $result['u_email_neu'], $erfolgsmeldung);
			
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
			
			pdoQuery("UPDATE `user` SET `u_email` = :u_email, `u_email_neu` = NULL, `u_email_code` = NULL WHERE `u_id` = :u_id", [':u_email'=>$result['u_email_neu'], ':u_id'=>$userId]);
		}
	}
}
?>