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

function checkFormularInputFeld($sqldatei) {
	$sqlfp = fopen($sqldatei, "r");
	$sqlinhalt = fread($sqlfp, filesize($sqldatei));
	$sqlarray = explode(';', $sqlinhalt);
	
	foreach ($sqlarray as $key => $value) {
		// Das letzte Element ist immer nur ein leerer String
		if($value == "") {
			break;
		}
		$query = pdoQuery($value, []);
	}
}
?>