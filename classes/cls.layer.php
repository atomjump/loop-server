<?php 

global $cfg;
global $msg;
global $lang;
define("CUSTOMER_PRICE_PER_SMS_US_DOLLARS", $cnf['sms']['USDollarsPerSMS']);  //$0.16 or 10p. When we are charged 5p per message.

class cls_layer
{

 	public $layer_name;
 	public $always_send_email;
 	
 	
 	function __construct()
 	{
 		$always_send_email = false;			//By default we don't always send an email i.e. we check if we have already sent one. But there are some examples where we do always want to send an email, e.g. shortmail
 	
 	}
 	
 	

	public function get_layer_id($passcode, $reading = null)
	{

		$this->layer_name = $passcode; //store

	
	
		
	


		if($passcode != "") {
			//This is a private passcode request
			$sql = "SELECT * FROM tbl_layer WHERE passcode = '" . md5($passcode). "'";
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			if($row = db_fetch_array($result))
			{
				$row['myaccess'] = 'readwrite';
				if($row['int_group_id']) {
					//Private messages will be sent to the master group user
					$_SESSION['layer-group-user'] = $row['int_group_id'];
				
				} else {
					
					$_SESSION['layer-group-user'] = '';
					
				}
				
				
				if($row['var_public_code']) {
					//Yes, this layer needs access to be granted - set status to false until we have set it from a login
					if(!isset($_SESSION['access-layer-granted'])||($_SESSION['access-layer-granted'] == "")) {
						$_SESSION['access-layer-granted'] = 'false';
					}
				} else {
					$_SESSION['access-layer-granted'] = 'true';
				
				}
				
				//Check we're an owner of the layer.
				$lg = new cls_login();				
				if($lg->is_owner($_SESSION['logged-user'], $row['int_group_id'], $row['int_layer_id'])) {
					//Cool is owner, so authenticate this layer
					$_SESSION['authenticated-layer'] = $row['int_layer_id'];
				} else {
					//unset the authenticated layer
					$_SESSION['authenticated-layer'] = '';
				}
							
				//Get the group user if necessary
				$lg->get_group_user();
			
				
				return $row;
			} 
		} else {
			if($reading != "") {
				$sql = "SELECT * FROM tbl_layer WHERE int_layer_id = '" . $reading . "'";
				$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
				if($row = db_fetch_array($result))
				{
					if($row['enm_access'] == 'public-admin-write-only') {
						//If we're reading and we have public read access then proceed with a search
						$row['myaccess'] = 'read';
						
						if($row['int_group_id']) {
							//Private messages will be sent to the master group user
							$_SESSION['layer-group-user'] = $row['int_group_id'];
				
						} else {
					     
				       		$_SESSION['layer-group-user'] = '';
				
				    	}
				    	
				    	
				    	
				    	if($row['var_public_code']) {
							//Yes, this layer needs access to be granted - set status to false until we have set it from a login
							error_log("Check c:" . $_SESSION['access-layer-granted']);		//TESTING
							if(!isset($_SESSION['access-layer-granted'])||($_SESSION['access-layer-granted'] == "")) {
								$_SESSION['access-layer-granted'] = 'false';
							}
						} else {
							$_SESSION['access-layer-granted'] = 'true';
				
						}
				    	
				    	
				    	//Check we're an owner of the layer
				    	$lg = new cls_login();
						if($lg->is_owner($_SESSION['logged-user'], $row['int_group_id'], $row['int_layer_id'])) {
							//Cool is owner, so authenticate this layer
							$_SESSION['authenticated-layer'] = $row['int_layer_id'];
						} else {
							//unset the authenticated layer
							$_SESSION['authenticated-layer'] = '';
						}
						
						
						//Get the group user if necessary
						$lg->get_group_user();
					
			
								
						
						return $row;
					}
				}
			} 
		}
		
		//Definitely not an owner
		$_SESSION['authenticated-layer'] = '';
	
		return false;

	}
	
	public function new_layer($passcode, $status, $group_id = 'NULL', $public_passcode = NULL, $date_decay = 'NULL')
	{
		global $cnf;
		
		//Inputs:
		//passcode is the layer name in text format
		//status = 'public','public-admin-write-only','private'
		//group_id = optional, if the later is a part of a group of layers
		//public_passcode = ...
		//title is created if the config file includes showAutomaticTitle = true
		//     and there are multiple replacement strings in titleReplace
		//date_decay = in MySQL date/time format ("yyyy-mm-dd hh:mm:ss"), for the
		//intended time the forum will self-decay. Note: you will need to add a 'decay' 
		//plugin to switch this capability on.
		
		if($public_passcode == NULL) {
			$public_passcode = "NULL";
		} else {
			$public_passcode = "'" . $public_passcode . "'";  //usually a text string except when null. Note: TODO: check may need a clean_data() around the $public_passcode above?
		}
		
		if(isset($cnf['showAutomaticTitle'])&&($cnf['showAutomaticTitle'] == true)) {
			$title = $passcode;
			//Loop through each replace expression of the forum name and remove from the title
			
			if(isset($cnf['titleReplace'])) {
		
				for($cnt = 0; $cnt < count($cnf['titleReplace']); $cnt++) {
					$regex = $cnf['titleReplace'][$cnt]['regex'];
					$replace_with = $cnf['titleReplace'][$cnt]['replaceWith'];
				
					$title = preg_replace($regex, $replace_with, $title);
				}
			}
						
			$title = "'" . clean_data($title) . "'";	//Encapsulate for SQL
		} else {
			$title = "NULL";		//a blank database entry		
		}
		
		//Optional decay time on this forum
		if(isset($_REQUEST['general'])) {
			$general_data = explode(",", $_REQUEST['general']);
			for($cnt = 0; $cnt < count($general_data); $cnt++) {
				$tag = explode(":", $general_data[$cnt]);
				if($tag[0] == 'decayIn') {
					//decayIn could be "1 week", "20 minutes". This is added to the current time to create a timestamp.
					$now = date("Y-m-d H:i:s");
					$date_decay = "'" . clean_data(date('Y-m-d H:i:s',strtotime("+" . $tag[1],strtotime($now)))) . "'";
				}
				
				if($tag[0] == 'decayTime') {
					//Or an absolute date/time string passed in
					$date_decay = "'" . clean_data(date('Y-m-d H:i:s',$tag[1])) . "'";
				}
			}
			
		}
		
		if(isset($_REQUEST['date-owner-start'])) {
			$timestamp = strtotime($_REQUEST['date-owner-start']);
			$start = date("Y-m-d H:i:s", $timestamp);
		
		} else {
			$start = date("Y-m-d H:i:s");
		}
			
		$sql = "INSERT INTO tbl_layer (
			  enm_access,
			  passcode,
			  int_group_id,
			  var_public_code,
			  var_title,
			  date_to_decay,
			  date_owner_start)
			  VALUES (
			  	'". clean_data($status) . "',
			  	'" . md5($passcode) . "',
			  	" . clean_data($group_id) . ",
			  	" . clean_data($public_passcode) . ",
			  	" . $title . ",
			  	" . $date_decay . ", NOW())";
		dbquery($sql) or die("Unable to execute query $sql " . dberror());	  	 
	
