<?php

//Kopf fuer das Forum
function kopf_forum($admin)
{
    
    global $http_host, $id, $u_nick, $menue;
    global $f1, $f2, $farbe_chat_background1, $grafik_background1, $farbe_chat_text1, $farbe_chat_link1;
    global $farbe_chat_vlink1, $stylesheet, $chat;
    global $aktion;
    
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
	<title><?php echo (isset($body_titel) ? $body_titel : ""); ?></title>
	<meta charset="utf-8">
	<style type="text/css">
	<?php echo $stylesheet; ?>
	body {
		background-color:<?php echo $farbe_chat_background1; ?>;
	<?php if(strlen($grafik_background1) > 0) { ?>
		background-image:<?php echo $grafik_background1; ?>;
	<?php } ?>
	}
	a, a:link {
		color:<?php echo $farbe_chat_link1; ?>;
	}
	a:visited, a:active {
		color:<?php echo $farbe_chat_vlink1; ?>;
	}
	</style>
	<script>
	function ask(text) {
			return(confirm(text));
	}
	function neuesFenster(url) {
			hWnd=window.open(url,"<?php echo $fenster; ?>","resizable=yes,scrollbars=yes,width=300,height=580");
	}
	function neuesFenster2(url) {
			hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580");
	}
	</script>
	</head>
	<body>
	<?php
    // zeige_kopf();
    
    if (($admin) && (!$aktion)) {
        $lnk[1] = $f1
            . "&nbsp;[<A HREF=\"forum.php?http_host=$http_host&id=$id&aktion=forum_neu\">$menue[1]</A>]"
            . $f2;
    }
    
    if (!isset($lnk[1]))
        $lnk[1] = "";
    
    echo "<center><table width=\"760\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n";
    echo "<tr><td><B><a href=\"forum.php?id=$id&http_host=$http_host\">"
        . $chat . "-Forum</A>" . $lnk[1] . "</b></td>\n";
    echo "</table>\n";
    
}

//Fuss für das Forum
function fuss_forum()
{
    echo "<br><br></center>";
    // zeige_fuss();
}

//Zeigt fehlende Eingaben an
function show_missing($missing)
{
    
    global $farbe_hervorhebung_forum;
    
    echo "<p><b><font color=\"$farbe_hervorhebung_forum\">$missing</font></b></p>";
    
}

//Eingabemaske für neues Forum
function maske_forum($fo_id = 0)
{
    
    global $id, $http_host, $mysqli_link;
    global $f1, $f2, $farbe_tabelle_kopf, $farbe_tabelle_kopf2, $farbe_tabellenrahmen;
    global $t, $farbe_text;
    
    if ($fo_id > 0) {
        $fo_id = intval($fo_id);
        $sql = "select fo_name, fo_admin from forum where fo_id=$fo_id";
        $query = mysqli_query($mysqli_link, $sql);
        $fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
        $fo_admin = mysqli_result($query, 0, "fo_admin");
        @mysqli_free_result($query);
        
        $kopfzeile = str_replace("xxx", $fo_name, $t['forum_edit']);
        $button = $t['forum_edit_button'];
        
    } else {
        
        $kopfzeile = $t['forum_neu'];
        $button = $t['forum_button'];
        
    }
    
    echo "<form action=\"forum.php\" method=\"post\">";
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\"><tr><td>";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">";
    echo "<tr bgcolor=\"$farbe_tabelle_kopf\"><td colspan=\"2\"><DIV style=\"color:$farbe_text; font-weight:bold;\">$kopfzeile</td></tr>\n";
    echo "</table></td></tr><tr><td>";
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\" bgcolor=\"$farbe_tabelle_kopf2\">";
    echo "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";
    echo "<tr><td width=\"260\">$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[forum_msg1]</DIV> $f2</td>";
    echo "<td width=500><input type=\"text\" size=\"50\" name=\"fo_name\" value=\"$fo_name\"></td></tr>";
    
    // Forumsrechte für Gast einstellen
    echo "<tr><td>$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[forum_msg3]</DIV> $f2</td>";
    if (($fo_admin & 8) == 8) {
        $selg1 = "SELECTED";
        $selg2 = "";
    }
    if (($fo_admin & 16) == 16) {
        $selg1 = "";
        $selg2 = "SELECTED";
    }
    
    echo "<td><select size=\"1\" name=\"fo_gast\">";
    echo "<option value=\"0\">$t[forum_msg7]\n";
    echo "<option value=\"8\" $selg1>$t[forum_msg5]\n";
    echo "<option value=\"24\" $selg2>$t[forum_msg6]\n";
    echo "</select></td></tr>\n";
    
    if (($fo_admin & 2) == 2) {
        $selu1 = "SELECTED";
        $selu2 = "";
    }
    if (($fo_admin & 4) == 4) {
        $selu1 = "";
        $selu2 = "SELECTED";
    }
    // Forumsrechte für einen User einstellen
    echo "<tr><td>$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[forum_msg4]</DIV> $f2</td>";
    echo "<td><select size=\"1\" name=\"fo_user\">";
    echo "<option value=\"0\">$t[forum_msg7]\n";
    echo "<option value=\"2\" $selu1>$t[forum_msg5]\n";
    echo "<option value=\"6\" $selu2>$t[forum_msg6]\n";
    echo "</select></td></tr>\n";
    
    echo "<tr style=\"color:$farbe_text;\"><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"$button\"></td></tr>";
    echo "</table></td></tr></table>\n";
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
    echo "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">";
    if ($fo_id > 0) {
        echo "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
        echo "<input type=\"hidden\" name=\"aktion\" value=\"forum_editieren\">";
    } else {
        echo "<input type=\"hidden\" name=\"aktion\" value=\"forum_anlegen\">";
    }
    
    echo "</form>\n";
    
}

