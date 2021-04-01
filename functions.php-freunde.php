<?php

function zeige_freunde($aktion, $zeilen)
{
    
    // Zeigt Liste der Freunde an
    
    global $id, $http_host, $mysqli_link, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $mysqli_link, $u_nick, $u_id;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2;
    
    switch ($aktion) {
        case "normal":
        default:
            $query = "SELECT f_id,f_text,f_userid,f_freundid,f_zeit,date_format(f_zeit,'%d.%m.%y %H:%i') as zeit FROM freunde WHERE f_userid=$u_id AND f_status = 'bestaetigt' "
                . "UNION "
                . "SELECT f_id,f_text,f_userid,f_freundid,f_zeit,date_format(f_zeit,'%d.%m.%y %H:%i') as zeit FROM freunde WHERE f_freundid=$u_id AND f_status = 'bestaetigt' "
                . "ORDER BY f_zeit desc ";
            $button = "LÖSCHEN";
            $titel1 = "hat";
            $titel2 = "bestätigte(n) Freunde";
            break;
        
        case "bestaetigen":
            $query = "SELECT f_id,f_text,f_userid,f_freundid,"
                . "date_format(f_zeit,'%d.%m.%y %H:%i') as zeit "
                . "from freunde " . "WHERE ( "
                . " f_freundid=$u_id) AND (f_status='beworben') "
                . "order by f_zeit desc";
            $button = "LÖSCHEN";
            $button2 = "BESTÄTIGEN";
            $titel1 = "hat";
            $titel2 = "noch zu bestätigende Freunde";
            break;
    }
    
    echo "<FORM NAME=\"freund_loeschen\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"bearbeite\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    $result = mysqli_query($mysqli_link, $query);
    if ($result) {
        
        $anzahl = mysqli_num_rows($result);
        if ($anzahl == 0) {
            
            // Keine Freunde
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2><DIV style=\"color:$farbe_text;\"><b>$u_nick $titel1 noch keine $titel2:</b><DIV style=\"color:$farbe_text;\"></TD></TR>\n";
            if ($aktion == "normal")
                echo "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD>&nbsp;</TD><TD align=\"left\">Sie haben keine Freunde eingestellt.</TD></TR>";
            if ($aktion == "bestaetigen")
                echo "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD>&nbsp;</TD><TD align=\"left\">Sie haben keine Freunde zu bestaetigen.</TD></TR>";
            
        } else {
            
            // Freunde anzeigen
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=5><DIV style=\"color:$farbe_text;\"><b>$u_nick $titel1 $anzahl $titel2:</b></DIV></TD></TR>\n"
                . "<TR><TD WIDTH=\"5%\">" . $f1 . "Markieren" . $f2
                . "</TD><TD WIDTH=\"35%\">" . $f1 . "Nickname" . $f2 . "</TD>"
                . "<TD WIDTH=\"35%\">" . $f1 . "Info" . $f2
                . "</TD><TD WIDTH=\"20%\" ALIGN=\"CENTER\">" . $f1
                . "Datum des Eintrags" . $f2
                . "</TD><TD WIDTH=\"5%\" ALIGN=\"CENTER\">" . $f1 . "Info"
                . $f2 . "</TD></TR>\n";
            
            $i = 0;
            $bgcolor = $farbe_tabelle_zeile1;
            while ($row = mysqli_fetch_object($result)) {
                
                // User aus DB lesen
                if ($row->f_userid != $u_id) {
                    $query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id,"
                        . "date_format(u_login,'%d.%m.%y %H:%i') as login, "
                        . "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online "
                        . "from user left join online on o_user=u_id "
                        . "WHERE u_id=$row->f_userid ";
                    $result2 = mysqli_query($mysqli_link, $query);
                } elseif ($row->f_freundid != $u_id) {
                    $query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id,"
                        . "date_format(u_login,'%d.%m.%y %H:%i') as login, "
                        . "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online "
                        . "from user left join online on o_user=u_id "
                        . "WHERE u_id=$row->f_freundid ";
                    $result2 = mysqli_query($mysqli_link, $query);
                }
                if ($result2 && mysqli_num_rows($result2) > 0) {
                    
                    // User gefunden -> Ausgeben
                    $row2 = mysqli_fetch_object($result2);
                    $freund_nick = "<b>"
                        . user($row2->u_id, $row2, TRUE, FALSE) . "</b>";
                    
                } else {
                    
                    // User nicht gefunden, Freund löschen
                    $freund_nick = "NOBODY";
                    $query = "DELETE from freunde WHERE f_id=$row->f_id";
                    $result2 = mysqli_query($mysqli_link, $query);
                    
                }
                
                // Unterscheidung online ja/nein, Text ausgeben
                if ($row2->o_id) {
                    $txt = $freund_nick . "<br>online&nbsp;"
                        . gmdate("H:i:s", $row2->online) . "&nbsp;Std/Min/Sek";
                    $auf = "<b>" . $f1;
                    $zu = $f2 . "</b>";
                } else {
                    $txt = $freund_nick . "<br>Letzter&nbsp;Login:&nbsp;"
                        . str_replace(" ", "&nbsp;", $row2->login);
                    $auf = $f1;
                    $zu = $f2;
                }
                
                // Infotext setzen
                if ($row->f_text == "") {
                    $infotext = "&nbsp;";
                } else {
                    $infotext = htmlentities($row->f_text);
                }
                
                // ID des Freundes finden
                if ($u_id == $row->f_freundid) {
                    $freundid = $row->f_userid;
                } else {
                    $freundid = $row->f_freundid;
                }
                
                echo "<TR BGCOLOR=\"$bgcolor\"><TD ALIGN=\"CENTER\">" . $auf
                    . "<INPUT TYPE=\"CHECKBOX\" NAME=\"f_freundid[]\" VALUE=\""
                    . $freundid . "\"></TD>"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"f_nick[]\" VALUE=\""
                    . $row2->u_nick . "\"></TD>" . "<TD>" . $auf . $txt . $zu
                    . "</TD>" . "<TD>" . $auf . $infotext . $zu . "</TD>"
                    . "<TD ALIGN=\"CENTER\">" . $auf . $row->zeit . $zu
                    . "</TD>" . "<TD ALIGN=\"CENTER\">" . $auf
                    . "<a href=\"freunde.php?http_host=$http_host&id=$id&aktion=editinfotext&editeintrag=$row->f_id\">[ÄNDERN]</A>"
                    . $zu . "</TD>" . "</TR>\n";
                
                if (($i % 2) > 0) {
                    $bgcolor = $farbe_tabelle_zeile1;
                } else {
                    $bgcolor = $farbe_tabelle_zeile2;
                }
                $i++;
            }
            
            echo "<TR BGCOLOR=\"$bgcolor\"><TD COLSPAN=2><INPUT TYPE=\"checkbox\" onClick=\"toggle(this.checked)\">"
                . $f1 . " Alle Auswählen" . $f2 . "</TD>\n";
            echo "<TD ALIGN=\"right\" COLSPAN=2>" . $f1;
            if ($aktion == "bestaetigen")
                echo "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$button2\">";
            echo "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$button\">" . $f2
                . "</TD></TR>\n";
        }
        
        echo "</TABLE></FORM>\n";
    }
}

