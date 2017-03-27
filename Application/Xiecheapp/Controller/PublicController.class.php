<?php
namespace Xiecheapp\Controller;

class PublicController extends CommonController {

	public function index(){
		//如果通过认证跳转到首页
		redirect(__APP__);
	}
	public function login(){
		$_SESSION['url'] = $_SERVER['HTTP_REFERER'];
		$uid = $this->GetUserId();
		$title = "登录/注册-携车网";
		$this->assign("title", $title);
		if($uid){
			redirect("/myhome");
		}else{
			$this->assign('noshow',true);
            $this->assign('noclose',true);
			$this->display("login");
		}	
		
	}

    public function city(){
		$model_city = D("city");
		$map['is_show'] = 1;
		$city_info = $model_city->where($map)->select();
		$this->assign('city_info',$city_info);
        $this->display();
    }

    public function changecity(){
        if(!$_GET['citynum']){
            $this->error('参数错误');
        }
        $_SESSION['city'] = $_GET['citynum'];
        header('location:'.WEB_ROOT);
    }
	// 验证码
	public function verify() {
		$type = isset ( $_GET ['type'] ) ? $_GET ['type'] : 'gif';
		$width = isset ( $_GET ['width'] ) ? $_GET ['width'] : '100';
		$height = isset ( $_GET ['height'] ) ? $_GET ['height'] : '50';
		import ( "Org.Util.Image" );
		\Image::buildImageVerify ( 4, 1, $type, $width, $height );
	}

		// 用户登出
    public function logout()
    {
        if(isset($_SESSION['uid'])) {
        	$Xsession = M('Xsession');
        	$res=$Xsession->where("uid=$_SESSION[uid]")->find();
        	if($res){
        		$Xsession->where("uid=$_SESSION[uid]")->delete(); 
        	}
			foreach ($_SESSION as $key=>$value) {
				unset($_SESSION[$key]);
			}
			
			cookie('username',null);
			$this->assign('noshow',true);
			$this->assign("jumpUrl",__APP__);
			//var_dump($_SERVER['HTTP_REFERER']);exit;
			$refer = $_SERVER['HTTP_REFERER'];
			if (mb_strpos($refer, '.html')) {
				$refer = str_replace('.html', '', $refer);
			}
            $this->redirect($refer);
        }else {
			if($_SESSION['uid']) {
				cookie('uid',null);
			}
            $this->error('已经登出！');
        }
    }


    //用户登录验证
    public function logincheck(){
    	$logintype = @I('post.logintype');
    	$password = @I('post.password');
    	$username = @I('post.username');
    	$verify = @I('post.verify');
    	
    	if(!$username ) {
    		if ( $logintype =='top'){
    			echo 3;exit;
    		}else{
    			$this->_error('帐号错误!');
    		}
    	}elseif (!$password){
    		if ($logintype == 'top' ){
    			echo 4;exit;
    		}else{
    			$this->_error('密码必须!');
    		}
    	}
    	if (!$logintype || $logintype!='top' ){
    		if (!$verify){
    			$this->_error('密码必须!');
    		}
    		if( $_SESSION['verify'] != md5($verify) ) {
    			$this->_error('验证码错误!');
    		}
    	}
    	
    	
    	$password_md5 = $this->_pwdHash($password);
    	
    	$Member = M('Member');
    	
    	if (is_numeric($username)){
    		if (substr($username,0,1)==1){
    			$res = $Member -> where(array('mobile'=>$username,'status'=>'1'))->find();
    		}else {
    			$res = $Member -> where(array('cardid'=>$username,'status'=>'1'))->find();
    		}
    	}else {
    		$res = $Member -> where(array('username'=>$username,'status'=>'1'))->find();
    	}
    	if($res and $res['password'] == $password_md5){
    		$res['is_save'] = @I('post.remember_pass');
    		$this->save_session($res);
    		if (empty($res['username'])){
    			$this->assign('jumpUrl',__APP__.'/member/complete_member');
    		}
    		$data_login['last_login_time'] = time();
    		$map['uid'] = $res['uid'];
    		$Member->where($map)->save($data_login);
    		if ( $logintype == 'top' ){
    			if($res['username'] == "" ) {
    				echo $res['mobile'];exit;
    			}else {
    				echo $res['username'];exit;
    			}
    		}else{
    			if (!I('post.jumpUrl')) {
    				$this->assign('jumpUrl',$_SESSION['url']);
    				$this->success('登录成功！',$_SESSION['url'],true);
    			}else{
    				$this->assign('jumpUrl',I('post.jumpUrl'));
    				$this->success('登录成功！',I('post.jumpUrl'),true);
    			}
    		}
    	}else{
    			$sess_data['is_save'] = I('post.remember_pass');
    			$model_card = D('Card');
    			$cardinfo = $model_card->where(array('cardid'=>$username,'password'=>$_POST['password'],'card_state'=>'0'))->find();
    			if ($cardinfo){
    				$data['cardid'] = I('post.username');
    				$data['password'] = $this->_pwdHash($password);
    				$data['reg_time'] = time();
    				$data['ip'] = get_client_ip();
    				if ($lastInsId = $Member->add($data)){
    					$save_data['card_state'] = 1;
    					$save_data['login_time'] = time();
    					$model_card->where("id='$cardinfo[id]'")->save($save_data);
    					$sess_data['uid'] = $lastInsId;
    					$sess_data['cardid'] = $_POST['username'];
    					$this->save_session($sess_data);
    					$this->assign('jumpUrl',__APP__.'/member/complete_member');
    					$data_login['last_login_time'] = time();
    					$map['uid'] = $lastInsId;
    					$Member->where($map)->save($data_login);
    					if ($logintype=='top'){
    						echo 2;exit;
    					}else{
    						$this->success("登录成功,请继续完善用户信息！",'/myhome',true);
    					}
    				}
    		}
    			
    		if ($logintype=='top'){
    			echo 0;exit;
    		}else{
    			$this->error('登录失败,密码错误！',null,true);
    		}
    	}
    }

