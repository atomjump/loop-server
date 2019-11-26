<?php
	$uploaded = false;

	//Comes in with '$output_file' as the _HI version of the name
 
		//Upload an image
		global $local_server_path;
		$target_dir = getcwd() . $image_path; //"/../images/property/";
		$raw_file = $_REQUEST['title'] . ".jpg";
		$hi_raw_file = $_REQUEST['title'] . "_HI.jpg";		//Hi res version
		$target_file = $target_dir . $raw_file;
		$hi_target_file = $target_dir . $hi_raw_file;
		$uploadOk = 1;
	
		// Check if image file is a actual image or fake image

		$check = getimagesize($hi_target_file);
		if($check !== false) {
			$message .="File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
			
			
			
			
		} else {
			$message .= "File is not an image.";
			$uploadOk = 0;
		}
		

		
		// Check file size
		if ($file["size"] > 10000000) {
			$message .= "Sorry, your file is too large.";
			$uploadOk = 0;
		}
		// Allow certain file formats
		if((stristr($check['mime'], "jpg") == false) && (stristr($check['mime'], "jpeg") == false)) {
			$message .= "Sorry, only JPG or JPEG files are allowed.";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$message .= "&nbsp;Your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			
			
				$message .= "The file ". basename( $file["name"]). " has been uploaded.";
				
				//Resize the image
				$src = imagecreatefromjpeg($hi_target_file);        
				list($width, $height) = getimagesize($hi_target_file); 

				$ratio = $height / $width;
				$resize = true;
					
				$base_size = 800;
				
				if($resize == true) {
					//We already have a 'hi res version' i.e. 1280 width.
					
					//Now we will create a faster 800 width version.
					$tmp = imagecreatetruecolor($base_size, ($base_size*$ratio));
					$filename = $target_file;

					imagecopyresampled($tmp, $src, 0, 0, 0, 0, $base_size, ($base_size*$ratio), $width, $height); 
					imagejpeg($tmp, $filename, 75);		//75% quality - this saves a lot on download space
					
					
				}

			
				
				//Image resized
				
				
				//Now copy across to the other
				//also see: upload_to_all($filename, $raw_file);		//Works! Just testing the follow in a parrallel process
				
				//Copy across to the other servers for future reference - but do in a separate process

				global $local_server_path;
				global $cnf;
				
				if(!isset($images_script)) {
					$script = $local_server_path . "send-images.php";
				
				} else {
					$script = $images_script;
				
				}
				$cmd = $cnf['phpPath'] . ' ' . $script . ' ' . $raw_file;
				error_log("Running " . $cmd);
				
				$response = shell_exec($cmd);
				
				if($hi_res == true) {
					$cmd = $cnf['phpPath'] . ' ' . $script . ' ' . $hi_raw_file;
					error_log("Running " . $cmd);
					
					$response = shell_exec($cmd);
					
				
				}
				
				if($response == '') {
					//A blank response signals all clear
					$uploaded = true;
				} else {
					$uploaded = false;
					$message .= "&nbsp;Sorry, there was an error uploading your file to Amazon.";
				
				}
				
		}
	 
	 
?>
