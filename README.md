<img src="https://atomjump.com/images/logo80.png">

# Smart Feedback Server

This acts as a server to the AtomJump Loop open source
interface at http://github.com/atomjump/loop


# Requirements

PHP 5, fully tested on PHP 5.3,
MySQL 5+, 
Apache2,
Linux server (though a Windows server may be partially functional)

# Optional
Amazon MySQL RDS (any number of db servers),
Load balancers haproxy,
SSL server


# Server Setup

1. /server directory. Replace atomjump with your own domain.

2. /config/configORIGINAL.json. Copy to config/config.json. Replace the options with your own accounts and paths.

3. Create a temporary image upload directory at
/images/im

chmod 777 /images/im


4. Copy SET_AS_htaccess to .htaccess and replace atomjump.com with your domain

5. Customer defined themes must be on a secure server if the server is on ssl.

6. To the ajFeedback object in your index.html, add a parameter
    "server": "http://yourserver.com"

7. In a MySQL prompt, run 'create database ssshout'. Then from the command line:
    mysql -u youruser -p ssshout < db/atomjump-loop-shell.sql

#Known project issues
Currently using an old style mysql php connection. We will try to update this
when possible.







# Smart Feedback Client

This tool provides a 'WhatsApp-like' group discussion forum from a popup on your website. It is ideal for feedback, but can also be used as a live discussion tool, or a CRM.  We actually run our entire operation off one page with several of these popups on it.

The client software is entirely Javascript and CSS, but it refers to an AtomJump Loop server to store messages.  Supported client platforms: IE8 upwards, Chrome, Firefox, Safari, Android Native, Android Chrome, Android Firefox, iPad, iPhone, Opera. There may be other supported platforms we haven't tested on.

See the demo at <a href="https://atomjump.com">AtomJump</a>

You are most welcome to adjust the styling by making changes to the CSS file for your project.




# Client Installation Instructions

