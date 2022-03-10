<?php
require_once("functions/functions.php");
require_once("functions/functions-schreibe.php");
require_once("languages/$sprache-chat.php");
require_once("languages/$sprache-smilies.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum
id_lese($id);

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	header('Location: ' . $chat_url);
	exit();
	die;
}

// Hole alle benötigten Einstellungen des Benutzers
$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");

// Raumwechsel
$o_raum_alt = filter_input(INPUT_POST, 'o_raum_alt', FILTER_SANITIZE_NUMBER_INT);
$neuer_raum = filter_input(INPUT_POST, 'neuer_raum', FILTER_SANITIZE_NUMBER_INT);

// Wurde Raum neuer_raum aus Formular übergeben? Falls ja Raum von $o_raum nach $neuer_raum wechseln
if (isset($neuer_raum) && $o_raum != $neuer_raum) {
	// Raum wechseln
	$o_raum = raum_gehe($o_id, $u_id, $u_nick, $o_raum, $neuer_raum);
	if ($o_raum == $neuer_raum) {
		// Benutzer in Raum ausgeben
		raum_user($neuer_raum, $u_id);
	}
}


$meta_refresh = '<script type="text/javascript">
	/* <![CDATA[ */
		function refreshRaum() {
			$("[id=raum]").load("raum.php");
		}
	
		$(document).ready(function(){
			window.setInterval("refreshRaum()", 10000);
			refreshRaum();
		});
	/* ]]> */
	</script>';

$title = $body_titel;
zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh);

echo "<body class=\"chatunten\">";

// Typ Eingabefeld für Chateingabe setzen
$text = "<form name=\"form\" method=\"post\">";
$text .= "<input type=\"text\" name=\"text\" autofocus autocomplete=\"off\" maxlength=\"1000\" value=\"\" size=\"111\">\n";
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
<?php
$text = "<div class=\"tabsy\">\n";

// Raumliste anzeigen
$text .= "<input type=\"radio\" id=\"tab1\" name=\"tab\" checked=\"checked\" >\n";
$text .= "<label class=\"tabButton\" for=\"tab1\">Räume</label>\n";
$text .= "<div class=\"tab\">\n";
$text .= "<div class=\"tab-content\">\n";


$text .= "<div id=\"raum\"></div>\n";


$text .= "</div>\n";
$text .= "</div>\n";

// Smilies anzeigen
$text .= "<input type=\"radio\" id=\"tab2\" name=\"tab\">\n";
$text .= "<label class=\"tabButton\" for=\"tab2\">Smilies</label>\n";
$text .= "<div class=\"tab\">\n";
$text .= "<div class=\"tab-content\" style=\"height: 68px; width: 100%; overflow-y: scroll;\">\n";

$text .= zeige_smilies('chat', $benutzerdaten);

$text .= "</div>\n";
$text .= "</div>\n";
$text .= "</div>\n";

zeige_tabelle_zentriert("", $text, true, false);
?>
</body>
</html>