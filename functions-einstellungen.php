<?php
function user_edit($f, $admin, $u_level) {
	// $f = Ass. Array mit Benutzerdaten
	
	global $id, $level, $f1, $f2, $f3, $f4;
	global $farbe_chat_user, $farbe_chat_user_breite, $farbe_chat_user_hoehe, $user_farbe, $t;
	global $u_id, $punktefeatures;
	global $eintritt_individuell;
	global $mysqli_link;
	
	$input_breite = 32;
	$passwort_breite = 15;
	
	if (ist_online($f['u_id'])) {
		$box = str_replace("%user%", $f['u_nick'], $t['user_zeige20']);
	} else {
		$box = str_replace("%user%", $f['u_nick'], $t['user_zeige21']);
	}
	
	$text = '';
	
	// Ausgabe in Tabelle
	$text .= "<form name=\"$f[u_nick]\" action=\"inhalt.php?seite=einstellungen\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"f[u_id]\" value=\"$f[u_id]\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"edit\">\n";
	
	$text .= "<table style=\"width:100%;\">";
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		
		//Avatar upload drag&drop
		$_SESSION['u_id'] = $u_id;
	
		$text .= "<tr><td colspan=\"2\">
<style>
	#holder { border: 10px dashed #ccc; width: 200px; min-height: 200px; margin: 20px auto;}
	#holder.hover { border: 10px dashed #0c0; }
	#holder img { display: block; margin: 10px auto; }
	#holder p { margin: 10px; font-size: 14px; }
	progress { width: 80%; }
	progress:after { content: '%'; }
	.fail { background: #c00; padding: 2px; color: #fff; }
	.hidden { display: none !important;}
</style>

<article>
	<div id='holder'>";
		$u_avatar_pfad = $f['u_avatar_pfad'];
		if($u_avatar_pfad) { // Benutzerdefinierter Avatar
			$text .= '<img src="./avatars/'.$u_avatar_pfad.'" style="width:200px; height:200px;" alt="'.$u_avatar_pfad.'" />';
		} 
	
	$text .= "</div>
	<p>" .$t['avatar_beschreibung1'] ."</p>
	<p id='upload' class='hidden'><label>Drag & drop not supported, but you can still upload via this input field:<br><input type='file'></label></p>
	<p id='filereader'>File API & FileReader API not supported</p>
	<p id='formdata'>XHR2's FormData is not supported</p>
	<p id='progress'>XHR2's upload progress isn't supported</p>
	<p>" . $t['avatar_beschreibung2'] ." <progress id='uploadprogress' min='0' max='100' value='0'>0</progress></p>
	<p>" . $t['avatar_loeschen'] . " <input type='checkbox' name=\"f[u_avatar_pfad]\" id=\"f[u_avatar_pfad]\" value='u_avatar_pfad'></p>
</article>
<script>
var holder = document.getElementById('holder'),
	tests = {
		filereader: typeof FileReader != 'undefined',
		dnd: 'draggable' in document.createElement('span'),
		formdata: !!window.FormData,
		progress: 'upload' in new XMLHttpRequest
	},
	support = {
		filereader: document.getElementById('filereader'),
		formdata: document.getElementById('formdata'),
		progress: document.getElementById('progress')
	},
	acceptedTypes = {
		'image/png': true,
		'image/jpeg': true,
		'image/gif': true
	},
	progress = document.getElementById('uploadprogress'),
	fileupload = document.getElementById('upload');

	'filereader formdata progress'.split(' ').forEach(function (api) {
		if (tests[api] === false) {
			support[api].className = 'fail';
		} else {
			//FFS. I could have done el.hidden = true, but IE doesn't support
			// hidden, so I tried to create a polyfill that would extend the
			// Element.prototype, but then IE10 doesn't even give me access
			// to the Element object. Brilliant.
			support[api].className = 'hidden';
		}
	}
);

function previewfile(file) {
	if (tests.filereader === true && acceptedTypes[file.type] === true) {
		var reader = new FileReader();
		reader.onload = function (event) {
		var image = new Image();
		image.src = event.target.result;
		image.width = 200; // a fake resize
		holder.appendChild(image);
	};
		reader.readAsDataURL(file);
	}
}

function readfiles(files) {
	debugger;
	var formData = tests.formdata ? new FormData() : null;
	for (var i = 0; i < files.length; i++) {
		if (tests.formdata) formData.append('file', files[i]);
		previewfile(files[i]);
	}
	
	// now post a new XHR request
	if (tests.formdata) {
		var xhr = new XMLHttpRequest();
		xhr.open('POST', './avatar_upload.php');
		xhr.onload = function() {
			progress.value = progress.innerHTML = 100;
		};
		
		if (tests.progress) {
			xhr.upload.onprogress = function (event) {
				if (event.lengthComputable) {
					var complete = (event.loaded / event.total * 100 | 0);
					progress.value = progress.innerHTML = complete;
				}
			}
		}
		xhr.send(formData);
	}
}

if (tests.dnd) {
	holder.ondragover = function () { this.className = 'hover'; return false; };
	holder.ondragend = function () { this.className = ''; return false; };
	holder.ondrop = function (e) {
		this.className = '';
		e.preventDefault();
		readfiles(e.dataTransfer.files);
	}
} else {
	fileupload.className = 'hidden';
	fileupload.querySelector('input').onchange = function () {
		readfiles(this.files);
	};
}
</script></td></tr>";
	
	}
	
	// Benutzername
	$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige18']
		. "</b><br>\n" . $f2
		. "<input type=\"text\" value=\"$f[u_nick]\" name=\"f[u_nick]\" SIZE=$input_breite>"
		. "</td></tr>\n";
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige6']
			. "</b><br>\n" . $f2
			. "<input type=\"text\" value=\"$f[u_email]\" name=\"f[u_email]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	}
	
	// Nur für Admins
	if ($admin) {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige3']
			. "</b><br>\n" . $f2
			. "<input type=\"text\" value=\"$f[u_adminemail]\" name=\"f[u_adminemail]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	} else if ($u_level == 'U') {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige3']
			. "</b> (<a href=\"inhalt.php?seite=einstellungen&id=$id&aktion=andereadminmail\">ändern</a>)<br>\n"
			. $f2 . htmlspecialchars($f['u_adminemail'])
			. "</td></tr>\n";
	}
	
	if ($admin) {
		if (!isset($f['u_kommentar']))
			$f['u_kommentar'] = "";
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige49']
			. "</b><br>\n" . $f2 . "<input type=\"text\" value=\""
			. htmlspecialchars($f['u_kommentar'])
			. "\" name=\"f[u_kommentar]\" SIZE=$input_breite>" . "</td></tr>\n";
	}
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige7']
			. "</b><br>\n" . $f2
			. "<input type=\"text\" value=\"$f[u_url]\" name=\"f[u_url]\" SIZE=$input_breite>"
			. "</td></tr>\n";
		
		// Signatur
			$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige44']
			. "</b><br>\n" . $f2 . "<input type=\"text\" value=\""
			. htmlspecialchars($f['u_signatur'])
			. "\" name=\"f[u_signatur]\" SIZE=$input_breite>" . "</td></tr>\n";
		
		if ($eintritt_individuell == "1") {
			// Eintrittsnachricht
			$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige53']
				. "</b><br>\n" . $f2 . "<input type=\"text\" value=\""
				. htmlspecialchars($f['u_eintritt'])
				. "\" name=\"f[u_eintritt]\" SIZE=$input_breite MAXLENGTH=\"100\">"
				. "</td></tr>\n";
			// Austrittsnachricht
			$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige54']
				. "</b><br>\n" . $f2 . "<input type=\"text\" value=\""
				. htmlspecialchars($f['u_austritt'])
				. "\" name=\"f[u_austritt]\" SIZE=$input_breite MAXLENGTH=\"100\">"
				. "</td></tr>\n";
		}
		
		// Passwort
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige19']
			. "</b><br>\n" . $f2
			. "<input type=\"PASSWORD\" name=\"passwort1\" SIZE=$passwort_breite>"
			. "<input type=\"PASSWORD\" name=\"passwort2\" SIZE=$passwort_breite>"
			. "</td></tr>\n";
	}
	
	// System Ein/Austrittsnachrichten Y/N
	$text .= "<tr><td colspan=2><hr size=2 noshade></td></tr>\n";
	$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige51'] . "</b>\n" . $f2 . "</td><td>" . $f1 . "<select name=\"f[u_systemmeldungen]\">";
	if ($f['u_systemmeldungen'] == "Y") {
		$text .= "<option selected value=\"Y\">$t[user_zeige36]";
		$text .= "<option value=\"N\">$t[user_zeige37]";
	} else {
		$text .= "<option value=\"Y\">$t[user_zeige36]";
		$text .= "<option selected value=\"N\">$t[user_zeige37]";
	}
	$text .= "</select>" . $f2 . "</td></tr>\n";
	
	
	// Alle Avatare anzeigen 1/0
	$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige57'] . "</b>\n" . $f2 . "</td><td>" . $f1 . "<select name=\"f[u_avatare_anzeigen]\">";
	if ($f['u_avatare_anzeigen'] == "1") {
		$text .= "<option selected value=\"1\">$t[user_zeige36]";
		$text .= "<option value=\"0\">$t[user_zeige37]";
	} else {
		$text .= "<option value=\"1\">$t[user_zeige36]";
		$text .= "<option selected value=\"0\">$t[user_zeige37]";
	}
	$text .= "</select>" . $f2 . "</td></tr>\n";
	
	// Farbe des Chats
	$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige64'] . "</b>\n" . $f2 . "</td><td>" . $f1 . "<select name=\"f[u_layout_farbe]\">";
	if ($f['u_layout_farbe'] == "2") { // grün
		$text .= "<option value=\"1\">$t[user_zeige65]";
		$text .= "<option selected value=\"2\">$t[user_zeige66]";
		$text .= "<option value=\"3\">$t[user_zeige67]";
		$text .= "<option value=\"4\">$t[user_zeige68]";
	} else if ($f['u_layout_farbe'] == "3") { // rot
		$text .= "<option value=\"1\">$t[user_zeige65]";
		$text .= "<option value=\"2\">$t[user_zeige66]";
		$text .= "<option selected value=\"3\">$t[user_zeige67]";
		$text .= "<option value=\"4\">$t[user_zeige68]";
	} else if ($f['u_layout_farbe'] == "4") { // pink
		$text .= "<option value=\"1\">$t[user_zeige65]";
		$text .= "<option value=\"2\">$t[user_zeige66]";
		$text .= "<option value=\"3\">$t[user_zeige67]";
		$text .= "<option selected value=\"4\">$t[user_zeige68]";
	} else { // blau
		$text .= "<option selected value=\"1\">$t[user_zeige65]";
		$text .= "<option value=\"2\">$t[user_zeige66]";
		$text .= "<option value=\"3\">$t[user_zeige67]";
		$text .= "<option value=\"4\">$t[user_zeige68]";
	}
	$text .= "</select>" . $f2 . "</td></tr>\n";
	
	// Darstellung der Nachrichten im Chat
	$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige69'] . "</b>\n" . $f2 . "</td><td>" . $f1 . "<select name=\"f[u_layout_chat_darstellung]\">";
	if ($f['u_layout_chat_darstellung'] == "2") { // Private Nachrichten hervorheben
		$text .= "<option value=\"1\">$t[user_zeige70]";
		$text .= "<option selected value=\"2\">$t[user_zeige71]";
	} else { // Standard
		$text .= "<option selected value=\"1\">$t[user_zeige70]";
		$text .= "<option value=\"2\">$t[user_zeige71]";
	}
	$text .= "</select>" . $f2 . "</td></tr>\n";
	
	// Smilies Y/N
	$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige35'] . "</b>\n" . $f2
		. "</td><td>" . $f1 . "<select name=\"f[u_smilie]\">";
	if ($f['u_smilie'] == "Y") {
		$text .= "<option selected value=\"Y\">$t[user_zeige36]";
		$text .= "<option value=\"N\">$t[user_zeige37]";
	} else {
		$text .= "<option value=\"Y\">$t[user_zeige36]";
		$text .= "<option selected value=\"N\">$t[user_zeige37]";
	}
	$text .= "</select>" . $f2 . "</td></tr>\n";
	
	// Punkte Anzeigen Y/N
	if ($u_level <> 'G' && $punktefeatures) {
		$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige52'] . "</b>\n" . $f2
			. "</td><td>" . $f1 . "<select name=\"f[u_punkte_anzeigen]\">";
		if ($f['u_punkte_anzeigen'] == "Y") {
			$text .= "<option selected value=\"Y\">$t[user_zeige36]";
			$text .= "<option value=\"N\">$t[user_zeige37]";
		} else {
			$text .= "<option value=\"Y\">$t[user_zeige36]";
			$text .= "<option selected value=\"N\">$t[user_zeige37]";
		}
		$text .= "</select>" . $f2 . "</td></tr>\n";
	}
	
	// Sicherer Modus notwendig
	$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige61'] . "</b>\n" . $f2
	. "</td><td>" . $f1 . "<select name=\"f[u_sicherer_modus]\">";
	if ($f['u_sicherer_modus'] == "Y") {
		$text .= "<option value=\"N\">$t[user_zeige62]";
		$text .= "<option selected value=\"Y\">$t[user_zeige63]";
	} else {
		$text .= "<option selected value=\"N\">$t[user_zeige62]";
		$text .= "<option value=\"Y\">$t[user_zeige63]";
	}
	$text .= "</select>" . $f2 . "</td></tr>\n";
	
	// Level nur für Admins
	if ($admin) {
		$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige8'] . "</b>\n" . $f2
			. "</td><td>" . $f1 . "<select name=\"f[u_level]\">\n";
		
		// Liste der Gruppen ausgeben
		
		reset($level);
		$i = 0;
		while ($i < count($level)) {
			$name = key($level);
			// Alle Level außer Besitzer zur Auswahl geben, für Gäste gibt es nur Gast
			if ($name != "B") {
				if ($f['u_level'] == "G") {
					if ($i == 0) {
						$text .= "<option selected value=\"G\">$level[G]\n";
					}
				} else {
					if ($name != "G") {
						if ($f['u_level'] == $name) {
							$text .= "<option selected value=\"$name\">$level[$name]\n";
						} else {
							$text .= "<option value=\"$name\">$level[$name]\n";
						}
					}
				}
			}
			next($level);
			$i++;
		}
		$text .= "</select>" . $f2 . "</td></tr>\n";
	}
	
	// Default für Farbe setzen, falls undefiniert
	if (strlen($f['u_farbe']) == 0) {
		$f['u_farbe'] = $user_farbe;
	}
	
	$link = "";
	// Farbe direkt einstellen
	if ($f['u_id'] == $u_id) {
		$url = "home_farben.php?id=$id&mit_grafik=0&feld=u_farbe&bg=Y&oldcolor="
			. urlencode($f['u_farbe']);
		$link = "<b>[<a href=\"$url\" target=\"Farben\" onclick=\"window.open('$url','Farben','resizable=yes,scrollbars=yes,width=400,height=500'); return(false);\">$t[user_zeige46]</A>]</b>";
		$text .= "<tr><td colspan=2><hr size=2 noshade></td></tr>"
			. "<tr><td>$f1<b>" . $t['user_zeige45'] . "</b>\n" . $f2
			. "</td><td>" . $f1
			. "<input type=\"text\" name=\"f[u_farbe]\" SIZE=7 value=\"$f[u_farbe]\">"
			. "<input type=\"hidden\" name=\"farben[u_farbe]\">" . $f2
			. "&nbsp;" . $f3 . $link . $f4 . "</td></tr>\n";
	} else if ($admin) {
		$text .= "<tr><td colspan=2><hr size=2 noshade></td></tr>"
			. "<tr><td>$f1<b>" . $t['user_zeige45'] . "</b>\n" . $f2
			. "</td><td>" . $f1
			. "<input type=\"text\" name=\"f[u_farbe]\" SIZE=7 value=\"$f[u_farbe]\">"
			. "<input type=\"hidden\" name=\"farben[u_farbe]\">" . $f2
			. "&nbsp;" . $f3 . $link . $f4 . "</td></tr>\n";
	}
	
	$text .= "</table>\n";
	
	$text .= $f1
		. "<hr size=2 noshade><input type=\"SUBMIT\" name=\"eingabe\" value=\"Ändern!\">"
		. $f2;
	
	if ($admin) {
		$text .= $f1
			. "&nbsp;<input type=\"SUBMIT\" name=\"eingabe\" value=\"Löschen!\">"
			. $f2;
	}
	
	// Farbenliste & aktuelle Farbe
	
	if ($f['u_id'] == $u_id) {
		$text .= "\n<hr size=2 noshade><table><tr><td colspan=\"2\"><b>"
			. $t['user_zeige10'] . "&nbsp;</b></td>" . "<td style=\"background-color:#". $f['u_farbe'] . ";\">&nbsp;&nbsp;&nbsp;</td>" . "</tr></table>";
		$text .= "<table style=\"border-collapse: collapse;\"><tr>\n";
		foreach ($farbe_chat_user as $key => $val) {
			$text .= "<td style=\"padding-left:0px; padding-right:0px;\">"
				. "<a href=\"inhalt.php?seite=einstellungen&id=$id&aktion=edit&f[u_id]=$f[u_id]&farbe=$val\">"
				."<div style=\"background-color:#" . $val ." ; width:" . $farbe_chat_user_breite . "px; height:" .  $farbe_chat_user_hoehe . "px; border:0px;\"></div></a></td>\n";
		}
		$text .= "</tr></table>\n";
	}
	
	// Fuß der Tabelle
	$text .= "</form>\n";
	
	// Box anzeigen
	zeige_tabelle_zentriert($box, $text);
	
}

