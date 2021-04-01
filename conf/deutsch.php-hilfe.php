<?php

// Sprachdefinition deutsch hilfe.php

$CHATHOSTNAME = $HTTP_HOST . dirname($PHP_SELF);
if (substr($CHATHOSTNAME, -1) != "/")
    $CHATHOSTNAME .= "/";

$t['menue1'] = "Übersicht";
$t['menue2'] = "Liste aller Befehle";
$t['menue3'] = "Liste aller Sprüche";
$t['menue4'] = "$chat Hilfemenü";
$t['menue5'] = "Chatiquette";
$t['menue6'] = "Legende";
$t['menue7'] = "Punkte/Community/SMS";
$t['menue7b'] = "Punkte/Community";
$t['menue8'] = "Nutzungsbestimmungen";
$t['menue9'] = "Datenschutz";

$t['hilfe0'] = "Übersicht über alle Befehle im Chat";
$t['hilfe1'] = "<b>Allgemeines Format:</b> /BEFEHL OPTION OPTION...";
$t['hilfe2'] = "<TH><DIV style=\"color:$farbe_text;\">Befehl</DIV></TH><TH><DIV style=\"color:$farbe_text;\">Funktion</DIV></TH><TH><DIV style=\"color:$farbe_text;\">Aliase</DIV></TH><TH><DIV style=\"color:$farbe_text;\">Anmerkungen</DIV></TH>";
$t['hilfe3'] = "Hilfe zu den voreingestellten Sprüchen";
$t['hilfe4'] = "Übersicht über alle voreingestellten Sprüche";
$t['hilfe5'] = "<TH WIDTH=15%><DIV style=\"color:$farbe_text;\">Spruch</DIV></TH><TH WIDTH=10%><DIV style=\"color:$farbe_text;\">Typ</DIV></TH><TH WIDTH=75%><DIV style=\"color:$farbe_text;\">Text</DIV></TH>";
$t['hilfe6'] = "$chat Übersicht";
$t['hilfe7'] = "Bei Fragen oder Anregungen wenden Sie sich bitte an <a href=\"mailto:$webmaster\">$webmaster</a>.";
$t['hilfe8'] = "Zusätzliche Befehle für Admins";
$t['hilfe9'] = "Chatiquette";
$t['hilfe10'] = "Legende";
$t['hilfe11'] = "Community: Punkte, Profil, Mail, Freunde und Homepage";
$t['hilfe12'] = "<b>Weitere Hilfe zu den Punkten</b>";
$t['hilfe13'] = "Datenschutzrichtlinien (Datenschutzerklärung) der fidion GmbH, Betreiber des mainChats";
$t['hilfe14'] = "Nutzungsbestimmungen";
$t['hilfe15'] = "Automatischer Logout";
$t['hilfe16'] = "<P>Sie wurden automatisch aus dem $chat ausgelogt, weil Sie %zeit%&nbsp;Minuten lang nichts geschrieben haben!</b></P>";

$t['sonst1'] = "Fenster schließen";

