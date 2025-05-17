<?php
$m = isset($_GET['m']) ? intval($_GET['m']) : 2;
$text = isset($_GET['k']) ? $_GET['k'] : "Fatiha";
$text = $text." suresi";
$image = ($m == 1 || $m == 9) ? imagecreatefrompng('baslik1.png') : imagecreatefrompng('baslik2.png');


imagealphablending($image, true);
imagesavealpha($image, true);

$color = imagecolorallocate($image, 64, 224, 208);
$font = "font/arial.ttf"; // 'TFF' yerine 'ttf'
$fontSize = 20;

$imageWidth = imagesx($image);
$imageHeight = imagesy($image);

$textBox = imagettfbbox($fontSize, 0, $font, $text);
$textWidth = abs($textBox[2] - $textBox[0]);
$textHeight = abs($textBox[7] - $textBox[1]);

$x = ($imageWidth - $textWidth) / 2;
$y = ($imageHeight - $textHeight) / 2 + $textHeight - 10;

if($m == 1) {
    $y = $y; 
} else {
    $y = $y - 20; 
}

imagettftext($image, $fontSize, 0, $x, $y, $color, $font, $text);

ob_start();
header('Content-Type: image/png');
ob_end_clean(); 

imagepng($image);
imagedestroy($image);
?>
