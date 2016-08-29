//Language & messages configuration
//Note: also see /config/messages.json for further messages configuration
var lsmsg = {
    "defaultLanguage" : "en",
    "msgs": {
        "en":{
              defaultYourName: 'Your Name',
              defaultYourEmail: 'Your Email',
              loggedIn: 'Logged in. Please wait..',
              passwordWrong: 'Sorry, your password is not correct.',
              passwordStored: 'Thanks, your password is now set.',
              registration: 'Thanks for registering.  To confirm your email address we\'ve sent an email with a link in it, which you should click within a day.',
              badResponse: 'Sorry, response is: ',
              more: 'More'
        },
        "es":{
              defaultYourName: 'Tu Nombre',
              defaultYourEmail: 'Su e-mail',
              loggedIn: 'Conectado Por favor, espere..',
              passwordWrong: 'Lo siento, la contraseña no es correcta.',
              passwordStored: 'Gracias, su contraseña se establece ahora.',
              registration: 'Gracias por registrarse. Para confirmar su dirección de correo electrónico que\'ve enviado un correo electrónico con un enlace en ella, lo que debe hacer clic en un día.',
              badResponse: 'Lo siento, la respuesta es: ',
              more: 'Mas'
        }       
    }
}
var lang = lsmsg.defaultLanguage;       





function myTrim(x)
{
	return x.replace(/^\s+|\s+$/gm,'');
}

function getCookie(cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++)
	{
		var c = myTrim(ca[i]);// ie8 didn't support .trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	}
	return "";
}

function setCookieNew(name,value,expires){
   document.cookie = name + "=" + value + ((expires==null) ? "" : ";expires=" + expires.toGMTString());
}

function cookieOffset()
{
  //Should output: Thu,31-Dec-2020 00:00:00 GMT
  var cdate = new Date;
  var expirydate=new Date();
  expirydate.setTime(expirydate.getTime()+(365*3*60*60*24*1000))
  var write = expirydate.toGMTString();
  
  return write;
}

function hideKeyboard(element) {
    element.attr('readonly', 'readonly'); // Force keyboard to hide on input field.
    element.attr('disabled', 'true'); // Force keyboard to hide on textarea field.
    setTimeout(function() {
        element.blur();  //actually close the keyboard
        // Remove readonly attribute after keyboard is hidden.
        element.removeAttr('readonly');
        element.removeAttr('disabled');
    }, 100);
}

