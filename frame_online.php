<?php

require "functions-init.php";

$body_tag = "<BODY BGCOLOR=\"$farbe_background\" ";
if (strlen($grafik_background) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_background\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_text\" " . "LINK=\"$farbe_link\" "
    . "VLINK=\"$farbe_vlink\" " . "ALINK=\"$farbe_vlink\">\n";
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel; ?></title>
<meta charset="utf-8">
<?php echo $stylesheet; ?>
</head>
<?php echo $body_tag; ?>
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
