<?php
/*
 * 用户管理
 */
class BaiduAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->baiduModel = D('baidu');//百度监控表
	}


	function index(){
		if($_REQUEST['create_start_time'] && $_REQUEST['create_end_time']){
			$map['create_time'] = array(array('lt',strtotime($_REQUEST['create_end_time'])),array('gt',strtotime($_REQUEST['create_start_time'])),'AND');
			$data['create_start_time'] = $_REQUEST['create_start_time'];
			$data['create_end_time'] = $_REQUEST['create_end_time'];
		}
		
		$count = $this->baiduModel->where($map)->count();
		// 导入分页类
		import("ORG.Util.Page");
		// 实例化分页类
		$p = new Page($count, 25);
		// 分页显示输出
		$page = $p->show_admin();
		$data['list'] = $this->baiduModel->where($map)->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();

		$this->assign('data',$data);
		$this->assign('page',$page);
		$this->display();
	}


  

}
