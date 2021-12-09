<?php

require_once("functions/functions.php");
require_once("functions/functions-msg.php");
require_once("languages/$sprache-chat.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
if( $id == '') {
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_URL);
}

$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
if( $aktion == "") {
	$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
}

$text = filter_input(INPUT_POST, 'text');
if( $text == '') {
	$text = filter_input(INPUT_GET, 'text');
}
$text2 = filter_input(INPUT_POST, 'text2');

// Sonderfix für das Rotieren von text und text2 für ohne javascript...
if (isset($text2) && strlen($text2) != 0) {
	$text = $text2;
}

$privat = filter_input(INPUT_POST, 'privat', FILTER_SANITIZE_STRING);

$user_chat_back = filter_input(INPUT_POST, 'user_chat_back', FILTER_SANITIZE_NUMBER_INT);

// Das Level aus der Tabelle "online" holen
$level_query = "SELECT `o_level` FROM `online` WHERE `o_hash`='$id';";
$level_result = mysqli_query($mysqli_link, $level_query);
$level_row = mysqli_fetch_object($level_result);
$u_level = $level_row->o_level;

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == "") {
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "chateingabe");

$title = $body_titel;
zeige_header_anfang($title, 'chatunten', '', $benutzerdaten['u_layout_farbe']);

// Ermitteln, ob sich der Benutzer im Chat oder im Forum aufhält
if ($o_raum && $o_raum == "-1") {
	$wo_online = "forum";
} else {
	$wo_online = "chat";
}

// Reset ausführen
if($aktion == "reset") {
	reset_system($wo_online);
}

// Dies ist alles nur für den Chat relevant
if($wo_online == "chat") {
	schreibe_nachricht_chat($text, $privat, $user_chat_back, $o_id, $benutzerdaten);
}
?>
</body>
</html>