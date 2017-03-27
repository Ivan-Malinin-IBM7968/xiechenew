<?php

/**
 * 配送要求信息
 * @author auto create
 */
class CnTmsLogisticsOrderDeliverRequirements
{
	
	/** 
	 * 配送类型： PTPS-普通配送 LLPS-冷链配送
	 **/
	public $deliveryType;
	
	/** 
	 * 送达日期（格式为 yyyy-MM-dd)
	 **/
	public $scheduleDay;
	
	/** 
	 * 送达结束时间（格式为 hh:mm）
	 **/
	public $scheduleEnd;
	
	/** 
	 * 送达开始时间（格式为 hh:mm）
	 **/
	public $scheduleStart;
	
	/** 
	 * 投递时延要求(1 工作日 2 节假日 104 预约达 )
	 **/
	public $scheduleType;	
}
?>