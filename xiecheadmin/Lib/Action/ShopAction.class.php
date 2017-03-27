<?php
class ShopAction extends CommonAction {
    // 框架首页
    public function index() {
        
    	$model = D('Shopmain');
    	$this->_list($model, '');
    	$this->display();
    }
    
    public function insert(){ 
		
    	$upload_info = $this->uploadimg();
    	
    	$model_shopmian = D('Shopmain');
    	$data = $model_shopmian->data_format_insert();
    	$data['shop_avatar'] = $upload_info[0]['savename'];
    	$model_shopmian->relation('Shopdetail')->add($data);
    }
    public function edit(){
		
        $model = D('Shopmain');
        $id = $_REQUEST ['id'];	
        $list = $model->relation('Shopdetail')->where("shop_id=$id")->find();
		$this->assign('list',$list);
		$this->display();
    }
    
    public function save() {
    	$model = D('Shopmain');
    	$data = $model->data_format_insert();
        	if($_FILES['avatar']['name']){
    	$upload_info = $this->uploadimg();
    	$data['shop_avatar'] = $upload_info[0]['savename']; 
    	}
    	dump($data); 	
    	$result =  $model->relation('Shopdetail')->save($data);
    	dump($result);
    }
}
?>