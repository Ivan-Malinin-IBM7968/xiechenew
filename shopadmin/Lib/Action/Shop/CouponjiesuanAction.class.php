<?php
/*
	@author:ysh
	@function:优惠券结算
	@time:2013/7/10
*/
class CouponjiesuanAction extends CommonAction {
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

		$model_couponjiesuan = D(GROUP_NAME.'/Couponjiesuan');
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
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

		/**导出数据start**/
        $execl = $_REQUEST['execl'];
		if($execl){
			//现金券
			$map_mc['coupon_type'] = 1;
			$couponjiesuan1 = $model_couponjiesuan->where($map_mc)->order("id DESC")->select();		
			//$couponjiesuan = $model_couponjiesuan->where($map_mc)->order("id DESC")->limit($p->firstRow.','.$p->listRows)->select();
			
			if($couponjiesuan1) {
				foreach($couponjiesuan1 as $key=>$val) {
					$membercoupon = $model_membercoupon->where(array('membercoupon_id'=>$val['membercoupon_id']))->find();
					$couponjiesuan1[$key]['coupon_name'] = $membercoupon['coupon_name'];
					$couponjiesuan1[$key]['mobile'] = $membercoupon['mobile'];
					$couponjiesuan1[$key]['licenseplate'] = $membercoupon['licenseplate'];
					$couponjiesuan1[$key]['use_time'] = date('Y-m-d H:i:s',$membercoupon['use_time']);
					$couponjiesuan1[$key]['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
					$couponjiesuan1[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];

					$member_info = $model_member->find($val['uid']);
					$couponjiesuan1[$key]['user_name'] = $member_info['username'];

					$jiesuan_amount1 += $val['cost_price'];//结算金额
					$jiesuan_commission1 += $val['commission'];//佣金
				}
				$true_pay1 = round($jiesuan_amount1 - $jiesuan_commission1,5);//实际支付
			}

			
			//套餐券
			$map_mc['coupon_type'] = 2;
			$couponjiesuan2 = $model_couponjiesuan->where($map_mc)->order("id DESC")->select();		
			
			if($couponjiesuan2) {
				foreach($couponjiesuan2 as $key=>$val) {
					$membercoupon = $model_membercoupon->where(array('membercoupon_id'=>$val['membercoupon_id']))->find();
					$couponjiesuan2[$key]['coupon_name'] = $membercoupon['coupon_name'];
					$couponjiesuan2[$key]['mobile'] = $membercoupon['mobile'];
					$couponjiesuan2[$key]['licenseplate'] = $membercoupon['licenseplate'];
					$couponjiesuan2[$key]['use_time'] = date('Y-m-d H:i:s',$membercoupon['use_time']);
					$couponjiesuan2[$key]['addtime'] = date('Y-m-d H:i:s',$val['addtime']);
					$couponjiesuan2[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];

					$member_info = $model_member->find($val['uid']);
					$couponjiesuan2[$key]['user_name'] = $member_info['username'];

					$jiesuan_amount2 += $val['coupon_amount'];//结算金额
					$jiesuan_commission2 += $val['commission'];//佣金
				}
				$true_pay2 = round($jiesuan_amount2 - $jiesuan_commission2,5);//实际支付
			}

			$jiesuan_amount = $jiesuan_amount1 + $jiesuan_amount2;
			$jiesuan_commission = $jiesuan_commission1 + $jiesuan_commission2;
			$true_pay = round($true_pay1 + $true_pay2,5);
			$this->export_execl($couponjiesuan1,$couponjiesuan2,$jiesuan_amount,$jiesuan_commission,$true_pay,'优惠券结算.xls');
		}
		/**导出数据end**/

		/* 计算总数
		$count = $model_couponjiesuan->where($map_mc)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 10);
		// 分页显示输出
		$page = $p->show_admin();
		*/
		
		//现金券
		$map_mc['coupon_type'] = 1;
		$couponjiesuan1 = $model_couponjiesuan->where($map_mc)->order("id DESC")->select();		
		//$couponjiesuan = $model_couponjiesuan->where($map_mc)->order("id DESC")->limit($p->firstRow.','.$p->listRows)->select();
		
		if($couponjiesuan1) {
			foreach($couponjiesuan1 as $key=>$val) {
				$membercoupon = $model_membercoupon->where(array('membercoupon_id'=>$val['membercoupon_id']))->find();
				$couponjiesuan1[$key]['coupon_name'] = $membercoupon['coupon_name'];
				$couponjiesuan1[$key]['membercoupon_id'] = $val['membercoupon_id'];
				$couponjiesuan1[$key]['mobile'] = $membercoupon['mobile'];
				$couponjiesuan1[$key]['licenseplate'] = $membercoupon['licenseplate'];
				$couponjiesuan1[$key]['use_time'] = $membercoupon['use_time'];
				$couponjiesuan1[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];
				
				$member_info = $model_member->find($membercoupon['uid']);
				$couponjiesuan1[$key]['user_name'] = $member_info['username'];

				$jiesuan_amount1 += $val['cost_price'];//结算金额
				$jiesuan_commission1 += $val['commission'];//佣金
			}
			$true_pay1 = round($jiesuan_amount1 - $jiesuan_commission1,5);//实际支付
		}

		
		//套餐券
		$map_mc['coupon_type'] = 2;
		$couponjiesuan2 = $model_couponjiesuan->where($map_mc)->order("id DESC")->select();		
		
		if($couponjiesuan2) {
			foreach($couponjiesuan2 as $key=>$val) {
				$membercoupon = $model_membercoupon->where(array('membercoupon_id'=>$val['membercoupon_id']))->find();
				$couponjiesuan2[$key]['membercoupon_id'] = $val['membercoupon_id'];
				$couponjiesuan2[$key]['coupon_name'] = $membercoupon['coupon_name'];
				$couponjiesuan2[$key]['mobile'] = $membercoupon['mobile'];
				$couponjiesuan2[$key]['licenseplate'] = $membercoupon['licenseplate'];
				$couponjiesuan2[$key]['use_time'] = $membercoupon['use_time'];
				$couponjiesuan2[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];

				$member_info = $model_member->find($membercoupon['uid']);
				$couponjiesuan2[$key]['user_name'] = $member_info['username'];

				$jiesuan_amount2 += $val['coupon_amount'];//结算金额
				$jiesuan_commission2 += $val['commission'];//佣金
			}
			$true_pay2 = round($jiesuan_amount2 - $jiesuan_commission2,5);//实际支付
		}

		$jiesuan_amount = $jiesuan_amount1 + $jiesuan_amount2;
		$jiesuan_commission = $jiesuan_commission1 + $jiesuan_commission2;
		$true_pay = round($true_pay1 + $true_pay2,5);

		$this->assign('couponjiesuan1',$couponjiesuan1);
		$this->assign('couponjiesuan2',$couponjiesuan2);

		$this->assign('jiesuan_amount',$jiesuan_amount);
		$this->assign('jiesuan_commission',$jiesuan_commission);
		$this->assign('true_pay',$true_pay);

		$this->assign('jiesuan_amount1',$jiesuan_amount1);
		$this->assign('jiesuan_commission1',$jiesuan_commission1);
		$this->assign('true_pay1',$true_pay1);

		$this->assign('jiesuan_amount2',$jiesuan_amount2);
		$this->assign('jiesuan_commission2',$jiesuan_commission2);
		$this->assign('true_pay2',$true_pay2);

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

		$model_couponjiesuan = D(GROUP_NAME.'/Couponjiesuan');
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
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


		//现金券
		$map_mc['coupon_type'] = 1;
		$couponjiesuan1 = $model_couponjiesuan->where($map_mc)->order("id DESC")->select();		

		if($couponjiesuan1) {
			foreach($couponjiesuan1 as $key=>$val) {
				$membercoupon = $model_membercoupon->where(array('membercoupon_id'=>$val['membercoupon_id']))->find();
				$couponjiesuan1[$key]['coupon_name'] = $membercoupon['coupon_name'];
				$couponjiesuan1[$key]['mobile'] = $membercoupon['mobile'];
				$couponjiesuan1[$key]['licenseplate'] = $membercoupon['licenseplate'];
				$couponjiesuan1[$key]['use_time'] = $membercoupon['use_time'];
				$couponjiesuan1[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];

				$member_info = $model_member->find($val['uid']);
				$couponjiesuan1[$key]['user_name'] = $member_info['username'];

				$jiesuan_amount1 += $val['cost_price'];//结算金额
				$jiesuan_commission1 += $val['commission'];//佣金
			}
			$true_pay1 = round($jiesuan_amount1 - $jiesuan_commission1,5);//实际支付
		}

		
		//套餐券
		$map_mc['coupon_type'] = 2;
		$couponjiesuan2 = $model_couponjiesuan->where($map_mc)->order("id DESC")->select();		
		
		if($couponjiesuan2) {
			foreach($couponjiesuan2 as $key=>$val) {
				$membercoupon = $model_membercoupon->where(array('membercoupon_id'=>$val['membercoupon_id']))->find();
				$couponjiesuan2[$key]['coupon_name'] = $membercoupon['coupon_name'];
				$couponjiesuan2[$key]['mobile'] = $membercoupon['mobile'];
				$couponjiesuan2[$key]['licenseplate'] = $membercoupon['licenseplate'];
				$couponjiesuan2[$key]['use_time'] = $membercoupon['use_time'];
				$couponjiesuan2[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];

				$member_info = $model_member->find($val['uid']);
				$couponjiesuan2[$key]['user_name'] = $member_info['username'];

				$jiesuan_amount2 += $val['coupon_amount'];//结算金额
				$jiesuan_commission2 += $val['commission'];//佣金
			}
			$true_pay2 = round($jiesuan_amount2 - $jiesuan_commission2,5);//实际支付
		}

		$jiesuan_amount = $jiesuan_amount1 + $jiesuan_amount2;
		$jiesuan_commission = $jiesuan_commission1 + $jiesuan_commission2;
		$true_pay = round($true_pay1 + $true_pay2,5);
		
		$this->assign("month_type",$month_type);

		$this->assign('couponjiesuan1',$couponjiesuan1);
		$this->assign('couponjiesuan2',$couponjiesuan2);

		$this->assign('jiesuan_amount',$jiesuan_amount);
		$this->assign('jiesuan_commission',$jiesuan_commission);
		$this->assign('true_pay',$true_pay);

		$this->assign('jiesuan_amount1',$jiesuan_amount1);
		$this->assign('jiesuan_commission1',$jiesuan_commission1);
		$this->assign('true_pay1',$true_pay1);

		$this->assign('jiesuan_amount2',$jiesuan_amount2);
		$this->assign('jiesuan_commission2',$jiesuan_commission2);
		$this->assign('true_pay2',$true_pay2);

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

		$model_couponjiesuan = D(GROUP_NAME.'/Couponjiesuan');
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
		$model_member = D(GROUP_NAME.'/Member');
		$model_couponjiesuanlog = D(GROUP_NAME."/Couponjiesuanlog");

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
		
		$jiesuan_info = $model_couponjiesuan->where($map_mc)->order("id DESC")->select();
		if($jiesuan_info) {
			foreach($jiesuan_info as $key=>$val) {

				$model_couponjiesuanlog->add(array('coupon_jiesuan_id'=>$val['id'],'name'=>$_SESSION['loginUserName'],'log'=>'商家确认结算将订单状态改为商家确认','addtime'=>time()));
			}
		}
		$data['jiesuan_status'] = 2;
		$model_couponjiesuan->where($map_mc)->save($data);

		$this->success('申请结算成功！',U('/Shop/Couponjiesuan/index/jiesuan_status/2'));
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

	/**
	 * 导EXECL方法
	 * @author wwy
	 * 
	*/
	function export_execl($list1,$list2,$jiesuan_amount,$jiesuan_commission,$true_pay,$filename){
		if($list1){
			foreach($list1 as $key=>$val){
				if ($n%2==1){
					$color = "#CCDDDD";
				}else {
					$color = "#FFFFFF";
				}
				$str_table1 .= '<tr bgcolor='.$color.'><td>'.$val['coupon_id'].'</td><td>现金券</td><td>'.$val['coupon_name'].'</td><td>'.$val['user_name'].'</td><td>'.$val['mobile'].'</td><td>原价:'.$val['cost_price'].'现价:'.$val['coupon_amount'].'</td><td>'.$val['commission'].'</td><td>'.$val['use_time'].'</td><td>'.$val['addtime'].'</td><td>'.$val['jiesuan_status_str'].'</td>';
			}
		}
		if($list2){
			foreach($list2 as $k=>$v){
				if ($n%2==1){
					$color = "#CCDDDD";
				}else {
					$color = "#FFFFFF";
				}
				$str_table2 .= '<tr bgcolor='.$color.'><td>'.$val['coupon_id'].'</td><td>套餐券</td><td>'.$val['coupon_name'].'</td><td>'.$val['user_name'].'</td><td>'.$val['mobile'].'</td><td>原价:'.$val['cost_price'].'现价:'.$val['coupon_amount'].'</td><td>'.$val['commission'].'</td><td>'.$val['use_time'].'</td><td>'.$val['addtime'].'</td><td>'.$val['jiesuan_status_str'].'</td>';
			}
		}
		$str_table3 = '<tr><td>总结算金额:'.$jiesuan_amount.'</td><td>总佣金:'.$jiesuan_commission.'</td><td>总实际支付:'.$true_pay.'</td>';
		$color = "#00CD34";
	    header("Content-type:aplication/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename='{$filename}'");
        $str = '<table><tr bgcolor='.$color.'><td>优惠券ID</td><td>优惠券类型</td><td>优惠券名称</td><td>用户名</td><td>手机号</td><td>金额</td><td>佣金</td><td>使用时间</td><td>结算时间</td><td>结算状态</td></tr>';
        $str .= $str_table1.$str_table2.$str_table3;
        $str .= '</table>';
        $str = iconv("UTF-8", "GBK", $str);
        echo $str;exit;
	}

}