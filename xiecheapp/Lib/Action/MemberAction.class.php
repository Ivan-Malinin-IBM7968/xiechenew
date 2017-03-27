<?php
//用户操作
class MemberAction extends CommonAction {
	public function __construct(){
		parent::__construct();
		$this->MemberModel = D('Member');//用户表
	}

	public function index() {
		$this->_empty();exit();
	}

    public function _before_save(){
	    $_SESSION['r_data'] = $_POST;
	    $this->assign('jumpUrl',Cookie::get('_currentUrl_'));
		
		if (empty($_POST['username'])){
		    $this->error('用户名不能为空！');
		}
		/*
		*注册时 邮箱不用必填
		*杨生华 2013/4/16
		if (empty($_POST['email'])){
		    $this->error('邮箱不能为空！');
		}
		*/

		if ($_POST['email'] and !preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $_POST['email'])){
		    $this->error('邮箱格式错误！');
		}
	    if ($_POST['mobile'] and !eregi("^1[0-9]{10}$",$_POST['mobile'])){
		    $this->error('手机号码格式错误！');
		}
		$mode_member = D('member');
	    if ($_POST['username']){
	        $map['username'] = $_POST['username'];
		    if ($mode_member->where($map)->find()){
		        $this->error('用户名已存在，请重新填写！');
		    }
		    unset($map);
		}
		
	    if ($_POST['email']){
	        $map['email'] = $_POST['email'];
		    if ($mode_member->where($map)->find()){
		        $this->error('邮箱号码已存在，请重新填写！');
		    }
		    unset($map);
		}
		
	    if ($_POST['mobile']){
	        $map['mobile'] = $_POST['mobile'];
		    if ($mode_member->where($map)->find()){
		        $this->error('手机号码已存在，请重新填写！');
		    }
		    unset($map);
		}
		
