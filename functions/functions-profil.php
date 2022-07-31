<?php
function profil_editor($u_id, $u_nick, $f) {
	// Editor zur Änderung oder zur Neuanlage eines Profils
	// $u_id=Benutzer-ID
	// $u_nick=Benutzername
	// $f=Array der Profileinstellungen
	global $lang;
	
	if( !isset($f['ui_id']) ) {
		$f['ui_id'] = 0;
	}
	
	// Benutzerdaten lesen
	$userdaten_bearbeiten = "\n[<a href=\"inhalt.php?bereich=einstellungen\">$lang[profil_einstellungen_aendern]</a>]";
	
	$zaehler = 0;
	
	$text = "<form action=\"inhalt.php?bereich=profil\" method=\"post\">\n";
	$text .= "<input type=\"hidden\" name=\"ui_id\" value=\"$f[ui_id]\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"aendern\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Benutzerdaten
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['ihre_benutzerdaten'], "", "", 0, "70", "");
	
	// Benutzer
	$value = "<b>" . zeige_userdetails($u_id) . "</b> $userdaten_bearbeiten";
	$text .= zeige_formularfelder("text", $zaehler, $lang['profil_benutzername'], "", $value);
	$zaehler++;

	$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
	
	// Überschrift: Farbeinstellungen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['profil_farbeinstellungen'], "", "", 0, "70", "");
	
	// Hintergrundfarbe der Seite
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_hintergrundfarbe'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<input type=\"text\" data-wheelcolorpicker name=\"ui_hintergrundfarbe\" value=\"$f[ui_hintergrundfarbe]\" />";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Farbe anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_aktuell_gespeicherte_farbe'] . "</td>\n";
	$text .= "<td style=\"background-color:#" . $f['ui_hintergrundfarbe'] . ";\">&nbsp;</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Textfarbe der Überschriften
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_ueberschriften_textfarbe'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<input type=\"text\" data-wheelcolorpicker name=\"ui_ueberschriften_textfarbe\" value=\"$f[ui_ueberschriften_textfarbe]\" />";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Farbe anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_aktuell_gespeicherte_farbe'] . "</td>\n";
	$text .= "<td style=\"background-color:#" . $f['ui_ueberschriften_textfarbe'] . ";\">&nbsp;</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Hintergrundfarbe der Überschriften und Rahmenfarbe der Tabelle
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_ueberschriften_hintergrundfarbe'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<input type=\"text\" data-wheelcolorpicker name=\"ui_ueberschriften_hintergrundfarbe\" value=\"$f[ui_ueberschriften_hintergrundfarbe]\" />";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Farbe anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_aktuell_gespeicherte_farbe'] . "</td>\n";
	$text .= "<td style=\"background-color:#" . $f['ui_ueberschriften_hintergrundfarbe'] . ";\">&nbsp;</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Textfarbe des Inhalts
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_inhalt_textfarbe'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<input type=\"text\" data-wheelcolorpicker name=\"ui_inhalt_textfarbe\" value=\"$f[ui_inhalt_textfarbe]\" />";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Farbe anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_aktuell_gespeicherte_farbe'] . "</td>\n";
	$text .= "<td style=\"background-color:#" . $f['ui_inhalt_textfarbe'] . ";\">&nbsp;</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Linkfarbe des Inhalts
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_inhalt_linkfarbe'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<input type=\"text\" data-wheelcolorpicker name=\"ui_inhalt_linkfarbe\" value=\"$f[ui_inhalt_linkfarbe]\" />";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Farbe anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_aktuell_gespeicherte_farbe'] . "</td>\n";
	$text .= "<td style=\"background-color:#" . $f['ui_inhalt_linkfarbe'] . ";\">&nbsp;</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Linkfarbe des Inhalts (aktiv)
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_inhalt_linkfarbe_aktiv'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<input type=\"text\" data-wheelcolorpicker name=\"ui_inhalt_linkfarbe_aktiv\" value=\"$f[ui_inhalt_linkfarbe_aktiv]\" />";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Farbe anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_aktuell_gespeicherte_farbe'] . "</td>\n";
	$text .= "<td style=\"background-color:#" . $f['ui_inhalt_linkfarbe_aktiv'] . ";\">&nbsp;</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Hintergrundfarbe des Inhalts
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_inhalt_hintergrundfarbe'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<input type=\"text\" data-wheelcolorpicker name=\"ui_inhalt_hintergrundfarbe\" value=\"$f[ui_inhalt_hintergrundfarbe]\" />";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	// Farbe anzeigen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['profil_aktuell_gespeicherte_farbe'] . "</td>\n";
	$text .= "<td style=\"background-color:#" . $f['ui_inhalt_hintergrundfarbe'] . ";\">&nbsp;</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
	
	
	
	// Überschrift: Ihr Profil
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['ihr_profil'], "", "", 0, "70", "");
	
	// Wohnort
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_wohnort'], "ui_wohnort", $f['ui_wohnort'], 0, "70", $lang['profil_wohnort_details']);
	$zaehler++;
	
	// Geburtsdatum
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_geburt'], "ui_geburt",  $f['ui_geburt'], 0, "12");
	$zaehler++;
	
	// Geschlecht
	$value = array($lang['profil_keine_angabe'], $lang['profil_geschlecht_maennlich'], $lang['profil_geschlecht_weiblich'], $lang['profil_geschlecht_divers']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $lang['profil_geschlecht'], "ui_geschlecht", $value, $f['ui_geschlecht']);
	$zaehler++;
	
	// Beziehungsstatus
	$value = array($lang['profil_keine_angabe'], $lang['profil_verheiratet'], $lang['profil_ledig'], $lang['profil_single'], $lang['profil_vergeben']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $lang['profil_beziehungsstatus'], "ui_beziehungsstatus", $value, $f['ui_beziehungsstatus']);
	$zaehler++;
	
	// Typ
	$value = array($lang['profil_keine_angabe'], $lang['profil_typ_zierlich'], $lang['profil_typ_schlank'], $lang['profil_typ_sportlich'], $lang['profil_typ_normal'], $lang['profil_typ_mollig'], $lang['profil_typ_dick']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $lang['profil_typ'], "ui_typ", $value, $f['ui_typ']);
	$zaehler++;
	
	// Beruf
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_webseite'], "ui_homepage", $f['ui_homepage']);
	$zaehler++;
	
	// Beruf
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_beruf'], "ui_beruf", $f['ui_beruf']);
	$zaehler++;
	
	// Lieblingsfilm
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_lieblingsfilm'], "ui_lieblingsfilm", $f['ui_lieblingsfilm']);
	$zaehler++;
	
	// Lieblingsserie
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_lieblingsserie'], "ui_lieblingsserie", $f['ui_lieblingsserie']);
	$zaehler++;
	
	// Lieblingsbuch
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_lieblingsbuch'], "ui_lieblingsbuch", $f['ui_lieblingsbuch']);
	$zaehler++;
	
	// Lieblingsschauspieler
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_lieblingsschauspieler'], "ui_lieblingsschauspieler", $f['ui_lieblingsschauspieler']);
	$zaehler++;
	
	// Lieblingsgetränk
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_lieblingsgetraenk'], "ui_lieblingsgetraenk", $f['ui_lieblingsgetraenk']);
	$zaehler++;
	
	// Lieblingsgericht
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_lieblingsgericht'], "ui_lieblingsgericht", $f['ui_lieblingsgericht']);
	$zaehler++;
	
	// Lieblingsspiel
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_lieblingsspiel'], "ui_lieblingsspiel", $f['ui_lieblingsspiel']);
	$zaehler++;
	
	// Lieblingsfarbe
	$text .= zeige_formularfelder("input", $zaehler, $lang['profil_lieblingsfarbe'], "ui_lieblingsfarbe", $f['ui_lieblingsfarbe']);
	$zaehler++;
	
	// Hobbies
	$text .= zeige_formularfelder("textarea", $zaehler, $lang['profil_hobby'], "ui_hobby", $f['ui_hobby']);
	$zaehler++;
	
	// Text über sich selbst
	$text .= zeige_formularfelder("textarea2", $zaehler, $lang['profil_text'], "ui_text", $f['ui_text']);
	$zaehler++;
	
	// Leerzeile
	$text .= zeige_formularfelder("leerzeile", $zaehler, "", "", "", 0, "70", "");
	
	// Überschrift: Allgemeines
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $lang['profil_allgemeines'], "", "", 0, "70", "");
	
	// Benutzerseite freigeben
	if($f['u_chathomepage'] == 1) {
		$u_chathomepage_link = "<a href=\"home.php?/$u_nick\" target=\"_blank\">" . $lang['profil_benutzerseite_zur_homepage'] . "</a>";
	} else {
		$u_chathomepage_link = "<a href=\"home.php?ui_userid=$u_id&aktion=&preview=yes\" target=\"_blank\">" . $lang['profil_benutzerseite_zur_vorschau'] . "</a>";
	}
	$value = array($lang['profil_benutzerseite_deaktivieren'], $lang['profil_benutzerseite_aktivieren']);
	$text .= zeige_formularfelder("selectbox", $zaehler, $lang['profil_benutzerseite'], "u_chathomepage", $value, $f['u_chathomepage'], "70", $u_chathomepage_link);
	$zaehler++;
	
	
	// Speichern oder zurücksetzen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor><input type=\"reset\" value=\"$lang[einstellungen_zuruecksetzen]\"> $lang[einstellungen_zuruecksetzen_beschreibung]</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"uebergabe\" value=\"$lang[einstellungen_speichern]\"></td>\n";
	$text .= "</tr>\n";
	

	$text .= "</table>\n";
	$text .= "</form>\n";
	
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