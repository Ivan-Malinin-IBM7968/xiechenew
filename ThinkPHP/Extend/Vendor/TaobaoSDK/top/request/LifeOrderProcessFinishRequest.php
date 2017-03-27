<?php
/**
 * TOP API: taobao.life.order.process.finish request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeOrderProcessFinishRequest
{
	/** 
	 * 淘宝订单号
	 **/
	private $orderId;
	
	/** 
	 * 服务过程号
	 **/
	private $processId;
	
	private $apiParas = array();
	
	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
		$this->apiParas["order_id"] = $orderId;
	}

	public function getOrderId()
	{
		return $this->orderId;
	}

	public function setProcessId($processId)
	{
		$this->processId = $processId;
		$this->apiParas["process_id"] = $processId;
	}

	public function getProcessId()
	{
		return $this->processId;
	}

	public function getApiMethodName()
	{
		return "taobao.life.order.process.finish";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->orderId,"orderId");
		RequestCheckUtil::checkNotNull($this->processId,"processId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
