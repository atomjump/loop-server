<?php

//This API class should be included by any Loop-server plugins 
//  The plugin is located in the /plugins/pluginname directory

if(isset($define_classes_path)) {
    //Optionally set $define_classes_path with the root of the loop-server
    require_once($define_classes_path . "classes/cls.basic_geosearch.php");
    require_once($define_classes_path . "classes/cls.layer.php");
    require_once($define_classes_path . "classes/cls.ssshout.php"); 
}




 
 
class cls_plugin_api {
    /*
        Example basic plugin
        <?php
            require_once($start_path . "classes/cls.pluginapi.php");
            
            class plugin_help_is_coming
            {
                function on_message($message_forum_name, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone)
                {
                    //Do your thing in here
                    
                    return true;
                    
                }
            }
        ?>

    */

    
    
    public $job;
    public $debug_parallel = true;		//usually false, but for debugging parallel processing you can switch this on.


	  
	/*
	   A manual database insertion function for plugins 
    */
	
	
	public function db_insert(  $table,                       //AtomJump Loop Server (ssshout by default) database table name eg. "tbl_email"
	                            $insert_field_names_str,    //Insert string e.g. "(var_layers, var_title, var_body, date_when_received, var_whisper)"
	                            $insert_field_data_str)     //Insert values e.g. ('" . clean_data($feed['aj']) . "','". clean_data($subject) . "','" . db_real_escape_string($raw_text) .  "', NOW(), '" . $feed['whisper'] . "') " )    
	{
	    //Returns true for successful, or breaks the server request if unsuccessful, with an error 
	    make_writable_db();
	    $sql = "INSERT INTO " . $table . " " . $insert_field_names_str . " VALUES " . $insert_field_data_str;  
	    dbquery($sql) or die("Unable to execute query $sql " . dberror());
	    return;
	}
		
	
	/* API db wrappers  */
	public function db_fetch_array($result)	
	{
		return db_fetch_array($result);
		
	}
	
	public function db_real_escape_string($str)
	{	
		return db_real_escape_string($str);
	}
	
	public function db_error()
	{
		return dberror();		//Note different spelling internally
	}
	
	public function db_insert_id()
	{
		return db_insert_id();
	}
	

	
			
	/*
	   A manual database update for plugins 
    	*/
	
	public function db_update($table,                       //AtomJump Loop Server ('atomjump' by default) database table name eg. "tbl_email"
	                            $update_set)    //Update set e.g. "var_title = 'test' WHERE var_title = 'test2'"  - can have multiple fields	                          
	{
	    //Returns true for successful, or breaks the server request if unsuccessful, with an error 
	    make_writable_db();
	    $sql = "UPDATE " . $table . " SET " . $update_set;  
	    dbquery($sql) or die("Unable to execute query $sql " . dberror());
	    return;
	}
	
	
	/*
	   A manual database selection for plugins 
    */
	
	
	public function db_select($sql)     
	{
	    //Returns result array when successful, or breaks the server request if unsuccessful, with an error 
	    $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
	    
	    return $result;
	}
	                                     
	
	/*
	    Get a forum id from a forum name for plugins
	*/	
	
	public function get_forum_id($message_forum_name)
	{
	    //Return the forum id object, or false if unsuccessful
	    /*
	    
	        Output:
	        
	        [ forum_id, access_type, forum_group_user_id, requires_password ]
	    
	        Where 'forum_id' e.g. 34
	              'access_type' eg. "readwrite", "read"
	              'forum_owner_user_id' is the user id to send a message to, to become visible to all the private forum owners.
	    		  'requires_password', is true if the forum is password protected, or false if not.
	    
	        Internally:
	            [
	                "myaccess",
	                "int_group_id",
	                layer-group-user,
	                enm_access
	            ]
	            
	              `int_layer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `enm_access` enum('public','public-admin-write-only','private') CHARACTER SET latin1 DEFAULT NULL,
                  `passcode` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
                  `int_group_id` int(10) unsigned DEFAULT NULL,
                  `var_public_code` varchar(255) COLLATE utf8_bin DEFAULT NULL
                  `date_owner_start` datetime DEFAULT NULL,
	    
	    
	    */
	
	    $ly = new cls_layer();
	    
	    $returns = $ly->get_layer_id($message_forum_name, null);
	    
	    if($returns != false) {
	        $output = array();
	        $output['forum_id'] = $returns['int_layer_id'];
	        $output['access_type'] = $returns['myaccess'];
	        $output['forum_owner_user_id'] = $returns['layer-group-user'];
	        if(isset($returns['var_public_code'])) {
	        	$output['requires_password'] = true;
	        } else {
	        	$output['requires_password'] = false;
	        }
	        return $output;
	        
	    } else {
	
    	    return false;
        }
	}

