<?php
/*
 */
class BidformsAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->BidorderModel = D('bidorder');//保险类订单表
		$this->ShopModel = D('shop');//店铺表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->BidCouponModel = D('bidcoupon');//保险类订单返利券表
		$this->FsModel = D("Fs");
	}

	/*
		@author:chf
		@function:显示保险类订单返利券页
		@time:2013-05-08
	*/
    public function index(){
		if($_REQUEST['shop_id']){
			$data['shop_id'] = $map['id'] = $_REQUEST['shop_id'];
		}else {
			$shopbidding_ids = $this->ShopbiddingModel->where()->group('shop_id')->select();
			if($shopbidding_ids) {
				foreach($shopbidding_ids as $key=>$val) {
					$shop_ids[] = $val['shop_id'];
				}
			}
			$map['id'] = array('in',$shop_ids);
		}
		if($_REQUEST['shopname']){
			$data['shop_name'] = $_REQUEST['shopname'];
		}

		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];
			$BID['create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
		}
		$map['status'] = 1;
		// 计算总数
		$count = $this->ShopModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show_admin();
		$data['shop'] = $this->ShopModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		if($data['shop']){
			foreach($data['shop'] as $k=>$v){
					$BID['shop_id'] = $v['id'];
					$data['shop'][$k]['order_count'] = $this->BidorderModel->where($BID)->count();
					$data['shop'][$k]['shopbidding_count'] = $this->ShopbiddingModel->where($BID)->count();
					$data['shop'][$k]['price'] = $this->BidCouponModel->where($BID)->sum('price');
			}
		
		}
		$data['shop_list'] = $this->ShopModel->select();
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
    }
	
	/*
		@author:chf
		@function:显示保险类订单页商品具体订单页
		@time:2013-05-08
	*/
	function ordercount(){
		$data['shop_id'] = $map['shop_id'] = $_REQUEST['shop_id'];

		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];
			$map['create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
			$Bid['create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
		}
		$map['status'] = 1;
		// 计算总数
		$count = $this->BidorderModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,25);
		// 分页显示输出
		$page = $p->show_admin();
		$data['Bidorder'] = $this->BidorderModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		
		if($data){
			foreach($data['Bidorder'] as $k=>$v){
				$Shopbidding = $this->ShopbiddingModel->where(array('id'=>$v['bid_id']))->find();
				$shop = $this->ShopModel->where(array('id'=>$Shopbidding['shop_id']))->find();
				$data['Bidorder'][$k]['order_id'] = $this->get_orderid($v['id']);
				$data['Bidorder'][$k]['shop_name'] = $shop['shop_name'];
			}
		
		}
		$Bid['shop_id'] = $data['shop_id'];
		$data['order_count'] = $this->BidorderModel->where($Bid)->count();

		
		$data['shop_list'] = $this->ShopModel->select();
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
	
	}
	

	/*
		@author:chf
		@function:显示保险类订单页返利页面信息
		@time:2013-05-08
	*/
	function rate(){
		$data['shop_id'] = $map['shop_id'] = $_REQUEST['shop_id'];
		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];

			$map['create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));

			$Bid['create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
		}
		$map['status'] = 1;
		// 计算总数
		$count = $this->BidCouponModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show_admin();
		$data['Bidcoupon'] = $this->BidCouponModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		
		if($data['Bidcoupon']){
			foreach($data['Bidcoupon'] as $k=>$v){
				$shop = $this->ShopModel->where(array('id'=>$v['shop_id']))->find();
				$data['Bidcoupon'][$k]['shop_name'] = $shop['shop_name'];
			}
		
		}
		$Bid['shop_id'] = $data['shop_id'];
		$data['price_count'] = $this->BidCouponModel->where($Bid)->sum('price');

		$this->assign('data',$data);
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

	/*
		@author:ysh
		@function:显示店铺参与的竞价列表
		@time:2013-11-6 
	*/
    public function shopcount(){
		if($_REQUEST['insurance_status']){
			$data['insurance_status'] = $map['insurance_status'] = $_REQUEST['insurance_status'];
		}
					
		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];
			$map['xc_shopbidding.create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
		}
		$map['shop_id'] = $_REQUEST['shop_id'];
		
		// 计算总数
		$count = $this->ShopbiddingModel->where($map)->join("xc_insurance ON xc_shopbidding.insurance_id=xc_insurance.id")->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show_admin();
		$list = $this->ShopbiddingModel->where($map)->join("xc_insurance ON xc_shopbidding.insurance_id=xc_insurance.id")->order("xc_shopbidding.create_time DESC")->limit($p->firstRow.','.$p->listRows)->field("xc_shopbidding.id,xc_shopbidding.insurance_id,xc_shopbidding.create_time,xc_insurance.user_name,xc_insurance.user_phone,xc_insurance.fsid,xc_insurance.insurance_name,xc_insurance.loss_price,xc_insurance.description,xc_insurance.insurance_status")->select();

		if($list){
			foreach($list as $k=>$v){
				$fs = $this->FsModel->where(array('fsid'=>$v['fsid']))->find();
				$list[$k]['fsname'] = $fs['fsname'];
				$bidorder = $this->BidorderModel->where(array('bid_id'=>$v['id'],'shop_id'=>$_REQUEST['shop_id']))->find();
				if($bidorder) {
					$list[$k]['is_order'] = 1;
					$list[$k]['bidorder_id'] = $bidorder['id'];
				}else {
					$list[$k]['is_order'] = 0;
				}
			}
		}

		if($_REQUEST['shop_id']) {
			$shop_info = $this->ShopModel->find($_REQUEST['shop_id']);
			$data['shop_id'] = $_REQUEST['shop_id'];
		}

		$this->assign('data',$data);
		$this->assign('shop_name',$shop_info['shop_name']);
		$this->assign('count',$count);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();


    }
    
}