<?php

/**
 * @author Martin Okorodudu <webmaster@fotocrib.com>
 *  This script contains functions for scrambling image files
 */

require_once 'utils.php';

/*
 * break up $img into cells of width $cell_w and height $cell_h
 */
function decompose($img, $cell_w, $cell_h) {
	if (!is_numeric($cell_w) || !is_numeric($cell_h)) {
		die("Error: non numeric input to decompose function");
	}
	$w = imagesx($img);
	$h = imagesy($img);

	if ($cell_w > $w || $cell_h > $h) {
		die("Error: cell image dimensions may not exceed image dimensions");
	}
	
	$new_img_w = $w;
	$new_img_h = $h;
	
	//resize image to fit cells exactly
	if ($w % $cell_w != 0) {
		$new_img_w = $cell_w - ($w % $cell_w ) + $w;
	}
	if ($h % $cell_h != 0) {
		$new_img_h = $cell_h - ($h % $cell_h ) + $h;
	}

	$img = resize($img, $new_img_w, $new_img_h);

	//grab new dimensions
	$w = imagesx($img);
	$h = imagesy($img);

	$cell_img_array = array();
	for ($j = 0; $j < $h; $j = $j + $cell_h) {

			//stores row cells
			$row_array = array();

		for ($i = 0; $i < $w; $i = $i + $cell_w) {

			//copy out cell
			$cell_img = imagecreatetruecolor($cell_w, $cell_h);
			imagecopy($cell_img, $img, 0, 0, $i, $j, $cell_w, $cell_h);

			$row_array[] = $cell_img;

		}
		//add row array to result
		$cell_img_array[] = $row_array;
	}
	return $cell_img_array;
}

/*
 * reconstructs a full image from $cell_img_array
 * $cell_img_array is a 2D array of image resources
 */
function reconstruct($cell_img_array) {
	
	if (!empty($cell_img_array)) {
		//get cell image dimensions
		$cell_img = $cell_img_array[0][0];
		
		$cell_w = 0;
		$cell_h = 0;
		
		$cell_w = imagesx($cell_img);
		$cell_h = imagesy($cell_img);
		
		//get reconstructed image dimensions
		$row_len = count($cell_img_array);
		$col_len = count($cell_img_array[0]);
		
		$w = $col_len * $cell_w;
		$h = $row_len * $cell_h;

		if ($cell_w > $w || $cell_h > $h) {
			die("Error: cell image dimensions may not exceed image dimensions");
		}

		$img = imagecreatetruecolor($w, $h);
		
		for ($j = 0; $j < $row_len; $j++) {
			for ($i = 0; $i < $col_len; $i++) {
				$cell_img = $cell_img_array[$j][$i];
				imagecopy($img, $cell_img, $i * $cell_w, $j * $cell_h, 0, 0, $cell_w, $cell_h);
			}
		}
		return $img;
	}
	return 0;
}

/*
 * Scrambles the contents of $img using 10 keys stored in array $keys
 * $keys contains 10 real numbers between 0.1 and 0.9
 * $factor determines how small the cell images will be
 */
