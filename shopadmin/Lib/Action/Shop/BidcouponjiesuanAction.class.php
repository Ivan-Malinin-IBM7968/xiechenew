<?php
/*
	@author:ysh
	@function:保险类优惠券结算
	@time:2013/7/25
*/
class BidcouponjiesuanAction extends CommonAction {
    function __construct() {
		parent::__construct();
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
	}
	
	//默认显示商家待确认状态的 结算列表
    public function index(){
        $shop_id = $_SESSION['shop_id'];
		$jiesuan_status = isset($_REQUEST['jiesuan_status'] )?$_REQUEST['jiesuan_status']:1;//默认显示商家待确认状态的 结算列表
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
		$month_type = $_REQUEST['month_type'];//last_month 上月未结算 this_month 本月未结算		

		$model_bidcouponjiesuan = D(GROUP_NAME.'/Bidcouponjiesuan');
        $model_bidcoupon = D(GROUP_NAME.'/Bidcoupon');
		$model_bidorder = D(GROUP_NAME.'/Bidorder');
		$model_member = D(GROUP_NAME.'/Member');

		$map_mc['shop_id'] = $shop_id;
		//$map_mc['jiesuan_status'] = 1;
		$true_jiesuan_url = "__URL__/true_jiesuan/";
		if($jiesuan_status) {
			$map_mc['jiesuan_status'] = $jiesuan_status;
			$this->assign("jiesuan_status",$jiesuan_status);
			$true_jiesuan_url .= "jiesuan_status/".$jiesuan_status."/";
		}
		if($start_time) {
			$map_mc['addtime'] = array('egt',strtotime($start_time));
			$this->assign("start_time",$start_time);
			$true_jiesuan_url .= "start_time/".$start_time."/";
		}
		if($end_time) {
			$map_mc['addtime'] = array('elt',strtotime($end_time));
			$this->assign("end_time",$end_time);
			$true_jiesuan_url .= "end_time/".$end_time."/";
		}
		if($start_time && $end_time) {
			$map_mc['addtime'] = array(array('egt',strtotime($start_time)),array('elt',strtotime($end_time)));
			$this->assign("start_time",$start_time);
			$this->assign("end_time",$end_time);
		}
		if($month_type == 'last_month') {
			$last_month = $this->lastMonth(time());
			$map_mc['addtime'] = array(array('egt',$last_month[0]),array('elt',$last_month[1]));
			$this->assign("start_time",date("Y-m-d" , $last_month[0] ));
			$this->assign("end_time",date("Y-m-d" , $last_month[1] ));
			$true_jiesuan_url .= "month_type/".$month_type."/";
		}
		if($month_type == 'this_month') {
			$last_month = $this->thisMonth(time());
			$map_mc['addtime'] = array(array('egt',$last_month[0]),array('elt',$last_month[1]));
			$this->assign("start_time",date("Y-m-d" , $last_month[0] ));
			$this->assign("end_time",date("Y-m-d" , $last_month[1] ));
			$true_jiesuan_url .= "month_type/".$month_type."/";
		}
		$this->assign("true_jiesuan_url",$true_jiesuan_url);
		
		$jiesuan_status_str = C('COUPON_JIESUAN_STATUS');
		/* 计算总数
		$count = $model_bidcouponjiesuan->where($map_mc)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 10);
		// 分页显示输出
		$page = $p->show_admin();
		*/
				
		//套餐券
		$couponjiesuan = $model_bidcouponjiesuan->where($map_mc)->order("id DESC")->select();		
		
		if($couponjiesuan) {
			foreach($couponjiesuan as $key=>$val) {
				$bidcoupon = $model_bidcoupon->where(array('id'=>$val['bidcoupon_id']))->find();
				$bidorder = $model_bidorder->find( $bidcoupon['bidorder_id'] );

				$couponjiesuan[$key]['bidorder_id'] = $bidorder['insurance_id'];
				$couponjiesuan[$key]['price'] = $bidcoupon['price'];
				$couponjiesuan[$key]['use_time'] = $bidcoupon['use_time'];
				$couponjiesuan[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];

				$member_info = $model_member->find($val['uid']);
				$couponjiesuan[$key]['mobile'] = $member_info['mobile'];
				$couponjiesuan[$key]['user_name'] = $member_info['username'];

				$jiesuan_amount += $val['coupon_amount'];//结算金额
				$jiesuan_commission += $val['commission'];//佣金
			}
			$true_pay = round($jiesuan_amount - $jiesuan_commission,5);//实际支付
		}


		$this->assign('couponjiesuan',$couponjiesuan);

		$this->assign('jiesuan_amount',$jiesuan_amount);
		$this->assign('jiesuan_commission',$jiesuan_commission);
		$this->assign('true_pay',$true_pay);

		//$this->assign('page',$page);
        
        $this->display();
    }
	
