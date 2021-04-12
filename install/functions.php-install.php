<?php

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

function step_1($chat) {
	?>
	<table style="width:100%; border:0px; text-align: center;">
		<tr>
			<td><img src="images/wizard.gif" style="width:110px; height:180px; border:0px;" alt=""></td>
			<td style="font-size:15px; font-family: Arial;">
				<p align="center"><span style="font-weight:bold;">Hinweise</span><br>
					Alle Rechte an mainChat vorbehalten, (C) fidion GmbH<br>
					Weitere Informationen zur Installation finden Sie hier [<a href="liesmich.php" target="_blank">LIESMICH</a>]
				</p>
			</td>
			<td><img src="images/fidionlogo.gif" style="width:138px; height:51px; border:0px;" alt=""></td>
		</tr>
		<tr>
			<td colspan="2"><br></td>
		</tr>
	</table>

	<form action="<?php echo $PHP_SELF ?>" method="post">
		<table style="width:100%; border:0px;">
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Datenbank Einstellungen</span></td>
			</tr>
			<tr>
				<td>Server:</td><td><input type="text" name="chat_host" size="40" value="<?php echo checkFormularInputFeld('localhost',$chat["host"]) ?>">*</td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td>Datenbank:</td>
				<td><input type="text" name="chat_dbase" size="40" value="<?php echo checkFormularInputFeld('',$chat["dbase"]) ?>">*</td>
			</tr>
			<tr>
				<td>Benutzer:</td>
				<td><input type="text" name="chat_user" size="40" value="<?php echo checkFormularInputFeld('',$chat["user"]) ?>">*</td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td>Passwort:</td>
				<td><input type="password" name="chat_pass" size="40" value=""></td>
			</tr>
			<tr>
				<td>Passwort (wiederholen):</td>
				<td><input type="password" name="chat_pass2" size="40" value=""></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px;text-align:center;color:#ffffff;"><span style="font-weight:bold;">Globale Einstellungen</span></td>
			</tr>
			<tr>
				<td> E-Mail Webmaster:</td>
				<td><input type="text" name="chat_webmaster" size="40" value="<?php echo checkFormularInputFeld('',$chat["webmaster"]) ?>">*</td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Zuständige Person für Hackversuche:</td>
				<td><input type="text" name="chat_hackmail" size="40" value="<?php echo checkFormularInputFeld('',$chat["hackmail"]) ?>">*</td>
			</tr>
			<tr>
				<td> Name des Chats:</td>
				<td><input type="text" name="chat_chatname" size="40" value="<?php echo checkFormularInputFeld('',$chat["chatname"]) ?>">*</td>
			</tr>
			<tr>
				<td> URL des Chats: (ohne http:// oder https://)</td>
				<td><input type="text" name="chat_chaturl" size="40" value="<?php echo checkFormularInputFeld('www.deinChat.de',$chat["chaturl"]) ?>">*</td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Voreingestellte Sprachdatei:</td>
				<td>
					<select name="chat_language">
						<option value="deutsch.php">Deutsch</option>
						<option value="englisch.php">Englisch</option>
					</select>
				</td>
			</tr>
			<tr>
				<td> Beim Login Auswahl des Raums möglich:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["raumauswahl"],'chat_raumauswahl'); ?></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Beim Login Boxen mit Chattern unterdrücken:</td>
				<td><?php echo checkFormularRadioButton('0',$chat["unterdrueckeraeume"],'chat_unterdrueckeraeume'); ?></td>
			</tr>
			<tr>
				<td> Lobby (Defaultraum):</td>
				<td><input type="text" name="chat_lobby" size="40" value="<?php echo checkFormularInputFeld('Lobby',$chat["lobby"]) ?>">*</td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Anzeige Indexseite oben:</td>
				<td><input type="text" name="chat_layoutkopf" size="40" value="<?php echo checkFormularInputFeld('',$chat["layoutkopf"]) ?>"></td>
			</tr>
			<tr>
				<td> Anzeige Indexseite unten:</td>
				<td><input type="text" name="chat_layoutfuss" size="40" value="<?php echo checkFormularInputFeld('',$chat["layoutfuss"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td colspan="2">Metatag:</td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Content-Type:</td>
				<td><input type="text" name="chat_contenttype" size="40" value="<?php echo checkFormularInputFeld('text/html; charset=utf-8',$chat["contenttype"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Author:</td>
				<td><input type="text" name="chat_author" size="40" value="<?php echo checkFormularInputFeld('mainchat@mainmedia.de',$chat["author"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Description:</td>
				<td><textarea cols="30" rows="5" name="chat_description"><?php echo checkFormularInputFeld('mainChat - Die HTML-Chat-Community für jede Homepage',$chat["description"]) ?></textarea></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Keywords:</td><td><textarea cols="30" rows="3" name="chat_keywords"><?php echo checkFormularInputFeld('Chat Community HTML kostenlos frei openSource, Smilies',$chat["keywords"]) ?></textarea></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Language:</td><td><input type="text" name="chat_metalanguage" size="40" value="<?php echo checkFormularInputFeld('deutsch, de',$chat["metalanguage"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Date:</td><td><input type="text" name="chat_date" size="40" value="<?php echo checkFormularInputFeld('01.01.2021',$chat["date"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Expire:</td><td><input type="text" name="chat_expire" size="40" value="<?php echo checkFormularInputFeld('31.12.2022',$chat["expire"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Robots:</td><td><input type="text" name="chat_robots1" size="40" value="<?php echo checkFormularInputFeld('index',$chat["robots1"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Robots:</td><td><input type="text" name="chat_robots2" size="40" value="<?php echo checkFormularInputFeld('follow',$chat["robots2"]) ?>"></td></tr>
			<tr>
				<td colspan="2">Text für Startseite im Bereich NOFRAMES für Suchmaschinen:</td>
			</tr>
			<tr>
				<td colspan="2">
					<textarea cols="80" rows="5" name="chat_noframes">
<p><span style="font-weight:bold;">mainChat</span> ist die schnelle HTML Chat Community in PHP.
Er wird mittlerweile auf <a href="https://github.com/netzhuffle/mainchat">Github</a> kostenlos zum Download angeboten.<br>
Eine Version, die unter PHP7 lauffähig ist, ist ebenfalls unter <a href="https://github.com/ePfirsich/OpenMainChat">Github</a> zum Download verfügbar.<br>
<span style="font-weight:bold;">Features:</span> Mail, Forum, Punkte, beliebig viele Räume und User, IRC-Befehle, Multihoming, SSL, 
Smilie-Grafiken (Smilies), Gäste, Administratoren, Top10 / Top100 Listen, 
Freunde, User-Homepages, Profile, Privatnachrichten, öffentliche Nachrichten, Mehrsprachig, 
vordefinierte Sprüche, Nicknamen-Ergänzung, Teergruben, Moderation, Spam-Schutz und vieles mehr.</p>
<p style="text-align:center;"><a href="index.php">weiter</a></p></textarea>
				</td>
			</tr>
			<tr>
				<td> Anonymer Gast-Login möglich:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["gastlogin"],'chat_gastlogin'); ?></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Beliebig viele Gast-Login von einer IP:</td>
				<td><?php echo checkFormularRadioButton('0',$chat["gastloginanzahl"],'chat_gastloginanzahl'); ?></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Erste Buchstabe groß:</td>
				<td><?php echo checkFormularRadioButton('0',$chat["uppername"],'chat_uppername'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Sonstige Einstellungen</span></td>
			</tr>
			<tr>
				<td> Verzeichnis für Logdateien:</td>
				<td><input type="text" name="chat_log" size="40" value="logs" value="<?php echo checkFormularInputFeld('logs',$chat["log"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Datei in der alle Sprüche sind:</td>
				<td><input type="text" name="chat_spruchliste" size="40" value="<?php echo checkFormularInputFeld('sprueche.conf',$chat["spruchliste"]) ?>"></td>
			</tr>
			<tr>
				<td> Befehl für Traceroute:</td>
				<td><input type="text" name="chat_traceroute" size="40" value="<?php echo checkFormularInputFeld('/usr/sbin/traceroute',$chat["traceroute"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Admin zeigen, wer einen Spruch eingegeben hat:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["showspruch"],'chat_showspruch'); ?></td>
			</tr>
			<tr>
				<td> Zusätzliche Adminfeatueres freischalten:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["adminfeatures"],'chat_adminfeatures'); ?></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Lustige Texte einschalten:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["lustigefeatures"],'chat_lustigefeatures'); ?></td>
			</tr>
			<tr>
				<td> Erweiterte Funktionen freischalten:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["erfeatures"],'chat_erfeatures'); ?></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Communityfeatures:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["comfeatures"],'chat_comfeatures'); ?></td>
			</tr>
			<tr>
				<td> Extra Module der Community (Forum):</td>
				<td><?php echo checkFormularRadioButton('1',$chat["forumfeatures"],'chat_forumfeatures'); ?></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Punktezählung einschalten:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["punktefeatures"],'chat_punktefeatures'); ?></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Moderationsmodul freigeschalten:</td>
				<td><?php echo checkFormularRadioButton('1',$chat["modmodul"],'chat_modmodul'); ?></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Globale Voreinstellungen für Frames</span></td>
			</tr>
			<tr>
				<td> Optionale URL des linken Frames:</td>
				<td><input type="text" name="chat_framelinks" size="40" value="<?php echo checkFormularInputFeld('',$chat["framelinks"]) ?>"></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Smilies</span></td>
			</tr>
			<tr>
				<td> Smiliesdatei:</td>
				<td><input type="text" name="chat_smiliesdatei" size="40" value="<?php echo checkFormularInputFeld('smilies-grafik.php',$chat["smiliesdatei"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Smiliespfad:</td>
				<td><input type="text" name="chat_smiliespfad" size="40" value="<?php echo checkFormularInputFeld('pics/smile/',$chat["smiliespfad"]) ?>"></td>
			</tr>
			<tr style="background-color:#85D4FF;">
				<td> Max. Anzahl Smilies pro Zeile:</td>
				<td><input type="text" name="chat_smiliesanzahl" size="40" value="<?php echo checkFormularInputFeld('3',$chat["smiliesanzahl"]) ?>"></td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr style="background-color:#007ABE;">
				<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Einstellungen für das Forum</span></td>
			</tr>
			<tr>
				<td colspan="2">Mit * gekennzeichnete Felder sind Pflichtfelder</td>
			</tr>
			<tr>
				<td colspan="2"><br></td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="hidden" name="aktion" value="step_2">
					<input type="submit" name="los" value="Installation starten">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="reset" value="Standardwerte wiederherstellen">
				</td>
			</tr>
		</table>
	</form>
	<?php
}

