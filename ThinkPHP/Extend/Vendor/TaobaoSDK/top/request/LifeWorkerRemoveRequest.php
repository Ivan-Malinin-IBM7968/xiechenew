<?php
/**
 * TOP API: taobao.life.worker.remove request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeWorkerRemoveRequest
{
	/** 
	 * 服务人员ID
	 **/
	private $workerId;
	
	private $apiParas = array();
	
	public function setWorkerId($workerId)
	{
		$this->workerId = $workerId;
		$this->apiParas["worker_id"] = $workerId;
	}

	public function getWorkerId()
	{
		return $this->workerId;
	}

	public function getApiMethodName()
	{
		return "taobao.life.worker.remove";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->workerId,"workerId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
