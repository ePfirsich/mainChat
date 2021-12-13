<?php

function pruefe_leserechte($th_id) {
	global $mysqli_link;
	// Prüft anhand der th_id, ob der Benutzer im Forum lesen darf
	global $u_level;
	
	$query = "SELECT th_fo_id FROM thema WHERE th_id = " . intval($th_id);
	$result = mysqli_query($mysqli_link, $query);
	$fo = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$fo_id = $fo['th_fo_id'];
	
	$query = "SELECT fo_admin FROM forum WHERE fo_id = '$fo_id'";
	$result = mysqli_query($mysqli_link, $query);
	list($admin_forum) = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$leserechte = false;
	if ($u_level == "G")
		if ($admin_forum == 0 || (($admin_forum & 8) == 8))
			$leserechte = true;
	
	if ($u_level == "U" || $u_level == "A" || $u_level == "M"
		|| $u_level == "Z")
		if ($admin_forum == 0 || (($admin_forum & 2) == 2))
			$leserechte = true;
	
	if ($u_level == "S" || $u_level == "C")
		$leserechte = true;
	
	return ($leserechte);
}

function hole_themen_id_anhand_posting_id($po_id) {
	global $mysqli_link;
	// Prüft anhand der po_id ob gesperrt ist
	$query = "SELECT po_th_id FROM posting WHERE po_id = " . intval($po_id);
	$result = mysqli_query($mysqli_link, $query);
	$fo = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	return ($fo['po_th_id']);
}

function pruefe_schreibrechte($th_id) {
	// Prüft anhand der th_id, ob der Benutzer ins Forum schreiben darf
	global $mysqli_link;
	global $u_level;
	
	$query = "SELECT th_fo_id FROM thema WHERE th_id = " . intval($th_id);
	$result = mysqli_query($mysqli_link, $query);
	$fo = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$fo_id = $fo['th_fo_id'];
	
	$query = "SELECT fo_admin FROM forum WHERE fo_id = '$fo_id'";
	$result = mysqli_query($mysqli_link, $query);
	list($admin_forum) = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$schreibrechte = false;
	if ($u_level == "G")
		if ((($admin_forum & 16) == 16))
			$schreibrechte = true;
	if ($u_level == "U" || $u_level == "A" || $u_level == "M"
		|| $u_level == "Z")
		if ($admin_forum == 0 || (($admin_forum & 4) == 4))
			$schreibrechte = true;
	if ($u_level == "S" || $u_level == "C")
		$schreibrechte = true;
	return ($schreibrechte);
}

function ist_thread_gesperrt($thread) {
	global $mysqli_link;
	global $forum_thread_sperren;
	
	// Prüft anhand der thread auf den man antworten will, gesperrt ist
	$query = "SELECT po_threadts, po_ts, po_threadgesperrt FROM posting WHERE po_id = " . intval($thread);
	$result = mysqli_query($mysqli_link, $query);
	$fo = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$threadgesperrt = false;
	
	if ($fo['po_threadgesperrt'] == 'Y') {
		$threadgesperrt = true;
	}
	
	if ($forum_thread_sperren > 0) {
		$abwann = mktime(0, 0, 0, date('m') - $forum_thread_sperren,
			date('d') + 1, date('Y'));
		
		// Alte Beiträge vor 4.12.2006 (ab hier erst protokoll des po_threadts)
		if ($fo['po_threadts'] == 0) {
			if ($fo['po_ts'] <= $abwann) {
				$threadgesperrt = true;
			}
		} else {
		// Alle Beiträge nach/mit 4.12.2006
			if ($fo['po_threadts'] <= $abwann) {
				$threadgesperrt = true;
			}
		}
	}
	
	return ($threadgesperrt);
}

function ist_posting_gesperrt($po_id) {
	global $mysqli_link;
	// Prüft anhand der po_id ob gesperrt ist
	$query = "SELECT po_gesperrt FROM posting WHERE po_id = " . intval($po_id);
	$result = mysqli_query($mysqli_link, $query);
	$fo = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$postinggesperrt = false;
	
	if ($fo['po_gesperrt'] == 'Y')
		$postinggesperrt = true;
	
	return ($postinggesperrt);
}

function sperre_posting($po_id) {
	global $mysqli_link;
	$query = "SELECT po_gesperrt FROM posting WHERE po_id = " . intval($po_id);
	$result = mysqli_query($mysqli_link, $query);
	$fo = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	if (ist_posting_gesperrt($po_id)) {
		// Beitrag entsperren
		$query = "UPDATE posting SET po_gesperrt = 'N' WHERE po_id = " . intval($po_id);
		$result = mysqli_query($mysqli_link, $query);
	} else {
		// Beitrag sperren
		$query = "UPDATE posting SET po_gesperrt = 'Y' WHERE po_id = " . intval($po_id);
		$result = mysqli_query($mysqli_link, $query);
	}
}

//Benutzer verlaesst Raum und geht ins Forum
//Austrittstext im Raum erzeugen, o_who auf 2 und o_raum auf -1 (community) setzen

function gehe_forum($u_id, $u_nick, $o_id, $o_raum) {
	global $mysqli_link, $t;
	
	//Austrittstext im alten raum erzeugen
	verlasse_chat($u_id, $u_nick, $o_raum);
	system_msg("", 0, $u_id, "", str_replace("%u_nick%", $u_nick, $t['betrete_forum1']));
	
	//Daten in online-tabelle richten
	$f['o_raum'] = -1; //-1 allgemein fuer community
	$f['o_who'] = "2"; //2 -> Forum
	schreibe_db("online", $f, $o_id, "o_id");
	
}

//Gelesene Beiträge des Benutzers einlesen
function lese_gelesene_postings($u_id) {
	global $mysqli_link, $u_gelesene;
	
	$sql = "select u_gelesene_postings from user
				where u_id = $u_id";
	$query = mysqli_query($mysqli_link, $sql);
	if (mysqli_num_rows($query) > 0)
		$gelesene = mysqli_result($query, 0, "u_gelesene_postings");
	$u_gelesene = unserialize($gelesene);
	mysqli_free_result($query);
}