// Ausführlicher Hilfstext für die interaktive Hilfe
$hilfe_befehlstext = array(
    1 => "/user\tListet alle User im aktuellen Raum\t/wer, /who, /w, /list",
    "/user RAUM\tListet alle User im Raum RAUM auf (*=Alle)\t/wer, /who, /w, /list\tFalls RAUM=*, werden alle Räume gezeigt.",
    "/raum\tListet alle offenen Räume auf\t/channel, /go",
    "/raum RAUM\tWechselt in RAUM. Falls RAUM nicht existiert, wird er als temporärer Raum neu angelegt\t/channel, /go",
    "/raum RAUM !\tWechselt in RAUM, auch wenn der Raum geschlossen ist (nur Admins und Raumbesitzer)\tforce, immer\tBitte Privatsphäre beachten!",
    "/people\tListet alle offenen Räume mit Usern auf.",
    "/weg\tSetzt oder löscht einen ich-bin-nicht-da Text.\t/away\tBeispiel: /away ich telefoniere",
    "/msg NAME TEXT\tTEXT an User NAME flüstern\t/talk, /tell, /t\tDie Übermittlung ist privat und kann von niemandem mitgelesen werden",
    "/msgf TEXT\tTEXT an alle Freunde flüstern\t/tf",
    "/mail NAME TEXT\tSchreibt Mail mit dem Inhalt TEXT an den User NAME\t/m\tDer Betreff entspricht den ersten Worten",
    "/me TEXT\tSpruch an alle im Raum<br>Kommentar in die Runde\t/txt\tDer Text wendet sich nicht an einen bestimmten User, sondern ist für alle im Raum bestimmt",
    "/op TEXT\tRuft einen Admin\t \tDer Text wird an die Admins geschickt, die gerade online sind",
    "/nick NAME\tSetzt Nickname auf NAME\t/name\tDer neue Nickname wird dauerhaft gespeichert",
    "/ignoriere\tZeigt die ignorierten User an\t/ignore, /ig",
    "/ignoriere NAME\tIgnoriert User NAME\t/ignore, /ig\tNochmalige Eingabe gibt den User NAME wieder frei",
    "/kick NAME\tSperrt User NAME aus dem aktuellen Raum dauerhaft aus\t/plop\tNochmalige Eingabe gibt den Eintritt für diesen User wieder frei",
    "/quit TEXT\tBeendet den Chat mit den Worten TEXT (logoff).\t/exit, /ende\tDie Angabe von TEXT ist optional",
    "/hilfe\tListet die Kurzhilfe auf\t/help, /?",
    "/freunde NAME\tMacht NAME zu meinem Freund\t/freund, /buddy\tNochmalige Eingabe nimmt NAME aus der Freundesliste",
    "/einlad NAME\tLädt einen User in den Raum ein\t/invite\tNur für Admins oder Raumbesitzer",
    "/uebergeberaum NAME\tÜbergibt den aktuellen Raum an den User NAME.\t/schenke\tNur für Admins oder Raumbesitzer",
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
    "/farbealle \tSetzt/zeigt die Farbe für andere User an\t/color2\tWerte in RRGGBB wie bei /farbe; abschalten mit \"/farbealle -\"",
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
    "/knebel\tKnebelt einen User\t/gag\t/gag idiot 10",
    "/gaga\tWie /gag, jedoch Anzeige aller im Chat geknebelten, nicht nur im eigenen Raum",
    "/einlad\tBearbeitet die geladenen User\t/invite\t/invite dilbert",
    "/zeige\tZeigt erweiterte Daten eines Users (% ist Joker)\t/whois\t/whois dilbert",
    "/analle TEXT\tTEXT an alle User flüstern, die online sind\t/toall\tNur für Admins",
    "/op\tPrivatnachricht an alle eingeloggten Admins\t\t/op tach.",
    "/oplist\tAuflisten aller eingeloggten Admins\t\t/oplist",
    "/lob USER PUNKTE\tLobt User: Er erhält Punkte gutgeschrieben\tDie Angabe von PUNKTE ist optional\t",
    "/tadel USER PUNKTE\tTadelt User: Er erhält Punkte abgezogen\tDie Angabe von PUNKTE ist optional\t/bestraf",
    "/blacklist USER\tSetzt User auf die Blacklist oder löscht ihn\t\t/blackliste",
    "/ip USER<br>/ip w.x.y.z\tZeigt die User zu einer IP\t\t",
    "/dupes\tZeigt doppelt eingeloggte User\t\t/dupes");

