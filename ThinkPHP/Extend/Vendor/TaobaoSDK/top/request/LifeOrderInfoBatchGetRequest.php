<?php
/**
 * TOP API: taobao.life.order.info.batch.get request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeOrderInfoBatchGetRequest
{
	/** 
	 * 申请预约服务的开始时间
	 **/
	private $beginTime;
	
	/** 
	 * 申请预约服务的结束时间
	 **/
	private $endTime;
	
	/** 
	 * 页号，从1开始，默认页大小为20条。
	 **/
	private $pageNo;
	
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

	public function setPageNo($pageNo)
	{
		$this->pageNo = $pageNo;
		$this->apiParas["page_no"] = $pageNo;
	}

	public function getPageNo()
	{
		return $this->pageNo;
	}

	public function getApiMethodName()
	{
		return "taobao.life.order.info.batch.get";
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
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
