<?php

require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

//AJAX get status of logged in / subscribed to this forum


$lg = new cls_login();
$ly = new cls_layer();


$json = array();

	
	//Check if we are subscribed.
	$lg = new cls_login();
	//Standard setup
	$subscribe_text = "subscription";
	if($msg['msgs'][$lang]['subscription']) $subscribe_text = $msg['msgs'][$lang]['subscription'];
	$subscribe = "<a href=\"javascript:\" onclick=\"$('#email-explain').slideToggle(); $('#save-button').html('" . $msg['msgs'][$lang]['subscribeSettingsButton'] . "')\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_text . "</a>";

	if($_SESSION['logged-email']) {
		//We are logged in, but not a forum owner
		//Not subscribed. Show a subscribe link.
		$subscribe_text = "subscribe";
		$subscribe = "<a href=\"javascript:\" onclick=\"return sub(" . $_SESSION['logged-user'] . ",'" .$_REQUEST['uniqueFeedbackId'] . "');\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_text . "</a>";
	}

	if($layer_info) {
	    				
		//Only the owners can do this
		$isowner = $lg->is_owner($_SESSION['logged-user'], $layer_info['int_group_id'], $layer_info['int_layer_id']);
		if($isowner == true) {	
			//Subscribed already. Show an unsubscribe link
			$subscribe_text = "unsubscribe";
			if($msg['msgs'][$lang]['unsubscribe']) $subscribe_text = $msg['msgs'][$lang]['unsubscribe'];
			$subscribe = "<a href=\"javascript:\" onclick=\"return unSub(" . $_SESSION['logged-user'] . ",'" .$_REQUEST['uniqueFeedbackId'] . "');\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_text . "</a>";

		}
	}
	
	
	//Now determine logged in state
	$loggedIn = "";
	
	if($_SESSION['logged-user']) { 
		$loggedIn = "<div style=\"float: right;\" id=\"comment-logout\" style=\"display: block;\">";
	} else {
		$loggedIn = "<div style=\"float: right;\" id=\"comment-logout\" style=\"display: none;\">";
	}
	
	$loggedIn .= "<a id=\"comment-logout-text\" href=\"javascript:\" onclick=\"beforeLogout(function() { $.get( '" . $root_server_url . "/logout.php', function( data ) { logout();  refreshLoginStatus(); } ); });";
	
	if(urldecode($_COOKIE['email']) == $_SESSION['logged-email']) {
		$loggedIn .= "style=\"display: block;\"";
	} else {
		$loggedIn .= "style=\"display: none;\"";
	}
	
	$loggedIn .= $msg['msgs'][$lang]['logoutLink'] . "</a> <span id=\"comment-not-signed-in\" ";
	if(urldecode($_COOKIE['email']) == $_SESSION['logged-email']) {
		$loggedIn .= "style=\"display: block;\"";
	} else {
		$loggedIn .= "style=\"display: none;\"";
	}
	
	$loggedIn .= $msg['msgs'][$lang]['notSignedIn'] . "</span>";
				
	/* Original logic from search-secure.php for logged in state (this version is called on a page refresh):
		<a id="comment-logout-text" href="javascript:" onclick="beforeLogout(function() { $('#subscribe-button').hide();
		             $.get( '<?php echo $root_server_url ?>/logout.php', function( data ) { logout(); } );  });" <?php if(urldecode($_COOKIE['email']) == $_SESSION['logged-email']) { ?>style="display: block;"<?php } else { ?>style="display: none;"<?php } ?>><?php echo $msg['msgs'][$lang]['logoutLink'] ?></a>
		
		<span id="comment-not-signed-in" <?php if(urldecode($_COOKIE['email']) == $_SESSION['logged-email']) { ?>style="display: none;"<?php } else { ?>style="display: block;"<?php } ?>><?php echo $msg['msgs'][$lang]['notSignedIn'] ?></span>
	</div>
	*/
	
	

	$json['subscribe'] = $subscribe;
	$json['loggedIn'] = $loggedIn;

	echo $_GET['callback'] . "(" . json_encode($json) . ")";


?>
