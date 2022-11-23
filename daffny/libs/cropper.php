<?php
class ImageCropper{

	public function init() {}

	function resize_and_crop($src_path, $dst_path, $width, $height, $quality=90){
		$info = getimagesize($src_path);
		$src_original_width = $info[0];
		$src_original_height = $info[1];
		$mime = $info['mime'];

		$type = substr(strrchr($mime, '/'), 1);

		switch ($type)
		{
			case 'jpeg':
				$image_create_func = 'ImageCreateFromJPEG';
				break;
			case 'png':
				$image_create_func = 'ImageCreateFromPNG';
				break;
			case 'bmp':
				$image_create_func = 'ImageCreateFromBMP';
				break;
			case 'gif':
				$image_create_func = 'ImageCreateFromGIF';
				break;
			default:
				$image_create_func = 'ImageCreateFromJPEG';
		}

		$width_ratio = $src_original_width/$width;
		$height_ratio = $src_original_height/$height;

		$percent = $width/$height;

		if($height_ratio < $width_ratio ){
			$target_height = $src_original_height;
			$target_width = $src_original_height * $percent;
		}else{
			$target_width = $src_original_width;
			$target_height = $src_original_width / $percent;
		}

		$image_p = imagecreatetruecolor($width, $height);
		$image = $image_create_func($src_path);

		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $target_width , $target_height);
		imagejpeg($image_p, $dst_path, $quality);
		ImageDestroy($image_p);
		ImageDestroy($image);
	}
}