<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

if (isset($eingabe_light_hilfe) && $eingabe_light_hilfe == "1"
    && ($reset <> "1") && (!$admin)) {
    echo "Sorry, deaktiviert";
    exit();
}

$title = $body_titel . ' - Info';
zeige_header_anfang($title, $farbe_mini_background, $grafik_mini_background, $farbe_mini_link, $farbe_mini_vlink);
?>
<script>
        window.focus()
        function win_reload(file,win_name) {
                win_name.location.href=file;
}
        function opener_reload(file,frame_number) {
                opener.parent.frames[frame_number].location.href=file;
}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

// Optional via JavaScript den oberen Werbeframe mit dem Werbeframe des Raums neu laden
if ($erweitertefeatures) {
    
    $query = "SELECT r_werbung FROM raum WHERE r_id=" . intval($o_raum);
    $result = mysqli_query($mysqli_link, $query);
    
    if ($result && mysqli_num_rows($result) != 0) {
    	$txt = mysqli_result($result, 0, 0);
        if (strlen($txt) > 7)
            $frame_online = $txt;
    }
    @mysqli_free_result($result);
}
// Frameset refreshen, falls reset=1, dann Fenster schliessen
if (isset($reset) && $reset && $o_js) {
    if (isset($forum) && $forum) {
        
        echo "<SCRIPT>";
        if ($frame_online != "")
            echo "opener_reload('$frame_online?http_host=$http_host','0');\n";
        echo "opener_reload('forum.php?http_host=$http_host&id=$id','1');\n"
            . "opener_reload('messages-forum.php?http_host=$http_host&id=$id','3');\n"
            . "opener_reload('interaktiv-forum.php?http_host=$http_host&id=$id','4');\n"
            . "window.close();\n" . "</SCRIPT>\n";
    } elseif ($u_level == "M") {
        echo "<SCRIPT>";
        if ($frame_online != "")
            echo "opener_reload('$frame_online?http_host=$http_host','0');\n";
        echo "opener_reload('chat.php?http_host=$http_host&id=$id&back=$chat_back','1');\n";
        if ($userframe_url) {
            echo "opener_reload('$userframe_url','2');\n";
        } else {
            echo "opener_reload('user.php?http_host=$http_host&id=$id&aktion=chatuserliste','2');\n";
        }
        echo "opener_reload('eingabe.php?http_host=$http_host&id=$id','3');\n"
            . "opener_reload('moderator.php?http_host=$http_host&id=$id','4');\n"
            . "opener_reload('interaktiv.php?http_host=$http_host&id=$id&o_raum_alt=$o_raum','5');\n"
            . "window.close();\n" . "</SCRIPT>\n";
    } else {
        echo "<SCRIPT>";
        if (isset($frame_online) && $frame_online != "")
            echo "opener_reload('$frame_online?http_host=$http_host','0');\n";
        echo "opener_reload('chat.php?http_host=$http_host&id=$id&back=$chat_back','1');\n";
        if (isset($userframe_url)) {
            echo "opener_reload('$userframe_url','2');\n";
        } else {
            echo "opener_reload('user.php?http_host=$http_host&id=$id&aktion=chatuserliste','2');\n";
        }
        echo "opener_reload('eingabe.php?http_host=$http_host&id=$id','3');\n"
            . "opener_reload('interaktiv.php?http_host=$http_host&id=$id&o_raum_alt=$o_raum','4');\n"
            . "window.close();\n" . "</SCRIPT>\n";
    }
}

