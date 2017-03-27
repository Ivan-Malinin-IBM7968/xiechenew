<?php
// 商家模型
class ShopmainModel extends RelationModel {
	
		//自动验证
		protected $_validate = array(
		/*
		array('u_c_id','require','名称必须'),
		//array('odometer','require','公里数必须'),
		array('total_cost','check_total_cost','总费用必须',1,'function'),
		array('odometer','check_odometer','公里数必须或填写的数值错误',0,'callback'),
		*/
		);
		//自动完成
		protected $_auto = array ( 
			array('open_time','strtotime',3,'function'),
			array('submit_time','time',3,'function'),
			array('close_time','strtotime',3,'function'),
		);
		
		protected $_link = array(
        	'Shopdetail'=>array(
				'mapping_type'=>HAS_ONE,
				'mapping_name'=>'Shopdetail',
                'class_name'  =>'Shopdetail',
				'foreign_key'=>'shop_id',
				),
		);
		
		public function data_format_insert(){
			$data = $this->create();
			if($data === false){
				js_back($this->getError());
			}
			$model_shopdetail = D('Shopdetail');
			$data['Shopdetail'] = $model_shopdetail->create();
			if($data['Shopdetail'] === false){
				js_back($this->getError());
			}
			return $data;
		}
}