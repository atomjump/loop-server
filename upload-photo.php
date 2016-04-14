<?php
		//Upload a photo to Amazon
		
		//$staging = true;		//TESTING ON STAGING ONLY

	
	include_once("config/db_connect.php");



	$_REQUEST['title'] = "upl" . $_SESSION['logged-user'] . "-" . rand(1,100000000);		//random image name for now - TODO better with ID
	$image_path = "/images/im/";
	$message = "";
	$images_script = "send-images-upload.php";
	require_once("components/upload.php");
	
	if($uploaded == true) {		
		$url = $cnf['amazonAWS']['imageURL'] . $_REQUEST['title'] . ".jpg";
	} else {
		$url = null;
	}
	
	$json = array("url" => $url, "msg" => $message);
	
	echo json_encode($json);
?>

