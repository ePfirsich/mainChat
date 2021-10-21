<?php

// Sprachdefinition deutsch hilfe.php

$CHATHOSTNAME = $http_host . dirname($PHP_SELF);
if (substr($CHATHOSTNAME, -1) != "/")
	$CHATHOSTNAME .= "/";

$t['hilfe15'] = "Automatischer Logout";
$t['hilfe16'] = "<p>Sie wurden automatisch aus dem $chat ausgelogt, weil Sie %zeit%&nbsp;Minuten lang nichts geschrieben haben!</b></p>";

$t['sonst1'] = "Fenster schließen";
?>