		return db_insert_id();
	}

	public function getFakeIpAddr()
	{
		//This is used now as the primary way to differentiate users
		$name = session_id();
		$ip = "192." . ord($name[0]) . '.' . ord($name[1]) . '.' . ord($name[2]);
	    return $ip; 
	
	
	}

	public function getRealIpAddr()
	{
		global $cnf;
		
		//Put all our proxy and servers in here
		//so that we don't ever return our own ip
	     $proxy_whitelisted = array();
	     
	     for($cnt = 0; $cnt< count($cnf['ips']); $cnt ++) {
	       $proxy_whitelisted[] = $cnf['ips'][$cnt];
	     }
	     
	     for($cnt = 0; $cnt< count($cnf['loadbalancer']['ips']); $cnt ++) {
	       $proxy_whitelisted[] = $cnf['loadbalancer']['ips'][$cnt];
	     }
	 
	 
		//Check if ip from session - early out
		if($_SESSION['user-ip']) {		
			return $_SESSION['user-ip'];
		}
		
		//Otherwise check from the various server methods
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	    {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    elseif (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    }
	    else
	    {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    $ips = explode(",", $ip);
	    
	    if(in_array($ips[0], $proxy_whitelisted)) {
	       //Try the end of the array
	        
	       if(in_array(end($ips), $proxy_whitelisted)) {
	          //Failed finding an ip address. NULL or a made up one?
	          //Make up an ip starting with 192 trying to be 
	          //fairly unique. TODO make this a genuinely
	          //unique uuid and split off from ip address.
	          // at the moment use a month and user agent
	          // differentiator. 
	          $us = md5($_SERVER['HTTP_USER_AGENT'] . date('Ym'));
	          $ip = "192." . ord($us[0]) . '.' . ord($us[1]) . '.' . ord($us[2]);
	          return $ip; 
	       } else {
	          return end($ips);
	       }
	    }
	    
	    return $ips[0];
	}
	
	
	public function get_remote($request, $timeout = 500)
	{
	
		//Asyncronously call our sms
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);  //always create new connection
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //return result
		
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	
	
	}
	
	public function get_remote_ssl($request, $timeout = 2000)
	{
		global $cnf;
		
		//Asyncronously call url
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	
	
      // Turn on SSL certificate verfication
      if($cnf['caPath']) {
      	$ca_path = $cnf['caPath'];
      } else {
      	$ca_path = "/etc/apache2/ssl/ca.pem";		//The default
      }
      curl_setopt($curl, CURLOPT_CAPATH, $ca_path);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);

	  $result = curl_exec($ch);
	  curl_close($ch);
	  return $result;
	
	
	}

	
	public function just_sent_message($layer_id, $just_sent_message_id, $mins = '05')
	{
		//Input mins as a string from 00 to 59
		global $server_timezone;
		
		
		if($this->always_send_email == true) {
			//An override to say always send an email e.g. from within shortmail which requires every email to be sent
			return false;
		}

		date_default_timezone_set($server_timezone);	 //UTC , TODO: global server_timezone??
		$sql = "SELECT * FROM tbl_ssshout WHERE int_layer_id = " . $layer_id . " AND TIMEDIFF(NOW(),date_when_shouted) < '00:" . $mins . ":00' AND int_ssshout_id <> $just_sent_message_id ORDER BY date_when_shouted DESC LIMIT 1";
		
		
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result)) {
			//Just sent an sms in the last 5 minutes
		 	return true;
		} else {
				return false;
		}
		
		return false;

	}
	
	
	//Group send capability
	public function notify_group($layer_id, $message, $message_id, $message_sender_user_id)
	{
		global $root_server_url;
		global $local_server_path;
		global $notify;
		global $staging;
		global $msg;
		global $lang;
		global $cnf;
				
		$sh = new cls_ssshout();
		$data = array(); 
		$cnt = 0;
		
		//Make sure we have the layer name
		if(isset($_REQUEST['passcode'])) {
			$layer_name = $_REQUEST['passcode'];
		}
		 
	
		//Notify each member of the group
		$sql = "SELECT * FROM tbl_layer_subscription l LEFT JOIN tbl_user u ON l.int_user_id = u.int_user_id WHERE l.enm_active = 'active' AND int_layer_id = " . $layer_id;
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		while($row = db_fetch_array($result)) {
			//Don't want to send a message we've sent to ourselves (wastes email and sms)
						
			if($cnt == 0) {
				//Init a message for notification - only on the first run through
				
				$message_details = array("observe_message" => $msg['msgs'][$lang]['observeMessage'],
										 "observe_url" => cur_page_url(),
										 "forum_message" => $msg['msgs'][$lang]['layerName'],
										 "forum_name" => $layer_name,
										 "remove_message" => $msg['msgs'][$lang]['removeComment'],
										 "remove_url" => $root_server_url . "/de.php?mid=" . $message_id . "&passcode=" . $layer_name);
				
				list($ret, $data) = $sh->call_plugins_notify("init", $message, $message_details, $message_id, $message_sender_user_id, null, $data);
			}
			

			
			//Always notify by email (if we don't have notifications enabled on our phone app - so that a delete can be clicked

			list($with_app, $data) = $sh->call_plugins_notify("addrecipient", $message, $message_details, $message_id, $message_sender_user_id, $row['int_user_id'], $data);
			if($with_app == false) {
				
				if($row['int_user_id']) {
					if($row['int_user_id'] != $message_sender_user_id) {		//Don't email to yourself
						$this->notify_by_email($row['int_user_id'], $message, $message_id, true, $layer_id);		//true defaults to admin user 
					}
				}
			}
					
			if($row['int_user_id'] != $message_sender_user_id) {		//Don't sms to yourself
			
				if($row['enm_sms'] == 'send') {
					//Also let user by know sms
					if($this->just_sent_message($layer_id, $message_id) == false) {
					
						//Asyncronously call our sms
						if($notify == true) {
						
						 //note: from user id is actually the recipient, it is their account we are reducing
							$cmd = "nohup nice -n 10 " . $cnf['phpPath'] . " " . $local_server_path . "send-sms.php phone=" . rawurlencode($row['var_phone']) . " message=" . rawurlencode($message . ' ' . cur_page_url()) . " user_from_id=" . $row['int_user_id'] . " staging=" . $staging . " > /dev/null 2>&1 &";	// . ' >/var/www/html/atomjump_staging/tmp/newlog.txt';
		
							array_push($process_parallel, $cmd);        //Store to be run by index.php at the end of everything else.
						
						}
						
					}
				}	
			}		//send to my own user if commented out.
			
			$cnt ++;		//increment so that we don't keep initing 
			
		
		}  //End while
	
	
	
		//Send off any/all plugin notifications together
		$sh->call_plugins_notify("send", $message, $message_details, $message_id, $message_sender_user_id, null, $data);
	
	}
	

	public function notify_by_email($user_id, $message, $message_id, $is_admin_user = false, $layer_id = null)
	{

		global $root_server_url;
		global $cnf;
		global $msg;
		global $lang;
	
		//Send a whisper to a recipient
		$sql = "SELECT * FROM tbl_user WHERE int_user_id = " . $user_id;
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
			$url = cur_page_url();
			$email_body = $message . "\n\n" . $msg['msgs'][$lang]['observeMessage'] . " <a href=\"$url\">$url</a>\n" . $msg['msgs'][$lang]['layerName'] . ": " . $this->layer_name;
			if($is_admin_user == true) {
				$url = $root_server_url . "/de.php?mid=" . $message_id . "&passcode=" . $this->layer_name;
				$email_body .= "\n\n" .$msg['msgs'][$lang]['removeComment'] . ": <a href=\"$url\">$url</a>";
			}
		
		    if($row['var_email'] != $cnf['email']['noReplyEmail']) {     //prevent endless mail loops
		    	$send_message = false;
		    	
		    	
		    	
		    	if($layer_id) {
					//This is on a particular layer - only send messages if they're after 20 minutes, so that we don't get a flurry of emails per message.
					if($this->just_sent_message($layer_id, $message_id, '20') == false) {
						$send_message = true;
					}
				} else {
					//No layer id specified - always email the message
					$send_message = true;
				
				}		    	
		    	
		    	
		    	if($send_message == true) {
			    	cc_mail($row['var_email'], $msg['msgs'][$lang]['newMsg'] . " " . cur_page_url(), $email_body, $cnf['email']['noReplyEmail']);
			    }
		    
			    
		    }
		
		}
	
	}
	
	
	//SMS Functions
	public function sms($phone_to, $message, $user_from_id)
	{
	  global $cnf;
	  global $msg;
	  global $lang;
	  
		//Wrapper
		$phone_to = "+" . $phone_to;
		
		
		if($cnf['sms']['use'] == "twilioSMS") {
		
			try {
		
			
		
				// this line loads the library 
				require('vendor/twilio/Services/Twilio.php'); 
			 
				$account_sid = $cnf['sms']['vendor']['twilioSMS']['accountSid']; 
				$auth_token = $cnf['sms']['vendor']['twilioSMS']['authToken'];
		
	
				$client = new Services_Twilio($account_sid, $auth_token); 
			 
				$client->account->messages->sendMessage(
					$cnf['sms']['vendor']['twilioSMS']['fromNum'],
					$phone_to, 
					$message 
				);
		
		
				//Reduce the user's balance by a certain amount (cost 5p from supplier)
				$sql = "UPDATE tbl_user SET dec_balance = dec_balance - " . CUSTOMER_PRICE_PER_SMS_US_DOLLARS . " WHERE int_user_id = " . $user_from_id;
				dbquery($sql) or die("Unable to execute query $sql " . dberror());	  	 
	
			} catch (Exception $e) {
				
				error_log($e->getMessage());
			}
		}
		
		return false;
	}
	
	public function international_phone_number($phone,$country_code)
	{
		//Phone is all sorts of dodgy formats
		if(($country_code == 'GB')) {
			//Currently only support UK mobiles
			//Get rid of any non digits
			$phone = preg_replace('/\D/','',$phone);
		
			//Count how many digits we have
			//07538069877
			//447538069877   [tick]
			//00447538069877
			if(strlen($phone) == 11) {
				//Chop the first 0 character and add 44
				$phone = "44" . substr($phone, 1);
			}
			
			if(strlen($phone) == 14) {
				//Chop the first two 00s
				$phone = substr($phone, 2);
			}
		
			return $phone;
		} else {
	
			return NULL;
		}
	}
	
	public function push_layer_granted($new_layer_granted) 
	{
		//Adds to a session array of access-granted arrays
		$layers_granted_array = json_decode($_SESSION['access-layers-granted']);
		if(!is_array($layers_granted_array)) $layers_granted_array = array();
		array_push($layers_granted_array, $new_layer_granted);
		$_SESSION['access-layers-granted'] = json_encode($layers_granted_array);
	}

	public function is_layer_granted($check_layer) 
	{
		//Returns true or false
		$layers_granted_array = json_decode($_SESSION['access-layers-granted']);
		if(!is_array($layers_granted_array)) return false;
		return in_array($check_layer, $layers_granted_array);
	}
	

}