function zeige_aktionen($aktion) {
	// Zeigt Matrix der Aktionen an
	// Definition der aktionen in config.php ($def_was)
	
	global $id, $PHP_SELF, $f1, $f2, $f3, $f4, $mysqli_link, $u_nick, $u_id, $def_was;
	global $forumfeatures;
	
	$query = "SELECT * FROM aktion " . "WHERE a_user=$u_id ";
	$button = "Eintragen";
	$box = "$t[aktion4] $u_nick $t[aktion5]:";
	$text = '';
	
	$text .= "<form name=\"freund_loeschen\" action=\"inhalt.php?seite=einstellungen\" method=\"post\">\n"
	. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
	. "<input type=\"hidden\" name=\"aktion\" value=\"aktion-eintragen\">\n"
		. "<table style=\"width:100%;\">";
		
		// Einstellungen aus Datenbank lesen
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) > 0) {
			while ($row = mysqli_fetch_array($result)) {
				$was[$row['a_was']][$row['a_wann']] = $row;
			}
		}
		
		// Aktionen zeigen
		$text .= "<tr><td></td>";
		
		$i = 0;
		$bgcolor = 'class="tabelle_zeile1"';
		
		// Alle möglichen a_wann in Array lesen
		$query = "SHOW COLUMNS FROM aktion like 'a_wann'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) != 0) {
			$txt = str_replace("'", "",
				substr(mysqli_result($result, 0, "Type"), 5, -1));
			$a_wann = explode(",", $txt);
		}
		$anzahl_spalten = count($a_wann);
		
		// Alle möglichen a_wie in Array lesen
		$query = "SHOW COLUMNS FROM aktion like 'a_wie'";
		$result = mysqli_query($mysqli_link, $query);
		if ($result && mysqli_num_rows($result) != 0) {
			$txt = str_replace("'", "",
				substr(mysqli_result($result, 0, "Type"), 4, -1));
			$a_wie = explode(",", $txt);
		}
		$anzahl_wie = count($a_wie);
		
		// OLM merken
		$onlinemessage = $a_wie[3];
		
		// Wenn das Forum deaktiviert wird, sollte der 3. Eintrag aus $def_was raus, wichtig ist aber die Einhaltung der Reihenfolge in def_was
		if (!$forumfeatures) {
			unset($def_was[2]);
		}
		
		// Spezial Offline-Nachrichten (OLM löschen)
		$offline_wie = $a_wie;
		for ($i = 0; $i < $anzahl_wie; $i++) {
			if (isset($offline_wie[$i]) && $offline_wie[$i] == $onlinemessage)
				unset($offline_wie[$i]);
		}
		
		// Alle Kombinationen von a_wie mit onlinemessage erstellen
		// Keine muss als erstes, OLM als letztes definiert sein!
		for ($i = 1; $i < $anzahl_wie - 2; $i++) {
			$a_wie[] = $a_wie[$i] . "," . $onlinemessage;
		}
		
		// Zeile der a_wann ausgeben
		foreach ($a_wann as $a_wann_eintrag) {
			$text .= "<td style=\"text-align:center;\"><b>" . $f1 . $a_wann_eintrag . $f2 . "</b></td>\n";
		}
		$text .= "</tr>\n";
		
		// Tabelle zeilenweise ausgeben
		$i = 0;
		foreach ($def_was as $def_was_eintrag) {
			
			$text .= "<tr><td style=\"text-align:right; font-weight:bold;\" $bgcolor>" . $f1 . $def_was_eintrag . $f2 . "</td>\n";
			foreach ($a_wann as $a_wann_eintrag) {
				$text .= "<td style=\"text-align:center;\" $bgcolor>" . $f3
				. "<select name=\"aktion_datensatz[$def_was_eintrag][$a_wann_eintrag]\">\n";
				
				// Zwischen offline/online unterscheiden
				if ($a_wann_eintrag == "Sofort/Offline") {
					$wie = $offline_wie;
				} else {
					$wie = $a_wie;
				}
				
				// Nachrichtentypen einzeln als Auswahl ausgeben
				foreach ($wie as $auswahl) {
					if (isset($was[$def_was_eintrag][$a_wann_eintrag]) && $was[$def_was_eintrag][$a_wann_eintrag]['a_wie'] == $auswahl) {
						$text .= "<option selected value=\"" . $was[$def_was_eintrag][$a_wann_eintrag]['a_id'] . "|" . $auswahl . "\">" . str_replace(",", "+", $auswahl) . "\n";
					} else {
						$text .= "<option value=\"" . (isset($was[$def_was_eintrag][$a_wann_eintrag]) ? $was[$def_was_eintrag][$a_wann_eintrag]['a_id'] : "") . "|" . $auswahl . "\">"
									. str_replace(",", " + ", $auswahl) . "\n";
					}
				}
				$text .= "</select>" . $f4 . "</td>\n";
				
			}
			$text .= "</tr>\n";
			
			if (($i % 2) > 0) {
				$bgcolor = 'class="tabelle_zeile1"';
			} else {
				$bgcolor = 'class="tabelle_zeile2"';
			}
			$i++;
		}
		
		$text .= "<tr><td $bgcolor>&nbsp;</td><td style=\"text-align:right;\" $bgcolor colspan=\"4\">"
		. $f1 . "<input type=\"submit\" name=\"los\" value=\"$button\">" . $f2
		. "</td></tr>\n" . "</table></form>\n";
		
		// Box anzeigen
		zeige_tabelle_zentriert($box, $text);
}

