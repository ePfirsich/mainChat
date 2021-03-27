<?php

function zeige_blacklist($aktion, $zeilen, $sort)
{
    
    // Zeigt Liste der Blacklist an
    
    global $id, $conn, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $conn, $u_nick, $u_id;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2;
    global $blacklistmaxdays;
    
    $blurl = $PHP_SELF . "?id=$id&http_host=$http_host&aktion=&sort=";
    
    switch ($sort) {
        case "f_zeit desc":
            $qsort = $sort . ",u_nick";
            $fsort = "f_zeit";
            $usort = "u_nick";
            break;
        
        case "f_zeit":
            $qsort = $sort . ",u_nick";
            $fsort = "f_zeit+desc";
            $usort = "u_nick";
            break;
        
        case "u_nick desc":
            $qsort = $sort . ",f_zeit desc";
            $fsort = "f_zeit+desc";
            $usort = "u_nick";
            break;
        
        case "u_nick":
            $qsort = $sort . ",f_zeit desc";
            $fsort = "f_zeit+desc";
            $usort = "u_nick+desc";
            break;
        
        default:
            $qsort = "f_zeit desc, u_nick";
            $fsort = "f_zeit";
            $usort = "u_nick";
    }
    
    switch ($aktion) {
        case "normal":
        default;
            $query = "SELECT u_nick,f_id,f_text,f_userid,f_blacklistid,"
                . "date_format(f_zeit,'%d.%m.%y %H:%i') as zeit "
                . "from blacklist left join user on f_blacklistid=u_id "
                . "order by $qsort";
            $button = "LÖSCHEN";
            $titel = "Blacklist-Einträge";
            
            // blacklist-expire
            if ($blacklistmaxdays) {
                $query2 = "DELETE FROM blacklist WHERE (TO_DAYS(NOW()) - TO_DAYS(f_zeit)>$blacklistmaxdays) ";
                mysqli_query($mysqli_link, $query2);
            }
    }
    
    echo "<FORM NAME=\"blacklist_loeschen\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"loesche\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    $result = mysqli_query($conn, $query);
    if ($result) {
        
        $anzahl = mysqli_num_rows($result);
        if ($anzahl == 0) {
            
            // Keine Blacklist-Einträge
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2><DIV style=\"color:$farbe_text;\"><B>Es gibt noch keine $titel:</B><DIV style=\"color:$farbe_text;\"></TD></TR>\n"
                . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD>&nbsp;</TD><TD align=\"left\">Es sind keine Blacklist-Einträge vorhanden.</TD></TR>";
            
        } else {
            
            // Blacklist anzeigen
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=5><DIV style=\"color:$farbe_text;\"><B>$titel: $anzahl</B></DIV></TD></TR>\n"
                . "<TR><TD WIDTH=\"5%\">" . $f1 . "Löschen" . $f2
                . "</TD><TD WIDTH=\"35%\">" . $f1 . "<A HREF=\"" . $blurl
                . $usort . "\">Nickname</A>" . $f2 . "</TD>"
                . "<TD WIDTH=\"35%\">" . $f1 . "Info" . $f2 . "</TD>"
                . "<TD WIDTH=\"13%\" ALIGN=\"CENTER\">" . $f1 . "<A HREF=\""
                . $blurl . $fsort . "\">Datum&nbsp;Eintrag</A>" . $f2
                . "</TD>\n" . "<TD WIDTH=\"13%\" ALIGN=\"CENTER\">" . $f1
                . "Eintrag&nbsp;von" . $f2 . "</TD></TR>\n";
            
            $i = 0;
            $bgcolor = $farbe_tabelle_zeile1;
            while ($row = mysqli_fetch_object($result)) {
                
                // User aus DB lesen
                $query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id,"
                    . "date_format(u_login,'%d.%m.%y %H:%i') as login, "
                    . "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online "
                    . "from user left join online on o_user=u_id "
                    . "WHERE u_id=$row->f_blacklistid ";
                $result2 = mysqli_query($conn, $query);
                if ($result2 && mysqli_num_rows($result2) > 0) {
                    
                    // User gefunden -> Ausgeben
                    $row2 = mysqli_fetch_object($result2);
                    $blacklist_nick = "<B>"
                        . user($row2->u_id, $row2, TRUE, FALSE) . "</B>";
                    
                } else {
                    
                    // User nicht gefunden, Blacklist-Eintrag löschen
                    $blacklist_nick = "NOBODY";
                    $query = "DELETE from blacklist WHERE f_id=$row->f_id";
                    $result2 = mysqli_query($conn, $query);
                    
                }
                
                // Unterscheidung online ja/nein, Text ausgeben
                if ($row2->o_id) {
                    $txt = $blacklist_nick . "<BR>online&nbsp;"
                        . gmdate("H:i:s", $row2->online) . "&nbsp;Std/Min/Sek";
                    $auf = "<B>" . $f1;
                    $zu = $f2 . "</B>";
                } else {
                    $txt = $blacklist_nick . "<BR>Letzter&nbsp;Login:&nbsp;"
                        . str_replace(" ", "&nbsp;", $row2->login);
                    $auf = $f1;
                    $zu = $f2;
                }
                
                // Infotext setzen
                if ($row->f_text == "") {
                    $infotext = "&nbsp;";
                } else {
                    $infotext = $row->f_text;
                }
                
                // Nick des Admins (Eintrager) setzen
                $f_userid = $row->f_userid;
                if ($f_userid && $f_userid != "NULL") {
                    $admin_nick = user($f_userid, "", FALSE, FALSE);
                } else {
                    $admin_nick = "";
                }
                
                echo "<TR BGCOLOR=\"$bgcolor\"><TD ALIGN=\"CENTER\">" . $auf
                    . "<INPUT TYPE=\"CHECKBOX\" NAME=\"f_blacklistid[]\" VALUE=\""
                    . $row->f_blacklistid . "\"></TD>"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"f_nick[]\" VALUE=\""
                    . $row2->u_nick . "\"></TD>" . "<TD>" . $auf . $txt . $zu
                    . "</TD>" . "<TD>" . $auf . $infotext . $zu . "</TD>"
                    . "<TD ALIGN=\"CENTER\">" . $auf . $row->zeit . $zu
                    . "</TD>" . "<TD ALIGN=\"CENTER\">" . $auf . $admin_nick
                    . $zu . "</TD>" . "</TR>\n";
                
                if (($i % 2) > 0) {
                    $bgcolor = $farbe_tabelle_zeile1;
                } else {
                    $bgcolor = $farbe_tabelle_zeile2;
                }
                $i++;
            }
            
            echo "<TR BGCOLOR=\"$bgcolor\"><TD COLSPAN=2><INPUT TYPE=\"checkbox\" onClick=\"toggle(this.checked)\">"
                . $f1 . " Alle Auswählen" . $f2 . "</TD>\n"
                . "<TD ALIGN=\"right\" COLSPAN=3>" . $f1
                . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$button\">"
                . $f2 . "</TD></TR>\n";
        }
        
        echo "</TABLE></FORM>\n";
    }
}

