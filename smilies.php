<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$title = $body_titel . ' - Info';
zeige_header_anfang($title, 'mini');
?>
<?php
echo "<script>\n";
echo "  var http_host='$http_host';\n";
echo "  var id='$id';\n";
echo "  var stdparm='?http_host='+http_host+'&id='+id;\n";
echo "</script><script src=\"jscript.js\"></script>\n";

zeige_header_ende();
?>
<body>
<?php
// Login ok?
if (strlen($u_id) != 0) {
	// Menue ausgeben, Tabelle aufbauen
	if (!isset($r_name)) {
		$r_name = "";
	}
	$linkuser = "href=\"user.php?http_host=$http_host&id=$id&aktion=chatuserliste\"";
	$linksmilies = "href=\"" . $smilies_datei
		. "?http_host=$http_host&id=$id\"";
	echo "<CENTER><b>$r_name</b><br>$f3<b>[<a onMouseOver=\"return(true)\" $linksmilies>"
		. $t['sonst1'] . "</A>]&nbsp;"
		. "[<a onMouseOver=\"return(true)\" $linkuser>" . $t['sonst2']
		. "</A>]</b>$f4<br>"
		. "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\" >\n";
	
	// Unterscheidung JavaScript an/aus
	if ($o_js) {
		
		// Array mit Smilies einlesen, Tabelle in Javascript ausgeben
		reset($smilie);
		while (list($smilie_code, $smilie_text) = each($smilie)) {
			$jsarr[] = "'$smilie_code','"
				. str_replace(" ", "&nbsp;", $smilie_text) . "'";
		}
		
		echo "\n\n<script language=\"JavaScript\">\n"
			. "   var color = new Array('tabelle_zeile1','tabelle_zeile2');\n"
			. "   var fett  = new Array('$f1<b>','</b>$f2','$f3','$f4','$f1','$f2');\n"
			. "   var liste = new Array(\n   " . @implode(",\n   ", $jsarr)
			. "   );\n" . "   showsmilies(liste);\n" . "</script>\n";
		
	} else { // kein javascript verfügbar
	
	// Array mit Smilies einlesen, HTML-Tabelle ausgeben
		reset($smilie);
		$schalt = TRUE;
		while (list($smilie_code, $smilie_text) = each($smilie)) {
			if ($schalt) {
				$farbe_tabelle = 'class="tabelle_zeile1"';
				$schalt = FALSE;
			} else {
				$farbe_tabelle = 'class="tabelle_zeile2"';
				$schalt = TRUE;
			}
			echo "<TR><TD $farbe_tabelle>$f1<b>$smilie_code</b>$f2</TD>"
				. "<TD $farbe_tabelle>" . $f1 . str_replace(" ", "&nbsp;", $smilie_text) . $f2
				. "</TD></TR>\n";
		}
	}
	
	echo "</TABLE>"
		. "<b>$r_name</b><br>$f3<b>[<a onMouseOver=\"return(true)\" $linksmilies>"
		. $t['sonst1'] . "</A>]&nbsp;"
		. "[<a onMouseOver=\"return(true)\" $linkuser>" . $t['sonst2']
		. "</A>]</b>$f4</CENTER>";
	
} else {
	echo "<p style=\"text-align:center;\">$t[sonst15]</p>\n";
}

?>
</body>
</html>