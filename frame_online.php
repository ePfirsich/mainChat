<?php

require "functions-init.php";

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel; ?></title>
<meta charset="utf-8">
<style type="text/css">
<?php echo $stylesheet; ?>
body {
	background-color:<?php echo $farbe_background; ?>;
<?php if(strlen($grafik_background) > 0) { ?>
	background-image:<?php echo $grafik_background; ?>;
<?php } ?>
}
a, a:link {
	color:<?php echo $farbe_chat_link; ?>;
}
a:visited, a:active {
	color:<?php echo $farbe_chat_vlink; ?>;
}
</style>
</head>
<body>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=100%><TR><TD ALIGN=CENTER>

<!-- Ab hier folgt der Kopf in HTML -->

<!-- Ende des Kopfs in HTML -->
</TD><TD ALIGN=CENTER>
<?php
// Werbebanner ausgeben
if (isset($banner) && strlen($banner) > 0) {
    readfile($banner);
}
?>
</TD></TR></TABLE>
<?php
if (isset($ivw) && $ivw)
    echo $ivw . "\n";
?>
</BODY></HTML>
