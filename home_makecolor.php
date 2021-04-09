<?php
$fg = filter_input(INPUT_GET, 'fg', FILTER_SANITIZE_URL);
$bg = filter_input(INPUT_GET, 'bg', FILTER_SANITIZE_URL);
$text = filter_input(INPUT_GET, 'text', FILTER_SANITIZE_URL);
$font = filter_input(INPUT_GET, 'font', FILTER_SANITIZE_URL);
$x = filter_input(INPUT_GET, 'x', FILTER_SANITIZE_URL);
$y = filter_input(INPUT_GET, 'y', FILTER_SANITIZE_URL);
$xp = filter_input(INPUT_GET, 'xp', FILTER_SANITIZE_URL);
$yp = filter_input(INPUT_GET, 'yp', FILTER_SANITIZE_URL);

if (isset($QUERY_STRING) && $QUERY_STRING == "") {
	$QUERY_STRING = "help";
}
if (!isset($QUERY_STRING)) {
	$QUERY_STRING = "";
}
if ($QUERY_STRING == "help") {
	$bg = "0000ff";
	$fg = "ffffff";
	$text = "(c) fidion GmbH";
	$x = 180;
	$y = 160;
	$yp = "t";
	$xp = "c";
	$font = 3;
}

if (!isset($fg)) {
	$fg = "";
}
if (!isset($bg)) {
	$bg = "";
}
if (!isset($text)) {
	$text = "";
}
if (!isset($font)) {
	$font = "";
}
if (!isset($x)) {
	$x = "";
}
if (!isset($y)) {
	$y = "";
}
if (!isset($xp)) {
	$xp = "";
}
if (!isset($yp)) {
	$yp = "";
}

if (substr($fg, 0, 1) == "#") {
	$fg = substr($fg, 1);
}
if (substr($bg, 0, 1) == "#") {
	$bg = substr($bg, 1);
}

if ($fg == "")
	$fg = "000000";
if (strlen($fg) != 6)
	$fg = "000000";
$rfg = hexdec(substr($fg, 0, 2));
$gfg = hexdec(substr($fg, 2, 2));
$bfg = hexdec(substr($fg, 4, 2));

if ($bg == "")
	$bg = "ffffff";
if (strlen($bg) != 6)
	$bg = "ffffff";
$rbg = hexdec(substr($bg, 0, 2));
$gbg = hexdec(substr($bg, 2, 2));
$bbg = hexdec(substr($bg, 4, 2));

if ($text == "") {
	$text = "mainChat";
}
if ($font == "" || $font < 0 || $font > 5) {
	$font = 3;
}
$height = ImageFontHeight($font);
$width = ImageFontWidth($font);

if ($x == "") {
	$x = 90;
}
if ($y == "") {
	$y = 20;
}

// Create Image
$img = ImageCreate($x, $y);

// Create Colors
$bgcol = ImageColorAllocate($img, $rbg, $gbg, $bbg);
$fgcol = ImageColorAllocate($img, $rfg, $gfg, $bfg);

// Fill Background
ImageFilledRectangle($img, 0, 0, $x - 1, $y - 1, $bgcol);
// Calculate Text Position
$xlen = strlen($text) * $width;
if ($xlen >= $x)
	$xlen = $x;
$ylen = $height;
if ($ylen >= $y)
	$ylen = $y;
if ($xp == "")
	$xp = "c";
if ($yp == "")
	$yp = "c";
switch ($xp) {
	case "r":
		$tx = $x - $xlen;
		break;
	case "l":
		$tx = 0;
		break;
	case "c":
		$tx = floor(($x - $xlen) / 2);
		break;
	default:
		$tx = $xp;
}
switch ($yp) {
	case "b":
		$ty = $y - $ylen;
		break;
	case "t":
		$ty = 0;
		break;
	case "c":
		$ty = floor(($y - $ylen) / 2);
		break;
	default:
		$ty = $yp;
}
// Set Text
ImageString($img, $font, $tx, $ty, $text, $fgcol);
if ($QUERY_STRING == "help") {
	$tx = 5;
	$ty += $height + 10;
	ImageString($img, $font, $tx, $ty, "Parameter: ", $fgcol);
	$ty += $height + 1;
	ImageString($img, $font, $tx, $ty, "x=xcoord", $fgcol);
	$ty += $height + 1;
	ImageString($img, $font, $tx, $ty, "y=ycoord", $fgcol);
	$ty += $height + 1;
	ImageString($img, $font, $tx, $ty, "xp=l|c|r", $fgcol);
	$ty += $height + 1;
	ImageString($img, $font, $tx, $ty, "yp=t|c|b", $fgcol);
	$ty += $height + 1;
	ImageString($img, $font, $tx, $ty, "font=1|2|3|4|5", $fgcol);
	$ty += $height + 1;
	ImageString($img, $font, $tx, $ty, "bg=rrggbb", $fgcol);
	$ty += $height + 1;
	ImageString($img, $font, $tx, $ty, "fg=rrggbb", $fgcol);
	$ty += $height + 1;
	ImageString($img, $font, $tx, $ty, "text=[text]", $fgcol);
}

$n_name = md5(uniqid(mt_rand()));
Header("Last-Modified: " . gmDate("D, d M Y H:i:s", Time()) . " GMT");
Header("Expires: " . gmDate("D, d M Y H:i:s", Time() - 3601) . " GMT");
Header("Pragma: no-cache");
Header("Cache-Control: no-cache");
Header("Content-Disposition: inline;filename=\"$n_name.gif\"");
Header("Content-Location: $n_name.gif");

Header("Content-type: image/gif");

ImageGif($img);
ImageDestroy($img);
?>
