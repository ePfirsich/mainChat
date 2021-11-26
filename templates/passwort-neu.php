<?php
// Gibt die Kopfzeile im Login aus
zeige_kopfzeile_login();

unset($richtig);
unset($u_id);

$richtig = 0;
$fehlermeldung = "";

if (isset($email) && isset($nickname) && isset($hash)) {
	$nickname = mysqli_real_escape_string($mysqli_link, coreCheckName($nickname, $check_name));
	$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
	$query = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user WHERE u_nick = '$nickname' AND u_level != 'G' AND u_adminemail = '$email' LIMIT 1";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$hash2 = md5($a['u_id'] . $a['u_login'] . $a['u_nick'] . $a['u_passwort'] . $a['u_adminemail'] . $a['u_punkte_jahr']);
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
} else if (isset($email) || isset($nickname)) {
	
	if( isset($nickname) && $nickname != "" ) {
		$nickname = mysqli_real_escape_string($mysqli_link, coreCheckName($nickname, $check_name));
	} else {
		$nickname = "";
	}
	
	if( isset($email) && $email != "" ) {
		$email = mysqli_real_escape_string($mysqli_link, urldecode($email));
		if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", mysqli_real_escape_string($mysqli_link, $email))) {
			$fehlermeldung .= $t['pwneu5'] . '<br>';
		}
	} else {
		$email = "";
	}
	
	if( $email == "" && $nickname == "" ) {
		$fehlermeldung .= $t['pwneu17'] . '<br>';
	}
	
	if ($fehlermeldung == "") {
		if($nickname != "") {
			$query = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user " . "WHERE u_nick = '$nickname' LIMIT 2";
		} else {
			$query1 = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user " . "WHERE u_adminemail = '$email' LIMIT 2";
		}
		//$query = "SELECT u_id, u_login, u_nick, u_passwort, u_adminemail, u_punkte_jahr FROM user " . "WHERE u_nick = '$nickname' OR u_adminemail = '$email' LIMIT 2";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
			$hash = md5(
				$a['u_id'] . $a['u_login'] . $a['u_nick']
				. $a['u_passwort']
				. $a['u_adminemail'] . $a['u_punkte_jahr']);
			
			$email = urlencode($a['u_adminemail']);
			$link = $chat_url . "/index.php?aktion=passwort_neu&email=" . $email . "&nickname=" . $nickname . "&hash=" . $hash;
			
			$text2 = str_replace("%link%", $link, $t['pwneu9']);
			$text2 = str_replace("%hash%", $hash, $text2);
			$text2 = str_replace("%nickname%", $a['u_nick'], $text2);
			$email = urldecode($a['u_adminemail']);
			$text2 = str_replace("%email%", $email, $text2);
			
			$mailbetreff = $t['pwneu8'];
			$mailempfaenger = $email;
			$header = "\n" . "X-MC-IP: " . $_SERVER["REMOTE_ADDR"] . "\n" . "X-MC-TS: " . time();
			$header .= "Mime-Version: 1.0\r\n";
			$header .= "Content-type: text/plain; charset=utf-8";
			
			// E-Mail versenden
			if($smtp_on) {
				mailsmtp($mailempfaenger, $mailbetreff, $text2, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
			} else {
				// Der PHP-Vessand benötigt \n und nicht <br>
				$text2 = str_replace("<br>", "\n", $text2);
				mail($mailempfaenger, $mailbetreff, $text2, "From: $webmaster ($chat)" . $header);
			}
			
			echo $t['pwneu7'];
			unset($hash);
		} else {
			if($nickname != "") {
				$fehlermeldung .= $t['pwneu6'] . '<br>';
			} else {
				$fehlermeldung .= $t['pwneu18'] . '<br>';
			}
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
		echo "<p style=\"color:#ff0000; font-weight:bold;\">$fehlermeldung</p>\n";
	}
	$box = $t['pwneu16'];
	$text = '';
	$text .= "<form action=\"index.php\">\n";
	$text .= "<table>\n";
	if ( (isset($email) || isset($nickname)) && $email <> "" && $nickname <> "" && (isset($hash) || $fehlermeldung == "") ) {
		if (!isset($hash)) {
			$hash = "";
		}
		$text .="<tr>";
		$text .="<td style=\"font-weight:bold;\">$t[pwneu2]</td>";
		$text .="<td><input type=hidden name=\"nickname\" width=\"50\" value=\"$nickname\">$nickname</td>";
		$text .="</tr>";
		$text .="<tr>";
		$text .="<td style=\"font-weight:bold;\">$t[pwneu3]</td>";
		$text .="<td style=\"font-weight:bold;\"><input type=\"hidden\" name=\"email\" width=\"50\" value=\"$email\">$email</td>";
		$text .="</tr>";
		$text .="<tr>";
		$text .="<td style=\"font-weight:bold;\">$t[pwneu10]</td>";
		$text .="<td style=\"font-weight:bold;\"><input name=\"hash\" width=\"50\" value=\"$hash\"></td>";
		$text .="</tr>";
	} else {
		if (!isset($nickname)) {
			$nickname = "";
		}
		if (!isset($email)) {
			$email = "";
		}
		$text .="<tr>";
		$text .="<td style=\"font-weight:bold;\">$t[pwneu2]</td>";
		$text .="<td style=\"font-weight:bold;\"><input name=\"nickname\" width=\"50\" value=\"$nickname\"></td>";
		$text .="</tr>";
		$text .="<tr>";
		$text .="<td style=\"font-weight:bold;\">$t[pwneu3]</td>";
		$text .="<td style=\"font-weight:bold;\"><input name=\"email\" width=\"50\" value=\"$email\"></td>";
		$text .="</tr>";
	}
	$text .="<tr>";
	$text .="<td colspan=\"2\" style=\"font-weight:bold;\"><input type=\"hidden\" name=\"aktion\" value=\"passwort_neu\"><input type=\"submit\" value=\"Absenden\"></td>";
	$text .="</tr>";
	$text .= "</table>";
	$text .= "</form>";
	
	zeige_tabelle_variable_breite($box,$text);
	
} else if ($richtig && $u_id) {
	$query = "SELECT `u_adminemail`, `u_nick` FROM `user` WHERE `u_id` = '$u_id' LIMIT 2";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		unset($f);
		$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$pwdneu = genpassword(8);
		$f['u_passwort'] = $pwdneu;
		$f['u_id'] = $u_id;
		$text = str_replace("%passwort%", $f['u_passwort'], $t['pwneu15']);
		$text = str_replace("%nickname%", $a['u_nick'], $text);
		
		// E-Mail versenden
		if($smtp_on) {
			$ok = mailsmtp($a['u_adminemail'], $t['pwneu14'], $text, $smtp_sender, $chat, $smtp_host, $smtp_port, $smtp_username, $smtp_password, $smtp_encryption, $smtp_auth, $smtp_autoTLS);
		} else {
			// Der PHP-Vessand benötigt \n und nicht <br>
			$t['pwneu14'] = str_replace("<br>", "\n", $t['pwneu14']);
			$ok = mail($a['u_adminemail'], $t['pwneu14'], $text, "From: $webmaster ($chat)");
		}
		
		if ($ok) {
			echo $t['pwneu12'];
			schreibe_db("user", $f, $f['u_id'], "u_id");
		} else {
			echo $t['pwneu13'];
		}
	}
}

zeige_fuss();
?>