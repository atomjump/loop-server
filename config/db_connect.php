<?php

  /* 
  		The main configuration and database connection script, that is included by all
  		other files.
  
  */
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
  
	function scale_up_horizontally_check($cnf)
	{
		$this_db_cnf = $cnf['db'];

		if(isset($_REQUEST['passcode'])) {
			$layer_name = $_REQUEST['passcode'];			
		}
		
		if(isset($_REQUEST['uniqueFeedbackId'])) {
			$layer_name = $_REQUEST['uniqueFeedbackId'];
		}

		if((isset($this_db_cnf['scaleUp']))&&(isset($layer_name))) {	
			//We are scaling up
			for($cnt = 0; $cnt< count($this_db_cnf['scaleUp']); $cnt ++) {	
				if(preg_match("/" . $this_db_cnf['scaleUp'][$cnt]['labelRegExp'] . "/",$layer_name, $matches) == true) {
					//Override with this database					
					$new_db_cnf = $this_db_cnf['scaleUp'][$cnt];
					
					//Check if we have our own unique plugins enabled for this installation
					if($new_db_cnf['plugins']) {
						global $cnf;
						$cnf['plugins'] = $new_db_cnf['plugins'];
					}
					return $new_db_cnf;
				}

			}
		}
		return $this_db_cnf;
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
		$notify = true;					//Notify users by email and sms, unless we are specified differently
	}



			
	//Defaults to the master production db		
	$db_username = $config['production']['db']['user']; //Edit this e.g. "peter"
	$db_password = $config['production']['db']['pass']; //Edit this e.g. "secretpassword"
	$db_host =  $config['production']['db']['hosts'][0]; 
	$db_name = $config['production']['db']['name'];
	
	$server_timezone = $config['production']['timezone'];
	$db_timezone = $server_timezone;
	$db_cnf = array();		//Make a global		
  
	if(($_SERVER["SERVER_NAME"] == $config['staging']['webDomain'])||($staging == true)||($config['usingStaging'] == true)) {
		//Staging
		
		
		$cnf = $config['staging'];
		$db_cnf = $cnf['db'];
		$db_cnf = scale_up_horizontally_check($cnf);

		
		$db_username = $db_cnf['user']; //Edit this e.g. "peter"
		$db_password = $db_cnf['pass']; //Edit this e.g. "secretpassword"
		$db_host =  $db_cnf['hosts'][0]; 
		$db_name = $db_cnf['name'];
	
	
		$root_server_url = trim_trailing_slash($cnf['webRoot']);
		$local_server_path = add_trailing_slash($cnf['fileRoot']);
			
		$db_total = count($db_cnf['hosts']);			//Total number of databases
		$max_db_attempts = 2;	//Maximum incremental attempts 
		if(((isset($db_read_only))&&($db_read_only == true))||  //This variable is set by caller scripts in forced read-only situations
		   (isset($db_cnf['singleWriteDb'])&&($db_cnf['singleWriteDb'] === false))) { 		
				$db_num = mt_rand(0,($db_total-1));		//If you add more DB nodes, increase this number
				$db_inc = true;
			
		} else {
			//Only one write db which is db 0
			$db_num = 0;
			$db_inc = false;
			
		}
		
		
		$db_timezone = $db_cnf['timezone'];
		
	} else {
  
        $cnf = $config['production']; 
		$root_server_url = trim_trailing_slash($cnf['webRoot']);
		$local_server_path = add_trailing_slash($cnf['fileRoot']);
		
		$db_cnf = $cnf['db'];
		$db_cnf = scale_up_horizontally_check($cnf);



		$db_username = $db_cnf['user']; //Edit this e.g. "peter"
		$db_password = $db_cnf['pass']; //Edit this e.g. "secretpassword"
		$db_host =  $db_cnf['hosts'][0]; 
		$db_name = $db_cnf['name'];
		
		//Live 
		$db_total = count($db_cnf['hosts']);			//Total number of databases
		$max_db_attempts = 2;	//Maximum incremental attempts 
		if(((isset($db_read_only))&&($db_read_only == true))||  //This variable is set by caller scripts in forced read-only situations
		   (isset($db_cnf['singleWriteDb'])&&($db_cnf['singleWriteDb'] === false))) { 		
				$db_num = mt_rand(0,($db_total-1));		//If you add more DB nodes, increase this number
				$db_inc = true;
			
		} else {
			//Only one write db which is db 0
			$db_num = 0;
			$db_inc = false;
			
		}
		$db_host = $db_cnf['hosts'][$db_num];	
		
			
		$db_timezone = $db_cnf['timezone'];
		
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
	
	//Use an amazon uploader - prep the libraries at compile time.
	if($cnf['uploads']['use'] == "amazonAWS") {
		require($local_server_path . 'vendor/aws-autoloader.php');
		use 'Aws\S3\S3Client';
		use 'Aws\S3\Exception\S3Exception';
	}
	

	if($db_cnf['ssl']) {
		$db_ssl = $db_cnf['ssl'];
	} else {
		$db_ssl = null;
	} 
	
	if($db_cnf['port']) {
		$db_port = $db_cnf['port'];
	} else {
		$db_port = null;
	} 
	
	

	//Leave the code below - this connects to the database
	$db = dbconnect($db_host, $db_username, $db_password, null, $db_ssl, $db_port);	
			
	if(!$db) {
		
		if($db_inc == true) {
			$cnt = 0;
			while((!$db) && ($cnt < $max_db_attempts)) {
				$db_num ++;
				if($db_num >= $db_total) $db_num = 0;
				$cnt++;
				//Loop through all the other databases and check if any of them are available - to a max number of attempts				
				$db_host = $db_cnf['hosts'][$db_num];			
				$db = dbconnect($db_host, $db_username, $db_password, null, $db_ssl, $db_port);
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
	dbselect($db_name);
	db_set_charset('utf8');
	db_misc();


	if(!isset($start_path)) {
		$start_path = $local_server_path;
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
	  return db_real_escape_string($string);
	}
	
	function clean_data_keep_tags($string)
	{
	
    	  //Use for cleaning input data before addition to database
	  if (get_magic_quotes_gpc()) {
	    $string = stripslashes($string);
	  }
	  
	  return db_real_escape_string($string);
	
	
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
    		global $db_host;
	    	global $db_username;
	    	global $db_password;
	    	global $db_name;
	    	global $db;
	    	global $db_cnf;
	    	global $db_total;
    	
    	
    	
    		//Ensure we don't need this functionality on a multi-write server - which is always writable, single node,
    		//or a single database server, or a forced read-only override section of code ($db_read_only)
    		if(($db_total === 1)||
    		   (isset($db_cnf['singleWriteDb'])&&($db_cnf['singleWriteDb'] === false))) {
    			
    			if($db) {
    				//Leave the current database
    				return;
    			
    			} else {
    				//We need to reconnect at this point - it is likely at the end of a session
    				$max_db_attempts = 2;	//Maximum incremental attempts 
					
					if($db_total === 1) {
						$db_num = 0;
						$db_inc = false;
					} else {
						$db_num = mt_rand(0,($db_total-1));		//If you add more DB nodes, increase this number
						$db_inc = true;
					}
					
					
					
					$db_host = $db_cnf['hosts'][$db_num];
					
					while((!$db) && ($cnt < $max_db_attempts)) {
						$db_num ++;
						if($db_num >= $db_total) $db_num = 0;
						$cnt++;
						//Loop through all the other databases and check if any of them are available - to a max number of attempts				
						$db_host = $db_cnf['hosts'][$db_num];	
						
						if($db_cnf['ssl']) {
							$db_ssl = $db_cnf['ssl'];
						} else {
							$db_ssl = null;
						} 

						if($db_cnf['port']) {
							$db_port = $db_cnf['port'];
						} else {
							$db_port = null;
						} 
														
						$db = dbconnect($db_host, $db_username, $db_password, null, $db_ssl, $db_port);
					}
					
					dbselect($db_name);
	  				db_set_charset('utf8');
	  				db_misc();
					return;
				}
			}
			

    
	    	//Double check we are connected to the master database - which is writable. Note this is single write database specific
	    	$db_master_host = $db_cnf['hosts'][0];
	    	if(($db_host != $db_master_host)||(!isset($db))) {
	    		//Reconnect to the master db to carry out the write operation
	    		dbclose();		//close off the current db
	    		
	    		$db_host = $db_master_host;
	    			    		
	    		if($db_cnf['ssl']) {
					$db_ssl = $db_master_host['ssl'];
				} else {
					$db_ssl = null;
				} 
				
				if($db_cnf['port']) {
					$db_port = $db_cnf['port'];
				} else {
					$db_port = null;
				} 
	    		
	    		$db = dbconnect($db_host, $db_username, $db_password, null, $db_ssl, $db_port);
	    		if(!$db) {
	    			//No response from the master
	    			http_response_code(503);
					exit(0);
	    		
	    		}
	    		
	    		dbselect($db_name);
	  			db_set_charset('utf8');
	  			db_misc();
	
	    	}
    	
    		return;
    	}

	function cc_mail($to_email, $subject, $body_text, $sender_email, $sender_name="", $to_name="", $bcc_email="")
	{
		global $root_server_url;
		global $local_server_path;
		global $notify;
		global $staging;
		global $process_parallel;
		global $cnf;
				
		if($notify == true) {		//This global variable may be set by an add-on e.g. the emailer, to ensure that notifications are not sent
			$cmd = 'nohup nice -n 10 ' . $cnf['phpPath'] .  ' ' . $local_server_path . 'send-email.php to=' . rawurlencode($to_email) . '  subject=' . rawurlencode($subject) . ' body=' . rawurlencode($body_text) . ' sender_email=' . rawurlencode($sender_email) .  ' sender_name=' . urlencode($sender_name) . ' to_name=' . urlencode($to_name) . ' staging=' . $staging . ' bcc=' . rawurlencode($bcc_email) . ' > /dev/null 2>&1 &';	//To log eg.: . ' >/var/www/html/atomjump_staging/tmp/newlog.txt';
						
			array_push($process_parallel, $cmd);        //Store to be run by index.php at the end of everything else.
		}

		//To test: https://atomjump.com/api/send-email.php?to=test@yourmail.com&subject=Test&body=test&sender_email=noreply@atomjump.com&sender_name=AtomJump

		return $result;
		
	}
	
	function send_generic_email($to_email, $subject, $body_text, $sender_email, $sender_name="", $to_name="", $bcc_email="")
	{
	 	global $cnf;
	 	global $local_server_path;
 
		require_once($local_server_path . "classes/PHPMailer/class.phpmailer.php");
		require_once($local_server_path . "classes/PHPMailer/class.smtp.php");

		$mail = new PHPMailer;

		//$mail->SMTPDebug = 3;                               // Enable verbose debug output

		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = $cnf['email']['sending']['smtp'];  // Specify main and backup SMTP servers (with comma separator)
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = $cnf['email']['sending']['user'];                 // SMTP username
		$mail->Password = $cnf['email']['sending']['pass'];                           // SMTP password
		if(isset($cnf['email']['sending']['encryption'])) {
			$mail->SMTPSecure = $cnf['email']['sending']['encryption'];                            // Enable TLS encryption, `ssl` also accepted
		}
		
		if(isset($cnf['email']['sending']['port'])) {
			$mail->Port = $cnf['email']['sending']['port'];
		} else {	
			$mail->Port = 587;                                    // TCP port to connect to
		}
	
		if((isset($sender_email))&&($sender_email != '')) {
			$mail->setFrom($sender_email, $sender_name);	//Note: some email senders will only send from a specific single email account e.g. the one set in noReplyEmail
		} else {
			$mail->setFrom($cnf['email']['noReplyEmail'], $cnf['email']['noReplyEmail']);
		}
		$mail->addAddress($to_email);     // Add a recipient
		
		if((isset($bcc_email))&&($bcc_email != '')) {			
			$mail->addBCC($bcc_email);
		}

		$mail->isHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = nl2br($body_text);
		$mail->AltBody = $body_text;

		if(!$mail->send()) {
			error_log('Message could not be sent.');
			error_log('Mailer Error: ' . $mail->ErrorInfo);
		} else {
			//error_log('Message has been sent');
		}
		
		return;
	
	}
	
	
	
	function send_mailgun($to_email, $subject, $body_text, $sender_email, $sender_name="", $to_name="", $bcc_email="")
	{
	
	 	global $cnf;
 
		$config = array();
	 
		$config['api_key'] = $cnf['email']['sending']['vendor']['mailgun']['key']; 
		$config['api_url'] = $cnf['email']['sending']['vendor']['mailgun']['url'];
	 
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
		global $notify;			//This global variable may be set by an add-on e.g. the emailer, to ensure that notifications are not sent
		
		if($notify == true) {		
	
			if($cnf['email']['sending']['use'] == 'mailgun') {
		
				return send_mailgun($to_email, $subject, $body_text, $sender_email, $sender_name, $to_name, $bcc_email);
			} else {
				//A generic SMTP server
				return send_generic_email($to_email, $subject, $body_text, $sender_email, $sender_name, $to_name, $bcc_email);
			}
		}
		
		return false;
		
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
			//Returns 'false' if image is not successfully put to Amazon, but otherwise 'true'
			global $local_server_path;
			global $root_server_url;
			global $cnf;
			
			
			
			if($cnf['uploads']['use'] == "amazonAWS") {
			
				

				

				//OLD: require_once($local_server_path . "vendor/amazon/S3.php");
	
				if(isset($cnf['uploads']['vendor']['amazonAWS']['uploadUseSSL'])) {
					$use_ssl = $cnf['uploads']['vendor']['amazonAWS']['uploadUseSSL'];
					
				} else {
					$use_ssl = false;		//Default
				}
				
				if(isset($cnf['uploads']['vendor']['amazonAWS']['uploadEndPoint'])) {
					$endpoint = $cnf['uploads']['vendor']['amazonAWS']['uploadEndPoint'];
				} else {
					$endpoint = "s3.amazonaws.com";		//Default
				}
	
				$bucket = "ajmp";		//Default
				if(isset($cnf['uploads']['vendor']['amazonAWS']['bucket'])) {
					$bucket = $cnf['uploads']['vendor']['amazonAWS']['bucket'];
				}
				
				
				//OLD: $s3 = new S3($cnf['uploads']['vendor']['amazonAWS']['accessKey'],$cnf['uploads']['vendor']['amazonAWS']['secretKey'], $use_ssl, $endpoint);		//Amazon AWS credentials
				
				
				// Configure a client using Spaces
				$client = new Aws\S3\S3Client([
						'version' => 'latest',
						'region'  => 'nyc3',				//TODO: change this
						'endpoint' => $endpoint,			//E.g. 'https://nyc3.digitaloceanspaces.com'
						'credentials' => [
								'key'    => $cnf['uploads']['vendor']['amazonAWS']['accessKey'],
								'secret' => $cnf['uploads']['vendor']['amazonAWS']['secretKey'],
							],
				]);
				
	
				try {
					// Upload data.
					$result = $s3->putObject([
						'Bucket' => $bucket,
						'Key'    => $filename,
						'Body'   => file_get_contents($filename),
						'ACL'    => 'public-read'
					]);

					// Print the URL to the object.
					error_log($result['ObjectURL']);
				} catch (S3Exception $e) {
					error_log($e->getMessage());
					return false;
				}

				
	
				// putObject($input, $bucket, $uri, $acl = self::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = self::STORAGE_CLASS_STANDARD, $serverSideEncryption = self::SSE_NONE)
				/*OLD: if($s3->putObject(S3::inputFile($filename, false), $bucket, $raw_file, S3::ACL_PUBLIC_READ, array(), array('Expires' => gmdate('D, d M Y H:i:s T', strtotime('+20 years')), null, $server_side_encryption))) {  //e.g. 'Thu, 01 Dec 2020 16:00:00 GMT'
					//Uploaded correctly
				} else {
					//Error uploading to Amazon
					return false;
				}*/
			
			}
			
			
			if($cnf['uploads']['use'] == "generic") {
				//Upload image to a generic remote service 
				$handle = fopen($filename, "r");
				$data = fread($handle, filesize($filename));
				$POST_DATA   = array('file'=>base64_encode($data),'FILENAME'=>$raw_file);
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $cnf['uploads']['genericUploadURL']);
				curl_setopt($curl, CURLOPT_TIMEOUT, 30);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
				$response = curl_exec($curl);		        
				if(curl_error($curl))
				{
					error_log('error:' . curl_error($curl));
				}
			
				curl_close ($curl);
			}
		
		
			if(isset($cnf['uploads']['imagesShare'])) {
				//Share across our own servers
			
				//Get the domain of the web url, and replace with ip:1080
				$parse = parse_url($root_server_url);
				$domain = $parse['host'];
					
				if($specific_server == '') {  //Defaults to all
					$servers = array();
					for($cnt =0; $cnt< count($cnf['ips']); $cnt++) {
						$server_url = str_replace($domain, $cnf['ips'][$cnt] . ":" . $cnf['uploads']['imagesShare']['port'], $root_server_url) . "/copy-image.php";
						if($cnf['uploads']['imagesShare']['https'] == false) {
							//Only do with http
							$server_url = str_replace("https", "http", $server_url);
						}
						$servers[] = $server_url;
					
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
					if(curl_error($curl))
					{
						error_log('error:' . curl_error($curl));
					}
				
					curl_close ($curl);
				
				
				 }
			 }
		     
		     return true;
		
	
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
	
	function dbconnect($host, $user, $pass, $dbname = null, $ssldetails = null, $dbport = 3306)
	{
		//Using old style:
		/*
			mysql_connect($host, $user, $pass);
		*/
	
		
		if(($ssldetails) && (isset($ssldetails['use'])) && ($ssldetails['use'] === true)) {
			//SSL connection
			$con = mysqli_init();
			if (!$con) return false;
			
			mysqli_ssl_set($con, $ssldetails['key'], $ssldetails['cert'], $ssldetails['cacert'], $ssldetails['capath'], $ssldetails['protocol']);   
						
			if(isset($ssldetails['verify']) && ($ssldetails['verify'] === false)) {
				$connection_type = MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;   //  works for PHP >= 5.6
			} else {
				$connection_type = MYSQLI_CLIENT_SSL;			
			}
						
						
			if($dbname) {
				if(mysqli_real_connect($con,"p:" . $host, $user, $pass, $dbname, $dbport, null, $connection_type)) {  
			        return $con;
			    } else {
			    	return false;
			    }
			} else {
				if(mysqli_real_connect($con,"p:" . $host, $user, $pass, null, $dbport, null, $connection_type)) { 
					return $con;
				} else {
					return false;
				}
			}
			
		} else {
		
			//Normal non-ssl
		
			if($dbname) {
				return mysqli_connect("p:" . $host, $user, $pass, $dbname, $dbport);		//p is for persistent connection
			} else {
				return mysqli_connect("p:" . $host, $user, $pass, null, $dbport);
			}
		}
		
	}
	
	function dbselect($dbname)
	{
		global $db;
		//Using old style = mysql_select_db($dbname);
		return mysqli_select_db($db, $dbname);
		
	}
	
	function dbclose()
	{
		global $db;
		//Using old style = mysql_close();
		mysqli_close($db);
		
	}
	
	function dbquery($sql)
	{
		global $db;
		
		//Using old style: return mysql_query($sql);
		return mysqli_query($db, $sql);
	}
	
	function db_fetch_array($result)
	{
		//Old style: mysql_fetch_array($result)
		return mysqli_fetch_array($result);
	}
	
	function db_real_escape_string($str)
	{
		global $db;
		//Using old style: mysql_real_escape_string
		return mysqli_real_escape_string($db, $str);
		//Caution character set must be set prior with mysqli_set_charset() 
		
	}
	
	function dberror()
	{
		global $db;
		//Old style: mysql_error()
		return mysqli_error($db);
	}
	
	function db_insert_id()
	{
		global $db;
		//Old style: mysql_insert_id();
		return mysqli_insert_id($db);
	}

	function db_set_charset($set)
	{
		global $db;
		//Old way: dbquery("SET NAMES 'utf8'");
		mysqli_set_charset($db, $set);
	}
	
	function db_misc()
	{
		
		//Old way would have nothing in here. mysqli needs it for innodb tables - we have a few.
		dbquery('SET AUTOCOMMIT = 1');	
	
	}

?>
