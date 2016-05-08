<?php 
	require('config/db_connect.php');
	

	/*
	$_SESSION['authenticated-layer'] = '';
	$_SESSION['logged-user'] = '';
	$_SESSION['logged-email'] = '';
	$_SESSION['user-ip'] = '';
	$_SESSION['temp-user-name'] = '';
	$_SESSION['lat'] = '';
	$_SESSION['lon'] = '';
	$_SESSION['logged-group-user'] = '';
	$_SESSION['layer-group-user'] = '';
	
	$_SESSION['view-count'] = 0; //testing this
 */

 error_log("Logging out");
 
 // Unset all of the session variables.
 $_SESSION = array();

 // If it's desired to kill the session, also delete the session cookie.
 // Note: This will destroy the session, and not just the session data!
 if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
 }
 
 // Remove any cookies
 
 setcookie("your_name", "", time() - 3600);
 setcookie("email", "", time() - 3600);
 setcookie("phone", "", time() - 3600);
 setcookie("your_password", "", time() - 3600);


 // Finally, destroy the session.
 session_destroy();
 
?>
