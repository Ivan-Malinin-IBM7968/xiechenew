<?php
/*
 */

class BidformsAction extends CommonAction {
    function __construct() {
		parent::__construct();
		if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
		$this->BidorderModel = D('bidorder');//保险类订单表
		$this->ShopModel = D('shop');//店铺表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->BidCouponModel = D('bidcoupon');//保险类订单返利券表
		$this->BidorderjiesuanModel = D('bidorderjiesuan');//保险类订单返利券表
		$this->FsModel = D("Fs");
	 
	}



	/*
		@author:chf
		@function:显示保险类订单返利券页
		@time:2013-05-08
	*/
    public function index(){
		$Bidorder['shop_id'] = $map['shop_id'] = $_SESSION['shop_id'];
		$map['status'] = 1;
		if($_REQUEST['insurance_status']){
			$map['insurance_status'] = $_REQUEST['insurance_status'];
		}
		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];
			$map['xc_insurance.create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
		}
		
		$Bidorder['jiesuan_status'] = '0';
		//按竞价状态查询 链表查询
		$count = $this->BidorderModel->where($map)->join("xc_insurance on xc_insurance.id = xc_bidorder.insurance_id")->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 10);
		// 分页显示输出
		
		$page = $p->show_admin();
		foreach ($_REQUEST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		$data['alllist'] = $this->BidorderModel->where($map)->join("xc_insurance on xc_insurance.id = xc_bidorder.insurance_id")->limit($p->firstRow.','.$p->listRows)->select();
		

		if($data['alllist']){
			foreach($data['alllist'] as $k=>$v){
				$Bidoreder = $this->BidorderModel->where(array('insurance_id'=>$v['id']))->find();
				$data['alllist'][$k]['order_id'] = $this->get_orderid($Bidoreder['id']);
				$data['alllist'][$k]['car_name'] = $this->get_car_info($v['brand_id'],$v['series_id'],$v['model_id']);
				if($v['insurance_status'] == '1'){
					$data['alllist'][$k]['shopbid_status'] = '竞价中';
				}elseif($v['insurance_status'] == '2'){
					$data['alllist'][$k]['shopbid_status'] = '竞价结束';
				}elseif($v['insurance_status'] == '3'){
					$data['alllist'][$k]['shopbid_status'] = '竞价确认';
				}elseif($v['insurance_status'] == '4'){
					$data['alllist'][$k]['shopbid_status'] = '竞价完成';
				}
			}
		}


		$data['shopbidding_count'] = $this->ShopbiddingModel->where(array('shop_id'=>$_SESSION['shop_id'],'status'=>1))->count();//抢单总数
		
		$data['BidorderCount'] = count($data['alllist']);//订单次数
		$score = $this->BidorderjiesuanModel->where($Bidorder)->sum('loss_price');//4S店总费用
		$insurance_commission = $this->BidorderjiesuanModel->where($Bidorder)->sum('insurance_commission');//保险订单返回总金额
		$data['sumscore'] = $score - $insurance_commission;//可结算金额
		
		$this->assign('data',$data);
		$this->assign('map',$map);
		$this->assign('page',$page);
		$this->display();
    }
	
	/*
		@author:chf
		@function:显示保险类订单页商品具体订单页
		@time:2013-05-08
	*/
	function ordercount(){
		$data['shop_id'] = $map['shop_id'] = $_SESSION['shop_id'];
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
		$data['shop_id'] = $map['shop_id'] = $_SESSION['shop_id'];
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
		$Bid['shop_id'] = $_SESSION['shop_id'];
		$data['price_count'] = $this->BidCouponModel->where($Bid)->sum('price');

		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
	}

	/*
		@author:ysh
		@function:显示店铺参与的竞价列表
		@time:2013-11-5 
	*/
    public function shopcount(){
		if($_REQUEST['insurance_status']){
			$data['xc_insurance.insurance_status'] = $map['insurance_status'] = $_REQUEST['insurance_status'];
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
		$list = $this->ShopbiddingModel->where($map)->join("xc_insurance ON xc_shopbidding.insurance_id=xc_insurance.id")->order("xc_shopbidding.create_time DESC")->limit($p->firstRow.','.$p->listRows)->field("xc_shopbidding.id,xc_shopbidding.insurance_id,xc_shopbidding.create_time,xc_insurance.fsid,xc_insurance.insurance_name,xc_insurance.loss_price,xc_insurance.description,xc_insurance.insurance_status")->select();
		 
		if($list){
			foreach($list as $k=>$v){
				$list[$k]['user_phone'] = preg_replace("/(\d{3})(\d{4})/","$1****",$v['user_phone']);
				$fs = $this->FsModel->where(array('fsid'=>$v['fsid']))->find();
				$list[$k]['fsname'] = $fs['fsname'];
				$bidorder = $this->BidorderModel->where(array('bid_id'=>$v['id'],'shop_id'=>$_SESSION['shop_id']))->find();
				if($bidorder) {
					$list[$k]['is_order'] = 1;
					$list[$k]['bidorder_id'] = $bidorder['id'];
				}else {
					$list[$k]['is_order'] = 0;
				}
			}
		}

		$this->assign('data',$data);
		$this->assign('list',$list);
		$this->assign('page',$page);
		$this->display();
    }

    
}