<?php
class CrontabAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->MembercouponModel = D('membercoupon');//优惠卷信息
		$this->InsuranceModel = D('Insurance');//优惠卷信息
	}

	/*
		@author:chf
		@function:脚本(处理优惠卷如超过15天没付款的自动为删除状态 is_delete = 1)
		@time:2013-04-12
	*/

	function Breakmembercoupon(){
		exit;
		$time = time()- 86400*15;
		$Membercoupon = $this->MembercouponModel->where(array('create_time'=>array('lt',$time),'is_delete'=>0,'is_pay'=>0))->select();
	
		if($Membercoupon){
			foreach($Membercoupon as $k=>$v){
				//$this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->save(array('is_delete'=>1));
			}
		
		}
		echo "OK";
		
	}

	/*
		@author:ysh
		@function:脚本(处理用户提交保险竞价的有效期 更改状态)
		@time:2013/5/13
	*/
	function update_insurance_status(){
		$map['validity_time'] = array('lt',time());
		$map['insurance_status'] = 1;
		$map['state'] = 1;

		$data['insurance_status'] = 2;
		$this->InsuranceModel->where($map)->save($data);

		echo "OK";		
	}

	/*
		@author:ysh
		@function:脚本(给4s店负责人发送当天预约的用户信息)
		@time:2013/7/19
	*/
	function sendSMS_to_4S() {
		//您好，车辆沪EH8665通过携车网预约今天 09:00到贵店（上海鹏宇-东风雪铁龙4s店）进行大保养（机油、三滤），工时费8折，配件费无折扣，请您予以关照，如有问题请致电携车网4006602822，谢谢
		$model_order = D("Order");
		$model_shop = D("Shop");
		$model_service = D("Serviceitem");

		$start_time = strtotime(date("Y-m-d",time()));
		$end_time = strtotime(date("Y-m-d",time()))+86400;

		$map['status'] = 1;
		$map['order_state'] = 1;
		$map['order_time'] =  array(array('egt',$start_time),array('elt',$end_time));
		$order_list = $model_order->where($map)->select();
		if($order_list) {
			foreach($order_list as $key=>$val) {
				//service_ids
				$shop_info = $model_shop->find($val['shop_id']);
				if($shop_info['shop_mobile'] != "") {

					$service_map['id'] = array('in',$val['service_ids']);
					$services_list = $model_service->where($service_map)->select();
					if($services_list) {
						foreach($services_list as $k=>$v) {
							$services_str .= $v['name']."，";
						}
					}
					$send_add_order_data = array(
						'phones'=>$shop_info['shop_mobile'],
						'content'=>"您好，车辆 ".$val['licenseplate']." 通过携车网预约今天".date("H:i",$val['order_time'])."到贵店（".$shop_info['shop_name']."）进行".$services_str." 工时费".($val['workhours_sale']*10)."折，配件费".($val['product_sale']*10)."折，请您予以关照，如有问题请致电携车网4006602822，谢谢",
					);
					unset($services_str);
					
					$return_data = $this->curl_sms($send_add_order_data);
					$model_sms = D('Sms');
					$now = time();
					$send_add_order_data['sendtime'] = $now;
					$model_sms->add($send_add_order_data);
					
				}
			}
		}
		echo "OK";
	}

	/*
		@author:ysh
		@function:脚本(下单时间在15天以前的，状态设置为失效)
		@time:2013/7/23
	*/
	function update_membercoupon_delete() {
		$model_membercoupon = D('Membercoupon');
		
		$start_time = date("Y-m-d",time()-86400*15);
		$map['is_delete'] = 0;
		$map['is_pay'] = 0;
		$map['is_use'] = 0;
		$map['create_time'] = array('elt'=>$start_time);
		
		$data['is_delete'] = 1;
		$model_membercoupon->where($map)->save($data);

		echo "OK";
	}

	/*
		@author:ysh
		@function:脚本(导入用户数据)
		@time:2013-10-17 
	*/
	function insert_member() {
		$model_membernew = D('Membernew');
		$model_member= D('Member');
		
		
		$membernew = $model_membernew->query("SELECT * FROM xc_membernew WHERE is_insert=0 AND not exists (SELECT * FROM xc_member WHERE xc_membernew.mobile=xc_member.mobile) ORDER BY uid LIMIT 1");

		$data['password'] = md5(",,,,,,,,,,");
		$data['mobile'] = $membernew[0]['mobile'];
		$data['fromstatus'] = '9';
		$data['reg_time'] = time();
		$data['memo'] = "短信用户  ".$membernew[0]['text'];


		$model_member->data($data)->add();
		
		$model_membernew->where(array("uid"=>$membernew[0][uid]))->save(array('is_insert'=>1));
		echo "ok";		
	}

	/*
		@author:ysh
		@function:脚本(插入订单数据)
		@time:2013-11-21 
	*/
	function insert_order() {
		$model_membernew = D('Membernew');
		$model_member= D('Member');
		$model_order = D('Order');

		//$membernew = $model_membernew->query("SELECT * FROM xc_membernew WHERE is_insert=0 AND not exists (SELECT * FROM xc_member WHERE xc_membernew.mobile=xc_member.mobile) ORDER BY uid LIMIT 1");
		$membernew = $model_membernew->query("SELECT * FROM xc_membernew WHERE is_insert=1 AND is_order_insert=1 ORDER BY uid LIMIT 1");
		
		foreach($membernew as $key=>$val) {
			$data = array();
			//$data['password'] = md5(",,,,,,,,,,");
			$data['mobile'] = $val['mobile'];
			$data['fromstatus'] = '30';
			//$data['reg_time'] = time();
			//$data['memo'] = "短信用户  ".$val['text'];

			$member = $model_member->where($data)->find();
			$shop_id = $val['shop_id'];

			if($val['shop_id'] == 54 || $val['shop_id'] == 56) {
				$shop_ids = "58,121,345,346,356,362,364,1345,1356,55,125,57,370,115,56,1330,348,1346";
				$shop_ids_arr = explode("," , $shop_ids);
				$arr_index = array_rand($shop_ids_arr);
				$shop_id = $shop_ids_arr[$arr_index];
			}
			if($val['shop_id'] == 8) {
				$shop_ids = "10,87,297,304,306,9,11,298,299,118,289,291,1338,1344";
				$shop_ids_arr = explode("," , $shop_ids);
				$arr_index = array_rand($shop_ids_arr);
				$shop_id = $shop_ids_arr[$arr_index];
			}
			if($val['shop_id'] == 62 || $val['shop_id'] == 103) {
				$shop_ids = "62,536,538,541,542,120,537";
				$shop_ids_arr = explode("," , $shop_ids);
				$arr_index = array_rand($shop_ids_arr);
				$shop_id = $shop_ids_arr[$arr_index];
			}
			if($val['shop_id'] == 23) {
				$shop_ids = "21,23,110";
				$shop_ids_arr = explode("," , $shop_ids);
				$arr_index = array_rand($shop_ids_arr);
				$shop_id = $shop_ids_arr[$arr_index];
			}
			if($val['shop_id'] == 71) {
				$shop_ids = "204,205";
				$shop_ids_arr = explode("," , $shop_ids);
				$arr_index = array_rand($shop_ids_arr);
				$shop_id = $shop_ids_arr[$arr_index];
			}

			$lastorder = $model_order->where(array('shop_id'=>$shop_id))->order('id DESC')->find();

			$order_data = array();
			$order_data['order_des'] = "membernew";
			$order_data['uid'] = $member['uid'];
			$order_data['shop_id'] = $shop_id;
			$order_data['brand_id'] = $val['brand_id'];
			$order_data['series_id'] = $val['series_id'];
			$order_data['model_id'] = $val['model_id'];
			$order_data['timesaleversion_id'] = isset($lastorder['timesaleversion_id'])?$lastorder['timesaleversion_id']:0;
			$order_data['service_ids'] = 9;
			$order_data['product_sale'] = 0.00;
			$order_data['workhours_sale'] = isset($lastorder['workhours_sale'])?$lastorder['workhours_sale']:0.70;
			$order_data['truename'] = $val['truename'];
			$order_data['mobile'] = $val['mobile'];
			$order_data['licenseplate'] = $val['licenseplate'];
			$order_data['total_price'] = rand(350,800);
			
			$order_data['order_time'] = 	mktime(rand(8,17),0,0,date("m",time()),date("d",time())+rand(1,3),date("Y",time()));
			$order_data['order_state'] = 2;
			$order_data['create_time'] = time();
			$order_data['complete_time'] = $order_data['order_time']+3600;
			$order_data['iscomment'] = 1;
			$order_data['remark'] = "脚本订单,不要打电话";
			
			$model_order->add($order_data);
			
			
			$model_membernew->where(array("uid"=>$val['uid']))->save(array('is_insert'=>1,'is_order_insert'=>2));
		}




		
		echo "ok";	
	}

		/*
		@author:wwy
		@function:脚本(插入上门保养订单数据)
		@time:2014-1-16 
	*/
	function insert_carservice() {
		$model_membercarservice = D('Membercarservice');
		$model_member= D('Member');
		$model_order = D('reservation_order');

		//$membernew = $model_membernew->query("SELECT * FROM xc_membernew WHERE is_insert=0 AND not exists (SELECT * FROM xc_member WHERE xc_membernew.mobile=xc_member.mobile) ORDER BY uid LIMIT 1");
		$membernew = $model_membercarservice->query("SELECT * FROM xc_membernew WHERE is_insert=1 AND is_order_insert=1 ORDER BY uid LIMIT 1");
		
		foreach($membernew as $key=>$val) {
			$data = array();
			//$data['password'] = md5(",,,,,,,,,,");
			$data['mobile'] = $val['mobile'];
			$data['fromstatus'] = '30';
			//$data['reg_time'] = time();
			//$data['memo'] = "短信用户  ".$val['text'];

			$member = $model_member->where($data)->find();

			$order_data = array();
			$order_data['uid'] = $member['uid'];
			$order_data['model_id'] = $val['model_id'];
			$order_data['truename'] = $val['truename'];
			$order_data['mobile'] = $val['mobile'];
			$order_data['licenseplate'] = $val['licenseplate'];
			$order_data['amount'] = 99;

			$item_content = array(
				'oil_id'     => $_SESSION['item_0'],
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
		 
			$order_data['item'] = serialize($item_content); 

			$order_data['order_time'] = mktime(rand(8,17),0,0,date("m",time()),date("d",time())+rand(1,3),date("Y",time()));
			$order_data['status'] = 8;
			$order_data['create_time'] = time();
			$order_data['remark'] = "脚本订单,不要打电话";
			
			$model_order->add($order_data);
			//echo $model_order->getLastsql();
			
			$model_membercarservice->where(array("uid"=>$val['uid']))->save(array('is_insert'=>1,'is_order_insert'=>2));exit;
		}
		
		echo "ok";	
	}
		
	
	function update_coupon() {
		$model_coupon = D("Coupon");
		$couponlist = $model_coupon->where()->select();
		foreach($couponlist as $key=>$val) {
			 $coupon_des = str_replace("纵横携车","携车",$val['coupon_des']);
			
			$data=array();
			$data['id'] = $val['id'];
			$data['coupon_des'] = $coupon_des;
			$model_coupon->save($data);
		}
		echo "ok";
	}

	/*
	@author:wwy
	@function:脚本(处理掉超过续约时间未续约的商铺)
	@time:2014/3/31
	*/
	function update_contract() {
		//4S店
		$model_shop = D('Shop');
		
		$now=time();
		$map['end_time']=array('lt',$now);
		$map['shop_class']= 1;
		$map['status']= 1;
		$list = $model_shop->where($map)->select();
		//echo $model_shop->getlastsql();exit;

		if($list){
			foreach($list as $k=>$v){
				if($v['end_time']<$now and $v['shop_class']=='1'){
					$data['shop_class'] = 2;
					$mad['id']=$v['id'];
					$model_shop->where($mad)->save($data);
					//echo $model_shop->getlastsql();
				}
			}
		}

		//优惠券
		/*$model_coupon = D('Coupon');
		
		$str['end_time']=array('lt',$now);
		$str['is_delete']= 0;
		$list = $model_coupon->where($str)->select();
		//echo $model_coupon->getlastsql();exit;

		if($list){
			foreach($list as $k=>$v){
				if($v['end_time']<$now and $v['is_delete']=='0'){
					$data['is_delete'] = 1;
					$arr['id']=$v['id'];
					$model_coupon->where($arr)->save($data);
					//echo $model_coupon->getlastsql();exit;
				}
			}
		}*/
		
		echo "OK";
	}

	/*
	@author:wwy
	@function:脚本(获取最近3个月前下过订单但最近3个月没下过的)
	@time:2014/4/1
	*/
	function get_order90() {
		//$model_order = D('Order');
		$model_order = D(GROUP_NAME.'/Order');
		$sql="SELECT DISTINCT (xc_order.uid) FROM xc_order LEFT JOIN xc_member ON xc_order.uid=xc_member.uid WHERE xc_order.create_time < UNIX_TIMESTAMP( ) - ( 90 *86400 ) AND xc_order.uid NOT IN (SELECT DISTINCT (uid) FROM xc_order WHERE create_time > UNIX_TIMESTAMP( ) - ( 90 *86400 ) AND order_state=2) AND xc_member.fromstatus!='30' AND xc_order.order_state NOT IN (0,1,2)";
		$order_uid = $model_order->query($sql);
		print_r($order_uid);

		foreach($order_uid as $key=>$val) {
			$map['uid'] = $val['uid'];
			$model_order_90 = D('Order_90');
			$row = $model_order_90->where($map)->count();
			if($row<1){
				$info = $model_order->where($map)->order('create_time desc')->find();
				$data['order_id'] = $info['id'];
				$data['uid'] = $info['uid'];
				$data['mobile'] = $info['mobile'];
				$data['operator_remark'] = $info['operator_remark'];
				$data['last_order_time'] = $info['create_time'];
				$data['create_time'] = time();
				$model_order_90->add($data);
				//echo $model_order_90->getlastsql();exit;
			}
		}
		//删掉未回访记录中已下单的
		$sql="SELECT xc_order_90.uid,xc_order.create_time FROM xc_order_90 LEFT JOIN xc_order ON xc_order_90.uid=xc_order.uid WHERE xc_order.create_time > UNIX_TIMESTAMP( ) - ( 90 *86400 )";
		$del_uid = $model_order_90->query($sql);
		if(is_array($del_uid)){
			foreach($del_uid as $k=>$v){
				$up['recall_time'] = $v['create_time']+86400*90;
				$condition['uid'] = $v['uid'];
				$model_order_90->where($condition)->save($up);
			}
		}
		echo "OK";
	}


	/*
	@author:ysh
	@function:脚本(修改以前图片上传的泛域名 统一改为www.xieche.com.cn)
	@time:2014/4/17
	*/
	function update_coupon_img_url(){
		$model_coupon = D("Coupon");
		//$map['id'] = 23;
		$coupon_list = $model_coupon->where($map)->select();
		

		$search = array("x.xieche.net", "www.xieche.net");
		$replace = "www.xieche.com.cn";
		foreach($coupon_list as $key=>$val) {
			$coupon_des = str_replace($search,$replace,$val['coupon_des']);
			
			$data = array();
			$data['id'] = $val['id'];
			$data['coupon_des'] = $coupon_des;
			$model_coupon->save($data);
		}
		echo "ok";
	}

	/*
		@author:wwy
		@function:脚本(发短信)
		@time:2015-3-9
	*/
	function send_jishi_msg() {
		$reservation_order_model = D('reservation_order');
		$model_technician = D('Technician');
		$model_user = M('tp_admin.user','xc_');
		$model_sms = M('tp_xieche.sms','xc_');
		$map['order_time'] = array(array('egt',strtotime(date('Y-m-d 0:00:00'))),array('elt',strtotime(date('Y-m-d 23:59:59'))));
		$map['status'] = 2;
		$map['technician_id'] = array('egt','0');
		$map['business_source'] = array('neq','30');
		$order_info = $reservation_order_model->where($map)->select();
		//echo $reservation_order_model->getLastsql();

		foreach($order_info as $k=>$v){
			$info = $model_technician->where(array('id'=>$v['technician_id']))->find();
			//echo $model_technician->getLastsql();
			$user_info = $model_user->where(array('id'=>$info['user_id']))->find();
			//echo $model_user->getLastsql();
			/*
			$sms = array(
					'phones'=>$v['mobile'],
					'content'=>'亲，您'.date('m',$v['order_time']).'月'.date('d',$v['order_time']).'日预约的携车网-府上养车，将由五星技师'.$info['truename'].'师傅（'.$user_info['mobile'].'）上门为您服务。期待与您的见面！有疑问询：400-660-2822。',
			);
			$res = $this->curl_sms($sms,'',1,1);
			*/
			// dingjb 2015-09-28 17:57:34 切换到云通讯
			$sms = array(
					'phones'  => $v['mobile'],
					'content' => array(
						date('m',$v['order_time']),
						date('d',$v['order_time']),
						$info['truename'],
						$user_info['mobile']
					),
			);
			$res = $this->curl_sms($sms, null, 4, '37758');

			$sms['sendtime'] = time();
			$model_sms->data($sms)->add();
		}
	}
	
	/*
		@author:wwy
		@function:脚本(跑昨天完成数据，以及当天上门的订单数据)
		@time:2015-4-27
	*/
	function carservice_daily() {
		$order_model = D('reservation_order');
		$model = D('daycomplete');
		//跑昨日数据
		$last_time = strtotime(date('Y-m-d 0:00:00',time()))-86400;
		$last_info = $model->where(array('create_time'=>$last_time))->find();
		//echo $model->getLastsql();
		//如果找到的数据是昨天的数据就开始计算
		if($last_info){
			$ids = unserialize($last_info['order_ids']);
			$data['complete_sum'] = 0;
			$data['cancel_sum'] = 0;
			$data['delay_sum'] = 0;
			foreach($ids as $k=>$v){
				$info = $order_model->where(array('id'=>$v['id']))->find();
				if($info['status']==9){
					$data['complete_sum']++;
				}elseif($info['status']==8){
					$data['cancel_sum']++;
				}elseif($info['status']<8){
					$data['delay_sum']++;
				}
			}
			$model->where(array('id'=>$last_info['id']))->save($data);
		}else{
			//没有发现昨日数据，直接插入一条空数据
			$data['create_time'] = $last_time;
			$model->add($data);
		}

		//跑7日前数据
		$time = strtotime(date('Y-m-d 0:00:00',time()))-86400*7;
		$seven_info = $model->where(array('create_time'=>$time))->find();
		if($seven_info){
			$ids = unserialize($seven_info['order_ids']);
			$data['complete_sum'] = 0;
			$data['cancel_sum'] = 0;
			$data['delay_sum'] = 0;
			foreach($ids as $k=>$v){
				$info = $order_model->where(array('id'=>$v['id']))->find();
				if($info['status']==9){
					$data['complete_sum']++;
				}elseif($info['status']==8){
					$data['cancel_sum']++;
				}elseif($info['status']<8){
					$data['delay_sum']++;
				}
			}
			$model->where(array('id'=>$seven_info['id']))->save($data);
		}

		//还没有今日数据就插入
		$today_data['create_time'] = strtotime(date('Y-m-d 0:00:00',time()));
		if($last_info['create_time']!=$today_data['create_time']){
			$start = strtotime(date('Y-m-d'));
			$end = strtotime(date('Y-m-d'))+86400;
			$map['order_time'] = array(array('egt',$start),array('elt',$end));
			$map['status'] = 2;
			$ids = $order_model ->where($map)->field('id')->select();
			echo $order_model ->getLastsql();
			$today_data['order_ids'] = serialize($ids);
			$model ->add($today_data);
		}
	}
}

?>