function step_2($mysqli_link, $select, $chat, $fpconfig) {
	$configtpl = "../conf/config.php-tpl";
	$fpconfigtpl = fopen($configtpl, "r");
	$inhalt = fread($fpconfigtpl, filesize($configtpl));
	foreach ($chat as $key => $value)
		$inhalt = str_replace("%$key%", addslashes($value), $inhalt);
	fwrite($fpconfig, $inhalt, strlen($inhalt));
	?>
	<table style="width:100%; border:0px;">
		<tr style="background-color:#007ABE;">
			<td style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Konfigurationsdatei</span></td>
		</tr>
		<tr>
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
	
	$mysqldatei = "../dok/mysql.def";
	$mysqlfp = fopen($mysqldatei, "r");
	$mysqlinhalt = fread($mysqlfp, filesize($mysqldatei));
	$mysqlarray = explode(';', $mysqlinhalt);
	
	foreach ($mysqlarray as $key => $value) {
		mysqli_query($mysqli_link, $value);
	}
	
	?>
		<tr>
			<td colspan="2"><br><br></td>
		</tr>
		<tr style="background-color:#007ABE;">
			<td style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Datenbank</span></td>
		</tr>
		<tr>
			<td>In der Datenbank <?php echo $chat["dbase"] ?> (Datenbankuser: <?php echo $chat["user"] ?>) wurden folgende Tabellen angelegt: <br>
				<?php
				
				$tables = mysqli_query($mysqli_link, "SHOW TABLES FROM `".$chat['dbase']."` ");
				for ($i = 0; $i < mysqli_num_rows($tables); $i++) {
					mysqli_data_seek( $tables, $i );
					$f = mysqli_fetch_array( $tables );
					$table = $f[0];
					
					?>
					<span style="font-weight:bold;"><?php echo $table ?></span>,
					<?php
				}
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2"><br><br></td>
		</tr>
		<tr>
			<td> Mit dem <span style="font-weight:bold;">Name admin und Passwort admin</span> können Sie sich beim ersten Mal anmelden!<br><a href="../index.php">Hier geht es zum Chat!</a></td>
		</tr>
	</table>
	<?php
}

?>