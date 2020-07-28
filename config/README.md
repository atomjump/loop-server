## Setting up the Config


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

**db** **scaleUp** **labelRegExp**: This is a javascript/PHP regular expression that changes the database used for this forum. E.g. "^hello", would detect the forums 'hello_there', 'hello_anything' etc. Then the standard db details can be entered for this case i.e. 'name','hosts','user','pass','port','deleteDeletes','timezone'. You can also have different set of plugins with a unique 'plugins' array (Ver >= 1.9.5).

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

**uploads** **hiRes** **width** (**height**): width in pixels of uploaded images, in their hi-res version. The low-res version is displayed to the whole group, and the hi-res version is only used when a user taps on that one photo.

**uploads** **lowRes** **width** (**height**): width in pixels of uploaded images, in their low-res version. The low-res version is displayed to the whole group, and the hi-res version is only used when a user taps on that one photo.

**uploads** **replaceHiResURLMatch**: set to a string in your uploaded images URL path that identifies that the photo is on your server, and not copied from a different server e.g. 'atomjump' would identify for "http://yourserver.com/atomjump/images/im/yourphoto.jpg". Images which include this string, will show the hi-res uploaded version when clicked e.g. "http://yourserver.com/atomjump/images/im/yourphoto_HI.jpg".

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
