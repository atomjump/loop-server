<?php
//get command line params
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

//note: this script can be run from e.g. a plugin's path, therefore it needs to be able to be run independently
//from it's own directory.
require(__DIR__ . '/config/db_connect.php');

require(__DIR__ . "/classes/cls.basic_geosearch.php");
require(__DIR__ . "/classes/cls.layer.php");
require(__DIR__ . "/classes/cls.ssshout.php");




//Send an sms message asyncronously.  To call: 
/*  $ch = curl_init();
 
curl_setopt($ch, CURLOPT_URL, 'https://yoururl.com/send-sms.php?phone=0234932&message=Hi+there&user_from_id=10');
curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1);
 
curl_exec($ch);
curl_close($ch);
*/
$ly = new cls_layer();



$ly->sms($_REQUEST['phone'], $_REQUEST['message'], $_REQUEST['user_from_id']);

?>
