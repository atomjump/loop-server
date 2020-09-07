<img src="https://atomjump.com/images/logo80.png">

# AtomJump Messaging Server

This acts as a server to the AtomJump Messaging front-end 
at http://github.com/atomjump/loop


# Requirements

* PHP. This software has been fully tested on PHP 5.3, 5.5, 7.0, 7.1 (with curl, php-mbstring, zip, php-xml support added) 
* MySQL 5+ 
* Apache 2  
* Linux server (though a Windows server may be partially functional)  

# Recommended pre-installation steps

Modify the upload size in php.ini (found in e.g. /etc/php5/apache2/php.ini)
```
upload_max_filesize = 10M
max_execution_time = 200
service apache2 reload
```

Imagemagick can be used to handle image uploads (Ubuntu command):
```
sudo apt-get install imagemagick
```

To keep timing in-sync (Ubuntu command):
```
sudo apt-get install ntp
```

# Optional Components

* Multi-server MySQL clusters (single-write or multi-write)  
* Load balancers with haproxy  
* SSL messaging server
* SSL database connection



# Installation

On your Linux server, download and unzip the latest release of the loop-server from https://github.com/atomjump/loop-server/releases

```
https://github.com/atomjump/loop-server/releases
```

Or git clone 

```
git clone https://github.com/atomjump/loop-server.git
```

Or using composer https://getcomposer.org/ see https://packagist.org/packages/atomjump/loop-server
```
composer require atomjump/loop-server
```


We will refer to paths as being from the root of the loop-server folder.

1. /server folder. You can refer to some example server configuration files. Replace atomjump with your own domain, and put any relevant files into your Apache 'sites available' setup. You may need to restart Apache.

2. /config/configORIGINAL.json. Copy this file to /config/config.json. Replace the options with your own accounts and paths. Copy /config/messagesORIGINAL.json to /config/messages.json. Replace these options with your own words or languages.

3. Allow image uploads, and image caching by the browser

```
a2enmod expires
chmod 777 /images/im
```

4. Copy /SET_AS_htaccess to /.htaccess and replace atomjump.com with your domain in this file.

5. Customer defined themes must be on a secure server if the server is on ssl.

6. To the ajFeedback object in your index.html, add a parameter
```   
 "server": "http://yourserver.com"
```