class cls_login
{
	public function get_usercode()
	{
		//Get a usercode for display
		$ly = new cls_layer(); 
		$ip = $ly->getFakeIpAddr();  //get new user's ip address
		
		$subscription_string = $this->get_subscription_string();
		$subscriber_array = explode(",", $subscription_string);
		if($subscriber_array[0]) {
			$subscriber_count = count($subscriber_array);
		} else {
			$subscriber_count = "[NA]";
		}
	
		return array("thisUser" => $ip . ":" . $_SESSION['logged-user'],
					"layerUsers" => $subscription_string,
					"layerUserCount" => $subscriber_count);
	}
	
	
	public function check_group_intact($user_group, $layer_id)		
	{
		//Keeps the database in sync with what the webmaster has set in terms of users who listen into the group. 
		$in_db = array();
	
		$sql = "SELECT * FROM tbl_layer_subscription l WHERE int_layer_id = " . $layer_id;
		
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		while($row = db_fetch_array($result)) {
			$in_db[] = $row['int_user_id'];

		}
		
		
		
		//For each item in the correct group
		foreach($user_group as $correct_user => $correct_sms) {
			if(in_array($correct_user, $in_db)) {
				//In the group - keep in

				//Update the sms status - note possibly too many update queries here
				$sql = "UPDATE tbl_layer_subscription SET enm_sms = '" .  clean_data($correct_sms) . "', enm_active = 'active' WHERE int_user_id = " . $correct_user . " AND int_layer_id = " . $layer_id;
				$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());

			} else {
				
				//Add into the db
				$sql = "INSERT INTO tbl_layer_subscription (int_layer_id, int_user_id, enm_active, enm_sms) VALUES ( " . clean_data($layer_id) . ", " . $correct_user . ", 'active', '" . clean_data($correct_sms) . "')";
				$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			}
			
			
		}
		
		
		//Now see if anyone is in the group who shouldn't be in there - deactivate
		foreach($in_db as $user_in) {
			if(isset($user_group[$user_in])) {
			
				//Already in db, and in webmaster's version
				
				
				//Always update the sms status with latest - note this could result in too many queries?
				$sql = "UPDATE tbl_layer_subscription SET enm_sms = '" .  clean_data($user_group[$user_in]) . "' WHERE int_user_id = " . $user_in . " AND int_layer_id = " . $layer_id;
				$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			} else {
				//Remove from the db
				$sql = "UPDATE tbl_layer_subscription SET enm_active = 'inactive' WHERE int_layer_id = $layer_id AND int_user_id = " . $user_in;
				$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			
			}
		
		}
		
