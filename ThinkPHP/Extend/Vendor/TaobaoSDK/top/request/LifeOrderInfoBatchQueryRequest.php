<?php
/**
 * TOP API: taobao.life.order.info.batch.query request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeOrderInfoBatchQueryRequest
{
	/** 
	 * 预约的开始时间，毫秒
	 **/
	private $beginTime;
	
	/** 
	 * 预约的结束时间，毫秒
	 **/
	private $endTime;
	
	/** 
	 * 订单的查询状态，0已预约未接单，1已预约已接单。0默认兼容原有的订单查询。
	 **/
	private $orderStatus;
	
	/** 
	 * 页码，第一页是1，每页20条。
	 **/
	private $pageNo;
	
	/** 
	 * 服务类型KEY，由生活服务平台分发给商家。
	 **/
	private $serviceKey;
	
	private $apiParas = array();
	
	public function setBeginTime($beginTime)
	{
		$this->beginTime = $beginTime;
		$this->apiParas["begin_time"] = $beginTime;
	}

	public function getBeginTime()
	{
		return $this->beginTime;
	}

	public function setEndTime($endTime)
	{
		$this->endTime = $endTime;
		$this->apiParas["end_time"] = $endTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}

	public function setOrderStatus($orderStatus)
	{
		$this->orderStatus = $orderStatus;
		$this->apiParas["order_status"] = $orderStatus;
	}

	public function getOrderStatus()
	{
		return $this->orderStatus;
	}

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
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
		return "taobao.life.order.info.batch.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->beginTime,"beginTime");
		RequestCheckUtil::checkNotNull($this->endTime,"endTime");
		RequestCheckUtil::checkNotNull($this->pageNo,"pageNo");
		RequestCheckUtil::checkNotNull($this->serviceKey,"serviceKey");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
