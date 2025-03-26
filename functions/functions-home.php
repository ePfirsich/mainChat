<?php

function home_info($user_id, $u_nick, $home, $feld, $bilder) {
	// Zeigt die öffentlichen Benutzerdaten an
	global $level, $lang, $locale;
	
	$text = "";
	
	$text .= "<table style=\"border-radius: 3px; background-color: #$home[ui_ueberschriften_hintergrundfarbe]; width: 99%; margin: auto; margin-bottom: 10px;\">\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" style=\"background-color: #$home[ui_ueberschriften_hintergrundfarbe]; color: #$home[ui_ueberschriften_textfarbe]; font-weight: bold; padding: 5px;\">$lang[homepage_von] $u_nick</td>\n";
	$text .= "</tr>\n";
	$text .= "<tr>\n";
	
	
	// Informationen / Text über mich - Start
	if (true) {
		$tabellenhintergrund = "background-color:#$home[ui_inhalt_hintergrundfarbe]; background-image: url(home_bild.php?u_id=$user_id&feld=ui_bild5);";
	} else {
		$tabellenhintergrund = "background-color:#$home[ui_inhalt_hintergrundfarbe];";
	}
	$text .= "<td style=\"color: #$home[ui_inhalt_textfarbe]; $tabellenhintergrund padding-left:2px; padding-right:2px; width:60%; vertical-align:top;\">\n";
	
	$text .= "<table style=\"width:100%;\">\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" style=\"background-color: #$home[ui_ueberschriften_hintergrundfarbe]; color: #$home[ui_ueberschriften_textfarbe]; font-weight: bold; padding: 5px;\">$lang[homepage_informationen]</td>\n";
	$text .= "</tr>\n";
	
	// Benutzerdaten lesen
	pdoQuery("SET `lc_time_names` = :lc_time_names", [':lc_time_names'=>$locale]);
	
	$query = pdoQuery("SELECT user.*, `o_id`, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS `online`, date_format(`u_login`,'%d. %M %Y um %H:%i') AS `login` FROM `user` LEFT JOIN `online` ON `o_user` = `u_id` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$result = $query->fetch();
		
		// Benutzername
		$text .= "<tr>\n";
		$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_benutzername]:</td>\n";
		$text .= "<td><b>" . zeige_userdetails($result['u_id'], FALSE, true) . "</b></td>";
		$text .= "</tr>\n";
		
		// Onlinezeit oder letzter Login
		$text .= "<tr>\n";
		if ($result['o_id'] != "NULL" && $result['o_id']) {
			$text .= "<td>&nbsp;</td>";
			$text .= "<td style=\"vertical-align:top;\" class=\"smaller\"><b>"
				. str_replace("%online%", gmdate("H:i:s", $result['online']), $lang['chat_msg92']) . "</b></td>\n";
		} else {
			$text .= "<td>&nbsp;</td>";
			$text .= "<td style=\"vertical-align:top;\" class=\"smaller\"><b>" . str_replace("%login%", $result['login'], $lang['chat_msg94']) . "</b></td>\n";
		}
		$text .= "</tr>\n";
		
		// Level
		$text .= "<tr>\n";
		$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_level]:</td>\n";
		$text .= "<td class=\"smaller\"><b>" . $level[$result['u_level']] . "</b></td>\n";
		$text .= "</tr>\n";
		
		// Punkte
		if ($result['u_punkte_gesamt']) {
			$date = new DateTime();
			$date->format('Y-m-d H:i:s');
			
			if ($result['u_punkte_datum_monat'] != date("n", time())) {
				$result['u_punkte_monat'] = 0;
			}
			if ($result['u_punkte_datum_jahr'] != date("Y", time())) {
				$result['u_punkte_jahr'] = 0;
			}
			
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">" . $lang['benutzer_punkte'] . ":" . "</td>\n";
			$text .= "<td class=\"smaller\"><b>" . $result['u_punkte_gesamt'] . "/" . $result['u_punkte_jahr'] . "/" . $result['u_punkte_monat'] . "&nbsp;"
				. str_replace("%jahr%", formatIntl($date,'Y',$locale), str_replace("%monat%", formatIntl($date,'MMMM',$locale), $lang['benutzer_punkte_anzeige'])) . "</b></td>\n";
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
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_wohnort]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_wohnort'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_geburt'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_geburt]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_geburt'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_geschlecht'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_geschlecht]:</td>\n";
			$text .= "<td class=\"smaller\">" . zeige_profilinformationen_von_id("geschlecht", $home['ui_geschlecht']) . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_beziehungsstatus'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_beziehungsstatus]:</td>\n";
			$text .= "<td class=\"smaller\">" . zeige_profilinformationen_von_id("beziehungsstatus", $home['ui_beziehungsstatus']) . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_typ'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\"> $lang[profil_typ]:</td>\n";
			$text .= "<td class=\"smaller\">" . zeige_profilinformationen_von_id("typ", $home['ui_typ']) . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_beruf'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_beruf]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_beruf'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsfilm'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_lieblingsfilm]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsfilm'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsserie'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_lieblingsserie]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsserie'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsbuch'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_lieblingsbuch]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsbuch'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsschauspieler'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_lieblingsschauspieler]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsschauspieler'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsgetraenk'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_lieblingsgetraenk]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsgetraenk'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsgericht'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_lieblingsgericht]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsgericht'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsspiel'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_lieblingsspiel]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsspiel'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_lieblingsfarbe'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_lieblingsfarbe]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_lieblingsfarbe'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_hobby'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_hobby]:</td>\n";
			$text .= "<td class=\"smaller\">" . $home['ui_hobby'] . "</td>";
			$text .= "</tr>\n";
		}
		
		if( $home['ui_homepage'] != "" ) {
			$text .= "<tr>\n";
			$text .= "<td style=\"vertical-align:top; text-align:right; width: 150px;\" class=\"smaller\">$lang[profil_webseite]:</td>\n";
			$text .= "<td class=\"smaller\"><a href=\"" . urlencode($home['ui_homepage']) . "\" rel=\"nofollow\" target=\"_blank\">" . $home['ui_homepage'] . "</a></td>";
			$text .= "</tr>\n";
		}
	}
	
	$text_inhalt = $home[$feld];
	
	if($text_inhalt != "") {
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\">&nbsp;</td></tr>\n";
		$text .= "<tr>\n";
		
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\" style=\"background-color: #$home[ui_ueberschriften_hintergrundfarbe]; color: #$home[ui_ueberschriften_textfarbe]; font-weight: bold; padding: 5px;\">$lang[homepage_text_ueber_mich]</td>\n";
		$text .= "</tr>\n";
		$text .= "<tr>\n";
		$text .= "<td colspan=\"2\" style=\"vertical-align:top;\">" . $text_inhalt . "</td>\n";
		$text .= "</tr>\n";
	}
	$text .= "</table>\n";
	// Informationen / Text über mich - Ende
	
	$text .= "</td>\n";
	
	if (true) {
		$tabellenhintergrund = "background-color:#$home[ui_inhalt_hintergrundfarbe]; background-image: url(home_bild.php?u_id=$user_id&feld=ui_bild6);";
	} else {
		$tabellenhintergrund = "background-color:#$home[ui_inhalt_hintergrundfarbe];";
	}
	
	$text .= "<td style=\"color: #$home[ui_inhalt_textfarbe]; $tabellenhintergrund padding-left:2px; padding-right:2px; vertical-align:top;\">\n";

	// Bilder - Start
	$text .= "<table style=\"width:100%;\">\n";
	$text .= "<tr>\n";
	$text .= "<td style=\"background-color: #$home[ui_ueberschriften_hintergrundfarbe]; color: #$home[ui_ueberschriften_textfarbe]; font-weight: bold; padding: 5px;\">$lang[homepage_bilder]</td>\n";
	$text .= "</tr>\n";
	$text .= "<tr>\n";
	
	if (!isset($bilder)) {
		$bilder = "";
	}
	
	$text .= home_bild($user_id, $u_nick, "ui_bild1", $bilder);
	$text .= home_bild($user_id, $u_nick, "ui_bild2", $bilder);
	$text .= home_bild($user_id, $u_nick, "ui_bild3", $bilder);
	
	$text .= "</table>\n";
	// Bilder - Ende
	
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	return $text;
}

