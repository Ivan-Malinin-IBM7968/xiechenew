<?php
/*
 * 用户管理
 */
class MemberAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->MembercouponModel = D('membercoupon');//优惠卷信息
		$this->MemberModel = D('member');//用户表
		$this->ShopModel = D('Shop');//商铺表
		$this->CouponModel = D('coupon');//优惠券信息表
		$this->model_order_90 = D('order_90');//90天回访用户
		$this->model_feedback = D('order_90_feedback');//回访数据
	}


	function _filter(&$map) {
		if(isset($_REQUEST['type']) && $_REQUEST['keyword'] !=''){
		    if ($_REQUEST['type']=='uid'){
			    $map[$_REQUEST['type']] = $_REQUEST['keyword'];
		    }else{
			    $map[$_REQUEST['type']] = array('like', "%" . $_REQUEST['keyword'] . "%");
		    }
		}
	}
	
	public function sendMsg() {
		try {
			$mobile = @$_POST['mobile'];
			if (!$mobile) {
				throw new Exception('电话号码不能为空');
			}
			$content = @$_POST['content'];
			if (!$content) {
				throw new Exception('发送内容不能为空');
			}
			$model_sms = M('tp_xieche.sms','xc_');//记录发送
			$count = $model_sms->field('content')->where(array('phones'=>$mobile,'type'=>2))->find();
			if ($count) {
				$kefu = explode(' ',$count['content']);
				$name = @$kefu[1];
				$msg = '该用户已经被'.$name.'发送过了，不能重复发送';
				throw new Exception($msg);
			}
			$uid = $_SESSION['authId'];
			$staff = array(
					1	=> '6002',//admin
					223 => '6002',//王俊炜
					171 => '6003',//彭晓文
					267 => '6004',//朱迎春
					259 => '6005',//乔敬超
					234 => '6006',//张美婷
					243 => '6007',//黄美琴
					242 => '6008',//李宝峰
					266 => '6010',//黄赟
					252	=> '6009',//周祥金
					268 => '6001',//周洋
					269 => '6005'//寇建学
			);
			$username = array(
					1	=> 'admin',
					223 => '王俊炜',
					171 => '彭晓文',
					259 => '乔敬超',
					234 => '张美婷',
					243 => '黄美琴',
					242 => '李宝峰',
					251 => '庄玉成',
					252	=> '周祥金',
					267 => '朱迎春',
					266 => '黄赟',
					268 => '周洋',
					269 => '寇建学'
			);
			//$url = 'http://114.80.208.222:8080/NOSmsPlatform/server/SMServer.htm';
			$url = 'http://58.83.147.92:8080/qxt/smssenderv2';
			$header = array("Content-Type: application/x-www-form-urlencoded;charset=GBK");
			$content = '【携车网-府上养车】你很忙，我们知道，99元上门为您保养爱车，4S品质，省钱省事。另有4S店预约折扣、车辆维修返利等，好多便宜。400-660-2822，服务工号：'.$staff[$uid].' '.$username[$uid];
			
			$param = http_build_query(array(
					'user'=>'zs_donghua',
					'password'=>md5('121212'),
					'tele'=>$mobile,
					'msg'=>iconv("utf-8","gbk//IGNORE",$content),
				)
			);
			
			$res = $this->_curl($url,$param,$header);

			if(substr($res,0,2)=='ok'){
				$data = array(
						'phones' => $mobile,
						'content' => $content,
						'type'=>2,
						'sendtime'=>time()
				);
				$add = $model_sms->add($data);
				
				$array = array(
						'oid'=>'00',
						'operate_id'=>$_SESSION['authId'],
						'log'=>'发送了消息:'.$content
						);
				$this->addOperateLog($array);

				if ($add) {
					$ret = array(
							'msg'=>'success',
							'status'=>1
					);
					echo json_encode($ret);
				}else{
					throw new Exception('插入数据失败');
				}
				
			}else{
				throw new Exception('发送失败');
			}
			
		}catch (Exception $e){
			$msg = $e->getMessage();
			$ret = array(
					'msg'=>$msg,
					'status'=>0
			);
			echo json_encode($ret);
		}
	}

	public function _trans_data(&$list){
		$shop_model = D('Shop');
		$comment_model = D('Comment');
		$MemberModel = D('member');
		$MembersalecouponModel = D('membersalecoupon');
		$member_from = C('MEMBER_FORM');
		
		
		$shop_list = $shop_model->select();
		foreach($list AS $kk=>$vv){
			$member = $this->MemberModel->where(array('uid'=>$vv['uid']))->find();
			foreach ($member_from as $k=>$v){
				if($member['fromstatus'] == $k){
					$list[$kk]['fromstatus'] = $v;
				}
			}
			
			$Membersalecoupon = $MembersalecouponModel->where(array('uid'=>$vv['uid']))->find();
			if($Membersalecoupon){
				$list[$kk]['sale'] = "已领";
			}else{
				$list[$kk]['sale'] = "<a href='__APP__/Store/salecoupon/add_membersalecoupon/coupon_id/1/mobile/".$vv['mobile']."' target='_blank'>点击领取</a>";
			}
		}
		return $list;
	}

    function _before_add(){
        if ($_GET['mobile'] and eregi("^1[0-9]{10}$",$_GET['mobile'])){
            $come_phone = $_GET['mobile'];
		}else {
            $come_phone = '';
		}
		$this->assign('mobile',$come_phone);
    }

	function read(){
		//echo '111';
		//dump($_REQUEST);
		if(isset($_REQUEST['id']) && $_REQUEST['id'] !=''){
			$map['uid'] = array('eq', $_REQUEST['id']);
		}
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile'] !=''){
			$map['mobile'] = array('eq', $_REQUEST['mobile']);
		}
		if(isset($_REQUEST['phonenum']) && $_REQUEST['phonenum'] !=''){
			$map['mobile'] = array('eq', $_REQUEST['phonenum']);
		}
			$member_model = D('Member');
			$member_info = $member_model ->where($map)->find();
			if(!$member_info){
				header("Content-Type: text/plain; charset=utf-8");
				$this->redirect('Store/member/add/',array('mobile'=>$_REQUEST['phonenum']),2,'无此用户，跳转到添加用户页面~');
			}
		//	echo $member_model->getlastsql();
			//dump($member_info);

			//获取用户回访记录
			$feedback_info=$this->model_feedback->where("uid = $member_info[uid]")->select();
			$model_user = M('tp_admin.user','xc_');
			if(is_array($feedback_info)){
				foreach($feedback_info as $key=>$value){
					$user_info = $model_user ->where("id = $value[operator_id]")->field('nickname')->find();
					$feedback_info[$key]['operator_name'] = $user_info['nickname'];
				}
			}
		//获取用户订单
		$reservation_order = D('reservation_order');
		$bidorder = D('bidorder');
		//获取用户上门保养的信息
		//$map1['uid'] =$member_info['uid'];
		$map1['mobile'] = $member_info['mobile'];
		$re_order_info = $reservation_order->where($map1)->select();
		$checkreport_total = D('checkreport_total');

		foreach($re_order_info as $k=>$v )
		{
			$checkrp_total_info = $checkreport_total->where("reservation_id = $v[id]")->order('create_time desc')->find();
			$re_order_info[$k]['checkreport_id'] = base64_encode($checkrp_total_info[id].'168');
			$re_order_info[$k][id] = $this->get_orderid($v[id]);
		}
		//dump($re_order_info);
		//获取用户事故车的信息
		$bidorder_info = $bidorder->where("uid = $member_info[uid]")->select();

		//获取用户记录
			$order_90_info=$this->model_order_90->where("uid = $member_info[uid]")->find();

			$membercar_model = D('Membercar');
			$membercar_info = $membercar_model ->where("uid = $member_info[uid]")->order("status DESC")->select();
			$order_model = D('Order');
			$order_info =$order_model->where("uid = $member_info[uid]")->order('create_time desc')->select();
			foreach($membercar_info AS $k=>$v){
				$car_list = $this->Membercar_format_by_model_id($membercar_info[$k]['model_id']);
				$membercar_info[$k]['car_info'] = $car_list;
			}

			$model_webusercar = D('Webusercar');
			$webusercar_info = $model_webusercar->where(array('uid'=>$member_info['uid']))->find();
			if($webusercar_info) {
				$webusercar = $this->get_car_info($webusercar_info['brand_id'],$webusercar_info['series_id'],$webusercar_info['model_id']);
			}
			/*用户购买优惠卷信息*/
			$membercoupon['is_delete'] = 0;
			$membercoupon['uid'] = $_REQUEST['id'];
			$count = $this->MembercouponModel->where($membercoupon)->count();
			// 导入分页类
			import("ORG.Util.Page");
			// 实例化分页类
			$p = new Page($count, 100);
			// 分页显示输出
			 foreach ($_POST as $key => $val) {
				if (!is_array($val) && $val != "" ) {
					$p->parameter .= "$key/" . urlencode($val) . "/";
				}
			}
			$page = $p->show_admin();
			$list = $this->MembercouponModel->order('membercoupon_id DESC')->where($membercoupon)->limit($p->firstRow.','.$p->listRows)->select();
			if($list){
				foreach($list as $k=>$v){
					$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
					$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
					$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
					$list[$k]['username'] = $member['username'];
					$list[$k]['shop_name'] = $shop['shop_name'];
					$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
					$list[$k]['cost_price'] = $coupon['cost_price'];
				}
			}
			//显示外呼oracle数据
			$url = 'http://www.fqcd.3322.org:88/api.php';
			$post = array(
					'task'=>3,
					'code'=> 'fqcd123223',
					'call'=>$member_info['mobile']
			);
