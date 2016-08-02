<?php
	//Cron job to remove and trailing 'typing' entries after 15 minutes. We can run this every 5 minutes, but make sure it is only on one machine.
	//To install put the following line in after typing 
	//		sudo crontab -e
	//		*/5 * * * *	/usr/bin/php /var/www/html/feedback/typing-cron.php
	$agent = "AJ feed bot - https://atomjump.com";
	ini_set("user_agent",$agent);
	$_SERVER['HTTP_USER_AGENT'] = $agent;
	$start_path = "/var/www/html/feedback/";

	include_once('config/db_connect.php');		

	$sql = "UPDATE tbl_ssshout SET enm_active = false where enm_active = true and enm_status = 'typing' and date_when_shouted < DATE_SUB(NOW(),INTERVAL 15 MINUTE)";
	dbquery($sql) or die("Unable to execute query $sql " . dberror());
	
?>
