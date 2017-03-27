<?php
/**
 * TOP API: taobao.life.inventory.update request
 * 
 * @author auto create
 * @since 1.0, 2015.09.16
 */
class LifeInventoryUpdateRequest
{
	/** 
	 * 日内库存
	 **/
	private $dayInventorys;
	
	/** 
	 * 服务日程ID
	 **/
	private $serveScheduleId;
	
	private $apiParas = array();
	
	public function setDayInventorys($dayInventorys)
	{
		$this->dayInventorys = $dayInventorys;
		$this->apiParas["day_inventorys"] = $dayInventorys;
	}

	public function getDayInventorys()
	{
		return $this->dayInventorys;
	}

	public function setServeScheduleId($serveScheduleId)
	{
		$this->serveScheduleId = $serveScheduleId;
		$this->apiParas["serve_schedule_id"] = $serveScheduleId;
	}

	public function getServeScheduleId()
	{
		return $this->serveScheduleId;
	}

	public function getApiMethodName()
	{
		return "taobao.life.inventory.update";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->serveScheduleId,"serveScheduleId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
