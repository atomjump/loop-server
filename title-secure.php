<?php 

 //Getting the title of the internal frame that appears within the popup
	require('config/db_connect.php');
	require("classes/cls.layer.php");
	require("classes/cls.ssshout.php");
	
	$ly = new cls_layer();
	$sh = new cls_ssshout();
	
	
	
	//We may have a possible user request in the case of receiving an email
	if(isset($_REQUEST['possible_user'])) {
	
		if(isset($_REQUEST['check'])) {
			if($sh->check_email_secure(urldecode($_COOKIE['email']), $_REQUEST['check'])) {
				//Test if there is no password on this email account, and set ourselves as the logged in user
				$sh->new_user($_COOKIE['email'], '');
				$_SESSION['logged-email'] = urldecode($_COOKIE['email']);
			}
		}
	
	}
	
	
	
	
	
	function currentdir($url) {
		// note: anything without a scheme ("example.com", "example.com:80/", etc.) is a folder
		// remove query (protection against "?url=http://example.com/")
		if ($first_query = strpos($url, '?')) $url = substr($url, 0, $first_query);
		// remove fragment (protection against "#http://example.com/")
		if ($first_fragment = strpos($url, '#')) $url = substr($url, 0, $first_fragment);
		// folder only
		$last_slash = strrpos($url, '/');
		if (!$last_slash) {
			return '/';
		}
		// add ending slash to "http://example.com"
		if (($first_colon = strpos($url, '://')) !== false && $first_colon + 2 == $last_slash) {
			return $url . '/';
		}
		return substr($url, 0, $last_slash + 1);
	}

	
	function urldir($relativeurl, $callerurl) {
	  return currentdir($callerurl) . $relativeurl;
	  //reduce callerurl from https://blahblah/dir/dir/script.xyz
	  // to https://blahblah/dir/dir/
	  // and then add a relative url to it
	  // eg. '../../dir/script.css'
	}
	
	//Optional params
	if(isset($_REQUEST['server'])) {
	    $server = $_REQUEST['server'];
	} else {
	    $server = "https://atomjump.com";
	}
	if(isset($_REQUEST['clientremoteurl'])) {
	    $clientremoteurl = $_REQUEST['clientremoteurl'];
	} else {
	    $clientremoteurl = "https://atomjump.com/index.html";
	}
	
	
	
	
	

	
	
	
	//Get the layer info into the session vars
	$layer_info = $ly->get_layer_id($_REQUEST['uniqueFeedbackId'], null);
	if(isset($layer_info['var_public_code'])) {
		$granted = false;
		
		if($_SESSION['access-layer-granted'] == $layer_info['int_layer_id']) { 	//Normal access has been granted  
			$granted = true;
		}
    } else {
    	 $granted = true;
    }
	//Get new user in here, and set user IP address in session
	
	
	//Keep track of the number of views we have from this session - also reset if reloading
	$_SESSION['view-count'] = 0;
	
		
	
	//Ensure no caching
	header("Cache-Control: no-store, no-cache, must-revalidate, private, no-transform"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Pragma: no-cache"); // HTTP/1.0
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
?><?php echo 'Title: '; if(isset($layer_info['var_title'])) { echo $layer_info['var_title']; } ?>