//gibt Liste aller Foren mit Themen aus
function forum_liste()
{
    
    global $mysqli_link;
    global $id, $http_host, $forum_admin, $chat_grafik, $farbe_text;
    global $t, $f1, $f2, $f3, $f4, $farbe_tabelle_kopf, $farbe_tabelle_kopf2;
    global $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $farbe_tabellenrahmen, $u_level;
    
    $sql = "select fo_id, fo_name, fo_order, fo_admin,
                th_id, th_fo_id, th_name, th_desc, th_anzthreads, th_anzreplys, th_order, th_postings
                from forum, thema
                where fo_id = th_fo_id ";
    if ($u_level == "G")
        $sql .= "and ( ((fo_admin & 8) = 8) or fo_admin = 0) ";
    if ($u_level == "U" || $u_level == "A" || $u_level == "M"
        || $u_level == "Z")
        $sql .= "and ( ((fo_admin & 2) = 2) or fo_admin = 0) ";
    
    $sql .= "order by fo_order, th_order";
    
    $query = mysqli_query($mysqli_link, $sql);
    //fo_id merken zur Darstellnug des Kopfes
    $fo_id_last = 0;
    $zeile = 0;
    if ($query && mysqli_num_rows($query) > 0) {
        while ($thema = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
            //Neues Forum?
            if ($fo_id_last != $thema['fo_id']) {
                if ($zeile > 0)
                    echo "</table></td></tr></table><br>";
                echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\"></tr><td>\n";
                echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
                echo "<tr bgcolor=\"$farbe_tabelle_kopf\">\n";
                echo "<td width=\"1\"><img src=\"pics/fuell.gif\" width=\"1\" height=\"25\" border=\"0\"></td>\n";
                echo "<td width=\"559\" colspan=\"2\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
                echo "<tr><td>&nbsp;<DIV style=\"color:$farbe_text; font-weight:bold;\">&nbsp;&nbsp;"
                    . htmlspecialchars($thema['fo_name'])
                    . "<a name=\"" . $thema['fo_id'] . "\"></a></DIV></td>";
                if ($forum_admin) {
                    echo "<td style=\"color:$farbe_text;\" width=\"85\" align=\"right\" valign=\"middle\"><a href=\"forum.php?id=$id&http_host=$http_host&aktion=forum_delete&fo_id=$thema[fo_id]\" onClick=\"return ask('$t[conf_delete_forum]')\">$chat_grafik[forum_loeschen]</a>&nbsp;</td>";
                    echo "<td style=\"color:$farbe_text;\" width=\"85\" align=\"right\" valign=\"middle\"><a href=\"forum.php?id=$id&http_host=$http_host&aktion=forum_edit&fo_id=$thema[fo_id]\">$chat_grafik[forum_editieren]</a>&nbsp;</td>";
                    echo "<td style=\"color:$farbe_text;\" width=\"90\" align=\"right\" valign=\"middle\">&nbsp;<a href=\"forum.php?id=$id&http_host=$http_host&fo_id=$thema[fo_id]&aktion=thema_neu\">$chat_grafik[forum_neuesthema]</a>&nbsp;</td>";
                    echo "<td width=\"17\"><a style=\"color:$farbe_text;\" href=\"forum.php?id=$id&http_host=$http_host&fo_id=$thema[fo_id]&fo_order=$thema[fo_order]&aktion=forum_up\">"
                        . $chat_grafik['forum_pfeil_oben']
                        . "</a><br><img src=\"pics/fuell.gif\" width=\"1\" height=\"1\" border=\"0\"><br><a href=\"forum.php?id=$id&http_host=$http_host&fo_id=$thema[fo_id]&fo_order=$thema[fo_order]&aktion=forum_down\">"
                        . $chat_grafik['forum_pfeil_unten'] . "</a></td>\n";
                }
                echo "</tr></table></td>\n";
                echo "<td width=\"67\" align=\"center\">$f1<DIV style=\"color:$farbe_text; font-weight:bold;\">"
                    . $t['anzbeitraege'] . "</DIV>$f2</td>\n";
                echo "<td width=\"66\" align=\"center\">$f1<DIV style=\"color:$farbe_text; font-weight:bold;\">"
                    . $t['anzthreads'] . "</DIV>$f2</td>\n";
                echo "<td width=\"66\" align=\"center\">$f1<DIV style=\"color:$farbe_text; font-weight:bold;\">"
                    . $t['anzreplys'] . "</DIV>$f2</td>\n";
                echo "</tr>\n";
                echo "<tr bgcolor=\"$farbe_tabellenrahmen\"><td colspan=\"6\"><img src=\"pics/fuell.gif\" width=\"1\" height=\"1\" border=\"0\"></td></tr>\n";
            }
            if ($zeile % 2)
                $farbe = $farbe_tabelle_zeile1;
            else $farbe = $farbe_tabelle_zeile2;
            if ($thema['th_name'] != "dummy-thema") {
                
                if ($thema['th_postings'])
                    $arr_posting = explode(",", $thema['th_postings']);
                else $arr_posting = array();
                $ungelesene = anzahl_ungelesene3($arr_posting, $thema['th_id']);
                
                echo "<tr bgcolor=$farbe>";
                echo "<td width=\"1\"><img src=\"pics/fuell.gif\" width=\"1\" height=\"30\" border=\"0\"></td>\n";
                
                if ($ungelesene == 0)
                    $folder = $chat_grafik['forum_ordnerneu'];
                else if ($ungelesene < 11)
                    $folder = $chat_grafik['forum_ordnerblau'];
                else $folder = $chat_grafik['forum_ordnervoll'];
                
                echo "<td width=\"30\" align=\"center\">$folder</td>";
                if ($forum_admin) {
                    echo "<td width=\"550\">";
                    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">";
                    echo "<tr><td width=\"385\">$f1<B><A href=\"forum.php?id=$id&http_host=$http_host&th_id=$thema[th_id]&aktion=show_thema\">"
                        . htmlspecialchars($thema['th_name'])
                        . "</a></B>$f2<br>" . $f3 . " "
                        . htmlspecialchars($thema['th_desc'])
                        . "$f4</td>";
                    echo "<td width=\"85\" align=\"center\" valign=\"middle\"><a href=\"forum.php?id=$id&http_host=$http_host&aktion=thema_delete&th_id=$thema[th_id]\" onClick=\"return ask('$t[conf_delete_thema]')\">$chat_grafik[forum_loeschen]</a>&nbsp;</td>";
                    echo "<td width=\"85\" align=\"center\" valign=\"middle\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$thema[th_id]&aktion=thema_edit\">$chat_grafik[forum_themabearbeiten]</a>&nbsp;</td>";
                    echo "<td width=\"20\" align=\"center\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$thema[th_id]&fo_id=$thema[fo_id]&th_order=$thema[th_order]&aktion=thema_up\">"
                        . $chat_grafik['forum_pfeil_oben']
                        . "</a><br><img src=\"pics/fuell.gif\" width=\"1\" height=\"1\" border=\"0\"><br><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$thema[th_id]&fo_id=$thema[fo_id]&th_order=$thema[th_order]&aktion=thema_down\">"
                        . $chat_grafik['forum_pfeil_unten']
                        . "</td></tr></table></td>\n";
                    
                } else {
                    
                    echo "<td width=\"550\">$f1<B><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$thema[th_id]&aktion=show_thema\">"
                        . htmlspecialchars($thema['th_name'])
                        . "$f2</a></B><br>" . $f3 . " "
                        . htmlspecialchars($thema['th_desc'])
                        . "$f4</td>\n";
                    
                }
                
                echo "<td align=\"center\">$f1 $ungelesene $f2</td>\n";
                echo "<td align=\"center\">$f1 $thema[th_anzthreads] $f2</td>\n";
                echo "<td align=\"center\">$f1 $thema[th_anzreplys] $f2</td>\n";
                echo "</tr>";
            }
            $fo_id_last = $thema['fo_id'];
            $zeile++;
        }
    }
    echo "</table></td></tr></table>";
    show_icon_description("forum");
    @mysqli_free_result($query);
}

//Zeigt Erklärung der verschiedenen Folder an
function show_icon_description($mode)
{
    
    global $t, $f3, $f4, $chat_grafik;
    
    echo "<br><table width=\"760\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n";
    echo "<tr><td width=\"20\" align=\"center\">$chat_grafik[forum_ordnerneu]</td>\n";
    echo "<td width=\"740\">$f3 = $t[desc_folder] ";
    echo "$f4</td></tr>";
    echo "<tr><td width=\"20\" align=\"center\">$chat_grafik[forum_ordnerblau]</td>\n";
    echo "<td width=\"740\">$f3 = $t[desc_redfolder] ($chat_grafik[forum_ordnervoll] = $t[desc_burningredfolder])$f4</td></tr>";
    
    echo "<tr><td width=\"20\" align=\"center\">$chat_grafik[forum_topthema]</td>\n";
    echo "<td width=\"740\">$f3 = $t[desc_topposting] $f4</td></tr>";
    echo "<tr><td width=\"20\" align=\"center\">$chat_grafik[forum_threadgeschlossen]</td>\n";
    echo "<td width=\"740\">$f3 = $t[desc_threadgeschlossen] $f4</td></tr>";
    
    echo "</table>";
    
}

