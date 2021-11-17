<?php

function profil_editor($u_id, $u_nick, $f) {
	// Editor zur Änderung oder zur Neuanlage eines Profils
	// $u_id=Benutzer-ID
	// $u_nick=Benutzername
	// $f=Array der Profileinstellungen
	global $mysqli_link, $t, $id, $f1, $f2;
	
	$eingabe_breite = 45;
	
	// Voreinstellungen
	if (!isset($f['ui_land']))
		$f['ui_land'] = "Deutschland";
	if (!isset($f['ui_strasse']))
		$f['ui_strasse'] = "";
	if (!isset($f['ui_plz']))
		$f['ui_plz'] = "";
	if (!isset($f['ui_ort']))
		$f['ui_ort'] = "";
	if (!isset($f['ui_tel']))
		$f['ui_tel'] = "";
	if (!isset($f['ui_handy']))
		$f['ui_handy'] = "";
	if (!isset($f['ui_fax']))
		$f['ui_fax'] = "";
	if (!isset($f['ui_icq']))
		$f['ui_icq'] = "";
	if (!isset($f['ui_geburt']))
		$f['ui_geburt'] = "";
	if (!isset($f['ui_beruf']))
		$f['ui_beruf'] = "";
	if (!isset($f['ui_hobby']))
		$f['ui_hobby'] = "";
	if (!isset($f['ui_id']))
		$f['ui_id'] = "";
	if (!isset($f['ui_geschlecht']))
		$f['ui_geschlecht'] = "";
	if (!isset($f['ui_beziehung']))
		$f['ui_beziehung'] = "";
	if (!isset($f['ui_typ']))
		$f['ui_typ'] = "";
	
		// Benutzerdaten lesen
	$query = "SELECT * FROM `user` WHERE `u_id`=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$userdata = mysqli_fetch_array($result);
		mysqli_free_result($result);
		$url = "inhalt.php?seite=einstellungen&id=$id";
		$userdaten_bearbeiten = "\n[<a href=\"$url\">Einstellungen ändern</a>]";
	}
	
	$text = "
	<form name=\"profil\" action=\"inhalt.php?seite=profil\" method=\"post\">
	<table>
		<tr>
			<td class=\"tabelle_kopfzeile\" colspan=\"5\">Ihre Benutzerdaten:</td>
		</tr>
		<tr>
			<td class=\"tabelle_zeile1\" style=\"text-align:right;\">Benutzer:</td>
			<td class=\"tabelle_zeile1\" colspan=\"2\"><b>" . zeige_userdetails($userdata['u_id'], $userdata) . "</b></td>
			<td class=\"tabelle_zeile1\">$userdaten_bearbeiten</td>
		</tr>";
	
	$bgcolor = 'class="tabelle_zeile1"';
	if ($userdata['u_email']) {
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>E-Mail:</td>
			<td $bgcolor colspan=\"3\">" . htmlspecialchars($userdata['u_email']) ." (öffentlich)</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
	}
	if ($userdata['u_adminemail']) {
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>E-Mail:</td>
			<td $bgcolor colspan=\"3\">" . htmlspecialchars($userdata['u_adminemail']) . " (intern, für Admins)</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
	}
	if ($userdata['u_url']) {
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Homepage:</td>
			<td $bgcolor colspan=\"3\"><a href=\"" . htmlspecialchars($userdata['u_url']) . "\" target=\"_blank\">" . htmlspecialchars($userdata['u_url']) . "</a></td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
	}
	$text .= "
	<tr>
		<td>
			<input type=\"hidden\" name=\"id\" value=\"$id\">
			<input type=\"hidden\" name=\"aktion\" value=\"neu\">
		</td>
	</tr>";
	$bgcolor = 'class="tabelle_zeile1"';
	$text .= "
	<tr>
		<td colspan=\"5\">&nbsp;</td>
	</tr>
	<tr>
		<td class=\"tabelle_kopfzeile\" colspan=\"5\">Ihr neues Profil:</td>
	</tr>
	<tr>
		<td style=\"text-align:right;\" $bgcolor>Straße:</td>
		<td $bgcolor colspan=\"3\">$f1<input type=\"text\" name=\"f[ui_strasse]\" value=\"" . $f['ui_strasse'] . "\" size=$eingabe_breite>$f2</td>
	</tr>";
	if ($bgcolor == 'class="tabelle_zeile1"') {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>PLZ:</td>
			<td $bgcolor>$f1<input type=\"text\" name=\"f[ui_plz]\" value=\"$f[ui_plz]\" size=8>$f2</td>
			<td style=\"text-align:right;\" $bgcolor>Ort:</td>
			<td style=\"width:100%;\" $bgcolor>$f1<input type=\"text\" name=\"f[ui_ort]\" value=\"" . $f['ui_ort'] . "\" size=" . ($eingabe_breite - 15) . ">$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Land:</td>
			<td  $bgcolor colspan=\"3\">$f1<input type=\"text\" name=\"f[ui_land]\" value=\"" . $f['ui_land'] . "\" size=$eingabe_breite>$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Telefon:</td>
			<td $bgcolor colspan=\"3\">$f1<input type=\"text\" name=\"f[ui_tel]\" value=\"" . $f['ui_tel'] . "\" size=$eingabe_breite>$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Mobil:</td>
			<td $bgcolor colspan=\"3\">$f1<input type=\"text\" name=\"f[ui_handy]\" value=\"" . $f['ui_handy'] . "\" size=$eingabe_breite>$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Telefax:</td>
			<td $bgcolor colspan=\"3\">$f1<input type=\"text\" name=\"f[ui_fax]\" value=\"" . $f['ui_fax'] . "\" size=$eingabe_breite>$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>ICQ:</td>
			<td $bgcolor colspan=\"3\">$f1<input type=\"text\" name=\"f[ui_icq]\" value=\"" . $f['ui_icq'] . "\" size=$eingabe_breite>$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Geburtsdatum:</td>
			<td $bgcolor colspan=\"3\">$f1<input type=\"text\" name=\"f[ui_geburt]\" value=\"" . $f['ui_geburt'] . "\" size=12>" . " (TT.MM.JAHR, z.B. 24.01.1969)$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Geschlecht:</td>
			<td $bgcolor colspan=\"3\">" . $f1 . autoselect("f[ui_geschlecht]", $f['ui_geschlecht'], "userinfo", "ui_geschlecht") . $f2 . "</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Beziehung:</td>
			<td $bgcolor colspan=\"3\">" . $f1 . autoselect("f[ui_beziehung]", $f['ui_beziehung'], "userinfo", "ui_beziehung") . $f2 . "</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Typ:</td>
			<td $bgcolor colspan=\"3\">" . $f1 . autoselect("f[ui_typ]", $f['ui_typ'], "userinfo", "ui_typ") . $f2 . "</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Beruf:</td>
			<td $bgcolor colspan=\"3\">$f1<input type=\"text\" name=\"f[ui_beruf]\" value=\"" . $f['ui_beruf'] . "\" size=$eingabe_breite>$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>Hobbies:</td>
			<td $bgcolor colspan=\"3\">$f1<textarea name=\"f[ui_hobby]\" value=\"" . $f['ui_hobby'] . "\" rows=\"4\" cols=\"" . ($eingabe_breite - 6) . "\" wrap=\"virtual\">$f[ui_hobby]</textarea>$f2</td>
		</tr>";
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>
			<td $bgcolor colspan=\"3\">
				$f1
				<input type=\"hidden\" name=\"f[ui_id]\" value=\"$f[ui_id]\">
				<input type=\"hidden\" name=\"f[ui_userid]\" value=\"$u_id\">
				<input type=\"hidden\" name=\"nick\" value=\"$userdata[u_nick]\">
				<input type=\"hidden\" name=\"id\" value=\"$id\">
				<input type=\"hidden\" name=\"aktion\" value=\"neu\">
				<input type=\"submit\" name=\"los\" value=\"Eintragen\">
				$f2
			</td>
		</tr>
	</table>
	</form>";
	
	return $text;
}

?>