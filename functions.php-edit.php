<?php

require_once("functions.php-func-verlasse_chat.php");
require_once("functions.php-func-nachricht.php");

function user_edit($f, $admin, $u_level, $size = ARRAY()) {
	// $f = Ass. Array mit Userdaten
	// $size = Ass. Array mit Fenstereinstellungen (Optional)
	
	global $id, $http_host, $level, $f1, $f2, $f3, $f4, $farbe_tabelle_kopf, $farbe_tabelle_koerper;
	global $farbe_chat_user, $farbe_chat_user_groesse, $farbe_text, $user_farbe;
	global $t, $backup_chat, $smilies_pfad, $erweitertefeatures;
	global $frame_size, $u_id, $communityfeatures, $punktefeatures;
	global $einstellungen_aendern, $eintritt_individuell;
	
	$input_breite = 32;
	$passwort_breite = 15;
	
	if (ist_online($f['u_id'])) {
		$box = str_replace("%user%", $f['u_nick'], $t['user_zeige20']);
	} else {
		$box = str_replace("%user%", $f['u_nick'], $t['user_zeige21']);
	}
	
	$text = '';
	$text .= "<TABLE CELLPADDING=5 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=\"$farbe_tabelle_koerper\">\n";
	$text .= "<TR><TD COLSPAN=2>";
	
	// Ausgabe in Tabelle
	$text .= "<FORM NAME=\"$f[u_nick]\" ACTION=\"edit.php\" METHOD=POST>\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"f[u_id]\" VALUE=\"$f[u_id]\">\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"edit\">\n";
	
	$text .= "<TABLE BORDER=0 CELLPADDING=0 WIDTH=100%>";
	
	// Backup-Algotithmus einschalten?
	$text .= "<TR><TD COLSPAN=2>" . $f1 . $t['user_zeige14']
		. "<SELECT NAME=\"f[u_backup]\">";
	if ($backup_chat) {
		$text .= "<OPTION VALUE=\"0\">$t[user_zeige15]";
	} elseif ($f['u_backup'] == 1) {
		$text .= "<OPTION SELECTED VALUE=\"1\">$t[user_zeige15]";
		$text .= "<OPTION VALUE=\"0\">$t[user_zeige16]";
	} else {
		$text .= "<OPTION VALUE=\"1\">$t[user_zeige15]";
		$text .= "<OPTION SELECTED VALUE=\"0\">$t[user_zeige16]";
	}
	
	$text .= "</SELECT><INPUT TYPE=\"SUBMIT\" NAME=\"eingabe\" VALUE=\"Ändern!\">"
		. $f2 . "<HR SIZE=2 NOSHADE></TD></TR>\n";
	
	// Nur für Admins
	if ($admin) {
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige17']
			. "</b><br>\n" . $f2
			. "<INPUT TYPE=\"TEXT\" VALUE=\"$f[u_name]\" NAME=\"f[u_name]\" SIZE=$input_breite>"
			. "</TD></TR>\n";
	} else if (($einstellungen_aendern) && ($u_level == 'U')) {
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige17']
			. "</b> (<a href=\"edit.php?http_host=$http_host&id=$id&aktion=andereadminmail\">ändern</a>)<br>\n"
			. $f2 . htmlspecialchars($f['u_name'])
			. "</TD></TR>\n";
	}
	
	if (!$einstellungen_aendern) {
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige18']
			. "</b>&nbsp;&nbsp;\n" . $f['u_nick'] . $f2 . "</TD></TR>\n";
	} else {
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige18']
			. "</b><br>\n" . $f2
			. "<INPUT TYPE=\"TEXT\" VALUE=\"$f[u_nick]\" NAME=\"f[u_nick]\" SIZE=$input_breite>"
			. "</TD></TR>\n";
	}
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige6']
			. "</b><br>\n" . $f2
			. "<INPUT TYPE=\"TEXT\" VALUE=\"$f[u_email]\" NAME=\"f[u_email]\" SIZE=$input_breite>"
			. "</TD></TR>\n";
	}
	
	// Nur für Admins
	if ($admin) {
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige3']
			. "</b><br>\n" . $f2
			. "<INPUT TYPE=\"TEXT\" VALUE=\"$f[u_adminemail]\" NAME=\"f[u_adminemail]\" SIZE=$input_breite>"
			. "</TD></TR>\n";
	} else if (($einstellungen_aendern) && ($u_level == 'U')) {
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige3']
			. "</b> (<a href=\"edit.php?http_host=$http_host&id=$id&aktion=andereadminmail\">ändern</a>)<br>\n"
			. $f2 . htmlspecialchars($f['u_adminemail'])
			. "</TD></TR>\n";
	}
	
	if ($admin) {
		if (!isset($f['u_kommentar']))
			$f['u_kommentar'] = "";
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige49']
			. "</b><br>\n" . $f2 . "<INPUT TYPE=\"TEXT\" VALUE=\""
			. htmlspecialchars($f['u_kommentar'])
			. "\" NAME=\"f[u_kommentar]\" SIZE=$input_breite>" . "</TD></TR>\n";
	}
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige7']
			. "</b><br>\n" . $f2
			. "<INPUT TYPE=\"TEXT\" VALUE=\"$f[u_url]\" NAME=\"f[u_url]\" SIZE=$input_breite>"
			. "</TD></TR>\n";
		
		// Signatur
			$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige44']
			. "</b><br>\n" . $f2 . "<INPUT TYPE=\"TEXT\" VALUE=\""
			. htmlspecialchars($f['u_signatur'])
			. "\" NAME=\"f[u_signatur]\" SIZE=$input_breite>" . "</TD></TR>\n";
		
		if ($eintritt_individuell == "1") {
			// Eintrittsnachricht
			$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige53']
				. "</b><br>\n" . $f2 . "<INPUT TYPE=\"TEXT\" VALUE=\""
				. htmlspecialchars($f['u_eintritt'])
				. "\" NAME=\"f[u_eintritt]\" SIZE=$input_breite MAXLENGTH=\"100\">"
				. "</TD></TR>\n";
			// Austrittsnachricht
			$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige54']
				. "</b><br>\n" . $f2 . "<INPUT TYPE=\"TEXT\" VALUE=\""
				. htmlspecialchars($f['u_austritt'])
				. "\" NAME=\"f[u_austritt]\" SIZE=$input_breite MAXLENGTH=\"100\">"
				. "</TD></TR>\n";
		}
		
		// Passwort
		if ($einstellungen_aendern) {
			$text .= "<TR><TD COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige19']
				. "</b><br>\n" . $f2
				. "<INPUT TYPE=\"PASSWORD\" NAME=\"passwort1\" SIZE=$passwort_breite>"
				. "<INPUT TYPE=\"PASSWORD\" NAME=\"passwort2\" SIZE=$passwort_breite>"
				. "</TD></TR>\n";
		}
	}
	
	// System Ein/Austrittsnachrichten Y/N
	$text .= "<TR><TD COLSPAN=2><HR SIZE=2 NOSHADE></TD></TR>\n";
	$text .= "<TR><TD>" . $f1 . "<b>" . $t['user_zeige51'] . "</b>\n" . $f2
		. "</TD><TD>" . $f1 . "<SELECT NAME=\"f[u_systemmeldungen]\">";
	if ($f['u_systemmeldungen'] == "Y") {
		$text .= "<OPTION SELECTED VALUE=\"Y\">$t[user_zeige36]";
		$text .= "<OPTION VALUE=\"N\">$t[user_zeige37]";
	} else {
		$text .= "<OPTION VALUE=\"Y\">$t[user_zeige36]";
		$text .= "<OPTION SELECTED VALUE=\"N\">$t[user_zeige37]";
	}
	$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
	
	// Smilies Y/N
	if ($smilies_pfad && $erweitertefeatures) {
		$text .= "<TR><TD>" . $f1 . "<b>" . $t['user_zeige35'] . "</b>\n" . $f2
			. "</TD><TD>" . $f1 . "<SELECT NAME=\"f[u_smilie]\">";
		if ($f['u_smilie'] == "Y") {
			$text .= "<OPTION SELECTED VALUE=\"Y\">$t[user_zeige36]";
			$text .= "<OPTION VALUE=\"N\">$t[user_zeige37]";
		} else {
			$text .= "<OPTION VALUE=\"Y\">$t[user_zeige36]";
			$text .= "<OPTION SELECTED VALUE=\"N\">$t[user_zeige37]";
		}
		$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
	}
	
	// Punkte Anzeigen Y/N
	if ($communityfeatures && $u_level <> 'G' && $punktefeatures) {
		$text .= "<TR><TD>" . $f1 . "<b>" . $t['user_zeige52'] . "</b>\n" . $f2
			. "</TD><TD>" . $f1 . "<SELECT NAME=\"f[u_punkte_anzeigen]\">";
		if ($f['u_punkte_anzeigen'] == "Y") {
			$text .= "<OPTION SELECTED VALUE=\"Y\">$t[user_zeige36]";
			$text .= "<OPTION VALUE=\"N\">$t[user_zeige37]";
		} else {
			$text .= "<OPTION VALUE=\"Y\">$t[user_zeige36]";
			$text .= "<OPTION SELECTED VALUE=\"N\">$t[user_zeige37]";
		}
		$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
	}
	
	// Level nur für Admins
	if ($admin) {
		$text .= "<TR><TD>" . $f1 . "<b>" . $t['user_zeige8'] . "</b>\n" . $f2
			. "</TD><TD>" . $f1 . "<SELECT NAME=\"f[u_level]\">\n";
		
		// Liste der Gruppen ausgeben
		
		reset($level);
		$i = 0;
		while ($i < count($level)) {
			$name = key($level);
			// Alle Level außer Besitzer zur Auswahl geben, für Gäste gibt es nur Gast
			if ($name != "B") {
				if ($f['u_level'] == "G") {
					if ($i == 0) {
						$text .= "<OPTION SELECTED VALUE=\"G\">$level[G]\n";
					}
				} else {
					if ($name != "G") {
						if ($f['u_level'] == $name) {
							$text .= "<OPTION SELECTED VALUE=\"$name\">$level[$name]\n";
						} else {
							$text .= "<OPTION VALUE=\"$name\">$level[$name]\n";
						}
					}
				}
			}
			next($level);
			$i++;
		}
		$text .= "</SELECT>" . $f2 . "</TD></TR>\n";
	}
	
	// Einstellungen für Fenstergrößen
	if ($u_level != "G") {
		$text .= "<tr><TD colspan=\"2\"><hr size=\"2\" noshade>" . $f1 . "<b>" . $t['user_zeige43'] . "</b>\n" . $f2 . "</td></tr>\n";
		foreach ($frame_size['def'] as $key => $val) {
			$text .= "<tr><td>" . $f1 . "<b>" . $t[$key] . "</b>\n" . $f2
				. "</td><td>" . $f1
				. "<input type=\"text\" name=\"size[$key]\" size=\"4\" value=\"$size[$key]\">&nbsp;"
				. str_replace("%vor%", $val, $t['user_zeige42']) . $f2
				. "</td></tr>\n";
		}
	}
	
	// Default für Farbe setzen, falls undefiniert
	if (strlen($f['u_farbe']) == 0) {
		$f['u_farbe'] = $user_farbe;
	}
	
	$link = "";
	// Farbe direkt einstellen
	if ($f['u_id'] == $u_id) {
		if ($communityfeatures) {
			$url = "home_farben.php?http_host=$http_host&id=$id&mit_grafik=0&feld=u_farbe&bg=Y&oldcolor="
				. urlencode($f['u_farbe']);
			$link = "<b>[<a href=\"$url\" target=\"Farben\" onclick=\"window.open('$url','Farben','resizable=yes,scrollbars=yes,width=400,height=500'); return(false);\">$t[user_zeige46]</A>]</b>";
		}
		$text .= "<TR><TD COLSPAN=2><HR SIZE=2 NOSHADE></TD></TR>"
			. "<TR><TD>$f1<b>" . $t['user_zeige45'] . "</b>\n" . $f2
			. "</TD><TD>" . $f1
			. "<INPUT TYPE=\"TEXT\" NAME=\"f[u_farbe]\" SIZE=7 VALUE=\"$f[u_farbe]\">"
			. "<INPUT TYPE=\"HIDDEN\" NAME=\"farben[u_farbe]\">" . $f2
			. "&nbsp;" . $f3 . $link . $f4 . "</TD></TR>\n";
	} else if ($admin) {
		$text .= "<TR><TD COLSPAN=2><HR SIZE=2 NOSHADE></TD></TR>"
			. "<TR><TD>$f1<b>" . $t['user_zeige45'] . "</b>\n" . $f2
			. "</TD><TD>" . $f1
			. "<INPUT TYPE=\"TEXT\" NAME=\"f[u_farbe]\" SIZE=7 VALUE=\"$f[u_farbe]\">"
			. "<INPUT TYPE=\"HIDDEN\" NAME=\"farben[u_farbe]\">" . $f2
			. "&nbsp;" . $f3 . $link . $f4 . "</TD></TR>\n";
	}
	
	$text .= "</TABLE>\n";
	
	$text .= $f1
		. "<HR SIZE=2 NOSHADE><INPUT TYPE=\"SUBMIT\" NAME=\"eingabe\" VALUE=\"Ändern!\">"
		. $f2;
	
	if ($admin) {
		$text .= $f1
			. "&nbsp;<INPUT TYPE=\"SUBMIT\" NAME=\"eingabe\" VALUE=\"Löschen!\">"
			. $f2;
	}
	
	// Farbenliste & aktuelle Farbe
	
	if ($f['u_id'] == $u_id) {
		$text .= "\n<HR SIZE=2 NOSHADE><TABLE><tr><TD COLSPAN=2><b>"
			. $t['user_zeige10'] . "&nbsp;</b></TD>" . "<TD BGCOLOR=\"#"
			. $f['u_farbe'] . "\">&nbsp;&nbsp;&nbsp;</TD>" . "</TR></TABLE>";
		$text .= "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR>\n";
		foreach ($farbe_chat_user as $key => $val) {
			$text .= "<TD WIDTH=$farbe_chat_user_groesse " . "BGCOLOR=\"#" . $val
				. "\">"
				. "<a href=\"edit.php?http_host=$http_host&id=$id&aktion=edit&f[u_id]=$f[u_id]&farbe=$val\">"
				. "<IMG SRC=\"pics/fuell.gif\" WIDTH=$farbe_chat_user_groesse "
				. "HEIGHT=$farbe_chat_user_groesse ALT=\"\" BORDER=0></A></TD>\n";
		}
		$text .= "</TR></TABLE>\n";
	}
	
	// Fuß der Tabelle
	$text .= "</FORM>\n";
	
	// Box anzeigen
	show_box_title_content($box, $text);
	
}
?>