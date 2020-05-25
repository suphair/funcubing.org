<h1>MB2</h1>
<?php
$file_in='Images/in.jpg';
$file_out='Images/out.jpg';
$file_out_s='Images/out_s.jpg';

$BaseColors=[
    [255,255,255],
    [255,255,0],
    [255,165,0],
    [255,0,0],
    [0,128,0],
    [0,0,255],
];

$im_in = imagecreatefromjpeg($file_in);

list($width_in, $height_in) = getimagesize($file_in); 
$Amount=10000;

$k=sqrt($Amount / ($width_in * $height_in));
$width_out=floor($k * $width_in) * 3;    
$height_out=floor($k * $height_in) * 3;  

if($height_out*$width_out/3/3 < $Amount){
    if($height_out<$width_out and ($height_out+3)*$width_out/3/3 <= $Amount){
        $height_out=$height_out+3;
    }
    if($width_out<$height_out and $height_out*($width_out+3)/3/3 <= $Amount){
        $width_out=$width_out+3;
    }
    if($width_out==$height_out and ($height_out+3)*($width_out+3)/3/3 <= $Amount){
        $width_out=$width_out+3;
        $height_out=$height_out+3;
    }
}

$img_out=imagecreatetruecolor($width_out, $height_out);
imagecopyresampled($img_out,$im_in, 0, 0, 0, 0, $width_out, $height_out,$width_in, $height_in); 
imagejpeg($img_out, $file_out);

$img_out_s=imagecreatetruecolor($width_out, $height_out);
$colors_out=[];
for($x=0;$x < $width_out; $x++){
    for($y=0;$y < $height_out; $y++){
        $colors_out[$x][$y]=getColor($img_out,$x,$y);
    }
}

for($x=0;$x < $width_out; $x++){
    for($y=0;$y < $height_out; $y++){
        $color1=$colors_out[$x][$y]; 
        $delta=1000;
        $color2=$BaseColors[0];
        foreach($BaseColors as $c){
            $delta_c=deltaColor($color1,$c);
            if($delta>$delta_c){
                $delta=$delta_c;
                $color2=$c;
            }
        }
        setColor($img_out_s,$x,$y,$color2);
    }
} 

imagejpeg($img_out_s, $file_out_s);

?>
<img width="300px" src="Images/in.jpg">
<img width="300px" src="Images/out.jpg">
<img width="300px" src="Images/out_s.jpg">

<?php 
function getColor($im,$x,$y){
    $color=imagecolorsforindex($im,ImageColorAt($im,$x,$y));
    return [$color['red'],$color['green'],$color['blue']];
}

function setColor($im,$x,$y,$color){
    imagesetpixel($im,$x,$y,imagecolorallocate($im, $color[0], $color[1], $color[2]));
}

function deltaColor($color1,$color2){
    $K=[5,10,1];
    
    return sqrt((pow($color1[0]-$color2[0],2)*$K[0] + pow($color1[1]-$color2[1],2)*$K[1] + pow($color1[2]-$color2[2],2)*$K[2])/
    ($K[0]+$K[1]+$K[2]));   
}