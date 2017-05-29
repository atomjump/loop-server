<img src="https://atomjump.com/images/logo80.png">

# Open Messaging Server

This acts as a server to the AtomJump Loop open source
interface at http://github.com/atomjump/loop


# Requirements

PHP, fully tested on PHP 5.3, 5.5, 7.0 (with curl, php-mbstring, zip, php-xml support added),  
MySQL 5+, 
Apache2,  
Linux server (though a Windows server may be partially functional)  

# Recommended 

Modify upload size in php.ini (usually /etc/php5/apache2/php.ini)
```
upload_max_filesize = 10M
max_execution_time = 200
service apache2 reload
```

Imagemagick can be used (Ubuntu command):
```
sudo apt-get install imagemagick
```

To keep timing in-sync (Ubuntu command):
```
sudo apt-get install ntp
```

# Optional
Amazon MySQL RDS (any number of db servers),  
Load balancers with haproxy,  
SSL server


# Installation

1. /server directory. Replace atomjump with your own domain, and put any relevant files into your Apache 'sites available' setup. You may need to restart Apache.

2. /config/configORIGINAL.json. Copy to config/config.json. Replace the options with your own accounts and paths. Copy /config/messagesORIGINAL.json to /config/messages.json. Replace these options with your own words or languages.

3. Create a temporary image upload directory at
/images/im

```
chmod 777 /images/im
```

4. Copy SET_AS_htaccess to .htaccess and replace atomjump.com with your domain

5. Customer defined themes must be on a secure server if the server is on ssl.

6. To the ajFeedback object in your index.html, add a parameter
```   
 "server": "http://yourserver.com"
```

7. In a MySQL prompt, run 'create database ssshout'. Then from the command line:
```   
 mysql -u youruser -p ssshout < db/atomjump-loop-shell.sql
```


# Optional Installation

Add two cron tasks to your server:

* A typing cleanup task. On rare instances, a 'typing...' message is left (if the machine cut out etc.). This cleans up any of these old messages periodically (every 5 minutes).  

```
sudo crontab -e  
*/5 * * * *	/usr/bin/php /yourserverpath/typing-cron.php  
```

* A sentiment analysis task. This sentiment is reflected when you download a spreadsheet of the messages. It requires nodejs to be installed and available to be run by a cron job.  

On your internet server, first install NodeJS and npm. See Ubuntu install notes at https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-an-ubuntu-14-04-server, but there are several ways to do this depending on your platform e.g. MacOSX may vary slightly.  

```
cd /yourserverpath/node  
npm install  
sudo crontab -e  
 */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js -production  
```

This will update the production database message sentiments once every minute (or remove the -production to go to staging).  


# Windows installation

Running on Windows has currently not been tested, but you can use a Ubuntu 14.04 virtual machine from https://bitnami.com/stack/lamp/virtual-machine


# Messaging Client

This tool provides a 'WhatsApp-like' group discussion forum from a popup on your website. It is good for feedback, but can also be used as a live discussion tool, or a CRM.  We actually run our entire operation off one page with several of these popups on it.

The client software is entirely Javascript and CSS, but it refers to an AtomJump Loop server to store messages.  Supported client platforms: IE8 upwards, Edge, Chrome, Firefox, Safari, Android Native, Android Chrome, Android Firefox, iPad, iPhone, Opera. There may be other supported platforms we haven't tested on.

See the demo at <a href="https://atomjump.com">AtomJump</a>

You are most welcome to adjust the styling by making changes to the CSS file for your project.




## Client Installation