//Eingabemaske für Thema
function maske_thema($th_id = 0)
{
    
    global $id, $http_host, $fo_id, $mysqli_link;
    global $f1, $f2, $farbe_tabelle_kopf, $farbe_tabelle_kopf2, $farbe_tabellenrahmen;
    global $t, $chat_grafik, $farbe_text;
    
    if ($th_id > 0) {
        
        $sql = "select th_name, th_desc from thema where th_id=" . intval($th_id);
        $query = mysqli_query($mysqli_link, $sql);
        $th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
        $th_desc = htmlspecialchars(mysqli_result($query, 0, "th_desc"));
        @mysqli_free_result($query);
        
        $kopfzeile = $chat_grafik['forum_themabearbeiten'];
        $button = $t['thema_button_edit'];
        
    } else {
        
        $kopfzeile = $chat_grafik['forum_neuesthema'];
        $button = $t['thema_button'];
        
    }
    
    if (!isset($th_name))
        $th_name = "";
    if (!isset($th_desc))
        $th_desc = "";
    
    echo "<form action=\"forum.php\" method=\"post\">";
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\"><tr><td>";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">";
    echo "<tr bgcolor=\"$farbe_tabelle_kopf\"><td colspan=\"2\"><DIV style=\"color:$farbe_text; font-weight:bold;\">$kopfzeile</td></tr>\n";
    echo "</table></td></tr><tr><td>\n";
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\" bgcolor=\"$farbe_tabelle_kopf2\">";
    echo "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";
    echo "<tr><td width=\"260\">$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[thema_msg1]</DIV> $f2</td>";
    echo "<td width=500><input type=\"text\" size=\"40\" name=\"th_name\" value=\"$th_name\"></td></tr>\n";
    echo "<tr><td width=\"260\">$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[thema_msg2]</DIV> $f2</td>";
    echo "<td width=500><textarea name=\"th_desc\" rows=\"3\" cols=\"90\">$th_desc</textarea></td></tr>\n";
    if ($th_id > 0) {
        echo "<tr><td width=\"260\">$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[thema_msg3]</DIV> $f2</td>";
        echo "<td width=500>";
        echo "<input type=\"checkbox\" name=\"th_forumwechsel\" value=\"Y\">";
        
        $sql = "SELECT fo_id, fo_name FROM forum ORDER BY fo_order ";
        $query = mysqli_query($mysqli_link, $sql);
        echo "<SELECT NAME=\"th_verschiebe_nach\" SIZE=\"1\">";
        while ($row = mysqli_fetch_object($query)) {
            echo "<OPTION ";
            if ($row->fo_id == $fo_id)
                echo "SELECTED ";
            echo "VALUE=\"$row->fo_id\">$row->fo_name </OPTION>";
        }
        echo "</SELECT></td></tr>\n";
        @mysqli_free_result($query);
        
        echo "</td></tr>\n";
    }
    echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"$button\"></td></tr>\n";
    echo "</table></td></tr></table>";
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
    echo "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">";
    echo "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
    if ($th_id > 0) {
        echo "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
        echo "<input type=\"hidden\" name=\"aktion\" value=\"thema_editieren\">";
    } else {
        echo "<input type=\"hidden\" name=\"aktion\" value=\"thema_anlegen\">";
    }
    echo "</form>";
}

//Zeigt Pfad und Seiten in Themaliste an
function show_pfad($th_id)
{
    
    global $mysqli_link, $f3, $f4, $id, $http_host, $thread, $anzahl_po_seite;
    global $seite, $t, $farbe_vlink, $farbe_hervorhebung_forum;
    
    //Infos über Forum und Thema holen
    $sql = "select fo_id, fo_name, th_name, th_anzthreads
                from forum, thema
                where th_id = " . intval($th_id) . "
                and fo_id = th_fo_id";
    $query = mysqli_query($mysqli_link, $sql);
    $fo_id = htmlspecialchars(mysqli_result($query, 0, "fo_id"));
    $fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
    $th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
    $th_anzthreads = mysqli_result($query, 0, "th_anzthreads");
    @mysqli_free_result($query);
    
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
    echo "<tr><td>$f3<a href=\"forum.php?id=$id&http_host=$http_host#$fo_id\">$fo_name</a> > <a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&show_tree=$thread&aktion=show_thema&seite=$seite\">$th_name</a>$f4</td>\n";
    
    if (!$anzahl_po_seite || $anzahl_po_seite == 0)
        $anzahl_po_seite = 20;
    $anz_seiten = ceil(($th_anzthreads / $anzahl_po_seite));
    if ($anz_seiten > 1) {
        echo "<td align=\"right\">$f3 $t[page] ";
        for ($page = 1; $page <= $anz_seiten; $page++) {
            
            if ($page == $seite)
                $col = $farbe_hervorhebung_forum;
            else $col = $farbe_vlink;
            echo "<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&aktion=show_thema&seite=$page\"><font color=\"$col\">$page</font></a> ";
        }
        echo "$f4</td></tr>\n";
    } else {
        echo "</tr>\n";
    }
    echo "<tr><td<><img src=\"pics/fuell.gif\" width=\"1\" height=\"2\" border=\"0\"></td></tr>";
    echo "</table>\n";
    
    return $th_name;
}

