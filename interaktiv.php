<?php

// interaktiv.php muss mit id=$hash_id aufgerufen werden

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, admin
id_lese($id);

$body_tag = "<HTML><HEAD></HEAD><BODY BGCOLOR=\"$farbe_chat_background3\" ";
if (strlen($grafik_background3) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_background3\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_chat_text3\" "
    . "LINK=\"$farbe_chat_link3\" " . "VLINK=\"$farbe_chat_vlink3\" "
    . "ALINK=\"$farbe_chat_vlink3\">\n";

// Prüfung, ob User wegen Inaktivität ausgelogt werden soll
if ($u_id && $chat_timeout && $u_level != 'S' && $u_level != 'C'
    && $u_level != 'M' && $o_timeout_zeit) {
    
    if ($o_timeout_warnung == "J" && $chat_timeout < (time() - $o_timeout_zeit)) {
        
        // User ausloggen
        $zusatzjavascript = "<SCRIPT>\n"
            . "window.open(\"hilfe.php?http_host=$http_host&id=$id&aktion=logout\",'Logout',\"resizable=yes,scrollbars=yes,width=300,height=300\")\n"
            . "</SCRIPT>\n";
        require_once("functions.php-func-verlasse_chat.php");
        verlasse_chat($u_id, $u_nick, $o_raum);
        logout($o_id, $u_id, "interaktiv->timeout");
        unset($u_id);
        unset($o_id);
        
    } elseif ($o_timeout_warnung != "J"
        && (($chat_timeout / 4) * 3) < (time() - $o_timeout_zeit)) {
        
        // Warnung über bevorstehenden Logout ausgeben
        system_msg("", 0, $u_id, $system_farbe,
            str_replace("%zeit%", $chat_timeout / 60, $t['chat_msg101']));
        unset($f);
        $f['o_timeout_warnung'] = "J";
        schreibe_db("online", $f, $o_id, "o_id");
        
    }
} else {
    $zusatzjavascript = "";
}

if (isset($u_id) && $u_id) {
?>
<HTML><HEAD><TITLE><?php echo $body_titel; ?></TITLE><meta charset="utf-8">
<META HTTP-EQUIV="REFRESH" CONTENT="<?php echo intval($timeout / 3)
        . "; URL=interaktiv.php?http_host=$http_host&id=$id&o_raum_alt=$o_raum";
                                    ?>">
<SCRIPT>
        function chat_reload(file) {
                parent.chat.location.href=file;
}
        function frame_online_reload(file) {
                parent.frame_online.location.href=file;
}
</SCRIPT>
<?php echo $stylesheet; ?>
</HEAD> <?php
    
    echo $body_tag;
    
    // Timestamp im Datensatz aktualisieren
    aktualisiere_online($u_id, $o_raum);
    
    // Aktionen ausführen, falls nicht innerhalb der letzten 5
    // Minuten geprüft wurde (letzte Prüfung=o_aktion)
    if ($communityfeatures && (time() > ($o_aktion + 300))) {
        aktion("Alle 5 Minuten", $u_id, $u_nick, $id);
    }
    
    // Wurde Raum r_id aus Formular übergeben? Falls ja Raum von $o_raum nach $r_id wechseln
    if (isset($r_id) && $o_raum != $r_id) {
        // Raum wechseln
        // Im Beichtstuhl-Modus dürfen Admins geschlossene Räume betreten
        $o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $r_id,
            isset($beichtstuhl) ? $beichtstuhl : null);
        if ($o_raum == $r_id) {
            // User in Raum ausgeben
            raum_user($r_id, $u_id, $id);
        }
        
        // Falls Pull-Chat, chat-Fenster neu laden
        if ($backup_chat || $u_backup) {
            echo "<SCRIPT LANGUAGE=JavaScript>"
                . "chat_reload('chat.php?http_host=$http_host&id=$id')"
                . "</SCRIPT>\n";
        }
    }
    
    // Daten für Raum lesen
    $query = "SELECT raum.* FROM raum,online WHERE r_id=o_raum "
        . "AND o_id=$o_id ORDER BY o_aktiv DESC";
    
    $result = mysqli_query($mysqli_link, $query);
    
    if ($result && mysqli_num_rows($result) != 0) {
        $row = mysqli_fetch_object($result);
        $o_raum = $row->r_id;
    }
    mysqli_free_result($result);
    
    if (!isset($o_raum_alt))
        $o_raum_alt = -9;
    
    // Optional via JavaScript den oberen Werbeframe mit dem Werbeframe des Raums neu laden
    if ($erweitertefeatures && $o_js && $o_raum_alt != $o_raum) {
        if (isset($row->r_werbung) && strlen($row->r_werbung) > 7) {
            echo "<SCRIPT>\n"
                . "frame_online_reload('$row->r_werbung?http_host=$http_host');\n</SCRIPT>\n";
        } elseif (isset($frame_online) && $frame_online != "") {
            echo "<SCRIPT>\n"
                . "frame_online_reload('$frame_online?http_host=$http_host');\n</SCRIPT>\n";
        }
    }
    
    // Menue Ausgeben:
    
    echo "<FORM ACTION=\"" . $chat_url
        . "interaktiv.php\" NAME=form1 METHOD=POST>\n";
    echo "<TABLE BORDER=0 CELLSPACING=3 CELLPADDING=0 HEIGHT=100%>\n";
    
    // Benutzer-Menue
    
    // Anzahl der User insgesamt feststellen
    $query = "SELECT count(o_id) as anzahl FROM online "
        . "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
    $result = mysqli_query($mysqli_link, $query);
    if ($result && mysqli_num_rows($result) != 0) {
    	$anzahl_gesamt = mysqli_result($result, 0, "anzahl");
        mysqli_free_result($result);
    }
    
    // Anzahl der User in diesem Raum feststellen
    $query = "SELECT count(o_id) as anzahl FROM online "
        . "WHERE o_raum=$o_raum AND "
        . "(UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
    $result = mysqli_query($mysqli_link, $query);
    if ($result && mysqli_num_rows($result) != 0) {
    	$anzahl_raum = mysqli_result($result, 0, "anzahl");
        mysqli_free_result($result);
    }
    
    if ($u_level != "M") {
        echo "<TR><TD>&nbsp;</TD><TD>";
    } else {
        echo "<TR><TD>";
    }
    echo $f1;
    
    if ($anzahl_raum == 1) {
        $txt = str_replace("%anzahl_raum%", $anzahl_raum, $t['sonst2']);
        if (!isset($chattext['chatter'])) {
            $txt = str_replace("%chatter%", $t['sonst5'], $txt);
            $txt = str_replace("%einzahl%", $t['sonst7'], $txt);
        } else {
            $txt = str_replace("%chatter%", $chattext['chatter'], $txt);
            $txt = str_replace("%einzahl%", $chattext['einzahl'], $txt);
        }
    } else {
        $txt = str_replace("%anzahl_raum%", $anzahl_raum, $t['sonst3']);
        if (!isset($chattext['chattern'])) {
            $txt = str_replace("%chattern%", $t['sonst6'], $txt);
        } else {
            $txt = str_replace("%chattern%", $chattext['chattern'], $txt);
        }
    }
    if (!(($u_level == 'U' || $level == 'G')
        && (isset($useronline_anzeige_deaktivieren)
            && $useronline_anzeige_deaktivieren == "1"))) {
        echo " " . str_replace("%anzahl_gesamt%", $anzahl_gesamt, $txt) . $f2
            . "</TD>";
    }
    
    if ($u_level != "M") {
        echo "</TD><TD></TD><TD>";
    } else {
        echo "</TD></TR><TR><TD>";
    }
    
    // Special: Bei nur einem Raum kein Auswahl
    $query = "SELECT count(*) as zahl FROM raum";
    $result = mysqli_query($mysqli_link, $query);
    $a = mysqli_fetch_array($result);
    $zahl = $a['zahl'];
    
    if (($u_level == "U" || $u_level == "G") && ($single_room_verhalten == "1"))
        $zahl = 0;
    
    if ($zahl > 1) {
        echo $f3
            . "&nbsp;&nbsp;$t[sonst4]<br>&nbsp;<NOBR><SELECT NAME=\"r_id\"\" onChange=\"document.form1.submit()\">\n";
        
        // Admin sehen alle Räume, andere User nur die offenen
        if ($admin) {
            raeume_auswahl($o_raum, TRUE, TRUE);
        } else {
            raeume_auswahl($o_raum, FALSE, TRUE);
        }
        
        echo "</SELECT>" . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"o_raum_alt\" VALUE=\"$o_raum\">"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"neuer_raum\" VALUE=\"1\">";
        
        if ($u_level != "M") {
            echo "<INPUT TYPE=\"SUBMIT\" NAME=\"raum_submit\" VALUE=\"Go!\">&nbsp;</NOBR>"
                . $f4 . "</TD>\n";
        } else {
            echo "<BR>&nbsp;"
                . "<INPUT TYPE=\"SUBMIT\" NAME=\"raum_submit\" VALUE=\"Go!\">&nbsp;</NOBR>"
                . $f4 . "</TD>\n" . "</tr><tr>";
        }
    }
    
    echo "<TD>\n";
    
    // Werbung ausgeben
    if ($u_level != "M") {
        werbung('interaktiv', $werbung_gruppe);
    }
    
    echo "</TD></TR>\n" . "</TABLE></FORM>\n";
    echo "</BODY></HTML>\n";
    
} else {
    // User wird nicht gefunden. Login ausgeben
    echo "<HTML><HEAD>$zusatzjavascript</HEAD>"
        . "<BODY onLoad='javascript:parent.location.href=\"index.php?http_host=$http_host\"'>\n"
        . "</BODY></HTML>\n";
    exit;
}
        ?>