    //找回密码
    public function findpass(){
        $step = isset($_GET['step'])?$_GET['step']:1;
        if($step==1){

        }elseif($step==2){
            //var_dump($_SESSION['findverify']);
            if(!$_SESSION['finduname'] && !$_SESSION['finduname2']){
                $this->error("找回密码超时","/public/findpass",false);
            }
        }elseif($step==3){
            //var_dump($_SESSION['finduname']);
            //var_dump($_SESSION['mobile']);
            if(!$_SESSION['mobile']){
                $this->error("找回密码超时","/public/findpass",false);
            }
        }elseif($step==4){
            $this->assign('username',$_SESSION['finduname']);
            $this->assign('mobile',$_SESSION['mobile']);
        }
        $this->assign('noshow',true);
        $this->assign ('noclose',true);
        $this->assign("step",$step);
        $this->display('findpass');
    }

    function findpass1verify(){
        $finduname = @I('post.finduname');
        $verify = @I('post.verify');
        if($_POST){
            if($_SESSION['verify'] != md5($verify)){
                $this->error("验证码错误",NULL,true);
            }else{
                if($finduname){
                    $Fmember = D('member');
                    $res = $Fmember->where('username = "'.$finduname.'"')->find();
                    if(!$res){
						$res1 = $Fmember->where('mobile = "'.$finduname.'"')->find();
						if(!$res1){
							$this->error("用户名或者手机号不存在",NULL,true);
						}else{
							session('finduname',null);
							session('finduname',$finduname);
							$this->success("验证成功","?step=2",true);
						}
                    }else{
						session('finduname2',$finduname);
						$this->success("验证成功","?step=2",true);
					}
                }else{
                    $this->error("用户名不能为空",NULL,true);
                }
            }
        }else{
            $this->error("访问异常","/public/findpass",false);
        }
    }

