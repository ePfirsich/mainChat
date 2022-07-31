<?php
session_start();
require_once("conf/config.php");
require_once("functions/functions.php");
require_once("functions/functions-chat_lese.php");

$u_id = $_SESSION['u_id'];

class chatClass {
	public static function getRestChatLines($last_time_id) {
		global $chat_status_klein;
		$arr = array();
		$jsonData = '{"results":[';
		$refresh_zeit = 0;
	//	$query = "";
		$o_raum = "";
		
		
		// Voreinstellungen
		$beende_prozess = FALSE;
		set_time_limit($refresh_zeit + 30);
		ignore_user_abort(FALSE);
		
		$u_id = $_SESSION['u_id'];
		$id = $_SESSION['id'];
		$level = "";
		
		// Vergleicht Hash-Wert mit IP und liefert u_id, o_id, o_raum, admin
		id_lese();
		
		// 1 Sek pro Durchlauf fest eingestellt
		$durchlaeufe = $refresh_zeit;
		$zeige_userliste = 100;
		
		$DateAndTime = $_SESSION['DateAndTime'];
		
		$query_room = pdoQuery("SELECT `o_id`, `o_user`, `o_raum` FROM `online` WHERE `o_hash` = :o_hash", [':o_hash'=>$id]);
		$row_room = $query_room->fetch();
		$act_room = $row_room['o_raum'];
		
		$query = pdoQuery("SELECT `c_id`, `c_von_user`, `c_an_user`, `c_typ`, `c_raum`, `c_text`, `c_zeit`, `c_farbe`, `c_von_user_id`, `c_gelesen` FROM `chat`
				WHERE `c_id` > :c_id1 AND `c_zeit` >= DATE_SUB(:c_zeit1, INTERVAL 0 HOUR)
				AND `c_an_user` = 0 AND `c_raum` = :c_raum1 OR `c_id` > :c_id2 AND `c_zeit` >= DATE_SUB(:c_zeit2, INTERVAL 0 HOUR)
				AND `c_an_user` = :c_an_user1 AND `c_raum` = :c_raum2 OR `c_id` > :c_id3 AND `c_zeit` >= DATE_SUB(:c_zeit3, INTERVAL 0 HOUR)
				AND `c_an_user` = 0 AND `c_raum` = '0' OR `c_id` > :c_id4 AND `c_zeit` >= DATE_SUB(:c_zeit4, INTERVAL 0 HOUR) AND `c_an_user` = :c_an_user2 AND `c_raum` = '0'",
			[
				':c_id1'=>$last_time_id,
				':c_zeit1'=>$DateAndTime,
				':c_raum1'=>$act_room,
				':c_id2'=>$last_time_id,
				':c_zeit2'=>$DateAndTime,
				':c_an_user1'=>$u_id,
				':c_raum2'=>$act_room,
				':c_id3'=>$last_time_id,
				':c_zeit3'=>$DateAndTime,
				':c_id4'=>$last_time_id,
				':c_zeit4'=>$DateAndTime,
				':c_an_user2'=>$u_id
			]);
		
		$line = new stdClass;
		
		// Hole alle benötigten Einstellungen des Benutzers
		$benutzerdaten = hole_benutzer_einstellungen($u_id, "chatausgabe");
		
		// Systemnachrichten ausgeben
		$sysmsg = true;
		
		$result = $query->fetchAll();
		foreach($result as $zaehler => $row) {
			$query_usr = pdoQuery("SELECT `u_id`, `u_nick`, `u_level` FROM `user` WHERE `u_id` = :u_id", [':u_id'=>$u_id]);
			
			$row_usr = $query_usr->fetch();
			$usr_name = $row_usr['u_nick'];
			$times = date('H:i:s', strtotime($row['c_zeit']));
			$ava = "";
			$level = "";
			
			if( !$row['c_von_user'] ) {
				$vonuserid = "";
			} else {
				if($benutzerdaten['u_avatare_anzeigen'] == 1) {
					$query2 = pdoQuery("SELECT `ui_geschlecht` FROM `userinfo` WHERE `ui_userid` = :ui_userid", [':ui_userid'=>$row['c_von_user_id']]);
					$result2Count = $query2->rowCount();
					if ($result2Count == 1) {
						$result2 = $query2->fetch();
						$ui_gen = $result2['ui_geschlecht'];
					} else {
						$ui_gen = '0';
					}
					$ava = avatar_anzeigen($row['c_von_user_id'], $row['c_von_user'], "chat", $ui_gen);
				}
				$vonuserid = "" . $ava . " <b>" . $row['c_von_user'] . "</b>: ";
			}
			
			// Private Nachrichten
			if( $row['c_typ'] == 'P' ) {
				if ($benutzerdaten['u_layout_chat_darstellung'] == '0') {
					$vonuserid = "<span class=\"nachrichten_privat\" title=\"". $times ."\"><b>". $row['c_von_user'] ."&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg ". $row['c_von_user'] ." '); return(false)\">privat</a>):</b> ";
				} else {
					$vonuserid = "<span style=\"". $row['c_farbe'] ."\" title=\"". $times ."\"><b>". $row['c_von_user'] ."&nbsp;(<a href=\"#\" onMouseOver=\"return(true)\" onClick=\"appendtext_chat('/msg ". $row['c_von_user'] ." '); return(false)\">privat</a>):</b> ";
				}
			}
			
			// Sprüche und /me
			if( $row['c_typ'] == 'H' ) {
				$vonuserid = '';
				$row['c_text'] = "<i>" . $row['c_text'] . "</i>";
				
				if( $row_usr['u_level'] == 'S' || $row_usr['u_level'] == 'C' || $row_usr['u_level'] == 'M' ) {
					$level = " <b>(". $row['c_von_user'] .")</b>";
				}
			}
			
			// Status-Nachrichten
			if( $row['c_typ'] == 'S' ) {
				if ($chat_status_klein) {
					$row['c_text'] = "<small>" . $row['c_text'] . "</small>";
				}
				//$row['c_text'] = "<span class=\"fa-solid fa-circle-info icon16\"></span> <span>" . $row['c_text'];
			}
			
			$c_text = $row['c_text'];
			
			// Smilies ausgeben oder unterdrücken
			if ($benutzerdaten['u_smilies'] == "0") {
				$c_text = str_replace("<smil ", "<small><SMILIE></small><!--", $c_text);
				$c_text = str_replace(" smil>", "-->", $c_text);
			} else {
				$c_text = str_replace("<smil ", "<img src='images/smilies/style-$benutzerdaten[u_layout_farbe]/", $c_text);
				$c_text = str_replace(" smil>", "'>", $c_text);
			}
			
			// Leerzeichen Prüfung
			if(!ctype_space($c_text))
			{
			$line->c_id = $row['c_id'];
			$line->c_von_user = $row['c_von_user'];
			$line->c_farbe = $row['c_farbe'];
			$line->c_text = $c_text;
			//$line->c_typ = $row['c_typ'];
			//$line->chat_ausgabe = $ausgabe;
			$line->level = $level;
			$line->vonuserid = $vonuserid;
			$line->c_zeit = date('H:i:s', strtotime($row['c_zeit']));
			$arr[] = json_encode($line);
			}

		}
		
		// Raum merken
		$o_raum_alt = $o_raum;
		
		/*
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
		*/
		
		/*
		// Raumwechsel?
		if (($trigger_letzte_Zeilen == 0) && ($o_raum != $o_raum_alt)) {
			// Trigger für die letzten Nachrichten setzen
			$trigger_letzte_Zeilen = 1;
		}
		*/
		
		$jsonData .= implode(",", $arr);
		$jsonData .= ']}';
		
		return $jsonData;
	}
}
?>