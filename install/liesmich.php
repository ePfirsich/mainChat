<!DOCTYPE html>
<html dir="ltr" lang="de">
<head>
	<title>LIESMICH</title>
	<meta charset="utf-8">
</head>
<body>
	<pre>
	<?php
	$liesmich="../dok/LIESMICH";
	
	if (!file_exists ($liesmich))
	{
		echo "Keine LIESMICH-Datei gefunden!";
	}
	else
	{
		if (!$fp=fopen ($liesmich,"r"))
		{
			echo "Datei konnte nicht geöffnet werden!";
		}
		else
		{
			$inhalt=fread($fp,filesize($liesmich));
			echo $inhalt;
			fclose ($fp);
		}
	}
	
	?>
	</pre>
</body>
</html>
	