<?php
class CoupondaiAction extends CommonAction {
    function __construct() {
		parent::__construct();
	}
	
	/*
		@author:wwy
		@function:根据电话查询用户信息页
		@time:2014-06-24
	*/
	function index(){
		Cookie::set('_currentUrl_', __SELF__);
		$model_member= D(GROUP_NAME.'/Member');
		if ($_POST['mobile'] and !eregi("^1[0-9]{10}$",$_POST['mobile'])){
    		    $this->error('手机号码格式错误！');exit;
    		}
		if($_REQUEST['mobile']){
			$data['mobile'] = $_REQUEST['mobile'];
			$info = $model_member->where($data)->find();
			//$data['status'] = 1;
			if($info){
				$list = "<td>".$info['username']."</td><td>".$info['mobile']."</td><td><a href='__URL__/Coupondai/uid/".$info['uid']."'>代下优惠券</a></td>";
			}else{
				$list = '<tr><td>该用户尚未注册，请点击添加按钮完成注册</td></tr>';
			}

			$this->assign('mobile',$info['mobile']);
			$this->assign('list',$list);
		}
		$this->display();
	}
	//添加用户
	function _before_add(){
		if ($_GET['mobile'] and eregi("^1[0-9]{10}$",$_GET['mobile'])){
			$come_phone = $_GET['mobile'];
		}else {
			$come_phone = '';
		}
		$this->assign('mobile',$come_phone);
    }
	function add(){
		$this->display();
	}

	//执行添加用户操作
	public function insert(){
		if($_POST['mobile']){
    		if ($_POST['mobile'] and !eregi("^1[0-9]{10}$",$_POST['mobile'])){
    		    $this->error('手机号码格式错误！');exit;
    		}
			$member_model = D('Member');
			$mobile = $_POST['mobile'];
			$check = $member_model->where("mobile = '$mobile'")->find();
			if(!$check){
				$rand = rand_string(6,1);
				//$_POST['username'] = 'xieche_'.$rand.'_'.time();
				$_POST['password'] = md5($rand);
				$_POST['rand'] = $rand;
				$_POST['reg_time'] = time();
				$_POST['ip'] = get_client_ip();
				$_POST['fromstatus'] = '41';
				$uid = $member_model->add($_POST);
			}else{
				$this->error('号码已经存在');
				exit;
			}
		}else {
		    $this->error('请输入手机号码!');
			exit;
		}

		if($uid){
	        $ql_huodong = $_POST['ql_huodong'];
	        $huodong_str = "";
	        if ($ql_huodong){
	            $huodong_str = "3折工时优惠券稍后发到您的帐户中，如果您参与今天下午2点的抢楼活动更有机会获得工时费全免券，请访问".WEB_ROOT."qg/ 来参与活动。";
	        }
	        /*
			$send_add_user_data = array(
			'phones'=>$_POST['mobile'],
			//'content'=>'亲爱的用户，您可以使用您的手机号码'.$_POST['mobile'].'，密码'.$_POST['rand'].'来登录携车网www.xieche.com.cn。携车网是一家在线预订各大汽车品牌4S店保养维修的网站，通过网站预订最低五折起，还有保养套餐和现金券团购。手机客户端已上线，手机客户端下载http://t.cn/zTHB2Nv，客服4006602822。'.$huodong_str,
			'content'=>'您已注册成功，您可以使用您的手机号码'.$_POST['mobile'].'，密码'.$_POST['rand'].'来登录携车网，客服4006602822。'
		    );

			$this->curl_sms($send_add_user_data);
			*/

			// dingjb 2015-09-29 10:08:34 切换到云通讯
			$send_add_user_data = array(
				'phones'  => $_POST['mobile'],
				'content' => array(
					$_POST['mobile'],
					$_POST['rand']
				)
		    );

			$this->curl_sms($send_add_user_data, null, 4, '37653');

			$model_sms = D('Sms');
			$now = time();
			$send_add_user_data['sendtime'] = $now;
			$model_sms->add($send_add_user_data);
            
			$model_memberlog = D(GROUP_NAME.'/Memberlog');
	        $data['createtime'] = time();
	        $data['mobile'] = $_POST['mobile'];
	        $data['deal_uid'] = $_SESSION['authId'];
	        $data['memo'] = '用户注册';
	        $model_memberlog->add($data);
	        
			//tipask库user添加
			$model_user = D(GROUP_NAME.'/User');
			$tipask_data['uid'] = $model->id;
			$tipask_data['cardid'] = $_POST['cardid'];
			$tipask_data['password'] = $_POST['password'];
			$tipask_data['phone'] = $_POST['mobile'];
			$tipask_data['regtime'] = time();
			$tipask_data['regip'] = get_client_ip();
			$tipask_data['lastlogin'] = time();
			$model_user->add($tipask_data);

			$this->success('新增成功，跳转中~~',U('/Daicoupon/coupondai/Coupondai/uid/'.$uid));
			exit;
		}else{
			$this->error('新增失败');exit;
		}

	}

