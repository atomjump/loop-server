<?php

	//echo "This is a missing image served up by PHP";
	//echo "Caller:" . $_SERVER['REQUEST_URI'];
	
	$url = "https://staging.atomjump.com/api/images/im/upl440-47456560.jpg";
	
	$img = __DIR__ . '/images/im/testthis.jpg';
	file_put_contents($img, file_get_contents($url));
	
	header('Content-type: image/jpeg');
	readfile($img);


?>