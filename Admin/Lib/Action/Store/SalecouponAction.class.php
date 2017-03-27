<?php
class SalecouponAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->salecouponModel = D('Salecoupon');//优惠卷表
		$this->ShopModel = D('shop');//店铺表
		$this->MemberModel = D('Member');//用户表
		$this->MembersalecouponModel = D('Membersalecoupon');//抵用券表
		$this->model_sms = D('Sms');
		$this->LotteryModel = D('lottery');//抽奖监控表
		$this->LotteryloginModel = D('lotterylogin');//抽奖页登录监控表
		$this->LotteryappModel = D('lotteryapp');//下载APP登录监控表
	}
	
    public function index(){
		Cookie::set('_currentUrl_', __SELF__);
        $model_salecoupon = D(GROUP_NAME.'/Salecoupon');
        $model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');
		if($_REQUEST['coupon_name']){
			$map['coupon_name'] = array('LIKE','%'.$_REQUEST['coupon_name'].'%');
			$data['coupon_name'] = $_REQUEST['coupon_name'];
		}
		if($_REQUEST['shop_id']){
			$data['shop_id'] = $map['_string'] = "FIND_IN_SET('{$_REQUEST[shop_id]}', shop_ids)";
		}
        // 计算总数
        $count = $model_salecoupon->where($map)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_salecoupon->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if (!empty($list)){
            foreach ($list as $k=>$v){
				$map_c = array();
                $map_c['salecoupon_id'] = $v['id'];
                $list[$k]['coupon_number'] = $model_membersalecoupon->where($map_c)->count();
				$shop_count =explode(',' , $v['shop_ids']);
				$list[$k]['shop_count'] = count($shop_count);
            }
        }
        // 赋值赋值
		$data['shop_list'] = $this->ShopModel->where(array('shop_city'=>'3306'))->select();
        $this->assign('page', $page);
		$this->assign('data', $data);
        $this->assign('list', $list);
        $this->display();
    }

	/*
		@author:chf
		@function:绑定驴妈妈
		@time:2013-11-01
	*/
	function show_addlmm(){
		
		$this->display();
	}

	/*
		@author:chf
		@function:领取驴妈妈抵用券操作
		@time:2013-11-01
	*/
	function add_lmm(){
		$FROM = array('10'=>'vw','11'=>'svw','12'=>'bk','13'=>'xfl','14'=>'skd','15'=>'ft','16'=>'rc','17'=>'kia','18'=>'yh','19'=>'th','20'=>'sh','22'=>'lmm');//判断活动页面来源数组
		$mobile = $_POST['mobile'];
		//判断验证码
		if($mobile){
				$map['mobile'] = $mobile;
				$res = $this->MemberModel->where(array('mobile'=>$map['mobile'],'status'=>'1'))->find();
				if($res){
					$member['uid'] = $res['uid'];
				}else{
					$mobile_verify_lottery = round(13245,92457);
					$member_data['mobile'] = $mobile;
					$member_data['password'] = md5($mobile_verify_lottery);
					$member_data['reg_time'] = time();
					$member_data['ip'] = $_SERVER["REMOTE_ADDR"];
					$member_data['fromstatus'] = '22';
				
					$member['uid'] = $this->MemberModel->data($member_data)->add();
					
					$send_add_user_data = array(
						'phones'=>$mobile,
						'content'=>'亲爱的用户，您可以使用您的手机号码'.$mobile.'，密码'.$mobile_verify_lottery.'来登录携车网。携车网是一家在线预订各大汽车品牌4S店保养维修的网站，通过网站预约最低五折起，还有保养套餐和现金券团购。手机客户端已上线，手机客户端下载http://www.xieche.com.cn/download.php，客服4006602822。',
					);
					
					$this->curl_sms($send_add_user_data);
					$send_add_user_data['sendtime'] = time();
					$this->model_sms->data($send_add_user_data)->add();

					$model_memberlog = D('Memberlog');
					$data['createtime'] = time();
					$data['mobile'] = $mobile;
					$data['memo'] = '用户注册';
					$model_memberlog->data($data)->add();
					$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
					//$this->save_session($res);
				}
					
					$membersalesql['salecoupon_id'] = 2;
					$membersalesql['mobile'] = $mobile;
					$Membersalecount = $this->MembersalecouponModel->where($membersalesql)->count();
					$Membersale = $this->MembersalecouponModel->where($membersalesql)->find();
					
					$_SESSION['coupon_code'] = $Membersale['coupon_code'];
					if($Membersalecount == '0'){
						$model_salecoupon = D('Salecoupon');
						$salecoupon = $model_salecoupon->find('2');
						//插入membersalecoupon表
						$Membersalecoupon['coupon_name'] = $salecoupon['coupon_name'];
						$Membersalecoupon['salecoupon_id'] = $salecoupon['id'];
						$Membersalecoupon['mobile'] = $mobile;
						
						$Membersalecoupon['create_time'] = time();
						$Membersalecoupon['start_time'] = $salecoupon['start_time'];
						$Membersalecoupon['end_time'] = $salecoupon['end_time'];
						$Membersalecoupon['ratio'] = $salecoupon['jiesuan_money'];
						$Membersalecoupon['shop_ids'] = $salecoupon['shop_ids'];
						
						$coupon_code = '4006602822lv';
						

						$Membersalecoupon['coupon_code'] = '4006602822lv';
						
						$Membersalecoupon['from'] = 'lmm';//来源
						$Membersalecoupon['uid'] = $member['uid'];
						$membersalecoupon_id = $this->MembersalecouponModel->add($Membersalecoupon);
						$start_time = date('Y-m-d H:i',$salecoupon['start_time']);
						$end_time = date('Y-m-d',$salecoupon['end_time']);
						
						//您获取的促销抵用券编号:1165（携车网50元折后现金抵用券）已送达您的账户.请通过携车网预约后凭消费码:6471473686至指定4S店于有效期内(截至2014-10-10)消费，使用规则和适用店铺详见http://www.xieche.com.cn/****，客服电话4006602822
						
						$verify_str = "您获取的抵用券:（".$salecoupon['coupon_name']."）已送达您的账户.请通过携车网预约后凭消费码:".$coupon_code."至指定4S店于有效期内(截至".$end_time.")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服4006602822";

						//$verify_str = "您的抵用券(".$salecoupon['coupon_name']."金额:".$salecoupon['coupon_amount'].")请凭消费码至指定商铺处使用,有效期(".$start_time."至".$end_time.")抵用卷消费码:".$coupon_code;

						$send_verify = array(
							'phones'=>$mobile,
							'content'=>$verify_str,
						);
						$this->curl_sms($send_verify);
						
						$send_verify['sendtime'] = time();
						$this->model_sms->add($send_verify);
						
						$lottey['system'] =  $_SERVER['HTTP_USER_AGENT'];//系统
						$lottey['ip'] = $_SERVER["REMOTE_ADDR"];//IP
						$lottey['mobile'] = $mobile;//时间
						$lottey['create_time'] = time();//时间
						$this->LotteryModel->data($lottey)->add();
						$this->success('派发成功!');
						exit;
					}else{
						$this->error('已领取!');
						exit;
					}
		}else{
			$this->error('请填写手机号码!');
			exit;
		}
	
	}
	
	/*
		@author:ysh
		@function:
		@time:2014-03-24
	*/
    public function memberlist(){
		$ARRAY = array('bk'=>'比克','cd'=>'传单','ch'=>'小区','ft'=>'福特','Lot'=>'大众','pa'=>'平安礼品卡','rc'=>'东风日产','sh'=>'sh','svw'=>'上海大众','th'=>'th','wx'=>'微信','xfl'=>'雪弗莱','yh'=>'优惠','彩生活'=>'彩生活','微博'=>'微博','huangniu'=>'人人黄牛礼品');
	

        $model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');
        $model_member = D(GROUP_NAME.'/Member');
		$model_order = D(GROUP_NAME.'/Order');
	
		$order_state = C('ORDER_STATE');

        $coupon_id = isset($_GET['id'])?$_GET['id']:0;
		$mobile = $_REQUEST['mobile'];
		$post_from = $_REQUEST['from'];
		if($post_from){
			foreach($ARRAY as $k=>$v){
				if($post_from == $v){
					$map['from'] = $k;
				}
			}
			$this->assign('from',$post_from);
		}
        if ($coupon_id){
            $map['salecoupon_id'] = $coupon_id;
			if($mobile) {
				$map['mobile'] = $mobile;
			}
			if($_REQUEST['is_use'] != "" ) {
				$map['is_use'] = $_REQUEST['is_use'];
			}
			if($_REQUEST['order_id'] != "" ) {
				if($_REQUEST['order_id'] == 1) {
					$map['order_id'] = array('neq' , 0);
				}else {
					$map['order_id'] = array('eq' , 0);
				}
			}

			// 计算总数
			$count = $model_membersalecoupon->where($map)->count();
			// 导入分页类
			import("ORG.Util.Page");
			// 实例化分页类
			$p = new Page($count, 20);
			if($_REQUEST['is_use'] != "" ) {
				$p->parameter .= "is_use/" . urlencode($_REQUEST['is_use']) . "/";
			}
			if($_REQUEST['order_id'] != "" ) {
				$p->parameter .= "order_id/" . urlencode($_REQUEST['order_id']) . "/";
			}
			foreach ($_POST as $key => $val) {
				if (!is_array($val) && $val != "" ) {
					$p->parameter .= "$key/" . urlencode($val) . "/";
				}
			}
			// 分页显示输出
			$page = $p->show_admin();
            $membercoupon_info = $model_membersalecoupon->where($map)->limit($p->firstRow.','.$p->listRows)->order('membersalecoupon_id DESC')->select();
            if ($membercoupon_info){
                foreach ($membercoupon_info as $k=>$v){
					$membercoupon_info[$k]['true_order_id'] = $v['order_id'];
					if($v['order_id']) {
						$membercoupon_info[$k]['order_id'] = $this->get_orderid($v['order_id']);

						$order_info = $model_order->find($v['order_id']);
						$membercoupon_info[$k]['order_state'] = $order_state[$order_info['order_state']];
					}
					foreach($ARRAY as $ARRAY_k=>$ARRAY_v){
					
						if($v['from'] == $ARRAY_k){
							
							$membercoupon_info[$k]['from'] = $ARRAY_v; 
						}
					}
                    $memberinfo = $model_member->find($v['uid']);
                    $membercoupon_info[$k]['memberinfo'] = $memberinfo;
                }
            }
        }
		
		$this->assign('mobile',$mobile);
		$this->assign('is_use',$_REQUEST['is_use']);
		$this->assign('order_id',$_REQUEST['order_id']);
		$this->assign('page', $page);
		$this->assign('ARRAY', $ARRAY);
        $this->assign('memberlist',$membercoupon_info);
		$this->assign('coupon_id',$coupon_id);
        $this->display();
    }
	

    public function edit(){
		$model_salecoupon = D(GROUP_NAME.'/Salecoupon');
		$id = $_REQUEST['id'];
		$vo = $model_salecoupon->find($id);

		$model_shop = D(GROUP_NAME.'/Shop');
		$model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
		$model_fs = D(GROUP_NAME.'/Fs');

		$map['xc_shop.id'] = array('in',$vo['shop_ids']);
		$field = array(
			'xc_shop.id',
			'xc_shop.shop_name',
			'xc_shop_fs_relation.fsid'
		);
		//$shops = $model_shop->where($map)->order("field (id,{$vo[shop_ids]})")->select();
		$shops = $model_shop->where($map)->join("xc_shop_fs_relation ON xc_shop.id=xc_shop_fs_relation.shopid")->field($field)->order("xc_shop_fs_relation.fsid ASC")->select();
		if($shops) {
			foreach($shops as $key=>$val) {
				$fsinfo = $model_fs->find($val['fsid']);
				
				$result[$fsinfo['fsname']][] = $val;
				//$shops[$key]['fsname'] = $fsinfo['fsname'];
			}
		}

		$this->assign('result',$result);		
		$this->assign('vo', $vo);
		$this->display();
    }
    
     public function _before_insert(){
		if (isset($_POST['start_time']) and $_POST['start_time']){
            $_POST['start_time'] = strtotime($_POST['start_time']);
        }
        if (isset($_POST['end_time']) and $_POST['end_time']){
            $_POST['end_time'] = strtotime($_POST['end_time'].' 23:59:59');
        }
		if (!$_POST['shop_ids'] && $_POST['coupon_type'] == '1'){
		    $this->error("请选择店铺");
		}
		if($_POST['shop_ids']) {
			foreach($_POST['shop_ids'] as $v){
				$shop_ids.= $v.",";
			}
			$_POST['shop_ids'] = substr($shop_ids,0,-1);
		}
		if ($_FILES['coupon_pic']['name'] || $_FILES['coupon_logo']['name']){
            $this->couponupload();
        }
    }

    public function _before_update(){
		if (isset($_POST['start_time']) and $_POST['start_time']){
            $_POST['start_time'] = strtotime($_POST['start_time']);
        }
        if (isset($_POST['end_time']) and $_POST['end_time']){
            $_POST['end_time'] = strtotime($_POST['end_time'].' 23:59:59');
        }
		$_POST['update_time'] = time();
		if (!$_POST['shop_ids'] && $_POST['coupon_type'] == '1'){
		    $this->error("请选择店铺");
		}
		if($_POST['shop_ids']) {
			$_POST['shop_ids'] = array_unique($_POST['shop_ids']);
			foreach($_POST['shop_ids'] as $v){
				$shop_ids.= $v.",";
			}
			$_POST['shop_ids'] = substr($shop_ids,0,-1);
		}
		if ($_FILES['coupon_pic']['name'] || $_FILES['coupon_logo']['name']){
            $this->couponupload();
        }
    }
    
    public function del(){
        $map['id'] = $_POST['id'];
        $data['update_time'] = time();
        $data['is_delete'] = 1;
        $model_coupon = D(GROUP_NAME.'/Salecoupon');
        if($model_coupon->where($map)->save($data)){
            echo 1;
        }else {
            echo 0;
        }
        exit;
    }

	function add_membersalecoupon() {
		$coupon_id = $_REQUEST['coupon_id'];
		$mobile = $_REQUEST['mobile'];

		$model_salecoupon = D(GROUP_NAME.'/Salecoupon');
		$salecoupon = $model_salecoupon->find($coupon_id);

		$this->assign('coupon_id',$coupon_id);
		$this->assign('mobile',$mobile);
		$this->assign('salecoupon',$salecoupon);
		$this->display();
	}


	function insert_membersalecoupon() {
		$coupon_id = $_POST['coupon_id'];
		$mobile = $_POST['mobile'];
		if ($mobile and eregi("^1[0-9]{10}$",$mobile)){

			$model_salecoupon = D(GROUP_NAME.'/Salecoupon');
			$model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');

			$salecoupon = $model_salecoupon->find($coupon_id);
			$now = time();
			if($now > $salecoupon['start_time'] && $now < $salecoupon['end_time']) {
				
				$map['mobile'] = $mobile;
				$map['salecoupon_id'] = $coupon_id;
				$map['coupon_type'] = 1;
				$have_salecoupon = $model_membersalecoupon->where($map)->select();
				if(!$have_salecoupon) {
					$data['coupon_name'] = $salecoupon['coupon_name'];
					$data['salecoupon_id'] = $coupon_id;
					$data['coupon_type'] = $salecoupon['coupon_type'];
					$data['mobile'] = $mobile;
					
					$data['create_time'] = time();
					$data['start_time'] = $salecoupon['start_time'];
					$data['end_time'] = $salecoupon['end_time'];
					$data['ratio'] = $salecoupon['jiesuan_money'];
					$data['shop_ids'] = $salecoupon['shop_ids'];

					$coupon_code = $this->get_coupon_code();
					$data['coupon_code'] = $coupon_code;
					
					$model_member = D('Member');
					$model_sms = D('Sms');

					$map_m['mobile'] = $mobile;
					$member = $model_member->where($map_m)->find();
					if(!$member){
						$rand = rand_string(6,1);
						$member_data['mobile'] = $mobile;
 						$member_data['password'] = md5($rand);
						$member_data['reg_time'] = time();
						
						$member['uid'] = $model_member->add($member_data);
						$send_add_user_data = array(
							'phones'=>$mobile,
							'content'=>'亲爱的用户，您可以使用您的手机号码'.$mobile.'，密码'.$rand.'来登录携车网。携车网是一家在线预订各大汽车品牌4S店保养维修的网站，通过网站预约最低五折起，还有保养套餐和现金券团购。手机客户端已上线，手机客户端下载http://www.xieche.com.cn/download.php，客服4006602822。',
						);

					
						$this->curl_sms($send_add_user_data);
						$send_add_user_data['sendtime'] = $now;
						$model_sms->add($send_add_user_data);
						
						$model_memberlog = D(GROUP_NAME.'/Memberlog');
						$data['createtime'] = time();
						$data['mobile'] = $mobile;
						//$data['deal_uid'] = $_SESSION['authId'];
						$data['memo'] = '用户注册';
						$model_memberlog->add($data);

					}
					$data['uid'] = $member['uid'];
					
					$model_membersalecoupon->add($data);
					
					$start_time = date('Y-m-d H:i',$salecoupon['start_time']);
					$end_time = date('Y-m-d',$salecoupon['end_time']);
					
					/*上海大众4S店维修保养，携车网预约最低5折起，沪上全部4S店售后折扣比拼，团购再享折上折。携车网，唯一4S店售后折扣网站，品质养车，透明体验。电话4006602822，千元现金券抽奖点击http://www.xieche.com.cn/vw，百分百中奖
					$verify_str = "您获取的抵用券:（".$salecoupon['coupon_name']."）已送达您的账户.请通过携车网预定后凭消费码:".$coupon_code."至指定4S店于有效期内(截至".$end_time.")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服4006602822";

					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$this->curl_sms($send_verify);
					
					$send_verify['sendtime'] = $now;
					$model_sms->add($send_verify);
					*/
					$this->success('派发成功');
				}else {
					$this->error('派发失败,该手机号已经派发过现金券');
				}
			}else {
				$this->error('派发失败,该现金券已经过期');
			}


		}else {
			$this->error("请填写手机号");
		}

		
	}
	
	
	public function orders(){
        $model_membersalecoupon = D('Membersalecoupon');
        $membersalecoupon_id = $_REQUEST['membersalecoupon_id'];

        $map_mc['membersalecoupon_id'] = $membersalecoupon_id;
        $membersalecoupon = $model_membersalecoupon->where($map_mc)->find();

		$mobile = $membersalecoupon['mobile'];
        $model_order = D('Order');
        $map_o['mobile'] = $mobile;
        $map_o['order_state'] = 1;
		$orders = $model_order->where($map_o)->select();
        if($orders){
            foreach ($orders as $k=>$v){
                $map_m['order_id'] = $v['id'];
                $membersalecoupons = array();
                $membersalecoupons = $model_membersalecoupon->where($map_m)->select();
                $orders[$k]['membersalecoupons'] = $membersalecoupons;
            }
        }
        //echo '<pre>';print_r($orders);
        $this->assign('orderlist',$orders);
        $this->assign('membersalecoupon',$membersalecoupon);
        $this->display();
    }
	

	public function saveorder(){
        $order_id = isset($_REQUEST['order_id'])?$_REQUEST['order_id']:0;
        $membersalecoupon_id = isset($_REQUEST['membersalecoupon_id'])?$_REQUEST['membersalecoupon_id']:0;
        if ($membersalecoupon_id){
            $model_membersalecoupon = D('Membersalecoupon');
            $map_mc['membersalecoupon_id'] = $membersalecoupon_id;
            if ($order_id){
                $data['order_id'] = $order_id;
                if ($model_membersalecoupon->where($map_mc)->save($data)){
                    $this->success("绑定成功");
                }else{
                    $this->error("绑定失败");
                }
            }else{
				$this->error("订单生成失败");
            }
        }
    }


	/*
		@author:ysh
		@function:修改抵用券状态为使用
		@time:2013-10-29 
	*/
	function set_salecoupon_use() {
		$order_id = $_REQUEST['order_id'];
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];

		$model_membersalecoupon = D('Membersalecoupon');
		$model_order = D('Order');

		$map_mc['membersalecoupon_id'] = $membersalecoupon_id;
		$map_mc['order_id'] = $order_id;
		if ($order_id){
			$order_info = $model_order->find($order_id);
			$data['shop_id'] = $order_info['shop_id'];
			$data['is_use'] = 1;
			$data['use_time'] = time();
			$data['licenseplate'] = $order_info['licenseplate'];
			if ($model_membersalecoupon->where($map_mc)->save($data)){
				echo 1;exit();
			}else{
				echo 0;exit();
			}
		}else{
			echo 0;exit();
		}

	}
	
	/*
		@author:ysh
		@function:修改抵用券状态为使用
		@time:2013-10-30 
	*/
	function unset_salecoupon_order() {
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];

		$model_membersalecoupon = D('Membersalecoupon');

		$map_mc['membersalecoupon_id'] = $membersalecoupon_id;
		if ($membersalecoupon_id){
			$data['order_id'] = 0;
			if ($model_membersalecoupon->where($map_mc)->save($data)){
				$model_order = D("Order");
				$model_order->where($map_mc)->save(array('membersalecoupon_id'=>0));
				echo 1;exit();
			}else{
				echo 0;exit();
			}
		}else{
			echo 0;exit();
		}
	}


	/*
		@author:chf
		@function:发送优惠卷ID
		@time:2013-04-12
	*/
	function AjaxSendsms(){
		$MembercouponModel = D(GROUP_NAME.'/Membercoupon');
		$ShopModel = M('shop');
		$coupon_code = $_REQUEST['coupon_code'];
		$coupon_code = $_REQUEST['mobile'];
		$map['membercoupon_id'] = $_REQUEST['membercoupon_id'];
		$membercoupon_info = $MembercouponModel->where($map)->find();
		$Coupon = $this->CouponModel->where(array('id'=>$membercoupon_info['coupon_id']))->find();
		$shop_info = $ShopModel->where(array('id'=>$membercoupon_info['shop_id']))->find();
		if($membercoupon_info['coupon_type']==1){
			$coupon_type_str = "现金券编号：";
		}
		if($membercoupon_info['coupon_type']==2){
			$coupon_type_str = "团购券编号：";
		}
		if($membercoupon_info['start_time']){
			$start_time = date('Y-m-d h:i:s',$membercoupon_info['start_time']);
		}
		if($membercoupon_info['end_time']){
			$end_time = date('Y-m-d h:i:s',$membercoupon_info['end_time']);
		}
		$verify_str = "您的".$coupon_type_str.$membercoupon_info['membercoupon_id']."(".$membercoupon_info['coupon_name']."金额:".$Coupon['coupon_amount'].")请凭消费码至商户(".$shop_info['shop_name'].",".$shop_info['shop_address'].")处在有效期(".$start_time."至".$end_time.")优惠卷编码:".$membercoupon_info['coupon_code'];
		$send_verify = array('phones'=>$membercoupon_info['mobile'],
    	'content'=>$verify_str,
    	);
    	$return_data = $this->curl_sms($send_verify);
		echo "发送成功！";
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
		$Shop = $this->ShopModel->where($map)->select();
		if($Shop){
			echo json_encode($Shop);
		}else{
			echo 'none';
			
		}
	}
	
	//计算优惠券验证码
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

	public function _upload_init($upload) {
        //设置上传文件大小
        $upload->maxSize = C('UPLOAD_MAX_SIZE');
        //设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        //设置附件上传目录
        $upload->savePath = C('UPLOAD_ROOT') . '/Coupon/Logo/';
        $upload->thumb = true;
        $upload->saveRule = 'uniqid';
        $upload->thumbPrefix = 'coupon1_,coupon2_';//coupon1_网站图片显示；coupon2_手机APP图片显示
        //$resizeThumbSize_arr = explode(',', C('RESIZE_THUMB_SIZE'));
		$resizeThumbSize_arr = array('299,99','225,75');
        $upload->thumbMaxWidth = $resizeThumbSize_arr[0];
        $upload->thumbMaxHeight = $resizeThumbSize_arr[1];
        return $upload;
    }
    
}
?>