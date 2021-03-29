<?php

include("functions.php");

$body_tag = "<BODY BGCOLOR=\"$farbe_chat_background3\" ";
if (strlen($grafik_background3) > 0) {
    $body_tag = $body_tag . "BACKGROUND=\"$grafik_background3\" ";
}
$body_tag = $body_tag . "TEXT=\"$farbe_chat_text3\" "
    . "LINK=\"$farbe_chat_link3\" " . "VLINK=\"$farbe_chat_vlink3\" "
    . "ALINK=\"$farbe_chat_vlink3\">\n";
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel; ?></title>
<meta charset="utf-8">
</head><?php echo $body_tag; ?></body>
</html>