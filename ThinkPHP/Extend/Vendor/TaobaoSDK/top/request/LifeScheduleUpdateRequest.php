<?php
/**
 * TOP API: taobao.life.schedule.update request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeScheduleUpdateRequest
{
	/** 
	 * 服务日程
	 **/
	private $serveSchedule;
	
	private $apiParas = array();
	
	public function setServeSchedule($serveSchedule)
	{
		$this->serveSchedule = $serveSchedule;
		$this->apiParas["serve_schedule"] = $serveSchedule;
	}

	public function getServeSchedule()
	{
		return $this->serveSchedule;
	}

	public function getApiMethodName()
	{
		return "taobao.life.schedule.update";
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
