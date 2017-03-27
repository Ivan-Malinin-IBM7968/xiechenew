<?php
//首页
class CattleAction extends CommonAction {
	public function __construct() {
		parent::__construct();
		$this->MembercattleModel = D('membercattle');//用户推荐表
		$this->MemberModel = D('Member');//用户表
		$this->SalecouponModel = D('salecoupon');//用户表
		$this->MembersalecouponModel = D('Membersalecoupon');//抵用券表
		$this->model_sms = D('Sms');
		$this->LotteryModel = D('lottery');//抽奖监控表
		$this->PacarcodeModel = D('pacarcode');//抽奖监控表
		$this->orderModel = D('order');//订单表
		$this->membercouponModel = D('membercoupon');//优惠券表
		$this->bidorderModel = D('bidorder');//事故车表
	}


    public function demo()
    {
        var_dump($this->curl_sms(array(
            'phones'  => '15853205347',
            'content' => array(
                'ad', '5'
            )
        ), null, 4));
        die;
    }
	
	/*
		@author:chf
		@function:显示领券页
		@time:2014-05-15
	*/
	function index(){
		$this->assign('mobile',$_REQUEST['mobile']);
		$this->display();
	}

	/*
		@author:chf
		@function:登录显页
		@time:2014-05-15
	*/
	function detail(){
		$uid = $this->GetUserId();
		if($uid) {
			$userinfo = $this->MemberModel->find($uid);
			$userinfo['show_mobile']=substr_replace($userinfo['mobile'], '****', 3, -4);
			//我推荐的人信息
			$map['recommend_mobile'] = $userinfo['mobile'];
			$map['status'] = array('gt',0);
			$Membercattle = $this->MembercattleModel->where($map)->select();
			if($Membercattle) {
				foreach($Membercattle  as $key=>$val) {
					//查询我推荐的人 是否订单成功 优惠券购买 事故车维修等
					$ordercount = $this->orderModel->where(array('mobile'=>$val['mobile']))->count();
					$membercouponcount = $this->membercouponModel->where(array('mobile'=>$val['mobile']))->count();
					$bidordercount = $this->bidorderModel->where(array('mobile'=>$val['mobile']))->count();//事故车表
					if($ordercount>0 || $membercouponcount>0 || $bidordercount>0){
						//通过预约订单完成
						$orderinfo = $this->orderModel->where(array('mobile'=>$val['mobile'],'order_state'=>'2'))->find();
						if($orderinfo ) {
							$result[$val['uid']]['mobile']  = substr_replace($val['mobile'], '****', 3, -4);
							$result[$val['uid']]['service']  = '预约维修';
							$result[$val['uid']]['order_state']  = '已完成';
							$result[$val['uid']]['complete_time']  = date("Y-m-d H:i:s",$orderinfo['complete_time']);
							$result[$val['uid']]['state']  = "50元话费";
							continue;
						}

						//通过优惠券完成
						$membercouponinfo = $this->membercouponModel->where(array('mobile'=>$val['mobile'],'is_use'=>'1'))->find();
						if($membercouponinfo ) {
							$result[$val['uid']]['mobile']  = substr_replace($val['mobile'], '****', 3, -4);
							$result[$val['uid']]['service']  = '优惠券';
							$result[$val['uid']]['order_state']  = '已完成';
							$result[$val['uid']]['complete_time']  = date("Y-m-d H:i:s",$membercouponinfo['use_time']);
							$result[$val['uid']]['state']  = "50元话费";
							continue;
						}

						//通过事故车完成
						$bidorderinfo = $this->bidorderModel->where(array('mobile'=>$val['mobile'],'order_status'=>'4'))->find();
						if($bidorderinfo ) {
							$result[$val['uid']]['mobile']  = substr_replace($val['mobile'], '****', 3, -4);
							$result[$val['uid']]['service']  = '事故维修';
							$result[$val['uid']]['order_state']  = '已完成';
							$result[$val['uid']]['complete_time']  = date("Y-m-d H:i:s",$bidorderinfo['complete_time']);
							$result[$val['uid']]['state']  = "50元话费";
							continue;
						}

						//不符合的也要查出来 搞不懂
						if(  empty($orderinfo) && empty($membercouponinfo) && empty($bidorderinfo)  ){
							//通过预约订单完成
							$orderinfo = $this->orderModel->where(array('mobile'=>$val['mobile'],'order_state'=>array('neq',2)))->order('id DESC')->find();
							if($orderinfo ) {
								$order_state = C("ORDER_STATE");
								$result[$val['uid']]['mobile']  = substr_replace($val['mobile'], '****', 3, -4);
								$result[$val['uid']]['service']  = '预约维修';
								$result[$val['uid']]['order_state']  = $order_state[$orderinfo['order_state']];
								$result[$val['uid']]['complete_time']  = "---";
								$result[$val['uid']]['state']  = "暂未到账";
								continue;
							}

							//通过优惠券完成
							$membercouponinfo = $this->membercouponModel->where(array('mobile'=>$val['mobile'],'is_use'=>array('neq',1)))->order('membercoupon_id DESC')->find();
							if($membercouponinfo ) {
								$result[$val['uid']]['mobile']  = substr_replace($val['mobile'], '****', 3, -4);
								$result[$val['uid']]['service']  = '优惠券';
								$result[$val['uid']]['order_state']  = '未付款';
								$result[$val['uid']]['complete_time']  = "---";
								$result[$val['uid']]['state']  = "暂未到账";
								continue;
							}

							//通过事故车完成
							$bidorderinfo = $this->bidorderModel->where(array('mobile'=>$val['mobile'],'order_status'=>array('neq',4)))->order('id DESC')->find();
							if($bidorderinfo ) {
								$bidorder_state = C("BIDORDER_STATE");
								$result[$val['uid']]['mobile']  = substr_replace($val['mobile'], '****', 3, -4);
								$result[$val['uid']]['service']  = '事故维修';
								$result[$val['uid']]['order_state']  = $bidorder_state[$bidorderinfo['order_status']];
								$result[$val['uid']]['complete_time']  = "---";
								$result[$val['uid']]['state']  = "暂未到账";
								continue;
							}
						}
					}
				}
			}
			$this->assign('mobile',$userinfo['mobile']);
		}
		$this->assign("result",$result);
		$this->assign("userinfo",$userinfo);
		$this->display();
	}
	
