<?php
/**
 * TOP API: taobao.life.range.remove request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeRangeRemoveRequest
{
	/** 
	 * 服务范围ID
	 **/
	private $rangeId;
	
	private $apiParas = array();
	
	public function setRangeId($rangeId)
	{
		$this->rangeId = $rangeId;
		$this->apiParas["range_id"] = $rangeId;
	}

	public function getRangeId()
	{
		return $this->rangeId;
	}

	public function getApiMethodName()
	{
		return "taobao.life.range.remove";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->rangeId,"rangeId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