	/*
	    Get the current user's ip address
	*/		
		
	public function get_current_user_ip()
    {	
	    $ly = new cls_layer();
	    return $ly->getFakeIpAddr();
	}	


	/*
	    Get the current user's user id
	*/		
		
	public function get_current_user_id()
    {	
	    
	    return $_SESSION['logged-user'];
	}
	
	
	/*
	    Check if a layer has been granted access by the current user (i.e. the password has been entered by the current user)
	    Note: this will not check if that layer requires a check. If it is a public layer, it does not need this check.
	    See the function get_forum_id() return value
	    Returns: true or false
	*/
	public function is_forum_granted($check_forum_id) 
	{
		
		//Returns true or false
		$layers_granted_array = json_decode($_SESSION['access-layers-granted']);
		if(!is_array($layers_granted_array)) return false;
		return in_array($check_forum_id, $layers_granted_array);
	}
	
	/*
		Input, ideally an integer between 0 - 60 (60 seconds in a minute means this
		is usually the largest number we need for most things, i.e. 59 seconds ago). 
		However, any number can be used and the default is to return the English number
		if there is no matching number in the messages array "number" conversion for the
		input language.
	*/
	public function show_translated_number($number, $lang)
	{
		//Use db_connect version of function
		return show_translated_number($number, $lang);
	
	}
	
	
	/*
	    Run a parallel system process on the server machine
	*/	
	
	
    #
    # control.php
    #
    public function JobStartAsync($server, $url, $port=80,$conn_timeout=30, $rw_timeout=86400)
    {
	    $errno = '';
	    $errstr = '';
	
	    set_time_limit(0);
	
	    $fp = fsockopen($server, $port, $errno, $errstr, $conn_timeout);
	    if (!$fp) {
	       echo "$errstr ($errno)<br />\n";
	       return false;
	    }
	    $out = "GET $url HTTP/1.1\r\n";
	    $out .= "Host: $server\r\n";
	    $out .= "Connection: Close\r\n\r\n";
	
	    stream_set_blocking($fp, false);
	    stream_set_timeout($fp, $rw_timeout);
	    fwrite($fp, $out);
	
	    return $fp;
    }

    // returns false if HTTP disconnect (EOF), or a string (could be empty string) if still connected
    public function JobPollAsync(&$fp) 
    {
	    if ($fp === false) return false;
	
	    if (feof($fp)) {
		    fclose($fp);
		    $fp = false;
		    return false;
	    }
	
	    return fread($fp, 10000);
    }	
		
		
	public function parallel_system_call($command, $platform = "linux", $logfile = "", $server = "")
    {	
        switch($platform) {
            case "linux":
                if($logfile != "") {
                    $logfile = ">" . $logfile;
                }
	        
		        
	            
	            global $process_parallel_url;
	            global $process_parallel;
	            
	            if($server != "") {
	                //This is a URL based request (currently only works against an http server, not https)
	                $process_parallel_url = $this;
	                $this->job = $this->JobStartAsync($server,$command);
	            } else {
	                //This is an ordinary system process
	                if($logfile != "") {
	                    $logfile = " >" . $logfile;
	                
	                }
	                
	               
	                $cmd = "nohup nice -10 " . $command . " > /dev/null 2>&1 &"; 
	                /*if($this->$debug_parallel == true) {
		    			$cmd = $command;
		    		}*/
	                array_push($process_parallel, $cmd);        //Store to be run by index.php at the end of everything else.
	            }	        
		        
		    break;
		    
		    case "windows":
		        //Not yet supported
		    break;
		}
	    return;
	}
	
	
	public function complete_parallel_calls()
	{
		//Handle any post processing
		global $process_parallel;
		global $process_parallel_url;
		global $local_server_path;
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
		    
		    
		    global $cnf;
		    
		    $command = $cnf['phpPath'] . " " . $local_server_path . "run-process.php " . urlencode(json_encode($process_parallel));
		    $cmd = "nohup nice -10 " . $command . " > /dev/null 2>&1 &"; 
		    if($this->$debug_parallel == true) {
		    		$cmd = $command;
		    		error_log($cmd);
		    }
		    $ret = shell_exec($cmd);
			if($this->$debug_parallel == true) {
		    		error_log($ret);
		    }
		
		}
		

