<?php
session_start();

require_once("./conf/config.php");
require_once("./class.chat.php");
if(isset($_GET['lastTimeID'])) {
	$last_time_id = intval( $_GET['lastTimeID'] );
} else {
	$last_time_id = 0;
}

if($last_time_id >= $_SESSION['lastTimeID']) {
	if($last_time_id >= 30)
	{
		$_SESSION['lastTimeID'] = $last_time_id - 30;
	}
	else
	{
		$_SESSION['lastTimeID'] = $last_time_id - 3;
	}
}

if($last_time_id >= $_SESSION['lastTimeID'] - 30) {

} else {
	$last_time_id = $_SESSION['lastTimeID'];

}

$jsonData = chatClass::getRestChatLines($last_time_id);

print $jsonData;
?>