// markiert ein komplettes Thema als gelesen
function thema_alles_gelesen($th_id, $u_id) {
	global $mysqli_link, $u_gelesene;
	
	$query = "SELECT po_id FROM posting WHERE po_th_id = " . intval($th_id);
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) > 0) {
		if (!$u_gelesene[$th_id])
			$u_gelesene[$th_id][0] = array();
		
			while ($a = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			array_push($u_gelesene[$th_id], $a['po_id']);
			//wenn schon gelesen, dann wieder raus
		}
		$u_gelesene[$th_id] = array_unique($u_gelesene[$th_id]);
		$gelesene = serialize($u_gelesene);
		$sql = "UPDATE user SET u_gelesene_postings = '$gelesene' WHERE u_id = $u_id";
		mysqli_query($mysqli_link, $sql);
	}
}

// markiert ein Thema als gelesen
function thread_alles_gelesen($th_id, $thread_id, $u_id) {
	global $mysqli_link, $u_gelesene;
	
	$query = "SELECT po_id FROM posting WHERE po_id = " . intval($thread_id) . " OR po_vater_id = " . intval($thread_id);
	$result = mysqli_query($mysqli_link, $query);
	
	$liste = array();
	foreach ($result as $val){
		$liste[] = $val['po_id'];
	}
	
	if (!$u_gelesene[$th_id]) {
		$u_gelesene[$th_id][0] = array();
	}
	
	// alle Beiträge sind im Vater in der Themaorder, dieses array, an die gelesenen anhängen
	$a = mysqli_fetch_array($result, MYSQLI_ASSOC);
	$b = explode(",", $a['po_threadorder']);
	
	for ($i = 0; $i < count($liste); $i++) {
		array_push($u_gelesene[$th_id], $liste[$i]);
	}
	
	//wenn schon gelesen, dann wieder raus
	$u_gelesene[$th_id] = array_unique($u_gelesene[$th_id]);
	
	// und zurückschreiben
	$gelesene = serialize($u_gelesene);
	$sql = "UPDATE user SET u_gelesene_postings = '$gelesene' WHERE u_id = $u_id";
	mysqli_query($mysqli_link, $sql);
}

//markiert ein posting fuer einen Benutzer als gelesen
function markiere_als_gelesen($po_id, $u_id, $th_id) {
	global $mysqli_link, $u_gelesene;
	
	if (!$u_gelesene[$th_id])
		$u_gelesene[$th_id][0] = $po_id;
	else {
		
		array_push($u_gelesene[$th_id], $po_id);
		//wenn schon gelesen, dann wieder raus
		$u_gelesene[$th_id] = array_unique($u_gelesene[$th_id]);
	}
	
	$gelesene = serialize($u_gelesene);
	
	//schreiben nicht ueber schreibe_db, da sonst
	//online-tabelle neu geschrieben wird -> hier unnoetig
	//und unperformant
	$sql = "update user set u_gelesene_postings = '$gelesene' where u_id = $u_id";
	mysqli_query($mysqli_link, $sql);
	
}

//gibt die ungelesenen Beiträge in einem Thema zurueck
//Je nachdem, ob $arr_postings sich auf ein Thema bezieht
function anzahl_ungelesene(&$arr_postings, $th_id) {
	global $u_gelesene;
	
	//kein Beitrag im Thema --> keine ungelesenen
	if (count($arr_postings) == 0)
		return 0;
	
	//kein Beitrag gelesen --> alle ungelesen
	if (!$u_gelesene[$th_id])
		return count($arr_postings);
	
	// anzahl unterschied zwischen postings im Thema und den gelesenen
	//postings des users zurueckgeben
	
	return count(array_diff($arr_postings, $u_gelesene[$th_id]));
	
}

function anzahl_ungelesene2(&$arr_postings, $th_id) {
	global $mysqli_link;
	global $u_gelesene;
	
	//kein Beitrag in Gruppe --> keine ungelesenen
	if (count($arr_postings) == 0)
		return 0;
	
	//kein Beitrag gelesen --> alle ungelesen
	if (!$u_gelesene[$th_id])
		return count($arr_postings);
	
	// anzahl unterschied zwischen postings im Thema und den gelesenen
	//postings des users zurueckgeben
	
	$sql = "select po_id, po_u_id,  po_threadorder
				from posting
				where po_vater_id = 0
				and po_th_id = " . intval($th_id) . "
				order by po_ts desc";
	$query = mysqli_query($mysqli_link, $sql);
	
	$ungelesene = 0;
	while ($posting = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		if ($posting[po_threadorder] == "0") {
			$anzreplys = 0;
			$arr_postings = array($posting[po_id]);
		} else {
			$arr_postings = explode(",", $posting[po_threadorder]);
			$anzreplys = count($arr_postings);
			//Erster Beitrag mit beruecksichtigen
			$arr_postings[] = $posting[po_id];
		}
		
		$ungelesene += anzahl_ungelesene($arr_postings, $th_id);
	}
	
	return $ungelesene;
	
}

function anzahl_ungelesener_themen(&$arr_postings, $th_id) {
	global $mysqli_link;
	global $u_gelesene;
	
	//kein Beitrag in Gruppe --> keine ungelesenen
	if (count($arr_postings) == 0) {
		return 0;
	}
	
	//kein Beitrag gelesen --> alle ungelesen
	if (!$u_gelesene[$th_id]) {
		return count($arr_postings);
	}
	
	// Anzahl Unterschied zwischen postings im Thema und den gelesenen
	//postings des users zurueckgeben
	$arr = array_diff($arr_postings, $u_gelesene[$th_id]);
	$diff = count($arr);
	reset($arr);
	
	// Wenn keine Beiträge vorhanden sind, Themen auf 0 setzen
	// Ansonsten eine kommaseparierte Liste erstellen
	$themen = count($arr) == 0 ? '0' : implode(',', $arr);
	
	$query = "SELECT po_id FROM posting WHERE po_id IN (".$themen.") ";
	
	$result = mysqli_query($mysqli_link, $query);
	$num = mysqli_num_rows($result);
	
	return $num;
}

