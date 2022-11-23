<?php
/**************************************************************************************************
 * signature.php
 *
 * Version:		1.0
 * Date:		2012-03-15
 * Author:		C.A.W., Inc. dba INTECHCENTER
 * Address:		11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076
 * E-mail:		techsupport@intechcenter.com
 * CopyRight 2012 Intechcenter. - All Rights Reserved
 ***************************************************************************************************/

class Signature {
	protected $fontFile = "jenna_sue.ttf";

	public function setFontFile($pathToFile) {
		$this->fontFile = $pathToFile;
	}

	public function create($width, $height, $type, $data, $format = 'png') {
		$canvas = imagecreatetruecolor($width, $height);
		$background = imagecolorallocate($canvas, 255, 255, 255);
		imagefill($canvas, 0, 0, $background);
		imagecolortransparent($canvas, $background);
		switch ($type) {
			case 'hand':
				foreach ($data as $line) {
					$this->drawElement($canvas, $line);
				}
				imagefilter($canvas, IMG_FILTER_SMOOTH, 5);
				break;
			case 'text':
				$this->writeText($canvas, $data);
				break;
		}
		$color = imagecolorallocate($canvas, 200, 0, 0);
		imagestring($canvas, 1, 5, $height - 15, $_SESSION['member']['id'].":".time().':'.$_SERVER['REMOTE_ADDR'], $color);
		ob_start();
		if ($format == 'jpg') {
			imagejpeg($canvas, null, 9);
		} else {
			imagepng($canvas, null, 9);
		}
		return ob_get_clean();
	}

	protected function imageBoldLine($resource, $x1, $y1, $x2, $y2, $color, $weight=2) {
		if ((int)$weight == 1) {
			imageline($resource, $x1, $y1, $x2, $y2, $color);
			return;
		}
		$center = round($weight/2);
		for($i=0;$i<$weight;$i++) {
			$a = $center-$i; if($a<0){$a -= $a;}
			for($j=0;$j<$weight;$j++) {
				$b = $center-$j; if($b<0){$b -= $b;}
				$c = sqrt($a*$a + $b*$b);
				if($c<=$weight) {
					imageline($resource, $x1 +$i, $y1+$j, $x2 +$i, $y2+$j, $color);
				}
			}
		}
	}

	protected function getColorRgb($hexColor) {
		$colorVal = hexdec($hexColor);
		$rgbArray = array();
		$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
		$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
		$rgbArray['blue'] = 0xFF & $colorVal;
	}

	protected function drawElement($canvas, $data) {
		$rgbArray = $this->getColorRgb(array_shift($data));
		$color = imagecolorallocate($canvas, $rgbArray['red'], $rgbArray['green'], $rgbArray['blue']);
		$weight = (int)array_shift($data);
		$x = array_shift($data);
		$y = array_shift($data);
		while (count($data) > 1) {
			$x1 = array_shift($data);
			$y1 = array_shift($data);
			$this->imageBoldLine($canvas, $x, $y, $x1, $y1, $color, $weight);
			$x = $x1;
			$y = $y1;
		}
		return $canvas;
	}

	protected function writeText($canvas, $text) {
		$color = imagecolorallocate($canvas, 0, 0, 0);
		imagettftext($canvas, 44, 0, 10, 70, $color, $this->fontFile, $text);
	}
}