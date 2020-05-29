<?php


	include_once(__DIR__ . '/config/db_connect.php');		
	
	global $local_server_path;
	global $root_server_url;
	global $cnf;
	
	function is_image($pathToFile)
	{
	  if( false === exif_imagetype($pathToFile) )
	   return false;

	   return true;
	}
			
			
	if(isset($cnf['uploads']['imagesShare'])) {
			//Share across our own servers
			
			$specific_server = '';
			
			//Get the domain of the web url, and replace with ip:80
			$parse = parse_url($root_server_url);
			$domain = $parse['host'];
					
			if($specific_server == '') {  //Defaults to all
				$servers = array();
				for($cnt =0; $cnt< count($cnf['ips']); $cnt++) {
					$server_url = str_replace($domain, $cnf['ips'][$cnt] . ":" . $cnf['uploads']['imagesShare']['port'], $root_server_url) . "/";
					if($cnf['uploads']['imagesShare']['https'] == false) {
						//Only do with http
						$server_url = str_replace("https", "http", $server_url);
					}
					//error_log("server url:" . $server_url);   //TESTING
					$servers[] = $server_url;
					
				}
				
			} else {
				//Only process this one server
				$servers = array($specific_server);
		
			}
			
			//echo "This is a missing image served up by PHP";
			//echo "Caller:" . $_SERVER['REQUEST_URI'];
			$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

			// extracted basename
			$filename = basename($path);
			
			//$filename is now e.g. "upl-123443.jpg"
			
			$random_count = count($servers) - 1;
			
			$failure_getting = true;
			
			//Maximum 3 attempts from different cluster servers
			for($cnt = 0; $cnt < 3; $cnt++) {
				
				$server = $servers[$cnt];
		
				if($server) {
					$url  = trim_trailing_slash($server) . "/images/im/" . $filename;		
					//E.g. $url = "https://staging.atomjump.com/api/images/im/upl440-47456560.jpg";
					
					error_log("url:" . $url);   //TESTING
					
					$img = __DIR__ . '/images/im/' . $filename;
					
					//Download and put into our local image folder
					
					
					try {
						//Do a file check request first
						$checker = trim_trailing_slash($server) . "/image-exists.php?image=" . $filename . "&code=" . $cnf['uploads']['imagesShare']['checkCode'];	
						
						$checker_str = file_get_contents($url);
						// Check if file exists
						if($checker_str !== "true"){
							error_log("File not found");
							$failure_getting = true;
						} else{
							error_log("File exists");
							$str_image = file_get_contents($url);
							if($str_image === false) {
								error_log("Failed to get");   //TESTING
								$failure_getting = true;
							} else {
								//Put contents into local file
								if(file_put_contents($img, $str_image)) {
									if(is_image($img)) {
										//Pipe the image back to the browser
									
										header('Content-type: image/jpeg');
										readfile($img);
										exit(0);
									} else {
										//Remove the file
										unlink($img);
										$failure_getting = true;
									}
								} else {
									$failure_getting = true;
								}
							}
						}
   						
   						
						
					} catch (Exception $e) {
						error_log("Failed to get " . $e->getMessage());   //TESTING
						$failure_getting = true;
					}
										
				}
								
			}
			
			//Tried all the attempts
			if($failure_getting == true) {
				//Can put a default blank image in here
				error_log("Putting default in");   //TESTING
				
				$img = __DIR__ . '/images/im/default.jpg';
				header('Content-type: image/jpeg');
				readfile($img);
				exit(0);
			}
	} else {
		//Can put a default blank image in here
		$img = __DIR__ . '/images/im/default.jpg';
		
		header('Content-type: image/jpeg');
		readfile($img);
		exit(0);
	
	}


?>