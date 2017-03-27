<?php
//订单
class CouponjiesuanAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->ShopModel = D('shop');//商铺表
		$this->MembercouponModel = D('membercoupon');//优惠卷表
		$this->MemberModel = D('member');//用户表
		$this->CouponModel = D('coupon');//优惠券信息表
		$this->couponjiesuanModel = D('couponjiesuan');//结算表
		$this->couponjiesuanlogModel = D('couponjiesuanlog');//结算表日志表
	}
	
	/*
		@author:chf
		@function:显示优惠卷结算系统首页
		@time:2013-7-10
	*/
	function index(){
		if($_REQUEST['shopname']){
			$data['shopname'] = $_REQUEST['shopname'];
		}
		if($_REQUEST['shop_id']){
			$data['shop_id'] = $map['shop_id'] = $_REQUEST['shop_id'];
		}
		$count = $this->MembercouponModel->Distinct(true)->field('shop_id')->select();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page(count($count),25);
		// 分页显示输出
		foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }

		$page = $p->show_admin();
		$data['Membercoupon'] = $this->MembercouponModel->where($map)->group('shop_id')->limit($p->firstRow.','.$p->listRows)->select();
		foreach($data['Membercoupon'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			//$data['Membercoupon'][$k]['count'] = $this->MembercouponModel->where(array('is_jiesuan'=>0,'is_use'=>1,'is_delete'=>0,'shop_id'=>$v['shop_id']))->count();
			//$mad['xc_coupon.coupon_across'] = array('neq',1);
			$mad['xc_membercoupon.is_jiesuan'] = 0;
			$mad['xc_membercoupon.is_use'] = 1;
			$mad['xc_membercoupon.is_delete'] = 0;
			$mad['xc_membercoupon.shop_id'] = $v['shop_id'];
			$data['Membercoupon'][$k]['count'] = $this->MembercouponModel->join('xc_coupon on xc_coupon.id=xc_membercoupon.coupon_id')->where($mad)->count();

			$data['Membercoupon'][$k]['isjiesuan_count'] = $this->couponjiesuanModel->where(array('jiesuan_status'=>2,'shop_id'=>$v['shop_id']))->count();
			$data['Membercoupon'][$k]['shop_name'] = $shop['shop_name'];
			$lastcoupon = $this->MembercouponModel->where(array('is_jiesuan'=>1,'is_delete'=>0,'shop_id'=>$v['shop_id']))->order('jiesuan_time DESC')->find();

			$data['Membercoupon'][$k]['open_count'] = $this->couponjiesuanModel->where(array('jiesuan_status'=>3,'open'=>0,'shop_id'=>$v['shop_id']))->count();
			$data['Membercoupon'][$k]['pay_count'] = $this->couponjiesuanModel->where(array('pay_status'=>1,'shop_id'=>$v['shop_id']))->count();
			$data['Membercoupon'][$k]['jiesuan_time'] = $lastcoupon['jiesuan_time'];
		}
		$data['shop_list'] = $this->ShopModel->select();
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:显示优惠卷对应店铺的结算账单
		@time:2013-7-10
	*/
	function shopcoupon(){
		if($_REQUEST['is_across']=='1'){
			$map['xc_coupon.coupon_across'] = 1;
		}else{
			$map['xc_coupon.coupon_across'] = 0;
		}
		$map['xc_membercoupon.is_delete'] = 0;
		$map['xc_membercoupon.is_jiesuan'] = 0;
		$map['xc_membercoupon.is_use'] = 1;
		$map['xc_membercoupon.uid'] = array(array('neq','1'),array('neq','12918'));
		$map['xc_membercoupon.shop_ids'] = $_REQUEST['shop_id'];
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['xc_membercoupon.coupon_type'] = $_REQUEST['coupon_type'];
		}
		if($_REQUEST['start_usetime'] && $_REQUEST['end_usetime']){
			$map['xc_membercoupon.use_time'] = array(array('lt',strtotime($_REQUEST['end_usetime'])),array('gt',strtotime($_REQUEST['start_usetime'])),'AND');
			$data['start_usetime'] = $_REQUEST['start_usetime'];
			$data['end_usetime'] = $_REQUEST['end_usetime'];
		}
		
		$count = $this->MembercouponModel->join('xc_coupon on xc_coupon.coupon_id=xc_membercoupon.coupon_id')->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->MembercouponModel->join('xc_coupon on xc_coupon.id=xc_membercoupon.coupon_id')->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		echo $this->MembercouponModel->getLastsql();
	
		if($list){
			foreach($list as $k=>$v){
				$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($v['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($v['ratio']/100)));//单个佣金
					$allcoupon_amount+=$coupon['cost_price'];//总结算金额
					
				}else{
					
					$allcommission+=($coupon['coupon_amount']*($v['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($v['ratio']/100));//单个佣金
					$allcoupon_amount+=$coupon['coupon_amount'];//总结算金额
					
				}
				
			}
			

			$allpay = $allcoupon_amount - $allcommission;
		}
		
		$this->assign('is_across',$_REQUEST['is_across']);
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}
	
	/*
		@author:chf
		@function:显示优惠卷对应店铺的结算账单
		@time:2013-7-10
	*/
	function confirmshopcoupon(){
		$membercoupon_id = $_REQUEST['membercoupon_id'];//
		
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		foreach($membercoupon_id as $k=>$v){
			
			$membercoupon_ids.=$v.",";
		}
		
		$membercoupon_ids = substr($membercoupon_ids,0,-1);
		
		$map['membercoupon_id'] = array('in',$membercoupon_ids);
	    $count = $this->MembercouponModel->where($map)->count();
	
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->MembercouponModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($v['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($v['ratio']/100)));//单个佣金
					$allcoupon_amount+=$coupon['cost_price'];//总结算金额
				}else{
					
					$allcommission+=($coupon['coupon_amount']*($v['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($v['ratio']/100));//单个佣金
					$allcoupon_amount+=$coupon['coupon_amount'];//总结算金额
				}

			}
			
			$allpay = $allcoupon_amount - $allcommission;
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}


	/*
		@author:chf
		@function:向结算订单插入申请结算信息
		@time:2013-7-10
	*/
	function shopapply(){
		$membercoupon_id = $_REQUEST['membercoupon_id'];
		
		foreach($membercoupon_id as $k=>$v){
			$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v))->find();
			$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
			
			$this->MembercouponModel->where(array('membercoupon_id'=>$v))->data(array('is_jiesuan'=>1,'jiesuan_time'=>time()))->save();
			$data['membercoupon_id'] = $v;//用户优惠卷ID
			$data['coupon_id'] = $Membercoupon['coupon_id'];//优惠卷ID
			$data['coupon_type'] = $Membercoupon['coupon_type'];//优惠卷类型
			if($Membercoupon['shop_id']>0){
				$data['shop_id'] = $Membercoupon['shop_id'];//店铺ID
			}else{
				$data['shop_id'] = $Membercoupon['shop_ids'];//店铺ID
			}
			$data['cost_price'] = $coupon['cost_price'];//原价
			$data['coupon_amount'] = $coupon['coupon_amount'];//现价
			if($Membercoupon['coupon_type']=='1'){	
				$data['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
			}else{
				$data['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					
			}
			$data['ratio'] = $Membercoupon['ratio'];//比例
			$data['jiesuan_status'] = 1;//比例
			$data['addtime'] = time();


			$this->MembercouponModel->where(array('membercoupon_id'=>$v))->data(array('is_jiesuan'=>1,'jiesuan_time'=>time()))->save();

			$jiesuan_id = $this->couponjiesuanModel->add($data);

			$this->couponjiesuanlogModel->add(array('coupon_jiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'申请商家结算将订单状态改为申请结算','addtime'=>time()));
			

		}
		$this->success('申请结算成功！',U('/Store/Couponjiesuan/index'));
	}


	/*
		@author:chf
		@function:显示优惠卷对应店铺的支付结算账单
		@time:2013-7-10
	*/
	function jiesuancoupon(){
		
		$map['jiesuan_status'] = 2;
		$map['shop_id'] = $_REQUEST['shop_id'];
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		$count = $this->couponjiesuanModel->where($map)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->couponjiesuanModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		echo $this->couponjiesuanModel->getLastsql();
		if($list){
			foreach($list as $k=>$v){
				$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membercoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membercoupon['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
				$list[$k]['coupon_name'] = $coupon['coupon_name'];
				$list[$k]['mobile'] = $Membercoupon['mobile'];
				$list[$k]['uid'] = $Membercoupon['uid'];
				$list[$k]['use_time'] = $Membercoupon['use_time'];
				$list[$k]['licenseplate'] = $Membercoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['is_use'] = $Membercoupon['is_use'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
					$allcoupon_amount+=$coupon['cost_price'];//总结算金额
				}else{
					
					$allcommission+=($coupon['coupon_amount']*($Membercoupon['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					$allcoupon_amount+= $coupon['coupon_amount'];//总结算金额
				}
			}
			$allpay = $allcoupon_amount - $allcommission;
		}
		//print_r($list);
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}


	/*
		@author:chf
		@function:显示优惠卷对应店铺的结算账单
		@time:2013-7-10
	*/
	function confirmjiesuancoupon(){
		$membercoupon_id = $_REQUEST['membercoupon_id'];//
		
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		foreach($membercoupon_id as $k=>$v){
			
			$membercoupon_ids.=$v.",";
		}
		
		$membercoupon_ids = substr($membercoupon_ids,0,-1);
		
		$map['membercoupon_id'] = array('in',$membercoupon_ids);
	    $count = $this->couponjiesuanModel->where($map)->count();
	
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->couponjiesuanModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membercoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membercoupon['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
				
				$list[$k]['coupon_name'] = $coupon['coupon_name'];
				$list[$k]['mobile'] = $Membercoupon['mobile'];
				$list[$k]['licenseplate'] = $Membercoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
					$allcoupon_amount+=$coupon['cost_price'];//总结算金额 
				}else{
					
					$allcommission+=($coupon['coupon_amount']*($Membercoupon['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					$allcoupon_amount+=$coupon['coupon_amount'];//总结算金额 
				}
				

			}
			$allpay = $allcoupon_amount - $allcommission;
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}


	
	/*
		@author:chf
		@function:修改结算订单状态:已结算
		@time:2013-7-10
	*/
	function shopjiesuan(){
		$membercoupon_id = $_REQUEST['membercoupon_id'];
		foreach($membercoupon_id as $k=>$v){
			$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v))->find();
			$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
			$data['jiesuan_status'] = 3;//比例
			$jiesuan_id = $this->couponjiesuanModel->where(array('membercoupon_id'=>$v))->data($data)->save();
			$this->couponjiesuanlogModel->add(array('coupon_jiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'结算商家订单,将订单状态改为结算完成','addtime'=>time()));
		}
		$this->success('结算成功！',U('/Store/Couponjiesuan/index'));
	}


	/*
		@author:chf
		@function:显示优惠卷对应店铺的开票账单
		@time:2013-7-10
	*/
	function opencoupon(){
		$map['jiesuan_status'] = 3;
		$map['open'] = 0;
		$map['shop_id'] = $_REQUEST['shop_id'];
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		$count = $this->couponjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		$list = $this->couponjiesuanModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		if($list){
			foreach($list as $k=>$v){
				$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membercoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membercoupon['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
				$list[$k]['is_use'] = $Membercoupon['is_use'];
				$list[$k]['coupon_name'] = $coupon['coupon_name'];
				$list[$k]['mobile'] = $Membercoupon['mobile'];
				$list[$k]['uid'] = $Membercoupon['uid'];
				$list[$k]['use_time'] = $Membercoupon['use_time'];
				$list[$k]['licenseplate'] = $Membercoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
					$allcoupon_amount+= $coupon['cost_price'];//总结算金额
				}else{
					$allcommission+=($coupon['coupon_amount']*($Membercoupon['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					$allcoupon_amount+= $coupon['coupon_amount'];//总结算金额
				}
			}
			$allpay = $allcoupon_amount - $allcommission;
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:显示优惠卷对应店铺的开票结算账单
		@time:2013-7-10
	*/
	function confirmopencoupon(){
		$membercoupon_id = $_REQUEST['membercoupon_id'];//
		
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		foreach($membercoupon_id as $k=>$v){
			
			$membercoupon_ids.=$v.",";
		}
		
		$membercoupon_ids = substr($membercoupon_ids,0,-1);
		
		$map['membercoupon_id'] = array('in',$membercoupon_ids);
	    $count = $this->couponjiesuanModel->where($map)->count();
	
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->couponjiesuanModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membercoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membercoupon['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
				
				$list[$k]['coupon_name'] = $coupon['coupon_name'];
				$list[$k]['mobile'] = $Membercoupon['mobile'];
				$list[$k]['licenseplate'] = $Membercoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
					

				}else{
					
					$allcommission+=($coupon['coupon_amount']*($Membercoupon['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					$allcoupon_amount+= $coupon['cost_price'];//总结算金额
				}
				

			}
			$allpay = $allcoupon_amount - $allcommission;
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
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
		$membercoupon_id = $_REQUEST['membercoupon_id'];
		
		foreach($membercoupon_id as $k=>$v){
			$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v))->find();
			$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();

			$this->MembercouponModel->where(array('membercoupon_id'=>$v))->data(array('is_jiesuan'=>1,'jiesuan_time'=>time()))->save();
			
			$data['open'] = 1;//比例
			$jiesuan_id = $this->couponjiesuanModel->where(array('membercoupon_id'=>$v))->data($data)->save();
			$this->couponjiesuanlogModel->add(array('coupon_jiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'结算商家订单,已开票','addtime'=>time()));
			

		}
		$this->success('开票成功！',U('/Store/Couponjiesuan/index'));
	
	
	
	}
	
	/*
		@author:chf
		@function:显示具体查看
		@time:2013-10-28
	*/
	function viewdetail(){
		$map['jiesuan_status'] = array('neq',0);
		$map['shop_id'] = $_REQUEST['shop_id'];

		
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		if($_REQUEST['jiesuan_status']){
			$data['jiesuan_status'] = $map['jiesuan_status'] = $_REQUEST['jiesuan_status'];
		}else{
			$map['jiesuan_status'] = array('neq',0);
			$data['jiesuan_status'] = $_REQUEST['jiesuan_status']; 
		}
		$count = $this->couponjiesuanModel->where($map)->count();
		
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
		
		$list = $this->couponjiesuanModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		echo $this->couponjiesuanModel->getLastsql();
		if($list){
			foreach($list as $k=>$v){
				$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				if($Membercoupon['uid']==1){
					unset($list[$k]);
					continue;
				}
				$member = $this->MemberModel->where(array('uid'=>$Membercoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membercoupon['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
				$list[$k]['coupon_name'] = $coupon['coupon_name'];
				$list[$k]['use_time'] = $Membercoupon['use_time'];
				$list[$k]['uid'] = $Membercoupon['uid'];
				$list[$k]['mobile'] = $Membercoupon['mobile'];
				$list[$k]['licenseplate'] = $Membercoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
					$allcoupon_amount+= $coupon['cost_price'];//总结算金额
				}else{
					$allcommission+=($coupon['coupon_amount']*($Membercoupon['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					$allcoupon_amount+= $coupon['coupon_amount'];//总结算金额
				}
				

			}
			

			$allpay = $allcoupon_amount - $allcommission;
		}
		
		
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:显示可打款页面信息
		@time:2013-01-06
	*/
	function showpay(){
		$map['open'] = 1;
		$map['shop_id'] = $_REQUEST['shop_id'];
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		$count = $this->couponjiesuanModel->where($map)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->couponjiesuanModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membercoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membercoupon['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
				$list[$k]['coupon_name'] = $coupon['coupon_name'];
				$list[$k]['mobile'] = $Membercoupon['mobile'];
				$list[$k]['licenseplate'] = $Membercoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
					$allcoupon_amount+=$coupon['cost_price'];//总结算金额
				}else{
					
					$allcommission+=($coupon['coupon_amount']*($Membercoupon['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					$allcoupon_amount+= $coupon['coupon_amount'];//总结算金额
				}
			}
			

			$allpay = $allcoupon_amount - $allcommission;
		}
		
		
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);

		$this->display();
	}


		/*
		@author:chf
		@function:显示已开票的优惠券结算方案
		@time:2013-7-10
	*/
	function paycoupon(){
		$map['open'] = 1;
		$map['shop_id'] = $_REQUEST['shop_id'];
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		$count = $this->couponjiesuanModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		$list = $this->couponjiesuanModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		if($list){
			foreach($list as $k=>$v){
				$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membercoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membercoupon['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
				$list[$k]['is_use'] = $Membercoupon['is_use'];
				$list[$k]['coupon_name'] = $coupon['coupon_name'];
				$list[$k]['mobile'] = $Membercoupon['mobile'];
				$list[$k]['licenseplate'] = $Membercoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
					$allcoupon_amount+= $coupon['cost_price'];//总结算金额
				}else{
					$allcommission+=($coupon['coupon_amount']*($Membercoupon['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					$allcoupon_amount+= $coupon['coupon_amount'];//总结算金额
				}
			}
			$allpay = $allcoupon_amount - $allcommission;
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:显示已申请打款的页面信息
		@time:2013-01-06
	*/
	function confirmpayoupon(){
		$membercoupon_id = $_REQUEST['membercoupon_id'];//
		
		if($_REQUEST['coupon_type']){
			$data['coupon_type'] = $map['coupon_type'] = $_REQUEST['coupon_type'];
		}
		foreach($membercoupon_id as $k=>$v){
			
			$membercoupon_ids.=$v.",";
		}
		
		$membercoupon_ids = substr($membercoupon_ids,0,-1);
		
		$map['membercoupon_id'] = array('in',$membercoupon_ids);
	    $count = $this->couponjiesuanModel->where($map)->count();
	
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->couponjiesuanModel->order('membercoupon_id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v['membercoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Membercoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Membercoupon['shop_id']))->find();
				$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();
				
				$list[$k]['coupon_name'] = $coupon['coupon_name'];
				$list[$k]['mobile'] = $Membercoupon['mobile'];
				$list[$k]['licenseplate'] = $Membercoupon['licenseplate'];
				$list[$k]['username'] = $member['username'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $coupon['coupon_amount'];
				$list[$k]['cost_price'] = $coupon['cost_price'];
				if($v['coupon_type']=='1'){
					//总佣金
					$allcommission+=(($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));
					$list[$k]['commission']  = (($coupon['cost_price']-$coupon['coupon_amount'])+( $coupon['coupon_amount']*($Membercoupon['ratio']/100)));//单个佣金
					

				}else{
					
					$allcommission+=($coupon['coupon_amount']*($Membercoupon['ratio']/100));//总佣金
					$list[$k]['commission'] = ($coupon['coupon_amount']*($Membercoupon['ratio']/100));//单个佣金
					$allcoupon_amount+= $coupon['cost_price'];//总结算金额
				}
				

			}
			$allpay = $allcoupon_amount - $allcommission;
		}
		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('allcoupon_amount',$allcoupon_amount);
		$this->assign('allcommission',$allcommission);
		$this->assign('allpay',round($allpay,5));
		$this->assign('shop_id',$_REQUEST['shop_id']);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:chf
		@function:修改打款(已打款)
		@time:2013-01-06
	*/
	function shoppay(){
		$membercoupon_id = $_REQUEST['membercoupon_id'];
		
		foreach($membercoupon_id as $k=>$v){
			$Membercoupon = $this->MembercouponModel->where(array('membercoupon_id'=>$v))->find();
			$coupon = $this->CouponModel->where(array('id'=>$Membercoupon['coupon_id']))->find();

			
			
			$data['pay_status'] = 1;//打款状态
			$jiesuan_id = $this->couponjiesuanModel->where(array('membercoupon_id'=>$v))->data($data)->save();
			$this->couponjiesuanlogModel->add(array('coupon_jiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'结算商家订单,已打款','addtime'=>time()));
			

		}
		$this->success('打款成功！',U('/Store/Couponjiesuan/index'));
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