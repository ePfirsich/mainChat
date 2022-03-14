<?php
// Kategorie anlegen oder editieren
function maske_kategorie($fo_id = 0) {
	global $lang;
	
	if ($fo_id > 0) {
		$query = pdoQuery("SELECT `fo_name`, `fo_admin` FROM `forum_kategorien` where `fo_id` = :fo_id", [':fo_id'=>$fo_id]);
		
		$result = $query->fetch();
		$fo_name = htmlspecialchars($result['fo_name']);
		$fo_admin = $result['fo_admin'];
	} else {
		$fo_name = "";
		$fo_admin = "";
	}
	$zaehler = 0;
	
	$text = "";
	$text .= "<table style=\"width:100%;\">\n";
	$text .= "<form action=\"forum.php\" method=\"post\">\n";
	
	// Name der Kategorie
	$text .= zeige_formularfelder("input", $zaehler, $lang['forum_kategorie_name'], "fo_name",  $fo_name);
	$zaehler++;
	
	// Gäste dürfen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	
	$selg1 = "";
	$selg2 = "";
	if ($fo_admin != "" && ($fo_admin & 8) == 8) {
		$selg1 = "selected";
		$selg2 = "";
	}
	if ($fo_admin != "" && ($fo_admin & 16) == 16) {
		$selg1 = "";
		$selg2 = "selected";
	}
	
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['forum_berechtigungen_gaeste_duerfen'] . "</td>\n";
	$text .= "<td $bgcolor>\n";
	$text .= "<select size=\"1\" name=\"fo_gast\">\n";
	$text .= "<option value=\"0\">$lang[forum_berechtigungen_weder_lesen_noch_schreiben]</option>\n";
	$text .= "<option value=\"8\" $selg1>$lang[forum_berechtigungen_nur_lesen]</option>\n";
	$text .= "<option value=\"24\" $selg2>$lang[forum_berechtigungen__lesen_noch_schreiben]</option>\n";
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
	
	$selu1 = "";
	$selu2 = "";
	if ($fo_admin != "" && ($fo_admin & 2) == 2) {
		$selu1 = "selected";
		$selu2 = "";
	}
	if ($fo_admin != "" && ($fo_admin & 4) == 4) {
		$selu1 = "";
		$selu2 = "selected";
	}
	
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['forum_berechtigungen_benutzer_duerfen'] . "</td>\n";
	$text .= "<td $bgcolor>\n";
	$text .= "<select size=\"1\" name=\"fo_user\">\n";
	$text .= "<option value=\"0\">$lang[forum_berechtigungen_weder_lesen_noch_schreiben]</option>\n";
	$text .= "<option value=\"2\" $selu1>$lang[forum_berechtigungen_nur_lesen]</option>\n";
	$text .= "<option value=\"6\" $selu2>$lang[forum_berechtigungen__lesen_noch_schreiben]</option>\n";
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
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[forum_button_speichern]\"></td>\n";
	$text .= "</tr>\n";
	
	if ($fo_id > 0) {
		$text .= "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"kategorie_editieren\">\n";
	} else {
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"kategorie_anlegen\">\n";
	}
	
	$text .= "</table>\n";
	$text .= "</form>\n";
	
	
	return $text;
}

