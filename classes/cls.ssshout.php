<?php 

class cls_ssshout
{


 public $layer_name;
 


	
	public function get_user_ip($user_id)
	{
		$sql = "SELECT * FROM tbl_user WHERE int_user_id = " . $user_id;
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
			return $row['var_last_ip'];
		} else {
			return false;
		
		}
			
	
	
	}
	
	
	public function clear_old_session_data()
	{
	
		//$_SESSION['logged-user']  - this shouldn't be here. This is the user id of the signed in user.
		$_SESSION['logged-email'] = '';			//Set on sign in. This is the email of the signed in user.
		$_SESSION['user-ip'] = '';				//The logged in user's artificial ip address.
		$_SESSION['temp-user-name'] = '';		//This username us used potentially before another name is set e.g. Anon 55
		$_SESSION['lat'] = '';					//User's latitude, blank until this is supported
		$_SESSION['lon'] = '';					//User's longitude, blank until this is spupported
		$_SESSION['logged-group-user'] = '';	//This means we are logged in to view messages from this group user, if the same as the layer-group-user then it will be for this layer. Blank if not authorised. 
		$_SESSION['layer-group-user'] = '';		//The group user for this layer.
		$_SESSION['access-layer-granted'] = 'false';   //Either 'false' or a layer id if we have access to this layer (multiple user access with a password).
	
		$_SESSION['view-count'] = 0;			//0 or 1 for the number of times this layer has been viewed. 	
	
	}

	
	public function new_user($email, $ip, $phone = NULL, $login_as = true)
	{
		//If login_as is false, we don't send a welcome email out to the user (it is a system-only request).
		global $root_server_url;
		global $cnf;
		global $msg;
		global $lang;
		global $db;
		
	 
	 

	 
	 
		//Returns user id - old one if a duplicate, new one if new
				
		$phone = clean_data($phone);
		if((is_null($phone))||($phone == '')) {
			$phone = "";
			$insert_phone = "NULL";
		} else {
			$insert_phone = "'" . $phone . "'";
		}
		
		if((is_null($email))||($email == '')) {
			 //This is likely a first request for a session - just check for users with no email
	         
	         	if((isset($_SESSION['logged-user']))&&($_SESSION['logged-user'] != '')) {
				    return $_SESSION['logged-user'];
             	} else {
			        //no existing row with this ip
			 		//Create a new 'temporary' user
					$sql = "INSERT INTO tbl_user(var_last_ip, var_email, var_phone, date_created) VALUES ('" . clean_data($ip) . "', NULL," . clean_data($insert_phone) . ", NOW())";
					$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
				
					if($login_as == true) {
						//Set the logged user to the db user id
						$_SESSION['logged-user'] = db_insert_id();
						$this->clear_old_session_data();
					}
				
					return db_insert_id();
			 
			}
		} else {
		    //Incoming email exists, check if on the db
			$sql = "SELECT * FROM tbl_user WHERE var_email = '" . $email . "'";
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			
			if($row = db_fetch_array($result))
			{
			 //existing email
				$cnt++;		//Count how many results we have from this email address
				//A duplicate
				if($login_as == true) {
					
					if((isset($_SESSION['logged-user']))&&($_SESSION['logged-user'] != '')) {

						  //Already know our user id
						  if($row['int_user_id'] != $_SESSION['logged-user']) {
							  //OK - we were logged in as logged-user, but now we need to switch
						   	$_SESSION['logged-user'] = $row['int_user_id'];
						   	$this->clear_old_session_data();
					
						  }
				
				 	} else {
				 		//Ok we need to set the temp user
				 		$_SESSION['logged-user'] = $row['int_user_id'];
				 		$this->clear_old_session_data();
				 		
				 	}
			
			
					//Just make sure that we have the current phone number
					if($insert_phone != "NULL") {
						$sql = "UPDATE tbl_user SET var_phone = " . clean_data($insert_phone) . " WHERE int_user_id = " . $row['int_user_id'];
						$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
					}
				}
						
				return $row['int_user_id'];
			
			
			} else {
			    //new email
				//A new user
				if(($email != '')&&(!is_null($email))) {
			
			
					$confirm_code = md5(uniqid(rand())); 
			
					$sql = "INSERT INTO tbl_user(var_last_ip, var_email, var_phone, var_confirmcode, date_created) VALUES ('" . clean_data($ip) . "', '" . clean_data($email) . "'," . clean_data($insert_phone) . ",'" . clean_data($confirm_code) . "', NOW())";
					$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			
					//Let the user confirm their email address
			
					//TODO: deactivate if user hasn't confirmed email address after an hour or so
					if($login_as == true) {
						cc_mail($email, $msg['msgs'][$lang]['welcomeEmail']['title'], $msg['msgs'][$lang]['welcomeEmail']['pleaseClick'] . $root_server_url . "/link.php?d=" . $confirm_code . $msg['msgs'][$lang]['welcomeEmail']['confirm'] . str_replace('CUSTOMER_PRICE_PER_SMS_US_DOLLARS', CUSTOMER_PRICE_PER_SMS_US_DOLLARS, $msg['msgs'][$lang]['welcomeEmail']['setupSMS']) . str_replace('ROOT_SERVER_URL',$root_server_url, $msg['msgs'][$lang]['welcomeEmail']['questions']) . $msg['msgs'][$lang]['welcomeEmail']['regards'], $cnf['email']['webmasterEmail']);
						
						
						$_SESSION['logged-user'] = db_insert_id();
						$this->clear_old_session_data();
					}
					
					//Let me know there is a new user
					cc_mail($cnf['email']['adminEmail'], $msg['msgs'][$lang]['welcomeEmail']['warnAdminNewUser'], clean_data($email), $cnf['email']['webmasterEmail']);
				
					return db_insert_id();
			
				} else {
				 	//email is null
					//Create a new 'temporary' user
					$sql = "INSERT INTO tbl_user(var_last_ip, var_email, var_phone, date_created) VALUES ('" . clean_data($ip) . "', NULL," . clean_data($insert_phone) . ", NOW())";
					$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
				
					if($login_as == true) {
						//Set the logged user to the db user id
						$_SESSION['logged-user'] = db_insert_id();
						$this->clear_old_session_data();
					}
				 
					return db_insert_id();
			
				}
			} //end email check
		}
	
	
	
	
	}
	
	

	  
	public function social_post($public_to, $short_code, $user_id, $message, $message_id, $layer_id, $introducing = false, $from_user_id = null, $sender_title = null)
 	{
 	    	global $staging;
            global $msg;
            global $lang;
 	    
  	
	  	//Get the URL of where it was from  		
	   	$components = parse_url(cur_page_url());
		$params = parse_str($components['query']);
		//$params['m'] = $message_id;			//TODO: highlight this message for user
		$replaced = $components['scheme'] . "://" . $components['host'] . $components['path'];
		$visurl = $replaced;
		if($params) {
					$replaced .= "?" . http_build_query($params); 
		}
				
		  $sql = "SELECT * FROM tbl_user WHERE int_user_id = " . $from_user_id;
		  $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		  if($row = db_fetch_array($result))
		  {
				
				
				
							switch($short_code)
							{
									case "twt":
									   
									
												if($staging == true) {
															$message = "@TESTONLY " . $msg['msgs'][$lang]['tweetSeeReply'] . " " . $replaced . " " . $msg['msgs'][$lang]['tweetFrom'] . " " . $sender_title;
												} else {
															$message = "@$public_to " . $msg['msgs'][$lang]['tweetSeeReply'] . " " . $replaced . " " . $msg['msgs'][$lang]['tweetFrom'] . " " . $sender_title;
												}
												
												require_once(dirname(__FILE__) . '/twitter/TwitterAPIExchange.php');
												
												$soc = new cls_social();
			
												$resp = $soc->write_twitter($message);
			
		
									break;
							}		
				}				
	}
	
	public function social_outing($message, $public_to, $short_code)
	{
         global $msg;
         global $lang;
		 $outgoing = $message;
	
		 switch($short_code)
		 {
		 
		 	  case "twt":
		 	  			//In twitters case, they will be directed back to our page, and there must be a clear indication
		 	  			//of which message was sent to them
		 	  			$outgoing = $message . " " . str_replace("PUBLIC_TO", $public_to, $msg['msgs'][$lang]['tweetAlsoSentTo']);
		 	  break;
		 
		 	  default:
		 	  
		 	    $outgoing = $message;
		 	  break;
		 
		 }
	
	   return $outgoing;
	
	}	 
	
	
	
	
	
	public function whisper_by_email($user_id, $message, $message_id, $layer_id, $introducing = false, $from_user_id = null, $always_send_email = false)
	{
		global $root_server_url;
		global $local_server_path;
		global $notify;
		global $cnf;
		global $staging;
		global $msg;
		global $lang;
	
		//Get access rights either public or private - this determines whether we are sending mail and inviting for a live chat (public), or just sending
		//mail like an ordinary mail client (private)
		$sql = "SELECT enm_access FROM tbl_layer WHERE int_layer_id = " . $layer_id;
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
			$access = $row['enm_access'];
			error_log("Access:" . $access . "  Message:" . $message);
		} else {
			error_log("Could not find layer id:" . $layer_id);
			//Don't send the mail - some error
			return false;
		}
		
		
		 //Get the from user id's email if it exists
		$from_email = $cnf['email']['noReplyEmail'];
		$from_different = false;
		if($from_user_id) {
			$sql = "SELECT var_email FROM tbl_user WHERE int_user_id = " . $from_user_id; 
			$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			if($row2 = db_fetch_array($result))
			{
				if((!is_null($row2['var_email']))&&($access=='private')) {
					$from_email = $row2['var_email'];
					$from_different = true;
					$always_send_email = true;		//A private forum will always send an email - this is used by shortmail
				}
			
			}

		}
	
	
		//Send a whisper to a recipient
		$sql = "SELECT * FROM tbl_user WHERE int_user_id = " . $user_id;
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
			if($introducing == false) {
				//A standard email once they are familiar with the concept
				$email_body = $message;
				
				if($access == 'public') {
					$url = cur_page_url();
					$observe_url = $url;
					$observe_message = $msg['msgs'][$lang]['observeMessage'];
					$layer_message = $msg['msgs'][$lang]['layerName'];
					$layer_name = $this->layer_name;
					$email_body .= 	"\n\n" . $observe_message . " <a href=\"$observe_url\">$observe_url</a>\n" . $layer_message . ": " . $layer_name;
; 
				
					$url = $root_server_url . "/de.php?mid=" . $message_id;
					$remove_url = $url;
					$remove_message = $msg['msgs'][$lang]['removeComment'];
					$email_body .= "\n\n" . $remove_message . " <a href=\"$remove_url\">$remove_url</a>";  // . "u=" . urlencode(cur_page_url())
					
				} else {
					//Sent from a private email forum - append our logo
					$email_body .= $msg['msgs'][$lang]['fromShortMail'];
				
				}
				
				$message_details = array("observe_message" => $observe_message,
										 "observe_url" => $observe_url,
										 "forum_message" => $layer_message,
										 "forum_name" => $layer_name,
										 "remove_message" => $remove_message,
										 "remove_url" => $remove_url);
				
				$data = array();
				list($ret, $data) = $this->call_plugins_notify("init", $message, $message_details, $message_id, $from_user_id, $user_id, $data);
				list($with_app, $data) = $this->call_plugins_notify("addrecipient", $message, $message_details, $message_id, $from_user_id, $user_id, $data);
				if($with_app == false) {
					$ly = new cls_layer();
					$ly->always_send_email = $always_send_email;
					if($ly->just_sent_message($layer_id, $message_id, '20') == false) {
						$result = cc_mail($row['var_email'], summary($message, 45), $email_body, $from_email, null, null, $from_email);  //"Private message on " . cur_page_url()
					}
				}
				$this->call_plugins_notify("send", $message, $message_details, $message_id, $from_user_id, $user_id, $data);
				
				
			} else {
				//This is to someone who has been mentioned in the body of the message (introducing == true)
				$checksum = 233242 + $user_id * 19;
				 
    			$components = parse_url(cur_page_url());
				$params = parse_str($components['query']);
				$params['t'] = $user_id;
				$params['f'] = $from_user_id;
				$params['c'] = $checksum;
				$replaced = $components['scheme'] . "://" . $components['host'] . $components['path'] . "?" . http_build_query($params); 
				
				$email_body = $message;
				error_log("Email body is now 0:" . $email_body . "  Message:" . $message);
				
				
				if($access == 'public') { 
					if($from_different == false) {
						$observe_message = $msg['msgs'][$lang]['toReplySee'];
						$observe_url = $replaced;
						$email_body .= "\n\n" . $observe_message . " <a href=\"$observe_url\">$observe_url</a>";
					} else {
						$observe_message =$msg['msgs'][$lang]['replyOrChat'];
						$observe_url = $replaced;
						$email_body .= "\n\n" . $observe_message . " <a href=\"$observe_url\">$observe_url</a>";
				
					}
					
					$remove_message = $msg['msgs'][$lang]['removeComment'];
					$remove_url = $root_server_url . "/de.php?mid=" . $message_id;
					$email_body .= "\n\n" . $msg['msgs'][$lang]['removeComment'] . ": <a href=\"$remove_url\">$remove_url</a>";  // . "u=" . urlencode(cur_page_url())
				}
				
				error_log("Email body is now 1:" . $email_body);
				
				$email_body .= $msg['msgs'][$lang]['fromShortMail'];
				
				error_log("Email body is now 2:" . $email_body);
				
				$message .= $msg['msgs'][$lang]['fromShortMail'];
				
				$message_details = array("observe_message" => $observe_message,
										 "observe_url" => $observe_url,
										 "forum_message" => "",
										 "forum_name" => "",
										 "remove_message" => $remove_message,
										 "remove_url" => $remove_url);
				
				$data = array();
				list($ret, $data) = $this->call_plugins_notify("init", $message, $message_details, $message_id, $from_user_id, $user_id, $data);
				list($with_app, $data) = $this->call_plugins_notify("addrecipient", $message, $message_details, $message_id, $from_user_id, $user_id, $data);
				if($with_app == false) {	
					
					$ly = new cls_layer();
					$ly->always_send_email = $always_send_email;
					if($ly->just_sent_message($layer_id, $message_id, '20') == false) {
						//If haven't already sent a message from this

						error_log("Email body is now 3:" . $email_body);

						$result = cc_mail($row['var_email'], summary($message, 45), $email_body, $from_email, null, null, $from_email);  //First 45 letters of message is the title "A new message from " . $_SERVER["SERVER_NAME"]
					}
				}
				$this->call_plugins_notify("send", $message, $message_details, $message_id, $from_user_id, $user_id, $data);

			}
		
			if($row['var_phone']) {	//TODO: consider only smsing when the group is set?
				
				$ly = new cls_layer();
				$ly->always_send_email = $always_send_email;
				if($ly->just_sent_message($layer_id, $message_id) == false) {
					
				
					if($notify == true) {
						//Asyncronously call our sms
						if($access == 'public') {		//a private message forum only sends emails, not text messages. TODO but must have a reply option..
						
						 //note: from user id is recipient - it is their account we are reducing
							$cmd = "nohup nice -n 10 " . $cnf['phpPath'] . " " . $local_server_path . "send-sms.php phone=" . rawurlencode($row['var_phone']) . " message=" . rawurlencode($message . ' ' . cur_page_url()) . " user_from_id=" . $user_id . " staging=" . $staging;	  //To log this eg: . ' >/var/www/html/yourdir/tmp/newlog.txt';
					
		
							array_push($process_parallel, $cmd);        //Store to be run by index.php at the end of everything else.
						}
					}
					
					
				}
			
			}
		}
		
		return $result;
	
	}
	
	
	public function deactivate_shout($ssshout_id, $just_typing = false)
	{
		global $cnf;
		global $msg;
		global $lang;
		
		//just_typing == true, when you are just typing and it temporarily removes your 'typing' message
		//            == false, for when want full deactivation
		if((isset($cnf['db']['deleteDeletes']))
			&& ($cnf['db']['deleteDeletes'] == true)
			&& ($just_typing == false)) {
			
			//This is a genuine call to delete the message, and we need to remove it from the database completely.
			$sql = "DELETE FROM tbl_ssshout WHERE int_ssshout_id = " . clean_data($ssshout_id);
		} else { 
			//A regular deactivate
			$sql = "UPDATE tbl_ssshout SET enm_active = 'false' WHERE int_ssshout_id = " . clean_data($ssshout_id);
		}
		dbquery($sql) or die("Unable to execute query $sql " . dberror());
		
		if(($just_typing == false)&&
		   ($cnf['db']['deleteDeletes'] == false)) {
			//Warn overall admin - TODO: just layer admin?
			cc_mail($cnf['email']['adminEmail'], str_replace("MSG_ID", $ssshout_id, $msg['msgs'][$lang]['deactivatedCheck']), $cnf['email']['webmasterEmail']);
			echo "Deactivated message.";		//TODO more descriptive comment here.	
		}
	
	}
	
	public function get_email_from_user_id_insecure($user_id, $checksum)
	{
		$sql ="SELECT * FROM tbl_user WHERE int_user_id = " . clean_data($user_id);
		dbquery($sql) or die("Unable to execute query $sql " . dberror());
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
			//If there is no password, and the checksum is correct (checksum based off the user_id)
			
			if(is_null($row['var_pass'])) {			//Only if haven't set the password
				if($checksum == (($user_id * 19) + 233242)) {		//some pseudo random calc. This must be reversible from the other end
																														//ie. $checksum = 233242 + $user_id / 19;
					return $row['var_email'];
					
				
				}
			
			
			}
		}
		
		return false;
	
	
	}
	
	public function check_email_secure($email, $checksum) {
		//Get user id
		$sql ="SELECT * FROM tbl_user WHERE var_email = '" . clean_data($email) . "'";
		dbquery($sql) or die("Unable to execute query $sql " . dberror());
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
			if($checksum == (($row['int_user_id'] * 19) + 233242)) {
				return true;
			}
		}
		return false;
	}
	
	public function check_email_exists($email) {
		//Get user id
		$sql ="SELECT * FROM tbl_user WHERE var_email = '" . clean_data($email) . "'";
		dbquery($sql) or die("Unable to execute query $sql " . dberror());
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
			
				return true;
		
		}
		return false;
	}
	
	public function call_plugins($layer, $message, $message_id, $user_id, $whisper_to_id, $your_name, $email, $phone, $allowed_plugins) {
	    global $cnf;
	    global $local_server_path;
	    
	    if($allowed_plugins != null) {
	        //OK we have an array of allowed plugins
	        $plugins = $allowed_plugins;
	    } else {
	        //Otherwise, assume all plugins in the global config
	        $plugins = $cnf['plugins'];
	    }
	    	    
	    //Loop through each class and call each plugin_* -> on_message() function
	    for($cnt=0; $cnt < count($plugins); $cnt++) {
	        $plugin_name = $plugins[$cnt];
	        
	       
	        include_once($local_server_path . "plugins/" . $plugin_name . "/index.php");
	        $class_name = "plugin_" . $plugin_name;
	        
	        $pg = new $class_name();
	        
	        if(method_exists($pg,"on_message") == true) {
	            //OK call the on_message function of the plugin
	            
	            if(isset($_REQUEST['passcode'])) {
	            	$layer_name = $_REQUEST['passcode'];
	            } else {
	            	$layer_name = "";
	            }
	            
	            $pg->on_message($layer, $message, $message_id, $user_id, $whisper_to_id, $your_name, $email, $phone, $layer_name);
	        
	        } else {
	            //No on_message() in plugin - do nothing

	        }
	    }
	    return true;
	}
	
	
	public function call_plugins_notify($stage, $message, $message_details, $message_id, $sender_id, $recipient_id, $indata = null, $allowed_plugins = null) {
	    global $cnf;
	    global $local_server_path;
	    
	    if($allowed_plugins != null) {
	        //OK we have an array of allowed plugins
	        $plugins = $allowed_plugins;
	    } else {
	        //Otherwise, assume all plugins in the global config
	        $plugins = $cnf['plugins'];
	    }
	    
	    //Loop through each class and call each plugin_* -> before_notification() function
	    for($cnt=0; $cnt < count($plugins); $cnt++) {
	        $plugin_name = $plugins[$cnt];
	        
	       
	        include_once($local_server_path . "plugins/" . $plugin_name . "/index.php");
	        $class_name = "plugin_" . $plugin_name;
	        
	        $pg = new $class_name();
        
	        if(method_exists($pg,"on_notify") == true) {
	            //OK call the on_notify function of the plugin
	            list($ret, $data) = $pg->on_notify($stage, $message, $message_details, $message_id, $sender_id, $recipient_id, $indata);
	        
	        } else {
	            //No on_notify() in plugin - do nothing
	        }
	    }
	    
	    return array($ret, $data);
	}
	
	
	
	public function call_plugins_before_msg($message, $allowed_plugins = null) {
	    global $cnf;
	    global $local_server_path;
	    
	    if($allowed_plugins != null) {
	        //OK we have an array of allowed plugins
	        $plugins = $allowed_plugins;
	    } else {
	        //Otherwise, assume all plugins in the global config
	        $plugins = $cnf['plugins'];
	    }
	    
	    //Loop through each class and call each plugin_* -> before_message() function
	    for($cnt=0; $cnt < count($plugins); $cnt++) {
	        $plugin_name = $plugins[$cnt];
	        
	       
	        include_once($local_server_path . "plugins/" . $plugin_name . "/index.php");
	        $class_name = "plugin_" . $plugin_name;
	        
	        $pg = new $class_name();
	        
	        if(method_exists($pg,"before_message") == true) {
	            //OK call the on_message function of the plugin
	            $message = $pg->before_message($message);
	        
	        } else {
	            //No before_message() in plugin - do nothing
	        }
	    }
	    return $message;
	}
	
	
	
    public function call_plugins_settings($allowed_plugins) {
	    global $cnf;
	    global $local_server_path;
	    
	    if($allowed_plugins != null) {
	        //OK we have an array of allowed plugins
	        $plugins = $allowed_plugins;
	    } else {
	        //Otherwise, assume all plugins in the global config
	        $plugins = $cnf['plugins'];
	    }
	    	    
	    //Loop through each class and call each plugin_* -> on_message() function
	    for($cnt=0; $cnt < count($plugins); $cnt++) {
	        $plugin_name = $plugins[$cnt];
	        
	       
	        include_once($local_server_path . "plugins/" . $plugin_name . "/index.php");
	        $class_name = "plugin_" . $plugin_name;
	        
	        $pg = new $class_name();
	        
	        if(method_exists($pg,"on_more_settings") == true) {
	            //OK call the on_settings function of the plugin
	            $pg->on_more_settings();
	        
	        } else {
	            //No on_message() in plugin - do nothing

	        }
	    }
	    return true;
	}
	

    public function call_plugins_upload($allowed_plugins) {
	    global $cnf;
	    global $local_server_path;
	    
	    if($allowed_plugins != null) {
	        //OK we have an array of allowed plugins
	        $plugins = $allowed_plugins;
	    } else {
	        //Otherwise, assume all plugins in the global config
	        $plugins = $cnf['plugins'];
	    }
	    	    
	    //Loop through each class and call each plugin_* -> on_message() function
	    for($cnt=0; $cnt < count($plugins); $cnt++) {
	        $plugin_name = $plugins[$cnt];
	        
	       
	        include_once($local_server_path . "plugins/" . $plugin_name . "/index.php");
	        $class_name = "plugin_" . $plugin_name;
	        
	        $pg = new $class_name();
	        
	        if(method_exists($pg,"on_upload_screen") == true) {
	            //OK call the on_settings function of the plugin
	            $pg->on_upload_screen();
	        
	        } else {
	            //No on_message() in plugin - do nothing

	        }
	    }
	    return true;
	}
	
	
	public function call_plugins_emojis($allowed_plugins) {
	    global $cnf;
	    global $local_server_path;
	    
	    if($allowed_plugins != null) {
	        //OK we have an array of allowed plugins
	        $plugins = $allowed_plugins;
	    } else {
	        //Otherwise, assume all plugins in the global config
	        $plugins = $cnf['plugins'];
	    }
	    	    
	    //Loop through each class and call each plugin_* -> on_message() function
	    for($cnt=0; $cnt < count($plugins); $cnt++) {
	        $plugin_name = $plugins[$cnt];
	        
	       
	        include_once($local_server_path . "plugins/" . $plugin_name . "/index.php");
	        $class_name = "plugin_" . $plugin_name;
	        
	        $pg = new $class_name();
	        
	        if(method_exists($pg,"on_emojis_screen") == true) {
	            //OK call the on_settings function of the plugin
	            $pg->on_emojis_screen();
	        
	        } else {
	            //No on_emojis_screen() in plugin - do nothing

	        }
	    }
	    return true;
	}
	

	
	
	public function insert_shout($latitude, $longitude, $your_name, $shouted, $whisper_to, $email, $ip, $bg, $layer, $typing = false, $ssshout_id = null, $phone = null, $local_msg_id = null, $whisper_site = null, $short_code = null, $public_to = null, $date_override = null,$loginas = true, $allow_plugins = true, $allowed_plugins = null, $notification = true, $always_send_email = false)
	{
	    global $msg;
	    global $lang;
	    global $db;
		$email_in_msg = false;
	
		//Insert shouted text into database at this time
		$peano1 = $bg->generate_peano1($latitude, $longitude);		//Lat/lon of point in table
		$peano2 = $bg->generate_peano2($latitude, $longitude);
		$peano1iv = $bg->generate_peano_iv($peano1);
		$peano2iv = $bg->generate_peano_iv($peano2);
		
		
		
		if($typing == true) {
			$shouted = $msg['msgs'][$lang]['typing'];
		}
		
		if(($your_name != "")&&
			($your_name != "Your Name")) {
			$message = $your_name . ": " . $shouted;
		} else {
			$message = $shouted;
		}
		
		if($date_override) {
			 //Allow for a string override on the date
				$date_shouted = "'" . $date_override . "'";
		
		} else {
			 $date_shouted = "NOW()";					
		}
			
		
   		//If we are a user get our id
		$user_id = $this->new_user($email, $ip, $phone, $loginas);
				

		
		
		if(trim($message) != "") {
			
			if($typing == true) {
				$new_message = true;
			} else {
			
				//Check the message is not a repeat from the same ip, within the last 5 seconds
				if($ssshout_id) {
					$sql = "SELECT int_ssshout_id  FROM tbl_ssshout WHERE var_ip = '". $ip ."' AND var_shouted = '" . clean_data($message) . "' AND TIMEDIFF(NOW(),date_when_shouted) < '00:00:05'";
			
					$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
					if($row = db_fetch_array($result))
					{
						//Already exists - don't repeat.  Unless they're further than 5 seconds apart
						//e.g. a message like 'hi' which you do want to repeat
						$new_message = false;
					
						//Deactivate 'typing' message
					
						$sql = "UPDATE tbl_ssshout SET enm_active = 'false', enm_status = 'final'
													WHERE int_ssshout_id = " . $ssshout_id;
						dbquery($sql) or die("Unable to execute query $sql " . dberror());
					
					} else {
						//A new message - we'll print this
						$new_message = true;
			
					}
				} else {
					//Likely a repeat from a bot - TODO: only allow if same layer
					$new_message = true;
				
				
				}
			}
			
			if($new_message == true) {
				
				if($typing == false) {
					//Parse the message for any email addresses
					//First check if we include any email addresses - use this to whisper to them
					$pattern="/email:(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/";

					preg_match_all($pattern, $message, $matches);
					if(count($matches) > 0) {
						foreach($matches as $new_email) {
							if($new_email[0]) {
								//Chop off the 'email:'
								$email_to = str_replace("email:", "", $new_email[0]);
								$new_user_id = $this->new_user($email_to, '', '', false);		//false is do not log in as this new user (so no welcome message sent)
								//NOTE: this currently only supports a single email address that is sent privately, multiple email addresses
								//must be sent by the user publicly to be effective - TODO: possible soln is to replicate the message for each user 
								$email_in_msg = true;
							}
							
						}
					
					}
					
					
					if($short_code) {
							 //If it is a social post to a particular external network, do any changes
						  $message = $this->social_outing($message, $public_to, $short_code);	 
					}
					
					
					
				}
				
				
				
			
				$notify_group = false;
				
				if($whisper_to) {
					//Now check if we are whispering to anyone at this time - would be the same as whisper_site by default, but could be
					//any individual user.
					$whisper_to_group = explode(",",$whisper_to);
					
					$whisper_to_divided = explode(":",$whisper_to_group[0]);
					if(($whisper_to_divided[1] == '')||($whisper_to_divided[1] == 0)||($whisper_to_divided[1] == 'undefined')) {
				
						$whisper_to_id = "NULL";
					} else {
						$whisper_to_id = $whisper_to_divided[1];
					}
					
					if((isset($_SESSION['layer-group-user']))&&($_SESSION['layer-group-user'] != '')&&($_SESSION['layer-group-user'] != 'undefined')) {
						if($whisper_to == $whisper_site) {
							//Check to make sure we are whispering to the owner - in that case modify the target to be the special
							//group user
							
							$whisper_to_id = $_SESSION['layer-group-user'];
							$whisper_to_divided[0] = '1.1.2.1';	//make sure nobody else from same ip can see this
							
							//And ensure we notify the whole group
							$notify_group = true;
						}
					}
				} else {
					//No group - general public
					$whisper_to_id = "NULL";
					//leave whisper_to_divided as the ip of the site's user
					$whisper_to_divided[0] = '';
					$whisper_to_divided[1] = 0;
				}
				


			
				if($ssshout_id) {
					//Check if we already exist
					//Update an existing shout at the end of typing, after push commit
					if($typing == false) {
					   $status = "final";
					   
					   
					} else {
					   $status = "typing";
					}
					
					list($ssshout_processed, $include_payment) = $this->process_chars($message,$ip,$user_id, $ssshout_id, $allow_plugins, $allowed_plugins);
					
					$sql = "UPDATE tbl_ssshout SET date_when_shouted = " . clean_data($date_shouted) . ",
												var_shouted = '" . clean_data($message) . "',
												var_shouted_processed = '" . clean_data_keep_tags($ssshout_processed) . "',
												var_whisper_to = '" . $whisper_to_divided[0] . "',
												int_whisper_to_id= " . $whisper_to_id . ",
												enm_active = 'true',
												enm_status = '$status',
												int_author_id = $user_id
												WHERE int_ssshout_id = " . $ssshout_id . " and enm_status = 'typing'";
					if(!dbquery($sql)) {
							error_log("Unable to execute query $sql " . dberror());
							die("Unable to execute query $sql " . dberror());
					}
					
					//If there were multiple users being whispered to, we need to post new individual messages to each of the users.
					
					
					if($typing == false) {
						//Prep a message to users
						$message_id = $ssshout_id;
						$ssshout_id = "";		//this is the return value of null beacuse want a new message
						
					} else {
						//A continuation request
						$message_id = "";
						
					}
				} else {
				
					//Process the chars
					list($ssshout_processed, $include_payment) = $this->process_chars($message,$ip,$user_id, null, $allow_plugins, $allowed_plugins);
				
					if($typing == false) {
					   $status = "final";
					} else {
					   $status = "typing";
					}
					
					
					
					
					
				
				
					//Insert the new shout
					$sql = "INSERT tbl_ssshout(
									dec_latitude,
									dec_longitude,
									int_peano1,
									int_peano2,
									int_peano1inv,
									int_peano2inv,						
									var_shouted,
									var_shouted_processed,
									date_when_shouted,
									int_layer_id,
									enm_active,
									enm_status,
									var_ip,
									var_whisper_to,
									int_whisper_to_id,
									int_author_id
								) VALUES (		
									'" . $latitude ."',
									'" . $longitude ."',
								 	'" . $peano1 ."',
									'" . $peano2 . "',
									'" . $peano1iv ."',
									'" . $peano2iv . "',
									'" . clean_data($message) . "',
									'" . clean_data_keep_tags($ssshout_processed). "',
									" . $date_shouted . ",
									" . $layer . ",
									'true',
									'" . $status ."',
									'" . $ip . "',
									'" . $whisper_to_divided[0] . "',
									" . $whisper_to_id . ",
									" . $user_id ."
									)";	
									
									
						if(!dbquery($sql)) {
							error_log("Unable to execute query $sql " . dberror());
							die("Unable to execute query $sql " . dberror());
						} 
						$ssshout_id = db_insert_id();
						$message_id = $ssshout_id;
						
						
						if($include_payment == true) {
							//Includes payment need to process again with the message id
							list($ssshout_processed, $include_payment) = $this->process_chars($message,$ip,$user_id,$message_id, $allow_plugins, $allowed_plugins);
							$sql = "UPDATE tbl_ssshout SET 
												var_shouted_processed = '" . clean_data_keep_tags($ssshout_processed) . "'
												WHERE int_ssshout_id = " . $ssshout_id;
						}
						
						
						
						
				}
						
				
				
				//Handle a whisper option
				if($typing == false) {
					
					
					
					if($short_code) {
					    //We are also posting to a social network
						$result = $this->social_post($public_to, $short_code, $new_user_id, $message, $message_id, $layer, true, $user_id, $your_name);	//true is because we are introducing
			
					}
					
					if($email_in_msg == true) {
						//We are sending off a whisper to an email address
						$this->whisper_by_email($new_user_id, $message, $message_id, $layer, true, $user_id, $always_send_email);	//true is because we are introducing
					} 
				
				
					if($whisper_to_divided[1]) {
						//The whisper user
				
						//Whisper off an email of this message to the recipient
						//The number after the : is the user id
						if($notify_group == true) {
							//More than one user in the company - notify the whole group
							
							if($notification == true) {
							
								//Keep all relevant users updated by email or sms
								$ly = new cls_layer();
								$ly->always_send_email = $always_send_email;
								$ly->layer_name = $this->layer_name;
								$ly->notify_group($layer, $message, $message_id, $user_id);
							}
						
						} else {
					
							if($notification == true) {
								//Just one recipient - only let them know
								$this->whisper_by_email($whisper_to_divided[1], $message, $message_id, $layer, false, $user_id, $always_send_email);
							}
						
						}
	
					} else {
			
						if($notification == true) {
			
							//Keep all relevant users updated by email or sms
							$ly = new cls_layer();
							$ly->always_send_email = $always_send_email;
							$ly->layer_name = $this->layer_name;
							$ly->notify_group($layer, $message, $message_id, $user_id);
						}
					
					}
					
				}
				
				if(($typing == false)&&($message_id != "")) {      
			        //Hook into plugins here
			        if($allow_plugins == true) {
			            $this->call_plugins($layer, clean_data($message), $message_id, $user_id, $whisper_to_id, $your_name, $email, $phone, $allowed_plugins);
			        }
			    }
				

 				return $ssshout_id;
			}
		}
		
		
		
		return false;
	}
	
	public function is_social($my_line) {
	   //Checks whether this is a social network
	   // post. Direct message replies get sent publicly
	   
	   $networks = array(array( $msg['msgs'][$lang]['social']['viaTwitter'], "twt"),
	                     array( $msg['msgs'][$lang]['social']['viaFacebook'], "fbk" ));
	     
	   $outgoing = "";                  
	   foreach($networks as $network) {
	     if(strstr($my_line, $network[0]) != false) {
	        $outgoing = $network[1];
	     }
	   }
	   
	   return $outgoing;
	
	}
	
	public function process_chars($my_line, $ip, $user_id, $id = null, $allow_plugins = true, $allowed_plugins = null)
	{
		$include_payment = false;
        $orig_line = $my_line;
        global $msg;
        global $lang;
        global $root_server_url;
		
		
		//Handle any plugin-defined parsing of the message. Eg. turn smileys :) into smiley images.
        if($allow_plugins == true) {
            $my_line = $this->call_plugins_before_msg($my_line, $allowed_plugins);
        }
		
		
		
		
		
		//Turn xxx@ into clickable atomjump links
		$my_line = preg_replace("/\b(\w+)@([^\w]+|\z.?)/i", "$1.atomjump.com", $my_line);
			
			
		//Check for a payment link					
		if(preg_match('/pay\s([\d|\.]+)\s(pounds|dollars|pound|dollar)/i', $my_line, $pay)) {
			//Generate the user's email address for correct payment link
			//$pay[1] = amount, $pay[2] = currency
			switch($pay[2]) {
				case "pounds":
					$currency = "GBP";
				
				break;
				
				case "pound":
					$currency = "GBP";
				
				break;
				
				case "dollars":
					$currency = "USD";
				
				break;
				
				case "dollar":
					$currency = "USD";
				break;
				
				default:
					$currency = "USD";
				
				break;
				
			}
			
			
			$my_line = preg_replace('/(pay\s([\d|\.]+)\s(pounds|dollars|pound|dollar))/i', '<a target="_blank" href="' . $root_server_url . '/p2p-payment.php?user_id=' . $user_id . '&amount=' . trim($pay[1]) . '&currencyCode=' . $currency . '&msgid=' . $id. '">$1</a>', $my_line);
			$include_payment = true;  //switch on flag	
			
			//In this case we have a slightly different url definition, because we don't want to replace the dollar amount with a url link:
			//Turn any strings which are entirely chars (and not numbers) and include dots into urls
			//Convert any links into a href links
			$my_line= preg_replace('@(\s)((https?://)?([-\w]+\.[-\D.]+)+\D(:\d+)?(/([-\D/_\.]*([\?|\#]\D+)?)?)*)@', ' <a target="_blank" href="$2">$2</a>', $my_line);
			
		} else {		
				
			//Turn any strings which are entirely chars/numbers and include dots into urls
			//Convert any links into a href links
			$my_line= preg_replace('@(\s)((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*([\?|\#]\S+)?)?)*)@', ' <a target="_blank" href="$2">$2</a>', $my_line);
		
		}
		
		
		//Turn video links on youtube into embedded thumbnail which plays at youtube  
		$my_line = preg_replace('#>https://youtu.be\/(.*)<#i', '><img class="img-responsive" width="80%" src="https://img.youtube.com/vi/$1/0.jpg"><img src="https://atomjump.com/images/play.png" width="32" height="32" border="0"><', $my_line);
		

		//Turn uploaded images into responsive images, with a click through to the hi-res image
		$my_line = preg_replace("/href=\"(.*?)\.jpg\"\>(.*?ajmp(.*?))\.jpg\</i", 'href="$2_HI.jpg"><img src="$2.jpg" class="img-responsive" width="80%" border="0"><', $my_line);	 


		//Turn images into responsive images, with a click through to the image itself
		$my_line = preg_replace("/\>(.*?\.jpg)\</i", "><img src='$1'  class='img-responsive' width='80%' border='0'><", $my_line);	 
		
		
		//Turn remote images by themselves into responsive images, with a click through to the image itself
		$my_line = preg_replace("/\s(.*?\.jpg)\s/i", "><img src='$1'  class='img-responsive' width='80%' border='0'><", $my_line);	 


		//because you want the url to be an external link the href needs to start with 'http://'
		//Replace any href which doesn't have htt at the start
		$my_line = preg_replace("/href=\"(?:(http|ftp|https)\:\/\/)?([^\"]*)\"/","href=\"http://$2\"",$my_line);
		
		
		//Turn .atomjump.com links into xxx@ clickable links
		$my_line = preg_replace("/>(http:\/\/)?(.*?)\.atomjump\.com</i", ">$2@<", $my_line);


		//Turn long links into smaller 'More Info' text only (except where an image)
		$my_line = preg_replace("/>([^<]{50,})(<\/a>)/i", ">" . $msg['msgs'][$lang]['expandLink'] ."$2", $my_line);
		

		

		//Turn names into an ip address private whisper link
		if($ip != "") {
		
		 $privately = $msg['msgs'][$lang]['social']['privately'];
		 $private = "true";  //true
		 $shortcode = $this->is_social($my_line);
		 if($shortcode != "") {
		    $private = "false";  //false
		    $privately = $msg['msgs'][$lang]['social']['publiclyViaSocial'];
		 } 
			$my_line = preg_replace("/^([^:]+):\s/i", "<a href='#' onclick='whisper(\"" . $ip . ":" . $user_id . "\", \"$1\", " . $private . ", \"" . $shortcode ."\"); return false;' title='" . $msg['msgs'][$lang]['sendCommentTo'] . " $1 " . $privately . "'>$1</a>:&nbsp;", $my_line);		
		}
		
		
		
		
		

		return array($my_line, $include_payment);
	}
		


}





