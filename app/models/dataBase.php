<?php
	include_once('tools.php'); 
	class dataBase
	{
		function Conect()
		{	
			$tools = new Tools();
			$mysqli =  new mysqli("localhost", "root", "315k8528", "anatomy");
			if ($mysqli->connect_errno) {
				echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			}else{
				return $mysqli;
			}
		}

		function select($sql)
		{
			$dataBase = $this->Conect();
			$select = $dataBase->query($sql); 
			return $select;
			$select->close();
		}

		function util($sql){
			$dataBase = $this->Conect();
			$dataBase->query($sql);
		}
	}
?>
