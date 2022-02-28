<?php
session_start();

require_once("conf/config.php");
require_once("class.chat.php");
$last_time_id = intval( $_GET[ 'lastTimeID' ] );

if($last_time_id >= $_SESSION['lastTimeID']) {
	$_SESSION['lastTimeID'] = $last_time_id - 28;
}

if($last_time_id >= $_SESSION['lastTimeID']) {

} else {
	$last_time_id = $_SESSION['lastTimeID'];

}

$jsonData = chatClass::getRestChatLines($last_time_id);

print $jsonData;
?>