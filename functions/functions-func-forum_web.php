<?php
//Eingabemaske für neues Forum
function maske_forum($fo_id = 0) {
	global $id, $t;
	
	if ($fo_id > 0) {
		$fo_id = intval($fo_id);
		$sql = "SELECT fo_name, fo_admin FROM `forum_kategorien` where fo_id=$fo_id";
		$query = sqlQuery($sql);
		$fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
		$fo_admin = mysqli_result($query, 0, "fo_admin");
		mysqli_free_result($query);
		
		$kopfzeile = str_replace("xxx", $fo_name, $t['kategorie_editieren_mit_Name']);
		$button = $t['kategorie_editieren'];
	} else {
		$kopfzeile = $t['kategorie_anlegen'];
		$button = $t['kategorie_anlegen'];
		
	}
	
	$zaehler = 0;
	
	$text = "";
	$text .= "<a href=\"forum.php?aktion=suche&id=$id\" class=\"button\" title=\"$t[button_suche]\"><span class=\"fa fa-search icon16\"></span> <span>$t[button_suche]</span></a><br><br>\n";
	$text .= "<table style=\"width:100%;\">\n";
	$text .= "<form action=\"forum.php\" method=\"post\">\n";
	
	// Überschrift: Neue Kategorie anzeigen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $kopfzeile, "", "", 0, "70", "");
	
	// Name der Kategorie
	$text .= zeige_formularfelder("input", $zaehler, $t['kategorie_name'], "fo_name",  $fo_name);
	$zaehler++;
	
	// Gäste dürfen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	
	if (($fo_admin & 8) == 8) {
		$selg1 = "selected";
		$selg2 = "";
	}
	if (($fo_admin & 16) == 16) {
		$selg1 = "";
		$selg2 = "selected";
	}
	
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $t['forum_berechtigungen_gaeste_duerfen'] . "</td>\n";
	$text .= "<td $bgcolor>\n";
	$text .= "<select size=\"1\" name=\"fo_gast\">\n";
	$text .= "<option value=\"0\">$t[forum_berechtigungen_weder_lesen_noch_schreiben]</option>\n";
	$text .= "<option value=\"8\" $selg1>$t[forum_berechtigungen_nur_lesen]</option>\n";
	$text .= "<option value=\"24\" $selg2>$t[forum_berechtigungen__lesen_noch_schreiben]</option>\n";
	$text .= "</select>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	// Benutzer dürfen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	
	if (($fo_admin & 2) == 2) {
		$selu1 = "selected";
		$selu2 = "";
	}
	if (($fo_admin & 4) == 4) {
		$selu1 = "";
		$selu2 = "selected";
	}
	
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $t['forum_berechtigungen_benutzer_duerfen'] . "</td>\n";
	$text .= "<td $bgcolor>\n";
	$text .= "<select size=\"1\" name=\"fo_user\">\n";
	$text .= "<option value=\"0\">$t[forum_berechtigungen_weder_lesen_noch_schreiben]</option>\n";
	$text .= "<option value=\"2\" $selu1>$t[forum_berechtigungen_nur_lesen]</option>\n";
	$text .= "<option value=\"6\" $selu2>$t[forum_berechtigungen__lesen_noch_schreiben]</option>\n";
	$text .= "</select>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	// Neue Kategorie anlegen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$button\"></td>\n";
	$text .= "</tr>\n";
	
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	if ($fo_id > 0) {
		$text .= "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"forum_editieren\">\n";
	} else {
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"forum_anlegen\">\n";
	}
	
	$text .= "</table>\n";
	$text .= "</form>\n";
	
	
	return $text;
}

