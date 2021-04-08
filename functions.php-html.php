<?php

function zeige_header_anfang($title, $stylesheet, $zusatz_header = '') {
	// Gibt den HTML-Header auf der Eingangsseite
	global $metatag;
	?>
	<!DOCTYPE html>
	<html>
	<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<?php echo $metatag; ?>
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" href="css/<?php echo $stylesheet; ?>.css" type="text/css">
	
	
	
	
	<style type="text/css">
	body {
		background-color:<?php echo $hintergrundfarbe; ?>;
	<?php if(strlen($hintergrundgrafik) > 0) { ?>
		background-image:<?php echo $hintergrundgrafik; ?>;
	<?php } ?>
	}
	a:link {
		color:<?php echo $linkfarbe; ?>;
	}
	a:visited, a:active {
		color:<?php echo $linkfarbe_aktive; ?>;
	}
	</style>
	
	
	
	
	<?php
	if($zusatz_header != '') {
		echo $zusatz_header;
	}
}

function zeige_header_ende($zusatz_header = '') {
	// Gibt den HTML-Header auf der Eingangsseite
	
	if($zusatz_header != '') {
		echo $zusatz_header;
	}
	?>
	</head>
	<?php
}
?>