<?php

require_once "functions-registerglobals.php";

// Konfigurationsdatei einbinden
$filenameConfig = 'conf/config.php';
if ( !file_exists($filenameConfig) ) {
	?>
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>mainChat</title>
		<meta charset="utf-8">
	</head>
	<body>
		<table style="width:100%; border:0px;">
			<tr style="color:#ff0000; font-weigth:bold;">
				<td>FEHLER: Der Chat ist noch nicht konfiguriert!</td>
			</tr>
		</table>
	</body>
	</html>
	<?php
} else {
	require $filenameConfig;
	require_once("functions.php-html.php");
	
	// Liegt lokale Konfigurationsdatei "config.php-http_host" in "conf/" vor? Falls ja einbinden
	if (!(isset($http_host))) {
		$http_host = $_SERVER["HTTP_HOST"];
	}
	$http_host = str_replace(":80", "", strtolower($http_host));
	$http_host = str_replace(":8888", "", $http_host);
	
	$config = "config.php-" . $http_host;
	if (file_exists("conf/$config")) {
		require "conf/$config";
	}
	
	// Pfad des Chats auf dem WWW-Server
	$phpself = $_SERVER['PHP_SELF'];
	
	$chat_file = basename($phpself);
	$chat_file = str_replace("html", "php", $chat_file);
	$chat_url = dirname($phpself);
	if (substr($chat_url, -1) != "/")
		$chat_url .= "/";
	
	// Wenn zwingend SSL, dann ... 
	if (isset($SSLRedirect) && $SSLRedirect == "1") {
		// ... weiterleiten auf HTTPS wenn nicht schon sowieso aufgerufen
		if (!((isset($_SERVER["HTTPS"])
			&& ($_SERVER["HTTPS"] == '1' || strtolower($_SERVER["HTTPS"]) == 'on')))) {
			$url = 'https://' . $_SERVER["HTTP_HOST"] . '/index.php';
			if (!headers_sent())
				header('Location: ' . $url);
			die();
		}
		// ... für alle Ausgaben einmalig Protokollvariable
	 else {
			$serverprotokoll = 'https';
		}
	}
	// Wenn SSL aber nicht zwingend, für alle Ausgaben einmalig Protokollvariable
	 else if (((isset($_SERVER["HTTPS"])
		&& ($_SERVER["HTTPS"] == '1' || strtolower($_SERVER["HTTPS"]) == 'on')))
		|| $ssl_login) {
		$serverprotokoll = 'https';
	}
	// default Protokoll
	 else {
		$serverprotokoll = 'http';
	}
	
	// Zentrale Sprachdatei einbinden
	require "conf/$sprachconfig";
	
	// Sprachdatei für functions.php einbinden
	require "conf/" . $sprachconfig . "-functions.php";
	
	// Liegen lokale Functionen "functions.php-$chat_file" vor? Falls ja einbinden
	$functions = "functions.php-" . $chat_file;
	if (file_exists("$functions")) {
		require "$functions";
	}
	
	// Liegt lokale Sprachdatei "$sprachconfig-$chat_file" vor? Falls ja einbinden
	$config = $sprachconfig . "-" . $chat_file;
	if (file_exists("conf/$config")) {
		require "conf/$config";
	}
	
	// Falls Texte in Kopfzeilen weiß dargestellt werden sollen, Variable definieren
	if ($farbe_text_weiss == 1) {
		$ft0 = "<span style=\"color:#FFFFFF;\">";
		$ft1 = "</span>";
	} else {
		$ft0 = "";
		$ft1 = "";
	}
	
	// Globales
	$crypted_password_extern = 0;
	$upgrade_password = 0;
	
	ini_set("magic_quotes_runtime", 0);
	
	//apache_setenv('no-gzip', 1);
	//ini_set('zlib.output_compression', 0);
	//ini_set('implicit_flush', 1);
	
	if (substr(phpversion(), 0, strpos(phpversion(), '.')) >= 5) {
		date_default_timezone_set('Europe/Berlin');
	}
	
	if (ini_get('output_buffering') >= 1) {
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>mainChat</title>
			<meta charset="utf-8">
		</head>
		<body>
			<table style="width:100%; border:0px;">
				<tr style="color:#ff0000; font-weigth:bold;">
					<td>FEHLER: Der Chat funktioniert nur, wenn PHP: "output_buffering" auf 0 steht!</td>
				</tr>
			</table>
		</body>
		</html>
		<?php
		die();
	}
	
	$usleep = 50000;
}
?>