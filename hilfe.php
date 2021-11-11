<?php

require_once("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, o_js
id_lese($id);

$title = $body_titel . ' - Hilfe';
zeige_header_anfang($title, 'mini');
zeige_header_ende();
?>
<body>
<?php
// Menü als erstes ausgeben
echo "<br>";
$box = $t['menue1'];
$text = "<a href=\"hilfe.php?id=$id\">$t[menue4]</a>\n";
$text .= "| <a href=\"hilfe.php?id=$id&aktion=hilfe-befehle\">$t[menue5]</a>\n";
$text .= "| <a href=\"hilfe.php?id=$id&aktion=hilfe-sprueche\">$t[menue6]</a>\n";
$text .= "| <a href=\"hilfe.php?id=$id&aktion=hilfe-community\">$t[menue7]</a>\n";
$text .= "| <a href=\"hilfe.php?id=$id&aktion=chatiquette\">$t[menue8]</a>\n";
show_box_title_content($box, $text, true);
	
switch ($aktion) {
	case "chatiquette":
		// Datenschutz anzeigen
		require_once('templates/chatiquette.php');
		
		break;
		
	case "hilfe-befehle":
		id_lese($id);
		// Liste aller Befehle anzeigen
		require_once('templates/hilfe-befehle.php');
		
		break;
		
	case "hilfe-sprueche":
		// Liste aller Sprüche anzeigen
		require_once('templates/hilfe-sprueche.php');
		
		break;
		
	case "hilfe-community":
		// Punkte/Community anzeigen
		require_once('templates/hilfe-community.php');
		
		break;
		
	default:
		// Hilfe anzeigen
		require_once('templates/hilfe.php');
}
?>
</body>
</html>