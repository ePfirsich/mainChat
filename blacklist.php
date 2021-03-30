<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js
id_lese($id);

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
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel . "_Blacklist"; ?></title>
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
        function toggle(tostat ) {
                for(i=0; i<document.forms["blacklist_loeschen"].elements.length; i++) {
                     e = document.forms["blacklist_loeschen"].elements[i];
                     if ( e.type=='checkbox' )
                         e.checked=tostat;
                }
        }
</script>
<style type="text/css">
<?php echo $stylesheet; ?>
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
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$eingabe_breite = 45;

if ($admin && $u_id && $communityfeatures) {
    
    // Menü als erstes ausgeben
    $box = $ft0 . "Menü Blacklist" . $ft1;
    $text = "<A HREF=\"blacklist.php?http_host=$http_host&id=$id&aktion=\">Blacklist zeigen</A>\n"
        . "| <A HREF=\"blacklist.php?http_host=$http_host&id=$id&aktion=neu\">Neuen Eintrag hinzufügen</A>\n"
        . "| <A HREF=\"sperre.php?http_host=$http_host&id=$id\">Zugangssperren</A>\n";
    
    show_box2($box, $text, "100%");
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
    
    if (!isset($neuer_blacklist))
        $neuer_blacklist[] = "";
    if (!isset($sort))
        $sort = "";
    
    switch ($aktion) {
        
        case "neu":
        // Formular für neuen Eintrag ausgeben
            formular_neuer_blacklist($neuer_blacklist);
            break;
        
        case "neu2":
        // Neuer Eintrag, 2. Schritt: Nick Prüfen
            $neuer_blacklist['u_nick'] = mysqli_real_escape_string($mysqli_link, $neuer_blacklist['u_nick']); // sec
            $query = "SELECT u_id FROM user WHERE u_nick = '$neuer_blacklist[u_nick]'";
            $result = mysqli_query($mysqli_link, $query);
            if ($result && mysqli_num_rows($result) == 1) {
            	$neuer_blacklist['u_id'] = mysqli_result($result, 0, 0);
                neuer_blacklist($u_id, $neuer_blacklist);
                unset($neuer_blacklist);
                $neuer_blacklist[] = "";
                formular_neuer_blacklist($neuer_blacklist);
                zeige_blacklist("normal", "", $sort);
            } elseif ($neuer_blacklist['u_nick'] == "") {
                echo "<B>Fehler:</B> Bitte geben Sie einen Nicknamen an!<BR>\n";
                formular_neuer_blacklist($neuer_blacklist);
            } else {
                echo "<B>Fehler:</B> Der Nickname '$neuer_blacklist[u_nick]' existiert nicht!<BR>\n";
                formular_neuer_blacklist($neuer_blacklist);
            }
            @mysqli_free_result($result);
            break;
        
        case "loesche":
        // Eintrag löschen
        
            if (isset($f_blacklistid) && is_array($f_blacklistid)) {
                // Mehrere Einträge löschen
                foreach ($f_blacklistid as $key => $loesche_id) {
                    loesche_blacklist($loesche_id);
                }
                
            } else {
                // Einen Eintrag löschen
                if (isset($f_blacklistid))
                    loesche_blacklist($f_blacklistid);
            }
            
            formular_neuer_blacklist($neuer_blacklist);
            zeige_blacklist("normal", "", $sort);
            break;
        
        default:
            formular_neuer_blacklist($neuer_blacklist);
            zeige_blacklist("normal", "", $sort);
    }
    
} else {
    echo "<P ALIGN=CENTER><B>Fehler:</B> Sie sind ausgelogt oder haben keine Berechtigung!</P>\n";
}

if ($o_js || !$u_id) :
    echo $f1
        . "<CENTER>[<A HREF=\"javascript:window.close();\">$t[sonst1]</A>]</CENTER>"
        . $f2 . "<BR>\n";
endif;

?>
</body>
</html>