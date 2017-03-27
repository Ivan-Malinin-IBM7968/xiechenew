<?php
//首页
class LotteryAction extends CommonAction {
	public function __construct(){
		parent::__construct();
		$this->MemberModel = D('Member');//用户表
		$this->MembersalecouponModel = D('Membersalecoupon');//抵用券表
		$this->model_sms = D('Sms');
		$this->LotteryModel = D('lottery');//抽奖监控表
		$this->LotteryloginModel = D('lotterylogin');//抽奖页登录监控表
		$this->PadataModel = D('Padatatest');//测试接收微信订单数据表
		$this->PaweixinModel = D('paweixin');//携车手机微信比对表
		$this->LotteryappModel = D('lotteryapp');//下载APP登录监控表
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	}

	/*	
		@author:ysh
		@function:
		@time:2013/12/16
	*/
	function indexmobile(){

		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		
		$this->display('indexmobile');
	}


	/*	
		@author:chf
		@function:
		@time:2013-09-24
	*/
	function indexfby(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$_SESSION['from'] = $lotteylogion['from'];
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = 'wx';//来源
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display('indexfby');
	}


	/*	
		@author:chf
		@function:
		@time:2013-09-24
	*/
	function indexcsh(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$_SESSION['from'] = $lotteylogion['from'];
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = 'wx';//来源
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display('indexcsh');
	}

