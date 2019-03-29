<?php


	require('config/db_connect.php');

	require("classes/cls.basic_geosearch.php");
	require("classes/cls.layer.php");
	require("classes/cls.ssshout.php");
	require("classes/cls.social.php");

	$bg = new clsBasicGeosearch();
	$ly = new cls_layer();
	$sh = new cls_ssshout();

	function bot_detected() {

	  if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
		return TRUE;
	  }
	  else {
		return FALSE;
	  }

	 }

    function client_platform() {
		 if ((isset($_SERVER['HTTP_USER_AGENT'])) && (preg_match('/windows/i', $_SERVER['HTTP_USER_AGENT']))) {
			return 'windows';
			/* Note: this will miss < Win95 - they would not support 64-bit apps anyway */
		 }

		 if ((isset($_SERVER['HTTP_USER_AGENT'])) && (preg_match('/android/i', $_SERVER['HTTP_USER_AGENT']))) {
		 	return 'android';
		 }
 
 
         if ((isset($_SERVER['HTTP_USER_AGENT'])) && (preg_match('/iphone/i', $_SERVER['HTTP_USER_AGENT']))) {
		 	return 'ios';
		 }
		 
        if ((isset($_SERVER['HTTP_USER_AGENT'])) && (preg_match('/ipad/i', $_SERVER['HTTP_USER_AGENT']))) {
		 	return 'ios';
		 }

		 return 'other';
    }

	$user_message = "";



	$units = "km";

	//Want to use the user ip address for the latitude/longitude if we have specified
	//in the middle of the world - only accurate to city level

	//Get ip address
	$ip = $ly->getFakeIpAddr();


	//Check to see if we're logging in
	if(($_SESSION['logged-user'])||($_REQUEST['remoteapp'] == 'true')) {


		//Get the layer id
		if($_REQUEST['action'] == 'login') {
			if($_REQUEST['sms'] == 'on') {
				$sms = true;
			} else {
				$sms = false;
			}
		} else {
			if($_SESSION['authenticated-layer']) {
				$layer = $_SESSION['authenticated-layer'];
			}
		}



		if(($_REQUEST['passcode'] != '')||($_REQUEST['reading'] != '')) {
			$layer_info = $ly->get_layer_id($_REQUEST['passcode'], $_REQUEST['reading']);


			if($layer_info) {

				$layer_status = "existing";
				$layer = $layer_info['int_layer_id'];


			} else {
				//A new passcode
				$layer_status = "new";
				$layer_info = array();
				$layer_info['enm_access'] = 'private';
				$layer_info['myaccess'] = 'readwrite';
				$layer = $ly->new_layer($_REQUEST['passcode'], 'public');

				//Given this is a new layer - the first user is the correct user
				$lg = new cls_login();
				$lg->update_subscriptions(clean_data($_REQUEST['whisper_site']), $layer);

			}
		}



		if($layer) {
			//We are authenticated to read this layer
			$layer_status = "existing";
			$layer_info = array();
			$layer_info['enm_access'] = 'private';
			$layer_info['myaccess'] = 'readwrite';

			$_SESSION['authenticated-layer'] = $layer;



		} else {

			$layer_status = "existing";
			$layer_info = array();
			$layer_info['enm_access'] = 'private';
			$layer_info['myaccess'] = 'public-admin-write-only';
			$layer = ABOUT_LAYER_ID;		//Default to about layer


		}
	} else {


		//Check on which layer we're watching
		if(($_REQUEST['passcode'] != '')||($_REQUEST['reading'] != '')) {
			$layer_info = $ly->get_layer_id($_REQUEST['passcode'], $_REQUEST['reading']);


			if($layer_info) {

				$layer_status = "existing";
				$layer = $layer_info['int_layer_id'];


			} else {
				//A new passcode
				$layer_status = "new";
				$layer_info = array();
				$layer_info['enm_access'] = 'private';
				$layer_info['myaccess'] = 'readwrite';
				$layer = $ly->new_layer($_REQUEST['passcode'], 'public');

				//Given this is a new layer - the first user is the correct user
				$lg = new cls_login();
				$lg->update_subscriptions(clean_data($_REQUEST['whisper_site']), $layer);

			}
		} else {
			$layer_status = "existing";
			$layer_info = array();
			$layer_info['enm_access'] = 'private';
			$layer_info['myaccess'] = 'public-admin-write-only';
			$layer = ABOUT_LAYER_ID;		//Default to about layer

		}
	}


	if(($_REQUEST['action'] == 'ssshout')&&
		(($layer_info['myaccess'] == 'readwrite')||($_REQUEST['whisper_to'] != ''))) {
		//If we are shouting, and it is a readwrite, or we're privately whispering



		if(($_REQUEST['your_name'] != "")&&($_REQUEST['your_name'] != "Your Name"))  {
			$user_name = urldecode($_REQUEST['your_name']);
			$_SESSION['temp-user-name'] = $user_name;
		} else {
			if($_SESSION['temp-user-name']) {
				$user_name = $_SESSION['temp-user-name'];
			} else {
				$user_name = "";
			}
		}

		//Only do a geocode when doing a post
		if($user_name == "") {		// have fewer requests to freegeoip


			$resp = null; //Geocode option: json_decode($ly->get_remote("http://freegeoip.net/json/" . urlencode($ip),500));	//0.5sec timeout
			if (!$resp->latitude)
			{
				//Try the backup option at telize.com
				//Geocode option $resp = json_decode($ly->get_remote("http://www.telize.com/geoip/" . urlencode($ip),1000));
				$resp = null;
				if(!$resp->latitude) {

					// lookup failed
					$start_lat = 51;
					$start_lon = 0;

					if($user_name == "") {
						$user_name = $msg['msgs'][$lang]['anon'] . " " . substr($ip, -2) ;
						$_SESSION['temp-user-name'] = $user_name;
					}
				} else {

					//Same as response to freegeoip.net
					$start_lat = $resp->latitude;
					$start_lon = $resp->longitude;
					if(($resp->country_code == 'US')||($resp->country_code == 'GB')) {

						$units = "mi";
					}

					if($user_name == "") {
							if($resp->country_code != "") {
								$user_name = $resp->country_code . ' ' . substr($ip, -1);
							} else {
								$user_name = $msg['msgs'][$lang]['anon'] . " " . substr($ip, -2);
							}
					}

					$_SESSION['temp-user-name'] = $user_name;
				}

			}
			else
			{
				$start_lat = $resp->latitude;
				$start_lon = $resp->longitude;
		 	if(($resp->country_code == 'US')||($resp->country_code == 'GB')) {

					$units = "mi";
				}

				if($user_name == "") {
						if($resp->country_code != "") {
							$user_name = $resp->country_code . ' ' . substr($ip, -1);
						} else {
							$user_name = $msg['msgs'][$lang]['anon'] . " " . substr($ip, -2);
						}
				}

				$_SESSION['temp-user-name'] = $user_name;
			}
		} else {

			if($_SESSION['lat']) {
				//use the last session's lat/lon
				$start_lat = $_SESSION['lat'];
				$start_lon = $_SESSION['lon'];
			} else {
				//default lat/lon
				$start_lat = 51;
				$start_lon = 0;
			}
		}

		//If we have already got a precise lat/lon, keep that one
		if($_REQUEST['lat']) {
			$start_lat = $_REQUEST['lat'];
			$start_lon = $_REQUEST['lon'];

		}

		//Save the sessions approx lat/lon
		$_SESSION['lat'] = $start_lat;
		$_SESSION['lon'] = $start_lon;



		if($_REQUEST['typing'] == 'on') {
			$typing = true;

		} else {
			$typing = false;
		}

		//A simple hidden checksum to prevent basic robots - as a sum of all the javascript values
		if($_REQUEST['cs'] == '21633478') {
		    $sh->layer_name = $_REQUEST['passcode'];
			$shout_id = $sh->insert_shout($start_lat, $start_lon, $user_name, $_REQUEST['message'], $_REQUEST['whisper_to'], $_SESSION['logged-email'], $ip, $bg, $layer, $typing, $_REQUEST['shout_id'], $_REQUEST['phone'], $_REQUEST['msg_id'], $_REQUEST['whisper_site'], $_REQUEST['short_code'], $_REQUEST['public_to']);
		}


		if($_REQUEST['remoteapp'] == 'true') {
			header('Content-Type: application/json');
			//Go directly on to a search

			$se = new cls_search();

			if(isset($_REQUEST['msg_id'])) {
				$msg_id = $_REQUEST['msg_id'];
			} else {
				$msg_id = NULL;
			}
			
			$se->process($shout_id, $msg_id, 25);		//25 messages
		}
		
		//Handle any post processing
		global $process_parallel;
		global $process_parallel_url;
		if((isset($process_parallel_url))&&($process_parallel_url != null)) {
		    session_write_close();      //Ensure we don't have anything that runs after this command that uses the sessions 

            while (true) {
	            sleep(5);
	            
	            $r1 = $process_parallel_url->JobPollAsync($process_parallel_url->job);  
	
	            if ($r1 === false) break;
	
	            flush(); @ob_flush();
            }
		
		}
		
		if(count($process_parallel) > 0) {
		    //We have an array of shell commands to run
		    session_write_close();      //Ensure we don't have anything that runs after this command that uses the sessions 
		    flush(); @ob_flush();
		    
		    //Now run a single shell_exec() that runs all of these commands
		    global $cnf;
		    $command = $cnf['phpPath'] . " " . $local_server_path . "run-process.php " . urlencode(json_encode($process_parallel));
		    $cmd = "nohup nice -10 " . $command . " > /dev/null 2>&1 &"; 
		    $ret = shell_exec($cmd);
		
		}
		

		exit(0);		//We don't want to do anything else after a shout, now that it is ajax


	} else {
		if(($_REQUEST['action'] == 'ssshout')||($_REQUEST['reading'] != '')) {
			$user_message = "Read-only";
		}
	}


	//Check if we have options that apply below..
	if($_COOKIE["freq"]) {
		$_REQUEST['freq'] = $_COOKIE["freq"];
	}

	if($_COOKIE["your_name"]) {
		$_REQUEST['your_name'] = urldecode($_COOKIE["your_name"]);

	}


	if($layer_info['enm_access'] == 'private') {
		//A private ssshout
		if($_COOKIE["volume"]) {
			$_REQUEST['volume'] = $_COOKIE["volume"];
		} else {
			//First time here, or haven't changed settings yet - set to uberphone setting
			//This is so that one on one comms will appear to work better on first intro to ssshout
			$_REQUEST['volume'] = 1.00;

		}
	} else {
		//A public ssshout
		if($_COOKIE["volume"]) {
			$_REQUEST['volume'] = $_COOKIE["volume"];
		}
	}

	if($_COOKIE["family_filter"]) {
		$family_filter = $_COOKIE["family_filter"];
		if($family_filter == 'true') {
			$family_filter = true;
		} else {
			$family_filter = false;
		}
	} else {
		$family_filter = true;

	}

	if($_REQUEST['remoteapp'] == 'true') {
		//Go directly on to a search
		header('Content-Type: application/json');
		$se = new cls_search();

		$se->process();
		exit(0);

	}


	$subdomain = check_subdomain();

	if(isset($_REQUEST['orig_query'])) {
		$query = urlencode($_REQUEST['orig_query']);
	} else {
		$query = $subdomain;
	}

	


	if($cnf['serviceHome'] && $cnf['serviceHome'] != "https://yourcompany.com") {
		//Redirect to the homepage of the service. Particularly use by the reset password
		header("Location: " . $cnf['serviceHome']);
		exit(0);
	}


	//Ensure no caching
	header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Pragma: no-cache"); // HTTP/1.0
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

