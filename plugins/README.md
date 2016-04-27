## Plugins

Plugins can be installed in the /plugins directory.
See https://atomjump.com/smart.php for a list of plugins available.


## Plugin API

For a sample plugin called 'help_is_coming':

/plugins/help_is_coming/index.php

```
<?php
    include_once("classes/cls.pluginapi.php");
    
    class plugin_help_is_coming
    {
        public function on_message($message_forum_name, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone)
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

# Reading functions


**db_insert()**

Parameters
($table, $insert_field_names_str, $insert_field_data_str)  


**db_select()**

Parameters
($sql)


**db_update()**

Parameters
($table, $update_set)


**get_forum_id()**

Parameters
($message_forum_name)



# Hooks

**on_message()**

Output parameters
($message_forum_name, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone)


# Writing functions

**new_message()**

Required Parameters
($sender_name_str, $message, $recipient_id, $sender_email, $sender_ip, $message_forum_name, $options)

Optional Parameters in an $options object:
($sender_still_typing, $known_message_id, $sender_phone, $javascript_client_msg_id, $forum_owner_id, $social_post_short_code,   $social_recipient_handle_str, $date_override, $latitude, $longitude)


**hide_message()**

Required Parameters
($message_id)

Optional Parameters
($warn_admin)