With [bower](http://bower.io) from within the root of your project path:

**`bower install atomjump`**

(Or without bower, unpack into your project, edit index.html, and replace bootstrap css and javascript paths as mentioned)

Run atomjump/index.html in your browser.  You should see a 'Click me for comments' link. Click this to see the pop-up.




## Client Setup

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

1. Adjust 'uniqueFeedbackId' value to a unique value to suit your forum.  This can be unique per page or the same throughout the whole site.

2. Obtain the 'myMachineUser' value by following the sub-steps below:

	1. Settings
	2. Entering an email/Password
	3. Click save
	4. Settings
	5. Clicking: 'Your password', then 'Advanced'
	6. Copy the myMachineUser into the myMachineUser value in your html file.

  This ensures only you as a logged in user will receive messages from your site.
  
3. If you wish to, you can enter your mobile phone number under Settings to receive SMS messages when there are any messages
(at a cost of 16c per message. Messages within 5 minutes of each other do not trigger an SMS).  If you want to 
include an sms modify the myMachineUser string on your page to include the 3rd term 'sms'
e.g. "123.456.123.32:1200:sms".  If you don't include an 'sms', or the server does not include this option, you won't receive sms messages.

We do not currently support SMS messages via the atomjump.com/api server.


## To have more than one messaging forum on a single page

You can do this three different ways. The simplest is to set the link's href to '"#comment-open-' followed by the forum name:
```<a href="#comment-open-my_different_forum_name">Open special forum</a>```

Or, you can add the 'comment-open' class to a link with an 'id' referring to the forum name:
```<a class="comment-open" id="my_different_forum_name" href="javascript:">Open special forum</a>```

Or, for further control over the owner of the forum, you can add the following data tags, and enter your own names/ips:
```<a class="comment-open" data-uniquefeedbackid="my_different_forum_name" data-mymachineuser="10.12.13.14:2" href="javascript:">Open special forum</a>```



## To open a Shortmail enabled forum

You can add the 'shortmail' data tag e.g.

```<a class="comment-open" data-uniquefeedbackid="my_different_forum_name" data-mymachineuser="10.12.13.14:2" data-shortmail="true" href="javascript:">Open special email forum</a>```

Or, you can use an href "#shortmail-open-" class:
```<a href="#email-open-my_shortmail_forum_name">Open shortmail forum</a>```



## To add more than one user to receive messages

Open the messaging forum in your browser.

1. Settings
2. Entering an email/Password
3. Click save
4. Settings
5. Clicking: 'More', then 'Advanced'
6. Edit the 'This forum's private owners' and put in each email separated by a comma. 

Note: you can also add individuals with their ip/user id. In this case 'sms' can be added individually to each user to optionally send an sms also, provided the server supports this (currently atomjump.com/api does not).

e.g. "123.456.123.32:1200:sms,123.456.123.32:1201:sms"


## To change the theme

Add 

"cssBootstrap":"relative/url"

and

"cssFeedback":"relative/url/to/your/css"

to the ajFeedback object.

Note: your css file must be on an https server, if your server is using https.


## To download a forum's messages programmatically

**Endpoint**
http://yourserver.com/download.php

or for atomjump.com's web-service:

http://atomjump.com/api/download.php

**Parameters**

1. **email**
   Your AtomJump Loop email address from the standard interface
2. **pass**  
   Your AtomJump Loop password
3. **uniqueFeedbackId**  
   The particular forum to view. Note: include 'ajps_' at the start of this string for x.atomjump.com forums.
4. **from_id**  
   There is a limit of 2000 records per request. To download more, include the 'id' of the last record, from the previous download request, in this field.


Which returns a JSON object. Included for reporting is a 'sentiment' field which measures how positive the comment is (< 0 negative, 0= neutral, > 0 positive).

## Getting a live sentiment

Include the following parameters along with 1,2, and 3 above.

4. **format**

   Set to 'avg'
   
5. **duration**
 
   Period over which to average in seconds.
   
The response will be an average over the last period of all the message sentiment values.
This will be expressed as a single number eg. 5.324.
Note: it can take up to 1 minute before any new message's sentiment will be calculated.


## Wordpress Setup


1. Install the 'Header and Footer' plugin.
2. Install AtomJump using 'bower' as described above in your Wordpress folder. 
3. Go into 'Settings->Header and Footer', and enter the two sections below (adjusting any paths required to fit your installation)
4. Any link's address (i.e. the 'href') on the page can now start with '#comment-open-', followed by the forum name and it will open a popup.

* Copy into the '<HEAD> SECTION INJECTION' section:

```
<!-- AtomJump Feedback Starts -->
		   <!-- Bootstrap core CSS. Ver 3.3.1 sits in css/bootstrap.min.css -->
			  <link rel="StyleSheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
			
			<!-- AtomJump Feedback CSS -->
			<link rel="StyleSheet" href="/bower_components/atomjump/css/comments-0.9.1.css?ver=1">
			
			<!-- Bootstrap HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
			<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
			<![endif]-->
			
			<script>
				//Add your configuration here for AtomJump Messaging
				var ajFeedback = {
					"uniqueFeedbackId" : "test_feedback",		//This can be anything globally unique to your company/page	
					"myMachineUser" : "92.27.10.17:8",			/* Obtain this value from 1. Settings
																					2. Entering an email/Password
																					3. Click save
																					4. Settings
																					5. Clicking: 'More', then 'Developer Tools'
																					6. Copy the myMachineUser into here.
							
															*/
						"server":  "https://atomjump.com/api"
				}
			</script>
			<script type="text/javascript" src="/bower_components/atomjump/js/chat.js"></script>
<!-- AtomJump Feedback Ends -->
```

* Copy into the 'BEFORE THE </BODY> CLOSING TAG (FOOTER)' section:

```
<!-- Any link on the page can start with '#comment-open-', followed by the forum name and it will open a popup -->
<div id="comment-holder"></div><!-- holds the popup comments. Can be anywhere between the <body> tags -->
```

For more details see
https://atomjump.com




# Server Options



Staging:
To ensure you are set to staging, set the 'usingStaging' param to 'true'.

To test the web domain, use the following small PHP script:

```
<?php
        echo "Server name:" . $_SERVER['SERVER_NAME'];
?>
```

**db** **hosts**: there can be any number of db hosts, but the first one is the only write host, while the others are read.
   We use Amazon MySQL RDS for multiple db hosts.
   
**db** **scaleUp**: For different forums you can refer to completely different databases, to remove the heavy write usage in a multi-read/single write database server farm. This is an array of alternative db/hosts, which are used if a given regular expression is detected in the forum's name.

**db** **scaleUp** **labelRegExp**: This is a javascript/PHP regular expression that changes the database used for this forum. E.g. "^hello", would detect the forums 'hello_there', 'hello_anything' etc. Then the standard db details can be entered for this case i.e. 'name','hosts','user','pass','port','deleteDeletes','timezone'.


**ips**: any number of PHP machines with the server software on it.

**webRoot**: Web path including http/https of the server. Don't include trailing slash.

**fileRoot**: Local file system server's file path. Don't include trailing slash.

**serverTimezone**: Change this to the location of the physical server eg. Pacific/Auckland

**deleteDeletes**: Set to true by default, this means any user action to delete a message removes it completely from the database. If for your records you are required to keep hidden messages, set to false.

**loadbalancer**: Required for a production setup - any number of machines. Can be blank in a staging setup.

**phpPath**: Path to externally run any parallel PHP processes. Ver >= 0.5.5

**logoutPort**: Depreciated ver 0.6.3. Port which a logout is supported on. Default 1444. Use in an ssh situation, whereby this is a non-ssh port. Ver >= 0.5.5, ver <= 0.6.2

**adminMachineUser**: once your server has been set up, and you have saved your first user (yourself typically), find this user in the interface
Advanced settings, and write this into the config. This is the default location for private messages if there is no other
owner of a group.

**analytics** **use**: Can be 'none' or 'piwik'. None switches off analytics, while 'piwik' enables them.

**analytics** **piwik**:  optional. Only needed for retrieving unique backgrounds for subdomains of atomjump.

**social** **use**: Can be 'none' or 'all'. 'none' switches off all social media interaction, while 'all' enables all of them.

**social** **twitter**: optional. Retrieves tweets from twitter related to this subdomain, and allows for replying to the tweets (posts a 
message via twitter).

**readPort**:  optional. The port to put the plugin 'loop-server-fast' daemon on, if it is different to 3277, see https://www.npmjs.com/package/loop-server-fast. Ver >= 0.5.22.

**readURL**: optional. The full URL including ports that the plugin 'loop-server-fast' daemon is on, see https://www.npmjs.com/package/loop-server-fast, if this is different to the standard URL followed by 'readPort'. Note: if you use a non-standard port, some machines behind proxy servers, particularly corporates, or some public PCs may have the address filtered out. One approach here, at a slight loss of speed, is to use the standard port 80 for http and 443 for https, and ProxyPass in Apache. Ver >= 1.5.5.

**httpsKey**:  optional. If you are serving from an https address, you will need this local file path, for the plugin 'loop-server-fast', see https://www.npmjs.com/package/loop-server-fast. See also 'httpsCert', which is needed too. Ver >= 0.5.22

**httpsCert**:  optional. If you are serving from an https address, you will need this local file path, for the plugin 'loop-server-fast', see https://www.npmjs.com/package/loop-server-fast. See also 'httpsKey', which is needed too. Ver >= 0.5.22

**uploads** **use**:  This can be one of 'none', 'same', 'generic', 'amazonAWS'. 'none' means uploads are switched off. 'same' means the upload stays on the same server in the '/images/im/' folder. 'generic' means uploads.genericUploadURL should be set to a remote URL, which the image file will be uploaded to via a POST request. 'amazonAWS' refers to the use of an Amazon S3 AWS bucket for remote storage. You will need an Amazon 'accessKey', 'secretKey' and 'imageURL', in this case.

Note: You should make sure your server provides a caching response to image files, or Safari will continue to refresh the images every 5 seconds.

**uploads** **imagesShare**: If there are multiple PHP nodes, this defines which port to write uploaded images to, so that they are shared between nodes. 'Port' is a port such as 80, and 'https' is either true or false.

**uploads** **vendor** **amazonAWS**: optional. Currently required for image uploads. This is for S3 storage of uploaded images. You will need an Amazon 'accessKey', 'secretKey' and 'imageURL', in this case.


**email** **adminEmail**: Administrator's email address.

**email** **webmasterEmail**: The webmaster's email address.

**email** **noReplyEmail**: An email address for when you do not want a reply.

**email** **sending** **use**: This can be 'none', 'smtp' or 'mailgun'. 'none' means there are no emails sent as notifications. 'smtp' means a standard SMTP server is used, and you should enter the 'smtp' which is the host, 'user' which is the username, typically the email address, 'pass' which is the password, 'encryption' which can be 'tls', 'ssl' or left blank, and the 'port' which is the SMTP port number used. 'mailgun' means the Mailgun.com service is used and you will need a 'key' and 'url' from Mailgun.

For example, atomjump.com uses http://smtp2go.com, who provide an excellent email sending service: 
```
   "smtp": "mail.smtp2go.com",
   "user" : "...@atomjump.com",
   "pass": "...",
   "port": "2525"
```

**email** **sending** **vendor** **mailgun**: optional. You need a mailgun account which has about 10,000 free emails per month, but costs after that. You will need a 'key' and 'url' from Mailgun.

**sms** **use**: This can be 'none' or 'twilioSMS'. None switches off SMS.

**sms** **twilioSMS**: optional. Required for sending off SMSes via Twilio.

**sms** **USDollarsPerSMS**: for reporting purposes only. This is what is shown to users if they choose to use the SMS notifications.
Since there is a cost to you for each SMS, you will likely set this slightly higher than the actual cost of an SMS, to
account for fluctuations in price.


# Changing language packs

Please see the /config/language-packs/README.md file for instructions on changing which languages are available to your users.


# Plugins

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

Warning: if your plugin is a standalone script (as opposed to being called from the main server), you will need to also
call

```
$api->complete_parallel_calls(); 
```
if you want the notifications to be sent, as these happen in parallel.


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

Run a unix shell command in a parallel process. This allows e.g. a process to run in the background and after a period of time do some action. Also see complete_parallel_calls().



**complete_parallel_calls()**

No parameters.

Server: >= 0.9.0

This should be called at the end of your script to complete all of the parallel_system_call() commands, if your script is a standalone script (i.e. not called via a hook).  It will hard exit the script after completion, and close down the sessions.




# Performance

Performance depends on a number of factors. In general, since the Loop Server can be configured with a load balancer, multiple PHP servers and multiple database servers, the number of users supported can scale <i>almost</i> by adding more servers to the cluster. The basic PHP script on one server should handle (in the order of) one hundred simultaneous users, but this will vary considerably on the hardware used, the amount of usage and the type of usage by these users.

However, to extend performance into larger scale environments, we have released the NodeJS 'loop-server-fast' plugin at https://www.npmjs.com/package/loop-server-fast.  This aims to scale to thousands, or tens of thousand, simultaneous users per server. You can start by installing the PHP version, and then install the NodeJS version as you expand (you can revert back to the PHP version, also, if you have any issues).


# Contributing

Contributions are welcome, and they can take the shape of:

1. Core: Submit github pull requests. We will need to consider whether the feature should be in core, or externally as a plugin.  It is generally a good idea to get in touch with us via our homepage at https://atomjump.com to ensure we are not already working on a similar feature.
2. Plugins: Develop to the API above, and then publish on the https://atomjump.com/smart.php 'available plugins' link.  You are free to write or publish (or keep private) any number of plugins.
3. Translations: We are looking for any translations of the conf/messages.json file from English into your language. We have machine translated Spanish as a first step, but we need help in ensuring this is a good translation, and applying other languages.


# License

This software is open source under the MIT license. Copyright is with AtomJump Ltd. (New Zealand).  You can use this software for any commercial purposes.


