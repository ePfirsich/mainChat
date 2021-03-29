<?php

// Kopf nur ausgeben, wenn $ui_userid oder mit $argv[0] aufgerufen
// Sonst geht der redirekt nicht mehr.

require_once("functions-registerglobals.php");

if (isset($ui_userid) || (isset($aktion) && $aktion != "")
    || (isset($_SERVER["QUERY_STRING"]) && $_SERVER["QUERY_STRING"] <> "")) {
    require("functions.php");
    include("functions.php-hash.php");
    
    // Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
    if (isset($id)) {
        id_lese($id);
    } else {
        $u_nick = "";
    }
    // Kopf ausgeben
    
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
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel . "_Home"; ?></title>
<meta charset="utf-8">
<script>
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
</script>
<?php echo $stylesheet; ?>
</head>
<?php
} else {
    
    // Aufruf als home.php/USERNAME -> Redirekt auf home.php?USERNAME
    $u_id = "";
    $suchwort = $_SERVER["PATH_INFO"];
    if (substr($suchwort, -1) == "/")
        $suchwort = substr($suchwort, 0, -1);
    $suchwort = strtolower(substr($suchwort, strrpos($suchwort, "/") + 1));
    
    if (strlen($suchwort) >= 4) {
        header(
            "Location: http://" . $_SERVER["HTTP_HOST"]
                . $_SERVER["SCRIPT_NAME"] . "?" . $suchwort);
    }
}

// Pfad auf Cache
$cache = "home_bild";

