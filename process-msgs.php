<?php

    //On a one-off, convert all messages into processed version of message. You would use this script
    //if the software for processing messages had changed, and you needed to refresh for the new format.

	
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
	$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
	while($row = db_fetch_array($result))
	{
	
	
 		list($ssshout_processed, $include_payment) = $sh->process_chars($row['var_shouted'],$row['var_ip'],$row['int_author_id'],$row['int_ssshout_id']);	
		$sqlb = "UPDATE tbl_ssshout SET var_shouted_processed = '" . clean_data_keep_tags($ssshout_processed) . "' WHERE int_ssshout_id = " . $row['int_ssshout_id'];
		$resultb = dbquery($sqlb)  or die("Unable to execute query $sql " . dberror());
		echo ".";
	
	}
	


?>
