<!DOCTYPE html>
<html>
<head>
	<title>mainChat Aktualisierung</title>
	<meta charset="utf-8">
</head>
<body>
<?php

require("../conf/config.php");

$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_STRING);

$configdatei = "update.lock";

?>
<table style="width:100%; border:2px; background-color:#E5E5E5; border-color:#007ABE; font-family: Arial;">
	<tr>
		<td style="background-color:#007ABE;"><p style="font-size:20px;text-align:center;color:#ffffff;font-family:Arial;"><b>mainChat Aktualisierung von älteren Versionen</b></p></td>
	</tr>
	<tr>
		<td>
<?php
switch ($aktion) {
	case "update_unter_7.0.0":
		if (!file_exists($configdatei)) {
			$fp = @fopen($configdatei, "w+");
			if (!$fp) {
				?>
				<table style="width:100%; border:0px;">
					<tr style="color:#ff0000; font-weigth:bold;">
						<td>FEHLER: Die Konfigurationsdatei konnte nicht angelegt werden. Überprüfen Sie die Schreibrechte im Verzeichnis conf!</td>
					</tr>
				</table>
				<?php
			} else {
				// DB-Connect, ggf. 3 mal versuchen
				for ($c = 0; $c++ < 3 AND (!(isset($mysqli_link)));) {
					$mysqli_link = mysqli_connect('p:'.$mysqlhost, $mysqluser, $mysqlpass, $dbase);
					if ($mysqli_link) {
						mysqli_set_charset($mysqli_link, "utf8mb4");
						mysqli_select_db($mysqli_link, $dbase);
					}
				}
				
				if (!(isset($mysqli_link))) {
					?>
					<table style="width:100%; border:0px;">
						<tr style="color:#ff0000; font-weigth:bold;">
							<td>FEHLER: Datenbankverbindung fehlgeschlagen!</td>
						</tr>
					</table>
					<?php
					unlink($configdatei);
				} else {
					mysqli_set_charset($mysqli_link, "utf8mb4");
					
					$mysqldatei = "../dok/update.def";
					$mysqlfp = fopen($mysqldatei, "r");
					$mysqlinhalt = fread($mysqlfp, filesize($mysqldatei));
					$mysqlarray = explode(';', $mysqlinhalt);
					
					foreach ($mysqlarray as $key => $value) {
						mysqli_query($mysqli_link, $value);
					}
					?>
					<table style="width:100%; border:0px;">
						<tr style="background-color:#007ABE;">
							<td style="font-size:15px; text-align:center;color:#ffffff;"><span style="font-weight:bold;">Datenbank</span></td>
						</tr>
						<tr>
							<td>In der Datenbank <?php echo $dbase; ?> (Datenbankuser: <?php echo $mysqluser; ?>) wurden die Anpassungen erfolgreich durchgeführt.
							</td>
						</tr>
					</table>
					<?php
					
				}
			}
		} else {
			?>
			<table style="width:100%; border:0px;">
				<tr style="color:#ff0000; font-weigth:bold;">
					<td>FEHLER: Die Datei install/update.lock muss vor einer Installation gelöscht werden!</td>
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
					<td>FEHLER: Die Datei install/update.lock muss vor einer Installation gelöscht werden!</td>
				</tr>
			</table>
			<?php
		} else {
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
			<tr>
				<td colspan="2">
					<input type="hidden" name="aktion" value="update_unter_7.0.0">
					<input type="submit" name="los" value="Update von unter Version 7.0.0 starten">
				</td>
			</tr>
		</table>
	</form>
			
			
			
			
			
			
			
			
			
			
			<?php
		}
		break;
}
?>

		</td>
	</tr>
</table>

</body>
</html>