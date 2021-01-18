var Sentiment = require('sentiment');
var async = require('async');
var mysql = require('mysql');
var os = require('os');
var fs = require('fs');
var extend = require('extend');

/*

crontab -e 

*/
//  */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js
//  */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js -production
//  */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js -production -labelregexp ^api1


var verbose = false;


var config = JSON.parse(fs.readFileSync(__dirname + '/../config/config.json'));

var cnf = config.staging;
var db = cnf.db;
var labelRegExp = null;

//Loop through each arg to see if (process.argv[n] == '-production')
for(var cnt = 0; cnt< process.argv.length; cnt++) {
 
 if(verbose == true) console.log("Parsing paremeter " + process.argv[cnt]);
 if(process.argv[cnt] == '-production') {
	console.log("Using production database");
	cnf = config.production;
 }
 
 if(process.argv[cnt] == '-labelregexp') {
	if(process.argv[cnt + 1]) {
		labelRegExp = process.argv[cnt + 1];
		if(verbose == true) console.log("Checking config file for scaleUp option " + labelRegExp);
	} else {
		console.log("Sorry, no labelregexp parameter set. Usage: node sentiment.js -production -labelregexp ^api1");
	}
 }
}
 


function checkDatabase(connection) { 
	connection.query('SELECT int_ssshout_id, var_shouted FROM tbl_ssshout WHERE flt_sentiment IS NULL', function(err, rows, fields) {
  
  
	  if (err) throw err;
  	 
  	  var options = {};

  	 
  	  if(rows.length > 0) {	
		  //Yes there are some sentiments. Load any extra language files at this point.
		  var fr = require(__dirname + '/wordlist/fr-sentiment.json');
		  var es = require(__dirname + '/wordlist/es-sentiment.json');
		  var pt = require(__dirname + '/wordlist/pt-sentiment.json');
		  var hi = require(__dirname + '/wordlist/hi-sentiment.json');
		  var allLanguages = extend(fr,es, pt, hi);
	  
	  	  if(verbose == true) console.log('All languages: ' + JSON.stringify(allLanguages));
		  options = {
			extras: allLanguages
		  };
	  }
  
	  async.forEachLimit(rows, 5, function(row, cb) {
		  var snt = sentiment.analyze(row.var_shouted, options);
		  console.log('Sentiment: ' + snt.score);
	  
	  
		  connection.query('UPDATE tbl_ssshout SET flt_sentiment = ' + snt.score + ' WHERE int_ssshout_id = ' + row.int_ssshout_id, function( err) {
			
				if(err) {
				   console.log('ERR:' + err);
				   cb(err);
				} else {
				   cb(null);
				}
		  });
	  
	  
	  }, function(err) {
	 
		 if(err) {
			console.log('ERR:' + err);
		 } else {
		   console.log('Completed all sentiments!');
		 }        
		 //have completed all queries
		 connection.end();
	 
	  });
	});
}
 


if(labelRegExp) {
	//Try to match the db with one of the scaleup database options
	var foundOption = false;
	for(var cnt = 0; cnt < cnf.db.scaleUp.length; cnt++) {
		if(cnf.db.scaleUp[cnt].labelRegExp == labelRegExp) {
			//Then switch over to this scaleUp database.
			console.log("Using scaleUp database " + labelRegExp);
			db = cnf.db.scaleUp[cnt];
			foundOption = true;
		}
		
	}

	if(foundOption == false) {
		console.log("Sorry, could not find the scaleUp option " + labelRegExp);
	}
}

var allLanguages = [];


var sentiment = new Sentiment();

var connection = mysql.createConnection({
  host     : db.hosts[0],
  user     : db.user,
  password : db.pass,
  database : db.name
});
 
connection.connect();
checkDatabase(connection);





