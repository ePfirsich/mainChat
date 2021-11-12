<?php
if (isset($extra_agb) and ($extra_agb <> "") && ($extra_agb <> "standard")) {
	$t['agb'] = $extra_agb;
}
$box = $t['menue5'];
$text = $t['agb'] . "\n";

show_box_title_content($box,$text);
?>