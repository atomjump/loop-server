<?php if(!$offset) {
		 $offset = "";
	  } ?>
<!DOCTYPE html>
<html lang="en">
  <head>
  	    <meta charset="utf-8">
		 <meta name="viewport" content="width=device-width, user-scalable=no">
		 <?php switch($page) {
		 		case "health":
		 		
					?>
				 	<title>Long Buckby Healthy Delivery</title>
				 	
				 	<meta name="description" content="Offers a local delivery service for healthy foods in Long Buckby, Northamptonshire, United Kingdom.">
				 
				 	<meta name="keywords" content="healthy foods, health food, Long Buckby, Northamptonshire, United Kingdom">
				 	 
				 <?php 
				 break;
				 
				 
				 case "gotcha":
		 		
					?>
				 	<title>AtomJump Gotcha Test - keeping organisations human</title>
				 	
				 	<meta name="description" content="A free customer service response time test that people can try on organisations.">
				 
				 <meta name="keywords" content="customer service, response time">
				 <?php 
				 break;
				 
				 case "seetalent":
				 ?>
				 	<title>AtomJump SeeTalent - live discussion board for TV</title>
				 	
				 	<meta name="description" content="Transform your TV into a live discussion board.">
				 
				 <meta name="keywords" content="discussion board, TV accessory, media streaming">
				 <?php 
				 break;
				 
				 default: 
				 	?>
					 <title>AtomJump Smart Feedback</title>
					 
					 <meta name="description" content="Offer your customers a smart feedback form, with live chat, public & private posts across any mobile or desktop device.">
					 
					 <meta name="keywords" content="Feedback Form, Live Chat, Customer Chat">
		 <?php 		break;
		 		} ?>
		 
			  <!-- Bootstrap core CSS -->
			<link rel="StyleSheet" href="<?php echo $offset; ?>css/bootstrap.min.css" rel="stylesheet">
			
			<!-- AtomJump Feedback CSS -->
			<link rel="StyleSheet" href="<?php echo $offset; ?>css/comments-0.1.css?ver=1">
			
			<!-- Bootstrap HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
			<![endif]-->
			
			<!-- Include your version of jQuery here.  This is version 1.9.1 which is tested with AtomJump Feedback. -->
			<script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script> 
			<!-- Took from here 15 May 2014: http://ajax.googleapis.com/ajax/libs/jquery/1.9.1 -->
			
			<?php switch($page) {
		 		case "health" || "seetalent":
		 		
					?>
					<!-- For the dropdown autocomplete -->
					<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
					<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
				 <?php 		break;
				 
				  default: 
				  break;
		 		} ?>
				
			
			<script>
				var ajFeedback = {
					"uniqueFeedbackId" : "earth_feedback",
					"myMachineUser" : "92.27.10.17:3"
				}
			</script>
			<script type="text/javascript" src="<?php echo $offset; ?>js/chat.js"></script> <!-- TODO - keep path as js/chat.js -->
			
			<style>
				h2 {
					text-align: center;
				}
				
				textarea:focus, input:focus, img:focus {
					outline: 0;
				}
				
				

				
				
				.looplogo {
					position: relative; 
					width: 800px; 
					margin-left: auto; 
					margin-right: auto;
				}
				
				.smart-image {
					position: relative; 
					width: 373px; 
					margin-left: auto; 
					margin-right: auto;
				
				}
				
				.subs {
				
					position: fixed; 
					bottom: 10px; 
					float: left; 
					margin-left: 20px;
				}
				
				.cpy {
					position: fixed;
					right: 10px; 
					bottom: 10px; 
					float: right; 
					margin-right: 20px;
				}
				
				@media screen and (max-width: 480px) {
				
					.looplogo { 
						width: 373px; 
						height:288px; 
						
				
					}
					
					.smart-image { 
						width: 373px; 
										
					}
					
					
				}
				
				@media screen and (-webkit-min-device-pixel-ratio: 3.0) and (max-width: 1080px) {
					.looplogo { 
						width: 320px; 
						height:192px; 
						
				
					}
					
					.smart-image { 
						width: 290px; 
										
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
				
				.h2 {
					text-align: left !important;
				}
				
				.greyed {
				
					background-color: #F5F5F5 !important;
					padding-top: 15px;
					padding-bottom: 15px;
				
				}
				
				.white-backdrop {
				
					background-color: #FFFFFF !important;
					padding-top: 15px;
					padding-bottom: 15px;
				
				}
				
				
				
				
				.looplogo-second:hover {
					position: relative;
					height: 300px;
					width: 300px;
					margin-left: auto;
					margin-right: auto;
					padding-top: 0px;
				}
				
				.looplogo-second {
					position: relative; 
					width: 300px; 
					height:300px; 
					margin-left: auto; 
					margin-right: auto;
				}
				
				
				
				
				
				
				/* iphone and other phones */
				@media screen and (max-width: 480px) {
				
					.looplogo-second { 
						width: 300px; 
						height:300px; 
						
				
					}
					
					.looplogo-second:hover {
						height: 300px;
						width: 300px;
					}
					
					.smart-image { 
						width: 260px; 
										
					}
					
					.subs {
				
						position: relative; 
						margin-top: 10px; 
						float: left; 
						margin-left: 20px;
					}
					
					.cpy {
						position: relative;
						margin-top: 10px; 
						margin-right: 20px;
					}
				}
				
				/* Samsung S4 */
				@media screen and (-webkit-min-device-pixel-ratio: 3.0) and (max-width: 1080px) {
					.looplogo-second { 
						width: 300px; 
						height:300px; 
						
				
					}
					
					.looplogo-second:hover {
						height: 300px;
						width: 300px;
					}
					
					.smart-image { 
						width: 290px; 
										
					}
					
					.subs {
				
						position: relative; 
						bottom: 10px; 
						float: left; 
						margin-left: 20px;
					}
				
					.cpy {
						position: relative;
						right: 10px; 
						bottom: 10px; 
						float: right; 
						margin-right: 20px;
					}
				}
				
				
				<?php if(isset($include_image)) { ?>
						.wrapper{
							background: transparent !important;
							
						}
						
						
						html {
							background-color: transparent !important;
							background-image: url('<?php echo $image ?>');

							background-position: center center !important;
							background-repeat: no-repeat;
							background-attachment: fixed;
							-webkit-background-size: cover;
							-moz-background-size: cover;
							-o-background-size: cover;
							background-size: cover !important;
							height: 100%;
							min-height:100%;
							
						}
						
						
						.darkoverlay {
							
							width: 100%;
							background-color: black;
							opacity: 0.8;
							filter: alpha(opacity=90); /* For IE8 and earlier */
							
						
						}
						
						.st-logo {
							width: 70px;
							height: 70px;
							
							
						}
				<?php } ?>
				
				<?php if((isset($include_image))&&($include_image == true)) { ?>
					.cpy a:link, a:visited {
						color: #888;
						
					}
				<?php } else { ?>
					.cpy a:link, a:visited {
						color: #888;
					}
					
					.link-in-title a:link, a:visited {
						color: #333 !important;
						
					}
				
				<?php } ?>
				
				
				.videoWrapper {
					position: relative;
					padding-bottom: 56.25%; /* 16:9 */
					padding-top: 25px;
					height: 0;
				}
				.videoWrapper iframe {
					position: absolute;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
				}
				

			</style>
			
	</head>
