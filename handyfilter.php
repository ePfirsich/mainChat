<?php

// Liest aus der Userinfo die Handynummern, formatiert diese und schreibt sie zurück

include("functions.php");
include("smstools.php");

$query = "SELECT ui_id, ui_handy FROM userinfo";
$result = mysqli_query($mysqli_link, $query);
while ($a = mysqli_fetch_array($result)) {
    $nummer = FormatNumber($a[ui_handy]);
    
    $query = "UPDATE userinfo SET ui_handy = '" . mysqli_real_escape_string($mysqli_link, $nummer) . "' WHERE ui_id = " . intval($a[ui_id]);
    print "<li>$a[ui_id] - $a[ui_handy] => $nummer<BR>";
    mysqli_query($mysqli_link, $query);
}
?>