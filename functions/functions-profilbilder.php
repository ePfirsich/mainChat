<?php
function home_info($home) {
	// Zeigt die öffentlichen Benutzerdaten an
	global $userdata, $t, $level, $t, $u_id, $u_nick;
	
	$text = "";
	
	$text .= "<table class=\"tabelle_kopf_zentriert\" style=\"width:100%;\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_koerper\" style=\"width:50%; vertical-align:top;\">\n";
	
	// Bildinfos lesen und in Array speichern
	$query = "SELECT b_name,b_height,b_width,b_mime FROM bild WHERE b_user=$u_id";
	$result2 = sqlQuery($query);
	if ($result2 && mysqli_num_rows($result2) > 0) {
		unset($bilder);
		while ($row = mysqli_fetch_object($result2)) {
			$bilder[$row->b_name]['b_mime'] = $row->b_mime;
			$bilder[$row->b_name]['b_width'] = $row->b_width;
			$bilder[$row->b_name]['b_height'] = $row->b_height;
		}
	}
	mysqli_free_result($result2);
	
	if (!isset($bilder)) {
		$bilder = "";
	}
	
	$text .= "<table style=\"width:100%;\">";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$t[homepage_hintergrundgrafik]</td>";
	$text .= "</tr>\n";
	$text .= home_bild($home, "ui_bild4", $bilder);
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$t[homepage_hintergrundgrafik_des_inhalts]</td>";
	$text .= "</tr>\n";
	$text .= home_bild($home, "ui_bild5", $bilder);
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$t[homepage_hintergrundgrafik_der_grafiken]</td>";
	$text .= "</tr>\n";
	$text .= home_bild($home, "ui_bild6", $bilder);
	$text .= "</table>";
	
	$text .= "</td>\n";
	$text .= "<td class=\"tabelle_koerper\" style=\"vertical-align:top;\">\n";
	
	$text .= "<table style=\"width:100%;\">";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$t[homepage_bilder]</td>";
	$text .= "</tr>\n";

	// Bilder - Start
	if (!isset($bilder)) {
		$bilder = "";
	}
	
	$text .= home_bild($home, "ui_bild1", $bilder);
	$text .= home_bild($home, "ui_bild2", $bilder);
	$text .= home_bild($home, "ui_bild3", $bilder);
	
	$text .= "</table>\n";
	// Bilder - Ende
	
	return $text;
}

function home_bild(
	$home,
	$feld,
	$bilder) {
	
	global $u_id, $u_nick, $t;
	
	$text = "";
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:center; vertical-align:top;\"><br>";
	
	if (is_array($bilder) && isset($bilder[$feld]) && $bilder[$feld]['b_mime']) {
		
		$width = $bilder[$feld]['b_width'];
		$height = $bilder[$feld]['b_height'];
		$mime = $bilder[$feld]['b_mime'];
		
		$info = "<br>Info: " . $width . "x" . $height . " als " . $mime;
		
		$text .= "<img src=\"home_bild.php?u_id=$u_id&feld=$feld\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\"><br>" . $info;
		
		$text .= "<br><b>[<a href=\"inhalt.php?bereich=profilbilder&bildname=$feld\">$t[benutzer_loeschen]</a>]</b>";
	} else {
		
		$text .= "$t[user_kein_bild_hochgeladen]" . "<input type=\"file\" name=\"$feld\" size=\"" . (55 / 8) . "\">";
		$text .= "<br>" . "<input type=\"submit\" value=\"GO\">";
		
	}
	
	$text .= "<br>";
	$text .= "<br>";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	
	return $text;
}

function bild_holen($u_id, $name, $ui_bild, $groesse) {
	// Prüft hochgeladenes Bild und speichert es in die Datenbank
	// u_id = ID des Benutzers, dem das Bild gehört
	// 
	// Binäre Bildinformation -> home[ui_bild]
	// WIDTH				  -> home[ui_bild_width] 
	// HEIGHT				 -> home[ui_bild_height]
	// MIME-TYPE			  -> home[ui_bild_mime]
	
	global $t;
	
	$max_groesse = 60; // Maximale Bild- und Text größe in KB
	
	$fehlermeldung = '';
	if ($ui_bild && $groesse > 0 && $groesse < ($max_groesse * 1024)) {
		$image = getimagesize($ui_bild);
		
		if (is_array($image)) {
			$fd = fopen($ui_bild, "rb");
			if ($fd) {
				$f['b_bild'] = fread($fd, filesize($ui_bild));
				fclose($fd);
			}
			
			switch ($image[2]) {
				case 1:
					$f['b_mime'] = "image/gif";
					break;
				case 2:
					$f['b_mime'] = "image/jpeg";
					break;
				case 3:
					$f['b_mime'] = "image/png";
					break;
				
				default:
					$f['b_mime'] = "";
			}
			
			$f['b_width'] = $image[0];
			$f['b_height'] = $image[1];
			$f['b_user'] = $u_id;
			$f['b_name'] = $name;
			
			if ($f['b_mime']) {
				$query = "SELECT b_id FROM bild WHERE b_user=$u_id AND b_name='" . escape_string($name) . "'";
				$result = sqlQuery($query);
				if ($result && mysqli_num_rows($result) != 0) {
					$b_id = mysqli_result($result, 0, 0);
				}
				schreibe_db("bild", $f, $b_id, "b_id");
			} else {
				$fehlermeldung .= $t['profilbilder_fehlermeldung_falsches_bildformat'];
			}
			
			// Bild löschen
			unlink($ui_bild);
			
			// Cache löschen
			$cache = "home_bild";
			$cachepfad = $cache . "/" . substr($u_id, 0, 2) . "/" . $u_id . "/" . $name;
			if (file_exists($cachepfad)) {
				unlink($cachepfad);
				unlink($cachepfad . "-mime");
			}
			
		} else {
			$fehlermeldung .= $t['profilbilder_fehlermeldung_falsches_bildformat'];
			unlink($ui_bild);
		}
		
	} else if ($groesse >= ($max_groesse * 1024)) {
		$fehlermeldung .= str_replace("%max_groesse%", $max_groesse, $t['profilbilder_fehlermeldung_bild_zu_gross']);
	}

	return $fehlermeldung;
}

function bild_loeschen($bildname, $benutzer_id) {
	global $t;
	
	$text = "";
	if (isset($bildname) && $bildname && $benutzer_id) {
		$queryLoeschen = "DELETE FROM bild WHERE b_name='" . escape_string($bildname) . "' AND b_user=" . intval($benutzer_id);
		$resultLoeschen = sqlUpdate($queryLoeschen);
		
		$cache = "home_bild";
		$cachepfad = $cache . "/" . substr($benutzer_id, 0, 2) . "/" . $benutzer_id . "/" . $bildname;
		
		if (file_exists($cachepfad)) {
			unlink($cachepfad);
			unlink($cachepfad . "-mime");
		}
		
		$erfolgsmeldung = $t['profilbilder_erfolgsmeldung_bild_geloescht'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
	} else {
		$fehlermeldung = $t['profilbilder_fehlermeldung_bild_loeschen'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	return $text;
}
?>