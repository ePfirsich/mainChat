<?php

function step_1()
{
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" align=\"center\">\n"
        . "<tr><td><img src=\"images/wizard.gif\" width=\"110\" height=\"180\" alt=\"\" border=\"\"></td>\n"
        . "<td style=\"font-size:15px; font-family: Arial;\"><p align=\"center\"><b>Hinweise</b><br>\n"
        . "Alle Rechte an mainChat vorbehalten, (C) fidion GmbH<br>\n"
        . "Weitere Informationen zur Installation finden Sie hier [<a href=\"liesmich.php\" onClick=\"newWindow('liesmich.php','LIESMICH');return false;\">LIESMICH</a>]</p></td>\n"
        . "<td><img src=\"images/fidionlogo.gif\" width=\"138\" height=\"51\" alt=\"\" border=\"0\"</td></tr>\n"
        . "<tr><td colspan=\"2\"><br></td></tr></table>\n";
    
    echo "<form action=\"$PHP_SELF\" method=\"post\">\n"
        . "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n"
        . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>Datenbank Einstellungen</b></td></tr>\n"
        . "<tr><td>Datenbankserver:</td><td><input type=\"text\" name=\"chat[host]\" size=\"40\">*</td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td>Datenbankname:</td><td><input type=\"text\" name=\"chat[dbase]\" size=\"40\">*</td></tr>\n"
        . "<tr><td>Datenbankuser:</td><td><input type=\"text\" name=\"chat[user]\" size=\"40\">*</td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td>Mysql-Passwort:</td><td><input type=\"password\" name=\"chat[pass]\" size=\"40\"></td></tr>\n"
        . "<tr><td>Mysql-Passwort (wiederholen):</td><td><input type=\"password\" name=\"chat[pass2]\" size=\"40\"></td></tr>\n"
        . "<tr><td colspan=\"2\"><br><br></td></tr>\n"
        . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px;text-align:center;color:White;\"><b>Globale Einstellungen</b></td></tr>\n"
        . "<tr><td> E-Mail Webmaster:</td><td><input type=\"text\" name=\"chat[webmaster]\" size=\"40\">*</td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Zuständige Person für Hackversuche:</td><td><input type=\"text\" name=\"chat[hackmail]\" size=\"40\">*</td></tr>\n"
        . "<tr><td> Name des Chats:</td><td><input type=\"text\" name=\"chat[chatname]\" size=\"40\">*</td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Voreingestellte Sprachdatei:</td><td><select name=\"chat[language]\">\n"
        . "<option value=\"deutsch.php\">Deutsch</option>\n"
        . "<option value=\"englisch.php\">Englisch</option>\n" . "</select>"
        . "<tr><td> Beim Login Auswahl des Raums möglich:</td><td>ja<input type=\"radio\" name=\"chat[raumauswahl]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[raumauswahl]\" value=\"0\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Beim Login Boxen mit Chattern unterdrücken:</td><td>ja<input type=\"radio\" name=\"chat[unterdrueckeraeume]\" value=\"1\">&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[unterdrueckeraeume]\" value=\"0\" checked></td></tr>\n"
        . "<tr><td> Lobby (Defaultraum):</td><td><input type=\"text\" name=\"chat[lobby]\" value=\"Lobby\" size=\"40\">*</td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Anzeige Indexseite oben:</td><td><input type=\"text\" name=\"chat[layoutkopf]\" size=\"40\"></td></tr>\n"
        . "<tr><td> Anzeige Indexseite unten:</td><td><input type=\"text\" name=\"chat[layoutfuss]\" size=\"40\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td colspan=\"2\">Metatag:</td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Content-Type:</td><td><input type=\"text\" name=\"chat[contenttype]\" size=\"40\" value=\"text/html; charset=utf-8\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Author:</td><td><input type=\"text\" name=\"chat[author]\" value=\"mainchat@mainmedia.de\" size=\"40\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Description:</td><td><textarea cols=\"30\" rows=\"5\" name=\"chat[description]\">mainChat http://www.mainchat.de - Die HTML-Chat-Community für jede Homepage</textarea></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Keywords:</td><td><textarea cols=\"30\" rows=\"3\" name=\"chat[keywords]\">Chat Community HTML kostenlos frei Testen Mieten Lizenz</textarea></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Language:</td><td><input type=\"text\" name=\"chat[metalanguage]\" value=\"deutsch, de\" size=\"40\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Date:</td><td><input type=\"text\" name=\"chat[date]\" value=\"08.07.00\" size=\"40\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Expire:</td><td><input type=\"text\" name=\"chat[expire]\" value=\"08.07.01\" size=\"40\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Robots:</td><td><input type=\"text\" name=\"chat[robots1]\" value=\"index\" size=\"40\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td> Robots:</td><td><input type=\"text\" name=\"chat[robots2]\" value=\"follow\" size=\"40\"></td></tr>\n"
        . "<tr><td colspan=\"2\">Text für Startseite im Bereich NOFRAMES für Suchmaschinen:</td></tr>\n"
        . "<tr><td colspan=\"2\"><textarea cols=\"80\" rows=\"5\" name=\"chat[noframes]\"><P><B>mainChat</B> ist die schnelle HTML Chat Community in PHP von \n"
        . "der <B><A HREF=\"http://www.fidion.de/\">fidion GmbH</A></B> in Würzburg. \n"
        . "Er wird in einer kostenlosen, einer Miet-Lizenz und einer Lizenz zum Kauf angeboten.<BR>\n"
        . "<B>Features:</B> Mail, Forum, Punkte, beliebig viele Räume und User, IRC-Befehle, Multihoming, SSL, \n"
        . "Support für Webchat.de, Smilie-Grafiken (Smilies), Gäste, Administratoren, Top10 / Top100 Listen, \n"
        . "Freunde, User-Homepages, Profile, Privatnachrichten, öffentliche Nachrichten, Mehrsprachig, \n"
        . "vordefinierte Sprüche, Nicknamen-Ergänzung, Teergruben, Moderation, Spam-Schutz und vieles mehr.<BR>\n"
        . "Weitere Informationen zur <B>mainChat-Community</B> finden Sie auf unserem "
        . "<B><A HREF=\"http://www.mainchat.de/\">Angebot</A></B>\n"
        . "</P><P ALIGN=\"CENTER\"><A HREF=\"index.php\">weiter</A></P></textarea></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Stylesheet:</td><td><textarea cols=\"30\" rows=\"4\" name=\"chat[style]\"><style type=\"text/css\">\n"
        . "td, small, p, b, i, font {font-family:Arial,Helvetica;}\n"
        . "</style>\n</textarea></td></tr>\n"
        . "<tr><td> Anonymer Gast-Login möglich:</td><td>ja<input type=\"radio\" name=\"chat[gastlogin]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[gastlogin]\" value=\"0\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Beliebig viele Gast-Login von einer IP:</td><td>ja<input type=\"radio\" name=\"chat[gastloginanzahl]\" value=\"1\">&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[gastloginanzahl]\" value=\"0\" checked></td></tr>\n"
        . "<tr><td> Passwort in DB verschlüsseln:</td><td>ja<input type=\"radio\" name=\"chat[cryptlogin]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[cryptlogin]\" value=\"0\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Erste Buchstabe groß:</td><td>ja<input type=\"radio\" name=\"chat[uppername]\" value=\"1\">&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[uppername]\" value=\"0\" checked></td></tr>\n"
        . "<tr><td colspan=\"2\"><br><br></td></tr>\n"
        . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>Sonstige Einstellungen</b></td></tr>\n"
        . "<tr><td> Verzeichnis für Logdateien:</td><td><input type=\"text\" name=\"chat[log]\" size=\"40\" value=\"logs\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Datei in der alle Sprüche sind:</td><td><input type=\"text\" name=\"chat[spruchliste]\" size=\"40\" value=\"sprueche.conf\"></td></tr>\n"
        . "<tr><td> Befehl für Traceroute:</td><td><input type=\"text\" name=\"chat[traceroute]\" size=\"40\" value=\"/usr/sbin/traceroute\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Admin zeigen, wer einen Spruch eingegeben hat:</td><td>ja<input type=\"radio\" name=\"chat[showspruch]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[showspruch]\" value=\"0\"></td></tr>\n"
        . "<tr><td> Zusätzliche Adminfeatueres freischalten:</td><td>ja<input type=\"radio\" name=\"chat[adminfeatures]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[adminfeatures]\" value=\"0\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Lustige Texte einschalten:</td><td>ja<input type=\"radio\" name=\"chat[lustigefeatures]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[lustigefeatures]\" value=\"0\"></td></tr>\n"
        . "<tr><td> Erweiterte Funktionen freischalten (Miet- u. Kauflizenz):</td><td>ja<input type=\"radio\" name=\"chat[erfeatures]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[erfeatures]\" value=\"0\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Communityfeatures:</td><td>ja<input type=\"radio\" name=\"chat[comfeatures]\" value=\"1\">&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[comfeatures]\" value=\"0\" checked></td></tr>\n"
        . "<tr><td> Extra Module der Community (Forum):</td><td>ja<input type=\"radio\" name=\"chat[forumfeatures]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[forumfeatures]\" value=\"0\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Punktezählung einschalten:</td><td>ja<input type=\"radio\" name=\"chat[punktefeatures]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[punktefeatures]\" value=\"0\"></td></tr>\n"
        . "<tr><td> Chat versendet SMS:</td><td>ja<input type=\"radio\" name=\"chat[smsfeatures]\" value=\"1\">&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[smsfeatures]\" value=\"0\" checked></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Moderationsmodul freigeschalten:</td><td>ja<input type=\"radio\" name=\"chat[modmodul]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[modmodul]\" value=\"0\"></td></tr>\n"
        . "<tr><td colspan=\"2\"><br><br></td></tr>\n"
        . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>Globale Voreinstellungen für Frames</b></td></tr>\n"
        . "<tr><td> Optionale URL des linken Frames:</td><td><input type=\"text\" name=\"chat[framelinks]\"  size=\"40\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Frameset bleibt beim Login in Chat stehen:</td><td>ja<input type=\"radio\" name=\"chat[framesetbleibt]\" size=\"40\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[framesetbleibt]\" size=\"40\"></td></tr>\n"
        . "<tr><td colspan=\"2\"><br><br></td></tr>\n"
        . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>Smilies</b></td></tr>\n"
        . "<tr><td> Smiliesdatei:</td><td><input type=\"text\" name=\"chat[smiliesdatei]\" size=\"40\" value=\"smilies-grafik.php\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Smiliespfad:</td><td><input type=\"text\" name=\"chat[smiliespfad]\" size=\"40\" value=\"pics/smile/\"></td></tr>\n"
        . "<tr><td> Smilies Hintergrund:</td><td><input type=\"text\" name=\"chat[smiliesbg]\" size=\"40\" value=\"#FFFFFF\"></td></tr>\n"
        . "<tr bgcolor=\"#85D4FF\"><td > Max. Anzahl Smilies pro Zeile:</td><td><input type=\"text\" name=\"chat[smiliesanzahl]\" size=\"40\" value=\"3\"></td></tr>\n"
        . "<tr><td colspan=\"2\"><br><br></td></tr>\n"
        . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>Einstellungen für das Forum</b></td></tr>\n"
        . "<tr><td> Datenschutzhinweis anzeigen:</td><td>ja<input type=\"radio\" name=\"chat[datenschutz]\" value=\"1\" checked>&nbsp;&nbsp;nein<input type=\"radio\" name=\"chat[datenschutz]\" value=\"0\"></td></tr>\n"
        . "<tr><td colspan=\"2\">Mit * gekennzeichnete Felder sind Pflichtfelder</td></tr>\n"
        . "<tr><td colspan=\"2\"><br></td></tr>\n"
        . "<input type=\"hidden\" name=\"aktion\" value=\"step_2\">\n"
        . "<tr><td colspan=\"2\"><input type=\"submit\" name=\"los\" value=\"Installation starten\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type=\"reset\" value=\"Standardwerte wiederherstellen\"</td></tr>\n"
        . "</form>\n" . "</table>\n";
}

