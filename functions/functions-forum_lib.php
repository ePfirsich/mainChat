<?php

function pruefe_leserechte($forum_id) {
	// Prüft anhand der forum_id, ob der Benutzer im Forum lesen darf
	global $u_level;
	
	$query = pdoQuery("SELECT `forum_kategorie_id` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
	$resultCount = $query->rowCount();
	if($resultCount == 1) {
		$result = $query->fetch();
		
		$query = pdoQuery("SELECT `kat_admin` FROM `forum_kategorien` WHERE `kat_id` = :kat_id", [':kat_id'=>$result['forum_kategorie_id']]);
		
		$ba = $query->fetch();
		$admin_forum = $ba['kat_admin'];
		
		$leserechte = false;
		if ($u_level == "G") {
			if ($admin_forum == 0 || (($admin_forum & 8) == 8)) {
				$leserechte = true;
			}
		}
		
		if ($u_level == "U" || $u_level == "A" || $u_level == "M" || $u_level == "Z") {
			if ($admin_forum == 0 || (($admin_forum & 2) == 2)) {
				$leserechte = true;
			}
		}
		
		if ($u_level == "S" || $u_level == "C") {
			$leserechte = true;
		}
		
		return $leserechte;
	} else {
		return false;
	}
}

function hole_foren_id_anhand_posting_id($beitrag_id) {
	// Prüft anhand der `beitrag_id` ob gesperrt ist
	$query = pdoQuery("SELECT `beitrag_forum_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
	$resultCount = $query->rowCount();
	if($resultCount == 1) {
		$result = $query->fetch();
		return $result['beitrag_forum_id'];
	} else {
		return 0;
	}
}

function hole_themen_id_anhand_posting_id($beitrag_id) {
	// Prüft anhand der `beitrag_id` ob gesperrt ist
	$query = pdoQuery("SELECT `beitrag_thema_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
	$resultCount = $query->rowCount();
	if($resultCount == 1) {
		$result = $query->fetch();
		if($result['beitrag_thema_id'] == 0) {
			return $beitrag_id;
		} else {
			return $result['beitrag_thema_id'];
		}
	} else {
		return 0;
	}
}

function pruefe_schreibrechte($forum_id) {
	// Prüft anhand der forum_id, ob der Benutzer ins Forum schreiben darf
	global $u_level;
	
	$query = pdoQuery("SELECT `forum_kategorie_id` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
	
	$fo = $query->fetch();
	$kat_id = $fo['forum_kategorie_id'];
	
	$query = pdoQuery("SELECT `kat_admin` FROM `forum_kategorien` WHERE `kat_id` = :kat_id", [':kat_id'=>$kat_id]);
	
	$ba = $query->fetch();
	$admin_forum = $ba['kat_admin'];
	
	$schreibrechte = false;
	if ($u_level == "G") {
		if ((($admin_forum & 16) == 16)) {
			$schreibrechte = true;
		}
	}
	if ($u_level == "U" || $u_level == "A" || $u_level == "M" || $u_level == "Z") {
		if ($admin_forum == 0 || (($admin_forum & 4) == 4)) {
			$schreibrechte = true;
		}
	}
	if ($u_level == "S" || $u_level == "C") {
		$schreibrechte = true;
	}
	
	return ($schreibrechte);
}

function ist_thema_gesperrt($forum_id) {
	// Prüft ob ein Thema gesperrt ist
	
	$query = pdoQuery("SELECT `beitrag_gesperrt` FROM `forum_beitraege` WHERE `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_forum_id'=>$forum_id]);
	
	$fo = $query->fetch();
	
	$thema_gesperrt = false;
	
	if ($fo['beitrag_gesperrt'] == 1) {
		$thema_gesperrt = true;
	}
	
	return $thema_gesperrt;
}

function sperre_thema($forum_id) {
	global $lang;
	
	$text = "";
	if (ist_thema_gesperrt($forum_id)) {
		// Beitrag entsperren
		pdoQuery("UPDATE `forum_beitraege` SET `beitrag_gesperrt` = 0 WHERE `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_forum_id'=>$forum_id]);
		
		$erfolgsmeldung = $lang['forum_fehlermeldung_thema_entsperrt'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
	} else {
		// Beitrag sperren
		pdoQuery("UPDATE `forum_beitraege` SET `beitrag_gesperrt` = 1 WHERE `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_forum_id'=>$forum_id]);
		
		$erfolgsmeldung = $lang['forum_fehlermeldung_thema_gesperrt'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
	}
	
	return $text;
}

//Benutzer verlaesst Raum und geht ins Forum
//Austrittstext im Raum erzeugen, o_who auf 2 und o_raum auf -1 (community) setzen
function gehe_forum($u_id, $u_nick, $o_id, $o_raum) {
	global $lang;
	
	// Austrittstext im alten Raum erzeugen
	verlasse_chat($u_id, $u_nick, $o_raum);
	system_msg("", 0, $u_id, "", str_replace("%u_nick%", $u_nick, $lang['betrete_forum1']));
	
	// Daten in online-tabelle richten
	$f['o_raum'] = -1; //-1 allgemein fuer community
	$f['o_who'] = "2"; //2 -> Forum
	schreibe_online($f, "chat_forum", $u_id);
}

//Gelesene Beiträge des Benutzers einlesen
function lese_gelesene_postings($u_id) {
	global $u_gelesene;
	
	$query = pdoQuery("SELECT `u_gelesene_postings` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		$gelesene = $result['u_gelesene_postings'];
	}
	$u_gelesene = unserialize($gelesene);
}

// Markiert ein Forum oder alle Foren als gelesen
function forum_alles_gelesen($u_id, $forum_id = "") {
	global $u_gelesene;
	
	if($forum_id == "") {
		// Alle Foren als gelesen markieren
		$query = pdoQuery("SELECT `beitrag_id` FROM `forum_beitraege`", [':u_id'=>$u_id]);
	} else {
		// Spezifisches Forum als gelesen markieren
		$query = pdoQuery("SELECT `beitrag_id` FROM `forum_beitraege` WHERE `beitrag_forum_id` = :beitrag_forum_id", [':u_id'=>$forum_id]);
	}
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		if (!$u_gelesene[$forum_id]) {
			$u_gelesene[$forum_id][0] = array();
		}
		
		$result = $query->fetchAll();
		foreach($result as $zaehler => $runde) {
			array_push($u_gelesene[$forum_id], $runde['beitrag_id']);
			//wenn schon gelesen, dann wieder raus
		}
		$u_gelesene[$forum_id] = array_unique($u_gelesene[$forum_id]);
		$gelesene = serialize($u_gelesene);
		pdoQuery("UPDATE `user` SET `u_gelesene_postings` = :u_gelesene_postings WHERE `u_id` = :u_id", [':u_gelesene_postings'=>$gelesene, ':u_id'=>$u_id]);
	}
}

// markiert ein Thema als gelesen
function thread_alles_gelesen($forum_id, $thread_id, $u_id) {
	global $u_gelesene;
	
	$query = pdoQuery("SELECT `beitrag_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id OR `beitrag_thema_id` = :beitrag_thema_id", [':beitrag_id'=>$thread_id, ':beitrag_thema_id'=>$thread_id]);
	$result = $query->fetchAll();
	
	$liste = array();
	foreach ($result as $val){
		$liste[] = $val['beitrag_id'];
	}
	
	//kein Beitrag gelesen --> alle ungelesen
	if ( !isset($u_gelesene) || !$u_gelesene || !isset($u_gelesene[$forum_id]) || !$u_gelesene[$forum_id]) {
		$u_gelesene[$forum_id] = array();
	}
	
	for ($i = 0; $i < count($liste); $i++) {
		array_push($u_gelesene[$forum_id], $liste[$i]);
	}
	
	//wenn schon gelesen, dann wieder raus
	$u_gelesene[$forum_id] = array_unique($u_gelesene[$forum_id]);
	
	// und zurückschreiben
	$gelesene = serialize($u_gelesene);
	pdoQuery("UPDATE `user` SET `u_gelesene_postings` = :u_gelesene_postings WHERE `u_id` = :u_id", [':u_gelesene_postings'=>$gelesene, ':u_id'=>$u_id]);
}

//markiert ein posting fuer einen Benutzer als gelesen
function markiere_als_gelesen($beitrag_id, $u_id, $forum_id) {
	global $u_gelesene;
	
	if (!$u_gelesene[$forum_id]) {
		$u_gelesene[$forum_id][0] = $beitrag_id;
	} else {
		array_push($u_gelesene[$forum_id], $beitrag_id);
		//wenn schon gelesen, dann wieder raus
		$u_gelesene[$forum_id] = array_unique($u_gelesene[$forum_id]);
	}
	
	$gelesene = serialize($u_gelesene);
	pdoQuery("UPDATE `user` SET `u_gelesene_postings` = :u_gelesene_postings WHERE `u_id` = :u_id", [':u_gelesene_postings'=>$gelesene, ':u_id'=>$u_id]);
}

//gibt die ungelesenen Beiträge in einem Forum zurueck
//Je nachdem, ob $arr_postings sich auf ein Thema bezieht
function anzahl_ungelesene(&$arr_postings, $forum_id) {
	global $u_gelesene;
	
	//kein Beitrag im Thema --> keine ungelesenen
	if (count($arr_postings) == 0) {
		return 0;
	}
	
	//kein Beitrag gelesen --> alle ungelesen
	if ( !$u_gelesene || !$u_gelesene[$forum_id]) {
		$u_gelesene[$forum_id] = array();
		return count($arr_postings);
	}
	
	// anzahl unterschied zwischen postings im Thema und den gelesenen
	//postings des users zurueckgeben
	
	return count(array_diff($arr_postings, $u_gelesene[$forum_id]));
}

function anzahl_ungelesener_themen(&$arr_postings, $forum_id) {
	global $u_gelesene;
	
	//kein Beitrag in Gruppe --> keine ungelesenen
	if (count($arr_postings) == 0) {
		return 0;
	}
	
	//kein Beitrag gelesen --> alle ungelesen
	if ( !isset($u_gelesene) || !$u_gelesene || !isset($u_gelesene[$forum_id]) || !$u_gelesene[$forum_id]) {
		$u_gelesene = array();
		return count($arr_postings);
	}
	
	// Anzahl Unterschied zwischen postings im Thema und den gelesenen postings des Benutzers zurueckgeben
	$arr = array_diff($arr_postings, $u_gelesene[$forum_id]);
	$diff = count($arr);
	reset($arr);
	
	// Wenn keine Beiträge vorhanden sind, Themen auf 0 setzen
	// Ansonsten eine kommaseparierte Liste erstellen
	$themen = count($arr) == 0 ? '0' : implode(',', $arr);
	
	$query = pdoQuery("SELECT `beitrag_id` FROM `forum_beitraege` WHERE `beitrag_id` IN (:beitrag_id)", [':beitrag_id'=>$themen]);
	$resultCount = $query->rowCount();
	
	return $resultCount;
}

// Kategorie anlegen/editieren
function erstelle_editiere_kategorie($kat_id, $kat_name, $kat_admin) {
	if($kat_id == 0) {
		// Neue Kategorie anlegen
		
		//groesste Order holen
		$query = pdoQuery("SELECT MAX(`kat_order`) AS `maxorder` FROM `forum_kategorien`", []);
		
		$result = $query->fetch();
		$maxorder = $result['maxorder'];
		if ($maxorder) {
			$maxorder++;
		} else {
			$maxorder = 1;
		}
		
		pdoQuery("INSERT INTO `forum_kategorien` (`kat_name`, `kat_order`, `kat_admin`) VALUES (:kat_name, :kat_order, :kat_admin)",
			[
				':kat_name'=>$kat_name,
				':kat_order'=>$maxorder,
				':kat_admin'=>$kat_admin
			]);
	} else {
		// Kategorie editieren
		
		pdoQuery("UPDATE `forum_kategorien` SET `kat_name` = :kat_name, `kat_admin` = :kat_admin WHERE `kat_id` = :kat_id",
			[
				':kat_id'=>$kat_id,
				':kat_name'=>$kat_name,
				':kat_admin'=>$kat_admin
			]);
	}
}

// Schiebt Forum in Darstellungsreihenfolge nach oben
function kategorie_up($kat_id, $kat_order) {
	//Forum über aktuellem Forum holen
	$query = pdoQuery("SELECT `kat_id`, `kat_order` AS `prev_order` FROM `forum_kategorien` WHERE `kat_order` < :kat_order ORDER BY `kat_order` DESC LIMIT 1", [':kat_order'=>$kat_order]);
	
	$resultCount = $query->rowCount();
	//ist Forum oberstes Forum?
	if ($resultCount == 1) {
		$result = $query->fetch();
		
		//nein -> Reihenfolge vertauschen
		pdoQuery("UPDATE `forum_kategorien` SET `kat_order` = :kat_order WHERE `kat_id` = :kat_id",
			[
				':kat_id'=>$result['kat_id'],
				':kat_order'=>$kat_order
			]);
		
		pdoQuery("UPDATE `forum_kategorien` SET `kat_order` = :kat_order WHERE `kat_id` = :kat_id",
			[
				':kat_id'=>$kat_id,
				':kat_order'=>$result['prev_order']
			]);
	}
}

// Schiebt Forum in Darstellungsreihenfolge nach oben
function kategorie_down($kat_id, $kat_order) {
	//forum über aktuellem Forum holen
	$query = pdoQuery("SELECT `kat_id`, `kat_order` AS `next_order` FROM `forum_kategorien` WHERE `kat_order` > :kat_order ORDER BY `kat_order` LIMIT 1", [':kat_order'=>$kat_order]);
	
	$resultCount = $query->rowCount();
	//ist Thema schon letztes Thema?
	if ($resultCount == 1) {
		$result = $query->fetch();
		
		//nein -> Reihenfolge vertauschen
		pdoQuery("UPDATE `forum_kategorien` SET `kat_order` = :kat_order WHERE `kat_id` = :kat_id",
			[
				':kat_id'=>$result['kat_id'],
				':kat_order'=>$kat_order
			]);
		
		
		pdoQuery("UPDATE `forum_kategorien` SET `kat_order` = :kat_order WHERE `kat_id` = :kat_id",
			[
				':kat_id'=>$kat_id,
				':kat_order'=>$result['next_order']
			]);
	}
}

// Komplettes Forum mit allen Themen und postings loeschen
function loesche_kategorie($kat_id) {
	global $lang;
	
	$text = "";
	$query = pdoQuery("SELECT `kat_name` FROM `forum_kategorien` WHERE `kat_id` = :kat_id", [':kat_id'=>$kat_id]);
	$result = $query->fetch();
	$kat_name = $result['kat_name'];
	
	$query = pdoQuery("SELECT `forum_id` FROM `forum_foren` WHERE `forum_kategorie_id` = :forum_kategorie_id", [':forum_kategorie_id'=>$kat_id]);
	$result = $query->fetchAll();
	foreach($result as $zaehler => $forum) {
		$text .= loesche_forum($forum['forum_id']);
	}
	
	pdoQuery("DELETE FROM `forum_kategorien` WHERE `kat_id` = :kat_id", [':kat_id'=>$kat_id]);
	
	$erfolgsmeldung = str_replace("%kategorie%", $kat_name, $lang['forum_erfolgsmeldung_kategorie_geloescht']);
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
	
	return $text;
}

// Forum anlegen/editieren
function erstelle_editiere_forum($forum_id, $forum_kategorie_id, $forum_name, $forum_beschreibung) {
	// $kat_id, $kat_name, $kat_admin
	//groesste Order holen
	$query = pdoQuery("SELECT MAX(`forum_order`) AS `maxorder` FROM `forum_foren` WHERE `forum_kategorie_id` = :forum_kategorie_id", [':forum_kategorie_id'=>$forum_kategorie_id]);
	$result = $query->fetch();
	$maxorder = $result['maxorder'];
	if ($maxorder) {
		$maxorder++;
	} else {
		$maxorder = 1;
	}
	
	if($forum_id == 0) {
		// Neues Forum anlegen
		pdoQuery("INSERT INTO `forum_foren` (`forum_name`, `forum_beschreibung`, `forum_kategorie_id`, `forum_order`) VALUES (:forum_name, :forum_beschreibung, :forum_kategorie_id, :forum_order)",
			[
				':forum_name'=>$forum_name,
				':forum_beschreibung'=>$forum_beschreibung,
				':forum_kategorie_id'=>$forum_kategorie_id,
				':forum_order'=>$maxorder,
			]);
	} else {
		// Forum editieren
		pdoQuery("UPDATE `forum_foren` SET `forum_name` = :forum_name, `forum_beschreibung` = :forum_beschreibung, `forum_kategorie_id` = :forum_kategorie_id, `forum_order` = :forum_order WHERE `forum_id` = :forum_id",
			[
				':forum_id'=>$forum_id,
				':forum_name'=>$forum_name,
				':forum_beschreibung'=>$forum_beschreibung,
				':forum_kategorie_id'=>$forum_kategorie_id,
				':forum_order'=>$maxorder,
			]);
	}
}

// Schiebt ein Forum in Darstellungsreihenfolge nach oben
function forum_up($forum_id, $forum_order, $kat_id) {
	$query = pdoQuery("SELECT `forum_id`, `forum_order` FROM `forum_foren` WHERE `forum_kategorie_id` = :forum_kategorie_id AND `forum_order` < :forum_order ORDER BY `forum_order` DESC LIMIT 1", [':forum_kategorie_id'=>$kat_id, ':forum_order'=>$forum_order]);
	
	$resultCount = $query->rowCount();
	
	//ist Thema oberstes Thema?
	if ($resultCount == 1) {
		$result = $query->fetch();
		//nein -> Reihenfolge vertauschen
		pdoQuery("UPDATE `forum_foren` SET `forum_order` = :forum_order WHERE `forum_id` = :forum_id",
			[
				':forum_id'=>$result['forum_id'],
				':forum_order'=>$forum_order
			]);
		
		pdoQuery("UPDATE `forum_foren` SET `forum_order` = :forum_order WHERE `forum_id` = :forum_id",
			[
				':forum_id'=>$forum_id,
				':forum_order'=>$result['forum_order']
			]);
	}
}

// Schiebt ein Forum in Darstellungsreihenfolge nach unten
function forum_down($forum_id, $forum_order, $kat_id) {
	$query = pdoQuery("SELECT `forum_id`, `forum_order` FROM `forum_foren` WHERE `forum_kategorie_id` = :forum_kategorie_id AND `forum_order` > :forum_order ORDER BY `forum_order` DESC LIMIT 1", [':forum_kategorie_id'=>$kat_id, ':forum_order'=>$forum_order]);
	
	$resultCount = $query->rowCount();
	
	//ist Thema schon letztes Thema?
	if ($resultCount == 1) {
		$result = $query->fetch();
		
		//nein -> Reihenfolge vertauschen
		pdoQuery("UPDATE `forum_foren` SET `forum_order` = :forum_order WHERE `forum_id` = :forum_id",
			[
				':forum_id'=>$result['forum_id'],
				':forum_order'=>$forum_order
			]);
		
		pdoQuery("UPDATE `forum_foren` SET `forum_order` = :forum_order WHERE `forum_id` = :forum_id",
			[
				':forum_id'=>$forum_id,
				':forum_order'=>$result['forum_order']
			]);
	}
}

// Komplettes Forum mit allen Themen und Beiträgen loeschen
function loesche_forum($forum_id) {
	global $lang;
	
	if (!$forum_id) {
		$fehlermeldung = $lang['forum_fehlermeldung_forum_nicht_geloescht'];
		$text = hinweis($fehlermeldung, "fehler");
	} else {
		$query = pdoQuery("SELECT `forum_name` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
		$result = $query->fetch();
		$forum_name = $result['forum_name'];
		
		pdoQuery("DELETE FROM `forum_beitraege` WHERE `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_forum_id'=>$forum_id]);
		pdoQuery("DELETE FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
		
		$erfolgsmeldung = str_replace("%forum%", $forum_name, $lang['forum_erfolgsmeldung_forum_geloescht']);
		$text = hinweis($erfolgsmeldung, "erfolgreich");
	}
	
	return $text;
}

// Thema anlegen/editieren
function erstelle_editiere_thema($beitrag_id, $beitrag_titel, $beitrag_text, $forum_id, $beitrag_angepinnt, $beitrag_gesperrt) {
	global $forum_admin, $lang;
	
	$text = "";
	if($beitrag_id == 0) {
		global $u_id, $pdo;
		// Neues Thema anlegen
		if($forum_admin) {
			pdoQuery("INSERT INTO `forum_beitraege` (`beitrag_titel`, `beitrag_text`, `beitrag_thema_id`, `beitrag_forum_id`, `beitrag_angepinnt`, `beitrag_gesperrt`, `beitrag_thema_timestamp`, `beitrag_antwort_timestamp`, `beitrag_user_id`)
					VALUES (:beitrag_titel, :beitrag_text, :beitrag_thema_id, :forum_id, :beitrag_angepinnt, :beitrag_gesperrt, :beitrag_thema_timestamp, :beitrag_antwort_timestamp, :beitrag_user_id)",
				[
					':beitrag_titel'=>$beitrag_titel,
					':beitrag_text'=>htmlentities($beitrag_text),
					':beitrag_thema_id'=>0,
					':forum_id'=>$forum_id,
					':beitrag_angepinnt'=>$beitrag_angepinnt,
					':beitrag_gesperrt'=>$beitrag_gesperrt,
					':beitrag_thema_timestamp'=>time(),
					':beitrag_antwort_timestamp'=>time(),
					':beitrag_user_id'=>$u_id
				]);
		} else {
			pdoQuery("INSERT INTO `forum_beitraege` (`beitrag_titel`, `beitrag_text`, `beitrag_thema_id`, `beitrag_forum_id`, `beitrag_thema_timestamp`, `beitrag_antwort_timestamp`, `beitrag_user_id`)
					VALUES (:beitrag_titel, :beitrag_text, :beitrag_thema_id, :forum_id, :beitrag_thema_timestamp, :beitrag_antwort_timestamp, :beitrag_user_id)",
				[
					':beitrag_titel'=>$beitrag_titel,
					':beitrag_text'=>htmlentities($beitrag_text),
					':beitrag_thema_id'=>0,
					':forum_id'=>$forum_id,
					':beitrag_thema_timestamp'=>time(),
					':beitrag_antwort_timestamp'=>time(),
					':beitrag_user_id'=>$u_id
				]);
		}
		
		// Erfolgsmeldung anzeigen
		$erfolgsmeldung = $lang['forum_erfolgsmeldung_thema_erstellt'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
		
		$beitrag_id = $pdo->lastInsertId();
		
		// Zähler aktualisieren
		$query = pdoQuery("SELECT `forum_anzahl_themen`, `forum_beitraege` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
		
		$result = $query->fetch();
		$anzthreads = $result['forum_anzahl_themen'];
		$postings = $result['forum_beitraege'];
		
		if (!$postings) {
			//erster Beitrag in diesem Thema
			$postings = $beitrag_id;
		} else {
			//schon postings da
			$postings_array = explode(",", $postings);
			$postings_array[] = $beitrag_id;
			$postings = implode(",", $postings_array);
		}
		
		//neues Thema
		$anzthreads++;
		
		pdoQuery("UPDATE `forum_foren` SET `forum_anzahl_themen` = :forum_anzahl_themen, `forum_beitraege` = :forum_beitraege WHERE `forum_id` = :forum_id",
			[
				':forum_anzahl_themen'=>$anzthreads,
				':forum_beitraege'=>$postings,
				':forum_id'=>$forum_id
			]);
		
		$text .= verbuche_punkte($u_id, "add");
	} else {
		// Thema editieren
		if($forum_admin) {
			pdoQuery("UPDATE `forum_beitraege` SET `beitrag_titel` = :beitrag_titel, `beitrag_angepinnt` = :beitrag_angepinnt, `beitrag_gesperrt` = :beitrag_gesperrt WHERE `beitrag_id` = :beitrag_id",
				[
					':beitrag_id'=>$beitrag_id,
					':beitrag_titel'=>$beitrag_titel,
					':beitrag_angepinnt'=>$beitrag_angepinnt,
					':beitrag_gesperrt'=>$beitrag_gesperrt
				]);
		} else {
			pdoQuery("UPDATE `forum_beitraege` SET `beitrag_titel` = :beitrag_titel WHERE `beitrag_id` = :beitrag_id",
				[
					':beitrag_id'=>$beitrag_id,
					':beitrag_titel'=>$f['beitrag_titel'],
				]);
		}
		
		// Erfolgsmeldung anzeigen
		$erfolgsmeldung = $lang['forum_erfolgsmeldung_thema_editiert'];
		$text .= hinweis($erfolgsmeldung, "erfolgreich");
	}
	
	return array($text, $beitrag_id);
}

// Beitrag editieren
function editiere_beitrag($beitrag_id, $beitrag_text) {
	global $forum_aenderungsanzeige, $lang, $u_nick;
	
	if ($forum_aenderungsanzeige == "1") {
		$append = "<br>";
		$append .= $lang['letzte_aenderung'];
		$append = str_replace("%datum%", date("d.m.Y"), $append);
		$append = str_replace("%uhrzeit%", date("H:i"), $append);
		$append = str_replace("%user%", $u_nick, $append);
		
		$beitrag_text .= $append;
	}
	
	pdoQuery("UPDATE `forum_beitraege` SET `beitrag_text` = :beitrag_text WHERE `beitrag_id` = :beitrag_id",
		[
			':beitrag_id'=>$beitrag_id,
			':beitrag_text'=>htmlentities($beitrag_text),
		]);
}

//editiere_beitrag
//schreibt neuen/editierten Beitrag in die Datenbank
function schreibe_posting($beitrag_id, $forum_id, $beitrag_text) {
	global $beitrag_thema_id, $u_id, $lang, $pdo;
	
	$text = "";
	$query = pdoQuery("SELECT `beitrag_titel` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
	
	$result = $query->fetch();
	$beitrag_titel = $result['beitrag_titel'];
	
	//neuer Beitrag
	$f['beitrag_forum_id'] = $forum_id;
	$f['beitrag_user_id'] = $u_id;
	$f['beitrag_thema_id'] = $beitrag_thema_id;
	$f['beitrag_titel'] = $beitrag_titel;
	$f['beitrag_text'] = htmlentities($beitrag_text);
	$f['beitrag_thema_timestamp'] = time();
	$f['beitrag_antwort_timestamp'] = time();
	
	//Beitrag schreiben
	pdoQuery("INSERT INTO `forum_beitraege` (`beitrag_forum_id`, `beitrag_user_id`, `beitrag_thema_id`, `beitrag_titel`, `beitrag_text`, `beitrag_thema_timestamp`, `beitrag_antwort_timestamp`) VALUES (:beitrag_forum_id, :beitrag_user_id, :beitrag_thema_id, :beitrag_titel, :beitrag_text, :beitrag_thema_timestamp, :beitrag_antwort_timestamp)",
		[
			':beitrag_forum_id'=>$f['beitrag_forum_id'],
			':beitrag_user_id'=>$f['beitrag_user_id'],
			':beitrag_thema_id'=>$f['beitrag_thema_id'],
			':beitrag_titel'=>$f['beitrag_titel'],
			':beitrag_text'=>$f['beitrag_text'],
			':beitrag_thema_timestamp'=>$f['beitrag_thema_timestamp'],
			':beitrag_antwort_timestamp'=>$f['beitrag_antwort_timestamp']
		]);
	
	// Erfolgsmeldung anzeigen
	$erfolgsmeldung = $lang['forum_erfolgsmeldung_beitrag_erstellt'];
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
	
	$new_beitrag_id = $pdo->lastInsertId();
	
	//forum_beitraege muss neu geschrieben werden
	//anz_threads und anz_replys im Thema setzen
	//erst Tabelle `forum_foren` sperren
	pdoQuery("LOCK TABLES `forum_foren` WRITE", []);
	
	//altes forum_beitraege und anz_threads und anz_replys holen
	$query = pdoQuery("SELECT `forum_beitraege`, `forum_anzahl_antworten` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
	
	$result = $query->fetch();
	$postings = $result['forum_beitraege'];
	$anzreplys = $result['forum_anzahl_antworten'];
	
	if (!$postings) {
		//erster Beitrag in diesem Thema
		$postings = $new_beitrag_id;
	} else {
		//schon postings da
		$postings_array = explode(",", $postings);
		$postings_array[] = $new_beitrag_id;
		$postings = implode(",", $postings_array);
	}
	
	//neue Antwort
	$anzreplys++;
	
	//schreiben
	pdoQuery("UPDATE `forum_foren` SET `forum_beitraege` = :forum_beitraege, `forum_anzahl_antworten` = :forum_anzahl_antworten WHERE `forum_id` = :forum_id",
		[
			':forum_beitraege'=>$postings,
			':forum_anzahl_antworten'=>$anzreplys,
			':forum_id'=>$forum_id
			
		]);
	
	//Tabellen wieder freigeben
	pdoQuery("UNLOCK TABLES", []);
	
	$text .= verbuche_punkte($u_id, "add");
	
	return array($text, $new_beitrag_id);
}

// Löscht einen Beitrag aus einem Thema
function beitrag_loeschen($forum_id, $beitrag_id) {
	global $lang;
	
	$text = "";
	$query = pdoQuery("SELECT `forum_anzahl_antworten`, `forum_beitraege` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
	$result = $query->fetch();
	$postings = $result['forum_beitraege'];
	$anzreplys = $result['forum_anzahl_antworten'];
	
	//in array einlesen und zu loeschende rausschmeissen
	$arr_delete = array();
	$arr_new_postings = explode(",", $postings);
	$arr_new_postings = array_diff($arr_new_postings, $arr_delete);
	$new_postings = implode(",", $arr_new_postings);
	
	// forum_anzahl_antworten neu schreiben
	$anzreplys = $anzreplys --;
	pdoQuery("UPDATE `forum_foren` SET `forum_anzahl_antworten` = :forum_anzahl_antworten, `forum_beitraege` = :forum_beitraege WHERE `forum_id` = :forum_id",
		[
			':forum_anzahl_antworten'=>$anzreplys,
			':forum_beitraege'=>$new_postings,
			':forum_id'=>$forum_id
		]);
	
	$query = pdoQuery("SELECT `beitrag_user_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
	$result = $query->fetch();
	
	pdoQuery("DELETE FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
	
	
	$erfolgsmeldung = $lang['forum_erfolgsmeldung_beitrag_geloescht'];
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
	
	$text .= verbuche_punkte($result['beitrag_user_id'], "subtract");
	
	
	return $text;
}

// Löscht ein Thema und alle Beitrage davon
function thema_loeschen($forum_id, $beitrag_thema_id, $beitrag_id) {
	global $arr_delete, $lang;
	
	$text = "";
	$arr_delete = array();
	
	// Tabelle `forum_beitraege` und `forum_foren` locken
	pdoQuery("LOCK TABLES `forum_beitraege` WRITE, `forum_foren` WRITE", []);
	
	//rekursiv alle zu loeschenden postings in feld einlesen
	$arr_delete[] = $beitrag_thema_id;
	$query = pdoQuery("SELECT `beitrag_id` FROM `forum_beitraege` WHERE `beitrag_thema_id` = :beitrag_thema_id", [':beitrag_thema_id'=>$beitrag_thema_id]);
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetchAll();
		foreach($result as $zaehler => $posting) {
			$arr_delete[] = $posting['beitrag_id'];
		}
	}
	
	//eintragungen im Forum neu schreiben
	$query = pdoQuery("SELECT `forum_anzahl_themen`, `forum_anzahl_antworten`, `forum_beitraege` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
	
	$result = $query->fetch();
	$postings = $result['forum_beitraege'];
	$anzthreads = $result['forum_anzahl_themen'];
	$anzreplys = $result['forum_anzahl_antworten'];
	
	//in array einlesen und zu loeschende rausschmeissen
	$arr_new_postings = explode(",", $postings);
	
	$arr_new_postings = array_diff($arr_new_postings, $arr_delete);
	
	$new_postings = implode(",", $arr_new_postings);
	
	//forum_anzahl_themen und forum_anzahl_antworten neu schreiben
	$anzthreads--;
	$anzreplys = $anzreplys - count($arr_delete) + 1;
	
	pdoQuery("UPDATE `forum_foren` SET `forum_anzahl_themen` = :forum_anzahl_themen, `forum_anzahl_antworten` = :forum_anzahl_antworten, `forum_beitraege` = :forum_beitraege WHERE `forum_id` = :forum_id",
		[
			':forum_anzahl_themen'=>$anzthreads,
			':forum_anzahl_antworten'=>$anzreplys,
			':forum_beitraege'=>$new_postings,
			':forum_id'=>$forum_id
		]);
	
	$erfolgsmeldung = $lang['forum_erfolgsmeldung_thema_geloescht'];
	$text .= hinweis($erfolgsmeldung, "erfolgreich");
	
	// Punkte abziehen
	reset($arr_delete);
	foreach($arr_delete as $k => $v) {
		$query = pdoQuery("SELECT `beitrag_user_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>intval($v)]);
		
		$resultCount = $query->rowCount();
		if ($resultCount == 1) {
			$result = $query->fetch();
			
			$text .= verbuche_punkte($result['beitrag_user_id'], "subtract");
		}
	}
	
	reset($arr_delete);
	foreach($arr_delete as $k => $v) {
		pdoQuery("DELETE FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id", [':beitrag_id'=>$v]);
	}
	
	//Tabellen wieder freigeben
	pdoQuery("UNLOCK TABLES", []);
	
	return $text;
	
}

function verschiebe_thema_ausfuehren($beitrag_id, $beitrag_forum_id, $beitrag_forum_id_neu) {
	// Ändert alle Beiträge eine Themas
	$query = pdoQuery("SELECT `beitrag_id` FROM `forum_beitraege` WHERE `beitrag_id` = :beitrag_id AND `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_id'=>$beitrag_id, ':beitrag_forum_id'=>$beitrag_forum_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		pdoQuery("LOCK TABLES `forum_beitraege` WRITE, `forum_foren` WRITE", []);
		
		// Thema und alle Beiträge verschieben
		pdoQuery("UPDATE `forum_beitraege` SET `beitrag_forum_id` = :beitrag_forum_id_neu WHERE `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_forum_id_neu'=>$beitrag_forum_id_neu, ':beitrag_forum_id'=>$beitrag_forum_id]);
		
		pdoQuery("UNLOCK TABLES", []);
		
		
		// Zähler in den Foren aktualisieren
		$query = pdoQuery("SELECT COUNT(`beitrag_id`) AS `anzahl` FROM `forum_beitraege` WHERE `beitrag_forum_id` = :beitrag_forum_id", [':beitrag_forum_id'=>$beitrag_id]);
		$result = $query->fetch();
		$forum_anzahl_antworten = $result['anzahl'];
		
		// subtrahieren
		aktualisiere_foren_zaehler($beitrag_forum_id, 1, $forum_anzahl_antworten, false);
		
		// addieren
		aktualisiere_foren_zaehler($beitrag_forum_id_neu, 1, $forum_anzahl_antworten, true);
	}
	
}

function ersetze_smilies($text) {
	global $sprache, $smilie, $u_id;
	
	// Hole alle benötigten Einstellungen des Benutzers
	$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");
	
	preg_match_all("/(&amp;[^ |^<]+)/", $text, $test, PREG_PATTERN_ORDER);
	
	foreach($test[0] as $i => $smilie_code) {
		$smilie_code2 = str_replace("&amp;", "&", $smilie_code);
		$smilie_code2 = chop($smilie_code2);
		if ( isset($smilie[$smilie_code2]) && $smilie[$smilie_code2] ) {
			$text = str_replace($smilie_code, "<img src=\"images/smilies/style-" . $benutzerdaten['u_layout_farbe'] . "/" . $smilie[$smilie_code2] . "\">", $text);
		}
	}
	
	return $text;
}

// erzeugt Aktionen bei Antwort auf einen Beitrag
function aktion_sofort($beitrag_id, $beitrag_thema_id, $thread) {
	global $u_id;
	//aktionen nur fuer Antworten
	if ($beitrag_thema_id > 0) {
		$query = pdoQuery("SELECT `beitrag_user_id`, date_format(from_unixtime(`beitrag_thema_timestamp`), '%d.%m.%Y') AS `po_date`, `beitrag_titel`, `forum_name`, `kat_name` 
			FROM `forum_beitraege`, `forum_foren`, `forum_kategorien` WHERE `beitrag_id` = :beitrag_id AND `beitrag_forum_id` = `forum_id` AND `forum_kategorie_id` = `kat_id`", [':beitrag_id'=>$beitrag_thema_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetch();
			//Daten des Vaters holen
			$user = $result['beitrag_user_id'];
			$beitrag_thema_timestamp = $result['po_date'];
			$beitrag_titel = $result['beitrag_titel'];
			$thema = $result['forum_name'];
			$forum = $result['kat_name'];
		} else {
			return;
		}
		
		$query = pdoQuery("SELECT `u_id`, `u_nick`, date_format(from_unixtime(`beitrag_thema_timestamp`), '%d.%m.%Y %H:%i') AS `po_date`, `beitrag_titel` FROM `user`, `forum_beitraege` WHERE `beitrag_user_id` = `u_id` AND `beitrag_id` = :beitrag_id", [':beitrag_id'=>$beitrag_id]);
		
		$resultCount = $query->rowCount();
		if ($resultCount > 0) {
			$result = $query->fetch();
			$user_from_id = $result['u_id'];
			$user_from_nick = $result['u_nick'];
			$po_ts_antwort = $result['po_date'];
			$po_titel_antwort = $result['beitrag_titel'];
		} else {
			return;
		}
		
		//Ist betroffener Benutzer Online?
		$online = ist_online($user);
		
		if ($online) {
			$wann = "Sofort/Online";
		} else {
			$wann = "Sofort/Offline";
		}
		
		$text['beitrag_titel'] = $beitrag_titel;
		$text['beitrag_thema_timestamp'] = $beitrag_thema_timestamp;
		$text['forum'] = $forum;
		$text['thema'] = $thema;
		$text['user_from_nick'] = $user_from_nick;
		$text['po_titel_antwort'] = $po_titel_antwort;
		$text['po_ts_antwort'] = $po_ts_antwort;
		
		aktion($u_id, $wann, $user, $user_from_id, "Antwort auf eigenen Beitrag", $text);
	}
}

function aktualisiere_foren_zaehler($forum_id, $forum_anzahl_themen, $forum_anzahl_antworten, $addieren = true) {
	$query = pdoQuery("SELECT `forum_anzahl_themen`, `forum_anzahl_antworten` FROM `forum_foren` WHERE `forum_id` = :forum_id", [':forum_id'=>$forum_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount > 0) {
		$result = $query->fetch();
		
		if($addieren) {
			$forum_anzahl_themen = $result['forum_anzahl_themen'] + $forum_anzahl_themen;
			$forum_anzahl_antworten = $result['forum_anzahl_antworten'] + $forum_anzahl_antworten;
		} else {
			$forum_anzahl_themen = $result['forum_anzahl_themen'] - $forum_anzahl_themen;
			$forum_anzahl_antworten = $result['forum_anzahl_antworten'] - $forum_anzahl_antworten;
		}
		
		pdoQuery("UPDATE `forum_foren` SET  `forum_anzahl_themen` = :forum_anzahl_themen, `forum_anzahl_antworten` = :forum_anzahl_antworten WHERE `forum_id` = :forum_id",
			[
				':forum_id'=>$forum_id,
				':forum_anzahl_themen'=>$forum_anzahl_themen,
				':forum_anzahl_antworten'=>$forum_anzahl_antworten
			]);
	}
}
?>