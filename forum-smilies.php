<?php

require("functions.php");
require("conf/deutsch.php-smilies.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, o_js, u_level, admin
id_lese($id);

$title = $body_titel . ' - Info';
zeige_header_anfang($title, 'mini');
?>
<script>
<?php
echo "  var http_host='$http_host';\n";
echo "  var id='$id';\n";
echo "  var stdparm='?http_host='+http_host+'&id='+id;\n";
?>
function showsmilies( liste ) {
	 for(var i=0; i<liste.length; i+=2) {
		 var rowdef = "<td class=\"" + color[i / 2 & 1] + "\">&nbsp;"+fett[0]+"<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_opener(' "+liste[i]+" '); return(false)\">"+liste[i]+"</A>"+fett[1]+"&nbsp;</td>";
		 rowdef += "<td class=\"" + color[i / 2 & 1] + "\">"+fett[4]+liste[i+1]+fett[5]+"</td>";
		 document.write("<tr>"+rowdef+"</tr>\n");
	 }
}

function appendtext_opener( text ) {
	 opener.parent.frames['forum'].document.forms['form'].elements['po_text'].value=text + opener.parent.frames['forum'].document.forms['form'].elements['po_text'].value;
	 opener.parent.frames['forum'].document.forms['form'].elements['po_text'].focus();
}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Login ok?
if (strlen($u_id) != 0) {
	echo "<CENTER><TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
	
	// Unterscheidung Javascript an/aus
	if ($o_js) {
		echo "<tr><td colspan=\"2\" align=\"center\"><a href=# onClick=\"window.close()\">$f3 Fenster schliessen $f4</a></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n";
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
		
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><a href=# onClick=\"window.close()\">$f3 Fenster schliessen $f4</a></td></tr>\n";
		
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
	
	echo "</TABLE></CENTER>";
	
} else {
	echo "<p style=\"text-align:center;\">" . $t['sonst15'] . "</p>\n";
}

?>
</body>
</html>