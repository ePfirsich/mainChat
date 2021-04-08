<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, u_level, o_js
id_lese($id);

// Target von Sonderzeichen entfernen

$fenster = str_replace("+", "", $u_nick);
$fenster = str_replace("-", "", $fenster);
$fenster = str_replace("ä", "", $fenster);
$fenster = str_replace("ö", "", $fenster);
$fenster = str_replace("ü", "", $fenster);
$fenster = str_replace("Ä", "", $fenster);
$fenster = str_replace("Ö", "", $fenster);
$fenster = str_replace("Ü", "", $fenster);
$fenster = str_replace("ß", "", $fenster);

$title = $body_titel . ' - Profil';
zeige_header_anfang($title, 'mini');
?>
<script>
	window.focus()
	function win_reload(file,win_name) {
			win_name.location.href=file;
	}
	function opener_reload(file,frame_number) {
			opener.parent.frames[frame_number].location.href=file;
	}
	function neuesFenster(url,name) {
			hWnd=window.open(url,name,"resizable=yes,scrollbars=yes,width=300,height=580");
	}
	function neuesFenster2(url) { 
			hWnd=window.open(url,"<?php echo "640_" . $fenster; ?>","resizable=yes,scrollbars=yes,width=780,height=580"); 
	}
</script>
<?php
zeige_header_ende();
?>
<body>
<?php
// Timestamp im Datensatz aktualisieren
aktualisiere_online($u_id, $o_raum);

$eingabe_breite = 45;

