<?php
/*
 * ProductAction
 * 
 */
class ProductAction extends CommonAction {
	
	public function index(){
		$this->redirect('addproduct');
	}
	/*
	 * 添加产品
	 * 
	 * 
	 */
	
	
	public function addproduct() {
		$model_shop = D('Shopmain');
		$list_shop = $model_shop->where("status=1")->select();
		echo $model_shop->getLastSql();
		$model = D('Serviceitem');
		$service_list = $model->where("si_level=0")->select();
		$this->assign('service_list',$service_list);
		$this->assign('list_shop',$list_shop);
		$this->add();
	}
	/*
	 * 哎，这段代码真心妖怪，要怎么写呢，， modify by yyc
	 * 
	 * 
	 */
	public function listservice(){
		$shop_id = $_REQUEST['shop_id'];
		$model = D('Serviceitem');
		$service_list = $model->where("si_level=0")->select();
		$this->assign('shop_id',$shop_id);
		$this->assign('service_list',$service_list);
		$this->display();
	}

	
	public function listproduct() {
		//dump($_POST);
		if($_POST){
				$model_serviceitem = D('Serviceitem');
				$service_list = $model_serviceitem->where("si_level=0")->select();
				$str_model_id = implode(",",$_POST['model_id']);
				$map['car_model_id']  = array('in',$str_model_id);
				$map['shop_id'] = array('EQ',$_POST['shop_id']);
				$map['service_id'] = array('EQ',$_POST['service_id']);
				$model_product = D('Product');
				$list = $model_product->where($map)->select();
				//dump($list);
				//dump($list['0']['product_detail']);
				$detail_str = unserialize($list['0']['product_detail']);
				//dump($detail_str);
				foreach($detail_str AS $k=>$v){
						$model = D('Maintainclass');
						$tmp_middle = $detail_str[$k]['Midl'];
						$tmp_Midl = $model->where("ItemID=$tmp_middle")->find();
						$detail_str[$k]['Midl_name'] = $tmp_Midl['ItemName'];
				}
				$model_productsale = D('Productsale');
				$list_productsale = $model_productsale->where("shop_id=$_POST[shop_id]")->select();
				//dump($list_productsale);
				$this->assign('shop_id',$_POST['shop_id']);
				$this->assign('product_id_arr',$list);
				$this->assign('service_list',$service_list);
				$this->assign('list_productsale',$list_productsale);
				$this->assign('product_info',$_POST);
				$this->assign('product_detail',$detail_str);
				$this->assign('str_model_id',$str_model_id);
				$this->display();
		}
		
	}
		
	public function insert() {
		$model = D('Product');
		$item_detail = $model->item_detail_distribute($_POST);
		$car_model = $_POST['model_id'];
		$item_shop = $_POST['shop_id'];
		foreach($car_model AS $k=>$v){
			foreach($item_shop AS $key=>$val){
				$car_by_shop[]=array(
				'car_model_id'=>$v,
				'shop_id'=>$val,
				'product_detail'=>$item_detail,
				'service_item_id'=>$_POST['service_item_id'],
				'car_service_id'=>$_POST['service_id'],
				'car_brand_id'=>$_POST['brand_id'],
				'car_series_id'=>$_POST['series_id'],
				'service_id'=>$_POST['service_id'],
				);
			}
		}
		$list=$model->addAll($car_by_shop);
		dump($list);
	}
	
	public function editproduct(){
		$model = D('Product');
		$item_detail = $model->item_detail_distribute($_POST);
		$data['product_detail']=$item_detail;
		$product_id = $_POST['product_id'];
		
		foreach($product_id AS $k=>$v){
			$list=$model->where("product_id=$product_id[$k]")->data($data)->save();
			echo  $model->getLastSql();
			$list_arr[] = $list;
		}
		//dump($item_detail);
		dump($_list_arr);
		
	}
		

	public function ajax_get_level(){
		$service_item_id = $_POST['service_item_id'];
		//dump($service_item_id);
		$model = D('Serviceitem ');
		$list = $model->where("service_item_id=$service_item_id")->select();
		//$result = $this->product->get_level ($service_item_id);
		echo json_encode($list);
		return;
	}
		
	
	
//配件明细ajax输出
	public function maintaindetail(){
		//大分类生成中分类
		if ($_GET['select'] != null){
		  	$str=urldecode($_GET['select']); //解码 		  
			if ($_GET['select'] == 0){
			$MidClassSelect ='<select name="TXT_MidlClass[]" id="TXT_MidlClass" style="width:72px;">';
			$MidClassSelect.= "<option value='0'></option>";
			$MidClassSelect.= "</select>";
			echo $MidClassSelect;
			return;
			}
			$Maintainclass = M('Maintainclass');
			$rs = $Maintainclass ->where("PItemID ='$_GET[select]'")->select();	
			if ($rs){
				$MidClassSelect ="<select name=\"TXT_MidlClass[]\" id=\"TXT_MidlClass\" style=\"width:72px;\"><option value='0'></option>";
				foreach ($rs AS $key=>$val){
					$MidClassSelect.= '<option value="'.$val['ItemID'].'">'.$val['ItemName'].'</option>';
				}
				$MidClassSelect.='</select>';
			}
			echo $MidClassSelect;
			return;
		}
	}
    

	
}
