<?php

// Liefert ein Bild inkl. Header zur direkten Darstellung im Browser
// Übergabeparameter:
//			u_id -> Benutzer, zu dem das Bild gehört
//			feld -> Feldname in der DB

require_once("functions/functions.php");

$u_id = filter_input(INPUT_GET, 'u_id', FILTER_SANITIZE_NUMBER_INT);
$feld = filter_input(INPUT_GET, 'feld', FILTER_SANITIZE_URL);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Cache-Control: post-check=0, pre-check=0", FALSE);

$cache = "home_bild";
$feld = mysqli_real_escape_string($mysqli_link, $feld);

if ($feld != "ui_bild1" && $feld != "ui_bild2" && $feld != "ui_bild3" && $feld != "ui_bild4" && $feld != "ui_bild5" && $feld != "ui_bild6" && $feld != "avatar") {
	echo "Fehlerhaftes 'feld' in home_bild.php'<br>";
	exit;
}

if (!is_numeric($u_id)) {
	echo "Fehlerhafte 'u_id' in 'home_bild.php'<br>";
	exit;
}

require("functions/functions-init.php");

$bild = "";
$b_mime = "";

// Überprüfen, ob ein gecached Bild vorhanden ist
$cachepfad = $cache . "/" . "/" . substr($u_id, 0, 2) . "/" . $u_id . "/" . $feld;

// Prüfe ob der Benutzer existiert und die Benutzerseiten aktiviert ist
$query = "SELECT `u_chathomepage` FROM `user` WHERE `u_id`=$u_id ";
$result = sqlQuery($query);
if ($result && mysqli_num_rows($result) == 1) {
	/*
	if (mysqli_result($result, 0, "u_chathomepage") != '1' && $feld != "avatar") {
		echo "Die Benutzerseiten dieses Benutzers ist deaktiviert!<br>";
		exit;
	}
	*/
} else {
	echo "Der Benutzer wurde nicht gefunden!<br>";
	exit;
}
mysqli_free_result($result);

$anzeigeauscache = false;
if (file_exists($cachepfad)) {
	// Datei ist da
	// Prüfe ob Größe mit der in DB übereinstimmt
	
	// Hole Größe
	$image = getimagesize($cachepfad);
	if (is_array($image)) {
		// Typ Wert in Typ Text
		switch ($image[2]) {
			case 1:
				$image[2] = "image/gif";
				break;
			case 2:
				$image[2] = "image/jpeg";
				break;
			case 3:
				$image[2] = "image/png";
				break;
			case 4:
				$image[2] = "application/x-shockwave-flash";
				break;
			default:
				$image[2] = "";
		}
		
		// Größen aus DB
		$query = "SELECT b_width, b_height, b_mime FROM bild WHERE b_user=$u_id AND b_name='$feld'";
		$result = sqlQuery($query);
		if ($result && mysqli_num_rows($result) == 1) {
			$b_width = mysqli_result($result, 0, "b_width");
			$b_heigth = mysqli_result($result, 0, "b_height");
			$b_mime = mysqli_result($result, 0, "b_mime");
			
			// Wenn DB Werte = Cache Werte dann Cache laden
			if (($image[0] == $b_width) && ($image[1] == $b_heigth)
				&& ($image[2] == $b_mime)) {
				$anzeigeauscache = true;
			}
		}
		mysqli_free_result($result);
	}
}

if ($anzeigeauscache) {
	$datei = fopen($cachepfad, 'rb');
	if ($datei) {
		$bild = fread($datei, filesize($cachepfad));
		fclose($datei);
	}
	$datei = fopen($cachepfad . "-mime", 'r');
	if ($datei) {
		$b_mime = fread($datei, 1024);
		fclose($datei);
	}
	
	// Alte Daten löschen (mit 0,1% Wahrscheinlichkeit)
	if (mt_rand(1, 1000) == 1) {
		$befehl = "find " . $cache . "/ -mtime +1 -exec rm {} \;";
		exec($befehl);
	}
	
} else {
	// Bild aus der DB lesen
	
	$query = "SELECT b_bild,b_mime FROM bild WHERE b_user=$u_id AND b_name='$feld'";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) == 1) {
		$b_mime = mysqli_result($result, 0, "b_mime");
		$bild = mysqli_result($result, 0, "b_bild");
	}
	mysqli_free_result($result);
	
	// Bild in den Cache schreiben
	if (!@stat($cache)) {
		mkdir($cache, 0777);
	}
	if (!@stat($cache . "/")) {
		mkdir($cache . "/", 0777);
	}
	if (!@stat($cache . "/" . "/" . substr($u_id, 0, 2))) {
		mkdir($cache . "/" . "/" . substr($u_id, 0, 2), 0777);
	}
	if (!@stat($cache . "/" . "/" . substr($u_id, 0, 2) . "/" . $u_id)) {
		mkdir($cache . "/" . "/" . substr($u_id, 0, 2) . "/" . $u_id,0777);
	}
	
	$datei = fopen($cachepfad, "w");
	if ($datei) {
		fputs($datei, $bild, strlen($bild));
		fclose($datei);
	}
	$datei = fopen($cachepfad . "-mime", "wb");
	if ($datei) {
		fwrite($datei, $b_mime);
		fclose($datei);
	}
}

if ($b_mime) {
	header("Content-Type: $b_mime");
	echo $bild;
}
?>