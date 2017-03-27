<?php
class ServicefunAction extends CommonAction {
    function __construct() {
		parent::__construct();
//		if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
//            $shop_id = $_SESSION['shop_id'];
//        }else {
//            $this->error('店铺ID不存在！');
//        }
		$this->ServicefunModel = D('Servicefun');
		$this->ServicefunimgModel = D('Servicefunimg');
	}

	
    public function index(){
		$shop_id = $_SESSION['shop_id'];
		
		$map['shop_id'] = $shop_id;
		$Servicefun = $this->ServicefunModel->where($map)->select();
		$Servicefunimg = $this->ServicefunimgModel->where($map)->select();
		
		if($Servicefun) {
			foreach($Servicefun as $key=>$val) {
//				if($val['service_id'] != '0') {
					$service_ids[] = $val['service_id'];
//				}else {
//					$service_name['servicefun_id'] = $val['id'];
//					$service_name['service_name'] = $val['service_name'];
//				}
				
			}
		}
		$service_ids = implode(",",$service_ids);

		$this->assign("service_ids" , $service_ids);
//		$this->assign("service_name" , $service_name);
		$this->assign("Servicefun" , $Servicefun);
		$this->assign("Servicefunimg" , $Servicefunimg);
        $this->display();
    }

	public function pic_upload() {
		if ($_FILES['pic']['name'] || $_FILES['pic']['name']){
            $result = $this->upload("pic");
			echo $_POST['pic'];
        }
	}

	public function insert() {
		$shop_id = $_SESSION['shop_id'];

		$service_id = $_REQUEST['service_id'];
//		$add_service_name = $_REQUEST['add_service_name'];

		$pic = $_REQUEST['pic'];
		$pic_remark = $_REQUEST['pic_remark'];
		
		$servicefun_config = C("SERVICE_FUN");
		if($service_id) {
			foreach($service_id as $key=>$val) {
				if($val) {
					$servicefun_data = array();
					$servicefun_data['service_id'] = $val;
					$servicefun_data['service_name'] = $servicefun_config[$val];
					$servicefun_data['shop_id'] = $shop_id;
					$servicefun_data['create_time'] = time();
					$this->ServicefunModel->add($servicefun_data);
				}	
			}
		}
//		if($add_service_name) {
//			$servicefun_data = array();
//			$servicefun_data['service_id'] = 0;
//			$servicefun_data['service_name'] = $add_service_name;
//			$servicefun_data['shop_id'] = $shop_id;
//			$servicefun_data['create_time'] = time();
//			$this->ServicefunModel->add($servicefun_data);
//		}
		if($pic) {
			$pic_count = $this->ServicefunimgModel->where(array('shop_id'=>$shop_id,'status'=>1))->count();
			$gt_8 = $pic_count + count($pic);
			if($gt_8 >8) {
				$this->error("不能上传超过8张图片");
				exit();
			}
			foreach($pic as $key=>$val) {
				$servicefunimg_data = array();
				$servicefunimg_data['pic'] = $val;
				$servicefunimg_data['pic_remark'] = $pic_remark[$key];
				$servicefunimg_data['shop_id'] = $shop_id;
				$servicefunimg_data['create_time'] = time();
				$this->ServicefunimgModel->add($servicefunimg_data);
			}
		}
		$this->success("操作成功");
	}

	function  edit() {
		$shop_id = $_SESSION['shop_id'];

		$service_id = $_REQUEST['service_id'];
//		$servicefun_id = $_REQUEST['servicefun_id'];
//		$add_service_name = $_REQUEST['add_service_name'];
	
		$pic = $_REQUEST['pic'];
		$pic_remark = $_REQUEST['pic_remark'];
		
		$servicefun_config = C("SERVICE_FUN");
		if($service_id) {
			$this->ServicefunModel->where(array('shop_id'=>$shop_id,'service_id'=>array('neq',0)))->delete();
			foreach($service_id as $key=>$val) {
				if($val) {
					$servicefun_data = array();
					$servicefun_data['service_id'] = $val;
					$servicefun_data['service_name'] = $servicefun_config[$val];
					$servicefun_data['shop_id'] = $shop_id;
					$servicefun_data['create_time'] = time();
					$this->ServicefunModel->add($servicefun_data);
				}	
			}
		}else {
			$this->ServicefunModel->where(array('shop_id'=>$shop_id,'service_id'=>array('neq',0)))->delete();
		}
//		if($add_service_name) {
//			$servicefun_data = array();
//			$servicefun_data['service_id'] = 0;
//			$servicefun_data['service_name'] = $add_service_name;
//			$servicefun_data['shop_id'] = $shop_id;
//			$servicefun_data['create_time'] = time();
//			if($servicefun_id) {
//				$servicefun_data['id'] = $servicefun_id;
//				$this->ServicefunModel->save($servicefun_data);
//			}else {
//				$servicefun_data['id'] = $servicefun_id;
//				$this->ServicefunModel->add($servicefun_data);
//			}
//		}else {
//			$this->ServicefunModel->where(array('id'=>$servicefun_id))->delete();
//		}
		if($pic) {
			$pic_count = $this->ServicefunimgModel->where(array('shop_id'=>$shop_id,'status'=>1))->count();
			$gt_8 = $pic_count + count($pic);
			if($gt_8 >8) {
				$this->error("不能上传超过8张图片");
				exit();
			}
			foreach($pic as $key=>$val) {
				$servicefunimg_data = array();
				$servicefunimg_data['pic'] = $val;
				$servicefunimg_data['pic_remark'] = $pic_remark[$key];
				$servicefunimg_data['shop_id'] = $shop_id;
				$servicefunimg_data['create_time'] = time();
				$this->ServicefunimgModel->add($servicefunimg_data);
			}
		}
		$this->success("操作成功");
	}

	function edit_remark() {
		$id = $_REQUEST['id'];
		$pic_remark = $_REQUEST['pic_remark'];
		
		$data['id'] = $id;
		$data['pic_remark'] = $pic_remark;
		$this->ServicefunimgModel->save($data);
		echo "1";exit();
	}

	function delete() {
		$id = $_REQUEST['id'];
		$status = $_REQUEST['status'];
		
		$data['id'] = $id;
		$data['status'] = $status;
		$this->ServicefunimgModel->save($data);
		echo "1";exit();
	}

	function true_delete() {
		$id = $_REQUEST['id'];
		$files = $this->ServicefunimgModel->find($id);
		$path= 	C('UPLOAD_ROOT') . '/Service/';
		if(file_exists($path.$files['pic'])) {
			unlink($path.$files['pic']);
		}

		$this->ServicefunimgModel->where(array('id'=>$id))->delete();
		echo "1";exit();
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
        $upload->thumbPrefix = 'thumb550_,thumb300_';//550x412,300x225
        $resizeThumbSize_arr = explode(',', C('RESIZE_THUMB_SIZE'));
		$resizeThumbSize_arr = array('550,300','412,225');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];
        return $upload;
    }
    
}
?>