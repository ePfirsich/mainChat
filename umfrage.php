<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

// Sonderzeichen aus dem Target raus

$fenster = str_replace("+", "", $u_nick);
$fenster = str_replace("-", "", $fenster);
$fenster = str_replace("ä", "", $fenster);
$fenster = str_replace("ö", "", $fenster);
$fenster = str_replace("ü", "", $fenster);
$fenster = str_replace("Ä", "", $fenster);
$fenster = str_replace("Ö", "", $fenster);
$fenster = str_replace("Ü", "", $fenster);
$fenster = str_replace("ß", "", $fenster);

// Kopf ausgeben
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel . "_Umfrage"; ?></title>
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
                for(i=0; i<document.forms["mailbox"].elements.length; i++) {
                     e = document.forms["mailbox"].elements[i];
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

$admin = (($u_level == "C") || ($u_level == "S"));

// Browser prüfen
if (ist_netscape()) {
    $eingabe_breite = 40;
    $eingabe_breite1 = 49;
    $eingabe_breite2 = 50;
} else {
    $eingabe_breite = 55;
    $eingabe_breite1 = 87;
    $eingabe_breite2 = 75;
}

if ($u_id && $communityfeatures && $u_level != "G") {
    // Menü als erstes ausgeben
    $box = $ft0 . "Menü Umfrage" . $ft1;
    
    if (isset($adminuebersicht) && $adminuebersicht == "1" && $admin) {
        $urlzusatz = "&adminuebersicht=1";
    } else {
        $urlzusatz = "";
        $adminuebersicht = 0;
    }
    
    $text = "<A HREF=\"umfrage.php?http_host=$http_host&id=$id$urlzusatz&aktion=\">Übersicht</A>\n|\n"
        . "<A HREF=\"umfrage.php?http_host=$http_host&id=$id$urlzusatz&aktion=umfragen_aktuell\">Aktuelle Umfragen</A>\n|\n"
        . "<A HREF=\"umfrage.php?http_host=$http_host&id=$id$urlzusatz&aktion=umfragen_zukunft\">Zukünftige Umfragen</A>\n|\n"
        . "<A HREF=\"umfrage.php?http_host=$http_host&id=$id$urlzusatz&aktion=umfragen_abgeschlossen\">Abgeschlossene Umfragen</A>\n|\n"
        . "<A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community\">Hilfe</A>\n";
    
    show_box2($box, $text, "100%");
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
    
    switch ($aktion) {
        
        case "neu":
        case "aendern":
            if ($adminuebersicht == "1") {
                umfrage_aendern($umfrage);
            }
            break;
        
        case "umfrage":
        // berechtigungen prüfen
        // zeiten 1-4
        // punkte funktion, da link nicht angezeigt werden sollte
        // erster login funktion, da link nicht angezeigt werden sollte
        // $umfrage auf integer testen
        
            umfrage($umfrage);
            
            break;
        
        case "umfragen_aktuell":
            anzeige_umfragen_aktuell($adminuebersicht);
            break;
        
        case "umfragen_zukunft":
            anzeige_umfragen_zukuenftig($adminuebersicht);
            break;
        
        case "umfragen_abgeschlossen":
            anzeige_umfragen_abgeschlossen($adminuebersicht);
            break;
        
        default:
            anzeige_umfragen_aktuell($adminuebersicht);
            anzeige_umfragen_zukuenftig($adminuebersicht);
            anzeige_umfragen_abgeschlossen($adminuebersicht);
    }
    
} elseif ($u_level == "G") {
    echo "<P><B>Fehler:</B> Als Gast stehen Ihnen die Umfragen nicht zur Verfügung.</P>";
} else {
    echo "<P><B>Fehler:</B> Beim Aufruf dieser Seite ist ein Fehler aufgetreten.</P>";
}

if ($o_js || !$u_id) {
    echo "<BR />" . $f1
        . "<CENTER>[<A HREF=\"javascript:window.close();\">$t[sonst1]</A>]</CENTER>"
        . $f2 . "<BR>\n";
}

?>
</body>
</html>