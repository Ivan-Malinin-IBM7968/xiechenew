<?php
class DianpingAction extends CommonAction
{
    	function __construct() {

			parent::__construct();
			$this->ShopModel = D('Shop');
			$this->MemberModel = D('Member');
			$this->OrderModel = D('Order');
			$this->MembercouponModel = D('Membercoupon');
			$this->CouponModel = D('Coupon');
			$this->dianping_couponModel = D('Dianping_coupon');
			
			$this->Smsmodel = D('Sms');//短信表
			$this->ShopModel = D('Shop');
		}
		
		/*
		*@name： 发券接口
		*@author：ysh
		*@time：2014/6/16
		*/
		function sendreceipt() {
			//$postStr = $_GET["xml"];
			//$sign = $_GET["sign"];

			$postStr = "<?xml version='1.0' encoding='GBK' ?>
									<SendReceipt>
										<SequenceId>d_2185657o_188356773t_1398331237540</SequenceId>
										<ThirdpartyId>6</ThirdpartyId>
										<ThirdpartyKey>123456</ThirdpartyKey>
										<DealId>1003</DealId>
										<DianpingSerial>1</DianpingSerial>
										<SerialCount>1</SerialCount>
										<SerialList>
													<Serial>1234526743</Serial>
												  <Serial>1234526343</Serial>
										</SerialList>
										<OrderId>100234343</OrderId>
										<MobileNo>1350000000</MobileNo>
									</SendReceipt>";
			

			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$res = (array) $postObj;
			
			$dianping_orderid = $res['OrderId'];
			$mobile = $res['MobileNo'];
			$coupon_id = $res['DealId'];//不确定是不是对应 优惠券id
			$coupon_code = $this->get_coupon_code();

			$dianping_data['SequenceId'] = $res['SequenceId'];
			$dianping_data['mobile'] = $mobile;
			$dianping_data['dianping_orderid'] = $dianping_orderid;
			$dianping_data['coupon_id'] = $coupon_id;
			$dianping_data['coupon_code'] = $coupon_code;
			$dianping_data['create_time'] = time();
			$dianping_data['status'] = 1;
			$dianping_data['is_bind'] = 0;
			$dianping_data['is_refund'] = 0;
			$this->dianping_couponModel->add($dianping_data);
			
			echo "<?xml version='1.0' encoding='GBK' ?>
						<Result>
							<Message>返回验证码</Message>
							<Code>0000</Code>
							<SerialList>
								<Serial>{$coupon_code}</Serial>
							</SerialList>
						</Result>";
		}

		/* 做在商家后台 验证使用优惠券的地方
		function update_coupon() {
			//序列号验证接口。三方系统在用户使用券后，实时向大众点评发起请求，点评按规定格式返回。
			//大众点评地址：http://host/thirdparty/verifyreceipt?xml=xxx&sign=xxx

		}
		*/
		
		/*
		*@name： 退券接口
		*@author：ysh
		*@time：2014/6/16
		*/
		function refundreceipt() {
			//$postStr = $_GET["xml"];
			//$sign = $_GET["sign"];
			$postStr = "<?xml version='1.0' encoding='GBK' ?>
									<RefundReceipt>
										<SequenceId>d_2185657o_188356773t_1398331237540</SequenceId>
									<ThirdpartyId>6</ThirdpartyId>
									<ThirdpartyKey>623423</ThirdpartyKey>
									<DealId>1003</DealId>
									<SerialList>
									<Serial>5029761976</Serial>
									  </SerialList>
									  <OrderId>100234343</OrderId>
										  </RefundReceipt>
									";

			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$res = (array) $postObj;
			$res['SerialList'] = (array) $res['SerialList'];

			$map['dianping_orderid'] = $res['OrderId'];
			$map['coupon_id'] = $res['DealId'];
			$map['coupon_code'] = $res['SerialList']['Serial'];
			$dianping_coupon = $this->dianping_couponModel->where($map)->find();

			if($dianping_coupon) {
				$data['id'] = $dianping_coupon['id'];
				$data['is_refund'] = 1;
				$this->dianping_couponModel->save($data);

				//membercoupon 表也要修改
				//退款是否帮我们有关系 要将这个优惠券设置为已退款 
				if($dianping_coupon['is_bind'] == 1) {
					$membercoupon_map['dianping_coupon_id'] = $dianping_coupon['id'];
					$update_data['is_refund'] = 1;
					$this->MembercouponModel->where($membercoupon_map)->save($update_data);
				}
				echo "<?xml version='1.0' encoding='GBK' ?>
							<Result>
							  <Message>成功</Message>
								  <Code>0000</Code>
							<SerialList>
							<Serial >
								<Number>1234526743</Number>
								<Result >1</Result>
							</Serial>
							  </SerialList>
								  </Result>
							";
			}else {
				echo "<?xml version='1.0' encoding='GBK' ?>
							<Result>
							  <Message>团购套餐不存在</Message>
								  <Code>0005</Code>
							<SerialList>
							<Serial >
								<Number>1234526743</Number>
								<Result >0</Result>
							</Serial>
							  </SerialList>
								  </Result>
							";
			}
		}



