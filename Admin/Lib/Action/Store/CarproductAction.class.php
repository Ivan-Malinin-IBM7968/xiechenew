<?php
class CarproductAction extends CommonAction {
    function __construct() {
		parent::__construct();
		
	}
	function test(){
		 $model_serviceitem = D('Serviceitem');
		//$map['brand_id'] = $_POST['brand_id'];
		$map['brand_id'] = 59;
		//$map['series_id'] = $_POST['series_id'];
		$map['series_id'] = 418;
		//$map['model_id'] = $_POST['model_id'];
		$map['model_id'] = 1135;
		$this->assign('brand_id',$map['brand_id']);
		$this->assign('series_id',$map['series_id']);
		$this->assign('model_id',$map['model_id']);
		$condition['xc_product.service_id'] = array('in','10,9,11,27,12,26,24,23,22,25,20,19,16,15,14,28,17,18');
		$condition['xc_productrelation.car_brand_id'] = array('eq',$map['brand_id']);
		$condition['xc_productrelation.car_series_id'] = array('eq',$map['series_id']);
		$condition['xc_productrelation.car_model_id'] = array('eq',$map['model_id']);
		$model_product = D('Product');
		$list = $model_product->where($condition)->join('xc_productrelation ON xc_productrelation.product_id = xc_product.id')->select();
	
		if (!empty($list)){
			$model_brand = D('Carbrand');
			$model_series = D('Carseries');
			$model_model = D('Carmodel');
			foreach ($list as $k=>$val){
				$brandinfo = $model_brand->find($val['car_brand_id']);
				$list[$k]['brand_name'] = $brandinfo['brand_name'];
				
				$seriesinfo = $model_series->find($val['car_series_id']);
				$list[$k]['series_name'] = $seriesinfo['series_name'];
				
				$modelinfo = $model_model->find($val['car_model_id']);
				$list[$k]['model_name'] = $modelinfo['model_name'];
				
				$serviceiteminfo = $model_serviceitem->find($val['service_id']);
				$list[$k]['service_name'] = $serviceiteminfo['name'];
				$list[$k]['detail_list'] = unserialize($val['product_detail']);
			}
		}
		$this->assign('productlist',$list);
	    $this->assign('data',$data);
		$this->display();
	
	
	
	
	
	
	}
	public function index(){
	    $model_serviceitem = D(GROUP_NAME.'/Serviceitem');
	    $map['si_level'] = 0;
	    $level_0 = $model_serviceitem->where($map)->select();
	    $data = array();
	    if (!empty($level_0)){
	        foreach ($level_0 as $key=>$value) {
	            $data[$key] = $value;
	            $map['service_item_id'] = $value['id'];
	            $map['si_level'] = 1;
	            $data[$key]['items'] = $model_serviceitem->order("itemorder DESC")->where($map)->select();
	        }
	    }
	    
	    unset($map);
	    if (isset($_POST['serviceitem']) and !empty($_POST['serviceitem'])){
	        $serviceitem_str = implode(',',$_POST['serviceitem']);
	        $this->assign('serviceitem_str',$serviceitem_str);
	    }
	    $map = array('brand_id'=>0,'series_id'=>0,'model_id'=>0);
	    if (isset($_POST['brand_id']) and !empty($_POST['brand_id'])){
	        $map['brand_id'] = $_POST['brand_id'];
	    }
	    if (isset($_POST['series_id']) and !empty($_POST['series_id'])){
	        $map['series_id'] = $_POST['series_id'];
	    }
	    if (isset($_POST['model_id']) and !empty($_POST['model_id'])){
	        $map['model_id'] = $_POST['model_id'];
	    }
	    $this->assign('brand_id',$map['brand_id']);
	    $this->assign('series_id',$map['series_id']);
	    $this->assign('model_id',$map['model_id']);
	    if (!empty($serviceitem_str) and ($map['brand_id'] || $map['series_id'] || $map['model_id'])){
	        //$condition['xc_product.service_id'] = array('in',$serviceitem_str);
			$condition['service_id'] = array('in',$serviceitem_str);
	        if (isset($map['brand_id']) and $map['brand_id']){
	            //$condition['xc_productrelation.car_brand_id'] = array('eq',$map['brand_id']);
				$condition['brand_id'] = array('eq',$map['brand_id']);
	        }
	        if (isset($map['series_id']) and $map['series_id']){
	            //$condition['xc_productrelation.car_series_id'] = array('eq',$map['series_id']);
				$condition['series_id'] = array('eq',$map['series_id']);
	        }
	        if (isset($map['model_id']) and $map['model_id']){
	            //$condition['xc_productrelation.car_model_id'] = array('eq',$map['model_id']);
				$condition['model_id'] = array('eq',$map['model_id']);
	        }
	        $model_product = D(GROUP_NAME.'/Product');
			$model_productversion = D(GROUP_NAME.'/Productversion');
	        //$list = $model_product->where($condition)->join('xc_productrelation ON xc_productrelation.product_id = xc_product.id')->select();
			$list = $model_product->where($condition)->select();

			if (!empty($list)){
	            $model_brand = D(GROUP_NAME.'/Carbrand');
	            $model_series = D(GROUP_NAME.'/Carseries');
	            $model_model = D(GROUP_NAME.'/Carmodel');
	            foreach ($list as $k=>$val){
	                //$brandinfo = $model_brand->find($val['car_brand_id']);
					$brandinfo = $model_brand->find($val['brand_id']);
	                $list[$k]['brand_name'] = $brandinfo['brand_name'];
	                
	                //$seriesinfo = $model_series->find($val['car_series_id']);
					$seriesinfo = $model_series->find($val['series_id']);
	                $list[$k]['series_name'] = $seriesinfo['series_name'];
	                
	                //$modelinfo = $model_model->find($val['car_model_id']);
					$modelinfo = $model_model->find($val['model_id']);
	                $list[$k]['model_name'] = $modelinfo['model_name'];
	                
	                $serviceiteminfo = $model_serviceitem->find($val['service_id']);
	                $list[$k]['service_name'] = $serviceiteminfo['name'];

					//$productversion = $model_productversion->where(array('product_id'=>$val['id'],'status'=>0))->find();
	                $list[$k]['detail_list'] = unserialize($val['product_detail']);
	            }
	        }
	        //echo '<pre>';print_r($list);
	        $this->assign('productlist',$list);
	    }
	    
	    //echo '<pre>';print_r($data);
	    $this->assign('data',$data);
	    $this->display();
	}
	
