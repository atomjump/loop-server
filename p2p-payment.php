<?php
		require_once('config/db_connect.php');
		
		
		 //https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=youremail%40yoursite%2ecom&lc=US&item_name=AtomJump%20Loop%20Forum%20Payment&amount=21%2e00&currency_code=USD&button_subtype=services&no_note=0&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHostedGuest
		function CurlMePost($url,$post){ 
			// $post is a URL encoded string of variable-value pairs separated by &
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $post); 
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 3); // 3 seconds to connect
			curl_setopt ($ch, CURLOPT_TIMEOUT, 10); // 10 seconds to complete
			$output = curl_exec($ch);
			curl_close($ch);
			return $output;
		}
		
		
		//Get user's email address
		$sql = "SELECT * FROM tbl_user WHERE int_user_id = " . $_REQUEST['user_id'];
		
		$result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		if($row = db_fetch_array($result))
		{
			$email_to = $row['var_email'];
		
			if(isset($email_to)) {
			
			
			 $sql = "SELECT * FROM tbl_ssshout WHERE int_ssshout_id = " . $_REQUEST['msgid'];
		
				$resultb = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
				if($rowb = db_fetch_array($resultb))
				 {
				     $msg = summary($rowb['var_shouted'], 25);
				  }
			
				//"https://www.paypal.com/cgi-bin/webscr?actionType=pay&cancelUrl=http://atomjump.com&returnUrl=http://atomjump.com&errorLanguage=en_US&"
				/************************************************
				Note: $cost variable must be provided by you.
				In this example, I am sending over only 1 item, 
				thus item cost and total cost are the same.
				************************************************/
				/*
				$cost = $_REQUEST['amount'];
				
				//$baseurl = 'https://api-3t.sandbox.paypal.com/nvp'; //sandbox
				$baseurl = 'https://api-3t.paypal.com/nvp'; //live
				//$username = urlencode('yourApiUsernameFromPaypal');
				//$password = urlencode('yourApiPasswordFromPaypal');
				//$signature = urlencode('yourSignatureFromPaypal');
				$returnurl = urlencode('https://atomjump.com'); // where the user is sent upon successful completion
				$cancelurl = urlencode('https://atomjump.com'); // where the user is sent upon canceling the transaction
				//$post[] = "USER=$username";
				//$post[] = "PWD=$password";
				//$post[] = "SIGNATURE=$signature";
				//$post[] = "VERSION=65.1"; // very important!
				$post[] = "PAYMENTREQUEST_0_CURRENCYCODE=" . $_REQUEST['currencyCode']; 
				$post[] = "PAYMENTREQUEST_0_AMT=$cost";
				$post[] = "PAYMENTREQUEST_0_ITEMAMT=$cost";
				$post[] = "PAYMENTREQUEST_0_PAYMENTACTION=Sale"; // do not alter
				$post[] = "L_PAYMENTREQUEST_0_NAME0=AtomJump%20Loop%20Payment"; // use %20 for spaces
				$post[] = "L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital"; // do not alter
				$post[] = "L_PAYMENTREQUEST_0_QTY0=1";
				$post[] = "L_PAYMENTREQUEST_0_AMT0=$cost";
				$post['returnurl'] = "RETURNURL=$returnurl"; // do not alter
				$post['cancelurl'] = "CANCELURL=$cancelurl"; // do not alter
				$post['method'] = "METHOD=SetExpressCheckout"; // do not alter
				 
				$post_str = implode('&',$post);
				$output_str = CurlMePost($baseurl,$post_str);
				parse_str($output_str,$output_array);
				$ack = $output_array['ACK'];
				$token = (!empty($output_array['TOKEN'])) ? $output_array['TOKEN'] : '';
				
				
				//$redirecturl = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=$token"; //sandbox
				
				
				$redirecturl = "https://www.paypal.com/incontext?token=$token"; //live
				*/
				
				$redirecturl = "https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=" . urlencode($email_to) . "&lc=US&item_name=". urlencode($msg). "&amount=".urlencode($_REQUEST['amount']). "&currency_code=". $_REQUEST['currencyCode'] ."&button_subtype=services&no_note=0&bn=PP%2dBuyNowBF%3abtn_buynowCC_LG%2egif%3aNonHostedGuest";
				
				
				//Good but testing with js below: header("Location: " . $redirecturl);
			} else {
				echo $msg['msgs'][$lang]['cannotVerifyForPayment'];
				exit(0);
			}
		}

?>
<html>
    <head>
    	<script>
    	window.location = "<?php echo $redirecturl ?>";
    	</script>
	</head>
</html>