		function get_coupon_code(){
			$chars   =str_repeat('0123456789',3);
			$chars   =   str_shuffle($chars);

			$orderid     =   substr($chars,0,9);
			$sum = 0;
			for($ii=0;$ii<strlen($orderid);$ii++){
				$orderid = (string)$orderid;
				$sum += intval($orderid[$ii]);
			}
			$str = $sum%10;
			$result = $orderid.$str;
			return $result;
		}

	
	/*
		@fuction:注册页显示
		@2014-06-18
	*/
	function index(){
		$this->display();
	}


	/*
		@fuction:填写订单页显示
		@2014-06-18
	*/
	function exchange(){
		$dianping_id = $_SESSION['dianping_id'];
		$map['id'] = $dianping_id;
		$map['is_bind'] = 0;
		$map['is_refund'] = 0;
		$dianping_coupon = $this->dianping_couponModel->where($map)->find();

		$coupon_map['id'] = $dianping_coupon['coupon_id'];

		$coupon = $this->CouponModel->where($coupon_map)->find();
		$this->ShopModel->where($shop_map)->select();
		$shop_map['id'] = array('in',$coupon['shop_id']);
		$shop = $this->ShopModel->where($shop_map)->select();
		
		$this->assign('shop',$shop);
	
		$this->assign('dianping_coupon',$dianping_coupon);
		$this->assign('shop_id',$coupon['shop_id']);
		$this->display();
	}

	/*
		@fuction:订单下订
		@2014-06-18
	*/
	function order(){
		//显示订单信息
		$order = $this->OrderModel->where(array('id'=>$_SESSION['order_id']))->find();
		if($order['brand_id'] && $order['series_id'] && $order['model_id']){
			
			$car = $this->get_car_info($order['brand_id'],$order['series_id'],$order['model_id']);
			
			$this->assign('car',$car);
		}
		$shop = $this->ShopModel->where(array('id'=>$order['shop_id']))->find();
		$dianping['id'] = $_SESSION['dianping_id'];
		$dianpingif = $this->dianping_couponModel->where($dianping)->find();
		$this->assign('dianpingif',$dianpingif);
		$this->assign('shop',$shop);
		$this->assign('order',$order);
		$this->display();
	}


	/*
		@author:ysh
		@function:发送验证码
		@time:2013-09-24
	*/
	function send_verify() {
		$mobile = $_REQUEST['mobile'];
		$model_member = M('Member');
	    if (is_numeric($mobile)){
		    if (substr($mobile,0,1)==1){
				$model_sms = D('Sms');
				$condition['phones'] = $mobile;
				$smsinfo = $model_sms->where($condition)->order("sendtime DESC")->find();
				$now = time();
				if (!$smsinfo || ($now - $smsinfo['sendtime'])>=60){
					/*添加发送手机验证码*/
					$condition['phones'] = $mobile;
					$rand_verify = rand(10000, 99999);
					$_SESSION['mobile_verify_lottery'] = md5($rand_verify);
					$verify_str = "您的短信验证码：".$rand_verify."。";
					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$return_data = $this->curl_sms($send_verify);
					$send_verify['sendtime'] = time();
					$model_sms->add($send_verify);
					echo 1;exit;
				}else {
					 echo -1;
				}
		    }
		}
	}

