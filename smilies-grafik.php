<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, o_js, u_level, admin
id_lese($id);

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$title = $body_titel . ' - Smilies';
zeige_header_anfang($title, 'mini');

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
	
	// Menue ausgeben, Tabelle aufbauen
	$linkuser = "href=\"user.php?id=$id&aktion=chatuserliste\"";
	$linksmilies = "href=\"smilies-grafik.php" . "?id=$id\"";
	
	echo "<div style=\"text-align:center;\">$f1" . "[<a onMouseOver=\"return(true)\" $linksmilies>" . $t['sonst3'] . "</a>]&nbsp;"
		. "[<a onMouseOver=\"return(true)\" $linkuser>" . $t['sonst2'] . "</a>]$f2<br>";
	
	zeige_smilies('chat');
	
	echo "$f1" . "[<a onMouseOver=\"return(true)\" $linksmilies>" . $t['sonst3'] . "</a>]&nbsp;"
		. "[<a onMouseOver=\"return(true)\" $linkuser>" . $t['sonst2'] . "</a>]$f2</div>";
	
} else {
	echo "<p style=\"text-align:center;\">$t[sonst15]</p>\n";
}
?>
</body>
</html>