<?php

function zeige_moderations_antworten($o_raum, $answer = "") {
	global $lang, $u_id;
	
	$box = $lang['mod10'];
	$text = "";
	
	$query = pdoQuery("SELECT `c_id`, `c_text` FROM `moderation` WHERE `c_raum` = :c_raum AND `c_typ` = 'P' ORDER BY `c_text`", [':c_raum'=>$o_raum]);
	
	$resultCount = $query->rowCount();
	$text .= "<table style=\"width:100%;\">";
	if ($resultCount > 0) {
		$i = 0;
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$i++;
			if ($i % 2 == 0) {
				$bgcolor = 'class="tabelle_zeile1"';
			} else {
				$bgcolor = 'class="tabelle_zeile2"';
			}
			$text .= "<tr>";
			$text .= "<td align=left $bgcolor><small>";
			$text .= "<a href=\"schreibe.php?text=" . urlencode($row['c_text']) . "\" class=\"schreibe-chat\">";
			$text .= "$row[c_text]</a></small></td><td align=right $bgcolor><small>";
			$text .= "<a href=moderator.php?mode=answeredit&answer=$row[c_id] class=\"button\" title=\"$lang[mod12]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[mod12]</span></a> ";
			$text .= "<a href=moderator.php?mode=answerdel&answer=$row[c_id] class=\"button\" title=\"$lang[mod13]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[mod13]</span></a> ";
			$text .= "</small></td>";
			$text .= "</tr>";
		}
		
		$text .= "<span id=\"out\"></span>\n";
		$text .= "<script>
			document.querySelectorAll('a.schreibe-chat').forEach(item => {
				item.addEventListener('click', event => {
					// Default-Aktion für Klick auf den Link,
					// d. h. direktes Aufrufen der Seite, verhindern, da wir das Linkziel mit Ajax aufrufen wollen:
					event.preventDefault();
					// Link aus dem href-Attribut holen:
					const link = event.target.href;
					fetch(link, {
						method: 'get'
					}).then(res => {
						return res.text();
						}).then(res => {
							console.log(res);
							document.getElementById('out').innerHTML = res;
						});
				});
			})
			</script>";
	}
	$text .= "</table>";
	$text .= "<br><center>";
	$text .= "<form action=\"moderator.php\" method=\"post\">";
	$text .= $lang['mod11'] . "<br>";
	$text .= "<input type=\"hidden\" name=\"mode\" value=\"answernew\">";
	if ($answer != "")
		$text .= "<input type=\"hidden\" name=\"answer\" value=\"$answer\">";
		$text .= "<textarea name=\"answertxt\" rows=\"5\" cols=\"60\">";
	if ($answer != "") {
		$answer = intval($answer);
		
		$query = pdoQuery("SELECT `c_text` FROM `moderation` WHERE `c_id` = :c_id AND c_typ = 'P'", [':c_id'=>$answer]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetch();
			echo $result['c_text'];
		}
	}
	$text .= "</textarea>";
	$text .= "<br><input type=\"submit\" value=\"$lang[mod1]\">";
	$text .= "</form>";
	
	zeige_tabelle_volle_breite($box,$text);
}

