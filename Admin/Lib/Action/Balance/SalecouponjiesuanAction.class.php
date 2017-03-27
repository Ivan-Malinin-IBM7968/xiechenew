<?php
//订单
class SalecouponjiesuanAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->ShopModel = D('shop');//商铺表
		$this->MembercouponModel = D('membercoupon');//优惠卷表
		$this->MemberModel = D('member');//用户表
		$this->SalecouponModel = D('salecoupon');//派送优惠券信息表
		$this->MembersalecouponModel = D('membersalecoupon');//派送优惠券订单表
	    $this->MsalecouponjiesuanModel = D('membersalecouponjiesuan');//派送优惠券订单表
		$this->salecouponjiesuanlogModel = D('salecouponjiesuanlog');//派送优惠券订单表
		$this->couponjiesuanModel = D('couponjiesuan');//结算表
		$this->couponjiesuanlogModel = D('couponjiesuanlog');//结算表日志表
		$this->orderModel = D('order');//结算表日志表
	}
	
	/*
		@author:chf
		@function:显示抵用券结算系统首页
		@time:2013-8-20
	*/
	function index(){
		if($_REQUEST['shopname']){
			$data['shopname'] = $_REQUEST['shopname'];
		}
		if($_REQUEST['shop_id']){
			$data['shop_id'] = $map['shop_id'] = $_REQUEST['shop_id'];
		}else{
			$map['shop_id'] = array('neq','');
		}
		
		$count = $this->MembersalecouponModel->where($map)->group('shop_id')->count();
		
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,300);
		// 分页显示输出

		foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }

		$page = $p->show_admin();
		$data['Membersalecoupon'] = $this->MembersalecouponModel->where($map)->group('shop_id')->limit($p->firstRow.','.$p->listRows)->select();
	
		foreach($data['Membersalecoupon'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['Membersalecoupon'][$k]['count'] = $this->MembersalecouponModel->where(array('is_jiesuan'=>0,'is_use'=>1,'is_delete'=>0,'shop_id'=>$v['shop_id']))->count();
			$data['Membersalecoupon'][$k]['isjiesuan_count'] = $this->MsalecouponjiesuanModel->where(array('jiesuan_status'=>2,'shop_id'=>$v['shop_id']))->count();
			$data['Membersalecoupon'][$k]['shop_name'] = $shop['shop_name'];
			$lastcoupon = $this->MembersalecouponModel->where(array('is_jiesuan'=>1,'is_delete'=>0,'shop_id'=>$v['shop_id']))->order('jiesuan_time DESC')->find();

			$data['Membersalecoupon'][$k]['open_count'] = $this->MsalecouponjiesuanModel->where(array('jiesuan_status'=>3,'open'=>0,'shop_id'=>$v['shop_id']))->count();

			$data['Membersalecoupon'][$k]['jiesuan_time'] = $lastcoupon['jiesuan_time'];
		}
		$data['ShopList'] = $this->ShopModel->select();
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:显示抵用券对应店铺的结算账单
		@time:2013-8-20
	*/
	function shopcoupon(){
		$map['is_delete'] = 0;
		$map['is_jiesuan'] = 0;
		$map['is_use'] = 1;
		$map['shop_id'] = $_REQUEST['shop_id'];
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		if($_REQUEST['start_usetime'] && $_REQUEST['end_usetime']){
			$map['use_time'] = array(array('lt',strtotime($_REQUEST['end_usetime'])),array('gt',strtotime($_REQUEST['start_usetime'])),'AND');
			$data['start_usetime'] = $_REQUEST['start_usetime'];
			$data['end_usetime'] = $_REQUEST['end_usetime'];
		}
		/*改店铺前50张券免单*/
		$EditSale = $this->MembersalecouponModel->where($map)->limit('0,50')->select();
		foreach($EditSale as $k=>$v){
			$this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v['membersalecoupon_id']))->data(array('ratio'=>0))->save();
		}
		/*改店铺前50张券免单*/
		$count = $this->MembersalecouponModel->where($map)->count();
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		$list = $this->MembersalecouponModel->order('membersalecoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		echo $this->MembersalecouponModel->getLastsql();
		if($list){
			foreach($list as $k=>$v){
				$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$Salecoupon = $this->SalecouponModel->where(array('id'=>$v['salecoupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $Salecoupon['coupon_amount'];
				$list[$k]['cost_price'] = $v['ratio'];
				$list[$k]['commission'] = $v['ratio'];//单个佣金
				$allcommission+=$v['ratio'];//单个佣金
				if($v['ratio'] == '0'){
					$allcoupon_amount+=50;//应支付
				}else{
					$allcoupon_amount+=$v['ratio'];//应支付
				}
				if($v['ratio'] == '0'){
					$openallcommission+=50;//应支付
				}else{
					$openallcommission+=$v['ratio'];//应支付
				}
			}
			$allpay = $allcommission;//总支付金额
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('openallcommission',$openallcommission);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}
	
	/*
		@author:chf
		@function:显示抵用券对应店铺的结算账单
		@time:2013-8-20
	*/
	function confirmshopcoupon(){
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];//
		
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		foreach($membersalecoupon_id as $k=>$v){
			
			$membersalecoupon_ids.=$v.",";
		}
		
		$membersalecoupon_ids = substr($membersalecoupon_ids,0,-1);
		
		$map['membersalecoupon_id'] = array('in',$membersalecoupon_ids);
	    $count = $this->MembersalecouponModel->where($map)->count();
	
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->MembersalecouponModel->order('membersalecoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$Salecoupon = $this->SalecouponModel->where(array('id'=>$v['salecoupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $Salecoupon['coupon_amount'];
				$list[$k]['cost_price'] = $v['ratio'];
				$list[$k]['commission'] = $v['ratio'];//单个佣金
				$allcommission+=$v['ratio'];//单个佣金
				
				if($v['ratio'] == '0'){
					$allcoupon_amount+=50;//总结算金额
				}else{
					$allcoupon_amount+=$v['ratio'];//总结算金额
				}
				if($v['ratio'] == '0'){
					$openallcommission+=50;//应支付
				}else{
					$openallcommission+=$v['ratio'];//应支付
				}
			}
			$allpay = $allcommission;//总支付金额
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('openallcommission',$openallcommission);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}


	/*
		@author:chf
		@function:向结算订单插入申请结算信息
		@time:2013-8-20
	*/
	function shopapply(){
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];
		
		foreach($membersalecoupon_id as $k=>$v){
			$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v))->find();
			$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
			
			$data['membersalecoupon_id'] = $v;//用户优惠卷ID
			$data['salecoupon_id'] = $Membersalecoupon['salecoupon_id'];//优惠卷ID
			$data['shop_id'] = $Membersalecoupon['shop_id'];//店铺ID
			$data['coupon_amount'] = $Salecoupon['coupon_amount'];//现价
	
			$data['commission'] = $Membersalecoupon['ratio'];//单个佣金

			$data['ratio'] = $Membersalecoupon['ratio'];//比例
			$data['jiesuan_status'] = 1;//比例
			$data['addtime'] = time();


			$this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v))->data(array('is_jiesuan'=>1,'jiesuan_time'=>time()))->save();
			
			$jiesuan_id = $this->MsalecouponjiesuanModel->add($data);
			$this->salecouponjiesuanlogModel->add(array('salecouponjiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'申请商家结算将订单状态改为申请结算','addtime'=>time()));
		}
		$this->success('申请结算成功！',U('/Balance/Salecouponjiesuan/index'));
	}
			

	/*
		@author:chf
		@function:显示抵用券对应店铺的支付结算账单
		@time:2013-8-21
	*/
	function jiesuancoupon(){
		$map['jiesuan_status'] = 2;
		$map['shop_id'] = $_REQUEST['shop_id'];

		$count = $this->MsalecouponjiesuanModel->where($map)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->MsalecouponjiesuanModel->order('membersalecoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v['membersalecoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membersalecoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membersalecoupon['shop_id']))->find();
				$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
				$list[$k]['coupon_name'] = $Salecoupon['coupon_name'];
				$list[$k]['mobile'] = $Membersalecoupon['mobile'];
				$list[$k]['licenseplate'] = $Membersalecoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['uid'] = $member['uid'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['is_use'] = $Membersalecoupon['is_use'];
				$list[$k]['coupon_amount'] = $Salecoupon['coupon_amount'];
				$list[$k]['use_time'] = $Membersalecoupon['use_time'];
			
				
				$list[$k]['commission'] = $v['ratio'];//单个佣金
				$allcommission+=$v['ratio'];//单个佣金
				if($v['ratio'] == '0'){
					$allcoupon_amount+=50;//总结算金额
				}else{
					$allcoupon_amount+=$v['ratio'];//总结算金额
				}
				if($v['ratio'] == '0'){
					$openallcommission+=50;//应支付
				}else{
					$openallcommission+=$v['ratio'];//应支付
				}
			}
			$allpay = $allcommission;//总支付金额
		}
		
		
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('openallcommission',$openallcommission);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}


	/*
		@author:chf
		@function:显示抵用券对应店铺的结算账单
		@time:2013-8-21
	*/
	function confirmjiesuancoupon(){
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];//
		
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		foreach($membersalecoupon_id as $k=>$v){
			
			$membersalecoupon_ids.=$v.",";
		}
		
		$membersalecoupon_ids = substr($membersalecoupon_ids,0,-1);
		
		$map['membersalecoupon_id'] = array('in',$membersalecoupon_ids);
	    $count = $this->MsalecouponjiesuanModel->where($map)->count();
	
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->MsalecouponjiesuanModel->order('membersalecoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v['membersalecoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membersalecoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membersalecoupon['shop_id']))->find();
				$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
				$list[$k]['coupon_name'] = $Salecoupon['coupon_name'];
				$list[$k]['mobile'] = $Membersalecoupon['mobile'];
				$list[$k]['licenseplate'] = $Membersalecoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['is_use'] = $Membersalecoupon['is_use'];
				$list[$k]['coupon_amount'] = $Salecoupon['coupon_amount'];
				$list[$k]['use_time'] = $Membersalecoupon['use_time'];
			
				
				$list[$k]['commission'] = $v['ratio'];//单个佣金
				$allcommission+=$v['ratio'];//单个佣金
				if($v['ratio'] == '0'){
					$allcoupon_amount+=50;//总结算金额
				}else{
					$allcoupon_amount+=$v['ratio'];//总结算金额
				}
				if($v['ratio'] == '0'){
					$openallcommission+=50;//应支付
				}else{
					$openallcommission+=$v['ratio'];//应支付
				}
			}
			$allpay = $allcommission;//总支付金额
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('openallcommission',$openallcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}


	
	/*
		@author:chf
		@function:修改结算订单状态:已结算
		@time:2013-8-21
	*/
	function shopjiesuan(){
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];
		
		foreach($membersalecoupon_id as $k=>$v){
			$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v))->find();
			$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
			$data['jiesuan_status'] = 3;//比例
			$jiesuan_id = $this->MsalecouponjiesuanModel->where(array('membersalecoupon_id'=>$v))->data($data)->save();
			$this->salecouponjiesuanlogModel->add(array('salecouponjiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'结算商家订单,将订单状态改为结算完成','addtime'=>time()));
			

		}
		$this->success('结算成功！',U('/Balance/Salecouponjiesuan/index'));
	}


	/*
		@author:chf
		@function:显示抵用券对应店铺的开票账单
		@time:2013-8-21
	*/
	function opencoupon(){
		$map['jiesuan_status'] = 3;
		$map['shop_id'] = $_REQUEST['shop_id'];
		$count = $this->MsalecouponjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		$list = $this->MsalecouponjiesuanModel->order('membersalecoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		if($list){
			foreach($list as $k=>$v){
				$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v['membersalecoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membersalecoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membersalecoupon['shop_id']))->find();
				$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
				$list[$k]['coupon_name'] = $Salecoupon['coupon_name'];
				$list[$k]['mobile'] = $Membersalecoupon['mobile'];
				$list[$k]['licenseplate'] = $Membersalecoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['uid'] = $member['uid'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['is_use'] = $Membersalecoupon['is_use'];
				$list[$k]['coupon_amount'] = $Salecoupon['coupon_amount'];
				$list[$k]['use_time'] = $Membersalecoupon['use_time'];
				$list[$k]['commission'] = $v['ratio'];//单个佣金
				$allcommission+=$v['ratio'];//单个佣金
				if($v['ratio'] == '0'){
					$allcoupon_amount+=50;//总结算金额
				}else{
					$allcoupon_amount+=$v['ratio'];//总结算金额
				}
				if($v['ratio'] == '0'){
					$openallcommission+=50;//应支付
				}else{
					$openallcommission+=$v['ratio'];//应支付
				}
			}
			$allpay = $allcommission;//总支付金额
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('openallcommission',$openallcommission);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:显示抵用券对应店铺的开票结算账单
		@time:2013-8-21
	*/
	function confirmopencoupon(){
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];//
		
		
		foreach($membersalecoupon_id as $k=>$v){
			
			$membersalecoupon_ids.=$v.",";
		}
		
		$membersalecoupon_ids = substr($membersalecoupon_ids,0,-1);
		
		$map['membersalecoupon_id'] = array('in',$membersalecoupon_ids);
	    $count = $this->MsalecouponjiesuanModel->where($map)->count();
	
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->MsalecouponjiesuanModel->order('membersalecoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();

		if($list){
			foreach($list as $k=>$v){
				$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v['membersalecoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membersalecoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membersalecoupon['shop_id']))->find();
				$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
				$list[$k]['coupon_name'] = $Salecoupon['coupon_name'];
				$list[$k]['mobile'] = $Membersalecoupon['mobile'];
				$list[$k]['licenseplate'] = $Membersalecoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['is_use'] = $Membersalecoupon['is_use'];
				$list[$k]['coupon_amount'] = $Salecoupon['coupon_amount'];
				$list[$k]['use_time'] = $Membersalecoupon['use_time'];
				$list[$k]['commission'] = $v['ratio'];//单个佣金
				$allcommission+=$v['ratio'];//单个佣金
				if($v['ratio'] == '0'){
					$allcoupon_amount+=50;//总结算金额
				}else{
					$allcoupon_amount+=$v['ratio'];//总结算金额
				}
				if($v['ratio'] == '0'){
					$openallcommission+=50;//应支付
				}else{
					$openallcommission+=$v['ratio'];//应支付
				}
			}
			$allpay = $allcommission;//总支付金额
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('openallcommission',$openallcommission);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:修改开票:已开票
		@time:2013-7-10
	*/
	function shopopen(){
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];
		
		foreach($membersalecoupon_id as $k=>$v){
			$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v))->find();
			$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();

			$this->MembercouponModel->where(array('membercoupon_id'=>$v))->data(array('is_jiesuan'=>1,'jiesuan_time'=>time()))->save();
			
			$data['open'] = 1;//比例
			$jiesuan_id = $this->MsalecouponjiesuanModel->where(array('membersalecoupon_id'=>$v))->data($data)->save();
			$this->salecouponjiesuanlogModel->add(array('salecouponjiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'结算商家订单,已开票','addtime'=>time()));
		}
		$this->success('开票成功！',U('/Balance/Salecouponjiesuan/index'));
	
	}

	
	/*
		@author:chf
		@function:查看抵用券结算页面
		@time:2013-8-21
	*/
	function viewdetail(){
		$map['jiesuan_status'] = array('neq',0);
		$map['shop_id'] = $_REQUEST['shop_id'];

		if($_REQUEST['jiesuan_status']){
			$data['jiesuan_status'] = $map['jiesuan_status'] = $_REQUEST['jiesuan_status'];
		}else{
			$map['jiesuan_status'] = array('neq',0);
			$data['jiesuan_status'] = $_REQUEST['jiesuan_status']; 
		}
		$count = $this->MsalecouponjiesuanModel->where($map)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }
		$page = $p->show_admin();
		$list = $this->MsalecouponjiesuanModel->order('membersalecoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		if($list){
			foreach($list as $k=>$v){
				$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v['membersalecoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membersalecoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membersalecoupon['shop_id']))->find();
				$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();
				$list[$k]['coupon_name'] = $Salecoupon['coupon_name'];
				$list[$k]['mobile'] = $Membersalecoupon['mobile'];
				$list[$k]['licenseplate'] = $Membersalecoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['uid'] = $Membersalecoupon['uid'];
				$list[$k]['order_id'] = $Membersalecoupon['order_id'];
				$list[$k]['coupon_amount'] = $Salecoupon['coupon_amount'];
				$list[$k]['use_time'] = $Membersalecoupon['use_time'];
				$list[$k]['commission'] = $v['ratio'];//单个佣金
				$allcommission+=$v['ratio'];//单个佣金
				if($v['ratio'] == '0'){
					$allcoupon_amount+=50;//总结算金额
				}else{
					$allcoupon_amount+=$v['ratio'];//总结算金额
				}
				if($v['ratio'] == '0'){
					$openallcommission+=50;//应支付
				}else{
					$openallcommission+=$v['ratio'];//应支付
				}
			}
			$allpay = $allcommission;//总支付金额
		}
	
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('openallcommission',$openallcommission);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
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

   
}