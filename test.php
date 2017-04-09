<?php
	include_once("app/models/dataBase.php");
	$db = new dataBase();
	$_structures = $db->select("SELECT structure.Id_Structure AS id, structure.Name AS text FROM structure WHERE structure.Id_Structure LIKE 'A%.%.00.000' ORDER BY structure.Id_Structure");
	$structure = [];
	while($_structure = $_structures->fetch_assoc()){
		$_structure["text"] = utf8_encode($_structure["text"]);
		$structure[] = $_structure;
	}
	echo json_encode($structure);
?>