class cls_search {



	

		public function ago($time)
		{
		   global $msg; 
		   global $lang;
		   //Doesn't seem to be needed: global $db_timezone;
		   $periods = array($msg['msgs'][$lang]['time']['second'],
		                    $msg['msgs'][$lang]['time']['minute'],
		                    $msg['msgs'][$lang]['time']['hour'],
		                    $msg['msgs'][$lang]['time']['day'],
		                    $msg['msgs'][$lang]['time']['week'],
		                    $msg['msgs'][$lang]['time']['month'],
		                    $msg['msgs'][$lang]['time']['year'],
		                    $msg['msgs'][$lang]['time']['decade']);
		   $periods_plural = array($msg['msgs'][$lang]['time']['seconds'],
		                    $msg['msgs'][$lang]['time']['minutes'],
		                    $msg['msgs'][$lang]['time']['hours'],
		                    $msg['msgs'][$lang]['time']['days'],
		                    $msg['msgs'][$lang]['time']['weeks'],
		                    $msg['msgs'][$lang]['time']['months'],
		                    $msg['msgs'][$lang]['time']['years'],
		                    $msg['msgs'][$lang]['time']['decades']);
		   $lengths = array("60","60","24","7","4.35","12","10");
	
		   date_default_timezone_set($db_timezone); //e.g. "Europe/Berlin"	
		   $now = time();

		   $difference     = $now - $time;
		   $tense         = $msg['msgs'][$lang]['time']['ago'];

		   for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			   $difference /= $lengths[$j];
		   }

