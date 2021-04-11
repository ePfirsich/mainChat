<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

// Sonderzeichen aus dem Target raus

$fenster = str_replace("+", "", $u_nick);
$fenster = str_replace("-", "", $fenster);
$fenster = str_replace("ä", "", $fenster);
$fenster = str_replace("ö", "", $fenster);
$fenster = str_replace("ü", "", $fenster);
$fenster = str_replace("Ä", "", $fenster);
$fenster = str_replace("Ö", "", $fenster);
$fenster = str_replace("Ü", "", $fenster);
$fenster = str_replace("ß", "", $fenster);

$title = $body_titel . ' - Mail';
zeige_header_anfang($title, 'mini');
?>
<script>
		window.focus()
		function win_reload(file,win_name) {
				win_name.location.href=file;
		}
		function opener_reload(file,frame_number) {
				opener.parent.frames[frame_number].location.href=file;
		}
		function neuesFenster(url,name) {
				hWnd=window.open(url,name,"resizable=yes,scrollbars=yes,width=300,height=580");
		}
		function neuesFenster2(url) {
				hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
		}
		function toggle(tostat ) {
				for(i=0; i<document.forms["mailbox"].elements.length; i++) {
					 e = document.forms["mailbox"].elements[i];
					 if ( e.type=='checkbox' )
						 e.checked=tostat;
				}
		}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$eingabe_breite = 55;
$eingabe_breite1 = 87;
$eingabe_breite2 = 75;

