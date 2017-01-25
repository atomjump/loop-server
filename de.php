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

$sh->deactivate_shout($_REQUEST['mid'], $just_typing);


//For now, let anyone remove messages
$ip = $ly->getRealIpAddr();

echo "ok";

?>