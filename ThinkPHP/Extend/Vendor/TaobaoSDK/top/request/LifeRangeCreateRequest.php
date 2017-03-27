<?php
/**
 * TOP API: taobao.life.range.create request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeRangeCreateRequest
{
	/** 
	 * 这个服务范围下属的日程是否可循环使用
	 **/
	private $resourceRecyclable;
	
	/** 
	 * 服务范围
	 **/
	private $serveRange;
	
	/** 
	 * 服务类型KEY
	 **/
	private $serviceKey;
	
	private $apiParas = array();
	
	public function setResourceRecyclable($resourceRecyclable)
	{
		$this->resourceRecyclable = $resourceRecyclable;
		$this->apiParas["resource_recyclable"] = $resourceRecyclable;
	}

	public function getResourceRecyclable()
	{
		return $this->resourceRecyclable;
	}

	public function setServeRange($serveRange)
	{
		$this->serveRange = $serveRange;
		$this->apiParas["serve_range"] = $serveRange;
	}

	public function getServeRange()
	{
		return $this->serveRange;
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
		return "taobao.life.range.create";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->resourceRecyclable,"resourceRecyclable");
		RequestCheckUtil::checkNotNull($this->serviceKey,"serviceKey");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
