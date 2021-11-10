<?php

function profil_editor($u_id, $u_nick, $f) {
	// Editor zur Änderung oder zur Neuanlage eines Profils
	// $u_id=Benutzer-ID
	// $u_nick=Benutzername
	// $f=Array der Profileinstellungen
	global $mysqli_link, $communityfeatures, $t, $id, $f1, $f2;
	
	$eingabe_breite = 45;
	
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
	
		// Benutzerdaten lesen
	$query = "SELECT * FROM `user` WHERE `u_id`=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$userdata = mysqli_fetch_array($result);
		mysqli_free_result($result);
		$url = "edit.php?id=$id";
		$userdaten_bearbeiten = "\n[<a href=\"$url\" target=\"$fenster\" onClick=\"neuesFenster('$url');return(false)\">Einstellungen ändern</a>]";
	}
	
	?>
	<form name="profil" action="profil.php" method="POST">
	<table>
		<tr>
			<td class="tabelle_kopfzeile" colspan="5">Ihre Benutzerdaten:</td>
		</tr>
		<tr>
			<td class="tabelle_zeile1" style="text-align:right;">Benutzer:</td>
			<td class="tabelle_zeile1" colspan="2"><b><?php echo zeige_userdetails($userdata['u_id'], $userdata); ?></b></td>
			<td class="tabelle_zeile1"><?php echo $userdaten_bearbeiten; ?></td>
		</tr>
		<?php
		$bgcolor = 'class="tabelle_zeile1"';
		if ($userdata['u_email']) {
			?>
			<tr>
				<td style="text-align:right;" <?php echo $bgcolor; ?>>E-Mail:</td>
				<td <?php echo $bgcolor; ?> colspan="3"><?php echo htmlspecialchars($userdata['u_email']); ?> (öffentlich)</td>
			</tr>
			<?php
			if ($bgcolor == 'class="tabelle_zeile1"') {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
		}
		if ($userdata['u_adminemail']) {
			?>
			<tr>
				<td style="text-align:right;" <?php echo $bgcolor; ?>>E-Mail:</td>
				<td <?php echo $bgcolor; ?> colspan="3"><?php echo htmlspecialchars($userdata['u_adminemail']); ?> (intern, für Admins)</td>
			</tr>
			<?php
			if ($bgcolor == 'class="tabelle_zeile1"') {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
		}
		if ($userdata['u_url']) {
			?>
			<tr>
				<td style="text-align:right;" <?php echo $bgcolor; ?>>Homepage:</td>
				<td <?php echo $bgcolor; ?> colspan="3"><a href="<?php echo htmlspecialchars($userdata['u_url']); ?>" target="_blank"><?php echo htmlspecialchars($userdata['u_url']); ?></a></td>
			</tr>
			<?php
			if ($bgcolor == 'class="tabelle_zeile1"') {
				$bgcolor = 'class="tabelle_zeile2"';
			} else {
				$bgcolor = 'class="tabelle_zeile1"';
			}
		}
		?>
		<tr>
			<td>
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="aktion" value="neu">
			</td>
		</tr>
		<?php
		$bgcolor = 'class="tabelle_zeile1"';
		?>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td class="tabelle_kopfzeile" colspan="5">Ihr neues Profil:</td>
		</tr>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Straße:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_strasse]\" value=\"" . $f['ui_strasse'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>PLZ:</td>
			<td <?php echo $bgcolor; ?>><?php echo $f1 . "<input type=\"text\" name=\"f[ui_plz]\" value=\"$f[ui_plz]\" size=8>" . $f2; ?></td>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Ort:</td>
			<td style="width:100%;" <?php echo $bgcolor; ?>><?php echo $f1 . "<input type=\"text\" name=\"f[ui_ort]\" value=\"" . $f['ui_ort'] . "\" size=" . ($eingabe_breite - 15) . ">" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Land:</td>
			<td  <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_land]\" value=\"" . $f['ui_land'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
	
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Telefon:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_tel]\" value=\"" . $f['ui_tel'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Mobil:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_handy]\" value=\"" . $f['ui_handy'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Telefax:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_fax]\" value=\"" . $f['ui_fax'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>ICQ:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_icq]\" value=\"" . $f['ui_icq'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Geburtsdatum:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_geburt]\" value=\"" . $f['ui_geburt'] . "\" size=12>" . " (TT.MM.JAHR, z.B. 24.01.1969)" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Geschlecht:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1; autoselect("f[ui_geschlecht]", $f['ui_geschlecht'], "userinfo", "ui_geschlecht"); echo $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Beziehung:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1; autoselect("f[ui_beziehung]", $f['ui_beziehung'], "userinfo", "ui_beziehung"); echo $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Typ:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1; autoselect("f[ui_typ]", $f['ui_typ'], "userinfo", "ui_typ"); echo $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Beruf:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<input type=\"text\" name=\"f[ui_beruf]\" value=\"" . $f['ui_beruf'] . "\" size=$eingabe_breite>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		?>
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>Hobbies:</td>
			<td <?php echo $bgcolor; ?> colspan="3"><?php echo $f1 . "<textarea name=\"f[ui_hobby]\" value=\"" . $f['ui_hobby'] . "\" rows=\"4\" cols=\"" . ($eingabe_breite - 6) . "\" wrap=\"virtual\">$f[ui_hobby]</textarea>" . $f2; ?></td>
		</tr>
		<?php
		if ($bgcolor == 'class="tabelle_zeile1"') {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		?>
		
		<tr>
			<td style="text-align:right;" <?php echo $bgcolor; ?>>&nbsp;</td>
			<td <?php echo $bgcolor; ?> colspan="3">
				<?php echo $f1; ?>
				<input type="hidden" name="f[ui_id]" value="<?php echo $f['ui_id']; ?>">
				<input type="hidden" name="f[ui_userid]" value="<?php echo $u_id; ?>">
				<input type="hidden" name="nick" value="<?php echo $userdata['u_nick']; ?>">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="aktion" value="neu">
				<input type="submit" name="los" value="Eintragen">
				<?php echo $f2; ?>
			</td>
		</tr>
	</table>
	</form>
	<?php
}

?>