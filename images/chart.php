<?php
header('Content-Type: image/svg+xml');

$redline = [[20,200],[120,140],[220,120],[320,80],[420,120],[520,70],[620,150],[700,100]];
$greenline = [[20,200],[120,150],[220,120],[320,60],[420,170],[520,140],[620,100],[700,60]];
$bluepath = "M20 200 L120 160 L220 140 L320 90 L420 150 L520 110 L620 140 L700 50";

?>
<?xml version="1.0" encoding="utf-8"?>
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="740" height="220">
<rect x="0" y="0" width="100%" height="100%" style="fill: white; stroke:rgb(192,192,192); stroke-width: 1;"/>
<!-- viewport 700 x 200 -->
<line x1="20" y1="10" x2="20" y2="200" style="stroke:rgb(192,192,192);stroke-width:1" />
<text x="20" y="10" dx="-16" dy="16">Y</text>
<line x1="20" y1="200" x2="720" y2="200" style="stroke:rgb(192,192,192);stroke-width:1" />
<text x="720" y="210" dx="-16" dy="6">X</text>
<?php 
	$red = $green = "";
	foreach ($redline as $point) {
		$red .= "$point[0],$point[1] ";
	}
	foreach ($greenline as $point) {
		$green .= "$point[0],$point[1] ";
	}

?>

<polyline points="<?php echo trim($red);?>" style="fill:none;stroke:red;stroke-width:3" />
<polyline points="<?php echo trim($green);?>" style="fill:none;stroke:green;stroke-width:3" />
<path d="<?php echo trim($bluepath);?>" style="fill:none;stroke:blue;stroke-width:3;stroke-dasharray:10,5;"  />

<?php 
	$x1 = 20; $y1 = 200;
	$yellow = ""; 
	for ($i=0;$i<8; $i++) {
		$yellow .= "$x1,$y1 ";
		$x1 += 100;
		$y1 = rand(5,20)*10;
	}
?>

<polyline points="<?php echo trim($yellow);?>" style="fill:none;stroke:yellow;stroke-width:3" />
<text x="260" y="24" style="font-weight: bold;" >Пятая стадия понимания (желтый)</text>
</svg>