// Listet alle Foren mit den Anzahl Themen auf
function forum_liste() {
	global $forum_admin, $chat_grafik, $lang, $u_level;
	
	if ($u_level == "G") {
		$query = pdoQuery("SELECT `fo_id`, `fo_name`, `fo_order`, `fo_admin`, `th_id`, `th_fo_id`, `th_name`, `th_desc`, `th_anzthreads`, `th_anzreplys`, `th_order`, `th_postings` FROM `forum_kategorien`, `forum_foren` WHERE `fo_id` = `th_fo_id` "
		. "AND ( ((`fo_admin` & 8) = 8) OR `fo_admin` = 0) "
		. "ORDER BY `fo_order`, `th_order`", []);
	} else if ($u_level == "U" || $u_level == "A" || $u_level == "M" || $u_level == "Z") {
		$query = pdoQuery("SELECT `fo_id`, `fo_name`, `fo_order`, `fo_admin`, `th_id`, `th_fo_id`, `th_name`, `th_desc`, `th_anzthreads`, `th_anzreplys`, `th_order`, `th_postings` FROM `forum_kategorien`, `forum_foren` WHERE `fo_id` = `th_fo_id` "
		. "AND ( ((`fo_admin` & 2) = 2) OR `fo_admin` = 0) "
		. "ORDER BY `fo_order`, `th_order`", []);
	} else {
		$query = pdoQuery("SELECT `fo_id`, `fo_name`, `fo_order`, `fo_admin`, `th_id`, `th_fo_id`, `th_name`, `th_desc`, `th_anzthreads`, `th_anzreplys`, `th_order`, `th_postings` FROM `forum_kategorien`, `forum_foren` WHERE `fo_id` = `th_fo_id` "
		. "ORDER BY `fo_order`, `th_order`", []);
	}
	
	$queryCount = $query->rowCount();
	
	//fo_id merken zur Darstellnug des Kopfes
	$fo_id_last = 0;
	$zeile = 0;
	if ($queryCount > 0) {
		$text = "";
		
		// Funktioniert noch nicht
		//$text .= "<a href=\"forum.php?aktion=foren_als_gelesen\" class=\"button\" title=\"$lang[forum_foren_gelesen_markieren]\"><span class=\"fa-solid fa-check icon16\"></span> <span>$lang[forum_foren_gelesen_markieren]</span></a>\n";
		
		$text .= "<table style=\"width:100%\">\n";
		
		$result = $query->fetchAll();
		foreach($result as $zaehler => $thema) {
			//Neue Kategorie?
			if ($fo_id_last != $thema['fo_id']) {
				$text .= "<tr>\n";
				$text .= "<td colspan=\"6\">&nbsp;</td>";
				$text .= "</tr>\n";
				
				$text .= "<tr>\n";
				$text .= "<td colspan=\"2\" class =\"tabelle_kopfzeile\">&nbsp;&nbsp;&nbsp;" .html_entity_decode($thema['fo_name']) . "<a name=\"" . $thema['fo_id'] . "\"></a></td>\n";
				if ($forum_admin) {
					$text .= "<td class =\"tabelle_kopfzeile\" style=\"width:300px; text-align:right; vertical-align:middle;\">\n";
					$text .= "<a href=\"forum.php?aktion=kategorie_edit&fo_id=$thema[fo_id]\" class=\"button\" title=\"$lang[forum_button_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[forum_button_editieren]</span></a>\n";
					$text .= "<a href=\"forum.php?aktion=kategorie_delete&fo_id=$thema[fo_id]\" onClick=\"return ask('$lang[forum_kategorie_loeschen]')\" class=\"button\" title=\"$lang[forum_button_loeschen]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[forum_button_loeschen]</span></a>\n";
					$text .= "<a href=\"forum.php?fo_id=$thema[fo_id]&aktion=forum_neu\" class=\"button\" title=\"$lang[forum_button_neues_forum]\"><span class=\"fa-solid fa-plus icon16\"></span> <span>$lang[forum_button_neues_forum]</span></a>\n";
					$text .= "</td>\n";
					
					$text .= "<td class =\"tabelle_kopfzeile\" style=\"width:80px; text-align:center;\">\n";
					$text .= "<a href=\"forum.php?fo_id=$thema[fo_id]&fo_order=$thema[fo_order]&aktion=kategorie_up\" class=\"button\"><span class=\"fa-solid fa-arrow-up icon16\"></span></a> ";
					$text .= "<a href=\"forum.php?fo_id=$thema[fo_id]&fo_order=$thema[fo_order]&aktion=kategorie_down\" class=\"button\"><span class=\"fa-solid fa-arrow-down icon16\"></span></a>\n";
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
					$text .= "<a href=\"forum.php?th_id=$thema[th_id]&aktion=show_forum\">\n";
					$text .= html_entity_decode($thema['th_name']) . "</a><br><span class=\"smaller\"> " . html_entity_decode($thema['th_desc']) . "</span>\n";
					$text .= "</td>\n";
					
					$text .= "<td style=\"width:170px; text-align:center; vertical-align:middle;\" $farbe>\n";
					$text .= "<a href=\"forum.php?th_id=$thema[th_id]&aktion=forum_edit\" class=\"button\" title=\"$lang[forum_button_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[forum_button_editieren]</span></a>\n";
					$text .= "<a href=\"forum.php?aktion=forum_delete&th_id=$thema[th_id]\" onClick=\"return ask('$lang[forum_loeschen]')\" class=\"button\" title=\"$lang[forum_button_loeschen]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[forum_button_loeschen]</span></a>\n";
					$text .= "</td>\n";
					
					$text .= "<td style=\"text-align:center;\" $farbe>\n";
					$text .= "<a href=\"forum.php?th_id=$thema[th_id]&fo_id=$thema[fo_id]&th_order=$thema[th_order]&aktion=forum_up\" class=\"button\"><span class=\"fa-solid fa-arrow-up icon16\"></span></a> ";
					$text .= "<a href=\"forum.php?th_id=$thema[th_id]&fo_id=$thema[fo_id]&th_order=$thema[th_order]&aktion=forum_down\" class=\"button\"><span class=\"fa-solid fa-arrow-down icon16\"></span></a>\n";
					$text .= "</td>\n";
				} else {
					$text .= "<td style=\"font-weight:bold; padding-left:10px;\" $farbe>\n";
					$text .= "<a href=\"forum.php?th_id=$thema[th_id]&aktion=show_forum\">" . htmlspecialchars($thema['th_name']) . "</a><br>\n";
					$text .= "<span class=\"smaller\"> " . htmlspecialchars($thema['th_desc']) . "</span></td>\n";
					
					$text .= "<td $farbe>&nbsp;</td>";
					$text .= "<td $farbe>&nbsp;</td>";
				}
				$text .= "<td style=\"padding-left:10px;\" $farbe>";
				$text .= "<span class=\"fa-solid fa-comment icon16\"></span> $thema[th_anzthreads] $lang[forum_kategorie_themen]<br>";
				$text .= "<span class=\"fa-solid fa-pencil icon16\"></span> " . ( $thema['th_anzreplys'] + $thema['th_anzthreads']) . " $lang[forum_kategorie_beitraege]";
				$text .= "</td>\n";
				$text .= "</tr>\n";
			}
			
			$fo_id_last = $thema['fo_id'];
			$zeile++;
		}
		$text .= "</table>\n";
		$text .= show_icon_description();
	}
	
	return $text;
}

