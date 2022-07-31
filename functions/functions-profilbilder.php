<?php
function home_info() {
	// Zeigt die öffentlichen Benutzerdaten an
	global $userdata, $level, $lang, $u_id;
	
	$text = "";
	
	$text .= "<table class=\"tabelle_kopf_zentriert\" style=\"width:100%;\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_koerper\" style=\"width:50%; vertical-align:top;\">\n";
	
	// Bildinfos lesen und in Array speichern
	$query = pdoQuery("SELECT `b_name`, `b_height`, `b_width`, `b_mime` FROM `bild` WHERE `b_user` = :b_user", [':b_user'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		unset($bilder);
		foreach($result as $zaehler => $row) {
			$bilder[$row['b_name']]['b_mime'] = $row['b_mime'];
			$bilder[$row['b_name']]['b_width'] = $row['b_width'];
			$bilder[$row['b_name']]['b_height'] = $row['b_height'];
		}
	}
	
	if (!isset($bilder)) {
		$bilder = "";
	}
	
	$text .= "<table style=\"width:100%;\">";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$lang[homepage_hintergrundgrafik]</td>";
	$text .= "</tr>\n";
	$text .= home_bild("ui_bild4", $bilder);
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$lang[homepage_hintergrundgrafik_des_inhalts]</td>";
	$text .= "</tr>\n";
	$text .= home_bild("ui_bild5", $bilder);
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$lang[homepage_hintergrundgrafik_der_grafiken]</td>";
	$text .= "</tr>\n";
	$text .= home_bild("ui_bild6", $bilder);
	$text .= "</table>";
	
	$text .= "</td>\n";
	$text .= "<td class=\"tabelle_koerper\" style=\"vertical-align:top;\">\n";
	
	$text .= "<table style=\"width:100%;\">";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\">$lang[homepage_bilder]</td>";
	$text .= "</tr>\n";

	// Bilder - Start
	if (!isset($bilder)) {
		$bilder = "";
	}
	
	$text .= home_bild("ui_bild1", $bilder);
	$text .= home_bild("ui_bild2", $bilder);
	$text .= home_bild("ui_bild3", $bilder);
	
	$text .= "</table>\n";
	// Bilder - Ende
	
	return $text;
}

function home_bild($feld, $bilder) {
	global $u_id, $u_nick, $lang;
	
	$text = "";
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:center; vertical-align:top;\"><br>";
	
	if (is_array($bilder) && isset($bilder[$feld]) && $bilder[$feld]['b_mime']) {
		
		$width = $bilder[$feld]['b_width'];
		$height = $bilder[$feld]['b_height'];
		$mime = $bilder[$feld]['b_mime'];
		
		$info = "<br>Info: " . $width . "x" . $height . " als " . $mime;
		
		$text .= "<img src=\"home_bild.php?u_id=$u_id&feld=$feld\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\"><br>" . $info;
		
		$text .= "<br><b>[<a href=\"inhalt.php?bereich=profilbilder&bildname=$feld\">$lang[benutzer_loeschen]</a>]</b>";
	} else {
		
		$text .= "$lang[user_kein_bild_hochgeladen]" . "<input type=\"file\" name=\"$feld\" size=\"" . (55 / 8) . "\">";
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
	
	global $lang;
	
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
			
			$f['b_user'] = $u_id;
			$f['b_name'] = $name;
			$f['b_width'] = $image[0];
			$f['b_height'] = $image[1];
			
			if ($f['b_mime']) {
				$query = pdoQuery("SELECT `b_id` FROM `bild` WHERE `b_user` = :b_user AND `b_name` = :b_name", [':b_user'=>$u_id, ':b_name'=>$name]);
				
				$resultCount = $query->rowCount();
				if ($resultCount != 0) {
					// Editieren
					$result = $query->fetch();
					pdoQuery("UPDATE `bild` SET `b_user` = :b_user, `b_name` = :b_name, `b_width` = :b_width, `b_height` = :b_height, `b_mime` = :b_mime, `b_bild` = :b_bild WHERE `b_id` = :b_id",
						[
							':b_id'=>$result['b_id'],
							':b_user'=>$f['b_user'],
							':b_name'=>$f['b_name'],
							':b_width'=>$f['b_width'],
							':b_height'=>$f['b_height'],
							':b_mime'=>$f['b_mime'],
							':b_bild'=>$f['b_bild'],
						]);
				} else {
					// Neu erstellen
					pdoQuery("INSERT INTO `bild` (`b_user`, `b_name`, `b_width`, `b_height`, `b_mime`, `b_bild`) VALUES (:b_user, :b_name, :b_width, :b_height, :b_mime, :b_bild)",
						[
							':b_user'=>$f['b_user'],
							':b_name'=>$f['b_name'],
							':b_width'=>$f['b_width'],
							':b_height'=>$f['b_height'],
							':b_mime'=>$f['b_mime'],
							':b_bild'=>$f['b_bild'],
						]);
				}
			} else {
				$fehlermeldung .= $lang['profilbilder_fehlermeldung_falsches_bildformat'];
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
			$fehlermeldung .= $lang['profilbilder_fehlermeldung_falsches_bildformat'];
			unlink($ui_bild);
		}
		
	} else if ($groesse >= ($max_groesse * 1024)) {
		$fehlermeldung .= str_replace("%max_groesse%", $max_groesse, $lang['profilbilder_fehlermeldung_bild_zu_gross']);
	}

	return $fehlermeldung;
}

function bild_loeschen($bildname, $benutzer_id) {
	global $lang;
	
	$text = "";
	if (isset($bildname) && $bildname && $benutzer_id) {
		pdoQuery("DELETE FROM `bild` WHERE `b_name` = :b_name AND `b_user` = :b_user", [':b_name'=>$bildname, ':b_user'=>$benutzer_id]);
		
		$cache = "home_bild";
		$cachepfad = $cache . "/" . substr($benutzer_id, 0, 2) . "/" . $benutzer_id . "/" . $bildname;
		
		if (file_exists($cachepfad)) {
			unlink($cachepfad);
			unlink($cachepfad . "-mime");
		}
		
		$erfolgsmeldung = $lang['profilbilder_erfolgsmeldung_bild_geloescht'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
	} else {
		$fehlermeldung = $lang['profilbilder_fehlermeldung_bild_loeschen'];
		$text .= hinweis($fehlermeldung, "fehler");
	}
	
	return $text;
}
?>