With [bower](http://bower.io) from within the root of your project path:

**`bower install atomjump`**

(Or without bower, unpack into your project, edit index.html, and replace bootstrap css and javascript paths as mentioned)

Run atomjump/index.html in your browser.  You should see a 'Click me for comments' link. Click this to see the pop-up.




# Client Setup Instructions

Look carefully at the index.html example.

The code between
`<!-- AtomJump Feedback Starts -->`
 
 and
 `<!-- AtomJump Feedback Ends -->`
 
 should be put into your page's `<head>` section.

Links can be added to the comments with
`<a class="comment-open" href="javascript:">Click me for comments</a>`

The code 
`<div id="comment-holder"></div>`
must be placed anywhere in the `<body>` section.
	 
Note: jQuery ver 1.9.1 is used.  Other jQuery versions will likely work also.

1. Adjust 'uniqueFeedbackId' value to a unique value to suit your feedback.  This can be unique per page or the same throughout the whole site.

2. Obtain the 'myMachineUser' value by following the sub-steps below:

	1. Settings
	2. Entering an email/Password
	3. Click save
	4. Settings
	5. Clicking: 'Your password', then 'Advanced'
	6. Copy the myMachineUser into the myMachineUser value in your html file.

  This ensures only you as a logged in user will receive feedback from your site.
  
3. If you wish to, you can enter your mobile phone number under Settings to receive SMS messages when there is any feedback
(at a cost of 16c per message. Messages within 5 minutes of each other do not trigger an SMS).  If you want to 
include an sms modify the myMachineUser string on your page to include the 3rd term 'sms'
e.g. "123.456.123.32:1200:sms".  If you don't include an 'sms', you won't receive sms messages.

If you wish to send SMS messages, we will keep track of messages sent, and charge independently based on usage, on a monthly basis.


# To have more than one feedback forum on a single page

Add the following data tags, and enter your own names/ips:
```<a class="comment-open" data-uniquefeedbackid="my_different_forum_name" data-mymachineuser="10.12.13.14:2" href="javascript:">Open special forum</a>```


# To add more than one user to receive feedback

Open the feedback forum in your browser.

1. Settings
2. Entering an email/Password
3. Click save
4. Settings
5. Clicking: 'Your password', then 'Advanced'
6. Edit the 'This forum's private owners' and put in each myMachineUser separated by a comma. 'sms' can be added individually to each user to optionally send an sms also.

e.g. "123.456.123.32:1200:sms,123.456.123.32:1201:sms"


# To change the theme

Add 

"cssBootstrap":"relative/url"

and

"cssFeedback":"relative/url/to/your/css"

to the ajFeedback object.

Note: your css file must be on an https server, if your server is using https.


# To download a forum's messages programmatically

**Endpoint**
http://yourserver.com/download.php

**Parameters**

1. **email**
   Your AtomJump Loop email address from the standard interface
2. **pass**  
   Your AtomJump Loop password
3. **uniqueFeedbackId**  
   The particular forum to view.
4. **from_id**  
   There is a limit of 2000 records per request. To download more, include the 'id' of the last record, from the previous download request, in this field.


Which returns a JSON object. Included for reporting is a 'sentiment' field which measures how positive the comment is (< 0 negative, 0= neutral, > 0 positive).

# Getting a live sentiment

Include the following parameters along with 1,2, and 3 above.

4. **format**

   Set to 'avg'
   
5. **duration**
 
   Period over which to average in seconds.
   
The response will be an average over the last period of all the message sentiment values.
This will be expressed as a single number eg. 5.324.
Note: it can take up to 1 minute before any new message's sentiment will be calculated.


For more details see
https://atomjump.com


## Server Options



Production:
The port 1444 code is only used in a production install, and you must have configured haproxy. This is mapped inside the haproxy config 
back to port 80 for a normal http request.

Staging:
To ensure you are set to staging, set the 'webDomain' param in the config.json to the raw local domain or hostname actually used.
E.g. an ip address would be '192.168.40.10' or a domain would be 'subdomain.yourdomain.com'. If you change from an ip
to a domain, ensure you switch over this param at the time of the switchover, because the server defaults to production.
A staging server does not need haproxy to be configured.

To test the web domain, use the following small PHP script:

<?php
        echo "Server name:" . $_SERVER['SERVER_NAME'];
?>

**db** **hosts**: there can be any number of db hosts, but the first one is the only write host, while the others are read.
   We use Amazon MySQL RDS for multiple db hosts.

**ips**: any number of PHP machines with the server software on it.

**webRoot**: Web path including http/https of the server. Don't include trailing slash.

**fileRoot**: Local file system server's file path. Don't include trailing slash.

**serverTimezone**: Change this to the location of the physical server eg. Pacific/Auckland

**loadbalancer**: Required for a production setup - any number of machines. Can be blank in a staging setup.

**adminMachineUser**: once your server has been set up, and you have saved your first user (yourself typically), find this user in the interface
Advanced settings, and write this into the config. This is the default location for private messages if there is no other
owner of a group.

**mailgun**: optional. If you do not want emails being sent off, you can leave this off. You need a mailgun account otherwise
which has about 10,000 free emails per month, but costs after that.

**amazonAWS**: optional. Currently required for image uploads. This is for S3 storage of uploaded images.

**USDollarsPerSMS**: for reporting purposes only. This is what is shown to users if they choose to use the SMS notifications.
Since there is a cost to you for each SMS, you will likely set this slightly higher than the actual cost of an SMS, to
account for fluctuations in price.

**twilioSMS**: optional. Required for sending off SMSes.

**piwik**:  optional. Only needed for retrieving unique backgrounds for subdomains of atomjump.

**twitter**: optional. Retrieves tweets from twitter related to this subdomain, and allows for replying to the tweets (posts a 
message via twitter).


## Plugins

Plugins can be installed in the /plugins directory.
See https://atomjump.com/smart.php for a list of plugins available.


## Plugin API

For a sample plugin called 'help_is_coming':

/plugins/help_is_coming/index.php

```<?php
    include_once("classes/cls.pluginapi.php");
    
    class plugin_help_is_coming
    {
        function on_message($message_forum_name, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone)
        {
            //Do your thing in here. Here is a sample.
            $api = new cls_api();
            
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

**get_forum_id**

Parameters
($message_forum_name)



# Hooks

**on_message**

Output parameters
($message_forum_name, $message, $message_id, $sender_id, $recipient_id, $sender_name, $sender_email, $sender_phone)


# Writing functions

**new_message**

Required Parameters
($sender_name_str, $message, $recipient_id, $sender_email, $sender_ip, $message_forum_name)

Optional Parameters
($sender_still_typing, $known_message_id, $sender_phone, $javascript_client_msg_id, $forum_owner_id, $social_post_short_code,   $social_recipient_handle_str, $date_override, $latitude, $longitude)


**hide_message**

Required Parameters
($message_id)

Optional Parameters
($warn_admin)

