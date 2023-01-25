<?php 
	require('config/db_connect.php');

	//This version runs from the direct login within the standalone app, but is very simliar to clear-pass.php.
	//Changes should be made to both.

	//For plugins - language change in particular
	require($start_path . "classes/cls.layer.php");
	require($start_path . "classes/cls.ssshout.php");
	
	require($start_path . "classes/cls.pluginapi.php");
	
	$ly = new cls_layer();
	$sh = new cls_ssshout();
	$api = new cls_plugin_api();	
	


   global $cnf; 
   global $msg;
   global $lang;
   $screen_type = "resetpass";	
   
   	$subdomain = check_subdomain();
	$webroot = trim_trailing_slash($cnf['webRoot']);
	
	//Substitute [subdomain] with the actual subdomain if allowed and it exists
	if((isset($cnf['readURLAllowReplacement'])) && ($cnf['readURLAllowReplacement'] == true)) {
		if((isset($cnf['readURLIncludeDot'])) && ($cnf['readURLIncludeDot'] == true)) {
			$webroot = trim_trailing_slash(str_replace('[subdomain]', $subdomain . ".", $webroot));
		} else {
			$webroot = trim_trailing_slash(str_replace('[subdomain]', $subdomain , $webroot));
		}
	} else {
		$webroot = trim_trailing_slash(str_replace('[subdomain]', "", $webroot));		//Remove any mention of subdomains
	}
			

	include("components/inner_js_filename.php");
 
 
   if(isset($cnf['email']['sending']['vendor']['mailgun']['key'])) {
 		$unique_pass_reset = $cnf['db']['user'] . $cnf['email']['sending']['vendor']['mailgun']['key'];	//This should be unique per installation.	
   } else {
        $unique_pass_reset = $cnf['db']['user'] . $cnf['db']['pass'];	//Should also be unique per installation - the number is not shown.
   }	
 
 if($_REQUEST['action']) {
 	//Get default messages to display
   	$main_message = "";
	$follow_on_link = "https://atomjump.com";
	if($cnf['serviceHome']) {
		$follow_on_link = add_subdomain_to_path($cnf['serviceHome']);
	}
	$first_button_wording = "&#8962;";		//A 'home' UTF-8 char

	$first_button = $follow_on_link;
 
 
   $email = $_REQUEST['user'];
   
   //decrypt 
   $action = md5(date('Ymd') . $email . $unique_pass_reset);
   if($action == $_REQUEST['action']) {
       //check is valid timewise
   
       //clear user's password
       
          $sql = "UPDATE tbl_user SET var_pass = NULL WHERE var_email = '" . $email . "'";
      	  $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			        
           $main_message = $msg['msgs'][$lang]['passwordBeenResetApp'];
          //Password cleared
          
    } else {
    	  $main_message = $msg['msgs'][$lang]['passwordNotReset'];
    
    }
    
    $display_email = $email;
    include("components/basic-input-page.php");
 
 } else {
	
	  	
		$email = $_REQUEST['email'];
		
		if($email != '') {
			   $sql = "SELECT * FROM tbl_user WHERE var_email = '" . $email . "'";
      		   $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
			   if($row = db_fetch_array($result)) {
			      //There is an email like this on the system   
			   		 
			   } else {
			   		//The email doesn't exist on the system
			   		$output = $msg['msgs'][$lang]['emailNotExist'];
			   		
			   		$json = $output;

					//This is a jquery ajax json call, so we need a proper return
					if(isset($_GET['callback'])) {
						echo $_GET['callback'] . "(" . json_encode($json) . ")";
					} else {
						echo $output;
					}

			   		exit(0);
			   }
			
		
		} 
	
	  if($email != '') {
	     //Send an email to the logged email
	     $link = $root_server_url . '/clear-pass-phone.php?action=' . md5(date('Ymd') . $email . $unique_pass_reset) . '&user=' . $email;
	     $output = $msg['msgs'][$lang]['pass']['checkAndClick'];
	     cc_mail_direct($email, $msg['msgs'][$lang]['pass']['title'], $msg['msgs'][$lang]['pass']['pleaseClick'] ."<a href=\"$link\">$link</a>", $cnf['email']['webmasterEmail']);
	     
	  } else {
	  	 $output = $msg['msgs'][$lang]['pass']['pleaseEnterEmail'];
	  
	  
	  }
	
	  $json = $output;

	  //This is a jquery ajax json call, so we need a proper return
	  if(isset($_GET['callback'])) {
		  echo $_GET['callback'] . "(" . json_encode($json) . ")";
	  } else {
	  	echo $output;
	  }

 }
 
 //Note: needs a newline below after the 'greater' sign.
?>