//Zeigt ein Thema mit allen Postings an
function show_thema()
{
    
    global $mysqli_link;
    global $id, $http_host, $o_js, $forum_admin, $th_id, $show_tree, $seite, $farbe_link;
    global $t, $f1, $f2, $f3, $f4, $farbe_tabelle_kopf, $farbe_tabelle_kopf2, $farbe_tabellenrahmen;
    global $farbe_tabelle_zeile1, $farbe_tabelle_zeile2, $anzahl_po_seite, $chat_grafik, $farbe_text;
    global $admin, $anzahl_po_seite2, $u_id, $u_level;
    
    if ($anzahl_po_seite2) {
        $anzahl_po_seite2 = preg_replace("/[^0-9]/", "", $anzahl_po_seite2);
        $anzahl_po_seite = $anzahl_po_seite2;
        
        $f[u_forum_postingproseite] = $anzahl_po_seite2;
        
        if (!schreibe_db("user", $f, $u_id, "u_id")) {
            echo "Fehler beim Schreiben in DB!";
        }
        
    } else {
        $query = "SELECT u_forum_postingproseite FROM user WHERE u_id = '$u_id'";
        $result = mysqli_query($mysqli_link, $query);
        $a = mysqli_fetch_array($result);
        $anzahl_po_seite2 = $a['u_forum_postingproseite'];
        $anzahl_po_seite = $anzahl_po_seite2;
    }
    
    $leserechte = pruefe_leserechte($th_id);
    
    if (!$leserechte) {
        echo $t['leserechte'];
        exit;
    }
    if (!$seite)
        $seite = 1;
    
    $offset = ($seite - 1) * $anzahl_po_seite;
    
    $sql = "select po_id, po_u_id, date_format(from_unixtime(po_ts), '%d.%m.%y') as po_date,
                date_format(from_unixtime(po_threadts), '%d.%m.%y') as po_date2,
                po_titel, po_threadorder, po_topposting, po_threadgesperrt, po_gesperrt, u_nick,
		u_level, u_punkte_gesamt, u_punkte_gruppe, u_chathomepage
                from posting
                left join user on po_u_id = u_id
                where po_vater_id = 0
                and po_th_id = " . intval($th_id) . "
                order by po_topposting desc, po_threadts desc, po_ts desc
                limit $offset, $anzahl_po_seite";
    
    $query = mysqli_query($mysqli_link, $sql);
    
    $th_name = show_pfad($th_id);
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\"><tr><td>\n";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr bgcolor=\"$farbe_tabelle_kopf\">\n";
    echo "<td width=\"30\"><img src=\"pics/fuell.gif\" width=\"30\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"340\"><img src=\"pics/fuell.gif\" width=\"340\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"170\"><img src=\"pics/fuell.gif\" width=\"170\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"120\"><img src=\"pics/fuell.gif\" width=\"120\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"40\"><img src=\"pics/fuell.gif\" width=\"40\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"40\"><img src=\"pics/fuell.gif\" width=\"40\" height=\"1\" border=\"0\"></td></tr>\n";
    
    echo "<tr bgcolor=\"$farbe_tabelle_kopf\">\n";
    echo "<td colspan=\"3\"><table width=\"390\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
    echo "<tr><td width=\"3\"><img src=\"pics/fuell.gif\" width=\"3\" height=\"30\" border=\"0\"></td><td ><DIV style=\"color:$farbe_text; font-weight:bold;\">&nbsp;&nbsp;$th_name</DIV></td>\n";
    
    $schreibrechte = pruefe_schreibrechte($th_id);
    if ($schreibrechte)
        echo "<td width=\"100\" align=\"center\">$f3<a style=\"color:$farbe_text;\" href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_vater_id=0&aktion=thread_neu\">$t[neuer_thread]</a>$f4</td>";
    else {
        echo "<td width=\"100\" align=\"center\">$f3$t[nur_leserechte]$f4</td>";
    }
    echo "<td width=\"100\" align=\"center\">$f3<a style=\"color:$farbe_text;\" href=\"forum.php?id=$id&http_host=$http_host"
        . "&th_id=$th_id&aktion=thema_alles_gelesen\">$t[alles_gelesen]</a>$f4</td>";
    echo "</tr></table></td>\n";
    echo "<td>$f3<DIV style=\"color:$farbe_text; \">$t[autor]</DIV>$f4</td>\n";
    echo "<td align=\"center\">$f3<DIV style=\"color:$farbe_text; \">$t[datum]<br>$t[letztes_posting]</DIV>$f4</td>\n";
    echo "<td align=\"center\">$f3<DIV style=\"color:$farbe_text; \">$t[anzreplys]</DIV>$f4</td>\n";
    echo "<td align=\"center\">$f3<DIV style=\"color:$farbe_text; \">$t[anzneue]</DIV>$f4</td></tr>\n";
    echo "<tr bgcolor=\"$farbe_tabellenrahmen\"><td colspan=\"7\"><img src=\"pics/fuell.gif\" width=\"1\" height=\"1\" border=\"0\"></td></tr>\n";
    
    $zeile = 0;
    while ($posting = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
        
        set_time_limit(0);
        
        if ($zeile % 2)
            $farbe = $farbe_tabelle_zeile1;
        else $farbe = $farbe_tabelle_zeile2;
        
        if ($posting['po_threadorder'] == "0") {
            $anzreplys = 0;
            $icon = "<img src=\"pics/forum/o.gif\" width=\"20\" height=\"25\" border=\"0\">";
            $arr_postings = array($posting['po_id']);
        } else {
            $arr_postings = explode(",", $posting['po_threadorder']);
            $anzreplys = count($arr_postings);
            //Erstes Posting mit beruecksichtigen
            $arr_postings[] = $posting['po_id'];
            if ($show_tree == $posting['po_id'])
                $icon = "<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&aktion=show_thema&seite=$seite\"><img src=\"pics/forum/m.gif\" width=\"20\" height=\"25\" border=\"0\"></a>";
            else $icon = "<a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&show_tree=$posting[po_id]&aktion=show_thema&seite=$seite\"><img src=\"pics/forum/p.gif\" width=\"20\" height=\"25\" border=\"0\"></a>";
        }
        
        $ungelesene = anzahl_ungelesene($arr_postings, $th_id);
        
        array_pop($arr_postings);
        
        if ($ungelesene === 0) {
            if ($posting['po_topposting'] == 'Y') // Topposting 
 {
                $folder = $chat_grafik['forum_topthema'];
            } elseif ($posting['po_threadgesperrt'] == 'Y') // Geschlossener Thread
 {
                $folder = $chat_grafik['forum_threadgeschlossen'];
            } else {
                $folder = $chat_grafik['forum_ordnerneu'];
            }
        } elseif ($ungelesene < 11)
            $folder = $chat_grafik['forum_ordnerblau'];
        else $folder = $chat_grafik['forum_ordnervoll'];
        
        if ($ungelesene != 0) {
            $coli = "<font color=red>";
            $colo = "</font>";
        } else {
            $coli = "";
            $colo = "";
        }
        
        echo "<tr bgcolor=\"$farbe\"><td align=\"center\">$folder</nobr></td>\n";
        echo "<td align=\"center\">$icon</td>\n";
        
        if ($posting['po_gesperrt'] == 'Y' and !$forum_admin) {
            echo "<td>&nbsp;<b><font size=\"-1\" color=\"$farbe_link\">"
                . substr($posting['po_titel'], 0, 40)
                . "</font> <font size=\"-1\" color=\"red\">(gesperrt)</font></b></td>\n";
        } elseif ($posting['po_gesperrt'] == 'Y' and $forum_admin) {
            echo "<td>&nbsp;$f1<b><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">"
                . substr($posting['po_titel'], 0, 40)
                . "</a></b>$f2  <font size=\"-1\" color=\"red\"><b>(gesperrt)</b></font></td>\n";
        } else {
            echo "<td>&nbsp;$f1<b><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">"
                . substr($posting['po_titel'], 0, 40)
                . "</a></b>$f2</td>\n";
        }
        
        if (!$posting['u_nick']) {
            echo "<td>$f3<b>Nobody</b>$f4</td>\n";
        } else {
            
            $userdata = array();
            $userdata['u_id'] = $posting['po_u_id'];
            $userdata['u_nick'] = $posting['u_nick'];
            $userdata['u_level'] = $posting['u_level'];
            $userdata['u_punkte_gesamt'] = $posting['u_punkte_gesamt'];
            $userdata['u_punkte_gruppe'] = $posting['u_punkte_gruppe'];
            $userdata['u_chathomepage'] = $posting['u_chathomepage'];
            $userlink = user($posting['po_u_id'], $userdata, $o_js, FALSE,
                "&nbsp;", "", "", TRUE, FALSE, 29);
            if ($posting['u_level'] == 'Z') {
                echo "<td>$f1 $userdata[u_nick] $f2</td>\n";
            } else {
                echo "<td>$f1 $userlink $f2</td>\n";
            }
        }
        
        if ($posting['po_date2'] == '01.01.70'
            || $posting['po_date'] == $posting['po_date2']) {
            $date2 = "";
        } else {
            $date2 = "$f3; " . substr($posting['po_date2'], 0, 5) . "$f4";
        }
        echo "<td align=\"center\">$f3$posting[po_date]$f4$date2</td>\n";
        echo "<td align=\"center\">$f3$anzreplys$f4</td>\n";
        echo "<td align=\"center\">$f3$coli$ungelesene$colo$f4</td></tr>\n";
        
        if (($show_tree == $posting['po_id'])
            && ($posting['po_threadorder'] != "0")) {
            echo "<tr bgcolor=\"$farbe\"><td>&nbsp;</td><td colspan=\"6\">\n";
            zeige_baum($arr_postings, $posting['po_threadorder'],
                $posting['po_id']);
            echo "</td></tr>\n";
        }
        
        $zeile++;
        
    }
    
    echo "</table></td></tr></table>";
    
    show_pfad($th_id);
    show_icon_description("thema");
    
    echo "<br><table width=\"760\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n";
    echo "<tr><td>";
    echo "<form action=\"forum.php\">\n";
    echo "$t[forum_postingsproseite] <input name=\"anzahl_po_seite2\" size=\"3\" maxlength=\"4\" value=\"$anzahl_po_seite\">\n";
    echo "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n";
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
    echo "<input type=\"hidden\" name=\"aktion\" value=\"show_thema\">\n";
    echo "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
    echo "<input type=\"submit\" value=\"$t[speichern]\">\n";
    echo "</form>\n";
    echo "</td></tr>\n";
    echo "</table>\n";
    
}

