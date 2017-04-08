<?php
	use Pug\Pug;
	use Phroute\Phroute\RouteCollector;
	use Phroute\Phroute\Dispatcher;

	include_once("app/models/dataBase.php");
	include_once("app/controllers/controller.php");

	require 'vendor/autoload.php';

	$router = new RouteCollector();
	$pug = new Pug();
	$db = new dataBase();
	$_controller = new Controller();

	$router->get('/', function(){ 
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

	$router->get('/marking{id}?', function($id = null){ 
		if(isset($_GET["id"])){
			global $db;
			$sql = "SELECT image.Id_Image, image.Route, brand.Id_Brand, brand.Description, structure.Id_Structure, structure.Name FROM image INNER JOIN (brand INNER JOIN structure ON brand.Id_Structure = structure.Id_Structure) ON image.Id_Image = brand.Id_Image WHERE image.Id_Image = '" . $_GET["id"] . "';";
	        $select = $db->select($sql);
	        $rows = [];
	        while($row = $select->fetch_assoc()){
	        	$sql = "SELECT coordinate.Coordinate FROM coordinate WHERE coordinate.Id_Brand = '" . $row["Id_Brand"] . "';";
	        	$coordinate = $db->select($sql);
	        	$coordinates = [];
	        	$index = 0;
	        	while ($coord = $coordinate->fetch_assoc()) {
	        		$coordinates[] = json_decode($coord["Coordinate"]);
	        	}
	        	$rows[] = array("image" => $row["Route"], "Id_Image" => $row["Id_Image"], "_brandId" => $row["Id_Brand"], "_brandDescription" => $row["Description"], "_structureId" => $row["Id_Structure"], "_structureName" => $row["Name"], "polygons" => (($coordinates)));	
	        }
			return loged("marking", ($rows));
		}else{
			return loged("index");
		}
	});


	$router->post('/test', function($route = "Hola"){
		return "ok {$_POST['route']}";
	});

	$router->get('*', function(){ 
		return  "Hola";
	});

	$router->post('/control', function(){ 
		$_route = $_POST["route"];
		global $_controller;
		switch ($_route) {
			case 'login':
				$response = $_controller->_login($_POST["username"], $_POST["password"]);
				return json_encode($response);
				break;

			case 'save':
				$response = $_controller->_save($_POST["imageId"], $_POST["data"]);
				return $response;
				break;

			case 'test':
				return json_encode(array("test"=> "test"));
				break;
		}
	});

	function processInput($uri){        
		$uri = explode('/', $_SERVER['REQUEST_URI']);         
		return $uri[2]; 
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

    } catch (Exception $e) {
    	$response = $e;

    } 

	echo $response;
?>
