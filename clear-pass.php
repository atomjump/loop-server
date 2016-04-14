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
      	    $result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
			        
		        	
          header("Location: index.php");
          //Password cleared
          
       } else {
       		echo "Sorry, that link has not reset your password. Please try again, and ensure you use the same device and browser.";
       
       }
    } else {
    	  echo "Sorry, that link has not reset your password. Please try again, and ensure you use the same device and browser.";
    
    }
 
 } else {
	
	  
		$email = $_REQUEST['email'];
		if($email == '') {
			 $email = $_SESSION['logged-email'];
		} else {
			   $sql = "SELECT * FROM tbl_user WHERE var_email = '" . $email . "'";
      $result = mysql_query($sql)  or die("Unable to execute query $sql " . mysql_error());
			   if($row = mysql_fetch_array($result)) {
			      //There is an email like this on the system   
			   		 
			   } else {
			   		echo "Sorry, that email does not exist.";
			   		exit(0);
			   
			   }
			
		
		}
	
	  if($email != '') {
	     //Send an email to the logged email
	     $_SESSION['temp-email'] = $email;
	     $link =$root_server_url . '/clear-pass.php?action=' . md5(date('Ymd') . $email . 'sckskfjfnsll24hdb'); //todo improve this algo
	     cc_mail($email, "AtomJump Loop password reset", "Please click the following link to clear your password:\n\n<a href=\"$link\">$link</a>", "webmaster@atomjump.com");
	     echo "Check your email and click the link provided there.";
	  } else {
	  	 echo "Please enter your email above. Then click here.";
	  
	  
	  }
	


	
 }
?>
