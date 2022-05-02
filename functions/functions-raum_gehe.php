<?php

function raum_gehe($o_id, $u_id, $u_nick, $raum_alt, $raum_neu) {
	// user $u_id/$u_nick geht von $raum_alt in Raum $raum_neu
	// Nachricht in Raum $raum_alt wird erzeugt
	// ID des neuen Raums wird zurückgeliefert
	
	$raum_alt = intval($raum_alt);
	$raum_neu = intval($raum_neu);
	
	global $admin, $u_level, $u_punkte_gesamt, $lang, $lobby, $timeout;
	global $raum_eintrittsnachricht_anzeige_deaktivieren, $raum_austrittsnachricht_anzeige_deaktivieren;
	global $raum_eintrittsnachricht_kurzform, $raum_austrittsnachricht_kurzform;
	
	// Info zu altem Raum lesen
	$query = pdoQuery("SELECT `r_name`, `r_status1`, `r_austritt`, `r_min_punkte` FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$raum_alt]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$alt = $query->fetch();
	}
	
	// Ist Benutzer aus dem Raum ausgesperrt?
	$query = pdoQuery("SELECT `s_id` FROM `sperre` WHERE `s_raum` = :s_raum AND `s_user` = :s_user", [':s_raum'=>$raum_neu, ':s_user'=>$u_id]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 0) {
		$gesperrt = 0;
	} else {
		$gesperrt = 1;
	}
	
	// Info zu neuem Raum lesen
	$query = pdoQuery("SELECT * FROM `raum` WHERE `r_id` = :r_id", [':r_id'=>$raum_neu]);
	
	$resultCount = $query->rowCount();
	if ($resultCount == 1) {
		$neu = $query->fetch();
		
		// Online Punkte holen, damit der Benutzer zum Raumwechsel nicht ein/ausloggen muss
		$o_punkte = 0;
		$query2 = pdoQuery("SELECT `o_punkte` FROM `online` WHERE `o_id` = :o_id", [':o_id'=>intval($o_id)]);
		
		$result2Count = $query2->rowCount();
		if ($result2Count == 1) {
			$online = $query2->fetch();
			$o_punkte = $online['o_punkte'];
			unset($online);
		}
		unset($query2);
		unset($result2);
		
		// wenn hier nach Erweitertefeatures oder Punkte geprüft werden würde, was Sinn machen würde,
		// kommen Benutzer aus Kostenlosen chats, die mit der mainChat Community verbunden sind, trotzdem in den Raum, 
		// trotz zu wenigen Punkten
		if (($neu['r_name'] != $lobby) && ($neu['r_min_punkte'] > ($u_punkte_gesamt + $o_punkte)) && !$admin && $u_level != "A") {
			$zuwenigpunkte = 1;
		} else {
			$zuwenigpunkte = 0;
		}
		
		$raumwechsel = false;
		// Prüfen ob Raum geschlossen oder Admin
		// Prüfen, ob Raumwechsel erlaubt...
		
		// Raumwechsel erlaubt wenn Raum nicht geschlossen und user nicht gesperrt.
		if ($neu['r_status1'] == "G" || $neu['r_status1'] == "M" || $zuwenigpunkte == 1) {
			// Raum geschlossen. nur rein, wenn auf invite liste.
			$query = pdoQuery("SELECT `inv_user` FROM `invite` WHERE `inv_raum` = :inv_raum AND `inv_user` = :inv_user", [':inv_raum'=>$neu['r_id'], ':inv_user'=>$u_id]);
			
			$resultCount = $query->rowCount();
			if ($resultCount > 0) {
				$raumwechsel = true;
			}
			
			// oder falls user=raumbesitzer...
			// macht wenig sinn, das ein RB in seinen Raum ein ! angeben muss
			if ($neu['r_besitzer'] == $u_id) {
				$raumwechsel = true;
			}
		} else {
			// Raum offen, nur rein, wenn nicht gesperrt.
			if ($gesperrt == 0 && $zuwenigpunkte == 0) {
				$raumwechsel = true;
			}
		}
		
		// für admin Raumwechsel erlaubt.
		if ($admin) {
			$raumwechsel = true;
		}
		
		// Darf Raum nun betreten werden?
		if ($raumwechsel) {
			// Raum verlassen
			
			// Neuen Raum eintragen
			$f['o_raum'] = $raum_neu;
			$f['o_who'] = "0";
			schreibe_online($f, "betreten", $u_id);
			
			// Austrittstext
			if ($lang['raum_gehe1']) {
				$txt = $lang['raum_gehe1'] . " " . $alt['r_name'] . ":";
			} else {
				unset($txt);
			}
			
			if ($raum_austrittsnachricht_kurzform == "1") {
				unset($txt);
			}
			
			if (strlen($alt['r_austritt']) > 0) {
				$txt = "<b>$txt</b> ".$alt['r_austritt']."<br>";
			} else {
				unset($txt);
			}
			
			if ($raum_austrittsnachricht_anzeige_deaktivieren == "1") {
				unset($txt);
			}
			if (!isset($txt)) {
				$txt = "";
			}
			
			// Trenner zwischen den Räumen, Austrittstext
			system_msg("", 0, $u_id, "", " ");
			system_msg("", 0, $u_id, "", $txt . "<br>\n");
			
			if( !isset($u_id) || $u_id == null || $u_id == "") {
				global $kontakt;
				email_senden($kontakt, "user_id leer2", "Nickname: " . $u_nick);
			}
			
			// Raum betreten
			nachricht_betrete($u_id, $raum_neu, $u_nick, $neu['r_name']);
			
			// Nachricht falls gesperrt ausgeben
			if ($gesperrt || $zuwenigpunkte) {
				system_msg("", 0, $u_id, "", str_replace("%r_name_neu%", $neu['r_name'], $lang['raum_gehe2']));
			}
			
			// Topic vorhanden? ausgeben
			if ($lang['raum_gehe6']) {
				$txt = $lang['raum_gehe6'] . " " . $neu['r_name'] . ":";
			} else {
				unset($txt);
			}
			if (strlen($neu['r_topic']) > 0) {
				system_msg("", 0, $u_id, "", "<br><b>$txt</b> $neu[r_topic]");
			}
			
			// Eintrittsnachricht
			if ($lang['raum_gehe3']) {
				$txt = $lang['raum_gehe3'] . " " . $neu['r_name'] . ":";
			} else {
				unset($txt);
			}
			
			if ($raum_eintrittsnachricht_kurzform == "1")
				unset($txt);
			
			if ($raum_eintrittsnachricht_anzeige_deaktivieren == "1") {
			} else if (strlen($neu['r_eintritt']) > 0) {
				system_msg("", 0, $u_id, "", "<br><b>$txt $neu[r_eintritt], $u_nick!</b><br>");
			} else {
				system_msg("", 0, $u_id, "", "<br><b>$txt</b> $lang[betrete_chat2], $u_nick!<br>");
			}
			
			$raum = $raum_neu;
			
		} else {
			// Raum kann nicht betreten werden
			system_msg("", 0, $u_id, "", str_replace("%r_name_neu%", $neu['r_name'], $lang['raum_gehe4']));
			
			// Nachricht das gesperrt ausgeben
			if ($gesperrt) {
				system_msg("", 0, $u_id, "", str_replace("%r_name_neu%", $neu['r_name'], $lang['raum_gehe5']));
			}
			
			// Nachricht das zu wenige Punkte ausgeben
			if ($zuwenigpunkte) {
				if ($u_level == "G") {
					$fehler = str_replace("%r_name_neu%", $neu['r_name'], $lang['raum_gehe8']);
				} else {
					$fehler = str_replace("%r_name_neu%", $neu['r_name'], $lang['raum_gehe7']);
				}
				$fehler = str_replace("%r_min_punkte%", $neu['r_min_punkte'], $fehler);
				system_msg("", 0, $u_id, "", $fehler);
				unset($fehler);
			}
			
			$raum = $raum_alt;
		}
	}
	return ($raum);
}
?>