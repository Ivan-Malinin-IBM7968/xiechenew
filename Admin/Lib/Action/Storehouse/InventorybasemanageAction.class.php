<?php

class InventorybasemanageAction extends CommonAction {	
	protected $carbrand_model;
	protected $carmodel_model;
	protected $carseries_model;
	protected $storehouse_model;
	protected $inventory_conf_model;
	
	function __construct() {
		parent::__construct();
        $this->carbrand_model = M('tp_xieche.carbrand','xc_');  //车品牌
        $this->carmodel_model = M('tp_xieche.carmodel','xc_');  //车型号
        $this->carseries_model = M('tp_xieche.carseries','xc_');  //车型号
        $this->item_model = M('tp_xieche.item_filter','xc_');  //车型配置表
        $this->storehouse_model = M('tp_xieche.storehouse','xc_');
        $this->inventory_conf_model = M('tp_xieche.inventory_conf','xc_');//仓库配置表
	}
	//列表
	public function index() {
		// 计算总数
		$count = $this->inventory_conf_model->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 10);
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		// 分页显示输出
		$page = $p->show_admin();
		// 当前页数据查询
		$list = $this->inventory_conf_model->order('id ASC')->limit($p->firstRow.','.$p->listRows)->select();
		
		$this->assign('page',$page);
        $this->assign('list',$list);
        
		$this->display();
	}
	public function add(){
		$list = $this->inventory_conf_model->distinct(true)->field('storehouse_name,storehouse_id')->select();
		$this->assign('list',$list);
		$this->display();
	}
	public function do_add(){
		$storehouse = $this->_post('storehouse_id');
		list($storehouse_id,$storehouse_name) = explode('@', $storehouse);
		$data = array(
				'buyer'		 	  => $this->_post('buyer'),
				'storehouse_id'   => $storehouse_id,
				'storehouse_name' => $storehouse_name,
				'operator_person' => $this->_post('operator_person'),
				'return_person'	  => $this->_post('return_person'),
				'supplier'	 	  => $this->_post('supplier'),
				'create_time'	  => time(),
		);
		$res = $this->inventory_conf_model->add($data);
		if($res){
			$this->success('新增采购配置成功',U('Storehouse/Inventorybasemanage/index/'));
		}else{
			$this->error('新增采购配置失败');
		}		
	}
	
	public function del(){
		$id = $this->_get('id');
		$res = $this->inventory_conf_model->where(array('id'=>$id))->delete();
		if($res){
			$this->success('删除配置成功',U('Storehouse/Inventorybasemanage/index/'));
		}else{
			$this->error('删除配置失败');
		}
	}

}
