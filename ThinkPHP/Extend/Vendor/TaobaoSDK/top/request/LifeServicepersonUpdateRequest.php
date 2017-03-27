<?php
/**
 * TOP API: taobao.life.serviceperson.update request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeServicepersonUpdateRequest
{
	/** 
	 * 要操作的订单ID
	 **/
	private $orderId;
	
	/** 
	 * 服务人员基本信息
	 **/
	private $servicePerson;
	
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

	public function setServicePerson($servicePerson)
	{
		$this->servicePerson = $servicePerson;
		$this->apiParas["service_person"] = $servicePerson;
	}

	public function getServicePerson()
	{
		return $this->servicePerson;
	}

	public function getApiMethodName()
	{
		return "taobao.life.serviceperson.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->orderId,"orderId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
