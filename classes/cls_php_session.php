<?php
//Based on code from http://www.tonymarston.net/php-mysql/session-handler.html
//
//Usage:
//require_once 'classes/cls_php_session.php';
/*$session_class = new php_Session;
   session_set_save_handler(array(&$session_class, 'open'),
                         array(&$session_class, 'close'),
                         array(&$session_class, 'read'),
                         array(&$session_class, 'write'),
                         array(&$session_class, 'destroy'),
                         array(&$session_class, 'gc'));

Table required:

CREATE TABLE `php_session` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `user_id` varchar(16) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `session_data` longtext,
  PRIMARY KEY (`session_id`),
  KEY `last_updated` (`last_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


*/



class php_Session 
{
    // ****************************************************************************
    // This class saves the PHP session data in a database table.
    // ****************************************************************************
    
    // ****************************************************************************
    // class constructor
    // ****************************************************************************
    function php_Session ()
    {
        // save directory name of current script
     
     
    } // php_Session
    
    
    
    
    // ****************************************************************************
    function open ($save_path, $session_name)
    // open the session.
    {
        // do nothing
        return TRUE;
        
    } // open
    
    
    
    // ****************************************************************************
    function close ()
    // close the session.
    {
        if (!empty($this->fieldarray)) {
            // perform garbage collection
            $result = $this->gc(ini_get('session.gc_maxlifetime'));
            return $result;
        } // if
        
        return FALSE;
        
    } // close
    
    
    function bot_detected() 
    {
					
       if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
          return TRUE;
       }
       else {
          return FALSE;
       }

    }
    
    // ****************************************************************************
    function read ($session_id)
    // read any data for this session.
    {
      global $db;

      if($this->bot_detected()) {
    	      //check if bot session is available, if not create it
    	    		return TRUE;
     	}
    
    
    	$sql = "SELECT * FROM php_session WHERE session_id='" .clean_data($session_id) ."'";
        $result = dbquery($sql)  or die("Unable to execute query $sql " . dberror());
		while($row = db_fetch_array($result))
		{
          	$fieldarray[] = $row;
        }
        

        
        if (isset($fieldarray[0]['session_data'])) {
            $this->fieldarray = $fieldarray[0];
             
            return $fieldarray[0]['session_data'];
        } else {
            
            return '';  // return an empty string
        } // if
        
    } // read
    
    // ****************************************************************************
    function write ($session_id, $session_data)
    // write session data to the database.
    {
    	global $db;
    	global $staging;
    
        make_writable_db();			//Ensure we are writable
	    if(!isset($db)) {
    		error_log("Db doesn't exist after make_writable!");
    	} 


        
    	if($this->bot_detected()) {
    	      //check if bot session is available, if not create it    	    		
    		return TRUE;
        	    			 
    	}
    		
         if (!empty($this->fieldarray)) {
            if ($this->fieldarray['session_id'] != $session_id) {
                // user is starting a new session with previous data
                $this->fieldarray = array();
            } // if
        } // if
        
        
        
        if (empty($this->fieldarray)) {
        
           
            // create new record
            $array['session_id']   = $session_id;
            $array['session_data'] = clean_data($session_data);
            
            $sql = "INSERT INTO php_session(session_id,
					date_created,
					last_updated,
					session_data) VALUES ( '" . clean_data($array['session_id']) . "',
								NOW(),
								NOW(),
								'" . $array['session_data'] . "')";
 	          
 	     						$result = dbquery($sql) or error_log("Unable to execute query $sql " . dberror());
        
          } else {
        
            // update existing record
            if (isset($_SESSION['logged-user'])) {//was user_id
                $array['user_id']  = $_SESSION['logged-user'];//was user_id
            } // if
            $array['session_data'] = clean_data($session_data);
            $sql = "UPDATE php_session SET 
				user_id = '" . clean_data($array['user_id']) .  "' ,
				last_updated = NOW(),
				session_data ='" . $array['session_data'] . "'
				WHERE session_id = '" . $this->fieldarray['session_id'] . "'";

			 $result = dbquery($sql)  or error_log("Unable to execute query $sql " . dberror());
        } // if   NOTE: experimental clean_data()
        
        return TRUE;
        
    } // write    
    
    
    
    // ****************************************************************************
    function destroy ($session_id)
    // destroy the specified session.
    {
    	  make_writable_db();			//Ensure we are writable
       
       $sql = "DELETE FROM php_session WHERE session_id = '" . $this->fieldarray['session_id'] . "'";
       $result = dbquery($sql)  or error_log("Unable to execute query $sql " . dberror());
       
       return TRUE;
        
    } // destroy
    
    
    // ****************************************************************************
    function gc ($max_lifetime)
    // perform garbage collection.
    {
       
    
    	  make_writable_db();			//Ensure we are writable
    	
    	
    	   
    	   date_default_timezone_set("UTC");  //should be always UTC I think - not configurable from config
    	   
        $real_now = date('Y-m-d H:i:s');
        
        $dt1 = strtotime("$real_now -86400000 seconds");  //60*60*24*1000 = 1000 days because our servers all have different timezones.
        $dt2 = date('Y-m-d H:i:s', $dt1);
        
        $dtc1 = strtotime("$real_now -86400 seconds");  //60*60*24 = 1 day clearout null delay because our servers all have different timezones.
        $dtc2 = date('Y-m-d H:i:s', $dtc1);
   
        
        $sql = "DELETE FROM php_session WHERE last_updated < '$dt2' OR (last_updated < '$dtc2' AND (user_id IS NULL OR user_id = ''))";
        $result = dbquery($sql)  or error_log("Unable to execute query $sql " . dberror());
        //$count = $this->_dml_deleteSelection("last_updated < '$dt2'");
        
        return TRUE;
        
    } // gc
    
   
    
    
    
    
    
    // ****************************************************************************
    function __destruct ()
    // ensure session data is written out before classes are destroyed
    // (see http://bugs.php.net/bug.php?id=33772 for details)
    {
        @session_write_close();

    } // __destruct
    
// ****************************************************************************
} // end class
// ****************************************************************************

?>
