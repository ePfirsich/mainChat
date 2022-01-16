<?php

// Sprachdefinition deutsch functions.php

$t['functions_fehler_keine_datenbankverbindung'] = "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es später noch einmal!<br>";

$t['hinweis_fehler'] = "Fehler";
$t['hinweis_erfolgreich'] = "Erfolgreich";

$t['sperre1'] = "<b>Fehler:</b> Lobby kann nicht ermittelt werden!";
$t['sperre2'] = "gibt Benutzer";
$t['sperre3'] = "einen Schubs und wirft ihn/sie aus dem Raum!";
$t['sperre4'] = "<b>Fehler:</b> Benutzer '%s_user_name%' ist nicht in diesem Raum";
$t['sperre5'] = "den Zutritt frei.";
$t['sperre6'] = "<b>Fehler:</b> Aus der Lobby kann nur ein Admin Benutzer herauswerfen!";
$t['sperre7'] = "einen großen Schubs und wirft ihn/sie komplett aus dem Chat!";
$t['sperre8'] = "einen Schubs und wirft ihn/sie in den Raum";

$t['ignore1'] = "<b>$chat:</b> Benutzer '%i_user_name_passiv%' wird ab jetzt ignoriert.";
$t['ignore2'] = "<b>$chat:</b> Benutzer '%i_user_name_passiv%' wird von Benutzer '%i_user_name_aktiv%' ab jetzt ignoriert.";
$t['ignore3'] = "<b>$chat:</b> Benutzer '%i_user_name_passiv%' wird nicht länger ignoriert.";
$t['ignore4'] = "<b>$chat:</b> Benutzer '%i_user_name_passiv%' wird von Benutzer '%i_user_name_aktiv%' nicht mehr ignoriert.";
$t['ignore5'] = "<b>$chat:</b> Der Benutzer '%i_user_name_passiv%' ist Admin und kann nicht ignoriert werden!";

$t['raum_user1'] = "<b>Benutzer im Raum %r_name%:</b> ";
$t['raum_user12'] = "Da ist niemand.";

$t['betrete_chat1'] = "Eintrittsnachricht";
$t['betrete_chat2'] = "Willkommen";
$t['betrete_chat3'] = "Topic";

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

$t['raum_gehe1'] = "Austrittsnachricht";
$t['raum_gehe2'] = "<b>$chat:</b> Zur Info: Sie sind aus %r_name_neu% ausgesperrt.";
$t['raum_gehe3'] = "Eintrittsnachricht";
$t['raum_gehe4'] = "<b>$chat:</b> Raum %r_name_neu% kann nicht betreten werden!";
$t['raum_gehe5'] = "<b>$chat:</b> Sie sind leider aus %r_name_neu% ausgesperrt.";
$t['raum_gehe6'] = "Topic";
$t['raum_gehe7'] = "<b>$chat:</b> Sie haben leider zu wenig Punkte (mind. %r_min_punkte% nötig) um Raum %r_name_neu% zu betreten.";
$t['raum_gehe8'] = "<b>$chat:</b> Als Gast können Sie den Raum %r_name_neu% nicht betreten, da hierzu Punkte notwendig sind, die Sie als Gast nicht bekommen können. Melden Sie sich hierzu an.";

