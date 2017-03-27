<?php
namespace Xiecheapp\Controller;

class AppnewController extends CommonController {
	
	public function __construct() {
		parent::__construct();
		$this->IpcountModel = D('ipcount');//记录访问IP
		$this->RegionModel = D('region');//区域
		$this->ShopModel = D('shop');//商铺
		$this->CouponModel = D('coupon');//优惠券
		$this->TimesaleModel = D('timesale');//工时折扣表
		$this->TimesaleversionModel = D('timesaleversion');//工时折扣详情表
		$this->CarbrandModel = D('carbrand');//
		$this->BaiduModel = D('baidu');//百度表
		$this->membercar_model = D('Membercar');
		$this->carmodel_model = D('carmodel');
		$this->carseries_model = D('carseries');  //车型号
		$this->carbrand_model = D('carbrand');//
		$this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter','xc_');  //保养项目
        $this->filter_model = M('tp_xieche.item_filter','xc_');  //保养项目
        $this->reservation_order_model = M('tp_xieche.reservation_order', 'xc_');	//保养订单
		$this->user_model = M('tp_xieche.member', 'xc_');//用户表
		$this->memberlog_model = D('Memberlog');  //用户日志表
		$this->sms_model = D('Sms');  //短信表
		$this->carservicecode_model = M('tp_xieche.carservicecode','xc_');//上门保养抵用码字段
		$this->MembersalecouponModel = D('membersalecoupon');  //立减券表
		$this->MembercouponModel = D('membercoupon');         //用户的俄优惠券
		$this->CommentModel = D('comment');         //用户的4s评论表
		$this->Reservation_commentModel = D('reserveorder_comment');         //用户的上门评论表
		$this->technician_model = D('technician');  //技师表
		$this->InsuranceCompanyModel = D('insurancecompany'); //保险公司表

		$this->accesskey = 'fqcd67763332fqcd';
		
		if(ACTION_NAME !='getAccessKey'){	//验证key
			$this->_validAccessKey();	
		}
	}
	
	protected function _validAccessKey(){
		if ($this->accesskey != $_REQUEST['accesskey']) {
			$this->_ret(null,0,'error key');
		}
	}
	
	//公共返回方法
	protected function _ret($data=NULL, $status = 1, $msg='success'){
		$ret = array(
				'status' => $status,
				'msg'	=> $msg,
				'data'	=> $data
		);
		echo json_encode($ret);//特殊要返回放括号数组的情况
	}
	protected function _ret_c($data=NULL, $status = 1, $msg='success'){
		$ret = '{"status":'.$status.',"msg":"'.$msg.'","data":'.$data.'}';
		echo $ret;//特殊要返回放括号数组的情况
		exit;
	}

