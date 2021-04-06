<!DOCTYPE html>
<html>
<head>
	<title>mainChat Installation</title>
	<meta charset="utf-8">
</head>
<body>
<?php

$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_STRING);

$chat = array();
$chat["host"] = filter_input(INPUT_POST, 'chat_host', FILTER_SANITIZE_STRING);
$chat["dbase"] = filter_input(INPUT_POST, 'chat_dbase', FILTER_SANITIZE_STRING);
$chat["user"] = filter_input(INPUT_POST, 'chat_user', FILTER_SANITIZE_STRING);
$chat["pass"] = filter_input(INPUT_POST, 'chat_pass', FILTER_SANITIZE_STRING);
$chat["pass2"] = filter_input(INPUT_POST, 'chat_pass2', FILTER_SANITIZE_STRING);
$chat["webmaster"] = filter_input(INPUT_POST, 'chat_webmaster', FILTER_SANITIZE_STRING);
$chat["hackmail"] = filter_input(INPUT_POST, 'chat_hackmail', FILTER_SANITIZE_STRING);
$chat["chatname"] = filter_input(INPUT_POST, 'chat_chatname', FILTER_SANITIZE_STRING);
$chat["language"] = filter_input(INPUT_POST, 'chat_language', FILTER_SANITIZE_STRING);
$chat["raumauswahl"] = filter_input(INPUT_POST, 'chat_raumauswahl', FILTER_SANITIZE_STRING);
$chat["unterdrueckeraeume"] = filter_input(INPUT_POST, 'chat_unterdrueckeraeume', FILTER_SANITIZE_STRING);
$chat["lobby"] = filter_input(INPUT_POST, 'chat_lobby', FILTER_SANITIZE_STRING);
$chat["layoutkopf"] = filter_input(INPUT_POST, 'chat_layoutkopf', FILTER_SANITIZE_STRING);
$chat["layoutfuss"] = filter_input(INPUT_POST, 'chat_layoutfuss', FILTER_SANITIZE_STRING);
$chat["contenttype"] = filter_input(INPUT_POST, 'chat_contenttype', FILTER_SANITIZE_STRING);
$chat["author"] = filter_input(INPUT_POST, 'chat_author', FILTER_SANITIZE_STRING);
$chat["description"] = filter_input(INPUT_POST, 'chat_description', FILTER_SANITIZE_STRING);
$chat["keywords"] = filter_input(INPUT_POST, 'chat_keywords', FILTER_SANITIZE_STRING);
$chat["metalanguage"] = filter_input(INPUT_POST, 'chat_metalanguage', FILTER_SANITIZE_STRING);
$chat["date"] = filter_input(INPUT_POST, 'chat_date', FILTER_SANITIZE_STRING);
$chat["expire"] = filter_input(INPUT_POST, 'chat_expire', FILTER_SANITIZE_STRING);
$chat["robots1"] = filter_input(INPUT_POST, 'chat_robots1', FILTER_SANITIZE_STRING);
$chat["robots2"] = filter_input(INPUT_POST, 'chat_robots2', FILTER_SANITIZE_STRING);
$chat["noframes"] = filter_input(INPUT_POST, 'chat_noframes', FILTER_SANITIZE_STRING);
$chat["gastlogin"] = filter_input(INPUT_POST, 'chat_gastlogin', FILTER_SANITIZE_STRING);
$chat["gastloginanzahl"] = filter_input(INPUT_POST, 'chat_gastloginanzahl', FILTER_SANITIZE_STRING);
$chat["uppername"] = filter_input(INPUT_POST, 'chat_uppername', FILTER_SANITIZE_STRING);
$chat["log"] = filter_input(INPUT_POST, 'chat_log', FILTER_SANITIZE_STRING);
$chat["spruchliste"] = filter_input(INPUT_POST, 'chat_spruchliste', FILTER_SANITIZE_STRING);
$chat["traceroute"] = filter_input(INPUT_POST, 'chat_traceroute', FILTER_SANITIZE_STRING);
$chat["showspruch"] = filter_input(INPUT_POST, 'chat_showspruch', FILTER_SANITIZE_STRING);
$chat["adminfeatures"] = filter_input(INPUT_POST, 'chat_adminfeatures', FILTER_SANITIZE_STRING);
$chat["lustigefeatures"] = filter_input(INPUT_POST, 'chat_lustigefeatures', FILTER_SANITIZE_STRING);
$chat["erfeatures"] = filter_input(INPUT_POST, 'chat_erfeatures', FILTER_SANITIZE_STRING);
$chat["comfeatures"] = filter_input(INPUT_POST, 'chat_comfeatures', FILTER_SANITIZE_STRING);
$chat["forumfeatures"] = filter_input(INPUT_POST, 'chat_forumfeatures', FILTER_SANITIZE_STRING);
$chat["punktefeatures"] = filter_input(INPUT_POST, 'chat_punktefeatures', FILTER_SANITIZE_STRING);
$chat["modmodul"] = filter_input(INPUT_POST, 'chat_modmodul', FILTER_SANITIZE_STRING);
$chat["framelinks"] = filter_input(INPUT_POST, 'chat_framelinks', FILTER_SANITIZE_STRING);
$chat["framesetbleibt"] = filter_input(INPUT_POST, 'chat_framesetbleibt', FILTER_SANITIZE_STRING);
$chat["smiliesdatei"] = filter_input(INPUT_POST, 'chat_smiliesdatei', FILTER_SANITIZE_STRING);
$chat["smiliespfad"] = filter_input(INPUT_POST, 'chat_smiliespfad', FILTER_SANITIZE_STRING);
$chat["smiliesanzahl"] = filter_input(INPUT_POST, 'chat_smiliesanzahl', FILTER_SANITIZE_STRING);
$chat["datenschutz"] = filter_input(INPUT_POST, 'chat_datenschutz', FILTER_SANITIZE_STRING);

