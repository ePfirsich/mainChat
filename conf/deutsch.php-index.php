<?php
// Sprachdefinition deutsch index.php

$t['menue1'] = "$chat";
$t['menue2'] = "Login";
$t['menue3'] = "Registrierung";
$t['menue4'] = "Hilfe";
$t['menue5'] = "Liste aller Befehle";
$t['menue6'] = "Liste aller Sprüche";
$t['menue7'] = "Punkte/Community";
$t['menue8'] = "Chatiquette";
$t['menue9'] = "Nutzungsbestimmungen";
$t['menue10'] = "Datenschutzerklärung";

$t['hilfe0'] = "Übersicht über alle Befehle im Chat";
$t['hilfe1'] = "<b>Allgemeines Format:</b> /BEFEHL OPTION OPTION...";
$t['hilfe3'] = "Hilfe zu den voreingestellten Sprüchen";
$t['hilfe4'] = "Übersicht über alle voreingestellten Sprüche";
$t['hilfe6'] = "Hilfe";
$t['hilfe8'] = "Zusätzliche Befehle für Admins";
$t['hilfe9'] = "Chatiquette";
$t['hilfe11'] = "Community: Punkte, Profil, Mail, Freunde und Homepage";
$t['hilfe14'] = "Nutzungsbestimmungen";
$t['hilfe17'] = "Befehl";
$t['hilfe18'] = "Funktion";
$t['hilfe19'] = "Aliase";
$t['hilfe20'] = "Anmerkungen";
$t['hilfe21'] = "Spruch";
$t['hilfe22'] = "Typ";
$t['hilfe23'] = "Text";

// Ausführlicher Hilfstext für die interaktive Hilfe
$hilfe_befehlstext = array(
	1 => "/user\tListet alle Benutzer im aktuellen Raum\t/wer, /who, /w, /list",
	"/user RAUM\tListet alle Benutzer im Raum RAUM auf (*=Alle)\t/wer, /who, /w, /list\tFalls RAUM=*, werden alle Räume gezeigt.",
	"/raum\tListet alle offenen Räume auf\t/channel, /go",
	"/raum RAUM\tWechselt in RAUM. Falls RAUM nicht existiert, wird er als temporärer Raum neu angelegt\t/channel, /go",
	"/raum RAUM !\tWechselt in RAUM, auch wenn der Raum geschlossen ist (nur Admins und Raumbesitzer)\tforce, immer\tBitte Privatsphäre beachten!",
	"/people\tListet alle offenen Räume mit Benutzern auf.",
	"/weg\tSetzt oder löscht einen ich-bin-nicht-da Text.\t/away\tBeispiel: /away ich telefoniere",
	"/msg NAME TEXT\tTEXT an Benutzer NAME flüstern\t/talk, /tell, /t\tDie Übermittlung ist privat und kann von niemandem mitgelesen werden",
	"/msgf TEXT\tTEXT an alle Freunde flüstern\t/tf",
	"/mail NAME TEXT\tSchreibt Mail mit dem Inhalt TEXT an den Benutzer NAME\t/m\tDer Betreff entspricht den ersten Worten",
	"/me TEXT\tSpruch an alle im Raum<br>Kommentar in die Runde\t/txt\tDer Text wendet sich nicht an einen bestimmten Benutzer, sondern ist für alle im Raum bestimmt",
	"/op TEXT\tRuft einen Admin\t \tDer Text wird an die Admins geschickt, die gerade online sind",
	"/nick NAME\tSetzt Benutzername auf NAME\t/name\tDer neue Benutzername wird dauerhaft gespeichert",
	"/ignoriere\tZeigt die ignorierten Benutzer an\t/ignore, /ig",
	"/ignoriere NAME\tIgnoriert Benutzer NAME\t/ignore, /ig\tNochmalige Eingabe gibt den Benutzer NAME wieder frei",
	"/kick NAME\tSperrt Benutzer NAME aus dem aktuellen Raum dauerhaft aus\t/plop\tNochmalige Eingabe gibt den Eintritt für diesen Benutzer wieder frei",
	"/quit TEXT\tBeendet den Chat mit den Worten TEXT (logoff).\t/exit, /ende\tDie Angabe von TEXT ist optional",
	"/hilfe\tListet die Kurzhilfe auf\t/help, /?",
	"/freunde NAME\tMacht NAME zu meinem Freund\t/freund, /buddy\tNochmalige Eingabe nimmt NAME aus der Freundesliste",
	"/einlad NAME\tLädt einen Benutzer in den Raum ein\t/invite\tNur für Admins oder Raumbesitzer",
	"/uebergeberaum NAME\tÜbergibt den aktuellen Raum an den Benutzer NAME.\t/schenke\tNur für Admins oder Raumbesitzer",
	"/aendereraum STATUS\tÄndert den Status des aktuellen Raums auf offen, geschlossen oder moderiert.\t \tNur für Admins oder Raumbesitzer",
	"/loescheraum RAUM\tLöscht den angegebenen Raum\t \tNur für Admins oder Raumbesitzer",
	"/zeige NAME\tGibt die Benutzerinformationen für den Benutzer NAME aus <br>(% ist Joker).\t/whois",
	"/wuerfel\tWürfel werfen\t/dice\t/wuerfel 2W6 <br>wirft 2 sechseitige Würfel",
	"/dicecheck\tPrüft Würfelwürfe auf Korrektheit\t/quiddice\t/dicecheck 2W6 <br>prüft auf 2 sechseitige Würfel",
	"/suche NAME\tSucht alle Sprüche, die das Wort NAME enthalten\t/such\tGut geeignet, um einen Spruch passend zum Thema zu finden",
	"=spruch NAME ZUSATZ\tGibt einen vordefinierten Spruch aus (Siehe \"Liste aller Sprüche\")\t \tNAME und ZUSATZ ist optional und vom Spruch abhängig. Weitere Info vorhanden",
	"*TEXT*\tTEXT wird kursiv dargestellt\t \tWirkt in allen Texten, die öffentlich oder privat im Chat ausgegeben werden",
	"_TEXT_\tTEXT wird fett dargestellt\t \tWirkt in allen Texten, die öffentlich oder privat im Chat ausgegeben werden",
	"/time \tGibt die aktuelle Uhrzeit aus",
	"/farbe\tZeigt die aktuelle Farbe an\t/color",
	"/farbe RRGGBB\tSetzt neue Farbe auf RRGGBB\t/color\tRR steht für Rot, GG für Grün und BB für Blau, jeweils von 00 bis FF",
	"/farbealle \tSetzt/zeigt die Farbe für andere Benutzer an\t/color2\tWerte in RRGGBB wie bei /farbe; abschalten mit \"/farbealle -\"",
	"/farbenoise \tSetzt/zeigt die Farbe für Noises\t/color3\tWerte in RRGGBB wie bei /farbe; abschalten mit \"/farbenoise -\"",
	"/farbepriv \tSetzt/zeigt die Farbe für private Nachrichten\t/color4\tWerte in RRGGBB wie bei /farbe; abschalten mit \"/farbepriv -\"",
	"/farbesys \tSetzt/zeigt die Farbe für Systemnachrichten im Chat\t/color5\tWerte in RRGGBB wie bei /farbe; abschalten mit \"/farbesys -\"",
	"/farbebg \tSetzt/zeigt die Farbe für den Hintergrund. Änderung wird erst nach neuem Laden aktiv.\t/color6\tWerte in RRGGBB wie bei /farbe; abschalten mit \"/farbebg -\"",
	"/farbset X\tSetzt das vordefinierte Farbset X. \t \tDie möglichen Farbsets werden durch /farbset angezeigt. Beispiel: /farbset 3",
	"/farbreset \tSetzt die Farbspielereien wieder zurück auf den Chat-Default",
	"/clearedit \tEingabezeile nach Return löschen oder stehen lassen\t \tNochmalige Eingabe schaltet wieder zurück");

$hilfe_befehlstext_admin_ok = 1;
$hilfe_befehlstext_admin = array(
	1 => "/besitzeraum RAUM\tÜbernimmt Besitz von RAUM\t/besitze\tNur für Admins",
	"/raum RAUM !\twechselt in RAUM, auch wenn der Raum geschlossen ist (nur Admins und Raumbesitzer)\t force, immer\tBitte Privatsphäre beachten!",
	"/schubs USER [raum]\tbefördert USER in RAUM\t\tNur für Admins",
	"/knebel\tKnebelt einen Benutzer\t/gag\t/gag idiot 10",
	"/gaga\tWie /gag, jedoch Anzeige aller im Chat geknebelten, nicht nur im eigenen Raum",
	"/einlad\tBearbeitet die geladenen Benutzer\t/invite\t/invite dilbert",
	"/zeige\tZeigt erweiterte Daten eines Benutzers (% ist Joker)\t/whois\t/whois dilbert",
	"/analle TEXT\tTEXT an alle Benutzer flüstern, die online sind\t/toall\tNur für Admins",
	"/op\tPrivatnachricht an alle eingeloggten Admins\t\t/op tach.",
	"/oplist\tAuflisten aller eingeloggten Admins\t\t/oplist",
	"/lob USER PUNKTE\tLobt Benutzer: Er erhält Punkte gutgeschrieben\tDie Angabe von PUNKTE ist optional\t",
	"/tadel USER PUNKTE\tTadelt Benutzer: Er erhält Punkte abgezogen\tDie Angabe von PUNKTE ist optional\t/bestraf",
	"/blacklist USER\tSetzt Benutzer auf die Blacklist oder löscht ihn\t\t/blackliste",
	"/ip USER<br>/ip w.x.y.z\tZeigt die Benutzer zu einer IP\t\t",
	"/dupes\tZeigt doppelt eingeloggte Benutzer\t\t/dupes");

