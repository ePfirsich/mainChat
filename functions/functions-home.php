<?php

function home_info($u_id, $u_nick, $home, $feld, $bilder) {
	// Zeigt die öffentlichen Benutzerdaten an
	global $mysqli_link, $id, $f1, $f2, $f3, $f4, $userdata, $t, $level, $t;
	
	$text = "";
	
	$text .= "<table style=\"border-radius: 3px; background-color: #$home[ui_ueberschriften_hintergrundfarbe]; width: 99%; margin: auto; margin-bottom: 10px;\">\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" style=\"background-color: #$home[ui_ueberschriften_hintergrundfarbe]; color: #$home[ui_ueberschriften_textfarbe]; font-weight: bold; padding: 5px;\">$t[homepage_von] $u_nick</td>\n";
	$text .= "</tr>\n";
	$text .= "<tr>\n";
	
	
	// Informationen / Text über mich - Start
	if (true) {
		$tabellenhintergrund = "background-color:#$home[ui_inhalt_hintergrundfarbe]; background-image: url(home_bild.php?u_id=$u_id&feld=ui_bild5);";
	} else {
		$tabellenhintergrund = "background-color:#$home[ui_inhalt_hintergrundfarbe];";
	}
	$text .= "<td style=\"color: #$home[ui_inhalt_textfarbe]; $tabellenhintergrund padding-left:2px; padding-right:2px; width:60%; vertical-align:top;\">\n";
	
	$text .= "<table style=\"width:100%;\">\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" style=\"background-color: #$home[ui_ueberschriften_hintergrundfarbe]; color: #$home[ui_ueberschriften_textfarbe]; font-weight: bold; padding: 5px;\">$t[homepage_informationen]</td>\n";
	$text .= "</tr>\n";
	
	// Benutzerdaten lesen
	$query = "SELECT user.*,o_id, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online, "
		. "date_format(u_login,'%d.%m.%y %H:%i') AS login FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$userdata = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$userdata['u_chathomepage'] = 0;
		$userdata['u_punkte_anzeigen'] = 0;
		
		$online_zeit = $userdata['online'];
		$letzter_login = $userdata['login'];
		
		mysqli_free_result($result);
		
		// Benutzername
		$text .= "<tr>\n";
		$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_benutzername]:$f2</td>\n";
		$text .= "<td><b>" . zeige_userdetails($userdata['u_id'], $userdata) . "</b></td>";
		$text .= "</tr>\n";
		
		// Onlinezeit oder letzter Login
		$text .= "<tr>\n";
		if ($userdata['o_id'] != "NULL" && $userdata['o_id']) {
			$text .= "<td>&nbsp;</td>";
			$text .= "<td style=\"vertical-align:top;\"><b>"
				. $f1 . str_replace("%online%", gmdate("H:i:s", $online_zeit), $t['chat_msg92']) . $f2 . "</b></td>\n";
		} else {
			$text .= "<td>&nbsp;</td>";
			$text .= "<td style=\"vertical-align:top;\"><b>" . $f1 . str_replace("%login%", $letzter_login, $t['chat_msg94']) . $f2 . "</b></td>\n";
		}
		$text .= "</tr>\n";
		
		// Level
		$text .= "<tr>\n";
		$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_level]:$f2</td>\n";
		$text .= "<td><b>" . $f1 . $level[$userdata['u_level']] . $f2 . "</b></td>\n";
		$text .= "</tr>\n";
		
		// Punkte
		if ($userdata['u_punkte_gesamt']) {
			if ($userdata['u_punkte_datum_monat'] != date("n", time())) {
				$userdata['u_punkte_monat'] = 0;
			}
			if ($userdata['u_punkte_datum_jahr'] != date("Y", time())) {
				$userdata['u_punkte_jahr'] = 0;
			}
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">" . $f1 . $t['benutzer_punkte'] . ":" . $f2 . "</td>\n";
			$text .= "<td><b>" . $f1 . $userdata['u_punkte_gesamt'] . "/" . $userdata['u_punkte_jahr'] . "/" . $userdata['u_punkte_monat'] . "&nbsp;"
				. str_replace("%jahr%", strftime("%Y", time()), str_replace("%monat%", strftime("%B", time()), $t['benutzer_punkte_anzeige'])) . $f2 . "</b></td>\n";
			$text .= "</tr>\n";
		}
		
		// Leerzeile
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\">&nbsp;</td>\n";
		$text .= "</tr>\n";
	}
	
	if ($home['ui_userid']) {
		// Profil vorhanden
		
		// Wohnort
		if( $home['ui_wohnort'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_wohnort]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_wohnort'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_geburt'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_geburt]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_geburt'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_geschlecht'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_geschlecht]:$f2</td>\n";
			$text .= "<td>" . $f1 . zeige_profilinformationen_von_id("geschlecht", $home['ui_geschlecht']) . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_beziehungsstatus'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_beziehungsstatus]:$f2</td>\n";
			$text .= "<td>" . $f1 . zeige_profilinformationen_von_id("beziehungsstatus", $home['ui_beziehungsstatus']) . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_typ'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\"> $f1$t[profil_typ]:$f2</td>\n";
			$text .= "<td>" . $f1 . zeige_profilinformationen_von_id("typ", $home['ui_typ']) . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_beruf'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_beruf]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_beruf'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsfilm'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_lieblingsfilm]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_lieblingsfilm'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsserie'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_lieblingsserie]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_lieblingsserie'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsbuch'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_lieblingsbuch]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_lieblingsbuch'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsschauspieler'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_lieblingsschauspieler]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_lieblingsschauspieler'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsgetraenk'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_lieblingsgetraenk]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_lieblingsgetraenk'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsgericht'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_lieblingsgericht]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_lieblingsgericht'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsspiel'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_lieblingsspiel]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_lieblingsspiel'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsfarbe'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_lieblingsfarbe]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_lieblingsfarbe'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_hobby'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_hobby]:$f2</td>\n";
			$text .= "<td>" . $f1 . $home['ui_hobby'] . $f2 . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_homepage'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\">$f1$t[profil_webseite]:$f2</td>\n";
			$text .= "<td>$f1<a href=\"redirect.php?url=" . urlencode($home['ui_homepage']) . "\" target=\"_blank\">" . $home['ui_homepage'] . "</a>$f2</td>";
			$text .= "</tr>\n";
		}
	}
	
	$text_inhalt = $home[$feld];
	
	if($text_inhalt != "") {
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\">&nbsp;</td></tr>\n";
		$text .= "<tr>\n";
		
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\" style=\"background-color: #$home[ui_ueberschriften_hintergrundfarbe]; color: #$home[ui_ueberschriften_textfarbe]; font-weight: bold; padding: 5px;\">$t[homepage_text_ueber_mich]</td>\n";
		$text .= "</tr>\n";
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\" style=\"vertical-align:top;\">" . $text_inhalt . "</td>\n";
		$text .= "</tr>\n";
	}
	$text .= "</table>\n";
	// Informationen / Text über mich - Ende
	
	$text .= "</td>\n";
	
	if (true) {
		$tabellenhintergrund = "background-color:#$home[ui_inhalt_hintergrundfarbe]; background-image: url(home_bild.php?u_id=$u_id&feld=ui_bild6);";
	} else {
		$tabellenhintergrund = "background-color:#$home[ui_inhalt_hintergrundfarbe];";
	}
	
	$text .= "<td style=\"color: #$home[ui_inhalt_textfarbe]; $tabellenhintergrund padding-left:2px; padding-right:2px; vertical-align:top;\">\n";

	// Bilder - Start
	$text .= "<table style=\"width:100%;\">\n";
	$text .= "<tr>\n";
	$text .= "<td style=\"background-color: #$home[ui_ueberschriften_hintergrundfarbe]; color: #$home[ui_ueberschriften_textfarbe]; font-weight: bold; padding: 5px;\">$t[homepage_bilder]</td>\n";
	$text .= "</tr>\n";
	$text .= "<tr>\n";
	
	if (!isset($bilder)) {
		$bilder = "";
	}
	
	$text .= home_bild($u_id, $row->u_nick, $home, "ui_bild1", $bilder);
	$text .= home_bild($u_id, $row->u_nick, $home, "ui_bild2", $bilder);
	$text .= home_bild($u_id, $row->u_nick, $home, "ui_bild3", $bilder);
	
	$text .= "</table>\n";
	// Bilder - Ende
	
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	return $text;
}