	/*
		@author:wwy
		@function:客服代下优惠券选择页
		@time:2014-06-24
	*/
	function coupondai(){
		Cookie::set('_currentUrl_', __SELF__);
		$model_coupon= D(GROUP_NAME.'/Coupon');
		$uid = $_REQUEST['uid'];
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $_REQUEST['coupon_type'];
		}
		if($_REQUEST['coupon_name']){
			$data['coupon_name'] = array('like',"%".$_REQUEST['coupon_name']."%");
		}
		$data['is_delete'] = 0;
		$data['coupon_across'] = 1;
		$count = $model_coupon->where($data)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		foreach ($_POST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
		$page = $p->show_admin();
		$coupon = $model_coupon->where($data)->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_coupon->getLastsql();
		
		$this->assign('coupon',$coupon);
		$this->assign('page',$page);
		$this->assign('uid',$uid);
		$this->assign('coupon_type',$_REQUEST['coupon_type']);
		$this->assign('coupon_name',$_REQUEST['coupon_name']);
		$this->display();
	}

	/*
		@author:wwy
		@function:客服代下优惠券操作
		@time:2014-06-24
	*/
	function Coupondaiadd(){
		$model_member= D(GROUP_NAME.'/Member');
		$model_coupon= D(GROUP_NAME.'/Coupon');
		$model_membercoupon= D(GROUP_NAME.'/Membercoupon');
		$model_daicouponlog= D(GROUP_NAME.'/Daicouponlog');
		$model_order= D(GROUP_NAME.'/Order');
		$data['uid'] = $_POST['add_uid'];
		$data['coupon_id'] = $_POST['coupon_id'];
		$data['mobile'] = $_POST['mobile'];
		$member = $model_member->where(array('uid'=>$data['uid']))->find();
		$coupon = $model_coupon->where(array('id'=>$data['coupon_id']))->find();
		if($_POST['num'] > '0'){
			//代下订单
			$order_time= $_POST['date'].' '.$_POST['hours'].':'.$_POST['minute'];
			$order['order_time'] = strtotime($order_time);
			$order['service_ids'] = '10';
			$order['uid'] = $_POST['add_uid'];
			$order['coupon_id'] = $_POST['coupon_id'];
			$order['shop_id'] = $coupon['shop_id'];
			$order['mobile'] = $member['mobile'];
			$order['create_time'] = time();
			$order['operator_id'] = $_SESSION['authId'];
			$order['operator_remark'] = '爆款抢购优惠券代下单';
			//print_r($order);
			$data['order_id'] = $model_order->add($order);

			for($a=0;$a<$_POST['num'];$a++){
				$data['coupon_name'] = $coupon['coupon_name'];
				$data['coupon_type'] = $coupon['coupon_type'];
				$data['mobile'] = $member['mobile'];
				$data['shop_ids'] = $coupon['shop_id'];
				$data['start_time'] = $coupon['start_time'];
				$data['end_time'] = $coupon['end_time'];
				$data['create_time'] = time();
				$data['pa'] = '6';//客服代下单优惠券
				//print_r($data);
				$log['membercoupon_id'] = $model_membercoupon->add($data);
				$log['operator_id'] = $_SESSION['authId'];
				$log['create_time'] = time();
				//print_r($log);
				$model_daicouponlog->add($log);

			}

			//代下优惠券下行短信
			if($log['membercoupon_id']){
				$send_add_user_data = array(
				'phones'=>$member['mobile'],
				'content'=>'您已购买'.$coupon['coupon_name'].'团购券，但未完成支付。如您选择7月2号至7月8号到店，可到xxxxx店后由携车网相关工作人员协助完成支付后享受服务。预约其他时间段的，请登录携车网（'.WEB_ROOT.'Mobile-order_list）完成支付，凭携车网下行验证码到店享受服务。'
				);
				$this->curl_sms($send_add_user_data);
			}
			echo 2;
		}else{
			echo 1;
		}

	}