$t['chat_msg1'] = "Der Befehl '%chatzeile%' ist nur für Admins!";
$t['chat_msg4'] = "Sprüche gefunden:";
$t['chat_msg5'] = "<b>Fehler:</b> Bitte mindestens 3 Buchstaben als Suchwort abgeben!";
$t['chat_msg6'] = "'s letzte Worte:";
$t['chat_msg7'] = "<b>Fehler:</b> Sie können sich nicht selbst ignorieren!";
$t['chat_msg8'] = "<b>Fehler:</b> Benutzer '%chatzeile%' existiert nicht!";
$t['chat_msg9'] = "<b>$chat:</b> Benutzer '%u_nick%' ignoriert niemanden.";
$t['chat_msg10'] = "<b>$chat:</b> '%u_nick%' ignoriert ";
$t['chat_msg11'] = "<b>Fehler:</b> Raum '%chatzeile%' existiert nicht und ein neuer Raum kann nicht geöffnet werden!";
$t['chat_msg12'] = "<b>Räume:</b>";
$t['chat_msg13'] = "<b>$chat:</b> Der Benutzer '%s_user_name%' ist ein Admin und es ist nicht nett, Admins zu kicken!";
$t['chat_msg14'] = "<b>Fehler:</b> Benutzer '%chatzeile%' existiert nicht!";
$t['chat_msg15'] = "<b>Fehler:</b> Keine Rechte um jemanden aus diesem Raum zu werfen!";
$t['chat_msg16'] = "<b>In diesem Raum gesperrte Benutzer:</b>";
$t['chat_msg17'] = "In diesem Raum ist niemand gesperrt.";
$t['chat_msg21'] = "<b>$chat:</b> <span style=\"color:#%u_farbe%;\">Neue Farbe ist '%u_farbe%'.</span>";
$t['chat_msg22'] = "<b>$chat:</b> Aktuelle Farbe ist '%u_farbe%'.";
$t['chat_msg23'] = "<b>Fehler:</b> Bitte Farbe als RRGGBB (Rot-Grün-Blau) in Hexadezimal eingeben!";
$t['chat_msg24'] = "flüstert an";
$t['chat_msg25'] = "<b>Fehler:</b> Benutzer '%chatzeile%' ist nicht online!";
$t['chat_msg26'] = "<b>Fehler:</b> Benutzer '%chatzeile%' ist nicht online oder existiert nicht!";
$t['chat_msg32'] = "<b>$chat:</b> Benutzername wurde nicht ergänzt: <b>%nickkurz%</b> ist nicht eindeutig, im Chat sind: ";
$t['chat_msg33'] = "<b>$chat:</b> Bitte geben Sie für die Suche mit /whois mindestens 3 Buchstaben ein (% ist Joker).";
$t['chat_msg34'] = "<b>%user%</b> packt den Würfelbecher und wirft mit %wuerfel%: ";
$t['chat_msg35'] = "<b>$chat:</b> Beispiel: <b>/dice 3W6</b> wirft drei sechsseitige Würfel.";
$t['chat_msg36'] = "<b>Fehler: Spruch existiert schon: </b>";
$t['chat_msg37'] = "<b>Fehler:</b> Aufruf mit <b>/spruch spruchname anzahl_parameter &lt;Spruch&gt;<br>Beispiel:</b> /spruch lach 2 &lt;`0 lacht über `2 von `1.&gt;";
$t['chat_msg38'] = "<b>Fehler: &lt;anzahl_parameter&gt;</b> muss zwischen 0 und 2 liegen.";
$t['chat_msg39'] = "<b>Fehler:</b> Das Zeichen <b>`</b> darf nur als <b>`0</b>, <b>`1</b>, <b>`2</b> verwendet werden.";
$t['chat_msg40'] = "<b>Spruch eingetragen.</b> Aufruf mit =%spruch%";
$t['chat_msg41'] = "<b>Dateifehler beim Eintragen des Spruches aufgetreten. Sorry.</b>";
$t['chat_msg42'] = "<b>$chat:</b> Benutzername wurde nicht ergänzt: <b>%nickkurz%</b> ist nicht eindeutig, online in diesem Raum sind: ";
$t['chat_msg43'] = "<b>$chat:</b> Benutzername wurde nicht ergänzt: <b>%nickkurz%</b> ist nicht eindeutig, im Chat existieren: ";
$t['chat_msg44'] = "<b>Fehler:</b> Benutzer '%chatzeile%' ist nicht in diesem Raum!";
$t['chat_msg45'] = "<b>$chat:</b> alle doppelten IPs wurden gelistet.";
$t['chat_msg46'] = "<b>$chat:</b> keine doppelten IPs gefunden.";
$t['chat_msg47'] = "<b>$chat:</b> Die Nachricht wurde an die Admins weitergeleitet.";
$t['chat_msg48'] = "<b>$chat:</b> Die Nachricht konnte nicht weitergeleitet werden: kein Admin im Chat :-(";
$t['chat_msg49'] = "<b>$chat: Benutzer %user% aus Raum %raum% ruft um Hilfe:</b>";
$t['chat_msg50'] = "alle Admins";
$t['chat_msg51'] = "CHAT-Admins";
$t['chat_msg52'] = "an";
$t['chat_msg53'] = "Der Raum '%raumname%' existiert nicht.";
$t['chat_msg54'] = "<b>$chat:</b> Bitte nicht mehr als $smilies_anzahl Grafiken auswählen.";
$t['chat_msg55'] = "<b>Fehler:</b> Diese Funktion ist für Gäste nicht erlaubt. Bitte melden Sie sich mit Ihrem registrierten Benutzernamen an.";
$t['chat_msg56'] = "'s Raum";
$t['chat_msg64'] = "E-Mail";
$t['chat_msg65'] = "Admin E-Mail";
$t['chat_msg66'] = "Onlinezeit";
$t['chat_msg67'] = "Raum";
$t['chat_msg68'] = "Letzter Login";
$t['chat_msg76'] = "<b>Fehler:</b> In diesem Raum sind keine Smilie-Grafiken erlaubt.";
$t['chat_msg77'] = "<b>$chat:</b> Die Nachricht '%nachricht%' wurde nicht zugestellt.";
$t['chat_msg78'] = "flüstert an alle";
$t['chat_msg79'] = "<b>Fehler:</b> Es ist kein anderer Benutzer online.";
$t['chat_msg80'] = "<b>$chat:</b> Die Mail an '%chatzeile%' wurde verschickt.";
$t['chat_msg81'] = "<b>Fehler:</b> Bitte geben Sie einen Text für Ihre Mail ein.";
$t['chat_msg82'] = "<b>$chat:</b> '%u_nick%' wird von folgenden Benutzern ignoriert: ";
$t['chat_msg83'] = "<b>$chat:</b> '%u_nick%' ist jetzt Ihr Freund.";
$t['chat_msg84'] = "<b>$chat:</b> '%u_nick%' ist jetzt nicht mehr Ihr Freund.";
$t['chat_msg85'] = "<b>Freundesliste für %u_nick%. Ihre Freunde sind:</b>";
$t['chat_msg86'] = "<b>$chat:</b> Sie haben derzeit keine Freunde in Ihrer Freundesliste.";
$t['chat_msg89'] = "<b>Fehler:</b> Sie können sich nicht selbst zum Freund machen.";
$t['chat_msg90'] = "<b>$chat:</b> %u_nick% ist online im %raum%";
$t['chat_msg91'] = "%u_nick%\nonline %online% Std/Min/sek";
$t['chat_msg92'] = "[online %online% Std/Min/sek]";
$t['chat_msg93'] = "[offline]";
$t['chat_msg94'] = "[offline, letzter Login %login%]";
$t['chat_msg95'] = "<b>Fehler:</b> Benutzer '%chatzeile%' ist ein Gast oder gesperrt.";
$t['chat_msg96'] = "<b>$chat:</b> '%u_nick%' ist auf der Blacklist eingetragen.";
$t['chat_msg97'] = "<b>$chat:</b> '%u_nick%' ist von der Blacklist gelöscht.";
$t['chat_msg98'] = "<b>$chat:</b> Bitte geben Sie einen Benutzername an.";
$t['chat_msg99'] = "<b>Fehler:</b> Sie können sich nicht selbst in der Blackliste eintragen.";
$t['chat_msg100'] = "(Chatinfo) ";
$t['chat_msg101'] = "<b>$chat:</b> Sie werden nach %zeit% Minuten automatisch aus dem $chat ausgeloggt, wenn Sie nichts schreiben!";
$t['chat_msg103'] = "Sie werden von '%user%' ignoriert und dürfen ihm keine Mail schicken";
$t['chat_msg104'] = "Die Nachricht/E-Mail wurde nicht verschickt: Mailbombing-Schutz aktiv!";
$t['chat_msg106'] = "Derzeit liegen keine System- oder Privat-Nachrichten vor.<br>";
$t['chat_msg108'] = "<b>Fehler: </b> Um neue Räume anlegen zu dürfen, brauchen Sie mindestens %punkte% Punkte oder Sie müssen Admin sein!";
$t['chat_msg109'] = "Sie werden von '%nick%' ignoriert oder Sie ignorieren '%nick%' und können dem Benutzer daher keine privaten Nachrichten schicken";
$t['chat_msg114'] = "Heute ist der %datum% um %uhrzeit% Uhr Chat-Zeit.";
$t['freunde_fehlermeldung_ignoriert'] = "Der Benutzer '%u_nick%' kann nicht als Freund hinzugefügt werden, da Sie von diesem Benutzer ignoriert werden!<br>";
$t['freunde_fehlermeldung_gesperrt'] = "Der Benutzer '%u_nick%' kann nicht als Freund hinzugefügt werden, da dieser Benutzer gesperrt ist!<br>";
$t['freunde_fehlermeldung_gast'] = "Der Benutzer '%u_nick%' kann nicht als Freund hinzugefügt werden, da dieser Benutzer ein Gast ist!<br>";
$t['chat_msg119'] = "Aufruf: /ip username oder /ip www.xxx.yyy.zzz";
$t['chat_msg120'] = "Benutzer %u_nick% ist nicht online!";
$t['chat_msg121'] = "Keinen Benutzer mit passender IP gefunden!";
$t['chat_msg122'] = "Benutzer online mit der IP %ip% (%datum%, %uhrzeit%) ";
$t['chat_msg123'] = "Benutzer war mit IP %ip% da %datum% ";

