<?php

class StorehousemanageAction extends CommonAction {	
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
		$where = array();
		if( !empty($_POST['create_time']) ){
			$where['create_time'] = strtotime($_POST['create_time']);
			$this->assign('create_time',$_POST['create_time']);
		}
		if( !empty($_POST['name']) ){
			$where['name'] = array('like','%'.$_POST['name'].'%');
			$this->assign('name',$_POST['name']);
		}
		// 计算总数
		$count = $this->storehouse_model->where($where)->count();
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
		$storehouse_list = $this->storehouse_model->where($where)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		foreach ($storehouse_list as &$val){
			$val['now_count'] = $this->storehouse_item_model->where( array('s_id'=>$val['id'],'lock_status'=>1) )->count();
			unset($val);
		}
        $this->assign('storehouse_list',$storehouse_list);
        $this->assign('page',$page);
		$this->display();
	}
	public function add(){
		$list = $this->inventory_conf_model->distinct(true)->field('supplier')->select();//仓库配置信息
		$oil = $this->item_oil_model->select();//机油信息
		foreach ($oil as $key=>&$val){
			$val['name'] = $val['name'].'('.$val['norms'].'L)';
			unset($v);
		}
		$this->assign('list',$list);
		$this->assign('oil',$oil);
		$this->display();
	}
	public function do_add(){
		$price = $this->_post('price');
		$num = $this->_post('num');
		$total = $price * $num;
		list($name,$item_id) = explode('||', $this->_post('name'));
		$data = array(
				'name'		 => $name,
				'item_id'	 => $item_id,
				'super_cate' => $this->_post('super_cate'),
				'price'      => $price,
				'num'		 => $num,
				'norms'		 =>	$this->_post('norms'),
				'supplier'	 =>	$this->_post('supplier'),
				'remark'	 =>	$this->_post('remark'),
				'create_time'=> time(),
				'total_price'=> $total
		);
		$res = $this->storehouse_model->add($data);
		if($res){
			$data['num'] = 1;
			$data['s_id'] = $res;
			for ($i=0;$i<$num;$i++){
				$insert_item = $this->storehouse_item_model->add($data);
				if (!$insert_item) {
					$this->error('新增采购数据失败');
				}
			}
			//程序日志
			$array = array(
					'oid'=>$res,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作新增采购单:'.$res
			);
			$this->addOperateLog($array);
			$this->success('新增采购单成功',U('Storehouse/Storehousemanage/index/'));
		}else{
			$this->error('新增采购单失败');
		}		
	}
	public function edit(){
		$id = $this->_get('id');
		$suppliers = $this->inventory_conf_model->distinct(true)->field('supplier')->select();//供应商列表 只让他选
		$store_status = array(1=>'未入库',2=>'入库');	//库存状态
		$list = $this->storehouse_model->where(array('id'=>$id))->find();
		$this->assign('list',$list);
		$this->assign('store_status',$store_status);
		$this->assign('suppliers',$suppliers);
		$this->display();
	}
	public function do_edit(){
		$id = $this->_post('id');
		$price = $this->_post('price');
		$num = $this->_post('num');
		$total = $price * $num;
		
		$data = array(
				'name'		 => $this->_post('name'),
				'super_cate' => $this->_post('super_cate'),
				'price'      => $price,
				'num'		 => $num,
				'unit'		 =>	$this->_post('unit'),
				'norms'		 =>	$this->_post('norms'),
				'supplier'	 =>	$this->_post('supplier'),
				'remark'	 =>	$this->_post('remark'),
				'store_status'=>$this->_post('store_status'),
				'update_time'=> time(),
				'total_price'=> $total
		);
		
		$res = $this->storehouse_model->where(array('id'=>$id))->save($data);
		if($res){
			//程序日志
			$array = array(
					'oid'=>$id,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作修改采购单:'
			);
			$this->addOperateLog($array);
			$this->success('修改采购单成功',U('Storehouse/Storehousemanage/index/'));
		}else{
			$this->error('修改采购单失败');
		}
	}
	public function get_data_by_item(){
		$item = $this->_post('item');
		if ($item == 1) {
			$res = $this->item_oil_model->select();
			foreach ($res as $key=>&$val){
				$val['name'] = $val['name'].'('.$val['norms'].'L)';
				unset($v);
			}
		}else{
			$res = $this->item_model->where(array('type_id'=>$item))->select();
		}
		echo json_encode($res);
	}
	
	public function get_name_by_item(){
		$item = $this->_post('item');
		$res = $this->storehouse_model->where(array('super_cate'=>$item))->select();
		echo json_encode($res);
	}
	//退货
	public function return_goods(){
		$list = $this->storehouse_model->where(array('super_cate'=>1))->select();//机油信息
		$this->assign('list',$list);
		$this->display();
	}
	//下退货单
	public function do_return(){
		$id = $this->_post('id');
		$where = array(
				'id'=>$id,
		);
		$msg = date('Y-m-d H:i:s',time()).$_SESSION['loginAdminUserName'].'操作退货 ';
		$num = (int)$this->_post('num');
		$data =array(
				'msg'=>$msg.'退货数量:'.$num,
				'update_time'=>time(),
		);
		
		$update_arr = array(
				'store_status'=>$this->_post('store_status'),
				'msg'=>$msg,
				'update_time'=>time()
		);
		
		$count = $this->storehouse_item_model->where(array('s_id'=>$id,'store_status'=>array('NOT IN','4,5'),'lock_status'=>1))->count();
		//var_dump($this->storehouse_item_model->getLastSql());exit;
		if($count < $num){
			$this->error('添加退货单失败,没有那么多库存可以退');
		}
		$res = $this->storehouse_item_model->where(array('s_id'=>$id,'store_status'=>array('NOT IN','4,5'),'lock_status'=>1))->limit($num)->save($update_arr);	//退单个总的
		if($res){
			$update = $this->storehouse_model->where($where)->save($data);//退总的
			$update2 = $this->storehouse_model->where($where)->setDec('num',$num);
			if($update && $update2){
				//程序日志
				$array = array(
						'oid'=>$id,
						'operate_id'=>$_SESSION['authId'],
						'log'=>'操作添加退货单:退货数量：'.$num
				);
				$this->addOperateLog($array);
				$this->success('添加退货单成功',U('Storehouse/Storehousemanage/index/'));
			}else{
				$this->error('添加退货单失败');
			}
		}else{
			$this->error('添加退货单失败');
		}
	}

}
