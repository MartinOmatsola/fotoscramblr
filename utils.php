<?php

/**
 * Creates an image resource from a url or path to a file of type jpg, gif or png.
 * 
 * @param string $img_url URL or pathname to the image file
 * @return image resource
 */
function myImageCreate($img_url) {
	$img = 0;
	if (eregi(".jpg$", $img_url) > 0) {
		$img = imagecreatefromjpeg($img_url);
	}
	elseif (eregi(".png$", $img_url) > 0) {
		$img = imagecreatefrompng($img_url);
	}
	elseif (eregi(".gif$", $img_url) > 0) {
		$img = imagecreatefromgif($img_url);
	}
	return $img;
}

function computeAvgGrayscale($img_handle) {
	$count = 0;
	$new_w = imagesx($img_handle);
	$new_h = imagesy($img_handle);
	for ($j = 0; $j < $new_w; $j++) {
		for ($k = 0; $k < $new_h; $k++) {
			$rgb = ImageColorsForIndex($img_handle, ImageColorAt($img_handle, $j, $k));
			$count  = $count + (($rgb['red'] + $rgb['green'] + $rgb['blue'])) / 3 ;
		}
	}
	return $count / ($new_w * $new_h);
}

/**
 * Saves an image resource to a file.
 * 
 * @param resource $img The image to be saved.
 * @param string $img_url pathname to the destination file.
 * @return true iff save was successful
 */
function myImageSave($img, $img_url) {
	if (eregi(".jpg$", $img_url) > 0) {
		imagejpeg($img, $img_url);
		return true;
	}
	elseif (eregi(".png$", $img_url) > 0) {
		imagepng($img, $img_url);
		return true;
	}
	elseif (eregi(".gif$", $img_url) > 0) {
		imagegif($img, $img_url);
		return true;
	}
	return false;
}

/**
 * Resizes an image.
 *
 * @param resource $img The image to be resized.
 * @param integer $new_w The desired width.
 * @param integer $new_h The desired height.
 * @return the resized image resource
 */
function resize($img, $new_w, $new_h) {
	if (!is_numeric($new_w) || !is_numeric($new_h)) {
		die("Error: non numeric input to resize function");
	}
	$w = imagesx($img);
	$h = imagesy($img); 
	$resized_img = imagecreatetruecolor($new_w, $new_h);
	imagecopyresampled($resized_img, $img, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
	return $resized_img;
}

?>