	/**
	 * @function:确认结算信息
	 *	@author:ysh
	 * @return array 返回列表是后台提交的结算列表 商家用于确认的二次显示页面
	 * @time:2013/7/16
	*/
	function true_jiesuan() {
        $shop_id = $_SESSION['shop_id'];
		$jiesuan_status = isset($_REQUEST['jiesuan_status'] )?$_REQUEST['jiesuan_status']:1;//默认显示商家待确认状态的 结算列表
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
		$month_type = $_REQUEST['month_type'];//last_month 上月未结算 this_month 本月未结算

		$this->assign("month_type",$month_type);

		$model_bidcouponjiesuan = D(GROUP_NAME.'/Bidcouponjiesuan');
        $model_bidcoupon = D(GROUP_NAME.'/Bidcoupon');
		$model_bidorder = D(GROUP_NAME.'/Bidorder');
		$model_member = D(GROUP_NAME.'/Member');

		$map_mc['shop_id'] = $shop_id;
		$update_jiesuan_url = "__URL__/update_jiesuan/";

		if($jiesuan_status) {
			$map_mc['jiesuan_status'] = $jiesuan_status;
			$this->assign("jiesuan_status",$jiesuan_status);
			$update_jiesuan_url .= "jiesuan_status/".$jiesuan_status."/";
		}
		if($start_time) {
			$map_mc['addtime'] = array('egt',strtotime($start_time));
			$this->assign("start_time",$start_time);
			$update_jiesuan_url .= "start_time/".$start_time."/";
		}
		if($end_time) {
			$map_mc['addtime'] = array('elt',strtotime($end_time));
			$this->assign("end_time",$end_time);
			$update_jiesuan_url .= "end_time/".$end_time."/";
		}
		if($start_time && $end_time) {
			$map_mc['addtime'] = array(array('egt',strtotime($start_time)),array('elt',strtotime($end_time)));
			$this->assign("start_time",$start_time);
			$this->assign("end_time",$end_time);
		}
		if($month_type == 'last_month') {
			$last_month = $this->lastMonth(time());
			$map_mc['addtime'] = array(array('egt',$last_month[0]),array('elt',$last_month[1]));
			$this->assign("start_time",date("Y-m-d" , $last_month[0] ));
			$this->assign("end_time",date("Y-m-d" , $last_month[1] ));
			$update_jiesuan_url .= "month_type/".$month_type."/";
		}
		if($month_type == 'this_month') {
			$last_month = $this->thisMonth(time());
			$map_mc['addtime'] = array(array('egt',$last_month[0]),array('elt',$last_month[1]));
			$this->assign("start_time",date("Y-m-d" , $last_month[0] ));
			$this->assign("end_time",date("Y-m-d" , $last_month[1] ));
			$update_jiesuan_url .= "month_type/".$month_type."/";
		}
		$this->assign("update_jiesuan_url",$update_jiesuan_url);
				
		$jiesuan_status_str = C('COUPON_JIESUAN_STATUS');


		//套餐券
		$couponjiesuan = $model_bidcouponjiesuan->where($map_mc)->order("id DESC")->select();		
		
		if($couponjiesuan) {
			foreach($couponjiesuan as $key=>$val) {
				$bidcoupon = $model_bidcoupon->where(array('id'=>$val['bidcoupon_id']))->find();
				$bidorder = $model_bidorder->find( $bidcoupon['bidorder_id'] );

				$couponjiesuan[$key]['bidorder_id'] = $bidorder['insurance_id'];
				$couponjiesuan[$key]['price'] = $bidcoupon['price'];
				$couponjiesuan[$key]['use_time'] = $bidcoupon['use_time'];
				$couponjiesuan[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];

				$member_info = $model_member->find($val['uid']);
				$couponjiesuan[$key]['mobile'] = $member_info['mobile'];
				$couponjiesuan[$key]['user_name'] = $member_info['username'];

				$jiesuan_amount += $val['coupon_amount'];//结算金额
				$jiesuan_commission += $val['commission'];//佣金
			}
			$true_pay = round($jiesuan_amount - $jiesuan_commission,5);//实际支付
		}

		$this->assign('couponjiesuan',$couponjiesuan);

		$this->assign('jiesuan_amount',$jiesuan_amount);
		$this->assign('jiesuan_commission',$jiesuan_commission);
		$this->assign('true_pay',$true_pay);

		$this->display();
	}

