<?php
session_start();
require("functions.php");
$u_id = $_SESSION['u_id'];
$file = $_FILES["file"]["name"];


include "./conf/config.php";


$query2 = "SELECT * FROM user WHERE u_id LIKE '" . $u_id . "'";
$result2 = mysqli_query($mysqli_link, $query2);


if ($result2 && mysqli_num_rows($result2) == 1) {
	$row2 = mysqli_fetch_object($result2);

	$path_parts = pathinfo($_FILES['file']['name']);
	$dateiname_neu = "".$u_id.".".$path_parts['extension'];
	
	var_dump("test1");
	if($path_parts['extension'] == "jpg"
		|| $path_parts['extension'] == "JPG"
		|| $path_parts['extension'] == "png"
		|| $path_parts['extension'] == "PNG"
		|| $path_parts['extension'] == "gif"
		|| $path_parts['extension'] == "GIF") {
		
		var_dump("test2");
		var_dump("/avatars/".$dateiname_neu);
	
		$move = move_uploaded_file($_FILES['file']['tmp_name'], "./avatars/".$dateiname_neu);

		$query = "UPDATE user SET ui_avatar = '" . $dateiname_neu . "' WHERE u_id = '" . $u_id . "'";
		mysqli_query($mysqli_link, $query);
		
		return("Fehler");
		
	}


}
?>