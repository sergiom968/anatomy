<?php
	use Pug\Pug;
	use Phroute\Phroute\RouteCollector;
	use Phroute\Phroute\Dispatcher;

	require 'vendor/autoload.php';

	$router = new RouteCollector();
	$pug = new Pug();

	$router->get('/', function(){ 
		return loged("index");
	});

	$router->get('/add', function(){ 
		return loged("add");
	});

	$router->get('*', function(){ 
		return  "Hola";
	});

	function processInput($uri){        
		$uri = explode('/', $_SERVER['REQUEST_URI']);         
		return $uri[2];    
	}

	function render($pugFile){
		global $pug;
		$output = $pug->render("app/views/{$pugFile}.jade", array(
			'userName' => "undefined"
		));
		return $output;
	}

	function loged($pugFile){
		if (isset($_COOKIE["anatomy.userName"]) && $_COOKIE["anatomy.userName"] != "" && $_COOKIE["anatomy.userName"] != null) {
			return render($pugFile);
		}else{
			return render("errorLog");
		}
	}

	$dispatcher =  new Dispatcher($router->getData());
	$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], processInput($_SERVER['REQUEST_URI']));
	echo $response;
?>
