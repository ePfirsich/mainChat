<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

// optional Konfiguration für smilies lesen
if (isset($smilies_config) && file_exists("conf/" . $smilies_config)) {
    unset($smilie);
    unset($smilietxt);
    require("conf/" . $smilies_config);
}

$title = $body_titel . ' - Info';
zeige_header_anfang($title, $farbe_mini_background, $grafik_mini_background, $farbe_mini_link, $farbe_mini_vlink);

echo "<script>\n";
echo "  var http_host='$http_host';\n";
echo "  var id='$id';\n";
echo "  var stdparm='?http_host='+http_host+'&id='+id;\n";
echo "</script><script src=\"jscript.js\"></script>\n";
?>
<?php
zeige_header_ende();
?>
<body>
<?php
// Login ok?
if (strlen($u_id) != 0) {
    // Farben einstellen
    if ($smilies_hintergrund) {
        $farbe_tabelle_zeile1 = $smilies_hintergrund;
        $farbe_tabelle_zeile2 = $smilies_hintergrund;
    }
    
    if (!isset($r_name))
        $r_name = "";
    // Menue ausgeben, Tabelle aufbauen
    $linkuser = "href=\"user.php?http_host=$http_host&id=$id&aktion=chatuserliste\"";
    $linksmilies = "href=\"" . $smilies_datei
        . "?http_host=$http_host&id=$id\"";
    echo "<CENTER><b>$r_name</b><br>$f3<b>[<a onMouseOver=\"return(true)\" $linksmilies>"
        . $t['sonst1'] . "</A>]&nbsp;"
        . "[<a onMouseOver=\"return(true)\" $linkuser>" . $t['sonst2']
        . "</A>]</b>$f4<br>"
        . "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"3\" >\n";
    
    // Unterscheidung JavaScript an/aus
    if ($o_js) {
        
        // Array mit Smilies einlesen, Tabelle in Javascript ausgeben
        reset($smilie);
        while (list($smilie_code, $smilie_grafik) = each($smilie)) {
            $jsarr[] = "'$smilie_code','$smilie_grafik','"
                . str_replace(" ", "&nbsp;", $smilietxt[$smilie_code]) . "'";
        }
        
        echo "\n\n<SCRIPT LANGUAGE=\"JavaScript\">\n"
            . "   var color         = new Array('$farbe_tabelle_zeile1','$farbe_tabelle_zeile2');\n"
            . "   var fett          = new Array('$f1<b>','</b>$f2','$f3','$f4','$f1','$f2');\n"
            . "   var smilies_pfad  = '$smilies_pfad';\n"
            . "   var liste         = new Array(\n   "
            . @implode(",\n   ", $jsarr) . "   );\n"
            . "   showsmiliegrafiken(liste);\n" . 
            //			     "   stdparm=''; stdparm2=''; id=''; http_host=''; u_nick=''; raum=''; nlink=''; nick=''; url='';\n".
            "</SCRIPT>\n";
        
    } else { // kein javascript verfügbar
    
    // Array mit Smilies einlesen, HTML-Tabelle ausgeben
        reset($smilie);
        $schalt = TRUE;
        while (list($smilie_code, $smilie_grafik) = each($smilie)) {
            if ($schalt) {
                $farbe_tabelle = $farbe_tabelle_zeile1;
                $schalt = FALSE;
            } else {
                $farbe_tabelle = $farbe_tabelle_zeile2;
                $schalt = TRUE;
            }
            echo "<TR BGCOLOR=\"$farbe_tabelle\">"
                . "<TD>$f1<b>$smilie_code</b>$f2</TD>" . "<TD>" . $f1
                . str_replace(" ", "&nbsp;", $smilietxt[$smilie_code]) . $f2
                . "</TD>" . "<TD><IMG SRC=\"" . $smilies_pfad . $smilie_grafik
                . "\"></TD>" . "</TR>\n";
        }
    }
    
    echo "</TABLE>"
        . "<b>$r_name</b><br>$f3<b>[<a onMouseOver=\"return(true)\" $linksmilies>"
        . $t['sonst1'] . "</A>]&nbsp;"
        . "[<a onMouseOver=\"return(true)\" $linkuser>" . $t['sonst2']
        . "</A>]</b>$f4</CENTER>";
    
} else {
    echo "<p style=\"text-align:center;\">$t[sonst15]</p>\n";
}

?>
</body>
</html>