<?php

/**
 * 预约时段参数
 * @author auto create
 */
class BookingInventoryTopDto
{
	
	/** 
	 * 预约的服务开始时间，格式:yyyy-MM-dd HH:mm:ss
	 **/
	public $bookBeginTime;
	
	/** 
	 * 预约的服务技术时间，格式:yyyy-MM-dd HH:mm:ss
	 **/
	public $bookEndTime;
	
	/** 
	 * 扣减的数量，也就是服务时间的单元，需要安排的服务人员数量
	 **/
	public $bookQuantity;
	
	/** 
	 * 预约的服务地址。
	 **/
	public $serveLocation;
	
	/** 
	 * 服务日程ID
	 **/
	public $serveScheduleId;
	
	/** 
	 * 卖家定义的唯一订单号，等于32字符。
	 **/
	public $uniqueOrderId;	
}
?>