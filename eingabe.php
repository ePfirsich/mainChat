<?php
require_once("functions/functions.php");
require_once("functions/functions-schreibe.php");
require_once("languages/$sprache-chat.php");

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_URL);
if( $id == '') {
	$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_URL);
}

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "chateingabe");

$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe']);

echo "<body class=\"chatunten\">";

// Typ Eingabefeld für Chateingabe setzen
if ($u_level == "M") {
	$text_typ = "<textarea rows=\"3\" name=\"text\" autofocus autocomplete=\"off\" maxlength=\"255\" cols=\"111\"></textarea>\n";
} else {
	$text_typ = "<input type=\"text\" name=\"text\" autofocus autocomplete=\"off\" maxlength=\"255\" value=\"\" size=\"111\">\n";
}
$text = "<form name=\"form\" method=\"post\">";
$text .= $text_typ;
// Unterscheidung Normal oder sicherer Modus
if ($sicherer_modus || $benutzerdaten['u_sicherer_modus'] == "1") {
	$text .= "<select name=\"user_chat_back\">\n";
	for ($i = 10; $i <= 40; $i++) {
		$text .= "<option " . ($chat_back == $i ? "selected " : "") . "value=\"$i\">$i $t[eingabe1]\n";
	}
	$text .= "</select>\n";
}
$text .= "<input name=\"id\" value=\"$id\" type=\"hidden\">\n";
$text .= "<button type=\"submit\">Go!</button>";
$text .= "</form>";

zeige_tabelle_zentriert("", $text, true, false);
?>
<span id="out"></span>
<script>
	document.querySelector('form').addEventListener('submit', event => {
		event.preventDefault();
		fetch('schreibe.php', {
			method: 'post',
			body: new FormData(document.querySelector('form'))
		}).then(res => {
			return res.text();
		}).then(res => {
			//console.log(res);
			document.querySelector('input[name="text"]').value = '';
			document.getElementById('out').innerHTML = res;
		});
	})
</script>
</body>
</html>