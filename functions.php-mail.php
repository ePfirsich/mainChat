<?php

function formular_neue_email($neue_email, $m_id = "")
{
    
    // Gibt Formular für Nicknamen zum Versand einer Mail aus
    
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $conn, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF;
    
    if (!$eingabe_breite)
        $eingabe_breite = 30;
    
    // Nickname aus u_nick lesen und setzen
    if (isset($neue_email['m_an_uid']) && $neue_email['m_an_uid']) {
        $query = "SELECT u_nick FROM user WHERE u_id = " . intval($neue_email[m_an_uid]);
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) == 1) {
            $an_nick = mysql_result($result, 0, 0);
            if (strlen($an_nick) > 0)
                $neue_email['an_nick'] = $an_nick;
        }
    }
    
    if ($m_id) {
        $titel = "Mail weiterleiten an:";
    } else {
        $titel = "Neue Mail senden:";
    }
    
    echo "<FORM NAME=\"mail_neu\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"neu2\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"m_id\" VALUE=\"$m_id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    if (!isset($neue_email['an_nick']))
        $neue_email['an_nick'] = "";
    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2><DIV style=\"color:$farbe_text;\"><B>$titel</B></DIV></TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD align=\"right\"><B>Nickname:</B></TD><TD>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"neue_email[an_nick]\" VALUE=\""
        . $neue_email['an_nick'] . "\" SIZE=20>" . "&nbsp;"
        . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"WEITER\">" . $f2
        . "</TD></TR>\n" . "</TABLE></FORM>\n";
    
}