// Listet alle Foren mit den Anzahl Themen auf
function forum_liste() {
	global $id, $forum_admin, $chat_grafik, $t, $u_level;
	
	$sql = "SELECT fo_id, fo_name, fo_order, fo_admin, th_id, th_fo_id, th_name, th_desc, th_anzthreads, th_anzreplys, th_order, th_postings FROM `forum_kategorien`, `forum_foren` WHERE fo_id = th_fo_id ";
	if ($u_level == "G") {
		$sql .= "AND ( ((fo_admin & 8) = 8) OR fo_admin = 0) ";
	}
	if ($u_level == "U" || $u_level == "A" || $u_level == "M" || $u_level == "Z") {
		$sql .= "AND ( ((fo_admin & 2) = 2) OR fo_admin = 0) ";
	}
	$sql .= "ORDER BY fo_order, th_order";
	$query = sqlQuery($sql);
	
	//fo_id merken zur Darstellnug des Kopfes
	$fo_id_last = 0;
	$zeile = 0;
	if ($query && mysqli_num_rows($query) > 0) {
		$text = "";
		
		if ($forum_admin && !$aktion) {
			$text .= "<a href=\"forum.php?id=$id&aktion=forum_neu\" class=\"button\" title=\"$t[kategorie_anlegen]\"><span class=\"fa fa-plus icon16\"></span> <span>$t[kategorie_anlegen]</span></a>\n";
		}
		$text .= "<a href=\"forum.php?aktion=suche&id=$id\" class=\"button\" title=\"$t[button_suche]\"><span class=\"fa fa-search icon16\"></span> <span>$t[button_suche]</span></a>\n";
		
		
		$text .= "<table style=\"width:100%\">\n";
		while ($thema = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
			//Neue Kategorie?
			if ($fo_id_last != $thema['fo_id']) {
				$text .= "<tr>\n";
				$text .= "<td colspan=\"6\">&nbsp;</td>";
				$text .= "</tr>\n";
				
				$text .= "<tr>\n";
				$text .= "<td colspan=\"2\" class =\"tabelle_kopfzeile\">&nbsp;&nbsp;&nbsp;" .htmlspecialchars($thema['fo_name']) . "<a name=\"" . $thema['fo_id'] . "\"></a></td>\n";
				if ($forum_admin) {
					$text .= "<td class =\"tabelle_kopfzeile\" style=\"width:300px; text-align:right; vertical-align:middle;\">\n";
					$text .= "<a href=\"forum.php?id=$id&aktion=forum_edit&fo_id=$thema[fo_id]\" class=\"button\" title=\"$t[button_editieren]\"><span class=\"fa fa-pencil icon16\"></span> <span>$t[button_editieren]</span></a>\n";
					$text .= "<a href=\"forum.php?id=$id&aktion=forum_delete&fo_id=$thema[fo_id]\" onClick=\"return ask('$t[kategorie_loeschen]')\" class=\"button\" title=\"$t[button_loeschen]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[button_loeschen]</span></a>\n";
					$text .= "<a href=\"forum.php?id=$id&fo_id=$thema[fo_id]&aktion=thema_neu\" class=\"button\" title=\"$t[button_neues_forum]\"><span class=\"fa fa-plus icon16\"></span> <span>$t[button_neues_forum]</span></a>\n";
					$text .= "</td>\n";
					
					$text .= "<td class =\"tabelle_kopfzeile\" style=\"width:50px; text-align:center;\">\n";
					$text .= "<a href=\"forum.php?id=$id&fo_id=$thema[fo_id]&fo_order=$thema[fo_order]&aktion=forum_up\" class=\"button\"><span class=\"fa fa-arrow-up icon16\"></span></a><br>\n";
					$text .= "<br>\n";
					$text .= "<a href=\"forum.php?id=$id&fo_id=$thema[fo_id]&fo_order=$thema[fo_order]&aktion=forum_down\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a>\n";
					$text .= "</td>\n";
				} else {
					$text .= "<td class=\"tabelle_kopfzeile\" style=\"width:300px;\">&nbsp;</td>";
					$text .= "<td class=\"tabelle_kopfzeile\" style=\"width:50px;\">&nbsp;</td>";
				}
				$text .= "<td class=\"tabelle_kopfzeile\" style=\"width:120px; text-align:center;\">&nbsp;</td>\n";
				$text .= "</tr>\n";
			}
			
			if ($zeile % 2) {
				$farbe = 'class="tabelle_zeile1"';
			} else {
				$farbe = 'class="tabelle_zeile2"';
			}
			if ($thema['th_name'] != "dummy-thema") {
				if ($thema['th_postings']) {
					$arr_posting = explode(",", $thema['th_postings']);
				} else {
					$arr_posting = array();
				}
				$ungelesene = anzahl_ungelesener_themen($arr_posting, $thema['th_id']);
				
				$text .= "<tr>";
				
				if ($ungelesene == 0) {
					$folder = $chat_grafik['forum_ordnerneu'];
				} else if ($ungelesene < 11) {
					$folder = $chat_grafik['forum_ordnerblau'];
				} else {
					$folder = $chat_grafik['forum_ordnervoll'];
				}
				
				$text .= "<td style=\"width:50px; text-align:center;\" $farbe>$folder</td>";
				if ($forum_admin) {
					$text .= "<td style=\"font-weight:bold; padding-left:10px;\" $farbe>\n";
					$text .= "<a href=\"forum.php?id=$id&th_id=$thema[th_id]&aktion=show_thema\">\n";
					$text .= htmlspecialchars($thema['th_name']) . "</a><br><span class=\"smaller\"> " . htmlspecialchars($thema['th_desc']) . "</span>\n";
					$text .= "</td>\n";
					
					$text .= "<td style=\"width:170px; text-align:center; vertical-align:middle;\" $farbe>\n";
					$text .= "<a href=\"forum.php?id=$id&th_id=$thema[th_id]&aktion=thema_edit\" class=\"button\" title=\"$t[button_editieren]\"><span class=\"fa fa-pencil icon16\"></span> <span>$t[button_editieren]</span></a>\n";
					$text .= "<a href=\"forum.php?id=$id&aktion=thema_delete&th_id=$thema[th_id]\" onClick=\"return ask('$t[forum_loeschen]')\" class=\"button\" title=\"$t[button_loeschen]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[button_loeschen]</span></a>\n";
					$text .= "</td>\n";
					
					$text .= "<td style=\"text-align:center;\" $farbe>\n";
					$text .= "<a href=\"forum.php?id=$id&th_id=$thema[th_id]&fo_id=$thema[fo_id]&th_order=$thema[th_order]&aktion=thema_up\" class=\"button\"><span class=\"fa fa-arrow-up icon16\"></span></a><br>\n";
					$text .= "<br>\n";
					$text .= "<a href=\"forum.php?id=$id&th_id=$thema[th_id]&fo_id=$thema[fo_id]&th_order=$thema[th_order]&aktion=thema_down\" class=\"button\"><span class=\"fa fa-arrow-down icon16\"></span></a>\n";
					$text .= "</td>\n";
				} else {
					$text .= "<td style=\"font-weight:bold; padding-left:10px;\" $farbe>\n";
					$text .= "<a href=\"forum.php?id=$id&th_id=$thema[th_id]&aktion=show_thema\">" . htmlspecialchars($thema['th_name']) . "</a><br>\n";
					$text .= "<span class=\"smaller\"> " . htmlspecialchars($thema['th_desc']) . "</span></td>\n";
					
					$text .= "<td $farbe>&nbsp;</td>";
					$text .= "<td $farbe>&nbsp;</td>";
				}
				$text .= "<td style=\"padding-left:10px;\" $farbe>";
				$text .= "<span class=\"fa fa-comment icon16\"></span> $thema[th_anzthreads] $t[kategorie_themen]<br>";
				$text .= "<span class=\"fa fa-pencil icon16\"></span> " . ( $thema['th_anzreplys'] + $thema['th_anzthreads']) . " $t[kategorie_beitraege]";
				$text .= "</td>\n";
				$text .= "</tr>\n";
			}
			
			$fo_id_last = $thema['fo_id'];
			$zeile++;
		}
		$text .= "</table>\n";
		$text .= show_icon_description();
	}
	
	mysqli_free_result($query);
	
	return $text;
}

//Zeigt Erklärung der verschiedenen Folder an
function show_icon_description() {
	global $t, $chat_grafik;
	
	$text = "<br>\n";
	$text .= "<div style=\"margin-left:5px;\">\n";
	$text .= "$chat_grafik[forum_ordnerneu] = $t[desc_folder]<br>\n";
	$text .= "$chat_grafik[forum_ordnerblau] = $t[desc_redfolder] ($chat_grafik[forum_ordnervoll] = $t[desc_burningredfolder])<br>\n";
	$text .= "$chat_grafik[forum_topthema] = $t[desc_topposting]<br>\n";
	$text .= "$chat_grafik[forum_threadgeschlossen] = $t[desc_threadgeschlossen]\n";
	$text .= "</div>\n";
	
	return $text;
}

