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
	"<b>Für die ausführliche Hilfe mit allen Befehlen klicken Sie bitte in der Navigation auf [HILFE].</b>");
	
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
	$gast_name = array(1 => "Urzel", "Murzel", "Hurzel", "Kurzel", "Wurzel", "Purzel");
	
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

$lang['fehler'] = "Fehler";
$lang['seite_nicht_gefunden'] = "Die von Ihnen angeforderte Seite wurde nicht gefunden. Bitte überprüfen Sie die Adresse oder gehen Sie zurück auf die Startseite.";
$lang['kein_zugriff'] = "Diese Seite bzw. dieser Bereich steht möglicherweise nur angemeldeten Benutzern zur Verfügung.";

$lang['benutzer_nachricht'] = "Nachricht";
$lang['benutzer_benutzerseite'] = "Benutzerseite";
$lang['benutzer_weiblich'] = "weiblich";
$lang['benutzer_maennlich'] = "männlich";

$lang['forum_keine_neuen_beitraege'] = "Keine neuen Beiträge";
$lang['forum_neue_beitraege'] = "Neue Beiträge";
$lang['forum_mehr_als_10_neue_beitraege'] = "Mehr als 10 neue Beiträge";
$lang['forum_thema_geschlossen'] = "Thema geschlossen";
$lang['forum_angepinntes_thema'] = "Angepinntes Thema";

$lang['functions_fehler_keine_datenbankverbindung'] = "FEHLER: Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es später noch einmal!<br>";

$lang['hinweis_fehler'] = "Fehler";
$lang['hinweis_erfolgreich'] = "Erfolgreich";
$lang['hinweis_hinweis'] = "Hinweis";
$lang['hinweis_hinweis_abwesend'] = "Der Benutzer ist momentan aus folgendem Grund abwesend:";

$lang['sql_update_fehlgeschlagen'] = "Update fehlgeschlagen";
$lang['sql_update_keine_aendern'] = "Update hat keine Zeilen aktualisiert";

$lang['sql_query_fehlgeschlagen'] = "Abfrage fehlgeschlagen";
$lang['sql_query_kein_ergebnis'] = "Abfrage ohne Ergebnis";

$lang['sperre1'] = "<b>Fehler:</b> Lobby kann nicht ermittelt werden!";
$lang['sperre2'] = "gibt Benutzer";
$lang['sperre3'] = "einen Schubs und wirft ihn/sie aus dem Raum!";
$lang['sperre4'] = "<b>Fehler:</b> Benutzer '%s_user_name%' ist nicht in diesem Raum";
$lang['sperre5'] = "den Zutritt frei.";
$lang['sperre6'] = "<b>Fehler:</b> Aus der Lobby kann nur ein Admin Benutzer herauswerfen!";
$lang['sperre7'] = "einen großen Schubs und wirft ihn/sie komplett aus dem Chat!";
$lang['sperre8'] = "einen Schubs und wirft ihn/sie in den Raum";

