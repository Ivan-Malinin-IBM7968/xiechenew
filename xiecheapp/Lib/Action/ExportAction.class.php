<?php
/*
*乱七八糟导数据 数学应用题！！
*搞伐动撒意思 只管写写写
@time 2014/3/25
*/
class ExportAction extends CommonAction
{
	function __construct() {
		parent::__construct();
		$this->OrderModel = D('Order');
		$this->MemberModel = D('Member');
		$this->MembercouponModel = D('Membercoupon');
		$this->ShopModel = D("Shop");
		$this->dianping_couponModel = D('Dianping_coupon');
		$this->CouponModel = D('Coupon');
	}

	/*
	*@2013年4月，截止到每个月最后一天的累计注册会员数量，当月新增会员数量，当月新增会员来源，当月新增会员在当月的预约数量（含团购）（分别按照pc,app端统计）
	*@author ysh
	*@time 2014/3/25
	*/
	function test1() {
		$MembercouponModel = D('Membercoupon');
		$fromstatus = C("MEMBER_FORM");

		echo "日期,累计注册会员数量,当月新增会员数量,当月新增会员在当月的预约数量,团购数量<br>";
		$month = 0;
		while($month<=11) {
			$start_date = mktime(0, 0, 0, date("4")+$month, date(1),   date("2013"));
			$month ++;
			$end_date = mktime(0, 0, 0, date("4")+$month, date(1),   date("2013"));
			
			$map = array();
			$map['reg_time']= array(array('gt',$start_date),array('lt',$end_date)) ;
			//$map['fromstatus'] = array('neq',30);
			$every_month = $this->MemberModel->where($map)->select();

			$map_total = array();
			$map_total['reg_time'] = array('lt',$end_date) ;
			//$map_total['fromstatus'] = array('neq',30);
			$total_member = $this->MemberModel->where($map_total)->count();
			
			foreach($every_month as $key=>$val) {
				$uids[] = $val['uid'];
			}
			$map_order = array();
			$map_order['create_time']= array(array('gt',$start_date),array('lt',$end_date)) ;
			$map_order['uid'] = array('in',$uids);
			$order_count = $this->OrderModel->where($map_order)->count();

			$map_order['is_app'] = 1;
			$order_count_app = $this->OrderModel->where($map_order)->count();

			$order_count_pc = $order_count-$order_count_app;
			
			$map_coupon = array();
			$map_coupon['uid'] = array('in',$uids);
			$map_coupon['create_time']= array(array('gt',$start_date),array('lt',$end_date)) ;
			$map_coupon['is_pay'] = 1;
			$membercoupon_count = $this->MembercouponModel->where($map_coupon)->count();
			unset($uids);
			echo date("Y-m-d",$start_date)."-".date("Y-m-d",$end_date).",".$total_member.",".count($every_month).",".$order_count."(pc端:".$order_count_pc." app端:".$order_count_app."),".$membercoupon_count."<br>";
		}
	}
	//题目2截至目前，对于注册的会员，按照单个会员的订单数量，如注册之后从未进行过预约，仅进行过1次预约（含团购）,仅进行过2次预约（含团购）,仅进行过3次预约（含团购的用户数量统计及其占目前会员总量的比例？

	function timu2(){
		$member = $this->MemberModel->select();
		$mcount = count($member);
		$couponcount = $this->MembercouponModel->where()->count();
		foreach($member as $k=>$v){
			$count = $this->OrderModel->where(array('uid'=>$v['uid']))->count();
			$member_count = $this->MembercouponModel->where(array('uid'=>$v['uid']))->count();
			if($member_count['order_id']){
				
				if($count == 1){
					$b+=1;//下定过1次
				}
				if($count == 2){
					$c+=1;//下定过2次
				}
				if($count >= 3){
					$d+=1;//下定过3次
				}
			}
			if($count == 0){
				$o+=1;//没下过订的用户
			}
			if($count == 1){
				$i+=1;//下定过1次
			}
			if($count == 2){
				$s+=1;//下定过2次
			}
			if($count >= 3){
				$p+=1;//下定过3次
			}
		}
		echo "0次下单".$o."  "."1次下单".$i."  "."2次下单".$s."  "."3次以及以上下单".$p."用户总数量".$mcount."<br>"; 
		
		echo  "1次买优惠券".$b."  "."2次买优惠券".$c."  "."3次以及以上买优惠券".$d."券总数量".$couponcount;
	
	
	
	}


