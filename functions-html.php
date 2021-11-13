<?php

function zeige_header_anfang($title, $stylesheet, $zusatz_header = '', $u_layout_farbe = 1) {
	// Gibt den HTML-Header auf der Eingangsseite
	global $metatag;
	
//	echo "Test: " . $u_layout_farbe . "<br>";
	?>
	<!-- <?php echo $u_layout_farbe ?> -->
	<!DOCTYPE html>
	<html dir="ltr" lang="de">
	<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<?php echo $metatag;
	if($u_layout_farbe == 2) { // grün
		$cssDeklarationen = "gruen";
	} else if($u_layout_farbe == 3) { // rot
		$cssDeklarationen = "rot";
	} else if($u_layout_farbe == 4) { // pink
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
	
	// Fenstername
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);
	?>
	<script>
	function ask(text) {
			return(confirm(text));
	}
	
	function win_reload(file,win_name) {
		win_name.location.href=file;
	}
	
	function opener_reload(file,frame_number) {
		opener.parent.frames[frame_number].location.href=file;
	}

	function window_reload(file,win_name) {
		win_name.location.href=file;
	}
	
	function neuesFenster(url) {
			hWnd=window.open(url,"chat_popup","resizable=yes,scrollbars=yes,width=800,height=600");
	}
	</script>
	<script language="JavaScript" src="jscript.js"></script>
	</head>
	<?php
}
?>