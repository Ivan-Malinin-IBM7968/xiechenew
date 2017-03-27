<?php
/**
 * TOP API: taobao.life.schedule.query request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeScheduleQueryRequest
{
	/** 
	 * 服务范围ID
	 **/
	private $serveRangeId;
	
	private $apiParas = array();
	
	public function setServeRangeId($serveRangeId)
	{
		$this->serveRangeId = $serveRangeId;
		$this->apiParas["serve_range_id"] = $serveRangeId;
	}

	public function getServeRangeId()
	{
		return $this->serveRangeId;
	}

	public function getApiMethodName()
	{
		return "taobao.life.schedule.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->serveRangeId,"serveRangeId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
