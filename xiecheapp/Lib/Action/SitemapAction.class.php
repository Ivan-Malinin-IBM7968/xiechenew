<?php
/*CLASS:*/
class SitemapAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->ArticleModel = D('article');//用车心得
		$this->NoticeModel = D('notice');//优惠速递
		$this->couponModel = D('coupon');//4S售后优惠
		$this->shopModel = D('shop');//4S售后预约
		$this->_empty();
		exit();
	}
	
	/*
		@author:chf
		@function:显示关于我们
		@time:2013-07-11
	*/
	function index(){
		$data['Article'] = $this->ArticleModel->order('create_time DESC')->limit(5)->select();
		$data['Notice'] = $this->NoticeModel->order('update_time DESC')->limit(5)->select();
		$data['coupon'] = $this->couponModel->where(array('is_delete'=>0))->order('id DESC')->limit(5)->select();
		$data['shop'] = $this->shopModel->where(array('shop_city'=>3306))->order('id DESC')->limit(5)->select();
		$this->assign('data',$data);
		$this->display('sitemap');
	}

	function test(){
	
		echo "123";
	}


}

?>