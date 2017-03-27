<?php

class IndexAction extends CommonAction {
	// 框架首页
	public function index() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			echo 1111;exit;
			$this->assign('jumpUrl',__APP__.'/Public/login');
			header('Location:'.__APP__.'/Public/login');
			//$this->error('没有登录');
		}
		$this->display();
	}
	// 首页
	public function main() {
		// 统计数据
		$this->display();
	}

}
?>