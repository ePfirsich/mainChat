<?php

function profil_editor($u_id, $u_nick, $f) {
	// Editor zur Änderung oder zur Neuanlage eines Profils
	// $u_id=User-Id
	// $u_nick=Nickname
	// $f=Array der Profileinstellungen
	global $dbase, $mysqli_link, $communityfeatures, $t, $http_host, $id, $eingabe_breite, $f1, $f2;
	global $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $farbe_tabelle_kopf2, $farbe_text;
	
	// Fenstername
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $u_nick);
	
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
	
	// Userdaten lesen
	$query = "SELECT * FROM `user` WHERE `u_id`=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$userdata = mysqli_fetch_array($result);
		mysqli_free_result($result);
		$url = "edit.php?http_host=$http_host&id=$id";
		$userdaten_bearbeiten = "\n[<a href=\"$url\" TARGET=\"$fenster\" onclick=\"window.open('$url','$fenster','resizable=yes,scrollbars=yes,width=300,height=580'); return(false);\">Einstellungen ändern</A>]";
	}
	
	?>
	<table>
		<tr>
			<td class="tabelle_kopfzeile" colspan="5">Ihre Userdaten:</td>
		</tr>
		<tr>
			<td style="background-color:<?php echo $farbe_tabelle_zeile1;?>; text-align:right;">Nickname:</td>
			<td style="background-color:<?php echo $farbe_tabelle_zeile1;?>;" colspan="2"><b><?php echo user($userdata['u_id'], $userdata, TRUE, FALSE); ?></b></td>
			<td style="background-color:<?php echo $farbe_tabelle_zeile1;?>;"><?php echo $userdaten_bearbeiten; ?></td>
		</tr>
		<tr>
			<td style="background-color:<?php echo $farbe_tabelle_zeile2;?>; text-align:right;">Username:</td>
			<td style="background-color:<?php echo $farbe_tabelle_zeile2;?>;" colspan="2"><?php echo htmlspecialchars($userdata['u_name']); ?></td>
			<td style="background-color:<?php echo $farbe_tabelle_zeile2;?>;">&nbsp;</td>
		</tr>
		<?php
		$bgcolor = $farbe_tabelle_zeile1;
		if ($userdata['u_email']) {
			?>
			<tr>
				<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">E-Mail:</td>
				<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo htmlspecialchars($userdata['u_email']); ?> (öffentlich)</td>
			</tr>
			<?php
			if ($bgcolor == $farbe_tabelle_zeile1) {
				$bgcolor = $farbe_tabelle_zeile2;
			} else {
				$bgcolor = $farbe_tabelle_zeile1;
			}
		}
		if ($userdata['u_adminemail']) {
			?>
			<tr>
				<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">E-Mail:</td>
				<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo htmlspecialchars($userdata['u_adminemail']); ?> (intern, für Admins)</td>
			</tr>
			<?php
			if ($bgcolor == $farbe_tabelle_zeile1) {
				$bgcolor = $farbe_tabelle_zeile2;
			} else {
				$bgcolor = $farbe_tabelle_zeile1;
			}
		}
		if ($userdata['u_url']) {
			?>
			<tr>
				<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Homepage:</td>
				<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><a href="<?php echo htmlspecialchars($userdata['u_url']); ?>" target="_blank"><?php echo htmlspecialchars($userdata['u_url']); ?></a></td>
			</tr>
			<?php
			if ($bgcolor == $farbe_tabelle_zeile1) {
				$bgcolor = $farbe_tabelle_zeile2;
			} else {
				$bgcolor = $farbe_tabelle_zeile1;
			}
		}
		?>
		<tr>
			<td><form name="profil" action="profil.php" method="POST">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="aktion" value="neu">
				<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
			</td>
		</tr>
		<?php
		$bgcolor = $farbe_tabelle_zeile1;
		?>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td class="tabelle_kopfzeile" colspan="5">Ihr neues Profil:</td>
		</tr>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Straße:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_strasse]\" value=\"" . $f['ui_strasse'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">PLZ:</td>
			<td style="background-color:<?php echo $bgcolor;?>;"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_plz]\" value=\"$f[ui_plz]\" size=8>" . $f2; ?></td>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Ort:</td>
			<td style="background-color:<?php echo $bgcolor;?>; width:100%;"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_ort]\" value=\"" . $f['ui_ort'] . "\" size=" . ($eingabe_breite - 15) . ">" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Land:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_land]\" value=\"" . $f['ui_land'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
	
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Telefon:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_tel]\" value=\"" . $f['ui_tel'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Mobil:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_handy]\" value=\"" . $f['ui_handy'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Telefax:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_fax]\" value=\"" . $f['ui_fax'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">ICQ:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_icq]\" value=\"" . $f['ui_icq'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Geburtsdatum:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_geburt]\" value=\"" . $f['ui_geburt'] . "\" size=12>" . " (TT.MM.JAHR, z.B. 24.01.1969)" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Geschlecht:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1; autoselect("f[ui_geschlecht]", $f['ui_geschlecht'], "userinfo", "ui_geschlecht"); echo $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Beziehung:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1; autoselect("f[ui_beziehung]", $f['ui_beziehung'], "userinfo", "ui_beziehung"); echo $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Typ:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1; autoselect("f[ui_typ]", $f['ui_typ'], "userinfo", "ui_typ"); echo $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Beruf:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_beruf]\" value=\"" . $f['ui_beruf'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		
		?>
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">Hobbies:</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3"><?php echo $f1 . "<textarea name=\"f[ui_hobby]\" value=\"" . $f['ui_hobby'] . "\" rows=\"4\" cols=\"" . ($eingabe_breite - 6) . "\" wrap=\"virtual\">$f[ui_hobby]</textarea>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == $farbe_tabelle_zeile1) {
			$bgcolor = $farbe_tabelle_zeile2;
		} else {
			$bgcolor = $farbe_tabelle_zeile1;
		}
		?>
		
		<tr>
			<td style="background-color:<?php echo $bgcolor;?>; text-align:right;">&nbsp;</td>
			<td style="background-color:<?php echo $bgcolor;?>;" colspan="3">
				<?php echo $f1; ?>
				<input type="hidden" name="f[ui_id]" value="<?php echo $f['ui_id']; ?>">
				<input type="hidden" name="f[ui_userid]" value="<?php echo $u_id; ?>">
				<input type="hidden" name="nick" value="<?php echo $userdata['u_nick']; ?>">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="http_host" value="<?php echo $http_host; ?>">
				<input type="hidden" name="aktion" value="neu">
				<input type="submit" name="los" value="Eintragen">
				<?php echo $f2; ?>
			</td>
		</tr>
	</table>
	<?php
}

?>