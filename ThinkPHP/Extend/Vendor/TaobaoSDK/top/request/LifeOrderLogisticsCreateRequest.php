<?php
/**
 * TOP API: taobao.life.order.logistics.create request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeOrderLogisticsCreateRequest
{
	/** 
	 * 详细地址
	 **/
	private $address;
	
	/** 
	 * 区名称
	 **/
	private $area;
	
	/** 
	 * 收件人
	 **/
	private $name;
	
	/** 
	 * 淘宝订单号
	 **/
	private $orderId;
	
	/** 
	 * 联系电话
	 **/
	private $phone;
	
	private $apiParas = array();
	
	public function setAddress($address)
	{
		$this->address = $address;
		$this->apiParas["address"] = $address;
	}

	public function getAddress()
	{
		return $this->address;
	}

	public function setArea($area)
	{
		$this->area = $area;
		$this->apiParas["area"] = $area;
	}

	public function getArea()
	{
		return $this->area;
	}

	public function setName($name)
	{
		$this->name = $name;
		$this->apiParas["name"] = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
		$this->apiParas["order_id"] = $orderId;
	}

	public function getOrderId()
	{
		return $this->orderId;
	}

	public function setPhone($phone)
	{
		$this->phone = $phone;
		$this->apiParas["phone"] = $phone;
	}

	public function getPhone()
	{
		return $this->phone;
	}

	public function getApiMethodName()
	{
		return "taobao.life.order.logistics.create";
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
