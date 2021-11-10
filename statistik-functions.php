<?php
$coreroot = ".";

// Wandelt eine Background-Variable in den entsprechenden HTML-Source (bgcolor, background) um.
function coreMakeBackground($back) {
	$backc = trim(strtok("$back", ":"));
	$backp = trim(strtok(":"));
	
	if (("$backc" == "") || ("$backp" == "")) {
		if ("$backc" != "")
			return (" bgcolor=\"$backc\"");
		if ("$backp" != "")
			return (" background=\"$backp\"");
	} else return (" bgcolor=\"$backc\" background=\"$backp\"");
}

function statsResetMonth($year, $month) {
	global $grapharray;
	
	unset($grapharray);
	
	$i = 1;
	
	while ($i <= 31) {
		
		if (checkdate($month, $i, $year)) {
			$x = SPrintF("%02d", $i);
			
			$grapharray["$x"] = 0;
		}
		
		$i++;
	}
}

function statsResetHours($zeit, $hours) {
	global $grapharray;
	
	unset($grapharray);
	
	$i = 0;
	$c = intval(trim(date("H", $zeit)));
	
	while ($i < $hours) {
		$x = SPrintF("%02d", $c);
		
		$grapharray["$x"] = 0;
		
		$i++;
		$c++;
		
		if ($c > 23)
			$c = 0;
	}
}

function statsPrintGraph($title, $text_l, $text_b) {
	global $grapharray;
	global $STAT_TXT;
	global $STAT_BAR_HEIGHT;
	global $STAT_BAR_FONTBEG1;
	global $STAT_BAR_FONTEND1;
	global $STAT_BAR_FONTBEG2;
	global $STAT_BAR_FONTEND2;
	global $STAT_BAR_FONTBEG3;
	global $STAT_BAR_FONTEND3;
	$msg = "";
	
	if ((isset($grapharray)) && (count($grapharray) > 0)) {
		/* Als erstes wird der größte Eintrag im Array und einige Werte ermittelt. */
		
		$b = 0;
		$h = 0;
		$c = 2;
		$d = 0;
		$t = 0;
		$u = 0;
		
		if ($title == "") {
			$title = $STAT_TXT["0080"];
		}
		
		reset($grapharray);
		
		while (list($i, $v) = each($grapharray)) {
			if ($v > $b)
				$b = $v;
			
			if ($v > 0) {
				$t += $v;
				$u += 1;
			}
			
			$c++;
		}
		
		if ($b > 0)
			$h = ($STAT_BAR_HEIGHT / $b);
		else $h = 0;
		
		if ($u > 0)
			$d = intval($t / $u);
		else $d = 0;
		
		/* Linke Beschriftung und die Tabelle mit den Balken anzeigen lassen. */
		
		reset($grapharray);
		
		$msg .= "<div style=\"text-align:center;\">\n";
		$msg .= "<table style=\"margin:auto;\">\n";
		
		if ($title != "") {
			$msg .= "<tr>\n";
			$msg .= "<td class=\"tabelle_kopfzeile\" colspan=\"$c\" style=\"text-align:center;\">";
			$msg .= $STAT_BAR_FONTBEG3;
			$msg .= $title;
			$msg .= $STAT_BAR_FONTEND3;
			$msg .= "</td>\n";
			$msg .= "</tr>\n";
		}
		
		$msg .= "<tr>\n";
		$msg .= "<td class=\"tabelle_statistics_navigation\" style=\"width:20px;\">";
		$msg .= $STAT_BAR_FONTBEG2;
		
		$t0 = 0;
		$t1 = strlen($text_l);
		
		while ($t0 < $t1) {
			$msg .= substr($text_l, $t0, 1) . "<br>";
			
			$t0++;
		}
		
		$msg .= $STAT_BAR_FONTEND2;
		$msg .= "</td>\n";
		
		while (list($i, $v) = each($grapharray)) {
			if ($v > 0) {
				$s = intval($v * $h);
				
				$msg .= "<td class=\"tabelle_statistics_content\">";
				$msg .= $v;
				$msg .= "<br>";
				
				$msg .= "<div class=\"statistiken_balken\" style=\"height:".$s."px;\"></div><br>";
				
				$msg .= "</td>\n";
			} else {
				$msg .= "<td class=\"tabelle_statistics_content\">";
				$msg .= $v;
				$msg .= "</td>\n";
			}
		}
		
		$msg .= "<td class=\"tabelle_statistics_navigation\">&nbsp;</td>\n";
		$msg .= "</tr>\n";
		$msg .= "<tr>\n";
		$msg .= "<td class=\"tabelle_statistics_navigation\">&nbsp;</td>\n";
		
		/* Unter Leiste mit den Beschriftungen der einzelnen Balken ausgeben. */
		
		reset($grapharray);
		
		while (list($i, $v) = each($grapharray)) {
			$msg .= "<td class=\"tabelle_statistics_navigation\">";
			$msg .= $STAT_BAR_FONTBEG1;
			$msg .= $i;
			$msg .= $STAT_BAR_FONREND1;
			$msg .= "</td>\n";
		}
		
		$msg .= "<td class=\"tabelle_statistics_navigation\">";
		$msg .= $STAT_BAR_FONTBEG2;
		$msg .= $text_b;
		$msg .= $STAT_BAR_FONREND2;
		$msg .= "</td>\n";
		$msg .= "</tr>\n";
		$msg .= "</table>\n";
		
		/* Durchschnittliche Anzahl der Benutzer und höchste Anzahl der	*/
		/* Benutzer ausgeben. */
		
		$msg .= "<p style=\"text-align:center;\">";
		$msg .= $STAT_TXT["0100"] . " $d - " . $STAT_TXT["0101"] . " $b";
		$msg .= "</p>\n";
		$msg .= "</div>\n";
	}
	
	unset($grapharray);
	return $msg;
}

?>