		   $difference = round($difference);
		   
		   if($difference != 1) {
		        return "$difference $periods_plural[$j] " . $tense;
		   } else {
		        return "$difference $periods[$j] " . $tense;
		   }
		}

  // Original PHP code by Chirp Internet: www.chirp.com.au
  // Please acknowledge use of this code by including this header.

  public function cleanCSVData($str)
  {
    // escape tab characters
    $str = preg_replace("/\t/", "\\t", $str);

    // escape new lines
    $str = preg_replace("/\r?\n/", "\\n", $str);

    // convert 't' and 'f' to boolean values
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';

    // force certain number/date formats to be imported as strings
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }

    // escape fields that include double quotes
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
    return $str;
  }
  
  
  public function csv($res)
  {
      // output headers so that the file is downloaded rather than displayed
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename=messages.csv');

      // create a file pointer connected to the output stream
      $output = fopen('php://output', 'w');

      // output the column headings
      fputcsv($output, array($msg['msgs'][$lang]['fields']['id'],
                            $msg['msgs'][$lang]['fields']['text'],
                            $msg['msgs'][$lang]['fields']['timestamp'],
                            $msg['msgs'][$lang]['fields']['private'],
                            $msg['msgs'][$lang]['fields']['sentiment']
                             ));
								 
      // loop over the rows, outputting them
      foreach($res as $row){
        $rowout = array($this->cleanCSVData($row['id']),
                         $this->cleanCSVData($row['text']),
                         $this->cleanCSVData($row['timestamp']),
                         $this->cleanCSVData($row['private']),
                         $this->cleanCSVData($row['sentiment']));
     
        fputcsv($output, $rowout,';','"');
      }
  }
  
  public function excel($res)
  {
  
   /** Include PHPExcel */
   	require_once('classes/PHPExcel.php');
   	global $msg;
   	global $lang;


	  // Create new PHPExcel object
	  $objPHPExcel = new PHPExcel();

	  // Set document properties
	  $objPHPExcel->getProperties()->setCreator("AtomJump Loop")
							 ->setLastModifiedBy("AtomJump.com")
							 ->setTitle("AtomJump Loop Export")
							 ->setSubject("")
							 ->setDescription("")
							 ->setKeywords("")
							 ->setCategory("");


	  $i = 1;
   	$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $msg['msgs'][$lang]['fields']['id'])
	                              ->setCellValue('B' . $i, $msg['msgs'][$lang]['fields']['text'])
	                              ->setCellValue('C' . $i, $msg['msgs'][$lang]['fields']['timestamp'])
	                              ->setCellValue('D' . $i, $msg['msgs'][$lang]['fields']['private'])
	                              ->setCellValue('E' . $i, $msg['msgs'][$lang]['fields']['sentiment']);


  	$i = 2;
  	foreach($res as $row){
 
	  	$objPHPExcel->getActiveSheet()->setCellValue('A' . $i,$row['id'])
	                              ->setCellValue('B' . $i, $row['text'])
	                              ->setCellValue('C' . $i, $row['timestamp'])
	                              ->setCellValue('D' . $i, ($row['private'] == true) ? $msg['msgs'][$lang]['isPrivate'] : $msg['msgs'][$lang]['isPublic'])
	                              ->setCellValue('E' . $i, $row['sentiment']);
	   	$i++;
	  }

	  // Rename worksheet
	  $objPHPExcel->getActiveSheet()->setTitle('AtomJump Messages');


	  // Set active sheet index to the first sheet, so Excel opens this as the first sheet
	  $objPHPExcel->setActiveSheetIndex(0);


	  // Redirect output to a client’s web browser (Excel2007)
	  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	  header('Content-Disposition: attachment;filename="messages-' . date('Y-m-d-H:i:s') . '.xlsx"');
	  header('Cache-Control: max-age=0');
	  // If you're serving to IE 9, then the following may be needed
  	  header('Cache-Control: max-age=1');

	  // If you're serving to IE over SSL, then the following may be needed
	  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	  header ('Pragma: public'); // HTTP/1.0

	  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	  $objWriter->save('php://output');
	  exit;


}


