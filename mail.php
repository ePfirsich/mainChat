<?php

require("functions.php");
require_once("functions.php-func-sms.php");

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
?>
<HTML>
<HEAD><TITLE><?php echo $body_titel . "_Mail"; ?></TITLE><META CHARSET=UTF-8>
<SCRIPT>
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
</SCRIPT>
<?php echo $stylesheet; ?>
</HEAD> 
<?php
$body_tag = "<BODY BGCOLOR=\"$farbe_mini_background\" ";
if (strlen($grafik_mini_background) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_mini_background\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_mini_text\" "
    . "LINK=\"$farbe_mini_link\" " . "VLINK=\"$farbe_mini_vlink\" "
    . "ALINK=\"$farbe_mini_vlink\">\n";
echo $body_tag;

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

// Browser prüfen
if (ist_netscape()) {
    $eingabe_breite = 40;
    $eingabe_breite1 = 49;
    $eingabe_breite2 = 50;
} else {
    $eingabe_breite = 55;
    $eingabe_breite1 = 87;
    $eingabe_breite2 = 75;
}

if ($u_id && $communityfeatures && $u_level != "G") {
    
    // Löscht alle Mails, die älter als $mailloescheauspapierkorb Tage sind
    if ($mailloescheauspapierkorb < 1)
        $mailloescheauspapierkorb = 14;
    $query2 = "DELETE FROM mail WHERE m_an_uid = $u_id AND m_status = 'geloescht' AND m_geloescht_ts < '"
        . date("YmdHis",
            mktime(0, 0, 0, date("m"), date("d") - intval($mailloescheauspapierkorb),
                date("Y"))) . "'";
    $result2 = mysqli_query($conn, $query2);
    
    // Menü als erstes ausgeben
    $box = $ft0 . "Menü Mail" . $ft1;
    $text = "<A HREF=\"mail.php?http_host=$http_host&id=$id&aktion=\">Mailbox neu laden</A>\n|\n"
        . "<A HREF=\"mail.php?http_host=$http_host&id=$id&aktion=neu\">Neue Mail schreiben</A>\n|\n"
        . "<A HREF=\"mail.php?http_host=$http_host&id=$id&aktion=papierkorb\">Papierkorb zeigen</A>\n|\n"
        . "<A HREF=\"mail.php?http_host=$http_host&id=$id&aktion=papierkorbleeren\">Papierkorb leeren</A>\n|\n"
        . "<A HREF=\"mail.php?http_host=$http_host&id=$id&aktion=mailboxzu\">Mailbox zumachen</A>\n|\n"
        . "<A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#mail\">Hilfe</A>\n";
    
    show_box2($box, $text, "100%");
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
    
    switch ($aktion) {
        
        case "neu":
        // Formular für neue E-Mail ausgeben
            if (!isset($neue_email))
                $neue_email = "";
            formular_neue_email($neue_email);
            break;
        
        case "neu2":
        // Mail versenden, 2. Schritt: Nick Prüfen
            $neue_email['an_nick'] = str_replace(" ", "+", $neue_email['an_nick']);
            $neue_email['an_nick'] = mysqli_real_escape_string($mysqli_link, coreCheckName($neue_email['an_nick'], $check_name));
            
            if (!isset($m_id))
                $m_id = "";
            
            $query = "SELECT u_id,u_level  FROM user WHERE u_nick = '$neue_email[an_nick]'";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) == 1) {
            	$neue_email['m_an_uid'] = mysqli_result($result, 0, "u_id");
                
                $ignore = false;
                $query2 = "SELECT * FROM iignore WHERE i_user_aktiv='" . intval($neue_email[m_an_uid]) . "' AND i_user_passiv = '$u_id'";
                $result2 = mysqli_query($mysqli_link, $query2);
                $num = mysqli_num_rows($result2);
                if ($num >= 1) {
                    $ignore = true;
                }
                
                $boxzu = false;
                $query = "SELECT m_id FROM mail WHERE m_von_uid='" . intval($neue_email[m_an_uid]) . "' AND m_an_uid='" . intval($neue_email[m_an_uid]) . "' and m_betreff = 'MAILBOX IST ZU' and m_status != 'geloescht'";
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
                    echo "<P><B>Fehler:</B> An einen Gast, einen gesperrten User oder einen User, der Sie ignoriert kann "
                        . "keine Mail verschickt werden!</P>\n";
                    formular_neue_email($neue_email, $m_id);
                }
            } elseif ($neue_email['an_nick'] == "") {
                echo "<P><B>Fehler:</B> Bitte geben Sie einen Nicknamen an!</P>\n";
                formular_neue_email($neue_email, $m_id);
            } else {
                echo "<P><B>Fehler:</B> Der Nickname '$neue_email[an_nick]' existiert nicht!</P>\n";
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
                echo "<B>Fehler:</B> Bitte geben Sie einen Betreff zum Versenden ein!<BR>\n";
                $ok = FALSE;
            }
            if (strlen($neue_email['m_betreff']) > 254) {
                echo "<B>Fehler:</B> Bitte geben im Betreff weniger als 254 Zeichen ein!<BR>\n";
                $ok = FALSE;
            }
            
            // Text prüfen
            $neue_email['m_text'] = chat_parse(
                htmlspecialchars($neue_email['m_text']));
            if (strlen($neue_email['m_text']) < 4) {
                echo "<B>Fehler:</B> Bitte geben Sie einen Text zum Versenden ein!<BR>\n";
                $ok = FALSE;
            }
            if (strlen($neue_email['m_text']) > 10000) {
                echo "<B>Fehler:</B> Der Text ist zu lange!<BR>\n";
                $ok = FALSE;
            }
            
            // User-ID Prüfen
            $neue_email['m_an_uid'] = intval($neue_email['m_an_uid']);
            $query = "SELECT u_nick FROM user WHERE u_id = $neue_email[m_an_uid]";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) == 1) {
            	$an_nick = mysqli_result($result, 0, "u_nick");
            } else {
                echo "<B>Fehler:</B> Der User mit ID '$neue_email[m_an_uid]' existiert nicht!<BR>\n";
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
                        echo "<P><B>Fehler: </B>Ihre E-Mail an '$an_nick' wurde nicht verschickt.</P>";
                    }
                    
                } elseif ($neue_email['typ'] == 2) {
                    
                    $smszulang = false;
                    $text = $neue_email['m_betreff'] . " "
                        . $neue_email['m_text'];
                    $emp = user($u_id, $userdata, FALSE, FALSE, "&nbsp;", "",
                        "", FALSE, TRUE);
                    $absender = $emp . "@" . $chat . ": "; // Wir basteln uns den Absender der SMS
                    
                    if (160 - strlen($text) - strlen($absender) < 0) {
                        $smszulang = true;
                    }
                    
                    $text = substr($text, 0, 160 - strlen($absender)); // Text um Absender kürzen
                    $text = preg_replace(
                        "/[\\\\" . chr(1) . "-" . chr(31) . "]/", "", $text); // Ungültige Zeichen filtern
                    $complete = $absender . str_replace("-- ", "\n", $text);
                    
                    // Prüfen ob genug Punkte
                    if ($u_punkte_gesamt < $sms[punkte]) {
                        $fehler = "Um SMS verschicken zu dürfen brauchst Du mehr als $sms[punkte] Punkte";
                    }
                    
                    // Prüfen ob noch genug SMS-Guthaben da
                    $guthaben = hole_smsguthaben($u_id);
                    
                    if (!isset($fehler)) {
                        if ($guthaben <= 0) {
                            $fehler = "Du hast kein SMS-Guthaben mehr!";
                        }
                    }
                    
                    // Prüfen ob Empfänger SMS möchte
                    $neue_email['m_an_uid'] = intval($neue_email['m_an_uid']);
                    $query = "SELECT u_sms_ok FROM user WHERE u_id = '$neue_email[m_an_uid]'";
                    $result = mysqli_query($mysqli_link, $query);
                    $a = mysqli_fetch_array($result);
                    mysqli_free_result($result);
                    $sms_ok = $a[u_sms_ok];
                    if (!isset($fehler)) {
                        if ($sms_ok == "N") {
                            $fehler = "Dieser User möchte keine SMS empfangen";
                        }
                    }
                    
                    // Prüfen auf gültige Handynummer des Empfängers 
                    
                    $handynr = hole_handynummer($neue_email['m_an_uid']);
                    if (!isset($fehler)) {
                        if (!pruefe_handynummer($handynr)) {
                            $fehler = "Dieser User hat leider keine gültige Handynummer eingetragen.";
                        }
                    }
                    
                    if (isset($fehler)) {
                        echo "<p><b>Fehler: $fehler</b></p>";
                    } else {
                        sms_sende($u_id, $neue_email['m_an_uid'], $complete);
                        echo "<p>SMS wurde erfolgreich versendet!<BR>";
                        $guthaben = hole_smsguthaben($u_id);
                        echo "Restguthaben: $guthaben SMS";
                        if ($smszulang) {
                            echo "<p><b>Fehler! Die eingegebene SMS war zu lang. Sie wurde auf 160 Zeichen gekürzt!</p></b>";
                        }
                        
                    }
                    
                } else {
                    
                    $result = mail_sende($u_id, $neue_email['m_an_uid'],
                        $neue_email['m_text'], $neue_email['m_betreff']);
                    if ($result[0]) {
                        echo "<P>Ihre Mail an '$an_nick' wurde verschickt.</P>";
                        $neue_email['m_id'] = $result[0];
                    } else {
                        echo "<P><B>Fehler: </B>Ihre Mail an '$an_nick' wurde nicht verschickt.</P>";
                        echo "<P>$result[1]</P>";
                    }
                    
                }
                unset($neue_email);
                
                if (!isset($neue_email))
                    $neue_email = "";
                formular_neue_email($neue_email);
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
            
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_object($result);
            }
            @mysqli_free_result($result);
            
            // Nick prüfen
            $query = "SELECT u_id,u_nick FROM user WHERE u_id=" . $row->m_von_uid;
            $result = mysqli_query($conn, $query);
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
                echo "<B>Fehler:</B> Bitte geben Sie einen Nicknamen an!<BR>\n";
                formular_neue_email($neue_email);
            } else {
                echo "<B>Fehler:</B> Der Nickname '$neue_email[an_nick]' existiert nicht!<BR>\n";
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
            $result = mysqli_query($conn, $query);
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
                echo "<B>Fehler:</B> Bitte geben Sie einen Nicknamen an!<BR>\n";
                formular_neue_email($neue_email);
            } else {
                echo "<B>Fehler:</B> Der Nickname '$neue_email[an_nick]' existiert nicht!<BR>\n";
                formular_neue_email($neue_email);
            }
            @mysqli_free_result($result);
            break;
        
        case "zeige":
        // Eine Mail als Detailansicht zeigen
            echo "<BR>";
            zeige_email($m_id);
            zeige_mailbox("normal", "");
            break;
        
        case "papierkorbleeren":
        // Mails mit Status geloescht löschen
            echo "<BR>";
            $query = "DELETE FROM mail WHERE m_an_uid=$u_id AND m_status='geloescht'";
            $result = mysqli_query($conn, $query);
            if (!isset($neue_email))
                $neue_email[] = "";
            formular_neue_email($neue_email);
            zeige_mailbox("normal", "");
            break;
        
        case "papierkorb":
        // Papierkorb zeigen
            echo "<BR>";
            if (!isset($m_id))
                $m_id = "";
            zeige_email($m_id);
            zeige_mailbox("geloescht", "");
            break;
        
        case "weiterleiten":
        // Formular zum Weiterleitej einer Mail ausgeben
            if (!isset($neue_email))
                $neue_email[] = "";
            formular_neue_email($neue_email, $m_id);
            break;
        
        case "mailboxzu":
        // Formular zum Weiterleitej einer Mail ausgeben
            echo "<font face=\"Arial\">Schliesse nun Ihre Mailbox!</font>";
            $betreff = "MAILBOX IST ZU";
            $nachricht = "Bitte löschen Sie einfach diese E-Mail, wenn Sie wieder Mails empfangen möchten!";
            $result = mail_sende($u_id, $u_id, $nachricht, $betreff);
            if (!isset($neue_email))
                $neue_email[] = "";
            if (!isset($m_id))
                $m_id = "";
            formular_neue_email($neue_email, $m_id);
            zeige_mailbox("normal", "");
            break;
        
        default:
            if (!isset($neue_email))
                $neue_email[] = "";
            formular_neue_email($neue_email);
            zeige_mailbox("normal", "");
    }
    
} elseif ($u_level == "G") {
    echo "<P><B>Fehler:</B> Als Gast steht Ihnen die Mail-Funktion nicht zur Verfügung.</P>";
} else {
    echo "<P><B>Fehler:</B> Beim Aufruf dieser Seite ist ein Fehler aufgetreten.</P>";
}

if ($o_js || !$u_id) {
    echo $f1
        . "<CENTER>[<A HREF=\"javascript:window.close();\">$t[sonst1]</A>]</CENTER>"
        . $f2 . "<BR>\n";
}

?>
</BODY></HTML>
