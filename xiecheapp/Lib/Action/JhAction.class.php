<?php
//订单
class JhAction extends CommonAction {	
	public function __construct() {
		parent::__construct();
	}


    public function index(){
		$this->display();
	}

}