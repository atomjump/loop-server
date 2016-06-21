<?php


  if(!isset($config)) {
     //Get global config - but only once
     $data = file_get_contents (dirname(__FILE__) . "/config.json");
     if($data) {
        $config = json_decode($data, true);
        if(!isset($config)) {
          echo "Error: config/config.json is not valid JSON.";
          exit(0);
        }
     
     } else {
       echo "Error: Missing config/config.json.";
       exit(0);
     
     } 
  }
  
  function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
      return mb_convert_encoding($content, 'UTF-8',
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
  }
	

  if(!isset($msg)) {
     //Get global language file - but only once
     $data = file_get_contents_utf8(dirname(__FILE__) . "/messages.json");
     if($data) {
        $msg = json_decode($data, true);
        if(!isset($msg)) {
          echo "Error: config/messages.json is not valid JSON ";
          
          switch(json_last_error()) {
          
                case JSON_ERROR_NONE:
                    echo ' - No errors';
                break;
                case JSON_ERROR_DEPTH:
                    echo ' - Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    echo ' - Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    echo ' - Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    echo ' - Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                default:
                    echo ' - Unknown error';
                break;
          
          }
          exit(0);
        }
        
       
        
     
     } else {
       echo "Error: Missing messages/messages.json.";
       exit(0);
     
     } 
  }
  
   
  
  //Set default language, unless otherwise set
  $lang = $msg['defaultLanguage'];
  if(isset($_COOKIE['lang'])) {
     $lang = $_COOKIE['lang'];
  }
    

    function trim_trailing_slash($str) {
        return rtrim($str, "/");
    }
    
    function add_trailing_slash($str) {
        //Remove and then add
        return rtrim($str, "/") . '/';
    }


 	error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED); 
 	
  
 

	$db_name = $config['production']['db']['name'];			//Unless on the cloud below
	if(!isset($notify)) {
		$notify = true;					//Notify users by email and sms, unless we are specified difrferently
	}



			
			
	$db_username = $config['production']['db']['user']; //Edit this e.g. "peter"
	$db_password = $config['production']['db']['pass']; //Edit this e.g. "secretpassword"
	$db_host =  $config['production']['db']['hosts'][0]; 
	$db_name = $config['production']['db']['name'];
	
	$server_timezone = $config['production']['timezone'];
	$db_timezone = $server_timezone;
			
  
	if(($_SERVER["SERVER_NAME"] == $config['staging']['webDomain'])||($staging == true)) {
		//Staging
		
		
		$cnf = $config['staging'];

		
		$db_username = $cnf['db']['user']; //Edit this e.g. "peter"
		$db_password = $cnf['db']['pass']; //Edit this e.g. "secretpassword"
		$db_host =  $cnf['db']['hosts'][0]; 
		$db_name = $cnf['db']['name'];
	
	
		$root_server_url = trim_trailing_slash($cnf['webRoot']);
		$local_server_path = add_trailing_slash($cnf['fileRoot']);
		$db_inc = false;
		$staging = true;
		$db_timezone = $cnf['db']['timezone'];
		
	} else {
  
        $cnf = $config['production']; 
		$root_server_url = trim_trailing_slash($cnf['webRoot']);
		$local_server_path = add_trailing_slash($cnf['fileRoot']);
		
		//Live is now on amazon
		$db_total = count($cnf['db']['hosts']);			//Total number of databases
		$max_db_attempts = 2;	//Maximum incremental attempts 
		if((isset($db_read_only))&&($db_read_only == true)) { 
				$db_num = mt_rand(0,($db_total-1));		//If you add more DB nodes, increase this number
				$db_inc = true;
			
		} else {
			//Only one write db which is aj0
			$db_num = 0;
			$db_inc = false;
			
		}
		$db_host = $cnf['db']['hosts'][$db_num];	
		
			
		$db_timezone = $cnf['db']['timezone'];
		
		$staging = false;
	}	
	
	//Make sure we have a default logoutPort
	if(!isset($cnf['logoutPort'])) {
	    $cnf['logoutPort'] = 1444;
	}
	
	//General globals:
	$process_parallel = array();                //An array of system commands to run after an insert message request. These
	                                            //are set by any of the plugins, and are run right at the end of the script.
	
	$process_parallel_url = false;              //Used by plugins to run a process after everything else has finished in parallel. Set to true
	                                            //if this is to be run (currently only works for http servers, not https)
	

	//Leave the code below - this connects to the database
	$db = mysql_connect($db_host, $db_username, $db_password);
	if(!$db) {
		//TODO: Let us know that a server is down by email - but we only want to do this once!
		//cc_mail_direct($cfg['adminEmail'], "AtomJump Server " . $_SERVER['SERVER_ADDR'] . "," . gethostname(). " is down", "The most likely cause is the database is not connecting.", $cfg['webmasterEmail'], "", "");
	
		
		//header('HTTP/1.1 503 Service Temporarily Unavailable');
		//header('Status: 503 Service Temporarily Unavailable', false);	//false allows a second line
		//header('Retry-After: 300', false);//300 seconds
		
		if($db_inc == true) {
			$cnt = 0;
			while((!$db) && ($cnt < $max_db_attempts)) {
				$db_num ++;
				if($db_num >= $db_total) $db_num = 0;
				$cnt++;
				//Loop through all the other databases and check if any of them are available - to a max number of attempts				
				$db_host = $cnf['db']['hosts'][$db_num];			
				$db = mysql_connect($db_host, $db_username, $db_password);
			}
			
			if($cnt >= $max_db_attempts) {
				//Let the haproxy know our mysql and therefore server is down
				http_response_code(503);
				exit(0);
			
			}
		} else {
			//Let the haproxy know our mysql and therefore server is down
			http_response_code(503);
			exit(0);
		}
	}
	mysql_select_db($db_name);
	mysql_query("SET NAMES 'utf8'");		//SET NAMES 'utf8'   //_unicode_ci


	if(!isset($start_path)) {
		$start_path = "";
	}


	ini_set('session.gc_maxlifetime', 60*60*24*365*3);// expand
	ini_set('session.cookie_lifetime', 60*60*24*365*3);// expand

	require_once $start_path . 'classes/cls_php_session.php';	
	$session_class = new php_Session;
   	session_set_save_handler(array(&$session_class, 'open'),
                         array(&$session_class, 'close'),
                         array(&$session_class, 'read'),
                         array(&$session_class, 'write'),
                         array(&$session_class, 'destroy'),
                         array(&$session_class, 'gc'));
	
	if(isset($_REQUEST['ses'])&&($_REQUEST['ses'] != "")){
		session_id($_REQUEST['ses']);
	}

	session_start();

	function clean_data($string) {
	  //Use for cleaning input data before addition to database
	  if (get_magic_quotes_gpc()) {
	    $string = stripslashes($string);
	  }
	  $string = strip_tags($string);
	  return mysql_real_escape_string($string);
	}
	
	function clean_data_keep_tags($string)
	{
	
    //Use for cleaning input data before addition to database
	  if (get_magic_quotes_gpc()) {
	    $string = stripslashes($string);
	  }
	  
	  return mysql_real_escape_string($string);
	
	
	}
	
	function check_subdomain()
	{
	 global $config;
	
		//Check if we have a subdomain - return false if no, or the name of the subdomain if we have
		$server_name = $_SERVER['SERVER_NAME'];

		if(($server_name == $config['staging']['webDomain']) ||
		   ($server_name == $config['production']['webDomain'])) {
		   return false;
		} else {
		
			 
			$tempstr = str_replace(".atomjump.com","",$server_name);   
			$subdomain = str_replace(".ajmp.co","",$tempstr);		//Or alternatively the shorthand version
			
			return $subdomain;
		}
		
	
	}
	
	function make_writable_db()
    {
    	global $staging;
    	global $cnf;
    	
    	//Ensure we don't need this functionality on a staging server - which is always writable, single node
    	if($staging == true) { 	
    		return;
    	}
    
    	global $db_host;
    	global $db_username;
    	global $db_password;
    	global $db_name;
    
    	//Double check we are connected to the master database - which is writable. Note this is amazon specific
    	$db_master_host = $cnf['db']['hosts'][0];
    	if($db_host != $db_master_host) {
    		//Reconnect to the master db to carry out the write operation
    		mysql_close();		//close off the current db
    		
    		$db_host = $db_master_host;
    		$db = mysql_connect($db_host, $db_username, $db_password);
    		if(!$db) {
    			//No response from the master
    			http_response_code(503);
				   exit(0);
    		
    		}
    		
    		mysql_select_db($db_name);
  		  mysql_query("SET NAMES 'utf8'");		

    	}
    	
    	return;
    }

	function cc_mail($to_email, $subject, $body_text, $sender_email, $sender_name="", $to_name="", $bcc_email="")
	{
		global $root_server_url;
		global $local_server_path;
		global $notify;
		global $staging;
		
		

		if($notify == true) {
			$cmd = 'nohup nice -n 10 /usr/bin/php  ' . $local_server_path . 'send-email.php to=' . rawurlencode($to_email) . '  subject=' . rawurlencode($subject) . ' body=' . rawurlencode($body_text) . ' sender_email=' . rawurlencode($sender_email) .  ' sender_name=' . urlencode($sender_name) . ' to_name=' . urlencode($to_name) . ' staging=' . $staging . ' bcc=' . $bcc_email . ' > /dev/null 2>&1 &';	//To log eg.: . ' >/var/www/html/atomjump_staging/tmp/newlog.txt';
		
			$output = shell_exec($cmd);
		}

		//To test: https://atomjump.com/send-email.php?to=test@yourmail.com&subject=Test&body=test&sender_email=noreply@atomjump.com&sender_name=AtomJump

		return $result;
		
	}
	
	
	
	function send_mailgun($to_email, $subject, $body_text, $sender_email, $sender_name="", $to_name="", $bcc_email="")
	{
	
	 global $cnf;
 
		$config = array();
	 
		$config['api_key'] = $cnf['mailgun']['key']; 
		$config['api_url'] = $cnf['mailgun']['url'];
	 
		$message = array();
	 
		$message['from'] = $sender_email; 
		$message['to'] = $to_email;	
		if((isset($bcc_email))&&($bcc_email != '')) {
			$message['bcc'] = $bcc_email;
		}
		$message['subject'] = $subject;	 
		$message['html'] = nl2br($body_text); 
	
		$ch = curl_init();
	 
		curl_setopt($ch, CURLOPT_URL, $config['api_url']); 
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "api:{$config['api_key']}");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS,$message);
	 
		$result = curl_exec($ch);
	 
		curl_close($ch);
	 
		return $result;
	 
	}
	

	function cc_mail_direct($to_email, $subject, $body_text, $sender_email, $sender_name="", $to_name="", $bcc_email="")
	{
		return send_mailgun($to_email, $subject, $body_text, $sender_email, $sender_name, $to_name, $bcc_email);
		
	}
	
	
	function cur_page_url() {
	
		if($_REQUEST['clientremoteurl']) {
			//Our passed in value
			return urldecode($_REQUEST['clientremoteurl']);
		} else {
			if($_REQUEST['remoteurl']) {
				//Our passed in value
				return urldecode($_REQUEST['remoteurl']);
			} else {
				 $pageURL = 'http';
				 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
					 $pageURL .= "://";
				 if ($_SERVER["SERVER_PORT"] != "80") {
				  	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				 } else {
				  	$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				 }
				 return $pageURL;
			}
		}
	}
	
	define("ABOUT_LAYER_ID", 1);	
	define("DEFAULT_AD_WIDTH", 170);		//was 190
	
	function upload_to_all($filename, $raw_file, $specific_server = '')
	{
			//$raw_file is the hello.jpg, $filename is test/hello.jog
			//Share to Amazon S3
			global $local_server_path;
			global $cnf;
			
			
			require_once($local_server_path . "/vendor/amazon/S3.php");
	
			//See: https://github.com/tpyo/amazon-s3-php-class
			$s3 = new S3($cnf['amazonAWS']['accessKey'],$cnf['amazonAWS']['secretKey'] );		//Amazon AWS credentials
	
	
			if($s3->putObject(S3::inputFile($filename, false), "ajmp", $raw_file, S3::ACL_PUBLIC_READ, array(), array('Expires' => gmdate('D, d M Y H:i:s T', strtotime('-7 days'))))) {  //e.g. 'Thu, 01 Dec 2020 16:00:00 GMT'
				//Uploaded correctly
			}
		
		
			//Share across our own servers
			
			//Get the domain of the web url, and replace with ip:1080
			$parse = parse_url($root_server_url);
			$domain = $parse['host'];
			
			if($specific_server == '') {  //Defaults to all
				$servers = array();
				for($cnt =0; $cnt< count($cnf['ips']); $cnt++) {
				    $servers[] = str_replace($domain, $cnf['ips'][$cnt] . ":1080", $root_server_url) . "/copy-image.php";
				}
				
			} else {
				//Only process this one server
				$servers = array($specific_server);
		
			}
	
			foreach($servers as $server)
			{
	
     		 	//Coutesy http://stackoverflow.com/questions/19921906/moving-uploaded-image-to-another-server
		        $handle = fopen($filename, "r");
		        $data = fread($handle, filesize($filename));
		        $POST_DATA   = array('file'=>base64_encode($data),'FILENAME'=>$raw_file);
		        $curl = curl_init();
		        curl_setopt($curl, CURLOPT_URL, $server);
		        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		        curl_setopt($curl, CURLOPT_POST, 1);
		        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		        curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
		        $response = curl_exec($curl);
		        curl_close ($curl);

		        
		     }
		
	
	}
	
	function summary($details,$max)
	{
		if(strlen($details)>$max)
		{
		    $details = substr($details,0,$max);
		    $i = strrpos($details," ");
		    $details = substr($details,0,$i);
		    $details = $details."...";
		}
		return $details;
	}


?>
