<?php
class UserlogAction extends CommonAction {
    function __construct() {
		parent::__construct();
	}
	
    public function index(){
        $userlogModel = M('tp_admin.user_log','xc_'); 
        // 计算总数
        $count = $userlogModel->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 20);
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $userlogModel->order('id DESC')->limit($p->firstRow.','.$p->listRows)->select();
		
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);
        $this->display();
    }
    
    
}
?>