<?php
/*
 */
class InsuranceAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->InsuranceimgModel = D('insuranceimg');//用户保险竞价表
		$this->ShopModel = D('shop');//店铺表
		$this->shop_fs_relationModel = D('shop_fs_relation');//
		$this->FsModel = D('fs');//品牌表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
		$this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
		$this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
	}


	/*
		@author:chf
		@function:显示保险类订单页
		@time:2013-05-07
	*/
    public function index(){
		if($_REQUEST['user_name']){
			$data['user_name'] = $map['user_name'] = $_REQUEST['user_name'];
		}
		if($_REQUEST['user_phone']){
			$data['user_phone'] = $map['user_phone'] = $_REQUEST['user_phone'];
		}
		if($_REQUEST['insurance_status']){
			$data['insurance_status'] = $map['insurance_status'] = $_REQUEST['insurance_status'];
		}
		
		if($_REQUEST['insurance_name']){
			$data['insurance_name'] = $_REQUEST['insurance_name'];
			$map['insurance_name'] = array('LIKE',"%".$_REQUEST['insurance_name']."%"); 
		}
		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];
			$map['create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
		}
		$map['state'] = 1;
		
		// 计算总数
		$count = $this->InsuranceModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show_admin();
		$data['Insurance'] = $this->InsuranceModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		
		if($data['Insurance']){
			foreach($data['Insurance'] as $k=>$v){
				$fs = $this->FsModel->where(array('fsid'=>$v['fsid']))->find();
				$data['Insurance'][$k]['fsname'] = $fs['fsname'];
			}
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
		
    }

    	/*
		@author:ysh
		@function:修改用户询价页
		@time:2013-12-5
	*/
	public function add(){
		$model_shop = D("Shop");
		$model_fs = D("Fs");
		$model_shop_fs = D("Shop_fs_relation");
		
		//参与保险的店铺
		$shop_list = $model_shop->where(array('status'=>1))->select();
		if( $shop_list ) {
			foreach($shop_list as $key=>$val) {
				$shop_ids[] = $val['id'];
			}
		}

		$fs_map['shopid'] = array('in' , $shop_ids);
		$fsids = $model_shop_fs->where($fs_map)->group("fsid")->select();
		if( $fsids ) {
			foreach($fsids as $key=>$val) {
				$fsids_arr[] = $val['fsid'];
			}
		}

		$fs_info = $model_fs->where(array('fsid'=>array('in',$fsids_arr)))->select();
		
		$brand_list = $this->carbrand_model->select();
		$this->assign("brand_list", $brand_list);
		$model_insurancecompany = D("insurancecompany");

		$insurance_name = $model_insurancecompany->where()->select();

		$this->assign("insurance_name",$insurance_name);
		$this->assign("fs_info",$fs_info);
		$this->display();
	}

	/**
	 * 车型
	 */
	public function ajax_car_model(){
		$brand_id = intval($_POST['brand_id']);
		if( $brand_id ){
			$condition['brand_id'] = $brand_id;
			//$car_model_list = $this->car_model_model->where( $condition )->select();
			$car_model_list = $this->carseries_model->where( $condition )->select();
		}else{
			$car_model_list = "";
		}
		if( $car_model_list ){
			$return['errno'] = '0';
			$return['errmsg'] = 'success';
			$return['result'] = array('model_list' => $car_model_list );
		}else{
			$return['errno'] = '1';
			$return['errmsg'] = '该品牌下无录入车系';
		}
		$this->ajaxReturn( $return );
	}
	
	/**
	 * 车款
	 */
	public function ajax_car_style(){
		$model_id = intval( $_POST['model_id'] );
		if( $model_id ){
			//$condition['model_id'] = $model_id;
			//$car_style_list = $this->car_style_model->where( $condition )->select();
			$condition['series_id'] = $model_id;
			$car_style_list = $this->carmodel_model->where( $condition )->select();
		}else{
			$car_style_list = "";
		}
	
		if( $car_style_list ){
			$return['errno'] = '0';
			$return['errmsg'] = 'success';
			$return['result'] = array('style_list' => $car_style_list );
		}else{
			$return['errno'] = '1';
			$return['errmsg'] = '该车型下无录入车辆';
		}
		$this->ajaxReturn( $return );
	}



	/*
		@author:ysh
		@function:客服代下单
		@time:2014-1-7
	*/
	function insert(){
		if ($_FILES['driving_img']['name'] ||  $_FILES['car_img1']['name']){
			import("ORG.Net.UploadFile");
			$upload = new UploadFile();
			$upload = $this->_upload_init($upload);
			if (!$upload->upload()) {
				$this->error($upload->getErrorMsg());
			} else {
				$uploadList = $upload->getUploadFileInfo();
			}
			if($uploadList) {
				foreach($uploadList as $key=>$val) {
					switch ($val['key']) {
						case 0:
							$car_imgs['car_img1'] = $val['savename'];
							break;
						case 1:
							$car_imgs['car_img2'] = $val['savename'];
							break;
						case 2:
							$car_imgs['car_img3'] = $val['savename'];
							break;
						case 3:
							$car_imgs['car_img4'] = $val['savename'];
							break;
						case 4:
							$driving_img = $val['savename'];
							break;
					}

				}
			}
		}

		$member_model = D("Member");
		$memberinfo = $member_model->where(array('mobile' =>$_REQUEST['user_phone']))->find();
		if($memberinfo) {
			$uid = $memberinfo['uid'];
		}else {
			$rand_verify = rand(10000, 99999);

			$member_data['mobile'] = $_REQUEST['user_phone'];
			$member_data['password'] = md5($rand_verify);
			$member_data['reg_time'] = time();
			$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
			$uri = $_SERVER['REQUEST_URI'];
			$member_data['fromstatus'] = '35';

			$uid = $member_model->data($member_data)->add();
			$send_sms_data = array(
				'phones'=>$_REQUEST['user_phone'],
				'content'=>'您已注册成功，您可以使用您的手机号码'.$_REQUEST['user_phone'].'，密码'.$rand_verify.'来登录携车网，客服4006602822。',
			);
			$this->curl_sms($send_sms_data);
		
			$model_sms = D('Sms');
			$send_sms_data['sendtime'] = time();
			$model_sms->add($send_sms_data);
		}
		

		$data['uid'] = $uid;
		$data['user_name'] = $_REQUEST['user_name'];
		$data['user_phone'] = $_REQUEST['user_phone'];
		$data['proxy_phone'] = $_REQUEST['proxy_phone'];
		$data['fsid'] = $_REQUEST['fsid'];
		$data['brand_id'] = $_REQUEST['brand_id'];
		$data['series_id'] = $_REQUEST['series_id'];
		$data['model_id'] = $_REQUEST['model_id'];
		if( $_REQUEST['licenseplate'] ){
			$licenseplate_title = $_REQUEST['licenseplate_title'];
			$licenseplate = $_REQUEST['licenseplate'];
			$data['licenseplate'] = $licenseplate_title.$licenseplate;
		}

		$data['driving_img'] = $driving_img;
		$data['insurance_name'] = $_REQUEST['insurance_name'];
		if ($_REQUEST['is_loss'] = 1) {
			$data['loss_price'] = $_REQUEST['loss_price'];
		}else{
			$data['loss_price'] = 0;
		}
		$data['description'] = $_REQUEST['description'];
		$data['operator_remark'] = $_REQUEST['operator_remark'];
		$data['operator_name'] =$_SESSION['loginAdminUserName'];
		$data['is_operator'] = 1;

		$data['insurance_status'] = $_REQUEST['insurance_status'];
		$data['create_time'] = time();
		$validity_time = 86400;//代下单 有效期 一小时 竞价延迟出现
		$data['validity_time'] = time()+$validity_time;

		$insurance_model = D("Insurance");
		if (false === $insurance_model->create($data)) {
			$this->error($insurance_model->getError());
		}
		$insurance_id = $insurance_model->add();

		$insuranceimg_model = D("Insuranceimg");
		$data_img['insurance_id'] = $insurance_id;

		if($car_imgs) {
			foreach($car_imgs as $key=>$val) {
				$data_img['car_img'] = $val;
				$i = substr($key , -1);
				$data_img['car_location'] = $i;
				if (false === $insuranceimg_model->create($data_img)) {
					$this->error($insuranceimg_model->getError());
				}
				$list=$insuranceimg_model->add ($data_img);
			}
		}

		$this->success("操作成功！",U('/Store/Insurance/index'));
	}

	function get_brand_id(){
		$model_brand = D("Carbrand");
		$model_series = D("Carseries");

		$series_map['fsid'] = $_REQUEST['fsid'];
		$series_info = $model_series->where($series_map)->find();
		$brand_info = $model_brand->find($series_info['brand_id']);
		
		echo $brand_info['brand_id'];exit();
	}
	

	/*
		@author:ysh
		@function:修改用户询价页
		@time:2013-12-5
	*/
    public function edit(){
		if($_REQUEST['id']){
			$id = $_REQUEST['id'];
		}
		$data = $this->InsuranceModel->find($id);
		
		if($data){
			$fs = $this->FsModel->where(array('fsid'=>$data['fsid']))->find();
			$data['fsname'] = $fs['fsname'];
			$licenseplate_str = $data['licenseplate'];
			$licenseplate_title = mb_substr($licenseplate_str, 0,-6);
			$licenseplate = mb_substr($licenseplate_str,-6);
			$this->assign('licenseplate',$licenseplate);
			$this->assign('licenseplate_title',$licenseplate_title);

			$map_img['insurance_id'] = $id;
			$data['insurance_img'] = $this->InsuranceimgModel->where($map_img)->select();
			if( mb_strpos($data['insurance_img'][0]['car_img'],',')>0){
				$data['car_imgs'] = explode(',',$data['insurance_img'][0]['car_img']);
			}
		}

		$model_brand = D("Carbrand");
		$model_series = D("Carseries");

		$series_map['fsid'] = $data['fsid'];
		$series_info = $model_series->where($series_map)->find();
		$brand_info = $model_brand->find($series_info['brand_id']);

		$this->assign('brand_id',$brand_info['brand_id']);
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
    }

	/*
		@author:ysh
		@function:修改用户询价页do
		@time:2013-12-6
	*/
	public function edit_do() {
		if($_REQUEST['id']){
			$id = $_REQUEST['id'];
		}
		$insurance_info = $this->InsuranceModel->find($id);

		$data['id'] = $id;
		$data['brand_id'] = $_REQUEST['brand_id'];
		$data['series_id'] = $_REQUEST['series_id'];
		$data['model_id'] = $_REQUEST['model_id'];
		$data['loss_price'] = $_REQUEST['loss_price'];
		$data['is_operator'] = $_REQUEST['is_operator'];//将订单改为客服代下单模式---->客服操作选择4s店
		if($_REQUEST['is_operator'] == 1) {
			$data['validity_time'] = time()+$validity_time;//客服代下单模式 有效期改成1一天
		}
		$data['operator_remark'] = $_REQUEST['operator_remark'];//携车网客服描述--给商家看的
		$data['insurance_status'] = $_REQUEST['insurance_status'];
		if( $_REQUEST['licenseplate'] ){
			$licenseplate_title = $_REQUEST['licenseplate_title'];
			$licenseplate = $_REQUEST['licenseplate'];
			$licenseplate_str = $licenseplate_title.$licenseplate;
		}

		$data['licenseplate'] = $licenseplate_str;
		

		$bidorder_log_model = D("Bidorder_log");
		$log_data['name'] = $_SESSION['loginAdminUserName'];
		$log_data['insurance_id'] = $id;
		$log_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$log_data['create_time'] = time();
		
		if($insurance_info['insurance_status'] != $_REQUEST['insurance_status'] ) {
			$INSURANCE_STATUS = C("insurance_status");
			$log_data['content'] = "竞价状态 ".$INSURANCE_STATUS[$insurance_info['insurance_status']] ." 修改为 ".$INSURANCE_STATUS[$_REQUEST['insurance_status']];
			$bidorder_log_model->add($log_data);
		}
		if($insurance_info['licenseplate'] != $data['licenseplate'] ) {
			$log_data['content'] = "车牌号 ".$insurance_info['licenseplate'] ." 修改为 ".$data['licenseplate'];
			$bidorder_log_model->add($log_data);
		}
		if($insurance_info['loss_price'] != $_REQUEST['loss_price'] ) {
			$log_data['content'] = "定损金额 ".$insurance_info['loss_price'] ." 修改为 ".$_REQUEST['loss_price'];
			$bidorder_log_model->add($log_data);
		}
		if($insurance_info['is_operator'] != $_REQUEST['is_operator'] ) {
			$log_data['content'] = "客服代下单模式 ".$insurance_info['is_operator'] ." 修改为 ".$_REQUEST['is_operator'];
			$bidorder_log_model->add($log_data);
		}
		
		//客服为用户备注事故描述 修改状态为竞价中 发布4s店自动竞价
		if($insurance_info['insurance_status'] == '0' && $data['insurance_status'] == '1'&& $data['is_operator'] == '0') {
			$fs_shop_model = D("shop_fs_relation");
			$map_fs['fsid'] = $insurance_info['fsid'];
			$shop_ids = $fs_shop_model->where($map_fs)->select();
			if($shop_ids) {
				foreach($shop_ids as $key=>$val) {
					$shop_ids_arr[] = $val['shopid'];
				}
			}
			
			/*给4s店事故专员发送短信---------------START*/
			$shop_map['id'] = array('in' , $shop_ids_arr);
			$shop_map['safestate'] = 1;
			$shop_map['shop_class'] = 1;
			$shop_map['status'] = 1;
			$shopinfos = $this->ShopModel->where($shop_map)->select();
			if ($shopinfos) {
				foreach ($shopinfos as $key => $val) {
					if($val['shop_salemobile']) {
						$send_sms_data = array(
							'phones'=>$val['shop_salemobile'],
							'content'=>$_REQUEST['sale_sms_content'],
						);
						$this->curl_sms($send_sms_data);

						$model_sms = D('Sms');
						$send_sms_data['sendtime'] = time();
						$model_sms->add($send_sms_data);
					}
				}
			}
			//给4s店事故专员发送短信---------------END
			
			$shopsaleconfig_model = D("Shopsaleconfig");

			$map_shopsaleconfig['auto'] = 1;
			$map_shopsaleconfig['state'] = 1;
			$map_shopsaleconfig['shop_id'] = array('in' , $shop_ids_arr);
			$shopsaleconfig = $shopsaleconfig_model->where($map_shopsaleconfig)->select();
		
			if($shopsaleconfig) {
				$shopbidding_model = D("Shopbidding");

				foreach($shopsaleconfig as $key=>$val) {
					$shop_bidding_data = array();
					$shop_bidding_data['shop_id'] = $val['shop_id'];
					$shop_bidding_data['servicing_time'] = $this->service_time_rand($_REQUEST['loss_price']);
					$shop_bidding_data['scooter'] = $val['scooter'];
					$shop_bidding_data['rebate'] = $val['rebate'];
					$shop_bidding_data['remark'] = $val['remark'];
					$shop_bidding_data['insurance_id'] = $id;
					$shop_bidding_data['create_time'] = time();//时间

					$shopbidding_model->add($shop_bidding_data);
				}
			}
			$validity_time = 60*15;//有效期 15分钟+3分钟竞价延迟出现
			$data['validity_time'] = time()+$validity_time;
		}

		$this->InsuranceModel->save($data);
		$this->success("修改成功！");
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
		$shop_model = D("Shop");
		$map['status'] = 1;
		//$map['safestate'] = 1;
		$Shop = $shop_model->where($map)->select();
		if($Shop){
			echo json_encode($Shop);
		}else{
			echo 'none';
			
		}
	}

	/*
		@author:ysh
		@function:客服代下单指定4s店
		@time:2014-1-7
	*/
	function add_shopbidding(){
		$insurance_id = $_REQUEST['id'];
		$insurance_model = D("Insurance");
		$insurance = $insurance_model->find($insurance_id);
		
		
		if($_REQUEST['shopbidding_id']) {
			$shopbidding_id = $_REQUEST['shopbidding_id'];
			$shopbidding_model = D("Shopbidding");
			$shopbidding_info = $shopbidding_model->find($shopbidding_id);
			$this->assign("shopbidding_info",$shopbidding_info);
			
			$shop_ids_arr = $shopbidding_info['shop_id'];

			$this->assign("shop_id",$shopbidding_info['shop_id']);
		}else {
			$fs_shop_model = D("shop_fs_relation");
			$map_fs['fsid'] = $insurance['fsid'];
			$shop_ids = $fs_shop_model->where($map_fs)->select();
			if($shop_ids) {
				foreach($shop_ids as $key=>$val) {
					$shop_ids_arr[] = $val['shopid'];
				}
			}
		}



		$shop_model = D("Shop");
		$map['status'] = 1;
		//$map['safestate'] = 1;
		$map['id'] = array('in',$shop_ids_arr);
		$shoplist = $shop_model->where($map)->select();
		//print_r($shoplist);

		$this->assign("shoplist",$shoplist);
		$this->assign("insurance_id",$insurance_id);
		$this->assign("insurance",$insurance);
		$this->display();
	}

	/*
		@author:ysh
		@function:客服代下单指定4s店提交
		@time:2014-1-9
	*/
	function insert_shopbidding(){
		$insurance_id = $_REQUEST['insurance_id'];

		$shopbidding_model = D("Shopbidding");
		$shop_bidding_data = array();
		$shop_bidding_data['shop_id'] = $_REQUEST['shop_id'];
		$shop_bidding_data['servicing_time'] = $_REQUEST['servicing_time'];
		$shop_bidding_data['scooter'] = empty($_REQUEST['scooter'])?0:$_REQUEST['scooter'];
		$shop_bidding_data['rebate'] = $_REQUEST['rebate'];
		$shop_bidding_data['insurance_id'] = $insurance_id;
		$shop_bidding_data['create_time'] = time();//时间

		$shopbidding_id = $shopbidding_model->add($shop_bidding_data);
		$insurance_model = D("Insurance");
		$insurance_info = $insurance_model->find($insurance_id);
		//插入订单

		$bidorder_model = D("Bidorder");
		$data['insurance_id'] = $insurance_id;
		$data['shop_id'] = $_REQUEST['shop_id'];
		$data['uid'] = $insurance_info['uid'];
		$data['truename'] = $insurance_info['user_name'];
		$data['mobile'] = $insurance_info['user_phone'];
		$data['licenseplate'] = $insurance_info['licenseplate'];
		$data['bid_id'] = $shopbidding_id;
		//$data['fav_id'] = $fav_id;
		$data['order_status'] = 1;//代下单 ---订单直接为已确认
		$data['status'] = 1;
		$data['create_time'] = time();
		$data['tostore_time'] = strtotime($_REQUEST['tostore_time']);
		//$data['takecar_time'] = $_REQUEST['tostore_time']+($shop_bidding_data['servicing_time']*86400);
		$shopinfos = $this->ShopModel->where($shop_map)->find($_REQUEST['shop_id']);
		if($shopinfos['insurance_rebate']>0){
			$data['insurance_rebate'] = $shopinfos['insurance_rebate'];
		}else{
			$data['insurance_rebate'] = '5';
		}
		$data['customer_id'] = $_SESSION['authId'];
		$bidorder_id = $bidorder_model->add($data);
//echo $bidorder_model->getLastsql();exit;

		//修改insurance_status
		$insurance_data['id'] = $insurance_id;
		$insurance_data['insurance_status'] = 3;
		$insurance_model->save($insurance_data);


		/*给4s店事故专员发送短信---------------*/

		if ($shopinfos['shop_salemobile']) {
			$send_sms_data = array(
				'phones'=>$shopinfos['shop_salemobile'],
				'content'=>$_REQUEST['sale_sms_content'],
			);
			$this->curl_sms($send_sms_data,'',1);

			$model_sms = D('Sms');
			$send_sms_data['sendtime'] = time();
			$model_sms->add($send_sms_data);
		}

		//给用户发送短信---------------
		$send_sms_data = array(
			'phones'=>$insurance_info['user_phone'],
			'content'=>$_REQUEST['member_sms_content'],
		);
		$this->curl_sms($send_sms_data,'',1);

		$model_sms = D('Sms');
		$send_sms_data['sendtime'] = time();
		$model_sms->add($send_sms_data);

		$this->success("代下单成功",U('/Store/Bidorder/index'));
	}


	/*
		@author:chf
		@function:显示4s店
		@time:2013-05-07
	*/
	function showbid(){
		$map['insurance_id'] = $_REQUEST['id'];
		$map['status'] = 1;
		// 计算总数
		$count = $this->ShopbiddingModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show_admin();
		$data = $this->ShopbiddingModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		if($data){
			foreach($data as $k=>$v){
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data[$k]['shop_name'] = $shop['shop_name'];
			}
		}

		$insurance_info = $this->InsuranceModel->find($_REQUEST['id']);

		$this->assign('insurance_info',$insurance_info);
		$this->assign('data',$data);
		$this->assign('page',$page);

		$this->display();
	}

	/*
		@author:ysh
		@function:保险订单维修天数随即
		@time:2013-11-14 
	*/
	function service_time_rand($loss_price) {
		if($loss_price <= 2000) {
			return rand(4, 5)."天";
		}
		if($loss_price > 2000 && $loss_price <= 5000 ) {
			return rand(6, 7)."天";
		}
		if($loss_price > 5000) {
			return rand(9, 10)."天";
		}
	}

		/*
		@author:ysh
		@function:图片上传的初始化
		@time:2013/6/22
	*/
	public function _upload_init($upload) {
		//设置上传文件大小
		$upload->maxSize = C('UPLOAD_MAX_SIZE');
		//设置上传文件类型
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
		//设置附件上传目录
		$upload->savePath = C('UPLOAD_ROOT') . '/Driving/';
		$upload->saveRule = 'uniqid';

		$upload->thumb = true;
		$upload->thumbPrefix = 'thumb_';
		$resizeThumbSize_arr = array('60','45');
		$upload->thumbMaxWidth = $resizeThumbSize_arr[0];
		$upload->thumbMaxHeight = $resizeThumbSize_arr[1];

		$upload->uploadReplace = false;
		//$this->watermark = 1;水印
		return $upload;
	}

	function insurance_log() {
		$insurance_id = $_REQUEST['id'];
		$bidorder_log_model = D("Bidorder_log");
		$map['insurance_id'] = $insurance_id;
		$log_list = $bidorder_log_model->where($map)->select();
		$this->assign('log_list',$log_list);
		$this->display();
	}
    
}