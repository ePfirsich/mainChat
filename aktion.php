<?php

require_once("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, u_level, o_js
id_lese($id);

$title = $body_titel . ' - Aktionen';
zeige_header_anfang($title, 'mini');
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

if ($u_id) {
	// Menü als erstes ausgeben
	echo "<br>";
	$box = $t['aktion3'];
	$text = "<a href=\"hilfe.php?id=$id&aktion=hilfe-community#home\">Hilfe</a>\n";
	zeige_tabelle_zentriert($box, $text);
	
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
	
	echo "<br>";
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}
?>
</body>
</html>