function loesche_blacklist($f_blacklistid)
{
    // Löscht Blacklist-Eintrag aus der Tabelle mit f_blacklistid
    // $f_blacklistid User-ID des Blacklist-Eintrags
    
    global $id, $http_host, $conn, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $conn;
    global $u_id, $u_nick, $admin;
    
    if (!$admin || !$f_blacklistid) {
        echo "Fehler beim Löschen des Blacklist-Eintrags $f_blacklistid!<BR>";
        return (0);
    }
    
    $f_blacklistid = intval($f_blacklistid);
    
    $query = "DELETE from blacklist WHERE f_blacklistid=$f_blacklistid";
    $result = mysqli_query($conn, $query);
    
    $query = "SELECT u_nick FROM user where u_id=$f_blacklistid";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) != 0) {
        $f_nick = mysql_result($result, 0, 0);
        echo "<P><B>Hinweis:</B> '$f_nick' ist nicht mehr in der Blackliste eingetragen.</P>";
    }
    @mysqli_free_result($result);
}

function formular_neuer_blacklist($neuer_blacklist)
{
    
    // Gibt Formular für Nicknamen zum Hinzufügen als Blacklist-Eintrag aus
    
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $conn, $dbase;
    global $farbe_text, $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2;
    
    if (!$eingabe_breite)
        $eingabe_breite = 30;
    
    $titel = "Neuen Blacklist-Eintrag hinzufügen:";
    
    echo "<FORM NAME=\"blacklist_neu\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"neu2\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=3 CELLSPACING=0>";
    
    if (!isset($neuer_blacklist['u_nick']))
        $neuer_blacklist['u_nick'] = "";
    if (!isset($neuer_blacklist['f_text']))
        $neuer_blacklist['f_text'] = "";
    
    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2><DIV style=\"color:$farbe_text;\"><B>$titel</B></DIV></TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD align=\"right\"><B>Nickname:</B></TD><TD>"
        . $f1
        . "<INPUT TYPE=\"TEXT\" NAME=\"neuer_blacklist[u_nick]\" VALUE=\""
        . htmlspecialchars($neuer_blacklist['u_nick'])
        . "\" SIZE=20>" . $f2 . "</TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD align=\"right\"><B>Infotext:</B></TD><TD>"
        . $f1
        . "<INPUT TYPE=\"TEXT\" NAME=\"neuer_blacklist[f_text]\" VALUE=\""
        . htmlspecialchars($neuer_blacklist['f_text'])
        . "\" SIZE=$eingabe_breite>" . "&nbsp;"
        . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"EINTRAGEN\">" . $f2
        . "</TD></TR>\n" . "</TABLE></FORM>\n";
    
}