function bearbeite_moderationstexte($o_raum) {
	global $lang, $action, $u_id, $system_farbe;
	
	if (is_array($action)) {
		echo "<small>";
		$a = 0;
		reset($action);
		// erst mal die Datensätze reservieren...
		while ($a < count($action)) {
			$key = key($action);
			// nur markieren, was noch frei ist.
			pdoQuery("UPDATE `moderation` SET `c_moderator` = :c_moderator WHERE `c_id` = :c_id AND `c_typ` = 'N' AND `c_moderator` = 0", [':c_moderator'=>$u_id, ':c_id'=>$key]);
			
			next($action);
			$a++;
		}
		// jetzt die reservierten Aktionen bearbeiten.
		$a = 0;
		reset($action);
		while ($a < count($action)) {
			$key = key($action);
			// nur auswählen, was bereits von diesem Moderator reserviert ist
			$query = pdoQuery("SELECT * FROM `moderation` WHERE `c_id` = :c_id AND `c_typ` = 'N' AND `c_moderator` = :c_moderator", [':c_id'=>intval($key), ':c_moderator'=>$u_id]);
			
			$resultCount = $query->rowCount();
			$result = $query->fetch();
			if ($result > 0) {
				if ($resultCount > 0) {
					switch ($action[$key]) {
						case "ok":
						case "clear":
						case "thru":
						// vorbereiten für umspeichern... geht leider nicht 1:1, weil fetch_array mehr zurückliefert als in $result[] sein darf...
							$c['c_von_user'] = $result['c_von_user'];
							$c['c_an_user'] = $result['c_an_user'];
							$c['c_typ'] = $result['c_typ'];
							$c['c_raum'] = $result['c_raum'];
							$c['c_text'] = $result['c_text'];
							$c['c_farbe'] = $result['c_farbe'];
							$c['c_zeit'] = $result['c_zeit'];
							$c['c_von_user_id'] = $result['c_von_user_id'];
							if ($action[$key] == "ok") {
								// eigene ID vermerken
								$c['c_moderator'] = $u_id;
								// Zeit löschen, damit markierte oben erscheint...
								unset($c['c_zeit']);
							} else {
								// freigeben -> id=0 schreiben
								$c['c_moderator'] = 0;
							}
							// und in moderations-tabelle schreiben
							if ($action[$key] == "thru") {
								unset($c['c_moderator']);
								schreibe_chat($c);
							} else {
								schreibe_moderiert($c);
							}
							break;
						case "notagain":
							system_msg("", 0, $result['c_von_user_id'],
								$system_farbe, $lang['moderiertdel1']);
							system_msg("", 0, $result['c_von_user_id'],
								$system_farbe, "&gt;&gt;&gt; " . $result['c_text']);
							break;
						case "better":
							system_msg("", 0, $result['c_von_user_id'],
								$system_farbe, $lang['moderiertdel2']);
							system_msg("", 0, $result['c_von_user_id'],
								$system_farbe, "&gt;&gt;&gt; " . $result['c_text']);
							break;
						case "notime":
							system_msg("", 0, $result['c_von_user_id'],
								$system_farbe, $lang['moderiertdel3']);
							system_msg("", 0, $result['c_von_user_id'],
								$system_farbe, "&gt;&gt;&gt; " . $result['c_text']);
							break;
						case "delete":
							system_msg("", 0, $result['c_von_user_id'],
								$system_farbe, $lang['moderiertdel4']);
							system_msg("", 0, $result['c_von_user_id'],
								$system_farbe, "&gt;&gt;&gt; " . $result['c_text']);
							break;
					}
					// jetzt noch aus moderierter Tabelle löschen.
					
					pdoQuery("DELETE FROM `moderation` WHERE `c_id` = :c_id `AND `c_moderator` = :c_moderator", [':c_id'=>$key, ':c_moderator'=>$u_id]);
				} else {
					echo "$lang[mod9]<br>";
				}
			}
			next($action);
			$a++;
		}
		echo "</small>";
	}
}