//Maske zum Eingeben/Editieren/Quoten von Postings
function maske_posting($mode)
{
    
    global $id, $u_id, $http_host, $th_id, $po_id, $po_vater_id, $po_tiefe, $mysqli_link, $po_titel, $po_text, $thread, $seite;
    global $f1, $f2, $f3, $f4, $farbe_tabelle_kopf, $farbe_tabelle_kopf2, $farbe_tabellenrahmen, $farbe_text;
    global $t, $mysqli_link;
    global $forum_admin, $u_nick, $smilies_datei;
    
    $smilies_datei = "forum-" . $smilies_datei;
    
    switch ($mode) {
        
        case "neuer_thread":
            $kopfzeile = $t['neuer_thread'];
            $button = $t['neuer_thread_button'];
            $titel = $t['neuer_thread'];
            
            if (!$po_text)
                $po_text = erzeuge_fuss("");
            
            break;
        case "reply":
        //Daten des Vaters holen
            $sql = "select date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
                                po_titel, po_text, ifnull(u_nick, 'unknown') as u_nick
                                from posting
                                left join user on po_u_id = u_id
                                where po_id = " . intval($po_vater_id);
            $query = mysqli_query($mysqli_link, $sql);
            
            $autor = mysqli_result($query, 0, "u_nick");
            $po_date = mysqli_result($query, 0, "po_date");
            $po_titel = (mysqli_result($query, 0, "po_titel"));
            if (substr($po_titel, 0, 3) != $t['reply'])
                $po_titel = $t['reply'] . " " . $po_titel;
            $titel = $po_titel;
            $po_text = mysqli_result($query, 0, "po_text");
            $po_text = erzeuge_quoting($po_text, $autor, $po_date);
            $po_text = erzeuge_fuss($po_text);
            
            $po_tiefe = mysqli_result($query, 0, "po_tiefe");
            
            $kopfzeile = $po_titel;
            $button = $t['neuer_thread_button'];
            break;
        case "answer":
        //Daten des Vaters holen
            $sql = "select po_tiefe, po_titel
                                from posting
                                where po_id = " . intval($po_vater_id);
            $query = mysqli_query($mysqli_link, $sql);
            
            $po_titel = mysqli_result($query, 0, "po_titel");
            if (substr($po_titel, 0, 3) != $t['reply'])
                $po_titel = $t['reply'] . " " . $po_titel;
            $titel = $po_titel;
            $po_text = erzeuge_fuss("");
            
            $po_tiefe = mysqli_result($query, 0, "po_tiefe");
            
            $kopfzeile = $po_titel;
            $button = $t['neuer_thread_button'];
            break;
        
        case "edit":
        //Daten holen
            $sql = "select date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
                                po_titel, po_text, ifnull(u_nick, 'unknown') as u_nick, u_id, po_threadgesperrt, po_topposting
                                from posting
                                left join user on po_u_id = u_id
                                where po_id = " . intval($po_id);
            $query = mysqli_query($mysqli_link, $sql);
            
            $autor = mysqli_result($query, 0, "u_nick");
            $user_id = mysqli_result($query, 0, "u_id");
            $po_date = mysqli_result($query, 0, "po_date");
            $po_topposting = mysqli_result($query, 0, "po_topposting");
            $po_threadgesperrt = mysqli_result($query, 0, "po_threadgesperrt");
            $po_titel = mysqli_result($query, 0, "po_titel");
            $titel = $po_titel;
            $po_text = mysqli_result($query, 0, "po_text");
            $po_tiefe = mysqli_result($query, 0, "po_tiefe");
            
            //Testen ob User mogelt, indem er den Edit-Link mit anderer po_id benutzt
            if ((!$forum_admin) && ($user_id != $u_id)) {
                echo "wanna cheat eh? bad boy!";
                exit;
            }
            
            $kopfzeile = $po_titel;
            $button = $t['edit_button'];
            
            break;
        
    }
    echo "<form name=\"form\" action=\"forum.php\" method=\"post\">";
    show_pfad_posting($th_id, $titel);
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\"><tr><td>";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">";
    echo "<tr bgcolor=\"$farbe_tabelle_kopf\"><td><DIV style=\"color:$farbe_text; font-weight:bold;\">$kopfzeile</DIV></td></tr>\n";
    echo "</table></td></tr><tr><td>\n";
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\" bgcolor=\"$farbe_tabelle_kopf2\">\n";
    echo "<tr><td width=\"200\">$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[posting_msg1]</DIV> $f2</td>";
    echo "<td width=560><input type=\"text\" size=\"50\" name=\"po_titel\" value=\"$po_titel\"></td></tr>\n";
    echo "<tr><td colspan=\"2\">$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[posting_msg2]<DIV style=\"color:$farbe_text; font-weight:bold;\"> $f2<br>"
        . $f3
        . "<DIV style=\"color:$farbe_text; \">($t[desc_posting])</DIV>$f4</td></tr>\n";
    
    $link_smilies = "$smilies_datei?http_host=$http_host&id=$id";
    
    $fenster = str_replace("+", "", $u_nick);
    $fenster = str_replace("-", "", $fenster);
    $fenster = str_replace("ä", "", $fenster);
    $fenster = str_replace("ö", "", $fenster);
    $fenster = str_replace("ü", "", $fenster);
    $fenster = str_replace("Ä", "", $fenster);
    $fenster = str_replace("Ö", "", $fenster);
    $fenster = str_replace("Ü", "", $fenster);
    $fenster = str_replace("ß", "", $fenster);
    
    echo "<tr><td colspan=\"2\"><A HREF=\"$link_smilies\" TARGET=\"640_$fenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster('$link_smilies');return(false)\">$f3 <DIV style=\"color:$farbe_text; \">$t[smilies]</DIV> $f4</A></td></tr>\n";
    echo "<tr><td colspan=\"2\" align=\"center\"><textarea name=\"po_text\" rows=\"15\" cols=\"95\"  wrap=physical>$po_text</textarea></td></tr>\n";
    if ($forum_admin && ($mode == "edit")) {
        
        echo "<tr><td>$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">$t[posting_msg3]</DIV> $f2<br>$f3 <DIV style=\"color:$farbe_text; \">$t[posting_msg4]</DIV> $f4</td>\n";
        echo "<td><input type=\"text\" size=\"20\" name=\"autor\" value=\"$autor\"></td></tr>\n";
        
        // Nur im Obersten Vater die TOP und gesperrt einstellungen ändern lassen
        if ($po_id == $thread) {
            echo "<tr><td>$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">Thread gesperrt</DIV>$f2</td>\n";
            echo "<td><SELECT NAME=\"po_threadgesperrt\"><OPTION ";
            if ($po_threadgesperrt == 'Y')
                echo "SELECTED ";
            echo "VALUE=\"Y\">Ja</OPTION><OPTION ";
            if ($po_threadgesperrt == 'N')
                echo "SELECTED ";
            echo "VALUE=\"N\">Nein</SELECT></td></tr>\n";
            
            echo "<tr><td>$f1 <DIV style=\"color:$farbe_text; font-weight:bold;\">TOP Posting</DIV>$f2</td>\n";
            echo "<td><SELECT NAME=\"po_topposting\"><OPTION ";
            if ($po_topposting == 'Y')
                echo "SELECTED ";
            echo "VALUE=\"Y\">Ja</OPTION><OPTION ";
            if ($po_topposting == 'N')
                echo "SELECTED ";
            echo "VALUE=\"N\">Nein</SELECT></td></tr>\n";
        }
        
    }
    echo "<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" value=\"$button\"></td></tr>\n";
    echo "</table></td></tr></table>";
    show_pfad_posting($th_id, $titel);
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
    echo "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">";
    echo "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
    
    if ($mode == "neuer_thread") {
        echo "<input type=\"hidden\" name=\"po_tiefe\" value=\"0\">\n";
        echo "<input type=\"hidden\" name=\"po_vater_id\" value=\"0\">\n";
    } else if (($mode == "reply") || ($mode == "answer")) {
        $tiefe = $po_tiefe + 1;
        echo "<input type=\"hidden\" name=\"thread\" value=\"$thread\">\n";
        echo "<input type=\"hidden\" name=\"po_tiefe\" value=\"$tiefe\">\n";
        echo "<input type=\"hidden\" name=\"po_vater_id\" value=\"$po_vater_id\">\n";
        echo "<input type=\"hidden\" name=\"show_tree\" value=\"$thread\">\n";
    } else {
        $tiefe = $po_tiefe;
        echo "<input type=\"hidden\" name=\"thread\" value=\"$thread\">\n";
        echo "<input type=\"hidden\" name=\"po_tiefe\" value=\"$tiefe\">\n";
        echo "<input type=\"hidden\" name=\"po_id\" value=\"$po_id\">\n";
        echo "<input type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n";
    }
    
    echo "<input type=\"hidden\" name=\"aktion\" value=\"posting_anlegen\">";
    echo "<input type=\"hidden\" name=\"seite\" value=\"$seite\">";
    echo "<input type=\"hidden\" name=\"mode\" value=\"$mode\">";
    echo "</form>";
}

