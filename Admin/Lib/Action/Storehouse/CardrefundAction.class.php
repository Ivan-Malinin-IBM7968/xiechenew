<?php

class CardrefundAction extends CommonAction {	
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
		$where = array('tp_xieche.xc_cardrecord.type'=>5);
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
		$list = $this->mCardrecord->field('xc_cardrecord.*,xc_cardinfo.type,xc_cardinfo.name,xc_cardinfo.num,xc_cardinfo.cate_id')->where($where)->join('LEFT JOIN tp_xieche.xc_cardinfo ON tp_xieche.xc_cardrecord.info_id = tp_xieche.xc_cardinfo.id')->order('xc_cardrecord.id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        if ($list) {
        	$mAdmin = M('tp_admin.user','xc_');
        	foreach ($list as &$val){
        		$cateData = $this->mCardcate->field('use_way')->where( array('id'=>$val['cate_id']))->find();
        		if ($cateData) {
        			$val['use_way'] = $cateData['use_way'];
        		}
        		
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
		$where = array('status'=> 3);//出库以后的数据才可以退卡
		$list = $this->mCardinfo->where($where)->select();
		if ($list) {
			foreach ( $list as &$val){
				//找到卡列表里面的编码
				$listData = $this->mCardlist->field('id,ic_id')->where( array('info_id'=>$val['id'],'status'=>3) )->select();
				$val['cardListData'] = $listData;
				
				$cateData = $this->mCardcate->field('ic,use_way')->where( array('id'=>$val['cate_id']))->find();
				if ($cateData) {
					$val['ic'] = $cateData['ic'];
				}
				//可退卡数量
				$val['record_num'] = count($listData);
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
		$num = $this->_post('num');//退卡数量
		$idArr = explode('|', $ids);
		
		$list = $this->_post('list');
		if (!$list) {
			$this->error('操作退卡单失败,参数错误','',true);
		}
		$list = rtrim($list,'|');
		$listIdArr = explode('|',$list);
		
		$listNum = count($listIdArr);
		
		if ($num != $listNum) {
			$this->error('操作退卡单失败,填的数量和勾的数量不相同','',true);
		}
		
		$res = $up = $record = '';

		foreach ($idArr as $id){
			if (!$id) {
				continue;
			}
			//可退的数量
			$data = $this->mCardlist->field('record_num')->where( array('info_id'=>$id,'status'=>3) )->count();
			if (!$data || $data<$num) {
				$this->error('退卡数量超过了领卡数量','',true);
			}
			
			//退卡加库存
			$this->mCardhouse->where(array('info_id'=>$id))->setInc('num',$num);
			
			//退卡记录
			$recordData = array(
					'info_id' => $id,
					'type'=>5,
					'create_time'=>time(),
					'operator_person'=>$_SESSION['authId'],
					'remark' => $remark,
					'sale_type' => $type,
					'record_num'=>$num,
					'receive_person'=>$receive_person
			);
			$record = $this->mCardrecord->add($recordData);
			//更新卡状态
			foreach ($listIdArr as $listId){
				if (!$listId) {
					continue;
				}
				$this->mCardlist->where( array('id'=>$listId) )->save( array('status'=>5,'record_id'=>$record) );
			}
			
		}
		if($record){
			//程序日志
			$array = array(
					'oid'=>$res,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作出库:'.$res
			);
			$this->addOperateLog($array);
			$this->success('操作退卡成功',U('Storehouse/Cardhouse/index/'),true);
		}else{
			$this->error('操作退卡失败','',true);
		}		
	}

}
