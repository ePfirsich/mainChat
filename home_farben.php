<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel . "_Farben"; ?></title>
<meta charset="utf-8">
<script>
window.focus()
function colorsave() {
<?php

list($params, $query) = preg_split("/\?/", $QUERY_STRING . "?"); // ? am Ende um mindestens zwei Elemente zu haben
list($x, $y) = preg_split("/,/", $query . ","); // , am Ende um mindestens zwei Elemente zu haben

if ($y > 256)
    $y = 256;
if ($x < 16) {
    $R = floor($y);
    if ($R == 256)
        $R = 255;
    $G = $R;
    $B = $R;
} else {
    $x = $x - 16;
    if ($x > 256)
        $x = 256;
    $R = floor(($x % 64) / 4) * 16;
    $G = floor(($y % 64) / 4) * 16;
    $B = 0;
    $B = (floor($x / 64) + 4 * floor($y / 64)) * 16;
}
$newcolor = sprintf("%02x%02x%02x", $R, $G, $B);
if ($mit_grafik && isset($farbe_grafik) && $farbe_grafik) {
    $newcolor = $farbe_grafik;
} elseif ($mit_grafik && isset($setcolor) && $setcolor) {
    $newcolor = $setcolor;
} elseif ($x == "" && $mit_grafik && strlen($oldcolor) > 7) {
    $farbe_grafik = $oldcolor;
    $newcolor = $oldcolor;
    $oldcolor = "#FFFFFF";
} elseif ($x == "") {
    $newcolor = $oldcolor;
}
if (strlen($newcolor) < 8 && $newcolor > 1) {
    $setcolor = $newcolor;
} elseif (strlen($oldcolor) < 8 && $oldcolor > 1) {
    $setcolor = $oldcolor;
}

echo "    FARBE='" . $newcolor . "';\n";
echo "    if (FARBE!= \"\") {\n";
echo "    opener.document.forms[0].elements['farben[$feld]'].value=FARBE;\n";
if ($bg == "Y") {
    echo "    opener.document.forms[0].submit();\n";
} else {
    echo "    opener.document.images[\"img_$feld\"].src=\"home_makecolor.php?";
    if ($bg != "N") {
        echo "bg=$bg&fg=$newcolor\";\n";
    } else {
        echo "bg=$newcolor&text=%20\"\n";
    }
}

?>
    window.close();
    } else {
        alert('Bitte wählen Sie die Farbe vorher aus!\nHierzu einfach auf die Farbselectbox klicken.');
    }
}
</script>
<?php echo $stylesheet; ?>
</head>
<?php
$body_tag = "<BODY BGCOLOR=\"$farbe_mini_background\" ";
if (strlen($grafik_mini_background) > 0) :
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_mini_background\" ";
endif;
$body_tag = $body_tag . "TEXT=\"$farbe_mini_text\" "
    . "LINK=\"$farbe_mini_link\" " . "VLINK=\"$farbe_mini_vlink\" "
    . "ALINK=\"$farbe_mini_vlink\">\n";
echo $body_tag;

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

if ($u_id && $communityfeatures) {
    echo "<center><b>Bitte wählen Sie die Farbe aus:</b><BR>"
        . "<a href=\"home_farben.php?http_host=$http_host&id=$id&mit_grafik=$mit_grafik&feld=$feld&bg=$bg&oldcolor="
        . urlencode($oldcolor) . "&nix\">"
        . "<img src=\"pics/colors2.png\" ismap border=0></a>\n";
    echo "<br clear=all><img src=\"home_makecolor.php?x=135&y=25&text=neue%20Farbe&";
    
    if ($bg != "Y" && $bg != "N") {
        echo "bg=" . urlencode($bg) . "&fg=" . urlencode($newcolor) . "\";\n";
    } else {
        echo "bg=" . urlencode($newcolor) . "\"\n";
    }
    echo " height=25 width=135 border=0>";
    echo "<img src=\"home_makecolor.php?x=135&y=25&text=aktuelle%20Farbe&";
    
    if ($bg != "Y" && $bg != "N") {
        echo "bg=" . urlencode($bg) . "&fg=" . urlencode($oldcolor)
            . "&nix\";\n";
    } else {
        echo "bg=" . urlencode($oldcolor) . "&nix\"\n";
    }
    echo " height=25 width=135 border=0><BR>";
    
    // Optional Auswahl einer Grafik statt der Farbe
    if ($mit_grafik) {
        
        $bildliste = ARRAY('ui_bild4' => "Hintergrundgrafik 1",
            'ui_bild5' => "Hintergrundgrafik 2",
            'ui_bild6' => "Hintergrundgrafik 3");
        echo "<TABLE><TR><TD>"
            . "<FORM NAME=\"bildname\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"bg\" VALUE=\"$bg\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"oldcolor\" VALUE=\"$oldcolor\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"feld\" VALUE=\"$feld\">\n"
            . "<INPUT TYPE=\"HIDDEN\" NAME=\"mit_grafik\" VALUE=\"$mit_grafik\">\n"
            . $f1 . "oder&nbsp;mit&nbsp;Bild:$f2</TD>"
            . "<TD>$f1<SELECT NAME=\"farbe_grafik\" onChange=\"Javascript:submit()\">"
            . "<OPTION VALUE=\"\">Kein Bild\n";
        if (!isset($farbe_grafik))
            $farbe_grafik = "";
        foreach ($bildliste as $key => $val) {
            if ($key == $farbe_grafik) {
                echo "<OPTION SELECTED VALUE=\"$key\">$val\n";
            } else {
                echo "<OPTION VALUE=\"$key\">$val\n";
            }
        }
        if (!isset($setcolor))
            $setcolor = "";
        echo "</SELECT><INPUT TYPE=\"SUBMIT\" NAME=\"GO\" VALUE=\"GO\">" . $f2
            . "</TD></TR>\n" . "<TR><TD>" . $f1 . "oder direkt:" . $f2
            . "</TD>"
            . "<TD>$f1<INPUT TYPE=\"TEXT\" NAME=\"setcolor\" VALUE=\"$setcolor\" SIZE=\"7\" MAXLENGTH=\"7\" onChange=\"Javascript:submit()\">"
            . "<INPUT TYPE=\"SUBMIT\" NAME=\"GO\" VALUE=\"GO\">$f2</TD></TR>\n"
            . "<TR><TD>" . $f1 . "oder transparent:" . $f2 . "</TD>"
            . "<TD>$f1<B>[<A href=\"home_farben.php?http_host=$http_host&id=$id&mit_grafik=$mit_grafik&feld=$feld&bg=$bg&oldcolor=0&nix\">"
            . "wählen</A>]</B>$f2</TD></TR></TABLE>\n";
        
    } else {
        echo "<BR>";
    }
    
    echo "<BR>" . $f1
        . "<B>[<a href=\"javascript:colorsave();\">Farbe übernehmen</a>]\n"
        . "[<a href=\"home_farben.php?http_host=$http_host&id=$id&mit_grafik=$mit_grafik&feld=$feld&bg=$bg&oldcolor=$oldcolor&$oldcolor&nix\">Reset</a>]\n"
        . "[<a href=javascript:window.close();>nicht&nbsp;speichern</a>]</B>"
        . $f2 . "</CENTER>\n";
}
?>
</body>
</html>