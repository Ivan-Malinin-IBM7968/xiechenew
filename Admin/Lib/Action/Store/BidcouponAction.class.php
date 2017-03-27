<?php
/*
 */
class BidcouponAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->BidorderModel = D('bidorder');//保险类订单表
		$this->ShopModel = D('shop');//店铺表
		$this->ShopbiddingModel = D('shopbidding');//4S店铺竞价表
		$this->InsuranceModel = D('insurance');//用户保险竞价表
		$this->BidCouponModel = D('bidcoupon');//保险类订单返利券表
		$this->MemberModel = D('member');//用户表
	}

	/*
		@author:chf
		@function:显示保险类订单返利券页
		@time:2013-05-08
	*/
    public function index(){
		
		if($_REQUEST['username']){
			$data['member'] = $member['username'] = array('LIKE',"%".$_REQUEST['username']."%");
			$memberlist = $this->MemberModel->where($member)->select();
			if($memberlist){
				foreach($memberlist as $k=>$v){
					$uid.= $v['uid'].",";
				}
			}
			$uid = substr($uid,0,-1);
			$map['uid'] = array('IN',$uid);
		}
		
		//$map['status'] = 1;
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
				$member = $this->MemberModel->where(array('uid'=>$v['uid']))->find();
				$data['Bidcoupon'][$k]['username'] = $member['username'];
				$data['Bidcoupon'][$k]['mobile'] = $member['mobile'];
			}
		
		}
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
    }
	
	
    
}