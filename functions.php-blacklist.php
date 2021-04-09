<?php

function zeige_blacklist($aktion, $zeilen, $sort) {
	// Zeigt Liste der Blacklist an
	
	global $id, $mysqli_link, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $mysqli_link, $u_nick, $u_id, $t;
	global $blacklistmaxdays;
	
	$blurl = $PHP_SELF . "?id=$id&http_host=$http_host&aktion=&sort=";
	
	switch ($sort) {
		case "f_zeit desc":
			$qsort = $sort . ",u_nick";
			$fsort = "f_zeit";
			$usort = "u_nick";
			break;
		
		case "f_zeit":
			$qsort = $sort . ",u_nick";
			$fsort = "f_zeit+desc";
			$usort = "u_nick";
			break;
		
		case "u_nick desc":
			$qsort = $sort . ",f_zeit desc";
			$fsort = "f_zeit+desc";
			$usort = "u_nick";
			break;
		
		case "u_nick":
			$qsort = $sort . ",f_zeit desc";
			$fsort = "f_zeit+desc";
			$usort = "u_nick+desc";
			break;
		
		default:
			$qsort = "f_zeit desc, u_nick";
			$fsort = "f_zeit";
			$usort = "u_nick";
	}
	
	switch ($aktion) {
		case "normal":
		default;
			$query = "SELECT u_nick,f_id,f_text,f_userid,f_blacklistid,"
				. "date_format(f_zeit,'%d.%m.%y %H:%i') as zeit "
				. "from blacklist left join user on f_blacklistid=u_id "
				. "order by $qsort";
			$button = "LÖSCHEN";
			
			// blacklist-expire
			if ($blacklistmaxdays) {
				$query2 = "DELETE FROM blacklist WHERE (TO_DAYS(NOW()) - TO_DAYS(f_zeit)>$blacklistmaxdays) ";
				mysqli_query($mysqli_link, $query2);
			}
	}
	
	$result = mysqli_query($mysqli_link, $query);
	if ($result) {
		$text = '';
		
		$text .= "<form name=\"blacklist_loeschen\" action=\"$PHP_SELF\" method=\"POST\">\n"
			. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
			. "<input type=\"hidden\" name=\"aktion\" value=\"loesche\">\n"
			. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n"
			. "<table style=\"width:100%;\">";
		
		$anzahl = mysqli_num_rows($result);
		if ($anzahl == 0) {
			// Keine Blacklist-Einträge
			$box = $t['blacklist1'];
			
			$text .= "<tr><td class=\"tabelle_zeile1\">&nbsp;</td><td style=\"text-align:left;\" class=\"tabelle_zeile1\">Es sind keine Blacklist-Einträge vorhanden.</td></tr>";
			
		} else {
			// Blacklist anzeigen
			$box = $t['blacklist2'] . $anzahl;
			
			$text .= "<tr><td style=\"width:5%;\">" . $f1 . "Löschen" . $f2
				. "</td><td style=\"width:35%;\">" . $f1 . "<a href=\"" . $blurl
				. $usort . "\">Nickname</a>" . $f2 . "</td>"
				. "<td style=\"width:35%;\">" . $f1 . "Info" . $f2 . "</td>"
				. "<td style=\"width:13%;\" style=\"text-align:center;\">" . $f1 . "<a href=\""
				. $blurl . $fsort . "\">Datum&nbsp;Eintrag</a>" . $f2
				. "</td>\n" . "<td style=\"width:13%;\" style=\"text-align:center;\">" . $f1
				. "Eintrag&nbsp;von" . $f2 . "</td></tr>\n";
			
			$i = 0;
			$bgcolor = 'class="tabelle_zeile1"';
			while ($row = mysqli_fetch_object($result)) {
				
				// User aus DB lesen
				$query = "SELECT u_nick,u_id,u_level,u_punkte_gesamt,u_punkte_gruppe,o_id,"
					. "date_format(u_login,'%d.%m.%y %H:%i') as login, "
					. "UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_login) AS online "
					. "from user left join online on o_user=u_id "
					. "WHERE u_id=$row->f_blacklistid ";
				$result2 = mysqli_query($mysqli_link, $query);
				if ($result2 && mysqli_num_rows($result2) > 0) {
					
					// User gefunden -> Ausgeben
					$row2 = mysqli_fetch_object($result2);
					$blacklist_nick = "<b>"
						. user($row2->u_id, $row2, TRUE, FALSE) . "</b>";
					
				} else {
					
					// User nicht gefunden, Blacklist-Eintrag löschen
					$blacklist_nick = "NOBODY";
					$query = "DELETE from blacklist WHERE f_id=$row->f_id";
					$result2 = mysqli_query($mysqli_link, $query);
					
				}
				
				// Unterscheidung online ja/nein, Text ausgeben
				if ($row2->o_id) {
					$txt = $blacklist_nick . "<br>online&nbsp;"
						. gmdate("H:i:s", $row2->online) . "&nbsp;Std/Min/Sek";
					$auf = "<b>" . $f1;
					$zu = $f2 . "</b>";
				} else {
					$txt = $blacklist_nick . "<br>Letzter&nbsp;Login:&nbsp;"
						. str_replace(" ", "&nbsp;", $row2->login);
					$auf = $f1;
					$zu = $f2;
				}
				
				// Infotext setzen
				if ($row->f_text == "") {
					$infotext = "&nbsp;";
				} else {
					$infotext = $row->f_text;
				}
				
				// Nick des Admins (Eintrager) setzen
				$f_userid = $row->f_userid;
				if ($f_userid && $f_userid != "NULL") {
					$admin_nick = user($f_userid, "", FALSE, FALSE);
				} else {
					$admin_nick = "";
				}
				
				$text .= "<tr><td style=\"text-align:center;\" $bgcolor>" . $auf
					. "<input type=\"checkbox\" name=\"f_blacklistid[]\" value=\""
					. $row->f_blacklistid . "\"></td>"
					. "<input type=\"hidden\" name=\"f_nick[]\" value=\""
					. $row2->u_nick . "\"></td>" . "<td $bgcolor>" . $auf . $txt . $zu
					. "</td>" . "<td $bgcolor>" . $auf . $infotext . $zu . "</td>"
					. "<td style=\"text-align:center;\" $bgcolor>" . $auf . $row->zeit . $zu
					. "</td>" . "<td style=\"text-align:center;\" $bgcolor>" . $auf . $admin_nick
					. $zu . "</td>" . "</tr>\n";
				
				if (($i % 2) > 0) {
					$bgcolor = 'class="tabelle_zeile1"';
				} else {
					$bgcolor = 'class="tabelle_zeile2"';
				}
				$i++;
			}
			
			$text .= "<tr><td $bgcolor colspan=\"2\"><input type=\"checkbox\" onClick=\"toggle(this.checked)\">"
				. $f1 . " Alle Auswählen" . $f2 . "</td>\n"
				. "<td style=\"text-align:right;\" $bgcolor colspan=\"3\">" . $f1
				. "<input type=\"submit\" name=\"los\" value=\"$button\">"
				. $f2 . "</td></tr>\n";
		}
		
		$text .= "</table></form>\n";
		
		// Box anzeigen
		show_box_title_content($box, $text);
	}
}

