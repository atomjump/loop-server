<?php

//Download spreadsheet

//Note, we will need read only capability later on
$db_read_only = false;				//Ensure we can palm this off to RDS replicas - we are only reading here, never writing
									//except where we write because of the sessions vars.  Have added a test to reconnect with the
									//master in that case.
require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

$sh = new cls_ssshout();
$lg = new cls_login();

$logged = false;
if(($_SESSION['logged-user'] != '')&&(isset($_SESSION['logged-user']))) {
	//Already logged in, but check if we know the ip address
 $logged = true;				
 
 //Get the current layer - use to view 
					$layer_visible = $_REQUEST['uniqueFeedbackId'];
					
					$ly = new cls_layer();
					$layer_info = $ly->get_layer_id($layer_visible, null);
					if($layer_info) {
						$_SESSION['authenticated-layer'] = $layer_info['int_layer_id'];
					}
			
} else {

  if($sh->check_email_exists($_REQUEST['email'])) {
    if($lg->confirm($_REQUEST['email'], $_REQUEST['pass'], null, null, $_REQUEST['uniqueFeedbackId']) == 'LOGGED_IN')
	   {
	     $logged = true;
	   }
	 }
}



if($logged == true) {

  $se = new cls_search();
 
  if($_REQUEST['from_id']) {
     $from = $_REQUEST['from_id'];
  } else {
    $from = 0;
  }
  
  if($_REQUEST['format']) {
     $format = $_REQUEST['format'];
  } else {
    $format = "json";
  }
  
  if($_REQUEST['duration']) {
    $duration = $_REQUEST['duration'];
  } else {
    $duration = 900;
  }
  
  $se->process(NULL, NULL, 2000,  true, $from, $db_timezone, $format, $duration);
 
 
} else {
 //wrong username
  echo "{ 'Error' : 'Wrong credentials.' }";
}
?>
