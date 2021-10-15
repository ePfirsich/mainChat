<?php

function nachricht_betrete($u_id, $r_id, $u_nick, $r_name) {
	// Eintrittsnachricht in Raum schreiben
	// Aufruf mit Raum-Id, UserName, Raum-Name
	// liefert $back zurück
	global $mysqli_link, $nachricht_b, $lustigefeatures, $u_farbe;
	global $eintritt_individuell, $eintritt_useranzeige;
	
	// Nachricht Standard
	$text = $nachricht_b[0];
	
	// Nachricht Lustiege ein/austrittsnachrichten
	if ($lustigefeatures) {
		reset($nachricht_b);
		$anzahl = count($nachricht_b);
		$text = $nachricht_b[mt_rand(1, $anzahl) - 1];
	}
	
	// Nachricht auswählen
	if ($eintritt_individuell == "1") {
		$query = "SELECT `u_eintritt` FROM `user` WHERE `u_id` = $u_id";
		$result = mysqli_query($mysqli_link, $query);
		$row = mysqli_fetch_object($result);
		if (strlen($row->u_eintritt) > 0) {
			$text = $row->u_eintritt;
			if ($eintritt_useranzeige == "1") {
				$text = htmlspecialchars($text) . "  <b>($u_nick)</b> ";
			} else {
				$text = htmlspecialchars($text) . " <!-- <b>($u_nick)</b> -->";
			}
		}
		mysqli_free_result($result);
	}
	
	$text = str_replace("%u_name%", $u_nick, $text);
	$text = str_replace("%r_name%", $r_name, $text);
	
	$text = preg_replace("|%nick%|i", $u_nick, $text);
	$text = preg_replace("|%raum%|i", $r_name, $text);
	
	if (strlen($text) == 0) {
		$text = $u_nick;
	}
	
	// Nachricht im Chat ausgeben; falls Raum moderiert ist, nur HTML-Kommentar ausgeben
	$back = 0;
	if (raum_ist_moderiert($r_id)) {
		$back = system_msg("", 0, $u_id, $u_farbe, "<b>&gt;&gt;&gt;</b> "
			. $text);
	} else {
		// Spamschutz, verhindert die Eintrittsmeldung, wenn innerhalb von 60 Sek mehr als 15 Systemmiteilungen eingehen...
		
		$sql = "SELECT count(c_id) as nummer FROM chat WHERE c_von_user = '' and c_typ='S' and c_raum = " . intval($r_id) . " and c_zeit > '"
			. date("YmdHis", date("U") - 60) . "'";
		$result = mysqli_query($mysqli_link, $sql);
		$num = mysqli_fetch_array($result);
		$num = $num['nummer'];
		if ($num < 15) {
			$back = global_msg($u_id, $r_id, "<b>&gt;&gt;&gt;</b> " . $text);
		}
	}
	
	return $back;
}

function nachricht_verlasse($r_id, $u_nick, $r_name) {
	// Eintrittsnachricht in Raum schreiben
	// Aufruf mit Raum-Id, UserName, Raum-Name
	// liefert $back (ID des geschriebenen Datensatzes) zurück
	global $chat, $nachricht_v, $lustigefeatures, $u_farbe, $u_id;
	global $eintritt_individuell, $eintritt_useranzeige, $mysqli_link;
	
	// Nachricht Standard
	$text = $nachricht_v[0];
	
	// Nachricht Lustiege ein/austrittsnachrichten
	if ($lustigefeatures) {
		reset($nachricht_v);
		$anzahl = count($nachricht_v);
		$text = $nachricht_v[mt_rand(1, $anzahl) - 1];
	}
	
	// Nachricht auswählen
	if ($eintritt_individuell == "1") {
		$query = "SELECT `u_austritt` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $u_nick) . "'";
		$result = mysqli_query($mysqli_link, $query);
		$row = mysqli_fetch_object($result);
		if (strlen($row->u_austritt) > 0) {
			$text = $row->u_austritt;
			if ($eintritt_useranzeige == "1")
				$text = htmlspecialchars($text) . "  <b>($u_nick)</b> ";
			else $text = htmlspecialchars($text) . " <!-- <b>($u_nick)</b> -->";
		}
		mysqli_free_result($result);
	}
	
	$text = str_replace("%u_name%", $u_nick, $text);
	$text = str_replace("%r_name%", $r_name, $text);
	
	$text = preg_replace("|%nick%|i", $u_nick, $text);
	$text = preg_replace("|%raum%|i", $r_name, $text);
	
	if (strlen($text) == 0) {
		$text = $u_nick;
	}
	
	// Nachricht im Chat ausgeben; falls Raum moderiert ist, nur HTML-Kommentar ausgeben
	if (raum_ist_moderiert($r_id)) {
		$back = system_msg("", 0, $u_id, $u_farbe, "<b>&lt;&lt;&lt;</b> "
			. $text);
	} else {
		$back = global_msg($u_id, $r_id, "<b>&lt;&lt;&lt;</b> " . $text);
	}
	return ($back);
	
}
?>