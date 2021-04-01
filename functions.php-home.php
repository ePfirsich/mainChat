<?php

function edit_home(
    $u_id,
    $u_nick,
    $home,
    $einstellungen,
    $farben,
    $bilder,
    $aktion)
{
    // Editor für die eigene Homepage im Chat
    // u_id = User-Id
    // home = Array des Userprofiles mit Einstellungen
    // einstellungen = Array der Einstellungen
    // farben = Array mit Userfarben
    // bilder = Array mit Bildinfos
    
    global $farbe_tabelle_zeile2, $farbe_tabelle_zeile1, $f1, $f2;
    
    // HP-Tabelle ausgeben
    
    echo "<TABLE BORDER=\"0\" HEIGHT=\"100%\" WIDTH=\"100%\" BGCOLOR=\"$farbe_tabelle_zeile2\" CELLPADDING=5 CELLSPACING=0>"
        . "<TR><TD COLSPAN=\"3\" BGCOLOR=\"$farbe_tabelle_zeile1\">$f1<b>Meine Homepage:</b>"
        . $f2 . "</TD></TR>\n" . "<TR><TD VALIGN=\"TOP\" width=\"60%\">"
        . home_info($u_id, $u_nick, $farben, $aktion)
        . "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n"
        . home_profil($u_id, $u_nick, $home, $farben, $aktion)
        . "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n"
        . home_text($u_id, $u_nick, $home, "ui_text", $farben, $aktion)
        . "</TD>\n<TD><IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=1 HEIGHT=1><br></TD><TD VALIGN=\"TOP\">"
        . home_bild($u_id, $u_nick, $home, "ui_bild1", $farben, $aktion,
            $bilder)
        . "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n"
        . home_bild($u_id, $u_nick, $home, "ui_bild2", $farben, $aktion,
            $bilder)
        . "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n"
        . home_bild($u_id, $u_nick, $home, "ui_bild3", $farben, $aktion,
            $bilder)
        . "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n"
        . home_aktionen($u_id, $u_nick, $home, $farben, $aktion)
        . "</TD></TR>\n"
        . "</TABLE><IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>"
        . "<TABLE BORDER=\"0\" WIDTH=\"100%\"  BGCOLOR=\"$farbe_tabelle_zeile2\" CELLPADDING=5 CELLSPACING=0>"
        . "<TR><TD BGCOLOR=\"$farbe_tabelle_zeile1\" COLSPAN=\"2\">$f1<b>Homepage&nbsp;Einstellungen:</b>$f2</TD></TR>\n"
        . "<TR><TD VALIGN=\"TOP\">\n"
        . home_einstellungen($u_id, $u_nick, $home, $einstellungen)
        . "</TD><TD VALIGN=\"TOP\">"
        . home_hintergrund($u_id, $u_nick, $farben, $home, $bilder)
        . "</TD></TR></TABLE>\n"
        . "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n";
    
}

