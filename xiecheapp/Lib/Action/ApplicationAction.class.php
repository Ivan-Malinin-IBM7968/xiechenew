<?php
//订单
class ApplicationAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
	}



	function index(){

		$this->assign('title',"携车网手机客户端下载-携车网");
		$this->display();
	
	}





}