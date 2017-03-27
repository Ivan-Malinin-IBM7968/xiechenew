<?php
/*
 */
class BidorderAction extends CommonAction {
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
		$this->FsModel = D('fs');//品牌表
	}


	/*
		@author:chf
		@function:显示保险类订单页
		@time:2013-05-13
	*/
    public function index(){
		$map['shop_id'] = $_SESSION['shop_id'];

		if($_REQUEST['mobile']){
			$data['mobile'] = $map['mobile'] = $_REQUEST['mobile'];
		}
		if($_REQUEST['order_status']!=''){
			
			$data['order_status'] = $map['order_status'] = $_REQUEST['order_status'];
		}else{
			$data['order_status'] = $_REQUEST['order_status'];
		}
		
		if($_REQUEST['start_time'] && $_REQUEST['end_time']){
			$data['start_time'] = $_REQUEST['start_time'];
			$data['end_time'] = $_REQUEST['end_time'];
			$map['create_time'] = array( array('lt',strtotime($_REQUEST['end_time'])),array('gt',strtotime($_REQUEST['start_time']),"AND"));
		}
		
		$map['status'] = 1;
		// 计算总数
		$count = $this->BidorderModel->where($map)->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show_admin();
		$data['Bidorder'] = $this->BidorderModel->where($map)->order("create_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		if($data['Bidorder']){
			foreach($data['Bidorder'] as $k=>$v){
				$insurance_info = $this->InsuranceModel->find($v['insurance_id']);
				$data['Bidorder'][$k]['insurance_remark'] = $insurance_info['operator_remark'];
				$fs = $this->FsModel->where(array('fsid'=>$insurance_info['fsid']))->find();
				$data['Bidorder'][$k]['fsname'] = $fs['fsname'];

				$data['Bidorder'][$k]['order_id'] = $this->get_orderid($v['id']);
			}
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
    }
}