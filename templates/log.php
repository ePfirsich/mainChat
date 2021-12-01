<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id)) {
	die;
}

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
	$umschalturl = "<a href=\"inhalt.php?seite=log&id=$id&sysmsg=0&back=$back\">" . $t['sonst3'] . "</a>";
} else {
	$umschalturl = "<a href=\"inhalt.php?seite=log&id=$id&sysmsg=1&back=$back\">" . $t['sonst2'] . "</a>";
}

// Link zum Abspeichern
if ($aktion != "abspeichern") {
	echo "<center>" . $f1
	. "<b>[<a href=\"inhalt.php?seite=log&id=$id&aktion=abspeichern&sysmsg=$sysmsg&back=$back\">"
		. $t['sonst1'] . "</a>]</b>&nbsp;<b>[$umschalturl]</b>" . $f2
		. "</center><br>\n";
	flush();
}

// Log ausgeben
chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $back);
?>