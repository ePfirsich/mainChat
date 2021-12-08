<?php
// Allgemeine Übersetzungen

setlocale(LC_TIME, "de_DE");

// Kurzer Hilfstext
$hilfstext = array(
	1 => "<b>/user RAUM -</b> listet alle Benutzer im Raum (*=alle Räume)",
	"<b>/raum RAUM -</b> wechselt in RAUM (legt ggf. RAUM neu an), ohne Angabe von RAUM werden alle Räume gelistet",
	"<b>/zeige NAME-</b> Gibt die Benutzerinformationen für NAME aus (%=Joker)",
	"<b>/msg NAME TEXT -</b> TEXT an Benutzer NAME flüstern",
	"<b>/me TEXT -</b> Spruch im Raum",
	"<b>/weg TEXT-</b> setzt oder löscht einen ich-bin-nicht-da Text",
	"<b>/ignore NAME -</b> Ignoriert Benutzer NAME, ohne Angabe von NAME werden alle ignorierten Benutzer gezeigt",
	"<b>/kick NAME -</b> Sperrt Benutzer NAME aus dem aktuellen Raum aus (oder Freigabe)",
	"<b>/farbe RRGGBB -</b> Setzt neue Farbe auf RRGGBB, ohne Angabe von RRGGBB wird die aktuelle Farbe angezeigt",
	"<b>/op TEXT -</b> ruft einem Admin um Hilfe",
	"<b>/einlad NAME -</b> Lädt Benutzer in eigene Raum ein oder zeigt die Einladungen",
	"<b>/quit TEXT -</b> beendet chat mit den Worten TEXT",
	"<b>/suche NAME -</b> Sucht alle Sprüche die das Wort NAME enthalten",
	"<b>=spruch (NAME ZUSATZ optional) -</b> gibt vordefinierten Spruch aus",
	"<b>*TEXT* -</b> Text ist kursiv", "<b>_TEXT_ -</b> Text ist fett",
	"<b>USER direkt ansprechen -</b> USER: oder @USER ergibt [USER:]",
	"<b>Für die ausführliche Hilfe mit allen Befehlen klicken Sie bitte auf [HILFE] unter der Eingabezeile.</b>");
	
	// Texte Benutzerlevel
	$level['C'] = "ChatAdmin";
	$level['S'] = "Superuser";
	$level['A'] = "Admin(temp)";
	$level['G'] = "Gast";
	$level['U'] = "Benutzer";
	$level['M'] = "Moderator";
	$level['B'] = "Besitzer";
	$level['Z'] = "Gesperrt";
	
	// Text Besitzer/Benutzerlevel Kurzform
	$leveltext['C'] = "Admin";
	$leveltext['S'] = "Admin";
	$leveltext['A'] = "Admin";
	$leveltext['G'] = "Gast";
	$leveltext['U'] = "";
	$leveltext['M'] = "Moderator";
	$leveltext['B'] = "Besitzer";
	
	// Texte Status Räume
	// Achtung: groß-kleinschreibung ist wichtig.
	$raumstatus1['O'] = "offen";
	$raumstatus1['G'] = "geschlossen";
	$raumstatus1['m'] = "moderiert";
	$raumstatus1['M'] = "Moderiert+geschl.";
	$raumstatus1['E'] = "Stiller Eingangsraum";
	$raumstatus2['T'] = "temporär";
	$raumstatus2['P'] = "permanent";
	
	// Text o_who - wo in der Community befindet sich der Benutzer
	$whotext[0] = "CHAT";
	$whotext[1] = "LOGIN";
	$whotext[2] = "FORUM";
	
	// Texte möglicher Namen für Gäste
	$gast_name = array(1 => "Urzel", "Murzel", "Hurzel", "Kurzel", "Wurzel",
		"Purzel");
	
	$hilfe_spruchtext = "<b>Format:</b> '=SPRUCH USER ZUSATZTEXT'<br><br>"
		. "Je nach Typ muss USER (=Name eines Benutzers im Chat) oder "
		. "ZUSATZTEXT (=freier Text) eingegeben werden:"
		. "<ul><li><b>Typ 0:</b> kein USER oder ZUSATZTEXT"
		. "<li><b>Typ 1:</b> nur USER oder stattdessen ZUSATZTEXT (je nach Sinn)"
		. "<br>Wenn ein USER angegeben wird, genügt <b>bob</b> um das per Nickergänzung auf <b>bobby</b> zu erweitern."
		. "Wird die Nickergänzung nicht gewünscht, oder ist im ZUSATZTEXT ein Leerzeichen enthalten, muss der Text in \" gesetzt werden."
		. "<li><b>Typ 2:</b> USER und ZUSATZTEXT"
		. "<br>Hier kann auch USER und ZUSATZTEXT Leerzeichen enthalten, wenn der Text in \" gesetzt wird, oder alternativ das Leerzeichen durch ein + ersetzt wird."
		. "</ul>In den Sprüchen wird immer <b>`0</b> durch den eigenen Benutzernamen ersetzt. "
		. "Zum weiteren Verständnis probieren Sie einfach die Sprüche aus.<br><br>"
		. "Um bestimmte Sprüche auszuwählen ist es am einfachsten, im Chat "
		. "mit dem /such Befehl und einem Stichwort nach dem gewünschten Spruch "
		. "zu suchen.<br>";

$t['fehler'] = "Fehler";
$t['seite_nicht_gefunden'] = "Die von Ihnen angeforderte Seite wurde nicht gefunden. Bitte überprüfen Sie die Adresse oder gehen Sie zurück auf die Startseite.";
$t['kein_zugriff'] = "Diese Seite bzw. dieser Bereich steht möglicherweise nur angemeldeten Benutzern zur Verfügung.";
?>