// Chat bei u_backup neu aufbauen, damit nach Umstellung der Chat refresht wird
// u_backup in DB eintragen
if (strlen($u_id) > 0 && isset($f['u_backup']) && strlen($f['u_backup']) > 0) {
    unset($f['u_id']);
    unset($f['u_level']);
    unset($f['u_name']);
    unset($f['u_nick']);
    unset($f['u_auth']);
    unset($f['u_passwort']);
    schreibe_db("user", $f, $u_id, "u_id");
    if ($f['u_backup'] == 1)
        warnung($u_id, $u_nick, "sicherer_modus");
    if ($o_js) {
        echo "<SCRIPT LANGUAGE=JavaScript>"
            . "opener_reload('chat.php?http_host=$http_host&id=$id&back=$chat_back','1')\n"
            . "opener_reload('eingabe.php?http_host=$http_host&id=$id','3')"
            . "</SCRIPT>\n";
    }
}

// Menü als erstes ausgeben
$box = $ft0 . $t['menue4'] . $ft1;
$text = "<A HREF=\"hilfe.php?http_host=$http_host&id=$id\">$t[menue1]</A>\n"
    . "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=befehle\">$t[menue2]</A>\n"
    . "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=sprueche\">$t[menue3]</A>\n";
if ($communityfeatures) {
    $text .= "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=legende\">$t[menue6]</A>\n";
    $text .= "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community\">$t[menue7b]</A>\n";
}
$text .= "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=chatiquette\">$t[menue5]</A>\n";
if ($erweitertefeatures) {
    $text .= "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=agb\">$t[menue8]</A>\n";
}
if ($zeige_datenschutz) {
    $text .= "| <A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=privacy\">$t[menue9]</A>\n";
}
if ($aktion != "logout") {
    show_box2($box, $text, "100%");
    ?>
	<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
	<?php
}

