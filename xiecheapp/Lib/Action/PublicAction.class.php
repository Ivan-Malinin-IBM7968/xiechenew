<?php
class PublicAction extends CommonAction {

	public function index(){
		//如果通过认证跳转到首页
		redirect(__APP__);
	}
	
	
	
	public function login(){
		$uid = $this->GetUserId();
		if($uid){
			redirect(__APP__);
		}else{
			$this->display("login_bt");
		}	
		
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
			Cookie::clear();
			$this->assign("jumpUrl",__APP__);
            $this->success('登出成功！');
        }else {
			if($_SESSION['uid']) {
				Cookie::clear();
			}
            $this->error('已经登出！');
        }
    }
    //用户登录验证
    public function logincheck(){
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
        	if($_SESSION['verify'] != md5($_POST['verify'])) {
    			$this->error('验证码错误！');
    		}
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
    public function cut_test(){
        $str = "啊啊啊啊啊啊";
        echo g_substr($str,10);exit;
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
			$rand = rand_string();
		    $tolken = md5($rand.C('ALL_PS'));
			$data_login['tolken'] = $tolken;
			$data_login['tolken_time'] = time()+3600*24*365;
			$map['uid'] = $res['uid'];
			$Member->where($map)->save($data_login);
			if (empty($res['username'])){

				$res['username'] = $res['mobile'];
			    $xml_content .= "<status>0</status><tolken>".$tolken."</tolken><desc>登陆成功，继续完善用户资料</desc>";
//    			$xml_content.="</XML>";
//    			echo $xml_content;exit; 
			}else {
				$xml_content .= "<status>0</status><tolken>".$tolken."</tolken><desc>登陆成功</desc>";
			}
			
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
		                
		                $rand = rand_string();
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
    
}







?>