//Eingabemaske für Thema
function maske_thema($th_id = 0) {
	global $id, $fo_id;
	global $t;
	
	if ($th_id > 0) {
		$sql = "SELECT th_name, th_desc FROM `forum_foren` WHERE th_id=" . intval($th_id);
		$query = sqlQuery($sql);
		$th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
		$th_desc = htmlspecialchars(mysqli_result($query, 0, "th_desc"));
		mysqli_free_result($query);
		
		$button = $t['forum_speichern'];
	} else {
		$button = $t['forum_anlegen'];
	}
	
	if (!isset($th_name)) {
		$th_name = "";
		$kopfzeile = $t['forum_anlegen'];
	} else {
		$kopfzeile = str_replace("xxx", $th_name, $t['forum_editieren_mit_Name']);
	}
	
	if (!isset($th_desc)) {
		$th_desc = "";
	}
	
	$zaehler = 0;
	$text = "<table style=\"width:100%;\">\n";
	$text .= "<form action=\"forum.php\" method=\"post\">\n";
	
	// Überschrift: Neue Kategorie anlegen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $kopfzeile, "", "", 0, "70", "");
	
	// Name des Forums
	$text .= zeige_formularfelder("input", $zaehler, $t['forum_name'], "th_name",  $th_name);
	$zaehler++;
	
	// Beschreibung des Forums
	$text .= zeige_formularfelder("textarea", $zaehler, $t['forum_beschreibung'], "th_desc", $th_desc);
	$zaehler++;
	
	// Forum verschieben
	if ($th_id > 0) {
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$selectbox = "<input type=\"checkbox\" name=\"th_forumwechsel\" value=\"Y\">\n";
		
		$sql = "SELECT fo_id, fo_name FROM `forum_kategorien` ORDER BY fo_order ";
		$query = sqlQuery($sql);
		
		$selectbox .= "<select name=\"th_verschiebe_nach\" size=\"1\">\n";
		while ($row = mysqli_fetch_object($query)) {
			$selectbox .= "<option ";
			if ($row->fo_id == $fo_id) {
				$selectbox .= "selected ";
			}
			$selectbox .= "value=\"$row->fo_id\">$row->fo_name</option>";
		}
		$selectbox .= "</select>\n";
		
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $t['forum_verschieben'] . "</td>\n";
		$text .= "<td $bgcolor>$selectbox</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
		mysqli_free_result($query);
	}
	
	
	//Forum speichern
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$button\"></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
		
	$text .= "</table>\n";
	if ($th_id > 0) {
		$text .= "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"thema_editieren\">\n";
	} else {
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"thema_anlegen\">\n";
	}
	$text .= "</form>\n";
	
	
	return $text;
}

//Zeigt Pfad und Seiten in Themaliste an
function show_pfad($th_id, $fo_id, $fo_name, $th_name, $th_anzthreads) {
	global $id, $anzahl_po_seite, $seite, $t;
	
	$text = "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"smaller\"><a href=\"forum.php?id=$id#$fo_id\">$fo_name</a> > <a href=\"forum.php?id=$id&th_id=$th_id&aktion=show_thema&seite=$seite\">$th_name</a></td>\n";
	
	if (!$anzahl_po_seite || $anzahl_po_seite == 0) {
		$anzahl_po_seite = 20;
	}
	$anz_seiten = ceil(($th_anzthreads / $anzahl_po_seite));
	if ($anz_seiten > 1) {
			$text .= "<td style=\"text-align:right;\" class=\"smaller\">$t[page] ";
			for ($page = 1; $page <= $anz_seiten; $page++) {
				if ($page == $seite) {
					$col = "font-weight:bold;";
				} else {
					$col = '';
				}
				$text .= "<a href=\"forum.php?id=$id&th_id=$th_id&aktion=show_thema&seite=$page\"><span style=\"$col;\">$page</span></a> ";
			}
			$text .= "</td></tr>\n";
	} else {
		$text .= "</tr>\n";
	}
	$text .= "</table>\n";
	
	return $text;
}