	/*
		@author:ysh
		@function:添加验证码订单
		@time:2013-09-24
	*/
	function Dianping_add(){
		$mobile = $_POST['mobile'];//手机号
		$coupon_code = $_POST['coupon_code'];//验证码
		
		//判断验证码
		if($mobile){
			if($_SESSION['mobile_verify_lottery'] != pwdHash($_POST['mobile_verify_lottery']) && $_POST['mobile_verify_lottery'] != '989898') {
				echo "<script>alert('验证码输入错误~');history.go(-1);</script>";
			}else{
				$map['mobile'] = $mobile;
				$res = $this->MemberModel->where(array('mobile'=>$map['mobile'],'status'=>'1'))->find();
				$member['uid'] = $res['uid'];
				if($res){
					$this->save_session($res);
				}else{

					$member_data['mobile'] = $mobile;
					$member_data['password'] = md5($_POST['mobile_verify_lottery']);
					$member_data['reg_time'] = time();
					$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
					$member_data['fromstatus'] = '31';
					$member['uid'] = $this->MemberModel->where($member_data)->add();
					/*
					$send_add_user_data = array(
						'phones'=>$mobile,
						'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$_POST['mobile_verify_lottery'].'来登录携车网，客服4006602822。',
					);

					$this->curl_sms($send_add_user_data);
					*/

					//dingjb 2015-09-29 10:59:38 切换到云通讯
					$send_add_user_data = array(
						'phones' => $mobile,
						'content'=> array(
							$mobile,
							$_POST['mobile_verify_lottery']
						)
					);

					$this->curl_sms($send_add_user_data, null, 4, '37653');
					
					$send_add_user_data['sendtime'] = time();
					$this->model_sms->data($send_add_user_data)->add();
					$model_memberlog = D('Memberlog');
					$data['createtime'] = time();
					$data['mobile'] = $mobile;
					$data['memo'] = '用户注册';
					$model_memberlog->data($data)->add();
					$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
					$this->save_session($res);
				}
				$dianping['coupon_code'] = $coupon_code;
				$dianpingif = $this->dianping_couponModel->where($dianping)->find();
				$_SESSION['dianping_id'] = $dianpingif['id'];
				if($dianpingif && $dianpingif['is_bind'] ==0){
					
					//修改手机号 并且进行绑定
					$update_data['mobile'] = $mobile;
					$this->dianping_couponModel->where($dianping)->save($update_data);
					header("Location: ".__APP__."/Dianping-exchange-dianping_id-"); 		
					//header("Location: ".__APP__."/Dianping-exchange-dianping_id-".$dianpingif['id']); 
					
				}else{
					if($dianpingif['is_bind'] == 1) {
						echo "<script>alert('兑换券码已使用~');history.go(-1);</script>";
					}else {
						echo "<script>alert('兑换券码有错误~');history.go(-1);</script>";
					}
				}
			}
		}
	}