$hilfe_uebersichtstext = "<p><b>Erste Hilfe bei Problemen:</b><br>"
    . "Falls Sie im Chat keinen Text angezeigt bekommen oder von Zeit zu Zeit aus dem Chat fliegen, "
    . "können Sie in den <b><a href=\"hilfe.php?http_host=$http_host&id=$id&f[u_backup]=1\">sicheren Modus</a> umschalten</b>! Sie können "
    . "natürlich wieder in den <b><a href=\"hilfe.php?http_host=$http_host&id=$id&f[u_backup]=0\">Normalmodus</a> zurückschalten</b>. "
    . "Falls die <b>Ausgabe hängt</b> oder andere Fehler auftreten, können Sie mit "
    . "<b><a href=\"hilfe.php?http_host=$http_host&id=$id&reset=1\">[RESET]</a></b> "
    . "alle Fenster des Chats neu laden.</p>" . "<p><b>Popup-Blocker:</b><br>"
    . "Bitte beachten Sie, dass Popup-Blocker die Funktionalität des Chats beeinträchtigen können "
    . "und bei eingeschaltetem Popup-Blocker einige Funktionen nicht nutzbar sind. "
    . "Wir empfehlen Ihnen daher für den $chat den Popup-Blocker zu deaktivieren.</p>"
    . "<p><b>JavaScript:</b><br>"
    . "Bitte beachten Sie, dass die Bedienung des Chats mit JavaScript einfacher ist und schalten "
    . "Sie dieses bitte gegebenenfalls in Ihrem Browser ein.</p>"
    . "<p><b>Befehle:</b><br>"
    . "Die meisten Einstellungen können durch <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=befehle\">"
    . "<b>Befehle im Chat</b></A> oder direkt im "
    . "Fenster (<a href=\"edit.php?http_host=$http_host&id=$id\"><b>Einstellungen</b></A>, "
    . "<a href=\"raum.php?http_host=$http_host&id=$id\"><b>Räume</b></A>) verändert werden. Für die voreingestellten Sprüche, "
    . "die im Chat abgerufen werden können, gibt es noch eine <a href=\"hilfe.php?http_host=$http_host&id=$id&aktion=sprueche\">"
    . "<b>Übersicht</b></A>.</p>" . "<p><b>Liste:</b><br>"
    . "Im Chat, wie auch im Fenster (<a href=\"edit.php?http_host=$http_host&id=$id\"><b>Userliste</b></A>), "
    . "kann eine Liste aller in einem bestimmten "
    . "Raum chattenden User ausgegeben werden. Im Fenster ist es darüber hinaus möglich, "
    . "weitere Informationen abzufragen oder dem User eine private Nachricht zu schreiben.</p>"
    . "<p><b>Namen:</b><br>"
    . "Jeder User im Chat hat einen Usernamen und einen Nicknamen. "
    . "Mit beiden Namen ist der Login möglich; im Chat wird aber nur der Nickname angezeigt. "
    . "Der Nick darf keine Leerzeichen enthalten. Nick- wie auch Usernamen sind eindeutig, "
    . "also kann es keine zwei Benutzer mit demselben Namen geben." . "</p>"
    . "<p><b>Ergänzung des Nicks:</b><br>"
    . "Andere Benutzer können im Chat durch den Beginn des Nicks, gefolgt von einem Doppelpunkt, "
    . "gezielt angesprochen werden.<br>"
    . "<b>Aus dil: Hallo!</b> wird beispielsweise <b>[zu Dilbert] Hallo!</b><br>"
    . "Die Nickergänzung funktioniert unter anderem beim Chatten und beim Flüstern. "
    . "Ein @ vor dem Beginn des Nicknamens wird ebenfalls zum vollen Nick ergänzt.</p>"
    . "<p><b>Einstellungen:</b><br>"
    . "Jeder User kann optional eine E-Mail-Adresse und eine Homepage eintragen, "
    . "die in der Userliste im Fenster angezeigt werden. Neben dem Passwort, das neu gesetzt werden kann, "
    . "ist auch der Wechsel der Farbe durch Klick auf eines der bunten Felder möglich."
    . "<br>Zudem kann jeder User seine Systemeintrittsnachricht und -austrittsnachricht in den Raum ändern. (Dies "
    . "muss vom Chatbetreiber freigeschaltet sein.) Um hier automatisiert den eigenen Nicknamen einzutragen, kann man "
    . "<b>%nick%</b> eintragen. Um automatisiert den Raumnamen einzutragen, ist <b>%raum%</b> einzutragen. <br>"
    . "Weiterhin kann man, soweit im Chat die entsprechenden Features aktiviert sind, die Anzeige seines eigenen "
    . "Punktewürfels unterdrücken, die Anzeige der Smilies deaktivieren oder die Anzeige der Ein-/Austrittsnachrichten "
    . "unterdrücken." . "</p>" . "<p><b>Räume:</b><br>"
    . "Zum Wechsel in einen offenen Raum wählt man einfach den passenden Raum im Auswahlfeld "
    . "unter dem Chat-Eingabefeld aus. Hinter dem Raumnamen wird die Anzahl der User angezeigt, "
    . "die aktuell in diesem Raum chatten. Jeder User kann beliebig viele Räume aufmachen, "
    . "die er verändern darf. Nicht-permamente Räume werden aber automatisch nach Verlassen gelöscht. "
    . "Ein Besitzer eines Raums oder ein ChatAdmin darf den Raum umbenennen und den Ein- und "
    . "Austrittstext sowie den Raumstatus verändern. Einen offenen Raum darf jeder User betreten, "
    . "während ein geschlossener Raum nicht betreten werden darf und auch nicht angezeigt wird. "
    . "Ein Raum darf gelöscht werden; aber falls sich User darin aufhalten, werden diese in die Lobby geleitet. "
    . "Mit dem Befehl /kick können im Chat unerwünschte User aus einem Raum entfernt und der Wiedereintritt "
    . "automatisch damit verboten werden." . "</p>"
    . "<p><b><a href=\"user.php?http_host=$http_host&id=$id&aktion=chatuserliste\" TARGET=\"userliste\">Userliste</A>"
    . " im rechten Fenster neben dem Chat (@ > USERNAME)</b><br>"
    . "In der Userliste werden die Nicknamen aller User, die im aktuellen Raum online sind, "
    . "angezeigt. Das @ vor dem Nicknamen kopiert den Nicknamen in die Chat-Eingabezeile. "
    . "Dies ist sehr praktisch, um einen User <b>öffentlich</b> direkt anzusprechen. "
    . "Das > (Größer) vor den Nicknamen kopiert den Nicknamen in die Chat-Eingabezeile, "
    . "um diesen User <b>privat</b> anzuflüstern. Falls ein Nick in Klammern () steht, ist der "
    . "User gerade abwesend (/away). Ein Admin sieht vor dem Nick zusätzlich drei "
    . "Links <b>G K S</b> zur Ausführung der Befehle Gag (Knebel), Kick (Herauswurf) und "
    . "Sperre im Chat. Ein Raumbesitzer sieht nur den Link auf Kick.</p>"
    . "<p><b><a href=\"smilies.php?http_host=$http_host&id=$id\" TARGET=\"userliste\">Smileys-Liste</A>"
    . " im rechten Fenster neben dem Chat</b><br>"
    . "Das Fenster der Userliste kann auf eine Liste mit häufig benutzten Smileys umgeschaltet werden. "
    . "Ein Klick auf den Smiley kopiert ihn in die Eingabezeile. Sie können in Ihren Einstellungen "
    . "die Darstellung der Smileys als Grafiken unterdrücken. In jedem Raum lassen sich ebenfalls "
    . "die Grafiken unterdrücken (Smileys-Grafiken werden nur in ausgewählten mainChats angeboten).</p>"
    . "<p>"
    . "<b>Moderationsmodul für PromiChats (nur in ausgewählten mainChats)</b>"
    . "<ul>"
    . "<li>In moderierten Räumen kann ein moderierter Chat stattfinden. Alle öffentlichen Nachrichten werden "
    . "unterdrückt und an einen oder mehrere Moderatoren weitergeleitet, solange sich Moderatoren im Raum aufhalten. "
    . "<li>Die Moderatoren wählen eine oder mehrere Fragen aus und beantworten diese. Nach Beantwortung der Fragen werden"
    . " diese mit der Antwort im Chat veröffentlicht. "
    . "<li>Für die Moderatoren steht eine eigene Benutzeroberfläche in HTML zur Verfügung, mit der die Fragen der User schnell"
    . " und komfortabel durchgeschaltet, beantwortet oder unterdrückt werden können. "
    . "<li>Die Fragen, die ein Moderator zur Beantwortung ausgewählt hat, sind für alle anderen Moderatoren gesperrt. "
    . "<li>Öffentliche Ein- und Austrittsnachrichten werden in moderierten Räumen unterdrückt. "
    . "<li>Die Moderationsfunktionen sind nur in den Räumen aktiv, die auf den Status \"moderiert\" gesetzt sind."
    . "</ul>" . "</p>";

