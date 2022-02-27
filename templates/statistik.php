<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$monat = filter_input(INPUT_POST, 'monat', FILTER_SANITIZE_NUMBER_INT);
$jahr = filter_input(INPUT_POST, 'jahr', FILTER_SANITIZE_NUMBER_INT);

// Testen, ob Statistiken funktionieren...
$fehler = false;

//testen, ob statisiken überhaupt geschrieben werden
$r1 = sqlQuery("SELECT DISTINCT `id` FROM statistiken");
if ($r1 > 0) {
	if (mysqli_num_rows($r1) == 0) {
		// Keine Enträge vorhanden
		$msg = $lang['statistik_keine_statistiken'];
		$fehler = true;
	}
} else {
	$fehler = true;
	$msg = $lang['statistik_keine_statistiken'];
}

$box = $lang['titel'];
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
			
			$msg .= statsPrintGraph($lang['statistik_stunden'], $lang['statistik_benutzer'], $lang['statistik_uhrzeit']);
		}
		
		$box = $lang['statistik_nach_stunden'];
		zeige_tabelle_zentriert($box, $msg);
		
		break;
		
	default:
		// Auswahlbox Monat
		//$jahr = urldecode($jahr);
		//$monat = urldecode($monat);
		
		$grapharray = array();
		
		if (strlen($jahr) < 1) {
			$jahr = date("Y", time());
		}
		
		if ((intval($monat) < 1) || (intval($monat) > 12)) {
			$monat = intval(date("m", time()));
		}
		
		$monat = SPrintF("%02d", $monat);
		$jahr = SPrintF("%04d", $jahr);
		
		$msg = "";
		$msg .= "<form name=\"form\" action=\"inhalt.php?bereich=statistik\" method=\"post\">\n";
		$msg .= "<input type=\"hidden\" name=\"aktion\" value=\"$aktion\">\n";
		$msg .= "<input type=\"hidden\" name=\"page\" value=\"chat-month\">\n";
		$msg .= "<div style=\"margin-top:2px; text-align:center;\">$lang[statistik_monat]\n";
		$msg .= "<select name=\"monat\" onchange='form.submit();'>\n";
		
		while (list($i, $n) = each($t_month)) {
			if ($i == $monat) {
				$ausgewaehlterMonat = $n;
				$msg .= "<option value=\"$i\" selected>$n\n";
			} else {
				$msg .= "<option value=\"$i\">$n\n";
			}
		}
		
		$msg .= "</select>\n";
		// Auswahlbox Jahr  
		$msg .= "<select name=\"jahr\">\n";
		
		$i = 0;
		
		while ($i < 2) {
			$n = (date("Y", time()) - $i);
			
			if ($n == $jahr) {
				$msg .= "<option value=\"$n\" selected>$n\n";
			} else {
				$msg .= "<option value=\"$n\">$n\n";
			}
			$i++;
		}
		
		$msg .= "</select>\n";
		$msg .= "<input type=\"submit\" value=\"" . $lang["statistik_anzeigen"] . "\">\n";
		$msg .= "</div>\n";
		$msg .= "</form>\n";
		
		// Statistiken einzeln nach Monaten
		$r1 = sqlQuery("SELECT DISTINCT c_users FROM statistiken WHERE date(c_timestamp) LIKE '$y-$m%'");
		
		if ($r1 > 0) {
			statsResetMonth($jahr, $monat);
			
			$r0 = sqlQuery("SELECT *, DATE_FORMAT(c_timestamp,'%d') as tag FROM statistiken WHERE date(c_timestamp) LIKE '$jahr-$monat%' ORDER BY c_timestamp");
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
				
				$msg .= statsPrintGraph($ausgewaehlterMonat . " " . $jahr, $lang['statistik_benutzer'], $lang['statistik_tag']);
			}
		}
		
		$box = $lang['statistik_nach_monaten'];
		zeige_tabelle_zentriert($box, $msg);
}
?>