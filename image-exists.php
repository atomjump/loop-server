<?php
	include_once(__DIR__ . '/config/db_connect.php');		
	
	global $local_server_path;
	global $root_server_url;
	global $cnf;
	
	
	if($_REQUEST['code'] == $cnf['uploads']['imagesShare']['checkCode']) {
		$filename = str_replace("/", "", $_REQUEST['image']);
		$filename = str_replace("..", "", $filename);
		if(file_exists(__DIR__ . '/images/im/' . $filename)) {
			echo "true";
		
		} else {
			echo "false";
		}
	
	} else {
		echo "none";
	}

?>