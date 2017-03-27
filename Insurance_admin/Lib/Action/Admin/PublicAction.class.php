<?php

class PublicAction extends CommonAction {
	public function __construct() {
		parent::__construct();
		$this->IpModel = D('ip');//网站IP管理表
		$this->UserModel = D('user');//用户表
		$this->model_sms = D('Sms');
		
	}
	// 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl',__APP__.'/Public/login');
			header('Location:'.__APP__.'/Public/login');
			//$this->error('没有登录');
		}
	}

    protected function pub_display() {
        $this->display('./Tpl/Public/'.ACTION_NAME.'.html');
    }
	// 顶部页面
	public function top() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$model	=	D(GROUP_NAME."/"."Group");
		if( $_SESSION['authId'] ==1 ){
			$list	=	$model->where('status=1')->select();
			$this->assign('nodeGroupList',$list);
		}else{
			$list	=	$model->where('id=2')->select();
			//echo $model->getLastSql();
			//var_dump($list);
			$this->assign('nodeGroupList',$list);
		}

		$this->pub_display();
	}
	// 尾部页面
	public function footer() {
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$this->pub_display();
	}
	// 菜单页面
	public function menu() {
        $this->checkUser();
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
            //显示菜单项
            $menu  = array();
            if(isset($_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]) and 1==2) {
                //如果已经缓存，直接读取缓存
                $menu   =   $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]];
            }else {
                //读取数据库模块列表生成菜单项
                $node    =   D(GROUP_NAME."/"."Node");
				$id	=	$node->where(' level=1 and name="'.$_GET['name'].'"')->getField("id");
				$list	=	$node->where(array('_query'=>'level=2&is_show=1&status=1&pid='.$id))->field('id,name,module,group_id,title')->order('sort asc')->select();//echo $node->getLastSql();
				//var_dump($list);
                $accessList = $_SESSION['_ACCESS_LIST'];
                //var_dump($accessList);
                foreach($list as $key=>$module) {
                	//var_dump($module);
                     if(isset($accessList[strtoupper($_GET['name'])][strtoupper($module['name'])]) || !empty($_SESSION['administrator'])) {
                        //设置模块访问权限
                        $module['access'] =   1;
                        if(!empty($module['module'])) {
                            $module['name']   = $module['module'].'/'.$module['name'];
                        }
                        $menu[$key]  = $module;
                    }
                }
                //缓存菜单访问
                $_SESSION['menu'.$_SESSION[C('USER_AUTH_KEY')]]	=	$menu;
            }
            $group   = !empty($_GET['tag'])?$_GET['tag']:0;
            if(empty($group)) {
                if(empty($_SESSION['expressMenu'])) {
                    // 读取快捷方式
                    $list   =  D(GROUP_NAME."/".'Express')->where('status=1')->field('title,url')->order('sort')->select();
                    $_SESSION['expressMenu']    = $list;
                }else{
                    $list   =  $_SESSION['expressMenu'];
                }
                $this->assign('links',$list);
            }

            $this->assign('menuTag',$group);
            $this->assign('menu',$menu);
		}
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
        $this->pub_display();
	}

	// 用户登录页面
	public function login() {
	    $this->assign('account',$_SESSION['account']);
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('count',1);
			$this->pub_display();
		}else{
			$this->redirect('Admin-Index/index');
		}
	}
	/*
		author:chf
		function:发送手机验证码给用户
		time:2013-7-29
	*/
    function giveeverify(){
		$mobile = $_REQUEST['mobile'];
		$account = $_REQUEST['account'];
		$_SESSION['mobileeverify'] = rand(10000,999999);
		$userinfo = $this->UserModel->where(array('account'=>$account))->find();
        $_SESSION['account'] = $_REQUEST['account'];
		/*chf新增发送给4S店预约信息*/
		$model_sms = M('tp_xieche.Sms','xc_');

		$send = array(
		'phones'=>$userinfo['mobile'],
		'content'=>"验证码为：".$_SESSION['mobileeverify'],	
		);
		//$this->ceshi_sms($send,'1');
        $this->curl_sms($send);
		$send['sendtime'] = time();
		$model_sms->add($send);
		$this->success('发送成功！请注意查收短信！');
		
	}
	function tt(){
		$data['touser'] = "oF49ruJukiRNno_6NJ4CEY6waiN4";
		$data['content'] = "这是我们后台的短信验证码！33311122";

		var_dump($this->weixin_api($data));
		/*
		$send = array(
			'phones'=>'13661743916',
			'content'=>"验证码为：2014326,",	
			);
		$this->ceshi_sms($send,'3');
		echo "ok";
		*/
	}

	//短信接口
     function ceshi_sms($post = '' ,$type=''){
		$datamobile = array('130','131','132','155','156','186','185');
		$submobile = substr($post['phones'],0,3);
		$post['content'] = str_replace("联通", "联_通", $post['content']);
		if(in_array($submobile,$datamobile)){
			$post['content'] = $post['content']."  回复TD退订";
		}
			
		if($type=='1'){
			$post['content'] .= " 【携车网】";
			$client = new SoapClient("http://121.199.48.186:1210/Services/MsgSend.asmx?WSDL");//此处替换成您实际的引用地址
			$param = array("userCode"=>"csml","userPass"=>"csml5103","DesNo"=>$post['phones'],"Msg"=>$post['content'],"Channel"=>"1");
			$p = $client->__soapCall('SendMsg',array('parameters' => $param));
			$res = $p;
		}
		if($type>='2'){
			if($type == '2') {
				$xml_data="<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><account>dh21007</account><password>49e96c9b07f0628fec558b11894a9e8f</password><msgid></msgid><phones>$post[phones]</phones><content>$post[content]</content><subcode></subcode><sendtime></sendtime></message>";
			}
			if($type == '3') {
				$xml_data="<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><account>dhshzhxc</account><password>fqcd123223</password><msgid></msgid><phones>$post[phones]</phones><content>$post[content]</content><subcode></subcode><sendtime></sendtime></message>";
			}
			
			$url = 'http://www.10690300.com/http/sms/Submit';
			$curl = curl_init($url );
			if( !is_null( $charset ) ){
				curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8"));
			}
			if( !empty( $post ) ){
				$xml_data = 'message='.urlencode($xml_data);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $xml_data);
			}
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$res = curl_exec($curl);
			curl_close($curl);
		}
		//var_dump($res);
        return $res;
    }


	public function index()
	{
		//如果通过认证跳转到首页
		redirect(__APP__);
	}

	// 用户登出
    public function logout()
    {
        if(isset($_SESSION[C('USER_AUTH_KEY')])) {
			$User_log = D(GROUP_NAME."/".'User_log');
			$ip		=	get_client_ip();
			$data_log = array();
			$data_log['user_id']	=	$_SESSION[C('USER_AUTH_KEY')]	;
			$data_log['nickname']	= $_SESSION['loginAdminUserName'];
			$data_log['login_ip']	=	$ip;
			$data_log['login_time']	=	time();
			$data_log['login_type']	=	0;//1是登录 0是登出
			$User_log->add($data_log);


			$loginId	=	$_SESSION['loginId'];
			unset($_SESSION['loginId']);
			unset($_SESSION[C('USER_AUTH_KEY')]);
			unset($_SESSION['shop_id']);
			unset($_SESSION);
			session_destroy();
            $this->assign("jumpUrl",__URL__.'/login/');
            $this->success('登出成功！');
        }else {
            $this->error('已经登出！');
        }
    }

	// 登录检测
	public function checkLogin() {
		//获取IP
		/*$count = $this->IpModel->where(array('ip'=>$_SERVER['REMOTE_ADDR']))->count();
                
		if(!$count){
			if(!$_REQUEST['mobileeverify']){
				$this->error('请填写手机验证码！');
			}
		}
		
		if($_REQUEST['mobileeverify']){
			if($_REQUEST['mobileeverify'] != $_SESSION['mobileeverify']){
				$this->error('手机验证码错误');
			}
		}*/
		
		if(empty($_POST['account'])) {
			$this->error('帐号错误！');
		}elseif (empty($_POST['password'])){
			$this->error('密码必须！');
		}elseif (''===trim($_POST['verify'])){
			$this->error('验证码必须！');
		}
        //生成认证条件
        $map            =   array();
		// 支持使用绑定帐号登录
		$map['account']	= $_POST['account'];
        $map["status"]	=	array('gt',0);
        if($_SESSION['verify'] != md5($_POST['verify'])) {
            $this->error('验证码错误！');
        }
		import('ORG.Util.RBAC');
        $authInfo = RBAC::authenticate($map);
		//echo $authInfo['password']."=1111=".pwdHash($_POST['password']);
        //使用用户名、密码和状态的方式进行认证
        if(false === $authInfo) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['password'] != pwdHash($_POST['password'])) {
            	$this->error('密码错误！');
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];//$_session['authId']
            $_SESSION['loginAdminUserName']		=	$authInfo['nickname'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
			$_SESSION['login_count']	=	$authInfo['login_count'];
            $_SESSION['user_type']    =  $authInfo['type_id'];
			$_SESSION['city_id']    =  $authInfo['city_id'];
			$_SESSION['business_source']    =  $authInfo['business_source'];
            if($authInfo['account']=='admin') {
            	$_SESSION['administrator'] = true;
            }
            /*获取店铺ID
            $authId = $_SESSION['authId'];
            $model_user_shop = D(GROUP_NAME.'/user_shop');
            $condition['authid'] = $authId;
            $user_shop_info = $model_user_shop->where($condition)->find();
            if (isset($user_shop_info['shop_id'])){
                $_SESSION['shop_id'] = $user_shop_info['shop_id'];
            }
			*/
            //保存登录信息
			$User	=	D(GROUP_NAME."/".'User');
			$ip		=	get_client_ip();
			$time	=	time();
            $data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_login_time']	=	$time;
			$data['login_count']	=	array('exp','(login_count+1)');
			$data['last_login_ip']	=	$ip;
			$User->save($data);

			$User_log = D(GROUP_NAME."/".'User_log');
			$data_log = array();
			$data_log['user_id']	=	$authInfo['id'];
			$data_log['nickname']	= $authInfo['nickname'];
			$data_log['login_ip']	=	$ip;
			$data_log['login_time']	=	$time;
			$data_log['login_type']	=	1;//1是登录 0是登出
			$User_log->add($data_log);
            //$_SESSION['loginId']		=	$loginId;
			// 缓存访问权限
            RBAC::saveAccessList();		
			$this->success('登录成功！');
		}
	}

	public function profile() {
		$this->checkUser();
		$User	 =	 D(GROUP_NAME."/"."User");
		$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
		$this->assign('vo',$vo);
		$this->pub_display();
	}

	// 修改资料
	public function change() {
		$this->checkUser();
		$User	 =	 D(GROUP_NAME."/"."User");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();
		if(false !== $result) {
			$this->success('资料修改成功！');
		}else{
			$this->error('资料修改失败!');
		}
	}
    public function password(){
        $this->pub_display();
    }
    // 更换密码
    public function changePwd()
    {
		$this->checkUser();
        //对表单提交处理进行处理或者增加非表单数据
		if(md5($_POST['verify'])	!= $_SESSION['verify']) {
			$this->error('验证码错误！');
		}
		$map	=	array();
        $map['password']= pwdHash($_POST['oldpassword']);
        if(isset($_POST['account'])) {
            $map['account']	 =	 $_POST['account'];
        }elseif(isset($_SESSION[C('USER_AUTH_KEY')])) {
            $map['id']		=	$_SESSION[C('USER_AUTH_KEY')];
        }
        //检查用户
        $User    =   D(GROUP_NAME."/"."User");
        if(!$User->where($map)->field('id')->find()) {
            $this->error('旧密码不符或者用户名错误！');
        }else {
			$User->password	=	pwdHash($_POST['password']);
			$User->save();
			$this->assign('jumpUrl',__APP__.'/Public/main');
			$this->success('密码修改成功！');
         }
    }

	// 验证码显示
    public function verify()
    {
        import("ORG.Util.Image");
        Image::buildImageVerify(4);

    }


	/*
	*@name 微信发送客服消息接口
	*@author ysh
	*@time 2014/3/26
	*/
	function weixin_api($data) {

// 		$memcache_access_token = S('WEIXIN_access_token');
// 		if($memcache_access_token) {
// 			$access_token = $memcache_access_token;
// 		}else {
// 			$appid = "wx43430f4b6f59ed33";
// 			$secret = "e5f5c13709aa0ae7dad85865768855d6";
		
// 			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
// 			$result = file_get_contents($url);
// 			$result = json_decode($result,true);
// 			$access_token = $result['access_token'];
// 			S('WEIXIN_access_token',$access_token,7200);
// 		}
		$access_token = $this->getWeixinToken();

		$body = array(
			"touser"=>$data['touser'],
			"msgtype"=>"text", 
			"text" => array("content" => "%content%"),
		);
		$json_body = json_encode($body);
		$search = array('%title%' , '%description%' , '%content%');
		$replace = array($data['title'],$data['description'],$data['content']);
		$json_body = str_replace($search , $replace , $json_body);


		$host = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,5);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL,$host);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
		$output = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($output,true);

		if($result['errcode'] != 0) {
			S('WEIXIN_access_token',NUll);
			$this->weixin_api($data);
		}else {
			return $result;
		}
	}

}
?>
