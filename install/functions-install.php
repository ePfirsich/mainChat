<?php
/**
 * Führt eine SQL-Abfrage aus und gibt das Ergebnis zurück.
 * Benachrichtigt bei fehlerhaften Abfragen.
 * @param string $query Die Abfrage, die ausgeführt werden soll
 * @return Ressource|false Das Ergebnis der Abfrage
 */
function pdoQuery($query, $params) {
	global $pdo;
	
	try {
		$statement = $pdo->prepare($query);
		$statement->execute($params);
	} catch (PDOException $exception) {
		return false;
	}
	
	return $statement;
}

function checkFormularInputFeld($defaultValue, $insertedValue) {
	if ($insertedValue != null && $insertedValue != '') {
		return $insertedValue;
	} else {
		return $defaultValue;
	}
}

function checkFormularRadioButton($defaultValue, $insertedValue, $name) {
	if ($insertedValue != null && $insertedValue != '') {
		$value = $insertedValue;
	} else {
		$value = $defaultValue;
	}
	
	if($value == '0') {
		?>
		ja<input type="radio" name="<?php echo $name ?>" value="1">&nbsp;&nbsp;nein<input type="radio" name="<?php echo $name ?>" value="0" checked="checked">
		<?php
	} else {
		?>
		ja<input type="radio" name="<?php echo $name ?>" value="1" checked="checked">&nbsp;&nbsp;nein<input type="radio" name="<?php echo $name ?>" value="0">
		<?php
	}
}

$zaehlerFarben = 0;
function hintergrundfarbe_zellen() {
	global $zaehlerFarben;
	if ($zaehlerFarben % 2 != 0) {
		echo 'style="background-color:#E5E5E5;"';
	} else {
		echo 'style="background-color:#85D4FF;"';
	}
	$zaehlerFarben ++;
}

function generate_secret_salt() {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#*~()[]{}!$%&?#';
	$charactersLength = strlen($characters);
	$secret_salt = '';
	for ($i = 0; $i < 12; $i++) {
		$secret_salt .= $characters[rand(0, $charactersLength - 1)];
	}
	return $secret_salt;
}

/**
 * Hasht das eingegebene Passwort
 * @param string $password Eingegebenes Psswort des Mitglieds
 * @return string Gibt das gehashte Passwort zurück
 */
function encrypt_password_install($salt, $password) {
	$salted_password = $salt.$password;
	$password_hash = hash('sha256', $salted_password);
	
	return $password_hash;
}

