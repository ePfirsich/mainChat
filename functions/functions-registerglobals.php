<?php

if (ini_get('register_globals') == 0) {
	// Lade alle Variablen in Lokale Variablen
	
	/*
	foreach ($_GET as $varname => $value) {
		global $$varname;
		$$varname = $value;
	}
	*/
	
	foreach ($_POST as $varname => $value) {
		global $$varname;
		$$varname = $value;
	}
}
?>