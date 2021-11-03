<?php

require_once "functions-registerglobals.php";

// Konfigurationsdatei einbinden
$filenameConfig = 'conf/config.php';
if ( !file_exists($filenameConfig) ) {
	?>
	?>
	<!DOCTYPE html>
	<html dir="ltr" lang="de">
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
	require "conf/config.php";
	

	// Aufgerufene URL prüfen und gegebenenfalls zur URL weiterleiten die in der config hinterlegt ist
	if( isset($_SERVER['HTTP_HOST']) ) {
		$matches = array();
		if (substr_count($_SERVER['HTTP_HOST'], '.')==1) {
			// domain.tld
			preg_match('/^(?P<d>.+)\.(?P<tld>.+?)$/', $_SERVER['HTTP_HOST'], $matches);
		} else {
			// www.domain.tld, sub1.sub2.domain.tld, ...
			preg_match('/^(?P<sd>.+)\.(?P<d>.+?)\.(?P<tld>.+?)$/', $_SERVER['HTTP_HOST'], $matches);
		}
		
		$subdomain = (isset($matches['sd'])) ? $matches['sd'] : '';
		$domain = $matches['d'];
		$tld = $matches['tld'];
		$isHttps = (!empty($_SERVER['HTTPS']));
		if($isHttps) {
			$aufgerufeneURL = "https://" . $subdomain.'.'.$domain.'.'.$tld;
		} else {
			$aufgerufeneURL = "http://" . $subdomain.'.'.$domain.'.'.$tld;
		}
		
		if($aufgerufeneURL != $chat_url) {
			header('Location: '.$chat_url.'', true, 301);
			exit();
		}
	}
	// Ende der aufgerufenen URL
	
	
	// Pfad des Chats auf dem WWW-Server
	$phpself = $_SERVER['PHP_SELF'];
	
	$chat_file = basename($phpself);
	$chat_file = str_replace("html", "php", $chat_file);
	
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
		<html dir="ltr" lang="de">
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