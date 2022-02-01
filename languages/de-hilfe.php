<?php
// Übersetzungen von "Hilfe"
$t['titel'] = "Hilfe";

$t['hilfe_menue4'] = "Übersicht";
$t['hilfe_menue5'] = "Liste aller Befehle";
$t['hilfe_menue6'] = "Liste aller Sprüche";
$t['hilfe_menue7'] = "Punkte/Community";

$t['hilfe_uebersicht_befehle'] = "Übersicht über alle Befehle im Chat";
$t['hilfe_allgemeines_format'] = "<b>Allgemeines Format:</b> /BEFEHL OPTION OPTION...";
$t['hilfe_sprueche'] = "Hilfe zu den voreingestellten Sprüchen";
$t['hilfe_uebersicht_sprueche'] = "Übersicht über alle voreingestellten Sprüche";
$t['hilfe_hilfe'] = "Hilfe";
$t['hilfe_zusaetzliche_befehle'] = "Zusätzliche Befehle für Admins";
$t['hilfe_community'] = "Community: Punkte, Profil, Mail, Freunde und Benutzerseite";
$t['hilfe_befehl'] = "Befehl";
$t['hilfe_funktion'] = "Funktion";
$t['hilfe_aliase'] = "Aliase";
$t['hilfe_anmerkung'] = "Anmerkungen";
$t['hilfe_spruch'] = "Spruch";
$t['hilfe_typ'] = "Typ";
$t['hilfe_text'] = "Text";

// Ausführlicher Hilfstext für die interaktive Hilfe
$hilfe_befehlstext = array(
	1 => "/user\tListet alle Benutzer im aktuellen Raum\t/wer, /who, /w, /list",
	"/user RAUM\tListet alle Benutzer im Raum RAUM auf (*=Alle)\t/wer, /who, /w, /list\tFalls RAUM=*, werden alle Räume gezeigt.",
	"/raum\tListet alle offenen Räume auf\t/channel, /go",
	"/raum RAUM\tWechselt in RAUM. Falls RAUM nicht existiert, wird er als temporärer Raum neu angelegt\t/channel, /go",
	"/raum RAUM !\tWechselt in RAUM, auch wenn der Raum geschlossen ist (nur Admins und Raumbesitzer)\tforce, immer\tBitte Privatsphäre beachten!",
	"/people\tListet alle offenen Räume mit Benutzern auf.",
	"/weg\tSetzt oder löscht einen ich-bin-nicht-da Text.\t/away, /afk\tBeispiel: /away ich telefoniere",
	"/msg NAME TEXT\tTEXT an Benutzer NAME flüstern\t/talk, /tell, /t\tDie Übermittlung ist privat und kann von niemandem mitgelesen werden",
	"/msgf TEXT\tTEXT an alle Freunde flüstern\t/tf",
	"/me TEXT\tSpruch an alle im Raum<br>Kommentar in die Runde\t/txt\tDer Text wendet sich nicht an einen bestimmten Benutzer, sondern ist für alle im Raum bestimmt",
	"/op TEXT\tRuft einen Admin\t \tDer Text wird an die Admins geschickt, die gerade online sind",
	"/ignoriere\tZeigt die ignorierten Benutzer an\t/ignore, /ig",
	"/ignoriere NAME\tIgnoriert Benutzer NAME\t/ignore, /ig\tNochmalige Eingabe gibt den Benutzer NAME wieder frei",
	"/kick NAME\tSperrt Benutzer NAME aus dem aktuellen Raum dauerhaft aus\t/plop\tNochmalige Eingabe gibt den Eintritt für diesen Benutzer wieder frei",
	"/quit TEXT\tBeendet den Chat mit den Worten TEXT (logoff).\t/exit, /ende\tDie Angabe von TEXT ist optional",
	"/hilfe\tListet die Kurzhilfe auf\t/help, /?",
	"/freunde NAME\tMacht NAME zu meinem Freund\t/freund, /buddy\tNochmalige Eingabe nimmt NAME aus der Freundesliste",
	"/einlad NAME\tLädt einen Benutzer in den Raum ein\t/invite\tNur für Admins oder Raumbesitzer",
	"/zeige NAME\tGibt die Benutzerinformationen für den Benutzer NAME aus <br>(% ist Joker).\t/whois",
	"/wuerfel\tWürfel werfen\t/dice\t/wuerfel 2W6 <br>wirft 2 sechseitige Würfel",
	"/dicecheck\tPrüft Würfelwürfe auf Korrektheit\t/quiddice\t/dicecheck 2W6 <br>prüft auf 2 sechseitige Würfel",
	"/suche NAME\tSucht alle Sprüche, die das Wort NAME enthalten\t/such\tGut geeignet, um einen Spruch passend zum Thema zu finden",
	"=spruch NAME ZUSATZ\tGibt einen vordefinierten Spruch aus (Siehe \"Liste aller Sprüche\")\t \tNAME und ZUSATZ ist optional und vom Spruch abhängig. Weitere Info vorhanden",
	"*TEXT*\tTEXT wird kursiv dargestellt\t \tWirkt in allen Texten, die öffentlich oder privat im Chat ausgegeben werden",
	"_TEXT_\tTEXT wird fett dargestellt\t \tWirkt in allen Texten, die öffentlich oder privat im Chat ausgegeben werden",
	"/time \tGibt die aktuelle Uhrzeit aus",
	"/farbe\tZeigt die aktuelle Farbe an\t/color",
	"/farbe RRGGBB\tSetzt neue Farbe auf RRGGBB\t/color\tRR steht für Rot, GG für Grün und BB für Blau, jeweils von 00 bis FF");