//Prüft Benutzereingaben auf Vollständigkeit
// mode --> forum, thema, posting 
function check_input($mode) {
	global $t;
	
	$missing = "";
	
	if ($mode == "forum") {
		
		global $fo_name;
		if (!$fo_name)
			$missing = $t['missing_name_kategorie'];
		
	} else if ($mode == "thema") {
		global $th_name, $th_desc;
		if (!$th_name)
			$missing .= $t['missing_thname'];
		if (!$th_desc)
			$missing .= $t['missing_thdesc'];
		
	} else if ($mode == "posting") {
		global $po_titel, $po_text;
		if (strlen(trim($po_titel)) <= 0)
			$missing .= $t['missing_potitel'];
		if (strlen(trim($po_text)) <= 0)
			$missing .= $t['missing_potext'];
		
	}
	
	return $missing;
}

//neues Forum in Datenbank schreiben
function schreibe_forum() {
	global $fo_id, $fo_name, $fo_admin, $mysqli_link;
	global $fo_gast, $fo_user;
	
	$f['fo_name'] = $fo_name;
	$f['fo_admin'] = $fo_gast + $fo_user + 1;
	
	//groesste Order holen
	$sql = "select max(fo_order) as maxorder from forum";
	$query = mysqli_query($mysqli_link, $sql);
	$maxorder = mysqli_result($query, 0, "maxorder");
	mysqli_free_result($query);
	if ($maxorder)
		$maxorder++;
	else $maxorder = 1;
	$f['fo_order'] = $maxorder;
	$fo_id = schreibe_db("forum", $f, "", "fo_id");
	
	//Damit leeres Forum angezeigt wird, dummy-eintrag in Thema-Tabelle
	$ff["th_fo_id"] = $fo_id;
	$ff["th_name"] = "dummy-thema";
	$ff["th_anzthreads"] = 0;
	$ff["th_anzreplys"] = 0;
	$ff["th_order"] = 0;
	schreibe_db("thema", $ff, "", "th_id");
	
}

//Forum aendern
function aendere_forum() {
	global $fo_id, $fo_name, $fo_admin, $mysqli_link;
	global $fo_gast, $fo_user;
	
	$f['fo_name'] = $fo_name;
	$f['fo_admin'] = $fo_gast + $fo_user + 1;
	schreibe_db("forum", $f, $fo_id, "fo_id");
	
}

//Schiebt Forum in Darstellungsreihenfolge nach oben
function forum_up($fo_id, $fo_order) {
	global $mysqli_link;
	
	if (!$fo_id)
		return;
	
	//forum über aktuellem Forum holen
	$sql = "select fo_id, fo_order as prev_order
				from forum
				where fo_order < " . intval($fo_order) . "
				order by fo_order desc
				limit 1";
	$query = mysqli_query($mysqli_link, $sql);
	
	$numrows = mysqli_num_rows($query);
	
	//ist Forum oberstes Forum?
	if ($numrows == 1) {
		
		$prev_order = mysqli_result($query, 0, "prev_order");
		$prev_id = mysqli_result($query, 0, "fo_id");
		mysqli_free_result($query);
		
		//nein -> orders vertauschen
		$f['fo_order'] = $fo_order;
		schreibe_db("forum", $f, $prev_id, "fo_id");
		
		$f['fo_order'] = $prev_order;
		schreibe_db("forum", $f, $fo_id, "fo_id");
		
	} else {
		mysqli_free_result($query);
	}
	
}

//Schiebt Forum in Darstellungsreihenfolge nach oben
function forum_down($fo_id, $fo_order) {
	global $mysqli_link;
	
	if (!$fo_id)
		return;
	
	//forum über aktuellem Forum holen
	$sql = "select fo_id, fo_order as next_order
				from forum
				where fo_order > " . intval($fo_order) . "
				order by fo_order 
				limit 1";
	$query = mysqli_query($mysqli_link, $sql);
	
	$numrows = mysqli_num_rows($query);
	
	//ist Thema schon letztes Thema?
	if ($numrows == 1) {
		$next_order = mysqli_result($query, 0, "next_order");
		$next_id = mysqli_result($query, 0, "fo_id");
		mysqli_free_result($query);
		
		//nein -> orders vertauschen
		$f['fo_order'] = $fo_order;
		schreibe_db("forum", $f, $next_id, "fo_id");
		
		$f['fo_order'] = $next_order;
		schreibe_db("forum", $f, $fo_id, "fo_id");
		
	} else {
		mysqli_free_result($query);
	}
	
}

//Komplettes Forum mit allen Themen und postings loeschen
function loesche_forum($fo_id) {
	global $mysqli_link, $t;
	
	if (!$fo_id) {
		return;
	}
	
	$fo_id = intval($fo_id);
	
	$sql = "SELECT fo_name FROM forum WHERE fo_id=$fo_id";
	$query = mysqli_query($mysqli_link, $sql);
	$fo_name = mysqli_result($query, 0, "fo_name");
	mysqli_free_result($query);
	
	$sql = "SELECT th_id FROM thema WHERE th_fo_id=$fo_id";
	$query = mysqli_query($mysqli_link, $sql);
	while ($thema = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		
		$delsql = "DELETE FROM posting WHERE po_th_id=$thema[th_id]";
		mysqli_query($mysqli_link, $delsql);
		
	}
	mysqli_free_result($query);
	
	$sql = "DELETE FROM thema WHERE th_fo_id=$fo_id";
	mysqli_query($mysqli_link, $sql);
	
	$sql = "DELETE FROM forum WHERE fo_id=$fo_id";
	mysqli_query($mysqli_link, $sql);
	
	$box = $t['erfolgsmeldung'];
	$text = str_replace("%forum%", $fo_name, $t['forum_geloescht']);
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}

