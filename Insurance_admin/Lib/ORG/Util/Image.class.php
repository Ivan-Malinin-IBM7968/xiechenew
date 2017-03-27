<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Image.class.php 2601 2012-01-15 04:59:14Z liu21st $

/**
  +------------------------------------------------------------------------------
 * 图像操作类库
  +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Image.class.php 2601 2012-01-15 04:59:14Z liu21st $
  +------------------------------------------------------------------------------
 */
class Image {

    /**
      +----------------------------------------------------------
     * 取得图像信息
     *
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @param string $image 图像文件名
      +----------------------------------------------------------
     * @return mixed
      +----------------------------------------------------------
     */

    static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($img);
            $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "size" => $imageSize,
                "mime" => $imageInfo['mime']
            );
            return $info;
        } else {
            return false;
        }
    }

    /**
      +----------------------------------------------------------
     * 为图片添加水印
      +----------------------------------------------------------
     * @static public
      +----------------------------------------------------------
     * @param string $source 原文件名
     * @param string $water  水印图片
     * @param string $$savename  添加水印后的图片名
     * @param string $alpha  水印的透明度
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     * @throws ThinkExecption
      +----------------------------------------------------------
     */
    static public function water($source, $water, $savename=null, $alpha=80) {
        //检查文件是否存在
        if (!file_exists($source) || !file_exists($water))
            return false;

        //图片信息
        $sInfo = self::getImageInfo($source);
        $wInfo = self::getImageInfo($water);

        //如果图片小于水印图片，不生成图片
        if ($sInfo["width"] < $wInfo["width"] || $sInfo['height'] < $wInfo['height'])
            return false;

        //建立图像
        $sCreateFun = "imagecreatefrom" . $sInfo['type'];
        $sImage = $sCreateFun($source);
        $wCreateFun = "imagecreatefrom" . $wInfo['type'];
        $wImage = $wCreateFun($water);

        //设定图像的混色模式
        imagealphablending($wImage, true);

        //图像位置,默认为右下角右对齐
        $posY = $sInfo["height"] - $wInfo["height"];
        $posX = $sInfo["width"] - $wInfo["width"];

        //生成混合图像
        imagecopymerge($sImage, $wImage, $posX, $posY, 0, 0, $wInfo['width'], $wInfo['height'], $alpha);

        //输出图像
        $ImageFun = 'Image' . $sInfo['type'];
        //如果没有给出保存文件名，默认为原图像名
        if (!$savename) {
            $savename = $source;
            @unlink($source);
        }
        //保存图像
        $ImageFun($sImage, $savename);
        imagedestroy($sImage);
    }

    function showImg($imgFile, $text='', $x='10', $y='10', $alpha='50') {
        //获取图像文件信息
        //2007/6/26 增加图片水印输出，$text为图片的完整路径即可
        $info = Image::getImageInfo($imgFile);
        if ($info !== false) {
            $createFun = str_replace('/', 'createfrom', $info['mime']);
            $im = $createFun($imgFile);
            if ($im) {
                $ImageFun = str_replace('/', '', $info['mime']);
                //水印开始
                if (!empty($text)) {
                    $tc = imagecolorallocate($im, 0, 0, 0);
                    if (is_file($text) && file_exists($text)) {//判断$text是否是图片路径
                        // 取得水印信息
                        $textInfo = Image::getImageInfo($text);
                        $createFun2 = str_replace('/', 'createfrom', $textInfo['mime']);
                        $waterMark = $createFun2($text);
                        //$waterMark=imagecolorallocatealpha($text,255,255,0,50);
                        $imgW = $info["width"];
                        $imgH = $info["width"] * $textInfo["height"] / $textInfo["width"];
                        //$y	=	($info["height"]-$textInfo["height"])/2;
                        //设置水印的显示位置和透明度支持各种图片格式
                        imagecopymerge($im, $waterMark, $x, $y, 0, 0, $textInfo['width'], $textInfo['height'], $alpha);
                    } else {
                        imagestring($im, 80, $x, $y, $text, $tc);
                    }
                    //ImageDestroy($tc);
                }
                //水印结束
                if ($info['type'] == 'png' || $info['type'] == 'gif') {
                    imagealphablending($im, FALSE); //取消默认的混色模式
                    imagesavealpha($im, TRUE); //设定保存完整的 alpha 通道信息
                }
                Header("Content-type: " . $info['mime']);
                $ImageFun($im);
                @ImageDestroy($im);
                return;
            }

            //保存图像
            $ImageFun($sImage, $savename);
            imagedestroy($sImage);
            //获取或者创建图像文件失败则生成空白PNG图片
            $im = imagecreatetruecolor(80, 30);
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
            imagestring($im, 4, 5, 5, "no pic", $tc);
            Image::output($im);
            return;
        }
    }

    /**
      +----------------------------------------------------------
     * 生成缩略图
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @param string $image  原图
     * @param string $type 图像格式
     * @param string $thumbname 缩略图文件名
     * @param string $maxWidth  宽度
     * @param string $maxHeight  高度
     * @param string $position 缩略图保存目录
     * @param boolean $interlace 启用隔行扫描
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    static function thumb($image, $thumbname, $type='', $maxWidth=200, $maxHeight=50, $interlace=true) {
        // 获取原图信息
        $info = Image::getImageInfo($image);
        if ($info !== false) {
            $srcWidth = $info['width'];
            $srcHeight = $info['height'];
            $type = empty($type) ? $info['type'] : $type;
            $type = strtolower($type);
            $interlace = $interlace ? 1 : 0;
            unset($info);
            $scale = min($maxWidth / $srcWidth, $maxHeight / $srcHeight); // 计算缩放比例
            if ($scale >= 1) {
                // 超过原图大小不再缩略
                $width = $srcWidth;
                $height = $srcHeight;
            } else {
                // 缩略图尺寸
                $width = (int) ($srcWidth * $scale);
                $height = (int) ($srcHeight * $scale);
            }

            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            $srcImg = $createFun($image);

            //创建缩略图
            if ($type != 'gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height);
            else
                $thumbImg = imagecreate($width, $height);

            // 复制图片
            if (function_exists("ImageCopyResampled"))
                imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
            else
                imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth, $srcHeight);
            if ('gif' == $type || 'png' == $type) {
                //imagealphablending($thumbImg, false);//取消默认的混色模式
                //imagesavealpha($thumbImg,true);//设定保存完整的 alpha 通道信息
                $background_color = imagecolorallocate($thumbImg, 0, 255, 0);  //  指派一个绿色
                imagecolortransparent($thumbImg, $background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
            }

            // 对jpeg图形设置隔行扫描
            if ('jpg' == $type || 'jpeg' == $type)
                imageinterlace($thumbImg, $interlace);

            //$gray=ImageColorAllocate($thumbImg,255,0,0);
            //ImageString($thumbImg,2,5,5,"ThinkPHP",$gray);
            // 生成图片
            $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
            $imageFun($thumbImg, $thumbname);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            return $thumbname;
        }
        return false;
    }

    /**
      +----------------------------------------------------------
     * 根据给定的字符串生成图像
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @param string $string  字符串
     * @param string $size  图像大小 width,height 或者 array(width,height)
     * @param string $font  字体信息 fontface,fontsize 或者 array(fontface,fontsize)
     * @param string $type 图像格式 默认PNG
     * @param integer $disturb 是否干扰 1 点干扰 2 线干扰 3 复合干扰 0 无干扰
     * @param bool $border  是否加边框 array(color)
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    static function buildString($string, $rgb=array(), $filename='', $type='png', $disturb=1, $border=true) {
        if (is_string($size))
            $size = explode(',', $size);
        $width = $size[0];
        $height = $size[1];
        if (is_string($font))
            $font = explode(',', $font);
        $fontface = $font[0];
        $fontsize = $font[1];
        $length = strlen($string);
        $width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;
        $height = 22;
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($width, $height);
        } else {
            $im = @imagecreate($width, $height);
        }
        if (empty($rgb)) {
            $color = imagecolorallocate($im, 102, 104, 104);
        } else {
            $color = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        }
        $backColor = imagecolorallocate($im, 255, 255, 255);    //背景色（随机）
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        $pointColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));                 //点颜色

        @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        @imagestring($im, 5, 5, 3, $string, $color);
        if (!empty($disturb)) {
            // 添加干扰
            if ($disturb = 1 || $disturb = 3) {
                for ($i = 0; $i < 25; $i++) {
                    imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
                }
            } elseif ($disturb = 2 || $disturb = 3) {
                for ($i = 0; $i < 10; $i++) {
                    imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $pointColor);
                }
            }
        }
        Image::output($im, $type, $filename);
    }

    /**
      +----------------------------------------------------------
     * 生成图像验证码
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @param string $length  位数
     * @param string $mode  类型
     * @param string $type 图像格式
     * @param string $width  宽度
     * @param string $height  高度
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    static function buildImageVerify($length=4, $mode=1, $type='png', $width=48, $height=22, $verifyName='verify') {
        import('@.ORG.Util.String');
        $randval = String::randString($length, $mode);
        $_SESSION[$verifyName] = md5($randval);
        $width = ($length * 10 + 10) > $width ? $length * 10 + 10 : $width;
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($width, $height);
        } else {
            $im = imagecreate($width, $height);
        }
        $r = Array(225, 255, 255, 223);
        $g = Array(225, 236, 237, 255);
        $b = Array(225, 236, 166, 125);
        $key = mt_rand(0, 3);

        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);    //背景色（随机）
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        $stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
        // 干扰
        for ($i = 0; $i < 10; $i++) {
            imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $stringColor);
        }
        for ($i = 0; $i < 25; $i++) {
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $stringColor);
        }
        for ($i = 0; $i < $length; $i++) {
            imagestring($im, 5, $i * 10 + 5, mt_rand(1, 8), $randval{$i}, $stringColor);
        }
        Image::output($im, $type);
    }

    // 中文验证码
    static function GBVerify($length=4, $type='png', $width=180, $height=50, $fontface='simhei.ttf', $verifyName='verify') {
        import('ORG.Util.String');
        $code = String::randString($length, 4);
        $width = ($length * 45) > $width ? $length * 45 : $width;
        $_SESSION[$verifyName] = md5($code);
        $im = imagecreatetruecolor($width, $height);
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        $bkcolor = imagecolorallocate($im, 250, 250, 250);
        imagefill($im, 0, 0, $bkcolor);
        @imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        // 干扰
        for ($i = 0; $i < 15; $i++) {
            $fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $fontcolor);
        }
        for ($i = 0; $i < 255; $i++) {
            $fontcolor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $fontcolor);
        }
        if (!is_file($fontface)) {
            $fontface = dirname(__FILE__) . "/" . $fontface;
        }
        for ($i = 0; $i < $length; $i++) {
            $fontcolor = imagecolorallocate($im, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120)); //这样保证随机出来的颜色较深。
            $codex = String::msubstr($code, $i, 1);
            imagettftext($im, mt_rand(16, 20), mt_rand(-60, 60), 40 * $i + 20, mt_rand(30, 35), $fontcolor, $fontface, $codex);
        }
        Image::output($im, $type);
    }

    /**
      +----------------------------------------------------------
     * 把图像转换成字符显示
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @param string $image  要显示的图像
     * @param string $type  图像类型，默认自动获取
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    static function showASCIIImg($image, $string='', $type='') {
        $info = Image::getImageInfo($image);
        if ($info !== false) {
            $type = empty($type) ? $info['type'] : $type;
            unset($info);
            // 载入原图
            $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
            $im = $createFun($image);
            $dx = imagesx($im);
            $dy = imagesy($im);
            $i = 0;
            $out = '<span style="padding:0px;margin:0;line-height:100%;font-size:1px;">';
            set_time_limit(0);
            for ($y = 0; $y < $dy; $y++) {
                for ($x = 0; $x < $dx; $x++) {
                    $col = imagecolorat($im, $x, $y);
                    $rgb = imagecolorsforindex($im, $col);
                    $str = empty($string) ? '*' : $string[$i++];
                    $out .= sprintf('<span style="margin:0px;color:#%02x%02x%02x">' . $str . '</span>', $rgb['red'], $rgb['green'], $rgb['blue']);
                }
                $out .= "<br>\n";
            }
            $out .= '</span>';
            imagedestroy($im);
            return $out;
        }
        return false;
    }

    /**
      +----------------------------------------------------------
     * 生成高级图像验证码
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @param string $type 图像格式
     * @param string $width  宽度
     * @param string $height  高度
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    static function showAdvVerify($type='png', $width=180, $height=40, $verifyName='verifyCode') {
        $rand = range('a', 'z');
        shuffle($rand);
        $verifyCode = array_slice($rand, 0, 10);
        $letter = implode(" ", $verifyCode);
        $_SESSION[$verifyName] = $verifyCode;
        $im = imagecreate($width, $height);
        $r = array(225, 255, 255, 223);
        $g = array(225, 236, 237, 255);
        $b = array(225, 236, 166, 125);
        $key = mt_rand(0, 3);
        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        $numberColor = imagecolorallocate($im, 255, rand(0, 100), rand(0, 100));
        $stringColor = imagecolorallocate($im, rand(0, 100), rand(0, 100), 255);
        // 添加干扰
        /**/
          for($i=0;$i<10;$i++){
          $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
          imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
          }
          for($i=0;$i<255;$i++){
          $fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
          imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$fontcolor);
          } 
        imagestring($im, 5, 5, 1, "0 1 2 3 4 5 6 7 8 9", $numberColor);
        imagestring($im, 5, 5, 20, $letter, $stringColor);
        Image::output($im, $type);
    }

    /**
      +----------------------------------------------------------
     * 生成UPC-A条形码
      +----------------------------------------------------------
     * @static
      +----------------------------------------------------------
     * @param string $type 图像格式
     * @param string $type 图像格式
     * @param string $lw  单元宽度
     * @param string $hi   条码高度
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    static function UPCA($code, $type='png', $lw=2, $hi=100) {
        static $Lencode = array('0001101', '0011001', '0010011', '0111101', '0100011',
    '0110001', '0101111', '0111011', '0110111', '0001011');
        static $Rencode = array('1110010', '1100110', '1101100', '1000010', '1011100',
    '1001110', '1010000', '1000100', '1001000', '1110100');
        $ends = '101';
        $center = '01010';
        /* UPC-A Must be 11 digits, we compute the checksum. */
        if (strlen($code) != 11) {
            die("UPC-A Must be 11 digits.");
        }
        /* Compute the EAN-13 Checksum digit */
        $ncode = '0' . $code;
        $even = 0;
        $odd = 0;
        for ($x = 0; $x < 12; $x++) {
            if ($x % 2) {
                $odd += $ncode[$x];
            } else {
                $even += $ncode[$x];
            }
        }
        $code.= ( 10 - (($odd * 3 + $even) % 10)) % 10;
        /* Create the bar encoding using a binary string */
        $bars = $ends;
        $bars.=$Lencode[$code[0]];
        for ($x = 1; $x < 6; $x++) {
            $bars.=$Lencode[$code[$x]];
        }
        $bars.=$center;
        for ($x = 6; $x < 12; $x++) {
            $bars.=$Rencode[$code[$x]];
        }
        $bars.=$ends;
        /* Generate the Barcode Image */
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($lw * 95 + 30, $hi + 30);
        } else {
            $im = imagecreate($lw * 95 + 30, $hi + 30);
        }
        $fg = ImageColorAllocate($im, 0, 0, 0);
        $bg = ImageColorAllocate($im, 255, 255, 255);
        ImageFilledRectangle($im, 0, 0, $lw * 95 + 30, $hi + 30, $bg);
        $shift = 10;
        for ($x = 0; $x < strlen($bars); $x++) {
            if (($x < 10) || ($x >= 45 && $x < 50) || ($x >= 85)) {
                $sh = 10;
            } else {
                $sh = 0;
            }
            if ($bars[$x] == '1') {
                $color = $fg;
            } else {
                $color = $bg;
            }
            ImageFilledRectangle($im, ($x * $lw) + 15, 5, ($x + 1) * $lw + 14, $hi + 5 + $sh, $color);
        }
        /* Add the Human Readable Label */
        ImageString($im, 4, 5, $hi - 5, $code[0], $fg);
        for ($x = 0; $x < 5; $x++) {
            ImageString($im, 5, $lw * (13 + $x * 6) + 15, $hi + 5, $code[$x + 1], $fg);
            ImageString($im, 5, $lw * (53 + $x * 6) + 15, $hi + 5, $code[$x + 6], $fg);
        }
        ImageString($im, 4, $lw * 95 + 17, $hi - 5, $code[11], $fg);
        /* Output the Header and Content. */
        Image::output($im, $type);
    }

    static function output($im, $type='png', $filename='') {
        ob_clean(); //防止出现'图像因其本身有错无法显示'的问题
        header("Content-type: image/" . $type);
        $ImageFun = 'image' . $type;
        if (empty($filename)) {
            $ImageFun($im);
        } else {
            $ImageFun($im, $filename);
        }
        imagedestroy($im);
    }
    
    static function gettableimg($text){
      // Set font size
      $font_size = 12;
      $ts=explode("/n",$text);
      $width=0;
      foreach ($ts as $k=>$string) { //compute width
        $width=max($width,strlen($string));
      }
      // Create image width dependant on width of the string
      $width  = imagefontwidth($font_size)*$width;
      // Set height to that of the font
      $height = imagefontheight($font_size)*count($ts);
      
      // Create the image pallette
      $img = imagecreatetruecolor(200,100);
      // Dark red background
      $bg = imagecolorallocate($img, 255, 255, 255);
      imagefilledrectangle($img, 0, 0,200 ,100 , $bg);
      // White font color
      $color = imagecolorallocate($img, 0, 0, 0);
      $fontface = dirname(__FILE__) . "/simhei.ttf";
      $el=imagefontheight($font_size);
      $em=imagefontwidth($font_size);
      foreach ($ts as $k=>$string) {
          $a = 0+$k*50;
          $b = 0+$k*50;
          $c = 0+$k*50;
          $k++;
          $color = imagecolorallocate($img, $a, $b, $c);
        // Length of the string
        $len = strlen($string);
        // Y-coordinate of character, X changes, Y is static
        $ypos = 0;
        // Loop through the string
        //for($i=0;$i<$len;$i++){
          // Position of the character horizontally
          //$xpos = $i * $em;
          //$ypos = $k * $el;
          // Draw character
          //imagettftext($img,  20,  0,  $xpos,  $ypos,  $color,  $fontface, $string);    
          //imagechar($img, $font_size, $xpos, $ypos, $string, $color);
          imagettftext($img, $font_size, 0, 40, 30*$k, $color, $fontface, $string);
          //imagestring($img, 5, 0, 0, $string, $color);
          // Remove character from string
          //$string = substr($string, 1);     
        //}
      }
      Image::output($img);
    }
    
    static function getimgtable($tables_arr=array(),$img_name='',$tr_height='40',$td_width='70',$fontsize='12',$fontcolor='0,0,0'){
        $echo_word_width = 15.9;
        $height = 0;
        $width = 0;
        $height_add = 15;
        $height_addnum = 0;
        $width_arr = array();
        if (!empty($tables_arr)){
            foreach ($tables_arr as $t=>$table){
                if (!empty($table)){
                    foreach ($table as $tr){
                        if (isset($tr['height']) and $tr['height']){
                            $height +=$tr['height'];
                        }else {
                            $height +=$tr_height;
                        }
                        if (isset($tr['tr']) and !empty($tr['tr'])){
                            $tr_width = 0;
                            $height_tr_addnum = 0;
                            foreach ($tr['tr'] as $k=>$td){
                                if(!isset($width_arr[$t][$k])){
                                    $width_arr[$t][$k] = 0;
                                }
                                if (isset($td['width']) and $td['width']){
                                    $tr_width += $td['width'];
                                }else{
                                    $tr_width += $td_width;
                                }
                                if(!isset($td['colspan'])){
                                    $width_arr[$t][$k] = max($width_arr[$t][$k],$tr_width);
                                }
                            }
                        }
                        $width = max($width,$tr_width);
                    }
                }
            }
        }
    if (!empty($tables_arr)){
            foreach ($tables_arr as $t=>$table){
                if (!empty($table)){
                    foreach ($table as $tr){
                        if (isset($tr['tr']) and !empty($tr['tr'])){
                            $height_tr_addnum = 0;
                            foreach ($tr['tr'] as $k=>$td){
                                $str_width = (strlen($td['str']) + mb_strlen($td['str'],'UTF8'))*$echo_word_width/4;
                                if ($td['noproduct']){
                                    $height_tr_addnum = max($height_tr_addnum,ceil($str_width/($td['width']-5)));
                                }else {
                                    if (isset($width_arr[$t][$k-1])){
                                        if (!isset($td['colspan']) and ($width_arr[$t][$k]-$width_arr[$t][$k-1])-5<$str_width){
                                            $height_tr_addnum = max($height_tr_addnum,ceil($str_width/(($width_arr[$t][$k]-$width_arr[$t][$k-1])-5)));
                                        }else {
                                            
                                        }
                                    }else{
                                        if (!isset($td['colspan']) and $width_arr[$t][$k]-5<$str_width){
                                            $height_tr_addnum = max($height_tr_addnum,ceil($str_width/($td['width']-5)));
                                        }elseif($width_arr[$t][$k+$td['colspan']-1]-5<$str_width) {
                                            $height_tr_addnum = max($height_tr_addnum,ceil($str_width/($width_arr[$t][$k+$td['colspan']-1]-5)));
                                        }
                                    }
                                }
                            }
                        }
                        $height_addnum += $height_tr_addnum;
                    }
                }
            }
        }
        $max_width = $width;
        $height += $height_addnum*$height_add;
        //echo '<pre>';print_r($width_arr);exit;
        //echo $width.'--'.$height;exit;
        // Create the image pallette
        $img = imagecreatetruecolor($width+10,$height+20);
        $bg = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0 ,0 , $width+10 ,$height+20 , $bg);
        
        $fontface = dirname(__FILE__) . "/simhei.ttf";
        if (!empty($tables_arr)){
            $height = 0;
            $width = 0;
            foreach ($tables_arr as $t=>$table){
                if (!empty($table)){
                    foreach ($table as $tr){
                        if (isset($tr['height']) and $tr['height']){
                            $height_tr =$tr['height'];
                        }else {
                            $height_tr =$tr_height;
                        }
                        $height +=$height_tr;
                        if (isset($tr['tr']) and !empty($tr['tr'])){
                            $height_tr_add = 0;
                            foreach ($tr['tr'] as $k=>$td){
                                if (isset($td['size']) and $td['size']){
                                    $size = $td['size'];
                                }else{
                                    $size = $fontsize;
                                }
                                if (isset($td['str'])){
                                    $str = $td['str'];
                                }else{
                                    $str = '';
                                }
                                if (isset($td['color']) and $td['color']){
                                    $color = $td['color'];
                                }else{
                                    $color = $fontcolor;
                                }
                                $color_arr = explode(',',$color);
                                $colors = imagecolorallocate($img,$color_arr[0],$color_arr[1],$color_arr[2]);
                                if (isset($td['noproduct']) and $td['noproduct']){
                                    $width = 0;
                                    $next_width = $td['width'];
                                    //echo (strlen($str) + mb_strlen($str,'UTF8'))/4;
                                    $str_width = (strlen($str) + mb_strlen($str,'UTF8'))*$echo_word_width/4 ;
                                    $str_new = '';
                                    $str_move = '';//echo $next_width.'--'.$str_width;exit;
                                    if ($width+$str_width>$next_width-15){//echo $str;exit;
                                        for ($i=1;$i<$str_width/2;$i++){
                                            $str_new = mb_substr($str,0,-$i,'utf-8');
                                            $str_move = mb_substr($str,-$i,$i,'utf-8');
                                            $str_width = (strlen($str_new) + mb_strlen($str_new,'UTF8'))*$echo_word_width/4 ;
                                            if ($width+$str_width<$next_width-15){
                                                imagettftext($img, $size, 0, 10, $height, $colors, $fontface, $str_new);
                                                break;
                                            }
                                        }
                                        if ($str_move){
                                            $str_move_new = '';
                                            $str_move_move = '';
                                            $str_move_width = (strlen($str_move) + mb_strlen($str_move,'UTF8'))*$echo_word_width/4 ;
                                            if ($width+$str_move_width>$next_width-15){//echo $str_move;exit;
                                                for ($i=1;$i<$str_move_width/2;$i++){
                                                    $str_move_new = mb_substr($str_move,0,-$i,'utf-8');
                                                    $str_move_move = mb_substr($str_move,-$i,$i,'utf-8');
                                                    $str_move_width = (strlen($str_move_new) + mb_strlen($str_move_new,'UTF8'))*$echo_word_width/4 ;
                                                    if ($width+$str_move_width<$next_width-15){
                                                        $height_tr_add = max($height_tr_add,$height_add);
                                                        imagettftext($img, $size, 0, 10, $height+$height_add, $colors, $fontface, $str_move_new);
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($str_move_move){
                                            $str_move_move_new = '';
                                            $str_move_move_move = '';
                                            $str_move_move_width = (strlen($str_move_move) + mb_strlen($str_move_move,'UTF8'))*$echo_word_width/4 ;
                                            if ($width+$str_move_move_width>$next_width-15){//echo $str_move;exit;
                                                for ($i=1;$i<$str_move_move_width/2;$i++){
                                                    $str_move_move_new = mb_substr($str_move_move,0,-$i,'utf-8');
                                                    $str_move_move_move = mb_substr($str_move_move,-$i,$i,'utf-8');
                                                    $str_move_move_width = (strlen($str_move_move_new) + mb_strlen($str_move_move_new,'UTF8'))*$echo_word_width/4 ;
                                                    if ($width+$str_move_move_width<$next_width-15){
                                                        $height_tr_add = max($height_tr_add,$height_add*2);
                                                        imagettftext($img, $size, 0, 10, $height+$height_add*2, $colors, $fontface, $str_move_move_new);
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($str_move_move_move){//echo $str_move_move_move.'<br>';exit;
                                            $height_tr_add = max($height_tr_add,$height_add*3);
                                            imagettftext($img, $size, 0, 10, $height+$height_add*3, $colors, $fontface, $str_move_move_move); 
                                        }elseif($str_move_move) {
                                            $height_tr_add = max($height_tr_add,$height_add*2);
                                            imagettftext($img, $size, 0, 10, $height+$height_add*2, $colors, $fontface, $str_move_move_move); 
                                        }elseif ($str_move){
                                            $height_tr_add = max($height_tr_add,$height_add);
                                            imagettftext($img, $size, 0, 10, $height+$height_add, $colors, $fontface, $str_move_move_move); 
                                        }
                                        
                                    }
                                    //echo $height_tr_add;exit;
                                }else {
                                    if (isset($td['align']) and $td['align'] == 'center'){
                                        if (isset($td['colspan']) and $td['colspan']>1 and isset($width_arr[$t][$k+$td['colspan']-1])){
                                            $width = $width_arr[$t][$k+$td['colspan']-1];
                                        }else {
                                            $width = $width_arr[$t][$k];
                                        }
                                        $str_width = (strlen($str) + mb_strlen($str,'UTF8'))*$echo_word_width/4;
                                        $width = ($width-$str_width)/2;
                                    }else{
                                        $str_new = '';
                                        $str_move = '';
                                        if (isset($td['colspan']) and $td['colspan']>1){
                                            if ($k == '0'){
                                                $width = 0;
                                                $next_width = $width_arr[$t][$k+$td['colspan']-1];
                                            }else{
                                                $width = $width_arr[$t][$k+$td['colspan']-2];
                                                $next_width = $width_arr[$t][$k+$td['colspan']-1];
                                            }
                                        }else{
                                            if (isset($width_arr[$t][$k-1])){
                                                $width = $width_arr[$t][$k-1];
                                                $next_width = $width_arr[$t][$k];
                                            }else{
                                                $width = 0;
                                                $next_width = $width_arr[$t][$k];
                                            }
                                            $str_width = (strlen($str) + mb_strlen($str,'UTF8'))*$echo_word_width/4 ;
                                            if ($width+$str_width>$next_width-5){//echo $str;exit;
                                                for ($i=1;$i<$str_width/2;$i++){
                                                    $str_new = mb_substr($str,0,-$i,'utf-8');
                                                    $str_move = mb_substr($str,-$i,$i,'utf-8');
                                                    $str_width = (strlen($str_new) + mb_strlen($str_new,'UTF8'))*$echo_word_width/4 ;
                                                    if ($width+$str_width<$next_width-5){
                                                        break;
                                                    }
                                                }
                                                if ($str_move){
                                                    $str_move_new = '';
                                                    $str_move_move = '';
                                                    $str_move_width = (strlen($str_move) + mb_strlen($str_move,'UTF8'))*$echo_word_width/4 ;
                                                    if ($width+$str_move_width>$next_width-5){//echo $str_move;exit;
                                                        for ($i=1;$i<$str_move_width/2;$i++){
                                                            $str_move_new = mb_substr($str_move,0,-$i,'utf-8');
                                                            $str_move_move = mb_substr($str_move,-$i,$i,'utf-8');
                                                            $str_move_width = (strlen($str_move_new) + mb_strlen($str_move_new,'UTF8'))*$echo_word_width/4 ;
                                                            if ($width+$str_move_width<$next_width-5){
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($str_new and $str_move){
                                        if ($str_move_new and $str_move_move){
                                            imagettftext($img, $size, 0, $width+10, $height, $colors, $fontface, $str_new);
                                            $height_add = 15;
                                            imagettftext($img, $size, 0, $width+10, $height+$height_add, $colors, $fontface, $str_move_new);
                                            $height_add = $height_add*2;
                                            $height_tr_add = max($height_tr_add,$height_add);
                                            imagettftext($img, $size, 0, $width+10, $height+$height_add, $colors, $fontface, $str_move_move);
                                        }else{
                                            imagettftext($img, $size, 0, $width+10, $height, $colors, $fontface, $str_new);
                                            $height_add = 15;
                                            $height_tr_add = max($height_tr_add,$height_add);
                                            imagettftext($img, $size, 0, $width+10, $height+$height_add, $colors, $fontface, $str_move);
                                        }
                                    }else {
                                        imagettftext($img, $size, 0, $width+10, $height, $colors, $fontface, $str);
                                    }
                                //echo $width;exit;
                                }
                            }
                            $height +=$height_tr_add;
                        }
                    }
                }
            }
        }
        Image::output($img,'png',$img_name);
    }
    
    
    
}