if (isset($u_id) && $u_id && $communityfeatures) {
    
    // Timestamp im Datensatz aktualisieren
    aktualisiere_online($u_id, $o_raum);
    
    // Browser prüfen
    if (ist_netscape()) {
        $eingabe_breite = 40;
        $eingabe_breite1 = 49;
        $eingabe_breite2 = 30;
    } else {
        $eingabe_breite = 55;
        $eingabe_breite1 = 87;
        $eingabe_breite2 = 50;
    }
    
    // Voreinstellungen
    $max_groesse = 30; // Maximale Bild- und Text größe in KB
    $vor_einstellungen = ARRAY("Straße" => TRUE, "Tel" => TRUE, "Fax" => TRUE,
        "Handy" => TRUE, "PLZ" => TRUE, "Ort" => TRUE, "Land" => TRUE,
        "ICQ" => TRUE, "Hobbies" => TRUE, "Beruf" => TRUE,
        "Geschlecht" => TRUE, "Geburtsdatum" => TRUE, "Typ" => TRUE,
        "Beziehung" => TRUE);
    $farbliste = ARRAY(0 => "bgcolor", "info", "profil", "ui_text", "ui_bild1",
        "ui_bild2", "ui_bild3", "text", "link", "vlink", "aktionen");
    
    // Farben prüfen Voreinstellungen setzen
    foreach ($farbliste as $val) {
        if (isset($farben) && isset($farben[$val])) {
            if (strlen($farben[$val]) == 0) {
                // Voreinstellung
                switch ($val) {
                    case "bgcolor":
                        $farben[$val] = $farbe_mini_background;
                        break;
                    case "text":
                        $farben[$val] = $farbe_mini_text;
                        break;
                    case "link":
                        $farben[$val] = $farbe_mini_link;
                        break;
                    case "vlink":
                        $farben[$val] = $farbe_mini_vlink;
                        break;
                    default:
                        $farben[$val] = $farbe_tabelle_zeile2;
                }
            } elseif (substr($farben[$val], 0, -1) == "ui_bild") {
                
            } elseif (substr($farben[$val], 0, 1) != "#") {
                // Raute ergänzen
                $farben[$val] = "#" . $farben[$val];
            }
            // Auf 7 Zeichen kürzen
            if (substr($farben[$val], 0, 1) == "#") {
                $farben[$val] = substr($farben[$val], 0, 7);
            }
        }
    }
    
    switch ($aktion) {
        
        case "aendern":
        // Userid des Users, dessen Homepage geändert oder angezeigt wird.
            $ui_userid = $u_id;
            
            // Homepage für User $u_id bearbeiten
            
            // Body-Tag ausgeben
            $body_tag = "<BODY BGCOLOR=\"$farbe_mini_background\" ";
            if (strlen($grafik_mini_background) > 0) :
                $body_tag = $body_tag
                    . "BACKGROUND=\"$grafik_mini_background\" ";
            endif;
            $body_tag = $body_tag . "TEXT=\"$farbe_mini_text\" "
                . "LINK=\"$farbe_mini_link\" " . "VLINK=\"$farbe_mini_vlink\" "
                . "ALINK=\"$farbe_mini_vlink\">\n";
            echo $body_tag;
            
            // Menü als erstes ausgeben
            $box = $ft0 . "Menü Freunde" . $ft1;
            $text = "<A HREF=\"home.php?http_host=$http_host&id=$id&ui_userid=$u_id&aktion=&preview=yes\">Meine Homepage zeigen</A>\n"
                . "| <A HREF=\"home.php?http_host=$http_host&id=$id&aktion=aendern\">Meine Homepage bearbeiten</A>\n"
                . "| <A HREF=\"profil.php?http_host=$http_host&id=$id&aktion=aendern\">Profil ändern</A>\n"
                . "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#home\">Hilfe</A>\n";
            
            show_box2($box, $text, "100%");
            
            // Bild löschen
            if (isset($loesche) && substr($loesche, 0, 7) <> "ui_bild") {
                unset($loesche);
            }
            
            if (isset($loesche) && $loesche && $ui_userid) {
                $query = "DELETE FROM bild WHERE "
                    . "b_name='" . mysqli_real_escape_string($mysqli_link, $loesche) . "' AND b_user=" . intval($ui_userid);
                $result = mysqli_query($mysqli_link, $query);
                
                $cache = "home_bild";
                $cachepfad = $cache . "/" . $http_host . "/"
                    . substr($ui_userid, 0, 2) . "/" . $ui_userid . "/"
                    . $loesche;
                
                if (file_exists($cachepfad)) {
                    unlink($cachepfad);
                    unlink($cachepfad . "-mime");
                }
            }
            
            // Prüfen & in DB schreiben
            if (isset($home) && is_array($home)
                && strlen($home['ui_text']) > ($max_groesse * 1024)) {
                echo "<P><B>Fehler: </B> Ihr Text ist grösser als $max_groesse!</P>";
                unset($home['ui_text']);
            }
            
            if (isset($home) && is_array($home) && $home['ui_id']) {
                
                // Einstellungen für Homepage
                if (isset($einstellungen['u_chathomepage'])
                    && $einstellungen['u_chathomepage'] == "on") {
                    $einstellungen['u_chathomepage'] = "J";
                } else {
                    $einstellungen['u_chathomepage'] = "N";
                }
                
                // Bei Änderung der Einstellung speichern
                if ($einstellungen['u_chathomepage']
                    != $userdata['u_chathomepage']) {
                    unset($f);
                    $userdata['u_chathomepage'] = $einstellungen['u_chathomepage'];
                    $f['u_chathomepage'] = $einstellungen['u_chathomepage'];
                    schreibe_db("user", $f, $ui_userid, "u_id");
                }
                
                // hochgeladene Bilder in DB speichern
                $bildliste = ARRAY("ui_bild1", "ui_bild2", "ui_bild3",
                    "ui_bild4", "ui_bild5", "ui_bild6");
                foreach ($bildliste as $val) {
                    if (isset($_FILES[$val])
                        && is_uploaded_file($_FILES[$val]['tmp_name'])) {
                        // Abspeichern
                        bild_holen($ui_userid, $val, $_FILES[$val]['tmp_name'],
                            $_FILES[$val]['size']);
                    }
                }
                
                // Einstellungen aus Checkboxen True/False setzen und in ui_einstellungen packen
                if (is_array($einstellungen)) {
                    unset($einstellungen['u_chathomepage']);
                    foreach ($vor_einstellungen as $key => $val) {
                        if (isset($einstellungen[$key])
                            && $einstellungen[$key] == "on") {
                            $einstellungen[$key] = TRUE;
                        } else {
                            $einstellungen[$key] = FALSE;
                        }
                    }
                    $home['ui_einstellungen'] = serialize($einstellungen);
                }
                
                // Farben in ui_farbe packen
                if (is_array($farben)) {
                    $home['ui_farbe'] = serialize($farben);
                }
                
                // Änderungen in DB schreiben
                $ui_id = schreibe_db("userinfo", $home, $home['ui_id'], "ui_id");
                
            }
            
            // Daten laden und Editor anzeigen
            unset($home);
            $query = "SELECT * FROM userinfo WHERE ui_userid=" . intval($ui_userid);
            $result = mysqli_query($mysqli_link, $query);
            if ($result && mysqli_num_rows($result) == 1) {
                
                // Userprofil aus DB lesen
                $home = mysqli_fetch_array($result);
                if ($home['ui_farbe']) {
                    $farbentemp = unserialize($home['ui_farbe']);
                    if (is_array($farbentemp))
                        $farben = $farbentemp;
                }
                
                // Einstellung für u_chathomepage aus Userdaten lesen
                $einstellungen['u_chathomepage'] = $userdata['u_chathomepage'];
                
                // Bildinfos lesen und in Array speichern
                $query = "SELECT b_name,b_height,b_width,b_mime FROM bild "
                    . "WHERE b_user=" . intval($ui_userid);
                $result2 = mysqli_query($mysqli_link, $query);
                if ($result2 && mysqli_num_rows($result2) > 0) {
                    unset($bilder);
                    while ($row = mysqli_fetch_object($result2)) {
                        $bilder[$row->b_name]['b_mime'] = $row->b_mime;
                        $bilder[$row->b_name]['b_width'] = $row->b_width;
                        $bilder[$row->b_name]['b_height'] = $row->b_height;
                    }
                }
                @mysqli_free_result($result2);
                
                // Hidden Felder für die Farben erzeugen
                $inputliste = "";
                foreach ($farbliste as $val) {
                    $inputliste .= "<INPUT TYPE=\"HIDDEN\" NAME=\"farben[$val]\" VALUE=\""
                        . (isset($farben) ? $farben[$val] : "") . "\">\n";
                }
                
                echo "<FORM ENCTYPE=\"multipart/form-data\" NAME=\"home\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
                    . $inputliste
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"aendern\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"ui_userid\" VALUE=\"$ui_userid\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"home[ui_id]\" VALUE=\""
                    . $home['ui_id'] . "\">\n"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
                    . "<input TYPE=\"HIDDEN\" NAME=\"MAX_FILE_SIZE\" VALUE=\""
                    . ($max_groesse * 1024) . "\">\n";
                
                if (!isset($bilder))
                    $bilder = "";
                edit_home($ui_userid, $u_nick, $home, $einstellungen,
                    (isset($farben) ? $farben : null), $bilder, $aktion);
                echo "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"SPEICHERN\"></FORM>\n";
                
            } else {
                
                // Erst Profil anlegen
                echo "<P><B>Hinweis: </B> Sie haben leider noch kein Profil angelegt. Das Profil "
                    . "mit Ihren persönlichen Daten ist aber die Vorraussetzung für die Homepage. "
                    . "Bitte klicken Sie <A HREF=\"profil.php?http_host=$http_host&id=$id&aktion=aendern\">"
                    . "weiter zur Anlage eines Profils</A>.</P>\n";
                
            }
            @mysqli_free_result($result);
            
            if ($o_js || !$u_id)
                echo $f1
                    . "<CENTER>[<A HREF=\"javascript:window.close();\">$t[sonst1]</A>]</CENTER>"
                    . $f2 . "<BR>\n";
            
            echo "</BODY>";
            
            break;
        
        default:
            $hash = genhash($ui_userid);
            $url = "zeige_home.php?http_host=$http_host&ui_userid=$ui_userid&hash=$hash";
            if (isset($preview) && $preview == "yes")
                $url = "zeige_home.php?http_host=$http_host&ui_userid=$ui_userid&hash=$hash&preview=yes&preview_id=$id";
?>
<!DOCTYPE html>
<html>
<head>
<title>DEREFER</title>
<meta charset="utf-8">
<?php
echo '
<META HTTP-EQUIV="REFRESH" CONTENT="0; URL=' . $url
                . ' ">
</head>
<body bgcolor="#ffffff" link="#666666" vlink="#666666">
<table width="100%" height="100%" border="0"><tr><td align="center"><a href="'
                . $url
                . '"><font face="Arial, Helvetica, sans-serif" size="2" color="#666666">Einen Moment bitte, die angeforderte Seite wird geladen...</font></a></td></tr></table>
</body></html>';
    }
    
} else {
    require_once("functions.php-home.php");
    if (!isset($ui_userid))
        $ui_userid = -1;
    zeige_home($ui_userid, FALSE);
}
?>
</body>
</html>