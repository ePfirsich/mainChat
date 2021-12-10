<?php
// Kopf nur ausgeben, wenn $ui_userid oder mit $argv[0] aufgerufen
// Sonst geht der redirekt nicht mehr.

require_once("functions/functions.php");
require_once("languages/$sprache-profil.php");

if ( isset($ui_userid) || (isset($aktion) && $aktion != "") ) {
	require_once("functions/functions-hash.php");
	
	// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, u_level
	if (isset($id)) {
		id_lese($id);
	} else {
		$u_nick = "";
	}
	
	// Hole alle benötigten Einstellungen des Benutzers
	$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");
	
	$title = $body_titel . ' - Home';
	zeige_header_anfang($title, 'mini', '', $benutzerdaten['u_layout_farbe']);
	zeige_header_ende();
	?>
	<body>
	<?php
} else {
	$title = $body_titel . ' - Home';
	zeige_header_anfang($title, 'mini');
	// Aufruf als home.php/USERNAME -> Redirekt auf home.php?USERNAME
	$u_id = "";
	$suchwort = $_SERVER["PATH_INFO"];
	
	if (substr($suchwort, -1) == "/") {
		$suchwort = substr($suchwort, 0, -1);
	}
	$suchwort = strtolower(substr($suchwort, strrpos($suchwort, "/") + 1));
	
	if (strlen($suchwort) >= 4) {
		header("Location: http://" . $_SERVER["HTTP_HOST"]. $_SERVER["SCRIPT_NAME"] . "?" . $suchwort);
	}
}

if (isset($u_id) && $u_id) {
	// Voreinstellungen
	$max_groesse = 60; // Maximale Bild- und Text größe in KB
	
	switch ($aktion) {
		
		case "aendern":
			// Benutzer-ID des Benutzers, dessen Homepage geändert oder angezeigt wird.
			$ui_userid = $u_id;
			
			// Homepage für Benutzer $u_id bearbeiten
			
			// Menü als erstes ausgeben
			echo "<br>";
			$box = $t['profil_home1'];
			$text = "<a href=\"home.php?id=$id&ui_userid=$u_id&aktion=&preview=yes\" target=\"_blank\">Meine Homepage zeigen</a>\n"
				. "| <a href=\"inhalt.php?seite=profil&id=$id\">Profil ändern</a>\n"
				. "| <a href=\"inhalt.php?seite=hilfe&id=$id&aktion=hilfe-community#home\">Hilfe</a>\n";
			
			zeige_tabelle_zentriert($box, $text);
			
			// Bild löschen
			if (isset($loesche) && substr($loesche, 0, 7) <> "ui_bild") {
				unset($loesche);
			}
			
			if (isset($loesche) && $loesche && $ui_userid) {
				$query = "DELETE FROM bild WHERE b_name='" . mysqli_real_escape_string($mysqli_link, $loesche) . "' AND b_user=" . intval($ui_userid);
				$result = mysqli_query($mysqli_link, $query);
				
				$cache = "home_bild";
				$cachepfad = $cache . "/" . substr($ui_userid, 0, 2) . "/" . $ui_userid . "/" . $loesche;
				
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
						bild_holen($ui_userid, $val, $_FILES[$val]['tmp_name'], $_FILES[$val]['size']);
					}
				}
				
				// Änderungen in DB schreiben
				$ui_id = schreibe_db("userinfo", $home, $home['ui_id'], "ui_id");
			}
			
			// Daten laden und Editor anzeigen
			unset($home);
			$query = "SELECT * FROM userinfo WHERE ui_userid=" . intval($ui_userid);
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				// Benutzerprofil aus der Datenbank lesen
				$home = array();
				$home = mysqli_fetch_array($result, MYSQLI_ASSOC);
				
				// Bildinfos lesen und in Array speichern
				$query = "SELECT b_name,b_height,b_width,b_mime FROM bild WHERE b_user=" . intval($ui_userid);
				$result2 = mysqli_query($mysqli_link, $query);
				if ($result2 && mysqli_num_rows($result2) > 0) {
					unset($bilder);
					while ($row = mysqli_fetch_object($result2)) {
						$bilder[$row->b_name]['b_mime'] = $row->b_mime;
						$bilder[$row->b_name]['b_width'] = $row->b_width;
						$bilder[$row->b_name]['b_height'] = $row->b_height;
					}
				}
				mysqli_free_result($result2);
				
				echo "<form enctype=\"multipart/form-data\" name=\"home\" action=\"home.php\" method=\"post\">\n"
					. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
					. "<input type=\"hidden\" name=\"aktion\" value=\"aendern\">\n"
					. "<input type=\"hidden\" name=\"ui_userid\" value=\"$ui_userid\">\n"
					. "<input type=\"hidden\" name=\"home[ui_id]\" value=\"" . $home['ui_id'] . "\">\n"
					. "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . ($max_groesse * 1024) . "\">\n";
				
				if (!isset($bilder)) {
					$bilder = "";
				}
				edit_home($ui_userid, $u_nick, $home, $bilder, $aktion);
				echo "</form>\n";
				
			} else {
				// Erst Profil anlegen
				zeige_tabelle_zentriert($t['neues_profil'], $t['neues_profil_beschreibung']);
			}
			mysqli_free_result($result);
			
			break;
		
		default:
			$hash = genhash($ui_userid);
			//$url = "home.php?/$user_nick";
			$url = "zeige_home.php?ui_userid=$ui_userid&hash=$hash";
			if (isset($preview) && $preview == "yes") {
				$url = "zeige_home.php?ui_userid=$ui_userid&hash=$hash&preview=yes&preview_id=$id";
			}
			?>
			<!DOCTYPE html>
			<html dir="ltr" lang="de">
			<head>
			<title>DEREFER</title>
			<meta charset="utf-8">
			<link rel="stylesheet" href="css/style.css" type="text/css">
			<style type="text/css">
			body {
				background-color:#ffffff;
			}
			a, a:link {
				color:#666666;
			}
			a:visited, a:active {
				color:#666666;
			}
			</style>
			<?php
			$meta_refresh = '<meta http-equiv="refresh" content="0; URL=' . $url . '">';
			zeige_header_ende($meta_refresh);
			?>
			<body>
			<table width="100%" height="100%" border="0">
				<tr>
					<td align="center"><a href="<?php echo $url; ?>">Einen Moment bitte, die angeforderte Seite wird geladen...</a></td>
				</tr>
			</table>
			<?php
	}
} else {
	require_once("functions/functions-home.php");
	if (!isset($ui_userid)) {
		$ui_userid = -1;
	}
	zeige_home($ui_userid, FALSE);
}
?>
</body>
</html>