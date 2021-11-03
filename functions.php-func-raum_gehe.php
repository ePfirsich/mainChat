<?php

function raum_gehe($o_id, $u_id, $u_nick, $raum_alt, $raum_neu, $geschlossen) {
	// user $u_id/$u_nick geht von $raum_alt in Raum $raum_neu
	// falls $geschlossen=TRUE -> auch geschlossene Räume betreten
	// Nachricht in Raum $r_id wird erzeugt
	// ID des neuen Raums wird zurückgeliefert
	
	global $mysqli_link, $chat, $admin, $u_level, $u_punkte_gesamt, $t, $beichtstuhl, $lobby, $timeout;
	global $id, $erweitertefeatures, $forumfeatures, $communityfeatures;
	global $raum_eintrittsnachricht_anzeige_deaktivieren, $raum_austrittsnachricht_anzeige_deaktivieren;
	global $raum_eintrittsnachricht_kurzform, $raum_austrittsnachricht_kurzform;
	
	// Info zu altem Raum lesen
	$query = "SELECT r_name,r_status1,r_austritt,r_min_punkte from raum " . "WHERE r_id=" . intval($raum_alt);
	
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) == 1) {
		$alt = mysqli_fetch_object($result);
		mysqli_free_result($result);
	}
	
	// Ist Benutzer aus dem Raum ausgesperrt?
	$query = "SELECT s_id FROM sperre WHERE s_raum=" . intval($raum_neu) . " AND s_user=$u_id";
	$result = @mysqli_query($mysqli_link, $query);
	$rows = @mysqli_num_rows($result);
	if ($rows == 0) {
		$gesperrt = 0;
	} else {
		$gesperrt = 1;
	}
	mysqli_free_result($result);
	
	// Info zu neuem Raum lesen
	$query = "SELECT * from raum WHERE r_id=" . intval($raum_neu);
	
	$result = mysqli_query($mysqli_link, $query);
	
	if ($result && mysqli_num_rows($result) == 1) {
		$neu = mysqli_fetch_object($result);
		mysqli_free_result($result);
		
		// Online Punkte holen, damit der Benutzer zum Raumwechsel nicht ein/ausloggen muss
		$o_punkte = 0;
		if ($erweitertefeatures == 1) {
			$query2 = "SELECT o_punkte FROM online WHERE o_id=" . intval($o_id);
			$result2 = mysqli_query($mysqli_link, $query2);
			
			if ($result2 && mysqli_num_rows($result2) == 1) {
				$online = mysqli_fetch_object($result2);
				mysqli_free_result($result2);
				$o_punkte = $online->o_punkte;
				unset($online);
			}
			unset($query2);
			unset($result2);
			
		}
		
		// wenn hier nach Erweitertefeatures oder Punkte geprüft werden würde, was Sinn machen würde,
		// kommen Benutzer aus Kostenlosen chats, die mit der mainChat Community verbunden sind, trotzdem in den Raum, 
		// trotz zu wenigen Punkten
		if (($neu->r_name != $lobby)
			&& ($neu->r_min_punkte > ($u_punkte_gesamt + $o_punkte)) && !$admin
			&& $u_level != "A") {
			$zuwenigpunkte = 1;
		} else {
			$zuwenigpunkte = 0;
		}
		
		$raumwechsel = false;
		// Prüfen ob Raum geschlossen oder Admin
		// Prüfen, ob Raumwechsel erlaubt...
		
		// Raumwechsel erlaubt wenn Raum nicht geschlossen und user nicht gesperrt.
		if ($neu->r_status1 == "G" || $neu->r_status1 == "M" || $zuwenigpunkte == 1) {
			// Raum geschlossen. nur rein, wenn auf invite liste.
			$query = "SELECT inv_user FROM invite WHERE inv_raum=$neu->r_id AND inv_user=$u_id";
			$result = mysqli_query($mysqli_link, $query);
			if ($result > 0) {
				if (mysqli_num_rows($result) > 0)
					$raumwechsel = true;
				mysqli_free_result($result);
			}
			// oder falls user=raumbesitzer...
			// macht wenig sinn, das ein RB in seinen Raum ein ! angeben muss
			//if ($neu->r_besitzer==$u_id && $geschlossen) $raumwechsel=true;
			if ($neu->r_besitzer == $u_id)
				$raumwechsel = true;
		} else {
			// Raum offen, nur rein, wenn nicht gesperrt.
			if ($gesperrt == 0 && $zuwenigpunkte == 0)
				$raumwechsel = true;
		}
		
		// raumwechsel nicht erlaubt, wenn alter Raum teergrube (ausser für Admins + Tempadmins)
		if ($alt->r_status1 == "L" && $u_level != "A" && !$admin) {
			$raumwechsel = false;
		}
		
		// für admin raumwechsel erlaubt.
		if ($admin && $geschlossen) {
			$raumwechsel = true;
		}
		
		// Falls Beichtstuhl-Modus und $geschlossen!=TRUE, Anzahl der Benutzer im Raum
		// ermitteln. Der Raum darf betreten werden, wenn:
		// 1) genau ein Admin im Raum ist und
		// 2) kein Benutzer im Raum ist oder
		// 3) Raum temporär ist oder
		// 4) der Raum Lobby ist oder
		// 5) der Benutzer ein Admin ist
		if ($raumwechsel && $beichtstuhl && !$admin) {
			$query = "SELECT r_id,count(o_id) as anzahl, "
				. "count(o_level='C') as CADMIN, count(o_level='S') as SADMIN, "
				. "r_name='$lobby' as LOBBY, r_status2='T' AS STATUS "
				. "FROM raum LEFT JOIN online ON o_raum=r_id "
				. "WHERE ((UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
				. "OR r_name='Lobby' OR r_status2='T') "
				. "AND r_id=" . intval($raum_neu) . " "
				. "GROUP BY r_id HAVING anzahl=1 AND (CADMIN=1 OR SADMIN=1) OR LOBBY OR STATUS";
			// system_msg("",0,$u_id,"","DEBUG $query");
			$result = mysqli_query($mysqli_link, $query);
			
			if ($result && mysqli_num_rows($result) == 1) {
				$raumwechsel = TRUE;
			} else {
				$raumwechsel = FALSE;
			}
			mysqli_free_result($result);
			
		}
		
		// Darf Raum nun betreten werden?
		if ($raumwechsel) {
			
			// Raum verlassen
			$back = nachricht_verlasse($raum_alt, $u_nick, $alt->r_name);
			
			// back in DB merken
			$f['o_chat_id'] = $back;
			schreibe_db("online", $f, $o_id, "o_id");
			
			// Neuen Raum eintragen
			$query = "UPDATE online SET o_raum=" . intval($raum_neu) . " WHERE o_user=$u_id AND o_raum=" . intval($raum_alt);
			$result = mysqli_query($mysqli_link, $query);
			
			// Austrittstext
			if ($t['raum_gehe1']) {
				$txt = $t['raum_gehe1'] . " " . $alt->r_name . ":";
			} else {
				unset($txt);
			}
			
			if ($raum_austrittsnachricht_kurzform == "1")
				unset($txt);
			
			if (strlen($alt->r_austritt) > 0) {
				$txt = "<b>$txt</b> $alt->r_austritt<br>";
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
			system_msg("", 0, $u_id, "",
				$txt . "<br>\n");
			
			// Raum betreten
			nachricht_betrete($u_id, $raum_neu, $u_nick, $neu->r_name);
			
			// Wenn der Neue Raum eine Teergrube ist, dann Eingabzeile aktualisieren, daß der [FORUM] Link verschwindet
			// Es sei denn man ist Admin, dann braucht es nicht aktualisiert werden, denn der Link wird nicht ausgeblendet
			// bzw. wenn alter Raum Teergrube war, dann auch aktualisieren
			// $u_id über Online Tabelle, da der Benutzer auch geschubst werden kann, deswegen dessen o_hash 
			
			if (($forumfeatures) && ($communityfeatures) && (!$beichtstuhl)
				&& (($neu->r_status1 == "L") || ($alt->r_status1 == "L"))
				&& ($u_level != "A") && (!$admin)) {
				$query2 = "SELECT o_hash FROM online WHERE o_id=" . intval($o_id);
				$result2 = mysqli_query($mysqli_link, $query2);
				
				if ($result2 && mysqli_num_rows($result2) == 1) {
					$online = mysqli_fetch_object($result2);
					mysqli_free_result($result2);
					
					system_msg("", 0, $u_id, "",
						"<script>parent.frames[3].location.href='eingabe.php?id=$online->o_hash';</script>");
					
					unset($online);
				}
				unset($query2);
				unset($result2);
				
			}
			
			// Nachricht falls gesperrt ausgeben
			if ($gesperrt || $zuwenigpunkte) :
				system_msg("", 0, $u_id, "",
					str_replace("%r_name_neu%", $neu->r_name, $t['raum_gehe2']));
			endif;
			
			// Topic vorhanden? ausgeben
			if ($t['raum_gehe6']) {
				$txt = $t['raum_gehe6'] . " " . $neu->r_name . ":";
			} else {
				unset($txt);
			}
			if (strlen($neu->r_topic) > 0) {
				system_msg("", 0, $u_id, "", "<br><b>$txt</b> $neu->r_topic");
			}
			
			// Eintrittsnachricht
			if ($t['raum_gehe3']) {
				$txt = $t['raum_gehe3'] . " " . $neu->r_name . ":";
			} else {
				unset($txt);
			}
			
			if ($raum_eintrittsnachricht_kurzform == "1")
				unset($txt);
			
			if ($raum_eintrittsnachricht_anzeige_deaktivieren == "1") {
			} else if (strlen($neu->r_eintritt) > 0) {
				system_msg("", 0, $u_id, "",
					"<br><b>$txt $neu->r_eintritt, $u_nick!</b><br>");
			} else {
				system_msg("", 0, $u_id, "",
					"<br><b>$txt</b> $t[betrete_chat2], $u_nick!</b><br>");
			}
			
			$raum = $raum_neu;
			
		} else {
			// Raum kann nicht betreten werden
			system_msg("", 0, $u_id, "",
				str_replace("%r_name_neu%", $neu->r_name, $t['raum_gehe4']));
			
			// Nachricht das gesperrt ausgeben
			if ($gesperrt) :
				system_msg("", 0, $u_id, "",
					str_replace("%r_name_neu%", $neu->r_name, $t['raum_gehe5']));
			endif;
			
			// Nachricht das zu wenige Punkte ausgeben
			if ($zuwenigpunkte) :
				if ($u_level == "G") {
					$fehler = str_replace("%r_name_neu%", $neu->r_name,
						$t['raum_gehe8']);
				} else {
					$fehler = str_replace("%r_name_neu%", $neu->r_name,
						$t['raum_gehe7']);
				}
				$fehler = str_replace("%r_min_punkte%", $neu->r_min_punkte,
					$fehler);
				system_msg("", 0, $u_id, "", $fehler);
				unset($fehler);
			endif;
			
			$raum = $raum_alt;
		}
	}
	
	return ($raum);
}
?>