function formular_neue_email2($neue_email, $m_id = "")
{
    
    // Gibt Formular zum Versand einer neuen Mail aus
    
    global $id, $http_host, $eingabe_breite1, $eingabe_breite2, $PHP_SELF, $f1, $f2, $f3, $f4, $conn, $dbase, $u_id;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF;
    global $u_punkte_gesamt, $sms;
    
    $smsgh = hole_smsguthaben($u_id);
    if ($u_punkte_gesamt > $sms['punkte'] && $smsgh > 0)
        $darfsms = true;
    else $darfsms = false;
    
    if (!$eingabe_breite1)
        $eingabe_breite1 = 30;
    if (!$eingabe_breite2)
        $eingabe_breite2 = 40;
    echo '<script language="JavaScript">
	function zaehle()
	{
	betr=document.mail_neu.elements["neue_email[m_betreff]"].value.length;
	text=document.mail_neu.elements["neue_email[m_text]"].value.length;
	document.mail_neu.counter.value=betr+text;
	}
	</script>
	';
    
    if ($m_id) {
        
        // Alte Mail lesen und als Kopie in Formular schreiben
        $titel = "Mail weiterleiten an";
        $query = "SELECT m_betreff,m_text from mail "
            . "where m_id=" . intval($m_id) . " AND m_an_uid=$u_id";
        $result = mysqli_query($conn, $query);
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
        @mysqli_free_result($result);
        
    } else {
        // Neue Mail versenden
        $titel = "Neue Mail an";
    }
    
    // Signatur anfügen
    if (!isset($neue_email['m_text']))
        $neue_email['m_text'] = htmlspecialchars(erzeuge_fuss(""));
    
    // Userdaten aus u_id lesen und setzen
    if ($neue_email['m_an_uid']) {
        $query = "SELECT u_nick,u_email,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id, "
            . "date_format(u_login,'%d.%m.%y %H:%i') as login, "
            . "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online "
            . "from user left join online on o_user=u_id "
            . "WHERE u_id = $neue_email[m_an_uid]";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_object($result);
            
            $email = "";
            $email_select = "&nbsp;";
            // E-Mail Adresse vorhanden?
            $email_bekannt = false;
            if (strlen($row->u_email) > 0) {
                $email = $f1 . " (E-Mail: <A HREF=\"MAILTO:$row->u_email\">"
                    . htmlspecialchars($row->u_email) . "</A>)" . $f2;
                $email_bekannt = true;
            }
            
            $email_select = "<B>Art des Mailversands:</B>&nbsp;<SELECT NAME=\"neue_email[typ]\">";
            if (isset($neue_email['typ']) && $neue_email['typ'] == 1) {
                $email_select .= "<OPTION VALUE=\"0\">Mail in Chat-Mailbox\n";
                if ($email_bekannt)
                    $email_select .= "<OPTION SELECTED VALUE=\"1\">E-Mail an "
                        . $row->u_email . "\n";
                if ($darfsms)
                    $email_select .= "<OPTION VALUE=\"2\">SMS\n";
            } else {
                $email_select .= "<OPTION SELECTED VALUE=\"0\">Mail in Chat-Mailbox\n";
                if ($email_bekannt)
                    $email_select .= "<OPTION VALUE=\"1\">E-Mail an "
                        . $row->u_email . "\n";
                if ($darfsms)
                    $email_select .= "<OPTION VALUE=\"2\">SMS\n";
            }
            $email_select .= "</SELECT>\n";
            
            echo "<FORM NAME=\"mail_neu\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
                . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
                . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"neu3\">\n"
                . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
                . "<INPUT TYPE=\"HIDDEN\" NAME=\"neue_email[m_an_uid]\" VALUE=\"$neue_email[m_an_uid]\">\n"
                . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
            
            if ($row->o_id == "" || $row->o_id == "NULL")
                $row->online = "";
            
            if (!isset($neue_email['m_betreff']))
                $neue_email['m_betreff'] = "";
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=3><DIV style=\"color:$farbe_text;\"><B>$titel "
                . user($row->u_id, $row, TRUE, TRUE, "&nbsp;", $row->online,
                    $row->login) . "</B>$email</DIV></TD></TR>\n"
                . "<TR BGCOLOR=\"$farbe_tabelle_zeile2\"><TD VALIGN=\"TOP\" align=\"right\"><B>Betreff:</B></TD><TD  COLSPAN=2>"
                . $f1
                . "<INPUT TYPE=\"TEXT\" NAME=\"neue_email[m_betreff]\" VALUE=\""
                . $neue_email['m_betreff'] . "\" SIZE="
                . ($eingabe_breite1)
                . "
				 ONCHANGE=zaehle() ONFOCUS=zaehle() ONKEYDOWN=zaehle() ONKEYUP=zaehle()>"
                . $f2 . "<input name=\"counter\" size=3></TD></TR>"
                . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD VALIGN=\"TOP\" align=\"right\"><B>Ihr Text:</B></TD><TD COLSPAN=2>"
                . $f1 . "<TEXTAREA COLS=" . ($eingabe_breite2)
                . " ROWS=20 NAME=\"neue_email[m_text]\" ONCHANGE=zaehle() ONFOCUS=zaehle() ONKEYDOWN=zaehle() ONKEYUP=zaehle()>"
                . $neue_email['m_text'] . "</TEXTAREA>\n" . $f2
                . "</TD></TR>"
                . "<TR BGCOLOR=\"$farbe_tabelle_zeile2\"><TD>&nbsp;</TD><TD>$email_select</TD>"
                . "<TD ALIGN=\"right\" >" . $f1
                . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"VERSENDEN\">"
                . $f2 . "</TD></TR>\n" . "</TABLE></FORM>\n";
            
        } else {
            echo "<P><B>Fehler:</B> Der User mit ID '$neue_email[m_an_uid]' existiert nicht!</P>\n";
        }
    }
}

