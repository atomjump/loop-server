<?php 
	require('config/db_connect.php');
   global $cfg; 
   global $msg;
   global $lang;
 
 $unique_pass_reset = $cfg['db']['user'] . $cfg['mailgun']['key'];	//This should be unique per installation.	
	
 
 if($_REQUEST['action']) {
 
   $email = $_REQUEST['user'];
   
   //decrypt 
   $action = md5(date('Ymd') . $email . $unique_pass_reset);
   if($action == $_REQUEST['action']) {
       //check is valid timewise
   
       //clear user's password
       
          $sql = "UPDATE tbl_user SET var_pass = NULL WHERE var_email = '" . $email . "'";
      	  $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			        
           echo $msg['msgs'][$lang]['passwordBeenResetApp'];
          //Password cleared
          
    } else {
    	  echo $msg['msgs'][$lang]['passwordNotReset'];
    
    }
 
 } else {
	
	  	
		$email = $_REQUEST['email'];
		
		if($email != '') {
			   $sql = "SELECT * FROM tbl_user WHERE var_email = '" . $email . "'";
      		   $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			   if($row = db_fetch_array($result)) {
			      //There is an email like this on the system   
			   		 
			   } else {
			   		//The email doesn't exist on the system
			   		echo $msg['msgs'][$lang]['emailNotExist'];
			   		exit(0);
			   }
			
		
		} 
	
	  if($email != '') {
	     //Send an email to the logged email
	     $link = $root_server_url . '/clear-pass-phone.php?action=' . md5(date('Ymd') . $email . $unique_pass_reset . '&user=' . $email);
	     cc_mail($email, $msg['msgs'][$lang]['pass']['title'], $msg['msgs'][$lang]['pass']['pleaseClick'] ."<a href=\"$link\">$link</a>", $cnf['webmasterEmail']);
	     echo $msg['msgs'][$lang]['pass']['checkAndClick'];
	  } else {
	  	 echo $msg['msgs'][$lang]['pass']['pleaseEnterEmail'];
	  
	  
	  }
	


	
 }
?>
