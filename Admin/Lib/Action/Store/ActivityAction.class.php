<?php
/*
 * 用户管理
 */
class ActivityAction extends CommonAction {
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
			$start_time = strtotime($_REQUEST['create_start_time']);
			$end_time =  strtotime($_REQUEST['create_end_time']);
			if(($end_time - $start_time) /86400 == '1'){
				$map['reg_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
				$map_lottery['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
				
				$order_state0['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
				
				$order_state1['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');

				
				$order_state2['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
				
				$order_state_1['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
				$sale['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');

				$order_state0['order_state'] = '0';
				$order_state1['order_state'] = '1';
				$order_state2['order_state'] = '2';
				$order_state_1['order_state'] = '-1';
				$data['Membercount'] = $this->MemberModel->where($map)->count();//用户注册
				$data['Lotterylogin_count'] = $this->LotteryloginModel->where($map_lottery)->count();//PV
				$data['Order_state0'] = $this->OrderModel->where($order_state0)->count();
				
				$data['Order_state1'] = $this->OrderModel->where($order_state1)->count();
				$data['Order_state2'] = $this->OrderModel->where($order_state2)->count();
				$data['Order_state_1'] = $this->OrderModel->where($order_state_1)->count();
				$data['sale_count'] = $this->MembersalecouponModel->where($sale)->count();
				$this->assign('data',$data);
			}else{
				$count = ($end_time - $start_time) /86400;
				for($a=0;$a<$count;$a++){
					$sql_start_time = $start_time+86400*$a;
					$end_time = $sql_start_time+86400;
					
					$map['reg_time'] = array(array('lt',$end_time),array('gt',$sql_start_time),'AND');
					$map_lottery['create_time'] = array(array('lt',$end_time),array('gt',$sql_start_time),'AND');
					$new_array[$a]['start_time'] = $sql_start_time;
					$new_array[$a]['end_time'] = $end_time-1;
					$order_state0['create_time']  = array(array('lt',$end_time),array('gt',$sql_start_time),'AND');
					
					$order_state1['create_time'] = array(array('lt',$end_time),array('gt',$sql_start_time),'AND');

					$order_state2['create_time'] = array(array('lt',$end_time),array('gt',$sql_start_time),'AND');
					
					$order_state_1['create_time']  = array(array('lt',$end_time),array('gt',$sql_start_time),'AND');
					$sale['create_time']  = array(array('lt',$end_time),array('gt',$sql_start_time),'AND');

					$order_state0['order_state'] = '0';
					$order_state1['order_state'] = '1';
					$order_state2['order_state'] = '2';
					$order_state_1['order_state'] = '-1';

					$new_array[$a]['Membercount'] = $this->MemberModel->where($map)->count();//用户注册
				
					$new_array[$a]['Lotterylogin_count'] = $this->LotteryloginModel->where($map_lottery)->count();//PV
					$new_array[$a]['Order_state0'] = $this->OrderModel->where($order_state0)->count();
					
					$new_array[$a]['Order_state1'] = $this->OrderModel->where($order_state1)->count();//预约
					$count1+=$new_array[$a]['Order_state1']; 
					$new_array[$a]['Order_state2'] = $this->OrderModel->where($order_state2)->count();//确认
					$count2+=$new_array[$a]['Order_state2']; 
					$new_array[$a]['Order_state_1'] = $this->OrderModel->where($order_state_1)->count();//取消
					$count_1+=$new_array[$a]['Order_state_1']; 
					$new_array[$a]['sale_count'] = $this->MembersalecouponModel->where($sale)->count();//取消
				
					
				}
				//echo $count_1+$count2+$count1;
				$this->assign('new_array',$new_array);	
			}
			if($data){
				$this->assign('count','1');	
			}
			$this->assign('create_start_time',$_REQUEST['create_start_time']);	
			$this->assign('create_end_time',$_REQUEST['create_end_time']);	
			
		}
		
		
		
		$this->display();
	}


  

}
