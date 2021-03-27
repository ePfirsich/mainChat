<?php

// eingabe.php muss mit id=$hash_id aufgerufen werden

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js
id_lese($id);

if ($u_id) {
    
    // Ggf Farbe aktualisieren
    if (isset($farbe)) {
        if (strlen($farbe) == 6) {
            if (preg_match("/[a-f0-9]{6}/i", $farbe)) {
                $u_farbe = $farbe;
                $f['u_farbe'] = substr(htmlspecialchars($farbe), 0, 6);
                schreibe_db("user", $f, $u_id, "u_id");
                system_msg("", 0, $u_id, $system_farbe,
                    "<B>$chat:</B> " . "<FONT COLOR=\"#$u_farbe\">"
                        . str_replace("%u_nick%", $u_nick, $t['farbe1'])
                        . "</FONT>");
            }
        }
    }
    
    // Default für Farbe setzen, falls undefiniert
    if (!isset($u_farbe))
        $u_farbe = $user_farbe;
    
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
<HTML>
<HEAD><TITLE><?php echo $body_titel; ?></TITLE><META CHARSET=UTF-8>
<SCRIPT>
function resetinput() {
    document.forms['form'].elements['text'].value=document.forms['form'].elements['text2'].value;
<?php
    if ($u_clearedit == 1) {
        echo "    document.forms['form'].elements['text2'].value='';\n";
    }
?>
    document.forms['form'].submit();
    document.forms['form'].elements['text2'].focus();
    document.forms['form'].elements['text2'].select();
}
function neuesFenster(url) { 
	hWnd=window.open(url,"<?php echo $fenster; ?>","resizable=yes,scrollbars=yes,width=300,height=580"); 
}
function neuesFenster2(url) { 
	hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580"); 
}
function neuesFenster3(url) { 
	hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,menu=yes,width=780,height=580"); 
}
function window_reload(file,win_name) {
	win_name.location.href=file;
}
</SCRIPT>
<?php
    echo $stylesheet;
    echo "</HEAD>\n";
    
    // Body-Tag definieren
    $body_tag = "<BODY BGCOLOR=\"$farbe_chat_background2\" ";
    if (strlen($grafik_background2) > 0) {
        $body_tag = $body_tag . "BACKGROUND=\"$grafik_background2\" ";
    }
    $body_tag = $body_tag . "TEXT=\"$farbe_chat_text2\" "
        . "LINK=\"$farbe_chat_link2\" " . "VLINK=\"$farbe_chat_vlink2\" "
        . "ALINK=\"$farbe_chat_vlink2\">\n";
    
    echo $body_tag;
    
    // Eingabeformular mit Menu und Farbauswahl
    reset($farbe_chat_user);
    $i = 0;
    echo "<FORM NAME=\"form\" METHOD=POST TARGET=\"schreibe\" ACTION=\"schreibe.php\" onSubmit=\"resetinput(); return false;\">";
    
    echo "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0><TR>"
        . "<TD>&nbsp;</TD><TD COLSPAN=" . (count($farbe_chat_user) + 3) . ">";
    
    // Typ Eingabefeld für Chateingabe setzen
    if ($u_level == "M") {
        $text2_typ = "<TEXTAREA ROWS=\"4\" NAME=\"text2\" COLS=\""
            . $chat_eingabe_breite . "\"></TEXTAREA>";
    } else {
        $text2_typ = "<INPUT TYPE=\"TEXT\" NAME=\"text2\" maxlength=\""
            . ($chat_max_eingabe - 1) . "\" VALUE=\"\" SIZE=\""
            . $chat_eingabe_breite . "\""
    		. (isset($eingabe_inaktiv_autocomplete) && $eingabe_inaktiv_autocomplete == "1" ? "autocomplete=\"off\"" : "")
            . ">";
    }
    
    // Unterscheidung Normal oder sicherer Modus
    if ($backup_chat || $u_backup) {
        
        // Beim Netscape Eingabezeile schmäler setzen
        if (ist_netscape()) {
            $chat_eingabe_breite = floor($chat_eingabe_breite / 2) - 7;
            $mindestbreite = 18;
        } else {
            $chat_eingabe_breite = $chat_eingabe_breite - 11;
            $mindestbreite = 44;
        }
        
        // Bei zu schmaler Eingabenzeilen diese für rundes Layout auf Mindesbreite setzen
        if ($chat_eingabe_breite < $mindestbreite) {
            $chat_eingabe_breite = $mindestbreite;
        }
        
        echo $text2_typ . "<INPUT NAME=\"text\" VALUE=\"\" TYPE=\"HIDDEN\">"
            . "<SELECT NAME=\"user_chat_back\">\n";
        for ($i = 5; $i < 40; $i++) {
            echo "<OPTION " . ($chat_back == $i ? "SELECTED" : "")
                . " VALUE=\"$i\">$i&nbsp;$t[eingabe1]\n";
        }
        echo "</SELECT>"
            . "<INPUT NAME=\"http_host\" VALUE=\"$http_host\" TYPE=\"HIDDEN\">"
            . "<INPUT NAME=\"id\" VALUE=\"$id\" TYPE=\"HIDDEN\">"
            . "<INPUT NAME=\"u_backup\" VALUE=\"$u_backup\" TYPE=\"HIDDEN\">"
            . "<INPUT NAME=\"u_level\" VALUE=\"$u_level\" TYPE=\"HIDDEN\">"
            . $f1 . "<INPUT TYPE=\"SUBMIT\" VALUE=\"Go!\">" . $f2;
        echo "</TD></TR>\n";
    } else {
        
        // Beim Netscape Eingabezeile schmäler setzen
        if (ist_netscape()) {
            $chat_eingabe_breite = floor($chat_eingabe_breite / 2);
            $mindestbreite = 25;
        } else {
            $mindestbreite = 55;
        }
        
        // Bei zu schmaler Eingabenzeilen diese für rundes Layout auf Mindesbreite setzen
        if ($chat_eingabe_breite < $mindestbreite)
            $chat_eingabe_breite = $mindestbreite;
        
        echo $text2_typ . "<INPUT NAME=\"text\" VALUE=\"\" TYPE=\"HIDDEN\">"
            . "<INPUT NAME=\"id\" VALUE=\"$id\" TYPE=\"HIDDEN\">"
            . "<INPUT NAME=\"http_host\" VALUE=\"$http_host\" TYPE=\"HIDDEN\">"
            . "<INPUT NAME=\"u_backup\" VALUE=\"$u_backup\" TYPE=\"HIDDEN\">"
            . "<INPUT NAME=\"u_level\" VALUE=\"$u_level\" TYPE=\"HIDDEN\">"
            . $f1 . "<INPUT TYPE=\"SUBMIT\" VALUE=\"Go!\">" . $f2;
        echo "</TD></TR>\n";
    }
    
    $mlnk[4] = "hilfe.php?http_host=$http_host&id=$id";
    $mlnk[1] = "raum.php?http_host=$http_host&id=$id";
    $mlnk[2] = "user.php?http_host=$http_host&id=$id";
    $mlnk[3] = "edit.php?http_host=$http_host&id=$id";
    $mlnk[5] = "sperre.php?http_host=$http_host&id=$id";
    $mlnk[6] = "index.php?http_host=$http_host&id=$id&aktion=logoff";
    $mlnk[7] = "log.php?http_host=$http_host&id=$id&back=500";
    $mlnk[8] = "moderator.php?http_host=$http_host&id=$id&mode=answer";
    $mlnk[9] = "hilfe.php?http_host=$http_host&id=$id&reset=1";
    $mlnk[10] = "mail.php?http_host=$http_host&id=$id";
    $mlnk[11] = "index-forum.php?http_host=$http_host&id=$id";
    $mlnk[12] = "blacklist.php?http_host=$http_host&id=$id";
    $mlnk[13] = "umfrage.php?http_host=$http_host&id=$id";
    
    if ($admin) {
        $eingabe_light = 0;
        $eingabe_light_log = 0;
        $eingabe_light_hilfe = 0;
    }
    
    $umfrageanzeigen = 0;
    if ($umfragefeatures && $communityfeatures) {
        // Sind Umfragen vorhanden, nur dann umfragen anzeigen
        $query = "SELECT um_id FROM umfrage " . "WHERE um_start <= '"
            . date('Y-m-d H:i:s') . "'" . "  and um_ende >= '"
            . date('Y-m-d H:i:s') . "'";
        $result = mysql_query($query, $conn);
        if ($result && mysqli_num_rows($result) > 0) {
            $umfrageanzeigen = 1;
            mysqli_free_result($result);
        }
    }
    
    if ($forumfeatures && $communityfeatures) {
        // Raumstatus lesen, für Temporär, damit FORUM nicht angezeigt wird
        
        $query = "SELECT r_status1 from raum WHERE r_id=" . intval($o_raum);
        $result = mysql_query($query, $conn);
        if ($result && mysqli_num_rows($result) == 1) {
            $aktraum = mysqli_fetch_object($result);
            mysqli_free_result($result);
        }
    }
    
    // Code funktioniert mit und ohne javascript
    echo "<TR><TD></TD><TD><B>" . $f1;
    if (!isset($eingabe_light_hilfe) || !$eingabe_light_hilfe)
        echo "[<A HREF=\"$mlnk[4]\" TARGET=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[4]');return(false)\">$t[menue4]</A>]&nbsp;";
    if (!isset($eingabe_light) || !$eingabe_light)
        if (!isset($beichtstuhl) || !$beichtstuhl || $admin)
            echo "[<A HREF=\"$mlnk[1]\" TARGET=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[1]');return(false)\">$t[menue1]</A>]&nbsp;"
                . "[<A HREF=\"$mlnk[2]\" TARGET=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[2]');return(false)\">$t[menue2]</A>]&nbsp;";
    if (!isset($eingabe_light) || !$eingabe_light)
        if ($communityfeatures
            && (!isset($beichtstuhl) || !$beichtstuhl || $admin))
            echo "[<A HREF=\"$mlnk[10]\" TARGET=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[10]');return(false)\">$t[menue10]</A>]&nbsp;";
    if (!isset($eingabe_light) || !$eingabe_light)
        if ($forumfeatures && $communityfeatures
            && (!isset($beichtstuhl) || !$beichtstuhl || $admin)
            && ((isset($aktraum) && $aktraum->r_status1 <> "L") || $admin))
            echo "[<A HREF=\"$mlnk[11]\" onMouseOver=\"return(true)\" TARGET=\"topframe\">$t[menue11]</A>]&nbsp;";
    if (!isset($eingabe_light) || !$eingabe_light)
        if ($umfrageanzeigen && (!$beichtstuhl || $admin))
            echo "[<A HREF=\"$mlnk[13]\" TARGET=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mlnk[13]');return(false)\">$t[menue13]</A>]&nbsp;";
    if (!isset($eingabe_light) || !$eingabe_light)
        if (!isset($beichtstuhl) || !$beichtstuhl || $admin)
            echo "[<A HREF=\"$mlnk[3]\" TARGET=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$mlnk[3]');return(false)\">$t[menue3]</A>]&nbsp;";
    if (!isset($eingabe_light) || !$eingabe_light)
        if ($admin)
            echo "[<A HREF=\"$mlnk[5]\" TARGET=\"640_$fenster\" onClick=\"neuesFenster2('$mlnk[5]');return(false)\">$t[menue5]</A>]&nbsp;";
    if ($admin && $communityfeatures)
        echo "[<A HREF=\"$mlnk[12]\" TARGET=\"640_$fenster\" onClick=\"neuesFenster2('$mlnk[12]');return(false)\">$t[menue12]</A>]&nbsp;";
    if ($u_level == "M")
        echo "[<A HREF=\"$mlnk[8]\" TARGET=\"$fenster\" onClick=\"neuesFenster2('$mlnk[8]');return(false)\">$t[menue8]</A>]&nbsp;";
    if (!isset($eingabe_light_log) || !$eingabe_light_log)
        echo "[<A HREF=\"$mlnk[7]\" TARGET=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster3('$mlnk[7]');return(false)\">$t[menue7]</A>]&nbsp;";
    if ($o_js)
        echo "[<A HREF=\"$mlnk[9]\" TARGET=\"$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster3('$mlnk[9]');return(false)\">$t[menue9]</A>]&nbsp;";
    echo "&nbsp;&nbsp;";
    if (isset($chat_logout_url)) {
        echo "[<A HREF=\"$mlnk[6]\" onMouseOver=\"return(true)\" TARGET=\"_top\">$t[menue6]</A>]&nbsp;"
            . $f2 . "</B></TD>";
    } else {
        echo "[<A HREF=\"$mlnk[6]\" onMouseOver=\"return(true)\" TARGET=\"topframe\">$t[menue6]</A>]&nbsp;"
            . $f2 . "</B></TD>";
    }
    
    unset($aktraum);
    
    if ((!isset($eingabe_light) || !$eingabe_light)
        && (!isset($eingabe_light_farbe) || !$eingabe_light_farbe) && (!$admin)) {
        echo "<TD ALIGN=RIGHT><FONT SIZE=-1 COLOR=\"#" . $u_farbe
            . "\"><B>$t[farbe2]</B>&nbsp;</FONT></TD>";
        for (@reset($farbe_chat_user); list($nummer, $ufarbe) = each(
            $farbe_chat_user);) {
            echo "<TD BGCOLOR=\"#$ufarbe\">"
                . "<A onMouseOver=\"return(true)\" HREF=\"eingabe.php?http_host=$http_host&id=$id&farbe=$ufarbe\">"
                . "<IMG SRC=\"pics/fuell.gif\" WIDTH=$farbe_chat_user_groesse "
                . "HEIGHT=$farbe_chat_user_groesse ALT=\"\" BORDER=0></A></TD>\n";
        }
        echo "<TD><IMG SRC=\"pics/fuell.gif\" WIDTH=$farbe_chat_user_groesse "
            . "HEIGHT=$farbe_chat_user_groesse ALT=\"\" BORDER=0></TD>";
    }
    echo "</TR></TABLE>";
    
    echo "</FORM>\n";
    
} else {
    echo "<BODY onLoad='parent.location.href=\"index.php?http_host=$http_host\"'>\n";
    echo "</BODY></HTML>\n";
    exit;
}

?>

</BODY></HTML>
