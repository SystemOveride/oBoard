<?php
session_start();
$source = "back.jpg";
$my_img = imagecreatefromjpeg($source);
$text_colour_correct = imagecolorallocate( $my_img, 18, 192, 34 );
$text_colour_wrong = imagecolorallocate( $my_img, 196, 20, 3 );
$line_colour = imagecolorallocate( $my_img , 0 , 0 , 0 );
$length_total_string=10;
$length_correct_string=mt_rand(4,8);
$alphas = range('A', 'Z');
$numeric=range(0,9);
$alphanumeric=array_merge($alphas,$numeric);
$string_correct="";
$string_wrong="";
$colors = array();

for($i=0;$i<$length_total_string;$i++)
{
if($i<=$length_correct_string)
$string_correct.=$alphanumeric[mt_rand(0,35)];
else
$string_wrong.=$alphanumeric[mt_rand(0,35)];
}

if(mt_rand(0,1)==1)
{
imagestring( $my_img,5,3,10,$string_correct,$text_colour_correct );
imagestring( $my_img,5,12+9*$length_correct_string,10, $string_wrong,$text_colour_wrong );
imageline( $my_img , 3 , 15 , 100 , 15 , $line_colour );
}
else
{
imagestring( $my_img,5,3, 10,$string_wrong,$text_colour_wrong );
imagestring( $my_img,5,9*($length_total_string-$length_correct_string)-6,10,$string_correct,$text_colour_correct );
imageline( $my_img , 3 , 17 , 100 , 17 , $line_colour );
}
$_SESSION['captcha_code']=md5(strtolower($string_correct));
header( "Content-type: image/png" );
imagepng( $my_img );

imagecolordeallocate( $text_colour_correct);
imagecolordeallocate( $text_colour_wrong );
imagecolordeallocate( $background );
imagecolordeallocate( $line_colour );

imagedestroy( $my_img );

?>

 