if ($u_id && $communityfeatures && $u_level != "G") {
	
	// Löscht alle Mails, die älter als $mailloescheauspapierkorb Tage sind
	if ($mailloescheauspapierkorb < 1)
		$mailloescheauspapierkorb = 14;
	$query2 = "DELETE FROM mail WHERE m_an_uid = $u_id AND m_status = 'geloescht' AND m_geloescht_ts < '"
		. date("YmdHis",
			mktime(0, 0, 0, date("m"), date("d") - intval($mailloescheauspapierkorb),
				date("Y"))) . "'";
	$result2 = mysqli_query($mysqli_link, $query2);
	
	// Menü als erstes ausgeben
	$box = "Menü Mail";
	$text = "<a href=\"mail.php?http_host=$http_host&id=$id&aktion=\">Mailbox neu laden</a>\n|\n"
		. "<a href=\"mail.php?http_host=$http_host&id=$id&aktion=neu\">Neue Mail schreiben</a>\n|\n"
		. "<a href=\"mail.php?http_host=$http_host&id=$id&aktion=papierkorb\">Papierkorb zeigen</a>\n|\n"
		. "<a href=\"mail.php?http_host=$http_host&id=$id&aktion=papierkorbleeren\">Papierkorb leeren</a>\n|\n"
		. "<a href=\"mail.php?http_host=$http_host&id=$id&aktion=mailboxzu\">Mailbox zumachen</a>\n|\n"
		. "<a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#mail\">Hilfe</a>\n";
	
	show_menue($box, $text);
	
	switch ($aktion) {
		
		case "neu":
		// Formular für neue E-Mail ausgeben
			if (!isset($neue_email))
				$neue_email = array();
			formular_neue_email($neue_email);
			break;
		
		case "neu2":
		// Mail versenden, 2. Schritt: Nick Prüfen
			$neue_email['an_nick'] = str_replace(" ", "+", $neue_email['an_nick']);
			$neue_email['an_nick'] = mysqli_real_escape_string($mysqli_link, coreCheckName($neue_email['an_nick'], $check_name));
			
			if (!isset($m_id))
				$m_id = "";
			
			$query = "SELECT `u_id`, `u_level` FROM `user` WHERE `u_nick` = '$neue_email[an_nick]'";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				$neue_email['m_an_uid'] = mysqli_result($result, 0, "u_id");
				
				$ignore = false;
				$query2 = "SELECT * FROM iignore WHERE i_user_aktiv='" . intval($neue_email['m_an_uid']) . "' AND i_user_passiv = '$u_id'";
				$result2 = mysqli_query($mysqli_link, $query2);
				$num = mysqli_num_rows($result2);
				if ($num >= 1) {
					$ignore = true;
				}
				
				$boxzu = false;
				$query = "SELECT m_id FROM mail WHERE m_von_uid='" . intval($neue_email['m_an_uid']) . "' AND m_an_uid='" . intval($neue_email['m_an_uid']) . "' and m_betreff = 'MAILBOX IST ZU' and m_status != 'geloescht'";
				$result2 = mysqli_query($mysqli_link, $query);
				$num = mysqli_num_rows($result2);
				if ($num >= 1) {
					$boxzu = true;
				}
				
				if ($boxzu == true) {
					echo "<P>$t[chat_msg105]</P>";
					formular_neue_email($neue_email, $m_id);
					break;
				}
				
				$m_u_level = mysqli_result($result, 0, "u_level");
				if ($m_u_level != "G" && $m_u_level = "Z" && $ignore != true) {
					formular_neue_email2($neue_email, $m_id);
				} else {
					echo "<P><b>Fehler:</b> An einen Gast, einen gesperrten User oder einen User, der Sie ignoriert kann "
						. "keine Mail verschickt werden!</P>\n";
					formular_neue_email($neue_email, $m_id);
				}
			} elseif ($neue_email['an_nick'] == "") {
				echo "<P><b>Fehler:</b> Bitte geben Sie einen Nicknamen an!</P>\n";
				formular_neue_email($neue_email, $m_id);
			} else {
				echo "<P><b>Fehler:</b> Der Nickname '$neue_email[an_nick]' existiert nicht!</P>\n";
				formular_neue_email($neue_email, $m_id);
			}
			@mysqli_free_result($result);
			@mysqli_free_result($result2);
			break;
		
		case "neu3":
		// Mail versenden, 3. Schritt: Inhalt Prüfen
			$ok = TRUE;
			
			// Betreff prüfen
			$neue_email['m_betreff'] = htmlspecialchars(
				$neue_email['m_betreff']);
			if (strlen($neue_email['m_betreff']) < 1) {
				echo "<b>Fehler:</b> Bitte geben Sie einen Betreff zum Versenden ein!<br>\n";
				$ok = FALSE;
			}
			if (strlen($neue_email['m_betreff']) > 254) {
				echo "<b>Fehler:</b> Bitte geben im Betreff weniger als 254 Zeichen ein!<br>\n";
				$ok = FALSE;
			}
			
			// Text prüfen
			$neue_email['m_text'] = chat_parse(
				htmlspecialchars($neue_email['m_text']));
			if (strlen($neue_email['m_text']) < 4) {
				echo "<b>Fehler:</b> Bitte geben Sie einen Text zum Versenden ein!<br>\n";
				$ok = FALSE;
			}
			if (strlen($neue_email['m_text']) > 10000) {
				echo "<b>Fehler:</b> Der Text ist zu lange!<br>\n";
				$ok = FALSE;
			}
			
			// User-ID Prüfen
			$neue_email['m_an_uid'] = intval($neue_email['m_an_uid']);
			$query = "SELECT `u_nick` FROM `user` WHERE `u_id` = $neue_email[m_an_uid]";
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				$an_nick = mysqli_result($result, 0, "u_nick");
			} else {
				echo "<b>Fehler:</b> Der User mit ID '$neue_email[m_an_uid]' existiert nicht!<br>\n";
				$ok = FALSE;
			}
			@mysqli_free_result($result);
			
			// Mail schreiben
			if ($ok) {
				
				if ($neue_email['typ'] == 1) {
					
					// E-Mail an externe Adresse versenden
					if ($neue_email['m_id'] = email_versende($u_id,
						$neue_email['m_an_uid'], $neue_email['m_text'],
						$neue_email['m_betreff'], TRUE)) {
						echo "<P>Ihre E-Mail an '$an_nick' wurde verschickt.</P>";
					} else {
						echo "<P><b>Fehler: </b>Ihre E-Mail an '$an_nick' wurde nicht verschickt.</P>";
					}
				} else {
					
					$result = mail_sende($u_id, $neue_email['m_an_uid'],
						$neue_email['m_text'], $neue_email['m_betreff']);
					if ($result[0]) {
						echo "<P>Ihre Mail an '$an_nick' wurde verschickt.</P>";
						$neue_email['m_id'] = $result[0];
					} else {
						echo "<P><b>Fehler: </b>Ihre Mail an '$an_nick' wurde nicht verschickt.</P>";
						echo "<P>$result[1]</P>";
					}
					
				}
				unset($neue_email);
				
				if (!isset($neue_email)) {
					$neue_email = array();
				}
				formular_neue_email($neue_email);
				echo '<br>';
				zeige_mailbox("normal", "");
				
			} else {
				formular_neue_email2($neue_email);
			}
			
			break;
		
		case "loesche":
		// Mails als geloescht markieren oder aufheben
		
			if (!isset($neue_email))
				$neue_email[] = "";
			formular_neue_email($neue_email);
			
			if (isset($loesche_email) && is_array($loesche_email)) {
				// Mehrere Mails auf gelöscht setzen
				foreach ($loesche_email as $m_id) {
					loesche_mail($m_id, $u_id);
				}
				
			} else {
				// Eine Mail auf gelöscht setzen
				if (isset($loesche_email))
					loesche_mail($loesche_email, $u_id);
			}
			
			zeige_mailbox("normal", "");
			break;
		
		case "antworten":
		// Mail beantworten, Mail quoten und Absender lesen
			$m_id = intval($m_id);
			$query = "SELECT *,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit "
				. "FROM mail WHERE m_an_uid=$u_id AND m_id=$m_id ";
			
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				$row = mysqli_fetch_object($result);
			}
			@mysqli_free_result($result);
			
			// Nick prüfen
			$query = "SELECT `u_id`, `u_nick` FROM `user` WHERE `u_id`=" . $row->m_von_uid;
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				
				$row2 = mysqli_fetch_object($result);
				$neue_email['m_an_uid'] = $row2->u_id;
				if (substr($row->m_betreff, 0, 3) == "Re:") {
					$neue_email['m_betreff'] = $row->m_betreff;
				} else {
					$neue_email['m_betreff'] = "Re: " . $row->m_betreff;
				}
				$neue_email['m_text'] = erzeuge_umbruch($row->m_text, 70);
				$neue_email['m_text'] = erzeuge_quoting($neue_email['m_text'],
					$row2->u_nick, $row->zeit);
				$neue_email['m_text'] = erzeuge_fuss($neue_email['m_text']);
				
				$neue_email['m_text'] = str_replace("<b>", "_",
					$neue_email['m_text']);
				$neue_email['m_text'] = str_replace("</b>", "_",
					$neue_email['m_text']);
				
				$neue_email['m_text'] = str_replace("<i>", "*",
					$neue_email['m_text']);
				$neue_email['m_text'] = str_replace("</i>", "*",
					$neue_email['m_text']);
				
				formular_neue_email2($neue_email);
				
			} elseif ($neue_email['an_nick'] == "") {
				echo "<b>Fehler:</b> Bitte geben Sie einen Nicknamen an!<br>\n";
				formular_neue_email($neue_email);
			} else {
				echo "<b>Fehler:</b> Der Nickname '$neue_email[an_nick]' existiert nicht!<br>\n";
				formular_neue_email($neue_email);
			}
			@mysqli_free_result($result);
			break;
		
		case "antworten_forum":
		// Mail beantworten, Mail quoten und Absender lesen
			$query = "select date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
			po_titel, po_text, u_nick, u_id
			from posting
			left join user on po_u_id = u_id
			where po_id = " . intval($po_vater_id);
			$result = mysqli_query($mysqli_link, $query);
			if ($result && mysqli_num_rows($result) == 1) {
				$row = mysqli_fetch_object($result);
			}
			
			// Nick prüfen
			if ($row->u_nick && $row->u_nick != "NULL") {
				
				$row2 = mysqli_fetch_object($result);
				$neue_email['m_an_uid'] = $row->u_id;
				if (substr($row->po_titel, 0, 3) == "Re:") {
					$neue_email['m_betreff'] = $row->po_titel;
				} else {
					$neue_email['m_betreff'] = "Re: " . $row->po_titel;
				}
				$neue_email['m_text'] = erzeuge_umbruch($row->po_text, 70);
				$neue_email['m_text'] = erzeuge_quoting($neue_email['m_text'],
					$row->u_nick, $row->po_date);
				$neue_email['m_text'] = erzeuge_fuss($neue_email['m_text']);
				
				formular_neue_email2($neue_email);
				
			} elseif ($neue_email['an_nick'] == "") {
				echo "<b>Fehler:</b> Bitte geben Sie einen Nicknamen an!<br>\n";
				formular_neue_email($neue_email);
			} else {
				echo "<b>Fehler:</b> Der Nickname '$neue_email[an_nick]' existiert nicht!<br>\n";
				formular_neue_email($neue_email);
			}
			@mysqli_free_result($result);
			break;
		
		case "zeige":
		// Eine Mail als Detailansicht zeigen
			zeige_email($m_id);
			zeige_mailbox("normal", "");
			break;
		
		case "papierkorbleeren":
		// Mails mit Status geloescht löschen
			echo "<br>";
			$query = "DELETE FROM mail WHERE m_an_uid=$u_id AND m_status='geloescht'";
			$result = mysqli_query($mysqli_link, $query);
			if (!isset($neue_email)) {
				$neue_email[] = "";
			}
			formular_neue_email($neue_email);
			echo '<br>';
			zeige_mailbox("normal", "");
			break;
		
		case "papierkorb":
		// Papierkorb zeigen
			if (!isset($m_id)) {
					$m_id = "";
			}
			zeige_email($m_id);
			zeige_mailbox("geloescht", "");
			break;
		
		case "weiterleiten":
		// Formular zum Weiterleitej einer Mail ausgeben
			if (!isset($neue_email)) {
				$neue_email[] = "";
			}
			formular_neue_email($neue_email, $m_id);
			break;
		
		case "mailboxzu":
		// Formular zum Weiterleiten einer Mail ausgeben
			echo "Schliesse nun Ihre Mailbox!";
			$betreff = "MAILBOX IST ZU";
			$nachricht = "Bitte löschen Sie einfach diese E-Mail, wenn Sie wieder Mails empfangen möchten!";
			$result = mail_sende($u_id, $u_id, $nachricht, $betreff);
			if (!isset($neue_email))
				$neue_email[] = "";
			if (!isset($m_id))
				$m_id = "";
			formular_neue_email($neue_email, $m_id);
			echo '<br>';
			zeige_mailbox("normal", "");
			break;
		
		default:
			if (!isset($neue_email)) {
				$neue_email[] = "";
			}
			formular_neue_email($neue_email);
			echo '<br>';
			zeige_mailbox("normal", "");
	}
	
} elseif ($u_level == "G") {
	echo "<P><b>Fehler:</b> Als Gast steht Ihnen die Mail-Funktion nicht zur Verfügung.</P>";
} else {
	echo "<P><b>Fehler:</b> Beim Aufruf dieser Seite ist ein Fehler aufgetreten.</P>";
}

if ($o_js || !$u_id) {
	echo schliessen_link();
}
?>
</body>
</html>