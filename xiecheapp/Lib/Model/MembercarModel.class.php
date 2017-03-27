<?php
// 用户自定义车模型
class MembercarModel extends CommonModel {
	/*
	 * 自动验证
	 * 
	 */
	protected $_validate = array(
		//array('brand_id','check_zero','车辆信息必须！',1,'function'),
		//array('series_id','check_zero','车辆信息必须！',1,'function'),
		//array('model_id','check_zero','车辆信息必须！',1,'function'),
		//array('car_name','require','车昵称必须！'),

		);
	//添加车
	public function add_car() {
		$data = $this->create();
		if(false === $data){
			js_back($this->getError());
		}
		$data['uid'] = $this->GetUserId();
		return $this->add($data);
	}
	/*
	 * 当获取到brand_id series_id model_id的数组形式
	 * 格式化数据
	 * 形式：二位数组
	 * 
	 * 
	 */
	public function Membercar_format_by_arr($list_membercar,$arr_type='2'){
		$model_brand = D('carbrand');
		$model_series = D('carseries');
		$model_model = D('carmodel');
		if(is_array($list_membercar) AND $arr_type == 2){
			foreach($list_membercar AS $k=>$v){
				$list_default_brand = $model_brand->getByBrand_id($list_membercar[$k]['brand_id']);
				$list_default_series = $model_series->getBySeries_id($list_membercar[$k]['series_id']);
				$list_default_model = $model_model->getByModel_id($list_membercar[$k]['model_id']);
				//echo $model_model->getLastSql();
				
				//dump($list_default_model);
				$list_membercar[$k]['brand_name'] = $list_default_brand['brand_name'];
				$list_membercar[$k]['series_name'] = $list_default_series['series_name'];
				$list_membercar[$k]['model_name'] = $list_default_model['model_name'];
				//dump($list_membercar);
			}
			return $list_membercar;
		}elseif(is_array($list_membercar) AND $arr_type == 1){
		    $list_default_brand = $model_brand->getByBrand_id($list_membercar['brand_id']);
			$list_default_series = $model_series->getBySeries_id($list_membercar['series_id']);
			$list_default_model = $model_model->getByModel_id($list_membercar['model_id']);
			
			$list_membercar['brand_name'] = $list_default_brand['brand_name'];
			$list_membercar['series_name'] = $list_default_series['series_name'];
			$list_membercar['model_name'] = $list_default_model['model_name'];
		    return $list_membercar;
		}else{
			return false;
		}
	
		
	}
	
	/*
	 * 通过model_id来获取车型对应数据
	 * 初始化数据
	 * 
	 */
	public function Membercar_format_by_model_id($model_id) {
		$model_brand = D('carbrand');
		$model_series = D('carseries');
		$model_model = D('carmodel');
		if(!empty($model_id)){
			$list_default_model = $model_model->getByModel_id($model_id);
			//echo $model_model->getLastSql();
			$list_default_series = $model_series->getBySeries_id($list_default_model['series_id']);
		//	dump($list_default_series);
			$list_default_brand = $model_brand->getByBrand_id($list_default_series['brand_id']);
			
		}
		if($list_default_brand && $list_default_model && $list_default_series){
			$list['brand_name'] = $list_default_brand['brand_name'];
			$list['series_name'] = $list_default_series['series_name'];
			$list['model_name'] = $list_default_model['model_name'];
			//dump($list);
			return $list;
		}else{
				
			return false;
		}
	}
	/*
	 * 通过u_c_id来获取车型对应数据
	 * 初始化数据
	 * 
	 */
	public function Membercar_format_by_u_c_id($u_c_id){
		$u_c_id = isset($u_c_id)?$u_c_id:null;
			$car_arr = $this->getByu_c_id($u_c_id);
				if(is_array($car_arr)){
				$tmp[] = $car_arr;
				return $this->Membercar_format_by_arr($tmp);
				}else{					
					return false;
				}	
	}
	
    protected function check_zero(){
		if($_POST['series_id'] && $_POST['brand_id'] && $_POST['model_id']){
			return true;
		}else{
			return false;
		}
	}
	
}
?>