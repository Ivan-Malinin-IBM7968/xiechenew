<?php
/*
 */
class SalerecordAction extends CommonAction {
    
    public function index(){
        Cookie::set('_currentUrl_', __SELF__);
        if (isset($_REQUEST['operator_id']) and !empty($_REQUEST['operator_id'])){
            $condition['operator_id'] = $_REQUEST['operator_id'];
        }
        if (isset($_REQUEST['id']) and !empty($_REQUEST['id'])){
            $condition['salemanage_id'] = $_REQUEST['id'];
            $this->assign("salemanage_id",$_REQUEST['id']);
        }else {
            $this->error("数据错误！");
        }
        $condition['status'] = 0;
        //echo '<pre>';print_r($map);
        $model_salerecord = D(GROUP_NAME.'/Salerecord');
        // 计算总数
        $count = $model_salerecord->where($condition)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        $map['brand_name'] = $_REQUEST['brand_name'];
        foreach($_REQUEST as $key=>$val) {  
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/".urlencode($val)."/";   
			}
        }
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_salerecord->where($condition)->order('update_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
        // 赋值赋值
        //echo '<pre>';print_r($list);exit;
        $this->assign('page', $page);
        $this->assign('list', $list);
        //echo '<pre>';print_r($carbrandinfo);
        $this->display();
    }
    public function _before_insert(){
        $_POST['create_time'] = time();
        $_POST['update_time'] = time();
        $_POST['operator_id'] = $_SESSION['authId'];
        $_POST['operator_name'] = $_SESSION['loginAdminUserName'];
    }
    public function _before_update(){
        $_POST['update_time'] = time();
        
    }
    public function delete_record(){
        $id = isset($_POST['id'])?$_POST['id']:0;
        $model_salerecord = D(GROUP_NAME."/Salerecord");
        $data['status'] = 1;
        $map['id'] = $id;
        $model_salerecord->where($map)->save($data);
        echo 1;exit;
    }
}