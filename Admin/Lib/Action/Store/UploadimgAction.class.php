<?php

class UploadimgAction extends CommonAction {
    function __construct() {
		parent::__construct();
	
	}
	

	/*
		@author:chf
		@function:上传店铺图片
		@time:2013-8-19
	*/
	function index(){
		$this->display();
	}
	/*
		@author:chf
		@function:上传店铺图片
		@time:2013-8-19
	*/
	function update(){
		$this->point();
		$this->success('上传图片成功!',U('/Store/Uploading/index'));
	}

	public function point()
    {
        if (!empty($_FILES["img"]["name"])) { //提取文件域内容名称，并判断 
			$path= C('UPLOAD_ROOT') . '/Shop/280/';
			
			if(!file_exists($path)) 
			{ 
			//检查是否有该文件夹，如果没有就创建，并给予最高权限 
			
			}//END IF 
			//允许上传的文件格式 
			$filetype = $_FILES['img']['type']; 
			if($filetype == 'image/jpeg'){ 
					$type = '.jpg'; 
				} 
				if ($filetype == 'image/jpg') { 
					$type = '.jpg'; 
				} 
				if ($filetype == 'image/pjpeg') { 
					$type = '.jpg'; 
				} 
				if($filetype == 'image/gif'){ 
					$type = '.gif'; 
				} 
			}
			
			if($_FILES["img"]["name"]) 
			{ 
			$today=date('YmdHis'); //获取时间并赋值给变量 
			$file2 = $path.$_FILES["img"]["name"]; //图片的完整路径 
			$img = $_FILES["img"]["name"].$type; //图片名称 
			$flag=1; 
			}//END IF 
			if($flag) $requlest = move_uploaded_file($_FILES["img"]["tmp_name"],$file2); 
			$uploadfile =$path.$_FILES['img']['name']; //上传后文件所在的文件名和路径

			$this->thumbnail($uploadfile,'100');
			$this->thumbnail($uploadfile,'130');
			$this->thumbnail($uploadfile,'200');
			$this->thumbnail($uploadfile,'50');
			$this->thumbnail($uploadfile,'80');
			
       }

	  function thumbnail($uploadfile,$num){
			$path= C('UPLOAD_ROOT') . '/Shop/'.$num.'/';
			$smallfile = $path.$_FILES['img']['name']; //上传后缩略图文件所在的文件名和路径
			if($num == '100'){
				$dstW = 100; //设定缩略图的宽度
				$dstH = 75; //设定缩略图的高度
			}
			if($num == '130'){
				$dstW = 130; //设定缩略图的宽度
				$dstH = 97; //设定缩略图的高度
			}
			if($num == '200'){
				$dstW = 200; //设定缩略图的宽度
				$dstH = 150; //设定缩略图的高度
			}
			if($num == '50'){
				$dstW = 50; //设定缩略图的宽度
				$dstH = 37; //设定缩略图的高度
			}
			if($num == '80'){
				$dstW = 80; //设定缩略图的宽度
				$dstH = 60; //设定缩略图的高度
			}
			
			$src_image = ImageCreateFromJPEG($uploadfile); //读取JPEG文件并创建图像对象
			$srcW = ImageSX($src_image); //获得图像的宽
			$srcH = ImageSY($src_image); //获得图像的高
			$dst_image = ImageCreateTrueColor($dstW,$dstH); //创建新的图像对象
			ImageCopyResized($dst_image,$src_image,0,0,0,0,$dstW,$dstH,$srcW,$srcH); //将图像重定义大小后写入新的图像对象
			ImageJpeg($dst_image,$smallfile); //创建缩略图文件
	  }
    
}
?>