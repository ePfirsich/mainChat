<?php

require("functions.php");

// Vergleicht Hash-Wert mit IP und liefert u_id, u_name, o_id, o_raum, admin
id_lese($id);

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $body_titel; ?></title>
<meta charset="utf-8">
<?php

if (strlen($u_id) > 0) {
    // Falls keine Texte zur Moderation gefunden wurden, nach 10 Sek reload
    if ($o_js) {
        $moderations_zeilen = anzahl_moderationstexte($o_raum);
        if ($moderations_zeilen == 0) {
            echo "<meta http-equiv=\"REFRESH\" CONTENT= \"10; URL=moderator.php?http_host=$http_host&id=$id\">\n";
        }
    }
    
    echo "<SCRIPT>\n" . "  function chat_reload(file) {\n"
        . "  parent.chat.location.href=file;\n}\n" . "</SCRIPT>\n";
	?>
	<style type="text/css">
	<?php echo $stylesheet; ?>
	body {
		background-color:<?php echo $farbe_background3; ?>;
	<?php if(strlen($grafik_background3) > 0) { ?>
		background-image:<?php echo $grafik_background3; ?>;
	<?php } ?>
	}
	a, a:link {
		color:<?php echo $farbe_chat_link3; ?>;
	}
	a:visited, a:active {
		color:<?php echo $farbe_chat_vlink3; ?>;
	}
	</style>
	</head>
	<body>
	<?php
    // Timestamp im Datensatz aktualisieren
    aktualisiere_online($u_id, $o_raum);
    
    if ($moderationsmodul == 1) {
        if (!isset($mode))
            $mode = "";
        switch ($mode) {
            case "answer":
            // antwort anzeigen
                zeige_moderations_antworten($o_raum);
                break;
            case "answernew":
            // antwort anlegen oder nach editieren neu schreiben
                unset($f);
                $f['c_text'] = $answertxt;
                $f['c_von_user'] = $u_nick;
                $f['c_an_user'] = $u_id;
                $f['c_raum'] = $o_raum;
                $f['c_farbe'] = $u_farbe;
                $f['c_von_user_id'] = $u_id;
                $f['c_moderator'] = $u_id;
                $f['c_typ'] = "P";
                if (!isset($answer))
                    $answer = 0;
                schreibe_db("moderation", $f, $answer, "c_id");
                zeige_moderations_antworten($o_raum);
                break;
            case "answeredit":
            // antwort editieren
                zeige_moderations_antworten($o_raum, $answer);
                break;
            case "answerdel":
            // antwort löschem
                if ($answer != "") {
                    $answer = intval($answer);
                    $query = "DELETE FROM moderation WHERE c_id=$answer";
                    mysqli_query($mysqli_link, $query);
                }
                zeige_moderations_antworten($o_raum);
                break;
            default:
            // moderationstexte bearbeiten...
            // hierbei auch expire der moderierten Nachrichten...
                bearbeite_moderationstexte($o_raum);
                
                if (!isset($limit) || $limit == "") {
                    $rows = zeige_moderationstexte($o_raum);
                } else {
                    $rows = zeige_moderationstexte($o_raum, $limit);
                }
                
                if ($rows < 5) {
                    echo "<hr type=noshade width=90% height=1>\n";
                    echo $t['moderation1'] . "\n";
                    echo $t['moderation2'] . "\n";
                    echo $t['moderation3'] . "\n";
                    echo $t['moderation4'] . "\n";
                }
                echo "\n\n";
                flush();
        }
    } else {
        echo $t[moderation1];
        echo $t[moderation8];
    }
    
} else {
	// User wird nicht gefunden. Login ausgeben
	?>
	</head>
	<body onLoad='javascript:parent.location.href="index.php?http_host=<?php echo $http_host; ?>'>
	<?php
}
?>
</body>
</html>