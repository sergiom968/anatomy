<?php 

	use Gregwar\Image\Image;
	
	class Tools {
		
		function __construct(){
			
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
	}
?>