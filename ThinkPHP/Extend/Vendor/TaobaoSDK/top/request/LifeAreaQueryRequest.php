<?php
/**
 * TOP API: taobao.life.area.query request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeAreaQueryRequest
{
	/** 
	 * 需要指定区域ID下的子级区域
	 **/
	private $parentAreaId;
	
	private $apiParas = array();
	
	public function setParentAreaId($parentAreaId)
	{
		$this->parentAreaId = $parentAreaId;
		$this->apiParas["parent_area_id"] = $parentAreaId;
	}

	public function getParentAreaId()
	{
		return $this->parentAreaId;
	}

	public function getApiMethodName()
	{
		return "taobao.life.area.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->parentAreaId,"parentAreaId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
