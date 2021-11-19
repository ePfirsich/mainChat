<?php
// Gibt die Kopfzeile im Login aus
zeige_kopfzeile_login();

// Im Benutzername alle Sonderzeichen entfernen
if (isset($f['u_nick'])) {
	$f['u_nick'] = coreCheckName($f['u_nick'], $check_name);
}

// Eingaben prüfen

if ($los == $t['neu22']) {
	$ok = "1";
	$pos = strpos($f['u_nick'], "+");
	if ($pos === false) {
		$pos = -1;
	}
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
	
	if (strlen($f['u_email']) != 0 && !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", $f['u_email'])) {
		echo $t['neu8'];
		$ok = "0";
	}
	if (strlen($f['u_adminemail']) == 0 || !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", $f['u_adminemail'])) {
		echo $t['neu7'];
		$ok = "0";
	}
	
	// Gibt es den Benutzernamen schon?
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

if (!isset($f['u_nick'])) {
	$f['u_nick'] = "";
}
if (!isset($f['u_passwort'])) {
	$f['u_passwort'] = "";
}
if (!isset($f['u_email'])) {
	$f['u_email'] = "";
}
if (!isset($f['u_url'])) {
	$f['u_url'] = "";
}

$text = "<table><tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu12']
	. "</td>" . "<td>" . $f1
	. "<input type=\"text\" name=\"f[u_nick]\" value=\"$f[u_nick]\" size=\"40\">"
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
		. "<input type=\"text\" name=\"f[u_adminemail]\" value=\""
		. (isset($f[u_adminemail]) ? $f[u_adminemail] : "")
		. "\" size=\"40\">" . $f2 . "</td>" . "<td><b>*</b>&nbsp;"
		. $f1 . $t['neu18'] . $f2 . "</td></tr>";
} else {
	$text .= "<tr><td colspan=3>" . "<input type=\"hidden\" name=\"f[u_adminemail]\" value=\"$f[u_adminemail]\"></td></tr>";
}
$text .= "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu19'] . "</td>\n"
	. "<td>" . $f1
	. "<input type=\"text\" name=\"f[u_email]\" value=\"$f[u_email]\" size=\"40\">"
	. $f2 . "</td>\n" . "<td>" . $f1 . $t['neu20'] . $f2
	. "</td></tr>\n" . "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu21']
	. "</td>\n" . "<td>" . $f1
	. "<input type=\"text\" name=\"f[u_url]\" value=\"$f[u_url]\" size=\"40\">"
	. $f2 . "</td>\n" . "<td>" . $f1
	. "<input type=\"hidden\" name=\"aktion\" value=\"neu\">\n"
		. "<input type=\"hidden\" name=\"hash\" value=\""
		. (isset($hash) ? $hash : "") . "\">\n";
$text .= "<b><input type=\"submit\" name=\"los\" value=\"" . $t['neu22'] . "\"></b>" . $f2 . "</td>\n" . "</tr></table>";
if ($los != $t['neu22']) {
	echo $t['neu23'];
}

// Prüfe ob Mailadresse schon zu oft registriert, durch ZURÜCK Button bei der 1. registrierung
if ($ok) {
	$query = "SELECT `u_id` FROM `user` WHERE `u_adminemail` = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
	$result = mysqli_query($mysqli_link, $query);
	$num = mysqli_num_rows($result);
	// Jede E-Mail darf nur einmal zur Registrierung verwendet werden
	if ($num > 0) {
		$ok = 0;
		echo $t['neu55'] . "<br><br>";
		zeige_fuss();
	}
}

if ($ok) {
	echo ($t['neu24']);
}

if ((!$ok && $los == $t['neu22']) || ($los != $t['neu22'])) {
	?>
	<form action="index.php" name="form1" method="post">
	<?php
	$titel = $t['neu31'];
	zeige_tabelle_variable_breite($titel, $text, "");
	?>
	</form>
	<?php
}

if ($ok && $los == $t['neu22']) {
	// Daten in DB als Benutzer eintragen
	$text = $t['neu25'] . "<table><tr><td style=\"text-align: right; font-weight:bold;\">"
		. "<tr><td style=\"text-align: right; font-weight:bold;\">" . $t['neu12'] . "</td>"
		. "<td>" . $f1 . $f['u_nick'] . $f2
		. "</td></tr></table>\n" . $t['neu28']
		. "<form action=\"$chat_url\" name=\"login\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"login\" value=\"$f[u_nick]\">\n"
		. "<input type=\"hidden\" name=\"passwort\" value=\"$f[u_passwort]\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"login\">\n"
		. "<b><input type=\"submit\" value=\"" . $t['neu29']
		. "\"></b>\n"
		. "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n// end hiding -->\n</script>\n"
		. "</form>\n";
	$titel = $t['neu30'];
	
	zeige_tabelle_variable_breite($titel, $text, "");
	echo "<br>";
	
	// Homepage muss http:// enthalten
	if (!preg_match("|^(http://)|i", $f['u_url']) && strlen($f['u_url']) > 0) {
		$f['u_url'] = "http://" . $f['u_url'];
	}
	
	$f['u_level'] = "U";
	$f['u_loginfehler'] = "";
	
	$u_id = schreibe_db("user", $f, "", "u_id");
	$result = mysqli_query($mysqli_link, "UPDATE user SET u_neu=DATE_FORMAT(now(),\"%Y%m%d%H%i%s\") WHERE u_id=$u_id");
	
	// E-Mail überprüfen
	$query = "DELETE FROM mail_check WHERE email = '" . mysqli_real_escape_string($mysqli_link, $f['u_adminemail']) . "'";
	$result = mysqli_query($mysqli_link, $query);
	
}
zeige_fuss();
?>