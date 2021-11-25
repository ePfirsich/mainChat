<?php

function zeige_header_anfang($title, $stylesheet, $zusatz_header = '', $u_layout_farbe = 0) {
	// Gibt den HTML-Header auf der Eingangsseite
	global $metatag, $cssDeklarationen;
	?>
	<!DOCTYPE html>
	<html dir="ltr" lang="de">
	<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<?php echo $metatag;
	if($u_layout_farbe == 1) { // grün
		$cssDeklarationen = "gruen";
	} else if($u_layout_farbe == 2) { // rot
		$cssDeklarationen = "rot";
	} else if($u_layout_farbe == 3) { // pink
		$cssDeklarationen = "pink";
	} else { // blau
		$cssDeklarationen = "blau";
	}
	?>
	<link rel="stylesheet" href="css/style-<?php echo $cssDeklarationen; ?>.css" type="text/css">
	<link rel="stylesheet" href="css/style.css" type="text/css">
	<link rel="stylesheet" href="css/<?php echo $stylesheet; ?>.css" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<?php
	if($zusatz_header != '') {
		echo $zusatz_header;
	}
}

function zeige_header_ende($zusatz_header = '') {
	// Gibt den HTML-Header auf der Eingangsseite
	global $u_nick;
	
	if($zusatz_header != '') {
		echo $zusatz_header;
	}
	?>
	<script>
	function ask(text) {
			return(confirm(text));
	}
	</script>
	<script language="JavaScript" src="jscript.js"></script>
	</head>
	<?php
}
?>