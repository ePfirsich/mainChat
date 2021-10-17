<?php

// chat.php muss mit id=$hash_id aufgerufen werden
// Optional kann $back als Trigger für die Ausgabe der letzten n-Zeilen angegeben werden

require("functions.php");

// Benutzerdaten setzen
id_lese($id);

// Systemnachrichten ausgeben
$sysmsg = TRUE;

$title = $body_titel;
zeige_header_anfang($title, 'chatausgabe');

// Benutzerdaten gesetzt?
if ($u_id) {
	// Timestamp im Datensatz aktualisieren -> Benutzer im Chat / o_who=0
	aktualisiere_online($u_id, $o_raum, 0);
	
	// Algorithmus wählen
	// n-Zeilen ausgeben und nach Timeout neu laden
	?>
	<meta http-equiv="expires" content="0">
	<?php
	$meta_refresh = '<meta http-equiv="refresh" content="7; URL=chat.php?id=' . $id . '">';
	$meta_refresh .= "<script>\n"
		. "setInterval(\"window.scrollTo(1,300000)\",100)\n"
		. "function neuesFenster(url,name) {\n"
		. "hWnd=window.open(url,name,\"resizable=yes,scrollbars=yes,width=300,height=700\");\n"
		. "}\n</script>";
	
	zeige_header_ende($meta_refresh);

	// Chatausgabe, $letzte_id ist global
	chat_lese($o_id, $o_raum, $u_id, $sysmsg, $ignore, $chat_back);
	
} else {
	zeige_header_ende();
	// Auf Chat-Eingangsseite leiten
	?>
	<body onLoad='javascript:parent.location.href="index.php"'>
	<?php
}
?>
</body>
</html>