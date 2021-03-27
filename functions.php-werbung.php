<?php

// Funktionen, die NUR von interaktiv.php verwendet werden.

function werbung($werbung_flaeche, $werbung_gruppe)
{
    global $f1;
    global $f2;
    global $werbung_art;
    global $werbung_inhalt;
    global $werbung_default;
    global $werbefrei;
    
    if ($werbung_gruppe != "mietchat") {
        if (file_exists("werbung/fallback.php"))
            include("werbung/fallback.php");
        $gruppen = explode(",", $werbung_gruppe);
        
        // zunächst jede Gruppe aus der config versuchen...
        $werbunggelesen = 0;
        $i = 0;
        while ($werbunggelesen == 0 && !empty($gruppen[$i])) {
            $gruppen[$i] = str_replace(".php", "", $gruppen[$i]) . ".php";
            $werbung_gruppe = $gruppen[$i];
            if (file_exists("werbung/$werbung_gruppe")) {
                include("werbung/$werbung_gruppe");
                $werbunggelesen = 1;
            }
            $i++;
        }
        
        // wenn immer noch nicht geklappt, dann die Fallbacks der 
        // config-gruppen und deren Fallbacks...
        if (!$werbunggelesen && isset($wfb) && is_array($wfb)) {
            // werbe-fall-back aus datei.
            $i = 0;
            while ($werbunggelesen == 0 && !empty($gruppen[$i])) {
                $werbung_gruppe = $gruppen[$i];
                $j = 0;
                while ($werbunggelesen == 0 && !empty($wfb[$werbung_gruppe])
                    && $j < 10) {
                    $werbung_gruppe = $wfb[$werbung_gruppe];
                    if (file_exists("werbung/$werbung_gruppe")) {
                        include("werbung/$werbung_gruppe");
                        $werbunggelesen = 1;
                    }
                    $j++;
                }
                $i++;
            }
        }
        
        if (!$werbunggelesen && !empty($werbung_default)) {
            if (file_exists("werbung/$werbung_default")) {
                include("werbung/$werbung_default");
                $werbunggelesen = 1;
            }
        }
        
        if ($werbunggelesen && isset($werbung)) {
            $werbung_inhalt = $werbung[$werbung_flaeche]['inhalt'];
            $werbung_art = $werbung[$werbung_flaeche]['art'];
        }
    } else {
        // Spezialbehandlung mietchat: da hier nur einmal werbung gezeigt wird:
        if ($werbung_flaeche != "interaktiv") {
            unset($werbung_inhalt);
        }
    }
    
    // jetzt werbung ausgeben...
    if (!empty($werbung_inhalt)) {
        if ($werbung_art) {
            echo "<TABLE BORDER=1 CELLPADDING=3 BGCOLOR=\"#FFFFFF\"><TR><TD ALIGN=CENTER VALIGN=MIDDLE><B>"
                . $f1 . $werbung_inhalt . $f2 . "</B></TD></TR></TABLE>";
        } else {
            echo $werbung_inhalt;
        }
    }
}

?>
