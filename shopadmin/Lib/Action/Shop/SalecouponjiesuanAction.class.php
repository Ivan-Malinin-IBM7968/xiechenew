<?php
//订单
class SalecouponjiesuanAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
			$shop_id = $_SESSION['shop_id'];
		}else {
			$this->error('店铺ID不存在！');
		}
		$this->ShopModel = D('shop');//商铺表
		$this->MembercouponModel = D('membercoupon');//优惠卷表
		$this->MemberModel = D('member');//用户表
		$this->SalecouponModel = D('salecoupon');//派送优惠券信息表
		$this->MembersalecouponModel = D('membersalecoupon');//派送优惠券订单表
		$this->MsalecouponjiesuanModel = D('membersalecouponjiesuan');//派送优惠券订单表
		$this->salecouponjiesuanlogModel = D('salecouponjiesuanlog');//派送优惠券订单表
	}


	/*
		@author:chf
		@function:显示抵用券结算页
		@time:2013-8-21
	*/
    public function index(){
        $shop_id = $_SESSION['shop_id'];
		$jiesuan_status = isset($_REQUEST['jiesuan_status'] )?$_REQUEST['jiesuan_status']:1;//默认显示商家待确认状态的 结算列表
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
		$month_type = $_REQUEST['month_type'];//last_month 上月未结算 this_month 本月未结算		

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
		// 计算总数
		$count = $this->MsalecouponjiesuanModel->where($map_mc)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		// 分页显示输出
		$page = $p->show_admin();

		$Msalecouponjiesuan = $this->MsalecouponjiesuanModel->where($map_mc)->order("membersalecoupon_id DESC")->limit($p->firstRow.','.$p->listRows)->select();
		
		if($Msalecouponjiesuan) {
			foreach($Msalecouponjiesuan as $key=>$val) {
				$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$val['membersalecoupon_id']))->find();
				$Msalecouponjiesuan[$key]['coupon_name'] = $Membersalecoupon['coupon_name'];
				$Msalecouponjiesuan[$key]['mobile'] = $Membersalecoupon['mobile'];
				$Msalecouponjiesuan[$key]['licenseplate'] = $Membersalecoupon['licenseplate'];
				$Msalecouponjiesuan[$key]['use_time'] = $Membersalecoupon['use_time'];
				$Msalecouponjiesuan[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];
				
				$member_info = $this->MemberModel->where(array('uid'=>$Membersalecoupon['uid']))->find();
				if($member_info['username']){
					$Msalecouponjiesuan[$key]['user_name'] = $member_info['username'];
				}else{
					$Msalecouponjiesuan[$key]['user_name'] = $member_info['mobile'];
				}
				if($val['commission']=='0'){
					$jiesuan_amount1+=50;//结算金额
				}else{
					$jiesuan_amount1+=$val['commission'];//结算金额
				}
				
				$jiesuan_commission1+= $val['commission'];//佣金
			}
			$true_pay1 = round($jiesuan_amount1,5);//实际支付
		}

		$jiesuan_amount = $jiesuan_amount1;
		$jiesuan_commission = $jiesuan_commission1;
		$true_pay = round($true_pay1,5);

		$this->assign('Msalecouponjiesuan',$Msalecouponjiesuan);

		$this->assign('jiesuan_amount',$jiesuan_amount);
		$this->assign('jiesuan_commission',$jiesuan_commission);
		$this->assign('true_pay',$true_pay);

		$this->assign('jiesuan_amount1',$jiesuan_amount1);
		$this->assign('jiesuan_commission1',$jiesuan_commission1);
		$this->assign('true_pay1',$true_pay1);
		$this->assign('page',$page);
        $this->display();
    }
	

	/**
		 @author:chf
		 @function 返回列表是后台提交的结算列表 商家用于确认的二次显示页面
		 @time:2013-8-21
	*/
	function true_jiesuan() {
        $shop_id = $_SESSION['shop_id'];
		$jiesuan_status = isset($_REQUEST['jiesuan_status'] )?$_REQUEST['jiesuan_status']:1;//默认显示商家待确认状态的 结算列表
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
		$month_type = $_REQUEST['month_type'];//last_month 上月未结算 this_month 本月未结算

		$this->assign("month_type",$month_type);

		$map_mc['shop_id'] = $shop_id;

		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];//
		foreach($membersalecoupon_id as $k=>$v){
			$membersalecoupon_ids.=$v.",";
		}
		$membersalecoupon_ids = substr($membersalecoupon_ids,0,-1);
		$map_mc['membersalecoupon_id'] = array('in',$membersalecoupon_ids);


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

		/*$count = $this->MsalecouponjiesuanModel->where($map_mc)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($dcount, 20);
		// 分页显示输出
		$page = $p->show_admin();
		*/

		$Msalecouponjiesuan = $this->MsalecouponjiesuanModel->where($map_mc)->order("membersalecoupon_id DESC")->select();
		
		if($Msalecouponjiesuan) {
			foreach($Msalecouponjiesuan as $key=>$val) {
				$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$val['membersalecoupon_id']))->find();
				$Msalecouponjiesuan[$key]['coupon_name'] = $Membersalecoupon['coupon_name'];
				$Msalecouponjiesuan[$key]['mobile'] = $Membersalecoupon['mobile'];
				$Msalecouponjiesuan[$key]['licenseplate'] = $Membersalecoupon['licenseplate'];
				$Msalecouponjiesuan[$key]['use_time'] = $Membersalecoupon['use_time'];
				$Msalecouponjiesuan[$key]['jiesuan_status_str'] = $jiesuan_status_str[$val['jiesuan_status']];
				
				$member_info = $this->MemberModel->where(array('uid'=>$Membersalecoupon['uid']))->find();
				if($member_info['username']){
					$Msalecouponjiesuan[$key]['user_name'] = $member_info['username'];
				}else{
					$Msalecouponjiesuan[$key]['user_name'] = $member_info['mobile'];
				}
				
				if($val['commission']=='0'){
					$jiesuan_amount1+=50;//结算金额
				}else{
					$jiesuan_amount1+=$val['commission'];//结算金额
				}
				$jiesuan_commission1 += $val['commission'];//佣金
			}
			$true_pay1 = round($jiesuan_amount1,5);//实际支付
		}
		$jiesuan_amount = $jiesuan_amount1;
		$jiesuan_commission = $jiesuan_commission1;
		$true_pay = round($true_pay1,5);
		$this->assign("month_type",$month_type);

		$this->assign('couponjiesuan1',$couponjiesuan1);
		$this->assign('Msalecouponjiesuan',$Msalecouponjiesuan);
		$this->assign('jiesuan_amount',$jiesuan_amount);
		$this->assign('jiesuan_commission',$jiesuan_commission);
		$this->assign('true_pay',$true_pay);

		$this->assign('jiesuan_amount1',$jiesuan_amount1);
		$this->assign('jiesuan_commission1',$jiesuan_commission1);
		$this->assign('true_pay1',$true_pay1);
		$this->display();
	}
	/**
	 * @function:修改结算状态
	 *	@author:ysh
	 * @time:2013/7/16

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
	}	*/

	/*
		@author:chf
		@function:商家同意结算
		@time:2013-8-21
	*/
	function update_jiesuan(){
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];
		$shop_id = $_SESSION['shop_id'];
		foreach($membersalecoupon_id as $k=>$v){
			$Membersalecoupon = $this->MembersalecouponModel->where(array('membersalecoupon_id'=>$v))->find();
			$Salecoupon = $this->SalecouponModel->where(array('id'=>$Membersalecoupon['salecoupon_id']))->find();

			$this->MembercouponModel->where(array('membercoupon_id'=>$v))->data(array('is_jiesuan'=>1,'jiesuan_time'=>time()))->save();
			
			$data['jiesuan_status'] = 2;//比例
			$jiesuan_id = $this->MsalecouponjiesuanModel->where(array('membersalecoupon_id'=>$v,'shop_id'=>$shop_id))->data($data)->save();
			$this->salecouponjiesuanlogModel->add(array('salecouponjiesuan_id'=>$jiesuan_id,'name'=>$_SESSION['loginUserName'],'log'=>'商家确认结算将订单状态改为商家确认','addtime'=>time()));
		}
		$this->success('确认结算成功！',U('/Shop/Salecouponjiesuan/index/jiesuan_status/2'));
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