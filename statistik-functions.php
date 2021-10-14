<?php
$coreroot = ".";

// Wandelt eine Background-Variable in den entsprechenden HTML-Source (bgcolor, background) um.
function coreMakeBackground($back) {
	$backc = trim(strtok("$back", ":"));
	$backp = trim(strtok(":"));
	
	if (("$backc" == "") || ("$backp" == "")) {
		if ("$backc" != "")
			return (" BGCOLOR=\"$backc\"");
		if ("$backp" != "")
			return (" BACKGROUND=\"$backp\"");
	} else return (" BGCOLOR=\"$backc\" BACKGROUND=\"$backp\"");
}

// Wandelt eine Image-Variable in den entsprechenden HTML-Source (Src, Width, Height) um.
function coreMakeImage($image) {
	$img_w = strtok("$image", ",");
	$img_h = strtok(",");
	$img_p = strtok(",");
	
	return ("src=\"$img_p\" WIDTH=\"$img_w\" HEIGHT=\"$img_h\"");
}

function statsOverview($v = "%") {
	global $STAT_DB_HOST;
	global $STAT_DB_USER;
	global $STAT_DB_PASS;
	global $STAT_DB_NAME;
	global $STAT_TXT;
	global $STAT_TITLE_FONTBEG0;
	global $STAT_TITLE_FONTEND0;
	global $STAT_TITLE_FONTBEG2;
	global $STAT_TITLE_FONTEND2;
	global $STAT_TITLE_BACK1;
	global $grapharray;
	
	mysqli_connect('p:'.$STAT_DB_HOST, $STAT_DB_USER, $STAT_DB_PASS, $STAT_DB_NAME);
	mysqli_set_charset($mysqli_statistik_link, "utf8mb4");
	
	$m = date("m", time());
	$y = date("Y", time());
	
	$r1 = @mysqli_query($mysqli_statistik_link, "SELECT DISTINCT c_host FROM chat WHERE c_timestamp LIKE '$y$m%' '" . mysqli_real_escape_string($mysqli_statistik_link, $v) . "'");
	
	if ($r1 > 0) {
		$j = 0;
		$o = @mysqli_num_rows($r1);
		
		if ($o > 0) {
			echo ("<table style=\"width:100%;\">\n");
			echo ("<tr>\n");
			echo ("<td" . coreMakeBackground($STAT_TITLE_BACK1) . ">"
				. $STAT_TITLE_FONTBEG2 . $STAT_TXT["0054"]
				. $STAT_TITLE_FONTEND2 . "</td>\n");
			echo ("<td" . coreMakeBackground($STAT_TITLE_BACK1)
				. " style=\"text-align:right;\">" . $STAT_TITLE_FONTBEG2
				. $STAT_TXT["0055"] . $STAT_TITLE_FONTEND2 . "</td>\n");
			echo ("<td" . coreMakeBackground($STAT_TITLE_BACK1)
				. " style=\"text-align:right;\">" . $STAT_TITLE_FONTBEG2
				. $STAT_TXT["0056"] . $STAT_TITLE_FONTEND2 . "</td>\n");
			echo ("</tr>\n");
			
			while ($j < $o) {
				$c_host = @mysqli_result($r1, $j, "c_host");
				
				statsResetMonth($y, $m);
				
				$r0 = @mysqli_query($mysqli_statistik_link, 
					"SELECT * FROM chat WHERE c_timestamp LIKE '$y$m%' AND c_host='". mysqli_real_escape_string($mysqli_statistik_link, $c_host) . "' ORDER BY c_timestamp");
				
				if ($r0 > 0) {
					$i = 0;
					$u = 0;
					$n = @mysqli_num_rows($r0);
					
					while ($i < $n) {
						$c_timestamp = @mysqli_result($r0, $i, "c_timestamp");
						$c_users = @mysqli_result($r0, $i, "c_users");
						
						$x = trim(substr($c_timestamp, 6, 2));
						
						if ($c_users > $grapharray["$x"])
							$grapharray["$x"] = $c_users;
						
						$i++;
					}
					
					reset($grapharray);
					
					while (list($i, $n) = each($grapharray)) {
						if ($n > $u)
							$u = $n;
					}
					
					if ($c_host == "")
						$c_host = $STAT_TXT["0080"];
					
					echo ("<tr>\n");
					echo ("<td>");
					echo ($STAT_TITLE_FONTBEG0);
					echo ($c_host);
					echo ($STAT_TITLE_FONTEND0);
					echo ("</td>\n");
					
					echo ("<td style=\"text-align:right;\">");
					echo ($STAT_TITLE_FONTBEG0);
					echo ($u);
					echo ($STAT_TITLE_FONTEND0);
					echo ("</td>\n");
					
					echo ("<td style=\"text-align:right;\">");
					echo ($STAT_TITLE_FONTBEG0);
					echo ("&nbsp;&nbsp;" . $m . "/" . $y);
					echo ($STAT_TITLE_FONTEND0);
					echo ("</td>\n");
					echo ("</tr>\n");
				}
				
				$j++;
			}
			
			echo ("</table>\n");
		}
	}
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
	global $STAT_BAR_IMAGE_T;
	global $STAT_BAR_IMAGE_M;
	global $STAT_BAR_IMAGE_B;
	global $STAT_BAR_BACK0;
	global $STAT_BAR_BACK1;
	global $STAT_BAR_BACK2;
	global $STAT_BAR_FONTBEG0;
	global $STAT_BAR_FONTEND0;
	global $STAT_BAR_FONTBEG1;
	global $STAT_BAR_FONTEND1;
	global $STAT_BAR_FONTBEG2;
	global $STAT_BAR_FONTEND2;
	global $STAT_BAR_FONTBEG3;
	global $STAT_BAR_FONTEND3;
	$msg = "";
	
	if ((isset($grapharray)) && (count($grapharray) > 0)) {
		$img_w = trim(strtok($STAT_BAR_IMAGE_M, ","));
		$img_h = trim(strtok(","));
		$img_p = trim(strtok(","));
		
		/* Als erstes wird der größte Eintrag im Array und einige Werte ermittelt. */
		
		$b = 0;
		$h = 0;
		$c = 2;
		$d = 0;
		$t = 0;
		$u = 0;
		
		if ($title == "")
			$title = $STAT_TXT["0080"];
		
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
			$msg .= "<td" . coreMakeBackground($STAT_BAR_BACK2)
				. " colspan=\"$c\" style=\"text-align:center;\">";
			$msg .= $STAT_BAR_FONTBEG3;
			$msg .= $title;
			$msg .= $STAT_BAR_FONTEND3;
			$msg .= "</td>\n";
			$msg .= "</tr>\n";
		}
		
		$msg .= "<TR" . coreMakeBackground($STAT_BAR_BACK0) . ">\n";
		$msg .= "<td" . coreMakeBackground($STAT_BAR_BACK1) . ">";
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
				
				$msg .= "<td style=\"vertical-align:bottom; text-align:center;\">";
				$msg .= $STAT_BAR_FONTBEG0;
				$msg .= $v;
				$msg .= "<br>";
				
				if (strlen($STAT_BAR_IMAGE_T) > 0)
					$msg .= "<img " . coreMakeImage($STAT_BAR_IMAGE_T)
						. "><br>";
				
				$msg .= "<img src=\"$img_p\" style=\"width:".$img_w."px; height:".$s."px; border:0px;\">";
				$msg .= "<br>";
				
				if (strlen($STAT_BAR_IMAGE_B) > 0)
					$msg .= "<img " . coreMakeImage($STAT_BAR_IMAGE_B)
						. "><br>";
				
				$msg .= $STAT_BAR_FONTEND0;
				$msg .= "</td>\n";
			} else {
				$msg .= "<td style=\"vertical-align:bottom; text-align:center;\">";
				$msg .= $STAT_BAR_FONTBEG0;
				$msg .= $v;
				$msg .= $STAT_BAR_FONTEND0;
				$msg .= "</td>\n";
			}
		}
		
		$msg .= "<td>&nbsp;</td>\n";
		$msg .= "</tr>\n";
		$msg .= "<TR" . coreMakeBackground($STAT_BAR_BACK1) . ">\n";
		$msg .= "<td>&nbsp;</td>\n";
		
		/* Unter Leiste mit den Beschriftungen der einzelnen Balken ausgeben. */
		
		reset($grapharray);
		
		while (list($i, $v) = each($grapharray)) {
			$msg .= "<td>";
			$msg .= $STAT_BAR_FONTBEG1;
			$msg .= $i;
			$msg .= $STAT_BAR_FONREND1;
			$msg .= "</td>\n";
		}
		
		$msg .= "<td>";
		$msg .= $STAT_BAR_FONTBEG2;
		$msg .= $text_b;
		$msg .= $STAT_BAR_FONREND2;
		$msg .= "</td>\n";
		$msg .= "</tr>\n";
		$msg .= "</table>\n";
		
		/* Durchschnittliche Anzahl der Benutzer und höchste Anzahl der	*/
		/* Benutzer ausgeben. */
		
		$msg .= "<p style=\"text-align:center;\">";
		$msg .= $STAT_BAR_FONTBEG0;
		$msg .= $STAT_TXT["0100"] . " $d - " . $STAT_TXT["0101"] . " $b";
		$msg .= $STAT_BAR_FONTEND0;
		$msg .= "</p>\n";
		$msg .= "</div>\n";
	}
	
	unset($grapharray);
	return $msg;
}

?>