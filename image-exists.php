<?php
	//This section is a simplified cnf getter - for speed.	
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
  
  if(($_SERVER["SERVER_NAME"] == $config['staging']['webDomain'])||($staging == true)||($config['usingStaging'] == true)) {
		//Staging
		$staging = true;
		
		$cnf = $config['staging'];
  } else {
  		$cnf = $config['production'];
  
  }
  //Up until here
	
	
	if($_REQUEST['code'] == $cnf['uploads']['imagesShare']['checkCode']) {
		$filename = str_replace("/", "", $_REQUEST['image']);
		$filename = str_replace("..", "", $filename);
		if(file_exists(__DIR__ . '/images/im/' . $filename)) {
			echo "true";
		
		} else {
			echo "false";
		}
	
	} else {
		echo "none";
	}

?>