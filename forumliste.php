<?php
include("functions.php");
$query = "SELECT o_who,o_name,o_level,r_name,r_status1,r_status2, r_name='Lobby' as lobby FROM online left join raum on o_raum=r_id WHERE (UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(o_aktiv)) <= 300 and r_name is null ORDER BY lobby desc,r_name,o_who,o_name";
$result = mysql_query($query);
$text = "";
while ($a = mysqli_fetch_array($result)) {
    $text .= "$a[o_name] &nbsp;";
}
show_box2("User online im FORUM:", $text, "100%", false);
?>