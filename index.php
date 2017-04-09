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
		global $db;
		$sql = "SELECT * FROM image;";
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
	        $imageId = "";
	        $route = "";
	        while($row = $select->fetch_assoc()){
	        	$route = $row["Route"];
	        	$imageId = $row["Id_Image"];
	        	$sql = "SELECT coordinate.Coordinate FROM coordinate WHERE coordinate.Id_Brand = '" . $row["Id_Brand"] . "';";
	        	$coordinate = $db->select($sql);
	        	$coordinates = [];
	        	$index = 0;
	        	while ($coord = $coordinate->fetch_assoc()) {
	        		$coordinates[] = json_decode($coord["Coordinate"]);
	        	}
	        	$rows[] = array("_brandId" => $row["Id_Brand"], "_brandDescription" => $row["Description"], "_structureId" => $row["Id_Structure"], "_structureName" => $row["Name"], "polygons" => (($coordinates)));	
	        }
	        $_structures = $db->select("SELECT structure.Id_Structure AS id, structure.Name AS text FROM structure WHERE structure.Id_Structure LIKE 'A%.%.00.000' ORDER BY structure.Id_Structure");
	        $structure = [];
	        while($_structure = $_structures->fetch_assoc()){
	        	$_structure["text"] = utf8_encode($_structure["text"]);
	        	$structure[] = $_structure;
	        }
			return loged("marking", array("structures" => $structure, "route" => $route, "Id_Image" => $imageId, "data" =>$rows));
		}else{
			return loged("index");
		}
	});

	$router->get('/exit', function(){ 
		setcookie("anatomy_idUser", "", time() - 3600, "/");
		setcookie("anatomy_userName", "", time() - 3600, "/");
		setcookie("anatomy_userType", "", time() - 3600, "/");
		header('Location: /anatomy');
		return  loged("index");
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

			case 'getStructure':
				$response = $_controller->_getStructure($_POST["Id_Structure"]);
				return $response;
				break;

			case 'deleteBrand':
				$response = $_controller->_deleteBrand($_POST["_brandId"]);
				return $response;
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
