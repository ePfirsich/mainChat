<?php

// eingabe.php muss mit id=$hash_id aufgerufen werden

require_once("functions/functions.php");
require_once("languages/$sprache-chat.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == "") {
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "chateingabe");

$title = $body_titel;
zeige_header_anfang($title, 'chateingabe', '', $benutzerdaten['u_layout_farbe']);
?>
	<script>
	function resetinput() {
		document.forms['form'].elements['text'].value=document.forms['form'].elements['text2'].value;
		document.forms['form'].elements['text2'].value='';
		document.forms['form'].submit();
		document.forms['form'].elements['text2'].focus();
		document.forms['form'].elements['text2'].select();
	}
	</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Eingabeformular mit Menu und Farbauswahl
reset($farbe_chat_user);
$i = 0;
echo "<div style=\"margin-top: 3px;\"><form name=\"form\" method=\"post\" target=\"schreibe\" action=\"schreibe.php\" onSubmit=\"resetinput(); return false;\">";

// Typ Eingabefeld für Chateingabe setzen
if ($u_level == "M") {
	$text2_typ = "<textarea rows=\"3\" name=\"text2\" autofocus autocomplete=\"off\" cols=\"" . $chat_eingabe_breite . "\"></textarea>";
} else {
	$text2_typ = "<input type=\"text\" name=\"text2\" autofocus autocomplete=\"off\" maxlength=\"" . ($chat_max_eingabe - 1) . "\" value=\"\" size=\"" . $chat_eingabe_breite . "\">";
}

// Unterscheidung Normal oder sicherer Modus
if ($sicherer_modus == 1 || $benutzerdaten['u_sicherer_modus'] == "1") {
	echo $text2_typ . "<input name=\"text\" value=\"\" type=\"hidden\">"
		. "<select name=\"user_chat_back\">\n";
	for ($i = 5; $i < 40; $i++) {
		echo "<option " . ($chat_back == $i ? "selected" : "") . " value=\"$i\">$i&nbsp;$t[eingabe1]\n";
	}
	echo "</select>";
} else {
	echo $text2_typ . "<input name=\"text\" value=\"\" type=\"hidden\">";
}
echo "<input name=\"id\" value=\"$id\" type=\"hidden\">";
?>
</form>
</div>
</body>
</html>