7. In a MySQL prompt, run 'create database atomjump'. Then from the command line:
```   
 mysql -u youruser -p atomjump < db/atomjump-loop-shell.sql
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

**(sometimes called the 'loop' project)**

Is available from
https://github.com/atomjump/loop#loop 
or you can add this to your site from
http://atomjump.org/wp/add-atomjump-messaging-to-your-site/

This tool provides a 'WhatsApp-like' group discussion forum from a popup on your website. It is good for general messaging, either within a company or family, in either a private or public setting.  

The client software is entirely Javascript and CSS, but it refers to an AtomJump Messaging server to store messages. This is freely available as a separate project at https://github.com/atomjump/loop-server, or you can use the AtomJump.com server, by default.

Private forums on the hosted AtomJump.com hosted platform are purchased for US$10 / year to cover the (non-profit) Foundation's costs, but public forums are free, within reason.

Supported client platforms: IE8 upwards, Edge, Chrome, Firefox, Safari, Android Native, Android Chrome, Android Firefox, iPad, iPhone, Opera. There may be other supported platforms we haven't tested on.

See the demo at <a href="http://atomjump.org">AtomJump</a>

You can adjust the styling by making changes to the CSS file for your project.





## Client Installation

With [bower](http://bower.io) from within the root of your project path:

**`bower install atomjump`**

(Or without bower, unpack into your project, edit index.html, and replace bootstrap css and javascript paths as mentioned)

Run atomjump/index.html in your browser.  You should see a 'Click me for comments' link. Click this to see the pop-up. For Wordpress instructions, see below.




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

1. Adjust 'uniqueFeedbackId' value to a unique value to suit your forum's name.  This can be unique per page or the same throughout the whole site.

2. For messaging usage, refer to the Messaging guide at 
  http://atomjump.org/wp/user-guide/


## To have more than one messaging forum on a single page

You can do this three different ways. The simplest is to set the link's href to '"#comment-open-' followed by the forum name:
```<a href="#comment-open-my_different_forum_name">Open special forum</a>```

Or, you can add the 'comment-open' class to a link with an 'id' referring to the forum name (you also need a 'data-useid' tag):
```<a class="comment-open" id="my_different_forum_name" data-useid="true"  href="javascript:">Open special forum</a>```

Or, for further control over the owner of the forum, you can add the following data tags, and enter your own names/ips:
```<a class="comment-open" data-uniquefeedbackid="my_different_forum_name" data-mymachineuser="10.12.13.14:2" href="javascript:">Open special forum</a>```


## To open custom URLs within notifications

When the app or email setup sends notifications, it provides a clickable address for the user to go to in order to see the forum. If the usual current web address is behind a token or password, this link needs a lead-up page to allow for client-side logins. You can set this address with the 'data-notifyurl' token.

```<a class="comment-open" data-notifyurl="https://myurl.behind/?token=password-protected-page"  href="javascript:">Open with specific notification URL</a>```



## To open a Shortmail enabled forum

You can add the 'shortmail' data tag e.g.

```<a class="comment-open" data-uniquefeedbackid="my_different_forum_name" data-mymachineuser="10.12.13.14:2" data-shortmail="true" href="javascript:">Open special email forum</a>```

Or, you can use an href "#shortmail-open-" class:
```<a href="#email-open-my_shortmail_forum_name">Open shortmail forum</a>```



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

https://atomjump.com/api/download.php

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


1. Install the ‘Header and Footer’ plugin. https://wordpress.org/plugins/insert-headers-and-footers/
2. Go into ‘Settings->Header and Footer’, and enter the two sections from http://atomjump.org/wp/add-atomjump-messaging-to-your-site/. The main block should be entered into the ‘SECTION INJECTION’ section, and the ‘comment holder’ div should be entered into the ‘BEFORE THE CLOSING TAG (FOOTER)’ section.
3. Any link’s address (i.e. the ‘href’) on the page can now start with ‘#comment-open-‘, followed by the forum name and it will open a popup.


For more details see
http://atomjump.org



# Server Options



Staging:
To ensure you are set to staging, set the 'usingStaging' param to 'true'.

To test the web domain, use the following small PHP script:

```
<?php
        echo "Server name:" . $_SERVER['SERVER_NAME'];
?>
```
  
**db** **hosts**: there can be any number of db hosts, but some services do not allow multiple write hosts, and if this case the first one is the only write host, while the others are read. You can configure this with the 'singleWriteDb' option.

**db** **singleWriteDb**: optional. 'true' for a single write database cluster, and 'false' for a multiple write database cluster. This option is only applicable if there is more than one database host. Ver >= 1.8.9
   
**db** **scaleUp**: For different forums you can refer to completely different databases, to remove the heavy write usage in a multi-read/single write database server farm. This is an array of alternative db/hosts, which are used if a given regular expression is detected in the forum's name.

**db** **scaleUp** **labelRegExp**: This is a javascript/PHP regular expression that changes the database used for this forum. E.g. "^hello", would detect the forums 'hello_there', 'hello_anything' etc. Then the standard db details can be entered for this case i.e. 'name','hosts','user','pass','port','deleteDeletes','timezone','serviceHome'. You can also have different set of plugins with a unique 'plugins' array (Ver >= 1.9.5).

**db** **ssl**: Makes the connection to the database encrypted. Ver >= 1.9.5

```
"ssl" : {
         	"use" : false,
         	"key" : "",
         	"cert" : "",
         	"cacert": "",
         	"capath": "",
         	"protocol": "",
         	"verify": true
         }