		exit(0);		//We don't want to do anything else after a shout, now that it is ajax
	
	}
	
	
		
	
	/*
	    Create a new message function for plugins
	*/	
	
	public function new_message($sender_name_str,                           //e.g. 'Fred'
	                            $message,                                   //Message being sent e.g "Hello world!"
	                            $recipient_id,                              //User id of recipient e.g. "123.123.123.123:436" 
	                            $sender_email,                              //Sender's email address e.g. "fred@company.com"
	                            $sender_ip,                                 //Sender's ip address eg. "123.123.123.123"
	                            $message_forum_id,                        //Forum id e.g. 23, which is derived from a forum name e.g. 'aj_test'
	                            $options = null
	                            )
	 {
	     //Returns the message id if successful, or false if not successful.
	     
	     
	     $bg = new clsBasicGeosearch();
	     $ly = new cls_layer();
	     $sh = new cls_ssshout();
	 
	 
        $sender_still_typing = false;               //Set to true if this is a partially completed message
        $known_message_id = null;                   //If received an id from this function in the past
        $sender_phone = null;                       //Include the phone number for configuration purposes
        $javascript_client_msg_id = null;           //Browser id for this message. Important for 
        $forum_owner_id = null;                     //User id of forum owner
        $social_post_short_code = null;             //eg 'twt' for twitter, 'fcb' for facebook
        $social_recipient_handle_str = null;        //eg. 'atomjump' for '@atomjump' on Twitter
        $date_override = null;                      //optional string for a custom date (as opposed to now) 
        $latitude = 51.0;                            //for potential future location expansion
        $longitude = 0.0;                            //for potential future location expansion
	    $login_as = false;                          //Also login as this user
	    $allow_plugins = false;                     //To prevent infinite message sending loops, we don't refer to any other plugins
	                                                //after a message send
	    $allowed_plugins = null;                    //Use the standard plugins (null), or an array of approved plugins from the plugin
	                                                //developer. However, plugins that work with before_msg will continue to work 
	 	$notification = true;						//Can switch off notifications from this message when set to false.
	 	$always_send_email = false;					//Set to true to ensure an email will always be sent to the recipient(s). Usually, messages within around 20 minutes won't multiple-email the user (when false).
	 
	 
	    if(isset($options)) {
	     if(isset($options['sender_still_typing'])) $sender_still_typing = $options['sender_still_typing'];
	     if(isset($options['known_message_id']))  $known_message_id = $options['known_message_id'];
	     if(isset($options['sender_phone']))  $known_message_id = $options['sender_phone'];
	     if(isset($options['javascript_client_msg_id']))  $javascript_client_msg_id = $options['javascript_client_msg_id'];
	     if(isset($options['forum_owner_id']))  $forum_owner_id = $options['forum_owner_id'];
	     if(isset($options['social_post_short_code']))  $social_post_short_code = $options['social_post_short_code'];
	     if(isset($options['social_recipient_handle_str']))  $social_recipient_handle_str = $options['social_recipient_handle_str'];
	     if(isset($options['date_override']))  $date_override = $options['date_override'];
	     if(isset($options['latitude'])) $latitude = $options['latitude'];
	     if(isset($options['longitude']))  $longitude = $options['longitude'];
	     if(isset($options['login_as']))  $login_as = $options['login_as'];
	     if(isset($options['allow_plugins']))  $allow_plugins = $options['allow_plugins'];
	     if(isset($options['allowed_plugins']))  $allowed_plugins = $options['allowed_plugins'];
	     if(isset($options['notification']))  $notification = $options['notification'];
	     if(isset($options['always_send_email'])) $always_send_email = $options['always_send_email'];
	    }
	 	 
	 	
	     return $sh->insert_shout($latitude,
	                        $longitude,
	                        $sender_name_str,
	                        $message,
	                        $recipient_id,
	                        $sender_email,
	                        $sender_ip, 
	                        $bg,
	                        $message_forum_id,
	                        $sender_still_typing,
	                        $known_message_id,
	                        $sender_phone,
	                        $javascript_client_msg_id,
	                        $forum_owner_id,
	                        $social_post_short_code,
	                        $social_recipient_handle_str,
	                        $date_override,
	                        $login_as,
	                        $allow_plugins,
	                        $allowed_plugins,
	                        $notification,
	                        $always_send_email);
	 
	 }	
	 
	 
	 /*

	    Hide a message, and optionally warn an admin user.
	 */	
	 
	 public function hide_message($message_id, $warn_admin = false)
	 {
	    
	     $ly = new cls_layer();
	     $sh = new cls_ssshout();
	     
	     if($warn_admin = true) {
	        $just_typing = false;
	     } else {
	        $just_typing = true;
	     }	        
	     
	     return $sh->deactivate_shout($message_id, $just_typing);
		 
     }
   

}


?>
