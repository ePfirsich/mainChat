<?php
// Version / Copyright - nicht entfernen!
$mainchat_version = "mainChat 8.1.1 © by <a href=\"https://www.fidion.de\" target=\"_blank\">fidion GmbH</a> 1999-2018 - Ab Version 7.0 durch <a href=\"https://www.anime-community.de\" target=\"_blank\">Andreas Völkl</a> auf <a href=\"https://github.com/ePfirsich/mainChat\" target=\"_blank\">GitHub</a>";

// Caching unterdrücken
Header("Last-Modified: " . gmDate("D, d M Y H:i:s", Time()) . " GMT");
Header("Expires: " . gmDate("D, d M Y H:i:s", Time() - 3601) . " GMT");
Header("Pragma: no-cache");
Header("Cache-Control: no-cache");

session_start();
if(isset($_SESSION["id"])) {
	$id = $_SESSION["id"];
}

// Konfigurationsdatei einbinden
$autoknebel = [];
$filenameConfig = 'conf/config.php';
if ( !file_exists($filenameConfig) ) {
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
	exit;
} else {
	require $filenameConfig;
	
	// Allgemeine Übersetzungen
	require_once("./languages/$sprache.php");
	
	// Globales
	ini_set("magic_quotes_runtime", 0);
	
	date_default_timezone_set('Europe/Berlin');
	
	// OPEN A CONNECTION TO THE DATA BASE SERVER AND SELECT THE DB
	$dsn = "mysql:host=$mysqlhost;dbname=$dbase";
	
	$options  = array(
		PDO::MYSQL_ATTR_FOUND_ROWS		=> true,
		PDO::ATTR_EMULATE_PREPARES		=> false, // turn off emulation mode for "real" prepared statements
		//PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
		PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC, //make the default fetch be an associative array
	);
	
	$pdo = new PDO($dsn, $mysqluser, $mysqlpass, $options);
	
	if (!$pdo) {
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
					<td><?php echo $lang['functions_fehler_keine_datenbankverbindung']; ?></td>
				</tr>
			</table>
		</body>
		</html>
		<?php
	} else {
		// Aufgerufene URL prüfen und gegebenenfalls zur URL weiterleiten die in der config hinterlegt ist
		$http_host = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING);
		if( $http_host != "" ) {
			$matches = array();
			if (substr_count($http_host, '.')==1) {
				// domain.tld
				preg_match('/^(?P<d>.+)\.(?P<tld>.+?)$/', $http_host, $matches);
			} else {
				// www.domain.tld, sub1.sub2.domain.tld, ...
				preg_match('/^(?P<sd>.+)\.(?P<d>.+?)\.(?P<tld>.+?)$/', $http_host, $matches);
			}
			
			/*
			$subdomain = (isset($matches['sd'])) ? $matches['sd'] : '';
			$domain = $matches['d'];
			$tld = $matches['tld'];
			$https = filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_STRING);
			
			if($https == "on") {
				$aufgerufeneURL = "https://" . $subdomain.'.'.$domain.'.'.$tld;
			} else {
				$aufgerufeneURL = "http://" . $subdomain.'.'.$domain.'.'.$tld;
			}
			*/
			
			$server=$_SERVER['SERVER_NAME'];
			$datei=$_SERVER['SCRIPT_NAME'];
			$phpfad = substr($datei,0,strrpos($datei,"/")+1); // mit Slash am Ende , ohne +1 = Ohne Slash
			// sucht mit strrpos in der Zeichenkette nach dem letzten slash und
			// schneidet alles dahinter ab mit substr
			$aufgerufeneURL = $_SERVER['REQUEST_SCHEME'] . '://' . $server.$phpfad;
			// Letztes Zeichen entfernen
			$aufgerufeneURL = substr($aufgerufeneURL, 0, -1);
			
			if($aufgerufeneURL != $chat_url) {
				header('Location: '.$chat_url.'', true, 301);
				exit();
			}
		}
		// Ende der aufgerufenen URL
		
		if($forumfeatures == "1" || $punktefeatures == "1") {
			require_once("./functions/functions-community.php");
		}
		require_once("./functions/functions-global.php");
		require_once("./functions/functions-formulare.php");
		require_once("./functions/functions-html.php");
		
		$chat_grafik['home']="<span class=\"fa-solid fa-home icon16\" alt=\"$lang[benutzer_benutzerseite]\" title=\"$lang[benutzer_benutzerseite]\"></span>";
		$chat_grafik['mail']="<span class=\"fa-solid fa-envelope icon16\" alt=\"$lang[benutzer_nachricht]\" title=\"$lang[benutzer_nachricht]\"></span>";
		$chat_grafik['geschlecht_weiblich'] = "&nbsp;<span class=\"fa-solid fa-venus icon16\" alt=\"$lang[benutzer_weiblich]\"  title=\"$lang[benutzer_weiblich]\"></span>";
		$chat_grafik['geschlecht_maennlich'] = "&nbsp;<span class=\"fa-solid fa-mars icon16\" alt=\"$lang[benutzer_maennlich]\" title=\"$lang[benutzer_maennlich]\"></span>";
		$chat_grafik['forum_ordnerneu']="<span class=\"fa-regular fa-folder icon24\" alt=\"$lang[forum_keine_neuen_beitraege]\" title=\"$lang[forum_keine_neuen_beitraege]\"></span>";
		$chat_grafik['forum_ordnerblau']="<span class=\"fa-solid fa-folder icon24\" alt=\"$lang[forum_neue_beitraege]\" title=\"$lang[forum_neue_beitraege]\"></span>";
		$chat_grafik['forum_ordnervoll']="<span class=\"fa-solid fa-folder-open icon24\" alt=\"$lang[forum_mehr_als_10_neue_beitraege]\" title=\"$lang[forum_mehr_als_10_neue_beitraege]\"></span>";
		$chat_grafik['forum_threadgeschlossen']="<span class=\"fa-solid fa-lock icon24\" alt=\"$lang[forum_thema_geschlossen]\" title=\"$lang[forum_thema_geschlossen]\"></span>";
		$chat_grafik['forum_topthema']="<span class=\"fa-solid fa-thumb-tack icon24\" alt=\"$lang[forum_angepinntes_thema]\" title=\"$lang[forum_angepinntes_thema]\"></span>";
	}
}
?>