<?php
require_once("functions-registerglobals.php");

if (!isset($_SERVER["HTTP_REFERER"])) {
    $_SERVER["HTTP_REFERER"] = "";
}

if ((!isset($http_host) && !isset($login)) || ($frame == 1)) {
    // Falls diese Seite ohne Parameter aufgerufen wird, wird das oberste Frameset ausgegeben
    
    // Funktionen und Config laden, Host bestimmen
    require_once("functions-init.php");
    
    // Backdoorschutz über den HTTP_REFERER
    if (isset($chat_referer) && $chat_referer != "") {
        
        if (!preg_match("/" . $chat_referer . "/", $_SERVER["HTTP_REFERER"])
            && $aktion != "neu") {
            echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                . "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"5; URL=$chat_login_url\">\n"
                . "</HEAD>\n";
            zeige_kopf();
            $chat_login_url = "<a href=\"$chat_login_url\">$chat_login_url</a>";
            echo str_replace("%webseite%", $chat_login_url, $t['login26']);
            zeige_fuss();
            exit();
        }
    }
    
    // Soll Chatserver dynamisch gewählt werden?
    $chatserver = "";
    if ($chatserver_dynamisch) {
        
        // Datenbankconnect
        $connect = @mysqli_connect($chatserver_mysqlhost, $mysqluser, $mysqlpass);
        if ($connect) {
            
            mysqli_set_charset($mysqli_link, "utf8mb4");
            
            // Alle Hosts bestimmen, die in den letzten 150sek erreichbar waren und die den Typ "apache" haben...
            mysqli_select_db($connect, "chat_info");
            $result = mysqli_query($connect, 
                "select h_host from host where (NOW()-h_time)<150 and h_type='apache' order by h_av1");
            $rows = mysql_numrows($result);
            
            if ($rows > 0) {
                if ($rows > 1)
                    $max = 1;
                if ($rows > 4)
                    $max = 2;
                if ($rows > 6)
                    $max = 3;
                if ($rows > 9)
                    $max = 4;
                if ($max > 0) {
                    $chatserver = $serverprotokoll . "://"
                        . mysql_result($result, mt_rand(0, $max), "h_host")
                        . "/";
                } else {
                    $chatserver = $serverprotokoll . "://"
                        . mysql_result($result, 0, "h_host") . "/";
                }
            } else {
                $chatserver = "";
            }
            
            mysqli_free_result($result);
            mysqli_close($connect);
            
        } else {
            echo "<HTML><HEAD><TITLE>mainChat - http://www.mainchat.de </TITLE><META CHARSET=UTF-8></HEAD><BODY>"
                . "<P>Der mainChat ist leider aus technischen Gründen nicht erreichbar. "
                . "Bitte versuchen Sie es später noch einmal.</P></BODY></HTML>\n";
            exit();
        }
    }
    
    // Optional Frame links
    if (strlen($frame_links) > 5) {
        $opt = $frame_links_size . ",";
    } else {
        $opt = "";
    }
    
    // Frameset ausgeben
    echo "<HTML><HEAD>\n<TITLE>" . $body_titel
        . "</TITLE><META CHARSET=UTF-8>\n" . $metatag . "\n</HEAD>"
        . "<FRAMESET COLS=\"" . $opt
        . "100%,*\" BORDER=\"0\" FRAMEBORDER=\"0\">\n";
    
    if ($opt)
        echo "<FRAME SRC=\"" . $frame_links
            . "\" NAME=\"leftframe\" NORESIZE>\n";
    
    // Login durch das äußere Frameset durchschleifen
    if ($frame == 1 && $aktion == "login") {
        $url = $chatserver . "index.php?http_host=" . $http_host
            . "&javascript=" . $javascript . "&login=" . urlencode($login)
            . "&passwort=" . urlencode($passwort) . "&eintritt=" . $eintritt
            . "&aktion=" . $aktion . "&los=" . $los;
        if (is_array($f))
            foreach ($f as $key => $val) {
                $url .= "&f[" . $key . "]=" . urlencode($val);
            }
        
        // Für Kompatibilität zwischen 4.21 und 5.0.0
        if (isset($md5crypt)) {
            $url .= "&md5crypt=" . $md5crypt;
        }
        
        echo "<FRAME SRC=\"" . $url
            . "\" SCROLLING=\"AUTO\" NAME=\"topframe\" NORESIZE>\n</FRAMESET><NOFRAMES>\n";
        
        // E-Mail bestätigung durch das äußere Frameset durchschleifen
    } elseif ($frame == 1) {
        echo "<FRAME SRC=\"" . $chatserver
            . "index.php?http_host=$http_host&aktion=$aktion&email=$email&hash=$hash\" SCROLLING=\"AUTO\" NAME=\"topframe\" NORESIZE>\n"
            . "</FRAMESET><NOFRAMES>\n";
        
        // Normales Frameset mit Loginmaske
    } else {
        if (isset($_SERVER["HTTP_REFERER"])) {
            $ref = $_SERVER["HTTP_REFERER"];
        } else {
            $ref = "";
        }
        
        echo "<FRAME SRC=\"" . $chatserver
            . "index.php?http_host=$http_host&refereroriginal=" . $ref
            . "\" SCROLLING=\"AUTO\" NAME=\"topframe\" NORESIZE>\n"
            . "</FRAMESET><NOFRAMES>\n";
    }
    
    if ($noframes) {
        echo $noframes . "\n";
    } else {
        echo "<P><DIV ALIGN=\"CENTER\"><A HREF=\"index.php\">weiter</A></DIV>\n";
    }
    echo "</NOFRAMES></HTML>\n";
    
    exit();
    
} else {
    
    // Seite wurde mit Parametern innerhalb des Framesets aufgerufen: Eingangsseite ausgeben
    
    // Funktionen laden
    require("functions.php");
    require("functions.php-werbung.php");
    
    // Backdoorschutz über den HTTP_REFERER - wir prüfen ob bei gesetztem HTTP_HOST ob die index.php in eigenem Frameset läuft sonst => Fehler
    if (isset($chat_referer) && $chat_referer != "") {
        $tmp = parse_url($serverprotokoll . "://" . $_SERVER["HTTP_HOST"]);
        $chat_referer2 = $tmp['scheme'] . "://" . $tmp['host'];
        
        if ((!preg_match("#" . $chat_referer2 . "#", $_SERVER["HTTP_REFERER"]))
            && (!preg_match("#" . $chat_referer . "#", $refereroriginal))
            && $aktion != "neu") {
            echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                . "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"5; URL=$chat_login_url\">\n"
                . "</HEAD>\n";
            zeige_kopf();
            $chat_login_url = "<a href=\"$chat_login_url\">$chat_login_url</a>";
            echo str_replace("%webseite%", $chat_login_url, $t['login26']);
            zeige_fuss();
            exit();
        }
    }
    
    // Willkommen definieren
    if (isset($_SERVER["PHP_AUTH_USER"])) {
        $willkommen = str_replace("%PHP_AUTH_USER%", $_SERVER["PHP_AUTH_USER"],
            $t['willkommen1']);
    } else {
        $willkommen = $t['willkommen2'];
    }
    
    // Body-Tag definieren
    $body_tag = "<BODY BGCOLOR=\"$farbe_background\" ";
    if (strlen($grafik_background) > 0) {
        $body_tag = $body_tag . "BACKGROUND=\"$grafik_background\" ";
    }
    $body_tag = $body_tag . "TEXT=\"$farbe_text\" " . "LINK=\"$farbe_link\" "
        . "VLINK=\"$farbe_vlink\" " . "ALINK=\"$farbe_vlink\">\n";
    
    // Liste offener Räume definieren (optional)
    if ($raum_auswahl && (!isset($beichtstuhl) || !$beichtstuhl)) {
        // Falls eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
        if (strlen($eintrittsraum) == 0) {
            $eintrittsraum = $lobby;
        }
        
        // Raumauswahlliste erstellen
        $query = "SELECT r_name,r_id FROM raum "
            . "WHERE (r_status1='O' OR r_status1='E' OR r_status1 LIKE BINARY 'm') AND r_status2='P' "
            . "ORDER BY r_name";
        
        $result = @mysqli_query($conn, $query);
        if ($result) {
            $rows = mysqli_num_rows($result);
        } else {
            echo "<P><B>Datenbankfehler:</B> " . mysqli_error($mysqli_link) . ", "
                . mysqli_errno($mysqli_link) . "</P>";
            die();
        }
        if ($communityfeatures && $forumfeatures) {
            $raeume = "<TD><B>" . $t['login22'] . "</B><BR>";
        } else {
            $raeume = "<TD><B>" . $t['login12'] . "</B><BR>";
        }
        $raeume .= $f1 . "<SELECT NAME=\"eintritt\">";
        
        if ($communityfeatures && $forumfeatures)
            $raeume = $raeume
                . "<OPTION VALUE=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
        if ($rows > 0) {
            $i = 0;
            while ($i < $rows) {
                $r_id = mysql_result($result, $i, "r_id");
                $r_name = mysql_result($result, $i, "r_name");
                if ((!isset($eintritt) AND $r_name == $eintrittsraum)
                    || (isset($eintritt) AND $r_id == $eintritt)) {
                    $raeume = $raeume
                        . "<OPTION SELECTED VALUE=\"$r_id\">$r_name\n";
                } else {
                    $raeume = $raeume . "<OPTION VALUE=\"$r_id\">$r_name\n";
                }
                $i++;
            }
        }
        if ($communityfeatures && $forumfeatures)
            $raeume = $raeume
                . "<OPTION VALUE=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
        $raeume = $raeume . "</SELECT>" . $f2 . "</TD>\n";
        mysqli_free_result($result);
    } else {
        if (strlen($eintrittsraum) == 0) {
            $eintrittsraum = $lobby;
        }
        $lobby_id = RaumNameToRaumID($eintrittsraum);
        $raeume = "<TD><INPUT TYPE=HIDDEN NAME=\"eintritt\" VALUE=$lobby_id></TD>\n";
    }
    
    // Browser prüfen
    if (ist_netscape()) {
        $eingabe_breite = 12;
    } else {
        $eingabe_breite = 20;
    }
    
    // Logintext definieren
    $logintext = "<TABLE BORDER=0 CELLSPACING=0 WIDTH=100%><TR><TD><B>"
        . $t['login8'] . "</B><BR>" . $f1
        . "<INPUT TYPE=\"TEXT\" NAME=\"login\" VALUE=\"";
    if (isset($login))
        $logintext .= $login;
    $logintext .= "\" SIZE=$eingabe_breite>" . $f2 . "</TD>\n" . "<TD><B>"
        . $t['login9'] . "</B><BR>" . $f1
        . "<INPUT TYPE=\"PASSWORD\" NAME=\"passwort\" SIZE=$eingabe_breite>"
        . $f2 . "</TD>\n" . $raeume . "<TD><BR>" . $f1
        . "<B><INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"" . $t['login10']
        . "\"></B>\n"
        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"login\">" . $f2
        . "</TD>\n" . "</TR></TABLE>\n" . $t['login3'];
    
    // SSL?
    if (($ssl_login) || (isset($SSLRedirect) && $SSLRedirect == "1")) {
        $chat_file = "https://" . $http_host . $chat_url;
    }
    
    // IP bestimmen und prüfen. Ist Login erlaubt?
    $abweisen = false;
    $warnung = false;
    $ip_adr = $_SERVER["REMOTE_ADDR"];
    $ip_name = @gethostbyaddr($ip_adr);
    
    // Sperrt den Chat, wenn in der Sperre Domain "-GLOBAL-" ist
    $query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GLOBAL-'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $abweisen = true;
    }
    mysqli_free_result($result);
    
    // Sperrt den Chat für bestimmte Browser komplett
    if (($_SERVER["HTTP_USER_AGENT"] == "Spamy v3.0 with 32 Threads")
        || ($_SERVER["HTTP_USER_AGENT"]
            == "Powered by Spamy The Spambot v5.2.1")) {
        $abweisen = true;
    }
    
    // Gastsperre aktiv? Wird beim Login und beim AGB Login ausgewertet
    $temp_gast_sperre = false;
    // Wenn die dbase = "mainchat" und in Sperre = "-GAST-"
    // dann Gastlogin gesperrt
    $query = "SELECT is_domain FROM ip_sperre WHERE is_domain = '-GAST-'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $temp_gast_sperre = true;
    }
    mysqli_free_result($result);
    
    $query = "SELECT * FROM ip_sperre "
        . "WHERE (SUBSTRING_INDEX(is_ip,'.',is_ip_byte) "
        . " LIKE SUBSTRING_INDEX('" . mysqli_real_escape_string($mysqli_link, $ip_adr) . "','.',is_ip_byte) AND is_ip IS NOT NULL) "
        . "OR (is_domain LIKE RIGHT('" . mysqli_real_escape_string($mysqli_link, $ip_name) . "',LENGTH(is_domain)) AND LENGTH(is_domain)>0)";
    $result = mysqli_query($conn, $query);
    $rows = mysqli_num_rows($result);
    
    if ($rows > 0) {
        while ($row = mysqli_fetch_object($result)) {
            if ($row->is_warn == "ja") {
                // Warnung ausgeben
                $warnung = true;
                $infotext = $row->is_infotext;
            } else {
                // IP ist gesperrt
                $abweisen = true;
            }
        }
    }
    mysqli_free_result($result);
    
    // HTTP_X_FORWARDED_FOR IP bestimmen und prüfen. Ist Login erlaubt?
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ip_adr = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        $ip_adr = "";
    }
    
    if (!$abweisen && $ip_adr && $ip_adr != $_SERVER["REMOTE_ADDR"]) {
        $ip_name = @gethostbyaddr($ip_adr);
        $query = "SELECT * FROM ip_sperre "
            . "WHERE (SUBSTRING_INDEX(is_ip,'.',is_ip_byte) "
            . " LIKE SUBSTRING_INDEX('" . mysqli_real_escape_string($mysqli_link, $ip_adr) . "','.',is_ip_byte) AND is_ip IS NOT NULL) "
            . "OR (is_domain LIKE RIGHT('" . mysqli_real_escape_string($mysqli_link, $ip_name) . "',LENGTH(is_domain)) AND LENGTH(is_domain)>0)";
        $result = mysqli_query($conn, $query);
        $rows = mysqli_num_rows($result);
        
        if ($rows > 0) {
            while ($row = mysqli_fetch_object($result)) {
                if ($row->is_warn == "ja") {
                    // Warnung ausgeben
                    $warnung = true;
                    $infotext = $row->is_infotext;
                } else {
                    // IP ist gesperrt
                    $abweisen = true;
                }
            }
        }
        mysqli_free_result($result);
    }
    
    // zweite Prüfung, gibts was, was mit "*" in der mitte schafft? für p3cea9*.t-online.de
    $query = "SELECT * FROM ip_sperre WHERE is_domain like '_%*%_'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_object($result)) {
            $part = explode("*", $row->is_domain, 2);
            if ((strlen($part[0]) > 0) && (strlen($part[1]) > 0)) {
                if (substr($ip_name, 0, strlen($part[0])) == $part[0]
                    && substr($ip_name, strlen($ip_name) - strlen($part[1]),
                        strlen($part[1])) == $part[1]) {
                    
                    // IP stimmt überein
                    if ($row->is_warn == "ja") {
                        // Warnung ausgeben
                        $warnung = true;
                        $infotext = $row->is_infotext;
                    } else {
                        // IP ist gesperrt
                        $abweisen = true;
                    }
                }
            }
        }
    }
    mysqli_free_result($result);
    
    // Wenn $abweisen=true, dann ist Login ist für diesen User gesperrt
    // Es sei denn wechsel Forum -> Chat, dann "Relogin", und wechsel trotz IP Sperre in Chat möglich
    if ($abweisen && $aktion != "relogin" && strlen($login) > 0) {
        // test: ist user=admin -> dann nicht abweisen...
        $query = "select u_nick,u_level from user where u_nick='"
            . mysqli_real_escape_string($check_name), coreCheckName($login)
            . "' AND (u_level in ('S','C'))";
        $r = mysqli_query($conn, $query);
        $rw = mysqli_num_rows($r);
        
        if ($rw == 1 && (strlen($aktion) > 0)) {
            $abweisen = false;
        }
        mysqli_free_result($r);
        
        // Prüfung nun auf Admin beendet
        // Nun Prüfung ob genug Punkte
        $durchgangwegenpunkte = 0;
        
        if ($communityfeatures && $loginwhileipsperre <> 0) {
            // Test auf Punkte 
            $query = "select u_id, u_nick,u_level,u_punkte_gesamt from user "
                . "where (u_nick='" . mysqli_real_escape_string($check_name), coreCheckName($login)
                . "' OR u_name='" . mysqli_real_escape_string($mysqli_link, "$login, $check_name") . "') "
                . "AND (u_level in ('A','C','G','M','S','U')) ";
            "AND u_passwort = encrypt('" . mysqli_real_escape_string($mysqli_link, $passwort) . "',u_passwort)";
            // Nutzt die MYSQL -> Unix crypt um DES, SHA256, etc. automatisch zu erkennen
            // Durchleitung wg. Punkten im Fall der MD5() verschlüsselung wird nicht gehen
            $r = mysqli_query($conn, $query);
            $rw = mysqli_num_rows($r);
            
            if ($rw == 1 && strlen($aktion) > 0) {
                $row = mysqli_fetch_object($r);
                if ($row->u_punkte_gesamt >= $loginwhileipsperre) {
                    // genügend Punkte
                    // Login zulassen
                    $durchgangwegenpunkte = 1;
                    $abweisen = false;
                    $t_u_id = $row->u_id;
                    $t_u_nick = $row->u_nick;
                    $infotext = str_replace("%punkte%", $row->u_punkte_gesamt,
                        $t['ipsperre2']);
                }
            }
            mysqli_free_result($r);
        }
        
        if ($durchgangwegenpunkte == 1) {
            // Wenn User wegen Punkte durch IP Sperre kommen, dann Meldung an alle Admins
            if ($eintritt == 'forum') {
                $raumname = " (" . $whotext[2] . ")";
            } else {
                $query2 = "SELECT r_name from raum where r_id=" . intval($eintritt);
                $result2 = mysqli_query($conn, $query2);
                if ($result2 AND mysqli_num_rows($result2) > 0) {
                    $raumname = " (" . mysql_result($result2, 0, 0) . ") ";
                } else {
                    $raumname = "";
                }
                mysqli_free_result($result2);
            }
            
            $query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
            $result2 = mysqli_query($conn, $query2);
            if ($result2 AND mysqli_num_rows($result2) > 0) {
                $txt = str_replace("%ip_adr%", $ip_adr, $t['ipsperre1']);
                $txt = str_replace("%ip_name%", $ip_name, $txt);
                $txt = str_replace("%is_infotext%", $infotext, $txt);
                while ($row2 = mysqli_fetch_object($result2)) {
                    $ur1 = "user.php?http_host=<HTTP_HOST>&id=<ID>&aktion=zeig&user=$t_u_id";
                    $ah1 = "<A HREF=\"$ur1\" TARGET=\"$t_u_nick\" onclick=\"neuesFenster('$ur1','"
                        . $t_u_nick . "'); return(false);\">";
                    $ah2 = "</A>";
                    system_msg("", 0, $row2->o_user, $system_farbe,
                        str_replace("%u_nick%",
                            $ah1 . $t_u_nick . $ah2 . $raumname, $txt));
                }
                unset($infotext);
                unset($t_u_id);
                unset($u_nick);
            }
            mysqli_free_result($result2);
        }
    }
    
    if ($abweisen && (strlen($aktion) > 0) && $aktion <> "relogin") {
        $aktion = "abweisen";
        unset($logintext);
    }
    
    // Login ist für alle User gesperrt
    if (($chat_offline_kunde)
        || ((isset($chat_offline)) && (strlen($chat_offline) > 0))) {
        $aktion = "gesperrt";
    }
    
    // Ausloggen, falls eingeloggt
    if ($aktion == "logoff") {
        // Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum
        id_lese($id);
        
        // Header ausgeben
        if (($layout_bodytag) && (!isset($chat_logout_url)))
            echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                . "\n</HEAD>\n";
        
        // Logoff falls noch online
        if (strlen($u_id) > 0) {
            verlasse_chat($u_id, $u_nick, $o_raum);
            sleep(2);
            logout($o_id, $u_id, "index->logout");
        }
        
        if (isset($chat_logout_url) && ($chat_logout_url <> "")) {
            echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                . "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; URL=$chat_logout_url\">\n"
                . "</HEAD>\n";
            zeige_kopf();
            $chat_login_url = "<a href=\"$chat_logout_url\">$chat_logout_url</a>";
            echo ("Sie werden nun auf $chat_logout_url weitergeleitet");
            // echo "chat_refer = $chat_referer<BR>HTTP_REFERER=$HTTP_SERVER_VARS[HTTP_REFERER] <BR>";
            zeige_fuss();
            exit();
        }
        
    }
    
    // Falls in Loginmaske/Nutzungsbestimmungen auf Abbruch geklickt wurde
    if ($los == $t['login18'] && $aktion == "login")
        $aktion = "";
    
    // Titeltext der Loginbox setzen, Link auf Registrierung optional ausgeben
    
    if ($neuregistrierung_deaktivieren) {
        $login_titel = $ft0 . $t['default1'] . $ft1;
        if ($aktion == "neu")
            $aktion = "";
        if ($aktion == "neu2")
            $aktion = "";
        if ($aktion == "mailcheckm")
            $aktion = "";
    } else if ($t['login14']) {
        $login_titel = $ft0 . $t['default1']
            . " [<A HREF=\"$chat_file?http_host=$http_host&aktion=neu\">"
            . $ft0 . $t['login14'] . $ft1 . "</A>]" . $ft1;
    } else {
        $login_titel = $ft0 . $t['default1'] . $ft1;
    }
    
    if (!isset($chatserver) || $chatserver == "") {
        $chatserver = $serverprotokoll . "://" . $_SERVER['HTTP_HOST'] . "/";
    }
    
    if ($aktion == "neu" && $pruefe_email == "1" && isset($f['u_adminemail'])
        && $hash != md5($f['u_adminemail'] . "+" . date("Y-m-d"))) {
        if ($layout_bodytag)
            echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                . "\n</HEAD>\n";
        zeige_kopf();
        echo "<P><B>Fehler:</B> Die URL ist nicht korrekt! Bitte melden Sie sich "
            . "<A HREF=\"" . $chatserver
            . "index.php?http_host=$http_host\">hier</A> neu an.</P>";
        
        zeige_fuss();
        exit;
    }
    
    if ($aktion == "neu" && $pruefe_email == "1" && $los != $t['neu22'])
        $aktion = "mailcheck";
    if ($aktion == "neu2") {
        $aktion = "mailcheckm";
    }
    
    if (isset($f['u_adminemail'])) {
        $ro = "readonly";
    }
    
    if ($aktion == "mailcheck" && isset($email) && isset($hash)) {
        $email = mysqli_real_escape_string($mysqli_link, $email);
        $query = "SELECT * FROM mail_check WHERE email = '$email'";
        $result = mysqli_query($mysqli_link, $query);
        if ($result && mysql_numrows($result) == 1) {
            $a = mysqli_fetch_array($result);
            $hash2 = md5($a['email'] . "+" . $a['datum']);
            if ($hash == $hash2) {
                
                $aktion = "neu";
                $ro = "readonly";
                $f['u_adminemail'] = $email;
            } else {
                // Header ausgeben
                if ($layout_bodytag)
                    echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                        . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                        . "\n</HEAD>\n";
                zeige_kopf();
                echo "<P><B>Fehler:</B> Die URL ist nicht korrekt! Bitte melden Sie sich "
                    . "<A HREF=\"" . mysqli_real_escape_string($mysqli_link, $chatserver)
                    . "index.php?http_host=" . mysqli_real_escape_string($mysqli_link, $http_host) . "\">hier</A> neu an.</P>";
                $query = "DELETE FROM mail_check WHERE email = '$email'";
                mysqli_query($mysqli_link, $query);
                zeige_fuss();
                exit;
            }
        } else {
            // Header ausgeben
            if ($layout_bodytag)
                echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                    . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                    . "\n</HEAD>\n";
            zeige_kopf();
            echo "<P><B>Fehler:</B> Diese Mail wurde bereits für eine Anmeldung benutzt! Bitte melden Sie sich "
                . "<A HREF=\"" . $chatserver
                . "index.php?http_host=$http_host\">hier</A> neu an.</P>";
            zeige_fuss();
            exit;
        }
    }
    
    switch ($aktion) {
        case "passwort_neu":
            if ($layout_bodytag)
                echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                    . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                    . "\n</HEAD>\n";
            zeige_kopf();
            echo $willkommen;
            
            unset($richtig);
            unset($u_id);
            
            $richtig = 0;
            $fehlermeldung = "";
            
            if (isset($email) && isset($nickname) && isset($hash)) {
                $nickname = mysqli_real_escape_string($check_name), coreCheckName($nickname);
                $email = mysqli_real_escape_string($mysqli_link, urldecode($email));
                $query = "SELECT u_id, u_login, u_nick, u_name, u_passwort, u_adminemail, u_punkte_jahr FROM user "
                    . "WHERE u_nick = '$nickname' AND u_level = 'U' AND u_adminemail = '$email' LIMIT 1";
                $result = mysqli_query($mysqli_link, $query);
                if ($result && mysql_numrows($result) == 1) {
                    $a = mysqli_fetch_array($result);
                    $hash2 = md5(
                        $a['u_id'] . $a['u_login'] . $a['u_nick']
                            . $a['u_name'] . $a['u_passwort']
                            . $a['u_adminemail'] . $a['u_punkte_jahr']);
                    if ($hash == $hash2) {
                        $richtig = 1;
                        $u_id = $a['u_id'];
                    } else {
                        $fehlermeldung .= $t['pwneu11'];
                    }
                } else {
                    $fehlermeldung .= $t['pwneu11'];
                }
                mysqli_free_result($result);
            } else if (isset($email) && isset($nickname)) {
                $nickname = mysqli_real_escape_string($check_name), coreCheckName($nickname);
                $email = mysqli_real_escape_string($mysqli_link, urldecode($email));
                if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
                    mysqli_real_escape_string($mysqli_link, $email))) {
                    $fehlermeldung .= $t['pwneu5'] . '<br>';
                }
                
                if ($fehlermeldung == "") {
                    $query = "SELECT u_id, u_login, u_nick, u_name, u_passwort, u_adminemail, u_punkte_jahr FROM user "
                        . "WHERE u_nick = '$nickname' AND u_level = 'U' AND u_adminemail = '$email' LIMIT 2";
                    $result = mysqli_query($conn, $query);
                    if ($result && mysql_numrows($result) == 1) {
                        $a = mysqli_fetch_array($result);
                        $hash = md5(
                            $a['u_id'] . $a['u_login'] . $a['u_nick']
                                . $a['u_name'] . $a['u_passwort']
                                . $a['u_adminemail'] . $a['u_punkte_jahr']);
                        
                        $email = urlencode($a['u_adminemail']);
                        $link = $serverprotokoll . "://" . $http_host
                            . $chatserver . $_SERVER['PHP_SELF']
                            . "?http_host=$http_host&aktion=passwort_neu&frame=1&email="
                            . $email . "&nickname=" . $nickname . "&hash="
                            . $hash;
                        
                        $text2 = str_replace("%link%", $link, $t['pwneu9']);
                        $text2 = str_replace("%hash%", $hash, $text2);
                        $text2 = str_replace("%nickname%", $a['u_nick'], $text2);
                        $email = urldecode($a['u_adminemail']);
                        $text2 = str_replace("%email%", $email, $text2);
                        
                        $mailbetreff = $t['pwneu8'];
                        $mailempfaenger = $email;
                        $header = "\n" . "X-MC-IP: " . $_SERVER["REMOTE_ADDR"]
                            . "\n" . "X-MC-TS: " . time();
                        
                        mail($mailempfaenger, $mailbetreff, $text2,
                            "From: $webmaster ($chat)" . $header);
                        
                        echo $t['pwneu7'];
                        unset($hash);
                    } else {
                        $fehlermeldung .= $t['pwneu6'] . '<br>';
                        unset($hash);
                    }
                    mysqli_free_result($result);
                }
            } else {
                echo $t['pwneu1'];
            }
            
            if (!$richtig) {
                if ($fehlermeldung <> "") {
                    if (isset($hash)) {
                        echo $t['pwneu7'];
                    } else {
                        echo $t['pwneu1'];
                    }
                    echo "<p><font color=\"red\"><b>$fehlermeldung</b></font></p>\n";
                }
                echo "<form action=\"index.php\">\n"
                    . "<table border=0 cellpadding=5 cellspacing=0>\n";
                if (isset($email) && isset($nickname) && $email <> ""
                    && $nickname <> ""
                    && (isset($hash) || $fehlermeldung == "")) {
                    if (!isset($hash))
                        $hash = "";
                    echo "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>"
                        . $t['pwneu2']
                        . "</td><td><input type=hidden name=\"nickname\" width=50 value=\"$nickname\">$nickname</td></tr>\n"
                        . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>"
                        . $t['pwneu3']
                        . "</td><td><input type=hidden name=\"email\" width=50 value=\"$email\">$email</td></tr>\n"
                        . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>"
                        . $t['pwneu10']
                        . "</td><td><input name=\"hash\" width=50 value=\"$hash\"></td></tr>\n";
                } else {
                    if (!isset($nickname))
                        $nickname = "";
                    if (!isset($email))
                        $email = "";
                    echo "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>"
                        . $t['pwneu2']
                        . "</td><td><input name=\"nickname\" width=50 value=\"$nickname\"></td></tr>\n"
                        . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>"
                        . $t['pwneu3']
                        . "</td><td><input name=\"email\" width=50 value=\"$email\"></td></tr>\n";
                }
                echo "<input type=hidden name=\"http_host\" value=\"$http_host\">\n"
                    . "<input type=hidden name=\"aktion\" value=\"passwort_neu\">\n"
                    . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td colspan=2><input type=submit value=\"Absenden\"></td></tr>\n"
                    . "</form></table>";
            } else if ($richtig && $u_id) {
                $query = "SELECT u_adminemail, u_nick FROM user WHERE u_id = '$u_id' AND u_level = 'U' LIMIT 2";
                $result = mysqli_query($mysqli_link, $query);
                if ($result && mysql_numrows($result) == 1) {
                    unset($f);
                    $a = mysqli_fetch_array($result);
                    
                    $pwdneu = genpassword(8);
                    $f['u_passwort'] = $pwdneu;
                    $f['u_id'] = $u_id;
                    $text = str_replace("%passwort%", $f['u_passwort'],
                        $t['pwneu15']);
                    $text = str_replace("%nickname%", $a['u_nick'], $text);
                    $ok = mail($a['u_adminemail'], $t['pwneu14'], $text,
                        "From: $webmaster ($chat)");
                    
                    if ($ok) {
                        echo $t['pwneu12'];
                        schreibe_db("user", $f, $f['u_id'], "u_id");
                    } else {
                        echo $t['pwneu13'];
                    }
                }
            }
            
            zeige_fuss();
            break;
        
        case "neubestaetigen":
            if ($layout_bodytag)
                echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                    . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                    . "\n</HEAD>\n";
            zeige_kopf();
            echo $willkommen;
            
            unset($fehlermeldung);
            if ($email) {
                $email = mysqli_real_escape_string($mysqli_link, $email);
                if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
                    mysqli_real_escape_string($mysqli_link, $email))) {
                    $fehlermeldung = $t['neu51'];
                } else {
                    $query = "SELECT * FROM mail_check WHERE email = '$email'";
                    $result = mysqli_query($mysqli_link, $query);
                    if ($result && mysql_numrows($result) == 1) {
                        $a = mysqli_fetch_array($result);
                        $hash2 = md5($a['email'] . "+" . $a['datum']);
                        if ($hash == $hash2) {
                            $link = $serverprotokoll . "://" . $http_host
                                . $chatserver . $_SERVER['PHP_SELF']
                                . "?http_host=$http_host&aktion=neu2&frame=1";
                            
                            $text2 = str_replace("%link%", $link, $t['neu53']);
                            $text2 = str_replace("%hash%", $hash, $text2);
                            $email = urldecode($email);
                            $text2 = str_replace("%email%", $email, $text2);
                            
                            $mailbetreff = $t['neu38'];
                            $mailempfaenger = $email;
                            
                            echo $t['neu54'];
                            mail($mailempfaenger, $mailbetreff, $text2,
                                "From: $webmaster ($chat)");
                        } else {
                            $fehlermeldung = $t['neu52'];
                        }
                    } else {
                        $fehlermeldung = $t['neu51'];
                    }
                    mysqli_free_result($result);
                }
            }
            
            if (!$email || $fehlermeldung) {
                // Formular für die Freischaltung/Bestätigung der Mailadress
                echo "<p>" . $t['neu50'] . "</p>";
                if ($fehlermeldung) {
                    echo "<p><font color=\"red\"><b>$fehlermeldung</b></font></p>\n";
                }
                echo "<form action=\"index.php\">\n"
                    . "<table border=0 cellpadding=5 cellspacing=0>\n"
                    . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>" . $t['neu49']
                    . "</td><td><input name=\"email\" width=50 value=\"$email\"></td></tr>\n"
                    . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>" . $t['neu43']
                    . "</td><td><input name=\"hash\" width=50 value=\"$hash\"></td></tr>\n"
                    . "<input type=hidden name=\"http_host\" value=\"$http_host\">\n"
                    . "<input type=hidden name=\"aktion\" value=\"neubestaetigen\">\n"
                    . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td colspan=2><input type=submit value=\"Absenden\"></td></tr>\n"
                    . "</form></table>";
            }
            
            zeige_fuss();
            break;
        
        case "mailcheckm":
            echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                . "\n</HEAD>\n";
            zeige_kopf();
            echo $willkommen;
            echo "<p>" . $t['neu42'] . "</p>";
            echo "<form action=\"index.php\">\n"
                . "<table border=0 cellpadding=5 cellspacing=0>\n"
                . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>" . $t['neu17']
                . "</td><td><input name=\"email\" width=50></td></tr>\n"
                . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td>" . $t['neu43']
                . "</td><td><input name=\"hash\" width=50></td></tr>\n"
                . "<input type=hidden name=\"http_host\" value=\"$http_host\">\n"
                . "<input type=hidden name=\"aktion\" value=\"neu\">\n"
                . "<tr BGCOLOR=\"$farbe_tabelle_kopf\"><td colspan=2><input type=submit value=\"Absenden\"></td></tr>\n"
                . "</form></table>";
            zeige_fuss();
            break;
        
        case "mailcheck":
        // Header ausgeben
            if ($layout_bodytag)
                echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                    . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                    . "\n</HEAD>\n";
            zeige_kopf();
            echo $willkommen;
            
            // dieser Regex macht eine primitive Prüfung ob eine Mailadresse
            // der Form name@do.main entspricht, wobei
            if (isset($email)) {
                if (!preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
                    mysqli_real_escape_string($mysqli_link, $email))) {
                    echo str_replace("%email%", $email, $t['neu41']);
                    $email = "";
                }
            }
            
            if (!isset($email)) {
                // Formular für die Erstregistierung ausgeben, 1. Schritt
                echo "<p>" . $t['neu33'];
                if (isset($anmeldung_nurmitbest)
                    && strlen($anmeldung_nurmitbest) > 0) { // Anmeldung mit Externer Bestätigung
                    echo $t['neu45'];
                }
                echo "<p><table border=0 cellpadding=3 cellspacing=0>"
                    . "<form action=\"index.php\">" . "<tr><td>" . $t['neu34']
                    . "</td><td><input name=\"email\" maxlength=80></td></tr>"
                    . "<tr><td colspan=2><input type=submit value=\""
                    . $t['neu35'] . "\">"
                    . "<input type=hidden name=\"http_host\" value=\"$http_host\">"
                    . "<input type=hidden name=\"aktion\" value=\"mailcheck\"></td></tr>"
                    . "</form></table>";
            } else {
                // Mail verschicken, 2. Schritt
                
                $email = trim($email);
                
                // wir prüfen ob User gesperrt ist
                // entweder User = gesperrt
                $query = "SELECT * FROM user WHERE ( (u_adminemail='$email') OR (u_email='$email') ) AND u_level='Z'";
                $result = mysqli_query($mysqli_link, $query);
                $num = mysql_numrows($result);
                $gesperrt = false;
                if ($num >= 1) {
                    $gesperrt = true;
                    echo $t['neu40'];
                }
                
                // oder user ist auf Blacklist
                $query = "select u_nick from blacklist left join user on f_blacklistid=u_id "
                    . "WHERE user.u_adminemail ='$email'";
                $result = mysqli_query($mysqli_link, $query);
                $num = mysql_numrows($result);
                if ($num >= 1) {
                    $gesperrt = true;
                    echo $t['neu40'];
                }
                
                // oder Domain ist lt. Config verboten
                if ($domaingesperrtdbase != $dbase) {
                    $domaingesperrt = "";
                }
                for ($i = 0; $i < count($domaingesperrt); $i++) {
                    $teststring = strtolower($email);
                    if (isset($domaingesperrt[$i]) && $domaingesperrt[$i]
                        && (preg_match($domaingesperrt[$i], $teststring))) {
                        $gesperrt = true;
                        echo $t['neu40'];
                    }
                }
                unset($teststring);
                
                if (($begrenzung_anmeld_pro_mailadr > 0) and (!$gesperrt)) {
                    $query = "select u_id from user WHERE u_adminemail = '$email'";
                    $result = mysqli_query($mysqli_link, $query);
                    $num = mysql_numrows($result);
                    if ($num >= $begrenzung_anmeld_pro_mailadr) {
                        $gesperrt = true;
                        echo str_replace("%anzahl%",
                            $begrenzung_anmeld_pro_mailadr, $t['neu55']);
                    }
                }
                
                if (!$gesperrt) {
                    // Überprüfung auf Formular mehrmals abgeschickt
                    $query = "DELETE FROM mail_check WHERE email = '$email'";
                    mysqli_query($mysqli_link, $query);
                    
                    $hash = md5($email . "+" . date("Y-m-d"));
                    $email = urlencode($email);
                    
                    if (isset($anmeldung_nurmitbest)
                        && strlen($anmeldung_nurmitbest) > 0) {
                        // Anmeldung mit externer Bestätigung
                        $link1 = $serverprotokoll . "://" . $http_host
                            . $chatserver . $_SERVER['PHP_SELF']
                            . "?http_host=$http_host&aktion=neubestaetigen&frame=1";
                        $link2 = $serverprotokoll . "://" . $http_host
                            . $chatserver . $_SERVER['PHP_SELF']
                            . "?http_host=$http_host&aktion=neu2&frame=1";
                        
                        $text2 = str_replace("%link1%", $link1, $t['neu47']);
                        $text2 = str_replace("%link2%", $link2, $text2);
                        $text2 = str_replace("%hash%", $hash, $text2);
                        $email = urldecode($email);
                        $text2 = str_replace("%email%", $email, $text2);
                        
                        $mailbetreff = $t['neu46'];
                        $mailempfaenger = $anmeldung_nurmitbest;
                        
                        echo $t['neu48'];
                    } else {
                        // Normale Anmeldung
                        $link = $serverprotokoll . "://" . $http_host
                            . $_SERVER['PHP_SELF']
                            . "?http_host=$http_host&aktion=neu&email=$email&hash=$hash&frame=1";
                        $link2 = $serverprotokoll . "://" . $http_host
                            . $_SERVER['PHP_SELF']
                            . "?http_host=$http_host&aktion=neu2&frame=1";
                        
                        $text2 = str_replace("%link%", $link, $t['neu36']);
                        $text2 = str_replace("%link2%", $link2, $text2);
                        $text2 = str_replace("%hash%", $hash, $text2);
                        $email = urldecode($email);
                        $text2 = str_replace("%email%", $email, $text2);
                        
                        $mailbetreff = $t['neu38'];
                        $mailempfaenger = $email;
                        
                        echo $t['neu37'];
                    }
                    
                    mail($mailempfaenger, $mailbetreff, $text2,
                        "From: $webmaster ($chat)" . "\n" . "X-MC-IP: "
                            . $_SERVER["REMOTE_ADDR"] . "\n" . "X-MC-TS: "
                            . time());
                    $email = mysqli_real_escape_string($mysqli_link, $email);
                    $query = "REPLACE INTO mail_check (email,datum) VALUES ('$email',NOW())";
                    $result = mysqli_query($mysqli_link, $query);
                    
                }
            }
            
            zeige_fuss();
            break;
        
        case "abweisen":
        // Header ausgeben
            if ($layout_bodytag)
                echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                    . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                    . "\n</HEAD>\n";
            zeige_kopf();
            
            echo $t['login4'];
            
            zeige_fuss();
            
            break;
        
        case "gesperrt":
        // Header ausgeben
            if ($layout_bodytag)
                echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                    . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                    . "\n</HEAD>\n";
            zeige_kopf();
            
            if ($chat_offline_kunde) {
                echo $chat_offline_kunde_txt;
            } else {
                echo $chat_offline;
            }
            
            zeige_fuss();
            
            break;
        
        case "login":
        // Sequence für online- und chat-Tabelle erzeugen
            erzeuge_sequence("online", "o_id");
            erzeuge_sequence("chat", "c_id");
            
            // Weiter mit login
            if (!isset($passwort) || $passwort == "") {
                // Login als Gast
                
                // Falls Gast-Login erlaubt ist:
                if ($gast_login) {
                    
                    // Prüfen, ob von der IP und dem User-Agent schon ein Gast online ist und ggf abweisen
                    $query4711 = "SELECT o_id FROM online "
                        . "WHERE o_browser='" . $_SERVER["HTTP_USER_AGENT"]
                        . "' " . "AND o_ip='" . $_SERVER["REMOTE_ADDR"] . "' "
                        . "AND o_level='G' "
                        . "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
                    $result = mysqli_query($conn, $query4711);
                    if ($result)
                        $rows = mysqli_num_rows($result);
                    mysqli_free_result($result);
                    
                }
                
                // Login abweisen, falls mehr als ein Gast online ist oder Gäste gesperrt sind
                if (!$gast_login or ($rows > 1 && !$gast_login_viele)
                    or $temp_gast_sperre) {
                    
                    // Gäste sind gesperrt
                    
                    // Header ausgeben
                    if ($layout_bodytag) {
                        echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                            . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                            . "\n";
                        "</HEAD>\n";
                    }
                    zeige_kopf();
                    
                    // Passenden Fehlertext ausgeben
                    if (!$gast_login) {
                        echo $t['login16'];
                    } else {
                        echo $t['login15'];
                    }
                    
                    // Box für Login
                    if ($frameset_bleibt_stehen) {
                        echo "<FORM ACTION=\"$chat_file\" NAME=\"form1\" METHOD=\"POST\">"
                            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                    } else {
                        echo "<FORM ACTION=\"$chat_file\" TARGET=\"topframe\" NAME=\"form1\" METHOD=\"POST\">"
                            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                    }
                    
                    echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
                        . "// end hiding -->\n</script>\n";
                    
                    // Disclaimer ausgeben
                    show_box($login_titel, $logintext, "", "100%");
                    echo "<DIV align=center>" . $f3
                        . str_replace("%farbe_text%", $farbe_text, $disclaimer)
                        . $f4 . "</DIV>\n</FORM>";
                    zeige_fuss();
                    exit;
                    
                }
                
                // Login als gast
                
                // Im Nick alle Sonderzeichen entfernen, Länge prüfen
                if (!isset($login))
                    $login = "";
                $login = coreCheckName($login, $check_name);
                
                if (!isset($keineloginbox))
                    $keineloginbox = false;
                if (!$keineloginbox) {
                    if (strlen($login) < 4 || strlen($login) > 20)
                        $login = "";
                }
                
                // Falls kein Nick übergeben, Nick finden
                if (strlen($login) == 0) {
                    if ($gast_name_auto) {
                        // freien Nick bestimmen falls in der Config erlaubt
                        $rows = 1;
                        $i = 0;
                        $anzahl = count($gast_name);
                        while ($rows != 0 && $i < 100) {
                            $login = $gast_name[mt_rand(1, $anzahl)];
                            $query4711 = "SELECT u_id FROM user "
                                . "WHERE u_nick='$login' ";
                            $result = mysqli_query($conn, $query4711);
                            $rows = mysqli_num_rows($result);
                            $i++;
                        }
                        mysqli_free_result($result);
                    } else {
                        // freien Namen bestimmen
                        $rows = 1;
                        $i = 0;
                        while ($rows != 0 && $i < 100) {
                            $login = $t['login13'] . strval((mt_rand(1, 10000)) + 1);
                            $query4711 = "SELECT u_id FROM user WHERE u_nick='" . mysqli_real_escape_string($mysqli_link, $login) . "'";
                            $result = mysqli_query($conn, $query4711);
                            $rows = mysqli_num_rows($result);
                            $i++;
                        }
                        mysqli_free_result($result);
                    }
                }
                
                // Im Nick alle Sonderzeichen entfernen, vorsichtshalber nochmals prüfen
                $login = mysqli_real_escape_string($check_name), coreCheckName($login);
                
                // Userdaten für Gast setzen
                $f['u_level'] = "G";
                $f['u_passwort'] = mt_rand(1, 10000);
                $f['u_name'] = $login;
                $f['u_nick'] = $login;
                $passwort = $f['u_passwort'];
                
                // Prüfung, ob dieser User bereits existiert
                $query4711 = "SELECT u_id FROM user WHERE u_nick='$f[u_nick]'";
                $result = mysqli_query($conn, $query4711);
                
                if (mysqli_num_rows($result) == 0) {
                    // Account in DB schreiben
                    schreibe_db("user", $f, "", "u_id");
                }
                
            }
            
            // Login als registrierter User
            
            // Testen, ob frühere Loginversuche fehlschlugen (nur Admins)
            $query4711 = "SELECT u_id,u_nick,u_loginfehler,u_login,u_backup FROM user "
                . "WHERE (u_name = '$login' OR u_nick = '$login') "
                . "AND (u_level='S' OR u_level='C') ";
            $result = mysqli_query($conn, $query4711);
            if ($result)
                $rows = mysqli_num_rows($result);
            
            // Voreinstellung: Weiter mit Login
            $login_ok = true;
            
            // Mehr als ein güliger Account gefunden, nochmals nach nick suchen
            if ($result && $rows > 1) {
                mysqli_free_result($result);
                $query4711 = "SELECT u_id,u_nick,u_loginfehler,u_login,u_backup FROM user "
                    . "WHERE (u_nick = '$login') "
                    . "AND (u_level='S' OR u_level='C') ";
                $result = mysqli_query($conn, $query4711);
                if ($result)
                    $rows = mysqli_num_rows($result);
            }
            if ($result && $rows == 1) {
                $userdata = mysqli_fetch_object($result);
                if ($userdata->u_loginfehler) {
                    
                    // Nach zwei fehlgeschlagenen Logins und dem letzen Versuch keine 5 min vergangen 
                    // -> Login sperren und merken
                    $u_loginfehler = unserialize($userdata->u_loginfehler);
                    $letzter_eintrag = end($u_loginfehler);
                    if ((time() - $letzter_eintrag[login]) < 300
                        && count($u_loginfehler) > 9) {
                        $login_ok = false;
                    }
                }
            }
            
            // $crypted_password_extern = 0; wird in functions-init.php gesetzt
            // Wenn externe Schnittstelle vorhanden ist
            // wird die dort vorhandene von function und variablen importiert
            if (file_exists("ext/functions.php-" . $http_host)) {
                // Speziell für die weitere verwendung in auth_user wird die $crypted_password_extern = 0 oder 1 aus der Datei gelesen
                require("ext/functions.php-" . $http_host);
            }
            
            // Passwort prüfen und Userdaten lesen
            $rows = 0;
            $result = auth_user("u_nick", $login, $passwort);
            if ($result) {
                $rows = mysqli_num_rows($result);
            }
            
            // Nick nicht gefunden, optionales include starten und User ggf. aus externer Datenbank kopieren
            if ($rows == 0 && file_exists("ext/functions.php-" . $http_host)) {
                // Wenn User = Gast, dann mit leerem Passwort in die Externe Prüfung
                // erforderlich, da aus externer Schnittstelle Gastdaten ohne PW kommen
                // Sicherheitsproblem in der Externen Schnittstelle: da jeder unter diesem 
                // Gastnick einloggen kann, und der ursprüngliche User fliegt raus
                // Muss daher im Übergeordneten System sichergestellt sein
                if ($f['u_level'] == 'G') {
                    $passwort = '';
                }
                
                // Function ext_lese_user oben eingebunden
                $passwort_ext = ext_lese_user($login, $passwort);
                
                $result = auth_user("u_nick", $login, $passwort_ext);
                if ($result) {
                    $rows = mysqli_num_rows($result);
                    $passwort = $passwort_ext;
                    if ($rows == 1) {
                    }
                }
            }
            
            // Nick nicht gefunden, nochmals mit Usernamen suchen
            if ($rows == 0) {
                $result = auth_user("u_name", $login, $passwort);
                if ($result) {
                    $rows = mysqli_num_rows($result);
                }
                
            }
            
            // Login fehlgeschlagen
            if ($rows == 0 && isset($userdata) && is_array($userdata)
                && $userdata) {
                
                // Fehllogin bei Admin: falsches Passwort oder Username -> max 100 Loginversuche in Userdaten merken
                if ($userdata->u_loginfehler)
                    $u_loginfehler = unserialize($userdata->u_loginfehler);
                if (count($u_loginfehler) < 100) {
                    unset($f);
                    unset($temp);
                    $f['nick'] = substr($userdata->u_nick, 0, 20);
                    $f['pw'] = substr($passwort, 0, 20);
                    $f['login'] = time();
                    $f['ip'] = substr($_SERVER["REMOTE_ADDR"], 0, 20);
                    $u_loginfehler[] = $f;
                    $temp['u_loginfehler'] = serialize($u_loginfehler);
                    $temp['u_login'] = $userdata->u_login;
                    schreibe_db("user", $temp, $userdata->u_id, "u_id");
                }
            }
            
            // güliger Account gefunden, weiter mit Login oder Fehlermeldungen ausgeben
            if ($result && $rows == 1 && $login_ok) {
                
                // Login Ok, Userdaten setzen
                $row = mysqli_fetch_object($result);
                $u_id = $row->u_id;
                $u_name = $row->u_name;
                $u_nick = $row->u_nick;
                $u_level = $row->u_level;
                $u_backup = $row->u_backup;
                $u_agb = $row->u_agb;
                $u_punkte_monat = $row->u_punkte_monat;
                $u_punkte_jahr = $row->u_punkte_jahr;
                $u_punkte_datum_monat = $row->u_punkte_datum_monat;
                $u_punkte_datum_jahr = $row->u_punkte_datum_jahr;
                $u_punkte_gesamt = $row->u_punkte_gesamt;
                $ip_historie = unserialize($row->u_ip_historie);
                $u_frames = unserialize($row->u_frames);
                $nick_historie = unserialize($row->u_nick_historie);
                
                if ($loginimsicherenmodus == "1") {
                    $u_backup = 1;
                    $f['u_id'] = $u_id;
                    $f['u_backup'] = 1;
                    schreibe_db("user", $f, $u_id, "u_id");
                }
                
                // User online bestimmen
                if ($chat_max[$u_level] != 0) {
                    $query = "SELECT count(o_id) FROM online "
                        . "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
                    $result2 = mysqli_query($conn, $query);
                    if ($result2)
                        $onlineanzahl = mysql_result($result2, 0, 0);
                    mysqli_free_result($result2);
                }
                
                // Login erfolgreich ?
                
                if ($u_level == "Z") {
                    
                    // User gesperrt -> Fehlermeldung ausgeben
                    
                    // Header ausgeben
                    if ($layout_bodytag)
                        echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                            . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                            . "\n</HEAD>\n";
                    zeige_kopf();
                    
                    echo str_replace("%u_name%", $u_name,
                        str_replace("%u_nick%", $u_nick, $t['login5']));
                    
                    zeige_fuss();
                    
                } elseif (false && $HTTP_COOKIE_VARS[MAINCHAT2] != "on"
                    && ($u_level == "C" || $u_level == "S")) {
                    
                    // Der User ein Admin und es sind cookies gesetzt -> Fehlermeldung ausgeben
                    
                    // Header ausgeben
                    if ($layout_bodytag)
                        echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                            . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                            . "\n" . "</HEAD>\n";
                    zeige_kopf();
                    
                    echo str_replace("%url%", $chat_file, $t['login25']);
                    unset($u_name);
                    unset($u_nick);
                    
                    zeige_fuss();
                    
                } elseif ($chat_max[$u_level] != 0
                    && $onlineanzahl > $chat_max[$u_level]) {
                    
                    // Maximale Anzahl der User im Chat erreicht -> Fehlermeldung ausgeben
                    
                    // Header ausgeben
                    if ($layout_bodytag)
                        echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                            . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                            . "\n" . "</HEAD>\n";
                    zeige_kopf();
                    
                    $txt = str_replace("%online%", $onlineanzahl, $t['login24']);
                    $txt = str_replace("%max%", $chat_max[$u_level], $txt);
                    $txt = str_replace("%leveltxt%", $level[$u_level], $txt);
                    $txt = str_replace("%zusatztext%", $chat_max[zusatztext],
                        $txt);
                    echo $txt;
                    
                    unset($u_name);
                    unset($u_nick);
                    
                    // Box für Login
                    if ($frameset_bleibt_stehen) {
                        echo "<FORM ACTION=\"$chat_file\" NAME=\"form1\" METHOD=\"POST\">"
                            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                    } else {
                        echo "<FORM ACTION=\"$chat_file\" TARGET=\"topframe\" NAME=\"form1\" METHOD=\"POST\">"
                            . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                    }
                    
                    if ($gast_login && $communityfeatures && $forumfeatures) {
                        $titel = $login_titel . "[<A HREF=\""
                            . $_SERVER['PHP_SELF']
                            . "?http_host=$http_host&id=$id&aktion=login&"
                            . $t['login10'] . "=los&eintritt=forum\">" . $ft0
                            . $t['login23'] . $ft1 . "</A>]";
                    } else {
                        $titel = $login_titel;
                    }
                    
                    if ($einstellungen_aendern) {
                        $titel .= "[<A HREF=\"" . $_SERVER['PHP_SELF']
                            . "?http_host=$http_host&aktion=passwort_neu\">"
                            . $ft0 . $t['login27'] . $ft1 . "</A>]";
                    }
                    
                    // Box und Disclaimer ausgeben
                    show_box($titel, $logintext, "", "100%");
                    echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
                        . "// end hiding -->\n</script>\n";
                    echo "<DIV align=center>" . $f3
                        . str_replace("%farbe_text%", $farbe_text, $disclaimer)
                        . $f4 . "</DIV>\n</FORM>";
                    
                    zeige_fuss();
                    
                } else {
                    
                    if (($keine_agb == 1) && ($captcha_text == 0)) {
                        $u_agb = 'Y';
                    }
                    
                    if (($temp_gast_sperre) && ($u_level == 'G')) {
                        $captcha_text1 = "999";
                    } // abweisen, falls Gastsperre aktiv
                    if ((isset($captcha_text1)) and ($captcha_text1 == "")) {
                        $captcha_text1 = "999";
                    } // abweisen, falls leere eingabe
                    
                    if (($captcha_text == 1) && (isset($captcha_text2))
                        && ($captcha_text2
                            != md5(
                                $u_id + "code" + $captcha_text1 . "+"
                                    . date("Y-m-d h")))) {
                        $los = "";
                    }
                    
                    // muss User/Gast  noch Nutzungsbestimmungen bestätigen?
                    if ($los != $t['login17'] && $u_agb != "Y") {
                        
                        // Nutzungsbestimmungen ausgeben
                        
                        // ggf. AGBs aus Sprachdatei mit extra AGBs aus config.php überschreiben
                        if ((isset($extra_agb)) and ($extra_agb <> "")
                            and ($extra_agb <> "standard"))
                            $t['agb'] = $extra_agb;
                        
                        // Header ausgeben
                        if ($layout_bodytag)
                            echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                                . $metatag . $stylesheet . "\n"
                                . $zusatztext_kopf . "\n" . "</HEAD>\n";
                        zeige_kopf();
                        
                        // Box für Login
                        if ($frameset_bleibt_stehen) {
                            echo "<FORM ACTION=\"$chat_file\" NAME=\"form1\" METHOD=\"POST\">"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                        } else {
                            echo "<FORM ACTION=\"$chat_file\" TARGET=\"topframe\" NAME=\"form1\" METHOD=\"POST\">"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                        }
                        
                        if (!isset($eintritt))
                            $eintritt = RaumNameToRaumID($lobby);
                        
                        if ($captcha_text == 1) {
                            echo "<TABLE BORDER=0 CELLPADDING=6 CELLSPACING=0>"
                                . "<TR BGCOLOR=\"$farbe_tabelle_kopf\"><TD COLSPAN=2>"
                                . $t['agb'] . "</TD></TR>\n";
                            
                            echo "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2>"
                                . $t['captcha1'] . "<BR />" . "\n";
                            
                            $aufgabe = mt_rand(1, 3);
                            if ($aufgabe == 1) // +
 {
                                $zahl1 = mt_rand(0, 99);
                                $zahl2 = mt_rand(0, 99 - $zahl1);
                                $ergebnis = $zahl1 + $zahl2;
                            } else if ($aufgabe == 2) // -
 {
                                $zahl1 = mt_rand(0, 99);
                                $zahl2 = mt_rand(0, $zahl1);
                                $ergebnis = $zahl1 - $zahl2;
                            } else // *
 {
                                $zahl1 = mt_rand(0, 10);
                                $zahl2 = mt_rand(0, 10);
                                $ergebnis = $zahl1 * $zahl2;
                            }
                            
                            echo $tzahl[$zahl1] . " " . $taufgabe[$aufgabe]
                                . " " . $tzahl[$zahl2]
                                . " &nbsp;&nbsp;&nbsp;&nbsp;";
                            echo "<INPUT TYPE=\"TEXT\" NAME=\"captcha_text1\">";
                            echo "<INPUT TYPE=\"HIDDEN\" NAME=\"captcha_text2\" VALUE=\""
                                . md5(
                                    $u_id + "code" + $ergebnis . "+"
                                        . date("Y-m-d h")) . "\">";
                            
                            echo "</TD></TR><TR BGCOLOR=\"$farbe_tabelle_kopf\">"
                                . "<TD align=\"left\">" . $f1
                                . "<B><INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\""
                                . $t['login17'] . "\"></B></TD>\n"
                                . "<TD align=\"right\">" . $f1
                                . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\""
                                . $t['login18'] . "\"></TD>\n"
                                . "</TR></TABLE>\n"
                                . "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
                                . "// end hiding -->\n</script>\n"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"login\" VALUE=\"$login\">\n"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"passwort\" VALUE=\""
                                . $passwort . "\">\n"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"eintritt\" VALUE=\"$eintritt\">\n"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"login\"></FORM>\n";
                        } else {
                            echo "<TABLE BORDER=0 CELLPADDING=6 CELLSPACING=0>"
                                . "<TR BGCOLOR=\"$farbe_tabelle_kopf2\"><TD COLSPAN=2>"
                                . $t['agb'] . "</TD></TR>\n"
                                . "<TR BGCOLOR=\"$farbe_tabelle_kopf\">"
                                . "<TD align=\"left\">" . $f1
                                . "<B><INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\""
                                . $t['login17'] . "\"></B></TD>\n"
                                . "<TD align=\"right\">" . $f1
                                . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\""
                                . $t['login18'] . "\"></TD>\n"
                                . "</TR></TABLE>\n"
                                . "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
                                . "// end hiding -->\n</script>\n"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"login\" VALUE=\"$login\">\n"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"passwort\" VALUE=\"$passwort\">\n"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"eintritt\" VALUE=\"$eintritt\">\n"
                                . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"login\"></FORM>\n";
                        }
                        
                        zeige_fuss();
                        exit;
                        
                    } elseif ($los == $t['login17']) {
                        // AGBs wurden bestätigt
                        $u_agb = "Y";
                    } else {
                        $u_agb = "";
                    }
                    
                    // User in Blacklist überprüfen
                    // in den kostenlosen Chats konnte es sein, das die Tabelle nicht vorhanden ist
                    $query2 = "SELECT f_text from blacklist where f_blacklistid=$u_id";
                    $result2 = mysqli_query($conn, $query2);
                    if ($result2 AND mysqli_num_rows($result2) > 0) {
                        $infotext = "Blacklist: "
                            . mysql_result($result2, 0, 0);
                        $warnung = TRUE;
                    }
                    if ($result2)
                        mysqli_free_result($result2);
                    
                    // Bei Login dieses Users alle Admins (online, nicht Temp) warnen
                    if ($warnung) {
                        
                        if ($eintritt == 'forum') {
                            $raumname = " (" . $whotext[2] . ")";
                        } else {
                            $query2 = "SELECT r_name from raum where r_id=" . intval($eintritt);
                            $result2 = mysqli_query($conn, $query2);
                            if ($result2 AND mysqli_num_rows($result2) > 0) {
                                $raumname = " (" . mysql_result($result2, 0, 0)
                                    . ") ";
                            } else {
                                $raumname = "";
                            }
                            mysqli_free_result($result2);
                        }
                        
                        $query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
                        $result2 = mysqli_query($conn, $query2);
                        if ($result2 AND mysqli_num_rows($result2) > 0) {
                            $txt = str_replace("%ip_adr%", $ip_adr,
                                $t['default6']);
                            $txt = str_replace("%ip_name%", $ip_name, $txt);
                            $txt = str_replace("%is_infotext%", $infotext, $txt);
                            while ($row2 = mysqli_fetch_object($result2)) {
                                $ur1 = "user.php?http_host=<HTTP_HOST>&id=<ID>&aktion=zeig&user=$u_id";
                                $ah1 = "<A HREF=\"$ur1\" TARGET=\"$u_nick\" onclick=\"neuesFenster('$ur1','$u_nick'); return(false);\">";
                                $ah2 = "</A>";
                                system_msg("", 0, $row2->o_user, $system_farbe,
                                    str_replace("%u_nick%",
                                        $ah1 . $u_nick . $ah2 . $raumname,
                                        $txt));
                            }
                        }
                        mysqli_free_result($result2);
                    }
                    
                    // User nicht gesperrt, weiter mit Login und Eintritt in ausgewählten Raum mit ID $eintritt
                    
                    // Hash-Wert ermitteln
                    $hash_id = id_erzeuge($u_id);
                    
                    // $javascript wird als input type hidden übergeben...
                    if (isset($javascript) && $javascript == "on") {
                        $javascript = 1;
                    } else {
                        $javascript = 0;
                    }
                    
                    // Login
                    $o_id = login($u_id, $u_nick, $u_level, $hash_id,
                        $javascript, $ip_historie, $u_agb, $u_punkte_monat,
                        $u_punkte_jahr, $u_punkte_datum_monat,
                        $u_punkte_datum_jahr, $u_punkte_gesamt);
                    
                    // Beichtstuhl-Special
                    // Falls es Räume mit nur einem Admin gibt und niemand in der Lobby ist,
                    // Eintritt in einen solchen Raum, ansonsten in Lobby
                    if (isset($beichtstuhl) && $beichtstuhl) {
                        
                        $login_in_lobby = FALSE;
                        
                        // Prüfen, ob ein User in der Lobby ist
                        $query2 = "SELECT o_id "
                            . "FROM raum,online WHERE o_raum=r_id "
                            . "AND r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' "
                            . "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout ";
                        $result2 = mysqli_query($conn, $query2);
                        
                        if ($result2 && mysqli_num_rows($result2) > 0) {
                            
                            // Mehr als 0 User in Lobby
                            mysqli_free_result($result2);
                            $login_in_lobby = TRUE;
                            
                        } else {
                            
                            // Prüfen, ob es Räume mit genau einem Admin gibt
                            mysqli_free_result($result2);
                            $query2 = "SELECT r_id,count(o_id) as anzahl, "
                                . "count(o_level='C') as CADMIN, count(o_level='S') as SADMIN "
                                . "FROM raum,online WHERE o_raum=r_id "
                                . "AND r_name!='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' "
                                . "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
                                . "GROUP BY r_id HAVING anzahl=1 AND (CADMIN=1 OR SADMIN=1)";
                            $result2 = mysqli_query($conn, $query2);
                            $anzahl = mysqli_num_rows($result2);
                            if ($result2 && $anzahl == 1) {
                                $eintritt = mysql_result($result2, 0, "r_id");
                            } elseif ($result2 && $anzahl > 1) {
                                $eintritt = mysql_result($result2,
                                    mt_rand(0, $anzahl - 1), "r_id");
                            } else {
                                $login_in_lobby = TRUE;
                            }
                            mysqli_free_result($result2);
                            
                        }
                        
                        // ID der Lobby neu ermitteln -> Login in Lobby
                        if ($login_in_lobby) {
                            $query2 = "SELECT r_id FROM raum WHERE r_name = '" . mysqli_real_escape_string($mysqli_link, $eintrittsraum) . "' ";
                            $result2 = mysqli_query($conn, $query2);
                            if ($result2 && mysqli_num_rows($result2) == 1) {
                                $eintritt = mysql_result($result2, 0, "r_id");
                            }
                            mysqli_free_result($result2);
                        }
                        
                        // Bei Login dieses Users alle Admins (online, nicht Temp) informieren
                        $query2 = "SELECT r_name from raum where r_id= '" . mysqli_real_escape_string($mysqli_link, $eintritt) . "'";
                        $result2 = mysqli_query($conn, $query2);
                        if ($result2 AND mysqli_num_rows($result2) > 0) {
                            $raumname = mysql_result($result2, 0, 0);
                        } else {
                            $raumname = "";
                        }
                        mysqli_free_result($result2);
                        
                        $query2 = "SELECT o_user FROM online WHERE (o_level='S' OR o_level='C')";
                        $result2 = mysqli_query($conn, $query2);
                        if ($result2 AND mysqli_num_rows($result2) > 0) {
                            $txt = str_replace("%raumname%", $raumname,
                                $t['default7']);
                            while ($row2 = mysqli_fetch_object($result2)) {
                                $ur1 = "user.php?http_host=<HTTP_HOST>&id=<ID>&aktion=zeig&user=$u_id";
                                $ah1 = "<A HREF=\"$ur1\" TARGET=\"$u_nick\" onclick=\"neuesFenster('$ur1','$u_nick'); return(false);\">";
                                $ah2 = "</A>";
                                system_msg("", 0, $row2->o_user, $system_farbe,
                                    str_replace("%u_nick%",
                                        $ah1 . $u_nick . $ah2, $txt));
                            }
                        }
                        mysqli_free_result($result2);
                        
                    }
                    
                    // Kopf ausgeben
                    if ($layout_bodytag)
                        echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                            . $stylesheet . "\n" . $zusatztext_kopf
                            . "\n</HEAD>\n";
                    
                    if ($communityfeatures && $eintritt == "forum") {
                        
                        // Login ins Forum
                        betrete_forum($o_id, $u_id, $u_nick, $u_level);
                        
                        // Frame-Einstellungen für Browser definieren
                        $user_agent = strtolower($HTTP_USER_AGENT);
                        if (preg_match("/linux/", $user_agent)) {
                            $frame_type = "linux";
                        } elseif (preg_match("/solaris/", $user_agent)) {
                            $frame_type = "solaris";
                        } elseif (preg_match("/msie/", $user_agent)) {
                            $frame_type = "ie";
                        } elseif (preg_match('#mozilla/4\.7#i', $user_agent)) {
                            $frame_type = "nswin";
                        } else {
                            $frame_type = "def";
                        }
                        
                        // Obersten Frame definieren
                        if (!isset($frame_online)) {
                            $frame_online = "frame_online.php";
                        }
                        
                        // Falls user eigene Einstellungen für das Frameset hat -> überschreiben
                        if (is_array($u_frames)) {
                            foreach ($u_frames as $key => $val) {
                                if ($val)
                                    $frame_size[$frame_type][$key] = $val;
                            }
                        }
                        
                        echo "<FRAMESET ROWS=\"$frame_online_size,*,5,"
                            . $frame_size[$frame_type]['interaktivforum']
                            . ",1\" border=0 frameborder=0 framespacing=0>\n";
                        echo "<FRAME SRC=\"$frame_online?http_host=$http_host\" name=\"frame_online\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                        echo "<FRAME SRC=\"forum.php?http_host=$http_host&id=$hash_id\" name=\"forum\" MARGINWIDTH=\"0\" MARGINHEIGHT=\"0\" SCROLLING=AUTO>\n";
                        echo "<FRAME SRC=\"leer.php?http_host=$http_host\" name=\"leer\" MARGINWIDTH=\"0\" MARGINHEIGHT=\"0\" SCROLLING=NO>\n";
                        echo "<FRAMESET COLS=\"*,"
                            . $frame_size[$frame_type]['messagesforum']
                            . "\" border=0 frameborder=0 framespacing=0>\n";
                        echo "<FRAME SRC=\"messages-forum.php?http_host=$http_host&id=$hash_id\" name=\"messages\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=AUTO>\n";
                        echo "<FRAME SRC=\"interaktiv-forum.php?http_host=$http_host&id=$hash_id\" name=\"interaktiv\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                        echo "</FRAMESET>\n";
                        echo "<FRAME SRC=\"schreibe.php?http_host=$http_host&id=$hash_id&o_who=2\" name=\"schreibe\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                        echo "</FRAMESET>\n";
                        echo "<NOFRAMES>\n";
                        echo $body_tag . $t['login6'];
                        die();
                    } else {
                        
                        // Chat betreten
                        betrete_chat($o_id, $u_id, $u_nick, $u_level,
                            $eintritt, $javascript, $u_backup);
                        $back = 1;
                        
                        // Frame-Einstellungen für Browser definieren
                        $user_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
                        if (preg_match("/linux/", $user_agent)) {
                            $frame_type = "linux";
                        } elseif (preg_match("/solaris/", $user_agent)) {
                            $frame_type = "solaris";
                        } elseif (preg_match("/msie/", $user_agent)) {
                            $frame_type = "ie";
                        } elseif (preg_match('#mozilla/4\.7#i', $user_agent)) {
                            $frame_type = "nswin";
                        } else {
                            $frame_type = "def";
                        }
                        
                        // Obersten Frame definieren
                        if (!isset($frame_online) || $frame_online == "") {
                            $frame_online = "frame_online.php";
                        }
                        if ($u_level == "M") {
                            $frame_size[$frame_type]['interaktiv'] = $moderationsgroesse;
                            $frame_size[$frame_type]['eingabe'] = $frame_size[$frame_type]['eingabe']
                                * 2;
                        }
                        
                        // Falls user eigene Einstellungen für das Frameset hat -> überschreiben
                        if (is_array($u_frames)) {
                            foreach ($u_frames as $key => $val) {
                                if ($val)
                                    $frame_size[$frame_type][$key] = $val;
                            }
                        }
                        
                        // Frameset aufbauen
                        echo "<FRAMESET ROWS=\"$frame_online_size,*,"
                            . $frame_size[$frame_type]['eingabe'] . ","
                            . $frame_size[$frame_type]['interaktiv']
                            . ",1\" border=0 frameborder=0 framespacing=0>\n";
                        echo "<FRAME SRC=\"$frame_online?http_host=$http_host\" name=\"frame_online\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                        echo "<FRAMESET COLS=\"*,"
                            . $frame_size[$frame_type]['chatuserliste']
                            . "\" border=0 frameborder=0 framespacing=0>\n";
                        echo "<FRAME SRC=\"chat.php?http_host=$http_host&id=$hash_id&back=$back\" name=\"chat\" MARGINWIDTH=4 MARGINHEIGHT=0>\n";
                        if (!isset($userframe_url))
                            echo "<FRAME SRC=\"user.php?http_host=$http_host&id=$hash_id&aktion=chatuserliste\" MARGINWIDTH=4 MARGINHEIGHT=0 name=\"userliste\">\n";
                        else
                            echo "<FRAME SRC=\"$userframe_url\" MARGINWIDTH=4 MARGINHEIGHT=0 name=\"userliste\">\n";
                        
                        echo "</FRAMESET>\n";
                        echo "<FRAME SRC=\"eingabe.php?http_host=$http_host&id=$hash_id\" name=\"eingabe\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                        if ($u_level == "M") {
                            echo "<FRAMESET COLS=\"*,220\"  border=0 frameborder=0 framespacing=0>\n";
                            echo "<FRAME SRC=\"moderator.php?http_host=$http_host&id=$hash_id\" name=\"moderator\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=auto>\n";
                            echo "<FRAME SRC=\"interaktiv.php?http_host=$http_host&id=$hash_id\" name=\"interaktiv\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                            echo "</FRAMESET>\n";
                        } else {
                            echo "<FRAME SRC=\"interaktiv.php?http_host=$http_host&id=$hash_id\" name=\"interaktiv\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                        }
                        echo "<FRAME SRC=\"schreibe.php?http_host=$http_host&id=$hash_id\" name=\"schreibe\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                        echo "</FRAMESET>\n" . "<NOFRAMES>\n"
                            . "<!-- Browser/OS  $frame_type -->\n" . $body_tag
                            . $t['login6'];
                    }
                }
                
            } elseif (!$login_ok) {
                
                if ($rows == 0) {
                    // Zu viele fehlgeschlagenen Logins, aktueller Login ist
                    // wieder fehlgeschlagen, daher Mail an Betreiber verschicken
                    if ($userdata->u_loginfehler)
                        $u_loginfehler = unserialize($userdata->u_loginfehler);
                    $betreff = str_replace("%login%", $login, $t['login20']);
                    $text = str_replace("%login%", $login, $t['login21'])
                        . "\n";
                    foreach ($u_loginfehler as $key => $val) {
                        $text .= "[" . substr("00" . $key, -3) . "] "
                            . date("M d Y H:i:s", $val[login])
                            . " \tIP: $val[ip] \tPW: $val[pw]\n";
                    }
                    $text .= "\n-- \n   $chat ($serverprotokoll://"
                        . $HTTP_HOST . $_SERVER['PHP_SELF'] . " | "
                        . $http_host . ")\n";
                    mail($webmaster, $betreff, $text,
                        "From: $hackmail\nReply-To: $hackmail\nCC: $hackmail\n");
                }
                
                // Fehlermeldung ausgeben
                
                // Header ausgeben
                if ($layout_bodytag)
                    echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                        . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                        . "\n</HEAD>\n";
                zeige_kopf();
                unset($u_name);
                unset($u_nick);
                echo "<DIV align=center><P><B>"
                    . str_replace("%login%", $login, $t['login20'])
                    . "</B></P>" . $f3
                    . str_replace("%farbe_text%", $farbe_text, $disclaimer)
                    . $f4 . "</DIV>\n</FORM>";
                zeige_fuss();
                
            } else {
                
                // Login fehlgeschlagen
                
                // Header ausgeben
                if ($layout_bodytag)
                    echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                        . $metatag . $stylesheet . "\n" . $zusatztext_kopf
                        . "\n" . "</script>\n</HEAD>\n";
                zeige_kopf();
                
                unset($u_name);
                unset($u_nick);
                
                if (strlen($passwort) == 0) {
                    // Kein Passwort eingegeben oder der Nickname exitiert bereits
                    echo $t['login19'];
                } else {
                    // Falsches Passwort oder Nickname
                    echo $t['login7'];
                }
                
                // Box für Login
                if ($frameset_bleibt_stehen) {
                    echo "<FORM ACTION=\"$chat_file\" NAME=\"form1\" METHOD=\"POST\">"
                        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                } else {
                    echo "<FORM ACTION=\"$chat_file\" TARGET=\"topframe\" NAME=\"form1\" METHOD=\"POST\">"
                        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                }
                
                if ($gast_login && $communityfeatures && $forumfeatures) {
                    $titel = $login_titel . "[<A HREF=\""
                        . $_SERVER['PHP_SELF']
                        . "?http_host=$http_host&id=&aktion=login&"
                        . $t['login10'] . "=los&eintritt=forum\">" . $ft0
                        . $t['login23'] . $ft1 . "</A>]";
                } else {
                    $titel = $login_titel;
                }
                
                if ($einstellungen_aendern) {
                    $titel .= "[<A HREF=\"" . $_SERVER['PHP_SELF']
                        . "?http_host=$http_host&aktion=passwort_neu\">" . $ft0
                        . $t['login27'] . $ft1 . "</A>]";
                }
                
                // Box und Disclaimer ausgeben
                if (!isset($keineloginbox) || !$keineloginbox)
                    show_box($titel, $logintext, "", "100%");
                echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
                    . "// end hiding -->\n</script>\n";
                echo "<DIV align=center>" . $f3
                    . str_replace("%farbe_text%", $farbe_text, $disclaimer)
                    . $f4 . "</DIV>\n</FORM>";
                
                zeige_fuss();
                
            }
            break;
        
        case "neu":
        // Neu anmelden
        
        // Header ausgeben
            if ($layout_bodytag)
                echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                    . $metatag . $stylesheet . "\n" . $zusatztext_kopf . "\n"
                    . "</script>\n</HEAD>\n";
            zeige_kopf();
            echo $willkommen;
            
            // Gggf Nick setzen
            if ((isset($f['u_nick'])) && (strlen($f['u_nick']) < 4)) {
                $f['u_nick'] = $f['u_name'];
            }
            
            // Im Nick alle Sonderzeichen entfernen
            if (isset($f['u_nick']))
                $f['u_nick'] = coreCheckName($f['u_nick'], $check_name);
            
            // Tags aus dem Usernamen raus
            if (isset($f['u_name']))
                $f['u_name'] = strip_tags($f['u_name']);
            
            // Eingaben prüfen
            
            if ($los == $t['neu22']) {
                $ok = "1";
                if (strlen($f['u_name']) == 0) {
                    echo $t['neu1'];
                    $ok = "0";
                } elseif (strlen($f['u_name']) < 4) {
                    echo $t['neu2'];
                    $ok = "0";
                } elseif (strlen($f['u_name']) > 20) {
                    echo $t['neu3'];
                    $ok = "0";
                }
                
                $pos = strpos($f['u_nick'], "+");
                if ($pos === false)
                    $pos = -1;
                if ($pos >= 0) {
                    echo $t['neu44'];
                    $ok = "0";
                }
                
                if (strlen($f['u_nick']) > 20 || strlen($f['u_nick']) < 4) {
                    echo str_replace("%zeichen%", $check_name, $t['neu4']);
                    $ok = "0";
                }
                if (preg_match("/^" . $t['login13'] . "/i", $f['u_nick'])) {
                    echo str_replace("%gast%", $t['login13'], $t['neu32']);
                    $ok = "0";
                }
                if (strlen($f['u_passwort']) < 4) {
                    echo $t['neu5'];
                    $ok = "0";
                }
                if ($f['u_passwort'] != $u_passwort2) {
                    echo $t['neu6'];
                    $f['u_passwort'] = "";
                    $u_passwort2 = "";
                    $ok = "0";
                }
                
                if (strlen($f['u_email']) != 0
                    && !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
                        $f['u_email'])) {
                    echo $t['neu8'];
                    $ok = "0";
                }
                if (strlen($f['u_adminemail']) == 0
                    || !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})",
                        $f['u_adminemail'])) {
                    echo $t['neu7'];
                    $ok = "0";
                }
                
                // Gibts den Usernamen schon?
                $query = "SELECT u_id FROM user WHERE u_nick = '" . mysqli_real_escape_string($mysqli_link, $f['u_nick']) . "'";
                
                $result = mysqli_query($conn, $query);
                $rows = mysqli_num_rows($result);
                
                if ($rows != 0) {
                    echo $t['neu9'];
                    $ok = "0";
                }
                mysqli_free_result($result);
                
            } else {
                $ok = "0";
            }
            
            if (!isset($f['u_name']))
                $f['u_name'] = "";
            if (!isset($f['u_nick']))
                $f['u_nick'] = "";
            if (!isset($f['u_passwort']))
                $f['u_passwort'] = "";
            if (!isset($f['u_email']))
                $f['u_email'] = "";
            if (!isset($f['u_url']))
                $f['u_url'] = "";
            
            $text = "<TABLE><TR><TD ALIGN=RIGHT><B>" . $t['neu10']
                . "</B></TD>" . "<TD>" . $f1
                . "<INPUT TYPE=\"TEXT\" NAME=\"f[u_name]\" VALUE=\"$f[u_name]\" SIZE=40>"
                . $f2 . "</TD>" . "<TD><B>*</B>&nbsp;" . $f1 . $t['neu11']
                . $f2 . "</TD></TR>" . "<TR><TD ALIGN=RIGHT><B>" . $t['neu12']
                . "</B></TD>" . "<TD>" . $f1
                . "<INPUT TYPE=\"TEXT\" NAME=\"f[u_nick]\" VALUE=\"$f[u_nick]\" SIZE=40>"
                . $f2 . "</TD>" . "<TD>" . $f1 . $t['neu13'] . $f2
                . "</TD></TR>" . "<TR><TD ALIGN=RIGHT><B>" . $t['neu14']
                . "</B></TD>" . "<TD>" . $f1
                . "<INPUT TYPE=\"PASSWORD\" NAME=\"f[u_passwort]\" VALUE=\"$f[u_passwort]\" SIZE=40>"
                . $f2 . "</TD>" . "<TD><B>*</B></TD></TR>"
                . "<TR><TD ALIGN=RIGHT><B>" . $t['neu15'] . "</B></TD>"
                . "<TD>" . $f1
                . "<INPUT TYPE=\"PASSWORD\" NAME=\"u_passwort2\" VALUE=\"$f[u_passwort]\" SIZE=40>"
                . $f2 . "</TD>" . "<TD><B>*</B>" . $f1 . $t['neu16'] . $f2
                . "</TD></TR>";
            if (!isset($ro) || $ro == "") {
                $text .= "<TR><TD ALIGN=RIGHT><B>" . $t['neu17'] . "</B></TD>"
                    . "<TD>" . $f1
                    . "<INPUT TYPE=\"TEXT\" NAME=\"f[u_adminemail]\" VALUE=\""
                    . (isset($f[u_adminemail]) ? $f[u_adminemail] : "")
                    . "\" SIZE=40>" . $f2 . "</TD>" . "<TD><B>*</B>&nbsp;"
                    . $f1 . $t['neu18'] . $f2 . "</TD></TR>";
            } else {
                $text .= "<TR><TD COLSPAN=3>"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"f[u_adminemail]\" VALUE=\"$f[u_adminemail]\"></TD></TR>";
            }
            $text .= "<TR><TD ALIGN=RIGHT><B>" . $t['neu19'] . "</B></TD>\n"
                . "<TD>" . $f1
                . "<INPUT TYPE=\"TEXT\" NAME=\"f[u_email]\" VALUE=\"$f[u_email]\" SIZE=40>"
                . $f2 . "</TD>\n" . "<TD>" . $f1 . $t['neu20'] . $f2
                . "</TD></TR>\n" . "<TR><TD ALIGN=RIGHT><B>" . $t['neu21']
                . "</B></TD>\n" . "<TD>" . $f1
                . "<INPUT TYPE=\"TEXT\" NAME=\"f[u_url]\" VALUE=\"$f[u_url]\" SIZE=40>"
                . $f2 . "</TD>\n" . "<TD>" . $f1
                . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"neu\">\n"
                . "<TD>" . $f1
                . "<INPUT TYPE=\"HIDDEN\" NAME=\"hash\" VALUE=\""
                . (isset($hash) ? $hash : "") . "\">\n";
            if (!empty($backarray))
                $text .= "<INPUT TYPE=\"HIDDEN\" NAME=\"backarray\" VALUE=\""
                    . urlencode(urldecode($backarray)) . "\">";
            $text .= "<B><INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\""
                . $t['neu22'] . "\"></B>" . $f2 . "</TD>\n" . "</TR></TABLE>";
            if ($los != $t['neu22'])
                echo $t['neu23'];
            
            // Prüfe ob Mailadresse schon zu oft registriert, durch ZURÜCK Button bei der 1. registrierung
            if ($ok) {
                if ($begrenzung_anmeld_pro_mailadr > 0) {
                    $query = "select u_id from user WHERE u_adminemail = '" . mysqli_real_escape_string($mysqli_link, $f[u_adminemail]) . "'";
                    $result = mysqli_query($mysqli_link, $query);
                    $num = mysql_numrows($result);
                    if ($num >= $begrenzung_anmeld_pro_mailadr) {
                        $ok = 0;
                        echo str_replace("%anzahl%",
                            $begrenzung_anmeld_pro_mailadr, $t['neu55'])
                            . "<br><br>";
                        zeige_fuss();
                        break;
                    }
                }
            }
            
            if ($ok) {
                echo ($t['neu24']);
            }
            
            if ((!$ok && $los == $t['neu22']) || ($los != $t['neu22'])) {
                echo "<FORM ACTION=\"$chat_file\" NAME=\"form1\" METHOD=\"POST\">"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
                $titel = $ft0 . $t['neu31'] . $ft1;
                show_box($titel, $text, "", "");
                echo "</FORM>";
            }
            
            if ($ok && $los == $t['neu22']) {
                // Daten in DB als User eintragen
                
                if (!empty($backarray)) {
                    // fix für magic-quotes...
                    $backarray = urldecode($backarray);
                    $backarray = str_replace('\"', '"', $backarray);
                    $backarray = unserialize($backarray);
                    $text = $f1 . $backarray['text'] . $f2 . "\n"
                        . "<FORM ACTION=\"" . $backarray['url']
                        . "\" NAME=\"login\" METHOD=\"POST\">\n";
                    
                    if (is_array($backarray['parameter'])) {
                        for ($i = 0; $i < count($backarray['parameter']); $i++) {
                            $text .= "<INPUT TYPE=\"HIDDEN\" "
                                . $backarray['parameter'][$i] . ">\n";
                        }
                    }
                    $text .= "<B><INPUT TYPE=\"SUBMIT\" VALUE=\""
                        . $backarray['submittext'] . "\"></B>\n</FORM>\n";
                    $text = str_replace("{passwort}", $f['u_passwort'], $text);
                    $text = str_replace("{u_nick}", $f['u_nick'], $text);
                    
                    $titel = $backarray['titel'];
                } else {
                    $text = $t['neu25'] . "<TABLE><TR><TD ALIGN=RIGHT><B>"
                        . $t['neu26'] . "</B></TD>" . "<TD>" . $f1
                        . $f['u_name'] . $f2 . "</TD></TR>\n"
                        . "<TR><TD ALIGN=RIGHT><B>" . $t['neu27'] . "</B></TD>"
                        . "<TD>" . $f1 . $f['u_nick'] . $f2
                        . "</TD></TR></TABLE>\n" . $t['neu28']
                        . "<FORM ACTION=\"$chat_file\" NAME=\"login\" METHOD=\"POST\">\n"
                        . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
                        . "<INPUT TYPE=\"HIDDEN\" NAME=\"login\" VALUE=\"$f[u_nick]\">\n"
                        . "<INPUT TYPE=\"HIDDEN\" NAME=\"passwort\" VALUE=\"$f[u_passwort]\">\n"
                        . "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"login\">\n"
                        . "<B><INPUT TYPE=\"SUBMIT\" VALUE=\"" . $t['neu29']
                        . "\"></B>\n"
                        . "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n// end hiding -->\n</script>\n"
                        . "</FORM>\n";
                    $titel = $t['neu30'];
                }
                show_box($titel, $text, "", "");
                echo "<BR>";
                
                // Homepage muss http:// enthalten
                if (!preg_match("|^(http://)|i", $f['u_url'])
                    && strlen($f['u_url']) > 0) {
                    $f['u_url'] = "http://" . $f['u_url'];
                }
                
                $f['u_level'] = "U";
                $u_id = schreibe_db("user", $f, "", "u_id");
                $result = mysqli_query($conn, "UPDATE user SET u_neu=DATE_FORMAT(now(),\"%Y%m%d%H%i%s\") WHERE u_id=$u_id");
                
                if ($pruefe_email == "1") {
                    $query = "DELETE FROM mail_check WHERE email = '" . mysqli_real_escape_string($mysqli_link, $f[u_adminemail]) . "'";
                    $result = mysqli_query($mysqli_link, $query);
                }
                
            }
            
            zeige_fuss();
            
            break;
        
        case "relogin":
        // Login aus Forum in Chat; Userdaten setzen
            id_lese($id);
            $hash_id = $id;
            
            //system_msg("",0,$u_id,"","DEBUG: $neuer_raum ");
            
            // Chat betreten
            $back = betrete_chat($o_id, $u_id, $u_nick, $u_level, $neuer_raum,
                $o_js, $u_backup);
            
            // Frame-Einstellungen für Browser definieren
            $user_agent = strtolower($HTTP_USER_AGENT);
            if (preg_match("/linux/", $user_agent)) {
                $frame_type = "linux";
            } elseif (preg_match("/solaris/", $user_agent)) {
                $frame_type = "solaris";
            } elseif (preg_match("/msie/", $user_agent)) {
                $frame_type = "ie";
            } elseif (preg_match('#mozilla/4\.7#i', $user_agent)) {
                $frame_type = "nswin";
            } else {
                $frame_type = "def";
            }
            
            // Obersten Frame definieren
            if (!isset($frame_online))
                $frame_online = "";
            if (strlen($frame_online) == 0) {
                $frame_online = "frame_online.php";
            }
            if ($u_level == "M")
                $frame_size[$frame_type][interaktiv] = $moderationsgroesse;
            
            // Falls user eigene Einstellungen für das Frameset hat -> überschreiben
            $sql = "select u_frames from user where u_id = $u_id";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                $u_frames = mysql_result($result, 0, "u_frames");
            }
            if ($u_frames) {
                $u_frames = unserialize($u_frames);
                if (is_array($u_frames)) {
                    foreach ($u_frames as $key => $val) {
                        if ($val)
                            $frame_size[$frame_type][$key] = $val;
                    }
                }
            }
            mysqli_free_result($result);
            
            // Frameset aufbauen
            echo "<FRAMESET ROWS=\"$frame_online_size,*,"
                . $frame_size[$frame_type]['eingabe'] . ","
                . $frame_size[$frame_type]['interaktiv']
                . ",1\" border=0 frameborder=0 framespacing=0>\n";
            echo "<FRAME SRC=\"$frame_online?http_host=$http_host\" name=\"frame_online\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
            echo "<FRAMESET COLS=\"*,"
                . $frame_size[$frame_type]['chatuserliste']
                . "\" border=0 frameborder=0 framespacing=0>\n";
            echo "<FRAME SRC=\"chat.php?http_host=$http_host&id=$id&back=$back\" name=\"chat\" MARGINWIDTH=4 MARGINHEIGHT=0>\n";
            echo "<FRAME SRC=\"user.php?http_host=$http_host&id=$id&aktion=chatuserliste\" MARGINWIDTH=4 MARGINHEIGHT=0 name=\"userliste\">\n";
            echo "</FRAMESET>\n";
            echo "<FRAME SRC=\"eingabe.php?http_host=$http_host&id=$id\" name=\"eingabe\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
            if ($u_level == "M") {
                echo "<FRAMESET COLS=\"*,220\"  border=0 frameborder=0 framespacing=0>\n";
                echo "<FRAME SRC=\"moderator.php?http_host=$http_host&id=$id\" name=\"moderator\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=auto>\n";
                echo "<FRAME SRC=\"interaktiv.php?http_host=$http_host&id=$id\" name=\"interaktiv\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
                echo "</FRAMESET>\n";
            } else {
                echo "<FRAME SRC=\"interaktiv.php?http_host=$http_host&id=$id\" name=\"interaktiv\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
            }
            echo "<FRAME SRC=\"schreibe.php?http_host=$http_host&id=$id\" name=\"schreibe\" MARGINWIDTH=0 MARGINHEIGHT=0 SCROLLING=NO>\n";
            echo "</FRAMESET>\n" . "<NOFRAMES>\n"
                . "<!-- Browser/OS  $frame_type -->\n" . $body_tag
                . $t['login6'];
            
            break;
        
        default:
        // Homepage des Chats ausgeben
        
        // Kopf
        // Header ausgeben
            if ($layout_bodytag)
                echo "<HTML>\n<HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
                    . $metatag . $stylesheet . "\n" . $zusatztext_kopf . "\n"
                    . "</HEAD>\n";
            zeige_kopf();
            echo $willkommen;
            
            if ($zusatznachricht)
                echo "<P><BIG>" . $zusatznachricht . "</BIG></P>\n";
            
            echo "<table border=0 cellpadding=0 cellspacing=0><tr><td align=\"left\">";
            werbung("index_left", $werbung_gruppe);
            echo "</td><td><IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=\"4\" HEIGHT=\"4\"></td><td align=\"left\">";
            werbung("index_right", $werbung_gruppe);
            echo "</td></tr><tr><td><IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=\"4\" HEIGHT=\"4\"></td></tr></table>\n";
            // Box für Login
            if ($frameset_bleibt_stehen) {
                echo "<FORM ACTION=\"$chat_file\" NAME=\"form1\" METHOD=\"POST\">"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
            } else {
                echo "<FORM ACTION=\"$chat_file\" TARGET=\"topframe\" NAME=\"form1\" METHOD=\"POST\">"
                    . "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n";
            }
            
            if ($gast_login && $communityfeatures && $forumfeatures) {
                if (!isset($id))
                    $id = "";
                $titel = $login_titel . "[<A HREF=\"" . $_SERVER['PHP_SELF']
                    . "?http_host=$http_host&id=$id&aktion=login&"
                    . $t['login10'] . "=los&eintritt=forum\">" . $ft0
                    . $t['login23'] . $ft1 . "</A>]";
            } else {
                $titel = $login_titel;
            }
            
            if ($einstellungen_aendern) {
                $titel .= "[<A HREF=\"" . $_SERVER['PHP_SELF']
                    . "?http_host=$http_host&aktion=passwort_neu\">" . $ft0
                    . $t['login27'] . $ft1 . "</A>]";
            }
            
            // Box und Disclaimer ausgeben
            if (!isset($keineloginbox))
                show_box($titel, $logintext, "", "100%");
            echo "<script language=javascript>\n<!-- start hiding\ndocument.write(\"<input type=hidden name=javascript value=on>\");\n"
                . "// end hiding -->\n</script>\n";
            echo "<DIV align=center>" . $f3
                . str_replace("%farbe_text%", $farbe_text, $disclaimer) . $f4
                . "</DIV>\n</FORM>";
            werbung("index_popup", $werbung_gruppe);
            
            if (!isset($beichtstuhl) || !$beichtstuhl) {
                
                // Wie viele User sind in der DB?
                $query = "SELECT count(u_id) FROM user WHERE u_level in ('A','C','G','M','S','U')";
                $result = mysqli_query($conn, $query);
                $rows = mysqli_num_rows($result);
                if ($result) {
                    $useranzahl = mysql_result($result, 0, 0);
                    mysqli_free_result($result);
                }
                
                // User online und Räume bestimmen -> merken
                $query = "SELECT o_who,o_name,o_level,r_name,r_status1,r_status2, "
                    . "r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' as lobby "
                    . "FROM online left join raum on o_raum=r_id  "
                    . "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
                    . "ORDER BY lobby desc,r_name,o_who,o_name ";
                $result2 = mysqli_query($conn, $query);
                if ($result2)
                    $onlineanzahl = mysqli_num_rows($result2);
                // Anzahl der angemeldeten User ausgeben
                if ($onlineanzahl > 1 && $useranzahl > 1) {
                    $text = str_replace("%onlineanzahl%", $onlineanzahl,
                        $t['default2'])
                        . str_replace("%useranzahl%", $useranzahl,
                            $t['default3']);
                    
                    // Anzahl der Beiträge im Forum ausgeben
                    if ($communityfeatures && $forumfeatures) {
                        // Anzahl Themen
                        $query = "select count(th_id) from thema";
                        $result = mysqli_query($conn, $query);
                        if ($result AND mysqli_num_rows($result) > 0) {
                            $themen = mysql_result($result, 0, 0);
                            mysqli_free_result($result);
                        }
                        
                        // Dummy Themen abziehen
                        $query = "select count(th_id) from thema where th_name = 'dummy-thema'";
                        $result = mysqli_query($conn, $query);
                        if ($result AND mysqli_num_rows($result) > 0) {
                            $themen = $themen - mysql_result($result, 0, 0);
                            mysqli_free_result($result);
                        }
                        
                        // Anzahl Postings 
                        $query = "select count(po_id) from posting";
                        $result = mysqli_query($conn, $query);
                        if ($result AND mysqli_num_rows($result) > 0) {
                            $beitraege = mysql_result($result, 0, 0);
                            mysqli_free_result($result);
                        }
                        if ($beitraege && $themen)
                            $text .= str_replace("%themen%", $themen,
                                str_replace("%beitraege%", $beitraege,
                                    $t['default8']));
                    }
                    
                    show_box2(
                        str_replace("%chat%", $chat,
                            $ft0 . $t['default5'] . $ft1), $text, "100%",
                        false);
                    echo "<IMG SRC=\"pics/fuell.gif\" ALT=\"\" WIDTH=4 HEIGHT=4><BR>\n";
                }
                
                if (!isset($unterdruecke_raeume))
                    $unterdruecke_raeume = 0;
                if (!$unterdruecke_raeume && $abweisen == false) {
                    
                    // Wer ist online? Boxen mit Usern erzeugen, Topic ist Raumname
                    if ($onlineanzahl)
                        if (!isset($keineloginbox))
                            show_who_is_online($result2);
                }
                // result wird in "show_who_is_online" freigegeben
                // mysql_free_result($result2);
            }
            
            // Fuss
            zeige_fuss();
    }
}

?>
