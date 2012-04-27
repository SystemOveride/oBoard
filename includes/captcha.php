<?php
session_start();
$wid = 200;
$hei = 75;
$img = imagecreatetruecolor($wid, $hei);
$text = md5(microtime() + mt_rand(0, 3131));
$text = str_split($text);
$code = "";

for($i=0; $i < 10; $i++){
	$r = mt_rand(0, 31);
	$code .= $text[$r];
}

$pad = $wid / (strlen($code) + 1);

$_SESSION['code'] = $code;
$tcolors = Array();
$ncolors = Array();

$bg = imagecolorallocate($img, 0, 0, 0);

foreach (range(0, 2) as $n){
	$r = mt_rand(0, 255);
	$g = mt_rand(0, 255);
	$b = mt_rand(0, 255);
	$tcolors[$n] = imagecolorallocate($img, $r, $g, $b);
	$ncolors[$n] = imagecolorallocate($img, $b, $r, $g);
}

imagefill($img, 0, 0, $bg);
imagecolordeallocate($img, $bg);

for($i=0;$i<131;$i++){
	$x1 = rand(3, $wid - 3);
	$y1 = rand(3, $hei - 3);
	$x2 = $x1 - 2 - rand(0, 8);
	$y2 = $y1 - 2- rand(0,8);
	imageline($img, $x1, $y1, $x2, $y2, $ncolors[rand(0, count($ncolors) - 1)]);
}

for ($i=0; $i < strlen($code); $i++){
	$color = $tcolors[$i % count($tcolors)];
	imagestring($img, 5, $i*$pad, 30+rand(0,10), $code{$i}, $color);
}

header('Content-Type: image/png');
imagepng($img);

foreach (range(0, 2) as $n){
	imagecolordeallocate($img, $tcolors[$n]);
	imagecolordeallocate($img, $ncolors[$n]);
}
imagedestroy($img);

?>
