<?php
	//Upload a photo to Amazon or our own server
	
	include_once(__DIR__ ."/config/db_connect.php");
	

	$target_dir = "/images/im/";
	
	
	
	
	
	function base64ToImage($base64_string, $output_file) {
		$file = fopen($output_file, "wb");

		$data = explode(',', $base64_string);

		fwrite($file, base64_decode($data[1]));
		fclose($file);

		return $output_file;
	}

		

	include_once(__DIR__ ."/config/db_connect.php");


		
	global $cnf;

	$_REQUEST['title'] = "upl" . $_SESSION['logged-user'] . "-" . rand(1,100000000);		//random image name for now - TODO better with ID
	$image_path = "/images/im/";
	$message = "";
	
		
	if($_POST['images'][0]) {
		//This is a multiple file upload
		if($cnf['uploads']['use'] == "amazonAWS") {
			$output_file = __DIR__ . $target_dir . $_REQUEST['title'] . ".jpg";
			$low_res = true;			//Want two versions of the file up there.
			$resize = true;
		} else {
			$output_file = __DIR__ . $target_dir . $_REQUEST['title'] . ".jpg";
			$resize = false;
		}
		base64ToImage($_POST['images'][0], $output_file);
		$uploaded = true;
		$hi_res = true;
		
		
		$images_script = __DIR__ . "/send-images-upload.php";
		require_once(__DIR__ . "/components/upload-multiple.php");

		
	} else {
	
		$images_script = __DIR__ . "/send-images-upload.php";
		require_once(__DIR__ . "/components/upload.php");
	}


	
	
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

