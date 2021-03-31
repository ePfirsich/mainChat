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
	
	if (isset($SSLRedirect) && $SSLRedirect == "1") {
		header("Cache-Control: maxage=15"); //In seconds
		header("Pragma: public");
	}
}

// Userdaten setzen
id_lese($id);

if (isset($eingabe_light_log) && ($eingabe_light_log == "1") && (!$admin)) {
	echo "Sorry, deaktiviert";
	exit();
}

// Userdaten gesetzt?
if (strlen($u_id) > 0) {
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum);
	
	// Fenstername
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);

	$title = $body_titel . ' - Log';
	zeige_header_anfang($title, $farbe_chat_background1, $grafik_background1, $farbe_chat_link1, $farbe_chat_vlink1);
?>
<script>
		window.focus()
		function neuesFenster(url,name) {
			 hWnd=window.open(url,name,"resizable=yes,scrollbars=yes,width=300,height=700");
		}
		function neuesFenster2(url) {
			 hWnd=window.open(url,"640_<?php echo $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
		}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
	// Voreinstellungen
	// Trigger für die Ausgabe der letzten 100 Nachrichten setzen
	if ($back == 0)
		$back = 100;
	if ($back > 250)
		$back = 250;
	
	// Admins: Trigger für die Ausgabe der letzten 1000 Nachrichten setzen
	if ($admin)
		$back = 1000;
	
	// Systemnachrichten nicht ausgeben als Voreinstellung
	if (!isset($sysmsg))
		$sysmsg = 0;
	if ($sysmsg) {
		$umschalturl = "<A HREF=\"$PHP_SELF?id=$id&http_host=$http_host&sysmsg=0&back=$back\">"
			. $t['sonst3'] . "</A>";
	} else {
		$umschalturl = "<A HREF=\"$PHP_SELF?id=$id&http_host=$http_host&sysmsg=1&back=$back\">"
			. $t['sonst2'] . "</A>";
	}
	
	// Link zum Abspeichern
	if ($aktion != "abspeichern") {
		echo "<CENTER>" . $f1
			. "<B>[<A HREF=\"$PHP_SELF?id=$id&http_host=$http_host&aktion=abspeichern&sysmsg=$sysmsg&back=$back\">"
			. $t['sonst1'] . "</A>]</B>&nbsp;<B>[$umschalturl]</B>" . $f2
			. "</CENTER><BR>\n";
		flush();
	}
	
	// Log ausgeben
	chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $back);
	
	echo "</body></html>";
}

?>