	//获取全局key
	public function getAccessKey(){
		$key = 'fqcd67763332fqcd';
		$this->_ret($key);	
	}
	//保险公司的接口
	function get_insurance_Company(){
		$name = $this->InsuranceCompanyModel->field('name')->order('id asc')->select();
		foreach( $name as $k=>$v){
			foreach( $v as $k1=>$v1) {
				$data[] = $v1;
			}
		}
		$this->_ret($data,1,'success');
	}
	//创建免费上门检测订单
	function buy_check_order(){
		$uid = $this->app_get_userid();
		//$uid = $_REQUEST['uid'];
		$truename = $_REQUEST['truename'];
		$phone = $_REQUEST['phone'];
		$remark = $_REQUEST['remark'];
		$address = $_REQUEST['address'];
		$data['uid'] = $uid;
		$data['truename'] = $truename;
		$data['mobile'] = $phone;
		$data['remark'] = $remark;
		$data['address'] = $address;
		$data['order_type'] =2;
		$data['status'] =0;
		$data['create_time'] =time();
		$data['origin'] =8; //新版app
		//$data['pay_type'] =0;   默认为0
		$order_id = $this->reservation_order_model->add($data);
		if($order_id){
			$this->_ret(null,1,'success');
		}else{
			$this->_ret(null,0,'fail');
		}
	}
	//获取用户基本信息
	function get_user_info(){
		$uid = $this->app_get_userid();
//		$uid = $_REQUEST['uid'];
		$map['uid'] = $uid;
		//$map['status'] = 1;
		$member_info = $this->reservation_order_model->where($map)->field('uid,truename,mobile,licenseplate,address')->order('id desc')->find();
		if(!$member_info){
			$member_info  = $this->user_model->where($map)->field('uid,username as truename,mobile,prov,city,area')->find();
			if(!$member_info['truename']) $member_info['truename']=null;
			if($member_info['area'] =='NULL') {
				$member_info['area'] = '';
				$address = $member_info['prov'] . $member_info['city'] . $member_info['area'];
				$member_info['address'] = $address?$address:null;
				$member_info['licenseplate'] = null;
			}
			unset($member_info['prov'],$member_info['city'],$member_info['area']);
		}
		if($member_info){
			$this->_ret($member_info,1,'success');
		}else{
			$this->_ret(null,0,'fail');
		}
	}
//事故车的上传接口
	public function create_accident_order(){
		//$uid = $this->app_get_userid();
		$uid = $_REQUEST['uid'];
		if (empty($_POST['user_phone'])) {
			$this->_ret(null,0,"请输入电话号码");exit;
		}
		if(!is_numeric($_POST['user_phone']) or strlen($_POST['user_phone'])!=11){
			$this->_ret(null,0,'手机号码错误！');exit;
		}
		if ($_POST['user_phone'] and !eregi("^1[0-9]{10}$",$_POST['user_phone'])){
			$this->_ret(null,0,'手机号码格式错误！');exit;
		}
		if (empty($_POST['loss_price'])) {
			$this->_ret(null,0,"请输入定损金额");exit;
		}
		if (empty($_POST['car_img'])) {
			$this->_ret(null,0,"请添加车损照片");exit;
		}
		if (empty($_POST['driving_img'])) {
			$this->_ret(null,0,"请添加行驶证照片");exit;
		}
		if(!$uid){
			$member_model = D("Member");
			$member_info = $member_model->where(array('mobile' =>$_POST['user_phone']))->find();

			if($member_info) {
				$uid = $member_info['uid'];
			}else {
				$rand_verify = rand(10000, 99999);
				$member_data['mobile'] = $_POST['user_phone'];
				$member_data['password'] = md5($rand_verify);
				$member_data['reg_time'] = time();
				$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
				$member_data['fromstatus'] = '10';

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
		$data['brand_id'] = $_REQUEST['brandId'];
		$data['series_id'] = $_REQUEST['seriesId'];
		$data['model_id'] = $_REQUEST['modelId'];
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
		$data['operator_remark'] = 'app下单';
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
			$insuranceimg_model = D("Insuranceimg");   //绑定carimg
			$data_img['insurance_id'] = $insurance_id;
			$data_img['car_img'] = $_POST['car_img'];
			$res = $insuranceimg_model->add($data_img);
			if (!$res) {
				$this->_ret(null,0,"添加失败");
			}
			$this->_ret($res,1,"信息提交成功，携车网客服会尽快与您联系,您也可以拨打4006602822咨询");
		}else{
			$this->_ret(null,0,"信息提交失败");
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////
	//上传事故车的图片
	 /////////////////////////////////////////////////////////////////////////////////////////
	public function uploadDrvingImg(){
		header("Content-type:text/html;charset=utf-8");
		import("Org.Net.UploadFile");

		$upload = new \UploadFile();
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg','png','gif','jpeg');// 设置附件上传类型
		$upload->savePath =  'UPLOADS/Driving/';// 设置附件上传目录
		$upload->saveRule ='uniqid';
		//$upload = $this->_upload_init($upload,'Driving');
		if (!$upload->upload()) {
			$errOr_info = $upload->getErrorMsg();
			$this->_ret(null,0,$errOr_info);exit;
		} else {
			$uploadList = $upload->getUploadFileInfo();
			$imgUrl =@$uploadList[0]['savename'];
			if ($imgUrl) {
				$this->_ret($imgUrl,1,'success');exit;
			}else{
				$this->_ret(null,0,'返回文件失败');exit;
			}
		}
	}
	public function uploadCarImg(){
		header("Content-type:text/html;charset=utf-8");
		import("Org.Net.UploadFile");
		$upload = new \UploadFile();
		$upload->maxSize  = 3145728 ;// 设置附件上传大小
		$upload->allowExts  = array('jpg','png','gif','jpeg');// 设置附件上传类型
		$upload->savePath =  'UPLOADS/Driving/';// 设置附件上传目录
		$upload->saveRule ='uniqid';
		//$upload = $this->_upload_init($upload,'Caring');
		$info = $upload->upload();
		if (!$info) {
			$errOr_info = $upload->getErrorMsg();
			$this->_ret(null,0,$errOr_info);exit;
		} else {
			$uploadList = $upload->getUploadFileInfo();
			$imgUrl = @$uploadList[0]['savename'];
			if ($imgUrl) {
				$this->_ret($imgUrl,1,'success');exit;
			}else{
				$this->_ret(null,0,'返回文件失败');exit;
			}
		}
	}
	//维修报告及备注！
	function get_user_check_faults(){
		$uid = $this->app_get_userid();
		$map['uid'] =$uid; //25775;//33545 ;//
		$map['status'] = 9;
		$info = $this->reservation_order_model->where($map)-> order('id desc ,order_time desc')->find();
		// $arr_code = array('正常', '干净', '<3%', '>3.4mm', '轻微磨损', '>50%', '>3.5mm', '良好');
		$arr_code1 = array('偏低', '脏污', '偏低', '3%~4%', '2.7~3.4mm', '较重磨损', '2.5~3.5mm','偏高', '很脏', '轮胎损伤', '>4%', '<2.7mm', '严重磨损', '<50%', '<2.5mm', '较差', '有异常', '故障灯亮', '不工作');
		//$arr_code2 = array('偏高', '很脏', '轮胎损伤', '>4%', '<2.7mm', '严重磨损', '<50%', '<2.5mm', '较差', '有异常', '故障灯亮', '不工作');
		/////////////////////////////////////////////////////////
		$mCheckReport = D('checkreport_total');
		$checkitem = D('checkitem');
		$check_remarks = D('check_remarks');
		/////////////////////////////////////////////////////////
		if($info){
			$where = array('reservation_id'=>$info['id']);
			$res = $mCheckReport->where($where)->order('create_time desc')->limit(1)->find();
			if($res) {
				$data = unserialize($res['data']);
				if($data){
					//数据处理
					foreach ($data as $key => $val) {
						if(in_array($val, $arr_code1)) {
							$map_id['item_name'] = $key;
							$info_id = $checkitem->where($map_id)->find();
							$id = $info_id['id'];
							$map_w['order_id'] = $info['id'];
							$remark_info = $check_remarks->where($map_w)->find();
							if($info_id){
								$remarks = $info_id['suggestion'] ;
							}else{
								$remarks=null;
							}
							if (!$remark_info) {
								$arr1 = null;
							} else {
								if ($remark_info['image_url']) {
									if (mb_strpos($remark_info['image_url'], ',') > 0) {
										$arr = explode(',', $remark_info['image_url']);
										if ($arr) {
											foreach ($arr as $k => $v) {
												$arr_id = explode('_',$v);
												if($arr_id[1] == $id) {
													$arr1[] = WEB_ROOT.'/UPLOADS/Checkremarks/img/' . $v;
												}
											}
										}
									} else {
										$arr_id = explode('_',$remark_info['image_url']);
										if($arr_id[1] ==$id){
											$arr1=array(WEB_ROOT.'/UPLOADS/Checkremarks/img/'.$remark_info['image_url']);
										}else{
											$arr1=null;
										}
									}
								} else {
									$arr1 = null;
								}
							}
							$problem[] = array('check_item_id' => $id, 'item_name' => $key, 'checked_result' => $val, "text_remark" => $remarks, "images" => $arr1);
						}
						if($arr1){
							unset($arr1);
						}
					}
				}
			}else{
				$this->_ret(null,0,'暂无检测报告'); exit;
			}
			$map_c['order_id'] = $info['id'];
			$remarks_info = $check_remarks->where($map_c)->find();
			if ($remarks_info['remarks']) {
				$arr_remark = json_decode($remarks_info['remarks'], true);
				foreach( $arr_remark as $kk =>$vv){
					if(!$vv){
						unset( $arr_remark[$kk]);
					}
				}
				$remark = $arr_remark;
			}else{
				$remark = null;
			}
			$array_list =array(
				'order_id' => $res['reservation_id'],
				'check_date'=> $res['create_time'] ,
				'fault_check_items'=> $problem,   //检测的问题项！！
				'remark'=> $remark
			);
			$this->_ret($array_list,1,'success');

		}else{
			$this->_ret(null,0,'暂无检测报告');
		}
	}

	//查看检测报告项的备注
	function get_check_report_remark(){
		$this->app_get_userid();
		$order_id = $_REQUEST['order_id'];
		$check_item_id = $_REQUEST['check_item_id'];
		if($order_id && $check_item_id) {
			$check_remarks = D('check_remarks');
			$checkitem = D('checkitem');
			$map['id'] =$check_item_id;
			$item_info =  $checkitem->where($map)->find();
			if(!$item_info){
				$this->_ret(null, 0, '检查项id不存在');exit;
			}
			$map2['order_id'] = $order_id;
			$remark_info = $check_remarks->where($map2)->find();
			if(!$remark_info){
				$this->_ret(null, 0, '订单id不存在');exit;
			}
			if($remark_info['image_url']){
				if(mb_strpos($remark_info['image_url'],',')>0){
					$arr = explode(',',$remark_info['image_url']);
					if($arr){
						foreach($arr as $k => $v ){
							$arr_id = explode('_',$v);
							if($arr_id[1] == $check_item_id) {
								$arr1[] = WEB_ROOT.'/UPLOADS/Checkremarks/img/' . $v;
							}
						}
					}
				} else{
						$arr_id = explode('_',$remark_info['image_url']);
						if($arr_id[1] ==$check_item_id){
							$arr1=array(WEB_ROOT.'/UPLOADS/Checkremarks/img/'.$remark_info['image_url']);
						}else{
							$arr1=null;
						}
				}
			} else{
				$arr1=null;
			}
			if($remark_info['remarks']){
				$arr_remark = json_decode($remark_info['remarks'],true);
				foreach($arr_remark as $k1=>$v1){
					$arr_key[] = $k1;
				}
				if( in_array($item_info['item_name'],$arr_key)){
					$remark_text = $arr_remark[$item_info['item_name']];
				}else{
					$remark_text = null;
				}
			}
			$remark_data = array(
				"check_item_id" => $item_info['id'],
				"check_item_name" => $item_info['item_name'],
				"order_id" => $remark_info['order_id'],
				"text_remark" => $remark_text,
				"images" => $arr1
			);
			$this->_ret($remark_data, 1, 'sucess');
		}else{
			$this->_ret(null, 0, '参数不能为空');
		}
	}
	//用户检测报告列表的接口
	function user_check_report_list(){
		//用户登录！
		$uid = $this->app_get_userid();
		//$uid = 23635;//27269;
		$map['uid'] = $uid;
		$map['status'] = 9;
		$info = $this->reservation_order_model->where($map)-> order('id desc ,order_time desc')->select();
		$arr_code = array('正常', '干净', '<3%', '>3.4mm', '轻微磨损', '>50%', '>3.5mm', '良好');
		$arr_code1 = array('偏低', '脏污', '偏低', '3%~4%', '2.7~3.4mm', '较重磨损', '2.5~3.5mm');
		$arr_code2 = array('偏高', '很脏', '轮胎损伤', '>4%', '<2.7mm', '严重磨损', '<50%', '<2.5mm', '较差', '有异常', '故障灯亮', '不工作');
		/////////////////////////////////////////////////////////
		$mCheckReport = D('checkreport_total');
		$brand = D('carbrand');
		/////////////////////////////////////////////////////////
		if($info){
			foreach($info as $k =>$v){
				if($v['id']){
					$where = array('reservation_id'=>$v['id']);
					$res = $mCheckReport->where($where)->order('create_time desc')->limit(1)->find();
					if($res) {
						$data = unserialize($res['data']);
						if($data){
							$car_map = array('brand_name'=>$data['car_brand']);
							$car_info =  $brand->where($car_map)->find();
							if($car_info){
								$logo = WEB_ROOT.'/UPLOADS/Brand/Logo/'.$car_info['brand_logo'];
							}else{
								$logo = null;
							}
							$code = 0;
							//数据处理
							if ($res['create_time'] < 1419321491) {
								$baoyang = array('机油液位', '空气滤芯', '空调滤芯', '雨刮水液面', '左前胎胎压', '右前胎胎压', '左后胎胎压', '右后胎胎压', '防冻液液面', '防冻液冰点值', '制动液含水量');
								$yishun = array('前刹车片厚度', '后刹车片厚度', '前刹车盘厚度', '后刹车盘厚度', '蓄电池健康度', '前胎花纹深度', '后胎花纹深度', '雨刮片', '灯光照明');
								$dipan = array("球头","转向拉杆","平衡杆","连接杆","避震","上下摆臂","半轴");
								$anquan = array("安全带", "手制动器", "发动机舱管线", "电瓶指示灯", "机油指示灯", "水温指示灯", "ABS指示灯", "发动机指示灯", "安全带指示灯", "刹车指示灯", "气囊指示灯");
								//$this->assign('old', true);
							} else {
								$baoyang = array('机油液位', '空气滤芯', '空调滤芯', '雨刮水液面', '左前胎胎压', '右前胎胎压', '左后胎胎压', '右后胎胎压', '防冻液液面', '防冻液冰点值', '制动液含水量');
								$yishun = array('前刹车片厚度', '后刹车片厚度', '前刹车盘厚度', '后刹车盘厚度', '蓄电池健康度', '前胎花纹深度', '后胎花纹深度', '雨刮片', '灯光照明', '点烟器');
								$dipan = array("球头","转向拉杆","变速箱","油底壳螺丝","避震器","上下摆臂","半轴");
								$anquan = array("安全带", "手制动器", "发动机舱管线", "气门室盖垫", "方向机油壶", "机油指示灯", "ABS指示灯", "发动机指示灯", "刹车指示灯", "气囊指示灯");
							}
							foreach ($data as $key => $val) {
								if (in_array($key, $baoyang)) {
									if (in_array($val, $arr_code)) {
										$code += 2.63;
									} elseif(mb_strpos($key,'防冻液冰点值')===0){
										if(mb_strpos($val,'负')===0) {
											$code += 2.63;
										}
									}elseif(in_array($val, $arr_code1)){
										$problem[] = array('item_name'=>$key,'checked_result'=> $val);
									}elseif(in_array($val, $arr_code2)){
										$problem[] = array('item_name'=>$key,'checked_result'=> $val);
									}
									continue;
								} elseif (in_array($key, $yishun)) {
									if (in_array($val, $arr_code)) {
										$code += 2.63;
									} elseif(mb_strpos($key,'灯光照明')===0){
										if($val == null){
											$code += 2.63;
										}
									} elseif(in_array($val, $arr_code1)){
										$problem[] = array('item_name'=>$key,'checked_result'=> $val);
									}elseif(in_array($val, $arr_code2)){
										$problem[] = array('item_name'=>$key,'checked_result'=> $val);
									}
									continue;
								}elseif (in_array($key, $dipan)) {
									if (in_array($val, $arr_code)) {
										$code += 2.63;
									}elseif(in_array($val, $arr_code1)){
										$problem[] = array('item_name'=>$key,'checked_result'=> $val);
									}elseif(in_array($val, $arr_code2)){
										$problem[] = array('item_name'=>$key,'checked_result'=> $val);
									}
									continue;
								}elseif (in_array($key, $anquan)) {
									if (in_array($val, $arr_code)) {
										$code += 2.63;
									} elseif(in_array($val, $arr_code1)){
										$problem[] = array('item_name'=>$key,'checked_result'=> $val);
									}elseif(in_array($val, $arr_code2)){
										$problem[] = array('item_name'=>$key,'checked_result'=> $val);
									}
									continue;
								}
							}
							if($code){
								if($code>=99){
									$code=100;
								}else{
									$code = intval($code);
								}
							}
						}
					}
				}
				$array_list[] =array(
					'check_report_name' =>"38项车辆检测报告",
					'id' => $res['id'],
					'reservation_id' => $res['reservation_id'],
					'car_name' => $data['car_brand'] . $data['car_model'],
					'millage' => $data['mileage'],
					'car_logo' => $logo,
					'rating'=> $code,
					'check_date'=> $res['create_time'] ,
					'check_problem'=> $problem   //检测的问题项！！
				);
				if($problem){
					unset($problem);
				}
			}
			$this->_ret($array_list,1,'全部检测报告！');
		}else{
			$this->_ret(null,0,'您暂无订单！');
		}
	}

	/*
	@author:bright
	@function:上门保养检查报告
	@time:2014-08-30
	*/
	function user_last_check_report()
	{
		//用户登录！判断手机用户登录
		 $uid = $this->app_get_userid();
		//$uid = 20146;//32925;//32707;
		//////////////////////////////////////////////////////////////////////////////////////////
		//计算数量和分数
		$code = 0;
		$code1 = 0;
		$arr_code = array('正常', '干净', '<3%', '>3.4mm', '轻微磨损', '>50%', '>3.5mm', '良好');
		$arr_code1 = array('偏低', '脏污', '偏低', '3%~4%', '2.7~3.4mm', '较重磨损', '2.5~3.5mm');
		$arr_code2 = array('偏高', '很脏', '轮胎损伤', '>4%', '<2.7mm', '严重磨损', '<50%', '<2.5mm', '较差', '有异常', '故障灯亮', '不工作');
		///////////////////////////////////////////////////////////////////////////////////////////
		$mCheckReport = D('checkreport_total');
		$mCheckItem = D('checkitem');
		/////////////////////////////////////////////////////////
		$map1['uid'] = $uid;
		$map1['status'] = 9;
		$info = $this->reservation_order_model->where($map1)->order('id desc,order_time desc')->select();
		if($info){
			$where = 'reservation_id='.$info[0]['id'];
			if($info[1]['id'] ){
				$where = 'reservation_id in('.$info[0]['id'].','.$info[1]['id'].')';
			}else{
				$previous_data =null;
			}
			if($_REQUEST['reservation_id']){
				if( $info) {
					foreach($info as $k=>$v){
						$arr_id[] = $v['id'];
					}
				}
				$id1 = $_REQUEST['reservation_id'];
				if( in_array($id1,$arr_id)){
					foreach( $arr_id as $k2=>$v2){
						if($id1 ==$v2 ){
							if($arr_id[$k2+1]) {
								$id[] = $arr_id[$k2 + 1];
							}
						}
					}
					$id[] = $id1;
				}
				if($id[1]){
					$where = 'reservation_id in('.$id[1].','.$id[0].')';
				} else{
					$where = 'reservation_id ='.$id[0];
				}
			}
		} else{
			$this->_ret(null, 0, '您暂时没有检测报告!');exit;
		}

		$res = $mCheckReport->where($where)->order('create_time desc')->limit(2)->select();
		//echo $mCheckReport->getLastSql();
		$res2 = $res[0]['reservation_id'];
		$model = array();
		$model3 = array();
		if ($res) {
			$data = unserialize($res[0]['data']);
			$data2  = unserialize($res[1]['data']);
			if($data2){
				//数据处理
				if ($res[1]['create_time'] < 1419321491) {
					$baoyang = array('机油液位', '空气滤芯', '空调滤芯', '雨刮水液面', '左前胎胎压', '右前胎胎压', '左后胎胎压', '右后胎胎压', '防冻液液面', '防冻液冰点值', '制动液含水量');
					$yishun = array('前刹车片厚度', '后刹车片厚度', '前刹车盘厚度', '后刹车盘厚度', '蓄电池健康度', '前胎花纹深度', '后胎花纹深度', '雨刮片', '灯光照明');
					$dipan = array("球头","转向拉杆","平衡杆","连接杆","避震","上下摆臂","半轴");
					$anquan = array("安全带", "手制动器", "发动机舱管线", "电瓶指示灯", "机油指示灯", "水温指示灯", "ABS指示灯", "发动机指示灯", "安全带指示灯", "刹车指示灯", "气囊指示灯");
					//$this->assign('old', true);
				} else {
					$baoyang = array('机油液位', '空气滤芯', '空调滤芯', '雨刮水液面', '左前胎胎压', '右前胎胎压', '左后胎胎压', '右后胎胎压', '防冻液液面', '防冻液冰点值', '制动液含水量');
					$yishun = array('前刹车片厚度', '后刹车片厚度', '前刹车盘厚度', '后刹车盘厚度', '蓄电池健康度', '前胎花纹深度', '后胎花纹深度', '雨刮片', '灯光照明', '点烟器');
					$dipan = array("球头","转向拉杆","变速箱","油底壳螺丝","避震器","上下摆臂","半轴");
					$anquan = array("安全带", "手制动器", "发动机舱管线", "气门室盖垫", "方向机油壶", "机油指示灯", "ABS指示灯", "发动机指示灯", "刹车指示灯", "气囊指示灯");
				}
				$baoyangArr2 = $yishunArr2 = $dipanArr2 = $anquanArr2 = array();
				foreach ($data2 as $key => $val) {
					if (in_array($key, $baoyang)) {
						$map['item_name'] = $key;
						$item_info = $mCheckItem->where($map)->find();
						$id = $item_info['id'];
						if (in_array($val, $arr_code)) {
							$code1 += 2.63;
							$result_level = 0;
						} elseif (in_array($val, $arr_code1)) {
							$result_level = 1;
						} elseif (in_array($val, $arr_code2)) {
							$result_level = 2;
						}elseif(mb_strpos($key,'防冻液冰点值')===0){
							if(mb_strpos($val,'负')===0) {
								$code1 += 2.63;
								$result_level = 0;
							}else{
								$result_level = 2;
							}
						}
						$baoyangArr2[] = array('id' => $id,'name' => $key, 'checked_result' => $val, 'result_level' => $result_level);
						continue;
					} elseif (in_array($key, $yishun)) {
						$map['item_name'] = $key;
						$item_info = $mCheckItem->where($map)->find();
						$id = $item_info['id'];
						if (in_array($val, $arr_code)) {
							$code1 += 2.63;
							$result_level = 0;
						} elseif (in_array($val, $arr_code1)) {
							$result_level = 1;
						} elseif (in_array($val, $arr_code2)) {
							$result_level = 2;
						} elseif(mb_strpos($key,'灯光照明')===0){
							if($val == null){
								$code1 += 2.63;
								$result_level = 0;
							} else{
								$result_level =2;
							}
						}
						$yishunArr2[] = array('id' => $id,'name' => $key, 'checked_result' => $val, 'result_level' => $result_level);
						continue;
					}elseif (in_array($key, $dipan)) {
						$map['item_name'] = $key;
						$item_info = $mCheckItem->where($map)->find();
						$id = $item_info['id'];
						if (in_array($val, $arr_code)) {
							$code1 += 2.63;
							$result_level = 0;
						} elseif (in_array($val, $arr_code1)) {
							$result_level = 1;
						} elseif (in_array($val, $arr_code2)) {
							$result_level = 2;
						}
						$dipanArr2[] = array('id' => $id, 'name' => $key, 'checked_result' => $val, 'result_level' => $result_level);
						continue;
					}elseif (in_array($key, $anquan)) {
						$map['item_name'] = $key;
						$item_info = $mCheckItem->where($map)->find();
						$id = $item_info['id'];
						if (in_array($val, $arr_code)) {
							$code1 += 2.63;
							$result_level = 0;
						} elseif (in_array($val, $arr_code1)) {
							$result_level = 1;
						} elseif (in_array($val, $arr_code2)) {
							$result_level = 2;
						}
						$anquanArr2[] = array('id' => $id,'name' => $key, 'checked_result' => $val, 'result_level' => $result_level);
						continue;
					}
				}
					$model3[0]['category_name'] = '保养类';
					$model3[0]['check_items'] = $baoyangArr2;
					$model3[1]['category_name'] = '易损类';
					$model3[1]['check_items'] = $yishunArr2;
					$model3[2]['category_name'] = '底盘类';
					$model3[2]['check_items'] = $dipanArr2;
					$model3[3]['category_name'] = '安全类';
					$model3[3]['check_items'] = $anquanArr2;
				if($code1){
					if($code1>=99){
						$code1=100;
					}else{
						$code1 = intval($code1);
					}
				}
				$previous_data=array(
					'id' => $res[1]['id'],
					'reservation_id' => $res[1]['reservation_id'],
					'rating'=> $code1,
                    'check_date'=> $res[1]['create_time'],
					'check_item_categories'=> $model3
				);
			}
			if ($data) {
				//数据处理
				if ($res[0]['create_time'] < 1419321491) {
					$baoyang = array('机油液位', '空气滤芯', '空调滤芯', '雨刮水液面', '左前胎胎压', '右前胎胎压', '左后胎胎压', '右后胎胎压', '防冻液液面', '防冻液冰点值', '制动液含水量');
					$yishun = array('前刹车片厚度', '后刹车片厚度', '前刹车盘厚度', '后刹车盘厚度', '蓄电池健康度', '前胎花纹深度', '后胎花纹深度', '雨刮片', '灯光照明');
					$dipan = array("球头","转向拉杆","平衡杆","连接杆","避震","上下摆臂","半轴");
					$anquan = array("安全带", "手制动器", "发动机舱管线", "电瓶指示灯", "机油指示灯", "水温指示灯", "ABS指示灯", "发动机指示灯", "安全带指示灯", "刹车指示灯", "气囊指示灯");
					//$this->assign('old', true);
				} else {
					$baoyang = array('机油液位', '空气滤芯', '空调滤芯', '雨刮水液面', '左前胎胎压', '右前胎胎压', '左后胎胎压', '右后胎胎压', '防冻液液面', '防冻液冰点值', '制动液含水量');
					$yishun = array('前刹车片厚度', '后刹车片厚度', '前刹车盘厚度', '后刹车盘厚度', '蓄电池健康度', '前胎花纹深度', '后胎花纹深度', '雨刮片', '灯光照明', '点烟器');
					$dipan = array("球头","转向拉杆","变速箱","油底壳螺丝","避震器","上下摆臂","半轴");
					$anquan = array("安全带", "手制动器", "发动机舱管线", "气门室盖垫", "方向机油壶", "机油指示灯", "ABS指示灯", "发动机指示灯", "刹车指示灯", "气囊指示灯");
				}

				$baoyangArr = $yishunArr = $dipanArr = $anquanArr = array();
				foreach ($data as $key => $val) {
					if (in_array($key, $baoyang)) {
						$map['item_name'] = $key;
						$item_info = $mCheckItem->where($map)->find();
						$id = $item_info['id'];
						if (in_array($val, $arr_code)) {
							$code += 2.63;
							$result_level = 0;
						} elseif (in_array($val, $arr_code1)) {
							$result_level = 1;
						} elseif (in_array($val, $arr_code2)) {
							$result_level = 2;
						}elseif(mb_strpos($key,'防冻液冰点值')===0){
							if(mb_strpos($val,'负')===0) {
								$code += 2.63;
								$result_level = 0;
							}else{
								$result_level = 2;
							}
						}
						$baoyangArr[] = array('id' => $id,'name' => $key, 'checked_result' => $val, 'result_level' => $result_level);
						continue;
					} elseif (in_array($key, $yishun)) {
						$map['item_name'] = $key;
						$item_info = $mCheckItem->where($map)->find();
						$id = $item_info['id'];
						if (in_array($val, $arr_code)) {
							$code += 2.63;
							$result_level = 0;
						} elseif (in_array($val, $arr_code1)) {
							$result_level = 1;
						} elseif (in_array($val, $arr_code2)) {
							$result_level = 2;
						} elseif(mb_strpos($key,'灯光照明')===0){
							if($val == null){
								$code += 2.63;
								$result_level = 0;
							} else{
								$result_level =2;
							}
						}
						$yishunArr[] = array('id' => $id,'name' => $key, 'checked_result' => $val, 'result_level' => $result_level);
						continue;
					}elseif (in_array($key, $dipan)) {
						$map['item_name'] = $key;
						$item_info = $mCheckItem->where($map)->find();
						$id = $item_info['id'];
						if (in_array($val, $arr_code)) {
							$code += 2.63;
							$result_level = 0;
						} elseif (in_array($val, $arr_code1)) {
							$result_level = 1;
						} elseif (in_array($val, $arr_code2)) {
							$result_level = 2;
						}
						$dipanArr[] = array('id' => $id, 'name' => $key, 'checked_result' => $val, 'result_level' => $result_level);
						continue;
					}elseif (in_array($key, $anquan)) {
						$map['item_name'] = $key;
						$item_info = $mCheckItem->where($map)->find();
						$id = $item_info['id'];
						if (in_array($val, $arr_code)) {
							$code += 2.63;
							$result_level = 0;
						} elseif (in_array($val, $arr_code1)) {
							$result_level = 1;
						} elseif (in_array($val, $arr_code2)) {
							$result_level = 2;
						}
						$anquanArr[] = array('id' => $id,'name' => $key, 'checked_result' => $val, 'result_level' => $result_level);
						continue;
					}
				}
				$model[0]['category_name'] = '保养类';
				$model[0]['check_items'] = $baoyangArr;
				$model[1]['category_name'] = '易损类';
				$model[1]['check_items'] = $yishunArr;
				$model[2]['category_name'] = '底盘类';
				$model[2]['check_items'] = $dipanArr;
				$model[3]['category_name'] = '安全类';
				$model[3]['check_items'] = $anquanArr;


			} else {
				$this->_ret(null, 0, '您暂时没有检测报告');
				exit;
			}
			$check_remarks = D("check_remarks");
			$remarkdata['order_id'] = $res2;
			//$remarkdata['order_id'] = 5178;
			$res_remark = $check_remarks->where($remarkdata)->order('create_time desc')->select();
			if($res_remark) {

				$remarkcon = $res_remark[0]['remarks'];

				$remarkcon = json_decode($remarkcon, true);
				foreach ($remarkcon as $k => $v) {
					if (!$v) {
						unset($remarkcon[$k]);
					}
				}
			}else{
				$remarkcon = null;
			}
			if($code){
				if($code>=99){
					$code=100;
				}else{
					$code = intval($code);
				}
			}
			$model2 = array(
				'id' => $res[0]['id'],
				'reservation_id' => $res[0]['reservation_id'],
				'mobile' => $res[0]['mobile'],
				'check_date' => $res[0]['create_time'],
				'car_name' => $data['car_brand'] . $data['car_model'],
				'millage' => $data['mileage'],
				'rating' =>$code,
				'check_item_categories' => $model,
				'previous_check_report_data' => $previous_data,
				'check_remark' => $remarkcon
			);
			$this->_ret($model2, 1, '检测报告');
			// $this->_ret($remarkcon,1,'检测备注');
		} else{
			$this->_ret(null, 0, '您暂时没有检测报告！');
		}
	}

	//个人中心所有订单显示
	function get_user_all_order(){
		$list1 = $this->get_user_home_maintenance();
		$list2 = $this->get_user_4S_maintenance();
		$list3 = $this->get_user_4S_repair();
		$list4 = $this->get_user_accident_car();

		$data = array(
			'home_maintenance'=> $list1,
			'4S_maintenance'=> $list2,
			'4S_repair' =>$list3,
			'accident_car' =>$list4
		);
		if($list1||$list2||$list3||$list4){
			$this->_ret($data,1,'success');
		}else{
			$this->_ret(null,0,'暂无订单！');

		}
	}
	// 用户事故车订单
	public function get_user_accident_car(){
		$uid = $this->app_get_userid();
//		$uid = 24054;
		if (@$_REQUEST['detail']) {
			$id = @$_REQUEST['id'];
			$map['id'] =$id;
		}
		$map['uid'] = $uid;   //6591;// $uid;
		$insurance_model = D("Insurance");
		$insuranceimg_model = D("Insuranceimg");
		$bidorder_model = D("Bidorder");
		$shop_model = D("Shop");
		$car_model = D("carmodel");
		$order_status_arr = C("BIDORDER_STATE");
		$order_status_arr2 = C("insurance_status");

		$pay_state = C("PAY_STATE");

		$list = $insurance_model->where($map)->order('id DESC')->select();
		if($list) {
			foreach ($list as $key => $val) {
				$list[$key]['driving_img'] = "http://xieche.com.cn/UPLOADS/Driving/".$val['driving_img'];
				$list[$key]['order_id'] = $this->get_orderid($val['id']);//给用户看的order id
				$list[$key]['order_status'] = $val['insurance_status'];
				$list[$key]['order_status_name'] = $order_status_arr2[$val['insurance_status']];
				$list[$key]['pay_status'] = $pay_state[$val['pay_status']];
				$car_model_info  = $car_model->where(array('model_id' => $val['model_id']))->find();
				if($car_model_info){
					$list[$key]['model_name'] = $car_model_info['model_name'];
				}
				$bidorder_info = $bidorder_model->where(array('insurance_id' => $val['id']))->find();
				if ($bidorder_info) {
					$list[$key]['bid_order_id'] = $this->get_orderid($bidorder_info['id']);//给用户看的order id
					$list[$key]['order_status'] = $bidorder_info['order_status'];
					$list[$key]['order_status_name'] = $order_status_arr[$bidorder_info['order_status']];
					$shop_info = $shop_model->find($bidorder_info['shop_id']);
					$list[$key]['shop_name'] = $shop_info['shop_name'];
					$list[$key]['tostore_time'] =$shop_info['tostore_time'];
				}
				$insuranceimg_info = $insuranceimg_model->where(array('insurance_id' => $val['id']))->find();
				if($insuranceimg_info){
					if(mb_strpos($insuranceimg_info['car_img'],',')>0){
						$img = explode(',',$insuranceimg_info['car_img']);
						foreach($img as $k=>$v){
							$img[$k] = "http://xieche.com.cn//UPLOADS/Driving/".$v;
						}
						$list[$key]['car_img'] = $img;
					}else{
						$list[$key]['car_img'][] = "http://xieche.com.cn/UPLOADS/Driving/".$insuranceimg_info['car_img'];
					}
				}
			}
			if (@$_REQUEST['detail']) {
				$this->_ret($list, 1, "事故车订单");
			} else {
				return $list;
			}
			//          $this->_ret($list,1,"事故车订单");
		}else{
			if (@$_REQUEST['detail']) {
				$this->_ret(null,0,"暂无事故车订单");
			} else {
				return null;
			}

//            $this->_ret(null,0,"暂无事故车订单");
		}
	}
	//4s保养订单
	public function get_user_4S_maintenance()
	{
		$uid = $this->app_get_userid();
		if (@$_REQUEST['detail']) {
			$id = @$_REQUEST['id'];
			$map_order['id'] =$id;//$id;
		}
//		$uid = 6591;
		$map_order['uid'] =$uid;//6591; //$uid; //$uid;
		$model_order = D('Order');
		$map_order['status'] = 1;
		// $onemonth_time = time()-date('t')*24*3600;
		$map_order['order_type'] = 4;//4s店订单
		//$map_order['order_type'] = 3;//故障订单
		// 当前页数据查询
		$list = $model_order->where($map_order)->order('id DESC')->select();
		//echo $model_order->getlastSql();
		$model_shop = D('Shop');
		$service_item = D('serviceitem');
		$model_shop_fs_relation = D ( 'Shop_fs_relation' );
		if ($list) {
			foreach ($list as $k => $v) {
				if($v['shop_id']){
					$shop_model = D ( 'Shop' );
					$map_shop['id'] =$v['shop_id'];
					$shops = $shop_model->where( $map_shop )->find();
					$logo =  $shops['logo'] ;
					$fsid = $this->getfsid ( $shops ['id'] );
					$list [$k] ['brand_logo'] = "http://www.xieche.com.cn/UPLOADS/Brand/200/" . $fsid . ".jpg";
					// 店铺品牌
					if ($logo) {
						if ($shops ['shop_class'] == 1) {
							$list [$k] ['shop_logo'] = "http://www.xieche.com.cn/UPLOADS/Shop/200/" . $shops ['id'] . ".jpg";
						} else {
							$fsid = $this->getfsid ( $shops ['id'] );
							$list [$k] ['shop_logo'] = "http://www.xieche.com.cn/UPLOADS/Brand/200/" . $fsid . ".jpg";
						}
					} else {
						if (file_exists ( "http://www.xieche.com.cn/UPLOADS/Shop/200/" . $shops ['id'] . ".jpg" )) {
							$list [$k] ['shop_logo'] = "http://www.xieche.com.cn/UPLOADS/Shop/200/" . $shops ['id'] . ".jpg";
						} else {
							$fsid = $this->getfsid ( $shops ['id'] );
							$list [$k] ['shop_logo'] = "http://www.xieche.com.cn/UPLOADS/Brand/200/" . $fsid . ".jpg";
						}
					}
				}
					if ($v['service_ids']) {
						$id_str = $v['service_ids'];
						$map ['id'] = array(
							'in',
							$id_str
						);
					}
					$item_info = $service_item->where($map)->select();
					//echo  $service_item->getLastSql();
					foreach ($item_info as $k1 => $v1) {
						if ($v1['service_item_id'] == 1) {
							$item_list['0']['name'] = '常规保养';
							$item_list['0']['item']['name'][] = $v1['name'];
						}
						if ($v1['service_item_id'] == 2) {
							$item_list['1']['name'] = '常规替换及维修';
							$item_list['1']['item']['name'][] = $v1['name'];
						}
						if ($v1['service_item_id'] == 3) {
							$item_list['2']['name'] = '清洗项目';
							$item_list['2']['item']['name'][] = $v1['name'];
						}
						if ($v1['service_item_id'] == 4) {
							$item_list['3']['name'] = '有故障';
						}
					}
					$list[$k]['services'] = $item_list;
					$list[$k]["order_state"] = $this->get_4s_StatusName($v["order_state"]);
					$list[$k]["order_type"] = '4S店预约保养';
					$shop_id = $v['shop_id'];
					$map_s['id'] = $shop_id;
					$shopinfo = $model_shop->where($map_s)->find();
					$list[$k]['shop_name'] = $shopinfo['shop_name'];
					$list[$k]['order_id'] = $this->get_orderid($v['id']);
					/*$map_c['shop_id'] = $shop_id;
					$map_c['order_id'] = $this->get_orderid($v['id']);
					$map_c['uid'] = $uid;
					$isComment = $this->CommentModel->where($map_c)->find();
					if(!$isComment){
						$list[$k]['isComment'] = 1;
					}*/
					unset($item_list);
			}
			if (@$_REQUEST['detail']) {
				$this->_ret($list, 1, "4s店订单");
			}else {
				return $list;
			}
//            $this->_ret($list, 1, "4s店订单");
		} else {
			if (@$_REQUEST['detail']) {
				$this->_ret(null, 0, "暂无4s店订单");
			}else{
				return null;
			}
//            $this->_ret(null, 0, "暂无4s店订单");
		}
	}
	//关联id
	public function getfsid($shop_id) {
		$model_shop_fs_relation = D ( 'Shop_fs_relation' );
		$map_shopfs ['shopid'] = $shop_id;
		$shop_fs_relation = $model_shop_fs_relation->where ( $map_shopfs )->select ();
		if ($shop_fs_relation) {
			$model_carseries = D ( 'Carseries' );
			foreach ( $shop_fs_relation as $k => $v ) {
				if ($v ['fsid']) {
					return $v ['fsid'];
				}
			}
		}
	}
	//4s订单
	public function get_user_4S_repair(){
		$uid = $this->app_get_userid();
		if (@$_REQUEST['detail']) {
			$id = @$_REQUEST['id'];
			$map_order['id'] = $id;
		}
	//$uid = 6591;
		$map_order['uid'] =$uid; //$uid;
		$model_order = D('Order');
		$map_order['status'] = 1;
		// $onemonth_time = time()-date('t')*24*3600;
//        $map_order['order_type'] = 4;//4s店订单
		$map_order['order_type'] = 3;//故障订单
		// 当前页数据查询
		$list = $model_order->where($map_order)->order('id DESC')->select();
		// echo $a = $model_order->getlastSql();
		$model_shop = D('Shop');

		if ($list){
			foreach ($list as $k=>$v){
				if($v['shop_id']){
					$shop_model = D ( 'Shop' );
					$map_shop['id'] =$v['shop_id'];
					$shops = $shop_model->where( $map_shop )->find();
					$logo =  $shops['logo'] ;
					$fsid = $this->getfsid ( $shops ['id'] );
					$list [$k] ['brand_logo'] = "http://www.xieche.com.cn/UPLOADS/Brand/200/" . $fsid . ".jpg";
					// 店铺品牌
					if ($logo) {
						if ($shops ['shop_class'] == 1) {
							$list [$k] ['shop_logo'] = "http://www.xieche.com.cn/UPLOADS/Shop/200/" . $shops ['id'] . ".jpg";
						} else {
							$fsid = $this->getfsid ( $shops ['id'] );
							$list [$k] ['shop_logo'] = "http://www.xieche.com.cn/UPLOADS/Brand/200/" . $fsid . ".jpg";
						}
					} else {
						if (file_exists ( "http://www.xieche.com.cn/UPLOADS/Shop/200/" . $shops ['id'] . ".jpg" )) {
							$list [$k] ['shop_logo'] = "http://www.xieche.com.cn/UPLOADS/Shop/200/" . $shops ['id'] . ".jpg";
						} else {
							$fsid = $this->getfsid ( $shops ['id'] );
							$list [$k] ['shop_logo'] = "http://www.xieche.com.cn/UPLOADS/Brand/200/" . $fsid . ".jpg";
						}
					}
				}
				$list[$k]["order_state"] = $this->get_4s_StatusName($v["order_state"]);
				$list[$k]["order_type"]='4S店维修';
				$shop_id = $v['shop_id'];
				$map_s['id'] = $shop_id;
				$shopinfo = $model_shop->where($map_s)->find();
				$list[$k]['shop_name'] = $shopinfo['shop_name'];
				$list[$k]['order_id'] = $this->get_orderid($v['id']);
				/*$map_c['shop_id'] = $shop_id;
                $map_c['order_id'] = $this->get_orderid($v['id']);
                $map_c['uid'] = $uid;
                $isComment = $this->CommentModel->where($map_c)->find();
                if(!$isComment){
                    $list[$k]['isComment'] = 1;
                }*/
			}
			if (@$_REQUEST['detail']) {
				$this->_ret($list,1,"4s店维修订单");
			}else{
				return $list;
			}
//            $this->_ret($list,1,"4s店维修订单");
		} else{
			if (@$_REQUEST['detail']) {
				$this->_ret(null,0,"暂无4s店维修订单");
			}else{
				return null;
			}

//            $this->_ret(null,0,"暂无4s店维修订单");
		}
	}
	//单个4s店的优惠券详情
	function shop_coupon_detail(){
		$map['id'] = $_REQUEST['coupon_id'];
		$mcoupon = D('coupon');
		$model_shop = D('Shop');

		$map['is_delete'] =0;
		$latitude = $_REQUEST['latitude']?$_REQUEST['latitude']:0;
		$longitude = $_REQUEST['longitude']?$_REQUEST['longitude']:0;
		$Shop_coupon = $mcoupon->where($map)->select();
		if($Shop_coupon){
			foreach ($Shop_coupon as $k => $v) {
				if($v['coupon_type']==2){
					$Shop_coupon[$k]['type'] = 2;
					$Shop_coupon[$k]['type_name'] = '团购券';
				}elseif($v['coupon_type']==1){
					$Shop_coupon[$k]['type'] = 1;
					$Shop_coupon[$k]['type_name'] = '现金券';
				}
				$map_s['id'] = $v['shop_id'];
				$shopinfo = $model_shop->where($map_s)->find();
				$Shop_coupon[$k]['shop_name'] = $shopinfo['shop_name'];
				$Shop_coupon[$k]['shop_address'] = $shopinfo['shop_address'];
				$Shop_coupon[$k]['shop_maps'] = $shopinfo['shop_maps'];
				$Shop_coupon[$k]['shop_phone'] = $shopinfo['shop_phone'];
				$Shop_coupon[$k]['coupon_pic'] = "http://www.xieche.com.cn/UPLOADS/Coupon/Logo/".$v['coupon_pic'];
				$Shop_coupon[$k]['coupon_logo'] = "http://www.xieche.com.cn/UPLOADS/Coupon/Logo/".$v['coupon_logo'];
			}
			$this->_ret($Shop_coupon,1,'4s店的优惠券详情');
		}else{
			$this->_ret(null,0,'4s店暂无的优惠券详情');
		}
	}

	// 用户团购券订单
	function get_user_coupon(){
		$uid = $this->app_get_userid();
		//$uid = 24054;
		if (@$_REQUEST['detail']) {
			$id = @$_REQUEST['membercoupon_id'];
			$couponmap['xc_membercoupon.membercoupon_id'] = $id;
		}
//		$uid = 6591;
		$model_shop = D('Shop');
		$couponmap['xc_membercoupon.uid'] = $uid;
		$couponmap['xc_membercoupon.is_delete'] = 0;
		$couponmap['xc_coupon.is_delete'] = 0;            //'RIGHT JOIN
		$couponinfo = $this->MembercouponModel->where($couponmap)->join("LEFT JOIN xc_coupon ON xc_membercoupon.coupon_id=xc_coupon.id")->order("xc_membercoupon.membercoupon_id DESC")->select();
		//echo $model_membercoupon->getLastSql();
		if ($couponinfo){
			foreach ($couponinfo as $k=>$v){
				$couponinfo[$k]["coupon_des"]='';
				$shop_id = $v['shop_id'];
				$map_s['id'] = $shop_id;
				if($v['coupon_type']==2){
					$couponinfo[$k]['type'] = 2;
					$couponinfo[$k]['type_name'] = '团购券';
				}elseif($v['coupon_type']==1){
					$couponinfo[$k]['type'] = 1;
					$couponinfo[$k]['type_name'] = '现金券';
				}
				$couponinfo[$k]['price_original'] = $v['cost_price'];
				$couponinfo[$k]['price'] = $v['coupon_amount'];
				$shopinfo = $model_shop->where($map_s)->find();
				$couponinfo[$k]['shop_name'] = $shopinfo['shop_name'];
				$couponinfo[$k]['shop_address'] = $shopinfo['shop_address'];
				$couponinfo[$k]['shop_maps'] = $shopinfo['shop_maps'];
				$couponinfo[$k]['shop_phone'] = $shopinfo['shop_phone'];
				$couponinfo[$k]['coupon_pic'] = "http://www.xieche.com.cn/UPLOADS/Coupon/Logo/".$v['coupon_pic'];
				$couponinfo[$k]['coupon_logo'] = "http://www.xieche.com.cn/UPLOADS/Coupon/Logo/".$v['coupon_logo'];
			}
			if ($_REQUEST['detail']) {
				$this->_ret($couponinfo,1,"团购券订单");
			} else{
				$this->_ret($couponinfo,1,"团购券订单");
			}
//            $this->_ret($couponinfo,1,"团购券订单");
		}else{
			if ($_REQUEST['detail']) {
				$this->_ret(null,0,"暂无团购券订单");
			} else{
				$this->_ret(null,0,"暂无团购券订单");
			}
//            $this->_ret(null,0,"暂无团购券订单");
		}
	}
	//用户上门保养订单
	function get_user_home_maintenance(){
		$uid = $this->app_get_userid();
		if (@$_REQUEST['detail']) {
			$id = @$_REQUEST['id'];
			$map_r['id'] = $id;
		}
		$map_r["uid"]=$uid;// 25775;//$uid;//6591;//$uid;
		$map_r["is_del"]=0;
		$map_r['status'] = array('neq',8);
		$list_r = $this->reservation_order_model->where($map_r)->field('id,uid,status,model_id,licenseplate as car_number,mobile,address,longitude,latitude,truename,amount,item,order_time as reservation_date,create_time,replace_code,order_type,pay_type,pay_status,dikou_amount,technician_id')->order('id DESC')->select();
		if($list_r){
			foreach ($list_r as $key => $value) {
				$list_r[$key]['show_id'] = $this->get_orderid($value['id']);
				$list_r[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);
				$list_r[$key]['status_name'] = $this->getStatusName($value['status']);
				$list_r[$key]['order_type_name'] = $this->_carserviceConf($value['order_type']);
				//车型
				$style_param['model_id'] = $value['model_id'];
				$car_model = $this->carmodel_model->where($style_param)->find();
				$model_name = $car_model['model_name'];

				$series_id = $car_model['series_id'];
				$car_style = $this->carseries_model->where( array('series_id'=>$series_id) )->find();

				$style_name = $car_style['series_name'];

				$car_name = $style_name." - ".$model_name;;

				$brand_param['brand_id'] = $car_style['brand_id'];

				$car_brand = $this->carbrand_model->where($brand_param)->find();

				if($car_brand){
					$brand_name = $car_brand['brand_name'];
					$car_name = $brand_name." - ".$car_name;
					$brand_logo = $car_brand["brand_logo"];
					$list_r[$key]['carName'] = $car_name;
					$list_r[$key]['brand_logo']= WEB_ROOT.'/UPLOADS/Brand/Logo/'.$brand_logo;
				}
				if($value['technician_id']){
					$condition['id'] = $value['technician_id'];
					$technician_info = $this->technician_model->where($condition)->find();
					$list_r[$key]['technician_name'] = $technician_info['truename'];
				}
				if($value["item"]){
					$item_num = 1;
					$order_items = unserialize($value['item']);

					if( !empty( $order_items['oil_detail'] ) ){
						$item_oil_price = 0;
						$oil_data = $order_items['oil_detail'];
						foreach ( $oil_data as $id=>$num){
							if($num > 0){
								$res = $this->item_oil_model->field('name,price')->where( array('id'=>$id))->find();
								$item_oil_price += $res['price']*$num;
								$name = $res['name'];
							}
						}
						$oil_param['id'] =  $order_items['oil_id'];
						$item_list['1']['id'] = $order_items['oil_id'];
						$item_list['1']['name'] = $name;
						$item_list['1']['price'] = $item_oil_price;
						if (!$oil_param['id']){
							unset($item_list['1']);
						}
						if( $order_items['oil_id'] == '-1' ){
							$item_list['1']['id'] = 0;
							$item_list['1']['name'] = "自备配件";
							$item_list['1']['price'] = 0;
						}
						$item_num++;
					}

					if($order_items['filter_id']){
						if($order_items['filter_id'] == '-1'){	//自备配件的情况
							$item_list['2']['id'] = 0;
							$item_list['2']['name'] = "自备配件";
							$item_list['2']['price'] = 0;
						}else{
							$item_condition['id'] = $order_items['filter_id'];
							$name1 = $this->item_model->where($item_condition)->find();
							if(mb_strpos($name1['name'],'马勒')===0){
								$item_list[2]['name'] = "马勒";
							}elseif(mb_strpos($name1['name'],'曼牌')===0){
								$item_list[2]['name'] = "曼牌";
							} elseif(mb_strpos($name1['name'],'博世')===0){
								$item_list[2]['name'] = "博世";
							}else {
								$item_list[2]['name'] = $name1['name'];
							}
							$item_list[2]['price'] = $name1['price'];
						}
						$item_num++;
					}
					if (!$order_items['filter_id']){
						unset($item_list['2']);
					}
					if($order_items['kongqi_id']){
						if( $order_items['kongqi_id'] == '-1' ){
							$item_list['3']['id'] = 0;
							$item_list['3']['name'] = "自备配件";
							$item_list['3']['price'] = 0;
						}else{
							$item_condition2['id'] = $order_items['kongqi_id'];
							$name2 = $this->item_model->where($item_condition2)->find();
							if(mb_strpos($name2['name'],'马勒')===0){
								$item_list[3]['name'] = "马勒";
							}elseif(mb_strpos($name2['name'],'曼牌')===0){
								$item_list[3]['name'] = "曼牌";
							} elseif(mb_strpos($name2['name'],'博世')===0){
								$item_list[3]['name'] = "博世";
							}else {
								$item_list[3]['name'] = $name2['name'];
							}
							$item_list[3]['price'] = $name2['price'];
						}
						$item_num++;
					}
					if(!$order_items['kongqi_id']){
						unset($item_list['3']);
					}
					if($order_items['kongtiao_id']){
						if( $order_items['kongtiao_id'] == '-1' ){
							$item_list['4']['id'] = 0;
							$item_list['4']['name'] = "自备配件";
							$item_list['4']['price'] = 0;
						}else{
							$item_condition3['id'] = $order_items['kongtiao_id'];
							$name3 = $this->item_model->where($item_condition3)->find();
							if(mb_strpos($name3['name'],'马勒')===0){
								$item_list[4]['name'] = "马勒";
							}elseif(mb_strpos($name3['name'],'曼牌')===0){
								$item_list[4]['name'] = "曼牌";
							} elseif(mb_strpos($name3['name'],'博世')===0){
								$item_list[4]['name'] = "博世";
							}else {
								$item_list[4]['name'] = $name3['name'];
							}
							$item_list[4]['price'] = $name3['price'];
						}
						$item_num++;
					}
					if(!$order_items['kongtiao_id']){
						unset($item_list['4']);
					}
					$list_r[$key]['item'] = $item_list;
					$item_amount = 99;
					if ( $value['replace_code'] ) {
						$value = $this->get_codevalue($value['replace_code']);//判断抵用卷的价钱
						if ($value) {
							$list_r[$key]['replace_value'] = $value;
						}
						if($value != 99){
							$item_amount = $item_amount - $value;	//加服务费，减抵用券费用
						}else{
							$item_amount = 0;
						}
					}
					if($value['order_type']==2){
						$item_amount = 0;
					}

					if(is_array($item_list)){
						foreach ($item_list as $k => $v) {
							$item_amount += $v['price'];
						}
					}
					$list_r[$key]['amount2']= $item_amount;
				}
			}
			if (@$_REQUEST['detail']) {
				$this->_ret($list_r, 1, "上门保养订单");
			} else {
				return $list_r;
			}
//            $this->_ret($list_r,1,"上门保养订单");
		} else {
			if (@$_REQUEST['detail']) {
				$this->_ret(null, 0, "暂无上门保养订单");
			} else {
				return null;
			}
//            $this->_ret(null,0,"暂无上门保养订单");
		}
	}
	private function _carserviceConf($type){
		switch ($type){
			case 1:
				$name = '保养订单';
				break;
			case 2:
				$name = '检测订单';
				break;
			case 3:
				$name = '淘宝99元保养订单';
				break;
			case 6:
				$name = '免99元服务费订单';
				break;
			case 7:
				$name = '黄喜力套餐';
				break;
			case 8:
				$name = '蓝喜力套餐';
				break;
			case 9:
				$name = '灰喜力套餐';
				break;
			case 10:
				$name = '金美孚套餐';
				break;
			case 11:
				$name = '爱代驾高端保养';
				break;
			case 12:
				$name = '爱代驾中端保养';
				break;
			case 13:
				$name = '好车况';
				break;
			case 14:
				$name = '好空气';
				break;
			case 15:
				$name = '好动力';
				break;
			case 16:
				$name = '保养服务+检测+养护';
				break;
			case 17:
				$name = '矿物质油保养套餐+检测+养护';
				break;
			case 18:
				$name = '半合成油保养套餐+检测+养护';
				break;
			case 19:
				$name = '全合成油保养套餐+检测+养护';
				break;
			case 20:
				$name = '平安保险微信';
				break;
			case 22:
				$name = '168套餐';
				break;
			case 23:
				$name = '268套餐';
				break;
			case 24:
				$name = '368套餐';
				break;
			case 25:
				$name = '浦发199';
				break;
			case 26:
				$name = '浦发299';
				break;
			case 27:
				$name = '浦发399';
				break;
			case 28:
				$name = '全车检测38项(淘38)';
				break;
			case 29:
				$name = '细节养护7项(淘38)';
				break;
			case 30:
				$name = '更换空调滤工时(淘38)';
				break;
			case 31:
				$name = '更换雨刮工时(淘38)';
				break;
			case 32:
				$name = '小保养工时(淘38)';
				break;
			case 33:
				$name = '好空气套餐(奥迪宝马奔驰)';
				break;
			case 34:
				$name = '补配件免人工订单';
				break;
			case 35:
				$name = '好车况套餐:38项全车检测+7项细节养护';
				break;
		}
		return $name;
	}
	//获取订单状态
	private function get_4s_StatusName($status){
		switch ($status) {
			case '-1':
				return "已取消 ";
				break;

			case '1':
				return "预约确认";
				break;

			case '2':
				return "已完成";
				break;

			default:
				return "等待处理";
				break;
		}
	}
	//支付状态
	private function getPayStatusName($pay_status){
		switch ($pay_status) {
			case '0':
				return "未支付 ";
				break;

			case '1':
				return "已支付";
				break;

			case '2':
				return "申请退款";
				break;

			case '3':
				return "已退款";
				break;

			default:
				return "未支付";
				break;
		}
	}
	//获取订单状态
	private function getStatusName($status){
		switch ($status) {
			case '0':
				return "等待处理 ";
				break;

			case '1':
				return "预约确认";
				break;

			case '2':
				return "已分配技师";
				break;

			case '7':
				return "用户终止";
				break;

			case '8':
				return "订单作废";
				break;

			case '9':
				return "服务已完成";
				break;

			default:
				return "等待处理";
				break;
		}
	}
	//查找/显示上门保养的评论
	function get_reservation_comment(){
		$p = @$_REQUEST['p'];
		$p = $p?$p:1;
		$comment_info2 = $this->Reservation_commentModel->field('id,reserveorder_id,type,content as comment,comment_time')->order('id desc')->page($p,20)->select();
		foreach($comment_info2 as $k=>$v){
			$map["id"] =$v['reserveorder_id'];
			$member = $this->reservation_order_model->where($map)->find();
			if ($member) {
				$mobile = substr($member['mobile'],0,3);
				$mobile .= '****';
				$mobile .= substr($member['mobile'],-4,4);
				$comment_info2[$k]["mobile"] = $mobile;
				$comment_info2[$k]["user_name"] = '*'.mb_substr($member['truename'],1,strlen($member['truename']),'utf-8');
			}
			$id = $this->get_orderid($v["reserveorder_id"]);
			$comment_info2[$k]["reserveorder_id"] = $id;
		}
		if($comment_info2){
			$this->_ret($comment_info2,1,"success");
		} else{
			$this->_ret(null,0,"暂无评论！");
		}
	}
	//查找/显示4s店的评论
	function get_comment(){
		$shop_id = @$_REQUEST["shop_id"];
		$coupon_id = @$_REQUEST["coupon_id"];
		if($coupon_id){
			$map2["id"] =$coupon_id;
			$coupon_info = $this->CouponModel->where($map2)->find();
			$shop_id2 = $coupon_info["shop_id"];
		}
		$shop_id = $shop_id? $shop_id:$shop_id2;
		if($shop_id){
			$map["shop_id"] = $shop_id;
			// $map["comment_type"] =1; //field('id,nickname as name')
			$comment_info = $this->CommentModel->where($map)->field('uid,user_name,comment,update_time')->order('update_time desc,comment_type asc')->limit(0,25)->select();
			if($comment_info){
				$this->_ret($comment_info,1,"success");
			} else{
				$this->_ret(null,0,"暂无评论！");
			}
		}else{
			$this->_ret(null,0,"4s店错误！");
		}
	}
	//团购券现金券
	function get_coupon_list()
	{
		$model_id = @$_REQUEST['model_id'];
		$type = @$_REQUEST['type'];
		$longitude = @$_REQUEST['longitude'];
		$latitude = @$_REQUEST['latitude'];
		if (!$model_id) {
			$this->_ret(null, 0, '车型id不能为空');
		}
		$type = $type?$type:1;
		$longitude =$longitude?$longitude:0;
		$latitude =$latitude?$latitude:0;
		$this->get_app_tuijian_coupon('', $model_id, 12, true, $longitude, $latitude, $type);
	}

	/*
     * @author:chf @function:得到推荐优惠套餐券信息 @time:2015-04-03
     * $longitude 经度
     * $latitude  纬度
     */
	function get_app_tuijian_coupon($shop_id = '', $model_id = '', $limit = 4, $model_name = false, $longitude, $latitude, $type)
	{
		//现金券推荐
		$model_coupon = D('Coupon');
		$map_tuijian_c['is_delete'] = 0;
		$map_tuijian_c['show_s_time'] = array('lt', time());
		$map_tuijian_c['show_e_time'] = array('gt', time());
		$map_tuijian_c['coupon_type'] = 1;
		if ($shop_id) {
			$map_tuijian_c['shop_id'] = $shop_id;
		}
		$fsid_str = '';
		if ($model_id) {
			$model_carmodel = D('Carmodel');
			$map_mo['model_id'] = $model_id;
			$model = $model_carmodel->where($map_mo)->find();
			if ($model) {
				$map_model_id = array("like", "%{$model_id}%");
				$model_series = D('Carseries');
				if ($model_series) {
					$map_series_id = array("like", "%{$model['series_id']}%");
					$map_se['series_id'] = $model['series_id'];
					$series = $model_series->where($map_se)->find();
					if ($series) {
						if ($series['brand_id']) {
							$map_brand_id = $series['brand_id'];
							$map_other_brand = array("like", "%{$series['brand_id']},%");
						}
						$fsid_arr[] = $series['fsid'];
						$fsid_arr = array_unique($fsid_arr);
						$fsid_str = implode(',', $fsid_arr);
					}
				}
			}
		}

		if ($fsid_str) {
			if ($type == 1) {
				$map_tuijian_c['fsid'] = array('in', $fsid_str); //现金卷用fsid查询
				$tuijian_coupon1 = $model_coupon->where($map_tuijian_c)->field()->limit($limit)->select();
				$this->addCodeLog('couponlog', $model_coupon->getLastSql());
				foreach ($tuijian_coupon1 as $k => $v) {
					$data_xxx["id"] = $v["shop_id"];
					$shop_info = $this->ShopModel->where($data_xxx)->find();
					$img = "http://www.xieche.com.cn/UPLOADS/Coupon/Logo/" . $v["coupon_pic"];
					$shop_maps = explode(',', $shop_info["shop_maps"]);
					$distance = $this->GetDistance($latitude, $longitude, $shop_maps[1], $shop_maps[0]);
					$data_xx[] = array(
						'shop_name' => $shop_info["shop_name"],  //4sdianming
						'coupon_id' => $v["id"],
						'image' => $img, //
						'name' => $v["coupon_name"],
						'price_original' => $v["cost_price"],
						'price' => $v["coupon_amount"],
						'distance' => $distance,
						'type' => 1   //1.为现金券
					);
				}
				if ($tuijian_coupon1) {
					$this->_ret($data_xx, 1, "success");
				} else {
					$this->_ret(null, 0, "没有数据");
				}
			}
		}
		if ($type == 2) {
			//套餐券推荐本品牌
			unset($map_tuijian_c['id']);
			unset($map_tuijian_c['fsid']);

			if (isset($map_model_id)) {
				$map_tuijian_c['model_id'] = $map_model_id;
			}
			if (isset($map_series_id)) {
				$map_tuijian_c['series_id'] = $map_series_id;
			}
			if (isset($map_brand_id)) {
				$map_tuijian_c['brand_id'] = $map_brand_id;
				$map_tuijian_c['coupon_type'] = 2;
				$tuijian_coupon2 = $model_coupon->where($map_tuijian_c)->limit($limit)->select();
				$this->addCodeLog('couponlog', $model_coupon->getLastSql());
				foreach ($tuijian_coupon2 as $k => $v) {
					$data_xx["id"] = $v["shop_id"];
					$shop_info = $this->ShopModel->where($data_xx)->find();
					$img = "http://www.xieche.com.cn/UPLOADS/Coupon/Logo/" . $v["coupon_pic"];
					$shop_maps = explode(',', $shop_info["shop_maps"]);
					$distance = $this->GetDistance($latitude, $longitude, $shop_maps[0], $shop_maps[1]);
					$data_x[] = array(
						'shop_name' => $shop_info["shop_name"],  //4sdianming
						'coupon_id' => $v["id"],
						'image' => $img, //4s店 logo
						'name' => $v["coupon_name"],
						'price_original' => $v["cost_price"],
						'price' => $v["coupon_amount"],
						'distance' => $distance,
						'type' => 2   //2..团购券
					);
				}
				//跨品牌
				unset($map_tuijian_c['model_id']);
				unset($map_tuijian_c['series_id']);
				unset($map_tuijian_c['id']);
				$map_tuijian_c['brand_id'] = $map_other_brand;
				$map_tuijian_c['coupon_across'] = 1;
				$tuijian_coupon3 = $model_coupon->where($map_tuijian_c)->limit($limit)->select();
				$this->addCodeLog('couponlog', $model_coupon->getLastSql());
				$this->submitCodeLog('couponlog');
				foreach ($tuijian_coupon3 as $k => $v) {
					$data_xx["id"] = $v["shop_id"];
					$shop_info = $this->ShopModel->where($data_xx)->find();
					$img = "http://www.xieche.com.cn/UPLOADS/Coupon/Logo/" . $v["coupon_pic"];
					$shop_maps = explode(',', $shop_info["shop_maps"]);
					$distance = $this->GetDistance($latitude, $longitude, $shop_maps[0], $shop_maps[1]);
					$data_x[] = array(
						'shop_name' => $shop_info["shop_name"],  //4sdianming
						'coupon_id' => $v["id"],
						'image' => $img, //4s店 logo
						'name' => $v["coupon_name"],
						'price_original' => $v["cost_price"],
						'price' => $v["coupon_amount"],
						'distance' => $distance,
						'type' => 2   //2..团购券
					);
				}
			}
			if ($tuijian_coupon2 || $tuijian_coupon3) {
				$this->_ret($data_x, 1, "success");
			} else {
				$this->_ret(null, 0, "没有数据");
			}
		}

	}

	public function get_fsid($shop_id)
	{
		$model_shop_fs_relation = D('Shop_fs_relation');
		$map_shopfs ['shopid'] = $shop_id;
		$shop_fs_relation = $model_shop_fs_relation->where($map_shopfs)->select();
		if ($shop_fs_relation) {
			$model_carseries = D('Carseries');
			foreach ($shop_fs_relation as $k => $v) {
				if ($v ['fsid']) {
					return $v ['fsid'];
				}
			}
		}
	}

	function get_coupon_info()
	{
		header('Content-Type: application/json');
		$coupon_id = @$_REQUEST['coupon_id'];
		$latitude = @$_REQUEST['latitude'];
		$longitude = @$_REQUEST['longitude'];
		$data["id"] = $coupon_id;
		$detail_info = $this->CouponModel->where($data)->find();
		$data_shop["id"] = $detail_info["shop_id"];
		$shop_info = $this->ShopModel->where($data_shop)->find();
		$img = "http://www.xieche.com.cn/UPLOADS/Coupon/Logo/" . $detail_info["coupon_pic"];
		$str = '<script src="http://www.xieche.com.cn/Public_new/jquery/jquery.min.js"></script><script type="text/javascript">$(function(){$("img").css({"width":"100%","height":"auto"});$("p,p *").css({"font-size":"12px","line-height":"1em;"});})</script>';
		$shop_maps = explode(',', $shop_info["shop_maps"]);
		$distance = $this->GetDistance($latitude, $longitude, $shop_maps[1], $shop_maps[0]);
		$data_detail = array(
			'shop_name' => $shop_info["shop_name"],
			'image' => $img, //优惠券的图！
			'coupon_id' => $detail_info["id"],
			'name' => $detail_info["coupon_name"],
			'price_original' => $detail_info["cost_price"],
			'price' => $detail_info["coupon_amount"],
			'distance' => $distance,
			'sales_volume' => $shop_info["success_order"],
			'detail' => $str . $detail_info["coupon_des"]
		);
		if ($detail_info || $shop_info) {
			$this->_ret($data_detail, 1, "success");
		} else {
			$this->_ret(null, 0, "fail");
		}

	}

	/*
        @author:chf
        @function:发送验证码
        @time:2015-4-1
    */
	function send_verify()
	{
		$mobile = @$_REQUEST['phone'];
		//$mobile = '18701742050';
		if ($mobile) {
			$count = $this->user_model->where(array('mobile' => $mobile, 'status' => '1'))->count();
			if (!$count) {
				$condition['phones'] = $mobile;
				$rand_verify = rand(10000, 99999);
				$verify_str = "正在为您的手机验证，进行注册登录 ，您的短信验证码：" . $rand_verify;
				$send_verify = array(
					'phones' => $mobile,
					'content' => $verify_str
				);
				$this->curl_sms($send_verify);
				$model_verify = D('appverify');
				$send_verify1 = array(
					'id' => '',
					'phone' => $mobile,
					'verify' => $rand_verify,
					'sendtime' => time()
				);
				$num = $model_verify->add($send_verify1);
				$model_sms = D('sms');
				$send_verify = array(
					'phones' => $mobile,
					'sendtime' => time(),
					'content' => $verify_str . "||" . $_SESSION["app_mob"] . "||" . $_SESSION["app_verify"]
				);
				//var_dump($_SESSION);
				$num2 = $model_sms->add($send_verify);
				if ($num > 0 && $num2 > 0) {
					$this->_ret($rand_verify, 1, '发送验证码成功');
					exit;
				} else {
					$this->_ret(null, 0, '发送验证码失败');
					exit;
				}
			} else {
				$this->_ret(null, 0, '手机号已注册');
				exit;
			}
		} else {
			$this->_ret(null, 0, '未收到手机号');
			exit;
		}
	}

	//验证码验证
	function validate_phone_code()
	{
		$code = $_REQUEST["code"];
		$phone = $_REQUEST["phone"];
		$verify = D('appverify');
		$map_ver['phone'] = $phone;
		$map_ver['verify'] = $code;
		$verify_info = $verify->where($map_ver)->find();
		$code2 = md5($verify_info['verify']);

		$verify_code = isset($code) ? $code : '0';
		if ($code2 != md5($verify_code)) {
			$this->_ret(null, 0, '验证失败');
			exit;

		} else {
			$this->_ret(null, 1, '验证成功');
			exit;
		}
	}

	function send_verify2()
	{
		$mobile = @$_REQUEST['phone'];
		//$mobile = '18701742050';
		if ($mobile) {
			$count = $this->user_model->where(array('mobile' => $mobile, 'status' => '1'))->count();
			if ($count) {
				$condition['phones'] = $mobile;
				$rand_verify = rand(10000, 99999);
				$verify_str = "正在为您的手机验证，进行重置密码 ，您的短信验证码：" . $rand_verify;
				$send_verify = array(
					'phones' => $mobile,
					'content' => $verify_str
				);
				$this->curl_sms($send_verify);
				$model_verify = D('appverify');
				$send_verify1 = array(
					'id' => '',
					'phone' => $mobile,
					'verify' => $rand_verify,
					'sendtime' => time()
				);
				$num = $model_verify->add($send_verify1);
				$model_sms = D('sms');
				$send_verify = array(
					'phones' => $mobile,
					'sendtime' => time(),
					'content' => $verify_str . "||" . $_SESSION["app_mob"] . "||" . $_SESSION["app_verify"]
				);
				//var_dump($_SESSION);
				$num2 = $model_sms->add($send_verify);
				if ($num > 0 && $num2 > 0) {
					$this->_ret($rand_verify, 1, '发送验证码成功');
					exit;
				} else {
					$this->_ret(null, 0, '发送验证码失败');
					exit;
				}
			} else {
				$this->_ret(null, 0, '手机号未注册');
				exit;
			}
		} else {
			$this->_ret(null, 0, '未收到手机号');
			exit;
		}
	}

	/*
 * 插入操作
 * 执行插入
 *
 */
	function app_register()
	{
		$code = $_REQUEST["code"];
		$phone = $_REQUEST["phone"];
		$password = $_REQUEST["password"];
		$repassword = $_REQUEST["repassword"];
		$ver = D("appverify");
		$map_1['phone'] = $phone;
		$map_1['verify'] = $code;
		$info = $ver->where($map_1)->find();
		if (empty($phone)) {
			$this->_ret(null, 0, '用户名不能为空');
			exit;
		}
		if ($phone) {
			$map['mobile'] = $phone;
			$map['status'] = 1;
			$ress = $this->user_model->where($map)->find();
			if ($ress) {
				$this->_ret(null, 0, '手机号已经存在');
				exit;
			}
			unset($map);
		}
		if (empty($password)) {
			$this->_ret(null, 0, '密码不能为空');
			exit;
		}
		if (empty($repassword)) {
			$this->_ret(null, 0, '确认密码不能为空');
			exit;
		}
		if ($password != $repassword) {
			$this->_ret(null, 0, '两次密码不一致');
			exit;
		}
		if ($info && !empty($info)) {
			$data['password'] = md5($password);
			$data['mobile'] = $phone;
			$data['reg_time'] = time();
			$data['ip'] = get_client_ip();
			$data['last_login_time'] = time();
			$data['fromstatus'] = '10';//newapp

			$num = $this->user_model->add($data);
			if ($num) {
				$this->_ret(null, 1, '注册成功');
				exit;
			} else {
				//失败提示
				$this->_ret(null, 0, '注册失败');
				exit;
			}

		} else {
			$this->_ret(null, 0, '信息不正确！');
			exit;
		}

	}

	//用户登录验证
	function app_login()
	{
		$password = @$_REQUEST["password"];
		$username = @$_REQUEST["phone"];

		if (!$username) {
			$this->_ret(null, 0, '用户名不存在');
			exit;
		} elseif (!$password) {
			$this->_ret(null, 0, '密码不存在');
			exit;
		}
		$password_md5 = md5($password);

		$Member = M('Member');
		if (is_numeric($username)) {
			if (substr($username, 0, 1) == 1) {
				$res = $Member->where("mobile='$username' AND status='1'")->find();
			} else {
				$res = $Member->where("cardid='$username' AND status='1'")->find();
			}
		} else {
			$res = $Member->where("username='$username' AND status='1'")->find();
		}
		if (!$res) {
			$this->_ret(null, 0, '账号或密码不正确！');
			exit;

		}
		if ($res and $res['password'] == $password_md5) {
			$uid = $res["uid"];
			$mobile = $res['mobile'];
			$session = sha1(sha1($uid . time() . $res["password"] . "xie_che.com123"));
			$app_user = D("appusersession");
			$data_u["uid"] = $uid;
			$user_s_info = $app_user->where($data_u)->find();
			if ($user_s_info) {
				$user_id = $user_s_info["uid"];
				$data_user["uid"] = $user_id;
				$data = array(
					'session' => $session
				);
				$app_user->where($data_user)->save($data);
				$data2 = array(
					'uid' => $user_id,
					'mobile' => $mobile,
					'session' => $session
				);
				$this->_ret($data2, 1, '登录成功');
				exit;
			} else {
				$user_data = array(
					'id' => '',
					'uid' => $uid,
					'session' => $session,
					'create_time' => time()
				);
				$app_user->add($user_data);
				$data = array(
					'uid' => $uid,
					'mobile' => $mobile,
					'session' => $session
				);
				$this->_ret($data, 1, '登录成功');
				exit;
			}
		} else {
			$this->_ret(null, 0, '账户密码错误');
			exit;

		}
	}

	//验证用户身份
	function check_user_session()
	{
		$uid = @$_REQUEST["uid"];
		$session = @$_REQUEST["session"];
		$app_user = D("appusersession");
		$data_u["uid"] = $uid;
		$info = $app_user->where($data_u)->find();
		if ($info) {
			if ($info["session"] == $session) {
				$mem_info = $this->user_model->where($data_u)->find();
				$data = array(
					'username' => $mem_info["username"],
					'mobile' => $mem_info["mobile"]
				);
				$this->_ret($data, 1, '认证通过');
				exit;
			} else {
				$this->_ret(null, 0, '认证失败');
				exit;
			}
		} else {
			$this->_ret(null, 0, '用户不存在');
			exit;
		}
	}

	//忘记用户密码
	function update_password()
	{
		$mobile = @$_REQUEST["phone"];
		$pwd = @$_REQUEST["password"];
		$code = @$_REQUEST["code"];
		$ver = D("appverify");
		$map_1['phone'] = $mobile;
		$map_1['verify'] = $code;
		$info = $ver->where($map_1)->find();
		if ($mobile && $pwd && $info) {
			$data_pwd = array(
				'password' => md5($pwd),
			);
			$pwd_info = $this->user_model->where("mobile='$mobile' AND status='1'")->find();
			$num = $this->user_model->where("mobile='$mobile' AND status='1'")->save($data_pwd);
			if ($num) {
				$this->_ret(null, 1, '修改密码成功');
				exit;
			} elseif (md5($pwd) == $pwd_info["password"]) {
				$this->_ret(null, 1, '修改密码成功');
				exit;
			} else {
				$this->_ret(null, 0, '修改密码失败');
				exit;
			}
		} else {
			$this->_ret(null, 0, '信息不完整');
			exit;
		}
	}
	//个人中心订单中微信支付团购券
	function coupon_repay_weixin() {
		$uid = $this->app_get_userid();
		$membercoupon_id_str = $_REQUEST['order_id'];
		$map['membercoupon_id'] =  $membercoupon_id_str;
		$map['uid'] = $uid;
		$map['is_pay'] = 0;
		$map['is_delete'] = 0;
		$info = $this->MembercouponModel->where($map)->find();
		if($info){
			$wx_coupon_id = D('wxcouponidgroup');
			$data = array(
				'id'=>'',
				'coupon_ids'=>$membercoupon_id_str
			);
			$id_coupon = $wx_coupon_id->add($data);
			if($id_coupon) {
				$number = 1;
				$url = WEB_ROOT.'/weixinpaytest/get_paydata_coupon.php?membercoupon_id=' . $membercoupon_id_str . '&number=' . $number . '&id=' . $id_coupon;
				if ($stream = fopen($url, 'r')) {
					$data2 = stream_get_contents($stream);
					$data2 = substr($data2, strrpos($data2, '{'), strlen(substr($data2, strrpos($data2, '{'))) - 1);
//							$data3 = json_decode($data2, true);
//							echo $data2;
//							var_dump($data3);
					fclose($stream);
					$this->_ret_c($data2, 1, "success");
				}
			}
		}else{
			$this->_ret(null,0,'没有此团购券');
		}
	}
	//个人中心订单中微信支付上门保养
	function home_maintenance_repay_weixin() {
		$uid = $this->app_get_userid();
//		$uid =6591;
		$reservation_id = $_REQUEST['order_id'];
		$map['id'] =  $reservation_id;
		$map['uid'] = $uid;
		$map['pay_status'] = 0;
		$map['is_del'] = 0;
		$info = $this->reservation_order_model->where($map)->find();
		if($info){
			$url = WEB_ROOT.'/weixinpaytest/get_paydata.php?order_id='.$info['id'];
			if ($stream = fopen($url, 'r')) {
				$data2 = stream_get_contents($stream);
				$data2 = substr($data2, strrpos($data2, '{'), strlen(substr($data2, strrpos($data2, '{'))) - 1);
//							$data3 = json_decode($data2, true);
//							echo $data2;
//							var_dump($data3);
				fclose($stream);
				$this->_ret_c($data2, 1, "success");
			}
		}else{
			$this->_ret(null,0,'没有此上门保养订单');
		}
	}

	//购买团购券：
	/*
        @author:bright
        @function:下团购券订单
        @time:2014-11-10
    */
	function buy_coupon ()
	{
		$uid = $this->app_get_userid();
		$coupon_id = @$_REQUEST["coupon_id"];
		$pay_type = @$_REQUEST["pay_type"];   //2.weixin
		$phone = @$_REQUEST["phone"];
		$number = @$_REQUEST["count"];
		//$this->is_safepay();//处理现金账户支付流程
		//得到优惠券信息
		if (!$phone && !$pay_type && !$number && !$coupon_id) {
			$this->_ret(null, 0, '信息不完整');
			exit;
		}
		if ($number > 0 and is_numeric($number)) {
			$map_coupon['id'] = $coupon_id;
			$map_coupon['end_time'] = array('gt', time());
			$coupon = $this->CouponModel->where($map_coupon)->find();

			//			$model_shop = D('shop');
			//			$shop = $model_shop->where(array('id'=>$coupon['shop_id']))->find();
			if ($coupon['is_delete'] == '1') {
				$this->_ret(null,0,'抱歉,此优惠卷已下架！');exit;
			} else {  //300*（1-100/600）
				if ($coupon) {
					$All_amount = $coupon['coupon_amount'] * $number;//总支付金额
					for ($ii = 1; $ii <= $number; $ii++) {
						//有立减券-100
						$data['coupon_amount'] = $coupon['coupon_amount'];
						$data['uid'] = $uid;
						$data['coupon_id'] = $coupon_id;
						$data['coupon_name'] = $coupon['coupon_name'];
						$data['shop_ids'] = $coupon['shop_id'];
						$data['mobile'] = $phone;
						$data['end_time'] = $coupon['end_time'];
						$data['start_time'] = $coupon['start_time'];
						$data['coupon_type'] = $coupon['coupon_type'];
						$data['create_time'] = time();
						$data['pa'] = 8; //new_app

						/* 改到使用的时候再去计算
                         if($coupon['coupon_type']=='1'){
                        $data['ratio'] = $shop['cash_rebate'];//现金券比例
                        }else{
                        $data['ratio'] = $shop['group_rebate'];
                        }*/
						//记录购买平安好车访问用户
						if ($membercoupon_id = $this->MembercouponModel->add($data)) {
							$membercoupon_id_arr[] = $membercoupon_id;
						}
					}
					$membercoupon_id_str = implode(',', $membercoupon_id_arr);
					$_SESSION['membercoupon_ids'] = $membercoupon_id_str;
					$wx_coupon_id = D("wxcouponidgroup");
					$data_id = array(
						'id'=>'',
						'coupon_ids'=>$membercoupon_id_str
					);
					$id_coupon = $wx_coupon_id->add($data_id);
					if ($membercoupon_id_str && $id_coupon) {
						//echo $membercoupon_id_str;
						$url = WEB_ROOT.'/weixinpaytest/get_paydata_coupon.php?membercoupon_id='.$membercoupon_id_str.'&number='.$number.'&id='.$id_coupon;
						if ($stream = fopen($url, 'r')) {
							$data2 = stream_get_contents($stream);
							$data2 = substr($data2,strrpos($data2,'{'),strlen(substr($data2,strrpos($data2,'{')))-1);
//							$data3 = json_decode($data2, true);
//							echo $data2;
//							var_dump($data3);
							fclose($stream);
							$this->_ret_c($data2, 1, "订单已提交");
						} else {
							$this->_ret(null, 0, "500");
						}
						//$data = stream_get_contents($url1);
						//$data = file_get_contents($url1);
						//$this->success("订单已提交,请立即付款",__APP__.'/coupon/couponpay',true);
					} else {
						$this->_ret(null, 0, "订单提交失败");
					}

				} else {
					$this->_ret(null, 0, "操作失败");
				}
			}
		} else {
			$this->_ret(null, 0, "购买数量输入错误");
		}
	}

	//车品牌
	public function getCarBrand(){
		$mBrand = M('tp_xieche.carbrand','xc_');
		
// 		$data = $mBrand->where(array('isShow'=>1))->select();//TODO::测试环境暂时隐藏
		$data = $mBrand->select();
		$this->_ret($data);
	}
	//车系
	public function getCarSeries(){
		$brandId = @$_REQUEST['brand_id'];
		if (!$brandId) {
			$this->_ret(null,0,'品牌id不能为空');exit;
		}
		$mCarseries = M('tp_xieche.carseries','xc_');
		$data = $mCarseries->where(array('brand_id'=>$brandId))->select();
		$this->_ret($data);
	}
	//车型号
	public function getCarModel(){
		$seriesId = @$_REQUEST['series_id'];
		if (!$seriesId) {
			$this->_ret(null,0,'车系id不能为空');exit;
		}
		$mCarmodel = M('tp_xieche.carmodel','xc_');
		$data = $mCarmodel->field('item_set',true)->where(array('series_id'=>$seriesId))->select();
		$this->_ret($data);
	}

	//APP获取配件接口
	public function car_accessory(){
		session_start();
	
		$_SESSION['admin_style_id'] = $_REQUEST['model_id'];
	
		//车型
		$style_param['model_id'] = $_SESSION['admin_style_id'];
		//$car_style = $this->car_style_model->where($style_param)->find();
		$car_style = $this->carmodel_model->where($style_param)->find();
		$style_name = $car_style['model_name'];
		$car_name = $car_style['model_name'];
	
		if($car_style){
			$model_param['series_id'] = $car_style['series_id'];
			//$car_model = $this->car_model_model->where($model_param)->find();
			$car_model = $this->carseries_model->where($model_param)->find();
	
			if($car_model){
				$model_name = $car_model['series_name'];
				$car_name = $car_model['series_name']." - ".$car_name;
	
				$brand_param['brand_id'] = $car_model['brand_id'];
				//$car_brand = $this->car_brand_model->where($brand_param)->find();
				$car_brand = $this->carbrand_model->where($brand_param)->find();
				if($car_brand){
					$brand_name = $car_brand['brand_name'];
					$car_name = $car_brand['brand_name']." - ".$car_name;
				}
			}
		}
		$this->assign('car_name', $car_name);
		$this->assign('brand_name', $brand_name);
		$this->assign('model_name', $model_name);
		$this->assign('style_name', $style_name);
	
		//机油
		//$oil_num = ceil($car_style['oil_num']);
		$oil_num = ceil($car_style['oil_mass']);
		if ($oil_num <4) {
			$oil_num = 4;
		}
		if( $oil_num >3 ){
			$xx = $oil_num%4;
			$yy = ($oil_num - $xx)/4;
			if($xx){
				//$norms = $yy."x4L + ".$xx."x1L";
				$norms = $yy+$xx."L";
			}else{
				//$norms = $yy."x4L";
				$norms = $yy."L";
			}
		}else{
			$xx = $oil_num;
			$yy = 0;
			//$norms = $oil_num . "x1L";
			$norms = $oil_num . "L";
		}
		if( $oil_num <4 ){
			$car_style['norms'] = '4L';
		}else{
			$car_style['norms'] = $oil_num."L";//多少升机油
		}
	
		//所有物品详情
		$oil_list_all = $this->item_oil_model->where()->select();
		$oil_item = array();
		foreach( $oil_list_all as $nors){
			$nors['name'] = rtrim($nors['name'],'装');
			$oil_item[$nors['name']][$nors['norms']] = $nors;
		}

		$oil_param = array();
		//根据这辆车的机油类型来取数据，如果是矿物形，全取，半合成，取两个，全合成取一个
		switch ($car_style['oil_type']){
			case a:
				$oil_param['type'] = array('in','1,2,3');
				break;
			case b:
				$oil_param['type'] = array('in','2,3');
				break;
			case c:
				$oil_param['type'] = 3;
				break;
			default:
				$oil_param['type'] = 0;
				break;
		}
		//$oil_param['norms'] = $oil_num;	//需要多少升的机油
		$oil_param['norms'] = 4;	//需要多少升的机油

		if($_SESSION['order_type']==7){
			$oli_taocan = array('49');
		}
		if($_SESSION['order_type']==8){
			$oli_taocan = array('47','48');
		}
		if($_SESSION['order_type']==9){
			$oli_taocan = array('45','46');
		}

		//符合要求的品牌详情
		$item_sets=array();
		$oil_name_distinct = $this->item_oil_model->order('price')->where($oil_param)->select();

		foreach( $oil_name_distinct as $keys=>$names ){

			$names['name'] = rtrim($names['name'],'装');

			//$item_sets[$keys]['id'] = $names['id'];
			$item_sets[$keys]['name'] = $names['name'];
			$item_sets[$keys]['suite_id'] = $oil_item[$names['name']][4]['id'];
			//规格1L详情
			$item_sets[$keys]['accessories'][0]['id'] = $oil_item[$names['name']][1]['id'];
			$item_sets[$keys]['accessories'][0]['count'] = $xx;
			$item_sets[$keys]['accessories'][0]['unit_price'] = $oil_item[$names['name']][1]['price'];
			$item_sets[$keys]['accessories'][0]['name'] = $names['name'];
			$item_sets[$keys]['accessories'][0]['norms'] = $oil_item[$names['name']][1]['norms'];
			//规格4L详情
			$item_sets[$keys]['accessories'][1]['id'] = $oil_item[$names['name']][4]['id'];
			$item_sets[$keys]['accessories'][1]['count'] = $yy;
			$item_sets[$keys]['accessories'][1]['unit_price'] = $oil_item[$names['name']][4]['price'];
			$item_sets[$keys]['accessories'][1]['name'] = $names['name'];
			$item_sets[$keys]['accessories'][1]['norms'] = $oil_item[$names['name']][4]['norms'];
			//计算总价
			$item_sets[$keys]['total_price'] = $xx*$oil_item[$names['name']][1]['price']+$yy*$oil_item[$names['name']][4]['price'];
	
		}

		//排除套餐机油类型之外的机油选项
		if(isset($_SESSION['order_type']) && $_SESSION['order_type']>6){
			foreach($item_sets as $_kk=>$_vv){
				if(!in_array($_vv['id'],$oli_taocan)){
					unset($item_sets[$_kk]);
				}
			}
			foreach($item_sets as $_kk=>$_vv){
				$item_sets['0'] = $_vv;
				if($_kk!=0){
					unset($item_sets[$_kk]);
				}
			}
		}
		$car_style['type'] = '未设定';
		if($car_style['oil_type']==1){
			$car_style['type'] = '矿物油';
		}
		if($car_style['oil_type']==2){
			$car_style['type'] = '半合成油';
		}
		if($car_style['oil_type']==3){
			$car_style['type'] = '全合成油';
		}
	
		//项目
		$style_id = $_SESSION['admin_style_id'];
		$item_set = array();
		if( $style_id ){
			$condition['model_id'] = $style_id;
			//$style_info = $this->car_style_model->where($condition)->find();
			$style_info = $this->carmodel_model->where($condition)->find();
			$set_id_arr = $style_info['item_set'] ? unserialize($style_info['item_set']) : array();
	
			if( $set_id_arr ){
				foreach( $set_id_arr as $k=>$v){
					if(is_array($v)){
						foreach( $v as $_k=>$_v){
							$item_condition['id'] = $_v;
							$item_condition['name'] = array('notlike','%pm2%');
							$item_info_res = $this->filter_model->where($item_condition)->find();
							$item_info['id'] = $item_info_res['id'];
							$item_info['name'] = $item_info_res['name'];
							//$item_info['unit_price'] = $item_info_res['unit_price'] ? $item_info_res['unit_price'] : 0;
							//$item_info['number'] = $item_info_res['number'] ? $item_info_res['number'] : 0;
							$item_info['total_price'] = $item_info_res['price'] ? $item_info_res['price'] : 0;
							//$item_info['type'] = $item_info_res['type'] ? $item_info_res['type'] : 0;
							if($item_info['id']!=''){
								$item_set1[$k][$_k] = $item_info;
							}
						}
						//排除数组中缺乏元素页面选项空白的问题
						/*if(!$item_set[$k][0] and $item_set[$k][1]){
							$item_set[$k][0] = $item_set[$k][1];
							unset($item_set[$k][1]);
						}
						if(!$item_set[$k][1] and $item_set[$k][2]){
							$item_set[$k][1] = $item_set[$k][2];
							unset($item_set[$k][2]);
						}elseif(!$item_set[$k][1] and !$item_set[$k][2] and $item_set[$k][3]){
							$item_set[$k][1] = $item_set[$k][3];
							unset($item_set[$k][3]);
						}
						if(!$item_set[$k][2] and $item_set[$k][3]){
							$item_set[$k][2] = $item_set[$k][3];
							unset($item_set[$k][3]);
						}*/
						foreach($item_set1[$k] as $a=>$b){
							$item_set[$k][] = $b;
						}
						foreach($item_set[$k] as $kk=>$vv){
							if($item_set[$k][$kk]['price']<$item_set[$k][$kk-1]['price']){
								$item_set_new[$k][$kk-1] = $item_set[$k][$kk];
								$item_set_new[$k][$kk] = $item_set[$k][$kk-1];
							}else{
								$item_set_new[$k][$kk] = $item_set[$k][$kk];
							}
						}
						$item_set = $item_set_new;
					}
				}
			}
		}

        //排除大通柴油
        foreach($item_sets as $_kk=>$_vv){
            if($_vv['id']==62){
                unset($item_sets[$_kk]);
            }
        }

		 //print_r($item_sets);
		 //print_r($item_set);

		$data['1'] = $item_sets;
		$data['2'] = $item_set['1'];
		$data['3'] = $item_set['2'];
		$data['4'] = $item_set['3'];
		// if($_REQUEST['item_set']==2){
		// 	$data = $item_set;
		// }elseif($_REQUEST['item_set']==1){
		// 	$data = $item_sets;
		// }

		$this->_ret($data);
	}

	//APP上门保养下单接口
	public function create_order(){
		

		// $data['uid'] = $_REQUEST['uid'];							//用户ID
		// $data['truename'] = $_REQUEST['truename'];					//姓名 
		// $data['mobile'] = $_REQUEST['mobile'];						//电话
		// $data['licenseplate'] = $_REQUEST['licenseplate'];			//车牌
		// $data['car_reg_time'] = $_REQUEST['car_reg_time'];			//车辆注册时间
		// $data['engine_num'] = $_REQUEST['engine_num'];				//发动机号码
		// $data['vin_num'] = $_REQUEST['vin_num'];					//VIN码
		// $data['address'] = $_REQUEST['address'];					//地址
		// $data['longitude'] = $_REQUEST['longitude'];				//经度
		// $data['latitude'] = $_REQUEST['latitude'];					//纬度
		// $data['model_id'] = $_REQUEST['model_id'];					//车型ID
		// $data['amount'] = $_REQUEST['amount'];						//价格
		// $data['dikou_amount'] = $_REQUEST['dikou_amount'];			//抵扣金额
		// $data['price_remark'] = $_REQUEST['price_remark'];			//价格备注
		// $data['item'] = $_REQUEST['item'];						//机油ID 数量 价格 实例化格式保存
		// $data['pay_type'] = $_REQUEST['pay_type'];					//支付方式
		// $data['pay_status'] = $_REQUEST['pay_status'];				//支付状态
		// $data['status'] = $_REQUEST['status'];						//订单状态
		// $data['order_type'] = $_REQUEST['order_type'];				//订单类型（保养订单 检测订单.......）
		// $data['is_del'] = $_REQUEST['is_del'];						//是否删除
		// $data['replace_code'] = $_REQUEST['replace_code'];			//抵用码
		// $data['order_time'] = $_REQUEST['order_time'];				//预约上门日期 2015-3-12
		// $data['order_time2'] = $_REQUEST['order_time2'];			//预约上门时间 10:00-11:00
		// $data['create_time'] = $_REQUEST['create_time'];			//下单时间
		// $data['update_time'] = $_REQUEST['update_time'];			//更新时间
		// $data['remark'] = $_REQUEST['remark'];						//备注
		// $data['operator_remark'] = $_REQUEST['operator_remark'];	
		// $data['technician_id'] = $_REQUEST['technician_id'];		//技师ID
		// $data['city_id'] = $_REQUEST['city_id'];					//城市

		// $data['oil_1_id'] = $_REQUEST['oil_1_id'];					//机油id 1
		// $data['oil_1_num'] = $_REQUEST['oil_1_num'];
		// $data['oil_2_id'] = $_REQUEST['oil_2_id'];					//机油id 2
		// $data['oil_2_num'] = $_REQUEST['oil_2_num'];

		// $data['CheckboxGroup0_res'] = $_REQUEST['CheckboxGroup0_res'];	//机油ID
		// $data['CheckboxGroup1_res'] = $_REQUEST['CheckboxGroup1_res'];	//机滤ID
		// $data['CheckboxGroup2_res'] = $_REQUEST['CheckboxGroup2_res'];	//空气滤ID
		// $data['CheckboxGroup3_res'] = $_REQUEST['CheckboxGroup3_res'];	//空调滤ID

		//$res = $reservation_order->add($data);

		//判断是否是同一个人下单子,防止客服下两个人的单子出现uidbug
		if ($_SESSION['ck_mobile'] != $_REQUEST['mobile'] || ( $_SESSION['ck_name'] != $_REQUEST['truename'] ) ) {
			unset($_SESSION['uid']);
		}
		if (!$_REQUEST['model_id']) {
			$this->_ret('请先选择车型',null,'');
		}
		unset($_SESSION['item_0'],$_SESSION['item_1'],$_SESSION['item_2'],$_SESSION['item_3']);
		
		if(is_numeric($_REQUEST['CheckboxGroup0_res'])){
			$_SESSION['item_0'] = intval($_REQUEST['CheckboxGroup0_res']);
			$_SESSION['oil_detail'] = array(
					$_REQUEST['oil_1_id'] => $_REQUEST['oil_1_num'],
					$_REQUEST['oil_2_id'] => $_REQUEST['oil_2_num']
			);
		}else{
			$this->_ret(null,0,'fail');
		}
		if(is_numeric($_REQUEST['CheckboxGroup1_res'])){
			$_SESSION['item_1'] = intval($_REQUEST['CheckboxGroup1_res']);
		}else{
			$this->_ret(null,0,'fail');
		}
		if(is_numeric($_REQUEST['CheckboxGroup2_res'])){
			$_SESSION['item_2'] = intval($_REQUEST['CheckboxGroup2_res']);
		}else{
			$this->_ret(null,0,'fail');
		}
		if(is_numeric($_REQUEST['CheckboxGroup3_res'])){
			$_SESSION['item_3'] = intval($_REQUEST['CheckboxGroup3_res']);
		}else{
			$this->_ret(null,0,'fail');
		}

		if($_SESSION['uid']){
		}else{
			$userinfo = $this->user_model->where(array('mobile'=>$_REQUEST['mobile'],'status'=>'1'))->find();
			if($userinfo){
				$_SESSION['uid'] = $userinfo['uid'];
			}else{
				$member_data['mobile'] = $_REQUEST['mobile'];
				$member_data['password'] = md5($_REQUEST['mobile']);
				$member_data['reg_time'] = time();
				$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
				$member_data['fromstatus'] = '51';//WEB上门保养
				$_SESSION['uid'] = $this->user_model->data($member_data)->add();
				$send_add_user_data = array(
						'phones'=>$_REQUEST['mobile'],
						'content'=>'您已注册成功，您可以使用您的手机号码'.$_REQUEST['mobile'].'，密码'.$_REQUEST['mobile'].'来登录携车网，客服4006602822。',
				);
				$this->curl_sms($send_add_user_data);  //Todo 内外暂不发短信
				$send_add_user_data['sendtime'] = time();
				$this->sms_model->data($send_add_user_data)->add();

				$data['createtime'] = time();
				$data['mobile'] = $_REQUEST['mobile'];
				$data['memo'] = '用户注册';
				$this->memberlog_model->data($data)->add();
			}
		}
		 
		// $yzm = I('post.yzm');
		// if($yzm && $yzm != $_SESSION['mobileeverify']){
		// 	$this->error('验证码不正确，预约失败',null,'/carservice/order');
		// }
		 
		$has_replace_code = false;
		if($_REQUEST['replace_code'] ){
			//总价减去抵用码的价钱
			$has_replace_code = true;
			$order_info['replace_code']= $_REQUEST['replace_code'];
			//unset($_SESSION['replace_code']);
		}
		 
		$order_info['uid'] = $_SESSION['uid'];
		$order_info['truename'] = $_REQUEST['truename'];
		$order_info['address'] = $_REQUEST['address'];
		$order_info['mobile'] = $_REQUEST['mobile'];
		$order_info['model_id'] = $_REQUEST['model_id'];
		$order_info['licenseplate'] = $_REQUEST['licenseplate_type'].$_REQUEST['licenseplate'];
		if($_REQUEST['car_reg_time']){
			$order_info['car_reg_time'] = strtotime($_REQUEST['car_reg_time']);
		}else{
			$order_info['car_reg_time'] = 0;
		}
		$order_info['engine_num'] = $_REQUEST['engine_num'];
		$order_info['vin_num'] = $_REQUEST['vin_num'];

		$order_info['order_time'] = $_REQUEST['order_time'];
		$order_time2 = intval($_REQUEST['order_time2']);
		$order_info['order_time'] = strtotime($order_info['order_time']) + $order_time2 * 3600;
		$order_info['pay_type'] = $_REQUEST['pay_type'];
		$order_info['create_time'] = time();
		$order_info['remark'] = $_REQUEST['remark'];
		$order_info['origin'] = 8;//新版app

		$oil_1_id = $oil_2_id = $oil_1_price = $oil_2_price = $filter_id = $filter_price = $kongqi_id = $kongqi_price = $kongtiao_id = $kongtiao_price = 0;
		 
		//计算总价
		if(!empty($_SESSION['item_0'])){
			if( $_SESSION['item_0'] == '-1' ){	//不是自备配件
				$item_list['0']['id'] = 0;
				$item_list['0']['name'] = "自备配件";
				$item_list['0']['price'] = 0;
			}else{
				//通过机油id查出订单数据
				$item_oil_price = 0;
				$oil_data = $_SESSION['oil_detail'];
				$i = 0;
				foreach ( $oil_data as $id=>$num){
					if($num > 0){
						$res = $this->item_oil_model->field('name,price')->where( array('id'=>$id))->find();
						if ($i == 0) {
							$oil_1_id = $id;
							$oil_1_price = $res['price']*$num;
							$i++;
						}else{
							$oil_2_id = $id;
							$oil_2_price = $res['price']*$num;
						}
						$item_oil_price += $res['price']*$num;
						$name = $res['name'];
					}
				}
				$item_list['0']['id'] = $_SESSION['item_0'];
				$item_list['0']['name'] = $name;
				$item_list['0']['price'] = $item_oil_price;
			}
		}
		if(!empty($_SESSION['item_1'])){
			if($_SESSION['item_1'] == '-1'){
				$item_list['1']['id'] = 0;
				$item_list['1']['name'] = "自备配件";
				$item_list['1']['price'] = 0;
			}else{
				$filter_id = $item_condition['id'] = $_SESSION['item_1'];
				$item_list['1'] = $this->item_model->where($item_condition)->find();
				$filter_price = $item_list['1']['price'];
			}
		}
		if(!empty($_SESSION['item_2'])){
			if($_SESSION['item_2'] == '-1'){
				$item_list['2']['id'] = 0;
				$item_list['2']['name'] = "自备配件";
				$item_list['2']['price'] = 0;
			}else{
				$kongqi_id = $item_condition['id'] = $_SESSION['item_2'];
				$item_list['2'] = $this->item_model->where($item_condition)->find();
				$kongqi_price = $item_list['2']['price'];
			}
		}
		if(!empty($_SESSION['item_3'])){
			if($_SESSION['item_3'] == '-1'){
				$item_list['3']['id'] = 0;
				$item_list['3']['name'] = "自备配件";
				$item_list['3']['price'] = 0;
			}else{
				$kongtiao_id = $item_condition['id'] = $_SESSION['item_3'];
				$item_list['3'] = $this->item_model->where($item_condition)->find();
				$kongtiao_price = $item_list['3']['price'];
			}
		}

		$item_amount = 0;
		if(is_array($item_list)){
			foreach ($item_list as $key => $value) {
				$item_amount += $value['price'];
			}
		}
		 
		$item_content = array(
				'oil_id'     =>	$_SESSION['item_0'],
				'oil_detail' => $_SESSION['oil_detail'],
				'filter_id'  => $_SESSION['item_1'],
				'kongqi_id'  => $_SESSION['item_2'],
				'kongtiao_id' =>$_SESSION['item_3'],
				'price'=>array(
						'oil'=>array(
								$oil_1_id=>$oil_1_price,
								$oil_2_id=>$oil_2_price
						),
						'filter'=>array(
								$filter_id=>$filter_price
						),
						'kongqi'=>array(
								$kongqi_id=>$kongqi_price
						),
						'kongtiao'=> array(
								$kongtiao_id=>$kongtiao_price
						)
				)
		);
		 
		$order_info['item'] = serialize($item_content);
		 
		$value = 0;
		if($has_replace_code){
			$value = $this->get_codevalue($order_info['replace_code']);
			$order_info['dikou_amount'] = $value;
			if ($value != 99){
				$order_info['amount'] = $item_amount +99 -$value;
			}else{
				$order_info['amount'] = $item_amount;
			}
		}else{
			$order_info['amount'] = $item_amount + 99;
		}
		$id = $this->reservation_order_model->add($order_info);
		//echo $this->reservation_order_model->getLastsql();
// var_dump($order_info);
// echo 2;echo $id;echo 1;//exit;
		if($id){
			$update = array('status'=>1);
			$where = array('coupon_code'=>$replace_code);
			$res = $this->carservicecode_model->where($where)->save($update);
			$array = array('order_id'=>$id,'pay_type'=>$_REQUEST['pay_type'],'price'=>$order_info['amount']);
			$this->_ret($array,1,'');
		}else{
			$appdata['status'] = '0';
			$appdata['msg'] = 'fail';
			$this->_ret(null,0,'fail');
		}
		//var_dump($this->reservation_order_model->getLastSql());exit;
		//插入数据到我的车辆
		//$this->_insert_membercar($order_info);
		
		$_SESSION['ck_mobile'] = $order_info['mobile'];
		$_SESSION['ck_name'] = $order_info['truename'];
		
		// if(I('post.pay_type')==2){
		// 	$url = WEB_ROOT.'/weixinpaytest/nativecall.php?membercoupon_id='.$id.'&all_amount='.$order_info['amount'].'&coupon_name=上门保养套餐';
		// 	//$url = '/carservice/orderpay-id-'.$id;
		// 	$this->success('预约成功',$url,true);
		// }else{
		// 	$this->success('预约成功',U('/myhome/carservice_order'),true);
		// }

		
		

	}

	//验证上门保养抵扣码功能
	function valid_replace_code(){
		$replace_code = @$_REQUEST['coupon_code'];
		//$order_id = @$_POST['order_id'];
			if(!$replace_code){
				$data = array(
				'state' => '0',
				'msg'=>'param error',
				'data'=>''
				);
				echo json_encode($data);
				exit;
			}

			if($replace_code=='016888'){
				$data = array(
				'state' => '1',
				'msg'=>'验证成功',
				'data'=> '20'
				);
				echo json_encode($data);
				exit;
			}else{
				$mReservation = D('reservation_order');
				$dataReservation = $mReservation->where( array( 'replace_code'=>$replace_code ) )->count();
				if ( $dataReservation>0 ) {
					$data = array(
					'state' => '0',
					'msg'=>'该验证码已经使用过',
					'data'=>''
					);
					echo json_encode($data);
					exit;
				}
				$mCarservicecode = D('carservicecode');//上门保养抵用码字段
				$count = $mCarservicecode->where( array('coupon_code'=>$replace_code,'status'=>0) )->count();
				if(!$count){
					$data = array(
					'state' => '0',
					'msg'=>'抵扣券错误',
					'data'=>''
					);
					echo json_encode($data);
					exit;
				}else{
					$reduce = $this->get_codevalue($replace_code);//获取优惠券价格
					$data = array(
							'state' => '1',
							'msg'=>'验证成功',
							'data'=> $reduce
					);
					echo json_encode($data);
					exit;
				}
			}
			
	}
	/*
        @author:ws
        @function:显示店铺搜索页
        @time:2013-08-04
    */
	public function shop_list()
	{
		$shop_city = empty($_REQUEST['city_id']) ? '3306' : $_REQUEST['city_id'];
		$shop_area = isset($_REQUEST['area_id']) ? $_REQUEST['area_id'] : 0;

		$series_id = isset($_REQUEST['series_id']) ? $_REQUEST['series_id'] : 0;

		$mshop = D('shop');
		$model_shop_fs_relation = D('Shop_fs_relation');
		$model_carseries = D('Carseries');

		if ($series_id) {
			$map_series['series_id'] = $series_id;

			$series_info = $model_carseries->where($map_series)->find();

			$map_fs['fsid'] = $series_info['fsid'];
			$shop_fs = $model_shop_fs_relation->where($map_fs)->select();
			$shop_id_arr = array();
			if ($shop_fs) {
				foreach ($shop_fs as $s_k => $s_v) {
					$shop_id_arr[] = $s_v['shopid'];
				}
			}
			$shop_id_str = implode(',', $shop_id_arr);
			$map_shop['id'] = array('in', $shop_id_str);
		}

		$page_size = 25;
		$map_shop['status'] = 1;
		$map_shop['shop_city'] = $shop_city;


		$count = $mshop->where($map_shop)->count();// 查询满足要求的总记录数
		$Page = new \Think\Page($count, $page_size);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$show = $Page->show();// 分页显示输出

		if ($_REQUEST['order'] && $_REQUEST['order'] == 'have_coupon') {

			$shop_info = $mshop->where($map_shop)->field('id,logo,shop_name,shop_address,comment_rate,have_coupon,product_sale,shop_maps,fsid')->order('have_coupon desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		} else if ($_REQUEST['order'] == 'distance' and $_REQUEST['lat'] and $_REQUEST['long']) {

			$shops = $mshop->where($map_shop)->field('id,logo,shop_name,shop_address,comment_rate,have_coupon,product_sale,shop_maps,fsid')->order("have_coupon DESC,shop_class ASC,comment_rate DESC")->select();

			if ($shops) {
				foreach ($shops as $_key => $_val) {
					$shop_maps = $_val['shop_maps'];
					$shop_maps_arr = explode(',', $shop_maps);
					$shops[$_key]['distance'] = $this->GetDistance($_REQUEST['lat'], $_REQUEST['long'], $shop_maps_arr[1], $shop_maps_arr[0]);
				}
				$count = count($shops);
				for ($i = 0; $i < $count; $i++) {
					for ($j = $count - 1; $j > $i; $j--) {
						if ($shops[$j]['distance'] < $shops[$j - 1]['distance']) {
							//交换两个数据的位置
							$temp = $shops [$j];
							$shops [$j] = $shops [$j - 1];
							$shops [$j - 1] = $temp;
						}
					}
				}
				$p_count = ceil($count / $page_size);
				for ($ii = 0; $ii < $p_count; $ii++) {
					$page_shops[$ii] = array_slice($shops, $ii * $page_size, $page_size);
				}
				$p = isset($_REQUEST['p']) ? $_REQUEST['p'] : 1;
				if ($p == 0) {
					$p = 1;
				}
				$shop_info = $page_shops[$p - 1];
			}
		} else if ($_REQUEST['order'] && $_REQUEST['order'] == 'sale') {
			if ($series_id) {
				$shop_info = $mshop->table(array('xc_shop' => 'shop', 'xc_timesale' => 'timesale', 'xc_timesaleversion' => 'timesaleversion'))
					->where('shop.id in (' . $shop_id_str . ') and shop.id=timesale.shop_id and timesale.id=timesaleversion.timesale_id and shop.status = 1')->order('timesaleversion.product_sale DESC')
					->field('shop.id,shop.logo,shop.shop_name,shop.shop_address,shop.comment_rate,shop.have_coupon,timesaleversion.product_sale,shop.shop_maps,fsid')->limit($Page->firstRow . ',' . $Page->listRows)->select();
			} else {
				$shop_info = $mshop->table(array('xc_shop' => 'shop', 'xc_timesale' => 'timesale', 'xc_timesaleversion' => 'timesaleversion'))
					->where('shop.id=timesale.shop_id and timesale.id=timesaleversion.timesale_id and shop.status = 1')->order('timesaleversion.product_sale DESC')
					->field('shop.id,shop.logo,shop.shop_name,shop.shop_address,shop.comment_rate,shop.have_coupon,timesaleversion.product_sale,shop.shop_maps,fsid')->limit($Page->firstRow . ',' . $Page->listRows)->select();
			}

		} else {
			$shop_info = $mshop->where($map_shop)->field('id,logo,shop_name,shop_address,comment_rate,have_coupon,product_sale,shop_maps,fsid')->limit($Page->firstRow . ',' . $Page->listRows)->select();
		}
		//echo $mshop->getLastSql();

		foreach ($shop_info as $key => $value) {
			$shop_info[$key]['logo'] = $value['id'] . '.jpg';
			//$shop_info[$key]['has_tuan'] = $value['have_coupon'];

			$shop_info[$key]['lowest_discount'] = $value['product_sale'];
			$shop_info[$key]['positive_rating'] = $value['comment_rate'];

			if ($_REQUEST['order'] and $_REQUEST['order'] != 'distance' and $_REQUEST['lat'] and $_REQUEST['long']) {
				$shop_maps2 = $value['shop_maps'];
				$shop_maps_arr2 = explode(',', $shop_maps2);
				$shop_info[$key]['distance'] = $this->GetDistance($_REQUEST['lat'], $_REQUEST['long'], $shop_maps_arr2[1], $shop_maps_arr2[0]);
			}

			$coudata['fsid'] = $value['fsid'];
			$coudata['is_delete'] = 0;
			$coudata['coupon_type'] = 1;//现金券
			$resCou = $this->CouponModel->where($coudata)->find();
			if ($resCou) {
				$shop_info[$key]['has_coupon'] = 1;
			} else {
				$shop_info[$key]['has_coupon'] = 0;
			}


			$coudata['coupon_type'] = 2;
			$resCou = $this->CouponModel->where($coudata)->find();
			if ($resCou) {
				$shop_info[$key]['has_tuan'] = 1;
			} else {
				$shop_info[$key]['has_tuan'] = 0;
			}
		}
		if($_REQUEST['p']){
			$data['start'] = $_REQUEST['p'];
		}else{
			$data['start'] = 1;
		}
		$data['count'] = $count;
		$data['shops'] = $shop_info;

		$this->_ret($data);


	}

	public function shop_info()
	{
		if ($_REQUEST['id']) {
			$mshop = D('shop');
			$mtimesale = D('timesale');
			$mtimesaleversion = D('timesaleversion');
			$mshopdetail = D('shopdetail');
			$mcoupon = D('coupon');
			$shop_id =  $_REQUEST['id'];
			$where['id'] = $shop_id;

			$resShop = $mshop->where('id = ' . $_REQUEST['id'] . ' and status = 1')->field('id,fsid,shop_name,shop_address,shop_maps,shop_phone,shop_account')->find();
			// $resShop = $resShop[0];

			if ($resShop) {
				$resShop['hour_discounts'] = array();
				$resTimesale = $mtimesale->where(array('shop_id' => $_REQUEST['id'], 'status' => 1))->select();

				if ($resTimesale) {
					foreach ($resTimesale as $t_key => $t_value) {


						$resTimesaleversion = $mtimesaleversion->where(array('timesale_id' => $resTimesale[$t_key]['id'], 'status' => 1))->select();

						if ($resTimesaleversion) {
							foreach ($resTimesaleversion as $t2_key => $t2_value) {
								$resShop['hour_discounts'][$t2_key]['discount_rate'] = $resTimesaleversion[$t2_key]['workhours_sale'];
								$resShop['timesaleversion_id'] = $resTimesaleversion[$t2_key]['id'];
								$resShop['hour_discounts'][$t2_key]['days'] = $resTimesale[$t_key]['week'];
								$resShop['hour_discounts'][$t2_key]['time_duration'] = $resTimesale[$t_key]['begin_time'] . ' - ' . $resTimesale[$t_key]['end_time'];
							}
							//var_dump($resShop['hour_discounts']);
						}
					}
				}
				$map['shop_id'] = $shop_id;
				$map['is_delete'] =0;
				$Shop_coupon = $mcoupon->where($map)->field('id, coupon_name,coupon_type as type')->select();
				if($Shop_coupon){
					$resShop['coupons']=array();
					foreach ($Shop_coupon as $k => $v) {
						if ($v['type'] == 2) {
							$Shop_coupon[$k]['type_name'] = '团购券';
						} elseif ($v['type'] == 1) {
							$Shop_coupon[$k]['type_name'] = '现金券';
						}
					}
					$resShop['coupons']= $Shop_coupon;
				}else{
					$resShop['coupons']= null;
				}

				$resShop['address'] = $resShop['shop_address'];
				if ($resShop['shop_maps']) {
					$arr = explode(',', $resShop['shop_maps']);
					$resShop['latitude'] = $arr[1];
					$resShop['longitude'] = $arr[0];
				} else {
					$resShop['latitude'] = 0;
					$resShop['longitude'] = 0;
				}
				$resShop['phone'] = $resShop['shop_phone'];

				$resShop['image'] = $resShop['id'] . '.jpg';


				//品牌logo
				$mcarseries = D('carseries');
				$mcarbrand = D('carbrand');
				$mshop_fs_relation = D('shop_fs_relation');
				if ($_REQUEST['series_id']) {
					$resbrandid = $mcarseries->where('series_id = ' . $_REQUEST['series_id'])->find();
					if ($resbrandid) {
						$resbrandlogo = $mcarbrand->where(array('brand_id' => $resbrandid['brand_id']))->find();
						if ($resbrandlogo) {
							$resShop['brand_logo'] = $resbrandlogo['brand_logo'];
						}
					}
				} else {

					$resfsid = $mshop_fs_relation->where('shopid = ' . $resShop['id'])->find();

					if ($resfsid) {
						$resbrandid = $mcarseries->where('fsid = ' . $resfsid['fsid'])->find();
					} else {
						$resbrandid = $mcarseries->where('fsid = ' . $resShop['fsid'])->find();
					}

					if ($resbrandid) {
						$resbrandlogo = $mcarbrand->where(array('brand_id' => $resbrandid['brand_id']))->find();
						if ($resbrandlogo) {
							$resShop['brand_logo'] = $resbrandlogo['brand_logo'];
						}
					}
				}

				//描述
				$resdetail = $mshopdetail->where($where)->find();

				if ($resdetail) {
					$resShop['description'] = $resdetail['shop_detail'];
				}


				$rescoupon = $mcoupon->where(array('fsid' => $resShop['fsid']))->select();


				$data = $resShop;
				$this->_ret($data);
			}else{
				$this->_ret('店铺ID不正确');
			}

		} else {
			$this->_ret('店铺ID缺失');
		}

	}

	public function create_order_4s()
	{
		if (! $_POST ['brand_id']) {
            $this->_ret(null,0,'请先选择品牌');exit;
        }
		if (! $_POST ['series_id']) {
            $this->_ret(null,0,'请先选择车系');exit;
        }
		if (! $_POST ['model_id']) {
            $this->_ret(null,0,'请先选择车型'); exit;
        }
        if (empty ( $_POST ['xcagreement'] )) {
			$this->_ret(null,0,'请勾选同意携车网维修保养预约协议!');exit;
           // $this->error ( '请勾选同意携车网维修保养预约协议！', '', true );
        }
        if (empty ( $_POST ['order_date'] )) {
			$this->_ret(null,0,'请选择预约日期!');exit;
           // $this->error ( '请选择预约日期！', '', true );
        }

        if (empty ( $_POST ['truename'] )) {
			$this->_ret(null,0,'姓名不能为空!'); exit;
            //$this->error ( '姓名不能为空！', '', true );
        }
        if (empty ( $_POST ['mobile'] )) {
			$this->_ret(null,0,'手机号不能为空!'); exit;
           // $this->error ( '手机号不能为空！', '', true );
        }
        if (empty ( $_POST ['licenseplate'] )) {
			$this->_ret(null,0,'车牌号不能为空!'); exit;
            //$this->error ( '车牌号不能为空！', '', true );
        }
		if ($_REQUEST ['select_services']&& !empty($_REQUEST ['select_services']) ) {
			$select_services = $_REQUEST ['select_services'];
		}

		if($_REQUEST ['services_ios']){
			$byte1 =$_REQUEST ['services_ios'] ;
			$byte2 =  implode(',',$byte1);
			$byte3 = str_replace(' ','',$byte2);
			$byte4 = str_replace('(','',$byte3);
			$byte5 = str_replace(')','',$byte4);
			$byte6 = str_replace(PHP_EOL,'',$byte5);
			$select_services = explode(',',$byte6);
		}
       // $CityId = $this->GetCityId ( $_SESSION ['area_info'] [0] ); // 得到城市ID
		// 根据提交过来的预约时间，做判断(暂时先注销)
		if ($_POST ['order_date']) {
			// 载入产品MODEL
			$model_product = D('Product');
			// $map['product_id'] = array('in',$_REQUEST['product_str']);
			$request_headers = getallheaders();
			$uid = $request_headers['uid'];
			$order_time = $_POST ['order_date'] . ' ' . $_POST ['order_hours'] . ':' . $_POST ['order_minute'];
			$order_time = strtotime($order_time);

			$now = time();
			$fourhour = strtotime(date('Y-m-d') . ' 16:00:00');
			if ($now < $fourhour) {
				$min = 1;
				$max = 15;
			} else {
				$min = 2;
				$max = 16;
			}
			if ($order_time > (time() + 86400 * $max)) {
				$this->_ret(null,0,'最多预约15天以内,请重新选择！'); exit;
			}
			if (!$u_c_id = $_POST ['u_c_id']) {
				$u_c_id = 0;
			}
			$save_discount = 0.00;
			$productversion_ids_str = '';
			if ($_REQUEST ['membercoupon_id']) {
				$membercoupon_id = $_REQUEST ['membercoupon_id'];
				$model_membercoupon = D('Membercoupon');
				$map_mc ['membercoupon_id'] = $_REQUEST ['membercoupon_id'];
				$membercoupon = $model_membercoupon->where($map_mc)->find();
				$coupon_id = $membercoupon ['coupon_id'];
				$model_coupon = D('Coupon');
				$map_c ['id'] = $coupon_id;
				$coupon = $model_coupon->where($map_c)->find();
				$model_id = $coupon ['model_id'];
				$select_servicess = $coupon ['service_ids'];
				$shop_id = $coupon ['shop_id'];
				$total_price = $coupon ['coupon_amount'];
				$cost_price = $coupon ['cost_price'];
				$jiesuan_money = $coupon ['jiesuan_money'];
				$save_price = $cost_price - $total_price;
				$order_type = $coupon ['coupon_type'];
				$data ['order_name'] = $coupon ['coupon_name'];
				$data ['order_des'] = $coupon ['coupon_summary'];
			} else {
				$order_type = 4;
				//$select_services = $_REQUEST ['select_services'];
				if ($select_services) {
					$map ['service_id'] = array(
						'in',
						$select_services
					);
					$map ['model_id'] = array(
						'eq',
						$_REQUEST ['model_id']
					);
					$list_product = $model_product->where($map)->select();
				}

				$timesale_model = D('Timesale');
				$map_tsv ['xc_timesaleversion.id'] = $_POST ['timesaleversion_id'];
				$sale_arr = $timesale_model->where($map_tsv)->join("xc_timesaleversion ON xc_timesale.id=xc_timesaleversion.timesale_id")->find();
				if ($order_time > $sale_arr ['s_time'] and $order_time < $sale_arr ['e_time']) {
					$order_week = date("w", $order_time);
					$normal_week = explode(',', $sale_arr ['week']);

					if (!in_array($order_week, $normal_week)) {
						$this->_ret(null, 0, '预约时间错误,请重新选择！');exit;
					}
					$order_hour = date("H:i", $order_time);

					if (strtotime(date('Y-m-d') . ' ' . $order_hour) < strtotime(date('Y-m-d') . ' ' . $sale_arr ['begin_time']) || strtotime(date('Y-m-d') . ' ' . $order_hour) > strtotime(date('Y-m-d') . ' ' . $sale_arr ['end_time'])) {
						$msg = '服务范围为：' . $sale_arr ['begin_time'] . '到' . $sale_arr ['end_time'] . ',您预约时间不在服务时间范围内,请重新选择预约时间！';
						$this->_ret(null, 0, $msg);
						exit;
					}
				} else {
					$this->_ret(null, 0, '预约时间错误,请重新选择！！');exit;
				}
				// echo '<pre>';print_r($sale_arr);exit;

				// 计算订单总价格
				$total_product_price = 0;
				$total_workhours_price = 0;
				$productversion_ids_arr = array();
				if (!empty ($list_product)) {
					foreach ($list_product as $kk => $vv) {
						$productversion_ids_arr [] = $vv ['versionid'];
						$list_product [$kk] ['list_detai'] = unserialize($vv ['product_detail']);
						if (!empty ($list_product [$kk] ['list_detai'])) {
							foreach ($list_product [$kk] ['list_detai'] as $key => $val) {
								$list_product [$kk] ['list_detai'] [$key] ['total'] = $val ['price'] * $val ['quantity'];
								if ($val ['Midl_name'] == '工时费') {
									$total_workhours_price += $list_product [$kk] ['list_detai'] [$key] ['total'];
								} else {
									$total_product_price += $list_product [$kk] ['list_detai'] [$key] ['total'];
								}
							}
						}
					}
				}
				$cost_price = $total_workhours_price + $total_product_price;
				$jiesuan_money = 0;
				$productversion_ids_str = implode(",", $productversion_ids_arr);

				$total_price = 0;
				$save_price = 0;

				if (!empty ($sale_arr)) {
					if ($sale_arr ['product_sale'] > 0) {
						$total_price += $total_product_price * $sale_arr ['product_sale'];
					} else {
						$total_price += $total_product_price;
					}
					/*
                     * if ($sale_arr['workhours_sale']=='0.00'){ $sale_arr['workhours_sale'] = 1; }
                     */
					$workhours_sale = $sale_arr ['workhours_sale'];
					if ($workhours_sale > 0) {
						$total_price += $total_workhours_price * $workhours_sale;
						$save_price = $total_workhours_price * ($sale_arr ['workhours_sale'] - $workhours_sale);
						$save_discount = $sale_arr ['workhours_sale'] - $workhours_sale;
					} else {
						$total_price += $total_workhours_price * 0;
						$save_price = $total_workhours_price;
						$save_discount = $sale_arr ['workhours_sale'];
					}
				} else {
					$total_price += $total_product_price + $total_workhours_price;
				}
				$membercoupon_id = 0;
				$coupon_id = 0;
			}
			if ($sale_arr ['product_sale']) {
				$product_sale = $sale_arr ['product_sale'];
			} else {
				$product_sale = 0;
			}
			if ($sale_arr ['workhours_sale']) {
				$workhours_sale = $sale_arr ['workhours_sale'];
			} else {
				$workhours_sale = 0;
			}
			if ($_REQUEST ['member_id']) {
				$data ['member_id'] = $_REQUEST ['member_id'];
			}
			$data ['u_c_id'] = $u_c_id;
			$data ['uid'] = $uid?$uid:0;
			$data ['shop_id'] = $_POST ['shop_id'];
			$data ['brand_id'] = $_POST['brand_id'];
			$data ['series_id'] = $_POST['series_id'];
			$data ['model_id'] = $_POST['model_id'];
			$data ['timesaleversion_id'] = $_POST ['timesaleversion_id'];
			$str = implode(',',$select_services);
			$data ['service_ids'] = $str?$str:'';
			$data ['product_sale'] = $product_sale;
			$data ['workhours_sale'] = $workhours_sale;
			$data ['truename'] = $_POST ['truename'];
			$data ['mobile'] = $_POST ['mobile'];
			$data ['licenseplate'] = trim($_POST ['cardqz'] . $_POST ['licenseplate']);
			if ($_POST ['miles']) {
				$data ['mileage'] = $_POST ['miles'];
			} else {
				$data ['mileage'] = 0;
			}
			if ($_POST ['car_sn']) {
				$data ['car_sn'] = $_POST ['car_sn'];
			} else {
				$data ['car_sn'] = 0;
			}
			$data ['remark'] = $_POST ['remark'];
			$data ['order_time'] = $order_time;
			$data ['create_time'] = time();
			$data ['total_price'] = $total_price;
			$data ['cost_price'] = $cost_price;
			$data ['jiesuan_money'] = $jiesuan_money;
			$data ['productversion_ids'] = $productversion_ids_str;
			$data ['coupon_save_money'] = $save_price;
			$data ['coupon_save_discount'] = $save_discount;
			$data ['membercoupon_id'] = $membercoupon_id;
			$data ['coupon_id'] = $coupon_id;
			$data ['order_type'] = $order_type;


			if ($_REQUEST ['ra_servicemember_id']) {
				$data ['servicemember_id'] = $_REQUEST ['ra_servicemember_id'];
			}
			//app下单
			$data ['is_app'] = 1;

			$model = D('Order');
			if ($uid) {
				$data ['city_id'] = 0;
				/*
                // 抵用券选择[1]50元抵用券 [2]车会抵用券
                $radio_sale = $_POST ['radio_sale'];
                $code = $_POST ['code'];
                if ($radio_sale || $code) {
                    $membersalecoupon_id = $this->add_salemembercoupon ( $uid, $radio_sale, $code, $data ['shop_id'] );
                    $data ['membersalecoupon_id'] = $membersalecoupon_id;
                }
                */
				$order_num_id = $model->add($data);
				$sql1 =  $model->getLastsql();
				$this->addCodeLog('4s订单',$sql1);
				if (false != $order_num_id) {
					$_POST ['order_id'] = $model->getLastInsID();
					/*$this->MembersalecouponModel->where ( array (
                            'membersalecoupon_id' => $membersalecoupon_id
                    ) )->save ( array (
                            'order_id' => $_POST ['order_id']
                    ) );*/
				}
				$model_member = D('Member');
				$get_user_name = $model_member->where("uid=$uid")->find();
				if ($list_product) {
					foreach ($list_product as $k => $v) {
						$sub_order [] = array(
							'order_id' => $_POST ['order_id'],
							'productversion_id' => $list_product [$k] ['versionid'],
							'service_id' => $list_product [$k] ['service_id'],
							'service_item_id' => $list_product [$k] ['service_item_id'],
							'uid' => $uid,
							'user_name' => $get_user_name ['username'],
							'create_time' => time(),
							'update_time' => time()
						);
					}
					$model_suborder = D('Suborder');
					$list = $model_suborder->addAll($sub_order);
				}
			} else {
				//print_r($data);
				$model = D('Ordernologin');
				$id = $model->add($data);
				$sql2 = $model->getLastsql();
				$this->addCodeLog('4s订单',$sql2.'||'.$select_services.'|1|'.$byte1.'|2|'.$byte2.'|3|'.$byte3.'|4|'.$byte4.'|5|'.$byte5.'|6|'.$byte6);
				if ($id) {
					$_POST ['order_id'] = $model->getLastInsID();
				}
			}
			if (! empty ( $_POST ['order_id'] )) {
				if ($uid) {
					$array = array('order_id' => $_POST ['order_id'], 'pay_type' => $_REQUEST['pay_type']);
					$this->_ret($array, 1, '预约提交成功！'); exit;
					// $this->success ( '预约提交成功！', __APP__ . '/myhome',true );
				} else {
					$array = array('order_id' => $_POST ['order_id'], 'pay_type' => $_REQUEST['pay_type']);
					$this->_ret($array, 1, '预约提交成功！'); exit;
					// $this->success ( '预约提交成功！', __APP__ . '/index',true );
				}
			} else {
				$this->_ret(null, 0, '预约失败！');
				// $this->error ( '预约失败！', __APP__ . '/myhome',true );
			}
		}
	}

	//获取4S检查项目
	function shop_order_items()
	{
		$serviceitem = D('serviceitem');
		$array = array('0' => array('item_id' => 1, 'name' => "常规保养"), '1' => array('item_id' => 2, 'name' => "常规替换及维修"), '2' => array('item_id' => 3, 'name' => "清洗项目"));
		$item = $serviceitem->where(array('service_item_id' => 1))->field('id,name')->select();
		$array['0']['items'] = $item;
		$item = $serviceitem->where(array('service_item_id' => 2))->field('id,name')->select();
		$array['1']['items'] = $item;
		$item = $serviceitem->where(array('service_item_id' => 3))->field('id,name')->select();
		$array['2']['items'] = $item;
		//print_r($array);
		$this->_ret($array);
	}


	//Android tags
	function PushTags_android($from, $to, $content, $senduser)
	{
		import("Org.Util.Xinge");
		$push = new \XingeApp(2100098564, '8324049b42183a13a38cac8bdd451aa9');
		$mess = new \Message();
		if ($senduser == 1) {
			$tags = $from . "-" . $to;
		} else if ($senduser == 2) {
			$tags = "test1";
		}
		$mess->setExpireTime(0);
		$mess->setTitle('title');
		$mess->setContent($content);
		$custom = array('from' => $from, 'content' => $content, 'to' => $to, 'date' => time());
		$mess->setCustom($custom);
		$mess->setType(\Message::TYPE_MESSAGE);
		$tagList = array($tags);
		$ret = $push->PushTags(0, $tagList, 'OR', $mess);
		return ($ret);
	}

	//IOStags发送
	function PushTags_ios($from, $to, $content, $senduser)
	{
		import("Org.Util.Xinge");
		$push = new \XingeApp(2200098565, '295a010e4a6b88e32a9f2fe03918116c');
		$mess = new \MessageIOS();
		if ($senduser == 1) {
			$tags = $from . "-" . $to;
		} else if ($senduser == 2) {
			$tags = "test1";
		}
		$mess->setExpireTime(0);
		$mess->setAlert($content);
		$custom = array('from' => $from, 'content' => $content, 'to' => $to, 'date' => time());
		$mess->setCustom($custom);
		$tagList = array($tags);
		$ret = $push->PushTags(0, $tagList, 'OR', $mess, \XingeApp::IOSENV_DEV);
		return ($ret);
	}

	//发给客服

	function send_message()
	{
		if ($_REQUEST['from'] && $_REQUEST['to'] && $_REQUEST['content']) {
			if ($_REQUEST['to'] == 888) {
				$s_ios = $this->PushTags_ios("" . $_REQUEST['from'] . "", "" . $_REQUEST['to'] . "", "" . $_REQUEST['content'] . "", 2);
				$s_android = $this->PushTags_android($_REQUEST['from'], $_REQUEST['to'], $_REQUEST['content'], 2);
			} else {
				$s_ios = $this->PushTags_ios("" . $_REQUEST['from'] . "", "" . $_REQUEST['to'] . "", "" . $_REQUEST['content'] . "", 1);
				$s_android = $this->PushTags_android($_REQUEST['from'], $_REQUEST['to'], $_REQUEST['content'], 1);
			}

			$custom = array('from' => $_REQUEST['from'], 'content' => $_REQUEST['content'], 'to' => $_REQUEST['to'], 'date' => time());
			//var_dump($s_ios['ret_code']);exit;
			if ($s_ios['ret_code'] == 0 || $s_android['ret_code'] == 0) {
				$this->_ret($custom,1,'发送成功');
				/*$data['state'] = 'success';
				$data['msg'] = '发送成功';
				$data['data'] = $custom;*/
			} else {
				$this->_ret(null,0,'发送失败');
			/*	$data['state'] = 'fail';
				$data['message'] = '发送失败';
				$data['data'] = $custom;*/
			}
		} else {
			$this->_ret(null,0,'参数错误');
			/*$data['state'] = 'fail';
			$data['msg'] = '参数错误';
			$data['data'] = $custom;*/
		}
	}
}