    function findpass2verify(){

        $verify = $_SESSION['findverify'];
        $mobile = $_SESSION['mobile'];
        $sendverify = I("post.sendverify");
        $mobile = I("post.mobile");
        if(!$sendverify){
           $this->error("访问异常","/public/findpass",false);
        }
        if($verify==md5($sendverify)){
            $this->success("验证成功","?step=3",true);
        }else{
            $this->error("验证码错误",NULL,true);
        }

    }
    function findpass3verify(){

        $finduname = $_SESSION['finduname'];
        $mobile = $_SESSION['mobile'];
        $password = md5(I("post.password"));
        $password2 = md5(I("post.password2"));

        if(!$password||!$password2||!$finduname||!$mobile){
            $this->error("访问异常","/public/findpass",false);
        }

        if($password==$password2){
            $data['password'] = $password;
            $MemberModel = D('member');
			if($_SESSION['finduname2']){
				$map['username'] = $_SESSION['finduname2'];
				$map['mobile'] = $mobile;
				$map['status'] = 1;
			}elseif($mobile ==$finduname){
				$map['mobile'] = $finduname;
				$map['status'] = 1;
			}else{
				$map['mobile'] =0;
				$map['status'] = 8;
			}
            $res = $MemberModel->where($map)->save($data);

            if($res !== false){
                $this->success("密码修改成功","?step=4",true);
            }else{
                $this->error("密码修改失败，请返回重新验证","?step=1",true);
            }
        }
    }
    //发送找回密码手机验证码
    function send_verify2() {
		$mobile = @$_REQUEST['mobile'];
		$key = @$_REQUEST['key'];
		$model_sms = D('Sms');
		$MemberModel = D('member');
		if($key!= md5($mobile)){
			$data['code']= 0;
			$data['message'] = '手机号不正确';
			$this->ajaxReturn($data);
			exit;
		}
		if ($mobile){
			if($_SESSION['finduname2']){
				$count = $MemberModel->where(array('mobile'=>$mobile,'username'=>$_SESSION['finduname2'],'status'=>'1'))->count();
			}elseif($_SESSION['finduname'] and $mobile ==$_SESSION['finduname']){
				$count = $MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->count();
			}else{
				$count=0;
			}
			if($count){
				$condition['phones'] = $mobile;
				$rand_verify = rand(10000, 99999);
				session('mobile',null);
				session('findverify',null);
				session('mobile',$mobile);
				session('findverify',md5($rand_verify));
				$verify_str = "正在为您的手机验证，您的短信验证码：".$rand_verify;
				$send_verify = array(
					'phones'=>$mobile,
					'content'=>$verify_str,
				);
				$return_data = $this->curl_sms($send_verify);

				$send_verify['sendtime'] = time();
				$model_sms->add($send_verify);
				$data['code']= 1;
				$data['message'] = 'success';
				$this->ajaxReturn($data);
				exit;
			}else{
				$data['code']= 2;
				$data['message'] = '该手机不是用户预留手机，请重新检查。';
				$this->ajaxReturn($data);
				exit;
			}
		}else {
			$data['message']='发送失败！发送过于频繁，请一分钟后再尝试。';
			$data['code']=3;
			$this->ajaxReturn($data);
			exit;
		}
    }
	//发送注册验证码
	function send_verify() {
		$mobile = @$_REQUEST['mobile'];
		//验证手机号的加密~~~
		$key = $_REQUEST['key'];
		$verify = $_REQUEST['verify'];
		//echo $key;exit;
		if($_SESSION['verify'] != md5($verify)){
			$data['message'] = '验证码错误';
			$data['code']=0;
			$this->ajaxReturn($data);
			exit;
		}elseif($key !=md5($mobile)){
			$data['message']='手机号码错误';
			$data['code']=1;
			$this->ajaxReturn($data);
			exit;
		}
		//进行加密~~·
		$model_sms = D('Sms');
		$MemberModel = D('member');
		if ($mobile){
			$count = $MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->count();
			if(!$count){
				$condition['phones'] = $mobile;
				$rand_verify = rand(10000, 99999);
				session('mobile',null);
				session('mobile_verify_xieche',null);
				session('mobile',$mobile);
				session('mobile_verify_xieche',md5($rand_verify));
				$verify_str = "正在为您的手机验证，进行注册登录 ，您的短信验证码：".$rand_verify;
				$send_verify = array(
					'phones'=>$mobile,
					'content'=>$verify_str,
				);
				$return_data = $this->curl_sms($send_verify);

				$send_verify['sendtime'] = time();
				$model_sms->add($send_verify);
				$data['message']='success';
				$data['code']=2;
				$this->ajaxReturn($data);
				echo 2;
				exit;
			}else{
				$data['message']='手机号已被注册';
				$data['code']=3;
				$this->ajaxReturn($data);
				echo 3;
				exit;
			}
		}else {
			$data['message']='发送失败！发送过于频繁，请一分钟后再尝试。';
			$data['code']=4;
			$this->ajaxReturn($data);
			echo 4;
			exit;
		}
	}
    
