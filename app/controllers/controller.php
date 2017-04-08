<?php
	include_once("app/models/dataBase.php");

	class Controller{

		function _login($_username, $_password){
			$db = new dataBase();
			$sql = ("SELECT * FROM `usuario` WHERE `Usuario` = '" . $_username . "' AND `Contrasena` = '" . $_password . "' LIMIT 1;");
			$select = $db->select($sql);
			$json = array();
			$numero_filas = mysqli_num_rows($select);
			if($numero_filas == 0){
				$json = array("state" => false);
			}else{
				while($row = $select->fetch_assoc()){
					$nombre = utf8_decode($row["Nombre"]);
					$json = array("state" => true);
					setcookie("anatomy_userId", $row["Id_Usuario"], 0, "/");
					setcookie("anatomy_userType", $row["tipoUsuario"], 0, "/");
					setcookie("anatomy_userName", $row["Nombre"], 0, "/");
				}
			}
			return $json;
		}

		function _save($_imageId, $_data){
			
		}
	}
?>