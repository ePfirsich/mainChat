<?php

if (ini_get('register_globals') == 0) {
    
    // Initialisierung der Variablen
    $frame = 0;
    $aktion = "";
    $los = "";
    
    // Lade alle Variablen in Lokale Variablen
    foreach ($_GET as $varname => $value) {
        global $$varname;
        $$varname = $value;
    }
    
    foreach ($_POST as $varname => $value) {
        global $$varname;
        $$varname = $value;
    }
    
    // für home.php argc & argv
    foreach ($_SERVER as $varname => $value) {
        global $$varname;
        $$varname = $value;
    }
}

if (($frame != 1) && ($frame != 0)) {
    $frame = 0;
}

?>