function zeige_mailbox($aktion, $zeilen)
{
    
    // Zeigt die Mails in der Übersicht an
    
    global $id, $conn, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $conn, $u_nick, $u_id;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF, $chat;
    
    if (!$eingabe_breite)
        $eingabe_breite = 30;
    
    switch ($aktion) {
        case "alle";
            $query = "SELECT m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,m_betreff,u_nick "
                . "FROM mail LEFT JOIN user on m_von_uid=u_id "
                . "WHERE m_an_uid=$u_id order by m_zeit desc";
            $button = "LÖSCHEN";
            $titel = "Mails in der Mailbox für";
            break;
        
        case "geloescht":
            $query = "SELECT m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,m_betreff,u_nick "
                . "FROM mail LEFT JOIN user on m_von_uid=u_id "
                . "WHERE m_an_uid=$u_id AND m_status='geloescht' "
                . "order by m_zeit desc";
            $button = "WIEDERHERSTELLEN";
            $titel = "Mails im Papierkorb von";
            break;
        
        case "normal":
        default;
            $query = "SELECT m_id,m_status,m_von_uid,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,m_betreff,u_nick "
                . "FROM mail LEFT JOIN user on m_von_uid=u_id "
                . "WHERE m_an_uid=$u_id AND m_status!='geloescht' "
                . "order by m_zeit desc";
            $button = "LÖSCHEN";
            $titel = "Mails in der Mailbox für";
    }
    
    echo "<FORM NAME=\"mailbox\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"loesche\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    $result = mysqli_query($conn, $query);
    if ($result) {
        
        $anzahl = mysqli_num_rows($result);
        if ($anzahl == 0) {
            
            // Leere Mailbox
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2><DIV style=\"color:$farbe_text;\"><B>$anzahl $titel $u_nick:</B></DIV></TD></TR>\n";
            "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD>&nbsp;</TD><TD align=\"left\">Ihre Mailbox ist leer.</TD></TR>";
            
        } else {
            
            // Mails anzeigen
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=5><DIV style=\"color:$farbe_text;\"><B>$anzahl $titel $u_nick:</B></DIV></TD></TR>\n"
                . "<TR><TD>" . $f1 . "Löschen" . $f2 . "</TD><TD>" . $f1
                . "von" . $f2 . "</TD><TD>" . $f1 . "Betreff" . $f2
                . "</TD><TD>" . $f1 . "Datum" . $f2 . "</TD><TD>" . $f1
                . "Status" . $f2 . "</TD></TR>\n";
            
            $i = 0;
            $bgcolor = $farbe_tabelle_zeile1;
            while ($row = mysqli_fetch_object($result)) {
                
                $url = "<A HREF=\"" . $PHP_SELF
                    . "?id=$id&http_host=$http_host&aktion=zeige&m_id="
                    . $row->m_id . "\">";
                if ($row->m_status == "neu"
                    || $row->m_status == "neu/verschickt") {
                    $auf = "<B>" . $f1;
                    $zu = $f2 . "</B>";
                } else {
                    $auf = $f1;
                    $zu = $f2;
                }
                
                if ($row->u_nick == "NULL" || $row->u_nick == "") {
                    $von_nick = "$chat";
                } else {
                    $von_nick = $row->u_nick;
                }
                
                echo "<TR BGCOLOR=\"$bgcolor\"><TD ALIGN=\"CENTER\">" . $auf
                    . "<INPUT TYPE=\"CHECKBOX\" NAME=\"loesche_email[]\" VALUE=\""
                    . $row->m_id . "\"></TD>" . "<TD>" . $auf . $url
                    . $von_nick . "</A>" . $zu . "</TD>" . "<TD WIDTH=\"50%\">"
                    . $auf . $url
                    . htmlspecialchars($row->m_betreff) . "</A>"
                    . $zu . "</TD>" . "<TD>" . $auf . $row->zeit . $zu
                    . "</TD>" . "<TD>" . $auf . $row->m_status . $zu . "</TD>"
                    . "</TR>\n";
                
                if (($i % 2) > 0) {
                    $bgcolor = $farbe_tabelle_zeile1;
                } else {
                    $bgcolor = $farbe_tabelle_zeile2;
                }
                $i++;
            }
            
            echo "<TR BGCOLOR=\"$bgcolor\"><TD><INPUT TYPE=\"checkbox\" onClick=\"toggle(this.checked)\">"
                . $f1 . " Alle Auswählen" . $f2 . "</TD>\n"
                . "<TD ALIGN=\"right\" COLSPAN=4>" . $f1
                . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$button\">"
                . $f2 . "</TD></TR>\n";
        }
        
        echo "</TABLE></FORM>\n";
    }
}

