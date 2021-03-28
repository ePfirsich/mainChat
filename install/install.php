<html>
<head>
<title>Mainchat Installation</TITLE><META CHARSET=UTF-8>
<script>
function newWindow(url,name) 
{
	hWnd=window.open(url,name,'resizable=yes,scrollbars=yes,width=800,height=400');
}
</script>
</head>
<body>
<?php

require("functions.php-install.php");
$configdatei = "../conf/config.php";

echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"8\" border=\"2\" bordercolor=\"#007ABE\" bgcolor=\"#E5E5E5\" style=\"font-family: Arial;\">\n";
echo "<tr bgcolor=\"#007ABE\"><td><p style=\"font-size:20px;text-align:center;color:White;font-family:Arial;\"><b>Mainchat Installation</b></p></td></tr><tr><td>\n";

switch ($_POST['aktion']) {
    case "step_2":
        if (!file_exists($configdatei)) {
            $regexemail = "|^[_a-z0-9-]+(\.[_a-z0-9]+)*@([a-z0-9-]+\.)[a-z]{2,8}$|mi";
            
            if ($_POST['chat']['lobby'] == "" || $_POST['chat']['dbase'] == ""
                || $_POST['chat']['host'] == "" || $_POST['chat']['user'] == ""
                || $_POST['chat']['pass'] != $_POST['chat']['pass2']
                || ($_POST['chat']['webmaster'] == ""
                    || (!preg_match($regexemail, $_POST['chat']['webmaster'])))
                || ($_POST['chat']['hackmail'] == ""
                    || (!preg_match($regexemail, $_POST['chat']['hackmail'])))
                || $_POST['chat']['chatname'] == "") {
                echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n"
                    . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>FEHLER</b></td></tr>\n";
                if ($_POST['chat']['lobby'] == "")
                    echo "<tr style=\"color:red;font-weigth:bold;\"><td>Bitte tragen Sie ein Lobby Defaultraum ein!</td></tr>\n";
                if ($_POST['chat']['dbase'] == "")
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbanknamen ein!</td></tr>\n";
                if ($_POST['chat']['host'] == "")
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbankserver ein!</td></tr>\n";
                if ($_POST['chat']['user'] == "")
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Bitte tragen Sie den Datenbankuser ein!</td></tr>\n";
                if ($_POST['chat']['pass'] != $_POST['chat']['pass2'])
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Das Passwort stimmt nicht überein!</td></tr>\n";
                if ($_POST['chat']['webmaster'] == "")
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Bitte tragen Sie einen Webmaster ein!</td></tr>\n";
                if ((!preg_match($regexemail, $_POST['chat']['webmaster']))
                    && ($_POST['chat']['webmaster'] != ""))
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Ungültige Email-Adresse Webmaster!</td></tr>\n";
                if ($_POST['chat']['hackmail'] == "")
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Bitte tragen Sie eine zutändige Person bei Hackversuchen ein!</td></tr>\n";
                if ((!preg_match($regexemail, $_POST['chat']['hackmail']))
                    && ($_POST['chat']['hackmail'] != ""))
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Ungültige Email-Adresse Hackmail!</td></tr>\n";
                if ($_POST['chat']['chatname'] == "")
                    echo "<tr style=\"color:red; font-weigth:bold;\"><td>Bitte tragen Sie den Chatnamen ein!</td></tr>\n";
                echo "<tr><td colspan=\"2\"><br><br></td></tr></table>\n";
                step_1();
            } else {
                $fp = @fopen($configdatei, "w+");
                if (!$fp) {
                    echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n"
                        . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>FEHLER</b></td></tr>\n"
                        . "<tr style=\"color:red;font-weigth:bold;\"><td>Die Konfigurationsdatei konnte nicht angelegt werden. Überprüfen Sie die Schreibrechte im Verzeichnis conf!</td></tr>\n"
                        . "<tr><td colspan=\"2\"><br><br></td></tr></table>\n";
                    step_1();
                } else {
                	if (!@$mysqli_link = mysqli_connect($_POST['chat']['host'],
                        $_POST['chat']['user'], $_POST['chat']['pass'])) {
                        echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n"
                            . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>FEHLER</b></td></tr>\n"
                            . "<tr style=\"color:red; font-weigth:bold;\"><td>FEHLER: Datenbankverbindung fehlgeschlagen!</td></tr>\n"
                            . "<tr><td colspan=\"2\"><br><br></td></tr></table>\n";
                        unlink($configdatei);
                        step_1();
                    } else {
                        mysqli_set_charset($mysqli_link, "utf8mb4");
                        if (!$select = mysqli_select_db($mysqli_link, $_POST['chat']['dbase'])) {
                        	$select = mysqli_select_db($mysqli_link, $_POST['chat']['dbase']);
                            step_2($mysqli_link, $select, $_POST['chat'], $fp);
                        } else step_2($mysqli_link, $select, $_POST['chat'], $fp);
                    }
                }
            }
        } else {
            echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n"
                . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>FEHLER</b></td></tr>\n"
                . "<tr style=\"color:red; font-weigth:bold;\"><td>FEHLER-Die Datei config.php existiert bereits!</td></tr>\n"
                . "<tr><td colspan=\"2\"><br><br></td></tr></table>\n";
            step_1();
        }
        break;
    
    default:
        if (file_exists($configdatei)) {
            echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\" align=\"center\">\n"
                . "<tr bgcolor=\"#007ABE\"><td colspan=\"2\" style=\"font-size:15px; text-align:center;color:White;\"><B>FEHLER</b></td></tr>\n"
                . "<tr style=\"color:red; font-weigth:bold;\"><td>FEHLER-Die Datei config.php existiert bereits!</td></tr>\n"
                . "<tr><td colspan=\"2\"><br><br></td></tr></table>\n";
        } else {
            step_1();
        }
        break;
}

echo "</td></tr></table>\n";
?>
</body>
</html>