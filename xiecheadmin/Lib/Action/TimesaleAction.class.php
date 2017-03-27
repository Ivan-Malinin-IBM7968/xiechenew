<?php
/*
 * 分时折扣
 */
class TimesaleAction extends CommonAction {
	
	public function listtimesale() {
		$model_shop = D('Shopmain');
		$list = $model_shop->select();
		$this->assign('shop',$list);
		//dump($list);
		$model_timesale = D('timesale');
		$this->_list($model_timesale);
		$this->display();
	}
	
	public function _before_insert(){
		if($this->isPost()){
			$_POST['begin_time']=$_POST['start_hours'].':'.$_POST['start_minute'];
			$_POST['end_time']=$_POST['end_hours'].':'.$_POST['end_minute'];
		}
	}
	public function _before_edit(){
		$model_shop = D('Shopmain');
		$list = $model_shop->select();
		$this->assign('shop',$list);
	}
	public function _before_update(){
		if($this->isPost()){
			$_POST['begin_time']=$_POST['start_hours'].':'.$_POST['start_minute'];
			$_POST['end_time']=$_POST['end_hours'].':'.$_POST['end_minute'];
			dump($_POST);
		}
	}
}
