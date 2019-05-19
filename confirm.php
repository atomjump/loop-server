<?php

require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

//Confirm the user and password are correct - display after clicking advanced


$lg = new cls_login();
$ly = new cls_layer();



if($_REQUEST['usercode']) {

	//Get the layer id
	$ly->get_layer_id($_REQUEST['passcode'], null);

	//Read
	$json = $lg->get_usercode();
	
} else {
	if($_REQUEST['unsub']) {
		//Unsubscribe - just needs an email address and a layer id
		$json = $lg->unsubscribe($_REQUEST['uid'], $_REQUEST['unsub']);
	} else {
		if($_REQUEST['sub']) {
			$json = $lg->subscribe($_REQUEST['uid'], $_REQUEST['sub'], $_REQUEST['fp']);
		} else {
			//Confirm email/password
			$json = $lg->confirm($_REQUEST['email-opt'], $_REQUEST['pd'], $_REQUEST['ph'], $_REQUEST['users'], $_REQUEST['passcode'], false, $_REQUEST);

			//Confirm this layer
			$ly->get_layer_id($_REQUEST['passcode'], null);
		}
	}
}

echo $_GET['callback'] . "(" . json_encode($json) . ")";


?>
