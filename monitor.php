<?php
	//Run this script on a cron job, once every 5 minutes or so.
	//Connect to the database
	include_once('config/db_connect.php');
	
	echo "Total disk space " . disk_total_space("/") . "\n";
	echo "Free disk space " . disk_free_space("/") . "\n";
	
	$total_disk = disk_total_space("/");
	$total_free = disk_free_space("/");
	$total_used = $total_disk - $total_free;
	$disk_perc_used = $total_used / $total_disk * 100.0;
	echo "Disk used perc: " . $disk_perc , "\n"; 
	
	if($disk_perc_used > $cnf['warningDiskUsage']) {
		//The local drive has breached the warning level
	}
	
	$load = sys_getloadavg();
	echo "Load average CPU: " . json_encode($load) . "\n";
	
?>