function loesche_freund($f_freundid, $f_userid)
{
    // Löscht Freund aus der Tabelle mit f_userid und f_freundid
    // $f_userid User-ID 
    // $f_freundid User-ID
    
    global $id, $http_host, $mysqli_link, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $mysqli_link, $u_nick, $u_id;
    
    if (!$f_userid || !$f_freundid) {
        echo "Fehler beim Löschen des Freundes '$f_nick': $f_userid,$f_freundid!<br>";
        return (0);
    }
    $f_freundid = mysqli_real_escape_string($mysqli_link, $f_freundid);
    $f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
    
    $query = "DELETE from freunde WHERE "
        . "(f_userid=$f_userid AND f_freundid=$f_freundid) " . "OR "
        . "(f_userid=$f_freundid AND f_freundid=$f_userid)";
    $result = mysqli_query($mysqli_link, $query);
    
    $query = "SELECT `u_nick` FROM `user` WHERE `u_id`=$f_freundid";
    $result = mysqli_query($mysqli_link, $query);
    if ($result && mysqli_num_rows($result) != 0) {
    	$f_nick = mysqli_result($result, 0, 0);
        $back = "<P><b>Hinweis:</b> '$f_nick' ist nicht mehr Ihr Freund.</P>";
    }
    @mysqli_free_result($result);
    return ($back);
}

function formular_neuer_freund($neuer_freund)
{
    
    // Gibt Formular für Nicknamen zum Hinzufügen als Freund aus
    
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2;
    
    if (!$eingabe_breite)
        $eingabe_breite = 30;
    
    $titel = "Neuen Freund eintragen:";
    
    echo "<FORM NAME=\"freund_neu\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"neu2\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2><DIV style=\"color:$farbe_text;\"><b>$titel</b></DIV></TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD align=\"right\"><b>Nickname:</b></TD><TD>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"neuer_freund[u_nick]\" VALUE=\""
        . $neuer_freund['u_nick'] . "\" SIZE=20>" . $f2
        . "</TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD align=\"right\"><b>Infotext:</b></TD><TD>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"neuer_freund[f_text]\" VALUE=\""
        . htmlentities($neuer_freund['f_text'])
        . "\" SIZE=$eingabe_breite>" . "&nbsp;"
        . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"EINTRAGEN\">" . $f2
        . "</TD></TR>\n" . "</TABLE></FORM>\n";
    
}

function formular_editieren($f_id, $f_text)
{
    
    // Gibt Formular für Nicknamen zum Hinzufügen als Freund aus
    
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2;
    
    if (!$eingabe_breite)
        $eingabe_breite = 30;
    
    $titel = "Freundestext ändern:";
    
    echo "<FORM NAME=\"freund_neu\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"editinfotext2\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"f_id\" VALUE=\"$f_id\">\n"
        . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2><DIV style=\"color:$farbe_text;\"><b>$titel</b></DIV></TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD align=\"right\"><b>Infotext:</b></TD><TD>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f_text\" VALUE=\""
        . htmlentities($f_text) . "\" SIZE=$eingabe_breite>"
        . "&nbsp;" . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"ÄNDERN\">"
        . $f2 . "</TD></TR>\n" . "</TABLE></FORM>\n";
    
}