//Zeigt die gutgeschriebenen Punkte an
function verbuche_punkte($u_id)
{
    
    global $t, $punkte_pro_posting, $farbe_tabellenrahmen, $farbe_tabelle_kopf2, $farbe_text;
    global $punktefeatures;
    
    if ($punktefeatures) {
        echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\"><tr><td>\n";
        echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">";
        echo "<tr bgcolor=\"$farbe_tabelle_kopf2\" valign=\"bottom\">\n<TD><DIV style=\"color:$farbe_text; font-weight:bold;\">"
            . $t['forum_punkte1'] . punkte_offline($punkte_pro_posting, $u_id)
            . "</DIV></TD></tr></table></td></tr></table><BR>\n";
    }
}

//Zeigt Pfad in Posting an
function show_pfad_posting($th_id, $po_titel)
{
    
    global $mysqli_link, $f3, $f4, $id, $http_host, $thread, $seite;
    //Infos über Forum und Thema holen
    $sql = "select fo_id, fo_name, th_name
                from forum, thema
                where th_id = " . intval($th_id) . "
                and fo_id = th_fo_id";
    $query = mysqli_query($mysqli_link, $sql);
    $fo_id = htmlspecialchars(mysqli_result($query, 0, "fo_id"));
    $fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
    $th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
    @mysqli_free_result($query);
    
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
    echo "<tr><td>$f3<a href=\"forum.php?id=$id&http_host=$http_host#$fo_id\">$fo_name</a> > <a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&show_tree=$thread&aktion=show_thema&seite=$seite\">$th_name</a> > $po_titel $f4</td></tr>\n";
    echo "<tr><td><img src=\"pics/fuell.gif\" width=\"1\" height=\"2\" border=\"0\"></td></tr>";
    echo "</table>\n";
    
}

//gibt Navigation für Posting aus
function navigation_posting(
    $last,
    $next,
    $po_u_id,
    $th_id,
    $user_nick = "",
    $thread_gelesen_zeigen = FALSE)
{
    
    global $f1, $f2, $f3, $f4, $farbe_tabelle_kopf2, $t, $seite, $farbe_tabellenrahmen;
    global $id, $http_host, $po_id, $u_id, $thread, $forum_admin, $chat_grafik, $farbe_text;
    global $u_level;
    
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\"><tr><td>\n"
        . "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">"
        . "<tr bgcolor=\"$farbe_tabelle_kopf2\" valign=\"bottom\" align=\"center\">\n";
    
    if ($last)
        echo "<td width=\"50\" valign=\"middle\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$last&thread=$thread&aktion=show_posting&seite=$seite\">"
            . $chat_grafik['forum_pfeil_links'] . "</a></td>\n";
    else echo "<td width=\"50\" >&nbsp;</td>\n";
    
    if ($next)
        echo "<td width=\"50\" valign=\"middle\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$next&thread=$thread&aktion=show_posting&seite=$seite\">"
            . $chat_grafik['forum_pfeil_rechts'] . "</a></td>\n";
    else echo "<td width=\"50\" >&nbsp;</td>\n";
    
    if ($thread_gelesen_zeigen) {
        echo "<td width=\"170\" align=\"center\">$f3<a style=\"color:$farbe_text;\" href=\"forum.php?id=$id&http_host=$http_host"
            . "&th_id=$th_id&thread=$thread&aktion=thread_alles_gelesen&seite=$seite\">$t[thread_alles_gelesen]</a>$f4</td>";
    } else {
        echo "<td width=\"170\">&nbsp;</td>\n";
    }
    
    echo "<td width=\"210\">&nbsp;</td>\n";
    
    $threadgesperrt = ist_thread_gesperrt($thread);
    $schreibrechte = pruefe_schreibrechte($th_id);
    //darf user posting bearbeiten
    //entweder eigenes posting oder forum_admin
    if ((($u_id == $po_u_id && !$threadgesperrt) || ($forum_admin))
        && ($schreibrechte))
        echo "<td width=\"50\" ><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=edit&seite=$seite\">"
            . $chat_grafik['forum_editieren'] . "</a></td>";
    else echo "<td width=\"50\" >&nbsp;</td>\n";
    
    // Privat antworten
    if ($user_nick && $schreibrechte) {
        
        // Beim Target die Sonderzeichen rausamachen
        $pfenster = str_replace("+", "", $user_nick);
        $pfenster = str_replace("-", "", $pfenster);
        $pfenster = str_replace("ä", "", $pfenster);
        $pfenster = str_replace("ö", "", $pfenster);
        $pfenster = str_replace("ü", "", $pfenster);
        $pfenster = str_replace("Ä", "", $pfenster);
        $pfenster = str_replace("Ö", "", $pfenster);
        $pfenster = str_replace("Ü", "", $pfenster);
        $pfenster = str_replace("ß", "", $pfenster);
        
        $mailurl = "mail.php?aktion=antworten_forum&id=$id&http_host=$http_host&th_id=$th_id&po_vater_id=$po_id&thread=$thread";
        echo "<td width=\"50\" ><a href=\"$mailurl\" TARGET=\"640_$pfenster\" onMouseOver=\"return(true)\" onClick=\"neuesFenster2('$mailurl'); return(false)\">"
            . $chat_grafik['forum_privat'] . "</a></td>";
    } else {
        echo "<td width=\"50\" >&nbsp;</td>\n";
    }
    
    if ($schreibrechte && !$threadgesperrt) {
        echo "<td width=\"50\" ><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_vater_id=$po_id&thread=$thread&aktion=answer&seite=$seite\">"
            . $chat_grafik['forum_antworten'] . "</a></td>";
        echo "<td width=\"50\" ><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_vater_id=$po_id&thread=$thread&aktion=reply&seite=$seite\">"
            . $chat_grafik['forum_zitieren'] . "</a></td>";
    } else {
        echo "<td width=\"50\" >&nbsp;</td>\n";
        echo "<td width=\"50\" >&nbsp;</td>\n";
    }
    
    //nur forum-admins duerfen postings loeschen
    if ($forum_admin) {
        echo "</tr>";
        
        echo "<tr bgcolor=\"$farbe_tabelle_kopf2\" valign=\"bottom\" align=\"center\">";
        echo "<td width=\"50\" >&nbsp;</td>\n";
        echo "<td width=\"50\" >&nbsp;</td>\n";
        echo "<td width=\"170\" >&nbsp;</td>\n";
        echo "<td width=\"210\" >&nbsp;</td>\n";
        
        echo "<td width=\"50\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=sperre_posting&seite=$seite\">"
            . $chat_grafik['forum_sperren'] . "</a></td>";
        echo "<td width=\"50\"><a onClick=\"return ask('$t[conf_delete]')\" href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=delete_posting&seite=$seite\">"
            . $chat_grafik['forum_loeschen'] . "</a></td>";
        if ($po_id == $thread) {
            echo "<td width=\"50\"><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&thread=$thread&aktion=verschiebe_posting&seite=$seite\">"
                . $chat_grafik['forum_verschieben'] . "</a></td>";
        } else {
            echo "<td width=\"50\" >&nbsp;</td>\n";
        }
        echo "<td></td>";
    }
    
    echo "</tr></table></td></tr></table>";
    
}

