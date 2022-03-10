<?php
function nachricht_betrete($user_id, $r_id, $u_nick, $r_name) {
	// Eintrittsnachricht in Raum schreiben
	// Aufruf mit Raum-Id, Benutzername, Raum-Name
	global $nachricht_b, $lustigefeatures, $u_farbe, $eintritt_individuell;
	
	// Nachricht Standard
	$text = $nachricht_b[0];
	
	// Nachricht Lustige Ein-/Austrittsnachrichten
	if ($lustigefeatures) {
		reset($nachricht_b);
		$anzahl = count($nachricht_b);
		$text = $nachricht_b[mt_rand(1, $anzahl) - 1];
	}
	
	// Nachricht auswählen
	if ($eintritt_individuell == "1") {
		$query = pdoQuery("SELECT `u_eintritt` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
		
		$row = $query->fetch();
		
		// Debug-Output
		if( !isset($user_id) || $user_id == null || $user_id == "") {
			global $kontakt;
			email_senden($kontakt, "User-ID ist leer2", "Username: " . $u_nick . " Raum-ID: " . $r_id . " Raumname: " . $r_name);
		}
		if (strlen($row['u_eintritt']) > 0) {
			$text = $row['u_eintritt'];
			$text = $text . "  <b>($u_nick)</b> ";
		}
	}
	
	$text = str_replace("%u_nick%", $u_nick, $text);
	$text = str_replace("%r_name%", $r_name, $text);
	
	$text = preg_replace("|%nick%|i", $u_nick, $text);
	$text = preg_replace("|%raum%|i", $r_name, $text);
	
	if (strlen($text) == 0) {
		$text = $u_nick;
	}
	
	// Nachricht im Chat ausgeben; falls Raum moderiert ist, nur HTML-Kommentar ausgeben
	$ergebnis = 0;
	if (raum_ist_moderiert($r_id)) {
		$ergebnis = system_msg("", 0, $user_id, $u_farbe, "<b>&gt;&gt;&gt;</b> " . $text);
	} else {
		// Spamschutz, verhindert die Eintrittsmeldung, wenn innerhalb von 60 Sek mehr als 15 Systemmitteilungen eingehen...
		$query = pdoQuery("SELECT COUNT(`c_id`) AS `nummer` FROM `chat` WHERE `c_von_user` = '' AND `c_typ` = 'S' AND `c_raum` = :c_raum AND `c_zeit` > :c_zeit", [':c_raum'=>intval($r_id), ':c_zeit'=>date("YmdHis", date("U") - 60)]);
		
		$num = $query->fetch();
		$num = $num['nummer'];
		
		if ($num < 15) {
			$ergebnis = global_msg($user_id, $r_id, "<b>&gt;&gt;&gt;</b> " . $text);
		}
	}
	
	return $ergebnis;
}

function nachricht_verlasse($r_id, $u_nick, $r_name) {
	// Eintrittsnachricht in Raum schreiben
	// Aufruf mit Raum-Id, Benutzername, Raum-Name
	// liefert ID des geschriebenen Datensatzes zurück
	global $nachricht_v, $lustigefeatures, $u_farbe, $u_id, $eintritt_individuell;
	
	// Nachricht Standard
	$text = $nachricht_v[0];
	
	// Nachricht Lustige Ein-/Austrittsnachrichten
	if ($lustigefeatures) {
		reset($nachricht_v);
		$anzahl = count($nachricht_v);
		$text = $nachricht_v[mt_rand(1, $anzahl) - 1];
	}
	
	// Nachricht auswählen
	if ($eintritt_individuell == "1") {
		$query = pdoQuery("SELECT `u_austritt` FROM `user` WHERE `u_nick` = :u_nick", [':u_nick'=>$u_nick]);
		
		$row = $query->fetch();
		if (strlen($row['u_austritt']) > 0) {
			$text = $row['u_austritt'];
			$text = $text . " <b>($u_nick)</b> ";
		}
	}
	
	$text = str_replace("%u_nick%", $u_nick, $text);
	$text = str_replace("%r_name%", $r_name, $text);
	
	$text = preg_replace("|%nick%|i", $u_nick, $text);
	$text = preg_replace("|%raum%|i", $r_name, $text);
	
	if (strlen($text) == 0) {
		$text = $u_nick;
	}
	
	// Nachricht im Chat ausgeben; falls Raum moderiert ist, nur HTML-Kommentar ausgeben
	if (raum_ist_moderiert($r_id)) {
		system_msg("", 0, $u_id, $u_farbe, "<b>&lt;&lt;&lt;</b> " . $text);
	} else {
		global_msg($u_id, $r_id, "<b>&lt;&lt;&lt;</b> " . $text);
	}
}
?>