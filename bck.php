<?php
	//To be run with 
	//https://yoursite.com/bck.php?code=ewtw4
 include_once('config/db_connect.php');
	
	//See http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html#option_mysqldump_single-transaction
	if($_REQUEST['code'] == 'ewtw4') {
		system("mysqldump --single-transaction -u " . $cnf['db']['user'] . " -p" . $cnf['db']['pass'] . " " . $cnf['db']['name'] . " > backups/atomjump-loop-backup-" . date('Y-m-d--H-i-s') .".sql");
		echo "Database Backup Taken";
	}
	

?>
