<?php
	//Upload a photo to Amazon
		

	include_once(__DIR__ ."/config/db_connect.php");



	$_REQUEST['title'] = "upl" . $_SESSION['logged-user'] . "-" . rand(1,100000000);		//random image name for now - TODO better with ID
	$image_path = "/images/im/";
	$message = "";
	$images_script = __DIR__ . "/send-images-upload.php";
	require_once(__DIR__ . "/components/upload.php");
	
	if($uploaded == true) {		
		if($cnf['uploads']['use'] == "amazonAWS") {
			$url = $cnf['uploads']['vendor']['amazonAWS']['imageURL'] . $_REQUEST['title'] . ".jpg";
		} else {
			global $root_server_url;
			$url = $root_server_url . $image_path . $_REQUEST['title'] . ".jpg";
		}
	} else {
		$url = null;
	}
	
	$json = array("url" => $url, "msg" => $message);
	
	echo json_encode($json);
?>

