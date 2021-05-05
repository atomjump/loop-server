<?php

//Email confirmation
require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

//Confirm the user and password are correct


$lg = new cls_login();

$main_message = $lg->email_confirm($_REQUEST['d']);

if(isset($_REQUEST['id']) && isset($_REQUEST['deviceType'])) {
	//We are also trying to pair with the app at the same time. This request
	//would only come if you have the 'notifications' plugin installed.
	global $root_server_url;
	$url = trim_trailing_slash($root_server_url) . "/plugins/notifications/register.php?id=" . urlencode($_REQUEST['id']) . "&devicetype=" . urlencode($_REQUEST['deviceType']);
	
	//Redirect to the reigstration of the app.
	header("Location: " . $url, true, 301);
	exit(0);
}

$follow_on_link = "https://atomjump.com";
if($cnf['serviceHome']) {
	$follow_on_link = add_subdomain_to_path($cnf['serviceHome']);
}

$first_button_wording = "&#8962;";		//A 'home' UTF-8 char

$first_button = $follow_on_link;


 include("components/basic-page.php");
?>
