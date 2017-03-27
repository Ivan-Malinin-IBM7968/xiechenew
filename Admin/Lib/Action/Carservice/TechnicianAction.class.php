<?php
//首页
class TechnicianAction extends CommonAction {	
	function __construct() {
		parent::__construct();
		$this->UserroleModel = M('tp_admin.role_user','xc_');//网站用户关系表
		$this->UserModel = M('tp_admin.user','xc_');//网站用户表
		$this->TechnicianModel = M('tp_xieche.technician','xc_');//技师信息拓展表
		$this->CarbrandModel = M('tp_xieche.carbrand','xc_');//技师信息拓展表
	}
	//技师列表
	public function index() {
		$map['xc_role_user.role_id'] = 17;
		// 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count,25);
		foreach ($_POST as $key => $val) {
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/" . urlencode($val) . "/";
			}
		}
		if(!$_REQUEST['p']){
			$p->parameter = "index/".$p->parameter;
		}
		
        // 分页显示输出
        $page = $p->show_admin();
		// 计算总数
        $count = $this->UserModel->where($map)->count();

        // 当前页数据查询
		$sql="SELECT a.id FROM tp_admin.xc_user as a,tp_admin.xc_role_user as b where a.id = b.user_id and b.role_id =17";
		$list = $this->UserModel->query($sql);
        //$list = $this->UserroleModel->join('xc_role on xc_role.id=xc_role_user.role_id')->where($map)->order('create_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
		//echo $this->UserModel->getLastsql();
		//print_r($list);
		if(is_array($list)){
			foreach($list as $k=>$v){
				$info = $this->UserModel->where(array('id'=>$v['id']))->find();
				$extra_info = $this->TechnicianModel->where(array('user_id'=>$v['id']))->find();
				//print_r($extra_info);
				$list[$k]['user_id'] = $v['id'];
				$list[$k]['id'] = $extra_info['id'];
				$list[$k]['mobile'] = $info['mobile'];
				$list[$k]['truename'] = $extra_info['truename'];
				$list[$k]['address'] = $extra_info['address'];
				$list[$k]['brand_name'] = $extra_info['brand'];
				if($extra_info['status']==2){
					unset($list[$k]);
				}
			}
		}
		//print_r($list);
		$this->assign('list',$list);
		$this->display();
	}

	//添加技师
	function add(){
		//获取品牌列表
		$brand_list = $this->CarbrandModel->select();
		$this->assign('brand_list',$brand_list);
		$this->display();
	}

	function do_add(){
		if($_REQUEST['user_id']){
			$data['account'] = $_REQUEST['account'];
			$data['password'] = md5($_REQUEST['password']);
			$data['nickname'] = $_REQUEST['nickname'];
			$data['mobile'] = $_REQUEST['mobile'];
			$data['remark'] = $_REQUEST['remark'];
			$data['update_time'] = time();
			$this->UserModel->where(array('id'=>$_REQUEST['user_id']))->save($data);

			//编辑技师扩展信息
			$data2['truename'] = $_REQUEST['nickname'];
			$data2['address'] = $_REQUEST['address'];
			$data2['brand'] = $_REQUEST['brand'];
			$data2['pad_mobile'] = $_REQUEST['pad_mobile'];
			$data2['pad_sim'] = $_REQUEST['pad_sim'];
			$data2['create_time'] = time();
			$data2['status'] = $_REQUEST['status'];
			$technician_id = $this->TechnicianModel->where(array('user_id'=>$_REQUEST['user_id']))->save($data2);
			if($technician_id){
				$this->success('编辑技师成功');
			}else{
				$this->error('编辑失败');
			}
		}else{
			$data['account'] = $_REQUEST['account'];
			$data['password'] = md5($_REQUEST['password']);
			$data['nickname'] = $_REQUEST['nickname'];
			$data['mobile'] = $_REQUEST['mobile'];
			$data['remark'] = $_REQUEST['remark'];
			$data['create_time'] = time();
			$user_id = $this->UserModel->add($data);
			echo $this->UserModel->getlastsql();

			if($user_id) {
				//绑定技师身份
				$data1['role_id'] = 17;
				$data1['user_id'] = $user_id;
				$this->UserroleModel->add($data1);
				//添加技师扩展信息
				$data2['user_id'] = $user_id;
				$data2['truename'] = $_REQUEST['nickname'];
				$data2['address'] = $_REQUEST['address'];
				$data2['brand'] = $_REQUEST['brand'];
				$data2['pad_mobile'] = $_REQUEST['pad_mobile'];
				$data2['pad_sim'] = $_REQUEST['pad_sim'];
				$data2['create_time'] = time();
				$technician_id = $this->TechnicianModel->add($data2);
				echo $this->TechnicianModel->getlastsql();
				if($technician_id){
					$this->success('新增技师成功');
				}else{
					$this->error('新增失败');
				}
			}else {
				$this->error('注册失败');
			}
		}
	}

	//编辑技师
	function edit(){
		$info = $this->UserModel->where(array('id'=>$_REQUEST['user_id']))->find();
		//echo $this->UserModel->getlastsql();
		//print_r($info);
		$extra_info = $this->TechnicianModel->where(array('user_id'=>$_REQUEST['user_id']))->find();
		$this->assign('user_id',$_REQUEST['user_id']);
		$this->assign('info',$info);
		$this->assign('extra_info',$extra_info);
		$this->display();
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

}
?>