	    if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}
	    if (!empty($_POST['brand_id']) || !empty($_POST['series_id']) || !empty($_POST['model_id']) ){
	        if (empty($_POST['brand_id']) || empty($_POST['series_id']) || empty($_POST['model_id'])){
	           $this->error('您的车辆信息没有选完整，请选全！'); 
	        }
		}else {
		    if (!empty($_POST['car_name'])){
		        unset($_POST['car_name']);
		    }
		}
	}
	public function user_save(){
		//取得方法名
		$name = $this->getActionName();
		$Model = D($name);
        if (false === $Model->create()) {
            $this->error($Model->getError());
        }
        //保存当前数据对象
       return $result = $Model->save();
	}
    public function save() {
		$this->user_save();
		if (!empty($_POST['brand_id']) and !empty($_POST['series_id']) and !empty($_POST['model_id'])){
		    $Model = D('Membercar');
		    if (empty($_POST['car_name'])){
		        $_POST['car_name'] = "未命名";
		    }
    		if (false === $Model->create()) {
    			$this->error($Model->getError());			
    		}
    		$Model->add();
		}
		if ($_POST['uid']) { //保存成功
			$_SESSION['uid'] = $_POST['uid'];
			$_SESSION['username'] = $_POST['username'];
			Cookie::set('uid', $_POST['uid']);
			Cookie::set('username', $_POST['username']);
			$rand = rand_string();
			$rand = md5($rand.C('ALL_PS'));
			Cookie::set('x_id', $rand);			
			$data = array(
				'uid'=>$_POST['uid'],
				'username'=>$_POST['username'],
				'x_id'=>$rand,
			);
			$xsession = M('Xsession');
			$select_uid = $xsession->where("uid=$_POST[uid]")->find();
			if($select_uid){
				$xsession->where("s_id=$select_uid[s_id]")->save($data);
			}else{				
				$xsession ->add($data);
			}
			if (Cookie::get('_currentUrl_') == '/index.php/member/complete_member'){
			    $this->assign('jumpUrl','/index.php');
			}else{
			    $this->assign('jumpUrl',Cookie::get('_currentUrl_'));
			}
			$this->success('修改成功!');
		} else {
			//失败提示
			$this->error('修改失败!');
		}	
	}



	public function complete_member(){
	    if (isset($_SESSION['username']) and !empty($_SESSION['username'])){
	        $this->error('请到个人信息修改页修改信息！');
	    }
	    if (isset($_SESSION['r_data'])){
	        $r_data = $_SESSION['r_data'];
	        $r_data['brand_id'] = 0;
	        $r_data['series_id'] = 0;
	        $r_data['model_id'] = 0;
	        if ($_SESSION['r_data']['brand_id']){
	            $r_data['brand_id'] = $_SESSION['r_data']['brand_id'];
	        }
	        if ($_SESSION['r_data']['series_id']){
	            $r_data['series_id'] = $_SESSION['r_data']['series_id'];
	        }
	        if ($_SESSION['r_data']['model_id']){
	            $r_data['model_id'] = $_SESSION['r_data']['model_id'];
	        }
	    }else{
	        $r_data['brand_id'] = 0;
	        $r_data['series_id'] = 0;
	        $r_data['model_id'] = 0;
	    }
	    unset($_SESSION['r_data']);
	    $this->assign('r_data',$r_data);
	    $this->assign('uid',$_SESSION['uid']);
	    Cookie::set('_currentUrl_', __SELF__);
	    $this->display();
	}


	/*
		@author:chf
		@function:显示最新填写用户资料和昵称
		@time:2014-07-04
	*/
	public function complete_membernew(){
	    if (isset($_SESSION['username']) and !empty($_SESSION['username'])){
	        $this->error('请到个人信息修改页修改信息！');
	    }
	    if (isset($_SESSION['r_data'])){
	        $r_data = $_SESSION['r_data'];
	        $r_data['brand_id'] = 0;
	        $r_data['series_id'] = 0;
	        $r_data['model_id'] = 0;
	        if ($_SESSION['r_data']['brand_id']){
	            $r_data['brand_id'] = $_SESSION['r_data']['brand_id'];
	        }
	        if ($_SESSION['r_data']['series_id']){
	            $r_data['series_id'] = $_SESSION['r_data']['series_id'];
	        }
	        if ($_SESSION['r_data']['model_id']){
	            $r_data['model_id'] = $_SESSION['r_data']['model_id'];
	        }
	    }else{
	        $r_data['brand_id'] = 0;
	        $r_data['series_id'] = 0;
	        $r_data['model_id'] = 0;
	    }
	    unset($_SESSION['r_data']);
	    $this->assign('r_data',$r_data);
	    $this->assign('uid',$_SESSION['uid']);
	    Cookie::set('_currentUrl_', __SELF__);
	    $this->display();
	}



	/*
		@author:chf
		@function:发送验证码
		@time:2013-12-30
	*/
	function send_verify() {
		$mobile = $_REQUEST['mobile'];
		$model_sms = D('Sms');

	    if (is_numeric($mobile)){
			$count = $this->MemberModel->where(array('mobile'=>$mobile,'status'=>'1'))->count();
			if($count == '0'){

				$mobile_send_count = $model_sms->where(array('phones'=>$mobile))->count();
				if($mobile_send_count<10) {
					/*添加发送手机验证码*/
					$condition['phones'] = $mobile;
					$rand_verify = rand(10000, 99999);
					$_SESSION['mobile_verify_xieche'] = md5($rand_verify);
					$verify_str = "正在为您的手机验证，进行注册登录 ，您的短信验证码：".$rand_verify;
					/*
					$send_verify = array(
						'phones'=>$mobile,
						'content'=>$verify_str,
					);
					$return_data = $this->curl_sms($send_verify);
					*/

					// dingjb 2015-09-29 09:50:06 切换到云通讯
					$send_verify = array(
						'phones'  => $mobile,
						'content' => array($rand_verify),
					);
					$return_data = $this->curl_sms($send_verify, null, 4, '37650');

					$send_verify['sendtime'] = time();
					//外网注视去掉保存进短信记录表
					$model_sms->add($send_verify);
					echo 1;
					exit;
				}
			}else{
				echo 2;
				exit;
			}
		}else {
			 echo -1;
		}


	}

	public function _before_add(){

	    //验证推荐码是否正确
	    if (isset($_GET['uid']) and !empty($_GET['uid'])){
	        $code = md5($_GET['uid'].REGISTER_CODE);
	        if (isset($_GET['registercode']) and !empty($_GET['registercode'])){
    	        if ($_GET['registercode'] != $code){
    	            $this->error('推荐码错误！');
    	        }else {
    	            $model_member = D('member');
    	            $userinfo = $model_member->find($_GET['uid']-UID_ADD);
    	            $this->assign('ruid',$_GET['uid']);
    	            $this->assign('userinfo',$userinfo);
    	            $this->assign('registercode',$_GET['registercode']);
    	        }
	        }else {
	            $this->error('推荐码不存在！');
	        }
	    }
	    $r_data = array('brand_id'=>0,'series_id'=>0,'model_id'=>0);
	    if (isset($_SESSION['r_data'])){
	        $r_data = $_SESSION['r_data'];
	        $r_data['brand_id'] = 0;
	        $r_data['series_id'] = 0;
	        $r_data['model_id'] = 0;
	        if ($_SESSION['r_data']['brand_id']){
	            $r_data['brand_id'] = $_SESSION['r_data']['brand_id'];
	        }
	        if ($_SESSION['r_data']['series_id']){
	            $r_data['series_id'] = $_SESSION['r_data']['series_id'];
	        }
	        if ($_SESSION['r_data']['model_id']){
	            $r_data['model_id'] = $_SESSION['r_data']['model_id'];
	        }
	    }
	    unset($_SESSION['r_data']);
	    $this->assign('r_data',$r_data);

		//$model = D('carbrand');
		//$brand = $model->select();
		//$this->assign('brand',$brand);
	}




	/*
	 * 添加用户
	 * 前置操作
	 * 判断验证码
	 */	
	public function _before_insert(){
		
	    $_SESSION['r_data'] = $_POST;
	    $this->assign('jumpUrl','add');
        $mode_member = D('member');
		if (empty($_POST['username'])){
		    $this->error('用户名不能为空！');
		}
	    if ($_POST['username']){
    	    if (preg_match("/^[0-9]\d*$/i", $_POST['username'])){
                $this->error('用户名不能以数字开头！');
    	    }
	        $map['username'] = $_POST['username'];
		    if ($mode_member->where($map)->find()){
				//echo "123";exit;
				 $this->error('用户名已存在，请重新填写！');
		    }
		    /*$xieche_member = D('XiecheMember');
	        if ($xieche_member->where($map)->find()){
				//echo "456";exit;
		        $this->error('用户名已存在，请重新填写！');
		    }*/
		    unset($map);
		}
		
		if (empty($_POST['password'])){
		    $this->error('密码不能为空！');
		}
		if (empty($_POST['repassword']) and $_SERVER['HTTP_REFERER']=='http://www.xieche.com.cn/member/add'){
		    $this->error('确认密码不能为空！');
		}
	    if ($_POST['password'] !=$_POST['repassword'] and $_SERVER['HTTP_REFERER']=='http://www.xieche.com.cn/member/add'){
		    $this->error('两次密码输入不一样！');
		}
		/*
		*注册时 邮箱不用必填
		*杨生华 2013/4/16
		if (empty($_POST['email'])){
		    $this->error('邮箱不能为空！');
		}
		*/
	    if ($_POST['email'] and !preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $_POST['email'])){
		    $this->error('邮箱格式错误！');
		}
	    if ($_POST['email']){
	        $map['email'] = $_POST['email'];
		    if ($mode_member->where($map)->find()){
		        $this->error('邮箱号码已存在，请重新填写！');
		    }
		    unset($map);
		}
		
		if (empty($_POST['mobile'])){
		    $this->error('手机号码不能为空！');
		}
	    if ($_POST['mobile'] and !eregi("^1[0-9]{10}$",$_POST['mobile'])){
		    $this->error('手机号码格式错误！');
		}
	    if ($_POST['mobile']){
	        $map['mobile'] = $_POST['mobile'];
		    if ($mode_member->where($map)->find()){
		        $this->error('手机号码已存在，请重新填写！');
		    }
		    unset($map);
		}

		$mobile_verify = $_POST['mobile_verify'];//手机短信验证码
		if(!$_POST['mobile_verify']){
			echo "<script>alert('请填写手机验证码');javascript:history.go(-1);</script>";
			exit;
		}
		if($_SESSION['mobile_verify_xieche'] != md5($mobile_verify)){
			echo "<script>alert('手机短信验证码填写有误');javascript:history.go(-1);</script>";
			exit;
		}
		/*
	    if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}*/
	    if (!empty($_POST['brand_id']) || !empty($_POST['series_id']) || !empty($_POST['model_id']) ){
	        if (empty($_POST['brand_id']) || empty($_POST['series_id']) || empty($_POST['model_id'])){
	           $this->error('您的车辆信息没有选完整，请选全！'); 
	        }
		}else {
		    if (!empty($_POST['car_name'])){
		        unset($_POST['car_name']);
		    }
		}
		/*判断是否是从平安入口登录*/
		if(PA_BANNER == 'pa'){
			$_POST['fromstatus'] = '33' ;
 		}
	}

	/*
	 * 插入操作
	 * 执行插入
	 * 
	 */
	public function insert() {
		/*xieche库添加
		$xieche_member = D('XiecheMember');
		if (false === $xieche_member->create($_POST,1)) {
			$this->error($xieche_member->getError());
		}
		$_POST['uid'] = $xieche_member->add();
		*/
		//判断手机验证码正确否
		
		
		$_POST['uid'] = $this->user_insert();
		if (!empty($_POST['brand_id']) and !empty($_POST['series_id']) and !empty($_POST['model_id'])){
		    $Model = D('Membercar');
		    if (empty($_POST['car_name'])){
		        $_POST['car_name'] = "未命名";
		    }
    		if (false === $Model->create()) {
    			$this->error($Model->getError());			
    		}
    		$Model->add();
		}
		if ($_POST['uid']) { //保存成功
			$_SESSION['uid'] = $_POST['uid'];
			$_SESSION['username'] = $_POST['username'];
			Cookie::set('uid', $_POST['uid']);
			Cookie::set('username', $_POST['username']);
			$rand = rand_string();
			$rand = md5($rand.C('ALL_PS'));
			Cookie::set('x_id', $rand);			
			$data = array(
				'uid'=>$_POST['uid'],
				'username'=>$_POST['username'],
				'x_id'=>$rand,
			);
			$xsession = M('Xsession');
			$select_uid = $xsession->where("uid=$_POST[uid]")->find();
			if($select_uid){
				$xsession->where("s_id=$select_uid[s_id]")->save($data);
			}else{				
				$xsession ->add($data);
			}
			//保存推荐人
			if (!empty($_POST['ruid']) and !empty($_POST['register_code'])){
			    $data_reg['ruid'] = $_POST['ruid'] - UID_ADD;
			    $data_reg['register_code'] = $_POST['register_code'];
			    $data_reg['uid'] = $_POST['uid'];
			    $data_reg['create_time'] = $_POST['create_time'];
			    $model_registerrecommend = D('registerrecommend');
        		if (false === $model_registerrecommend->create($data_reg)) {
            		$this->error($model_registerrecommend->getError());			
            	}
            	$model_registerrecommend->add();
            	
            	$model_member = D('Member');
            	$condition['uid'] = $_POST['ruid'] - UID_ADD;
            	$model_member->where($condition)->setInc('recommend_number',1);
			}
			
			//tipask库user添加
			$model_user = D('User');
			$tipask_data['uid'] = $_POST['uid'];
			$tipask_data['username'] = $_POST['username'];
			$tipask_data['cardid'] = empty($_POST['cardid'])?0:$_POST['cardid'];
			$tipask_data['password'] = pwdHash($_POST['password']);
			$tipask_data['email'] = $_POST['email'];
			$tipask_data['mobile'] = $_POST['mobile'];
			$tipask_data['regtime'] = time();
			$tipask_data['regip'] = get_client_ip();
			$tipask_data['lastlogin'] = time();
			$model_user->add($tipask_data);
			
			$this->assign('jumpUrl', '/index.php');
			$this->success('注册成功!');
		} else {
			//失败提示
			$this->error('注册失败!');
		}	
	}
	/*
	*用以执行insert操作返回值
	*
	*/
	public function user_insert(){
		//取得方法名
		$name = $this->getActionName();
		$Model = D($name);
        if (false === $Model->create()) {
            $this->error($Model->getError());
        }
        //保存当前数据对象
       return $insert_id = $Model->add();  
	}
	
	/*
	*用以执行appinsert操作返回值
	*
	*/
	public function appuser_insert(){
		//取得方法名
		$name = $this->getActionName();
		$Model = D($name);
        if (false === $Model->create()) {
            $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
            $xml_content .= "<status>51</status><tolken></tolken><desc>注册失败</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
        }
        //保存当前数据对象
       return $insert_id = $Model->add();
	}
	
	
	public function update_user(){
	    $name = $this->getActionName();
	    $Model = D($name);
	    $_POST['uid'] = $this->GetUserId();
	    $userinfo = $Model->find($_POST['uid']);
	    $_POST['username'] = $userinfo['username'];
	    if (!empty($_POST['password'])){
	        if (empty($_POST['repassword'])){
	            $this->error('密码必须！');
	        }
	        if (empty($_POST['oldpassword'])){
	            $this->error('请输入原来的密码!');
	        }
    	    if ($_POST['password'] !=$_POST['repassword']){
    		    $this->error('两次密码输入不一样！');
    		}
	        $oldpassword_md5 = pwdHash($_POST['oldpassword']);
    		//$Member = M('Member');
    		//$res = $Member -> where("username='$_POST[username]'")->find();
    		if ($userinfo and $userinfo['password'] != $oldpassword_md5){
            	$this->error('旧密码错误！'); 
    		}else {
    		    $_POST['password'] = pwdHash($_POST['password']);
    		}
	    }else {
            unset($_POST['password']);       
	    }
		/*
		*注册时 邮箱不用必填
		*杨生华 2013/4/16
		if (empty($_POST['email'])){
		    $this->error('邮箱不能为空！');
		}
		*/
		
		if (empty($_POST['mobile'])){
		    $this->error('手机号码不能为空！');
		}
	    if ($_POST['email'] and !preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $_POST['email'])){
		    $this->error('邮箱格式错误！');
		}
	    if ($_POST['mobile'] and !eregi("^1[0-9]{10}$",$_POST['mobile'])){
		    $this->error('手机号码格式错误！');
		}
	    if ($_POST['email'] and $_POST['email'] != $userinfo['email']){
	        $map['email'] = $_POST['email'];
		    if ($Model->where($map)->find()){
		        $this->error('邮箱号码已存在，请重新填写！');
		    }
		    unset($map);
		}
		
	    if ($_POST['mobile']  and $_POST['mobile'] != $userinfo['mobile']){
	        $map['mobile'] = $_POST['mobile'];
		    if ($Model->where($map)->find()){
		        $this->error('手机号码已存在，请重新填写！');
		    }
		    unset($map);
		}
	    if (false !== $Model->save($_POST)) {
			//tipask库user修改密码
			$model_user = D('User');
			$tipask_data['uid'] = $_POST['uid'];
			$tipask_data['password'] = $_POST['password'];
			$tipask_data['email'] = $_POST['email'];
			$tipask_data['phone'] = $_POST['mobile'];
			$model_user->save($tipask_data);

           $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
           $this->success('修改成功!');
        }else {
           $this->error('修改失败!');
        }
	}
	
	public function check_user_info(){
	    if (isset($_POST['username'])){
	        $mode_member = D('member');
	        $map['username'] = $_POST['username'];
	        if ($mode_member->where($map)->find()){
	            echo 1;exit;
	        }else {
	            echo 2;exit;
	        }
	    }
	    
	    if (isset($_POST['email'])){
	        $mode_member = D('member');
	        $map['email'] = $_POST['email'];
	        if ($mode_member->where($map)->find()){
	            echo 1;exit;
	        }else {
	            echo 2;exit;
	        }
	    }
	    
	    if (isset($_POST['mobile'])){
	        $mode_member = D('member');
	        $map['mobile'] = $_POST['mobile'];
	        if ($mode_member->where($map)->find()){
	            echo 1;exit;
	        }else {
	            echo 2;exit;
	        }
	    }
	}
	//用户排名
	public function memberrank(){
	    //排名规则：推荐有效用户个数、注册时间
	    $model_member = D('Member');
	    $count = $model_member->count();
        // 导入分页类
        import("@.ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        // 分页显示输出
        $page = $p->show();
    
        // 当前页数据查询
        $memberlist = $model_member->order('recommend_effective_number DESC, reg_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
    
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('memberlist', $memberlist);
        $this->display();
	}
	
	 function get_password(){
	    $username = isset($_GET['username'])?$_GET['username']:'';
	    $this->assign('username',$username);
		$this->display('get_password_1');
	}
	
	public function getpwd_bymobile(){
	    //判断验证码		
		/*if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}*/
	    if(empty($_POST['username'])) {
			$this->error('帐号错误！');
		}
		$username = $_POST['username'];
		$Member = M('Member');
	    if (is_numeric($username)){
		    if (substr($username,0,1)==1){
		        $res = $Member -> where("mobile='$username' AND status='1'")->find();
		    }else {
		        $res = $Member -> where("cardid='$username' AND status='1'")->find();
		    }
		}else {
		    $res = $Member -> where("username='$username' AND status='1'")->find();
		}
		if (!$res){
		    $this->error('输入的账号不存在！');
		}

		/*添加发送手机验证码*/
		$model_sms = D('Sms');
	    $condition['phones'] = $res['mobile'];
	    $smsinfo = $model_sms->where($condition)->order("sendtime DESC")->find();
	    $now = time();
		
		$rand_verify = rand(100000, 999999);
		$_SESSION['mobile_verify'] = md5($rand_verify);
		
		$verify_str = "您的验证码：".$rand_verify;
		/*
		$send_verify = array('phones'=>$res['mobile'],
		'content'=>$verify_str,
		);
		$return_data = $this->curl_sms($send_verify);
		*/
		// dingjb 2015-09-29 13:00:38 切换到云通讯
		$send_verify = array(
			'phones' => $res['mobile'],
			'content'=> array(
				$rand_verify
			)
		);
		$return_data = $this->curl_sms($send_verify, null, 4, '37656');

		$send_verify['sendtime'] = $now;
		$model_sms->add($send_verify);
		session_start();
		// 种手机号码
		$_SESSION['wk_mobile']=$res['mobile'];
		/*添加发送手机验证码*/
		$this->assign('username',$username);
		$this->assign('memberinfo',$res);
	    $this->display('get_password_2');
	}
	
	public function ajax_send_mobile_verify(){
	    $mobile = isset($_SESSION['wk_mobile'])?$_SESSION['wk_mobile']:'0';
	    if ($mobile){
	        $model_sms = D('Sms');
	        $condition['phones'] = $mobile;
	        $smsinfo = $model_sms->where($condition)->order("sendtime DESC")->find();
	        $now = time();
	        if (!$smsinfo || ($now - $smsinfo['sendtime'])>=60){
    	        import('@.ORG.Util.String');
    	        $rand_verify = String::randString(6, 1);
    	        $_SESSION['mobile_verify'] = md5($rand_verify);
    	        if ($mobile and $rand_verify){
    	            $verify_str = "验证码：".$rand_verify;
    	            $send_verify = array('phones'=>$mobile,
    	                                 'content'=>$verify_str,
    	                                );
    	            $return_data = $this->curl_sms($send_verify);
    	            $send_verify['sendtime'] = $now;
    	            $model_sms->add($send_verify);
    	            echo 1;exit;
    	            //echo $return_data;exit;
    	        }
	        }else{
	            echo -1;
	        }
	    }
	    exit;
	}
	public function check_mobile_verify(){
	    $verify_code = isset($_POST['verify_code'])?$_POST['verify_code']:'0';
	    if($_SESSION['mobile_verify'] != md5($verify_code)) {
			echo -1;
		}else {
		    echo 1;
		}
		exit;
	}
	public function password_edit(){
	    if (!isset($_POST['mobile_verify']) || empty($_POST['mobile_verify'])){
	        $this->error('请输入验证码！');
	    }
	    //判断验证码
		if($_SESSION['mobile_verify'] != md5($_POST['mobile_verify'])) {
			$this->error('验证码错误！');
		}
		if (empty($_POST['uid'])){
		    $this->error('数据错误！');
		}
		$this->assign('uid',$_POST['uid']);
		$this->display('get_password_3');
	}
	
	public function save_new_password(){
	    if (empty($_POST['password'])){
	        $this->error('请输入新密码！');
	    }
	    if (empty($_POST['repassword'])){
	        $this->error('请输入确认密码！');
	    }
	    if ($_POST['password'] != $_POST['repassword']){
	        $this->error('两次输入的密码不同！');
	    }
	    $model_member = D('Member');
	    $condition['uid'] = $_POST['uid'];
	    $data['password'] = pwdHash($_POST['password']);
	    $data['update_time'] = time();
	    if ($model_member->where($condition)->save($data)){
			
			header("Location:/Member/get_password_4");
	      
	    }else {
	        $this->error('密码修改失败！');
	    }
	}


	public function get_password_1(){//新版忘记密码 先给胡椒出页面 逻辑之后加上
		$this->display('get_password_1');
	}



	public function get_password_2(){//新版忘记密码 先给胡椒出页面 逻辑之后加上
		 //判断验证码		
		/*if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}*/
	    if(empty($_POST['username'])) {
			$this->error('帐号错误！');
		}
		$username = $_POST['username'];
		$Member = M('Member');
	    if (is_numeric($username)){
		    if (substr($username,0,1)==1){
		        $res = $Member -> where("mobile='$username' AND status='1'")->find();
		    }else {
		        $res = $Member -> where("cardid='$username' AND status='1'")->find();
		    }
		}else {
		    $res = $Member -> where("username='$username' AND status='1'")->find();
		}
		if (!$res){
		    $this->error('输入的账号不存在！');
		}

		/*添加发送手机验证码*/
		$model_sms = D('Sms');
	    $condition['phones'] = $res['mobile'];
	    $smsinfo = $model_sms->where($condition)->order("sendtime DESC")->find();
	    $now = time();
		
		$rand_verify = rand(100000, 999999);
		$_SESSION['mobile_verify'] = md5($rand_verify);
		
		$verify_str = "验证码：".$rand_verify;
		$send_verify = array('phones'=>$res['mobile'],
		'content'=>$verify_str,
		);
		$return_data = $this->curl_sms($send_verify);
		$send_verify['sendtime'] = $now;
		$model_sms->add($send_verify);
		/*添加发送手机验证码*/
				
		$this->assign('username',$username);
		$this->assign('memberinfo',$res);

		$this->display('get_password_2');
	}
	public function get_password_3(){//新版忘记密码 先给胡椒出页面 逻辑之后加上
		 if (!isset($_POST['mobile_verify']) || empty($_POST['mobile_verify'])){
	        $this->error('请输入验证码！');
	    }
	    //判断验证码
		if($_SESSION['mobile_verify'] != md5($_POST['mobile_verify'])) {
			$this->error('验证码错误！');
		}
		if (empty($_POST['uid'])){
		    $this->error('数据错误！');
		}
		$this->assign('uid',$_POST['uid']);
		$this->display('get_password_3');
	}
	public function get_password_4(){//新版忘记密码 先给胡椒出页面 逻辑之后加上
		$this->display('get_password_4');
	}

	
	/*
	 * 插入操作
	 * 执行插入
	 * 
	 */
	public function appinsert() {
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
        $mode_member = D('member');
		if (empty($_POST['username'])){
		    $xml_content .= "<status>1</status><tolken></tolken><desc>用户名为空</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
	    if ($_POST['username']){
    	    if (preg_match("/^[0-9]\d*$/i", $_POST['username'])){
                $xml_content .= "<status>2</status><tolken></tolken><desc>用户名不能以数字开头</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
    	    }
	        $map['username'] = $_POST['username'];
		    if ($mode_member->where($map)->find()){
		        $xml_content .= "<status>3</status><tolken></tolken><desc>用户名已存在</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
		    }
		    unset($map);
		}
		
		if (empty($_POST['password'])){
		    $xml_content .= "<status>4</status><tolken></tolken><desc>密码为空</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
		if (empty($_POST['repassword'])){
		    $xml_content .= "<status>5</status><tolken></tolken><desc>确认密码为空</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
	    if ($_POST['password'] !=$_POST['repassword']){
		    $xml_content .= "<status>6</status><tolken></tolken><desc>两次密码不一样</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
		
	    if ($_POST['email'] and !preg_match("/^[0-9a-zA-Z]+(?:[\_\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i", $_POST['email'])){
		    $xml_content .= "<status>8</status><tolken></tolken><desc>邮箱格式错误</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
	    if ($_POST['email']){
	        $map['email'] = $_POST['email'];
		    if ($mode_member->where($map)->find()){
		        $xml_content .= "<status>9</status><tolken></tolken><desc>邮箱已存在</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
		    }
		    unset($map);
		}
		
		if (empty($_POST['mobile'])){
		    $xml_content .= "<status>10</status><tolken></tolken><desc>手机号码为空</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
	    if ($_POST['mobile'] and !eregi("^1[0-9]{10}$",$_POST['mobile'])){
		    $xml_content .= "<status>11</status><tolken></tolken><desc>手机号码格式错误</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}
	    if ($_POST['mobile']){
	        $map['mobile'] = $_POST['mobile'];
		    if ($mode_member->where($map)->find()){
		        $xml_content .= "<status>12</status><tolken></tolken><desc>手机号码已存在</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
		    }
		    unset($map);
		}
		
	    if (!empty($_POST['brand_id']) || !empty($_POST['series_id']) || !empty($_POST['model_id']) ){
	        if (empty($_POST['brand_id']) || empty($_POST['series_id']) || empty($_POST['model_id'])){
	            $xml_content .= "<status>14</status><tolken></tolken><desc>车辆信息没有选完整</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;
	        }
		}else {
		    if (!empty($_POST['car_name'])){
		        unset($_POST['car_name']);
		    }
		}
		$tolken = rand_string();
		$tolken = md5($tolken.C('ALL_PS'));
		$_POST['tolken'] = $tolken;
		$_POST['tolken_time'] = time()+3600*12*365;
		$_POST['uid'] = $this->appuser_insert();
		
		if (!empty($_POST['brand_id']) and !empty($_POST['series_id']) and !empty($_POST['model_id'])){
		    $Model = D('Membercar');
		    if (empty($_POST['car_name'])){
		        $_POST['car_name'] = "未命名";
		    }
    		if (false === $Model->create()) {
    			//$this->error($Model->getError());		
    			$xml_content .= "<status>50</status><tolken></tolken><desc>其他错误</desc>";
    			$xml_content.="</XML>";
    			echo $xml_content;exit;	
    		}
    		$Model->add();
		}
		if ($_POST['uid']) { //保存成功
			$_SESSION['uid'] = $_POST['uid'];
			$_SESSION['username'] = $_POST['username'];
			//Cookie::set('uid', $_POST['uid']);
			//Cookie::set('username', $_POST['username']);
			$rand = rand_string();
			$rand = md5($rand.C('ALL_PS'));
			//Cookie::set('x_id', $rand);			
			$data = array(
				'uid'=>$_POST['uid'],
				'username'=>$_POST['username'],
				'x_id'=>$rand,
			);
			$xsession = M('Xsession');
			$select_uid = $xsession->where("uid=$_POST[uid]")->find();
			if($select_uid){
				$xsession->where("s_id=$select_uid[s_id]")->save($data);
			}else{				
				$xsession ->add($data);
			}
			//保存推荐人
			if (!empty($_POST['ruid']) and !empty($_POST['register_code'])){
			    $data_reg['ruid'] = $_POST['ruid'] - UID_ADD;
			    $data_reg['register_code'] = $_POST['register_code'];
			    $data_reg['uid'] = $_POST['uid'];
			    $data_reg['create_time'] = $_POST['create_time'];
			    $model_registerrecommend = D('registerrecommend');
        		if (false === $model_registerrecommend->create($data_reg)) {
            		$xml_content .= "<status>50</status><tolken></tolken><desc>其他错误</desc>";
        			$xml_content.="</XML>";
        			echo $xml_content;exit;
            	}
            	$model_registerrecommend->add();
            	
            	$model_member = D('Member');
            	$condition['uid'] = $_POST['ruid'] - UID_ADD;
            	$model_member->where($condition)->setInc('recommend_number',1);
			}
			//$this->assign('jumpUrl', '/index.php');
			$xml_content .= "<status>0</status><tolken>".$tolken."</tolken><desc>注册成功</desc>";
			$_POST['username'] = g_substr($_POST['username'],10);
			$xml_content .= "<username>".$_POST['username']."</username><email>".$_POST['email']."</email><mobile>".$_POST['mobile']."</mobile>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		} else {
			//失败提示
			$xml_content .= "<status>51</status><tolken></tolken><desc>注册失败</desc>";
			$xml_content.="</XML>";
			echo $xml_content;exit;
		}	
	}
/*
	
	
		//注册
	public function register(){
		//判断验证码		
		if($_SESSION['verify'] != md5($_POST['verify'])) {
			$this->error('验证码错误！');
		}else{
			$this->_after_UserCarInsert();
		}
	}
//添加用户
	public function _before_MemberInsert(){
		//取得方法名
		$name = $this->getActionName();
			$Model = D($name);
        if (false === $Model->create()) {
            $this->error($Model->getError());
        }
        //保存当前数据对象
       return $insert_id = $Model->add();
	}
//添加用户自定义车型
	public function _after_UserCarInsert(){		
		$_POST['uid'] = $insert_id = $this->_before_MemberInsert();
		$Model = D('Membercar');
			if (false === $Model->create()) {
				$this->error($Model->getError());			
			}
				$list = $Model->add();		
			if ($list !== false) { //保存成功
				$this->assign('jumpUrl', Cookie::get('_currentUrl_'));
				$this->success('新增成功!');
			} else {
				//失败提示
				$this->error('新增失败!');
			}	
	}
*/
}