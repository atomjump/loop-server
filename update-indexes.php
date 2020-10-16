<?php

//Email confirmation
require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

	//Allowed to run this script once.
	$maiden_check = "config/indexes_changed_0.txt";
	if(!file_exists($maiden_check)) {
		file_put_contents($maiden_check, "Indexes have been run once.");
		$sql = "CREATE INDEX ordered_ssshout_id ON tbl_ssshout (enm_active, int_layer_id, date_when_shouted, int_ssshout_id, var_whisper_to, var_ip); CREATE INDEX ordered_ssshout_big_id ON tbl_ssshout (enm_active, int_layer_id, date_when_shouted, int_ssshout_id, var_whisper_to, var_ip, int_author_id, int_whisper_to_id); ALTER TABLE tbl_ssshout DROP INDEX ordered_ssshout_full; ALTER TABLE tbl_ssshout DROP INDEX ordered_ssshout_big;";
dbquery($sql);
	}

?>
