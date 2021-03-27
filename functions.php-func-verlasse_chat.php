<?php

function verlasse_chat($u_id, $u_name, $raum)
{
    // user $u_id/$u_name verlässt $raum
    // Nachricht in Raum $raum wird erzeugt
    // Liefert ID des geschriebenen Datensatzes zurück
    
    global $dbase, $chat, $system_farbe, $t, $lustigefeatures;
    global $eintritt_individuell, $eintritt_useranzeige;
    $back = 0;
    
    // Nachricht an alle
    if ($raum && $u_id) {
        $text = $t['chat_msg102'];
        
        if ($eintritt_individuell == "1") {
            $query = "SELECT u_austritt FROM user where u_id = $u_id";
            $result = mysql_query($query);
            $row = mysql_fetch_object($result);
            if (strlen($row->u_austritt) > 0) {
                $text = $row->u_austritt;
                if ($eintritt_useranzeige == "1")
                    $text = "<b>&lt;&lt;&lt;</b> " . htmlspecialchars($text)
                        . " (<b>$u_name</b> - verlässt Chat) ";
                else $text = "<b>&lt;&lt;&lt;</b> " . htmlspecialchars($text)
                    . " <!-- (<b>$u_name</b> - verlässt Chat) -->";
            }
            mysql_free_result($result);
            
            $query = "SELECT r_name FROM raum where r_id = " . intval($raum);
            $result = mysql_query($query);
            $row = mysql_fetch_object($result);
            if (isset($row->r_name)) {
                $r_name = $row->r_name;
            } else {
                $r_name = "[unbekannt]";
            }
            
            mysql_free_result($result);
        }
        
        $text = str_replace("%u_name%", $u_name, $text);
        $text = str_replace("%user%", $u_name, $text);
        $text = str_replace("%r_name%", $r_name, $text);
        
        $text = preg_replace("|%nick%|i", $u_name, $text);
        $text = preg_replace("|%raum%|i", $r_name, $text);
        
        $back = global_msg($u_id, $raum, $text);
    }
    
    return ($back);
}

?>