	/*	
		@author:chf
		@function:显示百度(百度SEO)
		@time:2013-09-24
	*/
	function indexbd(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$_SESSION['from'] = $lotteylogion['from'];
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = 'wx';//来源
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:显示抽奖页(微信)
		@time:2013-09-24
	*/
	function indexwx(){
		
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$_SESSION['from'] = $lotteylogion['from'];
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = 'wx';//来源
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:显示抽奖页(大众)
		@time:2013-09-24
	*/
	function indexch(){
		
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$_SESSION['from'] = $lotteylogion['from'];
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = 'ch';//来源
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:小区传单页
		@time:2013-09-24
	*/
	function indexxq(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = $lotteylogion['from'] = substr($uri,1,3);//来源
		
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display('indexxq');
	}


	/*	
		@author:chf
		@function:传单
		@time:2013-09-24
	*/
	function indexcd(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = $lotteylogion['from'] = substr($uri,1,3);//来源
		
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display('indexcd');
	}
	
	/*	
		@author:chf
		@function:驴妈妈活动页面(驴妈妈)
		@time:2013-09-24
	*/
	function indexlmm(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = $lotteylogion['from'] = substr($uri,1,3);//来源
		
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 2;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display('indexlmm');
	}


	/*	
		@author:chf
		@function:显示百度(新百度页)
		@time:2013-09-24
	*/
	function indexyh(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = $lotteylogion['from'] = substr($uri,1,3);//来源
		
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:显示百度(新百度页)
		@time:2013-09-24
	*/
	function indexth(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = $lotteylogion['from'] = substr($uri,1,3);//来源
		
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display();
	}


	/*	
		@author:chf
		@function:显示百度(新百度页)
		@time:2013-09-24
	*/
	function indexsh(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = $lotteylogion['from'] = substr($uri,1,3);//来源
		
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:显示抽奖页(大众)
		@time:2013-09-24
	*/
	function index(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$_SESSION['from'] = $lotteylogion['from'];
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$_SESSION['from'] = $lotteylogion['from'] = substr($uri,1,3);//来源
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('url',$lotteylogion['from']);
		$this->assign('data',$data);
		$this->assign('patest_id',$_GET['pa_id']);
		$this->display();
	}

	/*	
		@author:chf
		@function:显示抽奖页(别克)
		@time:2013-10-08
	*/
	function indexbk(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
	
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$lotteylogion['from'] = substr($uri,1,3);//来源
		$_SESSION['from'] = $lotteylogion['from'];
		$this->LotteryloginModel->data($lotteylogion)->add();
		
		
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('url',$lotteylogion['from']);
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:显示抽奖页(雪弗莱)
		@time:2013-10-08
	*/
	function indexxfl(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
	
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$lotteylogion['from'] = substr($uri,1,3);//来源\
		$_SESSION['from'] = $lotteylogion['from'];
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('url',$lotteylogion['from']);
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:显示抽奖页(斯柯达)
		@time:2013-10-08
	*/
	function indexskd(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
	
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}

		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$lotteylogion['from'] = substr($uri,1,3);//来源
		$_SESSION['from'] = $lotteylogion['from'];
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('url',$lotteylogion['from']);
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:显示抽奖页(一汽丰田)
		@time:2013-10-08
	*/
	function indexft(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
	
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$lotteylogion['from'] = substr($uri,1,3);//来源
		
		$_SESSION['from'] = $lotteylogion['from'];

		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();

		$this->assign('url',$lotteylogion['from']);
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:ysh
		@function:显示抽奖页(东风日产)
		@time:2013-10-18
	*/
	function indexrc(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
	
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}

		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$lotteylogion['from'] = substr($uri,1,3);//来源
		$_SESSION['from'] = $lotteylogion['from'];
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('url',$lotteylogion['from']);
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:ysh
		@function:显示抽奖页(起亚)
		@time:2013-10-18
	*/
	function indexkia(){
		if(eregi('Android/2',$_SERVER['HTTP_USER_AGENT'])){
			$header = '1';
		}
		if(eregi('Android/3',$_SERVER['HTTP_USER_AGENT'])){
			$header = '2';
		}
		if(eregi('Android 4',$_SERVER['HTTP_USER_AGENT'])){
			$header = '3';
		}
	
		if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT'])){
			$header = '4';
		}
		$lotteylogion['system'] = $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteylogion['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteylogion['create_time'] = time();//时间
		$uri = $_SERVER['REQUEST_URI'];
		$lotteylogion['from'] = substr($uri,1,3);//来源
		$_SESSION['from'] = $lotteylogion['from'];
		$this->LotteryloginModel->data($lotteylogion)->add();
		$map['mobile'] = $_SESSION['mobile'];
		$map['salecoupon_id'] = 1;
		$count = $this->MembersalecouponModel->where($map)->count();
		$data = $this->MembersalecouponModel->where($map)->find();
		$this->assign('url',$lotteylogion['from']);
		$this->assign('count',$count);
		$this->assign('header',$header);
		$this->assign('data',$data);
		$this->display();
	}

	/*	
		@author:chf
		@function:下载APP统计
		@time:2013-09-24
	*/
	function ajaxapp(){
		$lotteyapp['system'] =  $_SERVER['HTTP_USER_AGENT'];//系统
		$lotteyapp['ip'] = $_SERVER["REMOTE_ADDR"];//IP
		$lotteyapp['create_time'] = time();//时间
		$this->LotteryappModel->data($lotteyapp)->add();
	}

	/*
		@author:ysh
		@function:发送验证码
		@time:2013-09-24
	*/
	function send_verify() {
		$mobile = $_REQUEST['mobile'];
		$model_member = M('Member');
	    if (is_numeric($mobile)){
		    if (substr($mobile,0,1)==1){
				$model_sms = D('Sms');
				$condition['phones'] = $mobile;
				$smsinfo = $model_sms->where($condition)->order("sendtime DESC")->find();
				$now = time();
				if (!$smsinfo || ($now - $smsinfo['sendtime'])>=60){

					/*添加发送手机验证码*/
					$condition['phones'] = $mobile;
					
					$rand_verify = rand(10000, 99999);
					$_SESSION['mobile_verify_lottery'] = md5($rand_verify);
					$verify_str = "正在为您的手机验证，进行注册登录 ，您的短信验证码：".$rand_verify."!";
					/*
					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$return_data = $this->curl_sms($send_verify);
					*/

					// dingjb 2015-09-29 09:43:40 切换到云通讯
					$send_verify = array(
						'phones'  => $mobile,
						'content' => array($rand_verify),
					);
					$return_data = $this->curl_sms($send_verify, null, 4, '37650');


					$send_verify['sendtime'] = time();
					$model_sms->add($send_verify);
					echo 1;exit;
				}else {
					 echo -1;
				}
		    }
		}
	}


	/*
		@author:chf
		@function:发送验证码(驴妈妈跨域外调)
		@time:2013-09-24
	*/
	function send_verifylmm() {
		$cb = $_GET['callback'];
		$mobile = $_GET['mobile'];
		$model_member = M('Member');
		
	    if (is_numeric($mobile)){
		    if (substr($mobile,0,1)==1){
				$model_sms = D('Sms');
				$condition['phones'] = $mobile;
				$smsinfo = $model_sms->where($condition)->order("sendtime DESC")->find();
				$now = time();
				if (!$smsinfo || ($now - $smsinfo['sendtime'])>=60){
					
					/*添加发送手机验证码*/
					$condition['phones'] = $mobile;
					
					$rand_verify = rand(10000, 99999);
					$_SESSION['mobile_verify_lottery'] = md5($rand_verify);
					$verify_str = "正在为您的手机验证，进行注册登录 ，您的短信验证码：".$rand_verify;
					/*
					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$return_data = $this->curl_sms($send_verify);
					*/
					// dingjb 2015-09-29 09:47:04 切换到云通讯
					$send_verify = array(
						'phones'  => $mobile,
						'content' => array($rand_verify),
					);
					$return_data = $this->curl_sms($send_verify, null, 4, '37650');

					$send_verify['sendtime'] = time();
					$model_sms->add($send_verify);
					$new_array['success'] = '1';
					echo $cb."(".json_encode($new_array).")";exit();
				}else {
					$new_array['success'] = '-1';
					echo $cb."(".json_encode($new_array).")";exit();
				}
		    }
		}
	}

	/*	
		@author:chf
		@function:获取抽到奖的信息
		@time:2013-09-24
	*/
	function ajaxcj(){
		$KEY = sha1("xiechezhangzhengchaozhangzhen");
		if($_REQUEST['mobile']){
			$KEY = $_REQUEST['mobile'].$KEY;
		}else{
			$KEY = $_SESSION['mobile'].$KEY;
		}
		$this->LotteryModel = D('lottery');//抽奖表
		$prize_arr = array( 
			'0' => array('id'=>'1','min'=>'1','max'=>'29','prize'=>'一等奖','v'=>'0'), 
			'1' => array('id'=>'2','min'=>'302','max'=>'328','prize'=>'二等奖','v'=>'0'), 
			'2' => array('id'=>'3','min'=>'242','max'=>'268','prize'=>'三等奖','v'=>'0'), 
			'3' => array('id'=>'4','min'=>'182','max'=>'208','prize'=>'四等奖','v'=>'0'), 
			'4' => array('id'=>'5','min'=>'122','max'=>'148','prize'=>'五等奖','v'=>'0'), 
			'5' => array('id'=>'6','min'=>'62','max'=>'88','prize'=>'六等奖','v'=>'0'), 
			'6' => array('id'=>'7','min'=>array('32','92','152','217','272','332'),'max'=>array('44','102','162','227','282','342'),'prize'=>'50元现金券','v'=>'100')
		); 
		foreach ($prize_arr as $key => $val) { 
			$arr[$val['id']] = $val['v']; 
		} 
		$rid = $this->getRand($arr); //根据概率获取奖项id 
		$res = $prize_arr[$rid-1]; //中奖项 
		$min = $res['min']; 
		$max = $res['max']; 
		if($res['id']==7){ //七等奖 
			$i = mt_rand(0,5);
			$result['angle'] = mt_rand($min[$i],$max[$i]); 
			$map['mobile'] = $_REQUEST['mobile'];
			$map['salecoupon_id'] = '1';
			$salecount = $this->MembersalecouponModel->where($map)->count();
			if($salecount){
				$result['count'] = 'noway';
			}
		}else{ 
			$result['angle'] = mt_rand($min,$max); //随机生成一个角度 
		} 
		$result['prize'] = $res['prize'];
		$result['id'] = $res['id'];
		$result['KEY'] = $KEY;
		$result['session_username'] = $_SESSION['username'];
		$result['session_mobile'] = $_REQUEST['mobile'];
		echo json_encode($result); 
	}
	
	/*	
		@author:chf
		@function:获取抽到奖的信息
		@time:2013-09-24
	*/
	function lotterylogin(){
		$FROM = array('10'=>'vw','11'=>'svw','12'=>'bk','13'=>'xfl','14'=>'skd','15'=>'ft','16'=>'rc','17'=>'kia','18'=>'yh','19'=>'th','20'=>'sh','22'=>'lmm','26'=>'传单','29'=>'小区');//判断活动页面来源数组
		$mobile = $_POST['mobile'];
		//判断验证码
		if($_SESSION['mobile_verify_lottery'] != pwdHash($_POST['mobile_verify_lottery'])) {
			echo "1";
		}else{
			$map['mobile'] = $mobile;
			$res = $this->MemberModel->where(array('mobile'=>$map['mobile'],'status'=>'1'))->find();
			if($res){
				$this->save_session($res);
				echo $res['mobile'];
			}else{
				//$member_data['fromstatus'] = 9;
				$member_data['mobile'] = $mobile;
				$member_data['password'] = md5($_POST['mobile_verify_lottery']);
				$member_data['reg_time'] = time();
				$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
				$uri = $_SERVER['REQUEST_URI'];
				//$lotteylogion['from'] = substr($uri,1,3);//来源
				foreach($FROM as $k=>$v){
					if($_SESSION['from'] == $v){
						$member_data['fromstatus'] = $k;

					}
				}
				
				$member['uid'] = $this->MemberModel->data($member_data)->add();
			
				/*
				$send_add_user_data = array(
					'phones'=>$mobile,
					//'content'=>'亲爱的用户，您可以使用您的手机号码'.$mobile.'，密码'.$_POST['mobile_verify_lottery'].'来登录携车网www.xieche.com.cn。携车网是一家在线预订各大汽车品牌4S店保养维修的网站，通过网站预订最低五折起，还有保养套餐和现金券团购。手机客户端已上线，手机客户端下载http://www.xieche.com.cn/download.php，客服4006602822。',
					'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$_POST['mobile_verify_lottery'].'来登录携车网，客服4006602822。',
				);
		
				$this->curl_sms($send_add_user_data);
				*/
				// dingjb 2015-09-29 11:06:46 切换到云通讯
				$send_add_user_data = array(
					'phones' => $mobile,
					'content'=> array(
						$mobile,
						$_POST['mobile_verify_lottery']
					)
				);
		
				$this->curl_sms($send_add_user_data, null, 4, '37653');

				$send_add_user_data['sendtime'] = time();
				$this->model_sms->data($send_add_user_data)->add();
				
				$model_memberlog = D('Memberlog');
				$data['createtime'] = time();
				$data['mobile'] = $mobile;
				$data['memo'] = '用户注册';
				$model_memberlog->data($data)->add();
				$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
				$this->save_session($res);
				echo $mobile;
			}
		}
	}




	/*	
		@author:chf
		@function:获取抽到奖的信息(优惠页面新规矩不要抽奖直接50元现金券&&微信跳过来用页面)
		@time:2013-09-24
	*/
	function lotteryloginyh(){
		$FROM = array('10'=>'vw','11'=>'svw','12'=>'bk','13'=>'xfl','14'=>'skd','15'=>'ft','16'=>'rc','17'=>'kia','18'=>'yh','19'=>'th','20'=>'sh','22'=>'lmm','26'=>'传单','27'=>'ch','28'=>'fby','29'=>'小区','30'=>'短信','31'=>'微信','32'=>'百度seo',);//判断活动页面来源数组
		$mobile = $_POST['mobile'];
		//判断验证码
		if($mobile){
			if($_SESSION['mobile_verify_lottery'] != pwdHash($_POST['mobile_verify_lottery']) && $_POST['mobile_verify_lottery'] != '989898') {
				echo "1";
			}else{
				$map['mobile'] = $mobile;
				$res = $this->MemberModel->where(array('mobile'=>$map['mobile'],'status'=>'1'))->find();
				if($res){
					$this->save_session($res);
				}else{
					$member_data['mobile'] = $mobile;
					$member_data['password'] = md5($_POST['mobile_verify_lottery']);
					$member_data['reg_time'] = time();
					$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
					
					foreach($FROM as $k=>$v){
						if($_SESSION['from'] == $v){
							$member_data['fromstatus'] = $k;
						}
					}
					$member['uid'] = $this->MemberModel->data($member_data)->add();
					/*
					$send_add_user_data = array(
						'phones'=>$mobile,
						'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$_POST['mobile_verify_lottery'].'来登录携车网，客服4006602822。',
					);
					$this->curl_sms($send_add_user_data);
					*/
					// dingjb 2015-09-29 11:08:39 切换到云通讯
					$send_add_user_data = array(
						'phones'  => $mobile,
						'content' => array(
							$mobile,
							$_POST['mobile_verify_lottery']
						)
					);
					$this->curl_sms($send_add_user_data, null, 4, '37653');

					$send_add_user_data['sendtime'] = time();
					$this->model_sms->data($send_add_user_data)->add();
					
					$model_memberlog = D('Memberlog');
					$data['createtime'] = time();
					$data['mobile'] = $mobile;
					$data['memo'] = '用户注册';
					$model_memberlog->data($data)->add();
					$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
					$this->save_session($res);
					
				
				}
		
					if($_SESSION['from'] == 'lmm'){
						$membersalesql['salecoupon_id'] = 2;
					}else{
						$membersalesql['salecoupon_id'] = 1;
					}
					$membersalesql['mobile'] = $mobile;
					$Membersalecount = $this->MembersalecouponModel->where($membersalesql)->count();
					$Membersale = $this->MembersalecouponModel->where($membersalesql)->find();

					$_SESSION['coupon_code'] = $Membersale['coupon_code'];
					if($Membersalecount == '0'){
						$model_salecoupon = D('Salecoupon');
						if($_SESSION['from'] == 'lmm'){
							$salecoupon = $model_salecoupon->find('2');
						}
						else{
							$salecoupon = $model_salecoupon->find('1');
						}
						

						//插入membersalecoupon表
						$Membersalecoupon['coupon_name'] = $salecoupon['coupon_name'];
						$Membersalecoupon['salecoupon_id'] = $salecoupon['id'];
						$Membersalecoupon['coupon_type'] = $salecoupon['coupon_type'];
						$Membersalecoupon['mobile'] = $mobile;
						
						$Membersalecoupon['create_time'] = time();
						$Membersalecoupon['start_time'] = $salecoupon['start_time'];
						$Membersalecoupon['end_time'] = $salecoupon['end_time'];
						$Membersalecoupon['ratio'] = $salecoupon['jiesuan_money'];
						$Membersalecoupon['shop_ids'] = $salecoupon['shop_ids'];
						if($_SESSION['from'] == 'lmm'){
							$coupon_code = '4006602822lv';
						}else{
							$coupon_code = $this->get_coupon_code();
						}
						

						$_SESSION['coupon_code'] =$coupon_code;
						$_SESSION['coupon_code'] = $coupon_code;
						$Membersalecoupon['coupon_code'] = $coupon_code;
						
						$Membersalecoupon['from'] = $_SESSION['from'];//来源
						$Membersalecoupon['uid'] = $this->GetUserId();
						$membersalecoupon_id = $this->MembersalecouponModel->add($Membersalecoupon);
						$start_time = date('Y-m-d H:i',$salecoupon['start_time']);
						$end_time = date('Y-m-d',$salecoupon['end_time']);
						
						//您获取的促销抵用券编号:1165（携车网50元折后现金抵用券）已送达您的账户.请通过携车网预约后凭消费码:6471473686至指定4S店于有效期内(截至2014-10-10)消费，使用规则和适用店铺详见http://www.xieche.com.cn/****，客服电话4006602822
						
						$verify_str = "您获取的抵用券:（".$salecoupon['coupon_name']."）已送达您的账户.请通过携车网预订后凭消费码:".$coupon_code."至指定4S店于有效期内(截至".$end_time.")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服4006602822";

						//$verify_str = "您的抵用券(".$salecoupon['coupon_name']."金额:".$salecoupon['coupon_amount'].")请凭消费码至指定商铺处使用,有效期(".$start_time."至".$end_time.")抵用卷消费码:".$coupon_code;

						$send_verify = array(
							'phones'=>$mobile,
							'content'=>$verify_str,
						);
						$this->curl_sms($send_verify);
						
						$send_verify['sendtime'] = time();
						$this->model_sms->add($send_verify);
						
						$lottey['system'] =  $_SERVER['HTTP_USER_AGENT'];//系统
						$lottey['ip'] = $_SERVER["REMOTE_ADDR"];//IP
						$lottey['mobile'] = $mobile;//时间
						$lottey['create_time'] = time();//时间
						$this->LotteryModel->data($lottey)->add();
						echo '3';//成功
						exit;
					}else{
						echo "2";//重复
						exit;
					}

			}
		}else{
			echo "5";
			exit;
		}
		
	}
	


	/*
		抽完奖后下订
	*/
	function addsale(){
		$KEY = sha1("xiechezhangzhengchaozhangzhen");
		$KEY = $_POST['mobile'].$KEY;
		$URL_KEY = $_POST['KEY'];
		
		$mobile = $_POST['mobile'];

		if($URL_KEY == $KEY){
			$model_salecoupon = D('Salecoupon');
			$salecoupon = $model_salecoupon->find('1');
			//插入membersalecoupon表
			$data['coupon_name'] = $salecoupon['coupon_name'];
			$data['salecoupon_id'] = $salecoupon['id'];
			$data['coupon_type'] = $salecoupon['coupon_type'];
			$data['mobile'] = $mobile;
			
			$data['create_time'] = time();
			$data['start_time'] = $salecoupon['start_time'];
			$data['end_time'] = $salecoupon['end_time'];
			$data['ratio'] = $salecoupon['jiesuan_money'];
			$data['shop_ids'] = $salecoupon['shop_ids'];
			$coupon_code = $this->get_coupon_code();
			$data['coupon_code'] = $coupon_code;
			
			$data['from'] =$_POST['url'];//来源
			$data['uid'] = $this->GetUserId();
			$membersalecoupon_id = $this->MembersalecouponModel->add($data);
			
			
			$start_time = date('Y-m-d H:i',$salecoupon['start_time']);

			if($_SESSION['from'] == 'vw' || $_SESSION['from'] == 'bk'){
				$end_time = '2014-04-30';
			}else{
				$end_time = date('Y-m-d',$salecoupon['end_time']);
			}
			
			//您获取的促销抵用券编号:1165（携车网50元折后现金抵用券）已送达您的账户.请通过携车网预约后凭消费码:6471473686至指定4S店于有效期内(截至2014-10-10)消费，使用规则和适用店铺详见http://www.xieche.com.cn/****，客服电话4006602822
			
			$verify_str = "您获取的抵用券:（".$salecoupon['coupon_name']."）已送达您的账户.请通过携车网预订后凭消费码:".$coupon_code."至指定4S店于有效期内(截至".$end_time.")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服4006602822";

			//$verify_str = "您的抵用券(".$salecoupon['coupon_name']."金额:".$salecoupon['coupon_amount'].")请凭消费码至指定商铺处使用,有效期(".$start_time."至".$end_time.")抵用卷消费码:".$coupon_code;

			$send_verify = array(
				'phones'=>$mobile,
				'content'=>$verify_str,
			);
			$this->curl_sms($send_verify);
			
			$send_verify['sendtime'] = time();
			$this->model_sms->add($send_verify);
			
			$lottey['system'] =  $_SERVER['HTTP_USER_AGENT'];//系统
			$lottey['ip'] = $_SERVER["REMOTE_ADDR"];//IP
			$lottey['mobile'] = $mobile;//时间
			$lottey['create_time'] = time();//时间
			$this->LotteryModel->data($lottey)->add();
			echo $membersalecoupon_id;

		}else{
			echo "0";
		}
	}



	/*	
		@author:chf
		@function:获取抽到奖的信息点击领取页
		@time:2013-10-08
	*/
	function lotterylogin_sale(){
		$mobile = $_REQUEST['mobile'];
		//判断验证码
		if($_SESSION['mobile_verify_lottery'] != pwdHash($_POST['mobile_verify_lottery'])) {
			echo "1";
		}else{
			$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->count();
			if($res>0){
				$this->save_session($res);
			}else{
				if($_SESSION['wbfrom'] = '23'){
					$member_data['fromstatus'] = 23;
				}elseif($_SESSION['wbfrom'] = '24'){
					$member_data['fromstatus'] = 24;
				}elseif($_SESSION['wbfrom'] == '26'){
					$member_data['fromstatus'] = 26;
				}

				$member_data['mobile'] = $mobile;
				$member_data['password'] = md5($_POST['mobile_verify_lottery']);
				$member_data['reg_time'] = time();
				$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
				$uri = $_SERVER['REQUEST_URI'];
	
				$member['uid'] = $this->MemberModel->data($member_data)->add();
				/*
				$send_add_user_data = array(
					'phones'=>$mobile,
					'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$_POST['mobile_verify_lottery'].'来登录携车网，客服4006602822。',
				);
				$this->curl_sms($send_add_user_data);
				*/

				// dingjb 2015-09-29 11:11:24 切换到云通讯
				$send_add_user_data = array(
					'phones'  => $mobile,
					'content' => array(
						$mobile,
						$_POST['mobile_verify_lottery']
					)
				);
				$this->curl_sms($send_add_user_data, null, 4, '37653');

				$send_add_user_data['sendtime'] = time();
				$this->model_sms->data($send_add_user_data)->add();
				
				
			}
				if(!$_SESSION['wbfrom']){
					$countarr['salecoupon_id'] = '1';
				}elseif($_SESSION['wbfrom']=='22'){
					$countarr['salecoupon_id'] = '2';
				}
				
				$countarr['mobile'] = $mobile;
				$count = $this->MembersalecouponModel->where($countarr)->count();
				if($count==0){
					$model_memberlog = D('Memberlog');
					$data['createtime'] = time();
					$data['mobile'] = $mobile;
					$data['memo'] = '用户注册';
					$model_memberlog->data($data)->add();
					$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
					$this->save_session($res);

					$model_salecoupon = D('Salecoupon');
					if($_REQUEST['type'] == '2'){
						$salecoupon = $model_salecoupon->find('2');
					}
					else{
						$salecoupon = $model_salecoupon->find('1');
					}

					//插入membersalecoupon表
					$data['coupon_name'] = $salecoupon['coupon_name'];
					$data['salecoupon_id'] = $salecoupon['id'];
					$data['coupon_type'] = $salecoupon['coupon_type'];
					$data['mobile'] = $mobile;
					if($_SESSION['wbfrom']=='23'){
						$data['from'] = '微博';
					}elseif($_SESSION['wbfrom']=='24'){
						$data['from'] = '彩生活';
					}elseif($_SESSION['wbfrom']=='26'){
						$data['from'] = '传单';
					}elseif($_SESSION['wbfrom']=='28'){
						$data['from'] = 'fby';
					}
					$data['create_time'] = time();
					$data['start_time'] = $salecoupon['start_time'];
					$data['end_time'] = $salecoupon['end_time'];
					$data['ratio'] = $salecoupon['jiesuan_money'];
					$data['shop_ids'] = $salecoupon['shop_ids'];
					$coupon_code = $this->get_coupon_code();
					$data['coupon_code'] = $coupon_code;
					//$data['from'] = $_POST['url'];//来源
					$data['uid'] = $this->GetUserId();
					$membersalecoupon_id = $this->MembersalecouponModel->add($data);
					
					
					$start_time = date('Y-m-d H:i',$salecoupon['start_time']);

					$end_time = date('Y-m-d',$salecoupon['end_time']);
					
					//您获取的促销抵用券编号:1165（携车网50元折后现金抵用券）已送达您的账户.请通过携车网预约后凭消费码:6471473686至指定4S店于有效期内(截至2014-10-10)消费，使用规则和适用店铺详见http://www.xieche.com.cn/****，客服电话4006602822
					
					$verify_str = "您获取的抵用券:（".$salecoupon['coupon_name']."）已送达您的账户.请通过携车网预订后凭消费码:".$coupon_code."至指定4S店于有效期内(截至".$end_time.")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服4006602822";


					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$this->curl_sms($send_verify);
					
					$send_verify['sendtime'] = time();
					$this->model_sms->add($send_verify);
					
					$lottey['system'] =  $_SERVER['HTTP_USER_AGENT'];//系统
					$lottey['ip'] = $_SERVER["REMOTE_ADDR"];//IP
					$lottey['mobile'] = $mobile;//时间
					$lottey['create_time'] = time();//时间
					$this->LotteryModel->data($lottey)->add();
					echo "1";
				}else{
					echo "2";	
				
				}
		}
	}

	
	
	/*	
		@author:chf
		@function:获取抽到奖的信息点击领取页(驴妈妈跨域跳转页)
		@time:2013-10-08
	*/
	function lotterylogin_salelmm(){
		$cb = $_GET['callback'];
		$mobile = $_GET['mobile'];
		//判断验证码
		
		if(trim($_SESSION['mobile_verify_lottery']) != trim(pwdHash($_GET['mobile_verify_lottery']))) {
			$new_array['success'] = 'lotteryerror';
			echo $cb."(".json_encode($new_array).")";exit();
		}else{
			$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->count();
			if($res>0){
				$this->save_session($res);
			}else{
				
				$member_data['fromstatus'] = 22;
				$member_data['mobile'] = $mobile;
				$member_data['password'] = md5($_GET['mobile_verify_lottery']);
				$member_data['reg_time'] = time();
				$member_data['ip'] = $_SERVER["REMOTE_ADDR"];//IP
				$uri = $_SERVER['REQUEST_URI'];
	
				$member['uid'] = $this->MemberModel->data($member_data)->add();
				/*
				$send_add_user_data = array(
					'phones'=>$mobile,
					'content'=>'您已注册成功，您可以使用您的手机号码'.$mobile.'，密码'.$_GET['mobile_verify_lottery'].'来登录携车网，客服4006602822。',
					//'content'=>'亲爱的用户，您可以使用您的手机号码'.$mobile.'，密码'.$_GET['mobile_verify_lottery'].'来登录携车网www.xieche.com.cn。携车网是一家在线预订各大汽车品牌4S店保养维修的网站，通过网站预订最低五折起，还有保养套餐和现金券团购。手机客户端已上线，手机客户端下载http://www.xieche.com.cn/download.php，客服4006602822。',
				);
				$this->curl_sms($send_add_user_data);
				*/
				// dingjb 2015-09-29 11:13:00 切换到云通讯
				$send_add_user_data = array(
					'phones' => $mobile,
					'content'=> array(
						$mobile,
						$_GET['mobile_verify_lottery']
					) 
				);
				$this->curl_sms($send_add_user_data, null, 4, '37653');
				
				$send_add_user_data['sendtime'] = time();
				$this->model_sms->data($send_add_user_data)->add();
				
				
			}
				$countarr['salecoupon_id'] = '2';
				$countarr['mobile'] = $mobile;
				$count = $this->MembersalecouponModel->where($countarr)->count();
				if($count==0){
					$model_memberlog = D('Memberlog');
					$data['createtime'] = time();
					$data['mobile'] = $mobile;
					$data['memo'] = '用户注册';
					$model_memberlog->data($data)->add();
					$res = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->find();
					$this->save_session($res);

					$model_salecoupon = D('Salecoupon');
					$salecoupon = $model_salecoupon->find('2');
					//插入membersalecoupon表
					$data['coupon_name'] = $salecoupon['coupon_name'];
					$data['salecoupon_id'] = $salecoupon['id'];
					$data['coupon_type'] = $salecoupon['coupon_type'];
					$data['mobile'] = $mobile;
				
					$data['from'] = 'lmm';
					$data['create_time'] = time();
					$data['start_time'] = $salecoupon['start_time'];
					$data['end_time'] = $salecoupon['end_time'];
					$data['ratio'] = $salecoupon['jiesuan_money'];
					$data['shop_ids'] = $salecoupon['shop_ids'];
					$coupon_code = $this->get_coupon_code();
					$data['coupon_code'] = $coupon_code;
					//$data['from'] = $_POST['url'];//来源
					$data['uid'] = $this->GetUserId();
					$membersalecoupon_id = $this->MembersalecouponModel->add($data);
					
					
					$start_time = date('Y-m-d H:i',$salecoupon['start_time']);
					$end_time = date('Y-m-d',$salecoupon['end_time']);
					
					//您获取的促销抵用券编号:1165（携车网50元折后现金抵用券）已送达您的账户.请通过携车网预约后凭消费码:6471473686至指定4S店于有效期内(截至2014-10-10)消费，使用规则和适用店铺详见http://www.xieche.com.cn/****，客服电话4006602822
					
					$verify_str = "您获取的抵用券:（".$salecoupon['coupon_name']."）已送达您的账户.请通过携车网预订后凭消费码:".$coupon_code."至指定4S店于有效期内(截至".$end_time.")消费，使用规则和适用店铺详见http://www.xieche.com.cn/y50，客服4006602822";

					//$verify_str = "您的抵用券(".$salecoupon['coupon_name']."金额:".$salecoupon['coupon_amount'].")请凭消费码至指定商铺处使用,有效期(".$start_time."至".$end_time.")抵用卷消费码:".$coupon_code;

					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$this->curl_sms($send_verify);
					
					$send_verify['sendtime'] = time();
					$this->model_sms->add($send_verify);
					
					$lottey['system'] =  $_SERVER['HTTP_USER_AGENT'];//系统
					$lottey['ip'] = $_SERVER["REMOTE_ADDR"];//IP
					$lottey['mobile'] = $mobile;//时间
					$lottey['create_time'] = time();//时间
					$this->LotteryModel->data($lottey)->add();

					$new_array['success'] = $membersalecoupon_id;
					echo $cb."(".json_encode($new_array).")";exit();

				}else{
					$new_array['success'] = 'noway';
					echo $cb."(".json_encode($new_array).")";exit();
				}
		}
	}



	/*
		随机数产生
	*/
	function getRand($proArr) { 
		$result = ''; 
		//概率数组的总概率精度 
		$proSum = array_sum($proArr); 
		//概率数组循环 
		foreach ($proArr as $key => $proCur) { 
			$randNum = mt_rand(1, $proSum); 
			if ($randNum <= $proCur) { 
				$result = $key; 
				break; 
			} else { 
				$proSum -= $proCur; 
			} 
		} 
		unset ($proArr); 
		return $result; 
	}

	function detail(){
		$salecoupon_id = isset($_REQUEST['salecoupon_id'])?$_REQUEST['salecoupon_id']:0;
	    if ($salecoupon_id){
	        $model_salecoupon = D('Salecoupon');
			$coupon = $model_salecoupon->find($salecoupon_id);
			
	    }
	    $this->assign("coupon",$coupon);
	    $this->assign("salecoupon_id",$salecoupon_id);
		
		/*取得该团购券能使用的车型车系*/
		$this->assign('phone',C('CALL_400'));
		$this->assign('title',"{$coupon[coupon_name]}_保养团购券现金券_携车网");
		$this->display();
	}

	//计算优惠券验证码
	function get_coupon_code(){
        $orderid = $this->randString(9,1);
        $sum = 0;
	    for($ii=0;$ii<strlen($orderid);$ii++){
	        $orderid = (string)$orderid;
            $sum += intval($orderid[$ii]);
        }
        $str = $sum%10;
		$result = $orderid.$str;
        return $result;
    }

	/**
	 +----------------------------------------------------------
	 * 产生随机字串，可用来自动生成密码
	 * 默认长度6位 字母和数字混合 支持中文
	 +----------------------------------------------------------
	 * @param string $len 长度
	 * @param string $type 字串类型
	 * 0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 +----------------------------------------------------------
	 * @return string
	 +----------------------------------------------------------
	 */
	function randString($len=6,$type='',$addChars='') {
		$str ='';
		switch($type) {
			case 0:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 1:
				$chars= str_repeat('0123456789',3);
				break;
			case 2:
				$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
				break;
			case 3:
				$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
				break;
			case 4:
				$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
				break;
		}
		if($len>10 ) {//位数过长重复字符串一定次数
			$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
		}
		if($type!=4) {
			$chars   =   str_shuffle($chars);
			$str     =   substr($chars,0,$len);
		}else{
			// 中文随机字
			for($i=0;$i<$len;$i++){
			  $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
			}
		}
		return $str;
	}

	 public function save_session($res){
        $_SESSION['uid'] = $res['uid'];
		Cookie::set('uid', $res['uid']);
		if ($res['username']){
		    $_SESSION['username'] = $res['username'];
		    Cookie::set('username', $res['username']);
			
		}
		if ($res['mobile']){
		    $_SESSION['mobile'] = $res['mobile'];
		    Cookie::set('mobile', $res['mobile']);
		}
		if ($res['cardid']){
		    $_SESSION['cardid'] = $res['cardid'];
		    Cookie::set('cardid', $res['cardid']);
		}
		$rand = rand_string();
		$rand = md5($rand.C('ALL_PS'));
		Cookie::set('x_id', $rand);
		$res['username'] = isset($res['username'])?$res['username']:'';			
		$res['cardid'] = isset($res['cardid'])?$res['cardid']:'0';			
		$data = array(
			'uid'=>$res['uid'],
			'username'=>$res['username'],
			'cardid'=>$res['cardid'],
			'x_id'=>$rand,
			'login_time'=>time(),
		);
		$xsession = M('Xsession');
		$select_uid = $xsession->where("uid=$res[uid]")->find();
		if($select_uid){
			$list = $xsession->where("s_id=$select_uid[s_id]")->save($data);
		}else{		
			$xsession ->add($data);
		}
        $delete_map['login_time'] = array('lt',time()-3600);
		$xsession->where($delete_map)->delete();
    }

	//网页版 促销券显示
	function salecoupon() {
		$salecoupon_id = isset($_REQUEST['salecoupon_id'])?$_REQUEST['salecoupon_id']:0;
	    if ($salecoupon_id){
	        $model_salecoupon = D('Salecoupon');
			$coupon = $model_salecoupon->find($salecoupon_id);
			
	    }
	    $this->get_tuijian_coupon();
	    $this->assign("coupon",$coupon);
	    $this->assign("salecoupon_id",$salecoupon_id);

		$this->assign('id',$_REQUEST['id']);
		/*判断type是否是微博口登录如是记录SESSION*/
		if($salecoupon_id=='2'){
			$_SESSION['wbfrom'] = '22';
		}
		if($_REQUEST['type'] == 'wb'){
			$_SESSION['wbfrom'] = '23';
		}elseif($_REQUEST['type'] == 'csh'){
			$_SESSION['wbfrom'] = '24';
		}elseif($_REQUEST['type'] == '2n'){
			$_SESSION['wbfrom'] = '26';
		}elseif($_REQUEST['type'] == 'fby'){
			$_SESSION['wbfrom'] = '28';
		}
	
		/*取得该团购券能使用的车型车系*/
		$this->assign('phone',C('CALL_400'));
		$this->assign('title',"{$coupon[coupon_name]}_保养团购券现金券_携车网");
	    $this->display("coupondetail_new");
	}

	//网页版 我的促销券显示
	function my_salecoupon() {
//		if( true !== $this->login()){
//			exit;
//		}
		$membersalecoupon_id = isset($_REQUEST['membersalecoupon_id'])?$_REQUEST['membersalecoupon_id']:0;
	    if ($membersalecoupon_id){
	        $model_salecoupon = D('Salecoupon');
			$model_membersalecoupon = D('Membersalecoupon');
			if($_REQUEST['id']!=1){
				$map['salecoupon_id'] = $_REQUEST['id'];
			}else{
				$map['salecoupon_id'] = 1;
			}
			$map['membersalecoupon_id'] = $membersalecoupon_id;
			$map['uid'] = $this->GetUserId();
			$membersalecoupon = $model_membersalecoupon->where($map)->find();
			
			$coupon = $model_salecoupon->find($membersalecoupon['salecoupon_id']);
			$coupon['coupon_code'] = $membersalecoupon['coupon_code'];
	    }
	    $this->get_tuijian_coupon();
	    $this->assign("coupon",$coupon);
	    $this->assign("salecoupon_id",$salecoupon_id);
	
		/*取得该团购券能使用的车型车系*/
		$this->assign('phone',C('CALL_400'));
		$this->assign('title',"{$coupon[coupon_name]}_保养团购券现金券_携车网");
	    $this->display("my_coupondetail_new");
	}



	/*
		@author:chf
		@function:显示驴妈妈领券页和显示行验证码页面
		@time:2013-11-1
	*/
	function mysalecoupon_lmm() {
//		if( true !== $this->login()){
//			exit;
//		}
		$membersalecoupon_id = isset($_REQUEST['membersalecoupon_id'])?$_REQUEST['membersalecoupon_id']:0;
	    if ($membersalecoupon_id){
	        $model_salecoupon = D('Salecoupon');
	 		$model_membersalecoupon = D('Membersalecoupon');
			if($_REQUEST['id']==2){
				$map['salecoupon_id'] = $_REQUEST['id'];
			}else{
				$map['salecoupon_id'] = 1;
			}
			$map['membersalecoupon_id'] = $membersalecoupon_id;
			$map['uid'] = $this->GetUserId();
			$membersalecoupon = $model_membersalecoupon->where($map)->find();
			$coupon = $model_salecoupon->find($membersalecoupon['salecoupon_id']);
			$coupon['coupon_code'] = $membersalecoupon['coupon_code'];
	    }
	    $this->get_tuijian_coupon();
	    $this->assign("coupon",$coupon);
	    $this->assign("salecoupon_id",$salecoupon_id);

		
		/*取得该团购券能使用的车型车系*/
		$this->assign('phone',C('CALL_400'));
		$this->assign('title',"{$coupon[coupon_name]}_保养团购券现金券_携车网");
	    $this->display();
	}


	//网页版 我的促销券显示
	function my_salecoupon2() {
//		if( true !== $this->login()){
//			exit;
//		}
		$membersalecoupon_id = isset($_REQUEST['membersalecoupon_id'])?$_REQUEST['membersalecoupon_id']:0;
	    if ($membersalecoupon_id){
	        $model_salecoupon = D('Salecoupon');
			$model_membersalecoupon = D('Membersalecoupon');

			$map['membersalecoupon_id'] = $membersalecoupon_id;
			$map['uid'] = $this->GetUserId();
			$membersalecoupon = $model_membersalecoupon->where($map)->find();
			$coupon = $model_salecoupon->find($membersalecoupon['salecoupon_id']);
			$coupon['coupon_code'] = $membersalecoupon['coupon_code'];
	    }
	    $this->get_tuijian_coupon();
	    $this->assign("coupon",$coupon);
	    $this->assign("salecoupon_id",$salecoupon_id);

		
		/*取得该团购券能使用的车型车系*/
		$this->assign('phone',C('CALL_400'));
		$this->assign('title',"{$coupon[coupon_name]}_保养团购券现金券_携车网");
	    $this->display("my_coupondetail_new2");
	}

	function test(){
		$cb = $_GET['callback'];
		$data = array(
			'mobile' => $_REQUEST['mobile'],
		);

		echo $cb."(".json_encode($data).")";exit();
	}

	

}