$t['chat_spruch1'] = "<b>Fehler:</b> den Spruch '%spruchname%' gibt "
	. "es nicht als Typ 2 mit '%spruchname% USERNAME ZUSATZTEXT'. "
	. "Der Aufruf eines Spruchs ist immer bei Sprüchen des<br>"
	. "<b>Typ <I>0</I>: 'SPRUCHNAME'</b>, bei<br><b>Typ <I>1</I>: "
	. "'SPRUCHNAME USERNAME'</b> und bei<br><b>Typ <I>2</I>: "
	. "'SPRUCHNAME USERNAME ZUSATZTEXT'</b>";
$t['chat_spruch2'] = "<b>Fehler:</b> den Spruch '%spruchname%' gibt "
	. "es nicht als Typ 1 mit '%spruchname% USERNAME'. "
	. "Der Aufruf eines Spruchs ist immer bei Sprüchen des<br>"
	. "<b>Typ <I>0</I>: 'SPRUCHNAME'</b>, bei<br><b>Typ <I>1</I>: "
	. "'SPRUCHNAME USERNAME'</b> und bei<br><b>Typ <I>2</I>: "
	. "'SPRUCHNAME USERNAME ZUSATZTEXT'</b>";
$t['chat_spruch3'] = "<b>Fehler:</b> der Befehl oder Spruch '%spruchname%' " . "konnte nicht gefunden werden, %u_nick%.";
$t['chat_spruch4'] = "Variationen dieses Spruchs:";
$t['chat_spruch5'] = "<b>Fehler:</b> Befehl '%chatzeile%' existiert nicht! Sprüche werden mit =spruch abgerufen.";
$t['chat_spruch6'] = "zu";

