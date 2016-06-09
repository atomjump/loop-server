var sentiment = require('sentiment');
var async = require('async');
var mysql = require('mysql');
var os = require('os');
var fs = require('fs');


var config = JSON.parse(fs.readFileSync(__dirname + '/../config/config.json'));

if((process.argv[2]) && (process.argv[2] == '-production')){
  var cnf = config.production;
} else {
  var cnf = config.staging;
}
 

/*

crontab -e 

*/
//  */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js
//  */1 * * * * /usr/bin/nodejs /yourserverpath/node/sentiment.js -production


var connection = mysql.createConnection({
  host     : cnf.db.hosts[0],
  user     : cnf.db.user,
  password : cnf.db.pass,
  database : cnf.db.name
});
 
connection.connect();


 
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
 
