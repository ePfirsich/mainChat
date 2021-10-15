<?php

require("functions.php");
require("conf/deutsch.php-smilies-grafik.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, o_js, u_level, admin
id_lese($id);

// optional Konfiguration für smilies lesen
if (isset($smiles_config) && $smilies_config
	&& file_exists("conf/" . $smilies_config)) {
	unset($smilie);
	unset($smilietxt);
	require("conf/" . $smilies_config);
}

$title = $body_titel . ' - Smilies';
zeige_header_anfang($title, 'mini');
?>
<script>
<?php
echo "  var id='$id';\n";
echo "  var stdparm='?id='+id;\n";
?>
function showsmiliegrafiken( liste ) {
	for(var i=0; i<liste.length; i+=3) {
		var rowdef = "<td style=\"text-align:center;\" class=\"" + color[i / 2 & 1] + "\"><a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_opener(' "+liste[i]+" '); return(false)\"><img src=\""+smilies_pfad+liste[i+1]+"\" style=\"border:0px;\" alt=\""+liste[i]+"\"></a></td>";
		rowdef += "<td class=\"" + color[i / 2 & 1] + "\">"+fett[4]+liste[i+2]+fett[5]+"</td>";
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
	echo schliessen_link();
	
	echo "<div style=\"text-align:center;\"><table style=\"width:100%; border:0px;\">\n";
	
	// Unterscheidung Javascript an/aus
	if ($o_js) {
		// Array mit Smilies einlesen, Tabelle in Javascript ausgeben
		reset($smilie);
		while (list($smilie_code, $smilie_grafik) = each($smilie)) {
			$jsarr[] = "'$smilie_code','$smilie_grafik','"
				. str_replace(" ", "&nbsp;", $smilietxt[$smilie_code]) . "'";
		}
		
		echo "\n\n<script language=\"JavaScript\">\n"
			. "   var color = new Array('tabelle_zeile1','tabelle_zeile2');\n"
			. "   var fett		  = new Array('$f1<b>','</b>$f2','$f3','$f4','$f1','$f2');\n"
			. "   var smilies_pfad  = '$smilies_pfad';\n"
			. "   var liste		 = new Array(\n   "
			. @implode(",\n   ", $jsarr) . "   );\n"
			. "   showsmiliegrafiken(liste);\n" . 
			//				 "   stdparm=''; stdparm2=''; id=''; u_nick=''; raum=''; nlink=''; nick=''; url='';\n".
			"</script>\n";
	} else { // kein javascript verfügbar
	
	// Array mit Smilies einlesen, HTML-Tabelle ausgeben
		reset($smilie);
		$zahl = 0;
		while (list($smilie_code, $smilie_grafik) = each($smilie)) {
			if ( $zahl % 2 != 0 ) {
				$farbe_tabelle = 'class="tabelle_zeile1"';
			} else {
				$farbe_tabelle = 'class="tabelle_zeile2"';
			}
			echo "<tr>"
		. "<td $farbe_tabelle>$f1<b>$smilie_code</b>$f2</TD>"
		. "<td $farbe_tabelle>" . $f1 . str_replace(" ", "&nbsp;", $smilietxt[$smilie_code]) . $f2 . "</TD>"
			. "<td $farbe_tabelle><img src=\"" . $smilies_pfad . $smilie_grafik . "\" alt=\"\"></TD>"
			. "</tr>\n";
			$zahl++;
		}
	}
	echo "</table></div>";
	
	echo schliessen_link();
	
} else {
	echo "<p style=\"text-align:center;\">$t[sonst15]</p>\n";
}

?>
</body>
</html>