//Zeigt Erklärung der verschiedenen Folder an
function show_icon_description() {
	global $lang, $chat_grafik;
	
	$text = "<br>\n";
	$text .= "<div style=\"margin-left:5px;\">\n";
	$text .= "$chat_grafik[forum_ordnerneu] = $lang[desc_folder]<br>\n";
	$text .= "$chat_grafik[forum_ordnerblau] = $lang[desc_redfolder] ($chat_grafik[forum_ordnervoll] = $lang[desc_burningredfolder])<br>\n";
	$text .= "$chat_grafik[forum_topthema] = $lang[desc_topposting]<br>\n";
	$text .= "$chat_grafik[forum_threadgeschlossen] = $lang[desc_threadgeschlossen]\n";
	$text .= "</div>\n";
	
	return $text;
}

//Eingabemaske für Foren
function maske_forum($th_id = 0) {
	global $fo_id, $lang;
	
	$text = "";
	if ($th_id > 0) {
		$query = pdoQuery("SELECT `th_name`, `th_desc` FROM `forum_foren` WHERE `th_id` = :th_id", [':th_id'=>$th_id]);
		
		$result = $query->fetch();
		$th_name = htmlspecialchars($result['th_name']);
		$th_desc = htmlspecialchars($result['th_desc']);
	}
	
	if (!isset($th_name)) {
		$th_name = "";
	}
	
	if (!isset($th_desc)) {
		$th_desc = "";
	}
	
	$zaehler = 0;
	$text .= "<form action=\"forum.php\" method=\"post\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	// Name des Forums
	$text .= zeige_formularfelder("input", $zaehler, $lang['forum_name'], "th_name",  $th_name);
	$zaehler++;
	
	// Beschreibung des Forums
	$text .= zeige_formularfelder("textarea", $zaehler, $lang['forum_beschreibung'], "th_desc", $th_desc);
	$zaehler++;
	
	// Forum verschieben
	if ($th_id > 0) {
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$selectbox = "<input type=\"checkbox\" name=\"th_forumwechsel\" value=\"Y\">\n";
		
		$query = pdoQuery("SELECT `fo_id`, `fo_name` FROM `forum_kategorien` ORDER BY `fo_order`", []);
		
		$result = $query->fetchAll();
		
		$selectbox .= "<select name=\"th_verschiebe_nach\" size=\"1\">\n";
		foreach($result as $zaehler => $row) {
			$selectbox .= "<option ";
			if ($row['fo_id'] == $fo_id) {
				$selectbox .= "selected ";
			}
			$selectbox .= "value=\"$row[fo_id]\">$row[fo_name]</option>";
		}
		$selectbox .= "</select>\n";
		
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['forum_verschieben'] . "</td>\n";
		$text .= "<td $bgcolor>$selectbox</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
	}
	
	
	//Forum speichern
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[forum_button_speichern]\"></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	$text .= "</table>\n";
	$text .= "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
	
	if ($th_id > 0) {
		$text .= "<input type=\"hidden\" name=\"th_id\" value=\"$th_id\">\n";
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"forum_editieren\">\n";
	} else {
		$text .= "<input type=\"hidden\" name=\"aktion\" value=\"forum_anlegen\">\n";
	}
	$text .= "</form>\n";
	
	
	return $text;
}

