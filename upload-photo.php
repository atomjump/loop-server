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


	$_REQUEST['title'] = "upl" . $_SESSION['logged-user'] . "-" . rand(1,100000000);		//random image name for now - TODO better with ID
	$image_path = "/images/im/";
	$message = "";
	/*$images_script = __DIR__ . "/send-images-upload.php";
	require_once(__DIR__ . "/components/upload.php");
	*/
	
		
	//echo $_POST['images'][0];  TODO: loop through
	
	$message = "Sorry there was an error in processing.";
	//error_log("Passed in: " . json_encode($_POST));
	
	if($_POST['images'][0]) {
		$output_file = __DIR__ . $target_dir . $_REQUEST['title'] . ".jpg";
		base64ToImage($_POST['images'][0], $output_file);
		//echo "Written output file " . $output_file;
		$uploaded = true;
		
		$message .= substr($_POST['images'][0], 50);		//TEMP MESSAGE
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

