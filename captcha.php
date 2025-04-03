<?php 
session_start(); 
header("Content-type: image/jpeg"); // ✅ Set Content-Type

// Generate random verification code
$text = rand(10000, 99999); 
$_SESSION["vercode"] = $text; 

// Image dimensions
$height = 25; 
$width = 65;   
$image_p = imagecreate($width, $height); 

// Colors
$black = imagecolorallocate($image_p, 0, 0, 0); 
$white = imagecolorallocate($image_p, 255, 255, 255); 

// Fill background
imagefilledrectangle($image_p, 0, 0, $width, $height, $black);

// Add text
$font_size = 5;  // ✅ Fixed font size
imagestring($image_p, $font_size, 10, 5, $text, $white); 

// Output image
imagejpeg($image_p, null, 80); 
imagedestroy($image_p); // Free memory
?>
