<?php
/**
 * TOP API: taobao.life.range.query request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeRangeQueryRequest
{
	/** 
	 * 父级区域
	 **/
	private $parentAreaId;
	
	/** 
	 * 服务类型KEY
	 **/
	private $serviceKey;
	
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

	public function setServiceKey($serviceKey)
	{
		$this->serviceKey = $serviceKey;
		$this->apiParas["service_key"] = $serviceKey;
	}

	public function getServiceKey()
	{
		return $this->serviceKey;
	}

	public function getApiMethodName()
	{
		return "taobao.life.range.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->serviceKey,"serviceKey");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
