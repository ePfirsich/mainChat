<?php

// Liefert ein Bild inkl. Header zur direkten Darstellung im Browser
// Übergabeparameter:	http_host -> Virtueller Host des Chats
//			u_id -> User, zu dem das Bild gehört
//			feld -> Feldname in der DB

require_once("functions-registerglobals.php");

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("Cache-Control: post-check=0, pre-check=0", FALSE);

$cache = "home_bild";
$feld = mysql_real_escape_string($feld);
$http_host = mysql_real_escape_string($http_host);
$u_id = intval($u_id);

if ($feld != "ui_bild1" && $feld != "ui_bild2" && $feld != "ui_bild3"
    && $feld != "ui_bild4" && $feld != "ui_bild5" && $feld != "ui_bild6") {
    echo "Fehlerhaftes 'feld' in home_bild.php'<BR>";
    exit;
}

if (!is_numeric($u_id)) {
    echo "Fehlerhafte 'u_id' in 'home_bild.php'<BR>";
    exit;
}

require("functions-init.php");

$http_host = str_replace("\\", "", $http_host);
$http_host = str_replace("/", "", $http_host);
$config = "config.php-" . $http_host;

if ($http_host != "" && (!file_exists("conf/$config"))) {
    echo "Fehlerhafter 'http_host' in 'home_bild.php'<BR>";
    exit;
}

$bild = "";
$b_mime = "";

$url = parse_url($HTTP_REFERER);

if (isset($check_np_referer) && $check_np_referer == "1")
    if ($url['host'] != $http_host) {
        print "referer error! ";
        exit;
    }

// Überprüfen, ob ein gecached Bild vorhanden ist
$cachepfad = $cache . "/" . $http_host . "/" . substr($u_id, 0, 2) . "/"
    . $u_id . "/" . $feld;

$conn = 0;
// DB-Connect, ggf. 50 mal versuchen (insgesamt 10 sek)
for ($c = 0; $c++ < 50 AND !$conn;) {
    if ($conn = @mysql_connect($mysqlhost, $mysqluser, $mysqlpass)) {
        @mysql_select_db($dbase, $conn);
        mysql_set_charset("utf8mb4");
    }
    usleep(200000);
}
if (!$conn) {
    echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es später nocheinmal!<BR>";
    exit;
}

// Prüfe ob User existiert und NP aktiviert ist
$query = "SELECT u_chathomepage FROM user WHERE u_id=$u_id ";
$result = mysql_query($query, $conn);
if ($result && mysqli_num_rows($result) == 1) {
    if (mysql_result($result, 0, "u_chathomepage") != 'J') {
        echo "Nickpage dieses Users deaktiviert!<BR>";
        exit;
    }
} else {
    echo "User ID nicht gefunden!<BR>";
    exit;
}
@mysqli_free_result($result);

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
        $result = mysql_query($query, $conn);
        if ($result && mysqli_num_rows($result) == 1) {
            $b_width = mysql_result($result, 0, "b_width");
            $b_heigth = mysql_result($result, 0, "b_height");
            $b_mime = mysql_result($result, 0, "b_mime");
            
            // Wenn DB Werte = Cache Werte dann Cache laden
            if (($image[0] == $b_width) && ($image[1] == $b_heigth)
                && ($image[2] == $b_mime)) {
                $anzeigeauscache = true;
            }
        }
        @mysqli_free_result($result);
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
    $result = mysql_query($query, $conn);
    if ($result && mysqli_num_rows($result) == 1) {
        $b_mime = mysql_result($result, 0, "b_mime");
        $bild = mysql_result($result, 0, "b_bild");
    }
    @mysqli_free_result($result);
    
    // Bild in den Cache schreiben
    if (!@stat($cache))
        mkdir($cache, 0777);
    if (!@stat($cache . "/" . $http_host))
        mkdir($cache . "/" . $http_host, 0777);
    if (!@stat($cache . "/" . $http_host . "/" . substr($u_id, 0, 2)))
        mkdir($cache . "/" . $http_host . "/" . substr($u_id, 0, 2), 0777);
    if (!@stat(
        $cache . "/" . $http_host . "/" . substr($u_id, 0, 2) . "/" . $u_id))
        mkdir(
            $cache . "/" . $http_host . "/" . substr($u_id, 0, 2) . "/" . $u_id,
            0777);
    
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