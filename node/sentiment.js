var sentiment = require('sentiment');
var async = require('async');
var mysql = require('mysql');
var os = require('os');
var fs = require('fs');

/*

crontab -e 

*/
//  */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js
//  */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js -production
//  */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js -production -labelregexp ^api1



var config = JSON.parse(fs.readFileSync(__dirname + '/../config/config.json'));

var cnf = config.staging;
var db = cnf.db;
var labelRegExp = null;
if(process.argv[2]){
  //Loop through each arg to see if (process.argv[n] == '-production')
  for(var cnt; cnt< process.argv.length; cnt++) {
   
	 if(process.argv[cnt] == '-production') {
	  	cnf = config.production;
	 }
	 
	 if(process.argv[cnt] == '-labelregexp') {
	 	if(process.argv[cnt + 1]) {
	 		labelRegExp = process.argv[cnt + 1];
	 	}
	 }
  }
} 
 


function checkDatabase(connection) { 
	connection.query('SELECT int_ssshout_id, var_shouted FROM tbl_ssshout WHERE flt_sentiment IS NULL', function(err, rows, fields) {
  
  
	  if (err) throw err;
  
  
	  async.forEachLimit(rows, 5, function(row, cb) {
  
	  
		  var snt = sentiment(row.var_shouted);
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
	for(var cnt = 0; cnt<cnf.db.scaleUp.length; cnt++) {
		if(cnf.db.scaleUp[cnt].labelRegExp == labelRegExp) {
			//Then switch over to this scaleUp database.
			console.log("Using scaleUp database " + labelRegExp);
			db = cnf.db.scaleUp[cnt];
		}
		
	}

}

var connection = mysql.createConnection({
  host     : db.hosts[0],
  user     : db.user,
  password : db.pass,
  database : db.name
});
 
connection.connect();
checkDatabase(connection);