//Zeigt Pfad und Seiten in Themaliste an
function show_pfad($th_id, $fo_id, $fo_name, $th_name, $th_anzthreads) {
	global $anzahl_po_seite, $seite, $lang;
	
	$text = "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"smaller\"><a href=\"forum.php#$fo_id\">" . html_entity_decode($fo_name) . "</a> > <a href=\"forum.php?th_id=$th_id&aktion=show_forum&seite=$seite\">" . html_entity_decode($th_name) . "</a></td>\n";
	
	if (!$anzahl_po_seite || $anzahl_po_seite == 0) {
		$anzahl_po_seite = 20;
	}
	$anz_seiten = ceil(($th_anzthreads / $anzahl_po_seite));
	if ($anz_seiten > 1) {
			$text .= "<td style=\"text-align:right;\" class=\"smaller\">$lang[page] ";
			for ($page = 1; $page <= $anz_seiten; $page++) {
				if ($page == $seite) {
					$col = "font-weight:bold;";
				} else {
					$col = '';
				}
				$text .= "<a href=\"forum.php?th_id=$th_id&aktion=show_forum&seite=$page\"><span style=\"$col;\">$page</span></a> ";
			}
			$text .= "</td></tr>\n";
	} else {
		$text .= "</tr>\n";
	}
	$text .= "</table>\n";
	
	return $text;
}

