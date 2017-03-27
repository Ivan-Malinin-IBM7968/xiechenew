<?php
/**
 * TOP API: taobao.life.worker.update request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeWorkerUpdateRequest
{
	/** 
	 * 服务人员基本信息
	 **/
	private $serveWorker;
	
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

	public function getApiMethodName()
	{
		return "taobao.life.worker.update";
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
