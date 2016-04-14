<?php

//Email confirmation
require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

//Confirm the user and password are correct


$lg = new cls_login();

$json = $lg->email_confirm($_REQUEST['d']);

echo $json;		

?>
