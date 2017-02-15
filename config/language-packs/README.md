## Language Packs

These different language packs are available. Each directory name includes a number of different languages 
eg. "english-spanish-portuguese", and always consists of three files:

* messages.json
* chat-inner-messages.json
* chat-messages.json

1. The main messages.json should be copied over an existing messages.json file at /config/messages.json

2. chat-inner-messages.json should be copied into the top of /js/chat-inner-x.y.z.js (where x.y.z is the current version number)
   ```
   var lsmsg = [Paste over here (overwrite the existing curly brackets with your own)
	  ...              
   ]
   var lang = lsmsg.defaultLanguage;     
   ```

3. chat-messages.json is for the front-end, and does not sit in the server directory. You will likely have an
	installation of 'AtomJump Loop', and the file to edit will be /bower_components/atomjump/js/chat.js. 
    You should copy this over the lines at the top of chat.js:
   ```
   var lsmsg = [Paste over here (overwrite the existing curly brackets with your own)
	  ...              
   ]
   var lang = lsmsg.defaultLanguage;     
   ```
   On your front-end you should set the cookie named 'lang' to the 2 letter ISO language code
    e.g. 'en' for English, 'es' for 'Spanish', if you wish to change the language around the border of 
    the pop-up chat box.
   
   
You would typically only include the languages you wish to. If you included every language, 
the javascript files will be slightly larger, which could impact on page loading performance, slightly. 
           



