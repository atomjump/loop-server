<?php
	//copy image
	
	$encoded_file=$_POST['file'];
	$decoded_file=base64_decode($encoded_file);
	$dest = getcwd() . "/images/property/" . $_POST['FILENAME'];
		
	/*Now you can copy the uploaded file to your server.*/
	file_put_contents($dest,$decoded_file);

?>