function loesche_blacklist($f_blacklistid) {
	// Löscht Blacklist-Eintrag aus der Tabelle mit f_blacklistid
	// $f_blacklistid User-ID des Blacklist-Eintrags
	
	global $id, $http_host, $mysqli_link, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $dbase, $mysqli_link;
	global $u_id, $u_nick, $admin;
	
	if (!$admin || !$f_blacklistid) {
		echo "Fehler beim Löschen des Blacklist-Eintrags $f_blacklistid!<br>";
		return (0);
	}
	
	$f_blacklistid = intval($f_blacklistid);
	
	$query = "DELETE from blacklist WHERE f_blacklistid=$f_blacklistid";
	$result = mysqli_query($mysqli_link, $query);
	
	$query = "SELECT `u_nick` FROM `user` WHERE `u_id`=$f_blacklistid";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$f_nick = mysqli_result($result, 0, 0);
		echo "<P><b>Hinweis:</b> '$f_nick' ist nicht mehr in der Blackliste eingetragen.</P>";
	}
	@mysqli_free_result($result);
}

function formular_neuer_blacklist($neuer_blacklist) {
	// Gibt Formular für Nicknamen zum Hinzufügen als Blacklist-Eintrag aus
	
	global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
	
	if (!$eingabe_breite) {
		$eingabe_breite = 30;
	}
	
	$box = "Neuen Blacklist-Eintrag hinzufügen:";
	$text = '';
	
	$text .= "<form name=\"blacklist_neu\" action=\"$PHP_SELF\" method=\"POST\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"neu2\">\n"
		. "<input type=\"hidden\" name=\"http_host\" value=\"$http_host\">\n"
		. "<table style=\"width:100%;\">";
	
	if (!isset($neuer_blacklist['u_nick'])) {
		$neuer_blacklist['u_nick'] = "";
	}
	if (!isset($neuer_blacklist['f_text'])) {
		$neuer_blacklist['f_text'] = "";
	}
	
	$text .= "<tr><td style=\"text-align:right; font-weight:bold;\" class=\"tabelle_zeile1\">"."Nickname:</td>"
			. "<td class=\"tabelle_zeile1\">" . $f1 . "<input type=\"text\" name=\"neuer_blacklist[u_nick]\" value=\"" . htmlspecialchars($neuer_blacklist['u_nick']) . "\" size=20>" . $f2 . "</td></tr>\n"
			. "<tr><td style=\"text-align:right; font-weight:bold;\" class=\"tabelle_zeile1\">Infotext:</td>"
			. "<td class=\"tabelle_zeile1\">" . $f1 . "<input type=\"text\" name=\"neuer_blacklist[f_text]\" value=\"" . htmlspecialchars($neuer_blacklist['f_text'])
			. "\" size=$eingabe_breite>" . "&nbsp;"
			. "<input type=\"submit\" name=\"los\" value=\"Eintragen\">" . $f2
			. "</td></tr>\n" . "</table></form>\n";
	
	// Box anzeigen
	show_box_title_content($box, $text);
}

