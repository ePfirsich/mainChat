<?php
// Sprachdefinition deutsch index.php

$t['willkommen1'] = "<P><span style=\"font-size: large\"><b>Hallo %PHP_AUTH_USER%, willkommen im $chat!</b></span></P>\n";
$t['willkommen2'] = "<P><span style=\"font-size: large\"><b>Willkommen im $chat!</b></span></P>\n";

$t['login1'] = "<b>Login mit sicherer HTTPS-Anmeldung</b>";
$t['login2'] = "<b>neue HTTPS-Anmeldung</b>";
$t['login3'] = "Gäste können sich einloggen, in dem "
    . "sie einfach auf <b>Login</b> klicken " . "ohne einen Namen einzugeben";
$t['login4'] = "<P><b>Der Chat ist im Moment aus technischen Gründen geschlossen! "
    . "Wir bedauern dies sehr und arbeiten daran.<br><br> Bitte versuchen "
    . "Sie es später wieder.</b></P><br><br>\n";
$t['login5'] = "<P><b>Leider ist der Account %u_nick% (%u_name%) derzeit gesperrt.\n"
    . "Bitte wenden Sie sich an unseren <A HREF=\"MAILTO:" . $webmaster
    . "\">Webmaster</A>.</b></P><br><br>\n";
$t['login6'] = "<H2>Ihr Browser unterstützt keine Frames. Ohne Frames kein $chat :-)</H2>\n"
    . "</noframes></body></html>\n";
$t['login7'] = "<P><b>Falsches Passwort oder Nickname eingegeben! Bitte versuchen Sie es neu:</b></P>\n";
$t['login8'] = "Name/Nick:";
$t['login9'] = "Passwort:";
$t['login10'] = "Login";
$t['login11'] = "Login als";
$t['login12'] = "Raum:";
$t['login13'] = "Gast";
$t['login14'] = "als registrierter User neu anmelden";
$t['login15'] = "<P><b>Leider ist der Login als Gast derzeit gesperrt. Bitte haben Sie dafür Verständnis.</b></P>";
$t['login16'] = "<P><b>Leider ist kein Login als Gast möglich. Bitte melden Sie sich als registrierter User an.</b></P>";
$t['login17'] = "Ich erkenne diese Bedingungen an";
$t['login18'] = "Abbruch";
$t['login19'] = "<P><b>Dieser Nickname ist durch ein Passwort geschützt! Bitte versuchen Sie es neu:</b></P>\n";
$t['login20'] = "Zu viele Fehlversuche beim Login. Der Account %login% wurde für einige Zeit gesperrt!";
$t['login21'] = "Zu viele Fehlversuche beim Login. Der Account %login% wurde für einige Zeit gesperrt!\n\n"
    . "Hinweis: Diese Mail wurde automatisch erzeugt und wird an den Webmaster verschickt. "
    . "Über http://www.ripe.net/perl/whois können Sie unter Angabe der IP-Adresse den "
    . "Provider ermitteln, über den der Loginversuch stattfand. "
    . "Um den Verursacher zu ermitteln, wenden Sie sich bitte unter Angabe der IP und des "
    . "Datums/Uhrzeit direkt an den Provider.\n";
$t['login22'] = "Raum/Forum:";
$t['login23'] = "Beiträge im Forum zeigen";
$t['login24'] = "<P><b>Der Login ist leider nicht möglich!</b></P>"
    . "<P>Es sind im $chat bereits %online% User online. "
    . "Als %leveltxt% dürfen Sie ab maximal %max% User den $chat nicht mehr betreten.</P>"
    . "%zusatztext%";
$t['login25'] = "<P><b>Fehler beim Login:</b><br>Der Login als Admin (Superuser oder Chatadmin) "
    . "ohne aktivierte Cookies ist aus Sicherheitsgründen nicht gestattet. Bitte verwenden "
    . "Sie einen Browser mit aktivieren Cookies "
    . "</P><P>[<A HREF=\"%url%\">weiter zur Loginseite</A>]</P>";
