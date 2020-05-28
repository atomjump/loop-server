<?php

	echo "This is a missing image served up by PHP";
	echo "Caller:" . $_SERVER['REQUEST_URI'];
	
	$image = "https://staging.atomjump.com/api/images/im/upl440-47456560.jpg";
	header('Content-type: image/jpeg');
	readfile($image);


?>