function zeige_email($m_id)
{
    
    // Zeigt die Mail im Detail an
    
    global $id, $conn, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $conn, $u_nick, $u_id;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $PHP_SELF, $chat;
    
    if (!$eingabe_breite)
        $eingabe_breite = 30;
    
    echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    $query = "SELECT mail.*,date_format(m_zeit,'%d.%m.%y um %H:%i') as zeit,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe "
        . "FROM mail LEFT JOIN user on m_von_uid=u_id "
        . "WHERE m_an_uid=$u_id AND m_id=" . intval($m_id) . " order by m_zeit desc";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) == 1) {
        
        // Mail anzeigen
        $row = mysqli_fetch_object($result);
        
        if ($row->u_nick == "NULL" || $row->u_nick == "") {
            $von_nick = "$chat";
        } else {
            $von_nick = "<B>" . user($row->u_id, $row, TRUE, FALSE) . "</B>";
        }
        $row->m_text = str_replace("<ID>", $id, $row->m_text);
        $row->m_text = str_replace("<HTTP_HOST>", $http_host, $row->m_text);
        
        // Mail ausgeben
        echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=4><DIV style=\"color:$farbe_text;\"><B>Mail zeigen:</B></DIV></TD></TR>\n"
            . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD ALIGN=\"RIGHT\"><B>"
            . $f1 . "Von:" . $f2 . "</B></TD><TD COLSPAN=3 WIDTH=\"100%\">"
            . $von_nick . "</TD></TR>\n"
            . "<TR BGCOLOR=\"$farbe_tabelle_zeile2\"><TD ALIGN=\"RIGHT\"><B>"
            . $f1 . "Am:" . $f2 . "</B></TD><TD COLSPAN=3>" . $f1 . $row->zeit
            . $f2 . "</TD></TR>\n"
            . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD ALIGN=\"RIGHT\"><B>"
            . $f1 . "Betreff:" . $f2 . "</B></TD><TD COLSPAN=3>" . $f1
            . htmlspecialchars($row->m_betreff) . $f2
            . "</TD></TR>\n"
            . "<TR BGCOLOR=\"$farbe_tabelle_zeile2\"><TD ALIGN=\"RIGHT\"><B>"
            . $f1 . "Status:" . $f2 . "</B></TD><TD COLSPAN=3>" . $f1
            . $row->m_status . $f2 . "</TD></TR>\n"
            . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD>&nbsp;</TD><TD COLSPAN=3>"
            . $f1 . str_replace("\n", "<BR>\n", $row->m_text)
            . $f2 . "</TD></TR>\n";
        
        // Formular zur Löschen, Beantworten
        echo "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD>&nbsp;</TD>"
            . "<FORM NAME=\"mail_antworten\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
            . "<TD ALIGN=\"left\">" . $f1
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"m_id\" VALUE=\"" . $row->m_id
            . "\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"antworten\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
            . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"ANTWORTEN\">" . $f2
            . "</TD></FORM>\n"
            . "<FORM NAME=\"mail_antworten\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
            . "<TD ALIGN=\"center\">" . $f1
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"m_id\" VALUE=\"" . $row->m_id
            . "\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"weiterleiten\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
            . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"WEITERLEITEN\">"
            . $f2 . "</TD></FORM>\n"
            . "<FORM NAME=\"mail_loeschen\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
            . "<TD ALIGN=\"right\">" . $f1
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"loesche_email\" VALUE=\""
            . $row->m_id . "\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"loesche\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
            . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"LÖSCHEN\">" . $f2
            . "</TD></FORM></TR>\n";
        
        echo "</TABLE>\n";
        
        if ($row->m_status != "geloescht") {
            // E-Mail auf gelesen setzen
            unset($f);
            $f['m_status'] = "gelesen";
            $f['m_zeit'] = $row->m_zeit;
            schreibe_db("mail", $f, $row->m_id, "m_id");
        }
    }
}

function loesche_mail($m_id, $u_id)
{
    
    // Löscht eine Mail der ID m_id
    
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $conn, $u_nick, $u_id;
    
    $query = "SELECT m_zeit,m_id,m_status FROM mail "
        . "WHERE m_id=" . intval($m_id) . " AND m_an_uid=$u_id";
    $result = mysqli_query($conn, $query);
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
        echo "<P><B>Fehler:</B> Diese Mail nicht kann nicht gelöscht werden!</P>";
    }
    @mysqli_free_result($result);
    
}

?>