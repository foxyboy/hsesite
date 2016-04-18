<?php
	require_once '/vendor/autoload.php';
	$loader = new Twig_Loader_Filesystem('C:/Apache24/htdocs/templates');
	
	$twig = new Twig_Environment($loader, array());
	$host = "localhost";
	$user = "root";
	$pass = "";
	$db = "main";
	mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($db) or die(mysql_error());
	
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

	echo $twig->render('index.html', array('act' => 'params',
		'rows' => $rows));
?>