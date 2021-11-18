<?php

function profil_editor($u_id, $u_nick, $f) {
	// Editor zur Änderung oder zur Neuanlage eines Profils
	// $u_id=Benutzer-ID
	// $u_nick=Benutzername
	// $f=Array der Profileinstellungen
	global $mysqli_link, $t, $id, $f1, $f2;
	
	$eingabe_breite = 45;
	
	// Benutzerdaten lesen
	$query = "SELECT * FROM `user` WHERE `u_id`=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		$userdata = mysqli_fetch_array($result);
		mysqli_free_result($result);
		$url = "inhalt.php?seite=einstellungen&id=$id";
		$userdaten_bearbeiten = "\n[<a href=\"$url\">Einstellungen ändern</a>]";
	}
	
	$zaehler = 0;
	
	$text = "
	<form name=\"profil\" action=\"inhalt.php?seite=profil\" method=\"post\">
	<table style=\"width:100%;\">
		<tr>
			<td class=\"tabelle_kopfzeile\" colspan=\"2\">$t[ihre_benutzerdaten]</td>
		</tr>";
	
	// Benutzer
	$name = $t['profil_benutzername'];
	$value = "<b>" . zeige_userdetails($userdata['u_id'], $userdata) . "</b> $userdaten_bearbeiten";
	$text .= zeige_profilfelder("text", $zaehler, $name, "", $value);
	$zaehler++;
	
	// E-Mail
	$name = $t['profil_email'];
	$value = htmlspecialchars($userdata['u_email']);
	$text .= zeige_profilfelder("text", $zaehler, $name, "", $value);
	$zaehler++;
	
	if ($userdata['u_adminemail']) {
		// Interne E-Mail
		$name = $t['profil_interne_email'];
		$value = htmlspecialchars($userdata['u_adminemail']) . $t['profil_interne_email_details'];
		$text .= zeige_profilfelder("text", $zaehler, $name, "", $value);
		$zaehler++;
	}
	if ($userdata['u_url']) {
		// Homepage
		$name = $t['profil_homepage'];
		$value = "<a href=\"" . htmlspecialchars($userdata['u_url']) . "\" target=\"_blank\">" . htmlspecialchars($userdata['u_url']) . "</a>";
		$text .= zeige_profilfelder("text", $zaehler, $name, "", $value);
		$zaehler++;
	}
	
	$text .= "
	<tr>
		<td>&nbsp;</td>
	</tr>";

	$text .= "
	<tr>
		<td colspan=\"2\">&nbsp;</td>
	</tr>
	<tr>
		<td class=\"tabelle_kopfzeile\" colspan=\"2\">$t[ihr_profil]</td>
	</tr>
	<tr>";
	
	// Wohnort
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_wohnort'], "ui_wohnort", $f['ui_wohnort'], 0, "45", $t['profil_wohnort_details']);
	$zaehler++;
	
	// Geburtsdatum
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_geburt'], "ui_geburt",  $f['ui_geburt'], 0, "12");
	$zaehler++;
	
	// Geschlecht
	$value = array($t['profil_keine_angabe'], $t['profil_geschlecht_maennlich'], $t['profil_geschlecht_weiblich'], $t['profil_geschlecht_divers']);
	$text .= zeige_profilfelder("selectbox", $zaehler, $t['profil_geschlecht'], "ui_geschlecht", $value, $f['ui_geschlecht']);
	$zaehler++;
	
	// Beziehungsstatus
	$value = array($t['profil_keine_angabe'], $t['profil_verheiratet'], $t['profil_ledig'], $t['profil_single'], $t['profil_vergeben']);
	$text .= zeige_profilfelder("selectbox", $zaehler, $t['profil_beziehungsstatus'], "ui_beziehungsstatus", $value, $f['ui_beziehungsstatus']);
	$zaehler++;
	
	// Typ
	$value = array($t['profil_keine_angabe'], $t['profil_typ_zierlich'], $t['profil_typ_schlank'], $t['profil_typ_sportlich'], $t['profil_typ_normal'], $t['profil_typ_mollig'], $t['profil_typ_dick']);
	$text .= zeige_profilfelder("selectbox", $zaehler, $t['profil_typ'], "ui_typ", $value, $f['ui_typ']);
	$zaehler++;
	
	// Beruf
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_beruf'], "ui_beruf", $f['ui_beruf']);
	$zaehler++;
	
	// Lieblingsfilm
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_lieblingsfilm'], "ui_lieblingsfilm", $f['ui_lieblingsfilm']);
	$zaehler++;
	
	// Lieblingsserie
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_lieblingsserie'], "ui_lieblingsserie", $f['ui_lieblingsserie']);
	$zaehler++;
	
	// Lieblingsbuch
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_lieblingsbuch'], "ui_lieblingsbuch", $f['ui_lieblingsbuch']);
	$zaehler++;
	
	// Lieblingsschauspieler
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_lieblingsschauspieler'], "ui_lieblingsschauspieler", $f['ui_lieblingsschauspieler']);
	$zaehler++;
	
	// Lieblingsgetränk
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_lieblingsgetraenk'], "ui_lieblingsgetraenk", $f['ui_lieblingsgetraenk']);
	$zaehler++;
	
	// Lieblingsgericht
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_lieblingsgericht'], "ui_lieblingsgericht", $f['ui_lieblingsgericht']);
	$zaehler++;
	
	// Lieblingsspiel
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_lieblingsspiel'], "ui_lieblingsspiel", $f['ui_lieblingsspiel']);
	$zaehler++;
	
	// Lieblingsfarbe
	$text .= zeige_profilfelder("input", $zaehler, $t['profil_lieblingsfarbe'], "ui_lieblingsfarbe", $f['ui_lieblingsfarbe']);
	$zaehler++;
	
	// Hobbies
	$text .= zeige_profilfelder("textarea", $zaehler, $t['profil_hobby'], "ui_hobby", $f['ui_hobby']);
	$zaehler++;
		
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>
			<td $bgcolor>
				$f1
				<input type=\"hidden\" name=\"ui_id\" value=\"$f[ui_id]\">
				<input type=\"hidden\" name=\"ui_userid\" value=\"$u_id\">
				<input type=\"hidden\" name=\"nick\" value=\"$userdata[u_nick]\">
				<input type=\"hidden\" name=\"id\" value=\"$id\">
				<input type=\"hidden\" name=\"profilaenderungen\" value=\"true\">
				<input type=\"hidden\" name=\"aktion\" value=\"aendern\">
				<input type=\"submit\" name=\"los\" value=\"Eintragen\">
				$f2
			</td>
		</tr>
	</table>
	</form>";
	
	return $text;
}

