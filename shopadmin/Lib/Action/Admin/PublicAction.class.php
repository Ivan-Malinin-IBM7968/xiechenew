<?php

class PublicAction extends CommonAction {
	// 检查用户是否登录
	protected function checkUser() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl',__APP__.'/Public/login');
			//$this->error('没有登录');
			header("Location: ".__APP__."/Public/login");
		}
	}

    protected function pub_display() {
        $this->display('./Tpl/Public/'.ACTION_NAME.'.html');
    }
	// 顶部页面
	public function top() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl',__APP__.'/Public/login');
			//$this->error('没有登录');
			header("Location: ".__APP__."/Public/login");
		}
		C('SHOW_RUN_TIME',false);			// 运行时间显示
		C('SHOW_PAGE_TRACE',false);
		$model	=	D(GROUP_NAME."/"."Group");
        /*if($_SESSION['administrator']) {
            $list	=	$model->getField('id,title','status=1');
        }else{
		    $list	=	$model->where('status=1 AND id IN ('.join(",",$_SESSION['_GROUP_LIST']).')')->getField('id,title');
        }*/
        $list	=	$model->where('status=1')->select();
        //echo $model->getLastSql();
		$this->assign('nodeGroupList',$list);
		$this->pub_display();
	}
	// 尾部页面
	public function footer() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
			$this->assign('jumpUrl',__APP__.'/Public/login');
			//$this->error('没有登录');
			header("Location: ".__APP__."/Public/login");
		}
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
                $accessList = $_SESSION['_ACCESS_LIST'];
                //echo '<pre>';print_r($accessList);echo GROUP_NAME;
                foreach($list as $key=>$module) {
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

    // 后台首页 查看系统信息
    public function main() {
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            'PHP运行方式'=>php_sapi_name(),
            'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
            'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
            'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
            'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
            );
        $this->assign('info',$info);
        $this->pub_display();
    }

	// 用户登录页面
	public function login() {
		if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
            //$array['test'] = 'test';
            $this->assign($array);
			$this->pub_display();
		}else{
			$this->redirect('Admin-Index/index');
		}
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
		//import('ORG.Util.RBAC');
        //$authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
		$model_shopadmin = D(GROUP_NAME.'/shopadmin');
		$authInfo = $model_shopadmin->where($map)->find();
        if(false === $authInfo) {
            $this->error('帐号不存在或已禁用！');
        }else {
            if($authInfo['password'] != pwdHash($_POST['password'])) {
            	$this->error('密码错误！');
            }
            $_SESSION[C('USER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['loginUserName']		=	$authInfo['nickname'];
            $_SESSION['lastLoginTime']		=	$authInfo['last_login_time'];
			$_SESSION['login_count']	=	$authInfo['login_count'];
            $_SESSION['user_type']    =  $authInfo['type_id'];
            $_SESSION['remark']    =  $authInfo['remark'];
            if($authInfo['account']=='admin') {
            	$_SESSION['administrator']		=	true;
            }
            //获取店铺ID
            $authId = $_SESSION[C('USER_AUTH_KEY')];
            $model_user_shop = D(GROUP_NAME.'/user_shop');
            $condition['authid'] = $authId;
            $user_shop_info = $model_user_shop->where($condition)->find();
            if (isset($user_shop_info['shop_id'])){
                $_SESSION['shop_id'] = $user_shop_info['shop_id'];
				$model_shop = M('tp_xieche.Shop','xc_'); 
				$shop_info = $model_shop->find($user_shop_info['shop_id']);
				$_SESSION['safestate'] = $shop_info['safestate'];
				$_SESSION['shop_name'] = $shop_info['shop_name'];

				$model_salecoupon = M('tp_xieche.Salecoupon','xc_');
				$shops_salecoupon = $model_salecoupon->find('1');//抵用券id
				$_SESSION['shop_salecoupon'] = $shops_salecoupon['shop_ids'];

            }
            //保存登录信息
			$User	=	D(GROUP_NAME."/".'Shopadmin');
			$ip		=	get_client_ip();
			$time	=	time();
            $data = array();
			$data['id']	=	$authInfo['id'];
			$data['last_login_time']	=	$time;
			$data['login_count']	=	array('exp','(login_count+1)');
			$data['last_login_ip']	=	$ip;
			$User->save($data);
            //$_SESSION['loginId']		=	$loginId;
			// 缓存访问权限
            //RBAC::saveAccessList();
			$this->success('登录成功！');
		}
	}

	public function profile() {
		$this->checkUser();
		$User	 =	 D(GROUP_NAME."/"."Shopadmin");
		$vo	=	$User->getById($_SESSION[C('USER_AUTH_KEY')]);
		
		$model_shop = M('tp_xieche.Shop','xc_'); 
		$shop_info = $model_shop->find($_SESSION['shop_id']);
		$vo['shop_mobile'] = $shop_info['shop_mobile'];
		$vo['shop_salemobile'] = $shop_info['shop_salemobile'];

		$this->assign('vo',$vo);
		$this->pub_display();
	}

	// 修改资料
	public function change() {
		$this->checkUser();
		$User	 =	 D(GROUP_NAME."/"."Shopadmin");
		if(!$User->create()) {
			$this->error($User->getError());
		}
		$result	=	$User->save();

		if($_REQUEST['shop_mobile']) {
			$model_shop = M('tp_xieche.Shop','xc_');
			$shop_id = $_SESSION['shop_id'];
			$data['shop_mobile'] = $_REQUEST['shop_mobile'];
			$data['shop_salemobile'] = $_REQUEST['shop_salemobile'];
			$data['id'] = $shop_id;
			$model_shop->save($data);
		}
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
        $User    =   D(GROUP_NAME."/"."Shopadmin");
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

}
?>
