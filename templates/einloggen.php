<?php
// Sequence für online- und chat-Tabelle erzeugen
erzeuge_sequence("online", "o_id");
erzeuge_sequence("chat", "c_id");

// Weiter mit login
if (!isset($passwort) || $passwort == "") {
	// Login als Gast
	
	// Falls Gast-Login erlaubt ist:
	if ($gast_login) {
		// Prüfen, ob von der IP und dem Benutzer-Agent schon ein Gast online ist und ggf abweisen
		$query4711 = "SELECT o_id FROM online "
			. "WHERE o_browser='" . $_SERVER["HTTP_USER_AGENT"]
			. "' " . "AND o_ip='" . $_SERVER["REMOTE_ADDR"] . "' "
			. "AND o_level='G' "
			. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
		$result = mysqli_query($mysqli_link, $query4711);
		if ($result) {
			$rows = mysqli_num_rows($result);
		}
		mysqli_free_result($result);
	}
	
	// Login abweisen, falls mehr als ein Gast online ist oder Gäste gesperrt sind
	if (!$gast_login || ($rows > 1 && !$gast_login_viele) || $temp_gast_sperre) {
		// Gäste sind gesperrt
		
		// Header ausgeben
		zeige_header_ende();
		echo "<body>";
		zeige_kopf();
		
		// Passenden Fehlertext ausgeben
		if (!$gast_login) {
			echo $t['login16'];
		} else {
			echo $t['login15'];
		}
		
		// Box für Login
		?>
		<form action="index.php" target="_top" name="form1" method="post">
		<?php
		
		// Disclaimer ausgeben
		zeige_tabelle_variable_breite($login_titel, $logintext, "100%");
		echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n";
		echo "</form>";
		zeige_fuss();
		exit;
	}
	// Login als Gast
	
	// Im Benutzername alle Sonderzeichen entfernen, Länge prüfen
	if (!isset($login)) {
		$login = "";
	}
	$login = coreCheckName($login, $check_name);
	
	if (strlen($login) < 4 || strlen($login) > 20) {
		$login = "";
	}
	
	// Falls kein Benutzername übergeben, Nick finden
	if (strlen($login) == 0) {
		if ($gast_name_auto) {
			// Freien Benutzername bestimmen falls in der Config erlaubt
			$rows = 1;
			$i = 0;
			$anzahl = count($gast_name);
			while ($rows != 0 && $i < 100) {
				$login = $gast_name[mt_rand(1, $anzahl)];
				$query4711 = "SELECT u_id FROM user WHERE u_nick='$login' ";
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
	
	// Im Benutzername alle Sonderzeichen entfernen, vorsichtshalber nochmals prüfen
	$login = mysqli_real_escape_string($mysqli_link, coreCheckName($login, $check_name));
	
	// Benutzerdaten für Gast setzen
	$f['u_level'] = "G";
	$f['u_passwort'] = mt_rand(1, 10000);
	$f['u_nick'] = $login;
	$passwort = $f['u_passwort'];
	$f['u_loginfehler'] = '';
	
	// Prüfung, ob dieser Benutzer bereits existiert
	$query4711 = "SELECT `u_id` FROM `user` WHERE `u_nick`='$f[u_nick]'";
	$result = mysqli_query($mysqli_link, $query4711);
	
	if (mysqli_num_rows($result) == 0) {
		// Account in DB schreiben
		schreibe_db("user", $f, "", "u_id");
	}
}

// Login als registrierter Benutzer

// Testen, ob frühere Loginversuche fehlschlugen (nur Admins)
$query4711 = "SELECT u_id,u_nick,u_loginfehler,u_login FROM user "
	. "WHERE (u_nick = '$login') "
	. "AND (u_level='S' OR u_level='C') ";
$result = mysqli_query($mysqli_link, $query4711);
if ($result) {
	$rows = mysqli_num_rows($result);
}

// Voreinstellung: Weiter mit Login
$login_ok = true;

if ($result && $rows == 1) {
	$userdata = mysqli_fetch_object($result);
	if ($userdata->u_loginfehler) {
		// Nach zwei fehlgeschlagenen Logins und dem letzen Versuch keine 5 min vergangen 
		// -> Login sperren und merken
		$u_loginfehler = unserialize($userdata->u_loginfehler);
		$letzter_eintrag = end($u_loginfehler);
		if ((time() - $letzter_eintrag[login]) < 300 && count($u_loginfehler) > 9) {
			$login_ok = false;
		}
	}
}

// Passwort prüfen und Benutzerdaten lesen
$rows = 0;
$result = auth_user($login, $passwort);
if ($result) {
	$rows = mysqli_num_rows($result);
}

// Login fehlgeschlagen
if ($rows == 0 && isset($userdata) && is_array($userdata) && $userdata) {
	// Fehllogin bei Admin: falsches Passwort oder Benutzername -> max 100 Loginversuche in Benutzerdaten merken
	if ($userdata->u_loginfehler) {
		$u_loginfehler = unserialize($userdata->u_loginfehler);
	}
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
	// Login Ok, Benutzerdaten setzen
	$row = mysqli_fetch_object($result);
	$u_id = $row->u_id;
	$u_nick = $row->u_nick;
	$u_level = $row->u_level;
	$u_agb = $row->u_agb;
	$u_punkte_monat = $row->u_punkte_monat;
	$u_punkte_jahr = $row->u_punkte_jahr;
	$u_punkte_datum_monat = $row->u_punkte_datum_monat;
	$u_punkte_datum_jahr = $row->u_punkte_datum_jahr;
	$u_punkte_gesamt = $row->u_punkte_gesamt;
	$ip_historie = unserialize($row->u_ip_historie);
	$nick_historie = unserialize($row->u_nick_historie);
	
	$f['u_id'] = $u_id;
	schreibe_db("user", $f, $u_id, "u_id");
	
	// Benutzer online bestimmen
	if ($chat_max[$u_level] != 0) {
		$query = "SELECT count(o_id) FROM online "
			. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
		$result2 = mysqli_query($mysqli_link, $query);
		if ($result2) {
			$onlineanzahl = mysqli_result($result2, 0, 0);
		}
		mysqli_free_result($result2);
	}
	
	// Login erfolgreich ?
	if ($u_level == "Z") {
		// Benutzer gesperrt -> Fehlermeldung ausgeben
		
		// Header ausgeben
		zeige_header_ende();
		echo "<body>";
		zeige_kopf();
		
		echo str_replace("%u_nick%", $u_nick, $t['login5']);
		
		zeige_fuss();
		
	} else if (false && $HTTP_COOKIE_VARS[MAINCHAT2] != "on" && ($u_level == "C" || $u_level == "S")) {
		// Der Benutzer ein Admin und es sind cookies gesetzt -> Fehlermeldung ausgeben
		
		// Header ausgeben
		zeige_header_ende();
		echo "<body>";
		zeige_kopf();
		
		echo str_replace("%url%", $chat_url, $t['login25']);
		unset($u_nick);
		
		zeige_fuss();
		
	} else if ($chat_max[$u_level] != 0 && $onlineanzahl > $chat_max[$u_level]) {
		// Maximale Anzahl der Benutzer im Chat erreicht -> Fehlermeldung ausgeben
		
		// Header ausgeben
		zeige_header_ende();
		echo "<body>";
		zeige_kopf();
		
		$txt = str_replace("%online%", $onlineanzahl, $t['login24']);
		$txt = str_replace("%max%", $chat_max[$u_level], $txt);
		$txt = str_replace("%leveltxt%", $level[$u_level], $txt);
		$txt = str_replace("%zusatztext%", $chat_max[zusatztext], $txt);
		echo $txt;
		
		unset($u_nick);
		
		// Box für Login
		?>
		<form action="index.php" target="_top" name="form1" method="post">
		<?php
		
		$titel = $login_titel;
		$titel .= "[<a href=\"" . $_SERVER['PHP_SELF'] . "?aktion=passwort_neu\">" . $t['login27'] . "</a>]";
		
		// Box und Disclaimer ausgeben
		zeige_tabelle_variable_breite($titel, $logintext, "100%");
		echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n";
		echo "</form>";
		zeige_fuss();
	} else {
		$captcha_text1 = filter_input(INPUT_POST, 'captcha_text1', FILTER_SANITIZE_STRING);
		$captcha_text2 = filter_input(INPUT_POST, 'captcha_text2', FILTER_SANITIZE_STRING);
		$ergebnis = filter_input(INPUT_POST, 'ergebnis', FILTER_SANITIZE_STRING);
		
		if (($temp_gast_sperre) && ($u_level == 'G')) {
			$captcha_text1 = "999";
		} // abweisen, falls Gastsperre aktiv
		
		if ( isset($captcha_text1) && ($captcha_text1 == "")) {
			$captcha_text1 = "999";
		} // abweisen, falls leere eingabe
		
		// Prüfen, ob alles korrekt eingegeben wurde.
		if ( ($captcha_text == 1) && (isset($captcha_text2)) && ( $captcha_text2 != md5( $u_id . "+code+" . $captcha_text1 . "+" . date("Y-m-d h") ) )) {
			$los = "";
		}
		
		// muss Benutzer/Gast  noch Nutzungsbestimmungen bestätigen?
		if ($los != $t['login17'] && $u_agb != "Y") {
			// Nutzungsbestimmungen ausgeben
			
			// Header ausgeben
			zeige_header_ende();
			echo "<body>";
			zeige_kopf();
			
			// Box für Login
			?>
			<form action="index.php" target="_top" name="form1" method="post">
			<?php
			
			if (!isset($eintritt)) {
				$eintritt = RaumNameToRaumID($lobby);
			}
			
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
								
							echo $tzahl[$zahl1] . " " . $taufgabe[$aufgabe] . " " . $tzahl[$zahl2] . " &nbsp;&nbsp;&nbsp;&nbsp;";
							
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
		} else if ($los == $t['login17']) {
			// Nutzungsbestimmungen wurden bestätigt
			$u_agb = "Y";
		} else {
			$u_agb = "";
		}
		
		// Benutzer in Blacklist überprüfen
		$query2 = "SELECT f_text FROM blacklist WHERE f_blacklistid=$u_id";
		$result2 = mysqli_query($mysqli_link, $query2);
		if ($result2 AND mysqli_num_rows($result2) > 0) {
			$infotext = "Blacklist: " . mysqli_result($result2, 0, 0);
			$warnung = TRUE;
		}
		if ($result2) {
			mysqli_free_result($result2);
		}
		
		// Bei Login dieses Benutzers alle Admins (online, nicht Temp) warnen
		if ($warnung) {
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
				$txt = str_replace("%ip_adr%", $ip_adr, $t['default6']);
				$txt = str_replace("%ip_name%", $ip_name, $txt);
				$txt = str_replace("%is_infotext%", $infotext, $txt);
				while ($row2 = mysqli_fetch_object($result2)) {
					$ur1 = "inhalt.php?seite=benutzer&id=<ID>&aktion=benutzer_zeig&user=$u_id";
					$ah1 = "<a href=\"$ur1\" target=\"chat\">";
					$ah2 = "</a>";
					system_msg("", 0, $row2->o_user, $system_farbe, str_replace("%u_nick%", $ah1 . $u_nick . $ah2 . $raumname, $txt));
				}
			}
			mysqli_free_result($result2);
		}
		
		// Benutzer nicht gesperrt, weiter mit Login und Eintritt in ausgewählten Raum mit ID $eintritt
		
		// Hash-Wert ermitteln
		$hash_id = id_erzeuge($u_id);
		
		// $javascript wird als input type hidden übergeben...
		if (isset($javascript) && $javascript == "on") {
			$javascript = 1;
		} else {
			$javascript = 0;
		}
		
		// Login
		$o_id = login($u_id, $u_nick, $u_level, $hash_id, $javascript, $ip_historie, $u_agb, $u_punkte_monat, $u_punkte_jahr, $u_punkte_datum_monat, $u_punkte_datum_jahr, $u_punkte_gesamt);
		
		// Beichtstuhl-Special
		// Falls es Räume mit nur einem Admin gibt und niemand in der Lobby ist,
		// Eintritt in einen solchen Raum, ansonsten in Lobby
		if (isset($beichtstuhl) && $beichtstuhl) {
			$login_in_lobby = FALSE;
			
			// Prüfen, ob ein Benutzer in der Lobby ist
			$query2 = "SELECT o_id FROM raum,online WHERE o_raum=r_id AND r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' "
				. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
			$result2 = mysqli_query($mysqli_link, $query2);
			
			if ($result2 && mysqli_num_rows($result2) > 0) {
				// Mehr als 0 Benutzer in Lobby
				mysqli_free_result($result2);
				$login_in_lobby = TRUE;
			} else {
				// Prüfen, ob es Räume mit genau einem Admin gibt
				mysqli_free_result($result2);
				$query2 = "SELECT r_id,count(o_id) as anzahl, " . "COUNT(o_level='C') AS CADMIN, count(o_level='S') AS SADMIN "
					. "FROM raum,online WHERE o_raum=r_id AND r_name!='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' "
					. "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
					. "GROUP BY r_id HAVING anzahl=1 AND (CADMIN=1 OR SADMIN=1)";
				$result2 = mysqli_query($mysqli_link, $query2);
				$anzahl = mysqli_num_rows($result2);
				if ($result2 && $anzahl == 1) {
					$eintritt = mysqli_result($result2, 0, "r_id");
				} elseif ($result2 && $anzahl > 1) {
					$eintritt = mysqli_result($result2, mt_rand(0, $anzahl - 1), "r_id");
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
			
			// Bei Login dieses Benutzers alle Admins (online, nicht Temp) informieren
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
				$txt = str_replace("%raumname%", $raumname, $t['default7']);
				while ($row2 = mysqli_fetch_object($result2)) {
					$ur1 = "inhalt.php?seite=benutzer&id=<ID>&aktion=benutzer_zeig&user=$u_id";
					$ah1 = "<a href=\"$ur1\" target=\"chat\">";
					$ah2 = "</a>";
					system_msg("", 0, $row2->o_user, $system_farbe, str_replace("%u_nick%", $ah1 . $u_nick . $ah2, $txt));
				}
			}
			mysqli_free_result($result2);
			
		}
		
		zeige_header_ende();
		
		if ($eintritt == "forum") {
			// Login ins Forum
			betrete_forum($o_id, $u_id, $u_nick, $u_level);
			
			require_once("functions/functions-frameset.php");
			
			frameset_forum($hash_id);
			
		} else {
			// Chat betreten
			betrete_chat($o_id, $u_id, $u_nick, $u_level, $eintritt, $javascript);
			$back = 1;
			
			require_once("functions/functions-frameset.php");
			
			frameset_chat($hash_id);
		}
		?>
		<body>
		<?php
	}
} elseif (!$login_ok) {
	if ($rows == 0) {
		// Zu viele fehlgeschlagenen Logins, aktueller Login ist
		// wieder fehlgeschlagen, daher Mail an Betreiber verschicken
		if ($userdata->u_loginfehler) {
			$u_loginfehler = unserialize($userdata->u_loginfehler);
		}
		$betreff = str_replace("%login%", $login, $t['login20']);
		$text = str_replace("%login%", $login, $t['login21']) . "\n";
		foreach ($u_loginfehler as $key => $val) {
			$text .= "[" . substr("00" . $key, -3) . "] " . date("M d Y H:i:s", $val[login]) . " \tIP: $val[ip] \tPW: $val[pw]\n";
		}
		$text .= "\n-- \n   $chat (".$chat_url . $_SERVER['PHP_SELF'] . " | " . $chat_url . ")\n";
		
		// E-Mail versenden
		if($smtp_on) {
			mailsmtp($webmaster, $betreff, $text, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
		} else {
			mail($webmaster, $betreff, $text, "From: $hackmail\nReply-To: $hackmail\nCC: $hackmail\n");
		}
	}
	
	// Fehlermeldung ausgeben
	
	// Header ausgeben
	zeige_header_ende();
	echo "<body>";
	zeige_kopf();
	unset($u_nick);
	echo "<div style=\"text-align: center;\"><b>" . str_replace("%login%", $login, $t['login20']) . "</b></div>";
	zeige_fuss();
} else {
	// Login fehlgeschlagen
	
	// Header ausgeben
	zeige_header_ende();
	echo "<body>";
	zeige_kopf();
	
	unset($u_nick);
	
	if (strlen($passwort) == 0) {
		// Kein Passwort eingegeben oder der Benutzername exitiert bereits
		echo $t['login19'];
	} else {
		// Falsches Passwort oder Benutzername
		echo $t['login7'];
	}
	
	// Box für Login
	?>
	<form action="index.php" target="_top" name="form1" method="post">
	<?php
	
	$titel = $login_titel;
	$titel .= "[<a href=\"" . $_SERVER['PHP_SELF'] . "?aktion=passwort_neu\">" . $t['login27'] . "</a>]";
	
	// Box und Disclaimer ausgeben
	zeige_tabelle_variable_breite($titel, $logintext, "100%");
	echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n" . "// end hiding -->\n</script>\n";
	echo "</form>";
	zeige_fuss();
	
}
?>