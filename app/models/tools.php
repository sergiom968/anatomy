<?php 
	require 'vendor/autoload.php';
	use Gregwar\Image\Image;
	
	class Tools {

		function uploadImage($image){
			//$uploaddir = "{$_SERVER["HTTP_HOST"]}:{$_SERVER["SERVER_PORT"]}/anatomy/public/img/";
			$uploaddir = "public/img/";
			$code = $this->codeGenerator();
			$upname = $code . "." . pathinfo($image['name'], PATHINFO_EXTENSION);
			$uploadfile = $uploaddir . $upname;
			if (move_uploaded_file($image['tmp_name'], $uploadfile)) {
				return array("state" => "ok", "name" => $upname, "code" => $code);
			} else {
    			return array("state" => "error", "error" => $image['tmp_name']);
			}
		}

		function resizeImage($name){
			$imagen = getimagesize("public/img/" . $name);
			$width = $imagen[0];              
			$height = $imagen[1];  
			$heightNew = 0;
			$widthNew = 0;
			if($width > $height && $height < (($width/4)*3) ){
				$heightNew = round(($width/4)*3);
				$widthNew = $width;
				$originY = round(($heightNew - $height)/2);
			}else{
				$widthNew = round(($height/3)*4);
				$heightNew = $height;
				$originX = round(($widthNew - $width)/2);
			}
			copy("public/img/" . $name, "public/img/copy-" . $name);
			unlink("public/img/" . $name);
			Image::open('public/img/copy-' . $name)
				->scaleResize($widthNew, $heightNew, "black")
				->save('public/img/' . $name);
			unlink("public/img/copy-" . $name);
		}

		function codeGenerator(){
			$key = '';
			$pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
			$max = strlen($pattern)-1;
			for($i=0;$i < 20;$i++){
				$key .= $pattern{mt_rand(0,$max)};
			}
			return $key;
		}
	}
?>