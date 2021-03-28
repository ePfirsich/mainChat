<?php

require_once("functions.php");
require_once("functions.php-func-verlasse_chat.php");
require_once("functions.php-func-nachricht.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

if (!isset($suchtext))
    $suchtext = "";
$uu_suchtext = URLENCODE($suchtext);

// welcher raum soll abgefragt werden? ggf voreinstellen
// Positives $schau_raum entspricht r_id
// Negatives $schau_raum entspricht o_who * -1

if (isset($schau_raum) && !is_numeric($schau_raum)) {
    unset($schau_raum);
}

if ((isset($schau_raum)) && $schau_raum < 0) {
    // Community-Bereich gewählt
    $raum_subquery = "AND o_who=" . ($schau_raum * (-1));
} elseif ((isset($schau_raum)) && $schau_raum > 0) {
    // Raum gewählt
    $raum_subquery = "AND r_id=$schau_raum";
} elseif (!isset($schau_raum) && $o_who != 0) {
    // Voreinstellung auf den aktuellen Community-Bereich
    $schau_raum = $o_who * (-1);
    $raum_subquery = "AND o_who=$o_who";
} elseif (!isset($schau_raum) || $schau_raum == 0) {
    // Voreinstellung auf den aktuellen Raum
    $schau_raum = $o_raum;
    $raum_subquery = "AND r_id=$schau_raum";
}

// Welcher Browser wird benutzt? Breite der Eingabefelder einstellen
if (ist_netscape()) {
    $eingabe_breite = 18;
} else {
    $eingabe_breite = 31;
}

if ($aktion != "zeigalle" || $u_level != "S") {
    echo "<HTML>\n<HEAD><TITLE>" . $body_titel
        . "_Info</TITLE><META CHARSET=UTF-8>\n";
    
    if ($aktion == "chatuserliste") {
        echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"" . intval($timeout / 3)
            . "; URL=user.php?http_host=$http_host&id=$id&aktion=chatuserliste\">\n";
    }
    
    // Target Sonderzeichen raus...
    $fenster = str_replace("+", "", $u_nick);
    $fenster = str_replace("-", "", $fenster);
    $fenster = str_replace("ä", "", $fenster);
    $fenster = str_replace("ö", "", $fenster);
    $fenster = str_replace("ü", "", $fenster);
    $fenster = str_replace("Ä", "", $fenster);
    $fenster = str_replace("Ö", "", $fenster);
    $fenster = str_replace("Ü", "", $fenster);
    $fenster = str_replace("ß", "", $fenster);
    
    echo "<SCRIPT>\n" . "  var http_host='$http_host';\n" . "  var id='$id';\n"
        . "  var u_nick='" . $fenster . "';\n" . "  var raum='$schau_raum';\n"
        . "  var communityfeatures='$communityfeatures';\n";
    if (($admin) || ($u_level == 'M')) {
        echo "  var inaktiv_ansprechen='0';\n"
            . "  var inaktiv_mailsymbol='0';\n"
            . "  var inaktiv_userfunktionen='0';\n";
    } else {
        echo "  var inaktiv_ansprechen='$userleiste_inaktiv_ansprechen';\n"
            . "  var inaktiv_mailsymbol='$userleiste_inaktiv_mailsymbol';\n"
            . "  var inaktiv_userfunktionen='$userleiste_inaktiv_userfunktionen';\n";
    }
    
    echo "  var stdparm='?http_host='+http_host+'&id='+id+'&schau_raum='+raum;\n"
        . "  var stdparm2='?http_host='+http_host+'&id='+id;\n";
    if ($aktion != "chatuserliste") {
?>
window.focus();
function resetinput() {
    document.forms['form'].elements['text'].value=document.forms['form'].elements['text2'].value;
		<?php
        if ($u_clearedit == 1) {
            echo "    document.forms['form'].elements['text2'].value='';\n";
        }
        ?>
    document.forms['form'].submit();
    document.forms['form'].elements['text2'].focus();
    document.forms['form'].elements['text2'].select();
}
   function neuesFenster2(url) {
                hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
   }
		<?php
    }
    echo "</SCRIPT>\n";
    echo "<SCRIPT LANGUAGE=\"JavaScript\" src=\"jscript.js\"></script>\n";
    echo $stylesheet;
    
    echo "</HEAD>\n";
    
    // Body-Tag definieren
    echo "<BODY BGCOLOR=\"$farbe_mini_background\" "
        . (strlen($grafik_mini_background) > 0 ? "BACKGROUND=\"$grafik_mini_background\" "
            : "") . "TEXT=\"$farbe_mini_text\" " . "LINK=\"$farbe_mini_link\" "
        . "VLINK=\"$farbe_mini_vlink\" " . "ALINK=\"$farbe_mini_vlink\">\n";
}

// Login ok?
if (strlen($u_id) != 0) {
    // Fenstername
    $fenster = str_replace("+", "", $u_nick);
    $fenster = str_replace("-", "", $u_nick);
    
    // Timestamp im Datensatz aktualisieren
    aktualisiere_online($u_id, $o_raum);
    
    if ($aktion != "chatuserliste" && $aktion != "statistik") {
        // Menü als erstes ausgeben, falls nicht Spezialfall im Chatwindow oder statistikfenster.
        $box = $ft0 . $t['sonst2'] . $ft1;
        $text = "<LI><A HREF=\"user.php?http_host=$http_host&id=$id&schau_raum=$schau_raum\">$t[menue1]</A>\n";
        if ($u_level != "G") {
            $text .= "<LI><A HREF=\"user.php?http_host=$http_host&id=$id&schau_raum=$schau_raum&aktion=suche\">$t[menue2]</A>\n";
        }
        if ($adminlisteabrufbar && $u_level != "G") {
            $text .= "<LI><A HREF=\"user.php?http_host=$http_host&id=$id&schau_raum=$schau_raum&aktion=adminliste\">$t[menue12]</A>\n";
        }
        if ($communityfeatures && $u_level != "G") {
            if ($punktefeatures) {
                $ur1 = "top10.php?http_host=$http_host&id=$id";
                $url = "HREF=\"$ur1\" TARGET=\"640_$fenster\" onclick=\"window.open('$ur1','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\"";
                $text .= "<LI><A $url>$t[menue7]</A>\n";
            }
            ;
            $ur1 = "freunde.php?http_host=$http_host&id=$id";
            $url = "HREF=\"$ur1\" TARGET=\"640_$fenster\" onclick=\"window.open('$ur1','640_$fenster','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\"";
            $text .= "<LI><A $url>$t[menue8]</A>\n";
        }
        //	if ($admin && $erweitertefeatures) {
        if ($admin) {
            $text .= "<LI><A HREF=\"user.php?http_host=$http_host&id=$id&schau_raum=$schau_raum&aktion=zeigalle\">$t[menue3]</A>\n";
            if ($userimport) {
                $text .= "<LI><A HREF=\"user.php?http_host=$http_host&id=$id&schau_raum=$schau_raum&aktion=userimport\">$t[menue10]</A>\n";
                $text .= "<LI><A HREF=\"user.php?http_host=$http_host&id=$id&schau_raum=$schau_raum&aktion=userloeschen\">$t[menue11]</A>\n";
            }
        }
        if ($erweitertefeatures && $admin) {
            $ur1 = "user.php?http_host=$http_host&id=$id&aktion=statistik";
            $url = "HREF=\"$ur1\" TARGET=\"640_statistik\" onclick=\"window.open('$ur1','640_statistik','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\"";
            $text .= "<LI><A $url>$t[menue9]</A>\n";
        }
        if ($communityfeatures && $umfragefeatures && $admin) {
            $ur1 = "umfrage.php?http_host=$http_host&id=$id&adminuebersicht=1";
            $url = "HREF=\"$ur1\" TARGET=\"640_statistik\" onclick=\"window.open('$ur1','640_statistik','resizable=yes,scrollbars=yes,width=780,height=580'); return(false);\"";
            $text .= "<LI><A $url>$t[menue13]</A>\n";
        }
        if ($aktion != "zeigalle" || $u_level != "S") {
            show_box2($box, $text, "100%");
            echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
        }
    }
    
    if ($admin && isset($kick_user_chat) && $user) {
        // Nur Admins: User sofort aus dem Chat kicken
        $query = "SELECT o_id,o_raum,o_name FROM online "
            . "WHERE o_user='" . mysqli_real_escape_string($mysqli_link, $user) . "' "
            . "AND o_level!='C' AND o_level!='S' AND o_level!='A' ";
        $result = mysqli_query($mysqli_link, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_object($result);
            verlasse_chat($user, $row->o_name, $row->o_raum);
            logout($row->o_id, $user, "user->kick " . $u_id . " " . $u_nick);
            
            mysqli_free_result($result);
        } else {
            echo $t['sonst40'];
        }
    }
    
    // Traceroute ausgeben
    if ($admin AND isset($trace)) {
        if (!file_exists($traceroute)) {
            system_msg("", 0, $u_id, $system_farbe,
                "<b>config anpassen \$traceroute</b>");
        }
        
        $box = $ft0 . $t['sonst3'] . " " . $trace . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf><TR><TD>"
            . "<A HREF=\"#\" onClick=\"window.close();\">"
            . "<IMG SRC=\"pics/button-x.gif\" ALT=\"schließen\" WIDTH=15 HEIGHT=13 ALIGN=\"RIGHT\" BORDER=0></A>\n"
            . "<FONT SIZE=-1 COLOR=$farbe_text><B>$box</B></FONT>\n"
            . "</TD></TR></TABLE>\n<PRE>" . $f1;
        system("$traceroute $trace", $ergebnis);
        echo $f2 . "</PRE>\n";
    }
    
    // Auswahl
    switch ($aktion) {
        
        case "userimport2":
        // User importieren
            if ($u_level == "S") {
                if (is_uploaded_file($HTTP_POST_FILES['userdatei']['tmp_name'])) {
                    $file = fopen($HTTP_POST_FILES['userdatei']['tmp_name'],
                        "r");
                    if ($file) {
                        
                        echo "import.....<BR><SMALL>";
                        $i = 1;
                        while (!feof($file)) {
                            $zeile = rtrim(fgets($file, 4096));
                            $userd = explode("\t", $zeile);
                            if ($userd[0] != "Nickname"
                                AND strlen($userd[0]) > 2 AND $userd[1]
                                AND $userd[2] AND $userd[3]) {
                                
                                echo " $i " . $userd[0];
                                
                                unset($f);
                                unset($ui_userid);
                                $f['u_nick'] = $userd[0];
                                $f['u_name'] = $userd[1];
                                $f['u_adminemail'] = $userd[2];
                                $f['u_passwort'] = $userd[3];
                                $query = "SELECT u_id FROM user where u_nick like '" . mysqli_real_escape_string($mysqli_link, $f[u_nick]) . "'"; // User importieren
                                $result = mysqli_query($mysqli_link, $query);
                                if ($result && mysqli_num_rows($result)) {
                                	$ui_userid = mysqli_result($result, 0, 0);
                                    echo " ID: $ui_userid";
                                }
                                
                                if (schreibe_db("user", $f, $ui_userid, "u_id")) {
                                    echo " Ok";
                                }
                                
                                echo "<BR>\n";
                                $i++;
                            }
                        }
                        echo "</SMALL><BR><BR>";
                        
                        fclose($file);
                        unlink($HTTP_POST_FILES['userdatei']['tmp_name']);
                    } else {
                        echo $t['sonst48'];
                    }
                } else {
                    echo $t['sonst47'];
                }
            } else {
                echo "<HTML><BODY>$t[sonst41]</BODY></HTML>\n";
            }
        
        case "userimport":
        // User im Format "Nickname\tUsername\tAdmin E-Mail\tPasswort" importieren
        // Formular ausgeben
            if ($u_level == "S") {
                
                $box = $ft0 . $t['sonst45'] . $ft1;
                $text = "<FORM NAME=\"userimport2\" enctype=\"multipart/form-data\" ACTION=\"user.php\" method=\"post\">\n"
                    . "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"userimport2\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"userimport2\">\n"
                    . "<TR><TD colspan=2>" . $f1
                    . htmlspecialchars($t['sonst46']) . $f2 . "</TD></TR>\n"
                    . "<TR><TD colspan=2><IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=10></TD></TR>\n"
                    . "<TR><TD colspan=2>" . $f1
                    . "&nbsp;<INPUT name=\"userdatei\" type=\"file\" SIZE=\"$eingabe_breite\">"
                    . $f2 . "</TD></TR>\n";
                
                $text .= "<TR><TD colspan=2 ALIGN=RIGHT>" . $f1
                    . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"Go!\">"
                    . $f2 . "</TD></TR>" . "</TABLE></FORM>\n";
                show_box2($box, $text, "100%");
                echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
                
            } else {
                echo "<HTML><BODY>$t[sonst41]</BODY></HTML>\n";
            }
            break;
        
        case "userloeschen":
        // User Löschen
            if ($u_level == "S") {
                
                $box = $ft0 . $t['sonst51'] . $ft1;
                $text = "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>"
                    . "<TR><TD colspan=2>" . $f1 . $t['sonst49']
                    . " <A HREF=\"user.php?http_host=$http_host&id=$id&aktion=userloeschen2\">["
                    . $t['sonst50'] . "]</A>" . $f2 . "</TD></TR>\n"
                    . "</TABLE>\n";
                show_box2($box, $text, "100%");
                echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
                
            } else {
                echo "<HTML><BODY>$t[sonst41]</BODY></HTML>\n";
            }
            break;
        
        case "userloeschen2":
        // User Löschen
            if ($u_level == "S") {
                
                $query = "DELETE FROM user where u_level != 'C' AND u_level != 'S'";
                $result = mysqli_query($mysqli_link, $query);
                if ($result) {
                    $ok = mysqli_affected_rows($mysqli_link) . " " . $ok = $t['sonst52'];
                } else {
                    $ok = $t['sonst53'];
                }
                
                $box = $ft0 . $t['sonst51'] . $ft1;
                $text = "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>"
                    . "<TR><TD colspan=2>" . $f1 . $ok . $f2 . "</TD></TR>\n"
                    . "</TABLE>\n";
                show_box2($box, $text, "100%");
                echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
                
            } else {
                echo "<HTML><BODY>$t[sonst41]</BODY></HTML>\n";
            }
            break;
        
        case "zeigalle":
            if ($u_level == "S") {
                
                // Alle User listen
                $query = "SELECT u_nick,u_name,u_email,u_adminemail,u_url,u_level,u_punkte_gesamt,"
                    . "date_format(u_login,'%d.%m.%y %H:%i') as login FROM user "
                    . "ORDER BY u_nick";
                
                $result = mysqli_query($mysqli_link, $query);
                $rows = mysqli_num_rows($result);
                
                if (!$result || mysqli_num_rows($result) == 0) {
                    echo "<HTML><BODY><P>$t[sonst4]</P></BODY></HTML>\n";
                } else {
                    
                    Header(
                        "Content-Disposition: attachment; filename=\"mainChat-Export.xls\"");
                    Header("Pragma: no-cache");
                    Header("Expires: 0");
                    Header("ETag: 900aa-551-3c45620d");
                    Header("Accept-Ranges: bytes");
                    Header("Keep-Alive: timeout=15, max=100");
                    Header("Connection: Keep-Alive");
                    Header("Content-Type: application/vnd.ms-excel");
                    
                    if ($erweitertefeatures) {
                        echo $t['sonst26'];
                    } else {
                        echo $t['sonst26b'];
                    }
                    
                    while ($row = mysqli_fetch_object($result)) {
                        if ($erweitertefeatures) {
                            echo str_replace("\\", "",
                                htmlspecialchars($row->u_nick)) . "\t"
                                . str_replace("\\", "",
                                    htmlspecialchars($row->u_name)) . "\t"
                                . str_replace("\\", "",
                                    htmlspecialchars($row->u_adminemail))
                                . "\t"
                                . str_replace("\\", "",
                                    htmlspecialchars($row->u_email)) . "\t"
                                . str_replace("\\", "",
                                    htmlspecialchars($row->u_url)) . "\t"
                                . $row->u_punkte_gesamt . "\t" . $row->login
                                . "\t" . $row->u_level . "\n";
                        } else {
                            echo str_replace("\\", "",
                                htmlspecialchars($row->u_nick)) . "\t"
                                . str_replace("\\", "",
                                    htmlspecialchars($row->u_adminemail))
                                . "\t"
                                . str_replace("\\", "",
                                    htmlspecialchars($row->u_email)) . "\t"
                                . $row->u_level . "\n";
                            
                        }
                    }
                }
            } else {
                echo "<HTML><BODY>$t[sonst41]</BODY></HTML>\n";
            }
            break;
        
        case "suche":
        // Suchmaske für Ergebnis oder Suchmaske für erste Suche ausgeben
            if (strlen($suchtext) > 3
                || (isset($suchtext_eingabe) && $suchtext_eingabe == "Go!")) {
                
                // Suchergebnis mit Formular ausgeben
                $box = $ft0 . $t['sonst16'] . $ft1;
                $text = "<FORM NAME=\"suche\" ACTION=\"user.php\" METHOD=POST>\n"
                    . "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"suche\">\n"
                    . "<TR><TD colspan=2>" . $f1 . $t['sonst6'] . $f2
                    . "</TD></TR>" . "<TR><TD colspan=2>" . $f1
                    . "&nbsp;<INPUT TYPE=\"TEXT\" NAME=\"suchtext\" VALUE=\"$suchtext\" SIZE=\"$eingabe_breite\">"
                    . $f2 . "</TD></TR>\n";
                
                if ($admin) {
                    
                    // Suchformular nach IP-Adressen
                    $text .= "<TR><TD colspan=2><BR>" . $f1 . $t['sonst30']
                        . $f2 . "</TD></TR>" . "<TR><TD ALIGN=RIGHT>" . $f1
                        . $t['sonst31'] . $f2 . "</TD><TD>" . $f1
                        . "&nbsp;<INPUT TYPE=\"TEXT\" NAME=\"f[ip]\" VALUE=\"$f[ip]\" SIZE=\"6\">"
                        . $f2 . "</TD></TR>\n";
                    
                    // Liste der Gruppen ausgeben
                    $text .= "<TR><TD ALIGN=RIGHT>" . $f1 . $t['sonst21']
                        . "</TD>\n" . "<TD>&nbsp;<SELECT NAME=\"f[level]\">\n"
                        . "<OPTION VALUE=\"\">$t[sonst22]\n";
                    
                    reset($level);
                    while (list($levelname, $levelbezeichnung) = each($level)) {
                        if ($levelname != "B") {
                            if ($f['level'] == $levelname) {
                                $text .= "<OPTION SELECTED VALUE=\"$levelname\">$levelbezeichnung\n";
                            } else {
                                $text .= "<OPTION VALUE=\"$levelname\">$levelbezeichnung\n";
                            }
                        }
                    }
                    
                    $text .= "</SELECT>" . $f2 . "</TD></TR>\n";
                    
                }
                
                // Suche nach Neu & erstem Login
                $text .= "<TR><TD ALIGN=RIGHT>" . $f1 . $t['sonst32'] . $f2
                    . "</TD><TD>" . $f1
                    . "&nbsp;<INPUT TYPE=\"TEXT\" NAME=\"f[user_neu]\" VALUE=\"$f[user_neu]\" SIZE=\"6\">&nbsp;"
                    . $t['sonst34'] . $f2 . "</TD></TR>\n"
                    . "<TR><TD ALIGN=RIGHT>" . $f1 . $t['sonst33'] . $f2
                    . "</TD><TD>" . $f1
                    . "&nbsp;<INPUT TYPE=\"TEXT\" NAME=\"f[user_login]\" VALUE=\"$f[user_login]\" SIZE=\"6\">&nbsp;"
                    . $t['sonst35'] . $f2 . "</TD></TR>\n";
                
                // Suche nach User mit Homepage
                $text .= "<TR><TD ALIGN=RIGHT>" . $f1 . $t['sonst38']
                    . "</TD>\n"
                    . "<TD>&nbsp;<SELECT NAME=\"f[u_chathomepage]\">\n";
                if ($f['u_chathomepage'] == "J") {
                    $text .= "<OPTION VALUE=\"N\">$t[sonst22]\n"
                        . "<OPTION SELECTED VALUE=\"J\">$t[sonst39]\n";
                } else {
                    $text .= "<OPTION SELECTED VALUE=\"N\">$t[sonst22]\n"
                        . "<OPTION VALUE=\"J\">$t[sonst39]\n";
                }
                $text .= "</SELECT>" . $f2 . "</TD></TR>\n";
                
                $text .= "<TR><TD colspan=2 ALIGN=RIGHT>" . $f1
                    . "<INPUT TYPE=\"SUBMIT\" NAME=\"suchtext_eingabe\" VALUE=\"Go!\">"
                    . $f2 . "</TD></TR>" . "</TABLE></FORM>\n";
                show_box2($box, $text, "100%");
                echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
                
                // wenn einer nur nach % sucht, kanns auch weggelassen werden
                if ($suchtext == "%")
                    unset($suchtext);
                
                // Variablen säubern
                unset($query);
                unset($where);
                unset($subquery);
                unset($sortierung);
                
                $subquery = "";
                
                // Grundselect
                $select = "SELECT * FROM user ";
                
                // Where bedingung anhand des Suchtextes und des Levels
                if (!isset($suchtext) || !$suchtext) {
                    // Kein Suchtext, aber evtl subquery
                    $where[0] = "WHERE ";
                } elseif (strpos($suchtext, "%") >= 0) {
                    $suchtext = mysqli_real_escape_string($mysqli_link, $suchtext);
                    if ($admin) {
                        $where[0] = "WHERE (u_name LIKE '$suchtext' OR u_nick LIKE '$suchtext' OR u_email LIKE '$suchtext' "
                            . " OR u_adminemail LIKE '$suchtext') ";
                    } else {
                        $where[0] = "WHERE (u_nick LIKE '$suchtext' OR u_email LIKE '$suchtext') ";
                    }
                } else {
                    $suchtext = mysqli_real_escape_string($mysqli_link, $suchtext);
                    if ($admin) {
                        $where[0] = "WHERE u_name = '$suchtext' ";
                        $where[1] = "WHERE u_nick = '$suchtext' ";
                        $where[2] = "WHERE u_email = '$suchtext' ";
                        $where[3] = "WHERE u_adminemail = '$suchtext' ";
                    } else {
                        $where[0] = "WHERE u_nick = '$suchtext' ";
                        $where[1] = "WHERE u_email = '$suchtext' ";
                    }
                    
                }
                
                // Subquerys, wenn parameter gesetzt, teils anhand des Levels		
                if ($admin) {
                    // Optional level ergänzen
                    if ($f['level'] && $subquery)
                        $subquery .= "AND u_level = '" . mysqli_real_escape_string($mysqli_link, $f[level]) . "' ";
                    elseif ($f['level'])
                        $subquery = " u_level = '" . mysqli_real_escape_string($mysqli_link, $f[level]) . "' ";
                    
                    if ($f['ip'] && $subquery)
                        $subquery .= "AND u_ip_historie LIKE '%" . mysqli_real_escape_string($mysqli_link, $f[ip]) . "%' ";
                    elseif ($f['ip'])
                        $subquery = " u_ip_historie LIKE '%" . mysqli_real_escape_string($mysqli_link, $f[ip]) . "%' ";
                }
                
                if ($f['u_chathomepage'] == "J" && $subquery)
                    $subquery .= "AND u_chathomepage='J' ";
                elseif ($f['u_chathomepage'] == "J")
                    $subquery = " u_chathomepage='J' ";
                
                if ($f['user_neu'] && $subquery)
                    $subquery .= "AND u_neu IS NOT NULL AND date_add(u_neu, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_neu]) . "' day)>=NOW() ";
                elseif ($f['user_neu'])
                    $subquery = " u_neu IS NOT NULL AND date_add(u_neu, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_neu]) . "' day)>=NOW() ";
                
                if ($f['user_login'] && $subquery)
                    $subquery .= "AND date_add(u_login, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_login]) . "' hour)>=NOW() ";
                elseif ($f['user_login'])
                    $subquery = " date_add(u_login, interval '" . mysqli_real_escape_string($mysqli_link, $f[user_login]) . "' hour)>=NOW() ";
                
                if ($u_level == "U" && $subquery)
                    $subquery .= "AND u_level IN ('A','C','G','M','S','U') ";
                elseif ($u_level == "U")
                    $subquery = " u_level IN ('A','C','G','M','S','U') ";
                
                // Zur Sicherheit, falls ein Gast die Such URL direkt aufruft
                if ($u_level == "G")
                    $subquery = " 1=2 ";
                
                // Sortierung
                if ($admin) {
                    $sortierung = "order by u_nick,u_name ";
                } else {
                    $sortierung = "order by u_nick,u_name limit 0,$max_user_liste ";
                }
                
                // Zusammensetzen der Query
                $query = $select;
                if ($where[0] == "WHERE " && $subquery)
                    $query .= $where[0] . " " . $subquery;
                elseif ($where[0] != "WHERE " && $subquery)
                    $query .= $where[0] . " AND " . $subquery;
                elseif ($where[0] != "WHERE ")
                    $query .= $where[0];
                
                for ($i = 1; $i < count($where); $i++) {
                    if ($where[$i]) {
                        $query .= "UNION " . $select . $where[$i];
                        if ($subquery)
                            $query .= " AND " . $subquery;
                    }
                }
                $query .= $sortierung;
                
                $result = mysqli_query($mysqli_link, $query);
                $anzahl = mysqli_num_rows($result);
                
                if ($anzahl == 0) {
                    // Kein user gefunden
                    if ($suchtext == "%")
                        $suchtext = "";
                    echo "<P>"
                        . str_replace("%suchtext%", $suchtext, $t['sonst5'])
                        . "</P>\n";
                } elseif ($anzahl == 1) {
                    // Einen User gefunden, alle Userdaten ausgeben
                    if (!isset($zeigeip))
                        $zeigeip = 0;
                    while ($arr = mysqli_fetch_array($result)) {
                        user_zeige($arr['u_id'], $admin, $schau_raum, $u_level,
                            $zeigeip);
                    }
                } else {
                    
                    // Bei mehr als 1000 Ergebnissen Fehlermeldung ausgeben
                    if ($anzahl > 2000) {
                        echo "<B>$t[sonst24]</B><BR>";
                    } else {
                        
                        // Mehrere User gefunden, als Tabelle ausgeben
                        
                        for ($i = 0; $row = mysqli_fetch_array($result,
                            MYSQLI_ASSOC); $i++) {
                            
                            // Array mit Userdaten und Infotexten aufbauen
                            $larr[$i]['u_email'] = str_replace("\\", "",
                                htmlspecialchars($row['u_email']));
                            $larr[$i]['u_nick'] = strtr(
                                str_replace("\\", "",
                                    htmlspecialchars($row['u_nick'])), "I",
                                "i");
                            $larr[$i]['u_level'] = $row['u_level'];
                            $larr[$i]['u_id'] = $row['u_id'];
                            $larr[$i]['u_away'] = $row['u_away'];
                            if ($communityfeatures) {
                                // Wenn der User nicht möchte, daß sein Würfel angezeigt wird, ist hier die einfachste Möglichkeit
                                if ($row['u_punkte_anzeigen'] != "N") {
                                    $larr[$i]['gruppe'] = hexdec(
                                        $row['u_punkte_gruppe']);
                                } else {
                                    $larr[$i]['gruppe'] = 0;
                                }
                                $larr[$i]['u_chathomepage'] = $row['u_chathomepage'];
                            }
                            // Raumbesitzer einstellen, falls Level=User
                            if (isset($larr[$i]['isowner'])
                                && $larr[$i]['isowner']
                                && $userdata['u_level'] == "U")
                                $larr[$i]['u_level'] = "B";
                            
                            flush();
                        }
                        mysqli_free_result($result);
                        
                        $rows = count($larr);
                        $CELLPADDING = (($aktion == "chatuserliste"
                            OR $rows > 15) ? 0 : 3);
                        user_liste($larr, $rows);
                    }
                    
                }
                
            } else {
                
                // Suchformular ausgeben
                $box = $ft0 . $t['sonst17'] . $ft1;
                $text = "<FORM NAME=\"suche\" ACTION=\"user.php\" METHOD=POST>\n"
                    . "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0>"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"suche\">\n";
                if ($admin) {
                    $text .= "<TR><TD colspan=2>" . $f1 . $t['sonst25'] . $f2
                        . "</TD></TR>";
                } else {
                    $text .= "<TR><TD colspan=2>" . $f1 . $t['sonst7'] . $f2
                        . "</TD></TR>";
                }
                
                $text .= "<TR><TD colspan=2>" . $f1
                    . "&nbsp;<INPUT TYPE=\"TEXT\" NAME=\"suchtext\" VALUE=\"$suchtext\" SIZE=\"$eingabe_breite\">"
                    . $f2 . "</TD></TR>\n";
                
                if ($admin) {
                    
                    if (!isset($f)) {
                        $f['ip'] = "";
                        $f['level'] = "";
                    }
                    
                    // Suchformular nach IP-Adressen
                    $text .= "<TR><TD colspan=2><BR>" . $f1 . $t['sonst30']
                        . $f2 . "</TD></TR>" . "<TR><TD ALIGN=RIGHT>" . $f1
                        . $t['sonst31'] . $f2 . "</TD><TD>" . $f1
                        . "&nbsp;<INPUT TYPE=\"TEXT\" NAME=\"f[ip]\" VALUE=\"$f[ip]\" SIZE=\"6\">"
                        . $f2 . "</TD></TR>\n";
                    
                    // Liste der Gruppen ausgeben
                    $text .= "<TR><TD ALIGN=RIGHT>" . $f1 . $t['sonst21']
                        . "</TD>\n" . "<TD>&nbsp;<SELECT NAME=\"f[level]\">\n"
                        . "<OPTION VALUE=\"\">$t[sonst22]\n";
                    
                    reset($level);
                    while (list($levelname, $levelbezeichnung) = each($level)) {
                        if ($levelname != "B") {
                            if ($f['level'] == $levelname) {
                                $text .= "<OPTION SELECTED VALUE=\"$levelname\">$levelbezeichnung\n";
                            } else {
                                $text .= "<OPTION VALUE=\"$levelname\">$levelbezeichnung\n";
                            }
                        }
                    }
                    
                    $text .= "</SELECT>" . $f2 . "</TD></TR>\n";
                    
                }
                
                if (!isset($f['user_neu']))
                    $f['user_neu'] = "";
                if (!isset($f['user_login']))
                    $f['user_login'] = "";
                if (!isset($f['u_chathomepage']))
                    $f['u_chathomepage'] = "";
                
                // Suche nach Neu & erstem login
                $text .= "<TR><TD ALIGN=RIGHT>" . $f1 . $t['sonst32'] . $f2
                    . "</TD><TD>" . $f1
                    . "&nbsp;<INPUT TYPE=\"TEXT\" NAME=\"f[user_neu]\" VALUE=\"$f[user_neu]\" SIZE=\"6\">&nbsp;"
                    . $t['sonst34'] . $f2 . "</TD></TR>\n"
                    . "<TR><TD ALIGN=RIGHT>" . $f1 . $t['sonst33'] . $f2
                    . "</TD><TD>" . $f1
                    . "&nbsp;<INPUT TYPE=\"TEXT\" NAME=\"f[user_login]\" VALUE=\"$f[user_login]\" SIZE=\"6\">&nbsp;"
                    . $t['sonst35'] . $f2 . "</TD></TR>\n";
                
                // Suche nach User mit Homepage
                $text .= "<TR><TD ALIGN=RIGHT>" . $f1 . $t['sonst38']
                    . "</TD>\n"
                    . "<TD>&nbsp;<SELECT NAME=\"f[u_chathomepage]\">\n";
                if ($f['u_chathomepage'] == "J") {
                    $text .= "<OPTION VALUE=\"N\">$t[sonst22]\n"
                        . "<OPTION SELECTED VALUE=\"J\">$t[sonst39]\n";
                } else {
                    $text .= "<OPTION SELECTED VALUE=\"N\">$t[sonst22]\n"
                        . "<OPTION VALUE=\"J\">$t[sonst39]\n";
                }
                $text .= "</SELECT>" . $f2 . "</TD></TR>\n";
                
                $text .= "<TR><TD colspan=2 ALIGN=RIGHT>" . $f1
                    . "<INPUT TYPE=\"SUBMIT\" NAME=\"suchtext_eingabe\" VALUE=\"Go!\">"
                    . $f2 . "</TD></TR>" . "</TABLE></FORM>\n";
                show_box2($box, $text, "100%");
            }
            
            break;
        
        case "adminliste":
            if ($adminlisteabrufbar && $u_level != "G") {
                
                $result = mysqli_query($mysqli_link, 'select * from user where u_level = "S" or u_level = "C" order by u_nick ');
                $anzahl = mysqli_num_rows($result);
                
                for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
                    
                    // Array mit Userdaten und Infotexten aufbauen
                    $larr[$i][u_email] = str_replace("\\", "",
                        htmlspecialchars($row[u_email]));
                    $larr[$i][u_nick] = strtr(
                        str_replace("\\", "", htmlspecialchars($row[u_nick])),
                        "I", "i");
                    $larr[$i][u_level] = $row[u_level];
                    $larr[$i][u_id] = $row[u_id];
                    $larr[$i][u_away] = $row[u_away];
                    if ($communityfeatures) {
                        // Wenn der User nicht möchte, daß sein Würfel angezeigt wird, ist hier die einfachste Möglichkeit
                        if ($row[u_punkte_anzeigen] != "N") {
                            $larr[$i][gruppe] = hexdec($row[u_punkte_gruppe]);
                        } else {
                            $larr[$i][gruppe] = 0;
                        }
                        $larr[$i][u_chathomepage] = $row[u_chathomepage];
                    }
                    
                    flush();
                }
                mysqli_free_result($result);
                
                $rows = count($larr);
                $CELLPADDING = (($aktion == "chatuserliste" OR $rows > 15) ? 0
                    : 3);
                user_liste($larr, $rows);
            }
            
            break;
        
        case "zeig":
            if (!isset($zeigeip))
                $zeigeip = 0;
            // User mit ID $user anzeigen
            if ($user) {
                // User listen
                user_zeige($user, $admin, $schau_raum, $u_level, $zeigeip);
            } else echo "<P>$t[sonst11]</P>\n";
            
            break;
        
        case "statistik":
            include "statistik.php";
            break;
        
        default;
        // Beichtstuhlmodus (siehe config.php)
        // ID der Lobby merken
            if (isset($beichtstuhl) && $beichtstuhl && !$admin) {
                
                // Id der Lobby als Voreinstellung ermitteln
                $query = "SELECT r_id FROM raum WHERE r_name LIKE '" . mysqli_real_escape_string($mysqli_link, $lobby) . "' ";
                $result = mysqli_query($mysqli_link, $query);
                $rows = mysqli_num_rows($result);
                
                if ($rows > 0) {
                	$lobby_id = mysqli_result($result, 0, "r_id");
                }
                @mysqli_free_result($result);
            }
            
            // Raum listen
            $query = "SELECT raum.*,o_user,o_name,o_ip,o_userdata,o_userdata2,o_userdata3,o_userdata4,r_besitzer=o_user AS isowner "
                . "FROM online left join raum on o_raum=r_id "
                . "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout $raum_subquery "
                . "ORDER BY o_name";
            
            $result = mysqli_query($mysqli_link, $query);
            
            for ($i = 0; $row = mysqli_fetch_array($result, MYSQLI_ASSOC); $i++) {
                
                // Array mit Userdaten und Infotexten aufbauen
                $userdata = unserialize(
                    $row['o_userdata'] . $row['o_userdata2']
                        . $row['o_userdata3'] . $row['o_userdata4']);
                
                if ((!isset($beichtstuhl) || !$beichtstuhl) || $admin
                    || $userdata['u_id'] == $u_id || $lobby_id == $schau_raum
                    || $userdata['u_level'] == "S"
                    || $userdata['u_level'] == "C") {
                    // Variable aus o_userdata setzen
                    $larr[$i]['u_email'] = str_replace("\\", "",
                        htmlspecialchars($userdata['u_email']));
                    $larr[$i]['u_nick'] = strtr(
                        str_replace("\\", "",
                            htmlspecialchars($userdata['u_nick'])), "I", "i");
                    $larr[$i]['u_level'] = $userdata['u_level'];
                    $larr[$i]['u_id'] = $userdata['u_id'];
                    $larr[$i]['u_away'] = $userdata['u_away'];
                    $larr[$i]['r_besitzer'] = $row['r_besitzer'];
                    $larr[$i]['r_topic'] = $row['r_topic'];
                    $larr[$i]['o_ip'] = $row['o_ip'];
                    $larr[$i]['isowner'] = $row['isowner'];
                    if ($communityfeatures) {
                        if ($userdata['u_punkte_anzeigen'] != "N") {
                            $larr[$i]['gruppe'] = hexdec(
                                $userdata['u_punkte_gruppe']);
                        } else {
                            $larr[$i]['gruppe'] = 0;
                        }
                        $larr[$i]['u_chathomepage'] = $userdata['u_chathomepage'];
                    }
                    if (!$row['r_name'] || $row['r_name'] == "NULL") {
                        $larr[$i]['r_name'] = "["
                            . $whotext[($schau_raum * (-1))] . "]";
                    } else {
                        $larr[$i]['r_name'] = $t['sonst42'] . $row['r_name'];
                    }
                    
                } else {
                    // Anonyme User
                    $larr[$i]['u_nick'] = $t['sonst37'];
                    $larr[$i]['u_level'] = $userdata['u_level'];
                    $larr[$i]['u_away'] = $userdata['u_away'];
                    $larr[$i]['r_besitzer'] = $row['r_besitzer'];
                    $larr[$i]['r_topic'] = $row['r_topic'];
                    $larr[$i]['o_ip'] = $row['o_ip'];
                    if (!$row['r_name'] || $row['r_name'] == "NULL") {
                        $larr[$i]['r_name'] = "["
                            . $whotext[($schau_raum * (-1))] . "]";
                    } else {
                        $larr[$i]['r_name'] = $t['sonst42'] . $row['r_name'];
                    }
                    
                }
                // Spezialbehandlung für Admins
                if (!$admin) {
                    $larr[$i]['o_ip'] = "";
                }
                
                // Raumbesitzer einstellen, falls Level=User
                if ($larr[$i]['isowner'] && $userdata['u_level'] == "U")
                    $larr[$i]['u_level'] = "B";
                
            }
            @mysqli_free_result($result);
            
            if (isset($larr)) {
                $rows = count($larr);
            } else {
                $rows = 0;
            }
            
            $CELLPADDING = ($aktion == "chatuserliste" OR $rows > 15 ? 0 : 3);
            
            // Fehlerbehandlung, falls Arry leer ist -> nichts gefunden
            if (!$rows && $schau_raum > 0) {
                
                $query = "SELECT r_name FROM raum WHERE r_id=" . intval($schau_raum);
                $result = mysqli_query($mysqli_link, $query);
                if ($result && mysqli_num_rows($result) != 0)
                	$r_name = mysqli_result($result, 0, 0);
                echo "<P>" . str_replace("%r_name%", $r_name, $t['sonst13'])
                    . "</P>\n";
                
            } elseif (!$rows && $schau_raum < 0) {
                
                echo "<P>"
                    . str_replace("%whotext%", $whotext[($schau_raum * (-1))],
                        $t['sonst43']) . "</P>\n";
                
            } else { // array ist gefüllt -> Daten ausgeben
            
                if ($aktion != "chatuserliste") {
                    echo "<TABLE CELLPADDING=\"2\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\" BGCOLOR=\"$farbe_tabelle_kopf\">\n"
                        . "<TR><TD><A HREF=\"#\" onClick=\"window.close();\">"
                        . "<IMG SRC=\"pics/button-x.gif\" ALT=\"schließen\" WIDTH=\"15\" HEIGHT=\"13\" ALIGN=\"RIGHT\" BORDER=\"0\"></A>"
                        . "<FONT SIZE=\"-1\" COLOR=\"$farbe_text\"><B>$box</B></FONT>"
                        . "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=1 HEIGHT=13><BR>\n"
                        . "<TABLE CELLPADDING=\"5\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\" BGCOLOR=\"$farbe_tabelle_koerper\">"
                        . "<TR><TD><B>" . $larr[0]['r_name'] . "</B><BR>"
                        . ($larr[0]['r_topic'] ? $f1 . "Topic: "
                                . $larr[0]['r_topic'] . $f2 . "<BR>" : "")
                        . "<BR>";
                } else {
                    $linkuser = "href=\"user.php?http_host=$http_host&id=$id&aktion=chatuserliste\"";
                    echo "<CENTER>" . $f3 . "[<a onMouseOver=\"return(true)\" "
                        . $linkuser . ">" . $t['sonst19'] . "</A>]";
                    if ((!isset($beichtstuhl) || !$beichtstuhl)
                        && $smilies_datei != ""
                        && (!isset($smilies_aus) || !$smilies_aus)) {
                        $linksmilies = "href=\"" . $smilies_datei
                            . "?http_host=$http_host&id=$id\"";
                        echo "&nbsp;[<a onMouseOver=\"return(true)\" $linksmilies>"
                            . $t['sonst20'] . "</A>]";
                    }
                    echo $f4 . "<BR>\n" . $f1 . $larr[0]['r_name'] . $f2
                        . "<BR>\n";
                }
                
                // Userliste ausgeben
                if ($aktion != "chatuserliste") {
                    user_liste($larr, $rows);
                } else {
                    user_liste($larr, 0);
                }
                
                if ($aktion != "chatuserliste")
                    echo "<P ALIGN=\"CENTER\">" . $f1 . $t['sonst12'] . $f2
                        . "</P>\n";
                echo "</TD></TR></TABLE></TD></TR></TABLE>\n";
                
                if ($aktion == "chatuserliste") {
                    if ($rows > 15) {
                        echo "$f3<B>[<a onMouseOver=\"return(true)\" $linkuser>"
                            . $t['sonst19'] . "</A>]";
                        if ($smilies_datei != "") {
                            echo "&nbsp;[<a onMouseOver=\"return(true)\" $linksmilies>"
                                . $t['sonst20'] . "</A>]";
                        }
                        echo "</B>" . $f4 . "</CENTER>\n";
                    }
                }
                
            }
            
            // Raumauswahl
            
            if ($aktion != "chatuserliste") {
                // Kopf Tabelle
                echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
                
                $box = $ft0 . $t['sonst14'] . $ft1;
                echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
                echo "<TR><TD>";
                echo "<A HREF=\"/\" onClick=\"window.close(); return(false);\">"
                    . "<IMG SRC=\"pics/button-x.gif\" ALT=\"schließen\" "
                    . "WIDTH=15 HEIGHT=13 ALIGN=\"RIGHT\" BORDER=0></A>\n";
                echo "<FONT SIZE=-1 COLOR=$farbe_text><B>$box</B></FONT>\n";
                echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=1 HEIGHT=13><BR>\n";
                echo "<TABLE CELLPADDING=5 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=\"$farbe_tabelle_koerper\">\n";
                echo "<TR><TD>";
                
                echo "<FORM NAME=\"raum\" ACTION=\"user.php\" METHOD=POST>\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
                    . $f1
                    . "<SELECT NAME=\"schau_raum\" onChange=\"document.raum.submit()\">\n";
                
                if ($admin) {
                    raeume_auswahl($schau_raum, TRUE, FALSE, FALSE);
                } else {
                    raeume_auswahl($schau_raum, FALSE, FALSE, FALSE);
                }
                
                echo "</SELECT>"
                    . "<INPUT TYPE=\"SUBMIT\" NAME=\"eingabe\" VALUE=\"Go!\">"
                    . $f2 . "</FORM>\n";
                
                if ($aktion != "chatuserliste") {
                    // Fuß der Tabelle
                    echo "</TD></TR></TABLE></TD></TR></TABLE>\n";
                }
            }
        
    }
    
} else {
    echo "<P ALIGN=CENTER>$t[sonst15]</P>\n";
}

// Fuß
if ($aktion != "zeigalle" || $u_level != "S") {
    if ($aktion != "chatuserliste" && $o_js) {
        echo $f1
            . "<P ALIGN=CENTER>[<A HREF=\"javascript:window.close();\">$t[sonst1]</A>]</P>"
            . $f2 . "\n";
    }
    echo "</BODY></HTML>\n";
}
