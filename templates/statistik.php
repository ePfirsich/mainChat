<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

$monat = filter_input(INPUT_POST, 'monat', FILTER_SANITIZE_NUMBER_INT);
$jahr = filter_input(INPUT_POST, 'jahr', FILTER_SANITIZE_NUMBER_INT);

// Testen, ob Statistiken funktionieren...
$fehler = false;

//testen, überhaupt Statisiken geschrieben werden
$query = pdoQuery("SELECT DISTINCT `id` FROM `statistiken`", []);
$resultCount = $query->rowCount();
if ($resultCount == 0) {
	// Keine Enträge vorhanden
	$msg = $lang['statistik_keine_statistiken'];
	$fehler = true;
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
		
		$query = pdoQuery("SELECT `c_users`, DATE_FORMAT(`c_timestamp`,'%k') AS `stunde` FROM `statistiken` WHERE UNIX_TIMESTAMP(`c_timestamp`) > :c_timestamp ORDER BY `c_timestamp`", [':c_timestamp'=>$showtime]);
		
		$resultCount = $query->rowCount();
		$grapharray = array();
		if ($resultCount > 0) {
			$result = $query->fetchAll();
			foreach($result as $zaehler => $inhalt) {
				$x = $inhalt['stunde'];
				$c_users = $inhalt['c_users'];
				if(isset($grapharray["$x"])) {
					$grapharray["$x"] += $c_users;
				} else {
					$grapharray["$x"] = $c_users;
				}
			}
			
			$msg .= statsPrintGraph($lang['statistik_stunden'], $lang['statistik_benutzer'], $lang['statistik_uhrzeit']);
		}
		
		$box = $lang['statistik_nach_stunden'];
		zeige_tabelle_zentriert($box, $msg);
		
		break;
		
	default:
		// Auswahlbox Monat
		
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
		
		foreach($lang_month as $i => $n) {
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
		$query = pdoQuery("SELECT DISTINCT `c_users` FROM `statistiken` WHERE date(`c_timestamp`) LIKE :c_timestamp", [':c_timestamp'=>"$jahr-$monat%"]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			statsResetMonth($jahr, $monat);
			
			$query = pdoQuery("SELECT `c_users`, DATE_FORMAT(c_timestamp,'%d') AS `tag` FROM `statistiken` WHERE date(`c_timestamp`) LIKE :c_timestamp ORDER BY `c_timestamp`", [':c_timestamp'=>"$jahr-$monat%"]);
			
			$resultCount = $query->rowCount();
			$grapharray = array();
			if ($resultCount > 0) {
				$result = $query->fetchAll();
				foreach($result as $zaehler => $inhalt) {
					$x = $inhalt['tag'];
					$c_users = $inhalt['c_users'];
					
					if (!isset($grapharray["$x"]) || $c_users > $grapharray["$x"]) {
						$grapharray["$x"] = $c_users;
					}
				}
				
				$msg .= statsPrintGraph($ausgewaehlterMonat . " " . $jahr, $lang['statistik_benutzer'], $lang['statistik_tag']);
			}
		}
		
		$box = $lang['statistik_nach_monaten'];
		zeige_tabelle_zentriert($box, $msg);
}
?>