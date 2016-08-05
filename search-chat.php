<?php

//Main message listing request, PHP version

$db_read_only = true;				//Ensure we can palm this off to RDS replicas - we are only reading here, never writing
									//except where we write because of the sessions vars.  Have added a test to reconnect with the
									//master in that case.


require('config/db_connect.php');



require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

$sh = new cls_ssshout();



if(($_SESSION['logged-user'] != '')&&(isset($_SESSION['logged-user']))) {
	//Already logged in, but check if we know the ip address
	if((!isset($_SESSION['user-ip']))||($_SESSION['user-ip'] == '')) {
		//OK - we have likely logged off and then come back again. Get our ip address again
		$ly = new cls_layer();
		//$sh = new cls_ssshout();
	 
    	
		$ip = $sh->get_user_ip($_SESSION['logged-user']);

		$_SESSION['user-ip'] = $ip;					//Save their ip in this session
	}
} else {
	

		
	//First request from new user, make sure we get the ip address
	if(($_SERVER['SERVER_PORT'] == $cnf['logoutPort'])||   // this case is after a logout
	  (intval($_SESSION['view-count']) == 0)||
	  (!isset($_SESSION['view-count']))) {
        
        if(!isset($_SESSION['view-count'])) {
            $_SESSION['view-count'] = 0;    //initialise
        }

		$ly = new cls_layer();
		
		$ip = $ly->getRealIpAddr();
		
		make_writable_db();
		$user_id = $sh->new_user('', $ip, null, true);		//Was NULL
		
		$_SESSION['logged-user'] = $user_id;		//This will be a 'temporary user id' - for their ip address only
		$_SESSION['user-ip'] = $ip;					//Save their ip in this session
	
	}
}


//Count the number of times we've searched in this session
if(intval($_SESSION['view-count']) == 0) {
	//Note: a db write operation
	
	$_SESSION['view-count'] = intval($_SESSION['view-count']) + 1;

}


$se = new cls_search();

$se->process(NULL, NULL, $_REQUEST['records']);

session_write_close(); 

?>
