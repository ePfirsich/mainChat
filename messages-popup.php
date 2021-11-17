<?php

require_once("functions/functions.php");
require_once("functions/functions-func-chat_lese.php");

// Benutzerdaten setzen
id_lese($id);

$title = $body_titel;
zeige_header_anfang($title, 'chatausgabe', '', $u_layout_farbe);

// Benutzerdaten gesetzt?
if (strlen($u_id) > 0) {
	
	// Ohne die Stringersetzung, würde das Fenster bei Umlauten 
	// auf die Startseite springen, da id_lese ein Problem damit hat
	$userfuerrefresh = urlencode($user_nick);
	$meta_refresh = '<meta http-equiv="refresh" content="' . intval(15) . '; URL=messages-popup.php?id=' . $id . '&user=' . $user . '&user_nick=' . $userfuerrefresh . '">';
	zeige_header_ende($meta_refresh);
	?>
	<body onLoad="window.scrollTo(1,300000)">
	<?php
	// Timestamp im Datensatz aktualisieren
	aktualisiere_online($u_id, $o_raum, 2);
	
	// Aktuelle Privat- und Systemnachrichten oder Statusmeldung ausgeben
	if (!chat_lese($o_id, $o_raum, $u_id, TRUE, $ignore, 10, TRUE, $user)) {
		echo $t['chat_msg106'];
	}
	
	$pmu = mysqli_query($mysqli_link, "UPDATE chat SET c_gelesen=1 WHERE c_gelesen=0 AND c_typ='P' AND c_von_user_id=".$user);
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