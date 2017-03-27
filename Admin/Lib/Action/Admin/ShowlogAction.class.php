<?php
//操作日志
class ShowlogAction extends CommonAction {
	function __construct() {
		parent::__construct();
		$this->operatelog_model = M('tp_xieche.operatelog','xc_');  //操作日志表
        
	}
	public function index(){
// 		$where = array(
// 				'control' => $this->getActionName(),
// 				'function' => ACTION_NAME,
// 				'oid'=> $id
// 		);
		$where = array();
		if (!empty($_POST['c'])) {
			$where['control'] = $_POST['c'];
		}
		if (!empty($_POST['m'])) {
			$where['function'] = $_POST['m'];
		}
		if (!empty($_POST['id'])) {
			$where['oid'] = $_POST['id'];
		}	
		if ($where) {
			$res = $this->getloglist($where);
			echo json_encode($res);
		}
	}

}
?>