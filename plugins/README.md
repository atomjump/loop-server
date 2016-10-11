## Plugins

Plugins can be installed in the /plugins directory.
See https://atomjump.com/smart.php for a list of plugins available.


## Plugin API

For a sample plugin called 'hide_aargh':

/plugins/hide_aargh/index.php

```php
<?php
    include_once("classes/cls.pluginapi.php");
    
    class plugin_hide_aargh
    {
        public function on_message($message_forum_id, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone)
        {
            //Do your thing in here. Here is a sample.
            $api = new cls_plugin_api();
          
            //e.g. hide the message we have just posted if it includes the string 'aargh' in it.
            if(strpos($message, 'aargh') !== false) {
                $api->hide_message($message_id);
            }
            
            return true;
            
        }
    }
?>
```

Add the entry "hide_aargh" to the "plugins" array in config/config.json to activate the plugin.

## Reading functions


**db_insert()**

Parameters
($table, $insert_field_names_str, $insert_field_data_str)  
Server: >= 0.5.0

```
  $table                     //AtomJump Loop Server (ssshout by default) database table name eg. "tbl_email"
  $insert_field_names_str    //Insert string e.g. "(var_layers, var_title, var_body, date_when_received, var_whisper)"
  $insert_field_data_str     //Insert values e.g. ('" . clean_data($feed['aj']) . "','". clean_data($subject) . "','" . db_real_escape_string($raw_text) .  "', NOW(), '" . $feed['whisper'] . "') " )    
```

**db_select()**

Parameters
($sql)  
Server: >= 0.5.0

An ordinary SELECT SQL query.

**db_update()**

Parameters
($table, $update_set)  
Server: >= 0.5.0

Update_set is the SQL after an 'UPDATE table SET' e.g. "var_title = 'test' WHERE var_title = 'test2'"  - can have multiple fields

**db_fetch_array()**

Parameters
($results)  
Server: >= 0.5.21

Get an array of rows from a database query.

**db_real_escape_string()**

Parameters
($string)  
Server: >= 0.5.21

Ensure the string is escaped for input into the database.


**db_error()**

No parameters
Server: >= 0.5.21

Returns the database error text.


**db_insert_id()**

No parameters
Server: >= 0.5.21

Get the last database inserted id field value.

**get_forum_id()**

Parameters
($message_forum_name)  
Server: >= 0.5.0

```
Output is an array:
[ forum_id, access_type, forum_group_user_id ]

Where 'forum_id' e.g. 34
	  'access_type' eg. "readwrite", "read"
	  'forum_owner_user_id' is the user id to send a message to, to become visible to all the private forum owners.
```


**get_current_user_ip()**

No parameters  
Server: > 0.5.0

Get the user's ip address, although this is now an artificial ip address, actually a unique 'ip' based off their user id.


**get_current_user_id()**

No parameters  
Server: >= 0.5.0

Returns the integer user id value of the current user.







## Hooks

**on_message()**

Output parameters
($message_forum_id, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone, $message_forum_name)  
Plugin function returns  
(true)  
Server: >= 0.5.1
($message_forum_name >= 0.7.6)

Occurs after a message being posted.


**before_message()**

Output parameters
($message)  
Plugin function returns  
($message)  
Server: >= 0.5.0

Occurs just before a message is posted, allowing changes to the message text.


**on_more_settings()**

Output parameters
($message)  
Plugin function writes HTML inside ordinary PHP tags e.g. 

```php
?>Your settings HTML<?php  
```

Server: >= 0.5.9  


**on_save_settings()**

Output parameters
($user_id, $full_request: array of options, $type: NEW or SAVE)
Plugin function returns  
(true/false)  

Called when the settings are saved. $user_id is the integer user's id being saved. $full_request is the $_REQUEST array with the user's entered values. $type can be 'NEW' if it is a new record, or 'SAVE' to save an existing record.


**on_upload_screen()**

Output parameters
($message)  
Plugin function writes HTML inside ordinary PHP tags e.g. 

```php
?>Your settings HTML<?php  
```

Server: >= 0.5.9  


**on_notify()**

Output parameters
($stage, $message, $message_details, $message_id, $sender_id, $recipient_id, $in_data)  
Plugin function should return
($ret, $ret_data)
where $ret is true, unless a recipient is not to get a message, when your plugin function should return $ret = false

$ret_data should be used to forward the data on to the next stage function, in the $in_data parameter.

Server: >= 0.7.7  

