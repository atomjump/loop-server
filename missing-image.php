<?php


	include_once(__DIR__ . '/config/db_connect.php');		
	
	global $local_server_path;
	global $root_server_url;
	global $cnf;
			
			
	if(isset($cnf['uploads']['imagesShare'])) {
			//Share across our own servers
			
			$specific_server = '';
			
			//Get the domain of the web url, and replace with ip:80
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
					error_log("server url:" . $server_url);   //TESTING
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
			
			$random_count = count($servers);
			
			//Maximum 3 attempts from different cluster servers
			for($cnt = 0; $cnt < 3; $cnt++) {
				$random_server = rand($random_count);
				error_log("rand server:" . $random_server);   //TESTING
				
				$server = $servers[$random_server];
		
				if($server) {
					$url  = trim_trailing_slash($server) . "/images/im/" . $filename;
					//E.g. $url = "https://staging.atomjump.com/api/images/im/upl440-47456560.jpg";
					
					error_log("url:" . $url);   //TESTING
					
					$img = __DIR__ . '/images/im/' . $filename;
					
					//Download and put into our local image folder
					if(file_put_contents($img, file_get_contents($url))) {
						//Pipe the image back to the browser
						header('Content-type: image/jpeg');
						readfile($img);
					} else {
						//Can put a default blank image in here
						$img = __DIR__ . '/images/im/default.jpg';
						header('Content-type: image/jpeg');
						readfile($img);
					}
					
				}
			}
	} else {
		//Can put a default blank image in here
		$img = __DIR__ . '/images/im/default.jpg';
		
		header('Content-type: image/jpeg');
		readfile($img);
	
	}


?>