	/*
	*@对于目前所有有记录的预约订单或团购订单，按照不同渠道来源进行的订单分类统计数据
	*@author ysh
	*@time 2014/3/25
	*/
	function test3() {
		$fromstatus_arr = C("MEMBER_FORM");
		
		/*
		//预约订单
		$Model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $Model->query("SELECT fromstatus,count(*) FROM xc_order LEFT JOIN xc_member ON xc_order.uid=xc_member.uid GROUP BY xc_member.fromstatus ORDER BY count(*)");
		
		echo "用户来源,订单数量<br>";

		foreach($result as $key=>$val) {
			$fromstatus = $fromstatus_arr[$val['fromstatus']];
			if(!$fromstatus) {
				$fromstatus = "不能确定来源";
			}
			echo $fromstatus.",".$val['count(*)']."<br>";
		}
		*/

		//团购券订单
		$Model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$result = $Model->query("SELECT fromstatus,count(*) FROM xc_membercoupon LEFT JOIN xc_member ON xc_membercoupon.uid=xc_member.uid WHERE xc_membercoupon.uid !='6591' AND xc_membercoupon.membercoupon_id != '150' GROUP BY xc_member.fromstatus ORDER BY count(*)");
		
		echo "用户来源,订单数量<br>";

		foreach($result as $key=>$val) {
			$fromstatus = $fromstatus_arr[$val['fromstatus']];
			if(!$fromstatus) {
				$fromstatus = "不能确定来源";
			}
			echo $fromstatus.",".$val['count(*)']."<br>";
		}

	}

	/*
	*@对于目前已经合作的所有4S店，按照各个店已经累计实现的预约（含团购）数量进行分类，如按照事先10次以上（含10次），5-10次含5次，1-5次含1次，0次的分类，分别统计截至目前实现不同预约次数的4s店的数量
	*@author ysh
	*@time 2014/3/25
	*/
	function test5() {
		$Model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$order_result = $Model->query("SELECT shop_id, count(*) FROM xc_order WHERE order_state >1 GROUP BY shop_id ORDER BY count(*) DESC");

		foreach($order_result as $key=>$val) {
			$shop_info = $this->ShopModel->find($val['shop_id']);
			
			//$result = array('ten','five','one','zero');
			if($val['count(*)'] >=10) {
				$result['ten'] .= $val['shop_id'].",".$shop_info['shop_name'].",".$val['count(*)']."<br>";
			}
			if($val['count(*)'] >=5 && $val['count(*)'] <10) {
				$result['five'] .= $val['shop_id'].",".$shop_info['shop_name'].",".$val['count(*)']."<br>";
			}
			if($val['count(*)'] >=1 && $val['count(*)'] <5) {
				$result['one'] .= $val['shop_id'].",".$shop_info['shop_name'].",".$val['count(*)']."<br>";
			}
			
		}

		$not_exists = $Model->query("SELECT * FROM xc_shop WHERE shop_class=1 AND status=1 AND shop_prov=3305 AND not exists (SELECT * FROM xc_order WHERE xc_shop.id=xc_order.shop_id)");
		foreach($not_exists as $key=>$val) {
			$result['zero'] .= $val['id'].",".$val['shop_name']."<br>";
		}
		
		
		//echo "10次以上（含10次）,,5-10次含5次,,1-5次含1次,,0次";
		print_r($result[one]);
	}