?>
<!DOCTYPE html>
<html lang="en" id="fullscreen">
  <head>
  	    <meta charset="utf-8">
		 <meta name="viewport" content="width=device-width, user-scalable=no">
		 <title>AtomJump Loop Server - provided by AtomJump</title>

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
			  .looplogo:hover {
					position: relative;
					background: url(images/logo640.png)  no-repeat;
					height: 640px;
					width: 640px;
					margin-left: auto;
					margin-right: auto;
					padding-top: 0px;
			 	}

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





				.looplogo:hover {
					position: relative;
					height: 640px;
					width: 640px;
					margin-left: auto;
					margin-right: auto;
					padding-top: 0px;
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




				       .subs {

				        	position: fixed;
					        bottom: 10px;
					        float: left;
					        margin-left: 20px;
					        z-index: 20;
				       }

				       .share {

					        position: fixed;
				        	top: 10px;
				        	float: left;
				        	margin-left: 20px;
				        	z-index: 20;
			       	}

				      .cpy {
					        position: fixed;
					        right: 10px;
				        	bottom: 10px;
					        float: right;
					        margin-right: 20px;

			       	}

				
					.cpy a:link, a:visited {
						color: #888;
					}



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
					position: fixed;
					right: 10px;
					bottom: 10px;
					float: right;
					margin-right: 20px;
					z-index: 20;

				}

				 	<?php if($include_image) { ?>

						.wrapper{
							background: transparent !important;

						}


						html {
							background-color: transparent !important;
							background-image: url('<?php echo $image ?>');

							background-position: center center !important;
							background-repeat: no-repeat;
							 background-attachment: scroll; /* Don't have a fixe background image */
							-webkit-background-size: cover;
							-moz-background-size: cover;
							-o-background-size: cover;
							background-size: cover !important;
							height: 100%;
							min-height:100%;

						}
					<?php } ?>
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
						position: fixed;
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
		<?php if($include_image == true) { ?><div  class="divwrap"><?php } ?>


		

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
				<a href="https://atomjump.com"><img class="saver-hideable" src="https://atomjump.com/images/looplogo.svg" id="bg" alt=""></a>
				<br/>

			</div>
			</a>
		</div>







		<div class="subs">
  				<a href="https://github.com/atomjump/loop-server" title="Download Software"><img  border="0" src="https://atomjump.com/images/loopdownload.svg" width="80" height="80"></a>
		</div>

		
    	<div class="container-fluid darkoverlay" id="mydarkoverlay">
            <div class="row">
                <div class="col-md-2">
                </div>
                 <div class="col-md-8">
                    <h3 align="center">Set 'server' to:
                        <b><?php $actual_link = 'http://'.$_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
                            echo $actual_link; ?></b>
                        
                    </h3>
                 </div>
                <div class="col-md-2">
                </div>
            </div>
		<br/><br/><br/><br/>

		</div>


			<div class="cpy">
				<p align="right"><a href="https://atomjump.com/smart.php">Learn More</a></p>
				<p align="right"><b>Local Server Install</b></p>
				<p align="right"><small>&copy; <?php echo date('Y'); ?> <?php echo $msg['msgs'][$lang]['copyright'] ?></small></p>
			</div>

		<div id="comment-holder"></div><!-- holds the popup comments. Can be anywhere between the <body> tags -->


	</body>

</html>
