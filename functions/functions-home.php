<?php

function home_info($u_id, $u_nick, $home, $feld, $bilder) {
	// Zeigt die öffentlichen Benutzerdaten an
	global $id, $userdata, $t, $level, $t;
	
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
	$query = "SELECT user.*,o_id, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online, date_format(u_login,'%d.%m.%y %H:%i') AS login FROM user LEFT JOIN online ON o_user=u_id WHERE u_id=$u_id";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) == 1) {
		$userdata = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$userdata['u_chathomepage'] = 0;
		$userdata['u_punkte_anzeigen'] = 0;
		
		$online_zeit = $userdata['online'];
		$letzter_login = $userdata['login'];
		
		mysqli_free_result($result);
		
		// Benutzername
		$text .= "<tr>\n";
		$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_benutzername]:</td>\n";
		$text .= "<td><b>" . zeige_userdetails($userdata['u_id'], $userdata) . "</b></td>";
		$text .= "</tr>\n";
		
		// Onlinezeit oder letzter Login
		$text .= "<tr>\n";
		if ($userdata['o_id'] != "NULL" && $userdata['o_id']) {
			$text .= "<td>&nbsp;</td>";
			$text .= "<td style=\"vertical-align:top;\" class=\"smaller\"><b>"
				. str_replace("%online%", gmdate("H:i:s", $online_zeit), $t['chat_msg92']) . "</b></td>\n";
		} else {
			$text .= "<td>&nbsp;</td>";
			$text .= "<td style=\"vertical-align:top;\" class=\"smaller\"><b>" . str_replace("%login%", $letzter_login, $t['chat_msg94']) . "</b></td>\n";
		}
		$text .= "</tr>\n";
		
		// Level
		$text .= "<tr>\n";
		$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_level]:</td>\n";
		$text .= "<td class=\"smaller\"><b>" . $level[$userdata['u_level']] . "</b></td>\n";
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
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">" . $t['benutzer_punkte'] . ":" . "</td>\n";
			$text .= "<td class=\"smaller\"><b>" . $userdata['u_punkte_gesamt'] . "/" . $userdata['u_punkte_jahr'] . "/" . $userdata['u_punkte_monat'] . "&nbsp;"
				. str_replace("%jahr%", strftime("%Y", time()), str_replace("%monat%", strftime("%B", time()), $t['benutzer_punkte_anzeige'])) . "</b></td>\n";
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
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_wohnort]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_wohnort'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_geburt'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_geburt]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_geburt'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_geschlecht'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_geschlecht]:</td>\n";
			$text .= "<td class=\"smaller\">" . zeige_profilinformationen_von_id("geschlecht", $home['ui_geschlecht']) . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_beziehungsstatus'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_beziehungsstatus]:</td>\n";
			$text .= "<td class=\"smaller\">" . zeige_profilinformationen_von_id("beziehungsstatus", $home['ui_beziehungsstatus']) . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_typ'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\"> $t[profil_typ]:</td>\n";
			$text .= "<td class=\"smaller\">" . zeige_profilinformationen_von_id("typ", $home['ui_typ']) . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_beruf'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_beruf]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_beruf'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsfilm'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_lieblingsfilm]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsfilm'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsserie'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_lieblingsserie]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsserie'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsbuch'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_lieblingsbuch]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsbuch'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsschauspieler'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_lieblingsschauspieler]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsschauspieler'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsgetraenk'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_lieblingsgetraenk]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsgetraenk'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsgericht'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_lieblingsgericht]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsgericht'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsspiel'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_lieblingsspiel]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsspiel'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsfarbe'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_lieblingsfarbe]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsfarbe'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_hobby'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_hobby]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_hobby'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_homepage'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$t[profil_webseite]:</td>\n";
			$text .= "<td class=\"smaller\"><a href=\"redirect.php?url=" . urlencode($home['ui_homepage']) . "\" rel=\"nofollow\" target=\"_blank\">" . $home['ui_homepage'] . "</a></td>";
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

function home_bild($u_id, $u_nick, $home, $feld, $bilder) {
	
	global $id, $t;
	
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

function zeige_home($u_id, $force = FALSE) {
	// Zeigt die Benutzerseite des Benutzers u_id an
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
		$result = sqlQuery($query);
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
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$home = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$ok = TRUE;
		} else {
			$ok = FALSE;
		}
		mysqli_free_result($result);
		
		// Bildinfos lesen und in Array speichern
		$query = "SELECT b_name,b_height,b_width,b_mime FROM bild WHERE b_user=$u_id";
		$result2 = sqlQuery($query);
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
	} else {
		echo "<body>\n";
		zeige_tabelle_zentriert($t['profil_fehlermeldung'], $t['homepage_falscher_aufruf']);
	}
}
?>