<?php
/**
 * TOP API: taobao.life.schedule.remove request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeScheduleRemoveRequest
{
	/** 
	 * 服务日程ID
	 **/
	private $scheduleId;
	
	private $apiParas = array();
	
	public function setScheduleId($scheduleId)
	{
		$this->scheduleId = $scheduleId;
		$this->apiParas["schedule_id"] = $scheduleId;
	}

	public function getScheduleId()
	{
		return $this->scheduleId;
	}

	public function getApiMethodName()
	{
		return "taobao.life.schedule.remove";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->scheduleId,"scheduleId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
