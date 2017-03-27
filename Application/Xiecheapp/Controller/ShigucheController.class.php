<?php
namespace Xiecheapp\Controller;

class ShigucheController extends CommonController {
    function __construct() {
		parent::__construct();
		$this->MembersalecouponModel = D('membersalecoupon');
		$this->InsuranceCompanyModel = D('insurancecompany');
		$this->assign('noshow',true);
		$this->assign('noclose',true);
	}


	/*
		@author:bright
		@function:事故车
		@time:2014-11-11
	*/
    public function index(){
    	layout(false);
		$this->assign('title',"事故车维修,汽车维修-携车网");
		$this->assign('meta_keyword',"汽车维修,事故车维修,汽车维修预约");
		$this->assign('description',"事故车维修首选携车网,在线预约,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822");
		$this->display('index_01');
   
	}

	public function upcaring(){
    	layout(false);
		$this->display('upcaring');
   
	}

	public function updringimg(){
    	layout(false);
		$this->display('updringimg');
   
	}

	public function order(){
		$name = $this->InsuranceCompanyModel->field('name')->select();
		$this->assign('name',$name);
		$this->assign('title',"事故车维修下单,汽车维修-携车网");
		$this->assign('meta_keyword',"汽车维修,事故车维修,汽车维修预约");
		$this->assign('description',"事故车维修首选携车网,在线预约,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822");
		$this->display('order');
   
	}
	public function repair_order(){
		$model_id = @$_SESSION['modelId'];
	    $model_serviceitem = D('Serviceitem');
	    $list_si_level_0 = $model_serviceitem->where("si_level=0")->select();	
		$list_si_level_1 = $model_serviceitem->where("si_level=1")->order('itemorder DESC')->select();
		$product_model = D('Product');
		foreach ($list_si_level_1 as &$val){
			//var_dump($val['id']);
			$where  = array(
					'model_id'=>$model_id,//546
					'service_id'=>$val['id']
			);
			$field = 'tp_xieche.xc_productversion.product_detail,tp_xieche.xc_product.id';
			$join = 'LEFT JOIN tp_xieche.xc_productversion ON tp_xieche.xc_product.versionid = tp_xieche.xc_productversion.id';
			$list = $product_model->field($field)->where($where)->join($join)->find();
			if ($list) {
				$product_detail = unserialize($list['product_detail']);
				$val['price'] = $product_detail[1]['price'];
				$val['midl_name'] = $product_detail[1]['Midl_name'];
				$val['detail1'] = $product_detail[0];
				$val['detail2'] = $product_detail[1];
			}
			unset($val);
		}
		$this->assign('list_si_level_0',$list_si_level_0);
		$this->assign('list_si_level_1',$list_si_level_1);
		
		$this->get_tuijian_coupon($this->request_int('shop_id'),$model_id);
		$this->assign('title',"故障维修下单,汽车维修-携车网");
		$this->assign('meta_keyword',"汽车维修,事故车维修,汽车维修预约");
		$this->assign('description',"事故车维修首选携车网,在线预约,海量车型任您选,汽车保养维修预约更有分时折扣,事故车维修预约,不花钱,还有返利拿4006602822");

		$this->display('repair_order');
   
	}
	public function create_order(){
		if (empty($_POST['user_phone'])) {
			$this->error("请输入电话号码",__APP__.'/shiguche/order',true);
		}
		if(!is_numeric($_POST['user_phone']) or strlen($_POST['user_phone'])!=11){
			$this->error('手机号码错误！');
		}
		if ($_POST['user_phone'] and !eregi("^1[0-9]{10}$",$_POST['user_phone'])){
			$this->error('手机号码格式错误！');
		}
		if (empty($_POST['loss_price'])) {
			$this->error("请输入定损金额",__APP__.'/shiguche/order',true);
		}
		if (empty($_POST['car_img'])) {
			$this->error("请添加车损照片",__APP__.'/shiguche/order',true);
		}
		if (empty($_POST['driving_img'])) {
			$this->error("请添加行驶证照片",__APP__.'/shiguche/order',true);
		}
		$uid = $this->GetUserId();
		if(!$uid){
			$member_model = D("Member");
			$memberinfo = $member_model->where(array('mobile' =>$_POST['user_phone']))->find();
			
			if($memberinfo) {
				$uid = $memberinfo['uid'];
			}else {
				$rand_verify = rand(10000, 99999);
			
				$member_data['mobile'] = $_POST['user_phone'];
				$member_data['password'] = md5($rand_verify);
				$member_data['reg_time'] = time();
				$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
				$uri = $_SERVER['REQUEST_URI'];
				$member_data['fromstatus'] = '35';
			
				$uid = $member_model->data($member_data)->add();
				$send_sms_data = array(
						'phones'=>$_POST['user_phone'],
						'content'=>'您已注册成功，您可以使用您的手机号码'.$_POST['user_phone'].'，密码'.$rand_verify.'来登录携车网，客服4006602822。',
				);
				$this->curl_sms($send_sms_data);
			
				$model_sms = D('Sms');
				$send_sms_data['sendtime'] = time();
				$model_sms->add($send_sms_data);
			}
		}
		
		$data['uid'] = $uid;
		$data['user_name'] = $_POST['user_name'];
		$data['user_phone'] = $_POST['user_phone'];
		$data['proxy_phone'] = $_POST['proxy_phone'];
		$data['fsid'] = 1;
		$data['brand_id'] = $_SESSION['brandId'];
		$data['series_id'] = $_SESSION['seriesId'];
		$data['model_id'] = $_SESSION['modelId'];
		if( $_POST['licenseplate'] ){
			$licenseplate_title = $_POST['licenseplate_title'];
			$licenseplate = $_POST['licenseplate'];
			$data['licenseplate'] = $licenseplate_title.$licenseplate;
		}
		
		$data['driving_img'] = $_POST['driving_img'];
		$data['insurance_name'] = $_POST['insurance_name'];
		$data['loss_price'] = $_POST['loss_price'];
		// $data['description'] = $_POST['description'];
		// $data['operator_remark'] = $_POST['operator_remark'];
		// $data['operator_name'] =$_SESSION['loginAdminUserName'];
		$data['description'] = '事故车';
		$data['operator_remark'] = '用户前台下单';
		$data['operator_name'] = '用户自己';
		$data['is_operator'] = 1;
		
		$data['insurance_status'] = 1;
		$data['create_time'] = time();
		$validity_time = 86400;//代下单 有效期 一小时 竞价延迟出现
		
		
		$data['validity_time'] = time()+$validity_time;
		
		$insurance_model = D("Insurance");
		$insurance_id = $insurance_model->add($data);
		//print_r($_SESSION);
		//echo $insurance_model->getLastsql();exit;
		//var_dump($insurance_model->getLastSql());exit;
		if ( $insurance_id ) {
			$insuranceimg_model = D("Insuranceimg");	//绑定carimg
			$data_img['insurance_id'] = $insurance_id;
			$data_img['car_img'] = $_POST['car_img'];
			$res = $insuranceimg_model->add($data_img);
			if (!$res) {
				$this->error($insuranceimg_model->getError(),true);
			}
			$this->success("信息提交成功，携车网客服会尽快与您联系,您也可以拨打4006602822咨询",true);
		}else{
			$this->success("信息提交失败",true);
		}
	}
	public function uploadDrvingImg(){
		if (empty($_FILES['upload']['tmp_name'])) {
			echo 1;
			exit;
		}
		
		import("Org.Net.UploadFile");
		$upload = new \UploadFile();
		$upload = $this->_upload_init($upload,'Driving');
		if (!$upload->upload()) {
			echo 1;
			exit;
		} else {
			$uploadList = $upload->getUploadFileInfo();
			$imgUrl = @$uploadList[0]['savename'];
			if ($imgUrl) {
				echo $imgUrl;
			}else{
				echo 1;
			}
		}
	}
	public function uploadCarImg(){
		if (empty($_FILES['upload']['tmp_name'])) {
			echo 1;
			exit;
		}
		
		import("Org.Net.UploadFile");
		$upload = new \UploadFile();
		$upload = $this->_upload_init($upload,'Driving');
		if (!$upload->upload()) {
			echo 1;
			exit;
		} else {
			$uploadList = $upload->getUploadFileInfo();
			$imgUrl = @$uploadList[0]['savename'];
			if ($imgUrl) {
				echo $imgUrl;
			}else{
				echo 1;
			}
		}
	}
	