public function process($shout_id = null, $msg_id = null, $records = null, $download = false, $last_id = 0, $db_timezone= null, $format = "json", $avg_over_secs = 900)  //900 = 15 mins
{
           global $cnf;
           global $msg;
           global $lang;

			$ly = new cls_layer();
			$bg = new clsBasicGeosearch();
			$more = false;

			//Sequential search process. WARNING: depends on request being active
			//E.g. date_default_timezone_set("Europe/Berlin");
			date_default_timezone_set($server_timezone);

			if(($_REQUEST['passcode'] != '')||($_REQUEST['reading'] != '')) { 
				$layer_info = $ly->get_layer_id($_REQUEST['passcode'], $_REQUEST['reading']);
				if($layer_info) {
					$layer = $layer_info['int_layer_id'];
				} else {
					//Create a new layer - TODO: don't allow layers so easily
					$layer = $ly->new_layer($_REQUEST['passcode'], 'public'); 
					
					//Given this is a new layer - the first user is the correct user
					$lg = new cls_login();
					$lg->update_subscriptions(clean_data($_REQUEST['whisper_site']), $layer);	
					
				}
			} else {

				if($_SESSION['authenticated-layer']) {
					$layer = $_SESSION['authenticated-layer'];
				} else {
					$layer = 1;		//Default to about layer
				}
			}
			
			
			

			if($_REQUEST['units'] != '') {
				$units = $_REQUEST['units'];
			}

			if($_REQUEST['dbg'] == 'true') {
				$debug = true;
			} else {
				$debug = false;
			}
			
			if($_SESSION['logged-user']) {
				$user_check = " OR int_author_id = " . $_SESSION['logged-user'] . " OR int_whisper_to_id = " . $_SESSION['logged-user']; 
			
			}
			
			if($_SESSION['logged-group-user']) {
				$user_check .= " OR int_author_id = " . $_SESSION['logged-group-user'] . " OR int_whisper_to_id = " . $_SESSION['logged-group-user']; 
			
			}
			
			if($records < 100) {
				$initial_records = 100;	//min this can be - needs to be about 4 to 1 of private to public to start reducing the number of public messages visible
			} else {
				$initial_records = $records;
			}
			
			


			$ip = $ly->getFakeIpAddr();

		
			if($download == true) {
			  	if($db_timezone) {
			    	$src_tz = new DateTimeZone($db_timezone);
    			} else {
		    		$src_tz = new DateTimeZone($cnf['db']['timezone']); //eg. EDT
    			}
				$dest_tz = new DateTimeZone('UTC'); // TODO: Note this is a constant I think.
				// a download, for now same query
		  
		  
		  
				  switch($format) {
					case "avg":
					  	if($last_id == 0) {
							$last_id = PHP_INT_MAX;
					  	}
				 		$sql = "CREATE TEMPORARY TABLE recent SELECT TIMESTAMPDIFF(SECOND, date_when_shouted, NOW()) AS timeAgo, flt_sentiment FROM tbl_ssshout WHERE int_layer_id = " . $layer . " AND int_ssshout_id < " . $last_id . " AND enm_active = 'true' AND (var_whisper_to = '' OR ISNULL(var_whisper_to) OR var_whisper_to ='" . $ip . "' OR var_ip = '" . $ip . "' $user_check) ORDER BY int_ssshout_id DESC LIMIT $initial_records";
					break;
		
					case "excel":
					  //reverse order get latest only
					  if($last_id == 0) {
						$last_id = PHP_INT_MAX;
					  }
					  $sql = "SELECT * FROM tbl_ssshout WHERE int_layer_id = " . $layer . " AND int_ssshout_id < " . $last_id . " AND enm_active = 'true' AND (var_whisper_to = '' OR ISNULL(var_whisper_to) OR var_whisper_to ='" . $ip . "' OR var_ip = '" . $ip . "' $user_check) ORDER BY int_ssshout_id DESC LIMIT $initial_records";

					break;
		
					default:
					   //json
		
						//json so forwards
					   $sql = "SELECT * FROM tbl_ssshout WHERE int_layer_id = " . $layer . " AND int_ssshout_id > " . $last_id . " AND enm_active = 'true' AND (var_whisper_to = '' OR ISNULL(var_whisper_to) OR var_whisper_to ='" . $ip . "' OR var_ip = '" . $ip . "' $user_check) ORDER BY int_ssshout_id ASC LIMIT $initial_records";

					break;
				  }
		 	} else {   //End of download == true
		   		  	//Standard search
			  		$sql = "SELECT * FROM tbl_ssshout WHERE int_layer_id = " . $layer . " AND enm_active = 'true' AND (var_whisper_to = '' OR ISNULL(var_whisper_to) OR var_whisper_to ='" . $ip . "' OR var_ip = '" . $ip . "' $user_check) ORDER BY date_when_shouted DESC LIMIT $initial_records";
			}


			$ignore_query = false;
			if(isset($_SESSION['access-layer-granted'])&&($_SESSION['access-layer-granted'] != 'true')) {
			
				if(($_SESSION['access-layer-granted'] == 'false') || ($_SESSION['access-layer-granted'] != $layer)) { 
					//No view on this layer
					$results_array = array();
					$ignore_query = true;
				}
			
			} 
			
			
			if($ignore_query == false) {
				//Go get external searches first
				$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
				while($row = db_fetch_array($result))
				{
					$results_array[] = $row;		
				}
			}			


			//TODO: expand on whispering a bit so that only select those which a viewable from us in the top 50 results

			$json = array();
		

		   if($format == "avg") {
	  
				$results_arrayb = array();
				$resultb = dbquery("SELECT AVG(flt_sentiment) FROM recent WHERE timeAgo <  " .  $avg_over_secs)  or die("Unable to execute query $sql " . dberror());
						if($rowb = db_fetch_array($resultb))
						{
							 $results_arrayb[] = $rowb;
						 $json['sentimentAvg'] = round(floatval($results_arrayb[0][0]), 4);
						 echo $json['sentimentAvg'];
							return;  
						}
	   
		   }
			  
	  		$json['res'] = array();





			$mymax_results = count($results_array);
			if($mymax_results > $records) {
				 $mymax_results = $records;	//limit number to records requested
			  $more = true;   //show more flag for
			}
			$actual_cnt = 0;
			
			for($mycnt = 0; $mycnt < count($results_array); $mycnt++) {			
				$result = $results_array[$mycnt];
				$author_ip = $result['var_ip'];
				$author_user_id = $result['int_author_id'];
				$combined_author = $author_ip;
				if(isset($author_user_id)) {
					$combined_author .= ":" . $author_user_id;
				}
				
					$whisper_to_ip = $result['var_whisper_to'];
					$whisper_to_user_id = $result['int_whisper_to_id'];
				
	
					//If no whispering, or are whispering but to the viewer's ip address, or from the viewer's own ip
					if(($whisper_to_ip == '')||		//ie. public
					   (($whisper_to_ip == $ip)&&(is_null($whisper_to_user_id)))||	//private but no user id known
					   ($whisper_to_user_id == $_SESSION['logged-user'])||  //talk direct to owner
					   (($author_ip == $ip)&&(is_null($author_user_id)))||  				//authored by this ip no user known of author
					   ($author_user_id == $_SESSION['logged-user'])||						//authored by this viewer
					   (($_SESSION['logged-group-user'] != "")&&($whisper_to_user_id != "") && ($whisper_to_user_id == $_SESSION['logged-group-user']))) {				//private message to group
	
						//Right actually going to include message - now decide if whispering or public
						
						if(($whisper_to_ip == $ip && is_null($whisper_to_user_id))||		//if it was a whisper intended for our ip but unknown user
								($whisper_to_user_id == $_SESSION['logged-user'])||				//or a whisper specifically for us
						   ($author_ip == $ip && ($whisper_to_ip != ''|| isset($whisper_to_user_id)))||  //or def a whisper by viewer
						   ($author_user_id == $_SESSION['logged-user'] && ($whisper_to_ip != ''|| isset($whisper_to_user_id)))) { //or a whisper by viewer logged in
							//This is a whisper to you or from you, use 1/3 font size
							$whisper = true;
						} else {
							//A shout
							$whisper = false;
						}
						
						if($_SESSION['logged-group-user']) {
							if($whisper_to_user_id == $_SESSION['logged-group-user']) {
								$whisper = true;
						
							}
						}
					
						if(!$_SESSION['logged-user']) {
							//Force a blank user to see only public requests, until he has actually commented. 
							$whisper = false;		
					
						}
						 
	
		
						$shade = $result['int_ssshout_id'] %2;
						if($layer == 0) {
							//Public layer
							if($shade == 0) {
								$bgcolor = "public-light";  
							} else {
								$bgcolor = "public-dark"; 
							}
						} else {
							//Private layer - different colours
							if($shade == 0) {
								$bgcolor = "private-light"; 
							} else {
								$bgcolor = "private-dark"; 
							}
		
						}
		
						date_default_timezone_set($server_timezone); //E.g. "UTC" GMT"
						//Add the following to $result['var_shouted'] below to check everybody is getting the correct message
						//$dbg = " user_id:" . $_SESSION['logged-user'] . " author_ip:" . $author_ip . " author_user_id:" . $author_user_id . " whisper_to_ip:" . $whisper_to_ip . " whisper_to_user_id:". $whisper_to_user_id;
						
						if($actual_cnt <= $mymax_results) {
							if($download == true){
							  
		
							  $dt = new DateTime($result['date_when_shouted'], $src_tz);
                              $dt->setTimeZone($dest_tz);

							
		                      $json['res'][] = array(
										   'id' => $result['int_ssshout_id'],
										   'text' => $result['var_shouted'],  // . $dbg  : $dbg in temporarily  $this->process_chars( , $combined_author, $author_user_id, $result['int_ssshout_id']) taken out
											'timestamp' => $dt->format('Y-m-d\TH:i:s\Z'),
											'private' => $whisper,
											'sentiment' => round($result['flt_sentiment'],1)
										 );  
		    
		                    } else {
							
							  $json['res'][] = array('id' => $result['int_ssshout_id'],
							  				'text' => $result['var_shouted_processed'],  // . $dbg  : $dbg in temporarily  $this->process_chars( , $combined_author, $author_user_id, $result['int_ssshout_id']) taken out
											'lat' => $result['latitude'],
											'lon' => $result['longitude'],
											'dist' => $result['dist'],
											'ago' => $this->ago(strtotime($result['date_when_shouted'])),
											'whisper' => $whisper
										 ); 
							}
							$actual_cnt ++;
						}
						
					}
				
	
			}
			
			
		 $json['ses'] = session_id();			//Let caller know our session id

			if(isset($shout_id)) {
				$json['sid'] = $shout_id;
			
			}
			
			if(isset($msg_id)) {
				$json['lid'] = $msg_id;		//local client msg id
			}
			


			//Echo the jsonp
			if($download == true){
      	       $json['more'] = $more;     //relevant to download
			   switch($format) {
			     case 'csv':
			  
			        $this->csv($json[res]);
			     break;
			     
			     case 'excel':
			        $this->excel($json[res]);
			     break;
			     
			     default:
			  
			       //json
			       echo json_encode($json);
			     break;
			  }
			} else {
			
			  echo $_GET['callback'] . "(" . json_encode($json) . ")";
			}
			
			return;
		}
	}

?>
