<?php


	include_once(__DIR__ . '/config/db_connect.php');		
	
	global $local_server_path;
	global $root_server_url;
	global $cnf;
	
	$verbose = true;		//Usually false in live environs
	
	function is_image($pathToFile)
	{
	  if( false === exif_imagetype($pathToFile) ) return false;

	   return true;
	}
	
	function get_remote($url, $filename) {
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($curl);		        
		if(curl_error($curl))
		{
			error_log('error:' . curl_error($curl));
		}
	
		curl_close ($curl);
		
		return $response;
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
					$servers[] = $server_url;
					
				}
				
			} else {
				//Only process this one server
				$servers = array($specific_server);
		
			}
			
			$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
			if($verbose == true) error_log("input path:" . $path);
			
			//E.g. Input $path = /api/images/property/test.jpg
			$image_path = trim_trailing_slash($cnf['webRoot']) . "/images";
			$image_path = parse_url($image_path, PHP_URL_PATH);
			//E.g. $image_path = /api/images
			
			if($verbose == true) error_log("image path:" . $image_path);
			$path = str_replace($image_path, "", $path);
			//E.g. Target for path from  is /im/test.jpg
			if($verbose == true) error_log("path:" . $path);

			// extracted basename
			$filename = basename($path);
			
			//$filename is now e.g. "upl-123443.jpg"
			
			$random_count = count($servers) - 1;
			
			$failure_getting = true;
			
			//Maximum 3 attempts from different cluster servers
			for($cnt = 0; $cnt < 3; $cnt++) {
				
				$server = $servers[$cnt];
				if($verbose == true) error_log("checking server:" . $server);
				
				if($server) {
					$url  = trim_trailing_slash($server) . "/images" . $path;		
					//E.g. $url = "https://staging.atomjump.com/api/images/im/upl440-47456560.jpg";
					
					if($verbose == true) error_log("url:" . $url);
					
					$img = __DIR__ . '/images' . $path;
					
					//Download and put into our local image folder
					
					
					try {
						//Do a file check request first
						$checker = trim_trailing_slash($server) . "/image-exists.php?image=" . $path . "&code=" . $cnf['uploads']['imagesShare']['checkCode'];	
						if($verbose == true) error_log("checker: " . $checker );
						
						$checker_str = get_remote($checker, $path);
						if($verbose == true) error_log("checked response: " . $checker_str );
						// Check if file exists
						if($checker_str !== "true"){
							if($verbose == true) error_log("File not found");
							$failure_getting = true;
						} else{
							if($verbose == true) error_log("File exists");
							$str_image = get_remote($url, $path);
							if($str_image === false) {
								if($verbose == true) error_log("Failed to get"); 
								$failure_getting = true;
							} else {
								//Put contents into local file
								if(file_put_contents($img, $str_image)) {
									if(is_image($img)) {
										//Pipe the image back to the browser
										if($verbose == true) error_log("Is an image, piping out");
										header('Content-type: image/jpeg');
										readfile($img);
										exit(0);
									} else {
										//Remove the file
										if($verbose == true) error_log("Is not an image, removing the file downloaded");
										unlink($img);
										$failure_getting = true;
									}
								} else {
									$failure_getting = true;
								}
							}
						}
   						
   						
						
					} catch (Exception $e) {
						if($verbose == true) error_log("Failed to get " . $e->getMessage()); 
						$failure_getting = true;
					}
										
				}
								
			}
			
			//Tried all the attempts
			if($failure_getting == true) {
				//Can put a default blank image in here
				if($verbose == true) error_log("Putting default in");
				
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