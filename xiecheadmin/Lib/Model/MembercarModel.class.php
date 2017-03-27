<?php
// 用户自定义车模型
class MembercarModel extends Model {
			
	public function ListMembercar($list_membercar){
		$model_brand = D('carbrand');
		$model_series = D('carseries');
		$model_model = D('carmodel');
		//dump($list_membercar);
		if(is_array($list_membercar)){
			foreach($list_membercar AS $k=>$v){
				$list_default_brand = $model_brand->getByBrand_id($list_membercar[$k]['brand_id']);
				$list_default_series = $model_series->getBySeries_id($list_membercar[$k]['series_id']);
				$list_default_model = $model_model->getByModel_id($list_membercar[$k]['model_id']);
				$list_membercar[$k]['brand_name'] = $list_default_brand['brand_name'];
				$list_membercar[$k]['series_name'] = $list_default_series['series_name'];
				$list_membercar[$k]['model_name'] = $list_default_model['model_name'];
				//dump($list_membercar);
			}
			return $list_membercar;
		}else{
			return false;
		}
		
		
	}
	
	
	
	
}
?>