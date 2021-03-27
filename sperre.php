<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

function liste()
{
    
    global $farbe_tabelle_zeile1;
    global $farbe_tabelle_zeile2;
    global $farbe_text;
    global $farbe_tabelle_kopf;
    global $farbe_tabelle_kopf1;
    global $farbe_tabelle_kopf2;
    global $farbe_tabelle_koerper;
    global $dbase, $t;
    global $f1, $f2, $f3, $f4, $ft0, $ft1;
    global $id, $http_host;
    global $raumsperre;
    global $sperre_dialup;
    global $conn;
    
    $sperr = "";
    for ($i = 0; $i < count($sperre_dialup); $i++) {
        if ($sperr != "")
            $sperr .= " OR ";
        $sperr .= "is_domain like '%" . mysql_real_escape_string($sperre_dialup[$i]) . "%' ";
    }
    
    if (count($sperre_dialup) >= 1) {
        $query = "DELETE FROM ip_sperre WHERE ($sperr) AND to_days(now())-to_days(is_zeit) > 3 ";
        mysql_query($query);
    }
    
    if ($raumsperre) {
        $query = "delete from sperre where to_days(now())-to_days(s_zeit) > $raumsperre";
        mysql_query($query);
    }
    
    // Alle Sperren ausgeben
    $query = "SELECT u_nick,is_infotext,is_id,is_domain,UNIX_TIMESTAMP(is_zeit) as zeit,is_ip_byte,is_warn,"
        . "SUBSTRING_INDEX(is_ip,'.',is_ip_byte) as isip "
        . "FROM ip_sperre,user WHERE is_owner=u_id "
        . "ORDER BY is_zeit DESC,is_domain,is_ip";
    $result = mysql_query($query, $conn);
    $rows = mysqli_num_rows($result);
    
    if ($rows > 0) {
        $i = 0;
        $bgcolor = $farbe_tabelle_zeile1;
        
        // Kopf Tabelle
        $box = $ft0 . $t['sonst2'] . $ft1;
        echo "<TABLE CELLPADDING=4 CELLSPACING=0 BORDER=0 WIDTH=100%>\n";
        echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\">$t[sonst3]</TR>\n";
        
        while ($i < $rows) {
            // Ausgabe in Tabelle
            $row = mysqli_fetch_object($result);
            
            if (strlen($row->isip) > 0) {
                if ($row->is_ip_byte == 1) {
                    $isip = $row->isip . ".xxx.yyy.zzz";
                } elseif ($row->is_ip_byte == 2) {
                    $isip = $row->isip . ".yyy.zzz";
                } elseif ($row->is_ip_byte == 3) {
                    $isip = $row->isip . ".zzz";
                } else {
                    $isip = $row->isip;
                }
                
                flush();
                
                if ($row->is_ip_byte == 4) {
                    $ip_name = @gethostbyaddr($row->isip);
                    if ($ip_name == $row->isip) {
                        unset($ip_name);
                        echo "<TR VALIGN=TOP BGCOLOR=\"$bgcolor\"><TD><B>"
                            . $f1 . $row->isip . $f2 . "</B></TD>\n";
                    } else {
                        echo "<TR VALIGN=TOP BGCOLOR=\"$bgcolor\"><TD><B>"
                            . $f1 . $row->isip . "<br>(" . $ip_name . $f2
                            . ")</B></TD>\n";
                    }
                } else {
                    echo "<TR VALIGN=TOP BGCOLOR=\"$bgcolor\"><TD><B>" . $f1
                        . $isip . $f2 . "</B></TD>\n";
                }
            } else {
                flush();
                $ip_name = gethostbyname($row->is_domain);
                if ($ip_name == $row->is_domain) {
                    unset($ip_name);
                    echo "<TR VALIGN=TOP BGCOLOR=\"$bgcolor\"><TD><B>" . $f1
                        . $row->is_domain . $f2 . "</B></TD>\n";
                } else {
                    echo "<TR VALIGN=TOP BGCOLOR=\"$bgcolor\"><TD><B>" . $f1
                        . $row->is_domain . "<br>(" . $ip_name
                        . $f2 . ")</B></TD>\n";
                }
            }
            
            // Infotext
            echo "<TD>" . $f1 . $row->is_infotext . "&nbsp;"
                . $f2 . "</TD>\n";
            
            // Nur Warnung ja/nein
            if ($row->is_warn == "ja") {
                echo "<TD valign=\"middle\">" . $f3 . $t['sonst20'] . $f4
                    . "</TD>\n";
            } else {
                echo "<TD valign=\"middle\">" . $f3 . $t['sonst21'] . $f4
                    . "</TD>\n";
            }
            
            // User und Datum
            echo "<TD><small>$row->u_nick<br>\n";
            echo date("d.m.y", $row->zeit) . "&nbsp;";
            echo date("H:i:s", $row->zeit) . "&nbsp;";
            echo "</small></TD>\n";
            
            if ($row->is_domain == "-GLOBAL-") {
                echo "<TD>" . $f1 . "<B>\n";
                echo "<A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=loginsperre0\">"
                    . "[DEAKTIVIEREN]" . "</A>\n";
                echo "</B>" . $f2 . "</TD>\n";
            } elseif ($row->is_domain == "-GAST-") {
                echo "<TD>" . $f1 . "<B>\n";
                echo "<A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=loginsperregast0\">"
                    . "[DEAKTIVIEREN]" . "</A>\n";
                echo "</B>" . $f2 . "</TD>\n";
            } else {
                echo "<TD>" . $f1
                    . "<B>[<A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=aendern&is_id=$row->is_id\">ÄNDERN</A>]\n"
                    . "[<A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=loeschen&is_id=$row->is_id\">LÖSCHEN</A>]\n";
                if (isset($ip_name) && (strlen($ip_name) > 0)) {
                    echo "<br>[<A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=trace&is_id=$row->is_id\">TRACEROUTE</A>]\n";
                }
                echo "</B>" . $f2 . "</TD>";
            }
            echo "</TR>\n";
            
            // Farben umschalten
            if (($i % 2) > 0) {
                $bgcolor = $farbe_tabelle_zeile1;
            } else {
                $bgcolor = $farbe_tabelle_zeile2;
            }
            
            $i++;
            
        }
        
        echo "</TABLE>\n";
        
    } else {
        echo "<P ALIGN=CENTER>$t[sonst4]</P>\n";
    }
    mysqli_free_result($result);
    
}
?>
<HTML>
<HEAD><TITLE><?php echo $body_titel . "_Info"; ?></TITLE><META CHARSET=UTF-8>
<SCRIPT>
        window.focus()     
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