$t['chat_lese1'] = "privat";

$t['benutzer_online'] = "Benutzer %user% (ONLINE)";
$t['benutzer_offline'] = "Benutzer %user% (OFFLINE)";
$t['benutzer_avatar_loeschen'] = "löschen";

$t['benutzer_punkte'] = "Punkte";
$t['benutzer_punkte_anzeige'] = "(Alle/%jahr%/%monat%)";

$t['user_kein_bild_hochgeladen'] = "<b>Kein Bild hochgeladen - Achtung: Nur Bilder im JPG, GIF und PNG Format hochladen! Kein BMP Format! Die Bilder dürfen nicht grösser als 60 Kb sein!</b><br>";

$t['away1'] = "ist gerade nicht da: ";
$t['away2'] = "ist wieder da. ";

$t['knebel1'] = "Geknebelte Benutzer";
$t['knebel2'] = "Aufruf: %chatzeile% [nick] [minuten]";
$t['knebel3'] = "'%admin%' winkt freundlich in die Runde und fordert '%user%' auf, doch auch wieder mal etwas zu sagen.";
$t['knebel4'] = "'%admin%' blickt besonders giftig zu '%user%'. '%user%' ist daraufhin vor Schreck für %zeit% Minuten sprachlos.";
$t['knebel5'] = "<b>$chat:</b> Der Benutzer '%admin%' ist ein Admin und es gehört sich nicht, Admins zu knebeln.";
$t['knebel6'] = "<b>$chat:</b> Du bist vor Schreck noch %zeit% sprachlos.";
$t['knebel7'] = "<b>$chat:</b> Du verschluckst Dich und bist vor Schreck sprachlos.";
$t['knebel8'] = "<b>$chat:</b> %user% wurde im Raum %raum% geknebelt <b>(Autoknebel)</b>.";
$t['knebel9'] = "<b>$chat:</b> Text:";
$t['knebel10'] = "<b>$chat:</b> Private Textnachricht - Knebelgrund:";

