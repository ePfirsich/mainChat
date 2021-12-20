<?php
function checkFormularInputFeld($mysqli_link, $mysqldatei) {
	$mysqlfp = fopen($mysqldatei, "r");
	$mysqlinhalt = fread($mysqlfp, filesize($mysqldatei));
	$mysqlarray = explode(';', $mysqlinhalt);
	
	foreach ($mysqlarray as $key => $value) {
		// Das letzte Element ist immer nur ein leerer String
		if($value == "") {
			break;
		}
		mysqli_query($mysqli_link, $value);
	}
}
?>