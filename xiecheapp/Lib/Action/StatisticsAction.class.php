<?php
class StatisticsAction extends CommonAction {
    function __construct() {
		parent::__construct();
	}

	/*
		@author:wwy
		@function:脚本(统计每日新增数据)
		@time:2014-04-2
	*/

	function daily_new(){
		$model_daily_new = D('Daily_new');

		$time = $model_daily_new->where($time)->order('create_time desc')->field('create_time')->find();
		
		if(!$time){
			$start_time = strtotime(date('Y-m-d',time()).'0:00:00')-86400;//脚本运行前一天的零点
			$end_time = strtotime(date('Y-m-d',time()).'0:00:00');//脚本运行当天的零点
		}else{
			$start_time = $time['create_time']+86400;
			$end_time = $start_time+86400;
		}

		//公共部分
		$model_shop = D('Shop');
		$shop['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$data['shop_num'] = $model_shop->where($shop)->count();
		echo $model_shop->getLastsql();
		echo "</br>";
		$model_coupon = D('Coupon');
		$map_onshelf['start_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$map_onshelf['coupon_type'] = 1; 
		$data['onshelf_cashcoupon'] = $model_coupon->where($map_onshelf)->count();
		$map_onshelf['coupon_type'] = 2; 
		$data['onshelf_groupon'] = $model_coupon->where($map_onshelf)->count();
		$data['create_time'] = $start_time;

		//线上推广
		$from_arr='1,2,22,23,32';//1.百度、谷歌2.论坛、微博22.lmm23.微博32.百度SEO
		$data['origin_id']='1';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//朋友介绍
		$from_arr='3';//3.朋友介绍
		$data['origin_id']='2';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//手机客户端
		$from_arr='4,31,36';//4.APP31.微信36.携车微信
		$data['origin_id']='3';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//平安
		$from_arr='33,34,37';//33.平安好车34.平安WAP37.平安100礼品券
		$data['origin_id']='4';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//客服
		$from_arr='5,6';//5.客服电话6.老用户
		$data['origin_id']='5';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//短信群发
		$from_arr='9,10,11,12,13,14,15,16,17,18,19,20,7,8';//9.短信推广10.vw11.svw12.别克13.雪弗莱14.斯柯达15.一汽丰田16.东风日产17.起亚18.优惠19.th20.sh7.宣传单8.其他
		$data['origin_id']='6';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//线下活动
		$from_arr='21,24,25,26,27,28,29';//21.都市港湾小区24.彩生活25.出租车广告26.传单27.ch28.fby29.小区
		$data['origin_id']='7';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//买名单短信
		$from_arr='30';
		$data['origin_id']='8';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//事故车代下单
		$from_arr='35';
		$data['origin_id']='9';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//无来源
		$from_arr='';
		$data['origin_id']='10';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);

		//无用户
		$data['origin_id']='11';
		$from_arr='is_null';
		$data['order_num']=$this->get_order($from_arr,$start_time,$end_time);
		$data['member_num']=$this->get_member($from_arr,$start_time,$end_time);
		$data['sold_cashcoupon']=$this->get_sold(1,$from_arr,$start_time,$end_time);
		$data['sum_cashcoupon']=$this->get_sum(1,$from_arr,$start_time,$end_time);
		$data['sold_groupon']=$this->get_sold(2,$from_arr,$start_time,$end_time);
		$data['sum_groupon']=$this->get_sum(2,$from_arr,$start_time,$end_time);
		print_r($data);
		$model_daily_new->add($data);
		echo "</br>OK";
		
	}
	
	function get_order($from_arr,$start_time,$end_time){
		$model_order = D('Order');
		$order['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		if($from_arr=='is_null'){
			$order['xc_member.uid']=array('exp','is NULL');
		}else{
			$order['xc_member.fromstatus'] = array('in',$from_arr);
		}
		$order['xc_order.create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$result = $model_order->join('xc_member ON xc_order.uid = xc_member.uid')->where($order)->count();
		echo $model_order->getLastsql();
		echo "</br>";
		return $result;
	}

	function get_member($from_arr,$start_time,$end_time){
		$model_member = D('Member');
		$member['reg_time'] = array(array('gt',$start_time),array('lt',$end_time));
		if($from_arr=='is_null'){
			$member['xc_member.uid']=array('exp','is NULL');
		}else{
			$member['xc_member.fromstatus'] = array('in',$from_arr);
		}
		$result = $model_member->where($member)->count();
		echo $model_member->getLastsql();
		echo "</br>";
		return $result;
	}

	function get_sold($coupon_type,$from_arr,$start_time,$end_time){
		$model_membercoupon = D('Membercoupon');
		if($from_arr=='is_null'){
			$sold['xc_member.uid']=array('exp','is NULL');
		}else{
			$sold['xc_member.fromstatus'] = array('in',$from_arr);
		}
		$sold['xc_membercoupon.coupon_type'] = $coupon_type;
		$sold['xc_membercoupon.is_pay'] = 1;
		$sold['xc_membercoupon.create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$result = $model_membercoupon->join('xc_member ON xc_membercoupon.uid = xc_member.uid')->where($sold)->count();
		echo $model_membercoupon->getLastsql();
		echo "</br>";
		return $result;
	}

	function get_sum($coupon_type,$from_arr,$start_time,$end_time){
		$model_membercoupon = D('Membercoupon');
		if($from_arr=='is_null'){
			$member_fromstatus['xc_member.uid']=array('exp','is NULL');
		}else{
			$member_fromstatus['xc_member.fromstatus'] = array('in',$from_arr);
		}
		$member_fromstatus['xc_membercoupon.coupon_type'] = $coupon_type;
		$member_fromstatus['xc_membercoupon.is_pay'] = 1;
		$member_fromstatus['xc_membercoupon.create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$member_info = $model_membercoupon->join('xc_member ON xc_membercoupon.uid = xc_member.uid')->where($member_fromstatus)->select();
		echo $model_membercoupon->getLastsql();
		print_r($member_info);
		if($member_info){
			foreach($member_info as $key=>$value){
				$in[]=$value['membercoupon_id'];
			}
			$in=implode(",",$in);
			echo 'inininin='.$in.'</br>';
			//$in=substr($in,3,-1);
			$count['xc_membercoupon.membercoupon_id'] = array('in',$in);
			$result_fromstatus = $model_membercoupon->join('xc_coupon ON xc_membercoupon.coupon_id = xc_coupon.id')->where($count)->field(array('xc_membercoupon.coupon_amount'=>'a','xc_coupon.coupon_amount'=>'b'))->select();
			print_r($result_fromstatus);
			if($result_fromstatus){
				foreach($result_fromstatus as $k=>$v){
					if($v['a']){
						$sum = $sum+$v['a'];
					}else{
						$sum = $sum+$v['b'];
					}
				}
			}
		}else{ $sum = 0; }
		echo $model_membercoupon->getLastsql();
		echo "</br>";
		return $sum;
	}
	

	/*
		@author:wwy
		@function:脚本(统计每日销售数据)
		@time:2014-05-19
	*/

	function daily_new_sales(){

		$model_daily_new_sales = D('Daily_new_sales');

		$time = $model_daily_new_sales->where($time)->order('create_time desc')->field('create_time')->find();
		
		if(!$time){
			$start_time = strtotime(date('Y-m-d',time()).'0:00:00')-86400;//脚本运行前一天的零点
			$end_time = strtotime(date('Y-m-d',time()).'0:00:00');//脚本运行当天的零点
		}else{
			$start_time = $time['create_time']+86400;
			$end_time = $start_time+86400;
		}
		
		$model_shop = D('Shop');
		$sales_id = $model_shop->where('user_id!=0')->Distinct(true)->field('user_id')->select();
		print_r($sales_id);
		//$model_user = M('tp_admin.user','xc_');
		//$user_name = $model_user->where($data)->field('nickname')->find();
		foreach($sales_id as $k=>$v){
			$data['shop_num']= $this->get_shop($v['user_id'],$start_time,$end_time);
			$data['order_num']= $this->get_order_sales($v['user_id'],$start_time,$end_time);
			$data['sold_cashcoupon']= $this->get_sold_sales(1,$v['user_id'],$start_time,$end_time);
			$data['sum_cashcoupon']=$this->get_sum_sales(1,$v['user_id'],$start_time,$end_time);
			$data['sold_groupon']= $this->get_sold_sales(2,$v['user_id'],$start_time,$end_time);
			$data['sum_groupon']=$this->get_sum_sales(2,$v['user_id'],$start_time,$end_time);
			$data['onshelf_cashcoupon']=$this->get_onshelf_sales(1,$v['user_id'],$start_time,$end_time);
			$data['onshelf_groupon']=$this->get_onshelf_sales(2,$v['user_id'],$start_time,$end_time);
			$data['sales_id']=$v['user_id'];
			$data['create_time'] = $start_time;
			print_r($data);
			$model_daily_new_sales->add($data);
			echo $model_daily_new_sales->getLastsql();
		}

		echo "</br>OK";
	}
	//销售相关新增店铺数
	function get_shop($user_id,$start_time,$end_time){
		$model_shop = D('Shop');
		$data['user_id'] = $user_id;
		$data['create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$result = $model_shop->where($data)->count();
		echo $model_shop->getLastsql();
		echo "</br>";
		return $result;
	}
	//销售相关新增订单数
	function get_order_sales($user_id,$start_time,$end_time){
		$model_order = D('Order');
		$order['xc_shop.user_id'] = $user_id;
		$order['xc_order.create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$result = $model_order->join('xc_shop ON xc_order.shop_id = xc_shop.id')->where($order)->count();
		echo $model_order->getLastsql();
		echo "</br>";
		return $result;
	}
	//销售相关售出现金券数,售出团购券数
	function get_sold_sales($coupon_type,$user_id,$start_time,$end_time){
		$model_membercoupon = D('Membercoupon');
		$sold['xc_shop.user_id'] = $user_id;
		$sold['xc_membercoupon.coupon_type'] = $coupon_type;
		$sold['xc_membercoupon.is_pay'] = 1;
		$sold['xc_membercoupon.create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$result = $model_membercoupon->join('xc_shop ON xc_membercoupon.shop_id = xc_shop.id')->where($sold)->count();
		echo $model_membercoupon->getLastsql();
		echo "</br>";
		return $result;
	}
	//销售相关售出现金券
	function get_sum_sales($coupon_type,$user_id,$start_time,$end_time){
		$model_membercoupon = D('Membercoupon');
		$member_fromstatus['xc_shop.user_id'] = $user_id;
		$member_fromstatus['xc_membercoupon.coupon_type'] = $coupon_type;
		$member_fromstatus['xc_membercoupon.is_pay'] = 1;
		$member_fromstatus['xc_membercoupon.create_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$member_info = $model_membercoupon->join('xc_shop ON xc_membercoupon.shop_id = xc_shop.id')->where($member_fromstatus)->select();
		echo $model_membercoupon->getLastsql();
		print_r($member_info);
		if($member_info){
			foreach($member_info as $key=>$value){
				$in[]=$value['membercoupon_id'];
			}
			$in=implode(",",$in);
			echo 'inininin='.$in.'</br>';
			//$in=substr($in,3,-1);
			$count['xc_membercoupon.membercoupon_id'] = array('in',$in);
			$result_fromstatus = $model_membercoupon->join('xc_coupon ON xc_membercoupon.coupon_id = xc_coupon.id')->where($count)->field(array('xc_membercoupon.coupon_amount'=>'a','xc_coupon.coupon_amount'=>'b'))->select();
			print_r($result_fromstatus);
			if($result_fromstatus){
				foreach($result_fromstatus as $k=>$v){
					if($v['a']){
						$sum = $sum+$v['a'];
					}else{
						$sum = $sum+$v['b'];
					}
				}
			}
		}else{ $sum = 0; }
		echo $model_membercoupon->getLastsql();
		echo "</br>";
		return $sum;
	}
	//销售相关上架现金券，团购券
	function get_onshelf_sales($coupon_type,$user_id,$start_time,$end_time){
		$model_coupon = D('Coupon');
		$map_onshelf['xc_shop.user_id'] = $user_id;
		$map_onshelf['xc_coupon.start_time'] = array(array('gt',$start_time),array('lt',$end_time));
		$map_onshelf['xc_coupon.coupon_type'] = $coupon_type; 
		$result = $model_coupon->join('xc_shop ON xc_coupon.shop_id = xc_shop.id')->where($map_onshelf)->count();
		echo $model_coupon->getLastsql();
		echo "</br>";
		return $result;
	}

}

?>