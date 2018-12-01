var mysql = require('mysql');

var con = mysql.createConnection({
  host: "132.248.205.70",
  port:"3023",
  user: "cercondoc",
  password: "zeR0nd.81-",
  database: "ConDocDB"
});

con.connect(function(err) {
  if (err) throw err;
  //Select only "name" and "address" from "customers":
  con.query("SELECT *, FROM Titulos", function (err, result, fields) {
    if (err) throw err;
    console.log(result);
  });
});
