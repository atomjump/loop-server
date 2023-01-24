<?php
	
	/* OR perhaps use this version, which was the version from the Notifications plugin
	
	*/
	
	$default_name = "chat-inner-1.3.34.js";										//This should be updated when the Javascript file
																				//is updated. And you should 'git mv' the file to the
																				//new version number. Don't forget to also update the default config/configORIGINAL.json file
																				//in both places also. E.g. "chatInnerJSFilename": "/js/chat-inner-1.3.34.js"
	
	
	switch($inner_type) {
		case "search-secure":
	
			if(isset($cnf['chatInnerJSFilename']) && (file_exists(__DIR__ . $cnf['chatInnerJSFilename']))) {
				$chat_inner_js_filename = $cnf['chatInnerJSFilename'];
			} else {
				//The default version
				$chat_inner_js_filename = "/js/" . $default_name;			
			}
		break;
		
		default:
			if(isset($cnf['chatInnerJSFilename']) &&
			  (file_exists(add_trailing_slash($cnf['fileRoot']) . $cnf['chatInnerJSFilename']) )
			  ) {
					$chat_inner_js_filename = $cnf['chatInnerJSFilename'];
					$inner_js = trim_trailing_slash($webroot) . $chat_inner_js_filename;
			} else {
				//The default version
				$chat_inner_js_filename = "js/" . $default_name;			//Use the local version default version
				$inner_js = $chat_inner_js_filename;
			}
		break;	
	}

?>
