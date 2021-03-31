<?php
// Anzahl der User, die gerade Online sind, als Grafik ausgeben

// time limit auf 3 sekunden - falls z.B. ein DB-Server hängt, wird
// hiermit hoffentlich verhindert, daß dieser Prozess zu lange hängt
// und den Apache blockiert...

set_time_limit(3);

$userinfo_hilfe = "<B>Es wird eine transparente Grafik als JPG erzeugt, welche die aktuelle Zahl\nder User im Chat enthält. "
	. "Falls kein User im Chat ist, wird ein leeres JPG ausgegeben.\n\n"
	. "Übergabeparameter:</B>\n" . " text   - Text nach der Zahl\n"
	. " size   - Größe in Punkt für die Schrift\n"
	. " hrot   - Hintergrundfarbe Rotanteil\n"
	. " hgruen - Hintergrundfarbe Grünanteil\n"
	. " hblau  - Hintergrundfarbe Blauanteil\n"
	. " vrot   - Hintergrundfarbe Rotanteil\n"
	. " vgruen - Hintergrundfarbe Grünanteil\n"
	. " vblau  - Hintergrundfarbe Blauanteil\n"
	. " registriert=j  - zeige User-registriert statt User-online\n"
	. " art=user	   - Zeige Anzahl der User als Grafik oder Text (Voreinstellung)\n"
	. " art=raumliste  - Zeige Textliste der Räume, in denen ein Login möglich ist\n"
	. " art=alles	  - Zeige Textliste mit den wichtigen Informationen\n"
	. " null=j  - Falls j, erfolgt bei \"0 User online\" eine Ausgabe. Ansonsten wird nur ein transparentes leeres JPG ausgegeben.\n"
	. " text=j  - es wird statt einer Grafik die Anzahl der User als Text ausgegeben.\n\n"
	. " jscript=j  - es wird statt einer Grafik die Anzahl der User als Text ausgegeben.\n\n"
	. "<B>Beispiel 1:</B>\n&lt;IMG&nbsp;SRC=\"http://chat.main.de/userinfo.php?size=24&text=User+gerade+online+im+Testchat\"&gt;\n"
	. "ergibt folgende Grafik:\n\n"
	. "<IMG SRC=\"http://chat.main.de/userinfo.php?size=24&text=User+gerade+online+im+Testchat\">\n\n"
	. "<B>Beispiel 2:</B>\n&lt;IMG&nbsp;SRC=\"http://chat.main.de/userinfo.php?size=24&hrot=0&hgruen=0&hblau=0&vrot=255&vgruen=255&vblau=0\"&gt;\n"
	. "ergibt folgende Grafik in Gelb für einen blauen Hintergrund:\n\n"
	. "<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#0000FF\"><TR><TD><IMG SRC=\"http://chat.main.de/userinfo.php?size=24&hrot=0&hgruen=0&hblau=255&vrot=255&vgruen=255&vblau=0\"></TD></TR></TABLE>\n";

// Falls ?hilfe übergeben wurde, Hilfe anzeigen

// voreingestellter Zeichensatz
$fontname = "arialbd.ttf";

require("functions-init.php");
require("functions.php");

function get_maximum_height($fontname, $fontsize) {
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890!\"ß%&/()=?ß*+";
	$oldheight = 0;
	for ($i = 0; $i < strlen($str); $i++) {
		unset($b);
		$b = ImageTTFBBox($fontsize, 0, $fontname, $str[$i]);
		$height = abs($b[7]) + abs($b[1]);
		$height = $height - round($height / 4);
		if ($height > $oldheight) {
			$oldheight = $height;
		}
	}
	return array("0" => $oldheight, "1" => abs($b[7]));
}

function JPGText($str, $fontname, $fontsize, $backcol, $txtcol) {
	global $layout;
	
	Header("Last-Modified: " . gmDate("D, d M Y H:i:s", Time()) . " GMT");
	Header("Expires: " . gmDate("D, d M Y H:i:s", Time() - 3601) . " GMT");
	Header("Pragma: no-cache");
	Header("Cache-control: no-cache");
	Header("Content-Type: image/jpeg");
	$a = ImageTTFBBox($fontsize, 0, $fontname, $str);
	$width = $a[2] + 4;
	$bla = get_maximum_height($fontname, $fontsize);
	$height = $bla[0] + 3;
	$bl = $bla[1];
	$im = ImageCreate($width, $height);
	$bgcol = ImageColorAllocate($im, $backcol['red'], $backcol['green'],
		$backcol['blue']);
	$fgcol = ImageColorAllocate($im, $txtcol['red'], $txtcol['green'],
		$txtcol['blue']);
	
	if (!function_exists(imagegif)) {
		imageTTFText($im, $fontsize, 0, 2, $bl + ($fontsize / 6) + 2, $fgcol,
			$fontname, $str);
		imagejpeg($im, "", 80);
	} else {
		ImageColorTransparent($im, $bgcol);
		imageTTFText($im, $fontsize, 0, 2, $bl + ($fontsize / 6) + 2, $fgcol,
			$fontname, $str);
		imagegif($im);
	}
	ImageDestroy($im);
}

