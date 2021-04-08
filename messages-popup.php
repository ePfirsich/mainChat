<?php

require("functions.php");
require("functions.php-func-chat_lese.php");

// Userdaten setzen
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, 'chatausgabe');

// Userdaten gesetzt?
if (strlen($u_id) > 0) {
	
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
	
	// Ohne die Stringersetzung, würde das Fenster bei Umlauten 
	// auf die Startseite springen, da id_lese ein Problem damit hat
	$userfuerrefresh = urlencode($user_nick);
	$meta_refresh = '<meta http-equiv="refresh" content="' . intval(15) . '; URL=messages-popup.php?http_host=' . $http_host . '&id=' . $id . '&user=' . $user . '&user_nick=' . $userfuerrefresh . '">';

	$meta_refresh .= "<script>\n"
		. "function neuesFenster(url,name) {\n"
		. "hWnd=window.open(url,name,\"resizable=yes,scrollbars=yes,width=300,height=580\");\n"
		. "}\n" . "function neuesFenster2(url) {\n"
		. "hWnd=window.open(url,\"640_$fenster\",\"resizable=yes,scrollbars=yes,width=780,height=580\");\n"
		. "}\n" . "</script>\n";
	
	zeige_header_ende($meta_refresh);
	?>
	<body onLoad="window.scrollTo(1,300000)">
	<?php
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum, 2);
	
	// Aktuelle Privat- und Systemnachrichten oder Statusmeldung ausgeben
	if (!chat_lese($o_id, $o_raum, $u_id, TRUE, $ignore, 10, TRUE, $user)) {
		echo $t['chat_msg106'];
	}
} else {
	// User wird nicht gefunden. Login ausgeben
	
	zeige_header_ende();
	?>
	<body onLoad='javascript:parent.location.href="index.php?http_host=<?php echo $http_host; ?>'>
	<?php
}
?>
</body>
</html>