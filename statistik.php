<?php

$u_id = "";
if (function_exists("id_lese")) {
	id_lese($id);
}

// nur Admins dürfen Statistik ansehen...
if (!$u_id || !$admin || !$erweitertefeatures) {
	?>
	<!DOCTYPE html>
	<html dir="ltr" lang="de">
		<body>
			<b>Fehler:</b> Diese Seite darf nur aus dem Benutzer-Menü als Admin aufgerufen werden!
		</body>
	</html>
	<?php
	die();
}

include("statistik-functions.php");
include("conf/" . $sprachconfig . "-statistik.php");

// config-bereich

$STAT_BAR_HEIGHT = 300; // Höhe einer Statistiktabelle in Pixel. 

// Images aus denen ein Balken für die Statistik zusammengesetzt werden soll
$STAT_BAR_IMAGE_T = "14,2,pics/bar-t.gif";
$STAT_BAR_IMAGE_M = "14,2,pics/bar-m.gif";
$STAT_BAR_IMAGE_B = "14,2,pics/bar-b.gif";

// Fontstart und Fontende die zum Anzeigen der unteren Beschriftung verwendet werden sollen.
$STAT_BAR_FONTBEG1 = "<span style=\"font-size: smaller;\">";
$STAT_BAR_FONTEND1 = "</span>";

// Fontstart und Fontende die zum Anzeigen der seitlichen Beschriftung wendet werden sollen.
$STAT_BAR_FONTBEG2 = "<span style=\"font-size: smaller; font-weight:bold;\">";
$STAT_BAR_FONTEND2 = "</span>";

// Fontstart und Fontende die zum Anzeigen der Überschrift einer Statistik verwendet werden sollen.
$STAT_BAR_FONTBEG3 = "<span style=\"font-weight:bold;\">";
$STAT_BAR_FONTEND3 = "</span>";


// Bitte ab hier nichts ändern!
$mysqli_statistik_link = mysqli_connect('p:'.$STAT_DB_HOST, $STAT_DB_USER, $STAT_DB_PASS, $STAT_DB_NAME);
if ($mysqli_statistik_link) {
	mysqli_set_charset($mysqli_statistik_link, "utf8mb4");
	mysqli_select_db($mysqli_statistik_link, $STAT_DB_NAME);
}
$v = mysqli_real_escape_string($mysqli_statistik_link, "mainChat");

// Testen, ob Statistiken funktionieren...
$fehler = false;

if (!$mysqli_statistik_link) {
	// DB-Connect bereits danebengegangen...
	$fehler = true;
	$msg = $t['statistik4'];
} else {
	//testen, ob statisiken überhaupt geschrieben werden:
	$r1 = mysqli_query($mysqli_statistik_link, "SELECT DISTINCT `id` FROM chat");
	if ($r1 > 0) {
		if (mysqli_num_rows($r1) == 0) {
			// Keine Enträge vorhanden
			$msg = $t['statistik5'];
			$fehler = true;
		}
	} else {
		$fehler = true;
		$msg = $t['statistik5'];
	}
}

if ($fehler) {
	show_box_title_content($t['statistik1'], $msg);
	return;
}

// Menu ausgeben
$msg = "[<a href=\"$PHP_SELF?id=$id&aktion=statistik&type=monat\">" . $t['statistik3'] . "</a>]\n"
	. "[<a href=\"$PHP_SELF?id=$id&aktion=statistik&type=stunde\">" . $t['statistik2'] . "</a>]";

	show_box_title_content($t['statistik1'], $msg, true);

