<?php

require_once("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, o_js, u_level, admin
id_lese($id);

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$title = $body_titel . ' - Smilies';
zeige_header_anfang($title, 'mini', '', $u_layout_farbe);

echo "<script>\n";
echo "  var id='$id';\n";
echo "  var stdparm='?id='+id;\n";
echo "</script>\n";

zeige_header_ende();
?>
<body>
<?php
// Login ok?
if (strlen($u_id) != 0) {
	$box = "<center>" . $t['sonst1'] . "</center>";
	$text = "";
	
	// Menue ausgeben, Tabelle aufbauen
	$linkuser = "href=\"user.php?id=$id&aktion=chatuserliste\"";
	$linksmilies = "href=\"smilies.php" . "?id=$id\"";
	
	$text .= "<div style=\"text-align:center;\"><a $linksmilies><span class=\"fa fa-refresh icon16\"></span>" . $t['sonst3'] . "</a> | <a $linkuser><span class=\"fa fa-user icon16\"></span>" . $t['sonst2'] . "</a><br>";
	
	$text .= zeige_smilies('chat');
	
	$text .= "<a $linksmilies><span class=\"fa fa-refresh icon16\"></span>" . $t['sonst3'] . "</a> | <a $linkuser><span class=\"fa fa-user icon16\"></span>" . $t['sonst2'] . "</a></div>";
	
	show_box_title_content($box, $text);
	
}
?>
</body>
</html>