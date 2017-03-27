<?php
/**
 * TOP API: taobao.life.fuwubaoorder.info.batch.get request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeFuwubaoorderInfoBatchGetRequest
{
	/** 
	 * 交易结束的开始时间
	 **/
	private $beginTime;
	
	/** 
	 * 交易结束的结束时间
	 **/
	private $endTime;
	
	/** 
	 * 商品ID
	 **/
	private $itemId;
	
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

	public function setItemId($itemId)
	{
		$this->itemId = $itemId;
		$this->apiParas["item_id"] = $itemId;
	}

	public function getItemId()
	{
		return $this->itemId;
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
		return "taobao.life.fuwubaoorder.info.batch.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->beginTime,"beginTime");
		RequestCheckUtil::checkNotNull($this->endTime,"endTime");
		RequestCheckUtil::checkNotNull($this->itemId,"itemId");
		RequestCheckUtil::checkNotNull($this->pageNo,"pageNo");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