function neuer_blacklist($f_userid, $blacklist) {
	// Trägt neuen Blacklist-Eintrag in der Datenbank ein
	
	global $id, $http_host, $eingabe_breite, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $dbase;
	
	if (!$blacklist['u_id'] || !$f_userid) {
		echo "Fehler beim Anlegen des Blacklist-Eintrags: $f_userid,$blacklist[u_id]!<br>";
	} else {
		
		$blacklist['u_id'] = mysqli_real_escape_string($mysqli_link, $blacklist['u_id']); // sec
		$f_userid = mysqli_real_escape_string($mysqli_link, $f_userid); // sec
		
		// Prüfen ob Blacklist-Eintrag bereits in Tabelle steht
		$query = "SELECT f_id from blacklist WHERE "
			. "(f_userid=$blacklist[u_id] AND f_blacklistid=$f_userid) "
			. "OR " . "(f_userid=$f_userid AND f_blacklistid=$blacklist[u_id])";
		
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			
			echo "<P><b>Fehler:</b> '$blacklist[u_nick]' ist bereits in der Blackliste eingetragen!</P>\n";
			
		} elseif ($blacklist['u_id'] == $f_userid) {
			
			// Eigener Blacklist-Eintrag ist verboten
			echo "<P><b>Fehler:</b> Sie können sich nicht selbst als Blacklist-Eintrag hinzufügen!</P>\n";
		} else {
			
			// User ist noch kein Blacklist-Eintrag -> hinzufügen
			$f['f_userid'] = $f_userid;
			$f['f_blacklistid'] = $blacklist['u_id'];
			$f['f_text'] = htmlspecialchars($blacklist['f_text']);
			schreibe_db("blacklist", $f, 0, "f_id");
			
			echo "<P><b>Hinweis:</b> '$blacklist[u_nick]' ist jetzt in der Blacklist eingetragen.</P>";
			
		}
		
	}
}
?>