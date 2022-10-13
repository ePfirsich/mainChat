<?php
require_once("./functions/functions.php");
require_once("./functions/functions-msg.php");
require_once("./functions/functions-schreibe.php");
require_once("./languages/$sprache-chat.php");

$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_URL);
if( $aktion == "") {
	$aktion = filter_input(INPUT_GET, 'aktion', FILTER_SANITIZE_URL);
}

$text = filter_input(INPUT_POST, 'text');
if( $text == '') {
	$text = filter_input(INPUT_GET, 'text');
}

$username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_STRING);

$privat = filter_input(INPUT_POST, 'privat', FILTER_SANITIZE_STRING);

// Das Level aus der Tabelle "online" holen
$query = pdoQuery("SELECT `o_level` FROM `online` WHERE `o_hash` = :o_hash", [':o_hash'=>$id]);

$result = $query->fetch();
$u_level = $result['o_level'];

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese();

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe']);
echo "<body>";

schreibe_nachricht_chat($text, $privat, $o_id, $benutzerdaten);
?>
</body>
</html>