$hilfe_uebersichtstext = "<p><b>Erste Hilfe bei Problemen:</b><br>"
	. "Falls die <b>Ausgabe hängt</b> oder andere Fehler auftreten, können Sie mit "
	. "<b>[RESET]</b> "
	. "alle Fenster des Chats neu laden.</p>" . "<p><b>Popup-Blocker:</b><br>"
	. "Bitte beachten Sie, dass Popup-Blocker die Funktionalität des Chats beeinträchtigen können "
	. "und bei eingeschaltetem Popup-Blocker einige Funktionen nicht nutzbar sind. "
	. "Wir empfehlen Ihnen daher für den Chat den Popup-Blocker zu deaktivieren.</p>"
	. "<p><b>JavaScript:</b><br>"
	. "Bitte beachten Sie, dass die Bedienung des Chats mit JavaScript einfacher ist und schalten "
	. "Sie dieses bitte gegebenenfalls in Ihrem Browser ein.</p>"
	. "<p><b>Befehle:</b><br>"
	. "Die meisten Einstellungen können durch <a href=\"index.php?&aktion=hilfe-befehle\">"
	. "<b>Befehle im Chat</b></a> oder direkt im "
	. "Fenster (<b>Einstellungen</b>, "
	. "<b>Räume</b>) verändert werden. Für die voreingestellten Sprüche, "
	. "die im Chat abgerufen werden können, gibt es noch eine <a href=\"index.php?aktion=hilfe-sprueche\">"
	. "<b>Übersicht</b></a>.</p>" . "<p><b>Liste:</b><br>"
	. "Im Chat, wie auch im Fenster (<b>Benutzerliste</b>), "
	. "kann eine Liste aller in einem bestimmten "
	. "Raum chattenden Benutzer ausgegeben werden. Im Fenster ist es darüber hinaus möglich, "
	. "weitere Informationen abzufragen oder dem Benutzer eine private Nachricht zu schreiben.</p>"
	. "<p><b>Namen:</b><br>"
	. "Jeder Benutzer im Chat hat einen Benutzernamen. "
	. "Mit diesem Benutzernamen ist der Login möglich und wird im Chatangezeigt. "
	. "Der Benutzername darf keine Leerzeichen enthalten. Benutzernamen sind eindeutig, "
	. "also kann es keine zwei Benutzer mit demselben Benutzername geben." . "</p>"
	. "<p><b>Ergänzung des Benutzername:</b><br>"
	. "Andere Benutzer können im Chat durch den Beginn des Benutzername, gefolgt von einem Doppelpunkt, "
	. "gezielt angesprochen werden.<br>"
	. "<b>Aus dil: Hallo!</b> wird beispielsweise <b>[zu Dilbert] Hallo!</b><br>"
	. "Die Nickergänzung funktioniert unter anderem beim Chatten und beim Flüstern. "
	. "Ein @ vor dem Beginn des Benutzername wird ebenfalls zum vollen Benutzernamen ergänzt.</p>"
	. "<p><b>Einstellungen:</b><br>"
	. "Jeder Benutzer kann optional eine E-Mail-Adresse und eine Homepage eintragen, "
	. "die in der Benutzerliste im Fenster angezeigt werden. Neben dem Passwort, das neu gesetzt werden kann, "
	. "ist auch der Wechsel der Farbe durch Klick auf eines der bunten Felder möglich."
	. "<br>Zudem kann jeder Benutzer seine Systemeintrittsnachricht und -austrittsnachricht in den Raum ändern. (Dies "
	. "muss vom Chatbetreiber freigeschaltet sein.) Um hier automatisiert den eigenen Benutzernamen einzutragen, kann man "
	. "<b>%nick%</b> eintragen. Um automatisiert den Raumnamen einzutragen, ist <b>%raum%</b> einzutragen. <br>"
	. "Weiterhin kann man, soweit im Chat die entsprechenden Features aktiviert sind, die Anzeige seines eigenen "
	. "Punktewürfels unterdrücken, die Anzeige der Smilies deaktivieren oder die Anzeige der Ein-/Austrittsnachrichten "
	. "unterdrücken." . "</p>" . "<p><b>Räume:</b><br>"
	. "Zum Wechsel in einen offenen Raum wählt man einfach den passenden Raum im Auswahlfeld "
	. "unter dem Chat-Eingabefeld aus. Hinter dem Raumnamen wird die Anzahl der Benutzer angezeigt, "
	. "die aktuell in diesem Raum chatten. Jeder Benutzer kann beliebig viele Räume aufmachen, "
	. "die er verändern darf. Nicht-permamente Räume werden aber automatisch nach Verlassen gelöscht. "
	. "Ein Besitzer eines Raums oder ein ChatAdmin darf den Raum umbenennen und den Ein- und "
	. "Austrittstext sowie den Raumstatus verändern. Einen offenen Raum darf jeder Benutzer betreten, "
	. "während ein geschlossener Raum nicht betreten werden darf und auch nicht angezeigt wird. "
	. "Ein Raum darf gelöscht werden; aber falls sich Benutzer darin aufhalten, werden diese in die Lobby geleitet. "
	. "Mit dem Befehl /kick können im Chat unerwünschte Benutzer aus einem Raum entfernt und der Wiedereintritt "
	. "automatisch damit verboten werden." . "</p>"
	. "<p><b>Benutzerliste"
	. " im rechten Fenster neben dem Chat (@ > USERNAME)</b><br>"
	. "In der Benutzerliste werden die Benutzernamen aller Benutzer, die im aktuellen Raum online sind, "
	. "angezeigt. Das @ vor dem Benutzernamen kopiert den Benutzernamen in die Chat-Eingabezeile. "
	. "Dies ist sehr praktisch, um einen Benutzer <b>öffentlich</b> direkt anzusprechen. "
	. "Das > (Größer) vor den Benutzernamen kopiert den Benutzernamen in die Chat-Eingabezeile, "
	. "um diesen Benutzer <b>privat</b> anzuflüstern. Falls ein Nick in Klammern () steht, ist der "
	. "Benutzer gerade abwesend (/away). Ein Admin sieht vor dem Nick zusätzlich drei "
	. "Links <b>G K S</b> zur Ausführung der Befehle Gag (Knebel), Kick (Herauswurf) und "
	. "Sperre im Chat. Ein Raumbesitzer sieht nur den Link auf Kick.</p>"
	. "<p><b>Smileys-Liste"
	. " im rechten Fenster neben dem Chat</b><br>"
	. "Das Fenster der Benutzerliste kann auf eine Liste mit häufig benutzten Smileys umgeschaltet werden. "
	. "Ein Klick auf den Smiley kopiert ihn in die Eingabezeile. Sie können in Ihren Einstellungen "
	. "die Darstellung der Smileys als Grafiken unterdrücken. In jedem Raum lassen sich ebenfalls "
	. "die Grafiken unterdrücken.</p>"
	. "<p>"
	. "<b>Moderationsmodul für moderierte Räume</b>"
	. "<ul>"
	. "<li>In moderierten Räumen kann ein moderierter Chat stattfinden. Alle öffentlichen Nachrichten werden "
	. "unterdrückt und an einen oder mehrere Moderatoren weitergeleitet, solange sich Moderatoren im Raum aufhalten. "
	. "<li>Die Moderatoren wählen eine oder mehrere Fragen aus und beantworten diese. Nach Beantwortung der Fragen werden"
	. " diese mit der Antwort im Chat veröffentlicht. "
	. "<li>Für die Moderatoren steht eine eigene Benutzeroberfläche in HTML zur Verfügung, mit der die Fragen der Benutzer schnell"
	. " und komfortabel durchgeschaltet, beantwortet oder unterdrückt werden können. "
	. "<li>Die Fragen, die ein Moderator zur Beantwortung ausgewählt hat, sind für alle anderen Moderatoren gesperrt. "
	. "<li>Öffentliche Ein- und Austrittsnachrichten werden in moderierten Räumen unterdrückt. "
	. "<li>Die Moderationsfunktionen sind nur in den Räumen aktiv, die auf den Status \"moderiert\" gesetzt sind."
	. "</ul>" . "</p>";

$legende = "<tr><td colspan=\"2\" class=\"tabelle_zeile1\" style=\"padding: 5px; font-weight:bold;\">Wieviele Chat-Punkte ergeben welchen Würfel?</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"pics/gruppe1.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 1.000 - 9.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"pics/gruppe2.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: 10.000 - 99.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"pics/gruppe3.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 100.000 - 199.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"pics/gruppe4.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: 200.000 - 299.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"pics/gruppe5.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 300.000 - 499.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"pics/gruppe6.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: 500.000 - 749.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"pics/gruppe7.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 750.000 - 999.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"pics/gruppe8.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: 1.000.000 - 4.999.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"pics/gruppe9.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 5.000.000 - 9.999.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"pics/gruppe10.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: ab 10.000.000 Punkten</td></tr>"
	. "<tr><td colspan=\"2\" class=\"tabelle_zeile2\" style=\"padding: 5px; font-weight:bold;\">Weitere Symbole:</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<span class=\"fa fa-home icon16\" alt=\"Homepage\" title=\"Homepage\"></span><br></td><td class=\"tabelle_zeile1\">Homepage des Benutzers</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<span class=\"fa fa-envelope icon16\" alt=\"Mail\" title=\"Mail\"></span><br></td><td class=\"tabelle_zeile2\">E-Mail des Benutzers</td></tr>";

