<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

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
<HEAD><TITLE><?php echo $body_titel . "_Freunde"; ?></TITLE><META CHARSET=UTF-8>
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
            for(i=0; i<document.forms["freund_loeschen"].elements.length; i++) {
                 e = document.forms["freund_loeschen"].elements[i];
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
    $eingabe_breite = 30;
} else {
    $eingabe_breite = 45;
}

if ($u_id && $communityfeatures) {
    
    // Menü als erstes ausgeben
    $box = $ft0 . "Menü Freunde" . $ft1;
    $text = "<A HREF=\"freunde.php?http_host=$http_host&id=$id&aktion=\">Meine Freunde listen</A>\n"
        . "| <A HREF=\"freunde.php?http_host=$http_host&id=$id&aktion=neu\">Neuen Freund hinzufügen</A>\n"
        . "| <A HREF=\"freunde.php?http_host=$http_host&id=$id&aktion=bestaetigen\">Freundschaften bestaetigen</A>\n";
    if ($admin)
        $text .= "| <A HREF=\"freunde.php?http_host=$http_host&id=$id&aktion=admins\">Alle Admins als Freund hinzufügen</A>\n";
    $text .= "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#freunde\">Hilfe</A>\n";
    
    show_box2($box, $text, "100%");
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
    
    switch ($aktion) {
        
        case "editinfotext":
            if ((strlen($editeintrag) > 0)
                && (preg_match("/^[0-9]+$/", trim($editeintrag)) == 1)) {
                $query = "SELECT f_text FROM freunde WHERE (f_userid = $u_id or f_freundid = $u_id) AND (f_id = " . mysql_real_escape_string($editeintrag) . ")";
                $result = mysql_query($query, $conn);
                if ($result && mysql_num_rows($result) == 1) {
                    $infotext = mysql_result($result, 0, 0);
                    formular_editieren($editeintrag, $infotext);
                }
                @mysql_free_result($result);
            }
            break;
        
        case "editinfotext2":
            if ((strlen($f_id) > 0)
                && (preg_match("/^[0-9]+$/", trim($f_id)) == 1)) {
                $query = "SELECT f_text FROM freunde WHERE (f_userid = $u_id or f_freundid = $u_id) AND (f_id = " . intval($f_id) . ")";
                $result = mysql_query($query, $conn);
                if ($result && mysql_num_rows($result) == 1) {
                    // f_id ist zahl und gehört zu dem User, also ist update möglich
                    $back = edit_freund($f_id, $f_text);
                    echo "<P>$back</P>";
                    zeige_freunde("normal", "");
                }
                @mysql_free_result($result);
            }
            break;
        
        case "neu":
        // Formular für neuen Freund ausgeben
            if (!isset($neuer_freund)) {
                $neuer_freund['u_nick'] = "";
                $neuer_freund['f_text'] = "";
            }
            formular_neuer_freund($neuer_freund);
            break;
        
        case "neu2":
        // Neuer Freund, 2. Schritt: Nick Prüfen
            $neuer_freund['u_nick'] = htmlspecialchars($neuer_freund['u_nick']);
            $query = "SELECT u_id, u_level FROM user WHERE u_nick = '" . mysql_real_escape_string($neuer_freund[u_nick]) . "'";
            $result = mysql_query($query, $conn);
            if ($result && mysql_num_rows($result) == 1) {
                $neuer_freund['u_id'] = mysql_result($result, 0, 0);
                $neuer_freund['u_level'] = mysql_result($result, 0, 1);
                
                $ignore = false;
                $query2 = "SELECT * FROM iignore WHERE i_user_aktiv='$neuer_freund[u_id]' AND i_user_passiv = '$u_id'";
                $result2 = mysql_query($query2);
                $num = mysql_numrows($result2);
                if ($num >= 1) {
                    $ignore = true;
                }
                ;
                @mysql_free_result($result2);
                
                if ($ignore) {
                    echo (str_replace("%u_name%", $neuer_freund['u_nick'],
                        $t['chat_msg116']));
                    formular_neuer_freund($neuer_freund);
                } else if ($neuer_freund['u_level'] == 'Z') {
                    echo (str_replace("%u_name%", $neuer_freund['u_nick'],
                        $t['chat_msg117']));
                    formular_neuer_freund($neuer_freund);
                } else if ($neuer_freund['u_level'] == 'G') {
                    echo (str_replace("%u_name%", $neuer_freund['u_nick'],
                        $t['chat_msg118']));
                    formular_neuer_freund($neuer_freund);
                } else {
                    $back = neuer_freund($u_id, $neuer_freund);
                    echo "<P>$back</P>";
                    formular_neuer_freund($neuer_freund);
                    zeige_freunde("normal", "");
                }
                
            } elseif ($neuer_freund['u_nick'] == "") {
                echo "<B>Fehler:</B> Bitte geben Sie einen Nicknamen an!<BR>\n";
                formular_neuer_freund($neuer_freund);
            } else {
                echo "<B>Fehler:</B> Der Nickname '$neuer_freund[u_nick]' existiert nicht!<BR>\n";
                formular_neuer_freund($neuer_freund);
            }
            @mysql_free_result($result);
            break;
        
        case "admins":
        // Alle Admins (Status C und S) als Freund hinzufügen
            $query = "SELECT u_id,u_nick,u_level FROM user WHERE u_level='S' OR u_level='C'";
            $result = mysql_query($query, $conn);
            if ($result && mysql_num_rows($result) > 0) {
                while ($rows = mysql_fetch_array($result)) {
                    unset($neuer_freund);
                    $neuer_freund['u_nick'] = $rows['u_nick'];
                    $neuer_freund['u_id'] = $rows['u_id'];
                    $neuer_freund['f_text'] = $level[$rows['u_level']];
                    neuer_freund($u_id, $neuer_freund);
                }
                ;
            }
            zeige_freunde("normal", "");
            @mysql_free_result($result);
            break;
        
        case "bearbeite":
        // Freund löschen
            if ($los == "LÖSCHEN") {
                if (isset($f_freundid) && is_array($f_freundid)) {
                    // Mehrere Freunde löschen
                    foreach ($f_freundid as $key => $loesche_id) {
                        loesche_freund($loesche_id, $u_id);
                    }
                    
                } else {
                    // Einen Freund löschen
                    if (isset($f_freundid) && $f_freundid)
                        loesche_freund($f_freundid, $u_id);
                }
            }
            
            if ($los == "BESTÄTIGEN") {
                if (isset($f_freundid) && is_array($f_freundid)) {
                    // Mehrere Freunde bestätigen
                    foreach ($f_freundid as $key => $bearbeite_id) {
                        bestaetige_freund($bearbeite_id, $u_id);
                    }
                    
                } else {
                    // Einen Freund bestätigen
                    if (isset($f_freundid) && $f_freundid)
                        bestaetige_freund($f_freundid, $u_id);
                }
            }
            
            zeige_freunde("normal", "");
            break;
        
        case "bestaetigen":
            zeige_freunde("bestaetigen", "");
            break;
        
        default:
            zeige_freunde("normal", "");
    }
    
}

if ($o_js || !$u_id) {
    echo $f1
        . "<CENTER>[<A HREF=\"javascript:window.close();\">$t[sonst1]</A>]</CENTER>"
        . $f2 . "<BR>\n";
}

?>
</BODY></HTML>