$chatiquette = "
<p><b>CHATIQUETTE im mainChat</b></p>

<p><b>Wie melde ich mich an? </b></p>
<p>Wenn man den Chat als Gast betritt, sollte man sich sofort einen Namen zulegen (/nick name), da die meisten User sich nur 
ungerne mit einer (austauschbaren) Zahl unterhalten. Im realen Leben wird man ja auch mit seinem Namen angesprochen. Über den
Namen ist man leichter ansprechbar und unverwechselbar.
Merkt man nach einiger Zeit, dass man öfter in den Chat kommen möchte, empfiehlt es sich, einen festen Account (Nicknamen) 
zuzulegen. Natürlich kann man das auch gleich machen, damit ist der Name dann besetzt und kein anderer kann sich mehr so
nennen. Hierzu klickt man auf der Startseite auf >>neu anmelden<< und bestätigt die Anmeldung, die per Mail zugesandt wird.
Dann wählt man sich seinen Chatnamen und sein Zugangspasswort aus.
</p>

<b><p>Wie verhalte ich mich im Chat richtig, wenn ich zum ersten Mal reingehe? </p></b>

<p>Zuerst verschafft man sich am besten eine Übersicht über die Situation im Chat. 
Wieviele User und Admins anwesend sind, welche Gespräche laufen, 
welche Stimmung allgemein herrscht und wie die User miteinander umgehen. </p>