    public function reg() {
    	//判断手机验证码正确否
    	$mobile_verify = @$_POST['mobile_verify'];
    	$this->_valid($mobile_verify, '验证码不能为空');
    	
    	$username = @$_POST['username'];
    	$this->_valid($username, '用户名不能为空');
    	
    	$password = @$_POST['password'];
    	$this->_valid($username, '密码不能为空');
    	
    	$mobile = @$_POST['mobile'];
    	$this->_valid($username, '电话号码不能为空');
    	
    	$repassword = @$_POST['repassword'];
    	if ($password != $repassword) {
    		$this->_error('两次密码不相符,请重新输入密码');
    	}
    	
    	if ( session('mobile') !=$mobile || md5($mobile_verify) != session('mobile_verify_xieche') ) {
    		$this->_error('验证码不正确，请输入正确的手机号获取验证码');
    	}
    	
    	$Model = D('member');
    	if (false === $Model->create()) {
    		$this->error($Model->getError());
    	}
    	$Model->password = $this->pwdHash($password);
    	$uid = $Model->add();//先申请一个uid
    	if ($uid) { //保存成功
    		$_SESSION['uid'] = $uid;
    		$_SESSION['username'] = $username;
    		cookie('uid', $uid);
    		cookie('username', $username);
    		$rand = $this->_rand_string();
    		$rand = md5($rand.C('ALL_PS'));
    		cookie('x_id', $rand);
    		$data = array(
    				'uid'=>$uid,
    				'username'=>$username,
    				'x_id'=>$rand,
    		);
    		$xsession = M('Xsession');
    		$select_uid = $xsession->where("uid=$uid")->find();
    		if($select_uid){
    			$xsession->where("s_id=$select_uid[s_id]")->save($data);
    		}else{
    			$xsession ->add($data);
    		}
    		
    		//保存推荐人
    		if (!empty($_POST['ruid']) and !empty($_POST['register_code'])){
    			$data_reg['ruid'] = $_POST['ruid'] - UID_ADD;
    			$data_reg['register_code'] = $_POST['register_code'];
    			$data_reg['uid'] = $uid;
    			$data_reg['create_time'] = $_POST['create_time'];
    			$model_registerrecommend = D('registerrecommend');
    			if (false === $model_registerrecommend->create($data_reg)) {
    				$this->error($model_registerrecommend->getError(),null,null);
    			}
    			$model_registerrecommend->add();
    			 
    			$model_member = D('Member');
    			$condition['uid'] = $_POST['ruid'] - UID_ADD;
    			$model_member->where($condition)->setInc('recommend_number',1);
    		}
    		
    		//tipask库user添加
    		$model_user = D('User');
    		$tipask_data['uid'] = $uid;
    		$tipask_data['username'] = $username;
    		$tipask_data['cardid'] = empty($_POST['cardid'])?0:$_POST['cardid'];
    		$tipask_data['password'] = $this->pwdHash($password);
    		$tipask_data['email'] = $_POST['email'];
    		$tipask_data['mobile'] = $mobile;
    		$tipask_data['regtime'] = time();
    		$tipask_data['regip'] = get_client_ip();
    		$tipask_data['lastlogin'] = time();
    		$model_user->add($tipask_data);
    		
    		$this->assign('jumpUrl', '/index.php');
    		$this->success('注册成功!','/myhome',true);
    	}else {
    		//失败提示
    		$this->error('注册失败!',null,true);
    	}
    }

