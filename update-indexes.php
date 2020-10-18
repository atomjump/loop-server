<?php

//Email confirmation
require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

	//Allowed to run this script once. We will use the images folder because it has general access already.
	$maiden_check = trim_trailing_slash(__DIR__) . "/images/im/indexes_changed_0.txt";
	if(!file_exists($maiden_check)) {
		
		$sql = "CREATE INDEX ordered_ssshout_id ON tbl_ssshout (enm_active, int_layer_id, date_when_shouted, int_ssshout_id, var_whisper_to, var_ip)";
		dbquery($sql) or die("Sorry there was an error: unable to execute query. " . dberror());
		
		$sql = "CREATE INDEX ordered_ssshout_big_id ON tbl_ssshout (enm_active, int_layer_id, date_when_shouted, int_ssshout_id, var_whisper_to, var_ip, int_author_id, int_whisper_to_id)";
		dbquery($sql) or die("Sorry there was an error: unable to execute query. " . dberror());
		
		$sql = "ALTER TABLE tbl_ssshout DROP INDEX ordered_ssshout_full";
		dbquery($sql) or die("Sorry there was an error: unable to execute query. " . dberror());
		
		$sql = "ALTER TABLE tbl_ssshout DROP INDEX ordered_ssshout_big";
		dbquery($sql) or die("Sorry there was an error: unable to execute query. " . dberror());
		
		file_put_contents($maiden_check, "Indexes have been run once.");
		
		echo "Successfully changed the indexes. Go back to <a href='index.php'>home</a>";
	} else {
		echo "Sorry this has been run already. Go back to <a href='index.php'>home</a>";
	}

?>