//Zeigt ein Thema mit allen Beiträgen an
function show_thema() {
	global $id, $forum_admin, $th_id, $seite;
	global $anzahl_po_seite, $chat_grafik, $t;
	global $admin, $anzahl_po_seite2, $u_id, $locale;
	
	if ($anzahl_po_seite2) {
		$anzahl_po_seite2 = preg_replace("/[^0-9]/", "", $anzahl_po_seite2);
		$anzahl_po_seite = $anzahl_po_seite2;
		
		$f[u_forum_postingproseite] = $anzahl_po_seite2;
		
		if (!schreibe_db("user", $f, $u_id, "u_id")) {
			echo "Fehler beim Schreiben in DB!";
		}
		
	} else {
		$query = "SELECT `u_forum_postingproseite` FROM `user` WHERE `u_id` = '$u_id'";
		$result = sqlQuery($query);
		$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
		$anzahl_po_seite2 = $a['u_forum_postingproseite'];
		$anzahl_po_seite = $anzahl_po_seite2;
	}
	
	$leserechte = pruefe_leserechte($th_id);
	
	if (!$leserechte) {
		echo $t['leserechte'];
		exit;
	}
	if (!$seite) {
		$seite = 1;
	}
	
	$offset = ($seite - 1) * $anzahl_po_seite;
	
	$sql = "SET lc_time_names = '$locale'";
	$query = sqlQuery($sql);
	
	// Erklärung zu den Datumsformatierungen: https://www.w3schools.com/sql/func_mysql_date_format.asp
	$sql = "SELECT po_id, po_u_id, date_format(from_unixtime(po_ts), '%d. %M %Y') AS po_date, date_format(from_unixtime(po_threadts), '%d. %M %Y') AS po_date2,
				po_titel, po_threadorder, po_topposting, po_threadgesperrt, po_gesperrt, u_nick,
		u_level, u_punkte_gesamt, u_punkte_gruppe, u_punkte_anzeigen, u_chathomepage FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id
				WHERE po_vater_id = 0 AND po_th_id = " . intval($th_id) . " ORDER BY po_topposting desc, po_threadts desc, po_ts DESC LIMIT $offset, $anzahl_po_seite";
	
	$query = sqlQuery($sql);
	
	//Infos über Forum und Thema holen
	$sqlForum = "SELECT fo_id, fo_name, th_name, th_anzthreads FROM `forum_kategorien`, `forum_foren` WHERE th_id = " . intval($th_id) . " AND fo_id = th_fo_id";
	$queryForum = sqlQuery($sqlForum);
	$fo_id = htmlspecialchars(mysqli_result($queryForum, 0, "fo_id"));
	$fo_name = htmlspecialchars(mysqli_result($queryForum, 0, "fo_name"));
	$th_name = htmlspecialchars(mysqli_result($queryForum, 0, "th_name"));
	$th_anzthreads = mysqli_result($queryForum, 0, "th_anzthreads");
	mysqli_free_result($queryForum);
	
	$text = "";
	
	$text .= "<a href=\"forum.php?aktion=suche&id=$id\" class=\"button\" title=\"$t[button_suche]\"><span class=\"fa fa-search icon16\"></span> <span>$t[button_suche]</span></a><br><br>\n";
	
	$text .= show_pfad($th_id, $fo_id, $fo_name, $th_name, $th_anzthreads);
	
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"5\" class=\"tabelle_kopfzeile\">$th_name</td>\n";
	$text .= "<td style=\"text-align:right;\" class=\"tabelle_kopfzeile\">\n";
	
	$schreibrechte = pruefe_schreibrechte($th_id);
	if ($schreibrechte) {
		$text .= "<a href=\"forum.php?id=$id&th_id=$th_id&po_vater_id=0&aktion=thread_neu\" class=\"button\" title=\"$t[thema_erstellen]\"><span class=\"fa fa-plus icon16\"></span> <span>$t[thema_erstellen]</span></a>\n";
	} else {
		$text .= "<span class=\"smaller\">$t[nur_leserechte]</span><br>\n";
	}
	$text .= "<a href=\"forum.php?id=$id&th_id=$th_id&aktion=thema_alles_gelesen\" class=\"button\" title=\"$t[forum_alle_themen_als_gelesen_markieren]\"><span class=\"fa fa-check icon16\"></span> <span>$t[forum_alle_themen_als_gelesen_markieren]</span></a>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$t[geschrieben_von]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$t[forum_thema_erstellt_am]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$t[forum_anzahl_antworten]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$t[forum_letzte_Antwort]</td>\n";
	$text .= "</tr>\n";
	
	$zeile = 0;
	while ($posting = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		set_time_limit(0);
		
		if ($zeile % 2) {
			$farbe = 'class="tabelle_zeile1"';
		} else {
			$farbe = 'class="tabelle_zeile2"';
		}
		
		if ($posting['po_threadorder'] == "0") {
			$anzreplys = 0;
			$arr_postings = array($posting['po_id']);
		} else {
			$arr_postings = explode(",", $posting['po_threadorder']);
			$anzreplys = count($arr_postings);
			//Ersten Beitrag mit beruecksichtigen
			$arr_postings[] = $posting['po_id'];
		}
		
		$ungelesene = anzahl_ungelesene($arr_postings, $th_id);
		
		array_pop($arr_postings);
		
		if ($ungelesene === 0) {
			if ($posting['po_topposting'] == 1) { // Top Posting 
				$folder = $chat_grafik['forum_topthema'];
			} else if ($posting['po_threadgesperrt'] == 1) { // Geschlossenes Thema
				$folder = $chat_grafik['forum_threadgeschlossen'];
			} else {
				$folder = $chat_grafik['forum_ordnerneu'];
			}
		} else if ($ungelesene < 11) {
			$folder = $chat_grafik['forum_ordnerblau'];
		} else {
			$folder = $chat_grafik['forum_ordnervoll'];
		}
		
		if ($ungelesene != 0) {
			$coli = "<span style=\"#ff0000\">";
			$colo = "</span>";
		} else {
			$coli = "";
			$colo = "";
		}
		
		$text .= "<tr><td style=\"text-align:center;\" $farbe>$folder</nobr></td>\n";
			
		if ($posting['po_gesperrt'] == 1 && !$forum_admin) {
			$text .= "<td $farbe style=\"font-weight:bold; font-size: smaller;\">&nbsp;"
				. substr($posting['po_titel'], 0, 40) . " <span style=\"color:#ff0000; \">(gesperrt)</span></td>\n";
		} else if ($posting['po_gesperrt'] == 1 && $forum_admin) {
			$text .= "<td $farbe style=\"font-weight:bold;\" class=\"smaller\">&nbsp;<a href=\"forum.php?id=$id&th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">"
				. substr($posting['po_titel'], 0, 40) . "</a> <span style=\"color:#ff0000; \">(gesperrt)</span></td>\n";
		} else {
			$text .= "<td $farbe  class=\"smaller\">&nbsp;<b><a href=\"forum.php?id=$id&th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">" . substr($posting['po_titel'], 0, 40) . "</a></b></td>\n";
		}
		
		if (!$posting['u_nick']) {
			$text .= "<td $farbe><span class=\"smaller\"><b>Nobody</b></span></td>\n";
		} else {
			$userdata = array();
			$userdata['u_id'] = $posting['po_u_id'];
			$userdata['u_nick'] = $posting['u_nick'];
			$userdata['u_level'] = $posting['u_level'];
			$userdata['u_punkte_gesamt'] = $posting['u_punkte_gesamt'];
			$userdata['u_punkte_gruppe'] = $posting['u_punkte_gruppe'];
			$userdata['u_punkte_anzeigen'] = $posting['u_punkte_anzeigen'];
			$userdata['u_chathomepage'] = $posting['u_chathomepage'];
			$userlink = zeige_userdetails($posting['po_u_id'], $userdata);
			if ($posting['u_level'] == 'Z') {
				$text .= "<td $farbe><span class=\"smaller\">$userdata[u_nick]</span></td>\n";
			} else {
				$text .= "<td $farbe><span class=\"smaller\">$userlink</span></td>\n";
			}
		}
		
		if ($posting['po_date2'] == '01.01.70' || $posting['po_date'] == $posting['po_date2']) {
			$antworten = "";
		} else {
			$themen_id = $posting['po_id'];
			$sql2 = "SELECT po_u_id, u_nick, u_level, u_punkte_gesamt, u_punkte_gruppe, u_punkte_anzeigen, u_chathomepage FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id WHERE `po_vater_id` = $themen_id ORDER by po_id desc LIMIT 1;";
			$query2 = sqlQuery($sql2);
			$antwort_u_id = mysqli_result($query2, 0, "po_u_id");
			$antwort_u_nick = mysqli_result($query2, 0, "u_nick");
			$antwort_u_level = mysqli_result($query2, 0, "u_level");
			$antwort_u_punkte_gesamt = mysqli_result($query2, 0, "u_punkte_gesamt");
			$antwort_u_punkte_gruppe = mysqli_result($query2, 0, "u_punkte_gruppe");
			$antwort_u_punkte_anzeigen = mysqli_result($query2, 0, "u_punkte_anzeigen");
			$antwort_u_chathomepage = mysqli_result($query2, 0, "u_chathomepage");
			
			$userdata2 = array();
			$userdata2['u_id'] = $antwort_u_id;
			$userdata2['u_nick'] = $antwort_u_nick;
			$userdata2['u_level'] = $antwort_u_level;
			$userdata2['u_punkte_gesamt'] = $antwort_u_punkte_gesamt;
			$userdata2['u_punkte_gruppe'] = $antwort_u_punkte_gruppe;
			$userdata2['u_punkte_anzeigen'] = $antwort_u_punkte_anzeigen;
			$userdata2['u_chathomepage'] = $antwort_u_chathomepage;
			$antworten_userlink = zeige_userdetails($antwort_u_id, $userdata2);
			
			$antworten = $posting['po_date2'] . " von " . $antworten_userlink;
		}
		$text .= "<td style=\"text-align:center;\" $farbe><span class=\"smaller\">$posting[po_date]</span></td>\n"; // Wann wurde das Thema erstellt
		$text .= "<td style=\"text-align:center;\" $farbe><span class=\"smaller\">".$anzreplys."</span></td>\n"; // Wie viele Antworten hat das Thema
		$text .= "<td style=\"text-align:center;\" $farbe><span class=\"smaller\">$antworten</span></td>\n"; // Letzte Anwort von wem und wann
		
		$zeile++;
	}
	$text .= "</table>\n";
	$text .= show_pfad($th_id, $fo_id, $fo_name, $th_name, $th_anzthreads);
	$text .= show_icon_description();
	$text .= "<br>\n";
	$text .= "<table class=\"tabelle_gerust2\">\n";
	$text .= "<tr>\n";
	$text .= "<td>\n";
	$text .= "<form action=\"forum.php\" method=\"post\">\n";
	$text .= "$t[forum_postingsproseite] <input name=\"anzahl_po_seite2\" size=\"3\" maxlength=\"4\" value=\"$anzahl_po_seite\">\n";
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"show_thema\">\n";
	$text .= "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
	$text .= "<input type=\"submit\" value=\"$t[speichern]\">\n";
	$text .= "</form>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	
	return $text;
}

