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

if ($feld != "ui_bild1" && $feld != "ui_bild2" && $feld != "ui_bild3" && $feld != "ui_bild4" && $feld != "ui_bild5" && $feld != "ui_bild6" && $feld != "avatar") {
	echo "Fehlerhaftes 'feld' in home_bild.php'<br>";
	exit;
}

if (!is_numeric($u_id)) {
	echo "Fehlerhafte 'u_id' in 'home_bild.php'<br>";
	exit;
}

$bild = "";
$b_mime = "";

// Überprüfen, ob ein gecached Bild vorhanden ist
$cachepfad = $cache . "/" . "/" . substr($u_id, 0, 2) . "/" . $u_id . "/" . $feld;

// Prüfe ob der Benutzer existiert und die Benutzerseiten aktiviert ist
$query = pdoQuery("SELECT `u_chathomepage` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);

$resultCount = $query->rowCount();
if ($resultCount == 0) {
	echo "Der Benutzer wurde nicht gefunden!<br>";
	exit;
}

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
		
		// Größen auslesen
		$query = pdoQuery("SELECT `b_width`, `b_height`, `b_mime` FROM `bild` WHERE `b_user` = :b_user AND `b_name` = :b_name", [':b_user'=>$u_id, ':b_name'=>$feld]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$b_width = $result['b_width'];
			$b_heigth = $result['b_height'];
			$b_mime = $result['b_mime'];
			
			// Wenn DB Werte = Cache Werte dann Cache laden
			if (($image[0] == $b_width) && ($image[1] == $b_heigth)
				&& ($image[2] == $b_mime)) {
				$anzeigeauscache = true;
			}
		}
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
	$query = pdoQuery("SELECT `b_bild`, `b_mime` FROM `bild` WHERE `b_user` = :b_user AND `b_name` = :b_name", [':b_user'=>$u_id, ':b_name'=>$feld]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$result = $query->fetch();
		$b_mime = $result['b_mime'];
		$bild = $result['b_bild'];
	}
	
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