<?php
	//Run this script on a cron job, once every 5 minutes or so.
	//E.g   */5 * * * *     /usr/bin/php /yourpath/atomjump/loop-server/monitor.php
	//
	//To add this to your crontab via a script e.g.
	//line="*/5 * * * *     /usr/bin/php /yourpath/atomjump/loop-server/monitor.php"; (crontab -u root -l; echo "$line" ) | crontab -u root -
	//
	//which will create and delete the files:
	// /images/im/capacity/within-disk-capacity-warning.html
	// /images/im/capacity/within-cpu-capacity-warning.html 
	//
	//(Exists) within capacity
	//(Does not exist) over capacity
	
		
	//Connect to the database
	include_once('config/db_connect.php');
	
	echo "Total disk space " . disk_total_space("/") . "\n";
	echo "Free disk space " . disk_free_space("/") . "\n";
	
	$total_disk = disk_total_space("/");
	$total_free = disk_free_space("/");
	$total_used = $total_disk - $total_free;
	$disk_perc_used = $total_used / $total_disk * 100.0;
	echo "Disk used perc: " . $disk_perc_used , "\n"; 
	
	$within_capacity_folder = __DIR__ . "/images/im/capacity/";
	$within_capacity_file = __DIR__ . '/images/im/capacity/within-disk-capacity-warning.html';
	
	if(isset($cnf['warningDiskUsage'])) {
		$warning_disk_usage_perc = $cnf['warningDiskUsage']; 
	} else {
		$warning_disk_usage_perc = 90;			//Default to 90 perc disk will trigger a warning
	}
	
	if($disk_perc_used > $warning_disk_usage_perc) {
		//The local drive has breached the warning level	
		if(file_exists($within_capacity_file)) {
			//Remove the warning file, so that a remote service can see we are not within capacity, 
			//by the file's external lack of response from e.g. https://your.service.com/api/images/im/within-disk-capacity-warning.html.
			unlink($within_capacity_file);
		}
		
	} else {
			//Under the warning level threshold
			if(file_exists($within_capacity_file)) {
				//No need to do anything, it already exists, so we know we're within capacity
			} else {
				//Create the file again
				
				if (!file_exists($within_capacity_folder)) {
					mkdir($within_capacity_folder);
				}
				$handle = fopen($within_capacity_file, 'w');			
				if(!$handle) {
					 error_log("Cannot create disk capacity warning file:  " . $within_capacity_file .
									 "   Please make sure your /images/im/capacity folder is writable by your public web server user.");
				} else {
					$data = '<html><body>You messaging server is within disk capacity. If this file disappears, you have crossed the warning level on server disk capacity.</body></html>';
					fwrite($handle, $data);
				}
			}
	
	}
	
	
		
	if(isset($cnf['warningCPUUsage'])) {
		$warning_cpu_usage_perc = $cnf['warningCPUUsage']; 
	} else {
		$warning_cpu_usage_perc = 75;
	}
	
	$within_capacity_file = __DIR__ . '/images/im/capacity/within-cpu-capacity-warning.html';
	
	$load = sys_getloadavg();
	echo "Load average CPU: " . json_encode($load) . "\n";
	//This returns e.g. [0.2,0.24,0.15] with 1 minute, 5 minute, 15 minute averages. We'll use the 5 minute.
	$five_minute_perc = $load[1] * 100;
	if($five_minute_perc > $warning_cpu_usage_perc) {
		//The local CPU has breached the warning level
		if(file_exists($within_capacity_file)) {
			//Remove the warning file, so that a remote service can see we are not within capacity, 
			//by the file's external lack of response from e.g. https://your.service.com/api/images/im/within-disk-capacity-warning.html.
			unlink($within_capacity_file);
		}
	} else {
		//Under the warning level threshold
		if(file_exists($within_capacity_file)) {
			//No need to do anything, it already exists, so we know we're within capacity
		} else {
			//Create the file again
			
			if (!file_exists($within_capacity_folder)) {
				mkdir($within_capacity_folder);
			}
			$handle = fopen($within_capacity_file, 'w');			
			if(!$handle) {
				 error_log("Cannot create CPU capacity warning file:  " . $within_capacity_file .
								 "   Please make sure your /images/im/capacity folder is writable by your public web server user.");
			} else {
				$data = '<html><body>You messaging server is within CPU capacity. If this file disappears, you have crossed the warning level on server CPU capacity.</body></html>';
				fwrite($handle, $data);
			}
		}
	
	}
	
?>
