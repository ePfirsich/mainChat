<?php
$coreroot = ".";

// Wandelt eine Background-Variable in den entsprechenden HTML-Source (bgcolor, background) um.
function coreMakeBackground($back)
{
    $backc = trim(strtok("$back", ":"));
    $backp = trim(strtok(":"));
    
    if (("$backc" == "") || ("$backp" == "")) {
        if ("$backc" != "")
            return (" BGCOLOR=\"$backc\"");
        if ("$backp" != "")
            return (" BACKGROUND=\"$backp\"");
    } else return (" BGCOLOR=\"$backc\" BACKGROUND=\"$backp\"");
}

// Wandelt eine Image-Variable in den entsprechenden HTML-Source (Src, Width, Height) um.
function coreMakeImage($image)
{
    $img_w = strtok("$image", ",");
    $img_h = strtok(",");
    $img_p = strtok(",");
    
    return ("SRC=\"$img_p\" WIDTH=\"$img_w\" HEIGHT=\"$img_h\"");
}

function statsOverview($v = "%")
{
    global $STAT_DB_HOST;
    global $STAT_DB_USER;
    global $STAT_DB_PASS;
    global $STAT_DB_NAME;
    global $STAT_TXT;
    global $STAT_TITLE_FONTBEG0;
    global $STAT_TITLE_FONTEND0;
    global $STAT_TITLE_FONTBEG2;
    global $STAT_TITLE_FONTEND2;
    global $STAT_TITLE_BACK1;
    global $grapharray;
    
    @mysqli_connect($STAT_DB_HOST, $STAT_DB_USER, $STAT_DB_PASS);
    mysqli_set_charset($mysqli_link, "utf8mb4");
    
    $m = date("m", time());
    $y = date("Y", time());
    
    $r1 = @mysqli_query($mysqli_link, "SELECT DISTINCT c_host FROM chat WHERE c_timestamp LIKE '$y$m%' AND c_host LIKE '" . mysqli_real_escape_string($mysqli_link, $v) . "' ORDER BY c_host");
    
    if ($r1 > 0) {
        $j = 0;
        $o = @mysqli_num_rows($r1);
        
        if ($o > 0) {
            echo ("<TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACING=\"1\">\n");
            echo ("<TR>\n");
            echo ("<TD" . coreMakeBackground($STAT_TITLE_BACK1) . ">"
                . $STAT_TITLE_FONTBEG2 . $STAT_TXT["0054"]
                . $STAT_TITLE_FONTEND2 . "</TD>\n");
            echo ("<TD" . coreMakeBackground($STAT_TITLE_BACK1)
                . " ALIGN=\"RIGHT\">" . $STAT_TITLE_FONTBEG2
                . $STAT_TXT["0055"] . $STAT_TITLE_FONTEND2 . "</TD>\n");
            echo ("<TD" . coreMakeBackground($STAT_TITLE_BACK1)
                . " ALIGN=\"RIGHT\">" . $STAT_TITLE_FONTBEG2
                . $STAT_TXT["0056"] . $STAT_TITLE_FONTEND2 . "</TD>\n");
            echo ("</TR>\n");
            
            while ($j < $o) {
                $c_host = @mysql_result($r1, $j, "c_host");
                
                statsResetMonth($y, $m);
                
                $r0 = @mysqli_query($mysqli_link, 
                    "SELECT * FROM chat WHERE c_timestamp LIKE '$y$m%' AND c_host='". mysqli_real_escape_string($mysqli_link, $c_host) . "' ORDER BY c_timestamp");
                
                if ($r0 > 0) {
                    $i = 0;
                    $u = 0;
                    $n = @mysqli_num_rows($r0);
                    
                    while ($i < $n) {
                        $c_timestamp = @mysql_result($r0, $i, "c_timestamp");
                        $c_users = @mysql_result($r0, $i, "c_users");
                        
                        $x = trim(substr($c_timestamp, 6, 2));
                        
                        if ($c_users > $grapharray["$x"])
                            $grapharray["$x"] = $c_users;
                        
                        $i++;
                    }
                    
                    reset($grapharray);
                    
                    while (list($i, $n) = each($grapharray)) {
                        if ($n > $u)
                            $u = $n;
                    }
                    
                    if ($c_host == "")
                        $c_host = $STAT_TXT["0080"];
                    
                    echo ("<TR>\n");
                    echo ("<TD>");
                    echo ($STAT_TITLE_FONTBEG0);
                    echo ($c_host);
                    echo ($STAT_TITLE_FONTEND0);
                    echo ("</TD>\n");
                    
                    echo ("<TD ALIGN=\"RIGHT\">");
                    echo ($STAT_TITLE_FONTBEG0);
                    echo ($u);
                    echo ($STAT_TITLE_FONTEND0);
                    echo ("</TD>\n");
                    
                    echo ("<TD ALIGN=\"RIGHT\">");
                    echo ($STAT_TITLE_FONTBEG0);
                    echo ("&nbsp;&nbsp;" . $m . "/" . $y);
                    echo ($STAT_TITLE_FONTEND0);
                    echo ("</TD>\n");
                    echo ("</TR>\n");
                }
                
                $j++;
            }
            
            echo ("</TABLE>\n");
        }
    }
}

