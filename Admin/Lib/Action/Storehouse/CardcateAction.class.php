<?php

class CardcateAction extends CommonAction {	
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
	//采购列表
	public function index() {
		$where = array();
// 		if( !empty($_POST['create_time']) ){
// 			$where['create_time'] = strtotime($_POST['create_time']);
// 			$this->assign('create_time',$_POST['create_time']);
// 		}
// 		if( !empty($_POST['name']) ){
// 			$where['name'] = array('like','%'.$_POST['name'].'%');
// 			$this->assign('name',$_POST['name']);
// 		}
		// 计算总数
		$count = $this->mCardcate->where($where)->count();
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
		$list = $this->mCardcate->where($where)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$page);
		$this->display();
	}
	public function del(){
		$id = $_GET['id'];
		$ret = $this->mCardcate->delete(array('id'=>$id));
		if($ret){
			$this->success('删除成功',U('Storehouse/Cardcate/index/'));
		}else{
			$this->error('删除失败');
		}
	}
	public function add(){
		$this->display();
	}
	public function do_add(){
		$use_way = $this->_post('use_way');
		$ic = $this->_post('ic');
		$name = $this->_post('name');
		
		$data = array(
				'name'		 => $name,
				'use_way'    =>	$use_way,
				'ic'	 	 =>	$ic,
				'create_time'=> time(),
		);
		$res = $this->mCardcate->add($data);
		if($res){
			//程序日志
			$array = array(
					'oid'=>$res,
					'operate_id'=>$_SESSION['authId'],
					'log'=>'操作新增卡类别:'.$res
			);
			$this->addOperateLog($array);
			$this->success('新增卡类别成功',U('Storehouse/Cardcate/index/'));
		}else{
			$this->error('新增卡类别失败');
		}		
	}

}
