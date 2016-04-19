<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once '/functions.php';

$loader = new Twig_Loader_Filesystem('C:/Apache24/htdocs/templates');
	
	$twig = new Twig_Environment($loader, array());
	
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "main";
	mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db) or die(mysql_error());

$klein = new \Klein\Klein();

$klein->respond('GET', '/', function() use ($twig){
	$query = 'SELECT id, name, alias FROM catnames WHERE parent IS NULL';
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$groups[] = array(
			'name' => $row['name'], 
			'alias' => $row['alias'],
			'children' => getChildren($row['id'])
			);
	}

	echo $twig->render('index.html', array('groups' => $groups));	
});

$klein->respond('GET', '/categories/[*:title]', function($req, $res) use ($twig){
	$query = 'SELECT name, alias FROM catnames WHERE parent IN (SELECT id FROM catnames WHERE alias = "'.$req->title.'")';
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$groups[] = $row;
	}
	echo $twig->render('index.html', array('groups' => $groups, 'alias' => $req->title));
});

$klein->respond('GET', '/admin/', function () use ($twig) {

	$paramName = htmlspecialchars($_POST['name']);
	$paramType = htmlspecialchars($_POST['type']);
	if(!empty($paramName) && !empty($paramType)){
		$query = "INSERT INTO params (name, type) VALUES ('".$paramName."', '".$paramType."')";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = "SELECT * FROM params";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}

	echo $twig->render('admin.html', array('act' => 'params',
		'rows' => $rows));
});

$klein->respond('GET', '/admin/categories/', function() use ($twig){
	$catName = htmlspecialchars($_POST['name']);
	if(!empty($catName)){
		$query = "INSERT INTO catNames (name) VALUES ('".$catName."')";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = "SELECT * FROM catNames";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}

	echo $twig->render('cats.html', array('act' => 'cats',
		'rows' => $rows
		));	
});

$klein->respond('GET', '/admin/classes/', function() use ($twig){
	$catId = htmlspecialchars($_POST['name']);
	$paramId = htmlspecialchars($_POST['param']);
	if(!empty($catId) && !empty($paramId)){
		$query = "INSERT INTO categories (cat, param) VALUES ('".$catId."', ".$paramId.")";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = 	"SELECT cat.id AS id,
				cats.name AS catName,
				param.name AS paramName
				FROM categories AS cat
				LEFT JOIN catNames AS cats
				ON (cat.cat = cats.id)
				LEFT JOIN params AS param
				ON (cat.param = param.id)";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}
	
	$query = "SELECT id, name FROM params";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$params[] = $row;
	}
	
	$query = "SELECT id, name FROM catnames";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$cats[] = $row;
	}

	echo $twig->render('classes.html', array('act' => 'classes',
		'rows' => $rows,
		'params' => $params,
		'cats' => $cats
		));	
});

$klein->respond('GET', '/admin/products/', function() use ($twig){
	$prodName = htmlspecialchars($_POST['prodName']);
	if(!empty($prodName)){
		$query = "INSERT INTO products (name) VALUES ('".$prodName."')";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = "SELECT * FROM products";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}

	echo $twig->render('products.html', array('act' => 'goods',
		'rows' => $rows));	
});

$klein->respond('GET', '/admin/main/', function() use ($twig){
	$prodName = htmlspecialchars($_POST['prodName']);
	$catName = htmlspecialchars($_POST['paramName']);
	$value = htmlspecialchars($_POST['value']);
	if(!empty($prodName) && !empty($catName) && !empty($value)){
		$query = "INSERT INTO productparams (product, cat, value) VALUES ('".$prodName."', '".$catName."', '".$value."')";
		mysql_query($query) or die(mysql_error());
	}
	
	$query = 	"SELECT prodP.id AS id,
				prod.name AS prodName,
				cats.name AS catName,
				param.name AS paramName,
				prodP.value AS value,
				param.type AS type
				FROM productparams AS prodP
				LEFT JOIN products AS prod
				ON (prodP.product = prod.id)
				LEFT JOIN categories AS cat
				ON (prodP.cat = cat.id)
				LEFT JOIN params AS param
				ON (cat.param = param.id)
				LEFT JOIN catnames AS cats
				ON (cat.cat = cats.id)";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rows[] = $row;
	}
	
	$query = "SELECT id, name FROM products";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rowsP[] = $row;
	}

	$query = "SELECT id, name FROM catnames";
	$res = mysql_query($query);
	while($row = mysql_fetch_array($res)){
		$rowsC[] = $row;
	}

	echo $twig->render('productParams.html', array('act' => 'goodsParams',
		'rows' => $rows,
		'rowsP' => $rowsP,
		'rowsC' => $rowsC
		));	
});
$klein->dispatch();
?>