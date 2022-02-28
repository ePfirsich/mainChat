<?php

function zeige_header($title, $u_layout_farbe, $zusatz_header = '', $minimalistisch = false) {
	// Gibt den HTML-Header aus
	global $metatag, $sprache;
	?>
	<!DOCTYPE html>
	<html dir="ltr" lang="<?php echo $sprache; ?>">
	<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<?php if($minimalistisch == false) { ?>
	<?php echo $metatag; ?>
	<link rel="stylesheet" href="css/style-<?php echo $u_layout_farbe; ?>.css?v=003" type="text/css">
	<link rel="stylesheet" href="css/style.css?v=003" type="text/css">
	<link rel="stylesheet" href="fontawesome/css/all.css">
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
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
	<link rel="stylesheet" href="css/wheelcolorpicker.css" type="text/css">
	<script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>
	<script type="text/javascript" src="js/jquery.wheelcolorpicker.min.js"></script>
	<?php } ?>
	</head>
	<?php
}
?>