<?php
//用户个人中心controller
class MyhomeAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		if( true !== $this->login()){
			exit;
		}
	}
	public function index(){
	    $tab_n = isset($_COOKIE['tab_n'])?$_COOKIE['tab_n']:0;
	    $this->assign('tab_n',$tab_n);
	    Cookie::set('_currentUrl_', __SELF__);
		R('Order/orderlist'); 
	}

    public function orderlist(){
        R('Order/orderlist');
    }
    
    public function show_order_detail(){
        R('Order/show_order_detail');
    }
}