<?php

class CardsaleAction extends CommonAction {	
	protected $mCardcate;
	protected $mCardhouse;
	protected $mCardinfo;
	protected $mCardlist;
	protected $mCardrecord;
	function __construct() {
		parent::__construct();
        $this->mCardcate = M('tp_xieche.cardcate','xc_');  //卡类别配置表
        $this->mCardhouse = M('tp_xieche.cardhouse','xc_');//卡仓库表
        $this->mCardinfo = M('tp_xieche.cardinfo','xc_');//卡类型信息表
        $this->mCardlist = M('tp_xieche.cardlist','xc_');//卡列表
        $this->mCardrecord = M('tp_xieche.cardrecord','xc_');  //卡进出库记录表
	}
	
	public function index() {
		$where = array('tp_xieche.xc_cardrecord.type'=>4);
		if( !empty($_POST['type']) ){
			$where['tp_xieche.xc_cardinfo.type'] = $_POST['type'];
			$this->assign('type',$_POST['type']);
		}
		if( !empty($_POST['use_way']) ){
			$where['tp_xieche.xc_info.use_way'] = $_POST['use_way'];
			$this->assign('use_way',$_POST['use_way']);
		}
		if( !empty($_POST['name']) ){
			$where['tp_xieche.xc_cardinfo.name'] = $_POST['name'];
			$this->assign('name',$_POST['name']);
		}
		$use_way = $this->mCardcate->distinct(true)->field('use_way')->select();
		$this->assign('use_way_list',$use_way);
		
		$name = $this->mCardinfo->distinct(true)->field('name')->select();
		$this->assign('name_list',$name);
		// 计算总数
		$count = $this->mCardrecord->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardrecord.info = tp_xieche.xc_cardinfo.id')->count();
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
		$list = $this->mCardrecord->field('xc_cardrecord.*,xc_cardinfo.type,xc_cardinfo.name,xc_cardinfo.num,xc_cardinfo.use_way')->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardrecord.info_id = tp_xieche.xc_cardinfo.id')->order('xc_cardrecord.id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if ($list) {
        	$mAdmin = M('tp_admin.user','xc_');
        	foreach ($list as &$val){
        		$username = $mAdmin->field('nickname')->where(array('id'=>$val['operator_person']))->find();
        		$val['username'] = $username['nickname'];
        		unset($val);	
        	}
        }
       
		$this->assign('list',$list);
        $this->assign('page',$page);
		$this->display();
	}
	
