<?php

//Reading from and writing to social networks
//eg. Twitter/Facebook and any other ones
 
class cls_social {

   public $networks = array(array( "Via Twitter:", "twt"),
	                           array( "Via Facebook:", "fbk" ));
	  

		 
		 public function twitter_query($url, $requestMethod, $postfields)
		 {
		     
		    // Set keys
		    
		   global $cnf;
		    
     $consumerKey = $cnf['social']['twitter']['consumerKey'];   
     $consumerSecret = $cnf['social']['twitter']['consumerSecret']; 
     $accessToken = $cnf['social']['twitter']['accessToken']; 
     $accessTokenSecret = $cnf['social']['twitter']['accessTokenSecret'];  


				$settings = array(
        'oauth_access_token' => $accessToken,
        'oauth_access_token_secret' => $accessTokenSecret,
        'consumer_key' => $consumerKey,
        'consumer_secret' => $consumerSecret
     );
	
								
					$twitter = new TwitterAPIExchange($settings);
					
					if($requestMethod == 'POST'){
				   $response = $twitter->buildOauth($url, $requestMethod)
	                  ->setPostfields($postfields)
	                  ->performRequest();
    } else {
         //a get req
    			   $response = $twitter->setGetField($postfields)
    			            ->buildOauth($url, $requestMethod)
	                  ->performRequest();

    }
    
    return $response;
		 
		 }

   public function write_twitter($message)
   {
   



     // Create object
     $url = 'https://api.twitter.com/1.1/statuses/update.json';
				$requestMethod = 'POST';
				
				// Set status message
     $tweetMessage = $message;

			  // Check for 140 characters
     if(strlen($tweetMessage) <= 140)
     {
         // Post the status message
								$postfields = array(
				        'status' => $tweetMessage
				     );
				     
				     $response = $this->twitter_query($url, $requestMethod, $postfields);
								
								return $response;
     }
				
				 return false;
 

   
   }
   
   public function search_twitter($search, $last_id = null)
   {
   	  //TODO see https://dev.twitter.com/rest/public/search
   			$url = 'https://api.twitter.com/1.1/search/tweets.json';
   			$requestMethod = 'GET';
			  
				  $getfield = '?q=' . urlencode($search);
				  
				  if($last_id) {
				     $getfield .= '&since_id=' . $last_id;
						}				  
				  
				

				  //q=%23freebandnames&since_id=24012619984051000&max_id=250126199840518145&result_type=mixed&count=4
   			$response = $this->twitter_query($url, $requestMethod, $getfield);
								
					return json_decode($response);
   }
   

}


?>