function step_2($connect, $select, $chat, $fpconfig)
{
    $configtpl = "../conf/config.php-tpl";
    $fpconfigtpl = fopen($configtpl, "r");
    $inhalt = fread($fpconfigtpl, filesize($configtpl));
    foreach ($chat as $key => $value)
        $inhalt = str_replace("%$key%", addslashes($value), $inhalt);
    fwrite($fpconfig, $inhalt, strlen($inhalt));
    
    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n"
        . "<tr bgcolor=\"#007ABE\"><td style=\"font-size:15px; text-align:center;color:White;\"><b>Konfigurationsdatei</b></td></tr>\n"
        . "<tr><td > Im Verzeichnis conf wurde folgende Konfigurationsdatei angelegt: <br>\n";
    $config = "../conf/config.php";
    if (!file_exists($config))
        echo "<span style=\"color:red\">Anlegen der <b>config.php</b> misslungen</span></td></tr>\n";
    else echo "<b>config.php</b></td></tr>\n";
    
    $mysqldatei = "../dok/mysql.def";
    $mysqlfp = fopen($mysqldatei, "r");
    $mysqlinhalt = fread($mysqlfp, filesize($mysqldatei));
    $mysqlarray = explode(';', $mysqlinhalt);
    
    foreach ($mysqlarray as $key => $value)
        mysql_query($value);
    
    echo "<tr><td colspan=\"2\"><br><br></td></tr>\n"
        . "<tr bgcolor=\"#007ABE\"><td style=\"font-size:15px; text-align:center;color:White;\"><b>Datenbank</b></td></tr>\n"
        . "<tr><td>In der Datenbank " . $chat['dbase'] . " (Datenbankuser: "
        . $chat['user'] . ") wurden folgende Tabellen " . "angelegt: <br>\n";
    
    $tables = mysql_listtables($chat['dbase']);
    for ($i = 0; $i < mysql_num_rows($tables); $i++) {
        $table = mysql_tablename($tables, $i);
        echo "<b>" . $table . "</b>, \n";
    }
    echo "</td></tr>" . "<tr><td colspan=\"2\"><br><br></td></tr>\n";
    
    echo "<tr><td> Mit <b>Nickname admin und Passwort admin</b> können Sie sich beim ersten Mal anmelden!<br>\n"
        . "<a href=\"../index.php\">zum Chat</a></tr></td></table>\n";
    
}

?>