function encrypt($img, $keys, $factor) {
	$w = imagesx($img);
	$h = imagesy($img);

	$cell_w = ceil($factor * $w);
	$cell_h = ceil($factor * $h);

	//get inorder 2D array of $img
	$cell_img_array = decompose($img, $cell_w, $cell_h);

	//2D reverse
	for ($i = 0; $i < count($cell_img_array); $i++) {
		$cell_img_array[$i] = array_reverse($cell_img_array[$i]);
	}
	$cell_img_array = array_reverse($cell_img_array);
	
	//reverse odd rows
	for ($i = 0; $i < count($cell_img_array); $i++) {
		if ($i % 2 != 0) {
			$cell_img_array[$i] = array_reverse($cell_img_array[$i]);
		}
	}
	
	//even index delimiters must be even and odd index delimiters must be odd
	$delim1 = round($keys[0] * count($cell_img_array[0]));
	if ($delim1 % 2 == 0) {
		$delim1++;
	}

	$delim2 = round($keys[1] * count($cell_img_array[0]));
	if ($delim2 % 2 != 0) {
		$delim2++;
	}
	
	$delim3 = round($keys[2] * count($cell_img_array[0]));
	if ($delim3 % 2 == 0) {
		$delim3++;
	}

	$delim4 = round($keys[3] * count($cell_img_array[0]));
	if ($delim4 % 2 != 0) {
		$delim4++;
	}

	$delim5 = round($keys[4] * count($cell_img_array[0]));
	if ($delim5 % 2 == 0) {
		$delim5++;
	}

	$delim6 = round($keys[5] * count($cell_img_array[0]));
	if ($delim6 % 2 != 0) {
		$delim6++;
	}

	$delim7 = round($keys[6] * count($cell_img_array[0]));
	if ($delim7 % 2 == 0) {
		$delim7++;
	}

	$delim8 = round($keys[7] * count($cell_img_array[0]));
	if ($delim8 % 2 != 0) {
		$delim8++;
	}
		
	$delim9 = round($keys[8] * count($cell_img_array[0]));
	if ($delim9 % 2 == 0) {
		$delim9++;
	}

	$delim10 = round($keys[9] * count($cell_img_array[0]));
	if ($delim10 % 2 != 0) {
		$delim10++;
	}

	//swap columns using delims above
	for ($j = 0; $j < count($cell_img_array[0]) - $delim1; $j++) {
		if ($j % 2 != 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim1];
				$cell_img_array[$i][$j + $delim1] = $tmp;
			}	
		}
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim2; $j++) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim2];
				$cell_img_array[$i][$j + $delim2] = $tmp;
			}	
		}
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim3; $j++) {
		if ($j % 2 != 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim3];
				$cell_img_array[$i][$j + $delim3] = $tmp;
			}	
		}
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim4; $j++) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim4];
				$cell_img_array[$i][$j + $delim4] = $tmp;
			}	
		}
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim5; $j++) {
		if ($j % 2 != 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim5];
				$cell_img_array[$i][$j + $delim5] = $tmp;
			}
		}	
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim6; $j++) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim6];
				$cell_img_array[$i][$j + $delim6] = $tmp;
			}	
		}
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim7; $j++) {
		if ($j % 2 != 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim7];
				$cell_img_array[$i][$j + $delim7] = $tmp;
			}	
		}
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim8; $j++) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim8];
				$cell_img_array[$i][$j + $delim8] = $tmp;
			}	
		}
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim9; $j++) {
		if ($j % 2 != 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim9];
				$cell_img_array[$i][$j + $delim9] = $tmp;
			}	
		}
	}
	for ($j = 0; $j < count($cell_img_array[0]) - $delim10; $j++) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j + $delim10];
				$cell_img_array[$i][$j + $delim10] = $tmp;
			}	
		}
	}
	
	//even index delimiters must be even and odd index delimiters must be odd
	$delim1 = round($keys[0] * count($cell_img_array));
	if ($delim1 % 2 == 0) {
		$delim1++;
	}
	
	$delim2 = round($keys[1] * count($cell_img_array));
	if ($delim2 % 2 != 0) {
		$delim2++;
	}

	$delim3 = round($keys[2] * count($cell_img_array));
	if ($delim3 % 2 == 0) {
		$delim3++;
	}

	$delim4 = round($keys[3] * count($cell_img_array));
	if ($delim4 % 2 != 0) {
		$delim4++;
	}

	$delim5 = round($keys[4] * count($cell_img_array));
	if ($delim5 % 2 == 0) {
		$delim5++;
	}

	$delim6 = round($keys[5] * count($cell_img_array));
	if ($delim6 % 2 != 0) {
		$delim6++;
	}

	$delim7 = round($keys[6] * count($cell_img_array));
	if ($delim7 % 2 == 0) {
		$delim7++;
	}
	
	$delim8 = round($keys[7] * count($cell_img_array));
	if ($delim8 % 2 != 0) {
		$delim8++;
	}

	$delim9 = round($keys[8] * count($cell_img_array));
	if ($delim9 % 2 == 0) {
		$delim9++;
	}

	$delim10 = round($keys[9] * count($cell_img_array));
	if ($delim10 % 2 != 0) {
		$delim10++;
	}

	//swap rows using delims above
	
	for ($i = 0; $i < count($cell_img_array) - $delim1; $i++) {
		if ($i % 2 != 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim1];
			$cell_img_array[$i + $delim1] = $tmp;
		}	
	}
	for ($i = 0; $i < count($cell_img_array) - $delim2; $i++) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim2];
			$cell_img_array[$i + $delim2] = $tmp;	
		}
	}
	for ($i = 0; $i < count($cell_img_array) - $delim3; $i++) {
		if ($i % 2 != 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim3];
			$cell_img_array[$i + $delim3] = $tmp;
		}	
	}
	for ($i = 0; $i < count($cell_img_array) - $delim4; $i++) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim4];
			$cell_img_array[$i + $delim4] = $tmp;
		}	
	}
	for ($i = 0; $i < count($cell_img_array) - $delim5; $i++) {
		if ($i % 2 != 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim5];
			$cell_img_array[$i + $delim5] = $tmp;
		}	
	}
	for ($i = 0; $i < count($cell_img_array) - $delim6; $i++) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim6];
			$cell_img_array[$i + $delim6] = $tmp;	
		}
	}
	for ($i = 0; $i < count($cell_img_array) - $delim7; $i++) {
		if ($i % 2 != 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim7];
			$cell_img_array[$i + $delim7] = $tmp;
		}	
	}
	for ($i = 0; $i < count($cell_img_array) - $delim8; $i++) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim8];
			$cell_img_array[$i + $delim8] = $tmp;
		}	
	}
	for ($i = 0; $i < count($cell_img_array) - $delim9; $i++) {
		if ($i % 2 != 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim9];
			$cell_img_array[$i + $delim9] = $tmp;
		}	
	}
	for ($i = 0; $i < count($cell_img_array) - $delim10; $i++) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i + $delim10];
			$cell_img_array[$i + $delim10] = $tmp;
		}	
	}
	$img = reconstruct($cell_img_array);
	return $img;
}