```

**db** **ssl** **use**: Set to 'true' to use SSL, or 'false' to ignore the SSL settings and transfer data to the database unencrypted. Ver >= 1.9.5

**db** **ssl** **key**: The file path of the server's key file. Ver >= 1.9.5

**db** **ssl** **cert**: The file path of the server's certificate file. Ver >= 1.9.5

**db** **ssl** **cacert**: The file path of the CA Certificate file. Ver >= 1.9.5

**db** **ssl** **capath**: The path to the folder that contains the CA Certificate file. Ver >= 1.9.5

**db** **ssl** **protocol**: A list of the supported ssl protocols (separated by semi-colons). Ver >= 1.9.5. Here are some common protocols:

``` ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-SHA384:ECDHE-ECDSA-AES256-SHA384:ECDHE-RSA-AES256-SHA:ECDHE-ECDSA-AES256-SHA:DH-DSS-AES256-GCM-SHA384:DHE-DSS-AES256-GCM-SHA384:DH-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-SHA256:DHE-DSS-AES256-SHA256:DH-RSA-AES256-SHA256:DH-DSS-AES256-SHA256:DHE-RSA-AES256-SHA:DHE-DSS-AES256-SHA:
DH-RSA-AES256-SHA:DH-DSS-AES256-SHA:DHE-RSA-CAMELLIA256-SHA:DHE-DSS-CAMELLIA256-SHA:DH-RSA-CAMELLIA256-SHA:DH-DSS-CAMELLIA256-SHA:ECDH-RSA-AES256-GCM-SHA384:ECDH-ECDSA-AES256-GCM-SHA384:ECDH-RSA-AES256-SHA384:ECDH-ECDSA-AES256-SHA384:ECDH-RSA-AES256-SHA:ECDH-ECDSA-AES256-SHA:AES256-GCM-SHA384:AES256-SHA256:AES256-SHA:CAMELLIA256-SHA:PSK-AES256-CBC-SHA:ECDHE-RSA-AES128-GCM-SHA256:
ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-SHA256:ECDHE-ECDSA-AES128-SHA256:ECDHE-RSA-AES128-SHA:ECDHE-ECDSA-AES128-SHA:DH-DSS-AES128-GCM-SHA256:DHE-DSS-AES128-GCM-SHA256:DH-RSA-AES128-GCM-SHA256:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES128-SHA256:DHE-DSS-AES128-SHA256:DH-RSA-AES128-SHA256:DH-DSS-AES128-SHA256:DHE-RSA-AES128-SHA:DHE-DSS-AES128-SHA:DH-RSA-AES128-SHA:DH-DSS-AES128-SHA:DHE-RSA-SEED-SHA:DHE-DSS-SEED-SHA:DH-RSA-SEED-SHA:DH-DSS-SEED-SHA:DHE-RSA-CAMELLIA128-SHA:
DHE-DSS-CAMELLIA128-SHA:DH-RSA-CAMELLIA128-SHA:DH-DSS-CAMELLIA128-SHA:ECDH-RSA-AES128-GCM-SHA256:ECDH-ECDSA-AES128-GCM-SHA256:ECDH-RSA-AES128-SHA256:ECDH-ECDSA-AES128-SHA256:ECDH-RSA-AES128-SHA:ECDH-ECDSA-AES128-SHA:AES128-GCM-SHA256:AES128-SHA256:AES128-SHA:SEED-SHA:CAMELLIA128-SHA:IDEA-CBC-SHA:PSK-AES128-CBC-SHA:ECDHE-RSA-RC4-SHA:ECDHE-ECDSA-RC4-SHA:ECDH-RSA-RC4-SHA:ECDH-ECDSA-RC4-SHA:
RC4-SHA:RC4-MD5:PSK-RC4-SHA:ECDHE-RSA-DES-CBC3-SHA:ECDHE-ECDSA-DES-CBC3-SHA:EDH-RSA-DES-CBC3-SHA:EDH-DSS-DES-CBC3-SHA:DH-RSA-DES-CBC3-SHA:DH-DSS-DES-CBC3-SHA:ECDH-RSA-DES-CBC3-SHA:ECDH-ECDSA-DES-CBC3-SHA:DES-CBC3-SHA:PSK-3DES-EDE-CBC-SHA
```

**db** **ssl** **verify**: Whether to require verification of the certificates. We strongly recommend this is set to 'true'. 'false' requires PHP ver >= 5.6. Ver >= 1.9.5

**ips**: any number of PHP machines with the server software on it.

**webRoot**: Web path including http/https of the server. Don't include trailing slash.

**fileRoot**: Local file system server's file path. Don't include trailing slash.

**serviceHome**: The homepage of the service as a URL. This is used particularly after resetting a password.

**serverTimezone**: Change this to the location of the physical server eg. Pacific/Auckland

**delayFeeds**: Delays any API or feed download access for this number of seconds. The default is 1200 seconds or 20 minutes.

**titleReplace** **regex** **replaceWith**:  To create an automatic visual title for a forum based off the forum's actual database name, you should add in any regular expression replacements. This should be an array of { regex, replaceWith } objects, which are processed sequentially on the forum's database name. For example, xyz.atomjump.com pages are given a forum database name ajps_Xyz . The regex to replace the "ajps_" part of this to create a title "Xyz" you should should use 
{
	"regex": "/ajps_(.+)/",
	"replaceWith": "$1"
}
Where $1 is the entry in (.+). Note: you should include the /expression/ around your expressions, and can optionally include case insensitive modifiers, e.g. /expression/i.
More information on the supported expressions is available at https://www.php.net/manual/en/function.preg-replace.php.
You can switch automatic titles on or off with 'showAutomaticTitle' below.

**showAutomaticTitle** can be 'true' or 'false' depending on whether you wish to auto-generate titles for the forum headers. You can also modify the title using the 'titleReplace' options above.

**deleteDeletes**: Set to true by default, this means any user action to delete a message removes it completely from the database. If for your records you are required to keep hidden messages, set to false.

**loadbalancer**: Required for a production setup - any number of machines. Can be blank in a staging setup.

**phpPath**: Path to externally run any parallel PHP processes. Ver >= 0.5.5

**chatInnerJSFilename**: Path from the server that refers to the server-side Javascript. E.g. "/js/chat-inner-1.3.29.js". The numbers may change during updates.

**logoutPort**: Depreciated ver 0.6.3. Port which a logout is supported on. Default 1444. Use in an ssh situation, whereby this is a non-ssh port. Ver >= 0.5.5, ver <= 0.6.2

**adminMachineUser**: once your server has been set up, and you have saved your first user (yourself typically), find this user in the interface
Advanced settings, and write this into the config. This user is allowed to set forum passwords and limit the subscribers for a particular forum.

**analytics** **use**: Can be 'none'. Include a 'url' entry. The 'image' field is currently only needed for retrieving unique backgrounds for subdomains of atomjump.com.

**social** **use**: Can be 'none' or 'all'. 'none' switches off all social media interaction, while 'all' enables all of them.

**social** **twitter**: optional. Retrieves tweets from twitter related to this subdomain, and allows for replying to the tweets (posts a 
message via twitter).

**readPort**:  optional. The port to put the plugin 'loop-server-fast' daemon on, if it is different to 3277, see https://www.npmjs.com/package/loop-server-fast. Ver >= 0.5.22.

**readURL**: optional. The full URL including ports that the plugin 'loop-server-fast' daemon is on, see https://www.npmjs.com/package/loop-server-fast, if this is different to the standard URL followed by 'readPort'. Note: if you use a non-standard port, some machines behind proxy servers, particularly corporates, or some public PCs may have the address filtered out. One approach here, at a slight loss of speed, is to use the standard port 80 for http and 443 for https, and ProxyPass in Apache. Ver >= 1.5.5.

**httpsKey**:  optional. If you are serving from an https address, you will need this local file path, for the plugin 'loop-server-fast', see https://www.npmjs.com/package/loop-server-fast. See also 'httpsCert', which is needed too. Ver >= 0.5.22

**httpsCert**:  optional. If you are serving from an https address, you will need this local file path, for the plugin 'loop-server-fast', see https://www.npmjs.com/package/loop-server-fast. See also 'httpsKey', which is needed too. Ver >= 0.5.22

**uploads** **use**:  This can be one of 'none', 'same', 'generic', 'amazonAWS'. 'none' means uploads are switched off. 'same' means the upload stays on the same server in the '/images/im/' folder. 'generic' means uploads.genericUploadURL should be set to a remote URL, which the image file will be uploaded to via a POST request. 'amazonAWS' refers to the use of an Amazon S3 AWS bucket for remote storage. You will need an Amazon 'accessKey', 'secretKey' and 'imageURL', in this case.

Note: You should make sure your server provides a caching response to image files, or Safari will continue to refresh the images every 5 seconds.

**uploads** **hiRes** **width** (**height**): width in pixels of uploaded images, in their hi-res version. The low-res version is displayed to the whole group, and the hi-res version is only used when a user taps on that one photo.

**uploads** **lowRes** **width** (**height**): width in pixels of uploaded images, in their low-res version. The low-res version is displayed to the whole group, and the hi-res version is only used when a user taps on that one photo.

**uploads** **replaceHiResURLMatch**: set to a string in your uploaded images URL path that identifies that the photo is on your server, and not copied from a different server e.g. 'atomjump' would identify for "http://yourserver.com/atomjump/images/im/yourphoto.jpg". Images which include this string, will show the hi-res uploaded version when clicked e.g. "http://yourserver.com/atomjump/images/im/yourphoto_HI.jpg".

**uploads** **imagesShare**: If there are multiple PHP nodes, this defines which port to write uploaded images to, so that they are shared between nodes. 'port' is a port such as 80, and 'https' is either true or false. 'checkCode' is a unique code to your server, which you should change, for security purposes, to allow access from your nodes, only, Ver >= 2.5.7. If you are setting up an expandable cluster, you should also modify images/im/.htaccess and enter your subfolder path where it says CHANGE THIS BELOW, to provide a 'missing images' handler that allows expanding clusters, Ver >= 2.5.7. 

**uploads** **vendor** **amazonAWS**: optional. This is for S3 storage of uploaded images. You will need an Amazon 'accessKey', 'secretKey' and 'imageURL', in this case.


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
See http://atomjump.org/wp/plugin-summary/ for a list of plugins available.


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
  $table                     //AtomJump Loop Server ('atomjump' by default) database table name eg. "tbl_email"
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
[ forum_id, access_type, forum_group_user_id, requires_password ]

Where 'forum_id' e.g. 34
	  'access_type' eg. "readwrite", "read"
	  'forum_owner_user_id' is the user id to send a message to, to become visible to all the private forum owners.
	  'requires_password' is 'true' if the forum is password protected, or 'false' if not. (Server >= 2.3.4)
```