$t['login26'] = "Lieber Chatter, um diesen Chat zu betreten, müssen Sie ihn über die Webseite %webseite% betreten.";
$t['login27'] = "Passwort vergessen?";

$t['neu1'] = "<P><b>Bitte das Feld 'Name' ausfüllen!</b></P>\n";
$t['neu2'] = "<P><b>Bitte geben Sie als Usernamen mindestens 4 Zeichen ein!</b></P>\n";
$t['neu3'] = "<P><b>Bitte geben Sie als Usernamen maximal 20 Zeichen ein!</b></P>\n";
$t['neu4'] = "<P><b>Bitte geben Sie als Nicknamen mindestens 4 und maximal 20 gültige Zeichen ein!</b><br>Die gültigen Zeichen sind: %zeichen%</P>\n";
$t['neu5'] = "<P><b>Bitte das Feld 'Passwort' mit mindestens 4 Zeichen ausfüllen!</b></P>\n";
$t['neu6'] = "<P><b>Sie haben sich beim Passwort vertippt. Bitte neu versuchen!</b></P>\n";
$t['neu7'] = "<P><b>Bitte im Feld 'E-Mail (nur intern)' eine gültige E-Mail Adresse eingeben!</b></P>\n";
$t['neu8'] = "<P><b>Bitte im Feld 'E-Mail (öffentlich)' eine gültige E-Mail Adresse eingeben!</b></P>\n";
$t['neu9'] = "<P><b>Dieser Nickname ist leider schon vergeben! Bitte wählen Sie einen anderen.</b></P>\n";
$t['neu10'] = "Vor- & Nachname:";
$t['neu11'] = "(nur intern)";
$t['neu12'] = "NickName:";
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
$t['neu23'] = "<P>Bitte beachten Sie, daß Ihr <b>Name</b> und Ihre <b>E-Mail</b>\n"
    . "nur für interne Zwecke der Administration genutzt und dritten\n"
    . "Personen nicht zugänglich gemacht wird (gem. <a href=\"http://"
    . $HTTP_HOST . "/hilfe.php?http_host=" . $HTTP_HOST
    . "&id=&aktion=privacy\">Privacy Policy</a>). Falls Sie zum Beispiel Ihr Passwort\n"
    . "vergessen und uns kontaktieren, können wir Ihnen an diese Adresse Ihr "
    . "neues Passwort schicken. Die 2. optionale E-Mail Adresse (öffentlich) dagegen ist\n"
    . "im Chat für alle anderen Mitglieder sichtbar.</P>\n"
    . "<P>Alle Felder mit <b>*</b> sind Pflichtfelder. Die Felder <b>E-Mail (öffentlich)</b> und\n"
    . "<b>Homepage</b> sind öffentlich und müssen nicht ausgefüllt werden.\n"
    . "Falls Sie <b>Nickname</b> nicht ausfüllen, wird Ihr Name aus dem Feld\n"
    . "<b>Name</b> als öffentlicher\n"
    . "Nickname automatisch eingesetzt.</P>\n"
    . "<P>Hier können Sie vorab unsere <a href=\"http://" . $HTTP_HOST
    . "/hilfe.php?http_host=" . $HTTP_HOST
    . "&id=&aktion=agb\">AGBs</a> lesen. Diese müssen vor dem ersten Login bestätigt und akzeptiert werden.</P>\n";
