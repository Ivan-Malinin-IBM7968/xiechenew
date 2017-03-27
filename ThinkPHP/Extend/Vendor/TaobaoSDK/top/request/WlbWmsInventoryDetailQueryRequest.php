<?php
/**
 * TOP API: taobao.wlb.wms.inventory.detail.query request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class WlbWmsInventoryDetailQueryRequest
{
	/** 
	 * 库存查询条件
	 **/
	private $criteriaList;
	
	private $apiParas = array();
	
	public function setCriteriaList($criteriaList)
	{
		$this->criteriaList = $criteriaList;
		$this->apiParas["criteria_list"] = $criteriaList;
	}

	public function getCriteriaList()
	{
		return $this->criteriaList;
	}

	public function getApiMethodName()
	{
		return "taobao.wlb.wms.inventory.detail.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