function home_bild(
	$u_id,
	$u_nick,
	$home,
	$feld,
	$bilder) {
	
	global $f3, $f4, $id, $t;
	
	$text = "";
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:center; vertical-align:top;\"><br>";
	
	if (is_array($bilder) && isset($bilder[$feld]) && $bilder[$feld]['b_mime']) {
		
		$width = $bilder[$feld]['b_width'];
		$height = $bilder[$feld]['b_height'];
		$mime = $bilder[$feld]['b_mime'];
		
		$text .= "<img src=\"home_bild.php?u_id=$u_id&feld=$feld\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\"><br>";
	} else {
		$text .= "";
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
	
	global $max_groesse, $mysqli_link;
	
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
				$query = "SELECT b_id FROM bild WHERE b_user=$u_id AND b_name='" . mysqli_real_escape_string($mysqli_link, $name) . "'";
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) != 0) {
					$b_id = mysqli_result($result, 0, 0);
				}
				schreibe_db("bild", $f, $b_id, "b_id");
			} else {
				echo "<P><b>Fehler: </b> Es wurde kein gültiges Bildformat (PNG, JPEG, GIF, Flash) hochgeladen!</P>\n";
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
			echo "<P><b>Fehler: </b> Es wurde kein gültiges Bildformat (PNG, JPEG, GIF, Flash) hochgeladen!</P>\n";
			unlink($ui_bild);
		}
		
	} elseif ($groesse >= ($max_groesse * 1024)) {
		echo "<P><b>Fehler: </b> Das Bild muss kleiner als $max_groesse KB sein!</P>\n";
	}

	return ($home); // TODO: Wo wird $home definiert?
}

