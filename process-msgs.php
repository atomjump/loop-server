<?php

 //On a one-off, convert all messages into processed version of message.

	$start_path = "/var/www/html/feedback/";

	//Only include the following three lines when testing on staging server
	/*$start_path = "/var/www/html/atomjump_staging/";
	$_SERVER["SERVER_NAME"] = "staging.atomjump.com";		//TESTING ONLY!
	$staging = true;			//TESTING ONLY
	*/
	
	$notify = false;
	include_once('config/db_connect.php');	
	
	
	require($start_path . "classes/cls.basic_geosearch.php");
	require($start_path . "classes/cls.layer.php");
	require($start_path . "classes/cls.ssshout.php");

	$bg = new clsBasicGeosearch();
	$ly = new cls_layer();
	$sh = new cls_ssshout();
	
	
	
	
	
	
	//Read the email
	$sql = "SELECT * FROM tbl_ssshout";
	$result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
	while($row = mysql_fetch_array($result))
	{
	
	
 		list($ssshout_processed, $include_payment) = $sh->process_chars($row['var_shouted'],$row['var_ip'],$row['int_author_id'],$row['int_ssshout_id']);	
		$sqlb = "UPDATE tbl_ssshout SET var_shouted_processed = '" . clean_data_keep_tags($ssshout_processed) . "' WHERE int_ssshout_id = " . $row['int_ssshout_id'];
		$resultb = mysql_query($sqlb)  or die("Unable to execute query $sql " . mysql_error());
		echo ".";
	
	}
	


?>
