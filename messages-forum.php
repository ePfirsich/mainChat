<?php

require("functions.php");
require("functions.php-func-chat_lese.php");

// Benutzerdaten setzen
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, 'chatunten');

// Benutzerdaten gesetzt?
if (strlen($u_id) > 0) {
	aktualisiere_online($u_id, $o_raum);
	
	$meta_refresh = '<meta http-equiv="refresh" content="' . intval($timeout / 3) . '; URL=messages-forum.php?id=' . $id . '">';
	zeige_header_ende($meta_refresh);
	?>
	<body style="margin-left:5px; margin-bottom:5px;">
	<?php

	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum, 2);
	
	// Aktuelle Privat- und Systemnachrichten oder Statusmeldung ausgeben
	if (!chat_lese($o_id, $o_raum, $u_id, TRUE, $ignore, 10, TRUE)) {
		echo $f1 . $t['messages_forum1'] . $f2 . "\n";
	}

} else {
	// Benutzer wird nicht gefunden. Login ausgeben
	zeige_header_ende();
	?>
	<body onLoad='javascript:parent.location.href="index.php"'>
	<?php
}
?>
</body>
</html>