	public function del(){
		$id = $_GET['id'];
		$ret = $this->mCardinfo->delete(array('id'=>$id));
		if($ret){
			$this->success('删除成功',U('Storehouse/Cardcate/index/'));
		}else{
			$this->error('删除失败');
		}
	}
	public function add(){
		$where = array('store_status'=> array('neq',1) );//出库以后的数据才可以销售
		$list = $this->mCardinfo->where($where)->select();
		if ($list) {
			foreach ( $list as &$val){
				//找到卡列表里面的编码
				$listData = $this->mCardlist->field('id,ic_id')->where( array('info_id'=>$val['id'],'status'=>3) )->select();
				$val['cardListData'] = $listData;
				
				$cateData = $this->mCardcate->field('ic')->where( array('id'=>$val['cate_id']))->find();
				//如果销售数量大于了采购数量 那么就不能销售了
				$recordDataArr = $this->mCardrecord->field('record_num')->where( array('info_id'=>$val['id'],'type'=>4) )->select();
				$val['show'] = 1;
				if ( $recordDataArr ) {
					$sale_num = 0;
					foreach ($recordDataArr as $recordData){
							$sale_num += $recordData['record_num'];
					}
					if ($sale_num > $val['num']) {
						$val['show'] = 2;
					};
				}
				if ($cateData) {
					$val['ic'] = $cateData['ic'];
				}
				unset($val);
			};
		}
		$this->assign('list', $list);
		
		$this->assign('time', time());
		$this->display();
	}
	public function do_add(){
		$ids = $this->_post('id');
		$remark = $this->_post('remark');
		$type = $this->_post('type');
		$receive_person = $this->_post('receive_person');
		$idArr = explode('|', $ids);
		$list = $this->_post('list');
		if (!$list) {
			$this->error('操作销售单失败,参数错误','',true);
		}
		$list = rtrim($list,'|');
		$listIdArr = explode('|',$list);
		
		$res = $up = $record = '';
		$allNum = 0;
		//校验
		foreach ($idArr as $key => $idAndNum){
			if (!$idAndNum) {
				continue;
			}
			
			$tmpArr = explode(':',$idAndNum);
			$id = $tmpArr[0];
			$num = $tmpArr[1];
			if (!$id || !$num) {
				$this->error('操作出库失败,参数错误','',true);
			}
			
			$infoData = $this->mCardinfo->field('num')->where( array('id'=>$id) )->find();
			$storeNum = $infoData['num'];
			//如果有已经销售的，那么还要减去已经销售的数量才是库存的数量
			$recordData = $this->mCardrecord->field('record_num')->where( array('info_id'=>$id,'type'=>4) )->select();
			if ($recordData) {
				$sale_num = 0;
				foreach ($recordData as $saleNumArr){
					$sale_num += $saleNumArr['record_num'];
				}
				$storeNum -= $sale_num;
			}
			//var_dump($storeNum);exit;
			if ($num - $storeNum > 0) {
				$this->error('操作销售单失败,数量大于库存数量','',true);
			}
			$allNum +=$num;
		}
		
		$listNum = count($listIdArr);
		if ($allNum != $listNum) {
			$this->error('操作销售单失败,填的数量和勾的数量不相同','',true);
		}
		foreach ($idArr as $idAndNum){
			if (!$idAndNum) {
				continue;
			}
				
			$tmpArr = explode(':',$idAndNum);
			$id = $tmpArr[0];
			$num = $tmpArr[1];
			//更新为销售状态
			$update = array(
				'status'=>4
			);
			//$up = $this->mCardinfo->where( array('id'=>$id))->save($update);
			//更新卡状态
			foreach ($listIdArr as $listId){
				if (!$listId) {
					continue;
				}
				$this->mCardlist->where( array('id'=>$listId) )->save( array('status'=>4) );
			}
			//销售记录
			$recordData = array(
					'info_id' => $id,
					'type'=>4,
					'create_time'=>time(),
					'operator_person'=>$_SESSION['authId'],
					'remark' => $remark,
					'sale_type' => $type,
					'record_num'=>$num,
					'receive_person'=>$receive_person
			);
			$record = $this->mCardrecord->add($recordData);
			
			if($record){
				//程序日志
				$array = array(
						'oid'=>$res,
						'operate_id'=>$_SESSION['authId'],
						'log'=>'操作销售单:'.$res
				);
				$this->addOperateLog($array);
			}else{
				$this->error('操作销售单失败','',true);
			}
		}
		$this->success('操作销售单成功',U('Storehouse/Cardhouse/index/'),true);
	}
	public function edit(){
		$list = $this->mCardinfo->where(array('id'=>$_GET['id']))->find();
		$this->assign('id',$_GET['id']);
		$this->assign('list',$list);
		$this->display();
	}
	public function do_edit(){
		$id = $this->_post('id');
		$use_way = $this->_post('use_way');
		$type = $this->_post('type');
		$name = $this->_post('name');
		$num = $this->_post('num');
		$price = $this->_post('price');
	
		$data = array(
				'name'		 => $name,
				'use_way'    =>	$use_way,
				'type'	 	 =>	$type,
				'name'	 	 =>	$name,
				'num'	 	 =>	$num,
				'price'	 	 =>	$price,
				'user_id'	 => $_SESSION['authId'],
		);
		$res = $this->mCardinfo->where(array('id'=>$id))->save($data);
		if($id){
			//程序日志
			$array = array(
					'oid'=>$res,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作编辑卡类别:'.$res
			);
			$this->addOperateLog($array);
			$this->success('编辑卡类别成功',U('Storehouse/Cardinfo/index/'));
		}else{
			$this->error('编辑卡类别失败');
		}
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
