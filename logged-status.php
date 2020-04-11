<?php

require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

//AJAX get status of logged in / subscribed to this forum


$lg = new cls_login();
$ly = new cls_layer();
$sh = new cls_ssshout();


$json = array();

	//WARNING: Before changing any of this code: 
	//	  $_SESSION['logged-user']     is the account ID, that may not have the credentials passed yet	
	//    $_SESSION['logged-email']    means a password protected account, which has it's credentials already passed

	
	//Check if we are subscribed.
	$lg = new cls_login();
	//Standard setup
	$subscribe_text = "subscription";
	if($msg['msgs'][$lang]['subscription']) $subscribe_text = $msg['msgs'][$lang]['subscription'];
	$subscribe = "<a href=\"javascript:\" onclick=\"$('#email-explain').slideToggle(); $('#save-button').html('" . $msg['msgs'][$lang]['subscribeSettingsButton'] . "')\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_text . "</a>";
	
	$subscribe_toggle_pic_no_ear = "<img src=\"" . $root_server_url . "/images/no-ear.svg\" title=\"Subscribe\" style=\"width: 32px; height: 32px;\">";
	$subscribe_toggle_pic_ear = "<img src=\"" . $root_server_url . "/images/ear.svg\" title=\"Unsubscribe\" style=\"width: 32px; height: 32px;\">";
	$subscribe_toggle = "<a href=\"javascript:\" onclick=\"return subFront(" . $_SESSION['logged-user'] . ",'" . $_REQUEST['uniqueFeedbackId'] . "');\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_toggle_pic_no_ear . "</a>";


	//Get layer info
	$layer_info = $ly->get_layer_id($_REQUEST['passcode'], null);
	
	if($_SESSION['logged-user']) {
		$logged_user_text = $_SESSION['logged-user'];
	} else {
		$logged_user_text = "''";
	}
	

	if($_SESSION['logged-email']) {
		//We are logged in, but not a forum owner
		//Not subscribed.
		
		//Don't show a subscribe link if there is a domain limit, and our email address does not match the right domain
		$allow_subscription = false;
		if(isset($layer_info['var_subscribers_limit']) && ($layer_info['var_subscribers_limit'] != "")) {
			$email_components = explode("@", $_SESSION['logged-email']);
			if(($email_components[1]) && ($email_components[1] === $layer_info['var_subscribers_limit'])) {
				//Allow this user to subscribe
				$allow_subscription = true;
			}
		} else {
			//Allow this user to subscribe
			$allow_subscription = true;
		}
		
		if($allow_subscription == true) {
			// Show a subscribe link if we can subscribe
			$subscribe_text = "subscribe";	
			if($msg['msgs'][$lang]['subscribe']) $subscribe_text = $msg['msgs'][$lang]['subscribe'];
			$subscribe = "<a href=\"javascript:\" onclick=\"return sub(" . $logged_user_text . ",'" .$_REQUEST['passcode'] . "');\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_text . "</a>";
			$subscribe_toggle = "<a href=\"javascript:\" onclick=\"return subFront(" . $logged_user_text . ",'" . $_REQUEST['passcode'] . "');\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_toggle_pic_no_ear . "</a>";
			
		} else {
			$subscribe_text = "cannot subscribe"; 
			if($msg['msgs'][$lang]['cannotSubscribe']) $subscribe_text = $msg['msgs'][$lang]['cannotSubscribe'];
			$subscribe = $subscribe_text;
		}
	}

	if(($layer_info) &&($_SESSION['logged-user'] != "")) {
	    				
		//Only the owners can do this
		$isowner = $lg->is_owner($_SESSION['logged-user'], $layer_info['int_group_id'], $layer_info['int_layer_id']);
		if($isowner == true) {	
			//Subscribed already. Show an unsubscribe link
			$subscribe_text = "unsubscribe";	
			if($msg['msgs'][$lang]['unsubscribe']) $subscribe_text = $msg['msgs'][$lang]['unsubscribe'];
			$subscribe = "<a href=\"javascript:\" onclick=\"return unSub(" . $logged_user_text . ",'" .$_REQUEST['passcode'] . "');\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_text . "</a>";
			$subscribe_toggle = "<a href=\"javascript:\" onclick=\"return unSub(" . $logged_user_text . ",'" . $_REQUEST['passcode'] . "');\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_toggle_pic_ear . "</a>";

		}
	}
	
	
	//Now determine logged in state
	$loggedIn = "";
	$showingSignout = false;
	
	
	if($_SESSION['logged-user']) { 
		$loggedIn = "<div style=\"float: right;\" id=\"comment-logout\" style=\"display: block;\">";
		
		if($_SESSION['logged-email'] == '') {
			//At this stage we should check if this user already exists
			$user = $sh->check_user_exists($_SESSION['logged-user']);
			if($user['var_email']) {
				//THIS IS WRONG: $_SESSION['logged-email'] = $user['var_email'];
				$unlogged_email = $user['var_email'];
			}
		}
	} else {
		$loggedIn = "<div style=\"float: right;\" id=\"comment-logout\" style=\"display: none;\">";
	}
	
	//Logout link display
	$loggedIn .= "<a id=\"comment-logout-text\" href=\"javascript:\" onclick=\"beforeLogout(function() { $.get( '" . $root_server_url . "/logout.php', function( data ) { logout();  refreshLoginStatus(); } ); });\" ";
	
	
	//Now check if we are signed in to an authenticated layer - we still want a sign out option here, also.
	$loggedInMsg = "";
	
	if(((urldecode($_COOKIE['email']) == $_SESSION['logged-email'])&&($_SESSION['logged-email'] != ""))||
		((urldecode($_COOKIE['email']) == $unlogged_email)&&($unlogged_email != ""))) {
		$loggedInMsg = "style=\"display: block;\" >";
		$showingSignout = true;
	} else {
		$loggedInMsg = "style=\"display: none;\" >";
		$showingSignout = false;
	}
	
	$layer_info = $ly->get_layer_id($_REQUEST['passcode'], null);
	if($layer_info) {
		if(isset($layer_info['var_public_code'])) {
			if(($_SESSION['access-layer-granted'] === $layer_info['int_layer_id'])||($ly->is_layer_granted($layer_info['int_layer_id']))) {
				
				//Show the sign out option
				$loggedInMsg = "style=\"display: block;\" >";
				$showingSignout = true;			
			}
		}
	}
	
	$loggedIn .= $loggedInMsg;
	
	
	//'Not signed in' display
	$loggedIn .= $msg['msgs'][$lang]['logoutLink'] . "</a> <span id=\"comment-not-signed-in\" ";
	$loggedInMsg = "";
	if($showingSignout == true) {
		//Switch off
		
		$loggedInMsg = "style=\"display: none;\" >";
	} else {
		//Switch on
		$loggedInMsg = "style=\"display: block;\" >";
	}
	

	
	$loggedIn .= $loggedInMsg;

	
	$loggedIn .= $msg['msgs'][$lang]['notSignedIn'] . "</span>";
				
	/* Original logic from search-secure.php for logged in state (this version is called on a page refresh):
		<a id="comment-logout-text" href="javascript:" onclick="beforeLogout(function() { $('#subscribe-button').hide();
		             $.get( '<?php echo $root_server_url ?>/logout.php', function( data ) { logout(); } );  });" <?php if(urldecode($_COOKIE['email']) == $_SESSION['logged-email']) { ?>style="display: block;"<?php } else { ?>style="display: none;"<?php } ?>><?php echo $msg['msgs'][$lang]['logoutLink'] ?></a>
		
		<span id="comment-not-signed-in" <?php if(urldecode($_COOKIE['email']) == $_SESSION['logged-email']) { ?>style="display: none;"<?php } else { ?>style="display: block;"<?php } ?>><?php echo $msg['msgs'][$lang]['notSignedIn'] ?></span>
	</div>
	*/
	
	

	$json['subscribe'] = $subscribe;
	$json['loggedIn'] = $loggedIn;
	$json['subscribeToggle'] = $subscribe_toggle;

	echo $_GET['callback'] . "(" . json_encode($json) . ")";


?>
