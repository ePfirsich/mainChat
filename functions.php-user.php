<?php

// functions nur für user.php

require_once("functions.php-func-html_parse.php");
require_once("functions.php-func-raeume_auswahl.php");

function user_liste($larr, $anzahl) {
	// Gibt Userliste $larr als Tabelle aus
	
	global $t, $admin, $u_level, $adminfeatures, $o_js, $aktion, $u_id, $id, $show_geschlecht, $dbase, $mysqli_link;
	global $ft0, $ft1, $f1, $f2, $f3, $f4, $homep_ext_link;
	global $CELLPADDING, $punkte_grafik, $leveltext, $chat_grafik;
	
	// Array mit oder ohne Javascript ausgeben
	// Kopf Tabelle
	$box = $ft0 . $t['sonst18'] . $ft1;
	
	if (!isset($larr[0]['r_name']))
		$larr[0]['r_name'] = "";
	if (!isset($larr[0]['r_besitzer']))
		$larr[0]['r_besitzer'] = "";
	if (!isset($larr[0]['r_topic']))
		$larr[0]['r_topic'] = "";
	
	$r_name = $larr[0]['r_name'];
	$r_besitzer = $larr[0]['r_besitzer'];
	$r_topic = $larr[0]['r_topic'];
	$level = "user";
	$level2 = "user";
	if ($r_besitzer == $u_id && $adminfeatures)
		$level = "owner";
	if (($admin || $u_level == "A") && $adminfeatures)
		$level = "admin";
	if (($u_level == "C" || $u_level == "S") && $adminfeatures)
		$level2 = "admin";
	
	// Wartetext ausgeben
	if ($anzahl > 10)
		echo $f1 . "<b>$t[sonst23] $anzahl $t[sonst36]</b><br>\n" . $f2;
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
				$uid = $row[ui_userid];
				if ($row[ui_geschlecht] == "männlich")
					$geschl[$uid] = "M";
				else if ($row[ui_geschlecht] == "weiblich")
					$geschl[$uid] = "W";
				else $geschl[$uid] = "";
			}
			@mysqli_free_result($result);
		}
		
		// Leveltexte erzeugen
		unset($leveltxt);
		foreach ($leveltext as $key => $val) {
			$leveltxt[] = "leveltext[\"$key\"]=\"$val\"";
		}
		
		// Mit Javascript ausgeben, vollständiges Menü
		for ($k = 0; isset($larr[$k]) AND is_array($larr[$k])
			AND $v = $larr[$k]; $k++) {
			if ($level != "admin") {
				$v['hostname'] = "";
				$v['o_ip'] = "";
			} else {
				if (!isset($v['hostname']))
					$v['hostname'] = "";
				$v['hostname'] = htmlspecialchars($v['hostname']);
				if (!isset($v['o_ip']))
					$v['o_ip'] = "";
			}
			;
			$v['u_away'] = $v['u_away'] ? "y" : "";
			if (!isset($v['u_home_ok']))
				$v['u_home_ok'] = "";
			if (!isset($v['u_chathomepage']))
				$v['u_chathomepage'] = "";
			if (!isset($v['gruppe']))
				$v['gruppe'] = "";
			if ($show_geschlecht == true) {
				$geschlecht = $geschl[$v['u_id']];
				$jsarr[] = "'$v[u_id]','$v[u_chathomepage]','$v[u_nick]','$v[hostname]','$v[o_ip]','$v[u_away]','$v[u_level]','$v[gruppe]','$v[u_home_ok]','$geschlecht' ";
			} else $jsarr[] = "'$v[u_id]','$v[u_chathomepage]','$v[u_nick]','$v[hostname]','$v[o_ip]','$v[u_away]','$v[u_level]','$v[gruppe]','$v[u_home_ok]'";
		}
		
		echo "\n\n<script language=\"JavaScript\">\n"
			. "   var color = new Array('tabelle_zeile1','tabelle_zeile2');\n"
			. "   var fett  = new Array('$f1<b>','</b>$f2','$f3','$f4','$f1','$f2');\n"
			. "   var level = '$level';\n" . "   var level2 = '$level2';\n"
			. "   var padd  = '$CELLPADDING';\n"
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
		echo "<script language=\"JavaScript\" src=\"popup.js\"></script>\n";
	} else { // kein javascript verfügbar
	
		for ($k = 0; is_array($larr[$k]) AND $v = $larr[$k]; $k++) {
			if ( $k % 2 != 0 ) {
				$farbe_tabelle = 'class="tabelle_zeile1"';
			} else {
				$farbe_tabelle = 'class="tabelle_zeile2"';
			}
			
			if ($v['u_away']) {
				$user = "("
					. user($v['u_id'], $v, TRUE, FALSE, $trenner = "</TD><TD>")
					. ")";
			} else {
				$user = user($v['u_id'], $v, TRUE, FALSE,
					$trenner = "</TD><TD>");
			}
			;
			$trow .= "<tr>" . "<td $farbe_tabelle>" . $f1 . $user . $f2 . "</td></tr>";
		} // END-OF-FOR
		echo "<TABLE BORDER=\"0\" CELLPADDING=\"$CELLPADDING\" CELLSPACING=\"0\">$trow</TABLE>\n";
		
	}
	
}

