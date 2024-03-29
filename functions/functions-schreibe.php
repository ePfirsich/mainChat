<?php
require_once("./functions/functions-raum_gehe.php");
require_once("./functions/functions-msg.php");

function schreibe_nachricht_chat($text, $privat, $o_id, $benutzerdaten) {
	global $u_id, $u_nick, $lang, $o_raum, $admin, $u_level, $o_spam_zeilen, $o_spam_byte, $u_farbe, $user_farbe, $o_who, $chat_max_zeilen, $chat_max_byte, $o_spam_zeit, $chat_max_zeit, $punktefeatures;
	
	// $raum_einstellungen und $ist_moderiert setzen
	raum_ist_moderiert($o_raum);
	
	// Falls private Nachricht, Benutzernamen ergänzen
	if (isset($privat) && strlen($privat) > 2) {
		$text = "/msgpriv $privat " . $text;
	}
	// Initialisierung der Fehlerbehandlung
	$fehler = false;
	
	// Falls $text eine Eingabezeile enthält -> verarbeiten
	if ( isset($text) && strlen($text) != 0 && strlen($text) <= 1000 ) {
		// Spamschutz prüfen, falls kein Admin und kein Moderator
		if (!$admin && ($u_level <> "M")) {
			
			// Geschriebene Zeilen und Bytes aus DB lesen
			$spam_zeilen = unserialize($o_spam_zeilen);
			$spam_byte = unserialize($o_spam_byte);
			
			// Zeitpunkt sekundengenau bestimmen => Jeder Eintrag im Array entspricht einer Sekunde
			// mktime ( int Stunde, int Minute, int Sekunde, int Monat, int Tag, int Jahr
			$temp = getdate();
			$aktuelle_zeit = mktime($temp["hours"], $temp["minutes"], $temp["seconds"], $temp["mon"], $temp["mday"], $temp["year"]);
			
			// Array-Keys auf die richtige Zeit umrechnen
			// Alle Einträge im Array löschen, die älter als $chat_max_zeit Sekunden sind
			unset($neu_spam_zeilen);
			unset($neu_spam_byte);
			if (is_array($spam_zeilen) && $o_spam_zeit) {
				$zeitdifferenz = $aktuelle_zeit - $o_spam_zeit;
				foreach ($spam_zeilen as $key => $value) {
					if ((($key - $zeitdifferenz) * -1) < $chat_max_zeit) {
						$neu_spam_zeilen[$key - $zeitdifferenz] = $spam_zeilen[$key];
						$neu_spam_byte[$key - $zeitdifferenz] = $spam_byte[$key];
					}
				}
			} else {
				$o_spam_zeit = $aktuelle_zeit;
			}
			
			// Aktuelle Eingabe im Array summieren
			if (!isset($neu_spam_zeilen)) {
				$neu_spam_zeilen = array();
			}
			if (!isset($neu_spam_zeilen[0])) {
				$neu_spam_zeilen[0] = 0;
			}
			$neu_spam_zeilen[0] += 1;
			if (!isset($neu_spam_byte)) {
				$neu_spam_byte = array();
			}
			if (!isset($neu_spam_byte[0])) {
				$neu_spam_byte[0] = 0;
			}
			$neu_spam_byte[0] += strlen($text);
			
			unset($f);
			$f['o_spam_zeit'] = $aktuelle_zeit;
			$f['o_spam_zeilen'] = serialize($neu_spam_zeilen);
			$f['o_spam_byte'] = serialize($neu_spam_byte);
			schreibe_online($f, "schreiben", $u_id);
			
			// Prüfen wieviel Byte in wieviel Zeilen in den letzten $chat_max_zeit Sekunden geschrieben wurde
			if ((array_sum($neu_spam_zeilen) > $chat_max_zeilen) || (array_sum($neu_spam_byte) > $chat_max_byte)) {
				$fehler = true;
			}
		}
	} else if (isset($text) && strlen($text) > 1000) {
		$fehler = true;
	}
	
	// Falls kein Spam, Nachricht ausgeben
	if (!$fehler) {
		if (strlen($u_farbe) == 0) {
			$u_farbe = $user_farbe;
		}
		
		chat_msg($o_id, $u_id, $u_nick, $u_farbe, $admin, $o_raum, isset($text) ? $text : "", "");
	} else if ($fehler) { // Spam -> Fehler ausgeben
		// Systemnachricht mit Fehlermeldung an Benutzer schreiben
		system_msg("", 0, $u_id, $system_farbe, $lang['floodsperre1'] . " " . $text);
		
		// Zur Strafe 10 Punkte abziehen
		if ($punktefeatures) {
			$anzahl = 10;
			punkte((-1) * $anzahl, $o_id, $u_id, $lang['floodsperre2'], TRUE);
		}
	}
	
	// falls Moderator, moderationsfenster nach Eingabe neu laden, falls Benutzer im Chat
	if ($o_who != 2 && $u_level == "M") {
		reset_system("moderator");
	}
}
?>