<?php
session_start();
require_once("functions.php-func-verlasse_chat.php");
require_once("functions.php-func-nachricht.php");

function user_edit($f, $admin, $u_level, $size = ARRAY()) {
	// $f = Ass. Array mit Userdaten
	// $size = Ass. Array mit Fenstereinstellungen (Optional)
	
	global $id, $level, $f1, $f2, $f3, $f4;
	global $farbe_chat_user, $farbe_chat_user_groesse, $user_farbe;
	global $t, $backup_chat, $smilies_pfad, $erweitertefeatures;
	global $frame_size, $u_id, $communityfeatures, $punktefeatures;
	global $einstellungen_aendern, $eintritt_individuell;
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
	$text .= "<form name=\"$f[u_nick]\" ACTION=\"edit.php\" method=\"post\">\n"
		. "<input type=\"hidden\" name=\"id\" value=\"$id\">\n"
		. "<input type=\"hidden\" name=\"f[u_id]\" value=\"$f[u_id]\">\n"
		. "<input type=\"hidden\" name=\"aktion\" value=\"edit\">\n";
	
	$text .= "<table style=\"width:100%;\">";
	
	
	//Avatar upload drag&drop
	include "./conf/config.php";
	
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
  <div id='holder'>
  </div>
  <p>Um euer Avatar zu &auml;ndern, zieht einfach euer neues Bild auf die daf&uuml;r vorgesehene Fl&auml;che.</p>
  <p id='upload' class='hidden'><label>Drag & drop not supported, but you can still upload via this input field:<br><input type='file'></label></p>
  <p id='filereader'>File API & FileReader API not supported</p>
  <p id='formdata'>XHR2's FormData is not supported</p>
  <p id='progress'>XHR2's upload progress isn't supported</p>
  <p>Upload progress: <progress id='uploadprogress' min='0' max='100' value='0'>0</progress></p>
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
    // FFS. I could have done el.hidden = true, but IE doesn't support
    // hidden, so I tried to create a polyfill that would extend the
    // Element.prototype, but then IE10 doesn't even give me access
    // to the Element object. Brilliant.
    support[api].className = 'hidden';
  }
});

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
	
	
	
	
	
	
	
	
	
	
	
	
	
	// Nur für Admins
	if ($admin) {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige17']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_name]\" name=\"f[u_name]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	} else if (($einstellungen_aendern) && ($u_level == 'U')) {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige17']
			. "</b> (<a href=\"edit.php?id=$id&aktion=andereadminmail\">ändern</a>)<br>\n"
			. $f2 . htmlspecialchars($f['u_name'])
			. "</td></tr>\n";
	}
	
	if (!$einstellungen_aendern) {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige18']
			. "</b>&nbsp;&nbsp;\n" . $f['u_nick'] . $f2 . "</td></tr>\n";
	} else {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige18']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_nick]\" name=\"f[u_nick]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	}
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige6']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_email]\" name=\"f[u_email]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	}
	
	// Nur für Admins
	if ($admin) {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige3']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_adminemail]\" name=\"f[u_adminemail]\" SIZE=$input_breite>"
			. "</td></tr>\n";
	} else if (($einstellungen_aendern) && ($u_level == 'U')) {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige3']
			. "</b> (<a href=\"edit.php?id=$id&aktion=andereadminmail\">ändern</a>)<br>\n"
			. $f2 . htmlspecialchars($f['u_adminemail'])
			. "</td></tr>\n";
	}
	
	if ($admin) {
		if (!isset($f['u_kommentar']))
			$f['u_kommentar'] = "";
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige49']
			. "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\""
			. htmlspecialchars($f['u_kommentar'])
			. "\" name=\"f[u_kommentar]\" SIZE=$input_breite>" . "</td></tr>\n";
	}
	
	// Für alle außer Gäste
	if ($u_level != "G") {
		$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige7']
			. "</b><br>\n" . $f2
			. "<input type=\"TEXT\" value=\"$f[u_url]\" name=\"f[u_url]\" SIZE=$input_breite>"
			. "</td></tr>\n";
		
		// Signatur
			$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige44']
			. "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\""
			. htmlspecialchars($f['u_signatur'])
			. "\" name=\"f[u_signatur]\" SIZE=$input_breite>" . "</td></tr>\n";
		
		if ($eintritt_individuell == "1") {
			// Eintrittsnachricht
			$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige53']
				. "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\""
				. htmlspecialchars($f['u_eintritt'])
				. "\" name=\"f[u_eintritt]\" SIZE=$input_breite MAXLENGTH=\"100\">"
				. "</td></tr>\n";
			// Austrittsnachricht
			$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige54']
				. "</b><br>\n" . $f2 . "<input type=\"TEXT\" value=\""
				. htmlspecialchars($f['u_austritt'])
				. "\" name=\"f[u_austritt]\" SIZE=$input_breite MAXLENGTH=\"100\">"
				. "</td></tr>\n";
		}
		
		// Passwort
		if ($einstellungen_aendern) {
			$text .= "<tr><td colspan=2>" . $f1 . "<b>" . $t['user_zeige19']
				. "</b><br>\n" . $f2
				. "<input type=\"PASSWORD\" name=\"passwort1\" SIZE=$passwort_breite>"
				. "<input type=\"PASSWORD\" name=\"passwort2\" SIZE=$passwort_breite>"
				. "</td></tr>\n";
		}
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
	$text .= "<tr><td>" . $f1 . "<b>" . $t['user_zeige57'] . "</b>\n" . $f2 . "</td><td>" . $f1 . "<select name=\"f[avatar_status]\">";
	if ($f['avatar_status'] == "1") {
		$text .= "<option selected value=\"1\">$t[user_zeige36]";
		$text .= "<option value=\"0\">$t[user_zeige37]";
	} else {
		$text .= "<option value=\"1\">$t[user_zeige36]";
		$text .= "<option selected value=\"0\">$t[user_zeige37]";
	}
	$text .= "</select>" . $f2 . "</td></tr>\n";
	
	// Smilies Y/N
	if ($smilies_pfad && $erweitertefeatures) {
		$text .= "<tr><td colspan=2><hr size=2 noshade></td></tr>\n";
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
	}
	
	// Punkte Anzeigen Y/N
	if ($communityfeatures && $u_level <> 'G' && $punktefeatures) {
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
	
	// Einstellungen für Fenstergrößen
	if ($u_level != "G") {
		$text .= "<tr><td colspan=\"2\"><hr size=\"2\" noshade>" . $f1 . "<b>" . $t['user_zeige43'] . "</b>\n" . $f2 . "</td></tr>\n";
		foreach ($frame_size as $key => $val) {
			$text .= "<tr><td>" . $f1 . "<b>" . $t[$key] . "</b>\n" . $f2
				. "</td><td>" . $f1
				. "<input type=\"text\" name=\"size[$key]\" size=\"4\" value=\"$size[$key]\">&nbsp;"
				. str_replace("%vor%", $val, $t['user_zeige42']) . $f2
				. "</td></tr>\n";
		}
	}
	
	// Default für Farbe setzen, falls undefiniert
	if (strlen($f['u_farbe']) == 0) {
		$f['u_farbe'] = $user_farbe;
	}
	
	$link = "";
	// Farbe direkt einstellen
	if ($f['u_id'] == $u_id) {
		if ($communityfeatures) {
			$url = "home_farben.php?id=$id&mit_grafik=0&feld=u_farbe&bg=Y&oldcolor="
				. urlencode($f['u_farbe']);
			$link = "<b>[<a href=\"$url\" target=\"Farben\" onclick=\"window.open('$url','Farben','resizable=yes,scrollbars=yes,width=400,height=500'); return(false);\">$t[user_zeige46]</A>]</b>";
		}
		$text .= "<tr><td colspan=2><hr size=2 noshade></td></tr>"
			. "<tr><td>$f1<b>" . $t['user_zeige45'] . "</b>\n" . $f2
			. "</td><td>" . $f1
			. "<input type=\"TEXT\" name=\"f[u_farbe]\" SIZE=7 value=\"$f[u_farbe]\">"
			. "<input type=\"hidden\" name=\"farben[u_farbe]\">" . $f2
			. "&nbsp;" . $f3 . $link . $f4 . "</td></tr>\n";
	} else if ($admin) {
		$text .= "<tr><td colspan=2><hr size=2 noshade></td></tr>"
			. "<tr><td>$f1<b>" . $t['user_zeige45'] . "</b>\n" . $f2
			. "</td><td>" . $f1
			. "<input type=\"TEXT\" name=\"f[u_farbe]\" SIZE=7 value=\"$f[u_farbe]\">"
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
			$text .= "<td WIDTH=$farbe_chat_user_groesse " . "BGCOLOR=\"#" . $val
				. "\">"
				. "<a href=\"edit.php?id=$id&aktion=edit&f[u_id]=$f[u_id]&farbe=$val\">"
				. "<img src=\"pics/fuell.gif\" style=\"width:" . $farbe_chat_user_groesse . "px; height:" . $farbe_chat_user_groesse . "; border:0px;\" alt=\"\"></a></td>\n";
		}
		$text .= "</tr></table>\n";
	}
	
	// Fuß der Tabelle
	$text .= "</form>\n";
	
	// Box anzeigen
	show_box_title_content($box, $text);
	
}
?>