//Thema in Datenbank schreiben
function schreibe_thema($th_id = 0) {
	global $fo_id, $th_name, $th_desc, $mysqli_link, $th_forumwechsel, $th_verschiebe_nach;
	
	//neues Thema
	if ($th_id == 0) {
		$f['th_fo_id'] = $fo_id;
		$f['th_name'] = $th_name;
		$f['th_desc'] = $th_desc;
		$f['th_anzthreads'] = 0;
		$f['th_anzreplys'] = 0;
		
		//groesste Order holen
		$sql = "select max(th_order) as maxorder from thema where th_fo_id=" . intval($fo_id);
		$query = mysqli_query($mysqli_link, $sql);
		$maxorder = mysqli_result($query, 0, "maxorder");
		mysqli_free_result($query);
		if ($maxorder)
			$maxorder++;
		else $maxorder = 1;
		$f['th_order'] = $maxorder;
		$th_id = schreibe_db("thema", $f, "", "th_id");
	} else { //Thema editieren
	
		if ($th_forumwechsel == "Y"
			&& preg_match("/^([0-9])+$/i", $th_verschiebe_nach)) {
			//groesste Order holen
			$sql = "select max(th_order) as maxorder from thema where th_fo_id=" . intval($th_verschiebe_nach);
			$query = mysqli_query($mysqli_link, $sql);
			$maxorder = mysqli_result($query, 0, "maxorder");
			mysqli_free_result($query);
			if ($maxorder)
				$maxorder++;
			else $maxorder = 1;
			
			$f['th_fo_id'] = $th_verschiebe_nach;
			$f['th_order'] = $maxorder;
		}
		
		$f['th_name'] = $th_name;
		$f['th_desc'] = $th_desc;
		
		schreibe_db("thema", $f, $th_id, "th_id");
	}
}

//Schiebt Thema in Darstellungsreihenfolge nach oben
function thema_up($th_id, $th_order, $fo_id) {
	global $mysqli_link;
	
	if (!$th_id || !$fo_id)
		return;
	
	//thema über aktuellem Thema holen
	$sql = "select th_id, th_order as prev_order
				from thema
				where th_fo_id = " . intval($fo_id) . "
				and th_order < " . intval($th_order) . "
				order by th_order desc
				limit 1";
	$query = mysqli_query($mysqli_link, $sql);
	$prev_order = mysqli_result($query, 0, "prev_order");
	$prev_id = mysqli_result($query, 0, "th_id");
	mysqli_free_result($query);
	
	//ist Thema oberstes Thema?
	if ($prev_order > 0) {
		
		//nein -> orders vertauschen
		$f['th_order'] = $th_order;
		schreibe_db("thema", $f, $prev_id, "th_id");
		
		$f['th_order'] = $prev_order;
		schreibe_db("thema", $f, $th_id, "th_id");
		
	}
	
}

//Schiebt Thema in Darstellungsreihenfolge nach unten
function thema_down($th_id, $th_order, $fo_id) {
	global $mysqli_link;
	
	if (!$th_id || !$fo_id)
		return;
	
	//thema unter aktuellem Thema holen
	$sql = "select th_id, th_order as next_order
				from thema
				where th_fo_id = " . intval($fo_id) . "
				and th_order > " . intval($th_order) . "
				order by th_order
				limit 1";
	$query = mysqli_query($mysqli_link, $sql);
	
	$numrows = mysqli_num_rows($query);
	
	//ist Thema schon letztes Thema?
	if ($numrows == 1) {
		$next_order = mysqli_result($query, 0, "next_order");
		$next_id = mysqli_result($query, 0, "th_id");
		mysqli_free_result($query);
		
		//nein -> orders vertauschen
		$f['th_order'] = $th_order;
		schreibe_db("thema", $f, $next_id, "th_id");
		
		$f['th_order'] = $next_order;
		schreibe_db("thema", $f, $th_id, "th_id");
		
	} else {
		mysqli_free_result($query);
	}
	
}

//Komplettes Thema mit allen Beiträgen loeschen
function loesche_thema($th_id) {
	global $mysqli_link, $t;
	
	if (!$th_id) {
		return;
	}
	
	$th_id = intval($th_id);
	
	$sql = "SELECT th_name FROM thema WHERE th_id=$th_id";
	$query = mysqli_query($mysqli_link, $sql);
	$th_name = @mysqli_result($query, 0, "th_name");
	mysqli_free_result($query);
	
	$delsql = "DELETE FROM posting WHERE po_th_id=$th_id";
	mysqli_query($mysqli_link, $delsql);
	
	$sql = "DELETE FROM thema WHERE th_id=$th_id";
	mysqli_query($mysqli_link, $sql);
	
	$box = $t['erfolgsmeldung'];
	$text =  str_replace("%kategorie%", $th_name, $t['kategorie_geloescht']);
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
}

