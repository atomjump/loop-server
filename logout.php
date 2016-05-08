<?php 
	require('config/db_connect.php');
	
    require("classes/cls.layer.php");
	require("classes/cls.ssshout.php");
	
	$ly = new cls_layer();
	$sh = new cls_ssshout();
   
	//Do not remove the 'ses' value just yet.
	
	//This must be off:$_SESSION['authenticated-layer'] = '';
	$_SESSION['logged-user'] = '';
	$_SESSION['logged-email'] = '';
	$_SESSION['user-ip'] = '';
	$_SESSION['temp-user-name'] = '';
	$_SESSION['lat'] = '';
	$_SESSION['lon'] = '';
	$_SESSION['logged-group-user'] = '';
	$_SESSION['layer-group-user'] = '';
	
	$_SESSION['view-count'] = 0; //testing this
    

    //$_SESSION = array();

	$ip = $ly->getRealIpAddr();
    $user_id = $sh->new_user('', '', '', true);
    
    $_SESSION['logged-user'] = $user_id;		//This will be a 'temporary user id' - for their ip address only
	$_SESSION['user-ip'] = $ip;					//Save their ip in this session

    
    error_log("Logging out");
    session_write_close();      //Ensure we don't have anything that runs after this command that uses the sessions 

 
     // Remove any cookies
     setcookie("your_name", "deleted", time() - 3600);
     setcookie("email", "deleted", time() - 3600);
     setcookie("phone", "deleted", time() - 3600);
     setcookie("your_password", "deleted", time() - 3600);
 

    //Note: we should keep this session open
 
?>
