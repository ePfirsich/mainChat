<?php

function zeige_aktionen($aktion) {
	
	// Zeigt Matrix der Aktionen an
	// Definition der aktionen in config.php ($def_was)
	
	global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $mysqli_link, $u_nick, $u_id;
	global $def_was, $eingabe_breite;
	global $forumfeatures;
	
	$query = "SELECT * from aktion " . "WHERE a_user=$u_id ";
	$button = "Eintragen";
	$titel1 = "Für";
	$titel2 = "sind folgende Aktionen eingetragen";
	
	echo "<FORM NAME=\"freund_loeschen\" ACTION=\"$PHP_SELF\" METHOD=POST>\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"id\" VALUE=\"$id\">\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"aktion\" VALUE=\"eintragen\">\n"
		. "<INPUT TYPE=\"HIDDEN\" NAME=\"http_host\" VALUE=\"$http_host\">\n"
		. "<TABLE WIDTH=100% BORDER=0 CELLPADDING=4 CELLSPACING=0>";
	
	// Einstellungen aus Datenbank lesen 
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_array($result)) {
			$was[$row['a_was']][$row['a_wann']] = $row;
		}
	}
	
	// Aktionen zeigen
	echo "<TR BGCOLOR=\"#85D4FF\"><TD COLSPAN=5>XXX<b>$titel1 $u_nick $titel2:</b></TD></TR>\n"
		. "<TR><TD></TD>";
	
	$i = 0;
	$bgcolor = 'class="tabelle_zeile1"';
	
	// Alle möglichen a_wann in Array lesen
	$query = "SHOW COLUMNS FROM aktion like 'a_wann'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$txt = str_replace("'", "",
			substr(mysqli_result($result, 0, "Type"), 5, -1));
		$a_wann = explode(",", $txt);
	}
	$anzahl_spalten = count($a_wann);
	
	// Alle möglichen a_wie in Array lesen
	$query = "SHOW COLUMNS FROM aktion like 'a_wie'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$txt = str_replace("'", "",
			substr(mysqli_result($result, 0, "Type"), 4, -1));
		$a_wie = explode(",", $txt);
	}
	$anzahl_wie = count($a_wie);
	
	// OLM merken
	$onlinemessage = $a_wie[3];
	
	// Wenn das Forum deaktiviert wird, sollte der 3. Eintrag aus $def_was raus, wichtig ist aber die Einhaltung der Reihenfolge in def_was
	if (!$forumfeatures) {
		unset($def_was[2]);
	}
	
	// Spezial Offline-Nachrichten (OLM löschen)
	$offline_wie = $a_wie;
	for ($i = 0; $i < $anzahl_wie; $i++) {
		if (isset($offline_wie[$i]) && $offline_wie[$i] == $onlinemessage)
			unset($offline_wie[$i]);
	}
	
	// Alle Kombinationen von a_wie mit onlinemessage erstellen
	// Keine muss als erstes, OLM als letztes definiert sein!
	for ($i = 1; $i < $anzahl_wie - 2; $i++) {
		$a_wie[] = $a_wie[$i] . "," . $onlinemessage;
	}
	
	// Zeile der a_wann ausgeben
	foreach ($a_wann as $a_wann_eintrag) {
		echo "<TD ALIGN=\"CENTER\"><b>" . $f1 . $a_wann_eintrag . $f2
			. "</b></TD>\n";
	}
	echo "</TR>\n";
	
	// Tabelle zeilenweise ausgeben
	$i = 0;
	foreach ($def_was as $def_was_eintrag) {
		
		echo "<tr><td style=\"text-align:right;\" $bgcolor><b>" . $f1
			. $def_was_eintrag . $f2 . "</b></TD>\n";
		foreach ($a_wann as $a_wann_eintrag) {
			echo "<td style=\"text-align:center;\" $bgcolor>" . $f3
				. "<SELECT NAME=\"aktion_datensatz[$def_was_eintrag][$a_wann_eintrag]\">\n";
			
			// Zwischen offline/online unterscheiden
			if ($a_wann_eintrag == "Sofort/Offline") {
				$wie = $offline_wie;
			} else {
				$wie = $a_wie;
			}
			
			// Nachrichtentypen einzeln als Auswahl ausgeben
			foreach ($wie as $auswahl) {
				
				if (isset($was[$def_was_eintrag][$a_wann_eintrag])
					&& $was[$def_was_eintrag][$a_wann_eintrag]['a_wie']
						== $auswahl) {
					echo "<OPTION SELECTED VALUE=\""
						. $was[$def_was_eintrag][$a_wann_eintrag]['a_id'] . "|"
						. $auswahl . "\">" . str_replace(",", "+", $auswahl)
						. "\n";
				} else {
					echo "<OPTION VALUE=\""
						. (isset($was[$def_was_eintrag][$a_wann_eintrag]) ? $was[$def_was_eintrag][$a_wann_eintrag]['a_id']
							: "") . "|" . $auswahl . "\">"
						. str_replace(",", " + ", $auswahl) . "\n";
				}
			}
			echo "</SELECT>" . $f4 . "</TD>\n";
			
		}
		echo "</tr>\n";
		
		if (($i % 2) > 0) {
			$bgcolor = 'class="tabelle_zeile1"';
		} else {
			$bgcolor = 'class="tabelle_zeile2"';
		}
		$i++;
	}
	
	echo "<tr><td $bgcolor>&nbsp;</td><td style=\"text-align:right;\" $bgcolor colspan=\"4\">"
		. $f1 . "<INPUT TYPE=\"SUBMIT\" NAME=\"los\" VALUE=\"$button\">" . $f2
		. "</td></tr>\n" . "</table></form>\n";
	
}

function eintrag_aktionen($aktion_datensatz)
{
	
	// Array mit definierten Aktionen in die DB schreiben
	
	global $def_was, $dbase, $u_id, $u_nick, $mysqli_link;
	
	// Alle möglichen a_wann in Array lesen
	$query = "SHOW COLUMNS FROM aktion like 'a_wann'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$txt = str_replace("'", "",
			substr(mysqli_result($result, 0, "Type"), 5, -1));
		$a_wann = explode(",", $txt);
	}
	
	foreach ($def_was as $def_was_eintrag) {
		
		foreach ($a_wann as $a_wann_eintrag) {
			
			// In aktion_datensatz stehen ID und Wert als a_id|a_wie
			$temp = explode("|",
				$aktion_datensatz[$def_was_eintrag][$a_wann_eintrag]);
			
			if (!$temp[0] || $temp[0] == "0" || $temp[0] == "") {
				$query = "DELETE FROM aktion "
					. "WHERE a_was='" . mysqli_real_escape_string($mysqli_link, $def_was_eintrag) . "' "
					. "AND a_wann='" . mysqli_real_escape_string($mysqli_link, $a_wann_eintrag) . "' " . "AND a_user='$u_id'";
				$result = mysqli_query($mysqli_link, $query);
			}
			
			$f['a_wie'] = $temp[1];
			$f['a_was'] = $def_was_eintrag;
			$f['a_wann'] = $a_wann_eintrag;
			$f['a_user'] = $u_id;
			$f['a_text'] = $u_nick;
			schreibe_db("aktion", $f, $temp[0], "a_id");
		}
	}
}

?>