	public function detail(){
	    $product_id = isset($_GET['product_id'])?$_GET['product_id']:0;
	    if (empty($product_id)){
	        $this->error("产品ID错误");
	    }
	    $model_product = D(GROUP_NAME.'/Product');
	    //$map['xc_product.id'] = $product_id;
	    //$product_info = $model_product->where($map)->join('xc_productrelation ON xc_productrelation.product_id = xc_product.id')->find();
		$product_info = $model_product->find($product_id);
	    if ($product_info['product_detail']){
	        $product_info['product_detail_arr'] = unserialize($product_info['product_detail']);
	    }
	    $model_brand = D(GROUP_NAME.'/Carbrand');
        $model_series = D(GROUP_NAME.'/Carseries');
        $model_model = D(GROUP_NAME.'/Carmodel');
        $model_serviceitem = D(GROUP_NAME.'/Serviceitem');
        //$brandinfo = $model_brand->find($product_info['car_brand_id']);
		$brandinfo = $model_brand->find($product_info['brand_id']);
        $product_info['brand_name'] = $brandinfo['brand_name'];
        
        //$seriesinfo = $model_series->find($product_info['car_series_id']);
		$seriesinfo = $model_series->find($product_info['series_id']);
        $product_info['series_name'] = $seriesinfo['series_name'];
        
        //$modelinfo = $model_model->find($product_info['car_model_id']);
		$modelinfo = $model_model->find($product_info['model_id']);
        $product_info['model_name'] = $modelinfo['model_name'];
        
        $serviceiteminfo = $model_serviceitem->find($product_info['service_id']);
        $product_info['service_name'] = $serviceiteminfo['name'];
        //echo '<pre>';print_r($product_info['product_detail_arr']);
	    $this->assign('product_info',$product_info);
	    $this->display();
	}
    public function save_product(){
        $model_product = D(GROUP_NAME.'/Product');
        $model_productversion = D(GROUP_NAME.'/Productversion');
        $data = array();
        if (isset($_POST['product_id']) and $_POST['product_id']){
            if (isset($_POST['Midl_name']) and $_POST['Midl_name']){
                foreach ($_POST['Midl_name'] as $key=>$val){
                    if (!empty($val)){
                        $data[$key]['Midl_name'] = $val;
                        $data[$key]['price'] = $_POST['price'][$key];
                        $data[$key]['quantity'] = $_POST['quantity'][$key];
                        $data[$key]['unit'] = $_POST['unit'][$key];
                    }
                }
            }
            $add_data['product_detail'] = serialize($data);
            $map['status'] = 0;
            $map['product_id'] = $_POST['product_id'];
            $productversion = $model_productversion->where($map)->find();
            if ($productversion['product_detail'] != $add_data['product_detail']){
                $save_data['status'] = 1;
                $save_con['id'] = $productversion['id'];
                $model_productversion->where($save_con)->save($save_data);
                $add_data['product_id'] = $_POST['product_id'];
                $versionid = $model_productversion->add($add_data);
                $prosave_data['product_detail'] = serialize($data);
                $prosave_data['versionid'] = $versionid;
                $condition['id'] = $_POST['product_id'];
                if ($model_product->where($condition)->save($prosave_data)){
                    $this->success('修改成功');
                }else {
                    $this->error('修改失败');
                }
            }else {
                $this->error('修改失败');
            }
            /*$condition['id'] = $_POST['product_id'];
            if($model_product->where($condition)->save($save_data)){
                $model_product_img = D('Product_img');
                $del_condition['product_ids'] = array('like','%,'.$_POST['product_id'].',%');
                $del_condition['status'] = 0;
                $del_data['status'] = 1;
                $del_data['update_time'] = time();
                $model_product_img->where($del_condition)->save($del_data);
                $this->success('修改成功');
            }else {
                $this->error('修改失败');
            }*/
        }
    }
}
?>