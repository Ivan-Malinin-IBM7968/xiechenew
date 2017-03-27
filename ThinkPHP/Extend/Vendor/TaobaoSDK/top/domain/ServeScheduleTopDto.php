<?php

/**
 * 服务日程
 * @author auto create
 */
class ServeScheduleTopDto
{
	
	/** 
	 * 日历是否可循环使用，true是，false否。
	 **/
	public $recyclable;
	
	/** 
	 * 服务日程的开始时间，格式:yyyy-MM-dd HH:mm:ss
	 **/
	public $scheduleBeginTime;
	
	/** 
	 * 服务日程的结束时间，格式:yyyy-MM-dd HH:mm:ss
	 **/
	public $scheduleEndTime;
	
	/** 
	 * 当前状态，0可用，-1不可用。
	 **/
	public $scheduleStatus;
	
	/** 
	 * 所属的服务范围ID
	 **/
	public $serveRangeId;
	
	/** 
	 * 服务日程ID
	 **/
	public $serveScheduleId;	
}
?>