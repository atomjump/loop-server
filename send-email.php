<?php

//Send off an email
//eg. https://yoursite.com/send-email.php?to=peter@yoursite.com&subject=test&body=hi&staging=1&sender_email=webmaster@yoursite.com
foreach ($argv as $arg) {
    $e=explode("=",$arg);
    if(count($e)==2)
        $_REQUEST[$e[0]]=urldecode($e[1]);
    else   
        $_REQUEST[$e[0]]=0;
}

if(isset($_REQUEST['staging'])) {
	$staging = $_REQUEST['staging'];

}
require('config/db_connect.php');

require("classes/cls.basic_geosearch.php");
require("classes/cls.layer.php");
require("classes/cls.ssshout.php");

error_log("About to send email for real:" json_encode($_REQUEST));
echo cc_mail_direct($_REQUEST['to'], $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['sender_email'], $_REQUEST['sender_name'], $_REQUEST['to_name'], $_REQUEST['bcc']);


?>

