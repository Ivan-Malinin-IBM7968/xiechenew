<?php
/*
 * 用户管理
 */
class LotteryAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->MemberModel = D('member');//用户表
		$this->OrderModel = D('order');//订单表
		$this->LotteryloginModel = D('lotterylogin');//活动页面访问表
		$this->MembersalecouponModel = D('membersalecoupon');//抵用券表
	}

	/*
		@author:chf
		@funtion:显示活动报表页
		@time:2013-10-15
	*/
	function index(){
		if($_REQUEST['create_start_time'] && $_REQUEST['create_end_time']){
			$new_arr = C('LOTTERY_FROM');
				foreach($new_arr as $k=>$v){
					$map['reg_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
					$map['fromstatus'] = $k;
					$map_lottery['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
					$map_lottery['from'] = $v;

					$order_state0['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
					
					$order_state1['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');

					
					$order_state2['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
					
					$order_state_1['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
					$sale['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
					$sale['from'] = $v;
					$order_state0['order_state'] = '0';
					$order_state1['order_state'] = '1';
					$order_state2['order_state'] = '2';
					$order_state_1['order_state'] = '-1';

					$data[$k]['Membercount'] = $this->MemberModel->where($map)->count();//用户注册
					$data[$k]['Lotterylogin_count'] = $this->LotteryloginModel->where($map_lottery)->count();//PV

					//$Order_state0 = $this->OrderModel->where($order_state0)->count();
					$Order_state0 = $this->OrderModel->where($order_state0)->select();
				
					if($Order_state0){
						foreach($Order_state0 as $kk=>$vv){
							$memberOrder_state0 = $this->MemberModel->where(array('uid'=>$vv['uid']))->find();
							if($k == $memberOrder_state0['fromstatus']){
								$data[$k]['Order_state0'] =	$data[$k]['Order_state0']+1;
							}
						}
						if(!$data[$k]['Order_state0']){
							$data[$k]['Order_state0'] =	0;
						}
					}else{
						$data[$k]['Order_state0'] =	0;
					}
					$Order_state1 = $this->OrderModel->where($order_state1)->select();
					if($Order_state1){
						foreach($Order_state1 as $kk=>$vv){
							$memberOrder_state1 = $this->MemberModel->where(array('uid'=>$vv['uid']))->find();
							if($k == $memberOrder_state1['fromstatus']){
								$data[$k]['Order_state1'] =	$data[$k]['Order_state1']+1;
							}
						}
						if(!$data[$k]['Order_state1']){
							$data[$k]['Order_state1'] =	0;
						}
					}else{
						$data[$k]['Order_state1'] =	0;
					}

					//$data['Order_state0'] = $this->OrderModel->where($order_state0)->count();
					
					//$data['Order_state1'] = $this->OrderModel->where($order_state1)->count();
					$Order_state2 = $this->OrderModel->where($order_state2)->select();
					if($Order_state2){
						foreach($Order_state2 as $kk=>$vv){
							$memberOrder_state2 = $this->MemberModel->where(array('uid'=>$vv['uid']))->find();
							if($k == $memberOrder_state2['fromstatus']){
								$data[$k]['Order_state2'] =	$data[$k]['Order_state2']+1;
							}
						}
						if(!$data[$k]['Order_state2']){
							$data[$k]['Order_state2'] =	0;
						}
					}else{
						$data[$k]['Order_state2'] =	0;
					}

					$Order_state_1 = $this->OrderModel->where($order_state_1)->select();
					if($Order_state_1){
						foreach($Order_state_1 as $kk=>$vv){
							$memberOrder_state2 = $this->MemberModel->where(array('uid'=>$vv['uid']))->find();
							if($k == $memberOrder_state2['fromstatus']){
								$data[$k]['Order_state_1'] = $data[$k]['Order_state_1']+1;
							}
						}
						if(!$data[$k]['Order_state_1']){
							$data[$k]['Order_state_1'] = 0;
						}
					}else{
						$data[$k]['Order_state_1'] = 0;
					}
				

					//$data['Order_state2'] = $this->OrderModel->where($order_state2)->count();
					//$data['Order_state_1'] = $this->OrderModel->where($order_state_1)->count();

					$data[$k]['from'] = $v;
					$data[$k]['sale_count'] = $this->MembersalecouponModel->where($sale)->count();
				
				
				}
				//print_r($data);
				$this->assign('create_start_time',$_REQUEST['create_start_time']);
				$this->assign('create_end_time',$_REQUEST['create_end_time']);
				$this->assign('data',$data);


			
		}
		$this->display();
	}


  

}
