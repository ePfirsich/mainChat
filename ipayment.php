<?php

// Dieses Programm schreibt dem User seine Über IPayment gekauften SMS gut...
include("functions.php");

reset($HTTP_POST_VARS);
while (list($key, $val) = each($HTTP_POST_VARS)) {
    $v .= "$key => $val\n";
}
mail("martin@huskie.de", "Mainchat-Chat-SMS-Kauf erfolgreich!", $v,
    "From: info@fidion.de\nReturn-path: info@fidion.de\n");

// Nur der IPayment-Gateway darf hier durch
if ($REMOTE_ADDR != "195.20.224.139") {
    print "IP error!";
    exit;
}

// Betrag in EUR-Cent
$cc_amount = $HTTP_POST_VARS['trx_amount'];

// Ausrechnen wieviele SMS der User bekomt
$gekauftesms = floor($cc_amount / $sms[preis] + 0.5);

// Auslesen des bisherigen Guthabens
$query = "SELECT u_sms_guthaben FROM user WHERE u_id = '$u_id'";
$result = mysqli_query($mysqli_link, $query);
$a = @mysqli_fetch_array($result);
@mysqli_free_result($result);

// Dazuaddieren
$f['u_sms_guthaben'] = $a['u_sms_guthaben'] + $gekauftesms;
// Schreiben
$f['ui_id'] = schreibe_db("user", $f, $u_id, "u_id");

$mysqli_link2 = mysqli_connect('p:'."localhost", "user", "password", "database");
mysqli_set_charset($mysqli_link, "utf8mb4");
mysqli_select_db("ipayment", $mysqli_link2);
$query = "INSERT INTO transaction_log (u_nick, u_id, datum, handynr, ip, http_host, trx_amount) VALUES ('" . mysqli_real_escape_string($mysqli_link, $u_nick) . "','" . mysqli_real_escape_string($mysqli_link, $u_id) . "',NOW(),'" . mysqli_real_escape_string($mysqli_link, $handynr) . "','" . mysqli_real_escape_string($mysqli_link, $ret_ip) . "','" . mysqli_real_escape_string($mysqli_link, $http_host) . "','" . mysqli_real_escape_string($mysqli_link, $trx_amount) . "')";
$result = mysqli_query($mysqli_link, $query);
$id = mysqli_insert_id($mysqli_link);
$v = mysqli_real_escape_string($mysqli_link, $v);
$query = "INSERT INTO payment_log (id, payment_text) VALUES ('$id','$v')";
$result = mysqli_query($mysqli_link, $query);
?>