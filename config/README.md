## Setting up the Config


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

**readPort**:  optional. The port to put the plugin 'loop-server-fast' daemon on, see https://www.npmjs.com/package/loop-server-fast. Ver >= 0.5.22						

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