	function test2() {
		$Model = new Model(); // 实例化一个model对象 没有对应任何数据表
		$order_result = $Model->query("SELECT uid,count(*) FROM xc_membercoupon WHERE is_pay=1 group by uid");
		foreach($order_result as $key=>$val) {
			if($val['count(*)'] ==1) {
				$a++;
			}
			if($val['count(*)'] ==2) {
				$b++;
			}
			if($val['count(*)'] >=3) {
				$c++;
			}
		}
		echo $a.",".$b.",".$c;
	}
	

	function test_curl() {
		$data['touser'] = "oF49ruJukiRNno_6NJ4CEY6waiN4";
		$data['title'] = "这尼玛的我发出去的标题！！  oF49ruJukiRNno_6NJ4CEY6waiN4";
		$data['description'] = "这是我的微信号！！http://www.xieche.com.cn/mobile";
		$data['url'] = "http://www.xieche.com.cn/mobile";
		var_dump($this->weixin_api($data));

	}

	/*
	*@导出所有用户为上海大众的信息 	已下单的用户
	*@author ysh
	*@time 2014/5/14
	*/
	function get_useinfo_by_brand() {
		//已下单的用户----start
		$Model = new Model();
		$have_order = $Model->query("SELECT xc_order.id,xc_order.model_id,xc_order.uid,xc_order.truename,xc_order.mobile,xc_order.licenseplate,xc_order.shop_id,xc_order.order_state,xc_order.create_time FROM xc_order LEFT JOIN `xc_shop_fs_relation` ON xc_order.shop_id = xc_shop_fs_relation.shopid WHERE xc_shop_fs_relation.fsid =67 GROUP BY uid ");

		$shop_model  = D("shop");
		$ORDER_STATE = C("ORDER_STATE");
		foreach($have_order as $key=>$val) {
			if($val['model_id']) {
				$car_info = $this->get_car_info($val['model_id']);
			}else {
				$car_info = "未知";
			}
			$shop_info = $shop_model->find($val['shop_id']);
			$shop_name = $shop_info['shop_name'];
			$order_state = $ORDER_STATE[$val['order_state']];
			$val['create_time'] = date("Y-m-d H:i:s",$val['create_time'] );
			$str_table .= "<tr><td>{$val[id]}</td><td>{$car_info}</td><td>{$val[uid]}</td><td>{$val[truename]}</td><td>{$val[mobile]}</td><td>{$val[licenseplate]}</td><td>{$shop_name}</td><td>{$order_state}</td><td>{$val[create_time]}</td></tr>";
		}

		header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=已下单雪佛兰用户表.xls");
		$color = "#00CD34";
        $str = '<table><tr><td>订单号</td><td>车型车系</td><td>用户id</td><td>姓名</td><td>电话</td><td>车牌</td><td>4s店</td><td>预约状态</td><td>下定时间</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	
	}
	/*
	*@导出所有用户为上海大众的信息 	已下单的用户
	*@author ysh
	*@time 2014/5/14
	*/
	function get_useinfo_by_brand_no_order() {
		$Model = new Model();
		// AND (xc_membercar.series_id>=162 AND xc_membercar.series_id<=172 )
		$havent_order = $Model->query("SELECT * FROM xc_member LEFT JOIN xc_membercar ON xc_member.uid=xc_membercar.uid WHERE xc_membercar.brand_id=68 AND  not exists (SELECT * FROM xc_order WHERE xc_member.uid=xc_order.uid) ");
		
		foreach($havent_order as $key=>$val) {
			$car_info = $this->get_car_info($val['model_id']);
			$val['reg_time'] = date("Y-m-d H:i:s",$val['reg_time'] );
			$val[username] = str_replace(" ", "", $val[username]);
			$str_table .= "<tr><td>{$car_info}</td><td>{$val[uid]}</td><td>{$val[username]}</td><td>{$val[mobile]}</td><td>{$val[prov]}-{$val[city]}-{$val[area]}</td><td>{$val[reg_time]}</td></tr>";
		}

		header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=未下单雪佛兰用户表.xls");
		$color = "#00CD34";
        $str = '<table><tr><td>车型车系</td><td>用户id</td><td>用户名</td><td>电话</td><td>省区</td><td>注册时间</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;

	}