$hilfe_community = "<br>
<table style=\"float:right;\">
" . $legende
. "
</table>
<p><b><A NAME=\"punkte\">Punkte</A></b><br>
Unter dem Chat-Menüpunkt \"<b>Benutzer</b>\" führt der Link zur Benutzer Top10/100 Liste.
Alle angemeldeten Benutzer bekommen pro einzelnes im Chat geschriebenes Wort
einen Punkt. Die Voraussetzung ist, dass jedes Wort mindestens vier Buchstaben hat
und sich der Benutzer in einem öffentlichen, permanenten Raum mit mindestens
" . $punkte_ab_user
. " Benutzern befindet. Für das
erstmalige Ausfüllen des Profils bekommt jeder Benutzer 500 Punkte. Die Admins
haben ferner die Möglichkeit, den Benutzern Punkte zu schenken oder abzuziehen.
Dies geschieht mit den Befehlen \"<b>/lob Benutzername Punktezahl</b>\" und \"<b>/tadel
Benutzername Punktezahl</b>\". Wenn Sie dem Benutzer Heinz also 100 Punkte schenken
möchten, tippen Sie im Chat einfach \"/<b>lob Heinz 100</b>\" ein und schon bekommt
der Benutzer Heinz 100 Punkte gutgeschrieben. Beim Logout des Benutzers werden die
Punktezahlen jeweils aufaddiert und aktualisiert. Mit Klick auf den
Menüpunkt Top10/100 kann jeder Benutzer Einblick in die Rangliste nehmen. Je
nach Anzahl der Punke erhält jeder Benutzer (sowie auch die Admins) ein
kleines Würfelsymbol, das seinem Benutzernamen im Chat zugeordnet wird.<br><br>
<b>Tipp 1:</b> Die Punkte werden meistens erst beim Logout aufs Benutzerkonto gutgeschrieben,
daher gibt's erst nach dem Login einen Würfel mit mehr Augen.<br>
<b>Tipp 2:</b> Wer um Punkte bettelt, macht sich unbeliebt.<br>
<b>Tipp 3:</b> Wer in leeren Räumen Texte schreibt, um die Punkte nach oben zu
treiben, hat schnell keine Punkte mehr.<br><br>
<b><a name=\"profil\">Profile</A></b><br>
Unter dem Punkt \"<b>Einstellungen</b>\" kann ebenfalls jeder Einblick in sein Profil
nehmen und es gegebenenfalls ändern. Hier kann der Benutzer Angaben über seine
Anschrift, Telefon-, Handynummer, ICQ, Geburtsdatum, Geschlecht, Hobbys und
vieles mehr hinterlassen. Diese Profile sind im Chat öffentlich abrufbar.
Das Ausfüllen der Profile ist für die Benutzer freiwillig. Admins haben in Ihrem
eigenen Profil noch die Möglichkeit, sich die Profile aller Benutzer ausgeben zu
lassen.<br><br>
<b><A NAME=\"mail\">Chat-Mail</A></b><br>
Die Chat-Mail (<b>MAIL</b>, <b>/mail</b>) ist eine im Chat integrierte Mailbox mit
Weboberfläche. Im Chat selbst steht hinter jedem Nick ein Mailsymbol (blauer
Briefumschlag). Somit kann jeder registrierte Benutzer im Chat anonym Mail
empfangen, ohne seine echte Adresse herauszugeben. Um in das Mail-Menü zu
gelangen, klicken Sie in der unteren Chatleiste auf den Menüpunkt Mail. Nun
öffnet sich ein neues Fenster, in dem Sie Ihre Webmail-Oberfläche finden.
Hier können Sie direkt alle Ihre empfangenen Nachrichten sehen und per Klick
auf die Betreffzeile öffnen. Wenn Sie Nachrichten zum Löschen in den
Papierkorb verschieben möchten, markieren Sie einfach die betreffende Mail
durch Klick in das \"<b>Löschen</b>\"-Kästchen und klicken Sie dann auf den
<b>Löschen</b>-Button rechts unter den Nachrichten.<br>
Gelöschte Nachrichten werden automatisch nach einer gewissen Zeit aus dem Papierkorb
gelöscht. (Standardeinstellung 14 Tage, kann aber vom Chatbetreiber geändert werden)<br><br>
<b>Im Mailmenü haben Sie nun folgende Auswahlmöglichkeiten:</b></p>
<ul>
<li><i>Posteingang</i>
<li><i>Neue Nachricht schreiben</i>
<li><i>Papierkorb zeigen</i>
<li><i>Papierkorb leeren</i>
</ul><p>
Um eine neue Nachricht zu verfassen, geben Sie unter dem Punkt \"<b>Neue Nachricht senden</b>\"
in das Feld \"<b>Benutzername</b>\" den Benutzernamen des Benutzers ein, dem Sie eine Nachricht
zukommen lassen möchten und klicken Sie dann auf \"<b>weiter</b>\". Nun befinden Sie
sich in der Texteingabe für Ihre Mail. Am Fuße der Box können Sie übrigens
auswählen, ob Sie die Nachricht an die Chat-interne Mailbox des Benutzers oder
an seine reguläre E-Mail Adresse schicken möchten, sofern der Benutzer diese als
öffentlich in seinem Chatprofil angegeben hat. Sind Betreff und Textfeld
fertig ausgefüllt, verschicken Sie die Mail mit Klick auf den
\"<b>Senden</b>\"-Button. Wenn Sie eine empfangene Mail löschen, so wird sie zuerst
einmal in den Papierkorb verschoben. Durch Klick auf den Menüpunkt
\"<b>Papierkorb zeigen</b>\" können Sie sich alle zum Löschen vorgesehenen Mails noch
einmal anschauen. Wenn Sie nun sicher sind, dass sie auch alle im Papierkorb
befindlichen Mails löschen wollen, klicken Sie auf \"<b>Papierkorb leeren</b>\".<br><br>
<b><A NAME=\"freunde\">Freunde</A></b><br>
Bei den Freunden (<b>Einstellungen</b>, <b>/freunde</b>) können Sie einfach andere Benutzer im
Chat zu Freunden erklären oder aber aus der Freunde-Liste löschen. So wird
man stets über den Login/Logoff oder die Anwesenheit von Freunden im Chat
informiert. Die Freunde-Liste ruft der Chatter über den Punkt \"<b>USER</b>\" direkt
im Chat auf. Es erscheint ein Fenster, in dem sich auch das Main-Chat Menü
befindet. Durch Klicken auf \"<b>meine Freunde</b>\" gelangt man nun auf die Liste
und kann ersehen, wen man sich als Freund eingetragen hat oder von wem man
selbst als Freund aufgenommen wurde.<br><br>
<b><A NAME=\"aktionen\">Aktionen</A></b><br>
Über den Menüpunkt \"<b>Einstellungen</b>\" im Chat gelangen Sie zu den sogenannten
\"<b>Aktionen</b>\". Mit den Aktionen steuern Sie, bei welchen Ereignissen Sie mit
welcher Nachricht informiert werden wollen. Ein Ereignis ist z.B. der Login
oder Logout eines Freundes oder auch der Eingang einer neuen Mail. Wann Sie
die Nachricht erhalten wollen, wählen Sie aus der obersten Zeile aus.
Möglich ist der Empfang sofort wenn Sie online sind (<b>Sofort/Online</b>) (z.B.
Freund loggt ein/aus oder zu dem Moment, in dem Sie eine neue Mail
erhalten), bei Ihrem Login in den Chat oder regelmäßig <b>alle 5 Minuten</b>
(regelmäßige Information über die vorliegenden neuen Mails oder die
Anwesendheit Ihrer Freunde im Chat) Die Benachrichtigungen, die Sie
erhalten, wenn Sie nicht im Chat sind (offline), wählen Sie unter
<b>Sofort/Offline</b> aus. Die Art der Nachricht ist einstellbar: so gibt es <b>keine</b>
Benachrichtigung, <b>Chat-Mail</b> (chat-interne Mail), eine <b>E-Mail</b> an Ihre
nicht-öffentliche E-Mail Adresse oder eine <b>OLM</b> (OnLineMessage, direkte
Nachricht in Chat wie /msg). Zusätzlich sind auch Kombinationen von E-Mail
und OLM sowie Chat-Mail und OLM möglich, wobei Sie in diesem Fall zwei
Nachrichten erhalten.<br><br>
<b><a name=\"home\">Benutzer-Homepages</a></b><br>
Unter dem Punkt \"<b>Einstellungen -> Homepage</b>\" kann ebenfalls jeder Benutzer mit
wenigen Klicks seine eigene kleine Homepage erstellen, ohne jegliche
HTML-Kenntnisse zu besitzen. Nach der \"<b>Freischaltung</b>\" der Homepage wird hinter dem
Benutzernamen ein <b>Haus-Symbol</b> angezeigt, über das man sich die Homepage ansehen kann. In der
\"Suche nach Benutzern\" (<b>USER -> Suche</b>) ist die gezielte Suche nach Benutzern mit freigeschalteter Homepage
möglich.<br><br>
Ihre Homepage kann natürlich auch mit folgender Adresse von außerhalb des Chats abgerufen werden:<br>
<b> %chat_url%/home.php?/NICKNAME</b><br><br>
Grundsätzlich immer dargestellt werden</p>
<ul>
<li><I>Benutzername des Benutzers</I>
<li><I>Onlinezeit</I>
<li><I>Benutzerlevel</I>
<li><I>Punkte</I>
</ul><p>
	
