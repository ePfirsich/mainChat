<?php
require_once("functions/functions.php");
require_once("functions/functions-moderator.php");
require_once("languages/$sprache-chat.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

// Falls keine Texte zur Moderation gefunden wurden, nach 10 Sek reload
$moderations_zeilen = anzahl_moderationstexte($o_raum);
if ($moderations_zeilen == 0) {
	$meta_refresh = '<meta http-equiv="refresh" content="10; URL=moderator.php">';
}
$meta_refresh .= "<script>\n" . " function chat_reload(file) {\n" . "  parent.chat.location.href=file;\n}\n" . "</script>\n";
$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh);

echo "<body class=\"chatunten\">";

if ($moderationsmodul == 1) {
	if (!isset($mode)) {
		$mode = "";
	}
	
	switch ($mode) {
		case "answer":
		// antwort anzeigen
			zeige_moderations_antworten($o_raum);
			break;
		
		case "answernew":
		// antwort anlegen oder nach editieren neu schreiben
			unset($f);
			$f['c_text'] = $answertxt;
			$f['c_von_user'] = $u_nick;
			$f['c_an_user'] = $u_id;
			$f['c_raum'] = $o_raum;
			$f['c_farbe'] = $u_farbe;
			$f['c_von_user_id'] = $u_id;
			$f['c_moderator'] = $u_id;
			$f['c_typ'] = "P";
			if (!isset($answer))
				$answer = 0;
			schreibe_db("moderation", $f, $answer, "c_id");
			zeige_moderations_antworten($o_raum);
			break;
		
		case "answeredit":
		// antwort editieren
			zeige_moderations_antworten($o_raum, $answer);
			break;
		
		case "answerdel":
		// antwort löschem
			if ($answer != "") {
				$answer = intval($answer);
				$query = "DELETE FROM moderation WHERE c_id=$answer";
				sqlUpdate($query);
			}
			zeige_moderations_antworten($o_raum);
			break;
		
		default:
		// moderationstexte bearbeiten...
		// hierbei auch expire der moderierten Nachrichten...
			bearbeite_moderationstexte($o_raum);
			
			if (!isset($limit) || $limit == "") {
				$rows = zeige_moderationstexte($o_raum);
			} else {
				$rows = zeige_moderationstexte($o_raum, $limit);
			}
			
			if ($rows < 5) {
				echo "<hr type=noshade width=90% height=1>\n";
				echo "<div style=\"text-align:center;\">";
				echo $lang['moderation1'] . "\n";
				echo $lang['moderation2'] . "\n";
				echo $lang['moderation3'] . "\n";
				echo $lang['moderation4'] . "\n";
				echo "</div>";
			}
			echo "\n\n";
			flush();
	}
} else {
	echo "<div style=\"text-align:center;\">";
	echo $lang['moderation1'];
	echo $lang['moderation8'];
	echo "</div>";
}
?>
</body>
</html>