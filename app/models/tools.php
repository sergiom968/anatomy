<?php 

	class Tools {
		
		function __construct(){
			$this->$pug = new Pug();
		}

		function render($pugFile){
			$output = $pug->render('app/views/{$pugFile}.jade');
			return $output;
		}
	}
?>