Weiter kann der Chatuser entscheiden:</p>
<UL>
<li><I>welche Daten aus seinem Benutzerprofil auf der Homepage dargestellt werden sollen.</I>
</UL>
<p>
<p>Außerdem hat er der die Möglichkeit:</p>
<UL>
<li><I>Schrift-, Hintergrund- und Linkfarben sowie Hintergrundbilder für seine Homepage zu bestimmen,</I>
<li><I>beliebige Texte (auch mit HTML) in das Textfeld einzugeben,</I>
<li><I>Bilder von seinem eigenen Rechner hochzuladen und in seine Homepage einzufügen.</I>
</UL>
</p>";

$chatiquette = "
<p><b>Wie melde ich mich an? </b></p>
<p>Wenn man den Chat als Gast betritt, sollte man sich sofort einen Namen zulegen (/nick name), da die meisten Benutzer sich nur
ungerne mit einer (austauschbaren) Zahl unterhalten. Im realen Leben wird man ja auch mit seinem Namen angesprochen. Über den
Namen ist man leichter ansprechbar und unverwechselbar.
Merkt man nach einiger Zeit, dass man öfter in den Chat kommen möchte, empfiehlt es sich, einen festen Account (Benutzernamen)
zuzulegen. Natürlich kann man das auch gleich machen, damit ist der Name dann besetzt und kein anderer kann sich mehr so
nennen. Hierzu klickt man auf der Startseite auf >>neu anmelden<< und bestätigt die Anmeldung, die per Mail zugesandt wird.
Dann wählt man sich seinen Chatnamen und sein Zugangspasswort aus.
</p>
	
<b><p>Wie verhalte ich mich im Chat richtig, wenn ich zum ersten Mal reingehe? </p></b>
	
<p>Zuerst verschafft man sich am besten eine Übersicht über die Situation im Chat.
Wieviele Benutzer und Admins anwesend sind, welche Gespräche laufen,
welche Stimmung allgemein herrscht und wie die Benutzer miteinander umgehen. </p>
	
<p>Um ins Gespräch einzusteigen, spricht man am besten jemanden direkt an (name: text)
oder man gibt ein allgemeines Statement zu einem Thema ab und wartet dann, bis jemand auf einen eingeht.
Dann ist das Chatten nicht mehr schwer. </p>
	