<p>Um ins Gespräch einzusteigen, spricht man am besten jemanden direkt an (name: text) 
oder man gibt ein allgemeines Statement zu einem Thema ab und wartet dann, bis jemand auf einen eingeht.
Dann ist das Chatten nicht mehr schwer. </p>

<b><p>Was ist ein Admin? </p></b>
<p>Admin heißt Administrator (=\"Verwalter\")<br>
Admins im mainChat haben die Aufgabe, dafür zu sorgen, dass sich alle wohl fühlen.
Sie beantworten gerne Fragen zum Chat, helfen bei Problemen und werfen User aus dem Chat, 
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
Man ist anderen Usern gegenüber anonym; Admins jedoch können die IP-Adresse des Rechners, mit dem man online ist, sehen. 
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
Admins haben das Recht, User aus dem Chat zu werfen (\"kicken\"), wenn diese sich daneben benehmen.<br>
Davon wird meist, außer in Extremfällen, erst nach einer Vorwarnung Gebrauch gemacht. Sieht ein User nicht ein, dass er sich 
besser benehmen sollte, kann er auch gesperrt werden. In dem Fall wird die IP (Rechner-Adresse) blockiert, so dass sich der 
Betreffende nicht mehr in den Chat einloggen kann. Bei wiederholten Verstößen wird der Provider / die Bildungseinrichtung 
informiert und der User muss mit entsprechenden Konsequenzen rechnen. 
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

$legende = "<tr bgcolor=#E8E8FF><td colspan=\"2\"><p><b>Wieviele Chat-Punkte ergeben welchen Würfel?</b> </p></td></tr>"
    . "<tr bgcolor=#D8D8EE><td>&nbsp;<img src=\"pics/gruppe1.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 1.000 - 9.999 Punkte</td></tr>"
    . "<tr bgcolor=#E8E8FF><td>&nbsp;<img src=\"pics/gruppe2.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 10.000 - 99.999 Punkte</td></tr>"
    . "<tr bgcolor=#D8D8EE><td>&nbsp;<img src=\"pics/gruppe3.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 100.000 - 199.999 Punkte</td></tr>"
    . "<tr bgcolor=#E8E8FF><td>&nbsp;<img src=\"pics/gruppe4.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 200.000 - 299.999 Punkte</td></tr>"
    . "<tr bgcolor=#D8D8EE><td>&nbsp;<img src=\"pics/gruppe5.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 300.000 - 499.999 Punkte</td></tr>"
    . "<tr bgcolor=#E8E8FF><td>&nbsp;<img src=\"pics/gruppe6.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 500.000 - 749.999 Punkte</td></tr>"
    . "<tr bgcolor=#D8D8EE><td>&nbsp;<img src=\"pics/gruppe7.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 750.000 - 999.999 Punkte</td></tr>"
    . "<tr bgcolor=#E8E8FF><td>&nbsp;<img src=\"pics/gruppe8.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 1.000.000 - 4.999.999 Punkte</td></tr>"
    . "<tr bgcolor=#D8D8EE><td>&nbsp;<img src=\"pics/gruppe9.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: 5.000.000 - 9.999.999 Punkte</td></tr>"
    . "<tr bgcolor=#E8E8FF><td>&nbsp;<img src=\"pics/gruppe10.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>User: ab 10.000.000 Punkten</td></tr>"
    . "<tr bgcolor=#D8D8EE><td colspan=\"2\"><p><b>Weitere Symbole:</b> </p></td></tr>"
    . "<tr bgcolor=#E8E8FF><td>&nbsp;<img src=\"pics/haeuschen.gif\" width=\"12\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>Homepage des Users</td></tr>"
    . "<tr bgcolor=#D8D8EE><td>&nbsp;<img src=\"pics/mail.gif\" width=\"17\" height=\"12\" alt=\"\" border=\"0\"><br></td><td>E-Mail des Users</td></tr>";

$hilfe_community = "<br>
<TABLE ALIGN=RIGHT CELLPADDING=2 CELLSPACING=0 BORDER=0 BGCOLOR=$farbe_tabelle_kopf>
" . $legende
    . "
</table>
<P><b><A NAME=\"punkte\">Punkte</A></b><br>
Unter dem Chat-Menüpunkt \"<b>User</b>\" führt der Link zur User Top10/100 Liste. 
Alle angemeldeten User bekommen pro einzelnes im Chat geschriebenes Wort
einen Punkt. Die Voraussetzung ist, dass jedes Wort mindestens vier Buchstaben hat
und sich der User in einem öffentlichen, permanenten Raum mit mindestens 
" . $punkte_ab_user
    . " Usern befindet. Für das
erstmalige Ausfüllen des Profils bekommt jeder User 500 Punkte. Die Admins
haben ferner die Möglichkeit, den Usern Punkte zu schenken oder abzuziehen.
Dies geschieht mit den Befehlen \"<b>/lob Nickname Punktezahl</b>\" und \"<b>/tadel
Nickname Punktezahl</b>\". Wenn Sie dem User Heinz also 100 Punkte schenken
möchten, tippen Sie im Chat einfach \"/<b>lob Heinz 100</b>\" ein und schon bekommt
der User Heinz 100 Punkte gutgeschrieben. Beim Logout des Users werden die
Punktezahlen jeweils aufaddiert und aktualisiert. Mit Klick auf den
Menüpunkt Top10/100 kann jeder User Einblick in die Rangliste nehmen. Je
nach Anzahl der Punke erhält jeder User (sowie auch die Admins) ein
kleines Würfelsymbol, das seinem Nicknamen im Chat zugeordnet wird.<br><br>
<b>Tipp 1:</b> Die Punkte werden meistens erst beim Logout aufs Userkonto gutgeschrieben,
daher gibt's erst nach dem Login einen Würfel mit mehr Augen.<br>
<b>Tipp 2:</b> Wer um Punkte bettelt, macht sich unbeliebt.<br>
<b>Tipp 3:</b> Wer in leeren Räumen Texte schreibt, um die Punkte nach oben zu
treiben, hat schnell keine Punkte mehr.<br><br>
<b><a name=\"profil\">Profile</A></b><br>
Unter dem Punkt \"<b>Einstellungen</b>\" kann ebenfalls jeder Einblick in sein Profil
nehmen und es gegebenenfalls ändern. Hier kann der User Angaben über seine
Anschrift, Telefon-, Handynummer, ICQ, Geburtsdatum, Geschlecht, Hobbys und
vieles mehr hinterlassen. Diese Profile sind im Chat öffentlich abrufbar.
Das Ausfüllen der Profile ist für die User freiwillig. Admins haben in Ihrem
eigenen Profil noch die Möglichkeit, sich die Profile aller User ausgeben zu
lassen.<br><br>
<b><A NAME=\"mail\">Chat-Mail</A></b><br>
Die Chat-Mail (<b>MAIL</b>, <b>/mail</b>) ist eine im Chat integrierte Mailbox mit
Weboberfläche. Im Chat selbst steht hinter jedem Nick ein Mailsymbol (blauer
Briefumschlag). Somit kann jeder registrierte User im Chat anonym Mail
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
<b>Im Mailmenü haben Sie nun folgende Auswahlmöglichkeiten:</b></P>
<UL>
<li><I>Mailbox neu laden</I>
<li><I>Neue Mail schreiben</I>
<li><I>Papierkorb zeigen</I>
<li><I>Papierkorb leeren</I>
</ul><P>
Um eine neue Mail zu verfassen, geben Sie unter dem Punkt \"<b>Neue Mail senden</b>\"
in das Feld \"<b>Nickname</b>\" den Nicknamen des Users ein, dem Sie eine Nachricht
zukommen lassen möchten und klicken Sie dann auf \"<b>weiter</b>\". Nun befinden Sie
sich in der Texteingabe für Ihre Mail. Am Fuße der Box können Sie übrigens
auswählen, ob Sie die Nachricht an die Chat-interne Mailbox des Users oder
an seine reguläre E-Mail Adresse schicken möchten, sofern der User diese als
öffentlich in seinem Chatprofil angegeben hat. Sind Betreff und Textfeld
fertig ausgefüllt, verschicken Sie die Mail mit Klick auf den
\"<b>Senden</b>\"-Button. Wenn Sie eine empfangene Mail löschen, so wird sie zuerst
einmal in den Papierkorb verschoben. Durch Klick auf den Menüpunkt
\"<b>Papierkorb zeigen</b>\" können Sie sich alle zum Löschen vorgesehenen Mails noch
einmal anschauen. Wenn Sie nun sicher sind, dass sie auch alle im Papierkorb
befindlichen Mails löschen wollen, klicken Sie auf \"<b>Papierkorb leeren</b>\".<br><br>
<b><A NAME=\"freunde\">Freunde</A></b><br>
Bei den Freunden (<b>Einstellungen</b>, <b>/freunde</b>) können Sie einfach andere User im
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
<b><A NAME=\"home\">User-Homepages</A></b><br>
Unter dem Punkt \"<b>Einstellungen -> Homepage</b>\" kann ebenfalls jeder User mit
wenigen Klicks seine eigene kleine Homepage erstellen, ohne jegliche
HTML-Kenntnisse zu besitzen. Nach der \"<b>Freischaltung</b>\" der Homepage wird hinter dem 
Nicknamen ein <b>Haus-Symbol</b> angezeigt, über das man sich die Homepage ansehen kann. In der 
\"Suche nach Usern\" (<b>USER -> Suche</b>) ist die gezielte Suche nach Usern mit freigeschalteter Homepage
möglich.<br><br>
Ihre Homepage kann natürlich auch mit folgender Adresse von außerhalb des Chats abgerufen werden:<br>
<b>http://" . $CHATHOSTNAME
    . "home.php/NICKNAME</b><br><br>
Grundsätzlich immer dargestellt werden</P>
<ul>
<li><I>Nickname des Users</I>
<li><I>Onlinezeit</I>
<li><I>Userlevel</I>
<li><I>Punkte</I>
</ul><P>
    
Weiter kann der Chatuser entscheiden:</P>
<UL>
<li><I>welche Daten aus seinem Userprofil auf der Homepage dargestellt werden sollen.</I>
</UL>
<P>
<P>Außerdem hat er der die Möglichkeit:</P>
<UL>
<li><I>Schrift-, Hintergrund- und Linkfarben sowie Hintergrundbilder für seine Homepage zu bestimmen,</I>
<li><I>beliebige Texte (auch mit HTML) in das Textfeld einzugeben,</I>
<li><I>Bilder von seinem eigenen Rechner hochzuladen und in seine Homepage einzufügen.</I>
</UL>
</P>";

$hilfe_privacy = "<P>
Personenbezogene Daten sind Informationen, die dazu genutzt werden können,
Ihre Identität zu erfahren. Im Chat sind dies Informationen wie Ihr
richtiger Name, Ihre Adresse oder Ihre Telefonnummer, die Sie im Profil
hinterlegen können. Sie entscheiden in den Einstellungen Ihrer Homepage
frei, ob und welche der personenbezogenen Daten Sie auf Ihrer Homepage
veröffentlichen. Auch die Offenlegung Ihrer personenbezogenen Daten in Ihrem
Profil ist natürlich freiwillig.
</p><p>
Automatisch erstellte Statistiken, wie die User-Top10/100, fallen nicht
unter die personenbezogenen Daten. Ebenfalls ausgenommen ist die Darstellung
Ihres Nicknamens, Ihrer öffentlichen E-Mail Adresse, Ihrer Punkte, Ihrer
Onlinezeit und der Zeitpunkt des ersten und letzten Logins in den Chat.
Falls Sie keine öffentliche E-Mail Adresse wünschen, löschen Sie diese in
Ihren <b>Einstellungen</b>.
</p><p>
Wir speichern Ihre Daten auf eigenen Servern, auf die nur unsere
Mitarbeiter Zugriff haben.  Bei Ihrem Login werden auf unseren Servern
Daten, wie IP-Adresse, Datum und Uhrzeit, für Sicherungszwecke gespeichert. 
Es findet keine personenbezogene Verwertung statt, wobei die statistische
Auswertung anonymisierter Datensätze möglich ist.
</p><p>
Wir geben keine personenbezogenen Daten an Dritte weiter oder verkaufen
diese. Soweit wir gesetzlich oder per Gerichtsbeschluss dazu verpflichtet 
sind, werden wir Ihre Daten an auskunftsberechtigte Stellen übermitteln.
Die personenbezogenen Daten, die Sie in Ihr Profil eingegeben haben, können
Sie jederzeit wieder löschen.
</p><p>
Die ehrenamtlichen Admins (Chatadmin, Superuser) des Chats genießen eine besondere
Vertrauensstellung, da sie Zugriff auf personenbezogene Daten, wie Ihren
richtigen Name, Ihre Admin E-Mail Adresse und Ihre IP-Adressen mit Uhrzeit
und Datum haben.  Diese Daten dürfen ausschließlich dazu genutzt werden,
User, die gegen die Nutzungsbestimmungen verstoßen, zu identifizieren, aus dem Chat zu
entfernen und auch dauerhaft auszusperren. Die Admins haben auch das Recht,
zu diesen Zwecken einen Traceroute auf Ihren Rechner zu machen, den falsch
konfigurierte \"Personal Firewalls\" eventuell als Angriff erkennen können.
Auf Ihr Profil haben die Admins keinen Zugriff. Da Ihr Passwort
verschlüsselt gespeichert wird, können daher weder wir noch die Admins Ihr
Passwort lesen. Die Admin können allerdings die Passwörter der User auf
besonderen Wunsch ändern und das neue Passwort an die hinterlegte 
Admin-E-Mail Adresse schicken. Die Admin-E-Mail Adresse ist nur
durch Admins änderbar. Die Admins sind verpflichtet, sich an diese
Datenschutzbestimmungen und an die Nutzungsbestimmungen zu halten und keine
personenbezogenen Daten an Dritte weiterzugeben.
</P><P>
Wir setzen im Chat prinzipiell keine Cookies. Falls Sie dennoch ein Cookie 
erhalten, stammt dieses Cookie aus der Anzeige einer Agentur, auf die wir keinen
Einfluss haben. Bitte deaktivieren Sie Cookies in Ihrem Browser, wenn Sie keine
Cookies empfangen möchten.
</P>";
?>