	private function _upload_init($upload,$dir='') {
		//设置上传文件大小
		$upload->maxSize = C('UPLOAD_MAX_SIZE');
		//设置上传文件类型
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
		//设置附件上传目录
		
		if ($dir) {
			$upload->savePath = C('UPLOAD_ROOT2') . '/'.$dir.'/';;
		}else{
			$upload->savePath = C('UPLOAD_ROOT2') . '/';
		}
		$upload->saveRule = 'uniqid';
		//var_dump($upload->savePath);exit;
		$upload->thumb = true;
		$upload->thumbPrefix = 'thumb_';
		$resizeThumbSize_arr = array('60','45');
		$upload->thumbMaxWidth = $resizeThumbSize_arr[0];
		$upload->thumbMaxHeight = $resizeThumbSize_arr[1];
	
		$upload->uploadReplace = false;
		//$this->watermark = 1;水印
		return $upload;
	}


	/*
	 @author:bright
	@function:下订单
	@time:2014-11-10
	*/
	public function repair_create_order(){
		if (!$_SESSION['modelId']) {
			$this->error('请先选择车型',null,'/carservice/order');
		}
		$this->assign('jumpUrl', cookie('_currentUrl_'));
		
		if (empty($_POST['xcagreement'])){
			$this->error('请勾选同意携车网维修保养预约协议！');
		}
		if (empty($_POST['order_date'])){
			$this->error('请选择预约日期！');
		}
		// if (empty($_POST['order_hours']) || empty($_POST['order_minute'])){
		// 	$this->error('请选择预约时间！');
		// }
		if (empty($_POST['truename'])){
			$this->error('姓名不能为空！');
		}
		if (empty($_POST['mobile'])){
			$this->error('手机号不能为空！');
		}
		if (empty($_POST['licenseplate'])){
			$this->error('车牌号不能为空！');
		}
		$CityId = $this->GetCityId($_SESSION['area_info'][0]);//得到城市ID
		//根据提交过来的预约时间，做判断(暂时先注销)
		if($_POST['order_date']){
			//载入产品MODEL
			$model_product = D('Product');
			//$map['product_id'] = array('in',$_REQUEST['product_str']);
			if ($_REQUEST['select_services']){
				$select_services = implode(',',$_REQUEST['select_services']);
			}else {
				$select_services = '';
			}
			$uid = $this->GetUserId();
			$order_time= $_POST['order_date'];
			$order_time = strtotime($order_time);
	
			$now = time();
			$fourhour = strtotime(date('Y-m-d').' 16:00:00');
			if ($now < $fourhour){
				$min = 1;
				$max = 15;
			}else{
				$min = 2;
				$max = 16;
			}
			if($order_time > (time()+86400*$max) || $order_time < time()) {
				$this->error('最多预约15天以内,请重新选择！');
			}
			if(!$u_c_id = $_POST['u_c_id']){
				$u_c_id = 0;
			}
			//var_dump($order_time."-".(time()+86400*$max));exit;
			$save_discount = 0.00;
			$productversion_ids_str = '';
			if ($_REQUEST['membercoupon_id']){
				$membercoupon_id = $_REQUEST['membercoupon_id'];
				$model_membercoupon = D('Membercoupon');
				$map_mc['membercoupon_id'] = $_REQUEST['membercoupon_id'];
				$membercoupon = $model_membercoupon->where($map_mc)->find();
				$coupon_id = $membercoupon['coupon_id'];
				$model_coupon = D('Coupon');
				$map_c['id'] = $coupon_id;
				$coupon = $model_coupon->where($map_c)->find();
				$model_id = $coupon['model_id'];
				$select_services = $coupon['service_ids'];
				$shop_id = $coupon['shop_id'];
				$total_price = $coupon['coupon_amount'];
				$cost_price = $coupon['cost_price'];
				$jiesuan_money = $coupon['jiesuan_money'];
				$save_price = $cost_price - $total_price;
				$order_type = $coupon['coupon_type'];
				$data['order_name'] = $coupon['coupon_name'];
				$data['order_des'] = $coupon['coupon_summary'];
			}else{
				$order_type = 3;
				if ($select_services){
					$map['service_id'] = array('in',$select_services);
					$map['model_id'] = array('eq',$_REQUEST['model_id']);
					$list_product = $model_product->where($map)->select();
				}
	
				// $timesale_model = D('Timesale');
				// $map_tsv['xc_timesaleversion.id'] = $_POST['timesaleversion_id'];
				// $sale_arr = $timesale_model->where($map_tsv)->join("xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id")->find();
				// if ($order_time>$sale_arr['s_time'] and $order_time<$sale_arr['e_time']){
				// 	$order_week = date("w",$order_time);
				// 	$normal_week = explode(',',$sale_arr['week']);
				// 	if (!in_array($order_week,$normal_week)){
				// 		$this->error('预约时间错误,请重新选择！a');
				// 	}
				// 	$order_hour = date("H:i",$order_time);
				// 	if (strtotime(date('Y-m-d').' '.$order_hour)<strtotime(date('Y-m-d').' '.$sale_arr['begin_time']) || strtotime(date('Y-m-d').' '.$order_hour)>strtotime(date('Y-m-d').' '.$sale_arr['end_time'])){
				// 		$this->error('预约时间错误,请重新选择！b');
				// 	}
				// }else {
				// 	$this->error('预约时间错误,请重新选择！c'.$order_time.'-'.$sale_arr);
				// }
				//echo '<pre>';print_r($sale_arr);exit;
	
				//计算订单总价格
				$total_product_price = 0;
				$total_workhours_price = 0;
				$productversion_ids_arr = array();
				if (!empty($list_product)){
					foreach ($list_product as $kk=>$vv){
						$productversion_ids_arr[] = $vv['versionid'];
						$list_product[$kk]['list_detai'] = unserialize($vv['product_detail']);
						if (!empty($list_product[$kk]['list_detai'])){
							foreach ($list_product[$kk]['list_detai'] as $key=>$val){
								$list_product[$kk]['list_detai'][$key]['total'] = $val['price']*$val['quantity'];
								if ($val['Midl_name'] == '工时费'){
									$total_workhours_price += $list_product[$kk]['list_detai'][$key]['total'];
								}else {
									$total_product_price += $list_product[$kk]['list_detai'][$key]['total'];
								}
							}
						}
					}
				}
				$cost_price = $total_workhours_price+$total_product_price;
				$jiesuan_money = 0;
				$productversion_ids_str = implode(",",$productversion_ids_arr);
	
				$total_price = 0;
				$save_price = 0;
	
				if (!empty($sale_arr)){
					if ($sale_arr['product_sale']>0){
						$total_price += $total_product_price*$sale_arr['product_sale'];
					}else {
						$total_price += $total_product_price;
					}
					/*if ($sale_arr['workhours_sale']=='0.00'){
					 $sale_arr['workhours_sale'] = 1;
					}*/
					$workhours_sale = $sale_arr['workhours_sale'];
					if ($workhours_sale>0){
						$total_price += $total_workhours_price*$workhours_sale;
						$save_price = $total_workhours_price*($sale_arr['workhours_sale']-$workhours_sale);
						$save_discount = $sale_arr['workhours_sale']-$workhours_sale;
					}else{
						$total_price += $total_workhours_price*0;
						$save_price = $total_workhours_price;
						$save_discount = $sale_arr['workhours_sale'];
					}
				}else {
					$total_price += $total_product_price+$total_workhours_price;
				}
				$membercoupon_id = 0;
				$coupon_id = 0;
			}
			if ($sale_arr['product_sale']){
				$product_sale = $sale_arr['product_sale'];
			}else{
				$product_sale = 0;
			}
			if ($sale_arr['workhours_sale']){
				$workhours_sale = $sale_arr['workhours_sale'];
			}else{
				$workhours_sale = 0;
			}
			if ($_REQUEST['member_id']){
				$data['member_id'] = $_REQUEST['member_id'];
			}
			$data['u_c_id']=$u_c_id;
			$data['uid']=$uid;
			$data['shop_id']='';//TODO::暂时写空,没有店铺概念
			$data['model_id'] = $_SESSION['modelId'];
			$data['timesaleversion_id'] = '';
			$data['service_ids']=$select_services;
			$data['product_sale']=$product_sale;
			$data['workhours_sale']=$workhours_sale;
			$data['truename']=$_POST['truename'];
			$data['mobile']=$_POST['mobile'];
			$data['licenseplate']=trim($_POST['cardqz'].$_POST['licenseplate']);
			$data['mileage']=$_POST['miles'];
			$data['car_sn']=$_POST['car_sn'];
			$data['remark']=$_POST['remark'];
			$data['order_time']=$order_time;
			$data['create_time'] = time();
			$data['total_price']=$total_price;
			$data['cost_price']=$cost_price;
			$data['jiesuan_money']=$jiesuan_money;
			$data['productversion_ids']=$productversion_ids_str;
			$data['coupon_save_money']=$save_price;
			$data['coupon_save_discount']=$save_discount;
			$data['membercoupon_id']=$membercoupon_id;
			$data['coupon_id']=$coupon_id;
			$data['order_type']=$order_type;
			$data['city_id'] = $CityId;
				
			if($_REQUEST['ra_servicemember_id']){
				$data['servicemember_id'] = $_REQUEST['ra_servicemember_id'];
			}
			/*
			 @得到是否是百度过来的用户
			*/
			if($_COOKIE["Baidu_id"] > 0 ){
				$data['baidu_id'] = $_COOKIE["Baidu_id"];
				$data['baidu_ip'] = $_SERVER["REMOTE_ADDR"];
			}
			//@检查是否通过平安好车访问的用户
			if(TOP_CSS == 'pa'){
				$data['is_pa'] = '1';
			}
				
				
			$model = D('Order');
			if($uid){
				//抵用券选择[1]50元抵用券 [2]车会抵用券
				$radio_sale = $_POST['radio_sale'];
				$code = $_POST['code'];
				if($radio_sale || $code){
					$membersalecoupon_id = $this->add_salemembercoupon($uid,$radio_sale,$code,$data['shop_id']);
					$data['membersalecoupon_id'] = $membersalecoupon_id;
				}
				
// 				$model->add($data);
// 				var_dump($model->getLastSql());exit;
				if(false !== $model->add($data)){
					$_POST['order_id'] = $model->getLastInsID();
					$this->MembersalecouponModel->where(array('membersalecoupon_id'=>$membersalecoupon_id))->save(array('order_id'=>$_POST['order_id']));
				}
				$model_member = D('Member');
				$get_user_name = $model_member->where("uid=$uid")->find();
				if ($list_product){
					foreach($list_product AS $k=>$v){
						$sub_order[]=array(
								'order_id'=>$_POST['order_id'],
								'productversion_id'=>$list_product[$k]['versionid'],
								'service_id'=>$list_product[$k]['service_id'],
								'service_item_id'=>$list_product[$k]['service_item_id'],
								'uid'=>$uid,
								'user_name'=>$get_user_name['username'],
								'series_id'=>$_POST['series_id'],
								'create_time'=>time(),
								'update_time'=>time(),
						);
					}
					$model_suborder = D('Suborder');
					$list=$model_suborder->addAll($sub_order);
				}
			}else{
				$model = D('Ordernologin');
				if(false !== $model->add($data)){
					$_POST['order_id'] = $model->getLastInsID();
				}
			}
			if(!empty($_POST['order_id'])){
				if($uid) {
					$this->success('预约提交成功！',__APP__.'/myhome',true);
				}else {
					$this->success('预约提交成功！',__APP__.'/index',true);
				}
			}else {
				$this->error('预约失败！',__APP__.'/myhome',true);
			}
		}
	}
		
    
}