	/**
	 * @function:修改结算状态
	 *	@author:ysh
	 * @time:2013/7/16
	*/
	function update_jiesuan() {
		$shop_id = $_SESSION['shop_id'];
		$jiesuan_status = isset($_REQUEST['jiesuan_status'] )?$_REQUEST['jiesuan_status']:1;//默认显示商家待确认状态的 结算列表
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
		$month_type = $_REQUEST['month_type'];//last_month 上月未结算 this_month 本月未结算

		$model_bidcouponjiesuan = D(GROUP_NAME.'/Bidcouponjiesuan');
        $model_bidcoupon = D(GROUP_NAME.'/Bidcoupon');
		$model_member = D(GROUP_NAME.'/Member');
		$model_bidcouponjiesuanlog = D(GROUP_NAME."/Bidcouponjiesuanlog");

		$map_mc['shop_id'] = $shop_id;

		if($jiesuan_status) {
			$map_mc['jiesuan_status'] = $jiesuan_status;
		}
		if($start_time) {
			$map_mc['addtime'] = array('egt',strtotime($start_time));
		}
		if($end_time) {
			$map_mc['addtime'] = array('elt',strtotime($end_time));
		}
		if($start_time && $end_time) {
			$map_mc['addtime'] = array(array('egt',strtotime($start_time)),array('elt',strtotime($end_time)));
		}
		if($month_type == 'last_month') {
			$last_month = $this->lastMonth(time());
			$map_mc['addtime'] = array(array('egt',$last_month[0]),array('elt',$last_month[1]));
		}
		if($month_type == 'this_month') {
			$last_month = $this->thisMonth(time());
			$map_mc['addtime'] = array(array('egt',$last_month[0]),array('elt',$last_month[1]));
		}
		
		//现金券+套餐券
		//$map_mc['coupon_type'] = 1;
		
		$jiesuan_info = $model_bidcouponjiesuan->where($map_mc)->order("id DESC")->select();
		if($jiesuan_info) {
			foreach($jiesuan_info as $key=>$val) {

				$model_bidcouponjiesuanlog->add(array('bidcoupon_jiesuan_id'=>$val['id'],'name'=>$_SESSION['loginUserName'],'log'=>'商家确认结算将订单状态改为商家确认','addtime'=>time()));
			}
		}
		$data['jiesuan_status'] = 2;
		$model_bidcouponjiesuan->where($map_mc)->save($data);

		$this->success('申请结算成功！',U('/Shop/Bidcouponjiesuan/index/jiesuan_status/2'));
	}
	

	/**
	 * 获取上个月的开始和结束
	 * @param int $ts 时间戳
	 * @return array 第一个元素为开始日期，第二个元素为结束日期
	*/
	function lastMonth($ts) {
		$ts = intval($ts);
	 
		$oneMonthAgo = mktime(0, 0, 0, date('n', $ts) - 1, 1, date('Y', $ts));
		$oneMonthNow = mktime(0, 0, 0, date('n', $ts) , 1, date('Y', $ts));
		return array(
			$oneMonthAgo,
			$oneMonthNow
		);
	}
	
	/**
	 * 获取这个月的开始和结束
	 * @param int $ts 时间戳
	 * @return array 第一个元素为开始日期，第二个元素为结束日期
	*/
	function thisMonth($ts) {
		$ts = intval($ts);
	 
		$oneMonthAgo = mktime(0, 0, 0, date('n', $ts) , 1, date('Y', $ts));
		$oneMonthNow = mktime(0, 0, 0, date('n', $ts) , date('t', $ts), date('Y', $ts));
		return array(
			$oneMonthAgo,
			$oneMonthNow
		);
	}

	
}