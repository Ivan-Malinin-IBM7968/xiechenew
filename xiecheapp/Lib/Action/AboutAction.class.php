<?php
/*CLASS:*/
class AboutAction extends CommonAction {
    function __construct() {
		parent::__construct();
	}
	
	/*
		@author:chf
		@function:显示关于我们
		@time:2013-07-11
	*/
	function a4(){
		
		$this->display('4');
	}


	/*
		@author:chf
		@function:显示联系我们
		@time:2013-07-11
	*/
	function a16(){
		$this->display('16');
	}
	
	/*
		@author:chf
		@function:显示服务协议
		@time:2013-07-11
	*/
	function a24(){
		$this->display('24');
	}

	/*
		@author:chf
		@function:显示服务协议
		@time:2013-07-11
	*/
	function a9(){
		$title = '如何通过纵横携车网进行维修保养预约？|';
	    $this->assign('title',$title);
	    $this->display('9'); 
	}

	function a2(){
		$this->display('2');
	}
}

?>