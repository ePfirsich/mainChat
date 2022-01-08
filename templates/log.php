<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}

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
if (!isset($sysmsg)) {
	$sysmsg = 0;
}
if ($sysmsg) {
	$umschalturl = "<a href=\"inhalt.php?bereich=log&id=$id&sysmsg=0&back=$back\">" . $t['sonst3'] . "</a>";
} else {
	$umschalturl = "<a href=\"inhalt.php?bereich=log&id=$id&sysmsg=1&back=$back\">" . $t['sonst2'] . "</a>";
}

// Link zum Abspeichern
if ($aktion != "abspeichern") {
	echo "<div style=\"text-align:center;\" class=\"smaller\"><b>[<a href=\"inhalt.php?bereich=log&id=$id&aktion=abspeichern&sysmsg=$sysmsg&back=$back\">"
		. $t['sonst1'] . "</a>]&nbsp;[$umschalturl]</b></div><br>\n";
	flush();
}


// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "chatausgabe");

// Log ausgeben
chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $back, $benutzerdaten);
?>