<b><p>Was ist ein Admin? </p></b>
<p>Admin heißt Administrator (=\"Verwalter\")<br>
Admins im mainChat haben die Aufgabe, dafür zu sorgen, dass sich alle wohl fühlen.
Sie beantworten gerne Fragen zum Chat, helfen bei Problemen und werfen Benutzer aus dem Chat,
wenn diese andere belästigen.</p>
	
<p>Administrative Tätigkeit ist Freizeit, die nicht bezahlt wird.
Deswegen kann nicht gewährleistet werden, dass immer ein Admin zur Stelle ist.
Allerdings kennen sich viele Stammuser genauso gut aus und beantworten oft auch gerne Fragen,
falls die Admins kurzfristig nicht anwesend oder ansprechbar sind.
</p>
	
<b><p>Können Admins private Nachrichten mitlesen? </p></b>
	
<p>Immer wieder tauchen im Chat Leute auf, die behaupten, Admins könnten alle privaten Mitteilungen lesen.
Abgesehen davon, dass wir das moralisch nicht vertreten könnten, ist es technisch auch nicht vorgesehen.
Private Nachrichten im Chat werden nach einigen Sekunden automatisch wieder gelöscht und *nicht* mitgeloggt, das heißt im
Klartext:<br>
<b>Nein, können sie nicht.</b> </p>
	
<b><p>Bin ich im mainChat anonym? </p></b>
<p>
Ja und Nein.<br>
Man ist anderen Benutzern gegenüber anonym; Admins jedoch können die IP-Adresse des Rechners, mit dem man online ist, sehen.
Das muss auch so sein, damit bei Vorfällen, wie zum Beispiel Beleidigungen, gehandelt werden kann.
Benimmt sich jemand dauerhaft daneben und es ist nachvollziehbar, von welchem Provider er kommt, ist es auch kein Problem,
sich an den Provider zu wenden und zu beschweren. Die meisten Provider reagieren empfindlich auf solchen Missbrauch und
sperren den Zugang.</p>
	
<b><p>Was sollte ich im Chat grundsätzlich nicht tun? </p></b>
<p>Alles, was ich im realen Leben auch nicht tun würde.<br>
Ich gehe nicht in eine Kneipe und pöbele sofort die Leute an, dann kommt der Wirt und wirft mich raus.
Ich gehe auch nicht auf eine Party und baggere die weiblichen Gäste an, dann wollen sie meist schnell nicht mehr mit mir reden.
Ich frage nicht sofort jeden aus, auch wenn ich über ihn viel wissen möchte, sondern ich warte, bis sich das von selbst ergibt: vielleicht,
indem ich ein bisschen von mir erzähle.
Kurz gesagt: Ich benehme mich höflich, respektvoll und nicht zu aufdringlich. </p>
	
<b><p>Wann werde ich von den Admins aus dem Chat geworfen?</p></b>
<p>
Admins haben das Recht, Benutzer aus dem Chat zu werfen (\"kicken\"), wenn diese sich daneben benehmen.<br>
Davon wird meist, außer in Extremfällen, erst nach einer Vorwarnung Gebrauch gemacht. Sieht ein Benutzer nicht ein, dass er sich
besser benehmen sollte, kann er auch gesperrt werden. In dem Fall wird die IP (Rechner-Adresse) blockiert, so dass sich der
Betreffende nicht mehr in den Chat einloggen kann. Bei wiederholten Verstößen wird der Provider / die Bildungseinrichtung
informiert und der Benutzer muss mit entsprechenden Konsequenzen rechnen.
</p>
<b>Gründe zum Kicken sind: </b>
<p>
<br>
<b>Pöbeleien.</b> Jemanden zu beleidigen, den man gar nicht kennt, ist wenig stilvoll und wird nicht geduldet. Bei extremen
Vorfällen (dies gilt auch für die Verbreitung nationalsozialistischen Gedankenguts oder von Sex-Urls) wird ohne Vorwarnung
gekickt.
<br><br>
<b>Schreiben</b> nur mit Großbuchstaben. Das bedeutet, auf die Realität bezogen, lautes Schreien und ist wenig erwünscht.
Wenn etwas betont werden soll, kann man es auch kursiv (*) oder fett (_) darstellen. Ganze Sätze sollte man jedoch nicht betonen; das gilt als unfein.
</p>
";

$t['willkommen2'] = "<p><span style=\"font-size: large\"><b>Willkommen im $chat!</b></span></p><br>";
$t['willkommen3'] = "Willkommen im $chat!";

$t['login1'] = "<b>Login mit sicherer HTTPS-Anmeldung</b>";
$t['login2'] = "<b>neue HTTPS-Anmeldung</b>";
$t['login3'] = "Gäste können sich einloggen, in dem "
	. "sie einfach auf <b>Login</b> klicken " . "ohne einen Namen einzugeben";
$t['login4'] = "<p><b>Der Chat ist im Moment aus technischen Gründen geschlossen! "
	. "Wir bedauern dies sehr und arbeiten daran.<br><br> Bitte versuchen "
	. "Sie es später wieder.</b></p><br><br>";
$t['login5'] = "<p><b>Leider ist der Account %u_nick% derzeit gesperrt.\n"
	. "Bitte wenden Sie sich an unseren <a href=\"mailto:" . $webmaster
	. "\">Webmaster</a>.</b></p><br><br>\n";
$t['login6'] = "<h2>Ihr Browser unterstützt keine Frames. Ohne Frames kein $chat :-)</h2>\n" . "</noframes>\n";
$t['login7'] = "<p><b>Falsches Passwort oder Benutzername eingegeben! Bitte versuchen Sie es neu:</b></p>\n";
$t['login8'] = "Benutzername:";
$t['login9'] = "Passwort:";
$t['login10'] = "Login";
$t['login12'] = "Raum:";
$t['login13'] = "Gast";
$t['login14'] = "neuen Benutzernamen registrieren";
$t['login15'] = "<p><b>Leider ist der Login als Gast derzeit gesperrt. Bitte haben Sie dafür Verständnis.</b></p>";
$t['login16'] = "<p><b>Leider ist kein Login als Gast möglich. Bitte melden Sie sich mit Ihrem registrierten Benutzernamen an.</b></p>";
$t['login17'] = "Ich erkenne diese Bedingungen an";
$t['login18'] = "Abbruch";
$t['login19'] = "<p><b>Dieser Benutzername ist durch ein Passwort geschützt! Bitte versuchen Sie es neu:</b></p>\n";
$t['login20'] = "Zu viele Fehlversuche beim Login. Der Account %login% wurde für einige Zeit gesperrt!";
$t['login21'] = "Zu viele Fehlversuche beim Login. Der Account %login% wurde für einige Zeit gesperrt!\n\n"
	. "Hinweis: Diese Mail wurde automatisch erzeugt und wird an den Webmaster verschickt. "
	. "Über http://www.ripe.net/perl/whois können Sie unter Angabe der IP-Adresse den "
	. "Provider ermitteln, über den der Loginversuch stattfand. "
	. "Um den Verursacher zu ermitteln, wenden Sie sich bitte unter Angabe der IP und des "
	. "Datums/Uhrzeit direkt an den Provider.\n";
$t['login22'] = "Raum/Forum:";
$t['login24'] = "<p><b>Der Login ist leider nicht möglich!</b></p>"
	. "<p>Es sind im $chat bereits %online% Benutzer online. "
	. "Als %leveltxt% dürfen Sie ab maximal %max% Benutzer den $chat nicht mehr betreten.</p>"
	. "%zusatztext%";
$t['login25'] = "<p><b>Fehler beim Login:</b><br>Der Login als Admin (Superuser oder Chatadmin) "
	. "ohne aktivierte Cookies ist aus Sicherheitsgründen nicht gestattet. Bitte verwenden "
	. "Sie einen Browser mit aktivieren Cookies "
	. "</p><p>[<a href=\"%url%\">weiter zur Loginseite</a>]</p>";
$t['login26'] = "Lieber Chatter, um diesen Chat zu betreten, müssen Sie ihn über die Webseite %webseite% betreten.";
$t['login27'] = "Passwort vergessen?";

$t['neu1'] = "<p><b>Bitte das Feld 'Name' ausfüllen!</b></p>\n";
$t['neu2'] = "<p><b>Bitte geben Sie als Benutzernamen mindestens 4 Zeichen ein!</b></p>\n";
$t['neu3'] = "<p><b>Bitte geben Sie als Benutzernamen maximal 20 Zeichen ein!</b></p>\n";
$t['neu4'] = "<p><b>Bitte geben Sie als Benutzernamen mindestens 4 und maximal 20 gültige Zeichen ein!</b><br>Die gültigen Zeichen sind: %zeichen%</p>\n";
$t['neu5'] = "<p><b>Bitte das Feld 'Passwort' mit mindestens 4 Zeichen ausfüllen!</b></p>\n";
$t['neu6'] = "<p><b>Sie haben sich beim Passwort vertippt. Bitte neu versuchen!</b></p>\n";
$t['neu7'] = "<p><b>Bitte im Feld 'E-Mail (nur intern)' eine gültige E-Mail Adresse eingeben!</b></p>\n";
$t['neu8'] = "<p><b>Bitte im Feld 'E-Mail (öffentlich)' eine gültige E-Mail Adresse eingeben!</b></p>\n";
$t['neu9'] = "<p><b>Dieser Benutzername ist leider schon vergeben! Bitte wählen Sie einen anderen.</b></p>\n";
$t['neu12'] = "Benutzername:";
$t['neu13'] = "&nbsp;&nbsp;(Bitte&nbsp;nur&nbsp;ein&nbsp;Wort)";
$t['neu14'] = "Passwort:";
$t['neu15'] = "Passwort:";
$t['neu16'] = "&nbsp;(Wiederholung)";
$t['neu17'] = "E-Mail:";
$t['neu18'] = "(nur intern)";
$t['neu19'] = "E-Mail:";
$t['neu20'] = "&nbsp;&nbsp;(öffentlich)";
$t['neu21'] = "Homepage:";
$t['neu22'] = "Fertig";
$t['neu23'] = "<p>Bitte beachten Sie, daß Ihr <b>Name</b> und Ihre <b>E-Mail</b>\n"
	. "nur für interne Zwecke der Administration genutzt und dritten\n"
	. "Personen nicht zugänglich gemacht wird (gem. <a href=\"index.php?aktion=datenschutz\">Datenschutzerklärung</a>). Falls Sie zum Beispiel Ihr Passwort\n"
	. "vergessen und uns kontaktieren, können wir Ihnen an diese Adresse Ihr "
	. "neues Passwort schicken. Die 2. optionale E-Mail Adresse (öffentlich) dagegen ist\n"
	. "im Chat für alle anderen Mitglieder sichtbar.</p>\n"
	. "<p>Alle Felder mit <b>*</b> sind Pflichtfelder. Die Felder <b>E-Mail (öffentlich)</b> und\n"
	. "<b>Homepage</b> sind öffentlich und müssen nicht ausgefüllt werden.\n"
	. "Falls Sie <b>Benutzername</b> nicht ausfüllen, wird Ihr Name aus dem Feld\n"
	. "<b>Name</b> als öffentlicher\n"
	. "Benutzername automatisch eingesetzt.</p>\n"
	. "<p>Mit dem Abschluß der Registrierung (Klick auf Fertig) bestätigen Sie Ihr Einverständnis\n"
	. "zur Verarbeitung Ihrer personenbezogenen Daten gemäß unserer <a href=\"index.php?aktion=datenschutz\"><b>Datenschutzerklärung</b></a>.\n"
	. "Außerdem erklären Sie sich mit unseren <a href=\"index.php?aktion=nutzungsbestimmungen\"><b>Nutzungsbestimmungen</b></a> einverstanden.</p>\n";
$t['neu24'] = "<p><b>Wir freuen uns, Sie als neues Mitglied im $chat begrüßen zu dürfen!</b></p><br><br>\n";
$t['neu25'] = "<p>Sie wurden nun im $chat eingetragen!</p>\n";
$t['neu28'] = "<p><b>Bitte merken Sie sich Ihr Passwort, denn ohne Passwort kommen Sie nicht mehr in den Chat....</b></p>\n";
$t['neu29'] = "Weiter zum Chat";
$t['neu30'] = "Eintrag abgeschlossen";
$t['neu31'] = "Neuen Account registrieren:";
$t['neu32'] = "<p><b>Der Name '%gast%....' ist für Gast-Accounts reserviert. Bitte wählen Sie einen anderen Benutzernamen!</b></p>";
$t['neu33'] = "<p><b>Registrierung, 1. Schritt:</b> Um sich neu für den $chat zu registrieren, geben Sie bitte Ihre E-Mail Adresse ein:</p>";
$t['neu34'] = "Ihre E-Mail Adresse:";
$t['neu35'] = "Absenden";
$t['neu36'] = "Willkommen beim $chat!<br><br>Um nun die Registrierung als neuer Benutzer fortzusetzen, klicken Sie bitte auf den folgenden Link:"
	. "<br><br>%link% <br><br>Sollte es zu Problemen beim klicken auf den Registrierungslink kommen, so verwenden Sie diesen Link zum manuellen "
	. "Prüfen der E-Mail-Adresse:<br> %link2%<br><br>"
	. "Ihre E-Mail-Adresse: %email%<br>" . "Freischalt-Code   : %hash%<br><br>"
	. "<br>Wenn Sie diese Links nicht in Ihrem E-Mailprogramm anklicken können, so kopieren Sie diese in die Zwischenablage und fügen "
	. "Sie den Link dann in den Browser ein<br><br>"
		. "-- <br>   $chat (" . $chat_url . $PHP_SELF . ")<br>";
$t['neu37'] = "<p><b>Registrierung, 2. Schritt:</b> Sie erhalten nun eine E-Mail! Um die Registrierung abzuschließen, klicken Sie bitte auf den dort angegebenen Link!</p>";
$t['neu38'] = "Ihre Registrierung im $chat";
$t['neu39'] = "<p><b>Fehler: </b>An die E-Mail Adresse '%email%' wurde bereits verschickt!</p>";
$t['neu40'] = "Diese E-Mail-Adresse ist leider gesperrt! Bitte wenden Sie sich an den Webmaster";
$t['neu41'] = "<p><b>Fehler: </b>Die Eingabe '%email%' ist keine gültige  E-Mail Adresse!</p>";
$t['neu42'] = "Bitte geben Sie nun Ihre <b>E-Mail-Adresse</b> und den <b>Freischalt-Code</b> ein, den Sie soeben per E-Mail bekommen haben.<br>"
	. "Sie können hierzu natürlich auch gerne Cut und Paste (Strg+C und Strg+V) verwenden";
$t['neu43'] = "Freischalt-Code:";
$t['neu44'] = "<p><b>Fehler: </b>Pluszeichen sind im Benutzernamen nicht erlaubt!";
$t['neu45'] = "<p>Anmerkung: Sie erhalten nicht sofort einen E-Mail. Ihre Anmeldung muss erst vom Webmaster bestätigt werden.</p>";
$t['neu46'] = "Anfrage um Registrierung im $chat";
$t['neu47'] = "Hallo!<br><br>Ein Benutzer mit der E-Mail-Adresse %email% möchte sich in Ihrem Chat $chat anmelden."
	. "<br>Zum freigeben dieser E-Mail-Adresse verwenden Sie bitte diesen Link:<br><br> %link1%<br><br>"
	. "Freizugebende E-Mail-Adresse: %email%<br>" . "Freischalt-Code: %hash%<br><br>"
	. "Dieser Benutzer erhält dann automatisch eine Mail mit einem Link mit welchem er/sie sich anmelden kann.<br><br>"
	. "Möchten Sie dem Benutzer auf andere Weise den Freischalt-Code mitteilen, so sind folgende Angaben "
	. "an den Benutzer zu übermitteln:<br><br>"
	. "Internetadresse zum Freischalten: %link2%<br>"
	. "E-Mail-Adresse: %email%<br>" . "Freischalt-Code: %hash%<br><br>"
		. "-- <br>   $chat (" . $chat_url . $PHP_SELF . ")<br>";
$t['neu48'] = "<p><b>Registrierung, 2. Schritt:</b> Warten Sie bitte nun, bis Sie vom Webmaster eine E-Mail mit Ihrem "
	. "Freischaltcode erhalten! Um dann die Registrierung abzuschließen, benutzen Sie bitte den dort "
	. "angegebenen Link!</p>";
$t['neu49'] = "Freizugebende E-Mail-Adresse:";
$t['neu50'] = "Bitte geben Sie nun die <b>E-Mail-Adresse</b>, die Sie freigeben möchten, und den <b>Freischalt-Code</b> ein, "
	. "den Sie soeben per E-Mail bekommen haben.<br>"
	. "Sie können hierzu natürlich auch gerne Copy und Paste (Strg+C und Strg+V) verwenden";
$t['neu51'] = "E-Mail-Adresse falsch!";
$t['neu52'] = "Freischaltcode falsch!";
$t['neu53'] = "Willkommen beim $chat!<br><br>Der Webmaster hat Ihre E-Mail-Adresse nun freigeschaltet."
	. "<br>Zum endgültigen Anmelden verwenden Sie bitte nun den Link: "
	. "<br> %link%<br><br>Zum anmelden benötigen Sie dann noch:<br>"
	. "Ihre E-Mail-Adresse: %email%<br>" . "Ihren Freischalt-Code: %hash%<br><br>"
		. "-- <br>   $chat (" . $chat_url . $PHP_SELF . ")<br>";
$t['neu54'] = "Dem Benutzer/der Benutzerin wurde nun per E-Mail der Freischaltcode mitgeteilt.";
$t['neu55'] = "Mit dieser E-Mail ist bereits ein Benutzer registriert. Falls es sich um Ihren Account handelt, können Sie über <a href=\"index.php?aktion=passwort_neu\">\"Passwort vergessen?\"</a> ein neues Passwort anfordern.<br><a href=\"javascript:history.back()\">Zurück zur Registrierung!</a>";

$t['default1'] = "Login in den $chat oder";
$t['default2'] = "Gerade sind <b>%onlineanzahl% Benutzer online</b>, ";
$t['default3'] = "insgesamt sind %useranzahl% Benutzer registriert. ";
$t['default4'] = "Benutzer online in %raum%:";
$t['default5'] = "$chat:";
$t['default6'] = "<b>Warnung an alle Admins:</b> Benutzer <b>%u_nick%</b> loggt sich über %ip_adr%/%ip_name% im $chat ein (%is_infotext%)!";
$t['default7'] = "<b>$chat:</b> Benutzer '<b>%u_nick%</b>' betritt Raum '%raumname%'.";
$t['default8'] = "Im Forum finden sich %beitraege% Beiträge in %themen% Diskussionsthemen.";
$t['default9'] = "Raum ";
$t['default10'] = "Community-Bereich ";

$t['ipsperre1'] = "Info an alle Admins: Benutzer %u_nick% loggt sich über %ip_adr%/%ip_name% ein (%is_infotext%)!";
$t['ipsperre2'] = "IP-Sperre umgangen, da mehr als $loginwhileipsperre Punkte (%punkte%)";

$t['agb'] = "<ol><li>Wir bitten alle Benutzer im $chat um ein höfliches, respektvolles und nicht zu aufdringliches "
	. "Verhalten. </li>"
	. "<li>Nicht erlaubt ist die Registrierung ohne Angabe des wahren Vor- und Zunamens. "
	. "Pro Mitglied ist nur ein Account zulässig.</li>"
	. "<li>Im $chat sollte man grundsätzlich nichts tun, was man im realen Leben auch nicht tun würde. "
	. "Ausdrücklich untersagt sind Beleidigungen, Pöbeleien, Sticheln, Provozieren, Stänkern, Baggern, "
	. "anstößige Äußerungen, Schreiben nur in Großbuchstaben (wird als Anschreien gewertet), Spam und "
	. "nationalsozialistische Sprüche oder Benutzernamen. </li>"
	. "<li>Das öffentliche Posten von Links im Allgemeinen und das Einfügen von URLs"
	. "zum Zweck der Werbung, insbesondere für andere Internet-Angebote,"
	. "ist nicht gestattet.</li>"
	. "<li>Admins haben das Recht, Benutzer bei Verstößen gegen diese Regeln aus dem $chat zu werfen. Davon "
	. "wird meist, außer in Extremfällen, erst nach einer Vorwarnung Gebrauch gemacht. </li>"
	. "<li>Admins dürfen zu Administrationszwecken die Benutzerdaten einsehen und ggf. die IP-Adresse des "
	. "Benutzers ermitteln, um die IP-Adresse oder den Provider zu sperren. </li>"
	. "<li>Der Betreiber des $chat behält sich das Recht vor, bei Verstößen gegen die Regeln von seinem"
	. " Hausrecht Gebrauch zu machen, den Benutzer aus dem $chat auszusperren und gegebenenfalls zur Anzeige "
	. "zu bringen. </li>"
	. "<li>Der Betreiber haftet ausdrücklich nicht für die Inhalte im $chat. </li>"
	. "<li>Mit dem Login oder der Registrierung in den Mainchat erklären Sie sich mit "
	. "der Datenspeicherung gemäß unserer <a href=\"index.php?aktion=datenschutz\" style=\"text-decoration: underline; \">Datenschutzerklärung</a> einverstanden.</li>"
	. "</ol>";

$t['pwneu1'] = "<p><b>Neues Passwort, 1. Schritt (von 3):</b> Sie haben Ihr Passwort vergessen? Kein Problem, geben Sie hier einfach Ihren "
	. "Benutzernamen oder E-Mail-Adresse an. Wenn beide Felder ausgefüllt sind, wird der Benutzername überprüft. Wenn der Benutzername nicht bekannt ist, bitte nur das Feld E-Mail-Adresse ausfüllen. Sie erhalten anschließend  "
	. "eine E-Mail mit einem Sicherheitscode, der Sie berechtigt ein neues Passwort anzufordern.</p>";
$t['pwneu2'] = "Benutzername";
$t['pwneu3'] = "E-Mail-Adresse";
$t['pwneu5'] = "Es wurde keine gültige E-Mail-Adresse angegeben!";
$t['pwneu6'] = "Es wurde kein Benutzer mit diesem Benutzernamen gefunden.";
$t['pwneu7'] = "<p><b>Neues Passwort, 2. Schritt (von 3):</b> An Ihre E-Mail-Adresse wurde ein Sicherheitscode gesendet. Geben Sie bitte diesen Sicherheitscode in das untere Feld ein.</p>";
$t['pwneu8'] = "Ihre Passwortanforderung für den $chat";
$t['pwneu9'] = "Hallo %nickname%,

Sie oder jemand unbefugtes möchte ein neues Passwort für Ihren Benutzernamen.

Anbei erhalten Sie den Sicherheitscode, mit dem Sie sich ein neues Passwort erzeugen können. Ohne diesen wird kein neues Passwort erzeugt.  
Geben Sie den Sicherheitscode bitte in das vorgegebene Feld ein - oder kopieren Sie es mit \"Copy and Paste\" (markieren, STRG+C und STRG+V)

Sicherheitscode: %hash%

-- 
Ihr $chat-Team  ";
$t['pwneu10'] = "Sicherheitscode";
$t['pwneu11'] = "Der angegebene Sicherheitscode ist leider nicht richtig.";
$t['pwneu12'] = "<p><b>Neues Passwort, 3. Schritt (von 3):</b> An Ihre E-Mail-Adresse wurde soeben ein neues Passwort gesendet.</p>";
$t['pwneu13'] = "<p><b>Fehler: Die E-Mail konnte nicht versandt werden. Das Passwort wurde nicht geändert!</b></p>";
$t['pwneu14'] = "Ihr neues Passwort für den $chat";
$t['pwneu15'] = "Hallo %nickname%,

Ihr Passwort wurde geändert. Ihr neues Passwort lautet: %passwort%

Viel Spaß noch im $chat";
$t['pwneu16'] = "Neues Passwort anfordern";
$t['pwneu17'] = "Es wurde weder ein Benutzername noch eine E-Mail-Adresse angegeben.";
$t['pwneu18'] = "Es wurde kein Benutzer mit dieser E-Mail-Adresse gefunden.";

$t['impressum1'] = "Impressum";
$t['impressum2'] = "Name und Anschrift";

$t['datenschutzerklaerung1'] = "Datenschutzerklärung";
$t['datenschutzerklaerung2'] = "<h2>
	1. Datenschutz auf einen Blick
</h2>
<h3>
	Allgemeine Hinweise
</h3>
<p>Die folgenden Hinweise geben einen einfachen Überblick darüber, was mit Ihren personenbezogenen Daten passiert, wenn Sie unsere Website besuchen. Personenbezogene Daten sind alle Daten, mit denen Sie persönlich identifiziert werden können. Ausführliche Informationen zum Thema Datenschutz entnehmen Sie unserer unter diesem Text aufgeführten Datenschutzerklärung.</p>
<h3>
	Datenerfassung auf unserer Website
</h3>
<p><strong>Wer ist verantwortlich für die Datenerfassung auf dieser Website?</strong></p>
<p>Die Datenverarbeitung auf dieser Website erfolgt durch den Websitebetreiber. Dessen Kontaktdaten können Sie dem Impressum dieser Website entnehmen.</p>
<p><strong>Wie erfassen wir Ihre Daten?</strong></p>
<p>Ihre Daten werden zum einen dadurch erhoben, dass Sie uns diese mitteilen. Hierbei kann es sich z.B. um Daten handeln, die Sie in ein Kontaktformular eingeben.</p>
<p>Andere Daten werden automatisch beim Besuch der Website durch unsere IT-Systeme erfasst. Das sind vor allem technische Daten (z.B. Internetbrowser, Betriebssystem oder Uhrzeit des Seitenaufrufs). Die Erfassung dieser Daten erfolgt automatisch, sobald Sie unsere Website betreten.</p>
<p><strong>Wofür nutzen wir Ihre Daten?</strong></p>
<p>Ein Teil der Daten wird erhoben, um eine fehlerfreie Bereitstellung der Website zu gewährleisten. Andere Daten können zur Analyse Ihres Nutzerverhaltens verwendet werden.</p>
<p><strong>Welche Rechte haben Sie bezüglich Ihrer Daten?</strong></p>
<p>Sie haben jederzeit das Recht unentgeltlich Auskunft über Herkunft, Empfänger und Zweck Ihrer gespeicherten personenbezogenen Daten zu erhalten. Sie haben außerdem ein Recht, die Berichtigung, Sperrung oder Löschung dieser Daten zu verlangen. Hierzu sowie zu weiteren Fragen zum Thema Datenschutz können Sie sich jederzeit unter der im Impressum angegebenen Adresse an uns wenden. Des Weiteren steht Ihnen ein Beschwerderecht bei der zuständigen Aufsichtsbehörde zu.</p>
<h3>
	Analyse-Tools und Tools von Drittanbietern
</h3>
<p>Beim Besuch unserer Website kann Ihr Surf-Verhalten statistisch ausgewertet werden. Das geschieht vor allem mit Cookies und mit sogenannten Analyseprogrammen. Die Analyse Ihres Surf-Verhaltens erfolgt in der Regel anonym; das Surf-Verhalten kann nicht zu Ihnen zurückverfolgt werden. Sie können dieser Analyse widersprechen oder sie durch die Nichtbenutzung bestimmter Tools verhindern. Detaillierte Informationen dazu finden Sie in der folgenden Datenschutzerklärung.</p>
<p>Sie können dieser Analyse widersprechen. Über die Widerspruchsmöglichkeiten werden wir Sie in dieser Datenschutzerklärung informieren.</p>
<h2>
	2. Allgemeine Hinweise und Pflichtinformationen
</h2>
<h3>
	Datenschutz
</h3>
<p>Die Betreiber dieser Seiten nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend der gesetzlichen Datenschutzvorschriften sowie dieser Datenschutzerklärung.</p>
<p>Wenn Sie diese Website benutzen, werden verschiedene personenbezogene Daten erhoben. Personenbezogene Daten sind Daten, mit denen Sie persönlich identifiziert werden können. Die vorliegende Datenschutzerklärung erläutert, welche Daten wir erheben und wofür wir sie nutzen. Sie erläutert auch, wie und zu welchem Zweck das geschieht.</p>
<p>Wir weisen darauf hin, dass die Datenübertragung im Internet (z.B. bei der Kommunikation per E-Mail) Sicherheitslücken aufweisen kann. Ein lückenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht möglich.</p>
<h3>
	Hinweis zur verantwortlichen Stelle
</h3>
<p>Die Datenverarbeitung auf dieser Website erfolgt durch den Websitebetreiber. Dessen Kontaktdaten können Sie dem Impressum dieser Website entnehmen.</p>
<p>Verantwortliche Stelle ist die natürliche oder juristische Person, die allein oder gemeinsam mit anderen über die Zwecke und Mittel der Verarbeitung von personenbezogenen Daten (z.B. Namen, E-Mail-Adressen o. Ä.) entscheidet.</p>
<h3>
	Widerruf Ihrer Einwilligung zur Datenverarbeitung
</h3>
<p>Viele Datenverarbeitungsvorgänge sind nur mit Ihrer ausdrücklichen Einwilligung möglich. Sie können eine bereits erteilte Einwilligung jederzeit widerrufen. Dazu reicht eine formlose Mitteilung per E-Mail an uns. Die Rechtmäßigkeit der bis zum Widerruf erfolgten Datenverarbeitung bleibt vom Widerruf unberührt.</p>
<h3>
	Recht auf Datenübertragbarkeit
</h3>
<p>Sie haben das Recht, Daten, die wir auf Grundlage Ihrer Einwilligung oder in Erfüllung eines Vertrags automatisiert verarbeiten, an sich oder an einen Dritten in einem gängigen, maschinenlesbaren Format aushändigen zu lassen. Sofern Sie die direkte Übertragung der Daten an einen anderen Verantwortlichen verlangen, erfolgt dies nur, soweit es technisch machbar ist.</p>
<h3>
	SSL- bzw. TLS-Verschlüsselung
</h3>
<p>Diese Seite nutzt aus Sicherheitsgründen und zum Schutz der Übertragung vertraulicher Inhalte, wie zum Beispiel Bestellungen oder Anfragen, die Sie an uns als Seitenbetreiber senden, eine SSL-bzw. TLS-Verschlüsselung. Eine verschlüsselte Verbindung erkennen Sie daran, dass die Adresszeile des Browsers von “http://” auf “https://” wechselt und an dem Schloss-Symbol in Ihrer Browserzeile.</p>
<p>Wenn die SSL- bzw. TLS-Verschlüsselung aktiviert ist, können die Daten, die Sie an uns übermitteln, nicht von Dritten mitgelesen werden.</p>
<h3>
	Auskunft, Sperrung, Löschung
</h3>
<p>Sie haben im Rahmen der geltenden gesetzlichen Bestimmungen jederzeit das Recht auf unentgeltliche Auskunft über Ihre gespeicherten personenbezogenen Daten, deren Herkunft und Empfänger und den Zweck der Datenverarbeitung und ggf. ein Recht auf Berichtigung, Sperrung oder Löschung dieser Daten. Hierzu sowie zu weiteren Fragen zum Thema personenbezogene Daten können Sie sich jederzeit unter der im Impressum angegebenen Adresse an uns wenden.</p>
<h2>
	3. Datenerfassung auf unserer Website
</h2>
<h3>
	Cookies
</h3>
<p>Die Internetseiten verwenden teilweise so genannte Cookies. Cookies richten auf Ihrem Rechner keinen Schaden an und enthalten keine Viren. Cookies dienen dazu, unser Angebot nutzerfreundlicher, effektiver und sicherer zu machen. Cookies sind kleine Textdateien, die auf Ihrem Rechner abgelegt werden und die Ihr Browser speichert.</p>
<p>Die meisten der von uns verwendeten Cookies sind so genannte “Session-Cookies”. Sie werden nach Ende Ihres Besuchs automatisch gelöscht. Andere Cookies bleiben auf Ihrem Endgerät gespeichert bis Sie diese löschen. Diese Cookies ermöglichen es uns, Ihren Browser beim nächsten Besuch wiederzuerkennen.</p>
<p>Sie können Ihren Browser so einstellen, dass Sie über das Setzen von Cookies informiert werden und Cookies nur im Einzelfall erlauben, die Annahme von Cookies für bestimmte Fälle oder generell ausschließen sowie das automatische Löschen der Cookies beim Schließen des Browser aktivieren. Bei der Deaktivierung von Cookies kann die Funktionalität dieser Website eingeschränkt sein.</p>
<p>Cookies, die zur Durchführung des elektronischen Kommunikationsvorgangs oder zur Bereitstellung bestimmter, von Ihnen erwünschter Funktionen (z.B. Warenkorbfunktion) erforderlich sind, werden auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO gespeichert. Der Websitebetreiber hat ein berechtigtes Interesse an der Speicherung von Cookies zur technisch fehlerfreien und optimierten Bereitstellung seiner Dienste. Soweit andere Cookies (z.B. Cookies zur Analyse Ihres Surfverhaltens) gespeichert werden, werden diese in dieser Datenschutzerklärung gesondert behandelt.</p>
<h3>
	Server-Log-Dateien
</h3>
<p>Der Provider der Seiten erhebt und speichert automatisch Informationen in so genannten Server-Log-Dateien, die Ihr Browser automatisch an uns übermittelt. Dies sind:</p>
<ul>
	<li>Browsertyp und Browserversion</li>
	<li>verwendetes Betriebssystem</li>
	<li>Referrer URL</li>
	<li>Hostname des zugreifenden Rechners</li>
	<li>Uhrzeit der Serveranfrage</li>
	<li>IP-Adresse</li>
</ul>
<p>Eine Zusammenführung dieser Daten mit anderen Datenquellen wird nicht vorgenommen.</p>
<p>Grundlage für die Datenverarbeitung ist Art. 6 Abs. 1 lit. b DSGVO, der die Verarbeitung von Daten zur Erfüllung eines Vertrags oder vorvertraglicher Maßnahmen gestattet.</p>
<h3>
	Kontaktformular
</h3>
<p>Wenn Sie uns per Kontaktformular Anfragen zukommen lassen, werden Ihre Angaben aus dem Anfrageformular inklusive der von Ihnen dort angegebenen Kontaktdaten zwecks Bearbeitung der Anfrage und für den Fall von Anschlussfragen bei uns gespeichert. Diese Daten geben wir nicht ohne Ihre Einwilligung weiter.</p>
<p>Die Verarbeitung der in das Kontaktformular eingegebenen Daten erfolgt somit ausschließlich auf Grundlage Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO). Sie können diese Einwilligung jederzeit widerrufen. Dazu reicht eine formlose Mitteilung per E-Mail an uns. Die Rechtmäßigkeit der bis zum Widerruf erfolgten Datenverarbeitungsvorgänge bleibt vom Widerruf unberührt.</p>
<p>Die von Ihnen im Kontaktformular eingegebenen Daten verbleiben bei uns, bis Sie uns zur Löschung auffordern, Ihre Einwilligung zur Speicherung widerrufen oder der Zweck für die Datenspeicherung entfällt (z.B. nach abgeschlossener Bearbeitung Ihrer Anfrage). Zwingende gesetzliche Bestimmungen – insbesondere Aufbewahrungsfristen – bleiben unberührt.</p>
<h3>
	Registrierung auf dieser Website
</h3>
<p>Sie können sich auf unserer Website registrieren, um zusätzliche Funktionen auf der Seite zu nutzen. Die dazu eingegebenen Daten verwenden wir nur zum Zwecke der Nutzung des jeweiligen Angebotes oder Dienstes, für den Sie sich registriert haben. Die bei der Registrierung abgefragten Pflichtangaben müssen vollständig angegeben werden. Anderenfalls werden wir die Registrierung ablehnen.</p>
<p>Für wichtige Änderungen etwa beim Angebotsumfang oder bei technisch notwendigen Änderungen nutzen wir die bei der Registrierung angegebene E-Mail-Adresse, um Sie auf diesem Wege zu informieren.</p>
<p>Die Verarbeitung der bei der Registrierung eingegebenen Daten erfolgt auf Grundlage Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO). Sie können eine von Ihnen erteilte Einwilligung jederzeit widerrufen. Dazu reicht eine formlose Mitteilung per E-Mail an uns. Die Rechtmäßigkeit der bereits erfolgten Datenverarbeitung bleibt vom Widerruf unberührt.</p>
<p>Die bei der Registrierung erfassten Daten werden von uns gespeichert, solange Sie auf unserer Website registriert sind und werden anschließend gelöscht. Gesetzliche Aufbewahrungsfristen bleiben unberührt.</p>
<h3>
	Kommentarfunktion auf dieser Website
