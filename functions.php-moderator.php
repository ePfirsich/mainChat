<?php

function zeige_moderations_antworten($o_raum, $answer = "") {
	global $t;
	global $id;
	global $mysqli_link;
	global $u_id;
	
	$query = "SELECT c_id,c_text FROM moderation WHERE c_raum=" . intval($o_raum) . " AND c_typ='P' ORDER BY c_text";
	$result = mysqli_query($mysqli_link, $query);
	echo "<table style=\"width:100%;\">";
	if ($result > 0) {
		$i = 0;
		while ($row = mysqli_fetch_object($result)) {
			$i++;
			if ($i % 2 == 0) {
				$bgcolor = 'class="tabelle_zeile1"';
			} else {
				$bgcolor = 'class="tabelle_zeile2"';
			}
			echo "<tr>";
			echo "<td align=left $bgcolor><small>";
			echo "<a href=\"#\" onclick=\"opener.parent.frames['schreibe'].location='schreibe.php?id=$id&text=";
			echo urlencode($row->c_text);
			echo "'; return(false);\">";
			echo "$row->c_text</a></small></td><td align=right $bgcolor><small>";
			echo "<a href=moderator.php?id=$id&mode=answeredit&answer=$row->c_id>$t[mod12]</a> ";
			echo "<a href=moderator.php?id=$id&mode=answerdel&answer=$row->c_id>$t[mod13]</a> ";
			echo "</small></td>";
			echo "</tr>";
		}
		mysqli_free_result($result);
	}
	echo "</table>";
	echo "<br><center>";
	echo "<form>";
	echo $t['mod11'] . "<br>";
	echo "<input type=hidden name=id value=$id>";
	echo "<input type=hidden name=mode value=answernew>";
	if ($answer != "")
		echo "<input type=hidden name=answer value=$answer>";
	echo "<textarea name=answertxt rows=5 cols=60>";
	if ($answer != "") {
		$answer = intval($answer);
		$query = "SELECT c_id,c_text FROM moderation WHERE c_id=$answer AND c_typ='P'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result > 0) {
			echo mysqli_result($result, 0, "c_text");
		}
		mysqli_free_result($result);
	}
	echo "</textarea>";
	echo "<br><input type=submit value=\"$t[mod1]\">";
	echo "</form>";
	echo "<a href=# onclick=window.close();>" . $t['mod10'] . "</a></center>";
}

function bearbeite_moderationstexte($o_raum)
{
	global $t;
	global $id;
	global $mysqli_link;
	global $action;
	global $u_id;
	global $system_farbe;
	
	if (is_array($action)) {
		echo "<small>";
		$a = 0;
		reset($action);
		// erst mal die Datensätze reservieren...
		while ($a < count($action)) {
			$key = key($action);
			// nur markieren, was noch frei ist.
			$query = "UPDATE moderation SET c_moderator=$u_id WHERE c_id=" . intval($key) . " AND c_typ='N' AND c_moderator=0";
			$result = mysqli_query($mysqli_link, $query);
			next($action);
			$a++;
		}
		// jetzt die reservierten Aktionen bearbeiten.
		$a = 0;
		reset($action);
		while ($a < count($action)) {
			$key = key($action);
			// nur auswählen, was bereits von diesem Moderator reserviert ist
			$query = "SELECT * FROM moderation WHERE c_id=" . intval($key) . " AND c_typ='N' AND c_moderator=$u_id";
			$result = mysqli_query($mysqli_link, $query);
			if ($result > 0) {
				if (mysqli_num_rows($result) > 0) {
					$f = mysqli_fetch_array($result);
					switch ($action[$key]) {
						case "ok":
						case "clear":
						case "thru":
						// vorbereiten für umspeichern... geht leider nicht 1:1, 
						// weil fetch_array mehr zurückliefert als in $f[] sein darf...
							$c['c_von_user'] = $f['c_von_user'];
							$c['c_an_user'] = $f['c_an_user'];
							$c['c_typ'] = $f['c_typ'];
							$c['c_raum'] = $f['c_raum'];
							$c['c_text'] = $f['c_text'];
							$c['c_farbe'] = $f['c_farbe'];
							$c['c_zeit'] = $f['c_zeit'];
							$c['c_von_user_id'] = $f['c_von_user_id'];
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
							system_msg("", 0, $f['c_von_user_id'],
								$system_farbe, $t['moderiertdel1']);
							system_msg("", 0, $f['c_von_user_id'],
								$system_farbe, "&gt;&gt;&gt; " . $f['c_text']);
							break;
						case "better":
							system_msg("", 0, $f['c_von_user_id'],
								$system_farbe, $t['moderiertdel2']);
							system_msg("", 0, $f['c_von_user_id'],
								$system_farbe, "&gt;&gt;&gt; " . $f['c_text']);
							break;
						case "notime":
							system_msg("", 0, $f['c_von_user_id'],
								$system_farbe, $t['moderiertdel3']);
							system_msg("", 0, $f['c_von_user_id'],
								$system_farbe, "&gt;&gt;&gt; " . $f['c_text']);
							break;
						case "delete":
							system_msg("", 0, $f['c_von_user_id'],
								$system_farbe, $t['moderiertdel4']);
							system_msg("", 0, $f['c_von_user_id'],
								$system_farbe, "&gt;&gt;&gt; " . $f['c_text']);
							break;
					}
					// jetzt noch aus moderierter Tabelle löschen.
					mysqli_free_result($result);
					$query = "DELETE FROM moderation WHERE c_id=" . intval($key) . " AND c_moderator=$u_id";
					$result2 = mysqli_query($mysqli_link, $query);
				} else {
					echo "$t[mod9]<br>";
				}
			}
			next($action);
			$a++;
		}
		echo "</small>";
	}
}

