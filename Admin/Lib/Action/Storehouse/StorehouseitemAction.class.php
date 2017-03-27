<?php

class StorehouseitemAction extends CommonAction {	
	protected $item_model;
	protected $storehouse_model;
	protected $inventory_conf_model;
	protected $storehouse_item_model;
	protected $item_oil_model;
	function __construct() {
		parent::__construct();
        $this->item_model = M('tp_xieche.item_filter','xc_');  //机滤，空滤，表
        $this->storehouse_model = M('tp_xieche.storehouse','xc_');//仓库表
        $this->storehouse_item_model = M('tp_xieche.storehouse_item','xc_');//仓库数据详情表
        $this->inventory_conf_model = M('tp_xieche.inventory_conf','xc_');//仓库配置表
        $this->item_oil_model = M('tp_xieche.item_oil','xc_');  //保养机油
	}
	//采购列表
	public function index() {
		//搜索
		$where = array();
		if( !empty($_POST['super_cate']) ){
			$where['super_cate'] = $_POST['super_cate'];
			$this->assign('super_cate',$where['super_cate']);
		}
		if( !empty($_POST['sale_price']) && $_POST['sale_price'] ==2 ){
			//$where['sale_price'] = array('NEQ','NULL');
			$this->assign('sale_price',$_POST['sale_price']);
		}else{
			$where['sale_price'] = array('NEQ','NULL');
			$this->assign('sale_price',1);
		}
		if( !empty($_POST['store_status']) ){
			$where['tp_xieche.xc_storehouse_item.store_status'] = $_POST['store_status'];
			$this->assign('store_status',$_POST['store_status']);
		}
		// 计算总数
		$count = $this->storehouse_item_model->where($where)->join('LEFT JOIN tp_xieche.xc_storehouse ON tp_xieche.xc_storehouse_item.s_id = tp_xieche.xc_storehouse.id')->count();
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
		$list = $this->storehouse_item_model->field('tp_xieche.xc_storehouse.super_cate,tp_xieche.xc_storehouse_item.*')->where($where)->join('LEFT JOIN tp_xieche.xc_storehouse ON tp_xieche.xc_storehouse_item.s_id = tp_xieche.xc_storehouse.id')->order('tp_xieche.xc_storehouse_item.order_id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//var_dump($this->storehouse_item_model->getLastSql());exit;
		$this->assign('list',$list);
        $this->assign('page',$page);
		$this->display();
	}
	
	
	public function edit(){
		$id = $this->_get('id');
		$suppliers = $this->storehouse_item_model->distinct(true)->field('supplier')->select();//供应商列表 只让他选
		//$store_status = array(1=>'未入库',2=>'入库',3=>'出库',4=>'退货未退款',5=>'退货已退款');	//库存状态
		$store_status = array(1=>'未入库',2=>'入库',3=>'出库');
		$list = $this->storehouse_item_model->where(array('id'=>$id))->find();
		$this->assign('list',$list);
		$this->assign('store_status',$store_status);
		$this->assign('suppliers',$suppliers);
		$this->display();
	}
	public function do_edit(){
		$id = $this->_post('id');
		
		$data = array(
				'name'		 => $this->_post('name'),
				'suppliers' => $this->_post('suppliers'),
				'unit'      => $this->_post('unit'),
				'price'		 =>	$this->_post('price'),
				'sale_price'	 =>	$this->_post('sale_price'),
				'store_status'	 =>	$this->_post('store_status'),
				'remark'=>$this->_post('remark'),
				'update_time'=> time(),
				'total_price'=> $total
		);
		
		$res = $this->storehouse_item_model->where(array('id'=>$id))->save($data);
		if($res){
			//程序日志
			$array = array(
					'oid'=>$id,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作修改销售单'
			);
			$this->addOperateLog($array);
			$this->success('修改销售单成功',U('Storehouse/Storehouseitem/index/'));
		}else{
			$this->error('修改销售单失败');
		}
	}
	

}
