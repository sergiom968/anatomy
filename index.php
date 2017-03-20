<?php
	use Pug\Pug;
	use Phroute\Phroute\RouteCollector;
	use Phroute\Phroute\Dispatcher;

	require 'vendor/autoload.php';

	$router = new RouteCollector();
	$pug = new Pug();

	$router->get('/', function(){ 
		include_once("app/models/dataBase.php");
		$db = new dataBase();
		$sql = "SELECT * FROM imagen WHERE (((imagen.Id_Usuario)='" . $_COOKIE['anatomy_userId'] . "'));";
        $select = $db->select($sql);
        $rows = [];
        while($row = $select->fetch_assoc()){
            $rows[] = $row;
        }
		return loged("index", ($rows));
	});

	$router->get('/add', function(){ 
		return loged("add");
	});

	$router->get('/marking{idtes}?', function($idtes = null){ 
		return loged("marking");
		//return "ok {$_GET['test']}";
	});


	$router->post('/test', function($route = "Hola"){
		return "ok {$_POST['route']}";
	});

	$router->get('*', function(){ 
		return  "Hola";
	});

	function processInput($uri){        
		$uri = explode('/', $_SERVER['REQUEST_URI']);         
		return $uri[2]; 
		/*$uri = implode('/', 
            array_slice(
                explode('/', $_SERVER['REQUEST_URI']), 2));
		return $uri; */
	}

	function render($pugFile, $data){
		global $pug;
		$output = $pug->render("app/views/{$pugFile}.jade", $data);
		return $output;
	}

	function loged($pugFile, $data){
		if (isset($_COOKIE["anatomy_userName"]) && $_COOKIE["anatomy_userName"] != "") {
			return render($pugFile, array(
				'origin' => $pugFile,
				'userName' => $_COOKIE["anatomy_userName"],
				'userType' => $_COOKIE["anatomy_userType"],
				'userId' => $_COOKIE["anatomy_userId"],
				'data' => $data
			));
		}else{
			return render("login");
		}
	}

	$dispatcher =  new Dispatcher($router->getData());

	try {
        $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], processInput($_SERVER['REQUEST_URI']));

    } catch (Phroute\Exception\HttpRouteNotFoundException $e) {

        var_dump($e);      
        die();

    } catch (Phroute\Exception\HttpMethodNotAllowedException $e) {

        var_dump($e);       
        die();

    }

	echo $response;
?>