$t['invite1'] = "In diesem Raum sind eingeladen:";
$t['invite2'] = "In diesem Raum ist niemand eingeladen.";
$t['invite3'] = "'%admin%' hat '%user%' in den Raum '%raum%' eingeladen.";
$t['invite4'] = "'%admin%' hat '%user%' aus dem Raum '%raum%' ausgeladen.";
$t['invite5'] = "<b>$chat:</b> '%admin%' hat Sie in den Raum '%raum%' eingeladen. Mit '<b>/raum %raum%</b>' gelangen Sie dorthin. Wenn Sie 60 Sekunden warten, können Sie über die <b>Raumauswahl</b> (Anderen Raum betreten) in den Raum wechseln.";
$t['invite6'] = "<b>$chat:</b> Eine Beraterin läd Sie in das %raum% ein. Dort können Sie ein persönliches Beratungsgespräch unter vier Augen führen.<br><br>Mit '<b>/raum %raum%</b>' gelangen Sie dorthin. Wenn Sie 60 Sekunden warten, können Sie über die <b>Raumauswahl</b> (Anderen Raum betreten) in den Raum wechseln.";

$t['moderiert1'] = "<b>$chat:</b> Diese Funktion ist einem moderierten Raum nicht verfügbar, wenn ein Moderator anwesend ist.";
$t['moderiert2'] = "<b>$chat:</b> Ihre Eingabe wurde an den Moderator weitergeleitet.";

$t['moderiertdel1'] = "<b>$chat:</b> Ihre Eingabe wurde vom Moderator gelöscht: Diese Frage wurde (sinngemäß) bereits gestellt.";
$t['moderiertdel2'] = "<b>$chat:</b> Ihre Eingabe wurde vom Moderator gelöscht: Bitte freundlicher formulieren.";
$t['moderiertdel3'] = "<b>$chat:</b> Ihre Eingabe wurde vom Moderator gelöscht: Wir können leider nicht alle Fragen in der kurzen Zeit beantworten.";
$t['moderiertdel4'] = "<b>$chat:</b> Ihre Eingabe wurde vom Moderator gelöscht.";

$t['profil1'] = "<b>$chat:</b> Sie haben Ihr <a %link%>Profil</a> noch nicht ausgefüllt! Bitte klicken Sie &gt&gt<a %link%>HIER</a>&lt&lt";

$t['chatmsg_mail1'] = "<b>$chat:</b> Sie haben neue Nachrichten. <a %link%>Gleich lesen?</a>";
$t['chatmsg_mail2'] = " Nachricht von '%nick%' am %zeit%: %betreff%";
$t['chatmsg_freunde1'] = "<b>$chat:</b> Ein Freund ist da: %u_nick% betritt Raum %raum%";
$t['chatmsg_freunde2'] = "<b>$chat:</b> Ein Freund geht weg: %u_nick% verlässt den Chat";
$t['chatmsg_freunde5'] = "<b>$chat:</b> Ein Freund ist da: %u_nick% betritt das Forum";

$t['nachricht_mail5'] = "Hallo %user%, im Chat sind folgende Freunde online:\n\n";
$t['nachricht_mail6'] = "%anzahl% Freunde online im $chat";
$t['nachricht_mail9'] = "Hallo, über Ihre Freunde im Chat gibt es etwas neues:\n\n";
$t['nachricht_freunde3'] = "%u_nick% betritt Raum %raum%";
$t['nachricht_freunde4'] = "%u_nick% verlässt den Chat";
$t['nachricht_freunde6'] = "%u_nick% betritt das Forum";

$t['email_mail3'] = "[Mail-Weiterleitung aus dem $chat]\n\n";
$t['email_mail4'] = "Der Benutzer \"%name%\" hat folgende Nachricht gesendet:<br><br>
%nachricht%<br><br>
E-Mail Adresse: %email%<br>
Adresse des Chats: $chat_url";
$t['email_mail5'] = "Hallo %user%, im $chat sind folgende Freunde online:\n\n";
$t['email_mail6'] = "%anzahl% Freunde online im $chat";
$t['email_mail7'] = "<b>$chat:</b> Sie haben eine neue Nachricht von '%nick%' erhalten: %betreff%";
$t['email_mail8'] = "Hallo %user%, über Ihre Freunde im $chat gibt es etwas neues:\n\n";
$t['email_freunde3'] = "%u_nick% betritt Raum %raum% im $chat";
$t['email_freunde4'] = "%u_nick% verlässt den $chat";
$t['email_freunde6'] = "%u_nick% betritt das Forum im $chat";

