<?php
	//Upload a photo to Amazon or our own server
		

	//TESTING IMMEDIATE
	$target_dir = "/images/im/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			echo $_FILES["fileToUpload"]["tmp_name"] . " file is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo $_FILES["fileToUpload"]["tmp_name"] . " file is not an image.";
			$uploadOk = 0;
		}
	}
/*
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
	*/
?>

