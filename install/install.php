<!DOCTYPE html>
<html dir="ltr" lang="de">
<head>
	<title>mainChat Installation</title>
	<meta charset="utf-8">
</head>
<body>
<?php

$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_STRING);

$chat = array();
$chat["secret_salt"] = filter_input(INPUT_POST, 'chat_secret_salt', FILTER_SANITIZE_STRING);
$chat["host"] = filter_input(INPUT_POST, 'chat_host', FILTER_SANITIZE_STRING);
$chat["dbase"] = filter_input(INPUT_POST, 'chat_dbase', FILTER_SANITIZE_STRING);
$chat["user"] = filter_input(INPUT_POST, 'chat_user', FILTER_SANITIZE_STRING);
$chat["pass"] = filter_input(INPUT_POST, 'chat_pass', FILTER_SANITIZE_STRING);
$chat["pass2"] = filter_input(INPUT_POST, 'chat_pass2', FILTER_SANITIZE_STRING);
$chat["email_absender"] = filter_input(INPUT_POST, 'chat_email_absender', FILTER_SANITIZE_STRING);
$chat["kontakt"] = filter_input(INPUT_POST, 'chat_kontakt', FILTER_SANITIZE_STRING);
$chat["smtp_on"] = filter_input(INPUT_POST, 'chat_smtp_on', FILTER_SANITIZE_STRING);
$chat["smtp_host"] = filter_input(INPUT_POST, 'chat_smtp_host', FILTER_SANITIZE_STRING);
$chat["smtp_port"] = filter_input(INPUT_POST, 'chat_smtp_port', FILTER_SANITIZE_STRING);
$chat["smtp_encryption"] = filter_input(INPUT_POST, 'chat_smtp_encryption', FILTER_SANITIZE_STRING);
$chat["smtp_username"] = filter_input(INPUT_POST, 'chat_smtp_username', FILTER_SANITIZE_STRING);
$chat["smtp_password"] = filter_input(INPUT_POST, 'chat_smtp_password', FILTER_SANITIZE_STRING);
$chat["smtp_sender"] = filter_input(INPUT_POST, 'chat_smtp_sender', FILTER_SANITIZE_STRING);
$chat["smtp_auth"] = filter_input(INPUT_POST, 'chat_smtp_auth', FILTER_SANITIZE_STRING);
$chat["smtp_autoTLS"] = filter_input(INPUT_POST, 'chat_smtp_autoTLS', FILTER_SANITIZE_STRING);
$chat["chatname"] = filter_input(INPUT_POST, 'chat_chatname', FILTER_SANITIZE_STRING);
$chat["chaturl"] = filter_input(INPUT_POST, 'chat_chaturl', FILTER_SANITIZE_STRING);
$chat["language"] = filter_input(INPUT_POST, 'chat_language', FILTER_SANITIZE_STRING);
$chat["raumauswahl"] = filter_input(INPUT_POST, 'chat_raumauswahl', FILTER_SANITIZE_STRING);
$chat["unterdrueckeraeume"] = filter_input(INPUT_POST, 'chat_unterdrueckeraeume', FILTER_SANITIZE_STRING);
$chat["lobby"] = filter_input(INPUT_POST, 'chat_lobby', FILTER_SANITIZE_STRING);
$chat["layoutkopf"] = filter_input(INPUT_POST, 'chat_layoutkopf', FILTER_SANITIZE_STRING);
$chat["description"] = filter_input(INPUT_POST, 'chat_description', FILTER_SANITIZE_STRING);
$chat["keywords"] = filter_input(INPUT_POST, 'chat_keywords', FILTER_SANITIZE_STRING);
$chat["content_language"] = filter_input(INPUT_POST, 'chat_content_language', FILTER_SANITIZE_STRING);
$chat["noframes"] = filter_input(INPUT_POST, 'chat_noframes', FILTER_SANITIZE_STRING);
$chat["gastlogin"] = filter_input(INPUT_POST, 'chat_gastlogin', FILTER_SANITIZE_STRING);
$chat["gastloginanzahl"] = filter_input(INPUT_POST, 'chat_gastloginanzahl', FILTER_SANITIZE_STRING);
$chat["uppername"] = filter_input(INPUT_POST, 'chat_uppername', FILTER_SANITIZE_STRING);
$chat["showspruch"] = filter_input(INPUT_POST, 'chat_showspruch', FILTER_SANITIZE_STRING);
$chat["lustigefeatures"] = filter_input(INPUT_POST, 'chat_lustigefeatures', FILTER_SANITIZE_STRING);
$chat["forumfeatures"] = filter_input(INPUT_POST, 'chat_forumfeatures', FILTER_SANITIZE_STRING);
$chat["punktefeatures"] = filter_input(INPUT_POST, 'chat_punktefeatures', FILTER_SANITIZE_STRING);
$chat["modmodul"] = filter_input(INPUT_POST, 'chat_modmodul', FILTER_SANITIZE_STRING);
$chat["smiliesanzahl"] = filter_input(INPUT_POST, 'chat_smiliesanzahl', FILTER_SANITIZE_STRING);