switch ($type) {
	case "monat":
	// Auswahlbox Monat
		$y = urldecode($y);
		$m = urldecode($m);
		$v = urldecode($v);
		
		$grapharray = array();
		
		if (strlen($v) < 1)
			$v = "%";
		if (strlen($y) < 1)
			$y = date("Y", time());
		
		if ((intval($m) < 1) || (intval($m) > 12))
			$m = intval(date("m", time()));
		
		$m = SPrintF("%02d", $m);
		$y = SPrintF("%04d", $y);
		
		$msg = "";
		$msg .= "<center>\n";
		$msg .= "<form name=\"form\" action=\"$PHP_SELF\" method=\"post\">\n";
		$msg .= "<input type=\"hidden\" name=\"type\" value=\"$type\">\n";
		$msg .= "<input type=\"hidden\" name=\"aktion\" value=\"$aktion\">\n";
		$msg .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
		$msg .= "<input type=\"hidden\" name=\"page\" value=\"chat-month\">\n";
		$msg .= "<table>\n";
		$msg .= "<tr>\n";
		$msg .= "<td class=\"tabelle_koerper\">"
			. $STAT_TITLE_FONTBEG0 . "\n";
		$msg .= $STAT_TXT["0061"];
		$msg .= $STAT_TITLE_FONTEND0 . "</td>\n";
		$msg .= "<td class=\"tabelle_koerper\">"
			. $STAT_TITLE_FONTBEG0 . "\n";
		$msg .= "<select name=\"m\" onchange='form.submit();'>\n";
		
		while (list($i, $n) = each($STAT_MONTH_TXT)) {
			if ($i == $m) {
				$msg .= "<option value=\"$i\" SELECTED>$n\n";
			} else {
				$msg .= "<option value=\"$i\">$n\n";
			}
		}
		
		$msg .= "</select>\n";
		// Auswahlbox Jahr  
		$msg .= "<select name=\"y\">\n";
		
		$i = 0;
		
		while ($i < 2) {
			$n = (date("Y", time()) - $i);
			
			if ($n == $y)
				$msg .= "<option value=\"$n\" SELECTED>$n\n";
			else $msg .= "<option value=\"$n\">$n\n";
			
			$i++;
		}
		
		$msg .= "</select>\n";
		$msg .= $STAT_TITLE_FONTEND0;
		$msg .= "<input type=\"submit\" value=\"" . $STAT_TXT["0053"] . "\">\n";
		$msg .= "</td>\n";
		$msg .= "</tr>\n";
		$msg .= "</table>\n";
		$msg .= "</form>\n";
		$msg .= "</center>\n";
		
		// Statistiken einzeln nach Monaten
		
		$r1 = @mysqli_query($mysqli_statistik_link, "SELECT DISTINCT c_users FROM chat WHERE date(c_timestamp) LIKE '$y-$m%'");
		
		if ($r1 > 0) {
			statsResetMonth($y, $m);
			
			$r0 = @mysqli_query($mysqli_statistik_link, 
				"SELECT *, DATE_FORMAT(c_timestamp,'%d') as tag FROM chat WHERE date(c_timestamp) LIKE '$y-$m%' ORDER BY c_timestamp");
			if ($r0 > 0) {
				$i = 0;
				$n = @mysqli_num_rows($r0);
				
				while ($i < $n) {
					$x = @mysqli_result($r0, $i, "tag");
					$c_users = @mysqli_result($r0, $i, "c_users");
					
					if ($c_users > $grapharray["$x"])
						$grapharray["$x"] = $c_users;
					
					$i++;
				}
				
				$msg .= statsPrintGraph($chat, $STAT_TXT["0102"], $STAT_TXT["0105"]);
			}
		}
		
		show_box_title_content($t['statistik3'], $msg);
		break;
	
	case "stunde":
		
		$h = 24;
		$msg = "";
		
		$showtime = (time() - (($h - 1) * 60 * 60));
		
		statsResetHours($showtime, $h);
		
		$r0 = @mysqli_query($mysqli_statistik_link, "SELECT *, DATE_FORMAT(c_timestamp,'%k') as stunde FROM chat WHERE UNIX_TIMESTAMP(c_timestamp)>$showtime ORDER BY c_timestamp");
		
		if ($r0 > 0) {
			$i = 0;
			$n = @mysqli_num_rows($r0);
			while ($i < $n) {
				$x = @mysqli_result($r0, $i, "stunde");
				$c_users = @mysqli_result($r0, $i, "c_users");
				$grapharray["$x"] += $c_users;
				$i++;
			}
			
			$title = $v;
			
			$msg .= statsPrintGraph($title, $STAT_TXT["0102"], $STAT_TXT["0103"]);
		}
		
		show_box_title_content($t['statistik2'], $msg);
		break;
} // switch

?>