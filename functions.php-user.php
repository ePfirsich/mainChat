<?php

// functions nur für user.php

require_once("functions.php-func-html_parse.php");
require_once("functions.php-func-raeume_auswahl.php");

function user_liste($larr, $anzahl) {
	// Gibt Benutzerliste $larr als Tabelle aus
	global $t, $admin, $u_level, $adminfeatures, $o_js, $aktion, $u_id, $id, $show_geschlecht, $mysqli_link;
	global $f1, $f2, $f3, $f4, $homep_ext_link;
	global $punkte_grafik, $leveltext, $chat_grafik;
	
	$text = '';
	
	// Array mit oder ohne Javascript ausgeben
	// Kopf Tabelle
	$box = $t['sonst18'];
	
	if (!isset($larr[0]['r_name'])) {
		$larr[0]['r_name'] = "";
	}
	if (!isset($larr[0]['r_besitzer'])) {
		$larr[0]['r_besitzer'] = "";
	}
	if (!isset($larr[0]['r_topic'])) {
		$larr[0]['r_topic'] = "";
	}
	
	$r_name = $larr[0]['r_name'];
	$r_besitzer = $larr[0]['r_besitzer'];
	$r_topic = $larr[0]['r_topic'];
	$level = "user";
	$level2 = "user";
	if ($r_besitzer == $u_id && $adminfeatures) {
		$level = "owner";
	}
	if (($admin || $u_level == "A") && $adminfeatures) {
		$level = "admin";
	}
	if (($u_level == "C" || $u_level == "S") && $adminfeatures) {
		$level2 = "admin";
	}
	
	// Wartetext ausgeben
	if ($anzahl > 10) {
		$text .= $f1 . "<b>$t[sonst23] $anzahl $t[sonst36]</b><br>\n" . $f2;
		}
	flush();
	
	if ($o_js) {
		if ($show_geschlecht == true) {
			$geschl = array();
			foreach ($larr as $k => $v)
				$ids[] = intval($v['u_id']);
			$query = "SELECT ui_userid,ui_geschlecht FROM userinfo WHERE ui_userid in ('"
				. implode("','", $ids) . "')";
			$result = mysqli_query($mysqli_link, $query);
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$uid = $row['ui_userid'];
				if ($row['ui_geschlecht'] == "männlich")
					$geschl[$uid] = "M";
				else if ($row['ui_geschlecht'] == "weiblich")
					$geschl[$uid] = "W";
				else $geschl[$uid] = "";
			}
			mysqli_free_result($result);
		}
		
		// Leveltexte erzeugen
		unset($leveltxt);
		foreach ($leveltext as $key => $val) {
			$leveltxt[] = "leveltext[\"$key\"]=\"$val\"";
		}
		
		// Mit Javascript ausgeben, vollständiges Menü
		for ($k = 0; isset($larr[$k]) AND is_array($larr[$k]) AND $v = $larr[$k]; $k++) {
			if ($level != "admin") {
				$v['hostname'] = "";
				$v['o_ip'] = "";
			} else {
				if (!isset($v['hostname'])) {
					$v['hostname'] = "";
				}
				$v['hostname'] = htmlspecialchars($v['hostname']);
				if (!isset($v['o_ip'])) {
					$v['o_ip'] = "";
				}
			}
			;
			$v['u_away'] = $v['u_away'] ? "y" : "";
			if (!isset($v['u_home_ok'])) {
				$v['u_home_ok'] = "";
			}
			if (!isset($v['u_chathomepage'])) {
				$v['u_chathomepage'] = "";
			}
			if (!isset($v['gruppe'])) {
				$v['gruppe'] = "";
			}
			if ($show_geschlecht == true) {
				$geschlecht = $geschl[$v['u_id']];
				$jsarr[] = "'$v[u_id]','$v[u_chathomepage]','$v[u_nick]','$v[hostname]','$v[o_ip]','$v[u_away]','$v[u_level]','$v[gruppe]','$v[u_home_ok]','$geschlecht' ";
			} else $jsarr[] = "'$v[u_id]','$v[u_chathomepage]','$v[u_nick]','$v[hostname]','$v[o_ip]','$v[u_away]','$v[u_level]','$v[gruppe]','$v[u_home_ok]'";
		}
		$text .= "\n\n<script language=\"JavaScript\">\n"
			. "   var color = new Array('tabelle_zeile1','tabelle_zeile2');\n"
			. "   var fett  = new Array('$f1<b>','</b>$f2','$f3','$f4','$f1','$f2');\n"
			. "   var level = '$level';\n" . "   var level2 = '$level2';\n"
			. "   var ggrafik = new Array('$punkte_grafik[0]','$punkte_grafik[1]','$punkte_grafik[2]','$punkte_grafik[3]');\n"
			. "   var mgrafik = '$chat_grafik[mail]';\n"
			. "   var hgrafik = '$chat_grafik[home]';\n"
			. "   var gegrafik = new Array('$chat_grafik[geschlecht_maennlich]','$chat_grafik[geschlecht_weiblich]');\n"
			. "   var leveltext = new Array();\n" . @implode(";\n", $leveltxt)
			. ";\n" . "   var liste = new Array(\n" . @implode(",\n", $jsarr)
			. ");\n" . "   var homep_ext_link = '$homep_ext_link';\n"
			. "   var show_geschlecht = '$show_geschlecht';\n"
			. "   genlist(liste,'$aktion');\n" . 
			//				 "   stdparm=''; stdparm2=''; id=''; http_host=''; u_nick=''; raum=''; nlink=''; nick=''; url='';\n".
			"</script>\n";
		$text .= "<script language=\"JavaScript\" src=\"popup.js\"></script>\n";
	} else { // kein javascript verfügbar	
		for ($k = 0; is_array($larr[$k]) AND $v = $larr[$k]; $k++) {
			if ( $k % 2 != 0 ) {
				$farbe_tabelle = 'class="tabelle_zeile1"';
			} else {
				$farbe_tabelle = 'class="tabelle_zeile2"';
			}
			
			if ($v['u_away']) {
				$user = "(" . user($v['u_id'], $v, TRUE, FALSE, $trenner = "</td><td>") . ")";
			} else {
				$user = user($v['u_id'], $v, TRUE, FALSE, $trenner = "</td><td>");
			}
			;
			$trow .= "<tr>" . "<td $farbe_tabelle>" . $f1 . $user . $f2 . "</td></tr>";
		} // END-OF-FOR
		
		$text = "<table style=\"width:100%;\">$trow</table>\n";
		
		
	}
	return $text;
}

