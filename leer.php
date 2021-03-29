<?php

include("functions.php");

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel; ?></title>
<meta charset="utf-8">
<style type="text/css">
<?php echo $stylesheet; ?>
body {
	background-color:<?php echo $farbe_background3; ?>;
<?php if(strlen($grafik_background3) > 0) { ?>
	background-image:<?php echo $grafik_background3; ?>;
<?php } ?>
}
a, a:link {
	color:<?php echo $farbe_chat_link3; ?>;
}
a:visited, a:active {
	color:<?php echo $farbe_chat_vlink3; ?>;
}
</style>
<body>
</body>
</html>