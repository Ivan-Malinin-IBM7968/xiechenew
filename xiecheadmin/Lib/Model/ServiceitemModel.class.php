<?php
// 配件模型
class ServiceitemModel extends Model {
	
	/*
	 * 
	 * 查询服务项目
	 */
	public function GetServiceName($service_id) {
		$list = $this->where("si_level=1 AND service_id=$service_id")->find();
		if(!empty($list)){
			return $list;
			
		}else{
			return false;	
		}
	}
	
/*
 * 查询服务分类
 */
	public function GetServiceItemName($service_item_id) {
		$list = $this->where("si_level=0 AND service_id = $service_item_id")->find();
		if(!empty($list)){
			return $list;
			
		}else{
			return false;	
		}
	}
	
}

?>