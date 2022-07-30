<?php
require_once("functions/functions.php");
require_once("functions/functions-chat_lese.php");

// Optional kann $trigger_letzte_Zeilen als Trigger für die Ausgabe der letzten n-Zeilen angegeben werden
$trigger_letzte_Zeilen = 1;

// Benutzerdaten setzen
id_lese();

// Direkten Aufruf der Datei verbieten (nicht eingeloggt)
if( false && !isset($u_id) || $u_id == NULL || $u_id == "") {
	echo "<body onLoad='parent.location.href=\"index.php\"'>\n";
	header('Location: ' . $chat_url);
	exit();
	die;
} else {
	// Hole alle benötigten Einstellungen des Benutzers
	$benutzerdaten = hole_benutzer_einstellungen($u_id, "chatausgabe");
	
	// Zeit in Sekunden bis auch im Normalmodus die Seite neu geladen wird
	$refresh_zeit = 600;
	
	// Systemnachrichten ausgeben
	$sysmsg = true;
	
	$title = $body_titel;
	$meta_refresh = "";
	$meta_refresh = '<script type="text/javascript">
	/* <![CDATA[ */
		function refreshContent() {
			$("[id=seitenleiste]").load("seitenleiste.php");
		}
		
		$(document).ready(function(){
			window.setInterval("refreshContent()", 10000);
			refreshContent();
		});
	/* ]]> */
	</script>';
	
	$meta_refresh .= "<meta http-equiv=\"expires\" content=\"0\">\n";
	$meta_refresh .= "<script>
	setInterval(() => {
		document.getElementById('content-wrapper').scrollTo(1,300000);
	}, 100);
	</script>\n";
	//$meta_refresh .= '<script src="js/jscript.js"></script>';
	$meta_refresh .= '<script src="js/jquery-3.6.0.min.js"></script>';
	$meta_refresh .= '<script src="js/chat.js"></script>';
	//echo'<link rel="stylesheet" href="css/style.css">';
	//echo'</head>';
	
	zeige_header($title, $benutzerdaten['u_layout_farbe'], $meta_refresh);
	
	echo'<body>';
	?>
	<style>
	#wrapper {
		width: 100%;
		background-color: #fff;
	}
	
	#top-nav {
		position: fixed;
		left: 0;
		right: 0;
		top: 0;
		height: 60px;
		width: 100%;
		background-color: green;
	}
	
	#seitenleiste {
		position: fixed;
		width: 250px;
		height:100vh;
		right: 0;
		overflow-y: scroll;
		top: 60px;
		padding:1px;
	}
	
	#content-wrapper {
		margin: 60px 250px 0 0;
		padding: 0 10px;
		overflow-y: scroll;
		position: fixed;
		right: 0;
		left: 0;
		top: 0;
		height:100vh;
	}
	</style>
	<div id="wrapper">
		<div id="top-nav">
			Top nav
		</div>
		<div id="content-wrapper">
		<?php $i = 0;?>
			text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>
			text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>text<?php echo $i; $i++;?><br>
			<!-- <div id="view_ajax"></div> -->
		</div>
		<div id="seitenleiste"></div>
	</div>
<?php
}
?>
</body>
</html>