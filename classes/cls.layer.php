<?php 

global $cfg;
global $msg;
global $lang;
define("CUSTOMER_PRICE_PER_SMS_US_DOLLARS", $cnf['USDollarsPerSMS']);  //$0.16 or 10p. When we are charged 5p per message.

class cls_layer
{

 public $layer_name;

	public function get_layer_id($passcode, $reading)
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
					//Yes, this layer needs access to be granted - set status to false until 
					$_SESSION['access-layer-granted'] = 'false';
				} else {
					$_SESSION['access-layer-granted'] = 'true';
				
				}
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
						
						return $row;
					}
				}
			} 
		}
	
		return false;

	}
	
	public function new_layer($passcode, $status, $group_id = 'NULL', $public_passcode = NULL)
	{
		if($public_passcode == NULL) {
			$public_passcode = "NULL";
		} else {
			$public_passcode = "'" . $public_passcode . "'";  //usually a text string except when null
		}
	
		$sql = "INSERT INTO tbl_layer (
			  enm_access,
			  passcode,
			  int_group_id,
			  var_public_code)
			  VALUES (
			  	'". clean_data($status) . "',
			  	'" . md5($passcode) . "',
			  	" . clean_data($group_id) . ",
			  	" . clean_data($public_passcode) . ")";
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
	
		//Asyncronously call url
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	
	
      // Turn on SSL certificate verfication
      curl_setopt($curl, CURLOPT_CAPATH, "/etc/apache2/ssl/ca.pem");
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);

	  $result = curl_exec($ch);
	  curl_close($ch);
	  return $result;
	
	
	}

	
	public function just_sent_sms($layer_id, $just_sent_message_id)
	{
		//TODO check indexes on this query
		global $server_timezone;

		date_default_timezone_set($server_timezone);	 //UTC , TODO: global server_timezone??
		$sql = "SELECT * FROM tbl_ssshout WHERE int_layer_id = " . $layer_id . " AND TIMEDIFF(NOW(),date_when_shouted) < '00:05:00' AND int_ssshout_id <> $just_sent_message_id ORDER BY int_ssshout_id DESC";
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
	
		//Notify each member of the group - note tbl_group 
		$sql = "SELECT * FROM tbl_layer_subscription l LEFT JOIN tbl_user u ON l.int_user_id = u.int_user_id WHERE l.enm_active = 'active' AND int_layer_id = " . $layer_id;
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		while($row = db_fetch_array($result)) {
			//Don't want to send a message we've sent to ourselves (wastes email and sms)
			
			//Always notify by email - so that a delete can be clicked
			$this->notify_by_email($row['int_user_id'], $message, $message_id, true);		//true defaults to admin user 
					
			if($row['int_user_id'] != $message_sender_user_id) {		//Don't sms to yourself
			
				if($row['enm_sms'] == 'send') {
					//Also let user by know sms TODO: modify message for sms?
					if($this->just_sent_sms($layer_id, $message_id) == false) {
					
						//Asyncronously call our sms
						if($notify == true) {
						
						 //note: from user id is actually the recipient, it is their account we are reducing
							$cmd = "nohup nice -n 10 /usr/bin/php  " . $local_server_path . "send-sms.php phone=" . rawurlencode($row['var_phone']) . " message=" . rawurlencode($message . ' ' . cur_page_url()) . " user_from_id=" . $row['int_user_id'] . " staging=" . $staging . " > /dev/null 2>&1 &";	// . ' >/var/www/html/atomjump_staging/tmp/newlog.txt';
		
		
							$output = shell_exec($cmd);
						
						}
						
					}
				}	
			}		//send to my own user if commented out
		
		}
	
	}
	

	public function notify_by_email($user_id, $message, $message_id, $is_admin_user = false)
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
				$url = $root_server_url . "/de.php?mid=" . $message_id;
				$email_body .= "\n\n" .$msg['msgs'][$lang]['removeComment'] . ": <a href=\"$url\">$url</a>";
			}
		
		    if($row['var_email'] != $cnf['noReplyEmail']) {     //prevent endless mail loops
			    cc_mail($row['var_email'], $msg['msgs'][$lang]['newMsg'] . " " . cur_page_url(), $email_body, $cnf['noReplyEmail']);
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
		
			
		
		try {
		
			// this line loads the library 
			require('vendor/twilio/Services/Twilio.php'); 
			 
			$account_sid = $cnf['twilioSMS']['accountSid']; 
			$auth_token = $cnf['twilioSMS']['authToken'];
		
	
			$client = new Services_Twilio($account_sid, $auth_token); 
			 
			$client->account->messages->sendMessage(
				$cnf['twilioSMS']['fromNum'],
				$phone_to, 
				$message 
			);
		
		
			//Reduce the user's balance by a certain amount (cost 5p from supplier)
			$sql = "UPDATE tbl_user SET dec_balance = dec_balance - " . CUSTOMER_PRICE_PER_SMS_US_DOLLARS . " WHERE int_user_id = " . $user_from_id;
			dbquery($sql) or die("Unable to execute query $sql " . dberror());	  	 
	
		} catch (Exception $e) {
				
			error_log($e->getMessage());
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
	
	
	

}



class cls_login
{
	public function get_usercode()
	{
		//Get a usercode for display
		$ly = new cls_layer(); 
		$ip = $ly->getFakeIpAddr();  //get new user's ip address
		return array("thisUser" => $ip . ":" . $_SESSION['logged-user'],
					"layerUsers" => $this->get_subscription_string());
	}
	
	
	public function check_group_intact($user_group, $layer_id)		
	{
		//Keeps the database in sync with what the webmaster has set in terms of users who listen into the group.  A future step might be to allow
		//external users to subscribe also
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
		
			
		return $str;
		
	
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
		
		$sh = new cls_ssshout(); 
		
		
		//Check the default site whispering
		$whisper_to_site_group = explode(",",$whisper_site);
		$group_user_ids = array();
		foreach($whisper_to_site_group as $user_machine) {
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
							//Update 
							$sql = "UPDATE tbl_layer SET int_group_id = " . $group_user_id . " WHERE int_layer_id = " . $layer_id;
							$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
						} else {
						   

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
	
		//Check if this is a request to get access to a password protected forum
	    $forum_accessed = false;
	    if(isset($full_request['forumpasscheck'])) {
	    
	    	$ly = new cls_layer();
			$layer_info = $ly->get_layer_id($layer_visible);
			if($layer_info) {
					//Yes the layer exists
					
					if(md5(clean_data($full_request['forumpasscheck'])) == $layer_info['var_public_code']) {
					
						//And it is the correct password! Continue below with a login
						$_SESSION['access-layer-granted'] = $layer_info['int_layer_id'];
						
						$_SESSION['authenticated-layer'] = $layer_info['int_layer_id'];
					
						return "FORUM_LOGGED_IN,RELOAD";
						  	
					} else {
						//Sorry, this was the wrong password
						return "INCORRECT_PASS";
				
					}
			} else {
				//Sorry, this was the wrong password
				return "INCORRECT_PASS";
			}
	    
	    }
	    
	    //Check if this is saving the passcode - we need to be a group owner to do this.
	    if(isset($full_request['setforumpassword'])&&($full_request['setforumpassword'] != "")) {
	    	$ly = new cls_layer();
			$layer_info = $ly->get_layer_id($layer_visible);
			if($layer_info) {
	    	
				if($layer_info['var_public_code'] == NULL) {
			
					//Only the owners can do this
					if($_SESSION['logged-group-user'] == $_SESSION['layer-group-user']) {		
							//No password protection already - set it in this case
							$sql = "UPDATE tbl_layer SET var_public_code = '" . md5(clean_data($full_request['setforumpassword'])) . "' WHERE int_layer_id = " . $layer_info['int_layer_id'];
							dbquery($sql) or die("Unable to execute query $sql " . dberror());
					}	
				}
			}
		}
	    
	    
	
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
				
				
				//Handle any plugin generated settings
	        	$returns = $this->save_plugin_settings($user_id, $full_request, "SAVE");
                if(strcmp($returns, "RELOAD") == 0) {
                	$reload = ",RELOAD";
                }
				
				return "STORED_PASS" . $reload;
				
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
					
					
					//Get the current layer - use to view 
					$ly = new cls_layer();
					$layer_info = $ly->get_layer_id($layer_visible);
					if($layer_info) {
						$_SESSION['authenticated-layer'] = $layer_info['int_layer_id'];
					}
					
					
					//Get the group user if necessary
					$this->get_group_user();
					
					//Update the group if necessary too 
					if($_SESSION['logged-group-user'] == $_SESSION['layer-group-user']) {
						if($users) {
							$this->update_subscriptions($users);
						}
					}
					
					
					//Handle any plugin generated settings
					$returns = $this->save_plugin_settings($user_id, $full_request, "SAVE");
					if(strcmp($returns, "RELOAD") == 0) {
						$reload = ",RELOAD";
			   
					}
				
				
					
					//Normal forum login
					return "LOGGED_IN" . $reload;  
				    
					
				
				} else {
				
					//Incorrect password
					return "INCORRECT_PASS";
					
				}
			
			
			}
			
		} else {
			//Incorrect email - so, this is a new user
			$ly = new cls_layer(); 
			$ip = $ly->getFakeIpAddr();  //get new user's ip address	
			
			$sh = new cls_ssshout();
			
			$user_id = $sh->new_user($email, $ip);		//Sends off confirmation email
			
			
			//No password already, so presumably we need to store it
			if($password) {
				$sql = "UPDATE tbl_user SET var_pass = '" . md5(clean_data($password)) . "' WHERE int_user_id = " . $user_id;
				dbquery($sql) or die("Unable to execute query $sql " . dberror());
			
				//Set our session variable
				$_SESSION['logged-user'] = $user_id;
			}
			
			//Handle any plugin generated settings
			$returns = $this->save_plugin_settings($user_id, $full_request, "NEW");
        		if(strcmp($returns, "RELOAD") == 0) {
                		$reload = ",RELOAD";
            		}
			
			
			return "NEW_USER" . $reload;
		}
	}
	
	public function email_confirm($code)
	{
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
