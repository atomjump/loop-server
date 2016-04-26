<?php

//This API class should be included by any Loop-server plugins 
//  The plugin is located in the /plugins/pluginname directory
 
require_once("../../classes/cls.basic_geosearch.php");
require_once("../../classes/cls.layer.php");
require_once("../../classes/cls.ssshout.php"); 





 
 
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

    


	  
	/*
	   A manual database insertion function for plugins 
    */
	
	
	public function db_insert($table,                       //AtomJump Loop Server (ssshout by default) database table name eg. "tbl_email"
	                            $insert_field_names_str,    //Insert string e.g. "(var_layers, var_title, var_body, date_when_received, var_whisper)"
	                            $insert_field_data_str)     //Insert values e.g. ('" . clean_data($feed['aj']) . "','". clean_data($subject) . "','" . mysql_real_escape_string($raw_text) .  "', NOW(), '" . $feed['whisper'] . "') "
	{
	    //Returns true for successful, or breaks the server request if unsuccessful, with an error 
	    
	    //TODO: ensure this uses a more modern type of mysql insertion.
	    $sql = "INSERT INTO " . $table . " " . $insert_field_names_str . " VALUES " . $insert_field_data_str;  
	    mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
	    return;
	}
	
	
	
	/*
	   A manual database selection for plugins 
    */
	
	
	public function db_select($sql)     
	{
	    //Returns result array when successful, or breaks the server request if unsuccessful, with an error 
	    
	    //TODO: ensure this uses a more modern type of mysql connection.
	    $result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
	    
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
	        
	        [ forum_id, access_type, forum_group_user_id ]
	    
	        Where 'forum_id' e.g. 34
	              'access_type' eg. "readwrite", "read"
	              'forum_owner_user_id' is the user id to send a message to, to become visible to all the private forum owners.
	    
	    
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
                  `var_public_code` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                  `var_owner_string` varchar(255) COLLATE utf8_bin DEFAULT NULL,
                  `date_owner_start` datetime DEFAULT NULL,
	    
	    
	    */
	
	    $ly = new cls_layer();
	    
	    $returns = $ly->get_layer_id($message_forum_name, null);
	    
	    if($returns != false) {
	        $output = new array();
	        $output['forum_id'] = $returns['int_layer_id'];
	        $output['access_type'] = $returns['myaccess'];
	        $output['forum_owner_user_id'] = $returns['layer-group-user'];
	        return $output;
	        
	    } else {
	
    	    return false;
        }
	}
	
		
	
	/*
	    Create a new message function for plugins
	*/	
	
	public function new_message($sender_name_str,                           //e.g. 'Fred'
	                            $message,                                   //Message being sent e.g "Hello world!"
	                            $recipient_id,                              //User id of recipient e.g. 436 
	                            $sender_email,                              //Sender's email address e.g. "fred@company.com"
	                            $sender_ip,                                 //Sender's ip address eg. "123.123.123.123"
	                            $message_forum_name,                        //Forum name e.g. 'aj_interesting'
	                            $sender_still_typing = false,               //Set to true if this is a partially completed message
	                            $known_message_id = null,                   //If received an id from this function in the past
	                            $sender_phone = null,                       //Include the phone number for configuration purposes
	                            $javascript_client_msg_id = null,           //Browser id for this message. Important for 
	                            $forum_owner_id = null,                     //User id of forum owner
	                            $social_post_short_code = null,             //eg 'twt' for twitter, 'fcb' for facebook
	                            $social_recipient_handle_str = null,        //eg. 'atomjump' for '@atomjump' on Twitter
	                            $date_override = null,                      //optional string for a custom date (as opposed to now) 
	                            $latitude = 0.0,                            //for potential future location expansion
	                            $longitude = 0.0                            //for potential future location expansion
	                            )
	 {
	     //Returns the message id if successful, or false if not successful.
	     
	     $bg = new clsBasicGeosearch();
	     $ly = new cls_layer();
	     $sh = new cls_ssshout();
	 
	     return $sh->insert_shout($latitude,
	                        $longitude,
	                        $sender_name_str,
	                        $message,
	                        $recipient_id,
	                        $sender_email,
	                        $sender_ip, 
	                        $bg,
	                        $message_forum_name,
	                        $sender_still_typing,
	                        $known_message_id,
	                        $sender_phone,
	                        $javascript_client_msg_id,
	                        $forum_owner_id,
	                        $social_post_short_code,
	                        $social_recipient_handle_str,
	                        $date_override);
	 
	 }	
		 

   

}


?>