// 			$data = $this->_curl($url,$post);
// 			$return= json_decode($data,true);
$return =false;
			if ($return){
				$string = '<table style="margin-bottom:20px">';
				foreach ($return as  $key=>$val){
					$string .='<tr><td>'.$key.':</td>';
					foreach ($val as $td){
						if ($key == '当前状态') {
							switch ($td){
								case 1:
									$msg = '未分配';
									break;
								case 2:
									$msg = '已完成';
									break;
								case 3:
									$msg = '预约';
									break;
								case 4:
									$msg = '继续拨打';
									break;
								case 5:
									$msg = '已分配';
									break;
								default:
									$msg = '6';
									break;
							}
							$string .= '<td>'.$msg.'</td>';
						}else{
							$string .= '<td>'.$td.'</td>';
						}
					}
					$string .='</tr>';
				}
				$string .= '</tr></table>';
			}
			if(isset($string)){
				$this->assign('string',$string);
			}
			$where = array( 'admin_id' => $_SESSION['authId'] );
			$mStaff = M('tp_xieche.staff_oracle','xc_');
			$staff = $mStaff->where($where)->find();
			$this->assign('staff',$staff);
			
			$this->assign('data',$data);
			$this->assign('list',$list);
			$this->assign('page',$page);
			$this->assign('webusercar_info',$webusercar_info);//用户自定义车型
			/*用户购买优惠卷信息*/
			$this->assign('member_info',$member_info);
			$this->assign('feedback_info',$feedback_info);
			$this->assign('is_close',$order_90_info['is_close']);
			$this->assign('membercar_info',$membercar_info);
			$this->assign('order_info',$order_info);
			$this->assign('re_order_info',$re_order_info);
			$this->assign('bidorder_info',$bidorder_info);
			$this->display();
	}
	private function _curl($url, $post = NULL, $host = NULL) {
		$userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:12.0) Gecko/20100101 Firefox/12.0';
		$cookieFile = NULL;
		$hCURL = curl_init ();
		curl_setopt ( $hCURL, CURLOPT_URL, $url );
		curl_setopt ( $hCURL, CURLOPT_TIMEOUT, 30 );
		curl_setopt ( $hCURL, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt ( $hCURL, CURLOPT_USERAGENT, $userAgent );
		curl_setopt ( $hCURL, CURLOPT_FOLLOWLOCATION, TRUE );
		curl_setopt ( $hCURL, CURLOPT_AUTOREFERER, TRUE );
		curl_setopt ( $hCURL, CURLOPT_ENCODING, "gzip,deflate" );
		curl_setopt ( $hCURL, CURLOPT_HTTPHEADER, $host );
		if ($post) {
			curl_setopt ( $hCURL, CURLOPT_POST, 1 );
			curl_setopt ( $hCURL, CURLOPT_POSTFIELDS, $post );
		}
	
		$sContent = curl_exec ( $hCURL );
		// var_dump(curl_getinfo($hCURL));exit;
		if ($sContent === FALSE) {
			$error = curl_error ( $hCURL );
			curl_close ( $hCURL );
				
			throw new \Exception ( $error . ' Url : ' . $url );
		} else {
			curl_close ( $hCURL );
			return $sContent;
		}
	}

	/*
		@author:chf
		@function:显示用户购买的优惠卷信息页
		@time:2013-04-15
	*/
	function CouponRead(){
		//echo '111';
		//dump($_REQUEST);
		if(isset($_REQUEST['id']) && $_REQUEST['id'] !=''){
			$map['uid'] = array('eq', $_REQUEST['id']);
		}
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile'] !=''){
			$map['mobile'] = array('eq', $_REQUEST['mobile']);
		}
		if(isset($_REQUEST['phonenum']) && $_REQUEST['phonenum'] !=''){
			$map['mobile'] = array('eq', $_REQUEST['phonenum']);
		}
			$member_model = D('Member');
			$member_info = $member_model ->where($map)->find();
			if(!$member_info){
				header("Content-Type: text/plain; charset=utf-8");
				$this->redirect('Store/member/add/',array('mobile'=>$_REQUEST['phonenum']),2,'无此用户，跳转到添加用户页面~');
			}
		

			$MemberCoupon = $this->MembercouponModel->where(array('uid'=>$member_info['uid']))->select();
			if($MemberCoupon){
				foreach($MemberCoupon as $k=>$v){
					$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
					$MemberCoupon[$k]['username'] = $member['username'];
					$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
					$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
					$MemberCoupon[$k]['shop_name'] = $shop['shop_name'];
					$MemberCoupon[$k]['coupon_amount'] = $coupon['coupon_amount'];

					$MemberCoupon[$k]['cost_price'] = $coupon['cost_price'];
				}
			}
			$this->assign('member_info',$member_info);
			
			$this->assign('MemberCoupon',$MemberCoupon);
			$this->display('CouponRead');
	}



	public function check_cardid(){
	    $cardid = isset($_POST['cardid'])?$_POST['cardid']:0;
	    if ($cardid){
	        $model_card = D(GROUP_NAME.'/Card');
	        $condition['cardid'] = $cardid;
	        $cardinfo = $model_card->where($condition)->find();
	        if ($cardinfo and $cardinfo['card_state'] == 1){
	            $model_member = D(GROUP_NAME.'/Member');
	            $memberinfo = $model_member->where($condition)->find();
	            //echo $model_member->getLastSql();
	            if ($memberinfo['mobile']){
	                echo $memberinfo['mobile'];
	            }else {
	                echo 1;
	            }
	        }elseif($cardinfo and $cardinfo['card_state'] == 0){
	            echo 1;
	        }else {
	            echo -1;
	        }
	    }else{
	        echo -2;
	    }
	    exit;
	}
	public function _before_insert(){
		if($_POST['mobile']){
    		if ($_POST['mobile'] and !eregi("^1[0-9]{10}$",$_POST['mobile'])){
    		    $this->error('手机号码格式错误！');exit;
    		}
    		$cardid = isset($_POST['cardid'])?$_POST['cardid']:0;
		    if ($cardid){
		        if (!eregi("^6[0-9]{7}$",$cardid)){
    		        $this->error('卡号格式错误！');exit;
		        }
		        $model_card = D(GROUP_NAME.'/Card');
		        $condition['cardid'] = $cardid;
		        if(!$model_card->where($condition)->find()){
		            $this->error("卡号不存在！");
		        }
    		}
			$member_model = D('Member');
			$mobile = $_POST['mobile'];
			$check = $member_model->where("mobile = '$mobile'")->find();
			if(!$check){
				$rand = rand_string(6,1);
				//$_POST['username'] = 'xieche_'.$rand.'_'.time();
				$_POST['password'] = md5($rand);
				$_POST['cardid'] = $cardid;
				$_POST['rand'] = $rand;
				$_POST['reg_time'] = time();
				$_POST['ip'] = get_client_ip();
			}else{
			    if($cardid and !$check['cardid']){
			        $condition['mobile'] = $mobile;
			        $data['cardid'] = $cardid;
			        if ($member_model->where($condition)->save($data)){
			            $this->card_login($cardid);
			            $str = "卡号".$cardid."绑定到该手机号！";
			        }
			    }else{
			        $str = '';
			    }
				$this->error('号码已经存在'.$str,U('/Store/member/read/phonenum/'.$mobile));
				exit;
			}
		}else {
		    $this->error('请输入手机号码!');exit;
		}
	}
	public function card_login($cardid){
	    $model_card = D(GROUP_NAME.'/Card');
	    $condition['cardid'] = $cardid;
	    $data['card_state'] = 1;
	    $data['login_time'] = time();
	    $model_card->where($condition)->save($data);
	}
	
	public function sendsms(){
	    $mobile = isset($_POST['mobile'])?$_POST['mobile']:0;
	    if ($mobile) {
	    	$send_add_user_data = array(
				'phones'=>$mobile,
				'content'=>'携车网是一家免费在线预约各大汽车品牌4S店保养维修的网站，通过网站预约最低五折起，还有保养套餐和现金券团购。访问网址'.WEB_ROOT.'或拨打客服电话4006602822，您可以随时享有我们的体贴服务。手机客户端已上线，手机客户端下载http://t.cn/zTHB2Nv'
	    	);
	    	curl_sms_other($send_add_user_data);
	    	$model_sms = D('Sms');
	    	$now = time();
			$send_add_user_data['sendtime'] = $now;
			$model_sms->add($send_add_user_data);
			echo 1;exit;
	    }
	}

	public function _tigger_insert($model){
		//dump($model);

//		if($model->id and $_POST['reg']==1){ // dingjb 2015-09-18 14:44:41 删除无用判断 $_POST['reg'] == 1
		if($model->id ) {
		        $ql_huodong = $_POST['ql_huodong'];
		        $huodong_str = "";
		        if ($ql_huodong){
		            $huodong_str = "3折工时优惠券稍后发到您的帐户中，如果您参与今天下午2点的抢楼活动更有机会获得工时费全免券，请访问".WEB_ROOT."qg/ 来参与活动。";
		        }
				$send_add_user_data = array(
				'phones'=>$_POST['mobile'],
				//'content'=>'亲爱的用户，您可以使用您的手机号码'.$_POST['mobile'].'，密码'.$_POST['rand'].'来登录携车网www.xieche.com.cn。携车网是一家在线预订各大汽车品牌4S店保养维修的网站，通过网站预订最低五折起，还有保养套餐和现金券团购。手机客户端已上线，手机客户端下载http://t.cn/zTHB2Nv，客服4006602822。'.$huodong_str,
				'content'=>'您已注册成功，您可以使用您的手机号码'.$_POST['mobile'].'，密码'.$_POST['rand'].'来登录携车网，客服4006602822。'
			    );

				

			
			$this->curl_sms($send_add_user_data);

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

			$this->success('新增成功，跳转中~~',U('/Store/member/read/phonenum/'.$_POST['mobile']));
			exit;
		}else{
			$this->error('新增失败');exit;
		}

	}

	/*
	 * 通过model_id来获取车型对应数据
	 * 初始化数据
	 * 
	 */
	public function Membercar_format_by_model_id($model_id) {
		$model_brand = D('carbrand');
		$model_series = D('carseries');
		$model_model = D('carmodel');
		if(!empty($model_id)){
			$list_default_model = $model_model->getByModel_id($model_id);
			//echo $model_model->getLastSql();
			$list_default_series = $model_series->getBySeries_id($list_default_model['series_id']);
		//	dump($list_default_series);
			$list_default_brand = $model_brand->getByBrand_id($list_default_series['brand_id']);
			
		}
		if($list_default_brand && $list_default_model && $list_default_series){
			$list['brand_name'] = $list_default_brand['brand_name'];
			$list['series_name'] = $list_default_series['series_name'];
			$list['model_name'] = $list_default_model['model_name'];
			//dump($list);
			return $list;
		}else{
				
			return false;
		}
	}

	public function savememo(){
	    $memo = isset($_REQUEST['memo'])?$_REQUEST['memo']:'';
	    $id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
	    if ($id){
	        $model_member = D('Member');
	        $condition['uid'] = $id;
	        $data['memo'] = $memo;
	        if($model_member->where($condition)->save($data)){
	            $this->success("保存成功！");
	        }
	    }
	}
    
	public function resetpw(){
	    $id = isset($_GET['id'])?$_GET['id']:0;
	    if ($id){
	        $model_member = D('Member');
	        $condition['uid'] = $id;
	        $rand = rand_string(6,1);
	        $data['password'] = md5($rand);
	        if($model_member->where($condition)->save($data)){
	            $memberinfo = $model_member->find($id);
	            if ($memberinfo['mobile']){
    	            $send_add_user_data = array(
    					'phones'=>$memberinfo['mobile'],
						'content'=>'您已注册成功，您可以使用您的手机号码'.$_POST['mobile'].'，密码'.$rand.'来登录携车网，客服4006602822。',
    					//'content'=>'亲爱的用户，您可以使用您的手机号码'.$_POST['mobile'].'，密码'.$rand.'来登录携车网www.xieche.com.cn。携车网是一家在线预订制各大汽车品牌4S店保养维修的网站，通过网站预订最低五折起，还有保养套餐和现金券团购。手机客户端已上线，手机客户端下载http://t.cn/zTHB2Nv，客服4006602822。'.$huodong_str,
    			    );
    			    $result = curl_sms($send_add_user_data);
    			    //echo '<pre>';print_r($result);
    			    $model_sms = D('Sms');
        			$now = time();
        			$send_add_user_data['sendtime'] = $now;
        			$model_sms->add($send_add_user_data);
    			    $this->success("密码重置成功！");
	            }
	        }
	    }
	}
    public function coupon(){
        $uid = $_REQUEST['id'];
        $model_membercoupon = D('Membercoupon');
        $map['xc_membercoupon.uid'] = $uid;
        $map['xc_membercoupon.is_delete'] = 0;
        $coupon = $model_membercoupon->where($map)->join("xc_coupon ON xc_membercoupon.coupon_id=xc_coupon.id")->select();
        $this->assign('coupon',$coupon);
        $this->display();
    }
    
    public function add_coupon(){
        $uid = $_REQUEST['id'];
        Cookie::set('_currentUrl_', __SELF__);
        $model_coupon = D(GROUP_NAME.'/Coupon');
        $model_serviceitem = D(GROUP_NAME.'/Serviceitem');
        $map['is_delete'] = 0;
        // 计算总数
        $count = $model_coupon->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_coupon->where($map)->order('id ASC')->limit($p->firstRow.','.$p->listRows)->select();
        if (!empty($list)){
            foreach ($list as $k=>$v){
                if ($v['service_ids']){
                    $list[$k]['service_info'] = $model_serviceitem->get_servicename($v['service_ids']);
                }
            }
        }
        // 赋值赋值
        $this->assign('uid', $uid);
        $this->assign('page', $page);
        $this->assign('list', $list);
        
        $this->display();
    }
    
    public function del(){
        $membercoupon_id = $_POST['membercoupon_id'];
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
        $map['membercoupon_id'] = $membercoupon_id;
        $data['is_delete'] = 1;
        if($model_membercoupon->where($map)->save($data)){
            echo 1;
        }else{
            echo 0;
        }
        exit;
    }

	/*
		author:chf
		function:修改用户来源于哪里
		time:2013-7-29
	
	*/
	function edit(){
		if(isset($_REQUEST['id']) && $_REQUEST['id'] !=''){
			$map['uid'] = array('eq', $_REQUEST['id']);
		}
		$data = $this->MemberModel->where(array('uid'=>$map['uid']))->find();
		$this->assign('data',$data);
		$this->assign('uid',$_REQUEST['id']);
		$this->display('editfrom');
	}
	/*	author:chf
		function:操作修改用户来源
		time:2013-7-29
	*/
	function edit_do(){
		$map['uid'] = array('eq', $_REQUEST['uid']);
		$edit['fromstatus'] = $_REQUEST['fromstatus'];
		$edit['memo'] = $_REQUEST['memo'];
		$this->MemberModel->where(array('uid'=>$map['uid']))->data($edit)->save();
		if($_REQUEST['memo']) {
			$this->success('修改成功，跳转中~~',U("/Store/Member/CouponRead/id/{$_REQUEST[uid]}"));
		}else {
			$this->success('修改成功，跳转中~~',U('/Store/Member/index'));
		}
	}
    
	public function add_webusercar() {
		$uid = $_REQUEST['uid'];

		$this->assign("uid",$uid);
		$this->display();
	}

	public function edit_webusercar() {
		$id = $_REQUEST['id'];
		$uid = $_REQUEST['uid'];
		
		$model_webusercar =D(GROUP_NAME.'/Webusercar');
		$webusercar_info = $model_webusercar->find($id);
		if($webusercar_info['car_number']) {
			$licenseplate = explode("_" , $webusercar_info['car_number']);
			$webusercar_info['s_pro'] = $licenseplate[0];
			$webusercar_info['car_number'] = $licenseplate[1];
		}
		$this->assign("id",$id);
		$this->assign("uid",$uid);
		$this->assign("webusercar_info",$webusercar_info);
		$this->display();
	}

	public function add_webusercar_do() {
		$model_webusercar =D(GROUP_NAME.'/Webusercar');
		if (isset($_REQUEST['s_pro']) and isset($_REQUEST['car_number'])){
			$data['car_number'] = $_REQUEST['s_pro']."_".$_REQUEST['car_number'];
		}
		if (empty($_REQUEST['brand_id']) || empty($_REQUEST['series_id']) ){
			$this->error("车辆信息最少选到第二级！");
		}
		$data['brand_id'] = $_REQUEST['brand_id'];
		$data['series_id'] = $_REQUEST['series_id'];
		$data['model_id'] = $_REQUEST['model_id'];

		$data['create_time'] = time();
		$data['uid'] = $_REQUEST['uid'];
		
		$model_webusercar->add($data);
		$this->success("添加成功！","read/id/$_REQUEST[uid]");
	}

	public function edit_webusercar_do() {
		$id = $_REQUEST['id'];
		$uid = $_REQUEST['uid'];

		$model_webusercar =D(GROUP_NAME.'/Webusercar');
		if (isset($_REQUEST['s_pro']) and isset($_REQUEST['car_number'])){
			$data['car_number'] = $_REQUEST['s_pro']."_".$_REQUEST['car_number'];
		}
		if (empty($_REQUEST['brand_id']) || empty($_REQUEST['series_id']) ){
			$this->error("车辆信息最少选到第二级！");
		}

		$data['brand_id'] = $_REQUEST['brand_id'];
		$data['series_id'] = $_REQUEST['series_id'];
		$data['model_id'] = $_REQUEST['model_id'];

		$data['create_time'] = time();
		
		$model_webusercar->where(array('id'=>$id))->save($data);
		$this->success("修改成功！","read/id/$uid");
	}

	/*
		@author:ysh
		@function:得到汽车信息
		@time:2013/5/24
	*/
	function get_car_info($brand_id,$series_id,$model_id) {
		if ($brand_id){
			$model_carbrand = D('Carbrand');
			$map_b['brand_id'] = $brand_id;
			$brand = $model_carbrand->where($map_b)->find();
		}
		if ($series_id){
			$model_carseries = D('Carseries');
			$map_s['series_id'] = $series_id;
			$series = $model_carseries->where($map_s)->find();
		}
		if ($model_id){
			$model_carmodel = D('Carmodel');
			$map_m['model_id'] = $model_id;
			$model = $model_carmodel->where($map_m)->find();
		}
		return $brand['brand_name']." ".$series['series_name']." ".$model['model_name'] ;
	}

	/*
		@author:wwy
		@function:近90天未下单的老用户列表
		@time:2014/4/2
	*/
	public function feedback() {
		Cookie::set('_currentUrl_', __SELF__);
		$model_order90=D(GROUP_NAME.'/Order_90');
		if($_REQUEST['start_time']==$_REQUEST['end_time'] and $_REQUEST['start_time']>0){ 
		$_REQUEST['end_time']=date('Y-m-d',strtotime('+1 day',strtotime($_REQUEST['start_time']))); }
		if($_REQUEST['start_time']>0){
			$start_time=strtotime($_REQUEST['start_time']);
			$map['recall_time'] = array('egt',$start_time);
			$this->assign('start_time', $_REQUEST['start_time']);
		}
		if($_REQUEST['end_time']>0){
			$end_time=strtotime($_REQUEST['end_time']);
			$map['recall_time'] = array('elt',$end_time);
			$this->assign('end_time', $_REQUEST['end_time']);
		}
		if($_REQUEST['end_time']>0 and $_REQUEST['end_time']>0){
			$map['recall_time'] = array(array('egt',$start_time),array('elt',$end_time));
		}
		if($_REQUEST['mobile']>0){
			$map['mobile'] = $_REQUEST['mobile'];
		}
		if($_REQUEST['recall_time']=='null'){
			$map['recall_time'] = '0';
		}
		$map['is_close'] = 'false';
		/**导出数据start**/
        $execl = $_REQUEST['execl'];
		if($execl){
			$list_execl=$model_order90->order('last_order_time ASC')->select();
			if($list_execl){
				foreach($list_execl as $k=>$v){
					$map['uid'] = $v['uid'];
					$user_name = $this->MemberModel->where($map)->field('username')->find();
					$list_execl[$k]['user_name']= $user_name['username'];
					$list_execl[$k]['last_order_time']=date('Y-m-d',$v['last_order_time']);
					$list_execl[$k]['create_time']=date('Y-m-d',$v['create_time']);
				}
			}
			$this->export_execl($list_execl,'90天回访.xls');
		}
		/**导出数据end**/

        // 计算总数
        $count = $model_order90->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询

		$list=$model_order90->where($map)->order('recall_time ASC,last_order_time ASC')->limit($p->firstRow.','.$p->listRows)->select();
		echo $model_order90->getLastsql();
		if($list){
			foreach($list as $k=>$v){
				$mad['uid'] = $v['uid'];
				$user_name = $this->MemberModel->where($mad)->field('username')->find();
				$list[$k]['user_name']= $user_name['username'];
			}
		}
		//print_r($list);
		$this->assign("list",$list);
		$this->assign("page", $page);
		$this->display();
	}

	function export_execl($list,$filename){
		if($list){
			foreach($list as $key=>$val){
				if ($n%2==1){
					$color = "#CCDDDD";
				}else {
					$color = "#FFFFFF";
				}
				$str_table .= '<tr bgcolor='.$color.'><td>'.$val['id'].'</td><td>'.$val['order_id'].'</td><td>'.$val['user_name'].'</td><td>'.$val['mobile'].'</td><td>'.$val['operator_remark'].'</td><td>'.$val['last_order_time'].'</td><td>'.$val['create_time'].'</td>';
			}
		}
		$color = "#00CD34";
	    header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename='{$filename}'");
        $str = '<table><tr bgcolor='.$color.'><td>编号</td><td>订单号</td><td>用户名</td><td>电话号码</td><td>客服备注</td><td>最后下单时间</td><td>统计时间</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}

	//提交回访备注，修改下次回访时间以及是否关闭该用户回访
	function insert_feedbackinfo(){
		$map['uid'] = $_REQUEST['uid'];
		$info=$this->model_order_90->where(array('uid'=>$map['uid']))->find();
		if($info){
			$update['is_close'] = $_REQUEST['is_close'];
			$update['recall_time'] = strtotime($_REQUEST['recall_time']);
			$this->model_order_90->where(array('uid'=>$map['uid']))->data($update)->save();
			//echo $this->model_order_90->getLastsql();
		}else{
			$add['uid'] =  $_REQUEST['uid'];
			$add['mobile'] = $_REQUEST['mobile'];
			$add['operator_remark'] = '非脚本创建';
			$add['create_time'] = time();
			$add['recall_time'] = strtotime($_REQUEST['recall_time']);
			$this->model_order_90->data($add)->add();
			//echo $this->model_order_90->getLastsql();
		}
		$insert['content'] = $_REQUEST['content'];
		$insert['uid'] = $_REQUEST['uid'];
		$insert['operator_id'] = $_SESSION['authId'];
		$insert['create_time'] = time();
		$result=$this->model_feedback->data($insert)->add();
		//echo $this->model_feedback->getLastsql();
		if($result){
			$this->success('提交成功，跳转中~~',U('/Store/Member/read/'.$_REQUEST[uid]));
		}else{
			$this->success('提交失败，请确认~~',U('/Store/Member/read/'.$_REQUEST[uid]));
		}
	}
}