function home_url_parse($tag, $url)
{
	return ("$tag=\"redirect.php?url=" . urlencode($url) . "\" target=\"_blank\"");
}

function zeige_home($u_id, $force = FALSE) {
	// Zeigt die Homepage des Benutzers u_id an
	global $mysqli_link, $argv, $argc, $id, $check_name, $t;
	
	if ($u_id && $u_id <> -1) {
		// Aufruf als home.php?ui_userid=USERID
		$query = "SELECT `u_id`, `u_nick`, `u_chathomepage` FROM `user` WHERE `u_id`=$u_id";
		
	} else if ($u_id == -1 && isset($_SERVER['QUERY_STRING'])) {
		$nicknamen = $new_string=substr($_SERVER['QUERY_STRING'],1);
		// Aufruf als home.php?USERNAME
		$tempnick = mysqli_real_escape_string($mysqli_link, strtolower(urldecode($nicknamen)) );
		$tempnick = coreCheckName($tempnick, $check_name);
		
		$query = "SELECT `u_id`, `u_nick`, `u_chathomepage` FROM `user` WHERE `u_nick` = '" . $tempnick . "' and u_level in ('A','C','G','M','S','U')";
	}
	
	// Benutzerdaten lesen
	if ($query) {
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
			$u_chathomepage = $row->u_chathomepage;
			$u_id = $row->u_id;
		}
		mysqli_free_result($result);
	}
	
	// Profil lesen
	if ($u_id) {
		$query = "SELECT * FROM userinfo WHERE ui_userid=$u_id";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$home = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$ok = TRUE;
		} else {
			$ok = FALSE;
		}
		mysqli_free_result($result);
		
		// Bildinfos lesen und in Array speichern
		$query = "SELECT b_name,b_height,b_width,b_mime FROM bild WHERE b_user=$u_id";
		$result2 = mysqli_query($mysqli_link, $query);
		if ($result2 && mysqli_num_rows($result2) > 0) {
			unset($bilder);
			while ($row2 = mysqli_fetch_object($result2)) {
				$bilder[$row2->b_name]['b_mime'] = $row2->b_mime;
				$bilder[$row2->b_name]['b_width'] = $row2->b_width;
				$bilder[$row2->b_name]['b_height'] = $row2->b_height;
			}
		}
		mysqli_free_result($result2);
		
		// Falls nicht freigeschaltet....
		if (!isset($u_chathomepage)) {
			$u_chathomepage = '0';
		}
		if (!$force && $u_chathomepage != "1") {
			$ok = FALSE;
		}
	} else {
		$ok = FALSE;
	}
	
	if (true) {
		$bg = "background-color:#$home[ui_hintergrundfarbe]; background-image: url(home_bild.php?u_id=$u_id&feld=ui_bild4);";
	} else {
		$bg = "background-color:#$home[ui_hintergrundfarbe];";
	}
	
	if ($ok) {
		?>
		<style>
		body {
			color:#<?php echo $home['ui_inhalt_textfarbe']; ?>;
			<?php echo $bg; ?>
		}
		
		a {
			color:#<?php echo $home['ui_inhalt_linkfarbe']; ?>;
		}
		
		a:active {
			color:#<?php echo $home['ui_inhalt_linkfarbe']; ?>;
		}
		</style>
		</head>
		<body>
		<?php
		echo home_info($u_id, $row->u_nick, $home, "ui_text", $bilder);
	} else if ($u_chathomepage != "1") {
		echo "<body>"
			. "<p><b>Fehler: Dieser Benutzer hat keine Homepage!</b></p>";
		
	} else {
		echo "<body>"
			. "<p><b>Fehler: Aufruf ohne gültige Parameter!</b></p>";
		
	}
}
?>