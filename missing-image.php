<?php

	//echo "This is a missing image served up by PHP";
	//echo "Caller:" . $_SERVER['REQUEST_URI'];
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

	// extracted basename
	$filename = basename($path);
	echo $filename;
	exit(0);

	$url = "https://staging.atomjump.com/api/images/im/upl440-47456560.jpg";
	
	$img = __DIR__ . '/images/im/' . $filename;
	file_put_contents($img, file_get_contents($url));
	
	header('Content-type: image/jpeg');
	readfile($img);


?>