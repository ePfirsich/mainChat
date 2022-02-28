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
	$query = "SELECT DISTINCT c_an_user FROM chat WHERE c_typ='P' AND c_von_user_id=".$u_id;
	$pmu2 = sqlQuery($query);
	$pmue22 = mysqli_num_rows($pmu2);
	$pmue2 = mysqli_fetch_all($pmu2);
	
	$query = "SELECT DISTINCT c_von_user_id FROM chat WHERE c_typ='P' AND c_an_user=".$u_id;
	$pmu = sqlQuery($query);
	$pmuee = mysqli_num_rows($pmu);
	$pmue = mysqli_fetch_all($pmu);
	
	flush();
	$v = $larr[$k];
	// Anzeige der Benutzer ohne JavaScript
	if($pmue22 != 0) {
		for ($k = 0; $k < $pmue22; $k++) {
			$pmuea2 = $pmue2[$k][0];
			
			//SQL Teil für die Anzeige der nicht gelesenen Nachrichten.
			$query = "SELECT c_an_user FROM chat WHERE c_gelesen=0 AND c_typ='P' AND c_von_user_id=".$pmuea2." AND c_an_user=".$u_id;
			$pmu3 = sqlQuery($query);
			$pmue33 = mysqli_num_rows($pmu3);
			
			if ( $k % 2 != 0 ) {
				$farbe_tabelle = 'class="tabelle_zeile1"';
			} else {
				$farbe_tabelle = 'class="tabelle_zeile2"';
			}
			
			if($pmue33 > 0) {
				$pmue33t = " <b>(".$pmue33.")</b>";
			} else {
				$pmue33t = "";
			}
			
			$user = zeige_userdetails($pmuea2, $v) . "" . $pmue33t;
			
			$trow .= "<tr>";
			$trow .= "<td $farbe_tabelle><span class=\"smaller\">" . $user . "</td></tr>";
		}
	}
	
	if($pmuee !=0) {
		$pxx = false;
		
		for ($k = 0; $k < $pmuee; $k++) {
			$pmuea = $pmue[$k][0];
			
			$query = "SELECT c_an_user FROM chat WHERE c_gelesen=0 AND c_typ='P' AND c_von_user_id=".$pmuea;
			$pmu4 = sqlQuery($query);
			$pmue44 = mysqli_num_rows($pmu4);
			$pmue4 = mysqli_fetch_all($pmu4);
			
			for ($k = 0; $k < $pmue22; $k++) {
				$pmuea2 = $pmue2[$k][0];
				$pmuea3 = $pmue4[$k][0];
				
				if($pmuea == $pmuea2) {
					$pxx = true;
					//$pmue44 = "0";
				}
			}
			
			if(!$pxx) {
				if ( $k % 2 != 0 ) {
					$farbe_tabelle = 'class="tabelle_zeile1"';
				} else {
					$farbe_tabelle = 'class="tabelle_zeile2"';
				}
				
				if($pmue44 > 0) {
					$pmue44t = " <b>(".$pmue44.")</b>";
				} else {
					$pmue44t = "";
				}
				
				$user = zeige_userdetails($pmuea, $v) . "" . $pmue44t;
				
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
	for ($k = 0; is_array($larr[$k]) && $v = $larr[$k]; $k++) {
		if ( $k % 2 != 0 ) {
			$farbe_tabelle = 'class="tabelle_zeile1"';
		} else {
			$farbe_tabelle = 'class="tabelle_zeile2"';
		}
		
		if ($v['u_away']) {
			$user = "(" . zeige_userdetails($v['u_id'], $v, FALSE, "&nbsp;", "", "", FALSE, FALSE) . ")";
		} else {
			$user = zeige_userdetails($v['u_id'], $v);
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
				//$trow .= "<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"einladung('" . $v['u_nick'] . "'); return(false)\">E</a>&nbsp;";
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