switch ($aktion) {
    
    case "befehle":
    // Erklärung zu den Befehlen
        $box = $ft0 . $t['hilfe0'] . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
        echo "<TR><TD>";
        echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
        echo "<span style=\"font-size: small; color:$farbe_text;\"><b>$box</b></span>\n";
        echo "</TD></TR></TABLE>\n";
        
        // Tabelle ausgeben
        
        reset($hilfe_befehlstext);
        $anzahl = count($hilfe_befehlstext);
        $i = 0;
        $bgcolor = $farbe_tabelle_zeile1;
        
        // Tablelle ausgeben
        echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=4 CELLSPACING=0>\n";
        echo "<TR><TD COLSPAN=4 ALIGN=CENTER>$t[hilfe1]</CENTER></TD></TR>";
        echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\">$t[hilfe2]</TR>\n";
        
        while ($i < $anzahl) {
            $spname = key($hilfe_befehlstext);
            $spruchtmp = preg_split("/\t/", $hilfe_befehlstext[$spname], 4);
            if (!isset($spruchtmp[0]))
                $spruchtmp[0] = "&nbsp;";
            if (!isset($spruchtmp[1]))
                $spruchtmp[1] = "&nbsp;";
            if (!isset($spruchtmp[2]))
                $spruchtmp[2] = "&nbsp;";
            if (!isset($spruchtmp[3]))
                $spruchtmp[3] = "&nbsp;";
            echo "<TR BGCOLOR=\"$bgcolor\">" . "<TD>" . $f1
                . "&nbsp;<b>$spruchtmp[0]</b>" . $f2 . "</TD>\n" . "<TD>" . $f1
                . "$spruchtmp[1]" . $f2 . "</TD>\n" . "<TD>" . $f1
                . "$spruchtmp[2]" . $f2 . "</TD>\n" . "<TD>" . $f1
                . "$spruchtmp[3]" . $f2 . "</TD>\n" . "</TR>";
            next($hilfe_befehlstext);
            
            // Farben umschalten
            if (($i % 2) > 0) {
                $bgcolor = $farbe_tabelle_zeile1;
            } else {
                $bgcolor = $farbe_tabelle_zeile2;
            }
            
            $i++;
        }
        echo "</TABLE>\n";
        
        if ($admin && $hilfe_befehlstext_admin_ok == 1) {
            
            reset($hilfe_befehlstext_admin);
            $anzahl = count($hilfe_befehlstext_admin);
            $i = 0;
            $bgcolor = $farbe_tabelle_zeile1;
            
            // Tablelle ausgeben
            echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=4 CELLSPACING=0>\n";
            echo "<TR><TD COLSPAN=4 ALIGN=CENTER><b>$t[hilfe8]</b></CENTER></TD></TR>";
            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\">$t[hilfe2]</TR>\n";
            
            while ($i < $anzahl) {
                $spname = key($hilfe_befehlstext_admin);
                $spruchtmp = preg_split("/\t/",
                    $hilfe_befehlstext_admin[$spname], 4);
                if (!isset($spruchtmp[0]))
                    $spruchtmp[0] = "&nbsp;";
                if (!isset($spruchtmp[1]))
                    $spruchtmp[1] = "&nbsp;";
                if (!isset($spruchtmp[2]))
                    $spruchtmp[2] = "&nbsp;";
                if (!isset($spruchtmp[3]))
                    $spruchtmp[3] = "&nbsp;";
                echo "<TR BGCOLOR=\"$bgcolor\">" . "<TD>" . $f1
                    . "&nbsp;<b>$spruchtmp[0]</b>" . $f2 . "</TD>\n" . "<TD>"
                    . $f1 . "$spruchtmp[1]" . $f2 . "</TD>\n" . "<TD>" . $f1
                    . "$spruchtmp[2]" . $f2 . "</TD>\n" . "<TD>" . $f1
                    . "$spruchtmp[3]" . $f2 . "</TD>\n" . "</TR>";
                next($hilfe_befehlstext_admin);
                
                // Farben umschalten
                if (($i % 2) > 0) {
                    $bgcolor = $farbe_tabelle_zeile1;
                } else {
                    $bgcolor = $farbe_tabelle_zeile2;
                }
                
                $i++;
            }
            echo "</TABLE>\n";
        }
        
        break;
    
    case "sprueche":
    // Erklärung zu den Sprüchen
        $box = $ft0 . $t['hilfe3'] . $ft1;
        show_box2($box, $hilfe_spruchtext, "100%");
        ?>
		<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
		<?php
        
        // Liste mit Sprüchen ausgeben
        $box = $ft0 . $t['hilfe4'] . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
        echo "<TR><TD>";
        echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
        echo "<span style=\"font-size: small; color:$farbe_text;\"><b>$box</b></span>\n";
        echo "</TD></TR></TABLE>\n";
        
        // Sprüche in Array einlesen
        $spruchliste = file("conf/$datei_spruchliste");
        
        reset($spruchliste);
        $anzahl = count($spruchliste);
        $i = 0;
        $bgcolor = $farbe_tabelle_zeile1;
        
        // Tablelle ausgeben
        echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=4 CELLSPACING=0><TR BGCOLOR=\"$farbe_tabelle_kopf2\">"
            . "$t[hilfe5]</TR></TABLE>\n";
        
        while ($i < $anzahl) {
            $spname = key($spruchliste);
            $spruchtmp = preg_split("/\t/",
                substr($spruchliste[$spname], 0,
                    strlen($spruchliste[$spname]) - 1), 3);
            
            $spruchtmp[2] = str_replace("<", "&lt;", $spruchtmp[2]);
            $spruchtmp[2] = str_replace(">", "&gt;", $spruchtmp[2]);
            $spruchtmp[2] = preg_replace('|\*(.*?)\*|', '<i>\1</i>',
                preg_replace('|_(.*?)_|', '<b>\1</b>', $spruchtmp[2]));
            
            echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=4 CELLSPACING=0><TR BGCOLOR=\"$bgcolor\">"
                . "<TD WIDTH=15%>" . $f1 . "&nbsp;<b>$spruchtmp[0]</b>" . $f2
                . "</TD>\n" . "<TD ALIGN=CENTER WIDTH=10%>" . $f1
                . "<b>$spruchtmp[1]</b>" . $f2 . "</TD>\n" . "<TD WIDTH=75%>"
                . $f1 . "&lt;$spruchtmp[2]&gt;" . $f2 . "</TD>\n"
                . "</TR></TABLE>\n";
            next($spruchliste);
            
            // Farben umschalten
            if (($i % 2) > 0) {
                $bgcolor = $farbe_tabelle_zeile1;
            } else {
                $bgcolor = $farbe_tabelle_zeile2;
            }
            
            $i++;
        }
        
        echo "</TABLE>\n";
        break;
    
    case "legende":
        $box = $ft0 . $t['hilfe10'] . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
        echo "<TR><TD>";
        echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
        echo "<span style=\"font-size: small; color:$farbe_text;\"><b>$box</b></span>\n";
        echo "</TD></TR></TABLE>\n";
        
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n"
            . $legende . "</table><br>\n"
            . "<A HREF=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#punkte\">$t[hilfe12]</A><br>\n";
        ?>
		<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
		<?php
        break;
    
    case "community":
        $box = $ft0 . $t['hilfe11'] . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
        echo "<TR><TD>";
        echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
        echo "<span style=\"font-size: small; color:$farbe_text;\"><b>$box</b></span>\n";
        echo "</TD></TR></TABLE>\n";
        
        echo $hilfe_community . "\n";
        ?>
		<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
		<?php
        break;
    
    case "agb":
        include("conf/" . $sprachconfig . "-index.php");
        if (isset($extra_agb) and ($extra_agb <> "")
            and ($extra_agb <> "standard"))
            $t['agb'] = $extra_agb;
        $box = $ft0 . $t['hilfe14'] . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
        echo "<TR><TD>";
        echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
        echo "<span style=\"font-size: small; color:$farbe_text;\"><b>$box</b></span>\n";
        echo "</TD></TR></TABLE>\n";
        
        echo $t['agb'] . "\n";
        ?>
		<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
		<?php
        break;
    
    case "privacy":
        $box = $ft0 . $t['hilfe13'] . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
        echo "<TR><TD>";
        echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
        echo "<span style=\"font-size: small; color:$farbe_text;\"><b>$box</b></span>\n";
        echo "</TD></TR></TABLE>\n";
        
        echo $hilfe_privacy . "\n";
        ?>
		<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
		<?php
        break;
    
    case "chatiquette":
        $box = $ft0 . $t['hilfe9'] . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
        echo "<TR><TD>";
        echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
        echo "<span style=\"font-size: small; color:$farbe_text;\"><b>$box</b></span>\n";
        echo "</TD></TR></TABLE>\n";
        
        echo "$chatiquette\n";
        echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><br>\n";
        break;
    
    case "logout":
        $box = $ft0 . $t['hilfe15'] . $ft1;
        $text = str_replace("%zeit%", $chat_timeout / 60, $t['hilfe16']);
        show_box2($box, $text, "100%");
        
        echo "<br><br>";
        break;
    
    default;
        $box = $ft0 . $t['hilfe6'] . $ft1;
        echo "<TABLE CELLPADDING=2 CELLSPACING=0 BORDER=0 WIDTH=100% BGCOLOR=$farbe_tabelle_kopf>\n";
        echo "<TR><TD>";
        echo "<a href=\"javascript:window.close();\"><img src=\"pics/button-x.gif\" alt=\"schließen\" style=\"width:15px; height:13px; float: right; border:0px;\"></a>\n";
        echo "<span style=\"font-size: small; color:$farbe_text;\"><b>$box</b></span>\n";
        echo "</TD></TR></TABLE>\n";
        
        echo "$hilfe_uebersichtstext\n";
        ?>
		<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
		<?php
}

echo "<P>$t[hilfe7]</P>\n";
?>
<div style="text-align:center;">[<a href="javascript:window.close();"><?php echo $t['sonst1']; ?></a>]</div>
<br>
</body>
</html>