//schreibt neuen/editierten Beitrag in die Datenbank
function schreibe_posting() {
	global $mysqli_link, $th_id, $po_vater_id, $u_id, $po_id, $po_tiefe, $u_nick;
	global $po_titel, $po_text, $thread, $mode, $user_id, $autor;
	global $po_topposting, $po_threadgesperrt, $forum_admin, $t, $forum_aenderungsanzeige;
	
	if ($mode == "edit") {
		
		//muss autor neu gesetzt werden?
		if ($forum_admin && $autor) {
			$autor = intval($autor);
			
			if (!preg_match("/[a-z]|[A-Z]/", $autor))
				$sql = "SELECT `u_id` FROM `user` WHERE `u_id`=$autor";
			else $sql = "SELECT `u_id` FROM `user` WHERE `u_nick`='$autor'";
			$query = mysqli_query($mysqli_link, $sql);
			if (mysqli_num_rows($query) > 0) {
				$u_id_neu = mysqli_result($query, 0, "u_id");
			}
			
			/*
			if (!$u_id_neu) {
				echo "<b>Ein Benutzer mit dem Benutzernamen/der ID $autor existiert nicht!</b>";
			} else if ($u_id_neu != $user_id) {
				$f['po_u_id'] = $u_id_neu;
			}
			*/
		}
		
		if ($forum_admin) {
			$f['po_topposting'] = $po_topposting;
			$f['po_threadgesperrt'] = $po_threadgesperrt;
		}
		
		$f['po_titel'] = htmlspecialchars($po_titel);
		$f['po_text'] = htmlspecialchars(erzeuge_umbruch($po_text, 80));
		
		if ($forum_aenderungsanzeige == "1") {
			$append = $t['letzte_aenderung'];
			$append = str_replace("%datum%", date("d.m.Y"), $append);
			$append = str_replace("%uhrzeit%", date("H:i"), $append);
			$append = str_replace("%user%", $u_nick, $append);
			
			$f['po_text'] .= $append;
		}
		
		schreibe_db("posting", $f, $po_id, "po_id");
	} else { //neuer Beitrag
	
		$f['po_th_id'] = $th_id;
		$f['po_u_id'] = $u_id;
		$f['po_vater_id'] = $po_vater_id;
		$f['po_tiefe'] = $po_tiefe;
		$f['po_titel'] = htmlspecialchars($po_titel);
		$f['po_text'] = htmlspecialchars(erzeuge_umbruch($po_text, 80));
		$f['po_ts'] = time();
		$f['po_threadts'] = time();
		if ($po_vater_id != 0)
			$f['po_threadorder'] = 1;
		else $f['po_threadorder'] = 0;
		
		//Beitrag schreiben
		$new_po_id = schreibe_db("posting", $f, "", "po_id");
		
		//ist was schiefgelaufen?
		if (!$new_po_id)
			exit;
		
		//falls reply muss po_threadorder des vaters neu geschrieben werden
		if ($po_vater_id != 0) {
			
			//po_threadorder des threadvaters neu schreiben
			//dazu Tabelle posting locken
			$sql = "LOCK TABLES posting WRITE";
			@mysqli_query($mysqli_link, $sql);
			
			//alte Themaorder holen
			$sql = "select po_threadorder from posting where po_id = " . intval($thread);
			$query = mysqli_query($mysqli_link, $sql);
			$threadorder = mysqli_result($query, 0, "po_threadorder");
			mysqli_free_result($query);
			
			//erste Antwort?
			if ($threadorder == "0")
				$threadorder = $new_po_id;
			else {
				//jetzt hab ich arbeit...
				//rekursiv der unterste Beitrag dieses Teilbaums holen
				$insert_po_id = hole_letzten($po_vater_id, $new_po_id);
				
				//alte threadorder in feld aufsplitten
				$threadorder_array = explode(",", $threadorder);
				$threadorder_array_new = array();
				$i = 0;
				while (list($k, $v) = each($threadorder_array)) {
					if ($v == $insert_po_id) {
						$threadorder_array_new[$i] = $v;
						$i++;
						$threadorder_array_new[$i] = $new_po_id;
						
					} else {
						$threadorder_array_new[$i] = $v;
					}
					$i++;
				}
				$threadorder = implode(",", $threadorder_array_new);
			}
			
			//threadorder neu schreiben
			$sql = "update posting
								set po_threadorder = '$threadorder', po_threadts = "
				. time()
				. "
								where po_id = $thread";
			mysqli_query($mysqli_link, $sql);
			
			#print "neue threadorder: $threadorder<br>";
			
			//schliesslich noch die markierung des letzten in der Ebene entfernen
			$sql = "update posting
								set po_threadorder = '0'
								where po_threadorder = '1'
								and po_id <> $new_po_id
								and po_vater_id = " . intval($po_vater_id);
			mysqli_query($mysqli_link, $sql);
			
			//Tabellen wieder freigeben
			$sql = "UNLOCK TABLES";
			@mysqli_query($mysqli_link, $sql);
			
		} else {
			//Thema neu setzen
			$thread = $new_po_id;
			
		}
		
		//th_postings muss neu geschrieben werden
		//anz_threads und anz_replys im Thema setzen
		//erst Tabelle thema sperren
		$sql = "LOCK TABLES thema WRITE";
		@mysqli_query($mysqli_link, $sql);
		
		//altes th_postings und anz_threads und anz_replys holen
		$sql = "select th_postings, th_anzthreads, th_anzreplys from thema where th_id = " . intval($th_id);
		$query = mysqli_query($mysqli_link, $sql);
		$postings = mysqli_result($query, 0, "th_postings");
		$anzthreads = mysqli_result($query, 0, "th_anzthreads");
		$anzreplys = mysqli_result($query, 0, "th_anzreplys");
		
		if (!$postings) //erster Beitrag in diesem Thema
			$postings = $new_po_id;
		else { //schon postings da
			$postings_array = explode(",", $postings);
			$postings_array[] = $new_po_id;
			$postings = implode(",", $postings_array);
		}
		
		if ($po_vater_id == 0) {
			//neues Thema
			$anzthreads++;
		} else {
			//neue Antwort
			$anzreplys++;
		}
		
		//schreiben
		$sql = "update thema
						set th_postings = '$postings',
						th_anzthreads = $anzthreads,
						th_anzreplys = $anzreplys
						where th_id = " . intval($th_id);
		mysqli_query($mysqli_link, $sql);
		
		//Tabellen wieder freigeben
		$sql = "UNLOCK TABLES";
		@mysqli_query($mysqli_link, $sql);
		
	}
	
	if (!isset($new_po_id))
		$new_po_id = 0;
	return $new_po_id;
}

