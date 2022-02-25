<?php
// Version / Copyright - nicht entfernen!
$mainchat_version = "mainChat 7.1.3 © by <a href=\"https://www.fidion.de\" target=\"_blank\">fidion GmbH</a> 1999-2018 - Ab Version 7.0 durch <a href=\"https://www.anime-community.de\" target=\"_blank\">Andreas Völkl</a> auf <a href=\"https://github.com/ePfirsich/mainChat\" target=\"_blank\">GitHub</a>";

// Caching unterdrücken
Header("Last-Modified: " . gmDate("D, d M Y H:i:s", Time()) . " GMT");
Header("Expires: " . gmDate("D, d M Y H:i:s", Time() - 3601) . " GMT");
Header("Pragma: no-cache");
Header("Cache-Control: no-cache");

session_start();
$id = $_SESSION["id"];

// Konfigurationsdatei einbinden
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
} else if (ini_get('output_buffering') >= 1) {
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
				<td>FEHLER: Der Chat funktioniert nur, wenn PHP: \"output_buffering\" auf 0 steht!</td>
			</tr>
		</table>
	</body>
	</html>
	<?php
} else {
	require $filenameConfig;
	
	// Allgemeine Übersetzungen
	require_once("languages/$sprache.php");
	
	// Globales
	ini_set("magic_quotes_runtime", 0);
	
	date_default_timezone_set('Europe/Berlin');
	
	// DB-Connect, ggf. 3 mal versuchen
	for ($c = 0; $c++ < 3 && !isset($mysqli_link); ) {
		$mysqli_link = mysqli_connect('p:'.$mysqlhost, $mysqluser, $mysqlpass, $dbase);
		if ($mysqli_link) {
			mysqli_set_charset($mysqli_link, "utf8mb4");
			mysqli_select_db($mysqli_link, $dbase);
		}
	}
	
	if( $mysqli_link == null || $mysqli_link == "" ) {
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
					<td><?php echo $t['functions_fehler_keine_datenbankverbindung']; ?></td>
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
			
			$subdomain = (isset($matches['sd'])) ? $matches['sd'] : '';
			$domain = $matches['d'];
			$tld = $matches['tld'];
			$https = filter_input(INPUT_SERVER, 'HTTPS', FILTER_SANITIZE_STRING);
			
			if($https == "on") {
				$aufgerufeneURL = "https://" . $subdomain.'.'.$domain.'.'.$tld;
			} else {
				$aufgerufeneURL = "http://" . $subdomain.'.'.$domain.'.'.$tld;
			}
			
			if($aufgerufeneURL != $chat_url) {
				header('Location: '.$chat_url.'', true, 301);
				exit();
			}
		}
		// Ende der aufgerufenen URLH
		
		if($forumfeatures == "1" || $punktefeatures == "1") {
			require_once("functions-community.php");
		}
		require_once("functions-global.php");
		require_once("functions/functions-formulare.php");
		require_once("functions/functions-html.php");
		
		$chat_grafik['home']="<span class=\"fa-solid fa-home icon16\" alt=\"$t[benutzer_benutzerseite]\" title=\"$t[benutzer_benutzerseite]\"></span>";
		$chat_grafik['mail']="<span class=\"fa-solid fa-envelope icon16\" alt=\"$t[benutzer_nachricht]\" title=\"$t[benutzer_nachricht]\"></span>";
		$chat_grafik['geschlecht_weiblich'] = "&nbsp;<span class=\"fa-solid fa-venus icon16\" alt=\"$t[benutzer_weiblich]\"  title=\"$t[benutzer_weiblich]\"></span>";
		$chat_grafik['geschlecht_maennlich'] = "&nbsp;<span class=\"fa-solid fa-mars icon16\" alt=\"$t[benutzer_maennlich]\" title=\"$t[benutzer_maennlich]\"></span>";
		$chat_grafik['forum_ordnerneu']="<span class=\"fa-solid fa-folder-o icon24\" alt=\"$t[forum_keine_neuen_beitraege]\" title=\"$t[forum_keine_neuen_beitraege]\"></span>";
		$chat_grafik['forum_ordnerblau']="<span class=\"fa-solid fa-folder icon24\" alt=\"$t[forum_neue_beitraege]\" title=\"$t[forum_neue_beitraege]\"></span>";
		$chat_grafik['forum_ordnervoll']="<span class=\"fa-solid fa-folder-open icon24\" alt=\"$t[forum_mehr_als_10_neue_beitraege]\" title=\"$t[forum_mehr_als_10_neue_beitraege]\"></span>";
		$chat_grafik['forum_threadgeschlossen']="<span class=\"fa-solid fa-lock icon24\" alt=\"$t[forum_thema_geschlossen]\" title=\"$t[forum_thema_geschlossen]\"></span>";
		$chat_grafik['forum_topthema']="<span class=\"fa-solid fa-thumb-tack icon24\" alt=\"$t[forum_angepinntes_thema]\" title=\"$t[forum_angepinntes_thema]\"></span>";
	}
}
?>