function zeige_moderationstexte($o_raum, $limit = 20) {
	global $lang, $action, $moderation_rueckwaerts, $moderationsexpire, $u_id;
	
	// gegen DAU-Eingaben sichern...
	$limit = max(intval($limit), 20);
	// erst mal alle alten Msgs expiren...
	if ($moderationsexpire == 0) {
		$moderationsexpire = 30;
	}
	$expiretime = $moderationsexpire * 60;
	pdoQuery("DELETE FROM `moderation` WHERE unix_timestamp(c_zeit)+:time<unix_timestamp(NOW()) AND `c_moderator` = 0 AND `c_typ` = 'N'", [':time'=>$expiretime]);
	
	if ($moderation_rueckwaerts == 1) {
		$query = pdoQuery("SELECT `c_id`, `c_text`, `c_von_user`, `c_moderator` FROM `moderation` WHERE `c_raum` = :c_raum AND `c_typ` = 'N' ORDER BY `c_id` DESC LIMIT 0, :limit_end", [':c_raum'=>intval($o_raum), ':limit_end'=>intval($limit)]);
	} else {
		$query = pdoQuery("SELECT `c_id`, `c_text`, `c_von_user`, `c_moderator` FROM `moderation` WHERE `c_raum` = :c_raum AND `c_typ` = 'N' ORDER BY `c_id` LIMIT 0, :limit_end", [':c_raum'=>intval($o_raum), ':limit_end'=>intval($limit)]);
	}
	$resultCount = $query->rowCount();
		if ($resultCount > 0) {
		echo "<script>\n";
		echo "function sel() {\n";
		echo "	document.forms['modtext'].elements['ok'].focus();\n";
		echo "}\n";
		echo "</script>\n";
		echo "<form name=\"modtext\" action=\"moderator.php\" method=\"post\">\n";
		if ($resultCount > 0) {
			echo "<table style=\"width=100%;\">\n";
			echo "<tr>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"images/moderator/ok.gif\" height=20 width=20 alt=\"" . $lang['mod16'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"images/moderator/nope.gif\" height=20 width=20 alt=\"" . $lang['mod17'] . "\"></td>";
			echo "<td style=\"vertical-align:bottom;\"  class=\"tabelle_kopfzeile\">";
			echo "<table style=\"width=100%;\"><tr><td>";
			echo "<small><b>" . $lang['mod2'];
			echo "</td><td align=right>";
			echo "<input type=submit name=ok2 value=\"go!\">\n";
			echo "</b></small>";
			echo "</td></tr></table>";
			echo "</td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"images/moderator/ok.gif\" height=20 width=20 alt=\"" . $lang['mod14'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"images/moderator/wdh.gif\" height=20 width=20 alt=\"" . $lang['mod3'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"images/moderator/smile.gif\" height=20 width=20 alt=\"" . $lang['mod4'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"images/moderator/time.gif\" height=20 width=20 alt=\"" . $lang['mod5'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"images/moderator/nope.gif\" height=20 width=20 alt=\"" . $lang['mod15'] . "\"></td>";
			echo "</tr>\n";
			
			$i = 0;
			$result = $query->fetchAll();
			foreach($result as $zaehler => $row) {
				$i++;
				if ($i % 2 == 0) {
					$bgcolor = 'class="tabelle_zeile1"';
					$moderatorG = 'class="tabelle_zeile_moderator_1g"';
					$moderatorR = 'class="tabelle_zeile_moderator_1r"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
					$moderatorG = 'class="tabelle_zeile_moderator_2g"';
					$moderatorR = 'class="tabelle_zeile_moderator_2r"';
				}
				echo "<tr>";
				echo "<td align=center $moderatorG><small>";
				if ($row['c_moderator'] == $u_id || $row['c_moderator'] == 0) {
					echo "<input type=radio name=action[$row[c_id]] value='ok' onclick=submit();";
					if ($row['c_moderator'] == $u_id)
						echo " checked";
					echo ">";
					$moderatorenfarbe = 'class="moderator_schwarz"';
				} else {
					echo "&nbsp";
					$moderatorenfarbe = 'class="moderator_grau"';
				}
				echo "</small></td>";
				echo "<td align=center $moderatorG><small>";
				if ($row['c_moderator'] == $u_id) {
					echo "<input type=radio name=action[$row[c_id]] value='clear' onclick=submit();>";
					$b1 = "<b>";
					$b2 = "</b>";
				} else {
					$b1 = "";
					$b2 = "";
				}
				echo "&nbsp;</small></td>";
				echo "<td width=100% $bgcolor $moderatorenfarbe><small>$b1$row[c_von_user]: $row[c_text]$b2</small></td>";
				if ($row['c_moderator'] == $u_id || $row['c_moderator'] == 0) {
					echo "<td align=center $moderatorG><small><input type=radio name=action[$row[c_id]] value='thru';></small></td>";
					echo "<td align=center $moderatorR><small><input type=radio name=action[$row[c_id]] value='notagain';></small></td>";
					echo "<td align=center $moderatorR><small><input type=radio name=action[$row[c_id]] value='better';></small></td>";
					echo "<td align=center $moderatorR><small><input type=radio name=action[$row[c_id]] value='notime';></small></td>";
					echo "<td align=center $moderatorR><small><input type=radio name=action[$row[c_id]] value='delete';></small></td>";
				} else {
					echo "<td $moderatorG><small>&nbsp;</small></td>";
					echo "<td $moderatorR><small>&nbsp;</small></td>";
					echo "<td $moderatorR><small>&nbsp;</small></td>";
					echo "<td $moderatorR><small>&nbsp;</small></td>";
					echo "<td $moderatorR><small>&nbsp;</small></td>";
				}
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
		echo "<center>";
		echo "<input type=text name=limit value=$limit size=5>\n";
		echo "<input type=submit name=ok value=" . $lang['mod_ok'] . ">\n";
		echo "</center>";
		echo "</form>\n";
	}
	return $resultCount;
}

function anzahl_moderationstexte($o_raum) {
	$query = pdoQuery("SELECT `c_id` FROM `moderation` WHERE `c_raum` = :c_raum AND `c_typ` = 'N' ORDER BY `c_id`", [':c_raum'=>intval($o_raum)]);
	
	$resultCount = $query->rowCount();
	
	return $resultCount;
}

?>