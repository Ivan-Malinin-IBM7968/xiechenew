<?php
/*
 */
class ShopAction extends CommonAction {
    
    public function index(){
        if (isset($_SESSION['shop_id']) and !empty($_SESSION['shop_id'])){
            $shop_id = $_SESSION['shop_id'];
        }else {
            $this->error('店铺信息不存在！');
        }
        
        $province = R('Store/Region/getRegion');
        $this->assign('province',$province);
        
        $model_fs = D(GROUP_NAME. "/" . 'fs');
		$fs_arr = $model_fs->select();
		$this->assign('fs_arr',$fs_arr);
		
        $model_shop_fs_relation = D(GROUP_NAME.'/Shop_fs_relation');
	    $fs_arr = $model_shop_fs_relation->where("shopid=$shop_id")->select();
	    if (!empty($fs_arr)){
	        $fsids_arr = array();
	        foreach ($fs_arr as $v){
	            $fsids_arr[] = $v['fsid'];
	        }
	    }
	    $fsids_str = implode(',',$fsids_arr);
	    $this->assign('fsids_str',$fsids_str);
	    
        $model_shop = D(GROUP_NAME.'/Shop');
        $shopinfo = $model_shop->find($shop_id);
        $model_shopdetail = D(GROUP_NAME.'/Shopdetail');
        $shopinfo['Shopdetail'] = $model_shopdetail->find($shop_id);
        $this->assign('list',$shopinfo);
        $this->display();
    }
    
    public function get_map(){
		$model = D(GROUP_NAME . "/" . $this->getActionName());
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->find($id);

		$data = array(
			'name'=>$vo['shop_name'],
			'address'=>$vo['shop_address'],
			'tel'=>$vo['shop_maps'],
			'citycode'=>'131',
		
		);
		$data = '{"name":"'.$vo['shop_name'].'","address":"'.$vo['shop_address'].'","tel":"'.$vo['shop_maps'].'","point":"'.$vo['shop_maps'].'","citycode":131}';
		//$data = json_encode($data);
		$this->assign('data',$data);
        $this->display();
	}
}