//Maske zum Eingeben/Editieren/Quoten von Beiträgen
function maske_posting($mode) {
	global $id, $u_id, $th_id, $po_id, $po_vater_id, $po_titel, $po_text, $thread, $seite;
	global $forum_admin, $u_nick, $t;
	
	// Hole alle benötigten Einstellungen des Benutzers
	$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");
	
	switch ($mode) {
		case "neuer_thread":
			$button = $t['neuer_thread_button'];
			$titel = $t['thema_erstellen'];
			
			if (!$po_text) {
				$po_text = erzeuge_fuss("");
			}
			
			break;
		
		case "reply": // zitieren
		//Daten des Vaters holen
			$sql = "SELECT date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date, po_titel, po_text, ifnull(u_nick, 'unknown') AS u_nick
					FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id WHERE po_id = " . intval($po_vater_id);
			$query = sqlQuery($sql);
			
			$autor = mysqli_result($query, 0, "u_nick");
			$po_date = mysqli_result($query, 0, "po_date");
			$po_titel = (mysqli_result($query, 0, "po_titel"));
			if (substr($po_titel, 0, 3) != $t['reply'])
				$po_titel = $t['reply'] . " " . $po_titel;
			$titel = $po_titel;
			$po_text = mysqli_result($query, 0, "po_text");
			$po_text = erzeuge_quoting($po_text, $autor, $po_date);
			$po_text = erzeuge_fuss($po_text);
			
			//$kopfzeile = $po_titel;
			$button = $t['neuer_thread_button'];
			break;
		case "answer": // antworten
		//Daten des Vaters holen
			$sql = "SELECT po_titel FROM `forum_beitraege` WHERE po_id = " . intval($po_vater_id);
			$query = sqlQuery($sql);
			
			$po_titel = mysqli_result($query, 0, "po_titel");
			if (substr($po_titel, 0, 3) != $t['reply'])
				$po_titel = $t['reply'] . " " . $po_titel;
			$titel = $po_titel;
			$po_text = erzeuge_fuss("");
			
			//$kopfzeile = $po_titel;
			$button = $t['neuer_thread_button'];
			break;
		
		case "edit":
		//Daten holen
			$sql = "SELECT date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date, po_titel, po_text, ifnull(u_nick, 'unknown') AS u_nick, u_id, po_threadgesperrt, po_topposting
					FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id WHERE po_id = " . intval($po_id);
			$query = sqlQuery($sql);
			
			$autor = mysqli_result($query, 0, "u_nick");
			$user_id = mysqli_result($query, 0, "u_id");
			$po_date = mysqli_result($query, 0, "po_date");
			$po_topposting = mysqli_result($query, 0, "po_topposting");
			$po_threadgesperrt = mysqli_result($query, 0, "po_threadgesperrt");
			$po_titel = mysqli_result($query, 0, "po_titel");
			
			$titel = $po_titel;
			// Entfernt alle "Re: ", falls diese vorhanden sind.
			$titel = str_replace("Re: ", "", $titel);
			
			$po_text = mysqli_result($query, 0, "po_text");
			
			//Testen ob Benutzer mogelt, indem er den Edit-Link mit anderer po_id benutzt
			if ((!$forum_admin) && ($user_id != $u_id)) {
				echo "wanna cheat eh? bad boy!";
				exit;
			}
			
			//$kopfzeile = $po_titel;
			$button = $t['edit_button'];
			
			break;
	}
	
	$text = "<form name=\"form\" action=\"forum.php\" method=\"post\">";
	$text .= show_pfad_posting($th_id, $titel);
	
	
	// Titel kann nur beim Editieren des 1. Beitrags geändert werden
	if ( (($mode == "edit" || $mode == "answer" || $mode == "reply") && $po_id == $thread) || $mode == "neuer_thread" ) {
		// Weiter unten wird das Formularfeld angezeigt
	} else {
		$text .= "<input type=\"hidden\" name=\"po_titel\" value=\"$po_titel\">\n";
	}
	
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Neues Thema erstellen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $titel, "", "", 0, "70", "");
	
	// Titel
	if ( (($mode == "edit" || $mode == "answer" || $mode == "reply") && $po_id == $thread) || $mode == "neuer_thread" ) {
		$text .= zeige_formularfelder("input", $zaehler, $t['forum_titel'], "po_titel", $po_titel);
		$zaehler++;
	}
	
	
	// Beitrag
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $t['forum_beitrag'] . "</td>\n";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "</tr>\n";
	
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" $bgcolor>($t[desc_posting])</td>\n";
	$text .= "</tr>\n";
	
	
	// Smilies
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right; vertical-align:top;\" $bgcolor>" . zeige_smilies('forum', $benutzerdaten) . "</td>\n";
	$text .= "<td $bgcolor><textarea name=\"po_text\" rows=\"15\" cols=\"95\" wrap=\"physical\">$po_text</textarea></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	// Nur im Obersten Vater die TOP und gesperrt Einstellungen ändern lassen
	if ($forum_admin && ($mode == "edit") && $po_id == $thread) {
		// Thema gesperrt
		$value = array($t['posting_nein'], $t['posting_ja']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $t['posting_thema_gesperrt'], "po_threadgesperrt", $value, $po_threadgesperrt);
		$zaehler++;
		
		
		// Thema anpinnen
		$value = array($t['posting_nein'], $t['posting_ja']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $t['posting_thema_anpinnen'], "po_topposting", $value, $po_topposting);
		$zaehler++;
	}
	
	
	// Beitrag erstellen/editieren
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$button\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	
	$text .= show_pfad_posting($th_id, $titel);
	
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
	
	if ($mode == "neuer_thread") {
		$text .= "<input type=\"hidden\" name=\"po_vater_id\" value=\"0\">\n";
	} else if (($mode == "reply") || ($mode == "answer")) {
		$text .= "<input type=\"hidden\" name=\"thread\" value=\"$thread\">\n";
		$text .= "<input type=\"hidden\" name=\"po_vater_id\" value=\"$po_vater_id\">\n";
	} else {
		$text .= "<input type=\"hidden\" name=\"thread\" value=\"$thread\">\n";
		$text .= "<input type=\"hidden\" name=\"po_id\" value=\"$po_id\">\n";
		$text .= "<input type=\"hidden\" name=\"user_id\" value=\"$user_id\">\n";
	}
	
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"posting_anlegen\">";
	$text .= "<input type=\"hidden\" name=\"seite\" value=\"$seite\">";
	$text .= "<input type=\"hidden\" name=\"mode\" value=\"$mode\">";
	$text .= "</form>";
	
	return $text;
}

