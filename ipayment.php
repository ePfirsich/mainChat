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
$result = mysql_query($query);
$a = @mysql_fetch_array($result);
@mysql_free_result($result);

// Dazuaddieren
$f['u_sms_guthaben'] = $a['u_sms_guthaben'] + $gekauftesms;
// Schreiben
$f['ui_id'] = schreibe_db("user", $f, $u_id, "u_id");

$conn2 = mysql_connect("localhost", "www", "");
mysql_set_charset("utf8mb4");
mysql_selectdb("ipayment", $conn2);
$query = "INSERT INTO transaction_log (u_nick, u_id, datum, handynr, ip, http_host, trx_amount) VALUES ('" . mysql_real_escape_string($u_nick) . "','" . mysql_real_escape_string($u_id) . "',NOW(),'" . mysql_real_escape_string($handynr) . "','" . mysql_real_escape_string($ret_ip) . "','" . mysql_real_escape_string($http_host) . "','" . mysql_real_escape_string($trx_amount) . "')";
$result = mysql_query($query);
$id = mysql_insert_id();
$v = mysql_real_escape_string($v);
$query = "INSERT INTO payment_log (id, payment_text) VALUES ('$id','$v')";
$result = mysql_query($query);
?>