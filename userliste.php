<?php

require("functions.php");

// User die gerade Online sind als Liste ausgeben
// Diese Liste kann webchat.de einlesen und damit die Liste unserer User ausgeben

if (preg_match("/hilfe/", $REQUEST_URI)) {
    echo "optionale Parameter:<br>";
    echo "userliste2.php?zeit=j&klammer=j&datenbank=j&raum=j";
    exit;
}

if ($keinbr == "j")
    $br = "\n";
else $br = "<br>\n";

$query = "SELECT o_name,r_name,UNIX_TIMESTAMP(o_aktiv) as login FROM raum,online "
    . "WHERE o_raum=r_id "
    . "AND (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= $timeout "
    . "ORDER BY r_name,o_user ";
$result = @mysql_query($query, $conn);
$rows = @mysqli_num_rows($result);

if ($rows > 0) {
    $i = 0;
    $r_name_alt = "";
    while ($i < $rows) {
        if ($datenbank == "j") {
            if ($klammer == "j")
                echo "(";
            print "$dbase";
            if ($klammer == "j")
                echo ") ";
        }
        
        $row = @mysqli_fetch_array($result);
        if ($klammer == "j")
            echo "(";
        echo $row[o_name];
        if ($klammer == "j")
            echo ")";
        echo " ";
        if ($zeit != "n") {
            if ($klammer == "j")
                echo "(";
            echo $row[login];
            if ($klammer == "j")
                echo ")";
        }
        if ($raum != "n" && $webchatraumausgabe == 1) {
            $room = $webchatprivatraum;
            for ($j = 0; $j < count($webchatpredef); $j++) {
                if ($webchatpredef[$j] == $row[r_name])
                    $room = $row[r_name];
            }
            echo " ";
            if ($klammer == "j")
                echo "(";
            echo "$room";
            if ($klammer == "j")
                echo ")";
        }
        echo "$br";
        $i++;
    }
    mysqli_free_result($result);
}

?>
