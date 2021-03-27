<?php

function profil_editor($u_id, $u_nick, $f)
{
    // Editor zur Änderung oder zur Neuanlage eines Profils
    // $u_id=User-Id
    // $u_nick=Nickname
    // $f=Array der Profileinstellungen
    global $dbase, $conn, $communityfeatures, $t, $http_host, $id, $eingabe_breite, $f1, $f2;
    global $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $farbe_tabelle_kopf2, $farbe_text;
    
    // Fenstername
    $fenster = str_replace("+", "", $u_nick);
    $fenster = str_replace("-", "", $u_nick);
    
    // Voreinstellungen
    if (!isset($f['ui_land']))
        $f['ui_land'] = "Deutschland";
    if (!isset($f['ui_strasse']))
        $f['ui_strasse'] = "";
    if (!isset($f['ui_plz']))
        $f['ui_plz'] = "";
    if (!isset($f['ui_ort']))
        $f['ui_ort'] = "";
    if (!isset($f['ui_tel']))
        $f['ui_tel'] = "";
    if (!isset($f['ui_handy']))
        $f['ui_handy'] = "";
    if (!isset($f['ui_fax']))
        $f['ui_fax'] = "";
    if (!isset($f['ui_icq']))
        $f['ui_icq'] = "";
    if (!isset($f['ui_geburt']))
        $f['ui_geburt'] = "";
    if (!isset($f['ui_beruf']))
        $f['ui_beruf'] = "";
    if (!isset($f['ui_hobby']))
        $f['ui_hobby'] = "";
    if (!isset($f['ui_id']))
        $f['ui_id'] = "";
    if (!isset($f['ui_geschlecht']))
        $f['ui_geschlecht'] = "";
    if (!isset($f['ui_beziehung']))
        $f['ui_beziehung'] = "";
    if (!isset($f['ui_typ']))
        $f['ui_typ'] = "";
    
    // Userdaten lesen
    $query = "SELECT * FROM user WHERE u_id=$u_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) == 1) {
        $userdata = mysqli_fetch_array($result);
        mysqli_free_result($result);
        $url = "edit.php?http_host=$http_host&id=$id";
        $userdaten_bearbeiten = "\n[<A HREF=\"$url\" TARGET=\"$fenster\" onclick=\"window.open('$url','$fenster','resizable=yes,scrollbars=yes,width=300,height=580'); return(false);\">Einstellungen ändern</A>]";
    }
    
    echo "<TABLE CELLPADDING=\"2\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\">\n";
    
    echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=5><DIV style=\"color:$farbe_text;\"><B>Ihre Userdaten:</B></DIV></TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_zeile1\"><TD align=\"right\">Nickname:</TD><TD colspan=2><B>"
        . user($userdata['u_id'], $userdata, TRUE, FALSE) . "</B></TD><TD>"
        . $userdaten_bearbeiten . "</TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_zeile2\"><TD align=\"right\">Username:</TD><TD colspan=3>"
        . htmlspecialchars($userdata['u_name']) . "</TD></TR>\n";
    $bgcolor = $farbe_tabelle_zeile1;
    if ($userdata['u_email']) {
        echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">E-Mail:</TD><TD colspan=3>"
            . htmlspecialchars($userdata['u_email'])
            . " (öffentlich)</TD></TR>\n";
        if ($bgcolor == $farbe_tabelle_zeile1) {
            $bgcolor = $farbe_tabelle_zeile2;
        } else {
            $bgcolor = $farbe_tabelle_zeile1;
        }
    }
    if ($userdata['u_adminemail']) {
        echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">E-Mail:</TD><TD colspan=3>"
            . htmlspecialchars($userdata['u_adminemail'])
            . " (intern, für Admins)</TD></TR>\n";
        if ($bgcolor == $farbe_tabelle_zeile1) {
            $bgcolor = $farbe_tabelle_zeile2;
        } else {
            $bgcolor = $farbe_tabelle_zeile1;
        }
    }
    if ($userdata['u_url']) {
        echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Homepage:</TD><TD colspan=3><A HREF=\""
            . htmlspecialchars($userdata['u_url'])
            . "\" TARGET=\"_new\">"
            . htmlspecialchars($userdata['u_url'])
            . "</A></TD></TR>\n";
        if ($bgcolor == $farbe_tabelle_zeile1) {
            $bgcolor = $farbe_tabelle_zeile2;
        } else {
            $bgcolor = $farbe_tabelle_zeile1;
        }
    }
    echo "<TR><TD><FORM NAME=\"profil\" ACTION=\"profil.php\" METHOD=POST>\n";
    echo "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"neu\">\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
        . "</TD></TR>\n";
    
    $bgcolor = $farbe_tabelle_zeile1;
    echo "<TR><TD COLSPAN=5>&nbsp;</TD></TR>\n"
        . "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=5><DIV style=\"color:$farbe_text;\"><B>Ihr neues Profil:</B></DIV></TD></TR>\n"
        . "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Straße:</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_strasse]\" VALUE=\""
        . $f['ui_strasse'] . "\" SIZE=$eingabe_breite>" . $f2
        . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">PLZ:</TD><TD>" . $f1
        . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_plz]\" VALUE=\"$f[ui_plz]\" SIZE=8>"
        . $f2
        . "</TD><TD align=\"right\">&nbsp;&nbsp;Ort:</TD><TD width=\"100%\">"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_ort]\" VALUE=\""
        . $f['ui_ort'] . "\" SIZE=" . ($eingabe_breite - 15)
        . ">" . $f2 . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Land:</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_land]\" VALUE=\""
        . $f['ui_land'] . "\" SIZE=$eingabe_breite>" . $f2
        . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Telefon:</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_tel]\" VALUE=\""
        . $f['ui_tel'] . "\" SIZE=$eingabe_breite>" . $f2
        . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Handy:</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_handy]\" VALUE=\""
        . $f['ui_handy'] . "\" SIZE=$eingabe_breite>" . $f2
        . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Telefax:</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_fax]\" VALUE=\""
        . $f['ui_fax'] . "\" SIZE=$eingabe_breite>" . $f2
        . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">ICQ:</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_icq]\" VALUE=\""
        . $f['ui_icq'] . "\" SIZE=$eingabe_breite>" . $f2
        . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Geburtsdatum:</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_geburt]\" VALUE=\""
        . $f['ui_geburt'] . "\" SIZE=12>"
        . " (TT.MM.JAHR, z.B. 24.01.1969)" . $f2 . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Geschlecht:</TD><TD colspan=3>"
        . $f1;
    autoselect("f[ui_geschlecht]", $f['ui_geschlecht'], "userinfo",
        "ui_geschlecht");
    echo $f2 . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Beziehung:</TD><TD colspan=3>"
        . $f1;
    autoselect("f[ui_beziehung]", $f['ui_beziehung'], "userinfo",
        "ui_beziehung");
    echo $f2 . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Typ:</TD><TD colspan=3>"
        . $f1;
    autoselect("f[ui_typ]", $f['ui_typ'], "userinfo", "ui_typ");
    echo $f2 . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">Beruf:</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"TEXT\" NAME=\"f[ui_beruf]\" VALUE=\""
        . $f['ui_beruf'] . "\" SIZE=$eingabe_breite>" . $f2
        . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\" valign=\"top\">Hobbies:</TD><TD colspan=3>"
        . $f1 . "<TEXTAREA NAME=\"f[ui_hobby]\" VALUE=\""
        . $f['ui_hobby'] . "\" ROWS=\"4\" COLS=\""
        . ($eingabe_breite - 6) . "\" wrap=\"virtual\">$f[ui_hobby]</TEXTAREA>"
        . $f2 . "</TD></TR>\n";
    if ($bgcolor == $farbe_tabelle_zeile1) {
        $bgcolor = $farbe_tabelle_zeile2;
    } else {
        $bgcolor = $farbe_tabelle_zeile1;
    }
    
    echo "<TR BGCOLOR=\"$bgcolor\"><TD align=\"right\">&nbsp;</TD><TD colspan=3>"
        . $f1 . "<INPUT TYPE=\"HIDDEN\" NAME=\"f[ui_id]\" VALUE=\"$f[ui_id]\">"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"f[ui_userid]\" VALUE=\"$u_id\">"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"nick\" VALUE=\"$userdata[u_nick]\">"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"neu\">"
        . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"EINTRAGEN\">" . $f2
        . "</TD></TR>\n" . "</TABLE>\n";
    
}

?>
