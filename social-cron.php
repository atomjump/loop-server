<?php
	//Cron job to add new social posts every 2 minutes.
	//To install put the following line in after typing 
    ///usr/bin/php /var/www/html/feedback/social-cron.php 5 layername Search+Terms

	//Based on mail-cron.php
	//In our case: mail_id refers to the overall chat layer
	//			          uid refers to the highest message id from the remote service, from which to start the search (e.g. twitter is a 64-bit integer)
	
	
	$feed_exceptions = array("news","technology","tesco","cricket");		//expand on this list if you add into feed-cron.php. It prevents us running
					//a twitter list against these subdomains
	
	function strip_html_tags( $text )
	{

	
		$text = preg_replace(
		    array(
		      // Remove invisible content
		        '@<head[^>]*?>.*?</head>@siu',
		        '@<style[^>]*?>.*?</style>@siu',
		        '@<script[^>]*?.*?</script>@siu',
		        '@<object[^>]*?.*?</object>@siu',
		        '@<embed[^>]*?.*?</embed>@siu',
		        '@<applet[^>]*?.*?</applet>@siu',
		        '@<noframes[^>]*?.*?</noframes>@siu',
		        '@<noscript[^>]*?.*?</noscript>@siu',
		        '@<noembed[^>]*?.*?</noembed>@siu',
		      // Add line breaks before and after blocks
		        '@</?((address)|(blockquote)|(center)|(del))@iu',
		        '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
		        '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
		        '@</?((table)|(th)|(td)|(caption))@iu',
		        '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
		        '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
		        '@</?((frameset)|(frame)|(iframe))@iu',
		    ),
		    array(
		        ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
		        "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
		        "\n\$0", "\n\$0",
		    ),
		    $text );
		    
		    //Strip excess whitespaces
		    $text = preg_replace('/(\s)+/', ' ', $text);
		    
		return quoted_printable_decode(strip_tags($text));
	}
	
	
	$agent = "AJ feed bot - https://atomjump.com";
	ini_set("user_agent",$agent);
	$_SERVER['HTTP_USER_AGENT'] = $agent;
	$start_path = "/var/www/html/feedback/";
	
	//When testing on staging
	if( (isset($_REQUEST['staging'])) || ((isset($argv[4]))&&($argv[4] == 'staging')) ) {
	   $start_path = "/var/www/html/atomjump_staging/";
	   $_SERVER["SERVER_NAME"] = "staging.atomjump.com";
	   $staging = true;	
	}
	
	
	$notify = false;
	include_once('config/db_connect.php');	
	
	
	require($start_path . "classes/cls.basic_geosearch.php");
	require($start_path . "classes/cls.layer.php");
	require($start_path . "classes/cls.ssshout.php");
	require($start_path . "classes/cls.social.php");
	
	// Include twitteroauth
    require_once($start_path . 'classes/twitter/TwitterAPIExchange.php');


	$bg = new clsBasicGeosearch();
	$ly = new cls_layer();
	$sh = new cls_ssshout();
	$soc = new cls_social();
	
	//Get any commmand line args
	if($argc >= 1) {
		$freq = intval($argv[1]);
		if($argv[2]) {
				$_REQUEST['refresh'] = $argv[2];
		}
		
		if($argv[3]) {
				$query = urldecode($argv[3]);
		}
	} else {
		$freq = 5;
	}
	
	
	function read_tweets($soc, $search, $last_id) {
	     //since_id
	     $resp = $soc->search_twitter($search, $last_id);
	
				 //print_r($resp);
					return $resp;
	
	
	}
	
	
		
					  
	
	$silent = true;
	

	
	if(isset($_REQUEST['refresh'])) {
	 $layer = $_REQUEST['refresh'];
	 $subdomain = str_replace("ajps_", "", $layer);
	
	
	 if(in_array($subdomain, $feed_exceptions)) {
	 	   $feeds = array();
	 } else {
	
			//This is a request from the client, not the server for a particular feed to be refreshed
	
			
			//Now limit the array of accounts to the one that points at.  Note - ideally this would be an indexed database check once we have outgrown our array structure.
			$newfeeds = array();
			
			
			$layer_info = $ly->get_layer_id($layer); //, $reading)
			
			if($layer_info) {
					//yep alreay exists
					$layer_id = $layer_info['int_layer_id'];
			
			} else {
					//Create a new layer
					$layer_id = $ly->new_layer($layer, 'public'); 
					
					//Given this is a new layer - the first user is the correct user
					global $cnf;
					
					$lg = new cls_login();
					$lg->update_subscriptions($cnf['adminMachineUser'], $layer_id);		
					
				}
			
			
			$sql = "SELECT * from tbl_subdomain WHERE var_subdomain = '" . $subdomain . "'";
			$result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
				if($feed = mysql_fetch_array($result)) {
					   //Get the social id to check against
								$social_id = $feed['int_subdomain_id'];
								
								if((!$feed['var_search_words']) || ( (isset($query))&&($feed['var_search_words'] != $query) )) {
								
											$feed['var_search_words'] = $query;
											$feed['int_freq'] = $freq;
								
											//The subdomain already is there, just add in the additional stuff
											$sql = "UPDATE tbl_subdomain SET var_search_words = '" . clean_data(trim($query)) ."',
																				                       int_freq = '" . $freq . "' WHERE int_subdomain_id = " . $social_id;
	
										 mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
						  }
								
				} else {
						 
						//Create the record
						$sql = "INSERT INTO tbl_subdomain (var_subdomain, var_search_words, int_freq) VALUES (
													'" . clean_data(trim($subdomain)) . "',
													'" . clean_data(trim($query)) ."', 
											'" . $freq . "')";			//TODO: more params filled here eg. start date end date?
							mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
				   $social_id = mysql_insert_id();
				   
				   $feed['var_search_words'] = $query;
				   $feed['int_freq'] = $freq;
				}
					 
	   
			
			$freq = 10000;		//always refresh
			$feeds[] = $feed;	//$newfeeds;
		 }
	
			//Get the last date from which we should be searching
			$sql = "SELECT * FROM tbl_ssshout WHERE int_layer_id = " . $layer_id . " AND enm_active = 'true' ORDER BY int_ssshout_id DESC LIMIT 1";
		 $result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
			if($row = mysql_fetch_array($result))
			{
			   $last_date = $row['date_when_shouted'];
			} else {
						$last_date = null;
			}
	}
	
	

	if($silent == false) {
		echo "Frequency: $freq\n";
	}
	
	foreach($feeds as $feed) {
		
		if($silent == false) {
			
		}
		
		if($freq >= $feed['int_freq']) {		//Only call them if the freq in minutes of this request
			
			
			
			
			
			//Find the max last msg - TODO, do this differently for each social network
			$last_msg_id = null;
			$sql = "SELECT MAX(int_uid_id) as lastmsg from tbl_feed WHERE int_social_id = " . $social_id;
			$result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
			if($row = mysql_fetch_array($result))
			{
				$last_msg_id = $row['lastmsg'];
				 if($silent == false) {
			  		echo "Last message id:" . $last_msg_id . "\n";
			  	}
			  	
			  	
			
			}
			
			
			$search_terms = explode(",",$feed['var_search_words']);
			
			$results = array();
		 foreach($search_terms as $search) {
		   if($silent == false) {
		 			  echo "<br>Searching for $search  ";
		   }
		 	   //Search twitter
		 	   $res = read_tweets($soc, $search, $last_msg_id);
		 		  $results = $res;

		 }
			
			

			if($silent == false) {
			}
		
			//reverse statuses to keep cronological order in aj
			$statuses = array_reverse($results->statuses);
		
	
			foreach ($statuses as $message) {
			
			 
			  	$guid = (string) $message->id;
			  	$pubDate = (string) $message->created_at;			//Note: this is GMT
				 
				  
			  if($guid != "") {
		
				  //Check if this item has already been processed for this mailbox
				  $sql = "SELECT * FROM tbl_feed WHERE int_uid_id = '" . trim($guid) . "' AND int_social_id = " . $social_id;
				 
				  $result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
					if($row = mysql_fetch_array($result))
					{
			
						//Already exists - fast skip
					} else {
								//We want to shout this
								
								//Process the message
								$raw_text = $message->user->screen_name . ": Via Twitter: " . $message->text;		//TODO genearlise
								
								
								preg_match( '@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s+charset=([^\s"]+))?@i',
									$raw_text, $matches );
								$encoding = $matches[3];
								 
								/* Convert to UTF-8 before doing anything else */
								$utf8_text = iconv( $encoding, "utf-8", $raw_text );
								 
								/* Strip HTML tags and invisible text */
								$utf8_text = strip_html_tags( $utf8_text );
								 
								/* Decode HTML entities */
								$utf8_text = trim(html_entity_decode( $utf8_text, ENT_QUOTES, "UTF-8" )); 
				
								$subject = imap_utf8(quoted_printable_decode($message->subject));
								$subject = str_ireplace("=?utf-8?Q?","", $subject);		//Remove this weird string
			
								
					
					
								//Now get rid of former replies
								$utf8_text = preg_replace('/On \d(.*?)\d\d\d\d[\,]? at(.*)/is', '', $utf8_text);		//The s allows for newlines in the match so it goes to the end of the string
					
								if($utf8_text != "") {
		
								} else {
									$utf8_text = $subject;
				
								}
						
								
								
									
							
								
					
			
		
								$shouted = summary($utf8_text, 300) . " " . $link;		//guid may not be url for some feeds, may need to have link
								$your_name = $username;
								$whisper_to = $feed['var_whisper_to'];
							 $email ="";
		
								$ip = "92.27.10.17"; //must be something anything
								
								
								
								
								
								
								if($silent == false) {
								  	echo "Message added:" . $shouted . "\n";
								}
								  
							
								 

								  
								 //Insert the shout: ( $latitude, $longitude, $your_name, $shouted, $whisper_to, $email, $ip, $bg, $layer, $typing = false, $ssshout_id = null, $phone = null, $local_msg_id = null, $whisper_site = null, $short_code = null, $public_to = null, $date_override = null )
								 
								 
								 if(strtotime($pubDate) > strtotime($last_date)) {
								 			//Only include if it is after the most recent messages from real users - this keeps consistency for the search order
											$finalDate =  date("Y-m-d H:i:s", strtotime($pubDate));
											
											
											$sh->insert_shout(51, 0, $your_name, $shouted, $whisper_to, $email, $ip, $bg, $layer_id, false, null, null, null, null, null, null, $finalDate);  
									
								 
						
										//Now keep a record of this feed item for easy non duplication
										$sql = "INSERT INTO tbl_feed (int_uid_id, date_when_shouted, int_social_id) VALUES ('" . trim($guid) ."', 
														'" . date("Y-m-d H:i:s", strtotime($pubDate)) . "',
														" . $social_id . ")";		//TODO twitter, fb get different entries, they must be differentiated
										mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
								 }
				  }
						
					
				}
			
			  
			  
			}
		}
	}

	$json = array('Success');
	
	echo $_GET['callback'] . "(" . json_encode($json) . ")";
	
	session_destroy();  //remove session
	
	
?>