function user_zeige($user, $admin, $schau_raum, $u_level, $zeigeip) {
	// $user = ID des Benutzers
	// Falls $admin wahr werden IP und Onlinedaten ausgegeben
	
	global $mysqli_link, $level, $id, $f1, $f2, $f3, $f4;
	global $user_farbe, $ist_online_raum, $chat_max_eingabe, $t, $communityfeatures;
	global $chat_grafik, $whotext, $beichtstuhl, $erweitertefeatures, $msgpopup, $serverprotokoll;
	
	$eingabe_breite = 29;
	
	// Benutzer listen
	$query = "SELECT `user`.*,"
		. "FROM_Unixtime(UNIX_TIMESTAMP(u_login),'%d.%m.%Y %H:%i') AS `letzter_login`,"
		. "FROM_Unixtime(UNIX_TIMESTAMP(u_neu),'%d.%m.%Y %H:%i') AS `erster_login` "
		. "FROM `user` WHERE `u_id`=$user ";
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result AND mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_object($result);
		$uu_away = $row->u_away;
		$uu_nick = htmlspecialchars($row->u_nick);
		$uu_id = $row->u_id;
		$uu_email = htmlspecialchars($row->u_email);
		$uu_adminemail = htmlspecialchars($row->u_adminemail);
		$uu_url = htmlspecialchars($row->u_url);
		$uu_level = $row->u_level;
		$uu_farbe = $row->u_farbe;
		$letzter_login = $row->letzter_login;
		$erster_login = $row->erster_login;
		$ip_historie = unserialize($row->u_ip_historie);
		$uu_punkte_gesamt = $row->u_punkte_gesamt;
		$uu_punkte_monat = $row->u_punkte_monat;
		$uu_punkte_jahr = $row->u_punkte_jahr;
		$uu_chathomepage = $row->u_chathomepage;
		$uu_profil_historie = unserialize($row->u_profil_historie);
		$uu_kommentar = $row->u_kommentar;
		
		// Default für Farbe setzen, falls undefiniert
		if (strlen($uu_farbe) == 0) {
			$uu_farbe = $user_farbe;
		}
		
		// IP bestimmen
		unset($o_http_stuff);
		$query = "SELECT r_name,online.*,UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS onlinezeit "
			. " FROM online left join raum on o_raum=r_id WHERE o_user=$user ";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && $rows = mysqli_num_rows($result) == 1) {
			$o_row = mysqli_fetch_object($result);
			$onlinezeit = $o_row->onlinezeit;
			if ($admin) {
				$host_name = htmlspecialchars(gethostbyaddr($o_row->o_ip));
				$o_http_stuff = $o_row->o_http_stuff . $o_row->o_http_stuff2;
			}
			if (isset($o_http_stuff))
				$http_stuff = unserialize($o_http_stuff);
		}
		
		
		// Avatare
		$query2 = "SELECT * FROM user WHERE u_id LIKE '$row->u_id'";
		$result2 = mysqli_query($mysqli_link, $query2);
		
		if ($result2 && mysqli_num_rows($result2) == 1) {
			$row2 = mysqli_fetch_object($result2);
			
			$ui_avatar = $row2->ui_avatar;
			
		}
		
		$query1 = "SELECT * FROM userinfo WHERE ui_userid LIKE '$row->u_id'";
		$result1 = mysqli_query($mysqli_link, $query1);
		
		if ($result1 && mysqli_num_rows($result1) == 1) {
			$row1 = mysqli_fetch_object($result1);
			
			$ui_gen = $row1->ui_geschlecht;
		} else {
			$ui_gen[0] = '';
		}
		
		
		//Avatar pruefen
		$box = $t['user_zeige60'];
		$text = "";
		
		include "./conf/config.php";
		
		$text .= "<br><center>";
		if($ui_avatar) { // Benutzerdefinierter Avatar
			$ava = '<img src="./avatars/'.$ui_avatar.'" style="width:200px; height:200px;" alt="'.$ui_avatar.'" />';
		} else if ($ui_gen[0] == "m") { // Männlicher Standard-Avatar
			$ava = '<img src="./avatars/no_avatar_m.jpg" style="width:200px; height:200px;" alt="" />';
		} else if ($ui_gen[0] == "w") { // Weiblicher Standard-Avatar
			$ava = '<img src="./avatars/no_avatar_w.jpg" style="width:200px; height:200px;" alt="" />';
		} else { // Neutraler Standard-Avatar
			$ava = '<img src="./avatars/no_avatar_es.jpg" style="width:200px; height:200px;" alt="" />';
		}
		$text .= "</center><br>";
		
		// Box anzeigen
		show_box_title_content($box, $text);
		echo "<br>";
		
		
		// Kopf Tabelle "Private Nachricht"
		if (isset($onlinezeit) && $onlinezeit && $u_level != "G") {
			$box = str_replace("%uu_nick%", $uu_nick, $t['user_zeige11']);
			$text = '';
			
			$text .= "<form name=\"form\" method=\"post\" target=\"schreibe\" action=\"schreibe.php\" onSubmit=\"resetinput(); return false;\">";
			
			// Eingabeformular für private Nachricht ausgeben
			$text .= $f1;
			
			if ($msgpopup) {
				$text .= '<iframe src="messages-popup.php?id=' . $id
					. '&user=' . $user
					. '&user_nick=' . $uu_nick
					. '" width=100% height=200 marginwidth=\"0\" marginheight=\"0\" hspace=0 vspace=0 framespacing=\"0\"></iframe>';
			}
			
			$text .= "<input name=\"text2\" size=\"" . $eingabe_breite
				. "\" maxlength=\"" . ($chat_max_eingabe - 50)
				. "\" value=\"\" TYPE=\"text\">"
				. "<input name=\"text\" value=\"\" TYPE=\"hidden\">"
				. "<input name=\"id\" value=\"$id\" TYPE=\"hidden\">"
				. "<input name=\"privat\" value=\"$uu_nick\" TYPE=\"hidden\">"
				. "<input type=\"submit\" value=\"Go!\">" . $f2
				. "\n<script language=\"JavaScript\">\n\n"
				. "document.forms['form'].elements['text2'].focus();\n"
				. "\n</script>\n\n\n";
			
			$text .= "</form>";
			
			// Box anzeigen
			show_box_title_content($box, $text);
			
			echo '<br>';
		}
		
		// Kopf Tabelle Benutzerinfo
		$text = '';
		if (isset($onlinezeit) && $onlinezeit) {
			$box = str_replace("%user%", $uu_nick, $t['user_zeige20']);
		} else {
			$box = str_replace("%user%", $uu_nick, $t['user_zeige21']);
		}
		
		// Ausgabe in Tabelle
		$text .= "<table>";
		$text .= "<tr><td style=\"vertical-align:top;\"><b>" . $f1 . $t['user_zeige18'] . $f2 . "</b></td><td><b>" . user($user, $row, TRUE, FALSE);
		
		if ($uu_away != "") {
			$text .= $f1 . "<br></b>($uu_away)<b>" . $f2;
		}
		
		$text .= "</b></td></tr>\n";
		
		// Raum
		if (isset($o_row) && $o_row->r_name && $o_row->o_who == 0) {
			$text .= "<tr><td><b>" . $f1 . $t['user_zeige23'] . $f2
				. "</b></td><td><b>" . $f1 . $o_row->r_name . "&nbsp;["
				. $whotext[$o_row->o_who] . "]" . $f2 . "</b></td></tr>\n";
		} elseif (isset($o_row) && $o_row->o_who) {
			$text .= "<tr><td>" . $f1 . "&nbsp;" . $f2 . "</td>" . "<td><b>" . $f1
				. "[" . $whotext[$o_row->o_who] . "]" . $f2
				. "</b></td></tr>\n";
		}
		if (isset($onlinezeit) && $onlinezeit) {
			$text .= "<tr><td>" . $f1 . $t['user_zeige33'] . $f2
				. "</td><td style=\"vertical-align:bottom;\">" . $f3
				. gmdate("H:i:s", $onlinezeit) . "&nbsp;" . $t['sonst27'] . $f4
				. "</td></tr>\n";
		} else {
			$text .= "<tr><td>" . $f1 . $t['user_zeige9'] . $f2
				. "</td><td style=\"vertical-align:top;\">" . $f1 . "$letzter_login" . $f2
				. "</td></tr>\n";
		}
		
		if ($erster_login && $erster_login != "01.01.1970 01:00") {
			$text .= "<tr><td>" . $f1 . $t['user_zeige32'] . $f2
				. "</td><td style=\"vertical-align:top;\">" . $f1 . "$erster_login" . $f2
				. "</td></tr>\n";
		}
		
		// Punkte
		if ($communityfeatures && $uu_punkte_gesamt) {
			if ($row->u_punkte_datum_monat != date("n", time())) {
				$uu_punkte_monat = 0;
			}
			if ($row->u_punkte_datum_jahr != date("Y", time())) {
				$uu_punkte_jahr = 0;
			}
			$text .= "<tr><td>" . $f1 . $t['user_zeige38'] . $f2
				. "</td><td style=\"vertical-align:top;\">" . $f3 . $uu_punkte_gesamt . "/"
				. $uu_punkte_jahr . "/" . $uu_punkte_monat . "&nbsp;"
				. str_replace("%jahr%", substr(strftime("%Y", time()), 2, 2),
					str_replace("%monat%",
						substr(strftime("%B", time()), 0, 3),
						$t['user_zeige39'])) . $f4 . "</td></tr>\n";
		}
		
		if ($admin) {
			// Admin E-Mail
			if (strlen($uu_adminemail) > 0) {
				$text .= "<tr><td>" . $f1 . $t['user_zeige3'] . $f2 . "</td><td>"
					. $f3
					. "<a href=\"MAILTO:$uu_adminemail\">$uu_adminemail</a>"
					. $f4 . "</td></tr>\n";
			}
		}
		
		if ($communityfeatures) {
			// Benutzername Sonderzeichen raus für target
			$fenster = str_replace("+", "", $uu_nick);
			$fenster = str_replace("-", "", $fenster);
			$fenster = str_replace("ä", "", $fenster);
			$fenster = str_replace("ö", "", $fenster);
			$fenster = str_replace("ü", "", $fenster);
			$fenster = str_replace("Ä", "", $fenster);
			$fenster = str_replace("Ö", "", $fenster);
			$fenster = str_replace("Ü", "", $fenster);
			$fenster = str_replace("ß", "", $fenster);
			
			$url = "mail.php?aktion=neu2&neue_email[an_nick]="
				. URLENCODE($uu_nick) . "&id=" . $id;
				$text .= "<tr><td>" . $f1 . $t['user_zeige6'] . $f2 . "</td><td>" . $f3
				. "<a href=\"$url\" target=\"640_$fenster\" onClick=\"neuesFenster2('$url'); return(false)\">$chat_grafik[mail]</a>";
			$f4 . "</td></tr>\n";
		} elseif (strlen($uu_email) > 0) {
			$text .= "<tr><td>" . $f1 . $t['user_zeige6'] . $f2 . "</td><td>" . $f3
				. "<a href=\"MAILTO:$uu_email\">$uu_email</a>" . $f4
				. "</td></tr>\n";
		}
		
		if ($communityfeatures && $uu_chathomepage == "J") {
			$url = "home.php?ui_userid=$uu_id&id=" . $id;
			$text .= "<tr><td>" . $f1 . $t['user_zeige7'] . $f2 . "</td><td>" . $f3
				. "<a href=\"$url\" target=\"640_$fenster\" onClick=\"neuesFenster2('$url'); return(false)\">$chat_grafik[home]</a>";
			$f4 . "</td></tr>\n";
		} elseif (strlen($uu_url) > 0) {
			$text .= "<tr><td>" . $f1 . $t['user_zeige7'] . $f2 . "</td><td>" . $f3
				. "<a href=\"$uu_url\" target=\"_blank\">$uu_url</a>" . $f4
				. "</td></tr>\n";
		}
		
		$text .= "<tr><td>" . $f1 . $t['user_zeige8'] . $f2 . "</td><td>" . $f1
			. "$level[$uu_level]" . $f2 . "</td></tr>\n";
		
			$text .= "<tr><td>" . $f1 . $t['user_zeige10'] . $f2 . "</td>"
			. "<td style=\"background-color:#" . $uu_farbe . ";\">&nbsp;</td></tr>\n";
		
		if ($uu_kommentar && $admin) {
			$text .= "<tr><td style=\"vertical-align:top;\">" . $f1 . $t['user_zeige49'] . $f2
				. "</td><td>" . $f3;
			$text .= htmlspecialchars($uu_kommentar) . "<br>\n";
			$text .= $f4 . "</td></tr>\n";
		}
		
		if ($admin) {
			if (is_array($uu_profil_historie)) {
				$text .= "<tr><td style=\"vertical-align:top;\">";
				
				while (list($datum, $nick) = each($uu_profil_historie)) {
					if (!isset($erstes)) {
						$text .= "<tr><td style=\"vertical-align:top;\">" . $f1 . $t['sonst44'] . $f2
							. "</td><td>" . $f3;
						$erstes = TRUE;
					} else {
						$text .= "<tr><td></td><td>" . $f3;
					}
					$text .= $nick . "&nbsp;("
						. str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum))
						. ")" . $f4 . "</td></tr>\n";
				}
				$text .= "</tr>";
			}
			
			// IPs ausgeben
			if (isset($o_row) && $o_row->o_ip) {
				$text .= "<tr><td style=\"vertical-align:top;\">" . $f1 . $t['user_zeige4'] . $f2
					. "</td><td>" . $f3 . $host_name . $f4 . "</td></tr>\n"
					. "<tr><td style=\"vertical-align:top;\">" . $f1 . "IP" . $f2 . "</td><td>"
					. $f3 . $o_row->o_ip . " " . $t['sonst28'] . $f4
					. "</td></tr>\n";
				
				if ($zeigeip == 1 && is_array($ip_historie)) {
					while (list($datum, $ip_adr) = each($ip_historie)) {
						$text .= "<tr><td></td><td>" . $f3 . $ip_adr . "&nbsp;("
							. str_replace(" ", "&nbsp;",
								date("d.m.y H:i", $datum)) . ")" . $f4
							. "</td></tr>\n";
					}
				}
				
				$text .= "<tr><td style=\"vertical-align:top;\">" . $f1 . $t['user_zeige5'] . $f2
					. "</td><td>" . $f3 . htmlspecialchars($o_row->o_browser)
					. $f4 . "</td></tr>\n" . "<tr><td style=\"vertical-align:top;\">" . $f1
					. $t['user_zeige22'] . $f2 . "</td><td>" . $f3
					. "<a href=\"" . $serverprotokoll . "://" . $o_row->o_vhost
					. "\" target=_blank>$serverprotokoll://" . $o_row->o_vhost
					. "</a>" . $f4 . "</td></tr>\n";
				
				if ($o_http_stuff) {
					$text .= "<tr><td style=\"vertical-align:top;\">" . $f1 . $t['user_zeige31'] . $f2
						. "</td><td>" . $f3;
					if (is_array($http_stuff)) {
						while (list($o_http_stuff_name, $o_http_stuff_inhalt) = each(
							$http_stuff)) {
							if ($o_http_stuff_inhalt) {
								$text .= "<b>"
									. htmlspecialchars($o_http_stuff_name)
									. ":</b>&nbsp;"
									. htmlspecialchars($o_http_stuff_inhalt)
									. "<br>\n";
							}
						}
					}
					echo $f4 . "</td></tr>\n";
				}
				
			} elseif ($zeigeip == 1 && is_array($ip_historie)) {
				while (list($datum, $ip_adr) = each($ip_historie)) {
					if (!$erstes) {
						$text .= "<tr><td style=\"vertical-align:top;\">" . $f1 . $t['sonst29'] . $f2
							. "</td><td>" . $f3;
						$erstes = TRUE;
					} else {
						$text .= "<tr><td></td><td>" . $f3;
					}
					$text .= $ip_adr . "&nbsp;("
						. str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum))
						. ")" . $f4 . "</td></tr>\n";
				}
			}
			
		}
		
		// Fenstername
		$fenster = str_replace("+", "", $uu_nick);
		$fenster = str_replace("-", "", $uu_nick);
		
		// Benutzermenue mit Aktionen
		if ($u_level != "G") {
			$mlnk[1] = "schreibe.php?id=$id&text=/ignore%20$uu_nick";
			$mlnk[2] = "schreibe.php?id=$id&text=/einlad%20$uu_nick";
			$text .= "<tr><td style=\"vertical-align:top;\"><b>" . $f1 . $t['user_zeige24'] . $f2
				. "</b></td><td>" . $f1;
			if (!$beichtstuhl) {
				$text .= "[<a href=\"$mlnk[1]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[1]';return(false);\">$t[user_zeige29]</a>]<br>\n";
			}
			$text .= "[<a href=\"$mlnk[2]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[2]';return(false);\">$t[user_zeige30]</a>]<br>\n";
			if ($communityfeatures) {
				$mlnk[8] = "mail.php?id=$id&aktion=neu2&neue_email[an_nick]=$uu_nick";
				$mlnk[9] = "schreibe.php?id=$id&text=/freunde%20$uu_nick";
				$text .= "[<a href=\"$mlnk[8]\" target=\"640_$fenster\" onclick=\"window.open('$mlnk[8]','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\">$t[user_zeige40]</a>]<br>\n"
					. "[<a href=\"$mlnk[9]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[9]';return(false);\">$t[user_zeige41]</a>]<br>\n";
			}
			
		}
		
		// Adminmenue
		if ($admin) {
			$mlnk[7] = "user.php?id=$id&zeigeip=1&aktion=zeig&user=$user&schau_raum=$schau_raum";
			$text .= "[<a href=\"$mlnk[7]\">" . $t['user_zeige34'] . "</a>]<br>\n";
		}
		
		// Adminmenue
		if ($admin && $rows == 1) {
			$mlnk[8] = "user.php?id=$id&kick_user_chat=1&aktion=zeig&user=$user&schau_raum=$schau_raum";
			$mlnk[3] = "user.php?id=$id&trace="
				. urlencode($host_name)
				. "&aktion=zeig&user=$user&schau_raum=$schau_raum";
			$mlnk[4] = "schreibe.php?id=$id&text=/gag%20$uu_nick";
			$mlnk[5] = "schreibe.php?id=$id&text=/kick%20$uu_nick";
			$mlnk[6] = "sperre.php?id=$id&aktion=neu&hname=$host_name&ipaddr=$o_row->o_ip&uname="
				. urlencode($o_row->o_name);
			$text .= "[<a href=\"$mlnk[3]\">" . $t['user_zeige25'] . "</a>]<br>\n"
				. "[<a href=\"$mlnk[4]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[4]';return(false);\">$t[user_zeige28]</a>]<br>\n"
				. "[<a href=\"$mlnk[5]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[5]';return(false);\">$t[user_zeige27]</a>]<br>\n"
				. "[<a href=\"$mlnk[6]\" target=\"640_$fenster\" onclick=\"window.open('$mlnk[6]','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\">$t[user_zeige26]</a>]<br>\n";
			$text .= "[<a href=\"$mlnk[8]\">" . $t['user_zeige47'] . "</a>]<br>\n";
		}
		
		// Adminmenue
		if ($admin && $communityfeatures) {
			$mlnk[10] = "blacklist.php?id=$id&aktion=neu&neuer_blacklist[u_nick]=$uu_nick";
			$text .= "[<a href=\"$mlnk[10]\" target=\"640_$fenster\" onclick=\"window.open('$mlnk[10]','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\">$t[user_zeige48]</a>]<br>\n";
			
		}
		
		// Tabellenende
		$text .= "$f2</td></tr></table>\n";
		
		// Box anzeigen
		show_box_title_content($box, $text);
		
		// Admin-Menü 3
		if ($admin) {
			$text = '';
			$box = $t['user_zeige12'];
			echo '<br>';
			
			$text .= "<form name=\"edit\" action=\"edit.php\" method=\"post\">\n" . $f1
				. str_replace("%uu_nick%", $uu_nick, $t['user_zeige13']) . $f2
				. "\n" . "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"f[u_id]\" value=\"$uu_id\">\n"
				. "<input type=\"hidden\" name=\"f[u_nick]\" value=\"$uu_nick\">\n"
				. "<input type=\"hidden\" name=\"zeige_loesch\" value=\"1\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"edit\">\n"
				. $f1
				. "<input type=\"submit\" name=\"ein\" value=\"Ändern!\">"
				. "<input type=\"submit\" name=\"eingabe\" value=\"Löschen!\"><br>";
			
			$query = "SELECT `u_chathomepage` FROM `user` WHERE `u_id` = '$uu_id'";
			$result = mysqli_query($mysqli_link, $query);
			$g = @mysqli_fetch_array($result);
			
			if ($g['u_chathomepage'] == "J") {
				$text .= "<input type=\"submit\" name=\"eingabe\" value=\"Homepage löschen!\">" . $f2;
			}
			if ((($u_level == "C" || $u_level == "A") && ($uu_level == "U" || $uu_level == "M" || $uu_level == "Z")) || ($u_level == "S")) {
				$text .= "<br><input type=\"submit\" name=\"eingabe\" value=\"$t[chat_msg110]\">";
			}
			$text .= $f2 . "</form>\n";
			

		}
		
		// Box anzeigen
		show_box_title_content($box, $text);
		
		// ggf Profil ausgeben, wenn ein externes Profil eingebunden werden soll (Benutzername: $uu_nick)
		
		mysqli_free_result($result);
		
	}
}

?>