	function login() {
		$mobile = $_POST['mobile'];
		$password = pwdHash($_POST['password']);
		$res = $this->MemberModel -> where(array('mobile'=>$mobile,'status'=>'1'))->find();
		if($res and $res['password'] == $password) {
			$this->save_session($res);
			$data_login['last_login_time'] = time();
			$map['uid'] = $res['uid'];
			$this->MemberModel->where($map)->save($data_login);

			$this->assign('jumpUrl',__APP__.'/Cattle-detail');
			$this->success("登录成功！");

		}else {
			$this->error('密码错误！请重新登录');
		}

	}
	// 用户登出
	function logout() {
        if(isset($_SESSION['uid'])) {
        	$Xsession = M('Xsession');
        	$res=$Xsession->where("uid=$_SESSION[uid]")->find();
        	if($res){
        		$Xsession->where("uid=$_SESSION[uid]")->delete(); 
        	}
			foreach ($_SESSION as $key=>$value) {
				unset($_SESSION[$key]);
			}
			Cookie::clear();
			$this->assign("jumpUrl",__APP__);
            $this->success('登出成功！');
        }else {
			if($_SESSION['uid']) {
				Cookie::clear();
			}
            $this->error('已经登出！');
        }
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
				$verify_str = "您的短信验证码：".$rand_verify;
				$send_verify = array(
					'phones'=>$mobile,
					'content'=>$verify_str,
				);
				$return_data = $this->curl_sms($send_verify);
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
		$tuijian_mobile = $_POST['tuijian_mobile'];
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
					// dingjb 2015-09-29 10:57:55 切换到云通讯
					$send_add_user_data = array(
						'phones'  => $mobile,
						'content' => array(
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
				if(!$tuijian_mobile || $tuijian_mobile!='推荐手机号'){
					//判断推荐手机号码是否有推荐资格
					$ordercount = $this->orderModel->where(array('mobile'=>$tuijian_mobile,'order_state'=>'2'))->count();
					$membercouponcount = $this->membercouponModel->where(array('mobile'=>$tuijian_mobile,'is_use'=>'1'))->count();
					$bidordercount = $this->bidorderModel->where(array('mobile'=>$tuijian_mobile,'order_status'=>'4'))->count();//事故车表
					
					if($ordercount == '0' && $membercouponcount == '0' && $bidordercount =='0'){
						echo "8";//推荐手机号码不是老用户
						exit;
					}
				
				}

				//判断手机号码是否是新用户
				if(!$tuijian_mobile || $tuijian_mobile!='推荐手机号'){
					$newordercount = $this->orderModel->where(array('mobile'=>$mobile,'order_state'=>'2'))->count();
					$newmembercouponcount = $this->membercouponModel->where(array('mobile'=>$mobile,'is_use'=>'1'))->count();
					$newbidordercount = $this->bidorderModel->where(array('mobile'=>$mobile,'order_status'=>'4'))->count();//事故车表
					if($newordercount>0 || $newmembercouponcount>0 || $newbidordercount>0){
						echo "9";//冒充新用户推荐老用户
						exit;
					}
				}
				if(!$tuijian_mobile || $tuijian_mobile!='推荐手机号'){
					$cattlecount = $this->MembercattleModel->where(array('recommend_mobile'=>$tuijian_mobile,'mobile'=>$mobile))->count();
					if($cattlecount > 1 ){
						echo "7";//推荐手机号码重复
						exit;
					}

				}
				$Membersalecount = $this->MembersalecouponModel->where(array('mobile'=>$mobile,'salecoupon_id'=>'5'))->count();
				if($Membersalecount == '0'){
					$Pacarcode = $this->PacarcodeModel->where(array('status'=>'0'))->find();
					$this->PacarcodeModel->where(array('id'=>$Pacarcode['id']))->save(array('status'=>'1'));
					$membersalesql['id'] = '5';//黄牛好礼优惠券
					$salecoupon = $this->SalecouponModel->where($membersalesql)->find();
					//插入membersalecoupon表
					$Membersalecoupon['coupon_name'] = $salecoupon['coupon_name'];
					$Membersalecoupon['salecoupon_id'] = $salecoupon['id'];
					$Membersalecoupon['coupon_type'] = $salecoupon['coupon_type'];
					$Membersalecoupon['mobile'] = $mobile;
					$Membersalecoupon['money'] = $salecoupon['coupon_amount'];//红包金额
					$Membersalecoupon['create_time'] = time();
					$Membersalecoupon['start_time'] = $salecoupon['start_time'];
					$Membersalecoupon['end_time'] = $salecoupon['end_time'];
					$Membersalecoupon['ratio'] = $salecoupon['jiesuan_money'];
					$Membersalecoupon['shop_ids'] = $salecoupon['shop_ids'];
					$Membersalecoupon['coupon_code'] = $coupon_code = $Pacarcode['coupon_code'];
					$Membersalecoupon['from'] = 'huangniu';//来源
					$Membersalecoupon['uid'] = $this->GetUserId();
					$membersalecoupon_id = $this->MembersalecouponModel->add($Membersalecoupon);
					$start_time = date('Y-m-d H:i',$salecoupon['start_time']);
					$end_time = date('Y-m-d',$salecoupon['end_time']);
					$verify_str = "您获取的50元优惠券:（".$salecoupon['coupon_name']."）验证码为:".$Membersalecoupon['coupon_code']."已送达您的账户~";
					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$this->curl_sms($send_verify);
					$send_verify['sendtime'] = time();
					$this->model_sms->add($send_verify);
					if(!$tuijian_mobile || $tuijian_mobile!='推荐手机号'){
						//插入用户推荐表操作
						$this->MembercattleModel->add(array('recommend_mobile'=>$tuijian_mobile,'uid'=>$Membersalecoupon['uid'],'mobile'=>$mobile));
					}
					$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
					$this->save_session($res);	
					echo '3';//成功
					exit;
				}else{
					$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
					$this->save_session($res);
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


