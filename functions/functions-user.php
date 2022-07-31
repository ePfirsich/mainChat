<?php

// functions nur für user.php

require_once("functions/functions-raeume_auswahl.php");

function user_pm_list($larr) {
	// Gibt Benutzerliste $larr als Tabelle aus
	global $admin, $u_level, $adminfeatures, $u_id, $show_geschlecht;
	global $punkte_grafik, $leveltext;
	
	
	$text = '';
	
	//Wichtiger SQL Teil
	//SQL Teil für die richtige Berechnung der einzelnen PM Nachrichten
	$query = pdoQuery("SELECT DISTINCT `c_an_user` FROM `chat` WHERE `c_typ` = 'P' AND `c_von_user_id` = :c_von_user_id", [':c_von_user_id'=>$u_id]);
	
	$resultCount = $query->rowCount();
	$result = $query->fetchAll();
	
	flush();
	// Anzeige der Benutzer ohne JavaScript
	if($resultCount != 0) {
		$trow = "";
		foreach($result as $zaehler => $row) {
			$pmuea2 = $row['c_an_user'];
			
			//SQL Teil für die Anzeige der nicht gelesenen Nachrichten
			$query2 = pdoQuery("SELECT `c_an_user` FROM `chat` WHERE `c_gelesen` = 0 AND `c_typ` = 'P' AND `c_von_user_id` = :c_von_user_id AND `c_an_user` = :c_an_user", [':c_von_user_id'=>$pmuea2, ':c_an_user'=>$u_id]);
			
			$result2Count = $query2->rowCount();
			if ( $zaehler % 2 != 0 ) {
				$farbe_tabelle = 'class="tabelle_zeile1"';
			} else {
				$farbe_tabelle = 'class="tabelle_zeile2"';
			}
			
			if($result2Count > 0) {
				$pmue33t = " <b>(".$result2Count.")</b>";
			} else {
				$pmue33t = "";
			}
			
			$user = zeige_userdetails($pmuea2) . "" . $pmue33t;
			
			$trow .= "<tr>";
			$trow .= "<td $farbe_tabelle><span class=\"smaller\">" . $user . "</td></tr>";
		}
	}
	
	$query3 = pdoQuery("SELECT DISTINCT `c_von_user_id` FROM `chat` WHERE `c_typ` = 'P' AND `c_an_user` = :c_an_user", [':c_an_user'=>$u_id]);
	
	$result3Count = $query3->rowCount();
	if($result3Count != 0) {
		$result3 = $query3->fetchAll();
		$pxx = false;
		
		$trow = "";
		foreach($result3 as $zaehler3 => $row3) {
			$pmuea = $row3['c_von_user_id'];
			
			$query4 = pdoQuery("SELECT `c_an_user` FROM `chat` WHERE `c_gelesen` = 0 AND `c_typ` = 'P' AND `c_von_user_id` = :c_von_user_id", [':c_von_user_id'=>$pmuea]);
			
			$result4Count = $query4->rowCount();
			$result4 = $query4->fetchAll();
			foreach($result4 as $zaehler4 => $row4) {
				$pmuea2 = $row['c_an_user'];
				$pmuea3 = $row4['c_an_user'];
				
				if($pmuea == $pmuea2) {
					$pxx = true;
				}
			}
			
			if(!$pxx) {
				if ( $zaehler3 % 2 != 0 ) {
					$farbe_tabelle = 'class="tabelle_zeile1"';
				} else {
					$farbe_tabelle = 'class="tabelle_zeile2"';
				}
				
				if($result4Count > 0) {
					$pmue44t = " <b>(".$result4Count.")</b>";
				} else {
					$pmue44t = "";
				}
				
				$user = zeige_userdetails($pmuea) . "" . $pmue44t;
				
				$trow .= "<tr>";
				$trow .= "<td $farbe_tabelle><span class=\"smaller\">" . $user . "</td></tr>";
			}
		}
	}
	
	$text .= "<table style=\"width:100%;\">$trow</table>\n";
	
	return $text;
}

function user_liste($larr, $seitenleiste = false) {
	// Gibt Benutzerliste $larr als Tabelle aus
	global $admin, $u_level, $u_id, $show_geschlecht, $punkte_grafik, $leveltext;
	
	$text = '';
	
	if (!isset($larr[0]['r_name'])) {
		$larr[0]['r_name'] = "";
	}
	if (!isset($larr[0]['r_besitzer'])) {
		$larr[0]['r_besitzer'] = "";
	}
	if (!isset($larr[0]['r_topic'])) {
		$larr[0]['r_topic'] = "";
	}
	
	$r_name = $larr[0]['r_name'];
	$r_besitzer = $larr[0]['r_besitzer'];
	$r_topic = $larr[0]['r_topic'];
	$level = "user";
	$level2 = "user";
	if ($r_besitzer == $u_id) {
		$level = "owner";
	}
	if ($admin || $u_level == "A") {
		$level = "admin";
	}
	if ($u_level == "C" || $u_level == "S") {
		$level2 = "admin";
	}
	flush();
	
	// Anzeige der Benutzer
	$trow = "";
	foreach($larr as $k => $v) {
		if ( $k % 2 != 0 ) {
			$farbe_tabelle = 'class="tabelle_zeile1"';
		} else {
			$farbe_tabelle = 'class="tabelle_zeile2"';
		}
		
		if ($v['u_away']) {
			$user = "(" . zeige_userdetails($v['u_id'], FALSE, FALSE, FALSE) . ")";
		} else {
			$user = zeige_userdetails($v['u_id']);
		}
		
		$trow .= "<tr>";
		$trow .= "<td $farbe_tabelle><span class=\"smaller\"><b>";
		
		if ($seitenleiste) {
			if ($level == "admin") {
				$trow .= "<a href=\"schreibe.php?text=/gag%20$v[u_nick]\" class=\"schreibe-chat\">G</a>&nbsp;";
			}
			
			if ($level == "admin" || $level == "owner") {
				$trow .= "<a href=\"schreibe.php?text=/kick%20$v[u_nick]\" class=\"schreibe-chat\">K</a>&nbsp;";
			}
			
			if ($level2 == "admin") {
				$o_ip = $larr[0]['o_ip'];
				$host_name = htmlspecialchars(gethostbyaddr($o_ip));
				$trow .= "<a href=\"inhalt.php?bereich=sperren&aktion=neu&hname=$host_name&ipaddr=$o_ip&uname=$v[u_nick]\" target=\"chat\">S</a>&nbsp;";
			}
			
			if ($level == "admin" || $level == "owner") {
				$trow .= "<span id=\"out\"></span>\n";
				$trow .= "<script>
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
			
			
			
			if ($level == "admin" || $level == "owner") {
				$trow .= "&nbsp;";
			}
			
			$trow .= "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat(' @" . $v['u_nick'] . " '); return(false)\">@</a>&nbsp;";
			$trow .= "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg " . $v['u_nick'] . " '); return(false)\">&gt;</a>&nbsp;";
		} else {
			if ($level == "admin" || $level == "owner") {
				$trow .= "<a href=\"schreibe.php?text=/einlad%20$v[u_nick]\" class=\"schreibe-chat\">E</a>&nbsp;";
			}
			if ($level == "admin" || $level == "owner") {
				$trow .= "&nbsp;";
			}
		}
		
		$trow .= "</b>" . $user . "</td></tr>";
	}
	
	$text .= "<table style=\"width:100%;\">$trow</table>\n";
	
	return $text;
}
?>