<?php
/*
	@author:ysh
	@function:优惠券订单后台
	@time:2013/5/14
*/
class CouponorderAction extends CommonAction {
    function __construct() {
		parent::__construct();
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺ID不存在！');
        }
	}
    public function index(){//现金券套餐券列表
        $shop_id = $_SESSION['shop_id'];
		$coupon_type = $_REQUEST['coupon_type'];
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
        $model_membercoupon = D(GROUP_NAME.'/Membercoupon');
		$model_member = D(GROUP_NAME.'/Member');
		$model_coupon = D(GROUP_NAME.'/Coupon');

		$map_mc['is_use'] = 1;
		$map_mc['shop_id'] = $shop_id;
		if($coupon_type) {
			$map_mc['coupon_type'] = $coupon_type;
		}
		if($start_time) {
			$map_mc['use_time'] = array('egt',strtotime($start_time));
			$this->assign("start_time",$start_time);
		}
		if($end_time) {
			$map_mc['use_time'] = array('elt',strtotime($end_time));
			$this->assign("end_time",$end_time);
		}
		if($start_time && $end_time) {
			$map_mc['use_time'] = array(array('egt',strtotime($start_time)),array('elt',strtotime($end_time)));
			$this->assign("start_time",$start_time);
			$this->assign("end_time",$end_time);
		}
		$map_mc['is_delete'] = 0;
		// 计算总数
		$count = $model_membercoupon->where($map_mc)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 10);
		// 分页显示输出
		$page = $p->show_admin();
	
		$membercoupon = $model_membercoupon->where($map_mc)->order("use_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		if($membercoupon) {
			foreach($membercoupon as $key=>$val) {
				$coupon_info = $model_coupon->find($val['coupon_id']);
				
				$membercoupon[$key]['coupon_amount'] = $coupon_info['coupon_amount'];
				$membercoupon[$key]['cost_price'] = $coupon_info['cost_price'];

				$membercoupon[$key]['order_id'] = $this->get_orderid($val['order_id']);
				$membercoupon[$key]['true_order_id'] = $val['order_id'];

				$member_info = $model_member->find($val['uid']);
				$membercoupon[$key]['user_name'] = $member_info['username'];
			}
		}

		$this->assign('membercoupon',$membercoupon);
		$this->assign('coupon_type',$coupon_type);
		$this->assign('now',time());
		$this->assign('page',$page);
        
        $this->display();
    }

	function add_remark() {
		$membercoupon_id = $_REQUEST['membercoupon_id'];
		$model_membercoupon = D(GROUP_NAME.'/Membercoupon');

		$membercoupon = $model_membercoupon->find($membercoupon_id);

		$this->assign("membercoupon",$membercoupon);
		$this->assign("membercoupon_id",$membercoupon_id);
		$this->display();
	}

	function insert_remark() {
		$membercoupon_id = $_REQUEST['membercoupon_id'];
		$remark = $_REQUEST['remark'];
		$model_membercoupon = D(GROUP_NAME.'/Membercoupon');
		
		$data['membercoupon_id'] = $membercoupon_id;
		$data['remark'] = $remark;
		$model_membercoupon->save($data);
		$this->success("添加成功！",U("/Shop/Couponorder/index"));
	}
}