This hook allows you to embed custom code into a notification e.g. the plugin 'notifications' uses this function to send messages to the AtomJump phone app.

Used at 3 stages of the notification process, defined by $stage. Stages are:

```
1. init          - this call occurs once the first notification in a group is reached
2. addrecipient  - this occurs for each recipient in the group
3. send			 - this occurs on being ready to send 
```

$message_details will be an array in the following format:

```
array("observe_message" => $observe_message,		//This is the message to be written next to the visual link to the forum
		 "observe_url" => $observe_url,             //This is the URL of the visual link to the forum
		 "forum_message" => $layer_message,			//A message that preceeds the name of the forum the message was sent from
		 "forum_name" => $layer_name,				//The name of the forum the message was sent from
		 "remove_message" => $remove_message,		//A description of what to do to remove the message
		 "remove_url" => $remove_url);              //The URL to remove the message
```




## Writing functions

**new_message()**

Required Parameters
($sender_name_str, $message, $recipient_ip_colon_id, $sender_email, $sender_ip, $message_forum_id, $options)  
Server: >= 0.5.0

Optional Parameters in an $options object:
($sender_still_typing, $known_message_id, $sender_phone, $javascript_client_msg_id, $forum_owner_id, $social_post_short_code,   $social_recipient_handle_str, $date_override, $latitude, $longitude, $login_as, $allow_plugins, $allowed_plugins, $notification, $always_send_email)  
Server: >= 0.5.0

Additions:
($login_as), server >= 0.5.1  
($allow_plugins, $allowed_plugins), server >= 0.5.5
($notification, $always_send_email), server >= 1.0.4

Output parameters
($message_id)

```php
	$sender_name_str,                           //e.g. 'Fred'
	$message,                                   //Message being sent e.g "Hello world!"
	$recipient_id,                              //User id of recipient e.g. "123.123.123.123:436" 
	$sender_email,                              //Sender's email address e.g. "fred@company.com"
	$sender_ip,                                 //Sender's ip address eg. "123.123.123.123"
	$message_forum_id,                          //Forum id e.g. 23, which is derived from a forum name e.g. 'aj_test'

Options

	$sender_still_typing = false;               //Set to true if this is a partially completed message
	$known_message_id = null;                   //If received an id from this function in the past
	$sender_phone = null;                       //Include the phone number for configuration purposes
	$javascript_client_msg_id = null;           //Browser id for this message.
	$forum_owner_id = null;                     //User id of forum owner
	$social_post_short_code = null;             //eg 'twt' for twitter, 'fcb' for facebook
	$social_recipient_handle_str = null;        //eg. 'atomjump' for '@atomjump' on Twitter
	$date_override = null;                      //optional string for a custom date (as opposed to now) 
	$latitude = 0.0;                            //for potential future location expansion
	$longitude = 0.0;                            //for potential future location expansion
	$login_as = false;                          //Also login as this user
	$allow_plugins = false;                     //To prevent infinite message sending loops, we don't refer to any other plugins
												//after a message send
	$allowed_plugins = null;                    //Use the standard plugins (null), or an array of approved plugins from the plugin
												//developer. However, plugins that work with before_msg will continue to work 
	$notification = true;						//Set to false to switch off notifications from this message
	$always_send_email = false;					//Set to true to ensure an email will always be sent to the recipient(s). Usually, messages within around 20 minutes won't multiple-email the user (when false).
```

Usage example:

```php
    $options = array('notification' => false);		//turn off any notifications from these messages
                        
                        
    $new_message_id = $api->new_message($helper, $new_message, $sender_ip . ":" . $sender_id, $helper_email, $sender_ip, $message_forum_id, $options);
```


**hide_message()**

Required Parameters
($message_id)  
Server: >= 0.5.0

Optional Parameters
($warn_admin)  
Server: >= 0.5.0

Hides a message from view, where $message_id is an integer. $warn_admin can be true/false to warn the owner of the forum of the message being hidden.


## Misc

**parallel_system_call()**

Required Parameters
($command)  
Server: >= 0.5.5

Optional Parameters
($platform:'linux'/'windows' default:'linux')

Run a unix shell command in a parallel process. This allows e.g. a process to run in the background and after a period of time do some action.  Also see complete_parallel_calls().



**complete_parallel_calls()**

No parameters.

Server: >= 0.9.0

This should be called at the end of your script to complete all of the parallel_system_call() commands, if your script is a standalone script (i.e. not called via a hook).  It will hard exit the script after completion.