//Zeigt die gutgeschriebenen Punkte an
function verbuche_punkte($u_id) {
	global $t, $punkte_pro_posting;
	global $punktefeatures;
	
	if ($punktefeatures) {
		$erfolgsmeldung = $t['forum_punkte1'] . punkte_offline($punkte_pro_posting, $u_id);
		$text = hinweis($erfolgsmeldung, "erfolgreich");
	}
	
	return $text;
}

//Zeigt Pfad in Beiträgen an
function show_pfad_posting($th_id, $po_titel) {
	global $id, $thread, $seite;
	//Infos über Forum und Thema holen
	$sql = "SELECT fo_id, fo_name, th_name FROM `forum_kategorien`, `forum_foren` WHERE th_id = " . intval($th_id) . " AND fo_id = th_fo_id";
	$query = sqlQuery($sql);
	$fo_id = htmlspecialchars(mysqli_result($query, 0, "fo_id"));
	$fo_name = htmlspecialchars(mysqli_result($query, 0, "fo_name"));
	$th_name = htmlspecialchars(mysqli_result($query, 0, "th_name"));
	mysqli_free_result($query);
	
	$text = "";
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"smaller\"><a href=\"forum.php?id=$id#$fo_id\">$fo_name</a> > <a href=\"forum.php?id=$id&th_id=$th_id&aktion=show_thema&seite=$seite\">$th_name</a> > $po_titel</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	return $text;
}

//gibt Navigation für Beiträge aus
function navigation_posting($po_titel, $po_u_id, $th_id, $ist_navigation_top) {
	global $t, $seite, $id, $po_id, $u_id, $thread, $forum_admin;
	$text = "";
	
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\" style=\"text-align:right;\">\n";
	
	
	$threadgesperrt = ist_thread_gesperrt($thread);
	$schreibrechte = pruefe_schreibrechte($th_id);
	// Darf der Benutzer einen Beitrag bearbeiten?
	// Entweder eigenes posting oder forum_admin
	if ( ((($u_id == $po_u_id && !$threadgesperrt) || ($forum_admin)) && ($schreibrechte)) && $ist_navigation_top ) {
		$text .= "<a href=\"forum.php?id=$id&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=edit&seite= $seite\" class=\"button\" title=\"$t[thema_editieren]\"><span class=\"fa fa-pencil icon16\"></span> <span>$t[thema_editieren]</span></a>\n";
	}
	
	if ($schreibrechte && !$threadgesperrt && !$ist_navigation_top) {
		$text .= "<a href=\"forum.php?id=$id&th_id=$th_id&po_vater_id=$thread&thread=$thread&aktion=answer&seite=$seite\" class=\"button\" title=\"$t[thema_antworten]\"><span class=\"fa fa-reply icon16\"></span> <span>$t[thema_antworten]</span></a>\n";
	}

	//Nur Forum-Admins dürfen Beiträge loeschen
	if ($forum_admin && $ist_navigation_top) {
			$text .= "<a href=\"forum.php?id=$id&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=sperre_posting&seite=$seite\" class=\"button\" title=\"$t[thema_sperren]\"><span class=\"fa fa-lock icon16\"></span> <span>$t[thema_sperren]</span></a>\n";
			$text .= "<a href=\"forum.php?id=$id&th_id=$th_id&po_id=$po_id&thread=$thread&aktion=delete_posting&seite=$seite\" onClick=\"return ask('$t[thema_loeschen2]')\" class=\"button\" title=\"$t[thema_loeschen]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[thema_loeschen]</span></a>\n";
		if ($po_id == $thread) {
			$text .= "<a href=\"forum.php?id=$id&th_id=$th_id&thread=$thread&aktion=verschiebe_posting&seite=$seite\" class=\"button\" title=\"$t[thema_verschieben]\"><span class=\"fa fa-arrows icon16\"></span> <span>$t[thema_verschieben]</span></a>\n";
		}
	}
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	// Alle Beiträge automatisch als gelesen markieren, wenn man das Thema aufgerufen hat
	thread_alles_gelesen($th_id, $thread, $u_id);
	
	return $text;
}