**get_current_user_ip()**

No parameters  
Server: > 0.5.0

Get the user's ip address, although this is now an artificial ip address, actually a unique 'ip' based off their user id.


**get_current_user_id()**

No parameters  
Server: >= 0.5.0

Returns the integer user id value of the current user.


**is_forum_granted()**

Required Parameters
($check_forum_id)  
Server: >= 2.3.4 

Check if a layer (numerical ID) has been granted access (i.e. the password has been specifically entered by the current user). If there is no need for access to be granted (i.e. on a public forum - see get_forum_id() and 'requires_password' return param) you should ignore any 'false' values.

Returns: true/false




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


**on_msg_buttons()**

Output parameters
($message_id)
Plugin function should return
($html)
  
When a particular message is clicked, a screen with more options (e.g. deleting the message) appears. This plugin function should return the HTML for the new custom button directly, including a javascript onclick="" event, and code, such as a server 'get' request to handle that event.

Server: >= 2.2.8  


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




## Global Variables

**$root_server_url**
The AtomJump Messaging Server's URL.
Make the variable available to your function with:

```
global $root_server_url;
```

**$cnf**
The global configuration file as an array. This can be accessed with e.g. $cnf['webRoot'].
Make the variable available to your function with:

```
global $cnf;
```

**$msg**
The global messages configuration file as an array. This can be accessed with e.g. $msg['msgs'][$lang]['description'].
Make the variable available to your function with:

