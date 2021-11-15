<?php
switch ($aktion) {
	
	case "eintragen":
	// Ab in die Datenbank mit dem Eintrag
		eintrag_aktionen($aktion_datensatz);
		zeige_aktionen("normal");
		break;
	
	default:
		zeige_aktionen("normal");
}

$box = $t['aktion1'];
$text = $t['aktion2'];

// Box anzeigen
zeige_tabelle_zentriert($box, $text);
?>