function step_1($chat) {
	?>
	<table style="width:100%; border:0px; text-align: center;">
		<tr>
			<td><img src="images/wizard.gif" style="width:110px; height:180px; border:0px;" alt=""></td>
			<td style="font-size:15px; font-family: Arial;">
				<p align="center"><span style="font-weight:bold;">Hinweise</span><br>
					Alle Rechte an mainChat vorbehalten, &copy; fidion GmbH<br>
					Weitere Informationen zur Installation finden Sie hier [<a href="liesmich.php" target="_blank">LIESMICH</a>]
				</p>
			</td>
			<td><img src="images/fidionlogo.gif" style="width:138px; height:51px; border:0px;" alt=""></td>
		</tr>
		<tr>
			<td colspan="2"><br></td>
		</tr>
	</table>

	<form action="install.php" method="post">
		<input type="hidden" name="chat_secret_salt" size="40" value="<?php echo checkFormularInputFeld(generate_secret_salt(),$chat["secret_salt"]) ?>">
		<table style="width:100%; border:0px;">
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Datenbank Einstellungen</span></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td>Server:</td><td><input type="text" name="chat_host" size="40" value="<?php echo checkFormularInputFeld('localhost',$chat["host"]) ?>">*</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td>Datenbank:</td>
				<td><input type="text" name="chat_dbase" size="40" value="<?php echo checkFormularInputFeld('',$chat["dbase"]) ?>">*</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td>Benutzer:</td>
				<td><input type="text" name="chat_user" size="40" value="<?php echo checkFormularInputFeld('',$chat["user"]) ?>">*</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td>Passwort:</td>
				<td><input type="password" name="chat_pass" size="40" value=""></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td>Passwort (wiederholen):</td>
				<td><input type="password" name="chat_pass2" size="40" value=""></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px;text-align:center;color:#ffffff;"><span style="font-weight:bold;">Globale Einstellungen</span></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Name des Chats:</td>
				<td><input type="text" name="chat_chatname" size="40" value="<?php echo checkFormularInputFeld('',$chat["chatname"]) ?>">*</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> URL des Chats: (Mit https:// davor, aber ohne / am Ende)</td>
				<td><input type="text" name="chat_chaturl" size="40" value="<?php echo checkFormularInputFeld('https://www.deinChat.de',$chat["chaturl"]) ?>">*</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Voreingestellte Sprache des Chats:</td>
				<td>
					<select name="chat_language">
						<option value="de">de</option>
						<option value="en">eu</option>
					</select>
				</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Voreingestellte Sprache: (sollte die gleiche Sprache wie eins drüber sein)</td>
				<td>
					<select name="chat_content_locale">
						<option value="de_DE">de_DE</option>
						<option value="en_GB">en_GB</option>
					</select>
				</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Beim Login Auswahl des Raums möglich:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["raumauswahl"],'chat_raumauswahl'); ?></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Beim Login Boxen mit Benutzern unterdrücken:</td>
				<td><?php echo checkFormularRadioButton('0',$chat["unterdrueckeraeume"],'chat_unterdrueckeraeume'); ?></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Lobby (Standardraum):</td>
				<td><input type="text" name="chat_lobby" size="40" value="<?php echo checkFormularInputFeld('Lobby',$chat["lobby"]) ?>">*</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Text für den Login oben:</td>
				<td><textarea cols="80" rows="3" name="chat_layoutkopf"><?php echo checkFormularInputFeld('',$chat["layoutkopf"]) ?></textarea></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Description:</td>
				<td><textarea cols="80" rows="3" name="chat_description"><?php echo checkFormularInputFeld('mainChat - Die kostenlose Chat-Community für jede Homepage',$chat["description"]) ?></textarea></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Keywords:</td>
				<td><textarea cols="80" rows="3" name="chat_keywords"><?php echo checkFormularInputFeld('Chat Community HTML kostenlos frei openSource, Smilies',$chat["keywords"]) ?></textarea></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Text für Startseite im Bereich NOFRAMES für Suchmaschinen:</td>
				<td><textarea cols="80" rows="5" name="chat_noframes">
<p><span style="font-weight:bold;">mainChat</span> ist die schnelle HTML Chat Community in PHP.
Er wird mittlerweile auf <a href="https://github.com/ePfirsich/mainChat">Github</a> zum Download verfügbar.<br>
<span style="font-weight:bold;">Features:</span> Nachrichten, Forum, Punkte, beliebig viele Räume und Benutzer, IRC-Befehle, 
Smilies, Gäste, Administratoren, Top10 / Top100 Listen, 
Freunde, Benutzerseiten, Profile, Privatnachrichten, öffentliche Nachrichten, mehrsprachig, 
vordefinierte Sprüche, Benutzernamen-Ergänzung, Moderation, Spam-Schutz und vieles mehr.</p>
<p style="text-align:center;"><a href="index.php">weiter</a></p></textarea></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Anonymer Gast-Login möglich:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["gastlogin"],'chat_gastlogin'); ?></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Beliebig viele Gast-Login von einer IP:</td>
				<td><?php echo checkFormularRadioButton('0',$chat["gastloginanzahl"],'chat_gastloginanzahl'); ?></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Erster Buchstabe im Benutzernamen immmer groß:</td>
				<td><?php echo checkFormularRadioButton('0',$chat["uppername"],'chat_uppername'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">E-Mail Versand</span></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> E-Mail zum Versenden von allen E-Mails:</td>
				<td><input type="text" name="chat_email_absender" size="40" value="<?php echo checkFormularInputFeld('no-reply@deinChat.de',$chat["email_absender"]) ?>">*</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> E-Mail Kontakt (für das Kontaktformular):</td>
				<td><input type="text" name="chat_kontakt" size="40" value="<?php echo checkFormularInputFeld('info@deinChat.de',$chat["kontakt"]) ?>">*</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP aktivieren oder deaktivieren</td>
				<td>
					<select name="chat_smtp_on">
						<option value="true">aktiviert</option>
						<option value="false">deaktiviert</option>
					</select>
				</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP E-Mail Server</td>
				<td><input type="text" name="chat_smtp_host" size="40" value="<?php echo checkFormularInputFeld('',$chat["smtp_host"]) ?>"></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP Port (587 = STARTTLS, 465 = TLS)</td>
				<td><input type="text" name="chat_smtp_port" size="40" value="<?php echo checkFormularInputFeld('587',$chat["smtp_port"]) ?>"></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP Encryption, bei Port 25 muss hier false eingetragen werden</td>
				<td><input type="text" name="chat_smtp_encryption" size="40" value="<?php echo checkFormularInputFeld('tls',$chat["smtp_encryption"]) ?>"></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP Benutzername</td>
				<td><input type="text" name="chat_smtp_username" size="40" value="<?php echo checkFormularInputFeld('',$chat["smtp_username"]) ?>"></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP Passwort</td>
				<td><input type="text" name="chat_smtp_password" size="40" value="<?php echo checkFormularInputFeld('',$chat["smtp_password"]) ?>"></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP Sender E-Mail Adresse</td>
				<td><input type="text" name="chat_smtp_sender" size="40" value="<?php echo checkFormularInputFeld('no-reply@deinChat.de',$chat["smtp_sender"]) ?>"></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP Autorisierung erforderlich</td>
				<td>
					<select name="chat_smtp_auth">
						<option value="true">aktiviert</option>
						<option value="false">deaktiviert</option>
					</select>
				</td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> SMTP TLS aktiviert oder deaktiviert</td>
				<td>
					<select name="chat_smtp_autoTLS">
						<option value="true">aktiviert</option>
						<option value="false">deaktiviert</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Sonstige Einstellungen</span></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Admin zeigen, wer einen Spruch eingegeben hat:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["showspruch"],'chat_showspruch'); ?></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Lustige Texte einschalten:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["lustigefeatures"],'chat_lustigefeatures'); ?></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Forum aktivieren:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["forumfeatures"],'chat_forumfeatures'); ?></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Punktezählung einschalten:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["punktefeatures"],'chat_punktefeatures'); ?></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Moderationsmodul freischalten:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["modmodul"],'chat_modmodul'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Smilies</span></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td> Max. Anzahl Smilies pro Zeile:</td>
				<td><input type="text" name="chat_smiliesanzahl" size="40" value="<?php echo checkFormularInputFeld('5',$chat["smiliesanzahl"]) ?>"></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr <?php hintergrundfarbe_zellen(); ?>>
				<td colspan="2">
					<input type="hidden" name="aktion" value="step_2">
					<input type="submit" name="los" value="Installation starten">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" value="Standardwerte wiederherstellen">
				</td>
			</tr>
		</table>
	</form>
	<?php
}