if ($_SERVER['QUERY_STRING'] == "hilfe") {
	// Hilfe anzeigen
	
	$title = $body_titel . ' - Info';
	zeige_header_anfang($title, $farbe_mini_background, $grafik_mini_background, $farbe_mini_link, $farbe_mini_vlink);
	?>
	<?php
	zeige_header_ende();
	?>
	<body>
	<pre>
	<?php
	echo "userinfo.php - (C) by fidion GmbH 1999-2012\n\n" . $userinfo_hilfe . "\n</pre>";
	?>
	</body>
	</html>
	<?php
} else {
	if (!isset($art)) {
		$art = "";
	}
	
	// Art der Aktion
	switch ($art) {
		
		case "raumliste":
			if ($raum_auswahl && !$beichtstuhl) {
				
				// Falls eintrittsraum nicht gesetzt ist, mit Lobby überschreiben
				if (strlen($eintrittsraum) == 0) {
					$eintrittsraum = $lobby;
				}
				
				// Raumauswahlliste erstellen
				$query = "SELECT r_name,r_id FROM raum "
					. "WHERE (r_status1='O' OR r_status1 LIKE BINARY 'm') AND r_status2='P'"
					. "ORDER BY r_name";
				
				$result = mysqli_query($mysqli_link, $query);
				$raeume = "<SELECT NAME=\"eintritt\">";
				
				$i = 0;
				if ($communityfeatures && $forumfeatures)
					$raeume = $raeume
						. "<OPTION VALUE=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
				if ($result && mysqli_num_rows($result) > 0) {
					while ($row = mysqli_fetch_object($result)) {
						if ((!isset($eintritt)
							AND $row->r_name == $eintrittsraum)
							|| (isset($eintritt) AND $row->r_id == $eintritt)) {
							$raeume = $raeume
								. "<OPTION SELECTED VALUE=\"$row->r_id\">$row->r_name\n";
						} else {
							$raeume = $raeume
								. "<OPTION VALUE=\"$row->r_id\">$row->r_name\n";
						}
						$i++;
					}
				}
				if ($communityfeatures && $forumfeatures)
					$raeume = $raeume
						. "<OPTION VALUE=\"forum\">&gt;&gt;Forum&lt;&lt;\n";
				$raeume = $raeume . "</SELECT>\n";
				@mysqli_free_result($result);
			} else {
				if (strlen($eintrittsraum) == 0) {
					$eintrittsraum = $lobby;
				}
				$query = "SELECT r_id FROM raum WHERE r_name = '" . mysqli_real_escape_string($mysqli_link, $eintrittsraum) . "'";
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) == 1) {
					$lobby_id = mysqli_result($result, 0, "r_id");
				}
				@mysqli_free_result($result);
				$raeume = "<INPUT TYPE=HIDDEN NAME=\"eintritt\" VALUE=$lobby_id>\n";
			}
			
			echo $raeume;
			break;
		
		case "alles":
			if (!$beichtstuhl) {
				
				$ergebnis = array();
				
				// Wie viele User sind in der DB?
				$query = "SELECT COUNT(u_id) FROM `user` WHERE `u_level` IN ('A','C','G','M','S','U')";
				$result = mysqli_query($mysqli_link, $query);
				$rows = mysqli_num_rows($result);
				if ($result) {
					$ergebnis['registriert'] = mysqli_result($result, 0, 0);
					mysqli_free_result($result);
				} else {
					$ergebnis['registriert'] = 0;
				}
				
				// User online und Räume bestimmen -> merken
				$query = "SELECT o_who,o_name,o_level,r_name,r_status1,r_status2, "
					. "r_name='" . mysqli_real_escape_string($mysqli_link, $lobby) . "' as lobby "
					. "FROM online left join raum on o_raum=r_id  "
					. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
					. "ORDER BY lobby desc,r_name,o_who,o_name ";
				
				$result2 = mysqli_query($mysqli_link, $query);
				if ($result2) {
					$ergebnis['online'] = mysqli_num_rows($result2);
				} else {
					$ergebnis['online'] = 0;
				}
				
				if ($abweisen == false and !$beichtstuhl
					and $ergebnis['online'] > 0) {
					
					$text = '';
					$i = 0;
					$r_name_alt = '';
					$zeigen_alt = TRUE;
					while ($row = mysqli_fetch_object($result2)) {
						
						if (($row->o_level == 'S') or ($row->o_level == 'C')) {
							$nick = str_replace(';', '',
								'<b class="admin">' . $row->o_name . '</b>');
						} else {
							$nick = str_replace(';', '', $row->o_name);
						}
						
						// Unterscheidung Raum oder Community-Modul
						if (!$row->r_name or $row->r_name == 'NULL') {
							$r_name = $whotext[$row->o_who];
							$zeigen = TRUE;
						} else {
							// Nur offene, permanente Räume zeigen
							if (($row->r_status1 == 'O'
								|| $row->r_status1 == 'm')
								&& $row->r_status2 == 'P') {
								$zeigen = TRUE;
							} else {
								$zeigen = FALSE;
							}
							$r_name = $row->r_name;
						}
						
						// Textwechsel
						if ($r_name_alt != $r_name) {
							if (strlen($text) == 0) {
								$text = $nick . ';';
							} else {
								// Nur offene, permanente Räume zeigen
								if ($zeigen_alt) {
									$ergebnis[$r_name_alt] = $text;
								}
								$text = $nick . ';';
							}
							$r_name_alt = $r_name;
							$zeigen_alt = $zeigen;
						} else {
							$text .= $nick . ';';
						}
						$i++;
					}
					if ($zeigen_alt) {
						$ergebnis[$r_name_alt] = $text;
					}
					mysqli_free_result($result2);
					
				} else {
					@mysqli_free_result($result2);
				}
				foreach ($ergebnis as $key => $val) {
					echo $key . ";" . $val . "\n";
				}
			}
			
			break;
		
		default:
		// Anzahl der User abfragen
			if (isset($registriert) && $registriert == "j") {
				$query = "SELECT COUNT(u_id) AS `anzahl` FROM `user` WHERE `u_level` IN ('A','C','G','M','S','U')";
				$result = @mysqli_query($mysqli_link, $query);
				if ($result && @mysqli_num_rows($result) > 0) {
					$anzahl = @mysqli_result($result, 0, "anzahl");
					mysqli_free_result($result);
				}
			} else {
				$query = "SELECT COUNT(o_id) AS `anzahl` FROM `online` "
					. "WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout";
				$result = @mysqli_query($mysqli_link, $query);
				if ($result && @mysqli_num_rows($result) > 0) {
					$anzahl = @mysqli_result($result, 0, "anzahl");
					mysqli_free_result($result);
				}
			}
			
			if (isset($anzahl)) {
				if (!isset($text))
					$text = "";
				if (!isset($jscript))
					$jscript = "";
				if (!isset($registriert))
					$registriert = "";
				if (!isset($null))
					$null = "";
				
				if ($text != "j" && $jscript != "j") {
					
					// Voreinstellungen
					if (!isset($hrot)) {
						$hrot = 255;
					}
					if (!isset($hgruen)) {
						$hgruen = 255;
					}
					if (!isset($hblau)) {
						$hblau = 255;
					}
					if (!isset($vrot)) {
						$vrot = 0;
					}
					if (!isset($vgruen)) {
						$vgruen = 0;
					}
					if (!isset($vblau)) {
						$vblau = 0;
					}
					if (!isset($size)) {
						$size = 12;
					}
					if (!$text) {
						if ($registriert == "j") {
							$text = "User im $chat registriert!";
							$text0 = "Kein";
						} else {
							$text = "User im $chat online!";
							$text0 = "Kein";
						}
					} else {
						$text0 = "0";
					}
					if ($size > 64) {
						$size = 64;
					}
					// Nur bei mindestens einem User Anzahl ausgeben
					if ($anzahl == 0 && $null != "j") {
						$text = "";
					} elseif ($anzahl == 0) {
						$text = $text0 . " " . $text;
					} else {
						$text = $anzahl . " " . $text;
					}
					
					// Aufgabe JPG
					JPGText(urldecode($text), $fontname, $size,
						array("red" => $hrot, "green" => $hgruen,
							"blue" => $hblau),
						array("red" => $vrot, "green" => $vgruen,
							"blue" => $vblau));
					
				} else {
					if ($text == "j") {
						// Textausgabe
						echo $anzahl . "\n";
					}
					if ($jscript == "j") {
						echo "document.write('$anzahl $text');\n";
					}
				}
			}
	}
}
?>