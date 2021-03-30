<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel . "_Aktionen"; ?></title>
<meta charset="utf-8">
<script>
        window.focus()
        function win_reload(file,win_name) {
                win_name.location.href=file;
}
        function opener_reload(file,frame_number) {
                opener.parent.frames[frame_number].location.href=file;
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

$eingabe_breite = 70;

if ($u_id && $communityfeatures) {
    
    // Menü als erstes ausgeben
    $box = $ft0 . "Menü Aktionen" . $ft1;
    $text = "<A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#home\">Hilfe</A>\n";
    show_box2($box, $text, "100%");
    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
    
    switch ($aktion) {
        
        case "eintragen":
        // Ab in die Datenbank mit dem Eintrag
            eintrag_aktionen($aktion_datensatz);
            zeige_aktionen("normal");
            break;
        
        default:
            zeige_aktionen("normal");
    }
    
    echo "<P><B>Tipps zur den Aktionen:</B></P>"
        . "<P>Mit den Aktionen steuern Sie, bei welchen Ereignissen Sie mit "
        . "welcher Nachricht informiert werden wollen. Ein Ereignis ist z.B. "
        . "der Logon oder Logout eines <B>Freundes</B>, oder auch der Eingang einer "
        . "neuen <B>Mail</B>.</P>"
        . "<P>Wann Sie die Nachricht erhalten wollen, wählen Sie aus der obersten Zeile aus. "
        . "Möglich ist der Empfang sofort wenn Sie online sind (<B>Sofort/Online</B>) "
        . "(z.B. <B>Freund</B> loggt ein/aus "
        . "oder zu dem Moment, in dem Sie eine <B>Neue Mail</B> erhalten), bei Ihrem "
        . "<B>Login</B> in den Chat oder regelmäßig "
        . "<B>alle 5 Minuten</B> (regelmäßige Information über die vorliegenden "
        . "<B>Neuen Mails</B> oder die Anwesendheit Ihrer <B>Freunde</B> im Chat)</P>"
        . "<P>Die Benachrichtungen, die Sie erhalten, wenn Sie nicht im Chat sind (offline), "
        . "wählen Sie unter <B>Sofort/Offline</B> aus.</P>"
        . "<P>Die Art der Nachricht ist einstellbar, so gibt es <B>keine</B> Benachrichtigung, "
        . "<B>Chat-Mail</B> (chat-interne Mail), eine <B>E-Mail</B> an Ihre nicht-öffentliche "
        . "E-Mail Adresse oder eine <B>OLM</B> (OnLineMessage, direkte Nachricht in Chat wie "
        . "/msg)."
        . "Zusätzlich sind auch Kombinationen von <B>E-Mail und OLM</B> sowie <B>Chat-Mail "
        . "und OLM</B> möglich, wobei Sie in diesem Fall zwei Nachrichten erhalten.</P>";
}

if ($o_js || !$u_id) {
    echo $f1
        . "<CENTER>[<A HREF=\"javascript:window.close();\">$t[sonst1]</A>]</CENTER>"
        . $f2 . "<BR>\n";
}

?>
</body>
</html>