</h3>
<p>Für die Kommentarfunktion auf dieser Seite werden neben Ihrem Kommentar auch Angaben zum Zeitpunkt der Erstellung des Kommentars, Ihre E-Mail-Adresse und, wenn Sie nicht anonym posten, der von Ihnen gewählte Nutzername gespeichert.</p>
<p><strong>Speicherung der IP-Adresse</strong></p>
<p>Unsere Kommentarfunktion speichert die IP-Adressen der Nutzer, die Kommentare verfassen. Da wir Kommentare auf unserer Seite nicht vor der Freischaltung prüfen, benötigen wir diese Daten, um im Falle von Rechtsverletzungen wie Beleidigungen oder Propaganda gegen den Verfasser vorgehen zu können.</p>
<p><strong>Abonnieren von Kommentaren</strong></p>
<p>Als Nutzer der Seite können Sie nach einer Anmeldung Kommentare abonnieren. Sie erhalten eine Bestätigungsemail, um zu prüfen, ob Sie der Inhaber der angegebenen E-Mail-Adresse sind. Sie können diese Funktion jederzeit über einen Link in den Info-Mails abbestellen. Die im Rahmen des Abonnierens von Kommentaren eingegebenen Daten werden in diesem Fall gelöscht; wenn Sie diese Daten für andere Zwecke und an anderer Stelle (z.B. Newsletterbestellung) an uns übermittelt haben, verbleiben die jedoch bei uns.</p>
<p><strong>Speicherdauer der Kommentare</strong></p>
<p>Die Kommentare und die damit verbundenen Daten (z.B. IP-Adresse) werden gespeichert und verbleiben auf unserer Website, bis der kommentierte Inhalt vollständig gelöscht wurde oder die Kommentare aus rechtlichen Gründen gelöscht werden müssen (z.B. beleidigende Kommentare).</p>
<p><strong>Rechtsgrundlage</strong></p>
<p>Die Speicherung der Kommentare erfolgt auf Grundlage Ihrer Einwilligung (Art. 6 Abs. 1 lit. a DSGVO). Sie können eine von Ihnen erteilte Einwilligung jederzeit widerrufen. Dazu reicht eine formlose Mitteilung per E-Mail an uns. Die Rechtmäßigkeit der bereits erfolgten Datenverarbeitungsvorgänge bleibt vom Widerruf unberührt.</p>
<p></p>";