if ($u_id && $communityfeatures) {
	
	// Fenstername
	$fenster = str_replace("+", "", $u_nick);
	$fenster = str_replace("-", "", $fenster);
	$fenster = str_replace("ä", "", $fenster);
	$fenster = str_replace("ö", "", $fenster);
	$fenster = str_replace("ü", "", $fenster);
	$fenster = str_replace("Ä", "", $fenster);
	$fenster = str_replace("Ö", "", $fenster);
	$fenster = str_replace("Ü", "", $fenster);
	$fenster = str_replace("ß", "", $fenster);
	
	// Prüfung, ob für diesen user bereits ein profil vorliegt -> in $f lesen und merken
	// Falls Array aus Formular übergeben wird, nur ui_id überschreiben
	$query = "SELECT * FROM userinfo WHERE ui_userid=$u_id";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		if (!isset($f) || !is_array($f)) {
			$f = mysqli_fetch_array($result);
		} else {
			$f['ui_id'] = mysqli_result($result, 0, "ui_id");
		}
		$profil_gefunden = true;
	} else {
		$profil_gefunden = false;
	}
	@mysqli_free_result($result);
	
	// Menü als erstes ausgeben
	$box = "Menü Profil";
	$text = "<a href=\"edit.php?http_host=$http_host&id=$id\">Usereinstellungen ändern</a>\n";
	$text .= "| <a href=\"home.php?http_host=$http_host&id=$id&aktion=aendern\">Homepage bearbeiten</a>\n";
	if ($u_level == "S") {
		$text .= "| <a href=\"profil.php?http_host=$http_host&id=$id&aktion=zeigealle\">Alle Profile ausgeben</a>\n";
	}
	$ur1 = "user.php?http_host=$http_host&id=$id";
	$text .= "| <a href=\"$ur1\" target=\"$fenster\" onclick=\"window.open('$ur1','$fenster','resizable=yes,scrollbars=yes,width=300,height=580'); return(false);\">User</a>\n";
	$text .= "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=privacy\">Datenschutzhinweis</a>\n";
	$text .= "| <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=community#profil\">Hilfe</a>\n";
	
	show_box2($box, $text);
	?>
	<img src="pics/fuell.gif" alt="" style="width:4px; height:4px;"><br>
	<?php
	
	// Profil prüfen und ggf. neu eintragen
	if ($los == "Eintragen" && $f['ui_userid']) {
		
		// Schreibrechte?
		if ($f['ui_userid'] == $u_id || $admin) {
			
			$fehler = "";
			
			// Prüfungen
			if ($f['ui_plz'] != ""
				&& !preg_match("/^[0-9]{4,5}$/i", $f['ui_plz']))
				$fehler .= "Die Postleitzahl ist ungültig (z.B.: 97074)<br>\n";
			
			if ($f['ui_geburt'] != ""
				&& !preg_match("/^[0-9]{2}[.][0-9]{2}[.][0-9]{4}$/i",
					$f['ui_geburt']))
				$fehler .= "Das Geburtsdatum ist ungültig (z.B.: 24.01.1969)<br>\n";
			
			if ($f['ui_tel'] != ""
				&& !preg_match(
					"/^([0-9]{0,4})[\/-]{0,1}([0-9]{3,5})[\/-]([0-9]{3,})$/i",
					$f['ui_tel']))
				$fehler .= "Die Telefonnummer ist ungültig (z.B.: 0049-931-123456 oder 0931/123456)<br>\n";
			
			if ($f['ui_fax'] != ""
				&& !preg_match(
					"/^([0-9]{0,4})[\/-]{0,1}([0-9]{3,5})[\/-]([0-9]{3,})$/i",
					$f['ui_fax']))
				$fehler .= "Die Faxnummer ist ungültig (z.B.: 0049-931-123456 oder 0931/123456)<br>\n";
			
			if ($f['ui_handy'] != ""
				&& !preg_match("/^01([0-9]{9,11})$/i", $f['ui_handy']))
				$fehler .= "Die Handynummer ist ungültig (z.B.: 0170123456)<br>\n";
			
			if (strlen($f['ui_strasse']) > 100) {
				$fehler .= "Die Straße ist länger als 100 Zeichen!<br>\n";
			}
			if (strlen($f['ui_ort']) > 100) {
				$fehler .= "Der Ort ist länger als 100 Zeichen!<br>\n";
			}
			if (strlen($f['ui_land']) > 100) {
				$fehler .= "Das Land ist länger als 100 Zeichen!<br>\n";
			}
			if (strlen($f['ui_hobby']) > 255) {
				$fehler .= "Die Hobbies sind länger als 255 Zeichen!<br>\n";
			}
			if (strlen($f['ui_beruf']) > 100) {
				$fehler .= "Der Beruf ist länger als 100 Zeichen!<br>\n";
			}
			if (strlen($f['ui_icq']) > 100) {
				$fehler .= "Die ICQ-Nummer ist länger als 100 Zeichen!<br>\n";
			}
			
			if ($fehler != "") {
				echo "<P><b>Es sind folgende Fehler aufgetreten:</b><br>$fehler\n"
					. "<br><b>Bitte korrieren Sie die Fehler!</P>\n";
			} else {
				
				// Punkte gutschreiben?
				if (!$profil_gefunden && strlen($f['ui_ort']) > 2
					&& strlen($f['ui_plz']) > 3 && strlen($f['ui_strasse']) > 4) {
					punkte(500, $o_id, $u_id,
						"Sie haben Ihr Profil ausgefüllt. Dafür möchten wir uns bedanken:");
				}
				
				// HTML-Zeichen ersetzen
				$f['ui_icq'] = htmlspecialchars($f['ui_icq']);
				$f['ui_hobby'] = htmlspecialchars($f['ui_hobby']);
				$f['ui_beruf'] = htmlspecialchars($f['ui_beruf']);
				$f['ui_land'] = htmlspecialchars($f['ui_land']);
				$f['ui_ort'] = htmlspecialchars($f['ui_ort']);
				$f['ui_strasse'] = htmlspecialchars($f['ui_strasse']);
				$f['ui_icq'] = htmlspecialchars($f['ui_icq']);
				
				// Datensatz schreiben
				$f['ui_id'] = schreibe_db("userinfo", $f, $f['ui_id'], "ui_id");
				echo "<P><b>Ihr Profil wurde gespeichert!</b></P>\n";
				
			}
			
		} else {
			
			// Kein Recht die Daten zu schreiben!
			echo "<P><b>Fehler:</b> Sie haben keine Berechtigung, das Profil von '$nick' zu verändern!</P>";
			
		}
		
	}
	
	switch ($aktion) {
		
		case "neu":
		case "aendern":
		// Neues Profil einrichten oder bestehendes Ändern
			if ($profil_gefunden) {
				echo "<P><b>Bestehendes Profil bearbeiten:</b></P>\n";
			} else {
				echo "<P><b>Neues Profil anlegen:</b></P>";
			}
			
			// Textkopf
			if ($los != "Eintragen") {
				echo "<P>Hallo $u_nick, bitte füllen Sie so viele Felder wie möglich mit ehrlichen "
					. "Angaben aus. Falls Sie die eine oder andere Information über sich "
					. "nicht angeben wollen, lassen Sie das Feld leer oder wählen 'Keine Angabe'. "
					. "Ihr Profil ist in Ihrer Homepage öffentlich abrufbar, falls Sie "
					. "es in den Homepage-Einstellungen freigeben.</P>\n"
					. "<p><b>Vielen Dank, dass Sie Ihr Profil ausfüllen!</b></p>\n";
			}
			
			// Editor ausgeben
			if (!isset($f))
				$f[] = "";
			profil_editor($u_id, $u_nick, $f);
			echo "<br>\n";
			break;
		
		case "zeigealle":
		// Alle Profile listen
			if (!$admin) {
				echo "<p><b>Fehler:</b> Sie haben keine Berechtigung, die Profile zu lesen!</p>";
			} else {
				echo "<P><b>Alle Profile:</b></P><TABLE border=\"1\">\n"
					. "<TR><TH>Nick</TH><TH>Username</TH><TH>Straße</TH><TH>PLZ Ort</TH><TH>Land</TH><TH>Admin-EMail</TH><TH>E-Mail</TH><TH>URL</TH><TH>Geburt</TH><TH>Geschlecht</TH><TH>Fam. Stand</TH><TH>Typ</TH><TH>Beruf</TH><TH>Hobby</TH><TH>Tel</TH><TH>Fax</TH><TH>Handy</TH><TH>ICQ</TH></TR>";
				
				$query = "SELECT * FROM user,userinfo WHERE ui_userid=u_id order by u_nick,u_name";
				$result = mysqli_query($mysqli_link, $query);
				if ($result && mysqli_num_rows($result) > 0) {
					while ($row = mysqli_fetch_object($result)) {
						echo "<TR><TD><b>"
							. htmlspecialchars($row->u_nick)
							. "</b></TD><TD>"
							. htmlspecialchars($row->u_name)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_strasse)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_plz)
							. " "
							. htmlspecialchars($row->ui_ort)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_land)
							. "</TD><TD>"
							. htmlspecialchars($row->u_adminemail)
							. "</TD><TD>"
							. htmlspecialchars($row->u_email)
							. "</TD><TD>"
							. htmlspecialchars($row->u_url)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_geburt)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_geschlecht)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_beziehung)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_typ)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_beruf)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_hobby)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_tel)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_fax)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_handy)
							. "</TD><TD>"
							. htmlspecialchars($row->ui_icq)
							. "</TD></TR>\n";
					}
				}
				echo "</TABLE>\n";
				@mysqli_free_result($result);
			}
			
			break;
		
		default:
			echo "<P><b>Fehler:</b> Aufruf mit ungültigen Parametern!</P>\n";
		
	}
	
}

if ($o_js || !$u_id) {
	echo $f1 . "<p style=\"text-align:center;\">[<a href=\"javascript:window.close();\">$t[sonst1]</a>]</p>" . $f2 . "\n";
}
?>
</body>
</html>