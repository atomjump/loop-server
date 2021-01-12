<?php
	//Run this script on a cron job, once every 5 minutes or so.
	//Connect to the database
	include_once('config/db_connect.php');
	
	echo "Total disk space " . disk_total_space("/") . "\n";
	echo "Free disk space " . disk_free_space("/") . "\n";
	
	
?>