```
global $msg;
``` 

**$lang**
The current selected language e.g. "en" for English.
Make the variable available to your function with:

```
global $lang;
``` 

**$db_cnf**
The current selected database configuration. This can be accessed with e.g. $db_cnf['user'].
Make the variable available to your function with:

```
global $db_cnf;
``` 
 
**$_REQUEST['uniqueFeedbackId'] / $_REQUEST['passcode']** 
Either of these can be the current forum's name as a string. You should check for both.

**$_SESSION['logged-email']**
Set on sign in. This is the email of the signed in user.

**$_SESSION['user-ip']**
The logged-in user's artificial ip address.
		
**$_SESSION['temp-user-name']**
This username is used potentially before another name is set e.g. 'Anon 55'
		
**$_SESSION['authenticated-layer']**
The current user has the authority to read and write to this layer ID (applies to any forum, not just password protected forums).



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



**show_translated_number()**

Required parameters
($number, $lang) 

Server: >= 2.6.7

Input, ideally an integer between 0 - 60 (60 seconds in a minute means this is usually the largest number we need for most things, i.e. 59 seconds ago), and a language e.g. "en", "ch" from the messages.json translation list. However, any number can be used and the default is to return the English number if there is no matching number in the messages array "number" conversion for the input language.



## Examples

