<?php
	//copy image
	
	$encoded_file=$_POST['file'];
	$decoded_file=base64_decode($encoded_file);
	$dest = getcwd() . "/images/property/" . $_POST['FILENAME'];
	
	error_log("Destination for image " . $_POST['FILENAME'] . " is " . $dest);
	echo "Destination for image " . $_POST['FILENAME'] . " is " . $dest;
	
	/*Now you can copy the uploaded file to your server.*/
	file_put_contents($dest,$decoded_file);

?>