// Verschiebe Posting
function verschiebe_posting()
{
    global $id, $http_host, $mysqli_link, $po_id, $thread, $seite;
    global $f1, $f2, $f3, $f4, $farbe_tabelle_zeile1, $farbe_tabellenrahmen;
    global $t, $o_js, $th_id, $fo_id;
    
    $sql = "select po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
                po_titel, po_text, po_u_id, ifnull(u_nick, 'Nobody') as u_nick,
		u_email, u_id, u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage
                from posting
                left join user on po_u_id = u_id
                where po_id = " . intval($thread);
    
    $query = mysqli_query($mysqli_link, $sql);
    if ($query)
        $row = mysqli_fetch_object($query);
    mysqli_free_result($query);
    
    $sql = "SELECT fo_name, th_name FROM forum left join thema on fo_id = th_fo_id WHERE th_id = " . intval($th_id);
    $query = mysqli_query($mysqli_link, $sql);
    if ($query)
        $row2 = mysqli_fetch_object($query);
    mysqli_free_result($query);
    
    $sql = "SELECT fo_name, th_id, th_name FROM forum left join thema on fo_id = th_fo_id "
        . "WHERE th_name <> 'dummy-thema' " . "ORDER BY fo_order, th_order ";
    $query = mysqli_query($mysqli_link, $sql);
    
    echo "<form action=\"forum.php\" method=\"post\">";
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\">\n";
    echo "<tr><td><table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n";
    echo "<tr bgcolor=\"$farbe_tabelle_zeile1\"><td><b>$t[verschieben1]\"$row->po_titel\"</b></td></tr>";
    echo "</table></td></tr>";
    echo "<tr><td><table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n";
    echo "<tr bgcolor=\"$farbe_tabelle_zeile1\"><td>" . $f1
        . $t['verschieben2'] . $f2 . "</td><td>" . $row2->fo_name . " > "
        . $row2->th_name . "</td></tr>";
    echo "<tr bgcolor=\"$farbe_tabelle_zeile1\"><td>" . $f1
        . $t['verschieben3'] . $f2 . "</td><td>";
    echo "<SELECT NAME=\"verschiebe_nach\" SIZE=\"1\">";
    while ($row3 = mysqli_fetch_object($query)) {
        echo "<OPTION ";
        if ($row3->th_id == $th_id)
            echo "SELECTED ";
        echo "VALUE=\"$row3->th_id\">$row3->fo_name > $row3->th_name </OPTION>";
    }
    echo "</SELECT></td></tr>\n";
    @mysqli_free_result($query);
    echo "<tr bgcolor=\"$farbe_tabelle_zeile1\"><td align=\"center\" colspan=\"2\"><INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$t[verschieben4]\"></td></tr>";
    echo "</table></td></tr>";
    echo "</table>";
    
    echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
    echo "<input type=\"hidden\" name=\"thread_verschiebe\" value=\"$thread\">";
    echo "<input type=\"hidden\" name=\"seite\" value=\"$seite\">";
    echo "<input type=\"hidden\" name=\"verschiebe_von\" value=\"$th_id\">";
    echo "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">";
    echo "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
    echo "<input type=\"hidden\" name=\"aktion\" value=\"verschiebe_posting_ausfuehren\">";
    echo "</form>\n";
    
}

//zeigt Posting an
function show_posting()
{
    
    global $id, $http_host, $mysqli_link, $po_id, $thread, $seite;
    global $f1, $f2, $f3, $f4, $farbe_tabelle_zeile1, $farbe_tabellenrahmen;
    global $t, $o_js, $forum_admin;
    
    $sql = "select po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') as po_date, po_tiefe,
                po_titel, po_text, po_u_id, po_gesperrt, ifnull(u_nick, 'Nobody') as u_nick,
		u_email, u_id, u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage
                from posting
                left join user on po_u_id = u_id
                where po_id = " . intval($po_id);
    
    $query = mysqli_query($mysqli_link, $sql);
    if ($query)
        $row = mysqli_fetch_object($query);
    $th_id = $row->po_th_id;
    $po_u_id = $row->po_u_id;
    $po_date = $row->po_date;
    $po_tiefe = $row->po_tiefe;
    $po_titel = $row->po_titel;
    $po_gesperrt = $row->po_gesperrt;
    
    if ($po_gesperrt == 'Y' and !$forum_admin) {
        echo ('Posting gesperrt');
        return;
    }
    
    $po_text = ersetzte_smilies(chat_parse(nl2br($row->po_text)));
    if (($row->u_nick != "Nobody") && ($row->u_level <> "Z")) {
        $autor = user($po_u_id, $row, $o_js, FALSE, "&nbsp;", "", "", TRUE,
            TRUE);
    } else {
        $autor = $row->u_nick;
    }
    
    @mysqli_free_result($query);
    
    $sql = "select po_threadorder from posting where po_id=" . intval($thread);
    $query = mysqli_query($mysqli_link, $sql);
    $po_threadorder = mysqli_result($query, 0, "po_threadorder");
    
    @mysqli_free_result($query);
    
    //vorheriges und naechstes posting bestimmen
    if ($po_threadorder == "0") { //keine Antwort
        $last = 0;
        $next = 0;
    } else if ($po_id == $thread) { //root des Threads wird angezeigt
        $last = 0;
        $postingorder = explode(",", $po_threadorder);
        $next = $postingorder[0];
    } else { //muddu guggen...
        $postingorder = explode(",", $po_threadorder);
        $k = 0;
        while (list($k, $v) = each($postingorder)) {
            
            if ($v == $po_id)
                break;
        }
        if ($k > 0)
            $last = $postingorder[$k - 1];
        else $last = $thread;
        if (($k + 1) < count($postingorder))
            $next = $postingorder[$k + 1];
        else $next = 0;
    }
    
    if (!$forum_admin) {
        if (ist_posting_gesperrt($next))
            $next = 0;
        if (ist_posting_gesperrt($last))
            $last = 0;
    }
    
    show_pfad_posting($th_id, $po_titel);
    navigation_posting($last, $next, $po_u_id, $th_id, $row->u_nick, TRUE);
    echo "<table width=\"760\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\" bgcolor=\"$farbe_tabellenrahmen\"><tr><td>\n";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n";
    echo "<tr bgcolor=\"$farbe_tabelle_zeile1\"><td><b>$po_titel</b>";
    if ($po_gesperrt == 'Y') {
        echo " <b><font color=\"red\">(Posting gesperrt)</font></b>";
    }
    echo "</td></tr>";
    echo "<tr bgcolor=\"$farbe_tabelle_zeile1\"><td>" . $f1 . $t['datum']
        . $po_date . " " . $t['autor'] . $autor . $f2 . "</td></tr>";
    echo "</table></td></tr>";
    echo "<tr><td>\n";
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\">\n";
    echo "<tr bgcolor=\"$farbe_tabelle_zeile1\"><td>$f1" . $po_text
        . "$f2</td></tr>";
    if ($po_threadorder == "0") {
        echo "</table></td></tr></table>";
    } else {
        echo "</table></td></tr><tr><td>";
        echo "<table  width=\"760\" cellspacing=\"0\" cellpadding=\"5\" border=\"0\" bgcolor=\"$farbe_tabelle_zeile1\">\n";
        echo "<tr><td align=\"center\">";
        reset($postingorder);
        zeige_baum($postingorder, $po_threadorder, $thread, $po_id, TRUE);
        echo "</td></tr></table></td></tr></table>";
    }
    navigation_posting($last, $next, $po_u_id, $th_id, $row->u_nick, TRUE);
    show_pfad_posting($th_id, $po_titel);
}

