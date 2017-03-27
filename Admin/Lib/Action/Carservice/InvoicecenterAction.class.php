<?php
class InvoicecenterAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->Carservicecode = D('Carservicecode');//优惠卷表
		$this->ShopModel = D('shop');//店铺表
		$this->MemberModel = D('Member');//用户表
		$this->MembersalecouponModel = D('Membersalecoupon');//抵用券表
		$this->model_sms = D('Sms');
		$this->LotteryModel = D('lottery');//抽奖监控表
		$this->LotteryloginModel = D('lotterylogin');//抽奖页登录监控表
		$this->invoice_model = D('invoice');//下载APP登录监控表
		$this->technician_model = D('technician');//技师表
		$this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //预约订单
		$this->check_step_model = M('tp_xieche.check_step', 'xc_');  //技师步骤表
	}
	
	/*
		@author:wwy
		@function:
		@time:2014-11-10
	*/
    public function index(){
		//print_r($_SESSION);
        $model_checkreport = D(GROUP_NAME.'/Checkreport_total');
        $model_member = D(GROUP_NAME.'/Member');
		$model_order = D(GROUP_NAME.'/Order');
		if($_REQUEST['invoice_status'] and $_REQUEST['invoice_status']!='all'){
			$map['xc_invoice.invoice_status'] = $_REQUEST['invoice_status'];
			$this->assign('invoice_status',$_REQUEST['invoice_status']);
		}elseif($_REQUEST['invoice_status']=='all'){
			$this->assign('invoice_status',$_REQUEST['invoice_status']);
		}elseif(!$_REQUEST['invoice_status']){
			$map['xc_invoice.invoice_status'] = 1;
			$this->assign('invoice_status',$map['invoice_status']);
		}
		if($_REQUEST['order_id']){
			$map['xc_invoice.order_id'] = $_REQUEST['order_id'];
		}
        //时间区间
        if($_REQUEST['start_time'] && $_REQUEST['end_time']){
            $start_time = strtotime($_REQUEST['start_time']);
            $end_time  = strtotime($_REQUEST['end_time']); 
            $map['xc_reservation_order.order_time'] = array(array('lt', $end_time),array('gt',$start_time),"AND"); 
            //获取当前时间段内上门保养总金额
            $cond['order_time'] = array(array('lt', $end_time),array('gt',$start_time),"AND"); 
            $rs = $this->reservation_order_model->where($cond)->select() ;
            $total_amount = 0 ;
            foreach($rs as $k=>$v){
                $total_amount += $v['amount'] ;
            }
            
            $this->assign('total_amount',$total_amount);
            $this->assign('start_time',$_REQUEST['start_time']);
            $this->assign('end_time',$_REQUEST['end_time']);
        }


        // 计算总数
		$count = $this->invoice_model->join('LEFT JOIN xc_reservation_order ON xc_reservation_order.id = xc_invoice.order_id')->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		if(!$_REQUEST['p']){
			$p->parameter .= "index/";
		}
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
        
        
        //查询字段
        $fields = 'xc_reservation_order.order_time,xc_invoice.id,xc_invoice.id,xc_invoice.order_id,xc_invoice.invoice_status,xc_invoice.invoice_status,xc_invoice.receiver_address,xc_invoice.receiver_name,xc_invoice.receiver_phone';
        $invoice_info = $this->invoice_model->field($fields)->join('LEFT JOIN xc_reservation_order ON xc_reservation_order.id = xc_invoice.order_id' )->where($map)->limit($p->firstRow.','.$p->listRows)->order('xc_invoice.id DESC')->select();
        //echo $this->invoice_model->getLastsql();
        
        //查询满足条件的所有数据 ，为了求得已开票，待开票。
        $invoice = $this->invoice_model->field($fields)->join('LEFT JOIN xc_reservation_order ON xc_reservation_order.id = xc_invoice.order_id' )->where($map)->select();
		//echo $this->invoice_model->getLastsql();
        
        $wait_invoice = 0 ;  //待开票
        $has_invoice = 0 ; //已开票已寄出
        foreach ($invoice as $k => $v) {
            $order = $this->reservation_order_model->where(array('id'=>$v['order_id']))->find();
			$invoice[$k]['amount'] = $order['amount'];
             //已开票金额和已开票已寄出
             if($v['invoice_status']==1){
                 $wait_invoice +=  $invoice[$k]['amount'] ;
             }else{
                 $has_invoice +=  $invoice[$k]['amount'] ;
             }
        }
        
        //客户不开票金额
        if($total_amount){
            $no_invoice = $total_amount - $wait_invoice - $has_invoice  ;
            $this->assign('no_invoice',$no_invoice);
        }
        
        //数据显示处理
		if ($invoice_info){
			foreach ($invoice_info as $k=>$v){
            	$invoice_info[$k]['show_id'] = $this->get_orderid($v['order_id']).'('.$v['order_id'].')';
            	$invoice_info[$k]['order_id'] = $this->get_orderid($v['order_id']);
				$invoice_info[$k]['true_id'] = $v['order_id'];
				$order_info = $this->reservation_order_model->where(array('id'=>$v['order_id']))->find();
				$t_info = $this->technician_model->where(array('id'=>$order_info['technician_id']))->find();
				$invoice_info[$k]['name'] = $t_info['truename'];
				$invoice_info[$k]['status'] = $this->getStatusName($order_info['status']);
				$invoice_info[$k]['amount'] = $order_info['amount'];
				$mad['order_id'] = $v['order_id'];
				$mad['step_id'] = 6;
				$step_info = $this->check_step_model->where($mad)->find();
				$invoice_info[$k]['complete_time'] = $step_info['create_time'];
			}
		}
        
		
        $condition = array();
        $condition['status'] = 1;
        $technician_list = $this->technician_model->where($condition)->select();

        $this->assign('wait_invoice',$wait_invoice);
        $this->assign('has_invoice',$has_invoice);
		$this->assign('mobile',$mobile);
		$this->assign('is_use',$_REQUEST['is_use']);
		$this->assign('order_id',$_REQUEST['order_id']);
		$this->assign('page', $page);
		$this->assign('invoice_info', $invoice_info);
        $this->assign('memberlist',$code_info);
		$this->assign('coupon_id',$coupon_id);
		$this->assign('technician_list', $technician_list);
        $this->display();
    }

	private function getStatusName($status){
        switch ($status) {
            case '0':
                return "等待处理";
                break;
            
            case '1':
                return "预约确认";
                break;

            case '2':
                return "已分配技师";
                break;

            case '8':
                return "已作废";
                break;

            case '9':
                return "服务已完成";
                break;

            default:
                return "等待处理";
                break;
        }
    }

	    
}
?>