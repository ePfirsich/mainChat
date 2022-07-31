<?php
// Eingabemaske für Kategorien
function maske_kategorie($kat_id, $kat_name, $kat_admin) {
	global $lang;
	
	if ($kat_id > 0) {
		$query = pdoQuery("SELECT `kat_name`, `kat_admin` FROM `forum_kategorien` WHERE `kat_id` = :kat_id", [':kat_id'=>$kat_id]);
		$result = $query->fetch();
		
		if($kat_name == "") {
			$kat_name = $result['kat_name'];
		}
		
		if($kat_admin == 0) {
			$kat_admin = $result['kat_admin'];
		}
	}
	
	$zaehler = 0;
	$text = "";
	$text .= "<table style=\"width:100%;\">\n";
	$text .= "<form action=\"forum.php?bereich=forum\" method=\"post\">\n";
	
	// Name der Kategorie
	$text .= zeige_formularfelder("input", $zaehler, $lang['forum_kategorie_name'], "kat_name",  $kat_name);
	$zaehler++;
	
	// Gäste dürfen
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	
	$selg1 = "";
	$selg2 = "";
	if ($kat_admin != "" && ($kat_admin & 8) == 8) {
		$selg1 = "selected";
		$selg2 = "";
	}
	if ($kat_admin != "" && ($kat_admin & 16) == 16) {
		$selg1 = "";
		$selg2 = "selected";
	}
	// Standardeinstellung
	if($kat_admin == 0) {
		$selg1 = "";
		$selg2 = "selected";
	}
	
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['forum_berechtigungen_gaeste_duerfen'] . "</td>\n";
	$text .= "<td $bgcolor>\n";
	$text .= "<select size=\"1\" name=\"kat_gast\">\n";
	$text .= "<option value=\"0\">$lang[forum_berechtigungen_weder_lesen_noch_schreiben]</option>\n";
	$text .= "<option value=\"8\" $selg1>$lang[forum_berechtigungen_nur_lesen]</option>\n";
	$text .= "<option value=\"24\" $selg2>$lang[forum_berechtigungen_lesen_noch_schreiben]</option>\n";
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
	if ($kat_admin != "" && ($kat_admin & 2) == 2) {
		$selu1 = "selected";
		$selu2 = "";
	}
	if ($kat_admin != "" && ($kat_admin & 4) == 4) {
		$selu1 = "";
		$selu2 = "selected";
	}
	// Standardeinstellung
	if($kat_admin == 0) {
		$selu1 = "selected";
		$selu2 = "";
	}
	
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['forum_berechtigungen_benutzer_duerfen'] . "</td>\n";
	$text .= "<td $bgcolor>\n";
	$text .= "<select size=\"1\" name=\"kat_user\">\n";
	$text .= "<option value=\"0\">$lang[forum_berechtigungen_weder_lesen_noch_schreiben]</option>\n";
	$text .= "<option value=\"2\" $selu1>$lang[forum_berechtigungen_nur_lesen]</option>\n";
	$text .= "<option value=\"6\" $selu2>$lang[forum_berechtigungen_lesen_noch_schreiben]</option>\n";
	$text .= "</select>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	// Kategorie speichern
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[forum_button_speichern]\"></td>\n";
	$text .= "</tr>\n";
	
	$text .= "</table>\n";
	
	$text .= "<input type=\"hidden\" name=\"kat_id\" value=\"$kat_id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"kategorie_aendern\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	
	$text .= "</form>\n";
	
	
	return $text;
}

