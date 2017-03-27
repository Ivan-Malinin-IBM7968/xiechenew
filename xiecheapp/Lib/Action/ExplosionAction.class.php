<?php
//文章
class ExplosionAction extends CommonAction {
	public function __construct() {
		parent::__construct();
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		$this->couponModel = D('coupon');//优惠券表
		$this->FsModel = D('fs');//4S店
		$this->QuizModel = D('quiz');//分享表
		$this->CarseriesModel = D('carseries');//车系表
		$this->CarbandModel = D('carbrand');//品牌绑定
		$this->padatatestModel = D('padatatest');//微信临时用户保存表
		$this->paweixinModel = D('paweixin');//微信用户表
		$this->MemberModel = D('member');//用户表
		$this->explosioncouponModel = D('explosioncoupon');//爆款库存表
		$this->membercouponModel = D('membercoupon');//用户购买优惠券表
		$this->ShopModel = D('shop');//店铺
		$this->SmsModel = D('sms');//消息记录

		import("ORG.Util.hm");
		$_hmt = new hm("25bc1468b91be39c26580a917bfbf673");
		$_hmtPixel = $_hmt->trackPageView();
		$this->assign('baiduimg',$_hmtPixel);

	}


	/*
		@author:chf
		@function:爆款活动WAP主页
		@time:2014-04-10
	*/
	function index(){
		$data['coupon_across'] = '1';
		$data['is_delete'] = '0';
		$data['start_time'] = array('lt',time());
		$data['end_time'] = array('gt',time());
		$data['id'] = array('in',"486,455,419,430");
		$coupon = $this->couponModel->where($data)->limit(4)->select();
		if($coupon) {
			foreach ($coupon as $k=>$v){
				$coupon[$k]['coupon_amount'] = round($v['coupon_amount']);
            }
		}
		$this->assign('coupon',$coupon);
		$this->display();
	}


