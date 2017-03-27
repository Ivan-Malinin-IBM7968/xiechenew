<?php
/**
 * TOP API: taobao.life.inventory.rollback request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeInventoryRollbackRequest
{
	/** 
	 * 预约时段参数
	 **/
	private $bookingInventory;
	
	/** 
	 * 服务KEY
	 **/
	private $serviceKey;
	
	private $apiParas = array();
	
	public function setBookingInventory($bookingInventory)
	{
		$this->bookingInventory = $bookingInventory;
		$this->apiParas["booking_inventory"] = $bookingInventory;
	}

	public function getBookingInventory()
	{
		return $this->bookingInventory;
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
		return "taobao.life.inventory.rollback";
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
