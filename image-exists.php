<?php

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