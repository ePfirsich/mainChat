<?php
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
		
		if ($c > 23) {
			$c = 0;
		}
	}
}

function statsPrintGraph($title, $text_l, $text_b) {
	global $grapharray, $lang;
	$msg = "";
	
	if ((isset($grapharray)) && (count($grapharray) > 0)) {
		/* Als erstes wird der größte Eintrag im Array und einige Werte ermittelt. */
		
		$b = 0;
		$h = 0;
		$c = 2;
		$d = 0;
		$time = 0;
		$u = 0;
		
		reset($grapharray);
		
		while (list($i, $v) = each($grapharray)) {
			if ($v > $b)
				$b = $v;
			
			if ($v > 0) {
				$time += $v;
				$u += 1;
			}
			
			$c++;
		}
		
		if ($b > 0) {
			$h = (300 / $b);
		} else {
			$h = 0;
		}
		
		if ($u > 0) {
			$d = intval($time / $u);
		} else {
			$d = 0;
		}
		
		/* Linke Beschriftung und die Tabelle mit den Balken anzeigen lassen. */
		
		reset($grapharray);
		
		$msg .= "<div style=\"text-align:center;\">\n";
		$msg .= "<table style=\"margin:auto;\">\n";
		
		$msg .= "<tr>\n";
		$msg .= "<td class=\"tabelle_kopfzeile\" colspan=\"$c\" style=\"text-align:center;\"><span style=\"font-weight:bold;\">$title</span></td>\n";
		$msg .= "</tr>\n";
		
		$msg .= "<tr>\n";
		$msg .= "<td class=\"tabelle_statistics_navigation smaller\" style=\"width:20px;\"><span style=\"font-weight:bold;\">";
		
		$t0 = 0;
		$t1 = strlen($text_l);
		
		while ($t0 < $t1) {
			$msg .= substr($text_l, $t0, 1) . "<br>";
			
			$t0++;
		}
		
		$msg .= "</span></td>\n";
		
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
			$msg .= "<td class=\"tabelle_statistics_navigation smaller\">";
			$msg .= $i;
			$msg .= "</td>\n";
		}
		
		$msg .= "<td class=\"tabelle_statistics_navigation smaller\"><span style=\"font-weight:bold;\">$text_b</span></td>\n";
		$msg .= "</tr>\n";
		$msg .= "</table>\n";
		
		/* Durchschnittliche Anzahl der Benutzer und höchste Anzahl der	*/
		/* Benutzer ausgeben. */
		
		$msg .= "<p style=\"text-align:center;\">";
		$msg .= $lang["statistik_durchschnitt"] . " $d - " . $lang["statistik_hoechste_anzahl"] . " $b";
		$msg .= "</p>\n";
		$msg .= "</div>\n";
	}
	
	unset($grapharray);
	return $msg;
}

?>