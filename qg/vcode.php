<?php
error_reporting(0);
if(!$_SERVER["REMOTE_ADDR"] or $_SERVER["SERVER_ADDR"]==$_SERVER["REMOTE_ADDR"]) {
    exit;
}


function agent_has_gzip(){
        $hae=$_SERVER["HTTP_ACCEPT_ENCODING"];
        if(stripos($hae,"gzip")!==false) {
                return true;
        }
        if(stripos($hae,"deflate")!==false) {
                return true;
        }
        return false;
}
$max_face=53;

function get_random($without){
        global $max_face;
        $num=$without;
        while($num==$without){
                $num=mt_rand(0,$max_face);
        }
        return $num;
}

session_start();
$code_length=6;
$img_width=540;
$img_height=100;

$nmsg="";
for($Tmpa=0;$Tmpa<$code_length;$Tmpa++){
	$nmsg.=mt_rand(0,9);
}




//if(agent_has_gzip()){

if(false){


$unmsg = mb_convert_encoding($nmsg, "UTF-8","GBK");

$aimg = imagecreatetruecolor($img_width,$img_height);
$back = imagecolorallocate($aimg, 255, 255, 255);
imagefilledrectangle($aimg, 0, 0, $img_width - 1, $img_height - 1, $back);




$font="v.ttf";
for ($i=0;$i<mb_strlen($unmsg);$i++){
        $n=mb_substr($unmsg,$i,1,"UTF-8");
	imagettftext($aimg,70,0,$i*80+40,80,imageColorAllocate($aimg,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200)),$font,$n);
}
$font="t.ttf";

$alphanum = '123456';

while(true){
$pos = substr( str_shuffle( $alphanum ), 2, 4 );
$d=0;
for($i=0;$i<strlen($pos);$i++){
        $d=$d+$pos[$i];
}
if($d!=10 && $d!=14 && $d!=18) break;
}


for($j=0;$j<100;$j++){
	$xPos = mt_rand(0,$img_width);
	$yPos = mt_rand(0,$img_height);
	imagefilledrectangle($aimg,$xPos,$yPos,$xPos+mt_rand(3,6),$yPos+mt_rand(3,6),imageColorAllocate($aimg,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200)));
}


$xmsg="昆";
$xmsg = mb_convert_encoding($xmsg, "UTF-8","GBK");
$g=0;
for ($i=0;$i<mb_strlen($unmsg);$i++){
        $j=$i+1;
        if(substr_count($pos,$j)){
                $now_msg=$xmsg;
                $fcode.=$nmsg[$i];
        }else{
                $now_msg=mb_convert_encoding(chr(mt_rand(180,200)).chr(mt_rand(180,200)), "UTF-8","GBK");
        }
	imagettftext($aimg,mt_rand(20,26),mt_rand(-40,40),$i*80+40+mt_rand(10,40),90-mt_rand(10,60),imageColorAllocate($aimg,mt_rand(0,100),mt_rand(0,100),mt_rand(0,200)),$font,$now_msg);
}
$_SESSION[vcode] = $fcode;
$s="按顺序输入有[昆]字的4位数字，灭哈哈~";
$s = mb_convert_encoding($s, "UTF-8","GBK");
imagettftext($aimg,18,0,mt_rand(11,55),mt_rand(108,112),imageColorAllocate($aimg,mt_rand(100,150),mt_rand(100,150),mt_rand(100,150)),$font,$s);

$s="Please enter the 4 numbers with \"昆\"";
$s = mb_convert_encoding($s, "UTF-8","GBK");
imagettftext($aimg,14,0,mt_rand(11,55),mt_rand(130,138),imageColorAllocate($aimg,mt_rand(100,150),mt_rand(100,150),mt_rand(100,150)),$font,$s);

