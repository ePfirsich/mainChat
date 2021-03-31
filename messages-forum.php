<?php

require("functions.php");
require("functions.php-func-chat_lese.php");

// Userdaten setzen
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, $farbe_background3, $grafik_background3, $farbe_chat_link3, $farbe_chat_vlink3);

// Userdaten gesetzt?
if (strlen($u_id) > 0) {
	aktualisiere_online($u_id, $o_raum);
	
	// Fenstername
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);
	
	$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=messages-forum.php?http_host=' . $http_host . '&id=' . $id . '">\n';
	$meta_refresh .= "<script>\n" . "function neuesFenster(url,name) {\n"
		. "hWnd=window.open(url,name,\"resizable=yes,scrollbars=yes,width=300,height=580\");\n"
		. "}\n" . "function neuesFenster2(url) {\n"
		. "hWnd=window.open(url,\"640_$fenster\",\"resizable=yes,scrollbars=yes,width=780,height=580\");\n"
		. "}\n" . "</script>\n";
	
	zeige_header_ende($meta_refresh);
	?>
	<body>
	<?php

	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum, 2);
	
	// eigene Farbe für BG gesetzt? dann die nehmen.
	if ($u_farbe_bg != "" && $u_farbe_bg != "-")
		$farbe_chat_background1 = $u_farbe_bg;
	
	if (strlen($grafik_background1) > 0) {
		$table_option = "BACKGROUND=\"$grafik_background1\"";
	} else {
		$table_option = "BGCOLOR=\"$farbe_chat_background1\"";
	}
	?>
	<table style="width:100%;">
		<tr>
			<td>
				<table <?php echo $table_option; ?> style="width:100%;">
					<tr>
						<td>
						<?php
						// Aktuelle Privat- und Systemnachrichten oder Statusmeldung ausgeben
						if (!chat_lese($o_id, $o_raum, $u_id, TRUE, $ignore, 10, TRUE)) {
							echo $f1 . $t['messages_forum1'] . $f2 . "\n";
						}
						?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<?php
} else {
	// User wird nicht gefunden. Login ausgeben
	
	zeige_header_ende();
	?>
	<body onLoad="javascript:parent.location.href="index.php?http_host=<?php echo $http_host; ?>">
	<?php
}
?>
</body>
</html>