<?php

function formular_neue_email($neue_email, $m_id = "") {
	// Gibt Formular für den Benutzernamen zum Versand einer Mail aus
	
	global $id, $PHP_SELF, $f1, $f2, $mysqli_link;
	
	// Benutzername aus u_nick lesen und setzen
	if (isset($neue_email['m_an_uid']) && $neue_email['m_an_uid']) {
		$query = "select `u_nick` FROM `user` WHERE `u_id` = " . intval($neue_email[m_an_uid]);
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$an_nick = mysqli_result($result, 0, 0);
			if (strlen($an_nick) > 0) {
				$neue_email['an_nick'] = $an_nick;
			}
		}
	}
	
	if ($m_id) {
		$box = "Mail weiterleiten an:";
	} else {
		$box = "Neue Mail senden:";
	}
	$text = '';
	
	$text .= "<form name=\"mail_neu\" action=\"$PHP_SELF\" method=POST>\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"neu2\">\n"
		. "<input type=\"hidden\" name=\"m_id\" value=\"$m_id\">\n";
	
	if (!isset($neue_email['an_nick'])) {
	$neue_email['an_nick'] = "";
	}
	$text .= "<b>Benutzername:</b> "
		. $f1 . "<input type=\"text\" name=\"neue_email[an_nick]\" value=\"" . $neue_email['an_nick'] . "\" size=20>" . "&nbsp;"
		. "<input type=\"submit\" name=\"los\" value=\"Weiter\">" . $f2;
	
	// Box anzeigen
	show_box_title_content($box, $text);
}

