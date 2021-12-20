<?php

require_once("functions/functions-registerglobals.php");

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
	require_once("functions/functions-html.php");
	
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
	
	
	// Allgemeine Übersetzungen
	require_once("languages/$sprache.php");
	
	$chat_grafik['home']="<span class=\"fa fa-home icon16\" alt=\"$t[benutzer_benutzerseite]\" title=\"$t[benutzer_benutzerseite]\"></span>";
	$chat_grafik['mail']="<span class=\"fa fa-envelope icon16\" alt=\"$t[benutzer_nachricht]\" title=\"$t[benutzer_nachricht]\"></span>";
	$chat_grafik['geschlecht_weiblich'] = "&nbsp;<span class=\"fa fa-venus icon16\" alt=\"$t[benutzer_weiblich]\"  title=\"$t[benutzer_weiblich]\"></span>";
	$chat_grafik['geschlecht_maennlich'] = "&nbsp;<span class=\"fa fa-mars icon16\" alt=\"$t[benutzer_maennlich]\" title=\"$t[benutzer_maennlich]\"></span>";
	$chat_grafik['forum_ordnerneu']="<span class=\"fa fa-folder-o icon24\" alt=\"$t[forum_keine_neuen_beitraege]\" title=\"$t[forum_keine_neuen_beitraege]\"></span>";
	$chat_grafik['forum_ordnerblau']="<span class=\"fa fa-folder icon24\" alt=\"$t[forum_neue_beitraege]\" title=\"$t[forum_neue_beitraege]\"></span>";
	$chat_grafik['forum_ordnervoll']="<span class=\"fa fa-folder-open icon24\" alt=\"$t[forum_mehr_als_10_neue_beitraege]\" title=\"$t[forum_mehr_als_10_neue_beitraege]\"></span>";
	$chat_grafik['forum_threadgeschlossen']="<span class=\"fa fa-lock icon24\" alt=\"$t[forum_thema_geschlossen]\" title=\"$t[forum_thema_geschlossen]\"></span>";
	$chat_grafik['forum_topthema']="<span class=\"fa fa-thumb-tack icon24\" alt=\"$t[forum_angepinntes_thema]\" title=\"$t[forum_angepinntes_thema]\"></span>";
	
	// Globales
	$upgrade_password = 0;
	
	ini_set("magic_quotes_runtime", 0);
	
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