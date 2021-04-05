<?php
require_once("functions-registerglobals.php");

if (!isset($_SERVER["HTTP_REFERER"])) {
	$_SERVER["HTTP_REFERER"] = "";
}

if ((!isset($http_host) && !isset($login)) || ($frame == 1)) {
	// Falls diese Seite ohne Parameter aufgerufen wird, wird das oberste Frameset ausgegeben
	
	// Funktionen und Config laden, Host bestimmen
	require_once("functions-init.php");
	
	$title = $body_titel;
	zeige_header_anfang($title, $farbe_background, $grafik_background, $farbe_chat_link, $farbe_chat_vlink, $zusatztext_kopf);
	
	// Backdoorschutz über den HTTP_REFERER
	if ( (isset($chat_referer) && $chat_referer != "") && !preg_match("/" . $chat_referer . "/", $_SERVER["HTTP_REFERER"]) && $aktion != "neu") {
		$meta_refresh = '<meta http-equiv="refresh" content="5; URL=' . $chat_login_url . '">';
		zeige_header_ende($meta_refresh);
		?>
		<body>
		<?php
		zeige_kopf();
		$chat_login_url = "<a href=\"$chat_login_url\">$chat_login_url</a>";
		echo str_replace("%webseite%", $chat_login_url, $t['login26']);
		zeige_fuss();
		?>
		</body>
		</html>
		<?php
		exit();
	}
	
	// Soll Chatserver dynamisch gewählt werden?
	$chatserver = "";
	if ($chatserver_dynamisch) {
		
		// Datenbankconnect
		$mysqli_link = mysqli_connect('p:'.$chatserver_mysqlhost, $mysqluser, $mysqlpass, $dbase);
		if ($mysqli_link) {
			
			mysqli_set_charset($mysqli_link, "utf8mb4");
			
			// Alle Hosts bestimmen, die in den letzten 150sek erreichbar waren und die den Typ "apache" haben...
			mysqli_select_db($mysqli_link, "chat_info");
			$result = mysqli_query($mysqli_link, 
				"select h_host from host where (NOW()-h_time)<150 and h_type='apache' order by h_av1");
			$rows = mysqli_num_rows($result);
			
			if ($rows > 0) {
				if ($rows > 1)
					$max = 1;
				if ($rows > 4)
					$max = 2;
				if ($rows > 6)
					$max = 3;
				if ($rows > 9)
					$max = 4;
				if ($max > 0) {
					$chatserver = $serverprotokoll . "://"
						. mysqli_result($result, mt_rand(0, $max), "h_host")
						. "/";
				} else {
					$chatserver = $serverprotokoll . "://"
						. mysqli_result($result, 0, "h_host") . "/";
				}
			} else {
				$chatserver = "";
			}
			
			mysqli_free_result($result);
			mysqli_close($mysqli_link);
			
		} else {
			zeige_header_ende();
			?>
			<body>
			<P>Der mainChat ist leider aus technischen Gründen nicht erreichbar.<br>
			Bitte versuchen Sie es später noch einmal.</P>";
			<?php
			exit();
		}
	}
	
	// Optional Frame links
	if (strlen($frame_links) > 5) {
		$opt = $frame_links_size . ",";
	} else {
		$opt = "";
	}
	?>
	<frameset cols="<?php echo $opt; ?>100%,*" border="0" frameborder="0">
	<?php
		if ($opt) {
			echo "<frame src=\"" . $frame_links . "\" name=\"leftframe\" noresize>\n";
		}
	
	// Login durch das äußere Frameset durchschleifen
	if ($frame == 1 && $aktion == "login") {
		$url = $chatserver . "index.php?http_host=" . $http_host
			. "&javascript=" . $javascript . "&login=" . urlencode($login)
			. "&passwort=" . urlencode($passwort) . "&eintritt=" . $eintritt
			. "&aktion=" . $aktion . "&los=" . $los;
		if (is_array($f))
			foreach ($f as $key => $val) {
				$url .= "&f[" . $key . "]=" . urlencode($val);
			}
		
		echo "<frame src=\"" . $url
			. "\" scrolling=\"AUTO\" name=\"topframe\" noresize>\n</frameset><noframes>\n";
		
		// E-Mail bestätigung durch das äußere Frameset durchschleifen
	} elseif ($frame == 1) {
		echo "<frame src=\"" . $chatserver
			. "index.php?http_host=$http_host&aktion=$aktion&email=$email&hash=$hash\" scrolling=\"AUTO\" name=\"topframe\" noresize>\n"
			. "</frameset><noframes>\n";
		
		// Normales Frameset mit Loginmaske
	} else {
		if (isset($_SERVER["HTTP_REFERER"])) {
			$ref = $_SERVER["HTTP_REFERER"];
		} else {
			$ref = "";
		}
		
		echo "<frame src=\"" . $chatserver
			. "index.php?http_host=$http_host&refereroriginal=" . $ref
			. "\" scrolling=\"AUTO\" name=\"topframe\" noresize>\n"
			. "</frameset><noframes>\n";
	}
	
	if ($noframes) {
		echo $noframes . "\n";
	} else {
		echo "<P><DIV ALIGN=\"CENTER\"><a href=\"index.php\">weiter</A></DIV>\n";
	}
	?>
	</noframes>
	<?php
} else {
	// ELSE-FALL
	
	// Seite wurde mit Parametern innerhalb des Framesets aufgerufen: Eingangsseite ausgeben
	
	// Funktionen laden
	require("functions.php");
	
	if($body_titel != null && $body_titel != '') {
		$body_titel = $body_titel;
	} else {
		$body_titel = 'mainChat';
	}
	$title = $body_titel;
	zeige_header_anfang($title, $farbe_background, $grafik_background, $farbe_link, $farbe_vlink, $zusatztext_kopf);

	// Backdoorschutz über den HTTP_REFERER - wir prüfen ob bei gesetztem HTTP_HOST ob die index.php in eigenem Frameset läuft sonst => Fehler
	if (isset($chat_referer) && $chat_referer != "") {
		$tmp = parse_url($serverprotokoll . "://" . $_SERVER["HTTP_HOST"]);
		$chat_referer2 = $tmp['scheme'] . "://" . $tmp['host'];
		
		if ((!preg_match("#" . $chat_referer2 . "#", $_SERVER["HTTP_REFERER"]))
			&& (!preg_match("#" . $chat_referer . "#", $refereroriginal))
			&& $aktion != "neu") {
			
			$meta_refresh = '<meta http-equiv="refresh" content="5; URL=' . $chat_login_url . '">';
			
			zeige_header_ende($meta_refresh);
			?>
			<body>
			<?php
			zeige_kopf();
			$chat_login_url = "<a href=\"$chat_login_url\">$chat_login_url</a>";
			echo str_replace("%webseite%", $chat_login_url, $t['login26']);
			zeige_fuss();
			exit();
		}
	}
	
	// Willkommen definieren
	if (isset($_SERVER["PHP_AUTH_USER"])) {
		$willkommen = str_replace("%PHP_AUTH_USER%", $_SERVER["PHP_AUTH_USER"],
			$t['willkommen1']);
	} else {
		$willkommen = $t['willkommen2'];
	}
	
	// Liste offener Räume definieren (optional)
	if ($raum_auswahl && (!isset($beichtstuhl) || !$beichtstuhl)) {
		// Falls eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
		if (strlen($eintrittsraum) == 0) {
			$eintrittsraum = $lobby;
		}
		
		// Raumauswahlliste erstellen
		$query = "SELECT r_name,r_id FROM raum "
			. "WHERE (r_status1='O' OR r_status1='E' OR r_status1 LIKE BINARY 'm') AND r_status2='P' "
			. "ORDER BY r_name";
		
		$result = @mysqli_query($mysqli_link, $query);
		if ($result) {
			$rows = mysqli_num_rows($result);
		} else {
			echo "<P><b>Datenbankfehler:</b> " . mysqli_error($mysqli_link) . ", "
				. mysqli_errno($mysqli_link) . "</P>";
			die();
		}
		if ($communityfeatures && $forumfeatures) {
			$raeume = "<td><b>" . $t['login22'] . "</b><br>";
		} else {
			$raeume = "<td><b>" . $t['login12'] . "</b><br>";
		}
		$raeume .= $f1 . "<SELECT name=\"eintritt\">";
		
		if ($communityfeatures && $forumfeatures)
			$raeume = $raeume
				. "<OPTION value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
		if ($rows > 0) {
			$i = 0;
			while ($i < $rows) {
				$r_id = mysqli_result($result, $i, "r_id");
				$r_name = mysqli_result($result, $i, "r_name");
				if ((!isset($eintritt) AND $r_name == $eintrittsraum)
					|| (isset($eintritt) AND $r_id == $eintritt)) {
					$raeume = $raeume
						. "<OPTION SELECTED value=\"$r_id\">$r_name\n";
				} else {
					$raeume = $raeume . "<OPTION value=\"$r_id\">$r_name\n";
				}
				$i++;
			}
		}
		if ($communityfeatures && $forumfeatures)
			$raeume = $raeume
				. "<OPTION value=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
		$raeume = $raeume . "</SELECT>" . $f2 . "</td>\n";
		mysqli_free_result($result);
	} else {
		if (strlen($eintrittsraum) == 0) {
			$eintrittsraum = $lobby;
		}
		$lobby_id = RaumNameToRaumID($eintrittsraum);
		$raeume = "<td><input type=hidden name=\"eintritt\" value=$lobby_id></td>\n";
	}
	
	$eingabe_breite = 20;
	
	// Logintext definieren
	$logintext = "<table BORDER=0 CELLSPACING=0 WIDTH=100%><tr><td><b>"	. $t['login8'] . "</b><br>" . $f1 . "<input type=\"TEXT\" name=\"login\" value=\"";
	if (isset($login)) {
		$logintext .= $login;
	}
	$logintext .= "\" SIZE=$eingabe_breite>" . $f2 . "</td>\n" . "<td><b>"
		. $t['login9'] . "</b><br>" . $f1
		. "<input type=\"PASSWORD\" name=\"passwort\" SIZE=$eingabe_breite>"
		. $f2 . "</td>\n" . $raeume . "<td><br>" . $f1
		. "<b><input type=\"submit\" name=\"los\" value=\"" . $t['login10']
		. "\"></b>\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"login\">" . $f2
		. "</td>\n" . "</tr></table>\n" . $t['login3'];
	
	// SSL?
	if (($ssl_login) || (isset($SSLRedirect) && $SSLRedirect == "1")) {
		$chat_file = "https://" . $http_host . $chat_url;
	}
	
	// IP bestimmen und prüfen. Ist Login erlaubt?
	$abweisen = false;
	$warnung = false;
	$ip_adr = $_SERVER["REMOTE_ADDR"];
	$ip_name = @gethostbyaddr($ip_adr);
	
	// Sperrt den Chat, wenn in der Sperre Domain "-GLOBAL-" ist
	$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GLOBAL-'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) > 0) {
		$abweisen = true;
	}
	mysqli_free_result($result);
	
	// Sperrt den Chat für bestimmte Browser komplett
	if (($_SERVER["HTTP_USER_AGENT"] == "Spamy v3.0 with 32 Threads")
		|| ($_SERVER["HTTP_USER_AGENT"]
			== "Powered by Spamy The Spambot v5.2.1")) {
		$abweisen = true;
	}
	
	// Gastsperre aktiv? Wird beim Login und beim Nutzungsbestimmungen Login ausgewertet
	$temp_gast_sperre = false;
	// Wenn die dbase = "mainchat" und in Sperre = "-GAST-"
	// dann Gastlogin gesperrt
	$query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GAST-'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) > 0) {
		$temp_gast_sperre = true;
	}
	mysqli_free_result($result);
	
	$query = "SELECT * FROM ip_sperre "
		. "WHERE (SUBSTRING_INDEX(is_ip,'.',is_ip_byte) "
		. " LIKE SUBSTRING_INDEX('" . mysqli_real_escape_string($mysqli_link, $ip_adr) . "','.',is_ip_byte) AND is_ip IS NOT NULL) "
		. "OR (is_domain LIKE RIGHT('" . mysqli_real_escape_string($mysqli_link, $ip_name) . "',LENGTH(is_domain)) AND LENGTH(is_domain)>0)";
	$result = mysqli_query($mysqli_link, $query);
	$rows = mysqli_num_rows($result);
	
	if ($rows > 0) {
		while ($row = mysqli_fetch_object($result)) {
			if ($row->is_warn == "ja") {
				// Warnung ausgeben
				$warnung = true;
				$infotext = $row->is_infotext;
			} else {
				// IP ist gesperrt
				$abweisen = true;
			}
		}
	}
	mysqli_free_result($result);

	// HTTP_X_FORWARDED_FOR IP bestimmen und prüfen. Ist Login erlaubt?
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		$ip_adr = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else {
		$ip_adr = "";
	}
	
	if (!$abweisen && $ip_adr && $ip_adr != $_SERVER["REMOTE_ADDR"]) {
		$ip_name = @gethostbyaddr($ip_adr);
		$query = "SELECT * FROM ip_sperre "
			. "WHERE (SUBSTRING_INDEX(is_ip,'.',is_ip_byte) "
			. " LIKE SUBSTRING_INDEX('" . mysqli_real_escape_string($mysqli_link, $ip_adr) . "','.',is_ip_byte) AND is_ip IS NOT NULL) "
			. "OR (is_domain LIKE RIGHT('" . mysqli_real_escape_string($mysqli_link, $ip_name) . "',LENGTH(is_domain)) AND LENGTH(is_domain)>0)";
		$result = mysqli_query($mysqli_link, $query);
		$rows = mysqli_num_rows($result);
		
		if ($rows > 0) {
			while ($row = mysqli_fetch_object($result)) {
				if ($row->is_warn == "ja") {
					// Warnung ausgeben
					$warnung = true;
					$infotext = $row->is_infotext;
				} else {
					// IP ist gesperrt
					$abweisen = true;
				}
			}
		}
		mysqli_free_result($result);
	}
	
	// zweite Prüfung, gibts was, was mit "*" in der mitte schafft? für p3cea9*.t-online.de
	$query = "SELECT * FROM ip_sperre WHERE is_domain like '_%*%_'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_object($result)) {
			$part = explode("*", $row->is_domain, 2);
			if ((strlen($part[0]) > 0) && (strlen($part[1]) > 0)) {
				if (substr($ip_name, 0, strlen($part[0])) == $part[0]
					&& substr($ip_name, strlen($ip_name) - strlen($part[1]),
						strlen($part[1])) == $part[1]) {
					
					// IP stimmt überein
					if ($row->is_warn == "ja") {
						// Warnung ausgeben
						$warnung = true;
						$infotext = $row->is_infotext;
					} else {
						// IP ist gesperrt
						$abweisen = true;
					}
				}
			}
		}
	}
	mysqli_free_result($result);
	
	// Wenn $abweisen=true, dann ist Login ist für diesen User gesperrt
	// Es sei denn wechsel Forum -> Chat, dann "Relogin", und wechsel trotz IP Sperre in Chat möglich
	if ($abweisen && $aktion != "relogin" && strlen($login) > 0) {
		// test: ist user=admin -> dann nicht abweisen...
		$query = "SELECT `u_nick`, `u_level` FROM `user` WHERE `u_nick`='"
			. mysqli_real_escape_string($mysqli_link, coreCheckName($login, $check_name))
			. "' AND (u_level in ('S','C'))";
		$r = mysqli_query($mysqli_link, $query);
		$rw = mysqli_num_rows($r);
		
		if ($rw == 1 && (strlen($aktion) > 0)) {
			$abweisen = false;
		}
		mysqli_free_result($r);
		
		// Prüfung nun auf Admin beendet
		// Nun Prüfung ob genug Punkte
		$durchgangwegenpunkte = 0;
		
		if ($communityfeatures && $loginwhileipsperre <> 0) {
			// Test auf Punkte 
			$query = "select u_id, u_nick,u_level,u_punkte_gesamt from user "
				. "where (u_nick='" . mysqli_real_escape_string($mysqli_link, coreCheckName($login, $check_name))
				. "' OR u_name='" . mysqli_real_escape_string($mysqli_link, "$login, $check_name") . "') "
				. "AND (u_level in ('A','C','G','M','S','U')) ";
			"AND u_passwort = encrypt('" . mysqli_real_escape_string($mysqli_link, $passwort) . "',u_passwort)";
			// Nutzt die MYSQL -> Unix crypt um DES, SHA256, etc. automatisch zu erkennen
			// Durchleitung wg. Punkten im Fall der MD5() verschlüsselung wird nicht gehen
			$r = mysqli_query($mysqli_link, $query);
			$rw = mysqli_num_rows($r);
			
			if ($rw == 1 && strlen($aktion) > 0) {
				$row = mysqli_fetch_object($r);
				if ($row->u_punkte_gesamt >= $loginwhileipsperre) {
					// genügend Punkte
					// Login zulassen
					$durchgangwegenpunkte = 1;
					$abweisen = false;
					$t_u_id = $row->u_id;
					$t_u_nick = $row->u_nick;
					$infotext = str_replace("%punkte%", $row->u_punkte_gesamt,
						$t['ipsperre2']);
				}
			}
			mysqli_free_result($r);
		}
		
		if ($durchgangwegenpunkte == 1) {
			// Wenn User wegen Punkte durch IP Sperre kommen, dann Meldung an alle Admins
			if ($eintritt == 'forum') {
				$raumname = " (" . $whotext[2] . ")";
			} else {
				$query2 = "SELECT r_name from raum where r_id=" . intval($eintritt);
				$result2 = mysqli_query($mysqli_link, $query2);
				if ($result2 AND mysqli_num_rows($result2) > 0) {
					$raumname = " (" . mysqli_result($result2, 0, 0) . ") ";
				} else {
					$raumname = "";
				}
				mysqli_free_result($result2);
			}
			
			$query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
			$result2 = mysqli_query($mysqli_link, $query2);
			if ($result2 AND mysqli_num_rows($result2) > 0) {
				$txt = str_replace("%ip_adr%", $ip_adr, $t['ipsperre1']);
				$txt = str_replace("%ip_name%", $ip_name, $txt);
				$txt = str_replace("%is_infotext%", $infotext, $txt);
				while ($row2 = mysqli_fetch_object($result2)) {
					$ur1 = "user.php?http_host=<HTTP_HOST>&id=<ID>&aktion=zeig&user=$t_u_id";
					$ah1 = "<a href=\"$ur1\" target=\"$t_u_nick\" onclick=\"neuesFenster('$ur1','"
						. $t_u_nick . "'); return(false);\">";
					$ah2 = "</A>";
					system_msg("", 0, $row2->o_user, $system_farbe,
						str_replace("%u_nick%",
							$ah1 . $t_u_nick . $ah2 . $raumname, $txt));
				}
				unset($infotext);
				unset($t_u_id);
				unset($u_nick);
			}
			mysqli_free_result($result2);
		}
	}
	
	if ($abweisen && (strlen($aktion) > 0) && $aktion <> "relogin") {
		$aktion = "abweisen";
		unset($logintext);
	}
	
	// Login ist für alle User gesperrt
	if (($chat_offline_kunde) || ((isset($chat_offline)) && (strlen($chat_offline) > 0))) {
		$aktion = "gesperrt";
	}
	
	// Ausloggen, falls eingeloggt
	if ($aktion == "logoff") {
		// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum
		id_lese($id);
		
		/*
		// Header ausgeben
		if ( !isset($chat_logout_url) ) {
			zeige_header_ende();
			?>
			<body>
			<?php
		}
		*/
		
		// Logoff falls noch online
		if (strlen($u_id) > 0) {
			verlasse_chat($u_id, $u_nick, $o_raum);
			sleep(2);
			logout($o_id, $u_id, "index->logout");
		}
		
		if (isset($chat_logout_url) && ($chat_logout_url <> "")) {
			$meta_refresh = '<meta http-equiv="refresh" content="0; URL=' . $chat_logout_url . '">';
			
			zeige_header_ende($meta_refresh);
			?>
			<body>
			<?php
			zeige_kopf();
			$chat_login_url = "<a href=\"$chat_logout_url\">$chat_logout_url</a>";
			echo ("Sie werden nun auf $chat_logout_url weitergeleitet.");
			// echo "chat_refer = $chat_referer<br>HTTP_REFERER=$HTTP_SERVER_VARS[HTTP_REFERER] <br>";
			zeige_fuss();
			exit();
		}
		
	}
	
	// Falls in Loginmaske/Nutzungsbestimmungen auf Abbruch geklickt wurde
	if ($los == $t['login18'] && $aktion == "login")
		$aktion = "";
	
	// Titeltext der Loginbox setzen, Link auf Registrierung optional ausgeben
	
	if ($neuregistrierung_deaktivieren) {
		$login_titel = $ft0 . $t['default1'] . $ft1;
		if ($aktion == "neu")
			$aktion = "";
		if ($aktion == "neu2")
			$aktion = "";
		if ($aktion == "mailcheckm")
			$aktion = "";
	} else if ($t['login14']) {
		$login_titel = $ft0 . $t['default1']
			. " [<a href=\"$chat_file?http_host=$http_host&aktion=neu\">"
			. $ft0 . $t['login14'] . $ft1 . "</A>]" . $ft1;
	} else {
		$login_titel = $ft0 . $t['default1'] . $ft1;
	}
	
	if (!isset($chatserver) || $chatserver == "") {
		$chatserver = $serverprotokoll . "://" . $_SERVER['HTTP_HOST'] . "/";
	}
	
	if ($aktion == "neu" && $pruefe_email == "1" && isset($f['u_adminemail']) && $hash != md5($f['u_adminemail'] . "+" . date("Y-m-d"))) {
			zeige_header_ende();
			?>
			<body>
			<?php
		zeige_kopf();
		echo "<P><b>Fehler:</b> Die URL ist nicht korrekt! Bitte melden Sie sich "
			. "<a href=\"" . $chatserver
			. "index.php?http_host=$http_host\">hier</A> neu an.</P>";
		
		zeige_fuss();
		exit;
	}
	
	if ($aktion == "neu" && $pruefe_email == "1" && $los != $t['neu22'])
		$aktion = "mailcheck";
	if ($aktion == "neu2") {
		$aktion = "mailcheckm";
	}
	
	if (isset($f['u_adminemail'])) {
		$ro = "readonly";
	}
	
	if ($aktion == "mailcheck" && isset($email) && isset($hash)) {
		$email = mysqli_real_escape_string($mysqli_link, $email);
		$query = "SELECT * FROM mail_check WHERE email = '$email'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$a = mysqli_fetch_array($result);
			$hash2 = md5($a['email'] . "+" . $a['datum']);
			if ($hash == $hash2) {
				
				$aktion = "neu";
				$ro = "readonly";
				$f['u_adminemail'] = $email;
			} else {
				// Header ausgeben
				zeige_header_ende();
				?>
				<body>
				<?php
				zeige_kopf();
				echo "<P><b>Fehler:</b> Die URL ist nicht korrekt! Bitte melden Sie sich "
					. "<a href=\"" . mysqli_real_escape_string($mysqli_link, $chatserver)
					. "index.php?http_host=" . mysqli_real_escape_string($mysqli_link, $http_host) . "\">hier</A> neu an.</P>";
				$query = "DELETE FROM mail_check WHERE email = '$email'";
				mysqli_query($mysqli_link, $query);
				zeige_fuss();
				exit;
			}
		} else {
			// Header ausgeben
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			echo "<P><b>Fehler:</b> Diese Mail wurde bereits für eine Anmeldung benutzt! Bitte melden Sie sich "
				. "<a href=\"" . $chatserver
				. "index.php?http_host=$http_host\">hier</A> neu an.</P>";
			zeige_fuss();
			exit;
		}
	}
	
	switch ($aktion) {
		case "passwort_neu":
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			echo $willkommen;
			
			unset($richtig);
			unset($u_id);
			
			$richtig = 0;
			$fehlermeldung = "";
			
			if (isset($email) && isset($nickname) && isset($hash)) {
				$nickname = mysqli_real_escape_string($mysqli_link, coreCheckName($nickname, $check_name));
				$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
				$query = "SELECT u_id, u_login, u_nick, u_name, u_passwort, u_adminemail, u_punkte_jahr FROM user "
					. "WHERE u_nick = '$nickname' AND u_level = 'U' AND u_adminemail = '$email' LIMIT 1";
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) == 1) {
					$a = mysqli_fetch_array($result);
					$hash2 = md5(
						$a['u_id'] . $a['u_login'] . $a['u_nick']
							. $a['u_name'] . $a['u_passwort']
							. $a['u_adminemail'] . $a['u_punkte_jahr']);
					if ($hash == $hash2) {
						$richtig = 1;
						$u_id = $a['u_id'];
					} else {
						$fehlermeldung .= $t['pwneu11'];
					}
				} else {
					$fehlermeldung .= $t['pwneu11'];
				}
				mysqli_free_result($result);
			} else if (isset($email) && isset($nickname)) {
				$nickname = mysqli_real_escape_string($mysqli_link, coreCheckName($nickname, $check_name));
				$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
				if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
					mysqli_real_escape_string($mysqli_link, $email))) {
					$fehlermeldung .= $t['pwneu5'] . '<br>';
				}
				
				if ($fehlermeldung == "") {
					$query = "SELECT u_id, u_login, u_nick, u_name, u_passwort, u_adminemail, u_punkte_jahr FROM user "
						. "WHERE u_nick = '$nickname' AND u_level = 'U' AND u_adminemail = '$email' LIMIT 2";
					$result = mysqli_query($mysqli_link, $query);
					if ($result && mysqli_num_rows($result) == 1) {
						$a = mysqli_fetch_array($result);
						$hash = md5(
							$a['u_id'] . $a['u_login'] . $a['u_nick']
								. $a['u_name'] . $a['u_passwort']
								. $a['u_adminemail'] . $a['u_punkte_jahr']);
						
						$email = urlencode($a['u_adminemail']);
						$link = $serverprotokoll . "://" . $http_host
							. $chatserver . $_SERVER['PHP_SELF']
							. "?http_host=$http_host&aktion=passwort_neu&frame=1&email="
							. $email . "&nickname=" . $nickname . "&hash="
							. $hash;
						
						$text2 = str_replace("%link%", $link, $t['pwneu9']);
						$text2 = str_replace("%hash%", $hash, $text2);
						$text2 = str_replace("%nickname%", $a['u_nick'], $text2);
						$email = urldecode($a['u_adminemail']);
						$text2 = str_replace("%email%", $email, $text2);
						
						$mailbetreff = $t['pwneu8'];
						$mailempfaenger = $email;
						$header = "\n" . "X-MC-IP: " . $_SERVER["REMOTE_ADDR"]
							. "\n" . "X-MC-TS: " . time();
						
						mail($mailempfaenger, $mailbetreff, $text2,
							"From: $webmaster ($chat)" . $header);
						
						echo $t['pwneu7'];
						unset($hash);
					} else {
						$fehlermeldung .= $t['pwneu6'] . '<br>';
						unset($hash);
					}
					mysqli_free_result($result);
				}
			} else {
				echo $t['pwneu1'];
			}
			
			if (!$richtig) {
				if ($fehlermeldung <> "") {
					if (isset($hash)) {
						echo $t['pwneu7'];
					} else {
						echo $t['pwneu1'];
					}
					echo "<p><font color=\"red\"><b>$fehlermeldung</b></font></p>\n";
				}
				echo "<form action=\"index.php\">\n"
					. "<table border=0 cellpadding=5 cellspacing=0>\n";
				if (isset($email) && isset($nickname) && $email <> ""
					&& $nickname <> ""
					&& (isset($hash) || $fehlermeldung == "")) {
					if (!isset($hash)) {
						$hash = "";
					}
					?>
					<tr>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['pwneu2']; ?></td>
						<td><input type=hidden name="nickname" width="50" value="<?php echo $nickname; ?>"><?php echo $nickname; ?></td>
					</tr>
					<tr>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['pwneu3']; ?></td>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input type=hidden name="email" width="50" value="<?php echo $email; ?>"><?php echo $email; ?></td>
					</tr>
					<tr>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['pwneu10']; ?></td>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input name="hash" width="50" value="<?php echo $hash; ?>"></td>
					</tr>
					<?php
				} else {
					if (!isset($nickname))
						$nickname = "";
					if (!isset($email))
						$email = "";
					?>
					<tr>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['pwneu2']; ?></td>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input name="nickname" width="50" value="<?php echo $nickname; ?>"></td>
					</tr>
					<tr>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['pwneu3']; ?></td>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input name="email" width="50" value="<?php echo $email; ?>"></td>
					</tr>
					<?php
				}
					?>
					<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
					<input type="hidden" name="aktion" value="passwort_neu">
					<tr>
						<td colspan="2" style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input type=submit value="Absenden"></td>
					</tr>
				</form>
				</table>
				<?php
			} else if ($richtig && $u_id) {
				$query = "SELECT `u_adminemail`, `u_nick` FROM `user` WHERE `u_id` = '$u_id' AND `u_level` = 'U' LIMIT 2";
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) == 1) {
					unset($f);
					$a = mysqli_fetch_array($result);
					
					$pwdneu = genpassword(8);
					$f['u_passwort'] = $pwdneu;
					$f['u_id'] = $u_id;
					$text = str_replace("%passwort%", $f['u_passwort'],
						$t['pwneu15']);
					$text = str_replace("%nickname%", $a['u_nick'], $text);
					$ok = mail($a['u_adminemail'], $t['pwneu14'], $text,
						"From: $webmaster ($chat)");
					
					if ($ok) {
						echo $t['pwneu12'];
						schreibe_db("user", $f, $f['u_id'], "u_id");
					} else {
						echo $t['pwneu13'];
					}
				}
			}
			
			zeige_fuss();
			break;
		
		case "neubestaetigen":
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			echo $willkommen;
			
			unset($fehlermeldung);
			if ($email) {
				$email = mysqli_real_escape_string($mysqli_link, $email);
				if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
					mysqli_real_escape_string($mysqli_link, $email))) {
					$fehlermeldung = $t['neu51'];
				} else {
					$query = "SELECT * FROM mail_check WHERE email = '$email'";
					$result = mysqli_query($mysqli_link, $query);
					if ($result && mysqli_num_rows($result) == 1) {
						$a = mysqli_fetch_array($result);
						$hash2 = md5($a['email'] . "+" . $a['datum']);
						if ($hash == $hash2) {
							$link = $serverprotokoll . "://" . $http_host
								. $chatserver . $_SERVER['PHP_SELF']
								. "?http_host=$http_host&aktion=neu2&frame=1";
							
							$text2 = str_replace("%link%", $link, $t['neu53']);
							$text2 = str_replace("%hash%", $hash, $text2);
							$email = urldecode($email);
							$text2 = str_replace("%email%", $email, $text2);
							
							$mailbetreff = $t['neu38'];
							$mailempfaenger = $email;
							
							echo $t['neu54'];
							mail($mailempfaenger, $mailbetreff, $text2,
								"From: $webmaster ($chat)");
						} else {
							$fehlermeldung = $t['neu52'];
						}
					} else {
						$fehlermeldung = $t['neu51'];
					}
					mysqli_free_result($result);
				}
			}
			
			if (!$email || $fehlermeldung) {
				// Formular für die Freischaltung/Bestätigung der Mailadress
				echo "<p>" . $t['neu50'] . "</p>";
				if ($fehlermeldung) {
					echo "<p><font color=\"red\"><b>$fehlermeldung</b></font></p>\n";
				}
				?>
				<form action="index.php">
					<table>
						<tr>
							<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['neu49']; ?></td>
							<td><input name="email" width="50" value="<?php echo $email; ?>"></td>
						</tr>
						<tr>
							<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['neu43']; ?></td>
							<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input name="hash" width="50" value="<?php echo $hash; ?>"></td>
						</tr>
						<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
						<input type="hidden" name="aktion" value="neubestaetigen">
						<tr>
							<td colspan="2" style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input type="submit" value="Absenden"></td>
						</tr>
					</table>
				</form>
				<?php
			}
			
			zeige_fuss();
			break;
		
		case "mailcheckm":
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			echo $willkommen;
			echo "<p>" . $t['neu42'] . "</p>";
			?>
			<form action="index.php">
				<table>
					<tr>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['neu17']; ?></td>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input name="email" width="50"></td>
					</tr>
					<tr>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><?php echo $t['neu43']; ?></td>
						<td style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input name="hash" width="50"></td>
					</tr>
					<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
					<input type="hidden" name="aktion" value="neu">
					<tr>
						<td colspan="2" style="background-color:<?php echo $farbe_tabelle_kopf; ?>"><input type=submit value="Absenden"></td>
					</tr>
				</table>
			</form>
			<?php
			zeige_fuss();
			break;
		
		case "mailcheck":
		// Header ausgeben
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			echo $willkommen;
			
			// dieser Regex macht eine primitive Prüfung ob eine Mailadresse
			// der Form name@do.main entspricht, wobei
			if (isset($email)) {
				if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
					mysqli_real_escape_string($mysqli_link, $email))) {
					echo str_replace("%email%", $email, $t['neu41']);
					$email = "";
				}
			}
			
			if (!isset($email)) {
				// Formular für die Erstregistierung ausgeben, 1. Schritt
				echo "<p>" . $t['neu33'];
				if (isset($anmeldung_nurmitbest)
					&& strlen($anmeldung_nurmitbest) > 0) { // Anmeldung mit Externer Bestätigung
					echo $t['neu45'];
				}
				echo "<p><table border=0 cellpadding=3 cellspacing=0>"
					. "<form action=\"index.php\">" . "<tr><td>" . $t['neu34']
					. "</td><td><input name=\"email\" maxlength=80></td></tr>"
					. "<tr><td colspan=2><input type=submit value=\""
					. $t['neu35'] . "\">"
					. "<input type=hidden name=\"http_host\" value=\"$http_host\">"
					. "<input type=hidden name=\"aktion\" value=\"mailcheck\"></td></tr>"
					. "</form></table>";
			} else {
				// Mail verschicken, 2. Schritt
				
				$email = trim($email);
				
				// wir prüfen ob User gesperrt ist
				// entweder User = gesperrt
				$query = "SELECT * FROM `user` WHERE ( (`u_adminemail`='$email') OR (`u_email`='$email') ) AND `u_level`='Z'";
				$result = mysqli_query($mysqli_link, $query);
				$num = mysqli_num_rows($result);
				$gesperrt = false;
				if ($num >= 1) {
					$gesperrt = true;
					echo $t['neu40'];
				}
				
				// oder user ist auf Blacklist
				$query = "select u_nick from blacklist left join user on f_blacklistid=u_id "
					. "WHERE user.u_adminemail ='$email'";
				$result = mysqli_query($mysqli_link, $query);
				$num = mysqli_num_rows($result);
				if ($num >= 1) {
					$gesperrt = true;
					echo $t['neu40'];
				}
				
				// oder Domain ist lt. Config verboten
				if ($domaingesperrtdbase != $dbase) {
					$domaingesperrt = array();
				}
				
				for ($i = 0; $i < count($domaingesperrt); $i++) {
					$teststring = strtolower($email);
					if (isset($domaingesperrt[$i]) && $domaingesperrt[$i]
						&& (preg_match($domaingesperrt[$i], $teststring))) {
						$gesperrt = true;
						echo $t['neu40'];
					}
				}
				unset($teststring);
				
				if (($begrenzung_anmeld_pro_mailadr > 0) and (!$gesperrt)) {
					$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '$email'";
					$result = mysqli_query($mysqli_link, $query);
					$num = mysqli_num_rows($result);
					if ($num >= $begrenzung_anmeld_pro_mailadr) {
						$gesperrt = true;
						echo str_replace("%anzahl%",
							$begrenzung_anmeld_pro_mailadr, $t['neu55']);
					}
				}
				
				if (!$gesperrt) {
					// Überprüfung auf Formular mehrmals abgeschickt
					$query = "DELETE FROM mail_check WHERE email = '$email'";
					mysqli_query($mysqli_link, $query);
					
					$hash = md5($email . "+" . date("Y-m-d"));
					$email = urlencode($email);
					
					if (isset($anmeldung_nurmitbest)
						&& strlen($anmeldung_nurmitbest) > 0) {
						// Anmeldung mit externer Bestätigung
						$link1 = $serverprotokoll . "://" . $http_host
							. $chatserver . $_SERVER['PHP_SELF']
							. "?http_host=$http_host&aktion=neubestaetigen&frame=1";
						$link2 = $serverprotokoll . "://" . $http_host
							. $chatserver . $_SERVER['PHP_SELF']
							. "?http_host=$http_host&aktion=neu2&frame=1";
						
						$text2 = str_replace("%link1%", $link1, $t['neu47']);
						$text2 = str_replace("%link2%", $link2, $text2);
						$text2 = str_replace("%hash%", $hash, $text2);
						$email = urldecode($email);
						$text2 = str_replace("%email%", $email, $text2);
						
						$mailbetreff = $t['neu46'];
						$mailempfaenger = $anmeldung_nurmitbest;
						
						echo $t['neu48'];
					} else {
						// Normale Anmeldung
						$link = $serverprotokoll . "://" . $http_host
							. $_SERVER['PHP_SELF']
							. "?http_host=$http_host&aktion=neu&email=$email&hash=$hash&frame=1";
						$link2 = $serverprotokoll . "://" . $http_host
							. $_SERVER['PHP_SELF']
							. "?http_host=$http_host&aktion=neu2&frame=1";
						
						$text2 = str_replace("%link%", $link, $t['neu36']);
						$text2 = str_replace("%link2%", $link2, $text2);
						$text2 = str_replace("%hash%", $hash, $text2);
						$email = urldecode($email);
						$text2 = str_replace("%email%", $email, $text2);
						
						$mailbetreff = $t['neu38'];
						$mailempfaenger = $email;
						
						echo $t['neu37'];
					}
					
					mail($mailempfaenger, $mailbetreff, $text2,
						"From: $webmaster ($chat)" . "\n" . "X-MC-IP: "
							. $_SERVER["REMOTE_ADDR"] . "\n" . "X-MC-TS: "
							. time());
					$email = mysqli_real_escape_string($mysqli_link, $email);
					$query = "REPLACE INTO mail_check (email,datum) VALUES ('$email',NOW())";
					$result = mysqli_query($mysqli_link, $query);
					
				}
			}
			
			zeige_fuss();
			break;
		
		case "abweisen":
		// Header ausgeben
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			
			echo $t['login4'];
			
			zeige_fuss();
			
			break;
		
		case "gesperrt":
		// Header ausgeben
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			
			if ($chat_offline_kunde) {
				echo $chat_offline_kunde_txt;
			} else {
				echo $chat_offline;
			}
			
			zeige_fuss();
			
			break;
		
		case "login":
		// Sequence für online- und chat-Tabelle erzeugen
			erzeuge_sequence("online", "o_id");
			erzeuge_sequence("chat", "c_id");
			
			// Weiter mit login
			if (!isset($passwort) || $passwort == "") {
				// Login als Gast
				
				// Falls Gast-Login erlaubt ist:
				if ($gast_login) {
					
					// Prüfen, ob von der IP und dem User-Agent schon ein Gast online ist und ggf abweisen
					$query4711 = "SELECT o_id FROM online "
						. "WHERE o_browser='" . $_SERVER["HTTP_USER_AGENT"]
						. "' " . "AND o_ip='" . $_SERVER["REMOTE_ADDR"] . "' "
						. "AND o_level='G' "
						. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
					$result = mysqli_query($mysqli_link, $query4711);
					if ($result)
						$rows = mysqli_num_rows($result);
					mysqli_free_result($result);
					
				}
				
				// Login abweisen, falls mehr als ein Gast online ist oder Gäste gesperrt sind
				if (!$gast_login or ($rows > 1 && !$gast_login_viele)
					or $temp_gast_sperre) {
					
					// Gäste sind gesperrt
					
					// Header ausgeben
					zeige_header_ende();
					?>
					<body>
					<?php
					zeige_kopf();
					
					// Passenden Fehlertext ausgeben
					if (!$gast_login) {
						echo $t['login16'];
					} else {
						echo $t['login15'];
					}
					
					// Box für Login
					if ($frameset_bleibt_stehen) {
						?>
						<form action="<?php echo $chat_file; ?>" name="form1" method="POST">
						<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
						<?php
					} else {
						?>
						<form action="<?php echo $chat_file; ?>" target="topframe" name="form1" method="POST">
						<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
						<?php
					}
					
					echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
						. "// end hiding -->\n</script>\n";
					
					// Disclaimer ausgeben
					show_box($login_titel, $logintext, "", "100%");
					echo "<div style=\"text-align: center;\">" . $f3
						. str_replace("%farbe_text%", $farbe_text, $disclaimer)
						. $f4 . "</div>\n</form><br>";
					zeige_fuss();
					exit;
					
				}
				
				// Login als gast
				
				// Im Nick alle Sonderzeichen entfernen, Länge prüfen
				if (!isset($login))
					$login = "";
				$login = coreCheckName($login, $check_name);
				
				if (!isset($keineloginbox))
					$keineloginbox = false;
				if (!$keineloginbox) {
					if (strlen($login) < 4 || strlen($login) > 20)
						$login = "";
				}
				
				// Falls kein Nick übergeben, Nick finden
				if (strlen($login) == 0) {
					if ($gast_name_auto) {
						// freien Nick bestimmen falls in der Config erlaubt
						$rows = 1;
						$i = 0;
						$anzahl = count($gast_name);
						while ($rows != 0 && $i < 100) {
							$login = $gast_name[mt_rand(1, $anzahl)];
							$query4711 = "SELECT u_id FROM user "
								. "WHERE u_nick='$login' ";
							$result = mysqli_query($mysqli_link, $query4711);
							$rows = mysqli_num_rows($result);
							$i++;
						}
						mysqli_free_result($result);
					} else {
						// freien Namen bestimmen
						$rows = 1;
						$i = 0;
						while ($rows != 0 && $i < 100) {
							$login = $t['login13'] . strval((mt_rand(1, 10000)) + 1);
							$query4711 = "SELECT `u_id` FROM `user` WHERE `u_nick`='" . mysqli_real_escape_string($mysqli_link, $login) . "'";
							$result = mysqli_query($mysqli_link, $query4711);
							$rows = mysqli_num_rows($result);
							$i++;
						}
						mysqli_free_result($result);
					}
				}
				
				// Im Nick alle Sonderzeichen entfernen, vorsichtshalber nochmals prüfen
				$login = mysqli_real_escape_string($mysqli_link, coreCheckName($login, $check_name));
				
				// Userdaten für Gast setzen
				$f['u_level'] = "G";
				$f['u_passwort'] = mt_rand(1, 10000);
				$f['u_name'] = $login;
				$f['u_nick'] = $login;
				$passwort = $f['u_passwort'];
				$f['u_loginfehler'] = '';
				
				// Prüfung, ob dieser User bereits existiert
				$query4711 = "SELECT `u_id` FROM `user` WHERE `u_nick`='$f[u_nick]'";
				$result = mysqli_query($mysqli_link, $query4711);
				
				if (mysqli_num_rows($result) == 0) {
					// Account in DB schreiben
					schreibe_db("user", $f, "", "u_id");
				}
				
			}
			
			// Login als registrierter User
			
			// Testen, ob frühere Loginversuche fehlschlugen (nur Admins)
			$query4711 = "SELECT u_id,u_nick,u_loginfehler,u_login,u_backup FROM user "
				. "WHERE (u_name = '$login' OR u_nick = '$login') "
				. "AND (u_level='S' OR u_level='C') ";
			$result = mysqli_query($mysqli_link, $query4711);
			if ($result)
				$rows = mysqli_num_rows($result);
			
			// Voreinstellung: Weiter mit Login
			$login_ok = true;
			
			// Mehr als ein güliger Account gefunden, nochmals nach nick suchen
			if ($result && $rows > 1) {
				mysqli_free_result($result);
				$query4711 = "SELECT u_id,u_nick,u_loginfehler,u_login,u_backup FROM user "
					. "WHERE (u_nick = '$login') "
					. "AND (u_level='S' OR u_level='C') ";
				$result = mysqli_query($mysqli_link, $query4711);
				if ($result)
					$rows = mysqli_num_rows($result);
			}
			if ($result && $rows == 1) {
				$userdata = mysqli_fetch_object($result);
				if ($userdata->u_loginfehler) {
					
					// Nach zwei fehlgeschlagenen Logins und dem letzen Versuch keine 5 min vergangen 
					// -> Login sperren und merken
					$u_loginfehler = unserialize($userdata->u_loginfehler);
					$letzter_eintrag = end($u_loginfehler);
					if ((time() - $letzter_eintrag[login]) < 300
						&& count($u_loginfehler) > 9) {
						$login_ok = false;
					}
				}
			}
			
			// $crypted_password_extern = 0; wird in functions-init.php gesetzt
			// Wenn externe Schnittstelle vorhanden ist
			// wird die dort vorhandene von function und variablen importiert
			if (file_exists("ext/functions.php-" . $http_host)) {
				// Speziell für die weitere verwendung in auth_user wird die $crypted_password_extern = 0 oder 1 aus der Datei gelesen
				require("ext/functions.php-" . $http_host);
			}
			
			// Passwort prüfen und Userdaten lesen
			$rows = 0;
			$result = auth_user("u_nick", $login, $passwort);
			if ($result) {
				$rows = mysqli_num_rows($result);
			}
			
			// Nick nicht gefunden, optionales include starten und User ggf. aus externer Datenbank kopieren
			if ($rows == 0 && file_exists("ext/functions.php-" . $http_host)) {
				// Wenn User = Gast, dann mit leerem Passwort in die Externe Prüfung
				// erforderlich, da aus externer Schnittstelle Gastdaten ohne PW kommen
				// Sicherheitsproblem in der Externen Schnittstelle: da jeder unter diesem 
				// Gastnick einloggen kann, und der ursprüngliche User fliegt raus
				// Muss daher im Übergeordneten System sichergestellt sein
				if ($f['u_level'] == 'G') {
					$passwort = '';
				}
				
				// Function ext_lese_user oben eingebunden
				$passwort_ext = ext_lese_user($login, $passwort);
				
				$result = auth_user("u_nick", $login, $passwort_ext);
				if ($result) {
					$rows = mysqli_num_rows($result);
					$passwort = $passwort_ext;
					if ($rows == 1) {
					}
				}
			}
			
			// Nick nicht gefunden, nochmals mit Usernamen suchen
			if ($rows == 0) {
				$result = auth_user("u_name", $login, $passwort);
				if ($result) {
					$rows = mysqli_num_rows($result);
				}
				
			}
			
			// Login fehlgeschlagen
			if ($rows == 0 && isset($userdata) && is_array($userdata)
				&& $userdata) {
				
				// Fehllogin bei Admin: falsches Passwort oder Username -> max 100 Loginversuche in Userdaten merken
				if ($userdata->u_loginfehler)
					$u_loginfehler = unserialize($userdata->u_loginfehler);
				if (count($u_loginfehler) < 100) {
					unset($f);
					unset($temp);
					$f['nick'] = substr($userdata->u_nick, 0, 20);
					$f['pw'] = substr($passwort, 0, 20);
					$f['login'] = time();
					$f['ip'] = substr($_SERVER["REMOTE_ADDR"], 0, 20);
					$u_loginfehler[] = $f;
					$temp['u_loginfehler'] = serialize($u_loginfehler);
					$temp['u_login'] = $userdata->u_login;
					schreibe_db("user", $temp, $userdata->u_id, "u_id");
				}
			}
			
			// güliger Account gefunden, weiter mit Login oder Fehlermeldungen ausgeben
			if ($result && $rows == 1 && $login_ok) {
				
				// Login Ok, Userdaten setzen
				$row = mysqli_fetch_object($result);
				$u_id = $row->u_id;
				$u_name = $row->u_name;
				$u_nick = $row->u_nick;
				$u_level = $row->u_level;
				$u_backup = $row->u_backup;
				$u_agb = $row->u_agb;
				$u_punkte_monat = $row->u_punkte_monat;
				$u_punkte_jahr = $row->u_punkte_jahr;
				$u_punkte_datum_monat = $row->u_punkte_datum_monat;
				$u_punkte_datum_jahr = $row->u_punkte_datum_jahr;
				$u_punkte_gesamt = $row->u_punkte_gesamt;
				$ip_historie = unserialize($row->u_ip_historie);
				$u_frames = unserialize($row->u_frames);
				$nick_historie = unserialize($row->u_nick_historie);
				
				if ($loginimsicherenmodus == "1") {
					$u_backup = 1;
					$f['u_id'] = $u_id;
					$f['u_backup'] = 1;
					schreibe_db("user", $f, $u_id, "u_id");
				}
				
				// User online bestimmen
				if ($chat_max[$u_level] != 0) {
					$query = "SELECT count(o_id) FROM online "
						. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
					$result2 = mysqli_query($mysqli_link, $query);
					if ($result2)
						$onlineanzahl = mysqli_result($result2, 0, 0);
					mysqli_free_result($result2);
				}
				
				// Login erfolgreich ?
				if ($u_level == "Z") {
					
					// User gesperrt -> Fehlermeldung ausgeben
					
					// Header ausgeben
					zeige_header_ende();
					?>
					<body>
					<?php
					zeige_kopf();
					
					echo str_replace("%u_name%", $u_name,
						str_replace("%u_nick%", $u_nick, $t['login5']));
					
					zeige_fuss();
					
				} elseif (false && $HTTP_COOKIE_VARS[MAINCHAT2] != "on" && ($u_level == "C" || $u_level == "S")) {
					
					// Der User ein Admin und es sind cookies gesetzt -> Fehlermeldung ausgeben
					
					// Header ausgeben
					zeige_header_ende();
					?>
					<body>
					<?php
					zeige_kopf();
					
					echo str_replace("%url%", $chat_file, $t['login25']);
					unset($u_name);
					unset($u_nick);
					
					zeige_fuss();
					
				} elseif ($chat_max[$u_level] != 0 && $onlineanzahl > $chat_max[$u_level]) {
					
					// Maximale Anzahl der User im Chat erreicht -> Fehlermeldung ausgeben
					
					// Header ausgeben
					zeige_header_ende();
					?>
					<body>
					<?php
					zeige_kopf();
					
					$txt = str_replace("%online%", $onlineanzahl, $t['login24']);
					$txt = str_replace("%max%", $chat_max[$u_level], $txt);
					$txt = str_replace("%leveltxt%", $level[$u_level], $txt);
					$txt = str_replace("%zusatztext%", $chat_max[zusatztext],
						$txt);
					echo $txt;
					
					unset($u_name);
					unset($u_nick);
					
					// Box für Login
					if ($frameset_bleibt_stehen) {
						echo "<FORM ACTION=\"$chat_file\" name=\"form1\" method=\"POST\">"
							. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n";
					} else {
						echo "<FORM ACTION=\"$chat_file\" target=\"topframe\" name=\"form1\" method=\"POST\">"
							. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n";
					}
					
					if ($gast_login && $communityfeatures && $forumfeatures) {
						$titel = $login_titel . "[<a href=\""
							. $_SERVER['PHP_SELF']
							. "?http_host=$http_host&id=$id&aktion=login&"
							. $t['login10'] . "=los&eintritt=forum\">" . $ft0
							. $t['login23'] . $ft1 . "</A>]";
					} else {
						$titel = $login_titel;
					}
					
					if ($einstellungen_aendern) {
						$titel .= "[<a href=\"" . $_SERVER['PHP_SELF']
							. "?http_host=$http_host&aktion=passwort_neu\">"
							. $ft0 . $t['login27'] . $ft1 . "</A>]";
					}
					
					// Box und Disclaimer ausgeben
					show_box($titel, $logintext, "", "100%");
					echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
						. "// end hiding -->\n</script>\n";
					echo "<div style=\"text-align: center;\">" . $f3
						. str_replace("%farbe_text%", $farbe_text, $disclaimer)
						. $f4 . "</div>\n</form><br>";
					
					zeige_fuss();
					
				} else {
					$captcha_text1 = filter_input(INPUT_POST, 'captcha_text1', FILTER_SANITIZE_STRING);
					$captcha_text2 = filter_input(INPUT_POST, 'captcha_text2', FILTER_SANITIZE_STRING);
					$ergebnis = filter_input(INPUT_POST, 'ergebnis', FILTER_SANITIZE_STRING);
					
					if (($keine_agb == 1) && ($captcha_text == 0)) {
						$u_agb = 'Y';
					}
					
					if (($temp_gast_sperre) && ($u_level == 'G')) {
						$captcha_text1 = "999";
					} // abweisen, falls Gastsperre aktiv
					if ((isset($captcha_text1)) and ($captcha_text1 == "")) {
						$captcha_text1 = "999";
					} // abweisen, falls leere eingabe
					
					if ( ($captcha_text == 1) && (isset($captcha_text2)) && ($captcha_text2 != md5( $u_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") ) )) {
						$los = "";
					}
					
					// muss User/Gast  noch Nutzungsbestimmungen bestätigen?
					if ($los != $t['login17'] && $u_agb != "Y") {
						
						// Nutzungsbestimmungen ausgeben
						
						// ggf. Nutzungsbestimmungen aus Sprachdatei mit extra Nutzungsbestimmungen aus config.php überschreiben
						if ((isset($extra_agb)) and ($extra_agb <> "")
							and ($extra_agb <> "standard"))
							$t['agb'] = $extra_agb;
						
						// Header ausgeben
						zeige_header_ende();
						?>
						<body>
						<?php
						zeige_kopf();
						
						// Box für Login
						if ($frameset_bleibt_stehen) {
							?>
							<form action="<?php echo $chat_file; ?>" name="form1" method="POST">
							<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
							<?php
						} else {
							?>
							<form action="<?php echo $chat_file; ?>" target="topframe" name="form1" method="POST">
							<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
							<?php
						}
						
						if (!isset($eintritt))
							$eintritt = RaumNameToRaumID($lobby);
						
						if ($captcha_text == 1) {
							?>
							<table class="tabelle_kopf">
								<tr>
									<td colspan="2" class="tabelle_kopfzeile"><?php echo $t['willkommen3']; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="tabelle_koerper_login"><?php echo $t['agb']; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="tabelle_koerper_login"><?php echo $t['captcha1']; ?><br>
								<?php
								$aufgabe = mt_rand(1, 3);
								if ($aufgabe == 1) { // +
									$zahl1 = mt_rand(0, 99);
									$zahl2 = mt_rand(0, 99 - $zahl1);
									$ergebnis = $zahl1 + $zahl2;
								} else if ($aufgabe == 2) { // -
									$zahl1 = mt_rand(0, 99);
									$zahl2 = mt_rand(0, $zahl1);
									$ergebnis = $zahl1 - $zahl2;
								} else { // *
									$zahl1 = mt_rand(0, 10);
									$zahl2 = mt_rand(0, 10);
									$ergebnis = $zahl1 * $zahl2;
								}
								
								echo $tzahl[$zahl1] . " " . $taufgabe[$aufgabe]
									. " " . $tzahl[$zahl2]
									. " &nbsp;&nbsp;&nbsp;&nbsp;";
								
									//echo md5( $u_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") );
									//echo md5( $u_id . "code" . $ergebnis . "+" . date("Y-m-d h") );
									?>
										<input type="text" name="captcha_text1">
										<input type="hidden" name="captcha_text2" value="<?php echo md5( $u_id . "+code+" . $ergebnis . "+" . date("Y-m-d h") ); ?>">
										<input type="hidden" name="ergebnis" value="<?php echo $ergebnis; ?>">
									</td>
								</tr>
								<tr>
									<td align="left" class="tabelle_koerper_login"><?php echo $f1; ?><b><input type="submit" name="los" value="<?php echo $t['login17']; ?>"></b></td>
									<td align="right" class="tabelle_koerper_login"><?php echo $f1; ?><input type="submit" name="los" value="<?php echo $t['login18']; ?>"></td>
								</tr>
							</table>
							<?php echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n"; ?>
							<input type="hidden" name="login" value="<?php echo $login; ?>">
							<input type="hidden" name="passwort" value="<?php echo $passwort; ?>">
							<input type="hidden" name="eintritt" value="<?php echo $eintritt; ?>">
							<input type="hidden" name="aktion" value="login">
							</form>
							<?php
						} else {
							?>
							<table class="tabelle_kopf">
								<tr>
									<td colspan="2" class="tabelle_kopfzeile"><?php echo $t['willkommen3']; ?></td>
								</tr>
								<tr>
									<td colspan="2" class="tabelle_koerper_login"><?php echo $t['agb']; ?></td>
								</tr>
								<tr>
									<td align="left" class="tabelle_koerper_login"><?php echo $f1; ?><b><input type="submit" name="los" value="<?php echo $t['login17']; ?>"></b></td>
									<td align="right" class="tabelle_koerper_login"><?php echo $f1; ?><input type="submit" name="los" value="<?php echo $t['login18']; ?>"></td>
								</tr>
							</table>
							<?php echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n"; ?>
							<input type="hidden" name="login" value="<?php echo $login; ?>">
							<input type="hidden" name="passwort" value="<?php echo $passwort; ?>">
							<input type="hidden" name="eintritt" value="<?php echo $eintritt; ?>">
							<input type="hidden" name="aktion" value="login">
							</form>
							<?php
						}
						
						zeige_fuss();
						exit;
						
					} elseif ($los == $t['login17']) {
						// Nutzungsbestimmungen wurden bestätigt
						$u_agb = "Y";
					} else {
						$u_agb = "";
					}
					
					// User in Blacklist überprüfen
					// in den kostenlosen Chats konnte es sein, das die Tabelle nicht vorhanden ist
					$query2 = "SELECT f_text from blacklist where f_blacklistid=$u_id";
					$result2 = mysqli_query($mysqli_link, $query2);
					if ($result2 AND mysqli_num_rows($result2) > 0) {
						$infotext = "Blacklist: "
							. mysqli_result($result2, 0, 0);
						$warnung = TRUE;
					}
					if ($result2)
						mysqli_free_result($result2);
					
					// Bei Login dieses Users alle Admins (online, nicht Temp) warnen
					if ($warnung) {
						
						if ($eintritt == 'forum') {
							$raumname = " (" . $whotext[2] . ")";
						} else {
							$query2 = "SELECT r_name from raum where r_id=" . intval($eintritt);
							$result2 = mysqli_query($mysqli_link, $query2);
							if ($result2 AND mysqli_num_rows($result2) > 0) {
								$raumname = " (" . mysqli_result($result2, 0, 0)
									. ") ";
							} else {
								$raumname = "";
							}
							mysqli_free_result($result2);
						}
						
						$query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
						$result2 = mysqli_query($mysqli_link, $query2);
						if ($result2 AND mysqli_num_rows($result2) > 0) {
							$txt = str_replace("%ip_adr%", $ip_adr,
								$t['default6']);
							$txt = str_replace("%ip_name%", $ip_name, $txt);
							$txt = str_replace("%is_infotext%", $infotext, $txt);
							while ($row2 = mysqli_fetch_object($result2)) {
								$ur1 = "user.php?http_host=<HTTP_HOST>&id=<ID>&aktion=zeig&user=$u_id";
								$ah1 = "<a href=\"$ur1\" target=\"$u_nick\" onclick=\"neuesFenster('$ur1','$u_nick'); return(false);\">";
								$ah2 = "</A>";
								system_msg("", 0, $row2->o_user, $system_farbe,
									str_replace("%u_nick%",
										$ah1 . $u_nick . $ah2 . $raumname,
										$txt));
							}
						}
						mysqli_free_result($result2);
					}
					
					// User nicht gesperrt, weiter mit Login und Eintritt in ausgewählten Raum mit ID $eintritt
					
					// Hash-Wert ermitteln
					$hash_id = id_erzeuge($u_id);
					
					// $javascript wird als input type hidden übergeben...
					if (isset($javascript) && $javascript == "on") {
						$javascript = 1;
					} else {
						$javascript = 0;
					}
					
					// Login
					$o_id = login($u_id, $u_nick, $u_level, $hash_id,
						$javascript, $ip_historie, $u_agb, $u_punkte_monat,
						$u_punkte_jahr, $u_punkte_datum_monat,
						$u_punkte_datum_jahr, $u_punkte_gesamt);
					
					// Beichtstuhl-Special
					// Falls es Räume mit nur einem Admin gibt und niemand in der Lobby ist,
					// Eintritt in einen solchen Raum, ansonsten in Lobby
					if (isset($beichtstuhl) && $beichtstuhl) {
						
						$login_in_lobby = FALSE;
						
						// Prüfen, ob ein User in der Lobby ist
						$query2 = "SELECT o_id "
							. "FROM raum,online WHERE o_raum=r_id "
							. "AND r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' "
							. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
						$result2 = mysqli_query($mysqli_link, $query2);
						
						if ($result2 && mysqli_num_rows($result2) > 0) {
							
							// Mehr als 0 User in Lobby
							mysqli_free_result($result2);
							$login_in_lobby = TRUE;
							
						} else {
							
							// Prüfen, ob es Räume mit genau einem Admin gibt
							mysqli_free_result($result2);
							$query2 = "SELECT r_id,count(o_id) as anzahl, "
								. "count(o_level='C') as CADMIN, count(o_level='S') as SADMIN "
								. "FROM raum,online WHERE o_raum=r_id "
								. "AND r_name!='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' "
								. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
								. "GROUP BY r_id HAVING anzahl=1 AND (CADMIN=1 OR SADMIN=1)";
							$result2 = mysqli_query($mysqli_link, $query2);
							$anzahl = mysqli_num_rows($result2);
							if ($result2 && $anzahl == 1) {
								$eintritt = mysqli_result($result2, 0, "r_id");
							} elseif ($result2 && $anzahl > 1) {
								$eintritt = mysqli_result($result2,
									mt_rand(0, $anzahl - 1), "r_id");
							} else {
								$login_in_lobby = TRUE;
							}
							mysqli_free_result($result2);
							
						}
						
						// ID der Lobby neu ermitteln -> Login in Lobby
						if ($login_in_lobby) {
							$query2 = "SELECT r_id FROM raum WHERE r_name = '" . mysqli_real_escape_string($mysqli_link, $eintrittsraum) . "' ";
							$result2 = mysqli_query($mysqli_link, $query2);
							if ($result2 && mysqli_num_rows($result2) == 1) {
								$eintritt = mysqli_result($result2, 0, "r_id");
							}
							mysqli_free_result($result2);
						}
						
						// Bei Login dieses Users alle Admins (online, nicht Temp) informieren
						$query2 = "SELECT r_name from raum where r_id= '" . mysqli_real_escape_string($mysqli_link, $eintritt) . "'";
						$result2 = mysqli_query($mysqli_link, $query2);
						if ($result2 AND mysqli_num_rows($result2) > 0) {
							$raumname = mysqli_result($result2, 0, 0);
						} else {
							$raumname = "";
						}
						mysqli_free_result($result2);
						
						$query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
						$result2 = mysqli_query($mysqli_link, $query2);
						if ($result2 AND mysqli_num_rows($result2) > 0) {
							$txt = str_replace("%raumname%", $raumname,
								$t['default7']);
							while ($row2 = mysqli_fetch_object($result2)) {
								$ur1 = "user.php?http_host=<HTTP_HOST>&id=<ID>&aktion=zeig&user=$u_id";
								$ah1 = "<a href=\"$ur1\" target=\"$u_nick\" onclick=\"neuesFenster('$ur1','$u_nick'); return(false);\">";
								$ah2 = "</A>";
								system_msg("", 0, $row2->o_user, $system_farbe,
									str_replace("%u_nick%",
										$ah1 . $u_nick . $ah2, $txt));
							}
						}
						mysqli_free_result($result2);
						
					}
					
					zeige_header_ende();
					
					if ($communityfeatures && $eintritt == "forum") {
						
						// Login ins Forum
						betrete_forum($o_id, $u_id, $u_nick, $u_level);
						
						// Frame-Einstellungen für Browser definieren
						$user_agent = strtolower($HTTP_USER_AGENT);
						if (preg_match("/linux/", $user_agent)) {
							$frame_type = "linux";
						} elseif (preg_match("/solaris/", $user_agent)) {
							$frame_type = "solaris";
						} elseif (preg_match("/msie/", $user_agent)) {
							$frame_type = "ie";
						} elseif (preg_match('#mozilla/4\.7#i', $user_agent)) {
							$frame_type = "nswin";
						} else {
							$frame_type = "def";
						}
						
						// Obersten Frame definieren
						if (!isset($frame_online)) {
							$frame_online = "frame_online.php";
						}
						
						// Falls user eigene Einstellungen für das Frameset hat -> überschreiben
						if (is_array($u_frames)) {
							foreach ($u_frames as $key => $val) {
								if ($val)
									$frame_size[$frame_type][$key] = $val;
							}
						}
						
						echo "<frameset rows=\"$frame_online_size,*,5,"
							. $frame_size[$frame_type]['interaktivforum']
							. ",1\" border=0 frameborder=0 framespacing=0>\n";
						echo "<frame src=\"$frame_online?http_host=$http_host\" name=\"frame_online\" marginwidth=0 marginheight=0 scrolling=no>\n";
						echo "<frame src=\"forum.php?http_host=$http_host&id=$hash_id\" name=\"forum\" marginwidth=\"0\" marginheight=\"0\" scrolling=AUTO>\n";
						echo "<frame src=\"leer.php?http_host=$http_host\" name=\"leer\" marginwidth=\"0\" marginheight=\"0\" scrolling=no>\n";
						echo "<frameset cols=\"*,"
							. $frame_size[$frame_type]['messagesforum']
							. "\" border=0 frameborder=0 framespacing=0>\n";
						echo "<frame src=\"messages-forum.php?http_host=$http_host&id=$hash_id\" name=\"messages\" marginwidth=0 marginheight=0 scrolling=AUTO>\n";
						echo "<frame src=\"interaktiv-forum.php?http_host=$http_host&id=$hash_id\" name=\"interaktiv\" marginwidth=0 marginheight=0 scrolling=no>\n";
						echo "</frameset>\n";
						echo "<frame src=\"schreibe.php?http_host=$http_host&id=$hash_id&o_who=2\" name=\"schreibe\" marginwidth=0 marginheight=0 scrolling=no>\n";
						echo "</frameset>\n";
						echo "<noframes>\n";
						echo "<body " . $t['login6'];
						die();
					} else {
						// Chat betreten
						betrete_chat($o_id, $u_id, $u_nick, $u_level,
							$eintritt, $javascript, $u_backup);
						$back = 1;
						
						// Frame-Einstellungen für Browser definieren
						$user_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
						if (preg_match("/linux/", $user_agent)) {
							$frame_type = "linux";
						} elseif (preg_match("/solaris/", $user_agent)) {
							$frame_type = "solaris";
						} elseif (preg_match("/msie/", $user_agent)) {
							$frame_type = "ie";
						} elseif (preg_match('#mozilla/4\.7#i', $user_agent)) {
							$frame_type = "nswin";
						} else {
							$frame_type = "def";
						}
						
						// Obersten Frame definieren
						if (!isset($frame_online) || $frame_online == "") {
							$frame_online = "frame_online.php";
						}
						if ($u_level == "M") {
							$frame_size[$frame_type]['interaktiv'] = $moderationsgroesse;
							$frame_size[$frame_type]['eingabe'] = $frame_size[$frame_type]['eingabe']
								* 2;
						}
						
						// Falls user eigene Einstellungen für das Frameset hat -> überschreiben
						if (is_array($u_frames)) {
							foreach ($u_frames as $key => $val) {
								if ($val)
									$frame_size[$frame_type][$key] = $val;
							}
						}
						
						// Frameset aufbauen
						?>
						<frameset rows="<?php echo $frame_online_size;?>,*,<?php echo $frame_size[$frame_type]['eingabe']; ?>,<?php echo $frame_size[$frame_type]['interaktiv']; ?>,1" border="0" frameborder="0" framespacing="0">
							<frame src="<?php echo $frame_online;?>?http_host=<?php echo $http_host; ?>" name="frame_online" marginwidth="0" marginheight="0" scrolling="no">
							<frameset cols="*,<?php echo $frame_size[$frame_type]['chatuserliste'];?>" border="0" frameborder="0" framespacing="0">
								<frame src="chat.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>&back=<?php echo $back; ?>" name="chat" marginwidth="4" marginheight="0">
								<?php
								if (!isset($userframe_url)) {
									?>
									<frame src="user.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>&aktion=chatuserliste" marginwidth="4" marginheight="0" name="userliste">
									<?php
								} else {
									?>
									<frame src="<?php echo $userframe_url; ?>" marginwidth="4" marginheight="0" name="userliste">
									<?php
								}
								?>
							</frameset>
							<frame src="eingabe.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="eingabe" marginwidth="0" marginheight="0" scrolling="no">
							<?php
							if ($u_level == "M") {
								?>
								<frameset cols="*,220" border="0" frameborder="0" framespacing="0">
									<frame src="moderator.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="moderator" marginwidth="0" marginheight="0" scrolling="auto">
									<frame src="interaktiv.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
								</frameset>
								<?php
							} else {
								?>
								<frame src="interaktiv.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
								<?php
							}
							?>
							<frame src="schreibe.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="schreibe" marginwidth="0" marginheight="0" scrolling="no">
						</frameset>
						<?php
						echo "<noframes>\n"
							. "<!-- Browser/OS  $frame_type -->\n" . "<body "
							. $t['login6'];
					}
					?>
					<body>
					<?php
				}
				
			} elseif (!$login_ok) {
				
				if ($rows == 0) {
					// Zu viele fehlgeschlagenen Logins, aktueller Login ist
					// wieder fehlgeschlagen, daher Mail an Betreiber verschicken
					if ($userdata->u_loginfehler)
						$u_loginfehler = unserialize($userdata->u_loginfehler);
					$betreff = str_replace("%login%", $login, $t['login20']);
					$text = str_replace("%login%", $login, $t['login21'])
						. "\n";
					foreach ($u_loginfehler as $key => $val) {
						$text .= "[" . substr("00" . $key, -3) . "] "
							. date("M d Y H:i:s", $val[login])
							. " \tIP: $val[ip] \tPW: $val[pw]\n";
					}
					$text .= "\n-- \n   $chat ($serverprotokoll://"
						. $HTTP_HOST . $_SERVER['PHP_SELF'] . " | "
						. $http_host . ")\n";
					mail($webmaster, $betreff, $text,
						"From: $hackmail\nReply-To: $hackmail\nCC: $hackmail\n");
				}
				
				// Fehlermeldung ausgeben
				
				// Header ausgeben
				zeige_header_ende();
				?>
				<body>
				<?php
				zeige_kopf();
				unset($u_name);
				unset($u_nick);
				echo "<div style=\"text-align: center;\"><P><b>"
					. str_replace("%login%", $login, $t['login20'])
					. "</b></P>" . $f3
					. str_replace("%farbe_text%", $farbe_text, $disclaimer)
					. $f4 . "</div>\n</form><br>";
				zeige_fuss();
				
			} else {
				// Login fehlgeschlagen
				
				// Header ausgeben
				zeige_header_ende();
				?>
				<body>
				<?php
				zeige_kopf();
				
				unset($u_name);
				unset($u_nick);
				
				if (strlen($passwort) == 0) {
					// Kein Passwort eingegeben oder der Nickname exitiert bereits
					echo $t['login19'];
				} else {
					// Falsches Passwort oder Nickname
					echo $t['login7'];
				}
				
				// Box für Login
				if ($frameset_bleibt_stehen) {
					echo "<FORM ACTION=\"$chat_file\" name=\"form1\" method=\"POST\">"
						. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n";
				} else {
					echo "<FORM ACTION=\"$chat_file\" target=\"topframe\" name=\"form1\" method=\"POST\">"
						. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n";
				}
				
				if ($gast_login && $communityfeatures && $forumfeatures) {
					$titel = $login_titel . "[<a href=\""
						. $_SERVER['PHP_SELF']
						. "?http_host=$http_host&id=&aktion=login&"
						. $t['login10'] . "=los&eintritt=forum\">" . $ft0
						. $t['login23'] . $ft1 . "</A>]";
				} else {
					$titel = $login_titel;
				}
				
				if ($einstellungen_aendern) {
					$titel .= "[<a href=\"" . $_SERVER['PHP_SELF']
						. "?http_host=$http_host&aktion=passwort_neu\">" . $ft0
						. $t['login27'] . $ft1 . "</A>]";
				}
				
				// Box und Disclaimer ausgeben
				if (!isset($keineloginbox) || !$keineloginbox)
					show_box($titel, $logintext, "", "100%");
				echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
					. "// end hiding -->\n</script>\n";
				echo "<div style=\"text-align: center;\">" . $f3
					. str_replace("%farbe_text%", $farbe_text, $disclaimer)
					. $f4 . "</div>\n</form><br>";
				
				zeige_fuss();
				
			}
			break;
		
		case "neu":
		// Neu anmelden
		
		// Header ausgeben
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			echo $willkommen;
			
			// Gggf Nick setzen
			if ((isset($f['u_nick'])) && (strlen($f['u_nick']) < 4)) {
				$f['u_nick'] = $f['u_name'];
			}
			
			// Im Nick alle Sonderzeichen entfernen
			if (isset($f['u_nick']))
				$f['u_nick'] = coreCheckName($f['u_nick'], $check_name);
			
			// Tags aus dem Usernamen raus
			if (isset($f['u_name']))
				$f['u_name'] = strip_tags($f['u_name']);
			
			// Eingaben prüfen
			
			if ($los == $t['neu22']) {
				$ok = "1";
				if (strlen($f['u_name']) == 0) {
					echo $t['neu1'];
					$ok = "0";
				} elseif (strlen($f['u_name']) < 4) {
					echo $t['neu2'];
					$ok = "0";
				} elseif (strlen($f['u_name']) > 20) {
					echo $t['neu3'];
					$ok = "0";
				}
				
				$pos = strpos($f['u_nick'], "+");
				if ($pos === false)
					$pos = -1;
				if ($pos >= 0) {
					echo $t['neu44'];
					$ok = "0";
				}
				
				if (strlen($f['u_nick']) > 20 || strlen($f['u_nick']) < 4) {
					echo str_replace("%zeichen%", $check_name, $t['neu4']);
					$ok = "0";
				}
				if (preg_match("/^" . $t['login13'] . "/i", $f['u_nick'])) {
					echo str_replace("%gast%", $t['login13'], $t['neu32']);
					$ok = "0";
				}
				if (strlen($f['u_passwort']) < 4) {
					echo $t['neu5'];
					$ok = "0";
				}
				if ($f['u_passwort'] != $u_passwort2) {
					echo $t['neu6'];
					$f['u_passwort'] = "";
					$u_passwort2 = "";
					$ok = "0";
				}
				
				if (strlen($f['u_email']) != 0
					&& !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
						$f['u_email'])) {
					echo $t['neu8'];
					$ok = "0";
				}
				if (strlen($f['u_adminemail']) == 0
					|| !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
						$f['u_adminemail'])) {
					echo $t['neu7'];
					$ok = "0";
				}
				
				// Gibts den Usernamen schon?
				$query = "SELECT `u_id` FROM `user` WHERE `u_nick` = '" . mysqli_real_escape_string($mysqli_link, $f['u_nick']) . "'";
				
				$result = mysqli_query($mysqli_link, $query);
				$rows = mysqli_num_rows($result);
				
				if ($rows != 0) {
					echo $t['neu9'];
					$ok = "0";
				}
				mysqli_free_result($result);
				
			} else {
				$ok = "0";
			}
			
			if (!isset($f['u_name']))
				$f['u_name'] = "";
			if (!isset($f['u_nick']))
				$f['u_nick'] = "";
			if (!isset($f['u_passwort']))
				$f['u_passwort'] = "";
			if (!isset($f['u_email']))
				$f['u_email'] = "";
			if (!isset($f['u_url']))
				$f['u_url'] = "";
			
			$text = "<table><tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu10']
				. "</td>" . "<td>" . $f1
				. "<input type=\"TEXT\" name=\"f[u_name]\" value=\"$f[u_name]\" size=\"40\">"
				. $f2 . "</td>" . "<td><b>*</b>&nbsp;" . $f1 . $t['neu11']
				. $f2 . "</td></tr>" . "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu12']
				. "</td>" . "<td>" . $f1
				. "<input type=\"TEXT\" name=\"f[u_nick]\" value=\"$f[u_nick]\" size=\"40\">"
				. $f2 . "</td>" . "<td>" . $f1 . $t['neu13'] . $f2
				. "</td></tr>" . "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu14']
				. "</td>" . "<td>" . $f1
				. "<input type=\"PASSWORD\" name=\"f[u_passwort]\" value=\"$f[u_passwort]\" size=\"40\">"
				. $f2 . "</td>" . "<td><b>*</b></td></tr>"
				. "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu15'] . "</td>"
				. "<td>"
				. "<input type=\"PASSWORD\" name=\"u_passwort2\" value=\"$f[u_passwort]\" size=\"40\">"
				. $f2 . "</td>" . "<td><b>*</b>" . $f1 . $t['neu16'] . $f2
				. "</td></tr>";
			if (!isset($ro) || $ro == "") {
				$text .= "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu17'] . "</td>"
					. "<td>" . $f1
					. "<input type=\"TEXT\" name=\"f[u_adminemail]\" value=\""
					. (isset($f[u_adminemail]) ? $f[u_adminemail] : "")
					. "\" size=\"40\">" . $f2 . "</td>" . "<td><b>*</b>&nbsp;"
					. $f1 . $t['neu18'] . $f2 . "</td></tr>";
			} else {
				$text .= "<tr><td colsPAN=3>"
					. "<input type=\"hidden\" name=\"f[u_adminemail]\" value=\"$f[u_adminemail]\"></td></tr>";
			}
			$text .= "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu19'] . "</td>\n"
				. "<td>" . $f1
				. "<input type=\"TEXT\" name=\"f[u_email]\" value=\"$f[u_email]\" size=\"40\">"
				. $f2 . "</td>\n" . "<td>" . $f1 . $t['neu20'] . $f2
				. "</td></tr>\n" . "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu21']
				. "</td>\n" . "<td>" . $f1
				. "<input type=\"TEXT\" name=\"f[u_url]\" value=\"$f[u_url]\" size=\"40\">"
				. $f2 . "</td>\n" . "<td>" . $f1
				. "<input type=\"hidden\" name=\"aktion\" value=\"neu\">\n"
				. "<input type=\"hidden\" name=\"hash\" value=\""
				. (isset($hash) ? $hash : "") . "\">\n";
			if (!empty($backarray))
				$text .= "<input type=\"hidden\" name=\"backarray\" value=\""
					. urlencode(urldecode($backarray)) . "\">";
			$text .= "<b><input type=\"submit\" name=\"los\" value=\""
				. $t['neu22'] . "\"></b>" . $f2 . "</td>\n" . "</tr></table>";
			if ($los != $t['neu22'])
				echo $t['neu23'];
			
			// Prüfe ob Mailadresse schon zu oft registriert, durch ZURÜCK Button bei der 1. registrierung
			if ($ok) {
				if ($begrenzung_anmeld_pro_mailadr > 0) {
					$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
					$result = mysqli_query($mysqli_link, $query);
					$num = mysqli_num_rows($result);
					if ($num >= $begrenzung_anmeld_pro_mailadr) {
						$ok = 0;
						echo str_replace("%anzahl%",
							$begrenzung_anmeld_pro_mailadr, $t['neu55'])
							. "<br><br>";
						zeige_fuss();
						break;
					}
				}
			}
			
			if ($ok) {
				echo ($t['neu24']);
			}
			
			if ((!$ok && $los == $t['neu22']) || ($los != $t['neu22'])) {
				?>
				<form action="<?php echo $chat_file; ?>" name="form1" method="POST">
					<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
					<?php
					$titel = $ft0 . $t['neu31'] . $ft1;
					show_box($titel, $text, "", "");
					?>
				</form>
				<?php
			}
			
			if ($ok && $los == $t['neu22']) {
				// Daten in DB als User eintragen
				
				if (!empty($backarray)) {
					// fix für magic-quotes...
					$backarray = urldecode($backarray);
					$backarray = str_replace('\"', '"', $backarray);
					$backarray = unserialize($backarray);
					$text = $f1 . $backarray['text'] . $f2 . "\n"
						. "<form action=\"" . $backarray['url']
						. "\" name=\"login\" method=\"POST\">\n";
					
					if (is_array($backarray['parameter'])) {
						for ($i = 0; $i < count($backarray['parameter']); $i++) {
							$text .= "<input type=\"hidden\" "
								. $backarray['parameter'][$i] . ">\n";
						}
					}
					$text .= "<b><input type=\"submit\" value=\""
						. $backarray['submittext'] . "\"></b>\n</form>\n";
					$text = str_replace("{passwort}", $f['u_passwort'], $text);
					$text = str_replace("{u_nick}", $f['u_nick'], $text);
					
					$titel = $backarray['titel'];
				} else {
					$text = $t['neu25'] . "<table><tr><td style=\"text-align: right; font-weight:bold;\">"
						. $t['neu26'] . "</td>" . "<td>" . $f1
						. $f['u_name'] . $f2 . "</td></tr>\n"
						. "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu27'] . "</td>"
						. "<td>" . $f1 . $f['u_nick'] . $f2
						. "</td></tr></table>\n" . $t['neu28']
						. "<form action=\"$chat_file\" name=\"login\" method=\"POST\">\n"
						. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n"
						. "<input type=\"hidden\" name=\"login\" value=\"$f[u_nick]\">\n"
						. "<input type=\"hidden\" name=\"passwort\" value=\"$f[u_passwort]\">\n"
						. "<input type=\"hidden\" name=\"aktion\" value=\"login\">\n"
						. "<b><input type=\"submit\" value=\"" . $t['neu29']
						. "\"></b>\n"
						. "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n// end hiding -->\n</script>\n"
						. "</form>\n";
					$titel = $t['neu30'];
				}
				show_box($titel, $text, "", "");
				echo "<br>";
				
				// Homepage muss http:// enthalten
				if (!preg_match("|^(http://)|i", $f['u_url'])
					&& strlen($f['u_url']) > 0) {
					$f['u_url'] = "http://" . $f['u_url'];
				}
				
				$f['u_level'] = "U";
				$f['u_loginfehler'] = "";

				$u_id = schreibe_db("user", $f, "", "u_id");
				$result = mysqli_query($mysqli_link, "UPDATE user SET u_neu=DATE_FORMAT(now(),\"%Y%m%d%H%i%s\") WHERE u_id=$u_id");
				
				if ($pruefe_email == "1") {
					$query = "DELETE FROM mail_check WHERE email = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
					$result = mysqli_query($mysqli_link, $query);
				}
				
			}
			
			zeige_fuss();
			
			break;
		
		case "relogin":
		// Login aus Forum in Chat; Userdaten setzen
			id_lese($id);
			$hash_id = $id;
			
			//system_msg("",0,$u_id,"","DEBUG: $neuer_raum ");
			
			// Chat betreten
			$back = betrete_chat($o_id, $u_id, $u_nick, $u_level, $neuer_raum,
				$o_js, $u_backup);
			
			// Frame-Einstellungen für Browser definieren
			$user_agent = strtolower($HTTP_USER_AGENT);
			if (preg_match("/linux/", $user_agent)) {
				$frame_type = "linux";
			} elseif (preg_match("/solaris/", $user_agent)) {
				$frame_type = "solaris";
			} elseif (preg_match("/msie/", $user_agent)) {
				$frame_type = "ie";
			} elseif (preg_match('#mozilla/4\.7#i', $user_agent)) {
				$frame_type = "nswin";
			} else {
				$frame_type = "def";
			}
			
			// Obersten Frame definieren
			if (!isset($frame_online))
				$frame_online = "";
			if (strlen($frame_online) == 0) {
				$frame_online = "frame_online.php";
			}
			if ($u_level == "M")
				$frame_size[$frame_type][interaktiv] = $moderationsgroesse;
			
			// Falls user eigene Einstellungen für das Frameset hat -> überschreiben
			$sql = "SELECT `u_frames` FROM `user` WHERE `u_id` = $u_id";
			$result = mysqli_query($mysqli_link, $sql);
			if ($result && mysqli_num_rows($result) > 0) {
				$u_frames = mysqli_result($result, 0, "u_frames");
			}
			if ($u_frames) {
				$u_frames = unserialize($u_frames);
				if (is_array($u_frames)) {
					foreach ($u_frames as $key => $val) {
						if ($val)
							$frame_size[$frame_type][$key] = $val;
					}
				}
			}
			mysqli_free_result($result);
			
			// Frameset aufbauen
			?>
			<frameset rows="<?php echo $frame_online_size;?>,*,<?php echo $frame_size[$frame_type]['eingabe']; ?>,<?php echo $frame_size[$frame_type]['interaktiv']; ?>,1" border="0" frameborder="0" framespacing="0">
				<frame src="<?php echo $frame_online;?>?http_host=<?php echo $http_host; ?>" name="frame_online" marginwidth="0" marginheight="0" scrolling="no">
				<frameset cols="*,<?php echo $frame_size[$frame_type]['chatuserliste'];?>" border="0" frameborder="0" framespacing="0">
					<frame src="chat.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>&back=<?php echo $back; ?>" name="chat" marginwidth="4" marginheight="0">
					<frame src="user.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>&aktion=chatuserliste" marginwidth="4" marginheight="0" name="userliste">
				</frameset>
				<frame src="eingabe.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="eingabe" marginwidth="0" marginheight="0" scrolling="no">
				<?php
				if ($u_level == "M") {
					?>
					<frameset cols="*,220" border="0" frameborder="0" framespacing="0">
					<frame src="moderator.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="moderator" marginwidth="0" marginheight="0" scrolling="auto">
					<frame src="interaktiv.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
					</frameset>
					<?php
				} else {
					?>
					<frame src="interaktiv.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="interaktiv" marginwidth="0" marginheight="0" scrolling="no">
					<?php
				}
				?>
				<frame src="schreibe.php?http_host=<?php echo $http_host; ?>&id=<?php echo $hash_id; ?>" name="schreibe" marginwidth="0" marginheight="0" scrolling="no">
			</frameset>
			<?php
			echo "<noframes>\n"
				. "<!-- Browser/OS  $frame_type -->\n" . "<body "
				. $t['login6'];
			
			break;
		
		default:
			// Homepage des Chats ausgeben
			
			// Kopf
			// Header ausgeben
			?>
			<?php
			zeige_header_ende();
			?>
			<body>
			<?php
			zeige_kopf();
			echo $willkommen;
			
			if ($zusatznachricht) {
				?>
				<p><span style="font-size: x-large;"><?php echo $zusatznachricht; ?></span></p>
				<?php
			}

			// Box für Login
			if ($frameset_bleibt_stehen) {
				?>
				<form action="<?php echo $chat_file; ?>" name="form1" method="POST">
				<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
				<?php
			} else {
				?>
				<form action="<?php echo $chat_file; ?>" target="topframe" name="form1" method="POST">
				<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
				<?php
			}
			
			if ($gast_login && $communityfeatures && $forumfeatures) {
				if (!isset($id))
					$id = "";
				$titel = $login_titel . "[<a href=\"" . $_SERVER['PHP_SELF']
					. "?http_host=$http_host&id=$id&aktion=login&"
					. $t['login10'] . "=los&eintritt=forum\">" . $ft0
					. $t['login23'] . $ft1 . "</A>]";
			} else {
				$titel = $login_titel;
			}
			
			if ($einstellungen_aendern) {
				$titel .= "[<a href=\"" . $_SERVER['PHP_SELF']
					. "?http_host=$http_host&aktion=passwort_neu\">" . $ft0
					. $t['login27'] . $ft1 . "</A>]";
			}
			
			// Box und Disclaimer ausgeben
			if (!isset($keineloginbox))
				show_box($titel, $logintext, "", "100%");
			echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
				. "// end hiding -->\n</script>\n";
			echo "<div style=\"text-align: center;\">" . $f3
				. str_replace("%farbe_text%", $farbe_text, $disclaimer) . $f4
				. "</div>\n</form><br>";
			
			if (!isset($beichtstuhl) || !$beichtstuhl) {
				
				// Wie viele User sind in der DB?
				$query = "SELECT COUNT(u_id) FROM `user` WHERE `u_level` IN ('A','C','G','M','S','U')";
				$result = mysqli_query($mysqli_link, $query);
				$rows = mysqli_num_rows($result);
				if ($result) {
					$useranzahl = mysqli_result($result, 0, 0);
					mysqli_free_result($result);
				}
				
				// User online und Räume bestimmen -> merken
				$query = "SELECT o_who,o_name,o_level,r_name,r_status1,r_status2, "
					. "r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' as lobby "
					. "FROM online left join raum on o_raum=r_id  "
					. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
					. "ORDER BY lobby desc,r_name,o_who,o_name ";
				$result2 = mysqli_query($mysqli_link, $query);
				if ($result2)
					$onlineanzahl = mysqli_num_rows($result2);
				// Anzahl der angemeldeten User ausgeben
				if ($onlineanzahl > 1 && $useranzahl > 1) {
					$text = str_replace("%onlineanzahl%", $onlineanzahl,
						$t['default2'])
						. str_replace("%useranzahl%", $useranzahl,
							$t['default3']);
					
					// Anzahl der Beiträge im Forum ausgeben
					if ($communityfeatures && $forumfeatures) {
						// Anzahl Themen
						$query = "select count(th_id) from thema";
						$result = mysqli_query($mysqli_link, $query);
						if ($result AND mysqli_num_rows($result) > 0) {
							$themen = mysqli_result($result, 0, 0);
							mysqli_free_result($result);
						}
						
						// Dummy Themen abziehen
						$query = "select count(th_id) from thema where th_name = 'dummy-thema'";
						$result = mysqli_query($mysqli_link, $query);
						if ($result AND mysqli_num_rows($result) > 0) {
							$themen = $themen - mysqli_result($result, 0, 0);
							mysqli_free_result($result);
						}
						
						// Anzahl Postings 
						$query = "select count(po_id) from posting";
						$result = mysqli_query($mysqli_link, $query);
						if ($result AND mysqli_num_rows($result) > 0) {
							$beitraege = mysqli_result($result, 0, 0);
							mysqli_free_result($result);
						}
						if ($beitraege && $themen)
							$text .= str_replace("%themen%", $themen,
								str_replace("%beitraege%", $beitraege,
									$t['default8']));
					}
					
					show_box2(
						str_replace("%chat%", $chat, $ft0 . $t['default5'] . $ft1), $text,
						false);
					echo "<IMG src=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n";
				}
				
				if (!isset($unterdruecke_raeume))
					$unterdruecke_raeume = 0;
				if (!$unterdruecke_raeume && $abweisen == false) {
					
					// Wer ist online? Boxen mit Usern erzeugen, Topic ist Raumname
					if ($onlineanzahl)
						if (!isset($keineloginbox))
							show_who_is_online($result2);
				}
				// result wird in "show_who_is_online" freigegeben
				// mysqli_free_result($result2);
			}
			
			// Fuss
			zeige_fuss();
	}
	?>
	</body>
	<?php
}
?>
</html>