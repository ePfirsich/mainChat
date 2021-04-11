<?php

require_once("functions.php-func-verlasse_chat.php");
require_once("functions.php-func-nachricht.php");

function user_edit($f, $admin, $u_level, $size = ARRAY()) {
	// $f = Ass. Array mit Userdaten
	// $size = Ass. Array mit Fenstereinstellungen (Optional)
	
	global $id, $http_host, $level, $f1, $f2, $f3, $f4;
	global $farbe_chat_user, $farbe_chat_user_groesse, $user_farbe;
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
	
	// Ausgabe in Tabelle
	$text .= "<form name=\"$f[u_nick]\" ACTION=\"edit.php\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n"
		. "<input type=\"hidden\" name=\"f[u_id]\" value=\"$f[u_id]\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"edit\">\n";
	
	$text .= "<table style=\"width:100%;\">";
	
	// Backup-Algotithmus einschalten?
	$text .= "<tr><td colspan=2>" . $f1 . $t['user_zeige14']
		. "<select name=\"f[u_backup]\">";
	if ($backup_chat) {
		$text .= "<option value=\"0\">$t[user_zeige15]";
	} elseif ($f['u_backup'] == 1) {
		$text .= "<option selectED value=\"1\">$t[user_zeige15]";
		$text .= "<option value=\"0\">$t[user_zeige16]";
	} else {
		$text .= "<option value=\"1\">$t[user_zeige15]";
		$text .= "<option selectED value=\"0\">$t[user_zeige16]";
	}
	
	$text .= "</select><input type=\"submit\" name=\"eingabe\" value=\"Ändern!\">"
		. $f2 . "<HR SIZE=2 NOSHADE></td></tr>\n";
	
	// Nur für Admins
	if ($admin) {
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige17']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_name]\" name=\"f[u_name]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	} else if (($einstellungen_aendern) && ($u_level == 'U')) {
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige17']
			. "</b> (<a href=\"edit.php?http_host=$http_host&id=$id&aktion=andereadminmail\">ändern</a>)<br>\n"
			. $f2 . htmlspecialchars($f['u_name'])
			. "</td></tr>\n";
	}
	
	if (!$einstellungen_aendern) {
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige18']
			. "</b>&nbsp;&nbsp;\n" . $f['u_nick'] . $f2 . "</td></tr>\n";
	} else {
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige18']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_nick]\" name=\"f[u_nick]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	}
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige6']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_email]\" name=\"f[u_email]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	}
	
	// Nur für Admins
	if ($admin) {
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige3']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_adminemail]\" name=\"f[u_adminemail]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	} else if (($einstellungen_aendern) && ($u_level == 'U')) {
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige3']
			. "</b> (<a href=\"edit.php?http_host=$http_host&id=$id&aktion=andereadminmail\">ändern</a>)<br>\n"
			. $f2 . htmlspecialchars($f['u_adminemail'])
			. "</td></tr>\n";
	}
	
	if ($admin) {
		if (!isset($f['u_kommentar']))
			$f['u_kommentar'] = "";
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige49']
			. "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\""
			. htmlspecialchars($f['u_kommentar'])
			. "\" name=\"f[u_kommentar]\" SIZE=$input_breite>" . "</td></tr>\n";
	}
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige7']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_url]\" name=\"f[u_url]\" SIZE=$input_breite>"
			. "</td></tr>\n";
		
		// Signatur
			$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige44']
			. "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\""
			. htmlspecialchars($f['u_signatur'])
			. "\" name=\"f[u_signatur]\" SIZE=$input_breite>" . "</td></tr>\n";
		
		if ($eintritt_individuell == "1") {
			// Eintrittsnachricht
			$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige53']
				. "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\""
				. htmlspecialchars($f['u_eintritt'])
				. "\" name=\"f[u_eintritt]\" SIZE=$input_breite MAXLENGTH=\"100\">"
				. "</td></tr>\n";
			// Austrittsnachricht
			$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige54']
				. "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\""
				. htmlspecialchars($f['u_austritt'])
				. "\" name=\"f[u_austritt]\" SIZE=$input_breite MAXLENGTH=\"100\">"
				. "</td></tr>\n";
		}
		
		// Passwort
		if ($einstellungen_aendern) {
			$text .= "<tr><td COLSPAN=2>" . $f1 . "<b>" . $t['user_zeige19']
				. "</b><br>\n" . $f2
				. "<input type=\"PASSWORD\" name=\"passwort1\" SIZE=$passwort_breite>"
				. "<input type=\"PASSWORD\" name=\"passwort2\" SIZE=$passwort_breite>"
				. "</td></tr>\n";
		}
	}
	
	// System Ein/Austrittsnachrichten Y/N
	$text .= "<tr><td COLSPAN=2><HR SIZE=2 NOSHADE></td></tr>\n";
	$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige51'] . "</b>\n" . $f2
		. "</td><td>" . $f1 . "<select name=\"f[u_systemmeldungen]\">";
	if ($f['u_systemmeldungen'] == "Y") {
		$text .= "<option selectED value=\"Y\">$t[user_zeige36]";
		$text .= "<option value=\"N\">$t[user_zeige37]";
	} else {
		$text .= "<option value=\"Y\">$t[user_zeige36]";
		$text .= "<option selectED value=\"N\">$t[user_zeige37]";
	}
	$text .= "</select>" . $f2 . "</td></tr>\n";
	
	// Smilies Y/N
	if ($smilies_pfad && $erweitertefeatures) {
		$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige35'] . "</b>\n" . $f2
			. "</td><td>" . $f1 . "<select name=\"f[u_smilie]\">";
		if ($f['u_smilie'] == "Y") {
			$text .= "<option selectED value=\"Y\">$t[user_zeige36]";
			$text .= "<option value=\"N\">$t[user_zeige37]";
		} else {
			$text .= "<option value=\"Y\">$t[user_zeige36]";
			$text .= "<option selectED value=\"N\">$t[user_zeige37]";
		}
		$text .= "</select>" . $f2 . "</td></tr>\n";
	}
	
	// Punkte Anzeigen Y/N
	if ($communityfeatures && $u_level <> 'G' && $punktefeatures) {
		$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige52'] . "</b>\n" . $f2
			. "</td><td>" . $f1 . "<select name=\"f[u_punkte_anzeigen]\">";
		if ($f['u_punkte_anzeigen'] == "Y") {
			$text .= "<option selectED value=\"Y\">$t[user_zeige36]";
			$text .= "<option value=\"N\">$t[user_zeige37]";
		} else {
			$text .= "<option value=\"Y\">$t[user_zeige36]";
			$text .= "<option selectED value=\"N\">$t[user_zeige37]";
		}
		$text .= "</select>" . $f2 . "</td></tr>\n";
	}
	
	// Level nur für Admins
	if ($admin) {
		$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige8'] . "</b>\n" . $f2
			. "</td><td>" . $f1 . "<select name=\"f[u_level]\">\n";
		
		// Liste der Gruppen ausgeben
		
		reset($level);
		$i = 0;
		while ($i < count($level)) {
			$name = key($level);
			// Alle Level außer Besitzer zur Auswahl geben, für Gäste gibt es nur Gast
			if ($name != "B") {
				if ($f['u_level'] == "G") {
					if ($i == 0) {
						$text .= "<option selectED value=\"G\">$level[G]\n";
					}
				} else {
					if ($name != "G") {
						if ($f['u_level'] == $name) {
							$text .= "<option selectED value=\"$name\">$level[$name]\n";
						} else {
							$text .= "<option value=\"$name\">$level[$name]\n";
						}
					}
				}
			}
			next($level);
			$i++;
		}
		$text .= "</select>" . $f2 . "</td></tr>\n";
	}
	
	// Einstellungen für Fenstergrößen
	if ($u_level != "G") {
		$text .= "<tr><td colspan=\"2\"><hr size=\"2\" noshade>" . $f1 . "<b>" . $t['user_zeige43'] . "</b>\n" . $f2 . "</td></tr>\n";
		foreach ($frame_size as $key => $val) {
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
		$text .= "<tr><td COLSPAN=2><HR SIZE=2 NOSHADE></td></tr>"
			. "<tr><td>$f1<b>" . $t['user_zeige45'] . "</b>\n" . $f2
			. "</td><td>" . $f1
			. "<input type=\"TEXT\" name=\"f[u_farbe]\" SIZE=7 value=\"$f[u_farbe]\">"
			. "<input type=\"hidden\" name=\"farben[u_farbe]\">" . $f2
			. "&nbsp;" . $f3 . $link . $f4 . "</td></tr>\n";
	} else if ($admin) {
		$text .= "<tr><td COLSPAN=2><HR SIZE=2 NOSHADE></td></tr>"
			. "<tr><td>$f1<b>" . $t['user_zeige45'] . "</b>\n" . $f2
			. "</td><td>" . $f1
			. "<input type=\"TEXT\" name=\"f[u_farbe]\" SIZE=7 value=\"$f[u_farbe]\">"
			. "<input type=\"hidden\" name=\"farben[u_farbe]\">" . $f2
			. "&nbsp;" . $f3 . $link . $f4 . "</td></tr>\n";
	}
	
	$text .= "</table>\n";
	
	$text .= $f1
		. "<HR SIZE=2 NOSHADE><input type=\"SUBMIT\" name=\"eingabe\" value=\"Ändern!\">"
		. $f2;
	
	if ($admin) {
		$text .= $f1
			. "&nbsp;<input type=\"SUBMIT\" name=\"eingabe\" value=\"Löschen!\">"
			. $f2;
	}
	
	// Farbenliste & aktuelle Farbe
	
	if ($f['u_id'] == $u_id) {
		$text .= "\n<HR SIZE=2 NOSHADE><table><tr><td colspan=\"2\"><b>"
			. $t['user_zeige10'] . "&nbsp;</b></td>" . "<td style=\"background-color:#". $f['u_farbe'] . ";\">&nbsp;&nbsp;&nbsp;</td>" . "</tr></table>";
		$text .= "<table style=\"border-collapse: collapse;\"><tr>\n";
		foreach ($farbe_chat_user as $key => $val) {
			$text .= "<td WIDTH=$farbe_chat_user_groesse " . "BGCOLOR=\"#" . $val
				. "\">"
				. "<a href=\"edit.php?http_host=$http_host&id=$id&aktion=edit&f[u_id]=$f[u_id]&farbe=$val\">"
				. "<img src=\"pics/fuell.gif\" style=\"width:" . $farbe_chat_user_groesse . "px; height:" . $farbe_chat_user_groesse . "; border:0px;\" alt=\"\"></a></td>\n";
		}
		$text .= "</tr></table>\n";
	}
	
	// Fuß der Tabelle
	$text .= "</form>\n";
	
	// Box anzeigen
	show_box_title_content($box, $text);
	
}
?>