<?php
	include_once("app/models/tools.php");
	$tools = new Tools();
	//$tools->resizeImage("img201704180324.jpg");
	$code = $tools->codeGenerator();
	echo $code;
?>