function home_profil($u_id, $u_nick, $home, $farben, $aktion)
{
    // Zeigt die Userinfos im Profil an
    
    global $dbase, $http_host, $id, $f1, $f2, $f3, $f4, $vor_einstellungen;
    
    $url = "profil.php?http_host=$http_host&id=$id&aktion=aendern";
    $link = $f3 . "<b>[<A HREF=\"$url\">ÄNDERN</A>]</b>" . $f4;
    $text = "";
    
    if ($home['ui_userid']) {
        
        // Profil vorhanden
        
        // Voreinstellungen fürs Profil
        if (!$home['ui_einstellungen']) {
            $ui_einstellungen = $vor_einstellungen;
        } else {
            $ui_einstellungen = unserialize($home['ui_einstellungen']);
        }
        
        // Profil ausgeben
        if ($ui_einstellungen["Straße"] && $home['ui_strasse'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\" WIDTH=20%>" . $f1
                . "Straße:" . $f2 . "</TD><TD colspan=3 WIDTH=80%>"
                . htmlspecialchars($home['ui_strasse'])
                . "</TD></TR>\n";
        
        if (($ui_einstellungen["Ort"] || $ui_einstellungen["PLZ"])
            && ($home['ui_plz'] || $home['ui_ort'])) {
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1 . "Ort:"
                . $f2 . "</TD><TD colspan=3>";
            if ($ui_einstellungen["PLZ"])
                $text .= htmlspecialchars($home['ui_plz'])
                    . "&nbsp;&nbsp;";
            if ($ui_einstellungen["Ort"])
                $text .= htmlspecialchars($home['ui_ort']);
            $text .= "</TD></TR>\n";
        }
        
        if ($ui_einstellungen["Land"] && $home['ui_land'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1 . "Land:"
                . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_land'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Tel"] && $home['ui_tel'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1
                . "Telefon:" . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_tel'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Handy"] && $home['ui_handy'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1 . "Handy:"
                . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_handy'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Fax"] && $home['ui_fax'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1
                . "Telefax" . $f2 . ":</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_fax'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["ICQ"] && $home['ui_icq'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1 . "ICQ:"
                . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_icq'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Geburtsdatum"] && $home['ui_geburt'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1
                . "Geburtsdatum:" . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_geburt'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Geschlecht"] && $home['ui_geschlecht'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1
                . "Geschlecht:" . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_geschlecht'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Beziehung"] && $home['ui_beziehung'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1
                . "Beziehung:" . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_beziehung'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Typ"] && $home['ui_typ'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1 . "Typ:"
                . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_typ'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Beruf"] && $home['ui_beruf'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1 . "Beruf:"
                . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_beruf'])
                . "</TD></TR>\n";
        
        if ($ui_einstellungen["Hobbies"] && $home['ui_hobby'])
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1
                . "Hobbies:" . $f2 . "</TD><TD colspan=3>"
                . htmlspecialchars($home['ui_hobby'])
                . "</TD></TR>\n";
        
    } else {
        
        if ($aktion == "aendern") {
            $text .= "<TR><TD COLSPAN=3>Sie haben noch kein Profil erstellt</TD>"
                . "<TD ALIGN=\"RIGHT\">$link</TD></TR>";
        }
        
    }
    
    // Farbwähler und Profil-Link ausgeben
    if ($aktion == "aendern") {
        $text .= "<TR><TD colspan=4 align=\"right\">" . $link . "<br>\n"
            . home_farbe($u_id, $u_nick, $home, "profil", $farben['profil'])
            . "</TD></TR>\n";
    } else {
        $text .= "<TR><TD colspan=4>&nbsp;</TD></TR>\n";
    }
    
    if (is_array($farben) && strlen($farben['profil']) > 7) {
        $bg = "BACKGROUND=\"home_bild.php?http_host=$http_host&u_id=$u_id&feld="
            . $farben['profil'] . "\"";
    } elseif (is_array($farben) && strlen($farben['profil']) == 7) {
        $bg = "BGCOLOR=\"$farben[profil]\"";
    } else {
        $bg = "";
    }
    
    return ("<TABLE $bg CELLPADDING=\"2\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\" >$text</TABLE>");
}

function home_info($u_id, $u_nick, $farben, $aktion)
{
    // Zeigt die öffentlichen Userdaten an
    global $dbase, $mysqli_link, $http_host, $id, $f1, $f2, $f3, $f4, $userdata, $t, $level, $id;
    
    // Fenstername
    $fenster = str_replace("+", "", $u_nick);
    $fenster = str_replace("-", "", $fenster);
    $fenster = str_replace("ä", "", $fenster);
    $fenster = str_replace("ö", "", $fenster);
    $fenster = str_replace("ü", "", $fenster);
    $fenster = str_replace("Ä", "", $fenster);
    $fenster = str_replace("Ö", "", $fenster);
    $fenster = str_replace("Ü", "", $fenster);
    $fenster = str_replace("ß", "", $fenster);
    
    // Userdaten lesen
    $query = "SELECT user.*,o_id, "
        . "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online, "
        . "date_format(u_login,'%d.%m.%y %H:%i') as login "
        . "FROM user left join online on o_user=u_id " . "WHERE u_id=$u_id";
    $result = mysqli_query($mysqli_link, $query);
    if ($result && mysqli_num_rows($result) == 1) {
        $userdata = mysqli_fetch_array($result);
        $online_zeit = $userdata['online'];
        $letzter_login = $userdata['login'];
        
        mysqli_free_result($result);
        
        // Link auf Usereditor ausgeben
        if ($aktion == "aendern") {
            $url = "edit.php?http_host=$http_host&id=$id";
            $userdaten_bearbeiten = $f3
                . "<b>[<A HREF=\"$url\" TARGET=\"$fenster\" onclick=\"window.open('$url','$fenster','resizable=yes,scrollbars=yes,width=300,height=580'); return(false);\">ÄNDERN</A>]</b>"
                . $f4;
        } else {
            $userdaten_bearbeiten = "&nbsp;";
        }
        
        if (!$id) {
            $links_an = FALSE;
        } else {
            $links_an = TRUE;
        }
        
        $text = "<TR><TD valign=\"TOP\" align=\"right\" WIDTH=20%>" . $f1
            . "Nickname:" . $f2 . "</TD><TD colspan=3 WIDTH=\"80%\"><b>"
            . user($userdata['u_id'], $userdata, $links_an, FALSE)
            . "</b></TD></TR>\n";
        
        // Onlinezeit oder letzter Login		
        if ($userdata['o_id'] != "NULL" && $userdata['o_id']) {
            $text .= "<TR><TD>&nbsp;</TD><TD colspan=3 valign=\"TOP\"><b>"
                . $f1
                . str_replace("%online%", gmdate("H:i:s", $online_zeit),
                    $t['chat_msg92']) . $f2 . "</b></TD></TR>\n";
        } else {
            $text .= "<TR><TD>&nbsp;</TD><TD colspan=3 valign=\"TOP\"><b>"
                . $f1 . str_replace("%login%", $letzter_login, $t['chat_msg94'])
                . $f2 . "</b></TD></TR>\n";
        }
        
        // Level
        $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1
            . $t['user_zeige8'] . ":" . $f2 . "</TD>" . "<TD colspan=3><b>"
            . $f1 . $level[$userdata['u_level']] . $f2 . "</b></TD></TR>\n";
        
        // Punkte
        if ($userdata['u_punkte_gesamt']) {
            if ($userdata['u_punkte_datum_monat'] != date("n", time())) {
                $userdata['u_punkte_monat'] = 0;
            }
            if ($userdata['u_punkte_datum_jahr'] != date("Y", time())) {
                $userdata['u_punkte_jahr'] = 0;
            }
            $text .= "<TR><TD valign=\"TOP\" align=\"right\">" . $f1
                . $t['user_zeige38'] . ":" . $f2 . "</TD>"
                . "<TD colspan=3><b>" . $f1 . $userdata['u_punkte_gesamt']
                . "/" . $userdata['u_punkte_jahr'] . "/"
                . $userdata['u_punkte_monat'] . "&nbsp;"
                . str_replace("%jahr%", strftime("%Y", time()),
                    str_replace("%monat%", strftime("%B", time()),
                        $t['user_zeige39'])) . $f2 . "</b></TD></TR>\n";
        }
        
        // Farbwähler & Link auf Editor ausgeben
        if ($aktion == "aendern") {
            if (!isset($home))
                $home = "";
            $text .= "<TR><TD colspan=4 align=\"right\">"
                . $userdaten_bearbeiten . "<br>\n"
                . home_farbe($u_id, $u_nick, $home, "info", $farben['info'])
                . "</TD></TR>\n";
        } else {
            $text .= "<TR><TD colspan=4>&nbsp;</TD></TR>\n";
        }
    }
    if (is_array($farben) && strlen($farben['info']) > 7) {
        $bg = "BACKGROUND=\"home_bild.php?http_host=$http_host&u_id=$u_id&feld="
            . $farben['info'] . "\"";
    } elseif (is_array($farben) && strlen($farben['info']) == 7) {
        $bg = "BGCOLOR=\"$farben[info]\"";
    } else {
        $bg = "";
    }
    
    return ("<TABLE $bg CELLPADDING=\"2\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\">$text</TABLE>");
}

function unhtmlentities($string)
{
    // replace numeric entities
    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
    // replace literal entities
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    
    return strtr($string, $trans_tbl);
}

function home_text($u_id, $u_nick, $home, $feld, $farben, $aktion)
{
    // Gibt TEXT-AREA Feld für $feld aus
    
    global $eingabe_breite2, $f1, $f2;
    
    $text = $home[$feld];
    
    // Wieso ist "target" in der stopwordliste??
    // Umschiffung der ausfilterung bei: target="_blank"
    $text = preg_replace("|target\s*=\s*.\"\s*\_blank\s*.\"|i",
        '####----TAR----####', $text);
    
    // Problem: wenn zeichen z.b. b&#97;ckground codiert sind
    // dann würde dadurch ein sicherheitsloch entstehen
    $text = unhtmlentities($text);
    
    //	$stopwordarray=array("background","java","script","activex","embed","target","javascript");
    $stopwordarray = array("|background|i", "|java|i", "|script|i",
        "|activex|i", "|target|i", "|javascript|i");
    $text = str_replace("\n", "<br>\n", $text);
    
    foreach ($stopwordarray as $stopword) {
        // str_replace ist case sensitive, gefährlichen, wenn JavaScript geschrieben wird, oder Target, oder ActiveX
        while (preg_match($stopword, $text))
            $text = preg_replace($stopword, "", $text);
    }
    
    $text = str_replace("####----TAR----####", "target=\"_blank\"", $text);
    
    // Wir löschen die On-Handler aus der Homepage (einfache Version)
    // on gefolgt von 3-12 Buchstaben wird durch off ersetzt
    
    $text = preg_replace('|\son([a-z]{3,12})\s*=|i', ' off\\1=', $text);
    
    if ($aktion == "aendern") {
        $text = "<TR><TD COLSPAN=4 VALIGN=\"TOP\">$f1<b>Ihr Text über sich selbst:</b>$f2<br><TEXTAREA COLS="
            . ($eingabe_breite2) . " ROWS=20 NAME=\"home[$feld]\">"
            . $home[$feld] . "</TEXTAREA></TD></TR>"
            . "<TR><TD VALIGN=\"TOP\" ALIGN=\"RIGHT\">"
            . home_farbe($u_id, $u_nick, $home, $feld, $farben[$feld])
            . "</TD></TR>";
    } else {
        $text = "<TR><TD COLSPAN=4 VALIGN=\"TOP\">" . $text . "</TD></TR>";
    }
    
    if (is_array($farben) && strlen($farben['ui_text']) > 7) {
        $bg = "BACKGROUND=\"home_bild.php?http_host=$http_host&u_id=$u_id&feld="
            . $farben['ui_text'] . "\"";
    } elseif (is_array($farben) && strlen($farben['ui_text']) == 7) {
        $bg = "BGCOLOR=\"$farben[ui_text]\"";
    } else {
        $bg = "";
    }
    
    return ("<TABLE $bg CELLPADDING=\"5\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\">$text</TABLE>");
}

function home_bild(
    $u_id,
    $u_nick,
    $home,
    $feld,
    $farben,
    $aktion,
    $bilder,
    $beschreibung = "")
{
    global $PHP_SELF, $f1, $f2, $f3, $f4, $id, $eingabe_breite, $http_host;
    
    if (is_array($bilder) && isset($bilder[$feld]) && $bilder[$feld]['b_mime']) {
        
        $width = $bilder[$feld]['b_width'];
        $height = $bilder[$feld]['b_height'];
        $mime = $bilder[$feld]['b_mime'];
        
        if ($aktion == "aendern" || $aktion == "aendern_ohne_farbe") {
            $info = $f3 . "<br>Info: " . $width . "x" . $height . " als "
                . $mime . $f4;
        }
        
        if (!isset($info))
            $info = "";
        $text = "<TD ALIGN=\"CENTER\" VALIGN=\"TOP\" ><IMG SRC=\"home_bild.php?http_host=$http_host&u_id=$u_id&feld=$feld\" width=\"$width\" height=\"$height\" ALT=\"$u_nick\"><br>"
            . $info . "</TD>";
        
        if ($aktion == "aendern") {
            $text .= "<TD VALIGN=\"BOTTOM\" ALIGN=\"RIGHT\">" . $f3
                . "<b>[<A HREF=\"$PHP_SELF?http_host=$http_host&id=$id&aktion=aendern&loesche=$feld\">LÖSCHEN</A>]</b>"
                . $f4 . "<br>"
                . home_farbe($u_id, $u_nick, $home, $feld, $farben[$feld])
                . "</TD>\n";
        } elseif ($aktion == "aendern_ohne_farbe") {
            $text .= "<TD VALIGN=\"BOTTOM\" ALIGN=\"RIGHT\">" . $f3
                . "<b>[<A HREF=\"$PHP_SELF?http_host=$http_host&id=$id&aktion=aendern&loesche=$feld\">LÖSCHEN</A>]</b>"
                . $f4 . "</TD>\n";
        }
        
    } elseif ($aktion == "aendern" || $aktion == "aendern_ohne_farbe") {
        
        $text = "<TD ALIGN=\"CENTER\" VALIGN=\"TOP\"><b>Kein Bild hochgeladen - Achtung: Nur Bilder im JPG und GIF Format hochladen! Kein BMP Format! Die Bilder dürfen nicht grösser als 30 Kb sein!</b><br>"
            . "<INPUT TYPE=\"FILE\" NAME=\"$feld\" SIZE=\""
            . ($eingabe_breite / 8) . "\"></TD>";
        $text .= "<TD VALIGN=\"TOP\" ALIGN=\"RIGHT\">&nbsp;<br>"
            . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"GO\"><br></TD>\n";
        
    } else {
        
        $text = "";
        
    }
    
    if (!isset($farben[$feld])) {
        $bg = "";
    } elseif (strlen($farben[$feld]) > 7) {
        $bg = "BACKGROUND=\"home_bild.php?http_host=$http_host&u_id=$u_id&feld="
            . $farben[$feld] . "\"";
    } elseif (strlen($farben[$feld]) == 7) {
        $bg = "BGCOLOR=\"$farben[$feld]\"";
    } else {
        $bg = "";
    }
    
    if ($beschreibung)
        $text = "<TD align=\"RIGHT\">" . $f1 . $beschreibung . $f2 . "</TD>"
            . $text;
    if ($text && $beschreibung) {
        $text = "<TR>" . $text . "</TR>";
    } elseif ($text) {
        $text = "<TABLE $bg CELLPADDING=\"5\" CELLSPACING=\"0\"  WIDTH=\"100%\" BORDER=\"0\" ><TR>"
            . $text . "</TR></TABLE>";
    }
    
    return ($text);
}

function home_aktionen($u_id, $u_nick, $home, $farben, $aktion)
{
    // userdata wurde in home_info gesetzt
    global $chat, $id, $chat_grafik, $f1, $f2, $userdata, $id, $PHP_SELF;
    
    $fenster = str_replace("+", "", $u_nick);
    $fenster = str_replace("-", "", $fenster);
    $fenster = str_replace("ä", "", $fenster);
    $fenster = str_replace("ö", "", $fenster);
    $fenster = str_replace("ü", "", $fenster);
    $fenster = str_replace("Ä", "", $fenster);
    $fenster = str_replace("Ö", "", $fenster);
    $fenster = str_replace("Ü", "", $fenster);
    $fenster = str_replace("ß", "", $fenster);
    
    $text = "<TD VALIGN=\"TOP\">" . $f1;
    if ($id) {
        $url = "mail.php?aktion=neu2&neue_email[an_nick]=$u_nick&id=$id";
        $text .= "<A HREF=\"$url\" TARGET=\"640_$fenster\" onClick=\"neuesFenster2('$url'); return(false)\">"
            . "<b>Schreibe mir doch eine Mail!</b>&nbsp;$chat_grafik[mail]</A><br><br>";
    } else {
        #$text.="Ihr könnt mir eine Mail schicken, wenn Ihr euch vorher im ".
        #	"<A HREF=\"index.html\">$chat anmeldet</A>.<br><br>\n";
    }
    
    if ($userdata['u_url']) {
        $text .= "Mehr&nbsp;über&nbsp;mich: <b><A HREF=\"redirect.php?url="
            . urlencode($userdata['u_url'])
            . "\" TARGET=\"_new\">" . $userdata['u_url']
            . "</b></A><br>\n";
    }
    
    $text .= $f2 . "</TD>";
    if ($aktion == "aendern") {
        $text .= "<TD VALIGN=\"BOTTOM\" ALIGN=\"RIGHT\">"
            . home_farbe($u_id, $u_nick, $home, "aktionen", $farben['aktionen'])
            . "</TD>";
    }
    
    if (is_array($farben) && strlen($farben['aktionen']) > 7) {
        $bg = "BACKGROUND=\"home_bild.php?http_host=$http_host&u_id=$u_id&feld="
            . $farben['aktionen'] . "\"";
    } elseif (is_array($farben) && strlen($farben['aktionen']) == 7) {
        $bg = "BGCOLOR=\"$farben[aktionen]\"";
    } else {
        $bg = "";
    }
    
    $text = "<TABLE $bg CELLPADDING=\"5\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\"><TR>"
        . $text . "</TR></TABLE>";
    
    return ($text);
}

function home_hintergrund($u_id, $u_nick, $farben, $home, $bilder)
{
    // Einstellungen für den Hintergrund
    global $f1, $f2, $f3, $f4;
    
    $aktion = "aendern_ohne_farbe";
    
    $text = "<TR><TD align=\"RIGHT\">" . $f1 . "Hintergrund: " . $f2 . "</TD>"
        . "<TD BGCOLOR=\"$farben[bgcolor]\">&nbsp;</TD>" . "<TD>"
        . home_farbe($u_id, $u_nick, $home, "bgcolor", $farben['bgcolor'])
        . "</TD></TR>" . "<TR><TD align=\"RIGHT\">" . $f1 . "Textfarbe: " . $f2
        . "</TD>" . "<TD BGCOLOR=\"$farben[text]\">&nbsp;</TD>" . "<TD>"
        . home_farbe($u_id, $u_nick, $home, "text", $farben['text'], FALSE)
        . "</TD></TR>" . "<TR><TD align=\"RIGHT\">" . $f1 . "Linkfarbe: " . $f2
        . "</TD>" . "<TD BGCOLOR=\"$farben[link]\">&nbsp;</TD>" . "<TD>"
        . home_farbe($u_id, $u_nick, $home, "link", $farben['link'], FALSE)
        . "</TD></TR>" . "<TR><TD align=\"RIGHT\">" . $f1
        . "Linkfarbe (aktiv): " . $f2 . "</TD>"
        . "<TD BGCOLOR=\"$farben[vlink]\">&nbsp;</TD>" . "<TD>"
        . home_farbe($u_id, $u_nick, $home, "vlink", $farben['vlink'], FALSE)
        . "</TD></TR>"
        . home_bild($u_id, $u_nick, $home, "ui_bild4", $farben, $aktion,
            $bilder, "Hintergrundgrafik&nbsp;1:")
        . home_bild($u_id, $u_nick, $home, "ui_bild5", $farben, $aktion,
            $bilder, "Hintergrundgrafik&nbsp;2:")
        . home_bild($u_id, $u_nick, $home, "ui_bild6", $farben, $aktion,
            $bilder, "Hintergrundgrafik&nbsp;3:");
    
    if (strlen($farben['info']) > 7) {
        $bg = "BACKGROUND=\"home_bild.php?http_host=$http_host&u_id=$u_id&feld="
            . $farben['info'] . "\"";
    } elseif (strlen($farben['info']) == 7) {
        $bg = "BGCOLOR=\"$farben[info]\"";
    } else {
        $bg = "";
    }
    
    return ("<TABLE $bg CELLPADDING=\"2\" CELLSPACING=\"0\" BORDER=\"0\"  WIDTH=\"100%\">$text</TABLE>");
}

function home_einstellungen($u_id, $u_nick, $home, $einstellungen)
{
    // Einstellungen für Homepage:
    // Homepage öffentlich ja/nein
    // Profileinstellungen öffentlich ja/nein
    // Addresse öffentlich ja/nein
    
    global $f1, $f2, $vor_einstellungen;
    
    // Homepage freigeben
    if ($einstellungen['u_chathomepage'] == "J") {
        $checked = "CHECKED";
    } else {
        $checked = "";
    }
    $text = "<TR><TD align=\"RIGHT\">" . $f1 . "Homepage freigeben: " . $f2
        . "</TD>"
        . "<TD><INPUT TYPE=\"CHECKBOX\" NAME=\"einstellungen[u_chathomepage]\" $checked></TD></TR>\n";
    
    // Voreinstellungen fürs Profil
    if (!$home['ui_einstellungen']) {
        $ui_einstellungen = $vor_einstellungen;
    } else {
        $ui_einstellungen = unserialize($home['ui_einstellungen']);
    }
    
    // Einstellungen fürs Profil ausgeben
    foreach ($vor_einstellungen as $key => $val) {
        if ($ui_einstellungen[$key]) {
            $checked = "CHECKED";
        } else {
            $checked = "";
        }
        $text .= "<TR><TD align=\"RIGHT\">" . $f1 . $key . " zeigen:" . $f2
            . "</TD>"
            . "<TD><INPUT TYPE=\"CHECKBOX\" NAME=\"einstellungen[$key]\" $checked></TD></TR>\n";
    }
    
    return ("<TABLE CELLPADDING=\"2\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\">$text</TABLE>");
    
}

function home_farbe(
    $u_id,
    $u_nick,
    $home,
    $feld,
    $farbe = "#FFFFFF",
    $mit_grafik = TRUE)
{
    // gibt Link auf Farbwähler aus
    
    global $f3, $f4, $http_host, $id;
    
    $url = "home_farben.php?http_host=$http_host&id=$id&mit_grafik=$mit_grafik&feld=$feld&bg=Y&oldcolor="
        . urlencode($farbe);
    $link = $f3
        . "<b>[<A HREF=\"$url\" TARGET=\"Farben\" onclick=\"window.open('$url','Farben','resizable=yes,scrollbars=yes,width=400,height=500'); return(false);\">FARBE</A>]</b>"
        . $f4;
    
    return ($link);
}

function bild_holen($u_id, $name, $ui_bild, $groesse)
{
    // Prüft hochgeladenes Bild und speichert es in die Datenbank
    // u_id = ID des Users, dem das Bild gehört
    // 
    // Binäre Bildinformation -> home[ui_bild]
    // WIDTH                  -> home[ui_bild_width] 
    // HEIGHT                 -> home[ui_bild_height]
    // MIME-TYPE              -> home[ui_bild_mime]
    
    global $max_groesse, $dbase, $http_host, $mysqli_link;
    
    if ($ui_bild && $groesse > 0 && $groesse < ($max_groesse * 1024)) {
        
        $image = getimagesize($ui_bild);
        
        if (is_array($image)) {
            $fd = fopen($ui_bild, "rb");
            if ($fd) {
                $f['b_bild'] = fread($fd, filesize($ui_bild));
                fclose($fd);
            }
            
            switch ($image[2]) {
                case 1:
                    $f['b_mime'] = "image/gif";
                    break;
                case 2:
                    $f['b_mime'] = "image/jpeg";
                    break;
                case 3:
                    $f['b_mime'] = "image/png";
                    break;
                case 4:
                    $f['b_mime'] = "application/x-shockwave-flash";
                    break;
                
                default:
                    $f['b_mime'] = "";
            }
            
            $f['b_width'] = $image[0];
            $f['b_height'] = $image[1];
            $f['b_user'] = $u_id;
            $f['b_name'] = $name;
            
            if ($f['b_mime']) {
                $query = "SELECT b_id FROM bild WHERE b_user=$u_id AND b_name='" . mysqli_real_escape_string($mysqli_link, $name) . "'";
                $result = mysqli_query($mysqli_link, $query);
                if ($result && mysqli_num_rows($result) != 0) {
                	$b_id = mysqli_result($result, 0, 0);
                }
                schreibe_db("bild", $f, $b_id, "b_id");
            } else {
                echo "<P><b>Fehler: </b> Es wurde kein gültiges Bildformat (PNG, JPEG, GIF, Flash) hochgeladen!</P>\n";
            }
            
            // Bild löschen
            unlink($ui_bild);
            
            // Cache löschen
            $cache = "home_bild";
            $cachepfad = $cache . "/" . $http_host . "/" . substr($u_id, 0, 2)
                . "/" . $u_id . "/" . $name;
            if (file_exists($cachepfad)) {
                unlink($cachepfad);
                unlink($cachepfad . "-mime");
            }
            
        } else {
            echo "<P><b>Fehler: </b> Es wurde kein gültiges Bildformat (PNG, JPEG, GIF, Flash) hochgeladen!</P>\n";
            unlink($ui_bild);
        }
        
    } elseif ($groesse >= ($max_groesse * 1024)) {
        echo "<P><b>Fehler: </b> Das Bild muss kleiner als $max_groesse KB sein!</P>\n";
    }

    return ($home); // TODO: Wo wird $home definiert?
}

function home_url_parse($tag, $url)
{
    return ("$tag=\"redirect.php?url=" . urlencode($url) . "\" TARGET=\"_new\"");
}

function zeige_home($u_id, $force = FALSE, $defaultfarben = "")
{
    // Zeigt die Homepage des Users u_id an
    
    global $dbase, $mysqli_link, $argv, $argc, $http_host, $id, $homepage_extern, $check_name;
    
    if ($u_id && $u_id <> -1) {
        
        // Aufruf als home.php?ui_userid=USERID
        $query = "SELECT `u_id`, `u_nick`, `u_chathomepage` FROM `user` WHERE `u_id`=$u_id";
        
    } elseif ($u_id == -1 && isset($_SERVER['QUERY_STRING'])) {
        // Aufruf als home.php?USERNAME
        $tempnick = mysqli_real_escape_string($mysqli_link, strtolower(urldecode($_SERVER['QUERY_STRING'])));
        $tempnick = coreCheckName($tempnick, $check_name);
        
        $query = "SELECT `u_id`, `u_nick`, `u_chathomepage` FROM `user` WHERE `u_nick` = '"
            . $tempnick . "' and u_level in ('A','C','G','M','S','U')";
        if ($homepage_extern == "0")
            $query = "";
    }
    
    // Userdaten lesen
    if ($query) {
        $result = mysqli_query($mysqli_link, $query);
        if ($result && mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_object($result);
            $u_chathomepage = $row->u_chathomepage;
            $u_id = $row->u_id;
        }
        @mysqli_free_result($result);
    }
    
    // Profil lesen
    if ($u_id) {
        $query = "SELECT * FROM userinfo WHERE ui_userid=$u_id";
        $result = mysqli_query($mysqli_link, $query);
        if ($result && mysqli_num_rows($result) == 1) {
            $home = mysqli_fetch_array($result);
            if ($home['ui_farbe']) {
                $farben = unserialize($home['ui_farbe']);
            } else {
                $farben = $defaultfarben;
            }
            $ok = TRUE;
        } else {
            $ok = FALSE;
        }
        @mysqli_free_result($result);
        
        // Bildinfos lesen und in Array speichern
        $query = "SELECT b_name,b_height,b_width,b_mime FROM bild "
            . "WHERE b_user=$u_id";
        $result2 = mysqli_query($mysqli_link, $query);
        if ($result2 && mysqli_num_rows($result2) > 0) {
            unset($bilder);
            while ($row2 = mysqli_fetch_object($result2)) {
                $bilder[$row2->b_name]['b_mime'] = $row2->b_mime;
                $bilder[$row2->b_name]['b_width'] = $row2->b_width;
                $bilder[$row2->b_name]['b_height'] = $row2->b_height;
            }
        }
        @mysqli_free_result($result2);
        
        // Falls nicht freigeschaltet....
        if (!isset($u_chathomepage))
            $u_chathomepage = 'N';
        if (!$force && $u_chathomepage != "J")
            $ok = FALSE;
        
    } else {
        $ok = FALSE;
    }
    
    $aktion = "einfach";
    
    if (isset($farben['bgcolor']) && strlen($farben['bgcolor']) > 7) {
        $bg = "BACKGROUND=\"home_bild.php?http_host=$http_host&u_id=$u_id&feld="
            . $farben['bgcolor'] . "\"";
    } elseif (isset($farben['bgcolor']) && strlen($farben['bgcolor']) == 7) {
        $bg = "BGCOLOR=\"$farben[bgcolor]\"";
    } else {
        $bg = "";
    }
    
    if ($ok) {
        $body_tag = "<body $bg";
        if (is_array($farben)) {
            $body_tag .= " TEXT=\"$farben[text]\" " . "LINK=\"$farben[link]\" "
                . "VLINK=\"$farben[vlink]\" " . "ALINK=\"$farben[vlink]\"";
        }
        $body_tag .= ">\n";
        
        echo $body_tag
            . "<TABLE BORDER=\"0\" HEIGHT=\"100%\" WIDTH=\"100%\" CELLPADDING=0 CELLSPACING=0>"
            . "<TR><TD VALIGN=\"TOP\" width=\"60%\">"
            . home_info($u_id, $row->u_nick, $farben, $aktion)
            . home_profil($u_id, $row->u_nick, $home, $farben, $aktion);
        
        if (!isset($bilder))
            $bilder = "";
        
        $txt = home_text($u_id, $row->u_nick, $home, "ui_text", $farben,
            $aktion);
        echo $txt;
        echo "</TD>\n<TD VALIGN=\"TOP\" width=\"40%\">"
            . home_bild($u_id, $row->u_nick, $home, "ui_bild1", $farben,
                $aktion, $bilder)
            . home_bild($u_id, $row->u_nick, $home, "ui_bild2", $farben,
                $aktion, $bilder)
            . home_bild($u_id, $row->u_nick, $home, "ui_bild3", $farben,
                $aktion, $bilder)
            . home_aktionen($u_id, $row->u_nick, $home, $farben, $aktion);
        
        echo "</TD></TR></TABLE>\n";
        
        echo "</BODY>\n";
        
    } elseif ($u_chathomepage != "J") {
        
        echo "<body>"
            . "<P><b>Fehler: Dieser User hat keine Homepage!</b></P>";
        
        echo "</BODY>\n";
        
    } else {
        
        echo "<body>"
            . "<P><b>Fehler: Aufruf ohne gültige Parameter!</b></P>";

        echo "</BODY>\n";
        
    }
}
?>