require_once("functions-install.php");
$configdatei = "../conf/config.php";

?>
<table style="width:100%; border:2px; background-color:#E5E5E5; border-color:#007ABE; font-family: Arial;">
	<tr>
		<td style="background-color:#007ABE;"><p style="font-size:20px;text-align:center;color:#ffffff;font-family:Arial;"><b>mainChat Installation</b></p></td>
	</tr>
	<tr>
		<td>
<?php
switch ($aktion) {
	case "step_2":
		if (!file_exists($configdatei)) {
			$regexemail = "|^[_a-z0-9-]+(\.[_a-z0-9]+)*@([a-z0-9-]+\.)[a-z]{2,8}$|mi";
			
			if ($chat["lobby"] == "" || $chat["dbase"] == ""
				|| $chat["host"] == "" || $chat["user"] == ""
				|| $chat["pass"] != $chat["pass2"]
				|| ($chat["email_absender"] == "" || (!preg_match($regexemail, $chat["email_absender"])))
				|| ($chat["kontakt"] == "" || (!preg_match($regexemail, $chat["kontakt"])))
				|| $chat["chatname"] == ""|| $chat["chaturl"] == "") {
				?>
				<table style="width:100%;" align="center">
					<tr>
						<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff; background-color:#007ABE;"><b>FEHLER</b></td>
					</tr>
				<?php
				if ($chat["lobby"] == "") {
					echo "<tr style=\"color:#ff0000;font-weigth:bold;\"><td>Bitte tragen Sie ein Lobby Standardraum ein!</td></tr>\n";
				}
				if ($chat["dbase"] == "") {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbanknamen ein!</td></tr>\n";
				}
				if ($chat["host"] == "") {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbankserver ein!</td></tr>\n";
				}
				if ($chat["user"] == "") {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbankuser ein!</td></tr>\n";
				}
				if ($chat["pass"] != $chat["pass2"]) {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Das Passwort stimmt nicht überein!</td></tr>\n";
				}
				if ($chat["email_absender"] == "") {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie einen Webmaster ein!</td></tr>\n";
				}
				if ((!preg_match($regexemail, $chat["email_absender"])) && ($chat["email_absender"] != "")) {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Ungültige Email-Adresse Webmaster!</td></tr>\n";
				}
				if ($chat["kontakt"] == "") {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie eine zuständige Person bei Hackversuchen ein!</td></tr>\n";
				}
				if ((!preg_match($regexemail, $chat["kontakt"])) && ($chat["kontakt"] != "")) {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Ungültige Email-Adresse Kontakt!</td></tr>\n";
				}
				if ($chat["chatname"] == "") {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Chatnamen ein!</td></tr>\n";
				}
				if ($chat["chaturl"] == "") {
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Chat-URL ein!</td></tr>\n";
				}
				echo "<tr><td colspan=\"2\"><br><br></td></tr></table>\n";
				step_1($chat);
			} else {
				$fp = @fopen($configdatei, "w+");
				if (!$fp) {
					?>
					<table style="width:100%; border:0px;">
						<tr style="color:#ff0000; font-weigth:bold;">
							<td>FEHLER: Die Konfigurationsdatei konnte nicht angelegt werden. Überprüfen Sie die Schreibrechte im Verzeichnis conf!</td>
						</tr>
					</table>
					<?php
					step_1($chat);
				} else {
					$mysqli_link = mysqli_connect('p:'.$chat["host"], $chat["user"], $chat["pass"], $chat["dbase"]);
					if (!$mysqli_link) {
						?>
						<table style="width:100%; border:0px;">
							<tr style="color:#ff0000; font-weigth:bold;">
								<td>FEHLER: Datenbankverbindung fehlgeschlagen!</td>
							</tr>
						</table>
						<?php
						unlink($configdatei);
						step_1($chat);
					} else {
						mysqli_set_charset($mysqli_link, "utf8mb4");
						step_2($mysqli_link, $chat, $fp);
					}
				}
			}
		} else {
			?>
			<table style="width:100%; border:0px;">
				<tr style="color:#ff0000; font-weigth:bold;">
					<td>FEHLER: Die Datei config.php existiert bereits!</td>
				</tr>
			</table>
			<?php
			step_1($chat);
		}
		break;
	
	default:
		if (file_exists($configdatei)) {
			?>
			<table style="width:100%; border:0px;">
				<tr style="color:#ff0000; font-weigth:bold;">
					<td>FEHLER: Die Datei config.php existiert bereits!</td>
				</tr>
			</table>
			<?php
		} else {
			step_1($chat);
		}
		break;
}
?>

		</td>
	</tr>
</table>

</body>
</html>