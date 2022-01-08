<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}

require_once("functions/functions-hash.php");

// Voreinstellungen
$max_groesse = 60; // Maximale Bild- und Text größe in KB

// Benutzerseite für Benutzer $u_id bearbeiten

// Bild löschen
if (isset($loesche) && substr($loesche, 0, 7) <> "ui_bild") {
	unset($loesche);
}

if (isset($loesche) && $loesche) {
	$query = "DELETE FROM bild WHERE b_name='" . mysqli_real_escape_string($mysqli_link, $loesche) . "' AND b_user=$u_id";
	$result = sqlUpdate($query);
	
	$cache = "home_bild";
	$cachepfad = $cache . "/" . substr($u_id, 0, 2) . "/" . $u_id . "/" . $loesche;
	
	if (file_exists($cachepfad)) {
		unlink($cachepfad);
		unlink($cachepfad . "-mime");
	}
}

// Prüfen & in DB schreiben
if (isset($home) && is_array($home) && strlen($home['ui_text']) > ($max_groesse * 1024)) {
	echo "<p><b>Fehler: </b> Ihr Text ist grösser als $max_groesse!</p>";
	unset($home['ui_text']);
}

if (isset($home) && is_array($home) && $home['ui_id']) {
	// Bei Änderung der Einstellung speichern
	// hochgeladene Bilder in DB speichern
	$bildliste = ARRAY("ui_bild1", "ui_bild2", "ui_bild3", "ui_bild4", "ui_bild5", "ui_bild6");
	foreach ($bildliste as $val) {
		if (isset($_FILES[$val]) && is_uploaded_file($_FILES[$val]['tmp_name'])) {
			// Abspeichern
			bild_holen($u_id, $val, $_FILES[$val]['tmp_name'], $_FILES[$val]['size']);
		}
	}
	
	// Änderungen in DB schreiben
	$ui_id = schreibe_db("userinfo", $home, $home['ui_id'], "ui_id");
}

// Daten laden und Editor anzeigen
$query = "SELECT * FROM userinfo WHERE ui_userid=$u_id";
$result = sqlQuery($query);
if ($result && mysqli_num_rows($result) == 1) {
	unset($home);
	$home = array();
	$home = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	// HP-Tabelle ausgeben
	$box = $t['profil_bilder_hochladen'];
	
	$text = "<form enctype=\"multipart/form-data\" name=\"home\" action=\"inhalt.php?bereich=profilbilder\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"aendern\">\n"
			. "<input type=\"hidden\" name=\"ui_userid\" value=\"$u_id\">\n"
		. "<input type=\"hidden\" name=\"home[ui_id]\" value=\"" . $home['ui_id'] . "\">\n"
		. "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . ($max_groesse * 1024) . "\">\n";
	
	$text .= home_info($home);
	
	$text .= "</form>";
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
} else {
	// Erst Profil anlegen
	zeige_tabelle_zentriert($t['neues_profil'], $t['neues_profil_beschreibung']);
}
mysqli_free_result($result);
?>