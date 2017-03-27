<?php
/*
 */
class SalemanageAction extends CommonAction {
    
    public function index(){
        Cookie::set('_currentUrl_', __SELF__);
        if (isset($_REQUEST['shop_name']) and !empty($_REQUEST['shop_name'])){
            $condition['shop_name'] = array('like',"%".$_REQUEST['shop_name']."%");
            $this->assign('shop_name',$_REQUEST['shop_name']);
        }
        if (isset($_REQUEST['shop_type']) and !empty($_REQUEST['shop_type'])){
            $condition['shop_type'] = $_REQUEST['shop_type'];
            $this->assign('shop_type',$_REQUEST['shop_type']);
        }
        if (isset($_REQUEST['shop_boss']) and !empty($_REQUEST['shop_boss'])){
            $condition['shop_boss'] = array('like',"%".$_REQUEST['shop_boss']."%");
            $this->assign('shop_boss',$_REQUEST['shop_boss']);
        }
        $condition['status'] = 0;
        //echo '<pre>';print_r($condition);
        $model_salemanage = D(GROUP_NAME.'/Salemanage');
        // 计算总数
        $count = $model_salemanage->where($condition)->count();
        // 导入分页类
        import("ORG.Util.Page");
        // 实例化分页类
        $p = new Page($count, 10);
        $map['shop_name'] = $_REQUEST['shop_name'];
        $map['shop_type'] = $_REQUEST['shop_type'];
        $map['shop_boss'] = $_REQUEST['shop_boss'];
        foreach($_REQUEST as $key=>$val) {  
			if (!is_array($val) && $val != "" ) {
				$p->parameter .= "$key/".urlencode($val)."/";   
			}
        }
        // 分页显示输出
        $page = $p->show_admin();
        // 当前页数据查询
        $list = $model_salemanage->where($condition)->order('update_time DESC')->limit($p->firstRow.','.$p->listRows)->select();
        // 赋值赋值
        $this->assign('page', $page);
        $this->assign('list', $list);
        //echo '<pre>';print_r($carbrandinfo);
        $this->display();
    }
    public function _before_edit(){
        $model_salerecord = D(GROUP_NAME."/Salerecord");
        $id = $_GET['id'];
        $map['salemanage_id'] = $id;
        $map['status'] = 0;
        $salerecord = $model_salerecord->where($map)->select();
        $this->assign('salerecord',$salerecord);
    }
    
    public function _before_insert(){
        $_POST['create_time'] = time();
        $_POST['update_time'] = time();
        $_POST['operator_id'] = $_SESSION['authId'];
        $_POST['update_operator_id'] = $_SESSION['authId'];
    }
    public function _before_update(){
        $_POST['update_time'] = time();
        $_POST['update_operator_id'] = $_SESSION['authId'];
    }
    public function delete_sale(){
        $id = isset($_POST['id'])?$_POST['id']:0;
        $model_salemanage = D(GROUP_NAME."/Salemanage");
        $data['status'] = 1;
        $map['id'] = $id;
        $model_salemanage->where($map)->save($data);
        echo 1;exit;
    }
}