<?php

function zeige_header_anfang($title, $hintergrundfarbe, $hintergrundgrafik, $linkfarbe, $linkfarbe_aktive, $zusatz_header = '') {
	// Gibt den HTML-Header auf der Eingangsseite
	global $metatag, $stylesheet;
	?>
	<!DOCTYPE html>
	<html>
	<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<?php echo $metatag; ?>
	<style type="text/css">
	<?php echo $stylesheet; ?>
	body {
		background-color:<?php echo $hintergrundfarbe; ?>;
	<?php if(strlen($hintergrundgrafik) > 0) { ?>
		background-image:<?php echo $hintergrundgrafik; ?>;
	<?php } ?>
	}
	a, a:link {
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