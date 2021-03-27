<?php

require("functions.php");
require_once("functions-msg.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, admin
id_lese($id);

// $raum_einstellungen und $ist_moderiert setzen
raum_ist_moderiert($o_raum);

$body_tag = "<BODY BGCOLOR=\"$farbe_chat_background3\" ";
if (strlen($grafik_background3) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_background3\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_chat_text3\" "
    . "LINK=\"$farbe_chat_link3\" " . "VLINK=\"$farbe_chat_vlink3\" "
    . "ALINK=\"$farbe_chat_vlink3\">\n";

if (strlen($u_id) > 0) {
    
    // $chat_back gesetzt?
    if (isset($user_chat_back) && (strlen($user_chat_back) > 0)) {
        // chat_back in DB schreiben
        unset($f);
        $f['u_zeilen'] = $user_chat_back;
        schreibe_db("user", $f, $u_id, "u_id");
        $chat_back = $user_chat_back;
    }
    
    // sonderfix für das rotieren von text und text2 für ohne javascript...
    if (isset($text2) && strlen($text2) != 0)
        $text = $text2;
    
    // Falls private Nachricht, Nicknamen ergänzen
    if (isset($privat) && strlen($privat) > 2)
        $text = "/msgpriv $privat " . $text;
    
    // Initialisierung der Fehlerbehandlung
    $fehler = FALSE;
    
    // Falls $text eine Eingabezeile enthält -> verarbeiten
    
    // Prüfung der Eingabe bei Admin und Moderator auf 4 fache Anzahl der Normalen eingabe
    if ((($admin) || ($u_level == "M")) && isset($text) && (strlen($text) != 0)
        && (strlen($text) < (5 * $chat_max_eingabe))) {
    }
    // Normale Prüfung für User
 elseif (isset($text) && strlen($text) != 0
        && strlen($text) < $chat_max_eingabe) {
        
        // Spamschutz prüfen, falls kein Admin und kein Moderator
        if ((!$admin) && ($u_level <> "M")) {
            
            // Geschriebene Zeilen und Bytes aus DB lesen (Variable in id_lese() gesetzt)
            $spam_zeilen = unserialize($o_spam_zeilen);
            $spam_byte = unserialize($o_spam_byte);
            
            // Zeitpunkt sekundengenau bestimmen => Jeder Eintrag im Array entspricht einer Sekunde
            // mktime ( int Stunde, int Minute, int Sekunde, int Monat, int Tag, int Jahr
            $temp = getdate();
            $aktuelle_zeit = mktime($temp["hours"], $temp["minutes"],
                $temp["seconds"], $temp["mon"], $temp["mday"], $temp["year"]);
            
            // Array-Keys auf die richtige Zeit umrechnen
            // Alle Einträge im Array löschen, die älter als $chat_max_zeit Sekunden sind
            unset($neu_spam_zeilen);
            unset($neu_spam_byte);
            $zeitdifferenz = $aktuelle_zeit - $o_spam_zeit;
            if (is_array($spam_zeilen) && $o_spam_zeit) {
                foreach ($spam_zeilen as $key => $value) {
                    if ((($key - $zeitdifferenz) * -1) < $chat_max_zeit) {
                        $neu_spam_zeilen[$key - $zeitdifferenz] = $spam_zeilen[$key];
                        $neu_spam_byte[$key - $zeitdifferenz] = $spam_byte[$key];
                    }
                }
            } else {
                $o_spam_zeit = $aktuelle_zeit;
            }
            
            // Aktuelle Eingabe im Array summieren
            if (!isset($neu_spam_zeilen)) {
                $neu_spam_zeilen = array();
            }
            if (!isset($neu_spam_zeilen[0])) {
                $neu_spam_zeilen[0] = 0;
            }
            $neu_spam_zeilen[0] += 1;
            if (!isset($neu_spam_byte)) {
                $neu_spam_byte = array();
            }
            if (!isset($neu_spam_byte[0])) {
                $neu_spam_byte[0] = 0;
            }
            $neu_spam_byte[0] += strlen($text);
            
            unset($f);
            $f['o_spam_zeit'] = $aktuelle_zeit;
            $f['o_spam_zeilen'] = serialize($neu_spam_zeilen);
            $f['o_spam_byte'] = serialize($neu_spam_byte);
            schreibe_db("online", $f, $o_id, "o_id");
            
            // Prüfen wieviel Byte ind wieviel Zeilen in den letzten $chat_max_zeit Sekunden geschrieben wurde 
            if ((array_sum($neu_spam_zeilen) > $chat_max_zeilen)
                || (array_sum($neu_spam_byte) > $chat_max_byte)) {
                $fehler = TRUE;
            }
        }
        
        @mysqli_free_result($result);
        
    } elseif (isset($text) && strlen($text) >= $chat_max_eingabe) {
        $fehler = TRUE;
    }
    
    // Falls kein Spam, Nachricht ausgeben
    if (!$fehler) {
        if (strlen($u_farbe) == 0) {
            $u_farbe = $user_farbe;
        }
        chat_msg($o_id, $u_id, $u_nick, $u_farbe, $admin, $o_raum,
            isset($text) ? $text : "", "");
    }
    // Spam -> Fehler ausgeben
 elseif ($fehler) {
        // Systemnachricht mit Fehlermeldung an User schreiben
        system_msg("", 0, $u_id, $system_farbe,
            $t['floodsperre1'] . " " . $text);
        
        // Zur Strafe 10 Punkte abziehen
        if ($communityfeatures && $punktefeatures) {
            $anzahl = 10;
            punkte((-1) * $anzahl, $o_id, $u_id, $t['floodsperre2'], TRUE);
        }
        
    }
    
    // Kopf ausgeben
    echo "<HTML><HEAD></HEAD>" . $body_tag;
    
    // Timestamp im Datensatz aktualisieren
    aktualisiere_online($u_id, $o_raum);
    
    // Falls Pull-Chat, chat-Fenster neu laden (sicherer Modus), falls User im Chat
    if ($o_who != 2 && ($backup_chat || $u_backup)) {
        echo "<SCRIPT LANGUAGE=JavaScript>\n"
            . "parent.chat.location.href='chat.php?http_host=$http_host&id=$id'"
            . "</SCRIPT>\n";
    }
    // falls Moderator, moderationsfenster nach Eingabe neu laden, falls User im Chat
    if ($o_who != 2 && $u_level == "M") {
        echo "<SCRIPT LANGUAGE=JavaScript>\n"
            . "parent.moderator.location.href='moderator.php?http_host=$http_host&id=$id'"
            . "</SCRIPT>\n";
    }
    
    echo "</BODY></HTML>\n";
    
} else {
    
    echo "User nicht gefunden! ($id, $u_id, $u_name)<BR>";
    sleep(5);
    
    // User wird nicht gefunden. Login ausgeben
    echo "<HTML><HEAD></HEAD><HTML>";
    echo "<BODY onLoad='javascript:parent.location.href=\"index.php?http_host=$http_host\"'>\n";
    echo "</BODY></HTML>\n";
    exit;
}

?>
