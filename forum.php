<?php

include("functions.php");

// Userdaten setzen
id_lese($id);

//gültiger User?
if (strlen($u_id) > 0) {
	
	aktualisiere_online($u_id, $o_raum);
	
	// In Session merken, dass Text im Chat geschrieben wurde
	$query = "UPDATE online SET o_timeout_zeit=DATE_FORMAT(NOW(),\"%Y%m%d%H%i%s\"), o_timeout_warnung='N' " . "WHERE o_user=" . intval($u_id);
	$result = mysqli_query($mysqli_link, $query);
	
	//gelesene Beiträge lesen
	lese_gelesene_postings($u_id);
	
	//Admin fuer Forum darf kein Temp-Admin sein
	if ($u_level == "S" || $u_level == "C") {
		$forum_admin = TRUE;
	} else {
		$forum_admin = FALSE;
	}
	
	kopf_forum($forum_admin);
	
	switch ($aktion) {
		
		//Aktionen, die das Forum betreffen
		case "forum_neu":
			maske_forum();
			break;
		case "forum_anlegen":
			$missing = check_input("forum");
			if (!$missing) {
				schreibe_forum();
				forum_liste();
			} else {
				show_missing($missing);
				maske_forum();
			}
			break;
		case "forum_editieren":
			$missing = check_input("forum");
			if (!$missing) {
				aendere_forum();
				forum_liste();
			} else {
				show_missing($missing);
				maske_forum($fo_id);
			}
			break;
		case "forum_up":
			forum_up($fo_id, $fo_order);
			forum_liste();
			break;
		case "forum_down":
			forum_down($fo_id, $fo_order);
			forum_liste();
			break;
		case "forum_edit":
			maske_forum($fo_id);
			break;
		case "forum_delete":
			if ($forum_admin) {
				loesche_forum($fo_id);
			}
			forum_liste();
			break;
		
		//Aktionen, die ein Thema betreffen
		case "thema_neu":
			if ($u_level != "G") {
				maske_thema();
			} else {
				echo "<P>" . $t[forum_gast] . "</P>";
				forum_liste();
			}
			break;
		case "thema_anlegen":
			$missing = check_input("thema");
			if (!$missing) {
				schreibe_thema();
				forum_liste();
			} else {
				show_missing($missing);
				maske_thema();
			}
			break;
		case "thema_edit":
			maske_thema($th_id);
			break;
		case "thema_editieren":
			$missing = check_input("thema");
			if (!$missing) {
				schreibe_thema($th_id);
				forum_liste();
			} else {
				show_missing($missing);
				maske_thema($th_id);
			}
			break;
		case "thema_up":
			thema_up($th_id, $th_order, $fo_id);
			forum_liste();
			break;
		case "thema_down":
			thema_down($th_id, $th_order, $fo_id);
			forum_liste();
			break;
		case "show_thema":
			show_thema();
			break;
		case "thema_delete":
			if ($forum_admin) {
				loesche_thema($th_id);
			}
			forum_liste();
			break;
		
		case "thema_alles_gelesen":
			thema_alles_gelesen($th_id, $u_id);
			forum_liste();
			break;
		
		//Aktionen, die einen Beitrag betreffen
		case "thread_neu":
			$schreibrechte = pruefe_schreibrechte($th_id);
			
			if ($schreibrechte)
				maske_posting("neuer_thread");
			else {
				print $t[schreibrechte];
				forum_liste();
			}
			break;
		
		case "thread_alles_gelesen":
			thread_alles_gelesen($th_id, $thread, $u_id);
			show_thema();
			break;
		
		case "sperre_posting":
			if ($forum_admin) {
				sperre_posting($po_id);
				show_posting();
			}
			break;
		
		case "posting_anlegen":
			$thread_gesperrt = false;
			if (isset($thread) && ($thread > 0) && !$forum_admin) {
				$thread_gesperrt = ist_thread_gesperrt($thread);
			}
			$schreibrechte = pruefe_schreibrechte($th_id);
			
			if ($schreibrechte && !$thread_gesperrt) {
				$missing = check_input("posting");
				if (!$missing) {
					$new_po_id = schreibe_posting();
					if ($new_po_id) {
						markiere_als_gelesen($new_po_id, $u_id, $th_id);
						aktion_sofort($new_po_id, $po_vater_id, $thread);
						$po_id = $new_po_id;
						// Punkte gutschreiben  
						if ($u_id)
							verbuche_punkte($u_id);
						
					}
					//show_thema($th_id);
					show_posting();
				} else {
					show_missing($missing);
					maske_posting($mode);
				}
			} else {
				echo $t[schreibrechte];
				forum_liste();
			}
			break;
		case "show_posting":
		//			Falsch, die Leserechte anhand der $th_id die übergeben wurde zu überprüfen
		//			$leserechte=pruefe_leserechte($th_id);
		
		// 			Richtig, die $th_id anhand der $po_id zu bestimmen und zu prüfen
			$leserechte = pruefe_leserechte(
				hole_themen_id_anhand_posting_id($po_id));
			if ($leserechte) {
				show_posting();
				markiere_als_gelesen($po_id, $u_id, $th_id);
			} else {
				print $t[leserechte];
			}
			break;
		case "reply":
			$schreibrechte = pruefe_schreibrechte($th_id);
			if ($schreibrechte) {
				maske_posting("reply");
			} else {
				echo $t[schreibrechte];
				forum_liste();
			}
			break;
		case "answer":
			$schreibrechte = pruefe_schreibrechte($th_id);
			if ($schreibrechte) {
				maske_posting("answer");
			} else {
				echo $t[schreibrechte];
				forum_liste();
			}
			break;
		case "edit":
			maske_posting("edit");
			break;
		case "delete_posting":
			if ($forum_admin) {
				loesche_posting();
			}
			show_thema();
			break;
		case "verschiebe_posting":
			if ($forum_admin) {
				verschiebe_posting();
			}
			break;
		
		case "verschiebe_posting_ausfuehren":
			if ($forum_admin && $verschiebe_von <> $verschiebe_nach) {
				verschiebe_posting_ausfuehren();
			}
			$th_id = $verschiebe_von;
			show_thema();
			break;
		
		//Default
		default:
			forum_liste();
			break;
	}
	fuss_forum();
	
} else {
	// User wird nicht gefunden. Login ausgeben
	
	$title = $body_titel;
	zeige_header_anfang($title, $farbe_background, $grafik_background, $farbe_link, $farbe_vlink);
	
	zeige_header_ende();
	?>
	<body onLoad='javascript:parent.location.href="index.php?http_host=<?php echo $http_host; ?>'>
	</body>
	</html>
	<?php
	exit;
}
?>