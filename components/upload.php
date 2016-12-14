<?php
	$uploaded = false;

	foreach($_FILES as $file) {
	 
		//Upload an image
		global $local_server_path;
		$target_dir = getcwd() . $image_path; //"/../images/property/";
		$raw_file = $_REQUEST['title'] . ".jpg";
		$hi_raw_file = $_REQUEST['title'] . "_HI.jpg";		//Hi res version
		$target_file = $target_dir . $raw_file;
		$hi_target_file = $target_dir . $hi_raw_file;
		$uploadOk = 1;
		$imageFileType = $file["type"];	// pathinfo($file["tmp_name"]["type"],PATHINFO_EXTENSION);		//This isn't the best beacuse it is browser reporting it but it hopefully removes non jpg
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($file["tmp_name"]);
			if($check !== false) {
				$message .="File is an image - " . $check["mime"] . ".";
				$uploadOk = 1;
				
				
				
				
			} else {
				$message .= "File is not an image.";
				$uploadOk = 0;
			}
		}
		
		// Check file size
		if ($file["size"] > 10000000) {
			$message .= "Sorry, your file is too large.";
			$uploadOk = 0;
		}
		// Allow certain file formats
		if((stristr($imageFileType, "jpg") == false) && (stristr($imageFileType, "jpeg") == false)) {
			$message .= "Sorry, only JPG or JPEG files are allowed.";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$message .= "&nbsp;Your file was not uploaded.";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($file["tmp_name"], $target_file)) {
			
				$message .= "The file ". basename( $file["name"]). " has been uploaded.";
				
				//Resize the image
				$src = imagecreatefromjpeg($target_file);        
				list($width, $height) = getimagesize($target_file); 

				$ratio = $height / $width;
				$resize = true;
					
				$base_size = 800;
				
				if($resize == true) {
					$tmp = imagecreatetruecolor($base_size, ($base_size*$ratio));
					$filename = $target_file;

					imagecopyresampled($tmp, $src, 0, 0, 0, 0, $base_size, ($base_size*$ratio), $width, $height); 
					imagejpeg($tmp, $filename, 75);		//75% quality - this saves a lot on download space
					
					$hi_res = true;
					//We want a hi res version too
					$base_size = 1280;
					
					$tmp = imagecreatetruecolor($base_size, ($base_size*$ratio));
					$filename = $hi_target_file;

					imagecopyresampled($tmp, $src, 0, 0, 0, 0, $base_size, ($base_size*$ratio), $width, $height); 
					imagejpeg($tmp, $filename, 95);		//95% quality - this produces a good quality image for slower secondary downloads	
					
				}

			
				
				//Image resized
				
				
				//Now copy across to the other
				//also see: upload_to_all($filename, $raw_file);		//Works! Just testing the follow in a parrallel process
				
				//Copy across to the other servers for future reference - but do in a separate process

				global $local_server_path;
				global $cnf;
				
				if(!isset($images_script)) {
					$script = "send-images.php";
				
				} else {
					$script = $images_script;
				
				}
				$cmd = 'nohup nice -n 10 ' . $cnf['phpPath'] . ' ' . $local_server_path . $script . ' ' . $raw_file;
				$response = shell_exec($cmd);
				
				if($hi_res == true) {
					$cmd = 'nohup nice -n 10 ' . $cnf['phpPath'] . ' ' . $local_server_path . $script . ' ' . $hi_raw_file;
					$response = shell_exec($cmd);
			
				
				}
				
				if($response == '') {
					//A blank response signals all clear
					$uploaded = true;
				} else {
					$uploaded = false;
					$message .= "&nbsp;Sorry, there was an error uploading your file to Amazon.";
				
				}
				
			} else {
				$message .= "&nbsp;Sorry, there was an error uploading your file.";
			}
		}
	 }  //end of if an image to upload
	 
	 
?>
