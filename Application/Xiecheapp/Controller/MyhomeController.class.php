<?php
//用户个人中心controller
namespace Xiecheapp\Controller;

class MyhomeController extends CommonController {
	protected $mRepireprocess;
	
	function __construct() {
		
		parent::__construct();
		if( true !== $this->login()){ 
			redirect("/Public/login?jumpUrl=".urlencode($_SERVER['REQUEST_URI']));
		}
		
        $this->TimesaleModel = D('timesale');//工时折扣表
        $this->TimesaleversionModel = D('timesaleversion');//工时折扣详情表
        $this->MemberModel = D('member');//用户表
        $this->MembercarModel = D('Membercar');//我的车辆表
        $this->MembercouponModel = D('membercoupon');//团购现金券
        $this->CouponModel = D('coupon');//优惠券
        $this->MembersalecouponModel = D('membersalecoupon');//抵用券领取表
        $this->SalecouponModel = D('salecoupon');//抵用券表
        $this->BidcouponModel = D('bidcoupon');//用户返利抵用券
        $this->ShopModel = D('shop');//店铺表
        $this->BidcommentModel = D('bidordercomment');//评论表
        $this->InsuranceModel = D('insurance');//用户保险竞价表
        $this->reservation_order_model = D('reservation_order');
        $this->car_brand_model = M('tp_xieche.car_brand','xc_');  //车品牌 老数据,不用了
        $this->car_model_model = M('tp_xieche.car_model','xc_');  //车型号
        $this->car_style_model = M('tp_xieche.car_style','xc_');  //车型号
        
        $this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌	新数据
        $this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
        
        $this->orderModel = M('tp_xieche.order','xc_');  //4s店和故障维修订单
        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter', 'xc_');  //新保养项目
        $this->technician_model = D('technician');  //技师表
        $this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表
        $this->mRepireprocess = D('repairprocess');//维修进度表
        $this->MemberaccountModel = M('tp_xieche.memberaccount','xc_');
        $this->BidorderModel = M('tp_xieche.bidorder','xc_');
        $this->CommentModel = M('tp_xieche.comment','xc_');//评论表
        $this->assign('noshow',true);
		$this->assign('noclose',true);
	}

	
	//事故车订单
	public function shiguche_order(  $isShow = true ){
		$uid = $this->GetUserId();
		$map['uid'] = $uid;
		
		$insurance_model = D("Insurance");
		$bidorder_model = D("Bidorder");
		$shopbidding_model = D("Shopbidding");
		$shop_model = D("Shop");
		$fs_model = D("fs");
		//$map['state'] = 1;
		// 计算总数
		$count = $insurance_model->where($map)->count();
		// 导入分页类
		import("Org.Util.Page");
		// 实例化分页类
		$p = new \Page($count, 10);
		// 分页显示输出
		$page = $p->show();
		// 当前页数据查询
		
		$order_status_arr = C("BIDORDER_STATE");
		
		$pay_state = C("PAY_STATE");
		
		$list = $insurance_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list) {
			foreach($list as $key=>$val) {
				$list[$key]['order_id'] = $this->get_orderid($val['id']);//给用户看的order id
				$list[$key]['order_status'] = $order_status_arr[$val['order_status']];
				$list[$key]['pay_status'] = $pay_state[$val['pay_status']];
				$bidorder_info = $bidorder_model->where(array('insurance_id'=>$val['id']))->find();
				if ($bidorder_info) {
					$list[$key]['bid_order_id'] = $this->get_orderid($bidorder_info['id']);//给用户看的order id
					$list[$key]['order_status'] = $bidorder_info['order_status'];
					$shop_info = $shop_model->find($bidorder_info['shop_id']);
					$list[$key]['shop_name'] = $shop_info['shop_name'];
				}
			}
		}
		$this->assign('shigucheList',$list);
		$this->assign('page',$page);
		$this->assign('tp','shiguche_order');
		if ($isShow) {
			$this->display('shiguche_order');
		}
	}
	
	//42店保养
	public function shopservice_order(){
		$this->repair_order(true,true);
	}
	//故障订单
	public function repair_order( $isShow = true ,$type=false){
		cookie('_currentUrl_', '/myhome/repair_order');
        $uid = $this->GetUserId();
		$map_order['uid'] = $uid;
		
        $model_order = D('Order');
		$map_order['status'] = 1;
		$onemonth_time = time()-date('t')*24*3600; 
		$order_state = isset($_REQUEST['order_state'])?$_REQUEST['order_state']:'all';
		$order_date = isset($_REQUEST['order_date'])?$_REQUEST['order_date']:1;
		
		if ($order_state!='all'){
			if($order_state == 5){
				$map_order['order_state'] = -1;
			}else{
				 $map_order['order_state'] = $order_state;
			}
		   
		}
        if ($order_date==2){
		    $map_order['create_time'] = array(array('lt',$onemonth_time));
		}else {
		    $map_order['create_time'] = array(array('gt',$onemonth_time),array('lt',time()));
		}
		$this->assign('order_state',$order_state);
		$this->assign('order_date',$order_date);
		if ($type) {
			$map_order['order_type'] = 4;//4s店订单
		}else{
			$map_order['order_type'] = 3;//故障订单
		}
        // 计算总数
        $count = $model_order->where($map_order)->count();
        // 导入分页类
        import("Org.Util.Page");
        // 实例化分页类
        $p = new \Page($count, 10);
        // 分页显示输出
        $page = $p->show();
    
        // 当前页数据查询
        $list = $model_order->where($map_order)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_order->getlastSql();
        $model_shop = D('Shop');
        
        if ($list){
            foreach ($list as $k=>$v){
				
				if($v['membersalecoupon_id']){
					$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v['membersalecoupon_id']))->find();
					
					$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
				
					$list[$k]['salecoupon_name'] = $Salecoupon['coupon_name'];
				}
				$shop_id = $v['shop_id'];
				$map_s['id'] = $shop_id;
				$shopinfo = $model_shop->where($map_s)->find();
				$list[$k]['shop_name'] = $shopinfo['shop_name'];
				
				$list[$k]['order_id'] = $this->get_orderid($v['id']);

				$map_c['shop_id'] = $shop_id;
				$map_c['order_id'] = $this->get_orderid($v['id']);
				$map_c['uid'] = $uid;

				$isComment = $this->CommentModel->where($map_c)->find();

				if(!$isComment){
					$list[$k]['isComment'] = 1;
				}
            }
        }
        $this->assign('repairList',$list);
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->get_order_number($map_order);
        if ($isShow) {
	        if ($type) {
	        	$this->assign('tp','shopservice_order');
	        	$this->display('shopservice_order');;
	        }else{
	        	$this->assign('tp','shiguche_order');
				$this->display('repair_order');
	        }
        }
	}
	
	//团购订单
	public function coupon_order( $isShow =true  ,$isTuangou = false){
		$uid = $this->GetUserId();
		
		$model_membercoupon = D('Membercoupon');
		$model_shop = D('Shop');
		$couponmap['xc_membercoupon.uid'] = $uid;
		$couponmap['xc_membercoupon.is_delete'] = 0;
		$couponmap['xc_coupon.is_delete'] = 0;
			//团购券页面用的
			//$couponmap['xc_membercoupon.coupon_type'] = 2;
			$is_use = $this->request("is_use");
			if($is_use !="") {
				$couponmap['xc_membercoupon.is_use'] = $is_use;
				$this->assign("is_use",$is_use+1);
			}
		if($isTuangou){
			$is_pay = 1;
			if($is_pay !="") {
				$couponmap['xc_membercoupon.is_pay'] = $is_pay;
				$this->assign("is_pay",$is_pay+1);
			}
		}
		// 计算总数
		$count = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->count();
		// 导入分页类
		import("Org.Util.Page");
		// 实例化分页类
		$p = new \Page($count, 10);
		// 分页显示输出
		$page = $p->show();
		$couponinfo = $model_membercoupon->where($couponmap)->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->order("xc_membercoupon.membercoupon_id DESC")->limit($p->firstRow.','.$p->listRows)->select();
		//echo $model_membercoupon->getLastSql();
		if ($couponinfo){
			foreach ($couponinfo as $k=>$v){
				$shop_id = $v['shop_id'];
				$map_s['id'] = $shop_id;
				$shopinfo = $model_shop->where($map_s)->find();
				$couponinfo[$k]['shop_name'] = $shopinfo['shop_name'];
				$couponinfo[$k]['shop_address'] = $shopinfo['shop_address'];
				$couponinfo[$k]['shop_maps'] = $shopinfo['shop_maps'];
				$couponinfo[$k]['shop_phone'] = $shopinfo['shop_phone'];
			}
		}
		$this->assign('couponinfo',$couponinfo);
		//var_dump($couponinfo);
		$this->assign('page',$page);
		if ($isShow) { //不是全部订单
			if($isTuangou){	//团购券页面用的
				/*查询优惠卷信息*/
				$Coupon_map['is_delete'] = 0;
				$Coupon_map['show_s_time'] = array('lt',time());
				$Coupon_map['show_e_time'] = array('gt',time());
				$carName = cookie('brandName');
				if ($carName) {
					$Coupon_map ['coupon_name'] = array (
							"like","%$carName%"
					);
				}
				//$Coupon_map['coupon_type'] = $coupon_type;
				$Coupon = $this->CouponModel->where($Coupon_map)->order("rand()")->limit(4)->select();
				foreach($Coupon as $C_k=>$C_v){
					$shop = $model_shop->where(array('id'=>$C_v['shop_id']))->find();
					$Coupon[$C_k]['shop_name'] = $shop['shop_name'];
					$Coupon[$C_k]['shop_address'] = $shop['shop_address'];
				}
				$this->assign('Coupon',$Coupon);
				$this->assign('tp','mycoupon');
				$this->display('mycoupon');    //团购券
			}else{
				$this->assign('tp','coupon_order');
				$this->display('coupon_order');
			}
		}
	}
	public function carservice_order( $isShow = true ){
		cookie('_currentUrl_', '/myhome/mycarservice');
		$map = array();
		//搜索
		if($_POST['id']){
			$map['id'] = $_POST['id'];
		}
		if($_POST['mobile']){
			$map['mobile'] = $_POST['mobile'];
		}
		if($_POST['licenseplate']){
			$map['licenseplate'] = $_POST['licenseplate'];
		}
		if($_POST['technician_id']){
			$map['technician_id'] = $_POST['technician_id'];
		}
		
 		$uid = $this->GetUserId();
// 		if (!$uid){
// 			$this->error('很抱歉，您无法查看该订单信息','/index.php/myhome');
// 			return false;
// 		}
		
		$map['uid'] = $uid;
		//去掉僵尸单
		$map['origin'] = array('neq','4');
		// 计算总数
		$count = $this->reservation_order_model->where($map)->count();
		
		// 导入分页类
		import("Org.Util.Page");
		// 实例化分页类
		$p = new \Page($count,40);
		// 分页显示输出
		$page = $p->show();
		
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		// 当前页数据查询
		$list = $this->reservation_order_model->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		if(is_array($list)){
			foreach ($list as $key => $value) {
				$list[$key]['id'] = $this->get_orderid($value['id']);
				$list[$key]['pay_status_name'] = $this->getPayStatusName($value['pay_status']);
		
				$list[$key]['status_name'] = $this->getStatusName($value['status']);
		
				if($value['technician_id']){
					$condition['id'] = $value['technician_id'];
					$technician_info = $this->technician_model->where($condition)->find();
					$list[$key]['technician_name'] = $technician_info['truename'];
				}
			}
		}
		$condition = array();
		$condition['status'] = 1;
		$technician_list = $this->technician_model->where($condition)->select();
		$this->assign('data', $map);
		$this->assign('carserviceList', $list);
		$this->assign('page',$page);
		$this->assign('technician_list', $technician_list);
		$this->assign('tp','carservice_order');
		if ($isShow) {
			$this->display('carservice_order');
		}
	}
	//全部订单
	public function all_order(){
		$this->carservice_order(false);
		$this->shiguche_order(false);
		$this->repair_order(false);
		$this->coupon_order(false);
		//查找所有订单总数
		$uid = $this->GetUserId();
		$order_option = array('type'=>'count','table'=>'order','map'=>array('uid'=>$uid));
		$order_count = $this->get_order_data($order_option);
		$bidorder_option = array('type'=>'count','table'=>'bidorder','map'=>array('uid'=>$uid));
		$bidorder_count = $this->get_order_data($bidorder_option);
		$membercoupon_option = array('type'=>'count','table'=>'membercoupon','map'=>array('uid'=>$uid));
		$membercoupon_count = $this->get_order_data($membercoupon_option);
		$reservation_option = array('type'=>'count','table'=>'reservation_order','map'=>array('uid'=>$uid));
		$reservation_count = $this->get_order_data($reservation_option);
		$count = $order_count+$bidorder_count+$membercoupon_count+$reservation_count;
		// 导入分页类
		import("Org.Util.Page");
		// 实例化分页类
		$p = new \Page($count,15);
		// 分页显示输出
		$page2 = $p->show();
		$this->assign('page1',$page2);
		$this->assign('tp','all_order');
		$this->display('all_order');
	}
	//我的抵用券
	public function my_salecoupon(){
		$uid = $this->GetUserId();
	
		$model_shop = D('Shop');
		$couponmap['xc_membersalecoupon.uid'] = $uid;
		$couponmap['xc_membersalecoupon.is_delete'] = 0;
		$couponmap['xc_salecoupon.is_delete'] = 0;
		$couponmap['xc_salecoupon.coupon_type'] = '1';
		$is_use = $this->request("is_use");
		if($is_use !="") {
			$couponmap['xc_membersalecoupon.is_use'] = $is_use;
			$this->assign("is_use",$is_use+1);
		}
		// 计算总数
		$count = $this->SalecouponModel->where($couponmap)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.coupon_id")->count();
		// 导入分页类
		import("Org.Util.Page");
		// 实例化分页类
		$p = new \Page($count, 10);
		// 分页显示输出
		$page = $p->show();
		$couponinfo = $this->SalecouponModel->where($couponmap)->join("xc_salecoupon ON xc_salecoupon.id=xc_membersalecoupon.salecoupon_id")->order("xc_membersalecoupon.is_use ASC,xc_membersalecoupon.end_time DESC,xc_membersalecoupon.membersalecoupon_id DESC")->limit($p->firstRow.','.$p->listRows)->select();
		/*
		 if ($couponinfo){
			foreach ($couponinfo as $k=>$v){
			if( $v['shop_ids'] ) {
			$shop_ids = $v['shop_ids'];
			$map_s['id'] = array('in',explode(',', $shop_ids));
			$shopinfo = $model_shop->field('shop_name')->where($map_s)->select();
			//var_dump($model_shop->getLastSql());exit;
			$shop_name = '';
			foreach ( $shopinfo as $name ){
			$shop_name .= $name['shop_name'].',';
			};
			var_dump($shop_name);
			$couponinfo[$k]['shop_name'] = rtrim(',',$shop_name);
			$couponinfo[$k]['shop_address'] = $shopinfo['shop_address'];
			$couponinfo[$k]['shop_maps'] = $shopinfo['shop_maps'];
			$couponinfo[$k]['shop_phone'] = $shopinfo['shop_phone'];
			}
			}
		}
		*/
		/*查询优惠卷信息*/
		
		$Coupon_map['is_delete'] = 0;
		$Coupon_map['show_s_time'] = array('lt',time());
		$Coupon_map['show_e_time'] = array('gt',time());
		$carName = cookie('brandName');
		if ($carName) {
			$Coupon_map ['coupon_name'] = array (
					"like","%$carName%"
			);
		}
		//$Coupon_map['coupon_type'] = $coupon_type;
		$Coupon = $this->CouponModel->where($Coupon_map)->order("rand()")->limit(4)->select();
		foreach($Coupon as $C_k=>$C_v){
			$shop = $model_shop->where(array('id'=>$C_v['shop_id']))->find();
			$Coupon[$C_k]['shop_name'] = $shop['shop_name'];
			$Coupon[$C_k]['shop_address'] = $shop['shop_address'];
		}
		
		$this->assign('Coupon',$Coupon);
		$this->assign('couponinfo',$couponinfo);
		$this->assign('page',$page);
		$this->assign('tp','my_salecoupon');
		$this->display('my_salecoupon');
	}
	//我的团购卷
	public function my_coupon(){
		$this->coupon_order(true,true);
	}
	//我的评价
	function my_comment() {
		$uid = $this->GetUserId();
		$map['uid'] = $uid;
		if (isset($_GET['type'])) {
			$type = $_GET['type'];
			if (in_array($type, array(1,2,3))) {
				$map['comment_type'] = $type;
				$this->assign('type',$type);
			}
		}
		//$map['state'] = 1;
		// 计算总数
		$count = $this->CommentModel->where($map)->count();
		// 导入分页类
		import("Org.Util.Page");
		// 实例化分页类
		$p = new \Page($count, 10);
		// 分页显示输出
		$page = $p->show();
		// 当前页数据查询
		$list2 = $this->CommentModel->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		if($list2) {
			foreach($list2 as $key=>$val) {
				$shop_info = $this->ShopModel->find($val['shop_id']);
				$map_c = array('comment_type' => 1,'shop_id'=>$val['shop_id']);
				$good = $this->CommentModel->where($map_c)->count();
				$commont_count = $this->CommentModel->where(array('shop_id'=>$val['shop_id']))->count();
				$list2[$key]['comment_rate'] = (int)($good/$commont_count*100);
				$list2[$key]['shop_name'] = $shop_info['shop_name'];
			}
		}
		$this->assign('tp','my_comment');
		//dump($list2);
		$this->assign('list2',$list2);
		$this->assign('page',$page);
		$this->display();
	}
	//账户设置
	public function myinfo(){
		cookie('_currentUrl_', '/index.php/myhome/myinfo');
		$uid = $this->GetUserId();
		$model_member = D('Member');
		$map_m['uid'] = $uid;
		$map_m['status'] = 1;
		$member = $model_member->where($map_m)->find();
		$this->assign('member',$member);
		$this->assign('tp','myinfo');
		$this->display();
	}
	function my_safe(){
		$this->assign('tp','my_safe');
		$this->display();
	}
	//修改用户信息
	public function update_user(){
		$Model = D('Member');
		$uid = $this->GetUserId();
		if (!$uid) {
			$this->error('请先登录','');
		}
		$userinfo = $Model->find($uid);
		$_POST['username'] = $userinfo['username'];
		if (!empty($_POST['password'])){
			if (empty($_POST['repassword'])){
				$this->error('密码必须！');
			}
			if (empty($_POST['oldpassword'])){
				$this->error('请输入原来的密码!');
			}
			if ($_POST['password'] !=$_POST['repassword']){
				$this->error('两次密码输入不一样！');
			}
			$oldpassword_md5 = pwdHash($_POST['oldpassword']);
			//$Member = M('Member');
			//$res = $Member -> where("username='$_POST[username]'")->find();
			if ($userinfo and $userinfo['password'] != $oldpassword_md5){
				$this->error('旧密码错误！');
			}else {
				$_POST['password'] = pwdHash($_POST['password']);
			}
		}else {
			unset($_POST['password']);
		}
	
		if (empty($_POST['mobile'])){
			$this->error('手机号码不能为空！');
		}
		if ($_POST['email'] and !preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $_POST['email'])){
			$this->error('邮箱格式错误！');
		}
		if ($_POST['mobile'] and !eregi("^1[0-9]{10}$",$_POST['mobile'])){
			$this->error('手机号码格式错误！');
		}
		if ($_POST['email'] and $_POST['email'] != $userinfo['email']){
			$map['email'] = $_POST['email'];
			if ($Model->where($map)->find()){
				$this->error('邮箱号码已存在，请重新填写！');
			}
			unset($map);
		}
	
		if ($_POST['mobile']  and $_POST['mobile'] != $userinfo['mobile']){
			$map['mobile'] = $_POST['mobile'];
			if ($Model->where($map)->find()){
				$this->error('手机号码已存在，请重新填写！');
			}
			unset($map);
		}
		if (false !== $Model->where(array('uid'=>$uid))->save($_POST)) {
			//tipask库user修改密码
			$model_user = D('User');
			$tipask_data['uid'] = $_POST['uid'];
			$tipask_data['password'] = $_POST['password'];
			$tipask_data['email'] = $_POST['email'];
			$tipask_data['phone'] = $_POST['mobile'];
			$model_user->save($tipask_data);
	
			$this->assign('jumpUrl', cookie('_currentUrl_'));
			$this->success('修改成功!');
		}else {
			$this->error('修改失败!');
		}
	}
	//上门保养订单详情
	function carservice_detail(){
		$order_id = $this->get_true_orderid(I('get.order_id'));
		if (!$order_id) {
			$this->error('订单号不能为空','/myhome');
		}
			
		$condition['id'] = $order_id;
		
		$order_info = $this->reservation_order_model->where($condition)->find();
        
        //判断需要实例化哪张数据表 wql@20150821
        $this->getNeedModel($order_info['create_time']);
			
		$uid = $this->GetUserId();
		if($order_info['uid'] != $uid){
			$this->error('很抱歉，您无法查看该订单信息','/myhome');
		}
			
		
		//车型
		$style_param['model_id'] = $order_info['model_id'];
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
		}
		$order_info['car_name'] = $car_name;
		
		//商品详情
		$item_num = 1;
		$order_items = unserialize($order_info['item']);
		
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
			$item_list['0']['id'] = $order_items['oil_id'];
			$item_list['0']['name'] = $name;
			$item_list['0']['price'] = $item_oil_price;
			if (!$oil_param['id']){
				unset($item_list['0']);
			}
			if( $order_items['oil_id'] == '-1' ){
				$item_list['0']['id'] = 0;
				$item_list['0']['name'] = "自备配件";
				$item_list['0']['price'] = 0;
			}
			$item_num++;
		}
		if($order_items['filter_id']){
			if($order_items['filter_id'] == '-1'){	//自备配件的情况
				$item_list['1']['id'] = 0;
				$item_list['1']['name'] = "自备配件";
				$item_list['1']['price'] = 0;
			}else{
				$item_condition['id'] = $order_items['filter_id'];
				$name1 = $this->item_model->where($item_condition)->find();
				if(mb_strpos($name1['name'],'马勒')===0){
					$item_list[1]['name'] = "马勒";
				}elseif(mb_strpos($name1['name'],'曼牌')===0){
					$item_list[1]['name'] = "曼牌";
				} elseif(mb_strpos($name1['name'],'博世')===0){
					$item_list[1]['name'] = "博世";
				}else {
					$item_list[1]['name'] = $name1['name'];
				}
				$item_list[1]['price'] = $name1['price'];
			}
			$item_num++;
		}
		if($order_items['kongqi_id']){
			if( $order_items['kongqi_id'] == '-1' ){
				$item_list['2']['id'] = 0;
				$item_list['2']['name'] = "自备配件";
				$item_list['2']['price'] = 0;
			}else{
				$item_condition['id'] = $order_items['kongqi_id'];
				$name2 = $this->item_model->where($item_condition)->find();
				if(mb_strpos($name2['name'],'马勒')===0){
					$item_list[2]['name'] = "马勒";
				}elseif(mb_strpos($name2['name'],'曼牌')===0){
					$item_list[2]['name'] = "曼牌";
				} elseif(mb_strpos($name2['name'],'博世')===0){
					$item_list[2]['name'] = "博世";
				}else {
					$item_list[2]['name'] = $name2['name'];
				}
				$item_list[2]['price'] = $name2['price'];
			}
			$item_num++;
		}
		if($order_items['kongtiao_id']){
			if( $order_items['kongtiao_id'] == '-1' ){
				$item_list['3']['id'] = 0;
				$item_list['3']['name'] = "自备配件";
				$item_list['3']['price'] = 0;
			}else{
				$item_condition['id'] = $order_items['kongtiao_id'];
				$name3 = $this->item_model->where($item_condition)->find();
				if(mb_strpos($name3['name'],'马勒')===0){
					$item_list[3]['name'] = "马勒";
				}elseif(mb_strpos($name3['name'],'曼牌')===0){
					$item_list[3]['name'] = "曼牌";
				} elseif(mb_strpos($name3['name'],'博世')===0){
					$item_list[3]['name'] = "博世";
				}else {
					$item_list[3]['name'] = $name3['name'];
				}
				$item_list[3]['price'] = $name3['price'];
			}
			$item_num++;
		}
		
		$item_amount = 99;
		if ( $order_info['replace_code'] ) {
			$value = $this->get_codevalue($order_info['replace_code']);//判断抵用卷的价钱
			if ($value) {
				$this->assign('replace_value',$value);
			}
			if($value != 99){
				$item_amount = $item_amount - $value;	//加服务费，减抵用券费用
			}else{
				$item_amount = 0;
			}
		}
		
		if($order_info['order_type']==2){
			$item_amount = 0;
		}
		
		if(is_array($item_list)){
			foreach ($item_list as $key => $value) {
				$item_amount += $value['price'];
			}
		}
		if($order_info['technician_id']){
			 $technician = $this->technician_model->where(array('id'=>$order_info['technician_id']))->find();
		}
		
		$order_info['true_id'] = $order_info['id'];
		$order_info['id'] = $this->get_orderid($order_info['id']);
		//var_dump($item_list); exit;;
		$this->assign('item_num',$item_num);
		$this->assign('replace_code', $order_info['replace_code']);
		$this->assign("item_list", $item_list);
		$this->assign("item_amount", $item_amount);
		$this->assign("technician",$technician);
		$this->assign("order_info", $order_info);
        $this->display("carservice_detail");
    }
    //事故车详情
    public function shiguche_detail(){
    	//1.按照竞价id查询
    	$insurance_order_id = I('insurance_order_id');
    	
    	//2.按照bid_orderid查询
    	$order_id = I('get.order_id');
    		$uid = $this->GetUserId();
    	if ($insurance_order_id) {

    		$condition['id'] = $insurance_order_id;
    		$condition['uid'] = $uid;
    		$list = $this->InsuranceModel->where($condition)->find();

    		if($list['uid'] != $uid){
    			$this->error('很抱歉，您无法查看该订单信息','/myhome');
    		}
    		if ($list) {
    			$list['mobile'] = $list['user_phone'];
    		}
    		$this->assign('list',$list);
    		
    	}elseif ($order_id){
    		
    		$condition['xc_bidorder.id'] = $order_id;
			$condition['xc_bidorder.uid'] = $uid;
    		$list = $this->BidorderModel->where($condition)->join("xc_insurance ON xc_insurance.id=xc_bidorder.insurance_id")->find();
    		//var_dump($this->BidorderModel->getLastSql());
    		//var_dump($list);
    		$uid = $this->GetUserId();
    		if($list['uid'] != $uid){
    			$this->error('很抱歉，您无法查看该订单信息','/myhome');
    		}
    		$this->assign('list',$list);
    		
    	}else{
    		$this->error('订单id为空','/myhome');
    	}
       
        $this->display('shiguche_detail');
    }
    //维修详情
    public function repair_detail(){
    	$order_id = $this->get_true_orderid(I('get.order_id'));
    	
    	if (!$order_id) {
    		$this->error('订单id为空','/myhome');
    	}
    	$condition['id'] = $order_id;
    	$list = $this->orderModel->where($condition)->find();
    	
    	$shop_id = $list['shop_id'];
    	$shop = $this->ShopModel->field('shop_name,shop_address')->where( array('id'=>$shop_id) )->find();
    	if ($shop) {
    		$list = array_merge($list,$shop);
    	}
    	$uid = $this->GetUserId();
    	if($list['uid'] != $uid){
    		$this->error('很抱歉，您无法查看该订单信息','/myhome');
    	}
    	$this->assign('order_id',I('get.order_id'));
    	$this->assign('list',$list);
    	$this->display('repair_detail');
    }
    //团购券详情
    public function coupon_detail(){
    	$coupon_id = I('get.coupon_id');
    	if (!$coupon_id) {
    		$this->error('订单id为空','/myhome');
    	}
    	$condition['membercoupon_id'] = $coupon_id;
    	$list = $this->MembercouponModel->where($condition)->find();
    	if ($list) {
    		$uid = $this->GetUserId();
    		if($list['uid'] != $uid){
    			//$this->error('很抱歉，您无法查看该订单信息','/myhome');
    		}
    		$shop_id = $list['shop_ids'];
    		$shop = $this->ShopModel->field('shop_name,shop_address')->where( array('id'=>$shop_id) )->find();
    		//var_dump($shop);
    		$user = $this->MemberModel->field('username')->where( array('uid'=>$list['uid']) )->find();
    		if ($user) {
	    		$list = array_merge($list, $user);
    		}
    		
    		if ($shop) {
    			$list = array_merge($list, $shop);
    		}
    		
    	}
    	$this->assign('list',$list);
    	//var_dump($list);
    	$this->display('coupon_detail');
    }
    
    //评论
    public function comment(){
    	$uid = $this->GetUserId();
    	$type = @$_POST['type'];
    	if (!$type) {
    		$this->error('网络出错');
    	}
    	if ($type == 1) {
    		//4s店预约保养和维修评论;
    		$shopId = @$_POST['shop_id'];
    		$star = @$_POST['star'];
			switch($star){
				case 1: $comment_type=3;
					break;
				case 2: $comment_type=3;
					break;
				case 3: $comment_type=2;
					break;
				case 4: $comment_type=1;
					break;
				case 5: $comment_type=1;
					break;
				default:$comment_type=1;
			}
    		$orderId = @$_POST['order_id'];
    		$content = @$_POST['content'];
    		if ( !$shopId || !$orderId || !$uid) {
    			$this->error('网络出错');
    		}
    		$count = $this->CommentModel->where( array('uid'=>$uid,'order_id'=>$orderId) ) -> count();
    		if ($count) {
    			$this->error('您已经评论过该订单，不能再次评论',null,true);
    		}
    		$username = $_SESSION['username'];
    		$data = array(
    				'shop_id' => $shopId,
    				'uid'	=>$uid,
    				'order_id'=>$orderId,
    				'user_name' =>$username,
    				'comment' =>$content,
    				'comment_type' => $comment_type,
					'star'=>$star,
    				'create_time'=>time(),
					'update_time'=>time()

    		);
    		$res = $this->CommentModel->add($data);
    		if($res){
    			$this->success('评论成功，感谢您的宝贵意见',null,true);
    		}else{
    			$this->error('评论失败',null,true);
    		}
    	}else{
    		//事故车订单评论
    		$orderId = $_POST['order_id'];
    		$star = @$_POST['star'];
			switch($star){
				case 1: $comment_type=3;
					break;
				case 2: $comment_type=3;
					break;
				case 3: $comment_type=2;
					break;
				case 4: $comment_type=1;
					break;
				case 5: $comment_type=1;
					break;
				default:$comment_type=1;
			}

    		$content = @$_POST['content'];
    	
    		$data = array(
    				'operator_id'	=>$uid,
    				'bidorder_id'=>$orderId,
					'parent_id'=>0,
    				'content' =>$content,
    				'level' =>$comment_type,
    				'create_time'=>time()
    		);
    		$count = $this->BidcommentModel->where( array('bidorder_id'=>$orderId) ) -> count();
    		if ($count) {
    			$this->error('您已经评论过该订单，不能再次评论',null,true);
    		}
    		
    		$res = $this->BidcommentModel->add($data);
    		if($res){
    			$this->success('评论成功，感谢您的宝贵意见',null,true);
    		}else{
    			$this->error('评论失败',null,true);
    		}
    	}
    }
	//修改评论2
	public function comment2()
	{
		$uid = $this->GetUserId();
		$type = @$_POST['type2'];
		if (!$type) {
			$this->error('网络出错');
		}
		if ($type == 1) {
			//4s店预约保养和维修评论;
			$comment_id = @$_POST['comment_id'];
			$star = @$_POST['star2'];
			switch($star){
				case 1: $comment_type=3;
					break;
				case 2: $comment_type=3;
					break;
				case 3: $comment_type=2;
					break;
				case 4: $comment_type=1;
					break;
				case 5: $comment_type=1;
					break;
				default:$comment_type=1;
			}

			$content = @$_POST['content'];
			if ( !$comment_id ) {
				$this->error('网络出错');
			}
			$count = $this->CommentModel->where(array('id' => $comment_id))->count();
			if ($count) {
				$username = $_SESSION['username'];
				$data = array(
					'comment_type'=> $comment_type,
					'comment' => $content,
					'star'=>$star,
					'update_time' => time()
				);
				$res = $this->CommentModel->where(array('id' => $comment_id))->save($data);
				if ($res) {
					$this->success('修改成功，感谢您的宝贵意见', null, true);
				} else{
					$this->error('修改失败', null, true);
				}
			}
		}else {
			$this->error('网络出错');
		}
	}
    public function delComment(){
    	try {
    		$id = @$_POST['id'];
    		if (!$id) {
    			throw new \Exception('参数错误');
    		}
    		$uid = $this->GetUserId();
    		if (!$uid) {
    			throw new \Exception('请先登录');;
    		}
    		$count = $this->CommentModel->where( array(
    				'id' => $id,
    				'uid'=> $uid
    		))->count();
    		if (!$count) {
    			throw new \Exception('没有该订单');
    		}
    		$del = $this->CommentModel->where( array(
    				'id' => $id
    		))->delete();
    		if($del){
    			$ret = array('status'=>1);
    		}else{
    			throw new \Exception('删除失败');
    		}
    	}catch (\Exception $e){
    		$msg = $e->getMessage();
    		$ret = array('status'=>0,'msg'=>$msg);
    	}
   		echo json_encode($ret);
    }
    //删除团购卷
    public function delCoupon(){
    	try {
    		$id = @$_POST['id'];
    		if (!$id) {
    			throw new \Exception('参数错误');
    		}
    		$uid = $this->GetUserId();
    		if (!$uid) {
    			throw new \Exception('请先登录');;
    		}
    		$count = $this->MembercouponModel->where( array(
    				'membercoupon_id' => $id,
    				'uid'=> $uid
    		))->count();
    		if (!$count) {
    			throw new \Exception('没有该订单');
    		}
    		$del = $this->MembercouponModel->where( array(
    				'membercoupon_id' => $id
    		))->delete();
    		if($del){
    			$ret = array('status'=>1);
    		}else{
    			throw new \Exception('删除失败');
    		}
    	}catch (\Exception $e){
    		$msg = $e->getMessage();
    		$ret = array('status'=>0,'msg'=>$msg);
    	}
    	echo json_encode($ret);
    }
    //删除抵用卷
    public function delSalecoupon(){
    	try {
    		$id = @$_POST['id'];
    		if (!$id) {
    			throw new \Exception('参数错误');
    		}
    		$uid = $this->GetUserId();
    		if (!$uid) {
    			throw new \Exception('请先登录');;
    		}
    		$count = $this->MembersalecouponModel->where( array(
    				'membersalecoupon_id' => $id,
    				'uid'=> $uid
    		))->count();
    		if (!$count) {
    			throw new \Exception('没有该订单');
    		}
    		$del = $this->MembersalecouponModel->where( array(
    				'membersalecoupon_id' => $id
    		))->delete();
    		if($del){
    			$ret = array('status'=>1);
    		}else{
    			throw new \Exception('删除失败');
    		}
    	}catch (\Exception $e){
    		$msg = $e->getMessage();
    		$ret = array('status'=>0,'msg'=>$msg);
    	}
    	echo json_encode($ret);
    }
	/*
	 @author:liuhui
	@function:维修进度
	@time:2014-8-21
	*/
	public function bidorder_progress(){
		$f_order_id = @$_GET['bidorder_id'];
		if (!$f_order_id) {
			$this->error('参数为空','/index.php/myhome');
		}
		$order_id = $this->get_true_orderid($f_order_id);
		$uid = $this->GetUserId();
		$where = array(
				'order_id'=>$order_id,
				'uid'=>$uid
		);
	
		$data = $this->mRepireprocess->where($where)->order('up_time desc')->select();
		$des = '';
		if ( !empty($data) ) {
			foreach ( $data as &$val){
				if ( $val['describe'] ) {
					$val['describe'] = unserialize($val['describe']);
					$val['count'] = count($val['describe']);
					$val['des_t'] = $val['describe'][0]['des'];
					unset($val);
				}
			}
		}
		$mBidorder = D("Bidorder");
		$where2 = array(
				'id'=>$order_id,
				'uid'=>$uid
		);
		$complete_status = $mBidorder->field('order_status,complete_time')->where($where2)->find();
		$order_status = ( @$complete_status['order_status'] == 4 ) ? 1 : 0;	//是否已经提车
		$this->assign('data',$data);
		$this->assign('order_status',$order_status);
		$this->assign('complete_time',@$complete_status['complete_time']);
		$this->display();
	}

    public function index(){ 
        $model_order = D('Order');
        $uid = $this->GetUserId();
		$map_order['status'] = 1;
		$map_order['uid'] = $uid;
		//个人资料
		$member = $this->MemberModel->where(array('uid'=>$uid))->find();
		//print_r($member);
		//个人账户
		$account = $this->MemberaccountModel->where(array('uid'=>$uid))->find();
		//print_r($account);

		//待付款订单，待评价订单
		$order_option = array('type'=>'count','table'=>'order','map'=>array('pay_status'=>'0','uid'=>$uid));
		$order_count = $this->get_order_data($order_option);
		$bidorder_option = array('type'=>'count','table'=>'bidorder','map'=>array('pay_status'=>'0','uid'=>$uid));
		$bidorder_count = $this->get_order_data($bidorder_option);
 		$membercoupon_option = array('type'=>'count','table'=>'membercoupon','map'=>array('is_pay'=>'0','uid'=>$uid));
		$membercoupon_count = $this->get_order_data($membercoupon_option);
		$reservation_option = array('type'=>'count','table'=>'reservation_order','map'=>array('pay_status'=>'0','uid'=>$uid));
		$reservation_count = $this->get_order_data($reservation_option);
		$pay_count = $order_count+$bidorder_count+$membercoupon_count+$reservation_count;
		
		//待评价订单
		$order_option = array('type'=>'count','table'=>'order','map'=>array('iscomment'=>'0','uid'=>$uid));
		$ordercomment_count = $this->get_order_data($order_option);
		$comment_count = $ordercomment_count; 

		//最近订单
		$order_info = $model_order->where(array('uid'=>$uid))->order('create_time desc')->limit(2)->select();
		foreach($order_info as $k=>$v){
			$order_shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$order_info[$k]['shop_name'] = $order_shop['shop_name'];
			$order_info[$k]['order_id'] = $v['id'];
			$order_info[$k]['id'] = $this->get_orderid($v['id']);
		}

		$membercoupon_info = $this->MembercouponModel->where(array('uid'=>$uid))->order('create_time desc')->limit(2)->select();
		foreach($membercoupon_info as $kk=>$vv){
			$membercoupon_shop = $this->ShopModel->where(array('id'=>$vv['shop_id']))->find();
			$membercoupon_info[$kk]['shop_name'] = $membercoupon_shop['shop_name'];
		}

		$bidorder_info = $this->InsuranceModel->where(array('uid'=>$uid))->order('create_time desc')->limit(2)->select();
		foreach($bidorder_info as $kkk=>$vvv){
			$bidorder_shop = $this->ShopModel->where(array('id'=>$vvv['shop_id']))->find();
			$bidorder_info[$kkk]['shop_name'] = $bidorder_shop['shop_name'];
		}

		$map['uid'] = $uid;
		//去掉僵尸单
		$map['origin'] = array('neq','4');
		$carservice_info = $this->reservation_order_model->where($map)->order('create_time desc')->limit(2)->select();

		foreach($carservice_info as $_k=>$_v){
			$carservice_shop = $this->ShopModel->where(array('id'=>$_v['shop_id']))->find();
			$carservice_info[$_k]['shop_name'] = $carservice_shop['shop_name'];
			$carservice_info[$_k]['order_id'] = $_v['id'];
			$carservice_info[$_k]['id'] = $this->get_orderid($_v['id']);
		}

		//评论
		$order_comment = $this->CommentModel->where(array('uid'=>$uid))->limit(2)->order('id desc')->select();
		foreach ($order_comment as &$v){
			if ($v['shop_id']) {
				$map_c = array('comment_type' => 1,'shop_id'=>$v['shop_id']);
				$order = $this->orderModel->where(array('id'=>$v['order_id']))->find();
				$v['order_type']= $order['order_type'];
				$good = $this->CommentModel->where($map_c)->count();
				$commont_count = $this->CommentModel->where(array('shop_id'=>$v['shop_id']))->count();
				$v['comment_rate'] = (int)($good/$commont_count*100);
				$carservice_shop = $this->ShopModel->field('shop_name')->where(array('id' => $v['shop_id']))->find();
				$v['shop_name'] = $carservice_shop['shop_name'];
			}
			unset($v);
		}
		//echo $this->CommentModel->getLastSql();
		//print_r($order_comment);

		//onemonth_time = time()-date('t')*24*3600;
		//$map_order['create_time'] = array(array('lt',$onemonth_time));
		$map_order['iscomment'] = 0;
		$map_order['order_state'] = 2;
		$map_order['order_type'] = 0;
		$map_order['uid'] = $uid;
        // 计算总数
        $count = $model_order->where($map_order)->count();
        // 导入分页类
        import("Org.Util.Page");
        // 实例化分页类
        $p = new \Page($count, 5);
        // 分页显示输出
        $page = $p->show();
        // 当前页数据查询
        $list = $model_order->where($map_order)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if ($list){
            foreach ($list as $k=>$v){
                $list[$k]['order_id'] = $this->get_orderid($v['id']);
            }
        }
		//print_r($list);
		/*查询最近1个月的订单*/
		//$month_order['create_time'] = array(array('gt',$onemonth_time),array('lt',time()));
		/*未消费订单*/
		$consume['order_state'] = 1;
		$consume['uid'] = $uid;
		$momth_list = $model_order->where($consume)->order('id DESC')->select();
		if ($momth_list){
            foreach ($momth_list as $k=>$v){
                $momth_list[$k]['order_id'] = $this->get_orderid($v['id']);
            }
        }

		//推荐信息
		

		/*店铺信息*/
		$model_shop = D('Shop');
		if($this->city_name == "上海" ) {
			$map_sh['status'] = 1;
			$map_sh['shop_class'] = 1;
		}
		$map_sh['shop_city'] = $this->city_id;
		$shoplist = $model_shop->where($map_sh)->order("comment_rate DESC")->limit(3)->select();
		$shop_count = $model_shop->where($map_sh)->count();
		$this->assign('shop_count',$shop_count);
		if ($shoplist){
			$model_shop_fs_relation = D('Shop_fs_relation');

			foreach ($shoplist as $kk=>$vv){
				if (file_exists("UPLOADS/Shop/130/".$vv['id'].".jpg")){
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Shop/130/".$vv['id'].".jpg";
				}else {
					$shop_id = $vv['id'];
					$map_sfr['shopid'] = $shop_id;
					$shop_fs_relation = $model_shop_fs_relation->where($map_sfr)->find();
					$shoplist[$kk]['shop_pic'] = "/UPLOADS/Brand/130/".$shop_fs_relation['fsid'].".jpg";
				}
				$Timesale = $this->TimesaleModel->where(array('shop_id'=>$vv['id'],'status'=>1))->find();
				$Timesaleversion = $this->TimesaleversionModel->where(array('timesale_id'=>$Timesale['id'],'status'=>1))->find();
				$shoplist[$kk]['workhours_sale'] = $Timesaleversion['workhours_sale']*10;
			}
		}
		$this->assign('shoplist',$shoplist);
		
		/*查询优惠卷信息*/
		//跨品牌
		$Coupon_map['is_delete'] = 0;
		$Coupon_map['show_s_time'] = array('lt',time());
		$Coupon_map['show_e_time'] = array('gt',time());
		$Coupon_map['coupon_type'] = 2;
		$Coupon2 = $this->CouponModel->where($Coupon_map)->order("rand()")->limit(4)->select();
		$this->assign('Coupon2',$Coupon2);
		
		//本品牌,根据用户车型查卷
		$model_ids = $this->MembercarModel->where(array('uid'=>$uid))->select();
		if ($model_ids) {
			//$Coupon_map['model_id'] = array('in',$model_ids);
			$carName = cookie('brandName');
			if ($carName) {
				$Coupon_map ['coupon_name'] = array (
						"like","%$carName%"
			);
			}
			$Coupon3 = $this->CouponModel->where($Coupon_map)->order("id DESC")->limit(4)->select();
			$this->assign('Coupon3',$Coupon3);
			
			//现金卷
			$Coupon_map['coupon_type'] = 1;
			$Coupon = $this->CouponModel->where($Coupon_map)->order("id DESC")->limit(4)->select();
			$this->assign('Coupon',$Coupon);
		}
		
		
		/*查询优惠卷 团购券 抵用券数量 */
		$data['cash_coupon'] = $this->MembercouponModel->where(array('xc_membercoupon.uid'=>$uid,'xc_membercoupon.coupon_type'=>1,'xc_membercoupon.is_delete'=>0,'xc_coupon.is_delete'=>0))->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->count();//现金券数量
		$data['group_coupon'] = $this->MembercouponModel->where(array('xc_membercoupon.uid'=>$uid,'xc_membercoupon.coupon_type'=>2,'xc_membercoupon.is_delete'=>0,'xc_coupon.is_delete'=>0))->join("xc_coupon ON xc_coupon.id=xc_membercoupon.coupon_id")->count();//团购券数量
		$data['sale_coupon'] = $this->MembersalecouponModel->where(array('uid'=>$uid,'is_delete'=>0))->count();//抵用券数量
		$this->assign('list',$list);//1个月前的订单LIST
		$this->assign('momth_list',$momth_list);//1个月的订单LIST
        $this->assign('page',$page);
        $this->assign('count',$count);
		$this->assign('data',$data);
        $this->get_order_number($map_order);
		$this->assign('member',$member);
		$this->assign('account',$account);
		$this->assign('pay_count',$pay_count);
		$this->assign('comment_count',$comment_count);
		$this->assign('order',$order_info);
		$this->assign('membercoupon',$membercoupon_info);
		$this->assign('bidorder',$bidorder_info);
		$this->assign('carservice',$carservice_info);
		$this->assign('order_comment',$order_comment);
		$this->assign('tp','index');
		$this->assign('title','个人中心');
        $this->display('index');
    }

	

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

            case '3':
                return "服务已完成";
                break;

            default:
                return "等待处理";
                break;
        }
    }

    
    public function get_order_number($map_order){
        $model_order = D('Order');
        //未完成的订单数
        $map_order['order_state'] = array('lt',2);
        $count1 = $model_order->where($map_order)->count();
        //已完成的订单数
        $map_order['order_state'] = 2;
        $count2 = $model_order->where($map_order)->count();
        //已取消订单数
        $map_order['order_state'] = -1;
        $count3 = $model_order->where($map_order)->count();
        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('count3',$count3);
    }

	//获取订单数据公共方法
	function get_order_data($option){
		$model = D($option['table']);
		foreach($option['map'] as $k=>$v){
			$map[$k] = $v;
		}
		if($option['type']=='select'){
			$info = $model->where($map)->select();
		}
		if($option['type']=='find'){
			$info = $model->where($map)->find();
		}
		if($option['type']=='count'){
			$info = $model->where($map)->count();
		}
		return $info;
	}
    
    
    //判断订单时间是否大于新配件表上线时间，如果小于等于，实例化老配件表.否则实例化新配件表。wql@20150821
    public function getNeedModel($orderCreateTime) {
        $new_filter_time = strtotime(C('NEW_FILTER_TIME')) ;
        if($orderCreateTime <= $new_filter_time){
            $this->carbrand_model = D('carbrand');  //车品牌
            $this->carmodel_model = D('carmodel');  //车型号
            $this->carseries_model = D('carseries');  //车系
            $this->item_oil_model = D('item_oil');  //保养机油
            $this->item_model = D('item_filter');  //保养项目
        }else{
            $this->carbrand_model = D('new_carbrand');  //车品牌
            $this->carmodel_model = D('new_carmodel');  //车型号
            $this->carseries_model = D('new_carseries');  //车系
            $this->item_oil_model = D('new_item_oil');  //保养机油
            $this->item_model = D('new_item_filter');  //保养项目
        }
        
    }

	 
	
}