	function get_car_info($model_id) {
		$car_model = D("Carmodel");
		$carmodel = $car_model->find($model_id);

		$carseries_model = D("Carseries");
		$carseries = $carseries_model->find($carmodel['series_id']);

		$carbrand_model = D("Carbrand");
		$carbrand = $carbrand_model->find($carseries['brand_id']);
		return $carbrand['brand_name']." ".$carseries['series_name']." ".$carmodel['model_name'];
	}
	
	
	function get_bidorder() {
		$model_bidorder = D("Bidorder");
		$model_shop = D("Shop");
		$BIDORDER_STATE = C("BIDORDER_STATE");
		$map['order_status'] = array('neq','3');
		$bidorder = $model_bidorder->where($map)->order("shop_id")->select();
		if($bidorder) {
			foreach($bidorder as $key=>$val) {
				$shop_info = $model_shop->find($val['shop_id']);
				$order_status = $BIDORDER_STATE[$val[order_status]];
				$val['tostore_time'] = date("Y-m-d H:i:s",$val['tostore_time'] );
				if($val['complete_time'] ) {
					$val['complete_time'] = date("Y-m-d H:i:s",$val['complete_time'] );
				}

				$str_table .= "<tr><td>{$shop_info[shop_name]}</td><td>{$shop_info[shop_address]}</td><td>{$val[uid]}</td><td>{$val[truename]}</td><td>{$val[mobile]}</td><td>{$order_status}</td><td>{$val[tostore_time]}</td><td>{$val[complete_time]}</td></tr>";
			}
		}
		header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=事故车订单.xls");
        $str = '<table><tr><td>店铺</td><td>店铺地址</td><td>用户id</td><td>用户名</td><td>电话</td><td>订单状态</td><td>到店时间</td><td>完成时间</td></tr>';
        $str .= $str_table;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}

	/*大众 别克 雪佛兰 所有团购券的新跑一批验证码
	*	brand_id 大众 17 别克 11 雪佛兰 68
	*
	*/
	function run_coupon_code() {

		//$map['brand_id'] =  array('in','17,11,68');
		$map['id'] =  array('in','488,489,490,491,492,493,494,495,496,497,498,499,500,501,502,503,504,505,507,508,509,510,511,512,513,514,515,516,517,518,519,520,521');
		$map['is_delete'] = 0;
		$map['show_s_time'] = array('lt',time());
		$map['show_e_time'] = array('gt',time());
		$coupon_list = $this->CouponModel->where($map)->select();
		//print_r($coupon_list);exit;
		foreach($coupon_list as $key=>$val) {
			for($ii=0; $ii<20; $ii++) {
				$data['coupon_id'] = $val['id'];
				$data['coupon_code'] = $this->get_code();
				$data['create_time'] = time();
				$data['is_api'] = 0;
				$this->dianping_couponModel->add($data);
				//if($result){ echo $ii.'</br>';}
			}
			//exit;
		}
		echo "ok";
	}

	/*大众 别克 雪佛兰 所有团购券的新跑一批验证码
	*	brand_id 大众 17 别克 11 雪佛兰 68
	*
	*/
	function run_test() {
		
		//$map['brand_id'] =  array('in','17,11,68');
		
		$map['is_delete'] = 0;
		$map['coupon_across'] = array('gt','0');
		$map['show_s_time'] = array('lt',time());
		$map['show_e_time'] = array('gt',time());
		$coupon_list = $this->CouponModel->where($map)->select();

		foreach($coupon_list as $key=>$val) {
		  $a.= $val['id'].",";
		}
		echo $a;
	}

	function get_code() {
		$chars   =str_repeat('0123456789',3);
		$chars   =   str_shuffle($chars);

		$orderid     =   substr($chars,0,9);
		$sum = 0;
		for($ii=0;$ii<strlen($orderid);$ii++){
			$orderid = (string)$orderid;
			$sum += intval($orderid[$ii]);
		}
		$str = $sum%10;
		$result = $orderid.$str;
		return $result;
	}
}
?>