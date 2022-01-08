<?php

if (ini_get('register_globals') == 0) {
	// Initialisierung der Variablen
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
?>