	/*
		@author:wwy
		@function:根据客服ID获取客服下单记录
		@time:2014-06-24
	*/
	function daicouponlog(){
		if($_REQUEST['mobile']){
			$data['mobile'] = $_REQUEST['mobile'];
		}
		$map['xc_daicouponlog.operator_id'] = $_SESSION['authId'];
		$model_daicouponlog = D("Daicouponlog");
		 // 计算总数
        $count = $model_daicouponlog->where($map)->join("xc_membercoupon ON xc_membercoupon.membercoupon_id=xc_daicouponlog.membercoupon_id")->count();

        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
		//分页跳转的时候保证查询条件
        foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != '') {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_daicouponlog->where($map)->join("xc_membercoupon ON xc_membercoupon.membercoupon_id=xc_daicouponlog.membercoupon_id")->order('xc_daicouponlog.id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_daicouponlog->getLastsql();

		if(is_array($list)){
			foreach($list as $key=>$value){
				$mad['membercoupon_id'] = $value['membercoupon_id'];
				$info = $model_daicouponlog->where($mad)->find();
				$list[$key]['remark'] = $info['remark'];
			}
		}
		//print_r($list);
		$this->assign('page',$page);
		$this->assign('list',$list);
		$this->display();
	}

	function remark(){
		$model_daicouponlog = D("Daicouponlog");
		$map['id'] = $_REQUEST['id'];
		$data['remark'] = addslashes($_REQUEST['remark']);
		$result = $model_daicouponlog->where($map)->save($data);
		if($result){
			$this->success('备注成功，跳转中~~',U('/Daicoupon/coupondai/daicouponlog'));
		}
	}

	public function sendsms(){
	    $mobile = isset($_POST['mobile'])?$_POST['mobile']:0;
	    if ($mobile) {
	    	$send_add_user_data = array(
				'phones'=>$mobile,
				'content'=>'携车网是一家免费在线预约各大汽车品牌4S店保养维修的网站，通过网站预约最低五折起，还有保养套餐和现金券团购。访问网址'.WEB_ROOT.'或拨打客服电话4006602822，您可以随时享有我们的体贴服务。手机客户端已上线，手机客户端下载http://t.cn/zTHB2Nv'
	    	);
	    	$this->curl_sms($send_add_user_data);
	    	$model_sms = D('Sms');
	    	$now = time();
			$send_add_user_data['sendtime'] = $now;
			$model_sms->add($send_add_user_data);
			echo 1;exit;
	    }
	}

	function test_sms(){
		$send_add_user_data = array(
				'phones'=>13661743916,
				'content'=>'验证码：0000123'
	    	);
		$a = $this->curl_sms($send_add_user_data);
		print_r($a);
	}
}
?>