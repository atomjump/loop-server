<?php
	
	//Runs through a series of shell commands in the background
	//Usage:  php run_process.php [urlencoded, json_encoded array of shell commands to run]
	
	$debug_parallel = true;		//Usually false, switch to 'true' when debugging output from parallel processes

	
	if($argv[1]) {
		$process_parallel = json_decode(urldecode($argv[1]));

		
		for($cnt = 0; $cnt < count($process_parallel); $cnt++) {
			
			if($debug_parallel == true) {
				error_log("About to run: " . $process_parallel[$cnt]);
			}
			$ret = shell_exec($process_parallel[$cnt]);
			if($debug_parallel == true) {
				error_log("Finished running: " . $ret);
			}
		}
	} else {
		echo "Sorry, you need to pass a urlencoded, json_encoded array of shell commands to run.";
		
	}

?>