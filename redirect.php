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
<html><head>
<title>DEREFER</TITLE><meta charset="utf-8">
<META HTTP-EQUIV="REFRESH" CONTENT="0; URL=<?php echo $url; ?>">
</head>
<body bgcolor="#ffffff" link="#666666" vlink="#666666">
<table width="100%" height="100%" border="0"><tr><td align="center"><a href="<?php echo $url; ?>"><font face="Arial, Helvetica, sans-serif" size="2" color="#666666">Einen Moment bitte, die angeforderte Seite wird geladen...</font></a></td></tr></table>
</body></html>
<?php
}

?>