		if(sizeof($user_group) == 0) {
			//No owners of the group
			$sql = "UPDATE tbl_layer_subscription SET enm_active = 'inactive' WHERE int_layer_id = " . $layer_id;
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		
		}
		
			
		return $str;
		
	
	}
	
	public function remove_from_subscriptions($current_subs, $remove_user_id = null)
	{	
		//Take an existing string with all users e.g. 1.1.1.1:145:sms,2.2.2.2:32:sms,test@atomjump.com
		//and remove the current user from the subscriptions list
		if(!$remove_user_id) {
			$remove_user_id = $_SESSION['logged-user'];
		}
		
		$sh = new cls_ssshout(); 
		
		//Check the default site whispering
		$whisper_to_site_group = explode(",",$current_subs);
		$group_user_ids = array();
		
		$updated = false;
		$new_subs = "";
		
		//Search through existing users
		foreach($whisper_to_site_group as $user_machine) {
			//Check if this is an email address
			if(filter_var(trim($user_machine), FILTER_VALIDATE_EMAIL) == true) {
				//Convert user entered email into a user id
				$email = trim($user_machine);
				$ly = new cls_layer();
				$ip = $ly->getFakeIpAddr();
				$user_id = $sh->new_user($email, $ip, null, true);
				$user_machine = $ip . ":" . $user_id;
				
			} else {
			
				$whisper_to_divided = explode(":",$user_machine);
				$user_id = $whisper_to_divided[1];
			}
			
			if($remove_user_id == $user_id) {
				//Don't append this user to the list
				$updated = true;
			} else {
				//Append this user to the string
				if(!$new_subs) {
					//First on list
					$new_subs = $new_subs . $user_machine;
				} else {
					$new_subs = $new_subs . "," . $user_machine;
				}
			}
		}	
		
		if($updated == true) {
					
			//And resave the subscriptions
			$this->update_subscriptions($new_subs, $layer);
		}		
		
		return $new_subs;
	
	}
	
	public function add_to_subscriptions($current_subs, $layer = null, $new_user_id = null, $new_email = null)
	{
	
		//Take an existing string with all users e.g. 1.1.1.1:145:sms,2.2.2.2:32:sms,test@atomjump.com
		//and add the current user to the subscriptions list
		if(!$new_user_id) {
			$new_user_id = $_SESSION['logged-user'];
		}
		$ly = new cls_layer(); 
		$ip = $ly->getFakeIpAddr();  //get new user's ip address
		
	
		
		$new_user_machine = $ip . ":" . $new_user_id;
		
		$sh = new cls_ssshout(); 
		
		//Check the default site whispering
		$whisper_to_site_group = explode(",",$current_subs);
		$group_user_ids = array();
		$user_already_subscribed = false;
		
		
		
		//Check that this current user's email has the correct domain e.g. ...@thisdomain.com to be able to be added 
		//Get details of layer for any domain limits on new users
		if(!$layer) {
			if($_SESSION['authenticated-layer']) {
				$layer = $_SESSION['authenticated-layer'];
			} else {
				//No authenticated layer
				return false;		//TODO: Should this be limited here?
			}
		}
	
		if($layer) {
			$sql = "SELECT var_subscribers_limit FROM tbl_layer WHERE int_layer_id = " . $layer;
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			if($row = db_fetch_array($result))
			{
			
				if((isset($row['var_subscribers_limit'])) && ($row['var_subscribers_limit'] != "")) {
					//There is a limit on who can subscribe to this forum
					if($new_email) {
						$email_components = explode("@", $new_email);
					} else {
						$email_components = explode("@", $_SESSION['logged-email']);
					}
					if(($email_components[1]) && ($email_components[1] === $row['var_subscribers_limit'])) {
						//This is allowable - it is of the correct domain
					} else {
						//Use cannot be added - return last state early
						return false;
					}	
				}
			} else {
				//If we don't have a db connection we should also return
				return false;
			}
		}
		
		//Search through existing users
		foreach($whisper_to_site_group as $user_machine) {
			//Check if this is an email address
			if(filter_var(trim($user_machine), FILTER_VALIDATE_EMAIL) == true) {
				//Convert user entered email into a user id
				$email = trim($user_machine);
				$ly = new cls_layer();
				$ip = $ly->getFakeIpAddr();
				
				$user_id = $sh->new_user($email, $ip, null, true);
				$user_machine = $ip . ":" . $user_id;
				
			} else {
			
				$whisper_to_divided = explode(":",$user_machine);
				$user_id = $whisper_to_divided[1];
			}
			
			if($new_user_machine == $user_id) {
				$user_already_subscribed = true;
			}
		}	
		
		if($user_already_subscribed == false) {
			//Append to the string
			if($current_subs != "") {
				$current_subs = $current_subs . "," . $new_user_machine;
			} else {
				$current_subs = $new_user_machine;
			}
			
			//And resave the subscriptions
			$this->update_subscriptions($current_subs, $layer);
		}		
		
		return $current_subs;
	
	}
	
	public function update_subscriptions($whisper_site, $layer = null)
	{
		if(!$layer) {
			if($_SESSION['authenticated-layer']) {
				$layer = $_SESSION['authenticated-layer'];
			} else {
				return false;
			}
		}
	
		//Input is a string with all users  1.1.1.1:145:sms,2.2.2.2:32:sms in group
		// Or user entered emails: test@atomjump.com,hello@atomjump.com
		
		$sh = new cls_ssshout(); 
		
		//Check the default site whispering
		$whisper_to_site_group = explode(",",$whisper_site);
		$group_user_ids = array();
		foreach($whisper_to_site_group as $user_machine) {
			//Check if this is an email address
			if(filter_var(trim($user_machine), FILTER_VALIDATE_EMAIL) == true) {
				//Convert user entered email into a user id
				$email = trim($user_machine);
				$ly = new cls_layer();
				$ip = $ly->getFakeIpAddr();
				$user_id = $sh->new_user($email, $ip, null, true);
				$user_machine = $ip . ":" . $user_id;
				
			}
			
			$whisper_to_divided = explode(":",$user_machine);
			if(($whisper_to_divided[1] == '')||($whisper_to_divided[1] == 0)) {
				//Pass on this one
			} else {
				if($whisper_to_divided[2] == "sms") {
					$sms = "send";
				} else {
					$sms = "none";
				}
				$group_user_ids[$whisper_to_divided[1]] = $sms;		//2 is the sms
			}
			
			
		}		
		
		if($whisper_to_site_group[0]) {
			//Yes, our default site whispering is set
			//Check each of the users is in the db for this layer - don't do in the public sense. Note: because we only send through group details when sending
			$this->check_group_intact($group_user_ids, $layer);
		
		} else {
			//A blank 
			$this->check_group_intact(array(), $layer);
		}
		
		return;
	}
	
	public function get_subscription_string($layer_id = null)
	{
		
		if(!$layer_id) {
			if($_SESSION['authenticated-layer']) {
				$layer_id = $_SESSION['authenticated-layer'];
			} else {
				return "";
			}
		}
	
		//Output string is a string with all users in group eg. 1.1.1.1:145:sms,2.2.2.2:32:sms
		$output = "";
		$cnt = 0;
		
		$sql = "SELECT *,  ls.int_user_id AS ls_user_id  FROM tbl_layer_subscription ls LEFT JOIN tbl_user u ON ls.int_user_id = u.int_user_id WHERE enm_active = 'active' AND int_layer_id = " . $layer_id;
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		while($row = db_fetch_array($result))
		{
			if($cnt != 0) $output .= ",";
			$output .= $row['var_last_ip'] . ":" . $row['ls_user_id'];
			if($row['enm_sms'] == 'send') {
				$output .= ":sms";
			
			}
			$cnt ++;
		}
				
		return $output;
	
	}
	
				
	public function is_owner($user_id, $group_user_id, $layer_id)
	{
		//Returns true if this user is an owner of the group
		if($user_id && $layer_id) {
			if(!$group_user_id) {
				$group_user_id = $user_id;
			}
			$sql = "SELECT * FROM tbl_layer_subscription WHERE int_layer_id = " . $layer_id . " AND enm_active = 'active' AND (int_user_id = " . $user_id . " OR int_user_id = " . $group_user_id . ")"; 
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			if($row = db_fetch_array($result))
			{
				return true;
			} else {
			
				return false;
			}		
		} else {
			return false;
		}	
	}
	
	public function is_admin($user_id)
	{
		global $cnf;
		//Check this user is the admin user. Return true if so, and false if not
		
		$admin_machine_user = $cnf['adminMachineUser'];
		$admin_parts = explode(":", $admin_machine_user);
		
		//Returns true if this user is the system admin
		if(($user_id) && ($user_id === $admin_parts[1])) {
			return true;
		} else {
			return false;
		}
	}
	
	public function get_group_user($layer_id = null)
	{
		if(!$layer_id) {
			if($_SESSION['authenticated-layer']) {
				$layer_id = $_SESSION['authenticated-layer'];
			} else {
				return false;
			
			}
			
		}
		
		//For layers with multiple owner users, there is an associated group id.  This is used to view the group private messages
		$group_user_id = false;
	
		if(isset($_SESSION['logged-user'])) {
			$sql = "SELECT int_group_id FROM tbl_layer WHERE int_layer_id = " . $layer_id;
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			if($row = db_fetch_array($result))
			{
				if($row['int_group_id']) {
					$group_user_id = $row['int_group_id'];
					$_SESSION['layer-group-user'] = $group_user_id;
					
					//There is a group of more than 1 user, check if it is the same as our self
					$sql = "SELECT * FROM tbl_layer_subscription WHERE int_layer_id = " . $layer_id . " AND enm_active = 'active' AND int_user_id = " . $_SESSION['logged-user']; 
					$resultb = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
					if($rowb = db_fetch_array($resultb))
					{
						//Yep this user is correct - set the session to view messages from this user also
						$_SESSION['logged-group-user'] = $_SESSION['layer-group-user'];	
					} else {
					
						//Otherwise ensure not logged 
						$_SESSION['logged-group-user'] = '';
					
					}
					
				} else {
					//OK there is no group user yet. Create a new user on the main list, who can see all the private messages on the layer
					
					//Check if there is more than one subscriber to the group now. If so, add an overriding group user.
					$sql = "SELECT COUNT(*) AS count_in_group FROM tbl_layer_subscription WHERE int_layer_id = " . $layer_id;
					$resultb = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
					if($rowb = db_fetch_array($resultb))
					{
					
						if($rowb['count_in_group'] > 1) {		//There is a new group, more than one private user
							$sql = "INSERT INTO tbl_user(var_last_ip, var_email, var_phone, date_created) VALUES ('1.1.1.1', NULL,NULL, NOW())";
							$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
				
							$group_user_id = db_insert_id();
							$_SESSION['logged-group-user']  = $group_user_id;
							$_SESSION['layer-group-user'] = $group_user_id;		
							
							//Update 
							$sql = "UPDATE tbl_layer SET int_group_id = " . $group_user_id . " WHERE int_layer_id = " . $layer_id;
							$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
						} else {
						    //Otherwise ensure not logged 
							$_SESSION['logged-group-user'] = '';

						}
					}
				
				}
			} else {
				//No group user - likely to be one owner of layer			
			
			}
		
		}
		
		return $group_user_id;
	
	}


    public function save_plugin_settings($user_id, $full_request, $type = "SAVE", $allowed_plugins = null)
    {
        //$type = SAVE or NEW
        global $cnf;
        global $local_server_path;
	    
	    if($allowed_plugins != null) {
	        //OK we have an array of allowed plugins
	        $plugins = $allowed_plugins;
	    } else {
	        //Otherwise, assume all plugins in the global config
	        $plugins = $cnf['plugins'];
	    }
	    
	    $reloadScreen = false;
	    	    
	    //Loop through each class and call each plugin_* -> on_message() function
	    for($cnt=0; $cnt < count($plugins); $cnt++) {
	        $plugin_name = $plugins[$cnt];
	        
	       
	        include_once($local_server_path . "plugins/" . $plugin_name . "/index.php");
	        $class_name = "plugin_" . $plugin_name;
	        
	        $pg = new $class_name();
	        
	        if(method_exists($pg,"on_save_settings") == true) {
	            //OK call the on_settings function of the plugin
	            $returns = $pg->on_save_settings($user_id, $full_request, $type);
	        
	            
	            if(strcmp($returns, "RELOAD") == 0) {
	                $reloadScreen = true;
	            }
	        } else {
	            //No on_save_settings() in plugin - do nothing

	        }
	    }
	    if($reloadScreen === true) {
	        return "RELOAD";        //This option reloads the entire frame e.g. for a language change
	    } else {
	        return true;
        }
    
    }
    
    

    


	public function confirm($email, $password, $phone, $users = null, $layer_visible = null, $readonly = false, $full_request)
	{
		
		//Returns: [string with status],[RELOAD option - must be RELOAD],[user id] 
		//
		//user_id has been added for the app, which doesn't have sessions as such.
		//Note: if RELOAD doesn't exist, user_id may be in 2nd place
		
		//Check if this is a request to get access to a password protected forum
	    $forum_accessed = false;
	    $new_user = false;
	    
	   
	    
	   
	    $ly = new cls_layer(); 
	    $layer_info = $ly->get_layer_id($layer_visible);
	    if((isset($full_request['forumpasscheck']))&&($full_request['forumpasscheck'] != "")) {
	    
	    	//This is after a password has been entered for the forum
			if((!isset($_SESSION['logged-user']))||($_SESSION['logged-user'] == "")) {
				//We are a new user
				$ip = $ly->getFakeIpAddr();  //get new user's ip address	
			
				$sh = new cls_ssshout();
			
				$user_id = $sh->new_user($email, $ip, null, false);		//Don't actually login as this user
				
				
			}
			
			error_log("Email: " . $email . " IP:" . $ip . " user_id:" . $user_id);		//TESTING
	    	
	    	 //Check if we are the admin user - in this case we will log in automatically,
	    	 //which allows us to change the group password.
			$sql = "SELECT * FROM tbl_user WHERE var_email = '" . clean_data($email) . "'";
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			if(($row = db_fetch_array($result))&&($email != ""))
			{
				//Email exists
				if($this->is_admin($row['int_user_id']) == true) {
					$user_id = $row['int_user_id'];
					//Compare password with existing user password
					if(md5($password) == $row['var_pass']) {
						//Log in to the forum automatically
						$_SESSION['access-layer-granted'] = $layer_info['int_layer_id']; 
						$ly->push_layer_granted($layer_info['int_layer_id']);
						$_SESSION['authenticated-layer'] = $layer_info['int_layer_id'];
						
						if(($email != "")&&($password != "")) {
							//Continue with current user and fully login, but also refresh
							$_SESSION['logged-user'] = $user_id;
							$_SESSION['logged-email'] = clean_data($email);	
							return "FORUM_LOGGED_IN,RELOAD";
						} else {
							//Refresh the page and reload
							
							//Confirm this new blank user
							$_SESSION['logged-user'] = $user_id;
							$_SESSION['logged-email'] = clean_data($email);			//This is here to confirm the email matches the logged in
							
							return "FORUM_LOGGED_IN,RELOAD";
						} 		
					}
				}
				
			}
			
			
	    	
	    	
				    
	    
	    	
	    	
			$layer_info = $ly->get_layer_id($layer_visible);
			
			error_log("Layer id:" . $layer_info['int_layer_id']);		//TESTING
			if($layer_info) {
					//Yes the layer exists
					
					if(md5(clean_data($full_request['forumpasscheck'])) == $layer_info['var_public_code']) {
						
						//And it is the correct password! Continue below with a login
						$_SESSION['access-layer-granted'] = $layer_info['int_layer_id']; 
						$ly->push_layer_granted($layer_info['int_layer_id']);
						$_SESSION['authenticated-layer'] = $layer_info['int_layer_id'];
						
						error_log("Set access granted to " . $layer_info['int_layer_id'] . " Access layer granted: " . $_SESSION['access-layer-granted']);		//TESTING
						
						if(($email != "")&&($password != "")) {
							//Continue with current user and fully login, but also refresh
							
							$_SESSION['logged-user'] = $user_id;
							$_SESSION['logged-email'] = clean_data($email);
							$reload = ",RELOAD";
							
							error_log("Correct password, blank email. Reload = " . $reload);
						} else {
							//Refresh the page and reload
							
							//Confirm this new blank user
							$_SESSION['logged-user'] = $user_id;
							$_SESSION['logged-email'] = "";						
													
							error_log("Returning " . $layer_info['int_layer_id'] . " Access layer granted: " . $_SESSION['access-layer-granted']);		//TESTING
							
							return "FORUM_LOGGED_IN,RELOAD";
						} 
						  	
					} else {
						//Sorry, this was the wrong password
						return "FORUM_INCORRECT_PASS";		
				
					}
			} else {
				//Sorry, this was the wrong password
				return "FORUM_INCORRECT_PASS";	
			}
	    
	    }
	    
	    //Do a check on the layer access being granted.
	    if(isset($layer_info['var_public_code'])) {
	    	//Check this is a valid layer
	    	$layer_info = $ly->get_layer_id($layer_visible);
	    	
		    if(($_SESSION['access-layer-granted'] == 'true')||($_SESSION['access-layer-granted'] == $layer_info['int_layer_id'])||($ly->is_layer_granted($layer_info['int_layer_id']))) {
	    		//All good to continue...
	    		
	    	} else {
	    		//Sorry, the forum password hasn't been set
				return "FORUM_INCORRECT_PASS,RELOAD";
	    	}
	    	
	    } else {
	    	//There is no forum password. Access layer granted by default
	    	$layer_info = $ly->get_layer_id($layer_visible);
	    	$_SESSION['access-layer-granted'] = $layer_info['int_layer_id'];
	    	$_SESSION['authenticated-layer'] = $layer_info['int_layer_id'];
	    }
	    
	    //Check if this is saving the passcode - we need to be a sysadmin user to do this.
	    if(isset($full_request['setforumpassword'])&&($full_request['setforumpassword'] != "")&&($this->is_admin($_SESSION['logged-user']) == true)) {
    
	    	$ly = new cls_layer();
			$layer_info = $ly->get_layer_id($layer_visible);
			if($layer_info) {
	    				
				//No password protection already - set it in this case
				$sql = "UPDATE tbl_layer SET var_public_code = '" . md5(clean_data($full_request['setforumpassword'])) . "' WHERE int_layer_id = " . $layer_info['int_layer_id'];
				dbquery($sql) or die("Unable to execute query $sql " . dberror());
			}
		}
		
		//Check if this is saving the title - we need to be a sysadmin user to do this.
	    if(isset($full_request['setforumtitle'])&&($full_request['setforumtitle'] != "")&&($this->is_admin($_SESSION['logged-user']) == true)) {
    
	    	$ly = new cls_layer();
			$layer_info = $ly->get_layer_id($layer_visible);
			if($layer_info) {
	    				
				//No password protection already - set it in this case
				$sql = "UPDATE tbl_layer SET var_title = '" . clean_data($full_request['setforumtitle']) . "' WHERE int_layer_id = " . $layer_info['int_layer_id'];
				dbquery($sql) or die("Unable to execute query $sql " . dberror());
				$reload = ",RELOAD";		//Reload the new forum title
			}
		}
		
		
		
		//Check if this is saving a domain limitation - update this always
	    if(isset($full_request['subscriberlimit'])&&($this->is_admin($_SESSION['logged-user']) == true)) {
	    	$ly = new cls_layer();
			$layer_info = $ly->get_layer_id($layer_visible);
			if($layer_info) {
	    				
				//Set a domain limitation on email addresses of subscribers
				$sql = "UPDATE tbl_layer SET var_subscribers_limit = '" . clean_data($full_request['subscriberlimit']) . "' WHERE int_layer_id = " . $layer_info['int_layer_id'];
				dbquery($sql) or die("Unable to execute query $sql " . dberror());
			}	
	    
	   	}
	    
	    //Get the current layer - use to view 
	    if(($email != "")&&($password == "")&&(isset($full_request['email_modified']))&&($full_request['email_modified'] != "false")) {
	    	//This is a subscription case: an email has been entered, but no password.
	    	error_log("Subscription case");   //TESTING
	    	
	    	$ly = new cls_layer(); 
			$ip = $ly->getFakeIpAddr();  //get new user's ip address	
				
			$sh = new cls_ssshout();
				
			$saved_auth_layer = $_SESSION['access-layer-granted'];		//Save any authenticated sessions
			$user_id = $sh->new_user($email, $ip, null, false);		//But don't actually login as this user (since we don't have a password)
																	//otherwise you could simply login as another user by entering
																	//no password but their email
	    	$_SESSION['access-layer-granted'] = $saved_auth_layer;		//Get it back - saves entering it twice for the user, if a new user is created.
	    	
				
			//Check we're authorised to this layer if it has a password
			$layer_info = $ly->get_layer_id($layer_visible);
			
			
			if($layer_info['var_public_code']) {
				if($_SESSION['access-layer-granted']) {
						if(($_SESSION['access-layer-granted'] != $layer_info['int_layer_id'])&&(!$ly->is_layer_granted($layer_info['int_layer_id']))) {
							//Go back and get a password off the user.
							return "FORUM_INCORRECT_PASS,RELOAD";  
						}
				}
			}
			
			//Make sure we have the forum right password, if it exists
			if($layer_info) {
				//Yes the layer exists. Add ourselves to the subscription list.
				$current_subs = $this->get_subscription_string($layer_info['int_layer_id']);
				
				$new_subs = $this->add_to_subscriptions($current_subs, $layer_info['int_layer_id'], null, $email);  
				if($new_subs === false) {
					return "SUBSCRIPTION_DENIED";
				}			
			}
			
			return "SUBSCRIBED";
	    
		} else {
		
			//A regular login attempt
			error_log("Regular login attempt");   //TESTING
		
			//First check if the email exists
			$sql = "SELECT * FROM tbl_user WHERE var_email = '" . clean_data($email) . "'";
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			if(($row = db_fetch_array($result))&&($email != ""))
			{
				//Email exists
				
				//Check if the password already exists
				$user_id = $row['int_user_id'];
				if($row['var_pass'] == NULL) {
					//No password already, so presumably we need to store it
					$sql = "UPDATE tbl_user SET var_pass = '" . md5(clean_data($password)) . "' WHERE int_user_id = " . $user_id;
					dbquery($sql) or die("Unable to execute query $sql " . dberror());
					
					//Update phone if necessary too
					if($phone != "") {
						//f($phone != "Your Phone") {
							$sql = "UPDATE tbl_user SET var_phone = " . clean_data($phone) . " WHERE int_user_id = " . $user_id;
							$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
					} else {
							//A blank phone - we want to remove any old phone number
							$sql = "UPDATE tbl_user SET var_phone = NULL WHERE int_user_id = " . $user_id;
							$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
						
						//}
					}
					
					//Set our session variable
					$_SESSION['logged-user'] = $user_id;
					$_SESSION['logged-email'] = clean_data($email);	  //This is here to confirm the email matches the logged in
				
					
					//Handle any plugin generated settings
			    	$returns = $this->save_plugin_settings($user_id, $full_request, "SAVE");
		            if(strcmp($returns, "RELOAD") == 0) {
		            	$reload = ",RELOAD";
		            }
					
					return "STORED_PASS" . $reload . "," .$user_id;
					
				} else {
					//A password already - compare with existing password
					if(md5($password) == $row['var_pass']) {
					
						//Yup, a match - lets sign us in
					
						
					
						$_SESSION['logged-user'] = $user_id;
						$_SESSION['logged-email'] = clean_data($email);			//This is here to confirm the email matches the logged in
						
						//Update phone if necessary too
						if($phone != "") {
							
								$sql = "UPDATE tbl_user SET var_phone = " . clean_data($phone) . " WHERE int_user_id = " . $user_id;
								$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
						} else {
								//A blank phone - we want to remove any old phone number
								$sql = "UPDATE tbl_user SET var_phone = NULL WHERE int_user_id = " . $user_id;
								$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());

							
						}
						
					
						//Handle any plugin generated settings
						$returns = $this->save_plugin_settings($user_id, $full_request, "SAVE");
						if(strcmp($returns, "RELOAD") == 0) {
							$reload = ",RELOAD";
				   
						}
											
						//Get the group user if necessary
						$this->get_group_user();
					
						//Update the group if necessary too 							
						if($_SESSION['logged-group-user'] == $_SESSION['layer-group-user']) {
							if($users) {
								
								if($this->is_admin($_SESSION['logged-user']) == true) {
									$this->update_subscriptions($users);
								}
							}
						}
						
						//Normal forum login
						error_log("Normal forum login: LOGGED_IN" . $reload . "," .$user_id);   //TESTING 
						return "LOGGED_IN" . $reload . "," .$user_id;  
						
						
					
					} else {
					
						//Incorrect password
						return "INCORRECT_PASS";
						
					}
				
				
				}
				
			} else {
				//Incorrect email - so, this is a new email, or a blank email 
				$ly = new cls_layer(); 
				$ip = $ly->getFakeIpAddr();  //get new user's ip address	
				
				$sh = new cls_ssshout();
				
				
				$user_id = $sh->new_user($email, $ip);		//Sends off confirmation email if it is different
						
			
				//No password already, so presumably we need to store it
				if($password) {
					$sql = "UPDATE tbl_user SET var_pass = '" . md5(clean_data($password)) . "' WHERE int_user_id = " . $user_id;
					dbquery($sql) or die("Unable to execute query $sql " . dberror());
				
					//Set our session variable
					$_SESSION['logged-user'] = $user_id;
					$_SESSION['logged-email'] = clean_data($email);			//This is here to confirm the email matches the logged in
						
				} 
				
				//Handle any plugin generated settings
				$returns = $this->save_plugin_settings($user_id, $full_request, "NEW");
				if(strcmp($returns, "RELOAD") == 0) {
		        		$reload = ",RELOAD";
		    		}
			
			
				return "NEW_USER" . $reload . "," .$user_id;
			}
		}
	}
	
	public function unsubscribe($user_id = null, $layer_visible = null)
	{
		//Unsubscribe a user from a layer. If user id is not specified, use the current user - note this has some security issues if you
		//can specify the current user
		$ly = new cls_layer(); 
		$layer_info = $ly->get_layer_id($layer_visible);
		if($layer_info) {
			//Yes the layer exists
			$current_subs = $this->get_subscription_string($layer_info['int_layer_id']);
			
			return $this->remove_from_subscriptions($current_subs, $user_id);	
		} else {
			return "FAILURE";
		}
	}	
	
	public function subscribe($user_id = null, $layer_visible = null, $forum_password)
	{
		//Subscribe a user from a layer. If user id is not specified, use the current user - note this has some security issues if you
		//can specify the current user
		$ly = new cls_layer(); 
		$layer_info = $ly->get_layer_id($layer_visible);
		if($layer_info) {
			//Yes the layer exists
			if($layer_info['var_public_code']) {
					if(isset($forum_password) && $forum_password != "") {
						if(md5(clean_data($forum_password)) != $layer_info['var_public_code']) {
							return "FAILURE";
						}
					}
					
					if($_SESSION['access-layer-granted']) {
						if(($_SESSION['access-layer-granted'] != $layer_info['int_layer_id'])&&(!$ly->is_layer_granted($layer_info['int_layer_id']))) {
							return "FAILURE";
						}
					}
			} 
			
			
			$current_subs = $this->get_subscription_string($layer_info['int_layer_id']);
			
			$new_subs = $this->add_to_subscriptions($current_subs, $layer_info['int_layer_id'], $user_id);	
			if($new_subs === false) {
				return "FAILURE";
			} else {
				return $new_subs;
			}
		} else {
			return "FAILURE";
		}
	}	

	
	public function email_confirm($code)
	{
		global $msg;
		global $lang;
	
		$sql = "SELECT * FROM tbl_user WHERE var_confirmcode = '" . clean_data($code) . "'";
		
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
	
			//This confirmcode exists
			//Update as confirmed
			$user_id = $row['int_user_id'];
			$sql = "UPDATE tbl_user SET enm_confirmed = 'confirmed', date_updated = NOW() WHERE int_user_id = " . $user_id;
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			return $msg['msgs'][$lang]['thanksConfirmEmail'];
 
		} else {
			return $msg['msgs'][$lang]['invalidEmailCode'];
		
		}
	
	}
			
	


}


?>
