<?php
/*
	@author:ysh
	@function:优惠券订单后台
	@time:2013-9-26 
*/
class SalecouponorderAction extends CommonAction {
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
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
        $model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');
		$model_member = D(GROUP_NAME.'/Member');

		$map_mc['is_use'] = 1;
		$map_mc['shop_id'] = $shop_id;

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
		$count = $model_membersalecoupon->where($map_mc)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 10);
		// 分页显示输出
		$page = $p->show_admin();
	
		$membersalecoupon = $model_membersalecoupon->where($map_mc)->order("use_time DESC")->limit($p->firstRow.','.$p->listRows)->select();
		if($membersalecoupon) {
			foreach($membersalecoupon as $key=>$val) {
				$membersalecoupon[$key]['order_id'] = $this->get_orderid($val['order_id']);
				$membersalecoupon[$key]['true_order_id'] = $val['order_id'];

				$member_info = $model_member->find($val['uid']);
				$membersalecoupon[$key]['user_name'] = $member_info['username'];
			}
		}

		$this->assign('membersalecoupon',$membersalecoupon);
		$this->assign('now',time());
		$this->assign('page',$page);
        
        $this->display();
    }

	function add_remark() {
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];
		$model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');

		$membercoupon = $model_membersalecoupon->find($membersalecoupon_id);

		$this->assign("membersalecoupon",$membersalecoupon);
		$this->assign("membersalecoupon_id",$membersalecoupon_id);
		$this->display();
	}

	function insert_remark() {
		$membersalecoupon_id = $_REQUEST['membersalecoupon_id'];
		$remark = $_REQUEST['remark'];
		$model_membersalecoupon = D(GROUP_NAME.'/Membersalecoupon');
		
		$data['membersalecoupon_id'] = $membersalecoupon_id;
		$data['remark'] = $remark;
		$model_membersalecoupon->save($data);
		$this->success("添加成功！",U("/Shop/Salecouponorder/index"));
	}
}