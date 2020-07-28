<?php
	//copy image
	
	if(isset($_POST['targetpath'])) {
		$target_path = $_POST['targetpath'];
	} else {
		$target_path = "/images/property/";
	}
	
	$encoded_file=$_POST['file'];
	$decoded_file=base64_decode($encoded_file);
	$dest = getcwd() . $target_path . $_POST['FILENAME'];
		
	/*Now you can copy the uploaded file to your server.*/
	file_put_contents($dest,$decoded_file);

?>