function formular_neue_email2($neue_email, $m_id = "") {
	// Gibt Formular zum Versand einer neuen Mail aus
	
	global $id, $PHP_SELF, $f1, $f2, $mysqli_link, $u_id, $t;
	global $PHP_SELF;
	global $u_punkte_gesamt;
	
	$eingabe_breite1 = 87;
	$eingabe_breite2 = 75;
	
	if (!$eingabe_breite1) {
		$eingabe_breite1 = 30;
	}
	if (!$eingabe_breite2) {
		$eingabe_breite2 = 40;
	}
	
	echo '<script language="JavaScript">
	function zaehle()
	{
	betr=document.mail_neu.elements["neue_email[m_betreff]"].value.length;
	text=document.mail_neu.elements["neue_email[m_text]"].value.length;
	document.mail_neu.counter.value=betr+text;
	}
	</script>
	';
	
	$text = '';
	if ($m_id) {
		// Alte Mail lesen und als Kopie in Formular schreiben
		$box = "Nachricht weiterleiten an ";
		$query = "select m_betreff,m_text from mail "
			. "where m_id=" . intval($m_id) . " AND m_an_uid=$u_id";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
			
			$neue_email['m_betreff'] = $row->m_betreff . " [Weiterleitung]";
			$neue_email['m_text'] = $row->m_text;
			
			$neue_email['m_text'] = str_replace("<b>", "_",
				$neue_email['m_text']);
			$neue_email['m_text'] = str_replace("</b>", "_",
				$neue_email['m_text']);
			
			$neue_email['m_text'] = str_replace("<i>", "*",
				$neue_email['m_text']);
			$neue_email['m_text'] = str_replace("</i>", "*",
				$neue_email['m_text']);
			
		}
		mysqli_free_result($result);
		
	} else {
		// Neue Mail versenden
		$box = "Neue Nachricht an ";
	}
	
	// Signatur anfügen
	if (!isset($neue_email['m_text']))
		$neue_email['m_text'] = htmlspecialchars(erzeuge_fuss(""));
	
	// Benutzerdaten aus u_id lesen und setzen
	if ($neue_email['m_an_uid']) {
		$query = "SELECT u_nick,u_email,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, date_format(u_login,'%d.%m.%y %H:%i') AS login, "
			. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online FROM user LEFT JOIN online ON o_user=u_id WHERE u_id = ".$neue_email['m_an_uid']." ";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_object($result);
			$box .= zeige_userdetails($row->u_id, $row, TRUE, "&nbsp;", $row->online, $row->login);
			
			$email = "";
			$email_select = "&nbsp;";
			// E-Mail Adresse vorhanden?
			$email_bekannt = false;
			if (strlen($row->u_email) > 0) {
				$email = $f1 . " (E-Mail: <a href=\"mailto:$row->u_email\">" . htmlspecialchars($row->u_email) . "</a>)" . $f2;
				$email_bekannt = true;
			}
			
			$email_select = "<b>$t[nachrichten3]</b>&nbsp;<select name=\"neue_email[typ]\">";
			if (isset($neue_email['typ']) && $neue_email['typ'] == 1) {
				$email_select .= "<option value=\"0\">$t[nachrichten4]\n";
				if ($email_bekannt) {
					$email_select .= "<option selected value=\"1\">E-Mail an " . $row->u_email . "\n";
				}
			} else {
				$email_select .= "<option selected value=\"0\">$t[nachrichten4]\n";
				if ($email_bekannt)
					$email_select .= "<option value=\"1\">E-Mail an " . $row->u_email . "\n";
			}
			$email_select .= "</select>\n";
			
			$text .= "<form name=\"mail_neu\" action=\"$PHP_SELF\" method=post>\n"
				. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"neu3\">\n"
				. "<input type=\"hidden\" name=\"neue_email[m_an_uid]\" value=\"$neue_email[m_an_uid]\">\n"
				. "<table style=\"width:100%;\">";
			
			if ($row->o_id == "" || $row->o_id == "NULL") {
				$row->online = "";
				}
			
			if (!isset($neue_email['m_betreff'])) {
				$neue_email['m_betreff'] = "";
			}
			
			$text .= "<tr><td style=\"vertical-align:top; text-align:right;\" class =\"tabelle_zeile2\"><b>".$t['betreff']."</b></td><td colspan=2 class =\"tabelle_zeile2\">"
			. $f1
			. "<input type=\"text\" name=\"neue_email[m_betreff]\" value=\""
			. $neue_email['m_betreff'] . "\" size="
			. ($eingabe_breite1)
			. "
			 ONCHANGE=zaehle() ONFOCUS=zaehle() ONKEYDOWN=zaehle() ONKEYUP=zaehle()>"
			. $f2 . "<input name=\"counter\" size=3></td></tr>"
			. "<tr><td style=\"vertical-align:top; text-align:right;\" class=\"tabelle_zeile1\"><b>Ihr Text:</b></td><td colspan=2 class=\"tabelle_zeile1\">"
			. $f1 . "<textarea cols=" . ($eingabe_breite2)
			. " ROWS=20 name=\"neue_email[m_text]\" ONCHANGE=zaehle() ONFOCUS=zaehle() ONKEYDOWN=zaehle() ONKEYUP=zaehle()>"
			. $neue_email['m_text'] . "</textarea>\n" . $f2
			. "</td></tr>"
			. "<tr><td class=\"tabelle_zeile2\">&nbsp;</td><td class=\"tabelle_zeile2\">$email_select</td>"
			. "<td style=\"text-align:right;\" class=\"tabelle_zeile2\">" . $f1
			. "<input type=\"submit\" name=\"los\" value=\"Versenden\">"
			. $f2 . "</td></tr>\n" . "</table></form>\n";
			
			// Box anzeigen
			show_box_title_content($box, $text);
			
		} else {
			echo "<P><b>Fehler:</b> Der Benutzer mit der ID '$neue_email[m_an_uid]' existiert nicht!</P>\n";
		}
	}
}

