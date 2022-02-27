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

$bgcolor = 'class="tabelle_zeile1 smaller"';
echo "<table class=\"tabelle_kopf_zentriert\">\n"
	. "<tr>"
	. "<td class=\"tabelle_kopfzeile\" style=\"width:4%;\">&nbsp;</td>"
	. "<td class=\"tabelle_kopfzeile\" style=\"width:32%; font-weight:bold;\" colspan=\"2\">" . $lang['top1'] ." " . strftime("%B", time()) . "</td>"
	. "<td class=\"tabelle_kopfzeile\" style=\"width:32%; font-weight:bold;\" colspan=\"2\">" . $lang['top1'] ." " . strftime("%Y", time()) . "</td>"
	. "<td class=\"tabelle_kopfzeile\" style=\"width:32%; font-weight:bold;\" colspan=\"2\">" . $lang['top2'] ."</td>"
	. "</tr>\n";

// im Cache nachsehen, ob aktuelle Daten vorhanden sind (nicht älter als 6 Stunden)
$query = "SELECT * FROM top10cache WHERE t_eintrag=1 AND date_add(t_zeit, interval '6' hour)>=NOW()";
$result = sqlQuery($query);
if ($result && mysqli_num_rows($result) > 0) {
	$t_id = mysqli_result($result, 0, "t_id");
	$array_user = unserialize(mysqli_result($result, 0, "t_daten"));
} else {
	unset($array_user);
	// Top 100 Punkte im aktuellen Monat als Array aufbauen
	$query = "SELECT u_punkte_monat AS punkte,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage FROM user WHERE u_punkte_monat!=0 " . "and u_punkte_datum_monat="
		. date("n", time()) . " and u_punkte_datum_jahr=" . date("Y", time()) . " and u_level != 'Z' " . "ORDER BY u_punkte_monat desc,u_punkte_gesamt desc,u_punkte_jahr desc LIMIT 0,100";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) > 0) {
		$array_anzahl[0] = mysqli_num_rows($result);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$array_user[0][] = $row;
		}
	}
	mysqli_free_result($result);
	
	// Top 100 Punkte im aktuellen Jahr als Array aufbauen
	$query = "SELECT u_punkte_jahr as punkte,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage FROM user WHERE u_punkte_jahr!=0 AND u_punkte_datum_jahr="
		. date("Y", time()) . "  and u_level != 'Z' " . "ORDER BY u_punkte_jahr desc,u_punkte_gesamt desc,u_punkte_monat desc LIMIT 0,100";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) > 0) {
		$array_anzahl[1] = mysqli_num_rows($result);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$array_user[1][] = $row;
		}
	}
	mysqli_free_result($result);
	
	// Top 100 Gesamtpunkte als Array aufbauen
	$query = "SELECT u_punkte_gesamt as punkte,u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage FROM user "
		. "WHERE u_punkte_gesamt!=0  and u_level != 'Z' ORDER BY u_punkte_gesamt desc,u_punkte_monat desc,u_punkte_jahr desc LIMIT 0,100";
	$result = sqlQuery($query);
	if ($result && mysqli_num_rows($result) > 0) {
		$array_anzahl[2] = mysqli_num_rows($result);
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$array_user[2][] = $row;
		}
	}
	mysqli_free_result($result);
	
	// Daten in Cache schreiben und alle anderen Einträge löschen
	unset($f);
	$f['t_eintrag'] = 1;
	$f['t_daten'] = isset($array_user) ? serialize($array_user) : null;
	$t_id = schreibe_db("top10cache", $f, 0, "t_id");
	$query = "DELETE FROM top10cache WHERE t_eintrag=1 AND t_id !='$t_id'";
	$result = sqlUpdate($query);
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