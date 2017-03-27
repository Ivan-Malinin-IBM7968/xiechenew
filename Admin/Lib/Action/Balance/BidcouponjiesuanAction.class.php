<?php
//订单
class BidcouponjiesuanAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
		$this->ShopModel = D('shop');//商铺表
		//$this->MembercouponModel = D('membercoupon');//优惠卷表
		$this->MemberModel = D('member');//用户表
		//$this->CouponModel = D('coupon');//优惠券信息表
		//$this->couponjiesuanModel = D('couponjiesuan');//结算表
		//$this->couponjiesuanlogModel = D('couponjiesuanlog');//结算表日志表


		$this->BidcouponModel = D('Bidcoupon');//保险订单 返利现金券表
		$this->BidcouponjiesuanModel = D('Bidcouponjiesuan');//保险订单 返利现金券结算表
		$this->BidcouponjiesuanlogModel = D('Bidcouponjiesuanlog');//结算表日志表
	}
	
	/*
		@author:ysh
		@function:显示优惠卷结算系统首页
		@time:2013/7/18
	*/
	function index(){
		
		if($_REQUEST['shopname']){
			$data['shopname'] = $_REQUEST['shopname'];
		}
		if($_REQUEST['shop_id']){
			$data['shop_id'] = $map['shop_id'] = $_REQUEST['shop_id'];
		}
		$count = $this->BidcouponModel->group('shop_id')->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,25);
		// 分页显示输出

		foreach ($_REQUEST as $key => $val) {
            if (!is_array($val) && $val != "" ) {
                $p->parameter .= "$key/" . urlencode($val) . "/";
            }
        }

		$page = $p->show_admin();
		$data['Bidcoupon'] = $this->BidcouponModel->where($map)->group('shop_id')->limit($p->firstRow.','.$p->listRows)->select();
		foreach($data['Bidcoupon'] as $k=>$v){
			$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
			$data['Bidcoupon'][$k]['count'] = $this->BidcouponModel->where(array('is_jiesuan'=>0,'status'=>0,'shop_id'=>$v['shop_id']))->count();
			$data['Bidcoupon'][$k]['isjiesuan_count'] = $this->BidcouponjiesuanModel->where(array('jiesuan_status'=>2,'shop_id'=>$v['shop_id']))->count();
			$data['Bidcoupon'][$k]['shop_name'] = $shop['shop_name'];
			$lastcoupon = $this->BidcouponModel->where(array('is_jiesuan'=>1,'shop_id'=>$v['shop_id']))->order('jiesuan_time DESC')->find();

			$data['Bidcoupon'][$k]['open_count'] = $this->BidcouponjiesuanModel->where(array('jiesuan_status'=>3,'open'=>0,'shop_id'=>$v['shop_id']))->count();

			$data['Bidcoupon'][$k]['jiesuan_time'] = $lastcoupon['jiesuan_time'];
		}
		$data['shop_list'] = $this->ShopModel->select();

		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:ysh
		@function:显示优惠卷对应店铺的结算账单
		@time:2013/7/19
	*/
	function shopcoupon(){
		$map['is_jiesuan'] = 0;
		$map['status'] = 0;
		$map['shop_id'] = $_REQUEST['shop_id'];
		if($_REQUEST['start_usetime'] && $_REQUEST['end_usetime']){
			$map['use_time'] = array(array('lt',strtotime($_REQUEST['end_usetime'])),array('gt',strtotime($_REQUEST['start_usetime'])),'AND');
			$data['start_usetime'] = $_REQUEST['start_usetime'];
			$data['end_usetime'] = $_REQUEST['end_usetime'];
		}
		
		$count = $this->BidcouponModel->where($map)->count();

		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->BidcouponModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				//$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['mobile'] = $member['mobile'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $v['price'];
				$list[$k]['cost_price'] = $v['price'];
				
				//团购券的结算公式
				$allcommission+=($v['price']*($v['ratio']/100));//总佣金
				$list[$k]['commission'] = ($v['price']*($v['ratio']/100));//单个佣金
				$allcoupon_amount+=$v['price'];//总结算金额
		
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
		@author:ysh
		@function:显示优惠卷对应店铺的结算账单
		@time:2013/7/19
	*/
	function confirmshopcoupon(){
		$id = $_REQUEST['id'];
		if($id) {
			foreach($id as $k=>$v){
				$ids.=$v.",";
			}			
		}		
		$ids = substr($ids,0,-1);
		
		$map['id'] = array('in',$ids);
	    $count = $this->BidcouponModel->where($map)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->BidcouponModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				//$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['mobile'] = $member['mobile'];
				$list[$k]['shop_name'] = $shop['shop_name'];
				$list[$k]['coupon_amount'] = $v['price'];
				$list[$k]['cost_price'] = $v['price'];

				$allcommission+=($v['price']*($v['ratio']/100));//总佣金
				$list[$k]['commission'] = ($v['price']*($v['ratio']/100));//单个佣金
				$allcoupon_amount+=$v['price'];//总结算金额
				
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
		@author:ysh
		@function:向结算订单插入申请结算信息
		@time:2013/7/19
	*/
	function shopapply(){
		$id = $_REQUEST['id'];
		
		foreach($id as $k=>$v){
			$Bidcoupon = $this->BidcouponModel->where(array('id'=>$v))->find();
			
			$this->BidcouponModel->where(array('id'=>$v))->data(array('is_jiesuan'=>1,'jiesuan_time'=>time()))->save();
			$data['bidcoupon_id'] = $Bidcoupon['id'];//优惠卷ID
			$data['shop_id'] = $Bidcoupon['shop_id'];//店铺ID
			$data['cost_price'] = $Bidcoupon['price'];//原价
			$data['coupon_amount'] = $Bidcoupon['price'];//现价
			
			$data['commission'] = ($Bidcoupon['price']*($Bidcoupon['ratio']/100));//单个佣金
			
			$data['ratio'] = $Bidcoupon['ratio'];//比例
			$data['jiesuan_status'] = 1;
			$data['addtime'] = time();
			
			$jiesuan_id = $this->BidcouponjiesuanModel->add($data);

			$this->BidcouponjiesuanlogModel->add(array('bidcoupon_jiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginAdminUserName'],'log'=>'申请商家结算将订单状态改为申请结算','addtime'=>time()));
		}
		$this->success('申请结算成功！',U('/Store/Bidcouponjiesuan/index'));
	}


	/*
		@author:ysh
		@function:显示优惠卷对应店铺的支付结算账单
		@time:2013/7/19
	*/
	function jiesuancoupon(){
		
		$map['jiesuan_status'] = 2;
		$map['shop_id'] = $_REQUEST['shop_id'];
		$count = $this->BidcouponjiesuanModel->where($map)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->BidcouponjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Bidcoupon = $this->BidcouponModel->where(array('id'=>$v['bidcoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Bidcoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				//$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['mobile'] = $member['mobile'];
				$list[$k]['shop_name'] = $shop['shop_name'];

				$allcommission+=($v['coupon_amount']*($v['ratio']/100));//总佣金
				$list[$k]['commission'] = ($v['coupon_amount']*($v['ratio']/100));//单个佣金
				$allcoupon_amount+=$v['coupon_amount'];//总结算金额
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
		@function:显示优惠卷对应店铺的结算账单
		@time:2013-7-10
	*/
	function confirmjiesuancoupon(){
		$id = $_REQUEST['id'];//
		if($id) {
			foreach($id as $k=>$v){
				$ids.=$v.",";
			}			
		}		
		$ids = substr($ids,0,-1);
		
		$map['id'] = array('in',$ids);
	    $count = $this->BidcouponjiesuanModel->where($map)->count();
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->BidcouponjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Bidcoupon = $this->BidcouponModel->where(array('id'=>$v['bidcoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Bidcoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				//$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['mobile'] = $member['mobile'];
				$list[$k]['shop_name'] = $shop['shop_name'];

				$allcommission+=($v['coupon_amount']*($v['ratio']/100));//总佣金
				$list[$k]['commission'] = ($v['coupon_amount']*($v['ratio']/100));//单个佣金
				$allcoupon_amount+=$v['coupon_amount'];//总结算金额

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
		@author:ysh
		@function:修改结算订单状态:已结算
		@time:2013/7/19
	*/
	function shopjiesuan(){
		$id = $_REQUEST['id'];
		foreach($id as $k=>$v){		
			$data['jiesuan_status'] = 3;
			$this->BidcouponjiesuanModel->where(array('id'=>$v))->data($data)->save();
			$this->BidcouponjiesuanlogModel->add(array('bidcoupon_jiesuan_id'=>$v,'name'=>$_SESSION['loginAdminUserName'],'log'=>'结算商家订单,将订单状态改为结算完成','addtime'=>time()));
		}
		$this->success('结算成功！',U('/Store/Bidcouponjiesuan/index'));
	}


	/*
		@author:ysh
		@function:显示优惠卷对应店铺的开票账单
		@time:2013/7/19
	*/
	function opencoupon(){
		
		$map['jiesuan_status'] = 3;
		$map['shop_id'] = $_REQUEST['shop_id'];

		$count = $this->BidcouponjiesuanModel->where($map)->count();
		
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->BidcouponjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Bidcoupon = $this->BidcouponModel->where(array('id'=>$v['bidcoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Bidcoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				//$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['mobile'] = $member['mobile'];
				$list[$k]['shop_name'] = $shop['shop_name'];

				$allcommission+=($v['coupon_amount']*($v['ratio']/100));//总佣金
				$list[$k]['commission'] = ($v['coupon_amount']*($v['ratio']/100));//单个佣金
				$allcoupon_amount+=$v['coupon_amount'];//总结算金额
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
		@author:ysh
		@function:显示优惠卷对应店铺的开票结算账单
		@time:2013/7/19
	*/
	function confirmopencoupon(){
		$id = $_REQUEST['id'];//
		if($id) {
			foreach($id as $k=>$v){
				$ids.=$v.",";
			}			
		}		
		$ids = substr($ids,0,-1);
		
		$map['id'] = array('in',$ids);
	    $count = $this->BidcouponjiesuanModel->where($map)->count();	
	
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 50);
		// 分页显示输出
		$page = $p->show_admin();
		
		$list = $this->BidcouponjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Bidcoupon = $this->BidcouponModel->where(array('id'=>$v['bidcoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Bidcoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				//$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['mobile'] = $member['mobile'];
				$list[$k]['shop_name'] = $shop['shop_name'];

				$allcommission+=($v['coupon_amount']*($v['ratio']/100));//总佣金
				$list[$k]['commission'] = ($v['coupon_amount']*($v['ratio']/100));//单个佣金
				$allcoupon_amount+=$v['coupon_amount'];//总结算金额

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
		@author:ysh
		@function:修改开票:已开票
		@time:2013/7/19
	*/
	function shopopen(){
		$id = $_REQUEST['id'];
		
		foreach($id as $k=>$v){			
			$data['open'] = 1;//比例
			$this->BidcouponjiesuanModel->where(array('id'=>$v))->data($data)->save();
			$this->BidcouponjiesuanlogModel->add(array('Bidcoupon_jiesuan_id'=>$v,'name'=>$_SESSION['loginAdminUserName'],'log'=>'结算商家订单,已开票','addtime'=>time()));
			

		}
		$this->success('开票成功！',U('/Store/Bidcouponjiesuan/index'));
	
	
	
	}


	function viewdetail(){
		$map['jiesuan_status'] = array('neq',0);
		$map['shop_id'] = $_REQUEST['shop_id'];

		if($_REQUEST['jiesuan_status']){
			$data['jiesuan_status'] = $map['jiesuan_status'] = $_REQUEST['jiesuan_status'];
		}else{
			$map['jiesuan_status'] = array('neq',0);
			$data['jiesuan_status'] = $_REQUEST['jiesuan_status']; 
		}
		$count = $this->BidcouponjiesuanModel->where($map)->count();
		
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
		
		$list = $this->BidcouponjiesuanModel->order('id DESC')->where($map)->limit($p->firstRow.','.$p->listRows)->select();
		
		if($list){
			foreach($list as $k=>$v){
				$Bidcoupon = $this->BidcouponModel->where(array('id'=>$v['bidcoupon_id']))->find();
				$member = $this->MemberModel->where(array('uid'=>$Bidcoupon['uid']))->find();
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				//$coupon = $this->CouponModel->where(array('id'=>$v['coupon_id']))->find();
				$list[$k]['username'] = $member['username'];
				$list[$k]['mobile'] = $member['mobile'];
				$list[$k]['shop_name'] = $shop['shop_name'];

				$allcommission+=($v['coupon_amount']*($v['ratio']/100));//总佣金
				$list[$k]['commission'] = ($v['coupon_amount']*($v['ratio']/100));//单个佣金
				$allcoupon_amount+=$v['coupon_amount'];//总结算金额

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