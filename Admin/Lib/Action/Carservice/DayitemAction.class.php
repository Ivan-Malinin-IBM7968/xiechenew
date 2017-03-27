<?php
class DayitemAction extends CommonAction {
    function __construct() {
		parent::__construct();
        $this->checkreport_model = M('tp_xieche.checkreport_total', 'xc_');  //检测报告表
        $this->check_step_model = M('tp_xieche.check_step', 'xc_');  //技师步骤表
        $this->technician_model = M('tp_xieche.technician', 'xc_');  //技师表
        $this->technician_schedule_model = M('tp_xieche.technician_schedule', 'xc_');  //技师排期表 
        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter', 'xc_');  //保养项目
        
        $this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
        $this->filter_model = M('tp_xieche.item_filter','xc_');  //保养项目
        
        $this->user_model = M('tp_xieche.member', 'xc_');//用户表
		$this->admin_model = M('tp_admin.user', 'xc_');//后台用户表

        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->item_model = M('tp_xieche.item_filter','xc_');  //保养项目
        
        $this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //预约订单
        $this->carservicecode_model = M('tp_xieche.carservicecode','xc_');//上门保养抵用码字段
        $this->model_sms = M('tp_xieche.sms','xc_');//手机短信
	}
	
	/*
		@author:wwy
		@function:
		@time:2014-11-10
	*/
    public function index(){
        if($_POST['city_id'] and $_POST['city_id']!='all'){
            $map['city_id'] = $_POST['city_id'];
        }
		if( $_REQUEST['start_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('gt',strtotime($_REQUEST['start_time'])));
		}
		if( $_REQUEST['end_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
		}
		if( $_REQUEST['start_time'] && $_REQUEST['end_time'] ) {
			$map[$_REQUEST['time_type']] = array(array('gt',strtotime($_REQUEST['start_time'])),array('lt',strtotime($_REQUEST['end_time'].' 23:59:59')));
		}
		$map['status'] = 2;//已分配技师

		$model_reservation = D('reservation_order');
		$list = $model_reservation->where($map)->select();
		//echo $model_reservation->getLastsql();

		if($list!=''){
			foreach($list as $k=>$v){
				//获取用户指定检测项目信息
				$item = unserialize($v['item']);
				print_r($item);
			}
		}
		$this->assign('start_time',$_REQUEST['start_time']);
		$this->assign('end_time',$_REQUEST['end_time']);
		$this->assign('city_id', $_POST['city_id']);
		$this->assign('list', $list);
        $this->display();
    }
	    
}
?>