//Zeigt ein Thema mit allen Beiträgen an
function show_forum() {
	global $forum_admin, $th_id, $seite, $anzahl_po_seite, $chat_grafik, $lang, $admin, $u_id, $locale;
	
	$leserechte = pruefe_leserechte($th_id);
	
	if (!$leserechte) {
		echo $lang['leserechte'];
		exit;
	}
	if (!$seite) {
		$seite = 1;
	}
	
	$offset = ($seite - 1) * $anzahl_po_seite;
	
	pdoQuery("SET `lc_time_names` = :lc_time_names", [':lc_time_names'=>$locale]);
	
	// Erklärung zu den Datumsformatierungen: https://www.w3schools.com/sql/func_mysql_date_format.asp
	$query2 = pdoQuery("SELECT `po_id`, `po_u_id`, date_format(from_unixtime(`po_ts`), '%d. %M %Y') AS `po_date`, date_format(from_unixtime(`po_threadts`), '%d. %M %Y') AS `po_date2`,
			`po_titel`, `po_threadorder`, `po_topposting`, `po_threadgesperrt`, `u_nick`,
			`u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_punkte_anzeigen`, `u_nachrichten_empfangen`, `u_chathomepage` FROM `forum_beitraege` LEFT JOIN `user` ON `po_u_id` = `u_id`
			WHERE `po_vater_id` = 0 AND `po_th_id` = :po_th_id ORDER BY `po_topposting` DESC, `po_threadts` DESC, `po_ts` DESC LIMIT :limit_start, :limit_end", [':po_th_id'=>$th_id, ':limit_start'=>$offset, ':limit_end'=>$anzahl_po_seite]);
	$result2 = $query2->fetchAll();
	
	//Infos über Forum und Thema holen
	$query = pdoQuery("SELECT `fo_id`, `fo_name`, `th_name`, `th_anzthreads` FROM `forum_kategorien`, `forum_foren` WHERE `th_id` = :th_id AND `fo_id` = `th_fo_id`", [':th_id'=>$th_id]);
	
	$result = $query->fetch();
	$fo_id = htmlspecialchars($result['fo_id']);
	$fo_name = htmlspecialchars($result['fo_name']);
	$th_name = htmlspecialchars($result['th_name']);
	$th_anzthreads = $result['th_anzthreads'];
	
	$text = "";
	
	$text .= show_pfad($th_id, $fo_id, $fo_name, $th_name, $th_anzthreads);
	
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"5\" class=\"tabelle_kopfzeile\">" . html_entity_decode($th_name) . "</td>\n";
	$text .= "<td style=\"text-align:right;\" class=\"tabelle_kopfzeile\">\n";
	
	$schreibrechte = pruefe_schreibrechte($th_id);
	if ($schreibrechte) {
		$text .= "<a href=\"forum.php?th_id=$th_id&po_vater_id=0&aktion=thread_neu\" class=\"button\" title=\"$lang[thema_erstellen]\"><span class=\"fa-solid fa-plus icon16\"></span> <span>$lang[thema_erstellen]</span></a>\n";
	} else {
		$text .= "<span class=\"smaller\">$lang[nur_leserechte]</span><br>\n";
	}
	$text .= "<a href=\"forum.php?th_id=$th_id&aktion=forum_als_gelesen\" class=\"button\" title=\"$lang[forum_forum_gelesen_markieren]\"><span class=\"fa-solid fa-check icon16\"></span> <span>$lang[forum_forum_gelesen_markieren]</span></a>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$lang[geschrieben_von]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$lang[forum_thema_erstellt_am]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$lang[forum_anzahl_antworten]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$lang[forum_letzte_Antwort]</td>\n";
	$text .= "</tr>\n";
	
	$zeile = 0;
	foreach($result2 as $zaehler => $posting) {
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
		$text .= "<td $farbe class=\"smaller\">&nbsp;<b><a href=\"forum.php?th_id=$th_id&po_id=$posting[po_id]&thread=$posting[po_id]&aktion=show_posting&seite=$seite\">" . html_entity_decode(substr($posting['po_titel'], 0, 40)) . "</a></b></td>\n";
		
		if (!$posting['u_nick']) {
			$text .= "<td $farbe><span class=\"smaller\"><b>Nobody</b></span></td>\n";
		} else {
			$userlink = zeige_userdetails($posting['po_u_id']);
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
			$query = pdoQuery("SELECT `po_u_id` FROM `forum_beitraege` WHERE `po_vater_id` = :po_vater_id ORDER by `po_id` DESC LIMIT 1", [':po_vater_id'=>$themen_id]);
			
			$result = $query->fetch();
			
			$antworten_userlink = zeige_userdetails($result['po_u_id']);
			
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
	
	return $text;
}

//Maske zum Eingeben/Editieren/Quoten von Beiträgen
function maske_posting($mode) {
	global $u_id, $th_id, $po_id, $po_vater_id, $po_titel, $po_text, $thread, $seite, $forum_admin, $u_nick, $lang;
	
	// Hole alle benötigten Einstellungen des Benutzers
	$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");
	
	switch ($mode) {
		case "neuer_thread":
			$button = $lang['neuer_thread_button'];
			$titel = $lang['thema_erstellen'];
			
			if (!$po_text) {
				$po_text = erzeuge_fuss("");
			}
			
			break;
		
		case "reply": // zitieren
		//Daten des Vaters holen
			$query = pdoQuery("SELECT date_format(from_unixtime(`po_ts`), '%d.%m.%Y, %H:%i:%s') AS `po_date`, `po_titel`, `po_text`, ifnull(u_nick, 'unknown') AS `u_nick`
					FROM `forum_beitraege` LEFT JOIN `user` ON `po_u_id` = `u_id` WHERE `po_id` = :po_id", [':po_id'=>$po_vater_id]);
			
			$result = $query->fetch();
			$autor = $result['u_nick'];
			$po_titel = $result['po_titel'];
			if (substr($po_titel, 0, 3) != $lang['reply']) {
				$po_titel = $lang['reply'] . " " . $result['po_date'];
			}
			$titel = $po_titel;
			$po_text = $result['po_text'];
			$po_text = erzeuge_quoting($po_text, $autor, $result['po_date']);
			$po_text = erzeuge_fuss($po_text);
			
			//$kopfzeile = $po_titel;
			$button = $lang['neuer_thread_button'];
			break;
		case "answer": // antworten
		//Daten des Vaters holen
			$query = pdoQuery("SELECT `po_titel` FROM `forum_beitraege` WHERE `po_id` = :po_id", [':po_id'=>$po_vater_id]);
			
			$result = $query->fetch();
			$po_titel = $result['po_titel'];
			if (substr($po_titel, 0, 3) != $lang['reply']) {
				$po_titel = $lang['reply'] . " " . $po_titel;
			}
			$titel = $po_titel;
			$po_text = erzeuge_fuss("");
			
			//$kopfzeile = $po_titel;
			$button = $lang['neuer_thread_button'];
			break;
		
		case "edit":
		//Daten holen
			$query = pdoQuery("SELECT date_format(from_unixtime(`po_ts`), '%d.%m.%Y, %H:%i:%s') AS `po_date`, `po_titel`, `po_text`, ifnull(`u_nick`, 'unknown') AS `u_nick`, `u_id`, `po_threadgesperrt`, `po_topposting`
					FROM `forum_beitraege` LEFT JOIN `user` ON `po_u_id` = `u_id` WHERE `po_id` = :po_id", [':po_id'=>$po_id]);
			
			$result = $query->fetch();
			$autor = $result['u_nick'];
			$user_id = $result['u_id'];
			$po_topposting = $result['po_topposting'];
			$po_threadgesperrt = $result['po_threadgesperrt'];
			$po_titel = $result['po_titel'];
			
			$titel = $po_titel;
			// Entfernt alle "Re: ", falls diese vorhanden sind.
			$titel = str_replace("Re: ", "", $titel);
			
			$po_text = $result['po_text'];
			
			//Testen ob Benutzer mogelt, indem er den Edit-Link mit anderer po_id benutzt
			if ((!$forum_admin) && ($user_id != $u_id)) {
				exit;
			}
			
			//$kopfzeile = $po_titel;
			$button = $lang['edit_button'];
			
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
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Neues Thema erstellen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $titel, "", "", 0, "70", "");
	
	// Titel
	if ( (($mode == "edit" || $mode == "answer" || $mode == "reply") && $po_id == $thread) || $mode == "neuer_thread" ) {
		$text .= zeige_formularfelder("input", $zaehler, $lang['forum_titel'], "po_titel", $po_titel);
		$zaehler++;
	}
	
	
	// Beitrag
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['forum_beitrag'] . "</td>\n";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "</tr>\n";
	
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" $bgcolor>($lang[desc_posting])</td>\n";
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
		$value = array($lang['posting_nein'], $lang['posting_ja']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $lang['posting_thema_gesperrt'], "po_threadgesperrt", $value, $po_threadgesperrt);
		$zaehler++;
		
		
		// Thema anpinnen
		$value = array($lang['posting_nein'], $lang['posting_ja']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $lang['posting_thema_anpinnen'], "po_topposting", $value, $po_topposting);
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
	global $lang, $punkte_pro_posting;
	global $punktefeatures;
	
	if ($punktefeatures) {
		$erfolgsmeldung = $lang['forum_punkte1'] . punkte_offline($punkte_pro_posting, $u_id);
		$text = hinweis($erfolgsmeldung, "erfolgreich");
	}
	
	return $text;
}

//Zeigt Pfad in Beiträgen an
function show_pfad_posting($th_id, $po_titel) {
	global $thread, $seite;
	//Infos über Forum und Thema holen
	$query = pdoQuery("SELECT `fo_id`, `fo_name`, `th_name` FROM `forum_kategorien`, `forum_foren` WHERE `th_id` = :th_id AND `fo_id` = `th_fo_id`", [':th_id'=>$th_id]);
	
	$result = $query->fetch();
	$fo_id = htmlspecialchars($result['fo_id']);
	$fo_name = htmlspecialchars($result['fo_name']);
	$th_name = htmlspecialchars($result['th_name']);
	
	$text = "";
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"smaller\"><a href=\"forum.php#$fo_id\">" . html_entity_decode($fo_name) . "</a> > <a href=\"forum.php?th_id=$th_id&aktion=show_forum&seite=$seite\">" . html_entity_decode($th_name) . "</a> > " . html_entity_decode($po_titel) . "</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	return $text;
}

//gibt Navigation für Beiträge aus
function navigation_thema($po_titel, $po_u_id, $th_id, $ist_navigation_top) {
	global $lang, $seite, $po_id, $u_id, $thread, $forum_admin;
	$text = "";
	
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\" style=\"text-align:right;\">\n";
	
	
	$threadgesperrt = ist_thema_gesperrt($th_id);
	$schreibrechte = pruefe_schreibrechte($th_id);
	// Darf der Benutzer einen Beitrag bearbeiten?
	// Entweder eigenes posting oder forum_admin
	if ( ((($u_id == $po_u_id && !$threadgesperrt) || ($forum_admin)) && ($schreibrechte)) && $ist_navigation_top ) {
		$text .= "<a href=\"forum.php?th_id=$th_id&po_id=$po_id&thread=$thread&aktion=edit&seite=$seite\" class=\"button\" title=\"$lang[thema_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[thema_editieren]</span></a>\n";
	}
	
	if ($schreibrechte && !$threadgesperrt && !$ist_navigation_top) {
		$text .= "<a href=\"forum.php?th_id=$th_id&po_vater_id=$thread&thread=$thread&aktion=answer&seite=$seite\" class=\"button\" title=\"$lang[thema_antworten]\"><span class=\"fa-solid fa-reply icon16\"></span> <span>$lang[thema_antworten]</span></a>\n";
	}

	//Nur Forum-Admins dürfen Beiträge loeschen
	if ($forum_admin && $ist_navigation_top) {
		if($threadgesperrt) {
			$text .= "<a href=\"forum.php?th_id=$th_id&po_id=$po_id&thread=$thread&aktion=sperre_thema&seite=$seite\" class=\"button\" title=\"$lang[thema_entsperren]\"><span class=\"fa-solid fa-lock-open icon16\"></span> <span>$lang[thema_entsperren]</span></a>\n";
		} else {
			$text .= "<a href=\"forum.php?th_id=$th_id&po_id=$po_id&thread=$thread&aktion=sperre_thema&seite=$seite\" class=\"button\" title=\"$lang[thema_sperren]\"><span class=\"fa-solid fa-lock icon16\"></span> <span>$lang[thema_sperren]</span></a>\n";
		}
		$text .= "<a href=\"forum.php?th_id=$th_id&po_id=$po_id&thread=$thread&aktion=delete_posting&seite=$seite\" onClick=\"return ask('$lang[thema_loeschen2]')\" class=\"button\" title=\"$lang[thema_loeschen]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[thema_loeschen]</span></a>\n";
		//if ($po_id == $thread) {
			$text .= "<a href=\"forum.php?th_id=$th_id&thread=$thread&aktion=verschiebe_posting&seite=$seite\" class=\"button\" title=\"$lang[thema_verschieben]\"><span class=\"fa-solid fa-arrows icon16\"></span> <span>$lang[thema_verschieben]</span></a>\n";
		//}
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
	global $po_id, $thread, $seite, $lang, $th_id, $fo_id;
	
	$query = pdoQuery("SELECT `po_th_id`, date_format(from_unixtime(`po_ts`), '%d.%m.%Y, %H:%i:%s') AS `po_date`,
			`po_titel`, `po_text`, `po_u_id`, ifnull(`u_nick`, 'Nobody') AS `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_chathomepage`
			FROM `forum_beitraege` LEFT JOIN `user` ON `po_u_id` = `u_id` WHERE `po_id` = :po_id", [':po_id'=>$thread]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$row = $query->fetch();
	}
	
	$query = pdoQuery("SELECT `fo_name`, `th_name` FROM `forum_kategorien` LEFT JOIN `forum_foren` ON `fo_id` = `th_fo_id` WHERE `th_id` = :th_id", [':th_id'=>$th_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$row2 = $query->fetch();
	}
	
	$text = "";
	
	$text .= "<form action=\"forum.php\" method=\"post\">\n";
	$text .= "<table style=\"width:100%\">\n";
	
	
	// Überschrift: Thema verschieben
	$value = $lang['verschieben_verschiebe_thema'] . " " . $row['po_titel'];
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $value, "", "", 0, "70", "");
	
	
	// Verschieben vom Forum
	$value = $row2['fo_name'] . " > " . $row2['th_name'];
	$text .= zeige_formularfelder("text", $zaehler, $lang['verschieben_von_forum'], "", $value);
	$zaehler++;
	
	
	// Verschieben nach Forum
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['verschieben_nach_forum'] . "</td>\n";
	$text .= "<td $bgcolor>";
	$text .= "<select name=\"verschiebe_nach\" size=\"1\">\n";
	
	$query = pdoQuery("SELECT `fo_name`, `th_id`, `th_name` FROM `forum_kategorien` LEFT JOIN `forum_foren` ON `fo_id` = `th_fo_id` WHERE `th_name` <> 'dummy-thema' ORDER BY `fo_order`, `th_order`", []);
	
	$result = $query->fetchAll();
	foreach($result as $zaehler => $row3) {
		$text .= "<option ";
		if ($row3['th_id'] == $th_id) {
			$text .= "selected ";
		}
		$text .= "value=\"$row3[th_id]\">$row3[fo_name] > $row3[th_name]</option>";
	}
	$text .= "</select>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	// Verschieben
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[verschieben4]\"></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	$text .= "</table>\n";
		
	$text .= "<input type=\"hidden\" name=\"thread_verschiebe\" value=\"$thread\">\n";
	$text .= "<input type=\"hidden\" name=\"seite\" value=\"$seite\">\n";
	$text .= "<input type=\"hidden\" name=\"verschiebe_von\" value=\"$th_id\">\n";
	$text .= "<input type=\"hidden\" name=\"fo_id\" value=\"$fo_id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"verschiebe_posting_ausfuehren\">\n";
	$text .= "</form>\n";
	
	return $text;
}

// Zeigt das Thema an
function show_posting($th_id) {
	global $thread, $seite, $lang, $forum_admin, $u_id;
	
	$query = pdoQuery("SELECT `po_th_id`, date_format(from_unixtime(`po_ts`), '%d.%m.%Y, %H:%i:%s') AS `po_date`,
			`po_titel`, `po_text`, `po_u_id`, ifnull(`u_nick`, 'Nobody') AS `u_nick`, `u_id`, `u_level`
			FROM `forum_beitraege` LEFT JOIN `user` ON `po_u_id` = `u_id` WHERE `po_id` = :po_id", [':po_id'=>$th_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount = 1) {
		$row = $query->fetch();
		$th_id = $row['po_th_id'];
		$po_titel = $row['po_titel'];
	} else {
		$th_id = 0;
		$row['po_u_id'] = 0;
		$row['po_date'] = "";
		$po_titel = "";
	}
	
	$po_text = ersetze_smilies(chat_parse(nl2br($row['po_text'])));
	if (($row['u_nick'] != "Nobody") && ($row['u_level'] <> "Z")) {
		$autor = zeige_userdetails($row['po_u_id']);
	} else {
		$autor = $row['u_nick'];
	}
	
	$text = show_pfad_posting($th_id, $po_titel);
	
	$text .= navigation_thema($po_titel, $row['po_u_id'], $th_id, TRUE);
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Thema
	$text .= zeige_formularfelder("ueberschrift", $zaehler, html_entity_decode($po_titel), "", "", 0, "70", "");
	
	
	// Vom Benutzer gelesene Beiträge holen
	$query = pdoQuery("SELECT `u_gelesene_postings` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		$gelesene = $result['u_gelesene_postings'];
	}
	$u_gelesene = unserialize($gelesene);
	
	$query = pdoQuery("SELECT `po_id`, `po_th_id`, date_format(from_unixtime(`po_ts`), '%d.%m.%Y, %H:%i:%s') AS `po_date`,
			`po_titel`, `po_text`, `po_u_id`, `u_nick`, `u_level`, `po_threadorder`
			FROM `forum_beitraege` LEFT JOIN `user` ON `po_u_id` = `u_id` WHERE `po_id` = :po_id OR `po_vater_id` = :po_vater_id", [':po_id'=>$thread, ':po_vater_id'=>$thread]);
	
	$result = $query->fetchAll();
	foreach($result as $zaehler => $beitrag) {
		$po_text = ersetze_smilies(chat_parse(nl2br( $beitrag['po_text'] )));
		
		if (!@in_array($beitrag['po_id'], $u_gelesene[$th_id])) {
			$col = ' <span class="fa-solid fa-star icon16" alt="ungelesener Beitrag" title="ungelesener Beitrag"></span>';
		} else {
			$col = '';
		}
		$besonderer_status = $col;
		
		if (!$beitrag['u_nick']) {
			$userdetails = "gelöschter Benutzer";
		} else {
			$userdata = array();
			
			$userlink = zeige_userdetails($beitrag['po_u_id']);
			if ($beitrag['u_level'] == 'Z') {
				$userdetails = $userdata['u_nick'];
			} else {
				$userdetails = $userlink;
			}
		}
		
		$query = pdoQuery("SELECT `ui_geschlecht` FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$beitrag['po_u_id']]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			$ui_gen = $result['ui_geschlecht'];
		} else {
			$ui_gen = '0';
		}
		
		// Avatar
		$ava = avatar_anzeigen($beitrag['po_u_id'], $beitrag['u_nick'], "forum", $ui_gen);
		
		$text .= "<tr>\n";
		$text .= "<td rowspan=\"3\" class=\"tabelle_kopfzeile smaller\" style=\"width:150px; vertical-align:top; text-align:center;\">\n";
		$text .= "$ava <br>\n";
		$text .= $userdetails . $besonderer_status;
		$text .= "</td>\n";
		$text .= "<td class=\"tabelle_kopfzeile\">" . $lang['geschrieben_am'] . $beitrag['po_date'] . "</td>\n";
		$text .= "</tr>\n";
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_zeile1\">" . html_entity_decode($po_text) . "</td>\n";
		$text .= "</tr>\n";
		
		$text .= "<tr>\n";
		$text .= "<td class=\"tabelle_kopfzeile\" style=\"text-align:right;\">\n";
		
		$schreibrechte = pruefe_schreibrechte($beitrag['po_th_id']);
		// Darf der Benutzer den Beitrag bearbeiten?
		// Entweder eigenes posting oder forum_admin
		if ((($u_id == $beitrag['po_u_id']) || ($forum_admin)) && ($schreibrechte)) {
			$text .= "<a href=\"forum.php?th_id=" . $beitrag['po_th_id'] . "&po_id=" . $beitrag['po_id'] . "&thread=$thread&aktion=edit&seite=$seite\" class=\"button\" title=\"$lang[thema_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[thema_editieren]</span></a>\n";
		}
		
		if ($schreibrechte) {
			$text .= "<a href=\"forum.php?th_id=" . $beitrag['po_th_id'] . "&po_vater_id=$thread&thread=$thread&aktion=reply&seite=$seite\" class=\"button\" title=\"$lang[thema_zitieren]\"><span class=\"fa-solid fa-quote-right icon16\"></span> <span>$lang[thema_zitieren]</span></a>\n";
		}
		
		//Nur Forum-Admins dürfen Beiträge löschen
		if ($forum_admin) {
			$text .= "<a href=\"forum.php?th_id=" . $beitrag['po_th_id'] . "&po_id=" . $beitrag['po_id'] . "&thread=$thread&aktion=delete_posting&seite=$seite\" onClick=\"return ask('$lang[thema_loeschen2]')\" class=\"button\" title=\"$lang[thema_loeschen]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[thema_loeschen]</span></a>\n";
		}
		$text .= "</td>\n";
		$text .= "</tr>\n";
	}
	
	$text .= "</table>\n";
	
	$text .= navigation_thema($po_titel, $beitrag['po_u_id'], $th_id, FALSE);
	$text .= show_pfad_posting($th_id, $po_titel);
	
	$text .= "</table>\n";
	
	
	return $text;
}
?>