<?php
define('RD', dirname (__FILE__));

function create_image_thumb($thumb,$size){
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s", 10000) . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Content-Disposition: inline; filename=$thumb");
	header("Pragma: no-cache");
	$er=1;$img_src=$srcimg="";$new_height=$new_width=0;
	if (file_exists("uploads/$thumb")){$img_src="uploads/$thumb"; $er=0;}
	if ($er==1){$img_src="uploads/nofoto.jpg";$er=0;}
	
	if ($er==0){
			$sizes = getimagesize($img_src); $aspect_ratio = $sizes[0]/$sizes[1];  $type=$sizes[2];header("Content-Type:image/$type"); 
			
			if ($sizes[0]>=$sizes[1]){
				if ($sizes[0] <= $size){ $new_width = $sizes[0]; $new_height = $sizes[1]; }
				else{ $new_width = $size; $new_height = abs($new_width/$aspect_ratio); }
			}
			if ($sizes[0]<$sizes[1]){ 
				if ($sizes[1] <= $size){ $new_width = $sizes[0]; $new_height = $sizes[1]; }
				else{ $new_height = $size; $new_width = abs($new_height*$aspect_ratio); }
			}
			if ($new_height>$size){ $new_height = $size; $new_width = abs($new_height*$aspect_ratio); }
			
			$destimg=imagecreatetruecolor($new_width,$new_height);
			if ($type==1){ $srcimg=ImageCreateFromGIF($img_src); }
			if ($type==2){ $srcimg=ImageCreateFromJPEG($img_src); }
			if ($type==3){ $srcimg=ImageCreateFromPNG($img_src); }
			if ($type==4){ $srcimg=ImageCreateFromWBMP($img_src); }
			imagecopyresampled($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg));
			$black = imagecolorallocate($destimg, 0, 0, 0);
			if ($type==1){ imagecolortransparent($destimg, $black); ImageGIF($destimg,"",90);  }
			if ($type==2){ ImageJPEG($destimg,"",90); }
			if ($type==3){ imagecolortransparent($destimg, $black);ImagePNG($destimg,"",0,90); }
			if ($type==4){ ImageWBMP($destimg,"","image.bmp",90); }
			ImageDestroy ($destimg);
	}
}

if ($_GET["image"]!=""){ create_image_thumb($_GET["image"],$_GET["size"]);}
