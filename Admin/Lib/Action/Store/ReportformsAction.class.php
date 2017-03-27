<?php
class ReportformsAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->ShopModel = D('shop');//店铺表
		$this->OrderModel = D('order');//订单
		$this->MembercouponModel = D('membercoupon');//用户优惠卷信息
		$this->CouponModel = D('coupon');//优惠卷
		$this->MemberModel = D('member');//用户表
		$this->OrdernologinModel = D('ordernologin');//订单
		
	}
	

	/*	
		@author:chf
		@function:显示报表页
		@time:2013-10-11
	*/
	function index(){
		$data['ShopClass_1'] = $this->ShopModel->where(array('shop_class'=>1,'status'=>1))->select();//签约店铺
		$data['ShopClass_2'] = $this->ShopModel->where(array('shop_class'=>2,'status'=>1))->select();//非签约店铺
		if($_POST['real'] == 'real'){
			$all_map['order_des'] = array('neq','membernew');
			$bespoke_map['order_des'] = array('neq','membernew');
			$cancel_count['order_des'] = array('neq','membernew');
			$this->assign('real',$_POST['real']);
		}else{
			$this->assign('real','x');
		}
		if($_POST['start_time'] && $_POST['end_time']){
			$bespoke_map['create_time'] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'])));
			$all_map['complete_time'] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'])));
			$cancel_count['cancel_time'] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'])));
			$membercouponarr['pay_time'] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'])));
		}
		foreach($data['ShopClass_1'] as $k=>$v){
			$bespoke_map['shop_id'] = $v['id'];
			$data['ShopClass_1'][$k]['bespoke_count'] = $this->OrderModel->where($bespoke_map)->count();//当天预约总数
			$allbespoke_count+=$data['ShopClass_1'][$k]['bespoke_count'];//当天预约总数量
			$all_map['order_state'] = '2';
			$all_map['shop_id'] = $v['id'];
			$data['ShopClass_1'][$k]['all_count'] = $this->OrderModel->where($all_map)->count();//完成总数
			$all_count+=$data['ShopClass_1'][$k]['all_count'];//完成总数量
			
			$cancel_count['order_state'] = '-1';
			$cancel_count['shop_id'] = $v['id'];
			$data['ShopClass_1'][$k]['cancel_count'] = $this->OrderModel->where($cancel_count)->count();//取消总数

			$allcancel_count+=$data['ShopClass_1'][$k]['cancel_count'];//取消总数量
			$membercouponarr['shop_id'] = $v['id'];
			$membercouponarr['is_pay'] = 1;

			$data['ShopClass_1'][$k]['pay_count'] = $this->MembercouponModel->where($membercouponarr)->count();

			if($data['ShopClass_1'][$k]['bespoke_count'] || $data['ShopClass_1'][$k]['pay_count'] || $data['ShopClass_1'][$k]['all_count'] || $data['ShopClass_1'][$k]['cancel_count']){
				
				$membercoupon = $this->MembercouponModel->where($membercouponarr)->select();
				foreach($membercoupon as $kk=>$vv){
					$coupon	= $this->CouponModel->where(array('id'=>$vv['coupon_id']))->find();
					$pay_price = $coupon['coupon_amount']+$pay_price;
				}
				
				$arr['ShopClass_1'][$k]['shop_name'] = $v['shop_name'];
				$arr['ShopClass_1'][$k]['bespoke_count'] = $data['ShopClass_1'][$k]['bespoke_count'];//预约总数
				$arr['ShopClass_1'][$k]['all_count'] = $data['ShopClass_1'][$k]['all_count'];//完成总数
				$arr['ShopClass_1'][$k]['cancel_count'] = $data['ShopClass_1'][$k]['cancel_count'];//取消总数
				$arr['ShopClass_1'][$k]['pay_count'] = $data['ShopClass_1'][$k]['pay_count'];//取消总数
				$allpay_count+= $data['ShopClass_1'][$k]['pay_count'];//取消总数
				$arr['ShopClass_1'][$k]['pay_price'] = $pay_price;
				$pay_price ="";
				$order = $this->OrderModel->where($bespoke_map)->select();
				foreach($order as $order_k=>$order_v){
					$arr['ShopClass_1'][$k]['uid'] = $order_v['uid'].",".$arr['ShopClass_1'][$k]['uid'];
					
					$subuida[] = $order_v['uid'];
				}
				
				$order_cancel = $this->OrderModel->where($cancel_count)->select();
				foreach($order_cancel as $order_k=>$order_v){
					$arr['ShopClass_1'][$k]['order_id'] = $order_v['id'].",".$arr['ShopClass_1'][$k]['order_id'];
					$order_id1 = $order_v['id'].",".$order_id1;
				}
			}
		}		
			$uidarray = array_unique($subuida);
			foreach($uidarray as $v){
				$uid1 = $v.",".$uid1;
			}
			/*签约店铺来源匹配开始*/
			$member_map1['uid'] = array('in',$uidarray);
			$member_class1= $this->MemberModel->where($member_map1)->select();
			$count_class = 0;
			foreach($member_class1 as $k=>$v){
				foreach(C('MEMBER_FORM') as $kk=>$vv){
					if($v['fromstatus'] == $kk){
						$count_class++;
					}
				}
			}
			$this->assign('count_class',$count_class);
			/*签约店铺来源匹配结束*/
			/*签约店铺取消匹配开始*/
			$suborder_id = substr($order_id1,0,-1);
			$order_map1['id'] = array('in',$suborder_id);
			$order_map1['order_state'] = "-1";
			$order_class1 = $this->OrderModel->where($order_map1)->select();
			$order_count_class = 0;
			foreach($order_class1 as $k=>$v){
				foreach(C('CANCEL_STATE') as $kk=>$vv){
					if($v['cancel_state'] == $kk){
						$order_count_class++;
					}else{
						
					}
				}
			}
			$this->assign('order_count_class',$order_count_class);
			/*签约店铺取消匹配开始结束*/
			foreach($data['ShopClass_2'] as $k=>$v){
			
			$bespoke_map['shop_id'] = $v['id'];
			$data['ShopClass_2'][$k]['bespoke_count'] = $this->OrderModel->where($bespoke_map)->count();//该时间段预约总数
			$allbespoke_count2+=$data['ShopClass_2'][$k]['bespoke_count'];//当天预约总数量
			
			$all_map['order_state'] = 2;
			$all_map['shop_id'] = $v['id'];
			$data['ShopClass_2'][$k]['all_count'] = $this->OrderModel->where($all_map)->count();//完成总数

			$all_count2+=$data['ShopClass_2'][$k]['all_count'];//完成总数量

			$cancel_count['order_state'] = '-1';
			$cancel_count['shop_id'] = $v['id'];
			$data['ShopClass_2'][$k]['cancel_count'] = $this->OrderModel->where($cancel_count)->count();//取消总数

			$allcancel_count2+=$data['ShopClass_2'][$k]['cancel_count'];//取消总数量
			$membercouponarr2['shop_id'] = $v['id'];
			$membercouponarr2['is_pay'] = 1;
			$data['ShopClass_2'][$k]['pay_count'] = $this->MembercouponModel->where($membercouponarr2)->count();
			if($data['ShopClass_2'][$k]['bespoke_count'] || $data['ShopClass_2'][$k]['all_count'] || $data['ShopClass_2'][$k]['cancel_count']){
				
				$membercoupon2 = $this->MembercouponModel->where($membercouponarr2)->select();
				foreach($membercoupon2 as $kk=>$vv){
					$coupon	= $this->CouponModel->where(array('id'=>$vv['coupon_id']))->find();
					$pay_price2+=$coupon['coupon_amount'];
				}
				
				$arr['ShopClass_2'][$k]['shop_name'] = $v['shop_name'];
				$arr['ShopClass_2'][$k]['bespoke_count'] = $data['ShopClass_2'][$k]['bespoke_count'];//预约总数
				$arr['ShopClass_2'][$k]['all_count'] = $data['ShopClass_2'][$k]['all_count'];//完成总数
				$arr['ShopClass_2'][$k]['cancel_count'] = $data['ShopClass_2'][$k]['cancel_count'];//取消总数
				$arr['ShopClass_2'][$k]['pay_count'] = $data['ShopClass_1'][$k]['pay_count'];//取消总数
				$allpay_count2+= $data['ShopClass_2'][$k]['pay_count'];//取消总数
				$arr['ShopClass_2'][$k]['pay_price'] = $pay_price2;
				$pay_price2 ="";
				$order = $this->OrderModel->where($bespoke_map)->select();
				foreach($order as $order_k=>$order_v){
					$arr['ShopClass_2'][$k]['uid'] = $order_v['uid'].",".$arr['ShopClass_2'][$k]['uid'];
					$arr['ShopClass_2'][$k]['order_id'] = $order_v['id'].",".$arr['ShopClass_2'][$k]['order_id'];
					
					$subuida2[] = $order_v['uid'];	
				}

				$order_cancel1 = $this->OrderModel->where($cancel_count)->select();
				foreach($order_cancel1 as $order_k=>$order_v){
					$arr['ShopClass_1'][$k]['order_id'] = $order_v['id'].",".$arr['ShopClass_1'][$k]['order_id'];
					$order_id2 = $order_v['id'].",".$order_id2;

				}
			}
		}
			
			/*非签约店铺来源匹配开始*/
			$uidarray2 = array_unique($subuida2);
			foreach($uidarray2 as $v){
				$uid2 = $v.",".$uid2;
			}
			$member_map2['uid'] = array('in',$uidarray2);
			$member_class2= $this->MemberModel->where($member_map2)->select();
			$count_class2 = 0;
			foreach($member_class2 as $k=>$v){
				foreach(C('MEMBER_FORM') as $kk=>$vv){
					if($v['fromstatus'] == $kk){
						$count_class2++;
					}
				}
			}
			$this->assign('count_class2',$count_class2);
			/*非签约店铺来源匹配结束*/
			/*非签约店铺取消匹配开始*/
			$sub_orderid2 = substr($order_id2,0,-1);
			$order_map2['id'] = array('in',$sub_orderid2);
			
			$order_map2['order_state'] = "-1";
			$order_class2 = $this->OrderModel->where($order_map2)->select();
			$order_count_class2 = 0;
			foreach($order_class2 as $k=>$v){
				foreach(C('CANCEL_STATE') as $kk=>$vv){
					if($v['cancel_state'] == $kk){
						$order_count_class2++;
					}else{
						
					}
				}
			}
			$this->assign('order_count_class2',$order_count_class2);
			/*非签约店铺取消匹配开始结束*/
		$this->assign('arr',$arr);
		$this->assign('uid1',$uid1);
		$this->assign('order_id1',$order_id1);
		$this->assign('uid2',$uid2);
		$this->assign('order_id2',$order_id2);
		
		$this->assign('allbespoke_count',$allbespoke_count);
		$this->assign('all_count',$all_count);
		$this->assign('allcancel_count',$allcancel_count);
		$this->assign('allpay_count',$allpay_count);

		$this->assign('allbespoke_count2',$allbespoke_count2);
		$this->assign('all_count2',$all_count2);
		$this->assign('allcancel_count2',$allcancel_count2);
		$this->assign('allpay_count2',$allpay_count2);
			

		$this->assign('start_time',$_POST['start_time']);
		$this->assign('end_time',$_POST['end_time']);
		$this->display();
	}

	/*
		@author:chf
		@function:显示(订单数量)详情页
		@time:2013-10-11
	*/
	function detail(){
		$uid = substr($_REQUEST['uid'],0,-1);
		$data['uid'] = array('in',$uid);
		$member = $this->MemberModel->where($data)->select();
		
		//print_r(array_count_values($arr_uid));
		$count = 0;
		foreach($member as $k=>$v){
			
			foreach(C('MEMBER_FORM') as $kk=>$vv){
				if($v['fromstatus'] == $kk){
					$arr[$kk]+=1;
					$count++;
				}else{
					
				}
			}
		}
		for($a=1;$a<42;$a++){
			$new[]= $a;
		}

		$this->assign('arr',$arr);
		$this->assign('new',$new);
		$this->assign('count',$count);
		$this->display();
	}

	/*
		@author:chf
		@function:显示(订单数量)详情页
		@time:2013-10-11
	*/
	function logdetail(){
		$uid = substr($_REQUEST['uid'],0,-1);
		$data['uid'] = array('in',$uid);
		$member = $this->MemberModel->where($data)->select();
		foreach($member as $k=>$v){
			foreach(C('MEMBER_FORM') as $kk=>$vv){
			
				if($v['fromstatus'] == $kk){
					
					$arr[$kk]+=1;
				}else{
					
				}
			}
		}
		for($a=1;$a<41;$a++){
			$new[]= $a;
		}
		$this->assign('new',$new);
		$this->assign('arr',$arr);
		$this->display();
	}

	/*
		@author:chf
		@function:显示(订单数量)详情页
		@time:2013-10-11
	*/
	function canceldetail(){
		$order_id = substr($_REQUEST['order_id'],0,-1);
		$data['id'] = array('in',$order_id);
		$data['order_state'] = "-1";
		$order = $this->OrderModel->where($data)->select();
		
		foreach($order as $k=>$v){
			foreach(C('CANCEL_STATE') as $kk=>$vv){
				if($v['cancel_state'] == $kk){
					$arr[$kk]+=1;
				}else{
					
				}
			}
		}
		for($a=1;$a<10;$a++){
			$new[]= $a;
		}
		$this->assign('new',$new);
		$this->assign('arr',$arr);
		$this->assign('CANCEL_STATE',C('CANCEL_STATE'));
		$this->display();
	}
}
?>