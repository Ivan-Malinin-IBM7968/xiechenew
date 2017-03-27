<?php
/**
 * TOP API: taobao.life.worker.dispatch request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeWorkerDispatchRequest
{
	/** 
	 * 淘宝订单ID
	 **/
	private $tbOrderId;
	
	/** 
	 * 服务人员ID列表
	 **/
	private $workerIds;
	
	private $apiParas = array();
	
	public function setTbOrderId($tbOrderId)
	{
		$this->tbOrderId = $tbOrderId;
		$this->apiParas["tb_order_id"] = $tbOrderId;
	}

	public function getTbOrderId()
	{
		return $this->tbOrderId;
	}

	public function setWorkerIds($workerIds)
	{
		$this->workerIds = $workerIds;
		$this->apiParas["worker_ids"] = $workerIds;
	}

	public function getWorkerIds()
	{
		return $this->workerIds;
	}

	public function getApiMethodName()
	{
		return "taobao.life.worker.dispatch";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->tbOrderId,"tbOrderId");
		RequestCheckUtil::checkNotNull($this->workerIds,"workerIds");
		RequestCheckUtil::checkMaxListSize($this->workerIds,2000,"workerIds");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
