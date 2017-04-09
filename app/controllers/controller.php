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
			$db = new dataBase();
			$_data = json_decode($_data);
			foreach ($_data as $key) {
				$select = $db->select("SELECT * FROM brand WHERE brand.Id_Brand = '" . $key->{'_brandId'} . "';");
				if(mysqli_num_rows($select) == 0){
					$db->save("INSERT INTO brand (Id_Brand, Id_Image, Id_Structure, Description) VALUES ('" . $key->{'_brandId'} . "','" . $_imageId . "','" . $key->{'_structureId'} . "','" . $key->{'_brandDescription'} . "');");
				}else{
					$db->save("UPDATE brand SET Id_Structure = '" . $key->{'_structureId'} . "', Description = '" . $key->{'_brandDescription'} . "' WHERE brand.Id_Brand = '" . $key->{'_brandId'} . "';");
				}
				$db->save("DELETE FROM coordinate WHERE coordinate.Id_Brand = '" . $key->{'_brandId'} . "';");
				foreach ($key->{'polygons'} as $polygons) {
					$polygons = json_encode($polygons);
					$db->save("INSERT INTO coordinate (Id_Brand, Coordinate) VALUES ('" . $key->{'_brandId'} . "', '" . $polygons . "');");
				}
			}
			return json_encode(array("state" => true));
		}

		function _getStructure($_idStructure){
			$db = new dataBase();
			$_cut = substr($_idStructure, 0, 3);
	        $select = $db->select("SELECT structure.Id_Structure AS id, structure.Name AS text FROM structure WHERE structure.Id_Structure LIKE '" . $_cut . "%' AND structure.Id_Structure <> '" . $_idStructure . "' ORDER BY structure.Id_Structure");
	        $rows = [];
	        while($row = $select->fetch_assoc()){
	        	$row["text"] = utf8_encode($row["text"]);
	            $rows[] = $row;
	        }
			return json_encode(array("state" => true, "data" => $rows));
		}

		function _deleteBrand($_brandId){
			$db = new dataBase();
			$select = $db->select("SELECT * FROM brand WHERE brand.Id_Brand = '" . $_brandId . "';");
			if(mysqli_num_rows($select) > 0){
				$db->save("DELETE FROM coordinate WHERE coordinate.Id_Brand = '" . $_brandId . "';");
				$db->save("DELETE FROM brand WHERE brand.Id_Brand = '" . $_brandId . "';");
			}
			return json_encode(array("state" => true));
		}
	}
?>