//Zeigt Threadbaum 
function zeige_baum(
    &$postings_array,
    &$postings,
    $thread,
    $highlight = 0,
    $zeige_top = FALSE)
{
    
    global $mysqli_link, $f1, $f2, $f3, $f4, $seite, $th_id;
    global $farbe_hervorhebung_forum, $farbe_neuesposting_forum, $farbe_link;
    global $id, $u_id, $http_host, $o_js, $forum_admin;
    
    if (!$postings_array)
        return;
    
    if ($zeige_top) {
        $postings = mysqli_real_escape_string($mysqli_link, "$thread,$postings");
    }
    
    //vom user gelesene postings holen
    $sql = "select u_gelesene_postings from user where u_id=$u_id";
    $query = mysqli_query($mysqli_link, $sql);
    if (mysqli_num_rows($query) > 0)
        $gelesene = mysqli_result($query, 0, "u_gelesene_postings");
    $u_gelesene = unserialize($gelesene);
    
    $sql = "select po_id, date_format(from_unixtime(po_ts), '%d.%m %H:%i') as po_date, po_tiefe,
                po_titel, po_u_id, u_nick, u_level, u_punkte_gesamt, u_punkte_gruppe, u_chathomepage, po_threadorder, po_gesperrt
                from posting
                left join user on po_u_id = u_id
                where po_id in ($postings)";
    
    $query = mysqli_query($mysqli_link, $sql);
    
    //array mit po_id als index anlegen, um wahlfreien zugriff auf postings zu ermoeglichen
    $po_wahlfrei = array();
    
    while ($post = mysqli_fetch_object($query)) {
        $po_wahlfrei[$post->po_id] = $post;
    }
    @mysqli_free_result($query);
    
    echo "<table width=\"730\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n";
    
    if ($zeige_top) {
        
        if ($thread == $highlight)
            $col = $farbe_hervorhebung_forum;
        else $col = $farbe_link;
        
        if ($po_wahlfrei[$thread]->po_gesperrt == 'Y' and !$forum_admin) {
            echo "<td colspan=\"16\">&nbsp;<b><font size=-1 color=\"$col\">"
                . substr($po_wahlfrei[$thread]->po_titel, 0, 60)
                . "</font> <font size=\"-1\" color=\"red\">(gesperrt)</font></b></td>\n";
        } else if ($po_wahlfrei[$thread]->po_gesperrt == 'Y' and $forum_admin) {
            echo "<td colspan=\"16\">&nbsp;<b><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id="
                . $po_wahlfrei[$thread]->po_id
                . "&thread=$thread&aktion=show_posting&seite=$seite\"><font size=-1 color=\"$col\">"
                . substr($po_wahlfrei[$thread]->po_titel, 0, 60)
                . "$f2</a> <font size=\"-1\" color=\"red\">(gesperrt)</font></td>\n";
        } else {
            echo "<td colspan=\"16\">&nbsp;<b><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id="
                . $po_wahlfrei[$thread]->po_id
                . "&thread=$thread&aktion=show_posting&seite=$seite\"><font size=-1 color=\"$col\">"
                . substr($po_wahlfrei[$thread]->po_titel, 0, 60)
                . "$f2</a></td>\n";
        }
        
        if (isset($po_wahlfrei[$thread]->u_email)
            && $po_wahlfrei[$thread]->u_email)
            echo "<td align=\"center\"><b>$f3<a href=\"mailto:"
                . $po_wahlfrei[$thread]->u_email . "\">"
                . $po_wahlfrei[$thread]->autor . "$f4</b></a></td>\n";
        else if (isset($po_wahlfrei[$thread]->autor)
            && $po_wahlfrei[$thread]->autor)
            echo "<td align=\"center\"><b>$f3" . $po_wahlfrei[$thread]->autor
                . "$f4</b></td>\n";
        else echo "<td align=\"center\"><b>$f3" . "$f4</b></td>\n";
        echo "<td align=\"center\"><b>$f3" . $po_wahlfrei[$thread]->po_date
            . "$f4</b></td>\n";
        echo "<td>&nbsp;</td></tr>\n";
        
    }
    
    echo "<tr><td width=\"20\"><img src=\"pics/forum/linie.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"20\"><img src=\"pics/fuell.gif\" width=\"20\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"160\"><img src=\"pics/fuell.gif\" width=\"160\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"150\"><img src=\"pics/fuell.gif\" width=\"150\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"110\"><img src=\"pics/fuell.gif\" width=\"110\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"5\"><img src=\"pics/fuell.gif\" width=\"5\" height=\"1\" border=\"0\"></td>\n";
    echo "<td width=\"5\"><img src=\"pics/fuell.gif\" width=\"5\" height=\"1\" border=\"0\"></td></tr>\n";
    
    //in der richtigen reihenfolge postings durchlaufen
    
    $letztes = array();
    while (list($k, $v) = @each($postings_array)) {
        
        $span = 0;
        $tiefe = $po_wahlfrei[$v]->po_tiefe;
        if ($tiefe >= 15)
            $tiefe = 15;
        
        $letztes[$tiefe] = $po_wahlfrei[$v]->po_threadorder;
        
        echo "<tr>";
        for ($i = 1; $i <= $tiefe; $i++) {
            
            if (($i == $tiefe) && ($letztes[$i]))
                echo "<td><img src=\"pics/forum/ecke2.gif\" width=\"20\" height=\"20\" border=\"0\"></td>\n";
            else if (($i == $tiefe) && (!$letztes[$i]))
                echo "<td><img src=\"pics/forum/ecke1.gif\" width=\"20\" height=\"20\" border=\"0\"></td>\n";
            else if (!($i == $tiefe) && ($letztes[$i]))
                echo "<td><img src=\"pics/fuell.gif\" width=\"20\" height=\"20\" border=\"0\"></td>\n";
            else echo "<td><img src=\"pics/forum/linie.gif\" width=\"20\" height=\"20\" border=\"0\"></td>\n";
            
            $span++;
            
        }
        if ($v == $highlight)
            $col = $farbe_hervorhebung_forum;
        else if (!@in_array($v, $u_gelesene[$th_id]))
            $col = $farbe_neuesposting_forum;
        else $col = $farbe_link;
        
        if ($po_wahlfrei[$v]->po_gesperrt == 'Y' and !$forum_admin) {
            echo "<td colspan=\"" . (16 - $span)
                . "\">&nbsp;<b><font size=-1 color=\"$col\">"
                . substr($po_wahlfrei[$v]->po_titel, 0,
                    (60 - round($span * 2.5)))
                . "</font> <font size=\"-1\" color=\"red\">(gesperrt)</font></b></td>\n";
        } else if ($po_wahlfrei[$v]->po_gesperrt == 'Y' and $forum_admin) {
            echo "<td colspan=\"" . (16 - $span)
                . "\">&nbsp;<b><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id="
                . $po_wahlfrei[$v]->po_id
                . "&thread=$thread&aktion=show_posting&seite=$seite\"><font size=-1 color=\"$col\">"
                . substr($po_wahlfrei[$v]->po_titel, 0,
                    (60 - round($span * 2.5)))
                . " $f2</a> <font size=\"-1\" color=\"red\">(gesperrt)</font></td>\n";
        } else {
            echo "<td colspan=\"" . (16 - $span)
                . "\">&nbsp;<b><a href=\"forum.php?id=$id&http_host=$http_host&th_id=$th_id&po_id="
                . $po_wahlfrei[$v]->po_id
                . "&thread=$thread&aktion=show_posting&seite=$seite\"><font size=-1 color=\"$col\">"
                . substr($po_wahlfrei[$v]->po_titel, 0,
                    (60 - round($span * 2.5))) . " $f2</a></td>\n";
        }
        
        if (!($po_wahlfrei[$v]->u_nick)) {
            echo "<td>$f3<b>unknown</b>$f4</td>\n";
        } else {
            
            $userdata = array();
            $userdata['u_id'] = $po_wahlfrei[$v]->po_u_id;
            $userdata['u_nick'] = $po_wahlfrei[$v]->u_nick;
            $userdata['u_level'] = $po_wahlfrei[$v]->u_level;
            $userdata['u_punkte_gesamt'] = $po_wahlfrei[$v]->u_punkte_gesamt;
            $userdata['u_punkte_gruppe'] = $po_wahlfrei[$v]->u_punkte_gruppe;
            $userdata['u_chathomepage'] = $po_wahlfrei[$v]->u_chathomepage;
            $userlink = user($po_wahlfrei[$v]->po_u_id, $userdata, $o_js,
                FALSE, "&nbsp;", "", "", TRUE, FALSE, 29);
            if ($po_wahlfrei[$v]->u_level == 'Z') {
                echo "<td>$f1 $userdata[u_nick] $f2</td>\n";
            } else {
                echo "<td>$f1 $userlink $f2</td>\n";
            }
        }
        
        echo "<td align=\"center\"><b>$f3" . $po_wahlfrei[$v]->po_date
            . "$f4</b></td>\n";
        echo "<td colspan=\"2\">&nbsp;</td></tr>\n";
    }
    echo "</table>";
}
?>
</body>
</html>