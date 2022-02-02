<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}
$sysmsg = filter_input(INPUT_GET, 'sysmsg', FILTER_SANITIZE_NUMBER_INT);

// Systemnachrichten nicht ausgeben als Voreinstellung
if($sysmsg == "") {
	$sysmsg = false;
}

// Voreinstellungen
// Trigger für die Ausgabe der letzten 300 Nachrichten setzen
$anzahl_der_nachrichten = 300;

// Admins: Trigger für die Ausgabe der letzten 1000 Nachrichten setzen
if ($admin) {
	$anzahl_der_nachrichten = 1000;
}

if ($sysmsg) {
	$umschalturl = "<a href=\"inhalt.php?bereich=log&id=$id&sysmsg=0\">" . $t['log_systemnachrichten_unterdruecken'] . "</a>";
} else {
	$umschalturl = "<a href=\"inhalt.php?bereich=log&id=$id&sysmsg=1\">" . $t['log_systemnachrichten_anzeigen'] . "</a>";
}

// Link zum Abspeichern
if ($aktion != "abspeichern") {
	echo "<div style=\"text-align:center;\" class=\"smaller\"><b>[<a href=\"inhalt.php?bereich=log&id=$id&aktion=abspeichern&sysmsg=$sysmsg\">" . $t['log_abspeichern'] . "</a>]&nbsp;[$umschalturl]</b></div><br>\n";
	flush();
}


// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "chatausgabe");

// Log ausgeben
chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $anzahl_der_nachrichten, $benutzerdaten);
?>