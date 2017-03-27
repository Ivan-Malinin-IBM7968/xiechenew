<?php
//用户自定义车型controller
class MembercarAction extends CommonAction {
	function __construct() {
		parent::__construct();
		if( true !== $this->login()){
			exit;
		}
	}
	public function _before_add(){
	    $r_data = array('brand_id'=>0,'series_id'=>0,'model_id'=>0);
	    if (isset($_SESSION['r_data'])){
	        $r_data = $_SESSION['r_data'];
	    }
	    $this->assign('r_data',$r_data);
	    unset($_SESSION['r_data']);
		$model = D('carbrand');
		$brand = $model->select();
		$this->assign('brand',$brand);
	}
	public function _before_update(){
	    if (isset($_POST['s_pro']) and isset($_POST['car_number'])){
		    $_POST['car_number'] = $_POST['s_pro']."_".$_POST['car_number'];
		    unset($_POST['s_pro']);
		}
	    if (empty($_POST['brand_id']) || empty($_POST['series_id']) || empty($_POST['model_id'])){
		    $this->error('您的车辆信息没有选完整，请选全！');
		}
		if (empty($_POST['car_name'])){
		    $_POST['car_name'] = "未命名";
		}
	}
	
    public function _before_read(){
		$model = D('carbrand');
		$brand = $model->select();
		$this->assign('brand',$brand);
	}
	/*
	 * 添加用户自定义车型
	 * 
	 */
	public function save(){
	    $_SESSION['r_data'] = $_POST;
		$Model =D('Membercar');
		if (isset($_POST['s_pro']) and isset($_POST['car_number'])){
		    $_POST['car_number'] = $_POST['s_pro']."_".$_POST['car_number'];
		    unset($_POST['s_pro']);
		}
	    if (empty($_POST['brand_id']) || empty($_POST['series_id']) || empty($_POST['model_id'])){
	        $this->assign('jumpUrl','add');
		    $this->error('您的车辆信息没有选完整，请选全！');
		}
		if (empty($_POST['car_name'])){
		    $_POST['car_name'] = "未命名";
		}
		$_POST['create_time'] = time();
		$map['uid'] = $this->GetUserId();
		$map['status'] = 1;
		$map['is_default'] = 1;
		$membercar = $Model->where($map)->select();
		if (!$membercar){
		    $_POST['is_default'] = 1;
		}
		$list = $Model -> add_car();
		$this->action_tip($list);
	}
	/*
	 * 编辑平均油耗计算方式
	 */
	public function edit_avgoil_type(){
		//get检测
		$get_avgoil_type = isset($_GET['avgoil_type'])?intval($_GET['avgoil_type']):null;
		$get_u_c_id = isset($_GET['u_c_id'])?intval($_GET['u_c_id']):null;
		//判断
		if(!empty($get_avgoil_type) && !empty($get_u_c_id)){
			$model = D('Membercar');
			$uid = $model->GetUserId();
			$list = $model->where("uid=$uid AND u_c_id=$get_u_c_id")->find();
			if(is_array($list)){
				$data['avgoil_type'] = $get_avgoil_type;
				$list = $model->where("u_c_id=$get_u_c_id")->save($data);
				if($list){
					js_back('成功');					
				}else{
					js_back('失败');
					
				}
			}
		}
	}
	public function code_help(){
	    $this->display('codehelp');
	}
	public function getmycar(){
	    $xml_content="<?xml   version=\"1.0\"   encoding=\"UTF-8\"?><XML>";
	    if (isset($_GET['tolken'])){
	        $tolken = $_GET['tolken'];
	        $model_member = D('Member');
	        $membermap['tolken'] = $tolken;
	        $member = $model_member->where($membermap)->find();
	        if ($member){
    	        $model_membercar = D('Membercar');
    	        $membercarmap['uid'] = $member['uid'];
    	        $membercar = $model_membercar->where($membercarmap)->select();
    	        if ($membercar){
    	            foreach ($membercar as $key=>$val){
    	                $xml_content .= "<item><u_c_id>".$val['u_c_id']."</u_c_id><brand_id>".$val['brand_id']."</brand_id><series_id>".$val['series_id']."</series_id><model_id>".$val['model_id']."</model_id><car_name>".$val['car_name']."</car_name><status>".$val['status']."</status></item>";
    	            }
    	        }
	        }
	    }
	    $xml_content.="</XML>";
	    exit;
	}
	
	public function set_default_car(){
	    $uid = $this->GetUserId();
	    $u_c_id = isset($_POST['u_c_id'])?$_POST['u_c_id']:0;
	    if ($uid and $u_c_id){
	        $model_membercar = D('Membercar');
	        $map_car['uid'] = $uid;
	        $map_car['status'] = 1;
	        $data['is_default'] = 0;
	        $model_membercar->where($map_car)->save($data);
	        $map_d['u_c_id'] = $u_c_id;
	        $data_d['is_default'] = 1;
	        if($model_membercar->where($map_d)->save($data_d)){
	            echo 1;
	        }else {
	            echo -1;
	        }
	        
	    }else{
	        echo -1;
	    }
	    exit;
	}
	
	public function delete(){
	    $u_c_id = isset($_REQUEST['u_c_id'])?$_REQUEST['u_c_id']:0;
	    if ($u_c_id){
	        $model_membercar = D('Membercar');
	        $map['u_c_id'] = $u_c_id;
	        $membercar = $model_membercar->where($map)->find();
	        if ($membercar['is_default']==1){
	            $uid = $this->GetUserId();
	            $map_other['u_c_id'] = array('neq',$u_c_id);
	            $map_other['status'] = 1;
	            $map_other['uid'] = $uid;
	            $membercarother = $model_membercar->where($map_other)->order("u_c_id DESC")->find();
	            if ($membercarother['u_c_id']){
	                $map_d['u_c_id'] = $membercarother['u_c_id'];
	                $data_d['is_default'] = 1;
	                $model_membercar->where($map_d)->save($data_d);
	            }
	        }
	        $data['status'] = -1;
	        if($model_membercar->where($map)->save($data)){
	            $this->assign('jumpUrl', Cookie::get('_currentUrl_'));
                $this->success('删除成功！');
	        }else{
	            $this->error('删除失败！');
	        }
	    }else {
	        $this->error('删除失败！');
	    }
	}
	
}