$lang['ignore1'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzer '%i_user_name_passiv%' wird ab jetzt ignoriert.";
$lang['ignore2'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzer '%i_user_name_passiv%' wird von Benutzer '%i_user_name_aktiv%' ab jetzt ignoriert.";
$lang['ignore3'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzer '%i_user_name_passiv%' wird nicht länger ignoriert.";
$lang['ignore4'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzer '%i_user_name_passiv%' wird von Benutzer '%i_user_name_aktiv%' nicht mehr ignoriert.";
$lang['ignore5'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Der Benutzer '%i_user_name_passiv%' ist Admin und kann nicht ignoriert werden!";

$lang['raum_user1'] = "<b>Benutzer im Raum %r_name%:</b> ";
$lang['raum_user12'] = "Da ist niemand.";

$lang['betrete_chat1'] = "Eintrittsnachricht";
$lang['betrete_chat2'] = "Willkommen";
$lang['betrete_chat3'] = "Topic";

$nachricht_b[] = "'%u_nick%' betritt den Raum %r_name%.";
$nachricht_b[] = "'%u_nick%' kommt in den Raum %r_name%.";
$nachricht_b[] = "&lt;KLOPF&gt; '%u_nick%' klopft an und tritt in den Raum %r_name% ein.";
$nachricht_b[] = "'%u_nick%' springt in den Raum %r_name%.";
$nachricht_b[] = "'%u_nick%' kommt in den Raum %r_name% gestürmt.";
$nachricht_b[] = "'%u_nick%' plumpst in den Raum %r_name%.";
$nachricht_b[] = "'%u_nick%' schleppt sich mühsam in den Raum %r_name%.";
$nachricht_b[] = "'%u_nick%' kommt in den Raum %r_name% geschlichen.";
$nachricht_b[] = "&lt;ZONG!&gt; '%u_nick%' beamt in den Raum %r_name%.";
$nachricht_b[] = "'%u_nick%' torkelt in den Raum %r_name%.";
$nachricht_b[] = "'%u_nick%' kommt in den  Raum %r_name% gekrochen.";
$nachricht_b[] = "&lt;KRACH!&gt; '%u_nick%' schlägt die Tür ein und betritt Raum %r_name%.";

$nachricht_v[] = "'%u_nick%' verlässt den Raum %r_name%.";
$nachricht_v[] = "'%u_nick%' springt aus dem Raum %r_name%.";
$nachricht_v[] = "'%u_nick%' stürmt aus dem Raum %r_name%.";
$nachricht_v[] = "'%u_nick%' drückt eine Träne heraus und verlässt den Raum %r_name%.";
$nachricht_v[] = "&lt;ZONG!&gt; '%u_nick%' beamt sich aus dem Raum %r_name%.";
$nachricht_v[] = "'%u_nick%' torkelt aus dem Raum %r_name%.";
$nachricht_v[] = "'%u_nick%' schwankt aus dem Raum %r_name%.";
$nachricht_v[] = "'%u_nick%' kriecht aus dem Raum %r_name%.";
$nachricht_v[] = "'%u_nick%' schleppt sich aus dem Raum %r_name%.";
$nachricht_v[] = "'%u_nick%' plumpst aus dem Raum %r_name%.";

$nachricht_vc[] = "'%u_nick%' verlässt den Raum %r_name% und den Chat.";
$nachricht_vc[] = "'%u_nick%' springt aus dem Raum %r_name% und dem Chat.";
$nachricht_vc[] = "'%u_nick%' stürmt aus dem Raum %r_name% und dem Chat.";
$nachricht_vc[] = "'%u_nick%' drückt eine Träne heraus und verlässt den Raum %r_name% und den Chat.";
$nachricht_vc[] = "&lt;ZONG!&gt; '%u_nick%' beamt sich aus dem Raum %r_name% und dem Chat.";
$nachricht_vc[] = "'%u_nick%' torkelt aus dem Raum %r_name% und dem Chat.";
$nachricht_vc[] = "'%u_nick%' schwankt aus dem Raum %r_name% und dem Chat.";
$nachricht_vc[] = "'%u_nick%' kriecht aus dem Raum %r_name% und dem Chat.";
$nachricht_vc[] = "'%u_nick%' schleppt sich aus dem Raum %r_name% und dem Chat.";
$nachricht_vc[] = "'%u_nick%' plumpst aus dem Raum %r_name% und dem Chat.";

$lang['raum_gehe1'] = "Austrittsnachricht";
$lang['raum_gehe2'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Zur Info: Sie sind aus %r_name_neu% ausgesperrt.";
$lang['raum_gehe3'] = "Eintrittsnachricht";
$lang['raum_gehe4'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Raum %r_name_neu% kann nicht betreten werden!";
$lang['raum_gehe5'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Sie sind leider aus %r_name_neu% ausgesperrt.";
$lang['raum_gehe6'] = "Topic";
$lang['raum_gehe7'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Sie haben leider zu wenig Punkte (mind. %r_min_punkte% nötig) um Raum %r_name_neu% zu betreten.";
$lang['raum_gehe8'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Als Gast können Sie den Raum %r_name_neu% nicht betreten, da hierzu Punkte notwendig sind, die Sie als Gast nicht bekommen können. Melden Sie sich hierzu an.";

$lang['chat_msg1'] = "Der Befehl '%chatzeile%' ist nur für Admins!";
$lang['chat_msg4'] = "Sprüche gefunden:";
$lang['chat_msg5'] = "<b>Fehler:</b> Bitte mindestens 3 Buchstaben als Suchwort abgeben!";
$lang['chat_msg6'] = "'s letzte Worte:";
$lang['chat_msg7'] = "<b>Fehler:</b> Sie können sich nicht selbst ignorieren!";
$lang['chat_msg9'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzer '%u_nick%' ignoriert niemanden.";
$lang['chat_msg10'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> '%u_nick%' ignoriert ";
$lang['chat_msg11'] = "<b>Fehler:</b> Raum '%chatzeile%' existiert nicht und ein neuer Raum kann nicht geöffnet werden!";
$lang['chat_msg12'] = "<b>Räume:</b>";
$lang['chat_msg13'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Der Benutzer '%s_user_name%' ist ein Admin und es ist nicht nett, Admins zu kicken!";
$lang['chat_msg14'] = "<b>Fehler:</b> Benutzer '%chatzeile%' existiert nicht!";
$lang['chat_msg15'] = "<b>Fehler:</b> Keine Rechte um jemanden aus diesem Raum zu werfen!";
$lang['chat_msg16'] = "<b>In diesem Raum gesperrte Benutzer:</b>";
$lang['chat_msg17'] = "In diesem Raum ist niemand gesperrt.";
$lang['chat_msg21'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> <span style=\"color:#%u_farbe%;\">Neue Farbe ist '%u_farbe%'.</span>";
$lang['chat_msg22'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Aktuelle Farbe ist '%u_farbe%'.";
$lang['chat_msg23'] = "<b>Fehler:</b> Bitte Farbe als RRGGBB (Rot-Grün-Blau) in Hexadezimal eingeben!";
$lang['chat_msg24'] = "flüstert an";
$lang['chat_msg25'] = "<b>Fehler:</b> Benutzer '%chatzeile%' ist nicht online!";
$lang['chat_msg26'] = "<b>Fehler:</b> Benutzer '%chatzeile%' ist nicht online oder existiert nicht!";
$lang['chat_msg32'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzername wurde nicht ergänzt: <b>%nickkurz%</b> ist nicht eindeutig, im Chat sind: ";
$lang['chat_msg33'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Bitte geben Sie für die Suche mit /whois mindestens 3 Buchstaben ein (% ist Joker).";
$lang['chat_msg34'] = "<b>%user%</b> packt den Würfelbecher und wirft mit %wuerfel%: ";
$lang['chat_msg35'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Beispiel: <b>/dice 3W6</b> wirft drei sechsseitige Würfel.";
$lang['chat_msg36'] = "<b>Fehler: Spruch existiert schon: </b>";
$lang['chat_msg37'] = "<b>Fehler:</b> Aufruf mit <b>/spruch spruchname anzahl_parameter &lt;Spruch&gt;<br>Beispiel:</b> /spruch lach 2 &lt;`0 lacht über `2 von `1.&gt;";
$lang['chat_msg38'] = "<b>Fehler: &lt;anzahl_parameter&gt;</b> muss zwischen 0 und 2 liegen.";
$lang['chat_msg39'] = "<b>Fehler:</b> Das Zeichen <b>`</b> darf nur als <b>`0</b>, <b>`1</b>, <b>`2</b> verwendet werden.";
$lang['chat_msg40'] = "<b>Spruch eingetragen.</b> Aufruf mit =%spruch%";
$lang['chat_msg41'] = "<b>Dateifehler beim Eintragen des Spruches aufgetreten. Sorry.</b>";
$lang['chat_msg42'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzername wurde nicht ergänzt: <b>%nickkurz%</b> ist nicht eindeutig, online in diesem Raum sind: ";
$lang['chat_msg43'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzername wurde nicht ergänzt: <b>%nickkurz%</b> ist nicht eindeutig, im Chat existieren: ";
$lang['chat_msg44'] = "<b>Fehler:</b> Benutzer '%chatzeile%' ist nicht in diesem Raum!";
$lang['chat_msg45'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> alle doppelten IPs wurden gelistet.";
$lang['chat_msg46'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> keine doppelten IPs gefunden.";
$lang['chat_msg47'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Die Nachricht wurde an die Admins weitergeleitet.";
$lang['chat_msg48'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Die Nachricht konnte nicht weitergeleitet werden: kein Admin im Chat :-(";
$lang['chat_msg49'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Benutzer %user% aus Raum %raum% ruft um Hilfe:</b>";
$lang['chat_msg50'] = "alle Admins";
$lang['chat_msg51'] = "CHAT-Admins";
$lang['chat_msg52'] = "an";
$lang['chat_msg53'] = "Der Raum '%raumname%' existiert nicht.";
$lang['chat_msg54'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Bitte nicht mehr als $smilies_anzahl Smilies auf einmal auswählen.";
$lang['chat_msg55'] = "<b>Fehler:</b> Diese Funktion ist für Gäste nicht erlaubt. Bitte melden Sie sich mit Ihrem registrierten Benutzernamen an.";
$lang['chat_msg56'] = "'s Raum";
$lang['chat_msg64'] = "E-Mail";
$lang['chat_msg65'] = "Admin E-Mail";
$lang['chat_msg66'] = "Onlinezeit";
$lang['chat_msg67'] = "Raum";
$lang['chat_msg68'] = "Letzter Login";
$lang['chat_msg76'] = "<b>Fehler:</b> In diesem Raum sind keine Smilie-Grafiken erlaubt.";
$lang['chat_msg77'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Die Nachricht '%nachricht%' wurde nicht zugestellt.";
$lang['chat_msg78'] = "flüstert an alle";
$lang['chat_msg79'] = "<b>Fehler:</b> Es ist kein anderer Benutzer online.";
$lang['chat_msg82'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> '%u_nick%' wird von folgenden Benutzern ignoriert: ";
$lang['chat_msg90'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> %u_nick% ist online im %raum%";
$lang['chat_msg91'] = "%u_nick%\nonline %online% Std/Min/sek";
$lang['chat_msg92'] = "[online %online% Std/Min/sek]";
$lang['chat_msg93'] = "[offline]";
$lang['chat_msg94'] = "[offline, letzter Login %login%]";
$lang['chat_msg100'] = "(Chatinfo) ";
$lang['chat_msg101'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Sie werden nach %zeit% Minuten automatisch aus dem $chat ausgeloggt, wenn Sie nichts schreiben!";
$lang['chat_msg104'] = "Die Nachricht/E-Mail wurde nicht verschickt: Mailbombing-Schutz aktiv!";
$lang['chat_msg106'] = "Derzeit liegen keine System- oder Privat-Nachrichten vor.<br>";
$lang['chat_msg108'] = "<b>Fehler: </b> Um neue Räume anlegen zu dürfen, brauchen Sie mindestens %punkte% Punkte oder Sie müssen Admin sein!";
$lang['chat_msg109'] = "Sie werden von '%nick%' ignoriert oder Sie ignorieren '%nick%' und können dem Benutzer daher keine privaten Nachrichten schicken";
$lang['chat_msg114'] = "Heute ist der %datum% um %uhrzeit% Uhr Chat-Zeit.";
$lang['freunde_fehlermeldung_ignoriert'] = "Der Benutzer '%u_nick%' kann nicht als Freund hinzugefügt werden, da Sie von diesem Benutzer ignoriert werden!<br>";
$lang['freunde_fehlermeldung_gesperrt'] = "Der Benutzer '%u_nick%' kann nicht als Freund hinzugefügt werden, da dieser Benutzer gesperrt ist!<br>";
$lang['freunde_fehlermeldung_gast'] = "Der Benutzer '%u_nick%' kann nicht als Freund hinzugefügt werden, da dieser Benutzer ein Gast ist!<br>";
$lang['chat_msg119'] = "Aufruf: /ip username oder /ip www.xxx.yyy.zzz";
$lang['chat_msg120'] = "Benutzer %u_nick% ist nicht online!";
$lang['chat_msg121'] = "Keinen Benutzer mit passender IP gefunden!";
$lang['chat_msg122'] = "Benutzer online mit der IP %ip% (%datum%, %uhrzeit%) ";
$lang['chat_msg123'] = "Benutzer war mit IP %ip% da %datum% ";

$lang['chat_spruch1'] = "<b>Fehler:</b> den Spruch '%spruchname%' gibt es nicht als Typ 2 mit '%spruchname% USERNAME ZUSATZTEXT'. "
	. "Der Aufruf eines Spruchs ist immer bei Sprüchen des<br>"
	. "<b>Typ <I>0</I>: 'SPRUCHNAME'</b>, bei<br><b>Typ <I>1</I>: "
	. "'SPRUCHNAME USERNAME'</b> und bei<br><b>Typ <I>2</I>: "
	. "'SPRUCHNAME USERNAME ZUSATZTEXT'</b>";
$lang['chat_spruch2'] = "<b>Fehler:</b> den Spruch '%spruchname%' gibt es nicht als Typ 1 mit '%spruchname% USERNAME'. "
	. "Der Aufruf eines Spruchs ist immer bei Sprüchen des<br>"
	. "<b>Typ <I>0</I>: 'SPRUCHNAME'</b>, bei<br><b>Typ <I>1</I>: "
	. "'SPRUCHNAME USERNAME'</b> und bei<br><b>Typ <I>2</I>: "
	. "'SPRUCHNAME USERNAME ZUSATZTEXT'</b>";
$lang['chat_spruch3'] = "<b>Fehler:</b> Der Befehl oder Spruch '%spruchname%' " . "konnte nicht gefunden werden, %u_nick%.";
$lang['chat_spruch4'] = "Variationen dieses Spruchs:";
$lang['chat_spruch5'] = "<b>Fehler:</b> Befehl '%chatzeile%' existiert nicht! Sprüche werden mit =spruch abgerufen.";
$lang['chat_spruch6'] = "zu";

$lang['chat_lese1'] = "privat";

$lang['benutzer_online'] = "Benutzer %user% (ONLINE)";
$lang['benutzer_offline'] = "Benutzer %user% (OFFLINE)";
$lang['benutzer_loeschen'] = "löschen";

$lang['benutzer_punkte'] = "Punkte";
$lang['benutzer_punkte_anzeige'] = "(Alle/%jahr%/%monat%)";

$lang['user_kein_bild_hochgeladen'] = "<b>Kein Bild hochgeladen - Achtung: Nur Bilder im JPG, GIF und PNG Format hochladen! Kein BMP Format! Die Bilder dürfen nicht grösser als 60 Kb sein!</b><br>";

$lang['away1'] = "ist gerade nicht da: ";
$lang['away2'] = "ist wieder da. ";

$lang['knebel1'] = "Geknebelte Benutzer";
$lang['knebel2'] = "Aufruf: %chatzeile% [nick] [minuten]";
$lang['knebel3'] = "'%admin%' winkt freundlich in die Runde und fordert '%user%' auf, doch auch wieder mal etwas zu sagen.";
$lang['knebel4'] = "'%admin%' blickt besonders giftig zu '%user%'. '%user%' ist daraufhin vor Schreck für %zeit% Minuten sprachlos.";
$lang['knebel5'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Der Benutzer '%admin%' ist ein Admin und es gehört sich nicht, Admins zu knebeln.";
$lang['knebel6'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Du bist vor Schreck noch %zeit% sprachlos.";
$lang['knebel7'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Du verschluckst Dich und bist vor Schreck sprachlos.";
$lang['knebel8'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> %user% wurde im Raum %raum% geknebelt <b>(Autoknebel)</b>.";
$lang['knebel9'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Text:";
$lang['knebel10'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Private Textnachricht - Knebelgrund:";

$lang['invite1'] = "In diesem Raum sind eingeladen:";
$lang['invite2'] = "In diesem Raum ist niemand eingeladen.";
$lang['invite3'] = "'%admin%' hat '%user%' in den Raum '%raum%' eingeladen.";
$lang['invite4'] = "'%admin%' hat '%user%' aus dem Raum '%raum%' ausgeladen.";
$lang['invite5'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> '%admin%' hat Sie in den Raum '%raum%' eingeladen. Mit '<b>/raum %raum%</b>' gelangen Sie dorthin. Wenn Sie 60 Sekunden warten, können Sie über die <b>Raumauswahl</b> (Anderen Raum betreten) in den Raum wechseln.";

$lang['moderiert1'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Diese Funktion ist einem moderierten Raum nicht verfügbar, wenn ein Moderator anwesend ist.";
$lang['moderiert2'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Ihre Eingabe wurde an den Moderator weitergeleitet.";

$lang['moderiertdel1'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Ihre Eingabe wurde vom Moderator gelöscht: Diese Frage wurde (sinngemäß) bereits gestellt.";
$lang['moderiertdel2'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Ihre Eingabe wurde vom Moderator gelöscht: Bitte freundlicher formulieren.";
$lang['moderiertdel3'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Ihre Eingabe wurde vom Moderator gelöscht: Wir können leider nicht alle Fragen in der kurzen Zeit beantworten.";
$lang['moderiertdel4'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Ihre Eingabe wurde vom Moderator gelöscht.";

$lang['profil1'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Sie haben Ihr <a %link%>Profil</a> noch nicht ausgefüllt! Bitte klicken Sie &gt&gt<a %link%>HIER</a>&lt&lt";

$lang['chatmsg_mail1'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Sie haben neue Nachrichten. <a %link%>Gleich lesen?</a>";
$lang['chatmsg_mail2'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Nachricht von '%nick%' am %zeit%: %betreff%";
$lang['chatmsg_freunde1'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Ein Freund ist da: %u_nick% betritt Raum %raum%";
$lang['chatmsg_freunde2'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Ein Freund geht weg: %u_nick% verlässt den Chat";
$lang['chatmsg_freunde5'] = "<span class=\"fa-solid fa-circle-info icon16\"></span><span> Ein Freund ist da: %u_nick% betritt das Forum";

$lang['nachricht_mail5'] = "Hallo %user%, im Chat sind folgende Freunde online:\n\n";
$lang['nachricht_mail6'] = "%anzahl% Freunde sind online im Chat";
$lang['nachricht_mail6a'] = "%anzahl% Freund ist online im Chat";
$lang['nachricht_mail9'] = "Hallo, über Ihre Freunde im Chat gibt es etwas neues:\n\n";
$lang['nachricht_freunde3'] = "%u_nick% betritt Raum %raum%";
$lang['nachricht_freunde4'] = "%u_nick% verlässt den Chat";
$lang['nachricht_freunde6'] = "%u_nick% betritt das Forum";

$lang['email_mail3'] = "[Mail-Weiterleitung aus dem $chat]\n\n";
$lang['email_mail4'] = "Der Benutzer \"%name%\" hat folgende Nachricht gesendet:<br><br>
%nachricht%<br><br>
E-Mail Adresse: %email%<br>
Adresse des Chats: $chat_url";
$lang['email_mail5'] = "Hallo %user%, im $chat sind folgende Freunde online:\n\n";
$lang['email_mail6'] = "%anzahl% Freunde sind online im $chat";
$lang['email_mail6a'] = "%anzahl% Freund ist online im $chat";
$lang['email_mail7'] = "<b>$chat:</b> Sie haben eine neue Nachricht von '%nick%' erhalten: %betreff%";
$lang['email_mail8'] = "Hallo %user%, über Ihre Freunde im $chat gibt es etwas neues:\n\n";
$lang['email_freunde3'] = "%u_nick% betritt Raum %raum% im $chat";
$lang['email_freunde4'] = "%u_nick% verlässt den $chat";
$lang['email_freunde6'] = "%u_nick% betritt das Forum im $chat";

$lang['punkte1'] = "<b>$chat:</b> %text% Ihnen wurden %punkte% Punkte gutgeschrieben!";
$lang['punkte2'] = "<b>$chat:</b> %text% Ihnen wurden %punkte% Punkte abgezogen!";
$lang['punkte3'] = "Braver Benutzer! Großes Lob von %user%: ";
$lang['punkte4'] = "Böser Benutzer! Sie wurden von %user% getadelt: ";
$lang['punkte5'] = "Braver Benutzer! %user1% hat %user2% %punkte% Punkte gutgeschrieben!";
$lang['punkte6'] = "Böser Benutzer! %user1% hat %user2% %punkte% Punkte abgezogen!";
$lang['punkte7'] = "<b>Fehler:</b> Bitte mindestens einen Punkt vergeben!";
$lang['punkte8'] = "%user1% hat %user2% %punkte% Punkte gutgeschrieben!";
$lang['punkte9'] = "%user1% hat %user2% %punkte% Punkte abgezogen!";
$lang['punkte10'] = "<b>Fehler:</b> Sie können sich selbst keine Punkte geben oder abziehen!";
$lang['punkte11'] = "<b>Fehler:</b> Sie können nicht mehr als 1000 vergeben!";
$lang['punkte12'] = "%user% haut sich eines mit der Peitsche über und verliert einen Punkt!";
$lang['punkte13'] = "%user% denkt über den Sinn des Lebens nach und zieht sich %punkte% Punkte ab!";
$lang['punkte14'] = "%user% gibt sich mit der Peitsche %punkte% Schläge auf den Rücken und verliert %punkte% Punkte!";
$lang['punkte15'] = "%user% peitscht sich kräftig aus und verliert %punkte% Punkte!";
$lang['punkte16'] = "%user% peitscht sich ekstatisch aus und verliert %punkte% Punkte!";
$lang['punkte17'] = "%user% peitscht sich ekstatisch aus und verliert %punkte% Punkte (mehr, mehr, mehr...)!";
$lang['punkte18'] = "%user% geht in ein Bordell und verliert %punkte% Punkte!";
$lang['punkte19'] = "%user% geht in ein Casino und verliert %punkte% Punkte!";
$lang['punkte20'] = "%user% muss zum Finanzamt und verliert %punkte% Punkte!";
$lang['punkte21'] = "%user% wurden %punkte% Punkte gutgeschrieben!";
$lang['punkte22'] = "%user% wurden %punkte% Punkte abgezogen!";
$lang['punkte23'] = "Einem Gast können keine Punkte gutgeschrieben werden!";

//Texte für automatische generierung des Beitrags
$lang['kopfzeile'] = "{autor} schrieb am {date}";
$lang['gruss'] = "Mit freundlichen Grüßen";
$lang['betrete_forum1'] = "<br><b>Willkommen im Forum, %u_nick%!</b>";
$lang['betreff_new_posting'] = "Neue Antwort auf Ihren Beitrag %po_titel%";
$lang['msg_new_posting_chatmail'] = "Es liegt eine neue Antwort auf Ihren Beitrag <b>%po_titel%</b> vom %po_ts% vor. <br><br><b>Pfad: </b>%forum% -> %thema%<br><br><b>Autor:</b> %user_from_nick%, <b>Titel:</b> \"%po_titel_antwort%\" vom %po_ts_antwort%<br><br>";
$lang['msg_new_posting_email'] = "Es liegt eine neue Antwort auf Ihren Beitrag %po_titel% vom %po_ts% vor.\n\nPfad: %forum% -> %thema%\n\nAutor: %user_from_nick%, Titel: \"%po_titel_antwort%\" vom %po_ts_antwort%\n";
$lang['msg_new_posting_olm'] = "Es liegt eine <b>neue Antwort</b> auf Ihren Beitrag <b>%po_titel%</b> vom %po_ts% (Forum: %forum%, Thema: %thema%) vor. <b>Autor:</b> %user_from_nick%, <b>Titel:</b> \"%po_titel_antwort%\" <b>vom</b> %po_ts_antwort%";

$lang['nachrichten_posteingang_geschlossen'] = "Ihr Posteingang ist geschlossen!";
$lang['nachrichten_posteingang_geschlossen_text'] = "Bitte löschen Sie einfach diese E-Mail, wenn Sie wieder Nachrichten empfangen möchten!";
?>