function zeige_mailbox($aktion, $zeilen) {
	// Zeigt die Mails in der Übersicht an
	
	global $id, $mysqli_link, $PHP_SELF, $f1, $f2, $mysqli_link, $u_nick, $u_id, $chat, $t;
	
	$eingabe_breite = 55;
	
	if (!$eingabe_breite) {
		$eingabe_breite = 30;
	}
	
	switch ($aktion) {
		case "alle";
			$query = "SELECT m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') AS zeit,m_betreff,u_nick "
				. "FROM mail LEFT JOIN user on m_von_uid=u_id WHERE m_an_uid=$u_id ORDER BY m_zeit desc";
				$button = $t['loeschen'];
			$titel = $t['nachrichten1'];
			break;
		
		case "geloescht":
			$query = "select m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') AS zeit,m_betreff,u_nick "
				. "FROM mail LEFT JOIN user on m_von_uid=u_id WHERE m_an_uid=$u_id AND m_status='geloescht' ORDER BY m_zeit desc";
			$button = $t['wiederherstellen'];
			$titel = $t['nachrichten2'];
			break;
		
		case "normal":
		default;
			$query = "select m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') AS zeit,m_betreff,u_nick "
				. "FROM mail LEFT JOIN user on m_von_uid=u_id WHERE m_an_uid=$u_id AND m_status!='geloescht' ORDER BY m_zeit desc";
			$button = $t['loeschen'];
			$titel = $t['nachrichten1'];
	}
	
	$result = mysqli_query($mysqli_link, $query);
	if ($result) {
		$anzahl = mysqli_num_rows($result);
		$text = '';
		$box = "$anzahl $titel";
		
		$text .= "<form name=\"mailbox\" action=\"$PHP_SELF\" method=\"post\">\n"
				. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
				. "<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n";
		
		if ($anzahl == 0) {
			// Leere Mailbox
			$text .=  "Ihre Mailbox ist leer.";
			
		} else {
			// Mails anzeigen
			$text .= "<table style=\"width:100%;\">\n"
				. "<tr>"
				."<td style=\"font-weight:bold; width:30px; text-align:center;\"><input type=\"checkbox\" onClick=\"toggle(this.checked)\"></td>"
				."<td style=\"font-weight:bold; width:40px; text-align:center;\">" . $f1 . $t['status'] . $f2 . "</td>"
				."<td style=\"font-weight:bold;\">" . $f1 . $t['von'] . $f2 . "</td>"
				."<td style=\"font-weight:bold;\">" . $f1 . $t['betreff'] . $f2 . "</td>"
				."<td style=\"font-weight:bold;\">" . $f1 . $t['datum'] . $f2 . "</td>"
				."</tr>\n";
				
			
			$i = 0;
			$bgcolor = 'class="tabelle_zeile1"';
			while ($row = mysqli_fetch_object($result)) {
				
				$url = "<a href=\"" . $PHP_SELF
					. "?id=$id&aktion=zeige&m_id="
					. $row->m_id . "\">";
				if ($row->m_status == "neu" || $row->m_status == "neu/verschickt") {
					$auf = "<b>" . $f1;
					$zu = $f2 . "</b>";
					$status = "<span class=\"fa fa-commenting icon24\" alt=\"Neue Nachrichten\" title=\"Neue Nachrichten\"></span>";
				} else {
					$auf = $f1;
					$zu = $f2;
					$status = "<span class=\"fa fa-commenting-o icon24\" alt=\"Keine neuen Nachrichten\" title=\"Keine neuen Nachrichten\"></span>";
				}
				
				if ($row->u_nick == "NULL" || $row->u_nick == "") {
					$von_nick = "$chat";
				} else {
					$von_nick = $row->u_nick;
				}
				
				$text .= "<tr>"
					."<td style=\"text-align:center;\" $bgcolor>" . $auf . "<input type=\"checkbox\" name=\"loesche_email[]\" value=\"" . $row->m_id . "\">" . $zu . "</td>"
					. "<td $bgcolor style=\"text-align:center; padding-left: 5px;\">" . $status . "</td>"
					. "<td $bgcolor>" . $auf . $url . $von_nick . "</a>" . $zu . "</td>" . "<td style=\"width:50%;\" $bgcolor>" . $auf . $url . htmlspecialchars($row->m_betreff) . "</a>" . $zu . "</td>"
					. "<td $bgcolor>" . $auf . $row->zeit . $zu . "</td>"
					. "</tr>\n";
				
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
				}
				$i++;
			}
			
			$text .= "<tr><td $bgcolor colspan=\"5\"><input type=\"submit\" name=\"los\" value=\"$button\"></td>\n"
				. "</tr></table>\n";
		}
		
		$text .= "</form>\n";
		
		// Box anzeigen
		show_box_title_content($box, $text);
	}
}