//holt ausgehend von root_id den letzten Beitrag
//im Teilbaum unterhalb der root_id
function hole_letzten($root_id, $new_po_id) {
	global $mysqli_link;
	$sql = "select po_id
				from posting
				where po_vater_id = " . intval($root_id) . "
				and po_id <> " . intval($new_po_id) . "
				order by po_ts desc
				limit 1";
	
	$query = mysqli_query($mysqli_link, $sql);
	$anzahl = mysqli_num_rows($query);
	if ($anzahl > 0)
		$new_root_id = mysqli_result($query, 0, "po_id");
	mysqli_free_result($query);
	
	if ($anzahl > 0) {
		//es geht noch tiefer...
		$retval = hole_letzten($new_root_id, $new_po_id);
	} else {
		$retval = $root_id;
	}
	
	return $retval;
	
}

//loescht den Beitrag und alle Antworten darauf
function loesche_posting() {
	global $mysqli_link, $th_id, $po_id, $thread;
	global $arr_delete;
	global $punkte_pro_posting, $t;
	
	$arr_delete = array();
	
	//tabelle posting und thema locken
	$sql = "LOCK TABLES posting, thema WRITE";
	@mysqli_query($mysqli_link, $sql);
	
	//rekursiv alle zu loeschenden postings in feld einlesen
	$arr_delete[] = $po_id;
	hole_alle_unter($po_id);
	
	//po_threadorder neu schreiben
	//nur relevant, wenn Beitrag nicht erster im Thema ist
	//ansonsten wird es eh geloescht
	if ($po_id != $thread) {
		
		$sql = "SELECT po_threadorder, po_ts FROM posting WHERE po_id=" . intval($thread);
		$query = mysqli_query($mysqli_link, $sql);
		$threadorder = mysqli_result($query, 0, "po_threadorder");
		$new_ts = mysqli_result($query, 0, "po_ts");
		
		//in array einlesen und zu loeschende rausschmeissen
		$arr_new_threadorder = explode(",", $threadorder);
		
		$arr_new_threadorder = array_diff($arr_new_threadorder, $arr_delete);
		
		if (count($arr_new_threadorder) == 0) {
			$arr_new_threadorder[0] = 0;
		} else {
			// beim Löschen eines Beitrags wird hier die letzte änderung aller postings gesucht, damit 
			// der thread_ts wieder stimmt
			$new_ts = '0000000000';
			$new_threadorder = implode(",", $arr_new_threadorder);
			$arr_new_threadorder = explode(",", $new_threadorder);
			for ($i = 0; $i < count($arr_new_threadorder); $i++) {
				$sql = "SELECT po_ts FROM posting WHERE po_id = " . intval($arr_new_threadorder[$i]);
				$query = mysqli_query($mysqli_link, $sql);
				$ts = mysqli_result($query, 0, "po_ts");
				if ($ts > $new_ts) {
					$new_ts = $ts;
				}
			}
			
		}
		
		$new_threadorder = implode(",", $arr_new_threadorder);
		
		$sql = "UPDATE posting SET po_threadorder = '$new_threadorder', po_threadts = $new_ts WHERE po_id = $thread";
		mysqli_query($mysqli_link, $sql);
		
		//eventuell letzter Beitrag auf Ebene neu markieren
		$sql = "SELECT po_vater_id, po_threadorder FROM posting WHERE po_id = " . intval($po_id);
		$query = mysqli_query($mysqli_link, $sql);
		$threadorder = mysqli_result($query, 0, "po_threadorder");
		$vater_id = mysqli_result($query, 0, "po_vater_id");
		
		//falls letztes posting, dann neu setzen
		if ($threadorder == "1") {
			$sql = "SELECT po_id FROM posting WHERE po_vater_id = $vater_id AND po_id <> " . intval($po_id) . " ORDER BY po_ts desc LIMIT 1";
			$query = mysqli_query($mysqli_link, $sql);
			if (mysqli_num_rows($query) > 0) {
				
				$po_id_update = mysqli_result($query, 0, "po_id");
				
				$sql = "UPDATE posting SET po_threadorder = '1' WHERE po_id = $po_id_update";
				mysqli_query($mysqli_link, $sql);
			}
		}
		
	}
	
	//eintragungen in Thema neu schreiben
	
	$sql = "select th_anzthreads, th_anzreplys, th_postings from thema where th_id=" . intval($th_id);
	$query = mysqli_query($mysqli_link, $sql);
	$postings = mysqli_result($query, 0, "th_postings");
	$anzthreads = mysqli_result($query, 0, "th_anzthreads");
	$anzreplys = mysqli_result($query, 0, "th_anzreplys");
	
	//in array einlesen und zu loeschende rausschmeissen
	$arr_new_postings = explode(",", $postings);
	
	$arr_new_postings = array_diff($arr_new_postings, $arr_delete);
	
	$new_postings = implode(",", $arr_new_postings);
	
	//th_anzthreads und th_anzreplys neu schreiben
	if ($po_id == $thread) {
		
		$anzthreads--;
		$anzreplys = $anzreplys - count($arr_delete) + 1;
		
	} else {
		
		$anzreplys = $anzreplys - count($arr_delete);
		
	}
	
	$sql = "UPDATE thema SET th_anzthreads = $anzthreads, th_anzreplys = $anzreplys, th_postings = '$new_postings' WHERE th_id = " . intval($th_id);
	mysqli_query($mysqli_link, $sql);
	
	// Punkte abziehen
	reset($arr_delete);
	$zusammenfassung = "";
	while (list($k, $v) = @each($arr_delete)) {
		$sql = "SELECT po_u_id FROM posting WHERE po_id = " . intval($v);
		$result = mysqli_query($mysqli_link, $sql);
		if ($result && mysqli_num_rows($result) == 1) {
			$po_u_id = mysqli_result($result, 0, 0);
			if ($po_u_id) {
				$zusammenfassung .= $t['forum_punkte2'] . punkte_offline($punkte_pro_posting * (-1), $po_u_id) . "<br>";
			}
		}
		mysqli_free_result($result);
	}

	zeige_tabelle_zentriert($t['forum_punkte_abgezogen'], $zusammenfassung);
	
	reset($arr_delete);
	while (list($k, $v) = @each($arr_delete)) {
		$sql = "DELETE FROM posting WHERE po_id = " . intval($v);
		mysqli_query($mysqli_link, $sql);
	}
	
	//Tabellen wieder freigeben
	$sql = "UNLOCK TABLES";
	@mysqli_query($mysqli_link, $sql);
	
}

