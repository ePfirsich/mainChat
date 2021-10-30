<?php

function zeige_header_anfang($title, $stylesheet, $zusatz_header = '') {
	// Gibt den HTML-Header auf der Eingangsseite
	global $metatag;
	?>
	<!DOCTYPE html>
	<html dir="ltr" lang="de">
	<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<?php echo $metatag; ?>
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
	
	if($zusatz_header != '') {
		echo $zusatz_header;
	}
	?>
	</head>
	<?php
}
?>