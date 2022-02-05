<?php

require_once("functions/functions.php");
require_once("functions/functions-chat_lese.php");

$user = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_NUMBER_INT);
$user_nick = filter_input(INPUT_GET, 'user_nick', FILTER_SANITIZE_STRING);

// Benutzerdaten setzen
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "chatausgabe");

// Ohne die Stringersetzung, würde das Fenster bei Umlauten 
// auf die Startseite springen, da id_lese ein Problem damit hat
$userfuerrefresh = urlencode($user_nick);
$meta_refresh = '<meta http-equiv="refresh" content="15; URL=messages-popup.php?user=' . $user . '&user_nick=' . $userfuerrefresh . '">';
$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh);
?>
<body onLoad="window.scrollTo(1,300000)">
<?php
// Timestamp im Datensatz aktualisieren
//aktualisiere_online($u_id);

// Aktuelle Privat- und Systemnachrichten oder Statusmeldung ausgeben
if (!chat_lese($o_id, $o_raum, $u_id, TRUE, $ignore, 10, $benutzerdaten, TRUE, $user)) {
	echo $t['chat_msg106'];
}

$query = "UPDATE chat SET c_gelesen=1 WHERE c_gelesen=0 AND c_typ='P' AND c_von_user_id=".$user;
$pmu = sqlUpdate($query, true);
reset_system("userliste");
?>
</body>
</html>