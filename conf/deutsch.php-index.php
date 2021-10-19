<?php
// Sprachdefinition deutsch index.php

$t['willkommen2'] = "<p><span style=\"font-size: large\"><b>Willkommen im $chat!</b></span></p>\n";
$t['willkommen3'] = "Willkommen im $chat!";

$t['login1'] = "<b>Login mit sicherer HTTPS-Anmeldung</b>";
$t['login2'] = "<b>neue HTTPS-Anmeldung</b>";
$t['login3'] = "Gäste können sich einloggen, in dem "
	. "sie einfach auf <b>Login</b> klicken " . "ohne einen Namen einzugeben";
$t['login4'] = "<p><b>Der Chat ist im Moment aus technischen Gründen geschlossen! "
	. "Wir bedauern dies sehr und arbeiten daran.<br><br> Bitte versuchen "
	. "Sie es später wieder.</b></p><br><br>\n";
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
$t['login14'] = "als registrierter Benutzer neu anmelden";
$t['login15'] = "<p><b>Leider ist der Login als Gast derzeit gesperrt. Bitte haben Sie dafür Verständnis.</b></p>";
$t['login16'] = "<p><b>Leider ist kein Login als Gast möglich. Bitte melden Sie sich als registrierter Benutzer an.</b></p>";
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
$t['login23'] = "Beiträge im Forum zeigen";
$t['login24'] = "<p><b>Der Login ist leider nicht möglich!</b></p>"
	. "<p>Es sind im $chat bereits %online% Benutzer online. "
	. "Als %leveltxt% dürfen Sie ab maximal %max% Benutzer den $chat nicht mehr betreten.</p>"
	. "%zusatztext%";
$t['login25'] = "<p><b>Fehler beim Login:</b><br>Der Login als Admin (Superuser oder Chatadmin) "
	. "ohne aktivierte Cookies ist aus Sicherheitsgründen nicht gestattet. Bitte verwenden "
	. "Sie einen Browser mit aktivieren Cookies "
	. "</p><p>[<a href=\"%url%\">weiter zur Loginseite</A>]</p>";
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
	. "Personen nicht zugänglich gemacht wird (gem. <a href=\"hilfe.php?id=&aktion=privacy\">Datenschutzerklärung</a>). Falls Sie zum Beispiel Ihr Passwort\n"
	. "vergessen und uns kontaktieren, können wir Ihnen an diese Adresse Ihr "
	. "neues Passwort schicken. Die 2. optionale E-Mail Adresse (öffentlich) dagegen ist\n"
	. "im Chat für alle anderen Mitglieder sichtbar.</p>\n"
	. "<p>Alle Felder mit <b>*</b> sind Pflichtfelder. Die Felder <b>E-Mail (öffentlich)</b> und\n"
	. "<b>Homepage</b> sind öffentlich und müssen nicht ausgefüllt werden.\n"
	. "Falls Sie <b>Benutzername</b> nicht ausfüllen, wird Ihr Name aus dem Feld\n"
	. "<b>Name</b> als öffentlicher\n"
	. "Benutzername automatisch eingesetzt.</p>\n"
	. "<p>Mit dem Abschluß der Registrierung (Klick auf Fertig) bestätigen Sie Ihr Einverständnis\n"
	. "zur Verarbeitung Ihrer personenbezogenen Daten gemäß unserer <a href=\"hilfe.php?id=&aktion=privacy\"><b>Datenschutzerklärung</b></a>.\n"
	. "Außerdem erklären Sie sich mit unseren <a href=\"hilfe.php?id=&aktion=agb\"><b>Nutzungsbestimmungen</b></a> einverstanden.</p>\n";
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
$t['neu36'] = "Willkommen beim $chat!\n\nUm nun die Registrierung als neuer Benutzer fortzusetzen, klicken Sie bitte auf den folgenden Link:"
	. "\n\n%link% \n\nSollte es zu Problemen beim klicken auf den Registrierungslink kommen, so verwenden Sie diesen Link zum manuellen "
	. "Prüfen der E-Mail-Adresse:\n %link2%\n\n"
	. "Ihre E-Mail-Adresse: %email%\n" . "Freischalt-Code   : %hash%\n\n"
	. "\nWenn Sie diese Links nicht in Ihrem E-Mailprogramm anklicken können, so kopieren Sie diese in die Zwischenablage und fügen "
	. "Sie den Link dann in den Browser ein\n\n"
		. "-- \n   $chat ($serverprotokoll://" . $http_host . $PHP_SELF . ")\n";
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
$t['neu47'] = "Hallo!\n\nEin Benutzer mit der E-Mail-Adresse %email% möchte sich in Ihrem Chat $chat anmelden."
	. "\nZum freigeben dieser E-Mail-Adresse verwenden Sie bitte diesen Link:\n\n %link1%\n\n"
	. "Freizugebende E-Mail-Adresse: %email%\n" . "Freischalt-Code: %hash%\n\n"
	. "Dieser Benutzer erhält dann automatisch eine Mail mit einem Link mit welchem er/sie sich anmelden kann.\n\n"
	. "Möchten Sie dem Benutzer auf andere Weise den Freischalt-Code mitteilen, so sind folgende Angaben "
	. "an den Benutzer zu übermitteln:\n\n"
	. "Internetadresse zum Freischalten: %link2%\n"
	. "E-Mail-Adresse: %email%\n" . "Freischalt-Code: %hash%\n\n"
		. "-- \n   $chat ($serverprotokoll://" . $http_host . $PHP_SELF . ")\n";