function eintrag_aktionen($aktion_datensatz) {
	// Array mit definierten Aktionen in die DB schreiben
	
	global $def_was, $u_id, $u_nick, $mysqli_link, $t;
	
	// Alle möglichen a_wann in Array lesen
	$query = "SHOW COLUMNS FROM aktion LIKE 'a_wann'";
	$result = mysqli_query($mysqli_link, $query);
	if ($result && mysqli_num_rows($result) != 0) {
		$txt = str_replace("'", "", substr(mysqli_result($result, 0, "Type"), 5, -1));
		$a_wann = explode(",", $txt);
	}
	
	foreach ($def_was as $def_was_eintrag) {
		foreach ($a_wann as $a_wann_eintrag) {
			// In aktion_datensatz stehen ID und Wert als a_id|a_wie
			$temp = explode("|", $aktion_datensatz[$def_was_eintrag][$a_wann_eintrag]);
			
			if (!$temp[0] || $temp[0] == "0" || $temp[0] == "") {
				$query = "DELETE FROM aktion "
				. "WHERE a_was='" . mysqli_real_escape_string($mysqli_link, $def_was_eintrag) . "' "
				. "AND a_wann='" . mysqli_real_escape_string($mysqli_link, $a_wann_eintrag) . "' " . "AND a_user='$u_id'";
				$result = mysqli_query($mysqli_link, $query);
			}
			
			$f['a_wie'] = $temp[1];
			$f['a_was'] = $def_was_eintrag;
			$f['a_wann'] = $a_wann_eintrag;
			$f['a_user'] = $u_id;
			$f['a_text'] = $u_nick;
			schreibe_db("aktion", $f, $temp[0], "a_id");
		}
	}
	$box = $t['edit_erfolgsmeldung'];
	$text = $t['edit24'];
	zeige_tabelle_zentriert($box, $text);
}
?>