All of the AtomJump-built plugins serve as good example code. But here are some particular cases:

**Adding your own buttons**

For customising the buttons that are visible when you tap on a single message, and for including a specific type of export of a forum, see this shell project, which can be modified to suit your own.

https://github.com/atomjump/medimage_export/tree/shell



# Performance

Performance depends on a number of factors. In general, since the AtomJump Server can be configured with a load balancer, multiple PHP servers and multiple database servers, the number of users supported can scale <i>almost</i> by adding more servers to the cluster. The basic PHP script on one server should handle (in the order of) one hundred simultaneous users, but this will vary considerably on the hardware used, the amount of usage and the type of usage by these users.

However, to extend performance into larger scale environments, we have released the NodeJS 'loop-server-fast' plugin at https://www.npmjs.com/package/loop-server-fast.  This aims to scale to thousands, or tens of thousand, simultaneous users per server. You can start by installing the PHP version, and then install the NodeJS version as you expand (you can revert back to the PHP version, also, if you have any issues).

AtomJump Messaging can work in a single write/ multiple read MySQL cluster, or a multi-write / multi-read cluster. The latter should be scalable to millions of simultaneous users.



# Contributing

Contributions are welcome, and they can take the shape of:

1. Core: Submit github pull requests. We will need to consider whether the feature should be in core, or externally as a plugin.  It is generally a good idea to get in touch with us via our homepage at http://atomjump.org to ensure we are not already working on a similar feature.
2. Plugins: Develop to the API above, and then publish on the http://atomjump.org/wp/plugin-summary/ 'available plugins' link.  You are free to write or publish (or keep private) any number of plugins.
3. Translations: We are looking for any translations of the conf/messages.json file from English into your language. We have machine translated Spanish as a first step, but we need help in ensuring this is a good translation, and applying other languages.


# License

This software is open source under the MIT license. Copyright is with the AtomJump Foundation (New Zealand), a non-profit society.  You can use this software for any commercial purposes.


# Other credits

Ear and Help Icons designed by Roundicons https://www.flaticon.com/authors/roundicons from http://www.flaticon.com, and the other ear icon by Freepik https://www.flaticon.com/authors/freepik from the same site.