function step_2($pdo, $chat, $fpconfig, $salt) {
	$configtpl = "../conf/config.php-tpl";
	$fpconfigtpl = fopen($configtpl, "r");
	$inhalt = fread($fpconfigtpl, filesize($configtpl));
	foreach ($chat as $key => $value)
		$inhalt = str_replace("%$key%", addslashes($value), $inhalt);
	fwrite($fpconfig, $inhalt, strlen($inhalt));
	?>
	<table style="width:100%; border:0px;">
		<tr <?php hintergrundfarbe_zellen(); ?>>
			<td style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Konfigurationsdatei</span></td>
		</tr>
		<tr <?php hintergrundfarbe_zellen(); ?>>
			<td> Im Verzeichnis conf wurde folgende Konfigurationsdatei angelegt: <br>
	<?php
	$config = "../conf/config.php";
	if (!file_exists($config)) {
		?>
				<span style="color:#ff0000">Anlegen der <span style="font-weight:bold;">config.php</span> misslungen!</span>
			</td>
		</tr>
		<?php
	} else {
		?>
				<span style="font-weight:bold;">config.php</span>
			</td>
		</tr>
		<?php
	}
	
	$sqldatei = "../dok/mysql.sql";
	$sqlfp = fopen($sqldatei, "r");
	$sqlinhalt = fread($sqlfp, filesize($sqldatei));
	$sqlarray = explode(';', $sqlinhalt);
	
	foreach ($sqlarray as $key => $value) {
		if($value != "") {
			pdoQuery($value, []);
		}
	}
	
	$admin_passwort = encrypt_password_install($salt,"admin");
	pdoQuery("INSERT INTO `user` SET `u_id` = 1, `u_nick` = 'admin', `u_passwort` = :u_passwort, `u_level` = 'S'", [':u_passwort'=>$admin_passwort]);
	?>
		<tr <?php hintergrundfarbe_zellen(); ?>>
			<td colspan="2"><br><br></td>
		</tr>
		<tr <?php hintergrundfarbe_zellen(); ?>>
			<td style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Datenbank</span></td>
		</tr>
		<tr <?php hintergrundfarbe_zellen(); ?>>
			<td>In der Datenbank <?php echo $chat["dbase"] ?> (Datenbankuser: <?php echo $chat["user"] ?>) wurden folgende Tabellen angelegt: <br>
				<?php
				$query = pdoQuery("SHOW TABLES FROM `".$chat['dbase']."`", []);
				$result = $query->fetchAll();
				$db_tables_name = "Tables_in_" . $chat["dbase"];
				foreach($result as $zaehler => $row) {
					?>
					<span style="font-weight:bold;"><?php echo $row[$db_tables_name] ?></span>,
					<?php
				}
				?>
			</td>
		</tr>
		<tr <?php hintergrundfarbe_zellen(); ?>>
			<td colspan="2"><br><br></td>
		</tr>
		<tr <?php hintergrundfarbe_zellen(); ?>>
			<td> Mit dem <span style="font-weight:bold;">Name admin und Passwort admin</span> können Sie sich beim ersten Mal anmelden!<br><a href="../index.php">Hier geht es zum Chat!</a></td>
		</tr>
	</table>
	<?php
}

?>