$t['neu24'] = "<P><b>Wir freuen uns, Sie als neues Mitglied im $chat begrüßen zu dürfen!</b></P><br><br>\n";
$t['neu25'] = "<P>Sie wurden nun im $chat eingetragen!</P>\n";
$t['neu26'] = "Vor- & Nachname";
$t['neu27'] = "NickName";
$t['neu28'] = "<P><b>Bitte merken Sie sich Ihr Passwort, denn ohne Passwort kommen Sie nicht mehr in den Chat....</b></P>\n";
$t['neu29'] = "Weiter zum Chat";
$t['neu30'] = "Eintrag abgeschlossen";
$t['neu31'] = "Neu im $chat anmelden:";
$t['neu32'] = "<P><b>Der Name '%gast%....' ist für Gast-Accounts reserviert. Bitte wählen Sie einen anderen Nicknamen!</b></P>";
$t['neu33'] = "<P><b>Registrierung, 1. Schritt:</b> Um sich neu für den $chat zu registrieren, geben Sie bitte Ihre E-Mail Adresse ein:</P>";
$t['neu34'] = "Ihre E-Mail Adresse:";
$t['neu35'] = "Absenden";
$t['neu36'] = "Willkommen beim $chat!\n\nUm nun die Registrierung als neuer User fortzusetzen, klicken Sie bitte auf den folgenden Link:"
    . "\n\n%link% \n\nSollte es zu Problemen beim klicken auf den Registrierungslink kommen, so verwenden Sie diesen Link zum manuellen "
    . " Prüfen der E-Mailadresse:\n %link2%\n\n"
    . "Ihre E-Mailadresse: %email%\n" . "Freischalt-Code   : %hash%\n\n"
    . "\nWenn Sie diese Links nicht in Ihrem E-Mailprogramm anklicken können, so kopieren Sie diese in die Zwischenablage und fügen "
    . "Sie den Link dann in den Browser ein\n\n"
    . "-- \n   $chat ($serverprotokoll://" . $HTTP_HOST . $PHP_SELF . ")\n";
$t['neu37'] = "<P><b>Registrierung, 2. Schritt:</b> Sie erhalten nun eine E-Mail! Um die Registrierung abzuschließen, klicken Sie bitte auf den dort angegebenen Link!</P>";
$t['neu38'] = "Ihre Registrierung im $chat";
$t['neu39'] = "<P><b>Fehler: </b>An die E-Mail Adresse '%email%' wurde bereits verschickt!</P>";
$t['neu40'] = "Diese E-Mailadresse ist leider gesperrt! Bitte wenden Sie sich an den Webmaster";
$t['neu41'] = "<P><b>Fehler: </b>Die Eingabe '%email%' ist keine gültige  E-Mail Adresse!</P>";
$t['neu42'] = "Bitte geben Sie nun Ihre <b>E-Mailadresse</b> und den <b>Freischalt-Code</b> ein, den Sie soeben per E-Mail bekommen haben.<br>"
    . "Sie können hierzu natürlich auch gerne Cut und Paste (Strg+C und Strg+V) verwenden";
$t['neu43'] = "Freischalt-Code:";
$t['neu44'] = "<P><b>Fehler: </b>Pluszeichen nicht im Nicknamen erlaubt!";
$t['neu45'] = "<P>Anmerkung: Sie erhalten nicht sofort einen E-Mail. Ihre Anmeldung muss erst vom Webmaster bestätigt werden.</P>";
$t['neu46'] = "Anfrage um Registrierung im $chat";
$t['neu47'] = "Hallo!\n\nEin User mit der E-Mailadresse %email% möchte sich in Ihrem Chat $chat anmelden."
    . "\nZum freigeben dieser E-Mailadresse verwenden Sie bitte diesen Link:\n\n %link1%\n\n"
    . "Freizugebende E-Mailadresse: %email%\n" . "Freischalt-Code: %hash%\n\n"
    . "Dieser User erhält dann automatisch eine Mail mit einem Link mit welchem er/sie sich anmelden kann.\n\n"
    . "Möchten Sie dem User auf andere Weise den Freischalt-Code mitteilen, so sind folgende Angaben "
    . "an den User zu übermitteln:\n\n"
    . "Internetadresse zum Freischalten: %link2%\n"
    . "E-Mailadresse: %email%\n" . "Freischalt-Code: %hash%\n\n"
    . "-- \n   $chat ($serverprotokoll://" . $HTTP_HOST . $PHP_SELF . ")\n";
