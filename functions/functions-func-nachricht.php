<?php
function nachricht_betrete($user_id, $r_id, $u_nick, $r_name) {
	// Eintrittsnachricht in Raum schreiben
	// Aufruf mit Raum-Id, Benutzername, Raum-Name
	// liefert $back zurück
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
		$query = "SELECT `u_eintritt` FROM `user` WHERE `u_id` = $user_id";
		$result = sqlQuery($query);
		$row = mysqli_fetch_object($result);
		if (strlen($row->u_eintritt) > 0) {
			$text = $row->u_eintritt;
			$text = $text . "  <b>($u_nick)</b> ";
		}
		mysqli_free_result($result);
	}
	
	$text = str_replace("%u_nick%", $u_nick, $text);
	$text = str_replace("%r_name%", $r_name, $text);
	
	$text = preg_replace("|%nick%|i", $u_nick, $text);
	$text = preg_replace("|%raum%|i", $r_name, $text);
	
	if (strlen($text) == 0) {
		$text = $u_nick;
	}
	
	// Nachricht im Chat ausgeben; falls Raum moderiert ist, nur HTML-Kommentar ausgeben
	$back = 0;
	if (raum_ist_moderiert($r_id)) {
		$back = system_msg("", 0, $user_id, $u_farbe, "<b>&gt;&gt;&gt;</b> " . $text);
	} else {
		// Spamschutz, verhindert die Eintrittsmeldung, wenn innerhalb von 60 Sek mehr als 15 Systemmiteilungen eingehen...
		
		$sql = "SELECT COUNT(c_id) as nummer FROM chat WHERE c_von_user = '' AND c_typ='S' AND c_raum = " . intval($r_id) . " AND c_zeit > '" . date("YmdHis", date("U") - 60) . "'";
		$result = sqlQuery($sql);
		$num = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$num = $num['nummer'];
		if ($num < 15) {
			$back = global_msg($user_id, $r_id, "<b>&gt;&gt;&gt;</b> " . $text);
		}
	}
	
	return $back;
}

function nachricht_verlasse($r_id, $u_nick, $r_name) {
	// Eintrittsnachricht in Raum schreiben
	// Aufruf mit Raum-Id, Benutzername, Raum-Name
	// liefert $back (ID des geschriebenen Datensatzes) zurück
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
		$query = "SELECT `u_austritt` FROM `user` WHERE `u_nick` = '" . escape_string($u_nick) . "'";
		$result = sqlQuery($query);
		$row = mysqli_fetch_object($result);
		if (strlen($row->u_austritt) > 0) {
			$text = $row->u_austritt;
			$text = $text . "  <b>($u_nick)</b> ";
		}
		mysqli_free_result($result);
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
		$back = system_msg("", 0, $u_id, $u_farbe, "<b>&lt;&lt;&lt;</b> " . $text);
	} else {
		$back = global_msg($u_id, $r_id, "<b>&lt;&lt;&lt;</b> " . $text);
	}
	
	return ($back);
}
?>