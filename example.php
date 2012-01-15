<?php

require_once 'fotosecure.php';

$img = myImageCreate('jubei.jpg');

//keys must be between 0.1 and 0.9
$keys = array(0.45, 0.14, 0.667, 0.21, 0.789, 0.43. 0.5, 0.33, 0.19, 0.48);
$factor = 0.01; //factor should be less than 0.1 for better encryption, 0.01 is ideal

//encrypt jubei
$encrypted_img = encrypt($img, $keys, $factor);

//save encrypted image
//must use png format, jpeg is lossy
myImageSave($encrypted_img, 'encrypted.png');

//load encrypted image and decrypt it
$img = myImageCreate('encrypted.png');
$decrypted_img = decrypt($img, $keys, $factor);
myImageSave($decrypted_img, 'decrypted.png');

print '
	<html><head><title>Fotosecure Example</title></head>
	<body>
		<table width="100%">
			<tr>
				<td align="left"><img src="jubei.jpg" /><br /><b>Original Image</b></td>
				<td align="center"><img src="encrypted.png" /><br />Encrypted Image</td>
				<td align="right"><img src="decrypted.png" /><br />Decrypted Image</td>
			</tr>
		</table>
	</body>
	</html>
';	

?>
