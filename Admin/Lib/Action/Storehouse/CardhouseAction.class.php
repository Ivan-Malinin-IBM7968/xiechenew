<?php

class CardhouseAction extends CommonAction {	
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
		$where = array('tp_xieche.xc_cardrecord.type'=>2);
		if( !empty($_POST['type']) ){
			$where['tp_xieche.xc_cardinfo.type'] = $_POST['type'];
			$this->assign('type',$_POST['type']);
		}
		if( !empty($_POST['id']) ){
			$where['tp_xieche.xc_cardinfo.id'] = $_POST['id'];
			$this->assign('id',$_POST['id']);
		}
		if( !empty($_POST['use_way']) ){
			$where['tp_xieche.xc_info.use_way'] = $_POST['use_way'];
			$this->assign('use_way',$_POST['use_way']);
		}
		if( !empty($_POST['name']) ){
			$where['tp_xieche.xc_cardinfo.name'] = $_POST['name'];
			$this->assign('name',$_POST['name']);
		}
		if( !empty($_POST['start_time']) ){
			$where['tp_xieche.xc_cardinfo.create_time'][] = array('egt',strtotime($_POST['start_time']));;
			$this->assign('start_time',$_POST['start_time']);
		}
		if( !empty($_POST['end_time']) ){
			$where['tp_xieche.xc_cardinfo.create_time'][] = array('elt',strtotime($_POST['end_time']));
			$this->assign('end_time',$_POST['end_time']);
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
		$list = $this->mCardrecord->field('xc_cardrecord.*,xc_cardinfo.type,xc_cardinfo.name,xc_cardinfo.num,xc_cardinfo.cate_id,xc_cardinfo.id as info_id')->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardrecord.info_id = tp_xieche.xc_cardinfo.id')->order('xc_cardrecord.id DESC')->limit($p->firstRow.','.$p->listRows)->select();

        if ($list) {
        	$mAdmin = M('tp_admin.user','xc_');
        	foreach ($list as &$val){
        		$cate = $this->mCardcate->field('ic')->where( array('id'=>$val['cate_id']) )->find();
        		$val['ic'] = $cate['ic'];
        		
        		$cardId = $this->mCardlist->field('ic_id')->where( array('info_id'=>$val['info_id']) )->order('id asc')->find();//获取编码id
        		$ic = $val['ic'].$cardId['ic_id'];
        		$cardId = $this->mCardlist->field('ic_id')->where( array('info_id'=>$val['info_id']) )->order('id desc')->find();
        		$ic .= '-'.$val['ic'].$cardId['ic_id'];
        		$val['ic'] = $ic;
        		
        		$username = $mAdmin->field('nickname')->where(array('id'=>$val['operator_person']))->find();
        		$val['username'] = $username['nickname'];
        		unset($val);	
        	}
        }
       
		$this->assign('list',$list);
        $this->assign('page',$page);
		$this->display();
	}
	public function housedetail(){
		$where = array();
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
		if( !empty($_POST['start_time']) ){
			$where['tp_xieche.xc_cardinfo.create_time'][] = array('egt',strtotime($_POST['start_time']));;
			$this->assign('start_time',$_POST['start_time']);
		}
		if( !empty($_POST['end_time']) ){
			$where['tp_xieche.xc_cardinfo.create_time'][] = array('elt',strtotime($_POST['end_time']));
			$this->assign('end_time',$_POST['end_time']);
		}
		
		$use_way = $this->mCardcate->distinct(true)->field('use_way')->select();
		$this->assign('use_way_list',$use_way);
		
		$name = $this->mCardinfo->distinct(true)->field('name')->select();
		$this->assign('name_list',$name);
		// 计算总数
		$count = $this->mCardhouse->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardhouse.info = tp_xieche.xc_cardinfo.id')->count();
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
		$list = $this->mCardhouse->field('xc_cardhouse.*,xc_cardinfo.type,xc_cardinfo.name,xc_cardinfo.price,xc_cardinfo.cate_id')->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardhouse.info_id = tp_xieche.xc_cardinfo.id')->order('xc_cardhouse.id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		if ($list) {
			foreach ($list as &$val){
				if (!empty($val['cate_id'])) {
					$icData = $this->mCardcate->field('ic')->where(array('id'=>$val['cate_id']))->find();
					$val['ic'] = $icData['ic'];
				}
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
		$where = array('status'=> 1);
		$list = $this->mCardinfo->where($where)->select();
		if ($list) {
			foreach ( $list as &$val){
				$cateData = $this->mCardcate->field('ic')->where( array('id'=>$val['cate_id']))->find();
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
		$idArr = explode('|', $ids);
		$res = $up = $record = '';
		foreach ($idArr as $id){
			if (!$id) {
				continue;
			}
			$num = $this->mCardinfo->field('num')->where( array('id'=>$id))->find();
			$data = array(
					'info_id' => $id,
					'num' => $num['num'],
					'remark' => $remark,
					'create_time' => time()
			);
			$res = $this->mCardhouse->add($data);//入库
			if ($res) {
				//更新入库状态
				$update = array(
					'status'=>2
				);
				$up = $this->mCardinfo->where( array('id'=>$id))->save($update);
				
				//库存记录 
				$recordData = array(
						'info_id' => $id,
						'type'=>2,
						'create_time'=>time(),
						'operator_person'=>$_SESSION['authId'],
						'remark' => $remark
				);
				$record = $this->mCardrecord->add($recordData);
				//更新卡状态
				$this->mCardlist->where( array('info_id'=>$id) )->save( array('status'=>2,'record_id'=>$record) );
			}
			
		}
		if($record){
			//TODO::更新卡列表的数据 按照info id 来更新
			//程序日志
			$array = array(
					'oid'=>$res,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作入库:'.$res
			);
			$this->addOperateLog($array);
			$this->success('操作入库成功',U('Storehouse/Cardhouse/index/'),true);
		}else{
			$this->error('操作入库失败','',true);
		}		
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
