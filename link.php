<?php

//Email confirmation
require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

//Confirm the user and password are correct


$lg = new cls_login();

$main_message = $lg->email_confirm($_REQUEST['d']);

$follow_on_link = "https://atomjump.com";
if($cnf['serviceHome']) {
	$follow_on_link = add_subdomain_to_path($cnf['serviceHome']);
}

$first_button_wording = "&#8962;";		//A 'home' UTF-8 char

$first_button = $follow_on_link;


 include("components/basic-page.php");
?>