$t['punkte1'] = "<b>$chat:</b> %text% Ihnen wurden %punkte% Punkte gutgeschrieben!";
$t['punkte2'] = "<b>$chat:</b> %text% Ihnen wurden %punkte% Punkte abgezogen!";
$t['punkte3'] = "Braver Benutzer! Großes Lob von %user%: ";
$t['punkte4'] = "Böser Benutzer! Sie wurden von %user% getadelt: ";
$t['punkte5'] = "Braver Benutzer! %user1% hat %user2% %punkte% Punkte gutgeschrieben!";
$t['punkte6'] = "Böser Benutzer! %user1% hat %user2% %punkte% Punkte abgezogen!";
$t['punkte7'] = "<b>Fehler:</b> Bitte mindestens einen Punkt vergeben!";
$t['punkte8'] = "%user1% hat %user2% %punkte% Punkte gutgeschrieben!";
$t['punkte9'] = "%user1% hat %user2% %punkte% Punkte abgezogen!";
$t['punkte10'] = "<b>Fehler:</b> Sie können sich selbst keine Punkte geben oder abziehen!";
$t['punkte11'] = "<b>Fehler:</b> Sie können nicht mehr als 1000 vergeben!";
$t['punkte12'] = "%user% haut sich eines mit der Peitsche über und verliert einen Punkt!";
$t['punkte13'] = "%user% denkt über den Sinn des Lebens nach und zieht sich %punkte% Punkte ab!";
$t['punkte14'] = "%user% gibt sich mit der Peitsche %punkte% Schläge auf den Rücken und verliert %punkte% Punkte!";
$t['punkte15'] = "%user% peitscht sich kräftig aus und verliert %punkte% Punkte!";
$t['punkte16'] = "%user% peitscht sich ekstatisch aus und verliert %punkte% Punkte!";
$t['punkte17'] = "%user% peitscht sich ekstatisch aus und verliert %punkte% Punkte (mehr, mehr, mehr...)!";
$t['punkte18'] = "%user% geht in ein Bordell und verliert %punkte% Punkte!";
$t['punkte19'] = "%user% geht in ein Casino und verliert %punkte% Punkte!";
$t['punkte20'] = "%user% muss zum Finanzamt und verliert %punkte% Punkte!";
$t['punkte21'] = "%user% wurden %punkte% Punkte gutgeschrieben!";
$t['punkte22'] = "%user% wurden %punkte% Punkte abgezogen!";
$t['punkte23'] = "Einem Gast können keine Punkte gutgeschrieben werden!";

//Texte für automatische generierung des Beitrags
$t['kopfzeile'] = "{autor} schrieb am {date}";
$t['gruss'] = "Mit freundlichen Grüßen";
$t['betrete_forum1'] = "<br><b>Willkommen im Forum, %u_nick%!</b>";
$t['betreff_new_posting'] = "Neue Antwort auf Ihren Beitrag %po_titel%";
$t['msg_new_posting_chatmail'] = "Es liegt eine neue Antwort auf Ihren Beitrag <b>%po_titel%</b> vom %po_ts% vor.<br><br><b>Pfad: </b>%forum% -> %thema% -> %baum%<br><br><b>Autor:</b> %user_from_nick%, <b>Titel:</b> \"%po_titel_antwort%\" vom %po_ts_antwort%<br><br>";
$t['msg_new_posting_email'] = "Es liegt eine neue Antwort auf Ihren Beitrag %po_titel% vom %po_ts% vor.\n\nPfad: %forum% -> %thema% -> %baum%\n\nAutor: %user_from_nick%, Titel: \"%po_titel_antwort%\" vom %po_ts_antwort%\n";
$t['msg_new_posting_olm'] = "Es liegt eine <b>neue Antwort</b> auf Ihren Beitrag <b>%po_titel%</b> vom %po_ts% (Forum: %forum%, Thema: %thema%) vor. <b>Autor:</b> %user_from_nick%, <b>Titel:</b> \"%po_titel_antwort%\" <b>vom</b> %po_ts_antwort%";
?>
