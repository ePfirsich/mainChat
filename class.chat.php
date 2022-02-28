<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once("conf/config.php");
require_once("functions/functions.php");
require_once("functions/functions-chat_lese.php");

$u_id = $_SESSION['u_id'];

class chatClass {
	public static function getRestChatLines($last_time_id) {
		$arr = array();
		$jsonData = '{"results":[';
		
		// Voreinstellungen
		$beende_prozess = FALSE;
		set_time_limit($refresh_zeit + 30);
		ignore_user_abort(FALSE);
		
		$u_id = $_SESSION['u_id'];
		$id = $_SESSION['id'];
		$level = "";
		
		// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
		id_lese($id);
		
		// 1 Sek pro Durchlauf fest eingestellt
		$durchlaeufe = $refresh_zeit;
		$zeige_userliste = 100;
		
		$DateAndTime = $_SESSION['DateAndTime'];
		
		$query_room = "SELECT `o_id`, `o_user`, `o_raum` FROM `online` WHERE `o_hash` = '".$id."'";
		$result_room = sqlQuery($query_room);
		$row_room = mysqli_fetch_object($result_room);
		$act_room = $row_room->o_raum;
		
		$query = "SELECT c_id, c_von_user, c_an_user, c_typ, c_raum, c_text, c_zeit, c_farbe, c_von_user_id, c_br, c_gelesen FROM chat WHERE c_id > ".$last_time_id." and c_zeit >= DATE_SUB('".$DateAndTime."', INTERVAL 1 HOUR) and c_an_user = 0 and c_raum = '".$act_room."' or c_id > ".$last_time_id." and c_zeit >= DATE_SUB('".$DateAndTime."', INTERVAL 1 HOUR) and c_an_user = '".$u_id."' and c_raum = '".$act_room."' or c_id > ".$last_time_id." and c_zeit >= DATE_SUB('".$DateAndTime."', INTERVAL 1 HOUR) and c_an_user = 0 and c_raum = '0' or c_id > ".$last_time_id." and c_zeit >= DATE_SUB('".$DateAndTime."', INTERVAL 1 HOUR) and c_an_user = '".$u_id."' and c_raum = '0' ";
		$result = sqlQuery($query);
		$line = new stdClass;
		//echo 'Time: ' . $DateAndTime ;
		//echo "<br>USerID: ".$u_id;
		//echo "<br>UserHash: ".$id;
		
		// Hole alle benötigten Einstellungen des Benutzers
		$benutzerdaten = hole_benutzer_einstellungen($u_id, "chatausgabe");
		
		// Systemnachrichten ausgeben
		$sysmsg = true;
		
		//$ausgabe = chat_lese($row_room->o_id, $row_room->o_raum, $u_id, $sysmsg, $ignore, "1000", $benutzerdaten);
		
		while ($row = mysqli_fetch_object($result)) {
			$query_usr = "SELECT u_id, u_nick, u_level FROM user WHERE u_id = '".$u_id."'";
			$result_usr = sqlQuery($query_usr);
			$row_usr = mysqli_fetch_object($result_usr);
			$usr_name = $row_usr->u_nick;
			$times = date('H:i:s', strtotime($row->c_zeit));
			$ava = "";
			
			
			if( !$row->c_von_user ) {
				$vonuserid = "";
			} else {
				if($benutzerdaten['u_avatare_anzeigen'] == 1) {
					$ava = avatar_anzeigen($row->c_von_user_id, $row->c_von_user, "chat", "m");
				}
				$vonuserid = "" . $ava . " <b>" . $row->c_von_user . "</b>: ";
			}
			
			if( $row->c_typ == 'P' ) {
				$vonuserid = "<span class=\"nachrichten_privat\" title=\"". $times ."\"><b>". $row->c_von_user ."&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg ". $row->c_von_user ." '); return(false)\">privat</a>):</b> ";
			}
			
			if( $row->c_typ == 'H' ) {
				$vonuserid = '';
				
				if( $row_usr->u_level == 'S' || $row_usr->u_level == 'C' || $row_usr->u_level == 'M' ) {
					$level = " <b>(". $row->c_von_user .")</b>";
				}
			}
			
			$c_text =	$row->c_text;
			
			// Smilies ausgeben oder unterdrücken
			if ($benutzerdaten['u_smilies'] == "0") {
				$c_text = str_replace("<smil ", "<small>&lt;SMILIE&gt;</small><!--", $c_text);
				$c_text = str_replace(" smil>", "-->", $c_text);
			} else {
				$c_text = str_replace("<smil ", "<img src='images/smilies/style-$benutzerdaten[u_layout_farbe]/", $c_text);
				$c_text = str_replace(" smil>", "'>", $c_text);
			}
			
			$line->c_id = $row->c_id;
			$line->c_von_user = $row->c_von_user;
			$line->c_farbe = $row->c_farbe;
			$line->c_text = $c_text;
			//$line->c_typ = $row->c_typ;
			//$line->chat_ausgabe = $ausgabe;
			$line->level = $level;
			$line->vonuserid = $vonuserid;
			$line->c_zeit = date('H:i:s', strtotime($row->c_zeit));
			$arr[] = json_encode($line);
		}
		
		// Raum merken
		$o_raum_alt = $o_raum;
		
		// Bin ich noch online?
		//		$result = mysqli_query( $conn, "SELECT HIGH_PRIORITY o_raum,o_ignore FROM online WHERE o_id=" . $row_room->o_id . " ");
		//		if ($result > 0) {
		//				if (mysqli_Num_Rows($result) == 1) {
		//						$row = mysqli_fetch_object($result);
		//						$o_raum = $row->o_raum;
		//						$ignore = unserialize($row->o_ignore);
		//				} else {
		//						// Raus aus dem Chat, vorher kurz warten
		//						sleep(10);
		//						$beende_prozess = TRUE;
		//				}
		//				mysqli_free_result($result);
		//		}
		
		//$j++;
		//$i++;
		
		// Falls nach mehr als 100 sek. keine Ausgabe erfolgt, Userliste anzeigen
		// Nach > 120 Sekunden schlagen bei einigen Browsern Timeouts zu ;)
		if ($i > $zeige_userliste) {
			if (isset($raum_msg) && $raum_msg != "AUS") {
				system_msg("", 0, $u_id, $system_farbe, $raum_msg);
			} else {
				raum_user($o_raum, $u_id);
			}
			$i = 0;
		}
		
		// Raumwechsel?
		if (($trigger_letzte_Zeilen == 0) && ($o_raum != $o_raum_alt)) {
			// Trigger für die letzten Nachrichten setzen
			$trigger_letzte_Zeilen = 1;
		}
		
		$jsonData .= implode(",", $arr);
		$jsonData .= ']}';
		return $jsonData;
	}
}
?>