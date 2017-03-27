<?php
/*
 * 用户管理
 */
class MembernewAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->MembernewModel = D('membernew');//优惠卷信息
		
	}


	function index(){

		$count = $this->MembernewModel->count();
		//导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count,20);
		// 分页显示输出
		$page = $p->show_admin();
		$data = $this->MembernewModel->where($map)->order('addtime DESC')->limit($p->firstRow.','.$p->listRows)->select();
		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
	}
    

}