/*
 * reverses the operations performed by encrypt() above
 * the keys used must be the same as the ones used to encrypt the image
 */
function decrypt($img, $keys, $factor) {
	
	$cell_img_array = decompose($img, ceil($factor * imagesx($img)), ceil($factor * imagesy($img)));	
		
	$delim1 = round($keys[0] * count($cell_img_array));
	if ($delim1 % 2 == 0) {
		$delim1++;
	}
	
	$delim2 = round($keys[1] * count($cell_img_array));
	if ($delim2 % 2 != 0) {
		$delim2++;
	}

	$delim3 = round($keys[2] * count($cell_img_array));
	if ($delim3 % 2 == 0) {
		$delim3++;
	}

	$delim4 = round($keys[3] * count($cell_img_array));
	if ($delim4 % 2 != 0) {
		$delim4++;
	}

	$delim5 = round($keys[4] * count($cell_img_array));
	if ($delim5 % 2 == 0) {
		$delim5++;
	}

	$delim6 = round($keys[5] * count($cell_img_array));
	if ($delim6 % 2 != 0) {
		$delim6++;
	}

	$delim7 = round($keys[6] * count($cell_img_array));
	if ($delim7 % 2 == 0) {
		$delim7++;
	}
	
	$delim8 = round($keys[7] * count($cell_img_array));
	if ($delim8 % 2 != 0) {
		$delim8++;
	}

	$delim9 = round($keys[8] * count($cell_img_array));
	if ($delim9 % 2 == 0) {
		$delim9++;
	}

	$delim10 = round($keys[9] * count($cell_img_array));
	if ($delim10 % 2 != 0) {
		$delim10++;
	}
	//swap rows using delims above
	
	
	for ($i = count($cell_img_array) - 1; $i >= $delim10; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim10];
			$cell_img_array[$i - $delim10] = $tmp;
		}	
	}
	for ($i = count($cell_img_array) - 1; $i >= $delim9; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim9];
			$cell_img_array[$i - $delim9] = $tmp;	
		}
	}
	for ($i = count($cell_img_array) - 1; $i >= $delim8; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim8];
			$cell_img_array[$i - $delim8] = $tmp;
		}	
	}
	for ($i = count($cell_img_array) - 1; $i >= $delim7; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim7];
			$cell_img_array[$i - $delim7] = $tmp;
		}	
	}
	for ($i = count($cell_img_array) - 1; $i >= $delim6; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim6];
			$cell_img_array[$i - $delim6] = $tmp;
		}	
	}
	for ($i = count($cell_img_array) - 1; $i >= $delim5; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim5];
			$cell_img_array[$i - $delim5] = $tmp;	
		}
	}
	for ($i = count($cell_img_array) - 1; $i >= $delim4; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim4];
			$cell_img_array[$i - $delim4] = $tmp;
		}	
	}
	for ($i = count($cell_img_array) - 1; $i >= $delim3; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim3];
			$cell_img_array[$i - $delim3] = $tmp;
		}	
	}
	for ($i = count($cell_img_array) - 1; $i >= $delim2; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim2];
			$cell_img_array[$i - $delim2] = $tmp;
		}	
	}
	
	for ($i = count($cell_img_array) - 1; $i >= $delim1; $i--) {
		if ($i % 2 == 0) {
			$tmp = $cell_img_array[$i];
			$cell_img_array[$i] = $cell_img_array[$i - $delim1];
			$cell_img_array[$i - $delim1] = $tmp;
		}	
	}
	
	//even index delimiters must be even and odd index delimiters must be odd
	$delim1 = round($keys[0] * count($cell_img_array[0]));
	if ($delim1 % 2 == 0) {
		$delim1++;
	}

	$delim2 = round($keys[1] * count($cell_img_array[0]));
	if ($delim2 % 2 != 0) {
		$delim2++;
	}
	
	$delim3 = round($keys[2] * count($cell_img_array[0]));
	if ($delim3 % 2 == 0) {
		$delim3++;
	}

	$delim4 = round($keys[3] * count($cell_img_array[0]));
	if ($delim4 % 2 != 0) {
		$delim4++;
	}

	$delim5 = round($keys[4] * count($cell_img_array[0]));
	if ($delim5 % 2 == 0) {
		$delim5++;
	}

	$delim6 = round($keys[5] * count($cell_img_array[0]));
	if ($delim6 % 2 != 0) {
		$delim6++;
	}

	$delim7 = round($keys[6] * count($cell_img_array[0]));
	if ($delim7 % 2 == 0) {
		$delim7++;
	}

	$delim8 = round($keys[7] * count($cell_img_array[0]));
	if ($delim8 % 2 != 0) {
		$delim8++;
	}
		
	$delim9 = round($keys[8] * count($cell_img_array[0]));
	if ($delim9 % 2 == 0) {
		$delim9++;
	}

	$delim10 = round($keys[9] * count($cell_img_array[0]));
	if ($delim10 % 2 != 0) {
		$delim10++;
	}

	//swap columns using delims above
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim10; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim10];
				$cell_img_array[$i][$j - $delim10] = $tmp;
			}	
		}
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim9; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim9];
				$cell_img_array[$i][$j - $delim9] = $tmp;
			}	
		}
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim8; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim8];
				$cell_img_array[$i][$j - $delim8] = $tmp;
			}	
		}
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim7; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim7];
				$cell_img_array[$i][$j - $delim7] = $tmp;
			}	
		}
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim6; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim6];
				$cell_img_array[$i][$j - $delim6] = $tmp;
			}
		}	
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim5; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim5];
				$cell_img_array[$i][$j - $delim5] = $tmp;
			}	
		}
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim4; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim4];
				$cell_img_array[$i][$j - $delim4] = $tmp;
			}	
		}
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim3; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim3];
				$cell_img_array[$i][$j - $delim3] = $tmp;
			}	
		}
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim2; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim2];
				$cell_img_array[$i][$j - $delim2] = $tmp;
			}	
		}
	}
	for ($j = count($cell_img_array[0]) - 1; $j >= $delim1; $j--) {
		if ($j % 2 == 0) {
			for ($i = 0; $i < count($cell_img_array); $i++) {
				$tmp = $cell_img_array[$i][$j];
				$cell_img_array[$i][$j] = $cell_img_array[$i][$j - $delim1];
				$cell_img_array[$i][$j - $delim1] = $tmp;
			}	
		}
	}

	//reverse odd rows
	for ($i = 0; $i < count($cell_img_array); $i++) {
		if ($i % 2 != 0) {
			$cell_img_array[$i] = array_reverse($cell_img_array[$i]);
		}
	}
	//2D reverse
	for ($i = 0; $i < count($cell_img_array); $i++) {
		$cell_img_array[$i] = array_reverse($cell_img_array[$i]);
	}
	$cell_img_array = array_reverse($cell_img_array);
	
	$img = reconstruct($cell_img_array);
	return $img;
}

?>