$t['neu48'] = "<P><b>Registrierung, 2. Schritt:</b> Warten Sie bitte nun, bis Sie vom Webmaster eine E-Mail mit Ihrem "
    . "Freischaltcode erhalten! Um dann die Registrierung abzuschließen, benutzen Sie bitte den dort "
    . "angegebenen Link!</P>";
$t['neu49'] = "Freizugebende E-Mailadresse:";
$t['neu50'] = "Bitte geben Sie nun die <b>E-Mailadresse</b>, die Sie freigeben möchten, und den <b>Freischalt-Code</b> ein, "
    . "den Sie soeben per E-Mail bekommen haben.<br>"
    . "Sie können hierzu natürlich auch gerne Copy und Paste (Strg+C und Strg+V) verwenden";
$t['neu51'] = "E-Mailadresse falsch!";
$t['neu52'] = "Freischaltcode falsch!";
$t['neu53'] = "Willkommen beim $chat!\n\nDer Webmaster hat Ihre E-Mailadresse nun freigeschaltet."
    . "\nZum endgültigen Anmelden verwenden Sie bitte nun den Link: "
    . "\n %link%\n\nZum anmelden benötigen Sie dann noch:\n"
    . "Ihre E-Mailadresse: %email%\n" . "Ihren Freischalt-Code: %hash%\n\n"
    . "-- \n   $chat ($serverprotokoll://" . $HTTP_HOST . $PHP_SELF . ")\n";
$t['neu54'] = "Dem User/der Userin wurde nun per E-Mail der Freischaltcode mitgeteilt.";
$t['neu55'] = "Der Webmaster hat die Anzahl der Anmeldungen pro User auf %anzahl% begrenzt.";

$t['default1'] = "Login in den $chat oder";
$t['default2'] = "Gerade sind <b>%onlineanzahl% User online</b>,\n";
$t['default3'] = "insgesamt sind %useranzahl% User registriert.\n";
$t['default4'] = "User online in %raum%:";
$t['default5'] = "$chat:";
$t['default6'] = "<b>Warnung an alle Admins:</b> User <b>%u_nick%</b> loggt sich über %ip_adr%/%ip_name% im $chat ein (%is_infotext%)!";
$t['default7'] = "<b>$chat:</b> User '<b>%u_nick%</b>' betritt Raum '%raumname%'.";
$t['default8'] = "Im Forum finden sich %beitraege% Beiträge in %themen% Diskussionsthemen.\n";
$t['default9'] = "Raum ";
$t['default10'] = "Community-Bereich ";

$t['ipsperre1'] = "Info an alle Admins: User %u_nick% loggt sich über %ip_adr%/%ip_name% ein (%is_infotext%)!";
$t['ipsperre2'] = "IP-Sperre umgangen, da mehr als $loginwhileipsperre Punkte (%punkte%)";