// Verschiebe Beitrag
function verschiebe_posting() {
	global $id, $po_id, $thread, $seite;
	global $t, $th_id, $fo_id;
	
	$sql = "SELECT po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date,
				po_titel, po_text, po_u_id, ifnull(u_nick, 'Nobody') as u_nick, u_id, u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage
				FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id WHERE po_id = " . intval($thread);
	
	$query = sqlQuery($sql);
	if ($query)
		$row = mysqli_fetch_object($query);
	mysqli_free_result($query);
	
	$sql = "SELECT fo_name, th_name FROM `forum_kategorien` LEFT JOIN `forum_foren` ON fo_id = th_fo_id WHERE th_id = " . intval($th_id);
	$query = sqlQuery($sql);
	if ($query)
		$row2 = mysqli_fetch_object($query);
	mysqli_free_result($query);
	
	$sql = "SELECT fo_name, th_id, th_name FROM `forum_kategorien` LEFT JOIN `forum_foren` ON fo_id = th_fo_id WHERE th_name <> 'dummy-thema' " . "ORDER BY fo_order, th_order ";
	$query = sqlQuery($sql);
	
	$text = "";
	
	$text .= "<form action=\"forum.php\" method=\"post\">\n";
	$text .= "<table style=\"width:100%\">\n";
	
	
	// Überschrift: Thema verschieben
	$value = $t['verschieben_verschiebe_thema'] . " " . $row->po_titel;
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $value, "", "", 0, "70", "");
	
	
	// Verschieben vom Forum
	$value = $row2->fo_name . " > " . $row2->th_name;
	$text .= zeige_formularfelder("text", $zaehler, $t['verschieben_von_forum'], "", $value);
	$zaehler++;
	
	
	// Verschieben nach Forum
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $t['verschieben_nach_forum'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<select name=\"verschiebe_nach\" size=\"1\">\n";
	while ($row3 = mysqli_fetch_object($query)) {
		$text .= "<option ";
		if ($row3->th_id == $th_id) {
			$text .= "selected ";
		}
		$text .= "value=\"$row3->th_id\">$row3->fo_name > $row3->th_name </option>";
	}
	$text .= "</select>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	mysqli_free_result($query);
	
	// Verschieben
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" name=\"los\" value=\"$t[verschieben4]\"></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	$text .= "</table>\n";
		
	$text .= "<input type=\"hidden\" name=\"id\" value=\"$id\">\n";
	$text .= "<input type=\"hidden\" name=\"thread_verschiebe\" value=\"$thread\">\n";
	$text .= "<input type=\"hidden\" name=\"seite\" value=\"$seite\">\n";
	$text .= "<input type=\"hidden\" name=\"verschiebe_von\" value=\"$th_id\">\n";
	$text .= "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"verschiebe_posting_ausfuehren\">\n";
	$text .= "</form>\n";
	
	return $text;
}