//holt zum Löschen alle Beiträge unterhalb des vaters
function hole_alle_unter($vater_id) {
	global $mysqli_link, $arr_delete;
	
	$sql = "select po_id
				from posting
				where po_vater_id = " . intval($vater_id);
	$query = mysqli_query($mysqli_link, $sql);
	$anzahl = mysqli_num_rows($query);
	if ($anzahl > 0) {
		while ($posting = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
			
			$arr_delete[] = $posting['po_id'];
			hole_alle_unter($posting['po_id']);
			
		}
	}
	mysqli_free_result($query);
	
}

//bereinigt jede Woche einmal die Spalte u_gelesene_postings 
//des users $u_id
function bereinige_u_gelesene_postings($u_id) {
	global $mysqli_link;
	$u_id = intval($u_id);
	
	$sql = "select u_gelesene_postings, u_lastclean 
		from user
		where u_id = $u_id";
	$query = mysqli_query($mysqli_link, $sql);
	
	if ($query && mysqli_num_rows($query) > 0) {
		
		$lastclean = mysqli_result($query, 0, "u_lastclean");
		$gelesene = mysqli_result($query, 0, "u_gelesene_postings");
		if ($lastclean == "0") { //keine Bereinignng nötig
		
			$lastclean = time();
			$sql = "update user set u_lastclean = $lastclean where u_id = $u_id";
			mysqli_query($mysqli_link, $sql);
			
		} else if ($lastclean < (time() - 2592000)) { //Bereinigung nötig
			$lastclean = time();
			$arr_gelesene = unserialize($gelesene);
			
			//alle Beiträge in Feld einlesen
			$sql = "select po_id from posting order by po_id";
			$query = mysqli_query($mysqli_link, $sql);
			$arr_postings = array();
			while ($posting = mysqli_fetch_array($query, MYSQLI_ASSOC))
				$arr_postings[] = $posting['po_id'];
			
			if (is_array($arr_gelesene)) {
				
				while (list($k, $v) = each($arr_gelesene)) {
					
					$arr_gelesene[$k] = array_intersect($arr_gelesene[$k],
						$arr_postings);
					
				}
				
			}
			
			$gelesene_neu = serialize($arr_gelesene);
			
			$sql = "update user set 
				u_lastclean = $lastclean,
				u_gelesene_postings = '$gelesene_neu'
				where u_id = $u_id";
			
			mysqli_query($mysqli_link, $sql);
		}
		
	}
	
}

//bereinigt wenn ein SU das Forum betritt die Anzahl der beiträge und Replays mim Thema
function bereinige_anz_in_thema() {
	global $mysqli_link;
	
	$sql = "select th_id from thema order by th_id";
	$query = mysqli_query($mysqli_link, $sql);
	
	if ($query && mysqli_num_rows($query) > 0) {
		while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
			
			$sql = "LOCK TABLES posting WRITE, thema WRITE";
			@mysqli_query($mysqli_link, $sql);
			
			$sql2 = "select count(*) from posting where po_vater_id = 0 and po_th_id = "
				. $row['th_id'];
			$query2 = mysqli_query($mysqli_link, $sql2);
			$anzahl_thread = mysqli_result($query2, 0, 0);
			
			$sql2 = "select count(*) from posting where po_vater_id <> 0 and po_th_id = "
				. $row['th_id'];
			$query2 = mysqli_query($mysqli_link, $sql2);
			$anzahl_reply = mysqli_result($query2, 0, 0);
			
			$sql2 = "update thema set th_anzthreads = $anzahl_thread, th_anzreplys = $anzahl_reply  where th_id = $row[th_id]";
			mysqli_query($mysqli_link, $sql2);
			
			$sql = "UNLOCK TABLES";
			@mysqli_query($mysqli_link, $sql);
			
		}
	}
}