// Listet alle Foren mit den Anzahl Themen auf
function forum_liste() {
	global $forum_admin, $chat_grafik, $lang, $u_level;
	
	if ($u_level == "G") {
		$query = pdoQuery("SELECT `kat_id`, `kat_name`, `kat_order` FROM `forum_kategorien` WHERE ((`kat_admin` & 8) = 8) OR `kat_admin` = 0 ORDER BY `kat_order`", []);
	} else if ($u_level == "U" || $u_level == "A" || $u_level == "M" || $u_level == "Z") {
		$query = pdoQuery("SELECT `kat_id`, `kat_name`, `kat_order` FROM `forum_kategorien` WHERE ((`kat_admin` & 2) = 2) OR `kat_admin` = 0 ORDER BY `kat_order`", []);
	} else {
		// Superuser und Chat-Admin
		$query = pdoQuery("SELECT `kat_id`, `kat_name`, `kat_order` FROM `forum_kategorien` ORDER BY `kat_order`", []);
	}
	
	$queryCount = $query->rowCount();
	
	//kat_id merken zur Darstellung des Kopfes
	if ($queryCount > 0) {
		$text = "";
		
		// Funktioniert noch nicht
		//$text .= "<a href=\"forum.php?bereich=forum&aktion=foren_als_gelesen\" class=\"button\" title=\"$lang[forum_foren_gelesen_markieren]\"><span class=\"fa-solid fa-check icon16\"></span> <span>$lang[forum_foren_gelesen_markieren]</span></a>\n";
		
		$text .= "<table style=\"width:100%\">\n";
		
		$result = $query->fetchAll();
		foreach($result as $zaehler => $kategorie) {
			$text .= "<tr>\n";
			$text .= "<td colspan=\"6\">&nbsp;</td>";
			$text .= "</tr>\n";
			
			$text .= "<tr>\n";
			$text .= "<td colspan=\"2\" class =\"tabelle_kopfzeile\">&nbsp;&nbsp;&nbsp;" .html_entity_decode($kategorie['kat_name']) . "<a name=\"" . $kategorie['kat_id'] . "\"></a></td>\n";
			if ($forum_admin) {
				$text .= "<td class =\"tabelle_kopfzeile\" style=\"width:300px; text-align:right; vertical-align:middle;\">\n";
				$text .= "<a href=\"forum.php?bereich=forum&aktion=kategorie_aendern&kat_id=$kategorie[kat_id]\" class=\"button\" title=\"$lang[forum_button_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[forum_button_editieren]</span></a>\n";
				$text .= "<a href=\"forum.php?bereich=forum&aktion=kategorie_delete&kat_id=$kategorie[kat_id]\" onClick=\"return ask('$lang[forum_kategorie_loeschen]')\" class=\"button\" title=\"$lang[forum_button_loeschen]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[forum_button_loeschen]</span></a>\n";
				$text .= "<a href=\"forum.php?bereich=forum&forum_kategorie_id=$kategorie[kat_id]&aktion=forum_aendern\" class=\"button\" title=\"$lang[forum_button_neues_forum]\"><span class=\"fa-solid fa-plus icon16\"></span> <span>$lang[forum_button_neues_forum]</span></a>\n";
				$text .= "</td>\n";
				
				$text .= "<td class =\"tabelle_kopfzeile\" style=\"width:80px; text-align:center;\">\n";
				$text .= "<a href=\"forum.php?bereich=forum&kat_id=$kategorie[kat_id]&kat_order=$kategorie[kat_order]&aktion=kategorie_up\" class=\"button\"><span class=\"fa-solid fa-arrow-up icon16\"></span></a> ";
				$text .= "<a href=\"forum.php?bereich=forum&kat_id=$kategorie[kat_id]&kat_order=$kategorie[kat_order]&aktion=kategorie_down\" class=\"button\"><span class=\"fa-solid fa-arrow-down icon16\"></span></a>\n";
				$text .= "</td>\n";
			} else {
				$text .= "<td class=\"tabelle_kopfzeile\" style=\"width:300px;\">&nbsp;</td>";
				$text .= "<td class=\"tabelle_kopfzeile\" style=\"width:50px;\">&nbsp;</td>";
			}
			$text .= "<td class=\"tabelle_kopfzeile\" style=\"width:120px; text-align:center;\">&nbsp;</td>\n";
			$text .= "</tr>\n";
			
			
			$query2 = pdoQuery("SELECT `forum_id`, `forum_kategorie_id`, `forum_name`, `forum_beschreibung`, `forum_anzahl_themen`, `forum_anzahl_antworten`, `forum_order`, `forum_beitraege` FROM `forum_foren` WHERE `forum_kategorie_id` = :forum_kategorie_id ORDER BY `forum_order`", [':forum_kategorie_id'=>$kategorie['kat_id']]);
			
			$result2 = $query2->fetchAll();
			foreach($result2 as $zaehler2 => $forum) {
				if ($zaehler2 % 2) {
					$farbe = 'class="tabelle_zeile1"';
				} else {
					$farbe = 'class="tabelle_zeile2"';
				}
				
				if ($forum['forum_beitraege']) {
					$arr_posting = explode(",", $forum['forum_beitraege']);
				} else {
					$arr_posting = array();
				}
				$ungelesene = anzahl_ungelesener_themen($arr_posting, $forum['forum_id']);
				
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
					$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum[forum_id]&aktion=show_forum\">\n";
					$text .= html_entity_decode($forum['forum_name']) . "</a><br><span class=\"smaller\"> " . html_entity_decode($forum['forum_beschreibung']) . "</span>\n";
					$text .= "</td>\n";
					
					$text .= "<td style=\"width:170px; text-align:center; vertical-align:middle;\" $farbe>\n";
					$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum[forum_id]&forum_kategorie_id=$kategorie[kat_id]&aktion=forum_aendern\" class=\"button\" title=\"$lang[forum_button_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[forum_button_editieren]</span></a>\n";
					$text .= "<a href=\"forum.php?bereich=forum&aktion=forum_delete&forum_id=$forum[forum_id]\" onClick=\"return ask('$lang[forum_loeschen]')\" class=\"button\" title=\"$lang[forum_button_loeschen]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[forum_button_loeschen]</span></a>\n";
					$text .= "</td>\n";
					
					$text .= "<td style=\"text-align:center;\" $farbe>\n";
					$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum[forum_id]&kat_id=$kategorie[kat_id]&forum_order=$forum[forum_order]&aktion=forum_up\" class=\"button\"><span class=\"fa-solid fa-arrow-up icon16\"></span></a> ";
					$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum[forum_id]&kat_id=$kategorie[kat_id]&forum_order=$forum[forum_order]&aktion=forum_down\" class=\"button\"><span class=\"fa-solid fa-arrow-down icon16\"></span></a>\n";
					$text .= "</td>\n";
				} else {
					$text .= "<td style=\"font-weight:bold; padding-left:10px;\" $farbe>\n";
					$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum[forum_id]&aktion=show_forum\">" . html_entity_decode($forum['forum_name']) . "</a><br>\n";
					$text .= "<span class=\"smaller\"> " . html_entity_decode($forum['forum_beschreibung']) . "</span></td>\n";
					
					$text .= "<td $farbe>&nbsp;</td>";
					$text .= "<td $farbe>&nbsp;</td>";
				}
				$text .= "<td style=\"padding-left:10px;\" $farbe>";
				$text .= "<span class=\"fa-solid fa-comment icon16\"></span> $forum[forum_anzahl_themen] $lang[forum_kategorie_themen]<br>";
				$text .= "<span class=\"fa-solid fa-pencil icon16\"></span> " . ( $forum['forum_anzahl_antworten'] + $forum['forum_anzahl_themen']) . " $lang[forum_kategorie_beitraege]";
				$text .= "</td>\n";
				$text .= "</tr>\n";
			}
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

// Eingabemaske für Foren
function maske_forum($forum_id, $forum_kategorie_id, $forum_name, $forum_beschreibung) {
	global $lang;
	
	if ($forum_id > 0) {
		$query = pdoQuery("SELECT `forum_name`, `forum_beschreibung`, `forum_kategorie_id` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
		$result = $query->fetch();
		
		$forum_kategorie_id = $result['forum_kategorie_id'];
		
		if($forum_name == "") {
			$forum_name = $result['forum_name'];
			//$forum_name = html_entity_decode($result['forum_name']);
		}
		
		if($forum_beschreibung == "") {
			$forum_beschreibung = $result['forum_beschreibung'];
		}
	}
	
	$zaehler = 0;
	$text = "";
	$text .= "<form action=\"forum.php?bereich=forum\" method=\"post\">\n";
	$text .= "<table style=\"width:100%;\">\n";
	
	// Name des Forums
	$text .= zeige_formularfelder("input", $zaehler, $lang['forum_name'], "forum_name", $forum_name);
	$zaehler++;
	
	// Beschreibung des Forums
	$text .= zeige_formularfelder("textarea", $zaehler, $lang['forum_beschreibung'], "forum_beschreibung", $forum_beschreibung);
	$zaehler++;
	
	// Kategorie des Forums
	if ($forum_id > 0) {
		if ($zaehler % 2 != 0) {
			$bgcolor = 'class="tabelle_zeile2"';
		} else {
			$bgcolor = 'class="tabelle_zeile1"';
		}
		
		$query = pdoQuery("SELECT `kat_id`, `kat_name` FROM `forum_kategorien` ORDER BY `kat_order`", []);
		$result = $query->fetchAll();
		$selectbox = "<select name=\"forum_kategorie_id\" size=\"1\">\n";
		foreach($result as $zaehler => $row) {
			$selectbox .= "<option ";
			if ($row['kat_id'] == $forum_kategorie_id) {
				$selectbox .= "selected ";
			}
			$selectbox .= "value=\"$row[kat_id]\">$row[kat_name]</option>";
		}
		$selectbox .= "</select>\n";
		
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right;\" $bgcolor>" . $lang['forum_kategorie'] . "</td>\n";
		$text .= "<td $bgcolor>$selectbox</td>\n";
		$text .= "</tr>\n";
		$zaehler++;
	}
	
	
	// Forum speichern
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
	
	if ($forum_id == 0) {
		$text .= "<input type=\"hidden\" name=\"forum_kategorie_id\" value=\"$forum_kategorie_id\">\n";
	}
	$text .= "<input type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"forum_aendern\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "</form>\n";
	
	
	return $text;
}

//Zeigt Pfad und Seiten in Themaliste an
function show_pfad($forum_id, $kat_id, $kat_name, $forum_name, $forum_anzahl_themen) {
	global $anzahl_po_seite, $seite, $lang;
	
	$text = "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"smaller\"><a href=\"forum.php?bereich=forum#$kat_id\">" . html_entity_decode($kat_name) . "</a> > <a href=\"forum.php?bereich=forum&forum_id=$forum_id&aktion=show_forum&seite=$seite\">" . html_entity_decode($forum_name) . "</a></td>\n";
	
	if (!$anzahl_po_seite || $anzahl_po_seite == 0) {
		$anzahl_po_seite = 20;
	}
	$anz_seiten = ceil(($forum_anzahl_themen / $anzahl_po_seite));
	if ($anz_seiten > 1) {
			$text .= "<td style=\"text-align:right;\" class=\"smaller\">$lang[page] ";
			for ($page = 1; $page <= $anz_seiten; $page++) {
				if ($page == $seite) {
					$col = "font-weight:bold;";
				} else {
					$col = '';
				}
				$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&aktion=show_forum&seite=$page\"><span style=\"$col;\">$page</span></a> ";
			}
			$text .= "</td></tr>\n";
	} else {
		$text .= "</tr>\n";
	}
	$text .= "</table>\n";
	
	return $text;
}

//Zeigt ein Forum mit allen Themen an
function show_forum($forum_id) {
	global $forum_admin, $seite, $anzahl_po_seite, $chat_grafik, $lang, $admin, $locale;
	
	$leserechte = pruefe_leserechte($forum_id);
	
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
	$query2 = pdoQuery("SELECT `beitrag_id`, `beitrag_user_id`, date_format(from_unixtime(`beitrag_thema_timestamp`), '%d. %M %Y') AS `po_date`, date_format(from_unixtime(`beitrag_antwort_timestamp`), '%d. %M %Y') AS `po_date2`,
			`beitrag_titel`, `beitrag_angepinnt`, `beitrag_gesperrt`, `u_nick`,
			`u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_punkte_anzeigen`, `u_nachrichten_empfangen`, `u_chathomepage` FROM `forum_beitraege` LEFT JOIN `user` ON `beitrag_user_id` = `u_id`
			WHERE `beitrag_thema_id` = 0 AND `beitrag_forum_id` = :beitrag_forum_id ORDER BY `beitrag_angepinnt` DESC, `beitrag_antwort_timestamp` DESC, `beitrag_thema_timestamp` DESC LIMIT :limit_start, :limit_end", [':beitrag_forum_id'=>$forum_id, ':limit_start'=>$offset, ':limit_end'=>$anzahl_po_seite]);
	$result2 = $query2->fetchAll();
	
	//Infos über Forum und Thema holen
	$query = pdoQuery("SELECT `kat_id`, `kat_name`, `forum_name`, `forum_anzahl_themen` FROM `forum_kategorien`, `forum_foren` WHERE `forum_id` = :forum_id AND `kat_id` = `forum_kategorie_id`", [':forum_id'=>$forum_id]);
	
	$result = $query->fetch();
	$kat_name = htmlspecialchars($result['kat_name']);
	$forum_name = htmlspecialchars($result['forum_name']);
	$forum_anzahl_themen = $result['forum_anzahl_themen'];
	
	$text = "";
	
	$text .= show_pfad($forum_id, $result['kat_id'], $kat_name, $forum_name, $forum_anzahl_themen);
	
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"5\" class=\"tabelle_kopfzeile\">" . html_entity_decode($forum_name) . "</td>\n";
	$text .= "<td style=\"text-align:right;\" class=\"tabelle_kopfzeile\">\n";
	
	$schreibrechte = pruefe_schreibrechte($forum_id);
	if ($schreibrechte) {
		$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&aktion=thema_aendern\" class=\"button\" title=\"$lang[forum_thema_erstellen]\"><span class=\"fa-solid fa-plus icon16\"></span> <span>$lang[forum_thema_erstellen]</span></a>\n";
	} else {
		$text .= "<span class=\"smaller\">$lang[nur_leserechte]</span><br>\n";
	}
	$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&aktion=forum_als_gelesen\" class=\"button\" title=\"$lang[forum_forum_gelesen_markieren]\"><span class=\"fa-solid fa-check icon16\"></span> <span>$lang[forum_forum_gelesen_markieren]</span></a>\n";
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "<tr>\n";
	$text .= "<td colspan=\"2\" class=\"tabelle_kopfzeile\">&nbsp;</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$lang[geschrieben_von]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$lang[forum_thema_erstellt_am]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$lang[forum_anzahl_antworten]</td>\n";
	$text .= "<td style=\"text-align:center;\" class=\"tabelle_kopfzeile\">$lang[forum_letzte_Antwort]</td>\n";
	$text .= "</tr>\n";
	
	foreach($result2 as $zaehler => $posting) {
		set_time_limit(0);
		
		$queryrepyls = pdoQuery("SELECT `beitrag_id` FROM `forum_beitraege` WHERE `beitrag_thema_id` = :beitrag_thema_id", [':beitrag_thema_id'=>$posting['beitrag_id']]);
		
		// Anzahl an Antworten
		$anzreplys = $queryrepyls->rowCount();
		
		// Alle Beitraäge eines Themas (inklusive Thema) in einem Array
		$replys = $queryrepyls->fetchAll();
		$arr_postings = array();
		// Startbeitrag (Thema)
		$arr_postings [] = $posting['beitrag_id'];
		// Alle Antworten auf dieses Thema hinzufügen
		foreach($replys as $zaehlerX => $reply) {
			$arr_postings [] = $reply['beitrag_id'];
		}
		
		if ($zaehler % 2) {
			$farbe = 'class="tabelle_zeile1"';
		} else {
			$farbe = 'class="tabelle_zeile2"';
		}
		
		$ungelesene = anzahl_ungelesene($arr_postings, $forum_id);
		
		if ($ungelesene === 0) {
			if ($posting['beitrag_angepinnt'] == 1) { // Top Posting 
				$folder = $chat_grafik['forum_topthema'];
			} else if ($posting['beitrag_gesperrt'] == 1) { // Geschlossenes Thema
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
		$text .= "<td $farbe class=\"smaller\">&nbsp;<b><a href=\"forum.php?bereich=forum&forum_id=$forum_id&beitrag_id=$posting[beitrag_id]&&aktion=zeige_thema&seite=$seite\">" . html_entity_decode(substr($posting['beitrag_titel'], 0, 40)) . "</a></b></td>\n";
		
		if (!$posting['u_nick']) {
			$text .= "<td $farbe><span class=\"smaller\"><b>Nobody</b></span></td>\n";
		} else {
			$userlink = zeige_userdetails($posting['beitrag_user_id']);
			if ($posting['u_level'] == 'Z') {
				$text .= "<td $farbe><span class=\"smaller\">$userdata[u_nick]</span></td>\n";
			} else {
				$text .= "<td $farbe><span class=\"smaller\">$userlink</span></td>\n";
			}
		}
		
		if ($posting['po_date2'] == '01.01.70' || $posting['po_date'] == $posting['po_date2']) {
			$antworten = "";
		} else {
			$themen_id = $posting['beitrag_id'];
			$query3 = pdoQuery("SELECT `beitrag_user_id` FROM `forum_beitraege` WHERE `beitrag_thema_id` = :beitrag_thema_id ORDER by `beitrag_id` DESC LIMIT 1", [':beitrag_thema_id'=>$themen_id]);
			$result3 = $query3->fetch();
			
			$antworten_userlink = zeige_userdetails($result3['beitrag_user_id']);
			
			$antworten = $posting['po_date2'] . " von " . $antworten_userlink;
		}
		$text .= "<td style=\"text-align:center;\" $farbe><span class=\"smaller\">$posting[po_date]</span></td>\n"; // Wann wurde das Thema erstellt
		$text .= "<td style=\"text-align:center;\" $farbe><span class=\"smaller\">".$anzreplys."</span></td>\n"; // Wie viele Antworten hat das Thema
		$text .= "<td style=\"text-align:center;\" $farbe><span class=\"smaller\">$antworten</span></td>\n"; // Letzte Anwort von wem und wann
	}
	$text .= "</table>\n";
	$text .= show_pfad($forum_id, $result['kat_id'], $kat_name, $forum_name, $forum_anzahl_themen);
	$text .= show_icon_description();
	
	return $text;
}

//Maske zum Eingeben/Editieren/Quoten von Beiträgen
function maske_posting($mode) {
	global $u_id, $forum_id, $beitrag_id, $beitrag_thema_id, $beitrag_titel, $beitrag_text, $seite, $forum_admin, $lang;
	
	// Hole alle benötigten Einstellungen des Benutzers
	$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");
	
	switch ($mode) {
		case "reply": // zitieren
			//Daten des Vaters holen
			$query = pdoQuery("SELECT date_format(from_unixtime(`beitrag_thema_timestamp`), '%d.%m.%Y, %H:%i:%s') AS `po_date`, `beitrag_titel`, `beitrag_text`, ifnull(u_nick, 'unknown') AS `u_nick`
					FROM `forum_beitraege` LEFT JOIN `user` ON `beitrag_user_id` = `u_id` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_thema_id]);
			
			$result = $query->fetch();
			$autor = $result['u_nick'];
			$beitrag_titel = $result['beitrag_titel'];
			if (substr($beitrag_titel, 0, 3) != $lang['reply']) {
				$beitrag_titel = $lang['reply'] . " " . $result['po_date'];
			}
			$titel = $beitrag_titel;
			$beitrag_text = $result['beitrag_text'];
			$beitrag_text = erzeuge_quoting($beitrag_text, $autor, $result['po_date']);
			$beitrag_text = erzeuge_fuss($beitrag_text);
		break;
		
		case "answer": // antworten
			//Daten des Vaters holen
			$query = pdoQuery("SELECT `beitrag_titel` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_thema_id]);
			
			$result = $query->fetch();
			$beitrag_titel = $result['beitrag_titel'];
			if (substr($beitrag_titel, 0, 3) != $lang['reply']) {
				$beitrag_titel = $lang['reply'] . " " . $beitrag_titel;
			}
			$titel = $beitrag_titel;
			$beitrag_text = erzeuge_fuss("");
		break;
	}
	
	
	$button = $lang['neuer_thread_button'];
	
	$text = "<form name=\"form\" action=\"forum.php?bereich=forum\" method=\"post\">";
	$text .= show_pfad_posting($forum_id, $titel);
	
	
	$text .= "<input type=\"hidden\" name=\"beitrag_titel\" value=\"$beitrag_titel\">\n";
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	// Überschrift: Neues Thema erstellen
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $titel, "", "", 0, "70", "");
	
	
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
	$zaehler++;
	
	
	// Smilies
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right; vertical-align:top;\" $bgcolor>" . zeige_smilies('forum', $benutzerdaten) . "</td>\n";
	$text .= "<td $bgcolor><textarea name=\"beitrag_text\" rows=\"15\" cols=\"95\" wrap=\"physical\">$beitrag_text</textarea></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
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
	
	
	$text .= show_pfad_posting($forum_id, $titel);
	
	$text .= "<input type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n";
	$text .= "<input type=\"hidden\" name=\"beitrag_id\" value=\"$beitrag_thema_id\">\n";
	$text .= "<input type=\"hidden\" name=\"beitrag_thema_id\" value=\"$beitrag_thema_id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"$mode\">";
	$text .= "<input type=\"hidden\" name=\"seite\" value=\"$seite\">";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "</form>";
	
	return $text;
}

// Eingabemaske für Themen
function maske_thema($beitrag_id, $beitrag_titel, $beitrag_text, $beitrag_forum_id, $beitrag_angepinnt, $beitrag_gesperrt, $u_layout_farbe) {
	global $seite, $forum_admin, $lang;
	
	// Wird für die korrekte Anzeige der Smilies benötigt
	$benutzerdaten['u_layout_farbe'] = $u_layout_farbe;
	
	if ($beitrag_id > 0) {
		$query = pdoQuery("SELECT `beitrag_titel`, `beitrag_forum_id`, `beitrag_angepinnt`, `beitrag_gesperrt` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
		$result = $query->fetch();
		
		$beitrag_forum_id = $result['beitrag_forum_id'];
		
		if($beitrag_titel == "") {
			$beitrag_titel = $result['beitrag_titel'];
		}
		
		if($beitrag_angepinnt == "") {
			$beitrag_angepinnt = $result['beitrag_angepinnt'];
		}
		
		if($beitrag_gesperrt == "") {
			$beitrag_gesperrt = $result['beitrag_gesperrt'];
		}
		
		$titel = $beitrag_titel;
	} else {
		$titel = $lang['forum_kopfzeile_thema_erstellen'];
		
		if (!$beitrag_text) {
			$beitrag_text = erzeuge_fuss("");
		}
	}
	
	$text = "<form name=\"form\" action=\"forum.php?bereich=forum\" method=\"post\">";
	$text .= show_pfad_posting($beitrag_forum_id, $titel);
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	
	// Titel
	$text .= zeige_formularfelder("input", $zaehler, $lang['forum_titel'], "beitrag_titel", $beitrag_titel);
	$zaehler++;
	
	
	if ($beitrag_id == 0) {
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
		$zaehler++;
	
	
		// Smilies
		$text .= "<tr>\n";
		$text .= "<td style=\"text-align:right; vertical-align:top;\" $bgcolor>" . zeige_smilies('forum', $benutzerdaten) . "</td>\n";
		$text .= "<td $bgcolor><textarea name=\"beitrag_text\" rows=\"15\" cols=\"95\" wrap=\"physical\">$beitrag_text</textarea></td>\n";
		$text .= "</tr>\n";
		$zaehler++;
	}
	
	
	if ($forum_admin) {
		// Thema anpinnen
		$value = array($lang['posting_nein'], $lang['posting_ja']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $lang['posting_thema_anpinnen'], "beitrag_angepinnt", $value, $beitrag_angepinnt);
		$zaehler++;
		
		
		// Thema gesperrt
		$value = array($lang['posting_nein'], $lang['posting_ja']);
		$text .= zeige_formularfelder("selectbox", $zaehler, $lang['posting_thema_gesperrt'], "beitrag_gesperrt", $value, $beitrag_gesperrt);
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
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[forum_button_speichern]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	$text .= "<input type=\"hidden\" name=\"beitrag_id\" value=\"$beitrag_id\">\n";
	$text .= "<input type=\"hidden\" name=\"forum_id\" value=\"$beitrag_forum_id\">\n";
	$text .= "<input type=\"hidden\" name=\"beitrag_thema_id\" value=\"0\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"thema_aendern\">\n";
	$text .= "<input type=\"hidden\" name=\"seite\" value=\"$seite\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "</form>";
	
	
	$text .= show_pfad_posting($beitrag_forum_id, $titel);
	
	return $text;
}

// Beitrag editieren
function maske_beitrag_editieren($beitrag_id, $beitrag_text, $forum_id, $u_layout_farbe) {
	global $seite, $forum_admin, $lang;
	
	// Wird für die korrekte Anzeige der Smilies benötigt
	$benutzerdaten['u_layout_farbe'] = $u_layout_farbe;
	
	$query = pdoQuery("SELECT `beitrag_titel`, `beitrag_text`, `beitrag_forum_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
	$result = $query->fetch();
	
	if($beitrag_text == "") {
		$beitrag_text = $result['beitrag_text'];
	}
	
	$beitrag_forum_id = $result['beitrag_forum_id'];
	
	$titel = $result['beitrag_titel'];
	
	$text = "<form name=\"form\" action=\"forum.php?bereich=forum\" method=\"post\">";
	$text .= show_pfad_posting($beitrag_forum_id, $titel);
	
	$zaehler = 0;
	$text .= "<table style=\"width:100%;\">\n";
	
	
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
	$zaehler++;
	
	
	// Smilies
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right; vertical-align:top;\" $bgcolor>" . zeige_smilies('forum', $benutzerdaten) . "</td>\n";
	$text .= "<td $bgcolor><textarea name=\"beitrag_text\" rows=\"15\" cols=\"95\" wrap=\"physical\">$beitrag_text</textarea></td>\n";
	$text .= "</tr>\n";
	$zaehler++;
	
	
	// Beitrag erstellen/editieren
	if ($zaehler % 2 != 0) {
		$bgcolor = 'class="tabelle_zeile2"';
	} else {
		$bgcolor = 'class="tabelle_zeile1"';
	}
	$text .= "<tr>\n";
	$text .= "<td style=\"text-align:right;\" $bgcolor>&nbsp;</td>\n";
	$text .= "<td $bgcolor><input type=\"submit\" value=\"$lang[forum_button_speichern]\"></td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	$text .= "<input type=\"hidden\" name=\"beitrag_id\" value=\"$beitrag_id\">\n";
	$text .= "<input type=\"hidden\" name=\"forum_id\" value=\"$forum_id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"edit\">\n";
	$text .= "<input type=\"hidden\" name=\"seite\" value=\"$seite\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "</form>";
	
	
	$text .= show_pfad_posting($beitrag_forum_id, $titel);
	
	return $text;
}

//Zeigt die gutgeschriebenen Punkte an
function verbuche_punkte($u_id, $add_or_subtract) {
	global $punkte_pro_posting, $punktefeatures;
	
	$text = "";
	if ($punktefeatures) {
		if($add_or_subtract == "add") {
			// add
			$erfolgsmeldung = punkte_offline($punkte_pro_posting, $u_id);
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
		} else {
			// subtract
			$erfolgsmeldung = punkte_offline($punkte_pro_posting * (-1), $u_id);
			$text .= hinweis($erfolgsmeldung, "erfolgreich");
		}
	}
	
	return $text;
}

//Zeigt Pfad in Beiträgen an
function show_pfad_posting($forum_id, $beitrag_titel) {
	global $seite;
	//Infos über Forum und Thema holen
	$query = pdoQuery("SELECT `kat_id`, `kat_name`, `forum_name` FROM `forum_kategorien`, `forum_foren` WHERE `forum_id` = :forum_id AND `kat_id` = `forum_kategorie_id`", [':forum_id'=>$forum_id]);
	
	$result = $query->fetch();
	$kat_name = $result['kat_name'];
	$forum_name = $result['forum_name'];
	
	$text = "";
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"smaller\"><a href=\"forum.php?bereich=forum\">" . html_entity_decode($kat_name) . "</a> > <a href=\"forum.php?bereich=forum&forum_id=$forum_id&aktion=show_forum&seite=$seite\">" . html_entity_decode($forum_name) . "</a> > " . html_entity_decode($beitrag_titel) . "</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	return $text;
}

//gibt Navigation für Beiträge aus
function navigation_thema($beitrag_thema_id, $beitrag_id, $beitrag_titel, $beitrag_user_id, $forum_id, $ist_navigation_top) {
	global $lang, $seite, $u_id, $forum_admin;
	$text = "";
	
	$text .= "<table style=\"width:100%\">\n";
	$text .= "<tr>\n";
	$text .= "<td class=\"tabelle_kopfzeile\" style=\"text-align:right;\">\n";
	
	$threadgesperrt = ist_thema_gesperrt($forum_id);
	$schreibrechte = pruefe_schreibrechte($forum_id);
	// Darf der Benutzer einen Beitrag bearbeiten?
	// Entweder eigenes posting oder forum_admin
	if ( ((($u_id == $beitrag_user_id && !$threadgesperrt) || $forum_admin) && $schreibrechte) && $ist_navigation_top ) {
		$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&beitrag_id=$beitrag_id&aktion=thema_aendern&seite=$seite\" class=\"button\" title=\"$lang[forum_thema_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[forum_thema_editieren]</span></a>\n";
	}
	
	if ($schreibrechte && !$threadgesperrt && !$ist_navigation_top) {
		$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&beitrag_thema_id=$beitrag_thema_id&aktion=answer&seite=$seite\" class=\"button\" title=\"$lang[forum_thema_antworten]\"><span class=\"fa-solid fa-reply icon16\"></span> <span>$lang[forum_thema_antworten]</span></a>\n";
	}

	//Nur Forum-Admins dürfen Beiträge loeschen
	if ($forum_admin && $ist_navigation_top) {
		if($threadgesperrt) {
			$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&beitrag_id=$beitrag_id&aktion=sperre_thema&seite=$seite\" class=\"button\" title=\"$lang[forum_thema_entsperren]\"><span class=\"fa-solid fa-lock-open icon16\"></span> <span>$lang[forum_thema_entsperren]</span></a>\n";
		} else {
			$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&beitrag_id=$beitrag_id&aktion=sperre_thema&seite=$seite\" class=\"button\" title=\"$lang[forum_thema_sperren]\"><span class=\"fa-solid fa-lock icon16\"></span> <span>$lang[forum_thema_sperren]</span></a>\n";
		}
		$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&beitrag_thema_id=$beitrag_thema_id&beitrag_id=$beitrag_id&aktion=loesche_thema&seite=$seite\" onClick=\"return ask('$lang[forum_thema_loeschen_abfrage]')\" class=\"button\" title=\"$lang[forum_thema_loeschen]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[forum_thema_loeschen]</span></a>\n";
		$text .= "<a href=\"forum.php?bereich=forum&forum_id=$forum_id&beitrag_thema_id=$beitrag_thema_id&beitrag_id=$beitrag_id&aktion=verschiebe_thema&seite=$seite\" class=\"button\" title=\"$lang[forum_thema_verschieben]\"><span class=\"fa-solid fa-arrows icon16\"></span> <span>$lang[forum_thema_verschieben]</span></a>\n";
	}
	$text .= "</td>\n";
	$text .= "</tr>\n";
	$text .= "</table>\n";
	
	// Alle Beiträge automatisch als gelesen markieren, wenn man das Thema aufgerufen hat
	thread_alles_gelesen($forum_id, $beitrag_thema_id, $u_id);
	
	return $text;
}

// Verschiebe Beitrag
function verschiebe_thema($beitrag_id, $forum_id) {
	global $seite, $lang;
	
	$query = pdoQuery("SELECT `beitrag_forum_id`, date_format(from_unixtime(`beitrag_thema_timestamp`), '%d.%m.%Y, %H:%i:%s') AS `po_date`,
			`beitrag_titel`, `beitrag_text`, `beitrag_user_id`, ifnull(`u_nick`, 'Nobody') AS `u_nick`, `u_id`, `u_level`, `u_punkte_gesamt`, `u_punkte_gruppe`, `u_chathomepage`
			FROM `forum_beitraege` LEFT JOIN `user` ON `beitrag_user_id` = `u_id` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$row = $query->fetch();
	}
	
	$query = pdoQuery("SELECT `kat_name`, `forum_name` FROM `forum_kategorien` LEFT JOIN `forum_foren` ON `kat_id` = `forum_kategorie_id` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$row2 = $query->fetch();
	}
	
	$text = "";
	$zaehler = 0;
	
	$text .= "<form action=\"forum.php?bereich=forum\" method=\"post\">\n";
	$text .= "<table style=\"width:100%\">\n";
	
	
	// Überschrift: Thema verschieben
	$value = $lang['verschieben_verschiebe_thema'] . " " . $row['beitrag_titel'];
	$text .= zeige_formularfelder("ueberschrift", $zaehler, $value, "", "", 0, "70", "");
	
	
	// Verschieben vom Forum
	$value = $row2['kat_name'] . " > " . $row2['forum_name'];
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
	$text .= "<select name=\"beitrag_forum_id_neu\" size=\"1\">\n";
	
	$query = pdoQuery("SELECT `kat_name`, `forum_id`, `forum_name` FROM `forum_kategorien` LEFT JOIN `forum_foren` ON `kat_id` = `forum_kategorie_id` ORDER BY `kat_order`, `forum_order`", []);
	
	$result = $query->fetchAll();
	foreach($result as $zaehler => $row3) {
		$text .= "<option ";
		if ($row3['forum_id'] == $forum_id) {
			$text .= "selected ";
		}
		$text .= "value=\"$row3[forum_id]\">$row3[kat_name] > $row3[forum_name]</option>";
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
		
	$text .= "<input type=\"hidden\" name=\"seite\" value=\"$seite\">\n";
	$text .= "<input type=\"hidden\" name=\"beitrag_forum_id\" value=\"$forum_id\">\n";
	$text .= "<input type=\"hidden\" name=\"beitrag_id\" value=\"$beitrag_id\">\n";
	$text .= "<input type=\"hidden\" name=\"aktion\" value=\"verschiebe_thema\">\n";
	$text .= "<input type=\"hidden\" name=\"formular\" value=\"1\">\n";
	$text .= "</form>\n";
	
	return $text;
}

// Zeigt das Thema an
function zeige_forenthema($beitrag_id) {
	global $seite, $lang, $forum_admin, $u_id;
	
	$query = pdoQuery("SELECT `beitrag_forum_id`, `beitrag_thema_id`, date_format(from_unixtime(`beitrag_thema_timestamp`), '%d.%m.%Y, %H:%i:%s') AS `po_date`,
			`beitrag_titel`, `beitrag_text`, `beitrag_user_id`, ifnull(`u_nick`, 'Nobody') AS `u_nick`, `u_id`, `u_level`
			FROM `forum_beitraege` LEFT JOIN `user` ON `beitrag_user_id` = `u_id` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$row = $query->fetch();
		$beitrag_forum_id = $row['beitrag_forum_id'];
		$beitrag_thema_id = $beitrag_id;
		$beitrag_titel = $row['beitrag_titel'];
		
		$beitrag_text = ersetze_smilies(chat_parse(nl2br($row['beitrag_text'])));
		if (($row['u_nick'] != "Nobody") && ($row['u_level'] <> "Z")) {
			$autor = zeige_userdetails($row['beitrag_user_id']);
		} else {
			$autor = $row['u_nick'];
		}
		
		$text = show_pfad_posting($beitrag_forum_id, $beitrag_titel);
		
		$text .= navigation_thema($beitrag_id, $beitrag_id, $beitrag_titel, $row['beitrag_user_id'], $beitrag_forum_id, true);
		
		$zaehler = 0;
		$text .= "<table style=\"width:100%;\">\n";
		
		// Überschrift: Thema
		$text .= zeige_formularfelder("ueberschrift", $zaehler, html_entity_decode($beitrag_titel), "", "", 0, "70", "");
		
		
		// Vom Benutzer gelesene Beiträge holen
		$query = pdoQuery("SELECT `u_gelesene_postings` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetch();
			$gelesene = $result['u_gelesene_postings'];
		}
		$u_gelesene = unserialize($gelesene);
		
		$query = pdoQuery("SELECT `beitrag_id`, `beitrag_forum_id`, date_format(from_unixtime(`beitrag_thema_timestamp`), '%d.%m.%Y, %H:%i:%s') AS `po_date`, `beitrag_titel`, `beitrag_text`, `beitrag_user_id`, `u_nick`, `u_level`
			FROM `forum_beitraege` LEFT JOIN `user` ON `beitrag_user_id` = `u_id` WHERE `beitrag_id` = :beitrag_id OR `beitrag_thema_id` = :beitrag_thema_id", [':beitrag_id'=>$beitrag_id, ':beitrag_thema_id'=>$beitrag_id]);
		
		$result = $query->fetchAll();
		foreach($result as $zaehler => $beitrag) {
			$beitrag_text = ersetze_smilies(chat_parse(nl2br( $beitrag['beitrag_text'] )));
			
			if (isset($u_gelesene[$beitrag_thema_id]) && !@in_array($beitrag['beitrag_id'], $u_gelesene[$beitrag_thema_id])) {
				$col = ' <span class="fa-solid fa-star icon16" alt="ungelesener Beitrag" title="ungelesener Beitrag"></span>';
			} else {
				$col = '';
			}
			$besonderer_status = $col;
			
			if (!$beitrag['u_nick']) {
				$userdetails = "gelöschter Benutzer";
			} else {
				$userdata = array();
				
				$userlink = zeige_userdetails($beitrag['beitrag_user_id']);
				if ($beitrag['u_level'] == 'Z') {
					$userdetails = $userdata['u_nick'];
				} else {
					$userdetails = $userlink;
				}
			}
			
			$query = pdoQuery("SELECT `ui_geschlecht` FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$beitrag['beitrag_user_id']]);
			
			$resultCount = $query->rowCount();
			if ($resultCount == 1) {
				$result = $query->fetch();
				$ui_gen = $result['ui_geschlecht'];
			} else {
				$ui_gen = '0';
			}
			
			// Avatar
			$ava = avatar_anzeigen($beitrag['beitrag_user_id'], $beitrag['u_nick'], "forum", $ui_gen);
			
			$text .= "<tr>\n";
			$text .= "<td rowspan=\"3\" class=\"tabelle_kopfzeile smaller\" style=\"width:150px; vertical-align:top; text-align:center;\">\n";
			$text .= "$ava <br>\n";
			$text .= $userdetails . $besonderer_status;
			$text .= "</td>\n";
			$text .= "<td class=\"tabelle_kopfzeile\">" . $lang['geschrieben_am'] . $beitrag['po_date'] . "</td>\n";
			$text .= "</tr>\n";
			$text .= "<tr>\n";
			$text .= "<td class=\"tabelle_zeile1\">" . html_entity_decode($beitrag_text) . "</td>\n";
			$text .= "</tr>\n";
			
			$text .= "<tr>\n";
			$text .= "<td class=\"tabelle_kopfzeile\" style=\"text-align:right;\">\n";
			
			$schreibrechte = pruefe_schreibrechte($beitrag['beitrag_forum_id']);
			// Darf der Benutzer den Beitrag bearbeiten?
			// Entweder eigenes posting oder forum_admin
			if ( (($u_id == $beitrag['beitrag_user_id']) || $forum_admin) && $schreibrechte ) {
				$text .= "<a href=\"forum.php?bereich=forum&forum_id=" . $beitrag['beitrag_forum_id'] . "&beitrag_thema_id=$beitrag_thema_id&beitrag_id=" . $beitrag['beitrag_id'] . "&aktion=edit&seite=$seite\" class=\"button\" title=\"$lang[forum_beitrag_editieren]\"><span class=\"fa-solid fa-pencil icon16\"></span> <span>$lang[forum_beitrag_editieren]</span></a>\n";
			}
			
			if ($schreibrechte) {
				$text .= "<a href=\"forum.php?bereich=forum&forum_id=" . $beitrag['beitrag_forum_id'] . "&beitrag_thema_id=$beitrag_id&aktion=reply&seite=$seite\" class=\"button\" title=\"$lang[forum_beitrag_zitieren]\"><span class=\"fa-solid fa-quote-right icon16\"></span> <span>$lang[forum_beitrag_zitieren]</span></a>\n";
			}
			
			//Nur Forum-Admins dürfen Beiträge löschen
			if ($forum_admin && $zaehler != 0) {
				$text .= "<a href=\"forum.php?bereich=forum&forum_id=" . $beitrag['beitrag_forum_id'] . "&beitrag_thema_id=$beitrag_thema_id&beitrag_id=" . $beitrag['beitrag_id'] . "&aktion=loesche_beitrag&seite=$seite\" onClick=\"return ask('$lang[forum_beitrag_loeschen_abfrage]')\" class=\"button\" title=\"$lang[forum_beitrag_loeschen]\"><span class=\"fa-solid fa-trash icon16\"></span> <span>$lang[forum_beitrag_loeschen]</span></a>\n";
			}
			$text .= "</td>\n";
			$text .= "</tr>\n";
		}
		
		$text .= "</table>\n";
		
		$text .= navigation_thema($beitrag_id, $beitrag_id, $beitrag_titel, $row['beitrag_user_id'], $beitrag_forum_id, false);
		$text .= show_pfad_posting($beitrag_forum_id, $beitrag_titel);
		
		$text .= "</table>\n";
		
		
		return $text;
	} else {
		return false;
	}
}
?>