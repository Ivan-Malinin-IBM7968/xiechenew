<?php
// 后台用户模块
class UserAction extends CommonAction {
	function _filter(&$map){
		  $map['id'] = array('egt',2);
		$map['account'] = array('like',"%".$_POST['account']."%");
	}
	function _before_add(){
	    $model_user_shop = D(GROUP_NAME.'/Usershop');
        $shoplist = $model_user_shop->getshoplist();
        $this->assign('shoplist',$shoplist); // 模板变量赋值  
	}
    function _before_edit(){
        $model_usershop = D(GROUP_NAME.'/Usershop');
        $shoplist = $model_usershop->getshoplist();
        
        $model_user_shop = D(GROUP_NAME.'/User_shop');
        $condition['authid'] = $_GET['id'];
        $user_shop = $model_user_shop->where($condition)->find();
        $this->assign('shoplist',$shoplist); // 模板变量赋值  
        $this->assign('user_shop',$user_shop);
	}
    function _before_update(){
        if (isset($_POST['shop_id']) and $_POST['shop_id']){
            $data['shop_id'] = $_POST['shop_id'];
            $condition['authid '] = $_POST['id'];
            $model_user_shop = D(GROUP_NAME.'/user_shop');
            $model_user_shop->where($condition)->save($data);
        }
	}
	// 检查帐号
	public function checkAccount() {
        if(!preg_match('/^[a-z]\w{4,}$/i',$_POST['account'])) {
            $this->error( '用户名必须是字母，且5位以上！');
        }
		$User = M(GROUP_NAME."/"."User");
        // 检测用户名是否冲突
        $name  =  $_REQUEST['account'];
        $result  =  $User->getByAccount($name);
        if($result) {
        	$this->error('该用户名已经存在！');
        }else {
           	$this->success('该用户名可以使用！');
        }
    }

	// 插入数据
	public function insert() {
		// 创建数据对象
		$User	 =	 D(GROUP_NAME."/"."User");
		if(!$User->create()) {
			$this->error($User->getError());
		}else{
			// 写入帐号数据
			if($result	 =	 $User->add()) {
				$this->addRole($result);
				if (isset($_POST['shop_id']) and $_POST['shop_id']){
				    $data['authid'] = $result;
				    $data['shop_id'] = $_POST['shop_id'];
				    $model_user_shop = D(GROUP_NAME.'/user_shop');
				    $model_user_shop->add($data);
				}
				$this->success('用户添加成功！');
			}else{
				$this->error('用户添加失败！');
			}
		}
	}

	protected function addRole($userId) {
		//新增用户自动加入相应权限组
		$RoleUser = M(GROUP_NAME."/"."RoleUser");
		$RoleUser->user_id	=	$userId;
        // 默认加入网站编辑组
        $RoleUser->role_id	=	3;
		$RoleUser->add();
	}

    //重置密码
    public function resetPwd()
    {
    	$id  =  $_POST['id'];
        $password = $_POST['password'];
        if(''== trim($password)) {
        	$this->error('密码不能为空！');
        }
        $User = D(GROUP_NAME.'/User');
		$data['password']=	md5($password);
		$condition['id'] = $id;
		$result	= $User->where($condition)->save($data);
        if(false !== $result) {
            $this->success("密码修改为$password");
        }else {
        	$this->error('重置密码失败！');
        }
    }
}
?>