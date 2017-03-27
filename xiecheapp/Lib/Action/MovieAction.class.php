<?php
//微电影
class MovieAction extends CommonAction {
	protected $offlinespread;
	protected $mBidorder;
	protected $mRepairprocess;
	function __construct() {
		parent::__construct();
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	}
	
	/*
	@function： 微电影
	@author：bright
	@time：2014-9-18
	*/
	function index(){
// 		$v_name = @$_GET['v_name'];
// 		if(!$v_name){
// 			$this->error('参数为空');
// 		}
// 		$this->assign('v_name',$v_name);
		$this->display();
	}
	
	/*
	@function： 微电影
	@author：wwy
	@time：2014-12-25
	*/
	function vod(){
		//获取播放链接
		if($_REQUEST['order_id']){
			$url = "http://s.2xq.com:5099/get_order_video?order_id=".$_REQUEST['order_id'];
		}else{
			$url = "http://s.2xq.com:5099/get_order_video?order_id=1470";
		}
		$strm = stream_context_create(array('http' => array('method'=>'GET', 'timeout' => 15)) );
		$result = file_get_contents($url,false,$strm);
		$result = json_decode($result,true);
		//print_r($result);

		if($result['status'] == 1){
			//echo 'url='.$result['data']['0'];
			$this->assign('url',$result['data']['0']);
		}else{
			$this->error('打开失败');
		}
		$this->display();
	}
}