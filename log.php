<?php

// log.php muss mit id=$hash_id aufgerufen werden

require("functions.php");

// Falls Abspeichern, Header senden
if ($aktion == "abspeichern") {
	$dateiname = "log-" . date("YmdHi") . ".htm";
	
	header("Content-Description: File Transfer");
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"$dateiname\"");
	header("Content-Location: $dateiname");
	header("Cache-Control: maxage=15"); //In seconds
	header("Pragma: public");
}

// Benutzerdaten setzen
id_lese($id);

// Benutzerdaten gesetzt?
if (strlen($u_id) > 0) {
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);

	$title = $body_titel . ' - Log';
	zeige_header_anfang($title, 'chatausgabe');
	zeige_header_ende();
	?>
	<body>
	<?php
	// Voreinstellungen
	// Trigger für die Ausgabe der letzten 100 Nachrichten setzen
	if ($back == 0) {
		$back = 100;
	}
	if ($back > 250) {
		$back = 250;
	}
	
	// Admins: Trigger für die Ausgabe der letzten 1000 Nachrichten setzen
	if ($admin) {
		$back = 1000;
	}
	
	// Systemnachrichten nicht ausgeben als Voreinstellung
	if (!isset($sysmsg))
		$sysmsg = 0;
	if ($sysmsg) {
		$umschalturl = "<a href=\"$PHP_SELF?id=$id&sysmsg=0&back=$back\">"
			. $t['sonst3'] . "</a>";
	} else {
		$umschalturl = "<a href=\"$PHP_SELF?id=$id&sysmsg=1&back=$back\">"
			. $t['sonst2'] . "</a>";
	}
	
	// Link zum Abspeichern
	if ($aktion != "abspeichern") {
		echo "<center>" . $f1
			. "<b>[<a href=\"$PHP_SELF?id=$id&aktion=abspeichern&sysmsg=$sysmsg&back=$back\">"
			. $t['sonst1'] . "</a>]</b>&nbsp;<b>[$umschalturl]</b>" . $f2
			. "</center><br>\n";
		flush();
	}
	
	// Log ausgeben
	chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $back);
	
	echo "</body></html>";
}

?>