if (strlen($u_id) > 0 && $admin) {
    switch ($aktion) {
        
        case "loginsperre0":
            $query2 = "DELETE FROM ip_sperre WHERE is_domain = '-GLOBAL-' ";
            $result2 = mysql_query($query2, $conn);
            
            unset($aktion);
            break;
        
        case "loginsperregast0":
            $query2 = "DELETE FROM ip_sperre WHERE is_domain = '-GAST-' ";
            $result2 = mysql_query($query2, $conn);
            
            unset($aktion);
            break;
        
        case "loginsperre1":
            unset($f);
            $f['is_infotext'] = "";
            $f['is_domain'] = "-GLOBAL-";
            $f['is_owner'] = $u_id;
            schreibe_db("ip_sperre", $f, 0, "is_id");
            
            unset($aktion);
            break;
        
        case "loginsperregast1":
            unset($f);
            $f['is_infotext'] = "";
            $f['is_domain'] = "-GAST-";
            $f['is_owner'] = $u_id;
            schreibe_db("ip_sperre", $f, 0, "is_id");
            
            unset($aktion);
            break;
        
        default;
    }
    
    // Menü als erstes ausgeben
    if (isset($uname)) {
        $zusatztxt = "'" . $uname . "'&nbsp;&gt;&gt;&nbsp;";
    } else {
        $zusatztxt = "";
        $uname = "";
    }
    
    $box = $ft0 . "$chat Menü" . $ft1;
    $text = "<A HREF=\"sperre.php?http_host=$http_host&id=$id\">$t[menue1]</A>\n"
        . "| <A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=neu\">$t[menue2]</A>\n";
    
    if ($communityfeatures) {
        $query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GLOBAL-'";
        $result = mysql_query($query, $conn);
        if ($result && mysqli_num_rows($result) > 0) {
            $text .= "| <A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=loginsperre0\">"
                . "Loginsperre: Deaktivieren" . "</A>\n";
        } else {
            $text .= "| <A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=loginsperre1\">"
                . "Loginsperre: Aktivieren" . "</A>\n";
        }
        @mysqli_free_result($result);
        
        $query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GAST-'";
        $result = mysql_query($query, $conn);
        if ($result && mysqli_num_rows($result) > 0) {
            $text .= "| <A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=loginsperregast0\">"
                . "Loginsperre Gast: Deaktivieren" . "</A>\n";
        } else {
            $text .= "| <A HREF=\"sperre.php?http_host=$http_host&id=$id&aktion=loginsperregast1\">"
                . "Loginsperre Gast: Aktivieren" . "</A>\n";
        }
        @mysqli_free_result($result);
        
        $text .= "| <A HREF=\"blacklist.php?http_host=$http_host&id=$id&neuer_blacklist[u_nick]=$uname\">"
            . $zusatztxt . $t['menue3'] . "</A>\n";
    }
    
    show_box2($box, $text, "100%");
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
    
    // Soll Datensatz eingetragen oder geändert werden?
    if ((isset($eintragen)) && ($eintragen == $t['sonst13'])) {
        if (!isset($f['is_infotext']))
            $f['is_infotext'] = "";
        if (!isset($f['is_domain']))
            $f['is_domain'] = "";
        
        $f['is_infotext'] = htmlspecialchars($f['is_infotext']);
        $f['is_domain'] = htmlspecialchars($f['is_domain']);
        
        // IP zusamensetzen
        $f['is_ip'] = $ip1 . "." . $ip2 . "." . $ip3 . "." . $ip4;
        if (strlen($f['is_domain']) > 0 && $f['is_ip'] != "...") {
            echo "<P>$t[sonst5]</P>\n";
            $aktion = "neu";
        } elseif (strlen($f['is_domain']) > 0) {
            if (strlen($f['is_domain']) > 4) {
                // Eintrag Domain in DB
                unset($f['is_ip']);
                $f['is_owner'] = $u_id;
                schreibe_db("ip_sperre", $f, $f['is_id'], "is_id");
            } else {
                echo "<P>$t[sonst5]</P>\n";
                $aktion = "neu";
            }
        } elseif ($f['is_ip'] != "...") {
            if (strlen($ip1) > 0 && $ip1 < 256) {
                // Eintrag IP in DB
                if (strlen($ip4) > 0 && $ip4 < 256 && strlen($ip3) > 0
                    && $ip3 < 256 && strlen($ip2) > 0 && $ip2 < 256) {
                    $f['is_ip_byte'] = 4;
                } elseif (strlen($ip3) > 0 && $ip3 < 256 && strlen($ip2) > 0
                    && $ip2 < 256) {
                    $f['is_ip_byte'] = 3;
                    $f['is_ip'] = $ip1 . "." . $ip2 . "." . $ip3 . ".";
                } elseif (strlen($ip2) > 0 && $ip2 < 256) {
                    $f['is_ip_byte'] = 2;
                    $f['is_ip'] = $ip1 . "." . $ip2 . "..";
                } else {
                    $f['is_ip_byte'] = 1;
                    $f['is_ip'] = $ip1 . "...";
                }
                $f['is_owner'] = $u_id;
                if (!isset($f['is_id']))
                    $f['is_id'] = 0;
                schreibe_db("ip_sperre", $f, $f['is_id'], "is_id");
            } else {
                echo "<P>$t[sonst6]</P>\n";
                $aktion = "neu";
            }
        } else {
            echo "<P>$t[sonst7]</P>\n";
            $aktion = "neu";
        }
        
    }
    
    if (!isset($aktion))
        $aktion = "";
    // Auswahl
    switch ($aktion) {
        
        case "trace":
        // ID gesetzt?
            if (strlen($is_id) > 0) {
                $query = "SELECT is_infotext,is_domain,is_ip FROM ip_sperre "
                    . "WHERE is_id=" . intval($is_id);
                
                $result = mysql_query($query);
                $rows = mysqli_num_rows($result);
                
                if ($rows == 1) {
                    // Zeile lese
                    $row = mysqli_fetch_object($result);
                    
                    $box = $ft0 . $t['sonst17'] . " " . $row->is_domain
                        . $row->is_ip . $ft1;
                    echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
                    echo "<TR><TD>";
                    echo "<A HREF=\"javascript:window.close();\">"
                        . "<IMG SRC=\"pics/button-x.gif\" ALT=\"schließen\" "
                        . "WIDTH=15 HEIGHT=13 ALIGN=\"RIGHT\" BORDER=0></A>\n";
                    echo "<FONT SIZE=-1 COLOR=$farbe_text><B>$box</B></FONT>\n";
                    echo "</TD></TR></TABLE>\n<PRE>" . $f1;
                    
                    if (!file_exists($traceroute)) {
                        system_msg("", 0, $u_id, $system_farbe,
                            "<b>config anpassen \$traceroute</b>");
                    }
                    
                    if (strlen($row->is_domain) > 0) {
                        system("$traceroute $row->is_domain", $ergebnis);
                    } else {
                        system("$traceroute $row->is_ip", $ergebnis);
                    }
                    
                    echo $f2 . "</PRE>\n";
                    
                } else {
                    echo "<P>$t[sonst9]</P>\n";
                }
                mysqli_free_result($result);
            }
            
            echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
            liste();
            break;
        
        case "loeschen":
        // ID gesetzt?
            if (strlen($is_id) > 0) {
                $query = "SELECT is_infotext,is_domain,is_ip_byte,SUBSTRING_INDEX(is_ip,'.',is_ip_byte) as isip FROM ip_sperre "
                    . "WHERE is_id=" . intval($is_id);
                
                $result = mysql_query($query);
                $rows = mysqli_num_rows($result);
                
                if ($rows == 1) {
                    // Zeile lesen, IP zerlegen
                    $row = mysqli_fetch_object($result);
                    
                    $query2 = "DELETE FROM ip_sperre WHERE is_id=" . intval($is_id);
                    
                    $result2 = mysql_query($query2);
                    
                    if (strlen($row->is_domain) > 0) {
                        echo "<P>"
                            . str_replace("%row->is_domain%", $row->is_domain,
                                $t['sonst10']) . "</P>\n";
                    } else {
                        if ($row->is_ip_byte == 1) {
                            $isip = $row->isip . ".xxx.yyy.zzz";
                        } elseif ($row->is_ip_byte == 2) {
                            $isip = $row->isip . ".yyy.zzz";
                        } elseif ($row->is_ip_byte == 3) {
                            $isip = $row->isip . ".zzz";
                        } else {
                            $isip = $row->isip;
                        }
                        echo "<P>"
                            . str_replace("%row->is_domain%", $row->is_domain,
                                $t['sonst10']) . "</P>\n";
                    }
                } else {
                    echo "<P>$t[sonst9]</P>\n";
                }
                mysqli_free_result($result);
            }
            liste();
            break;
        
        case "aendern":
        // ID gesetzt?
            if (strlen($is_id) > 0) {
                $query = "SELECT is_infotext,is_domain,is_ip,is_ip_byte,is_warn FROM ip_sperre "
                    . "WHERE is_id=" . intval($is_id);
                
                $result = mysql_query($query);
                $rows = mysqli_num_rows($result);
                
                if ($rows == 1) {
                    // Zeile lesen, IP zerlegen
                    $row = mysqli_fetch_object($result);
                    $ip = explode(".", $row->is_ip);
                    
                    // Kopf Tabelle
                    echo "<FORM NAME=\"Sperre\" ACTION=\"sperre.php\" METHOD=POST>\n";
                    
                    $box = $ft0 . $t['sonst18'] . $ft1;
                    echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
                    echo "<TR><TD>";
                    echo "<A HREF=\"javascript:window.close();\">"
                        . "<IMG SRC=\"pics/button-x.gif\" ALT=\"schließen\" "
                        . "WIDTH=15 HEIGHT=13 ALIGN=\"RIGHT\" BORDER=0></A>\n";
                    echo "<FONT SIZE=-1 COLOR=$farbe_text><B>$box</B></FONT>\n";
                    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
                    echo "<TABLE CELLPADDING=5 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=\"$farbe_tabelle_koerper\">\n";
                    echo "<TR><TD>\n";
                    
                    echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=1>\n";
                    
                    echo "<TR><TD><B>$t[sonst19]</B></TD>";
                    
                    // Infotext
                    echo "<TD>" . $f1
                        . "<INPUT TYPE=\"TEXT\" NAME=\"f[is_infotext]\" "
                        . "VALUE=\"$row->is_infotext\" SIZE=24>" . $f2
                        . "</TD></TR>\n";
                    
                    // Domain/IP-Adresse
                    if (strlen($row->is_domain) > 0) {
                        echo "<TR><TD><B>$t[sonst11]</B></TD>";
                        echo "<TD>" . $f1
                            . "<INPUT TYPE=\"TEXT\" NAME=\"f[is_domain]\" "
                            . "VALUE=\"$row->is_domain\" SIZE=24>\n";
                    } else {
                        echo "<TR><TD><B>$t[sonst12]</B></TD>";
                        echo "<TD>" . $f1
                            . "<INPUT TYPE=\"TEXT\" NAME=\"ip1\" "
                            . "VALUE=\"$ip[0]\" SIZE=3 maxlength=3><B>.</B>"
                            . "<INPUT TYPE=\"TEXT\" NAME=\"ip2\" "
                            . "VALUE=\"$ip[1]\" SIZE=3 maxlength=3><B>.</B>"
                            . "<INPUT TYPE=\"TEXT\" NAME=\"ip3\" "
                            . "VALUE=\"$ip[2]\" SIZE=3 maxlength=3><B>.</B>"
                            . "<INPUT TYPE=\"TEXT\" NAME=\"ip4\" "
                            . "VALUE=\"$ip[3]\" SIZE=3 maxlength=3></TD></TR>\n";
                    }
                    
                    // Warnung ja/nein
                    echo "<TR><TD><B>$t[sonst22]</B></TD><TD>" . $f1
                        . "<SELECT NAME=\"f[is_warn]\">";
                    if ($row->is_warn == "ja") {
                        echo "<OPTION SELECTED VALUE=\"ja\">$t[sonst20]";
                        echo "<OPTION VALUE=\"nein\">$t[sonst21]";
                    } else {
                        echo "<OPTION VALUE=\"ja\">$t[sonst20]";
                        echo "<OPTION SELECTED VALUE=\"nein\">$t[sonst21]";
                    }
                    echo "</SELECT>";
                    
                    // Submitknopf
                    echo "&nbsp;<B><INPUT TYPE=\"SUBMIT\" VALUE=\"$t[sonst13]\" NAME=\"eintragen\"></B>\n"
                        . $f2 . "</TD></TR>\n";
                    echo "</TABLE>\n";
                    echo "</TD></TR></TABLE></TD></TR></TABLE>\n";
                    
                    echo "<INPUT TYPE=\"HIDDEN\" NAME=\"f[is_id]\" VALUE=\"$is_id\">\n"
                        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
                        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n";
                    echo "</FORM>\n";
                }
                mysqli_free_result($result);
            } else {
                echo "<P>$t[sonst9]</P>\n";
            }
            
            // Liste ausgeben
            echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
            liste();
            
            break;
        
        case "neu":
        // Werte von [SPERREN] aus Userliste übernehmen, falls vorhanden...
            if (isset($hname))
                $f['is_domain'] = $hname;
            elseif (isset($ipaddr))
                list($ip1, $ip2, $ip3, $ip4) = preg_split("/\./", $ipaddr, 4);
            if (isset($uname))
                $f['is_infotext'] = "Nick " . $uname;
            
            if (!isset($f['is_domain']))
                $f['is_domain'] = "";
            if (!isset($ip1))
                $ip1 = "";
            if (!isset($ip2))
                $ip2 = "";
            if (!isset($ip3))
                $ip3 = "";
            if (!isset($ip4))
                $ip4 = "";
            
            // Sperre neu anlegen, Eingabeformular
            echo "<FORM NAME=\"Sperre\" ACTION=\"sperre.php\" METHOD=POST>\n"
                . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
            
            $box = $ft0 . $t['sonst14'] . $ft1;
            echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
            echo "<TR><TD>";
            echo "<A HREF=\"javascript:window.close();\">"
                . "<IMG SRC=\"pics/button-x.gif\" ALT=\"schließen\" "
                . "WIDTH=15 HEIGHT=13 ALIGN=\"RIGHT\" BORDER=0></A>\n";
            echo "<FONT SIZE=-1 COLOR=$farbe_text><B>$box</B></FONT>\n";
            echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=1 HEIGHT=13><BR>\n";
            echo "<TABLE CELLPADDING=5 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=\"$farbe_tabelle_koerper\">\n";
            echo "<TR><TD>";
            
            echo "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n";
            echo $f1 . $t['sonst15'] . $f2 . "<BR>\n";
            echo "<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=1>\n";
            
            echo "<TR><TD><B>$t[sonst19]</B></TD>";
            echo "<TD>" . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[is_infotext]\" "
                . "VALUE=\"$f[is_infotext]\" SIZE=24>" . $f2 . "</TD></TR>\n";
            echo "<TR><TD><B>$t[sonst11]</B></TD>";
            echo "<TD>" . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[is_domain]\" "
                . "VALUE=\"$f[is_domain]\" SIZE=24>" . $f2 . "</TD></TR>\n";
            echo "<TR><TD><B>$t[sonst12]</B></TD>";
            echo "<TD>" . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"ip1\" "
                . "VALUE=\"$ip1\" SIZE=3 maxlength=3><B>.</B>"
                . "<INPUT TYPE=\"TEXT\" NAME=\"ip2\" "
                . "VALUE=\"$ip2\" SIZE=3 maxlength=3><B>.</B>"
                . "<INPUT TYPE=\"TEXT\" NAME=\"ip3\" "
                . "VALUE=\"$ip3\" SIZE=3 maxlength=3><B>.</B>"
                . "<INPUT TYPE=\"TEXT\" NAME=\"ip4\" "
                . "VALUE=\"$ip4\" SIZE=3 maxlength=3>" . $f2 . "</TD></TR>\n";
            
            echo "<TR><TD><B>$t[sonst22]</B></TD><TD>" . $f1
                . "<SELECT NAME=\"f[is_warn]\">";
            if (isset($f['is_warn']) && $f['is_warn'] == "ja") {
                echo "<OPTION SELECTED VALUE=\"ja\">$t[sonst20]";
                echo "<OPTION VALUE=\"nein\">$t[sonst21]";
            } else {
                echo "<OPTION VALUE=\"ja\">$t[sonst20]";
                echo "<OPTION SELECTED VALUE=\"nein\">$t[sonst21]";
            }
            echo "</SELECT>&nbsp;&nbsp;&nbsp;"
                . "<B><INPUT TYPE=\"SUBMIT\" VALUE=\"$t[sonst13]\" NAME=\"eintragen\"></B>\n"
                . $f2 . "</TD></TR>\n";
            echo "</TABLE>\n";
            echo "</TD></TR></TABLE></TD></TR></TABLE>\n";
            
            echo "</FORM>\n";
            
            // Liste ausgeben
            liste();
            
            break;
        
        default;
        // Liste ausgeben
            liste();
        
    }
    
} else {
    echo "<P ALIGN=CENTER>$t[sonst16]</P>\n";
}

if ($o_js) {
    echo $f1
        . "<P ALIGN=CENTER>[<A HREF=\"javascript:window.close();\">$t[sonst1]</A>]</P>"
        . $f2 . "\n";
}

?>

</BODY></HTML>
