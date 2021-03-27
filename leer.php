<?php

include("functions.php");

$body_tag = "<BODY BGCOLOR=\"$farbe_chat_background3\" ";
if (strlen($grafik_background3) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_background3\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_chat_text3\" "
    . "LINK=\"$farbe_chat_link3\" " . "VLINK=\"$farbe_chat_vlink3\" "
    . "ALINK=\"$farbe_chat_vlink3\">\n";
echo "<HTML><HEAD><TITLE>$body_titel></TITLE><META CHARSET=UTF-8></HEAD>$body_tag</BODY></HTML>\n";

?>
