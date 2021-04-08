<?php

// löst redirekt auf $url aus
if (isset($_POST["url"]))
    $url = $_POST["url"];
else if (isset($_GET["url"]))
    $url = $_GET["url"];

$url = urldecode($url);
$url = str_replace("<b>", "", $url);
$url = str_replace("</b>", "", $url);
$url = str_replace("<i>", "", $url);
$url = str_replace("</i>", "", $url);
$url = str_replace("<br>", "", $url);
$url = str_replace("<br/", "", $url);

$url = str_replace('&amp;', '&', $url);
$url = str_replace('&#039;', '\'', $url);
$url = str_replace('&quot;', '\"', $url);
$url = str_replace('&lt;', '<', $url);
$url = str_replace('&gt;', '>', $url);
$url = str_replace('\\', '', $url);

if ($url) {
    if (substr($url, 0, 7) != "http://" && substr($url, 0, 8) != "https://")
        $url = "http://" . $url;
?>
<!DOCTYPE html>
<html>
<head>
<title>DEREFER</title>
<meta charset="utf-8">
<meta http-equiv="refresh" content="0; URL=<?php echo $url; ?>">
<link rel="stylesheet" href="css/style.css" type="text/css">
<style type="text/css">
body {
	background-color:#ffffff;
}
a, a:link {
	color:#666666;
}
a:visited, a:active {
	color:#666666;
}
</style>
</head>
<body>
	<table style="width:100%; height:100%;">
		<tr>
			<td align="center"><a href="<?php echo $url; ?>">Einen Moment bitte, die angeforderte Seite wird geladen...</a></td>
		</tr>
	</table>
</body>
</html>
<?php
}
?>