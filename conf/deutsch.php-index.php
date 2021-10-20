<?php
// Sprachdefinition deutsch index.php

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
$t['neu36'] = "Willkommen beim $chat!<br><br>Um nun die Registrierung als neuer Benutzer fortzusetzen, klicken Sie bitte auf den folgenden Link:"
	. "<br><br>%link% <br><br>Sollte es zu Problemen beim klicken auf den Registrierungslink kommen, so verwenden Sie diesen Link zum manuellen "
	. "Prüfen der E-Mail-Adresse:<br> %link2%<br><br>"
	. "Ihre E-Mail-Adresse: %email%<br>" . "Freischalt-Code   : %hash%<br><br>"
	. "<br>Wenn Sie diese Links nicht in Ihrem E-Mailprogramm anklicken können, so kopieren Sie diese in die Zwischenablage und fügen "
	. "Sie den Link dann in den Browser ein<br><br>"
		. "-- <br>   $chat ($serverprotokoll://" . $http_host . $PHP_SELF . ")<br>";
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
		. "-- <br>   $chat ($serverprotokoll://" . $http_host . $PHP_SELF . ")<br>";
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
		. "-- <br>   $chat ($serverprotokoll://" . $http_host . $PHP_SELF . ")<br>";
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
