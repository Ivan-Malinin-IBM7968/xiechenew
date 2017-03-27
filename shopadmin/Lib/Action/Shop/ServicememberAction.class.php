<?php
class ServicememberAction extends CommonAction {
    function __construct() {
		parent::__construct();
//		if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
//            $shop_id = $_SESSION['shop_id'];
//        }else {
//            $this->error('店铺ID不存在！');
//        }
		$this->ServicememberModel = D('Servicemember');//优惠卷表
		$this->ShopModel = D('shop');//店铺表
	}

	
    public function index(){
		if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }

		Cookie::set('_currentUrl_', __SELF__);
		if($_REQUEST['name'] && $_REQUEST['name'] != '搜索服务顾问'  ){
			$map['name'] = array('LIKE','%'.$_REQUEST['name'].'%');
			$data['name'] = $_REQUEST['name'];
		}
		$shop_id = $_SESSION['shop_id'];
		if($shop_id){
			$data['shop_id'] = $map['shop_id'] = $shop_id;
		}
        // 计算总数
        $count = $this->ServicememberModel->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $this->ServicememberModel->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if (!empty($list)){
            foreach ($list as $k=>$v){
				$shop_info = $this->ShopModel->find($v['shop_id']);
				$list[$k]['shop_name'] = $shop_info['shop_name'];
				
				$shopadmin_model = M('tp_admin.shopadmin','xc_');

				$shopadmin_info = $shopadmin_model->find($v['shopadmin_id']);
				$list[$k]['account'] = $shopadmin_info['account'];
            }
        }
        // 赋值赋值
        $this->assign('page', $page);
		$this->assign('data', $data);
        $this->assign('list', $list);
        $this->display();
    }

	function _before_edit() {
		$id = $_REQUEST['id'];
		$servicemember = $this->ServicememberModel->find($id);

		$shopadmin_model = M('tp_admin.shopadmin','xc_');
		$shopadmin_info = $shopadmin_model->find($servicemember['shopadmin_id']);
		$this->assign('account',$shopadmin_info['account']);
		
	}
	function _before_insert() {
		
		$x = $_REQUEST['x'];
		$y = $_REQUEST['y'];
		$w = $_REQUEST['w'];
		$h = $_REQUEST['h'];

		$this->resized_img($_REQUEST['img_src'],$x,$y,$w,$h);
		$_POST['create_time'] = time();
		
		$shopadmin_model = M('tp_admin.shopadmin','xc_');
		$usershop_model = M('tp_admin.user_shop','xc_');

		$data['account'] = $_POST['username'];
		$data['nickname'] = $_POST['name'];
		$data['password'] = pwdHash($_POST['password']);
		$data['mobile'] = $_POST['mobile'];
		$data['create_time'] = time();
		$data['remark'] = "servicemember";
		$data['status'] = 1;
		$count = $shopadmin_model->where(array('account'=>$_POST['username']))->count();
		if($count>0){
			echo "<script>alert('后台用户名重复');history.go(-1);</script>";
			exit;
		}
		$_POST['shopadmin_id'] = $shopadmin_model->add($data);

		$data['authid'] = $_POST['shopadmin_id'];
		$data['shop_id'] = $_SESSION['shop_id'];

		$usershop_model->add($data);
		$_POST['shop_id'] = $_SESSION['shop_id'];
		$_POST['logo'] = $_REQUEST['img_src'];
	}


	function _before_update() {
		//----------->修改密码
		$id = $_REQUEST['id'];
		if($_POST['password'] && $_POST['repassword']) {
			$servicemember = $this->ServicememberModel->find($id);

			$shopadmin_model = M('tp_admin.shopadmin','xc_');
			$shopadmin_info = $shopadmin_model->find($servicemember['shopadmin_id']);

			$map	=	array();
			$map['password'] = pwdHash($_POST['oldpassword']);
			$map['id'] = $servicemember['shopadmin_id'];

			//检查用户
			if(!$shopadmin_model->where($map)->find()) {
				$this->error('旧密码不符！');
			}else {
				$data['id'] = $servicemember['shopadmin_id'];
				$data['password'] = pwdHash($_POST['password']);
				$shopadmin_model->save($data);
			 }
		}
		//----------->修改密码结束

		//$_POST['shop_id'] = $_SESSION['shop_id'];
		
		$x = $_REQUEST['x'];
		$y = $_REQUEST['y'];
		$w = $_REQUEST['w'];
		$h = $_REQUEST['h'];

		if($w && $h && $_REQUEST['img_src']) {
			$this->resized_img($_REQUEST['img_src'],$x,$y,$w,$h);
			$_POST['logo'] = $_REQUEST['img_src'];
		}
	}

	function delete() {
		$arr['id'] = $_REQUEST['id'];
		$data['state'] = $_REQUEST['state'];
		$this->ServicememberModel->where($arr)->save($data);
		$this->success("修改成功");
	}

	
	/*
		@author:chf
		@function:得到店铺名
		@time:2013-5-6
	*/
	function GetShopname(){
		if($_REQUEST['shopname']){
			$map['shop_name'] = array('LIKE',"%".$_REQUEST['shopname']."%");
		}
		$Shop = $this->ShopModel->where($map)->select();
		if($Shop){
			echo json_encode($Shop);
		}else{
			echo 'none';
			
		}
	}
	
	public function pic_upload() {

		if ($_FILES['logo']['name']){
		
			//echo $_FILES['logo']['name'];
            $result = $this->upload();
			echo $_POST['logo'];
        }
	}



	

	public function _upload_init($upload) {
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Service/';
        $upload->thumb = true;
        $upload->saveRule = 'uniqid';
        $upload->thumbPrefix = 'logo200_';//coupon1_网站图片显示；coupon2_手机APP图片显示
        $resizeThumbSize_arr = explode(',', C('RESIZE_THUMB_SIZE'));
		$resizeThumbSize_arr = array('200','200');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];
        return $upload;
    }


	function resized_img($img_src,$x,$y,$w,$h) {
			$path= 	C('UPLOAD_ROOT') . '/Service/';
			$smallfile = $path.$img_src; //上传后缩略图文件所在的文件名和路径
			
			if($img_src) {
				$img_info = explode(".",$img_src);
				$img_type = $img_info[1];
			}
			if($img_type == "png") {
				$src_image = ImageCreateFromPNG($smallfile); //读取JPEG文件并创建图像对象
			}elseif($img_type == "jpg") {
				$src_image = ImageCreateFromJPEG($smallfile); //读取JPEG文件并创建图像对象
			}elseif($img_type == "gif") {
				$src_image = ImageCreateFromGIF($smallfile); //读取JPEG文件并创建图像对象
			}
		
			$srcW = ImageSX($src_image); //获得图像的宽
			$srcH = ImageSY($src_image); //获得图像的高
			$dst_image = ImageCreateTrueColor($w,$h); //创建新的图像对象
			ImageCopyResized($dst_image,$src_image,0,0,$x,$y,$w,$h,$w,$h); //将图像重定义大小后写入新的图像对象
			//ImageCopyResampled($dst_image, $src_image, 0, 0, $x, $y, $w, $h, $w, $h);
			ImageJpeg($dst_image,$smallfile); //创建缩略图文件
			ImageDestroy($dst_image);
	}
    
}
?>