function statsResetMonth($year, $month)
{
    global $grapharray;
    
    unset($grapharray);
    
    $i = 1;
    
    while ($i <= 31) {
        
        if (checkdate($month, $i, $year)) {
            $x = SPrintF("%02d", $i);
            
            $grapharray["$x"] = 0;
        }
        
        $i++;
    }
}

function statsResetHours($zeit, $hours)
{
    global $grapharray;
    
    unset($grapharray);
    
    $i = 0;
    $c = intval(trim(date("H", $zeit)));
    
    while ($i < $hours) {
        $x = SPrintF("%02d", $c);
        
        $grapharray["$x"] = 0;
        
        $i++;
        $c++;
        
        if ($c > 23)
            $c = 0;
    }
}

function statsPrintGraph($title, $text_l, $text_b)
{
    global $grapharray;
    global $STAT_TXT;
    global $STAT_BAR_HEIGHT;
    global $STAT_BAR_IMAGE_T;
    global $STAT_BAR_IMAGE_M;
    global $STAT_BAR_IMAGE_B;
    global $STAT_BAR_BACK0;
    global $STAT_BAR_BACK1;
    global $STAT_BAR_BACK2;
    global $STAT_BAR_FONTBEG0;
    global $STAT_BAR_FONTEND0;
    global $STAT_BAR_FONTBEG1;
    global $STAT_BAR_FONTEND1;
    global $STAT_BAR_FONTBEG2;
    global $STAT_BAR_FONTEND2;
    global $STAT_BAR_FONTBEG3;
    global $STAT_BAR_FONTEND3;
    $msg = "";
    
    if ((isset($grapharray)) && (count($grapharray) > 0)) {
        $img_w = trim(strtok($STAT_BAR_IMAGE_M, ","));
        $img_h = trim(strtok(","));
        $img_p = trim(strtok(","));
        
        /* Als erstes wird der größte Eintrag im Array und einige Werte	*/
        /* ermittelt.																										*/
        
        $b = 0;
        $h = 0;
        $c = 2;
        $d = 0;
        $t = 0;
        $u = 0;
        
        if ($title == "")
            $title = $STAT_TXT["0080"];
        
        reset($grapharray);
        
        while (list($i, $v) = each($grapharray)) {
            if ($v > $b)
                $b = $v;
            
            if ($v > 0) {
                $t += $v;
                $u += 1;
            }
            
            $c++;
        }
        
        if ($b > 0)
            $h = ($STAT_BAR_HEIGHT / $b);
        else $h = 0;
        
        if ($u > 0)
            $d = intval($t / $u);
        else $d = 0;
        
        /* Linke Beschriftung und die Tabelle mit den Balken anzeigen	*/
        /* lassen.																										*/
        
        reset($grapharray);
        
        $msg .= "<DIV ALIGN=\"CENTER\">\n";
        $msg .= "<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">\n";
        
        if ($title != "") {
            $msg .= "<TR>\n";
            $msg .= "<TD" . coreMakeBackground($STAT_BAR_BACK2)
                . " COLSPAN=\"$c\" ALIGN=\"CENTER\">";
            $msg .= $STAT_BAR_FONTBEG3;
            $msg .= $title;
            $msg .= $STAT_BAR_FONTEND3;
            $msg .= "</TD>\n";
            $msg .= "</TR>\n";
        }
        
        $msg .= "<TR" . coreMakeBackground($STAT_BAR_BACK0) . ">\n";
        $msg .= "<TD" . coreMakeBackground($STAT_BAR_BACK1) . ">";
        $msg .= $STAT_BAR_FONTBEG2;
        
        $t0 = 0;
        $t1 = strlen($text_l);
        
        while ($t0 < $t1) {
            $msg .= substr($text_l, $t0, 1) . "<BR>";
            
            $t0++;
        }
        
        $msg .= $STAT_BAR_FONTEND2;
        $msg .= "</TD>\n";
        
        while (list($i, $v) = each($grapharray)) {
            if ($v > 0) {
                $s = intval($v * $h);
                
                $msg .= "<TD VALIGN=\"BOTTOM\" ALIGN=\"CENTER\">";
                $msg .= $STAT_BAR_FONTBEG0;
                $msg .= $v;
                $msg .= "<BR>";
                
                if (strlen($STAT_BAR_IMAGE_T) > 0)
                    $msg .= "<IMG " . coreMakeImage($STAT_BAR_IMAGE_T)
                        . "><BR>";
                
                $msg .= "<IMG SRC=\"$img_p\" BORDER=\"0\" WIDTH=\"$img_w\" HEIGHT=\"$s\">";
                $msg .= "<BR>";
                
                if (strlen($STAT_BAR_IMAGE_B) > 0)
                    $msg .= "<IMG " . coreMakeImage($STAT_BAR_IMAGE_B)
                        . "><BR>";
                
                $msg .= $STAT_BAR_FONTEND0;
                $msg .= "</TD>\n";
            } else {
                $msg .= "<TD VALIGN=\"BOTTOM\" ALIGN=\"CENTER\">";
                $msg .= $STAT_BAR_FONTBEG0;
                $msg .= $v;
                $msg .= $STAT_BAR_FONTEND0;
                $msg .= "</TD>\n";
            }
        }
        
        $msg .= "<TD>&nbsp;</TD>\n";
        $msg .= "</TR>\n";
        $msg .= "<TR" . coreMakeBackground($STAT_BAR_BACK1) . ">\n";
        $msg .= "<TD>&nbsp;</TD>\n";
        
        /* Unter Leiste mit den Beschriftungen der einzelnen Balken aus-	*/
        /* geben.																													*/
        
        reset($grapharray);
        
        while (list($i, $v) = each($grapharray)) {
            $msg .= "<TD>";
            $msg .= $STAT_BAR_FONTBEG1;
            $msg .= $i;
            $msg .= $STAT_BAR_FONREND1;
            $msg .= "</TD>\n";
        }
        
        $msg .= "<TD>";
        $msg .= $STAT_BAR_FONTBEG2;
        $msg .= $text_b;
        $msg .= $STAT_BAR_FONREND2;
        $msg .= "</TD>\n";
        $msg .= "</TR>\n";
        $msg .= "</TABLE>\n";
        
        /* Durchschnittliche Anzahl der Benutzer und höchste Anzahl der	*/
        /* Benutzer ausgeben. */
        
        $msg .= "<P ALIGN=\"CENTER\">";
        $msg .= $STAT_BAR_FONTBEG0;
        $msg .= $STAT_TXT["0100"] . " $d - " . $STAT_TXT["0101"] . " $b";
        $msg .= $STAT_BAR_FONTEND0;
        $msg .= "</P>\n";
        $msg .= "</DIV>\n";
    }
    
    unset($grapharray);
    return $msg;
}

?>