// Zeigt das Thema an
function show_posting() {
	global $id, $po_id, $thread, $seite, $t, $forum_admin;
	global $th_id, $u_id;
	
	$sql = "SELECT po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date,
				po_titel, po_text, po_u_id, po_gesperrt, ifnull(u_nick, 'Nobody') AS u_nick, u_id, u_level,u_punkte_gesamt,u_punkte_gruppe,u_chathomepage
				FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id WHERE po_id = " . intval($po_id);
	
	$query = sqlQuery($sql);
	if ($query) {
		$row = mysqli_fetch_object($query);
	}
	$th_id = $row->po_th_id;
	$po_u_id = $row->po_u_id;
	$po_date = $row->po_date;
	$po_titel = $row->po_titel;
	$po_gesperrt = $row->po_gesperrt;
	
	if ($po_gesperrt == 1 && !$forum_admin) {
		echo ('Beitrag gesperrt');
		return;
	}
	
	$po_text = ersetze_smilies(chat_parse(nl2br($row->po_text)));
	if (($row->u_nick != "Nobody") && ($row->u_level <> "Z")) {
		$autor = zeige_userdetails($po_u_id, $row);
	} else {
		$autor = $row->u_nick;
	}
	
	mysqli_free_result($query);
	
	$sql = "SELECT po_threadorder FROM `forum_beitraege` WHERE po_th_id= $th_id";
	$query = sqlQuery($sql);
	$po_threadorder = mysqli_result($query, 0, "po_threadorder");
	
	mysqli_free_result($query);
	
	$text = show_pfad_posting($th_id, $po_titel);
	
	$text .= navigation_posting($po_titel, $po_u_id, $th_id, TRUE);
	
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Thema
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $po_titel, "", "", 0, "70", "");
	
	
	// Vom Benutzer gelesene Beiträge holen
	$sql = "SELECT `u_gelesene_postings` FROM `user` WHERE `u_id`=$u_id";
	$query = sqlQuery($sql);
	if (mysqli_num_rows($query) > 0) {
		$gelesene = mysqli_result($query, 0, "u_gelesene_postings");
	}
	$u_gelesene = unserialize($gelesene);
	
	$sql = "SELECT po_id, po_th_id, date_format(from_unixtime(po_ts), '%d.%m.%Y, %H:%i:%s') AS po_date,
				po_titel, po_text, po_u_id, u_nick, u_level, u_punkte_gesamt, u_punkte_gruppe, u_punkte_anzeigen, u_chathomepage, po_threadorder, po_gesperrt
				FROM `forum_beitraege` LEFT JOIN user ON po_u_id = u_id WHERE po_id = $thread OR po_vater_id = $thread";
	$alle_beitraege = sqlQuery($sql);
	
	
	// Beiträge in der richtigen Reihenfolge durchlaufen
	foreach($alle_beitraege as $zaehler => $beitrag) {
		$span = 0;
		
		$po_text = ersetze_smilies(chat_parse(nl2br( $beitrag['po_text'] )));
		$po_date = $beitrag['po_date'];
		$po_gesperrt = $beitrag['po_gesperrt'];
		$po_u_id = $beitrag['po_u_id'];
		$po_th_id = $beitrag['po_th_id'];
		$po_id = $beitrag['po_id'];
		$po_u_nick = $beitrag['u_nick'];
		
		$po_threadorder = $beitrag['po_threadorder'];
		$po_u_level = $beitrag['u_level'];
		$po_u_punkte_gesamt = $beitrag['u_punkte_gesamt'];
		$po_u_punkte_gruppe = $beitrag['u_punkte_gruppe'];
		$po_u_punkte_anzeigen = $beitrag['u_punkte_anzeigen'];
		$po_u_chathomepage = $beitrag['u_chathomepage'];
		
		if (!@in_array($po_id, $u_gelesene[$th_id])) {
			$col = ' <span class="fa fa-star icon16" alt="ungelesener Beitrag" title="ungelesener Beitrag"></span>';
		} else {
			$col = '';
		}
		
		if ($po_gesperrt == 1) {
			$besonderer_status = " <span style=\"color:#ff0000;\">(gesperrt)</span>" . $col;
		} else {
			$besonderer_status = $col;
		}
		
		if (!$po_u_nick) {
			$userdetails = "gelöschter Benutzer";
		} else {
			$userdata = array();
			$userdata['u_id'] = $po_u_id;
			$userdata['u_nick'] = $po_u_nick;
			$userdata['u_level'] = $po_u_level;
			$userdata['u_punkte_gesamt'] = $po_u_punkte_gesamt;
			$userdata['u_punkte_gruppe'] = $po_u_punkte_gruppe;
			$userdata['u_punkte_anzeigen'] = $po_u_punkte_anzeigen;
			$userdata['u_chathomepage'] = $po_u_chathomepage;
			
			$userlink = zeige_userdetails($po_u_id, $userdata);
			if ($po_u_level == 'Z') {
				$userdetails = "$userdata[u_nick]";
			} else {
				$userdetails = "$userlink";
			}
		}
		
		// Start des Avatars
		// Bildinfos lesen und in Array speichern
		$queryAvatar = "SELECT `b_name`, `b_height`, `b_width`, `b_mime` FROM `bild` WHERE `b_name` = 'avatar' AND `b_user` = $po_u_id";
		$resultAvatar = sqlQuery($queryAvatar);
		unset($bilder);
		if ($resultAvatar && mysqli_num_rows($resultAvatar) > 0) {
			while ($rowAvatar = mysqli_fetch_object($resultAvatar)) {
				$bilder[$rowAvatar->b_name]['b_mime'] = $rowAvatar->b_mime;
				$bilder[$rowAvatar->b_name]['b_width'] = $rowAvatar->b_width;
				$bilder[$rowAvatar->b_name]['b_height'] = $rowAvatar->b_height;
			}
		}
		
		if (!isset($bilder)) {
			if ($ui_gen[0] == "m") { // Männlicher Standard-Avatar
				$ava = '<img src="./images/avatars/no_avatar_m.jpg" style="width:60px; height:60px;" alt="" /> ';
			} else if ($ui_gen[0] == "w") { // Weiblicher Standard-Avatar
				$ava = '<img src="./images/avatars/no_avatar_w.jpg" style="width:60px; height:60px;" alt="" /> ';
			} else { // Neutraler Standard-Avatar
				$ava = '<img src="./images/avatars/no_avatar_es.jpg" style="width:60px; height:60px;" alt="" /> ';
			}
		} else {
			$ava = avatar_editieren_anzeigen($po_u_id, $po_u_nick, $bilder, "forum") ." ";
		}
		// Ende des Avatars
		
		$text .= "<tr>\n";
		$text .= "<td rowspan=\"3\" class=\"tabelle_kopfzeile smaller\" style=\"width:150px; vertical-align:top; text-align:center;\">\n";
		$text .= "$ava <br>\n";
		$text .= $userdetails . $besonderer_status;
		$text .= "</td>\n";
		$text .= "<td class=\"tabelle_kopfzeile\">$t[geschrieben_am]$po_date</td>\n";
		$text .= "</tr>\n";
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_zeile1\">$po_text</td>\n";
		$text .= "</tr>\n";
		
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_kopfzeile\" style=\"text-align:right;\">\n";
		
		$threadgesperrt = ist_thread_gesperrt($thread);
		$schreibrechte = pruefe_schreibrechte($po_th_id);
		// Darf der Benutzer den Beitrag bearbeiten?
		// Entweder eigenes posting oder forum_admin
		if ((($u_id == $po_u_id && !$threadgesperrt) || ($forum_admin)) && ($schreibrechte)) {
			$text .= "<a href=\"forum.php?id=$id&th_id=$po_th_id&po_id=$po_id&thread=$thread&aktion=edit&seite=$seite\" class=\"button\" title=\"$t[thema_editieren]\"><span class=\"fa fa-pencil icon16\"></span> <span>$t[thema_editieren]</span></a>\n";
		}
		
		if ($schreibrechte && !$threadgesperrt) {
			$text .= "<a href=\"forum.php?id=$id&th_id=$po_th_id&po_vater_id=$thread&thread=$thread&aktion=reply&seite=$seite\" class=\"button\" title=\"$t[thema_zitieren]\"><span class=\"fa fa-quote-right icon16\"></span> <span>$t[thema_zitieren]</span></a>\n";
		}
		
		//Nur Forum-Admins dürfen Beiträge loeschen
		if ($forum_admin) {
			$text .= "<a href=\"forum.php?id=$id&th_id=$po_th_id&po_id=$po_id&thread=$thread&aktion=sperre_posting&seite=$seite\" class=\"button\" title=\"$t[thema_sperren]\"><span class=\"fa fa-lock icon16\"></span> <span>$t[thema_sperren]</span></a>\n";
			$text .= "<a href=\"forum.php?id=$id&th_id=$po_th_id&po_id=$po_id&thread=$thread&aktion=delete_posting&seite=$seite\" onClick=\"return ask('$t[thema_loeschen2]')\" class=\"button\" title=\"$t[thema_loeschen]\"><span class=\"fa fa-trash icon16\"></span> <span>$t[thema_loeschen]</span></a>\n";
		}
		$text .= "</td>\n";
		$text .= "</tr>\n";
	}
	
	$text .= "</table>\n";
	
	$text .= navigation_posting($po_titel, $po_u_id, $th_id, FALSE);
	$text .= show_pfad_posting($th_id, $po_titel);
	
	$text .= "</table>\n";
	
	
	return $text;
}
?>