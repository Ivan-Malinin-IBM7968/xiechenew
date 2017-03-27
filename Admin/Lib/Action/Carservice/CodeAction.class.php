<?php
class CodeAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->Carservicecode = D('Carservicecode');//优惠卷表
		$this->ShopModel = D('shop');//店铺表
		$this->MemberModel = D('Member');//用户表
		$this->MembersalecouponModel = D('Membersalecoupon');//抵用券表
		$this->model_sms = D('Sms');
		$this->LotteryModel = D('lottery');//抽奖监控表
		$this->LotteryloginModel = D('lotterylogin');//抽奖页登录监控表
		$this->LotteryappModel = D('lotteryapp');//下载APP登录监控表
		$this->technician_model = D('technician');//技师表
	}
	
	/*
		@author:wwy
		@function:
		@time:2014-11-10
	*/
    public function index(){
        $model_carservicecode = D(GROUP_NAME.'/Carservicecode');
        $model_member = D(GROUP_NAME.'/Member');
		$model_order = D(GROUP_NAME.'/Order');
		if($_REQUEST['mobile']){
			$map['mobile'] = $_REQUEST['mobile'];
		}
		if($_REQUEST['status']==''){
		}else{
			$map['status'] = $_REQUEST['status'];
		}

		if($_REQUEST['bind']==''){
		}elseif($_REQUEST['bind']==1){
			$map['mobile'] = array('gt',0);
		}elseif($_REQUEST['bind']==0){
			$map['mobile'] = 0;
		}
		if($_REQUEST['technician_id']){
            $map['js_code'] = $_REQUEST['technician_id'];
        }

		// 计算总数
		$count = $model_carservicecode->where($map)->count();

		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		if($_REQUEST['is_use'] != "" ) {
			$p->parameter .= "is_use/" . urlencode($_REQUEST['is_use']) . "/";
		}
		if($_REQUEST['order_id'] != "" ) {
			$p->parameter .= "order_id/" . urlencode($_REQUEST['order_id']) . "/";
		}
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		// 分页显示输出
		$page = $p->show_admin();
		$code_info = $model_carservicecode->where($map)->limit($p->firstRow.','.$p->listRows)->order('id DESC')->select();
		//echo $model_carservicecode->getLastsql();

		if ($code_info){
			foreach ($code_info as $k=>$v){
				$info = $this->technician_model->where(array('id'=>$v['js_code']))->find();
				$code_info[$k]['js'] = $info['truename'];
				$code_info[$k]['true_order_id'] = $v['order_id'];
				if($v['order_id']) {
					$code_info[$k]['order_id'] = $this->get_orderid($v['order_id']);

					$order_info = $model_order->find($v['order_id']);
					$code_info[$k]['order_state'] = $order_state[$order_info['order_state']];
				}
				foreach($ARRAY as $ARRAY_k=>$ARRAY_v){
				
					if($v['from'] == $ARRAY_k){
						
						$code_info[$k]['from'] = $ARRAY_v; 
					}
				}
				$memberinfo = $model_member->find($v['uid']);
				$code_info[$k]['memberinfo'] = $memberinfo;
			}
		}
		
        $condition = array();
        $condition['status'] = 1;
        $technician_list = $this->technician_model->where($condition)->select();

		$this->assign('mobile',$mobile);
		$this->assign('is_use',$_REQUEST['is_use']);
		$this->assign('order_id',$_REQUEST['order_id']);
		$this->assign('page', $page);
		$this->assign('ARRAY', $ARRAY);
        $this->assign('memberlist',$code_info);
		$this->assign('coupon_id',$coupon_id);
		$this->assign('technician_list', $technician_list);
        $this->display();
    }
	    
}
?>