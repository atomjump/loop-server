<?php

require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

//Confirm the user and password are correct - display after clicking advanced


$lg = new cls_login();


if($_REQUEST['usercode']) {

		//Read
		$json = $lg->get_usercode();
	
} else {
	//Confirm email/password
	$json = $lg->confirm($_REQUEST['email-opt'], $_REQUEST['pd'], $_REQUEST['ph'], $_REQUEST['users'], $_REQUEST['passcode']);
}

echo $_GET['callback'] . "(" . json_encode($json) . ")";


?>
