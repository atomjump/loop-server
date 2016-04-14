<?php
	//check database connection status
	include_once('config/db_connect.php');
	
	//If got through to here, the db is fine,
	http_response_code(200);
	
?>
