<?php
//订单
class PainterfaceAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		$this->PadataModel = D('Padata');
	}
	
	function index() {
		if($_POST) {
			$data['create_time'] = time();
			$data['text'] = serialize($_POST);
			$this->PadataModel->add($data);
		}
	}

	function wx_myhome() {
		
	}
	
}