function zeige_moderationstexte($o_raum, $limit = 20) {
	global $t;
	global $id;
	global $mysqli_link;
	global $action;
	global $moderation_rueckwaerts;
	global $moderationsexpire;
	global $u_id;
	global $o_js;
	
	// gegen DAU-Eingaben sichern...
	$limit = max(intval($limit), 20);
	// erst mal alle alten Msgs expiren...
	if ($moderationsexpire == 0)
		$moderationsexpire = 30;
	$expiretime = $moderationsexpire * 60;
	$query = "DELETE from moderation WHERE unix_timestamp(c_zeit)+$expiretime<unix_timestamp(NOW()) AND c_moderator=0 AND c_typ='N'";
	$result = mysqli_query($mysqli_link, $query);
	
	if ($moderation_rueckwaerts == 1)
		$rev = " DESC";
	$query = "SELECT c_id,c_text,c_von_user,c_moderator FROM moderation WHERE c_raum=" . intval($o_raum) . " AND c_typ='N' ORDER BY c_id $rev LIMIT 0, " . intval($limit);
	$result = mysqli_query($mysqli_link, $query);
	$i = 0;
	$rows = 0;
	if ($result > 0) {
		$rows = mysqli_num_rows($result);
		if ($o_js) {
			echo "<SCRIPT LANGUAGE=Javascript>\n";
			echo "function sel() {\n";
			echo "	document.forms['modtext'].elements['ok'].focus();\n";
			echo "}\n";
			echo "</SCRIPT>\n";
		}
		echo "<form name=modtext action=\"moderator.php?id=$id\" method=\"post\">\n";
		if ($rows > 0) {
			
			echo "<table style=\"width=100%;\">\n";
			echo "<tr>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"pics/ok.gif\" height=20 width=20 alt=\"" . $t['mod16'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"pics/nope.gif\" height=20 width=20 alt=\"" . $t['mod17'] . "\"></td>";
			echo "<td style=\"vertical-align:bottom;\"  class=\"tabelle_kopfzeile\">";
			echo "<table style=\"width=100%;\"><tr><td>";
			echo "<small><b>" . $t['mod2'];
			echo "</td><td align=right>";
			echo "<input type=submit name=ok2 value=\"go!\">\n";
			echo "</b></small>";
			echo "</td></tr></table>";
			echo "</td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"pics/ok.gif\" height=20 width=20 alt=\"" . $t['mod14'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"pics/wdh.gif\" height=20 width=20 alt=\"" . $t['mod3'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"pics/smile.gif\" height=20 width=20 alt=\"" . $t['mod4'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"pics/time.gif\" height=20 width=20 alt=\"" . $t['mod5'] . "\"></td>";
			echo "<td align=center style=\"vertical-align:bottom;\" class=\"tabelle_kopfzeile\"><img src=\"pics/nope.gif\" height=20 width=20 alt=\"" . $t['mod15'] . "\"></td>";
			echo "</tr>\n";
			
			while ($row = mysqli_fetch_object($result)) {
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
				if ($row->c_moderator == $u_id || $row->c_moderator == 0) {
					echo "<input type=radio name=action[$row->c_id] value='ok' onclick=submit();";
					if ($row->c_moderator == $u_id)
						echo " checked";
					echo ">";
					$moderatorenfarbe = 'class="moderator_schwarz"';
				} else {
					echo "&nbsp";
					$moderatorenfarbe = 'class="moderator_grau"';
				}
				echo "</small></td>";
				echo "<td align=center $moderatorG><small>";
				if ($row->c_moderator == $u_id) {
					echo "<input type=radio name=action[$row->c_id] value='clear' onclick=submit();>";
					$b1 = "<b>";
					$b2 = "</b>";
				} else {
					$b1 = "";
					$b2 = "";
				}
				echo "&nbsp;</small></td>";
				echo "<td width=100% $bgcolor $moderatorenfarbe><small>$b1$row->c_von_user: $row->c_text$b2</small></td>";
				if ($row->c_moderator == $u_id || $row->c_moderator == 0) {
					echo "<td align=center $moderatorG><small><input type=radio name=action[$row->c_id] value='thru';></small></td>";
					echo "<td align=center $moderatorR><small><input type=radio name=action[$row->c_id] value='notagain';></small></td>";
					echo "<td align=center $moderatorR><small><input type=radio name=action[$row->c_id] value='better';></small></td>";
					echo "<td align=center $moderatorR><small><input type=radio name=action[$row->c_id] value='notime';></small></td>";
					echo "<td align=center $moderatorR><small><input type=radio name=action[$row->c_id] value='delete';></small></td>";
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
		echo "<input type=submit name=ok value=" . $t['mod_ok'] . ">\n";
		echo "</center>";
		echo "</form>\n";
	}
	return $rows;
}

function anzahl_moderationstexte($o_raum)
{
	global $id;
	global $mysqli_link;
	
	$query = "SELECT c_id FROM moderation WHERE c_raum=" . intval($o_raum) . " AND c_typ='N' ORDER BY c_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result > 0) {
		$rows = mysqli_num_rows($result);
	}
	;
	return $rows;
}

?>