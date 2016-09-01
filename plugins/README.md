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
        public function on_message($message_forum_id, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone, $message_forum_name)
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

**db_select()**

Parameters
($sql)  
Server: >= 0.5.0

**db_update()**

Parameters
($table, $update_set)  
Server: >= 0.5.0


**db_fetch_array()**

Parameters
($results)  
Server: >= 0.5.21

**db_real_escape_string()**

Parameters
($string)  
Server: >= 0.5.21


**db_error()**

No parameters
Server: >= 0.5.21


**db_insert_id()**

No parameters
Server: >= 0.5.21


**get_forum_id()**

Parameters
($message_forum_name)  
Server: >= 0.5.0

**get_current_user_ip()**

No parameters  
Server: > 0.5.0


**get_current_user_id()**

No parameters  
Server: >= 0.5.0









## Hooks

**on_message()**

Output parameters
($message_forum_id, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone, $message_forum_name)  
Plugin function returns  
(true)  
Server: >= 0.5.1
($message_forum_name >= 0.7.6)

**before_message()**

Output parameters
($message)  
Plugin function returns  
($message)  
Server: >= 0.5.0


**on_more_settings()**

Output parameters
($message)  
Plugin function writes HTML inside ordinary PHP tags e.g. ?>Your settings HTML<?php  
Server: >= 0.5.9  


**on_save_settings()**

Output parameters
($user_id, $full_request: array of options, $type: NEW or SAVE)
Plugin function returns  
(true/false)  


**on_upload_screen()**

Output parameters
($message)  
Plugin function writes HTML inside ordinary PHP tags e.g. ?>Your settings HTML<?php  
Server: >= 0.5.9  



## Writing functions

**new_message()**

Required Parameters
($sender_name_str, $message, $recipient_ip_colon_id, $sender_email, $sender_ip, $message_forum_name, $options)  
Server: >= 0.5.0

Optional Parameters in an $options object:
($sender_still_typing, $known_message_id, $sender_phone, $javascript_client_msg_id, $forum_owner_id, $social_post_short_code,   $social_recipient_handle_str, $date_override, $latitude, $longitude, $login_as, $allow_plugins, $allowed_plugins)  
Server: >= 0.5.0

Additions:
($login_as), server >= 0.5.1  
($allow_plugins, $allowed_plugins), server >= 0.55

Output parameters
($message_id)


**hide_message()**

Required Parameters
($message_id)  
Server: >= 0.5.0

Optional Parameters
($warn_admin)  
Server: >= 0.5.0


## Misc

**parallel_system_call()**

Required Parameters
($command)  
Server: >= 0.5.5

Optional Parameters
($platform:'linux'/'windows' default:'linux')