/* 画圆
for($i=1;$i<=8;$i++){
$radius=mt_rand(80,120);
$x=$i*60;
imagefilledellipse($aimg, $x, mt_rand(30,90), $radius, $radius, imagecolorallocatealpha($aimg, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255), mt_rand(100,110)));
}


for($j=0;$j<50;$j++){
	$xPos = mt_rand(0,$img_width);
	$yPos = mt_rand(0,$img_height);
	imagefilledrectangle($aimg,$xPos,$yPos,$xPos+mt_rand(2,4),$yPos+mt_rand(2,4),imageColorAllocate($aimg,mt_rand(100,255),mt_rand(100,255),mt_rand(100,255)));
}
*/
}else{ // if gzip else not



$the_face=mt_rand(0,$max_face);
$the_face_img_file="face/{$the_face}.gif";
$face_img=imagecreatefromgif($the_face_img_file);

$unmsg = mb_convert_encoding($nmsg, "UTF-8","GBK");

$aimg = imagecreatetruecolor($img_width,$img_height);
$back = imagecolorallocate($aimg, 255, 255, 255);
imagefilledrectangle($aimg, 0, 0, $img_width - 1, $img_height - 1, $back);




$font="v.ttf";
for ($i=0;$i<mb_strlen($unmsg);$i++){
        $n=mb_substr($unmsg,$i,1,"UTF-8");
	imagettftext($aimg,70,0,$i*80+40,80,imageColorAllocate($aimg,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200)),$font,$n);
}
$font="t.ttf";

$alphanum = '123456';
while(true){
$pos = substr( str_shuffle( $alphanum ), 2, 4 );
$d=0;
for($i=0;$i<strlen($pos);$i++){
        $d=$d+$pos[$i];
}
if($d!=10 && $d!=14 && $d!=18) break;
}

for($j=0;$j<100;$j++){
	$xPos = mt_rand(0,$img_width);
	$yPos = mt_rand(0,$img_height);
	imagefilledrectangle($aimg,$xPos,$yPos,$xPos+mt_rand(3,6),$yPos+mt_rand(3,6),imageColorAllocate($aimg,mt_rand(0,100),mt_rand(0,150),mt_rand(0,200)));
}

$xmsg="昆";
$xmsg = mb_convert_encoding($xmsg, "UTF-8","GBK");
$g=0;
for ($i=0;$i<mb_strlen($unmsg);$i++){
        $j=$i+1;
        if(substr_count($pos,$j)){
                $t_img=$the_face_img_file;
                $now_img=$face_img;
                $fcode.=$nmsg[$i];
        }else{
                $t_img="face/".get_random($the_face).".gif";
                $now_img=imagecreatefromgif($t_img);
        }
        //$now_img = imagerotate($now_img, mt_rand(-45,50),"000",0);
        list($width, $height, $type, $attr) = getimagesize($t_img);
        imagecopymerge($aimg, $now_img, $i*80+40+mt_rand(0,30),mt_rand(10,60), 0, 0, $width, $height, 60 );
}
$_SESSION[vcode] = $fcode;
//$s="按顺序输入有4个相同图案的数字~";
//$s = mb_convert_encoding($s, "UTF-8","GBK");
//imagettftext($aimg,18,0,mt_rand(11,55),mt_rand(108,112),imageColorAllocate($aimg,mt_rand(100,150),mt_rand(100,150),mt_rand(100,150)),$font,$s);

//$s="Please enter the 4 numbers with same picture";
//$s = mb_convert_encoding($s, "UTF-8","GBK");
//imagettftext($aimg,14,0,mt_rand(11,55),mt_rand(130,138),imageColorAllocate($aimg,mt_rand(100,150),mt_rand(100,150),mt_rand(100,150)),$font,$s);

/* 画圆
for($i=1;$i<=8;$i++){
$radius=mt_rand(80,120);
$x=$i*60;
imagefilledellipse($aimg, $x, mt_rand(30,90), $radius, $radius, imagecolorallocatealpha($aimg, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255), mt_rand(100,110)));
}


for($j=0;$j<50;$j++){
	$xPos = mt_rand(0,$img_width);
	$yPos = mt_rand(0,$img_height);
	imagefilledrectangle($aimg,$xPos,$yPos,$xPos+mt_rand(2,4),$yPos+mt_rand(2,4),imageColorAllocate($aimg,mt_rand(100,255),mt_rand(100,255),mt_rand(100,255)));
}
*/



} //end if gzip

Header("Content-type: image/gif");
imagegif($aimg);
ImageDestroy($aimg);
