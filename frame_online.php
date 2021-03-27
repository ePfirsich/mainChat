<?php

require "functions-init.php";

$body_tag = "<BODY BGCOLOR=\"$farbe_background\" ";
if (strlen($grafik_background) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_background\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_text\" " . "LINK=\"$farbe_link\" "
    . "VLINK=\"$farbe_vlink\" " . "ALINK=\"$farbe_vlink\">\n";

echo "<HTML><HEAD><TITLE>$body_titel</TITLE><META CHARSET=UTF-8>\n"
    . "$stylesheet</HEAD>\n$body_tag\n";

?>
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
