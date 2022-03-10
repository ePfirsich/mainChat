<?php
// Direkten Aufruf der Datei verbieten
if( !isset($u_id) || $u_id == NULL || $u_id == "") {
	die;
}

switch ($aktion) {
	case "top100":
		$anzahl = 100;
		break;
	
	case "top1000":
		$anzahl = 1000;
		break;
	
	case "top10":
	
	default:
		$anzahl = 10;
}

$date = new DateTime();
$date->format('Y-m-d H:i:s');
$bgcolor = 'class="tabelle_zeile1 smaller"';
echo "<table class=\"tabelle_kopf_zentriert\">\n";
echo "<tr>\n";
echo "<td class=\"tabelle_kopfzeile\" style=\"width:4%;\">&nbsp;</td>\n";
echo "<td class=\"tabelle_kopfzeile\" style=\"width:32%; font-weight:bold;\" colspan=\"2\">" . $lang['top1'] ." " . formatIntl($date,'MMMM',$locale) . "</td>\n";
echo "<td class=\"tabelle_kopfzeile\" style=\"width:32%; font-weight:bold;\" colspan=\"2\">" . $lang['top1'] ." " . formatIntl($date,'Y',$locale) . "</td>\n";
echo "<td class=\"tabelle_kopfzeile\" style=\"width:32%; font-weight:bold;\" colspan=\"2\">" . $lang['top2'] ."</td>\n";
echo "</tr>\n";

// Im Cache nachsehen, ob aktuelle Daten vorhanden sind (nicht älter als 6 Stunden)
$query = pdoQuery("SELECT `t_id`, `t_daten` FROM `top10cache` WHERE `t_eintrag` = 1 AND date_add(`t_zeit`, interval '6' hour)>=NOW()", []);

$resultCount = $query->rowCount();
if ($resultCount > 0) {
	$result = $query->fetch();
	$t_id = $result['t_id'];
	$array_user = unserialize($result['t_daten']);
} else {
	unset($array_user);
	// Top 100 Punkte im aktuellen Monat als Array aufbauen
	$query = pdoQuery("SELECT `u_punkte_monat` AS `punkte`, `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_chathomepage` FROM `user` WHERE `u_punkte_monat` !=0 AND `u_punkte_datum_monat` = :u_punkte_datum_monat AND `u_punkte_datum_jahr` = :u_punkte_datum_jahr AND `u_level` != 'Z' ORDER BY `u_punkte_monat` DESC, `u_punkte_gesamt` DESC, `u_punkte_jahr` DESC LIMIT 0,100", [':u_punkte_datum_monat'=>date("n", time()), ':u_punkte_datum_jahr'=>date("Y", time())]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$array_user[0][] = $row;
		}
	}
	
	// Top 100 Punkte im aktuellen Jahr als Array aufbauen
	$query = pdoQuery("SELECT `u_punkte_jahr` AS `punkte`, `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_chathomepage` FROM `user` WHERE `u_punkte_jahr` !=0 AND `u_punkte_datum_jahr` = :u_punkte_datum_jahr AND `u_level` != 'Z' ORDER BY `u_punkte_jahr` DESC, `u_punkte_gesamt` DESC, `u_punkte_monat` DESC LIMIT 0,100", [':u_punkte_datum_jahr'=>date("Y", time())]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$array_user[1][] = $row;
		}
	}
	
	// Top 100 Gesamtpunkte als Array aufbauen
	$query = pdoQuery("SELECT `u_punkte_gesamt` AS `punkte`, `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_chathomepage` FROM `user` WHERE `u_punkte_gesamt` !=0 AND `u_level` != 'Z' ORDER BY `u_punkte_gesamt` DESC, `u_punkte_monat` DESC, `u_punkte_jahr` DESC LIMIT 0,100", []);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$array_user[2][] = $row;
		}
	}
	
	// Daten in Cache schreiben und alle anderen Einträge löschen
	unset($f);
	$f['t_eintrag'] = 1;
	$f['t_daten'] = isset($array_user) ? serialize($array_user) : null;
	
	pdoQuery("INSERT INTO `top10cache` (`t_eintrag`, `t_daten`) VALUES (:t_eintrag, :t_daten)",
		[
			':t_eintrag'=>$f['t_eintrag'],
			':t_daten'=>$f['t_daten']
		]);

	$t_id = $pdo->lastInsertId();
	
	pdoQuery("DELETE FROM `top10cache` WHERE `t_eintrag` = 1 AND `t_id` != :t_id", [':t_id'=>$t_id]);
}

// Array als Tabelle ausgeben
if (is_array($array_user)) {
	for ($i = 0; $i < $anzahl; $i++) {
		echo "<tr><td style=\"text-align:right; font-weight:bold;\" $bgcolor>" . ($i + 1) . "</td>";
		for ($j = 0; $j < 3; $j++) {
			if (isset($array_user[$j]) && isset($array_user[$j][$i])
				&& $array_user[$j][$i]['punkte']) {
				$array_user[$j][$i]['u_punkte_anzeigen'] = '1';
				echo "<td style=\"width:8%; text-align:right;\" $bgcolor>" . $array_user[$j][$i]['punkte'] . "</td>"
			."<td style=\"width:24%\" $bgcolor>" . zeige_userdetails($array_user[$j][$i]['u_id'], $array_user[$j][$i]) . "</td>\n";
			} else {
				echo "<td style=\"width:32%;\" colspan=\"2\" $bgcolor>" . "&nbsp;" . "</td>\n";
			}
		}
		echo "</tr>\n";
		flush();
		if (($i % 2) > 0) {
			$bgcolor = 'class="tabelle_zeile1 smaller"';
		} else {
			$bgcolor = 'class="tabelle_zeile2 smaller"';
		}
		
	}
}
echo "</table>";
?>