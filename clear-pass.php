<?php 
 require('config/db_connect.php');
 global $cnf; 
 global $msg;
 global $lang;
 
if(isset($cnf['email']['sending']['vendor']['mailgun']['key'])) {
	$unique_pass_reset = $cnf['db']['user'] . $cnf['email']['sending']['vendor']['mailgun']['key'];	//This should be unique per installation.	
} else {
	$unique_pass_reset = $cnf['db']['user'] . $cnf['db']['pass'];	//Should also be unique per installation - the number is not shown.
}


$main_message = "";
$follow_on_link = "https://atomjump.com";
if($cnf['serviceHome']) {
	$follow_on_link = add_subdomain_to_path($cnf['serviceHome']);
}

    
 if($_REQUEST['action']) {
   
   //decrypt 
   $action = md5(date('Ymd') . $_SESSION['temp-email'] . $unique_pass_reset);
   if($action == $_REQUEST['action']) {
       //check is valid timewise
   
       //clear user's password
       
       if($_SESSION['temp-email']) {
          $sql = "UPDATE tbl_user SET var_pass = NULL WHERE var_email = '" . $_SESSION['temp-email'] . "'";
      	  $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			
		   $target_url = add_trailing_slash(add_subdomain_to_path($cnf['serviceHome']));	        
		       	
           header("Location: " . $target_url);
          //Password cleared
          
       } else {
       		$main_message = $msg['msgs'][$lang]['passwordNotReset'];
       
       }
    } else {
    	  $main_message = $msg['msgs'][$lang]['passwordNotReset'];
    
    }
 
 } else {
	
	  
		$email = $_REQUEST['email'];
		if($email == '') {
			 $email = $_SESSION['logged-email'];
		} else {
			   $sql = "SELECT * FROM tbl_user WHERE var_email = '" . $email . "'";
      		   $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			   if($row = db_fetch_array($result)) {
			      //There is an email like this on the system   
			   		 
			   } else {
			   		$main_message = $msg['msgs'][$lang]['emailNotExist'];
			   		include("components/basic-page.php");
			   		exit(0);
			   
			   }
			
		
		}
	
	  if($email != '') {
	     //Send an email to the logged email
	     $_SESSION['temp-email'] = $email;
	     $link = $root_server_url . '/clear-pass.php?action=' . md5(date('Ymd') . $email . $unique_pass_reset); 
	     cc_mail_direct($email, $msg['msgs'][$lang]['pass']['title'], $msg['msgs'][$lang]['pass']['pleaseClick'] ."<a href=\"$link\">$link</a>", $cnf['email']['webmasterEmail']);
	     $main_message = $msg['msgs'][$lang]['pass']['checkAndClick'];
	  } else {
	  	 $main_message = $msg['msgs'][$lang]['pass']['pleaseEnterEmail'];
	  }
	
 }
 
 
 include("components/basic-page.php");
?>
