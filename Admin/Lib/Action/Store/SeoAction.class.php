<?php
class SeoAction extends CommonAction {
    function __construct() {
		parent::__construct();
		$this->SeoModel = D('seo');//店铺表
	}
	
   function index(){
	$data['seo'] = $this->SeoModel->select();
    $this->assign('data',$data);
	$this->display();
   }
    
}
?>