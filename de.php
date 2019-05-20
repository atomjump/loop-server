<?php

//Deactivate a message
require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");


$bg = new clsBasicGeosearch();
$ly = new cls_layer();
$sh = new cls_ssshout();


if(isset($_REQUEST['just_typing'])) {
	$just_typing = true;
} else {
	$just_typing = false;
}

//Must include a layer id in $_REQUEST['passcode'] also, because it could be from a completely different database in the case of scaleUp.

if($_REQUEST['passcode']) {

	$sh->deactivate_shout($_REQUEST['mid'], $just_typing);
}

//For now, let anyone remove messages
$ip = $ly->getRealIpAddr();

$json = "";

//This is a jquery ajax json call, so we need a proper return
if(isset($_GET['callback'])) {
	echo $_GET['callback'] . "(" . json_encode($json) . ")";
}

?>