function assignPortToURL(url, portNum) {
    if((portNum)&&(portNum != "")) {
        url = url.replace(/\/\/(.*?)\//, "//$1:" + portNum + "/");
    } else {
        //Do nothing if no port number 
           
    }
    return url;
}

var commentLayer="comments";
var ssshoutFreq = 5000;
var ssshoutHasFocus = true;
var myLoopTimeout;
var whisperOften = "1.1.1.1:2"; //defaults to admin user's ip and id
var whisperSite = "1.1.1.1:2"; //defaults to admin user's ip and id
var cs = 2438974;
var ssshoutServer = "https://atomjump.com/api";  //https://atomjump.com/api  normally
var typingTimer = 0;
var startShoutId = 0;		//start of a typing session

var currentlyTyping = false;
var records = 25;			//once more is clicked we will allow more
var showMore = 25;
var sendPublic = false;  //if true, override to a public social network response
var shortCode = "";  //shortcode for social network eg. twt, fbk
var publicTo = "";  //who on social network we are sending to eg. twitter handle


//Check for android browser
var navU = navigator.userAgent;

// Android Mobile
var isAndroid = navigator.userAgent.indexOf('Android') >= 0;
var webkitVer = parseInt((/WebKit\/([0-9]+)/.exec(navigator.appVersion) || 0)[1],10) || void 0; // also match AppleWebKit
var isNativeAndroid = isAndroid && webkitVer <= 534 && navigator.vendor.indexOf('Google') == 0;

if(isAndroid) {
		isNativeAndroid = true;
}


function initAtomJumpFeedback(params)
{
	commentLayer = params.uniqueFeedbackId;
	whisperOften = params.myMachineUser;
	whisperSite = params.myMachineUser;
	if(params.server){
	  ssshoutServer = params.server;
 }

}

//Run automatically
if(typeof ajFeedback !== 'undefined') {
	initAtomJumpFeedback(ajFeedback);
} 




function receiveMessage(msg)
{
	if(typeof jQuery == 'undefined') {
		//IE was complaining on the form close that jquery no longer existed in this frame.
	} else {
		if(!msg) {
			//Settings
			if($("#comment-popup-content").is(':visible')) {
				$("#comment-popup-content").hide();
				$("#comment-upload").hide();
				$("#comment-options").show();
			} else {
				$("#comment-popup-content").show();
				$("#comment-upload").hide();
				$("#comment-options").hide();
			}
		} else {
			if(msg == "upload") {
				//Upload
				if($("#comment-popup-content").is(':visible')) {
					$("#comment-popup-content").hide(); 
					$("#comment-upload").show();
					$("#comment-options").hide();
				} else {
					$("#comment-popup-content").show();
					$("#comment-upload").hide();
					$("#comment-options").hide();
				}
			} else {
				//Settings
				if($("#comment-popup-content").is(':visible')) {
					$("#comment-popup-content").hide();
					$("#comment-upload").hide();
					$("#comment-options").show();
				} else {
					$("#comment-popup-content").show();
					$("#comment-upload").hide();
					$("#comment-options").hide();
				}
			
			}
		
		}
	}
	
}

var waitForCommitTimer = false;
function waitForCommitFinish()
{
	
	waitForCommitTimer = setTimeout(function(){
		if($('#typing').val() == 'on') {
			waitForCommitFinish();
		} else {
			clearTimeout(waitForCommitTimer);
			registerNewKeypress();
		}
		
	}, 50);
}

var msg = function() {
	this.localMsgId = 1;
	this.localMsg = {};	//Must be an object for iteration
	
	function newMsg(whisper)
	{
		this.localMsg[this.localMsgId] = {};
		this.localMsg[this.localMsgId].typing = "on";
		this.localMsg[this.localMsgId].shoutId = "";
		this.localMsg[this.localMsgId].status = "requestId";
		this.localMsg[this.localMsgId].whisper = whisper;
		this.localMsg[this.localMsgId].whisperOften = whisperOften;
		this.localMsg[this.localMsgId].shouted = $('#shouted').val();
		this.localMsg[this.localMsgId].shortCode = shortCode;
		this.localMsg[this.localMsgId].shortCode = publicTo;
	    this.processEachMsg();

		records = showMore;	//If we had clicked more before, we want to reduce again

		return false;
	}
	this.newMsg = newMsg;


	
	
	function commitMsg(whisper)
	{
		//Commiting a new message locally
		
		if(sendPublic == true) {
		   //override
		   whisper = false;
		}
		this.localMsg[this.localMsgId].whisper = whisper;
		this.localMsg[this.localMsgId].whisperOften = whisperOften;
		this.localMsg[this.localMsgId].shouted = $('#shouted').val();		//Save whatever was entered when pushing enter or clicking send
		this.localMsg[this.localMsgId].typing = "off";
		this.localMsg[this.localMsgId].status = "committed";
  	    this.localMsg[this.localMsgId].shortCode = shortCode;
 	    this.localMsg[this.localMsgId].publicTo = publicTo;

		//Clear the shout input box
		$('#shouted').val('');
		$('#shouted').removeAttr('value');	//testing iphone
		if(isNativeAndroid) {
			//If the keyboard is left on, the DOM isn't updated
			hideKeyboard($('#shouted'));
		}
		$('#shouted').focus();



		//Go ahead and start processing all messages outstanding
		this.processEachMsg();

		this.localMsgId ++;		//next message local id


		//Allow a new message to be generated by typing again
		currentlyTyping = false;
		
		records = showMore;	//If we had clicked more before, we want to reduce again
		
		return false;
	}
	this.commitMsg = commitMsg;

	function finishMsg(msgId)
	{
		//Remove from local array
		this.localMsg[msgId] = {};
		delete this.localMsg[msgId];
	}
	this.finishMsg = finishMsg;

	function updateMsg(msgId, shoutId, status)
	{
		this.localMsg[msgId].shoutId = shoutId;
		this.localMsg[msgId].status = status;

	}

	this.updateMsg = updateMsg;

	function deactivateMsg(msgId)
	{
		//Remove message from server side
		if(msgId == this.localMsgId) {	//only if the current message
			this.localMsg[msgId].status = "deactivate";
			
			this.processEachMsg();
			
		}
	}

	this.deactivateMsg = deactivateMsg;
	
	function reactivateMsg(msgId)
	{
		//Check if in deactivating state
		if(this.localMsg[msgId]) {
			if(this.localMsg[msgId].status) {
				if(this.localMsg[msgId].status == 'deactivate') {
					this.localMsg[msgId].status = 'restarting';
					this.localMsg[msgId].shouted = $('#shouted').val();
					this.processEachMsg();		//start again
				}
			}
		}
	}
	
	this.reactivateMsg = reactivateMsg;


	function deactivateAll()
	{
		//Remove all outstanding messages
		$.ajaxSetup({async:false});		//Since we're closing down the window, we should be able to process all
																		//messages syncronously just in case the browser window has been closed

		var mythis = this;
		$.each(mythis.localMsg, function(key, value) {
			mythis.localMsg[key].status = "deactivate"; 
		});
		
		//Now resend all outstanding messages
		this.processEachMsg();
		
		$.ajaxSetup({async:true});		//Coming back out 

	
	}
	this.deactivateAll = deactivateAll;

	function processEachMsg()
	{
		//Loop through each message in the array
		var mythis = this;
		$.each(mythis.localMsg, function(key, value) {
			if(value.status == "deactivate") {
				//Start the deactivate process if we know the id
				if(value.shoutId) {
					var myShoutId = value.shoutId;
					var myKey = key;
					
					
					$.getJSON(ssshoutServer + "/de.php?callback=?", {
						mid: value.shoutId,
						just_typing: 'on'
					}, function(response){ 
						var results = response;
						refreshResults(results);
					});
				}
			} else {

				if(value.status == "requestId") {
					//Call for a new shoutId
					mythis.localMsg[key].status = "typing";

					$('#typing-now').val("on");
					$('#message').val(value.shouted); 
					$('#msg-id').val(key);		
					$('#shout-id').val("");
					
					submitShoutAjax(whisper, false, key);	//false for typing
					
				} else {
					//Typing or waiting for completion
					if(value.typing == "off") {



						if((value.status != "complete")&&
						   (value.status != "sending")) {
							
							
							//Check if we have our id yet
							if(value.shoutId) {
								//Ready to send
								
								$('#typing-now').val('off');
								$('#message').val(value.shouted);
								$('#msg-id').val(key);
								$('#shout-id').val(value.shoutId);
								submitShoutAjax(value.whisper, true, key);	//true for commit
								mythis.localMsg[key].status = "sending";
							}

						} else {
							if(value.status == "complete") {
								//Complete - let's remove from our local array
								mythis.finishMsg(key);
							}
						}
					} else {
						
						if(value.shoutId) {
								//Ready to restart
								if(value.status =="restarting") {
									$('#typing-now').val('on');
									$('#message').val(value.shouted);
									$('#msg-id').val(key);
									$('#shout-id').val(value.shoutId);
									submitShoutAjax(value.whisper, false, key);	//false for commit
									mythis.localMsg[key].status = "typing";
								}
						}
					}
				}
			}

		});

	}

	this.processEachMsg = processEachMsg;
}


function registerNewKeypress()
{
	if(typingTimer) {
		clearTimeout(typingTimer);
	}		//Extend the timer

	var myMsgId = mg.localMsgId;
	typingTimer = setTimeout(function() { 
  		//Delete the typing message (or rather deactivate it)
		mg.deactivateMsg(myMsgId);

	}, 10000);	//30000);

}

//Global msg
var mg = new msg();


$(document).ready(function() {
			var email = getCookie("email");
			var yourName = getCookie("your_name");
			var password = getCookie("your_password");
			var setLang = getCookie("lang");
			if(setLang) {
			    lang = setLang;			
			}
			var screenWidth = $(window).width();
			var screenHeight = $(window).height();
			
			//Recieve from parent
			if (window.addEventListener) {
			  window.addEventListener('message', function (e) {
					receiveMessage(e.data);
			  });
			}
			else { // IE8 or earlier
			  window.attachEvent('onmessage', function (e) {
					receiveMessage(e.data);
			  });
			}	
			
			
			//File upload
			$('input[type=file]').on('change', prepareUpload);
			
			ssshoutHasFocus = true;
			doLoop();
				
				$('#comment-show-password').click(function() {
					$("#comment-password-vis").slideToggle();
					
				});
				
				$('#comment-user-code').click(function() {
						//Show the user's ip/code
						
					   $.ajax({
							url: ssshoutServer + '/confirm.php?callback=?', 
							data: "usercode=true&passcode=" + commentLayer,
							crossDomain: true,
							dataType: "jsonp"
						}).done(function(response) {
							var msg = 'myMachineUser: ' + response.thisUser;
							$("#comment-messages").html(msg);
							$("#comment-messages").show();
							
						
							$("#group-users").val(response.layerUsers);
							$("#group-users-form").show();
							$("#set-forum-password-form").show();
						});
				});
				
				$('#shouted').bind('paste',function() {
				 	//Entered a paste operation. Note this wouldn't be detected by a js keypress ordinarily but it does pretty much what the keypress does		
				 	
				 	// Short pause to wait for paste to complete
					setTimeout( function() {
        
        
						//Register that we have started typing
						if(currentlyTyping == false) {
							currentlyTyping = true;
							mg.newMsg(true);  //start typing private message
							registerNewKeypress();
						
						} else {
					
					        mg.reactivateMsg(mg.localMsgId); //if it was deactivated
							registerNewKeypress();
					
						}
				
					}, 100); //end set timeout

				});
				
				$('#shouted').keyup(function(evt) {
					
					evt = evt || window.event;
 					var keyCode = evt.keyCode;

					
         			if((keyCode === 13)||(keyCode === 10)) {
						    //If a return, rely on the submit not the key. On iphone return is 10
						    return false;
       				}
					
					
					//Register that we have started typing
					if(currentlyTyping == false) {
						currentlyTyping = true;
						mg.newMsg(true);  //start typing private message
						registerNewKeypress();
						
					} else {
						
						//Already typing - wait until status is off again before swtching back on 
						mg.reactivateMsg(mg.localMsgId); //if it was deactivated
						registerNewKeypress();
					
					}
				});
							
				
				$('#chat-input-block').append('<input' + ' type="hidden" ' + 'name="cs" ' + ' value="'+ cs + '">');
				
				
		
		});



function whisper(whisper_to, targetName, priv, socialNetwork)
{
   if(typeof(priv) != "undefined") {
      
   		if((priv === false)||(priv == 0)) {
		      //Via a social network - still public. TODO change colour of button?
		 	  whisperOften = whisper_to;		//set global
			  $('#private-button').html("Public to " + targetName);
		 
		      sendPublic = true;
		      shortCode = socialNetwork;
		      publicTo = targetName;
		} else {
		    whisperOften = whisper_to;		//set global
	        $('#private-button').html("Send to " + targetName);
            sendPublic = false;
            shortCode = "";
            publicTo = "";
		   
		}
     
   } else {
   
      whisperOften = whisper_to;		//set global
	  $('#private-button').html("Send to " + targetName);
      sendPublic = false;
      shortCode = "";
      publicTo = "";
   
   }
   
}





function set_options_cookie() {

    var yourName = $('#your-name-opt').val();
    var email = $('#email-opt').val();
    var phone = $('#phone-opt').val();
    
    var sendNewUserMsg = true;
   
    if(yourName == "") {
    	yourName = ""; 
    	document.cookie = 'your_name=' + yourName + '; path=/; expires=' + cookieOffset() + ';';
    } else {
    	document.cookie = 'your_name=' + yourName + '; path=/; expires=' + cookieOffset() + ';';
    }
    $('#name-pass').val(yourName);	//Set the form
   
    
    if(email == "") {
    	sendNewUserMsg = false;
    	email = "";
    	document.cookie = 'email=' + email + '; path=/; expires=' + cookieOffset() + ';';
    } else {
    	document.cookie = 'email=' + email + '; path=/; expires=' + cookieOffset() + ';';
    }
    $("#email").val(email);		//Set the form
    
    
    if(phone == "") {
    	phone = '';
    	document.cookie = 'phone=' + phone + '; path=/; expires=' + cookieOffset() + ';';
    } else {
    	document.cookie = 'phone=' + phone + '; path=/; expires=' + cookieOffset() + ';';
    }
    $("#phone").val(phone);		//Set the form
    
    //Check if we are trying to check against a password
    var forumPass = $('#forumpass').val();
    if(forumPass != "") {    	
    	$("#forumpasscheck").val(forumPass);		//Set the form
    
    }
    
    
    var data = $('#options-frm').serialize();
    
    $.ajax({
			url: ssshoutServer + '/confirm.php?callback=?', 
			data: data,
			crossDomain: true,
			dataType: "jsonp"
		}).done(function(response) {
			var msg = "";
			var toggle = true;
			var reload = false;
			var timeMult= 1;
			
			var mytype = response.split(','); 
		
			switch(mytype[0])
			{
				case "LOGGED_IN":
					
					
					msg = lsmsg.msgs[lang].loggedIn;
					toggle = true;
					$('#comment-logout-text').show();	//show the correct text 
					$('#comment-not-signed-in').hide();
					$('#comment-logout').show();	//show the logout button
					
				break;
				
				case "FORUM_LOGGED_IN":
					toggle = false;
					msg = lsmsg.msgs[lang].loggedIn;
					
				break;
				
				case 'INCORRECT_PASS':		
					msg = lsmsg.msgs[lang].passwordWrong;
					toggle = false;
				break;
				
				case 'STORED_PASS':
					msg = lsmsg.msgs[lang].passwordStored;	
					toggle = true;	
				break;
				
				case 'NEW_USER':
				
				    if(sendNewUserMsg == true) {
					    msg = lsmsg.msgs[lang].registration;
					    toggle = true;
					    $('#comment-logout-text').show();	//show the correct text 
					    $('#comment-not-signed-in').hide();		
					    timeMult = 6;
					} else {
					    toggle = true;
					    $('#comment-logout-text').show();	//show the correct text 
					    $('#comment-not-signed-in').hide();	
					}
				break;
				
				default:
					msg = lsmsg.msgs[lang].badResponse + response;
					toggle = false;
				break;
			}
			
			
			
			//show the messages again
			if(toggle == true) {
			    
			    var reloadOpt = false;
			    if(mytype[1]) {
				    if(mytype[1] === "RELOAD") {
				        reloadOpt = true;
				    }
			    } 
			
				//Do toggle, but pause if there is a message
				if(msg == '') {
					//Switch back immediately
					$("#comment-popup-content").toggle(); 
					$("#comment-options").toggle();
					if(reloadOpt == true) {
			            location.reload();
			        }
					
				} else {
					$("#comment-messages").html(msg);
					$("#comment-messages").show();
					
					
					
					
					//Pause in here for 3 seconds before switching back to message view
					setTimeout(function(){
							
							$("#comment-messages").hide();
							$("#comment-popup-content").toggle(); 
							$("#comment-options").toggle();
							
							if(reloadOpt == true) {
			                   location.reload();
			                }
							
						}, (500*timeMult));
				
				}
			} else {
				//Don't toggle but is there is a message show it
				$("#comment-messages").html(msg);
				$("#comment-messages").show();
	
	            if(mytype[1]) {
			        //carry out a reload of the page too
			        if(mytype[1] === "RELOAD") {
			            location.reload();
			        }
			    }
	
			}
			
			
				
		});
    
    

	return false;

}




// Variable to store your files
var files;


// Grab the files and set them to our variable
function prepareUpload(event)
{
  files = event.target.files;
}


function upload() {

 	//TODO: show uploading progress
    
 	$('#uploading-wait').show();
    // Create a formdata object and add the files
    var data = new FormData();
    $.each(files, function(key, value)
    {
        data.append(key, value);
    });
    
    $.ajax({
			url: ssshoutServer + '/upload-photo.php', 
			data: data,
			dataType: "json",
			type: 'POST',
			cache: false,
			processData: false, // Don't process the files
     	contentType: false // Set content type to false as jQuery will tell the server its a query string request
		}).done(function(response) {
			
			
			$('#uploading-wait').hide();
			
			if(!response.url) {
				$('#uploading-msg').html(response.msg);
				$('#uploading-msg').show();
			
				
			} else {
				//Append the response url to the input box
				//Register that we have started typing
				setTimeout( function() {
        
        
					//Register that we have started typing
					if(currentlyTyping == false) {
						currentlyTyping = true;
						mg.newMsg(true);  //start typing private message
						registerNewKeypress();
						
					} else {
					
			      mg.reactivateMsg(mg.localMsgId); //if it was deactivated
	  				registerNewKeypress();
					
					}
				
				}, 100); //end set timeout
				
				
				$('#uploading-msg').html("");
				$('#uploading-msg').hide();
				$('#shouted').val( $('#shouted').val() + ' ' + response.url + ' ');
				$("#comment-popup-content").show(); 
				$("#comment-upload").hide(); 
			}

			
				
		});
    
    

	return false;

}







function submitShoutAjax(whisper, commit, msgId)
{
	
	if(commit == true) {			//if we're commiting, not typing	
		if(whisper == true) {
			$('#whisper_to').val(mg.localMsg[msgId].whisperOften);
		} else {
			$('#whisper_to').val("");		//clear back
	
		}
		$('#whisper_site').val(whisperSite);		//this is the master version from the website
	}
	
	
	if(mg.localMsg[msgId].shouted) {
		
		if(window.location.href) {
			var str = encodeURIComponent(window.location.href);
			$('#remoteurl').val(str);
		}
	
		if(mg.localMsg[msgId].shortCode) {
		   $('#short-code').val(mg.localMsg[msgId].shortCode);
		   $('#public-to').val(mg.localMsg[msgId].publicTo);
		} else {
		   //Note a bit slow on every request?
		   $('#short-code').val('');
		   $('#public-to').val('');
		}
		
		
		//Clear any removal to disable after a certain length of time
		if(commit == true) {
				//If we clicked a commit button
			
				//Check if we are still waiting on the previous shout_id
				clearTimeout(typingTimer);
		
		}
		
		var data = $('#comment-input-frm').serialize();
		var mycommit = commit;
		var myMsgId = msgId;
		var myShoutId = $('#shout-id');
				
		$.ajax({
			url: ssshoutServer + '/index.php', 
			data: data,
			crossDomain: true,
			dataType: "jsonp"
		}).done(function(response) {
	
			ssshoutHasFocus = true;
			
			if(mycommit == true) {
				//If we clicked a commit button
				
				mg.updateMsg(myMsgId, myShoutId, "complete");
				
				var results = response;
				refreshResults(results);
			
				clearTimeout(myLoopTimeout);		//reset the main timeout
				doLoop();		//Then refresh the main list
			} else {
				//Update screen and get the shout id only
				//Just a push button
				var results = response;
				refreshResults(results);
			
			}
			
			//Go ahead and continue processing all messages outstanding
			mg.processEachMsg();
	
		
					
		
		});
		
	} else {
	
		//TODO: Show warning for blank message sent?
	}
	
	
	return false;

}

function refreshResults(results)
{
	
	if(results.res) {
		if(results.res.length) {
			
			
				var newLine = "";
			
			
				newLine = "<table class=\"table table-striped\" style=\"table-layout: fixed;\">";
			
	 			for(var cnt=0; cnt<results.res.length; cnt++) {
	 				
	 				if(results.res[cnt].whisper == true) {
	 					var priv = "title=\"Private\" class=\"info\"";
	 				} else {
	 				
	 					var priv = "";
	 				}
	 			
	 				if(results.res[cnt].text) {
	 					
	 					var line = '<tr ' + priv + '><td style=\"word-wrap: break-word;\" width="65%">' + family(results.res[cnt].text) + '</td><td style="max-width:36%; padding-right: 0px !important;"><div style=" min-width: 55px; overflow: hidden; white-space:nowrap;">' + results.res[cnt].ago + '</div></td></tr>';
		 				newLine = newLine + line;
		 				
		 				
		 			}
			
				}
				
				if((results.res.length >= showMore)&&(records <= showMore)) {		//we need to show more if there are more	
					var line = '<tr><td style=\"word-wrap: break-word;\" width="65%">&nbsp;</td><td style="max-width:36%; padding-right: 0px !important;"><div style=" min-width: 55px; overflow: hidden; white-space:nowrap;"><a href="javascript:" onclick=\"records=500;\">' +lsmsg.msgs[lang].more + '</a></div></td></tr>';
			 		newLine = newLine + line;
			 	}
			
				newLine = newLine + '</table>';
				$('#comment-prev-messages').html(newLine);
		}
	}
	
	if(results.ses) {
		//Session results
		$('#ses').val(results.ses);
  	
      	//Set the cookie also so that when we come back we will have same user
      	var ses = results.ses;
      	document.cookie = 'ses=' + ses + '; path=/; expires=' + cookieOffset() + ';'; //Thu,31-Dec-2020 00:00:00 GMT
  
	}
	
	if(results.sid) {
		//Session results
	
		mg.updateMsg(results.lid, results.sid, "gotid");
	
	}

}


function doSearch()
{
	//Port is set in search-secure
	
	if(portReset == false) {
		//OK - this is the first one after a logout = we can reset if after this
		portReset = true;	
	}
	
	if(granted == false) {
		return;
	
	}
	
	
	if((readPort)&&(readPort != null)&&(readPort != "")&&(!port)) {
		//Use an alternative port for reading - useful by the Loop-server-fast plugin
		var serv = assignPortToURL(ssshoutServer, readPort);
	} else {
	
		var serv = assignPortToURL(ssshoutServer, port);
	}
	
	
	 $.getJSON(serv + "/search-chat.php?callback=?", {
					lat: $('#lat').val(),
					lon: $('#lon').val(),
					passcode: commentLayer,
					units: 'mi',
					volume: 1.00,
					records: records,
					whisper_site: whisperSite
											
		}, function(response){ 
			 	if(portReset == true) {
			 		port = "";			//reset the port if it had been set	
			 	} else {
			 		//This was still a residual reset middway when we clicked logout
			 		//OK now we can reset the port next time we call - this is particularly after a logout is called
			 		portReset = true;
			 		return;		//Don't refresh the results on this request
			 		
			 			
			 	}	  			
				
				
				
				var results = response;
				refreshResults(results);
				
				
	});
}


cs += 9585328;

function doLoop()
{
	
	if((ssshoutHasFocus == true)&&(granted == true)) {
		//Only do searches when have focus
		doSearch();
	} 
	
	
	
	myLoopTimeout = setTimeout(function() {	doLoop(); }, ssshoutFreq);  //Continue loop no matter what
}

cs += 124856;
				
function family(string)
{
	if(string) {
		string = string.replace(/f+u+c+k+/gi, "****");
		string = string.replace(/s+h+i+t+/gi, "****");
		string = string.replace(/c+o+c+k+/gi, "****");
		string = string.replace(/d+i+c+k+/gi, "****");
		string = string.replace(/p+e+n+u+s+/gi, "*****");
		string = string.replace(/p+e+n+i+s+/gi, "*****");
		string = string.replace(/a+r+s+e+h+o+l+e+/gi, "********");
		string = string.replace(/b+a+r+s+t+a+r+d+/gi, "********");
		string = string.replace(/v+a+g+i+n+a+/gi, "********");
		string = string.replace(/t+i+t+s+/gi, "********");
		string = string.replace(/t+e+s+t+i+c+a+l+s+/gi, "********");
		string = string.replace(/w+i+l+l+i+e+/gi, "********");
		string = string.replace(/b+i+t+c+h+/gi, "*****");
		return string;
	} else {
		return '';
	}

}

cs += 9484320;


function beforeLogout(cb) {
    //This is called before logout.php is called
    
    //Reset the email/pass
    $('#email-opt').val('');
    $('#your-name-opt').val('');
    $('#password-opt').val('');
    $('#phone-opt').val('');
    $('#name-pass').val('');

 
    
    //Clear out the local cookies
    document.cookie = "your_name=deleted; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    document.cookie = "email=deleted; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    document.cookie = "phone=deleted; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    document.cookie = "your_password=deleted; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";

    
    cb();

}

function logout() {
	//This is called after the call to logout.php is complete
	$('#comment-logout-text').hide();	//show the correct text 
	$('#comment-not-signed-in').show();
	$('#ses').val('');  //also sign out the current sess
 
    

    $('#comment-prev-messages').html('');   //remove any existing messages
   

	portReset = false; 
	port=initPort;
	
	//And run a search
	doSearch();
	return;
}


var myEvent = window.attachEvent || window.addEventListener;
var chkevent = window.attachEvent ? 'onbeforeunload' : 'beforeunload'; /// make IE7, IE8 compitable
//iphone is 'pagehide' event, and blackberry is 'onunload'

myEvent(chkevent, function(e) { // For >=IE7, Chrome, Firefox


	//get requests syncronously in deactivate all
	mg.deactivateAll();
	
  return;	
});

myEvent("pagehide", function(e) { // For Iphones/Ipads

	mg.deactivateAll();
	
  return;	
});

myEvent("onunload", function(e) { // For Blackberrys

	mg.deactivateAll();
	
  return;	
});


			