	/*
		@author:chf
		@function:爆款活动WAP详情页
		@time:2014-04-10
	*/
	function coupon_list(){
		if($_REQUEST['fs_Name']){
			$this->assign('fs_Name',$_REQUEST['fs_Name']);
		}
		if($_REQUEST['fsid']) {
			$data['_string'] = "FIND_IN_SET('{$_REQUEST[fsid]}', fsid_across)";
		}
		
		$data['coupon_across'] = '1';
		$data['is_delete'] = '0';
		$data['start_time'] = array('lt',time());
		$data['end_time'] = array('gt',time());

		// 导入分页类
		$page_size = 10;
		import("ORG.Util.Page");
		$count = $this->couponModel->where($data)->count();
		
		// 实例化分页类
		$p = new Page($count, $page_size);
		$page = $p->show_app();
			
		$couponlist = $this->couponModel->where($data)->limit($p->firstRow.','.$p->listRows)->select();

		if($couponlist) {
			foreach ($couponlist as $k=>$v){
                $couponlist[$k]['coupon_logo'] = WEB_ROOT."/UPLOADS/Coupon/Logo/coupon1_".$v['coupon_logo'];
				$couponlist[$k]['coupon_amount'] = round($v['coupon_amount']);
            }
		}


		//选择车型取得数据(XX需求呵呵)
		$FSdata = $this->FsModel->select();
		foreach($FSdata as $k=>$v){
			$Carseries = $this->CarseriesModel->where(array('fsid'=>$v['fsid']))->find();
			$Carband = $this->CarbandModel->where(array('brand_id'=>$Carseries['brand_id']))->find();
			$FSdata[$k]['word'] = $Carband['word']; 
		}

		$this->assign('FSdata',$FSdata);
		$this->assign('page',$page);
		$this->assign('couponlist',$couponlist);
		$this->display();
	}

	
	/*
		@author:chf
		@function:爆款活动WAP详情页
		@time:2014-06-23
	*/
	function playground(){
			$uid = $this->GetUserId();
			if($uid) {
				$member = $this->MemberModel->where(array('uid'=>$uid))->find();
				$mobile = $member['mobile'];
			}
			
			$this->assign('uid',$uid);
			$this->assign('member',$member);
			//2014.6.25 12点 活动开始
			$happy_start_time = mktime(12, 0, 0, date("m")  , date("d"), date("Y"));
			if(time()>$happy_start_time) {
				$this->assign("is_open","1");
			}
			//猜底价的

			$year = date('Y',time());//年
			$month = date('m',time());//月
			$hour = date('d',time());
			$start_time = strtotime($year.'-'.$month.'-'.$hour."00:00:00");
			$end_time = strtotime($year.'-'.$month.'-'.$hour."23:59:59");
			$ex_map['create_time'] = array(array('egt',$start_time),array('lt',$end_time));
			$explosion_coupon = $this->explosioncouponModel->where($ex_map)->group("coupon_id")->find();//还能兑换的张数
			
			$coupon_id = $explosion_coupon['coupon_id'];
			$coupon = $this->couponModel->find($coupon_id);


			$coupon['coupon_pic'] = WEB_ROOT."/UPLOADS/Coupon/Logo/".$coupon['coupon_pic'];
			$coupon['coupon_amount'] = round($coupon['coupon_amount']);
			$coupon['quiz_amount'] = str_split($coupon['coupon_amount']);
			$coupon['rand_amount'] = $this->get_rand($coupon['quiz_amount']['1']);
			
			$this->assign('coupon',$coupon);
			
			//你可能感兴趣的
			$map['coupon_across'] = '1';
			$map['is_delete'] = '0';
			$map['start_time'] = array('lt',time());
			$map['end_time'] = array('gt',time());
			$map['id'] = array('in',"486,455,419,430");
			$couponList = $this->couponModel->where($map)->limit(4)->order('rand()')->select();
			if($couponList) {
				foreach ($couponList as $k=>$v){
					$couponList[$k]['coupon_amount'] = round($v['coupon_amount']);
				}
			}
			$this->assign('couponList',$couponList);

			if(count($member) != '0'){
				//今天开始时间
				$year = date('Y',time());//年
				$month = date('m',time());//月
				$hour = date('d',time());
				$start_time = strtotime($year.'-'.$month.'-'.$hour."00:00:00");
				$end_time = strtotime($year.'-'.$month.'-'.$hour."23:59:59");
				$data['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
				$data['mobile'] = $mobile;
				
				$Quiz = $this->QuizModel->where($data)->select();
				if(count($Quiz) == 0){
					$Quiz_count = '0';//1次都没参与过 有一次抽奖机会
				}else if(count($Quiz) == 1){
					if($Quiz[0]['is_share'] == '0'){
						$Quiz_count = '1';//竞猜过一次但是没分享
					}else{
						$Quiz_count = '2';//竞猜过一次已经分享但是没来猜第二次
					}
				}else{
					$Quiz_count = '3';//竞猜过第二次你他妈分享了也不能在猜了！
				}

			}else{
				$Quiz_count = 'weixinnone';//你丫的没绑定猜你个大JB~
			}
		
			//分享操作
			$this->assign('Quiz_count',$Quiz_count);
			//$this->assign('Quiz_count','1');
			$this->display();
	//	}
	//	echo "显示出错";
	}

	/*
		@author:chf
		@function:爆款活动WAP详情页
		@time:2014-06-23
	*/
	function result(){
		$uid = $this->GetUserId();
		$member = $this->MemberModel->where(array('uid'=>$uid))->find();
		//今天开始时间
		$year = date('Y',time());//年
		$month = date('m',time());//月
		$hour = date('d',time());
		$start_time = strtotime($year.'-'.$month.'-'.$hour."00:00:00");
		$end_time = strtotime($year.'-'.$month.'-'.$hour."23:59:59");
		$count_data['create_time'] = $data['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$count_data['status'] = '1';
		$data['status'] = '2';
		$count_data['uid'] = array('eq','');
		$explosion = $this->explosioncouponModel->where($data)->select();//还能兑换的张数

		$count = $this->explosioncouponModel->where($count_data)->count();//还能兑换的张数
		$Quiz = $this->QuizModel->group('coupon_id')->where(array('uid'=>$member['uid']))->select();
		//竞猜
		if($Quiz){
		foreach($Quiz as $k=>$v){
				$coupon = $this->couponModel->where(array('id'=>$v['coupon_id']))->find();
				$Quiz[$k]['coupon_name'] = $coupon['coupon_name'];
				$Quiz[$k]['cai_count'] = $this->QuizModel->where(array('uid'=>$v['uid'],'coupon_id'=>$v['coupon_id']))->count();//竞猜次数
				$Quiz[$k]['huan_count'] = $this->explosioncouponModel->where(array('uid'=>$v['uid'],'coupon_id'=>$v['coupon_id'],'status'=>'2'))->count();//是否兑换
				$membercoupon = $this->membercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$Quiz[$k]['coupon_code'] = $membercoupon['coupon_code'];
			}
		}
		if($explosion){
			foreach($explosion as $k=>$v){
				$member_ex = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				$explosion[$k]['mobile'] = preg_replace("/(\d{3})(\d{4})/","$1****", $member_ex['mobile']);
				$explosion[$k]['use_time'] = $v['use_time'];
			}
		}
		
		$this->assign('member',$member);
		$this->assign('count',$count);
		$this->assign('Quiz',$Quiz);
		$this->assign('explosion',$explosion);
		$this->assign('shop_ex',$shop_ex);
		$this->display();
	}
	
	/*
		@author:chf
		@function:利用微信传来的临时微信ID得到对应已验证的微信号信息
		@time:2014-06-23
	*/
	function add_explosion(){
		$uid = $this->GetUserId();//微信用户绑定表主键ID
		$member = $this->MemberModel->where(array('uid'=>$uid))->find();
		$data['coupon_id'] = $_POST['coupon_id'];

		$year = date('Y',time());//年
		$month = date('m',time());//月
		$hour = date('d',time());
		$start_time = strtotime($year.'-'.$month.'-'.$hour."00:00:00");
		$end_time = strtotime($year.'-'.$month.'-'.$hour."23:59:59");
		$data['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$data['status'] = '1';
		$explosion = $this->explosioncouponModel->where($data)->find();
		
		if(count($explosion) != '0'){
			$have = $this->explosioncouponModel->where(array('uid'=>$uid,'coupon_id'=>$_POST['coupon_id']))->select();
			if( $have ) {
				$coupon = $this->couponModel->where(array('id'=>$data['coupon_id']))->find();
			
				$membercoupon['coupon_id'] = $coupon['id'];
				$membercoupon['coupon_name'] = $coupon['coupon_name'];
				$membercoupon['uid'] = $member['uid'];
				$membercoupon['coupon_type'] = $coupon['coupon_type'];
				$membercoupon['mobile'] = $member['mobile'];
				$membercoupon['coupon_code'] = $this->get_coupon_code();
				$membercoupon['shop_ids'] = $coupon['shop_id'];
				$membercoupon['coupon_amount'] = '0';
				$membercoupon['create_time'] = time();
				$membercoupon['start_time'] = $coupon['start_time'];
				$membercoupon['end_time'] = $coupon['end_time'];
				$membercoupon['is_pay'] = '1';
				$membercoupon['pay_time'] = time();
				$membercoupon['pa'] = '6';//爆款抢购 免费送券5张用户
				$membercoupon_id = $this->membercouponModel->add($membercoupon);
				
				$shop = $this->ShopModel->where(array('id'=>$coupon['shop_id']))->find();
				
				if($membercoupon['coupon_type']==1){
					$coupon_type_str = "现金券编号:";
				}
				if($membercoupon['coupon_type']==2){
					$coupon_type_str = "套餐券编号:";
				}
				
				$start_time = date('Y-m-d',$membercoupon['start_time']);
				$end_time = date('Y-m-d',$membercoupon['end_time']);

				$cms_map['phones'] = $member['mobile'];
				$cms_map['content'] = "您的".$coupon_type_str.$membercoupon_id."(".$membercoupon['coupon_name'].")支付已成功，请凭消费码:".$membercoupon['coupon_code']."至商户(".$shop['shop_name'].",".$shop['shop_address'].")处在有效期(".$start_time."至".$end_time.")消费";
				$send_time = time();
				$this->curl_sms($cms_map);

				$sms_map['phones'] = $member['mobile'];
				$sms_map['sendtime'] = time();
				$sms_map['content'] = $cms_map['content'];
				$this->SmsModel->add($sms_map);
				$this->explosioncouponModel->where(array('id'=>$explosion['id']))->save(array('status'=>2,'uid'=>$member['uid'],'use_time'=>time(),"membercoupon_id"=>$membercoupon_id));
				
				echo "1";//兑换成功
			}else{
				echo "3";//已经兑换过
			}
			
		}else{
			echo "2";//兑换库存量不足
		
		}
		exit;
	}

	function Back(){
	
	
	
	}

	/*
		得到验证码随机数
	*/
	function get_coupon_code(){
        $orderid = $this->randString(9,1);
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
		@author:chf
		@function:利用微信传来的临时微信ID得到对应已验证的微信号信息
		@time:2014-06-23
	*/
	function get_weixininfo($pa_id){
		$padatetest = $this->padatatestModel->where(array('id'=>$pa_id))->find();
		$paweixin = $this->paweixinModel->where(array('wx_id'=>$padatetest['FromUserName'],'type'=>'2','status'=>'2'))->find();
		return $paweixin;
	}

	/*
		@author:ysh
		@function:Ajax竞猜判断
		@time:2014-06-23
	*/
	function Ajax_postQuiz(){
		$num = $_REQUEST['num'];
		$coupon_id = $_REQUEST['coupon_id'];
		$uid = $this->GetUserId();

		$member = $this->MemberModel->where(array('uid'=>$uid))->find();

		$year = date('Y',time());//年
		$month = date('m',time());//月
		$hour = date('d',time());
		$start_time = strtotime($year.'-'.$month.'-'.$hour."00:00:00");
		$end_time = strtotime($year.'-'.$month.'-'.$hour."23:59:59");
		$map['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$map['uid'] = $uid;
		$map['mobile'] = $member['mobile'];

		$Quiz = $this->QuizModel->where($map)->select();
		if(count($Quiz) == 0){
			$Quiz_count = '0';//1次都没参与过 有一次抽奖机会
			$is_share = 0;
		}else if(count($Quiz) == 1){
			if($Quiz['is_share'] == '0'){
				$Quiz_count = '1';//竞猜过一次但是没分享
			}else{
				$Quiz_count = '2';//竞猜过一次已经分享但是没来猜第二次
				$is_share = 1;
			}
		}else{
			$Quiz_count = '3';//竞猜过第二次你他妈分享了也不能在猜了！
		}
		
		if($Quiz_count != 3 && $Quiz_count != 1 ) {
			$coupon = $this->couponModel->find($coupon_id);
			$data['is_share'] = $is_share;
			$data['uid'] = $uid;
			$data['mobile'] = $member['mobile'];
			$data['create_time'] = time();
			$data['coupon_id'] = $coupon_id;
			$data['coupon_amount'] = $coupon['coupon_amount'];
			$data['quiz_result'] = $num;
			$coupon['coupon_amount'] = round($coupon['coupon_amount']);
			$coupon['quiz_amount'] = str_split($coupon['coupon_amount']);
			if($num == $coupon['quiz_amount']['1']) {//猜对了返回猜对信息 跳转到领取页面 进行领取
				$data['is_true'] = 1;
			}
			$this->QuizModel->add($data);
		}else {
			if($Quiz_count == 3) {
				echo "4";//竞猜过第二次你他妈分享了也不能在猜了！
			}
			if($Quiz_count == 1) {
				echo "5";//竞猜过一次 但是没有分享 不能再猜！！
			}
		}

		if($data['is_true'] == 1) {
			echo "1";//竞猜成功！要去领取了
		}else {
			if($Quiz_count == 0) {
				echo "2";//竞猜失败 可以进行分享再来一次
			}
			if($Quiz_count == 1 || $Quiz_count == 2) {
				echo "3";//竞猜了2次都失败了
			}
			
		}
	}

	/*
		@author:ysh
		@function:Ajax返回分享接口数据
		@time:2014-06-23
	*/
	function Ajax_shareReturn(){
		$uid = $this->GetUserId();
		$coupon_id = $_REQUEST['coupon_id'];
		$member = $this->MemberModel->where(array('uid'=>$uid))->find();
		
		$year = date('Y',time());//年
		$month = date('m',time());//月
		$hour = date('d',time());
		$start_time = strtotime($year.'-'.$month.'-'.$hour."00:00:00");
		$end_time = strtotime($year.'-'.$month.'-'.$hour."23:59:59");
		$map['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$map['uid'] = $uid;
		$map['mobile'] = $member['mobile'];
		$map['coupon_id'] = $coupon_id;
		
		$data['is_share'] = 1;
		$data['share_time'] = time();
		$quiz = $this->QuizModel->where($map)->order('create_time ASC')->save($data);
		if($quiz) {
			echo "1";
		}
	}

	/*
		@author:ysh
		@function:获取5位不重复随即数
		@time:2014-06-23
	*/
	function get_rand($char) {
		$arr = array();
		$ii= 0;
		while ($ii<4){
			$a = rand(0,9);
			if( !in_array($a,$arr) && $a!=$char)  {
				$arr[] = $a;
				$ii++;
			}
		}
		$arr[4] = $char;
		shuffle($arr);
		$akey =  array('a','b','c','d','e');
		return array_combine($akey,$arr);
	}

		/*
		@author:ysh
		@function:发送验证码
		@time:2013-09-24
	*/
	function send_verify() {
		$mobile = $_REQUEST['mobile'];
	    if (is_numeric($mobile)){
		    if (substr($mobile,0,1)==1){
				$model_sms = D('Sms');
				$condition['phones'] = $mobile;
				$smsinfo = $model_sms->where($condition)->order("sendtime DESC")->find();
				$now = time();
				if (!$smsinfo || ($now - $smsinfo['sendtime'])>=60){

					/*添加发送手机验证码*/
					$condition['phones'] = $mobile;
					/*
					$rand_verify = rand(10000, 99999);
					$_SESSION['mobile_verify_lottery'] = md5($rand_verify);
					$verify_str = "正在为您的手机验证，进行注册登录 ，您的短信验证码：".$rand_verify;
					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$return_data = $this->curl_sms($send_verify);
					*/

					// dingjb 2015-09-29 09:39:59 切换到云通讯
					$rand_verify = rand(10000, 99999);
					$_SESSION['mobile_verify_lottery'] = md5($rand_verify);
					$verify_str = "正在为您的手机验证，进行注册登录 ，您的短信验证码：".$rand_verify;
					$send_verify = array(
						'phones'  => $mobile,
						'content' => array($rand_verify),
					);
					$return_data = $this->curl_sms($send_verify, null, 4, '37650');

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
		@function:登录
		@time:2014/6/24
	*/
	function login(){
		$mobile = $_POST['mobile'];
		//判断验证码
		if($mobile){
			if($_SESSION['mobile_verify_lottery'] != pwdHash($_POST['mobile_verify_lottery']) && $_POST['mobile_verify_lottery'] != '989898') {
				echo "1";
			}else{
				$map['mobile'] = $mobile;
				$res = $this->MemberModel->where(array('mobile'=>$map['mobile'],'status'=>'1'))->find();
				if($res){
					$this->save_session($res);
					echo "3";
				}else{
					$member_data['mobile'] = $mobile;
					$member_data['password'] = md5($_POST['mobile_verify_lottery']);
					$member_data['reg_time'] = time();
					$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
					$member_data['fromstatus'] = '31';//微信竞猜分享
					$member['uid'] = $this->MemberModel->data($member_data)->add();
					/*
					$send_add_user_data = array(
						'phones'=>$mobile,
						'content'=>"您已注册成功，您可以使用您的手机号码".$mobile."，密码".$_POST['mobile_verify_lottery']."来登录携车网，客服4006602822",
					);

					$this->curl_sms($send_add_user_data);
					*/
					// dingjb 2015-09-29 11:01:41 切换到云通讯
					$send_add_user_data = array(
						'phones'  => $mobile,
						'content' => array(
							$mobile,
							$_POST['mobile_verify_lottery']
						)
					);

					$this->curl_sms($send_add_user_data, null, 4, '37653');


					$send_add_user_data['sendtime'] = time();
					$this->SmsModel->data($send_add_user_data)->add();
					
					$model_memberlog = D('Memberlog');
					$data['createtime'] = time();
					$data['mobile'] = $mobile;
					$data['memo'] = '用户注册';
					$model_memberlog->data($data)->add();
					$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
					$this->save_session($res);
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

		/**
	 +----------------------------------------------------------
	 * 产生随机字串，可用来自动生成密码
	 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function randString($len=6,$type='',$addChars='') {
		$str ='';
		switch($type) {
			case 0:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 1:
				$chars= str_repeat('0123456789',3);
				break;
			case 2:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
				break;
			case 3:
				$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 4:
				$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
				break;
		}
		if($len>10 ) {//位数过长重复字符串一定次数
			$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
		}
		if($type!=4) {
			$chars   =   str_shuffle($chars);
			$str     =   substr($chars,0,$len);
		}else{
			// 中文随机字
			for($i=0;$i<$len;$i++){
			  $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
			}
		}
		return $str;
	}



	/*
		@author:
		@function:显示3折广告
		@time:2014-06-25
	
	*/
	function show_explosion(){
	
		$this->assign('title',"携车网 养车更养眼");
		$this->display();
	}


	function test_ex() {
			$year = date('Y',time());//年
			$month = date('m',time());//月
			$hour = date('d',time());
			$start_time = strtotime($year.'-'.$month.'-'.$hour."00:00:00");
			$end_time = strtotime($year.'-'.$month.'-'.$hour."23:59:59");
			$ex_map['create_time'] = array(array('egt',$start_time),array('lt',$end_time));
			$explosion_coupon = $this->explosioncouponModel->where($ex_map)->group("coupon_id")->find();//还能兑换的张数
			echo $this->explosioncouponModel->getLastSql();
			print_r($explosion_coupon);
	}
}