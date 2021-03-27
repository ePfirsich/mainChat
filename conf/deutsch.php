<?php
// Sprachdefinition deutsch

setlocale(LC_TIME, "de_DE");

// Kurzer Hilfstext
$hilfstext = array(
    1 => "<B>/user RAUM -</B> listet alle User im Raum (*=alle Räume)",
    "<B>/raum RAUM -</B> wechselt in RAUM (legt ggf. RAUM neu an), ohne Angabe von RAUM werden alle Räume gelistet",
    "<B>/zeige NAME-</B> Gibt die Benutzerinformationen für NAME aus (%=Joker)",
    "<B>/msg NAME TEXT -</B> TEXT an User NAME flüstern",
    "<B>/me TEXT -</B> Spruch im Raum",
    "<B>/weg TEXT-</B> setzt oder löscht einen ich-bin-nicht-da Text",
    "<B>/nick NAME -</B> Setzt Nickname auf NAME",
    "<B>/ignore NAME -</B> Ignoriert User NAME, ohne Angabe von NAME werden alle ignorierten User gezeigt",
    "<B>/kick NAME -</B> Sperrt User NAME aus dem aktuellen Raum aus (oder Freigabe)",
    "<B>/farbe RRGGBB -</B> Setzt neue Farbe auf RRGGBB, ohne Angabe von RRGGBB wird die aktuelle Farbe angezeigt",
    "<B>/op TEXT -</B> ruft einem Admin um Hilfe",
    "<B>/einlad NAME -</B> Läd User in eigene Raum ein oder zeigt die Einladungen",
    "<B>/quit TEXT -</B> beendet chat mit den Worten TEXT",
    "<B>/suche NAME -</B> Sucht alle Sprüche die das Wort NAME enthalten",
    "<B>=spruch (NAME ZUSATZ optional) -</B> gibt vordefinierten Spruch aus",
    "<B>*TEXT* -</B> Text ist kursiv", "<B>_TEXT_ -</B> Text ist fett",
    "<B>USER direkt ansprechen -</B> USER: oder @USER ergibt [USER:]",
    "<B>Für die ausführliche Hilfe mit allen Befehlen klicken Sie bitte auf [HILFE] unter der Eingabezeile.</B>");

// Texte Userlevel 
$level['C'] = "ChatAdmin";
$level['S'] = "Superuser";
$level['A'] = "Admin(temp)";
$level['G'] = "Gast";
$level['U'] = "User";
$level['M'] = "Moderator";
$level['B'] = "Besitzer";
$level['Z'] = "Gesperrt";

// Text Besitzer/Userlevel Kurzform
$leveltext['C'] = "Admin";
$leveltext['S'] = "Admin";
$leveltext['A'] = "Admin";
$leveltext['G'] = "Gast";
$leveltext['U'] = "";
$leveltext['M'] = "Moderator";
$leveltext['B'] = "Besitzer";

// Texte Status Räume
// achtung: groß-kleinschreibung ist wichtig.
$raumstatus1['O'] = "offen";
$raumstatus1['G'] = "geschlossen";
$raumstatus1['L'] = "Teergrube";
$raumstatus1['m'] = "moderiert";
$raumstatus1['M'] = "Moderiert+geschl.";
$raumstatus1['E'] = "Stiller Eingangsraum";
$raumstatus2['T'] = "temporär";
$raumstatus2['P'] = "permanent";

// Text o_who - wo in der Community befindet sich der User
$whotext[0] = "CHAT";
$whotext[1] = "LOGIN";
$whotext[2] = "FORUM";

// Texte möglicher Namen für Gäste
$gast_name = array(1 => "Urzel", "Murzel", "Hurzel", "Kurzel", "Wurzel",
    "Purzel");

$hilfe_spruchtext = "<B>Format:</B> '=SPRUCH USER ZUSATZTEXT'<BR><BR>"
    . "Je nach Typ muss USER (=Name eines Users im Chat) oder "
    . "ZUSATZTEXT (=freier Text) eingegeben werden:"
    . "<UL><LI><B>Typ 0:</B> kein USER oder ZUSATZTEXT"
    . "<LI><B>Typ 1:</B> nur USER oder stattdessen ZUSATZTEXT (je nach Sinn)"
    . "<BR>Wenn ein USER angegeben wird, genügt <b>bob</b> um das per Nickergänzung auf <b>bobby</b> zu erweitern."
    . "Wird die Nickergänzung nicht gewünscht, oder ist im ZUSATZTEXT ein Leerzeichen enthalten, muss der Text in \" gesetzt werden."
    . "<LI><B>Typ 2:</B> USER und ZUSATZTEXT"
    . "<BR>Hier kann auch USER und ZUSATZTEXT Leerzeichen enthalten, wenn der Text in \" gesetzt wird, oder alternativ das Leerzeichen durch ein + ersetzt wird."
    . "</UL>In den Sprüchen wird immer <B>`0</B> durch den eigenen Nicknamen ersetzt. "
    . "Zum weiteren Verständnis probieren Sie einfach die Sprüche aus.<BR><BR>"
    . "Um bestimmte Sprüche auszuwählen ist es am einfachsten, im Chat "
    . "mit dem /such Befehl und einem Stichwort nach dem gewünschten Spruch "
    . "zu suchen.<BR>";
?>
