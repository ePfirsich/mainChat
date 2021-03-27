<?php

function zeige_aktionen($aktion)
{
    
    // Zeigt Matrix der Aktionen an
    // Definition der aktionen in config.php ($def_was)
    
    global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $conn, $u_nick, $u_id;
    global $farbe_tabelle_kopf2, $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $def_was, $eingabe_breite;
    global $farbe_text, $smsfeatures, $forumfeatures;
    
    $query = "SELECT * from aktion " . "WHERE a_user=$u_id ";
    $button = "EINTRAGEN";
    $titel1 = "Für";
    $titel2 = "sind folgende Aktionen eingetragen";
    
    echo "<FORM NAME=\"freund_loeschen\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"eintragen\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "<TABLE WIDTH=100% BORDER=0 CELLPADDING=4 CELLSPACING=0>";
    
    // Einstellungen aus Datenbank lesen 
    $result = mysql_query($query, $conn);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $was[$row['a_was']][$row['a_wann']] = $row;
        }
    }
    
    // Aktionen zeigen
    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=5><DIV style=\"color:$farbe_text;\"><B>$titel1 $u_nick $titel2:</B></DIV></TD></TR>\n"
        . "<TR><TD></TD>";
    
    $i = 0;
    $bgcolor = $farbe_tabelle_zeile1;
    
    // Alle möglichen a_wann in Array lesen
    $query = "SHOW COLUMNS FROM aktion like 'a_wann'";
    $result = mysql_query($query, $conn);
    if ($result && mysqli_num_rows($result) != 0) {
        $txt = str_replace("'", "",
            substr(mysql_result($result, 0, "Type"), 5, -1));
        $a_wann = explode(",", $txt);
    }
    $anzahl_spalten = count($a_wann);
    
    // Alle möglichen a_wie in Array lesen
    $query = "SHOW COLUMNS FROM aktion like 'a_wie'";
    $result = mysql_query($query, $conn);
    if ($result && mysqli_num_rows($result) != 0) {
        $txt = str_replace("'", "",
            substr(mysql_result($result, 0, "Type"), 4, -1));
        $a_wie = explode(",", $txt);
    }
    $anzahl_wie = count($a_wie);
    
    // OLM merken
    $onlinemessage = $a_wie[3];
    
    // SMS merken
    $sms = $a_wie[4];
    
    // Wenn keine SMS angeboten werden, dann machen auch die Einstellungen diesbezüglich keinen Sinn
    if (!$smsfeatures) {
        unset($a_wie[4]);
    }
    
    // Wenn das Forum deaktiviert wird, sollte der 3. Eintrag aus $def_was raus, wichtig ist aber die Einhaltung der Reihenfolge in def_was
    if (!$forumfeatures) {
        unset($def_was[2]);
    }
    
    // Spezial Offline-Nachrichten (OLM löschen)
    $offline_wie = $a_wie;
    for ($i = 0; $i < $anzahl_wie; $i++) {
        if (isset($offline_wie[$i]) && $offline_wie[$i] == $onlinemessage)
            unset($offline_wie[$i]);
    }
    // Alle Kombinationen von offline_wie mit sms erstellen
    if ($smsfeatures) {
        for ($i = 1; $i < $anzahl_wie - 2; $i++) {
            $offline_wie[] = $offline_wie[$i] . "," . $sms;
        }
    }
    
    // Alle Kombinationen von a_wie mit onlinemessage erstellen
    // Keine muss als erstes, OLM als letztes definiert sein!
    for ($i = 1; $i < $anzahl_wie - 2; $i++) {
        $a_wie[] = $a_wie[$i] . "," . $onlinemessage;
    }
    
    // Alle Kombinationen von a_wie mit sms erstellen
    if ($smsfeatures) {
        for ($i = 1; $i < $anzahl_wie - 1; $i++) {
            $a_wie[] = $a_wie[$i] . "," . $sms;
        }
    }
    
    // Zeile der a_wann ausgeben
    foreach ($a_wann as $a_wann_eintrag) {
        echo "<TD ALIGN=\"CENTER\"><B>" . $f1 . $a_wann_eintrag . $f2
            . "</B></TD>\n";
    }
    echo "</TR>\n";
    
    // Tabelle zeilenweise ausgeben
    $i = 0;
    foreach ($def_was as $def_was_eintrag) {
        
        echo "<TR BGCOLOR=\"$bgcolor\"><TD ALIGN=\"RIGHT\"><B>" . $f1
            . $def_was_eintrag . $f2 . "</B></TD>\n";
        foreach ($a_wann as $a_wann_eintrag) {
            
            echo "<TD ALIGN=\"CENTER\">" . $f3
                . "<SELECT NAME=\"aktion_datensatz[$def_was_eintrag][$a_wann_eintrag]\">\n";
            
            // Zwischen offline/online unterscheiden
            if ($a_wann_eintrag == "Sofort/Offline") {
                $wie = $offline_wie;
            } else {
                $wie = $a_wie;
            }
            
            // Nachrichtentypen einzeln als Auswahl ausgeben
            foreach ($wie as $auswahl) {
                
                if (isset($was[$def_was_eintrag][$a_wann_eintrag])
                    && $was[$def_was_eintrag][$a_wann_eintrag]['a_wie']
                        == $auswahl) {
                    echo "<OPTION SELECTED VALUE=\""
                        . $was[$def_was_eintrag][$a_wann_eintrag]['a_id'] . "|"
                        . $auswahl . "\">" . str_replace(",", "+", $auswahl)
                        . "\n";
                } else {
                    echo "<OPTION VALUE=\""
                        . (isset($was[$def_was_eintrag][$a_wann_eintrag]) ? $was[$def_was_eintrag][$a_wann_eintrag]['a_id']
                            : "") . "|" . $auswahl . "\">"
                        . str_replace(",", " + ", $auswahl) . "\n";
                }
            }
            echo "</SELECT>" . $f4 . "</TD>\n";
            
        }
        echo "</TR>\n";
        
        if (($i % 2) > 0) {
            $bgcolor = $farbe_tabelle_zeile1;
        } else {
            $bgcolor = $farbe_tabelle_zeile2;
        }
        $i++;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD>&nbsp;</TD><TD ALIGN=\"right\" COLSPAN=4>"
        . $f1 . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$button\">" . $f2
        . "</TD></TR>\n" . "</TABLE></FORM>\n";
    
}