function neuer_blacklist($f_userid, $blacklist)
{
    
    // Trägt neuen Blacklist-Eintrag in der Datenbank ein
    
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $conn, $dbase;
    
    if (!$blacklist['u_id'] || !$f_userid) {
        echo "Fehler beim Anlegen des Blacklist-Eintrags: $f_userid,$blacklist[u_id]!<BR>";
    } else {
        
        $blacklist['u_id'] = mysqli_real_escape_string($mysqli_link, $blacklist['u_id']); // sec
        $f_userid = mysqli_real_escape_string($mysqli_link, $f_userid); // sec
        
        // Prüfen ob Blacklist-Eintrag bereits in Tabelle steht
        $query = "SELECT f_id from blacklist WHERE "
            . "(f_userid=$blacklist[u_id] AND f_blacklistid=$f_userid) "
            . "OR " . "(f_userid=$f_userid AND f_blacklistid=$blacklist[u_id])";
        
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            
            echo "<P><B>Fehler:</B> '$blacklist[u_nick]' ist bereits in der Blackliste eingetragen!</P>\n";
            
        } elseif ($blacklist['u_id'] == $f_userid) {
            
            // Eigener Blacklist-Eintrag ist verboten
            echo "<P><B>Fehler:</B> Sie können sich nicht selbst als Blacklist-Eintrag hinzufügen!</P>\n";
        } else {
            
            // User ist noch kein Blacklist-Eintrag -> hinzufügen
            $f['f_userid'] = $f_userid;
            $f['f_blacklistid'] = $blacklist['u_id'];
            $f['f_text'] = htmlspecialchars($blacklist['f_text']);
            schreibe_db("blacklist", $f, 0, "f_id");
            
            echo "<P><B>Hinweis:</B> '$blacklist[u_nick]' ist jetzt in der Blacklist eingetragen.</P>";
            
        }
        
    }
}
?>