function neuer_freund($f_userid, $freund)
{
    // Trägt neuen Freund in der Datenbank ein
    
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase, $chat, $system_farbe;
    
    if (!$freund['u_id'] || !$f_userid) {
        echo "Fehler beim Anlegen des Freundes: $f_userid,$freund[u_id]!<br>";
    } else {
        
        // Prüfen ob Freund bereits in Tabelle steht
        $freund['u_id'] = mysqli_real_escape_string($mysqli_link, $freund['u_id']);
        $f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
        
        $query = "SELECT f_id from freunde WHERE "
            . "(f_userid=$freund[u_id] AND f_freundid=$f_userid) " . "OR "
            . "(f_userid=$f_userid AND f_freundid=$freund[u_id])";
        
        $result = mysqli_query($mysqli_link, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            
            $back = "<P><b>Fehler:</b> '$freund[u_nick]' ist bereits als Ihr Freund eingetragen!</P>\n";
            
        } elseif ($freund['u_id'] == $f_userid) {
            
            // Eigener Freund ist verboten
            $back = "<P><b>Fehler:</b> Sie können sich nicht selbst als Freund eintragen!</P>\n";
        } else {
            
            // User ist noch kein Freund -> hinzufügen
            $f['f_userid'] = $f_userid;
            $f['f_freundid'] = $freund['u_id'];
            $f['f_text'] = $freund['f_text'];
            $f['f_status'] = "beworben";
            schreibe_db("freunde", $f, 0, "f_id");
            
            $betreff = "Neue Freundesbewerbung";
            $text = "Hallo $freund[u_nick]!\nIch möchte gerne Ihr Freund im $chat werden!\n"
                . "wenn Sie das auch wollen und im $chat eingeloggt sind, so klicken Sie bitte "
                . "<a href=\"freunde.php?http_host=$http_host&id=<ID>&aktion=bestaetigen\">hier</a>.\n"
                . "Sollten Sie diese E-Mail als Weiterleitung bekommen, so müssen Sie sich erst in den Chat einloggen.\n\n";
            
            $fenster = str_replace("+", "", $freund['u_nick']);
            $fenster = str_replace("-", "", $fenster);
            $fenster = str_replace("ä", "", $fenster);
            $fenster = str_replace("ö", "", $fenster);
            $fenster = str_replace("ü", "", $fenster);
            $fenster = str_replace("Ä", "", $fenster);
            $fenster = str_replace("Ö", "", $fenster);
            $fenster = str_replace("Ü", "", $fenster);
            $fenster = str_replace("ß", "", $fenster);
            
            mail_sende($f_userid, $freund['u_id'], $text, $betreff);
            if (ist_online($freund['u_id'])) {
                $msg = "Hallo $freund[u_nick], jemand möchte Ihr Freund werden. "
                    . "<a href=\"freunde.php?http_host=$http_host&aktion=bestaetigen&id=<ID>\" target=\"640_$fenster\" "
                    . "onclick=\"window.open('freunde.php?http_host=$http_host&aktion=bestaetigen&id=<ID>','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\">"
                    . "gleich zustimmen?</a>";
                
                #system_msg("",0,$f_userid,$system_farbe,$msg);
                system_msg("", 0, $freund['u_id'], $system_farbe, $msg);
            }
            $back = "<b>Hinweis:</b> Sie bewerben sich nun bei '$freund[u_nick]' als Freund.";
            
        }
        
    }
    return ($back);
}

function edit_freund($f_id, $f_text)
{
    // Ändert den Infotext beim Freund
    
    $f_id = intval($f_id);
    
    unset($f);
    $f['f_text'] = $f_text;
    schreibe_db("freunde", $f, $f_id, "f_id");
    
    $back = "<b>Hinweis:</b> Der Freundestext wurde geändert.";
    
    return ($back);
}

function bestaetige_freund($f_userid, $freund)
{
    global $dbase, $mysqli_link;
    $f_userid = mysqli_real_escape_string($mysqli_link, $f_userid);
    $freund = mysqli_real_escape_string($mysqli_link, $freund);
    $query = "UPDATE freunde SET f_status = 'bestaetigt', f_zeit = NOW() WHERE f_userid = '$f_userid' AND f_freundid = '$freund'";
    mysqli_query($mysqli_link, $query);
    $query = "SELECT `u_nick` FROM `user` WHERE `u_id`='$f_userid'";
    $result = mysqli_query($mysqli_link, $query);
    if ($result && mysqli_num_rows($result) != 0) {
    	$f_nick = mysqli_result($result, 0, 0);
        $back = "<P><b>Hinweis: </b>Die Freundschaft mit '$f_nick' wurde bestätigt!</P>";
    }
    return ($back);
}
?>
