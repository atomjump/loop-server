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



?>
<!DOCTYPE html>
<html lang="en" id="fullscreen">
  <head>
  	    <meta charset="utf-8">
		 <meta name="viewport" content="width=device-width, user-scalable=no">
		 <title>AtomJump Messaging Server - provided by AtomJump</title>

		 <meta name="description" content="<?php echo $msg['msgs'][$lang]['description'] ?>">

		 <meta name="keywords" content="<?php echo $msg['msgs'][$lang]['keywords'] ?>">

			  <!-- Bootstrap core CSS -->
			<link rel="StyleSheet" href="https://atomjump.com/css/bootstrap.min.css" rel="stylesheet">

			<!-- AtomJump Feedback CSS -->
			<link rel="StyleSheet" href="https://atomjump.com/css/comments-0.1.css">

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
			<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
			<!-- Took from here 15 May 2014: http://ajax.googleapis.com/ajax/libs/jquery/1.9.1 -->

			<!-- For the dropdown autocomplete -->
			<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
			<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>

			<style>
				h2 {
					text-align: center;
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
        			position: absolute;
        			top: 800px;
        			width: 100%;
        			background-color: black;
        			opacity: 0.9;
    				filter: alpha(opacity=90); /* For IE8 and earlier */
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


				$(document).ready(function(){

				});
		</script>

		

		

		<div>
		    <div id="logo-wrapper" class="looplogo">
				<a href="<?php echo $follow_on_link ?>"><img class="saver-hideable" src="https://atomjump.com/wp/wp-content/uploads/2018/12/speech-bubble-start-1.png" id="bg" alt=""></a>
			</div>
		</div>
   		
   		<div class="container-fluid">
			<div class="row justify-content-center">
				<div class="col-md-2">
				</div>
				 <div class="col-md-8">
						<h3 align="center" style="color: #aaa;"><?php echo $main_message ?></h3>
				
						<div class="form-row text-center">
    						<div class="col-12">
				
								<a class="btn btn-primary btn-lg" style="margin: 6px;" href='<?php echo $first_button ?>'><?php echo $first_button_wording ?></a>

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

		</div>



		<div id="comment-holder"></div><!-- holds the popup comments. Can be anywhere between the <body> tags -->


	</body>

</html>
