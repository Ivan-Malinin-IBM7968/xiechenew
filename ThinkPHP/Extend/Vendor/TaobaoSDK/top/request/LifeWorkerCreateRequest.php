<?php
/**
 * TOP API: taobao.life.worker.create request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeWorkerCreateRequest
{
	/** 
	 * 服务人员基本信息
	 **/
	private $serveWorker;
	
	/** 
	 * 服务类型KEY，由淘宝生活服务平台分配。
	 **/
	private $serviceKey;
	
	private $apiParas = array();
	
	public function setServeWorker($serveWorker)
	{
		$this->serveWorker = $serveWorker;
		$this->apiParas["serve_worker"] = $serveWorker;
	}

	public function getServeWorker()
	{
		return $this->serveWorker;
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
		return "taobao.life.worker.create";
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
