<?php

class FinancemanageAction extends CommonAction {	
	protected $item_model;
	protected $storehouse_model;
	protected $inventory_conf_model;
	protected $storehouse_item_model;
	protected $item_oil_model;
	protected $finance_model;
	function __construct() {
		parent::__construct();
        $this->item_model = M('tp_xieche.item_filter','xc_');  //机滤，空滤，表
        $this->storehouse_model = M('tp_xieche.storehouse','xc_');//仓库表
        $this->storehouse_item_model = M('tp_xieche.storehouse_item','xc_');//仓库数据详情表
        $this->inventory_conf_model = M('tp_xieche.inventory_conf','xc_');//仓库配置表
        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
        $this->finance_model = M('tp_xieche.finance','xc_');  //财务表
        $this->reservation_order_model = M('tp_xieche.reservation_order','xc_');  //上门保养预约
	}
	//采购列表
	public function index() {
		//先只做上门保养的结算，可根据finance_type扩展
		// 计算总数
		$count = $this->reservation_order_model->join('RIGHT JOIN tp_xieche.xc_finance ON tp_xieche.xc_reservation_order.id  = tp_xieche.xc_finance.oid')->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 20);
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		// 分页显示输出
		$page = $p->show_admin();
		// 当前页数据查询
		$list =$this->reservation_order_model->join('RIGHT JOIN tp_xieche.xc_finance ON tp_xieche.xc_reservation_order.id  = tp_xieche.xc_finance.oid')->order('tp_xieche.xc_finance.id ASC')->limit($p->firstRow.','.$p->listRows)->select();
        //var_dump($this->reservation_order_model->getLastSql());exit;
		$this->assign('list',$list);
        $this->assign('page',$page);
		$this->display();
	}

	//结算
	public function account_order(){
		$id = $this->_post('id');
		$data = array(
				'amount_status'		 => 2,
		);
		$res = $this->finance_model->where(array('id'=>$id))->save($data);
		if($res){
			//程序日志
			$array = array(
					'oid'=>$id,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作已结算:结算id:'.$id
			);
			$this->addOperateLog($array);
			echo json_encode(array('status'=>1));
		}else{
			echo json_encode(array('status'=>0));
		}		
	}

}
