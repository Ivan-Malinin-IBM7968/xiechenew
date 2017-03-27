<?php
//提问
namespace Xiecheapp\Controller;

class CaseController extends CommonController {
	function __construct() {
		parent::__construct();
		$this->assign('noshow',true);
		$this->assign('noclose',true);
	}
	
	public function index(){
		$this->display('index');
	}
	


}