$hilfe_befehlstext_admin_ok = 1;
$hilfe_befehlstext_admin = array(
	1 => "/raum RAUM !\twechselt in RAUM, auch wenn der Raum geschlossen ist (nur Admins und Raumbesitzer)\t force, immer\tBitte Privatsphäre beachten!",
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
	. "Die meisten Einstellungen können durch <a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-befehle&id=$id\">"
	. "<b>Befehle im Chat</b></a> oder direkt im "
	. "Fenster (<b>Einstellungen</b>, "
	. "<b>Räume</b>) verändert werden. Für die voreingestellten Sprüche, "
	. "die im Chat abgerufen werden können, gibt es noch eine <a href=\"inhalt.php?bereich=hilfe&aktion=hilfe-sprueche&id=$id\">"
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
	. "Neben dem Passwort, das neu gesetzt werden kann, "
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
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"images/wuerfel/gruppe1.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 1.000 - 9.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"images/wuerfel/gruppe2.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: 10.000 - 99.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"images/wuerfel/gruppe3.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 100.000 - 199.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"images/wuerfel/gruppe4.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: 200.000 - 299.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"images/wuerfel/gruppe5.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 300.000 - 499.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"images/wuerfel/gruppe6.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: 500.000 - 749.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"images/wuerfel/gruppe7.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 750.000 - 999.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"images/wuerfel/gruppe8.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: 1.000.000 - 4.999.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<img src=\"images/wuerfel/gruppe9.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile2\">Benutzer: 5.000.000 - 9.999.999 Punkte</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<img src=\"images/wuerfel/gruppe10.gif\" style=\"width:12px; height:12px;\" alt=\"\"><br></td><td class=\"tabelle_zeile1\">Benutzer: ab 10.000.000 Punkten</td></tr>"
	. "<tr><td colspan=\"2\" class=\"tabelle_zeile2\" style=\"padding: 5px; font-weight:bold;\">Weitere Symbole:</td></tr>"
	. "<tr><td class=\"tabelle_zeile1\">&nbsp;<span class=\"fa fa-home icon16\" alt=\"Benutzerseite\" title=\"Benutzerseite\"></span><br></td><td class=\"tabelle_zeile1\">Benutzerseite des Benutzers</td></tr>"
	. "<tr><td class=\"tabelle_zeile2\">&nbsp;<span class=\"fa fa-envelope icon16\" alt=\"Nachricht\" title=\"Nachricht\"></span><br></td><td class=\"tabelle_zeile2\">Nachricht an den Benutzer</td></tr>";

$hilfe_community = "<br>
<table style=\"float:right;\">
" . $legende
. "
</table>
<p><b><a name=\"punkte\">Punkte</a></b><br>
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
<b><a name=\"profil\">Profile</a></b><br>
Unter dem Punkt \"<b>Profil</b>\" kann ebenfalls jeder Einblick in sein Profil
nehmen und es gegebenenfalls ändern. Hier kann der Benutzer Angaben über seinen Wohnort, Geburtsdatum, Geschlecht Hobbys und vieles mehr hinterlassen. Diese Profile sind im Chat öffentlich abrufbar.
Das Ausfüllen der Profile ist für die Benutzer freiwillig. Admins haben in Ihrem eigenen Profil noch die Möglichkeit, sich die Profile aller Benutzer ausgeben zu lassen.<br><br>
<b><A name=\"mail\">Chat-Mail</a></b><br>
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
auswählen, ob Sie die Nachricht als Nachricht im Chat des Benutzers oder an seine reguläre E-Mail Adresse schicken möchten, sofern der Benutzer dies in den Einstellungen erlaubt.
Sind Betreff und Textfeld fertig ausgefüllt, verschicken Sie die Mail mit Klick auf den
\"<b>Senden</b>\"-Button. Wenn Sie eine empfangene Mail löschen, so wird sie zuerst
einmal in den Papierkorb verschoben. Durch Klick auf den Menüpunkt
\"<b>Papierkorb zeigen</b>\" können Sie sich alle zum Löschen vorgesehenen Nachrichten noch
einmal anschauen. Wenn Sie nun sicher sind, dass sie auch alle im Papierkorb
befindlichen Nachrichten löschen wollen, klicken Sie auf \"<b>Papierkorb leeren</b>\".<br><br>
<b><a name=\"freunde\">Freunde</a></b><br>
Unter dem Punkt \"<b>Freunde</b>\" können Sie einfach andere Benutzer im Chat zu Freunden erklären oder aber aus der Freunde-Liste löschen. So wird
man stets über den Login/Logoff oder die Anwesenheit von Freunden im Chat informiert. Die Freunde-Liste ruft der Benutzer über den Punkt \"<b>USER</b>\" direkt
im Chat auf. Es erscheint ein Fenster, in dem sich auch das Main-Chat Menü befindet. Durch Klicken auf \"<b>meine Freunde</b>\" gelangt man nun auf die Liste
und kann ersehen, wen man sich als Freund eingetragen hat oder von wem man selbst als Freund aufgenommen wurde.<br><br>
<b><a name=\"aktionen\">Benachrichtigungen</a></b><br>
Über den Menüpunkt \"<b>Einstellungen</b>\" im Chat gelangen Sie zu den sogenannten
\"<b>Benachrichtigungen</b>\". Mit den Benachrichtigungen steuern Sie, bei welchen Ereignissen Sie mit
welcher Nachricht informiert werden wollen. Ein Ereignis ist z.B. der Login
oder Logout eines Freundes oder auch der Eingang einer neuen Mail. Wann Sie
die Nachricht erhalten wollen, wählen Sie aus der obersten Zeile aus.
Möglich ist der Empfang sofort wenn Sie online sind (<b>Sofort/Online</b>) (z.B.
Freund loggt ein/aus oder zu dem Moment, in dem Sie eine neue Mail
erhalten), bei Ihrem Login in den Chat oder regelmäßig <b>alle 5 Minuten</b>
(regelmäßige Information über die vorliegenden neuen Nachrichten oder die
Anwesendheit Ihrer Freunde im Chat) Die Benachrichtigungen, die Sie
erhalten, wenn Sie nicht im Chat sind (offline), wählen Sie unter
<b>Sofort/Offline</b> aus. Die Art der Nachricht ist einstellbar: so gibt es <b>keine</b>
Benachrichtigung, <b>Chat-Mail</b> (Nachricht im Chat), eine <b>E-Mail</b> an Ihre E-Mail Adresse oder eine <b>OLM</b> (OnLineMessage, direkte
Nachricht in Chat wie /msg). Zusätzlich sind auch Kombinationen von E-Mail
und OLM sowie Chat-Mail und OLM möglich, wobei Sie in diesem Fall zwei
Nachrichten erhalten.<br><br>
<b><a name=\"home\">Benutzerseite</a></b><br>
Unter dem Punkt \"<b>Profil</b>\" kann ebenfalls jeder Benutzer mit wenigen Klicks seine eigene kleine Benutzerseite erstellen.
Nach der \"<b>Aktivierung</b>\" der Benutzerseite wird hinter dem Benutzernamen ein <b>Haus-Symbol</b> angezeigt, über das man sich die Benutzerseite ansehen kann. In der
\"Suche nach Benutzern\" (<b>Benutzer -> Suche</b>) ist die gezielte Suche nach Benutzern mit aktivierter Benutzerseite möglich.<br><br>
Ihre Benutzerseite kann natürlich auch mit folgender Adresse von außerhalb des Chats abgerufen werden:<br>
<b> %chat_url%/home.php?/NICKNAME</b><br><br>
Grundsätzlich immer dargestellt werden</p>
<ul>
<li><i>Benutzername des Benutzers</i>
<li><i>Onlinezeit</i>
<li><i>Benutzerlevel</i>
<li><i>Punkte</i>
</ul><p>
	
Weiter kann der Benutzer entscheiden:</p>
<ul>
<li><i>welche Daten aus seinem Benutzerprofil auf der Benutzerseite dargestellt werden sollen.</i>
</ul>
<p>
<p>Außerdem hat er der die Möglichkeit:</p>
<ul>
<li><i>Schrift-, Hintergrund- und Linkfarben sowie Hintergrundbilder für seine Benutzerseite zu bestimmen,</i>
<li><i>beliebige Texte (auch mit HTML) in das Textfeld einzugeben,</i>
<li><i>Bilder von seinem eigenen Rechner hochzuladen und in seine Benutzerseite einzufügen.</i>
</ul>
</p>";
?>