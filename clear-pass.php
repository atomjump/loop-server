<?php 
	require('config/db_connect.php');

 if($_REQUEST['action']) {
   
   //decrypt 
   $action = md5(date('Ymd') . $_SESSION['temp-email'] . 'sckskfjfnsll24hdb');
	   if($action == $_REQUEST['action']) {
       //check is valid timewise
   
       //clear user's password
       
       if($_SESSION['temp-email']) {
          $sql = "UPDATE tbl_user SET var_pass = NULL WHERE var_email = '" . $_SESSION['temp-email'] . "'";
      	    $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			        
		        	
          header("Location: index.php");
          //Password cleared
          
       } else {
       		echo $msg['msgs'][$lang]['passwordNotReset'];
       
       }
    } else {
    	  echo $msg['msgs'][$lang]['passwordNotReset'];
    
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
			   		echo $msg['msgs'][$lang]['emailNotExist'];
			   		exit(0);
			   
			   }
			
		
		}
	
	  if($email != '') {
	     //Send an email to the logged email
	     $_SESSION['temp-email'] = $email;
	     $link =$root_server_url . '/clear-pass.php?action=' . md5(date('Ymd') . $email . 'sckskfjfnsll24hdb'); //todo improve this algo
	     cc_mail($email, $msg['msgs'][$lang]['pass']['title'], $msg['msgs'][$lang]['pass']['pleaseClick'] ."<a href=\"$link\">$link</a>", $cnf['webmasterEmail']);
	     echo $msg['msgs'][$lang]['pass']['checkAndClick'];
	  } else {
	  	 echo $msg['msgs'][$lang]['pass']['pleaseEnterEmail'];
	  
	  
	  }
	


	
 }
?>
