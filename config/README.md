## Setting up the Config


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

**phpPath**: Path to externally run any parallel PHP processes. Ver >= 0.5.5

**logoutPort**: Port which a logout is supported on. Default 1444. Use in an ssh situation, whereby this is a non-ssh port. Ver >= 0.5.5

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

**readPort**:  optional. The port to put the plugin 'loop-server-fast' daemon on, see https://www.npmjs.com/package/loop-server-fast						

**httpsKey**:  optional. If you are serving from an https address, you will need this local file path, for the plugin 'loop-server-fast', see https://www.npmjs.com/package/loop-server-fast. See also 'httpsCert', which is needed too.

**httpsCert**:  optional. If you are serving from an https address, you will need this local file path, for the plugin 'loop-server-fast', see https://www.npmjs.com/package/loop-server-fast. See also 'httpsKey', which is needed too.