require("functions.php-install.php");
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
				|| ($chat["webmaster"] == ""
					|| (!preg_match($regexemail, $chat["webmaster"])))
				|| ($chat["hackmail"] == ""
					|| (!preg_match($regexemail, $chat["hackmail"])))
				|| $chat["chatname"] == "") {
				?>
				<table style="width:100%;" align="center">
					<tr>
						<td colspan="2" style="font-size:15px; text-align:center;color:#ffffff; background-color:#007ABE;"><b>FEHLER</b></td>
					</tr>
				<?php
				if ($chat["lobby"] == "")
					echo "<tr style=\"color:#ff0000;font-weigth:bold;\"><td>Bitte tragen Sie ein Lobby Defaultraum ein!</td></tr>\n";
				if ($chat["dbase"] == "")
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbanknamen ein!</td></tr>\n";
				if ($chat["host"] == "")
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbankserver ein!</td></tr>\n";
				if ($chat["user"] == "")
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbankuser ein!</td></tr>\n";
				if ($chat["pass"] != $chat["pass2"])
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Das Passwort stimmt nicht überein!</td></tr>\n";
				if ($chat["webmaster"] == "")
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie einen Webmaster ein!</td></tr>\n";
				if ((!preg_match($regexemail, $chat["webmaster"]))
					&& ($chat["webmaster"] != ""))
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Ungültige Email-Adresse Webmaster!</td></tr>\n";
				if ($chat["hackmail"] == "")
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie eine zuständige Person bei Hackversuchen ein!</td></tr>\n";
				if ((!preg_match($regexemail, $chat["hackmail"]))
					&& ($chat["hackmail"] != ""))
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Ungültige Email-Adresse Hackmail!</td></tr>\n";
				if ($chat["chatname"] == "")
					echo "<tr style=\"color:#ff0000; font-weigth:bold;\"><td>Bitte tragen Sie den Chatnamen ein!</td></tr>\n";
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
						if (!$select = mysqli_select_db($mysqli_link, $chat["dbase"])) {
							$select = mysqli_select_db($mysqli_link, $chat["dbase"]);
							step_2($mysqli_link, $select, $chat, $fp);
						} else step_2($mysqli_link, $select, $chat, $fp);
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