<!DOCTYPE html>
<html lang="en" id="fullscreen">
  <head>
  	    <meta charset="utf-8">
		 <meta name="viewport" content="width=device-width, user-scalable=no">
		 <title>AtomJump Messaging Server - provided by AtomJump</title>

		 <meta name="description" content="<?php echo $msg['msgs'][$lang]['description'] ?>">

		 <meta name="keywords" content="<?php echo $msg['msgs'][$lang]['keywords'] ?>">


			
			  <!-- Bootstrap core CSS -->
			<link rel="StyleSheet" href="front-end/css/bootstrap.min.css" rel="stylesheet">

			<!-- AtomJump Feedback CSS -->
			<link rel="StyleSheet" href="front-end/css/comments-1.0.4.css?ver=1">
			
			

			<!-- Bootstrap HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
			  <style>
			  

				.looplogo {
					background: url(images/logo640.png)  no-repeat;
					position: relative;
					width: 640px;
					height:640px;
					margin-left: auto;
					margin-right: auto;
				}
			  </style>
			<![endif]-->

			<!-- Include your version of jQuery here.  This is version 1.9.1 which is tested with AtomJump Feedback. -->
			<script type="text/javascript" src="front-end/js/jquery-1.11.0.min.js"></script>
			<!-- Took from here 15 May 2014: http://ajax.googleapis.com/ajax/libs/jquery/1.9.1 -->

			<!-- For the dropdown autocomplete -->
			<link rel="stylesheet" href="front-end/css/jquery-ui.css">
			<script src="front-end/js/jquery-ui.js"></script>


			<script>
				/*
					//Add your configuration here for AtomJump Feedback
					var ajFeedback = {
						"uniqueFeedbackId" : "Setup",	//Anything globally unique to your company/page, starting with 'apix-'	
						"myMachineUser" : "<?php echo $cnf['adminMachineUser']; ?>",			
						"server":  "<?php echo $webroot; ?>",
						"cssFeedback": "css/comments-1.0.4.css?ver=1",
						"cssBootstrap": "css/bootstrap.min.css"
					}
				*/
			</script>
			
			<?php if($screen_type == "signup") { ?>
				<script type="text/javascript" src="<?php echo $inner_js ?>"></script>
			<?php } else { //Warning - these will conflict if on the same screen ?>
				<script type="text/javascript" src="front-end/js/chat-1.0.9.js"></script>
				<!--No svg support -->
				<!--[if lt IE 9]>
				  <script src="https://frontcdn.atomjump.com/atomjump-frontend/chat-1.0.7.js"></script>
				<![endif]-->
			<?php } ?>
			 


			<style>
			
				
			
				h2 {
					text-align: center;
				}
				
				.signuptitle {
					font-size: 24px;
					font-family: inherit;
					font-weight: 500;
					line-height: 1.1;
					color: inherit;
				} 
				
				.signuptitle-section {
					margin-top: 20px;
					margin-bottom: 30px;
				}

				textarea:focus, input:focus, img:focus {
					outline: 0;
				}
		
				.looplogo {
					position: relative;
					width: 600px;
					height:600px;
					margin-left: auto;
					margin-right: auto;
					z-index: 10;
				}


				.overimage {
					position: relative;
					top: 0px;
					z-index: 1;
				}


        		.darkoverlay {
        			position: relative;
        			top: 100px;
        			width: 100%;
        			background-color: black;
        			opacity: 0.5;
    				filter: alpha(opacity=50); /* For IE8 and earlier */
    				z-index: 1;
        		}
        		
        		.infront {
        			z-index: 100;
        			position: relative;	/* Trying this */
        		}


			   .share {
					position: fixed;
					top: 10px;
					float: left;
					margin-left: 20px;
					z-index: 20;
				}

				.cpy {
						position: relative;
						right: 10px;
						bottom: 10px;
						float: right;
						margin-right: 20px;
				}

			
				/*.cpy a:link, a:visited {
					color: #888;
				}*/



				/* iphone and other phones */
				@media screen and (max-width: 480px) {

					.looplogo {
						width: 320px;
						height:320px;


					}

					.looplogo:hover {
						height: 320px;
						width: 320px;
					}

					.subs {

						position: relative;
						margin-top: 10px;
						float: left;
						margin-left: 20px;
						z-index: 0;
					}

					.cpy {
						position: relative;
						margin-top: 10px;
						margin-right: 20px;
					}

				
				}


				/* ipad */
				@media screen and (max-device-width: 1024px) and (min-device-width: 768px) {

					.cpy {
						position: relative;
						right: 10px;
						bottom: 10px;
						float: right;
						margin-right: 20px;
						z-index: 20;

					}

	
				}

				/* Samsung S4 */
				@media screen and (-webkit-min-device-pixel-ratio: 3.0) and (max-width: 1080px) {
					.looplogo {
						width: 320px;
						height:320px;


					}

					.looplogo:hover {
						height: 320px;
						width: 320px;
					}

					.subs {

						position: fixed;
						bottom: 10px;
						float: left;
						margin-left: 20px;
						z-index: 0;
					}

					.cpy {
						position: relative;
						right: 10px;
						bottom: 10px;
						float: right;
						margin-right: 20px;
					}


					
				}

				#bg {
					position:relative;
					top:0;
					left:0;
					width:100%;
					height:100%;
					z-index: -1;
				}
				
				
				





			</style>


			<script>
    		var ie8 = false;
			</script>



			<!--[if IE 8]>
				<script>
					ie8 = true;
					document.getElementById('sumo').src = "";	//blank out this on IE8
				</script>
			<![endif]-->

	</head>

	<body>


		<script>
				var granted = false;
				
				var sendPublic = true;
				var sendPrivatelyMsg = '<?php echo $msg['msgs'][$lang]['sendPrivatelyButton'] ?>';
				var sendPubliclyMsg = '<?php echo $msg['msgs'][$lang]['sendButton'] ?>';
				var goPrivateMsg = '<?php echo $msg['msgs'][$lang]['sendSwitchToPrivate'] ?>';
				var goPublicMsg = '<?php echo $msg['msgs'][$lang]['sendSwitchToPublic'] ?>';

				//Overwrite the default message slightly
				
				lsmsg.msgs.en.loggedIn = "Logged in.";		//original is 'Logged in. Please wait..'
				lsmsg.msgs.es.loggedIn = "Conectado.";
				lsmsg.msgs.pt.loggedIn = "Iniciado.";
				lsmsg.msgs.ch.loggedIn = "已登录。";
				lsmsg.msgs.de.loggedIn = "Eingeloggt.";
				lsmsg.msgs.fr.loggedIn = "Connecté.";
				lsmsg.msgs.hi.loggedIn = "में लॉग इन";
				lsmsg.msgs.ru.loggedIn = "Выполнен вход.";
				lsmsg.msgs.jp.loggedIn = "ログインしました。";
				lsmsg.msgs.bg.loggedIn = "লগ ইন";
				lsmsg.msgs.ko.loggedIn = "로그인되었습니다.";
				lsmsg.msgs.pu.loggedIn = "ਲੌਗ ਇਨ ਹੋਇਆ.";
				lsmsg.msgs.it.loggedIn = "Accesso effettuato.";
				lsmsg.msgs.in.loggedIn = "Sudah masuk.";
				lsmsg.msgs.cht.loggedIn = "已登錄。";
				
				
			
				

				function isChromeDesktop()
				{
					var ua = navigator.userAgent;
					if ((/Chrome/i.test(ua))||(/Safari/i.test(ua))) {
						//Is Chrome, now return false if mobile version - actually Android we still want this option on
						if (/webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile|mobile/i.test(ua)) {
							return false;
						}
						return true;

					} else {
						return false;
					}
				}
					
					
				function getParentUrl() {
					var isInIframe = (parent !== window),
						parentUrl = null;

					if (isInIframe) {
						parentUrl = document.referrer;
					}

					return parentUrl;
				}
				
				
				function clearPass()
				{
					var ur = "<?php echo $webroot; ?>/clear-pass.php";
				
					var email = $('#email-opt').val();
					if(email != '') {
						ur = ur + '?email=' + email;
					
						//Also save this cookie
						document.cookie = 'email=' + encodeURIComponent(email) + '; path=/; expires=' + cookieOffset() + ';';
					}
				
			
					$('#clear-password').html("<img src=\"images/ajax-loader.gif\" width=\"16\" height=\"16\">");
					 $.get(ur, function(response) { 
						 
						   $('#clear-password').html(response);
					   
					 });
				  
					 return false;
				 }
				 
				 function refresh() {
				  var hash = null;
				  var url = window.location.origin;
				  var pathname = window.location.pathname;
				  if(window.location.search) {
				  		hash = window.location.search;
				  } else {
				  		if(window.location.hash) {
				  	 		hash = window.location.hash;
				  	 	}
				  }
					
				  if(hash) {
				  	 if(hash[0] == '?') hash = '&' + hash.substring(1);
				 	 window.location = url + pathname + '?application_refresh=' + (Math.random() * 100000) + hash;
				  } else {
				  	window.location.reload(true);
				  }
				}


				$(document).ready(function(){
					$("#change-lang-button").click(function() {
						var newLang = $("[name='lang']").val();
						document.cookie = 'lang=' + newLang  + '; path=/; expires=' + cookieOffset() + ';'; 
						//Works on all platforms except iphones: window.location.reload(true);
						refresh();
					});
					
					
					
					$("#pair-again-button").click(function() {
						//Works on all platforms except iphones: window.location.reload(true);
						refresh();
					});
					
					
					$("#sign-and-pair-button").click(function() {
						
						var allGood = true;
						if($("#email-opt").val() == '') {
							$("#comment-messages").html("<?php echo $msg['msgs'][$lang]['enterEmail'] ?>");		//"Enter your email". Better would be: Please enter an email address.
							allGood = false;
						}
						
						if($("#password-opt").val() == '') {
							$("#comment-messages").html("<?php echo $msg['msgs'][$lang]['enterPassword'] ?>");		//"Enter your password" Please enter a password.
							
							allGood = false;
						}
						
						if(($("#email-opt").val() == '') && ($("#password-opt").val() == '')) {
							$("#comment-messages").html("<?php echo $msg['msgs'][$lang]['enterEmail'] ?>"); //"Enter your email". Better would be: Please enter an email and password.
							allGood = false;
						}
						
						if(allGood == true) {
							$("#comment-messages").html("<img src=\"images/ajax-loader.gif\" width=\"16\" height=\"16\">");
							
						}
						$("#comment-messages").show();
						
						if(allGood == true) {
							var returned = set_options_cookie();
							
							//$("#sign-and-pair-button").hide();
							$("#pair-again-button").fadeIn();
							
							return returned;
						} else {
							return false;
						}
						
					});
				});
		</script>

		
		<?php if($screen_type == "signup") { ?>
     	<div class="container-fluid infront">
			<div class="row justify-content-center">
				<div class="col-md-12">
				
				<div class="">
					<span class="signuptitle-section" style="text-align:left; float: left; width: 50%;">
						<span class="signuptitle"><?php echo $notifications_config['msgs'][$lang]['signUp']; ?></span></br>
						<span><?php echo $notifications_config['msgs'][$lang]['orSignIn']; ?></span>
					</span>
					<span class="signuptitle-section" style="text-align:right; float: right; width: 50%;">
						
							<a href="<?php echo $follow_on_link; ?>"><img src="front-end/img/logo80.png" width="70" height="70"></a>

					</span>
				</div>
				<div style="clear: both;"></div>
				
				<h3 align="center" style="color: #aaa;"><?php echo $main_message ?></h3>
				
				<!-- Signup Form -->
				<form id="options-frm" class="form" role="form" action="" onsubmit=""  method="POST">
				 				 <input type="hidden" name="passcode" id="passcode-options-hidden" value="<?php echo $_REQUEST['uniqueFeedbackId'] ?>">
				 				 <input type="hidden" name="general" id="general-options-hidden" value="<?php echo $_REQUEST['general'] ?>">
				 				 <input type="hidden" name="id" id="pair-id" value="<?php echo $_REQUEST['id'] ?>">
				 				 <input type="hidden" name="devicetype" id="device-type" value="<?php echo $_REQUEST['devicetype'] ?>">
				 				 <input type="hidden" name="date-owner-start" value="<?php echo $date_start ?>">
				 				 <input type="hidden" id="email-modified" name="email_modified" value="false">
				 				 <?php $sh->call_plugins_settings(null); //User added plugins here ?>								
				 				
				 				 <a id="change-lang-button"><img style="margin-top: 10px; margin-bottom: 14px;" src='front-end/img/refresh.png' width='60' height='60'></a> <img src="images/flags.png" width="48" height="14"><br/>
								 <div class="form-group">
		 									<div><?php echo $msg['msgs'][$lang]['yourEmail'] ?></div>
						  					<input oninput="if(this.value.length > 0) { $('#email-modified').val('true'); $('#save-button').html('<?php if($msg['msgs'][$lang]['subscribeSettingsButton']) {
		 echo $msg['msgs'][$lang]['subscribeSettingsButton']; 
		} else { 
			echo $msg['msgs'][$lang]['saveSettingsButton'];
		} ?>'); } else { $('#email-modified').val('false'); $('#save-button').html('<?php echo $msg['msgs'][$lang]['saveSettingsButton'] ?>'); }" id="email-opt" name="email-opt" type="email" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['enterEmail'] ?>" autocomplete="false" value="<?php echo $display_email; ?>">
								</div>
								<!--<div><a id="comment-show-password" href="javascript:"><?php echo $msg['msgs'][$lang]['more'] ?></a></div>-->
								<div id="comment-password-vis" style="">
									<div  class="form-group">
										<div><?php echo $msg['msgs'][$lang]['yourPassword'] ?> <a id='clear-password' href="javascript:" onclick="return clearPass();"><?php echo $msg['msgs'][$lang]['resetPasswordLink'] ?></a> <span id="password-explain" style="display: none; color: #f88374;"><?php echo $msg['msgs'][$lang]['yourPasswordReason'] ?> </span></div>
						  				<input oninput="if(this.value.length > 0) { $('#save-button').html('<?php if($msg['msgs'][$lang]['loginSettingsButton']) {
		 echo $msg['msgs'][$lang]['loginSettingsButton']; 
		} else { 
			echo $msg['msgs'][$lang]['saveSettingsButton'];
		} ?>'); } else { $('#save-button').html('<?php echo $msg['msgs'][$lang]['saveSettingsButton'] ?>'); }" id="password-opt" name="pd" type="password" class="form-control" autocomplete="false" placeholder="<?php echo $msg['msgs'][$lang]['enterPassword'] ?>" value="<?php if(isset($_REQUEST['pd'])) { echo $_REQUEST['pd']; } ?>">
									</div>
									<div  class="form-group">
										 <input  id="phone-opt" name="ph" type="hidden" placeholder="<?php echo $msg['msgs'][$lang]['enterMobile'] ?>" value="<?php if(isset($_COOKIE['phone'])) { echo urldecode($_COOKIE['phone']); } else { echo ''; } ?>">
									</div>
									<div id="user-id-show" class="form-group" style="display:none;">
										<div style="color: red;" id="user-id-show-set"></div>
						  			</div>
									
								</div>
								<div class="form-group">
				 						<div><?php echo $msg['msgs'][$lang]['yourName'] ?> (<?php echo $msg['msgs'][$lang]['optional'] ?>)</div>
							 			<input id="your-name-opt" name="your-name-opt" type="text" class="form-control" placeholder="<?php echo $msg['msgs'][$lang]['enterYourName'] ?>" autocomplete="false" value="<?php if(isset($_COOKIE['your_name'])) { echo urldecode($_COOKIE['your_name']); } else { echo ''; } ?>" >
								</div>
								<div class="form-group">
		 									<div style="display: none; color: red;" id="comment-messages"></div>
								</div>
								<br/>
							 <button id="sign-and-pair-button" type="submit" class="btn btn-primary" style="margin-bottom:3px;"><?php echo $msg['msgs'][$lang]['saveSettingsButton']; ?></button><br/><small><?php echo $msg['msgs'][$lang]['tip']; ?></small><br/><br/>
							 <button style="display: none;" id="pair-again-button"  class="btn btn-primary btn-lg" style="margin-bottom:3px;"><?php echo $first_button_wording ?></button>
							<br/>
							<br/>
							
							 
							 
				 </form>
				
				
				</div> 		
			</div>
		</div>
   		
   		
   		<?php } else { 	
   			//logo-wrapper
   	    ?>
		<div>			
		    <a href="<?php echo $speech_bubble_link ?>">
		    	<div id="logo-wrapper" class="looplogo <?php echo $welcome_popup ?>" >
		    		<img class="saver-hideable" src="img/speech-bubble-start-1.png" id="bg" alt="" border="0">
				</div>
			</a>
		</div>
   		

   		
   		<div class="container-fluid infront">
			<div class="row justify-content-center">
				<div class="col-md-2">
				</div>
				 <div class="col-md-8">
						<h3 align="<?php echo $center ?>" style="color: #aaa;"><?php echo $main_message ?></h3>
				
						<div class="form-row text-center">
    						<div class="col-12">
				
								<a class="btn btn-primary" style="margin: 6px;" href='<?php echo $first_button ?>'><?php echo $first_button_wording ?></a>

								<?php if($second_button) { ?>
									<a class="btn btn-primary" style="margin: 6px;" href='<?php echo $second_button ?>'><?php echo $second_button_wording ?></a>
								<?php } ?>
							</div>
						</div>

			 
				 </div>
				<div class="col-md-2">
				</div>
			</div>
		</div>
		
    	<div class="container-fluid darkoverlay" id="mydarkoverlay">
            <div class="row">
                <div class="col-md-2">
                </div>
                 <div class="col-md-8">
                 </div>
                <div class="col-md-2">
                	<p align="right"><a href="https://atomjump.com/smart.php">Learn More</a></p>
					<p align="right" style="color: #aaa;"><small>&copy; <?php echo date('Y'); ?> <?php echo $msg['msgs'][$lang]['copyright'] ?></small></p>
                </div>
            </div>
        </div>
		<br/><br/><br/><br/>

		<!-- Needed? Seems to be straggling: </div>-->
		<?php } ?>


		<div id="comment-holder"></div><!-- holds the popup comments. Can be anywhere between the <body> tags -->


	</body>

</html>
