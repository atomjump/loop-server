<?php


	//Can be run from the command line as  php send-images.php all [ip]
	//			eg php send-images.php all      (transfers all images to all servers)
	//				php send-images.php all http://172.100.21.34:1080/copy-image.php (Transfers all images to 172.100.21.34 server)

	$start_path = getcwd() . "/";
	//Send all .jpg images in this folder - to be run from the command line. If a param is set - just send that image
	include_once('config/db_connect.php');			
	
	
	if(($argc > 1)&&($argv[1] != 'all')) {
	
		error_log("Send images run on " . getcwd() . "/images/property/" . $argv[1]);		//tell apache logs about it
		if(upload_to_all(getcwd() . "/images/property/" . $argv[1], $argv[1], "/images/property/")) {
		
		} else {
			echo "Sorry, there was a problem with the image upload.";
		}
	
	} else {
	
		if(isset($_REQUEST['image'])&&($_REQUEST['code'] == 'sdfsdfgew3345')) {
			global $local_server_path;
			if(upload_to_all($local_server_path . "images/property/" . $_REQUEST['image'], $_REQUEST['image'], null, "/images/property/")) {
			
			} else {
				echo "Sorry, there was a problem with the image upload.";
			}
		
		} else {
			if(($argc > 1)&&($argv[1] == 'all')) {
				//loop through all .jpgs
				$path = getcwd() . "/images/property/";
				$dir = new DirectoryIterator($path);
				foreach ($dir as $fileinfo) {
					if (!$fileinfo->isDot()) {
						var_dump($fileinfo->getFilename());
						$filen = $fileinfo->getFilename();
						
						if($argv[2]) {
							//A specific server
							$specific = $argv[2];
						} else {
							$specific = '';
						
						}
						
						upload_to_all($path . $filen, $filen, $specific, "/images/property/");
						
						
						echo $path . $filen . " , " . $filen . "\n";
					}
				}
			
			}
		}
	
	}

?>
