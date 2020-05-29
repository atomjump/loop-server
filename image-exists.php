<?php
	include_once(__DIR__ . '/config/db_connect.php');		
	
	global $local_server_path;
	global $root_server_url;
	global $cnf;
	
	echo "Conf: " . $cnf['uploads']['imagesShare']['checkCode'] . " code=" . $_REQUEST['code'];
	
	if($_REQUEST['code'] === $cnf['uploads']['imagesShare']['code']) {
		$filename = $_REQUEST['image'];
		if(file_exists(__DIR__ . '/images/im/' . $filename)) {
			echo "true";
		
		} else {
			echo "false";
		}
	
	} else {
		echo "none";
	}

?>