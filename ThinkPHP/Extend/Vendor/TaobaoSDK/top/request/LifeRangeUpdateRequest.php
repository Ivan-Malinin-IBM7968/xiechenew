<?php
/**
 * TOP API: taobao.life.range.update request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeRangeUpdateRequest
{
	/** 
	 * 服务范围
	 **/
	private $serveRange;
	
	/** 
	 * 服务类型KEY，由淘宝生活服务平台分发给商家
	 **/
	private $serviceKey;
	
	private $apiParas = array();
	
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
		return "taobao.life.range.update";
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
