<!DOCTYPE html>
<html dir="ltr" lang="de">
<head>
	<title>mainChat Aktualisierung</title>
	<meta charset="utf-8">
</head>
<body>
<?php

require_once("functions-update.php");
require("../conf/config.php");

$aktion = filter_input(INPUT_POST, 'aktion', FILTER_SANITIZE_STRING);

?>
<table style="width:100%; border:2px; background-color:#E5E5E5; border-color:#007ABE; font-family: Arial;">
	<tr>
		<td style="background-color:#007ABE;"><p style="font-size:20px;text-align:center;color:#ffffff;font-family:Arial;"><b>mainChat Aktualisierung von älteren Versionen</b></p></td>
	</tr>
	<tr>
		<td>
<?php
function datenbank_migration($migrationsdateienArray) {
	$configdatei = "update.lock";
	
	if (!file_exists($configdatei)) {
		$fp = @fopen($configdatei, "w+");
		if(!$fp) {
			?>
				<table style="width:100%; border:0px;">
					<tr style="color:#ff0000; font-weigth:bold;">
						<td>FEHLER: Die Konfigurationsdatei konnte nicht angelegt werden. Überprüfen Sie die Schreibrechte im Verzeichnis conf!</td>
					</tr>
				</table>
				<?php
		} else {
			global $mysqlhost, $mysqluser, $mysqlpass, $dbase;
			// Globales
			ini_set("magic_quotes_runtime", 0);
			
			date_default_timezone_set('Europe/Berlin');
			
			// OPEN A CONNECTION TO THE DATA BASE SERVER AND SELECT THE DB
			$dsn = "mysql:host=$mysqlhost;dbname=$dbase";
			
			$options  = array(
				PDO::MYSQL_ATTR_FOUND_ROWS		=> true,
				PDO::ATTR_EMULATE_PREPARES		=> false, // turn off emulation mode for "real" prepared statements
				//PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION, //turn on errors in the form of exceptions
				PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC, //make the default fetch be an associative array
			);
			
			$pdo = new PDO($dsn, $mysqluser, $mysqlpass, $options);
			
			if (!$pdo) {
				?>
				<table style="width:100%; border:0px;">
					<tr style="color:#ff0000; font-weigth:bold;">
						<td>FEHLER: Datenbankverbindung fehlgeschlagen!</td>
					</tr>
				</table>
				<?php
				unlink($configdatei);
			} else {
				$migrationspfad = "../dok/";
				
				for ($i = 0; $i < count($migrationsdateienArray); $i++) {
					$migrationsdatei = $migrationsdateienArray[$i];
					$sqldatei = $migrationspfad . $migrationsdatei;
					checkFormularInputFeld($sqldatei);
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
	}
}

switch ($aktion) {
	case "update_von_7_1_3":
		// Update von 7.1.2
		$migrationsdateienArray = ["update_7_1_3.sql", "update_8_0_0.sql"];
		datenbank_migration($migrationsdateienArray);
		
	case "update_von_7_1_2":
		// Update von 7.1.2
		$migrationsdateienArray = ["update_7_1_3.sql", "update_8_0_0.sql"];
		datenbank_migration($migrationsdateienArray);
		
	case "update_von_7_1_1":
		// Update von 7.1.1
		$migrationsdateienArray = ["update_7_1_2.sql", "update_7_1_3.sql", "update_8_0_0.sql"];
		datenbank_migration($migrationsdateienArray);
	
	case "update_von_7_1_0":
		// Update von 7.1.0
		$migrationsdateienArray = ["update_7_1_1.sql", "update_7_1_2.sql", "update_7_1_3.sql", "update_8_0_0.sql"];
		datenbank_migration($migrationsdateienArray);
		
		break;
	
	case "update_von_7_0_10":
		// Update von 7.0.10
		$migrationsdateienArray = ["update_7_1_0.sql", "update_7_1_1.sql", "update_7_1_2.sql", "update_7_1_3.sql", "update_8_0_0.sql"];
		datenbank_migration($migrationsdateienArray);
		
		break;
	
	case "update_von_7_0_9":
		// Update von 7.0.9
		$migrationsdateienArray = ["update_7_0_10.sql", "update_7_1_0.sql", "update_7_1_1.sql", "update_7_1_2.sql", "update_7_1_3.sql", "update_8_0_0.sql"];
		datenbank_migration($migrationsdateienArray);
		
		break;
	
	case "update_unter_7_0_0":
		$migrationsdateienArray = ["update_unter_7_0_0.sql", "update_7_0_10.sql", "update_7_1_0.sql", "update_7_1_1.sql", "update_7_1_2.sql", "update_7_1_3.sql", "update_8_0_0.sql"];
		datenbank_migration($migrationsdateienArray);
		
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
			<td>
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
	
	Bitte bei jeder Aktualisierung die <b>config.php</b> mit der <b>config.php-tpl</b> abgleichen, ob Felder hinzugekommen oder entfernt wurden.<br>
	<br>
	<br>
	Es muss immer nur eine Aktualisierung durchgeführt werden.<br>
	<form action="update.php" method="post">
		<input type="hidden" name="aktion" value="update_von_7_1_3">
		<input type="submit" value="Aktualisierung von Version 7.1.3 starten">
	</form>
	<br>
	<form action="update.php" method="post">
		<input type="hidden" name="aktion" value="update_von_7_1_2">
		<input type="submit" value="Aktualisierung von Version 7.1.2 starten">
	</form>
	<br>
	<form action="update.php" method="post">
		<input type="hidden" name="aktion" value="update_von_7_1_1">
		<input type="submit" value="Aktualisierung von Version 7.1.1 starten">
	</form>
	<br>
	<form action="update.php" method="post">
		<input type="hidden" name="aktion" value="update_von_7_1_0">
		<input type="submit" value="Aktualisierung von Version 7.1.0 starten">
	</form>
	<br>
	<form action="update.php" method="post">
		<input type="hidden" name="aktion" value="update_von_7_0_10">
		<input type="submit" value="Aktualisierung von Version 7.0.10 starten">
	</form>
	<br>
	<form action="update.php" method="post">
		<input type="hidden" name="aktion" value="update_von_7_0_9">
		<input type="submit" value="Aktualisierung von Version 7.0.9 starten">
	</form>
	<br>
	<form action="update.php" method="post">
		<input type="hidden" name="aktion" value="update_unter_7_0_0">
		<input type="submit" value="Aktualisierung von unter Version 7.0.0 starten">
	</form>
	<br>
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