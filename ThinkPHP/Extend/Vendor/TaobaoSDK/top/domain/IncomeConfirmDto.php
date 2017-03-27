<?php

/**
 * 确认单申请
 * @author auto create
 */
class IncomeConfirmDto
{
	
	/** 
	 * appkey
	 **/
	public $appkey;
	
	/** 
	 * 确认扩展信息
	 **/
	public $extend;
	
	/** 
	 * 确认金额
	 **/
	public $fee;
	
	/** 
	 * 卖家nick
	 **/
	public $nick;
	
	/** 
	 * 服务市场有效订单ID
	 **/
	public $orderId;
	
	/** 
	 * 外部确认账单ID
	 **/
	public $outConfirmId;
	
	/** 
	 * 外部订单ID
	 **/
	public $outOrderId;	
}
?>