        //用户登录验证
    public function logincheck2(){
    	header("Content-type: text/html; charset=utf-8");
    	if(!$_POST['username']) {
    		if ($_POST['logintype']=='top'){
    			echo 3;exit;
    		}else{
    			$this->error('帐号错误！');
    		}
    	}elseif (!$_POST['password']){
    		if ($_POST['logintype']=='top'){
    			echo 4;exit;
    		}else{
    			$this->error('密码必须！');
    		}
    			
    	}
    	if (!isset($_POST['logintype']) || $_POST['logintype']!='top' ){
    		if (empty($_POST['verify'])){
    			$this->error('验证码必须！');
    		}
    	}
    	$password_md5 = pwdHash($_POST['password']);
    	if (!isset($_POST['logintype']) || $_POST['logintype']!='top' ){
//     		if($_SESSION['verify'] != md5($_POST['verify'])) {
//     			$this->error('验证码错误！');
//     		}
    	}
    	$Member = M('Member');
    	$username = $_POST['username'];
    
    	if (is_numeric($username)){
    		if (substr($username,0,1)==1){
    
    			$res = $Member -> where(array('mobile'=>$username,'status'=>'1'))->find();
    		}else {
    			$res = $Member -> where(array('cardid'=>$username,'status'=>'1'))->find();
    		}
    	}else {
    		$res = $Member -> where(array('username'=>$username,'status'=>'1'))->find();
    	}
    
    	if($res and $res['password'] == $password_md5){
    		$res['is_save'] = $_POST['remember_pass'];
    		$this->save_session($res);
    		if (empty($res['username'])){
    			$this->assign('jumpUrl',__APP__.'/member/complete_member');
    		}
    		$data_login['last_login_time'] = time();
    		$map['uid'] = $res['uid'];
    		$Member->where($map)->save($data_login);
    		if ($_POST['logintype']=='top'){
    			if($res['username'] == "" ) {
    				echo $res['mobile'];exit;
    			}else {
    				echo $res['username'];exit;
    			}
    		}else{
    			if (empty($_POST['jumpUrl'])) {
    				$this->assign('jumpUrl','myhome');
    			}
    			$this->assign('jumpUrl',$_POST['jumpUrl']);
    			$this->success('登录成功！');
    		}
    	}else{
    		if (is_numeric($_POST[username]) and strlen($_POST[username])==8){
    			$sess_data['is_save'] = $_POST['remember_pass'];
    
    			$model_card = D('Card');
    			$cardinfo = $model_card->where(array('cardid'=>$username,'password'=>$_POST['password'],'card_state'=>'0'))->find();
    			if ($cardinfo){
    				$data['cardid'] = $_POST['username'];
    				$data['password'] = pwdHash($_POST['password']);
    				$data['reg_time'] = time();
    				$data['ip'] = get_client_ip();
    				if ($lastInsId = $Member->add($data)){
    					$save_data['card_state'] = 1;
    					$save_data['login_time'] = time();
    					$model_card->where("id='$cardinfo[id]'")->save($save_data);
    					$sess_data['uid'] = $lastInsId;
    					$sess_data['cardid'] = $_POST['username'];
    					$this->save_session($sess_data);
    					$this->assign('jumpUrl',__APP__.'/member/complete_member');
    					$data_login['last_login_time'] = time();
    					$map['uid'] = $lastInsId;
    					$Member->where($map)->save($data_login);
    					if ($_POST['logintype']=='top'){
    						echo 2;exit;
    					}else{
    						$this->success("登录成功,请继续完善用户信息！");exit;
    					}
    				}
    			}
    		}
    			
    		if ($_POST['logintype']=='top'){
    			echo 0;exit;
    		}else{
    			$this->error('登录失败！');
    		}
    	}
    }