$t['captcha1'] = "Prüfziffer: (Bitte Ergebnis der Rechenaufgabe als Zahl eingeben)";

$taufgabe[1] = "plus";
$taufgabe[2] = "minus";
$taufgabe[3] = "mal";

$tzahl[0] = "null";
$tzahl[1] = "eins";
$tzahl[2] = "zwei";
$tzahl[3] = "drei";
$tzahl[4] = "vier";
$tzahl[5] = "fünf";
$tzahl[6] = "sechs";
$tzahl[7] = "sieben";
$tzahl[8] = "acht";
$tzahl[9] = "neun";
$tzahl[10] = "zehn";
$tzahl[11] = "elf";
$tzahl[12] = "zwölf";
$tzahl[13] = "dreizehn";
$tzahl[14] = "vierzehn";
$tzahl[15] = "fünfzehn";
$tzahl[16] = "sechzehn";
$tzahl[17] = "siebzehn";
$tzahl[18] = "achtzehn";
$tzahl[19] = "neunzehn";
$tzahl[20] = "zwanzig";
$tzahl[21] = "einundzwanzig";
$tzahl[22] = "zweiundzwanzig";
$tzahl[23] = "dreiundzwanzig";
$tzahl[24] = "vierundzwanzig";
$tzahl[25] = "fünfundzwanzig";
$tzahl[26] = "sechsundzwanzig";
$tzahl[27] = "siebenundzwanzig";
$tzahl[28] = "achtundzwanzig";
$tzahl[29] = "neunundzwanzig";
$tzahl[30] = "dreißig";
$tzahl[31] = "einunddreißig";
$tzahl[32] = "zweiunddreißig";
$tzahl[33] = "dreiunddreißig";
$tzahl[34] = "vierunddreißig";
$tzahl[35] = "fünfunddreißig";
$tzahl[36] = "sechsunddreißig";
$tzahl[37] = "siebenunddreißig";
$tzahl[38] = "achtunddreißig";
$tzahl[39] = "neununddreißig";
$tzahl[40] = "vierzig";
$tzahl[41] = "einundvierzig";
$tzahl[42] = "zweiundvierzig";
$tzahl[43] = "dreiundvierzig";
$tzahl[44] = "vierundvierzig";
$tzahl[45] = "fünfundvierzig";
$tzahl[46] = "sechsundvierzig";
$tzahl[47] = "siebenundvierzig";
$tzahl[48] = "achtundvierzig";
$tzahl[49] = "neunundvierzig";
$tzahl[50] = "fünfzig";
$tzahl[51] = "einundfünfzig";
$tzahl[52] = "zweiundfünfzig";
$tzahl[53] = "dreiundfünfzig";
$tzahl[54] = "vierundfünfzig";
$tzahl[55] = "fünfundfünfzig";
$tzahl[56] = "sechsundfünfzig";
$tzahl[57] = "siebenundfünfzig";
$tzahl[58] = "achtundfünfzig";
$tzahl[59] = "neunundfünfzig";
$tzahl[60] = "sechzig";
$tzahl[61] = "einundsechzig";
$tzahl[62] = "zweiundsechzig";
$tzahl[63] = "dreiundsechzig";
$tzahl[64] = "vierundsechzig";
$tzahl[65] = "fünfundsechzig";
$tzahl[66] = "sechsundsechzig";
$tzahl[67] = "siebenundsechzig";
$tzahl[68] = "achtundsechzig";
$tzahl[69] = "neunundsechzig";
$tzahl[70] = "siebzig";
$tzahl[71] = "einundsiebzig";
$tzahl[72] = "zweiundsiebzig";
$tzahl[73] = "dreiundsiebzig";
$tzahl[74] = "vierundsiebzig";
$tzahl[75] = "fünfundsiebzig";
$tzahl[76] = "sechsundsiebzig";
$tzahl[77] = "siebenundsiebzig";
$tzahl[78] = "achtundsiebzig";
$tzahl[79] = "neunundsiebzig";
$tzahl[80] = "achtzig";
$tzahl[81] = "einundachtzig";
$tzahl[82] = "zweiundachtzig";
$tzahl[83] = "dreiundachtzig";
$tzahl[84] = "vierundachtzig";
$tzahl[85] = "fünfundachtzig";
$tzahl[86] = "sechsundachtzig";
$tzahl[87] = "siebenundachtzig";
$tzahl[88] = "achtundachtzig";
$tzahl[89] = "neunundachtzig";
$tzahl[90] = "neunzig";
$tzahl[91] = "einundneunzig";
$tzahl[92] = "zweiundneunzig";
$tzahl[93] = "dreiundneunzig";
$tzahl[94] = "vierundneunzig";
$tzahl[95] = "fünfundneunzig";
$tzahl[96] = "sechsundneunzig";
$tzahl[97] = "siebenundneunzig";
$tzahl[98] = "achtundneunzig";
$tzahl[99] = "neunundneunzig";

?>