function user_zeige($user, $admin, $schau_raum, $u_level, $zeigeip)
{
	// $user = ID des Users
	// Falls $admin wahr werden IP und Onlinedaten ausgegeben
	
	global $mysqli_link, $dbase, $level, $id, $http_host, $f1, $f2, $f3, $f4, $farbe_tabelle_koerper;
	global $user_farbe, $farbe_text, $ist_online_raum, $chat_max_eingabe, $t, $ft0, $ft1, $communityfeatures;
	global $chat_grafik, $whotext, $beichtstuhl, $erweitertefeatures, $msgpopup, $serverprotokoll;
	
	$eingabe_breite = 29;
	
	// User listen
	$query = "SELECT `user`.*,"
		. "FROM_Unixtime(UNIX_TIMESTAMP(u_login),'%d.%m.%Y %H:%i') AS `letzter_login`,"
		. "FROM_Unixtime(UNIX_TIMESTAMP(u_neu),'%d.%m.%Y %H:%i') AS `erster_login` "
		. "FROM `user` WHERE `u_id`=$user ";
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result AND mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_object($result);
		$uu_away = $row->u_away;
		$uu_nick = htmlspecialchars($row->u_nick);
		$uu_name = htmlspecialchars($row->u_name);
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
		
		// Kopf Tabelle "Private Nachricht"
		if (isset($onlinezeit) && $onlinezeit && $u_level != "G") {
			$box = $ft0
				. str_replace("%uu_nick%", $uu_nick, $t['user_zeige11']) . $ft1;
			
			echo "<table class=\"tabelle_kopf\">\n"
				. "<FORM NAME=\"form\" METHOD=POST target=\"schreibe\" ACTION=\"schreibe.php\" onSubmit=\"resetinput(); return false;\">"
				. "<TR><TD><a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n"
				. "<span style=\"font-size: smaller; color:$farbe_text;\"><b>$box</b></span>\n"
				. "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n"
				. "<TABLE CELLPADDING=5 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=\"$farbe_tabelle_koerper\">\n"
				. "<TR><TD>";
			
			// Eingabeformular für private Nachricht ausgeben
			
			echo $f1;
			
			if ($msgpopup) {
				echo '<iframe src="messages-popup.php?id=' . $id
					. '&http_host=' . $http_host . '&user=' . $user
					. '&user_nick=' . $uu_nick
					. '" width=100% height=200 marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0></iframe>';
					?>
					<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
					<?php
			}
			
			echo "<INPUT NAME=\"text2\" SIZE=\"" . $eingabe_breite
				. "\" maxlength=\"" . ($chat_max_eingabe - 50)
				. "\" VALUE=\"\" TYPE=\"TEXT\">"
				. "<INPUT NAME=\"text\" VALUE=\"\" TYPE=\"HIDDEN\">"
				. "<INPUT NAME=\"http_host\" VALUE=\"$http_host\" TYPE=\"HIDDEN\">"
				. "<INPUT NAME=\"id\" VALUE=\"$id\" TYPE=\"HIDDEN\">"
				. "<INPUT NAME=\"privat\" VALUE=\"$uu_nick\" TYPE=\"HIDDEN\">"
				. "<input type=\"SUBMIT\" VALUE=\"Go!\">" . $f2
				. "\n<script language=\"JavaScript\">\n\n"
				. "document.forms['form'].elements['text2'].focus();\n"
				. "\n</script>\n\n\n";
			
			// Fuß der Tabelle
			echo "</TD></TR></TABLE></TD></TR></FORM></TABLE>\n"
				. "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n";
		}
		
		// Kopf Tabelle Userinfo
		
		if (isset($onlinezeit) && $onlinezeit) {
			$box = $ft0 . str_replace("%user%", $uu_nick, $t['user_zeige20'])
				. $ft1;
		} else {
			$box = $ft0 . str_replace("%user%", $uu_nick, $t['user_zeige21'])
				. $ft1;
		}
		
		echo "<table class=\"tabelle_kopf\">\n"
			. "<TR><TD><a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n"
			. "<span style=\"font-size: smaller; color:$farbe_text;\"><b>$box</b></span>\n"
			. "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n"
			. "<TABLE CELLPADDING=5 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=\"$farbe_tabelle_koerper\">\n"
			. "<TR><TD>";
		
		// Ausgabe in Tabelle
		echo "<TABLE BORDER=0 CELLPADDING=0>";
		echo "<TR><TD style=\"vertical-align:top;\"><b>" . $f1 . $t['user_zeige18'] . $f2
			. "</b></TD><TD><b>" . user($user, $row, TRUE, FALSE);
		
		if ($uu_away != "") {
			echo $f1 . "<br></b>($uu_away)<b>" . $f2;
		}
		
		echo "</b></TD></TR>\n";
		
		if ($admin) {
			// Name
			if (strlen($uu_name) > 0) {
				echo "<TR><TD><b>" . $f1 . $t['user_zeige2'] . $f2
					. "</b></TD><TD><b>" . $f1 . "$uu_name" . $f2
					. "</b></TD></TR>\n";
			}
		}
		
		// Raum
		if (isset($o_row) && $o_row->r_name && $o_row->o_who == 0) {
			echo "<TR><TD><b>" . $f1 . $t['user_zeige23'] . $f2
				. "</b></TD><TD><b>" . $f1 . $o_row->r_name . "&nbsp;["
				. $whotext[$o_row->o_who] . "]" . $f2 . "</b></TD></TR>\n";
		} elseif (isset($o_row) && $o_row->o_who) {
			echo "<TR><TD>" . $f1 . "&nbsp;" . $f2 . "</TD>" . "<TD><b>" . $f1
				. "[" . $whotext[$o_row->o_who] . "]" . $f2
				. "</b></TD></TR>\n";
		}
		if (isset($onlinezeit) && $onlinezeit) {
			echo "<TR><TD>" . $f1 . $t['user_zeige33'] . $f2
				. "</TD><TD style=\"vertical-align:bottom;\">" . $f3
				. gmdate("H:i:s", $onlinezeit) . "&nbsp;" . $t['sonst27'] . $f4
				. "</TD></TR>\n";
		} else {
			echo "<TR><TD>" . $f1 . $t['user_zeige9'] . $f2
				. "</TD><TD style=\"vertical-align:top;\">" . $f1 . "$letzter_login" . $f2
				. "</TD></TR>\n";
		}
		
		if ($erster_login && $erster_login != "01.01.1970 01:00") {
			echo "<TR><TD>" . $f1 . $t['user_zeige32'] . $f2
				. "</TD><TD style=\"vertical-align:top;\">" . $f1 . "$erster_login" . $f2
				. "</TD></TR>\n";
		}
		
		// Punkte
		if ($communityfeatures && $uu_punkte_gesamt) {
			if ($row->u_punkte_datum_monat != date("n", time())) {
				$uu_punkte_monat = 0;
			}
			if ($row->u_punkte_datum_jahr != date("Y", time())) {
				$uu_punkte_jahr = 0;
			}
			echo "<TR><TD>" . $f1 . $t['user_zeige38'] . $f2
				. "</TD><TD style=\"vertical-align:top;\">" . $f3 . $uu_punkte_gesamt . "/"
				. $uu_punkte_jahr . "/" . $uu_punkte_monat . "&nbsp;"
				. str_replace("%jahr%", substr(strftime("%Y", time()), 2, 2),
					str_replace("%monat%",
						substr(strftime("%B", time()), 0, 3),
						$t['user_zeige39'])) . $f4 . "</TD></TR>\n";
		}
		
		if ($admin) {
			// Admin E-Mail
			if (strlen($uu_adminemail) > 0) {
				echo "<TR><TD>" . $f1 . $t['user_zeige3'] . $f2 . "</TD><TD>"
					. $f3
					. "<a href=\"MAILTO:$uu_adminemail\">$uu_adminemail</A>"
					. $f4 . "</TD></TR>\n";
			}
		}
		
		if ($communityfeatures) {
			
			// Nickname Sonderzeichen raus für target
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
			echo "<TR><TD>" . $f1 . $t['user_zeige6'] . $f2 . "</TD><TD>" . $f3
				. "<a href=\"$url\" target=\"640_$fenster\" onClick=\"neuesFenster2('$url'); return(false)\">$chat_grafik[mail]</A>";
			$f4 . "</TD></TR>\n";
		} elseif (strlen($uu_email) > 0) {
			echo "<TR><TD>" . $f1 . $t['user_zeige6'] . $f2 . "</TD><TD>" . $f3
				. "<a href=\"MAILTO:$uu_email\">$uu_email</A>" . $f4
				. "</TD></TR>\n";
		}
		
		if ($communityfeatures && $uu_chathomepage == "J") {
			$url = "home.php?ui_userid=$uu_id&id=" . $id;
			echo "<TR><TD>" . $f1 . $t['user_zeige7'] . $f2 . "</TD><TD>" . $f3
				. "<a href=\"$url\" target=\"640_$fenster\" onClick=\"neuesFenster2('$url'); return(false)\">$chat_grafik[home]</A>";
			$f4 . "</TD></TR>\n";
		} elseif (strlen($uu_url) > 0) {
			echo "<TR><TD>" . $f1 . $t['user_zeige7'] . $f2 . "</TD><TD>" . $f3
				. "<a href=\"$uu_url\" target=\"_blank\">$uu_url</A>" . $f4
				. "</TD></TR>\n";
		}
		
		echo "<TR><TD>" . $f1 . $t['user_zeige8'] . $f2 . "</TD><TD>" . $f1
			. "$level[$uu_level]" . $f2 . "</TD></TR>\n";
		
		echo "<TR><TD>" . $f1 . $t['user_zeige10'] . $f2 . "</TD>"
			. "<TD BGCOLOR=\"#" . $uu_farbe . "\">&nbsp;</TD></TR>\n";
		
		if ($uu_kommentar && $admin) {
			echo "<TR><TD style=\"vertical-align:top;\">" . $f1 . $t['user_zeige49'] . $f2
				. "</TD><TD>" . $f3;
			echo htmlspecialchars($uu_kommentar) . "<br>\n";
			echo $f4 . "</TD></TR>\n";
		}
		
		if ($admin) {
			if (is_array($uu_profil_historie)) {
				echo "<TR><TD style=\"vertical-align:top;\">";
				
				while (list($datum, $nick) = each($uu_profil_historie)) {
					if (!isset($erstes)) {
						echo "<TR><TD style=\"vertical-align:top;\">" . $f1 . $t['sonst44'] . $f2
							. "</TD><TD>" . $f3;
						$erstes = TRUE;
					} else {
						echo "<TR><TD></TD><TD>" . $f3;
					}
					echo $nick . "&nbsp;("
						. str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum))
						. ")" . $f4 . "</TD></TR>\n";
				}
				echo "</TR>";
			}
			
			// IPs ausgeben
			if (isset($o_row) && $o_row->o_ip) {
				echo "<TR><TD style=\"vertical-align:top;\">" . $f1 . $t['user_zeige4'] . $f2
					. "</TD><TD>" . $f3 . $host_name . $f4 . "</TD></TR>\n"
					. "<TR><TD style=\"vertical-align:top;\">" . $f1 . "IP" . $f2 . "</TD><TD>"
					. $f3 . $o_row->o_ip . " " . $t['sonst28'] . $f4
					. "</TD></TR>\n";
				
				if ($zeigeip == 1 && is_array($ip_historie)) {
					while (list($datum, $ip_adr) = each($ip_historie)) {
						echo "<TR><TD></TD><TD>" . $f3 . $ip_adr . "&nbsp;("
							. str_replace(" ", "&nbsp;",
								date("d.m.y H:i", $datum)) . ")" . $f4
							. "</TD></TR>\n";
					}
				}
				
				echo "<TR><TD style=\"vertical-align:top;\">" . $f1 . $t['user_zeige5'] . $f2
					. "</TD><TD>" . $f3 . htmlspecialchars($o_row->o_browser)
					. $f4 . "</TD></TR>\n" . "<TR><TD style=\"vertical-align:top;\">" . $f1
					. $t['user_zeige22'] . $f2 . "</TD><TD>" . $f3
					. "<a href=\"" . $serverprotokoll . "://" . $o_row->o_vhost
					. "\" target=_blank>$serverprotokoll://" . $o_row->o_vhost
					. "</a>" . $f4 . "</TD></TR>\n";
				
				if ($o_http_stuff) {
					echo "<TR><TD style=\"vertical-align:top;\">" . $f1 . $t['user_zeige31'] . $f2
						. "</TD><TD>" . $f3;
					if (is_array($http_stuff)) {
						while (list($o_http_stuff_name, $o_http_stuff_inhalt) = each(
							$http_stuff)) {
							if ($o_http_stuff_inhalt) {
								echo "<b>"
									. htmlspecialchars($o_http_stuff_name)
									. ":</b>&nbsp;"
									. htmlspecialchars($o_http_stuff_inhalt)
									. "<br>\n";
							}
						}
					}
					echo $f4 . "</TD></TR>\n";
				}
				
			} elseif ($zeigeip == 1 && is_array($ip_historie)) {
				while (list($datum, $ip_adr) = each($ip_historie)) {
					if (!$erstes) {
						echo "<TR><TD style=\"vertical-align:top;\">" . $f1 . $t['sonst29'] . $f2
							. "</TD><TD>" . $f3;
						$erstes = TRUE;
					} else {
						echo "<TR><TD></TD><TD>" . $f3;
					}
					echo $ip_adr . "&nbsp;("
						. str_replace(" ", "&nbsp;", date("d.m.y H:i", $datum))
						. ")" . $f4 . "</TD></TR>\n";
				}
			}
			
		}
		
		// Fenstername
		$fenster = str_replace("+", "", $uu_nick);
		$fenster = str_replace("-", "", $uu_nick);
		
		// Usermenue mit Aktionen
		if ($u_level != "G") {
			$mlnk[1] = "schreibe.php?http_host=$http_host&id=$id&text=/ignore%20$uu_nick";
			$mlnk[2] = "schreibe.php?http_host=$http_host&id=$id&text=/einlad%20$uu_nick";
			echo "<TR><TD style=\"vertical-align:top;\"><b>" . $f1 . $t['user_zeige24'] . $f2
				. "</b></TD><TD>" . $f1;
			if (!$beichtstuhl)
				echo "[<a href=\"$mlnk[1]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[1]';return(false);\">$t[user_zeige29]</A>]<br>\n";
			echo "[<a href=\"$mlnk[2]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[2]';return(false);\">$t[user_zeige30]</A>]<br>\n";
			if ($communityfeatures) {
				$mlnk[8] = "mail.php?http_host=$http_host&id=$id&aktion=neu2&neue_email[an_nick]=$uu_nick";
				$mlnk[9] = "schreibe.php?http_host=$http_host&id=$id&text=/freunde%20$uu_nick";
				echo "[<a href=\"$mlnk[8]\" target=\"640_$fenster\" onclick=\"window.open('$mlnk[8]','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\">$t[user_zeige40]</A>]<br>\n"
					. "[<a href=\"$mlnk[9]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[9]';return(false);\">$t[user_zeige41]</A>]<br>\n";
			}
			
		}
		
		// Adminmenue
		if ($admin) {
			$mlnk[7] = "user.php?http_host=$http_host&id=$id&zeigeip=1&aktion=zeig&user=$user&schau_raum=$schau_raum";
			echo "[<a href=\"$mlnk[7]\">" . $t['user_zeige34'] . "</A>]<br>\n";
		}
		
		// Adminmenue
		if ($admin && $rows == 1) {
			$mlnk[8] = "user.php?http_host=$http_host&id=$id&kick_user_chat=1&aktion=zeig&user=$user&schau_raum=$schau_raum";
			$mlnk[3] = "user.php?http_host=$http_host&id=$id&trace="
				. urlencode($host_name)
				. "&aktion=zeig&user=$user&schau_raum=$schau_raum";
			$mlnk[4] = "schreibe.php?http_host=$http_host&id=$id&text=/gag%20$uu_nick";
			$mlnk[5] = "schreibe.php?http_host=$http_host&id=$id&text=/kick%20$uu_nick";
			$mlnk[6] = "sperre.php?http_host=$http_host&id=$id&aktion=neu&hname=$host_name&ipaddr=$o_row->o_ip&uname="
				. urlencode($o_row->o_name);
			echo "[<a href=\"$mlnk[3]\">" . $t['user_zeige25'] . "</A>]<br>\n"
				. "[<a href=\"$mlnk[4]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[4]';return(false);\">$t[user_zeige28]</A>]<br>\n"
				. "[<a href=\"$mlnk[5]\" target=\"schreibe\" onclick=\"opener.parent.frames['schreibe'].location='$mlnk[5]';return(false);\">$t[user_zeige27]</A>]<br>\n"
				. "[<a href=\"$mlnk[6]\" target=\"640_$fenster\" onclick=\"window.open('$mlnk[6]','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\">$t[user_zeige26]</A>]<br>\n";
			echo "[<a href=\"$mlnk[8]\">" . $t['user_zeige47'] . "</A>]<br>\n";
		}
		
		// Adminmenue
		if ($admin && $communityfeatures) {
			$mlnk[10] = "blacklist.php?http_host=$http_host&id=$id&aktion=neu&neuer_blacklist[u_nick]=$uu_nick";
			echo "[<a href=\"$mlnk[10]\" target=\"640_$fenster\" onclick=\"window.open('$mlnk[10]','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\">$t[user_zeige48]</A>]<br>\n";
			
		}
		
		// Tabellenende
		echo "$f2</TD></TR></TABLE>\n";
		
		// Fuß der Tabelle
		echo "</TD></TR></TABLE></TD></TR></TABLE>\n";
		
		// Admin-Menü 3
		if ($admin) {
			$box = $ft0 . $t['user_zeige12'] . $ft1;
			
			?>
			<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
			<?php
			echo "<table class=\"tabelle_kopf\">\n";
			echo "<TR><TD>";
			echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
			echo "<span style=\"font-size: smaller; color:$farbe_text;\"><b>$box</b></span>\n";
			?>
			<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
			<?php
			echo "<TABLE CELLPADDING=5 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=\"$farbe_tabelle_koerper\">\n";
			echo "<TR><TD>";
			
			echo "<FORM NAME=\"edit\" ACTION=\"edit.php\" METHOD=POST>\n" . $f1
				. str_replace("%uu_nick%", $uu_nick, $t['user_zeige13']) . $f2
				. "\n" . "<input type=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
				. "<input type=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
				. "<input type=\"HIDDEN\" NAME=\"f[u_id]\" VALUE=\"$uu_id\">\n"
				. "<input type=\"HIDDEN\" NAME=\"f[u_name]\" VALUE=\"$uu_name\">\n"
				. "<input type=\"HIDDEN\" NAME=\"f[u_nick]\" VALUE=\"$uu_nick\">\n"
				. "<input type=\"HIDDEN\" NAME=\"zeige_loesch\" VALUE=\"1\">\n"
				. "<input type=\"HIDDEN\" NAME=\"aktion\" VALUE=\"edit\">\n"
				. $f1
				. "<input type=\"SUBMIT\" NAME=\"ein\" VALUE=\"Ändern!\">"
				. "<input type=\"SUBMIT\" NAME=\"eingabe\" VALUE=\"Löschen!\"><br>";
			
			$query = "SELECT `u_chathomepage` FROM `user` WHERE `u_id` = '$uu_id'";
			$result = mysqli_query($mysqli_link, $query);
			$g = @mysqli_fetch_array($result);
			
			if ($g['u_chathomepage'] == "J")
				echo "<input type=\"SUBMIT\" NAME=\"eingabe\" VALUE=\"Homepage löschen!\">"
					. $f2;
			if ((($u_level == "C" || $u_level == "A")
				&& ($uu_level == "U" || $uu_level == "M" || $uu_level == "Z"))
				|| ($u_level == "S"))
				echo "<br><input type=\"SUBMIT\" NAME=\"eingabe\" VALUE=\"$t[chat_msg110]\">";
			echo "</FORM>\n";
		}
		
		// Fuß der Tabelle
		echo "</TD></TR></TABLE></TD></TR></TABLE>\n";
		
		// ggf Profil ausgeben, wenn ein externes Profil eingebunden werden soll (Nickname: $uu_nick)
		
		mysqli_free_result($result);
		
	}
}

?>