function home_bild($user_id, $u_nick, $feld, $bilder) {
	$text = "";
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:center; vertical-align:top;\"><br>";
	
	if (is_array($bilder) && isset($bilder[$feld]) && $bilder[$feld]['b_mime']) {
		
		$width = $bilder[$feld]['b_width'];
		$height = $bilder[$feld]['b_height'];
		$mime = $bilder[$feld]['b_mime'];
		
		$text .= "<img src=\"home_bild.php?u_id=$user_id&feld=$feld\" style=\"width:".$width."px; height:".$height."px;\" alt=\"$u_nick\"><br>";
	} else {
		$text .= "";
	}
	
	$text .= "<br>";
	$text .= "<br>";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	
	return $text;
}

function zeige_home($user_id, $force = FALSE) {
	// Zeigt die Benutzerseite des Benutzers u_id an
	global $argv, $argc, $check_name, $lang;
	
	$query_string = filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_STRING);
	
	if ($user_id && $user_id <> -1) {
		// Aufruf als home.php?ui_userid=USERID
		$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_chathomepage` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$user_id]);
	} else if ($user_id == -1 && $query_string != "") {
		$username = $new_string=substr($query_string,1);
		// Aufruf als home.php?USERNAME
		$tempnick = strtolower(urldecode($username));
		$tempnick = coreCheckName($tempnick, $check_name);
		
		$query = pdoQuery("SELECT `u_id`, `u_nick`, `u_chathomepage` FROM `user` WHERE `u_nick` = :u_nick AND `u_level` IN ('A','C','G','M','S','U')", [':u_nick'=>$tempnick]);
	}
	
	
	// Benutzerdaten lesen
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$result = $query->fetch();
		$u_chathomepage = $result['u_chathomepage'];
		$user_id = $result['u_id'];
	}
	
	// Profil lesen
	if ($user_id) {
		$query = pdoQuery("SELECT * FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$user_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$home = $query->fetch();
			$ok = TRUE;
		} else {
			$ok = FALSE;
		}
		
		// Bildinfos lesen und in Array speichern
		$query2 = pdoQuery("SELECT `b_name`, `b_height`, `b_width`, `b_mime` FROM `bild` WHERE `b_user` = :b_user", [':b_user'=>$user_id]);
		
		$result2Count = $query2->rowCount();
		if ($resultCount > 0) {
			$result2 = $query2->fetchAll();
			unset($bilder);
			foreach($result2 as $zaehler => $row) {
				$bilder[$row['b_name']]['b_mime'] = $row['b_mime'];
				$bilder[$row['b_name']]['b_width'] = $row['b_width'];
				$bilder[$row['b_name']]['b_height'] = $row['b_height'];
			}
		}
		
		// Falls die Benutzerseite nicht freigeschaltet ist
		if (!isset($u_chathomepage)) {
			$u_chathomepage = '0';
		}
		if (!$force && $u_chathomepage != "1") {
			$ok = FALSE;
		}
	} else {
		$ok = FALSE;
	}
	
	if ($ok) {
		$bg = "background-color:#$home[ui_hintergrundfarbe]; background-image: url(home_bild.php?u_id=$user_id&feld=ui_bild4);";
		
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
		echo home_info($user_id, $result['u_nick'], $home, "ui_text", $bilder);
	} else {
		echo "<body>\n";
		
		$fehlermeldung = $lang['homepage_fehlermeldung_falscher_aufruf'];
		$text = hinweis($fehlermeldung, "fehler");
		
		$box = $lang['profil_benutzerseite'];
		zeige_tabelle_zentriert($box, $text);
	}
}
?>