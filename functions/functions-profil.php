<?php

function profil_editor($u_id, $u_nick, $f) {
	// Editor zur Änderung oder zur Neuanlage eines Profils
	// $u_id=Benutzer-ID
	// $u_nick=Benutzername
	// $f=Array der Profileinstellungen
	global $mysqli_link, $t, $id, $f1, $f2;
	
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
	$value = "<b>" . zeige_userdetails($userdata['u_id'], $userdata) . "</b> $userdaten_bearbeiten";
	$text .= zeige_formularfelder("text", $zaehler, $t['profil_benutzername'], "", $value);
	$zaehler++;
	
	// E-Mail
	$value = htmlspecialchars($userdata['u_email']);
	$text .= zeige_formularfelder("text", $zaehler, $t['profil_email'], "", $value);
	$zaehler++;
	
	if ($userdata['u_adminemail']) {
		// Interne E-Mail
		$value = htmlspecialchars($userdata['u_adminemail']) . $t['profil_interne_email_details'];
		$text .= zeige_formularfelder("text", $zaehler, $t['profil_interne_email'], "", $value);
		$zaehler++;
	}

	$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
	
	// Überschrift: Ihr Profil
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $t['ihr_profil'], "", "", 0, "70", "");
	
	// Wohnort
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_wohnort'], "ui_wohnort", $f['ui_wohnort'], 0, "70", $t['profil_wohnort_details']);
	$zaehler++;
	
	// Geburtsdatum
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_geburt'], "ui_geburt",  $f['ui_geburt'], 0, "12");
	$zaehler++;
	
	// Geschlecht
	$value = array($t['profil_keine_angabe'], $t['profil_geschlecht_maennlich'], $t['profil_geschlecht_weiblich'], $t['profil_geschlecht_divers']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $t['profil_geschlecht'], "ui_geschlecht", $value, $f['ui_geschlecht']);
	$zaehler++;
	
	// Beziehungsstatus
	$value = array($t['profil_keine_angabe'], $t['profil_verheiratet'], $t['profil_ledig'], $t['profil_single'], $t['profil_vergeben']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $t['profil_beziehungsstatus'], "ui_beziehungsstatus", $value, $f['ui_beziehungsstatus']);
	$zaehler++;
	
	// Typ
	$value = array($t['profil_keine_angabe'], $t['profil_typ_zierlich'], $t['profil_typ_schlank'], $t['profil_typ_sportlich'], $t['profil_typ_normal'], $t['profil_typ_mollig'], $t['profil_typ_dick']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $t['profil_typ'], "ui_typ", $value, $f['ui_typ']);
	$zaehler++;
	
	// Beruf
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_homepage'], "ui_homepage", $f['ui_homepage']);
	$zaehler++;
	
	// Beruf
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_beruf'], "ui_beruf", $f['ui_beruf']);
	$zaehler++;
	
	// Lieblingsfilm
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_lieblingsfilm'], "ui_lieblingsfilm", $f['ui_lieblingsfilm']);
	$zaehler++;
	
	// Lieblingsserie
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_lieblingsserie'], "ui_lieblingsserie", $f['ui_lieblingsserie']);
	$zaehler++;
	
	// Lieblingsbuch
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_lieblingsbuch'], "ui_lieblingsbuch", $f['ui_lieblingsbuch']);
	$zaehler++;
	
	// Lieblingsschauspieler
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_lieblingsschauspieler'], "ui_lieblingsschauspieler", $f['ui_lieblingsschauspieler']);
	$zaehler++;
	
	// Lieblingsgetränk
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_lieblingsgetraenk'], "ui_lieblingsgetraenk", $f['ui_lieblingsgetraenk']);
	$zaehler++;
	
	// Lieblingsgericht
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_lieblingsgericht'], "ui_lieblingsgericht", $f['ui_lieblingsgericht']);
	$zaehler++;
	
	// Lieblingsspiel
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_lieblingsspiel'], "ui_lieblingsspiel", $f['ui_lieblingsspiel']);
	$zaehler++;
	
	// Lieblingsfarbe
	$text .= zeige_formularfelder("input", $zaehler, $t['profil_lieblingsfarbe'], "ui_lieblingsfarbe", $f['ui_lieblingsfarbe']);
	$zaehler++;
	
	// Hobbies
	$text .= zeige_formularfelder("textarea", $zaehler, $t['profil_hobby'], "ui_hobby", $f['ui_hobby']);
	$zaehler++;
	
	// Hobbies
	$text .= zeige_formularfelder("textarea2", $zaehler, $t['profil_text'], "ui_text", $f['ui_text']);
	$zaehler++;
		
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	
		$text .= "
		<tr>
			<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>
			<td $bgcolor>
				<input type=\"hidden\" name=\"ui_id\" value=\"$f[ui_id]\">
				<input type=\"hidden\" name=\"ui_userid\" value=\"$u_id\">
				<input type=\"hidden\" name=\"nick\" value=\"$userdata[u_nick]\">
				<input type=\"hidden\" name=\"id\" value=\"$id\">
				<input type=\"hidden\" name=\"aktion\" value=\"aendern\">
				<input type=\"submit\" name=\"los\" value=\"Eintragen\">
			</td>
		</tr>
	</table>
	</form>";
	
	return $text;
}

function unhtmlentities($string) {
	// replace numeric entities
	// $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
	$string = preg_replace_callback(
		"~&#x([0-9a-f]+);~i",
		function ($matches) {
			return chr(hexdec($matches[1]));
		},
		$string
		);
	
	//$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
	$string = preg_replace_callback(
		"/&#x([0-9a-f]+);/",
		function ($matches) {
			return chr(hexdec($matches[1]));
		},
		$string
		);
	
	// replace literal entities
	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
	$trans_tbl = array_flip($trans_tbl);
	
	return strtr($string, $trans_tbl);
}
?>