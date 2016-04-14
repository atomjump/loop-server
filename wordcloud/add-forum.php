<?php
    //Create a new entry into the data/words file for a wordcloud 
    $data_file = 'data/words.json';
    $str = file_get_contents($data_file);
    $json = json_decode($str, true); // decode the JSON into an associative array
    $new_forum = $_REQUEST['new-forum'];
    
    if(isset($new_forum) && ($new_forum != '')) {
        $temperature = intval($_REQUEST['temperature']);
        if(isset($temperature)) {
            
        } else {
            $temperature = 3;       //default to pretty small
        }
        
        $new_list = array();
        $new_list[0] = $new_forum;
        $new_list[1] = $temperature;
        
        //Handle case of overwrite if already exists
        $exists = false;
        for($cnt = 0; $cnt< count($json['list']); $cnt++) {
            if($json['list'][$cnt][0] == $new_forum) {
                //Modify the temperature
                if($temperature == 0) {
                    unset($json['list'][$cnt]);     //remove entry in this case
                } else {
                
                    $json['list'][$cnt][1] = $temperature;  //set new temp
                }
                $exists = true;
            }
        
        } 
        
       
        
        if($exists == false) {
            //Add a new entry
            array_push($json['list'], $new_list);
        }
        
        //Write out array to json file
        file_put_contents($data_file, json_encode($json));
        
        
    }
    
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	header("Pragma: no-cache"); // HTTP/1.0
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
    header("Location: index.php");
?>
