<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel . "_Info"; ?></title>
<meta charset="utf-8">
<?php
echo "<SCRIPT>\n";
echo "  var http_host='$http_host';\n";
echo "  var id='$id';\n";
echo "  var stdparm='?http_host='+http_host+'&id='+id;\n";
echo "</SCRIPT><SCRIPT src=\"jscript.js\"></script>\n";
?>
<style type="text/css">
<?php echo $stylesheet; ?>
<?php echo "a { text-decoration: none; font-weight:bold }"; ?>
body {
	background-color:<?php echo $farbe_mini_background; ?>;
<?php if(strlen($grafik_mini_background) > 0) { ?>
	background-image:<?php echo $grafik_mini_background; ?>;
<?php } ?>
}
a, a:link {
	color:<?php echo $farbe_mini_link; ?>;
}
a:visited, a:active {
	color:<?php echo $farbe_mini_vlink; ?>;
}
</style>
</head>
<body>
<?php
// Login ok?
if (strlen($u_id) != 0) {
    // Menue ausgeben, Tabelle aufbauen
    if (!isset($r_name)) {
        $r_name = "";
    }
    $linkuser = "href=\"user.php?http_host=$http_host&id=$id&aktion=chatuserliste\"";
    $linksmilies = "href=\"" . $smilies_datei
        . "?http_host=$http_host&id=$id\"";
    echo "<CENTER><B>$r_name</B><BR>$f3<B>[<a onMouseOver=\"return(true)\" $linksmilies>"
        . $t['sonst1'] . "</A>]&nbsp;"
        . "[<a onMouseOver=\"return(true)\" $linkuser>" . $t['sonst2']
        . "</A>]</B>$f4<BR>"
        . "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" >\n";
    
    // Unterscheidung JavaScript an/aus
    if ($o_js) {
        
        // Array mit Smilies einlesen, Tabelle in Javascript ausgeben
        reset($smilie);
        while (list($smilie_code, $smilie_text) = each($smilie)) {
            $jsarr[] = "'$smilie_code','"
                . str_replace(" ", "&nbsp;", $smilie_text) . "'";
        }
        
        echo "\n\n<SCRIPT LANGUAGE=\"JavaScript\">\n"
            . "   var color = new Array('$farbe_tabelle_zeile1','$farbe_tabelle_zeile2');\n"
            . "   var fett  = new Array('$f1<B>','</B>$f2','$f3','$f4','$f1','$f2');\n"
            . "   var liste = new Array(\n   " . @implode(",\n   ", $jsarr)
            . "   );\n" . "   showsmilies(liste);\n" . "</SCRIPT>\n";
        
    } else { // kein javascript verfügbar
    
    // Array mit Smilies einlesen, HTML-Tabelle ausgeben
        reset($smilie);
        $schalt = TRUE;
        while (list($smilie_code, $smilie_text) = each($smilie)) {
            if ($schalt) {
                $farbe_tabelle = $farbe_tabelle_zeile1;
                $schalt = FALSE;
            } else {
                $farbe_tabelle = $farbe_tabelle_zeile2;
                $schalt = TRUE;
            }
            echo "<TR BGCOLOR=\"$farbe_tabelle\"><TD>$f1<B>$smilie_code</B>$f2</TD>"
                . "<TD>" . $f1 . str_replace(" ", "&nbsp;", $smilie_text) . $f2
                . "</TD></TR>\n";
        }
    }
    
    echo "</TABLE>"
        . "<B>$r_name</B><BR>$f3<B>[<a onMouseOver=\"return(true)\" $linksmilies>"
        . $t['sonst1'] . "</A>]&nbsp;"
        . "[<a onMouseOver=\"return(true)\" $linkuser>" . $t['sonst2']
        . "</A>]</B>$f4</CENTER>";
    
} else {
    echo "<P ALIGN=CENTER>$t[sonst15]</P>\n";
}

?>
</body>
</html>