	/*
		@author:chf
		@function:添加信息进优惠券 和订单
		@time:2014-
	*/
	function info_add(){
		$sms_model = D('sms');
		$where_data['id'] = $dianping_id = $_SESSION['dianping_id'];
		$coupon_code = $_POST['coupon_code'];
		$uid = $_SESSION['uid'];
		$map_dianping['id'] = $dianping_id;
		$map_dianping['is_bind'] = 0;
		$map_dianping['is_refund'] = 0;
		$dianping_coupon = $this->dianping_couponModel->where($map_dianping)->find();
		$order_time = strtotime($_POST['date']);//预约时间
		
		if($dianping_coupon) {
			$coupon_info = $this->CouponModel->where(array('id'=>$dianping_coupon['coupon_id']))->find();
			//添加到membercoupon----start
			$membercoupon_data['coupon_name'] = $coupon_info['coupon_name'];
			$membercoupon_data['uid'] = $uid;
			$membercoupon_data['mobile'] = $_SESSION['mobile'];
			$membercoupon_data['coupon_id'] = $dianping_coupon['coupon_id'];
			$membercoupon_data['coupon_code'] = $coupon_code;
			$membercoupon_data['coupon_type'] = $coupon_info['coupon_type'];
			$membercoupon_data['shop_ids'] = $coupon_info['shop_id'];
			$membercoupon_data['create_time'] = time();
			$membercoupon_data['start_time'] = $coupon_info['start_time'];
			$membercoupon_data['end_time'] = $coupon_info['end_time'];
			$membercoupon_data['is_pay'] = 1;
			$membercoupon_data['pay_time'] = time();
			$membercoupon_data['pa'] = '5';//大众点评已经支付
			$membercoupon_data['dianping_coupon_id'] = $dianping_coupon['id'];
			$membercoupon_id = $this->MembercouponModel->add($membercoupon_data);
		
			$shop = $this->ShopModel->where(array('id'=>$coupon_info['shop_id']))->find();
			
			if($coupon_info['coupon_type']==1){
				$coupon_type_str = "现金券编号:";
			}
			if($coupon_info['coupon_type']==2){
				$coupon_type_str = "套餐券编号:";
			}
			
			$start_time = date('Y-m-d',$coupon_info['start_time']);
			$end_time = date('Y-m-d',$coupon_info['end_time']);

			$cms_map['phones'] = $_SESSION['mobile'];
			$cms_map['content'] = "您的".$coupon_type_str.$membercoupon_id."(".$coupon_info['coupon_name'].")支付已成功，请凭消费码:".$coupon_code."至商户(".$shop['shop_name'].",".$shop['shop_address'].")处在有效期(".$start_time."至".$end_time.")消费";
			$send_time = time();
			$this->curl_sms($cms_map);
			
			$sms_map['phones'] = $_SESSION['mobile'];
			$sms_map['sendtime'] = time();
			$sms_map['content'] = $cms_map['content'];
			$sms_model->add($sms_map);
			
			//测试SMS结束


			//添加到membercoupon----end
			//添加到order-------start
			$order_data['uid'] = $uid;
			$order_data['mobile'] = $_SESSION['mobile'];;
			$order_data['brand_id'] = $_POST['brand_id'];
			$order_data['series_id'] = $_POST['series_id'];
			$order_data['model_id'] = $_POST['model_id'];
			$order_data['shop_id'] = $_POST['shop_id'];
			$order_data['truename'] = $_POST['truename'];
			$order_data['licenseplate'] = $_POST['car_pai_1'].$_POST['car_pai_2'];
			$order_data['order_time'] = $_POST['car_pai_1'].$_POST['car_pai_2'];
			$order_data['operator_remark'] = '大众点评兑换优惠券的订单';
			$order_data['create_time'] = time();
			$_SESSION['order_id'] = $this->OrderModel->add($order_data);
			
			//添加到order----end
			//修改手机号 并且进行绑定
			$update_data['mobile'] = $_SESSION['mobile'];
			$update_data['is_bind'] = '1';
			$this->dianping_couponModel->where($where_data)->save($update_data);
		
		}
		header("Location: ".__APP__."/Dianping-order");
	}

	
	 public function save_session($res){
        $_SESSION['uid'] = $res['uid'];
		Cookie::set('uid', $res['uid']);
		if ($res['username']){
		    $_SESSION['username'] = $res['username'];
		    Cookie::set('username', $res['username']);
			
		}
		if ($res['mobile']){
		    $_SESSION['mobile'] = $res['mobile'];
		    Cookie::set('mobile', $res['mobile']);
		}
		if ($res['cardid']){
		    $_SESSION['cardid'] = $res['cardid'];
		    Cookie::set('cardid', $res['cardid']);
		}
		$rand = rand_string();
		$rand = md5($rand.C('ALL_PS'));
		Cookie::set('x_id', $rand);
		$res['username'] = isset($res['username'])?$res['username']:'';			
		$res['cardid'] = isset($res['cardid'])?$res['cardid']:'0';			
		$data = array(
			'uid'=>$res['uid'],
			'username'=>$res['username'],
			'cardid'=>$res['cardid'],
			'x_id'=>$rand,
			'login_time'=>time(),
		);
		$xsession = M('Xsession');
		$select_uid = $xsession->where("uid=$res[uid]")->find();
		if($select_uid){
			$list = $xsession->where("s_id=$select_uid[s_id]")->save($data);
		}else{		
			$xsession ->add($data);
		}
        $delete_map['login_time'] = array('lt',time()-3600);
		$xsession->where($delete_map)->delete();
    }


}
?>