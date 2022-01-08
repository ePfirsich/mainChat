<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == "") {
	die;
}

// Testen, ob Statistiken funktionieren...
$fehler = false;

//testen, ob statisiken überhaupt geschrieben werden
$r1 = sqlQuery("SELECT DISTINCT `id` FROM statistiken");
if ($r1 > 0) {
	if (mysqli_num_rows($r1) == 0) {
		// Keine Enträge vorhanden
		$msg = $t['statistik_keine_statistiken'];
		$fehler = true;
	}
} else {
	$fehler = true;
	$msg = $t['statistik_keine_statistiken'];
}

$box = $t['titel'];
if ($fehler) {
	zeige_tabelle_zentriert($box, $msg);
	return;
}

switch ($aktion) {
	case "stunde":
		
		$h = 24;
		$msg = "";
		
		$showtime = (time() - (($h - 1) * 60 * 60));
		
		statsResetHours($showtime, $h);
		
		$r0 = sqlQuery("SELECT *, DATE_FORMAT(c_timestamp,'%k') as stunde FROM statistiken WHERE UNIX_TIMESTAMP(c_timestamp)>$showtime ORDER BY c_timestamp");
		
		if ($r0 > 0) {
			$i = 0;
			$n = @mysqli_num_rows($r0);
			while ($i < $n) {
				$x = @mysqli_result($r0, $i, "stunde");
				$c_users = @mysqli_result($r0, $i, "c_users");
				$grapharray["$x"] += $c_users;
				$i++;
			}
			
			$msg .= statsPrintGraph($t['statistik_stunden'], $t['statistik_benutzer'], $t['statistik_uhrzeit']);
		}
		
		$box = $t['statistik_nach_stunden'];
		zeige_tabelle_zentriert($box, $msg);
		
		break;
		
	default:
		// Auswahlbox Monat
		$y = urldecode($y);
		$m = urldecode($m);
		
		$grapharray = array();
		
		if (strlen($y) < 1) {
			$y = date("Y", time());
		}
		
		if ((intval($m) < 1) || (intval($m) > 12)) {
			$m = intval(date("m", time()));
		}
		
		$m = SPrintF("%02d", $m);
		$y = SPrintF("%04d", $y);
		
		$msg = "";
		$msg .= "<form name=\"form\" action=\"inhalt.php?bereich=statistik\" method=\"post\">\n";
		$msg .= "<input type=\"hidden\" name=\"aktion\" value=\"$aktion\">\n";
		$msg .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
		$msg .= "<input type=\"hidden\" name=\"page\" value=\"chat-month\">\n";
		$msg .= "<div style=\"margin-top:2px; text-align:center;\">\n";
		$msg .= $t["statistik_monat"];
		$msg .= " " . "\n";
		$msg .= "<select name=\"m\" onchange='form.submit();'>\n";
		
		while (list($i, $n) = each($t_month)) {
			if ($i == $m) {
				$ausgewaehlterMonat = $n;
				$msg .= "<option value=\"$i\" selected>$n\n";
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
			
			if ($n == $y) {
				$msg .= "<option value=\"$n\" selected>$n\n";
			} else {
				$msg .= "<option value=\"$n\">$n\n";
			}
			$i++;
		}
		
		$msg .= "</select>\n";
		$msg .= "<input type=\"submit\" value=\"" . $t["statistik_anzeigen"] . "\">\n";
		$msg .= "</div>\n";
		$msg .= "</form>\n";
		
		// Statistiken einzeln nach Monaten
		$r1 = sqlQuery("SELECT DISTINCT c_users FROM statistiken WHERE date(c_timestamp) LIKE '$y-$m%'");
		
		if ($r1 > 0) {
			statsResetMonth($y, $m);
			
			$r0 = sqlQuery("SELECT *, DATE_FORMAT(c_timestamp,'%d') as tag FROM statistiken WHERE date(c_timestamp) LIKE '$y-$m%' ORDER BY c_timestamp");
			if ($r0 > 0) {
				$i = 0;
				$n = @mysqli_num_rows($r0);
				
				while ($i < $n) {
					$x = @mysqli_result($r0, $i, "tag");
					$c_users = @mysqli_result($r0, $i, "c_users");
					
					if ($c_users > $grapharray["$x"]) {
						$grapharray["$x"] = $c_users;
					}
					$i++;
				}
				
				$msg .= statsPrintGraph($ausgewaehlterMonat . " " . $y, $t['statistik_benutzer'], $t['statistik_tag']);
			}
		}
		
		$box = $t['statistik_nach_monaten'];
		zeige_tabelle_zentriert($box, $msg);
}
?>