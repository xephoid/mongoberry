// Standard setup stuff

var express = require("express"),
	http = require("http"),
	querystring = require('querystring'),
	sleep = require("sleep");
var app = module.exports = express();
var listenPort = 80;

// log requests
//app.use(express.logger("dev"));

app.engine(".html", require("ejs").__express);
app.set("views", __dirname);
app.set("view engine", "html");

app.use(express.bodyParser());
app.use(express.cookieParser("MongoberrySecretKeyShhhhhhh!!!"));
app.use(express.session());

app.all('*', function(req, res, next) {
  res.header('Access-Control-Allow-Origin', '*');
  res.header('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS');
  res.header('Access-Control-Allow-Headers', 'Content-Type');
  next();
});

// End standard setup stuff

//Helps remove noise from log when testing
app.get("/favicon.ico", function(req, res, next) {
	res.send("");
});

app.get("/healthcheck", function(req, res, next) {
	res.send("OK");
});

app.get("/", function(req, res, next) {
	  res.render("index");
});

app.get("/index.html", function(req, res, next) {
	  res.render("index");
});

app.get("/maintenance.html", function(req, res, next) {
	  res.render("maintenance");
});

app.get("/maintenance", function(req, res, next) {
	  res.render("maintenance");
});

//Static file routing
app.use("/assets", express.static(__dirname + "/assets"));
app.use("/images", express.static(__dirname + "/images"));

app.use(app.router);

// Start server listening on specified port
if (!module.parent) {
	app.listen(listenPort);
	console.log("Listening on port " + listenPort);
}

