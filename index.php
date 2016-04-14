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

	//Get approx lat/lon from city ip address
	$ip = $ly->getRealIpAddr();





	if(isset($_REQUEST['scrsave'])){
	    $screensave = true;
	    $include_image = true;
	    $image = $_REQUEST['scrsave'];
	} else {
	   $screensave = false;

	}



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
			$user_name = $_REQUEST['your_name'];
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
						$user_name = "Anon " . substr($ip, -2) ;
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
								$user_name = "Anon " . substr($ip, -2);
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
							$user_name = "Anon " . substr($ip, -2);
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
		$_REQUEST['your_name'] = $_COOKIE["your_name"];

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

			//Latest social network requests
			global $root_server_url;
			global $staging;

			if(($subdomain != false)&&(!isset($_REQUEST['m']))) {


				$req = "/social-cron.php 1 ajps_" . $subdomain . " " . urlencode($query);

				if($staging == true) {
			    $req .= " staging";			//Use for testing tweets etc.
			 }


				//Special case exception - no tweets on this subdomain
				if($subdomain != 'medimage') {
				   $cmd = 'nohup nice -n 10 /usr/bin/php  ' . getcwd() . $req;
		    if(bot_detected() == FALSE) {
		        $response = shell_exec($cmd);
		    }
		  }
			} else {
				//On someone's website - don't get social, stuff. And also not after having clicking on
				//a twitter link in to here (request 'm')
				if($staging == true) {
					  //Test tweets being added here
								$req = "/social-cron.php 1 test_feedback test staging";			//Use for testing tweets etc.

     				$cmd = 'nohup nice -n 10 /usr/bin/php  ' . getcwd() . $req;
		       $response = shell_exec($cmd);
				}
			}





	//Screen save option - usually either demoing or having purchased
	//Check if there is a fullscreen option for this subdomain


	$include_image = false;
	$forsale = false;
	if($subdomain != false) {
		//Check if there is an owner string for this subdomain
		$sql = "SELECT * FROM tbl_subdomain WHERE var_subdomain = '". clean_data($subdomain) . "'";
		$result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
		if($row = mysql_fetch_array($result))
		{
			$owner_string = $row['var_owner_string'];

				if($row['enm_fullscreen'] == 'true') {
					//Set the screensave option
					$screensave = true;

			   	} else {
			   		$screensave = false;
			   	}

		} else {
		 global $cnf;
			$owner_string = $cnf['adminMachineUser']; 	//AtomJump's home ip and AtomJump user
			$screensave = false;
		}

		$raw_image = $subdomain . ".jpg";
		$image = "images/property/" . $raw_image;
		$image_own = "images/property/" . $subdomain . "_OWN.jpg";
		$image_hi = "images/property/" . $subdomain . "_HI.jpg";





		if(file_exists($local_server_path . $image_own)) {
			//A unique image is owned by someone here
			$include_image = true;
			$image = $image_own;



			//Check if a hi-res image exists already
			$image_hi = "images/property/" . $subdomain . "_OWN_HI.jpg";
			if(file_exists($local_server_path . $image_hi)) {
				$hi_res_image = true;
			} else {
				$hi_res_image = false;
			}


				if(isset($_REQUEST['orig_query'])) {
					//Now we have the image, redirect the browser back to the ex orig-query in order to preserve a nice url
					//to use and copy/share
					header("Location: http://" . $subdomain . ".atomjump.com");
					exit(0);

				}

		} else {


			if(file_exists($local_server_path . $image_hi)) {
				$hi_res_image = true;
			} else {
				$hi_res_image = false;
			}


			if(file_exists($local_server_path . $image)) {
					//Already cached
					$include_image = true;
					$forsale = true;

					if(isset($_REQUEST['orig_query'])) {
					//Now we have the image, redirect the browser back to the ex orig-query in order to preserve a nice url
					//to use and copy/share
					header("Location: http://" . $subdomain . ".atomjump.com");
					exit(0);

				}
			} else {
				//Need to get new image from image service


				if(isset($_REQUEST['orig_query'])) {
					$query = urlencode($_REQUEST['orig_query']);
				} else {
					$query = $subdomain;

				}


				global $cnf;






				//Pixabay
				$results = $ly->get_remote_ssl("https://pixabay.com/api/?key=" . $cnf['pixabay']['key']. "&q=" . $query ."&image_type=photo&min_width=640&min_height=480&safesearch=true&per_page=5");

				//TODO: get approval for hi-res images https://pixabay.com/api/docs/
				//https://pixabay.com/api/?key=APIKEY&response_group=high_resolution&q=yellow+flower

				$results_array = json_decode($results);
				$url = $results_array->hits[0]->webformatURL;		//TODO: can randomise the 0 number from 0 - 4

				if(isset($url)) {
					//Get the image from the server and cache it locally
					file_put_contents($image, file_get_contents($url));

					//Copy across to the other servers for future reference - but do in a separate process
					$cmd = 'nohup nice -n 10 /usr/bin/php  ' . getcwd() . '/send-images.php ' . $raw_image;
					$response = shell_exec($cmd);
				} else {
					//Default image
					$image = "images/property/forsale.jpg";
				}


				if(isset($_REQUEST['orig_query'])) {
					//Now we have the image, redirect the browser back to the ex orig-query in order to preserve a nice url
					//to use and copy/share
					header("Location: http://" . $subdomain . ".atomjump.com");
					exit(0);

				}


				$include_image = true;
				$forsale = true;

			}
		}

	}



	//Special case
	if($subdomain == 'medimage') {
	   $include_image = false;
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
		 <?php if($subdomain == false) { ?><title>AtomJump Loop - smart feedback for your site</title><?php } else { ?><title><?php echo ucwords($subdomain); ?> smart feedback - provided by AtomJump Loop</title><?php } ?>

		 <meta name="description" content="Offer your customers a smart feedback form, with live chat, public & private posts across any mobile or desktop device.">

		 <meta name="keywords" content="Feedback Form, Live Chat, Customer Chat">

			  <!-- Bootstrap core CSS -->
			<link rel="StyleSheet" href="css/bootstrap.min.css" rel="stylesheet">

			<!-- AtomJump Feedback CSS -->
			<link rel="StyleSheet" href="css/comments-0.1.css">

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


			<script>
				var ajFeedback = {
					"uniqueFeedbackId" : "<?php if($subdomain == false) { ?>test_feedback<?php
											} else {
												echo 'ajps_' . $subdomain;
											} ?>",
					"myMachineUser" : "<?php if(isset($owner_string)) {
												echo $owner_string;
											} else {
											 global $cnf;
												echo $cnf['adminMachineUser'];
											}
										 ?>",

					<?php if($staging == true) {
					    ?>"server": "https://staging.atomjump.com"<?php
					} else {
					    ?>"server": "https://atomjump.com"<?php
					} ?>				}
			</script>
			<script type="text/javascript" src="js/chat-1.0.js"></script>



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

				<?php if($include_image) { ?>
    .wrapper{
					background: url('<?php echo $image ?>') center center fixed;  /* fixed : not on mobiles */
					-webkit-background-size: cover;
					-moz-background-size: cover;
					-o-background-size: cover;
					background-size: cover;


					transition: background 0.5s ease-in-out;
					-webkit-transition: background 0.5s ease-in-out;
   					-moz-transition: background 0.5s ease-in-out;
    	}



		   <?php if($screensave == true) { ?>
		   .screensave {

						z-index: 6000;
						width: 100%;
						position: absolute;
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






		    		.screensave-subtitle {
		    			z-index: 6001;
		    			font-size: 400%;
		    			color: white;
		    			text-shadow: black 0.1em 0.1em 0.2em;
		    			top: 60px;
		    			position: relative;
		    			text-align: center;
		    			width: 100%;

		    		}

		    		.screensave-title {
		    			z-index: 6001;
		    			font-size: <?php if(strlen($subdomain) > 8) {
		    								echo "500%";
		    							} else {
		    								if(strlen($subdomain) > 16) {
		    									echo "350%";
		    								} else {
		    									echo "800%";

		    								}
		    							} ?>;
		    			color: white;
		    			text-shadow: black 0.1em 0.1em 0.2em;
		    			top: 30px;
		    			position: relative;
		    			text-align: center;
		    			width: 100%;
		    			word-wrap: break-word;

		    		}

		    		.screensave-supports {
		    			z-index: 6001;
		    			width: 192px;
		    			margin-left: auto;
						   margin-right: auto;
		    			top: 30px;
		    			position: relative;
		    			word-wrap: break-word;
		    		}

		    		.screensave-height {
		    			z-index: 2500;			/* half way between popupcontainer and popup */
		    		}

		    		.screensave-button {

		    			font-size: 100%;
		    			color: white;
		    			text-shadow: black 0.1em 0.1em 0.2em;
		    			position: fixed;
						   right: 2px;
						   top: 4px;
					    float: right;
						   margin-right: 0px;

		    		}

		    		.screensave-button a:link, a:visited {
						    color: white;
						    text-shadow: black 0.1em 0.1em 0.2em;
				    }

		    		<?php } ?>


        		<?php } ?>



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

				<?php if($include_image == true) { ?>
					.cpy a:link, a:visited {
						color: white;
						text-shadow: black 0.1em 0.1em 0.2em
					}
				<?php } else { ?>
					.cpy a:link, a:visited {
						color: #888;
					}

				<?php } ?>


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


					<?php if($include_image) { ?>
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
					<?php } ?>
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





			<?php if($subdomain == false) { ?>
				<script id="sumo" src="//load.sumome.com/" data-sumo-site-id="91888ccca450632c1e4d0b15524a9d3d5c3af4efcfa8683124fa7e29ca461fa8" async="async"></script>
			<?php } else { ?>
				<script id="sumo" src="//load.sumome.com/" data-sumo-site-id="91888ccca450632c1e4d0b15524a9d3d5c3af4efcfa8683124fa7e29ca461fa8" async="async"></script>
			<?php } ?>

			<!--[if IE 8]>
				<script>
					ie8 = true;
					document.getElementById('sumo').src = "";	//blank out this on IE8
				</script>
			<![endif]-->

	</head>

	<body  <?php if($include_image == true) { ?>class="wrapper"<?php } ?>  onscroll="$('#download-tip').hide();">
		<?php if($include_image == true) { ?><div  class="divwrap"><?php } ?>


		<?php if($screensave == true) { ?>
			<div class="screensave-button screensave-height" title="Go Fullscreen"><a class="screensave-height" href="javascript:" id="start-screen-saver"><img class="screensave-height" src="images/largerscreen.svg" width="47" height="36" border="0"></a></div>
			<div id="screensaver" class="screensave" style="display: none;">

				<div class="screensave-subtitle">Discuss Live @</div>
				<div class="screensave-title"><span style="word-wrap:break-word;"><?php echo $subdomain ?></span>&#8203;.ajmp&#8203;.co</div>
				<div class="screensave-supports"><img src="images/android.png" width="64" height="64" border="0"><img src="images/iphone.png" width="64" height="64" border="0"><img src="images/www.png" width="64" height="64" border="0"></div>
			</div>
			<?php if(isset($_REQUEST['show'])) { ?>
				<?php if(!isset($_REQUEST['kiosk'])) { ?>
					<div id="dialog-confirm" title="Full-screen">
						<p>The SeeTalent streaming page is about to go full-screen.  Please confirm.</p>
					</div>
				<?php } ?>
			<?php } ?>

		<?php } ?>

		<script>


				function openPopup(possibleUser, check)
				{
						var screenWidth = $(window).width();
						var screenHeight = $(window).height();
						$('#comment-popup-container').width(Math.floor(screenWidth) + "px");
						$('#comment-popup-container').height(Math.floor(screenHeight) + "px");



						$("#comment-in-here").html('');


						$('#comment-popup-container').fadeIn(400,function(){



							var wid = ($("#comment-in-here").width() - 5);		//5 is to ensure scroll bar always accounted for
							var hei = ($("#comment-popup-text-container").height() - 10);


							$("#comment-in-here").html('<iframe id="comment-iframe" src="' + ssshoutServer + '/search-secure.php?width=' + wid + '&height=' + hei + '&uniqueFeedbackId=' + commentLayer + '&myMachineUser=' + whisperOften + '&possible_user=' + possibleUser + '&check=' + check + '&clientremoteurl=' + encodeURIComponent(myUrl) + '" frameBorder="0" scrolling="no" width="' + wid + '" height="' + hei + '" onload="$(\'#comment-loading\').hide();"></iframe>');
						});
						//end duplicate
				}




				<?php if($screensave == true) { ?>

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


					function toggleScreenSaver()
					{

						//Always clear this
						if(saverOn) {
							window.clearInterval(saverOn);
						}
						if(saverOff) {
							window.clearInterval(saverOff);
						}
						$('#screensaver').fadeOut();
						$('#mydarkoverlay').fadeIn();

						e = document.getElementById("fullscreen");

						if (RunPrefixMethod(document, "FullScreen") || RunPrefixMethod(document, "IsFullScreen") || startedFullScreen == true) {


							RunPrefixMethod(document, "CancelFullScreen");
							//exit fullscreen

							//Show other screen saver items again
							$('.saver-hideable').show();

							$('.screensave-height').css('z-index', 2500);		//move button higher to be displayed

							if(isChromeDesktop()) {
								$('#logo-wrapper').height(600);		//due to weird Chrome bug, returning to original state
							}


							startedFullScreen = false;

							//Duplicate of chat.js functionality
							$('#comment-iframe').attr('src','');		//blank out the iframe, kicking in the onbeforeupdate event

							$("#comment-popup-container").fadeOut(400, function() {


								//Tell iframe to stop searching
								var ifr = document.getElementById('comment-iframe');
								if(ifr) {
									var receiver =  ifr.contentWindow;
									if(receiver) {
										receiver.postMessage('stop', ssshoutServer);
									}
								}

								$("#comment-in-here").html('');

								$("#comment-popup-container").hide();
								$('#comment-holder').focus();
								$('#comment-loading').show();
							});
							ssshoutHasFocusOuter = false;	//stop seekin new search terms;
							window.focus();
							//end duplicate



						}
						else {
							//start fullscreen
							startedFullScreen = true;
							$('.screensave-height').css('z-index', 6001);		//move button higher to be displayed

							RunPrefixMethod(e, "RequestFullScreen");
							$('#start-screen-saver').blur();		//remove focus on button in Firefox

							//Show instructions initially
							$('#screensaver').fadeIn('fast');
							setTimeout("$('#screensaver').fadeOut('fast');",5000);	//5 seconds initially

							if(isChromeDesktop()) {
								$('#logo-wrapper').height(screen.height);		//this is a weird bug in Chrome.
							}

							//Hide the other screen features
							$('.saver-hideable').hide();

							saverOn = window.setInterval("$('#screensaver').fadeIn('fast');", 20000);	//Toggle on regularly

							setTimeout("toggleOffSaver();", 5000);	//Toggle off regularly
							$('#mydarkoverlay').fadeOut();

							setTimeout("openPopup('','');", 1000);	//wait a second

						}
					}









					function toggleOffSaver()
					{
						if(saverOn) {	//If we're still on, then follow up with an off
							saverOff = window.setInterval("$('#screensaver').fadeOut('fast');", 20000);
						}

					}




					var saverOn;	//Toggle on regularly
					var saverOff;
					var startedFullScreen = false;

						$('#start-screen-saver').click(function() {		//register a click event

						toggleScreenSaver();

					});


					<?php if((isset($_REQUEST['show'])) && ($screensave == true)) { ?>
						<?php if(isset($_REQUEST['kiosk'])) { ?>
							//This mode is particularly for kiosks which are already fullscreen, just need to start sequence
							toggleScreenSaver();
						<?php } else { ?>
							//Need a keyboard or mouse response to go fullscreen
							 $( "#dialog-confirm" ).dialog({
									resizable: false,
									height:240,
									modal: true,
									buttons: {
										"Go Full-screen": function() {

												$( this ).dialog( "close" );
												toggleScreenSaver();
											},
											Cancel: function() {
												$( this ).dialog( "close" );
											}
										}
							});
						<?php } ?>
					<?php } ?>
				<?php } ?>

				function newDomain(input) {

					origStr = document.getElementById(input).value;
					str = origStr.replace(/\s+/g, '');
					str = str.replace(/[^a-z0-9]/gi, '');


					if(str == origStr) {
						//Straightforward redirect
						window.location = 'http://' + str + '.atomjump.com';
					} else {
						window.location = 'http://' + str + '.atomjump.com/?orig_query=' + encodeURIComponent(origStr);


					}

					return false;



				}

				function getHiRes()
				{
					if($('html').css('background-image') != "none") {
						//Yes we are probably a mobile device
						$('html').css('background-image', "url('<?php echo $image_hi ?>')");
					} else {
						$('.wrapper').css('background-image', "url('<?php echo $image_hi ?>')");

					}


					$('.screensave').css('background-image', "url('<?php echo $image_hi ?>')");		//This is usually carried out in the background

				}

				$(document).ready(function(){

				 	//http://stackoverflow.com/questions/24944925/background-image-jumps-when-address-bar-hides-ios-android-mobile-chrome
				 	var bg = jQuery("html");

					$(window).resize(function() { resizeBackground(); });

					function resizeBackground() {
								bg.height(screen.height);
					}
					resizeBackground();



				 $( "#doma" ).autocomplete({
						source: "search-suggest.php",
						minLength: 1,//search after one characters
						select: function(event,ui){
							//do something
							$('#doma').val(ui.item.value);
							newDomain("doma");
						}
					});

					$( "#srch" ).autocomplete({
						source: "search-suggest.php",
						minLength: 1,//search after one characters
						select: function(event,ui){
							//do something
							$('#srch').val(ui.item.value);
							newDomain("srch");
						}
					});

					<?php if(isset($_REQUEST['t'])) {	//we already know who the user is suppose to be, auto start the popup
								//TODO - we need to make this the email address of the user t
							$email = $sh->get_email_from_user_id_insecure($_REQUEST['t'], $_REQUEST['c']);

							if($email != false) {
								?>
								email = '<?php echo $email ?>';
								document.cookie = 'email=' + email + '; path=/; expires=Thu,31-Dec-2020 00:00:00 GMT;';
								openPopup(email, <?php echo $_REQUEST['c'] ?>);


					<?php	}
						} ?>




				});
		</script>

		<?php if($hi_res_image == true) { //Start loading this on page load, but only show it once it is loaded ?>
		<div style="display: none;">
			<img src="<?php echo $image_hi ?>" alt="" onload="getHiRes();"/>
		</div>
		<?php } ?>

		<?php if($subdomain == false) { ?>

		<div class="container-fluid" >

			<div class="row" style="padding-top: 40px; ">
				<div class="col-md-4">
    			</div>

				<div class="col-md-4">

					<form name="frmMainSearch" action="index.php" onsubmit="return newDomain('srch');" method="POST" style="position: relative; z-index: 1000;" >
					<div class="form-group col-xs-12 col-sm-12 col-md-12 col-lg-12">
					  <div class="" >
						<input id="srch" name="orig_query" type="text" class="form-control" maxlength="30" placeholder="Feedback On.." autocomplete="off" style="background-color:#f8ebec; color: #701375; border: 1px solid #8473d9;">
					  </div>
					</div>

				</form>
				</div>
				<div class="col-md-4">
    			</div>
			</div>
		</div>

		<?php } ?>

		<div style="">
			<a class="comment-open" id="this-comment-open" href="javascript:" data-uniquefeedbackid="<?php if($subdomain == false) { ?>test_feedback<?php
											} else {
												echo 'ajps_' . $subdomain;
											} ?>" data-mymachineuser="<?php if(isset($owner_string)) {
												echo $owner_string;
											} else {
											 global $cnf;
												echo $cnf['adminMachineUser'];
											}
										 ?>" <?php if(($subdomain == true)&&($subdomain != 'medimage')) { ?>data-socialrefresh="true"<?php } ?> >


			<div id="logo-wrapper" class="looplogo">
				<img class="saver-hideable" src="images/<?php if($subdomain == false) {
						   echo "looplogo.svg";
				   } else {
				     if($subdomain == 'medimage') {
				       echo "medimage-www-logo.svg";
				     } else {
					       	if($forsale == false) {
						        	echo "looplogo_trans_single.svg";
						      } else {
						        	echo "looplogo_trans_sale.svg";
						      }
						   }
					} ?>" id="bg" alt="">
				<!-- Any link on the page can have the 'comment-open' class added and a blank 'href="javascript:"' --><br/>

			</div>
			</a>
		</div>







		<?php if($subdomain == false) { ?>
				<div class="subs">
	  				<a href="https://github.com/atomjump/loop" title="Download Software"><img  border="0" src="images/loopdownload.svg" width="80" height="80"></a>
			</div>
		<?php } ?>

		<?php if($subdomain == 'medimage') { ?>
			<div class="subs">


			 <a href="<?php $platform = client_platform();
			 		switch($platform) {
			 			case 'windows':
			 				echo 'https://atomjump.com/public_product/MedImageInstaller.exe';
			 			break;

			 			case 'android':
			 				echo 'https://play.google.com/store/apps/details?id=com.phonegap.medimage';
			 			break;
			 			
			 			case 'ios':
			 			 echo 'https://itunes.apple.com/us/app/atomjump-medimage/id1087679463?ls=1&mt=8';
			 			break;

			 			default:
			 				echo 'medimage.php';
			 			break;
			 	} ?>" title="Download Software"><img  border="0" src="images/loopdownload.svg" width="80" height="80"><?php if($platform == 'windows') { ?><img id="download-tip" border="0" src="images/download-tip.png" width="261" height="150" style="position: relative; top: -70px"><?php } ?></a>
			</div>
		<?php } ?>

		<!--<br/><br/><br/>
    	<br/><br/><br/>	-->

    	<div class="container-fluid darkoverlay" id="mydarkoverlay">
   	<?php if($subdomain != 'medimage') { ?>

			<div class="row" style="padding-top: 30px">
				<div class="col-md-2">
    			</div>

				<div class="col-md-8">

					<form name="frmNewDomain" action="index.php" onsubmit="return newDomain('doma');" method="POST">
						<div class="form-group col-xs-12 col-sm-12 col-md-7 col-lg-8">
						  <div class="">
							<input id="doma" name="orig_query" type="text" class="form-control" maxlength="30" placeholder="Enter a place or brand" autocomplete="off">
						  </div>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-5 col-lg-4">
							<button type="submit" onclick="return newDomain('doma');" id="private-button"  class="btn btn-info" style="margin-bottom:3px;">AtomJump</button>

						</div>
					</form>
				</div>
				<div class="col-md-2">
    			</div>
			</div>
  <?php } ?>
  
  	<?php if($subdomain == 'medimage') { ?>
  
			  <div class="row" style="padding-top: 30px">
 
    			<div class="col-md-2">
    			</div>
  			  
  			  <div class="col-md-8">
  	     	<h3 style="color: #CCC;">Photograph and catalogue your patient's health issues with your phone, and send them directly to your PC.</h3>
  		     <h4 style="color: #999;">Available on Android Play and the Apple Appstore. Companion software available for PC, Mac and Linux.</h4>
  		   	</div>
   			
    			<div class="col-md-2">
    			</div>
   			
    	</div>
    	
    	
     	<br/>
     	<br/>
     	<br/>
 	
     
     
     <div class="row">
 	
      	<div class="col-md-2"> 
      			</div>
  	 		  <div class="col-md-4">
      	   <h3>On your phone, enter a patient ID, then snap!...</h3> 			      
  
   		   	</div>
   			
    			  
  			  	<div class="col-md-4">
         <h3>...on your PC</h3>
 
    			</div>
   
  		   	<div class="col-md-2">
    			</div>
   

     </div>  	
    	<?php 
        date_default_timezone_set('UTC');

    	   $mytime = date("-Y-m-d-H-i-s") . ".jpg";
    	   $mytime = "-[timestamp].jpg";
    	   
    	   ?>
	 
	 
	 
	    <div class="row">
 
    			<div class="col-md-2">
    			</div>
  			  
  			  <div class="col-md-4">
    		     <input type="text" placeholder="" class="form-control" style="width:50%;">
  		   	</div>
   			
   			  
  			  	<div class="col-md-4">
  			  	    <h4 style="color: #999;">\ image<?php echo $mytime ?></h4>
    			</div>
   
  		   	<div class="col-md-2">
    			</div>
 			
    	</div>
     
     <br/>
     
     <div class="row">
 
    			<div class="col-md-2">
    			</div>
  			  
  			  <div class="col-md-4">
    		     <input type="text" placeholder="patient01" class="form-control" style="width:50%;"  >
  		   	</div>
   			
     			  
  			  	<div class="col-md-4">
  			  	    <h4 style="color: #999;">\ patient01<?php echo $mytime ?></h4>
    			</div>
   
  		   	<div class="col-md-2">
    			</div>
 			
    	</div>
    	
     <br/>
   	
     <div class="row">
 
    			<div class="col-md-2">
    			</div>
  			  
  			  <div class="col-md-4">
    		     <input type="text" placeholder="#patient02" class="form-control"  style="width:50%;">
  		   	</div>
   			
    			  
  			  	<div class="col-md-4">
  			  	    <h4 style="color: #999;">\ patient02 \ image<?php echo $mytime ?></h4>
    			</div>
   
  		   	<div class="col-md-2">
    			</div>
 			
    	</div>
 
     <br/>
 
 
     <div class="row">
 
    			<div class="col-md-2">
    			</div>
  			  
  			  <div class="col-md-4">
    		     <input type="text" placeholder="#patient02 mole" class="form-control" style="width:50%;">
  		   	</div>
   			
  	  
  			  	<div class="col-md-4">
  			  	    <h4 style="color: #999;">\ patient02 \ mole<?php echo $mytime ?></h4>
    			</div>
   
  		   	<div class="col-md-2">
    			</div>
 			
    	</div>

   <?php } else { //start of normal rows ?>
  
			<div class="row">
 
    			<div class="col-md-2">
    			</div>

    			<div class="col-md-2">
					<h3>Top Topics</h3>
					<ul>
					<li><a href="http://news.atomjump.com">News</a></li>
					<li><a href="http://technology.atomjump.com">Technology</a></li>
					<li><a href="http://health.atomjump.com">Health</a></li>
					<li><a href="http://music.atomjump.com">Music</a></li>
					<li><a href="http://fitness.atomjump.com">Fitness</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_topics" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More topics..</a></li>
					</ul>
				</div>





				<div class="col-md-2">

					<h3>Companies</h3>
					<ul>
					<li><a href="http://icbc.atomjump.com">ICBC</a></li>
					<li><a href="http://shell.atomjump.com">Shell</a></li>
					<li><a href="http://toyota.atomjump.com">Toyota</a></li>
					<li><a href="http://ge.atomjump.com">General Electric</a></li>
					<li><a href="http://hsbc.atomjump.com">HSBC</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_companies" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More companies..</a></li>
					</ul>
				</div>

				<div class="col-md-2">
					<h3>Top Places</h3>
					<ul>
					<li><a href="http://georgewashingtonbridge.atomjump.com">George Washington Bridge</a></li>
					<li><a href="http://timessquare.atomjump.com">Times Square</a></li>
					<li><a href="http://panamacanal.atomjump.com">Panama Canal</a></li>
					<li><a href="http://shibuyacrossing.atomjump.com">Shibuya Crossing</a></li>
					<li><a href="http://oxfordstreet.atomjump.com">Oxford Street</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_places" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More places..</a></li>
					</ul>
				</div>

				<div class="col-md-2" style="padding-left: 20px;">
					<h3>Top Cities</h3>
					<ul>
					<li><a href="http://newyork.atomjump.com">New York</a></li>
					<li><a href="http://london.atomjump.com">London</a></li>
					<li><a href="http://tokyo.atomjump.com">Tokyo</a></li>
					<li><a href="http://mexicocity.atomjump.com">Mexico City</a></li>
					<li><a href="http://delhi.atomjump.com">Delhi</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_cities" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More cities..</a></li>
					</ul>
				</div>

				<div class="col-md-2">
    			</div>


			</div>

			<div class="row">




				<div class="col-md-2">
    			</div>
    			<div class="col-md-2">
					<h3>Top Stations</h3>
					<ul>
					<li><a href="http://shinjukustation.atomjump.com">Shinjuku</a></li>
					<li><a href="http://parisnord.atomjump.com">Paris Nord</a></li>
					<li><a href="http://taipeirailwaystation.atomjump.com">Taipei Railway Station</a></li>
					<li><a href="http://romatermini.atomjump.com">Roma Termini Railway Station</a></li>
					<li><a href="http://delhistation.atomjump.com">Delhi Station</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_stations" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More stations..</a></li>
					</ul>
				</div>

				<div class="col-md-2">
					<h3>Top Airports</h3>
					<ul>
					<li><a href="http://hartsfield-jacksonatlanta.atomjump.com">Hartsfield-Jackson Atlanta</a></li>
					<li><a href="http://beijingairport.atomjump.com">Beijing</a></li>
					<li><a href="http://heathrow.atomjump.com">London Heathrow</a></li>
					<li><a href="http://tokyohaneda.atomjump.com">Tokyo Haneda</a></li>
					<li><a href="http://chicagoohare.atomjump.com">Chicago O'Hare</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_airports" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More airports..</a></li>
					</ul>
				</div>


				<div class="col-md-2">

					<h3>Top Countries</h3>
					<ul>
					<li><a href="http://qatar.atomjump.com">Qatar</a></li>
					<li><a href="http://luxemburg.atomjump.com">Luxembourg</a></li>
					<li><a href="http://singapore.atomjump.com">Singapore</a></li>
					<li><a href="http://norway.atomjump.com">Norway</a></li>
					<li><a href="http://bruneidarussalam.atomjump.com">Brunei Darussalam</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_countries" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More countries..</a></li>
					</ul>
				</div>


				<?php /*<div class="col-md-2">
					<h3>Top Hotels</h3>
					<ul>
					<li><a href="http://hotelpresidentwilson.atomjump.com">Hotel President Wilson</a></li>
					<li><a href="http://rajpalacejaipurhotel.atomjump.com">Raj Palace Hotel</a></li>
					<li><a href="http://fourseasonshotel.atomjump.com">Four Seasons Hotel</a></li>
					<li><a href="http://laucalaislandresort.atomjump.com">Laucala Island Resort</a></li>
					<li><a href="http://hotelplazaathenee.atomjump.com">Hotel Plaza Athenee</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_hotels" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More hotels..</a></li>
					</ul>
				</div> */ ?>

				<div class="col-md-2" style="padding-left: 20px;">


					<h3>Top People</h3>
					<ul>
					<li><a href="http://billgates.atomjump.com">Bill Gates</a></li>
					<li><a href="http://carlosslim.atomjump.com">Carlos Slim</a></li>
					<li><a href="http://warrenbuffett.atomjump.com">Warren Buffett</a></li>
					<li><a href="http://amancioortega.atomjump.com">Amancio Ortega</a></li>
					<li><a href="http://larryellison.atomjump.com">Larry Ellison</a></li>
					<li><a class="comment-open" data-uniquefeedbackid="ajp_more_people" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">More people..</a></li>
					</ul>
				</div>

				<div class="col-md-2">
    			</div>
			</div>

			<br/>

			<div class="row">

    			<div class="col-md-2">
    			</div>
    			<div class="col-md-8">
    			 <h3>What is this service?</h3>
    				<p>A <a href="smart.php">smart feedback</a> tool, that can be used to communicate in real-time with users or other businesses.</p>
    				<p><a href="https://github.com/atomjump/loop">Developers</a> can add the live group feedback to their websites, for free.</p>
    				<p>Small businesses can create a free hosted <a href="howto.php">feedback forum</a> instantly.</p>
    				<p>The public can share text, video and pictorial feedback live with their favourite services.</p>


    				<br/>
    				<br/>
    				<h3>Meeting up? <a href="http://feerce.com">Feerce</a> is your app.</h3>


    			</div>
				<div class="col-md-2">
    			</div>
			</div>



   <?php } //end of normal links ?>
			<br/><br/><br/><br/>

		</div>

		<?php if($subdomain == false) { ?>
			<div class="cpy">
				<p align="right"><a href="<?php if($subdomain == 'medimage') { echo 'medimage.php'; } else { echo 'howto.php'; } ?>">Learn More</a></p>
				<p align="right"><a class="comment-open" data-uniquefeedbackid="aj_other_products" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">Other Products</a></p>
				<p align="right"><small>&copy; <?php echo date('Y'); ?> AtomJump.com</small></p>
			</div>
		<?php } else { ?>
			<div class="cpy saver-hideable">


				<p align="right"><a href="<?php if($subdomain == 'medimage') { echo 'medimage.php'; } else { echo 'howto.php'; } ?>">Learn More</a></p>
				<p align="right"><a class="comment-open" data-uniquefeedbackid="ajp_credits" data-mymachineuser="<?php global $cnf; echo $cnf['adminMachineUser']; ?>" href="javascript:">Credits</a></p>

				<p align="right"><small>&copy; <?php echo date('Y'); ?> AtomJump.com</small></p>
			</div>

    	<?php } ?>

		<div id="comment-holder"></div><!-- holds the popup comments. Can be anywhere between the <body> tags -->

		<?php require_once("components/piwik.php"); ?>

		<?php if($include_image == true) { ?></div><?php } //end of divwrap ?>
	</body>

</html>
