<?php
  $verbose = false;

  //This section is a simplified cnf getter - for speed.	
  if(!isset($config)) {
     //Get global config - but only once
     $data = file_get_contents (dirname(__FILE__) . "/config/config.json");
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
  
  if(($_SERVER["SERVER_NAME"] == $config['staging']['webDomain'])||($config['usingStaging'] == true)) {
		//Staging
		$cnf = $config['staging'];
  } else {
  		$cnf = $config['production'];
  
  }
  //Up until here
	
	if($verbose == true) error_log($_REQUEST['code'] . " vs " . $cnf['uploads']['imagesShare']['checkCode']);
	
	if($_REQUEST['code'] == $cnf['uploads']['imagesShare']['checkCode']) {
		$filename = str_replace("/", "", $_REQUEST['image']);		//Remove any additional filename modifications for other areas
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