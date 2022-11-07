<?php
require_once("./functions/functions.php");
require_once("./functions/functions-chat_lese.php");

// Optional kann $trigger_letzte_Zeilen als Trigger für die Ausgabe der letzten n-Zeilen angegeben werden
$trigger_letzte_Zeilen = 1;

// Benutzerdaten setzen
id_lese();

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	echo "<body onLoad='parent.location.href=\"index.php\"'>\n";
} else {
	// Hole alle benötigten Einstellungen des Benutzers
	$benutzerdaten = hole_benutzer_einstellungen($u_id, "chatausgabe");
	
	// Zeit in Sekunden bis auch im Normalmodus die Seite neu geladen wird
	$refresh_zeit = 600;
	
	// Systemnachrichten ausgeben
	$sysmsg = true;
	
	$title = $body_titel;
	$meta_refresh = "";

	$meta_refresh .= "<meta http-equiv=\"expires\" content=\"0\">\n";
	$meta_refresh .= "<script>\n setInterval(\"window.scrollTo(1,300000)\",100)\n</script>\n";
	//$meta_refresh .= '<script src="js/jscript.js"></script>';
	$meta_refresh .= '<script src="js/jquery-3.6.1.min.js"></script>';
	$meta_refresh .= '<script src="js/chat.js"></script>';
	//echo'<link rel="stylesheet" href="css/style.css">';
	//echo'</head>';
	
	zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh);
	
	echo'<body>';
	echo'<div id="container">';
	echo'<div id="view_ajax"></div>';
	echo'</div>';
	
	// Trigger für die Ausgabe der letzten 20 Nachrichten setzen
//	$trigger_letzte_Zeilen = 20;
	
	// echo "\n\n--myboundary\nContent-Type: text/html\n\n";
	//echo "<body onLoad='parent.chat.location=\"chat.php\"'>";
	//flush();
}
?>
</body>
</html>