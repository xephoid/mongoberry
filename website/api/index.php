<?php
class Request {
	private $array = array();

	public function __construct() {
		$this->array = array_merge($_GET, $_POST);
	}

	public function __get($key) {
		if (array_key_exists($key, $this->array)) {
			return $this->array[$key];
		}
		return null;
	}
}

$req = new Request();

if (!is_null($req->a)) {
	$action = strtolower($req->a);
} else {
	echo "{\"error\": \"You must specify an action!\"}";
	exit;
}

if (!is_null($req->db)) {
	$dbName = $req->db;
} else {
	$dbName = "default";
}

if (!is_null($req->co)) {
	$cName = $req->co;
} else {
	$cName = "documents";
}

if (!is_null($req->fi)) {
	$filter = $req->fi;
} else {
	$filter = "{}";
}

if (!is_null($req->up)) {
	$update = $req->up;
} else {
	$update = "{}";
}

$document = $_GET["do"];
$sort = $_GET["so"];
$limit = $_GET["li"];
$fields = $_GET["fs"];
$upsert = $_GET["us"];
$multi = $_GET["mu"];

$mongo = new MongoClient("mongodb://bakeneko");

$coll = $mongo->$dbName->$cName;

function error($msg) {
	echo json_encode(array("error" => $msg), JSON_FORCE_OBJECT);
	exit;
}

function doFind($coll, $filter, $fields) {
	$cursor = $coll->find($filter);
	$result = array();
	foreach ($cursor as $doc) {
		$result[] = $doc;
	}
	return $result;
}

function doSave($coll, $document) {
	$coll->insert($document);
	$id = "$id";
	return $document;
}

function doUpdate($coll, $filter, $update) {
	if (is_null($filter)) {
		error("Could not parse fi!");
	}
	if (is_null($update)) {
		error("Could not parse up!");
	}
	$coll->update($filter, array( '$set' => $update));
	return array("success" => true);
}


$result = "{}";
try {
	if ($action == "find") {
		$result = json_encode(doFind($coll, json_decode($filter, true), $fields));
	} else if ($action == "update") {
		$result = json_encode(doUpdate($coll, json_decode($filter, true), json_decode($update, true)), JSON_FORCE_OBJECT);
	} else if ($action == "insert") {
		$result = json_encode(doSave($coll, json_decode($document)));
	} else if ($action == "remove") {
		$result = json_encode(doRemove($coll, json_decode($filter, true)), JSON_FORCE_OBJECT);
	} else {
		$result = "{error: \"Invalid action\"}";
	}
} catch (Exception $e) {
	$result = "{\"error\": \"" . $e->getMessage() . "\"}";
}

if (!$_GET["pretty"]) {
	echo $result;
} else {
?>
<html>
<head>
	<title>Mongoberry API</title>
	<style>
body {
	font: normal normal 14px Open Sans;
}
pre { 
	background: #f5f2f0; 
	border: 3px solid lightsteelblue;
	margin: 0;
}
.header {
	background: lightsteelblue; 
	font-size: 150%;
	font-weigth: 600;
	padding: 3px 3px 3px 10px;
}
.json-key    { color: brown; }
.json-value  { color: navy; }
.json-string { color: olive; }
	</style>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<script>
// prettyPrint taken from: http://techdem.centerkey.com/2013/05/javascript-colorized-pretty-print-json.html
if (!library)
   var library = {};

library.json = {
   replacer: function(match, pIndent, pKey, pVal, pEnd) {
      var key = '<span class=json-key>';
      var val = '<span class=json-value>';
      var str = '<span class=json-string>';
      var r = pIndent || '';
      if (pKey)
         r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
      if (pVal)
         r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
      return r + (pEnd || '');
      },
   prettyPrint: function(obj) {
      var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
      return JSON.stringify(obj, null, 3)
         .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
         .replace(/</g, '&lt;').replace(/>/g, '&gt;')
         .replace(jsonLine, library.json.replacer);
      }
   };

var result = <?php echo $result; ?>;

$(function() {
	$('#json').html(library.json.prettyPrint(result));
});
	</script>
</head>
<body>
	<div class="header">Response:</div>
	<pre><code id="json"></code></pre>
</body>
</html>
<?php
}
?>