function verschiebe_posting_ausfuehren() {
	global $mysqli_link, $thread_verschiebe, $verschiebe_von, $verschiebe_nach;
	
	if (!preg_match("/^([0-9])+$/i", $thread_verschiebe))
		exit();
	if (!preg_match("/^([0-9])+$/i", $verschiebe_von))
		exit();
	if (!preg_match("/^([0-9])+$/i", $verschiebe_nach))
		exit();
	
	// Ändert alle Beiträge eine Themas
	$sql = "SELECT po_threadorder FROM posting WHERE po_id = " . intval($thread_verschiebe) . " AND po_th_id = " . intval($verschiebe_von);
	$query = mysqli_query($mysqli_link, $sql);
	if ($query && mysqli_num_rows($query) == 1) {
		$sql = "LOCK TABLES posting WRITE, thema WRITE";
		@mysqli_query($mysqli_link, $sql);
		
		// Verschiebt alle Kinder wenn vorhanden
		$postings = mysqli_result($query, 0, "po_threadorder");
		if (trim($postings) <> "0") {
			$postings2 = explode(",", $postings);
			for ($i = 0; $i < count($postings2); $i++) {
				$sqlupdate = "UPDATE posting SET po_th_id = " . intval($verschiebe_nach) . " WHERE po_id = " . $postings2[$i];
				mysqli_query($mysqli_link, $sqlupdate);
			}
		}
		
		// Verschiebt den Vater
		$sqlupdate = "UPDATE posting SET po_th_id = " . intval($verschiebe_nach) . " WHERE po_id = " . intval($thread_verschiebe);
		mysqli_query($mysqli_link, $sqlupdate);
		
		// Baut Themaorder des Themas ALT und NEU komplett neu auf
		// Da manchmal auch diese Themaorder kaputt geht
		$sql2 = "SELECT po_id FROM posting WHERE po_th_id = " . intval($verschiebe_von);
		$query2 = mysqli_query($mysqli_link, $sql2);
		$neuethreadorder = "0";
		if ($query2 && mysqli_num_rows($query2) > 0) {
			while ($row2 = mysqli_fetch_array($query2, MYSQLI_ASSOC)) {
				if ($neuethreadorder == "0")
					$neuethreadorder = "$row2[po_id]";
				else $neuethreadorder .= ",$row2[po_id]";
			}
		}
		$sqlupdate = "UPDATE thema SET th_postings = '$neuethreadorder' WHERE th_id = " . intval($verschiebe_von);
		mysqli_query($mysqli_link, $sqlupdate);
		
		$sql2 = "SELECT po_id FROM posting WHERE po_th_id = " . intval($verschiebe_nach);
		$query2 = mysqli_query($mysqli_link, $sql2);
		$neuethreadorder = "0";
		if ($query2 && mysqli_num_rows($query2) > 0) {
			while ($row2 = mysqli_fetch_array($query2, MYSQLI_ASSOC)) {
				if ($neuethreadorder == "0")
					$neuethreadorder = "$row2[po_id]";
				else $neuethreadorder .= ",$row2[po_id]";
			}
		}
		$sqlupdate = "UPDATE thema SET th_postings = '$neuethreadorder' WHERE th_id = " . intval($verschiebe_nach);
		mysqli_query($mysqli_link, $sqlupdate);
		
		$sql = "UNLOCK TABLES";
		@mysqli_query($mysqli_link, $sql);
		
		bereinige_anz_in_thema();
	}
	
}

function ersetze_smilies($text) {
	global $sprache, $smilie, $u_id;
	
	// Hole alle benötigten Einstellungen des Benutzers
	$benutzerdaten = hole_benutzer_einstellungen($u_id, "standard");
	
	preg_match_all("/(&amp;[^ |^<]+)/", $text, $test, PREG_PATTERN_ORDER);
	
	while (list($i, $smilie_code) = each($test[0])) {
		$smilie_code2 = str_replace("&amp;", "&", $smilie_code);
		$smilie_code2 = chop($smilie_code2);
		if ($smilie[$smilie_code2]) {
			$text = str_replace($smilie_code, "<img src=\"images/smilies/style-" . $benutzerdaten['u_layout_farbe'] . "/" . $smilie[$smilie_code2] . "\">", $text);
		}
	}
	
	return $text;
}

// erzeugt Aktionen bei Antwort auf einen Beitrag
function aktion_sofort($po_id, $po_vater_id, $thread) {
	global $mysqli_link, $t, $u_id;
	//aktionen nur fuer Antworten
	if ($po_vater_id > 0) {
		
		$sql = "SELECT po_u_id, date_format(from_unixtime(po_ts), '%d.%m.%Y') AS po_date, po_titel, th_name, fo_name 
			FROM posting, thema, forum WHERE po_id = " . intval($po_vater_id) . " AND po_th_id = th_id AND th_fo_id = fo_id";
		
		$query = mysqli_query($mysqli_link, $sql);
		
		if ($query && mysqli_num_rows($query) > 0) {
			
			//Daten des Vaters holen
			$user = mysqli_result($query, 0, "po_u_id");
			$po_ts = mysqli_result($query, 0, "po_date");
			$po_titel = mysqli_result($query, 0, "po_titel");
			$thema = mysqli_result($query, 0, "th_name");
			$forum = mysqli_result($query, 0, "fo_name");
			
			mysqli_free_result($query);
			
		} else return;
		
		$sql = "select u_id, u_nick, 
			date_format(from_unixtime(po_ts), '%d.%m.%Y %H:%i') as po_date, po_titel  
			from user, posting 
			where po_u_id = u_id 
			and po_id = " . intval($po_id);
		
		$query = mysqli_query($mysqli_link, $sql);
		
		if ($query && mysqli_num_rows($query) > 0) {
			$user_from_id = mysqli_result($query, 0, "u_id");
			$user_from_nick = mysqli_result($query, 0, "u_nick");
			$po_ts_antwort = mysqli_result($query, 0, "po_date");
			$po_titel_antwort = mysqli_result($query, 0, "po_titel");
			mysqli_free_result($query);
		} else return;
		
		//Ist betroffener Benutzer Online?
		$online = ist_online($user);
		
		if ($online) {
			$wann = "Sofort/Online";
		} else {
			$wann = "Sofort/Offline";
		}
		
		//Themaorder fuer dieses Thema holen
		$sql = "select po_threadorder 
			from posting 
			where po_id=" . intval($thread);
		$query = mysqli_query($mysqli_link, $sql);
		if ($query && mysqli_num_rows($query) == 1) {
			
			$threadorder = mysqli_result($query, 0, "po_threadorder");
			
		} else return;
		
		$baum = erzeuge_baum($threadorder, $po_id, $thread);
		
		$text['po_titel'] = $po_titel;
		$text['po_ts'] = $po_ts;
		$text['forum'] = $forum;
		$text['thema'] = $thema;
		$text['user_from_nick'] = $user_from_nick;
		$text['po_titel_antwort'] = $po_titel_antwort;
		$text['po_ts_antwort'] = $po_ts_antwort;
		$text['baum'] = $baum;
		
		aktion($u_id, $wann, $user, $user_from_id, "", "Antwort auf eigenen Beitrag", $text);
	}
	
}

?>