function zeige_email($m_id) {
	// Zeigt die Mail im Detail an
	
	global $id, $mysqli_link, $PHP_SELF, $f1, $f2, $mysqli_link, $u_nick, $u_id, $chat, $t;
	
	$query = "select mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe "
		. "FROM mail LEFT JOIN user on m_von_uid=u_id "
		. "WHERE m_an_uid=$u_id AND m_id=" . intval($m_id) . " order by m_zeit desc";
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) == 1) {
		
		// Mail anzeigen
		$row = mysqli_fetch_object($result);
		
		if ($row->u_nick == "NULL" || $row->u_nick == "") {
			$von_nick = "$chat";
		} else {
			$von_nick = "<b>" . zeige_userdetails($row->u_id, $row) . "</b>";
		}
		$row->m_text = str_replace("<ID>", $id, $row->m_text);
		
		$text = '';
		$box = htmlspecialchars($row->m_betreff);
		
		// Mail ausgeben
		$text .= "<table style=\"width:100%;\">";
		$text .= "<tr>"
			."<td style=\"text-align:right; width:200px;\" class=\"tabelle_zeile1\"><b>" . $f1 . "Von" . $f2 . "</b></td>"
			."<td colspan=3 class=\"tabelle_zeile1\">" . $von_nick . "</td>"
			."</tr>\n"
			. "<tr>"
			."<td style=\"text-align:right;\" class=\"tabelle_zeile2\"><b>" . $f1 . "Am" . $f2 . "</b></td>"
			."<td colspan=3 class=\"tabelle_zeile2\">" . $f1 . $row->zeit . $f2 . "</td>"
			."</tr>\n"
			. "<tr><td class=\"tabelle_zeile1\">&nbsp;</td><td colspan=3 class=\"tabelle_zeile1\">"
			. $f1 . str_replace("\n", "<br>\n", $row->m_text)
			. $f2 . "</td></tr>\n";
		
		// Formular zur Löschen, Beantworten
		$text .= "<tr><td class=\"tabelle_zeile1\">&nbsp;</td>"
			. "<td style=\"text-align:left;\" class=\"tabelle_zeile1\"><form name=\"mail_antworten\" action=\"$PHP_SELF\" method=\"post\">" . $f1
			. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
			. "<input type=\"hidden\" name=\"m_id\" value=\"" . $row->m_id . "\">\n"
			. "<input type=\"hidden\" name=\"aktion\" value=\"antworten\">\n"
			. "<input type=\"SUBMIT\" name=\"los\" value=\"antworten\">" . $f2 . "</form></td>\n"
			. "<td style=\"text-align:center;\" class=\"tabelle_zeile1\"><form name=\"mail_antworten\" action=\"$PHP_SELF\" method=\"post\">" . $f1
			. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
			. "<input type=\"hidden\" name=\"m_id\" value=\"" . $row->m_id . "\">\n"
			. "<input type=\"hidden\" name=\"aktion\" value=\"weiterleiten\">\n"
			. "<input type=\"SUBMIT\" name=\"los\" value=\"weiterleiten\">" . $f2 . "</form></td>\n"
			. "<td style=\"text-align:right;\" class=\"tabelle_zeile1\"><form name=\"mail_loeschen\" action=\"$PHP_SELF\" method=\"post\">" . $f1
			. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
			. "<input type=\"hidden\" name=\"loesche_email\" value=\"" . $row->m_id . "\">\n"
			. "<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n"
			. "<input type=\"submit\" name=\"los\" value=\"".$t['loeschen']."\">" . $f2
			. "</form></td></tr>\n";
		
		$text .= "</table>\n";
		
		// Box anzeigen
		show_box_title_content($box, $text);
		
		if ($row->m_status != "geloescht") {
			// E-Mail auf gelesen setzen
			unset($f);
			$f['m_status'] = "gelesen";
			$f['m_zeit'] = $row->m_zeit;
			schreibe_db("mail", $f, $row->m_id, "m_id");
		}
	}
}

function loesche_mail($m_id, $u_id) {
	
	// Löscht eine Mail der ID m_id
	
	global $id, $PHP_SELF, $mysqli_link, $u_nick, $u_id;
	
	$query = "select m_zeit,m_id,m_status FROM mail "
		. "WHERE m_id=" . intval($m_id) . " AND m_an_uid=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) == 1) {
		
		$row = mysqli_fetch_object($result);
		$f['m_zeit'] = $row->m_zeit;
		
		if ($row->m_status != "geloescht") {
			$f['m_status'] = "geloescht";
			$f['m_geloescht_ts'] = date("YmdHis");
		} else {
			$f['m_status'] = "gelesen";
			$f['m_geloescht_ts'] = '00000000000000';
		}
		;
		schreibe_db("mail", $f, $row->m_id, "m_id");
	} else {
		echo "<P><b>Fehler:</b> Diese Mail nicht kann nicht gelöscht werden!</P>";
	}
	mysqli_free_result($result);
	
}

?>