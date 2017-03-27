<?php

/*
 */

class ProductAction extends CommonAction {
	
	public function index(){
		$this->redirect('add');
	}

	public function _before_add(){
		$model = D(GROUP_NAME . "/" . 'Serviceitem');
		$service_list = $model->where("si_level=0")->select();
		//dump($service_list);
		$this->assign('service_list',$service_list);		
	}


	public function insert() {
		$model = D(GROUP_NAME . "/" . 'Product');
		$mainclass = include($this->getCacheFilename('maintainclass'));
		if(!$mainclass){
			$this->error('缓存出错');
		}
		$item_detail = $model->item_detail_distribute($_POST,$mainclass);
		$data = array(
			'flag'=>$_POST['flag'],
			'product_detail'=>$item_detail,
			'emission'=>$_POST['emission'],
			'shift'=>$_POST['shift'],
			'service_item_id'=>$_POST['service_item_id'],
			'service_id'=>$_POST['service_id'],
			'brand_id'=>$_POST['brand_id'],
		);
		if(false !== $model->add($data)){
			$_POST['product_id'] = $model->getLastInsID();
		}

		$car_model = $_POST['model_id'];
		foreach($car_model AS $k=>$v){
				$car_by_shop[]=array(
				'car_model_id'=>$v,
				'service_item_id'=>$_POST['service_item_id'],
				'car_service_id'=>$_POST['service_id'],
				'car_brand_id'=>$_POST['brand_id'],
				'car_series_id'=>$_POST['series_id'],
				'service_id'=>$_POST['service_id'],
				'product_id'=>$_POST['product_id'],
				);

		}
		//dump($car_by_shop);
		$model = D(GROUP_NAME . "/" . 'Productrelation');
		$list=$model->addAll($car_by_shop);
		if(false !== $list){
			$this->success('插入成功！');
		}

	}



	public function ajax_get_level(){
		$service_item_id = $_POST['service_item_id'];
		$model = D(GROUP_NAME . "/" . 'Serviceitem ');
		$list = $model->where("service_item_id=$service_item_id")->select();
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
			$Maintainclass = D(GROUP_NAME . "/" . 'Maintainclass');
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