function eintrag_aktionen($aktion_datensatz)
{
    
    // Array mit definierten Aktionen in die DB schreiben
    
    global $def_was, $dbase, $u_id, $u_nick, $conn;
    
    // Alle möglichen a_wann in Array lesen
    $query = "SHOW COLUMNS FROM aktion like 'a_wann'";
    $result = mysql_query($query, $conn);
    if ($result && mysqli_num_rows($result) != 0) {
        $txt = str_replace("'", "",
            substr(mysql_result($result, 0, "Type"), 5, -1));
        $a_wann = explode(",", $txt);
    }
    
    foreach ($def_was as $def_was_eintrag) {
        
        foreach ($a_wann as $a_wann_eintrag) {
            
            // In aktion_datensatz stehen ID und Wert als a_id|a_wie
            $temp = explode("|",
                $aktion_datensatz[$def_was_eintrag][$a_wann_eintrag]);
            
            if (!$temp[0] || $temp[0] == "0" || $temp[0] == "") {
                $query = "DELETE FROM aktion "
                    . "WHERE a_was='" . mysql_real_escape_string($def_was_eintrag) . "' "
                    . "AND a_wann='" . mysql_real_escape_string($a_wann_eintrag) . "' " . "AND a_user='$u_id'";
                $result = mysql_query($query, $conn);
            }
            
            $f['a_wie'] = $temp[1];
            $f['a_was'] = $def_was_eintrag;
            $f['a_wann'] = $a_wann_eintrag;
            $f['a_user'] = $u_id;
            $f['a_text'] = $u_nick;
            schreibe_db("aktion", $f, $temp[0], "a_id");
        }
    }
}

?>
