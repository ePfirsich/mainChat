<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}
$sysmsg = filter_input(INPUT_GET, 'sysmsg', FILTER_SANITIZE_NUMBER_INT);

// Voreinstellungen
// Trigger für die Ausgabe der letzten 250 Nachrichten setzen
$anzahl_der_nachrichten = 250;

// Admins: Trigger für die Ausgabe der letzten 1000 Nachrichten setzen
if ($admin) {
	$anzahl_der_nachrichten = 1000;
}

// Systemnachrichten nicht ausgeben als Voreinstellung
if (!isset($sysmsg)) {
	$sysmsg = 0;
}
if ($sysmsg) {
	$umschalturl = "<a href=\"inhalt.php?bereich=log&id=$id&sysmsg=0\">" . $t['sonst3'] . "</a>";
} else {
	$umschalturl = "<a href=\"inhalt.php?bereich=log&id=$id&sysmsg=1\">" . $t['sonst2'] . "</a>";
}

// Link zum Abspeichern
if ($aktion != "abspeichern") {
	echo "<div style=\"text-align:center;\" class=\"smaller\"><b>[<a href=\"inhalt.php?bereich=log&id=$id&aktion=abspeichern&sysmsg=$sysmsg\">"
		. $t['sonst1'] . "</a>]&nbsp;[$umschalturl]</b></div><br>\n";
	flush();
}


// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "chatausgabe");

// Log ausgeben
chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $anzahl_der_nachrichten, $benutzerdaten);
?>