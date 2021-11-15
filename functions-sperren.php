<?php
function liste() {
	global $t;
	global $f1, $f2, $f3, $f4;
	global $id;
	global $raumsperre;
	global $sperre_dialup;
	global $mysqli_link;
	
	$sperr = "";
	$text = '';
	
	for ($i = 0; $i < count($sperre_dialup); $i++) {
		if ($sperr != "")
			$sperr .= " OR ";
			$sperr .= "is_domain like '%" . mysqli_real_escape_string($mysqli_link, $sperre_dialup[$i]) . "%' ";
	}
	
	if (count($sperre_dialup) >= 1) {
		$query = "DELETE FROM ip_sperre WHERE ($sperr) AND to_days(now())-to_days(is_zeit) > 3 ";
		mysqli_query($mysqli_link, $query);
	}
	
	if ($raumsperre) {
		$query = "delete from sperre where to_days(now())-to_days(s_zeit) > $raumsperre";
		mysqli_query($mysqli_link, $query);
	}
	
	// Alle Sperren ausgeben
	$query = "SELECT u_nick,is_infotext,is_id,is_domain,UNIX_TIMESTAMP(is_zeit) as zeit,is_ip_byte,is_warn,"
		. "SUBSTRING_INDEX(is_ip,'.',is_ip_byte) as isip "
		. "FROM ip_sperre,user WHERE is_owner=u_id "
		. "ORDER BY is_zeit DESC,is_domain,is_ip";
	$result = mysqli_query($mysqli_link, $query);
	$rows = mysqli_num_rows($result);
	
	if ($rows > 0) {
		$i = 0;
		$bgcolor = 'class="tabelle_zeile1"';
		
		$text .= "<table class=\"tabelle_kopf_zentriert\">\n";
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst23]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst24]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst25]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst26]</td>";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[sonst27]</td>";
		$text .= "</tr>\n";
		
		while ($i < $rows) {
			// Ausgabe in Tabelle
			$row = mysqli_fetch_object($result);
			
			if (strlen($row->isip) > 0) {
				if ($row->is_ip_byte == 1) {
					$isip = $row->isip . ".xxx.yyy.zzz";
				} elseif ($row->is_ip_byte == 2) {
					$isip = $row->isip . ".yyy.zzz";
				} elseif ($row->is_ip_byte == 3) {
					$isip = $row->isip . ".zzz";
				} else {
					$isip = $row->isip;
				}
				
				flush();
				
				if ($row->is_ip_byte == 4) {
					$ip_name = @gethostbyaddr($row->isip);
					if ($ip_name == $row->isip) {
						unset($ip_name);
						$text .= "<tr><td $bgcolor><b>"
						. $f1 . $row->isip . $f2 . "</b></td>\n";
					} else {
						$text .= "<tr><td $bgcolor><b>"
						. $f1 . $row->isip . "<br>(" . $ip_name . $f2
						. ")</b></td>\n";
					}
				} else {
					$text .= "<tr><td $bgcolor><b>" . $f1
					. $isip . $f2 . "</b></td>\n";
				}
			} else {
				flush();
				$ip_name = gethostbyname($row->is_domain);
				if ($ip_name == $row->is_domain) {
					unset($ip_name);
					$text .= "<tr><td $bgcolor><b>" . $f1
					. $row->is_domain . $f2 . "</b></td>\n";
				} else {
					$text .= "<tr><td $bgcolor><b>" . $f1
					. $row->is_domain . "<br>(" . $ip_name
					. $f2 . ")</b></td>\n";
				}
			}
			
			// Infotext
			$text .= "<td $bgcolor>" . $f1 . $row->is_infotext . "&nbsp;"
				. $f2 . "</td>\n";
				
				// Nur Warnung ja/nein
				if ($row->is_warn == "ja") {
					$text .= "<td style=\"vertical-align:middle;\" $bgcolor>" . $f3 . $t['sonst20'] . $f4 . "</td>\n";
				} else {
					$text .= "<td style=\"vertical-align:middle;\" $bgcolor>" . $f3 . $t['sonst21'] . $f4 . "</td>\n";
				}
				
				// Eintrag - Benutzer und Datum
				$text .= "<td $bgcolor><small>$row->u_nick<br>\n";
				$text .= date("d.m.y", $row->zeit) . "&nbsp;";
				$text .= date("H:i:s", $row->zeit) . "&nbsp;";
				$text .= "</small></td>\n";
				
				// Aktion
				if ($row->is_domain == "-GLOBAL-") {
					$text .= "<td $bgcolor>" . $f1 . "<b>\n";
					$text .= "<a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperre0\">" . "[" . $t['sonst30'] ."]" . "</a>\n";
					$text .= "</b>" . $f2 . "</td>\n";
				} elseif ($row->is_domain == "-GAST-") {
					$text .= "<td $bgcolor>" . $f1 . "<b>\n";
					$text .= "<a href=\"inhalt.php?seite=sperren&id=$id&aktion=loginsperregast0\">" . "[" . $t['sonst30'] ."]" . "</a>\n";
					echo "</b>" . $f2 . "</td>\n";
				} else {
					$text .= "<td $bgcolor>" . $f1
					. "<b>[<a href=\"inhalt.php?seite=sperren&id=$id&aktion=aendern&is_id=$row->is_id\">$t[sonst28]</a>]\n"
					. "[<a href=\"inhalt.php?seite=sperren&id=$id&aktion=loeschen&is_id=$row->is_id\">$t[sonst29]</a>]\n";
					$text .= "</b>" . $f2 . "</td>";
				}
				$text .= "</tr>\n";
				
				// Farben umschalten
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
				}
				
				$i++;
				
		}
		
		$text .= "</table>\n";
		
	} else {
		$text .= "<p style=\"text-align:center;\">$t[sonst4]</p>\n";
	}
	
	return $text;
	
	mysqli_free_result($result);
}
?>