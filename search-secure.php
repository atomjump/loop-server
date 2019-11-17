<?php 

 //Internal frame that appears within the popup
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
	
	
	
	
	
	if((isset($_REQUEST['cssBootstrap']))&&($_REQUEST['cssBootstrap'] != '')) {
	    
	    if(substr($_REQUEST['cssBootstrap'], 0, 4) == "http") {
	        //An absolute url
	        $cssBootstrap = $_REQUEST['cssBootstrap'];
	    } else {
	        //A relative one
	        $cssBootstrap = urldir($_REQUEST['cssBootstrap'], $clientremoteurl);
	    }
	    //TODO: Possible improvement here - check for https/http of server = https/http of css files
	} else {
	    $cssBootstrap = "https://atomjump.com/css/bootstrap.min.css";
	}
			
    if((isset($_REQUEST['cssFeedback']))&&($_REQUEST['cssFeedback'] != '')) {
	    if(substr($_REQUEST['cssFeedback'], 0, 4) == "http") {
	         //An absolute url
	        $cssFeedback = $_REQUEST['cssFeedback'];
	    } else {
	        //A relative one
	        $cssFeedback = urldir($_REQUEST['cssFeedback'], $clientremoteurl);
	    }    
	        
	        
	} else {
		if($staging == true) {
			$cssFeedback = "https://staging.atomjump.com/css/comments-0.1.css";
		} else {
	    	$cssFeedback = "https://atomjump.com/css/comments-0.1.css";
	    }
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

	if(($layer_info)&&($_SESSION['logged-user'] != "")) {
	    				
		//Only the owners can do this
		$isowner = $lg->is_owner($_SESSION['logged-user'], $layer_info['int_group_id'], $layer_info['int_layer_id']);
		if($isowner == true) {	
			//Subscribed already. Show an unsubscribe link
			$subscribe_text = "unsubscribe";
			if($msg['msgs'][$lang]['unsubscribe']) $subscribe_text = $msg['msgs'][$lang]['unsubscribe'];
			$subscribe = "<a href=\"javascript:\" onclick=\"return unSub(" . $_SESSION['logged-user'] . ",'" .$_REQUEST['uniqueFeedbackId'] . "');\" title=\"" . $msg['msgs'][$lang]['yourEmailReason'] . "\">" . $subscribe_text . "</a>";

		}
		
		
	}
	

	
	
	//Ensure no caching
	header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Pragma: no-cache"); // HTTP/1.0
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
?><!DOCTYPE html>
<html lang="en">
  <head>
  	    <meta charset="utf-8">
		 <title>AtomJump Loop - messaging for your site</title>
		 
		 <meta name="description" content="<?php echo $msg['msgs'][$lang]['description'] ?>">
		 
		 <meta name="keywords" content="<?php echo $msg['msgs'][$lang]['keywords'] ?>">
		 
			  <!-- Bootstrap core CSS -->
			<link rel="StyleSheet" href="<?php echo $cssBootstrap ?>" rel="stylesheet">
			
			<!-- AtomJump Feedback CSS -->
			<link rel="StyleSheet" href="<?php echo $cssFeedback ?>">
			
			<!-- Bootstrap HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
			<![endif]-->
			
			<!-- Include your version of jQuery here.  This is version 1.9.1 which is tested with AtomJump Feedback. -->
			<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script> 
			<!-- Took from here 15 May 2014: http://ajax.googleapis.com/ajax/libs/jquery/1.9.1 -->
			
			
			<script>
				var initPort = "";
				
				<?php if(isset($cnf['readPort'])) { //Support a specific port for fast reading with the loop-server-fast plugin. 'readURL' will be used instead if it is set, which gives more control.
				?>
					var readPort = "<?php echo $cnf['readPort'] ?>";
				<?php } else { ?>
					var readPort = null;
				<?php } ?>
				
				
				<?php if(isset($cnf['readURL'])) { //Support a specific URL for fast reading with the loop-server-fast plugin 
				?>
					var readURL = "<?php echo trim_trailing_slash($cnf['readURL']) ?>";
				<?php } else { ?>
					var readURL = null;
				<?php } ?>
				
				
				
				var portReset = true;
			
				var ajFeedback = {
					"uniqueFeedbackId" : "<?php echo $_REQUEST['uniqueFeedbackId'] ?>",
					"myMachineUser" : "<?php echo $_REQUEST['myMachineUser'] ?>",
					"server" : "<?php echo trim_trailing_slash($_REQUEST['server']) ?>"
				}
				
				
				var port = "";
				
				<?php if($granted == true) { ?>
				   var granted = true;
				<?php } else { ?>
				   var granted = false;
				<?php } ?>
				
				var sendPublic = true;
				var sendPrivatelyMsg = '<?php echo $msg['msgs'][$lang]['sendPrivatelyButton'] ?>';
				var sendPubliclyMsg = '<?php echo $msg['msgs'][$lang]['sendButton'] ?>';
				var goPrivateMsg = '<?php echo $msg['msgs'][$lang]['sendSwitchToPrivate'] ?>';
				var goPublicMsg = '<?php echo $msg['msgs'][$lang]['sendSwitchToPublic'] ?>';
				

				
			</script>
			<script type="text/javascript" src="<?php echo $root_server_url ?>/js/chat-inner-1.3.3.js"></script> 
			
			
	</head>
	<body class="comment-popup-body">
		 <div id="comment-popup-content" class="comment-inner-style" style="width: <?php echo $_REQUEST['width'] ?>px; height: <?php echo $_REQUEST['height'] ?>px;">
			<div style="clear: both;"></div>
			
			
			
			<script>
				
			
				var startedFullScreen = false;
				var pfx = ["webkit", "moz", "ms", "o", ""];
				function RunPrefixMethod(obj, method) {

					var p = 0, m, t;
					while (p < pfx.length && !obj[m]) {
						m = method;
						if (pfx[p] == "") {
							m = m.substr(0,1).toLowerCase() + m.substr(1);
						}
						m = pfx[p] + m;
						t = typeof obj[m];
						if (t != "undefined") {
							pfx = [pfx[p]];
							return (t == "function" ? obj[m]() : obj[m]);
						}
						p++;
					}

				}
				
				
				function switchPublic()
				{
					//Reset any
					shortCode = "";
            		publicTo = "";
				
					if(sendPublic == true) {
						//Switch to private
						sendPublic = false;
						
						//Hide the public button
						$('#public-button').hide();
						$('#private-button').show();
						$('#private-button').html(sendPrivatelyMsg);
						
						//Show the public option on the link
						$('#private-public-link').html(goPublicMsg);
					} else {
						
						//Switch to public
						sendPublic = true;
						
						//Hide the private button
						$('#public-button').show();
						$('#private-button').hide();
						$('#public-button').html(sendPubliclyMsg);
						
						//Show the private option on the link
						$('#private-public-link').html(goPrivateMsg);
					
					}
				
					return false;
				}
				
			</script>
			
			
			
			
			<div id="comment-chat-form" class="container" >
				   <form id="comment-input-frm" class="form form-inline" role="form" action="" onsubmit="return mg.commitMsg(sendPublic);"  autocomplete="off" method="GET">
							<input type="hidden" name="action" value="ssshout">
							<input type="hidden" id="lat" name="lat" value="">
							<input type="hidden" id="lon" name="lon" value="">
							<input type="hidden" id="whisper_to" name="whisper_to" value="">
							<input type="hidden" id="whisper_site" name="whisper_site" value="">
							<input type="hidden" id="name-pass" name="your_name" value="<?php echo urldecode($_COOKIE['your_name']); ?>">
							<input type="hidden" name="passcode" id="passcode-hidden" value="<?php echo $_REQUEST['uniqueFeedbackId'] ?>">
							<input type="hidden" id="reading" name="reading" value="">
							<input type="hidden" name="remoteapp" value="true">
							<input type="hidden" id="clientremoteurl" name="clientremoteurl" value="<?php echo $_REQUEST['clientremoteurl'] ?>">
							<input type="hidden" id="remoteurl" name="remoteurl" value="">
							<input type="hidden" id="units" name="units" value="mi">
							<input type="hidden" id="short-code" name="short_code" value="">
							<input type="hidden" id="public-to" name="public_to" value="">
						    <input type="hidden" id="volume" name="volume" value="1.00">
							<input type="hidden" id="ses" name="ses" value="<?php if(isset($_COOKIE['ses'])) { echo $_COOKIE['ses']; } else { echo session_id(); } ?>">
							<input type="hidden" name="cs" value="21633478">
							<input type="hidden" id="typing-now" name="typing" value="off">
							<input type="hidden" id="shout-id" name="shout_id" value="">
					  		<input type="hidden" id="msg-id" name="msg_id" value="">
					   		<input type="hidden" id="message" name="message" value="">
							<input type="hidden" id="email" name="email" value="<?php if(isset($_COOKIE['email'])) { echo urldecode($_COOKIE['email']); } else { echo ''; } ?>">
							<input type="hidden" id="phone" name="phone" value="<?php if(isset($_COOKIE['phone'])) { echo urldecode($_COOKIE['phone']); } else { echo ''; } ?>">
							
							<?php if($granted == true) { ?>
								<div class="form-group col-xs-12 col-sm-12 col-md-7 col-lg-8">
								  <div class="">
										<textarea id="shouted" name="shouted" class="form-control" maxlength="510" placeholder="<?php echo $msg['msgs'][$lang]['enterComment'] ?>" autocomplete="off"></textarea>
								  </div>
								</div>
								<div class="form-group col-xs-12 col-sm-12 col-md-5 col-lg-4">
									<button type="submit" id="private-button"  class="btn btn-info" style="margin-bottom:3px; display: none;"><?php echo $msg['msgs'][$lang]['sendPrivatelyButton'] ?></button>
									<button type="submit" id="public-button" class="btn btn-primary" style="margin-bottom:3px;"><?php echo $msg['msgs'][$lang]['sendButton'] ?></button>
									<a href="javscript:" style="white-space: nowrap; margin-left:3px;" onclick="return switchPublic();" id="private-public-link"><?php echo $msg['msgs'][$lang]['sendSwitchToPrivate'] ?></a>
									<a target="_blank" href="<?php if($cnf['video']['url']) {	
	echo str_replace("[FORUM]", $_REQUEST['uniqueFeedbackId'], $cnf['video']['url']);
} else {
	echo "https://meet.jit.si/aj-changeme-" . $_REQUEST['uniqueFeedbackId']; 
} ?>" onclick="event.stopPropagation(); return true;" style="margin-bottom:3px;"><img id="video-button" src="<?php echo $root_server_url ?>/images/video.svg" title="Video Chat" style="width: 48px; height: 32px;"></a><a target="_blank" href="subscribe.php" onclick="event.stopPropagation(); return true;" style="margin-bottom:3px;"><img id="subscribe-button" src="<?php echo $root_server_url ?>/images/video.svg" title="Subscribe" style="width: 48px; height: 32px;"></a>
								</div>
							
							<?php } else { //No access so far - need to log in with the forum password ?>
								<div class="form-group col-xs-12 col-sm-12 col-md-7 col-lg-8">
								  <div class="">
									<input id="forumpass" name="forumpass" type="password" class="form-control" maxlength="510" placeholder="<?php echo $msg['msgs'][$lang]['enterForumPass'] ?>" autocomplete="off"> 
								  </div>
								</div>
								<div class="form-group col-xs-12 col-sm-12 col-md-5 col-lg-4">
									<button onclick="return set_options_cookie();" class="btn btn-primary" style="margin-bottom:3px;"><?php echo $msg['msgs'][$lang]['enterForumPassButton'] ?></button>
								</div>
							<?php } ?>
					</form>
			</div>
			<div id="warnings" class="alert alert-warning" role="alert" style="display: none;"></div>
			<div id="comment-prev-messages">
			</div>
			<div style="display: none; color: #800000;" id="forum-logged-in">
			</div>

		</div>
		<div id="comment-options" class="comment-frm-scroller" style="width: <?php echo $_REQUEST['width'] ?>px; height: <?php echo $_REQUEST['height'] ?>px;">
				<h4><?php echo $msg['msgs'][$lang]['commentSettings'] ?></h4>
				
				<div id="logged-status">
					<div style="float: right;" id="comment-logout" <?php if($_SESSION['logged-user']) { ?>style="display: block;"<?php } else { ?>style="display: none;"<?php } ?>>
					
						
						<a id="comment-logout-text" href="javascript:" onclick="beforeLogout(function() { 
							         $.get( '<?php echo $root_server_url ?>/logout.php', function( data ) { logout();  refreshLoginStatus(); } );  });" <?php if(urldecode($_COOKIE['email']) == $_SESSION['logged-email']) { ?>style="display: block;"<?php } else { ?>style="display: none;"<?php } ?>><?php echo $msg['msgs'][$lang]['logoutLink'] ?></a>
						
						<span id="comment-not-signed-in" <?php if(urldecode($_COOKIE['email']) == $_SESSION['logged-email']) { ?>style="display: none;"<?php } else { ?>style="display: block;"<?php } ?>><?php echo $msg['msgs'][$lang]['notSignedIn'] ?></span>
					</div>
				</div>
					
				 <form id="options-frm" class="form" role="form" action="" onsubmit="return set_options_cookie();"  method="POST">
				 				 <input type="hidden" name="passcode" id="passcode-options-hidden" value="<?php echo $_REQUEST['uniqueFeedbackId'] ?>">
				 				 <div class="form-group">
				 						<div><?php echo $msg['msgs'][$lang]['yourName'] ?></div>
							 			<input id="your-name-opt" name="your-name-opt" type="text" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['enterYourName'] ?>" autocomplete="false" value="<?php if(isset($_COOKIE['your_name'])) { echo urldecode($_COOKIE['your_name']); } else { echo ''; } ?>" >
								</div>
								 <div class="form-group">
		 									<div><?php echo $msg['msgs'][$lang]['yourEmail'] ?> <a href="javascript:" onclick="$('#email-explain').slideToggle();" title="<?php echo $msg['msgs'][$lang]['yourEmailReason'] ?>"><?php echo $msg['msgs'][$lang]['optional'] ?></a><span id="subscribe-button">, <?php echo $subscribe; ?></a></span> <span id="email-explain" style="display: none;  color: #f88374;"><?php echo $msg['msgs'][$lang]['yourEmailReason'] ?></span></div>
						  					<input oninput="if(this.value.length > 0) { $('#save-button').html('<?php if($msg['msgs'][$lang]['subscribeSettingsButton']) {
		 echo $msg['msgs'][$lang]['subscribeSettingsButton']; 
		} else { 
			echo $msg['msgs'][$lang]['saveSettingsButton'];
		} ?>'); } else { $('#save-button').html('<?php echo $msg['msgs'][$lang]['saveSettingsButton'] ?>'); }" id="email-opt" name="email-opt" type="email" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['enterEmail'] ?>" autocomplete="false" value="<?php if(isset($_COOKIE['email'])) { echo urldecode($_COOKIE['email']); } else { echo ''; } ?>">
								</div>
								<div><a id="comment-show-password" href="javascript:"><?php echo $msg['msgs'][$lang]['more'] ?></a></div>
								<div id="comment-password-vis" style="display: none;">
									<div  class="form-group">
										<div><?php echo $msg['msgs'][$lang]['yourPassword'] ?> <a href="javascript:" onclick="$('#password-explain').slideToggle();" title="<?php echo $msg['msgs'][$lang]['yourPasswordReason'] ?>"><?php echo $msg['msgs'][$lang]['optional'] ?></a>, <a id='clear-password' href="javascript:" onclick="return clearPass();"><?php echo $msg['msgs'][$lang]['resetPasswordLink'] ?></a> <span id="password-explain" style="display: none; color: #f88374;"><?php echo $msg['msgs'][$lang]['yourPasswordReason'] ?> </span></div>
						  				<input oninput="if(this.value.length > 0) { $('#save-button').html('<?php if($msg['msgs'][$lang]['loginSettingsButton']) {
		 echo $msg['msgs'][$lang]['loginSettingsButton']; 
		} else { 
			echo $msg['msgs'][$lang]['saveSettingsButton'];
		} ?>'); } else { $('#save-button').html('<?php echo $msg['msgs'][$lang]['saveSettingsButton'] ?>'); }" id="password-opt" name="pd" type="password" class="form-control" autocomplete="false" placeholder="<?php echo $msg['msgs'][$lang]['enterPassword'] ?>" value="<?php if(isset($_REQUEST['pd'])) { echo $_REQUEST['pd']; } ?>">
									</div>
									<div  class="form-group">
										<?php global $cnf; if($cnf['sms']['use'] != 'none') { ?>
										<div><?php echo $msg['msgs'][$lang]['yourMobile'] ?> <a href="javascript:" onclick="$('#mobile-explain').slideToggle();" title="<?php echo $msg['msgs'][$lang]['yourMobileReason'] ?>"><?php echo $msg['msgs'][$lang]['yourMobileLink'] ?></a>  <span id="mobile-explain" style="display: none;  color: #f88374;"><?php echo $msg['msgs'][$lang]['yourMobileReason'] ?></span></div>
										 <input  id="phone-opt" name="ph" type="text" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['enterMobile'] ?>" autocomplete="false" value="<?php if(isset($_COOKIE['phone'])) { echo urldecode($_COOKIE['phone']); } else { echo ''; } ?>">
										 <?php } else { ?>
										 <input  id="phone-opt" name="ph" type="hidden" placeholder="<?php echo $msg['msgs'][$lang]['enterMobile'] ?>" value="<?php if(isset($_COOKIE['phone'])) { echo urldecode($_COOKIE['phone']); } else { echo ''; } ?>">
										 <?php } ?>
									</div>
									<?php $sh->call_plugins_settings(null); //User added plugins here ?>									
									<div style="float: right;">
						  					<a id="comment-user-code" href="javascript:"><?php echo $msg['msgs'][$lang]['advancedLink'] ?></a>
						  			</div>
						  			
									<?php global $cnf;
										 $admin_user_id = explode(":", $cnf['adminMachineUser']);
						
										 if(($_SESSION['logged-user'])&&($_SESSION['logged-user'] == $admin_user_id[1])) {
										 	//Show a set forum password option, and group users form ?>
										
										 <div id="group-users-form" class="form-group" style="display:none;">
											<div><?php echo $msg['msgs'][$lang]['privateOwners'] ?> <a href="javascript:" onclick="$('#users-explain').slideToggle();" title="<?php echo $msg['msgs'][$lang]['privateOwnersReason'] ?>"><?php echo $msg['msgs'][$lang]['optional'] ?></a>  <span id="users-explain" style="display: none;  color: #f88374;"><?php echo $msg['msgs'][$lang]['privateOwnersReasonExtended'] ?></span></div>
											 <input  id="group-users" name="users" type="text" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['privateOwnersEnter'] ?>" value="">
											  <div style="padding-top:10px;"><span style="color: red;" id="group-user-count"></span> <?php echo $msg['msgs'][$lang]['subscribers'] ?></div>
										</div>
										<div id="subscribers-limit-form" class="form-group" style="display:none;">
											<div><?php echo $msg['msgs'][$lang]['limitSubscribers'] ?> <a href="javascript:" onclick="$('#subscribers-limit-explain').slideToggle();" title="<?php echo $msg['msgs'][$lang]['limitSubscribersReason'] ?>"><?php echo $msg['msgs'][$lang]['optional'] ?></a>  <span id="subscribers-limit-explain" style="display: none;  color: #f88374;"><?php echo $msg['msgs'][$lang]['limitSubscribersReasonExtended'] ?></span></div>
											 <input  id="subscribers-limit" name="subscriberlimit" type="text" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['limitSubscribersEnter'] ?>" value="<?php if(($layer_info['var_subscribers_limit']) && ($layer_info['var_subscribers_limit'] != "")) { echo $layer_info['var_subscribers_limit']; } ?>">
										</div>
										<div id="set-forum-password-form" class="form-group" style="display:none;">
											<div><?php echo $msg['msgs'][$lang]['setForumPass'] ?> <a href="javascript:" onclick="$('#forum-password-explain').slideToggle();" title="<?php echo $msg['msgs'][$lang]['setForumPassReason'] ?>"><?php echo $msg['msgs'][$lang]['optional'] ?></a>  <span id="forum-password-explain" style="display: none;  color: #f88374;"><?php echo $msg['msgs'][$lang]['setForumPassReasonExtended'] ?></span></div>
											 <input  id="set-forum-password" name="setforumpassword" type="password" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['setForumPassEnter'] ?>" value="">
											 <input type="hidden" id="forumpasscheck" name="forumpasscheck" value="">
										</div>
									<?php } else { ?>
							
										<div id="group-users-form" class="form-group" style="display:none;">
											<div style="padding-top:10px;"><span style="color: red;" id="group-user-count"></span> <?php echo $msg['msgs'][$lang]['subscribers'] ?></div>
											<input  id="group-users" name="users" type="hidden" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['privateOwnersEnter'] ?>" value="">
										</div>
									 	<input  id="set-forum-password" name="setforumpassword" type="hidden" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['setForumPassEnter'] ?>" value="">
									 	<input type="hidden" id="forumpasscheck" name="forumpasscheck" value="">
									 	<input type="hidden" id="subscribers-limit" name="subscriberlimit"  class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['limitSubscribersEnter'] ?>" value="">
										
									<?php } ?>
									<div id="user-id-show" class="form-group" style="display:none;">
										<div style="color: red;" id="user-id-show-set"></div>
						  			</div>
									
								</div>
								<div class="form-group">
		 									<div style="display: none; color: red;" id="comment-messages"></div>
								</div>
								<br/>
							 <button id="save-button" type="submit" class="btn btn-primary" style="margin-bottom:3px;"><?php echo $msg['msgs'][$lang]['saveSettingsButton'] ?></button>
							<br/>
							<br/>
							 <div><?php echo $msg['msgs'][$lang]['tip'] ?></div>
							 <br/>
							 <div><?php echo $msg['msgs'][$lang]['getYourOwn'] ?></div>
							 
				 </form>
		</div>
		<div id="comment-upload" class="comment-frm-scroller" style="width: <?php echo $_REQUEST['width'] ?>px; height: <?php echo $_REQUEST['height'] ?>px;">
				<?php global $cnf; if($cnf['uploads']['use'] != 'none') { ?>
				<h4><?php echo $msg['msgs'][$lang]['uploadTitle'] ?></h4>
				<?php } ?>
				
					
				 <form id="upload-frm" class="form" role="form" action="" onsubmit="return upload();"  method="POST">
				 		<?php global $cnf; if($cnf['uploads']['use'] != 'none') { ?>	
				 				 <div class="form-group">
				 						<div><?php echo $msg['msgs'][$lang]['selectImage'] ?></div>
							 			<input id="image" name="fileToUpload" type="file" accept=".jpg,.jpeg," class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['selectImagePrompt'] ?>" >
								</div>
								<div id="uploading-wait" style="display: none; margin-bottom: 10px;"><?php echo $msg['msgs'][$lang]['uploadingWait'] ?> <img src="images/ajax-loader.gif"></div>
								<div id="uploading-msg" style="display: none; color: #900; margin-bottom: 10px;"></div>
								
											 <button type="submit" class="btn btn-primary" style="margin-bottom:3px;" name="submit"><?php echo $msg['msgs'][$lang]['uploadButton'] ?></button>
						  	 <br/>
							 <br/>
							 
							 <div><?php echo $msg['msgs'][$lang]['uploadLimits'] ?></div>
							 <br/>
					   <?php } ?>			 
								 	<h4><?php echo $msg['msgs'][$lang]['downloadTitle'] ?></h4>
								  <div><?php echo $msg['msgs'][$lang]['downloadDescription'] ?> 	</div>
								  <br/>
						    <div><a href="download.php?format=excel&uniqueFeedbackId=<?php echo $_REQUEST['uniqueFeedbackId'] ?>" class="btn btn-primary" role="button"><?php echo $msg['msgs'][$lang]['downloadButton'] ?></a></div>
							 	 <br/>
							 <br/>
					
							 <div><?php echo $msg['msgs'][$lang]['getYourOwn'] ?></div>
							 <?php $sh->call_plugins_upload(null); //User added plugins here ?>
				 </form>
		</div>
		
		<div id="comment-emojis" class="comment-frm-scroller" style="z-index: 11000; width: <?php echo $_REQUEST['width'] ?>px; height: <?php echo $_REQUEST['height'] ?>px;">
				<div style="z-index: 5000">	
				 <?php $sh->call_plugins_emojis(null); //User added plugins here ?>
				</div>
		</div>
		
		<div id="comment-single-msg" class="comment-frm-scroller" style="z-index: 11000; width: <?php echo $_REQUEST['width'] ?>px; height: <?php echo $_REQUEST['height'] ?>px;">
				<h2>Single Message</h2>
		</div>
		
		
		
		
	
		<script>
		
			function ieVersion() {
			  var uaString = window.navigator.userAgent;
			  uaString = uaString || navigator.userAgent;
			  var match = /\b(MSIE |Trident.*?rv:|Edge\/)(\d+)/.exec(uaString);
			  if (match) return parseInt(match[2])
			}
		
		
			function clearPass()
			{
				var ur = "clear-pass.php";
				
				var email = $('#email-opt').val();
				if(email != '') {
					ur = ur + '?email=' + email;
					
					//Also save this cookie
					document.cookie = 'email=' + encodeURIComponent(email) + '; path=/; expires=' + cookieOffset() + ';';
				}
				
			
				 $.get(ur, function(response) { 
				  		 
					   $('#clear-password').html(response);
					   
				 });
				  
				 return false;
		 	 }
		 
		 	  function unSub(uid, uniqueFeedbackId)
		 	  {
		 	  	ur = "confirm.php?uid=" + uid + "&unsub=" + uniqueFeedbackId;
		 		$.get(ur, function(response) { 
				  	 if(response.includes("FAILURE") === true) {
				  	 	$('#email-explain').html("<?php echo $msg['msgs'][$lang]['problemUnsubscribing'] ?>");
					    $('#email-explain').show();
				  	 } else { 
				  		
					   $('#group-users').html(response);
					   $('#email-explain').html("Successfully unsubscribed.");
					   $('#email-explain').show();
					   $('#subscribe-button').hide();
					 }
					   
				 });
		 	  }
		 	  
		 	   function sub(uid, uniqueFeedbackId)
		 	  {
		 	  	ur = "confirm.php?uid=" + uid + "&sub=" + uniqueFeedbackId + "&fp=" + $("#set-forum-password").val();
		 		$.get(ur, function(response) { 
				  	 if(response.includes("FAILURE") === true) {
				  	 	$('#email-explain').html("Sorry there was a problem subscribing. Please try again.");
					    $('#email-explain').show();
				  	 } else { 
				  		
					   $('#group-users').html(response);
					   $('#email-explain').html("Successfully subscribed.");
					   $('#email-explain').show();
					   $('#subscribe-button').hide();
					 } 
					   
				 });
		 	  }
		
				function vidDeactivate()
				{
					$('#video-button').attr("src", "<?php echo $root_server_url ?>/images/no-video.svg");
					$('#video-button').attr("title","<?php echo $msg['msgs'][$lang]['videoSupportedPlatforms'] ?>");
					$('#video-button').parent().attr("onclick", "return false;");
				}
				
				
				
					
			
				
			
			
				$(document).ready(function(){
					
					
					if(ieVersion() <= 11) {
						//In other words all versions of IE, but not Edge, which is 12+
						vidDeactivate();
					}
					
					
					
				});
				
				
				$(window).on('load', function() {
					//After fully loaded page
				 	refreshLoginStatus();	//Refresh the logged in status
				});
			
		</script>
		
	</body>
</html>