$t['agb'] = "<p><b>Nutzungsbestimmungen $chat:</b></p>"
    . "<ol><li>Wir bitten alle User im $chat um ein höfliches, respektvolles und nicht zu aufdringliches "
    . "Verhalten. </li>"
    . "<li>Nicht erlaubt ist die Registrierung ohne Angabe des wahren Vor- und Zunamens. "
    . "Pro Mitglied ist nur ein Account zulässig.</li>"
    . "<li>Im $chat sollte man grundsätzlich nichts tun, was man im realen Leben auch nicht tun würde. "
    . "Ausdrücklich untersagt sind Beleidigungen, Pöbeleien, Sticheln, Provozieren, Stänkern, Baggern, "
    . "anstößige Äußerungen, Schreiben nur in Großbuchstaben (wird als Anschreien gewertet), Spam und "
    . "nationalsozialistische Sprüche oder Nicknamen. </li>"
    . "<li>Das öffentliche Posten von Links im Allgemeinen und das Einfügen von URLs"
    . "zum Zweck der Werbung, insbesondere für andere Internet-Angebote,"
    . "ist nicht gestattet.</li>"
    . "<li>Admins haben das Recht, User bei Verstößen gegen diese Regeln aus dem $chat zu werfen. Davon "
    . "wird meist, außer in Extremfällen, erst nach einer Vorwarnung Gebrauch gemacht. </li>"
    . "<li>Admins dürfen zu Administrationszwecken die Userdaten einsehen und ggf. die IP-Adresse des "
    . "Users ermitteln, um die IP-Adresse oder den Provider zu sperren. </li>"
    . "<li>Der Betreiber des $chat behält sich das Recht vor, bei Verstößen gegen die Regeln von seinem"
    . " Hausrecht Gebrauch zu machen, den User aus dem $chat auszusperren und gegebenenfalls zur Anzeige "
    . "zu bringen. </li>"
    . "<li>Der Betreiber haftet ausdrücklich nicht für die Inhalte im $chat. </li>"
    . "<li>Mit dem Login oder der Registrierung in den Mainchat erklären Sie sich mit "
    . "der Datenspeicherung gemäß unserer <a href=\"http://" . $HTTP_HOST
    . "/hilfe.php?http_host=" . $HTTP_HOST
    . "&id=&aktion=privacy\" style=\" color: #000000!important; text-decoration: underline; \">Privacy Policy</a> einverstanden.</li>"
    . "</ol>"
    . "<p>Bei Fragen oder Anregungen wenden Sie sich bitte an <a href=\"mailto:$webmaster\" style=\" color: #000000!important; \">$webmaster</a>.</p>";

$t['pwneu1'] = "<p><b>Neues Passwort, 1. Schritt (von 3):</b> Sie haben Ihr Passwort vergessen? Kein Problem, geben Sie hier einfach Ihren "
    . "Nicknamen an. In Zusammenhang mit der E-Mailadresse, die Sie bei der Anmeldung angegeben haben, erhalten Sie "
    . "eine E-Mail mit einem Sicherheitscode, der Sie berechtigt ein neues Passwort anzufordern.</p>";
$t['pwneu2'] = "Nickname";
$t['pwneu3'] = "E-Mailadresse";
$t['pwneu5'] = "Ungültige E-Mailadresse!";
$t['pwneu6'] = "Nickname und E-Mailadresse passen nicht zusammen!";
$t['pwneu7'] = "<p><b>Neues Passwort, 2. Schritt (von 3):</b> An Ihre E-Mailadresse wurde ein Sicherheitscode gesendet. Geben Sie bitte diesen "
    . "Sicherheitscode in das untere Feld ein.</p>";
$t['pwneu8'] = "Ihre Passwortanforderung für den $chat";
$t['pwneu9'] = "Hallo %nickname%,

Sie oder jemand unbefugtes möchte ein neues Passwort für Ihren Nicknamen.

Anbei erhalten Sie den Sicherheitscode, mit dem Sie sich ein neues Passwort erzeugen können. Ohne diesen wird kein neues Passwort erzeugt.  
Geben Sie den Sicherheitscode bitte in das vorgegebene Feld ein - oder kopieren Sie es mit \"Copy and Paste\" (markieren, STRG+C und STRG+V)

Sicherheitscode: %hash%
   
-- 
Ihr $chat-Team  ";
$t['pwneu10'] = "Sicherheitscode";
$t['pwneu11'] = "Der angegebene Sicherheitscode ist leider nicht richtig.";
$t['pwneu12'] = "<p><b>Neues Passwort, 3. Schritt (von 3):</b> An Ihre E-Mailadresse wurde soeben ein neues Passwort gesendet.</p>";
$t['pwneu13'] = "<p><b>Fehler: Die E-Mail konnte nicht versandt werden. Das Passwort wurde nicht geändert!</b></p>";
$t['pwneu14'] = "Ihr neues Passwort für den $chat";
$t['pwneu15'] = "Hallo %nickname%,

Ihr Passwort wurde geändert. Ihr neues Passwort lautet: %passwort%

Viel Spaß noch im $chat";

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
