<?php
//订单
class PaAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->MemberModel = D('Member');//用户表
		$this->SalecouponModel = D('salecoupon');//用户表
		$this->MembersalecouponModel = D('Membersalecoupon');//抵用券表
		$this->model_sms = D('Sms');
		$this->LotteryModel = D('lottery');//抽奖监控表
		$this->PacarcodeModel = D('pacarcode');//抽奖监控表
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	}
	

	function index(){
		$this->display();
	}

	/*
		@author:ysh
		@function:发送验证码
		@time:2013-09-24
	*/
	function send_verify(){
		$mobile = $_REQUEST['mobile'];
		$model_member = M('Member');
	    if (is_numeric($mobile)){
		    if (substr($mobile,0,1)==1){
				$model_sms = D('Sms');
				/*添加发送手机验证码*/
				$condition['phones'] = $mobile;
				$rand_verify = rand(10000, 99999);
				$_SESSION['mobile_verify_lottery'] = md5($rand_verify);
				$verify_str = "正在为您的手机验证，进行注册登录 ，您的短信验证码：".$rand_verify."!!!";

				/*
				$send_verify = array(
					'phones'=>$mobile,
					'content'=>$verify_str,
				);
				$return_data = $this->curl_sms($send_verify);
				*/

				// dingjb 2015-09-29 09:57:00 切换到云通讯
				$send_verify = array(
					'phones'  => $mobile,
					'content' => array($rand_verify),
				);
				$return_data = $this->curl_sms($send_verify, null, 4, '37650');

				$send_verify['sendtime'] = time();
				$model_sms->add($send_verify);
				echo 1;exit;
		    }
		}
	}




	/*	
		@author:chf
		@function:获取抽到奖的信息(优惠页面新规矩不要抽奖直接50元现金券)
		@time:2013-09-24
	*/
	function palogion(){
		$mobile = $_POST['mobile'];
		//判断验证码
		if($mobile){
			if($_SESSION['mobile_verify_lottery'] != pwdHash($_POST['mobile_verify_lottery'])) {
				echo "1";
			}else{
				$map['mobile'] = $mobile;
				$res = $this->MemberModel->where(array('mobile'=>$map['mobile'],'status'=>'1'))->find();
				if($res){
					$this->save_session($res);
				}else{
					$member_data['mobile'] = $mobile;
					$member_data['password'] = md5($_POST['mobile_verify_lottery']);
					$member_data['reg_time'] = time();
					$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
					$member_data['fromstatus'] = '33';
					$member['uid'] = $this->MemberModel->data($member_data)->add();
					/*
					$send_add_user_data = array(
						'phones'=>$mobile,
						'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$_POST['mobile_verify_lottery'].'来登录携车网，客服4006602822。',
					);
			
					$this->curl_sms($send_add_user_data);
					*/
					// dingjb 2015-09-29 11:56:40 切换到云通讯
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
				
					$Membersalecount = $this->MembersalecouponModel->where(array('mobile'=>$mobile,'salecoupon_id'=>'4'))->count();
				
				
					if($Membersalecount == '0'){
					
						$Pacarcode = $this->PacarcodeModel->where(array('status'=>'0'))->find();
						
						$this->PacarcodeModel->where(array('id'=>$Pacarcode['id']))->save(array('status'=>'1'));
						
						$membersalesql['id'] = '4';//给平安发100元优惠券 红包ID
						$salecoupon = $this->SalecouponModel->where($membersalesql)->find();
							
						//插入membersalecoupon表
						$Membersalecoupon['coupon_name'] = $salecoupon['coupon_name'];
						$Membersalecoupon['salecoupon_id'] = $salecoupon['id'];
						$Membersalecoupon['coupon_type'] = $salecoupon['coupon_type'];
						$Membersalecoupon['mobile'] = $mobile;
						
						$Membersalecoupon['create_time'] = time();
						$Membersalecoupon['start_time'] = $salecoupon['start_time'];
						$Membersalecoupon['end_time'] = $salecoupon['end_time'];
						$Membersalecoupon['ratio'] = $salecoupon['jiesuan_money'];
						$Membersalecoupon['shop_ids'] = $salecoupon['shop_ids'];
				
						$Membersalecoupon['coupon_code'] = $coupon_code = $Pacarcode['coupon_code'];
				
						$Membersalecoupon['from'] = 'pa';//来源
						$Membersalecoupon['uid'] = $this->GetUserId();
						$membersalecoupon_id = $this->MembersalecouponModel->add($Membersalecoupon);
						
						$start_time = date('Y-m-d H:i',$salecoupon['start_time']);
						$end_time = date('Y-m-d',$salecoupon['end_time']);
						$verify_str = "您获取的立减券:（".$salecoupon['coupon_name']."）已送达您的账户.消费码为:".$coupon_code;

						$send_verify = array(
							'phones'=>$mobile,
							'content'=>$verify_str,
						);
						$this->curl_sms($send_verify);
						
						$send_verify['sendtime'] = time();
						$this->model_sms->add($send_verify);
						echo '3';//成功
						exit;
					}else{
						echo "2";//重复
						exit;
				}
			}
		}else{
			echo "5";
			exit;
		}
		
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