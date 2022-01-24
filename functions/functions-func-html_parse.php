<?php

// function html_parse wird von user und interaktiv benötigt.
function html_parse($privat, $text, $at_sonderbehandlung = 0) {
	// Filtert Text, ersetzt Smilies und ersetzt folgende Zeichen:
	// _ in <b>
	// * in <I>
	// http(s)://###### oder www.###### in <a href="http(s)://###" target=_blank>http(s)://###</A>
	// E-Mail Adressen in A-Tag mit Mailto
	// privat ist wahr bei privater Nachricht
	
	global $admin, $sprache, $o_raum, $u_id, $u_level;
	global $t, $system_farbe, $smilies_anzahl, $ist_moderiert;
	
	$text_an_user = "";
	
	// Grafik-Smilies ergänzen, falls Funktion aktiv und Raum ist nicht moderiert
	// Für Gäste gesperrt
	// $ist_moderiert ist in raum_ist_moderiert() (Caching!) gesetzt worden!
	
	if (!$ist_moderiert) {
		preg_match_all("/(&amp;[^ ]+)/", $text, $test, PREG_PATTERN_ORDER);
		$anzahl = count($test[0]);
		if ($anzahl > 0) {
			if ($anzahl > $smilies_anzahl) {
				// Fehlermeldung ausgeben
				system_msg("", 0, $u_id, $system_farbe, $t[chat_msg54]);
			}
			
			// Prüfen, ob im aktuellen Raum Smilies erlaubt sind
			if (!$privat) {
				$query = "SELECT r_smilie FROM raum WHERE r_id=" . intval($o_raum);
				$result = sqlQuery($query);
				if ($result && mysqli_num_rows($result) > 0 && mysqli_result($result, 0, 0) == 0) {
					$smilie_ok = FALSE;
				} else {
					$smilie_ok = TRUE;
				}
			} else {
				$smilie_ok = TRUE;
			}
			
			// Konfiguration für Smilies lesen
			require_once("languages/$sprache-smilies.php");
			
			if (!$smilie_ok) {
				// Nur die Fehlermeldung ausgeben, falls es das angegeben Smile auch gibt
				$anzahl = 0;
				while (list($i, $smilie_code) = each($test[0])) {
					$smilie_code = str_replace("&amp;", "&", $smilie_code);
					if ($smilie[$smilie_code]) {
						$anzahl++;
					}
				}
				
				if ($anzahl > 0) {
					system_msg("", 0, $u_id, $system_farbe, $t[chat_msg76]);
				}
			} else {
				while (list($i, $smilie_code) = each($test[0])) {
					if ($anzahl > $smilies_anzahl) {
						// Mehr als $smilies_anzahl Smilies sind nicht erlaubt
						$text = str_replace(" " . $smilie_code . " ", "", $text);
					} else {
						// Falls smilie existiert, ersetzen
						$smilie_code2 = str_replace("&amp;", "&", $smilie_code);
						if (isset($smilie[$smilie_code2])) {
							$text = str_replace($smilie_code, "<smil " . $smilie[$smilie_code2] . " smil>", $text);
						}
					}
				}
			}
		}
	}
	
	// doppelte Zeichen merken und wegspeichern...
	$text = str_replace("__", "###substr###", $text);
	$text = str_replace("**", "###stern###", $text);
	$text = str_replace("@@", "###klaffe###", $text);
	$text = str_replace("[", "###auf###", $text);
	$text = str_replace("]", "###zu###", $text);
	$text = str_replace("|", "###strich###", $text);
	$text = str_replace("+", "###plus###", $text);
	
	// Jetzt nach nach italic und bold parsen...  * in <I>, $_ in <b>
	if (substr_count($text, "http://") == 0 && substr_count($text, "https://") == 0 && substr_count($text, "www.") == 0
		&& !preg_match("(\w[-._\w]*@\w[-._\w]*\w\.\w{2,3})", $text)) {
		$text = preg_replace('|\*(.*?)\*|', '<i>\1</i>',
			preg_replace('|_(.*?)_|', '<b>\1</b>', $text));
	}
	
	// Erst mal testen ob www oder http oder email vorkommen
	if (preg_match("/(https?:|www\.|@)/i", $text)) {
		// Zerlegen der Zeile in einzelne Bruchstücke. Trennzeichen siehe $split
		// leider müssen zunächst erstmal in $text die gefundenen urls durch dummies 
		// ersetzt werden, damit bei der Angabge von 2 gleichen urls nicht eine bereits 
		// ersetzte url nochmal ersetzt wird -> gibt sonst Müll bei der Ausgabe.
		
		// wird evtl. später nochmal gebraucht...
		$split = '/[ ,\[\]\(\)]/';
		$txt = preg_split($split, $text);
		
		// Wie viele Worte hat die Zeile?
		for ($i = 500; $i >= 0; $i--) {
			if ((isset($txt[$i])) && $txt[$i] != "") {
				break;
			}
		}
		
		// Schleife über alle Worte...
		for ($j = 0; $j <= $i; $j++) {
			// test, ob am Ende der URL noch ein Sonderzeichen steht...
			$txt[$j] = preg_replace("!\?$!", "", $txt[$j]);
			$txt[$j] = preg_replace("!\.$!", "", $txt[$j]);
		}
		for ($j = 0; $j <= $i; $j++) {
			// erst mal nick_replace, falls Wort mit "@" beginnt.
			if (substr($txt[$j], 0, 1) == "@") {
				$nick = nick_ergaenze($txt[$j], "raum", 1); // fehlermeldungen unterdrücken.
				if (($admin || $u_level == "A") && $nick['u_nick'] == "") {
					$nick = nick_ergaenze($txt[$j], "online", 1);
				}
				// Fehlermeldungen unterdrücken.
				if ($nick['u_nick'] != "") {
					if ($at_sonderbehandlung == 1) {
					// in /me sprüchen kein [zu Nick] an den anfang stellen, sondern nur nick ergänzen
						$rep = $nick['u_nick'];
						$text = preg_replace("!$txt[$j]!", $rep, $text);
					} else {
						$text_an_user = $nick['u_id'];
						$rep = "[" . $t['chat_spruch6'] . "&nbsp;" . $nick['u_nick'] . "] ";
						$text = $rep . preg_replace("!$txt[$j]!", "", $text);
					}
				}
				
				if ($u_level == "G") {
					break;
				}
			}
			
			if ($u_level != "G") {
				// E-Mail Adressen in A-Tag mit Mailto
				// E-Mail-Adresse -> Format = *@*.*
				if (preg_match( '/^[\w][\w.-]*@[[:alnum:].-]+\.[[:alnum:]-]{2,}$/', $txt[$j])) {
					// Aber im Prinzip müsste man sich die RFCs für Mailadressen und Domainnamen nochmal anschauen!
					// Wort=Mailadresse? -> im text durch dummie ersetzen, im wort durch href.
					$text = preg_replace("/!$txt[$j]!/", "####$j####", $text);
					// kranke lösung für ie: neues fenster mit mailto aufpoppen, dann gleich wieder schließen...
					$rep = "<a href=mailto:" . $txt[$j]
						. " onclick=\"ww=window.open('mailto:" . $txt[$j]
						. "','Chat_Klein','resizable=yes,scrollbars=yes,width=10,height=10'); ww.window.close(); return(false);\">"
						. $txt[$j] . "</a>";
					$txt[$j] = preg_replace("/" . $txt[$j] . "/", $rep, $txt[$j]);
				}
				
				// www.###### in <a href="http://###" target=_blank>http://###</A>
				if (preg_match("/^www\..*\..*/", $txt[$j])) {
					// sonderfall -> "?" in der URL -> dann "?" als Sonderzeichen behandeln...
					$txt2 = preg_replace("!\?!", "\\?", $txt[$j]);
					
					// Doppelcheck, kann ja auch mit http:// in gleicher Zeile nochmal vorkommen. also erst die 
					// http:// mitrausnehmen, damit dann in der Ausgabe nicht http://http:// steht...
					$text = preg_replace("!http(s?)://$txt2!", "-###$1$j####", $text);
					
					// Wort=URL mit www am Anfang? -> im text durch dummie ersetzen, im wort durch href.
					$text = preg_replace("!$txt2!", "####$j####", $text);
					
					// und den ersten Fall wieder Rückwärts, der wird ja später in der schleife nochmal behandelt.
					$text = preg_replace("!-###(s?)\d*####/!", "http$1://$txt2/",
						$text);
					
					// url aufbereiten
					$txt[$j] = preg_replace("/" . $txt2 . "/", "<a href=\"redirect.php?url=" . urlencode("http://$txt2") . "\" target=_blank>http://$txt2</a>", $txt[$j]);
				}
				// http(s)://###### in <a href="http(s)://###" target=_blank>http(s)://###</A>
				if (preg_match("!^https?://!", $txt[$j])) {
					// Wort=URL mit http(s):// am Anfang? -> im text durch dummie ersetzen, im wort durch href.
					// Zusatzproblematik.... könnte ein http-get-URL sein, mit "?" am Ende oder zwischendrin... urgs.
					
					// sonderfall -> "?" in der URL -> dann "?" als Sonderzeichen behandeln...
					$txt2 = preg_replace("!\?!", "\\?", $txt[$j]);
					$text = preg_replace("!$txt2!", "####$j####", $text);
					
					// und wieder Rückwärts, falls zuviel ersetzt wurde...
					$text = preg_replace("!####\d*####/!", "$txt[$j]/", $text);
					
					// url aufbereiten
					$txt[$j] = preg_replace("!$txt2!", "<a href=\"redirect.php?url=" . urlencode($txt2) . "\" target=_blank>$txt2</a>", $txt[$j]);
				}
			}
		}
		
		// nun noch die Dummy-Strings durch die urls ersetzen...
		for ($j = 0; $j <= $i; $j++) {
			// erst mal "_" in den dummy-strings vormerken...
			// es soll auch urls mit "_" geben ;-)
			$txt[$j] = str_replace("_", "###substr###", $txt[$j]);
			$text = preg_replace("![-#]###$j####!", $txt[$j], $text);
		}
	} // ende http, mailto, etc.
	
	// gemerkte @, _ und * zurückwandeln.
	$text = str_replace("###plus###", "+", $text);
	$text = str_replace("###strich###", "|", $text);
	$text = str_replace("###auf###", "[", $text);
	$text = str_replace("###zu###", "]", $text);
	$text = str_replace("###substr###", "_", $text);
	$text = str_replace("###stern###", "*", $text);
	$text = str_replace("###klaffe###", "@", $text);
	
	return array($text, $text_an_user);
}
?>