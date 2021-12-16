<?php

function zeige_header($title, $u_layout_farbe, $zusatz_header = '', $minimalistisch = false) {
	// Gibt den HTML-Header aus
	global $metatag;
	?>
	<!DOCTYPE html>
	<html dir="ltr" lang="de">
	<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<?php if($minimalistisch == false) { ?>
	<?php echo $metatag; ?>
	<link rel="stylesheet" href="css/style-<?php echo $u_layout_farbe; ?>.css" type="text/css">
	<link rel="stylesheet" href="css/style.css?v=001" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<script src="ckeditor/ckeditor.js"></script>
	<?php
	if($zusatz_header != '') {
		echo $zusatz_header;
	}
	?>
	<script>
	function ask(text) {
			return(confirm(text));
	}
	</script>
	<script type="text/javascript" src="js/jscript.js?v=001"></script>
	
	<!-- jQuery Color picker -->
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<link rel="stylesheet" href="css/wheelcolorpicker.css" type="text/css">
	<script type="text/javascript" src="js/jquery.wheelcolorpicker.min.js"></script>
	<?php } ?>
	</head>
	<?php
}
?>