$t['neu48'] = "<p><b>Registrierung, 2. Schritt:</b> Warten Sie bitte nun, bis Sie vom Webmaster eine E-Mail mit Ihrem "
	. "Freischaltcode erhalten! Um dann die Registrierung abzuschließen, benutzen Sie bitte den dort "
	. "angegebenen Link!</p>";
$t['neu49'] = "Freizugebende E-Mail-Adresse:";
$t['neu50'] = "Bitte geben Sie nun die <b>E-Mail-Adresse</b>, die Sie freigeben möchten, und den <b>Freischalt-Code</b> ein, "
	. "den Sie soeben per E-Mail bekommen haben.<br>"
	. "Sie können hierzu natürlich auch gerne Copy und Paste (Strg+C und Strg+V) verwenden";
$t['neu51'] = "E-Mail-Adresse falsch!";
$t['neu52'] = "Freischaltcode falsch!";
$t['neu53'] = "Willkommen beim $chat!\n\nDer Webmaster hat Ihre E-Mail-Adresse nun freigeschaltet."
	. "\nZum endgültigen Anmelden verwenden Sie bitte nun den Link: "
	. "\n %link%\n\nZum anmelden benötigen Sie dann noch:\n"
	. "Ihre E-Mail-Adresse: %email%\n" . "Ihren Freischalt-Code: %hash%\n\n"
		. "-- \n   $chat ($serverprotokoll://" . $http_host . $PHP_SELF . ")\n";
$t['neu54'] = "Dem Benutzer/der Benutzerin wurde nun per E-Mail der Freischaltcode mitgeteilt.";
$t['neu55'] = "Mit dieser E-Mail ist bereits ein Benutzer registriert. Falls es sich um Ihren Account handelt, können Sie über <a href=\"index.php?aktion=passwort_neu\">\"Passwort vergessen?\"</a> ein neues Passwort anfordern.<br><a href=\"javascript:history.back()\">Zurück zur Registrierung!</a>";

$t['default1'] = "Login in den $chat oder";
$t['default2'] = "Gerade sind <b>%onlineanzahl% Benutzer online</b>,\n";
$t['default3'] = "insgesamt sind %useranzahl% Benutzer registriert.\n";
$t['default4'] = "Benutzer online in %raum%:";
$t['default5'] = "$chat:";
$t['default6'] = "<b>Warnung an alle Admins:</b> Benutzer <b>%u_nick%</b> loggt sich über %ip_adr%/%ip_name% im $chat ein (%is_infotext%)!";
$t['default7'] = "<b>$chat:</b> Benutzer '<b>%u_nick%</b>' betritt Raum '%raumname%'.";
$t['default8'] = "Im Forum finden sich %beitraege% Beiträge in %themen% Diskussionsthemen.\n";
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
	. "der Datenspeicherung gemäß unserer <a href=\"hilfe.php?id=&aktion=privacy\" style=\" color: #000000!important; text-decoration: underline; \">Datenschutzerklärung</a> einverstanden.</li>"
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
