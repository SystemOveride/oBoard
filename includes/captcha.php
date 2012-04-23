<?php
session_start();
$source 		        = "back.jpg";
$img 			        = imagecreatefromjpeg($source);
$green_text 		= imagecolorallocate( $img, 18, 192, 34 );
$red_text 		    = imagecolorallocate( $img, 196, 20, 3 );
$line_colour 		= imagecolorallocate( $img, 0, 0, 0 );
$len_tot	            = 10;
$len_right 	        = mt_rand(4,8);
$alphas 		        = range('A', 'Z');
$numeric		        = range(0,9);
$alphanumeric	= array_merge($alphas,$numeric);
$string_correct	= "";
$string_wrong		= "";
$colors 		        = array();

for ( $i=0; $i<$len_tot; $i++ ) {
	if ( $i <= $len_right ) {
		$string_correct .= $alphanumeric[mt_rand(0,35)];
	} else {
		$string_wrong .= $alphanumeric[mt_rand(0,35)];
	}
}

if ( mt_rand ( 0, 1 ) == 1 ) {
    imagestring ( $img, 5, 3, 10, $string_correct, $green_text );
    imagestring ( $img, 5, 12 + 9 * $len_right, 10, $string_wrong, $red_text );
    imageline ( $img , 3, 17, 100, 17, $line_colour );
} else {
    imagestring ( $img, 5, 3, 10, $string_wrong, $red_text );
    imagestring ( $img, 5, 9 * ( $len_tot - $len_right ) - 6, 10, $string_correct, $green_text );
    imageline ( $img , 3, 17, 100, 17, $line_colour );
}

$_SESSION['captcha_code'] = md5 ( strtolower ( $string_correct ) );
header ( "Content-type: image/png" );
imagepng ( $img );

imagecolordeallocate( $green_text );
imagecolordeallocate( $red_text );
imagecolordeallocate( $background );
imagecolordeallocate( $line_colour );
imagedestroy( $img );

?>
