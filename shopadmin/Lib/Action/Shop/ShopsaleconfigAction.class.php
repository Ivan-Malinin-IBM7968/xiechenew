<?php
/*
 */
class ShopsaleconfigAction extends CommonAction {
    
    public function index(){
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺信息不存在！');
        }
                
        $model_shopsaleconfig = D(GROUP_NAME. "/Shopsaleconfig");
		$list = $model_shopsaleconfig->where(array('shop_id'=>$shop_id))->find();

		$this->assign('shop_id',$shop_id);
		$this->assign('list',$list);
        $this->display();
    }

}