function zeige_profilfelder($art_der_anzeige, $zaehler, $name, $key, $value, $ausgewaehltes_feld_selectbox = 0, $breite_eingabefeld = "45", $beschreibung = "") {
	// Hintergrundfarbe mit jeder Zeile wechseln
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	
	$text = "";
	
	$text .= "<tr>\n";
	if($art_der_anzeige == "selectbox") {
		// Auswahlfeld anzeigen
		$text .= "<td style=\"text-align:right; width:200px;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor>";
		
		//$text .= autoselect("f[ui_geschlecht]", $f['ui_geschlecht'], "userinfo", "ui_geschlecht");
		$text .= "<select name=\"$key\">";
		for ($i=0; $i<count($value);$i++) {
			if($i == $ausgewaehltes_feld_selectbox) {
				$selected = " selected";
			} else {
				$selected = "";
			}
			$text .= "<option$selected value=\"$i\">$value[$i]</option>\n";
			$felder[$i];
		}
		$text .= "</select> $beschreibung</td>\n";
	} else if($art_der_anzeige == "input") {
		// Input anzeigen
		$text .= "<td style=\"text-align:right;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor><input type=\"text\" name=\"$key\" value=\"$value\" maxlenght=\"100\" size=\"$breite_eingabefeld\"> $beschreibung</td>\n";
	} else if($art_der_anzeige == "textarea") {
		// Textarea anzeigen
		$text .= "<td style=\"text-align:right;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor><textarea name=\"$key\" rows=\"4\" cols=\"" . ($breite_eingabefeld - 10) . "\" maxlenght=\"255\">$value</textarea> $beschreibung</td>\n";
	} else {
		// Text anzeigen
		$text .= "<td style=\"text-align:right;\" $bgcolor>$name</td>\n";
		$text .= "<td $bgcolor>$value $beschreibung</td>\n";
	}
	$text .= "</tr>\n";
	
	return $text;
}

?>