    //APP用户登录验证
    public function applogincheck(){
        $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
    	if(empty($_POST['username'])) {
			$xml_content .= "<status>2</status><tolken></tolken><desc>账号为空</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}elseif (empty($_POST['password'])){
			$xml_content .= "<status>3</status><tolken></tolken><desc>密码为空</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
		$password_md5 = pwdHash($_POST['password']);
    	
		$Member = M('Member');
		$username = $_POST['username'];
		if (is_numeric($username)){
		    if (substr($username,0,1)==1){
		        $res = $Member -> where("mobile='$username' AND status='1'")->find();
		    }else {
		        $res = $Member -> where("cardid='$username' AND status='1'")->find();
		    }
		}else {
		    $res = $Member -> where("username='$username' AND status='1'")->find();
		}
		if($res and $res['password'] == $password_md5){
			$this->save_session($res);
			$data_login['last_login_time'] = time();
			$rand = $this->_rand_string();
		    $tolken = md5($rand.C('ALL_PS'));
			$data_login['tolken'] = $tolken;
			$data_login['tolken_time'] = time()+3600*24*365;
			$map['uid'] = $res['uid'];
			$Member->where($map)->save($data_login);
			if (empty($res['username'])){
			    $xml_content .= "<status>1</status><tolken>".$tolken."</tolken><desc>登陆成功，继续完善用户资料</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit; 
			}
			$xml_content .= "<status>0</status><tolken>".$tolken."</tolken><desc>登陆成功</desc>";
			$model_order = D('Order');
			$map_o['uid'] = $res['uid'];
			$map_o['order_state'] = array('gt',0);
			$map_o['status'] = 1;
			$order = $model_order->where($map_o)->order("id DESC")->find();
			$res['username'] = g_substr($res['username'],10);
			$xml_content .= "<uid>".$res['uid']."</uid><username>".$res['username']."</username><truename>".$order['truename']."</truename><email>".$res['email']."</email><mobile>".$res['mobile']."</mobile><prov>".$res['prov']."</prov><city>".$res['city']."</city><area>".$res['area']."</area>";
			$xml_content.="</XML>";
			echo $xml_content;exit;			
		}else{
		    if (is_numeric($_POST['username']) and strlen($_POST['username'])==8){
		        $model_card = D('Card');
		        $cardinfo = $model_card->where("cardid='$_POST[username]' AND password='$_POST[password]' AND card_state='0'")->find();
		        if ($cardinfo){
		            $data['cardid'] = $_POST['username']; 
		            $data['password'] = pwdHash($_POST['password']);
		            $data['reg_time'] = time();
		            $data['ip'] = get_client_ip();
		            if ($lastInsId = $Member->add($data)){
		                $save_data['card_state'] = 1;
		                $save_data['login_time'] = time();
		                $model_card->where("id='$cardinfo[id]'")->save($save_data);
		                $sess_data['uid'] = $lastInsId;
		                $sess_data['cardid'] = $_POST['username'];
		                $this->save_session($sess_data);
		                $this->assign('jumpUrl',__APP__.'/member/complete_member');
		                $data_login['last_login_time'] = time();
		                
		                $rand = $this->_rand_string();
            		    $tolken = md5($rand.C('ALL_PS'));
            			$data_login['tolken'] = $tolken;
            			$data_login['tolken_time'] = time()+3600*12;
            		    
            			$map['uid'] = $lastInsId;
            			$Member->where($map)->save($data_login);
		                $xml_content .= "<status>1</status><tolken>".$tolken."</tolken><desc>登陆成功，继续完善用户资料</desc>";
            			$xml_content.="</XML>";
            			echo $xml_content;exit;
		            }
		        }
		    }
			$xml_content .= "<status>6</status><tolken></tolken><desc>登陆失败</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}	
    }



    public function save_session($res){
        $_SESSION['uid'] = $res['uid'];
		if($res['is_save'] == 1) {
			$expire = 3600*24*90;
		}
		cookie('uid', $res['uid'] , $expire);
		if ($res['username']){
		    $_SESSION['username'] = $res['username'];
		    cookie('username', $res['username']);
			
		}
		if ($res['mobile']){
		    $_SESSION['mobile'] = $res['mobile'];
		    cookie('mobile', $res['mobile'], $expire);
		}
		if ($res['cardid']){
		    $_SESSION['cardid'] = $res['cardid'];
		    cookie('cardid', $res['cardid'], $expire);
		}
		$rand = $this->_rand_string();
		$rand = md5($rand.C('ALL_PS'));
		cookie('x_id', $rand, $expire);
		$res['username'] = isset($res['username'])?$res['username']:'';			
		$res['cardid'] = isset($res['cardid'])?$res['cardid']:'0';			
		$data = array(
			'uid'=>$res['uid'],
			'username'=>$res['username'],
			'x_id'=>$rand,
			'login_time'=>time(),
		);
		
		$xsession = M('Xsession');
		$select_uid = $xsession->where("uid=$res[uid]")->find();
		
		if($select_uid){
			$list = $xsession->where("s_id=$select_uid[s_id]")->save($data);
		}else{	
			$xsession->add($data);
		}
        $delete_map['login_time'] = array('lt',time()-3600);
		$xsession->where($delete_map)->delete();
    }
     //MD5加密
	private function _pwdHash($password, $type = 'md5') {
		return hash ( $type, $password );
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
	private function _rand_string($len = 6, $type = '', $addChars = '') {
		$str = '';
		switch ($type) {
			case 0 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			case 1 :
				$chars = str_repeat ( '0123456789', 3 );
				break;
			case 2 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
				break;
			case 3 :
				$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
				break;
		}
		if ($len > 10) { //位数过长重复字符串一定次数
			$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
		}
		if ($type != 4) {
			$chars = str_shuffle ( $chars );
			$str = substr ( $chars, 0, $len );
		} else {
			// 中文随机字
			for($i = 0; $i < $len; $i ++) {
				$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
			}
		}
		return $str;
	}
	
    public function honour(){
        $this->assign('noshow',true);
        $this->assign('noclose',true);
        $this->display("honour");
    
        }
    public function aboutus(){
        $this->assign('noshow',true);
        $this->assign('noclose',true);
        $this->display("aboutus");
          
    }   
    
}







?>
